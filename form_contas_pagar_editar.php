<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";
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
  <link href="css/jquery-ui.css" rel="stylesheet" />
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <link href="css/daterangepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-datepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet" >
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

   @ session_start();   
    if(isset($_SESSION['menu_gestao_adm'])) {
        $array_gestao_adm = explode("!",$_SESSION['menu_gestao_adm']);

        if ($array_gestao_adm[1] == 0){
            echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
            echo '<strong class="negrito">Atenção! </strong><span>Você não tem acesso a esse programa!</span>';  
            echo '</div>';         
            exit;
        }
    }
    else {
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Você não efetuou o login!</span>';  
        echo '</div>';         
        exit;
    }

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];

    $chave_ctp = $_REQUEST['id'];
    //$codigo_fornecedor = substr($chave_ctp,0,9);
    //$parcela_ctp = substr($chave_ctp,9,3);
    //$numero_ctp = substr($chave_ctp,12,15);

    $contas_pagar = mysqli_query($conector, "select * from contas_pagar 
        where ctp_id='$chave_ctp' "); 
                     
    $registro_ctp = mysqli_fetch_object($contas_pagar);

    $codigo_fornecedor = $registro_ctp->ctp_codigo_fornecedor;
    $parcela_ctp = $registro_ctp->ctp_parcela;
    $numero_ctp = $registro_ctp->ctp_numero_doc;

    $qtd_parcela = $registro_ctp->ctp_qtd_parcelas;
    $data_emissao = $registro_ctp->ctp_data_emissao; 
    $data_vencimento = $registro_ctp->ctp_data_vencimento; 
    $vlr_parcela = $registro_ctp->ctp_valor_parcela; 
    $vlr_juros = $registro_ctp->ctp_valor_juros; 
    $desc_juros = $registro_ctp->ctp_descricao_valor_juros; 
    $vlr_desconto = $registro_ctp->ctp_valor_desconto; 
    $desc_desconto = $registro_ctp->ctp_descricao_valor_desconto; 
    $vlr_outro = $registro_ctp->ctp_outro_valor; 
    $desc_outro = $registro_ctp->ctp_descricao_outro_valor; 
    $codigo_fazenda = $registro_ctp->ctp_codigo_fazenda;
    $codigo_conta= $registro_ctp->ctp_codigo_conta; 
    $codigo_c_custo= $registro_ctp->ctp_codigo_centro_custos; 
    $codigo_conta_pag= $registro_ctp->ctp_conta_pagamento; 
    $numero_cheque = $registro_ctp->ctp_numero_cheque; 
    $situacao = $registro_ctp->ctp_situacao; 
    $aceite = $registro_ctp->ctp_aceite; 
    $agendamento=$registro_ctp->ctp_agendamento; 
    $data_agendamento = new DateTime($registro_ctp->ctp_data_agendamento); 
    $nome_fornecedor = $registro_ctp->ctp_nome_fornecedor;
    $descricao_compra = $registro_ctp->ctp_descricao_compra;
    $observacoes_ctp  = $registro_ctp->ctp_observacoes;
    $tipo_documento = $registro_ctp->ctp_tipo_documento;

    if($agendamento=="S") {
        $desc_agendamento='*Este registro faz parte de um agendamento. Data para pagamento: ' . $data_agendamento->format('d/m/Y');
    }
    else {
        $desc_agendamento='';
    }

    if ($situacao == "P") {
        $desc_situacao = "Pago";
    } 
    else if ($situacao == "C") {
        $desc_situacao = "Pago Parcial";
    } 
    else {
        $desc_situacao = "Em Aberto";
    }

    $plano_contas = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_debito_credito='D' and tbl_plano_contas_ana_sin='A' and tbl_plano_contas_lixeira=0 order by tbl_plano_contas_descricao ASC"); 

    $cli_for = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_lixeira=0 and (tbl_pessoa_classe=3 or tbl_pessoa_classe=5) order by tbl_pessoa_nome ASC"); 

    $conta_pagamento = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC"); 

    $c_custo = mysqli_query($conector, "select * from tbl_centro_custo where tbl_cc_lixeira=0 order by tbl_cc_codigo_id ASC"); 

    $tipos_documentos = mysqli_query($conector, "select * from tbl_tipo_documento where tbl_tipo_doc_lixeira=0"); 

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0");

    $nd_esc_an  = mysqli_real_escape_string($conector, $numero_ctp);
    $for_esc_an = intval($codigo_fornecedor);
    if ($nd_esc_an !== '' && $nd_esc_an !== '0') {
        $rs_qtd_an = mysqli_query($conector, "SELECT COUNT(*) as qtd FROM tbl_ctp_anexos a INNER JOIN contas_pagar c ON c.ctp_id = a.anexo_ctp_id WHERE c.ctp_numero_doc = '$nd_esc_an' AND c.ctp_codigo_fornecedor = '$for_esc_an'");
    } else {
        $rs_qtd_an = mysqli_query($conector, "SELECT COUNT(*) as qtd FROM tbl_ctp_anexos WHERE anexo_ctp_id = '$chave_ctp'");
    }
    $row_qtd_an = $rs_qtd_an ? mysqli_fetch_object($rs_qtd_an) : null;
    $qtd_anexos = $row_qtd_an ? (int)$row_qtd_an->qtd : 0;

    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = "SELECT * FROM usuario 
        WHERE id_usuario = '$codigo_usuario' AND 
              lixeira_usuario=0 ";  
    $query = mysqli_query($conector_acesso, $tbl_usuario);

    $num_rows_usuario = mysqli_num_rows($query);

    if ($num_rows_usuario!=0){
        $reg_usuario = mysqli_fetch_assoc($query);

        $array_locais_usuario = explode(',', $reg_usuario['local_usuario']);
        $qtd_locais_usuario = count($array_locais_usuario);

        if ($qtd_locais_usuario==0) {
            $array_locais_usuario='';
        }
    }
    else {
        $array_locais_usuario='';
    }

    $data_sistema = date("Y-m-d");

?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php";
        include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";

    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_contas_pagar.php"> Contas a Pagar</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Contas a Pagar Editar</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-search-dollar"></i> Contas a Pagar - Editar</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="gravar_contas_pagar.php" enctype="multipart/form-data" id="form_gravar_contas_pagar">

                            <div class="panel"> 
                                <div class=panel-body>

                                    <div class="row" id="errors"></div>
                      
                                    <ul class="nav nav-tabs m-bot15">
                                        <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="tab-content">
                                                <div class="tab-pane active">
                                                <?php 
		                                        if ($situacao=='' && $agendamento=='' && ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2)) {
		                                        echo '<div class="row">                
		                                        <div class="form-group col-md-12">
		                                        <button type="button" class="btn btn-primary confirma_gravar_ctp">Confirmar Edição</button>
		                                        <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
		                                                            </div>
		                                                        </div>';
		                                                }
                                                        else if ($aceite==''){
                                                            echo '
                                                                <div class="row">                
                                                                    <div class="form-group col-md-12">
                                                                        <button type="button" class="btn btn-primary confirma_gravar_ctp">Confirmar Edição</button>
                                                                        <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                                    </div>
                                                                </div>';
                                                        }
		                                                else {
		                                                    echo '
		                                                        <div class="row">                
		                                                            <div class="form-group col-md-12">
		                                                                <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
		                                                            </div>
		                                                        </div>
		                                                    ';
		                                                }
		                                            ?>
		                                        </div> <!-- dados-->
                                            </div>

                                            <div class="row">
                                            	<input name="tipo_operacao" type="hidden" class="form-control" id="tipo_operacao" value="2">

                                                <input name="grupo_usuario" type="hidden" class="form-control" id="grupo_usuario" <?php echo "value='".$codigo_grupo_usuario."'";?>>

                                                <div class="form-group col-md-3">
                                                    <label for="doc_editar" class="control-label">Documento Nº</label>
                                                    <input name="doc_editar" type="number" class="form-control" id="doc_editar" data-toggle='tooltip' data-placement='top'  title="Caso não tenha o Nº, o sistema irá criar um automaticamente"
                                                    <?php echo "value='".$numero_ctp."'";?>>
                                                    <input name="ctp_id" type="hidden" class="form-control" id="ctp_id"  
                                                    <?php echo "value='".$chave_ctp."'";?>>

                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="tipo_doc" class="control-label">Tipo do Documento</label>
                                                    <select class="form-control" id="tipo_doc" name="tipo_doc" required="">

                                                      <option value="00" selected="selected">Sem Tipo Cadastrado</option>

                                                      <?php while($registro_tipo_doc = mysqli_fetch_object($tipos_documentos)) { ?>

                                                      <option value="<?php 
                                                       echo $registro_tipo_doc->tbl_tipo_doc_id ?>"

                                                      <?php 
                                                          if($registro_tipo_doc->tbl_tipo_doc_id==$tipo_documento) 
                                                             { echo "selected"; }
                                                      ?>>

                                                        
                                                      <?php 
                                                          echo $registro_tipo_doc->tbl_tipo_doc_descricao;
                                                      ?>
                                                      </option>
                                                      <?php } ?>

                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="parcela_editar" class="control-label">Parcela Nº</label>
                                                    <input name="parcela_editar" type="text" class="form-control" id="parcela_editar" readonly=""
                                                    <?php echo "value='".$parcela_ctp."'";?>>
                                                </div> 

                                                <div class="form-group col-md-3">
                                                    <label for="qtd_parcela" class="control-label">Quantidade de Parcelas</label>
                                                    <input name="qtd_parcela" type="text" class="form-control" id="qtd_parcela" readonly=""
                                                    <?php echo "value='".$qtd_parcela."'";?> >
                                                </div>

                                            </div>

                                            <div class="row"> 
                                                <div class="form-group col-md-6">
                                                    <label for="codigo_cli_for" class="control-label"><span class="required">*</span> Razão/Nome</label>
	                                                <select class="form-control" id="codigo_cli_for" name="codigo_cli_for" readonly="">

	                                                  <option value="999999999" selected="selected">Sem Fornecedor Cadastrado</option>

	                                                  <?php while($registo_cli_for = mysqli_fetch_object($cli_for)) { ?>

	                                                  <option value="<?php 
	                                                   echo $registo_cli_for->tbl_pessoa_id ?>"

                                                      <?php 
                                                          if($registo_cli_for->tbl_pessoa_id==$codigo_fornecedor) 
                                                             { echo "selected"; }
                                                      ?>>
	                                                    
	                                                  <?php 
	                                                      echo $registo_cli_for->tbl_pessoa_nome;
	                                                  ?>
	                                                  </option>
	                                                  <?php } ?>

	                                                </select>
                                                </div>
                                                    <div class="form-group col-md-6">
                                                    <label for="nome_for" class="control-label">&nbsp;</label>
                                                    <input name="nome_for" type="text" class="form-control" id="nome_for" aria-describedby="passwordHelpBlock" onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$nome_fornecedor."'";?>>
                                                    </div>

                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="codigo_fazenda" class="control-label"><span class="required">*</span> Local</label>
                                                    <select class="form-control" id="codigo_fazenda" name="codigo_fazenda">

                                                    <option value="000000000">...</option>

                                                    <?php 
                                                        while($reg_local = mysqli_fetch_object($tbl_local)) { 
                                                            
                                                            foreach ($array_locais_usuario as $value) {
                                                                $value = ltrim($value);
                                                                $value = rtrim($value);
                                                                if ($value==$reg_local->tbl_pessoa_id) {
                                                                    if ($reg_local->tbl_pessoa_id==$codigo_fazenda) {
                                                                    echo '<option value="'.$value.'" selected>' .$reg_local->tbl_pessoa_nome. '</option>'; 
                                                                    }
                                                                    else {
                                                                    echo '<option value="'.$value.'">' .$reg_local->tbl_pessoa_nome. '</option>'; 

                                                                    }
                                                                }
                                                            }
                                                        } 
                                                    ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="codigo_cc" class="control-label">Centro de Custo</label>
                                                    <select class="form-control" id="codigo_cc" name="codigo_cc">
                                                    <option value="000" selected="selected">...</option>

                                                      <?php while($registo_cc = mysqli_fetch_object($c_custo)) { ?>

                                                      <option value="<?php 
                                                       	echo $registo_cc->tbl_cc_codigo_id ?>"

                                                       	<?php 
                                                      		if($registo_cc->tbl_cc_codigo_id==$codigo_c_custo) 
                                                         		{ echo "selected"; }
                                                  		?>>
                                                        
                                                      <?php 
                                                          echo $registo_cc->tbl_cc_descricao;
                                                      ?>
                                                      </option>
                                                      <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-4">
	                                                <label for="codigo_conta" class="control-label"><span class="required">*</span> Conta Contábil</label>
	                                                <select class="form-control" id="codigo_conta" name="codigo_conta" required="">

	                                                  <option value="0000000" selected="selected">...</option>

	                                                  <?php while($registo_pcontas = mysqli_fetch_object($plano_contas)) { ?>

	                                                  <option value="<?php 
	                                                   echo $registo_pcontas->tbl_plano_contas_codigo_id ?>"

	                                                    <?php 
	                                                      if($registo_pcontas->tbl_plano_contas_codigo_id==$codigo_conta) 
	                                                         { echo "selected"; }
	                                                  ?>>
                                                    
	                                                  <?php 
	                                                      echo $registo_pcontas->tbl_plano_contas_descricao;
	                                                  ?>
	                                                  </option>
	                                                  <?php } ?>
	                                                </select>
                                                </div>
                                            </div>

                                            <div class="row m-bot15">
                                                <div class="col-md-12">
                                                  <label for="descricao_compra" class="control-label"><span class="required">*</span>Descrição da Compra</label>
                                                  <textarea name="descricao_compra" type="text" class="form-control" id="descricao_compra" rows="1" onkeyup="maiuscula(this)"><?php echo $descricao_compra; ?></textarea>
                                                </div>
                                            </div>


                                            <hr>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="data_emissao" class="control-label"><span class="required">*</span>
                                                    Data de Emissão</label>
                                                    <input name="data_emissao" type="date" class="form-control" id="data_emissao"
                                                    <?php echo "value='".$data_emissao."'";?>>
                                                </div>

                                                <div class="form-group col-md-6">
                                                     <label for="data_vencimento" class="control-label"><span class="required">*</span>
                                                     Data de Vencimento</label>
                                                    <input name="data_vencimento" type="date" class="form-control" id="data_vencimento"
                                                    <?php echo "value='".$data_vencimento."'";?>>
                                                </div>
                                            </div>

	                                        <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="vlr_parcela" class="control-label"><span class="required">*</span>Valor da Parcela</label>
                                                    <input name="vlr_parcela" type="text" class="form-control" id="vlr_parcela" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_parcela()" 
                                                    <?php echo "value='".number_format($vlr_parcela, 2, ',', '.')."'";?>>
                                                </div>
                                            </div>
                                             
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="vlr_juros" class="control-label">Valor dos Juros</label>
                                                    <input name="vlr_juros" type="text" class="form-control" id="vlr_juros" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_juros()" 
                                                        <?php echo "value='".number_format($vlr_juros, 2, ',', '.')."'";?>>
                                                </div>

                                                <div class="form-group col-md-8">
                                                    <label for="desc_juros" class="control-label">Descrição dos Juros</label>
                                                    <input name="desc_juros" type="text" class="form-control" id="desc_juros"
                                                    <?php echo "value='".$desc_juros."'";?>>
                                                </div>
                                            </div>
                                              
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="vlr_desconto" class="control-label">Valor do Desconto</label>
                                                    <input name="vlr_desconto" type="text" class="form-control" id="vlr_desconto"  placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_desconto()" 
                                                        <?php echo "value='".number_format($vlr_desconto, 2, ',', '.')."'";?>>
                                                </div>

                                                <div class="form-group col-md-8">
                                                    <label for="desc_desconto" class="control-label">Descrição do Desconto</label>
                                                    <input name="desc_desconto" type="text" class="form-control" id="desc_desconto"
                                                    <?php echo "value='".$desc_desconto."'";?>>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="vlr_acrescimo" class="control-label">Valor Outros Acréscimos</label>
                                                    <input name="vlr_acrescimo" type="text" class="form-control" id="vlr_acrescimo" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_acrescimo()" 
                                                        <?php echo "value='".number_format($vlr_outro, 2, ',', '.')."'";?>>
                                                </div>

                                                <div class="form-group col-md-8">
                                                    <label for="desc_acrescimo" class="control-label">Descrição Outros Acréscimos</label>
                                                    <input name="desc_acrescimo" type="text" class="form-control" id="desc_acrescimo"
                                                    <?php echo "value='".$desc_outro."'";?>>
                                                </div>
                                            </div>

                                            <div class="row m-bot15" style="margin-top:6px;">
                                                <div class="col-md-12">
                                                  <label for="observacoes" class="control-label">Observação</label>
                                                  <textarea name="observacoes" class="form-control" id="observacoes" rows="2"><?php echo htmlspecialchars($observacoes_ctp ?? ''); ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row" style="margin-top:2px;margin-bottom:6px;">
                                                <div class="col-md-12">
                                                    <?php
                                                        $nd_js_ed  = addslashes($numero_ctp);
                                                        $for_js_ed = intval($codigo_fornecedor);
                                                        $id_js_ed  = intval($chave_ctp);
                                                        $auto_inp  = 'true';
                                                    ?>
                                                    <a href="#" onclick="abrirModalAnexos('<?= $nd_js_ed ?>',<?= $for_js_ed ?>,<?= $id_js_ed ?>,'<?= $nd_js_ed ?>',<?= $auto_inp ?>); return false;"
                                                       style="font-size:0.9em;font-weight:500;color:#128cb8;">
                                                        <i class="fas fa-paperclip"></i> Anexos
                                                        <?php if ($qtd_anexos > 0): ?>
                                                        <span style="font-size:11px;color:#888;font-weight:400;">(<?= $qtd_anexos ?>)</span>
                                                        <?php endif; ?>
                                                    </a>
                                                </div>
                                            </div>

											<hr>

                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                <label for="codigo_forma_rec" class="control-label">Banco/Conta Pagamento</label>
                                                <select class="form-control" id="codigo_forma_rec" name="codigo_forma_rec" >

                                                  <option value="0" selected="selected">...</option>

                                                  <?php 
while ($reg_conta_pag = mysqli_fetch_object($conta_pagamento)) { 
    $codigo_conta = $reg_conta_pag->tbl_conta_pagamento_id;
    $nome_banco = $reg_conta_pag->tbl_conta_pagamento_descricao;
    $agencia = $reg_conta_pag->tbl_conta_pagamento_agencia;
    $conta = $reg_conta_pag->tbl_conta_pagamento_conta;
    $descricao_conta = $nome_banco .' (Age: '.$agencia.' Cta: '.$conta.')';

    if ($codigo_conta == $codigo_conta_pag) {
        echo '<option value="' . $codigo_conta . '" selected="selected">' . $descricao_conta .
         '</option>';
    }
    else {
        echo '<option value="'.$codigo_conta.'">' . $descricao_conta .
         '</option>';

    }
}?>

                                                </select>

                                                </div>

                                                <div class="form-group col-md-4 cheque">
                                                    <label for="cheque_editar" class="control-label">Número do Cheque</label>
                                                    <input name="cheque_editar" type="text" class="form-control" id="cheque_editar" maxlength="10"
                                                    <?php echo "value='".$numero_cheque."'";?>>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-8">
                                                    <label for="desc_situacao" class="control-label">Situação da Conta
                                                    </label>
                                                    <input name="desc_situacao" type="text" class="form-control" id="desc_situacao" readonly
                                                    <?php echo "value='".$desc_situacao."'";?>>
                                                </div>

                                                <div class="form-group col-md-1" id="baixa_conta_pagar"  hidden="true" >
                                                    <label for="baixa_conta_pagar" class="control-label">&nbsp;
                                                    </label>

                                                    <button type="button" 
                                                    class="form-control btn-primary" onClick="baixar_conta_pagar()"> Baixar</button>
                                                </div>

                                                <div class="col-md-2">
                                                    <img src='img/aguarde.gif' title='' alt=''
                                                     width='42' height='35' 
                                                    id="img_aguarde_baixa" hidden="true" />
                                                </div>
                                            </div>

                                            <div class="row">
                                            </div>

                                            <div id="baixar_conta" hidden="true">
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                         <label for="data_pagamento" class="control-label"><span class="required">*</span>
                                                         Data do Pagamento</label>
                                                        <input name="data_pagamento" type="date" class="form-control" id="data_pagamento" 
                                                        >
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                         <label for="valor_pagamento" class="control-label"><span class="required">*</span>
                                                         Valor do Pagamento</label>
                                                        <input name="valor_pagamento" type="text" class="form-control" id="valor_pagamento" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_pagamento()" 
                                                        >
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-8">
                                                         <label for="historico" class="control-label"><span class="required">*</span>
                                                         Histórico</label>
                                                        <input name="historico" type="text" class="form-control" id="historico" 
                                                        >
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="baixa_conta_pagar" 
                                                        class="control-label">&nbsp;
                                                        </label>

                                                        <button type="button" class=" form-control btn btn-primary" onClick="executar_baixa_conta_pagar_individual()">Confirmar Baixa</button>
                                                    </div>


                                                </div>
                                            </div>

                                            <?php 
                                                if ($situacao=="P" || $situacao=="C"){
                                                    echo '<table class="table table-advance table-hover" width="100%" >';
                                                    echo '<thead>';
                                                    echo '<tr>';
                                                    echo '<th>Pagamento</th>'; 
                                                    echo '<th>Valor</th>'; 
                                                    echo '<th>Histórico</th>';
                                                    echo '<th>Baixado Por</th>'; 
                                                    echo '<th>Estornar</th>'; 
                                                    echo '</tr>';
                                                    echo '</thead>';
                                                    echo '<tbody>';

    $ssql = "select * from baixa_contas_pagar 
        where bcp_id='$chave_ctp'";
    $rs = mysqli_query($conector, $ssql); 
    $num_total_registros = mysqli_num_rows($rs);

    while ($fila = mysqli_fetch_object($rs)) {
        $historico = $fila->bcp_historico_pagamento;
        $valor_pagamento = $fila->bcp_valor_pagamento;
        $valor_pagamento = number_format($valor_pagamento, 2, ".", "");
        $sequencia_baixa = $fila->bcp_sequencia_pagamento;
        $numero_agendamento = $fila->bcp_numero_agendamento;
        $baixado_por = $fila->bcp_usuario_aceite;
        $data_pagamento = new DateTime($fila->bcp_data_pagamento);
        $data_baixa = new DateTime($fila->bcp_data_aceite);
                    
        // AJUSTAR O PROGRAMA DEPOIS, A SEQUENCIA DA BAIXA DEVE ESTAR NA CHAVE
        $bcp_chave_baixa = $chave_ctp.$sequencia_baixa;

        //$bcp_chave_baixa = $codigo_fornecedor . $parcela_ctp . $sequencia_baixa . $numero_ctp;

                                                        echo "<tr>";
                                                        echo "<td width='10%' align='center'>" . $data_pagamento ->format('d/m/Y') . "</td>";
                                                        echo "<td width='8%' align='right'>" . $valor_pagamento . "</td>";
                                                        echo "<td width='40%' align='left'>" . $historico . "</td>";
                                                        echo "<td width='20%' align='left'>" . $baixado_por . ' ' . $data_baixa->format('d/m/Y H:i:s') ."</td>";
                                                        echo "<td align='center' width='8%'><a href='#'>
                                                            <i class='btn icon_trash_alt'  data-toggle='tooltip' data-placement='left' title='Estorna a baixa'
                                                             onClick='estornar_baixa_contas_pagar(\"{$bcp_chave_baixa}\")'></i> 
                                                             </a></td>";
                                                        echo "</tr>";
                                                    }
                                                    echo '</tbody>';
                                                    echo '</table>'; 
                                                }

                                                if ($situacao=='' && $agendamento=='' && ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2) && $situacao=='') {
                                                    echo '
                                                        <div class="row">                
                                                            <div class="form-group col-md-12">
                                                                <button type="button" class="btn btn-primary confirma_gravar_ctp">Confirmar Edição</button>
                                                                <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                            </div>
                                                        </div>';
                                                }
                                                else if ($aceite=='' && $situacao==''){
                                                    echo '
                                                        <div class="row">                
                                                            <div class="form-group col-md-12">
                                                                <button type="button" class="btn btn-primary confirma_gravar_ctp">Confirmar Edição</button>
                                                                <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                            </div>
                                                        </div>';
                                                }
                                                else {
                                                    echo '
                                                        <div class="row">                
                                                            <div class="form-group col-md-12">
                                                                <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                            </div>
                                                        </div>
                                                    ';
                                                }
                                            ?>

                                        </div> <!-- dados-->

                                    </div> <!--tab-content -->

                                </div> <!--panel-body -->
                            </div> <!--panel -->      
                        </form>
                    </section> <!-- panel-group -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->

	        <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
	            aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	        	<div class="modal-dialog modal-dialog-centered" role="document">
	            	<div class="modal-content">
	            		<div class="modal-header">
	                		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	                		<h4 class="modal-title">Contas a Pagar</h4>
	            		</div>
	            		<div class="modal-body"></div>
	            		<div class="modal-footer">
	                		<button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
	              		</div>
	            	</div>
	        	</div>
	        </div>

	        <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
	            aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	        	<div class="modal-dialog modal-dialog-centered" role="document">
	            	<div class="modal-content">
	            		<div class="modal-header">
	                		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	                		<h4 class="modal-title">Contas a Pagar - Erro</h4>
	            		</div>
	            		<div class="modal-body"></div>
	            		<div class="modal-footer">
	                		<button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
	                		</button>
	              		</div>
	            	</div>
	        	</div>
	        </div>

            <div>
                <?php
                    include "ajuda.php";
                ?>
            </div>
            
        </section> <!-- wrapper -->
    </section><!--main-content -->


<?php include "modal_anexos.php"; ?>

<?php
  $javascript_file_name = 'contas_pagar.js';
  require 'rodape.php';
?>




