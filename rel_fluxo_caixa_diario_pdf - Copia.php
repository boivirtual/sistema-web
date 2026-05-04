<?php

include "conecta_mysql.inc";

$id_pesagem = $_REQUEST["id_pesagem"];


@ session_start(); 

$cnpj_empresa = $_SESSION['id_cliente'];

$empresa = mysqli_query($conector, "SELECT tbl_empresa_nome FROM tbl_empresa
                                     WHERE tbl_empresa_cpf_cnpj >='$cnpj_empresa'"); 
$registro_empresa = mysqli_fetch_object($empresa);  
$empresa_nome = utf8_decode($registro_empresa->tbl_empresa_nome);

$sql = mysqli_query($conector, "SELECT * from tbl_pesagem
    WHERE tbl_pesagem_id ='$id_pesagem'");
    
$reg_pesagem = mysqli_fetch_object($sql);

$local = $reg_pesagem->tbl_pesagem_codigo_local;
$lote = utf8_decode($reg_pesagem->tbl_pesagem_lote);
$lote = ltrim($lote);
$filtros= utf8_decode($reg_pesagem->tbl_pesagem_filtros);
$animais_pesados = $reg_pesagem->tbl_pesagem_qtd_animais_pesados;
$data_emissao = new DateTime($reg_pesagem->tbl_pesagem_data);
$data_emissao_edi =$data_emissao->format('d/m/Y');

$_SESSION['nome_relatorio']= "Grupo de Pesagem - Novos Pastos";
$_SESSION['filtros']=$filtros;

ob_start ();
define('FPDF_FONTPATH', 'fpdf/font/');
require_once('fpdf/pdf_padrao_retrato.php');
$pdf=new PDF("P","mm","A4");
$pdf->Open();

$liny = 372;
$numero_paginas = 1;
$pagina_atual = 0;
$array_retorno = salta_pagina($pdf, $liny, $numero_paginas, $pagina_atual);   

$pagina_atual=$array_retorno[0];
$liny=$array_retorno[1];

$pdf->SetFont('arial','B',10); 
$pdf->SetXY(5, $liny);
$pdf->Cell(30,4, 'Filtros:',0,0,'L');

$pdf->SetFont('arial','',10); 
$pdf->SetXY(19, $liny);
$pdf->Cell(30,4, $filtros,0,0,'L');
//$pdf->MultiCell(30, 4, utf8_encode($filtros) ,0,'L', false);

$liny=$liny+5;
$pdf->SetFont('arial','B',10); 
$pdf->SetXY(5, $liny);
$pdf->Cell(30,4, 'Descriчуo da Pesagem:',0,0,'L');

$pdf->SetFont('arial','',10); 
$pdf->SetXY(46, $liny);
$pdf->Cell(30,4, $lote,0,0,'L');

$liny=$liny+5;
$pdf->SetFont('arial','B',10); 
$pdf->SetXY(5, $liny);
$pdf->Cell(30,4, 'Animais Pesados:',0,0,'L');

$pdf->SetFont('arial','',10); 
$pdf->SetXY(37, $liny);
$pdf->Cell(30,4, $animais_pesados,0,0,'L');

$pdf->SetFont('arial','B',10); 
$pdf->SetXY(109, $liny);
$pdf->Cell(30,4, 'Data:',0,0,'L');

$pdf->SetFont('arial','',10); 
$pdf->SetXY(120, $liny);
$pdf->Cell(30,4, $data_emissao_edi,0,0,'L');

$liny=$liny+7;
$pdf->SetFont('arial','B',10); 
$pdf->SetXY(5, $liny);
$pdf->Cell(15,6, 'Grupo',1,0,'L');
$pdf->SetXY(20, $liny);
$pdf->Cell(30,6, 'Categoria',1,0,'L');
$pdf->SetXY(50, $liny);
$pdf->Cell(15,6, 'Sexo',1,0,'L');
$pdf->SetXY(65, $liny);
$pdf->Cell(22,6, 'Quantidade',1,0,'L');
$pdf->SetXY(87, $liny);
$pdf->Cell(45,6, 'Pasto Destino',1,0,'L');


$grupo_anterior = 999;
$categoria_anterior = 0;
$desc_categoria_anterior = '';
$sexo_anterior = '';
$total_animais = 0;

$sql = mysqli_query($conector, "SELECT * from tbl_item_pesagem
    INNER JOIN tbl_pesagem 
            ON tbl_pesagem_id = tbl_ite_pesagem_numero_id
    WHERE tbl_ite_pesagem_numero_id ='$id_pesagem'
    ORDER BY tbl_ite_pesagem_grupo_pasto_destino ASC,   
             tbl_ite_pesagem_categoria ASC,
             tbl_ite_pesagem_sexo ASC
              ");

while ($reg_pesagem = mysqli_fetch_object($sql)){
    $item = $reg_pesagem->tbl_ite_pesagem_numero_item;
    $grupo = $reg_pesagem->tbl_ite_pesagem_grupo_pasto_destino;
    $qtd = $reg_pesagem->tbl_ite_pesagem_qtd_animais;
    $pasto_destino = $reg_pesagem->tbl_ite_pesagem_pasto_destino;
    $sexo = $reg_pesagem->tbl_ite_pesagem_sexo;
    $categoria = $reg_pesagem->tbl_ite_pesagem_categoria;

    $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade where tab_codigo_categoria_idade='$categoria'");
    $num_rows = mysqli_num_rows($tbl_categoria);

    if ($num_rows!=0){
        $reg = mysqli_fetch_object($tbl_categoria);
        if ($reg->tab_categoria_idade_ate==999999999) {
            $desc_categoria = '> 36 meses'; 
        }
        else {
            $desc_categoria = $reg->tab_categoria_idade_de . ' a ' . 
                              $reg->tab_categoria_idade_ate . ' meses';
        }
    }
    else {
        $desc_categoria = '';
    }

    $tbl_pasto = mysqli_query($conector, "select * from tbl_pasto 
        where tbl_pasto_id='$pasto_origem' and tbl_pasto_lixeira=0"); 

    $num_rows = mysqli_num_rows($tbl_pasto);

    if ($num_rows!=0) {
        $reg_pasto = mysqli_fetch_object($tbl_pasto);
        $desc_pasto_origem = $reg_pasto->tbl_pasto_descricao;
    }
    else {
        $desc_pasto_origem = '';
    }

    $tbl_pasto = mysqli_query($conector, "select * from tbl_pasto 
        where tbl_pasto_id='$pasto_destino' and tbl_pasto_lixeira=0"); 

    $num_rows = mysqli_num_rows($tbl_pasto);

    if ($num_rows!=0) {
        $reg_pasto = mysqli_fetch_object($tbl_pasto);
        $desc_pasto_destino = $reg_pasto->tbl_pasto_descricao;
    }
    else {
        $desc_pasto_destino = '';
    }

    if ($grupo!=$grupo_anterior) {
        if ($grupo_anterior==999) {
            $grupo_anterior=$grupo;
            $pasto_anterior=$pasto_destino;
            $total_animais = 0;
            $categoria_anterior = 0;
            $sexo_anterior = '';
        }
        else {
            $liny=$liny+6;
            $pdf->SetFont('arial','',10); 
            $pdf->SetXY(5, $liny);
            $pdf->Cell(15,6, $grupo_anterior,1,0,'R');
            $pdf->SetXY(20, $liny);
            $pdf->Cell(30,6, $desc_categoria_anterior,1,0,'L');
            $pdf->SetXY(50, $liny);
            $pdf->Cell(15,6, $sexo_anterior,1,0,'C');
            $pdf->SetXY(65, $liny);
            $pdf->Cell(22,6, $total_animais,1,0,'R');
            $pdf->SetXY(87, $liny);
            $pdf->Cell(45,6, $pasto_anterior_destino,1,0,'L');
                    
            $grupo_anterior=$grupo;
            $pasto_anterior=$pasto_destino;
            $total_animais = 0;
            $categoria_anterior = 0;
            $sexo_anterior = '';
        }
    }

    if ($categoria!=$categoria_anterior) {
        if ($categoria_anterior==0) {
            $total_animais = 0;
            $categoria_anterior = $categoria;
            $desc_categoria_anterior = $desc_categoria;
            $sexo_anterior = '';
        }
        else {
            $liny=$liny+6;
            $pdf->SetFont('arial','',10); 
            $pdf->SetXY(5, $liny);
            $pdf->Cell(15,6, $grupo_anterior,1,0,'R');
            $pdf->SetXY(20, $liny);
            $pdf->Cell(30,6, $desc_categoria_anterior,1,0,'L');
            $pdf->SetXY(50, $liny);
            $pdf->Cell(15,6, $sexo_anterior,1,0,'C');
            $pdf->SetXY(65, $liny);
            $pdf->Cell(22,6, $total_animais,1,0,'R');
            $pdf->SetXY(87, $liny);
            $pdf->Cell(45,6, $pasto_anterior_destino,1,0,'L');

            $total_animais = 0;
            $categoria_anterior = $categoria;
            $desc_categoria_anterior = $desc_categoria;
            $sexo_anterior = '';
        }
    }

    if ($sexo!=$sexo_anterior) {
        if ($sexo_anterior=='') {
            $total_animais = 0;
            $sexo_anterior = $sexo;
        }
        else {
            $liny=$liny+6;
            $pdf->SetFont('arial','',10); 
            $pdf->SetXY(5, $liny);
            $pdf->Cell(15,6, $grupo_anterior,1,0,'R');
            $pdf->SetXY(20, $liny);
            $pdf->Cell(30,6, $desc_categoria_anterior,1,0,'L');
            $pdf->SetXY(50, $liny);
            $pdf->Cell(15,6, $sexo_anterior,1,0,'C');
            $pdf->SetXY(65, $liny);
            $pdf->Cell(22,6, $total_animais,1,0,'R');
            $pdf->SetXY(87, $liny);
            $pdf->Cell(45,6, $pasto_anterior_destino,1,0,'L');

            $total_animais = 0;
            $sexo_anterior = $sexo;
        }
    }
        $total_animais+=$qtd;           
} 

$liny=$liny+6;
$pdf->SetFont('arial','',10); 
$pdf->SetXY(5, $liny);
$pdf->Cell(15,6, $grupo_anterior,1,0,'R');
$pdf->SetXY(20, $liny);
$pdf->Cell(30,6, $desc_categoria_anterior,1,0,'L');
$pdf->SetXY(50, $liny);
$pdf->Cell(15,6, $sexo_anterior,1,0,'C');
$pdf->SetXY(65, $liny);
$pdf->Cell(22,6, $total_animais,1,0,'R');
$pdf->SetXY(87, $liny);
$pdf->Cell(45,6, $pasto_anterior_destino,1,0,'L');


// FIM DO PROCESSAMENTO
$codigo_fornecedorpdf= 'lista_grupo_pesagem.pdf';
ob_clean(); 
$pdf->Output($codigo_fornecedorpdf, "I");

mysqli_close($conector);

function salta_pagina($pdf, $liny, $numero_paginas, $pagina_atual) {

	$pagina_atual++;
	$_SESSION['nome_setor']='Pсgina: ' . $pagina_atual . ' de ' . $numero_paginas;

	$pdf->AddPage();
	$liny=21;
    
	return [$pagina_atual, $liny];
}



?>