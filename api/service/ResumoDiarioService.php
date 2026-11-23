<?php
/**
 * Resumo Diário da Fazenda (aplicativo) — regras.
 */
class ResumoDiarioService{

    /**
     * Confere se o dia anterior a $data (DateTime) ficou sem resumo da
     * fazenda: pendente quando a fazenda não tem registro no dia anterior E
     * a empresa já tem algum resumo/folga (de qualquer fazenda) até esse dia
     * — assim toda fazenda é cobrada depois que a empresa começou a fazer o
     * resumo (tabela vazia não cobra nada).
     *
     * Também informa se a própria $data já tem registro da fazenda
     * (tipo_no_dia: 'R' = Resumo, 'F' = Folga, null = livre) — o app não
     * deixa escolher no calendário um dia já registrado.
     *
     * Devolve { success, pendente, dia, tipo_no_dia } (dia = o dia anterior
     * conferido).
     */
    public function diaAnteriorPendente($bd, $fazenda, DateTime $data){
        $dao = new ResumoDiarioDao($bd);
        if (!$dao->conectado()) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }

        $tipoNoDia = $dao->tipoNoDia($fazenda, $data->format('Y-m-d'));
        if ($tipoNoDia === false) {
            return ["success" => false, "message" => "Erro ao consultar os resumos."];
        }

        // tbl_resumo_diario_data = dia a que o resumo se refere (sem hora);
        // o momento da gravação fica em tbl_resumo_diario_incluido_em.
        $anterior = (clone $data)->modify('-1 day')->format('Y-m-d');

        $noDia = $dao->contarNoDia($fazenda, $anterior);
        if ($noDia === null) {
            return ["success" => false, "message" => "Erro ao consultar os resumos."];
        }

        $pendente = false;
        if ($noDia === 0) {
            $antes = $dao->contarEmpresaAte($anterior);
            if ($antes === null) {
                return ["success" => false, "message" => "Erro ao consultar os resumos."];
            }
            $pendente = $antes > 0;
        }

