<?php
include "conecta_mysql.inc";

@ session_start(); 
$controle_estoque = $_SESSION['controle_estoque'];

for ($i=1; $i<=20; $i++){
    $valor[$i]=0;
}

$local = $_POST['local'];  
$pasto = $_POST['pasto'];  
$categoria_animal = $_POST['codigo_categoria']; 
$sexo_animal = $_POST['sexo'];  
$tem_categoria='N';
$sexo = '';
$desc_categoria = '';

if ($pasto==0) {
	$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
				INNER JOIN tbl_pasto
				        ON tbl_pasto_id = tbl_animal_pasto_id
		             WHERE tbl_animal_pasto_local='$local' AND 
		                   tbl_pasto_tipo_curral='S' AND 
		                   tbl_animal_pasto_situacao='A'");
}
else {
	$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
				INNER JOIN tbl_pasto
				        ON tbl_pasto_id = tbl_animal_pasto_id
		             WHERE tbl_animal_pasto_local='$local' AND 
		                   tbl_animal_pasto_id='$pasto' AND 
		                   tbl_animal_pasto_situacao='A'");
}

$num_rows = mysqli_num_rows($tbl_pasto); 

$codigo_categoria = 0;

if($num_rows !=0){
	while ($reg_animal = mysqli_fetch_object($tbl_pasto)) {
		$data_nascimento = $reg_animal->tbl_animal_pasto_nascimento;
		$sexo = $reg_animal->tbl_animal_pasto_sexo;
		//$codigo_categoria = $reg_animal->tbl_animal_pasto_categoria;
	    $data_acompanhamento_calculo = date("Y-m-d");
	    $date = new DateTime($data_nascimento); 
	    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
	    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
	    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
	    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

		if ($controle_estoque=='I') {
			/*if ($codigo_categoria==$categoria_animal && $sexo==$sexo_animal) {
				$tem_categoria = 'S';
			}*/

		    $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
		            WHERE tab_registro_lixeira_categoria_idade='0'");

		    $num_rows_categoria = mysqli_num_rows($categoria); 

		    if ($num_rows_categoria!=0) {
		        while ($reg_categoria = mysqli_fetch_object($categoria)) {
		            $idade_de = $reg_categoria->tab_categoria_idade_de;
		            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

		            if ($idade >= $idade_de && $idade <= $idade_ate) {
		            	if ($idade_ate==999999999){
			                $desc_categoria= '> 36 meses';
		            	}
		            	else {
			                $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
		            	}

		                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

						if ($codigo_categoria==$categoria_animal && $sexo==$sexo_animal) {
							$tem_categoria = 'S';
						}	
					}
				}
			}
		}
		else {
		    $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
		            WHERE tab_registro_lixeira_categoria_idade='0'");

		    $num_rows_categoria = mysqli_num_rows($categoria); 
			$codigo_categoria = 0;

		    if ($num_rows_categoria!=0) {
		        while ($reg_categoria = mysqli_fetch_object($categoria)) {
		            $idade_de = $reg_categoria->tab_categoria_idade_de;
		            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

		            if ($idade >= $idade_de && $idade <= $idade_ate) {
		            	if ($idade_ate==999999999){
			                $desc_categoria= '> 36 meses';
		            	}
		            	else {
			                $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
		            	}

		                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

						if ($codigo_categoria==$categoria_animal && $sexo==$sexo_animal) {
							$tem_categoria = 'S';
						}		
		            }
		        }
		    }                   
		}
	}
}

$valor[0]=$tem_categoria;
$valor[1]=$sexo;
$valor[2]=$sexo_animal;
$valor[3]=$desc_categoria;
$valor[4]=$codigo_categoria;
$str=$valor[0] . '<|>' . $valor[1] . '<|>' . $valor[2] . '<|>' . $valor[3] . 
'<|>'. $valor[4] . '<|>';
echo $str; 
mysqli_close($conector);
?>