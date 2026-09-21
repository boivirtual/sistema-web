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
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

// Instanciamos a classe
$spreadsheet = new Spreadsheet();

@ session_start(); 
$cnpj_cliente = $_SESSION['id_cliente'];
$controle_estoque = $_SESSION['controle_estoque'];

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

$local = $_REQUEST["local"];
$descricao_filtro= $_REQUEST["descricao_filtro"];

@ session_start(); 

$tbl_pessoa = mysqli_query($conector, "select * from tbl_pessoa 
        where tbl_pessoa_id ='$local' and tbl_pessoa_lixeira=0"); 
$reg_local = mysqli_fetch_object($tbl_pessoa);
$desc_local = utf8_encode($reg_local->tbl_pessoa_nome);

$nome_relatorio = "Mapa de Gado";

$spreadsheet->getActiveSheet()
    ->getPageSetup()
    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
$spreadsheet->getActiveSheet()
    ->getPageSetup()
    ->setPaperSize(PageSetup::PAPERSIZE_A4);

$spreadsheet->getActiveSheet()->mergeCells('A1:M1');
$spreadsheet->getActiveSheet()->mergeCells('N1:O1');
$spreadsheet->getActiveSheet()->mergeCells('B2:O2');
$spreadsheet->getActiveSheet()->mergeCells('B3:O3');
$spreadsheet->getActiveSheet()->mergeCells('A4:A5');
$spreadsheet->getActiveSheet()->mergeCells('C4:D4');
$spreadsheet->getActiveSheet()->mergeCells('E4:F4');
$spreadsheet->getActiveSheet()->mergeCells('G4:H4');
$spreadsheet->getActiveSheet()->mergeCells('I4:J4');
$spreadsheet->getActiveSheet()->mergeCells('K4:K5');
$spreadsheet->getActiveSheet()->mergeCells('L4:L5');
$spreadsheet->getActiveSheet()->mergeCells('N4:N5');
$spreadsheet->getActiveSheet()->mergeCells('M4:M5');
$spreadsheet->getActiveSheet()->mergeCells('O4:O5');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
	->setCellValue("N1", "Data: " . $data_sistema)
	->setCellValue("A2", "Filtro ")
	->setCellValue("B2", $descricao_filtro);

$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('A1:O1')->getFont()->setSize(8);
$spreadsheet->getActiveSheet()->getStyle('A2:O2')->getFont()->setSize(8);
$spreadsheet->getActiveSheet()->getStyle('A3:B3')->getFont()->setSize(8);
$spreadsheet->getActiveSheet()->getStyle('A4:O4')->getFont()->setSize(8);
$spreadsheet->getActiveSheet()->getStyle('A5:K5')->getFont()->setSize(8);

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

$spreadsheet->getActiveSheet()->getStyle('A3:O3')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A3:O3')->getFill()->getStartColor()->setARGB('DCDCDC');
$spreadsheet->getActiveSheet()->getStyle('A4:O4')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A4:O4')->getFill()->getStartColor()->setARGB('C0C0C0');
$spreadsheet->getActiveSheet()->getStyle('A5:O5')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A5:O5')->getFill()->getStartColor()->setARGB('C0C0C0');

$spreadsheet->getActiveSheet()->getStyle('A1:O1')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A2:O2')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A3:O3')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A4:O4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A5:O5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);
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
$spreadsheet->getActiveSheet()->getStyle('M4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('N4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('O4')->applyFromArray($styleArray);
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
$spreadsheet->getActiveSheet()->getStyle('M5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('N5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('O5')->applyFromArray($styleArray);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A3","Total Animais")

    ->setCellValue("A4","Pasto")
    ->setCellValue("B4","00 a 07 meses")
    ->setCellValue("C4","08 a 12 meses")
    ->setCellValue("E4","13 a 24 meses")
    ->setCellValue("G4","25 a 36 meses")
    ->setCellValue("I4","> 36 meses")
    ->setCellValue("K4","Total")
    ->setCellValue("L4","Descrição Lote")
    ->setCellValue("M4","Lote")
    ->setCellValue("N4","Dias de Permanência")
    ->setCellValue("O4","Área do Pasto (ha)")

    ->setCellValue("B5","Macho/Fêmea")
    ->setCellValue("C5","Macho")
    ->setCellValue("D5","Fêmea")
    ->setCellValue("E5","Macho")
    ->setCellValue("F5","Fêmea")
    ->setCellValue("G5","Macho")
    ->setCellValue("H5","Fêmea")
    ->setCellValue("I5","Macho")
    ->setCellValue("J5","Fêmea");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(7);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(7);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(7);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(7);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(7);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(7);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(7);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(7);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(7);
$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(11);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal($align);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical($align);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('K4:O4')->getAlignment()->setHorizontal($align);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('K4:O4')->getAlignment()->setVertical($align);

$spreadsheet->getActiveSheet()->getStyle('K4:O4')->getAlignment()->setWrapText(true);

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('N1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$spreadsheet->getActiveSheet()->getStyle('A3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('B3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

$spreadsheet->getActiveSheet()->getStyle('A2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A4:K4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A5:K5') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$linha=5;

$total_animais = 0;
$total_cat_M_F = 0;
$ultima_data = '0000-00-00';

for ($i = 1; $i <=5; $i++) {
    $j = str_pad($i, 3, "0", STR_PAD_LEFT);
    $total_cat_macho[$j]=0;
    $total_cat_femea[$j]=0;
}
            

$tbl_pasto= mysqli_query($conector, "SELECT * FROM tbl_pasto
    WHERE tbl_pasto_codigo_local='$local' AND 
          tbl_pasto_modulo=999");

$num_rows = mysqli_num_rows($tbl_pasto);

if ($num_rows!=0) {
    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
        $descricao = utf8_encode($reg_pasto->tbl_pasto_descricao);
        $codigo_pasto = $reg_pasto->tbl_pasto_id;
        $descricao_lote = utf8_encode($reg_pasto->tbl_pasto_descricao_lote);
        $id_lote = ltrim($reg_pasto->tbl_pasto_id_lote, '0').'/'.
                   substr($reg_pasto->tbl_pasto_ano_lote, 2, 2);
        $area = $reg_pasto->tbl_pasto_area;

        if ($area==0) {
            $area='';
        }

        // Pega dias com animais no pasto
        $dias_pasto = 0;

        $dataAtual = new DateTime();
        $dataAnterior = new DateTime($reg_pasto->tbl_pasto_data_com_animais_anterior);
        $dataCom = new DateTime($reg_pasto->tbl_pasto_data_com_animais);

        if ($dataCom!='') {
            $diff = $dataAtual->diff($dataCom);
            $dias_pasto = $diff->days;
        }

        // Fim pega dias com animais no pasto

        $tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id='$codigo_pasto' AND 
                  tbl_animal_pasto_situacao='A'
            ORDER BY tbl_animal_pasto_nascimento DESC");

        $num_rows_animal = mysqli_num_rows($tbl_animal_pasto);
        $total_animais_pasto = 0;

        for ($i = 1; $i <=5; $i++) {
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
            $total_macho[$j]=0;
            $total_femea[$j]=0;
        }

        //$dias_pasto = 0;

        if ($num_rows_animal!=0) {
            while ($reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto)) {

                $inclusao = $reg_animal_pasto->tbl_animal_pasto_incluido_em;
                $alteracao = $reg_animal_pasto->tbl_animal_pasto_alterado_em;

                if ($inclusao!='') {
                    if ($inclusao>$ultima_data){
                        $ultima_data=$inclusao;
                    }
                }

                if ($alteracao!='') {
                    if ($alteracao>$ultima_data){
                        $ultima_data=$alteracao;
                    }
                }

                $sexo = $reg_animal_pasto->tbl_animal_pasto_sexo;
                //$codigo_categoria = $reg_animal_pasto->tbl_animal_pasto_categoria;
                $data_nascimento = $reg_animal_pasto->tbl_animal_pasto_nascimento;
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($data_nascimento); 
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                if ($controle_estoque=='I'){
                    $total_animais++;
                    $total_animais_pasto++;

                        $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                            WHERE tab_registro_lixeira_categoria_idade='0'");
                        $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                        while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                            }
                        }

                    if ($sexo=='M') {
                        $total_macho[$codigo_categoria]++;
                        $total_cat_macho[$codigo_categoria]++;
                    }
                    else {
                        $total_femea[$codigo_categoria]++;
                        $total_cat_femea[$codigo_categoria]++;
                    }

                    /*$tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                        WHERE tab_registro_lixeira_categoria_idade='0'");
                    $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                    while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                        $idade_de = $reg_categoria->tab_categoria_idade_de;
                        $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                        if ($idade >= $idade_de && $idade <= $idade_ate) {
                            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

                            if ($sexo=='M') {
                                $total_macho[$codigo_categoria]++;
                                $total_cat_macho[$codigo_categoria]++;
                            }
                            else {
                                $total_femea[$codigo_categoria]++;
                                $total_cat_femea[$codigo_categoria]++;
                            }
                        }
                    }*/
                }
                else {
                    /*$data_nascimento = $reg_animal_pasto->tbl_animal_pasto_nascimento;
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
                    */

                    $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                        WHERE tab_registro_lixeira_categoria_idade='0'");
                    $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                    while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                        $idade_de = $reg_categoria->tab_categoria_idade_de;
                        $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                        if ($idade >= $idade_de && $idade <= $idade_ate) {
                            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                            $total_animais++;
                            $total_animais_pasto++;

                            if ($sexo=='M') {
                                $total_macho[$codigo_categoria]++;
                                $total_cat_macho[$codigo_categoria]++;
                            }
                            else {
                                $total_femea[$codigo_categoria]++;
                                $total_cat_femea[$codigo_categoria]++;
                            }
                        }
                    }
                }
            }

            if ($total_animais_pasto!=0) {
                $linha++;

                $celulas = 'A'.$linha;
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
                $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $descricao);

                $celulas = 'B'.$linha.':O'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'O'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $celulas = 'L'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);

                for ($i = 1; $i <=5; $i++) {
                    $i = str_pad($i, 3, "0", STR_PAD_LEFT);

                    if ($i==1) {
                        $total_M_F = $total_macho[$i] + $total_femea[$i];

                        if ($total_M_F==0) {
                            $total_M_F='';
                        }

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,  $total_M_F);
                    }
                    else if ($i==2) {
                        if ($total_macho[$i]==0) {
                            $total_macho[$i]='';
                        }

                        if ($total_femea[$i]==0) {
                            $total_femea[$i]='';
                        }

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $total_macho[$i]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $total_femea[$i]);
                    }
                    else if ($i==3) {
                        if ($total_macho[$i]==0) {
                            $total_macho[$i]='';
                        }

                        if ($total_femea[$i]==0) {
                            $total_femea[$i]='';
                        }

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $total_macho[$i]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $total_femea[$i]);
                    }
                    else if ($i==4) {
                        if ($total_macho[$i]==0) {
                            $total_macho[$i]='';
                        }

                        if ($total_femea[$i]==0) {
                            $total_femea[$i]='';
                        }

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $total_macho[$i]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $total_femea[$i]);
                    }
                    else if ($i==5) {
                        if ($total_macho[$i]==0) {
                            $total_macho[$i]='';
                        }

                        if ($total_femea[$i]==0) {
                            $total_femea[$i]='';
                        }

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $total_macho[$i]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $total_femea[$i]);
                    }
                }

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $total_animais_pasto);

                $celulas = 'L'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_lote);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $id_lote);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $dias_pasto);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $area);

            }
        }
    }
}

