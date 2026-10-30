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

    // Tipo do registro da fazenda no dia (AAAA-MM-DD): 'R' (Resumo),
    // 'F' (Folga), null se o dia está livre, false se a consulta falhar.
    public function tipoNoDia($fazenda, $dia){
        $fazenda = (int) $fazenda;
        $st = mysqli_prepare(
            $this->con,
            "SELECT tbl_resumo_diario_tipo FROM tbl_resumo_diario
              WHERE tbl_resumo_diario_fazenda = ? AND tbl_resumo_diario_data = ?
              LIMIT 1"
        );
        if (!$st) {
            return false;
        }
        mysqli_stmt_bind_param($st, 'is', $fazenda, $dia);
        mysqli_stmt_execute($st);
        mysqli_stmt_bind_result($st, $tipo);
        $achou = mysqli_stmt_fetch($st);
        mysqli_stmt_close($st);
        return $achou ? (string) $tipo : null;
    }

    // Volume de chuva (mm) registrado no programa Chuva para a fazenda no dia
    // (AAAA-MM-DD). null se não há registro, false se a consulta falhar.
    public function chuvaNoDia($fazenda, $dia){
        $fazenda = (int) $fazenda;
        $st = mysqli_prepare(
            $this->con,
            "SELECT SUM(tbl_chuva_volume_chuva) FROM tbl_chuva
              WHERE tbl_chuva_local = ? AND tbl_chuva_data = ?"
        );
        if (!$st) {
            return false;
        }
        mysqli_stmt_bind_param($st, 'is', $fazenda, $dia);
        mysqli_stmt_execute($st);
        mysqli_stmt_bind_result($st, $volume);
        mysqli_stmt_fetch($st);
        mysqli_stmt_close($st);
        return $volume === null ? null : (float) $volume;
    }

    // Grava um registro (Resumo ou Folga). Devolve 'ok', 'duplicado' (a
    // fazenda já tem registro nessa data — índice único fazenda + data) ou
    // 'erro'.
    public function incluir(ResumoDiario $r){
        $st = mysqli_prepare(
            $this->con,
            "INSERT INTO tbl_resumo_diario (tbl_resumo_diario_fazenda, tbl_resumo_diario_usuario,
                tbl_resumo_diario_data, tbl_resumo_diario_tipo, tbl_resumo_diario_incluido_em)
             VALUES (?, ?, ?, ?, ?)"
        );
        if (!$st) {
            return 'erro';
        }
        $fazenda = (int) $r->getFazenda();
        $usuario = $r->getUsuario();
        $data = $r->getData();
        $tipo = $r->getTipo();
        $incluidoEm = $r->getIncluidoEm();
        mysqli_stmt_bind_param($st, 'issss', $fazenda, $usuario, $data, $tipo, $incluidoEm);
        $ok = mysqli_stmt_execute($st);
        $codigoErro = mysqli_stmt_errno($st);
        mysqli_stmt_close($st);
        if ($ok) {
            return 'ok';
        }
        // 1062 = Duplicate entry
        return $codigoErro === 1062 ? 'duplicado' : 'erro';
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
