<?php
include "conecta_mysql.inc";

for ($i=1; $i<=20; $i++){
    $valor[$i]=0;
}

$codigo_categoria = 0;
$tem_categoria = 'N';
$tem_raca = 'N';
$tem_pai = 'N';
$tem_mae = 'N';
$tem_sexo = 'S';
$tem_data_nasc = 'N';
$tem_peso_nasc = 'N';
$tem_peso_desmama = 'N';
$tem_peso_ult = 'N';

$id_animal = $_POST['id_animal'];  
$local_filtro = $_POST['local'];  
$categoria_filtro = $_POST['categoria']; 
$sexo_filtro = $_POST['sexo'];  
$raca_filtro = $_POST['raca'];  
$pai_filtro = $_POST['pai'];  
$mae_filtro = $_POST['mae'];  
$data_nasc_inicial = $_POST['data_nasc_inicial'];
$data_nasc_final = $_POST['data_nasc_final'];
$peso_nasc_inicial = $_POST['peso_nasc_inicial'];
$peso_nasc_final = $_POST['peso_nasc_final'];
$peso_desmama_inicial = $_POST['peso_desmama_inicial'];
$peso_desmama_final = $_POST['peso_desmama_final'];
$peso_ult_inicial = $_POST['peso_ult_inicial'];
$peso_ult_final = $_POST['peso_ult_final'];

$codigo_numerico = substr($id_animal, -9);

if (strlen($id_animal)!=9){
	$data = explode("-", $id_animal);
	$codigo_alfa = $data[0];
}
else {
	$codigo_alfa = '';
}

$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
                                WHERE tbl_animal_codigo_alfa='$codigo_alfa' AND 
                                      tbl_animal_codigo_numerico='$codigo_numerico' AND
                                      tbl_animal_ativo='S' AND 
                                      tbl_animal_lixeira=0");

$num_rows = mysqli_num_rows($tbl_animal);

