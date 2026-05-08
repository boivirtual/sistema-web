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

@session_start();
$cnpj_cliente = $_SESSION['id_cliente'];

// abre banco de dados
$servidor = "localhost";
$usuario_bd = "root";
$banco = $cnpj_cliente;
$senha_bd = "a2ngei9Mxh";

$conector = mysqli_connect($servidor, $usuario_bd, $senha_bd);

if (mysqli_connect_error()) {
    print_r("Falha na conexão: ", mysqli_connect_error());
    exit;
}

$bancoselecionado = mysqli_select_db($conector, $banco);

if ($bancoselecionado === FALSE) {
    print_r("Falha na seleção do banco de dados: " . mysqli_error($conector));
    exit;
}

$codigo_local = $_REQUEST["local"];
$tipo_rel = $_REQUEST['tipo_rel'];
$data_inicial = $_REQUEST["data_inicial"];
$data_final = $_REQUEST["data_final"];
$descricao_filtro = $_REQUEST["descricao_filtro"];
$codigo_cc = $_REQUEST["codigo_cc"];

$local = array();
$matriz_itens = explode(",", $codigo_local);
$quantidade_itens = count($matriz_itens);

for ($i = 0; $i < $quantidade_itens; $i++) {
    $local[$i] = $matriz_itens[$i];
}

$local = implode(',', $local);
$local = substr($local, 0, -1);

$wlocal = '';

if ($codigo_local != '') {
    if ($tipo_rel == 1) {
        $wlocal = " AND tbl_venda_codigo_local_origem IN(";
        $wlocal .= $local;
        $wlocal .= ")";
    } else {
        $wlocal = " AND tbl_venda_codigo_local_destino IN(";
        $wlocal .= $local;
        $wlocal .= ")";
    }
}

$centro_custo= array();
$matriz_itens = explode(",", $codigo_cc);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $centro_custo[$i]=$matriz_itens[$i];
}

$centro_custo = implode(',', $centro_custo);
$centro_custo = substr($centro_custo,0, -1);

$wcc = '';

if ($codigo_cc!='') {
    $wcc = " AND tbl_venda_centro_custos IN(";
    $wcc.= $centro_custo;
    $wcc.= ")";
}

if ($data_inicial == 0 && $data_final == 0) {
    $wperiodo = '';
} else {
    $wperiodo = " AND tbl_venda_emissao >= '$data_inicial' AND tbl_venda_emissao <= '$data_final'";
}

if ($tipo_rel == 2) {
    $desc_tipo_rel = 'Compras';
} else {
    $desc_tipo_rel = 'Vendas';
}

$nome_relatorio = "Relatório Financeiro de " . $desc_tipo_rel;

