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

			// AJUSTE 01/09/2026: a coluna Prenhas passou a ser filtrada pela data de
			// PREVISAO DE PARTO (tbl_ite_cobertura_previsao_parto) dentro do periodo.
			// Antes o filtro usava o campo tbl_cobertura_data (data da monta).
			// Regras atuais deste card (Monta):
			//   - Prenhas ............ diagnostico = 'P' e previsao de parto no periodo
			//   - Femeas ............. Prenhas (previsao de parto no periodo)
			//                          + Negativas (tbl_cobertura_data no periodo)
			//   - Falta Diagnosticar . sem diagnostico e tbl_cobertura_data no periodo
			$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
				INNER JOIN tbl_cobertura
				        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id

			    WHERE tbl_cobertura_lixeira=0 AND
			          tbl_cobertura_controle = 'M' AND
			          tbl_cobertura_codigo_local='$id_fazenda' AND
			          (
			            (tbl_ite_cobertura_resultado_diagnostico = 'P' AND
			             tbl_ite_cobertura_previsao_parto >= '$periodo_de' AND
			             tbl_ite_cobertura_previsao_parto <= '$periodo_ate')
			            OR
			            (tbl_cobertura_data >= '$periodo_de' AND
			             tbl_cobertura_data <= '$periodo_ate')
			          )

			    ORDER BY tbl_ite_cobertura_codigo_id_animal ASC");

			$num_rows = mysqli_num_rows($tbl_item_cobertura);

			if ($num_rows!=0) {
			    while ($reg_cobertura = mysqli_fetch_object($tbl_item_cobertura)){
			        $codigo_id_animal = $reg_cobertura->tbl_ite_cobertura_codigo_id_animal;
			        $codigo_numerico = $reg_cobertura->tbl_ite_cobertura_codigo_numerico;
			        $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
			        $previsao_parto = $reg_cobertura->tbl_ite_cobertura_previsao_parto;
			        $data_cobertura = $reg_cobertura->tbl_cobertura_data;

			        // AJUSTE 01/09/2026: prenha = diagnostico 'P' com PREVISAO DE PARTO
			        // dentro do periodo (antes contava pela data da monta / tbl_cobertura_data)
			        $prenha_no_periodo = ($diagnostico=='P' &&
			            $previsao_parto!='' &&
			            $previsao_parto>=$periodo_de && $previsao_parto<=$periodo_ate);

			        // negativa / falta de diagnostico continuam pela data da monta
			        $cobertura_no_periodo = ($data_cobertura>=$periodo_de &&
			            $data_cobertura<=$periodo_ate);

			        $total_cobertura[$id_fazenda]++;

			        // Coluna Prenhas
			        if ($prenha_no_periodo) {
			            $total_prenhas[$id_fazenda]++;
			        }

			        // Coluna Femeas: prenhas (previsao de parto no periodo)
			        //                + negativas (data da monta no periodo)
			        if ($prenha_no_periodo || ($diagnostico=='N' && $cobertura_no_periodo)) {
			            $qtd_animais[$id_fazenda]++;
			        }

			        // Coluna Falta Diagnosticar: sem diagnostico e data da monta no periodo
			        if ($diagnostico=='' && $cobertura_no_periodo) {
			            $falta_diagnostico[$id_fazenda]++;
			        }
			    }

			    // Coluna % Prenhez
			    if ($qtd_animais[$id_fazenda]>0) {
			        $taxa_prenhez =
			            ($total_prenhas[$id_fazenda]/$qtd_animais[$id_fazenda])*100;

			        $per_prenhez[$id_fazenda] = number_format($taxa_prenhez,2,',','.');
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
