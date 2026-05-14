<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $numero_pesagem = $_REQUEST['id'];

    $tbl_pesagem = mysqli_query($conector, "select * from tbl_pesagem
        inner join tabela_epoca_pesagem
                on tbl_pesagem_codigo_epoca = tab_codigo_epoca_pesagem 
        where tbl_pesagem_id='$numero_pesagem'"); 

    $reg_pesagem = mysqli_fetch_object($tbl_pesagem);

    $nome_inclusao = utf8_decode($reg_pesagem->tbl_pesagem_incluido_por);
    $data_inclusao = new DateTime($reg_pesagem->tbl_pesagem_incluido_em);
    $incluido_por = $nome_inclusao . ' em ' . $data_inclusao->format('d/m/Y');

    $data_emissao = new DateTime($reg_pesagem->tbl_pesagem_data);
    $data_emissao_edi =$data_emissao->format('d/m/Y');

    $controle = $reg_pesagem->tbl_pesagem_controle;
    $codigo_local = $reg_pesagem->tbl_pesagem_codigo_local;
    $codigo_epoca = $reg_pesagem->tbl_pesagem_codigo_epoca;
    $lote = $reg_pesagem->tbl_pesagem_lote;
    $animais_pesados = intval($reg_pesagem->tbl_pesagem_qtd_animais_pesados);
    $peso_kg = number_format($reg_pesagem->tbl_pesagem_peso_kg,2,',','.');
    $peso_arroba = number_format($reg_pesagem->tbl_pesagem_peso_arroba,2,',','.');
    $peso_medio_kg = number_format($reg_pesagem->tbl_pesagem_peso_medio_kg,2,',','.');
    $peso_medio_arroba = number_format($reg_pesagem->tbl_pesagem_peso_medio_arroba,2,',','.');
    $filtros=$reg_pesagem->tbl_pesagem_filtros;
    $sexo = $reg_pesagem->tbl_pesagem_sexo;
    $pasto = $reg_pesagem->tbl_pesagem_pasto;
    $categoria = $reg_pesagem->tbl_pesagem_categoria;
    $num_movimentacao = $reg_pesagem->tbl_pesagem_codigo_movimentacao;
    $desc_motivo = $reg_pesagem->tab_descricao_epoca_pesagem;
    $data_sistema = date("Y-m-d");
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

  <style type="text/css">
    /* 1. Protege a coluna ID (primeira coluna) */
    #tabela_itens td.id_animal {
        min-width: 70px !important;
        width: 70px !important;
        white-space: nowrap !important;
        text-align: left !important;
    }

    #tabela_itens td.categoria,
    #tabela_itens th:nth-child(12) {
        white-space: nowrap !important;
        min-width: 90px !important;
    }

    /* 3. Garante que a tabela use todo o espaço sem esmagar as colunas */
    #tabela_itens {
        table-layout: auto !important;
        width: 100% !important;
    }    

    /* 4. Esconde qualquer ícone que o DataTables tente colocar na primeira coluna ou em outras */
    table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child:before,
    table.dataTable.dtr-inline.collapsed > tbody > tr > th:first-child:before {
        display: none !important;
    }

    /* 5. Garante que a coluna 12 seja o único lugar da bolinha */
    #tabela_itens td.dtr-control {
        cursor: pointer;
        position: relative;
        text-align: center !important;
    }

    /* 6. A Bolinha Azul (Redonda e Independente) */
    table.dataTable.responsive > tbody > tr > td.dtr-control:before {
        display: inline-block !important;
        content: '+' !important;
        background-color: #128cb8 !important;
        color: white !important;
        border-radius: 50% !important;
        width: 20px !important;
        height: 20px !important;
        line-height: 18px !important;
        text-align: center !important;
        font-size: 16px !important;
        font-weight: bold !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        /* Remove qualquer posicionamento absoluto que o plugin force */
        position: relative !important; 
        top: 0 !important;
        left: 0 !important;
    }

    /* 7. Bolinha aberta (Vermelha) */
    table.dataTable.responsive > tbody > tr.parent > td.dtr-control:before {
        content: '-' !important;
        background-color: #d9534f !important;
    }

    .codigo_id, .id_repetido {
        display: none !important;
    }

    /* Quando o botão tiver o atributo 'disabled', ele ignora a cor original */
    .btn.finalizar:disabled {
        background-color: #cccccc !important;
        border-color: #999999 !important;
        color: #666666 !important;
        cursor: not-allowed; /* Muda o mouse para aquele sinal de 'proibido' */
    }    

    .coluna_selecao_motivo {
        width: 35px !important;
        min-width: 35px !important;
        text-align: center !important;
        vertical-align: middle !important;
    }
    .check_item_motivo {
        cursor: pointer;
        transform: scale(1.05);
    }
   </style>

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
    ?>

    <section id="container" class="">
        <?php
            include "cabecalho.php"; 
            include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; 
            include "limpar_secao_selecao_matrizes.php"; 
            include "limpar_secao_compra_venda.php"; 
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_pesagem_animais.php"> Pesagem</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Consultar</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="icon_box-checked"></i> Pesagem - Consultar</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data" id="form_gravar_pedido">

                            <div class="panel"> 
                                <div class=panel-body>
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="container" id="dados_cliente">
                                            <div class="row">  
                                                <button type="button" class="btn btn-info pull-right" onclick="finalizar_sair()">Voltar</button>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="numero_pesagem_id" class="label_consulta">Nº do Documento:&nbsp;</label>
                                                    <span id="numero_pesagem_id"><?php echo $numero_pesagem;?></span>

                                                    <input type="hidden" id="consultar_pesagem" value="S">

                                                    <input type="hidden" id="local_pesagem"
                                                    <?php echo "value='".$codigo_local."'";?>>

                                                    <input type="hidden" id="descricao_lote" <?php echo "value='".$lote."'";?>>

                                                    <input type="hidden" id="descricao_filtro" <?php echo "value='".$filtros."'";?>>

                                                    <input type="hidden" id="data_pesagem" <?php echo "value='".$reg_pesagem->tbl_pesagem_data."'";?>>

                                                </div>

                                                <div class="col-md-4">
                                                    <label class="label_consulta">Data da Pesagem:&nbsp;</label>
                                                    <span><?php echo $data_emissao_edi;?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="label_consulta">Incluido por:&nbsp;</label>
                                                    <span id="includido_por" ><?php echo $incluido_por;?></span>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="label_consulta">Lote:&nbsp;</label>
                                                    <span><?php echo $lote;?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="label_consulta">Motivo:&nbsp;</label>
                                                    <span><?php echo $desc_motivo;?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="num_movimentacao" class="label_consulta">Nº da Movimentação:&nbsp;</label>
                                                    <span id="num_movimentacao" ><?php echo $num_movimentacao;?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p class="text-muted-dark descricao_filtro" style="font-size: 12px; color:lightgray;"><?php echo 'Filtro: ' . $filtros;?></p>
                                                </div>
                                            </div>

                                            <hr align="center"> 

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="label_consulta">Animais Pesados:&nbsp;</label> <span ><?php echo $animais_pesados;?></span>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="label_consulta">Pesos: &nbsp; Total Kg:&nbsp;</label><span>
                                                    <?php echo $peso_kg;?>
                                                    </span>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="label_consulta">Arrobas:&nbsp;</label><span><?php echo $peso_arroba;?></span>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="label_consulta">Médio Kg:&nbsp;</label><span>
                                                    <?php echo $peso_medio_kg;?>
                                                    </span>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="label_consulta">Médio Arrobas:&nbsp;</label><span><?php echo $peso_medio_arroba;?></span>
                                                </div>
                                            </div>

                                        <div class="row">
                                            <div class="col-md-12">
