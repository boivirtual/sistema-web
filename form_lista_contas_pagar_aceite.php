<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_inicial = $_REQUEST["data_inicial"];
    $data_final = $_REQUEST["data_final"];
    $tipo_data = $_REQUEST["tipo_data"];
    $array_fornecedor = $_REQUEST["array_fornecedor"];
    $array_fazenda = $_REQUEST["array_fazenda"];
    $limpa_filtros = $_REQUEST["limpa_filtros"];

    $array_conta = $_REQUEST["array_conta"];
    $conta = array();
    $matriz_itens = explode(",", $array_conta);
    $quantidade_itens = count($matriz_itens);

    // monta array das contas
    for($i=0; $i < $quantidade_itens; $i++) {

        if (substr($matriz_itens[$i], 3, 4) !=0) {
            $conta[$i]=$matriz_itens[$i];
        }
    }

    $conta = implode(',', $conta);

    $wconta = '';

    if ($array_conta!='') {
        $wconta = " AND ctp_codigo_conta IN(";
        $wconta.= $conta;
        $wconta.= ")";
    }

    $fornecedor= array();
    $matriz_itens = explode(",", $array_fornecedor);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $fornecedor[$i]=$matriz_itens[$i];
    }

    $fornecedor = implode(',', $fornecedor);
    $fornecedor = substr($fornecedor,0, -1);

    $wfornecedor = '';

    if ($array_fornecedor!='') {
        $wfornecedor = " AND ctp_codigo_fornecedor IN(";
        $wfornecedor.= $fornecedor;
        $wfornecedor.= ")";
    }

    $fazenda= array();
    $matriz_itens = explode(",", $array_fazenda);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $fazenda[$i]=$matriz_itens[$i];
    }

    $fazenda = implode(',', $fazenda);
    $fazenda = substr($fazenda,0, -1);

    $wfazenda = '';

    if ($array_fazenda!='') {
        $wfazenda = " AND ctp_codigo_fazenda IN(";
        $wfazenda.= $fazenda;
        $wfazenda.= ")";
    }

    $conta= array();
    $conta_inicial='2000000';
    $conta_final='5999999';
    $tipo_conta = 'A';

    @ session_start(); 

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];

    $_SESSION['data_inicial_aceite']=$data_inicial;
    $_SESSION['data_final_aceite']=$data_final;
    $_SESSION['tipo_data_aceite']=$tipo_data;
    $_SESSION['codigo_fornecedor_aceite']=$array_fornecedor;
    $_SESSION['codigo_local_aceite']=$array_fazenda; 
    $_SESSION['codigo_conta_aceite']=$array_conta; 
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Creative - Bootstrap 3 Responsive Admin Template">
  <meta name="author" content="GeeksLabs">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Fazendas Agrolandes</title>

  <!-- Bootstrap CSS -->

</head>

