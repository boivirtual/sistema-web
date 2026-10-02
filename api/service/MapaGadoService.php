<?php
/**
 * Mapa de Gado (Tabuleiro) do aplicativo — regras de negócio.
 *
 * Endpoints: api/rest/mapa-gado/tabuleiro.php, transferir_tudo.php e
 * descricao_lote.php (só leem a requisição e devolvem o JSON).
 * Consultas/gravações: PastoDao, AnimalPastoDao, NutricaoDao,
 * CategoriaIdadeDao e LoteAnimaisDao — todas na MESMA conexão, para a
 * transferência e a descrição do lote rodarem numa transação só.
 *
 * Mesmas regras do sistema web (ler_mapa_gados.php,
 * transferir_tudo_mapa_gados.php e gravar_alterar_descricao_lote.php), com
 * uma diferença deliberada: "agora" é a data/hora em que a ação foi feita no
 * aparelho (data_hora), não a hora em que chegou ao servidor — uma ação
 * feita offline e enviada horas depois grava as mesmas datas que gravaria
 * se tivesse internet na hora.
 */
class MapaGadoService{

    // ---------------------------------------------------------------------
    // Tabuleiro (exportação para o cache offline do app)
    // ---------------------------------------------------------------------

    /**
     * Dados "crus" do tabuleiro das fazendas (pastos + animais ativos +
     * faixas de categoria + opções de descrição do lote). As contagens de
     * bezerros/fêmeas/machos dependem da idade no dia, então o app calcula
     * na hora, a partir do cache.
     *
     * "ordem" é a posição do pasto no tabuleiro dentro da fazenda: primeiro
     * ENTRADA/SAÍDA (módulo 999), depois os demais, com o MESMO ORDER BY do
     * web — que não desempata pastos do mesmo módulo; por isso a posição sai
     * daqui, do próprio resultado do MySQL, e não é reordenada no app.
     */
    public function tabuleiro($bd, $fazendas){
        $idsFazendas = array_values(array_unique(array_filter(
            array_map('intval', is_array($fazendas) ? $fazendas : []),
            function ($id) { return $id > 0; }
        )));
        if (trim((string) $bd) === '' || count($idsFazendas) === 0) {
            return ["success" => false, "message" => "Informe bd e a lista de fazendas."];
        }

        $pastoDao = new PastoDao($bd);
        $con = $pastoDao->getConexao();
        if (!$con) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }
        $animalPastoDao = new AnimalPastoDao($bd, $con);
        $categoriaDao = new CategoriaIdadeDao($bd, $con);
        $loteDao = new LoteAnimaisDao($bd, $con);

        $categorias = [];
        foreach ($categoriaDao->getCategoria() as $categoria) {
            $categorias[] = [
                "id"  => (int) $categoria->getId(),
                "de"  => (int) $categoria->getIdadeDe(),
                "ate" => (int) $categoria->getIdadeAte(),
            ];
        }

        $descricoesLote = [];
        foreach ($loteDao->listarDescricoes() as $d) {
            $descricoesLote[] = [
                "id"        => (int) $d['tbl_descricao_lote_id'],
                "descricao" => (string) $d['tbl_descricao_lote'],
            ];
        }

