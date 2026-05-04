
<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    @ session_start(); 

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];
    $controle_estoque = $_SESSION['controle_estoque'];

    $tipo_rel = $_REQUEST['tipo_rel'];
    $descricao_filtro = $_REQUEST['descricao_filtro'];

    $local = $_REQUEST['local'];

    $tbl_pessoa= mysqli_query($conector, "SELECT * FROM tbl_pessoa
        WHERE tbl_pessoa_id='$local'");

    $num_rows = mysqli_num_rows($tbl_pessoa);

    if ($num_rows!=0) {
        $reg_pessoa = mysqli_fetch_object($tbl_pessoa);
        $nome_pessoa = $reg_pessoa->tbl_pessoa_nome;
    }
    else {
        $nome_pessoa = '';
    }

    $tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                WHERE tbl_animal_pasto_local='$local' AND 
                      tbl_animal_pasto_situacao='A'");

    $total_local = mysqli_num_rows($tbl_animal_pasto);

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

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; 
            include "limpar_secao_selecao_matrizes.php"; 
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
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Mapa de Gado</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-map"></i> Mapa de Gado</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data">

                            <div class="panel"> 
                                <div class=panel-body>
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="container" id="dados_cliente">

                                                <input type="hidden" id="expande_tela" value="S">

                                                <input type="hidden" id="codigo_local"
                                                    <?php echo "value='".$local."'";?>>

                                                <input type="hidden" id="tipo_rel"
                                                    <?php echo "value='".$tipo_rel."'";?>>

                                                <input type="hidden" id="descricao_filtro"
                                                    <?php echo "value='".$descricao_filtro."'";?>>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label class="label_consulta_rel">Filtro:</label>
                                                    <span><?php echo 
                                                        $descricao_filtro;?></span>
                                                </div>

                                                <div class="col-md-4">  
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                                                    </button>

                                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="lista_mapa_gado_excel()">Excel</button>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label class="label_consulta_rel">Total de Animais:</label>
                                                    <span><?php echo $total_local;?></span>
                                                </div>
                                            </div>

<table class="table table-bordered table-striped table-advance table-hover" id="tabela_itens_consulta" width="100%" style="font-size: 12px;">

<tbody>

<?php

    $total_animais = 0;
    $total_cat_M_F = 0;
    $ultima_data = '0000-00-00';

    for ($i = 1; $i <=5; $i++) {
        $j = str_pad($i, 3, "0", STR_PAD_LEFT);
        $total_cat_macho[$j]=0;
        $total_cat_femea[$j]=0;
    }
            
    $tbl_pasto= mysqli_query($conector, "SELECT * FROM tbl_pasto
        WHERE tbl_pasto_codigo_local='$local' AND 
              tbl_pasto_lixeira=0 AND
              tbl_pasto_modulo=999");

    $num_rows = mysqli_num_rows($tbl_pasto);

    if ($num_rows!=0) {
        while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
            $descricao = $reg_pasto->tbl_pasto_descricao;
            $codigo_pasto = $reg_pasto->tbl_pasto_id;
            $descricao_lote = $reg_pasto->tbl_pasto_descricao_lote;
            $id_lote = ltrim($reg_pasto->tbl_pasto_id_lote, '0').'/'.
                       substr($reg_pasto->tbl_pasto_ano_lote, 2, 2);
            $area = $reg_pasto->tbl_pasto_area;

            if ($area==0) {
                $area='';
            }

            // Pega dias com animais no pasto
            $dias_pasto = 0;

            $dataAtual = new DateTime();
            $dataAnterior = new DateTime($reg_pasto->tbl_pasto_data_com_animais_anterior);
            $dataCom = new DateTime($reg_pasto->tbl_pasto_data_com_animais);

            if ($dataCom!='') {
                $diff = $dataAtual->diff($dataCom);
                $dias_pasto = $diff->days;
            }

            // Fim pega dias com animais no pasto

            $tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                WHERE tbl_animal_pasto_id='$codigo_pasto' AND 
                      tbl_animal_pasto_situacao='A'
                ORDER BY tbl_animal_pasto_nascimento DESC");

            $num_rows_animal = mysqli_num_rows($tbl_animal_pasto);
            $total_animais_pasto = 0;

            for ($i = 1; $i <=5; $i++) {
                $j = str_pad($i, 3, "0", STR_PAD_LEFT);
                $total_macho[$j]=0;
                $total_femea[$j]=0;
            }

            if ($num_rows_animal!=0) {
                while ($reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto)) {
                    $sexo = $reg_animal_pasto->tbl_animal_pasto_sexo;
                    //$codigo_categoria = $reg_animal_pasto->tbl_animal_pasto_categoria;

                    $inclusao = $reg_animal_pasto->tbl_animal_pasto_incluido_em;
                    $alteracao = $reg_animal_pasto->tbl_animal_pasto_alterado_em;

                    if ($inclusao!='') {
                        if ($inclusao>$ultima_data){
                            $ultima_data=$inclusao;
                        }
                    }

                    if ($alteracao!='') {
                        if ($alteracao>$ultima_data){
                            $ultima_data=$alteracao;
                        }
                    }

                    $data_nascimento = $reg_animal_pasto->tbl_animal_pasto_nascimento;
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); 
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    if ($controle_estoque=='I'){
                        $total_animais++;
                        $total_animais_pasto++;

                        $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                            WHERE tab_registro_lixeira_categoria_idade='0'");
                        $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                        while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                            }
                        }

                        if ($sexo=='M') {
                            $total_macho[$codigo_categoria]++;
                            $total_cat_macho[$codigo_categoria]++;
                        }
                        else {
                            $total_femea[$codigo_categoria]++;
                            $total_cat_femea[$codigo_categoria]++;
                        }

                        /*$tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                            WHERE tab_registro_lixeira_categoria_idade='0'");
                        $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                        while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

                                if ($sexo=='M') {
                                    $total_macho[$codigo_categoria]++;
                                    $total_cat_macho[$codigo_categoria]++;
                                }
                                else {
                                    $total_femea[$codigo_categoria]++;
                                    $total_cat_femea[$codigo_categoria]++;
                                }
                            }
                        }*/
                    }
                    else {
                        $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                            WHERE tab_registro_lixeira_categoria_idade='0'");
                        $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                        while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                                $total_animais++;
                                $total_animais_pasto++;

                                if ($sexo=='M') {
                                    $total_macho[$codigo_categoria]++;
                                    $total_cat_macho[$codigo_categoria]++;
                                }
                                else {
                                    $total_femea[$codigo_categoria]++;
                                    $total_cat_femea[$codigo_categoria]++;
                                }
                            }
                        }
                    }
                }

                if ($total_animais_pasto!=0) {
                    echo '<tr>';
                    echo '<td width="12%">'.$descricao.'</td>';
                    for ($i = 1; $i <=5; $i++) {
                        $i = str_pad($i, 3, "0", STR_PAD_LEFT);

                        if ($i==1) {
                            $total_M_F = $total_macho[$i] + $total_femea[$i];

                            if ($total_M_F==0) {
                                $total_M_F='';
                            }

                            echo '<td width="5%" class="text-center">'.$total_M_F.'</td>';
                        }
                        else if ($i==2) {
                            if ($total_macho[$i]==0) {
                                $total_macho[$i]='';
                            }

                            if ($total_femea[$i]==0) {
                                $total_femea[$i]='';
                            }
                            echo '<td width="5%" class="text-center">'.$total_macho[$i].'</td>';
                            echo '<td width="5%" class="text-center">'.$total_femea[$i].'</td>';
                        }
                        else if ($i==3) {
                            if ($total_macho[$i]==0) {
                                $total_macho[$i]='';
                            }

                            if ($total_femea[$i]==0) {
                                $total_femea[$i]='';
                            }
                            echo '<td width="5%" class="text-center">'.$total_macho[$i].'</td>';
                            echo '<td width="5%" class="text-center">'.$total_femea[$i].'</td>';
                        }
                        else if ($i==4) {
                            if ($total_macho[$i]==0) {
                                $total_macho[$i]='';
                            }

                            if ($total_femea[$i]==0) {
                                $total_femea[$i]='';
                            }
                            echo '<td width="5%" class="text-center">'.$total_macho[$i].'</td>';
                            echo '<td width="5%" class="text-center">'.$total_femea[$i].'</td>';
                        }
                        else if ($i==5) {
                            if ($total_macho[$i]==0) {
                                $total_macho[$i]='';
                            }

                            if ($total_femea[$i]==0) {
                                $total_femea[$i]='';
                            }
                            echo '<td width="5%" class="text-center">'.$total_macho[$i].'</td>';
                            echo '<td width="5%" class="text-center">'.$total_femea[$i].'</td>';
                        }
                    }

                    echo '<td width="5%" class="text-center">'.$total_animais_pasto.'</td>';
                    echo '<td width="16%" align="Left">'.$descricao_lote.'</td>';
                    echo '<td width="6%" align="center">'.$id_lote.'</td>';
                    echo '<td width="8%" align="center">'.$dias_pasto.'</td>';
                    echo '<td width="8%" class="text-center">'.$area.'</td>';
                    echo '</tr>';
                }
            }
        }
    }

    $tbl_pasto= mysqli_query($conector, "SELECT * FROM tbl_pasto
        WHERE tbl_pasto_codigo_local='$local' AND 
              tbl_pasto_lixeira=0 AND
              tbl_pasto_modulo!=999");

    $num_rows = mysqli_num_rows($tbl_pasto);

    if ($num_rows!=0) {
        while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
            $descricao = $reg_pasto->tbl_pasto_descricao;
            $codigo_pasto = $reg_pasto->tbl_pasto_id;
            $descricao_lote = $reg_pasto->tbl_pasto_descricao_lote;
            $id_lote = ltrim($reg_pasto->tbl_pasto_id_lote, '0').'/'.
                       substr($reg_pasto->tbl_pasto_ano_lote, 2, 2);
            $area = $reg_pasto->tbl_pasto_area;

            if ($area==0) {
                $area='';
            }

            // Pega dias com animais no pasto
            $dias_pasto = 0;

            $dataAtual = new DateTime();
            $dataAnterior = new DateTime($reg_pasto->tbl_pasto_data_com_animais_anterior);
            $dataCom = new DateTime($reg_pasto->tbl_pasto_data_com_animais);

            if ($dataCom!='') {
                $diff = $dataAtual->diff($dataCom);
                $dias_pasto = $diff->days;
            }

            // Fim pega dias com animais no pasto

            $tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                WHERE tbl_animal_pasto_id='$codigo_pasto' AND 
                      tbl_animal_pasto_situacao='A'
                ORDER BY tbl_animal_pasto_nascimento DESC");

            $num_rows_animal = mysqli_num_rows($tbl_animal_pasto);
            $total_animais_pasto = 0;

            for ($i = 1; $i <=5; $i++) {
                $j = str_pad($i, 3, "0", STR_PAD_LEFT);
                $total_macho[$j]=0;
                $total_femea[$j]=0;
            }

            //$dias_pasto = 0;

            if ($num_rows_animal!=0) {
                while ($reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto)) {

                    $inclusao = $reg_animal_pasto->tbl_animal_pasto_incluido_em;
                    $alteracao = $reg_animal_pasto->tbl_animal_pasto_alterado_em;

                    if ($inclusao!='') {
                        if ($inclusao>$ultima_data){
                            $ultima_data=$inclusao;
                        }
                    }

                    if ($alteracao!='') {
                        if ($alteracao>$ultima_data){
                            $ultima_data=$alteracao;
                        }
                    }

                    $sexo = $reg_animal_pasto->tbl_animal_pasto_sexo;
                    $data_nascimento = $reg_animal_pasto->tbl_animal_pasto_nascimento;
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
                    //$codigo_categoria = $reg_animal_pasto->tbl_animal_pasto_categoria;

                    if ($controle_estoque=='I'){
                        $total_animais++;
                        $total_animais_pasto++;

                        $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                            WHERE tab_registro_lixeira_categoria_idade='0'");
                        $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                        while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                            }
                        }

                        if ($sexo=='M') {
                            $total_macho[$codigo_categoria]++;
                            $total_cat_macho[$codigo_categoria]++;
                        }
                        else {
                            $total_femea[$codigo_categoria]++;
                            $total_cat_femea[$codigo_categoria]++;
                        }

                        /*$tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                            WHERE tab_registro_lixeira_categoria_idade='0'");
                        $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                        while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

                                if ($sexo=='M') {
                                    $total_macho[$codigo_categoria]++;
                                    $total_cat_macho[$codigo_categoria]++;
                                }
                                else {
                                    $total_femea[$codigo_categoria]++;
                                    $total_cat_femea[$codigo_categoria]++;
                                }
                            }
                        }*/
                    }
                    else {
                        $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                            WHERE tab_registro_lixeira_categoria_idade='0'");
                        $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                        while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                                $total_animais++;
                                $total_animais_pasto++;

                                if ($sexo=='M') {
                                    $total_macho[$codigo_categoria]++;
                                    $total_cat_macho[$codigo_categoria]++;
                                }
                                else {
                                    $total_femea[$codigo_categoria]++;
                                    $total_cat_femea[$codigo_categoria]++;
                                }
                            }
                        }
                    }
                }

                if ($total_animais_pasto!=0) {
                    echo '<tr>';
                    echo '<td width="12%">'.$descricao.'</td>';
                    for ($i = 1; $i <=5; $i++) {
                        $i = str_pad($i, 3, "0", STR_PAD_LEFT);

                        if ($i==1) {
                            $total_M_F = $total_macho[$i] + $total_femea[$i];

                            if ($total_M_F==0) {
                                $total_M_F='';
                            }

                            echo '<td width="5%" class="text-center">'.$total_M_F.'</td>';
                        }
                        else if ($i==2) {
                            if ($total_macho[$i]==0) {
                                $total_macho[$i]='';
                            }

                            if ($total_femea[$i]==0) {
                                $total_femea[$i]='';
                            }
                            echo '<td width="5%" class="text-center">'.$total_macho[$i].'</td>';
                            echo '<td width="5%" class="text-center">'.$total_femea[$i].'</td>';
                        }
                        else if ($i==3) {
                            if ($total_macho[$i]==0) {
                                $total_macho[$i]='';
                            }

                            if ($total_femea[$i]==0) {
                                $total_femea[$i]='';
                            }
                            echo '<td width="5%" class="text-center">'.$total_macho[$i].'</td>';
                            echo '<td width="5%" class="text-center">'.$total_femea[$i].'</td>';
                        }
                        else if ($i==4) {
                            if ($total_macho[$i]==0) {
                                $total_macho[$i]='';
                            }

                            if ($total_femea[$i]==0) {
                                $total_femea[$i]='';
                            }
                            echo '<td width="5%" class="text-center">'.$total_macho[$i].'</td>';
                            echo '<td width="5%" class="text-center">'.$total_femea[$i].'</td>';
                        }
                        else if ($i==5) {
                            if ($total_macho[$i]==0) {
                                $total_macho[$i]='';
                            }

                            if ($total_femea[$i]==0) {
                                $total_femea[$i]='';
                            }
                            echo '<td width="5%" class="text-center">'.$total_macho[$i].'</td>';
                            echo '<td width="5%" class="text-center">'.$total_femea[$i].'</td>';
                        }
                    }

                    echo '<td width="5%" class="text-center">'.$total_animais_pasto.'</td>';
                    echo '<td width="14%" align="Left">'.$descricao_lote.'</td>';
                    echo '<td width="6%" align="center">'.$id_lote.'</td>';
                    echo '<td width="8%" align="center">'.$dias_pasto.'</td>';
                    echo '<td width="10%" class="text-center">'.$area.'</td>';
                    echo '</tr>';
                }
            }
        }
    }

    echo '<tr>';
    echo '<th width="12%">Total Animais</th>';

    for ($i = 1; $i <=5; $i++) {
        $i = str_pad($i, 3, "0", STR_PAD_LEFT);

        if ($i==1) {
            $total_cat_M_F = intval($total_cat_macho[$i]) + intval($total_cat_femea[$i]);
        }
    }

    if ($total_cat_M_F==0) {
        $total_cat_M_F='';
    }         

    if ($total_cat_macho['002']==0) {
        $total_cat_macho['002']='';
    }         

    if ($total_cat_femea['002']==0) {
        $total_cat_femea['002']='';
    }       

    if ($total_cat_macho['003']==0) {
        $total_cat_macho['003']='';
    }         

    if ($total_cat_femea['003']==0) {
        $total_cat_femea['003']='';
    }       

    if ($total_cat_macho['004']==0) {
        $total_cat_macho['004']='';
    }         

    if ($total_cat_femea['004']==0) {
        $total_cat_femea['004']='';
    }       

    if ($total_cat_macho['005']==0) {
        $total_cat_macho['005']='';
    }         

    if ($total_cat_femea['005']==0) {
        $total_cat_femea['005']='';
    }       

    echo '<td width="5%" class="text-center">'.$total_cat_M_F.'</td>';
    echo '<td width="5%" class="text-center">'.$total_cat_macho['002'].'</td>';
    echo '<td width="5%" class="text-center">'.$total_cat_femea['002'].'</td>';
    echo '<td width="5%" class="text-center">'.$total_cat_macho['003'].'</td>';
    echo '<td width="5%" class="text-center">'.$total_cat_femea['003'].'</td>';
    echo '<td width="5%" class="text-center">'.$total_cat_macho['004'].'</td>';
    echo '<td width="5%" class="text-center">'.$total_cat_femea['004'].'</td>';
    echo '<td width="5%" class="text-center">'.$total_cat_macho['005'].'</td>';
    echo '<td width="5%" class="text-center">'.$total_cat_femea['005'].'</td>';
    echo '<td width="5%" class="text-center">'.$total_animais.'</td>';
    echo '<td colspan="4"></td>';
    echo '</tr>';

