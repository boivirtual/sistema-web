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
include_once "conecta_mysql_credenciais.inc";
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

    $tipo_registro = ltrim($_REQUEST['tipo_registro']);
    $local = ltrim($_REQUEST['local']);
    $vacas_paridas = $_REQUEST['vacas_paridas'];
    //$data_paridas_ate = $_REQUEST['data_paridas'];
    $vacas_solteiras = $_REQUEST['vacas_solteiras'];
    $novilhas = $_REQUEST['novilhas'];
    $idade_de = $_REQUEST['idade_de'];
    $idade_ate = $_REQUEST['idade_ate'];
    $peso_acima = $_REQUEST['peso_acima'];
    $id_parametro_estacao = $_REQUEST['id_estacao_monta'];
    $desc_filtro = $_REQUEST['filtros'];
    $id_cobertura = 0;

    // Em 21/03/2025 a data Paridas Até passou a ser calculado 35 dias a menos da data digita na tela no input Apta Em conforme o trello:

    // Cartão: MELHORIA NA REPRODUÇÃO (PARA DEPOIS DA ESTAÇÃO 24/2025)
    // Cheklist: MELHORAR A USABILIDADE: DIMINUIR CLIQUES E MELHORAR MENSAGENS NA TELA 

    // Subtrair 35 dias da Data digitada
    $aptasEm = $_REQUEST['data_paridas'];
    $data_paridas_ate = date("Y-m-d", strtotime($aptasEm . "- 35 days"));

    if ($vacas_paridas=='VP') {
        $filtro_paridas='S';
    }
    else {
        $filtro_paridas='';
    }

    if ($vacas_solteiras=='VS') {
        $filtro_solteiras='S';
    }
    else {
        $filtro_solteiras='';
    }

    if ($novilhas=='NO') {
        $filtro_novilhas='S';
    }
    else {
        $filtro_novilhas='';
    }

    if ($filtro_paridas=='' && $filtro_solteiras=='' && $filtro_novilhas=='') {
        $sem_filtros = 'S';
    }
    else {
        $sem_filtros = '';
    }

    if ($idade_ate=='') {
        $idade_ate=9999;
    }

    if ($peso_acima==0) {
        $peso_acima=1;
    }

    $peso_acima = floatval($peso_acima);

    //$data_paridas_de = date("Y-m-d", strtotime('-8 month',strtotime($data_paridas_ate)));
    //$data_paridas_de = date("Y-m-d", strtotime('-1 day',strtotime($data_paridas_de)));

   // pega periodo da estacao de monta atual

    $tbl_estacao_monta = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
            WHERE tbl_par_estacao_id = '$id_parametro_estacao'");

    $num_row = mysqli_num_rows($tbl_estacao_monta);

    if ($num_row!=0) {
        $reg_estacao = mysqli_fetch_object($tbl_estacao_monta);
        $inicio_estacao = $reg_estacao->tbl_par_estacao_monta_inicial;
        $fim_estacao = $reg_estacao->tbl_par_estacao_monta_final;
    }
    else {
        $inicio_estacao = '0000-00-00';
        $fim_estacao = '9999-99-99';
    }

$animais_listados = 0;

