<?php
include "conecta_mysql.inc";

$data_hoje = date('Y-m-d');

@ session_start();
$codigo_usuario = $_SESSION['id_usuario'];

$tbl_usuario = "SELECT * FROM usuario 
	WHERE id_usuario = '$codigo_usuario' AND 
          lixeira_usuario=0 ";  
$query = mysqli_query($conector_acesso, $tbl_usuario);

$num_rows_usuario = mysqli_num_rows($query);

if ($num_rows_usuario!=0){
	$reg_usuario = mysqli_fetch_assoc($query);

	$array_locais_usuario = explode(',', $reg_usuario['local_usuario']);
	$qtd_locais_usuario = count($array_locais_usuario);

	if ($qtd_locais_usuario==0) {
		$array_locais_usuario='';
	}
}
else {
	$array_locais_usuario='';
}

$periodo_de = $_POST['periodo_de'];
$periodo_ate = $_POST['periodo_ate'];

//$periodo_de = '2024-01-02';
//$periodo_ate = '2025-06-03';

foreach ($array_locais_usuario as $value) {
	$value = ltrim($value);
	$value = rtrim($value);

	$fazendas = mysqli_query($conector, "SELECT * FROM tbl_pessoa
        WHERE tbl_pessoa_id='$value' AND 
              tbl_pessoa_lixeira=0");

	$num_rows_pessoa = mysqli_num_rows($fazendas);	

	if ($num_rows_pessoa!=0) {
		while ($reg_fazenda = mysqli_fetch_object($fazendas)) {
			$id_fazenda = $reg_fazenda->tbl_pessoa_id;
        	$desc_fazenda[$id_fazenda]=$reg_fazenda->tbl_pessoa_nome;
        	$qtd_animais[$id_fazenda]=0;
        	$total_cobertura[$id_fazenda]=0;
        	$total_prenhas[$id_fazenda]=0;
        	$per_prenhez[$id_fazenda]=0;
        	$falta_diagnostico[$id_fazenda]=0;

   			$animal_anterior = 0;

   			/*if ($id_fazenda==77) {
   				echo 'Inicio: ' . $qtd_animais[$id_fazenda] . ' animais, ' . $total_prenhas[$id_fazenda] . ' Prenhes </br>';
   			}*/

			$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
				INNER JOIN tbl_cobertura
			            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id

			    WHERE tbl_cobertura_lixeira=0 AND
			          tbl_cobertura_controle = 'M' AND
			          tbl_cobertura_data>='$periodo_de' AND
                      tbl_cobertura_data<='$periodo_ate' AND 
			          tbl_cobertura_codigo_local='$id_fazenda'
			        
			    ORDER BY tbl_ite_cobertura_codigo_id_animal ASC"); 

			$num_rows = mysqli_num_rows($tbl_item_cobertura);

			if ($num_rows!=0) {
			    while ($reg_cobertura = mysqli_fetch_object($tbl_item_cobertura)){
			        $codigo_id_animal = $reg_cobertura->tbl_ite_cobertura_codigo_id_animal;

			        $codigo_numerico = $reg_cobertura->tbl_ite_cobertura_codigo_numerico;
		
					$diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

   					/*if ($id_fazenda==77) {
	                	echo 'Animal: ' . $codigo_numerico . ' diagnostico: ' . $diagnostico . '</br>';
	                }*/

            		if ($codigo_id_animal!=$animal_anterior) {
                		$qtd_animais[$id_fazenda]++;
                		$animal_anterior=$codigo_id_animal;

   						/*if ($id_fazenda==77) {
                			echo 'Qtd: ' . $qtd_animais[$id_fazenda] . '</br>';
                		}*/
			       	}

			       	$total_cobertura[$id_fazenda]++;


			       	if ($diagnostico=='P') {
			       		$total_prenhas[$id_fazenda]++;

   						/*if ($id_fazenda==77) {
				       		echo 'Prenhes: ' . $total_prenhas[$id_fazenda] . '</br>';
				       	}*/

			       		$taxa_prenhez = 
			       		    ($total_prenhas[$id_fazenda]/$qtd_animais[$id_fazenda])*100;

			        	$per_prenhez[$id_fazenda] = number_format($taxa_prenhez,2,',','.');
			        }

			        if ($diagnostico==''){
			        	$falta_diagnostico[$id_fazenda]++;
			        }
			   	} 
			}
        }
    }
}

$desc_fazenda = implode("|", $desc_fazenda);
$qtd_animais = implode("|", $qtd_animais);
$total_cobertura = implode("|", $total_cobertura);
$total_prenhas = implode("|", $total_prenhas);
$per_prenhez = implode("|", $per_prenhez);
$falta_diagnostico = implode("|", $falta_diagnostico);

$valor[0]= $desc_fazenda;
$valor[1]= '';
$valor[2]= $qtd_animais;
$valor[3]= $total_cobertura;
$valor[4]= $total_prenhas;
$valor[5]= $per_prenhez;
$valor[6]= $falta_diagnostico;

$str=$valor[0] . '<|>' . $valor[1] . '<|>' . $valor[2] . '<|>' . 
     $valor[3] . '<|>' . $valor[4] . '<|>' . $valor[5] . '<|>' . 
     $valor[6];
echo $str; 
?>