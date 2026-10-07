<?php
/**
 * Mapa de Gado (tela do pasto) — botão NUTRIÇÃO, chamado pelo aplicativo
 * (inclusive reenvio de ações feitas offline). Mesmas regras de
 * gravar_nutricao.php e ler_itens_nutricao.php do sistema web, numa
 * transação e com reenvio seguro.
 *
 * Usa NutricaoDao, PastoDao e AnimalPastoDao na MESMA conexão.
 */
class MapaGadoNutricaoService{

    /** Dias de nutrição que vão junto com o tabuleiro para o cache do
     *  aplicativo (datas mais antigas são buscadas na hora, com internet). */
    const DIAS_NO_CACHE = 30;

    // ---------------------------------------------------------------------
    // Listas para o aplicativo
    // ---------------------------------------------------------------------

    /**
     * Situações do cocho, produtos (com a unidade) e as nutrições recentes
     * das fazendas — vai junto com a resposta do tabuleiro.
     */
    public function dadosParaCache($bd, $fazendas, $con){
        $dao = new NutricaoDao($bd, $con);
        $desde = date('Y-m-d', strtotime('-' . self::DIAS_NO_CACHE . ' days'));
        return [
            "scores_cocho"      => $dao->listarScoresCocho(),
            "produtos_nutricao" => $dao->listarProdutos(),
            "nutricoes"         => $dao->listarItens($fazendas, $desde),
            "nutricoes_desde"   => $desde,
        ];
    }

    /** Itens de nutrição de um pasto numa data (tabela do modal). */
    public function itens($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        $local = (int) ($dados['fazenda'] ?? 0);
        $pasto = (int) ($dados['pasto'] ?? 0);
        $data = DateTime::createFromFormat('!Y-m-d', (string) ($dados['data'] ?? ''));
        if ($bd === '' || $local <= 0 || $pasto <= 0 || !$data) {
            return ["success" => false, "message" => "Informe bd, fazenda, pasto e data."];
        }
        $dao = new NutricaoDao($bd);
        return [
            "success" => true,
            "nutricoes" => $dao->listarItens([$local], null, $pasto, $data->format('Y-m-d')),
        ];
    }

    // ---------------------------------------------------------------------
    // Confirmar Inclusão
    // ---------------------------------------------------------------------

    /**
     * Mesma regra de gravar_nutricao.php (tipoGravacao 0):
     *   - grava a nutrição com a quantidade de animais do pasto e a
     *     Descrição do Lote (com o número) que o pasto tem no momento;
     *   - baixa a quantidade do estoque do produto na fazenda (se houver
     *     estoque cadastrado);
     *   - Situação do Cocho 006 ("Iniciando Nutrição para este grupo"): só
     *     isso, com o código 006 na própria nutrição;
     *   - outras situações: a nutrição entra com cocho 0 e a situação
     *     informada vai para as nutrições da data ANTERIOR do pasto (se não
     *     estiver encerrada), junto com os dias e o consumo por cabeça/dia.
     *
     * Reenvio seguro: a nutrição fica com incluido_em = data_hora da ação e
     * incluido_por = usuário; se já existe, responde "ignorado" com o id.
     */
    public function incluir($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        $local = (int) ($dados['fazenda'] ?? 0);
        $pasto = (int) ($dados['pasto'] ?? 0);
        $produto = (int) ($dados['produto'] ?? 0);
        $cocho = (int) ($dados['cocho'] ?? 0);
        $quantidade = str_replace(',', '.', trim((string) ($dados['quantidade'] ?? '')));
        $dataObj = DateTime::createFromFormat('!Y-m-d', (string) ($dados['data'] ?? ''));

        if ($bd === '' || $local <= 0 || $pasto <= 0) {
            return ["success" => false, "message" => "Não foi possível identificar a fazenda ou o pasto."];
        }
        if ($cocho <= 0 || !$dataObj || $produto <= 0 || $quantidade === '' || !is_numeric($quantidade)) {
            return ["success" => false, "message" => "Preencha todos os campos da nutrição!"];
        }

        $agora = $this->dataHoraDaAcao($dados);
        $usuario = $this->usuarioDaAcao($dados);
        $data = $dataObj->format('Y-m-d');
        if ($data > substr($agora, 0, 10)) {
            return ["success" => false, "message" => "A Data não pode ser maior que a data atual!"];
        }

        $pastoDao = new PastoDao($bd);
        $con = $pastoDao->getConexao();
        if (!$con) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }
        $dao = new NutricaoDao($bd, $con);
        $animalPastoDao = new AnimalPastoDao($bd, $con);

        mysqli_begin_transaction($con);

        $jaGravada = $dao->buscarDaAcao($local, $pasto, $produto, $usuario, $agora);
        if ($jaGravada > 0) {
            mysqli_rollback($con);
            mysqli_close($con);
            return ["success" => true, "ignorado" => true, "message" => "Registro gravado com sucesso.", "id" => $jaGravada];
        }

        $p = $pastoDao->buscarPastoParaMovimentacao($pasto);
        if (!$p) {
            return $this->falhar($con, 'O pasto não foi encontrado.');
        }

        // Lote como a tela do web manda: "DESCRIÇÃO L-0012/26", id "0012",
        // ano "2026" (pasto sem número: "DESCRIÇÃO ", 0 e 0).
        $idLote = (int) $p['tbl_pasto_id_lote'];
        if ($idLote !== 0) {
            $idLoteTexto = str_pad((string) $idLote, 4, "0", STR_PAD_LEFT);
            $anoLote = (string) $p['tbl_pasto_ano_lote'];
            $descIdLote = 'L-' . $idLoteTexto . '/' . substr($anoLote, 2, 2);
        } else {
            $idLoteTexto = '0';
            $anoLote = '0';
            $descIdLote = '';
        }