        $colunasLote = $pastoDao->colunasDescricaoLote();
        $animalDao = new AnimalDao($bd, $con);
        $pastos = [];
        $idsPastos = [];
        $pesosMedios = [];
        foreach ($idsFazendas as $fazenda) {
            $ordem = 0;
            $linhas = array_merge(
                $pastoDao->listarPastosEntradaSaida($fazenda, $colunasLote),
                $pastoDao->listarPastosModulos($fazenda, $colunasLote)
            );
            $foraTabuleiro = $pastoDao->listarPastosForaTabuleiro($fazenda, $colunasLote);
            foreach ($foraTabuleiro as $i => $row) {
                $foraTabuleiro[$i]['_fora_tabuleiro'] = true;
            }
            foreach (array_merge($linhas, $foraTabuleiro) as $row) {
                $ordem++;
                $idsPastos[] = (int) $row['tbl_pasto_id'];
                $lotes = [];
                for ($i = 1; $i <= 6; $i++) {
                    $lotes[] = (string) ($row["tbl_pasto_descricao_lote_{$i}"] ?? '');
                }
                $pastos[] = [
                    "id"             => (int) $row['tbl_pasto_id'],
                    "local"          => (int) $row['tbl_pasto_codigo_local'],
                    "descricao"      => (string) $row['tbl_pasto_descricao'],
                    "modulo"         => (int) $row['tbl_pasto_modulo'],
                    "capim"          => (string) ($row['tbl_tipo_capim_descricao'] ?? ''),
                    "categorias"     => (string) ($row['tbl_pasto_array_categoria'] ?? ''),
                    "ordem"          => $ordem,
                    "descricao_lote" => (string) ($row['tbl_pasto_descricao_lote'] ?? ''),
                    "lotes"          => $lotes,
                    // false = módulos 1006/1007: só aparecem no Mapa Satélite
                    "tabuleiro"      => empty($row['_fora_tabuleiro']),
                    "data_com_animais" => $row['tbl_pasto_data_com_animais'],
                    "data_sem_animais" => $row['tbl_pasto_data_sem_animais'],
                    // Lotação (Kg/Ha) e "L-0012/26" da tela do pasto
                    "area"           => (float) ($row['tbl_pasto_area'] ?? 0),
                    "id_lote"        => (int) ($row['tbl_pasto_id_lote'] ?? 0),
                    "ano_lote"       => (int) ($row['tbl_pasto_ano_lote'] ?? 0),
                ];
            }

            foreach ($animalDao->pesosMediosPorCategoriaSexo($fazenda) as $peso) {
                $pesosMedios[] = ["local" => $fazenda] + $peso;
            }
        }

        $animais = [];
        foreach ($animalPastoDao->listarAtivosDosPastos($idsPastos) as $row) {
            $animais[] = [
                "local"      => (int) $row['tbl_animal_pasto_local'],
                "item"       => (int) $row['tbl_animal_pasto_numero_item'],
                "pasto"      => (int) $row['tbl_animal_pasto_id'],
                "sexo"       => (string) $row['tbl_animal_pasto_sexo'],
                "nascimento" => $row['tbl_animal_pasto_nascimento'],
            ];
        }

        mysqli_close($con);

