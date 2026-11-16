<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $numero_venda = $_REQUEST['id'];

    $tbl_venda = mysqli_query($conector, "select * from tbl_venda
        inner join tbl_pessoa
                on tbl_pessoa_id = tbl_venda_codigo_local_origem
             where tbl_venda_id ='$numero_venda'"); 

    $reg_venda = mysqli_fetch_object($tbl_venda);

    $tipo = $reg_venda->tbl_venda_categoria; 

    $nome_inclusao = $reg_venda->tbl_venda_incluido_por;
    $data_inclusao = new DateTime($reg_venda->tbl_venda_incluido_em);
    $incluido_por = $nome_inclusao . ' em ' . $data_inclusao->format('d/m/Y');

    $data_emissao = new DateTime($reg_venda->tbl_venda_emissao);
    $data_emissao_edi = $data_emissao->format('d/m/Y');
    $data_emissao = $reg_venda->tbl_venda_emissao;

    $codigo_local_origem = $reg_venda->tbl_venda_codigo_local_origem;
    $desc_origem = $reg_venda->tbl_pessoa_nome;
    $codigo_local_destino = $reg_venda->tbl_venda_codigo_local_destino;
    $codigo_tipo = $reg_venda->tbl_venda_tipo;
    $total_venda = $reg_venda->tbl_venda_total_venda;
    $total_desconto = $reg_venda->tbl_venda_total_desconto;
    $total_receber = $reg_venda->tbl_venda_total_receber;
    $conta_contabil = $reg_venda->tbl_venda_conta_contabil;
    $gta = $reg_venda->tbl_venda_gta;
    $transporte = $reg_venda->tbl_venda_nome_transportadora;
    $motorista = $reg_venda->tbl_venda_dados_motorista;

    $vlr_pri_parcela = $reg_venda->tbl_venda_valor_primeira_parcela;
    $vencimento_pri_parcela =  new DateTime($reg_venda->tbl_venda_vencimento_primeira_parcela);
    $vencimento_edi = $vencimento_pri_parcela->format('d/m/Y');
    $forma_pag_pri = $reg_venda->tbl_venda_forma_pgto_primeira_parcela;
    $conta_pag_pri = $reg_venda->tbl_venda_conta_pgto_primeira_parcela;

    $array_itens = $reg_venda->tbl_venda_array_itens;
    $matriz_itens = explode("<|>", $array_itens);
    $quantidade_itens = count($matriz_itens);

    $array_parcelas = $reg_venda->tbl_venda_array_parcelas;
    $matriz_parcelas = explode("<|>", $array_parcelas);
    $quantidade_parcelas = count($matriz_parcelas);

    $tbl_forma = mysqli_query($conector, "select * from tbl_forma_pagamento
             where tbl_forma_pagamento_id ='$forma_pag_pri'"); 
    $num_rows = mysqli_num_rows($tbl_forma);

    if ($num_rows!=0) {
        $reg_forma = mysqli_fetch_object($tbl_forma);
        $desc_forma_pri = $reg_forma->tbl_forma_pagamento_descricao;
    }
    else {
        $desc_forma_pri = '';
    }

    $tbl_conta = mysqli_query($conector, "select * from tbl_conta_pagamento
             where tbl_conta_pagamento_id  ='$conta_pag_pri'"); 
    $num_rows = mysqli_num_rows($tbl_conta);

    if ($num_rows!=0) {
        $reg_conta = mysqli_fetch_object($tbl_conta);
        $desc_conta_pri = $reg_conta->tbl_conta_pagamento_descricao;
    }
    else {
        $desc_conta_pri = '';
    }

    $tbl_destino = mysqli_query($conector, "select * from tbl_pessoa
             where tbl_pessoa_id='$codigo_local_destino'"); 
    $num_rows = mysqli_num_rows($tbl_destino);

    if ($num_rows!=0) {
        $reg_destino = mysqli_fetch_object($tbl_destino);
        $desc_destino = $reg_destino->tbl_pessoa_nome;
    }
    else {
        $desc_destino = '';
    }

    $tbl_plano_contas = mysqli_query($conector, "select * from tbl_plano_contas
             where tbl_plano_contas_codigo_id ='$conta_contabil'"); 
    $num_rows = mysqli_num_rows($tbl_plano_contas);

    if ($num_rows!=0) {
        $reg_conta = mysqli_fetch_object($tbl_plano_contas);
        $desc_plano_contas = $reg_conta->tbl_plano_contas_descricao;
    }
    else {
        $desc_plano_contas = '';
    }

    switch ($codigo_tipo) {
        case 'V':
            $desc_tipo = 'Peso Vivo';
            break;
        case 'M':
            $desc_tipo = 'Peso Morto';
            break;
        case 'C':
            $desc_tipo = 'Cabeça';
            break;
    }

    $aceite = 'Nº do Documento: ' . $reg_venda->tbl_venda_codigo_movimentacao;

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

</head>

<body>
    <?php
    @ session_start();   

    if(isset($_SESSION['menu_gestao_adm'])) {
        $array_cadastro = explode("!",$_SESSION['menu_gestao_adm']);

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
    ?>

    <section id="container" class="">
        <?php
            include "cabecalho.php";
            include "opcoes_menu.php"; 
            include "limpar_secao_selecao_matrizes.php";
            include "limpar_secao_ctp.php";
            include "limpar_secao_ctr.php";
            include "limpar_secao_pesagem.php"; 
            include "limpar_secao_movimentacao.php"; 
            include "limpar_secao_nutricao.php"; 
            include "limpar_secao_nascimento.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_compra_venda_animais.php"> Compra/Venda Animais</a> <i class="fa fa-angle-right seta-direita"></i>

            <?php
                if ($tipo==2) {
                    echo '<span class="titulo">Compra - Consultar</span></span>';
                }
                else {
                    echo '<span class="titulo">Venda - Consultar</span></span>';
                }
            ?>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">

                <?php
                    if ($tipo==2) {
                        echo '<h3 class="page-header"><i class="fa fa-shopping-cart"></i>Compra - Consultar</h3>';
                    }
                    else {
                        echo '<h3 class="page-header"><i class="fa fa-shopping-cart"></i>Venda - Consultar</h3>';
                    }
                ?>

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
                                                <div class="col-md-4">
                                                    <label for="num_orc" class="label_consulta">Nº do Documento:&nbsp;</label>
                                                    <span id="num_orc"><?php echo $numero_venda;?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="label_consulta">Emissão:&nbsp;</label>
                                                    <span><?php echo $data_emissao_edi;?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="includido_por" class="label_consulta">Incluido por:&nbsp;</label>
                                                    <span id="includido_por" ><?php echo $incluido_por;?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label for="faturado_em" class="label_consulta">Movimentação:&nbsp;</label>
                                                    <span id="faturado_em" ><?php echo $aceite;?></span>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="label_consulta">Conta Contábil:&nbsp;</label>
                                                    <span><?php echo $desc_plano_contas;?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="label_consulta">Tipo da Venda:&nbsp;</label>
                                                    <span><?php echo $desc_tipo ;?></span>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label class="label_consulta">Origem:&nbsp;</label>
                                                    <span><?php echo $desc_origem;?></span>
                                                </div>
                                                <div class="form-group col-md-5">
                                                    <label class="label_consulta">Destino:&nbsp;</label>
                                                    <span><?php echo $desc_destino;?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="label_consulta">Total Venda:&nbsp;</label>
                                                    <span><?php echo number_format($total_venda,2,',','.');?></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="label_consulta">Desconto:&nbsp;</label>
                                                    <span><?php echo number_format($total_desconto,2,',','.');?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="label_consulta">Total Receber:&nbsp;</label>
                                                    <span><?php echo number_format($total_receber,2,',','.');?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label class="label_consulta">Nº GTA:&nbsp;</label>
                                                    <span><?php echo $gta;?></span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="label_consulta">Transporte:&nbsp;</label>
                                                    <span><?php echo $transporte;?></span>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label class="label_consulta">Dados Motorista:&nbsp;</label>
                                                    <span><?php echo $motorista;?></span>
                                                </div>
                                            </div>

                                            <table class="table table-striped table-advance table-hover" id="tabela_prazos_consulta" width="100%" >

                                                <thead>
                                                    <tr>
                                                        <th> Prazo</th>
                                                        <th> Vencimento</th>
                                                        <th> Parcela </th>
                                                        <th> Forma Pgto</th>
                                                        <th> Conta Pgto</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <tr>
                                                        <td>Primeira Parcela</td>
                                                        <td><?php echo $vencimento_edi;?></td>
                                                        <td><?php echo number_format($vlr_pri_parcela,2,',','.');?></td>
                                                        <td><?php echo $desc_forma_pri;?></td>
                                                        <td><?php echo $desc_conta_pri;?></td>
                                                    </tr>
                                                    <?php 
                                                        if ($array_parcelas!='') {
                                                            for($i=0; $i < $quantidade_parcelas; $i++) {
                                                                $tabela_parcelas = $matriz_parcelas[$i];

                                                                $itens = explode("|", $tabela_parcelas);
                                                                $prazo = $itens[0];
                                                                $valor = $itens[1];
                                                                $forma = $itens[2];
                                                                $conta = $itens[3];

                                                                $tbl_forma = mysqli_query($conector, "select * from tbl_forma_pagamento
                                                                         where tbl_forma_pagamento_id ='$forma'"); 
                                                                $num_rows = mysqli_num_rows($tbl_forma);

                                                                if ($num_rows!=0) {
                                                                    $reg_forma = mysqli_fetch_object($tbl_forma);
                                                                    $desc_forma = $reg_forma->tbl_forma_pagamento_descricao;
                                                                }
                                                                else {
                                                                    $desc_forma = '';
                                                                }

                                                                $tbl_conta = mysqli_query($conector, "select * from tbl_conta_pagamento
                                                                         where tbl_conta_pagamento_id  ='$conta'"); 
                                                                $num_rows = mysqli_num_rows($tbl_conta);

                                                                if ($num_rows!=0) {
                                                                    $reg_conta = mysqli_fetch_object($tbl_conta);
                                                                    $desc_conta = $reg_conta->tbl_conta_pagamento_descricao;
                                                                }
                                                                else {
                                                                    $desc_conta = '';
                                                                }

                                                                $string_dias= "+".$prazo." days";
                                                                $data_vencimento = date("d/m/Y", strtotime($string_dias,strtotime($data_emissao)));

                                                                echo '<tr>';
                                                                echo '<td>'.$prazo.'</td>';
                                                                echo '<td>'.$data_vencimento.'</td>';
                                                                echo '<td>'.number_format($valor,2,',','.').'</td>';
                                                                echo '<td>'.$desc_forma.'</td>';
                                                                echo '<td>'.$desc_conta.'</td>';
                                                                echo '</tr>';
                                                            }

                                                        }

                                                    ?>
                                                </tbody>
                                            </table>
                                                                                       
                                            <hr align="center"> 

                                            <table class="table table-striped table-advance table-hover" id="tabela_itens_consulta" width="100%" style="font-size: 12px">

                                            <?php
                                                switch ($codigo_tipo) {
                                                    case 'V':
                                                        echo '<thead>
                                                            <tr>
                                                                <th>Categoria</th>
                                                                <th>Sexo</th>
                                                                <th style="text-align: right;">Qtd Animais</th>
                                                                <th style="text-align: right;">Peso Total Kg</th>
                                                                <th style="text-align: right;">Peso @</th>
                                                                <th style="text-align: center;">Und Negociada</th>
                                                                <th style="text-align: right;">Valor Unitário</th>
                                                                <th style="text-align: right;">Valor Total</th>
                                                                <th style="text-align: right;">Conta</th>
                                                            </tr>
                                                        </thead>';
                                                        break;
                                                    case 'M':
                                                        echo '<thead>
                                                            <tr>
                                                                <th style="text-align: right;">Qtd Animais</th>
                                                                <th style="text-align: center;">Sexo</th>
                                                                <th style="text-align: right;">Peso Vivo Kg</th>
                                                                <th style="text-align: right;">Peso Ajustado Kg</th>
                                                                <th style="text-align: right;">Peso Morto Kg</th>
                                                                <th style="text-align: right;">Peso Morto @</th>
                                                                <th style="text-align: center;">Und Negociada</th>
                                                                <th style="text-align: right;">Valor Unitário</th>
                                                                <th>Valor Total</th>
                                                                <th style="text-align: right;">Rendimento Carcaça</th>
                                                                <th style="text-align: right;">Conta</th>
                                                            </tr>
                                                        </thead>';
                                                        break;
                                                    case 'C':
                                                        echo '<thead>
                                                            <tr>
                                                                <th>Categoria</th>
                                                                <th>Sexo</th>
                                                                <th style="text-align: right;">Qtd Animais</th>
                                                                <th style="text-align: right;">Peso</th>
                                                                <th style="text-align: right;">Valor Unitário</th>
                                                                <th style="text-align: right;">Valor Total</th>
                                                                <th style="text-align: right;">R$/@ Aproximado</th>
                                                                <th style="text-align: right;">Conta</th>
                                                            </tr>
                                                        </thead>';
                                                        break;
                                                }

                                                echo '<tbody>';    

    $tbl_item = mysqli_query($conector, "select * from tbl_item_venda
             where tbl_ite_venda_numero_id  ='$numero_venda'"); 
    $num_rows = mysqli_num_rows($tbl_item);

    if ($num_rows!=0) {
        while ($reg_item = mysqli_fetch_object($tbl_item)) {
            $categoria = $reg_item->tbl_ite_venda_categoria;
            $sexo = $reg_item->tbl_ite_venda_sexo;
            $qtd = $reg_item->tbl_ite_venda_quantidade;
            $conta = $reg_item->tbl_ite_conta_contabil;
            $peso_vivo = number_format($reg_item->tbl_ite_venda_peso_vivo,2,',','.');
            $peso_ajustado = number_format($reg_item->tbl_ite_venda_peso_vivo_ajustado,2,',','.');
            $peso_morto = number_format($reg_item->tbl_ite_venda_peso_morto,2,',','.');
            $arroba = number_format($reg_item->tbl_ite_venda_arroba,2,',','.');
            $und = $reg_item->tbl_ite_venda_unidade_negociada;
            $vlr_unit = number_format($reg_item->tbl_ite_venda_valor_unitario,2,',','.');
            $vlr_total = number_format($reg_item->tbl_ite_venda_valor_total,2,',','.');
            $rendimento = number_format($reg_item->tbl_ite_percentual_rendimento,2,',','.');

            $plano_contas = mysqli_query($conector, "select * from tbl_plano_contas
                     where tbl_plano_contas_codigo_id ='$conta'"); 
            $num_rows = mysqli_num_rows($plano_contas);

            if ($num_rows!=0) {
                $reg_conta = mysqli_fetch_object($plano_contas);
                $desc_plano_contas = $reg_conta->tbl_plano_contas_descricao;
            }
            else {
                $desc_plano_contas = '';
            }

            $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade
                               where tab_codigo_categoria_idade='$categoria'"); 
            $num_rows = mysqli_num_rows($tbl_categoria);
            if ($num_rows!=0) {
                $reg_cat = mysqli_fetch_object($tbl_categoria);
                $idade_de = $reg_cat->tab_categoria_idade_de;
                $idade_ate = $reg_cat->tab_categoria_idade_ate;
                $descricao_cat = $idade_de . ' a ' . $idade_ate . ' meses';

                if ($descricao_cat=='37 a 999999999 meses'){
                    $descricao_cat = '> 36 meses';
                }
            }
            else {
                $descricao_cat = '';
            }

            if ($und==1) {
                $und = '@';
            }
            else {
                $und = 'Kg';
            }

            switch ($codigo_tipo) {
                case 'V':
                    echo '<tr>
                          <td>'.$descricao_cat.'</td>
                          <td>'.$sexo.'</td>
                          <td align="right">'.$qtd.'</td>
                          <td align="right">'.$peso_vivo.'</td>
                          <td align="right">'.$arroba.'</td>
                          <td align="center">'.$und.'</td>
                          <td align="right">'.$vlr_unit.'</td>
                          <td align="right">'.$vlr_total.'</td>
                          <td align="right">'.$desc_plano_contas.'</td>
                          </tr>';
                    break;
                case 'M':
                    echo '<tr>
                          <td align="right">'.$qtd.'</td>
                          <td align="center">'.$sexo.'</td>
                          <td align="right">'.$peso_vivo.'</td>
                          <td align="right">'.$peso_ajustado.'</td>
                          <td align="right">'.$peso_morto.'</td>
                          <td align="right">'.$arroba.'</td>
                          <td align="center">'.$und.'</td>
                          <td align="right">'.$vlr_unit.'</td>
                          <td align="right">'.$vlr_total.'</td>
                          <td align="right">'.$rendimento.'%</td>
                          <td align="right">'.$desc_plano_contas.'</td>
                          </tr>';
                    break;
                case 'C':
                    echo '<tr>
                          <td>'.$descricao_cat.'</td>
                          <td>'.$sexo.'</td>
                          <td align="right">'.$qtd.'</td>
                          <td align="right">'.$peso_vivo.'</td>
                          <td align="right">'.$vlr_unit.'</td>
                          <td align="right">'.$vlr_total.'</td>
                          <td align="right">'.$arroba.'</td>
                          <td align="right">'.$desc_plano_contas.'</td>
                          </tr>';
                    break;
            }
        }
    }

                                            ?>
                                                </tbody>
                                            </table>

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
                            <h4 class="modal-title">Venda</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair();">Fechar</button>
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
                            <h4 class="modal-title">Venda - Erro</h4>
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
  $javascript_file_name = 'compra_venda.js';
  require 'rodape.php';
?>




