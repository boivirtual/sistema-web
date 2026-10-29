<?php
/**
 * Resumo Diário da Fazenda (aplicativo) — regras.
 */
class ResumoDiarioService{

    /**
     * Confere se o dia anterior a $data (DateTime) ficou sem resumo da
     * fazenda: pendente quando não tem registro no dia anterior E a fazenda
     * já tem algum resumo mais antigo que ele (sem nenhum resumo anterior —
     * ex.: tabela vazia — não cobra nada).
     *
     * Devolve { success, pendente, dia } (dia = o dia anterior conferido).
     */
    public function diaAnteriorPendente($bd, $fazenda, DateTime $data){
        $dao = new ResumoDiarioDao($bd);
        if (!$dao->conectado()) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
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
            $antes = $dao->contarAntesDe($fazenda, $anterior);
            if ($antes === null) {
                return ["success" => false, "message" => "Erro ao consultar os resumos."];
            }
            $pendente = $antes > 0;
        }

        return [
            "success"  => true,
            "pendente" => $pendente,
            "dia"      => $anterior,
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
