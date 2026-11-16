<?php
include "conecta_mysql.inc";

@ session_start(); 
$controle_estoque = $_SESSION['controle_estoque'];

for ($i=1; $i<=20; $i++){
    $valor[$i]=0;
}

$tem_registros = 'N';

$desc_categoria = [];
$arrayCategorias = [];
$descricaoCategorias = [];

$sql = "SELECT * FROM tabela_categoria_idade 
    WHERE tab_registro_lixeira_categoria_idade='0'"; 
        
$rs = mysqli_query($conector,$sql); 

while ($fila = mysqli_fetch_object($rs)){
    $codigo_id = $fila->tab_codigo_categoria_idade;
    $idade_de = $fila->tab_categoria_idade_de;
    $idade_ate = $fila->tab_categoria_idade_ate;

    if ($idade_ate==999999999){
        array_push($desc_categoria, '> 36 m');
            $descricaoCategorias = [
                "id" => $codigo_id,
                "idade_de" => $idade_de,
                "idade_ate" => $idade_ate
        ];
        array_push($arrayCategorias, $descricaoCategorias);
    }
    else {
        array_push($desc_categoria, $idade_de . ' a ' . $idade_ate . ' m');
        $descricaoCategorias = [
            "id" => $codigo_id,
            "idade_de" => $idade_de,
            "idade_ate" => $idade_ate
        ];
        array_push($arrayCategorias, $descricaoCategorias);
    }
}

$codigo_pesagem = $_POST['pesagem_id'];  
$tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_pesagem
    WHERE tbl_pesagem_id='$codigo_pesagem'");

$num_rows = mysqli_num_rows($tbl_pesagem);