        // Nutrição anterior do pasto (não vale para "Iniciando Nutrição").
        $dataAnterior = null;
        if ($cocho !== 6) {
            $anterior = $dao->buscarAnterior($local, $pasto, $data);
            if ($anterior && (string) $anterior['tbl_nutricao_encerrada'] !== 'S') {
                $dataAnterior = (string) $anterior['tbl_nutricao_data'];
            }
        }

        $r = $dao->incluir([
            'data'        => $data,
            'local'       => $local,
            'pasto'       => $pasto,
            'produto'     => $produto,
            'qtd_animais' => $animalPastoDao->contarAtivosNoPasto($pasto),
            'quantidade'  => $quantidade,
            'score_cocho' => $cocho === 6 ? 6 : 0,
            'id_ano_lote' => $idLoteTexto . $anoLote,
            'ano_lote'    => $anoLote,
            'lote'        => (string) $p['tbl_pasto_descricao_lote'] . ' ' . $descIdLote,
            'data_hora'   => $agora,
            'usuario'     => $usuario,
        ]);
        if ($r['error']) {
            return $this->falhar($con, 'Ocorreu um erro na gravação ' . $r['message']);
        }
        $id = $r['id'];

        $r = $dao->movimentarEstoque($produto, $local, -1 * (float) $quantidade, $usuario, $agora);
        if ($r['error']) {
            return $this->falhar($con, 'Erro na alteração do estoque - ' . $r['message']);
        }

        if ($dataAnterior !== null) {
            $dias = (new DateTime($dataAnterior))->diff(new DateTime($data))->days;
            if ($dias == 0) {
                $dias = 1;
            }
            foreach ($dao->listarDoPastoNaData($local, $pasto, $dataAnterior) as $ant) {
                $animais = (float) $ant['tbl_nutricao_qtd_animais'];
                $consumo = $animais > 0
                    ? ((float) $ant['tbl_nutricao_quantidade_produto'] / $animais / $dias) * 1000
                    : 0;
                $r = $dao->atualizarConsumo($ant['tbl_nutricao_id'], $cocho, $dias, $consumo);
                if ($r['error']) {
                    return $this->falhar($con, 'Ocorreu um erro na gravação ' . $r['message']);
                }
            }
        }

        mysqli_commit($con);
        mysqli_close($con);

        return ["success" => true, "ignorado" => false, "message" => "Registro gravado com sucesso.", "id" => $id];
    }

    // ---------------------------------------------------------------------
    // Excluir (lixeira da tabela)
    // ---------------------------------------------------------------------

    /**
     * Mesma regra de gravar_nutricao.php (tipoGravacao 2): apaga a nutrição
     * e devolve a quantidade ao estoque do produto na fazenda.
     *
     * No web, quando o produto não tem estoque cadastrado na fazenda, a
     * nutrição é apagada e a tela mostra "Estoque não encontrado para o
     * produto/local"; aqui a exclusão responde sucesso com esse texto em
     * "aviso" (o registro foi apagado de verdade).
     *
     * Reenvio seguro: registro que já não existe -> "ignorado".
     */
    public function excluir($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        $id = (int) ($dados['id'] ?? 0);
        if ($bd === '' || $id <= 0) {
            return ["success" => false, "message" => "Registro de nutrição não informado."];
        }

        $agora = $this->dataHoraDaAcao($dados);
        $usuario = $this->usuarioDaAcao($dados);

        $pastoDao = new PastoDao($bd);
        $con = $pastoDao->getConexao();
        if (!$con) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }
        $dao = new NutricaoDao($bd, $con);

        mysqli_begin_transaction($con);

        $reg = $dao->buscarPorId($id);
        if (!$reg) {
            mysqli_rollback($con);
            mysqli_close($con);
            return ["success" => true, "ignorado" => true, "message" => "Registro excluido com sucesso."];
        }

        $r = $dao->excluir($id);
        if ($r['error']) {
            return $this->falhar($con, 'Ocorreu um erro na exclusão ' . $r['message']);
        }

        $r = $dao->movimentarEstoque(
            $reg['tbl_nutricao_codigo_produto'],
            $reg['tbl_nutricao_codigo_local'],
            (float) $reg['tbl_nutricao_quantidade_produto'],
            $usuario,
            $agora
        );
        if ($r['error']) {
            return $this->falhar($con, 'Erro na alteração do estoque - ' . $r['message']);
        }

        mysqli_commit($con);
        mysqli_close($con);

        $resposta = ["success" => true, "ignorado" => false, "message" => "Registro excluido com sucesso."];
        if (!$r['encontrado']) {
            $resposta["aviso"] = "Estoque não encontrado para o produto/local";
        }
        return $resposta;
    }

    // ---------------------------------------------------------------------
    // Auxiliares
    // ---------------------------------------------------------------------

    private function falhar($con, $mensagem){
        mysqli_rollback($con);
        mysqli_close($con);
        return ["success" => false, "message" => $mensagem];
    }

    /** data_hora enviada pelo app (Y-m-d H:i:s); sem ela, agora. */
    private function dataHoraDaAcao($dados){
        $d = DateTime::createFromFormat('Y-m-d H:i:s', (string) ($dados['data_hora'] ?? ''));
        return $d ? $d->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
    }

    /** incluido_por é varchar(30). */
    private function usuarioDaAcao($dados){
        return mb_substr(trim((string) ($dados['usuario'] ?? '')), 0, 30);
    }
}
