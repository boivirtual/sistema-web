<?php
include "conecta_mysql.inc";

$local = $_POST['local'];  

@ session_start();
$codigo_usuario = $_SESSION['id_usuario'];

$tbl_usuario = "SELECT * FROM usuario WHERE id_usuario = '$codigo_usuario' AND 
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

// CRIA E ZERA ARRAY COM O CODIGO DAS FAZENDAS
foreach ($array_locais_usuario as $value) {
	$value = ltrim($value);
	$value = rtrim($value);

    $total_fazenda[$value]=0;
    $fazenda_id[$value]=$value;
    $desc_fazenda[$value]='';
    $ultima_data[$value]=0;
    $cab_ha[$value]=0;
}

// SOMA ANIMAIS POR FAZENDA

foreach ($array_locais_usuario as $value) {
	$value = ltrim($value);

	if ($local==0) {
		$codigo_fazenda = rtrim($value);

		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
									inner join tbl_pessoa
									        on tbl_pessoa_id = tbl_animal_pasto_local 
	                                WHERE tbl_animal_pasto_local ='$codigo_fazenda' AND  tbl_animal_pasto_situacao='A'");
		$num_rows = mysqli_num_rows($tbl_pasto);	

		if ($num_rows!=0){
			while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
				$desc_fazenda[$codigo_fazenda]=$reg_pasto->tbl_pessoa_nome;
				$area_fazenda=$reg_pasto->tbl_pessoa_area_util_fazenda;

				$inclusao = $reg_pasto->tbl_animal_pasto_incluido_em;
				$alteracao = $reg_pasto->tbl_animal_pasto_alterado_em;

				if ($inclusao!='') {
					if ($inclusao>$ultima_data[$codigo_fazenda]){
						$ultima_data[$codigo_fazenda]=$inclusao;
					}
				}

				if ($alteracao!='') {
					if ($alteracao>$ultima_data[$codigo_fazenda]){
						$ultima_data[$codigo_fazenda]=$alteracao;
					}
				}

				if ($area_fazenda=='' || $area_fazenda==0) {
					$area_fazenda=1;
				}

				$total_fazenda[$codigo_fazenda]++;	
				$locacao=$total_fazenda[$codigo_fazenda]/$area_fazenda;
				$cab_ha[$codigo_fazenda]=number_format($locacao,2,',','.');

			}
		}
	}
	else if ($local==$value) {
		$codigo_fazenda = rtrim($value);

		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
									inner join tbl_pessoa
									        on tbl_pessoa_id = tbl_animal_pasto_local
	                                WHERE tbl_animal_pasto_local='$codigo_fazenda' AND
	                                      tbl_animal_pasto_situacao='A'");
		$num_rows = mysqli_num_rows($tbl_pasto);	

		if ($num_rows!=0){
			while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {

				$desc_fazenda[$codigo_fazenda]=$reg_pasto->tbl_pessoa_nome;
				$area_fazenda=$reg_pasto->tbl_pessoa_area_util_fazenda;

				$inclusao = $reg_pasto->tbl_animal_pasto_incluido_em;
				$alteracao = $reg_pasto->tbl_animal_pasto_alterado_em;

				if ($inclusao!='') {
					if ($inclusao>$ultima_data[$codigo_fazenda]){
						$ultima_data[$codigo_fazenda]=$inclusao;
					}
				}

				if ($alteracao!='') {
					if ($alteracao>$ultima_data[$codigo_fazenda]){
						$ultima_data[$codigo_fazenda]=$alteracao;
					}
				}

				$total_fazenda[$codigo_fazenda]++;	
				$locacao=$total_fazenda[$codigo_fazenda]/$area_fazenda;
				$cab_ha[$codigo_fazenda]=number_format($locacao,2,',','.');
			}
		}
	}	
}

$total_fazenda = implode("|", $total_fazenda);
$fazenda_id = implode("|", $fazenda_id);
$desc_fazenda= implode("|", $desc_fazenda);
$ultima_data= implode("|", $ultima_data);
$cab_ha = implode("|", $cab_ha);

$valor[0]= $fazenda_id;
$valor[1]= $desc_fazenda;
$valor[2]= $total_fazenda;
$valor[3]= $ultima_data;
$valor[4]= $cab_ha;

$str=$valor[0] . '<|>' . $valor[1] . '<|>' . $valor[2] . '<|>' . $valor[3] . '<|>' . $valor[4];
echo $str; 
?>