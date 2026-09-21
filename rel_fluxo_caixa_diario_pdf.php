<?php

include "conecta_mysql.inc";

$mes = $_REQUEST["mes"];
$ano = $_REQUEST["ano"];
$opc_rel = $_REQUEST["opc_rel"];
$forma_pag = $_REQUEST["forma_pag"];

$data_inicial = $ano . '-' . $mes . '-01';

$array_mes[1] = 'Janeiro';
$array_mes[2] = 'Fevereiro';
$array_mes[3] = 'Março';
$array_mes[4] = 'Abril';
$array_mes[5] = 'Maio';
$array_mes[6] = 'Junho';
$array_mes[7] = 'Julho';
$array_mes[8] = 'Agosto';
$array_mes[9] = 'Setembro';
$array_mes[10] = 'Outubro';
$array_mes[11] = 'Novembro';
$array_mes[12] = 'Dezembro';

if (substr($mes, 0,1)==0) {
    $ind_mes = substr($mes, 1,1);
}
else {
    $ind_mes = $mes;
}

$liny=375;


@ session_start(); 

$cnpj_empresa = $_SESSION['id_cliente'];

$empresa = mysqli_query($conector, "SELECT tbl_empresa_nome FROM tbl_empresa
                                     WHERE tbl_empresa_cpf_cnpj >='$cnpj_empresa'"); 
$registro_empresa = mysqli_fetch_object($empresa);  
$codigo_fornecedor_empresa = utf8_encode($registro_empresa->tbl_empresa_nome);

if ($forma_pag==0) {
    $desc_forma_pag = 'Todas';
}
else {
    $forma_pagamento = mysqli_query($conector, "select * from tbl_conta_pagamento 
                where tbl_conta_pagamento_id='$forma_pag' and tbl_conta_pagamento_lixeira=0"); 
    $registro_forma_pag = mysqli_fetch_object($forma_pagamento);
    $desc_forma_pag = $registro_forma_pag->tbl_conta_pagamento_descricao;
}

if ($opc_rel==1) {
    $desc_opc_rel = 'Realizados/Não Realizados';
}
else if ($opc_rel==2) {
    $desc_opc_rel = 'Realizados';
}
else {
    $desc_opc_rel = 'Não Realizados';
} 

$filtros = 'Filtros: Data ' . $array_mes[$ind_mes] .'/'. $ano . ' - Tipo Rel: ' .  $desc_opc_rel . ' - Forma Rec/Pag: ' . $desc_forma_pag;
$mes_ano = 'Data: '. $array_mes[$ind_mes] .'/'. $ano . ' - Forma Recebimento/Pagamento: ' . $desc_forma_pag;

$numero_paginas = 1;
$pagina_atual = 0;

$_SESSION['nome_relatorio']= "Fluxo de Caixa Diário" . ' - ' . $desc_opc_rel;
$_SESSION['filtros']=$filtros;

ob_start ();
define('FPDF_FONTPATH', 'fpdf/font/');
require_once('fpdf/pdf_padrao_retrato.php');
$pdf=new PDF("P","mm","A4");
$pdf->Open();

if ($opc_rel==1){
    // Realizado e Nao Realizado    
} // fim opcrel = 1
else if ($opc_rel==2) {
    // Realizado
    //apurar saldo anterior realizado
    $total_saldo_anterior=0;
    $total_recebido=0;
    $total_pago=0;
    $total_geral_recebido=0;
    $total_geral_pago=0;
    $total_geral_mes=0;
    $total_geral_final=0;

    if ($forma_pag==0) {
        $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                                      WHERE bcr_data_pagamento<'$data_inicial'"); 
    }
    else {
        $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                                        INNER JOIN contas_receber
                                                ON bcr_id=ctr_id
                                            WHERE bcr_data_pagamento<'$data_inicial' AND 
                                                  ctr_codigo_forma_recebimento='$forma_pag'"); 
    }
    $num_rows_contas_rec = mysqli_num_rows($contas_rec);

    if ($num_rows_contas_rec!=0){
        while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){
            $valor_pago = $registro_contas_rec->bcr_valor_pagamento;
            $total_recebido+=$valor_pago;
        } 
    }
    if ($forma_pag==0) {
        $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                                          WHERE bcp_data_pagamento<'$data_inicial'"); 
    }
    else {
        $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                                         INNER JOIN contas_pagar
                                                 ON bcp_numero_id=ctp_numero_doc AND 
                                                    bcp_parcela=ctp_parcela AND 
                                                    bcp_codigo_fornecedor=ctp_codigo_fornecedor
                                              WHERE bcp_data_pagamento<'$data_inicial' AND 
                                                    ctp_conta_pagamento='$forma_pag'"); 
    }
    $num_rows_contas_pag = mysqli_num_rows($contas_pag);

    if ($num_rows_contas_pag!=0){
        while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
            $valor_pago = $registro_contas_pag->bcp_valor_pagamento;
            $total_pago+=$valor_pago;
        } 
    }
                        
    $total_saldo_anterior+= $total_recebido - $total_pago;
    $total_saldo = $total_saldo_anterior;

    //fim apurar saldo anterior realizado

    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $data_lista = date("Y-m-d", strtotime('-1 day',strtotime($data_inicial)));

    for ($i=1; $i <= $dias_mes ; $i++) { 
        $data_dia[$i] = 0;
        $valor_recebimentos_diario[$i] = 0;
        $valor_pagamentos_diario[$i] = 0;
        $valor_saldo_diario[$i] = 0;
    }

    for ($i=0; $i < $dias_mes ; $i++) { 
        $total_recebido = 0;
        $total_pago = 0;

        $data_lista = date("Y-m-d", strtotime('+1 day',strtotime($data_lista)));
        $data_edi = new DateTime($data_lista);

        if ($forma_pag==0) {
            $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                WHERE bcr_data_pagamento='$data_lista'"); 
        }
        else {
            $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                INNER JOIN contas_receber
                ON bcr_id=ctr_id
                WHERE bcr_data_pagamento='$data_lista' AND 
                  ctr_codigo_forma_recebimento='$forma_pag'"); 
        }

        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

        if ($num_rows_contas_rec!=0){
            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                $valor_pago = $registro_contas_rec->bcr_valor_pagamento;
                $total_recebido+=$valor_pago;
                $total_geral_recebido+=$valor_pago;
            } 
        }

        if ($forma_pag==0) {
            $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                                               WHERE bcp_data_pagamento='$data_lista'"); 
        }
        else {
            $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                                             INNER JOIN contas_pagar
                                                     ON bcp_numero_id=ctp_numero_doc AND 
                                                        bcp_parcela=ctp_parcela AND 
                                                        bcp_codigo_fornecedor=ctp_codigo_fornecedor
                                                  WHERE bcp_data_pagamento='$data_lista' AND 
                                                        ctp_conta_pagamento='$forma_pag'"); 
        }
        $num_rows_contas_pag = mysqli_num_rows($contas_pag);

        if ($num_rows_contas_pag!=0){
            while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
                $valor_pago = $registro_contas_pag->bcp_valor_pagamento;
                $total_pago+=$valor_pago;
                $total_geral_pago+=$valor_pago;
            } 
        }
                        
        $total_saldo+= $total_recebido - $total_pago;

        $total_geral_mes+=$total_recebido - $total_pago;
        $total_geral_final=$total_saldo;
                       
        $dia = (int)substr($data_lista,8,2);
        $data_dia[$dia] = $data_edi;
        $valor_recebimentos_diario[$dia] = $total_recebido;
        $valor_pagamentos_diario[$dia] = $total_pago;
        $valor_saldo_diario[$dia] = $total_saldo;
    }
    
    if ($liny>372) {
		$array_retorno = salta_pagina($pdf, $liny, $numero_paginas, $pagina_atual, $mes_ano);	

		$pagina_atual=$array_retorno[0];
		$liny=$array_retorno[1];
    }

	$pdf->SetFont('arial','B',12); 
	$pdf->SetXY(5, $liny);
	$pdf->Cell(30,4, 'Saldo Anterior',0,0,'L');
    if ($total_saldo_anterior<0) {
        $pdf->SetTextColor(210, 0, 0);
    }
    else if ($total_saldo_anterior>0) {
        $pdf->SetTextColor(0, 128, 0);
    }
    else {
        $pdf->SetTextColor(0, 0, 0);
    }    

	$pdf->SetXY(95, $liny);
	$pdf->Cell(30,4, number_format($total_saldo_anterior,2,',','.'),0,0,'R');
	$liny=$liny+5;
	$pdf->SetXY(05, $liny);
	
	$pdf->SetDrawColor(192,192,192);

    $pdf->Cell(200,0,'',1,0,'C');
	$liny=$liny+2;

	$pdf->SetFont('arial','B',12); 
    $pdf->SetTextColor(0, 0, 0);
	$pdf->SetXY(5, $liny);
	$pdf->Cell(30,4, 'Saldo do Mês',0,0,'L');
    
    $pdf->SetTextColor(0, 128, 0);
	$pdf->SetXY(35, $liny);
	$pdf->Cell(30,4, number_format($total_geral_recebido,2,',','.'),0,0,'R');
    $pdf->SetTextColor(210, 0, 0);
	$pdf->SetXY(65, $liny);
	$pdf->Cell(30,4, number_format($total_geral_pago,2,',','.'),0,0,'R');

    if ($total_geral_mes<0) {
        $pdf->SetTextColor(210, 0, 0);
    }
    else if ($total_geral_mes>0) {
        $pdf->SetTextColor(0, 128, 0);
    }
    else {
        $pdf->SetTextColor(0, 0, 0);
    }    
	$pdf->SetXY(95, $liny);
	$pdf->Cell(30,4, number_format($total_geral_mes,2,',','.'),0,0,'R');
	$liny=$liny+5;
	$pdf->SetXY(05, $liny);
	
	$pdf->SetDrawColor(192,192,192);

    $pdf->Cell(200,0,'',1,0,'C');
	$liny=$liny+2;

	$pdf->SetFont('arial','B',12); 
    $pdf->SetTextColor(0, 0, 0);
	$pdf->SetXY(5, $liny);
	$pdf->Cell(30,4, 'Saldo Final',0,0,'L');
    if ($total_geral_final<0) {
        $pdf->SetTextColor(210, 0, 0);
    }
    else if ($total_geral_final>0) {
        $pdf->SetTextColor(0, 128, 0);
    }
    else {
        $pdf->SetTextColor(0, 0, 0);
    }    

	$pdf->SetXY(95, $liny);
	$pdf->Cell(30,4, number_format($total_geral_final,2,',','.'),0,0,'R');
	$liny=$liny+5;
	$pdf->SetXY(05, $liny);
	
	$pdf->SetDrawColor(192,192,192);

    $pdf->Cell(200,0,'',1,0,'C');
	$liny=$liny+2;

    for ($i=1; $i <= $dias_mes ; $i++) { 
        $pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('arial','',12); 
		$pdf->SetXY(5, $liny);
		$pdf->Cell(30,4, $data_dia[$i]->format('d/m/Y'),0,0,'L');
		$pdf->SetXY(35, $liny);
		$pdf->Cell(30,4, number_format($valor_recebimentos_diario[$i],2,',','.'),0,0,'R');
		$pdf->SetXY(65, $liny);
		$pdf->Cell(30,4, number_format($valor_pagamentos_diario[$i],2,',','.'),0,0,'R');
        if ($valor_saldo_diario[$i]<0) {
            $pdf->SetTextColor(210, 0, 0);
        }
        else if ($valor_saldo_diario[$i]>0) {
            $pdf->SetTextColor(0, 128, 0);
        }
        else {
            $pdf->SetTextColor(0, 0, 0);
        }    

		$pdf->SetXY(95, $liny);
		$pdf->Cell(30,4, number_format($valor_saldo_diario[$i],2,',','.'),0,0,'R');
		$liny=$liny+5;
		$pdf->SetXY(05, $liny);
		
		$pdf->SetDrawColor(192,192,192);

	    $pdf->Cell(200,0,'',1,0,'C');
		$liny=$liny+2;
	}
    $pdf->SetTextColor(0, 0, 0);

} // fim opcrel = 2
else {
    // Nao Realizado
    //apurar saldo anterior nao realizado
    $total_saldo_anterior=0;
    $total_recebido=0;
    $total_pago=0;
    $total_geral_recebido=0;
    $total_geral_pago=0;
    $total_geral_mes=0;
    $total_geral_final=0;
    if ($forma_pag==0) {
        $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                             WHERE ctr_data_vencimento<'$data_inicial' AND 
                                                   ctr_situacao=''"); 
    }
    else {
        $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                           WHERE ctr_data_vencimento<'$data_inicial' AND 
                                                  ctr_situacao='' AND 
                                                  ctr_codigo_forma_recebimento='$forma_pag'"); 
    }
    $num_rows_contas_rec = mysqli_num_rows($contas_rec);

    if ($num_rows_contas_rec!=0){
        while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){
            $valor_parcela = $registro_contas_rec->ctr_valor_parcela;
            $valor_desconto = $registro_contas_rec->ctr_valor_desconto;
            $valor_juros = $registro_contas_rec->ctr_valor_juros;
            $valor_outro = $registro_contas_rec->ctr_valor_acrescimo;
            $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;
            $total_recebido+=$vlr_pagamento;
        } 
    }

    if ($forma_pag==0) {
        $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                           WHERE ctp_data_vencimento<'$data_inicial' AND 
                                                 ctp_situacao=''"); 
    }
    else {
        $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                              WHERE ctp_data_vencimento<'$data_inicial' AND 
                                                    ctp_situacao='' AND 
                                                    ctp_conta_pagamento='$forma_pag'"); 
    }
    $num_rows_contas_pag = mysqli_num_rows($contas_pag);

    if ($num_rows_contas_pag!=0){
        while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
            $valor_parcela = $registro_contas_pag->ctp_valor_parcela;
            $valor_desconto = $registro_contas_pag->ctp_valor_desconto;
            $valor_juros = $registro_contas_pag->ctp_valor_juros;
            $valor_outro = $registro_contas_pag->ctp_outro_valor;
            $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;
                            $total_pago+=$vlr_pagamento;
        } 
    }
                        
    $total_saldo_anterior+= $total_recebido - $total_pago;
    $total_saldo = $total_saldo_anterior;

    //fim apurar saldo anterior realizado

    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $data_lista = date("Y-m-d", strtotime('-1 day',strtotime($data_inicial)));

    for ($i=1; $i <= $dias_mes ; $i++) { 
        $data_dia[$i] = 0;
        $valor_recebimentos_diario[$i] = 0;
        $valor_pagamentos_diario[$i] = 0;
        $valor_saldo_diario[$i] = 0;
    }

    for ($i=0; $i < $dias_mes ; $i++) { 
        $total_recebido = 0;
        $total_pago = 0;
        $data_lista = date("Y-m-d", strtotime('+1 day',strtotime($data_lista)));
        $data_edi = new DateTime($data_lista);

        if ($forma_pag==0) {
            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                                 WHERE ctr_data_vencimento='$data_lista' AND 
                                                       ctr_situacao=''"); 
        }
        else {
            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                          WHERE ctr_data_vencimento='$data_lista' AND 
                                                      ctr_situacao='' AND 
                                                      ctr_codigo_forma_recebimento='$forma_pag'"); 
        }
        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

        if ($num_rows_contas_rec!=0){
            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                $valor_parcela = $registro_contas_rec->ctr_valor_parcela;
                $valor_desconto = $registro_contas_rec->ctr_valor_desconto;
                $valor_juros = $registro_contas_rec->ctr_valor_juros;
                $valor_outro = $registro_contas_rec->ctr_valor_acrescimo;
                $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;
                $total_recebido+=$vlr_pagamento;
                $total_geral_recebido+=$vlr_pagamento;
            } 
        }

        if ($forma_pag==0) {
            $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                               WHERE ctp_data_vencimento='$data_lista' AND 
                                                     ctp_situacao=''"); 
        }
        else {
            $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                                 WHERE ctp_data_vencimento='$data_lista' AND 
                                                        ctp_situacao='' AND 
                                                       ctp_conta_pagamento='$forma_pag'"); 
        }

        $num_rows_contas_pag = mysqli_num_rows($contas_pag);

        if ($num_rows_contas_pag!=0){
            while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
                $valor_parcela = $registro_contas_pag->ctp_valor_parcela;
                $valor_desconto = $registro_contas_pag->ctp_valor_desconto;
                $valor_juros = $registro_contas_pag->ctp_valor_juros;
                $valor_outro = $registro_contas_pag->ctp_outro_valor;
                $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;
                $total_pago+=$vlr_pagamento;
                $total_geral_pago+=$vlr_pagamento;
            } 
        }
                        
        $total_saldo+= $total_recebido - $total_pago;
        $total_geral_mes+=$total_recebido - $total_pago;
        $total_geral_final=$total_saldo;
                        
        $dia = (int)substr($data_lista,8,2);
        $data_dia[$dia] = $data_edi;
        $valor_recebimentos_diario[$dia] = $total_recebido;
        $valor_pagamentos_diario[$dia] = $total_pago;
        $valor_saldo_diario[$dia] = $total_saldo;
    }

    if ($liny>372) {
        $array_retorno = salta_pagina($pdf, $liny, $numero_paginas, $pagina_atual, $mes_ano);   

        $pagina_atual=$array_retorno[0];
        $liny=$array_retorno[1];
    }

    $pdf->SetFont('arial','B',12); 
    $pdf->SetXY(5, $liny);
    $pdf->Cell(30,4, 'Saldo Anterior',0,0,'L');
    if ($total_saldo_anterior<0) {
        $pdf->SetTextColor(210, 0, 0);
    }
    else if ($total_saldo_anterior>0) {
        $pdf->SetTextColor(0, 128, 0);
    }
    else {
        $pdf->SetTextColor(0, 0, 0);
    }    

    $pdf->SetXY(95, $liny);
    $pdf->Cell(30,4, number_format($total_saldo_anterior,2,',','.'),0,0,'R');
    $liny=$liny+5;
    $pdf->SetXY(05, $liny);
    
    $pdf->SetDrawColor(192,192,192);

    $pdf->Cell(200,0,'',1,0,'C');
    $liny=$liny+2;

    $pdf->SetFont('arial','B',12); 
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(5, $liny);
    $pdf->Cell(30,4, 'Saldo do Mês',0,0,'L');
    
    $pdf->SetTextColor(0, 128, 0);
    $pdf->SetXY(35, $liny);
    $pdf->Cell(30,4, number_format($total_geral_recebido,2,',','.'),0,0,'R');
    $pdf->SetTextColor(210, 0, 0);
    $pdf->SetXY(65, $liny);
    $pdf->Cell(30,4, number_format($total_geral_pago,2,',','.'),0,0,'R');

    if ($total_geral_mes<0) {
        $pdf->SetTextColor(210, 0, 0);
    }
    else if ($total_geral_mes>0) {
        $pdf->SetTextColor(0, 128, 0);
    }
    else {
        $pdf->SetTextColor(0, 0, 0);
    }    
    $pdf->SetXY(95, $liny);
    $pdf->Cell(30,4, number_format($total_geral_mes,2,',','.'),0,0,'R');
    $liny=$liny+5;
    $pdf->SetXY(05, $liny);
    
    $pdf->SetDrawColor(192,192,192);

    $pdf->Cell(200,0,'',1,0,'C');
    $liny=$liny+2;

    $pdf->SetFont('arial','B',12); 
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(5, $liny);
    $pdf->Cell(30,4, 'Saldo Final',0,0,'L');
    if ($total_geral_final<0) {
        $pdf->SetTextColor(210, 0, 0);
    }
    else if ($total_geral_final>0) {
        $pdf->SetTextColor(0, 128, 0);
    }
    else {
        $pdf->SetTextColor(0, 0, 0);
    }    

    $pdf->SetXY(95, $liny);
    $pdf->Cell(30,4, number_format($total_geral_final,2,',','.'),0,0,'R');
    $liny=$liny+5;
    $pdf->SetXY(05, $liny);
    
    $pdf->SetDrawColor(192,192,192);

    $pdf->Cell(200,0,'',1,0,'C');
    $liny=$liny+2;

    for ($i=1; $i <= $dias_mes ; $i++) { 
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('arial','',12); 
        $pdf->SetXY(5, $liny);
        $pdf->Cell(30,4, $data_dia[$i]->format('d/m/Y'),0,0,'L');
        $pdf->SetXY(35, $liny);
        $pdf->Cell(30,4, number_format($valor_recebimentos_diario[$i],2,',','.'),0,0,'R');
        $pdf->SetXY(65, $liny);
        $pdf->Cell(30,4, number_format($valor_pagamentos_diario[$i],2,',','.'),0,0,'R');
        if ($valor_saldo_diario[$i]<0) {
            $pdf->SetTextColor(210, 0, 0);
        }
        else if ($valor_saldo_diario[$i]>0) {
            $pdf->SetTextColor(0, 128, 0);
        }
        else {
            $pdf->SetTextColor(0, 0, 0);
        }    

        $pdf->SetXY(95, $liny);
        $pdf->Cell(30,4, number_format($valor_saldo_diario[$i],2,',','.'),0,0,'R');
        $liny=$liny+5;
        $pdf->SetXY(05, $liny);
        
        $pdf->SetDrawColor(192,192,192);

        $pdf->Cell(200,0,'',1,0,'C');
        $liny=$liny+2;
    }
    $pdf->SetTextColor(0, 0, 0);
} // fim opcrel = 3

