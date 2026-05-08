<?php
$data_sistema = date("d/m/Y");
$data_hora_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];
$cnpj_cliente = $_SESSION['id_cliente'];

//      Começa Excel
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\GridLines;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


// Instanciamos a classe
$spreadsheet = new Spreadsheet();

// abre banco de dados
$servidor = "localhost";
$usuario_bd = "root";
$senha_bd = "a2ngei9Mxh";
$banco = $cnpj_cliente;

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

$data_hoje = date("Y-m-d");
$local = ltrim($_REQUEST['local']);
$vacas_paridas = $_REQUEST['vacas_paridas'];
$data_paridas_ate = $_REQUEST['data_paridas'];
$vacas_solteiras = $_REQUEST['vacas_solteiras'];
$novilhas = $_REQUEST['novilhas'];
$idade_de = $_REQUEST['idade_de'];
$idade_ate = $_REQUEST['idade_ate'];
$peso_acima = $_REQUEST['peso_acima'];
$desc_filtro = $_REQUEST['filtros'];
$descricao_filtro = utf8_decode($_REQUEST['filtros']);
$data_selecao = date("Y-m-d");
$id_estacao_monta = $_REQUEST['id_estacao_monta'];

if ($idade_de=='' || $idade_de==0) {
    $idade_de_gravar=0;
}else {
    $idade_de_gravar=$idade_de;
}

if ($idade_ate=='' || $idade_ate==0) {
    $idade_ate_gravar=0;
}
else {
    $idade_ate_gravar=$idade_ate;

}

if ($idade_ate=='') {
    $idade_ate=9999;
}

if ($peso_acima==0) {
    $peso_acima=1;
}

if ($peso_acima=='' || $peso_acima==0) {
    $peso_acima_gravar=0;
}
else {
    $peso_acima_gravar=$peso_acima;
}

$peso_acima = floatval($peso_acima);

if ($data_paridas_ate=='') {
    $data_paridas_ate='9999-99-99';
    $data_paridas_de='0000-00-00';
}
else {
    $data_paridas_de = date("Y-m-d", strtotime('-8 month',strtotime($data_paridas_ate)));
    $data_paridas_de = date("Y-m-d", strtotime('-1 day',strtotime($data_paridas_de)));
}

