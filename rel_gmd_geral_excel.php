<?php

$data_sistema = date("d/m/Y");

// 		Começa Excel
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Instanciamos a classe
$spreadsheet = new Spreadsheet();

@ session_start(); 
$cnpj_cliente = $_SESSION['id_cliente'];

// abre banco de dados
$banco = $cnpj_cliente;
//$senha_bd = "";

// Servidor
include_once "conecta_mysql_credenciais.inc";

  $conector = mysqli_connect($servidor, $usuario_bd, $senha_bd);
  
  if (mysqli_connect_error()) {
  	  print_r("Falha na conexão: ", mysqli_connect_error());
      exit;
  }

  $bancoselecionado = mysqli_select_db($conector,$banco);

  if ($bancoselecionado === FALSE) {
  	  print_r("Falha na seleção do banco de dados: " . mysqli_error($conector));
      exit;
  }

    $data_sistema = date("d/m/Y");

    $data_inicial = $_REQUEST['data_inicial'];
    $partes = explode("-", $data_inicial);
    $ano_inicial = $partes[0];
    $mes_inicial = $partes[1];
    $dia_inicial = '01';

    $data_final = $_REQUEST['data_final'];
    $partes = explode("-", $data_final);
    $ano_final = $partes[0];
    $mes_final = $partes[1];
    $dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);

    $data1 = new DateTime($data_inicial);
    $data2 = new DateTime($data_final);
    $intervalo = $data1->diff($data2);
    $qtd_meses = $intervalo->y * 12 + $intervalo->m + $intervalo->d/30 + $intervalo->h / 24;
    $qtd_meses++;
    $ano_atual = $ano_inicial;

    if ($qtd_meses>12) {
        echo 'Erro meses';
        exit;
    }

    $data_array=new DateTime($data_inicial);

    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
    $mes_extenco = ucfirst(utf8_encode($mes_extenco));
    $array_mes_extenco[0]=$mes_extenco.'/'.$ano_atual;

    $array_mes[0]=$data_array->format('m');
    $array_ano[0]=$data_array->format('Y');

    $ano_mes = $data_array->format('Y').$data_array->format('m');
    $array_mes_ano[$ano_mes]=$ano_mes;
    $array_peso[$ano_mes]=0;

    for ($i=1; $i < $qtd_meses; $i++) { 
        $proximo_mes=1;
        $data_array->add(new DateInterval('P'.$proximo_mes.'M'));

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
        $mes_extenco = ucfirst(utf8_encode($mes_extenco));
        $array_mes_extenco[$i]=$mes_extenco.'/'.$ano_atual;

        if ($mes_extenco == 'Dezembro') {
            $ano_atual++;
        }

        $array_mes[$i]=$data_array->format('m');
        $array_ano[$i]=$data_array->format('Y');
        $ano_mes = $data_array->format('Y').$data_array->format('m');
        $array_mes_ano[$ano_mes]=$ano_mes;
        $array_peso[$ano_mes]=0;
    } 

    $array_celula[0]='H';
    $array_celula[1]='I';
    $array_celula[2]='J';
    $array_celula[3]='K';
    $array_celula[4]='L';
    $array_celula[5]='M';
    $array_celula[6]='N';
    $array_celula[7]='O';
    $array_celula[8]='P';
    $array_celula[9]='Q';
    $array_celula[10]='R';
    $array_celula[11]='S';
    $array_celula[12]='T';
    $array_celula[13]='U';
    $array_celula[14]='V';

$codigo_alfa_filtro = $_REQUEST['codigo_alfa'];
$codigo_numerico_filtro = $_REQUEST['codigo_numerico']; 
$local_filtro = $_REQUEST["local"];
$raca_filtro = $_REQUEST["raca"];
$categoria_filtro = $_REQUEST["categoria"];
$pai_filtro = $_REQUEST["pai"];
$mae_filtro = $_REQUEST["mae"];
$sexo_filtro = $_REQUEST["sexo"];
$solteiras = $_REQUEST["solteiras"];
$descarte = $_REQUEST["descarte"];
$paridas = $_REQUEST["paridas"];
$data_paridas_ate = $_REQUEST["data_paridas"];
$parto = $_REQUEST["parto"];
$num_parto_de = $_REQUEST['num_parto_de'];
$num_parto_ate = $_REQUEST['num_parto_ate'];
$aborto = $_REQUEST["aborto"];
$num_aborto_de = $_REQUEST['num_aborto_de'];
$num_aborto_ate = $_REQUEST['num_aborto_ate'];
$previsao_parto_de = $_REQUEST['previsao_parto_de'];
$previsao_parto_ate = $_REQUEST['previsao_parto_ate'];
$positivo = $_REQUEST['positivo'];
$negativo = $_REQUEST['negativo'];

if (isset($_REQUEST['estacao'])) {
    $estacao_filtro = $_REQUEST['estacao'];

    $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
        WHERE tbl_par_estacao_nome='$estacao_filtro'
        ORDER BY tbl_par_estacao_id ASC");  

    $num_rows = mysqli_num_rows($sql);
    $array_estacao = array();

    if ($num_rows!=0) {
        while ($reg_estacao = mysqli_fetch_object($sql)){
            $codigo_estacao = $reg_estacao->tbl_par_estacao_id;
            $array_estacao[] = $codigo_estacao;
        }

        $array_estacao = implode(',', $array_estacao);
    }
}

