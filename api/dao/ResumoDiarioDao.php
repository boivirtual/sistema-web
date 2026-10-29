<?php
/**
 * Resumo Diário da Fazenda (aplicativo) — acesso à tbl_resumo_diario.
 * Um resumo por fazenda + data (índice único uk_resumo_diario_fazenda_data).
 * Prepared statements e campos explicitados (CLAUDE.md).
 */
class ResumoDiarioDao{

    private $con;

    public function __construct($banco){
        require __DIR__ . "/../../conecta_mysql_credenciais.inc";
        $this->con = @mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        if ($this->con) {
            mysqli_set_charset($this->con, "utf8");
        }
    }

    public function conectado(){
        return (bool) $this->con;
    }

    // Quantos resumos a fazenda tem exatamente no dia (AAAA-MM-DD).
    // null se a consulta falhar.
    public function contarNoDia($fazenda, $dia){
        return $this->contar(
            "SELECT COUNT(tbl_resumo_diario_id) FROM tbl_resumo_diario
              WHERE tbl_resumo_diario_fazenda = ? AND tbl_resumo_diario_data = ?",
            $fazenda,
            $dia
        );
    }

    // Quantos resumos a fazenda tem antes do dia (AAAA-MM-DD).
    // null se a consulta falhar.
    public function contarAntesDe($fazenda, $dia){
        return $this->contar(
            "SELECT COUNT(tbl_resumo_diario_id) FROM tbl_resumo_diario
              WHERE tbl_resumo_diario_fazenda = ? AND tbl_resumo_diario_data < ?",
            $fazenda,
            $dia
        );
    }

    private function contar($sql, $fazenda, $dia){
        $fazenda = (int) $fazenda;
        $st = mysqli_prepare($this->con, $sql);
        if (!$st) {
            return null;
        }
        mysqli_stmt_bind_param($st, 'is', $fazenda, $dia);
        mysqli_stmt_execute($st);
        mysqli_stmt_bind_result($st, $total);
        mysqli_stmt_fetch($st);
        mysqli_stmt_close($st);
        return (int) $total;
    }
}
