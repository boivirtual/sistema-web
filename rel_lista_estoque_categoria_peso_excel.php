<?php

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

@ session_start(); 
$controle_estoque = $_SESSION['controle_estoque'];

$local_filtro = $_REQUEST["local"];

$local= array();
$matriz_itens = explode(",", $local_filtro);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $local[$i]=$matriz_itens[$i];
}

$local = implode(',', $local);
$local = substr($local,0, -1);

$wlocal = '';
$wlocal_animais = '';
$wlocal_animais_pasto = '';
$wlocal_media_categoria = '';

if ($local_filtro!='') {
    $wlocal = " AND tbl_fechamento_local IN(";
    $wlocal.= $local;
    $wlocal.= ")";

    $wlocal_animais = " AND tbl_animal_codigo_fazenda IN(";
    $wlocal_animais.= $local;
    $wlocal_animais.= ")";

    $wlocal_animais_pasto = " WHERE tbl_animal_pasto_local IN(";
    $wlocal_animais_pasto.= $local;
    $wlocal_animais_pasto.= ")";

    $wlocal_media_categoria = " WHERE tbl_pm_local_id IN(";
    $wlocal_media_categoria.= $local;
    $wlocal_media_categoria.= ")";
}

$data_hoje=new DateTime();
$mes_hoje=$data_hoje->format('m');
$ano_hoje=$data_hoje->format('Y');

$data_inicial = $_REQUEST['data_inicial'];
$partes = explode("-", $data_inicial);
$mes_inicial = $partes[1];
$ano_inicial = $partes[0];

$data_final = $_REQUEST['data_final'];
$partes = explode("-", $data_final);
$mes_final = $partes[1];
$ano_final = $partes[0];

$data1 = new DateTime($data_inicial);
$data2 = new DateTime($data_final);
$intervalo = $data1->diff($data2);
$qtd_meses = $intervalo->y * 12 + $intervalo->m + $intervalo->d/30 + $intervalo->h / 24;
$qtd_meses++;
$ano_atual = $ano_inicial;

$data_array=new DateTime($data_inicial);

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
$mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
$mes_extenco = ucfirst(utf8_encode($mes_extenco));

$array_mes_extenco[0]=$mes_extenco.'/'.$ano_atual;

$array_mes[0]=$data_array->format('m');
$array_ano[0]=$data_array->format('Y');

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
} 

$tipo_rel= $_REQUEST["tipo_rel"];
$descricao_filtro= $_REQUEST["descricao_filtro"];

$nome_relatorio = 'Estoque de Animais por Kilo (peso)';

$data_sistema = date("d/m/Y");

$spreadsheet->getActiveSheet()->mergeCells('A1:J1');
$spreadsheet->getActiveSheet()->mergeCells('B2:K2');
$spreadsheet->getActiveSheet()->mergeCells('B3:K3');
$spreadsheet->getActiveSheet()->mergeCells('B4:F4');
$spreadsheet->getActiveSheet()->mergeCells('G4:J4');
$spreadsheet->getActiveSheet()->mergeCells('A4:A5');
$spreadsheet->getActiveSheet()->mergeCells('L4:L5');

$spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue("L1", "Data: " . $data_sistema)
        ->setCellValue("A2", "Filtro: ")
        ->setCellValue("B2", $descricao_filtro);

$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
//$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);

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

$spreadsheet->getActiveSheet()->getStyle('A4:L4')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A4:L4')->getFill()->getStartColor()->setARGB('DCDCDC');
$spreadsheet->getActiveSheet()->getStyle('A5:L5')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A5:L5')->getFill()->getStartColor()->setARGB('C0C0C0');

