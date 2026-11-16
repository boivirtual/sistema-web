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

@ session_start(); 

$codigo_grupo_usuario = $_SESSION['grupo_usuario'];
$controle_estoque = $_SESSION['controle_estoque'];

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
$wlocal_fechamento = '';
$wfazenda = '';

if ($local_filtro!='') {
    $wlocal = " AND tbl_mov_estoque_local IN(";
    $wlocal.= $local;
    $wlocal.= ")";

    $wlocal_fechamento = " AND tbl_fechamento_local IN(";
    $wlocal_fechamento.= $local;
    $wlocal_fechamento.= ")";

    $wfazenda = " AND tbl_animal_codigo_fazenda IN(";
    $wfazenda.= $local;
    $wfazenda.= ")";
}

$tipo_rel= $_REQUEST["tipo_rel"];
$descricao_filtro= $_REQUEST["descricao_filtro"];
$nome_relatorio = 'Estoque de Animais por Kilo (peso)';

$data_sistema = date("d/m/Y");

	$spreadsheet->getActiveSheet()->mergeCells('A1:J1');
	$spreadsheet->getActiveSheet()->mergeCells('B2:K2');
    $spreadsheet->getActiveSheet()->mergeCells('B3:K3');
	$spreadsheet->getActiveSheet()->mergeCells('C4:F4');
    $spreadsheet->getActiveSheet()->mergeCells('G4:J4');
    $spreadsheet->getActiveSheet()->mergeCells('A4:A5');
    $spreadsheet->getActiveSheet()->mergeCells('B4:B5');
    $spreadsheet->getActiveSheet()->mergeCells('K4:K5');

	$spreadsheet->setActiveSheetIndex(0)
	    ->setCellValue('A1', $nome_relatorio)
		->setCellValue("K1", "Data: " . $data_sistema)
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

    $spreadsheet->getActiveSheet()->getStyle('A4:K4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A4:K4')->getFill()->getStartColor()->setARGB('C0C0C0');
    $spreadsheet->getActiveSheet()->getStyle('A5:K5')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A5:K5')->getFill()->getStartColor()->setARGB('DCDCDC');

    $spreadsheet->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('A2:K2')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('A3:K3')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('A4:K4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('A5:K5')->applyFromArray($styleArray);
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

	$spreadsheet->setActiveSheetIndex(0)
	    ->setCellValue("A4","Meses")
        ->setCellValue("B4","Estoque Inicial")
        ->setCellValue("C5","Nascimento")
	    ->setCellValue("D5","Compra")
	    ->setCellValue("E5","Tranferência")
	    ->setCellValue("F5","Outras Entradas")
        ->setCellValue("G5","Morte")
        ->setCellValue("H5","Venda")
        ->setCellValue("I5","Tranferência")
        ->setCellValue("J5","Outras Saídas")
        ->setCellValue("K4","Estoque Final")
	    ->setCellValue("C4","Entradas")
	    ->setCellValue("G4","Saídas");

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
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

	$spreadsheet->getActiveSheet()->getStyle('A1:k1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('C4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('G4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('A5:k5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical($align);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('B4')->getAlignment()->setVertical($align);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('K4')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('K4')->getAlignment()->setVertical($align);

$linha=5;
    $estoque_final = 0;
    $estoque_inicial = 0;
    $estoque_ent_nasc = 0;
    $estoque_ent_compra = 0;
    $estoque_ent_transf = 0;
    $estoque_ent_outra = 0;
    $estoque_sai_morte = 0;
    $estoque_sai_venda = 0;
    $estoque_sai_transf = 0;
    $estoque_sai_outra = 0;

    $total_ent_nasc = 0;
    $total_ent_compra = 0;
    $total_ent_transf = 0;
    $total_ent_outra = 0;
    $total_sai_morte = 0;
    $total_sai_venda = 0;
    $total_sai_transf = 0;
    $total_sai_outra = 0;

    $total_meses = 0;
    //$media_final = 0;

    $data_inicial = $data_inicial . '-01';
    $data_final = $data_final . '-31';

    for ($i=0; $i<$qtd_meses; $i++) { 

        $mes_lista = $array_mes[$i];
        $ano_lista = $array_ano[$i];

        $estoque_ent_nasc = 0;
        $estoque_ent_compra = 0;
        $estoque_ent_transf = 0;
        $estoque_ent_outra = 0;
        $estoque_sai_morte = 0;
        $estoque_sai_venda = 0;
        $estoque_sai_transf = 0;
        $estoque_sai_outra = 0;
        $estoque_sem_mov = 0;
        $estoque_fim = 0;

        // Pega estoque fechamento
        $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque_ent_sai_peso
            WHERE year(tbl_fechamento_data)='$ano_lista' AND 
                  month(tbl_fechamento_data)='$mes_lista'" . 
                $wlocal_fechamento);

        $num_rows = mysqli_num_rows($mov_estoque);  

        if ($num_rows!=0) {
            while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                $estoque_inicial+= $reg_mov->tbl_fechamento_peso_inicial;
                $ent_nascimento = $reg_mov->tbl_fechamento_peso_ent_nascimento;
                $ent_compra = $reg_mov->tbl_fechamento_peso_ent_compra;
                $ent_tranferencia = $reg_mov->tbl_fechamento_peso_ent_transferencia;
                $ent_outras = $reg_mov->tbl_fechamento_peso_ent_outras;

                $sai_morte = $reg_mov->tbl_fechamento_peso_sai_morte;
                $sai_venda = $reg_mov->tbl_fechamento_peso_sai_venda;
                $sai_tranferencia = $reg_mov->tbl_fechamento_peso_sai_transferencia;
                $sai_outras = $reg_mov->tbl_fechamento_peso_sai_outras;
                $peso_sem_mov = $reg_mov->tbl_fechamento_peso_sem_movimentacao;

                $estoque_ent_nasc+=$ent_nascimento;
                $estoque_ent_compra+=$ent_compra;
                $estoque_ent_transf+=$ent_tranferencia;
                $estoque_ent_outra+=$ent_outras;
                $estoque_sai_morte+=$sai_morte;   
                $estoque_sai_venda+=$sai_venda;
                $estoque_sai_transf+=$sai_tranferencia;
                $estoque_sai_outra+=$sai_outras;
                $estoque_sem_mov+=$peso_sem_mov;

                $estoque_fim+=$reg_mov->tbl_fechamento_peso_final;
            }

            $estoque_final = $estoque_inicial + $estoque_ent_nasc +
                             $estoque_ent_compra + $estoque_ent_transf +
                             $estoque_ent_outra + $estoque_sem_mov;

            $estoque_final = $estoque_final - $estoque_sai_morte - 
                             $estoque_sai_venda - $estoque_sai_transf -
                             $estoque_sai_outra;

            if ($estoque_sem_mov==0) {
                $estoque_final = $estoque_fim;
            }

            $total_meses+= $estoque_final;

            $linha++;
            $celulas = 'A'.$linha.':L'.$linha;
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
            $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_mes_extenco[$i]);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $estoque_inicial);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $estoque_ent_nasc);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $estoque_ent_compra);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $estoque_ent_transf);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $estoque_ent_outra);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $estoque_sai_morte);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $estoque_sai_venda);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $estoque_sai_transf);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $estoque_sai_outra);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $estoque_final);

            $estoque_inicial = 0;

            $total_ent_nasc+= $estoque_ent_nasc;
            $total_ent_compra+= $estoque_ent_compra;
            $total_ent_transf+= $estoque_ent_transf;
            $total_ent_outra+= $estoque_ent_outra;
            $total_sai_morte+= $estoque_sai_morte;
            $total_sai_venda+= $estoque_sai_venda;
            $total_sai_transf+= $estoque_sai_transf;
            $total_sai_outra+= $estoque_sai_outra;
        }

    }

    // Pega movimentação e estoque final do mes atual
    if ($mes_hoje==$mes_lista && $ano_hoje==$ano_lista) {

        // pega o estoque final do mes anterior
        if ($estoque_inicial==0) {

            $mes_ant = $mes_lista - 1;
            $ano_ant = $ano_lista;

            if ($mes_ant==0) {
                $mes_ant=12;
                $ano_ant=$ano_ant - 1;
            }

            $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque_ent_sai_peso
                WHERE year(tbl_fechamento_data)='$ano_ant' AND 
                      month(tbl_fechamento_data)='$mes_ant'" . 
                    $wlocal_fechamento);

            $num_rows = mysqli_num_rows($mov_estoque);  

            if ($num_rows!=0) {
                while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                    $estoque_inicial+= $reg_mov->tbl_fechamento_peso_final;
                }
            }
        }

        $estoque_ent_nasc = 0;
        $estoque_ent_compra = 0;
        $estoque_ent_transf = 0;
        $estoque_ent_outra = 0;

        $estoque_sai_morte = 0;
        $estoque_sai_venda = 0;
        $estoque_sai_transf = 0;
        $estoque_sai_outra = 0;
        $estoque_final = 0;

        // Pega estoque sem nascimento
        $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
            WHERE year(tbl_mov_estoque_data_emissao)='$ano_lista' AND 
                  month(tbl_mov_estoque_data_emissao)='$mes_lista' AND 
                      tbl_mov_estoque_tipo_movimentacao!='N' AND
                      tbl_mov_estoque_tipo_movimentacao!='A' AND 
                      tbl_mov_estoque_tipo_movimentacao!='B' AND 
                      tbl_mov_estoque_codigo_id_animal!=999999999" . 
                    $wlocal);

        $num_rows = mysqli_num_rows($mov_estoque);  

        if ($num_rows!=0) {
            while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;

                if ($controle_estoque=='I') {
                    $codigo_id_animal = $reg_mov->tbl_mov_estoque_codigo_id_animal;

                    $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais 
                        WHERE tbl_animal_codigo_id='$codigo_id_animal'");

                    $num_rows_animais = mysqli_num_rows($tbl_animais);
                    $peso = 0;

                    if ($num_rows_animais!=0) {
                        $reg_animal = mysqli_fetch_object($tbl_animais);

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
                    }
                }
                else {
                    $peso = $reg_mov->tbl_mov_estoque_primeiro_peso;
                }

                if ($ent_sai=='E') {
                    if ($tipo=='C') {
                        $estoque_ent_compra+=$peso;
                    }
                    else if ($tipo=='T') {
                        $estoque_ent_transf+=$peso;
                    }
                    else{
                        $estoque_ent_outra+=$peso;
                    }
                }
                else {
                    if ($tipo=='M') {
                        $estoque_sai_morte+=$peso;   
                    }
                    else if ($tipo=='V') {
                        $estoque_sai_venda+=$peso;
                    }
                    else if ($tipo=='T') {
                        $estoque_sai_transf+=$peso;
                    }
                    else {
                        $estoque_sai_outra+=$peso;
                    }
                }
            }
        }

        // Pega estoque nascimento

        $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
            WHERE year(tbl_mov_estoque_nascimento)='$ano_lista' AND 
                  month(tbl_mov_estoque_nascimento)='$mes_lista' AND
                  tbl_mov_estoque_tipo_movimentacao='N' AND
                  tbl_mov_estoque_codigo_id_animal!=999999999" . 
                  $wlocal);

        $num_rows = mysqli_num_rows($mov_estoque);  

        if ($num_rows!=0) {
            while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;

                if ($controle_estoque=='I') {
                    $codigo_id_animal = $reg_mov->tbl_mov_estoque_codigo_id_animal;

                    $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais 
                        WHERE tbl_animal_codigo_id='$codigo_id_animal'");

                    $num_rows_animais = mysqli_num_rows($tbl_animais);
                    $peso = 0;

                    if ($num_rows_animais!=0) {
                        $reg_animal = mysqli_fetch_object($tbl_animais);

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
                    }
                }
                else {
                    $peso = $reg_mov->tbl_mov_estoque_primeiro_peso;
                }

                if ($ent_sai=='E') {
                    if ($tipo=='N') {
                        $estoque_ent_nasc+=$peso;   
                    }
                }
            }
        }

        // Pega estoque atual do cadastro em peso 
        if ($controle_estoque=='I') {
            $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais
                where tbl_animal_lixeira=0 AND 
                      tbl_animal_ativo='S'" . $wfazenda);

            while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                $peso = $reg_animal->tbl_animal_ultimo_peso;

                if ($peso==0) {
                    $peso = $reg_animal->tbl_animal_primeiro_peso;
                }
                
                $estoque_final+=$peso;
            }

            if ($estoque_final==0) {
                $estoque_final = $estoque_inicial;
            }

            $total_meses+= $estoque_final;
        }
        else {
            $tbl_media= mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
                where tbl_pm_local_id='$local'");

            while ($reg_media = mysqli_fetch_object($tbl_media)) {
                $peso = $reg_media->tbl_pm_peso_total_atual; 
                $estoque_final+=$peso;
            }
        }

        //$media_final = $total_meses/$qtd_meses;

    $linha++;
    $celulas = 'A'.$linha.':L'.$linha;
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
    $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_mes_extenco[$i-1]);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $estoque_inicial);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $estoque_ent_nasc);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $estoque_ent_compra);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $estoque_ent_transf);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $estoque_ent_outra);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $estoque_sai_morte);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $estoque_sai_venda);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $estoque_sai_transf);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $estoque_sai_outra);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $estoque_final);

        $estoque_inicial = $estoque_final;

        $total_ent_nasc+= $estoque_ent_nasc;
        $total_ent_compra+= $estoque_ent_compra;
        $total_ent_transf+= $estoque_ent_transf;
        $total_ent_outra+= $estoque_ent_outra;
        $total_sai_morte+= $estoque_sai_morte;
        $total_sai_venda+= $estoque_sai_venda;
        $total_sai_transf+= $estoque_sai_transf;
        $total_sai_outra+= $estoque_sai_outra;
}

        $linha++;

        $celulas = 'A'.$linha.':B'.$linha;
        $spreadsheet->getActiveSheet()->mergeCells($celulas);

        $celulas = 'A'.$linha.':L'.$linha;
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
        $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, 'Totais');
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $total_ent_nasc);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $total_ent_compra);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $total_ent_transf);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $total_ent_outra);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $total_sai_morte);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $total_sai_venda);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $total_sai_transf);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $total_sai_outra);
        //$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $media_final);
        $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment;filename="estoque_peso.xlsx"');

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