        return [
            "success"  => true,
            "pendente"    => $pendente,
            "dia"         => $anterior,
            "tipo_no_dia" => $tipoNoDia,
        ];
    }

    /**
     * Seção 1 do Resumo: atividades já registradas no sistema para a fazenda
     * no dia $dia (DateTime): chuva_mm = volume registrado, null se não
     * choveu (sem registro ou volume zero); mudou_pasto = houve transferência
     * de animais entre pastos no dia (data de alteração do animal ou datas
     * com/sem animais do pasto — ver ResumoDiarioDao::mudouPastoNoDia);
     * nutricao = teve nutrição no dia; nascimentos, abortos (Aborto, Absorção
     * e Natimorto) e mortes = quantos registros foram lançados no dia — as
     * mesmas consultas dos "Registros de Hoje" do Nascimento e da Morte
     * (NascimentoDao/MorteDao::registrosDoDia); pesados = quantos animais a
     * fazenda pesou no dia (Seção 4, Atividades do Curral); nutricoes = o que
     * foi distribuído no dia, por pasto e produto (lista do card da Nutrição);
     * pastos_alterados = nomes dos pastos com troca de animais no dia (lista
     * do card "Trocamos animais de pasto").
     *
     * Devolve { success, chuva_mm, mudou_pasto, pastos_alterados, nutricao,
     *           nutricoes, nascimentos, abortos, mortes, pesados }
     */
    public function atividadesDoDia($bd, $fazenda, DateTime $dia){
        $dao = new ResumoDiarioDao($bd);
        if (!$dao->conectado()) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }
        $d = $dia->format('Y-m-d');

        $chuva = $dao->chuvaNoDia($fazenda, $d);
        $mudouPasto = $dao->mudouPastoNoDia($fazenda, $d);
        $nutricao = $dao->nutricaoNoDia($fazenda, $d);
        $nutricoes = $dao->nutricoesDoDia($fazenda, $d);
        $pastosAlterados = $dao->pastosAlteradosNoDia($fazenda, $d);
        $pesados = $dao->animaisPesadosNoDia($fazenda, $d);
        if ($chuva === false || $mudouPasto === null || $nutricao === null
            || $pesados === null || $nutricoes === null
            || $pastosAlterados === null) {
            return ["success" => false, "message" => "Erro ao consultar as atividades do dia."];
        }

        // Nascimento x Aborto/Absorção/Natimorto: mesma separação do
        // MapaGadoNascimentoService::registrosHoje()
        $nascimentos = 0;
        $abortos = 0;
        $con = $dao->getConexao();
        foreach ((new NascimentoDao($bd, $con))->registrosDoDia((int) $fazenda, $d) as $r) {
            $natimorto = (int) $r['animal'] === 999999999 || $r['numerico'] === null;
            if ($r['tipo'] === 'A' || $r['tipo'] === 'B' || $natimorto) {
                $abortos++;
            } else {
                $nascimentos++;
            }
        }
        $mortes = count((new MorteDao($bd, $con))->registrosDoDia((int) $fazenda, $d));

        return [
            "success"     => true,
            "chuva_mm"    => $chuva !== null && $chuva > 0 ? $chuva : null,
            "mudou_pasto" => $mudouPasto,
            "pastos_alterados" => $pastosAlterados,
            "nutricao"    => $nutricao,
            "nutricoes"   => $nutricoes,
            "nascimentos" => $nascimentos,
            "abortos"     => $abortos,
            "mortes"      => $mortes,
            "pesados"     => $pesados,
        ];
    }

    /**
     * Seção 3 do Resumo (Conferência de Rebanho): animais ativos da fazenda,
     * no total e por pasto — "Animais Esperados" do Gado Todo (total) e da
     * Parte do Gado (soma dos pastos conferidos). O total é a soma dos
     * pastos, igual aos ativos do cadastro de animais da fazenda. Também
     * quantas vacas prenhas a fazenda tem (a pergunta "Conferiu as Vacas
     * Prenhas?" só aparece com alguma).
     *
     * Devolve { success, total, prenhas, pastos: { "id do pasto": quantidade } }
     */
    public function animaisDaFazenda($bd, $fazenda){
        $dao = new ResumoDiarioDao($bd);
        if (!$dao->conectado()) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }
        $porPasto = $dao->animaisPorPasto($fazenda);
        $prenhas = $dao->vacasPrenhas($fazenda);
        if ($porPasto === null || $prenhas === null) {
            return ["success" => false, "message" => "Erro ao consultar os animais da fazenda."];
        }
        return [
            "success" => true,
            "total"   => array_sum($porPasto),
            "prenhas" => $prenhas,
            // objeto JSON mesmo sem pastos (array vazio viraria [])
            "pastos"  => (object) $porPasto,
        ];
    }

    /**
     * Finalizar da Seção 9: grava o Resumo (tipo 'R') da fazenda no dia
     * $dia (DateTime) com os dados das 9 seções ($resumo, ver
     * ResumoDiarioDao::CAMPOS_RESUMO). Listas e seleções múltiplas chegam
     * como listas e são guardadas em JSON.
     *
     * Data futura não pode. Dia já com Resumo (ex.: o app reenviou com a
     * internet caindo): não grava de novo e responde sucesso com
     * "ja_registrado": true. Dia de Folga: recusa. Resumo do dia na lixeira:
     * reaproveita o mesmo registro.
     *
     * Devolve { success, message?, ja_registrado? }
     */
    public function gravarResumo($bd, $fazenda, $usuario, DateTime $dia, array $resumo){
        // horário de Brasília para o "hoje" e o incluido_em (igual à Folga)
        date_default_timezone_set('America/Sao_Paulo');
        if ($dia > new DateTime('today')) {
            return ["success" => false, "message" => "Não é possível gravar o resumo de uma data futura."];
        }

        $dao = new ResumoDiarioDao($bd);
        if (!$dao->conectado()) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }

        $data = $dia->format('Y-m-d');
        $existente = $dao->registroDoDia($fazenda, $data);
        if ($existente === false) {
            return ["success" => false, "message" => "Erro ao consultar os resumos."];
        }
        $idLixeira = null;
        if ($existente !== null) {
            if ($existente['lixeira'] === 0) {
                if ($existente['tipo'] === ResumoDiario::TIPO_FOLGA) {
                    return ["success" => false, "message" =>
                        "O dia " . $dia->format('d/m/Y') . " já está registrado como Folga desta fazenda."];
                }
                return ["success" => true, "ja_registrado" => true];
            }
            $idLixeira = $existente['id'];
        }

        $r = $dao->gravarResumo(
            $fazenda,
            $data,
            $usuario,
            date('Y-m-d H:i:s'),
            $this->camposDoResumo($resumo),
            $idLixeira
        );
        if ($r === 'erro') {
            return ["success" => false, "message" => "Não foi possível gravar o Resumo Diário."];
        }
        return ["success" => true, "ja_registrado" => $r === 'duplicado'];
    }

    /**
     * Converte o que o app mandou em valores das colunas: S/N, números,
     * horas (HH:MM), textos e listas (JSON). O que não vier ou vier fora do
     * formato fica NULL.
     */
    private function camposDoResumo(array $r){
        $sn = function ($v) {
            if ($v === true || $v === 'S') return 'S';
            if ($v === false || $v === 'N') return 'N';
            return null;
        };
        $inteiro = function ($v) {
            return is_numeric($v) ? (int) $v : null;
        };
        $texto = function ($v, $max = 5000) {
            $v = trim((string) ($v ?? ''));
            return $v === '' ? null : mb_substr($v, 0, $max);
        };
        $lista = function ($v) {
            return (is_array($v) && count($v) > 0)
                ? json_encode($v, JSON_UNESCAPED_UNICODE)
                : null;
        };
        $hora = function ($v) {
            $v = (string) ($v ?? '');
            return preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $v) ? $v . ':00' : null;
        };
        $gado = (string) ($r['conferiu_gado'] ?? '');

        return [
            // Seção 1
            'chuva_mm'    => is_numeric($r['chuva_mm'] ?? null) ? (float) $r['chuva_mm'] : null,
            'mudou_pasto' => $sn($r['mudou_pasto'] ?? null),
            'nutricao'    => $sn($r['nutricao'] ?? null),
            'nascimentos' => $inteiro($r['nascimentos'] ?? null),
            'abortos'     => $inteiro($r['abortos'] ?? null),
            'mortes'      => $inteiro($r['mortes'] ?? null),
            'pesados'     => $inteiro($r['pesados'] ?? null),
            // Seção 2
            'confirmou_nao_registradas' => $sn($r['confirmou_nao_registradas'] ?? null),
            // Seção 3
            'conferiu_gado'       => in_array($gado, ['T', 'P', 'N'], true) ? $gado : null,
            'pastos_conferidos'   => $lista($r['pastos_conferidos'] ?? null),
            'animais_esperados'   => $inteiro($r['animais_esperados'] ?? null),
            'animais_encontrados' => $inteiro($r['animais_encontrados'] ?? null),
            'pastos_diferenca'    => $lista($r['pastos_diferenca'] ?? null),
            'vacas_prenhas'       => $inteiro($r['vacas_prenhas'] ?? null),
            'conferiu_prenhas'    => $sn($r['conferiu_prenhas'] ?? null),
            'obs_rebanho'         => $texto($r['obs_rebanho'] ?? null),
            // Seção 4
            'curral_atividades' => $lista($r['curral_atividades'] ?? null),
            'remedios'          => $lista($r['remedios'] ?? null),
            'curral_outras'     => $texto($r['curral_outras'] ?? null),
            // Seções 5 a 8
            'nenhuma_mistura'       => $sn($r['nenhuma_mistura'] ?? null),
            'misturas'              => $lista($r['misturas'] ?? null),
            'nenhum_servico'        => $sn($r['nenhum_servico'] ?? null),
            'servicos'              => $lista($r['servicos'] ?? null),
            'nenhuma_solicitacao'   => $sn($r['nenhuma_solicitacao'] ?? null),
            'pedidos'               => $lista($r['pedidos'] ?? null),
            'nenhuma_chegada_saida' => $sn($r['nenhuma_chegada_saida'] ?? null),
            'chegadas_saidas'       => $lista($r['chegadas_saidas'] ?? null),
            // Seção 9
            'trator_inicio'         => $hora($r['trator_inicio'] ?? null),
            'trator_termino'        => $hora($r['trator_termino'] ?? null),
            'trator_motivos'        => $lista($r['trator_motivos'] ?? null),
            'trator_outros_motivos' => $texto($r['trator_outros_motivos'] ?? null, 255),
            'bomba_inicio'          => $hora($r['bomba_inicio'] ?? null),
            'bomba_termino'         => $hora($r['bomba_termino'] ?? null),
            'equipe'                => $lista($r['equipe'] ?? null),
            'obs_gerais'            => $texto($r['obs_gerais'] ?? null),
        ];
    }

    /**
     * Seção 9 do Resumo ("Equipe do Dia"): funcionários (classe 05) ativos
     * da fazenda, em ordem de nome.
     *
     * Devolve { success, funcionarios: [{ id, nome }] }
     */
    public function funcionariosDaFazenda($bd, $fazenda){
        $dao = new ResumoDiarioDao($bd);
        if (!$dao->conectado()) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }
        $lista = $dao->funcionariosDaFazenda($fazenda);
        if ($lista === null) {
            return ["success" => false, "message" => "Erro ao consultar os funcionários da fazenda."];
        }
        return ["success" => true, "funcionarios" => $lista];
    }

    /**
     * "Registrar Folga" do modal do dia sem resumo: grava o dia $dia
     * (DateTime) da fazenda como Folga, com o usuário que registrou.
     * Data futura não pode. Se a fazenda já tem registro nesse dia (ex.:
     * o app reenviou com a internet caindo), não grava de novo e responde
     * sucesso com "ja_registrado": true.
     *
     * Devolve { success, message?, ja_registrado? }
     */
    public function registrarFolga($bd, $fazenda, $usuario, DateTime $dia){
        // horário de Brasília para o "hoje" e o incluido_em (igual ao
        // ChuvaService)
        date_default_timezone_set('America/Sao_Paulo');
        if ($dia > new DateTime('today')) {
            return ["success" => false, "message" => "Não é possível registrar folga em data futura."];
        }

        $dao = new ResumoDiarioDao($bd);
        if (!$dao->conectado()) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }

        $registro = (new ResumoDiario())
            ->setFazenda((int) $fazenda)
            // o campo guarda até 30 caracteres
            ->setUsuario(mb_substr($usuario, 0, 30))
            ->setData($dia->format('Y-m-d'))
            ->setTipo(ResumoDiario::TIPO_FOLGA)
            ->setIncluidoEm(date('Y-m-d H:i:s'));

        $r = $dao->incluir($registro);
        if ($r === 'erro') {
            return ["success" => false, "message" => "Não foi possível registrar a folga."];
        }
        return ["success" => true, "ja_registrado" => $r === 'duplicado'];
    }
}