        return [
            "success"         => true,
            "categorias"      => $categorias,
            "descricoes_lote" => $descricoesLote,
            "pastos"          => $pastos,
            "animais"         => $animais,
            "pesos_medios"    => $pesosMedios,
        ];
    }

    // ---------------------------------------------------------------------
    // Mapa Satélite (exportação para o cache offline do app)
    // ---------------------------------------------------------------------

    /**
     * Mapa de cada fazenda (GeoJSON dos pastos, mesma fonte do web), cor
     * de cada módulo e coordenada da fazenda. $versoes = {fazenda: versão
     * que o app já tem}: o GeoJSON (o pedaço pesado) só volta quando mudou
     * — "geojson": null significa "o seu ainda vale".
     */
    public function satelite($bd, $fazendas, $versoes){
        $idsFazendas = array_values(array_unique(array_filter(
            array_map('intval', is_array($fazendas) ? $fazendas : []),
            function ($id) { return $id > 0; }
        )));
        if (trim((string) $bd) === '' || count($idsFazendas) === 0) {
            return ["success" => false, "message" => "Informe bd e a lista de fazendas."];
        }
        $versoes = is_array($versoes) ? $versoes : [];

        $con = (new PastoDao($bd))->getConexao();
        if (!$con) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }
        $mapaDao = new MapaFazendaDao($bd, $con);

        $modulos = [];
        foreach ($mapaDao->listarModulosComCor() as $m) {
            $modulos[] = ["id" => (int) $m['id'], "cor" => (string) $m['cor']];
        }

        $mapas = [];
        foreach ($idsFazendas as $fazenda) {
            $leitura = $mapaDao->lerMapa($fazenda);
            $geojson = null;
            $versao = $leitura['json'] === '' ? '' : $leitura['versao'];

            if ($versao !== '' && (string) ($versoes[(string) $fazenda] ?? '') !== $versao) {
                $geojson = json_decode($leitura['json']);
                if ($geojson === null || !isset($geojson->features)) {
                    // mapa inválido: igual ao web, não mostra nada
                    $geojson = null;
                    $versao = '';
                }
            }

            $coordenadas = $mapaDao->coordenadasFazenda($fazenda);
            $mapas[] = [
                "local"     => $fazenda,
                "versao"    => $versao,
                "geojson"   => $geojson,
                "latitude"  => $coordenadas['latitude'],
                "longitude" => $coordenadas['longitude'],
            ];
        }

        mysqli_close($con);

        return ["success" => true, "modulos" => $modulos, "mapas" => $mapas];
    }

    // ---------------------------------------------------------------------
    // Mover TODOS os animais de um pasto para outro
    // ---------------------------------------------------------------------

    /**
     * Mesma regra de transferir_tudo_mapa_gados.php:
     *   - datas com/sem animais dos dois pastos (janela de 24h);
     *   - Premissa 1: destino SEM descrição do lote e origem COM -> a
     *     descrição vai para o destino;
     *   - Premissas 1 e 6: a origem sempre fica sem descrição do lote;
     *   - nutrição do dia passa para o pasto destino;
     *   - animais ativos da origem passam para o destino.
     *
     * Reenvio seguro: se a origem já não tem animais ativos (ação já
     * aplicada, resposta perdida no caminho, ou alguém já esvaziou o pasto),
     * não altera nada e responde sucesso com "ignorado": true.
     */
    public function transferirTudo($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        $origem = (int) ($dados['origem'] ?? 0);
        $destino = (int) ($dados['destino'] ?? 0);

        if ($bd === '' || $origem <= 0 || $destino <= 0) {
            return ["success" => false, "message" => "Não foi possível identificar o pasto de origem ou de destino."];
        }
        if ($origem === $destino) {
            return ["success" => false, "message" => "O pasto de origem e o de destino são o mesmo."];
        }

        $agora = $this->dataHoraDaAcao($dados);
        $usuario = $this->usuarioDaAcao($dados);

        $pastoDao = new PastoDao($bd);
        $con = $pastoDao->getConexao();
        if (!$con) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }
        $animalPastoDao = new AnimalPastoDao($bd, $con);
        $nutricaoDao = new NutricaoDao($bd, $con);

        mysqli_begin_transaction($con);

        $pOrigem = $pastoDao->buscarPastoParaMovimentacao($origem);
        if (!$pOrigem) {
            return $this->falhar($con, 'O pasto de origem não foi encontrado.');
        }
        $pDestino = $pastoDao->buscarPastoParaMovimentacao($destino);
        if (!$pDestino) {
            return $this->falhar($con, 'O pasto de destino não foi encontrado.');
        }

        if ($animalPastoDao->contarAtivosNoPasto($origem) === 0) {
            mysqli_rollback($con);
            mysqli_close($con);
            return [
                "success" => true,
                "ignorado" => true,
                "message" => "O pasto de origem já não tem animais.",
                "descricao_lote_pasto_destino" => (string) $pDestino['tbl_pasto_descricao_lote'],
            ];
        }

        $descricaoOrigem = (string) $pOrigem['tbl_pasto_descricao_lote'];
        $descricaoDestino = (string) $pDestino['tbl_pasto_descricao_lote'];
        $registrosDestino = $animalPastoDao->contarRegistrosNoPasto($destino);

        // PASTO DE ORIGEM — datas
        $semAnterior = $this->dataOuAgora($pOrigem['tbl_pasto_data_sem_animais_anterior'], $agora);
        if ($this->horasDesde($agora, $pOrigem['tbl_pasto_data_com_animais']) < 24) {
            $campos = [
                'tbl_pasto_data_com_animais'          => $semAnterior,
                'tbl_pasto_data_com_animais_anterior' => $semAnterior,
                'tbl_pasto_data_sem_animais'          => $semAnterior,
                'tbl_pasto_data_sem_animais_anterior' => $semAnterior,
            ];
            $erro = 'Ocorreu um erro ao atualizar as datas SEM retornar data anterior ';
        } else {
            $campos = $this->camposLoteVazio() + [
                'tbl_pasto_data_sem_animais'          => $agora,
                'tbl_pasto_data_sem_animais_anterior' => $this->dataOuAgora($pOrigem['tbl_pasto_data_sem_animais'], $agora),
            ];
            $erro = 'Ocorreu um erro ao atualizar as datas SEM ';
        }
        $r = $pastoDao->atualizarCamposPasto($origem, $campos, $usuario, $agora);
        if ($r['error']) {
            return $this->falhar($con, $erro . $r['message']);
        }

        // PASTO DE DESTINO — datas (só quando não tinha nenhum registro de animal)
        if ($registrosDestino == 0) {
            $comAnterior = $this->dataOuAgora($pDestino['tbl_pasto_data_com_animais_anterior'], $agora);
            $semDestino = $this->dataOuAgora($pDestino['tbl_pasto_data_sem_animais'], $agora);
            if ($this->horasDesde($agora, $pDestino['tbl_pasto_data_com_animais']) < 24 ||
                $this->horasDesde($agora, $semDestino) < 24) {
                $campos = [
                    'tbl_pasto_data_com_animais'          => $comAnterior,
                    'tbl_pasto_data_com_animais_anterior' => $comAnterior,
                    'tbl_pasto_data_sem_animais'          => $comAnterior,
                    'tbl_pasto_data_sem_animais_anterior' => $comAnterior,
                ];
            } else {
                $campos = [
                    'tbl_pasto_data_com_animais'          => $agora,
                    'tbl_pasto_data_com_animais_anterior' => $pDestino['tbl_pasto_data_com_animais'],
                ];
            }
            $r = $pastoDao->atualizarCamposPasto($destino, $campos, $usuario, $agora);
            if ($r['error']) {
                return $this->falhar($con, 'Ocorreu um erro ao atualizar as datas COM ' . $r['message']);
            }
        }

        // DESCRIÇÃO DO LOTE — Premissa 1
        if ($descricaoOrigem != '' && $descricaoDestino == '') {
            $campos = [
                'tbl_pasto_descricao_lote' => $descricaoOrigem,
                'tbl_pasto_id_lote'        => (string) $pOrigem['tbl_pasto_id_lote'],
                'tbl_pasto_ano_lote'       => (string) $pOrigem['tbl_pasto_ano_lote'],
            ];
            for ($i = 1; $i <= 6; $i++) {
                $campos["tbl_pasto_descricao_lote_{$i}"] = (string) $pOrigem["tbl_pasto_descricao_lote_{$i}"];
            }
            $r = $pastoDao->atualizarCamposPasto($destino, $campos, $usuario, $agora);
            if ($r['error']) {
                return $this->falhar($con, 'Ocorreu um erro ao atualizar a Descrição do Lote do Pasto Destino ' . $r['message']);
            }
            $descricaoDestino = $descricaoOrigem;
        }

        // Premissas 1 e 6: a origem sempre fica sem descrição do lote.
        $r = $pastoDao->atualizarCamposPasto($origem, $this->camposLoteVazio(), $usuario, $agora);
        if ($r['error']) {
            return $this->falhar($con, 'Ocorreu um erro ao atualizar a Descrição do Lote do Pasto Origem ' . $r['message']);
        }

        // Nutrição do dia vai junto (igual ao web, sem bloquear se falhar).
        $nutricaoDao->transferirNutricaoDoDia($origem, $destino, substr($agora, 0, 10));

        $r = $animalPastoDao->transferirAtivos($origem, $destino, $usuario, $agora);
        if ($r['error']) {
            return $this->falhar($con, 'Ocorreu um erro ao atualizar os animais no pasto ' . $r['message']);
        }

        mysqli_commit($con);
        mysqli_close($con);

        return [
            "success" => true,
            "ignorado" => false,
            "message" => "Animais movidos com sucesso.",
            "descricao_lote_pasto_destino" => $descricaoDestino,
        ];
    }

    // ---------------------------------------------------------------------
    // Nova Descrição do Lote
    // ---------------------------------------------------------------------

    /**
     * Mesma regra de gravar_alterar_descricao_lote.php com novo_id = 'S':
     * as descrições preenchidas (até 6) vão para as primeiras posições e o
     * pasto recebe um novo número de lote da fazenda/ano.
     *
     * Reenvio seguro: se o pasto já está com exatamente essa descrição,
     * gravada pelo mesmo usuário a partir dessa mesma data/hora, não gera
     * outro número de lote — responde sucesso com "ignorado": true.
     */
    public function gravarDescricaoLote($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        $pasto = (int) ($dados['pasto'] ?? 0);
        $descricaoLote = (string) ($dados['descricao_lote'] ?? '');
        $lotesRecebidos = is_array($dados['lotes'] ?? null) ? $dados['lotes'] : [];

        if ($bd === '' || $pasto <= 0) {
            return ["success" => false, "message" => "Pasto não informado."];
        }
        if (trim($descricaoLote) === '') {
            return ["success" => false, "message" => "A Descrição do Lote não pode ser vazia."];
        }

        $agora = $this->dataHoraDaAcao($dados);
        $usuario = $this->usuarioDaAcao($dados);
        $ano = (int) substr($agora, 0, 4);

        $lotes = [];
        foreach (array_slice($lotesRecebidos, 0, 6) as $l) {
            if ((string) $l !== '') {
                $lotes[] = (string) $l;
            }
        }
        $lotes = array_pad($lotes, 6, '');

        $pastoDao = new PastoDao($bd);
        $con = $pastoDao->getConexao();
        if (!$con) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }
        $loteDao = new LoteAnimaisDao($bd, $con);

        mysqli_begin_transaction($con);

        $p = $pastoDao->buscarPastoParaMovimentacao($pasto);
        if (!$p) {
            return $this->falhar($con, 'O pasto não foi encontrado.');
        }

        $jaAplicado = (string) $p['tbl_pasto_descricao_lote'] === $descricaoLote &&
            (string) $p['tbl_pasto_alterado_por'] === $usuario &&
            (string) $p['tbl_pasto_alterado_em'] >= $agora;
        for ($i = 0; $jaAplicado && $i < 6; $i++) {
            $jaAplicado = (string) $p['tbl_pasto_descricao_lote_' . ($i + 1)] === $lotes[$i];
        }
        if ($jaAplicado) {
            mysqli_rollback($con);
            mysqli_close($con);
            return ["success" => true, "ignorado" => true, "message" => "Descrição do Lote já gravada."];
        }

        $idLote = $loteDao->proximoIdLote($p['tbl_pasto_codigo_local'], $ano);

        $campos = [
            'tbl_pasto_id_lote'        => (string) $idLote,
            'tbl_pasto_ano_lote'       => (string) $ano,
            'tbl_pasto_descricao_lote' => $descricaoLote,
        ];
        for ($i = 1; $i <= 6; $i++) {
            $campos["tbl_pasto_descricao_lote_{$i}"] = $lotes[$i - 1];
        }
        $r = $pastoDao->atualizarCamposPasto($pasto, $campos, $usuario, $agora);
        if ($r['error']) {
            return $this->falhar($con, 'Ocorreu um erro ao atualizar a Descrição do Lote: ' . $r['message']);
        }

        mysqli_commit($con);
        mysqli_close($con);

        return [
            "success" => true,
            "ignorado" => false,
            "message" => "Atualização da Descrição do Lote com sucesso",
            "id_lote" => str_pad($idLote, 4, "0", STR_PAD_LEFT),
            "ano_lote" => (string) $ano,
        ];
    }

    // ---------------------------------------------------------------------
    // Auxiliares
    // ---------------------------------------------------------------------

    private function falhar($con, $mensagem){
        mysqli_rollback($con);
        mysqli_close($con);
        return ["success" => false, "message" => $mensagem];
    }

    private function camposLoteVazio(){
        $campos = [
            'tbl_pasto_descricao_lote' => null,
            'tbl_pasto_id_lote'        => null,
            'tbl_pasto_ano_lote'       => null,
        ];
        for ($i = 1; $i <= 6; $i++) {
            $campos["tbl_pasto_descricao_lote_{$i}"] = null;
        }
        return $campos;
    }

    /** data_hora enviada pelo app (Y-m-d H:i:s); sem ela, agora. */
    private function dataHoraDaAcao($dados){
        $d = DateTime::createFromFormat('Y-m-d H:i:s', (string) ($dados['data_hora'] ?? ''));
        return $d ? $d->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
    }

    /** alterado_por é varchar(30). */
    private function usuarioDaAcao($dados){
        return mb_substr(trim((string) ($dados['usuario'] ?? '')), 0, 30);
    }

    /** Pasto que nunca teve um ciclo anterior registrado: usa "agora". */
    private function dataOuAgora($valor, $agora){
        if ($valor === null || $valor === '' || $valor === '0000-00-00' || $valor === '0000-00-00 00:00:00') {
            return $agora;
        }
        return $valor;
    }

    /** Mesma conta do web: horas entre "agora" e a data (nula = "agora",
     *  como o new DateTime(null) do web). */
    private function horasDesde($agora, $data){
        $diff = (new DateTime($agora))->diff(new DateTime($data ?? $agora));
        return $diff->h + ($diff->days * 24);
    }
}