$westacao = "";
if (!empty ($array_estacao)) {
    
    $array_estacao = explode(',', $array_estacao);

    $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(";
    $westacao.= implode(',', $array_estacao);
    $westacao.= ")";
}

if ($data_paridas_ate=='') {
    $data_paridas_ate='9999-99-99';
    $data_paridas_de='0000-00-00';
}
else {
    $data_paridas_de = date("Y-m-d", strtotime('-8 month',strtotime($data_paridas_ate)));
    $data_paridas_de = date("Y-m-d", strtotime('-1 day',strtotime($data_paridas_de)));
}

if ($previsao_parto_de=='') {
    $previsao_parto_de = '0000-00-00';
    $previsao_parto_ate = '9999-99-99';
}

$vaca_solteira = '';
$vaca_parida = '';
$vaca_descarte = '';
$tem_parto = '';
$tem_aborto = '';
$tem_previsao_parto = '';
$ultimo_parto = '0000-00-00';
$data_previsao_parto = '0000-00-00';
$tem_positivo = '';
$tem_negativo = '';

$local= array();
$matriz_itens = explode(",", $local_filtro);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $local[$i]=$matriz_itens[$i];
}

$local = implode(',', $local);
$local = substr($local,0, -1);

$wlocal = '';
$wlocal_anterior = '';

if ($local_filtro!='') {
    $wlocal = " AND tbl_animal_codigo_fazenda IN(";
    $wlocal.= $local;
    $wlocal.= ")";

    $wlocal_anterior = " AND (tbl_animal_codigo_fazenda IN(";
    $wlocal_anterior.= $local;
    $wlocal_anterior.= ")";
    $wlocal_anterior.= " OR tbl_animal_codigo_fazenda_anterior IN(";
    $wlocal_anterior.= $local;
    $wlocal_anterior.= "))";

}

$wsexo='';
if ($sexo_filtro=='Todos') {
    $wsexo='';
}
else {
    $wsexo = " AND tbl_animal_sexo IN(";
    $wsexo.= "'" . $sexo_filtro . "'";
    $wsexo.= ")";
}

$raca= array();
$matriz_itens = explode(",", $raca_filtro);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $raca[$i]=$matriz_itens[$i];
}

$raca = implode(',', $raca);
$raca = substr($raca,0, -1);

$wraca = '';

if ($raca_filtro!='') {
    $wraca = " AND tbl_animal_codigo_raca IN(";
    $wraca.= $raca;
    $wraca.= ")";
}

$categoria= array();
$matriz_itens = explode(",", $categoria_filtro);
$quantidade_categoria = count($matriz_itens);

for($i=0; $i < $quantidade_categoria; $i++) {
    $categoria[$i]=$matriz_itens[$i];
}

$categoria = implode(',', $categoria);
$categoria = substr($categoria,0, -1);
$quantidade_categoria--;

$wcategoria = '';

if ($categoria_filtro!='') {
    $wcategoria = explode(",", $categoria);
}


/*for ($k=0; $k < $quantidade_categoria; $k++) { 
   $value = $wcategoria[$k];
   print_r ('Categorias agora: ' . $value);
}
exit;*/

$pai= array();
$matriz_itens = explode(",", $pai_filtro);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $pai[$i]=$matriz_itens[$i];
}

$pai = implode(',', $pai);
$pai = substr($pai,0, -1);

$wpai = '';

if ($pai_filtro!='') {
    $wpai = " AND tbl_animal_codigo_pai IN(";
    $wpai.= $pai;
    $wpai.= ")";
}

$mae= array();
$matriz_itens = explode(",", $mae_filtro);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $mae[$i]=$matriz_itens[$i];
}

$mae = implode(',', $mae);
$mae = substr($mae,0, -1);

$wmae = '';

if ($mae_filtro!='') {
    $wmae = " AND tbl_animal_codigo_mae IN(";
    $wmae.= $mae;
    $wmae.= ")";
}

$peso_nasc_inicial = $_REQUEST["peso_nasc_inicial"];
$peso_nasc_final = $_REQUEST["peso_nasc_final"];

$peso_desmama_inicial = $_REQUEST["peso_desmama_inicial"];
$peso_desmama_final = $_REQUEST["peso_desmama_final"];

$peso_ult_inicial = $_REQUEST["peso_ult_inicial"];
$peso_ult_final = $_REQUEST["peso_ult_final"];

if ($peso_nasc_inicial==0 && $peso_nasc_final==0){
    $wpeso_nasc = '';
}
else {
    $wpeso_nasc = " AND tbl_animal_primeiro_peso >= '$peso_nasc_inicial' AND tbl_animal_primeiro_peso <= '$peso_nasc_final'";
}

if ($peso_desmama_inicial==0 && $peso_desmama_final==0){
    $wpeso_desmama = '';
}
else {
    $wpeso_desmama = " AND tbl_animal_peso_desmama >= '$peso_desmama_inicial' AND tbl_animal_peso_desmama <= '$peso_desmama_final'";
}

if ($peso_ult_inicial==0 && $peso_ult_final==0){
    $wpeso_ult = '';
}
else {
    $wpeso_ult = " AND tbl_animal_ultimo_peso >= '$peso_ult_inicial' AND tbl_animal_ultimo_peso <= '$peso_ult_final'";
}

$data_nasc_inicial = $_REQUEST["data_nasc_inicial"];
$data_nasc_final = $_REQUEST["data_nasc_final"];