if ($num_rows!=0) {
	$reg_pesagem = mysqli_fetch_object($tbl_pesagem);

	$data_inclusao = new DateTime($reg_pesagem->tbl_pesagem_data);
    $epoca = $reg_pesagem->tbl_pesagem_codigo_epoca;
    $local = $reg_pesagem->tbl_pesagem_codigo_local;

	$tbl_epoca = mysqli_query($conector, "SELECT * FROM tabela_epoca_pesagem
		WHERE tab_codigo_epoca_pesagem ='$epoca'");
	$num_rows_epoca = mysqli_num_rows($tbl_epoca);

	if ($num_rows_epoca!=0){
	    $reg_epoca = mysqli_fetch_object($tbl_epoca);
		$valor[10]= $reg_epoca->tab_descricao_epoca_pesagem;   
	}
	else {
		$valor[10]= '';
	}

    $valor[0] = $reg_pesagem->tbl_pesagem_filtros;
    $valor[1] = $reg_pesagem->tbl_pesagem_lote;
    $valor[2] = $data_inclusao->format('d/m/Y');
    $valor[3] = $reg_pesagem->tbl_pesagem_qtd_animais_pesados;
    $valor[4] = $reg_pesagem->tbl_pesagem_peso_kg;
    $valor[5] = $reg_pesagem->tbl_pesagem_peso_arroba;
    $valor[6] = $reg_pesagem->tbl_pesagem_peso_medio_kg;
    $valor[7] = $reg_pesagem->tbl_pesagem_peso_medio_arroba;
    $valor[9] = $reg_pesagem->tbl_pesagem_codigo_epoca;
    $valor[10] = $reg_pesagem->tbl_pesagem_data;
    $valor[11] = $reg_pesagem->tbl_pesagem_qtd_animais_a_pesar;
    $valor[12] = $reg_pesagem->tbl_pesagem_criterios_apartacao;

	$codigos_pais = [];

    $sql = mysqli_query($conector, "SELECT * from tbl_animais
    	WHERE tbl_animal_codigo_fazenda = '$local' AND 
    	      tbl_animal_ativo = 'S'");

    while ($reg_animal = mysqli_fetch_object($sql)) {
    	if ($reg_animal->tbl_animal_codigo_pai) {
	        $codigos_pais[] = $reg_animal->tbl_animal_codigo_pai;
	    }
    }

    $dados_pais_semem = [];
    $dados_pais_animal = [];

    if (!empty($codigos_pais)) {
        $sql_pais_semem = "SELECT tbl_semem_codigo_id, tbl_semem_nome FROM tbl_semem WHERE tbl_semem_codigo_id IN (" . implode(',', $codigos_pais) . ")";

        $rs_pais_semem = mysqli_query($conector, $sql_pais_semem);

        while ($reg_pai_semem = mysqli_fetch_object($rs_pais_semem)) {
            $dados_pais_semem[$reg_pai_semem->tbl_semem_codigo_id] = $reg_pai_semem->tbl_semem_nome;
        }

        $sql_pais_animal = "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa, tbl_animal_codigo_numerico FROM tbl_animais WHERE tbl_animal_codigo_id IN (" . implode(',', $codigos_pais) . ")";

        $rs_pais_animal = mysqli_query($conector, $sql_pais_animal);

        while ($reg_pai_animal = mysqli_fetch_object($rs_pais_animal)) {
            $dados_pais_animal[$reg_pai_animal->tbl_animal_codigo_id] = $reg_pai_animal->tbl_animal_codigo_alfa . ' ' . intval($reg_pai_animal->tbl_animal_codigo_numerico);
        }
    }

	$numero_do_item=0;
	$matriz_itens= array();

	$rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
		INNER JOIN tbl_animais
		        ON tbl_animal_codigo_id = tbl_ite_pesagem_codigo_id_animal
		WHERE tbl_ite_pesagem_numero_id='$codigo_pesagem'");

	$num_rows = mysqli_num_rows($rs);

	if ($num_rows!=0){
	    while ($reg_item = mysqli_fetch_object($rs)){
            $ultimo_peso = 0; 
			$data_ultimo_peso = 0;


	    	if ($reg_item->tbl_ite_pesagem_ultimo_peso=='' || $reg_item->tbl_ite_pesagem_ultimo_peso==0) {
		    	$diferencaPeso = (int)$reg_item->tbl_ite_pesagem_peso - 
		    					 (int)$reg_item->tbl_animal_ultimo_peso;
	            if ($reg_item->tbl_animal_ultimo_peso!=0 && 
	            	$reg_item->tbl_animal_ultimo_peso!='') {
	                $ultimo_peso = (int)$reg_item->tbl_animal_ultimo_peso;
	                $data_ultimo_peso = $reg_item->tbl_animal_data_ultimo;
	            }
	            else if ($reg_item->tbl_animal_peso_desmama!=0 && 
	            	     $reg_item->tbl_animal_peso_desmama!='') {
	                $ultimo_peso = (int)$reg_item->tbl_animal_peso_desmama;
	                $data_ultimo_peso = $reg_item->tbl_animal_data_desmama;
	            }
	            else if ($reg_item->tbl_animal_primeiro_peso!=0 && 
	            		 $reg_item->tbl_animal_primeiro_peso!=''){
	                $ultimo_peso = (int)$reg_item->tbl_animal_primeiro_peso;
	                $data_ultimo_peso = $reg_item->tbl_animal_data_primeiro_peso;
	            }
	    	}
	    	else {
	    		$diferencaPeso = (int)$reg_item->tbl_ite_pesagem_peso - 
	    					     (int)$reg_item->tbl_ite_pesagem_ultimo_peso;
                $ultimo_peso = (int)$reg_item->tbl_ite_pesagem_ultimo_peso;
                $data_ultimo_peso = $reg_item->tbl_ite_pesagem_data_emissao;
	    	}

            $data = new DateTime($data_ultimo_peso);
			$data_ultimo_peso_edi =  $data->format('d/m/Y');

		    $data_nasc = $reg_item->tbl_animal_data_nascimento;
		    $idade_meses = 0;
		    if ($data_nasc && $data_nasc != '0000-00-00') {
		        $nascimento = new DateTime($data_nasc);
		        $hoje = new DateTime();
		        $intervalo = $hoje->diff($nascimento);
		        $idade_meses = ($intervalo->y * 12) + $intervalo->m;
		    }

	        for($i = 0; $i < count($arrayCategorias); $i++){
	            $id_categoria = $arrayCategorias[$i]['id'];
	            $idade_de = $arrayCategorias[$i]['idade_de'];
	            $idade_ate = $arrayCategorias[$i]['idade_ate'];

	            if ($idade_meses >= $idade_de && $idade_meses <= $idade_ate) {
	                $codigo_categoria = $id_categoria;

	                if ($idade_ate==999999999) {
	                    $descricao_categoria=' > 36 meses';
	                }
	                else {
	                    $descricao_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
	                }
	            }
	        }                        

	    	$id_pai_animal = $reg_item->tbl_animal_codigo_pai;
	        $codigo_pai_alfa_numerico = '';

	        if (isset($dados_pais_semem[$id_pai_animal])) {
	            $codigo_pai_alfa_numerico = $dados_pais_semem[$id_pai_animal];
	        } 
	        elseif (isset($dados_pais_animal[$id_pai_animal])) {
	            $codigo_pai_alfa_numerico = $dados_pais_animal[$id_pai_animal];
	        }

			$valor_item[0]=$reg_item->tbl_ite_pesagem_codigo_animal;
			$valor_item[1]=(int)$reg_item->tbl_ite_pesagem_peso;
			$valor_item[2]=$reg_item->tbl_ite_pesagem_sexo;
			$valor_item[3]=$reg_item->tbl_ite_pesagem_nascimento;
			$valor_item[4]=$reg_item->tbl_ite_pesagem_raca;
			$valor_item[5]=$reg_item->tbl_ite_pesagem_pelagem;
			$valor_item[6]=$reg_item->tbl_ite_pesagem_mae;
			$valor_item[7]=$reg_item->tbl_ite_pesagem_observacao;
			$valor_item[8]=$reg_item->tbl_ite_pesagem_codigo_id_animal;;
			$valor_item[9]=$reg_item->tbl_ite_pesagem_numero_item;
			$valor_item[10]=$reg_item->tbl_ite_pesagem_categoria;
			$valor_item[11]=$reg_item->tbl_ite_pesagem_peso_medio;
			$valor_item[12]=$reg_item->tbl_ite_pesagem_arroba;
			$valor_item[13]=$reg_item->tbl_ite_pesagem_arroba_media;
			$valor_item[14]=$reg_item->tbl_ite_pesagem_qtd_animais;
			$valor_item[15]=$reg_item->tbl_ite_pesagem_criterio_apartacao;
			$valor_item[16]=$diferencaPeso;
			$valor_item[17]=$ultimo_peso;
			$valor_item[18]=$data_ultimo_peso_edi;
			$valor_item[19]=$idade_meses;
			$valor_item[20]=$descricao_categoria;
			$valor_item[21]=$reg_item->tbl_animal_observacao;
			$valor_item[22]=$codigo_pai_alfa_numerico;
			$valor_item[24]=$reg_item->tbl_ite_pesagem_mens_repetido;
			$valor_item[25]=$reg_item->tbl_ite_pesagem_id_repetido;

			// Verifica se o animal esta repetido em outra lista de pesagem sem finalizar

			$animal_id = $reg_item->tbl_ite_pesagem_codigo_id_animal;

			$sql = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
				INNER JOIN tbl_pesagem
				        ON tbl_pesagem_id = tbl_ite_pesagem_numero_id
				WHERE tbl_pesagem_codigo_local='$local' AND 
					  tbl_pesagem_finalizada='N' AND 
					  tbl_ite_pesagem_codigo_id_animal='$animal_id'");

			$valor_item[23] = mysqli_num_rows($sql);

			$itens[$numero_do_item] = 
				$valor_item[0] . '|' . $valor_item[1] . '|' . $valor_item[2] . '|' .
				$valor_item[3] . '|' . $valor_item[4] . '|' . $valor_item[5] . '|' .
			 	$valor_item[6] . '|' . $valor_item[7] . '|' . $valor_item[8] . '|' .
			 	$valor_item[9] . '|' . $valor_item[10] . '|' . $valor_item[11] . '|' . 
			 	$valor_item[12] . '|' . $valor_item[13] . '|' . $valor_item[14] . '|' . 
			 	$valor_item[15]. '|' . $valor_item[16]. '|' . $valor_item[17]. '|' . 
			 	$valor_item[18]. '|' . $valor_item[19]. '|' . $valor_item[20]. '|' . 
			 	$valor_item[21]. '|' . $valor_item[22]. '|' . $valor_item[23]. '|' . 
			 	$valor_item[24]. '|' . $valor_item[25];
					
			array_push($matriz_itens, $itens[$numero_do_item]);
			$numero_do_item++;
	    }

		$matriz_com_itens = implode("<!>", $matriz_itens);
	}
	else {
		$matriz_com_itens = 0;
	}

	$valor[8] = $matriz_com_itens;

	$str=$valor[0] . '<|>';

	for ($i=1; $i<=20; $i++){
	    $str.=$valor[$i] . '<|>';
	}
	echo $str; 
	mysqli_close($conector);
	exit;
}					

$valor[0]=999999999;
$valor[1]='Pesagem não cadastrada.';
$str=$valor[0] . '<|>' . $valor[1] . '<|>';
echo $str; 
mysqli_close($conector);
?>