$tbl_pasto= mysqli_query($conector, "SELECT * FROM tbl_pasto
    WHERE tbl_pasto_codigo_local='$local' AND 
          tbl_pasto_modulo!=999");

$num_rows = mysqli_num_rows($tbl_pasto);

if ($num_rows!=0) {
    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
        $descricao = utf8_encode($reg_pasto->tbl_pasto_descricao);
        $codigo_pasto = $reg_pasto->tbl_pasto_id;
        $descricao_lote = utf8_encode($reg_pasto->tbl_pasto_descricao_lote);
        $id_lote = ltrim($reg_pasto->tbl_pasto_id_lote, '0').'/'.
                   substr($reg_pasto->tbl_pasto_ano_lote, 2, 2);
        $area = $reg_pasto->tbl_pasto_area;

        if ($area==0) {
            $area='';
        }

        // Pega dias com animais no pasto
        $dias_pasto = 0;

        $dataAtual = new DateTime();
        $dataAnterior = new DateTime($reg_pasto->tbl_pasto_data_com_animais_anterior);
        $dataCom = new DateTime($reg_pasto->tbl_pasto_data_com_animais);

        if ($dataCom!='') {
            $diff = $dataAtual->diff($dataCom);
            $dias_pasto = $diff->days;
        }

        // Fim pega dias com animais no pasto

        $tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id='$codigo_pasto' AND 
                  tbl_animal_pasto_situacao='A'
            ORDER BY tbl_animal_pasto_nascimento DESC");

        $num_rows_animal = mysqli_num_rows($tbl_animal_pasto);
        $total_animais_pasto = 0;

        for ($i = 1; $i <=5; $i++) {
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
            $total_macho[$j]=0;
            $total_femea[$j]=0;
        }

        //$dias_pasto = 0;

        if ($num_rows_animal!=0) {
            while ($reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto)) {

                $inclusao = $reg_animal_pasto->tbl_animal_pasto_incluido_em;
                $alteracao = $reg_animal_pasto->tbl_animal_pasto_alterado_em;

                if ($inclusao!='') {
                    if ($inclusao>$ultima_data){
                        $ultima_data=$inclusao;
                    }
                }

                if ($alteracao!='') {
                    if ($alteracao>$ultima_data){
                        $ultima_data=$alteracao;
                    }
                }

                $sexo = $reg_animal_pasto->tbl_animal_pasto_sexo;
                //$codigo_categoria = $reg_animal_pasto->tbl_animal_pasto_categoria;
                $data_nascimento = $reg_animal_pasto->tbl_animal_pasto_nascimento;
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($data_nascimento); // Data de Nascimento
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                if ($controle_estoque=='I'){
                    $total_animais++;
                    $total_animais_pasto++;

                        $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                            WHERE tab_registro_lixeira_categoria_idade='0'");
                        $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                        while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                            }
                        }


                    if ($sexo=='M') {
                        $total_macho[$codigo_categoria]++;
                        $total_cat_macho[$codigo_categoria]++;
                    }
                    else {
                        $total_femea[$codigo_categoria]++;
                        $total_cat_femea[$codigo_categoria]++;
                    }

                    /*$tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                        WHERE tab_registro_lixeira_categoria_idade='0'");
                    $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                    while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                        $idade_de = $reg_categoria->tab_categoria_idade_de;
                        $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                        if ($idade >= $idade_de && $idade <= $idade_ate) {
                            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

                            if ($sexo=='M') {
                                $total_macho[$codigo_categoria]++;
                                $total_cat_macho[$codigo_categoria]++;
                            }
                            else {
                                $total_femea[$codigo_categoria]++;
                                $total_cat_femea[$codigo_categoria]++;
                            }
                        }
                    }*/
                }
                else {
                    $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                        WHERE tab_registro_lixeira_categoria_idade='0'");
                    $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                    while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                        $idade_de = $reg_categoria->tab_categoria_idade_de;
                        $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                        if ($idade >= $idade_de && $idade <= $idade_ate) {
                            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                            $total_animais++;
                            $total_animais_pasto++;

                            if ($sexo=='M') {
                                $total_macho[$codigo_categoria]++;
                                $total_cat_macho[$codigo_categoria]++;
                            }
                            else {
                                $total_femea[$codigo_categoria]++;
                                $total_cat_femea[$codigo_categoria]++;
                            }
                        }
                    }
                }
            }

            if ($total_animais_pasto!=0) {
                $linha++;

                $celulas = 'A'.$linha;
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
                $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $descricao);

                $celulas = 'B'.$linha.':O'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'O'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $celulas = 'L'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);

                for ($i = 1; $i <=5; $i++) {
                    $i = str_pad($i, 3, "0", STR_PAD_LEFT);

                    if ($i==1) {
                        $total_M_F = $total_macho[$i] + $total_femea[$i];

                        if ($total_M_F==0) {
                            $total_M_F='';
                        }

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,  $total_M_F);
                    }
                    else if ($i==2) {
                        if ($total_macho[$i]==0) {
                            $total_macho[$i]='';
                        }

                        if ($total_femea[$i]==0) {
                            $total_femea[$i]='';
                        }

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $total_macho[$i]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $total_femea[$i]);
                    }
                    else if ($i==3) {
                        if ($total_macho[$i]==0) {
                            $total_macho[$i]='';
                        }

                        if ($total_femea[$i]==0) {
                            $total_femea[$i]='';
                        }
                        
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $total_macho[$i]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $total_femea[$i]);
                    }
                    else if ($i==4) {
                        if ($total_macho[$i]==0) {
                            $total_macho[$i]='';
                        }

                        if ($total_femea[$i]==0) {
                            $total_femea[$i]='';
                        }

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $total_macho[$i]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $total_femea[$i]);
                    }
                    else if ($i==5) {
                        if ($total_macho[$i]==0) {
                            $total_macho[$i]='';
                        }

                        if ($total_femea[$i]==0) {
                            $total_femea[$i]='';
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $total_macho[$i]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $total_femea[$i]);
                    }
                }
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $total_animais_pasto);
                $celulas = 'L'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_lote);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $id_lote);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $dias_pasto);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $area);

            }
        }
    }
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 3, $total_animais);
}