<body>
	<section class="panel">
        <table class="table table-borderless table-hover" width="100%" 
            style="font-size: 12px" id="tabela_aceite_contas">

        <tbody>
            <?php
                $chave_anterior = '';
                $total_periodo = 0;
                $total_selecionados = 0;

                if ($data_inicial!=0 && $data_final!=0 && 
                    $tipo_data=="V" ){
                    $rs = mysqli_query($conector, "SELECT * FROM contas_pagar
                        INNER JOIN tbl_pessoa
                                ON tbl_pessoa_id=ctp_codigo_fazenda
                        LEFT JOIN tbl_plano_contas
                                ON tbl_plano_contas_codigo_id=ctp_codigo_conta
                        WHERE ctp_aceite='' AND 
                              ctp_data_vencimento>='$data_inicial' AND 
                              ctp_data_vencimento<='$data_final'
                        " . $wfornecedor . $wfazenda . $wconta .
                        "  
                        ORDER BY ctp_codigo_fazenda, ctp_codigo_fornecedor, ctp_data_vencimento, ctp_numero_doc, ctp_parcela ASC");
                }
                else if ($data_inicial!=0 && $data_final!=0 && 
                    $tipo_data=="E" ){
                    $rs = mysqli_query($conector, "SELECT * FROM contas_pagar
                        INNER JOIN tbl_pessoa
                                ON tbl_pessoa_id=ctp_codigo_fazenda
                        LEFT JOIN tbl_plano_contas
                                ON tbl_plano_contas_codigo_id=ctp_codigo_conta
                        WHERE ctp_aceite='' AND 
                              ctp_data_emissao>='$data_inicial' AND 
                              ctp_data_emissao<='$data_final'
                        " . $wfornecedor . $wfazenda . $wconta .
                        "  
                        ORDER BY ctp_codigo_fazenda, ctp_codigo_fornecedor, ctp_data_emissao, ctp_numero_doc, ctp_parcela ASC");
                }
                else {
                    $rs = mysqli_query($conector, "SELECT * FROM contas_pagar
                        INNER JOIN tbl_pessoa
                                ON tbl_pessoa_id=ctp_codigo_fazenda
                        LEFT JOIN tbl_plano_contas
                                ON tbl_plano_contas_codigo_id=ctp_codigo_conta
                        WHERE ctp_aceite=''
                        " . $wfornecedor . $wfazenda . $wconta .
                        "  
                        ORDER BY ctp_codigo_fazenda, ctp_codigo_fornecedor, ctp_data_vencimento, ctp_numero_doc, ctp_parcela ASC");
                }
                                    
                    while ($fila = mysqli_fetch_object($rs)){
                        $ctp_id = $fila->ctp_id;
                        $data_emissao = new DateTime($fila->ctp_data_emissao);
                        $data_vencimento = new DateTime($fila->ctp_data_vencimento);
                        $banco = $fila->ctp_codigo_banco;
                        $numero_cheque = $fila->ctp_numero_cheque;

                        if (empty($fila->ctp_numero_doc)) {
                            $numero_id = $fila->ctp_numero_documento;
                        }
                        else {
                            $numero_id = $fila->ctp_numero_doc;
                        }

                        $parcela = $fila->ctp_parcela;
                        $codigo_for = $fila->ctp_codigo_fornecedor;
                        $nome_for = $fila->ctp_nome_fornecedor;
                        $codigo_fazenda = $fila->ctp_codigo_fazenda;
                        $desc_fazenda = $fila->tbl_pessoa_nome;
                        $codigo_conta = $fila->ctp_codigo_conta;
                        // Se ctp_codigo_conta for NULL é rateio — busca contas da tbl_ctp_rateio
                        if (is_null($codigo_conta)) {
                            $num_doc_esc  = mysqli_real_escape_string($conector, $numero_id);
                            $parcela_esc  = mysqli_real_escape_string($conector, $parcela);
                            $fazenda_esc  = mysqli_real_escape_string($conector, ltrim($fila->ctp_codigo_fazenda, '0'));
                            $rs_rat = mysqli_query($conector,
                                "SELECT rc_nome_conta FROM tbl_ctp_rateio
                                 WHERE rc_codigo_local = '$fazenda_esc'
                                   AND rc_ctp_id IN (
                                       SELECT ctp_id FROM contas_pagar
                                       WHERE ctp_numero_doc = '$num_doc_esc'
                                         AND ctp_parcela    = '$parcela_esc'
                                         AND ctp_codigo_conta IS NULL
                                   )
                                 ORDER BY rc_id ASC");
                            $total_rat = mysqli_num_rows($rs_rat);
                            $first_rat = mysqli_fetch_object($rs_rat);
                            $desc_conta = $first_rat ? $first_rat->rc_nome_conta : 'Rateio';
                            if ($total_rat > 1) {
                                $desc_conta .= ' +' . ($total_rat - 1);
                            }
                        } else {
                            $desc_conta = $fila->tbl_plano_contas_descricao;
                        }
                        $descricao_compra = $fila->ctp_descricao_compra;
                        $situacao = $fila->ctp_situacao;
                        $vlr_parcela = $fila->ctp_valor_parcela;
                        $vlr_juros = $fila->ctp_valor_juros;
                        $vlr_desconto = $fila->ctp_valor_desconto;
                        $vlr_outro = $fila->ctp_outro_valor;
                        $total_parcela = $vlr_parcela - $vlr_desconto + $vlr_juros + $vlr_outro;

                        $total_periodo+=$total_parcela;

                        if ($situacao=="P"){
                            $desc_situacao = "Paga";
                        }
                        else if ($situacao=="C") {
                            $desc_situacao = "Paga Parc";
                        }
                        else {
                            $desc_situacao = "";
                        }

                        $chave_ctp = $codigo_fazenda . $codigo_for . $codigo_conta.str_replace('-', '', $fila->ctp_data_emissao).$numero_id;
                        $registro= $numero_id.$codigo_fazenda.$codigo_for.$codigo_conta.str_replace('-', '', $fila->ctp_data_emissao.$descricao_compra);

                        if ($chave_anterior!=$registro) {
                            echo "<tr>";
                            echo "<td width='2%'>
                                  <input type='checkbox' class='checkbox1' name='id_ctp' value='".$ctp_id."' onClick='somar_total_para_baixar()'>
                                </td>";
                            echo "<td width='10%'>".$numero_id."</td>";
                            echo "<td width='15%'>".$desc_fazenda."</td>";
                            echo "<td width='15%'>".$nome_for."</td>";
                            echo "<td width='10%'>".$desc_conta."</td>";
                            echo "<td width='6%'>".$data_emissao->format('d/m/Y')."</td>";
                            echo "<td width='3%' align='center'>".$parcela."</td>";
                            echo "<td width='6%'>".$data_vencimento->format('d/m/Y')."</td>";
                            echo "<td width='12%'>".number_format($total_parcela, 2, ",", ".")."</td>";
                            echo "<td width='19%' style='font-size: 10px;'>".$descricao_compra."</td>";
                            echo "<td width='10%'>".$desc_situacao."</td>";
                            echo "</tr>";
                            $chave_anterior=$registro;
                        }
                        else {
                            echo "<tr>";
                            echo "<td style='color: #fff;' width='2%'>
                                <input type='checkbox' class='checkbox1' name='id_ctp'  value='".$ctp_id."' onClick='somar_total_para_baixar()'>
                                </td>";
                            echo "<td width='10%'>".$numero_id."</td>";
                            echo "<td style='color: #fff;' width='15%'>".$desc_fazenda."</td>";
                            echo "<td style='color: #fff;' width='15%'>".$nome_for."</td>";
                            echo "<td style='color: #fff;' width='10%'>".$desc_conta."</td>";
                            echo "<td style='color: #fff;' width='6%'>".$data_emissao->format('d/m/Y')."</td>";
                            echo "<td width='3%' align='center'>".$parcela."</td>";
                            echo "<td width='6%'>".$data_vencimento->format('d/m/Y')."</td>";
                            echo "<td width='12%'>".number_format($total_parcela, 2, ",", ".")."</td>";
                            echo "<td></td>";
                            echo "<td width='10%'>".$desc_situacao."</td>";
                            echo "</tr>";
                        }
                    }
            ?>    
        </tbody>

        <thead>
            <tr>
                <div class="row col-md-12" id="total_contas">
                    <div class="col-md-2">
                        <label class="control-label">Total Geral</label>
                        <input class="form-control form-control-sm" type="text" readonly="" <?php echo "value='".number_format($total_periodo, 2, ",", ".")."'";?>>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label">Total Selecionados</label>
                        <input class="form-control form-control-sm" type="text" readonly="" id="total_selecionado">
                    </div>
    
                    <div class="col-md-2">
                        <label class="control-label">&nbsp;</label>
                        <p>
                        <a href="#" onclick="exibe_filtros_aceite()"> 
                            <i class="fas fa-filter"></i> + Filtros
                        </a>
                        </p>
                    </div>

                    <?php 
                        //if ($limpa_filtros!=1) :
                    ?>
                    <div class="col-md-2 limpar_filtros">
                        <label class="control-label">&nbsp;</label>
                        <p>
                        <a href="#" onclick="limpar_filtros_tela_inicial()">Limpar Filtros
                        </a>
                        </p>
                    </div>

                    <?php //endif; ?>

                    <div class="col-md-6"></div>
                </div>
            </tr>

            <tr>
                <th><input type="checkbox" class='checkbox1' id="seleciona_todos_aceite"></th> 
                <th>Documento</th>
                <th>Fazenda</th>
                <th>Fonte Recebedora</th> 
                <th>Conta</th>
                <th>Emissão</th> 
                <th>Parcela</th>
                <th>Vencimentos</th> 
                <th>Valor</th> 
                <th>Descrição</th>
                <th>Pgto</th>
            </tr>
        </thead>
        <tfoot>
        </tfoot>
        </table>
    </section>

    <script src="js/contas_pagar_aceite.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>


                
                
