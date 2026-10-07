<?php
/**
 * Consultas e gravações da MORTE de um animal (controle de estoque por
 * animal), usadas por MapaGadoService::gravarMorte() — as mesmas de
 * gravar_morte.php do sistema web, só que na conexão/transação recebida.
 *
 * Os métodos de gravação devolvem ["error" => bool, "message" => string].
 */
class MorteDao{

    private $con;

    /** $con: conexão já aberta (a transação é do service); sem ela, abre
     *  uma nova. */
    public function __construct($banco, $con = null){
        if ($con) {
            $this->con = $con;
        } else {
            require __DIR__ . "/../../conecta_mysql_credenciais.inc";
            $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        }
        mysqli_set_charset($this->con, "utf8");
    }

    private function esc($valor){
        return mysqli_real_escape_string($this->con, (string) $valor);
    }

    private function resultado($ok){
        return $ok ? ["error" => false, "message" => ""]
                   : ["error" => true, "message" => mysqli_error($this->con)];
    }

    private function linha($sql){
        $r = mysqli_query($this->con, $sql);
        return $r ? mysqli_fetch_assoc($r) : null;
    }

    // ---------------------------------------------------------------------
    // Listas para o aplicativo
    // ---------------------------------------------------------------------

    /** Motivos de morte (select "Motivo da Morte"), na ordem do web. */
    public function listarMotivos(){
        $a = [];
        $r = mysqli_query($this->con, "SELECT tab_codigo_causa_morte, tab_descricao_causa_morte
            FROM tabela_causa_morte
            WHERE tab_registro_lixeira_causa_morte = 0");
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $a[] = [
                    "id" => (int) $row['tab_codigo_causa_morte'],
                    "descricao" => (string) $row['tab_descricao_causa_morte'],
                ];
            }
        }
        return $a;
    }

    public function descricaoMotivo($codigo){
        $codigo = (int) $codigo;
        $row = $this->linha("SELECT tab_descricao_causa_morte
            FROM tabela_causa_morte
            WHERE tab_codigo_causa_morte = {$codigo} AND tab_registro_lixeira_causa_morte = 0");
        return $row ? (string) $row['tab_descricao_causa_morte'] : null;
    }

    /**
     * Animais ativos das fazendas que estão "em Estação de Monta" — mesma
     * regra de ler_animal_movimentacao_morte_outra.php: a estação do animal
     * é a da cobertura mais recente dele; está em estação se o item mais
     * recente de cobertura (controle 'C') dessa estação ainda não tem
     * nascido/aborto registrado.
     */
    public function listarAnimaisEmEstacaoMonta($fazendas){
        $ids = array_values(array_filter(array_map('intval', $fazendas), function ($id) {
            return $id > 0;
        }));
        if (count($ids) === 0) {
            return [];
        }

        $r = mysqli_query($this->con, "SELECT tbl_ite_cobertura_codigo_id_animal AS animal,
                tbl_cobertura_codigo_estacao_monta AS estacao,
                tbl_cobertura_controle AS controle,
                tbl_ite_cobertura_nascido AS nascido
            FROM tbl_item_cobertura
            INNER JOIN tbl_cobertura ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
            INNER JOIN tbl_parametro_estacao_monta
                    ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
            INNER JOIN tbl_animais ON tbl_animal_codigo_id = tbl_ite_cobertura_codigo_id_animal
            WHERE tbl_cobertura_lixeira = 0 AND
                  tbl_animal_ativo = 'S' AND tbl_animal_lixeira = 0 AND
                  tbl_animal_codigo_fazenda IN (" . implode(',', $ids) . ")
            ORDER BY tbl_ite_cobertura_numero_id DESC");

        $estacao = [];   // animal => estação da cobertura mais recente
        $decidido = [];  // animal => bool
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $animal = (int) $row['animal'];
                if (!isset($estacao[$animal])) {
                    $estacao[$animal] = $row['estacao'];
                }
                if (isset($decidido[$animal])) {
                    continue;
                }
                if ($row['controle'] === 'C' && $row['estacao'] == $estacao[$animal]) {
                    $decidido[$animal] = ((string) $row['nascido'] === '');
                }
            }
        }
        $lista = [];
        foreach ($decidido as $animal => $emEstacao) {
            if ($emEstacao) {
                $lista[] = $animal;
            }
        }
        return $lista;
    }

    // ---------------------------------------------------------------------
    // Animal
    // ---------------------------------------------------------------------

    /** Cadastro do animal (travado para a transação), com raça, pelagem e
     *  o código da mãe como o web grava no item da movimentação. */
    public function buscarAnimal($codigoId){
        $codigoId = (int) $codigoId;
        return $this->linha("SELECT a.tbl_animal_codigo_id, a.tbl_animal_codigo_alfa,
                a.tbl_animal_codigo_numerico, a.tbl_animal_sexo, a.tbl_animal_data_nascimento,
                a.tbl_animal_codigo_fazenda, a.tbl_animal_codigo_origem,
                a.tbl_animal_ativo, a.tbl_animal_situacao, a.tbl_animal_lixeira,
                a.tbl_animal_primeiro_peso, a.tbl_animal_peso_desmama, a.tbl_animal_ultimo_peso,
                r.tab_descricao_raca AS desc_raca, p.tab_descricao_pelagem AS desc_pelagem,
                mae.tbl_animal_codigo_alfa AS mae_alfa, mae.tbl_animal_codigo_numerico AS mae_numerico
            FROM tbl_animais a
            LEFT JOIN tabela_racas r ON r.tab_codigo_raca = a.tbl_animal_codigo_raca
            LEFT JOIN tabela_pelagens p ON p.tab_codigo_pelagem = a.tbl_animal_codigo_pelagem
            LEFT JOIN tbl_animais mae ON mae.tbl_animal_codigo_id = a.tbl_animal_codigo_mae
            WHERE a.tbl_animal_codigo_id = {$codigoId}
            FOR UPDATE");
    }

    public function baixarAnimalPorMorte($codigoId, $dataMorte, $usuario, $observacao, $origemAnterior, $fazendaAnterior){
        $codigoId = (int) $codigoId;
        return $this->resultado(mysqli_query($this->con, "UPDATE tbl_animais SET
                tbl_animal_ativo = 'N',
                tbl_animal_baixado_em = '" . $this->esc($dataMorte) . "',
                tbl_animal_baixado_por = '" . $this->esc($usuario) . "',
                tbl_animal_observacao = '" . $this->esc($observacao) . "',
                tbl_animal_codigo_origem_anterior = '" . $this->esc($origemAnterior) . "',
                tbl_animal_codigo_fazenda_anterior = '" . $this->esc($fazendaAnterior) . "',
                tbl_animal_situacao = 'M'
            WHERE tbl_animal_codigo_id = {$codigoId}"));
    }

    // ---------------------------------------------------------------------
    // Animal no pasto (tbl_animal_pasto)
    // ---------------------------------------------------------------------

    /** Registro do pasto com esse sexo e essa data de nascimento. */
    public function buscarNoPastoPorNascimento($local, $pasto, $sexo, $nascimento){
        $local = (int) $local;
        $pasto = (int) $pasto;
        return $this->linha("SELECT tbl_animal_pasto_numero_item, tbl_animal_pasto_nascimento
            FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_local = {$local} AND
                  tbl_animal_pasto_id = {$pasto} AND
                  tbl_animal_pasto_sexo = '" . $this->esc($sexo) . "' AND
                  tbl_animal_pasto_nascimento = '" . $this->esc($nascimento) . "'
            LIMIT 1");
    }

    /** Registro mais recente do pasto com esse sexo e categoria. */
    public function buscarNoPastoPorCategoria($local, $pasto, $sexo, $categoria){
        $local = (int) $local;
        $pasto = (int) $pasto;
        $categoria = (int) $categoria;
        return $this->linha("SELECT tbl_animal_pasto_numero_item, tbl_animal_pasto_nascimento
            FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_local = {$local} AND
                  tbl_animal_pasto_id = {$pasto} AND
                  tbl_animal_pasto_sexo = '" . $this->esc($sexo) . "' AND
                  tbl_animal_pasto_categoria = {$categoria}
            ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");
    }

    /** Registro mais recente da fazenda (qualquer pasto) com esse sexo e
     *  essa data de nascimento. */
    public function buscarNaFazendaPorNascimento($local, $sexo, $nascimento){
        $local = (int) $local;
        return $this->linha("SELECT tbl_animal_pasto_numero_item, tbl_animal_pasto_nascimento,
                tbl_animal_pasto_id
            FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_local = {$local} AND
                  tbl_animal_pasto_sexo = '" . $this->esc($sexo) . "' AND
                  tbl_animal_pasto_nascimento = '" . $this->esc($nascimento) . "'
            ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");
    }

    public function trocarNascimentoNoPasto($local, $pasto, $numeroItem, $nascimento){
        $local = (int) $local;
        $pasto = (int) $pasto;
        $numeroItem = (int) $numeroItem;
        return $this->resultado(mysqli_query($this->con, "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_nascimento = '" . $this->esc($nascimento) . "'
            WHERE tbl_animal_pasto_local = {$local} AND
                  tbl_animal_pasto_numero_item = {$numeroItem} AND
                  tbl_animal_pasto_id = {$pasto}"));
    }

    public function excluirDoPasto($local, $numeroItem){
        $local = (int) $local;
        $numeroItem = (int) $numeroItem;
        return $this->resultado(mysqli_query($this->con, "DELETE FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_local = {$local} AND
                  tbl_animal_pasto_numero_item = {$numeroItem}"));
    }

    /** Todas as linhas do pasto nessa fazenda (qualquer situação). */
    public function contarRegistrosNoPasto($local, $pasto){
        $local = (int) $local;
        $pasto = (int) $pasto;
        $row = $this->linha("SELECT COUNT(*) AS qtd FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_local = {$local} AND tbl_animal_pasto_id = {$pasto}");
        return $row ? (int) $row['qtd'] : 0;
    }

    // ---------------------------------------------------------------------
    // Fechamento mensal (morte com data de um mês já fechado)
    // ---------------------------------------------------------------------

    public function baixarDoFechamentoMensal($local, $dataFechamento, $categoria, $sexo, $peso){
        $local = (int) $local;
        $categoria = (int) $categoria;
        $row = $this->linha("SELECT tbl_fechamento_id, tbl_fechamento_qtd, tbl_fechamento_peso
            FROM tbl_fechamento_mensal_estoque
            WHERE tbl_fechamento_local = {$local} AND
                  tbl_fechamento_data = '" . $this->esc($dataFechamento) . "' AND
                  tbl_fechamento_categoria = {$categoria} AND
                  tbl_fechamento_sexo = '" . $this->esc($sexo) . "'
            LIMIT 1 FOR UPDATE");
        if (!$row) {
            return ["error" => false, "message" => ""];
        }
        $qtd = ((int) $row['tbl_fechamento_qtd']) - 1;
        $pesoNovo = ((float) $row['tbl_fechamento_peso']) - (float) $peso;
        return $this->resultado(mysqli_query($this->con, "UPDATE tbl_fechamento_mensal_estoque SET
                tbl_fechamento_qtd = '{$qtd}',
                tbl_fechamento_peso = '{$pesoNovo}'
            WHERE tbl_fechamento_id = " . (int) $row['tbl_fechamento_id']));
    }

    public function somarMorteNoFechamentoEntSai($local, $dataFechamento, $peso){
        $local = (int) $local;
        $row = $this->linha("SELECT tbl_fechamento_id, tbl_fechamento_peso_sai_morte, tbl_fechamento_peso_final
            FROM tbl_fechamento_mensal_estoque_ent_sai_peso
            WHERE tbl_fechamento_local = {$local} AND
                  tbl_fechamento_data = '" . $this->esc($dataFechamento) . "'
            LIMIT 1 FOR UPDATE");
        if (!$row) {
            return ["error" => false, "message" => ""];
        }
        $pesoMorte = ((float) $row['tbl_fechamento_peso_sai_morte']) + (float) $peso;
        $pesoFinal = ((float) $row['tbl_fechamento_peso_final']) - (float) $peso;
        return $this->resultado(mysqli_query($this->con, "UPDATE tbl_fechamento_mensal_estoque_ent_sai_peso SET
                tbl_fechamento_peso_sai_morte = '{$pesoMorte}',
                tbl_fechamento_peso_final = '{$pesoFinal}'
            WHERE tbl_fechamento_id = " . (int) $row['tbl_fechamento_id']));
    }

    // ---------------------------------------------------------------------
    // Movimentação de morte (tipo 888)
    // ---------------------------------------------------------------------

    /** Morte já gravada por essa mesma ação do aplicativo (usuário +
     *  data/hora da ação) para esse animal — reenvio seguro. */
    public function existeMovimentacaoDaAcao($local, $codigoId, $usuario, $dataHora){
        $local = (int) $local;
        $codigoId = (int) $codigoId;
        $row = $this->linha("SELECT m.tbl_movimentacao_id
            FROM tbl_movimentacao m
            INNER JOIN tbl_item_movimentacao i ON i.tbl_ite_movimentacao_numero_id = m.tbl_movimentacao_id
            WHERE m.tbl_movimentacao_tipo = 888 AND
                  m.tbl_movimentacao_codigo_local_origem = {$local} AND
                  m.tbl_movimentacao_incluido_em = '" . $this->esc($dataHora) . "' AND
                  m.tbl_movimentacao_incluido_por = '" . $this->esc($usuario) . "' AND
                  i.tbl_ite_movimentacao_codigo_id_animal = {$codigoId}
            LIMIT 1");
        return $row !== null && $row !== false;
    }

    /** Cabeçalho da movimentação; devolve também "id". */
    public function incluirMovimentacao($local, $dataMorte, $usuario, $dataHora){
        $local = (int) $local;
        $ok = mysqli_query($this->con, "INSERT INTO tbl_movimentacao (
                tbl_movimentacao_controle, tbl_movimentacao_data,
                tbl_movimentacao_codigo_local_origem, tbl_movimentacao_codigo_local_destino,
                tbl_movimentacao_tipo, tbl_movimentacao_qtd_animais_pesados,
                tbl_movimentacao_peso_kg, tbl_movimentacao_peso_arroba,
                tbl_movimentacao_peso_medio_kg, tbl_movimentacao_peso_medio_arroba,
                tbl_movimentacao_filtros, tbl_movimentacao_situacao,
                tbl_movimentacao_incluido_em, tbl_movimentacao_incluido_por,
                tbl_movimentacao_lixeira
            ) VALUES (
                'I', '" . $this->esc($dataMorte) . "',
                {$local}, NULL,
                888, 1,
                0, 0,
                0, 0,
                NULL, 'N',
                '" . $this->esc($dataHora) . "', '" . $this->esc($usuario) . "',
                0
            )");
        $r = $this->resultado($ok);
        $r["id"] = $ok ? (int) mysqli_insert_id($this->con) : 0;
        return $r;
    }

    public function incluirItemMovimentacao($numeroMovimentacao, $dataMorte, $item){
        $numeroMovimentacao = (int) $numeroMovimentacao;
        return $this->resultado(mysqli_query($this->con, "INSERT INTO tbl_item_movimentacao (
                tbl_ite_movimentacao_numero_id, tbl_ite_movimentacao_numero_item,
                tbl_ite_movimentacao_data_emissao, tbl_ite_movimentacao_codigo_id_animal,
                tbl_ite_movimentacao_codigo_animal, tbl_ite_movimentacao_peso,
                tbl_ite_movimentacao_sexo, tbl_ite_movimentacao_nascimento,
                tbl_ite_movimentacao_raca, tbl_ite_movimentacao_pelagem,
                tbl_ite_movimentacao_mae, tbl_ite_movimentacao_observacao,
                tbl_ite_movimentacao_motivo_morte, tbl_ite_movimentacao_codigo_pasto,
                tbl_ite_movimentacao_codigo_categoria
            ) VALUES (
                {$numeroMovimentacao}, 1,
                '" . $this->esc($dataMorte) . "', " . (int) $item['codigo_id'] . ",
                '" . $this->esc($item['codigo_animal']) . "', 0,
                '" . $this->esc($item['sexo']) . "', '" . $this->esc($item['nascimento']) . "',
                '" . $this->esc($item['raca']) . "', '" . $this->esc($item['pelagem']) . "',
                '" . $this->esc($item['mae']) . "', '" . $this->esc($item['observacao']) . "',
                " . (int) $item['motivo'] . ", " . (int) $item['pasto'] . ",
                " . (int) $item['categoria'] . "
            )"));
    }

    public function incluirSaidaEstoque($codigoId, $dataMorte, $nascimento, $local, $numeroMovimentacao, $pasto, $categoria, $sexo){
        return $this->resultado(mysqli_query($this->con, "INSERT INTO tbl_movimentacao_estoque (
                tbl_mov_estoque_codigo_id_animal, tbl_mov_estoque_data_emissao,
                tbl_mov_estoque_nascimento, tbl_mov_estoque_local,
                tbl_mov_estoque_entrada_saida, tbl_mov_estoque_tipo_movimentacao,
                tbl_mov_estoque_local_origem, tbl_mov_estoque_local_destino,
                tbl_mov_estoque_codigo_movimentacao, tbl_mov_estoque_codigo_pasto,
                tbl_mov_estoque_codigo_categoria, tbl_mov_estoque_codigo_raca,
                tbl_mov_estoque_codigo_pelagem, tbl_mov_estoque_sexo,
                tbl_mov_estoque_primeiro_peso
            ) VALUES (
                " . (int) $codigoId . ", '" . $this->esc($dataMorte) . "',
                '" . $this->esc($nascimento) . "', " . (int) $local . ",
                'S', 'M',
                " . (int) $local . ", NULL,
                " . (int) $numeroMovimentacao . ", " . (int) $pasto . ",
                " . (int) $categoria . ", NULL,
                NULL, '" . $this->esc($sexo) . "',
                NULL
            )"));
    }

    // ---------------------------------------------------------------------
    // Cobertura (reprodução) — fêmea que morreu em estação de monta
    // ---------------------------------------------------------------------

    /** Item de cobertura mais recente do animal (controle 'C'). */
    public function buscarUltimaCobertura($codigoId){
        $codigoId = (int) $codigoId;
        return $this->linha("SELECT tbl_ite_cobertura_numero_id, tbl_ite_cobertura_numero_item,
                tbl_ite_cobertura_dia_1, tbl_ite_cobertura_resultado_diagnostico,
                tbl_ite_cobertura_nascido, tbl_cobertura_qtd_animais,
                tbl_cobertura_codigo_grupo, tbl_cobertura_protocoloiatf
            FROM tbl_item_cobertura
            INNER JOIN tbl_cobertura ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
            WHERE tbl_cobertura_lixeira = 0 AND
                  tbl_cobertura_controle = 'C' AND
                  tbl_ite_cobertura_codigo_id_animal = {$codigoId}
            ORDER BY tbl_cobertura_id DESC LIMIT 1");
    }

    public function atualizarQtdAnimaisCobertura($coberturaId, $qtd, $usuario, $dataHora){
        return $this->resultado(mysqli_query($this->con, "UPDATE tbl_cobertura SET
                tbl_cobertura_qtd_animais = '" . (int) $qtd . "',
                tbl_cobertura_alterado_em = '" . $this->esc($dataHora) . "',
                tbl_cobertura_alterado_por = '" . $this->esc($usuario) . "'
            WHERE tbl_cobertura_id = " . (int) $coberturaId));
    }

    public function excluirItemCobertura($coberturaId, $numeroItem){
        return $this->resultado(mysqli_query($this->con, "DELETE FROM tbl_item_cobertura
            WHERE tbl_ite_cobertura_numero_id = " . (int) $coberturaId . " AND
                  tbl_ite_cobertura_numero_item = " . (int) $numeroItem));
    }

    /** Refaz a sequência dos itens da cobertura (1, 2, 3...) depois de
     *  excluir um — em dois passos, como o web (9001... e depois 1...). */
    public function renumerarItensCobertura($coberturaId){
        $coberturaId = (int) $coberturaId;
        foreach ([9000, 0] as $base) {
            $r = mysqli_query($this->con, "SELECT tbl_ite_cobertura_numero_item
                FROM tbl_item_cobertura
                WHERE tbl_ite_cobertura_numero_id = {$coberturaId}
                ORDER BY tbl_ite_cobertura_numero_item ASC");
            if (!$r) {
                return $this->resultado(false);
            }
            $antigos = [];
            while ($row = mysqli_fetch_assoc($r)) {
                $antigos[] = (int) $row['tbl_ite_cobertura_numero_item'];
            }
            $novo = $base;
            foreach ($antigos as $antigo) {
                $novo++;
                $ok = mysqli_query($this->con, "UPDATE tbl_item_cobertura SET
                        tbl_ite_cobertura_numero_item = {$novo}
                    WHERE tbl_ite_cobertura_numero_id = {$coberturaId} AND
                          tbl_ite_cobertura_numero_item = {$antigo}");
                if (!$ok) {
                    return $this->resultado(false);
                }
            }
        }
        return ["error" => false, "message" => ""];
    }

    /** Marca o item de cobertura como "nascido = Outro, fêmea morta";
     *  $negativar também passa o diagnóstico para Negativo. */
    public function marcarCoberturaFemeaMorta($coberturaId, $numeroItem, $negativar, $usuario, $dataHora){
        $diagnostico = $negativar ? "tbl_ite_cobertura_resultado_diagnostico = 'N'," : "";
        return $this->resultado(mysqli_query($this->con, "UPDATE tbl_item_cobertura SET
                tbl_ite_cobertura_nascido = 'O',
                tbl_ite_cobertura_situacao_femea_nascido_outro = 'M',
                {$diagnostico}
                tbl_ite_cobertura_negativo_em = '" . $this->esc($dataHora) . "',
                tbl_ite_cobertura_negativo_por = '" . $this->esc($usuario) . "'
            WHERE tbl_ite_cobertura_numero_id = " . (int) $coberturaId . " AND
                  tbl_ite_cobertura_numero_item = " . (int) $numeroItem));
    }
}
