<?php
// AJUSTE DO PESO DE DESMAMA
function calcular_peso_desmama($peso_desmama, $peso_nasc, $data_inicial, $data_final) {
    if ($peso_desmama!='' && $peso_desmama!=0) {
        if ($peso_nasc=='' || $peso_nasc==0) {
            $peso_nasc = 30;
        }
        $diferenca = strtotime($data_final) - strtotime($data_inicial);
        $dias = floor($diferenca / (60 * 60 * 24));

        $diferenca_peso = $peso_desmama - $peso_nasc;
        $gmd = $diferenca_peso/$dias;

        $peso_desmama = $peso_nasc + ($gmd * 205);
        $peso_desmama_edi = number_format($peso_desmama,2,',','.');
    }
    else {
        $peso_desmama = '';
    }
    return $peso_desmama;
}
// FIM AJUSTE DO PESO DE DESMAMA

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

$_SESSION['opcao_situacao_reprodutica_rel']='I';

//$codigo_alfa = $_REQUEST["codigo_alfa"];
$codigo_alfa_numerico = $_REQUEST["codigo_alfa_numerico"];

if ($codigo_alfa_numerico!='') {
    $codigo_numerico_consulta = substr($codigo_alfa_numerico, -9);

    if (strlen($codigo_alfa_numerico)!=9){
        $data = explode("-", $codigo_alfa_numerico);
        $codigo_alfa_consulta = $data[0];
    }
    else {
        $codigo_alfa_consulta = '';
    }
}

$mensagem = '';

$tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
    INNER JOIN tbl_pessoa
            ON tbl_pessoa_id = tbl_animal_codigo_fazenda
    WHERE tbl_animal_codigo_alfa='$codigo_alfa_consulta' AND 
          tbl_animal_codigo_numerico='$codigo_numerico_consulta' AND 
          tbl_animal_sexo='F'"); 

$num_rows_animais = mysqli_num_rows($tbl_animais);

if ($num_rows_animais!=0) {
        $reg_animal = mysqli_fetch_object($tbl_animais);
        $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
        $ativo = $reg_animal->tbl_animal_ativo;
        $animal_situacao = $reg_animal->tbl_animal_situacao;
        $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
        $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
        $descarte = $reg_animal->tbl_animal_descarte_reproducao;
        $descarte_em = new DateTime($reg_animal->tbl_animal_descarte_em);
        $descarte_em_edi = $descarte_em->format('d/m/Y H:i:s');
        $descarte_por = 'Por ' . $reg_animal->tbl_animal_descarte_por .' em '. $descarte_em_edi;
        $nome_pessoa = utf8_encode($reg_animal->tbl_pessoa_nome); 
        $descricao_filtro = utf8_encode($nome_pessoa);     
        $codigo_origem = $reg_animal->tbl_animal_codigo_origem;
        $pai = $reg_animal->tbl_animal_codigo_pai;

        $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
        $num_rows_pai = mysqli_num_rows($tab_pai);

        if ($num_rows_pai!=0){
            $reg = mysqli_fetch_object($tab_pai);
                $descricao_pai = utf8_encode($reg->tbl_semem_nome);
        }
        else {
            $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . ltrim($reg->tbl_animal_codigo_numerico, "0");
            }
            else {
                $descricao_pai = '';
            }
        }

        $mae =  $reg_animal->tbl_animal_codigo_mae;

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

        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
        $data_baixa = $reg_animal->tbl_animal_baixado_em;

        if ($data_baixa!='') {
            $data_acompanhamento_calculo = date($data_baixa);
        }
        else {
            $data_acompanhamento_calculo = date("Y-m-d");
        }

        $date = new DateTime($data_nascimento);

        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');

        $idade_ano = $idade_acompanhamento->format('%Y');
        $idade_mes = $idade_acompanhamento->format('%m');
        $idade_animal = $idade_acompanhamento_mostra_anos+
                        $idade_acompanhamento_mostra_meses;

        if ($idade_ano==0 && $idade_mes!=0) {
            $desc_idade = $idade_mes . ' mes(es)';
        }
        else if ($idade_ano!=0 && $idade_mes==0){
            $desc_idade = $idade_ano . ' ano(s)';
        }
        else if ($idade_ano!=0 && $idade_mes!=0) {
            $desc_idade = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
        }
        else {
            $desc_idade = '';
        }

        $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
        $data_nascimento_edi = $data->format('d/m/Y');

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

        switch ($codigo_categoria) {
            case '001':
                $desc_categoria= '00 a 07 meses';
                break;
            case '002':
                $desc_categoria= '08 a 12 meses';
                break;
            case '003':
                $desc_categoria= '13 a 24 meses';
                break;
        case '004':
                $desc_categoria= '25 a 36 meses';
                break;
            case '005':
                $desc_categoria= '> 36 meses';
                break;
        }     

        $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_origem'");
                
        $num_rows = mysqli_num_rows($tab_fazenda);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_fazenda);
            $desc_origem = utf8_encode($reg->tbl_pessoa_nome);
        }
        else {
            $desc_origem = '';
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

        if ($reg_animal->tbl_animal_em_estacao_monta=='S') {
            $em_estacao_monta ='SIM';
        }
        else {
            $em_estacao_monta ='NÃO';
        }

        // Verifica quantas estações teve para a femea
        $qtd_estacoes = 0;
        $id_estacao_ant = 0;

        $tbl_item_cobertura = mysqli_query($conector, "SELECT * from tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
            WHERE tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' AND   
                  tbl_cobertura_controle = 'C' AND 
                  tbl_cobertura_lixeira = 0
            ORDER BY tbl_cobertura_codigo_estacao_monta ASC"); 

        $num_rows_itens = mysqli_num_rows($tbl_item_cobertura);

        if ($num_rows_itens!=0) {
            while ($reg_item = mysqli_fetch_object($tbl_item_cobertura)) {
                $id_estacao = $reg_item->tbl_cobertura_codigo_estacao_monta;

                if ($id_estacao != $id_estacao_ant) {
                    $qtd_estacoes++;
                    $id_estacao_ant=$id_estacao;
                }
            }
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
            $diagnostico = $reg_item->tbl_ite_cobertura_resultado_diagnostico;
            $nascido = $reg_item->tbl_ite_cobertura_nascido;

            $id_estacao = $reg_item->tbl_cobertura_codigo_estacao_monta;
            $estacao_monta = $reg_item->tbl_par_estacao_nome;
        }
        else {
            $estacao_monta = '';
            $id_estacao = 0;
            $diagnostico = '';
            $nascido = '';
        }

        // verifica abortos/absorsão
        $tbl_aborto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
            where tbl_mov_estoque_codigo_mae='$codigo_animal_id' and 
                  tbl_mov_estoque_entrada_saida='A'");

        $qtd_abortos = mysqli_num_rows($tbl_aborto);

        // primeiro verifica quantos partos
        $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
            WHERE tbl_animal_codigo_mae='$codigo_animal_id'");

        $qtd_partos = mysqli_num_rows($tbl_filhos);

        // verifica parto natimorto
        $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
            where tbl_mov_estoque_codigo_mae='$codigo_animal_id' and 
                  tbl_mov_estoque_codigo_id_animal=999999999 and 
                  tbl_mov_estoque_entrada_saida='E' and 
                  tbl_mov_estoque_tipo_movimentacao='N'");
        $qtd_natimorto = mysqli_num_rows($tbl_natimorto);

        // Verifica situação da Fêmea
        $situacao = '';

        if ($diagnostico=='P' && $nascido=='') {
            $situacao = 'Prenha';
        }
        else {
            // Verifica qual o ultimo parto para saber a idade
            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo_animal_id'
                ORDER BY tbl_animal_data_nascimento DESC limit 1");

            $ultimo_filho = mysqli_num_rows($tbl_filhos);

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
                    $situacao = 'Parida';
                }
                else {
                    $situacao = 'Solteira';
                }
            }
            else {
                $situacao = 'Solteira';
            }
        }
    }
    else {
        $mensagem = ' - Registro não encontrado';
    }


