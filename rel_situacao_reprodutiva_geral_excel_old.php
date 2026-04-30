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


    $_SESSION['opcao_situacao_reprodutica_rel']='G';

    $local_filtro = $_GET["local"];

    if (isset($_GET['estacao'])) {
        $estacao_filtro = $_GET['estacao'];

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

        $westacao = " AND  tbl_cobertura_codigo_estacao_monta IN(";
        $westacao.= implode(',', $array_estacao);
        $westacao.= ")";
    }

    $local= array();
    $matriz_itens = explode(",", $local_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $local[$i]=$matriz_itens[$i];
    }

    $local = implode(',', $local);
    $local = substr($local,0, -1);

    $wlocal = '';

    if ($local_filtro!='') {
        $wlocal = " AND tbl_animal_codigo_fazenda IN(";
        $wlocal.= $local;
        $wlocal.= ")";
    }

    $worigem = '';

    if (isset($_GET['origem'])) {
        $origem_filtro = $_GET["origem"];

        $origem= array();
        $matriz_itens = explode(",", $origem_filtro);
        $quantidade_itens = count($matriz_itens);

        for($i=0; $i < $quantidade_itens; $i++) {
            $origem[$i]=$matriz_itens[$i];
        }

        $origem = implode(',', $origem);
        $origem = substr($origem,0, -1);

        if ($origem_filtro!='') {
            $worigem = " AND tbl_animal_codigo_origem IN(";
            $worigem.= $origem;
            $worigem.= ")";
        }
    }

    $wraca = '';

    if (isset($_GET['raca'])) {
        $raca_filtro = $_GET["raca"];

        $raca= array();
        $matriz_itens = explode(",", $raca_filtro);
        $quantidade_itens = count($matriz_itens);

        for($i=0; $i < $quantidade_itens; $i++) {
            $raca[$i]=$matriz_itens[$i];
        }

        $raca = implode(',', $raca);
        $raca = substr($raca,0, -1);

        if ($raca_filtro!='') {
            $wraca = " AND tbl_animal_codigo_raca IN(";
            $wraca.= $raca;
            $wraca.= ")";
        }
    }

    $wcategoria = '';

    if (isset($_GET['categoria'])) {
        $categoria_filtro = $_GET["categoria"];
        $categoria= array();
        $matriz_itens = explode(",", $categoria_filtro);
        $quantidade_categoria = count($matriz_itens);

        for($i=0; $i < $quantidade_categoria; $i++) {
            $categoria[$i]=$matriz_itens[$i];
        }

        $categoria = implode(',', $categoria);
        $categoria = substr($categoria,0, -1);
        $quantidade_categoria--;

        if ($categoria_filtro!='') {
            $wcategoria = explode(",", $categoria);
        }
    }

    $wpai = '';

    if (isset($_GET['pai'])) {
        $pai_filtro = $_GET["pai"];

        $pai= array();
        $matriz_itens = explode(",", $pai_filtro);
        $quantidade_itens = count($matriz_itens);

        for($i=0; $i < $quantidade_itens; $i++) {
            $pai[$i]=$matriz_itens[$i];
        }

        $pai = implode(',', $pai);
        $pai = substr($pai,0, -1);

        if ($pai_filtro!='') {
            $wpai = " AND tbl_animal_codigo_pai IN(";
            $wpai.= $pai;
            $wpai.= ")";
        }
    }

    $wmae = '';

    if (isset($_GET['mae'])) { 
        $mae_filtro = $_GET["mae"];
        $mae= array();
        $matriz_itens = explode(",", $mae_filtro);
        $quantidade_itens = count($matriz_itens);

        for($i=0; $i < $quantidade_itens; $i++) {
            $mae[$i]=$matriz_itens[$i];
        }

        $mae = implode(',', $mae);
        $mae = substr($mae,0, -1);

        if ($mae_filtro!='') {
            $wmae = " AND tbl_animal_codigo_mae IN(";
            $wmae.= $mae;
            $wmae.= ")";
        }
    }

    $peso_nasc_inicial = $_GET["peso_nasc_inicial"];
    $peso_nasc_final = $_GET["peso_nasc_final"];

    $peso_desmama_inicial = $_GET["peso_desmama_inicial"];
    $peso_desmama_final = $_GET["peso_desmama_final"];

    $peso_ult_inicial = $_GET["peso_ult_inicial"];
    $peso_ult_final = $_GET["peso_ult_final"];

    if ($peso_nasc_inicial=='' && $peso_nasc_final==''){
        $wpeso_nasc = '';
    }
    else {
        $wpeso_nasc = " AND tbl_animal_primeiro_peso >= '$peso_nasc_inicial' AND tbl_animal_primeiro_peso <= '$peso_nasc_final'";
    }

    if ($peso_desmama_inicial=='' && $peso_desmama_final==''){
        $wpeso_desmama = '';
    }
    else {
        $wpeso_desmama = " AND tbl_animal_peso_desmama >= '$peso_desmama_inicial' AND tbl_animal_peso_desmama <= '$peso_desmama_final'";
    }

    if ($peso_ult_inicial=='' && $peso_ult_final==''){
        $wpeso_ult = '';
    }
    else {
        $wpeso_ult = " AND tbl_animal_ultimo_peso >= '$peso_ult_inicial' AND tbl_animal_ultimo_peso <= '$peso_ult_final'";
    }

    $data_nasc_inicial = $_GET["data_nasc_inicial"];
    $data_nasc_final = $_GET["data_nasc_final"];

    if ($data_nasc_inicial=='' && $data_nasc_final==''){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
    }

    $wativo = $_GET['ativo'];

    $tipo_rel= $_GET["tipo_rel"];
    $descricao_filtro= $_GET["descricao_filtro"];

    $desc_tipo_rel = 'Geral';

    $filtro_solteiras = $_GET["solteiras"];
    $descarte = $_GET["descarte"];
    $filtro_paridas = $_GET["paridas"];
    $data_paridas_ate = $_GET["data_paridas"];
    $filtro_parto = $_GET["parto"];
    $num_parto_de = $_GET['num_parto_de'];
    $num_parto_ate = $_GET['num_parto_ate'];
    $filtro_aborto = $_GET["aborto"];
    $num_aborto_de = $_GET['num_aborto_de'];
    $num_aborto_ate = $_GET['num_aborto_ate'];
    $previsao_parto_de = $_GET['previsao_parto_de'];
    $previsao_parto_ate = $_GET['previsao_parto_ate'];
    $filtro_positivo = $_GET['positivo'];
    $filtro_negativo = $_GET['negativo'];

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


