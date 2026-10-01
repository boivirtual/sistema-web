<?php
class NutricaoDao{

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

    /** Nutrição lançada no pasto de origem no dia da transferência passa
     *  para o pasto destino (Mapa de Gado — mover todos os animais). */
    public function transferirNutricaoDoDia($origem, $destino, $data){
        $origem = (int) $origem;
        $destino = (int) $destino;
        $data = mysqli_real_escape_string($this->con, $data);

        $ok = mysqli_query($this->con, "UPDATE tbl_nutricao SET tbl_nutricao_codigo_pasto = {$destino}
            WHERE tbl_nutricao_codigo_pasto = {$origem} AND tbl_nutricao_data = '{$data}'");

        return $ok ? ["error" => false, "message" => ""]
                   : ["error" => true, "message" => "Ocorreu um erro ao transferir a nutrição!"];
    }
}