<table class="table table-striped table-advance table-hover" id="tabela_itens" style="font-size: 12px; width:100%;">
    <thead>
        <tr>
            <th class="coluna_selecao_motivo" style="width:35px; text-align:center;">
                <input type="checkbox" id="check_todos_motivo">
            </th>
            <th style="vertical-align: middle;"> Id</th>
            <th style="vertical-align: middle;"> Pesagem</th>
            <th style="vertical-align: middle; width: 80px; text-align: center;"> Ganho de Peso</th>
            <th style="vertical-align: middle; width: 80px; text-align: center;"> Último Peso</th>
            <th style="vertical-align: middle; width: 80px; text-align: center;"> Data Último Peso</th>
            <th style="vertical-align: middle;"> Sexo</th>
            <th style="vertical-align: middle;"> Nascimento</th>
            <th style="vertical-align: middle;"> Apartação</th>
            <th style="vertical-align: middle; width: 80px; text-align: center;"> Observação da Pesagem</th>
            <th style="vertical-align: middle; width: 80px; text-align: center;"> Mãe</th>
            <th style="vertical-align: middle; width: 80px; text-align: center;"> Categoria</th>
            <th style="vertical-align: middle;"></th>
            <th style="vertical-align: middle;"> Idade em Meses</th>
            <th style="vertical-align: middle;"> Raça</th>
            <th style="vertical-align: middle;"> Pelagem</th>
            <th style="vertical-align: middle;"> Pai</th>
            <th style="vertical-align: middle;"> Observação</th>
            <th hidden="">ID Oculto</th> 
            <th hidden="">Lote(s)</th>                    
            <th hidden="">Id Repetido</th>                    
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>                                    </div>
                                        </div>                                                        
                                        <input type="hidden" name="array_itens" id="array_itens">    

                                                <hr align="center"> 

                                                <div class="row">  
                                                    <?php
                                                        echo '<button type="button" class="btn btn-info pull-right" onclick="finalizar_sair()">Voltar</button>';
                                                    ?>
                                                </div>
                                            </div> <!-- fim container -->
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
                            <h4 class="modal-title">Pesagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair()">Fechar</button>
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

            <div class="modal fade" id="modal_novo_motivo" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Incluir Pesagem Novo Motivo</h4>
                        </div>
                        <div class="modal-body">
                            
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success" type="button" id="btn_modal_confirmar_novo_motivo">Confirmar
                            </button>

                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_filtro_apartacao" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Quantidade de Animais por Apartação</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
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
            
        </section> <!-- wrapper -->
    </section><!--main-content -->


<?php 
  $javascript_file_name = 'pesagem_consulta.js';
  require 'rodape.php';
?>