@ session_start(); 

$cnpj_empresa = $_SESSION['id_cliente'];

$empresa = mysqli_query($conector, "SELECT tbl_empresa_nome FROM tbl_empresa
                                     WHERE tbl_empresa_cpf_cnpj ='$cnpj_empresa'"); 
$registro_empresa = mysqli_fetch_object($empresa);  
$nome_empresa = utf8_encode($registro_empresa->tbl_empresa_nome);

$nome_relatorio = "Situação Reprodutiva - " . $desc_tipo_rel;

    $spreadsheet->getActiveSheet()->mergeCells('A1:Q1');
    $spreadsheet->getActiveSheet()->mergeCells('R1:S1');
    $spreadsheet->getActiveSheet()->mergeCells('B2:S2');
    $spreadsheet->getActiveSheet()->mergeCells('A3:S3');

    $spreadsheet->getActiveSheet()->mergeCells('A4:F4');
    $spreadsheet->getActiveSheet()->mergeCells('G4:L4');
    $spreadsheet->getActiveSheet()->mergeCells('M4:S4');

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', $nome_relatorio)
            ->setCellValue("O1", "Data: " . $data_sistema)
            ->setCellValue("A2", "Filtro: ")
            ->setCellValue("B2", $descricao_filtro);

    $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);


    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A4","Fêmeas")
            ->setCellValue("G4","Situação Atual")
            ->setCellValue("M4","Situação Reprodutiva");

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A5","Id Fêmea")
            ->setCellValue("B5","Raça")
            ->setCellValue("C5","Pelagem")
            ->setCellValue("D5","Nascimento")
            ->setCellValue("E5","Pai")
            ->setCellValue("F5","Mãe")
            ->setCellValue("G5","Nº Partos")
            ->setCellValue("H5","Aborto")
            ->setCellValue("I5","Natimorto")
            ->setCellValue("J5","Último Parto")
            ->setCellValue("K5","Último Bezerro Vivo")
            ->setCellValue("L5","Pai Semen Embrião")
            ->setCellValue("M5","Última Estação de Monta")
            ->setCellValue("N5","Nº Coberturas")
            ->setCellValue("O5","Diagnóstico")
            ->setCellValue("P5","Pai Semen Embrião")
            ->setCellValue("Q5","Previsão Parto")
            ->setCellValue("R5","Data Aptidão")
            ->setCellValue("S5","Descarte");

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(16);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(9);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(9);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(9);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(9);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(9);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(16);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(16);
    $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(9);

    $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('R1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('A2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('B2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('A4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('G4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('M4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A5:S5')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A5:S5')->getAlignment()->setVertical($align);

    $spreadsheet->getActiveSheet()->getStyle('G5:H5')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('J5:L5')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('M5:S5')->getAlignment()->setWrapText(true);

    $spreadsheet->getActiveSheet()->getStyle('A4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A4')->getFill()->getStartColor()->setARGB('F2F2F2');

    $spreadsheet->getActiveSheet()->getStyle('G4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('G4')->getFill()->getStartColor()->setARGB('D9D9D9');

    $spreadsheet->getActiveSheet()->getStyle('M4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('M4')->getFill()->getStartColor()->setARGB('BFBFBF');

    $linha=5;

   /*$sql = "SELECT * from tbl_animais 
        WHERE tbl_animal_codigo_numerico=9105 or tbl_animal_codigo_numerico=6138
         ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC";*/ 

    $sql = "SELECT * from tbl_animais 
        WHERE tbl_animal_lixeira=0 AND 
              tbl_animal_ativo='$wativo' AND 
              tbl_animal_sexo='F'" . $wlocal . $worigem . $wraca . $wpai . 
              $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .
        " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC";

    $rs = mysqli_query($conector, $sql); 
    $num_rows_animais = mysqli_num_rows($rs);

    $animais_listados = 0;
    $ultimo_parto = '0000-00-00';
    $data_previsao_servico = '0000-00-00';
    $data_previsao_parto = '0000-00-00';

    if ($num_rows_animais!=0){
        while ($reg_animal = mysqli_fetch_object($rs)){
            $codigo = $reg_animal->tbl_animal_codigo_id;
            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
            $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
            $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
            $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
            $mae = $reg_animal->tbl_animal_codigo_mae; 
            $pai = $reg_animal->tbl_animal_codigo_pai; 
            $ativo = $reg_animal->tbl_animal_ativo; 
            $animal_descarte = $reg_animal->tbl_animal_descarte_reproducao;

            if ($animal_descarte=='S') {
                $animal_descarte = 'Sim';
            }
            else {
                $animal_descarte = '';   
            }

            $tem_negativo = '';
            $tem_positivo = '';
            $vaca_descarte = '';
            $nascido = '';
            $data_aborto_natimorto = '0000-00-00';

            if ($descarte=='S') {
                if ($animal_descarte=='Sim') {
                    $vaca_descarte = 'S';
                }
            }

            // verifica a cobertura do animal

            $sql = "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      (tbl_cobertura_controle = 'C' OR  
                       tbl_cobertura_controle = 'M')
                ORDER BY tbl_cobertura_incluido_em DESC limit 1"; 

            $tbl_item_cobertura = mysqli_query($conector, $sql);

            $num_rows = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
                $controle = $reg_cobertura->tbl_cobertura_controle;
                $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
                
                if ($controle == 'C') {
                    $tbl_estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
                        WHERE tbl_par_estacao_id ='$estacao_animal'");

                    $num_rows_estacao = mysqli_num_rows($tbl_estacao);

                    if ($num_rows_estacao!=0) {
                        $reg_estacao = mysqli_fetch_object($tbl_estacao);
                        $desc_estacao_monta = $reg_estacao->tbl_par_estacao_nome;
                    }
                    else {
                        $estacao_animal = 0;
                        $desc_estacao_monta = 'Desconhecida';                    
                    }
                }
                else {
                    $estacao_animal = 0;
                    $desc_estacao_monta = 'Monta';                    
                }
            }
            else {
                $estacao_animal = 0;
                $desc_estacao_monta = '';
            }

            // verifica numero de coberturas na estacao

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      tbl_cobertura_controle = 'C' AND 
                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'"); 

            $num_coberturas = mysqli_num_rows($tbl_item_cobertura);

            if ($num_coberturas==0) {
                $num_coberturas = '';
            } 

            $data_nasc = new DateTime($reg_animal->tbl_animal_data_nascimento);
            $data_nasc_edi = $data_nasc->format('d/m/Y');
            $data_nasc_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_nasc_edi);
            $peso_nasc = $reg_animal->tbl_animal_primeiro_peso; 
            $peso_nasc_edi = number_format($peso_nasc,2,',','.');
            $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
            $peso_desmama_edi = number_format($peso_desmama,2,',','.');
            $peso_ultimo = $reg_animal->tbl_animal_ultimo_peso; 
            $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
            $data_ultimo = new DateTime($reg_animal->tbl_animal_data_ultimo);
            $data_ultimo_edi = $data_ultimo->format('d/m/Y');
            $observacao = ltrim($reg_animal->tbl_animal_observacao); 
            $observacao = rtrim($observacao); 

            if ($codigo_alfa=='') {
                $codigo_edi = intval($codigo_numerico);
            }
            else {
                $codigo_edi = $codigo_alfa.'-'.intval($codigo_numerico);
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
                if ($reg->tbl_animal_codigo_alfa==''){
                    $descricao_mae = intval($reg->tbl_animal_codigo_numerico);
                }
                else {
                    $descricao_mae = $reg->tbl_animal_codigo_alfa.'-'. intval($reg->tbl_animal_codigo_numerico);
                }
            }
            else {
                $descricao_mae = '';
            }

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
            $num_rows = mysqli_num_rows($tab_pai);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pai);
                $descricao_pai = utf8_encode($reg->tbl_semem_nome);
                $pai = $reg->tbl_semem_codigo_id;
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                $num_rows = mysqli_num_rows($tab_pai);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    if ($reg->tbl_animal_codigo_alfa==''){
                        $descricao_pai = $reg->tbl_animal_codigo_numerico;
                    }
                    else {
                        $descricao_pai = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                    }
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

            $tab_pelagem = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem ='$codigo_pelagem'");
            $num_rows = mysqli_num_rows($tab_pelagem);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pelagem);
                $descricao_pelagem = utf8_encode($reg->tab_descricao_pelagem);
            }
            else {
                $descricao_pelagem = '';
            }

            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
            $data_baixa = $reg_animal->tbl_animal_baixado_em;

            if ($data_baixa!='') {
                $data_acompanhamento_calculo = date($data_baixa);
            }
            else {
                $data_acompanhamento_calculo = date("Y-m-d");
            }

            //$data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); 
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade_animal = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($categoria);    

            if ($num_rows!=0) {
                while ($reg_categoria = mysqli_fetch_object($categoria)) {
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade_animal >= $idade_de && 
                        $idade_animal <= $idade_ate) {
                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                    }
                }
            }                   

            // verifica vacas solteiras
            if ($filtro_solteiras=='S' || $filtro_paridas=='S') {

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'
                    ORDER BY tbl_animal_data_nascimento DESC limit 1");

                $ultimo_filho = mysqli_num_rows($tbl_filhos);

                if ($ultimo_filho!=0) {
                    $reg_filhos = mysqli_fetch_object($tbl_filhos);
                    $bezzero_ativo = $reg_filhos->tbl_animal_ativo;
                    $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento; 
                    $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                    $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                    if ($codigo_alfa_filho=='') {
                        $codigo_edi_filho = intval($codigo_numerico_filho);
                    }
                    else {
                        $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                    }

                    $data_ref = date("Y-m-d");
                    $diferenca = strtotime($data_ref) - strtotime($ultimo_parto);
                    $dias_nascimento_bezerro = floor($diferenca / (60 * 60 * 24));

                    $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai_ult_filho = utf8_encode($reg->tbl_semem_nome);
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai_ult_filho'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                    $descricao_pai_ult_filho = $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_pai_ult_filho = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_pai_ult_filho = '';
                        }
                    }

                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($ultimo_parto); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade_ano = $idade_acompanhamento->format('%Y');
                    $idade_mes = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    if ($idade < 8) {
                        if ($bezzero_ativo=='S') {
                            $vaca_parida = 'S';
                            $vaca_solteira = '';
                        }
                        else {
                            if ($dias_nascimento_bezerro<=35) {
                                $vaca_parida = 'S';
                                $vaca_solteira = '';
                            }
                            else {
                                $vaca_parida = '';
                                $vaca_solteira = 'S';
                            }
                        }
                    }
                    else {
                        $vaca_solteira = 'S';
                        $vaca_parida = '';
                    }
                }
                else {
                    $ultimo_parto = '0000-00-00';
                    $codigo_edi_filho = '';
                    $vaca_solteira = 'S';
                    $vaca_parida = '';
                    $descricao_pai_ult_filho = '';
                    $bezzero_ativo='';
                    $dias_nascimento_bezerro='';
                }

                if ($ultimo_parto=='0000-00-00') {
                    $ultimo_parto_edi = '';
                }
                else {
                    $data = new DateTime($ultimo_parto);
                    $ultimo_parto_edi = $data->format('d/m/Y');
                    $ultimo_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ultimo_parto_edi);
                }

                // VERIFICA SE A VACA ESTA PRENHE

                $tbl_prenhe = mysqli_query($conector, "SELECT * FROM tbl_cobertura
                    INNER JOIN tbl_item_cobertura 
                            ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                    WHERE tbl_cobertura_lixeira=0 AND 
                          tbl_ite_cobertura_codigo_id_animal='$codigo' AND  
                          tbl_ite_cobertura_resultado_diagnostico='P' AND  
                          (tbl_ite_cobertura_nascido='' OR 
                           tbl_ite_cobertura_nascido IS NULL)");

                $num_rows_prenhe = mysqli_num_rows($tbl_prenhe);

                if ($num_rows_prenhe!=0) {
                    //print_r('Vaca prenhe: ' . $codigo_edi . '</br>');
                    $vaca_solteira = '';
                }
            }
            else {
                $ultimo_parto_edi = '';
                $codigo_edi_filho = '';
                $descricao_pai_ult_filho = '';
                $bezzero_ativo='';
                $dias_nascimento_bezerro='';

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'
                    ORDER BY tbl_animal_data_nascimento DESC limit 1");

                $ultimo_filho = mysqli_num_rows($tbl_filhos);

                if ($ultimo_filho!=0) {
                    $reg_filhos = mysqli_fetch_object($tbl_filhos);
                    $bezzero_ativo = $reg_filhos->tbl_animal_ativo;
                    $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento;
                    $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                    $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                    if ($codigo_alfa_filho=='') {
                        $codigo_edi_filho = intval($codigo_numerico_filho);
                    }
                    else {
                        $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                    }

                    $data_ref = date("Y-m-d");
                    $diferenca = strtotime($data_ref) - strtotime($ultimo_parto);
                    $dias_nascimento_bezerro = floor($diferenca / (60 * 60 * 24));

                    $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai_ult_filho = utf8_encode($reg->tbl_semem_nome);
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai_ult_filho'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                $descricao_pai_ult_filho = $reg->tbl_animal_codigo_numerico;
                            }
                        else {
                            $descricao_pai_ult_filho = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_pai_ult_filho = '';
                        }
                    }
                } 
                else {
                    $ultimo_parto = '0000-00-00';
                }
                           
                if ($ultimo_parto=='0000-00-00') {
                    $ultimo_parto_edi = '';
                }
                else {
                    $data = new DateTime($ultimo_parto);
                    $ultimo_parto_edi = $data->format('d/m/Y');
                    $ultimo_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ultimo_parto_edi);
                }
            }

            // verifica partos
            if ($num_parto_de!='' && $num_parto_ate!='') {

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'");

                $num_partos = mysqli_num_rows($tbl_filhos);

                // verifica parto natimorto

                $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                        where tbl_mov_estoque_codigo_mae='$codigo' and 
                              tbl_mov_estoque_codigo_id_animal=999999999 and 
                              tbl_mov_estoque_entrada_saida='E' and 
                              tbl_mov_estoque_tipo_movimentacao='N'");
                $num_natimorto = mysqli_num_rows($tbl_natimorto);

                $num_partos = $num_partos + $num_natimorto;

                if ($num_partos>=$num_parto_de && 
                    $num_partos<=$num_parto_ate && $idade_animal>=8) {
                    $tem_parto = "S";
                }
                else {
                    $tem_parto = "";
                }
            }
            else {
                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'");

                $num_partos = mysqli_num_rows($tbl_filhos);

                // verifica parto natimorto

                $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                    where tbl_mov_estoque_codigo_mae='$codigo' and 
                          tbl_mov_estoque_codigo_id_animal=999999999 and 
                          tbl_mov_estoque_entrada_saida='E' and 
                          tbl_mov_estoque_tipo_movimentacao='N'");
                $num_natimorto = mysqli_num_rows($tbl_natimorto);

                $num_partos = $num_partos + $num_natimorto;
            }

            // verifica se tem abortos ou natimortos
            if ($num_aborto_de!='' && $num_aborto_ate!='') {
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

            // agora verifica o numero de natimortos
            $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='S' AND 
                      tbl_mov_estoque_tipo_movimentacao='M'
                ORDER BY tbl_mov_estoque_nascimento DESC");

            $num_natimorto = mysqli_num_rows($tbl_natimorto);

            if ($num_natimorto==0) {
                $num_natimorto = '';
                $data_natimorto='0000-00-00';
            }
            else {
                $reg_natimorto = mysqli_fetch_object($tbl_natimorto);
                $data_natimorto=$reg_natimorto->tbl_mov_estoque_nascimento;
            }

            // agora verifica o numero de abortos
            $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='A' AND 
                      (tbl_mov_estoque_tipo_movimentacao='A' OR
                       tbl_mov_estoque_tipo_movimentacao='B')
                ORDER BY tbl_mov_estoque_nascimento DESC");

            $num_aborto = mysqli_num_rows($tbl_aborto);

            if ($num_aborto==0) {
                $num_aborto = '';
                $data_aborto='0000-00-00';
            }
            else {
                $reg_aborto = mysqli_fetch_object($tbl_aborto);
                $data_aborto=$reg_aborto->tbl_mov_estoque_nascimento;
            }

            //print_r('Aborto ' . $data_aborto . '</br>');

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

            // Se tem natimorto e a data é maior o ultimo parto considera como ultimo parto
            if ($data_aborto_natimorto>$ultimo_parto) {
                $data = new DateTime($data_aborto_natimorto);
                $ultimo_parto_edi = $data->format('d/m/Y');
                $ultimo_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ultimo_parto_edi);                
                $natimorto = 'S';
            }
            else {
                $natimorto = 'N';
            }

            // verifica previsao de parto
            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      (tbl_cobertura_controle = 'C' OR
                      tbl_cobertura_controle = 'M') AND 
                      tbl_ite_cobertura_resultado_diagnostico = 'P' 
                ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

            $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_coberturas!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                $cobertura_id = $reg_cobertura->tbl_cobertura_id;
                $controle = $reg_cobertura->tbl_cobertura_controle;
                $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;

                if ($controle=='C') {
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
                    $data_previsao_parto = $reg_cobertura->tbl_ite_cobertura_previsao_parto;
                }
            }
            else {
                $data_previsao_parto = '';
            }
        
            if ($data_previsao_parto=='' || $data_previsao_parto=='0000-00-00') {
                $data_previsao_parto = '0000-00-00';
                $previsao_parto_edi = '';
            }
            else {
                $data = new DateTime($data_previsao_parto);
                $previsao_parto_edi = $data->format('d/m/Y');
                $previsao_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($previsao_parto_edi);                 
            }

            // calcula data da aptidão

            $data_aptidao_edi = '';
            
            if ($ultimo_parto!='0000-00-00') {
                $data_aptidao_edi = date("d/m/Y", strtotime($ultimo_parto . "+ 35 days"));
                //print_r('Aptidao pelo ultimo parto ' . $data_aptidao_edi . '</br>');
            }

            if ($data_aborto_natimorto!='0000-00-00' && $data_aborto_natimorto>$ultimo_parto) {
                $data_aptidao_edi = date("d/m/Y", strtotime($data_aborto_natimorto . "+ 35 days"));
            }

            if ($data_aptidao_edi!='') {
                $data_aptidao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_aptidao_edi);    
            }

            // Verifica diagnostico
            if ($filtro_positivo=='S' || $filtro_negativo=='S'){

                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira=0 AND
                          tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                         (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M')
                    ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

                $num_rows = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                    $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_semen = $reg->tbl_semem_nome;
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$semen'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                $descricao_semen = $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_semen = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_semen = '';
                        }
                    }

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
                $diagnostico = '';
                $descricao_semen = '';

                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira=0 AND 
                          tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M')
                    ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

                $num_rows = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                    $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_semen = $reg->tbl_semem_nome;
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$semen'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                $descricao_semen = $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_semen = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_semen = '';
                        }
                    }
                }
            }

            // verifica natimortos, nascidos ou abortos na estacao

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      ((tbl_cobertura_controle = 'C' AND  
                        tbl_cobertura_codigo_estacao_monta ='$estacao_animal') OR 
                       (tbl_cobertura_controle = 'M')) AND 
                      tbl_ite_cobertura_resultado_diagnostico = 'P' 
                ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

            $num_rows_item = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_item!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $nascido_aborto = $reg_cobertura->tbl_ite_cobertura_nascido;
            }
            else {
                $nascido_aborto = '';
            }

            if ($filtro_positivo=='S' AND 
                $nascido_aborto!='') {
                $tem_positivo='';
            }

            if ($data_previsao_parto!='0000-00-00' AND 
                $nascido_aborto!='') {
                $data_previsao_parto='0000-00-00';
            }

            if ($num_partos==0 && $num_aborto=='' && $num_natimorto=='' && $num_coberturas=='') {
                $vaca_solteira = '';
            }

            if ($wcategoria=="" && 
                $descarte==$vaca_descarte && 
                $data_previsao_parto>=$previsao_parto_de &&  
                $data_previsao_parto<=$previsao_parto_ate && 

                (($filtro_solteiras==$vaca_solteira && 
                 ($previsao_parto_edi=='' || 
                 ($nascido=='N' || $nascido=='A' || 
                 $nascido=='M' || $nascido=='O'))) ||

                 ($filtro_paridas==$vaca_parida && 
                 $ultimo_parto>=$data_paridas_de && 
                 $ultimo_parto<=$data_paridas_ate)) &&

                $filtro_parto==$tem_parto &&
                $filtro_aborto==$tem_aborto && 
                $filtro_positivo==$tem_positivo &&
                $filtro_negativo==$tem_negativo 
                ) {

                    if ($diagnostico=='N') {
                        $previsao_parto_edi='';
                        $desc_diagnostico = 'Negativo';
                    }
                    else if ($diagnostico=='P') {
                        switch ($nascido) {
                            case 'N':
                                $desc_diagnostico = 'Nascido';
                                break;
                            case 'A':
                                $desc_diagnostico = 'Aborto';
                                break;
                            case 'M':
                                $desc_diagnostico = 'Natimorto';
                                break;
                            case 'S':
                                $desc_diagnostico = 'Outro';
                                break;
                            default:
                                $desc_diagnostico = 'Positivo';
                                break;       
                        }
                    }
                    else {
                        $desc_diagnostico = '';
                    }

                                    $linha++;
                                    $celulas = 'A'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                                    $celulas = 'E'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                                    $celulas = 'F'.$linha.':H'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                                    $celulas = 'I'.$linha.':N'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                                    $celulas = 'L'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                                    $celulas = 'O'.$linha.':S'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                                    $celulas = 'P'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                                    $spreadsheet->getActiveSheet()->getStyle('S'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));

                                    if ($nascido=='N' || $nascido=='A' || 
                                        $nascido=='M' || $nascido=='O') {
                                        $spreadsheet->getActiveSheet()->getStyle('Q'.$linha)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                    }
                                    else {
                                        $data_aptidao_edi = '';
                                    }

                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $descricao_raca);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $descricao_pelagem);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_nasc_edi);

                                    $celulas = 'D'.$linha;
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY'); 

                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_pai);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_mae);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $num_partos);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $num_aborto);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $num_natimorto);

