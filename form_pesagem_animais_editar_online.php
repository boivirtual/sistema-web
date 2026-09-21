<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $numero_pesagem = $_REQUEST['id'];

    $tbl_pesagem = mysqli_query($conector, "select * from tbl_pesagem
        where tbl_pesagem_id='$numero_pesagem'"); 

    $reg_pesagem = mysqli_fetch_object($tbl_pesagem);

    $nome_inclusao = $reg_pesagem->tbl_pesagem_incluido_por;
    $data_inclusao = new DateTime($reg_pesagem->tbl_pesagem_incluido_em);
    $incluido_por = $nome_inclusao . ' em ' . $data_inclusao->format('d/m/Y');
    $data_emissao = $reg_pesagem->tbl_pesagem_data;
    $codigo_local = $reg_pesagem->tbl_pesagem_codigo_local;
    $codigo_epoca = $reg_pesagem->tbl_pesagem_codigo_epoca;
    $lote = $reg_pesagem->tbl_pesagem_lote;
    $peso_kg = $reg_pesagem->tbl_pesagem_peso_kg;
    $peso_arroba = $reg_pesagem->tbl_pesagem_peso_arroba;
    $peso_medio_kg = $reg_pesagem->tbl_pesagem_peso_medio_kg;
    $peso_medio_arroba = $reg_pesagem->tbl_pesagem_peso_medio_arroba;
    $filtros=$reg_pesagem->tbl_pesagem_filtros;

    $rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
        WHERE tbl_ite_pesagem_numero_id='$numero_pesagem' AND 
              tbl_ite_pesagem_peso!=0");
    $animais_pesados = mysqli_num_rows($rs);

    $rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
        WHERE tbl_ite_pesagem_numero_id='$numero_pesagem'");
    $qtd_itens = mysqli_num_rows($rs);

    $tbl_epoca_pesagem = mysqli_query($conector, "select * from tabela_epoca_pesagem where tab_registro_lixeira_epoca_pesagem=0"); 
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
  <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 

  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">
  <style type="text/css">
    /* 1. Protege a coluna ID (primeira coluna) */
    #tabela_itens td:nth-child(2), 
    #tabela_itens th:nth-child(2) {
        min-width: 60px !important;
        white-space: nowrap !important;
        text-align: left !important;
    }
    /* 2. Ajuste na coluna Categoria (para não espremer o ID) */
    #tabela_itens td:nth-child(10) {
        white-space: nowrap !important;
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
        display: none;
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
    $controle_estoque= $_SESSION['controle_estoque'];

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
            include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
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
            <span class="titulo">Editar</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="icon_box-checked"></i> Pesagem - Digitação Lista On-line</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="row col-md-12" id="selecionar_pasagem">
                        <form method="POST" action="#" enctype="multipart/form-data" id="form_gravar_pesagem">

                            <input type="hidden" id="editar_online"
                            value="S">

                            <input type="hidden" name="controle_estoque" id="controle_estoque"
                            <?php echo "value='".$controle_estoque."'";?>>

                            <input name="numero_pesagem_id" type="hidden" id="numero_pesagem_id"
                            <?php echo "value='".$numero_pesagem."'";?>>

                            <input name="local_pesagem" type="hidden" id="local_pesagem"
                            <?php echo "value='".$codigo_local."'";?>>

                            <input name="epoca_pesagem" type="hidden" id="epoca_pesagem"
                            <?php echo "value='".$codigo_epoca."'";?>>

                            <input  name="finalizar_pesagem" type="hidden" id="finalizar_pesagem" value="N">

                            <input  name="tipo_gravacao" type="hidden" id="tipo_gravacao" value="2">

                            <input  name="tipo_pesagem" type="hidden" id="tipo_pesagem" value="ONLINE">

                            <div class="tab-panel">
                                <div class="tab-pane active table-responsive">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Animais Pesados</legend>
                                            
                                                        <div class="row">
                                                            <div class="form-group col-md-7">
                                                                <p class="text-muted-dark descricao_filtro" style="font-size: 11px; color:lightgray;"></p>

                                                                <input type="hidden" name="descricao_filtro" class="descricao_filtro">
                                                            </div>

                                                            <div class="form-group col-md-5">
                                                                <button type="button" class="btn btn-success" onclick="continuar_pesagem()" data-toggle='tooltip' data-placement='top' title="Continuar digitando os pesos"><i class="fas fa-weight"></i> Pesar
                                                                </button>

                                                                <button type="button" class="btn btn-primary finalizar" onclick="terminar_pesagem()">Finalizar Pesagem</button>

                                                                <button type="button" class="btn btn-info pull-right" data-toggle='tooltip' data-placement='top' title="Você poderá continuar a digitação mais tarde" onclick="fecha_consultar_pesagem()">Sair sem Finalizar</button> 
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"><span class="required">*</span> Data </label>
                                                                <input class="form-control" type="date" id="data_pesagem" name="data_pesagem">
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"><span class="required">*</span> Motivo da Pesagem </label>

                                                            <select class="form-control" name="epoca_pesagem" id="epoca_pesagem">

                                                            <?php 

                                                            while($reg_ep = mysqli_fetch_object($tbl_epoca_pesagem)) { ?>

                                                                <option value="<?php 
                                                                    echo $reg_ep->tab_codigo_epoca_pesagem ?>"
                                                                        
                                                                        <?php 
                                                                            if ($codigo_epoca==$reg_ep->tab_codigo_epoca_pesagem) { 
                                                                                echo "selected";      
                                                                            }
                                                                ?>>
                                                                                
                                                                <?php 
                                                                echo $reg_ep->tab_descricao_epoca_pesagem;
                                                                ?>
                                                                </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <label class="control-label"><span class="required">*</span> Lote</label>

                                                                <input class="form-control descricao_lote" type="text" name="descricao_lote" id="descricao_lote" maxlength="100">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Animais para Pesar:&nbsp;
                                                                <span class="total_a_pesar" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="total_a_pesar" id="total_a_pesar" class="total_a_pesar" >
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Animais Pesados:&nbsp;
                                                                <span class="total_pesados" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="total_pesados" id="total_pesados" class="total_pesados">
                                                            </div>
                                                        </div>

                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Peso Total Kg:&nbsp;
                                                                <span class="peso_total_kg" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_total_kg" class="peso_total_kg">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Peso Total @:&nbsp;
                                                                <span class="peso_total_arroba" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_total_arroba" class="peso_total_arroba">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Peso Médio Kg:&nbsp;
                                                                <span class="peso_medio_kg" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_medio_kg" class="peso_medio_kg">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Peso Médio @:&nbsp;
                                                                <span class="peso_medio_arroba" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_medio_arroba" class="peso_medio_arroba">
                                                            </div>
                                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
<table class="table table-striped table-advance table-hover" id="tabela_itens" style="font-size: 12px; width:100%;">
    <thead>
        <tr>
            <th class="coluna_selecao_motivo" style="display:none; width:35px; text-align:center;">
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
            <th style="vertical-align: middle;"> <i class="icon_cogs"></i> Ações</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>                                            </div>
                                        </div>                                                        
                                        <input type="hidden" name="array_itens" id="array_itens">    

                                        <div class="row">
                                            <div class="col-md-7">
                                            </div>

                                            <div class="form-group col-md-5 botoes_final">
                                                <button type="button" class="btn btn-success" onclick="continuar_pesagem()" data-toggle='tooltip' data-placement='top' title="Continuar digitando os pesos"><i class="fas fa-weight"></i> Pesar
                                                </button>

                                                <button type="button" class="btn btn-primary finalizar" onclick="terminar_pesagem()">Finalizar Pesagem</button>

                                                <button type="button" class="btn btn-info pull-right" data-toggle='tooltip' data-placement='top' title="Você poderá continuar a digitação mais tarde" onclick="fecha_consultar_pesagem()">Sair sem Finalizar</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div> 
                            </div>
                        </form>
                    </div> <!-- selecionar_pasagem -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->

            <div class="modal fade" id="modal_pesar_individual" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-lg modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <!--    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
                            <h4 class="modal-title" id="modal_incluirLabel">Pesagem - Individual - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" >
                                <input name="codigo_id" type="hidden" id="codigo_id" value="0">
                                <input name="sexo_animal" type="hidden" id="sexo_animal">
                                <input name="nascimento_animal" type="hidden" id="nascimento_animal">
                                <input name="raca_animal" type="hidden" id="raca_animal">
                                <input name="pelagem_animal" type="hidden" id="pelagem_animal">
                                <input name="mae_animal" type="hidden" id="mae_animal">
                                <input name="pai_animal" type="hidden" id="pai_animal">
                                <input name="desc_categoria" type="hidden" id="desc_categoria">

                                <input name="mens_repetido" type="hidden" id="mens_repetido">
                                <input name="id_repetido" type="hidden" id="id_repetido">

                                <input name="idade_meses" type="hidden" id="idade_meses">
                                <input name="observacao_cadastro" type="hidden" id="observacao_cadastro">

                                <div class="tab-content">
                                    <div class="alert alert-danger alert_erro_animal" id="alert_erro_animal" hidden="true">
                                        <strong class="negrito"></strong><span></span>
                                    </div> 

                                    <div id="dados" class="tab-pane active">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="lote" class="control-label"><span class="required">*</span> Descrição do Lote</label>
                                                <input name="lote" type="text" class="form-control" id="lote" maxlength="100"
                                                >
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="qtd_a_pesar" class="control-label"><span class="required">*</span> Animais para Pesar</label>
                                                <input name="qtd_a_pesar" type="number" class="form-control" id="qtd_a_pesar">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="qtd_pesado" class="control-label">Animais Pesados</label>
                                                <input name="qtd_pesado" type="number" class="form-control" id="qtd_pesado" readonly="">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="codigo_number_filtro" class="control-label"><span class="required">*</span> Id Animal</label>
                                                <input name="codigo_number_filtro" type="text" class="form-control" id="codigo_number_filtro" autocomplete="off"
                                                onchange="ler_animal_editar_online()" >
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label for="peso_animal" class="control-label"><span class="required">*</span> Peso (Kg)</label>
                                                <input name="peso_animal" type="number" class="form-control" id="peso_animal" onkeypress = "return desabilita_enter (this, event)">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label"> Apartação</label>

                                                <select class="form-control" name="apartacao_item" id="apartacao_item">
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="observacao" class="control-label">Observação</label>
                                                <input name="observacao" type="text" class="form-control" id="observacao" maxlength="100">
                                            </div>


                                            <div class="form-group col-md-2" id="incluir">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn-success" id="btn_salvar" onClick="SalvarIncluirEdicao()">Confirmar</button>
                                            </div>

                                            <div class="form-group col-md-2" id="editar" hidden="" >
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn-success" onClick="Salvar_editar()">Confirmar</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <p id="descricao_animal" class="text-primary"></p>
                                            </div>

                                            <div class="form-group col-md-12" style="text-align: center;">
                                                <span id="ultimo_peso" class="text-success" style="font-size: 20px; font-weight: 600;"></span>
                                                <span id="desc_descarte" class="text-danger" style="font-size: 20px; font-weight: 600;"></span>
                                                <p id="data_ult_peso" class="text-success" style="font-size: 12px; font-weight: 300;"></p>
                                                <input type="hidden" id="ult_peso_calculo">
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-md-2">
                                                <button type="button" class="btn btn-primary" onclick="pausar_pesagem()">Pausar Pesagem</button>
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
                        <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_excluir" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Pesagem</h4>
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
                            <h4 class="modal-title">Pesagem - Mesagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_finalizar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Pesagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-success" type="button" onclick="gravar_pesagem_finalizar();">Sim

                            <button data-dismiss="modal" class="btn btn-danger" type="button">Não
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


            <div class="modal fade" id="mensagem_erro_animal_filtro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                                    <p class="desc_modal">FALTA VALIDAR O CÓDIGO DO ANIMAL.</p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_1">Após digitar número, selecione o código na LISTA SUSPENSA.</span></p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_2">Se não aparecer o codigo na Lista Suspensa é porque o animal não existe na fazenda.</span></p>
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="qualModal">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
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
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js"></script>

<script src="js/pesagem.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
<script src="js/typeahead.js?<?php echo Versao; ?>"></script>

    <script>
        $(document).ready(function(){
           $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>

    <script>
        var mask = {
             money: function() {
                var el = this
                ,exec = function(v) {
                v = v.replace(/\D/g,"");
                v = new String(Number(v));
                var len = v.length;
                if (1== len)
                v = v.replace(/(\d)/,"0.0$1");
                else if (2 == len)
                v = v.replace(/(\d)/,"0.$1");
                else if (len > 2) {
                v = v.replace(/(\d{2})$/,'.$1');
                }
                return v;
                };

                setTimeout(function(){
                el.value = exec(el.value);
                },1);
             }
        }

        $(document).ready(function(){
             $('#codigo_number_filtro').typeahead({
                source: function(query, result) {  
                    $.ajax({
                        url:"fetch.php",
                        method:"POST",
                        data:{query:query,
                              local: $('#local_pesagem').val()},
                        dataType:"json",
                        success:function(data)
                        {
                            result($.map(data, function(item){
                            return item;
                        }));
                        }
                    })
                }
            });

            $("#codigo_number_filtro").click(function(){
                $('#codigo_number_filtro').val('');
                $("#codigo_id").val(0);                
                $("#ultimo_peso").text('');
                $("#data_ult_peso").text('');
                $("#desc_descarte").text('');
                $("#descricao_animal").text('');
                $("#alert_erro_animal .negrito").html('');
                $("#alert_erro_animal span").html('');
                $(".alert_erro_animal").hide();
                document.getElementById("codigo_number_filtro").style.borderColor = "";

                return false;
            });

            $("#peso_animal").click(function(){
                $("#peso_animal").val('');
                $("#alert_erro_animal .negrito").html('');
                $("#alert_erro_animal span").html('');
                $(".alert_erro_animal").hide();
                return;
            });

        });

    </script>

</body>
</html>