// FIM DO PROCESSAMENTO
$codigo_fornecedorpdf= 'fluxo_caixa_diario.pdf';
ob_clean(); 
$pdf->Output($codigo_fornecedorpdf, "I");

mysqli_close($conector);

function salta_pagina($pdf, $liny, $numero_paginas, $pagina_atual, $mes_ano) {

	$pagina_atual++;
	$_SESSION['nome_setor']='Página: ' . $pagina_atual . ' de ' . $numero_paginas;

	$pdf->AddPage();
	$liny=21;
    
	$pdf->SetFont('arial','',12); 
	$pdf->SetXY(5, $liny);
	$pdf->Cell(35,4, $mes_ano ,0,0,'L');

/*
	$liny=$liny+4;
	$pdf->SetXY(5, $liny);
	$pdf->Cell(60,4, 'Centro de Custos: ' . $desc_centro_custos ,0,0,'L');

	$liny=$liny+4;

	$pdf->SetXY(5, $liny);
	$pdf->Cell(60,4, 'Período: '.$data_inicio_edi->format('d/m/Y').' até '.$data_fim_edi->format('d/m/Y').
	  ' - Relatório ' . $desc_tipo_rel . $desc_tipo_data,0,0,'L');
*/
	$liny=$liny+5;
	$pdf->SetXY(2, $liny);
	$pdf->Cell(206,0,'',1,0,'L');

	$liny=$liny+4;

	$pdf->SetXY(05, $liny);
	$pdf->Cell(30,4, 'Data',0,0,'L');
	$pdf->SetXY(35, $liny);
	$pdf->Cell(30,4, 'Recebimentos',0,0,'R');
	$pdf->SetXY(65, $liny);
	$pdf->Cell(30,4, 'Pagamentos',0,0,'R');
	$pdf->SetXY(95, $liny);
	$pdf->Cell(30,4, 'Saldo',0,0,'R');

	$liny=$liny+6;

	$pdf->SetXY(05, $liny);
    $pdf->Cell(200,0,'',1,0,'C');
	$liny=$liny+3;

	return [$pagina_atual, $liny];
}



?>