<?php 
	include "conecta_mysql.inc";

	$mensagem_erro = '';
	$data_sistema = date("Y-m-d H:i:s");


	$uploaddir = 'planilhas_excel/';
	$uploadfile = $uploaddir . basename($_FILES['arquivo_excel']['name']);

    move_uploaded_file($_FILES['arquivo_excel']['tmp_name'], $uploadfile);

	require 'vendor/autoload.php'; //autoload do projeto

	use PhpOffice\PhpSpreadsheet\IOFactory; //classe responsável pelo load dos arquivos de planilha
	use PhpOffice\PhpSpreadsheet\Spreadsheet; //classe responsável pela manipulação da planilha

	$spreadsheet = IOFactory::load($uploadfile); //carregando a planilha spreadsheet2 em um objeto PHP

	$sheet = $spreadsheet->getActiveSheet(); //retornando a aba ativa

	$local = $sheet->getCell('A1')->getValue();

	if (!$local) {
		echo 'Não achei a planilhas_excel';
		mysqli_close($conector);
		exit;
	}

	$numero_linhas = 51;
	$linhas_lidas = 0;

	for ($i=1; $i <=$numero_linhas; $i++) { 
		$celula = "A" . $i;
		$local = $sheet->getCell($celula)->getValue();

		$celula = "B" . $i;
		$item = $sheet->getCell($celula)->getValue();

		$celula = "D" . $i;
		$nascimento = $sheet->getCell($celula)->getValue();
		$nascimento = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($nascimento));

		$celula = "E" . $i;
		$categoria = $sheet->getCell($celula)->getValue();

		$celula = "F" . $i;
		$sexo = $sheet->getCell($celula)->getValue();

		echo $local.' '.$item.' '.$categoria.' '.$nascimento.' '.$sexo.'</br>'; 

		$linhas_lidas++;

		$sql = "UPDATE tbl_animal_pasto SET
			tbl_animal_pasto_nascimento='$nascimento',
			tbl_animal_pasto_categoria='$categoria'
			WHERE tbl_animal_pasto_local ='$local' AND 
				  tbl_animal_pasto_numero_item ='$item'";

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			echo ' ERRO: ' . $erro_mysql . '</br>';
		} 

	 		/*$rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
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
		}*/
	}


	//unlink($uploadfile);
	echo 'Registros encontrados: ' . $linhas_lidas;

	mysqli_close($conector);

?>