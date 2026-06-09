<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";

    $cobertura_id = $_REQUEST['id'];

    $tbl_cobertura = mysqli_query($conector, "select * from tbl_cobertura
        where tbl_cobertura_lixeira=0 and 
              tbl_cobertura_id ='$cobertura_id'"); 

    $reg_matrizes = mysqli_fetch_object($tbl_cobertura);

    $nome_inclusao = $reg_matrizes->tbl_cobertura_incluido_por;
    $data_inclusao = new DateTime($reg_matrizes->tbl_cobertura_incluido_em);
    $incluido_por = $nome_inclusao . ' em ' . $data_inclusao->format('d/m/Y');

    $data_emissao = new DateTime($reg_matrizes->tbl_cobertura_data);
    $data_emissao_edi = $data_emissao->format('d/m/Y');

    $codigo_local = $reg_matrizes->tbl_cobertura_codigo_local;
    $qtd_animais = $reg_matrizes->tbl_cobertura_qtd_animais;
    $filtros=$reg_matrizes->tbl_cobertura_filtros;
    $controle=$reg_matrizes->tbl_cobertura_controle;
    $grupo=$reg_matrizes->tbl_cobertura_codigo_grupo;
    $codigo_estacao_monta=$reg_matrizes->tbl_cobertura_codigo_estacao_monta;
    $protocolo_iatf_id=$reg_matrizes->tbl_cobertura_protocoloiatf;

    if ($controle=='C') {
    	$desc_controle = 'Fêmeas p/Cobertura';
    }
    else {
    	$desc_controle = 'Descarte';
    }

    $tbl_origem = mysqli_query($conector, "select * from tbl_pessoa
              where tbl_pessoa_id='$codigo_local'"); 
    $num_rows = mysqli_num_rows($tbl_origem);

    if ($num_rows!=0) {
        $reg_origem = mysqli_fetch_object($tbl_origem);
        $desc_origem = $reg_origem->tbl_pessoa_nome;
    }
    else {
        $desc_origem = '';
    }

    $tbl_grupo = mysqli_query($conector, "select * from tbl_grupo_estacao_monta
              where tbl_grupo_id ='$grupo' and 
                    tbl_grupo_codigo_estacao_monta='$codigo_estacao_monta' and 
                    tbl_grupo_codigo_local='$codigo_local'"); 
    $num_rows = mysqli_num_rows($tbl_grupo);

    if ($num_rows!=0) {
        $reg_grupo = mysqli_fetch_object($tbl_grupo);
        $desc_grupo = $reg_grupo->tbl_grupo_descricao;
    }
    else {
        $desc_grupo = '';
    }

    $tbl_protocolo_iatf = mysqli_query($conector, "select * from tbl_protocoloiatf
        where tbl_protocoloiatf_id  ='$protocolo_iatf_id'"); 
    $num_rows = mysqli_num_rows($tbl_protocolo_iatf);

    if ($num_rows!=0) {
        $reg_protocolo_iatf = mysqli_fetch_object($tbl_protocolo_iatf);
        $desc_protocolo_iatf = $reg_protocolo_iatf->tbl_protocoloiatf_descricao;
    }
    else {
        $desc_protocolo_iatf = '';
    }

    $data_sistema = date("Y-m-d");


    // pega dados para achar a data do servico
    $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
        WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");
            
    $num_rows = mysqli_num_rows($sql);  
    
    $data_servico = 0;

    if ($num_rows!=0) {
        $reg_protocolo_cobertura = mysqli_fetch_object($sql);

        $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
            WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                  tbl_ite_protocoloiatf_protocolo_id = '$protocolo_iatf_id' 
            ORDER BY tbl_ite_protocoloiatf_id ASC");

        while($reg_itens_protocolo_iatf = mysqli_fetch_object($sql)){
            $dias = substr($reg_itens_protocolo_iatf->tbl_ite_protocoloiatf_descricao, 3);
            $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));
        }
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

    $controle_estoque = $_SESSION['controle_estoque'];
    ?>

    <section id="container" class="">
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
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Reprodução <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_selecao_matrizes.php"> Seleção de Fêmemas</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Consulta</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><img src="img/matrizes.png"> Seleção de Fêmeas - Consultar</h3>
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
                                                    <label for="num_orc" class="label_consulta">Nº do Documento:&nbsp;</label>
                                                    <span id="num_orc"><?php echo $cobertura_id;?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="includido_por" class="label_consulta">Incluido por:&nbsp;</label>
                                                    <span id="includido_por" ><?php echo $incluido_por;?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label class="label_consulta">Tipo Registro:&nbsp;</label>
                                                    <span><?php echo $desc_controle;?></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="label_consulta">Emissão:&nbsp;</label>
                                                    <span><?php echo $data_emissao_edi;?></span>
                                                </div>
                                            </div>

                                            <?php

                                                if ($controle=='C') :
                                            ?>
                                            <div class="row"> 
                                                <div class="col-md-12">
                                                    <label class="label_consulta">Filtros:&nbsp;</label> <span class="desc_filtro"><?php echo $filtros;?></span>
                                                </div>
                                            </div>

                                            <?php
                                                endif;
                                            ?>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label class="label_consulta">Local:&nbsp;</label>
                                                    <span class="desc_local"><?php echo $desc_origem;?></span>

                                                    <input type="hidden" name="id_local" id="id_local" 
                                                	<?php echo "value='".$codigo_local."'";?>>

                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="label_consulta">Grupo:&nbsp;</label> <span  class="desc_grupo"><?php echo $grupo.' - '.$desc_grupo;?></span>
                                                    <input type="hidden" name="id_grupo" id="id_grupo"
                                                	<?php echo "value='".$grupo."'";?>>

                                                </div>

                                                <div class="col-md-4">
                                                    <label class="label_consulta">Qtde Animais:&nbsp;</label> <span class="qtd_animais"><?php echo $qtd_animais;?></span>
                                                </div>

                                                <?php

                                                    if ($controle=='C') :
                                                ?>

                                                <div class="col-md-4">
                                                    <label class="label_consulta">Protocolo IATF:&nbsp;</label> <span ><?php echo $desc_protocolo_iatf;?></span>
                                                </div>

                                                <?php
                                                    endif;
                                                ?>

                                            </div>

                                            <hr align="center"> 

                                            <div class="row">  
                                                <?php
                                                    if ($grupo!=0) {
                                                        echo '
                                                        <div class="col-md-5">
                                                        <label class="control-label">&nbsp;</label>
                                                        <p onclick="modal_inserir_nova_matriz()" style="color: #1E90FF; cursor: pointer; font-size: 15px;">
                                                        <i class="fa fa-plus"></i>
                                                        &nbsp;Inserir Fêmea</p>
                                                        </div>';
                                                    }

                                                    echo '<button type="button" class="btn btn-info pull-right" onclick="finalizar_sair()">Voltar</button>';
                                                    ?>
                                            </div>

                                            <table class="table table-striped table-advance table-hover" id="tabela_itens_consulta" width="100%" style="font-size: 12px">

                                                <thead>
                                                    <tr>
                                                        <th><i class='fa fa-sort-alpha-asc'></i></th>
                                                        <th>Nº da Fêmea</th>
                                                        <th>Raça</th>
                                                        <th>Nº de Partos</th>
                                                        <th>Nº de Abortos</th>
                                                        <th>Idade Ano/Mes</th>
                                                        <th>Último Parto</th>
                                                        <th>Pai do último parto</th>
                                                        <th><i class="icon_cogs"></i> Ações</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php
         $rs = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
            WHERE tbl_ite_cobertura_numero_id ='$cobertura_id'
            ORDER BY tbl_ite_cobertura_codigo_numerico ASC");
        
        $num_rows = mysqli_num_rows($rs);

         if ($num_rows!=0){
            while ($fila = mysqli_fetch_object($rs)){
                $numero_item = $fila->tbl_ite_cobertura_numero_item;
                $codigo_animal_id = $fila->tbl_ite_cobertura_codigo_id_animal;
                $codigo_animal = $fila->tbl_ite_cobertura_codigo_animal;

                $codigo_alfa = $fila->tbl_ite_cobertura_codigo_alfa;
                $codigo_numerico = intval($fila->tbl_ite_cobertura_codigo_numerico);

                if ($codigo_alfa=='') {
                    $matriz = intval($codigo_numerico);
                }
                else {
                    $matriz = $codigo_alfa.'-'.intval($codigo_numerico);
                }

                $dia_1 = $fila->tbl_ite_cobertura_dia_1;
                $dia_2 = $fila->tbl_ite_cobertura_dia_2;

                $tbl_animal = mysqli_query($conector, "select * from tbl_animais 
                    where tbl_animal_codigo_id ='$codigo_animal_id'"); 
                $num_row_animal = mysqli_num_rows($tbl_animal);

                if ($num_row_animal!=0) {
                    $reg_animal = mysqli_fetch_object($tbl_animal);
                    $codigo_id= $reg_animal->tbl_animal_codigo_id ;
                    $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
                    $data_baixa = $reg_animal->tbl_animal_baixado_em;

                    // calcula a idade pela data do serviço conforme o trello (CORREÇÕES DA REPRODUÇÃO) 12/01/2024
                    
                    if ($controle == 'C' && $data_servico!='0') {
                        $data_acompanhamento_calculo = $data_servico;
                    }
                    else if ($data_baixa!='') {
                        $data_acompanhamento_calculo = date($data_baixa);
                    }
                    else {
                        $data_acompanhamento_calculo = date("Y-m-d");
                    }

                    $date = new DateTime($data_nascimento); 
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    $idade_ano_mes = $idade_acompanhamento->format('%Y') .' a/ '. str_pad($idade_acompanhamento->format('%m') , 2 , '0' , STR_PAD_LEFT) . ' m';

                    /*$data_nascimento= $reg_animal->tbl_animal_data_nascimento;

                    $data_inicial = $data_nascimento;
                    $data_final = date("Y-m-d");
                    $diferenca = strtotime($data_final) - 
                                 strtotime($data_inicial);
                    $idade = floor($diferenca / (60 * 60 * 24 * 30));
                    $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);*/

                    $numero_abortos = 0;
                    $dias_ultimo_parto = 0;
                    $coberturas_estacao = 0;
                    
                    $codigo_raca= $reg_animal->tbl_animal_codigo_raca;

                    $tbl_raca = mysqli_query($conector,"select * from tabela_racas 
                        where tab_codigo_raca ='$codigo_raca' and 
                              tab_registro_lixeira_raca = 0"); 
                    $num_row_raca = mysqli_num_rows($tbl_raca);

                    if ($num_row_raca!=0) {
                        $reg_raca = mysqli_fetch_object($tbl_raca);
                        $desc_raca = $reg_raca->tab_descricao_raca;
                    }
                    else {
                        $desc_raca = '';
                    }

                    $tbl_filhos = mysqli_query($conector,"select * from tbl_animais 
                        where tbl_animal_codigo_mae='$codigo_animal_id'
                        order by tbl_animal_codigo_id asc"); 
                    $numero_partos = mysqli_num_rows($tbl_filhos);

                    if ($numero_partos!=0) {
                        while ($reg_filhos = mysqli_fetch_object($tbl_filhos)){
                            $ultimo_parto=new DateTime($reg_filhos->tbl_animal_data_nascimento);
                            $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');

                            $data_inicial = $reg_filhos->tbl_animal_data_nascimento;
                            $data_final = date("Y-m-d");
                            $diferenca = strtotime($data_final) - 
                                         strtotime($data_inicial);
                            $dias_ultimo_parto = floor($diferenca / (60 * 60 * 24));

                            $ultimo_parto=$reg_filhos->tbl_animal_data_nascimento;
                            $codigo_pai=$reg_filhos->tbl_animal_codigo_pai;
                        }
                    }
                    else {
                        $codigo_pai=0;
                        $ultimo_parto_edi='';
                        $dias_ultimo_parto='';
                    }

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$codigo_pai'");
                    $num_rows_pai = mysqli_num_rows($tab_pai);

                    if ($num_rows_pai!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai = $reg->tbl_semem_nome;
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$codigo_pai'");
                        $num_rows_pai = mysqli_num_rows($tab_pai);

                        if ($num_rows_pai!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                        }
                        else {
                            $descricao_pai = '';
                        }
                    }

                    $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                        WHERE tbl_mov_estoque_codigo_mae='$codigo_animal_id' AND 
                              tbl_mov_estoque_codigo_id_animal=999999999 AND
                              tbl_mov_estoque_entrada_saida='A' AND 
                              (tbl_mov_estoque_tipo_movimentacao='A' OR
                               tbl_mov_estoque_tipo_movimentacao='B') 
                        ORDER BY tbl_mov_estoque_nascimento DESC");

                    $numero_abortos = mysqli_num_rows($tbl_aborto);
                }

                if ($data_baixa!='') {
                    echo '<tr style="color: red;">';
                }
                else {
                    echo '<tr>';
                }
                echo '<td align="right">'.$codigo_alfa.'</td>';
                echo '<td align="left">'.$codigo_numerico.'</td>';
                echo '<td>'.$desc_raca.'</td>';
                echo '<td>'.$numero_partos.'</td>';
                echo '<td>'.$numero_abortos.'</td>';
                echo '<td>'.$idade_ano_mes.'</td>';
                echo '<td>'.$ultimo_parto_edi.'</td>';
                echo '<td>'.$descricao_pai.'</td>';
	            echo "<td width='15%'>";    
	            echo "<div class='btn-group'>";
	            //if ($dia_1=='' && $dia_2=='') {
		            echo "<a class='btn' href='#'>
		                <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir essa fêmea' onClick='excluir_matriz_lista(\"{$cobertura_id}\",\"{$numero_item}\",\"{$codigo_animal_id}\",\"{$codigo_animal}\")'>
		                </i></a>";
	            //}
	            echo "</div>";
	            echo "</td>";
                echo '</tr>';
            }
        }
                                                    ?>    
                                                </tbody>
                                            </table>
                                                                                        
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

            <div class="modal fade" id="inserir_nova_matriz" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Seleção de Fêmeas - Inserir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" id="form_inserir_matriz">

                                <input name="codigo_id" type="hidden" id="codigo_id" value="0">

                                <input name="cobertura_numero_id" type="hidden" id="cobertura_numero_id">

                                <input name="codigo_grupo" type="hidden" id="codigo_grupo" <?php echo "value='".$grupo."'";?>>

                                <input name="tipo_inserir" type="hidden" id="tipo_inserir" value="0">

                                <input name="local" type="hidden" id="local" <?php echo "value='".$codigo_local."'";?>>

                                <input name="estacao_monta" type="hidden" id="estacao_monta" <?php echo "value='".$codigo_estacao_monta."'";?>>

	                            <div class="alert alert-danger alert_erro_animal" id="alert_erro_animal" hidden="true">
		                            <strong class="negrito"></strong><span></span>
	                            </div> 

	                            <div class="row">
	                                <div class="col-md-6">
	                                    <span class="grupo_matriz" style="color: #808080;">
	                                    </span>
	                                </div>

	                                <div class="col-md-6">
	                                    <span class="animais_matriz" style="color: #808080;">
	                                    </span>
	                                </div>
	                            </div>

	                            <div class="row">
	                                <div class="form-group col-md-12">
	                                    <span class="filtro_matriz" style="color: #C0C0C0; font-size: 12px;">
	                                    </span>
	                                </div>
	                            </div>

	                            <div class="row">
	                                <div class="form-group col-md-3">
	                                    <label class="control-label"><span class="required">*</span> Nº da Fêmea</label>
	                                    <input name="id_animal" type="text" class="form-control" id="id_animal" autocomplete="off" onchange="ler_animal()" >
	                                </div>

                                    <div class="form-group col-md-9">
	                                    <label class="control-label">&nbsp;</label>
                                        <p id="descricao_animal" class="text-primary"></p>
                                    </div>

	                                <!--<div class="form-group col-md-2">
	                                    <label class="control-label">&nbsp;</label>
	                                </div>-->
	                            </div>
	                        </form>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success gravar_inserir pull-left" type="button" onclick="gravar_inserir_matrizes()">Gravar</button>

                            <button data-dismiss="modal" class="btn btn-info pull-right" id="voltar" type="button">Voltar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_sair" tabindex="-1" role="dialog" 
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
            
            <div>
                <?php
                    include "ajuda.php";
                ?>
            </div>

        </section> <!-- wrapper -->
    </section><!--main-content -->

</section>

 <div class="text-center">
     <div class="credits">
         <font size="2"><p style="color:#C0C0C0">Copyright &copy; Agrolandes 2021</p></font>
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
             $('#id_animal').typeahead({
                source: function(query, result) {  
                    $.ajax({
                        url:"fetch_matriz_cobertura.php",
                        method:"POST",
                        data:{query:query,
                              local: $('#id_local').val()},
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

            $("#id_animal").click(function(){
                $('#id_animal').val('');
                $("#codigo_id").val(0);                
                $("#descricao_animal").text('');
                $("#alert_erro_animal .negrito").html('');
                $("#alert_erro_animal span").html('');
                $(".alert_erro_animal").hide();
                $(".gravar_inserir").hide();
                return false;
            });
        });

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




