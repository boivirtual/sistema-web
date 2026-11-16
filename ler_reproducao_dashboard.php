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

if (isset($_POST['estacao'])) {
	$estacao = $_POST['estacao'];
}
else {
	$estacao = 0;
}

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

            $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
                WHERE tbl_par_codigo_local = '$id_fazenda' AND 
                      tbl_par_lixeira=0 AND 
                      tbl_par_estacao_nome = '$estacao'
                ORDER BY tbl_par_estacao_id DESC LIMIT 1");  

            $num_rows = mysqli_num_rows($sql);

            if ($num_rows !=0){
                $reg_estacao = mysqli_fetch_object($sql);
                $id_estacao = $reg_estacao->tbl_par_estacao_id;
                $data_inicial = $reg_estacao->tbl_par_estacao_monta_inicial;
                $data_final = $reg_estacao->tbl_par_estacao_monta_final;

				$periodo_estacao[$id_fazenda]=
				substr($data_inicial, 5, 2).'/'.
				substr($data_inicial, 0, 4).' a '.
				substr($data_final, 5, 2).'/'.
				substr($data_final, 0, 4);

    			$animal_anterior = 0;

			    $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
			        INNER JOIN tbl_cobertura
			                ON tbl_cobertura_id = tbl_ite_cobertura_numero_id

			        WHERE tbl_cobertura_lixeira=0 AND
			              tbl_cobertura_controle = 'C' AND 
			              tbl_cobertura_codigo_local='$id_fazenda' AND 
			              tbl_cobertura_codigo_estacao_monta='$id_estacao'
			        
			        ORDER BY tbl_cobertura_codigo_estacao_monta ASC, 
			                 tbl_ite_cobertura_codigo_id_animal ASC"); 

			    $num_rows = mysqli_num_rows($tbl_item_cobertura);

			    if ($num_rows!=0) {
			        while ($reg_cobertura = mysqli_fetch_object($tbl_item_cobertura)){
			            $codigo_id_animal = $reg_cobertura->tbl_ite_cobertura_codigo_id_animal;
						$diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
						$D0 = $reg_cobertura->tbl_ite_cobertura_dia_1;


            			if ($codigo_id_animal!=$animal_anterior) {
                			$qtd_animais[$id_fazenda]++;
                			$animal_anterior=$codigo_id_animal;
			        	}

			        	$total_cobertura[$id_fazenda]++;

			        	if ($diagnostico=='P') {
			        		$total_prenhas[$id_fazenda]++;

			        		$taxa_prenhez = 
			        		    ($total_prenhas[$id_fazenda]/$qtd_animais[$id_fazenda])*100;

			        		$per_prenhez[$id_fazenda] = number_format($taxa_prenhez,2,',','.');
			        	}

			        	if ($D0=='S' && $diagnostico==''){
			        		$falta_diagnostico[$id_fazenda]++;
			        	}
			   		} 
			   	}
            }
            else {
                $periodo_estacao[$id_fazenda]='';
            }
        }
    }
}

$desc_fazenda = implode("|", $desc_fazenda);
$periodo_estacao = implode("|", $periodo_estacao);
$qtd_animais = implode("|", $qtd_animais);
$total_cobertura = implode("|", $total_cobertura);
$total_prenhas = implode("|", $total_prenhas);
$per_prenhez = implode("|", $per_prenhez);
$falta_diagnostico = implode("|", $falta_diagnostico);

$valor[0]= $desc_fazenda;
$valor[1]= $periodo_estacao;
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