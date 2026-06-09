<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    function tirarAcentos($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
    }


    $data_inicial = $_REQUEST["data_inicial"];
    $data_final = $_REQUEST["data_final"];
    $tipo_data = $_REQUEST["tipo_data"];
    $array_cliente = $_REQUEST["array_cliente"];
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
        $wconta = " AND ctr_codigo_conta IN(";
        $wconta.= $conta;
        $wconta.= ")";
    }

    $cliente= array();
    $matriz_itens = explode(",", $array_cliente);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $cliente[$i]=$matriz_itens[$i];
    }

    $cliente = implode(',', $cliente);
    $cliente = substr($cliente,0, -1);

    $wcliente = '';

    if ($array_cliente!='') {
        $wcliente = " AND ctr_codigo_cliente_fornecedor IN(";
        $wcliente.= $cliente;
        $wcliente.= ")";
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
        $wfazenda = " AND ctr_codigo_fazenda IN(";
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
        $wcc = " AND ctr_codigo_c_custo IN(";
        $wcc.= $cc;
        $wcc.= ")";
    }

    @ session_start(); 
    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];

    $_SESSION['data_inicio_ctr']=$data_inicial;
    $_SESSION['data_fim_ctr']=$data_final;
    $_SESSION['tipo_data_ctr']=$tipo_data;
    $_SESSION['razao_nome_ctr']=$array_cliente;
    $_SESSION['lista_ctr']='S'; 
    $_SESSION['codigo_c_custo_ctr']=$array_cc; 
    $_SESSION['codigo_local_ctr']=$array_fazenda; 
    $_SESSION['codigo_conta_ctr']=$array_conta; 

    $total_geral_parcelas = 0;
    $total_geral_pagos = 0;
    $total_pago=0;
    $data_sistema = date("Y-m-d");

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
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->

</head>

