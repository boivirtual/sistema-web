<?php

include "conecta_mysql.inc";
$mensagem = 0;

$bcr_id = $_POST['bcr_id'];
$parcela = $_POST['numero_parcela'];
$sequencia = $_POST['sequencia_baixa'];

$sql = ("DELETE FROM baixa_contas_receber WHERE bcr_id='$bcr_id' and 
                                                bcr_parcela='$parcela' and 
							                    bcr_sequencia='$sequencia'");

$resultado = mysqli_query($conector,$sql);

$ssql = "select * from baixa_contas_receber 
    where bcr_id='$bcr_id' and 
          bcr_parcela='$parcela'";

$conta_baixada = mysqli_query($conector,$ssql);

$num_rows = mysqli_num_rows($conta_baixada);

if ($num_rows != 0) {
    $sql = ("UPDATE contas_receber SET ctr_situacao='C'
	         WHERE ctr_id='$bcr_id'");
} else {
    $sql = ("UPDATE contas_receber SET ctr_situacao=''
	         WHERE ctr_id='$bcr_id'");
}

$resultado = mysqli_query($conector,$sql);

if (!$resultado) {
    $mensagem = "Erro na atualizacao da conta" . "\n" . mysql_error();
}

echo $mensagem;

mysqli_close($conector);
?>


