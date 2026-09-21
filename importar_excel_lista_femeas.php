<?php 
	include "conecta_mysql.inc";

	$mensagem_erro = '';
	$data_sistema = date("Y-m-d H:i:s");
	$data_selecao = date("Y-m-d");

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$numero_cobertura= $_POST['numero_doc'];
	$local= $_POST['codigo_local'];
	$femeas_listadas= $_POST['femeas_listadas'];

	$uploaddir = 'planilhas_excel/';
	$uploadfile = $uploaddir . basename($_FILES['arquivo_excel']['name']);

    move_uploaded_file($_FILES['arquivo_excel']['tmp_name'], $uploadfile);

	require 'vendor/autoload.php'; //autoload do projeto

	use PhpOffice\PhpSpreadsheet\IOFactory; //classe responsável pelo load dos arquivos de planilha
	use PhpOffice\PhpSpreadsheet\Spreadsheet; //classe responsável pela manipulação da planilha

	$spreadsheet = IOFactory::load($uploadfile); //carregando a planilha spreadsheet2 em um objeto PHP

	$sheet = $spreadsheet->getActiveSheet(); //retornando a aba ativa

	$num_planilha = $sheet->getCell('B3')->getValue();

	if ($num_planilha!=$numero_cobertura) {
		$mensagem_erro = 'O nº da planilha não corresponde ao nº do documento informado';
		echo '
			<script type="text/javascript">
				location.href="form_selecao_matrizes.php?editar=true&erro='.$mensagem_erro.'";
			</script>';
		mysqli_close($conector);
		exit;
	}

	$numero_linhas = $femeas_listadas + 5;
	$tem_item_confirmado = '';

	for ($i=6; $i <=$numero_linhas; $i++) { 
		$celula = "A" . $i;
		$confirma = $sheet->getCell($celula)->getValue(); //$cellA1 recebe os dados da célula A1

		if ($confirma!='' && $confirma!='Descartada') {
			$tem_item_confirmado = 'S';
		}
	}
	
	if ($tem_item_confirmado=='') {
		$mensagem_erro = 'Não existem fêmeas confirmadas nessa tabela';
		echo '
			<script type="text/javascript">
				location.href="form_selecao_matrizes.php?editar=true&erro='.$mensagem_erro.'";
			</script>';
		mysqli_close($conector);
		exit;
	}

	$tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura
	    WHERE tbl_cobertura_lixeira=0 AND 
	          tbl_cobertura_id ='$numero_cobertura'");

	$num_rows = mysqli_num_rows($tbl_cobertura);

	if ($num_rows!=0) {
		$reg = mysqli_fetch_object($tbl_cobertura);
		$controle = $reg->tbl_cobertura_controle;
		$local = $reg->tbl_cobertura_codigo_local;
	}
	else {
		$mensagem_erro = 'Erro ao verficar a lista da cobertura';
		echo '
			<script type="text/javascript">
				location.href="form_selecao_matrizes.php?editar=true&erro='.$mensagem_erro.'";
			</script>';
		mysqli_close($conector);
		exit;
	}

	if ($controle == 'C') {
		for ($i=6; $i <=$numero_linhas; $i++) { 
			$celula = "A" . $i;
			$confirma = $sheet->getCell($celula)->getValue(); //$cellA1 recebe os dados da célula A1

			$celula = "B" . $i;
			$codigo = $sheet->getCell($celula)->getValue(); //$cellA1 recebe os dados da célula A1

		 	$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
		        WHERE tbl_ite_cobertura_numero_id ='$numero_cobertura' AND 
		              tbl_ite_cobertura_codigo_animal='$codigo'");

		    $num_rows = mysqli_num_rows($tbl_item_cobertura);

		    if ($num_rows!=0) {
			    $reg_item = mysqli_fetch_object($tbl_item_cobertura);
			    $codigo_animal_id = $reg_item->tbl_ite_cobertura_codigo_id_animal;
			    $numero_item = $reg_item->tbl_ite_cobertura_numero_item;

			    if ($confirma=='' || $confirma=='Descartada') {
				    $sql = ("DELETE FROM tbl_item_cobertura 
					    	WHERE tbl_ite_cobertura_numero_id = '$numero_cobertura' AND 
					    		  tbl_ite_cobertura_numero_item = '$numero_item'");

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
						$mensagem_erro = 'Ocorreu um erro na exclusão do item. Id animal: ' . 
						$codigo . ' ' . $erro_mysql;
						echo '
							<script type="text/javascript">
								location.href="form_selecao_matrizes.php?editar=true&erro='.$mensagem_erro.'";
							</script>';
						mysqli_close($conector);
						exit;
					}

					$femeas_listadas--;
				} 
			}
		}

		if ($femeas_listadas>0) {
			$sql = "UPDATE tbl_cobertura SET
			  		tbl_cobertura_qtd_animais='$femeas_listadas',
					tbl_cobertura_alterado_em='$data_sistema',
					tbl_cobertura_alterado_por='$nomeusuario',
					tbl_cobertura_planilha_processada='S'
			WHERE tbl_cobertura_id='$numero_cobertura'";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
				$mensagem_erro = 'Ocorreu um erro ao alterar o registro do cobertura ' . $erro_mysql;
				echo '
					<script type="text/javascript">
						location.href="form_selecao_matrizes.php?editar=true&erro='.$mensagem_erro.'";
					</script>';
				mysqli_close($conector);
				exit;
			} 
		}

		unlink($uploadfile);
		mysqli_close($conector);

		$mensagem_erro = 'Planilha processada com sucesso';

		echo '
			<script type="text/javascript">

				location.href="form_selecao_matrizes_incluir.php?editar=true&id_cobertura='.$numero_cobertura.'";
			</script>';
	}
	else {
		for ($i=6; $i <=$numero_linhas; $i++) { 
			$celula = "A" . $i;
			$confirma = $sheet->getCell($celula)->getValue(); //$cellA1 recebe os dados da célula A1

			$celula = "B" . $i;
			$codigo = $sheet->getCell($celula)->getValue(); //$cellA1 recebe os dados da célula A1

			if ($confirma!='' && $confirma!='Descartada') {
			 	$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
			        WHERE tbl_ite_cobertura_numero_id ='$numero_cobertura' AND 
			              tbl_ite_cobertura_codigo_animal='$codigo'");

			    $num_rows = mysqli_num_rows($tbl_item_cobertura);

			    if ($num_rows!=0) {
				    $reg_item = mysqli_fetch_object($tbl_item_cobertura);
				    $codigo_id_animal = $reg_item->tbl_ite_cobertura_codigo_id_animal;

					$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
					    WHERE tbl_animal_codigo_id = '$codigo_id_animal'");

					$reg_animal = mysqli_fetch_object($tbl_animal); 
					$codigo_alfa_consulta = $reg_animal->tbl_animal_codigo_alfa;
					$codigo_numerico_consulta = $reg_animal->tbl_animal_codigo_numerico;

					if ($codigo_alfa_consulta==''){
						$codigo_edi = $codigo_numerico_consulta; 
					}
					else {
						$codigo_edi = $codigo_alfa_consulta.'-'.$codigo_numerico_consulta; 
					}

					$sql = "INSERT INTO tbl_cobertura (
						tbl_cobertura_controle,
						tbl_cobertura_data,
						tbl_cobertura_codigo_local,
						tbl_cobertura_codigo_grupo,
						tbl_cobertura_codigo_estacao_monta,
						tbl_cobertura_protocoloiatf,
						tbl_cobertura_qtd_animais,
						tbl_cobertura_filtros,
						tbl_cobertura_incluido_em,
						tbl_cobertura_incluido_por,
						tbl_cobertura_alterado_em,
						tbl_cobertura_alterado_por,
						tbl_cobertura_lixeira,
						tbl_cobertura_lixeira_em,
						tbl_cobertura_lixeira_por,
						tbl_cobertura_filtro_vacas_paridas,
						tbl_cobertura_filtro_data_paridas,
						tbl_cobertura_filtro_vacas_solteiras,
						tbl_cobertura_filtro_novilhas,
						tbl_cobertura_filtro_idade_de,
						tbl_cobertura_filtro_idade_ate,
						tbl_cobertura_filtro_peso_acima,
						tbl_cobertura_planilha_processada
						) VALUES (
						    'M',
							'$data_selecao',
							'$local',
							0,
							0,
							0,
							1,
							'',
							'$data_sistema',
							'$nomeusuario',
							null,
							null,
							0,
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							''
						)";
								
					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
					   	header('Content-type: application/json');
					   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar a fêmea na cobertura'. $erro_mysql));
						mysqli_close($conector);
						exit;
					} 

					$cobertura_id = mysqli_insert_id($conector);
					$cobertura_id = str_pad($cobertura_id, 9, "0", STR_PAD_LEFT);

					$sql = ("UPDATE tbl_cobertura SET 
						    		tbl_cobertura_codigo_estacao_monta='$cobertura_id'
						    WHERE tbl_cobertura_id ='$cobertura_id'");

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado) {
					   	header('Content-type: application/json');
					   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro no ajuste do código da estação (monta natural) no cadastro de cobertura' . $erro_mysql));
						mysqli_close($conector);
						exit;
					}

					$sql = "INSERT INTO tbl_item_cobertura (
										tbl_ite_cobertura_numero_id,
										tbl_ite_cobertura_numero_item,
										tbl_ite_cobertura_codigo_id_animal,
										tbl_ite_cobertura_codigo_animal,
										tbl_ite_cobertura_codigo_alfa,
										tbl_ite_cobertura_codigo_numerico,
										tbl_ite_cobertura_data_emissao,
										tbl_ite_cobertura_codigo_touro_semen,
										tbl_ite_cobertura_lote_semen,
										tbl_ite_cobertura_data_diagnostico,
										tbl_ite_cobertura_resultado_diagnostico,
										tbl_ite_cobertura_nome_inseminador,
										tbl_ite_cobertura_destino,
										tbl_ite_cobertura_dia_1,
										tbl_ite_cobertura_dia_2,
										tbl_ite_cobertura_dia_3,
										tbl_ite_cobertura_dia_4,
										tbl_ite_cobertura_dia_5,
										tbl_ite_cobertura_dia_6,
										tbl_ite_cobertura_observacao,
										tbl_ite_cobertura_numero_cobertura,
										tbl_ite_cobertura_data_prenhes,
										tbl_ite_cobertura_previsao_parto
										)
						VALUES ('$cobertura_id', 
							1,
							'$codigo_id_animal',
							'$codigo_edi',
							'$codigo_alfa_consulta',
							'$codigo_numerico_consulta',
							'$data_selecao',
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							0,
							null,
							null
						)";

					$resultado = mysqli_query($conector, $sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado) {
						header('Content-type: application/json');
						echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o item do registro da cobertura'. $erro_mysql));
						mysqli_close($conector);
						exit;
					}
				}
			}
		}

	    $sql = ("DELETE FROM tbl_item_cobertura 
	       	WHERE tbl_ite_cobertura_numero_id='$numero_cobertura'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao excluir os itens da lista de cobertura em excel' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		$sql = ("DELETE FROM tbl_cobertura 
			WHERE tbl_cobertura_id ='$numero_cobertura'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao excluir a lista de cobertura em excel' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		unlink($uploadfile);
		mysqli_close($conector);

		$mensagem_erro = 'Planilha processada com sucesso';

		echo '
			<script type="text/javascript">

				location.href="form_selecao_matrizes.php";
			</script>';


	}

?>