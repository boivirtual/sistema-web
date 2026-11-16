<?php
    // Evento da agenda gerado pelo Protocolo IATF (D0, D7, D9, D11...): tem a cobertura
    // gravada em tbl_agenda_codigo_cobertura e o dia do protocolo no título ("-D9-").
    // Esses eventos não podem ser editados nem excluídos pela agenda.
    function agenda_evento_protocolo($codigo_cobertura, $titulo) {
        return ((int) $codigo_cobertura > 0 && preg_match('/-D\d+-/', (string) $titulo) === 1);
    }
?>
