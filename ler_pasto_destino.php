<?php 
/*  Verifica se a descrição do lote esta vazia para atender a premissa 1
    das transferencia de todos os animais do mapa de gado
    Chamada dos programas 
    form_mapa_gado.php  
    form_mapa_gados_movimentcao_exibir_pasto.php
    mapa_gados.js
*/ 

include "conecta_mysql.inc";

$pasto_destino = $_POST["id_destino"];

$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
    WHERE tbl_pasto_id = $pasto_destino AND 
          tbl_pasto_lixeira = 0");

$reg_pasto = mysqli_fetch_object($tbl_pasto);
$descricao_lote = $reg_pasto->tbl_pasto_descricao_lote;

header('Content-type: application/json');
echo json_encode(array('success' => true, 'message' => $descricao_lote));
exit;

?>