<body>
	<section class="panel lista_contas">

        <table class="table table-striped table-advance table-hover" id="tabela_contas_receber" 
         width="100%" style="font-size: 10px" >
                          
            <tbody>
                <?php
                    $criterio="";

                    if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="V" ){
                        $criterio = 
                        " WHERE ctr_lixeira=0 and 
                                ctr_data_vencimento >='$data_inicial' and
                                ctr_data_vencimento <='$data_final'" . $wcliente . $wfazenda . $wcc . $wconta .
                        " ORDER BY ctr_situacao, ctr_data_vencimento, ctr_numero_doc ASC";
                    }
                    else if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="E"){
                        $criterio = 
                        " WHERE ctr_lixeira=0 and 
                                ctr_data_emissao >='$data_inicial' and
                                ctr_data_emissao <='$data_final'" . $wcliente . $wfazenda . $wcc . $wconta .
                        " ORDER BY ctr_situacao, ctr_data_emissao, ctr_numero_doc ASC";
                    }
                    else if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="P"){
                        $criterio = 
                        " WHERE bcr_data_pagamento >='$data_inicial' and
                                bcr_data_pagamento <='$data_final' " . $wcliente . $wfazenda . $wcc . $wconta .
                        " ORDER BY bcr_situacao, bcr_data_pagamento, bcr_numero_doc ASC";
                    }
                    else if ($data_inicial==0 && $data_final==0){
                        $criterio = " WHERE ctr_lixeira=0" .$wcliente . $wfazenda . $wcc . $wconta .
                        " ORDER BY ctr_situacao, ctr_data_emissao, ctr_numero_doc ASC";
                        }


                    if ($criterio=='') {
                        mysqli_close($conector);
                        exit;
                    }
                    else {    
                        if ($tipo_data=="P"){
                            $ssql = "SELECT * FROM baixa_contas_receber
                                INNER JOIN contas_receber
                                        ON bcr_id=ctr_id
                                INNER JOIN tbl_pessoa
                                        ON tbl_pessoa_id=ctr_codigo_fazenda
                                " . $criterio; 
                        }
                        else {
                            $ssql = "SELECT * FROM contas_receber
                                INNER JOIN tbl_pessoa
                                        ON tbl_pessoa_id=ctr_codigo_fazenda
                                " . $criterio; 
                        }

                        $rs = mysqli_query($conector, $ssql); 
                                
                        $total_geral_parcelas = 0;
                        $total_geral_pagos = 0;
                        $total_aberto = 0;
                        $registros_encontrados = mysqli_num_rows($rs);

                        while ($registro_ctr = mysqli_fetch_object($rs)){
                            if ($tipo_data=="P"){
                                $id_ctr = $registro_ctr->ctr_id;
                                $numero_doc = $registro_ctr->bcr_numero_doc;
                                $numero_parcela = $registro_ctr->bcr_parcela;
                                $razao = tirarAcentos($registro_ctr->ctr_nome_cliente); 
                                $data_pagamento = $registro_ctr->bcr_data_pagamento; 
                                $total_pago = $registro_ctr->bcr_valor_pagamento; 
                                $situacao = $registro_ctr->bcr_situacao;
                                $codigo_forma_pagto = $registro_ctr->ctr_codigo_forma_recebimento;
                                $codigo_conta_pagto = $registro_ctr->ctr_codigo_conta_recebimento;
                                $data_emissao = $registro_ctr->ctr_data_emissao; 
                                $data_vencimento = $registro_ctr->ctr_data_vencimento; 
                                $vlr_parcela = $registro_ctr->ctr_valor_parcela; 
                                $vlr_juros = $registro_ctr->ctr_valor_juros; 
                                $vlr_desconto = $registro_ctr->ctr_valor_desconto; 
                                $vlr_acrescimo = $registro_ctr->ctr_valor_acrescimo; 
                                $vlr_parcela = $vlr_parcela + $vlr_juros + $vlr_acrescimo - $vlr_desconto;

                                $codigo_conta = $registro_ctr->ctr_codigo_conta;

                                $conta = "SELECT * FROM tbl_plano_contas
                                                   WHERE tbl_plano_contas_codigo_id='$codigo_conta'"; 
                                $conta_query = mysqli_query($conector, $conta);
                                $reg_conta = mysqli_fetch_object($conta_query);
                                $desc_conta = substr($reg_conta->tbl_plano_contas_descricao, 0,10);

                                $doc_parcela = $numero_doc . '-' . $numero_parcela;
                                $emissao_edi = new DateTime($data_emissao);
                                $vencimento_edi = new DateTime($data_vencimento);
                                $pagamento_edi = new DateTime($data_pagamento);

                                $total_geral_parcelas+= $vlr_parcela;
                                $total_geral_pagos+= $total_pago;
                            }
                            else {
                                $id_ctr = $registro_ctr->ctr_id;
                                $numero_doc = $registro_ctr->ctr_numero_doc;
                                $numero_parcela = $registro_ctr->ctr_parcela;
                                $razao = tirarAcentos($registro_ctr->ctr_nome_cliente); 
                                $data_emissao = $registro_ctr->ctr_data_emissao; 
                                $data_vencimento = $registro_ctr->ctr_data_vencimento; 
                                $vlr_parcela = $registro_ctr->ctr_valor_parcela; 
                                $vlr_juros = $registro_ctr->ctr_valor_juros; 
                                $vlr_desconto = $registro_ctr->ctr_valor_desconto; 
                                $vlr_acrescimo = $registro_ctr->ctr_valor_acrescimo; 
                                $vlr_parcela = $vlr_parcela + $vlr_juros + $vlr_acrescimo - $vlr_desconto;
                                $codigo_forma_pagto = $registro_ctr->ctr_codigo_forma_recebimento;
                                $codigo_conta_pagto = $registro_ctr->ctr_codigo_conta_recebimento;
                                $situacao = $registro_ctr->ctr_situacao;
                                $codigo_conta = $registro_ctr->ctr_codigo_conta;

                                $conta = "SELECT * FROM tbl_plano_contas
                                                   WHERE tbl_plano_contas_codigo_id='$codigo_conta'"; 
                                $conta_query = mysqli_query($conector, $conta);
                                $reg_conta = mysqli_fetch_object($conta_query);
                                $desc_conta = substr($reg_conta->tbl_plano_contas_descricao, 0,10);

                                $contas_baixadas = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                                    WHERE bcr_id='$id_ctr'"); 

                                $num_rows_contas = mysqli_num_rows($contas_baixadas);
                                $total_pago=0;

                                if ($num_rows_contas!=0){
                                    while ($fila_baixada = mysqli_fetch_object($contas_baixadas)) {
                                        $valor_pagamento = $fila_baixada->bcr_valor_pagamento;
                                        $total_pago = $total_pago + $valor_pagamento;
                                        $data_pagamento = $fila_baixada->bcr_data_pagamento;
                                        $pagamento_edi = new DateTime($data_pagamento);

                                    }
                                }   
                                else {
                                    $pagamento_edi = "";
                                } 

                                $doc_parcela = $numero_doc . '-' . $numero_parcela;
                                $emissao_edi = new DateTime($data_emissao);
                                $vencimento_edi = new DateTime($data_vencimento);

                                $total_geral_parcelas+= $vlr_parcela;
                                $total_geral_pagos+= $total_pago;

                            }

                            $desc_fazenda = $registro_ctr->tbl_pessoa_nome;

                            $total_aberto = $total_geral_parcelas - $total_geral_pagos;

                            echo "<tr>";

                            if ($situacao=="P"){
                                echo "<td width='2%'><i class='btn icon_check' style='color:green' data-toggle='tooltip' data-placement='left' title='Pago' ></i></td>";
                                echo "<td width='10%'>".$doc_parcela."</td>";
                                echo "<td width='10%'>".$desc_fazenda."</td>";
                                echo "<td width='10%'>".$desc_conta."</td>";
                                echo "<td width='8%'>".$vencimento_edi->format('d/m/Y')."</td>";
                                echo "<td width='8%'>".$emissao_edi->format('d/m/Y')."</td>";
                                echo "<td width='17%'>".$razao."</td>";
                                echo "<td width='8%'>".number_format($vlr_parcela, 2, ",", ".")."</td>";
                                if ($pagamento_edi=='') {
                                    echo "<td width='6'></td>";
                                    echo "<td width='6%'></td>";
                                }
                                else {
                                    echo "<td width='6'>".$pagamento_edi->format('d/m/Y')."</td>";
                                    echo "<td width='6%'>".number_format($total_pago, 2, ",", ".")."</td>";
                                }

                                if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4){
                                    echo "<td width='15%'>";
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' href='form_contas_receber_editar.php?id_ctr=".$id_ctr."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                    echo "</div>";
                                    echo "</td>";
                                }   
                                else {
                                    echo "<td width='15%'>";
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' href='form_contas_receber_editar.php?id=".$numero_doc."&parcela=".$numero_parcela."&id_ctr=".$id_ctr."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                    echo "</div>";
                                    echo "</td>";

                                }                              
                            }
                            else {
                                if ($data_vencimento<$data_sistema) {
                                    echo "<td width='2%'>
                                        <input type='checkbox' name='id_ctr' class='checkbox1' data-toggle='tooltip' data-placement='top' 
                                           title='Seleciona esse registro para baixar' 
                                         onClick='somar_total_para_baixar()' value='" . $id_ctr . "'></td>";
                                    echo "<td width='10%' style='color:#B22222'>".$doc_parcela."</td>";
                                    echo "<td style='color:#B22222' width='10%'>".$desc_fazenda."</td>";
                                    echo "<td width='10%' style='color:#B22222'>".$desc_conta."</td>";
                                    echo "<td width='8%' style='color:#B22222'>".$vencimento_edi->format('d/m/Y')."</td>";
                                    echo "<td width='8%' style='color:#B22222'>".$emissao_edi->format('d/m/Y')."</td>";
                                    echo "<td width='17%' style='color:#B22222'>".$razao."</td>";
                                    echo "<td width='8%' style='color:#B22222'>".number_format($vlr_parcela, 2, ",", ".")."</td>";

                                    if ($situacao=="P" || $situacao=="C"){
                                        echo "<td width='6%' style='color:#B22222'>".$pagamento_edi->format('d/m/Y')."</td>";
                                        echo "<td width='6%' style='color:#B22222'>".number_format($total_pago, 2, ",", ".")."</td>";
                                    }
                                    else {
                                        echo "<td width='6%'></td>";
                                        echo "<td width='6%'></td>";
                                    }

                                    if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4){
                                        echo "<td width='15%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='form_contas_receber_editar.php?id_ctr=".$id_ctr."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                        echo "<a class='btn' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='enviar_lixeira(\"{$numero_doc}\",\"{$numero_parcela}\",\"{$id_ctr}\")' ></i></a>"; 

                                        echo '<a class="btn" href="#" 
                                                data-toggle="modal" 
                                                data-target="#modal_baixar" 
                                                data-wid="'.$id_ctr.'"
                                                data-wdoc="'.$numero_doc.'"
                                                data-wparcela="'.$numero_parcela.'"
                                                data-wvalor="'.$vlr_parcela.'"
                                                data-wvencimento="'.$data_vencimento.'"
                                                data-wcontapag="'.$codigo_conta_pagto.'"
                                                >
                                                <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                </a>';
                                        echo "</div>";
                                        echo "</td>";
                                    }
                                    else {
                                        echo "<td width='15%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='form_contas_receber_editar.php?id=".$numero_doc."&parcela=".$numero_parcela."&id_ctr=".$id_ctr."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                        echo '<a class="btn" href="#" 
                                                data-toggle="modal" 
                                                data-target="#modal_baixar" 
                                                data-wid="'.$id_ctr.'"
                                                data-wdoc="'.$numero_doc.'"
                                                data-wparcela="'.$numero_parcela.'"
                                                data-wvalor="'.$vlr_parcela.'"
                                                data-wvencimento="'.$data_vencimento.'"
                                                data-wcontapag="'.$codigo_conta_pagto.'"
                                                >
                                                <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                </a>';
                                        echo "</div>";
                                        echo "</td>";
                                    }
                                }
                                else {
                                    echo "<td width='2%'>
                                        <input type='checkbox' name='id_ctr' class='checkbox1' 
                                         data-toggle='tooltip' data-placement='top'
                                         title='Seleciona esse registro para baixar' 
                                         onClick='somar_total_para_baixar()' value='".$id_ctr."'</td>";
                                    echo "<td width='10%'>".$doc_parcela."</td>";
                                    echo "<td width='10%'>".$desc_fazenda."</td>";
                                    echo "<td width='10%'>".$desc_conta."</td>";
                                    echo "<td width='8%'>".$vencimento_edi->format('d/m/Y')."</td>";
                                    echo "<td width='8%'>".$emissao_edi->format('d/m/Y')."</td>";
                                    echo "<td width='17%'>".$razao."</td>";
                                    echo "<td width='8%'>".number_format($vlr_parcela, 2, ",", ".")."</td>";

                                    if ($situacao=="P" || $situacao=="C"){
                                        echo "<td width='6%'>".$pagamento_edi->format('d/m/Y')."</td>";
                                        echo "<td width='6%'>".number_format($total_pago, 2, ",", ".")."</td>";
                                    }
                                    else {
                                        echo "<td width='6%'></td>";
                                        echo "<td width='6%'></td>";
                                    }

                                    if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4){
                                        echo "<td width='15%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='form_contas_receber_editar.php?id_ctr=".$id_ctr."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                        echo "<a class='btn' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='enviar_lixeira(\"{$numero_doc}\",\"{$numero_parcela}\",\"{$id_ctr}\")' ></i></a>"; 

                                        echo '<a class="btn" href="#" 
                                                data-toggle="modal" 
                                                data-target="#modal_baixar" 
                                                data-wid="'.$id_ctr.'"
                                                data-wdoc="'.$numero_doc.'"
                                                data-wparcela="'.$numero_parcela.'"
                                                data-wvalor="'.$vlr_parcela.'"
                                                data-wvencimento="'.$data_vencimento.'"
                                                data-wcontapag="'.$codigo_conta_pagto.'"
                                                >
                                                <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                </a>';
                                        echo "</div>";
                                        echo "</td>";
                                    }
                                    else {
                                        echo "<td width='15%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='form_contas_receber_editar.php?id=".$numero_doc."&parcela=".$numero_parcela."&id_ctr=".$id_ctr."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                        echo '<a class="btn" href="#" 
                                                data-toggle="modal" 
                                                data-target="#modal_baixar" 
                                                data-wid="'.$id_ctr.'"
                                                data-wdoc="'.$numero_doc.'"
                                                data-wparcela="'.$numero_parcela.'"
                                                data-wvalor="'.$vlr_parcela.'"
                                                data-wvencimento="'.$data_vencimento.'"
                                                data-wcontapag="'.$codigo_conta_pagto.'"
                                                >
                                                <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                </a>';
                                        echo "</div>";
                                        echo "</td>";

                                    }

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
                            <label class="control-label">Registros encontrados</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".$registros_encontrados."'";?>>
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">Total em Aberto</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".number_format($total_aberto, 2, ",", ".")."'";?>>
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">Total Recebido</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".number_format($total_geral_pagos, 2, ",", ".")."'";?>>
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">Total Geral</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".number_format($total_geral_parcelas, 2, ",", ".")."'";?>>
                        </div>
                    </div>
                </tr>

                <tr>
                    <div class="row col-md-8 confirmar_baixa_selecionados" hidden="">
                        <div class="form-group col-md-3">
                            <button type="button" class="btn btn-primary pull-left" id="baixar_selecionadas"
                            onClick="modal_baixar()" >Baixar Selecionados</button>
                        </div>
                    </div>
                </tr>

                <tr>
                    <th>
                        <input type="checkbox" class='checkbox1' id="seleciona_todos_somar" data-toggle="tooltip" data-placement="top" title="Selecionar Todos os registros para baixar">
                    </th> 
                    <th> Documento</th> 
                    <th> Local</th>
                    <th> Conta</th>
                    <th> Vencimento</th>
                    <th> Emissão</th>
                    <th> Razão Social/Nome</th>
                    <th> Valor Parcela</th>
                    <th> Recebimento</th>
                    <th> Valor</th>
                    <th> <i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>
            <tfoot>
            </tfoot>
        </table>
    </section>


    <script src="js/contas_receber.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>


                
                
