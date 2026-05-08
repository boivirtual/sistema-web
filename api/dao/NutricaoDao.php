<?php
class NutricaoDao{

    private $con;

    public function __construct($banco){
        $this->con = mysqli_connect('localhost', 'root', 'a2ngei9Mxh', $banco);
        $this->systemDateHour = date('Y-m-d H:i:s');
        $this->systemDate = date('Y-m-d');
    }

    public function transferNutricao($incluirId, $removerId){
        $sql = "UPDATE tbl_nutricao SET tbl_nutricao_codigo_pasto = $incluirId WHERE tbl_nutricao_codigo_pasto = $removerId AND tbl_nutricao_data = '{$this->systemDate}'";
        mysqli_query($this->con, $sql);
        if(mysqli_error($this->con)){
            return [
                "error" => true,
                "message" => "Ocorreu um erro ao transferir a nutrição!"
            ];
        }

        return [
            "error" => false,
            "message" => ""
        ];
    }
}