<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_inicial = $_REQUEST["data_inicial"];
    $data_final = $_REQUEST["data_final"];
    $tipo_data = $_REQUEST["tipo_data"];
    $array_fornecedor = $_REQUEST["array_fornecedor"];
    $array_fazenda = $_REQUEST["array_fazenda"];
    $array_cc = $_REQUEST["array_cc"];

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

    $cc= array();
    $matriz_itens = explode(",", $array_cc);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $cc[$i]=$matriz_itens[$i];
    }

    $cc = implode(',', $cc);
    $cc = substr($cc,0, -1);

    $wcc = '';

    if ($array_cc!='') {
        $wcc = " AND ctp_codigo_centro_custos IN(";
        $wcc.= $cc;
        $wcc.= ")";
    }

    $conta= array();
    $conta_inicial='2000000';
    $conta_final='5999999';

    @ session_start(); 

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];

    $_SESSION['data_inicio_ctp']=$data_inicial;
    $_SESSION['data_fim_ctp']=$data_final;
    $_SESSION['tipo_data_ctp']=$tipo_data;
    $_SESSION['razao_nome_ctp']=$array_fornecedor;
    $_SESSION['lista_ctp']='S'; 
    $_SESSION['codigo_c_custo_ctp']=$array_cc; 
    $_SESSION['codigo_local_ctp']=$array_fazenda; 
    $_SESSION['codigo_conta_ctp']=$array_conta; 

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
	<section class="panel lista_contas">

        <table class="table table-striped table-advance table-hover" id="tabela_contas_pagar" 
         width="100%" style="font-size: 10px">
                          
            <tbody>
                <?php
                    $criterio="";

                    if ($data_inicial!=0 && $data_final!=0){
                        $criterio = 
                        " WHERE bcp_data_pagamento >='$data_inicial' and
                                bcp_data_pagamento <='$data_final'" . 
                                $wfornecedor . $wfazenda . $wcc . $wconta . 
                        " ORDER BY bcp_numero_id ASC, bcp_data_pagamento ASC";
                    }
                    else {
                        $criterio = " WHERE" .
                            $wfornecedor . $wfazenda . $wcc . $wconta .
                            " ORDER BY bcp_numero_id ASC, bcp_data_pagamento ASC";
                    }
                    
                    if ($criterio=='') {
                        mysqli_close($conector);
                        exit;
                    }
                    else {    
                        $ssql = "SELECT * FROM baixa_contas_pagar
                            INNER JOIN contas_pagar
                                    ON bcp_id=ctp_id
                            INNER JOIN tbl_plano_contas
                                    ON tbl_plano_contas_codigo_id=ctp_codigo_conta
                            INNER JOIN tbl_pessoa
                                    ON tbl_pessoa_id=ctp_codigo_fazenda"
                            . $criterio; 

                    $rs = mysqli_query($conector, $ssql); 
                               
                    $total_geral = 0;
                    $total_pagos = 0;
                    $total_pagos_parcial=0;
                    $total_vencidos=0;
                    $total_avencer=0;
                    $data_sistema = date("Y-m-d");

                    $chave_ctp_anterior = 0;

                    while ($registro_ctp = mysqli_fetch_object($rs)){
                        $numero_doc = $registro_ctp->bcp_numero_id;
                        $numero_parcela = $registro_ctp->bcp_parcela;
                        $ctp_id = $registro_ctp->ctp_id;

                        $chave_ctp = $ctp_id . $numero_parcela;

                        $codigo_fornecedor = $registro_ctp->bcp_codigo_fornecedor;
                        $sequencia_pag = $registro_ctp->bcp_sequencia_pagamento;
                        $razao = $registro_ctp->bcp_nome_fornecedor; 
                        $data_pagamento = $registro_ctp->bcp_data_pagamento; 
                        $pagamento_edi = new DateTime($data_pagamento);
                        $valor_pagamento = $registro_ctp->bcp_valor_pagamento; 
                        $situacao = $registro_ctp->ctp_situacao;
                        $agendamento = $registro_ctp->ctp_agendamento;

                        $data_emissao = $registro_ctp->ctp_data_emissao; 
                        $data_vencimento = $registro_ctp->ctp_data_vencimento; 
                        $vlr_parcela = $registro_ctp->ctp_valor_parcela + $registro_ctp->ctp_valor_juros + $registro_ctp->ctp_outro_valor - $registro_ctp->ctp_valor_desconto; 
                        $aceite = $registro_ctp->ctp_aceite;
                        $codigo_forma_pagto = $registro_ctp->ctp_conta_pagamento;
                        $doc_parcela = str_pad($numero_doc, 9, "0", STR_PAD_LEFT);
                        $emissao_edi = new DateTime($data_emissao);
                        $vencimento_edi = new DateTime($data_vencimento);

                        /*$pagamentos = "select * from baixa_contas_pagar
                            where bcp_id='$ctp_id'";

                        $contas_baixadas = mysqli_query($conector, $pagamentos); 
                        $num_rows_contas = mysqli_num_rows($contas_baixadas);
                        $total_pago=0;

                        if ($num_rows_contas!=0){
                            while ($fila_baixada = mysqli_fetch_object($contas_baixadas)) {
                                $valor_pagamento = $fila_baixada->bcp_valor_pagamento;
                                $total_pago+= $valor_pagamento;
                                $data_pagamento = $fila_baixada->bcp_data_pagamento;
                                $pagamento_edi = new DateTime($data_pagamento);
                            }
                        }   
                        else {
                            $pagamento_edi = "";
                        } */


                        if ($chave_ctp_anterior!=$chave_ctp) {
                            $total_geral+= $vlr_parcela;
                            $chave_ctp_anterior=$chave_ctp;
                        }
                            
                        if ($situacao != "P"){
                            if ($data_vencimento < $data_sistema) {
                                $total_vencidos= $total_vencidos + $vlr_parcela - $valor_pagamento;
                            }
                            else {
                                $total_avencer= $total_avencer + $vlr_parcela - $valor_pagamento;
                            }
                        }

                        $total_pagos+= $valor_pagamento;
                            
                        $desc_conta = $registro_ctp->tbl_plano_contas_descricao;
                        $desc_fazenda = $registro_ctp->tbl_pessoa_nome;

                        $total_a_pagar = $vlr_parcela - $valor_pagamento;

                        echo "<tr>";
                        if ($aceite==""){
                            echo "<td width='2%'></td>";
                            echo "<td width='4%'>";
                            echo "<a  style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$doc_parcela."</a>";
                            echo "</td>";

                            echo "<td width='2%'>";
                            echo "<a style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$numero_parcela."</a>";
                            "</td>";

                            echo "<td width='14%'>";
                            echo "<a style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$desc_fazenda."</a>";
                            "</td>";

                            echo "<td width='15%'>";
                            echo "<a style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$desc_conta."</a>";
                                "</td>";
                            echo "<td width='14%'>";
                            echo "<a  style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$razao."</a>";
                                "</td>";

                            echo "<td width='6%'>";
                            echo "<a  style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$emissao_edi->format('d/m/Y')."</a>";
                            "</td>";

                            echo "<td width='6%'>";
                            echo "<a  style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$vencimento_edi->format('d/m/Y')."</a>";
                            "</td>";

                            echo "<td width='6%'>";
                            echo "<a  style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".number_format($vlr_parcela, 2, ",", ".")."</a>";
                            "</td>";

                            echo "<td  style='color:#ccc' width='6%'>".$pagamento_edi->format('d/m/Y')."</td>";
                            echo "<td  style='color:#ccc' width='6%'>".number_format($valor_pagamento, 2, ",", ".")."</td>";
                            echo "<td width='19%'>";
                            echo "<div class='btn-group'>";
                            echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='right' title='Editar esse registro' ></i></a>";
                            echo "<a class='btn' style='font-size: 11px;' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='right' title='Excluir esse registro' onClick='enviar_lixeira(\"{$ctp_id}\",\"{$doc_parcela}\",\"{$numero_parcela}\",1)' ></i></a>"; 
                            echo "</div>";
                            echo "</td>";
                        }
                        else if ($situacao=="P"){
                            echo "<td width='2%'><i class='btn icon_check' style='color:green' data-toggle='tooltip' data-placement='right' title='Pago' ></i></td>";
                            echo "<td width='4%'>".$doc_parcela."</td>";
                            echo "<td width='2%'>".$numero_parcela."</td>";
                            echo "<td width='14%'>".$desc_fazenda."</td>";
                            echo "<td width='15%'>".$desc_conta."</td>";
                            echo "<td width='14%'>".$razao."</td>";
                            echo "<td width='6%'>".$emissao_edi->format('d/m/Y')."</td>";
                            echo "<td width='6%'>".$vencimento_edi->format('d/m/Y')."</td>";
                            echo "<td width='6%'>".number_format($vlr_parcela, 2, ",", ".")."</td>";
                            if ($pagamento_edi=='') {
                                echo "<td width='6'></td>";
                                echo "<td width='6%'></td>";
                            }
                            else {
                                echo "<td width='6'>".$pagamento_edi->format('d/m/Y')."</td>";
                                echo "<td width='6%'>".number_format($valor_pagamento, 2, ",", ".")."</td>";
                            }
                            if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4) {
                                echo "<td width='19%'>";
                                echo "<div class='btn-group'>";
                                echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                echo "</div>";
                            }
                            else {
                                echo "<td width='19%'>";
                                echo "<div class='btn-group'>";
                                echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                echo "</div>";
                            }
                            echo "</td>";
                        }
                        else {
                            if ($data_vencimento < $data_sistema) {
                                if ($numero_doc!=0 && $numero_doc!='') {
                                    echo "<td width='2%'>
                                        <input type='checkbox' name='id_ctp' class='checkbox2' data-toggle='tooltip' data-placement='right' 
                                               title='Seleciona esse registro para baixar' 
                                             onClick='somar_total_para_baixar()' value='" . $ctp_id . "'></td>";
                                }
                                else {
                                    echo "<td style='color:#B22222' width='2%'></td>";
                                }
                                echo "<td style='color:#B22222' width='4%'>".$doc_parcela."</td>";
                                echo "<td style='color:#B22222'width='2%'>".$numero_parcela."</td>";
                                echo "<td style='color:#B22222'width='14%'>".$desc_fazenda."</td>";
                                echo "<td style='color:#B22222'width='15%'>".$desc_conta."</td>";
                                echo "<td style='color:#B22222'width='14%'>".$razao."</td>";
                                echo "<td style='color:#B22222'width='6%'>".$emissao_edi->format('d/m/Y')."</td>";
                                echo "<td style='color:#B22222'width='6%'>".$vencimento_edi->format('d/m/Y')."</td>";
                                echo "<td style='color:#B22222'width='6%'>".number_format($vlr_parcela, 2, ",", ".")."</td>";

                                if ($situacao=="P" || $situacao=="C"){
                                    echo "<td style='color:#B22222'width='6%'>".$pagamento_edi->format('d/m/Y')."</td>";
                                    echo "<td style='color:#B22222'width='6%'>".number_format($valor_pagamento, 2, ",", ".")."</td>";
                                }
                                else {
                                    echo "<td width='6%'></td>";
                                    echo "<td width='6%'></td>";
                                }

                                if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4) {
                                    echo "<td width='19%'>";
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                    echo "<a class='btn' style='font-size: 11px;' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='enviar_lixeira(\"{$ctp_id}\",\"{$doc_parcela}\",\"{$numero_parcela}\",1)' ></i></a>"; 

                                    echo '<a class="btn" style="font-size: 11px;" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_baixar" 
                                              data-wdoc="'.$numero_doc.'"
                                              data-wparcela="'.$numero_parcela.'"
                                              data-wctpid="'.$ctp_id.'"
                                              data-wvalor="'.$total_a_pagar.'"
                                              data-wvencimento="'.$data_vencimento.'"
                                                  data-wformapag="'.$codigo_forma_pagto.'"
                                                  >
                                              <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                  </a>';

                                    echo "</div>";
                                }
                                else {
                                    echo "<td width='19%'>";
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                    echo '<a class="btn" style="font-size: 11px;" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_baixar" 
                                              data-wdoc="'.$numero_doc.'"
                                              data-wparcela="'.$numero_parcela.'"
                                              data-wctpid="'.$ctp_id.'"
                                              data-wvalor="'.$total_a_pagar.'"
                                              data-wvencimento="'.$data_vencimento.'"
                                              data-wformapag="'.$codigo_forma_pagto.'"
                                                  >
                                              <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                              </a>';
                                    echo "</div>";
                                }
                                echo "</td>";
                            }
                            else {
                                if ($numero_doc!=0 && $numero_doc!='') {
                                    echo "<td width='2%'>
                                        <input type='checkbox' name='id_ctp' class='checkbox2' data-toggle='tooltip' data-placement='right' 
                                               title='Seleciona esse registro para baixar' 
                                             onClick='somar_total_para_baixar()' value='" . $ctp_id . "'></td>";
                                }
                                else {
                                    echo "<td width='2%'></td>";
                                }
                                echo "<td width='4%'>".$doc_parcela."</td>";
                                echo "<td width='2%'>".$numero_parcela."</td>";
                                echo "<td width='14%'>".$desc_fazenda."</td>";
                                echo "<td width='15%'>".$desc_conta."</td>";
                                echo "<td width='14%'>".$razao."</td>";
                                echo "<td width='6%'>".$emissao_edi->format('d/m/Y')."</td>";
                                echo "<td width='6%'>".$vencimento_edi->format('d/m/Y')."</td>";
                                echo "<td width='6%'>".number_format($vlr_parcela, 2, ",", ".")."</td>";

                                if ($situacao=="P" || $situacao=="C"){
                                    echo "<td width='6%'>".$pagamento_edi->format('d/m/Y')."</td>";
                                    echo "<td width='6%'>".number_format($valor_pagamento, 2, ",", ".")."</td>";
                                }
                                else {
                                    echo "<td width='6%'></td>";
                                    echo "<td width='6%'></td>";
                                }

                                if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4) {
                                    echo "<td width='19%'>";
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                    echo "<a class='btn' style='font-size: 11px;' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='enviar_lixeira(\"{$ctp_id}\",\"{$doc_parcela}\",\"{$numero_parcela}\",1)' ></i></a>"; 

                                    echo '<a class="btn" style="font-size: 11px;" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_baixar" 
                                              data-wdoc="'.$numero_doc.'"
                                              data-wparcela="'.$numero_parcela.'"
                                              data-wctpid="'.$ctp_id.'"
                                              data-wvalor="'.$total_a_pagar.'"
                                              data-wvencimento="'.$data_vencimento.'"
                                              data-wformapag="'.$codigo_forma_pagto.'"
                                              >
                                              <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                              </a>';
                                        echo "</div>";
                                }
                                else {
                                    echo "<td width='19%'>";
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                    echo '<a class="btn" style="font-size: 11px;" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_baixar" 
                                              data-wdoc="'.$numero_doc.'"
                                              data-wparcela="'.$numero_parcela.'"
                                              data-wctpid="'.$ctp_id.'"
                                              data-wvalor="'.$total_a_pagar.'"
                                              data-wvencimento="'.$data_vencimento.'"
                                              data-wformapag="'.$codigo_forma_pagto.'"
                                              >
                                              <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                              </a>';
                                    echo "</div>";
                                }
                                echo "</td>";
                            }
                        }
                        echo "</tr>";
                    }         
                }
                mysqli_close($conector);

                echo '
                    <script type="text/javascript">
                        $("#aguardar").modal("hide");
                    </script>
                    ';
                                
                ?>
            </tbody>

            <thead>
                <tr>
                    <div class="row col-md-12" id="total_contas">
                        <div class="form-group col-md-2">
                            <label class="control-label">Total no período</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".number_format($total_geral, 2, ",", ".")."'";?>>
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">Vencidos</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".number_format($total_vencidos, 2, ",", ".")."'";?>>
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">A Vencer</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".number_format($total_avencer, 2, ",", ".")."'";?>>
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">Pagos</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".number_format($total_pagos, 2, ",", ".")."'";?>>
                        </div>

                        <div class="form-group col-md-2"></div>

                    </div>
                </tr>
                <tr>
                    <div class="row col-md-8 confirmar_baixa_selecionados" hidden="">
                        <div class="form-group col-md-3">
                            <button type="button" class="btn btn-primary pull-left" id="baixar_selecionados"
                            onClick="modal_baixar()" >Baixar Selecionados</button>
                        </div>
                    </div>
                </tr>

                <tr>
                    <th>
                        <input type="checkbox" class='checkbox2' id="seleciona_todos_somar" data-toggle="tooltip" data-placement="right" title="Selecione um registro para baixar ou clique aqui para selecionar todos">
                    </th> 
                    <th> Documento</th> 
                    <th> Parcela</th>
                    <th> Local</th>
                    <th> Conta</th>
                    <th> Razão Social/Nome</th>
                    <th> Emissão</th>
                    <th> Vencimento</th>
                    <th> Valor Parcela</th>
                    <th> Pagamento</th>
                    <th> Valor Pago</th>
                    <th> <i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>
        </table>

    </section>

    <script src="js/contas_pagar.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>


                
                
