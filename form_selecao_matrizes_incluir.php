<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";

    // carrega a lista de femeas sem grupo registrados ou lista de monta sem selecinar as femeas
    if(isset($_REQUEST["editar"]) && $_REQUEST["editar"] == true) {
        $id_cobertura = $_REQUEST["id_cobertura"];
    }
    else {
        $id_cobertura = '';
    }

    $_REQUEST["editar"] = false;
    $_REQUEST["id_cobertura"] = '';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS 
  <link href="css/jquery-ui.css" rel="stylesheet" />-->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css" rel="stylesheet" type="text/css" />
  <link href="css/select-1.13.14.css" rel="stylesheet" > 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_manejo_reprodutivo'])) {
        $array_manejo_reprodutivo = explode("!",$_SESSION['menu_manejo_reprodutivo']);

        if ($array_manejo_reprodutivo[0] == 0){
            echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
            echo '<strong class="negrito">Atenção! </strong><span>Você não tem acesso a esse programa!</span>';  
            echo '</div>';         
            exit;
        }
    }
    else {
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Você não efetuol o login!</span>';  
        echo '</div>';         
        exit;
    }

    $local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0");

    $tbl_protocolo = mysqli_query($conector, "select * from tbl_protocoloiatf 
       where tbl_protocoloiatf_lixeira=0");

    $data_sistema = date("Y-m-d");
    $ano = date("Y");
    $mes = date("m");
    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    $data_parida = date('Y-m-d', strtotime('-1 month', strtotime($data_sistema)));

    $grupo_usuario = $_SESSION['grupo_usuario'];
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

    $id_parametro_estacao = 0;
    $local_id = '000000000';
    $filtros = '';
    $planilha_processada = '';
    
    if ($id_cobertura!='') {
        $tbl_cobertura = mysqli_query($conector, "select * from tbl_cobertura
            where tbl_cobertura_lixeira=0 and 
                  tbl_cobertura_id = '$id_cobertura'");

        $num_row = mysqli_num_rows($tbl_cobertura);

        if ($num_row!=0) {
            $reg_cobertura = mysqli_fetch_object($tbl_cobertura);
            $id_parametro_estacao = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
            $local_id = $reg_cobertura->tbl_cobertura_codigo_local;
            $filtros = $reg_cobertura->tbl_cobertura_filtros;
            $planilha_processada = $reg_cobertura->tbl_cobertura_planilha_processada;
        }
    }
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php";
        include "opcoes_menu.php";
        include "limpar_secao_compra_venda.php"; 
        include "limpar_secao_ctp.php"; 
        include "limpar_secao_ctr.php"; 
        include "limpar_secao_pesagem.php"; 
        include "limpar_secao_movimentacao.php"; 
        include "limpar_secao_nutricao.php"; 
        include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Reprodução <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_selecao_matrizes.php"> Seleção de Fêmeas</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Incluir</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><img src="img/matrizes.png"> Seleção de Fêmeas- Incluir</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
                    <div class="row col-md-12" id="selecionar_pasagem">
                        <form method="POST" action="#" id="form_gravar_selecionados" enctype="multipart/form-data" >

                            <input type="hidden" id="cobertura_programa" value="S">

                            <input type="hidden" name="id_cobertura_lista_sem_grupo" id="id_cobertura_lista_sem_grupo" <?php echo "value='".$id_cobertura."'";?>>

                            <input type="hidden" name="id_estacao_monta" id="id_estacao_monta" <?php echo "value='".$id_parametro_estacao."'";?>>

                            <input type="hidden" name="id_local" id="id_local" <?php echo "value='".$local_id."'";?>>

                            <input type="hidden" name="planilha_processada" id="planilha_processada" <?php echo "value='".$planilha_processada."'";?>>

                            <input type="hidden" name="opcao_femeas_sem_grupo" id="opcao_femeas_sem_grupo">

                            <input type="hidden" name="filtros" class="filtros" <?php echo "value='".$filtros."'";?>>

                            <input type="hidden" name="array_matrizes" id="array_matrizes">
                            <input type="hidden" name="array_grupos" id="array_grupos">
                            <input type="hidden" name="ordem_grupos" id="ordem_grupos">

                            <ul class="nav nav-tabs m-bot15">
                                <li class="active">
                                    <a data-toggle="tab" href="#dados">Seleção de Fêmeas</a>
                                </li>

                                <li class="parametros">
                                    <a data-toggle="tab" href="#parametros">Parametros da Estação</a>
                                </li>
                            </ul>
                            
                            <!--<div class="tab-panel selecionar_dados_pesagem"> 
                                <div class="tab-pane active">-->
                            <div class="tab-content">
                                <div id="dados" class="tab-pane active">
                                    <fieldset class="scheduler-border filtrar" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Consultar Fêmeas para Cobertura</legend>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="finalizar_sair();">Voltar</button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="control-label"><span class="required">*</span> Tipo de Registro</label>

                                                <div class="clearfix"></div>

                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_registro_matrizes" value="I" 
                                                    class="tipo_registro_matrizes"
                                                    > IATF 
                                                </label>

                                                <label class="radio-inline">
                                                      <input type="radio" name="tipo_registro_matrizes" value="M"
                                                      class="tipo_registro_matrizes"
                                                      > Monta
                                                </label>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="local_id" class="control-label"><span class="required">*</span> Fazenda
                                                </label>

                                                <select class="form-control" required="" name="local_id" id="local_id">
                                                </select>

                                            </div>

                                            <div class="col-md-4 estacao_monta" hidden>
                                                <label class="control-label">&nbsp;

                                                <p class="page-header data_estacao_monta" style="font-size: 12px;"></p>
                                                </label>
                                            </div>

                                            <div class="col-md-3 grupo_monta" hidden>
                                                <label class="control-label">&nbsp;</label>
                                                <p class="grupos_estacao"  
                                                    onclick="modal_grupo_estacao()" style="color: #1E90FF; cursor: pointer; font-size: 13px;">
                                                    <i class="fa fa-edit"></i>
                                                    &nbsp;Incluir/Editar Grupos</p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <label class="control-label">&nbsp;</label>                       
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" value="VP" name="vacas_paridas" id="vacas_paridas"> Vacas Paridas Aptas
                                                    </label>
                                                </div>
                                            </div>

<!--
A data 'Paridas Até' foi substituida por 'Aptas em' conforme o trello 
Cartão: MELHORIA NA REPRODUÇÃO (PARA DEPOIS DA ESTAÇÃO 24/2025)
Cheklist: MELHORAR A USABILIDADE: DIMINUIR CLIQUES E MELHORAR MENSAGENS NA TELA 
-->
                                            <div class="col-md-3">
                                                <label class="control-label"><span class="required">*</span> Aptas em<i class="icon_info_alt btn" data-toggle="tooltip" data-placement="top" title="Aptas na data indicada significa que: A vaca pariu em pelo menos 35 dias anteriores a essa data." style="font-size: 12px;" ></i></label>
                                                <input type="date" name="paridas_ate" id="paridas_ate" class="form-control">
                                                            
                                                <input type="hidden" id="data_paridas" <?php echo "value='".$data_parida."'";?>>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <label class="control-label">&nbsp;</label>                       
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" value="VS" name="vacas_solteiras" id="vacas_solteiras"> Vacas Solteiras 
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="control-label">&nbsp;</label>
                                                <span class="form-control" style="border: none;">(Paridas há 8 meses +)</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <label class="control-label">&nbsp;</label>                       
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" value="NO" name="novilhas" id="novilhas"> Novilhas
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="control-label">Idade (meses)</label>
                                                <input type="number" name="idade_de" id="idade_de" class="form-control" placeholder="de" onkeypress="return numeros(this, event)">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <input type="number" name="idade_ate" id="idade_ate" class="form-control" placeholder=" até" onkeypress="return numeros(this, event)">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="control-label"> Peso acima de (Kg)</label>
                                                <input type="number" name="peso_acima" id="peso_acima" class="form-control">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-primary pull-right botoes_confirma" id="botao_lista" onclick="listar_animais()">Listar
                                                </button>
                                            </div>

<!-- O botão Lista Excel Off-line foi desativado em 21/05/2025 conforme reunião do dia 20/05/2025 registrada no trello (Cartao 'MELHORIA NA REPRODUÇÃO (PARA DEPOIS DA ESTAÇÃO 24/2025)' - Cheklist 'AJUSTE REUNIAO 20/05/2025')  -->
                                            <div class="form-group col-md-2" hidden>
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-success pull-right botoes_confirma" id="botao_excel" onclick="listar_animais_excel()">Lista Excel Off-line
                                                </button>
                                            </div>
                                        </div>
                                    </fieldset> 

                                    <div class="tab-content listar" hidden="">
                                        <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">

                                        <input type="hidden" name="num_matrizes" id="num_matrizes">

                                        <input type="hidden" name="grupo_usuario" id="grupo_usuario" <?php echo "value='".$grupo_usuario."'";?>>

                                        <div class="row">
                                            <div class="form-group col-md-10">
                                                <span class="text-muted filtros" style="font-size: 12px; color: #829c9c"><?php echo $filtros;?></span>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <button type="button" class="btn btn-info pull-right" onclick="volta_filtros();">Voltar</button>
                                            </div>
                                        </div>

                                        <div id="lista_animais"></div>
                                    </div>
                                </div>

                                <div id="parametros" class="tab-pane">

                                    <div class="row">  
                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-info pull-right aba_dados" >Voltar</button>
                                        </div>

                                        <div class="row">        
                                            <div class="col-md-3">
                                                <label class="control-label">&nbsp;</label>
                                            </div>

                                            <div class="col-md-2 label-center">
                                                <label class="control-label">&nbsp;</label>
                                            </div>

                                            <div class="col-md-4 label-center">
                                                <label class="control-label">&nbsp;</label>
                                            </div>

                                            <div class="col-md-3 label-center">
                                                <label class="control-label">&nbsp;</label>
                                            </div>
                                        </div>

                                        <div class="row">        
                                            <div class="col-md-3">
                                                <label class="control-label">Fazendas</label>
                                            </div>

                                            <div class="col-md-2 label-center">
                                                <label class="control-label"><span class="required">*</span> Nome da Estação</label>&nbsp;&nbsp;
                                            </div>

                                            <div class="col-md-2 label-center">
                                                <label class="control-label"><span class="required">*</span> Período de</label>
                                            </div>

                                            <div class="col-md-2 label-center">
                                                <label class="control-label"><span class="required">*</span> Até</label>
                                            </div>

                                            <div class="col-md-3 label-center">
                                                <label class="control-label">Estações Anteriores</label>
                                            </div>

                                            <!--<div class="col-md-2 label-center">
                                                <label class="control-label"><span class="required">*</span> Número</label>
                                            </div>-->
                                        </div>
                            
                                        <?php
                                            $ssql = "SELECT * FROM tbl_pessoa 
                                                 WHERE tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"; 
                                            $rs = mysqli_query($conector,$ssql); 
                                            while ($fila = mysqli_fetch_object($rs)){
                                                $codigo_id = $fila->tbl_pessoa_id;
                                                $nome_fazenda = $fila->tbl_pessoa_nome;

                                                foreach ($array_locais_usuario as $value) {
                                                    $value = ltrim($value);
                                                    $value = rtrim($value);

                                                    if ($value==$codigo_id) {
                                                        echo ' 
                                                        <div class="row">        
                                                            <div class="form-group col-md-3">
                                                                <input name="codigo_fazenda" type="hidden" value="'.$codigo_id.'">

                                                                <input name="nome_fazenda" type="hidden" value="'.$nome_fazenda.'">
                                                                <span>'.$nome_fazenda.'</span>

                                                                <input type="hidden" name="id_parametro" id="id_parametro" value="0">
                                                            </div>

                                                            <div class="form-group col-md-2">
                                                                <input name="nome_estacao" type="text" class="form-control nome_estacao"
                                                                onkeyup="maiuscula(this)" onkeypress="return desabilita_enter (this, event)">
                                                            </div>

                                                            <div class="form-group col-md-2">
                                                                <input name="inicio_estacao" type="date" class="form-control inicio_estacao">
                                                            </div>

                                                            <div class="form-group col-md-2">
                                                                <input name="fim_estacao" type="date" class="form-control fim_estacao">
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                <select class="form-control" name="lista_estacoes" onchange="ler_parametro( this.value)">
                                                                </select>
                                                            </div> 

                                                        </div>';
                                                    }
                                                }
                                            }
                                        ?> 
                            
                                        <input name="array_codigo_fazenda" type="hidden" id="array_codigo_fazenda">

                                        <input name="array_codigo_parametro" type="hidden" id="array_codigo_parametro">

                                        <input name="array_nome_estacao" type="hidden" id="array_nome_estacao">

                                        <input name="array_inicio_estacao" type="hidden" id="array_inicio_estacao">

                                        <input name="array_fim_estacao" type="hidden" id="array_fim_estacao">

                                        <!--<input name="array_codigo_alfa" type="hidden" id="array_codigo_alfa">

                                        <input name="array_codigo_numerico" type="hidden" id="array_codigo_numerico">-->

                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-success" onClick="gravar_parametros()">Gravar Alterações</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--    </div>
                            </div> -->
                        </form>
                    </div>    
                </div>
            </div>
	        <!-- page end-->

            <div class="modal fade" id="modal_grupo_estacao" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-lg modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal_incluirLabel">Seleção de Fêmeas - Grupos </h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" id="form_grupo_estacao">
                                <div class="tab-content">
                                    <div class="alert alert-danger alert_erro_grupo" hidden="true">
                                        <strong class="negrito"></strong><span></span>
                                    </div> 

                                        <div class="row">
                                            <div class="form-group col-md-10">
                                                <p class="page-header nome_fazenda" style="color: #808080;">
                                                </p>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <button data-dismiss="modal" class="btn btn-info pull-right" type="button">Voltar</button>
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-md-3">
                                                <a class='btn' href='#' id='btnAdicionar' style="font-size: 16px">
                                                <i class='fa fa-plus'></i>&nbsp;Novo Grupo
                                                </a>
                                            </div>

                                            <div class="form-group col-md-2 novo_grupo" hidden="">
                                                <label class="control-label"><span class="required">*</span> Código</label> 
                                                <input name="codigo_grupo" type="number" class="form-control" id="codigo_grupo">
                                            </div>

                                            <div class="form-group col-md-3 novo_grupo" hidden="">
                                                <label class="control-label"><span class="required">*</span> Descrição</label> 
                                                <input name="descricao_grupo" type="text" class="form-control" id="descricao_grupo" maxlength="30" onkeyup="maiuscula(this)" onkeypress="return desabilita_enter(this, event)">
                                            </div>

                                            <div class="form-group col-md-2 novo_grupo" hidden="">
                                                <label class="control-label">&nbsp;</label> 
                                                <button type="button" class="form-control btn-success gravar_grupo" onClick="gravar_grupo()">Gravar</button>

                                                <input type="hidden" name="codigo_estacao_grupo" id="codigo_estacao_grupo">

                                                <input type="hidden" name="codigo_local_grupo" id="codigo_local_grupo">

                                                <input type="hidden" name="proximo_grupo" id="proximo_grupo">

                                                <input type="hidden" name="tipo_gravacao_grupo" id="tipo_gravacao_grupo" value="0">
                                            </div>
                                        </div>

                                        <div id="lista_grupos_estacao"></div>
                                </div> <!-- Fim tab-content-->
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-info pull-right" type="button">Voltar
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal" id="confirmar_grupo" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Seleção de Fêmeas - Confirmar Grupos</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <!--<div class="form-group col-md-6">
                                    <p class="page-header" style="color: #808080;">FÊMEAS POR GRUPO
                                    </p>
                                </div>-->

                                <div class="form-group col-md-6">
                                    <p class="page-header animais_selecionados" style="color: #808080;">
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <div id="grupos_selecionados"></div>                                
                                </div>
                            </div>

                            <?php
                                //if ($planilha_processada=='') :
                            ?>

                            <!--<div class="row">
                                <div class="form-group col-md-12">
                                    <p style="color: red;">Ao confirmar essas Fêmeas não será mais possível importa a planilha do Excel
                                    </p>                                
                                </div>
                            </div>-->

                            <?php
                                //endif;
                            ?>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success gravar_selecao" type="button" onclick="verificar_femeas_gravar_matrizes()">Confirma</button>

                            <button data-dismiss="modal" class="btn btn-info pull-right" type="button">Voltar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="confirmar_grupo_com_id" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="fechar_modal_diagnostico_negativo();">&times;</button>
                            <h4 class="modal-title">Seleção de Fêmeas - Confirmar Grupos</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <h4 class="page-header-animais id_cobertura"></h4>

                                    <div class="form-group col-md-12">
                                        <label class='control-label' style='font-weight: bold;'><span class='required'>*</span> Existem fêmeas sem grupo digitado. O que deseja fazer?</label>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label class="radio-inline">
                                                    <input type='radio' name='opcao_gravar_grupo' id="manter_femeas" value='M'>Gravar e continuar a digitação dos grupos mais tarde.
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label class="radio-inline">
                                                    <input type='radio' name='opcao_gravar_grupo' id="excluir_femeas" value='E'>Gravar e finalizar a digitação de grupos para esse documento.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success gravar_selecao" type="button" onclick="gravar_matrizes();">Confirma</button>

                            <button data-dismiss="modal" class="btn btn-info pull-right" type="button">Voltar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_sair_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Seleção de Fêmeas </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Seleção de Fêmeas</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_parametro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Seleção de Fêmeas - Parametros</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" onclick="selecao_matrizes_incluir();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_grupo" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Seleção de Fêmeas - Grupos</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" onclick="modal_grupo_estacao();">Fechar</button>
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
                            <h4 class="modal-title">Seleção de Fêmeas - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="aguardar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p class="aguardar">Aguarde <i class='fa fa-spinner fa-spin fa-2x' ></i></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <?php
                    include "ajuda.php";
                ?>
            </div>

        </section>
    </section>
</section>

 <div class="text-center">
     <div class="credits">
         <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2024</p></font>
     </div>
 </div>

</section> <!-- container section start end -->
  
<!-- javascripts -->
<script src="js/jquery.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.scrollTo.min.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js?<?php echo Versao; ?>"></script>
<script src="js/ga.js?<?php echo Versao; ?>" type="text/javascript" ></script>
<script src="js/bootstrap-switch.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.tagsinput.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.hotkeys.js?<?php echo Versao; ?>"></script>
<script src="js/bootstrap-wysiwyg.js?<?php echo Versao; ?>"></script>
<script src="js/bootstrap-wysiwyg-custom.js?<?php echo Versao; ?>"></script>
<script src="js/moment.js?<?php echo Versao; ?>"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>
<script src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js" type="text/javascript" ></script>

<script src="js/matrizes.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>

<script src="js/typeahead.js"></script>

    <script>
        $(document).ready(function(){
           $('[data-toggle="tooltip"]').tooltip();   
        });

        $(document).ready(function(){
            $("#local_id").click(function(){
                var tipo_registro = $("input[name='tipo_registro_matrizes']:checked").val();

                if (tipo_registro==undefined) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html('Informe o Tipo de Registro!');
                    return;
                }
            });
        });
    </script>

    <script>
        function reseta_confirma(){
            clickedConfirm = true;
        }

        $(document).ready(function() {
            needToConfirm = false;
            clickedConfirm = false; 
            window.onbeforeunload = askConfirm;
        });

        function askConfirm() {
            if(clickedConfirm){
                needToConfirm = false;
            }
            if (needToConfirm) {
                return ''; 
            }
        }

        $('#btn_salvar').click(function(){
            var a_pesar = $('#qtd_a_pesar').val();
            var pesados = $('#total_pesados').val();
            if(a_pesar > pesados){  
                needToConfirm = true;
            }else{
                needToConfirm = false;
            }
        });
    </script>

</body>
</html>


