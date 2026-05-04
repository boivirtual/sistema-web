<?php
    include "conecta_mysql.inc";

    $numero_mov = $_REQUEST['id'];

    $tbl_movimentacao = mysqli_query($conector, "select * from tbl_movimentacao
                                                where tbl_movimentacao_id ='$numero_mov'"); 

    $reg_mov = mysqli_fetch_object($tbl_movimentacao);

    $nome_inclusao = $reg_mov->tbl_movimentacao_incluido_por;
    $data_movimentacao = $reg_mov->tbl_movimentacao_data;
    $data_inclusao = new DateTime($reg_mov->tbl_movimentacao_incluido_em);
    $incluido_por = $nome_inclusao . ' em ' . $data_inclusao->format('d/m/Y');

    $data_emissao = new DateTime($reg_mov->tbl_movimentacao_data);
    $data_emissao_edi = $data_emissao->format('d/m/Y');

    $codigo_local_origem = $reg_mov->tbl_movimentacao_codigo_local_origem;
    $codigo_local_destino = $reg_mov->tbl_movimentacao_codigo_local_destino;
    $codigo_pesagem = $reg_mov->tbl_movimentacao_codigo_pesagem;
    $peso_kg = $reg_mov->tbl_movimentacao_peso_kg;
    $peso_arroba = $reg_mov->tbl_movimentacao_peso_arroba;
    $peso_medio_kg = $reg_mov->tbl_movimentacao_peso_medio_kg;
    $peso_medio_arroba = $reg_mov->tbl_movimentacao_peso_medio_arroba;
    $filtros=$reg_mov->tbl_movimentacao_filtros;
    $animais_digitados = $reg_mov->tbl_movimentacao_qtd_animais_pesados;
    $tipo_movimentacao = $reg_mov->tbl_movimentacao_tipo;

    switch ($tipo_movimentacao) {
        case 003:
            $tipo_mov = 'V';
            $desc_tipo = 'Venda';
            break;
        case 004:
            $tipo_mov = 'C';
            $desc_tipo = 'Compra';
            break;
        case 005:
            $tipo_mov = 'T';
            $desc_tipo = 'Tranferência';
            break;
    }

    $data_sistema = date("Y-m-d");

    $rs = mysqli_query($conector, "SELECT * FROM tbl_pessoa
                                           WHERE tbl_pessoa_id ='$codigo_local_origem'");
    $num_rows = mysqli_num_rows($rs);
    if ($num_rows!=0) {
        $reg_origem = mysqli_fetch_object($rs);
        $desc_origem = $reg_origem->tbl_pessoa_nome;
    }
    else {
        $desc_origem ='';
    }

    $rs = mysqli_query($conector, "SELECT * FROM tbl_pessoa
                                           WHERE tbl_pessoa_id='$codigo_local_destino'");
    $num_rows = mysqli_num_rows($rs);
    if ($num_rows!=0) {
        $reg_destino = mysqli_fetch_object($rs);
        $desc_destino = $reg_destino->tbl_pessoa_nome;
    }
    else {
        $desc_destino ='';
    }

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

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>
    <?php
        @ session_start();   

        if(isset($_SESSION['menu_manejo_animais'])) {
            $array_cadastro = explode("!",$_SESSION['menu_manejo_animais']);

            if ($array_cadastro[2] == 0){
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

        $controle_estoque = $_SESSION['controle_estoque'];
    ?>

    <section id="container" class="">
        <?php
            include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; 
            include "limpar_secao_compra_venda.php"; 
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php"; 
            include "limpar_secao_pesagem.php"; 
            include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_movimentacao_animais.php"> Movimentações</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Editar</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="icon_box-checked"></i> Movimentações - Editar</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data" id="form_gravar">

                            <div class="panel"> 
                                <div class=panel-body>
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="container" id="dados_cliente">

                                            <input  name="codigo_pesagem" type="hidden" id="epoca_pesagem" <?php echo "value='".$codigo_pesagem."'";?>>

                                            <input type="hidden" name="controle_estoque" id="controle_estoque"
                                            <?php echo "value='".$controle_estoque."'";?>>

                                            <input  name="finalizar_pesagem" type="hidden" id="finalizar_pesagem" value="N">

                                            <input  name="tem_itens_gravar" type="hidden" id="tem_itens_gravar" value="S">

                                            <input  name="tipo_movimentacao" type="hidden"  <?php echo "value='".$tipo_mov."'";?>>
                                            
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label for="num_orc" class="label_consulta">Nº do Documento:&nbsp;</label>
                                                    <span id="num_orc"><?php echo $numero_mov;?></span>

                                                    <input name="numero_movimentacao_id" type="hidden" id="numero_movimentacao_id" <?php echo "value='".$numero_mov."'";?>>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="includido_por" class="label_consulta">Incluido por:&nbsp;</label>
                                                    <span id="includido_por" ><?php echo $incluido_por;?></span>

                                                    <input type="hidden" name="data_movimentacao" <?php echo "value='".$data_movimentacao."'";?>>
                                                </div>
                                            </div>

                                            <div class="row"> 
                                                <div class="col-md-8">
                                                    <label class="label_consulta">Filtros:&nbsp;</label> <span><?php echo $filtros;?></span>

                                                    <input type="hidden" name="descricao_filtro_dig" class="descricao_filtro"
                                                    <?php echo "value='".$filtros."'";?>>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="label_consulta">Movimentação:&nbsp;</label>
                                                    <span><?php echo $desc_tipo;?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="label_consulta">Emissão:&nbsp;</label>
                                                    <span><?php echo $data_emissao_edi;?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label class="label_consulta">Origem:&nbsp;</label>
                                                    <span><?php echo $desc_origem;?></span>
                                                    <input type="hidden" name="local_origem"
                                                    <?php echo "value='".$codigo_local_origem."'";?>>     

                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label class="label_consulta">Destino:&nbsp;</label>
                                                    <span><?php echo $desc_destino;?></span>
                                                    <input type="hidden" name="local_destino" <?php echo "value='".$codigo_local_destino."'";?>>     
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-10">
                                                    <label class="label_consulta">Qtde Animais:&nbsp;</label> <span ><?php echo $animais_digitados;?></span>
                                                    <input type="hidden" name="total_digitados" 
                                                        <?php echo "value='".$animais_digitados."'";?>>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Total Kg:&nbsp;</label><span>
                                                    <?php echo number_format($peso_kg,2,',','.');?>
                                                    </span>
                                                    <input type="hidden" name="peso_total_kg" class="peso_total_kg"
                                                        <?php echo "value='".$peso_kg."'";?>>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Total Arrobas:&nbsp;</label><span><?php echo number_format($peso_arroba,2,',','.');?></span>
                                                    <input type="hidden" name="peso_total_arroba" class="peso_total_arroba"
                                                        <?php echo "value='".$peso_arroba."'";?>>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Médio Kg:&nbsp;</label><span>
                                                    <?php echo number_format($peso_medio_kg,2,',','.');?>
                                                    </span>
                                                    <input type="hidden" name="peso_medio_kg" class="peso_medio_kg" <?php echo "value='".$peso_medio_kg."'";?>>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Médio Arrobas:&nbsp;</label><span><?php echo number_format($peso_medio_arroba,2,',','.');?></span>
                                                    <input type="hidden" name="peso_medio_arroba" class="peso_medio_arroba"
                                                        <?php echo "value='".$peso_medio_arroba."'";?>>
                                                </div>
                                            </div>

                                            <hr align="center"> 

                                            <div class="row">
                                                <div class="form-group col-md-9">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <button type="button" class="btn btn-primary" onclick="finalizar_movimentacao_editar()">Aplicar Modificações
                                                    </button>

                                                    <button type="button" class="btn btn-info pull-right" onclick="finalizar_sair()">Voltar</button>
                                                </div>
                                            </div>

                                            <table class="table table-striped table-advance table-hover" id="tabela_itens_digitados" width="100%">

                                                <thead>
                                                    <tr>
                                                        <th> Id</th>
                                                        <th> Categoria</th>
                                                        <th> Peso</th>
                                                        <th> Sexo</th>
                                                        <th> Nascimento</th>
                                                        <th> Raça</th>
                                                        <th> Pelagem</th>
                                                        <th> Mãe</th>
                                                        <th> Observação</th> 
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                </tbody>
                                            </table>

                                            <input type="hidden" name="array_itens" id="array_itens">                                            
                                            </div> <!-- fim container -->
                                        </div> <!-- dados-->
                                    </div> <!--tab-content -->

                                </div> <!--panel-body -->
                            </div> <!--panel -->      
                        </form>
                    </section> <!-- panel-group -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->

            <div class="modal fade" id="modal_individual" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered style-responsive" role="document" style="width: 800px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Movimentação - Individual - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" >
                                <input name="codigo_id" type="hidden" id="codigo_id" value="0">
                                <input name="sexo_animal" type="hidden" id="sexo_animal">
                                <input name="nascimento_animal" type="hidden" id="nascimento_animal">
                                <input name="raca_animal" type="hidden" id="raca_animal">
                                <input name="pelagem_animal" type="hidden" id="pelagem_animal">
                                <input name="mae_animal" type="hidden" id="mae_animal">

                                <div class="tab-content">
                                    <div class="alert alert-danger alert_erro_animal" id="alert_erro_animal" hidden="true">
                                        <strong class="negrito"></strong><span></span>
                                    </div> 

                                    <div id="dados" class="tab-pane active">
                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="qtd_a_digitar" class="control-label">Quantidade de Animais</label>
                                                <input name="qtd_a_digitar" type="number" class="form-control" id="qtd_a_digitar" readonly="" <?php echo "value='".$animais_digitados."'";?>>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="qtd_digitado" class="control-label">Animais Digitados</label>
                                                <input name="qtd_digitado" type="number" class="form-control" id="qtd_digitado" readonly="" <?php echo "value='".$animais_digitados."'";?>>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="id_animal" class="control-label"><span class="required">*</span> Id Animal</label>
                                                <input name="id_animal" type="text" class="form-control" id="id_animal" readonly="" >
                                            </div>

                                            <div class="form-group col-md-5">
                                                <label for="observacao" class="control-label">Observação</label>
                                                <input name="observacao" type="text" class="form-control" id="observacao" maxlength="100"
                                                onkeyup="maiuscula(this)">
                                            </div>

                                            <div class="form-group col-md-2" id="editar">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn-success" onClick="salvar_editar_edicao()">Confirmar</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <p id="descricao_animal" class="text-primary"></p>
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-md-2">
                                                <button type="button" class="btn btn-primary" onclick="pausar_digitacao()">Pausar Digitação</button>
                                            </div>
                                        </div>

                                    </div> <!-- fim tab-pane active-->
                                </div> <!-- Fim tab-content-->
                            </form>
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
                            <h4 class="modal-title">Pesagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default fecha_editar_dados" type="button" onclick="finalizar_sair()">Fechar</button>
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
                            <h4 class="modal-title">Pesagem - Erro</h4>
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


<?php 
  $javascript_file_name = 'movimentacao.js';
  require 'rodape.php';
?>