$spreadsheet->getActiveSheet()->getStyle('A1:L1')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A2:L2')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A3:L3')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A4:L4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A5:L5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('E4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('F4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('H4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('I4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('J4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('K4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('L4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('E5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('F5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('G5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('H5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('I5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('J5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('K5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('L5')->applyFromArray($styleArray);

$spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4","Meses")
        ->setCellValue("B5","0 a 7 meses")
        ->setCellValue("C5","8 a 12 meses")
        ->setCellValue("D5","13 a 24 meses")
        ->setCellValue("E5","25 a 36 meses")
        ->setCellValue("F5","> 36 meses")
        ->setCellValue("G5","0 a 7 meses")
        ->setCellValue("H5","8 a 12 meses")
        ->setCellValue("I5","13 a 24 meses")
        ->setCellValue("J5","25 a 36 meses")
        ->setCellValue("K5","> 36 meses")
        ->setCellValue("L4","Totais")

        ->setCellValue("B4","Fêmeas")
        ->setCellValue("G4","Machos");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(18);

$spreadsheet->getActiveSheet()->getStyle('A1:k1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('G4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A5:L5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical($align);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('L4')->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('L4')->getAlignment()->setVertical($align);

$linha=5;

    $data_inicial = $data_inicial . '-01';
    $data_final = $data_final . '-31';

    $total_geral = 0;
    $media_final = 0;

    $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
        WHERE tab_registro_lixeira_categoria_idade='0'");
    $num_rows_cat = mysqli_num_rows($categoria);    

    if ($num_rows_cat!=0) {
        while ($reg_categoria = mysqli_fetch_object($categoria)) {
            $id_categoria = $reg_categoria->tab_codigo_categoria_idade;
            $qtd_media_femea[$id_categoria] = 0;
            $valor_media_femea[$id_categoria] = 0;
            $total_media_femea[$id_categoria] = 0;            

            $qtd_media_macho[$id_categoria] = 0;
            $valor_media_macho[$id_categoria] = 0;
            $total_media_macho[$id_categoria] = 0;            
        }
    }

    for ($i=0; $i<($qtd_meses); $i++) { 
        $mes_lista = $array_mes[$i];
        $ano_lista = $array_ano[$i];

        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");
            $num_rows_cat = mysqli_num_rows($categoria);    

        if ($num_rows_cat!=0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $id_categoria = $reg_categoria->tab_codigo_categoria_idade;
                $peso_femea[$id_categoria] = 0;
                $peso_macho[$id_categoria] = 0;
            }
        }

        $total = 0;

        $fechamento= mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
            WHERE year(tbl_fechamento_data)='$ano_lista' AND 
                  month(tbl_fechamento_data)='$mes_lista'" . $wlocal);

        $num_rows = mysqli_num_rows($fechamento);

        if ($num_rows!=0) {
            while ($reg_fec = mysqli_fetch_object($fechamento)) {
                $sexo = $reg_fec->tbl_fechamento_sexo; 
                $peso = $reg_fec->tbl_fechamento_peso;
                $categoria = $reg_fec->tbl_fechamento_categoria;

                if ($sexo=='M') {
                    $peso_macho[$categoria]+=$peso;
                    $total+=$peso;
                } 
                else {
                    $peso_femea[$categoria]+=$peso;
                    $total+=$peso;
                }

                $total_geral+=$peso;
            }
        }

        for ($k=1; $k <=5; $k++) { 

            $index = str_pad($k , 3 , '0' , STR_PAD_LEFT);

            if ($peso_femea[$index]==0) {
                $peso_femea[$index]='';
            }
            else {
                $qtd_media_femea[$index]++;
                $valor_media_femea[$index]+=$peso_femea[$index];

            }

            if ($peso_macho[$index]==0) {
                $peso_macho[$index]='';
            }
            else {
                $qtd_media_macho[$index]++;
                $valor_media_macho[$index]+=$peso_macho[$index];
            }
        }

        if (($mes_hoje!=$mes_lista || $ano_hoje!=$ano_lista || $qtd_meses==1) && $total!=0) {
            $linha++;
            $celulas = 'A'.$linha.':L'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_mes_extenco[$i]);

            $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $peso_femea['001']);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $peso_femea['002']);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $peso_femea['003']);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $peso_femea['004']);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $peso_femea['005']);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $peso_macho['001']);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $peso_macho['002']);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $peso_macho['003']);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $peso_macho['004']);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $peso_macho['005']);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $total);
        }
    }

    // calcular estoque do ultimo mes se for o mes atual

    if ($mes_hoje==$mes_lista && $ano_hoje==$ano_lista) {
        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");
            $num_rows_cat = mysqli_num_rows($categoria);    

        if ($num_rows_cat!=0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $id_categoria = $reg_categoria->tab_codigo_categoria_idade;
                $peso_femea[$id_categoria] = 0;
                $peso_macho[$id_categoria] = 0;
            }
        }

        $total = 0;

        if ($controle_estoque=='I') {
            $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais
                WHERE tbl_animal_lixeira=0 AND tbl_animal_ativo='S'" . $wlocal_animais);

            $num_rows = mysqli_num_rows($tbl_animais);  
            if ($num_rows!=0) {
                while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                    $data_nasc = $reg_animal->tbl_animal_data_nascimento;
                    $sexo = $reg_animal->tbl_animal_sexo;

                    $primeiro_peso = $reg_animal->tbl_animal_primeiro_peso; 
                    $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
                    $ultimo_peso = $reg_animal->tbl_animal_ultimo_peso; 

                    if ($ultimo_peso!=0 && $ultimo_peso!='') {
                        $peso = $ultimo_peso;
                    }
                    else if ($peso_desmama!=0 && $peso_desmama!='') {
                        $peso = $peso_desmama;
                    }
                    else if ($primeiro_peso!=0 && $primeiro_peso!=''){
                        $peso = $primeiro_peso;
                    }
                    else {
                        $peso = 0;
                    }
            
                    $data_nascimento = $data_nasc;  
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    if ($idade<0) {
                        $idade=1;
                    }

                    $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                        WHERE tab_registro_lixeira_categoria_idade='0'");

                    $num_rows = mysqli_num_rows($tbl_categoria);    

                    if ($num_rows!=0) {
                        while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

                                if ($sexo=='M'){
                                    $peso_macho[$codigo_categoria]+=$peso;
                                    //$total+=$peso;
                                }
                                else {
                                    $peso_femea[$codigo_categoria]+=$peso;
                                    //$total+=$peso;
                                }

                                //$total_geral+=$peso;
                            }
                        }
                    }
                }
            }
        }
        else {
            $sql= "SELECT * FROM tbl_peso_medio_categoria" . $wlocal_media_categoria;

            $media_categoria = mysqli_query($conector, $sql);
            $num_rows = mysqli_num_rows($media_categoria); 

            if ($num_rows!=0) {
                while ($reg_animal = mysqli_fetch_object($media_categoria)) {
                    $sexo = $reg_animal->tbl_pm_sexo;
                    $peso_total = $reg_animal->tbl_pm_peso_total_atual;
                    $qtd_total = $reg_animal->tbl_pm_qtd_total_atual;
                    $codigo_categoria = $reg_animal->tbl_pm_categoria_id;

                    if ($sexo=='M'){
                        $peso_macho[$codigo_categoria]+=$peso_total;
                        //$total+=$peso_total;
                    }
                    else {
                        $peso_femea[$codigo_categoria]+=$peso_total;
                        //$total+=$peso_total;
                    }

                    //$total_geral+=$peso_total;                    
                }
            }
            else {
                $mes_hoje--;

                if ($mes_hoje==0) {
                    $mes_hoje = '01';
                    $ano_hoje--;
                }

                $media_categoria = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria 
                    WHERE year(tbl_pm_data)='$ano_hoje' and 
                          month(tbl_pm_data)='$mes_hoje'". $wlocal_media_categoria);

                $num_rows = mysqli_num_rows($media_categoria);  

                if ($num_rows!=0) {
                    while ($reg_animal = mysqli_fetch_object($media_categoria)) {
                        $sexo = $reg_animal->tbl_pm_sexo;
                        $peso_total = $reg_animal->tbl_pm_peso_total_atual;
                        $qtd_total = $reg_animal->tbl_pm_qtd_total_atual;
                        $codigo_categoria = $reg_animal->tbl_pm_categoria_id;

                        if ($sexo=='M'){
                            $peso_macho[$codigo_categoria]=$peso_total;
                            //$total+=$peso_total;
                        }
                        else {
                            $peso_femea[$codigo_categoria]=$peso_total;
                            //$total+=$peso_total;
                        }

                        //$total_geral+=$peso_total;                    
                    }
                }
            }
        }

        for ($k=1; $k <=5; $k++) { 

            $index = str_pad($k , 3 , '0' , STR_PAD_LEFT);

            if ($peso_femea[$index]==0) {
                $peso_femea[$index]='';
            }
            else {
                $qtd_media_femea[$index]++;
                $valor_media_femea[$index]+=$peso_femea[$index];

                $total+=$peso_femea[$index];
            }

            if ($peso_macho[$index]==0) {
                $peso_macho[$index]='';
            }
            else {
                $qtd_media_macho[$index]++;
                $valor_media_macho[$index]+=$peso_macho[$index];

                $total+=$peso_macho[$index];
            }
        }

        $linha++;
        $celulas = 'A'.$linha.':L'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_mes_extenco[$i-1]);

        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $peso_femea['001']);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $peso_femea['002']);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $peso_femea['003']);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $peso_femea['004']);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $peso_femea['005']);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $peso_macho['001']);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $peso_macho['002']);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $peso_macho['003']);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $peso_macho['004']);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $peso_macho['005']);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $total);
    }

for ($k=1; $k <=5; $k++) { 
    $index = str_pad($k , 3 , '0' , STR_PAD_LEFT);

    if ($valor_media_femea[$index]!=0 && $qtd_media_femea[$index]!=0) {
        $total_media_femea[$index]=$valor_media_femea[$index]/$qtd_media_femea[$index];
        $total_geral+=$total_media_femea[$index];
    }

    if ($valor_media_macho[$index]!=0 && $qtd_media_macho[$index]!=0) {
        $total_media_macho[$index]=$valor_media_macho[$index]/$qtd_media_macho[$index];
        $total_geral+=$total_media_macho[$index];
    }
}

$media_final = $total_geral/$qtd_meses;

$linha++;
$celulas = 'A'.$linha.':L'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, 'Média do Período');
$spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('J'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);

$celulas = 'B'.$linha.':L'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode("#.##0,00");

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $total_media_femea['001']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $total_media_femea['002']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $total_media_femea['003']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $total_media_femea['004']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $total_media_femea['005']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $total_media_macho['001']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $total_media_macho['002']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $total_media_macho['003']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $total_media_macho['004']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $total_media_macho['005']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $media_final);


// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment;filename="estoque_categoria_peso.xlsx"');

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