?>

</tbody>

<thead>
    <tr>
        <th rowspan="2" style="vertical-align: middle;text-align:center;">Pasto</th>
<!--$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 3, $total_animais);-->

        <th style="text-align: center;">00 a 07 meses</td>
        <th colspan="2" style="text-align: center;">08 a 12 meses</td>
        <th colspan="2" style="text-align: center;">13 a 24 meses</td>
        <th colspan="2" style="text-align: center;">25 a 36 meses</td>
        <th colspan="2" style="text-align: center;">> 36 meses</td>
        <th rowspan="2" style="vertical-align: middle;text-align:center;">Total</th>
        <th rowspan="2" style="vertical-align: middle;text-align:center;">Descrição Lote</th>
        <th rowspan="2" style="vertical-align: middle;text-align:center;"> Lote</th>
        <th rowspan="2" style="vertical-align: middle;text-align:center;">Dias de Permanência</th>
        <th rowspan="2" style="vertical-align: middle;text-align:center;">Área Pasto (ha)</th>
    </tr>
    <tr>
        <th>Macho/Fêmea</th>
        <th>Macho</th>
        <th>Fêmea</th>
        <th>Macho</th>
        <th>Fêmea</th>
        <th>Macho</th>
        <th>Fêmea</th>
        <th>Macho</th>
        <th>Fêmea</th>
    </tr>
</thead>

</table>

                                            <div class="row">  
                                                <div class="col-md-3">
<span style="font-size: 12px"><?php 
        $date = new DateTime( $ultima_data );
        echo 'Última Atualização: ' . $date->format( 'd-m-Y H:i' ) ?>
</span>
                                                </div>
                                                <div class="col-md-9">
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                                                    </button>

                                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="lista_mapa_gado_excel()">Excel</button>
                                                </div>
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
                            <h4 class="modal-title">Mapa de Gado</h4>
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
                            <h4 class="modal-title">Mapa de Gado - Mensagem</h4>
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
        </section> <!-- wrapper -->
    </section><!--main-content -->


<?php 
  $javascript_file_name = 'mapa_gados_relatorios.js';
  require 'rodape.php';
?>




