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
$banco = $cnpj_cliente;
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

    $local_filtro = $_REQUEST["local"];
    $origem_filtro = $_REQUEST["origem"];
    $raca_filtro = $_REQUEST["raca"];
    $categoria_filtro = $_REQUEST["categoria"];
    $pai_filtro = $_REQUEST["pai"];
    $mae_filtro = $_REQUEST["mae"];
    $codigo_alfa = $_REQUEST["codigo_alfa"];
    $codigo_numerico = $_REQUEST["codigo_numerico"];

    if (isset($_REQUEST['estacao'])) {
        $estacao_filtro = $_REQUEST['estacao'];

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

        $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(";
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

    $origem= array();
    $matriz_itens = explode(",", $origem_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $origem[$i]=$matriz_itens[$i];
    }

    $origem = implode(',', $origem);
    $origem = substr($origem,0, -1);

    $worigem = '';

    if ($origem_filtro!='') {
        $worigem = " AND tbl_animal_codigo_origem IN(";
        $worigem.= $origem;
        $worigem.= ")";
    }

    $raca= array();
    $matriz_itens = explode(",", $raca_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $raca[$i]=$matriz_itens[$i];
    }

    $raca = implode(',', $raca);
    $raca = substr($raca,0, -1);

    $wraca = '';

    if ($raca_filtro!='') {
        $wraca = " AND tbl_animal_codigo_raca IN(";
        $wraca.= $raca;
        $wraca.= ")";
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

    $pai= array();
    $matriz_itens = explode(",", $pai_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $pai[$i]=$matriz_itens[$i];
    }

    $pai = implode(',', $pai);
    $pai = substr($pai,0, -1);

    $wpai = '';

    if ($pai_filtro!='') {
        $wpai = " AND tbl_animal_codigo_pai IN(";
        $wpai.= $pai;
        $wpai.= ")";
    }

    $mae= array();
    $matriz_itens = explode(",", $mae_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $mae[$i]=$matriz_itens[$i];
    }

    $mae = implode(',', $mae);
    $mae = substr($mae,0, -1);

    $wmae = '';

    if ($mae_filtro!='') {
        $wmae = " AND tbl_animal_codigo_mae IN(";
        $wmae.= $mae;
        $wmae.= ")";
    }

    $peso_nasc_inicial = $_REQUEST["peso_nasc_inicial"];
    $peso_nasc_final = $_REQUEST["peso_nasc_final"];

    $peso_desmama_inicial = $_REQUEST["peso_desmama_inicial"];
    $peso_desmama_final = $_REQUEST["peso_desmama_final"];

    $peso_ult_inicial = $_REQUEST["peso_ult_inicial"];
    $peso_ult_final = $_REQUEST["peso_ult_final"];

    if ($peso_nasc_inicial==0 && $peso_nasc_final==0){
        $wpeso_nasc = '';
    }
    else {
        $wpeso_nasc = " AND tbl_animal_primeiro_peso >= '$peso_nasc_inicial' AND tbl_animal_primeiro_peso <= '$peso_nasc_final'";
    }

    if ($peso_desmama_inicial==0 && $peso_desmama_final==0){
        $wpeso_desmama = '';
    }
    else {
        $wpeso_desmama = " AND tbl_animal_peso_desmama >= '$peso_desmama_inicial' AND tbl_animal_peso_desmama <= '$peso_desmama_final'";
    }

    if ($peso_ult_inicial==0 && $peso_ult_final==0){
        $wpeso_ult = '';
    }
    else {
        $wpeso_ult = " AND tbl_animal_ultimo_peso >= '$peso_ult_inicial' AND tbl_animal_ultimo_peso <= '$peso_ult_final'";
    }

    $data_nasc_inicial = $_REQUEST["data_nasc_inicial"];
    $data_nasc_final = $_REQUEST["data_nasc_final"];

    if ($data_nasc_inicial==0 && $data_nasc_final==0){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
    }

    $wativo = $_REQUEST['ativo'];

    $tipo_rel= $_REQUEST["tipo_rel"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

    if ($tipo_rel == "I") {
        $desc_tipo_rel = 'Individual';
    }
    else {
        $desc_tipo_rel = 'Geral';
    }

    $filtro_solteiras = $_REQUEST["solteiras"];
    $descarte = $_REQUEST["descarte"];
    $filtro_paridas = $_REQUEST["paridas"];
    $data_paridas_ate = $_REQUEST["data_paridas"];
    $filtro_parto = $_REQUEST["parto"];
    $num_parto_de = $_REQUEST['num_parto_de'];
    $num_parto_ate = $_REQUEST['num_parto_ate'];
    $filtro_aborto = $_REQUEST["aborto"];
    $num_aborto_de = $_REQUEST['num_aborto_de'];
    $num_aborto_ate = $_REQUEST['num_aborto_ate'];
    $previsao_parto_de = $_REQUEST['previsao_parto_de'];
    $previsao_parto_ate = $_REQUEST['previsao_parto_ate'];
    $filtro_positivo = $_REQUEST['positivo'];
    $filtro_negativo = $_REQUEST['negativo'];

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

    if ($tipo_rel=='I') { // lista individual

        if ($codigo_alfa=='') {
            $codigo_consulta = $codigo_numerico;            
        }
        else {
            $codigo_consulta = $codigo_alfa . '-' . $codigo_numerico;
        }

        $mensagem = '';

        $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
            INNER JOIN tbl_pessoa
                    ON tbl_pessoa_id = tbl_animal_codigo_fazenda
            WHERE tbl_animal_codigo_alfa='$codigo_alfa' AND 
                  tbl_animal_codigo_numerico='$codigo_numerico' AND 
                  tbl_animal_sexo='F'"); 

        $num_rows_animais = mysqli_num_rows($tbl_animais);

        if ($num_rows_animais!=0) {
            $reg_animal = mysqli_fetch_object($tbl_animais);
            $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
            $ativo = $reg_animal->tbl_animal_ativo;
            $animal_situacao = $reg_animal->tbl_animal_situacao;
            $descarte = $reg_animal->tbl_animal_descarte_reproducao;
            $descarte_em = new DateTime($reg_animal->tbl_animal_descarte_em);
            $descarte_em_edi = $descarte_em->format('d/m/Y H:i:s');
            $descarte_por = 'Descartado por ' . $reg_animal->tbl_animal_descarte_por .' em '. $descarte_em_edi;
            $nome_pessoa = $reg_animal->tbl_pessoa_nome; 
            $descricao_filtro = $nome_pessoa;                        
            $num_coberturas =$reg_animal->tbl_animal_numero_coberturas;
            $num_abortos = $reg_animal->tbl_animal_numero_abortos;
            
            if ($ativo=='N') {
                $ativo = 'Não';
            }
            else {
                $ativo = 'Sim';
            }

            switch ($animal_situacao) {
            case 'T':
                $animal_situacao='Aguardando Transferência';
                break;
            case 'V':
                $animal_situacao='Vendido';
                break;
            case 'M':   
                $animal_situacao='Morte';
                break;
            case 'S':   
                $animal_situacao='Outra Saída';
                break;
            } 

            if ($reg_animal->tbl_animal_em_estacao_monta=='S') {
                $em_estacao_monta ='SIM';
            }
            else {
                $em_estacao_monta ='NÃO';
            }

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * from tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                INNER JOIN tbl_parametro_estacao_monta
                        ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
                WHERE tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' AND 
                      tbl_cobertura_controle = 'C' AND 
                      tbl_cobertura_lixeira = 0
                ORDER BY tbl_ite_cobertura_numero_id DESC LIMIT 1"); 

            $num_rows_itens = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_itens!=0) {
                $reg_item = mysqli_fetch_object($tbl_item_cobertura);
                $id_estacao = $reg_item->tbl_cobertura_codigo_estacao_monta;
                $estacao_monta = $reg_item->tbl_par_estacao_nome;
            }
            else {
                $estacao_monta = '';
                $id_estacao = 0;
            }

            // primeiro verifica quantos partos
            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo_animal_id'");

            $num_partos = mysqli_num_rows($tbl_filhos);

            $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                where tbl_mov_estoque_codigo_mae='$codigo_animal_id' and 
                      tbl_mov_estoque_codigo_id_animal=999999999 and 
                      tbl_mov_estoque_entrada_saida='E' and 
                      tbl_mov_estoque_tipo_movimentacao='N'");
            $num_natimorto = mysqli_num_rows($tbl_natimorto);
            $num_partos = $num_partos + $num_natimorto;

            // agora verifica qual o ultimo parto para saber a idade
            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo_animal_id'
                ORDER BY tbl_animal_data_nascimento DESC limit 1");

            $ultimo_filho = mysqli_num_rows($tbl_filhos);

            $parida = '';
            $solteira = '';
            $situacao = '';

            if ($ultimo_filho!=0) {
                $reg_filhos = mysqli_fetch_object($tbl_filhos);
                $nascimento_filho = $reg_filhos->tbl_animal_data_nascimento;  
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($nascimento_filho); // Data de Nascimento
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade_ano = $idade_acompanhamento->format('%Y');
                $idade_mes = $idade_acompanhamento->format('%m');
                $idade_filho = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                if ($idade_filho < 8) {
                    $parida = 'S';
                    $situacao = 'Parida';
                }
                else {
                    $solteira = 'S';
                    $situacao = 'Solteira';
                }
            }
        }
        else {
            $mensagem = ' - Registro não encontrado';
        }
    }

@ session_start(); 

$cnpj_empresa = $_SESSION['id_cliente'];

$empresa = mysqli_query($conector, "SELECT tbl_empresa_nome FROM tbl_empresa
                                     WHERE tbl_empresa_cpf_cnpj ='$cnpj_empresa'"); 
$registro_empresa = mysqli_fetch_object($empresa);  
$nome_empresa = utf8_encode($registro_empresa->tbl_empresa_nome);

$nome_relatorio = "Situação Reprodutiva - " . $desc_tipo_rel;

if ($tipo_rel=='I') { // lista individual
    $spreadsheet->getActiveSheet()->mergeCells('A1:F1');
    $spreadsheet->getActiveSheet()->mergeCells('B2:G2');
    $spreadsheet->getActiveSheet()->mergeCells('C3:G3');
    //$spreadsheet->getActiveSheet()->mergeCells('A4:G4');
    $spreadsheet->getActiveSheet()->mergeCells('A5:B5');
    $spreadsheet->getActiveSheet()->mergeCells('C5:E5');

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', $nome_relatorio)
            ->setCellValue("G1", "Data: " . $data_sistema)
            ->setCellValue("A2", "Fazenda:")
            ->setCellValue("B2", $descricao_filtro)
            ->setCellValue("A3", "Fêmea:")
            ->setCellValue("B3", $codigo_consulta . ' - ' . $situacao);

    if ($descarte == 'S') {
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("C3", $descarte_por);
    }

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A4", "Animal Ativo:")
            ->setCellValue("B4", $ativo);

    if ($ativo == 'Não') {
        $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("C4", "Situação:")
                ->setCellValue("D4", $animal_situacao);
    }

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A5', "Estação de Monta: " . $estacao_monta)
            ->setCellValue("C5", "Nº Coberturas: " . $num_coberturas);

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(16);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(13);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(24);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(16);

    $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('G1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('A2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('B2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('A3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('B3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('A4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('C4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('A5') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('C5') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $spreadsheet->getActiveSheet()->getStyle('A5:E5')->getFont()->setBold(true);

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

    /*$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A5:P5')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A5:P5')->getAlignment()->setVertical($align);

    $spreadsheet->getActiveSheet()->getStyle('G5:H5')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('J5:L5')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('N5:O5')->getAlignment()->setWrapText(true);

    /*$spreadsheet->getActiveSheet()->getStyle('A4:P4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A4:P4')->getFill()->getStartColor()->setARGB('EBEDEF');
    $spreadsheet->getActiveSheet()->getStyle('A5:P5')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A5:P5')->getFill()->getStartColor()->setARGB('D6DBDF');*/

    $linha=5;

    $tbl_cobertura = mysqli_query($conector, "select * from tbl_item_cobertura
        inner join tbl_cobertura
                on tbl_ite_cobertura_numero_id = tbl_cobertura_id
        inner join tbl_protocoloiatf
                on tbl_protocoloiatf_id = tbl_cobertura_protocoloiatf   
        where tbl_cobertura_lixeira=0 and
              tbl_cobertura_codigo_estacao_monta='$id_estacao' and 
              tbl_cobertura_controle='C' and 
              tbl_ite_cobertura_codigo_id_animal = '$codigo_animal_id' and 
              tbl_ite_cobertura_numero_cobertura !=0
        order by tbl_ite_cobertura_numero_cobertura DESC");

    $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);
    $numero_cobertura = $num_rows_cobertura;

    if ($num_rows_cobertura!=0) {
        while ($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){
            $cobertura = $reg_cobertura->tbl_cobertura_id;
            $protocolo = $reg_cobertura->tbl_cobertura_protocoloiatf;

            $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura'");

            $num_rows_protocolo = mysqli_num_rows($sql);
            $reg_protocolo_cobertura = mysqli_fetch_object($sql);

            $tbl_item_iatf = mysqli_query($conector, "select * from tbl_item_protocoloiatf where tbl_ite_protocoloiatf_protocolo_id='$protocolo'
                order by tbl_ite_protocoloiatf_id ASC");

            $qtd_item_iatf = mysqli_num_rows($tbl_item_iatf);
            $tem_inseminacao = '';

            while ($reg_itens_iatf = mysqli_fetch_object($tbl_item_iatf)) {
                $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);
                $data = date("d/m/Y", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));
                $data = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);

                $data_inseminacao = date("Y-m-d", strtotime(str_replace('/', '-', $data)));
            }

            $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
            $tem_d0 = $reg_cobertura->tbl_ite_cobertura_dia_1;

            if ($qtd_item_iatf==2) {
                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_2;
            }

            if ($qtd_item_iatf==3) {
                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_3;
            }

            if ($qtd_item_iatf==4) {
                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_4;
            }

            if ($qtd_item_iatf==5) {
                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_5;
            }

            if ($qtd_item_iatf==6) {
                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_6;
            }

            if ($tem_inseminacao=='S' && $diagnostico!='P' && $diagnostico!='N') {
                $desc_diagnostico = 'Aguardando Diagnostico';
            }
            else if ($diagnostico=='P') {
                $desc_diagnostico = 'Diagnostico Positivo';
            }
            else if ($diagnostico=='N'){
                $desc_diagnostico = 'Diagnostico Negativo';
            }
            else {
                $desc_diagnostico = 'Aguardando Inseminação';
            } 

            $id_touro_semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

            $tbl_semen = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$id_touro_semen'");
            $num_rows = mysqli_num_rows($tbl_semen);

            if ($num_rows!=0) {
                $reg_touro_semen = mysqli_fetch_object($tbl_semen);

                if ($reg_touro_semen->tbl_semem_nome!='') {
                    $desc_touro_semen = $reg_touro_semen->tbl_semem_codigo_alfa .'-'. $reg_touro_semen->tbl_semem_nome;
                }
                else {
                    $desc_touro_semen = $reg_touro_semen->tbl_semem_codigo_alfa;
                }
            }
            else {
                $tbl_touro = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$id_touro_semen'");
                $num_rows = mysqli_num_rows($tbl_touro);

                if ($num_rows!=0) {
                    $reg_touro_semen = mysqli_fetch_object($tbl_touro);

                    if ($reg_touro_semen->tbl_animal_codigo_alfa!='') {
                        $desc_touro_semen = $reg_touro_semen->tbl_animal_codigo_alfa .'-'.$reg_touro_semen->tbl_animal_codigo_numerico;
                    }
                    else {
                        $desc_touro_semen = $reg_touro_semen->tbl_animal_codigo_numerico;
                    }
                }
                else {
                    $desc_touro_semen = '';
                }
            }

            if ($tem_d0=='S') {
                $linha++;

                $celulas = 'A'.$linha.':B'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'D'.$linha.':E'.$linha;
                $spreadsheet->getActiveSheet()->mergeCells($celulas);

                $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $numero_cobertura);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $data);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $desc_touro_semen);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $desc_diagnostico);
            }

            $numero_cobertura--;
        } 
    }

    $tbl_cobertura = mysqli_query($conector, "select * from tbl_historico_monta_natural
        where tbl_historico_monta_codigo_id_mae = '$codigo_animal_id'
        order by tbl_historico_monta_data_diagnostico DESC");  
    $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);
    $numero_cobertura = $num_rows_cobertura;

    if ($num_rows_cobertura!=0) {
        while ($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){
            $desc_diagnostico = 'Diagnostico Positivo';
            $desc_touro_semen = 'Monta Natural';
            $data = date("d/m/Y", strtotime($reg_cobertura->tbl_historico_monta_data_diagnostico));
            $data = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);

            $linha++;

            $celulas = 'A'.$linha.':B'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $celulas = 'D'.$linha.':E'.$linha;
            $spreadsheet->getActiveSheet()->mergeCells($celulas);

            $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);

            $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $data);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $desc_touro_semen);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $desc_diagnostico);
        } 
    }

    // partos
    $linha++;
    $celulas = 'A'.$linha.':G'.$linha;
    $spreadsheet->getActiveSheet()->mergeCells($celulas);

    $linha++;
    $celulas = 'A'.$linha.':G'.$linha;
    $spreadsheet->getActiveSheet()->mergeCells($celulas);

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$linha, "Nº Partos: " . $num_partos);

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setBold(true);

    $linha++;
    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A".$linha,"Nº ID")
            ->setCellValue("B".$linha,"Nascimento")
            ->setCellValue("C".$linha,"Categoria")
            ->setCellValue("D".$linha,"Sexo")
            ->setCellValue("E".$linha,"Touro/Semem")
            ->setCellValue("F".$linha,"Local")
            ->setCellValue("G".$linha,"Estação");

    $celulas = 'A'.$linha.':G'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');

    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);

    $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
        where tbl_mov_estoque_codigo_mae='$codigo_animal_id' and 
              tbl_mov_estoque_codigo_id_animal=999999999 and 
              tbl_mov_estoque_entrada_saida='E' and 
              tbl_mov_estoque_tipo_movimentacao='N'");

    $num_natimorto = mysqli_num_rows($tbl_natimorto);

    if ($num_natimorto!=0) {
        while ($reg_natimorto = mysqli_fetch_object($tbl_natimorto)) {
            $codigo_fazenda = $reg_natimorto->tbl_mov_estoque_local;
            $codigo_edi = 'Natimorto';
            $data = new DateTime($reg_natimorto->tbl_mov_estoque_nascimento); 
            $data_edi = $data->format('d/m/Y');
            $data_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_edi);                                             

            $data_nascimento = $reg_natimorto->tbl_mov_estoque_nascimento; 
            $sexo = $reg_natimorto->tbl_mov_estoque_sexo; 

            if ($sexo == 'N') {
                $sexo = 'Não identificado';
            }

            $tab_origem = mysqli_query($conector, "select * from tbl_pessoa 
                where tbl_pessoa_id='$codigo_fazenda'");
            $num_rows = mysqli_num_rows($tab_origem);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_origem);
                $desc_fazenda = $reg->tbl_pessoa_nome;
            }
            else {
                $desc_fazenda = '';
            }

            $desc_categoria = '';
            $descricao_pai = '';

            $linha++;

            $celulas = 'A'.$linha.':D'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $celulas = 'D'.$linha.':E'.$linha;

            $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);

            $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $data_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $desc_categoria);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $sexo);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_pai);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $desc_fazenda);
        }
    }
          
    $sql = "select * from tbl_animais 
               inner join tabela_racas
                       on tab_codigo_raca = tbl_animal_codigo_raca   
                    where tbl_animal_codigo_mae='$codigo_animal_id'
                    order by tbl_animal_data_nascimento DESC"; 
    $rs = mysqli_query($conector, $sql); 

    while ($reg_animal = mysqli_fetch_object($rs)){
        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
        $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
        $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
        $data_edi = $data->format('d/m/Y');
        $data_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_edi);                                             
        $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
        $sexo = $reg_animal->tbl_animal_sexo; 
        $raca = utf8_encode($reg_animal->tab_descricao_raca);
        $ativo = $reg_animal->tbl_animal_ativo;;
        $pai = $reg_animal->tbl_animal_codigo_pai; 
        $estacao_nascido = $reg_animal->tbl_animal_estacao_monta_nascimento; 

        if ($codigo_alfa=='') {
            $codigo_edi = intval($codigo_numerico);
        }
        else {
            $codigo_edi = $codigo_alfa . '-' . intval($codigo_numerico);
        }

        $tab_origem = mysqli_query($conector, "select * from tbl_pessoa 
            where tbl_pessoa_id='$codigo_fazenda'");
        $num_rows = mysqli_num_rows($tab_origem);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_origem);
            $desc_fazenda = utf8_encode($reg->tbl_pessoa_nome);
        }
        else {
            $desc_fazenda = '';
        }

        $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
        $num_rows_pai = mysqli_num_rows($tab_pai);

        if ($num_rows_pai!=0){
            $reg = mysqli_fetch_object($tab_pai);
            $descricao_pai = $reg->tbl_semem_codigo_alfa;
        }
        else {
            $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
            }
            else {
                $descricao_pai = '';
            }
        }

        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");
        $num_rows = mysqli_num_rows($categoria);    

        if ($num_rows!=0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $idade_de = $reg_categoria->tab_categoria_idade_de;
                $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                if ($idade >= $idade_de && $idade <= $idade_ate) {
                    if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                        $desc_categoria=' > 36 meses';
                    }
                    else {
                        $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
                    }
                }
            }
        }                   

        $estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
            WHERE tbl_par_estacao_id='$estacao_nascido'");
        $num_rows = mysqli_num_rows($estacao);    

        if ($num_rows!=0) {
            $reg_estacao = mysqli_fetch_object($estacao);
            $desc_estacao = $reg_estacao->tbl_par_estacao_nome;
        }
        else {
            $desc_estacao = '';
        }

        $linha++;

        $celulas = 'B'.$linha.':D'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $celulas = 'A'.$linha.':E'.$linha;
        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

        $celulas = 'G'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

        $celulas = 'F'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);

        $celulas = 'D'.$linha.':E'.$linha;

        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $data_edi);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $desc_categoria);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $sexo);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_pai);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $desc_fazenda);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $desc_estacao);
    } 

    // abortos
    $linha++;
    $celulas = 'A'.$linha.':G'.$linha;
    $spreadsheet->getActiveSheet()->mergeCells($celulas);

    $linha++;
    $celulas = 'A'.$linha.':G'.$linha;
    $spreadsheet->getActiveSheet()->mergeCells($celulas);

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$linha, "Nº Abortos: " . $num_abortos);
            
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setBold(true);

    $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
        where tbl_mov_estoque_codigo_mae='$codigo_animal_id' and 
              tbl_mov_estoque_codigo_id_animal=999999999 and 
              tbl_mov_estoque_entrada_saida='A'");

    $num_natimorto = mysqli_num_rows($tbl_natimorto);

    if ($num_natimorto!=0) {
        while ($reg_natimorto = mysqli_fetch_object($tbl_natimorto)) {
            $codigo_fazenda = $reg_natimorto->tbl_mov_estoque_local;
            $data = new DateTime($reg_natimorto->tbl_mov_estoque_nascimento); 
            $data_edi = $data->format('d/m/Y');
            $data_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_edi);

            $data_nascimento = $reg_natimorto->tbl_mov_estoque_nascimento; 
            $sexo = $reg_natimorto->tbl_mov_estoque_sexo; 
            $tipo_ocorrencia = $reg_natimorto->tbl_mov_estoque_tipo_movimentacao; 

            if ($sexo == 'N') {
                $sexo = 'Sexo não identificado';
            }

            if ($tipo_ocorrencia=='A') {
                $codigo_edi = 'Aborto';
            }
            else {
                $codigo_edi = 'Absorção';
            }

            $tab_origem = mysqli_query($conector, "select * from tbl_pessoa 
                where tbl_pessoa_id='$codigo_fazenda'");
            $num_rows = mysqli_num_rows($tab_origem);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_origem);
                $desc_fazenda = $reg->tbl_pessoa_nome;
            }
            else {
                $desc_fazenda = '';
            }

            $desc_categoria = '';
            $descricao_pai = '';

            $linha++;

            $celulas = 'D'.$linha.':E'.$linha;
            $spreadsheet->getActiveSheet()->mergeCells($celulas);

            $celulas = 'B'.$linha.':C'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $celulas = 'A'.$linha.':D'.$linha;
            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

            $celulas = 'C'.$linha.':D'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);

            $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);

            $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $data_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $desc_fazenda);
        }
    }
}
else { // lista geral
    $spreadsheet->getActiveSheet()->mergeCells('A1:Q1');
    $spreadsheet->getActiveSheet()->mergeCells('R1:S1');
    $spreadsheet->getActiveSheet()->mergeCells('B2:S2');
    $spreadsheet->getActiveSheet()->mergeCells('A3:S3');
    $spreadsheet->getActiveSheet()->mergeCells('A4:C4');
    $spreadsheet->getActiveSheet()->mergeCells('D4:E4');
    $spreadsheet->getActiveSheet()->mergeCells('F4:K4');
    $spreadsheet->getActiveSheet()->mergeCells('L4:S4');

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', $nome_relatorio)
            ->setCellValue("O1", "Data: " . $data_sistema)
            ->setCellValue("A2", "Filtro: ")
            ->setCellValue("B2", $descricao_filtro);

    $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);


    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A4","Total de Fêmeas")
            ->setCellValue("F4","Atual")
            ->setCellValue("L4","Estação de Monta");

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
    $spreadsheet->getActiveSheet()->getStyle('A4:D4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('F4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('L4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A5:S5')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A5:S5')->getAlignment()->setVertical($align);

    $spreadsheet->getActiveSheet()->getStyle('G5:H5')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('J5:L5')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('M5:S5')->getAlignment()->setWrapText(true);

    /*$spreadsheet->getActiveSheet()->getStyle('A4:P4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A4:P4')->getFill()->getStartColor()->setARGB('EBEDEF');
    $spreadsheet->getActiveSheet()->getStyle('A5:P5')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A5:P5')->getFill()->getStartColor()->setARGB('D6DBDF');*/

    $linha=5;

/*                $sql = "SELECT * from tbl_animais 
                    WHERE tbl_animal_lixeira=0 AND 
                          tbl_animal_ativo='$wativo' AND 
                          tbl_animal_sexo='F'"  . 
                            $wlocal . $worigem . $wraca . $wpai . $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .
                    " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC"; 

                $rs = mysqli_query($conector, $sql); 
                $num_rows_animais = mysqli_num_rows($rs);

                $animais_listados = 0;
                $ultimo_parto = '0000-00-00';
                $data_previsao = '0000-00-00';
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
                        $sql = mysqli_query($conector, "SELECT * FROM
                                tbl_item_cobertura
                            INNER JOIN tbl_cobertura
                                    ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                            INNER JOIN tbl_parametro_estacao_monta
                                    ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
                            WHERE tbl_cobertura_lixeira=0 AND 
                                      tbl_ite_cobertura_codigo_id_animal='$codigo'" . $westacao . "
                            ORDER BY tbl_ite_cobertura_numero_id DESC limit 1");

                        $num_rows = mysqli_num_rows($sql);

                        if ($num_rows!=0) {
                            $reg_cobertura = mysqli_fetch_object($sql);
                            $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
                            $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
                            $estacao_monta = $reg_cobertura->tbl_par_estacao_nome;
                        }
                        else {
                            $codigo_local = 0;
                            $estacao_animal = 0;
                            $estacao_monta = '';
                        }

                        // verifica numero de coberturas na estacao
                        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                            INNER JOIN tbl_cobertura
                                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                            WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
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
                            $descricao_pai = $reg->tbl_semem_codigo_alfa;
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
                        $data_acompanhamento_calculo = date("Y-m-d");
                        $date = new DateTime($data_nascimento); // Data de Nascimento
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
                        if ($solteiras=='S' || $paridas=='S') {
                            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                                WHERE tbl_animal_codigo_mae='$codigo'
                                ORDER BY tbl_animal_data_nascimento DESC limit 1");

                            $ultimo_filho = mysqli_num_rows($tbl_filhos);

                            if ($ultimo_filho!=0) {
                                $reg_filhos = mysqli_fetch_object($tbl_filhos);
                                $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento; 

                                $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                                $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                                if ($codigo_alfa_filho=='') {
                                    $codigo_edi_filho = intval($codigo_numerico_filho);
                                }
                                else {
                                    $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                                }

                                $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                                $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                                $num_rows = mysqli_num_rows($tab_pai);

                                if ($num_rows!=0){
                                    $reg = mysqli_fetch_object($tab_pai);
                                    $descricao_pai_ult_filho = $reg->tbl_semem_codigo_alfa;
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
                                    $vaca_parida = 'S';
                                    $vaca_solteira = '';
                                }
                                else {
                                    $vaca_solteira = 'S';
                                    $vaca_parida = '';
                                }
                            }
                            else {
                                $ultimo_parto = '0000-00-00';
                                $codigo_edi_filho = '';
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
                        else {
                            $ultimo_parto_edi = '';
                            $codigo_edi_filho = '';
                            $descricao_pai_ult_filho = '';

                            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                                WHERE tbl_animal_codigo_mae='$codigo'
                                ORDER BY tbl_animal_data_nascimento DESC limit 1");

                            $ultimo_filho = mysqli_num_rows($tbl_filhos);

                            if ($ultimo_filho!=0) {
                                $reg_filhos = mysqli_fetch_object($tbl_filhos);
                                $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento;

                                $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                                $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                                if ($codigo_alfa_filho=='') {
                                    $codigo_edi_filho = intval($codigo_numerico_filho);
                                }
                                else {
                                    $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                                }

                                $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                                $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                                $num_rows = mysqli_num_rows($tab_pai);

                                if ($num_rows!=0){
                                    $reg = mysqli_fetch_object($tab_pai);
                                    $descricao_pai_ult_filho = $reg->tbl_semem_codigo_alfa;
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
                        }
                        else {
                            $reg_natimorto = mysqli_fetch_object($tbl_natimorto);

                            $data_aborto_natimorto=$reg_natimorto->tbl_mov_estoque_nascimento;
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
                        }
                        else {
                            $reg_aborto = mysqli_fetch_object($tbl_aborto);

                            $data_aborto_natimorto=$reg_aborto->tbl_mov_estoque_nascimento;
                        }

                        // Verifica previsão de parto
                        if ($previsao_parto_de!='' && $previsao_parto_ate!='') {

                            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                                INNER JOIN tbl_cobertura
                                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                                WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
                                ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                            $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

                            if ($num_rows_coberturas!=0) {
                                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                                $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                                $cobertura_id = $reg_cobertura->tbl_cobertura_id;

                                $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;

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
                                $data_previsao_parto = '0000-00-00';
                            }

                            if ($data_previsao_parto=='0000-00-00') {
                                $previsao_parto_edi = '';
                            }
                            else {
                                $data = new DateTime($data_previsao_parto);
                                $previsao_parto_edi = $data->format('d/m/Y');
                                $previsao_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($previsao_parto_edi);                                           
                            }
                        }
                        else {
                            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                                INNER JOIN tbl_cobertura
                                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                                WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
                                ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                            $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

                            if ($num_rows_coberturas!=0) {
                                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                                $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                                $cobertura_id = $reg_cobertura->tbl_cobertura_id;

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

                                    $data_previsao = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                                }
                            }
                            else {
                                $data_previsao = '0000-00-00';
                            }

                            if ($data_previsao=='0000-00-00') {
                                $previsao_parto_edi = '';
                            }
                            else {
                                $data = new DateTime($data_previsao);
                                $previsao_parto_edi = $data->format('d/m/Y');
                                $previsao_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($previsao_parto_edi);                                           
                            }
                        }


                        // calcula data da aptidão
                        if ($data_aborto_natimorto!='0000-00-00') {
                            $data_aptidao_edi = date("d/m/Y", strtotime($data_aborto_natimorto . "+ 35 days"));
                        }
                        else if ($ultimo_parto!='0000-00-00') {
                            $data_aptidao_edi = date("d/m/Y", strtotime($ultimo_parto . "+ 35 days"));
                        }
                        else if ($data_previsao_parto!='0000-00-00') {
                            $data_aptidao_edi = date("d/m/Y", strtotime($data_previsao_parto . "+ 35 days"));
                        }
                        else if ($data_previsao!='0000-00-00') {
                            $data_aptidao_edi = date("d/m/Y", strtotime($data_previsao . "+ 35 days"));
                        }
                        else {
                            $data_aptidao_edi = '';
                        }

                        $data_aptidao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_aptidao_edi);    

                        // Verifica diagnostico
                        if ($filtro_positivo=='S' || $filtro_negativo=='S'){
                            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                                INNER JOIN tbl_cobertura
                                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                                WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
                                ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

                            $num_rows = mysqli_num_rows($tbl_item_cobertura);

                            if ($num_rows!=0) {
                                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                                $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

                                $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                                $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                                $num_rows = mysqli_num_rows($tab_pai);

                                if ($num_rows!=0){
                                    $reg = mysqli_fetch_object($tab_pai);
                                    $descricao_semen = $reg->tbl_semem_codigo_alfa;
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
                                WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
                                ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

                            $num_rows = mysqli_num_rows($tbl_item_cobertura);

                            if ($num_rows!=0) {
                                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                                $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                                $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                                $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                                $num_rows = mysqli_num_rows($tab_pai);

                                if ($num_rows!=0){
                                    $reg = mysqli_fetch_object($tab_pai);
                                    $descricao_semen = $reg->tbl_semem_codigo_alfa;
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
                                WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
                                ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

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

                                if ($wcategoria=="" && 
                                    $descarte==$vaca_descarte && 
                                    $data_previsao_parto>=$previsao_parto_de && 
                                    $data_previsao_parto<=$previsao_parto_ate && 
                                    (($solteiras==$vaca_solteira && ($previsao_parto_edi=='' || ($nascido=='N' || $nascido=='A' || 
                                        $nascido=='M' || $nascido=='O'))) || 
                                    ($paridas==$vaca_parida && 
                                    $ultimo_parto>=$data_paridas_de && 
                                    $ultimo_parto<=$data_paridas_ate)) &&

                                    $filtro_parto==$tem_parto &&
                                    $filtro_aborto==$tem_aborto && 
                                    $filtro_positivo==$tem_positivo && 
                                    $filtro_negativo==$tem_negativo 
                                    ) {
*/
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
            $sql = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura
                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                INNER JOIN tbl_parametro_estacao_monta
                        ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal='$codigo'" . $westacao . "
                ORDER BY tbl_ite_cobertura_numero_id DESC limit 1");

            $num_rows = mysqli_num_rows($sql);

            if ($num_rows!=0) {
                $reg_cobertura = mysqli_fetch_object($sql);
                $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
                $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
                $estacao_monta = $reg_cobertura->tbl_par_estacao_nome;
            }
            else {
                $codigo_local = 0;
                $estacao_animal = 0;
                $estacao_monta = '';
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
                            $descricao_pai = $reg->tbl_semem_codigo_alfa;
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
                        $data_acompanhamento_calculo = date("Y-m-d");
                        $date = new DateTime($data_nascimento); // Data de Nascimento
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
                    $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento; 
                    $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                    $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                    if ($codigo_alfa_filho=='') {
                        $codigo_edi_filho = intval($codigo_numerico_filho);
                    }
                    else {
                        $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                    }

                    $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai_ult_filho = $reg->tbl_semem_codigo_alfa;
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
                        $vaca_parida = 'S';
                        $vaca_solteira = '';
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
                    $vaca_solteira = '';
                }
            }
            else {
                $ultimo_parto_edi = '';
                $codigo_edi_filho = '';
                $descricao_pai_ult_filho = '';

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'
                    ORDER BY tbl_animal_data_nascimento DESC limit 1");

                $ultimo_filho = mysqli_num_rows($tbl_filhos);

                if ($ultimo_filho!=0) {
                    $reg_filhos = mysqli_fetch_object($tbl_filhos);
                    $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento;
                    $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                    $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                    if ($codigo_alfa_filho=='') {
                        $codigo_edi_filho = intval($codigo_numerico_filho);
                    }
                    else {
                        $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                    }

                    $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai_ult_filho = $reg->tbl_semem_codigo_alfa;
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

            if ($data_natimorto>$ultimo_parto) {
                $data = new DateTime($data_natimorto);
                $ultimo_parto_edi = $data->format('d/m/Y');
                $ultimo_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ultimo_parto_edi);
                $natimorto = 'S';
            }
            else {
                $natimorto = 'N';
            }

            // Verifica previsão de parto
            if ($previsao_parto_de!='' && $previsao_parto_ate!='') {

                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira=0 AND 
                          tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          tbl_cobertura_controle = 'C' AND 
                          tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                          tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
                    ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows_coberturas!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                    $cobertura_id = $reg_cobertura->tbl_cobertura_id;
                    $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;

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
                    $data_previsao_parto = '0000-00-00';
                }

                if ($data_previsao_parto=='0000-00-00') {
                    $previsao_parto_edi = '';
                }
                else {
                    $data = new DateTime($data_previsao_parto);
                    $previsao_parto_edi = $data->format('d/m/Y');
                    $previsao_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($previsao_parto_edi);                   }
            }
            else {
                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira=0 AND 
                          tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          tbl_cobertura_controle = 'C' AND 
                          tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                          tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
                    ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows_coberturas!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                    $cobertura_id = $reg_cobertura->tbl_cobertura_id;

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

                        $data_previsao_servico = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                    }
                }
                else {
                    $data_previsao_servico = '0000-00-00';
                }

                if ($data_previsao_servico=='0000-00-00') {
                    $previsao_parto_edi = '';
                }
                else {
                    $data = new DateTime($data_previsao_servico);
                    $previsao_parto_edi = $data->format('d/m/Y');
                    $previsao_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($previsao_parto_edi);                   }
            }

            // calcula data da aptidão

            $data_aptidao_edi = '';

            if ($ultimo_parto!='0000-00-00') {
                $data_aptidao_edi = date("d/m/Y", strtotime($ultimo_parto . "+ 35 days"));
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
                          tbl_cobertura_controle = 'C' AND 
                          tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
                    ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

                $num_rows = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                    $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_semen = $reg->tbl_semem_codigo_alfa;
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
                          tbl_cobertura_controle = 'C' AND 
                          tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
                    ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

                $num_rows = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                    $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_semen = $reg->tbl_semem_codigo_alfa;
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
                      tbl_cobertura_controle = 'C' AND 
                      tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
                ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

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
    $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Considerado aqui a data do Natimorto.');
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
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $estacao_monta);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $num_coberturas);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $diagnostico);
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
                                                $spreadsheet->getActiveSheet()->getStyle('P'.$linha)->getFont()->setColor(new Color(Color::COLOR_GRAY));
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
    $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Considerado aqui a data do Natimorto.');
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
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $estacao_monta);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $num_coberturas);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $diagnostico);
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

    $spreadsheet->getActiveSheet()->getStyle('D4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 4, $animais_listados);

}

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
exit;


?>