$cobertura_gravada = '';
$numero_item = 0;
$numero_cobertura = 0;
$linha=5;

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


    // LISTA MATRIZES ON-LINE 1ª PREMISSA SEXO = F

    /*$sql = mysqli_query($conector, "SELECT * FROM tbl_animais
        WHERE tbl_animal_codigo_id=3969 OR tbl_animal_codigo_id=4231");*/

    $sql = mysqli_query($conector, "SELECT * FROM tbl_animais
        WHERE tbl_animal_sexo='F' AND 
              tbl_animal_ativo='S' AND
              tbl_animal_lixeira=0 AND 
              tbl_animal_codigo_fazenda='$local'
        ORDER BY tbl_animal_codigo_numerico ASC");
    $num_row = mysqli_num_rows($sql);

    //print_r('Registros encontrados aqui: ' . $num_row . '</br>');
    //exit;

    if ($num_row!=0) {
        while ($reg_animal = mysqli_fetch_object($sql)){
            $codigo_id= $reg_animal->tbl_animal_codigo_id;
            $codigo_alfa= $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico= $reg_animal->tbl_animal_codigo_numerico;
            $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
            $ultimo_peso=  floatval($reg_animal->tbl_animal_ultimo_peso);
            $codigo_raca= $reg_animal->tbl_animal_codigo_raca;
            $descarte = $reg_animal->tbl_animal_descarte_reproducao;

            //print_r('Id Animal: ' . $codigo_numerico . ' Local: ' . $local .' Estacao: '. $id_parametro_estacao . '</br>');

            if ($ultimo_peso==0) {
                $ultimo_peso = 1;
            }

            // VERIFICA IDADE > 12 2ª PREMISSA

            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            $idade_ano_mes = $idade_acompanhamento->format('%Y') .' a/ '. 
                    str_pad($idade_acompanhamento->format('%m') , 2 , '0' , STR_PAD_LEFT) . ' m';

            // VERIFICA SE A VACA ESTA PRENHE 3º PREMISSA

            $tbl_prenhe = mysqli_query($conector, "SELECT * FROM tbl_cobertura
                INNER JOIN tbl_item_cobertura 
                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal='$codigo_id' AND  
                      tbl_ite_cobertura_resultado_diagnostico='P' AND  
                      (tbl_ite_cobertura_nascido='' OR 
                       tbl_ite_cobertura_nascido IS NULL)");

            $num_rows_prenhe = mysqli_num_rows($tbl_prenhe);

            //print_r('Prenha: ' . $num_rows_prenhe .'</br>');

            //VERIFICA SE A FÊMEA JA FOI SELECIONADA NESSA ESTACAO 4ª PREMISSA

            $femea_selecionada = '';

            $tbl_selecao = mysqli_query($conector, "SELECT  * FROM tbl_cobertura
                INNER JOIN tbl_item_cobertura 
                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_cobertura_codigo_local = '$local' AND 
                      tbl_cobertura_codigo_estacao_monta = '$id_parametro_estacao' AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo_id'
                ORDER BY tbl_ite_cobertura_numero_id DESC LIMIT 1");

            $selecionada_estacao = mysqli_num_rows($tbl_selecao);

            if ($selecionada_estacao!=0) {
                $femea_selecionada = 'S';
                $reg_selecao = mysqli_fetch_object($tbl_selecao);
                $diagnostico_selecao = $reg_selecao->tbl_ite_cobertura_resultado_diagnostico;

                if ($diagnostico_selecao=='N') {
                    $femea_selecionada = '';
                }
            } 

            //VERIFICA SE A FÊMEA JA FOI SELECIONADA NA LISTA DE MONTA 
            $cobertura_controle = '';

            $tbl_selecao = mysqli_query($conector, "SELECT  * FROM tbl_cobertura
                INNER JOIN tbl_item_cobertura 
                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                     WHERE tbl_cobertura_lixeira=0 AND 
                           tbl_cobertura_codigo_local = '$local' AND 
                           tbl_cobertura_controle = 'M' AND
                           tbl_ite_cobertura_codigo_id_animal = '$codigo_id'
                  ORDER BY tbl_ite_cobertura_numero_id DESC LIMIT 1");

            $selecionada_monta = mysqli_num_rows($tbl_selecao);

            if ($selecionada_monta!=0) {
                $reg_selecao = mysqli_fetch_object($tbl_selecao);
                $diagnostico_selecao = $reg_selecao->tbl_ite_cobertura_resultado_diagnostico;
                $cobertura_controle = $reg_selecao->tbl_cobertura_controle;
                $nascido = $reg_selecao->tbl_ite_cobertura_nascido;

                $femea_selecionada = 'S';

                if ($diagnostico_selecao=='N' || $nascido=='A' || $nascido=='M' || $nascido=='O') {
                    $femea_selecionada = '';
                }
            }
            //print_r('Selecionada Na Estacao: ' . $femea_selecionada .'</br>');

            // VERIFICA SE ANIMAL TEM PARTO A MENOS DE 35 DIAS
            $data_nasc_bezerro = '0000-00-00';

            $tbl_filhos = mysqli_query($conector,"SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo_id'
                ORDER BY tbl_animal_codigo_id  DESC LIMIT 1"); 

            $numero_rows_partos = mysqli_num_rows($tbl_filhos);

            if ($numero_rows_partos!=0) {
                $reg_parto = mysqli_fetch_object($tbl_filhos);
                $codigo_bezerro = $reg_parto->tbl_animal_codigo_numerico;
                $data_nasc_bezerro=$reg_parto->tbl_animal_data_nascimento;
                $bezerro_ativo = $reg_parto->tbl_animal_ativo;
                $bezerro_situacao = $reg_parto->tbl_animal_situacao;

                $data_ref = date("Y-m-d", strtotime($data_hoje . "- 35 days"));

                if ($bezerro_situacao=='M') {
                    $data_morte = substr($reg_parto->tbl_animal_baixado_em, 0, 10) ;
                }
                else {
                    $data_morte = '0000-00-00';
                }

                $diferenca = strtotime($data_ref) - strtotime($data_nasc_bezerro);
                $dias_parto = floor($diferenca / (60 * 60 * 24));

                $animal_tem_parto = 'S';

                //if ($dias_parto>35 && $cobertura_controle=='M') {
                    //$femea_selecionada = '';
                //}
                //print_r ('Dias de parto: '. $dias_parto . '</br>');

            }
            else {
                $animal_tem_parto = 'N';
            }

            //print_r('Tem Parto: ' . $animal_tem_parto . ' Dias Parto: '.$dias_parto.'</br>');

            // VERIFICA TAMBEM SE TEVE NATIMORTO A MENOS 35 DIAS
            $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='S' AND 
                      tbl_mov_estoque_tipo_movimentacao='M' 
                ORDER BY tbl_mov_estoque_nascimento DESC");

            $num_natimorto = mysqli_num_rows($tbl_natimorto);

            if ($num_natimorto!=0) {
                $reg_natmorto = mysqli_fetch_object($tbl_natimorto);

                if ($reg_natmorto->tbl_mov_estoque_nascimento>$data_nasc_bezerro) {
                    $data_nasc_bezerro=$reg_natmorto->tbl_mov_estoque_nascimento;

                    $data_ref = date("Y-m-d", strtotime($data_hoje . "- 35 days"));

                    $diferenca = strtotime($data_ref) - strtotime($data_nasc_bezerro);
                    $dias_parto = floor($diferenca / (60 * 60 * 24));

                    $animal_tem_parto = 'S';

                    //if ($dias_parto>35 && $cobertura_controle=='M') {
                        //$femea_selecionada = '';
                   // }

                    //print_r ('Dias de natimorto: '. $dias_parto . '</br>');

                }
            }

            // VERIFICAR FILTROS DE VACAS PARIDAS

            $vaca_parida='N';

            if ($filtro_paridas=='S') {
                if ($animal_tem_parto=='S') {
                    if ($data_nasc_bezerro<=$data_paridas_ate) {

                        //print_r('Bezerro: ' . $data_nasc_bezerro.'</br>');

                        $date = new DateTime($data_nasc_bezerro); // Data de Nascimento do bezzero
                        $idade_acompanhamento = $date->diff(new DateTime($data_paridas_ate));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade_bezerro = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                        if ($idade_bezerro<8) {
                            if ($bezerro_situacao!='M') {

                                $aborto = VerAborto($conector, $codigo_id, $data_paridas_ate);

                                if ($aborto[0]=='S' && $aborto[2]>=$data_nasc_bezerro) {

                                    if ($aborto[2]<=$data_paridas_ate) {
                                        $vaca_parida='S';
                                    }
                                    else {
                                        $vaca_parida='N';
                                    }
                                }
                                else {
                                   $vaca_parida='S'; 
                                }
                            }
                        }
                    }
                }
            }

            //print_r('Parida: ' . $vaca_parida . ' Idade Bezerro: '.$idade_bezerro.'</br>');

            // VERIFICAR FILTROS DE VACAS SOLTEIRAS

            $vaca_solteira='N';
            
            if ($filtro_solteiras=='S') {
                //print_r('Vou ver solteiras, tem partos?' . $animal_tem_parto . '</br>');

                if ($animal_tem_parto=='S') { // alternativas 1, 2, 3, 4, 5, 6
                    $date = new DateTime($data_nasc_bezerro); // Data de Nascimento do bezzero
                    $idade_acompanhamento = $date->diff(new DateTime($data_hoje));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade_bezerro = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    //print_r('Idade Bezzero: ' . $idade_bezerro . '</br>');

                    if ($idade_bezerro>=8) { // alternativas 1, 2, 3, 4

                        if ($bezerro_situacao!='M') { // 1, 2, bezerro vivo

                            $aborto = VerAborto($conector, $codigo_id, $data_hoje);
                            
                            if ($aborto[0]=='N') { // alternativa 1
                                $vaca_solteira='S';
                            }
                            else { // alternativa 2
                                $data_aborto = $aborto[2];
                                $data_ref = CalcularDataRef($data_hoje);

                                if ($data_aborto<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                        }
                        else { // 3, 4 bezzero não está vivo
                            $aborto = VerAborto($conector, $codigo_id, $data_hoje);

                            if ($aborto[0]=='S') { // alternativa 3
                                $data_aborto = $aborto[2];
                                $data_ref = CalcularDataRef($data_hoje);

                                if ($data_aborto<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                            else { // alternativa 4
                                $vaca_solteira='S';
                            }
                        }
                    }
                    else { // alternativas 5, 6
                        if ($bezerro_situacao=='M') { // Bezerro vivo não

                            //print_r('Situacao Bezerro: ' . $bezerro_situacao  . '</br>');

                            $aborto = VerAborto($conector, $codigo_id, $data_hoje);


                            if ($aborto[0]=='S') { // alternativa 5
                                $data_aborto = $aborto[2];
                                $data_ref = CalcularDataRef($data_hoje);

                                if ($data_aborto<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                            else { // alternativa 6
                                $data_ref = CalcularDataRef($data_hoje);

                                //print_r('Data Nascimento ' . $data_nasc_bezerro . ' Data Ref: ' . $data_ref . '</br>');

                                if ($data_nasc_bezerro<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                        }
                    }
                }
                else { // alternativa 7
                    $aborto = VerAborto($conector, $codigo_id, $data_hoje);

                    if ($aborto[0]=='S') {
                        $data_aborto = $aborto[2];
    
                        $data_ref = CalcularDataRef($data_hoje);

                        if ($data_aborto<=$data_ref) {
                            $vaca_solteira='S';
                        }
                    }

                    //print_r('Vou ver natimorto');

                    $natimorto = VerNatimorto($conector, $codigo_id, $data_hoje);

                    if ($natimorto[0]=='S') {
                        $data_natimorto = $natimorto[2];
    
                        $data_ref = CalcularDataRef($data_hoje);

                        //print_r('Natimorto: ' . $data_natimorto . 
                               // ' Data Ref: ' . $data_ref . 
                               //' Dias: ' . $natimorto[1]);

                        if ($data_natimorto<=$data_ref) {
                            $vaca_solteira='S';
                        }
                    }
                    else {
                       $data_natimorto='0000-00-00'; 
                    }
                }
            }

            // VERIFICAR FILTROS DE NOVILHAS

            $novilha='N';
            
            if ($filtro_novilhas=='S') {
                if ($idade>=$idade_de && $idade<=$idade_ate) {
                    if ($animal_tem_parto=='N') {
                        $aborto = VerAborto($conector, $codigo_id, $data_hoje);
                        if ($aborto[0]=='N') {
                            $novilha='S';
                        }
                    }
                }
            }

            // VERIFICAR ANIMAIS SEM FILTRO
            $imp_esse_sem_filtro = '';

            if ($sem_filtros=='S') {
                if ($animal_tem_parto=='S') {

                    if ($dias_parto>=35) {
                        $aborto = VerAborto($conector, $codigo_id, $data_hoje);

                        if ($aborto[0]=='S') {
                            if ($aborto[1]>=35) {
                                $imp_esse_sem_filtro = 'S';
                            }
                            else {
                               $imp_esse_sem_filtro = 'N'; 
                            }
                        }
                        else {
                            $imp_esse_sem_filtro = 'S';
                        }
                    }
                    else {
                        $imp_esse_sem_filtro = 'N';
                    }
                }
                else {
                    $aborto = VerAborto($conector, $codigo_id, $data_hoje);
                    
                    if ($aborto[0]=='S') {
                        if ($aborto[1]>=35) {
                            $imp_esse_sem_filtro = 'S';
                        }
                        else {
                           $imp_esse_sem_filtro = 'N'; 
                        }
                    }
                    else {
                        $imp_esse_sem_filtro = 'S';
                    }
                }
            }

            // TESTAR PREMISSAS 2ª, 3ª, 4ª E PESO

            if ($num_rows_prenhe==0 && $idade>=12 && $femea_selecionada=='' && $ultimo_peso>=$peso_acima) {

                if ($sem_filtros=='S' && $imp_esse_sem_filtro=='S') {

                    $listados = ImprimirFemea($conector, $spreadsheet, $animais_listados, $codigo_id, $codigo_alfa, $codigo_numerico, $data_nascimento, $ultimo_peso, $codigo_raca, $descarte, $local, $id_parametro_estacao, $idade_ano_mes, $cobertura_gravada, $numero_item, $numero_cobertura, $linha, $tipo_registro);

                    $animais_listados=$listados[0];
                    $cobertura_gravada=$listados[1]; 
                    $numero_item=$listados[2]; 
                    $numero_cobertura=$listados[3]; 
                    $linha=$listados[4];
                }
                else if ($filtro_solteiras==$vaca_solteira) {
                    $listados = ImprimirFemea($conector, $spreadsheet, $animais_listados, $codigo_id, $codigo_alfa, $codigo_numerico, $data_nascimento, $ultimo_peso, $codigo_raca, $descarte, $local, $id_parametro_estacao, $idade_ano_mes, $cobertura_gravada, $numero_item, $numero_cobertura, $linha, $tipo_registro);

                    $animais_listados=$listados[0];
                    $cobertura_gravada=$listados[1]; 
                    $numero_item=$listados[2]; 
                    $numero_cobertura=$listados[3]; 
                    $linha=$listados[4];
                }
                else if ($filtro_paridas==$vaca_parida) {
                    $listados = ImprimirFemea($conector, $spreadsheet, $animais_listados, $codigo_id, $codigo_alfa, $codigo_numerico, $data_nascimento, $ultimo_peso, $codigo_raca, $descarte, $local, $id_parametro_estacao, $idade_ano_mes, $cobertura_gravada, $numero_item, $numero_cobertura, $linha, $tipo_registro);

                    $animais_listados=$listados[0];
                    $cobertura_gravada=$listados[1]; 
                    $numero_item=$listados[2]; 
                    $numero_cobertura=$listados[3]; 
                    $linha=$listados[4];
                }
                else if ($filtro_novilhas==$novilha) {
                    $listados = ImprimirFemea($conector, $spreadsheet, $animais_listados, $codigo_id, $codigo_alfa, $codigo_numerico, $data_nascimento, $ultimo_peso, $codigo_raca, $descarte, $local, $id_parametro_estacao, $idade_ano_mes, $cobertura_gravada, $numero_item, $numero_cobertura, $linha, $tipo_registro);

                    $animais_listados=$listados[0];
                    $cobertura_gravada=$listados[1]; 
                    $numero_item=$listados[2]; 
                    $numero_cobertura=$listados[3]; 
                    $linha=$listados[4];
                }
                
            } // FIM DO $num_rows_prenhe==0 && $idade>=12 && 
              //$femea_selecionada=='' && $ultimo_peso>=$peso_acima

        } // FIM DO while

    } // FIM DO $num_row!=0

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


// VERIFICA SE TEVE ABORTO
function VerAborto($conector, $codigo_id, $data_ref) {
    $teve_aborto = 'N';
    $dias_aborto = 0;
    $data_aborto = '0000.00.00';

    $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
        WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
              tbl_mov_estoque_codigo_id_animal=999999999 AND
              tbl_mov_estoque_entrada_saida='A' AND 
              (tbl_mov_estoque_tipo_movimentacao='A' OR
               tbl_mov_estoque_tipo_movimentacao='B') 
        ORDER BY tbl_mov_estoque_nascimento DESC");

    $num_aborto = mysqli_num_rows($tbl_aborto);

    if ($num_aborto!=0) {
        $teve_aborto = 'S';
    }

    if ($teve_aborto == 'S') {
        $reg_aborto = mysqli_fetch_object($tbl_aborto);

        $data_aborto=$reg_aborto->tbl_mov_estoque_nascimento;
        $data_ref = date("Y-m-d", strtotime($data_ref . "- 35 days"));

        $diferenca = strtotime($data_ref) - strtotime($data_aborto);
        $dias_aborto = floor($diferenca / (60 * 60 * 24));
    }

    return [$teve_aborto, $dias_aborto, $data_aborto];
}

// VERIFICA SE TEVE NATMORTO
function VerNatimorto($conector, $codigo_id, $data_ref) {
    $teve_natimorto = 'N';
    $dias_natimorto = 0;
    $data_natimorto = '0000.00.00';

    $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
        WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
              tbl_mov_estoque_codigo_id_animal=999999999 AND
              tbl_mov_estoque_entrada_saida='S' AND 
              tbl_mov_estoque_tipo_movimentacao='M' 
        ORDER BY tbl_mov_estoque_nascimento DESC");

    $num_natimorto = mysqli_num_rows($tbl_natimorto);

    if ($num_natimorto!=0) {
        $teve_natimorto = 'S';
    }

    if ($teve_natimorto == 'S') {
        $reg_natimorto = mysqli_fetch_object($tbl_natimorto);

        $data_natimorto=$reg_natimorto->tbl_mov_estoque_nascimento;
        $data_ref = date("Y-m-d", strtotime($data_ref . "- 35 days"));

        $diferenca = strtotime($data_ref) - strtotime($data_natimorto);
        $dias_natimorto = floor($diferenca / (60 * 60 * 24));
    }

    return [$teve_natimorto, $dias_natimorto, $data_natimorto];
}

// CALCULA A DATA DE REF - 35 DIAS
function CalcularDataRef($data_ref) {
    $data_ref = date("Y-m-d", strtotime($data_ref . "- 35 days"));
    return $data_ref;
}

// IMPRIME A FEMEA
function ImprimirFemea($conector, $spreadsheet, $animais_listados, $codigo_id, $codigo_alfa, $codigo_numerico, $data_nascimento, $ultimo_peso, $codigo_raca, $descarte, $local, $id_parametro_estacao, $idade_ano_mes, $cobertura_gravada, $numero_item, $numero_cobertura, $linha, $tipo_registro) {

    $data_hoje = date("Y-m-d");

    if ($tipo_registro=='I') {
        $controle = 'C';
        $planilha_processada = '';
    }
    else {
        $controle = 'M';
        $planilha_processada = 'A';
    }

    $descricao_filtro = utf8_decode($_REQUEST['filtros']);
    $data_selecao = date("Y-m-d");
    $data_hora_sistema = date("Y-m-d H:i:s");
    $vacas_paridas = $_REQUEST['vacas_paridas'];
    $data_paridas_ate = $_REQUEST['data_paridas'];
    $vacas_solteiras = $_REQUEST['vacas_solteiras'];
    $novilhas = $_REQUEST['novilhas'];
    $idade_de = $_REQUEST['idade_de'];
    $idade_ate = $_REQUEST['idade_ate'];
    $peso_acima = $_REQUEST['peso_acima'];

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

    if ($peso_acima=='' || $peso_acima==0) {
        $peso_acima_gravar=0;
    }
    else {
        $peso_acima_gravar=$peso_acima;
    }

    @ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

    $codigo_numerico = ltrim($codigo_numerico, "0");

    if ($codigo_alfa==''){
        $codigo_edi = $codigo_numerico; 
    }
    else {
        $codigo_edi = $codigo_alfa.'-'.$codigo_numerico; 
    }

    $tbl_raca = mysqli_query($conector,"SELECT * FROM tabela_racas 
        WHERE tab_codigo_raca ='$codigo_raca' AND 
              tab_registro_lixeira_raca = 0"); 

    $num_row_raca = mysqli_num_rows($tbl_raca);

    if ($num_row_raca!=0) {
        $reg_raca = mysqli_fetch_object($tbl_raca);
        $desc_raca = utf8_encode($reg_raca->tab_descricao_raca);
    }
    else {
        $desc_raca = '';
    }

    // VERIFICA NUMERO DE PARTOS 
    $tbl_filhos = mysqli_query($conector,"SELECT * FROM tbl_animais 
        WHERE tbl_animal_codigo_mae='$codigo_id'"); 

    $numero_partos = mysqli_num_rows($tbl_filhos);

    $tbl_filhos = mysqli_query($conector,"SELECT * FROM tbl_animais 
        WHERE tbl_animal_codigo_mae='$codigo_id'
        ORDER BY tbl_animal_codigo_numerico DESC LIMIT 1"); 

    $numero_rows_partos = mysqli_num_rows($tbl_filhos);

    if ($numero_rows_partos!=0) {
        while ($reg_filhos = mysqli_fetch_object($tbl_filhos)){
            $codigo_pai=$reg_filhos->tbl_animal_codigo_pai;
            $bezerro_ativo = $reg_filhos->tbl_animal_ativo;
            $bezerro_situacao = $reg_filhos->tbl_animal_situacao;

            $ultimo_parto=new DateTime($reg_filhos->tbl_animal_data_nascimento);
            $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');

            $data_aptidao_edi = date("d/m/Y", strtotime($reg_filhos->tbl_animal_data_nascimento . "+ 35 days"));

            $ultimo_parto=$reg_filhos->tbl_animal_data_nascimento;

            $data_nascimento = $reg_filhos->tbl_animal_data_nascimento;
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade_bezerro = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            if ($bezerro_ativo=='N' && $bezerro_situacao=='M') {
                $data_inicial = $reg_filhos->tbl_animal_baixado_em;
                $data_final = date("Y-m-d");
                $diferenca = strtotime($data_final) - strtotime($data_inicial);
                $dias_natimorto = floor($diferenca / (60 * 60 * 24));

                if ($dias_natimorto<=0) {
                    $dias_natimorto=1;
                }
            }
        }
    }
    else {
        $codigo_pai = 0;
        $ultimo_parto_edi = '';
        $data_aptidao_edi = '';
        $ultimo_parto = '0000-00-00';
    }

    $tab_pai = mysqli_query($conector, "SELECT * FROM tbl_semem 
        WHERE tbl_semem_codigo_id='$codigo_pai'");

    $num_rows_pai = mysqli_num_rows($tab_pai);

    if ($num_rows_pai!=0){
        $reg = mysqli_fetch_object($tab_pai);
        $descricao_pai = $reg->tbl_semem_nome;
    }
    else {
        $tab_pai = mysqli_query($conector, "SELECT * FROM tbl_animais WHERE tbl_animal_codigo_id='$codigo_pai'");

        $num_rows_pai = mysqli_num_rows($tab_pai);

        if ($num_rows_pai!=0){
            $reg = mysqli_fetch_object($tab_pai);
            $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
        }
        else {
            $descricao_pai = '';
        }
    }

    $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
        WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
              tbl_mov_estoque_codigo_id_animal=999999999 AND 
              tbl_mov_estoque_entrada_saida='A'");

    $numero_abortos = mysqli_num_rows($tbl_aborto);

    // VERIFICA NATIMORTO E SOMA NO NUMERO DE PARTOS/DATA APTIDAO
    $natimorto = VerNatimorto($conector, $codigo_id, $data_hoje);

    if ($natimorto[0]=='S') {

        $data_natimorto = $natimorto[2];
        $numero_partos+=1;

        if ($data_natimorto > $ultimo_parto) {
            $ultimo_parto=new DateTime($data_natimorto);
            $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');
            $ultimo_parto=$data_natimorto;

            $tem_natimorto = 'S';
        }
        else {
            $tem_natimorto = 'N';
            $data_natimorto = '0000-00-00';
        }
    }
    else {
        $tem_natimorto = 'N';
        $data_natimorto = '0000-00-00';
    }

    // VERIFICA ABORTO PARA CALCULAR A DATA DE APTIDAO
    $aborto = VerAborto($conector, $codigo_id, $data_hoje);
    if ($aborto[0]=='S') {
        $data_aborto = $aborto[2];
    }
    else {
        $data_aborto = '0000-00-00';
    }

    // Verifica qual data será considerada para calcular a aptidao

    if ($data_natimorto=='0000-00-00' && $data_aborto=='0000-00-00') {
        $data_aborto_natimorto='0000-00-00';
    }
    else if ($data_natimorto>$data_aborto){
        $data_aborto_natimorto=$data_natimorto;
    }
    else if ($data_aborto>$data_natimorto) {
        $data_aborto_natimorto=$data_aborto;
    }

    if ($ultimo_parto!='0000-00-00') {
        $data_aptidao_edi = date("d/m/Y", strtotime($ultimo_parto . "+ 35 days"));
    }

    if ($data_aborto_natimorto!='0000-00-00' && $data_aborto_natimorto>$ultimo_parto) {
        $data_aptidao_edi = date("d/m/Y", strtotime($data_aborto_natimorto . "+ 35 days"));
    }

    $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura
        INNER JOIN tbl_item_cobertura 
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
        WHERE tbl_cobertura_lixeira=0 AND 
              tbl_cobertura_codigo_local = '$local' AND 
              tbl_cobertura_codigo_estacao_monta = '$id_parametro_estacao' AND 
              tbl_ite_cobertura_codigo_id_animal='$codigo_id' AND
              (tbl_ite_cobertura_resultado_diagnostico='P' or
               tbl_ite_cobertura_resultado_diagnostico='N')");

    $coberturas_estacao = mysqli_num_rows($tbl_cobertura);

    $linha++;
    $celulas = 'B'.$linha;

    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    if ($descarte=='S') {
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, 'Descartada');
        $celulas = 'A'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
    }

    if ($codigo_alfa=='') {
        $codigo_ed = intval($codigo_numerico);
    }
    else {
        $codigo_ed = $codigo_alfa.'-'.intval($codigo_numerico);
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

    if ($tem_natimorto=='S') {
        $celulas = 'H'.$linha;
        $spreadsheet->getActiveSheet()->getComment($celulas)->setAuthor('');
        $commentRichText = $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Mensagem');
        $commentRichText->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun("\r\n");
        $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Considerado aqui a data do Natimorto.');
        $spreadsheet->getActiveSheet()->getComment($celulas)->setWidth('100pt');
        $spreadsheet->getActiveSheet()->getComment($celulas)->setHeight('50pt');
        $spreadsheet->getActiveSheet()->getComment($celulas)->setMarginLeft('100pt');
        $spreadsheet->getActiveSheet()->getComment($celulas)->getFillColor()->setRGB('EEEEEE');
    }

    if ($data_aptidao_edi!='') {
        $data_aptidao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_aptidao_edi);

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $data_aptidao_edi);
    }

    $animais_listados++;

    if ($cobertura_gravada == '') {
        if ($data_paridas_ate=='') {
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
                '$id_parametro_estacao',
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
                '$planilha_processada'
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
                '$id_parametro_estacao',
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
                '$planilha_processada'
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

    $numero_item++;

    $sql = "INSERT INTO tbl_item_cobertura (
        tbl_ite_cobertura_numero_id,
        tbl_ite_cobertura_numero_item,
        tbl_ite_cobertura_codigo_id_animal,
        tbl_ite_cobertura_codigo_animal,
        tbl_ite_cobertura_codigo_alfa,
        tbl_ite_cobertura_codigo_numerico,
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
                '$codigo_alfa',
                '$codigo_numerico',
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

    return [$animais_listados, $cobertura_gravada, $numero_item, $numero_cobertura, $linha];
}                


?>
              
                