if ($num_rows!=0) {
	$reg_animal = mysqli_fetch_object($tbl_animal);

	$nascimento = new DateTime($reg_animal->tbl_animal_data_nascimento);
	$raca = $reg_animal->tbl_animal_codigo_raca;
	$pelagem = $reg_animal->tbl_animal_codigo_pelagem;
	$local = $reg_animal->tbl_animal_codigo_fazenda;
	$sexo_animal = $reg_animal->tbl_animal_sexo;
	$pai_animal = $reg_animal->tbl_animal_codigo_pai;
	$mae_animal = $reg_animal->tbl_animal_codigo_mae;
	$data_nasc_animal = $reg_animal->tbl_animal_data_nascimento;
	$peso_nasc_animal = $reg_animal->tbl_animal_primeiro_peso;
	$peso_desmama_animal = $reg_animal->tbl_animal_peso_desmama;
	$peso_ult_animal = $reg_animal->tbl_animal_ultimo_peso;
	$em_estacao_monta = $reg_animal->tbl_animal_selecioanada_reproducao;

	if ($reg_animal->tbl_animal_sexo=='M') {
		$sexo = 'Macho';
	}
	else {
		$sexo = 'Fêmea';
	}

	if ($local!=$local_filtro) {
		$valor[0]='Nao tem animal';
		$valor[1]='Animal não consta no local ou Id não cadastrado.';
		$str=$valor[0] . '<|>' . $valor[1] . '<|>';
		echo $str; 
		mysqli_close($conector);
		exit;	
	}

	if ($sexo_filtro!='Todos' && $sexo_filtro!=$sexo_animal) {
		$tem_sexo='N';
	}

    $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
    $data_acompanhamento_calculo = date("Y-m-d");
    $date = new DateTime($data_nascimento); // Data de Nascimento
    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

    /*$data_inicial = $reg_animal->tbl_animal_data_nascimento;
    $data_final = date("Y-m-d");
    $diferenca = strtotime($data_final) - strtotime($data_inicial);
    $idade = floor($diferenca / (60 * 60 * 24 * 30));
    $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);*/

    $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");

    $num_rows = mysqli_num_rows($categoria); 

    if ($num_rows!=0) {
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
            }
        }
    }                   

    if ($categoria_filtro!='') {
		foreach ($categoria_filtro as $value) {
	        if ($value==$codigo_categoria) {
	        	$tem_categoria = 'S';
	        }
	    }
    }

    if ($raca_filtro!='') {
		foreach ($raca_filtro as $value) {
	        if ($value==$raca) {
	        	$tem_raca = 'S';
	        }
	    }
    }

    if ($pai_filtro!='') {
		foreach ($pai_filtro as $value) {
	        if ($value==$pai_animal) {
	        	$tem_pai = 'S';
	        }
	    }
    }

    if ($mae_filtro!='') {
		foreach ($mae_filtro as $value) {
	        if ($value==$mae_animal) {
	        	$tem_mae = 'S';
	        }
	    }
    }

    if ($data_nasc_animal>=$data_nasc_inicial && $data_nasc_animal<=$data_nasc_final) {
    	$tem_data_nasc='S';
    }

    if ($peso_nasc_animal>=$peso_nasc_inicial  && $peso_nasc_animal<=$peso_nasc_final) {
    	$tem_peso_nasc='S';
    }

    if ($peso_desmama_animal>=$peso_desmama_inicial  && 
    	$peso_desmama_animal<=$peso_desmama_final) {
    	$tem_peso_desmama='S';
    }

    if ($peso_ult_animal>=$peso_ult_inicial  && 
    	$peso_ult_animal<=$peso_ult_final) {
    	$tem_peso_ult='S';
    }

	$tbl_raca = mysqli_query($conector, "SELECT * FROM tabela_racas
	                        WHERE tab_codigo_raca='$raca'");
	$num_rows = mysqli_num_rows($tbl_raca);

	if ($num_rows!=0) {
		$reg_raca = mysqli_fetch_object($tbl_raca);
		$desc_raca = $reg_raca->tab_descricao_raca;
	}
	else {
		$desc_raca = '';
	}

	$tbl_pelagem = mysqli_query($conector, "SELECT * FROM tabela_pelagens
	                        WHERE tab_codigo_pelagem='$pelagem'");
	$num_rows = mysqli_num_rows($tbl_pelagem);

	if ($num_rows!=0) {
		$reg_pelagem = mysqli_fetch_object($tbl_pelagem);
		$desc_pelagem = $reg_pelagem->tab_descricao_pelagem;
	}
	else {
		$desc_pelagem = '';
	}

	$tbl_mae = mysqli_query($conector, "SELECT * FROM tbl_animais
	                        WHERE tbl_animal_codigo_id='$reg_animal->tbl_animal_codigo_mae'");
	$num_rows = mysqli_num_rows($tbl_mae);

	if ($num_rows!=0) {
		$reg_mae = mysqli_fetch_object($tbl_mae);
		$codigo_mae = $reg_mae->tbl_animal_codigo_alfa . $reg_mae->tbl_animal_codigo_numerico;
	}
	else {
		$codigo_mae = '';
	}

    $descricao_animal = $sexo . ' - Nasc: ' . $nascimento->format('d/m/Y') . ' - '. $desc_raca . ' ' . $desc_pelagem . ' - Mãe: ' . $codigo_mae;

    $valor[0] = $reg_animal->tbl_animal_codigo_id;
    $valor[1] = $sexo;
    $valor[2] = $nascimento->format('d/m/Y');
	$valor[3] = $desc_raca;
	$valor[4] = $desc_pelagem;
	$valor[5] = $codigo_mae;
	$valor[6] = $descricao_animal;
	$valor[7] = $tem_categoria;
	$valor[8] = $tem_sexo;
	$valor[9] = $tem_raca;
	$valor[10] = $tem_pai;
	$valor[11] = $tem_mae;
	$valor[12] = $tem_data_nasc;
	$valor[13] = $tem_peso_nasc;
	$valor[14] = $tem_peso_desmama;
	$valor[15] = $tem_peso_ult;
	$valor[16] = $codigo_categoria;
	$valor[17] = $desc_categoria;
	$valor[18] = $em_estacao_monta;

	$str=$valor[0] . '<|>';

	for ($i=1; $i<=20; $i++){
	    $str.=$valor[$i] . '<|>';
	}
	echo $str; 
	mysqli_close($conector);
	exit;

}					

$valor[0]='Nao tem animal';
$valor[1]='Animal não cadastrado com esse código.';
$str=$valor[0] . '<|>' . $valor[1] . '<|>';
echo $str; 
mysqli_close($conector);
?>