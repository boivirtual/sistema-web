<?php
include "conecta_mysql.inc";
include "valida_sessao.inc";

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id) {
    $sql = "SELECT * FROM tbl_versao WHERE tbl_versao_codigo_id = $id";
	$res = mysqli_query($conector, $sql);
	$row = mysqli_fetch_assoc($res);

    if ($row) {
        // Formata a data para o padrão brasileiro
        $dataF = date('d/m/Y', strtotime($row['tbl_versao_data']));
        
        // Retorna os dados em formato JSON
        echo json_encode([
            'titulo' => 'Versão ' . $row['tbl_versao_numero'] . ' - ' . $dataF,
            'texto'  => $row['tbl_versao_descricao']
        ]);
    }
}

?>