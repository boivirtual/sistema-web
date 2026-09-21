<?php

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");

include "conecta_mysql.inc";

$idAnimal = $_POST["idAnimal"];
$idPasto = $_POST["idPasto"];
$sexoAnimal = $_POST["sexoAnimal"];
$idadeAnimal = $_POST["idadeAnimal"];

$sql = "UPDATE tbl_animal_pasto SET
        tbl_animal_pasto_nascimento = '$idadeAnimal',
        tbl_animal_pasto_sexo = '$sexoAnimal',
        tbl_animal_pasto_alterado_em = '$data_sistema',
        tbl_animal_pasto_alterado_por = '$nomeusuario'
        WHERE tbl_animal_pasto_numero_item = '$idAnimal' AND tbl_animal_pasto_id = '$idPasto'";

$resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));

?>