$nome_relatorio = "Situação Reprodutiva - Individual";

$spreadsheet->getActiveSheet()->mergeCells('A1:L1');
$spreadsheet->getActiveSheet()->mergeCells('B9:D9');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue("M1", "Data: " . $data_sistema)
    ->setCellValue("A2", "Fêmea ")
    ->setCellValue("B2", $codigo_alfa_numerico . ' - ' . $situacao)
    ->setCellValue("A3", "Nascimento ")
    ->setCellValue("B3", $data_nascimento_edi)
    ->setCellValue("C3", "Idade ")
    ->setCellValue("D3", $desc_idade)
    ->setCellValue("A4", "Categoria ")
    ->setCellValue("B4", $desc_categoria)
    ->setCellValue("A5", "Raça ")
    ->setCellValue("B5", $descricao_raca)
    ->setCellValue("C5", "Pelagem ")
    ->setCellValue("D5", $descricao_pelagem)
    ->setCellValue("A6", "Fazenda ")
    ->setCellValue("B6", $descricao_filtro)
    ->setCellValue("A7", "Origem ")
    ->setCellValue("B7", $desc_origem)
    ->setCellValue("A9", "Pai ")
    ->setCellValue("B9", $descricao_pai)
    ->setCellValue("A10", "Mãe ")
    ->setCellValue("B10", $descricao_mae);

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A8", "Animal Ativo ")
            ->setCellValue("B8", $ativo);

    if ($ativo == 'Não') {
        $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("C8", "Situação ")
                ->setCellValue("D8", $animal_situacao);
        $spreadsheet->getActiveSheet()->getStyle('B8')->getFont()->setColor(new Color(Color::COLOR_RED));
        $spreadsheet->getActiveSheet()->getStyle('D8')->getFont()->setColor(new Color(Color::COLOR_RED));
    }
    else {
        $spreadsheet->getActiveSheet()->getStyle('B8')->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
    }

    if ($descarte == 'S') {
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A11", "Descartado ")
            ->setCellValue("B11", $descarte_por);
        $spreadsheet->getActiveSheet()->getStyle('B11')->getFont()->setColor(new Color(Color::COLOR_RED));
    }

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('B13', "Estações de Monta: " . $qtd_estacoes)
            ->setCellValue("C13", "Partos: " . $qtd_partos)
            ->setCellValue("D13", "Natimorto: " . $qtd_natimorto)
            ->setCellValue("E13", "Abortos: " .$qtd_abortos);

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('B14', "Estação")
            ->setCellValue('C14', "Data")
            ->setCellValue("D14", "Fazenda ")
            ->setCellValue("E14", "Cobertura(s)")
            ->setCellValue("F14", "Diagnóstico Final")
            ->setCellValue("G14", "Touro/Sêmen")
            ->setCellValue("H14", "Nascimento Bezerro")
            ->setCellValue("I14", "ID")
            ->setCellValue("J14", "Sexo")
            ->setCellValue("K14", "Peso Desmama")
            ->setCellValue("L14", "Raça")
            ->setCellValue("M14", "Idade");

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(13);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(23);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(13);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(24);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(13);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(21);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(8);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(19);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(20);


    $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('A2:A11') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('C6') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('M1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->getStyle('B13:E13')->getFont()->setBold(true);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('B13:M13')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('B13:M13')->getAlignment()->setVertical($align);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('B14:M14')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('B14:M14')->getAlignment()->setVertical($align);

    $spreadsheet->getActiveSheet()->getStyle('E14')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('G14')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('J14')->getAlignment()->setWrapText(true);

    $spreadsheet->getActiveSheet()->setShowGridlines(true);

    $spreadsheet->getActiveSheet()->getStyle('B14:M14')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('B14:M14')->getFill()->getStartColor()->setARGB('BFBFBF');

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

    $linha=14;

    $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        INNER JOIN tbl_cobertura
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
        INNER JOIN tbl_pessoa
                ON tbl_cobertura_codigo_local = tbl_pessoa_id 
        WHERE tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' AND 
              (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M') AND 
              tbl_cobertura_lixeira = 0
        ORDER BY tbl_cobertura_codigo_estacao_monta DESC, tbl_cobertura_id ASC");

    $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);  

    if ($num_rows_cobertura!=0) {
        $estacao_anterior = 0;
        $numero_coberturas = 0;

        while ($reg_cobertura = mysqli_fetch_object($tbl_cobertura)) {
            $cobertura = $reg_cobertura->tbl_cobertura_id;
            $controle = $reg_cobertura->tbl_cobertura_controle;
            $item = $reg_cobertura->tbl_ite_cobertura_numero_item;
            $estacao = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
            $protocolo = $reg_cobertura->tbl_cobertura_protocoloiatf;
            $nome_fazenda = $reg_cobertura->tbl_pessoa_nome;
            $id_touro_semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;
            $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;
            $situacao_femea = $reg_cobertura->tbl_ite_cobertura_situacao_femea_nascido_outro;

            $tab_parametro = mysqli_query($conector, "select * from tbl_parametro_estacao_monta 
                where tbl_par_estacao_id='$estacao'");
            $num_rows_parametro = mysqli_num_rows($tab_parametro);

            if ($num_rows_parametro!=0){
                $reg_estacao = mysqli_fetch_object($tab_parametro);
                $desc_estacao = $reg_estacao->tbl_par_estacao_nome;
                $data_parametro_estacao = $reg_estacao->tbl_par_estacao_monta_inicial;
            }
            else {
                $desc_estacao = 'Monta';
                $data_parametro_estacao = 0;
            }

            $data_emissao = '';

            if ($controle=='C') {
                if ($data_parametro_estacao!=0) {
                    $data_emissao = new DateTime($data_parametro_estacao);
                    //$data_emissao_edi = $data_emissao->format('d/m/Y');
                }
            }
            else if ($controle=='M'){
                $data_prenhes = $reg_cobertura->tbl_ite_cobertura_data_prenhes;
                $dataObj = DateTime::createFromFormat('Y-m-d', $data_prenhes);

                if ($dataObj !== false) {
                    $data_emissao = new DateTime($data_prenhes);
                    //$data_emissao_edi = $data_emissao->format('d/m/Y');
                } 
                else {
                    $data_emissao = new DateTime($reg_cobertura->tbl_ite_cobertura_data_emissao);
                    //$data_emissao_edi = $data_emissao->format('d/m/Y');
                }
            }

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$id_touro_semen'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_semem_nome;
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$id_touro_semen'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . ltrim($reg->tbl_animal_codigo_numerico, "0");
                }
                else {
                    $descricao_pai = '';
                }
            }

            $tbl_item_iatf = mysqli_query($conector, "SELECT * FROM tbl_item_protocoloiatf WHERE tbl_ite_protocoloiatf_protocolo_id='$protocolo'");
            $qtd_item_iatf = mysqli_num_rows($tbl_item_iatf);

            $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
            $tem_d0 = $reg_cobertura->tbl_ite_cobertura_dia_1;

            $tem_inseminacao = '';
            
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

            if (($tem_inseminacao=='S' && $diagnostico!='P' && $diagnostico!='N') || ($controle=='M' && $diagnostico!='P' && $diagnostico!='N')) {
                $desc_diagnostico = 'Aguardando Diagnóstico';
            }
            else if ($diagnostico=='P') {
                $desc_diagnostico = 'Positivo';
            }
            else if ($diagnostico=='N'){
                $desc_diagnostico = 'Negativo';
            }
            else {
                $desc_diagnostico = 'Aguardando Inseminação';
            } 

            if ($estacao!=$estacao_anterior && $estacao_anterior!=0) {
                // pega dados do nascimento do bezzero
                $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
                    INNER JOIN tbl_pessoa
                            ON tbl_pessoa_id = tbl_animal_codigo_fazenda
                    WHERE tbl_animal_codigo_mae='$codigo_animal_id' AND 
                          tbl_animal_estacao_monta_nascimento='$estacao_anterior' 
                    ORDER BY tbl_animal_codigo_id DESC"); 

                $num_rows_animais = mysqli_num_rows($tbl_animais);
                $gemelar=0;

                if ($num_rows_animais!=0) {
                    while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                        $codigo_numerico = ltrim($reg_animal->tbl_animal_codigo_numerico, "0");
                        $ativo = $reg_animal->tbl_animal_ativo;
                        $raca_id = $reg_animal->tbl_animal_codigo_raca;
                        $sexo = $reg_animal->tbl_animal_sexo;
                        $peso_desmama = $reg_animal->tbl_animal_peso_desmama;
                        $peso_nasc = $reg_animal->tbl_animal_primeiro_peso;
                        $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                        $data_final = $reg_animal->tbl_animal_data_desmama;
                        $situacao = $reg_animal->tbl_animal_situacao;

                        $peso_desmama = calcular_peso_desmama($peso_desmama, $peso_nasc, $data_inicial, $data_final);

                        if ($codigo_alfa=='') {
                            $codigo_animal_edi = $codigo_numerico;            
                        }
                        else {
                            $codigo_animal_edi = $codigo_alfa . '-' . $codigo_numerico;
                        }

                        $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca ='$raca_id'");
                        $num_rows_raca = mysqli_num_rows($tab_raca);

                        if ($num_rows_raca!=0){
                            $reg = mysqli_fetch_object($tab_raca);
                            $descricao_raca = utf8_encode($reg->tab_descricao_raca);
                        }
                        else{
                            $descricao_raca = '';
                        }

                        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
                        $data_baixa = $reg_animal->tbl_animal_baixado_em;

                        if ($data_baixa!='') {
                            $data_acompanhamento_calculo = date($data_baixa);
                        }
                        else {
                            $data_acompanhamento_calculo = date("Y-m-d");
                        }

                        $date = new DateTime($data_nascimento);

                        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade_acompanhamento_mostra_dia = $idade_acompanhamento->format('%d');

                        $idade_ano = $idade_acompanhamento->format('%Y');
                        $idade_mes = $idade_acompanhamento->format('%m');
                        $idade_dia = $idade_acompanhamento->format('%d');

                        if ($idade_ano==0 && $idade_mes!=0) {
                            $idade_animal = $idade_mes . ' mes(es)';
                        }
                        else if ($idade_ano!=0 && $idade_mes==0){
                            $idade_animal = $idade_ano . ' ano(s)';
                        }
                        else if ($idade_ano!=0 && $idade_mes!=0) {
                            $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
                        }
                        else if ($idade_ano==0 && $idade_mes==0){
                            $idade_animal = $idade_dia . ' dia(s)';
                        }
                        else {
                            $idade_animal = '';
                        }

                        $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
                        $data_nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);

                        $linha++;

                        $celulas = 'C'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $celulas = 'E'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $celulas = 'H'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $celulas = 'J'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $celulas = 'K'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        if ($data_nascimento_edi!='') {
                            $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                        }

                        if ($data_emissao_anterior!='') {
                            $data_emissao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_emissao_anterior);

                            $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                        }

                        if ($peso_desmama!='' && $peso_desmama!=0) {
                            $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                        }
                        else {
                            $peso_desmama='';
                        }

                        if ($gemelar==0) {
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_estacao_anterior);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $data_emissao_edi);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $nome_fazenda_anterior);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $numero_coberturas);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $diagnostico_anterior);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_pai_anterior);
                            $gemelar++;
                        }

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $data_nascimento_edi);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $codigo_animal_edi);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $sexo);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $peso_desmama);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_raca);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $idade_animal);

                        if ($situacao!='') {
                            $celulas = 'G'.$linha.':L'.$linha;

                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                    }
                }
                else {
                    if ($nascido_anterior=='M') {
                        $codigo_animal_edi='Natimorto';
                    }
                    else if ($nascido_anterior=='A') {
                        $codigo_animal_edi='Aborto';
                    }
                    else if ($situacao_femea_anterior=='M'){
                        $codigo_animal_edi='F Morreu';
                    }
                    else if ($situacao_femea_anterior=='V'){
                        $codigo_animal_edi='F Vendida';
                    }
                    else {
                        $codigo_animal_edi='';
                    }

                    // data da movimentacao na tabela de estoque

                    $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_movimentacao_estoque 
                        WHERE tbl_mov_estoque_cobertura_numero_id='$cobertura_anterior' AND 
                              tbl_mov_estoque_cobertura_numero_item='$item_anterior'"); 

                    $num_rows_estoque = mysqli_num_rows($tbl_estoque);

                    if ($num_rows_estoque!=0) {
                        $reg_estoque = mysqli_fetch_object($tbl_estoque);
                        $data = new DateTime($reg_estoque->tbl_mov_estoque_nascimento); 
                        $data_nascimento_edi = $data->format('d/m/Y');
                        $sexo = $reg_estoque->tbl_mov_estoque_sexo;
                    }
                    else {
                        $data_nascimento_edi = '';
                        $sexo = '';
                    }
                    
                    $descricao_raca = '';
                    $idade_animal = '';
                    $peso_desmama = '';

                    $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
                    $data_nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);

                    $linha++;

                    $celulas = 'C'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $celulas = 'E'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $celulas = 'H'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $celulas = 'J'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $celulas = 'K'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    if ($data_nascimento_edi!='') {
                        $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                    }

                    if ($data_emissao_anterior!='') {
                        $data_emissao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_emissao_anterior);

                        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                    }

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_estacao_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $data_emissao_edi);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $nome_fazenda_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $numero_coberturas);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $diagnostico_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_pai_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $data_nascimento_edi);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $codigo_animal_edi);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $sexo);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $peso_desmama);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_raca);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $idade_animal);

                }
                // fim dados nascimento do bezzero                

                $estacao_anterior=$estacao;
                $numero_coberturas = 1;
                $diagnostico_anterior = $desc_diagnostico;
                $desc_estacao_anterior = $desc_estacao;
                $nome_fazenda_anterior = $nome_fazenda;
                $descricao_pai_anterior = $descricao_pai;
                $nascido_anterior = $nascido;
                $situacao_femea_anterior = $situacao_femea;
                $cobertura_anterior = $cobertura;
                $item_anterior = $item;
                $data_emissao_anterior = $data_emissao;

                if ($controle=='M' && $desc_diagnostico=='Aguardando Diagnóstico') {
                    $numero_coberturas--;
                }
            }
            else {
                $estacao_anterior=$estacao;
                $numero_coberturas++;
                $diagnostico_anterior = $desc_diagnostico;
                $desc_estacao_anterior = $desc_estacao;
                $nome_fazenda_anterior = $nome_fazenda;
                $descricao_pai_anterior = $descricao_pai;
                $nascido_anterior = $nascido;
                $situacao_femea_anterior = $situacao_femea;
                $cobertura_anterior = $cobertura;
                $item_anterior = $item;
                $data_emissao_anterior = $data_emissao;

                if ($controle=='M' && $desc_diagnostico=='Aguardando Diagnóstico') {
                    $numero_coberturas--;
                }
            }
        }

        // pega dados do nascimento do bezzero

        $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
            INNER JOIN tbl_pessoa
                    ON tbl_pessoa_id = tbl_animal_codigo_fazenda
            WHERE tbl_animal_codigo_mae='$codigo_animal_id' AND 
                  tbl_animal_estacao_monta_nascimento='$estacao_anterior' 
            ORDER BY tbl_animal_codigo_id DESC"); 

        $num_rows_animais = mysqli_num_rows($tbl_animais);
        $gemelar = 0;

        if ($num_rows_animais!=0) {
            while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                $codigo_numerico = ltrim($reg_animal->tbl_animal_codigo_numerico, "0");
                $ativo = $reg_animal->tbl_animal_ativo;
                $situacao = $reg_animal->tbl_animal_situacao;
                $raca_id = $reg_animal->tbl_animal_codigo_raca;
                $sexo = $reg_animal->tbl_animal_sexo;
                $peso_desmama = $reg_animal->tbl_animal_peso_desmama;
                $peso_nasc = $reg_animal->tbl_animal_primeiro_peso;
                $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                $data_final = $reg_animal->tbl_animal_data_desmama;

                $peso_desmama = calcular_peso_desmama($peso_desmama, $peso_nasc, $data_inicial, $data_final);

                if ($codigo_alfa=='') {
                    $codigo_animal_edi = $codigo_numerico;            
                }
                else {
                    $codigo_animal_edi = $codigo_alfa . '-' . $codigo_numerico;
                }

                $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca ='$raca_id'");
                $num_rows_raca = mysqli_num_rows($tab_raca);

                if ($num_rows_raca!=0){
                    $reg = mysqli_fetch_object($tab_raca);
                    $descricao_raca = utf8_encode($reg->tab_descricao_raca);
                }
                else{
                    $descricao_raca = '';
                }

                $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
                $data_baixa = $reg_animal->tbl_animal_baixado_em;

                if ($data_baixa!='') {
                    $data_acompanhamento_calculo = date($data_baixa);
                }
                else {
                    $data_acompanhamento_calculo = date("Y-m-d");
                }

                $date = new DateTime($data_nascimento);
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade_acompanhamento_mostra_dia = $idade_acompanhamento->format('%d');

                $idade_ano = $idade_acompanhamento->format('%Y');
                $idade_mes = $idade_acompanhamento->format('%m');
                $idade_dia = $idade_acompanhamento->format('%d');

                if ($idade_ano==0 && $idade_mes!=0) {
                    $idade_animal = $idade_mes . ' mes(es)';
                }
                else if ($idade_ano!=0 && $idade_mes==0){
                    $idade_animal = $idade_ano . ' ano(s)';
                }
                else if ($idade_ano!=0 && $idade_mes!=0) {
                    $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
                }
                else if ($idade_ano==0 && $idade_mes==0){
                    $idade_animal = $idade_dia . ' dia(s)';
                }
                else {
                    $idade_animal = '';
                }

                $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
                $data_nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);

                $linha++;

                $celulas = 'C'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'E'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'H'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'J'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'K'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                if ($data_nascimento_edi!='') {
                    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                }

                if ($data_emissao_anterior!='') {
                    $data_emissao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_emissao_anterior);

                    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                }

                if ($peso_desmama!='' && $peso_desmama!=0) {
                    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                }
                else {
                    $peso_desmama='';
                }

                if ($gemelar==0) {
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_estacao_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $data_emissao_edi);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $nome_fazenda_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $numero_coberturas);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $diagnostico_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_pai_anterior);
                    $gemelar++;
                }

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $data_nascimento_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $codigo_animal_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $sexo);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $peso_desmama);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_raca);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $idade_animal);

                if ($situacao!='') {
                    $celulas = 'G'.$linha.':L'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                }
            }
        }
        else {
            if ($nascido_anterior=='M') {
                $codigo_animal_edi='Natimorto';
            }
            else if ($nascido_anterior=='A') {
                $codigo_animal_edi='Aborto';
            }
            else if ($situacao_femea_anterior=='M'){
                $codigo_animal_edi='F Morreu';
            }
            else if ($situacao_femea_anterior=='V'){
                $codigo_animal_edi='F Vendida';
            }
            else {
                $codigo_animal_edi='';
            }

            // data da movimentacao na tabela de estoque

            $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_cobertura_numero_id='$cobertura_anterior' AND 
                      tbl_mov_estoque_cobertura_numero_item='$item_anterior'"); 

            $num_rows_estoque = mysqli_num_rows($tbl_estoque);

            if ($num_rows_estoque!=0) {
                $reg_estoque = mysqli_fetch_object($tbl_estoque);
                $data = new DateTime($reg_estoque->tbl_mov_estoque_nascimento); 
                $data_nascimento_edi = $data->format('d/m/Y');
                $sexo = $reg_estoque->tbl_mov_estoque_sexo;
            }
            else {
                $data_nascimento_edi = '';
                $sexo = '';
            }

            $descricao_raca = '';
            $idade_animal = '';
            $peso_desmama = '';
            $situacao = '';

            $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
            $data_nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);

            $linha++;

            $celulas = 'C'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $celulas = 'E'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $celulas = 'H'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $celulas = 'J'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $celulas = 'K'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            if ($data_nascimento_edi!='') {
                $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
            }

            if ($data_emissao_anterior!='') {
                $data_emissao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_emissao_anterior);

                $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
            }

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_estacao_anterior);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $data_emissao_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $nome_fazenda_anterior);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $numero_coberturas);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $diagnostico_anterior);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_pai_anterior);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $data_nascimento_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $codigo_animal_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $sexo);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $peso_desmama);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_raca);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $idade_animal);

        }
        // fim dados nascimento do bezzero                
    }  

