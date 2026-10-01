<?php
/**
 * Descrição do Lote de animais no pasto: opções de descrição
 * (tbl_descricao_lote_animais) e numeração dos lotes por fazenda/ano
 * (tbl_sequencia_lote_animais) — mesmas tabelas de popular_descricao_lote.php
 * e gravar_alterar_descricao_lote.php (sistema web).
 */
class LoteAnimaisDao{

    private $con;

    /** $con: conexão já aberta para reaproveitar (ex: várias DAOs na mesma
     *  transação); sem ela, abre uma nova. */
    public function __construct($banco, $con = null){
        if ($con) {
            $this->con = $con;
        } else {
            require __DIR__ . "/../../conecta_mysql_credenciais.inc";
            $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        }
    }

    /** Opções de "Descrição do Lote" ativas. */
    public function listarDescricoes(){
        $a = [];
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, "SELECT tbl_descricao_lote_id, tbl_descricao_lote
            FROM tbl_descricao_lote_animais
            WHERE tbl_descricao_lote_lixeira = 0");
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $a[] = $row;
            }
        }
        return $a;
    }

    /** Próximo número de lote da fazenda no ano, já gravado na sequência.
     *  Mesma regra do web: lê o último da fazenda/ano (+1, ou 1 se não
     *  houver) e atualiza a linha da fazenda. Chamar dentro de transação. */
    public function proximoIdLote($local, $ano){
        $local = mysqli_real_escape_string($this->con, (string) $local);
        $ano = (int) $ano;

        $r = mysqli_query($this->con, "SELECT tbl_sequencial_id_lote
            FROM tbl_sequencia_lote_animais
            WHERE tbl_sequencial_id_local = '{$local}' AND
                  tbl_sequencial_ano_lote = '{$ano}'
            ORDER BY tbl_sequencial_id_lote DESC LIMIT 1 FOR UPDATE");
        $row = $r ? mysqli_fetch_assoc($r) : null;
        $idLote = $row ? ((int) $row['tbl_sequencial_id_lote']) + 1 : 1;

        mysqli_query($this->con, "UPDATE tbl_sequencia_lote_animais SET
            tbl_sequencial_id_lote = '{$idLote}',
            tbl_sequencial_ano_lote = '{$ano}'
            WHERE tbl_sequencial_id_local = '{$local}'");

        return $idLote;
    }
}
