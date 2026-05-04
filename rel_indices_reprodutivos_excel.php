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
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    $local_filtro = $_REQUEST["local"];
    $estacao_filtro = $_REQUEST["estacao_monta"];
    $tipo_cobertura = $_REQUEST["tipo_cobertura"];
    $periodo_de = $_REQUEST["periodo_de"];
    $periodo_ate = $_REQUEST["periodo_ate"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

    $local= array();
    $matriz_itens = explode(",", $local_filtro);
    $quantidade_fazendas = count($matriz_itens);

    for($i=0; $i < $quantidade_fazendas; $i++) {
        $local[$i]=$matriz_itens[$i];
    }

    $local = implode(',', $local);
    $local = substr($local,0, -1);

    $wlocal = '';

    if ($local_filtro!='') {
        $wlocal = " AND tbl_cobertura_codigo_local IN(";
        $wlocal.= $local;
        $wlocal.= ")";
    }

    if ($tipo_cobertura=='I') {
        $estacao= array();
        $matriz_itens = explode(",", $estacao_filtro);
        $quantidade_estacoes = count($matriz_itens);

        for($i=0; $i < $quantidade_estacoes; $i++) {
            $estacao[$i]=$matriz_itens[$i];
        }

        $estacao = implode(',', $estacao);
        $estacao = substr($estacao,0, -1);

        $westacao = '';

        if ($estacao_filtro!='') {
            $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(";
            $westacao.= $estacao;
            $westacao.= ")";
        }
    }

$nome_relatorio = "Índices Reprodutivos";

$spreadsheet->getActiveSheet()->mergeCells('A1:M1');
$spreadsheet->getActiveSheet()->mergeCells('N1:O1');
$spreadsheet->getActiveSheet()->mergeCells('B2:O2');
$spreadsheet->getActiveSheet()->mergeCells('A3:O3');
$spreadsheet->getActiveSheet()->mergeCells('A4:I4');
$spreadsheet->getActiveSheet()->mergeCells('K4:O4');
$spreadsheet->getActiveSheet()->mergeCells('J4:J5');

$spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue("N1", "Data: " . $data_sistema)
        ->setCellValue("A2", "Filtro: ")
        ->setCellValue("B2", $descricao_filtro)
        ->setCellValue("A4", 'Dados')
        ->setCellValue("K4", 'Índices');

/*$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);
*/

$spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A5","Estação de Monta")
            ->setCellValue("B5","Fazenda")
            ->setCellValue("C5","Qtd Fêmeas")
            ->setCellValue("D5","Qtd Coberturas")
            ->setCellValue("E5","Qtd Positivas")
            ->setCellValue("F5","Nascidos")
            ->setCellValue("G5","Abortos")
            ->setCellValue("H5","Natimorto")
            ->setCellValue("I5","Desmamados")
            ->setCellValue("K5","Eficiência do Serviço")
            ->setCellValue("L5","Taxa Prenhez")
            ->setCellValue("M5","Taxa Natalidade")
            ->setCellValue("N5","Taxa Gestação")
            ->setCellValue("O5","Taxa Desmame");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(2);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(10);

$spreadsheet->getActiveSheet()->getStyle('A1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('K4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->getStyle('M1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A5:O5')->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A5:O5')->getAlignment()->setVertical($align);

$spreadsheet->getActiveSheet()->getStyle('A5:O5')->getAlignment()->setWrapText(true);

$spreadsheet->getActiveSheet()->getStyle('A5:I5')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A5:I5')->getFill()->getStartColor()->setARGB('EBEDEF');
$spreadsheet->getActiveSheet()->getStyle('K5:O5')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('K5:O5')->getFill()->getStartColor()->setARGB('EBEDEF');
$spreadsheet->getActiveSheet()->getStyle('A5:O5')->getFont()->setSize(8);

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

$spreadsheet->getActiveSheet()->getStyle('A1:O1')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A2:O2')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A5:I5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('K5:O5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('E5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('F5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('G5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('H5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('J5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('K5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('L5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('M5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('N5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('O5')->applyFromArray($styleArray);

$linha=5;

    if ($tipo_cobertura=='I') {
        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
            INNER JOIN tbl_pessoa
                    ON tbl_pessoa_id = tbl_cobertura_codigo_local
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_cobertura_controle = 'C' AND 
                  tbl_cobertura_encerrada='S'" . $wlocal . $westacao . 
            "ORDER BY tbl_cobertura_codigo_estacao_monta ASC, 
                      tbl_ite_cobertura_codigo_id_animal ASC"); 
    }
    else {
        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
            INNER JOIN tbl_pessoa
                    ON tbl_pessoa_id = tbl_cobertura_codigo_local
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_cobertura_controle = 'M' AND 
                  tbl_ite_cobertura_data_prenhes>='$periodo_de' AND
                  tbl_ite_cobertura_data_prenhes<='$periodo_ate' AND 
                  tbl_cobertura_encerrada='S'" . $wlocal . 
            "ORDER BY tbl_cobertura_codigo_local ASC,
                      tbl_ite_cobertura_codigo_id_animal ASC"); 
    }

    $num_rows = mysqli_num_rows($tbl_item_cobertura);

    $estacao_anterior = 0;
    $local_anterior = 0;
    $animal_anterior = 0;
    $qtd_femeas = 0;
    $qtd_coberturas = 0;
    $qtd_positivos = 0;
    $qtd_nascidos = 0;
    $qtd_aborto = 0;
    $qtd_natimorto = 0;
    $qtd_desmame = 0;
    $indice = 0;
    $array_total_coberturas = array();
    $array_total_femeas = array();
    $array_total_eficiencia = array();
    $array_total_positivos = array();
    $array_total_nascidos = array();
    $array_total_aborto_natimorto = array();

    $total_coberturas = 0;
    $total_femeas = 0;
    $total_positivos = 0;
    $total_nascidos = 0;
    $total_abortos = 0;
    $total_natimorto = 0;
    $total_desmame = 0;

    $sub_categoria=array();
    $sub_qtd_femeas=array();
    $sub_qtd_coberturas=array();
    $sub_qtd_positivos=array();
    $sub_qtd_nascidos=array();
    $sub_qtd_aborto=array();
    $sub_qtd_natimorto=array();
    $sub_qtd_desmame=array();
    $sub_eficiencia_servico=array();
    $sub_taxa_prenhez=array();
    $sub_taxa_natalidade=array();
    $sub_perda_gestacao=array();
    $sub_taxa_desmame=array();

    for ($i=0; $i < 3; $i++) { 
        $sub_categoria[$i]='';
        $sub_qtd_femeas[$i]=0;
        $sub_qtd_coberturas[$i]=0;
        $sub_qtd_positivos[$i]=0;
        $sub_qtd_nascidos[$i]=0;
        $sub_qtd_aborto[$i]=0;
        $sub_qtd_natimorto[$i]=0;
        $sub_qtd_desmame[$i]=0;
        $sub_eficiencia_servico[$i]=0;
        $sub_taxa_prenhez[$i]=0;
        $sub_taxa_natalidade[$i]=0;
        $sub_perda_gestacao[$i]=0;
        $sub_taxa_desmame[$i]=0;
    }

    if ($num_rows!=0) {
        while ($reg_cobertura = mysqli_fetch_object($tbl_item_cobertura)){
            $codigo_id_animal = $reg_cobertura->tbl_ite_cobertura_codigo_id_animal;
            $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
            $codigo_estacao = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
            $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
            $nascidos = $reg_cobertura->tbl_ite_cobertura_nascido;
            $cobertura_id = $reg_cobertura->tbl_ite_cobertura_numero_id;
            $item_cobertura = $reg_cobertura->tbl_ite_cobertura_numero_item;

            // Verifica numero de partos
            $tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais
                WHERE tbl_animal_codigo_mae = '$codigo_id_animal'");  

            $num_rows = mysqli_num_rows($tbl_animais);

            if ($num_rows==0) {
                $sub_categoria[0]='Novilhas';
                $categoria_animal='N';
            }
            else if ($num_rows==1) {
                $sub_categoria[1]='Primiparas';
                $categoria_animal='P';
            }
            else {
                $sub_categoria[2]='Multiparas';
                $categoria_animal='M';
            }

            if ($tipo_cobertura=='I') { // IATF
                //if ($protocoloiatf_tipo == $tipo_cobertura[$i]) {

                    if ($codigo_estacao!=$estacao_anterior) {
                        if ($estacao_anterior==0){
                            $estacao_anterior=$codigo_estacao;
                            $desc_local = $reg_cobertura->tbl_pessoa_nome;

                            $sql = mysqli_query($conector, "SELECT * FROM
                                tbl_parametro_estacao_monta
                                WHERE tbl_par_estacao_id = '$codigo_estacao'");  

                            $num_rows = mysqli_num_rows($sql);

                            if ($num_rows!=0) {
                                $reg_estacao = mysqli_fetch_object($sql);
                                $desc_estacao_monta = $reg_estacao->tbl_par_estacao_nome;
                            }
                            else {
                                $desc_estacao_monta = '';
                            }
                        }
                        else {
                            for ($j=0; $j < 3; $j++) { 
                                $sub_eficiencia_servico[$j] = $sub_qtd_coberturas[$j]/$sub_qtd_femeas[$j];

                                $sub_taxa_prenhez[$j] = ($sub_qtd_positivos[$j]/$sub_qtd_femeas[$j])*100;
                                $sub_taxa_natalidade[$j] = ($sub_qtd_nascidos[$j]/$sub_qtd_positivos[$j])*100;
                                $sub_perda_gestacao[$j] = (($sub_qtd_aborto[$j]+$sub_qtd_natimorto[$j])/$sub_qtd_positivos[$j])*100;
                                $sub_taxa_desmame[$j] = ($sub_qtd_desmame[$j]/$sub_qtd_femeas[$j])*100;
                            }

                            $eficiencia_servico = $qtd_coberturas/$qtd_femeas;

                            $total_coberturas+= $qtd_coberturas;
                            $total_femeas+= $qtd_femeas;

                            $taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
                            $taxa_prenhez = number_format($taxa_prenhez,2,'.','.');
                            $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
                            $taxa_natalidade = number_format($taxa_natalidade,2,'.','.');
                            $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
                            $perda_gestacao = number_format($perda_gestacao,2,'.','.');
                            $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;
                            $taxa_desmame = number_format($taxa_desmame,2,'.','.');

                        $linha++;

                        $celulas = 'K'.$linha.':O'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                        $celulas = 'A'.$linha.':I'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $spreadsheet->getActiveSheet()->getStyle('B'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                        $celulas = 'K'.$linha.':O'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha,$desc_estacao_monta);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$desc_local);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd_femeas);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $qtd_coberturas);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $qtd_positivos);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $qtd_nascidos);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $qtd_aborto);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $qtd_natimorto);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $qtd_desmame);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $eficiencia_servico);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $taxa_prenhez.' %');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $taxa_natalidade.' %');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $perda_gestacao.' %');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $taxa_desmame.' %');


                            for ($i=0; $i < 3; $i++) { 
                                if ($sub_categoria[$i]!='') {
                                    $linha++;

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                                    $celulas = 'A'.$linha.':I'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $celulas = 'A'.$linha.':O'.$linha;

                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

                                    $sub_taxa_prenhez[$i] = number_format($sub_taxa_prenhez[$i],2,'.','.');
                                    $sub_taxa_natalidade[$i] = number_format($sub_taxa_natalidade[$i],2,'.','.');
                                    $sub_perda_gestacao[$i] = number_format($sub_perda_gestacao[$i],2,'.','.');
                                    $sub_taxa_desmame[$i] = number_format($sub_taxa_desmame[$i],2,'.','.');

                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$sub_categoria[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sub_qtd_femeas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $sub_qtd_coberturas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $sub_qtd_positivos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $sub_qtd_nascidos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $sub_qtd_aborto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $sub_qtd_natimorto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $sub_qtd_desmame[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $sub_eficiencia_servico[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $sub_taxa_prenhez[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $sub_taxa_natalidade[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $sub_perda_gestacao[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $sub_taxa_desmame[$i].' %');
                                }
                            }

                            /*$taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
                            $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
                            $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
                            $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;

                            echo '<tr style="font-weight: 700;">
                                <td>'.$desc_estacao_monta.'</td>
                                <td style="font-size: 10px;">'.$desc_local.'</td>
                                <td style="text-align: right;">'.$qtd_femeas.'</td>
                                <td style="text-align: right;">'.$qtd_coberturas.'</td>
                                <td style="text-align: right;">'.$qtd_positivos.'</td>
                                <td style="text-align: right;">'.$qtd_nascidos.'</td>
                                <td style="text-align: right;">'.$qtd_aborto.'</td>
                                <td style="text-align: right;">'.$qtd_natimorto.'</td>
                                <td style="text-align: right;">'.$qtd_desmame.'</td>
                                <td style="border: none;"></td>
                                <td style="text-align: right;">'.number_format($eficiencia_servico,2,',','.').'</td>
                                <td style="text-align: right;">'.number_format($taxa_prenhez,2,',','.').' %</td>
                                <td style="text-align: right;">'.number_format($taxa_natalidade,2,',','.').' %</td>
                                <td style="text-align: right;">'.number_format($perda_gestacao,2,',','.').' %</td>
                                <td style="text-align: right;">'.number_format($taxa_desmame,2,',','.').' %</td>
                            </tr>';

                            for ($i=0; $i < 3; $i++) { 
                                if ($sub_categoria[$i]!='') {
                                    echo '<tr style="color: #a5a7a8">
                                        <td></td>
                                        <td  style="font-size: 10px;text-align: right;">'.$sub_categoria[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_femeas[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_coberturas[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_positivos[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_nascidos[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_aborto[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_natimorto[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_desmame[$i].'</td>
                                        <td style="border: none;"></td>
                                        <td style="text-align: right;">'.number_format($sub_eficiencia_servico[$i],2,',','.').'</td>
                                        <td style="text-align: right;">'.number_format($sub_taxa_prenhez[$i],2,',','.').' %</td>
                                        <td style="text-align: right;">'.number_format($sub_taxa_natalidade[$i],2,',','.').' %</td>
                                        <td style="text-align: right;">'.number_format($sub_perda_gestacao[$i],2,',','.').' %</td>
                                        <td style="text-align: right;">'.number_format($sub_taxa_desmame[$i],2,',','.').' %</td>
                                    </tr>';
                                }
                            }*/

                            $estacao_anterior=$codigo_estacao;
                            $desc_local = $reg_cobertura->tbl_pessoa_nome;

                            $sql = mysqli_query($conector, "SELECT * FROM
                                tbl_parametro_estacao_monta
                                WHERE tbl_par_estacao_id = '$codigo_estacao'");  

                            $num_rows = mysqli_num_rows($sql);

                            if ($num_rows!=0) {
                                $reg_estacao = mysqli_fetch_object($sql);
                                $desc_estacao_monta = $reg_estacao->tbl_par_estacao_nome;
                            }
                            else {
                                $desc_estacao_monta = '';
                            }

                            $animal_anterior = 0;
                            $qtd_femeas = 0;
                            $qtd_coberturas = 0;
                            $qtd_positivos = 0;
                            $qtd_nascidos = 0;
                            $qtd_aborto = 0;
                            $qtd_natimorto = 0;
                            $qtd_desmame = 0;

                            for ($i=0; $i < 3; $i++) { 
                                $sub_qtd_femeas[$i]=0;
                                $sub_qtd_coberturas[$i]=0;
                                $sub_qtd_positivos[$i]=0;
                                $sub_qtd_nascidos[$i]=0;
                                $sub_qtd_aborto[$i]=0;
                                $sub_qtd_natimorto[$i]=0;
                                $sub_qtd_desmame[$i]=0;
                                $sub_eficiencia_servico[$i]=0;
                                $sub_taxa_prenhez[$i]=0;
                                $sub_taxa_natalidade[$i]=0;
                                $sub_perda_gestacao[$i]=0;
                                $sub_taxa_desmame[$i]=0;
                            }
                        }
                    }

                    $qtd_coberturas++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_coberturas[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_coberturas[1]++;
                    }   
                    else {
                        $sub_qtd_coberturas[2]++;
                    }                    

                    if ($codigo_id_animal!=$animal_anterior) {
                        $qtd_femeas++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_femeas[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_femeas[1]++;
                        }   
                        else {
                            $sub_qtd_femeas[2]++;
                        }                    

                        $animal_anterior=$codigo_id_animal;

                        // verifica desmama 
                        $sql = mysqli_query($conector, "SELECT * FROM tbl_animais
                            INNER JOIN tbl_item_pesagem 
                                    ON tbl_ite_pesagem_codigo_id_animal=tbl_animal_codigo_id 
                            INNER JOIN tbl_pesagem
                                    ON tbl_pesagem_id  = tbl_ite_pesagem_numero_id 
                            WHERE tbl_animal_codigo_mae = '$codigo_id_animal' AND  tbl_pesagem_codigo_epoca = 2 AND 
                                  tbl_animal_estacao_monta_nascimento = '$codigo_estacao'");  

                        // verificar os animais que tiveram peso de desmama independente de estar ativo ou não. Não precisa considerar a idade 
                        $num_rows = mysqli_num_rows($sql);

                        if ($num_rows!=0) {
                            while ($reg_animal = mysqli_fetch_object($sql)) {
                                /*$data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
                                //VER AQUI QUANDO O ANIMAL FOI VENDIDO, MORTO OU OUTRA SAIDA PARA CALCULAR A IDADE. SO VALE PARA ANIMAIS DESMAMADOS <= DATA DA MOVIMENTACAO E TIVEREM > 7 MESES                          
                                $data_acompanhamento_calculo = date("Y-m-d");
                                $date = new DateTime($data_nascimento); // Data de Nascimento
                                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                                $idade_animal = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
                                */
                                //if ($idade_animal>=7) {
                                    $qtd_desmame++;
                                    $total_desmame++;

                                    if ($categoria_animal=='N') {
                                        $sub_qtd_desmame[0]++;
                                    }
                                    else if ($categoria_animal=='P') {
                                        $sub_qtd_desmame[1]++;
                                    }   
                                    else {
                                        $sub_qtd_desmame[2]++;
                                    }                    
                                //}
                            }
                        }
                    }

                    if ($diagnostico == 'P') {
                        $qtd_positivos++;
                        $total_positivos++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_positivos[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_positivos[1]++;
                        }   
                        else {
                            $sub_qtd_positivos[2]++;
                        }                    
                    }

                    if ($nascidos == 'N' and $diagnostico == 'P') {
                        $qtd_nascidos++;
                        $total_nascidos++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_nascidos[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_nascidos[1]++;
                        }   
                        else {
                            $sub_qtd_nascidos[2]++;
                        }                    
                    }
                    else if ($nascidos == 'A') {
                        $qtd_aborto++;
                        $total_abortos++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_aborto[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_aborto[1]++;
                        }   
                        else {
                            $sub_qtd_aborto[2]++;
                        }                    
                    }
                    else if ($nascidos == 'M') {
                        $qtd_natimorto++;
                        $total_natimorto++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_natimorto[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_natimorto[1]++;
                        }   
                        else {
                            $sub_qtd_natimorto[2]++;
                        }                    
                    }

                    // VER O QUE FAZER QUANDO NASCIDOS FOR 'OUTRO' VENDA, MORTE, OURA SAIDA
                //}
            }
            else { // Monta
                if ($codigo_local!=$local_anterior) {
                    if ($local_anterior==0){
                        $local_anterior=$codigo_local;
                        $desc_local = $reg_cobertura->tbl_pessoa_nome;
                    }
                    else {
                        for ($j=0; $j < 3; $j++) { 
                            $sub_eficiencia_servico[$j] = $sub_qtd_coberturas[$j]/$sub_qtd_femeas[$j];
                            $sub_taxa_prenhez[$j] = ($sub_qtd_positivos[$j]/$sub_qtd_femeas[$j])*100;
                            $sub_taxa_natalidade[$j] = ($sub_qtd_nascidos[$j]/$sub_qtd_positivos[$j])*100;
                            $sub_perda_gestacao[$j] = (($sub_qtd_aborto[$j]+$sub_qtd_natimorto[$j])/$sub_qtd_positivos[$j])*100;
                            $sub_taxa_desmame[$j] = ($sub_qtd_desmame[$j]/$sub_qtd_femeas[$j])*100;
                        }

                        $eficiencia_servico = $qtd_coberturas/$qtd_femeas;

                        $total_coberturas+= $qtd_coberturas;
                        $total_femeas+= $qtd_femeas;

                            $taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
                            $taxa_prenhez = number_format($taxa_prenhez,2,'.','.');
                            $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
                            $taxa_natalidade = number_format($taxa_natalidade,2,'.','.');
                            $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
                            $perda_gestacao = number_format($perda_gestacao,2,'.','.');
                            $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;
                            $taxa_desmame = number_format($taxa_desmame,2,'.','.');

                        $linha++;

                        $celulas = 'K'.$linha.':O'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                        $celulas = 'A'.$linha.':I'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $spreadsheet->getActiveSheet()->getStyle('B'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                        $celulas = 'K'.$linha.':O'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha,'Monta');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$desc_local);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd_femeas);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $qtd_coberturas);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $qtd_positivos);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $qtd_nascidos);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $qtd_aborto);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $qtd_natimorto);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $qtd_desmame);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $eficiencia_servico);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $taxa_prenhez.' %');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $taxa_natalidade.' %');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $perda_gestacao.' %');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $taxa_desmame.' %');


                            for ($i=0; $i < 3; $i++) { 
                                if ($sub_categoria[$i]!='') {
                                    $linha++;

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                                    $celulas = 'A'.$linha.':I'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $celulas = 'A'.$linha.':O'.$linha;

                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

                                    $sub_taxa_prenhez[$i] = number_format($sub_taxa_prenhez[$i],2,'.','.');
                                    $sub_taxa_natalidade[$i] = number_format($sub_taxa_natalidade[$i],2,'.','.');
                                    $sub_perda_gestacao[$i] = number_format($sub_perda_gestacao[$i],2,'.','.');
                                    $sub_taxa_desmame[$i] = number_format($sub_taxa_desmame[$i],2,'.','.');

                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$sub_categoria[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sub_qtd_femeas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $sub_qtd_coberturas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $sub_qtd_positivos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $sub_qtd_nascidos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $sub_qtd_aborto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $sub_qtd_natimorto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $sub_qtd_desmame[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $sub_eficiencia_servico[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $sub_taxa_prenhez[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $sub_taxa_natalidade[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $sub_perda_gestacao[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $sub_taxa_desmame[$i].' %');
                                }
                            }

                        /*$taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
                        $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
                        $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
                        $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;

                        echo '<tr style="font-weight: 700;">
                            <td>Monta</td>
                            <td style="font-size: 10px;">'.$desc_local.'</td>
                            <td style="text-align: right;">'.$qtd_femeas.'</td>
                            <td style="text-align: right;">'.$qtd_coberturas.'</td>
                            <td style="text-align: right;">'.$qtd_positivos.'</td>
                            <td style="text-align: right;">'.$qtd_nascidos.'</td>
                            <td style="text-align: right;">'.$qtd_aborto.'</td>
                            <td style="text-align: right;">'.$qtd_natimorto.'</td>
                            <td style="text-align: right;">'.$qtd_desmame.'</td>
                            <td style="border: none;"></td>
                            <td style="text-align: right;">'.number_format($eficiencia_servico,2,',','.').'</td>
                            <td style="text-align: right;">'.number_format($taxa_prenhez,2,',','.').' %</td>
                            <td style="text-align: right;">'.number_format($taxa_natalidade,2,',','.').' %</td>
                            <td style="text-align: right;">'.number_format($perda_gestacao,2,',','.').' %</td>
                            <td style="text-align: right;">'.number_format($taxa_desmame,2,',','.').' %</td>
                            </tr>';

                        for ($i=0; $i < 3; $i++) { 
                            if ($sub_categoria[$i]!='') {
                                echo '<tr style="color: #a5a7a8">
                                    <td></td>
                                    <td  style="font-size: 10px;text-align: right;">'.$sub_categoria[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_femeas[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_coberturas[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_positivos[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_nascidos[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_aborto[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_natimorto[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_desmame[$i].'</td>
                                    <td style="border: none;"></td>
                                    <td style="text-align: right;">'.number_format($sub_eficiencia_servico[$i],2,',','.').'</td>
                                    <td style="text-align: right;">'.number_format($sub_taxa_prenhez[$i],2,',','.').' %</td>
                                    <td style="text-align: right;">'.number_format($sub_taxa_natalidade[$i],2,',','.').' %</td>
                                    <td style="text-align: right;">'.number_format($sub_perda_gestacao[$i],2,',','.').' %</td>
                                    <td style="text-align: right;">'.number_format($sub_taxa_desmame[$i],2,',','.').' %</td>
                                </tr>';
                            }
                        }*/

                        $local_anterior=$codigo_local;
                        $desc_local = $reg_cobertura->tbl_pessoa_nome;

                        $animal_anterior = 0;
                        $qtd_femeas = 0;
                        $qtd_coberturas = 0;
                        $qtd_positivos = 0;
                        $qtd_nascidos = 0;
                        $qtd_aborto = 0;
                        $qtd_natimorto = 0;
                        $qtd_desmame = 0;

                        for ($i=0; $i < 3; $i++) { 
                            $sub_qtd_femeas[$i]=0;
                            $sub_qtd_coberturas[$i]=0;
                            $sub_qtd_positivos[$i]=0;
                            $sub_qtd_nascidos[$i]=0;
                            $sub_qtd_aborto[$i]=0;
                            $sub_qtd_natimorto[$i]=0;
                            $sub_qtd_desmame[$i]=0;
                            $sub_eficiencia_servico[$i]=0;
                            $sub_taxa_prenhez[$i]=0;
                            $sub_taxa_natalidade[$i]=0;
                            $sub_perda_gestacao[$i]=0;
                            $sub_taxa_desmame[$i]=0;
                        }
                    }
                }

                $qtd_coberturas++;

                if ($categoria_animal=='N') {
                    $sub_qtd_coberturas[0]++;
                }
                else if ($categoria_animal=='P') {
                    $sub_qtd_coberturas[1]++;
                }   
                else {
                    $sub_qtd_coberturas[2]++;
                }                    

                if ($codigo_id_animal!=$animal_anterior) {
                    $qtd_femeas++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_femeas[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_femeas[1]++;
                    }   
                    else {
                        $sub_qtd_femeas[2]++;
                    }                    

                    $animal_anterior=$codigo_id_animal;

                    // verifica desmama 
                    $sql = mysqli_query($conector, "SELECT * FROM tbl_animais
                        INNER JOIN tbl_item_pesagem 
                                ON tbl_ite_pesagem_codigo_id_animal=tbl_animal_codigo_id 
                        INNER JOIN tbl_pesagem
                                ON tbl_pesagem_id  = tbl_ite_pesagem_numero_id 
                        WHERE tbl_animal_codigo_mae = '$codigo_id_animal' AND 
                              tbl_pesagem_codigo_epoca = 2 AND 
                              tbl_animal_codigo_cobertura = '$cobertura_id'");

                    // verificar os animais que tiveram peso de desmama independente de estar ativo ou não. Não precisa considerar a idade 
                    $num_rows = mysqli_num_rows($sql);

                    if ($num_rows!=0) {
                        while ($reg_animal = mysqli_fetch_object($sql)) {
                            /*$data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
                            //VER AQUI QUANDO O ANIMAL FOI VENDIDO, MORTO OU OUTRA SAIDA PARA CALCULAR A IDADE. SO VALE PARA ANIMAIS DESMAMADOS <= DATA DA MOVIMENTACAO E TIVEREM > 7 MESES                          
                            $data_acompanhamento_calculo = date("Y-m-d");
                            $date = new DateTime($data_nascimento); // Data de Nascimento
                            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                            $idade_animal = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
                            */
                            //if ($idade_animal>=7) {
                            $qtd_desmame++;
                            $total_desmame++;

                            if ($categoria_animal=='N') {
                                $sub_qtd_desmame[0]++;
                            }
                            else if ($categoria_animal=='P') {
                                $sub_qtd_desmame[1]++;
                            }   
                            else {
                                $sub_qtd_desmame[2]++;
                            }                    
                            //}
                        }
                    }
                }

                if ($diagnostico == 'P') {
                    $qtd_positivos++;
                    $total_positivos++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_positivos[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_positivos[1]++;
                    }   
                    else {
                        $sub_qtd_positivos[2]++;
                    }                    
                }

                if ($nascidos == 'N' and $diagnostico == 'P') {
                    $qtd_nascidos++;
                    $total_nascidos++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_nascidos[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_nascidos[1]++;
                    }   
                    else {
                        $sub_qtd_nascidos[2]++;
                    }                    
                }
                else if ($nascidos == 'A') {
                    $qtd_aborto++;
                    $total_abortos++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_aborto[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_aborto[1]++;
                    }   
                    else {
                        $sub_qtd_aborto[2]++;
                    }                    
                }
                else if ($nascidos == 'M') {
                    $qtd_natimorto++;
                    $total_natimorto++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_natimorto[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_natimorto[1]++;
                    }   
                    else {
                        $sub_qtd_natimorto[2]++;
                    }                    
                }
                // VER O QUE FAZER QUANDO NASCIDOS FOR 'OUTRO' VENDA, MORTE, OURA SAIDA
            }

            /*$sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
                WHERE tbl_protocoloiatf_id = '$protocolo_id' AND 
                      tbl_protocoloiatf_lixeira = 0");
                    
            $reg_protocolo_iatf = mysqli_fetch_object($sql);

            $protocoloiatf_tipo = $reg_protocolo_iatf->tbl_protocoloiatf_tipo;*/

            //for ($i=0; $i < count($tipo_cobertura); $i++) { 
            //}

        } // FIM DO WHILE

        if ($tipo_cobertura=='I') {
            $eficiencia_servico = $qtd_coberturas/$qtd_femeas;

            $total_coberturas+= $qtd_coberturas;
            $total_femeas+= $qtd_femeas;

        $taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
        $taxa_prenhez = number_format($taxa_prenhez,2,'.','.');
        $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
        $taxa_natalidade = number_format($taxa_natalidade,2,'.','.');
        $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
        $perda_gestacao = number_format($perda_gestacao,2,'.','.');
        $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;
        $taxa_desmame = number_format($taxa_desmame,2,'.','.');

        for ($j=0; $j < 3; $j++) { 
            if ($sub_qtd_femeas[$j]!=0) {
                $sub_eficiencia_servico[$j] = $sub_qtd_coberturas[$j]/$sub_qtd_femeas[$j];
                $sub_taxa_prenhez[$j] = ($sub_qtd_positivos[$j]/$sub_qtd_femeas[$j])*100;
                $sub_taxa_desmame[$j] = ($sub_qtd_desmame[$j]/$sub_qtd_femeas[$j])*100;
            }

            if ($sub_qtd_positivos[$j]!=0) {
                $sub_taxa_natalidade[$j] = ($sub_qtd_nascidos[$j]/$sub_qtd_positivos[$j])*100;
                $sub_perda_gestacao[$j] = (($sub_qtd_aborto[$j]+$sub_qtd_natimorto[$j])/$sub_qtd_positivos[$j])*100;
            }
        }

    $linha++;

    $celulas = 'K'.$linha.':O'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

    $celulas = 'A'.$linha.':I'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->getStyle('B'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $celulas = 'K'.$linha.':O'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha,$desc_estacao_monta);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$desc_local);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd_femeas);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $qtd_coberturas);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $qtd_positivos);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $qtd_nascidos);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $qtd_aborto);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $qtd_natimorto);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $qtd_desmame);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $eficiencia_servico);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $taxa_prenhez.' %');
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $taxa_natalidade.' %');
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $perda_gestacao.' %');
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $taxa_desmame.' %');

                            for ($i=0; $i < 3; $i++) { 
                                if ($sub_categoria[$i]!='') {
                                    $linha++;

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                                    $celulas = 'A'.$linha.':I'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $celulas = 'A'.$linha.':O'.$linha;

                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

                                    $sub_taxa_prenhez[$i] = number_format($sub_taxa_prenhez[$i],2,'.','.');
                                    $sub_taxa_natalidade[$i] = number_format($sub_taxa_natalidade[$i],2,'.','.');
                                    $sub_perda_gestacao[$i] = number_format($sub_perda_gestacao[$i],2,'.','.');
                                    $sub_taxa_desmame[$i] = number_format($sub_taxa_desmame[$i],2,'.','.');

                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$sub_categoria[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sub_qtd_femeas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $sub_qtd_coberturas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $sub_qtd_positivos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $sub_qtd_nascidos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $sub_qtd_aborto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $sub_qtd_natimorto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $sub_qtd_desmame[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $sub_eficiencia_servico[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $sub_taxa_prenhez[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $sub_taxa_natalidade[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $sub_perda_gestacao[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $sub_taxa_desmame[$i].' %');
                                }
                            }
        }
        else {
            $eficiencia_servico = $qtd_coberturas/$qtd_femeas;

            $total_coberturas+= $qtd_coberturas;
            $total_femeas+= $qtd_femeas;

        $taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
        $taxa_prenhez = number_format($taxa_prenhez,2,'.','.');
        $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
        $taxa_natalidade = number_format($taxa_natalidade,2,'.','.');
        $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
        $perda_gestacao = number_format($perda_gestacao,2,'.','.');
        $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;
        $taxa_desmame = number_format($taxa_desmame,2,'.','.');

        for ($j=0; $j < 3; $j++) { 
            if ($sub_qtd_femeas[$j]!=0) {
                $sub_eficiencia_servico[$j] = $sub_qtd_coberturas[$j]/$sub_qtd_femeas[$j];
                $sub_taxa_prenhez[$j] = ($sub_qtd_positivos[$j]/$sub_qtd_femeas[$j])*100;
                $sub_taxa_desmame[$j] = ($sub_qtd_desmame[$j]/$sub_qtd_femeas[$j])*100;
            }

            if ($sub_qtd_positivos[$j]!=0) {
                $sub_taxa_natalidade[$j] = ($sub_qtd_nascidos[$j]/$sub_qtd_positivos[$j])*100;
                $sub_perda_gestacao[$j] = (($sub_qtd_aborto[$j]+$sub_qtd_natimorto[$j])/$sub_qtd_positivos[$j])*100;
            }
        }

    $linha++;

    $celulas = 'K'.$linha.':O'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

    $celulas = 'A'.$linha.':I'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->getStyle('B'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $celulas = 'K'.$linha.':O'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha,'Monta');
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$desc_local);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd_femeas);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $qtd_coberturas);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $qtd_positivos);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $qtd_nascidos);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $qtd_aborto);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $qtd_natimorto);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $qtd_desmame);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $eficiencia_servico);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $taxa_prenhez.' %');
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $taxa_natalidade.' %');
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $perda_gestacao.' %');
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $taxa_desmame.' %');

                            for ($i=0; $i < 3; $i++) { 
                                if ($sub_categoria[$i]!='') {
                                    $linha++;

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                                    $celulas = 'A'.$linha.':I'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $celulas = 'A'.$linha.':O'.$linha;

                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

                                    $sub_taxa_prenhez[$i] = number_format($sub_taxa_prenhez[$i],2,'.','.');
                                    $sub_taxa_natalidade[$i] = number_format($sub_taxa_natalidade[$i],2,'.','.');
                                    $sub_perda_gestacao[$i] = number_format($sub_perda_gestacao[$i],2,'.','.');
                                    $sub_taxa_desmame[$i] = number_format($sub_taxa_desmame[$i],2,'.','.');

                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$sub_categoria[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sub_qtd_femeas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $sub_qtd_coberturas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $sub_qtd_positivos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $sub_qtd_nascidos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $sub_qtd_aborto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $sub_qtd_natimorto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $sub_qtd_desmame[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $sub_eficiencia_servico[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $sub_taxa_prenhez[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $sub_taxa_natalidade[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $sub_perda_gestacao[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $sub_taxa_desmame[$i].' %');
                                }
                            }
        }


/*    if ($num_rows!=0) {
        while ($reg_cobertura = mysqli_fetch_object($tbl_item_cobertura)){
            $codigo_id_animal = $reg_cobertura->tbl_ite_cobertura_codigo_id_animal;
            $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
            $codigo_estacao = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
            $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
            $nascidos = $reg_cobertura->tbl_ite_cobertura_nascido;
            //$protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;

            // Verifica numero de partos
            $tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais
                WHERE tbl_animal_codigo_mae = '$codigo_id_animal'");  

            $num_rows = mysqli_num_rows($tbl_animais);

            if ($num_rows==0) {
                $sub_categoria[0]='Novilhas';
                $categoria_animal='N';
            }
            else if ($num_rows==1) {
                $sub_categoria[1]='Primiparas';
                $categoria_animal='P';
            }
            else {
                $sub_categoria[2]='Multiparas';
                $categoria_animal='M';
            }

            $sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
                WHERE tbl_protocoloiatf_id = '$protocolo_id' AND 
                      tbl_protocoloiatf_lixeira = 0");
                    
            $reg_protocolo_iatf = mysqli_fetch_object($sql);

            $protocoloiatf_tipo = $reg_protocolo_iatf->tbl_protocoloiatf_tipo;

            for ($i=0; $i < count($tipo_cobertura); $i++) { 
                if ($protocoloiatf_tipo == $tipo_cobertura[$i]) {

                    if ($codigo_estacao!=$estacao_anterior) {
                        if ($estacao_anterior==0){
                            $estacao_anterior=$codigo_estacao;
                            $desc_local = $reg_cobertura->tbl_pessoa_nome;

                            $sql = mysqli_query($conector, "SELECT * FROM
                                tbl_parametro_estacao_monta
                                WHERE tbl_par_estacao_id = '$codigo_estacao'");  

                            $num_rows = mysqli_num_rows($sql);

                            if ($num_rows!=0) {
                                $reg_estacao = mysqli_fetch_object($sql);
                                $desc_estacao_monta = $reg_estacao->tbl_par_estacao_nome;
                            }
                            else {
                                $desc_estacao_monta = '';
                            }
                        }
                        else {
                            for ($j=0; $j < 3; $j++) { 
                                $sub_eficiencia_servico[$j] = $sub_qtd_coberturas[$j]/$sub_qtd_femeas[$j];

                                $sub_taxa_prenhez[$j] = ($sub_qtd_positivos[$j]/$sub_qtd_femeas[$j])*100;
                                $sub_taxa_natalidade[$j] = ($sub_qtd_nascidos[$j]/$sub_qtd_positivos[$j])*100;
                                $sub_perda_gestacao[$j] = (($sub_qtd_aborto[$j]+$sub_qtd_natimorto[$j])/$sub_qtd_positivos[$j])*100;
                                $sub_taxa_desmame[$j] = ($sub_qtd_desmame[$j]/$sub_qtd_femeas[$j])*100;
                            }

                            $eficiencia_servico = $qtd_coberturas/$qtd_femeas;

                            $total_coberturas+= $qtd_coberturas;
                            $total_femeas+= $qtd_femeas;

                            $taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
                            $taxa_prenhez = number_format($taxa_prenhez,2,'.','.');
                            $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
                            $taxa_natalidade = number_format($taxa_natalidade,2,'.','.');
                            $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
                            $perda_gestacao = number_format($perda_gestacao,2,'.','.');
                            $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;
                            $taxa_desmame = number_format($taxa_desmame,2,'.','.');

                        $linha++;

                        $celulas = 'K'.$linha.':O'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                        $celulas = 'A'.$linha.':I'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $spreadsheet->getActiveSheet()->getStyle('B'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                        $celulas = 'K'.$linha.':O'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha,$desc_estacao_monta);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$desc_local);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd_femeas);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $qtd_coberturas);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $qtd_positivos);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $qtd_nascidos);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $qtd_aborto);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $qtd_natimorto);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $qtd_desmame);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $eficiencia_servico);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $taxa_prenhez.' %');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $taxa_natalidade.' %');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $perda_gestacao.' %');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $taxa_desmame.' %');


                            for ($i=0; $i < 3; $i++) { 
                                if ($sub_categoria[$i]!='') {
                                    $linha++;

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                                    $celulas = 'A'.$linha.':I'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $celulas = 'A'.$linha.':O'.$linha;

                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

                                    $sub_taxa_prenhez[$i] = number_format($sub_taxa_prenhez[$i],2,'.','.');
                                    $sub_taxa_natalidade[$i] = number_format($sub_taxa_natalidade[$i],2,'.','.');
                                    $sub_perda_gestacao[$i] = number_format($sub_perda_gestacao[$i],2,'.','.');
                                    $sub_taxa_desmame[$i] = number_format($sub_taxa_desmame[$i],2,'.','.');

                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$sub_categoria[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sub_qtd_femeas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $sub_qtd_coberturas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $sub_qtd_positivos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $sub_qtd_nascidos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $sub_qtd_aborto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $sub_qtd_natimorto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $sub_qtd_desmame[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $sub_eficiencia_servico[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $sub_taxa_prenhez[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $sub_taxa_natalidade[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $sub_perda_gestacao[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $sub_taxa_desmame[$i].' %');
                                }
                            }

                        $estacao_anterior=$codigo_estacao;
                        $desc_local = $reg_cobertura->tbl_pessoa_nome;

                        $sql = mysqli_query($conector, "SELECT * FROM
                            tbl_parametro_estacao_monta
                            WHERE tbl_par_estacao_id = '$codigo_estacao'");  

                        $num_rows = mysqli_num_rows($sql);

                        if ($num_rows!=0) {
                            $reg_estacao = mysqli_fetch_object($sql);
                            $desc_estacao_monta = $reg_estacao->tbl_par_estacao_nome;
                        }
                        else {
                            $desc_estacao_monta = '';
                        }

                        $animal_anterior = 0;
                        $qtd_femeas = 0;
                        $qtd_coberturas = 0;
                        $qtd_positivos = 0;
                        $qtd_nascidos = 0;
                        $qtd_aborto = 0;
                        $qtd_natimorto = 0;
                        $qtd_desmame = 0;

                        for ($i=0; $i < 3; $i++) { 
                            $sub_qtd_femeas[$i]=0;
                            $sub_qtd_coberturas[$i]=0;
                            $sub_qtd_positivos[$i]=0;
                            $sub_qtd_nascidos[$i]=0;
                            $sub_qtd_aborto[$i]=0;
                            $sub_qtd_natimorto[$i]=0;
                            $sub_qtd_desmame[$i]=0;
                            $sub_eficiencia_servico[$i]=0;
                            $sub_taxa_prenhez[$i]=0;
                            $sub_taxa_natalidade[$i]=0;
                            $sub_perda_gestacao[$i]=0;
                            $sub_taxa_desmame[$i]=0;
                        }
                    }
                }

                    $qtd_coberturas++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_coberturas[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_coberturas[1]++;
                    }   
                    else {
                        $sub_qtd_coberturas[2]++;
                    }                    

                    if ($codigo_id_animal!=$animal_anterior) {
                        $qtd_femeas++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_femeas[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_femeas[1]++;
                        }   
                        else {
                            $sub_qtd_femeas[2]++;
                        }                    

                        $animal_anterior=$codigo_id_animal;

                        // verifica desmama 
                        $sql = mysqli_query($conector, "SELECT * FROM tbl_animais
                            INNER JOIN tbl_item_pesagem 
                                    ON tbl_ite_pesagem_codigo_id_animal=tbl_animal_codigo_id 
                            INNER JOIN tbl_pesagem
                                    ON tbl_pesagem_id  = tbl_ite_pesagem_numero_id 
                            WHERE tbl_animal_codigo_mae = '$codigo_id_animal' AND  tbl_pesagem_codigo_epoca = 2 AND 
                                  tbl_animal_estacao_monta_nascimento = '$codigo_estacao'");  

                        // verificar os animais que tiveram peso de desmama independente de estar ativo ou não. Não precisa considerar a idade 
                        $num_rows = mysqli_num_rows($sql);

                        if ($num_rows!=0) {
                            while ($reg_animal = mysqli_fetch_object($sql)) {
                                //if ($idade_animal>=7) {
                                    $qtd_desmame++;
                                    $total_desmame++;

                                    if ($categoria_animal=='N') {
                                        $sub_qtd_desmame[0]++;
                                    }
                                    else if ($categoria_animal=='P') {
                                        $sub_qtd_desmame[1]++;
                                    }   
                                    else {
                                        $sub_qtd_desmame[2]++;
                                    }                    
                                //}
                            }
                        }
                    }

                    if ($diagnostico == 'P') {
                        $qtd_positivos++;
                        $total_positivos++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_positivos[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_positivos[1]++;
                        }   
                        else {
                            $sub_qtd_positivos[2]++;
                        }                    
                    }

                    if ($nascidos == 'N' and $diagnostico == 'P') {
                        $qtd_nascidos++;
                        $total_nascidos++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_nascidos[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_nascidos[1]++;
                        }   
                        else {
                            $sub_qtd_nascidos[2]++;
                        }                    
                    }
                    else if ($nascidos == 'A') {
                        $qtd_aborto++;
                        $total_abortos++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_aborto[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_aborto[1]++;
                        }   
                        else {
                            $sub_qtd_aborto[2]++;
                        }                    
                    }
                    else if ($nascidos == 'M') {
                        $qtd_natimorto++;
                        $total_natimorto++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_natimorto[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_natimorto[1]++;
                        }   
                        else {
                            $sub_qtd_natimorto[2]++;
                        }                    
                    }

                    // VER O QUE FAZER QUANDO NASCIDOS FOR 'OUTRO' VENDA, MORTE, OURA SAIDA
                }
            }

        } // FIM DO WHILE

        $eficiencia_servico = $qtd_coberturas/$qtd_femeas;

        $total_coberturas+= $qtd_coberturas;
        $total_femeas+= $qtd_femeas;

        $taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
        $taxa_prenhez = number_format($taxa_prenhez,2,'.','.');
        $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
        $taxa_natalidade = number_format($taxa_natalidade,2,'.','.');
        $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
        $perda_gestacao = number_format($perda_gestacao,2,'.','.');
        $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;
        $taxa_desmame = number_format($taxa_desmame,2,'.','.');

        for ($j=0; $j < 3; $j++) { 
            if ($sub_qtd_femeas[$j]!=0) {
                $sub_eficiencia_servico[$j] = $sub_qtd_coberturas[$j]/$sub_qtd_femeas[$j];
                $sub_taxa_prenhez[$j] = ($sub_qtd_positivos[$j]/$sub_qtd_femeas[$j])*100;
                $sub_taxa_desmame[$j] = ($sub_qtd_desmame[$j]/$sub_qtd_femeas[$j])*100;
            }

            if ($sub_qtd_positivos[$j]!=0) {
                $sub_taxa_natalidade[$j] = ($sub_qtd_nascidos[$j]/$sub_qtd_positivos[$j])*100;
                $sub_perda_gestacao[$j] = (($sub_qtd_aborto[$j]+$sub_qtd_natimorto[$j])/$sub_qtd_positivos[$j])*100;
            }
        }

    $linha++;

    $celulas = 'K'.$linha.':O'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

    $celulas = 'A'.$linha.':I'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->getStyle('B'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $celulas = 'K'.$linha.':O'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha,$desc_estacao_monta);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$desc_local);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd_femeas);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $qtd_coberturas);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $qtd_positivos);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $qtd_nascidos);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $qtd_aborto);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $qtd_natimorto);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $qtd_desmame);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $eficiencia_servico);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $taxa_prenhez.' %');
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $taxa_natalidade.' %');
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $perda_gestacao.' %');
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $taxa_desmame.' %');

                            for ($i=0; $i < 3; $i++) { 
                                if ($sub_categoria[$i]!='') {
                                    $linha++;

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                                    $celulas = 'A'.$linha.':I'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $celulas = 'A'.$linha.':O'.$linha;

                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

                                    $celulas = 'K'.$linha.':O'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
                                    $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

                                    $sub_taxa_prenhez[$i] = number_format($sub_taxa_prenhez[$i],2,'.','.');
                                    $sub_taxa_natalidade[$i] = number_format($sub_taxa_natalidade[$i],2,'.','.');
                                    $sub_perda_gestacao[$i] = number_format($sub_perda_gestacao[$i],2,'.','.');
                                    $sub_taxa_desmame[$i] = number_format($sub_taxa_desmame[$i],2,'.','.');

                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,$sub_categoria[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sub_qtd_femeas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $sub_qtd_coberturas[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $sub_qtd_positivos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $sub_qtd_nascidos[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $sub_qtd_aborto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $sub_qtd_natimorto[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $sub_qtd_desmame[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $sub_eficiencia_servico[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $sub_taxa_prenhez[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $sub_taxa_natalidade[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $sub_perda_gestacao[$i].' %');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $sub_taxa_desmame[$i].' %');
                                }
                            }
*/
    // calculo do total da eficiencia do serviço
    $media_eficiencia_servico = $total_coberturas/$total_femeas;

    // calculo do total da taxa de prenhez
    $media_taxa_prenhez = ($total_positivos/$total_femeas)*100;
    $media_taxa_prenhez = number_format($media_taxa_prenhez,2,'.','.');

    // calculo do total da taxa de natalidade
    $media_taxa_natalidade = ($total_nascidos/$total_positivos)*100;
    $media_taxa_natalidade = number_format($media_taxa_natalidade,2,'.','.');

    // calculo do total da perda na gestação
    $media_perda_gestacao= (($total_abortos+$total_natimorto)/$total_positivos)*100;
    $media_perda_gestacao = number_format($media_perda_gestacao,2,'.','.');

    // calculo do total da taxa de desmame
    $media_taxa_desmame = 0.00;
    $media_taxa_desmame = number_format($media_taxa_desmame,2,'.','.');


    if ($quantidade_fazendas>2 || $quantidade_estacoes>2) {
        $linha++;

        $celulas = 'K'.$linha.':O'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

        //$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode("#.##0,00");

        $celulas = 'A'.$linha.':I'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $celulas = 'K'.$linha.':O'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('O'.$linha)->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha,'Totais');
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $total_femeas);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $total_coberturas);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $total_positivos);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $total_nascidos);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $total_abortos);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $total_natimorto);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $total_desmame);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $media_eficiencia_servico);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $media_taxa_prenhez.' %');
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $media_taxa_natalidade.' %');
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $media_perda_gestacao.' %');
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $media_taxa_desmame.' %');

    }

}

// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Remove a coluna Qtd Cobertura e eficiencia do servico quando for monta
if ($tipo_cobertura=='M') {
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->removeColumnByIndex(4);
    $sheet->removeColumnByIndex(10);
}

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="indices_reprodutivos.xlsx"');
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