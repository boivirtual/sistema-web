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
   exit ("Falha na conex�o com o banco de dados: " . mysqli_error($conector));
}
  
$bancoselecionado = mysqli_select_db($conector,$banco);

if ($bancoselecionado === FALSE) {
  exit ("Falha na sele��o do banco de dados: " . mysql_error($conector));
}
 
$arquivo = "planilhas_excel/paca.csv";
$local = 6;
$numero_item = 0;
$epoca_pesagem = 11;
$descricao_lote = 'Pesagem Tabela Excel';
$total_pesados = 0;
$peso_total_kg = 0;
$filtros = 'FAZENDA PACA->Controle Ganho de Peso->Sexo:Todos';

$fp = fopen($arquivo, 'r');

if ($fp === FALSE) {
   exit ("Falha na sele��o do arquivo texto: " . mysql_error($conector));
}

$arq = fopen($arquivo,'r');

while(!feof($arq)) {
    for($i=0; $i<1; $i++){
        if ($conteudo = fgets($arq)){
            $linha = explode(';', $conteudo);
        }

        $linha[$i]=ltrim($linha[$i]);
        $linha[$i]=rtrim($linha[$i]);

        $data_pesagem = '2022-02-15';

        $total_pesados++;

        $peso_total_kg+= $linha[8];
    }
}

$peso_total_arroba = $peso_total_kg/30;
$peso_medio_kg = $peso_total_kg/$total_pesados;
$peso_medio_arroba = $peso_total_arroba / $total_pesados;

$sql = "INSERT INTO tbl_pesagem (
                    tbl_pesagem_controle,
                    tbl_pesagem_data,
                    tbl_pesagem_codigo_local,
                    tbl_pesagem_codigo_epoca,
                    tbl_pesagem_lote,
                    tbl_pesagem_qtd_animais_pesados,
                    tbl_pesagem_peso_kg,
                    tbl_pesagem_peso_arroba,
                    tbl_pesagem_peso_medio_kg,
                    tbl_pesagem_peso_medio_arroba,
                    tbl_pesagem_filtros,
                    tbl_pesagem_finalizada,
                    tbl_pesagem_incluido_em,
                    tbl_pesagem_incluido_por,
                    tbl_pesagem_alterado_em,
                    tbl_pesagem_alterado_por,
                    tbl_pesagem_lixeira,
                    tbl_pesagem_lixeira_em,
                    tbl_pesagem_lixeira_por,
                    tbl_pesagem_pasto,
                    tbl_pesagem_categoria,
                    tbl_pesagem_sexo,
                    tbl_pesagem_codigo_movimentacao,
                    tbl_pesagem_origem
                    ) VALUES (
                    'I',
                    '$data_pesagem',
                    '$local',
                    '$epoca_pesagem',
                    '$descricao_lote',
                    '$total_pesados',
                    '$peso_total_kg',
                    '$peso_total_arroba',
                    '$peso_medio_kg',
                    '$peso_medio_arroba',
                    '$filtros',
                    'S',
                    '$data_sistema',
                    'Administrador',
                    null,
                    null,
                    0,
                    null,
                    null,
                    null,
                    null,
                    null,
                    0
                )";

$resultado = mysqli_query($conector,$sql);
$erro_mysql = mysqli_error($conector);

if (!$resultado){
    echo 'Pesagem ' . $erro_mysql;
    exit;
} 

$numero_pesagem = mysqli_insert_id($conector);
$numero_pesagem = str_pad($numero_pesagem, 9, "0", STR_PAD_LEFT);

echo 'Pesagem: ' . $numero_pesagem . $filtros . '</br>';

$fp = fopen($arquivo, 'r');

if ($fp === FALSE) {
   exit ("Falha na sele��o do arquivo texto: " . mysql_error($conector));
}

$arq = fopen($arquivo,'r');


while(!feof($arq)) {
    for($i=0; $i<1; $i++){
	    if ($conteudo = fgets($arq)){
		    $linha = explode(';', $conteudo);
	    }

        $linha[$i]=ltrim($linha[$i]);
        $linha[$i]=rtrim($linha[$i]);

        if ($linha[6]=='') {$linha[6]=0;}
        if ($linha[7]=='') {$linha[7]=0;}
        if ($linha[8]=='') {$linha[8]=0;}
        if ($linha[10]=='') {$linha[10]=0;}

        $linha[1] = str_pad($linha[1], 9, "0", STR_PAD_LEFT);

        $codigo_animal = $linha[0].'-'.$linha[1];
        $codigo_animal = rtrim($codigo_animal);
        $peso = $linha[8];

        if ($linha[2]=='F') {
            $sexo = 'F�mea';
        }
        else {
            $sexo = 'Macho';
        }

        $nascimento = $linha[3];
        $raca = $linha[4];

        $tbl_animal = mysqli_query($conector, "select * from tbl_animais 
            where tbl_animal_codigo_alfa='$linha[0]' and 
                  tbl_animal_codigo_numerico='$linha[1]'");
        $num_rows = mysqli_num_rows($tbl_animal);

        if ($num_rows!=0){
            $reg_animal = mysqli_fetch_object($tbl_animal);
            $codigo_id = $reg_animal->tbl_animal_codigo_id;
        }
        else {
             $codigo_id = 0;
        }

        $tbl_raca = mysqli_query($conector, "SELECT * FROM tabela_racas
            WHERE tab_codigo_raca='$raca'");

        $num_rows = mysqli_num_rows($tbl_raca);    
        $reg_raca = mysqli_fetch_object($tbl_raca);
        $desc_raca = $reg_raca->tab_descricao_raca;

        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");

        $num_rows = mysqli_num_rows($tbl_categoria);    
        $codigo_categoria = 0;

        while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
            $idade_de = $reg_categoria->tab_categoria_idade_de;
            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

            if ($idade >= $idade_de && $idade <= $idade_ate) {
                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
            }
        }

        $numero_item++;

        $sql = "INSERT INTO tbl_item_pesagem (
                    tbl_ite_pesagem_numero_id,
                    tbl_ite_pesagem_numero_item,
                    tbl_ite_pesagem_data_emissao,
                    tbl_ite_pesagem_codigo_id_animal,
                    tbl_ite_pesagem_codigo_animal,
                    tbl_ite_pesagem_peso,
                    tbl_ite_pesagem_sexo,
                    tbl_ite_pesagem_nascimento,
                    tbl_ite_pesagem_raca,
                    tbl_ite_pesagem_pelagem,
                    tbl_ite_pesagem_mae,
                    tbl_ite_pesagem_observacao,
                    tbl_ite_pesagem_categoria,
                    tbl_ite_pesagem_qtd_animais
                ) VALUES (
                    '$numero_pesagem',
                    '$numero_item',
                    '$data_pesagem',
                    '$codigo_id',
                    '$codigo_animal',
                    '$peso',
                    '$sexo',
                    '$nascimento',
                    '$desc_raca',
                    null,
                    null,
                    null,
                    '$codigo_categoria',
                    1
            )";

        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            echo 'Item Pesagem ' . $erro_mysql;
            exit;
        }

        echo $linha[0] . ' - ' . $linha[1] . '</br>';
	}
}
echo 'fim do processamento';
  
mysqli_close($conector);  
fclose($arq);


?>