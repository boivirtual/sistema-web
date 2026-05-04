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
$servidor = "localhost";
$usuario_bd = "root";
$banco = $cnpj_cliente;
//$senha_bd = "";

// Servidor
$senha_bd = "a2ngei9Mxh";

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
$ano = $_REQUEST["ano"];

$ano_inicial = $ano - 4;
$ano_final = $ano;

$descricao_filtro= $_REQUEST["descricao_filtro"];

@ session_start(); 

$cnpj_empresa = $_SESSION['id_cliente'];

$empresa = mysqli_query($conector, "SELECT tbl_empresa_nome FROM tbl_empresa
                                     WHERE tbl_empresa_cpf_cnpj >='$cnpj_empresa'"); 
$registro_empresa = mysqli_fetch_object($empresa);  
$nome_empresa = utf8_encode($registro_empresa->tbl_empresa_nome);

$nome_relatorio = "Registro Anual de Chuvas";

$spreadsheet->getActiveSheet()->mergeCells('A1:Z1');
$spreadsheet->getActiveSheet()->mergeCells('AA1:AC1');
$spreadsheet->getActiveSheet()->mergeCells('B2:AC2');
$spreadsheet->getActiveSheet()->mergeCells('A3:AC3');
$spreadsheet->getActiveSheet()->mergeCells('P4:T4');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
	->setCellValue("AA1", "Data: " . $data_sistema)
	->setCellValue("A2", "Filtro: ")
	->setCellValue("B2", $descricao_filtro)
	->setCellValue("P4", "Histórico dos últimos 5 anos");

$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('P4')->getFont()->setColor(new Color(Color::COLOR_GRAY));

$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A4","Dia")
    ->setCellValue("B4","Jan")
    ->setCellValue("C4","Fev")
    ->setCellValue("D4","Mar")
    ->setCellValue("E4","Abr")
    ->setCellValue("F4","Mai")
    ->setCellValue("G4","Jun")
    ->setCellValue("H4","Jul")
    ->setCellValue("I4","Ago")
    ->setCellValue("J4","Set")
    ->setCellValue("K4","Out")
    ->setCellValue("L4","Nov")
    ->setCellValue("M4","Dez")
    ->setCellValue("N4","Total")
    ->setCellValue("A36","mm")
    ->setCellValue("A37","Dias");

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("P6","Ano")
    ->setCellValue("Q6","Jan")
    ->setCellValue("R6","Fev")
    ->setCellValue("S6","Mar")
    ->setCellValue("T6","Abr")
    ->setCellValue("U6","Mai")
    ->setCellValue("V6","Jun")
    ->setCellValue("W6","Jul")
    ->setCellValue("X6","Ago")
    ->setCellValue("Y6","Set")
    ->setCellValue("Z6","Out")
    ->setCellValue("AA6","Nov")
    ->setCellValue("AB6","Dez")
    ->setCellValue("AC6","Total");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(8);

$spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('AA')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('AB')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('AC')->setWidth(6);

$spreadsheet->getActiveSheet()->getStyle('AA1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$spreadsheet->getActiveSheet()->getStyle('A4:AC4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A36:N36')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A37:N37')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('P4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

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

    $spreadsheet->getActiveSheet()->getStyle('A1:AC1')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('A2:AC2')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('A3:AC3')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B2:AC2')->applyFromArray($styleArray);

$linha=4;

$total_volume_ano_atual[$ano]=0;
$total_dias_ano_atual[$ano]=0;

for ($i = 0; $i <= 24; $i++) {
    $valor[$i]=0;
}

for ($a=$ano_inicial; $a<=$ano_final ; $a++) { 
	$total_volume_ano[$a]=0;

	for ($m=1; $m <=12; $m++) { 
       	$mes = ltrim($m, "0");
		$volume_anual[$a][$mes]=0;
	}
}

for ($m=1; $m <=12; $m++) { 
    $mes = ltrim($m, "0");
    $total_volume_mes[$mes]=0;

    $dias_chuva[$mes]=0;
    for ($d=1; $d <=31; $d++) { 
        $dia = ltrim($d, "0");
        $volume[$dia][$mes]='';
    }
}

$chuva= mysqli_query($conector, "SELECT * FROM tbl_chuva
    WHERE year(tbl_chuva_data) = '$ano' AND tbl_chuva_local='$local'");

$num_rows = mysqli_num_rows($chuva);  

if ($num_rows!=0) {
    while ($reg_chuva = mysqli_fetch_object($chuva)) {
        $data_chuva=new DateTime($reg_chuva->tbl_chuva_data);
        $mes_chuva=$data_chuva->format('m');
        $dia_chuva=$data_chuva->format('d');
        $ano_chuva=$data_chuva->format('Y');

        $mes_chuva=ltrim($mes_chuva, "0");
        $dia_chuva=ltrim($dia_chuva, "0");

        $volume[$dia_chuva][$mes_chuva]=$reg_chuva->tbl_chuva_volume_chuva;

        if ($reg_chuva->tbl_chuva_volume_chuva!=0 && 
            $reg_chuva->tbl_chuva_volume_chuva!='') {

            $total_volume_mes[$mes_chuva]+=$reg_chuva->tbl_chuva_volume_chuva;
            $dias_chuva[$mes_chuva]++;

            $total_volume_ano_atual[$ano_chuva]+=$reg_chuva->tbl_chuva_volume_chuva;
            $total_dias_ano_atual[$ano_chuva]++;
        }
    }
}

$lin_grade=3;

for ($d=1; $d <=34; $d++) { 
    $lin_grade++;
    $celulas = 'A'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'B'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'C'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'D'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'E'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'F'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'G'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'H'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'I'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'J'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'K'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'L'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'M'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'N'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
}

for ($d=1; $d <=31; $d++) { 
    $dia = ltrim($d, "0");

    $linha++;
    $celulas = 'A'.$linha.':N'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $dia);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $volume[$dia][1]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $volume[$dia][2]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $volume[$dia][3]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $volume[$dia][4]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $volume[$dia][5]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $volume[$dia][6]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $volume[$dia][7]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $volume[$dia][8]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $volume[$dia][9]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $volume[$dia][10]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $volume[$dia][11]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $volume[$dia][12]);
}

for ($m=1; $m <=12; $m++) {
    $mes = ltrim($m, "0");
    if ($total_volume_mes[$mes]==0){
        $total_volume_mes[$mes]='';
    } 
    if ($dias_chuva[$mes]==0){
        $dias_chuva[$mes]='';
    } 
}

$spreadsheet->getActiveSheet()->getStyle('A4:N4')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A4:N4')->getFill()->getStartColor()->setARGB('D6DBDF');

$spreadsheet->getActiveSheet()->getStyle('P6:AC6')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('P6:AC6')->getFill()->getStartColor()->setARGB('D6DBDF');

$spreadsheet->getActiveSheet()->getStyle('A36:N36')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A36:N36')->getFill()->getStartColor()->setARGB('EBEDEF');
$spreadsheet->getActiveSheet()->getStyle('A37:N37')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A37:N37')->getFill()->getStartColor()->setARGB('D6DBDF');


$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 36, $total_volume_mes[1]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 36, $total_volume_mes[2]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 36, $total_volume_mes[3]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, 36, $total_volume_mes[4]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, 36, $total_volume_mes[5]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, 36, $total_volume_mes[6]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, 36, $total_volume_mes[7]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, 36, $total_volume_mes[8]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, 36, $total_volume_mes[9]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, 36, $total_volume_mes[10]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, 36, $total_volume_mes[11]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, 36, $total_volume_mes[12]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, 36, $total_volume_ano_atual[$ano]);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 37, $dias_chuva[1]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 37, $dias_chuva[2]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 37, $dias_chuva[3]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, 37, $dias_chuva[4]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, 37, $dias_chuva[5]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, 37, $dias_chuva[6]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, 37, $dias_chuva[7]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, 37, $dias_chuva[8]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, 37, $dias_chuva[9]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, 37, $dias_chuva[10]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, 37, $dias_chuva[11]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, 37, $dias_chuva[12]);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, 37, $total_dias_ano_atual[$ano]);