$tbl_local = mysqli_query($conector, "SELECT * FROM tbl_pessoa
                                        WHERE tbl_pessoa_id='$local'");

$num_rows = mysqli_num_rows($tbl_local);

if ($num_rows!=0) {
    $reg_local = mysqli_fetch_object($tbl_local);
    $desc_local = $reg_local->tbl_pessoa_nome;
}
else {
    $desc_local = 'Não Informado';
}

$nome_relatorio = "Seleção de Fêmeas para Cobertura";
$desc_venda = 'ESCREVA SIM NA LINHA DA FÊMEA QUE VAI SELECIONAR';

$spreadsheet->getActiveSheet()->mergeCells('A1:F1');
$spreadsheet->getActiveSheet()->mergeCells('G1:H1');
$spreadsheet->getActiveSheet()->mergeCells('A2:H2');
$spreadsheet->getActiveSheet()->mergeCells('D3:E3');
$spreadsheet->getActiveSheet()->mergeCells('A4:H4');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue('A3', 'Nº Documento ')
    ->setCellValue('D3', 'Fêmeas Listadas ')
    ->setCellValue('G1', 'Data: ' . $data_sistema);
    
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A5","Selecionar?")
    ->setCellValue("B5","Nº Fêmea")
    ->setCellValue("C5","Raça")
    ->setCellValue("D5","Idade (Ano/Mês)")
    ->setCellValue("E5","Coberturas nessa estação")
    ->setCellValue("F5","Nº Partos")
    ->setCellValue("G5","Nº Abortos")
    ->setCellValue("H5","Último Parto")
    ->setCellValue("I5","Data Aptidão");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(14);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(13);

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('B3:D3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 2, $desc_filtro);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 4, $desc_venda);
$spreadsheet->getActiveSheet()->getStyle('A4')->getFont()->setColor(new Color(Color::COLOR_RED));

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setVertical($align);


$spreadsheet->getActiveSheet()->getStyle('D5')->getAlignment()->setWrapText(true);

$spreadsheet->getActiveSheet()->getStyle('E5')->getAlignment()->setWrapText(true);

$animais_listados = 0;
$controle = 'C';
$cobertura_gravada = '';
$numero_item = 0;
$numero_cobertura = 0;

$linha=5;

/*$tbl_animais = mysqli_query($conector, "select * from tbl_animais 
                    where tbl_animal_sexo='F' and 
                          tbl_animal_ativo='S' and
                          tbl_animal_lixeira=0 and 
                          (tbl_animal_descarte_reproducao='' or 
                           tbl_animal_descarte_reproducao IS NULL) and
                          (tbl_animal_selecioanada_reproducao='' or 
                           tbl_animal_selecioanada_reproducao IS NULL) and                           
                          tbl_animal_codigo_fazenda='$local'
                    order by tbl_animal_codigo_numerico ASC");*/

$tbl_animais = mysqli_query($conector, "select * from tbl_animais 
    where tbl_animal_sexo='F' and 
          tbl_animal_ativo='S' and
          tbl_animal_lixeira=0 and 
          tbl_animal_codigo_fazenda='$local'
   order by tbl_animal_codigo_numerico ASC"); 

$num_row = mysqli_num_rows($tbl_animais);

if ($num_row!=0) {
    while ($reg_animal = mysqli_fetch_object($tbl_animais)){
        $codigo_id= $reg_animal->tbl_animal_codigo_id ;
        $codigo_alfa= $reg_animal->tbl_animal_codigo_alfa;
        $codigo_num= $reg_animal->tbl_animal_codigo_numerico;
        $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
        $ultimo_peso=  floatval($reg_animal->tbl_animal_ultimo_peso);
        $codigo_raca= $reg_animal->tbl_animal_codigo_raca;
        $prenhe = $reg_animal->tbl_animal_prenhe;
        $descarte = $reg_animal->tbl_animal_descarte_reproducao;

        $tbl_raca = mysqli_query($conector,"select * from tabela_racas 
                                where tab_codigo_raca ='$codigo_raca' and 
                                      tab_registro_lixeira_raca = 0"); 
        $num_row_raca = mysqli_num_rows($tbl_raca);

        if ($num_row_raca!=0) {
            $reg_raca = mysqli_fetch_object($tbl_raca);
            $desc_raca = utf8_encode($reg_raca->tab_descricao_raca);
        }
        else {
            $desc_raca = '';
        }

        $codigo_numerico = ltrim($codigo_num, "0");

        if ($codigo_alfa=='') {
            $codigo_ed = $codigo_numerico;
        }
        else {
            $codigo_ed = $codigo_alfa.'-'.$codigo_numerico;
        }

        $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        $idade_ano_mes = $idade_acompanhamento->format('%Y') .' a '. 
        str_pad($idade_acompanhamento->format('%m') , 2 , '0' , STR_PAD_LEFT) . ' m';

        //$dias_ultimo_parto = 0;
        $coberturas_estacao = 0;
        $descricao_pai = '';
        $numero_abortos='';
        $ultimo_parto_edi='';
        $data_aptidao_edi='';
        $idade_bezerro=0;
        $bezerro_ativo='';
        $data_aborto = '0000-00-00';
        $data_natimorto = '0000-00-00';
        $ultimo_parto = '0000-00-00';
        $dias_natimorto = '';
        $dias_aborto = 0;
        $num_aborto = 0;
        $num_natimorto = 0;

        if (($vacas_paridas=='VP' || $novilhas=='NO' || $vacas_solteiras=='VS') || ($vacas_paridas=='' && $novilhas=='' && $vacas_solteiras=='' && $idade>=12)) {

            //VERIFICA SE A FÊMEA JA FOI SELECIONADA NESSA ESTACAO

            $femea_selecionada = '';

            $tbl_selecao = mysqli_query($conector, "select * from tbl_cobertura
                inner join tbl_item_cobertura 
                        on tbl_ite_cobertura_numero_id = tbl_cobertura_id
                where tbl_cobertura_codigo_local = '$local' and 
                      tbl_cobertura_codigo_estacao_monta = '$id_estacao_monta' and 
                      tbl_ite_cobertura_codigo_id_animal='$codigo_id'
                order by tbl_ite_cobertura_numero_item DESC LIMIT 1");

            $selecionada_estacao = mysqli_num_rows($tbl_selecao);

            if ($selecionada_estacao!=0) {
                $femea_selecionada = 'S';

                $reg_selecao = mysqli_fetch_object($tbl_selecao);
                $diagnostico_selecao = $reg_selecao->tbl_ite_cobertura_resultado_diagnostico;

                if ($diagnostico_selecao=='N') {
                    $femea_selecionada = '';
                }
            } 

            $tbl_aborto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                where tbl_mov_estoque_codigo_mae='$codigo_id' and 
                      tbl_mov_estoque_codigo_id_animal=999999999 and 
                      tbl_mov_estoque_entrada_saida='A'");

            $numero_abortos = mysqli_num_rows($tbl_aborto);

            $tbl_cobertura = mysqli_query($conector, "select * from tbl_cobertura
                inner join tbl_item_cobertura 
                        on tbl_ite_cobertura_numero_id = tbl_cobertura_id
                where tbl_cobertura_codigo_local = '$local' and 
                      tbl_cobertura_codigo_estacao_monta = '$id_estacao_monta' and 
                      tbl_ite_cobertura_codigo_id_animal='$codigo_id' and 
                      (tbl_ite_cobertura_resultado_diagnostico='P' or 
                       tbl_ite_cobertura_resultado_diagnostico='N')");

            $coberturas_estacao = mysqli_num_rows($tbl_cobertura);

            // verifica vaca prenhe
            $tbl_prenhe = mysqli_query($conector, "select * from tbl_cobertura
                        inner join tbl_item_cobertura 
                                on tbl_ite_cobertura_numero_id = tbl_cobertura_id
                        where tbl_ite_cobertura_codigo_id_animal='$codigo_id' and 
                              tbl_ite_cobertura_resultado_diagnostico='P' and 
                              (tbl_ite_cobertura_nascido='' or 
                               tbl_ite_cobertura_nascido is null)");

            $num_rows_prenhe = mysqli_num_rows($tbl_prenhe);

            if ($num_rows_prenhe!=0) {
                $prenhe='S';
            }
            else {
                $prenhe='';
            }

            $tbl_filhos = mysqli_query($conector,"select * from tbl_animais 
                            where tbl_animal_codigo_mae='$codigo_id'
                            order by tbl_animal_codigo_numerico ASC"); 
            $numero_partos = mysqli_num_rows($tbl_filhos);

            if ($numero_partos!=0) {
                while ($reg_filhos = mysqli_fetch_object($tbl_filhos)){
                    $codigo_pai=$reg_filhos->tbl_animal_codigo_pai;
                    $bezerro_ativo = $reg_filhos->tbl_animal_ativo;
                    $bezerro_situacao = $reg_filhos->tbl_animal_situacao;
                    $ultimo_parto=new DateTime($reg_filhos->tbl_animal_data_nascimento);
                    $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');
                    $data_aptidao_edi = date("d/m/Y", strtotime($reg_filhos->tbl_animal_data_nascimento . "+ 35 days"));
                    $ultimo_parto=$reg_filhos->tbl_animal_data_nascimento;

                    /*
                    $data_inicial = $reg_filhos->tbl_animal_data_nascimento;
                    $data_final = date("Y-m-d");
                    $diferenca = strtotime($data_final) - 
                                 strtotime($data_inicial);
                    $dias_ultimo_parto = floor($diferenca / (60 * 60 * 24));
                    */

                    $data_nascimento= $reg_filhos->tbl_animal_data_nascimento;
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade_bezerro = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    if ($bezerro_ativo=='N' && $bezerro_situacao=='M') {
                        $data_inicial = $reg_filhos->tbl_animal_baixado_em;
                        $data_final = date("Y-m-d");
                        $diferenca = strtotime($data_final) - 
                                     strtotime($data_inicial);
                        $dias_natimorto = floor($diferenca / (60 * 60 * 24));

                        if ($dias_natimorto<=0) {
                            $dias_natimorto=1;
                        }
                    }
                }
            }
            else {
                $codigo_pai = 0;
            }

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$codigo_pai'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                $descricao_pai = $reg->tbl_semem_codigo_alfa;
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$codigo_pai'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                }
                else {
                    $descricao_pai = '';
                }
            }

            // verifica Natimorto
            $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='S' AND 
                      tbl_mov_estoque_tipo_movimentacao='M'
                ORDER BY tbl_mov_estoque_nascimento DESC");

            $num_natimorto = mysqli_num_rows($tbl_natimorto);

            if ($num_natimorto==0) {
                $data_natimorto = '0000-00-00';
            }
            else {
                $reg_natimorto = mysqli_fetch_object($tbl_natimorto);
                $data_natimorto=$reg_natimorto->tbl_mov_estoque_nascimento;
                $descricao_pai = '';
                $numero_partos+=$num_natimorto; 

                $data_inicial = $reg_natimorto->tbl_mov_estoque_nascimento;
                $data_final = date("Y-m-d");
                $diferenca = strtotime($data_final) - 
                             strtotime($data_inicial);
                $dias_natimorto = floor($diferenca / (60 * 60 * 24));
            }

            // verifica Aborto/Absorção
            $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='A' AND 
                      (tbl_mov_estoque_tipo_movimentacao='A' OR
                       tbl_mov_estoque_tipo_movimentacao='B') 
                ORDER BY tbl_mov_estoque_nascimento DESC");

            $num_aborto = mysqli_num_rows($tbl_aborto);

            if ($num_aborto==0) {
                $data_aborto = '0000-00-00';
            }
            else {
                $reg_aborto = mysqli_fetch_object($tbl_aborto);
                $data_aborto=$reg_aborto->tbl_mov_estoque_nascimento;
                $descricao_pai = '';

                $data_inicial = $reg_aborto->tbl_mov_estoque_nascimento;
                $data_final = date("Y-m-d");
                $diferenca = strtotime($data_final) - 
                             strtotime($data_inicial);
                $dias_aborto = floor($diferenca / (60 * 60 * 24));
            }
        }
        else {
            $descricao_pai = '';
            $numero_partos='';
            $numero_abortos='';
            $ultimo_parto_edi='';
            $data_aptidao_edi='';
            $idade_bezerro=0;
            $bezerro_ativo='';
            $coberturas_estacao='';
            $data_aborto = '0000-00-00';
            $data_natimorto = '0000-00-00';
            $ultimo_parto = '0000-00-00';
            $dias_natimorto = '';
            $dias_aborto = 0;
            $num_aborto = 0;
            $num_natimorto = 0;
        }

        if ($data_natimorto>$ultimo_parto || $data_aborto>$ultimo_parto) {
            if ($data_natimorto!='0000-00-00') {
                $ultimo_parto=new DateTime($reg_natimorto->tbl_mov_estoque_nascimento);
                $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');

                $data_aptidao_edi = date("d/m/Y", strtotime($reg_natimorto->tbl_mov_estoque_nascimento . "+ 35 days"));
                $ultimo_parto=$reg_natimorto->tbl_mov_estoque_nascimento;
            }
            else if ($data_aborto!='000-00-00') {
                $ultimo_parto=new DateTime($reg_aborto->tbl_mov_estoque_nascimento);
                $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');

                $ultimo_parto=$reg_aborto->tbl_mov_estoque_nascimento;

                $data_aptidao_edi = date("d/m/Y", strtotime($reg_aborto->tbl_mov_estoque_nascimento . "+ 35 days"));
            }
            // INFORMAR O PAI DO NATIMORTO AQUI
            // COPIAR ESSA ROTINA NO RELATORIO EXCEL
        }

        if ( ($vacas_paridas=='VP' && $ultimo_parto>=$data_paridas_de && 
            $ultimo_parto<=$data_paridas_ate && $numero_partos!=0 && $prenhe!='S') ||

            ($novilhas=='NO' && $idade>=$idade_de && $idade<=$idade_ate &&
             $numero_partos==0 && $dias_aborto==0) || 

            ($vacas_paridas=='' && $novilhas=='' && $vacas_solteiras=='' && $idade>=12 && $prenhe!='S') ||

            ($vacas_solteiras=='VS' && $numero_partos!=0 && $idade_bezerro>=8 && $prenhe!='S' && ($dias_aborto==0 || $dias_aborto>35) && ($dias_natimorto=='' || $dias_natimorto>35)) || 

            ($vacas_solteiras=='VS' && $numero_partos!=0 && $idade_bezerro<8 && $bezerro_ativo=='N' && $prenhe!='S' && ($dias_aborto==0 || $dias_aborto>35) && ($dias_natimorto=='' || $dias_natimorto>35)) ||

            ($vacas_solteiras=='VS' && $prenhe!='S' && $dias_aborto>35) 

            ) {


            if ($ultimo_peso>=$peso_acima && $selecionada_estacao=='') {
                $linha++;
                $celulas = 'B'.$linha;

                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                if ($descarte=='S') {
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, 'Descartada');
                }
 
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $codigo_ed);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $desc_raca);

                $celulas = 'D'.$linha.':G'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $idade_ano_mes);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $coberturas_estacao);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $numero_partos);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $numero_abortos);

                $celulas = 'H'.$linha.':I'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);


                if ($ultimo_parto_edi!='') {
                    $ultimo_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ultimo_parto_edi);                                            
                    $spreadsheet->getActiveSheet()->setCellValue('H'.$linha, $ultimo_parto_edi);
                }

                if ($data_aptidao_edi!='') {
                    $data_aptidao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_aptidao_edi);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $data_aptidao_edi);
                }

                $animais_listados++;

                if ($cobertura_gravada == '') {
                    if ($data_paridas_ate=='9999-99-99') {
                        $sql = "INSERT INTO tbl_cobertura (
                                        tbl_cobertura_controle,
                                        tbl_cobertura_data,
                                        tbl_cobertura_codigo_local,
                                        tbl_cobertura_codigo_grupo,
                                        tbl_cobertura_codigo_estacao_monta,
                                        tbl_cobertura_protocoloiatf,
                                        tbl_cobertura_qtd_animais,
                                        tbl_cobertura_filtros,
                                        tbl_cobertura_incluido_em,
                                        tbl_cobertura_incluido_por,
                                        tbl_cobertura_alterado_em,
                                        tbl_cobertura_alterado_por,
                                        tbl_cobertura_lixeira,
                                        tbl_cobertura_lixeira_em,
                                        tbl_cobertura_lixeira_por,
                                        tbl_cobertura_filtro_vacas_paridas,
                                        tbl_cobertura_filtro_data_paridas,
                                        tbl_cobertura_filtro_vacas_solteiras,
                                        tbl_cobertura_filtro_novilhas,
                                        tbl_cobertura_filtro_idade_de,
                                        tbl_cobertura_filtro_idade_ate,
                                        tbl_cobertura_filtro_peso_acima,
                                        tbl_cobertura_planilha_processada
                                    ) VALUES (
                                    '$controle',
                                    '$data_selecao',
                                    '$local',
                                    0,
                                    '$id_estacao_monta',
                                    0,
                                    0,
                                    '$descricao_filtro',
                                    '$data_hora_sistema',
                                    '$nomeusuario',
                                    null,
                                    null,
                                    0,
                                    null,
                                    null,
                                    '$vacas_paridas',
                                    null,
                                    '$vacas_solteiras',
                                    '$novilhas',
                                    '$idade_de_gravar',
                                    '$idade_ate_gravar',
                                    '$peso_acima_gravar',
                                    ''
                                )";
                    }
                    else {
                        $sql = "INSERT INTO tbl_cobertura (
                                            tbl_cobertura_controle,
                                            tbl_cobertura_data,
                                            tbl_cobertura_codigo_local,
                                            tbl_cobertura_codigo_grupo,
                                            tbl_cobertura_codigo_estacao_monta,
                                            tbl_cobertura_protocoloiatf,
                                            tbl_cobertura_qtd_animais,
                                            tbl_cobertura_filtros,
                                            tbl_cobertura_incluido_em,
                                            tbl_cobertura_incluido_por,
                                            tbl_cobertura_alterado_em,
                                            tbl_cobertura_alterado_por,
                                            tbl_cobertura_lixeira,
                                            tbl_cobertura_lixeira_em,
                                            tbl_cobertura_lixeira_por,
                                            tbl_cobertura_filtro_vacas_paridas,
                                            tbl_cobertura_filtro_data_paridas,
                                            tbl_cobertura_filtro_vacas_solteiras,
                                            tbl_cobertura_filtro_novilhas,
                                            tbl_cobertura_filtro_idade_de,
                                            tbl_cobertura_filtro_idade_ate,
                                            tbl_cobertura_filtro_peso_acima,
                                            tbl_cobertura_planilha_processada
                                        ) VALUES (
                                        '$controle',
                                        '$data_selecao',
                                        '$local',
                                        0,
                                        '$id_estacao_monta',
                                        0,
                                        0,
                                        '$descricao_filtro',
                                        '$data_hora_sistema',
                                        '$nomeusuario',
                                        null,
                                        null,
                                        0,
                                        null,
                                        null,
                                        '$vacas_paridas',
                                        '$data_paridas_ate',
                                        '$vacas_solteiras',
                                        '$novilhas',
                                        '$idade_de_gravar',
                                        '$idade_ate_gravar',
                                        '$peso_acima_gravar',
                                        ''
                                    )";
                    }

                    $resultado = mysqli_query($conector,$sql);
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado){
                        header('Content-type: application/json');
                        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o grupo de fêmeas'. $erro_mysql));
                        mysqli_close($conector);
                        exit;
                    } 

                    $numero_cobertura = mysqli_insert_id($conector);
                    $numero_cobertura = str_pad($numero_cobertura, 9, "0", STR_PAD_LEFT);
                    $cobertura_gravada = 'S';
                }

                $codigo_numerico = ltrim($codigo_num, "0");

                if ($codigo_alfa==''){
                    $codigo_edi = $codigo_numerico; 
                }
                else {
                    $codigo_edi = $codigo_alfa.'-'.$codigo_numerico; 
                }

                $numero_item++;

                $sql = "INSERT INTO tbl_item_cobertura (
                                        tbl_ite_cobertura_numero_id,
                                        tbl_ite_cobertura_numero_item,
                                        tbl_ite_cobertura_codigo_id_animal,
                                        tbl_ite_cobertura_codigo_animal,
                                        tbl_ite_cobertura_data_emissao,
                                        tbl_ite_cobertura_codigo_touro_semen,
                                        tbl_ite_cobertura_lote_semen,
                                        tbl_ite_cobertura_data_diagnostico,
                                        tbl_ite_cobertura_resultado_diagnostico,
                                        tbl_ite_cobertura_nome_inseminador,
                                        tbl_ite_cobertura_destino,
                                        tbl_ite_cobertura_dia_1,
                                        tbl_ite_cobertura_dia_2,
                                        tbl_ite_cobertura_dia_3,
                                        tbl_ite_cobertura_dia_4,
                                        tbl_ite_cobertura_dia_5,
                                        tbl_ite_cobertura_dia_6,
                                        tbl_ite_cobertura_observacao,
                                        tbl_ite_cobertura_numero_cobertura
                                )
                                VALUES ('$numero_cobertura', 
                                        '$numero_item',
                                        '$codigo_id',
                                        '$codigo_edi',
                                        '$data_selecao',
                                        null,
                                        null,
                                        null,
                                        null,
                                        null,
                                        null,
                                        null,
                                        null,
                                        null,
                                        null,
                                        null,
                                        null,
                                        null,
                                        0
                        )";
                                                               
                $resultado = mysqli_query($conector, $sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado) {
                    header('Content-type: application/json');
                    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o item do registro da cobertura'. $erro_mysql));
                    mysqli_close($conector);
                    exit;
                }
            }
        }
    } 
}

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 3, $numero_cobertura);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, 3, $animais_listados);

if ($numero_cobertura !=0) {
    $sql = "UPDATE tbl_cobertura SET tbl_cobertura_qtd_animais='$animais_listados'
             WHERE tbl_cobertura_id='$numero_cobertura'";

    $resultado = mysqli_query($conector,$sql);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a cobertura descarte'. $erro_mysql));
        mysqli_close($conector);
        exit;
    } 
}


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

$nome_arquivo = "lista_femeas_cobertura_" .$numero_cobertura. ".xlsx";
// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$nome_arquivo.'"');
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
              
                
