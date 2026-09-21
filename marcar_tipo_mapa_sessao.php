<?php 
@ session_start(); 

$tipo_mapa = $_POST['tipo_mapa'];

$_SESSION['tipo_mapa_gado'] = $tipo_mapa;
exit;

?>