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
 
$arquivo = "planilhas_excel/pasto paca.csv";
$local = 6;

  
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

        $array_categoria = '001!002!003!004!005';
        $array_qtd_categoria_macho = '';
        $array_qtd_categoria_femea = '';
        $array_qtd_macho_anterior = '';
        $array_qtd_femea_anterior = ''; 

        $modulo = $linha[0];
        $descricao = $linha[1];

        if ($linha[2]=='') {
            $area = 0;
        }
        else {
           $area = $linha[2]; 
        }

        if ($linha[3]=='') {
            $capim = 0;
        }
        else {
           $capim = $linha[3]; 
        }


    $sql = "INSERT INTO  tbl_pasto (
            tbl_pasto_codigo_local,
            tbl_pasto_descricao,
            tbl_pasto_latitude,
            tbl_pasto_longitude,
            tbl_pasto_area,
            tbl_pasto_modulo,
            tbl_pasto_tipo_capim,
            tbl_pasto_descricao_lote,
            tbl_pasto_tipo_curral,
            tbl_pasto_incluido_em,
            tbl_pasto_incluido_por,
            tbl_pasto_alterado_em,
            tbl_pasto_alterado_por,
            tbl_pasto_lixeira,
            tbl_pasto_lixeira_em,
            tbl_pasto_lixeira_por,
            tbl_pasto_array_categoria,
            tbl_pasto_array_qtd_animais_macho,
            tbl_pasto_array_qtd_animais_femea,
            tbl_pasto_array_qtd_animais_ambos
            ) 
            VALUES (
                    '$local',
                    '$descricao',
                    null,
                    null,
                    '$area',
                    '$modulo',
                    '$capim',
                    null,
                    null,
                    '$data_sistema',
                    'George',
                    null,
                    null,
                    0,
                    null,
                    null,
                    '$array_categoria',
                    '!!!!',
                    '!!!!',
                    null
            )";

        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            echo 'Pasto ' . $erro_mysql;
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