for ($i = 1; $i <=5; $i++) {
    $i = str_pad($i, 3, "0", STR_PAD_LEFT);

    if ($i==1) {
        $total_cat_M_F = intval($total_cat_macho[$i]) + intval($total_cat_femea[$i]);
    }
}

if ($total_cat_M_F==0) {
    $total_cat_M_F='';
}         

if ($total_cat_macho['002']==0) {
    $total_cat_macho['002']='';
}         

if ($total_cat_femea['002']==0) {
    $total_cat_femea['002']='';
}       

if ($total_cat_macho['003']==0) {
    $total_cat_macho['003']='';
}         

if ($total_cat_femea['003']==0) {
    $total_cat_femea['003']='';
}       

if ($total_cat_macho['004']==0) {
    $total_cat_macho['004']='';
}         

if ($total_cat_femea['004']==0) {
    $total_cat_femea['004']='';
}       

if ($total_cat_macho['005']==0) {
    $total_cat_macho['005']='';
}         

if ($total_cat_femea['005']==0) {
    $total_cat_femea['005']='';
}       

$linha++;

$celulas = 'A'.$linha;
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

$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, 'Totais Animais');

$celulas = 'B'.$linha.':O'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);

$celulas = 'A'.$linha.':O'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('DCDCDC');

$celulas = 'L'.$linha.':O'.$linha;
$spreadsheet->getActiveSheet()->mergeCells($celulas);
$spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,  $total_cat_M_F);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $total_cat_macho['002']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $total_cat_femea['002']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $total_cat_macho['003']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $total_cat_femea['003']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $total_cat_macho['004']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $total_cat_femea['004']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $total_cat_macho['005']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $total_cat_femea['005']);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $total_animais);

$date = new DateTime( $ultima_data );

$linha++;
$celulas = 'A'.$linha.':C'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha,'Última Atualização: ' . $date->format('d-m-Y H:i'));

// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="mapa_gado.xlsx"');
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