
<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $numero_movimentacao = $_REQUEST['id'];

    $tbl_movimentacao = mysqli_query($conector, "select * from tbl_movimentacao
             where tbl_movimentacao_id='$numero_movimentacao'"); 

    $reg_mov = mysqli_fetch_object($tbl_movimentacao);

    $nome_inclusao = $reg_mov->tbl_movimentacao_incluido_por;
    $data_inclusao = new DateTime($reg_mov->tbl_movimentacao_incluido_em);
    $incluido_por = $nome_inclusao . ' em ' . $data_inclusao->format('d/m/Y');

    $data_emissao = new DateTime($reg_mov->tbl_movimentacao_data);
    $data_emissao_edi = $data_emissao->format('d/m/Y');

    $controle = $reg_mov->tbl_movimentacao_controle;
    $codigo_local_origem = $reg_mov->tbl_movimentacao_codigo_local_origem;
    $codigo_local_destino = $reg_mov->tbl_movimentacao_codigo_local_destino;
    $codigo_tipo = $reg_mov->tbl_movimentacao_tipo;
    $animais_pesados = $reg_mov->tbl_movimentacao_qtd_animais_pesados;
    $peso_kg = $reg_mov->tbl_movimentacao_peso_kg;
    $peso_arroba = $reg_mov->tbl_movimentacao_peso_arroba;
    $peso_medio_kg = $reg_mov->tbl_movimentacao_peso_medio_kg;
    $peso_medio_arroba = $reg_mov->tbl_movimentacao_peso_medio_arroba;
    $filtros=$reg_mov->tbl_movimentacao_filtros;

    if ($codigo_local_origem==999999999) {
        $desc_origem = 'ACERTO INICIAL DO ESTOQUE';
    }
    else {
        $tbl_origem = mysqli_query($conector, "select * from tbl_pessoa
                 where tbl_pessoa_id='$codigo_local_origem'"); 
        $num_rows = mysqli_num_rows($tbl_origem);

        if ($num_rows!=0) {
            $reg_origem = mysqli_fetch_object($tbl_origem);
            $desc_origem = $reg_origem->tbl_pessoa_nome;
        }
        else {
            $desc_origem = '';
        }
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

    switch ($codigo_tipo) {
        case 3:
            $desc_tipo = 'Venda';
            if ($reg_mov->tbl_movimentacao_aceite_financeiro_em!=''){
                $nome_aceite = $reg_mov->tbl_movimentacao_aceite_financeiro_por;
                $data_aceite = new DateTime($reg_mov->tbl_movimentacao_aceite_financeiro_em);
                $aceite = 'Faturado em: ' . $data_aceite->format('d/m/Y') . ' - Documento Nº: ' . $reg_mov->tbl_movimentacao_codigo_venda;
            }
            else {
                if ($reg_mov->tbl_movimentacao_situacao=='') {
                    $aceite = 'Aguardando Baixa Estoque'; 
                }
                else {
                    $aceite = 'Aguardando Faturamento'; 
                }
            }
            break;
        case 4:
            $desc_tipo = 'Compra';
            if ($codigo_local_origem==999999999) {
                $aceite = 'Confirmado';
            }
            else if ($reg_mov->tbl_movimentacao_aceite_financeiro_em!=''){
                $nome_aceite = $reg_mov->tbl_movimentacao_aceite_financeiro_por;
                $data_aceite = new DateTime($reg_mov->tbl_movimentacao_aceite_financeiro_em);
                $aceite = 'Faturado em: ' . $data_aceite->format('d/m/Y') . ' - Documento Nº: ' . $reg_mov->tbl_movimentacao_codigo_venda;
            }
            else {
                $aceite = 'Aguardando Faturamento'; 
            }
            break;
        case 5:
            $desc_tipo = 'Tranferência';
            if ($reg_mov->tbl_movimentacao_aceite_transferencia_em!=''){
                $nome_aceite = $reg_mov->tbl_movimentacao_aceite_transferencia_por;
                $data_aceite = new DateTime($reg_mov->tbl_movimentacao_aceite_transferencia_em);
                $aceite = 'Aceite da Transferência por: '  . $nome_aceite . ' em ' . $data_aceite->format('d/m/Y');
            }
            else {
                if ($reg_mov->tbl_movimentacao_situacao=='') {
                    $aceite = 'Aguardando Baixa Estoque'; 
                }
                else {
                    $aceite = 'Aguardando Aceite'; 
                }
            }
            break;
        case 888:
            $tbl_item = mysqli_query($conector, "select * from tbl_item_movimentacao
                inner join tabela_causa_morte on tab_codigo_causa_morte = 
                                                 tbl_ite_movimentacao_motivo_morte
                where tbl_ite_movimentacao_numero_id ='$numero_movimentacao'"); 

            $reg_item = mysqli_fetch_object($tbl_item);
            $desc_motivo = $reg_item->tab_descricao_causa_morte;

            $desc_tipo = 'Morte - ' . $desc_motivo;
            $aceite = '';

            break;
        case 881:
            $desc_tipo = 'Natimorto';
            $aceite = '';
            break;
        case 999:
            $desc_tipo = 'Outras saídas';
            $aceite = '';
            break;
    }

    if ($codigo_tipo==888 || $codigo_tipo==881 || $codigo_tipo==999) {
        $tbl_item = mysqli_query($conector, "select * from tbl_item_movimentacao
            where tbl_ite_movimentacao_numero_id ='$numero_movimentacao'");

        $reg_item = mysqli_fetch_object($tbl_item);
        $codigo_pasto = $reg_item->tbl_ite_movimentacao_codigo_pasto;

        $tbl_pasto = mysqli_query($conector, "select * from tbl_pasto
            where tbl_pasto_id ='$codigo_pasto'");

        $num_rows = mysqli_num_rows($tbl_pasto);

        if ($num_rows!=0) {
            $reg_pasto = mysqli_fetch_object($tbl_pasto);
            $desc_pasto = $reg_pasto->tbl_pasto_descricao;
        }
        else {
            $desc_pasto = '';
        }
    }

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

    $controle_estoque = $_SESSION['controle_estoque'];

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
            include "limpar_secao_pesagem.php"; 
            include "limpar_secao_nutricao.php"; 
            include "limpar_secao_nascimento.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_movimentacao_animais.php"> Movimentação</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Consultar</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-cogs"></i> Movimentação - Consultar</h3>
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
                                                <div class="col-md-8">
                                                    <label for="numero_movimentacao_id" class="label_consulta">Nº do Documento:&nbsp;</label>
                                                    <span id="numero_movimentacao_id"><?php echo $numero_movimentacao;?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="includido_por" class="label_consulta">Incluido por:&nbsp;</label>
                                                    <span id="includido_por" ><?php echo $incluido_por;?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label for="faturado_em" class="label_consulta">Faturado/Transferido:&nbsp;</label>
                                                    <span id="faturado_em" ><?php echo $aceite;?></span>
                                                </div>
                                            </div>

                                            <div class="row"> 
                                                <div class="col-md-8">
                                                    <label class="label_consulta">Filtros:&nbsp;</label> <span><?php echo $filtros;?></span>
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
                                                </div>

                                                <div class="form-group col-md-4">

                                                <?php
    if ($codigo_tipo==888 || $codigo_tipo==881 || $codigo_tipo==999){
        echo '<label class="label_consulta">Pasto:&nbsp;</label>';
        echo '<span>'.$desc_pasto.'</span>';
    }
    else {
        echo '<label class="label_consulta">Destino:&nbsp;</label>';
        echo '<span>'.$desc_destino.'</span>';
    }
                                                ?>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="label_consulta">Qtde Animais:&nbsp;</label> <span ><?php echo $animais_pesados;?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Total Kg:&nbsp;</label><span>
                                                    <?php echo number_format($peso_kg,2,',','.');?>
                                                    </span>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Total Arrobas:&nbsp;</label><span><?php echo number_format($peso_arroba,2,',','.');?></span>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Médio Kg:&nbsp;</label><span>
                                                    <?php echo number_format($peso_medio_kg,2,',','.');?>
                                                    </span>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Médio Arrobas:&nbsp;</label><span><?php echo number_format($peso_medio_arroba,2,',','.');?></span>
                                                </div>
                                            </div>

                                            <hr align="center"> 

                                            <div class="row">  
                                                <button type="button" class="btn btn-info pull-right" onclick="finalizar_sair()">Voltar
                                                </button>
                                            </div>

                                            <table class="table table-striped table-advance table-hover" id="tabela_itens_consulta" width="100%" style="font-size: 12px">

                                                <thead>
                                                    <tr>
                                                    <?php 
                                                        if ($codigo_tipo==4){
                                                            if ($controle_estoque=='I')
                                                            {
                                                                echo '<th>Categoria</th>';
                                                                echo '<th style="text-align: right;">Idade (meses)</th>';
                                                                echo '<th style="text-align: center;">Sexo</th>';
                                                                echo '<th>Raça</th>';
                                                                echo '<th>Pelagem</th>';
                                                                echo '<th style="text-align: right;">Qtde Categoria</th>';
                                                                echo '<th style="text-align: right;">Peso Médio</th>';

                                                                echo '<th style="text-align: right;">Seq Númerica</th>';
                                                                echo '<th>Marcação Alfa</th>'; 
                                                            }
                                                            else {
                                                                echo '<th>Categoria</th>';
                                                                echo '<th style="text-align: right;">Idade (meses)</th>';
                                                                echo '<th style="text-align: center;">Sexo</th>';
                                                                echo '<th>Raça</th>';
                                                                echo '<th style="text-align: right;">Qtde Categoria</th>';
                                                                echo '<th style="text-align: right;">Peso Médio</th>';
                                                                echo '<th></th>';
                                                            }
                                                        }
                                                        else {
                                                            if ($controle_estoque=='I') {
                                                                echo '<th>Id</th>';
                                                                echo '<th>Peso</th>';
                                                                echo '<th>Categoria</th>';
                                                                echo '<th>Sexo</th>';
                                                                echo '<th>Nascimento</th>';
                                                                echo '<th>Raça</th>';
                                                                echo '<th>Pelagem</th>';
                                                                echo '<th>Mãe</th>';
                                                                echo '<th>Motivo</th>';
                                                                echo '<th>Observação</th>'; 
                                                            }
                                                            else {
                                                                echo '<th>Categoria</th>';
                                                                echo '<th>Qtde</th>';
                                                                echo '<th>Sexo</th>';
                                                                echo '<th>Peso Kg</th>';
                                                                echo '<th>Peso Médio Kg</th>';
                                                                echo '<th>Peso @</th>';
                                                                echo '<th>Peso Médio @</th>';
                                                                echo '<th>Observação</th>'; 
                                                            }
                                                        }
                                                    ?>
                                                    </tr>
                                                </thead>

                                                <tbody>
    <?php
        if ($controle_estoque=='I') {
            if ($codigo_tipo==4){
                $rs = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
                    WHERE tbl_ite_movimentacao_numero_id='$numero_movimentacao'
                    ORDER BY tbl_ite_movimentacao_numero_item ASC");
            }
            else {
                $rs = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
                    INNER JOIN tbl_animais
                        ON tbl_animal_codigo_id = tbl_ite_movimentacao_codigo_id_animal
                    WHERE tbl_ite_movimentacao_numero_id='$numero_movimentacao'
                    ORDER BY tbl_animal_codigo_numerico ASC");
            }
        }
        else {
            $rs = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
                WHERE tbl_ite_movimentacao_numero_id='$numero_movimentacao'");
        }
    
        $num_rows = mysqli_num_rows($rs);

        if ($num_rows!=0){
            while ($fila = mysqli_fetch_object($rs)){

                $motivo = $fila->tbl_ite_movimentacao_motivo_morte;

                if ($codigo_tipo==4) {
                    $codigo_categoria = $fila->tbl_ite_categoria_compra;
                }
                else {
                    $codigo_categoria = $fila->tbl_ite_movimentacao_codigo_categoria;
                }

                $tbl_motivo = mysqli_query($conector, "SELECT * FROM tabela_causa_morte
                    WHERE tab_codigo_causa_morte='$motivo'");

                $num_rows_motivo = mysqli_num_rows($tbl_motivo);
                
                if ($num_rows_motivo!=0){
                    $reg_motivo = mysqli_fetch_object($tbl_motivo);
                    $desc_motivo = $reg_motivo->tab_descricao_causa_morte; 
                }
                else {
                    $desc_motivo = '';
                }    

                $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                        WHERE tab_codigo_categoria_idade ='$codigo_categoria'");
                $num_rows = mysqli_num_rows($tbl_categoria);    

                if ($num_rows!=0) {
                    $reg_categoria = mysqli_fetch_object($tbl_categoria);
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;
                    if ($idade_ate==999999999) {
                        $desc_categoria = '> 36 meses';
                    }
                    else {
                        $desc_categoria = $idade_de . ' a ' . $idade_ate . ' meses';
                    }
                }  
                else {
                    $desc_categoria='';
                }                 

                echo '<tr>';

                if ($codigo_tipo==4) {
                    if ($controle_estoque=='I') {
                        echo '<td>' . $desc_categoria.'</td>';
                        echo '<td style="text-align: right;">' . $fila->tbl_ite_idade_meses_compra . '</td>';
                        echo '<td style="text-align: center;">' . $fila->tbl_ite_movimentacao_sexo . '</td>';
                        echo '<td>' . $fila->tbl_ite_movimentacao_raca. '</td>';
                        echo '<td>' . $fila->tbl_ite_movimentacao_pelagem. '</td>';
                        echo '<td style="text-align: right;">' . $fila->tbl_ite_qtd_categoria_compra. '</td>';
                        echo '<td style="text-align: right;">' . $fila->tbl_ite_movimentacao_peso. '</td>';
                        echo '<td style="text-align: right;">' . $fila->tbl_ite_sequencia_numerica_compra. '</td>';
                        echo '<td>' . $fila->tbl_ite_marcacao_alfa_compra. '</td>';
                    }
                    else {
                        echo '<td width="20%">' . $desc_categoria.'</td>';
                        echo '<td width="10%" style="text-align: right;">' . $fila->tbl_ite_idade_meses_compra . '</td>';
                        echo '<td width="10%" style="text-align: center;">' . $fila->tbl_ite_movimentacao_sexo . '</td>';
                        echo '<td width="20%">' . $fila->tbl_ite_movimentacao_raca. '</td>';
                        echo '<td width="10%" style="text-align: right;">' . $fila->tbl_ite_qtd_categoria_compra. '</td>';
                        echo '<td width="10%" style="text-align: right;">' . $fila->tbl_ite_movimentacao_peso. '</td>';
                        echo '<td width="20%"></td>';
                    }
                }
                else {
                    if ($controle_estoque=='I') {
                        $codigo_animal = $fila->tbl_ite_movimentacao_codigo_animal;

                        $caracteres = strlen($codigo_animal);

                        /*if ($caracteres>=9){
                            $codigo_numerico = intval(substr($codigo_animal, (strlen($codigo_animal) - 9), 9));
                            $codigo_alfa = strrev(preg_replace('/\d/', '',  strrev($codigo_animal), 9));

                            if ($codigo_alfa=='' && $codigo_numerico==0) {
                                $codigo_animal_edi = '';
                            }
                            else if ($codigo_alfa==''){
                                $codigo_animal_edi = $codigo_numerico;
                            }
                            else {
                                $codigo_animal_edi = $codigo_alfa.'-'.$codigo_numerico;
                            }
                        }
                        else {*/
                            $codigo_animal_edi = $codigo_animal;
                        //}

                        $codigo_mae = $fila->tbl_ite_movimentacao_mae;

                        $caracteres = strlen($codigo_mae);

                        if ($caracteres>=9){
                            $codigo_numerico_mae = intval(substr($codigo_mae, (strlen($codigo_mae) - 9), 9));
                            $codigo_alfa_mae = strrev(preg_replace('/\d/', '',  strrev($codigo_mae), 9));

                            if ($codigo_alfa_mae=='' && $codigo_numerico_mae==0) {
                                $codigo_mae_edi = '';
                            }
                            else if ($codigo_alfa_mae==''){
                                $codigo_mae_edi = $codigo_numerico_mae;
                            }
                            else {
                                $codigo_mae_edi = $codigo_alfa_mae.'-'.$codigo_numerico_mae;
                            }
                        }
                        /*else if ($codigo_mae==0 || $codigo_mae=='') {
                            $codigo_mae_edi = '';
                        }*/
                        else {
                            $codigo_mae_edi = $codigo_mae;
                        }

                        echo '<td>' .$codigo_animal_edi.'</td>';
                        echo '<td>' . number_format($fila->tbl_ite_movimentacao_peso,2,',','.') . '</td>';
                        echo '<td>' . $desc_categoria . '</td>';
                        echo '<td>' . $fila->tbl_ite_movimentacao_sexo . '</td>';
                        echo '<td>' . $fila->tbl_ite_movimentacao_nascimento . '</td>';
                        echo '<td>' . $fila->tbl_ite_movimentacao_raca. '</td>';
                        echo '<td>' . $fila->tbl_ite_movimentacao_pelagem. '</td>';
                        echo '<td>' . $codigo_mae_edi. '</td>';
                        echo '<td>' . $desc_motivo. '</td>';
                        echo '<td>' . $fila->tbl_ite_movimentacao_observacao. '</td>';
                    }
                    else {
                        echo '<td>' . $desc_categoria .'</td>';
                        echo '<td>' . $fila->tbl_ite_movimentacao_qtde_categoria . '</td>';
                        echo '<td>' . $fila->tbl_ite_movimentacao_sexo . '</td>';
                        echo '<td>' . number_format($fila->tbl_ite_movimentacao_peso,2,',','.') . '</td>';
                        echo '<td>' . number_format($fila->tbl_ite_movimentacao_peso_medio,2,',','.') . '</td>';
                        echo '<td>' . number_format($fila->tbl_ite_movimentacao_peso_arroba,2,',','.') . '</td>';
                        echo '<td>' . number_format($fila->tbl_ite_movimentacao_peso_arroba_medio,2,',','.') . '</td>';
                        echo '<td>' . $fila->tbl_ite_movimentacao_observacao. '</td>';
                    }
                }
                echo '</tr>';
                                                            }
                                                        }
                                                    ?>    
                                                </tbody>
                                            </table>
                                                                                        
                                                <hr align="center"> 

                                                <div class="row">  
                                                    <button type="button" class="btn btn-info pull-right" 
                                                        onclick="finalizar_sair()">Voltar
                                                    </button>
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
                            <h4 class="modal-title">Movimentação</h4>
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
                            <h4 class="modal-title">Movimentação - Erro</h4>
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




