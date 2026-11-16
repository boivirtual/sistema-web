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


include_once "conecta_mysql_credenciais.inc";
$banco = "97174041604";
   
$conector = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
  
if ($conector === FALSE) {
   exit ("Falha na conexão com o banco de dados: " . mysqli_error($conector));
}
  
$bancoselecionado = mysqli_select_db($conector,$banco);

if ($bancoselecionado === FALSE) {
  exit ("Falha na seleção do banco de dados: " . mysql_error($conector));
}
 
$arquivo = "pai.csv";
  
$fp = fopen($arquivo, 'r');

if ($fp === FALSE) {
   exit ("Falha na seleção do arquivo texto: " . mysql_error($conector));
}

$arq = fopen($arquivo,'r');

while(!feof($arq)) {
    for($i=0; $i<1; $i++){
	    if ($conteudo = fgets($arq)){
		    $linha = explode(';', $conteudo);
	    }

        $linha[$i]=ltrim($linha[$i]);
        $linha[$i]=rtrim($linha[$i]);

        $codigo_animal = $linha[0];

        if (substr($codigo_animal,0,1)=='A' || 
            substr($codigo_animal,0,1)=='C' ||
            substr($codigo_animal,0,1)=='P') {

            if (substr($codigo_animal,0,2)=='AA') {
                $codigo_alfa = substr($codigo_animal,0,2);
                $codigo_numerico = substr($codigo_animal,2,4);
            }
            else {
                $codigo_alfa = substr($codigo_animal,0,1);
                $codigo_numerico = substr($codigo_animal,1,5);
            }
        }
        else {
           $codigo_alfa = '';
           $codigo_numerico = $codigo_animal; 
        }

        $obs = $linha[5];
        $nome_pai = $linha[6];
        $codigo_pai = $linha[7];

        if (substr($codigo_pai,0,1)=='S') {
            $tbl_semen = mysqli_query($conector, "SELECT * FROM tbl_semem
                WHERE tbl_semem_codigo_alfa ='$codigo_pai'");

            $num_rows = mysqli_num_rows($tbl_semen);    

            if ($num_rows!=0) {
                $reg = mysqli_fetch_object($tbl_semen);
                $codigo_pai_id = $reg->tbl_semem_codigo_id;
            }
            else {
                $codigo_pai_id = 0;
            }
        }
        else {
            $tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
                WHERE tbl_animal_codigo_numerico ='$codigo_pai'");

            $num_rows = mysqli_num_rows($tbl_animal);    

            if ($num_rows!=0) {
                $reg = mysqli_fetch_object($tbl_animal);
                $codigo_pai_id = $reg->tbl_animal_codigo_id;
            }
            else {
                $codigo_pai_id = 0;
            }

        }

        $tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
            WHERE tbl_animal_codigo_alfa ='$codigo_alfa' AND 
                  tbl_animal_codigo_numerico ='$codigo_numerico'");

        $num_rows = mysqli_num_rows($tbl_animal);    

        if ($num_rows!=0) {
            $reg = mysqli_fetch_object($tbl_animal);
            $codigo_animal_id = $reg->tbl_animal_codigo_id;
            $observacao_animal = $reg->tbl_animal_observacao;
        }
        else {
            $codigo_animal_id = 0;
            $observacao_animal = '';
        }

        $sql = "UPDATE tbl_animais SET 
                       tbl_animal_codigo_pai='$codigo_pai_id',
                       tbl_animal_nome_pai='$nome_pai'
                       WHERE tbl_animal_codigo_id='$codigo_animal_id'";
        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado) {
            echo $codigo_animal_id .' '. $erro_mysql . '</br>';
        }
        else {
            echo $codigo_animal_id.'-'.$codigo_alfa.$codigo_numerico .' '. $codigo_pai_id .' '. $nome_pai . '</br>'; 
        }

        if ($observacao_animal=='' && $obs!='') {
            $sql = "UPDATE tbl_animais SET 
                           tbl_animal_observacao='$obs'
                           WHERE tbl_animal_codigo_id='$codigo_animal_id'";
            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado) {
                echo $codigo_animal_id .' '. $erro_mysql . '</br>';
            }
            else {
                echo 'Observação: ' . $obs . '</br>'; 
            }

        }
	}
} 
echo 'fim do processamento';
  
mysqli_close($conector);  
fclose($arq);


?>