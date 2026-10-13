<?php
/**
 * Consultas do NASCIMENTO (tela do pasto do Mapa de Gado), usadas por
 * MapaGadoNascimentoService — as mesmas de form_mapa_gados_movimentacao.php
 * do sistema web.
 */
class NascimentoDao{

    private $con;

    /** $con: conexão já aberta (a transação é do service); sem ela, abre
     *  uma nova. */
    public function __construct($banco, $con = null){
        if ($con) {
            $this->con = $con;
        } else {
            require __DIR__ . "/../../conecta_mysql_credenciais.inc";
            $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        }
        mysqli_set_charset($this->con, "utf8");
    }

    private function listar($sql, $montar){
        $a = [];
        $r = mysqli_query($this->con, $sql);
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $a[] = $montar($row);
            }
        }
        return $a;
    }

    /** Grupo "SEMEM" do select "Pai Nº": todo sêmen fora da lixeira. */
    public function listarSemens(){
        return $this->listar(
            "SELECT tbl_semem_codigo_id, tbl_semem_nome, tab_descricao_raca
               FROM tbl_semem
              INNER JOIN tabela_racas ON tab_codigo_raca = tbl_semem_codigo_raca
              WHERE tbl_semem_lixeira = 0",
            function ($row) {
                return [
                    "id"   => (string) $row['tbl_semem_codigo_id'],
                    "nome" => (string) $row['tbl_semem_nome'],
                    "raca" => (string) $row['tab_descricao_raca'],
                ];
            }
        );
    }

    /** Grupo "ANIMAIS" do select "Pai Nº": todo macho fora da lixeira (de
     *  qualquer fazenda, ativo ou não — igual ao web), pelo número. */
    public function listarMachos(){
        return $this->listar(
            "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa,
                    tbl_animal_codigo_numerico, tab_descricao_raca
               FROM tbl_animais
              INNER JOIN tabela_racas ON tab_codigo_raca = tbl_animal_codigo_raca
              WHERE tbl_animal_lixeira = 0
                AND tbl_animal_sexo = 'M'
              ORDER BY tbl_animal_codigo_numerico",
            function ($row) {
                return [
                    "id"       => (string) $row['tbl_animal_codigo_id'],
                    "alfa"     => (string) $row['tbl_animal_codigo_alfa'],
                    "numerico" => (string) $row['tbl_animal_codigo_numerico'],
                    "raca"     => (string) $row['tab_descricao_raca'],
                ];
            }
        );
    }

    /** Opções de "Raça". */
    public function listarRacas(){
        return $this->listar(
            "SELECT tab_codigo_raca, tab_descricao_raca
               FROM tabela_racas
              WHERE tab_registro_lixeira_raca = 0",
            function ($row) {
                return [
                    "id"        => (string) $row['tab_codigo_raca'],
                    "descricao" => (string) $row['tab_descricao_raca'],
                ];
            }
        );
    }

    /** Opções de "Pelagem". */
    public function listarPelagens(){
        return $this->listar(
            "SELECT tab_codigo_pelagem, tab_descricao_pelagem
               FROM tabela_pelagens
              WHERE tab_registro_lixeira_pelagem = 0",
            function ($row) {
                return [
                    "id"        => (string) $row['tab_codigo_pelagem'],
                    "descricao" => (string) $row['tab_descricao_pelagem'],
                ];
            }
        );
    }
}
