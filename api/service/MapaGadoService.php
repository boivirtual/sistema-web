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

        // Botão Morte: motivos, animais em estação de monta (aviso) e o tipo
        // de controle de estoque da empresa ('I' por animal, 'L' por lote).
        $morteDao = new MorteDao($bd, $con);
        $motivosMorte = $morteDao->listarMotivos();
        $emEstacaoMonta = $morteDao->listarAnimaisEmEstacaoMonta($idsFazendas);
        $controleEstoque = $morteDao->controleEstoque($bd);

        mysqli_close($con);

        return [
            "success"         => true,
            "controle_estoque" => $controleEstoque,
            "motivos_morte"   => $motivosMorte,
            "animais_estacao_monta" => $emEstacaoMonta,
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

        $descricaoDestino = (string) $pDestino['tbl_pasto_descricao_lote'];
        $registrosDestino = $animalPastoDao->contarRegistrosNoPasto($destino);

        $erro = $this->datasDaOrigemVazia($pastoDao, $origem, $pOrigem, $usuario, $agora);
        if ($erro !== null) {
            return $this->falhar($con, $erro);
        }
        if ($registrosDestino == 0) {
            $erro = $this->datasDoDestinoQueRecebe($pastoDao, $destino, $pDestino, $usuario, $agora);
            if ($erro !== null) {
                return $this->falhar($con, $erro);
            }
        }
        $erro = $this->premissasDaDescricaoDoLote($pastoDao, $origem, $destino, $pOrigem, $descricaoDestino, $usuario, $agora);
        if ($erro !== null) {
            return $this->falhar($con, $erro);
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
     * Mesma regra de gravar_alterar_descricao_lote.php: as descrições
     * preenchidas (até 6) vão para as primeiras posições e o pasto recebe
     * um novo número de lote da fazenda/ano (novo_id = 'S').
     *
     * Com "manter_numero" (clique no campo Descrição do Lote da tela do
     * pasto — novo_id = 'N' no web), o pasto que já tem número de lote
     * continua com ele; só gera número novo se ainda não tiver.
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

        $manterNumero = !empty($dados['manter_numero']) &&
            (int) $p['tbl_pasto_id_lote'] > 0;
        if ($manterNumero) {
            $idLote = (int) $p['tbl_pasto_id_lote'];
            $ano = (int) $p['tbl_pasto_ano_lote'];
        } else {
            $idLote = $loteDao->proximoIdLote($p['tbl_pasto_codigo_local'], $ano);
        }

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
    // Transferir animais de UMA categoria (botão Confirma da tela do pasto)
    // ---------------------------------------------------------------------

    /**
     * Mesma regra de remover_animais_categoria.php:
     *   - passa para o destino a quantidade pedida de animais ativos da
     *     categoria (faixa de idade) e do sexo informados — sexo vazio =
     *     bezerros, os dois sexos;
     *   - destino que não tinha nenhum registro de animal: datas com/sem
     *     animais (janela de 24h);
     *   - se a origem ficou sem animais: datas da origem, Premissas 1 e 6
     *     da Descrição do Lote e a nutrição do dia vão para o destino
     *     (iguais às de "mover todos").
     *
     * A idade é calculada na data da ação (data_hora), para dar o mesmo
     * resultado que o usuário viu no aparelho mesmo se o envio atrasar.
     *
     * Reenvio seguro: os animais movidos ficam com alterado_em = data_hora
     * e alterado_por = usuário da ação; se o destino já tem animal com essa
     * marca, a ação já foi aplicada — responde sucesso com "ignorado".
     */
    public function transferirCategoria($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        $origem = (int) ($dados['origem'] ?? 0);
        $destino = (int) ($dados['destino'] ?? 0);
        $categoria = (int) ($dados['categoria'] ?? 0);
        $sexo = strtoupper(trim((string) ($dados['sexo'] ?? '')));
        $quantidade = (int) ($dados['quantidade'] ?? 0);

        if ($bd === '' || $origem <= 0 || $destino <= 0) {
            return ["success" => false, "message" => "Não foi possível identificar o pasto de origem ou de destino."];
        }
        if ($origem === $destino) {
            return ["success" => false, "message" => "O pasto de origem e o de destino são o mesmo."];
        }
        if ($categoria <= 0) {
            return ["success" => false, "message" => "Selecione a Qual Categoria."];
        }
        if ($sexo !== 'M' && $sexo !== 'F') {
            $sexo = '';
        }
        if ($quantidade <= 0) {
            return ["success" => false, "message" => "Informe a Quantidade para transferir."];
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
        $categoriaDao = new CategoriaIdadeDao($bd, $con);

        mysqli_begin_transaction($con);

        $pOrigem = $pastoDao->buscarPastoParaMovimentacao($origem);
        if (!$pOrigem) {
            return $this->falhar($con, 'O pasto de origem não foi encontrado.');
        }
        $pDestino = $pastoDao->buscarPastoParaMovimentacao($destino);
        if (!$pDestino) {
            return $this->falhar($con, 'O pasto de destino não foi encontrado.');
        }

        $descricaoOrigem = (string) $pOrigem['tbl_pasto_descricao_lote'];
        $descricaoDestino = (string) $pDestino['tbl_pasto_descricao_lote'];

        if ($animalPastoDao->contarMovidosNaAcao($destino, $usuario, $agora) > 0) {
            mysqli_rollback($con);
            mysqli_close($con);
            return [
                "success" => true,
                "ignorado" => true,
                "message" => "Transferência já gravada.",
                "descricao_lote_pasto_destino" => $descricaoDestino,
                "descricao_lote_pasto_origem" => $descricaoOrigem,
            ];
        }

        // Faixa de idade da categoria pedida.
        $faixa = null;
        foreach ($categoriaDao->getCategoria() as $c) {
            if ((int) $c->getId() === $categoria) {
                $faixa = [(int) $c->getIdadeDe(), (int) $c->getIdadeAte()];
                break;
            }
        }
        if ($faixa === null) {
            return $this->falhar($con, 'A categoria informada não foi encontrada.');
        }

        // Animais da categoria/sexo, na ordem do número do item.
        $dataAcao = new DateTime(substr($agora, 0, 10));
        $itens = [];
        foreach ($animalPastoDao->listarAtivosDoPasto($origem) as $animal) {
            if ($sexo !== '' && (string) $animal['tbl_animal_pasto_sexo'] !== $sexo) {
                continue;
            }
            $idade = (new DateTime((string) $animal['tbl_animal_pasto_nascimento']))->diff($dataAcao);
            $meses = ((int) $idade->format('%Y')) * 12 + (int) $idade->format('%m');
            if ($meses >= $faixa[0] && $meses <= $faixa[1]) {
                $itens[] = (int) $animal['tbl_animal_pasto_numero_item'];
            }
        }
        if (count($itens) < $quantidade) {
            return $this->falhar($con, 'A quantidade de animais para transferir da categoria é insuficiente.');
        }
        $itens = array_slice($itens, 0, $quantidade);

        $registrosDestino = $animalPastoDao->contarRegistrosNoPasto($destino);

        $r = $animalPastoDao->transferirItens($origem, $destino, $itens, $categoria, $usuario, $agora);
        if ($r['error']) {
            return $this->falhar($con, 'Ocorreu um erro ao atualizar os animais no pasto ' . $r['message']);
        }

        // Origem ficou vazia: mesmas regras de "mover todos".
        $origemFicouVazia = $animalPastoDao->contarAtivosNoPasto($origem) === 0;
        if ($origemFicouVazia) {
            $erro = $this->datasDaOrigemVazia($pastoDao, $origem, $pOrigem, $usuario, $agora);
            if ($erro !== null) {
                return $this->falhar($con, $erro);
            }
        }
        if ($registrosDestino == 0) {
            $erro = $this->datasDoDestinoQueRecebe($pastoDao, $destino, $pDestino, $usuario, $agora);
            if ($erro !== null) {
                return $this->falhar($con, $erro);
            }
        }
        if ($origemFicouVazia) {
            $erro = $this->premissasDaDescricaoDoLote($pastoDao, $origem, $destino, $pOrigem, $descricaoDestino, $usuario, $agora);
            if ($erro !== null) {
                return $this->falhar($con, $erro);
            }
            $descricaoOrigem = '';
            $nutricaoDao->transferirNutricaoDoDia($origem, $destino, substr($agora, 0, 10));
        }

        mysqli_commit($con);
        mysqli_close($con);

        return [
            "success" => true,
            "ignorado" => false,
            "message" => "Animais movidos com sucesso.",
            "descricao_lote_pasto_destino" => $descricaoDestino,
            "descricao_lote_pasto_origem" => $descricaoOrigem,
        ];
    }

    // ---------------------------------------------------------------------
    // Levar a Descrição do Lote do pasto origem para o pasto destino
    // ---------------------------------------------------------------------

    /**
     * Opção "Levar a Descrição do Lote" depois de transferir parte dos
     * animais para um pasto sem descrição (gravar_levar_descricao_lote_
     * pasto_destino + trocar_id_lote_pasto_origem no web):
     *   - o destino recebe a descrição, os lotes e o NÚMERO do lote da
     *     origem (se a origem não tiver número, gera um para o destino);
     *   - a origem continua com a descrição e recebe um número NOVO.
     *
     * Reenvio seguro: destino já com a descrição da origem, gravada pelo
     * mesmo usuário a partir dessa data/hora -> "ignorado".
     */
    public function levarDescricaoLote($dados){
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
        $ano = (int) substr($agora, 0, 4);

        $pastoDao = new PastoDao($bd);
        $con = $pastoDao->getConexao();
        if (!$con) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }
        $loteDao = new LoteAnimaisDao($bd, $con);

        mysqli_begin_transaction($con);

        $pOrigem = $pastoDao->buscarPastoParaMovimentacao($origem);
        if (!$pOrigem) {
            return $this->falhar($con, 'O pasto de origem não foi encontrado.');
        }
        $pDestino = $pastoDao->buscarPastoParaMovimentacao($destino);
        if (!$pDestino) {
            return $this->falhar($con, 'O pasto de destino não foi encontrado.');
        }

        $descricaoOrigem = (string) $pOrigem['tbl_pasto_descricao_lote'];
        if (trim($descricaoOrigem) === '') {
            return $this->falhar($con, 'O pasto de origem não tem Descrição do Lote para levar.');
        }

        $jaAplicado = (string) $pDestino['tbl_pasto_descricao_lote'] === $descricaoOrigem &&
            (string) $pDestino['tbl_pasto_alterado_por'] === $usuario &&
            (string) $pDestino['tbl_pasto_alterado_em'] >= $agora;
        if ($jaAplicado) {
            mysqli_rollback($con);
            mysqli_close($con);
            return [
                "success" => true,
                "ignorado" => true,
                "message" => "Descrição do Lote já levada para o pasto destino.",
                "id_lote_origem" => str_pad((string) (int) $pOrigem['tbl_pasto_id_lote'], 4, "0", STR_PAD_LEFT),
                "ano_lote_origem" => (string) $pOrigem['tbl_pasto_ano_lote'],
                "id_lote_destino" => str_pad((string) (int) $pDestino['tbl_pasto_id_lote'], 4, "0", STR_PAD_LEFT),
                "ano_lote_destino" => (string) $pDestino['tbl_pasto_ano_lote'],
            ];
        }

        // Destino: descrição + número do lote da origem.
        $idDestino = (int) $pOrigem['tbl_pasto_id_lote'];
        $anoDestino = (int) $pOrigem['tbl_pasto_ano_lote'];
        if ($idDestino <= 0) {
            $idDestino = $loteDao->proximoIdLote($pDestino['tbl_pasto_codigo_local'], $ano);
            $anoDestino = $ano;
        }
        $campos = [
            'tbl_pasto_id_lote'        => (string) $idDestino,
            'tbl_pasto_ano_lote'       => (string) $anoDestino,
            'tbl_pasto_descricao_lote' => $descricaoOrigem,
        ];
        for ($i = 1; $i <= 6; $i++) {
            $campos["tbl_pasto_descricao_lote_{$i}"] = (string) $pOrigem["tbl_pasto_descricao_lote_{$i}"];
        }
        $r = $pastoDao->atualizarCamposPasto($destino, $campos, $usuario, $agora);
        if ($r['error']) {
            return $this->falhar($con, 'Ocorreu um erro ao atualizar a Descrição do Lote: ' . $r['message']);
        }

        // Origem: mesma descrição, número novo.
        $idOrigem = $loteDao->proximoIdLote($pOrigem['tbl_pasto_codigo_local'], $ano);
        $r = $pastoDao->atualizarCamposPasto($origem, [
            'tbl_pasto_id_lote'  => (string) $idOrigem,
            'tbl_pasto_ano_lote' => (string) $ano,
        ], $usuario, $agora);
        if ($r['error']) {
            return $this->falhar($con, 'Ocorreu um erro ao atualizar a Descrição do Lote: ' . $r['message']);
        }

        mysqli_commit($con);
        mysqli_close($con);

        return [
            "success" => true,
            "ignorado" => false,
            "message" => "Atualização da Descrição do Lote com sucesso",
            "id_lote_origem" => str_pad((string) $idOrigem, 4, "0", STR_PAD_LEFT),
            "ano_lote_origem" => (string) $ano,
            "id_lote_destino" => str_pad((string) $idDestino, 4, "0", STR_PAD_LEFT),
            "ano_lote_destino" => (string) $anoDestino,
        ];
    }

    // ---------------------------------------------------------------------
    // Morte de um animal (botão Morte da tela do pasto)
    // ---------------------------------------------------------------------

    /**
     * Mesma regra de gravar_morte.php (sistema web) para o controle de
     * estoque POR ANIMAL ('I'):
     *   - baixa do pasto um registro com o sexo e a data de nascimento do
     *     animal; se o pasto não tem esse nascimento, troca a data com um
     *     registro de outro pasto da fazenda (o estoque do pasto não é
     *     ligado ao cadastro do animal);
     *   - morte com data de um mês já fechado: ajusta o fechamento mensal;
     *   - pasto ficou sem animais: limpa a Descrição do Lote e acerta as
     *     datas com/sem animais;
     *   - grava a movimentação tipo 888 (Morte) com o item;
     *   - baixa o animal (inativo, situação 'M') e registra a saída no
     *     histórico de estoque;
     *   - fêmea em cobertura: tira da cobertura ou marca o item, como o web.
     *
     * Tudo numa transação. Reenvio seguro: a movimentação fica com
     * incluido_em = data_hora da ação e incluido_por = usuário; se já
     * existe para esse animal, responde sucesso com "ignorado": true.
     *
     * O controle por lote ('L') ainda não é atendido pelo aplicativo.
     */
    public function gravarMorte($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        $local = (int) ($dados['fazenda'] ?? 0);
        $pasto = (int) ($dados['pasto'] ?? 0);
        $codigoId = (int) ($dados['animal'] ?? 0);
        $motivo = (int) ($dados['motivo'] ?? 0);
        $observacao = trim((string) ($dados['observacao'] ?? ''));
        $dataMorteObj = DateTime::createFromFormat('!Y-m-d', (string) ($dados['data_morte'] ?? ''));

        if ($bd === '' || $local <= 0) {
            return ["success" => false, "message" => "Fazenda não informada."];
        }
        if ($pasto <= 0) {
            return ["success" => false, "message" => "Informe o Pasto!"];
        }
        if ($codigoId <= 0) {
            return ["success" => false, "message" => "FALTA VALIDAR O CÓDIGO DO ANIMAL."];
        }
        if ($motivo <= 0) {
            return ["success" => false, "message" => "Informe o Motivo da Morte!"];
        }
        if (!$dataMorteObj) {
            return ["success" => false, "message" => "A Data precisa ser informada!"];
        }

        $agora = $this->dataHoraDaAcao($dados);
        $usuario = $this->usuarioDaAcao($dados);
        $hoje = substr($agora, 0, 10);
        $dataMorte = $dataMorteObj->format('Y-m-d');
        if ($dataMorte > $hoje) {
            return ["success" => false, "message" => "A Data não pode ser maior que a data atual!"];
        }

        $pastoDao = new PastoDao($bd);
        $con = $pastoDao->getConexao();
        if (!$con) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }
        $morteDao = new MorteDao($bd, $con);
        $categoriaDao = new CategoriaIdadeDao($bd, $con);

        mysqli_begin_transaction($con);

        if ($morteDao->existeMovimentacaoDaAcao($local, $codigoId, $usuario, $agora)) {
            mysqli_rollback($con);
            mysqli_close($con);
            return ["success" => true, "ignorado" => true, "message" => "Morte já gravada."];
        }

        $animal = $morteDao->buscarAnimal($codigoId);
        if (!$animal || (int) $animal['tbl_animal_lixeira'] !== 0) {
            return $this->falhar($con, 'Animal não cadastrado com esse código.');
        }
        if ((string) $animal['tbl_animal_ativo'] !== 'S') {
            return $this->falhar($con, 'O animal já está baixado no sistema.');
        }
        if ((int) $animal['tbl_animal_codigo_fazenda'] !== $local) {
            return $this->falhar($con, 'Animal não consta no local ou Id não cadastrado.');
        }

        $nascimento = (string) $animal['tbl_animal_data_nascimento'];
        if ($dataMorte < $nascimento) {
            return $this->falhar($con, 'A Data da Morte não pode ser menor que a Data do Nascimento.');
        }

        $descricaoMotivo = $morteDao->descricaoMotivo($motivo);
        if ($descricaoMotivo === null) {
            return $this->falhar($con, 'Informe o Motivo da Morte!');
        }

        $sexo = (string) $animal['tbl_animal_sexo'] === 'M' ? 'M' : 'F';

        // Peso do animal (para o fechamento mensal): último, desmama ou primeiro.
        $peso = 0;
        foreach (['tbl_animal_ultimo_peso', 'tbl_animal_peso_desmama', 'tbl_animal_primeiro_peso'] as $campo) {
            if ($animal[$campo] != 0 && $animal[$campo] != '') {
                $peso = $animal[$campo];
                break;
            }
        }

        // Categoria (faixa de idade) do animal na data da ação.
        $idade = (new DateTime($nascimento))->diff(new DateTime($hoje));
        $meses = ((int) $idade->format('%Y')) * 12 + (int) $idade->format('%m');
        $categoria = 0;
        foreach ($categoriaDao->getCategoria() as $c) {
            if ($meses >= (int) $c->getIdadeDe() && $meses <= (int) $c->getIdadeAte()) {
                $categoria = (int) $c->getId();
            }
        }
        $descricoesCategoria = [
            1 => '00 a 07 meses',
            2 => '08 a 12 meses',
            3 => '13 a 24 meses',
            4 => '25 a 36 meses',
            5 => '> 36 meses',
        ];
        $descCategoria = $descricoesCategoria[$categoria] ?? '';

        // ANIMAL NO PASTO — o registro com o nascimento do animal; se o
        // pasto não tem, troca a data com um registro de outro pasto.
        $registro = $morteDao->buscarNoPastoPorNascimento($local, $pasto, $sexo, $nascimento);
        if (!$registro) {
            $atual = $morteDao->buscarNoPastoPorCategoria($local, $pasto, $sexo, $categoria);
            if (!$atual) {
                return $this->falhar($con, 'Não existe animais com o sexo ' . $sexo . ', categoria ' . $descCategoria . ' no pasto.');
            }
            $trocar = $morteDao->buscarNaFazendaPorNascimento($local, $sexo, $nascimento);
            if (!$trocar) {
                return $this->falhar($con, 'Não existe animais com o sexo ' . $sexo . ', categoria ' . $descCategoria . ', nascimento ' . $nascimento . ' em outros pastos.');
            }

            $r = $morteDao->trocarNascimentoNoPasto($local, $pasto, $atual['tbl_animal_pasto_numero_item'], $trocar['tbl_animal_pasto_nascimento']);
            if ($r['error']) {
                return $this->falhar($con, 'Ocorreu um erro ao ajustar o nascimento no pasto atual ' . $r['message']);
            }
            $r = $morteDao->trocarNascimentoNoPasto($local, $trocar['tbl_animal_pasto_id'], $trocar['tbl_animal_pasto_numero_item'], $atual['tbl_animal_pasto_nascimento']);
            if ($r['error']) {
                return $this->falhar($con, 'Ocorreu um erro ao ajustar o nascimento no pasto trocar ' . $r['message']);
            }

            $registro = $morteDao->buscarNoPastoPorNascimento($local, $pasto, $sexo, $nascimento);
            if (!$registro) {
                return $this->falhar($con, 'Erro na alteração do registro no pasto.');
            }
        }

        $r = $morteDao->excluirDoPasto($local, $registro['tbl_animal_pasto_numero_item']);
        if ($r['error']) {
            return $this->falhar($con, 'Erro na alteração do registro no pasto.' . $r['message']);
        }

        // FECHAMENTO MENSAL — só quando a morte é de outro mês.
        if (substr($hoje, 0, 7) !== substr($dataMorte, 0, 7)) {
            $fechamento = new DateTime($dataMorte);
            $fechamento->modify('last day of this month');
            $dataFechamento = $fechamento->format('Y-m-d');

            $r = $morteDao->baixarDoFechamentoMensal($local, $dataFechamento, $categoria, $sexo, $peso);
            if ($r['error']) {
                return $this->falhar($con, 'Ocorreu um erro na alteração do fechamento mensal!' . $r['message']);
            }
            $r = $morteDao->somarMorteNoFechamentoEntSai($local, $dataFechamento, $peso);
            if ($r['error']) {
                return $this->falhar($con, 'Ocorreu um erro na alteração do fechamento mensal Ent/Sai' . $r['message']);
            }
        }

        // PASTO FICOU VAZIO — limpa a Descrição do Lote e acerta as datas.
        if ($morteDao->contarRegistrosNoPasto($local, $pasto) === 0) {
            $p = $pastoDao->buscarPastoParaMovimentacao($pasto);
            if ($p) {
                $r = $pastoDao->atualizarCamposPasto($pasto, $this->camposLoteVazio(), $usuario, $agora);
                if ($r['error']) {
                    return $this->falhar($con, 'Ocorreu um erro ao atualizar a descrição do lote no pasto origem' . $r['message']);
                }
                $erro = $this->datasDaOrigemVazia($pastoDao, $pasto, $p, $usuario, $agora);
                if ($erro !== null) {
                    return $this->falhar($con, $erro);
                }
            }
        }

        // MOVIMENTAÇÃO DE MORTE (tipo 888) + item
        $r = $morteDao->incluirMovimentacao($local, $dataMorte, $usuario, $agora);
        if ($r['error']) {
            return $this->falhar($con, 'Ocorreu um erro ao registrar a movimentação' . $r['message']);
        }
        $numeroMovimentacao = $r['id'];

        $alfa = (string) $animal['tbl_animal_codigo_alfa'];
        $numerico = (string) $animal['tbl_animal_codigo_numerico'];
        $r = $morteDao->incluirItemMovimentacao($numeroMovimentacao, $dataMorte, [
            'codigo_id'     => $codigoId,
            'codigo_animal' => $alfa !== '' ? $alfa . '-' . $numerico : $numerico,
            'sexo'          => $sexo,
            'nascimento'    => (new DateTime($nascimento))->format('d/m/Y'),
            'raca'          => (string) $animal['desc_raca'],
            'pelagem'       => (string) $animal['desc_pelagem'],
            'mae'           => (string) $animal['mae_alfa'] . (string) $animal['mae_numerico'],
            'observacao'    => $observacao,
            'motivo'        => $motivo,
            'pasto'         => $pasto,
            'categoria'     => $categoria,
        ]);
        if ($r['error']) {
            return $this->falhar($con, 'Ocorreu um erro na gravação dos itens.' . $r['message']);
        }

        // ANIMAL — baixa e histórico de estoque
        $r = $morteDao->baixarAnimalPorMorte(
            $codigoId,
            $dataMorte,
            $usuario,
            'Motivo da morte: ' . $descricaoMotivo . '. Obs: ' . $observacao,
            $animal['tbl_animal_codigo_origem'],
            $animal['tbl_animal_codigo_fazenda']
        );
        if ($r['error']) {
            return $this->falhar($con, 'Ocorreu um erro na atualização do animal.' . $r['message']);
        }
        $r = $morteDao->incluirSaidaEstoque($codigoId, $dataMorte, $nascimento, $local, $numeroMovimentacao, $pasto, $categoria, $sexo);
        if ($r['error']) {
            return $this->falhar($con, 'Erro na gravacao histórico saída morte.' . $r['message']);
        }

        // COBERTURA — fêmea que estava em cobertura
        $cobertura = $morteDao->buscarUltimaCobertura($codigoId);
        if ($cobertura) {
            $coberturaId = (int) $cobertura['tbl_ite_cobertura_numero_id'];
            $numeroItem = (int) $cobertura['tbl_ite_cobertura_numero_item'];

            if ($cobertura['tbl_cobertura_protocoloiatf'] == 0 || (string) $cobertura['tbl_ite_cobertura_dia_1'] === '') {
                $r = $morteDao->atualizarQtdAnimaisCobertura($coberturaId, ((int) $cobertura['tbl_cobertura_qtd_animais']) - 1, $usuario, $agora);
                if ($r['error']) {
                    return $this->falhar($con, 'Erro na atualização da qtd de animais no grupo ' . $cobertura['tbl_cobertura_codigo_grupo'] . ' da cobertura ' . $coberturaId . ' erro ' . $r['message']);
                }
                $r = $morteDao->excluirItemCobertura($coberturaId, $numeroItem);
                if ($r['error']) {
                    return $this->falhar($con, 'Erro na exclusão do registro item de cobertura ' . $r['message']);
                }
                $r = $morteDao->renumerarItensCobertura($coberturaId);
                if ($r['error']) {
                    return $this->falhar($con, 'Erro refazer os itens! ' . $r['message']);
                }
            } else if ((string) $cobertura['tbl_ite_cobertura_resultado_diagnostico'] === 'P') {
                if ((string) $cobertura['tbl_ite_cobertura_nascido'] === '') {
                    $r = $morteDao->marcarCoberturaFemeaMorta($coberturaId, $numeroItem, false, $usuario, $agora);
                    if ($r['error']) {
                        return $this->falhar($con, 'Ocorreu um erro na atualização do item de cobertura animal ' . $alfa . ' ' . $numerico . ' erro ' . $r['message']);
                    }
                }
            } else {
                $r = $morteDao->marcarCoberturaFemeaMorta($coberturaId, $numeroItem, true, $usuario, $agora);
                if ($r['error']) {
                    return $this->falhar($con, 'Ocorreu um erro na atualização do item de cobertura animal ' . $alfa . ' ' . $numerico . ' erro ' . $r['message']);
                }
            }
        }

        mysqli_commit($con);
        mysqli_close($con);

        return [
            "success" => true,
            "ignorado" => false,
            "message" => "Movimentação de morte processada com sucesso.",
            "movimentacao" => str_pad((string) $numeroMovimentacao, 9, "0", STR_PAD_LEFT),
        ];
    }

    // ---------------------------------------------------------------------
    // Regras comuns de "a origem ficou sem animais" (mover todos e
    // transferir por categoria) — devolvem null ou a mensagem de erro.
    // ---------------------------------------------------------------------

    /** Datas com/sem animais do pasto de origem que ficou vazio. */
    private function datasDaOrigemVazia($pastoDao, $origem, $pOrigem, $usuario, $agora){
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
        return $r['error'] ? $erro . $r['message'] : null;
    }

    /** Datas do pasto de destino que não tinha nenhum registro de animal. */
    private function datasDoDestinoQueRecebe($pastoDao, $destino, $pDestino, $usuario, $agora){
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
        return $r['error'] ? 'Ocorreu um erro ao atualizar as datas COM ' . $r['message'] : null;
    }

    /**
     * Premissa 1: destino SEM descrição do lote e origem COM -> a descrição
     * (com o número do lote) vai para o destino. Premissas 1 e 6: a origem
     * sempre fica sem descrição. $descricaoDestino sai atualizada.
     */
    private function premissasDaDescricaoDoLote($pastoDao, $origem, $destino, $pOrigem, &$descricaoDestino, $usuario, $agora){
        $descricaoOrigem = (string) $pOrigem['tbl_pasto_descricao_lote'];
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
                return 'Ocorreu um erro ao atualizar a Descrição do Lote do Pasto Destino ' . $r['message'];
            }
            $descricaoDestino = $descricaoOrigem;
        }

        $r = $pastoDao->atualizarCamposPasto($origem, $this->camposLoteVazio(), $usuario, $agora);
        return $r['error']
            ? 'Ocorreu um erro ao atualizar a Descrição do Lote do Pasto Origem ' . $r['message']
            : null;
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