if ($natimorto=='S') {
    $celulas = 'J'.$linha;
    $spreadsheet->getActiveSheet()->getComment($celulas)->setAuthor('');
    $commentRichText = $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Mensagem');
    $commentRichText->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun("\r\n");
    $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Natimorto ou Aborto.');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setWidth('100pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setHeight('50pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setMarginLeft('100pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->getFillColor()->setRGB('EEEEEE');
}
else if ($bezzero_ativo=='N' && 
        ($dias_nascimento_bezerro>0 && 
         $dias_nascimento_bezerro<=35)) {
    $celulas = 'J'.$linha;
    $spreadsheet->getActiveSheet()->getComment($celulas)->setAuthor('');
    $commentRichText = $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Mensagem');
    $commentRichText->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun("\r\n");
    $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Morreu <=35 dias.');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setWidth('100pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setHeight('50pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setMarginLeft('100pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->getFillColor()->setRGB('EEEEEE');

}
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $ultimo_parto_edi);

                                    if ($ultimo_parto_edi!='') {
                                        $celulas = 'J'.$linha;
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY'); 
                                    }

                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $codigo_edi_filho);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_pai_ult_filho);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $desc_estacao_monta);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $num_coberturas);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $desc_diagnostico);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(16, $linha, $descricao_semen);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(17, $linha, $previsao_parto_edi);

                                    if ($previsao_parto_edi!='') {
                                        $celulas = 'Q'.$linha;
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                                    }
                                    
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(18, $linha, $data_aptidao_edi);

                                    if ($data_aptidao_edi!='') {
                                        $celulas = 'R'.$linha;
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                                    }

                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(19, $linha, $animal_descarte);

                                    $animais_listados++;
                                }
                                else {
                                    for ($k=0; $k < $quantidade_categoria; $k++) { 
                                        $value = $wcategoria[$k];
                                        if ($value==$codigo_categoria &&
                                            $descarte==$vaca_descarte && 
                                            $data_previsao_parto>=$previsao_parto_de && 
                                            $data_previsao_parto<=$previsao_parto_ate && 

                                            (($filtro_solteiras==$vaca_solteira && ($previsao_parto_edi=='' || ($nascido=='N' || $nascido=='A' || 
                                            $nascido=='M' || $nascido=='O'))) ||
                                            ($filtro_paridas==$vaca_parida && 
                                            $ultimo_parto>=$data_paridas_de && 
                                            $ultimo_parto<=$data_paridas_ate)) &&

                                            $filtro_parto==$tem_parto &&
                                            $filtro_aborto==$tem_aborto && 
                                            $filtro_positivo==$tem_positivo && 
                                            $filtro_negativo==$tem_negativo  
                                        ) {

                                            if ($diagnostico=='N') {
                                                $previsao_parto_edi='';
                                                $desc_diagnostico = 'Negativo';
                                            }
                                            else if ($diagnostico=='P') {
                                                switch ($nascido) {
                                                    case 'N':
                                                        $desc_diagnostico = 'Nascido';
                                                        break;
                                                    case 'A':
                                                        $desc_diagnostico = 'Aborto';
                                                        break;
                                                    case 'M':
                                                        $desc_diagnostico = 'Natimorto';
                                                        break;
                                                    case 'S':
                                                        $desc_diagnostico = 'Outro';
                                                        break;
                                                    default:
                                                        $desc_diagnostico = 'Positivo';
                                                        break;       
                                                }
                                            }
                                            else {
                                                $desc_diagnostico = '';
                                            }

                                            $linha++;
                                            $celulas = 'A'.$linha;
                                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                                            $celulas = 'E'.$linha;
                                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                                            $celulas = 'I'.$linha.':H'.$linha;
                                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                                            $celulas = 'K'.$linha.':N'.$linha;
                                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                                            $celulas = 'O'.$linha.':S'.$linha;
                                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                                            $spreadsheet->getActiveSheet()->getStyle('S'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));

                                            if ($nascido=='N' || $nascido=='A' || 
                                                $nascido=='M' || $nascido=='O') {
                                                $spreadsheet->getActiveSheet()->getStyle('Q'.$linha)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                            }
                                            else {
                                                $data_aptidao_edi = '';
                                            }

                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $descricao_raca);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $descricao_pelagem);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_nasc_edi);

                                            $celulas = 'D'.$linha;
                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY'); 

                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_pai);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_mae);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $num_partos);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $num_aborto);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $num_natimorto);
