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

    // Conexão aberta, para os DAOs de Nascimento e Morte usarem a mesma.
    public function getConexao(){
        return $this->con;
    }

    // A fazenda teve nutrição no dia (AAAA-MM-DD, data da nutrição; sem as
    // excluídas)? true/false, null se a consulta falhar.
    public function nutricaoNoDia($fazenda, $dia){
        $fazenda = (int) $fazenda;
        $st = mysqli_prepare(
            $this->con,
            "SELECT COUNT(tbl_nutricao_id) FROM tbl_nutricao
              WHERE tbl_nutricao_codigo_local = ? AND tbl_nutricao_data = ?
                AND tbl_nutricao_lixeira = 0"
        );
        if (!$st) {
            return null;
        }
        mysqli_stmt_bind_param($st, 'is', $fazenda, $dia);
        mysqli_stmt_execute($st);
        mysqli_stmt_bind_result($st, $total);
        mysqli_stmt_fetch($st);
        mysqli_stmt_close($st);
        return (int) $total > 0;
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

    // Quantos resumos/folgas a empresa tem (de qualquer fazenda) até o dia
    // (AAAA-MM-DD), inclusive. null se a consulta falhar.
    public function contarEmpresaAte($dia){
        $st = mysqli_prepare(
            $this->con,
            "SELECT COUNT(tbl_resumo_diario_id) FROM tbl_resumo_diario
              WHERE tbl_resumo_diario_data <= ?"
        );
        if (!$st) {
            return null;
        }
        mysqli_stmt_bind_param($st, 's', $dia);
        mysqli_stmt_execute($st);
        mysqli_stmt_bind_result($st, $total);
        mysqli_stmt_fetch($st);
        mysqli_stmt_close($st);
        return (int) $total;
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

    // Teve transferência de animais entre pastos da fazenda no dia
    // (AAAA-MM-DD)? Confere as duas marcas que a transferência grava:
    //  - tbl_animal_pasto_alterado_em do animal movido (some se o animal
    //    morre depois: a Morte apaga a linha dele do pasto);
    //  - tbl_pasto_data_sem_animais (origem esvaziada) ou
    //    tbl_pasto_data_com_animais (destino que recebeu) — só as
    //    transferências gravam essas datas.
    // true/false, null se a consulta falhar.
    public function mudouPastoNoDia($fazenda, $dia){
        $fazenda = (int) $fazenda;
        $inicio = $dia . ' 00:00:00';
        $fim = $dia . ' 23:59:59';
        $animais = $this->contarNoPeriodo(
            "SELECT COUNT(tbl_animal_pasto_numero_item) FROM tbl_animal_pasto
              WHERE tbl_animal_pasto_local = ?
                AND tbl_animal_pasto_alterado_em BETWEEN ? AND ?",
            $fazenda, $inicio, $fim
        );
        if ($animais === null || $animais > 0) {
            return $animais === null ? null : true;
        }
        $pastos = $this->contarNoPeriodo(
            "SELECT COUNT(tbl_pasto_id) FROM tbl_pasto
              WHERE tbl_pasto_codigo_local = ? AND tbl_pasto_lixeira = 0
                AND (tbl_pasto_data_sem_animais BETWEEN ? AND ?
                     OR tbl_pasto_data_com_animais BETWEEN ? AND ?)",
            $fazenda, $inicio, $fim, $inicio, $fim
        );
        return $pastos === null ? null : $pastos > 0;
    }

    // COUNT com a fazenda (int) e as datas (strings) na ordem do SQL.
    private function contarNoPeriodo($sql, $fazenda, ...$datas){
        $st = mysqli_prepare($this->con, $sql);
        if (!$st) {
            return null;
        }
        $tipos = 'i' . str_repeat('s', count($datas));
        mysqli_stmt_bind_param($st, $tipos, $fazenda, ...$datas);
        mysqli_stmt_execute($st);
        mysqli_stmt_bind_result($st, $total);
        mysqli_stmt_fetch($st);
        mysqli_stmt_close($st);
        return (int) $total;
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