/*    $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        INNER JOIN tbl_cobertura
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
        INNER JOIN tbl_parametro_estacao_monta
                ON tbl_cobertura_codigo_estacao_monta = tbl_par_estacao_id 
        INNER JOIN tbl_pessoa
                ON tbl_cobertura_codigo_local = tbl_pessoa_id 
        WHERE tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' AND 
              tbl_cobertura_controle = 'C' AND 
              tbl_cobertura_lixeira = 0
        ORDER BY tbl_cobertura_codigo_estacao_monta DESC, tbl_cobertura_id ASC");

    $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);  

    if ($num_rows_cobertura!=0) {
        $estacao_anterior = 0;
        $numero_coberturas = 0;

        while ($reg_cobertura = mysqli_fetch_object($tbl_cobertura)) {
            $cobertura = $reg_cobertura->tbl_cobertura_id;
            $item = $reg_cobertura->tbl_ite_cobertura_numero_item;
            $estacao = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
            $protocolo = $reg_cobertura->tbl_cobertura_protocoloiatf;
            $desc_estacao = $reg_cobertura->tbl_par_estacao_nome;
            $nome_fazenda = utf8_encode($reg_cobertura->tbl_pessoa_nome);
            $id_touro_semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;
            $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;
            $situacao_femea = $reg_cobertura->tbl_ite_cobertura_situacao_femea_nascido_outro;

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$id_touro_semen'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = utf8_encode($reg->tbl_semem_nome);
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$id_touro_semen'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . ltrim($reg->tbl_animal_codigo_numerico, "0");
                }
                else {
                    $descricao_pai = '';
                }
            }

            $tbl_item_iatf = mysqli_query($conector, "SELECT * FROM tbl_item_protocoloiatf WHERE tbl_ite_protocoloiatf_protocolo_id='$protocolo'");
            $qtd_item_iatf = mysqli_num_rows($tbl_item_iatf);

            $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
            $tem_d0 = $reg_cobertura->tbl_ite_cobertura_dia_1;

            $tem_inseminacao = '';
            
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
                $desc_diagnostico = 'Aguardando Diagnóstico';
            }
            else if ($diagnostico=='P') {
                $desc_diagnostico = 'Positivo';
            }
            else if ($diagnostico=='N'){
                $desc_diagnostico = 'Negativo';
            }
            else {
                $desc_diagnostico = 'Aguardando Inseminação';
            } 

            if ($estacao!=$estacao_anterior && $estacao_anterior!=0) {

                // pega dados do nascimento do bezzero
                $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
                    INNER JOIN tbl_pessoa
                            ON tbl_pessoa_id = tbl_animal_codigo_fazenda
                    WHERE tbl_animal_codigo_mae='$codigo_animal_id' AND 
                          tbl_animal_estacao_monta_nascimento='$estacao_anterior' 
                    ORDER BY tbl_animal_codigo_id DESC"); 

                $num_rows_animais = mysqli_num_rows($tbl_animais);
                $gemelar=0;

                if ($num_rows_animais!=0) {
                    while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                        $codigo_numerico = ltrim($reg_animal->tbl_animal_codigo_numerico, "0");
                        $ativo = $reg_animal->tbl_animal_ativo;
                        $raca_id = $reg_animal->tbl_animal_codigo_raca;
                        $sexo = $reg_animal->tbl_animal_sexo;
                        $peso_desmama = $reg_animal->tbl_animal_peso_desmama;
                        $peso_nasc = $reg_animal->tbl_animal_primeiro_peso;
                        $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                        $data_final = $reg_animal->tbl_animal_data_desmama;

                        $peso_desmama = calcular_peso_desmama($peso_desmama, $peso_nasc, $data_inicial, $data_final);

                        if ($codigo_alfa=='') {
                            $codigo_animal_edi = $codigo_numerico;            
                        }
                        else {
                            $codigo_animal_edi = $codigo_alfa . '-' . $codigo_numerico;
                        }

                        $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca ='$raca_id'");
                        $num_rows_raca = mysqli_num_rows($tab_raca);

                        if ($num_rows_raca!=0){
                            $reg = mysqli_fetch_object($tab_raca);
                            $descricao_raca = utf8_encode($reg->tab_descricao_raca);
                        }
                        else{
                            $descricao_raca = '';
                        }

                        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
                        $data_baixa = $reg_animal->tbl_animal_baixado_em;

                        if ($data_baixa!='') {
                            $data_acompanhamento_calculo = date($data_baixa);
                        }
                        else {
                            $data_acompanhamento_calculo = date("Y-m-d");
                        }

                        $date = new DateTime($data_nascimento);

                        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade_acompanhamento_mostra_dia = $idade_acompanhamento->format('%d');

                        $idade_ano = $idade_acompanhamento->format('%Y');
                        $idade_mes = $idade_acompanhamento->format('%m');
                        $idade_dia = $idade_acompanhamento->format('%d');

                        if ($idade_ano==0 && $idade_mes!=0) {
                            $idade_animal = $idade_mes . ' mes(es)';
                        }
                        else if ($idade_ano!=0 && $idade_mes==0){
                            $idade_animal = $idade_ano . ' ano(s)';
                        }
                        else if ($idade_ano!=0 && $idade_mes!=0) {
                            $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
                        }
                        else if ($idade_ano==0 && $idade_mes==0){
                            $idade_animal = $idade_dia . ' dia(s)';
                        }
                        else {
                            $idade_animal = '';
                        }

                        $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
                        $data_nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);

                        $linha++;

                        $celulas = 'D'.$linha.':E'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $celulas = 'G'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $celulas = 'I'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $celulas = 'J'.$linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        if ($data_nascimento_edi!='') {
                            $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                        }

                        if ($peso_desmama!='' && $peso_desmama!=0) {
                            $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                        }
                        else {
                            $peso_desmama='';
                        }

                        if ($gemelar==0) {
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_estacao_anterior);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $nome_fazenda_anterior);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $numero_coberturas);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $diagnostico_anterior);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_pai_anterior);
                            $gemelar++;
                        }

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $data_nascimento_edi);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $codigo_animal_edi);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $sexo);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $peso_desmama);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $descricao_raca);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $idade_animal);

                        if ($situacao!='') {
                            $celulas = 'G'.$linha.':L'.$linha;

                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }

                        if ($controle=='M' && $desc_diagnostico=='Aguardando Diagnóstico') {
                            $numero_coberturas--;
                        }
                    }
                }
                else {
                    if ($nascido_anterior=='M') {
                        $codigo_animal_edi='Natimorto';
                    }
                    else if ($nascido_anterior=='A') {
                        $codigo_animal_edi='Aborto';
                    }
                    else if ($situacao_femea_anterior=='M'){
                        $codigo_animal_edi='F Morreu';
                    }
                    else if ($situacao_femea_anterior=='V'){
                        $codigo_animal_edi='F Vendida';
                    }
                    else {
                        $codigo_animal_edi='';
                    }

                    // data da movimentacao na tabela de estoque

                    $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_movimentacao_estoque 
                        WHERE tbl_mov_estoque_cobertura_numero_id='$cobertura_anterior' AND
                              tbl_mov_estoque_cobertura_numero_item='$item_anterior'"); 

                    $num_rows_estoque = mysqli_num_rows($tbl_estoque);

                    if ($num_rows_estoque!=0) {
                        $reg_estoque = mysqli_fetch_object($tbl_estoque);
                        $data = new DateTime($reg_estoque->tbl_mov_estoque_nascimento);
                        $data_nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);
                         //$data_nascimento_edi = $data->format('d/m/Y');
                        $sexo = $reg_estoque->tbl_mov_estoque_sexo;
                    }
                    else {
                        $data_nascimento_edi = '';
                        $sexo = '';
                    }
                    
                    $descricao_raca = '';
                    $idade_animal = '';
                    $peso_desmama_edi = '';
                    $peso_desmama = '';

                $linha++;

                $celulas = 'D'.$linha.':E'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'G'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'I'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'J'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                if ($data_nascimento_edi!='') {
                    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                }

                if ($peso_desmama!='' && $peso_desmama!=0) {
                    $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                }
                else {
                    $peso_desmama='';
                }

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_estacao_anterior);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $nome_fazenda_anterior);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $numero_coberturas);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $diagnostico_anterior);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_pai_anterior);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $data_nascimento_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $codigo_animal_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $sexo);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $peso_desmama);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $descricao_raca);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $idade_animal);
                }

                if ($controle=='M' && $desc_diagnostico=='Aguardando Diagnóstico') {
                    $numero_coberturas--;
                }
                // fim dados nascimento do bezzero                


                $estacao_anterior=$estacao;
                $numero_coberturas = 1;
                $diagnostico_anterior = $desc_diagnostico;
                $desc_estacao_anterior = $desc_estacao;
                $nome_fazenda_anterior = $nome_fazenda;
                $descricao_pai_anterior = $descricao_pai;
                $nascido_anterior = $nascido;
                $situacao_femea_anterior = $situacao_femea;
                $cobertura_anterior = $cobertura;
                $item_anterior = $item;
            }
            else {
                $estacao_anterior=$estacao;
                $numero_coberturas++;
                $diagnostico_anterior = $desc_diagnostico;
                $desc_estacao_anterior = $desc_estacao;
                $nome_fazenda_anterior = $nome_fazenda;
                $descricao_pai_anterior = $descricao_pai;
                $nascido_anterior = $nascido;
                $situacao_femea_anterior = $situacao_femea;
                $cobertura_anterior = $cobertura;
                $item_anterior = $item;
            }
        }

        // pega dados do nascimento do bezzero

        $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
            INNER JOIN tbl_pessoa
                    ON tbl_pessoa_id = tbl_animal_codigo_fazenda
            WHERE tbl_animal_codigo_mae='$codigo_animal_id' AND 
                  tbl_animal_estacao_monta_nascimento='$estacao_anterior' 
            ORDER BY tbl_animal_codigo_id DESC"); 

        $num_rows_animais = mysqli_num_rows($tbl_animais);
        $gemelar = 0;

        if ($num_rows_animais!=0) {
            while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                $codigo_numerico = ltrim($reg_animal->tbl_animal_codigo_numerico, "0");
                $ativo = $reg_animal->tbl_animal_ativo;
                $situacao = $reg_animal->tbl_animal_situacao;
                $raca_id = $reg_animal->tbl_animal_codigo_raca;
                $sexo = $reg_animal->tbl_animal_sexo;
                $peso_desmama = $reg_animal->tbl_animal_peso_desmama;
                $peso_nasc = $reg_animal->tbl_animal_primeiro_peso;
                $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                $data_final = $reg_animal->tbl_animal_data_desmama;

                $peso_desmama = calcular_peso_desmama($peso_desmama, $peso_nasc, $data_inicial, $data_final);

                if ($codigo_alfa=='') {
                    $codigo_animal_edi = $codigo_numerico;            
                }
                else {
                    $codigo_animal_edi = $codigo_alfa . '-' . $codigo_numerico;
                }

                $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca ='$raca_id'");
                $num_rows_raca = mysqli_num_rows($tab_raca);

                if ($num_rows_raca!=0){
                    $reg = mysqli_fetch_object($tab_raca);
                    $descricao_raca = utf8_encode($reg->tab_descricao_raca);
                }
                else{
                    $descricao_raca = '';
                }

                $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
                $data_baixa = $reg_animal->tbl_animal_baixado_em;

                if ($data_baixa!='') {
                    $data_acompanhamento_calculo = date($data_baixa);
                }
                else {
                    $data_acompanhamento_calculo = date("Y-m-d");
                }

                $date = new DateTime($data_nascimento);
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade_acompanhamento_mostra_dia = $idade_acompanhamento->format('%d');

                $idade_ano = $idade_acompanhamento->format('%Y');
                $idade_mes = $idade_acompanhamento->format('%m');
                $idade_dia = $idade_acompanhamento->format('%d');

                if ($idade_ano==0 && $idade_mes!=0) {
                    $idade_animal = $idade_mes . ' mes(es)';
                }
                else if ($idade_ano!=0 && $idade_mes==0){
                    $idade_animal = $idade_ano . ' ano(s)';
                }
                else if ($idade_ano!=0 && $idade_mes!=0) {
                    $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
                }
                else if ($idade_ano==0 && $idade_mes==0){
                    $idade_animal = $idade_dia . ' dia(s)';
                }
                else {
                    $idade_animal = '';
                }

                $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
                $data_nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);

                $linha++;

                $celulas = 'D'.$linha.':E'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'G'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'I'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'J'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                if ($data_nascimento_edi!='') {
                    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                }

                if ($peso_desmama!='' && $peso_desmama!=0) {
                    $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                }
                else {
                    $peso_desmama='';
                }

                if ($gemelar==0) {
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_estacao_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $nome_fazenda_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $numero_coberturas);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $diagnostico_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_pai_anterior);
                    $gemelar++;
                }

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $data_nascimento_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $codigo_animal_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $sexo);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $peso_desmama);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $descricao_raca);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $idade_animal);

                if ($situacao!='') {
                    $celulas = 'G'.$linha.':L'.$linha;

                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                }
            }
        }
        else {
            if ($nascido_anterior=='M') {
                $codigo_animal_edi='Natimorto';
            }
            else if ($nascido_anterior=='A') {
                $codigo_animal_edi='Aborto';
            }
            else if ($situacao_femea_anterior=='M'){
                $codigo_animal_edi='F Morreu';
            }
            else if ($situacao_femea_anterior=='V'){
                $codigo_animal_edi='F Vendida';
            }
            else {
                $codigo_animal_edi='';
            }

            // data da movimentacao na tabela de estoque

            $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_cobertura_numero_id='$cobertura_anterior' AND 
                      tbl_mov_estoque_cobertura_numero_item='$item_anterior'"); 

            $num_rows_estoque = mysqli_num_rows($tbl_estoque);

            if ($num_rows_estoque!=0) {
                $reg_estoque = mysqli_fetch_object($tbl_estoque);
                $data = new DateTime($reg_estoque->tbl_mov_estoque_nascimento); 
                $data_nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);
                //$data_nascimento_edi = $data->format('d/m/Y');
                $sexo = $reg_estoque->tbl_mov_estoque_sexo;
            }
            else {
                $data_nascimento_edi = '';
                $sexo = '';
            }

            $descricao_raca = '';
            $idade_animal = '';
            $peso_desmama = '';
            $situacao = '';

            $linha++;

            $celulas = 'D'.$linha.':E'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $celulas = 'G'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $celulas = 'I'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $celulas = 'J'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            if ($data_nascimento_edi!='') {
                $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
            }

            if ($peso_desmama!='' && $peso_desmama!=0) {
                $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
            }
            else {
                $peso_desmama='';
            }

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_estacao_anterior);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $nome_fazenda_anterior);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $numero_coberturas);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $diagnostico_anterior);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_pai_anterior);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $data_nascimento_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $codigo_animal_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $sexo);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $peso_desmama);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $descricao_raca);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $idade_animal);

            if ($situacao!='') {
                $celulas = 'G'.$linha.':L'.$linha;

                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
            }

        }
        // fim dados nascimento do bezzero                

    }*/  

    // Pega animais sem estação de monta
    $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
        INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_animal_codigo_fazenda
        INNER JOIN tabela_racas
                ON tbl_animal_codigo_raca = tab_codigo_raca 
        WHERE tbl_animal_codigo_mae='$codigo_animal_id' AND 
              (tbl_animal_estacao_monta_nascimento='' OR tbl_animal_estacao_monta_nascimento is null)
        ORDER BY tbl_animal_codigo_id DESC"); 

    $num_rows_animais = mysqli_num_rows($tbl_animais);

    if ($num_rows_animais!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico = ltrim($reg_animal->tbl_animal_codigo_numerico, "0");
            $ativo = $reg_animal->tbl_animal_ativo;
            $situacao = $reg_animal->tbl_animal_situacao;
            $nome_fazenda = utf8_encode($reg_animal->tbl_pessoa_nome);
            $pai = $reg_animal->tbl_animal_codigo_pai;
            $descricao_raca = utf8_encode($reg_animal->tab_descricao_raca);
            $raca_id = $reg_animal->tbl_animal_codigo_raca;
            $sexo = $reg_animal->tbl_animal_sexo;
            $peso_desmama = $reg_animal->tbl_animal_peso_desmama;
            $peso_nasc = $reg_animal->tbl_animal_primeiro_peso;
            $data_inicial = $reg_animal->tbl_animal_data_nascimento;
            $data_final = $reg_animal->tbl_animal_data_desmama;

            $peso_desmama = calcular_peso_desmama($peso_desmama, $peso_nasc, $data_inicial, $data_final);

            if ($codigo_alfa=='') {
                $codigo_animal_edi = $codigo_numerico;            
            }
            else {
                $codigo_animal_edi = $codigo_alfa . '-' . $codigo_numerico;
            }

            $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca ='$raca_id'");
            $num_rows_raca = mysqli_num_rows($tab_raca);

            if ($num_rows_raca!=0){
                $reg = mysqli_fetch_object($tab_raca);
                $descricao_raca = utf8_encode($reg->tab_descricao_raca);
            }
            else{
                $descricao_raca = '';
            }

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = utf8_encode($reg->tbl_semem_nome);
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . ltrim($reg->tbl_animal_codigo_numerico, "0");
                }
                else {
                    $descricao_pai = '';
                }
            }

            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
            $data_baixa = $reg_animal->tbl_animal_baixado_em;

            if ($data_baixa!='') {
                $data_acompanhamento_calculo = date($data_baixa);
            }
            else {
                $data_acompanhamento_calculo = date("Y-m-d");
            }

            $date = new DateTime($data_nascimento);

            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade_acompanhamento_mostra_dia = $idade_acompanhamento->format('%d');

            $idade_ano = $idade_acompanhamento->format('%Y');
            $idade_mes = $idade_acompanhamento->format('%m');
            $idade_dia = $idade_acompanhamento->format('%d');

            if ($idade_ano==0 && $idade_mes!=0) {
                $idade_animal = $idade_mes . ' mes(es)';
            }
            else if ($idade_ano!=0 && $idade_mes==0){
                $idade_animal = $idade_ano . ' ano(s)';
            }
            else if ($idade_ano!=0 && $idade_mes!=0) {
                $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
            }
            else if ($idade_ano==0 && $idade_mes==0){
                $idade_animal = $idade_dia . ' dia(s)';
            }
            else {
                $idade_animal = '';
            }

            $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
            $data_nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);
            //$data_nascimento_edi = $data->format('d/m/Y');

            if ($situacao!='') {
                $linha++;

                $celulas = 'C'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'E'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'H'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'J'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'K'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                if ($data_nascimento_edi!='') {
                    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                }

                if ($peso_desmama!='' && $peso_desmama!=0) {
                    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                }
                else {
                    $peso_desmama = '';
                }

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'Sem Estação');
                //$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $nome_fazenda);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_pai);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $data_nascimento_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $codigo_animal_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $sexo);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $peso_desmama);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_raca);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $idade_animal);

                $celulas = 'G'.$linha.':K'.$linha;

                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));

            }
            else {
                $linha++;

                $celulas = 'C'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'E'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'H'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'J'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $celulas = 'K'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                if ($data_nascimento_edi!='') {
                    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                }

                if ($peso_desmama!='' && $peso_desmama!=0) {
                    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                }
                else {
                    $peso_desmama = '';
                }

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'Sem Estação');
                //$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $nome_fazenda);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_pai);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $data_nascimento_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $codigo_animal_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $sexo);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $peso_desmama);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_raca);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $idade_animal);
            }
        }
    }

    // Pega abortos sem estação de monta
    $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_movimentacao_estoque 
        INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_mov_estoque_local
        WHERE tbl_mov_estoque_codigo_mae='$codigo_animal_id' AND 
              tbl_mov_estoque_entrada_saida='A' AND 
              (tbl_mov_estoque_cobertura_numero_id='' OR 
               tbl_mov_estoque_cobertura_numero_id is null)              
        ORDER BY tbl_mov_estoque_numero_id DESC"); 

    $num_rows_estoque = mysqli_num_rows($tbl_estoque);

    if ($num_rows_estoque!=0) {
        while ($reg_estoque = mysqli_fetch_object($tbl_estoque)) {
            $nome_fazenda = utf8_encode($reg_estoque->tbl_pessoa_nome);
            $tipo_movimentacao = $reg_estoque->tbl_mov_estoque_tipo_movimentacao;

            if ($tipo_movimentacao=='A'){
                $codigo_animal_edi = 'Aborto';            
            }
            else {
                $codigo_animal_edi = 'Absorção';
            }

            $descricao_pai = '';
            $idade_animal = '';
            $descricao_raca = '';

            $data = new DateTime($reg_estoque->tbl_mov_estoque_nascimento); 
            $data_nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);
            //$data_nascimento_edi = $data->format('d/m/Y');

            $linha++;

            $celulas = 'C'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $celulas = 'E'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $celulas = 'H'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $celulas = 'J'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $celulas = 'K'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            if ($data_nascimento_edi!='') {
                $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
            }

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'Sem Estação');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $nome_fazenda);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $descricao_pai);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $data_nascimento_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $codigo_animal_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_raca);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $idade_animal);

        }
    }

// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="situacao_reprodutiva_individual.xlsx"');
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