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
}
