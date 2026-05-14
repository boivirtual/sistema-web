<?php 
	include "conecta_mysql.inc";

	$mensagem_erro = '';
	$data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
    $controle_estoque= $_SESSION['controle_estoque'];
	$numero_pesagem_id= $_POST['numero_doc'];
	$local= $_POST['local_pesado'];
	$epoca_pesagem= $_POST['epoca_pesado'];
	$animais_pesados= $_POST['animais_pesados'];

	$uploaddir = 'planilhas_excel/';
	$uploadfile = $uploaddir . basename($_FILES['arquivo_excel']['name']);

    move_uploaded_file($_FILES['arquivo_excel']['tmp_name'], $uploadfile);

	require 'vendor/autoload.php'; //autoload do projeto

	use PhpOffice\PhpSpreadsheet\IOFactory; //classe responsável pelo load dos arquivos de planilha
	use PhpOffice\PhpSpreadsheet\Spreadsheet; //classe responsável pela manipulação da planilha

	$spreadsheet = IOFactory::load($uploadfile); //carregando a planilha spreadsheet2 em um objeto PHP

	$sheet = $spreadsheet->getActiveSheet(); //retornando a aba ativa

	$peso_total_kg=0;
	$total_pesados = 0;

	$num_planilha = $sheet->getCell('B3')->getValue();

	if ($num_planilha!=$numero_pesagem_id) {
		$mensagem_erro = 'Essa tabela do excel não corresponde ao documento selecionado Nº ' . $numero_pesagem_id . ' . Tabela selecionada Nº ' . $num_planilha;
		echo '
			<script type="text/javascript">
				location.href="form_pesagem_animais.php?editar=true&erro='.$mensagem_erro.'";
			</script>';
		mysqli_close($conector);
	}

	$lote = $sheet->getCell('B4')->getValue();

	$numero_linhas = $animais_pesados + 6;

	if ($controle_estoque=='I') {
		for ($i=7; $i <=$numero_linhas; $i++) { 
			$celula = "A" . $i;
			$codigo = $sheet->getCell($celula)->getValue();

			$celula = "B" . $i;
			$peso = $sheet->getCell($celula)->getValue();


			$celula = "I" . $i;
			$obs = $sheet->getCell($celula)->getValue();


	 		$rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
	                WHERE tbl_ite_pesagem_numero_id ='$numero_pesagem_id' AND tbl_ite_pesagem_codigo_animal='$codigo'");
	        $num_rows = mysqli_num_rows($rs);

	        if ($num_rows!=0) {
		        $fila = mysqli_fetch_object($rs);
		        $codigo_animal_id = $fila->tbl_ite_pesagem_codigo_id_animal;
		        $data_pesagem = $fila->tbl_ite_pesagem_data_emissao;

		 		$rs = mysqli_query($conector, "SELECT * FROM tbl_animais
		                WHERE tbl_animal_codigo_id='$codigo_animal_id'");

		        $fila = mysqli_fetch_object($rs);
		        $obs_animal = $fila->tbl_animal_observacao;

				if ($peso!='') {
					$total_pesados++;
					$peso_total_kg+=$peso;

					$sql = "UPDATE tbl_item_pesagem SET
							tbl_ite_pesagem_peso='$peso',
							tbl_ite_pesagem_observacao='$obs'
							WHERE tbl_ite_pesagem_numero_id='$numero_pesagem_id' AND tbl_ite_pesagem_codigo_animal='$codigo'";

					$resultado = mysqli_query($conector,$sql);
				    $erro_mysql = mysqli_error($conector);

					if (!$resultado){
						$mensagem_erro = 'Ocorreu um erro na gravação dos itens. Id animal: ' . $codigo . ' Peso:  '. $peso . ' ' . $erro_mysql;
						echo '
							<script type="text/javascript">
								location.href="form_pesagem_animais.php?editar=true&erro='.$mensagem_erro.'";
							</script>';
						mysqli_close($conector);
						exit;
					} 

					if ($obs!='') {
						$obs_animal.= ' - ' . $obs;
					}

			        if ($epoca_pesagem==001) {
					    $sql = "UPDATE tbl_animais SET
							tbl_animal_primeiro_peso='$peso',
							tbl_animal_lote_primeiro_peso='$lote',
							tbl_animal_data_primeiro_peso='$data_pesagem',
							tbl_animal_observacao='$obs_animal'
					    WHERE tbl_animal_codigo_id='$codigo_animal_id'";
					    $resultado = mysqli_query($conector,$sql);
			        }
			        else if ($epoca_pesagem==002 || $epoca_pesagem==8) {
					    $sql = "UPDATE tbl_animais SET
							tbl_animal_peso_desmama='$peso',
							tbl_animal_lote_desmama='$lote',
							tbl_animal_data_desmama='$data_pesagem',
							tbl_animal_observacao='$obs_animal'
		 	 		    WHERE tbl_animal_codigo_id='$codigo_animal_id'";
					    $resultado = mysqli_query($conector,$sql);
			        }

					$sql = "UPDATE tbl_animais SET
						tbl_animal_ultimo_peso='$peso',
						tbl_animal_lote_ultimo='$lote',
						tbl_animal_data_ultimo='$data_pesagem',
						tbl_animal_observacao='$obs_animal'
					WHERE tbl_animal_codigo_id='$codigo_animal_id'";

				    $resultado = mysqli_query($conector,$sql);
				    $erro_mysql = mysqli_error($conector);

		   			if (!$resultado){
						$mensagem_erro = 'Ocorreu um erro na gravação do peso no cadastro de animais.' . $erro_mysql;
						echo '
							<script type="text/javascript">
								location.href="form_pesagem_animais.php?editar=true&erro='.$mensagem_erro.'";
							</script>';
							mysqli_close($conector);
							exit;
		   			} 
				}
	        }
		}
	}
	else {
		for ($i=7; $i <=$numero_linhas; $i++) { 
			$celula = "A" . $i;
			$item = $sheet->getCell($celula)->getValue();

			$celula = "E" . $i;
			$peso = $sheet->getCell($celula)->getValue();

			$celula = "G" . $i;
			$grupo = $sheet->getCell($celula)->getValue();

			$celula = "H" . $i;
			$obs = $sheet->getCell($celula)->getValue();

	 		$rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
	                WHERE tbl_ite_pesagem_numero_id ='$numero_pesagem_id' AND tbl_ite_pesagem_numero_item ='$item'");
	        $num_rows = mysqli_num_rows($rs);

	        if ($num_rows!=0) {
		        $fila = mysqli_fetch_object($rs);
		        $data_pesagem = $fila->tbl_ite_pesagem_data_emissao;

		        if ($peso!='') {
					$total_pesados++;
					$peso_total_kg+=$peso;

					$peso_arroba = ($peso/30);

		        	if ($grupo!='') {
						$sql = "UPDATE tbl_item_pesagem SET
							tbl_ite_pesagem_peso='$peso',
							tbl_ite_pesagem_observacao='$obs',
							tbl_ite_pesagem_peso_medio='$peso',
							tbl_ite_pesagem_arroba='$peso_arroba',
							tbl_ite_pesagem_arroba_media='$peso_arroba',
							tbl_ite_pesagem_grupo_pasto_destino='$grupo'
							WHERE tbl_ite_pesagem_numero_id='$numero_pesagem_id' AND tbl_ite_pesagem_numero_item ='$item'";
		        	}
		        	else {
						$sql = "UPDATE tbl_item_pesagem SET
							tbl_ite_pesagem_peso='$peso',
							tbl_ite_pesagem_observacao='$obs',
							tbl_ite_pesagem_peso_medio='$peso',
							tbl_ite_pesagem_arroba='$peso_arroba',
							tbl_ite_pesagem_arroba_media='$peso_arroba'
							WHERE tbl_ite_pesagem_numero_id='$numero_pesagem_id' AND tbl_ite_pesagem_numero_item ='$item'";
		        	}

					$resultado = mysqli_query($conector,$sql);
				    $erro_mysql = mysqli_error($conector);

					if (!$resultado){
						$mensagem_erro = 'Ocorreu um erro na gravação dos itens. Item animal: ' . $item . ' Peso:  '. $peso . ' ' . $erro_mysql;
						echo '
							<script type="text/javascript">
								location.href="form_pesagem_animais.php?editar=true&erro='.$mensagem_erro.'";
							</script>';
						mysqli_close($conector);
						exit;
					} 
				}
	        }
		}
	}

	if ($peso_total_kg!=0) {
		$peso_total_arroba = $peso_total_kg/30;
		$peso_medio_kg = $peso_total_kg/$total_pesados;
		$peso_medio_arroba = $peso_total_arroba/$total_pesados;
//		$peso_total_arroba = round($peso_total_kg/30);
//		$peso_medio_kg = round($peso_total_kg/$total_pesados);
//		$peso_medio_arroba = round($peso_total_arroba/$total_pesados);

		$sql = "UPDATE tbl_pesagem SET
		  		tbl_pesagem_lote='$lote',
				tbl_pesagem_qtd_animais_pesados='$total_pesados',
				tbl_pesagem_peso_kg='$peso_total_kg',
				tbl_pesagem_peso_arroba='$peso_total_arroba',
				tbl_pesagem_peso_medio_kg='$peso_medio_kg',
				tbl_pesagem_peso_medio_arroba='$peso_medio_arroba',
				tbl_pesagem_alterado_em='$data_sistema',
				tbl_pesagem_alterado_por='$nomeusuario'
		WHERE tbl_pesagem_id='$numero_pesagem_id'";

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			$mensagem_erro = 'Ocorreu um erro ao registrar a pesagem ' . $erro_mysql;
			echo '
				<script type="text/javascript">
					location.href="form_pesagem_animais.php?editar=true&erro='.$mensagem_erro.'";
				</script>';
			mysqli_close($conector);
		} 
	}
	else {
		$mensagem_erro = 'Não existem pesos registrados nessa tabela';
		echo '
			<script type="text/javascript">
				location.href="form_pesagem_animais.php?editar=true&erro='.$mensagem_erro.'";
			</script>';
		mysqli_close($conector);
	}

	unlink($uploadfile);

	mysqli_close($conector);

	if ($controle_estoque=='I') {
		echo '
			<script type="text/javascript">
				location.href="form_pesagem_animais_editar_offline.php?id='.$numero_pesagem_id.'";
			</script>';
	}
	else {
		echo '
			<script type="text/javascript">
				location.href="form_pesagem_animais_editar_lote_offline.php?id='.$numero_pesagem_id.'";
			</script>';
	}

/*

	$mensagem_erro = 'Entrei';

	echo '
		<script type="text/javascript">
			location.href="form_pesagem_animais.php?editar=true&erro='.$mensagem_erro.'";
		</script>';

*/
?>