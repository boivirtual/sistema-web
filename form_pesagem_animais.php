<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    if(isset($_REQUEST["editar"]) && $_REQUEST["editar"] == true) {
        $erro_importar_pesagem = $_REQUEST["erro"];
    }
    else {
        $erro_importar_pesagem = '';
    }

    $_REQUEST["editar"] = false;

    $data_sistema = date("Y-m-d");
    $ano = date("Y");
    $mes = date("m");

    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css?<?php echo Versao; ?>" rel="stylesheet">
    <link href="css/bootstrap-theme.css?<?php echo Versao; ?>" rel="stylesheet">
    <link href="css/elegant-icons-style.css" rel="stylesheet" />
    <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
    <link href="css/style-responsive.css?<?php echo Versao; ?>" rel="stylesheet" />
    <link href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="css/select-1.13.14.css" rel="stylesheet" > 

    <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

    <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>
  <?php
    @ session_start();

    if(isset($_SESSION['menu_manejo_animais'])) {
        $array_cadastro = explode("!",$_SESSION['menu_manejo_animais']);

        if ($array_cadastro[1] == 0){
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

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $tbl_epoca = mysqli_query($conector, "select * from tabela_epoca_pesagem where tab_registro_lixeira_epoca_pesagem=0"); 

    if ($_SESSION['data_inicial_pesagem']==''){
        $data_inicial = $ano . '-' . $mes . '-01';
    }
    else {
        $data_inicial =  $_SESSION['data_inicial_pesagem'];  
    }

    if ($_SESSION['data_final_pesagem']==''){
        $data_final = $ano . '-' . $mes . '-' . $dias_mes;
    }
    else {
        $data_final =  $_SESSION['data_final_pesagem'];   
    }

    $array_epoca= $_SESSION['epoca_pesagem'];
    $local= $_SESSION['local_pesagem'];

    $controle_estoque= $_SESSION['controle_estoque'];

    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = "SELECT * FROM usuario WHERE id_usuario = '$codigo_usuario' AND 
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

?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; 
        include "limpar_secao_selecao_matrizes.php"; 
        include "limpar_secao_compra_venda.php"; 
        include "limpar_secao_ctp.php"; 
        include "limpar_secao_ctr.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Pesagem</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-weight"></i> Pesagem</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div class="form-group">
                        <?php 
                            if ($controle_estoque=='I') :
                        ?>
                        <a href="form_pesagem_animais_incluir.php">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Nova Pesagem"/>
                        </a>

                        <?php 
                            else :
                        ?>

                        <a href="form_pesagem_animais_incluir_lote.php">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Nova Pesagem"/>
                        </a>

                        <?php 
                            endif;
                        ?>

                        <input type="hidden" id="erro_importar_pesagem" <?php echo "value='".$erro_importar_pesagem."'";?>>
                    </div> 

                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="form_produtos.php" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Consultar Pesagem</legend>

                                        <div class="row digitar_filtros">
                                            <input type="hidden" name="controle_estoque" id="controle_estoque"
                                            <?php echo "value='".$controle_estoque."'";?>>

                                            <input id="lista_pesagem_automatico" type="hidden" <?php echo "value='" . $_SESSION['lista_pesagem'] . "'"; ?>>

                                            <input id="exibe_local" type="hidden" <?php echo "value='".$local."'"; ?>>

                                            <div class="form-group col-md-4">
                                                <label for="data_inicial" class="control-label">Data Inicial</label>

                                                <input type="date" name="data_inicial" id="data_inicial" class="form-control"
                                                    <?php echo "value='".$data_inicial."'";?>>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="data_final" class="control-label">Data Final</label>
                                                <input name="data_final" type="date" class="form-control" id="data_final"
                                                    <?php echo "value='".$data_final."'";?>>
                                            </div>
                                        </div>
                                        
                                        <div class="row digitar_filtros">    
                                            <div class="form-group col-md-4">
                                                <label for="codigo_local" class="control-label">Fazenda</label>
                                                <select class="form-control selectpicker" id="codigo_local" multiple name="codigo_local">

                                                <?php 
                                                    while($reg_local = mysqli_fetch_object($tbl_local)) { 
                                                    
                                                        foreach ($array_locais_usuario as $value) {
                                                            $value = ltrim($value);
                                                            $value = rtrim($value);
                                                            if ($value==$reg_local->tbl_pessoa_id) {
                                                                echo '<option value="'.$value.'">' .$reg_local->tbl_pessoa_nome. '</option>'; 
                                                            }
                                                        }
                                                    } 
                                                 ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="codigo_pesagem" class="control-label">Motivo da Pesagem</label>
                                                <select class="form-control selectpicker" multiple id="codigo_pesagem" name="codigo_pesagem">
                                                      
                                                <?php while($reg_epoca = mysqli_fetch_object($tbl_epoca)) { ?>

                                                    <option value="<?php 
                                                        echo $reg_epoca->tab_codigo_epoca_pesagem ?>"

                                                    <?php 

                                                    	if ($array_epoca!="") {
                                                    		foreach ($array_epoca as $value) {
                            									if ($value==$reg_epoca->tab_codigo_epoca_pesagem) { 
                            										echo "selected";       
                            									}
                            								}                    		
                                                    	}
                                                    ?>>
                                                        
                                                    <?php 
                                                        echo $reg_epoca->tab_descricao_epoca_pesagem ;
                                                    ?>
                                                    </option>
                                                <?php } ?>

                                                </select>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right consultar" onclick="consultar()">Consultar</button>
                                            </div>
                                        </div>

                                        <div class="row filtros_consulta" hidden>
                                            <div class="col-md-10">
                                                <p style="font-size: 12px; color: #829c9c">Filtros: 
                                                    <span class="descricao_filtro" style="font-weight: normal;">
                                                    </span>

                                                    <span class="mais_filtros" hidden>&nbsp;
                                                        <a href="#" data-toggle='tooltip' data-placement='top' title="Exibir Filtros" onclick="exibe_mais_filtros()"> 
                                                            <i class="fas fa-filter"></i> +
                                                        </a>
                                                    </span>

                                                    <span class="menos_filtros" hidden>&nbsp;
                                                        <a href="#" data-toggle='tooltip' data-placement='top' title="Esconder Filtros" onclick="exibe_menos_filtros()"> 
                                                            <i class="fas fa-filter"></i> -
                                                        </a>
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="col-md-2">
                                                <a href="#" style="font-size: 0.9em; font-weight: 600; text-align: right; color: #128cb8; float: right;" onclick="mais_relatorios()" data-toggle="tooltip" data-placement="top" title="Histórico de Animais" class="pull-right"><i class="fa fa-plus"></i> Relatórios</a>
                                            </div>
                                        </div> 
                                    </fieldset>
                                    <div id="lista_pesagem"></div>
                                </div>
                            </div>
                        </form>
                    </div>    
                </div>
            </div>
	        <!-- page end-->

            <div class="modal fade" id="modal_pesagem_sem_finalizar" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle myLargeModalLabel"  aria-hidden="true" data-backdrop="static">
                <div class="modal-lg modal-dialog modal-dialog-centered" role="document"style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title" id="modal_incluirLabel">Pesagens não finalizadas</h3>
                        </div>

                        <div class="modal-body">
                            <div id="lista_pesagem_sem_finalizar"></div>
                        </div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-info" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_importar_excel" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title" id="modal_incluirLabel">Importar Excel da pesagem</h3>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="importar_excel_pesagem.php" enctype="multipart/form-data" id="form_importar_excel">
                                  
                                <div class="tab-content">
                                    <div class="tab-pane active">

                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="local_pesado" class="control-label">Fazenda</label>
                                                <input type="text" class="form-control input-sm local_pesado" readonly="">                             
                                                <input type="hidden" name="local_pesado" id="local_pesado">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="epoca_pesado" class="control-label">Motivo da Pesagem</label>
                                                <input type="text" class="form-control input-sm epoca_pesado" readonly="">                                                <input type="hidden" name="epoca_pesado" id="epoca_pesado">
                                            </div>

                                            <div class="col-md-3">
                                                <label for="animais_pesados" class="control-label">Animais Pesados</label>
                                                <input type="text" class="form-control input-sm animais_pesados" readonly=""> 

                                                <input type="hidden" name="numero_doc" id="numero_doc">
                                                <input type="hidden" name="animais_pesados" id="animais_pesados">

                                            </div>
                                        </div>

                                        <hr>

                                        <div class="row form-group">
                                            <div class="form-group col-md-6">
                                                <label for="arquivo_excel"><span class="required">*</span> Informe o arquivo excel</label>
                                                <input type="file" class="form-control-file" name="arquivo_excel" required>                             
                                            </div>
                                        </div>
                                        <div class="row">  
                                            <div class="form-group col-xs-12 col-md-12">
                                                <button type="submit" class="btn btn-primary">Confirmar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-info" type="button">Voltar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_listar_grupo_destino" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title" id="modal_incluirLabel">Grupo de Pesagem - Indicar Novos Pastos</h3>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" id="form_importar_excel">
                                  
                                <div class="tab-content">

                                    <input type="hidden" id="id_pesagem">

                                    <div class="row">
                                        <div class="form-group col-xs-12 col-md-12">
                                            <div id="lista_grupos"></div>
                                        </div>
                                    </div>

                                    <div class="row">  
                                        <div class="form-group col-xs-12 col-md-12">
                                            <button type="button" class="btn btn-primary" onclick="imprimir_grupo_pesagem()">Imprimir PDF</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-info" type="button">Voltar</button>
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
                            <h4 class="modal-title">Pesagem </h4>
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
                            <h4 class="modal-title">Pesagem - Mensagem</h4>
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

 <!--main content end-->
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

<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js"></script>

<script src="js/pesagem.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao; ?>"></script>

<script>
    $(document).ready(function(){
       $('[data-toggle="tooltip"]').tooltip();   
    });
</script>

</body>

</html>