if ($natimorto=='S') {
    $celulas = 'J'.$linha;
    $spreadsheet->getActiveSheet()->getComment($celulas)->setAuthor('');
    $commentRichText = $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Mensagem');
    $commentRichText->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun("\r\n");
    $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun(' Natimorto ou Aborto.');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setWidth('100pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setHeight('50pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setMarginLeft('100pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->getFillColor()->setRGB('EEEEEE');
}
else if ($bezzero_ativo=='N' && 
        ($dias_nascimento_bezerro>0 && 
         $dias_nascimento_bezerro<=35)) {
    $celulas = 'J'.$linha;
    $spreadsheet->getActiveSheet()->getComment($celulas)->setAuthor('');
    $commentRichText = $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Mensagem');
    $commentRichText->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun("\r\n");
    $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Morreu <=35 dias.');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setWidth('100pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setHeight('50pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->setMarginLeft('100pt');
    $spreadsheet->getActiveSheet()->getComment($celulas)->getFillColor()->setRGB('EEEEEE');

}

                                            
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $ultimo_parto_edi);

                                            if ($ultimo_parto_edi!='') {
                                                $celulas = 'J'.$linha;
                                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                                            }
                                            
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $codigo_edi_filho);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_pai_ult_filho);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $desc_estacao_monta);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $num_coberturas);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $desc_diagnostico);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(16, $linha, $descricao_semen);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(17, $linha, $previsao_parto_edi);

                                            if ($previsao_parto_edi!='') {
                                                $celulas = 'Q'.$linha;
                                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                                            }
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(18, $linha, $data_aptidao_edi);

                                            if ($data_aptidao_edi!='') {
                                                $celulas = 'R'.$linha;
                                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                                            }

                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(19, $linha, $animal_descarte);
                 
                                            $animais_listados++;
                                        }
                                    }
                                }
                        }
                    }

    $spreadsheet->getActiveSheet()->getStyle('A4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 4, 'Fêmeas: ' . $animais_listados);

// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');


header('Content-Disposition: attachment;filename="situacao_reprodutiva.xlsx"');
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

?>