if ($tipo_rel == 2) {
    $spreadsheet->getActiveSheet()->mergeCells('A1:K1');
    $spreadsheet->getActiveSheet()->mergeCells('B2:L2');
    $spreadsheet->getActiveSheet()->mergeCells('A3:L3');
    $spreadsheet->getActiveSheet()->mergeCells('A4:B4');
    $spreadsheet->getActiveSheet()->mergeCells('D4:G4');
    $spreadsheet->getActiveSheet()->mergeCells('A5:B5');
    $spreadsheet->getActiveSheet()->mergeCells('D5:G5');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue("L1", "Data: " . $data_sistema)
        ->setCellValue("A2", "Filtro: ")
        ->setCellValue("B2", $descricao_filtro);

    $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));

    $spreadsheet->getActiveSheet()->getStyle("A4:L4")->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle("A4:L4")->getFill()->getStartColor()->setARGB('bfbfbf');

    $spreadsheet->getActiveSheet()->getStyle("A6:L6")->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle("A6:L6")->getFill()->getStartColor()->setARGB('d9d9d9');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("C4", "Total Cabeças")
        ->setCellValue("H4", "Total Kg")
        ->setCellValue("I4", "Total @")
        ->setCellValue("J4", "Total")
        ->setCellValue("K4", "R$ Médio @")
        ->setCellValue("L4", "R$ Médio Cabeça")

        ->setCellValue("A6", "Data")
        ->setCellValue("B6", "Tipo Compra")
        ->setCellValue("C6", "Nº Cabeças")
        ->setCellValue("D6", "Categoria")
        ->setCellValue("E6", "Sexo")
        ->setCellValue("F6", "Fornecedor")
        ->setCellValue("G6", "Fazenda")
        ->setCellValue("H6", "Peso Total Kg")
        ->setCellValue("I6", "Peso Total @")
        ->setCellValue("J6", "Valor Total")
        ->setCellValue("K6", "R$/@")
        ->setCellValue("L6", "R$/Cabeça");

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(6);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(25);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(13);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(16);

    $spreadsheet->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('J1:L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('A4:L4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('A6:L6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
} else {
    $spreadsheet->getActiveSheet()->mergeCells('A1:O1');
    $spreadsheet->getActiveSheet()->mergeCells('B2:P2');
    $spreadsheet->getActiveSheet()->mergeCells('A3:P3');
    $spreadsheet->getActiveSheet()->mergeCells('A4:B4');
    $spreadsheet->getActiveSheet()->mergeCells('D4:G4');
    $spreadsheet->getActiveSheet()->mergeCells('A5:B5');
    $spreadsheet->getActiveSheet()->mergeCells('D5:G5');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue("P1", "Data: " . $data_sistema)
        ->setCellValue("A2", "Filtro: ")
        ->setCellValue("B2", $descricao_filtro);

    $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));

    $spreadsheet->getActiveSheet()->getStyle("A4:P4")->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle("A4:P4")->getFill()->getStartColor()->setARGB('bfbfbf');

    $spreadsheet->getActiveSheet()->getStyle("A6:P6")->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle("A6:P6")->getFill()->getStartColor()->setARGB('d9d9d9');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("C4", "Total Cabeças")
        ->setCellValue("H4", "Total Kg")
        ->setCellValue("I4", "Total Kg")
        ->setCellValue("J4", "Rendimento Médio")
        ->setCellValue("K4", "Total Real")
        ->setCellValue("L4", "Total Desconto")
        ->setCellValue("M4", "Total Receber")
        ->setCellValue("N4", "R$ Médio @")
        ->setCellValue("O4", "R$ Médio @ Vendida")
        ->setCellValue("P4", "R$ Médio Cabeça")

        ->setCellValue("A6", "Data")
        ->setCellValue("B6", "Tipo Venda")
        ->setCellValue("C6", "Nº Cabeças")
        ->setCellValue("D6", "Categoria")
        ->setCellValue("E6", "Sexo")
        ->setCellValue("F6", "Comprador")
        ->setCellValue("G6", "Fazenda")
        ->setCellValue("H6", "Peso Fazenda")
        ->setCellValue("I6", "Peso Morto")
        ->setCellValue("J6", "Rend Carcaça (%)")
        ->setCellValue("K6", "Valor Real")
        ->setCellValue("L6", "Desconto Funrural")
        ->setCellValue("M6", "Valor Receber")
        ->setCellValue("N6", "R$ @ Negociada")
        ->setCellValue("O6", "R$ @ Vendida")
        ->setCellValue("P6", "R$/Cabeça");

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(13);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(13);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(8);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(25);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(13);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(13);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(17);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(17);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(17);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(16);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(16);
    $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(17);

    $spreadsheet->getActiveSheet()->getStyle('A1:O1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('P1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('A4:P4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('A6:P6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

$linha = 6;

    $total_qtd = 0;
    $total_peso_vivo = 0;
    $total_peso_morto = 0;
    $total_rendimento_medio = 0;
    $total_negociado = 0;
    $total_real = 0;
    $total_desconto = 0;
    $total_receber = 0;            
    $total_medio_arroba = 0;
    $total_medio_arroba_vendida = 0;
    $total_medio_cabeca = 0;
    $qtd_arroba_media = 0;
    $qtd_cabeca_media = 0;
    $qtd_redimento_media = 0;          
    $qtd_negociado_media = 0;          
    $total_rendimento_medio_edi = 0;
    $total_peso_vivo_edi = 0;
    $total_peso_morto_edi = 0;
    $total_real_edi = 0;
    $total_medio_arroba_edi = 0;
    $total_medio_cabeca_edi = 0;  
    $total_peso_arroba_edi = 0;
    $desconto_edi = 0;
    $total_desconto_edi = 0;
    $total_receber_edi = 0;
    $vlr_receber_edi = 0;
    $total_arroba = 0;
    $total_medio_negociado_edi = 0;

            $sql = "SELECT * from tbl_venda 
                WHERE tbl_venda_lixeira=0 AND 
                      tbl_venda_categoria='$tipo_rel'" . 
                      $wlocal . 
                      $wcc . 
                      $wperiodo .
                " ORDER BY tbl_venda_emissao ASC"; 

        $rs = mysqli_query($conector, $sql); 
        $num_rows_animais = mysqli_num_rows($rs);

        if ($num_rows_animais != 0) {

            while ($reg_venda = mysqli_fetch_object($rs)){
                $numero_doc = $reg_venda->tbl_venda_id ;
                $codigo_origem = $reg_venda->tbl_venda_codigo_local_origem;
                $codigo_destino = $reg_venda->tbl_venda_codigo_local_destino;

                $total_venda = $reg_venda->tbl_venda_total_venda;
                $desconto = $reg_venda->tbl_venda_total_desconto;

                if ($desconto != 0) {
                    $total_desconto+= $desconto;
                    $desconto_edi = number_format($desconto,2,',','.');
                    $total_desconto_edi = number_format($total_desconto,2,',','.');

                    $per_desconto = ($desconto / $total_venda) * 100;
                }
                else {
                    $per_desconto = 0;
                }

                $vlr_receber = $reg_venda->tbl_venda_total_receber;
                $total_receber+= $vlr_receber;
                $vlr_receber_edi = number_format($vlr_receber,2,',','.');
                $total_receber_edi = number_format($total_receber,2,',','.');
                $per_receber = ($vlr_receber / $total_venda) * 100;
                
                $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_origem'");
                $num_rows = mysqli_num_rows($tbl_local);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tbl_local);
                    $desc_origem = utf8_encode($reg->tbl_pessoa_nome);
                }
                else {
                    $desc_origem = '';
                }

                $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_destino'");
                $num_rows = mysqli_num_rows($tbl_local);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tbl_local);
                    $desc_destino = utf8_encode($reg->tbl_pessoa_nome);
                }
                else {
                    $desc_destino = '';
                }

                $data_venda = new DateTime($reg_venda->tbl_venda_emissao);
                $data_venda_edi = $data_venda->format('d/m/Y');

                $tipo = $reg_venda->tbl_venda_tipo;

                switch ($tipo) {
                    case 'V':
                        $desc_tipo = 'Vivo';
                        break;
                    case 'M':
                        $desc_tipo = 'Morto';
                        break;
                    default:
                        $desc_tipo = 'Cabeça';
                        break;
                }

                $tbl_item = mysqli_query($conector, "select * from tbl_item_venda
                         where tbl_ite_venda_numero_id  ='$numero_doc'"); 
                $num_rows = mysqli_num_rows($tbl_item);

                if ($num_rows!=0) {
                    while ($reg_item = mysqli_fetch_object($tbl_item)) {
                        $categoria = $reg_item->tbl_ite_venda_categoria;
                        $sexo = $reg_item->tbl_ite_venda_sexo;
                        $qtd = $reg_item->tbl_ite_venda_quantidade;
                        $total_qtd+=$qtd;

                        if ($reg_item->tbl_ite_venda_peso_vivo_ajustado==0) {
                            $peso = $reg_item->tbl_ite_venda_peso_vivo;
                            $peso_edi = number_format($reg_item->tbl_ite_venda_peso_vivo,2,',','.');
                        }
                        else {
                            $peso = $reg_item->tbl_ite_venda_peso_vivo_ajustado;
                            $peso_edi = number_format($reg_item->tbl_ite_venda_peso_vivo_ajustado,2,',','.');
                        }

                        $total_peso_vivo+=$peso;
                        $total_peso_vivo_edi = number_format($total_peso_vivo,2,',','.');

                        if ($reg_item->tbl_ite_venda_peso_morto!=0) {
                            $peso_morto = $reg_item->tbl_ite_venda_peso_morto;
                            $peso_morto_edi = number_format($reg_item->tbl_ite_venda_peso_morto,2,',','.');
                        }
                        else {
                            $peso_morto = $reg_item->tbl_ite_venda_peso_morto;
                            $peso_morto_edi = '';
                        }

                        $total_peso_morto+=$peso_morto;
                        $total_peso_morto_edi = number_format($total_peso_morto,2,',','.');

                        $arroba = $reg_item->tbl_ite_venda_arroba;
                        $total_arroba+= $arroba;
                        $total_peso_arroba_edi = number_format($total_arroba,2,',','.');
                        
                        $arroba_edi = number_format($reg_item->tbl_ite_venda_arroba,2,',','.');
                        $und = $reg_item->tbl_ite_venda_unidade_negociada;

                        $vlr_unit = $reg_item->tbl_ite_venda_valor_unitario;

                        if ($tipo=='M' && $und==2) {
                            $vlr_unit = ($peso_morto/$arroba)*$vlr_unit;
                        }

                        if ($tipo!='M') {
                            $vlr_unit_edi = '';
                        }
                        else {
                            $vlr_unit_edi = number_format($vlr_unit,2,',','.');
                            $qtd_negociado_media++;
                            $total_negociado+=$vlr_unit;
                            $total_medio_negociado_div = $total_negociado/$qtd_negociado_media;
                            $total_medio_negociado_edi = number_format($total_medio_negociado_div,2,',','.');
                        }

                        $vlr_total = $reg_item->tbl_ite_venda_valor_total;
                        $vlr_total_edi = number_format($vlr_total,2,',','.');

                        $total_real+=$vlr_total;
                        $total_real_edi = number_format($total_real,2,',','.');

                        $vlr_unit_cabeca = $vlr_total/$qtd;
                        $vlr_unit_cabeca_edi = number_format($vlr_unit_cabeca,2,',','.');

                        if ($per_desconto!=0) {
                            $vlr_desconto = ($vlr_total * $per_desconto) / 100;
                            $vlr_desconto_edi = number_format($vlr_desconto,2,',','.');
                        }
                        else {
                            $vlr_desconto = '';
                        }

                        $vlr_receber_item = ($vlr_total * $per_receber) / 100;
                        $vlr_receber_item_edi = number_format($vlr_receber_item,2,',','.');

                        $total_medio_cabeca+= $vlr_unit_cabeca;
                        $qtd_cabeca_media++;
                        $total_medio_cabeca_div = $total_medio_cabeca/$qtd_cabeca_media;
                        $total_medio_cabeca_edi = number_format($total_medio_cabeca_div,2,',','.');

                        if ($arroba!=0) {
                            if ($tipo=='C') {
                                $arroba = $peso/30;
                                $vlr_arroba = $vlr_total/$arroba;
                                $vlr_arroba_edi = number_format($vlr_arroba,2,',','.');
                                $qtd_arroba_media++;
                                $total_medio_arroba+= $vlr_arroba;
                            }
                            else {
                                if ($und==1) {
                                    $vlr_arroba = $vlr_total/$arroba;
                                    $vlr_arroba_edi = number_format($vlr_arroba,2,',','.');
                                    $qtd_arroba_media++;
                                    $total_medio_arroba+= $vlr_arroba;
                                }
                                else {
                                    if ($tipo=='V' && $und==2) {
                                        $vlr_arroba = $arroba * $vlr_unit;
                                    }
                                    else {
                                        $vlr_arroba = $vlr_total/$arroba;
                                    }
                                    
                                    $vlr_arroba_edi = number_format($vlr_arroba,2,',','.');
                                    $qtd_arroba_media++;
                                    $total_medio_arroba+= $vlr_arroba;
                                }
                            }
                        }
                        else {
                            $vlr_arroba =0;
                        }

                        if ($total_medio_arroba==0 || $qtd_arroba_media==0) {
                            $total_medio_arroba_div = 0;
                            $total_medio_arroba_edi = number_format($total_medio_arroba_div,2,',','.');
                        }
                        else {
                            $total_medio_arroba_div = $total_medio_arroba/$qtd_arroba_media;
                            $total_medio_arroba_edi = number_format($total_medio_arroba_div,2,',','.');
                        }

                        if ($reg_item->tbl_ite_percentual_rendimento==0) {
                            //if ($peso!=0) {
                                //$rendimento = number_format(50,2,',','.');
                                //$qtd_redimento_media++;
                                //$total_rendimento_medio+=50;

                                //$total_rendimento_medio_div = $total_rendimento_medio/$qtd_redimento_media;
                                //$total_rendimento_medio_edi = number_format($total_rendimento_medio_div,2,',','.');
                           // }
                           // else {
                                $rendimento = '';
                            //}
                        }
                        else {
                            //$rendimento = number_format($reg_item->tbl_ite_percentual_rendimento,2,',','.');
                            $rendimento = $reg_item->tbl_ite_percentual_rendimento;
                            $qtd_redimento_media++;
                            $total_rendimento_medio+=$reg_item->tbl_ite_percentual_rendimento;

                            $total_rendimento_medio_div = $total_rendimento_medio/$qtd_redimento_media;
                            $total_rendimento_medio_edi = number_format($total_rendimento_medio_div,2,',','.');
                        }

                        $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade
                                where tab_codigo_categoria_idade='$categoria'"); 
                        $num_rows = mysqli_num_rows($tbl_categoria);
                        if ($num_rows!=0) {
                            $reg_cat = mysqli_fetch_object($tbl_categoria);
                            $idade_de = $reg_cat->tab_categoria_idade_de;
                            $idade_ate = $reg_cat->tab_categoria_idade_ate;
                            $descricao_cat = $idade_de . ' a ' . $idade_ate . ' meses';

                            if ($descricao_cat=='37 a 999999999 meses'){
                                $descricao_cat = '> 36 meses';
                            }
                        }
                        else {
                            $descricao_cat = '';
                        }

                        if ($und==1) {
                            $und = '@';
                        }
                        else {
                            $und = 'Kg';
                        }

                if ($tipo_rel == 2) {
                    $linha++;
                    $celulas = 'A' . $linha . ':C' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $celulas = 'E' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $celulas = 'H' . $linha . ':L' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $data_venda_edi);
                    $celulas = 'A' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_tipo);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $descricao_cat);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $sexo);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $desc_origem);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $desc_destino);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $peso);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $arroba);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $vlr_total);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $vlr_arroba);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $vlr_unit_cabeca);
                } else {
                    $linha++;

                    $celulas = 'A' . $linha . ':C' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $celulas = 'E' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $celulas = 'H' . $linha . ':P' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $data_venda_edi);
                    $celulas = 'A' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_tipo);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $descricao_cat);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $sexo);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $desc_destino);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $desc_origem);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $peso);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $peso_morto);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $rendimento);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $vlr_total);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $vlr_desconto);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $vlr_receber_item);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $vlr_unit);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $vlr_arroba);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(16, $linha, $vlr_unit_cabeca);
                }
            }
        }
    }
}

if ($tipo_rel == 2) {
    $spreadsheet->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->getStyle('H5:L5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
    $spreadsheet->getActiveSheet()->getStyle('H5:L5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 5, $total_qtd);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, 5, $total_peso_vivo);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, 5, $total_arroba);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, 5, $total_real);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, 5, $total_medio_arroba_div);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, 5, $total_medio_cabeca_div);
} else {
    $spreadsheet->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->getStyle('H5:P5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
    $spreadsheet->getActiveSheet()->getStyle('H5:P5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 5, $total_qtd);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, 5, $total_peso_vivo);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, 5, $total_peso_morto);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, 5, $total_rendimento_medio_div);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, 5, $total_real);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, 5, $total_desconto);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, 5, $total_receber);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, 5, $total_medio_negociado_div);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, 5, $total_medio_arroba_div);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(16, 5, $total_medio_cabeca_div);
}


$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

if ($tipo_rel == 2) {
    header('Content-Disposition: attachment;filename="compra.xlsx"');
} else {
    header('Content-Disposition: attachment;filename="venda.xlsx"');
}

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
