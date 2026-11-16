<?php
include "conecta_mysql.inc";

$data_nascimento = $_POST['data_nascimento'];  
$data_acompanhamento_calculo = date("Y-m-d");

$date = new DateTime($data_nascimento); // Data de Nascimento
$idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo)); // Data do Acompanhamento
$idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
$idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');

$idade_ano = $idade_acompanhamento->format('%Y');
$idade_mes = $idade_acompanhamento->format('%m');

if ($idade_ano==0 && $idade_mes!=0) {
    $idade_animal = $idade_mes . ' mes(es)';
}
else if ($idade_ano!=0 && $idade_mes==0){
    $idade_animal = $idade_ano . ' ano(s)';
}
else if ($idade_ano!=0 && $idade_mes!=0) {
    $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
}
else {
    $idade_animal = '';
}

$total_meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

$valor[0]=0;
$valor[1]=0;
$valor[2]='';
$valor[3]='';

$valor[2]=$idade_animal;
$valor[3]=$total_meses;

$categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                                           WHERE tab_registro_lixeira_categoria_idade='0'");
$num_rows = mysqli_num_rows($categoria);	

if ($num_rows!=0) {
	while ($reg_categoria = mysqli_fetch_object($categoria)) {
	    $idade_de = $reg_categoria->tab_categoria_idade_de;
	    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

	    if ($total_meses >= $idade_de && $total_meses <= $idade_ate) {
	    	$valor[0]=1;

            if ($idade_ate==999999999){
		    	$valor[1]= '> 36 meses';
            }
            else {
		    	$valor[1]= $idade_de . ' a ' . $idade_ate . ' meses';
            }
	    }
	}
}					

$str=$valor[0] . '<|>' . $valor[1] . '<|>' . $valor[2] . '<|>' . $valor[3];
echo $str; 
?>