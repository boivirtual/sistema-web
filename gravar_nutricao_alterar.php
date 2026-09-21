<?php 
include "conecta_mysql.inc";

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");

$tipo_gravacao = $_POST['tipo_gravacao'];
$id_nutricao = $_POST['id_nutricao'];
$id_local = $_POST['id_local'];
$id_produto = $_POST['id_produto'];
$qtd_produto = $_POST['quantidade'];
$data_encerramento = $_POST['data_encerramento'];
$qtd_animais = $_POST['qtd_animais'];
$ultima_nutricao = $_POST['ultima_nutricao'];
$dias_nutricao = $_POST['dias_nutricao'];

if ($tipo_gravacao==1 && $data_encerramento=='') {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Data do Encerramento!'));
	exit;
}

if ($tipo_gravacao==2){
	$sql = "DELETE FROM tbl_nutricao WHERE tbl_nutricao_id = '$id_nutricao'";

	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro excluido com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao excluir o registro: ' . $erro_mysql));
	   	exit;
	} 
	else {
        $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_produto_estoque
	            WHERE tbl_produto_estoque_codigo_id='$id_produto' AND 
	                  tbl_produto_estoque_codigo_local='$id_local' AND 
	                  tbl_produto_estoque_lixeira = 0"); 
        $num_rows = mysqli_num_rows($tbl_estoque);

        if ($num_rows!=0) {
            $reg_est = mysqli_fetch_object($tbl_estoque);
            $qtd_estoque = $reg_est->tbl_produto_estoque_atual;
            $qtd_estoque+= $quantidade;

            $sql = ("UPDATE tbl_produto_estoque SET
                            tbl_produto_estoque_atual='$qtd_estoque',
                            tbl_produto_estoque_alterado_em='$data_sistema',
                            tbl_produto_estoque_alterado_por='$nomeusuario'
                        WHERE tbl_produto_estoque_codigo_id='$id_produto' AND 
                              tbl_produto_estoque_codigo_local='$id_local'");

            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
                header('Content-type: application/json');
                echo json_encode(array('error' => $erro_mysql, 'message' => 'Erro na alteração do estoque - ' . $erro_mysql));
                exit;
            } 
            else {
                header('Content-type: application/json');
                echo json_encode($resposta);
            }
        }
        else {
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Estoque não encontrado para o produto/local'));
            exit;
        }
	}
}
else {
	$consumo = ($qtd_produto/$qtd_animais/$dias_nutricao)*1000;

	$sql = ("UPDATE tbl_nutricao SET
                tbl_nutricao_dias_consumo='$dias_nutricao',
                tbl_nutricao_consumo_cabeca_dia='$consumo',
                tbl_nutricao_data_encerramento='$data_encerramento',
                tbl_nutricao_encerrada ='S',
				tbl_nutricao_encerrado_em='$data_sistema',
				tbl_nutricao_encerrado_por='$nomeusuario'
	 		WHERE tbl_nutricao_id='$id_nutricao'");

	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Nutrição encerrada com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro no encerramento da nutrição: ' . $erro_mysql));
	} 
	else {
	   	header('Content-type: application/json');
	   	echo json_encode($resposta);
	}

	mysqli_close($conector);
	exit;
}

mysqli_close($conector);
exit;

?>