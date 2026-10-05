<?php
// Grava a nova data de um evento arrastado/redimensionado no calendário da agenda
include "conecta_mysql.inc";
include_once "funcao_agenda_protocolo.php";
@ session_start();
$nomeusuario = mysqli_real_escape_string($conector, $_SESSION['nome_usuario']);
$data_sistema = date("Y-m-d H:i:s");

header('Content-type: application/json');

$id_evento = isset($_POST["id_evento"]) ? mysqli_real_escape_string($conector, $_POST["id_evento"]) : '';
$data_inicial = isset($_POST["data_inicial"]) ? $_POST["data_inicial"] : '';
$data_final = isset($_POST["data_final"]) ? $_POST["data_final"] : '';

$formato_data_hora = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';

if ($id_evento=='' || !preg_match($formato_data_hora, $data_inicial) || ($data_final!='' && !preg_match($formato_data_hora, $data_final))) {
    echo json_encode(array('error' => true, 'message' => 'Dados inválidos para alterar a data do evento.'));
    mysqli_close($conector);
    exit;
}

$tbl_agenda_evento = mysqli_query($conector, "SELECT tbl_agenda_codigo_cobertura, tbl_agenda_titulo FROM tbl_agenda
    WHERE tbl_agenda_id = '$id_evento' AND tbl_agenda_lixeira = 0");
$reg_agenda_evento = $tbl_agenda_evento ? mysqli_fetch_object($tbl_agenda_evento) : null;

if (!$reg_agenda_evento) {
    echo json_encode(array('error' => true, 'message' => 'Evento não encontrado.'));
    mysqli_close($conector);
    exit;
}

// Eventos gerados pelo Protocolo IATF (D0, D7, D9...) não podem ser editados
if (agenda_evento_protocolo($reg_agenda_evento->tbl_agenda_codigo_cobertura, $reg_agenda_evento->tbl_agenda_titulo)) {
    echo json_encode(array('error' => true, 'message' => 'Este evento foi gerado pelo Protocolo IATF e não pode ser editado ou excluído pela agenda.'));
    mysqli_close($conector);
    exit;
}

$data_final_sql = ($data_final=='') ? "null" : "'$data_final'";

$sql = "UPDATE tbl_agenda SET
    tbl_agenda_data_inicial='$data_inicial',
    tbl_agenda_data_final=$data_final_sql,
    tbl_agenda_alterado_em='$data_sistema',
    tbl_agenda_alterado_por='$nomeusuario'
WHERE tbl_agenda_id='$id_evento'";

$resultado = mysqli_query($conector,$sql);
$erro_mysql = mysqli_error($conector);

if (!$resultado){
    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao processar sua solicitação. ' . $erro_mysql));
    mysqli_close($conector);
    exit;
}

echo json_encode(array('success' => true, 'message' => 'Registro alterado com sucesso.'));
mysqli_close($conector);
exit;
?>