if ($data_nasc_inicial==0 && $data_nasc_final==0){
    $wdata_nasc = '';
}
else {
    $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
}

$wativo = $_REQUEST['ativo'];

$tipo_rel= $_REQUEST["tipo_rel"];
$descricao_filtro= $_REQUEST["descricao_filtro"];

@ session_start(); 

$codigo_grupo_usuario = $_SESSION['grupo_usuario'];

$desc_tipo_rel = 'Por Categoria';

$nome_relatorio = "Análise de Ganho de Peso Rebanho - " . $desc_tipo_rel;

$spreadsheet->getActiveSheet()->mergeCells('A1:K1');
$spreadsheet->getActiveSheet()->mergeCells('B2:L2');
$spreadsheet->getActiveSheet()->mergeCells('B4:D4');
$spreadsheet->getActiveSheet()->mergeCells('B5:D5');

	$spreadsheet->setActiveSheetIndex(0)
	    ->setCellValue('A1', $nome_relatorio)
		->setCellValue("A2", "Filtro: ")
		->setCellValue("B2", $descricao_filtro);

	$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
	$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);

	$spreadsheet->setActiveSheetIndex(0)
	    ->setCellValue("A4","Animais");

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('F4',"GMD Global");

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A6","Categoria")
        ->setCellValue("B6","Sexo")
        ->setCellValue("C6","Qtde")
        ->setCellValue("D6","GMD");

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);

	$spreadsheet->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	$spreadsheet->getActiveSheet()->getStyle('J1:L1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
	$spreadsheet->getActiveSheet()->getStyle('A2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
	$spreadsheet->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('A5') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	$spreadsheet->getActiveSheet()->getStyle('F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('B6:D6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->getStyle('A4:D4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A4:D4')->getFill()->getStartColor()->setARGB('DCDCDC');

    $spreadsheet->getActiveSheet()->getStyle('A6:D6')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A6:D6')->getFill()->getStartColor()->setARGB('DCDCDC');

    $spreadsheet->getActiveSheet()->getStyle('F4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('F4')->getFill()->getStartColor()->setARGB('DCDCDC');

    $spreadsheet->getActiveSheet()->setShowGridlines(true);

    $styleArray = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
    ];

    $spreadsheet->getActiveSheet()->getStyle('A4:D4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F5')->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->getStyle('A5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D5')->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->getStyle('A6')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B6')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C6')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D6')->applyFromArray($styleArray);

    $linha=6;

    $data_inicial = $ano_inicial .'-'. $mes_inicial .'-01' ;
    $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
    $animais_listados=0;
    $gmd_total = 0;
    $numero_gmd = 0;

    $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
        WHERE tab_registro_lixeira_categoria_idade='0'");

    $num_rows = mysqli_num_rows($tbl_categoria);    

    if ($num_rows!=0) {
        while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
            $idade_de = $reg_categoria->tab_categoria_idade_de;
            $idade_ate = $reg_categoria->tab_categoria_idade_ate;
            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

            if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                $desc_categoria = ' > 36 meses';
            }
            else {
                $desc_categoria =  $reg_categoria->tab_categoria_idade_de . ' a ' .
                $reg_categoria->tab_categoria_idade_ate . ' meses';
            }

            $array_categoria[$codigo_categoria] = $codigo_categoria;
            $array_desc_categoria[$codigo_categoria] = $desc_categoria;
            $array_qtd_macho_categoria[$codigo_categoria] = 0;
            $array_qtd_femea_categoria[$codigo_categoria] = 0;
            $array_gmd_macho_categoria[$codigo_categoria] = 0;
            $array_gmd_femea_categoria[$codigo_categoria] = 0;
        }
    }   

    if ($codigo_alfa_filtro!='' || $codigo_numerico_filtro!='') {
        $tbl_animal = mysqli_query($conector, "SELECT * from tbl_animais 
            WHERE tbl_animal_codigo_alfa = '$codigo_alfa_filtro' AND 
                  tbl_animal_codigo_numerico = '$codigo_numerico_filtro'");
    }
    else {
        $tbl_animal = mysqli_query($conector, "SELECT * from tbl_animais 
            WHERE tbl_animal_lixeira=0 AND
                  tbl_animal_ativo='$wativo'" . $wlocal . $wsexo . $wraca . 
                  $wpai . $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . 
                  $wdata_nasc .

                  " OR (DATE(tbl_animal_baixado_em)>='$data_inicial' AND DATE(tbl_animal_baixado_em)<='$data_final' AND tbl_animal_ativo='N' AND (tbl_animal_situacao='V' OR tbl_animal_situacao='M'))" . $wlocal_anterior . $wsexo . $wraca . $wpai . 
                      $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .
            " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC"); 
    }

    $num_rows_animais = mysqli_num_rows($tbl_animal);

    if ($num_rows_animais!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animal)) {
            $codigo = $reg_animal->tbl_animal_codigo_id;
            $sexo = $reg_animal->tbl_animal_sexo; 
            $animal_descarte = $reg_animal->tbl_animal_descarte_reproducao;

            // verifica a cobertura do animal
            $sql = mysqli_query($conector, "SELECT * FROM
                    tbl_item_cobertura
                INNER JOIN tbl_cobertura
                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                INNER JOIN tbl_parametro_estacao_monta
                        ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal='$codigo'" . $westacao . "
                ORDER BY tbl_ite_cobertura_numero_id DESC limit 1");

            $num_rows = mysqli_num_rows($sql);

            if ($num_rows!=0) {
                $reg_cobertura = mysqli_fetch_object($sql);
                $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
                $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
                $estacao_monta = $reg_cobertura->tbl_par_estacao_nome;
            }
            else {
                $codigo_local = 0;
                $estacao_animal = 0;
                $estacao_monta = '';
            }

            $tem_negativo = '';
            $tem_positivo = '';
            $vaca_descarte = '';

            if ($descarte=='S') {
                if ($animal_descarte=='S') {
                    $vaca_descarte = 'S';
                }
            }

            $data_peso_nascimento=0;
            $peso_nascimento=0;

            if ($reg_animal->tbl_animal_primeiro_peso!='') {
                $data_primeiro_peso = substr($reg_animal->tbl_animal_data_primeiro_peso, 0, 10);

                if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                    $data_peso_nascimento = $data_primeiro_peso;
                    $peso_nascimento = $reg_animal->tbl_animal_primeiro_peso;
                }
            }
            else {
                if ($reg_animal->tbl_animal_movimentacao_compra!='') {
                    $data_primeiro_peso = $reg_animal->tbl_animal_data_compra;

                    if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                        $data_peso_nascimento = $data_primeiro_peso;
                        $peso_nascimento = $reg_animal->tbl_animal_ultimo_peso;
                    }
                }
            }

            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
            $data_acompanhamento_calculo = $data_final;
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            $categoria_animal = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                    WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($categoria_animal);    

            if ($num_rows!=0) {
                while ($reg_cat_animal = mysqli_fetch_object($categoria_animal)) {
                    $idade_de = $reg_cat_animal->tab_categoria_idade_de;
                    $idade_ate = $reg_cat_animal->tab_categoria_idade_ate;

                    if ($idade >= $idade_de && $idade <= $idade_ate) {
                        $codigo_categoria_animal = $reg_cat_animal->tab_codigo_categoria_idade;
                    }
                }
            }                   

            // verifica vacas solteiras
            if ($solteiras=='S' || $paridas=='S') {
                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'
                    ORDER BY tbl_animal_data_nascimento DESC limit 1");

                $ultimo_filho = mysqli_num_rows($tbl_filhos);

                if ($ultimo_filho!=0) {
                    $reg_filhos = mysqli_fetch_object($tbl_filhos);
                    $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento; 

                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($ultimo_parto); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade_ano = $idade_acompanhamento->format('%Y');
                    $idade_mes = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    if ($idade < 8) {
                        $vaca_parida = 'S';
                        $vaca_solteira = '';
                    }
                    else {
                        $vaca_solteira = 'S';
                        $vaca_parida = '';
                    }
                }
                else {
                    $ultimo_parto = '0000-00-00';
                }
            }

            // verifica partos
            if ($sexo == 'Femea' && $num_parto_de!='' && $num_parto_ate!='') {
                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'");

                $num_partos = mysqli_num_rows($tbl_filhos);

                if ($num_partos>=$num_parto_de && 
                    $num_partos<=$num_parto_ate && $idade_animal>=8) {
                    $tem_parto = "S";
                }
                else {
                    $tem_parto = "";
                }
            }

            // verifica abortos
            if ($sexo == 'Femea' && $num_aborto_de!='' && $num_aborto_ate!='') {
                $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                    WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                          tbl_mov_estoque_codigo_id_animal=999999999 AND
                          (tbl_mov_estoque_entrada_saida='A' OR 
                           tbl_mov_estoque_entrada_saida='S') AND 
                          (tbl_mov_estoque_tipo_movimentacao='M' OR
                           tbl_mov_estoque_tipo_movimentacao='A' OR
                           tbl_mov_estoque_tipo_movimentacao='B')");

                $num_natimorto = mysqli_num_rows($tbl_natimorto);

                if ($num_natimorto>=$num_aborto_de && 
                    $num_natimorto<=$num_aborto_ate) {
                    $tem_aborto = "S";
                }
                else {
                    $tem_aborto = "";
                }
            }       

            // Verifica previsão de parto
            if ($previsao_parto_de!='' && $previsao_parto_ate!='') {

                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          tbl_cobertura_controle = 'C' AND 
                          tbl_ite_cobertura_resultado_diagnostico = 'P'
                    ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows_coberturas!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                    $cobertura_id = $reg_cobertura->tbl_cobertura_id;

                    $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                            WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");

                    $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                    $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
                        WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                              tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                        ORDER BY tbl_ite_protocoloiatf_id ASC");

                    $dias_previsao_parto = 282;

                    while($reg_itens_iatf = mysqli_fetch_object($sql)){
                        $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);

                        $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

                        $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                    }
                }
                else {
                    $data_previsao_parto = '0000-00-00';
                }
            }

            // verifica natimortos, nascidos ou abortos na estacao

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      tbl_cobertura_controle = 'C' AND 
                      tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
                ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

            $num_rows_item = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_item!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);

                $nascido_aborto = $reg_cobertura->tbl_ite_cobertura_nascido;
            }
            else {
                $nascido_aborto = '';
            }

            // Verifica diagnostico
            if ($positivo=='S' || $negativo=='S'){
                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          tbl_cobertura_controle = 'C' AND 
                          tbl_cobertura_codigo_estacao_monta =
                          '$estacao_animal'  
                    ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

                $num_rows = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

                    if ($diagnostico=='P'){
                        $tem_positivo = 'S';
                        $tem_negativo = '';
                    } 
                    else if ($diagnostico=='N') {
                        $tem_negativo = 'S';
                        $tem_positivo = '';
                    }
                    else {
                        $tem_negativo = '';
                        $tem_positivo = '';
                    }
                }
                else {
                    $tem_negativo = '';
                    $tem_positivo = '';
                }
            }
            else {
                $tem_negativo = '';
                $tem_positivo = '';
            }

            if ($positivo=='S' AND $nascido_aborto!='') {
                $tem_positivo='';
            }

            if ($data_previsao_parto!='0000-00-00' AND 
                $nascido_aborto!='') {
                $data_previsao_parto='0000-00-00';
            }

            if ($data_peso_nascimento!=0) {
                $data_peso_inicial = $data_peso_nascimento;
                $peso_inicial = $peso_nascimento;
            }
            else {
                $data_peso_inicial='0000-00-00';
                $peso_inicial = 9999;
            }

            if ($descarte==$vaca_descarte && 
                $data_previsao_parto>=$previsao_parto_de && 
                $data_previsao_parto<=$previsao_parto_ate && 
                $solteiras==$vaca_solteira && 
                $paridas==$vaca_parida && 
                $ultimo_parto>=$data_paridas_de && 
                $ultimo_parto<=$data_paridas_ate &&
                $parto==$tem_parto &&
                $aborto==$tem_aborto && 
                $positivo==$tem_positivo && 
                $negativo==$tem_negativo
                ) {

                $data_peso_final = '0000-00-00';
                $peso_final = 9999;

                $tbl_peso = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                    WHERE tbl_ite_pesagem_data_emissao>='$data_inicial' AND 
                          tbl_ite_pesagem_data_emissao<='$data_final' AND 
                          tbl_ite_pesagem_codigo_id_animal='$codigo' AND 
                          tbl_ite_pesagem_peso !=0
                        ORDER BY tbl_ite_pesagem_data_emissao ASC");

                $num_rows_peso = mysqli_num_rows($tbl_peso);    

                if ($num_rows_peso!=0) {
                    if ($data_peso_nascimento!=0) {
                        $partes = explode("-", $data_peso_nascimento);

                        for ($i=0; $i < $qtd_meses; $i++) { 
                            if ($array_mes[$i]==$partes[1] && $array_ano[$i]==$partes[0]) {
                                $peso_inicial=$peso_nascimento;
                            }
                        }
                    }

                    while ($reg_peso = mysqli_fetch_object($tbl_peso)) {
                        $data_peso = $reg_peso->tbl_ite_pesagem_data_emissao;
                        $peso = $reg_peso->tbl_ite_pesagem_peso;

                        if ($peso == 0) {
                            $peso = 9999;
                        }

                        $partes = explode("-", $data_peso_inicial);
                        $ano_mes_peso_inicial = $partes[0].$partes[1];

                        $partes = explode("-", $data_peso_final);
                        $ano_mes_peso_final = $partes[0].$partes[1];

                        $partes = explode("-", $data_peso);
                        $ano_mes_peso = $partes[0].$partes[1];

                        if ($data_peso_inicial=='0000-00-00') {
                            $data_peso_inicial=$data_peso;
                            $peso_inicial=$peso;
                        }

                        if ($ano_mes_peso_inicial==$ano_mes_peso) {
                            if ($peso_inicial==9999) {
                                if ($peso<$peso_inicial && $peso!=0) {
                                    $data_peso_inicial=$data_peso;
                                    $peso_inicial = $peso;
                                }
                            }
                        }

                        if ($ano_mes_peso_inicial!=$ano_mes_peso) {
                            if ($ano_mes_peso_final==$ano_mes_peso) {
                                if ($peso<$peso_final && $peso!=0) {
                                    $data_peso_final=$data_peso;
                                    $peso_final = $peso;
                                }
                            }
                            else {
                                $data_peso_final=$data_peso;
                                $peso_final = $peso;
                            }
                        }
                    }  
                } 

                if ($peso_inicial==9999) {
                    $peso_inicial = 0;
                }

                if ($peso_final==9999) {
                    $peso_final = 0;
                }

                $diferenca = strtotime($data_peso_final) - strtotime($data_peso_inicial);
                $dias = floor($diferenca / (60 * 60 * 24)); 

                if ($peso_final && $peso_inicial) {
                    $ganho = $peso_final - $peso_inicial;
                }
                else {
                    $ganho = 0;
                }

                if ($ganho!=0 && $dias!=0) {
                    $gmd = $ganho / $dias;
                }
                else {
                    $gmd=0;
                }

                if ($gmd!=0) {
                    if ($sexo=="M") {
                        $array_gmd_macho_categoria[$codigo_categoria_animal]+=$gmd;
                        $array_qtd_macho_categoria[$codigo_categoria_animal]++;
                    }
                    else {
                        $array_gmd_femea_categoria[$codigo_categoria_animal]+=$gmd;
                        $array_qtd_femea_categoria[$codigo_categoria_animal]++;
                    }
                }
            }
        }
    }

    if ($wcategoria=="") {
        for ($i=1; $i <= 5; $i++) { 
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
            if ($array_qtd_macho_categoria[$j]!=0) {
                $linha++;

                $celulas = 'B'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'C'.$linha.':D'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_desc_categoria[$j]);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'M');
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_qtd_macho_categoria[$j]);

                $gmd = $array_gmd_macho_categoria[$j]/
                       $array_qtd_macho_categoria[$j];

                $gmd_total+= $array_gmd_macho_categoria[$j];

                if ($gmd!=0) {
                    $numero_gmd+= $array_qtd_macho_categoria[$j];
                }

                //$gmd_edi = number_format($gmd,3,',','.');
                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);  

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $gmd);
                $animais_listados+=$array_qtd_macho_categoria[$j];
            }

            if ($array_qtd_femea_categoria[$j]!=0) {
                $linha++;

                $celulas = 'B'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'C'.$linha.':D'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_desc_categoria[$j]);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'F');
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_qtd_femea_categoria[$j]);

                $gmd = $array_gmd_femea_categoria[$j]/
                       $array_qtd_femea_categoria[$j];

                $gmd_total+= $array_gmd_femea_categoria[$j];

                if ($gmd!=0) {
                    $numero_gmd+= $array_qtd_femea_categoria[$j];
                }

                //$gmd_edi = number_format($gmd,3,',','.');
                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);  

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $gmd);
                $animais_listados+=$array_qtd_femea_categoria[$j];
            }
        }
    }
    else {
        for ($i=1; $i <= 5; $i++) { 
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);

            for ($k=0; $k < $quantidade_categoria; $k++) { 
                $value = $wcategoria[$k];
                if ($value==$j) {
                    if ($array_qtd_macho_categoria[$j]!=0) {
                        $linha++;

                        $celulas = 'B'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $celulas = 'C'.$linha.':D'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_desc_categoria[$j]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'M');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_qtd_macho_categoria[$j]);

                        $gmd = $array_gmd_macho_categoria[$j]/
                               $array_qtd_macho_categoria[$j];

                        $gmd_total+= $array_gmd_macho_categoria[$j];
                        $numero_gmd+= $array_qtd_macho_categoria[$j];

                        //$gmd_edi = number_format($gmd,3,',','.');
                        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $gmd);
                        $animais_listados+=$array_qtd_macho_categoria[$j];
                    }

                    if ($array_qtd_femea_categoria[$j]!=0) {
                        $linha++;

                        $celulas = 'B'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $celulas = 'C'.$linha.':D'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_desc_categoria[$j]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'F');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_qtd_femea_categoria[$j]);

                        $gmd = $array_gmd_femea_categoria[$j]/
                               $array_qtd_femea_categoria[$j];

                        $gmd_total+= $array_gmd_femea_categoria[$j];
                        $numero_gmd+= $array_qtd_femea_categoria[$j];

                        //$gmd_edi = number_format($gmd,3,',','.');
                        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);    
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $gmd);
                        $animais_listados+=$array_qtd_femea_categoria[$j];
                    }
                }
            }
        }
    }

