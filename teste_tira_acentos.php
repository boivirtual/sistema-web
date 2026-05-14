<?php
function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}

    include "conecta_mysql.inc";

    $ajuda= mysqli_query($conector, "SELECT * FROM tbl_ajuda");

    $num_rows = mysqli_num_rows($ajuda);  
    
        while ($reg_ajuda = mysqli_fetch_object($ajuda)) {
            $id = $reg_ajuda->id_ajuda;
            $palavra = $reg_ajuda->palavra_chave_ajuda;
            $palavra = tirarAcentos($palavra);

            $sql = ("UPDATE tbl_ajuda SET palavra_chave_ajuda='$palavra'
                WHERE id_ajuda ='$id'");

            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
                echo 'Erro: ' . $erro_mysql . '</br>';   
            }
        }

   echo 'Fim processamento';
?>