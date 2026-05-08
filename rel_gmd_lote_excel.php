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

$data_sistema = date("Y-m-d");
$partes = explode("-", $data_sistema);
$ano_sistema = $partes[0];
$mes_sistema = $partes[1];
$dia_sistema = cal_days_in_month(CAL_GREGORIAN, $mes_sistema, $ano_sistema);

$data_inicial = $_REQUEST['data_inicial'];
$partes = explode("-", $data_inicial);
$ano_inicial = $partes[0];
$mes_inicial = $partes[1];
$dia_inicial = cal_days_in_month(CAL_GREGORIAN, $mes_inicial, $ano_inicial);

$data_final = $_REQUEST['data_final'];
$partes = explode("-", $data_final);
$ano_final = $partes[0];
$mes_final = $partes[1];
$dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);

@ session_start(); 

$local_filtro = $_REQUEST["local"];
$categoria_filtro = $_REQUEST["categoria"];
$sexo_filtro = $_REQUEST["sexo"];

$wsexo_media='';
$wsexo_fechamento='';

if ($sexo_filtro!='Todos') {
    $wsexo_media = " AND tbl_pm_sexo IN(";
    $wsexo_media .= "'" . $sexo_filtro . "'";
    $wsexo_media.= ")";

    $wsexo_fechamento = " AND tbl_fechamento_sexo IN(";
    $wsexo_fechamento .= "'" . $sexo_filtro . "'";
    $wsexo_fechamento.= ")";
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

$descricao_filtro= $_REQUEST["descricao_filtro"];

$nome_relatorio = "Análise ganho de peso geral para controle de animais por lote";

$spreadsheet->getActiveSheet()->mergeCells('A1:K1');
$spreadsheet->getActiveSheet()->mergeCells('B2:L2');
$spreadsheet->getActiveSheet()->mergeCells('A4:B4');
$spreadsheet->getActiveSheet()->mergeCells('C4:D4');
$spreadsheet->getActiveSheet()->mergeCells('E4:F4');

	$spreadsheet->setActiveSheetIndex(0)
	    ->setCellValue('A1', $nome_relatorio)
		->setCellValue("A2", "Filtro: ")
		->setCellValue("B2", $descricao_filtro);

	$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
	$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('I4',"GMD Global");

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("C4","Pesagem Inicial")
        ->setCellValue("E4","Pesagem Final");

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A5","Categoria")
        ->setCellValue("B5","Sexo")
        ->setCellValue("C5","Qtde Animais")
        ->setCellValue("D5","Peso Médio")
        ->setCellValue("E5","Qtde Animais")
        ->setCellValue("F5","Peso Médio")
        ->setCellValue("G5","GMD");

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);

	$spreadsheet->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	$spreadsheet->getActiveSheet()->getStyle('J1:L1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
	$spreadsheet->getActiveSheet()->getStyle('A2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
	$spreadsheet->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('A5:G5') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	$spreadsheet->getActiveSheet()->getStyle('I4:I5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('B6:D6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->getStyle('A4:G4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A4:G4')->getFill()->getStartColor()->setARGB('DCDCDC');

    $spreadsheet->getActiveSheet()->getStyle('A5:G6')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A5:G5')->getFill()->getStartColor()->setARGB('DCDCDC');

    $spreadsheet->getActiveSheet()->getStyle('I4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('I4')->getFill()->getStartColor()->setARGB('DCDCDC');

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

    $spreadsheet->getActiveSheet()->getStyle('A4:G4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('I4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('I5')->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->getStyle('A5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('G5')->applyFromArray($styleArray);

    $linha=5;

    $data_inicial = $ano_inicial .'-'. $mes_inicial .'-'. $dia_inicial;
    $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
    $data_sistema = $ano_sistema .'-'. $mes_sistema .'-'. $dia_sistema;

    // Cria data inicial e final para verificar se houve pesagem no periodo. Não considera a data inicial do periodo e sim o proximo mes do inicial

    $data_inicial_pesagem = date('Y-m-d', strtotime('+1 month', strtotime($ano_inicial .'-'. $mes_inicial .'-01')));

    if ($ano_final.$mes_final==$ano_sistema.$mes_sistema) {
        $data_final_pesagem = $data_sistema;
    }
    else {
        $data_final_pesagem = $data_final;
    }
    // Fim cria data inicial e final para pesagem

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

            $array_qtd_inicial_macho[$codigo_categoria] = 0;
            $array_qtd_final_macho[$codigo_categoria] = 0;
            $array_peso_medio_inicial_macho[$codigo_categoria] = 0;
            $array_peso_medio_final_macho[$codigo_categoria] = 0;
            $array_data_inicial_macho[$codigo_categoria] = '0000-00-00';
            $array_data_final_macho[$codigo_categoria] = '0000-00-00';
            $array_gmd_macho_categoria[$codigo_categoria] = 0;

            $array_qtd_inicial_femea[$codigo_categoria] = 0;
            $array_qtd_final_femea[$codigo_categoria] = 0;
            $array_peso_medio_inicial_femea[$codigo_categoria] = 0;
            $array_peso_medio_final_femea[$codigo_categoria] = 0;
            $array_data_inicial_femea[$codigo_categoria] = '0000-00-00';
            $array_data_final_femea[$codigo_categoria] = '0000-00-00';
            $array_gmd_femea_categoria[$codigo_categoria] = 0;
        }
    }   

    $tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
        WHERE tbl_fechamento_data='$data_inicial' AND 
              tbl_fechamento_local='$local_filtro'" . $wsexo_fechamento);

    $num_rows_fechamento = mysqli_num_rows($tbl_fechamento);  

    if ($num_rows_fechamento!=0) {
        while ($reg_fechamento = mysqli_fetch_object($tbl_fechamento)) {
            $data_peso = $reg_fechamento->tbl_fechamento_data;
            $sexo = $reg_fechamento->tbl_fechamento_sexo;
            $categoria = $reg_fechamento->tbl_fechamento_categoria;
            $qtd_animais = $reg_fechamento->tbl_fechamento_qtd;
            $peso = $reg_fechamento->tbl_fechamento_peso;

            $peso_medio=0;

            if ($qtd_animais!=0 && $peso!=0) {
                $peso_medio=$peso/$qtd_animais;
            }

            if ($sexo=='M') {
                /*$partes = explode("-", $array_data_inicial_macho[$categoria]);
                $ano_mes_inicial_macho = $partes[0].$partes[1];
                $partes = explode("-", $array_data_final_macho[$categoria]);
                $ano_mes_final_macho = $partes[0].$partes[1];
                $partes = explode("-", $data_peso);
                $ano_mes_peso = $partes[0].$partes[1];*/

                //if ($array_data_inicial_macho[$categoria]=='0000-00-00') {
                    $array_data_inicial_macho[$categoria]=$data_peso;
                    $array_peso_medio_inicial_macho[$categoria]+=$peso_medio;
                    $array_qtd_inicial_macho[$categoria]+=$qtd_animais;
                //}

                /*if ($ano_mes_inicial_macho==$ano_mes_peso) {
                    if ($peso_medio<$array_peso_medio_inicial_macho[$categoria] && $peso_medio!=0) {
                        $array_data_inicial_macho[$categoria]=$data_peso;
                        $array_peso_medio_inicial_macho[$categoria]=$peso_medio;
                        $array_qtd_inicial_macho[$categoria]=$qtd_animais;
                    }
                }

                if ($ano_mes_inicial_macho!=$ano_mes_peso) {
                    if ($ano_mes_final_macho==$ano_mes_peso) {
                        if ($peso_medio>$array_peso_medio_final_macho[$categoria] && $peso_medio!=0) {
                            $array_data_final_macho[$categoria]=$data_peso;
                            $array_peso_medio_final_macho[$categoria]=$peso_medio;
                            $array_qtd_final_macho[$categoria]=$qtd_animais;
                        }
                    }
                    else {
                        $array_data_final_macho[$categoria]=$data_peso;
                        $array_peso_medio_final_macho[$categoria]=$peso_medio;
                        $array_qtd_final_macho[$categoria]=$qtd_animais;
                    }

                    $diferenca = strtotime($array_data_final_macho[$categoria]) - strtotime($array_data_inicial_macho[$categoria]);
                    $dias = floor($diferenca / (60 * 60 * 24)); 

                    if ($array_peso_medio_final_macho[$categoria]!=0 && 
                        $array_peso_medio_inicial_macho[$categoria]!=0) {
                        $ganho = $array_peso_medio_final_macho[$categoria] - 
                                 $array_peso_medio_inicial_macho[$categoria];
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
                        $array_gmd_macho_categoria[$categoria]=$gmd;
                    }
                }*/
            }
            else {
                /*$partes = explode("-", $array_data_inicial_femea[$categoria]);
                $ano_mes_inicial_femea = $partes[0].$partes[1];
                $partes = explode("-", $array_data_final_femea[$categoria]);
                $ano_mes_final_femea = $partes[0].$partes[1];
                $partes = explode("-", $data_peso);
                $ano_mes_peso = $partes[0].$partes[1];*/

                //if ($array_data_inicial_femea[$categoria]=='0000-00-00') {
                    $array_data_inicial_femea[$categoria]=$data_peso;
                    $array_peso_medio_inicial_femea[$categoria]+=$peso_medio;
                    $array_qtd_inicial_femea[$categoria]+=$qtd_animais;
                //}

                /*if ($ano_mes_inicial_femea==$ano_mes_peso) {
                    if ($peso_medio>$array_peso_medio_inicial_femea[$categoria] && $peso_medio!=0) {
                            $array_data_inicial_femea[$categoria]=$data_peso;
                            $array_peso_medio_inicial_femea[$categoria]=$peso_medio;
                            $array_qtd_inicial_femea[$categoria]=$qtd_animais;
                    }
                }

                if ($ano_mes_inicial_femea!=$ano_mes_peso) {
                    if ($ano_mes_final_femea==$ano_mes_peso) {
                        if ($peso_medio<$array_peso_medio_final_femea[$categoria] && $peso_medio!=0) {
                            $array_data_final_femea[$categoria]=$data_peso;
                            $array_peso_medio_final_femea[$categoria]=$peso_medio;
                            $array_qtd_final_femea[$categoria]=$qtd_animais;
                        }
                    }
                    else {
                        $array_data_final_femea[$categoria]=$data_peso;
                        $array_peso_medio_final_femea[$categoria]=$peso_medio;
                        $array_qtd_final_femea[$categoria]=$qtd_animais;
                    }

                    $diferenca = strtotime($array_data_final_femea[$categoria]) - strtotime($array_data_inicial_femea[$categoria]);
                    $dias = floor($diferenca / (60 * 60 * 24)); 

                    if ($array_peso_medio_final_femea[$categoria]!=0 && 
                        $array_peso_medio_inicial_femea[$categoria]!=0) {
                        $ganho = $array_peso_medio_final_femea[$categoria] - 
                                 $array_peso_medio_inicial_femea[$categoria];
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
                        $array_gmd_femea_categoria[$categoria]=$gmd;
                    }
                }*/
            }
        } // Fim while fechamento data inicial
    } // Fim if fechamento data inicial


    if ($ano_final.$mes_final==$ano_sistema.$mes_sistema) {

        // Gera dados finais pela tbl_peso_medio_categoria

        $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
            WHERE tbl_pm_qtd_total_atual!=0 AND 
                  tbl_pm_local_id='$local_filtro'" . $wsexo_media);

        // ver where vazio

        $num_rows_media = mysqli_num_rows($tbl_media);  

        if ($num_rows_media!=0) {
            while ($reg_media = mysqli_fetch_object($tbl_media)) {
                $data_peso = $data_sistema;
                $local = $reg_media->tbl_pm_local_id;
                $sexo = $reg_media->tbl_pm_sexo;
                $categoria = $reg_media->tbl_pm_categoria_id;
                $qtd_animais = $reg_media->tbl_pm_qtd_total_atual;
                $peso_medio = $reg_media->tbl_pm_peso_medio_atual;

                $tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                    INNER JOIN tbl_pesagem
                            ON tbl_pesagem_id  = tbl_ite_pesagem_numero_id 
                    WHERE tbl_pesagem_finalizada='S' AND 
                          tbl_pesagem_codigo_local='$local' AND 
                          tbl_ite_pesagem_data_emissao>='$data_inicial_pesagem' AND 
                          tbl_ite_pesagem_data_emissao<='$data_final_pesagem' AND 
                          tbl_ite_pesagem_categoria='$categoria' AND 
                          tbl_ite_pesagem_sexo='$sexo'");

                $num_rows_pesagem = mysqli_num_rows($tbl_pesagem);    

                if ($num_rows_pesagem!=0) {
                    if ($sexo=='M') {
                        $array_data_final_macho[$categoria]=$data_peso;
                        $array_peso_medio_final_macho[$categoria]+=$peso_medio;
                        $array_qtd_final_macho[$categoria]+=$qtd_animais;
                    }
                    else {
                        $array_data_final_femea[$categoria]=$data_peso;
                        $array_peso_medio_final_femea[$categoria]+=$peso_medio;
                        $array_qtd_final_femea[$categoria]+=$qtd_animais;
                    }
                }

            } // Fim while fechamento data final
        } // Fim if fechamento data final
    }
    else {
        // Gera dados finais pela tabela tbl_fechamento_mensal_estoque
        $tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
            WHERE tbl_fechamento_data='$data_final' AND 
                  tbl_fechamento_local='$local_filtro'" .$wsexo_fechamento);

        $num_rows_fechamento = mysqli_num_rows($tbl_fechamento);  

        if ($num_rows_fechamento!=0) {
            while ($reg_fechamento = mysqli_fetch_object($tbl_fechamento)) {
                $data_peso = $reg_fechamento->tbl_fechamento_data;
                $local = $reg_fechamento->tbl_fechamento_local;
                $sexo = $reg_fechamento->tbl_fechamento_sexo;
                $categoria = $reg_fechamento->tbl_fechamento_categoria;
                $qtd_animais = $reg_fechamento->tbl_fechamento_qtd;
                $peso = $reg_fechamento->tbl_fechamento_peso;

                $peso_medio=0;

                if ($qtd_animais!=0 && $peso!=0) {
                    $peso_medio=$peso/$qtd_animais;
                }

                $tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                    INNER JOIN tbl_pesagem
                            ON tbl_pesagem_id  = tbl_ite_pesagem_numero_id 
                    WHERE tbl_pesagem_finalizada='S' AND 
                          tbl_pesagem_codigo_local='$local' AND 
                          tbl_ite_pesagem_data_emissao>='$data_inicial_pesagem' AND 
                          tbl_ite_pesagem_data_emissao<='$data_final_pesagem' AND 
                          tbl_ite_pesagem_categoria='$categoria' AND 
                          tbl_ite_pesagem_sexo='$sexo'");

                $num_rows_pesagem = mysqli_num_rows($tbl_pesagem);    

                if ($num_rows_pesagem!=0) {
                    if ($sexo=='M') {
                        $array_data_final_macho[$categoria]=$data_peso;
                        $array_peso_medio_final_macho[$categoria]+=$peso_medio;
                        $array_qtd_final_macho[$categoria]+=$qtd_animais;
                    }
                    else {
                        $array_data_final_femea[$categoria]=$data_peso;
                        $array_peso_medio_final_femea[$categoria]+=$peso_medio;
                        $array_qtd_final_femea[$categoria]+=$qtd_animais;
                    }
                }
            } // Fim while fechamento data final
        } // Fim if fechamento data final
    }

    // Gera ganho de peso

    for ($i=1; $i <= 5; $i++) { 
        $categoria = str_pad($i, 3, "0", STR_PAD_LEFT);

        // Macho
        $diferenca = strtotime($array_data_final_macho[$categoria]) - 
                     strtotime($array_data_inicial_macho[$categoria]);
        $dias = floor($diferenca / (60 * 60 * 24));             
                    
        if ($array_peso_medio_final_macho[$categoria]!=0 && 
            $array_peso_medio_inicial_macho[$categoria]!=0) {
            $ganho = $array_peso_medio_final_macho[$categoria] - 
                     $array_peso_medio_inicial_macho[$categoria];
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
            $array_gmd_macho_categoria[$categoria]=$gmd;
        }

        // Fêmea
        $diferenca = strtotime($array_data_final_femea[$categoria]) - 
                     strtotime($array_data_inicial_femea[$categoria]);
        $dias = floor($diferenca / (60 * 60 * 24));             
                    
        if ($array_peso_medio_final_femea[$categoria]!=0 && 
            $array_peso_medio_inicial_femea[$categoria]!=0) {
            $ganho = $array_peso_medio_final_femea[$categoria] - 
                     $array_peso_medio_inicial_femea[$categoria];
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
            $array_gmd_femea_categoria[$categoria]=$gmd;
        }
    }

    if ($wcategoria=="") {
        for ($i=1; $i <= 5; $i++) { 
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
            if ($array_qtd_inicial_macho[$j]!=0 && $array_qtd_final_macho[$j]!=0){
                        
                /*if ($array_data_inicial_macho[$j]==$array_data_final_macho[$j]) {
                    $array_data_final_macho[$j]=0;
                    $array_peso_medio_final_macho[$j]=0;
                    $array_qtd_final_macho[$j]=0;
                }*/
                        
                $linha++;

                $celulas = 'B'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'C'.$linha.':G'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_desc_categoria[$j]);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'M');
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_qtd_inicial_macho[$j]);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $array_peso_medio_inicial_macho[$j]);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $array_qtd_final_macho[$j]);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $array_peso_medio_final_macho[$j]);

                $gmd_total+= ($array_qtd_final_macho[$j] * $array_gmd_macho_categoria[$j]);
                $numero_gmd+= $array_qtd_final_macho[$j];
                //$gmd_edi = number_format($array_gmd_macho_categoria[$j],3,',','.');
                $gmd_edi = $array_gmd_macho_categoria[$j];

                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $gmd_edi);
            }

            if ($array_qtd_inicial_femea[$j]!=0 && $array_qtd_final_femea[$j]!=0) {
                        
                /*if ($array_data_inicial_femea[$j]==$array_data_final_femea[$j]) {
                    $array_data_final_femea[$j]=0;
                    $array_peso_medio_final_femea[$j]=0;
                    $array_qtd_final_femea[$j]=0;
                }*/
                        
                $linha++;

                $celulas = 'B'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'C'.$linha.':G'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_desc_categoria[$j]);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'F');
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_qtd_inicial_femea[$j]);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $array_peso_medio_inicial_femea[$j]);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $array_qtd_final_femea[$j]);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $array_peso_medio_final_femea[$j]);

                $gmd_total+= ($array_qtd_final_femea[$j] * $array_gmd_femea_categoria[$j]);
                $numero_gmd+= $array_qtd_final_femea[$j];
                //$gmd_edi = number_format($array_gmd_femea_categoria[$j],3,',','.');
                $gmd_edi = $array_gmd_femea_categoria[$j];

                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $gmd_edi);
            }
        }
    }
    else {
        for ($i=1; $i <= 5; $i++) { 
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
            for ($k=0; $k < $quantidade_categoria; $k++) { 
                $value = $wcategoria[$k];
                if ($value==$j) {
                    if ($array_qtd_inicial_macho[$j]!=0 && 
                        $array_qtd_final_macho[$j]!=0) {
                        /*if ($array_data_inicial_macho[$j]==$array_data_final_macho[$j]) {
                            $array_data_final_macho[$j]=0;
                            $array_peso_medio_final_macho[$j]=0;
                            $array_qtd_final_macho[$j]=0;
                        }*/
                        
                        $linha++;

                        $celulas = 'B'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $celulas = 'C'.$linha.':G'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_desc_categoria[$j]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'M');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_qtd_inicial_macho[$j]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $array_peso_medio_inicial_macho[$j]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $array_qtd_final_macho[$j]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $array_peso_medio_final_macho[$j]);

                        $gmd_total+= ($array_qtd_final_macho[$j] * $array_gmd_macho_categoria[$j]);
                        $numero_gmd+= $array_qtd_final_macho[$j];
                        //$gmd_edi = number_format($array_gmd_macho_categoria[$j],3,',','.');
                        $gmd_edi = $array_gmd_macho_categoria[$j];

                    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                        $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $gmd_edi);
                    }

                    if ($array_qtd_inicial_femea[$j]!=0 && 
                        $array_qtd_final_femea[$j]!=0) {
                        /*if ($array_data_inicial_femea[$j]==$array_data_final_femea[$j]) {
                            $array_data_final_femea[$j]=0;
                            $array_peso_medio_final_femea[$j]=0;
                            $array_qtd_final_femea[$j]=0;
                        }*/

                        $linha++;

                        $celulas = 'B'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $celulas = 'C'.$linha.':G'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                        $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_desc_categoria[$j]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'F');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_qtd_inicial_femea[$j]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $array_peso_medio_inicial_femea[$j]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $array_qtd_final_femea[$j]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $array_peso_medio_final_femea[$j]);

                        $gmd_total+= ($array_qtd_final_femea[$j] * $array_gmd_femea_categoria[$j]);
                        $numero_gmd+= $array_qtd_final_femea[$j];
                        //$gmd_edi = number_format($array_gmd_femea_categoria[$j],3,',','.');
                        $gmd_edi = $array_gmd_macho_categoria[$j];

                        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                        $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                        $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $gmd_edi);
                    }
                }
            }
        }
    }

    if ($gmd_total!=0 && $numero_gmd>0) {
        $media_gmd = $gmd_total / $numero_gmd;
        $media_gmd_edi = $media_gmd;
    }
    else {
        $media_gmd_edi = 0;
    }

    //$gmd_edi = $array_gmd_macho_categoria[$j];

    $spreadsheet->getActiveSheet()->getStyle('I5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, 5, $media_gmd_edi);


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="gmd_local.xlsx"');
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