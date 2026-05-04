<?php 
  ob_start();
  header('Content-Type: text/html; charset=utf-8');

  @ session_start(); 
 
  $servidor = "127.0.0.1";
  $usuario_bd = "root";
  $senha_bd = "a2ngei9Mxh";
  $banco = 97174041604;
   
  $conector = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
  
  if (mysqli_connect_error()) {
      printf("Falha na conexão: ", mysqli_connect_error());
      exit();
  }

  $bancoselecionado = mysqli_select_db($conector,$banco);

  if ($bancoselecionado === FALSE) {
      printf("Falha na seleção do banco de dados: ", mysqli_error($conector));
      exit();
  }

$local = $_POST['codigo_local'];
$data_ocorrencia = $_POST['data_aborto'];
$codigo_mae = $_POST['codigo_mae_animal'];
$codigo_mae_consulta = $_POST['codigo_mae_consulta'];
$ocorrencia = $_POST['opcao_nascimento'];

$data_sistema = date("Y-m-d");

// entrada_saida = A (Aborto/Absorsao)
// entrada_saida = E (Entrada)

// tipo_movimentacao = A (Aborto)
// tipo_movimentacao = B (Absorção)
// tipo_movimentacao = N (Nascimento - natimorto)

if ($ocorrencia=='A') {
	$entrada_saida = "A";
	$tipo_movimentacao = "A";
	$nascido = 'A';
}
else if ($ocorrencia=='B') {
	$entrada_saida = "A";
	$tipo_movimentacao = "B";
	$nascido = 'A';
}

$id_animal = 999999999;

$sql = "INSERT INTO tbl_movimentacao_estoque
                   (tbl_mov_estoque_codigo_id_animal,
	                tbl_mov_estoque_data_emissao,
	                tbl_mov_estoque_nascimento,
	                tbl_mov_estoque_local,
	                tbl_mov_estoque_entrada_saida,
	                tbl_mov_estoque_tipo_movimentacao,
	                tbl_mov_estoque_local_origem,
	                tbl_mov_estoque_local_destino,
	                tbl_mov_estoque_codigo_movimentacao,
	                tbl_mov_estoque_codigo_pasto,
	                tbl_mov_estoque_codigo_raca,
	                tbl_mov_estoque_codigo_pelagem,
	                tbl_mov_estoque_sexo,
	                tbl_mov_estoque_primeiro_peso,
	                tbl_mov_estoque_codigo_mae
	                ) 
	                VALUES ('$id_animal',
	                        '$data_sistema',
	                        '$data_ocorrencia',
	                        '$local',
	                        '$entrada_saida',
	                        '$tipo_movimentacao',
	                        '$local',
	                        null,
	                        null,
	                        null,
	                        null,
	                        null,
	                        'N',
	                        null,
	                        '$codigo_mae'
	                )";
			        
$resultado = mysqli_query($conector,$sql);
$erro_mysql = mysqli_error($conector);

if (!$resultado){
echo '
	<script type="text/javascript">
		alert ("Erro na inclusão do registro! "'.$erro_mysql.');
		location.href="aborto.php?local='.$local.'&codigo_id='.$codigo_mae.'&codigo_consulta='.$codigo_mae_consulta.'"
	</script>"
   	';
	exit;
}

$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
    WHERE tbl_animal_codigo_id ='$codigo_mae'");

$num_rows_animal = mysqli_num_rows($tbl_animal);	

if ($num_rows_animal!=0) {
	$reg_animal = mysqli_fetch_object($tbl_animal);
	$numero_aborto =  $reg_animal->tbl_animal_numero_abortos;
	$numero_aborto++;
}
else {
	$numero_aborto = 1;
}

$sql = ("UPDATE tbl_animais SET 
		tbl_animal_numero_abortos='$numero_aborto'
	WHERE tbl_animal_codigo_id ='$codigo_mae'");

$resultado = mysqli_query($conector,$sql);
$erro_mysql = mysqli_error($conector);

if (!$resultado){
echo '
	<script type="text/javascript">
		alert ("Erro ao alterar o cadastro da mãe! "'.$erro_mysql.');
		location.href="aborto.php?local='.$local.'&codigo_id='.$codigo_mae.'&codigo_consulta='.$codigo_mae_consulta.'"
	</script>"
   	';
	exit;
}

echo '
	<script type="text/javascript">
		alert ("Registro Gravado com sucesso!");
		location.href="aborto.php?local='.$local.'&codigo_id='.$codigo_mae.'&codigo_consulta='.$codigo_mae_consulta.'"
	</script>"
   	';
?>