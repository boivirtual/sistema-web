<?php 
include "conecta_mysql.inc";

$nome_usuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");
$pasto_origem = $_POST["pasto_origem"];
$id_lote = $_POST["id_lote"];
$novo_id = $_POST["novo_id"];
$ano_lote = $_POST["ano_lote"];
$descricao_lote = $_POST["descricao_lote"];
$descricao_lote1 = $_POST["descricao_lote1"];
$descricao_lote2 = $_POST["descricao_lote2"];
$descricao_lote3 = $_POST["descricao_lote3"];
$descricao_lote4 = $_POST["descricao_lote4"];
$descricao_lote5 = $_POST["descricao_lote5"];
$descricao_lote6 = $_POST["descricao_lote6"];

// Verifica qual a fazenda
$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
        WHERE tbl_pasto_id = '$pasto_origem'");

$reg_pasto = mysqli_fetch_object($tbl_pasto);
$id_fazenda = $reg_pasto->tbl_pasto_codigo_local;
//------------------------------------------------------------

// Ordena descricoes do lote com dados 
for ($i=0; $i <=6 ; $i++) { 
   $array_lotes[$i]='';
}

for ($i=0; $i <6 ; $i++) { 
    if ($descricao_lote1) {
        $array_lotes[$i]=$descricao_lote1;
        $descricao_lote1='';
    }
    else if ($descricao_lote2) {
        $array_lotes[$i]=$descricao_lote2;
        $descricao_lote2='';
    }
    else if ($descricao_lote3) {
        $array_lotes[$i]=$descricao_lote3;
        $descricao_lote3='';
    }
    else if ($descricao_lote4) {
        $array_lotes[$i]=$descricao_lote4;
        $descricao_lote4='';
    }
    else if ($descricao_lote5) {
        $array_lotes[$i]=$descricao_lote5;
        $descricao_lote5='';
    }
    else if ($descricao_lote6) {
        $array_lotes[$i]=$descricao_lote6;
        $descricao_lote6='';
    }
}

if ($id_lote=='' || $id_lote==0 || $novo_id=='S') {
    $data_atual = new DateTime();
    $ano_lote = $data_atual->format('Y');

    $tbl_sequencial = mysqli_query($conector, "SELECT * FROM tbl_sequencia_lote_animais
        WHERE tbl_sequencial_id_local  = '$id_fazenda' AND 
              tbl_sequencial_ano_lote ='$ano_lote'
        ORDER BY tbl_sequencial_id_lote DESC LIMIT 1");

    $num_rows = mysqli_num_rows($tbl_sequencial);

    if ($num_rows==0) {
        $id_lote = 1;
    }
    else {
        $reg_sequencial = mysqli_fetch_object($tbl_sequencial);
        $id_lote = $reg_sequencial->tbl_sequencial_id_lote;
        $id_lote++;
    }

    $query = "UPDATE tbl_sequencia_lote_animais SET 
    tbl_sequencial_id_lote  = '$id_lote',
    tbl_sequencial_ano_lote  = '$ano_lote'
    WHERE tbl_sequencial_id_local = '$id_fazenda'";

    $request = mysqli_query($conector, $query);
}

$query = "UPDATE tbl_pasto SET 
    tbl_pasto_id_lote = '$id_lote',
    tbl_pasto_ano_lote = '$ano_lote',
    tbl_pasto_descricao_lote = '$descricao_lote',
    tbl_pasto_descricao_lote_1='$array_lotes[0]',
    tbl_pasto_descricao_lote_2='$array_lotes[1]',
    tbl_pasto_descricao_lote_3='$array_lotes[2]',
    tbl_pasto_descricao_lote_4='$array_lotes[3]',
    tbl_pasto_descricao_lote_5='$array_lotes[4]',
    tbl_pasto_descricao_lote_6='$array_lotes[5]',
    tbl_pasto_alterado_em = '$data_sistema',
    tbl_pasto_alterado_por = '$nome_usuario'
    WHERE tbl_pasto_id = '$pasto_origem'";

$request = mysqli_query($conector, $query);
$erro_mysql = mysqli_error($conector);

if (!$request){
    header('Content-type: application/json');
    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a Descrição do Lote: ' . $erro_mysql));
    exit;
}
else {

    $id_lote = str_pad($id_lote, 4, "0", STR_PAD_LEFT);
    $ano = substr($ano_lote, 2, 2);
    $desc_id_lote = 'L-'.$id_lote.'/'.$ano;

    header('Content-type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Atualização da Descrição do Lote com sucesso ', 
        'descricao_com_id' => $descricao_lote .' '. $desc_id_lote,
        'descricao_lote' => $descricao_lote,
        'descricao_lote1' => $array_lotes[0],
        'descricao_lote2' => $array_lotes[1],
        'descricao_lote3' => $array_lotes[2],
        'descricao_lote4' => $array_lotes[3],
        'descricao_lote5' => $array_lotes[4],
        'descricao_lote6' => $array_lotes[5],
        'id_lote' => $id_lote,
        'ano_lote' => $ano_lote
        
        ));
}

mysqli_close($conector);
exit;

?>