$lin_grade=5;

for ($d=1; $d <=6; $d++) { 
    $lin_grade++;
    $celulas = 'P'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'Q'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'R'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'S'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'T'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'U'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'V'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'W'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'X'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'Y'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'Z'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'AA'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'AB'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
    $celulas = 'AC'.$lin_grade;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleArray);
}

$chuva= mysqli_query($conector, "SELECT * FROM tbl_chuva
    WHERE year(tbl_chuva_data)>= '$ano_inicial' AND
          year(tbl_chuva_data)<= '$ano_final' AND
          tbl_chuva_local='$local'");

$num_rows = mysqli_num_rows($chuva);  

if ($num_rows!=0) {
    while ($reg_chuva = mysqli_fetch_object($chuva)) {
        $data_chuva=new DateTime($reg_chuva->tbl_chuva_data);
        $mes_chuva=$data_chuva->format('m');
        $ano_chuva=$data_chuva->format('Y');

        $mes_chuva=ltrim($mes_chuva, "0");

        if ($reg_chuva->tbl_chuva_volume_chuva!=0 && 
            $reg_chuva->tbl_chuva_volume_chuva!='') {

           	$volume_anual[$ano_chuva][$mes_chuva]+=$reg_chuva->tbl_chuva_volume_chuva;
            $total_volume_ano[$ano_chuva]+=$reg_chuva->tbl_chuva_volume_chuva;
        }
    }
}

$linha=6;

for ($a=$ano_inicial; $a<=$ano_final; $a++) { 
    for ($m=1; $m <=12; $m++) {
        $mes = ltrim($m, "0");
        if ($volume_anual[$a][$mes]==0){
            $volume_anual[$a][$mes]='';
        } 
    }

    $linha++;
    $celulas = 'P'.$linha.':AC'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(16, $linha, $a);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(17, $linha, $volume_anual[$a][1]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(18, $linha, $volume_anual[$a][2]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(19, $linha, $volume_anual[$a][3]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(20, $linha, $volume_anual[$a][4]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(21, $linha, $volume_anual[$a][5]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(22, $linha, $volume_anual[$a][6]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(23, $linha, $volume_anual[$a][7]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(24, $linha, $volume_anual[$a][8]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(25, $linha, $volume_anual[$a][9]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(26, $linha, $volume_anual[$a][10]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(27, $linha, $volume_anual[$a][11]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(28, $linha, $volume_anual[$a][12]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(29, $linha, $total_volume_ano[$a]);
}

// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="registro_anual_chuva.xlsx"');
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