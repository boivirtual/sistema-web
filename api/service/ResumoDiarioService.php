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
     * (NascimentoDao/MorteDao::registrosDoDia).
     *
     * Devolve { success, chuva_mm, mudou_pasto, nutricao, nascimentos,
     *           abortos, mortes }
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
        if ($chuva === false || $mudouPasto === null || $nutricao === null) {
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
            "nutricao"    => $nutricao,
            "nascimentos" => $nascimentos,
            "abortos"     => $abortos,
            "mortes"      => $mortes,
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
