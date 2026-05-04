<?php 
include "conecta_mysql.inc";

$nome_usuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");

//dados do pasto de onde os animais vao sair
$pasto_remover_id = $_SESSION["pasto_id"];
$query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_remover_id AND tbl_pasto_lixeira = 0";
$request = mysqli_query($conector, $query);
$pasto_remover = mysqli_fetch_object($request);

$array_qtd_machos_remover = explode("!", $pasto_remover->tbl_pasto_array_qtd_animais_macho);
$array_qtd_femeas_remover = explode("!", $pasto_remover->tbl_pasto_array_qtd_animais_femea);
$array_qtd_ambos_remover = explode("!", $pasto_remover->tbl_pasto_array_qtd_animais_ambos);

//dados do pasto onde os animais vao entrar
$pasto_incluir_id = $_POST["pasto_incluir_id"];
$query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_incluir_id AND tbl_pasto_lixeira = 0";
$request = mysqli_query($conector, $query);
$pasto_incluir = mysqli_fetch_object($request);

$array_qtd_machos_incluir = explode("!", $pasto_incluir->tbl_pasto_array_qtd_animais_macho);
$array_qtd_femeas_incluir = explode("!", $pasto_incluir->tbl_pasto_array_qtd_animais_femea);
$array_qtd_ambos_incluir = explode("!", $pasto_incluir->tbl_pasto_array_qtd_animais_ambos);

for($i = 0; $i < count($array_qtd_machos_remover); $i++){
    if($array_qtd_machos_remover[$i] != ''){
        if($array_qtd_machos_incluir[$i] != ''){
            $array_qtd_machos_incluir[$i] += $array_qtd_machos_remover[$i];
            $array_qtd_machos_remover[$i] = '';
        }else{
            $array_qtd_machos_incluir[$i] = $array_qtd_machos_remover[$i];
            $array_qtd_machos_remover[$i] = '';
        }
    }
    if($array_qtd_femeas_remover[$i] != ''){
        if($array_qtd_femeas_incluir[$i] != ''){
            $array_qtd_femeas_incluir[$i] += $array_qtd_femeas_remover[$i];
            $array_qtd_femeas_remover[$i] = '';
        }else{
            $array_qtd_femeas_incluir[$i] = $array_qtd_femeas_remover[$i];
            $array_qtd_femeas_remover[$i] = '';
        }
    }
    if($array_qtd_ambos_remover[$i] != ''){
        if($array_qtd_ambos_incluir[$i] != ''){
            $array_qtd_ambos_incluir[$i] += $array_qtd_ambos_remover[$i];
            $array_qtd_ambos_remover[$i] = '';
        }else{
            $array_qtd_ambos_incluir[$i] = $array_qtd_ambos_remover[$i];
            $array_qtd_ambos_remover[$i] = '';
        }
    }
}

$machos_remover = implode("!", $array_qtd_machos_remover);
$femeas_remover = implode("!", $array_qtd_femeas_remover);
$ambos_remover = implode("!", $array_qtd_ambos_remover);

$query = "UPDATE tbl_pasto SET 
        tbl_pasto_array_qtd_animais_macho = '$machos_remover',
        tbl_pasto_array_qtd_animais_femea = '$femeas_remover',
        tbl_pasto_array_qtd_animais_ambos = '$ambos_remover',
        tbl_pasto_alterado_em = '$data_sistema',
        tbl_pasto_alterado_por = '$nome_usuario'
        WHERE tbl_pasto_id = '$pasto_remover_id'";
$request = mysqli_query($conector, $query);

$machos_incluir = implode("!", $array_qtd_machos_incluir);
$femeas_incluir = implode("!", $array_qtd_femeas_incluir);
$ambos_incluir = implode("!", $array_qtd_ambos_incluir);

$query = "UPDATE tbl_pasto SET 
        tbl_pasto_array_qtd_animais_macho = '$machos_incluir',
        tbl_pasto_array_qtd_animais_femea = '$femeas_incluir',
        tbl_pasto_array_qtd_animais_ambos = '$ambos_incluir',
        tbl_pasto_alterado_em = '$data_sistema',
        tbl_pasto_alterado_por = '$nome_usuario'
        WHERE tbl_pasto_id = '$pasto_incluir_id'";
$request = mysqli_query($conector, $query);

?>