/*    $num_rows_animais = mysqli_num_rows($tbl_animal);

    if ($num_rows_animais!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animal)) {
            $codigo = $reg_animal->tbl_animal_codigo_id;
            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
            $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
            $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
            $sexo = $reg_animal->tbl_animal_sexo; 
            $mae = $reg_animal->tbl_animal_codigo_mae; 
            $pai = $reg_animal->tbl_animal_codigo_pai; 
            $ativo = $reg_animal->tbl_animal_ativo; 
            $data_nasc = new DateTime($reg_animal->tbl_animal_data_nascimento);
            $data_nasc_edi = $data_nasc->format('d/m/Y');

            $data_peso_nascimento=0;
            $peso_nascimento=0;

            if ($reg_animal->tbl_animal_primeiro_peso!='') {
    $data_primeiro_peso = substr($reg_animal->tbl_animal_data_primeiro_peso, 0, 10);

                if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                    $data_peso_nascimento = $data_primeiro_peso;
                    $peso_nascimento = $reg_animal->tbl_animal_primeiro_peso;
                }
            }

            if ($codigo_alfa=='') {
                $codigo_edi = $codigo_numerico;
            }
            else {
                $codigo_edi = $codigo_alfa.'-'.$codigo_numerico;
            }

            $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_fazenda'");
            $num_rows = mysqli_num_rows($tab_fazenda);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_fazenda);
                $desc_local = $reg->tbl_pessoa_nome;
            }
            else {
                $desc_local = '';
            }

            $tab_mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$mae'");
            $num_rows = mysqli_num_rows($tab_mae);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_mae);
                $descricao_mae = $reg->tbl_animal_codigo_alfa. '-' . $reg->tbl_animal_codigo_numerico;
            }
            else {
                $descricao_mae = '';
            }

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
            $num_rows = mysqli_num_rows($tab_pai);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pai);
                $descricao_pai = $reg->tbl_semem_codigo_alfa;
                $pai = $reg->tbl_semem_codigo_id;
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                $num_rows = mysqli_num_rows($tab_pai);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                }
                else {
                    $descricao_pai = '';
                }
            }

            $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
            $num_rows = mysqli_num_rows($tab_raca);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_raca);
                $descricao_raca = utf8_encode($reg->tab_descricao_raca);
            }
            else {
                $descricao_raca = '';
            }

            $data_inicio = $reg_animal->tbl_animal_data_nascimento;
            $data_fim = $data_final;
            $diferenca = strtotime($data_fim) - 
                         strtotime($data_inicio);
            $idade = floor($diferenca / (60 * 60 * 24 * 30));
            $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);

            $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($tbl_categoria);    

            if ($num_rows!=0) {
                while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade >= $idade_de && $idade <= $idade_ate) {
                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                    }
                }
            }                   

            if ($wcategoria=="") {
                foreach ($array_mes_ano as $value) { 
                    $array_peso[$value]=0;
                } 

                $data_peso_inicial = 0;
                $peso_inicial = 0;

                if ($data_peso_nascimento!=0) {
                    $partes = explode("-", $data_peso_nascimento);
                    $ano_mes_peso = $partes[0].$partes[1];

                    for ($i=0; $i < $qtd_meses; $i++) { 
                        if ($array_mes[$i]==$partes[1] && $array_ano[$i]==$partes[0]) {
                            $array_peso[$ano_mes_peso]=$peso_nascimento;
                            $data_peso_inicial = $data_peso_nascimento;
                            $peso_inicial = $peso_nascimento;
                        }
                    }
                }

                if ($data_peso_inicial==0) {
                    $data_peso_inicial='0000-00-00';
                    $peso_inicial = 9999;
                }

                $data_peso_final = '0000-00-00';
                $peso_final = 9999;

                $tbl_peso = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                    WHERE tbl_ite_pesagem_data_emissao>='$data_inicial' AND 
                          tbl_ite_pesagem_data_emissao<='$data_final' AND 
                          tbl_ite_pesagem_codigo_id_animal='$codigo'
                    ORDER BY tbl_ite_pesagem_data_emissao ASC");

                $num_rows_peso = mysqli_num_rows($tbl_peso);    

                if ($num_rows_peso!=0) {
                    while ($reg_peso = mysqli_fetch_object($tbl_peso)) {
                        $data_peso = $reg_peso->tbl_ite_pesagem_data_emissao;
                        $peso = $reg_peso->tbl_ite_pesagem_peso;

                        if ($peso == 0) {
                            $peso = 9999;
                        }

                        $partes = explode("-", $data_peso_inicial);
                        $ano_mes_peso_inicial = $partes[0].$partes[1];

                        $partes = explode("-", $data_peso_final);
                        $ano_mes_peso_final = $partes[0].$partes[1];

                        $partes = explode("-", $data_peso);
                        $ano_mes_peso = $partes[0].$partes[1];

                        if ($data_peso_inicial=='0000-00-00') {
                            $data_peso_inicial=$data_peso;
                            $peso_inicial=$peso;
                        }

                        if ($ano_mes_peso_inicial==$ano_mes_peso) {
                            if ($peso<$peso_inicial && $peso!=0) {
                                $data_peso_inicial=$data_peso;
                                $peso_inicial = $peso;
                            }
                        }

                        if ($ano_mes_peso_inicial!=$ano_mes_peso) {
                            if ($ano_mes_peso_final==$ano_mes_peso) {
                                if ($peso<$peso_final && $peso!=0) {
                                        $data_peso_final=$data_peso;
                                        $peso_final = $peso;
                                }
                            }
                            else {
                                $data_peso_final=$data_peso;
                                $peso_final = $peso;
                            }
                        }

                        $array_peso[$ano_mes_peso]=$peso;
                    }
                }                   

                if ($peso_inicial==9999) {
                    $peso_inicial = 0;
                }

                if ($peso_final==9999) {
                    $peso_final = 0;
                }

                $diferenca = strtotime($data_peso_final) - strtotime($data_peso_inicial);
                $dias = floor($diferenca / (60 * 60 * 24)); 

                if ($peso_final && $peso_inicial) {
                    $ganho = $peso_final - $peso_inicial;
                }
                else {
                    $ganho = 0;
                }

                if ($ganho!=0 && $dias!=0) {
                    $gmd = $ganho / $dias;
                    $gmd_edi = number_format($gmd,3,',','.');
                    $gmd_total+= $gmd;
                    $numero_gmd++;
                }
                else {
                    $gmd_edi = 0;
                }

                $linha++;

                if ($ativo=='N') {
                    $celulas = 'C'.$linha.':D'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $celulas = 'H'.$linha.':U'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $celulas = 'A'.$linha.':U'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_local);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_nasc_edi);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_raca);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_mae);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_pai);

                    $coluna = 7;
                    foreach ($array_peso as $value) { 
                        $coluna++;
                        if ($value>0) {
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $value);
                        }
                    } 

                    if ($ganho!=0) {
                        $coluna++;
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $ganho);

                        $coluna++;
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $gmd_edi);
                    }
                }
                else {
                    $celulas = 'C'.$linha.':D'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $celulas = 'H'.$linha.':U'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_local);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_nasc_edi);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_raca);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_mae);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_pai);

                    $coluna = 7;
                    foreach ($array_peso as $value) { 
                        $coluna++;
                        if ($value>0) {
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $value);
                        }
                    } 

                    if ($ganho!=0) {
                        $coluna++;
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $ganho);

                        $coluna++;
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $gmd_edi);
                    }
                }
                $animais_listados++;
            }
            else {
                foreach ($wcategoria as $value) {
                    if ($value==$codigo_categoria) {

                        foreach ($array_mes_ano as $value) { 
                            $array_peso[$value]=0;
                        } 

                        $data_peso_inicial = 0;
                        $peso_inicial = 0;

                        if ($data_peso_nascimento!=0) {
                            $partes = explode("-", $data_peso_nascimento);
                            $ano_mes_peso = $partes[0].$partes[1];

                            for ($i=0; $i < $qtd_meses; $i++) { 
                                if ($array_mes[$i]==$partes[1] && $array_ano[$i]==$partes[0]) {
                                    $array_peso[$ano_mes_peso]=$peso_nascimento;
                                    $data_peso_inicial = $data_peso_nascimento;
                                    $peso_inicial = $peso_nascimento;
                                }
                            }
                        }

                        if ($data_peso_inicial==0) {
                            $data_peso_inicial='0000-00-00';
                            $peso_inicial = 9999;
                        }

                        $data_peso_final = '0000-00-00';
                        $peso_final = 9999;

                        $tbl_peso = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                            WHERE tbl_ite_pesagem_data_emissao>='$data_inicial' AND 
                                  tbl_ite_pesagem_data_emissao<='$data_final' AND 
                                  tbl_ite_pesagem_codigo_id_animal='$codigo'
                                  ORDER BY tbl_ite_pesagem_data_emissao ASC");

                        $num_rows_peso = mysqli_num_rows($tbl_peso);    

                        if ($num_rows_peso!=0) {
                            while ($reg_peso = mysqli_fetch_object($tbl_peso)) {
                                $data_peso = $reg_peso->tbl_ite_pesagem_data_emissao;
                                $peso = $reg_peso->tbl_ite_pesagem_peso;

                                if ($peso == 0) {
                                    $peso = 9999;
                                }

                                $partes = explode("-", $data_peso_inicial);
                                $ano_mes_peso_inicial = $partes[0].$partes[1];

                                $partes = explode("-", $data_peso_final);
                                $ano_mes_peso_final = $partes[0].$partes[1];

                                $partes = explode("-", $data_peso);
                                $ano_mes_peso = $partes[0].$partes[1];

                                if ($data_peso_inicial=='0000-00-00') {
                                    $data_peso_inicial=$data_peso;
                                    $peso_inicial=$peso;
                                }

                                if ($ano_mes_peso_inicial==$ano_mes_peso) {
                                    if ($peso<$peso_inicial && $peso!=0) {
                                        $data_peso_inicial=$data_peso;
                                        $peso_inicial = $peso;
                                    }
                                }

                                if ($ano_mes_peso_inicial!=$ano_mes_peso) {
                                    if ($ano_mes_peso_final==$ano_mes_peso) {
                                        if ($peso<$peso_final && $peso!=0) {
                                            $data_peso_final=$data_peso;
                                            $peso_final = $peso;
                                        }
                                    }
                                    else {
                                        $data_peso_final=$data_peso;
                                        $peso_final = $peso;
                                    }
                                }

                                $array_peso[$ano_mes_peso]=$peso;
                            }
                        }                   

                        if ($peso_inicial==9999) {
                            $peso_inicial = 0;
                        }

                        if ($peso_final==9999) {
                            $peso_final = 0;
                        }

                        $diferenca = strtotime($data_peso_final) - strtotime($data_peso_inicial);
                        $dias = floor($diferenca / (60 * 60 * 24)); 

                        if ($peso_final && $peso_inicial) {
                            $ganho = $peso_final - $peso_inicial;
                        }
                        else {
                            $ganho = 0;
                        }

                        if ($ganho!=0 && $dias!=0) {
                            $gmd = $ganho / $dias;
                            $gmd_edi = number_format($gmd,3,',','.');
                            $gmd_total+= $gmd;
                            $numero_gmd++;
                        }
                        else {
                            $gmd_edi = 0;
                        }

                        $linha++;

                        if ($ativo=='N') {
                            $celulas = 'C'.$linha.':D'.$linha;
                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                            $celulas = 'H'.$linha.':U'.$linha;
                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                            $celulas = 'A'.$linha.':U'.$linha;
                            $spreadsheet->getActiveSheet()->getStyle($celula)->getFont()->setColor(new Color(Color::COLOR_RED));

                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_local);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_nasc_edi);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_raca);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_mae);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_pai);

                            $coluna = 7;
                            foreach ($array_peso as $value) { 
                                $coluna++;
                                if ($value>0) {
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $value);
                                }
                            } 

                            if ($ganho!=0) {
                                $coluna++;
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $ganho);

                                $coluna++;
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $gmd_edi);
                            }
                        }
                        else {
                            $celulas = 'C'.$linha.':D'.$linha;
                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                            $celulas = 'H'.$linha.':U'.$linha;
                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_local);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_nasc_edi);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_raca);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_mae);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_pai);

                            $coluna = 7;
                            foreach ($array_peso as $value) { 
                                $coluna++;
                                if ($value>0) {
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $value);
                                }
                            } 
                            
                            if ($ganho!=0) {
                                $coluna++;
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $ganho);

                                $coluna++;
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $gmd_edi);
                            }
                        }

                        $animais_listados++;
                    }
                }
            }
        }
    }
*/

    if ($gmd_total!=0 && $numero_gmd>0) {
        $media_gmd = $gmd_total / $numero_gmd;
        $media_gmd_edi = number_format($media_gmd,3,',','.');
    }
    else {
        $media_gmd_edi = 0;
        $media_gmd = 0;
    }

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 5, $animais_listados);

    $spreadsheet->getActiveSheet()->getStyle('F5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, 5, $media_gmd);


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="gmd_por_categoria.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');

mysqli_close($conector);
exit;


?>