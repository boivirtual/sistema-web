<?php
//include "valida_sessao.inc";
 
function tira( $texto ){

$procurar = array( "-");
$trocar   = array( "");
return str_replace( $procurar, $trocar, $texto );
}

function tiras( $texto ){

$procurar = array( "/");
$trocar   = array( "");
return str_replace( $procurar, $trocar, $texto );
}

function tira_ponto( $texto ){

$procurar = array( ".");
$trocar   = array( "");
return str_replace( $procurar, $trocar, $texto );
}

$data_sistema = date("Y-m-d H:i:s");
$data_movimentacao=date("Y-m-d");


$servidor = "127.0.0.1";
$usuario_bd = "root";
$senha_bd = "";
$banco = "04527017000152";
   
$conector = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
  
if ($conector === FALSE) {
   exit ("Falha na conexуo com o banco de dados: " . mysqli_error($conector));
}
  
$bancoselecionado = mysqli_select_db($conector,$banco);

if ($bancoselecionado === FALSE) {
  exit ("Falha na seleчуo do banco de dados: " . mysql_error($conector));
}
 
$arquivo = "planilhas_excel/Plano de Contas.csv";
  
$fp = fopen($arquivo, 'r');

if ($fp === FALSE) {
   exit ("Falha na seleчуo do arquivo texto: " . mysql_error($conector));
}

$arq = fopen($arquivo,'r');

while(!feof($arq)) {
    for($i=0; $i<1; $i++){
	    if ($conteudo = fgets($arq)){
		    $linha = explode(';', $conteudo);
	    }

        $linha[$i]=ltrim($linha[$i]);
        $linha[$i]=rtrim($linha[$i]);

    $sql = "INSERT INTO tbl_plano_contas (
                        tbl_plano_contas_codigo_id,
                        tbl_plano_contas_nivel,
                        tbl_plano_contas_descricao,
                        tbl_plano_contas_refrencia_contabilidade,
                        tbl_plano_contas_debito_credito,
                        tbl_plano_contas_ana_sin,
                        tbl_plano_contas_descricao_complementar,
                        tbl_plano_contas_incluido_em,
                        tbl_plano_contas_incluido_por,
                        tbl_plano_contas_alterado_em,
                        tbl_plano_contas_alterado_por,
                        tbl_plano_contas_lixeira,
                        tbl_plano_contas_lixeira_em,
                        tbl_plano_contas_lixeira_por
                       ) 
                VALUES ('$linha[0]',
                        3,
                        '$linha[1]',
                        null,
                        '$linha[2]',
                        'A',
                        null,
                        '$data_sistema',
                        'George',
                        null,
                        null,
                        0,
                        null,
                        null
                       )";

        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            echo 'Plano ' . $erro_mysql;
            exit;
        }
        else {
            echo $linha[0] . ' ' . $linha[1] . '<br>';
        }
	}
}
echo 'fim do processamento';
  
mysqli_close($conector);  
fclose($arq);


?>