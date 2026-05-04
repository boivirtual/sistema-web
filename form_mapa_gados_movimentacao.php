<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";
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
  <link href="css/style.css?<?php echo Versao;?>" rel="stylesheet">
  <link href="css/style-responsive.css?<?php echo Versao;?>" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css" rel="stylesheet" type="text/css" />

  <link href="css/select-1.13.14.css" rel="stylesheet" > 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

<style type="text/css">
    table thead th { border-bottom: 0 !important;}
    table tbody tr td { padding-left:0px !important;}
    table thead tr th { padding-left:0px !important;}
    input::placeholder { color: gray !important; }
</style>

</head>

<body>

  <?php
    @ session_start();

    if(isset($_SESSION['menu_manejo_animais'])) {
        $array_manejo = explode("!",$_SESSION['menu_manejo_animais']);

        if ($array_manejo[0] == 0){
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
    $data_sistema = date("Y-m-d");

    $pasto_id = $_POST["pasto_id"];

    //echo 'Pasto: ' . $pasto_id . ' Secao: ' . $_SESSION["pasto_id"];

    if ($pasto_id=='') {
        $pasto_id = $_SESSION["pasto_id"];
    }

    $_SESSION["pasto_id"] = $pasto_id;

    $grupo_usuario = $_SESSION['grupo_usuario'];
    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = "SELECT * FROM usuario WHERE id_usuario = '$codigo_usuario' AND lixeira_usuario=0 ";  
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

    $tbl_pasto = mysqli_query($conector,"SELECT * FROM tbl_pasto 
        INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_pasto_codigo_local
        WHERE tbl_pasto_id = $pasto_id AND 
              tbl_pasto_lixeira = 0");

    $reg_pasto = mysqli_fetch_object($tbl_pasto);

    // PEGA O ID E ANO DO LOTE DE ANIMAIS 29/10/2024
    if ($reg_pasto->tbl_pasto_id_lote!=0) {
        $id_lote = $reg_pasto->tbl_pasto_id_lote;
        $ano_lote = $reg_pasto->tbl_pasto_ano_lote;
        $desc_id_lote = 'L-'.$id_lote.'/'.substr($ano_lote, 2, 2);
    }
    else {
        $id_lote = 0;
        $ano_lote = 0;
        $desc_id_lote = '';
    }

    $descricao_lote_com_id = $reg_pasto->tbl_pasto_descricao_lote.' '.
                             $desc_id_lote;

    $descricao_lote = $reg_pasto->tbl_pasto_descricao_lote;
    $descricao_lote1 = $reg_pasto->tbl_pasto_descricao_lote_1;
    $descricao_lote2 = $reg_pasto->tbl_pasto_descricao_lote_2;
    $descricao_lote3 = $reg_pasto->tbl_pasto_descricao_lote_3;
    $descricao_lote4 = $reg_pasto->tbl_pasto_descricao_lote_4;
    $descricao_lote5 = $reg_pasto->tbl_pasto_descricao_lote_5;
    $descricao_lote6 = $reg_pasto->tbl_pasto_descricao_lote_6;
    $pasto_id = $reg_pasto->tbl_pasto_id;
    $desc_pasto = $reg_pasto->tbl_pasto_descricao;
    $local_id = $reg_pasto->tbl_pasto_codigo_local;
    $desc_local = $reg_pasto->tbl_pessoa_nome;
    $tipo_capim_id = $reg_pasto->tbl_pasto_tipo_capim;

    $_SESSION["pasto_id"] = $pasto_id;

    $tbl_capim = mysqli_query($conector, "select * from tbl_tipo_capim
        where tbl_tipo_capim_id = '$tipo_capim_id'"); 

    if (mysqli_num_rows($tbl_capim) > 0) {
        $reg_capim = mysqli_fetch_object($tbl_capim);
        $desc_capim = $reg_capim->tbl_tipo_capim_descricao;
    }
    else {
        $desc_capim = '';
    }

    $tbl_animais_pasto = mysqli_query($conector,"SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_id' AND 
              tbl_animal_pasto_situacao = 'A'");
    
    $total_animais_pasto = mysqli_num_rows($tbl_animais_pasto);

    if ($total_animais_pasto > 0){
        $dataAtual = new DateTime();
        $dataCom = new DateTime($reg_pasto->tbl_pasto_data_com_animais);
        $diff = $dataAtual->diff($dataCom);
        $tempoPasto = $total_animais_pasto . " animais há " . $diff->days . 
        " dia(s)";
    }
    else {
        $dataAtual = new DateTime();
        $dataSem = new DateTime($reg_pasto->tbl_pasto_data_sem_animais);
        $diff = $dataAtual->diff($dataSem);
        $tempoPasto = "Pasto vazio há " . $diff->days . " dia(s)";
    }

    $tbl_descricao = mysqli_query($conector, "select * from tbl_descricao_lote_animais
        where tbl_descricao_lote_lixeira=0"); 

    $array_categoria = explode("!", $reg_pasto->tbl_pasto_array_categoria);
    $desc_categoria = [];
    $arrayCategorias = [];
    $descricaoCategorias = [];

    for($i = 0; $i < count($array_categoria); $i++){
        $codigo_categoria = $array_categoria[$i];

        $ssql = "SELECT * FROM tabela_categoria_idade 
        WHERE tab_codigo_categoria_idade ='$codigo_categoria' and 
              tab_registro_lixeira_categoria_idade='0'"; 
        
        $rs = mysqli_query($conector,$ssql); 
        $fila = mysqli_fetch_object($rs);

        $codigo_id = $fila->tab_codigo_categoria_idade;
        $idade_de = $fila->tab_categoria_idade_de;
        $idade_ate = $fila->tab_categoria_idade_ate;

        if ($idade_ate==999999999){
            array_push($desc_categoria, '> 36 m');
            $descricaoCategorias = [
                "id" => $codigo_id,
                "idade_de" => $idade_de,
                "idade_ate" => $idade_ate
            ];
            array_push($arrayCategorias, $descricaoCategorias);
        }
        else {
            array_push($desc_categoria, $idade_de . ' a ' . $idade_ate . ' m');
            $descricaoCategorias = [
                "id" => $codigo_id,
                "idade_de" => $idade_de,
                "idade_ate" => $idade_ate
            ];
            array_push($arrayCategorias, $descricaoCategorias);
        }
    }
        
    $semem = mysqli_query($conector, "select * from tbl_semem
        inner join tabela_racas
                on tab_codigo_raca=tbl_semem_codigo_raca
             where tbl_semem_lixeira=0"); 

    $pai = mysqli_query($conector, "select * from tbl_animais 
        inner join tabela_racas
                on tab_codigo_raca=tbl_animal_codigo_raca
             where tbl_animal_lixeira=0 and 
                   tbl_animal_sexo='M'
             order by tbl_animal_codigo_numerico"); 

    $raca = mysqli_query($conector, "select * from tabela_racas 
        where tab_registro_lixeira_raca=0");

    $pelagem = mysqli_query($conector, "select * from tabela_pelagens 
        where tab_registro_lixeira_pelagem=0");

    $tbl_motivo_morte = mysqli_query($conector, "select * from tabela_causa_morte
        where tab_registro_lixeira_causa_morte=0"); 
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
        include "limpar_secao_pesagem.php"; 
        include "limpar_secao_movimentacao.php"; 
        include "limpar_secao_nutricao.php"; 
        include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_mapa_gados.php"> Mapa de Gado</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Movimentações</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-xs-10 col-lg-12">
                    <h3 class="page-header"><i class="fa fa-retweet"></i> Mapa de Gado - Movimentações</h3>
                </div>
            </div>
          
            <div class="row">
                <div>
                    <input name='controle_estoque' type='hidden' id='controle_estoque'<?php echo "value='".$controle_estoque."'";?>>

                    <input type='hidden' name='pasto_origem' id='pasto_origem' <?php echo "value='".$pasto_id."'";?>>

                    <input type='hidden' id='desc_pasto_origem' <?php echo "value='".$desc_pasto."'";?>>

                    <input type='hidden' name='local_origem' id='local_origem' <?php echo "value='".$local_id."'";?>>
                    

                    <input id='id_lote' <?php echo "value='".$id_lote."'";?> type='hidden'></input>

                    <input id='ano_lote' <?php echo "value='".$ano_lote."'";?> type='hidden'></input>

                    <input id='totalAnimais' <?php echo "value='".$total_animais_pasto."'";?> type='hidden'></input>

                    <input id='descricao_lote' type='hidden' <?php echo "value='".$descricao_lote."'";?>>

                    <input id='descricao_lote_1' type='hidden' <?php echo "value='".$descricao_lote1."'";?>>

                    <input id='descricao_lote_2' type='hidden' <?php echo "value='".$descricao_lote2."'";?>>

                    <input id='descricao_lote_3' type='hidden' <?php echo "value='".$descricao_lote3."'";?>>

                    <input id='descricao_lote_4' type='hidden' <?php echo "value='".$descricao_lote4."'";?>>  

                    <input id='descricao_lote_5' type='hidden' <?php echo "value='".$descricao_lote5."'";?>> 

                    <input id='descricao_lote_6' type='hidden' <?php echo "value='".$descricao_lote6."'";?>>

                </div>

                <!--<div class="col-lg-12">-->
                    <div class="row table-responsive" id="consulta_mapa">
                        <div class="row">
                            <div class="col-xs-10 col-md-6 ">
                                <span class="nome_fazenda">
                                    <?php echo $desc_local;?>
                                </span>
                            </div>

                            <div class="col-xs-2 col-md-6">
                                <button type="button" class="btn btn-info pull-right voltar" onclick="voltar()">Voltar
                                </button> 
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-12 span_centro">
                                <span class="info_pasto"><?php echo $desc_pasto;?> - <?php echo $desc_capim;?>
                                </span>

                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xs-12 col-md-12 span_centro">
                                <span> <?php echo $tempoPasto;?></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <table class="table table-responsive" id="tabela_animais_pasto" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="color: blue; text-align: center;">MACHOS</th>
                                            <th colspan="2" style="color: orange;text-align: center;">FÊMEAS</th>
                                            <th colspan="2" style="color: gray;text-align: center;">BEZERROS</th>
                                        </tr>
                                    </thead>

                                    <tbody>
<?php
    // Exibir animais por categoria

    $arrayMacho = [
        0 => 0,
        1 => 0,
        2 => 0,
        3 => 0,
        4 => 0
    ];
    $arrayFemea = [
        0 => 0,
        1 => 0,
        2 => 0,
        3 => 0,
        4 => 0
    ];

    $arrayMachoImp = [
        0 => '',
        1 => '',
        2 => '',
        3 => '',
        4 => ''
    ];
    $arrayFemeaImp = [
        0 => '',
        1 => '',
        2 => '',
        3 => '',
        4 => ''
    ];

    $tbl_animais_pasto = mysqli_query($conector,"SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_id' AND 
              tbl_animal_pasto_situacao = 'A'");
    
    $num_rows_animais = mysqli_num_rows($tbl_animais_pasto);

    if ($num_rows_animais > 0){
        while ($reg_animais = mysqli_fetch_object($tbl_animais_pasto)) {
            $sexo = $reg_animais->tbl_animal_pasto_sexo;
            $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); 
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            for($i = 0; $i < count($arrayCategorias); $i++){
                $id_categoria = $arrayCategorias[$i]['id'];
                $idade_de = $arrayCategorias[$i]['idade_de'];
                $idade_ate = $arrayCategorias[$i]['idade_ate'];
        
                if ($idade >= $idade_de && 
                    $idade <= $idade_ate && 
                    $sexo == "F"){
                    $arrayFemea[$i]+= 1;
                }
                else if($idade >= $idade_de && 
                        $idade <= $idade_ate && 
                        $sexo == "M"){
                    $arrayMacho[$i]+= 1;
                }
            }                        

        } // Fim while animais no pasto
    } // Fim if animais no pasto

    $total_bezerros = 0;

    for($i = 0; $i < count($array_categoria); $i++){
        if($arrayFemea[$i] != '' && $array_categoria[$i] == 001){
            $total_bezerros+= $arrayFemea[$i];
        }

        if($arrayMacho[$i] != '' && $array_categoria[$i] == 001){
            $total_bezerros+= $arrayMacho[$i];
        }
    }

    for ($i=0; $i < 4; $i++) { 

        if ($i==0) {
            $qtd_macho = '';
            $qtd_femea = '';
            $des_cat_macho = '';
            $des_cat_femea = '';
            $des_cat_bezerro = '';
            $imprimir = '';

            if ($total_bezerros==0) {
                $total_bezerros='';
            }
            else {
                $des_cat_bezerro = '00 a 07 m';
                $imprimir = 'S';
            }

            for ($j=1; $j <=4; $j++) { 
                if ($arrayMacho[$j]!=0 && $arrayMachoImp[$j]=='') {
                    $qtd_macho = $arrayMacho[$j];
                    $des_cat_macho = $desc_categoria[$j];
                    $arrayMachoImp[$j]='S';
                    $j=4;
                    $imprimir = 'S';
                }
            }

            for ($j=1; $j <=4; $j++) { 
                if ($arrayFemea[$j]!=0 && $arrayFemeaImp[$j]=='') {
                    $qtd_femea = $arrayFemea[$j];
                    $des_cat_femea = $desc_categoria[$j];
                    $arrayFemeaImp[$j]='S';
                    $j=4;
                    $imprimir = 'S';
                }
            }

            if ($imprimir) {
                echo '<tr style="font-size: 11px;">
                        <td width="25%" style="color: blue;">'.$des_cat_macho.'</td>
                        <td align="right" width="08%" style="color: blue;">'.$qtd_macho.'</td>
                
                        <td width="25%"style="color: orange;">'.$des_cat_femea.'</td>
                        <td  align="right" width="08%" style="color: orange;">'.$qtd_femea.'</td>

                        <td width="25%" style="color: gray;">'.$des_cat_bezerro.'</td>
                        <td align="right"  width="09%" style="color: gray;">'.$total_bezerros.'</td>
                    </tr>
                ';    
            }
        }
        else if ($i==1) {
            $qtd_macho = '';
            $qtd_femea = '';
            $des_cat_macho = '';
            $des_cat_femea = '';
            $imprimir = '';

            for ($j=1; $j <=4; $j++) { 
                if ($arrayMacho[$j]!=0 && $arrayMachoImp[$j]=='') {
                    $qtd_macho = $arrayMacho[$j];
                    $des_cat_macho = $desc_categoria[$j];
                    $arrayMachoImp[$j]='S';
                    $j=4;
                    $imprimir = 'S';
                }
            }

            for ($j=1; $j <=4; $j++) { 
                if ($arrayFemea[$j]!=0 && $arrayFemeaImp[$j]=='') {
                    $qtd_femea = $arrayFemea[$j];
                    $des_cat_femea = $desc_categoria[$j];
                    $arrayFemeaImp[$j]='S';
                    $j=4;
                    $imprimir = 'S';
                }
            }

            if ($imprimir) {
                echo '<tr style="font-size: 11px;">
                        <td width="25%" style="color: blue;">'.$des_cat_macho.'</td>
                        <td align="right" width="08%" style="color: blue;">'.$qtd_macho.'</td>
                
                        <td width="25%"style="color: orange;">'.$des_cat_femea.'</td>
                        <td  align="right" width="08%" style="color: orange;">'.$qtd_femea.'</td>

                        <td width="25%" style="color: gray;"></td>
                        <td align="right"  width="09%" style="color: gray;"></td>
                    </tr>
                ';    
            }
        }
        else if ($i==2) {
            $qtd_macho = '';
            $qtd_femea = '';
            $des_cat_macho = '';
            $des_cat_femea = '';
            $imprimir = '';

            for ($j=1; $j <=4; $j++) { 
                if ($arrayMacho[$j]!=0 && $arrayMachoImp[$j]=='') {
                    $qtd_macho = $arrayMacho[$j];
                    $des_cat_macho = $desc_categoria[$j];
                    $arrayMachoImp[$j]='S';
                    $j=4;
                    $imprimir = 'S';
                }
            }

            for ($j=1; $j <=4; $j++) { 
                if ($arrayFemea[$j]!=0 && $arrayFemeaImp[$j]=='') {
                    $qtd_femea = $arrayFemea[$j];
                    $des_cat_femea = $desc_categoria[$j];
                    $arrayFemeaImp[$j]='S';
                    $j=4;
                    $imprimir = 'S';
                }
            }

            if ($imprimir) {
                echo '<tr style="font-size: 11px;">
                        <td width="25%" style="color: blue;">'.$des_cat_macho.'</td>
                        <td align="right" width="08%" style="color: blue;">'.$qtd_macho.'</td>
                
                        <td width="25%"style="color: orange;">'.$des_cat_femea.'</td>
                        <td  align="right" width="08%" style="color: orange;">'.$qtd_femea.'</td>
                        <td width="25%" style="color: gray;"></td>
                        <td align="right"  width="09%" style="color: gray;"></td>
                    </tr>
                ';    
            }
        }
        else {
            $qtd_macho = '';
            $qtd_femea = '';
            $des_cat_macho = '';
            $des_cat_femea = '';
            $imprimir = '';

            for ($j=1; $j <=4; $j++) { 
                if ($arrayMacho[$j]!=0 && $arrayMachoImp[$j]=='') {
                    $qtd_macho = $arrayMacho[$j];
                    $des_cat_macho = $desc_categoria[$j];
                    $arrayMachoImp[$j]='S';
                    $j=4;
                    $imprimir = 'S';
                }
            }

            for ($j=1; $j <=4; $j++) { 
                if ($arrayFemea[$j]!=0 && $arrayFemeaImp[$j]=='') {
                    $qtd_femea = $arrayFemea[$j];
                    $des_cat_femea = $desc_categoria[$j];
                    $arrayFemeaImp[$j]='S';
                    $j=4;
                    $imprimir = 'S';
                }
            }

            if ($imprimir) {
                echo '<tr style="font-size: 11px;">
                        <td width="25%" style="color: blue;">'.$des_cat_macho.'</td>
                        <td align="right" width="08%" style="color: blue;">'.$qtd_macho.'</td>
                
                        <td width="25%"style="color: orange;">'.$des_cat_femea.'</td>
                        <td  align="right" width="08%" style="color: orange;">'.$qtd_femea.'</td>

                        <td width="25%" style="color: gray;"></td>
                        <td align="right"  width="09%" style="color: gray;"></td>
                    </tr>
                ';              
            }
        }
    }
    // Fim Exibir animais por categoria
?>                                        

                                    </tbody>

                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="row">&nbsp;</div>
                        
                        <input type="hidden" id="dispositivo">

                        <div class="row desktop">
                            <div class="col-xs-8 col-md-4">
                                <label class="control-label" for="categoria_sexo_d"><span class="required">*</span> Transferir Animais de Pasto?</label>

                                <select class="form-control categoria_sexo" id="categoria_sexo_d" name="categoria_sexo_d">

                                    <option value='0'>Qual Categoria</option>
                                </select>                                
                            </div>

                            <div class="col-xs-4 col-md-2">
                                <label for="quantidade_d" class="control-label">&nbsp;</label>

                                <input type="number" name="quantidade_d" id="quantidade_d" class="form-control quantidade" placeholder="Qtde">
                            </div>

                            <div class="col-xs-8 col-md-4">
                                <label for="novo_pasto_d" class="control-label">&nbsp;</label>

                                <select class="form-control novo_pasto" id="novo_pasto_d" name="novo_pasto_d">
                                </select>                     
                            </div>

                            <div class="form-group col-xs-4 col-md-2">
                                <label class="control-label">&nbsp;</label>

                                <button class='form-control btn btn-success confirma' type='button'
                                onclick='retirar_por_categoria();'>Confirma
                                    </button>
                            </div>
                        </div> 

                        <div class="row form-group desktop">
                            <div class="col-xs-12 col-md-12">
                                <label for="descricao_lote_d" class="control-label">Descrição do Lote</label>

                                <input type="text" name="descricao_lote_d" id="descricao_lote_d" class="form-control descricao_lote" onkeyup='maiuscula(this)' onclick='abrir_modal_descricao_lote(0)' <?php echo "value='".$descricao_lote_com_id."'";?>>
                            </div>


                        </div> <!-- Fim classe Desktop -->

                        <div class="row mobile" hidden>
                            <div class="col-xs-8 col-md-5">
                                <label class="control-label" for="categoria_sexo_m"><span class="required">*</span> Transferir Animais de Pasto?</label>

                                <select class="form-control categoria_sexo" id="categoria_sexo_m" name="categoria_sexo_m">

                                    <option value='0'>Qual Categoria</option>
                                </select>                                
                            </div>

                            <div class="col-xs-4 col-md-3">
                                <label for="quantidade_m" class="control-label">&nbsp;</label>

                                <input type="number" name="quantidade_m" id="quantidade_m" class="form-control quantidade" placeholder="Qtde">
                            </div>
                        </div>

                        <div class="row mobile" hidden>
                            <div class="col-xs-8 col-md-5">
                                <label for="novo_pasto_m" class="control-label">&nbsp;</label>

                                <select class="form-control novo_pasto" id="novo_pasto_m" name="novo_pasto_m">
                                </select>                     
                            </div>

                            <div class="form-group col-xs-4 col-md-2">
                                <label for="" class="control-label">&nbsp;</label>

                                <button class='form-control btn btn-success confirma' type='button'
                                onclick='retirar_por_categoria();'>Confirma
                                    </button>
                            </div>
                        </div> 

                        <div class="row form-group mobile" hidden>
                            <div class="col-xs-12 col-md-12">
                                <label for="descricao_lote_m" class="control-label">Descrição do Lote</label>

                                <input type="text" name="descricao_lote_m" id="descricao_lote_m" class="form-control descricao_lote" onkeyup='maiuscula(this)' onclick='abrir_modal_descricao_lote(0)' <?php echo "value='".$descricao_lote_com_id."'";?>>

                            </div>


                        </div> <!-- Fim classe Mobile -->

                        <div class="row form-group">
                            <div class="col-xs-12 col-md-12 span_centro">
                                <span class="info_pasto">Outras Atividades</span>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="form-group col-xs-4 col-md-4">
                                <button class='form-control btn btn-primary outras_atividades' type='button'
                                onclick='abrir_modal_nutricao();'>Nutrição
                                </button>
                            </div>

                            <div class="form-group col-xs-4 col-md-4">
                                <button class='form-control btn btn-primary outras_atividades' type='button'
                                onclick='abrir_modal_nascimento();'>Nascimento
                                </button>
                            </div>

                            <div class="form-group col-xs-4 col-md-4">
                                <button class='form-control btn btn-primary outras_atividades' type='button'
                                onclick='abrir_modal_morte();'>Morte
                                </button>
                            </div>
                        </div>
                    </div>
                <!--</div>-->
            </div>

	        <!-- page end-->
            <div class='modal fade' id='modal_nutricao' tabindex='-1' role='dialog' aria-labelledby='modal_incluirCenterTitle' data-backdrop='static'>

                <div class='modal-dialog modal-dialog-centered modal-lg' role='document' style='width: 100%;'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h4 class='modal-title' id='modal_incluirLabel'>Mapa de Gado - Nutrição</h4>
                        </div>

                        <div class='modal-body'>
                            <form method='POST' action='' enctype='multipart/form-data' id='form_gravar_nutricao'>

                                <input type='hidden' name='grupo_usuario' id='grupo_usuario' <?php echo "value='".$grupo_usuario."'";?> >
                                  
                                <!--<input type='hidden' name='tipo_gravacao' id='tipo_gravacao' value='0'>-->

                                <input type='hidden' id='data_hoje' <?php echo "value='".$data_sistema."'";?>>
                                   
                                <input name='local_id_nutricao' type='hidden' id='local_id_nutricao' <?php echo "value='".$local_id."'";?>>

                                <input name='pasto_id_nutricao' type='hidden' id='pasto_id_nutricao' <?php echo "value='".$pasto_id."'";?>>

                                <div class='tab-content'>
                                    <div id='dados' class='tab-pane active'>
                                        <button type='button' class='btn btn-info pull-right' data-dismiss='modal' onclick='location.reload();'>Voltar
                                        </button>

                                        <div class='row' style='padding: 10px 10px 20px 14px;'>
                                            <p class='nome_fazenda'><?php echo $desc_local;?></p>

                                            <div class='info_pasto' style='text-align: center !important;'>
                                                Pasto: <?php echo $desc_pasto;?>
                                            </div>
                                        </div>

                                        <div class='row'>
                                            <div class='form-group col-md-7'>
                                                <label class='control-label' for='slctCocho'><span class='required'>*</span> Situação do Cocho</label>
                                                <select name='slctCocho' id='slctCocho' class='form-control custom-select' onchange='selecionouCocho()'>
                                                    <option value='000000000'>...</option>
                                                </select>
                                            </div>
                                            <div class='form-group col-md-3'>
                                                <label class='control-label' for='dataNutricao'><span class='required'>*</span>Data</label>

                                                <input type='date' name='dataNutricao' id='dataNutricao' class='form-control' <?php echo "value='".$data_sistema."'";?> onchange='lerNutricao()'>
                                            </div>
                                        </div>

                                        <div class='row'>
                                            <div class='form-group col-md-5'> 
                                                <label class='control-label' for='nomeProduto'><span class='required'>*</span>Produto</label>

                                                <select name='nomeProduto' id='nomeProduto' class='form-control custom-select' onchange='selecionouProduto()'>
                                                    <option value='000000000'>...</option>
                                                </select>
                                            </div>

                                            <div class='form-group col-md-2'>
                                                <label class='control-label' for='qtdProduto'><span class='required'>*</span>Quantidade</label>
                                                <input type='number' class='form-control custom-select' name='qtdProduto' id='qtdProduto'>
                                            </div>

                                            <div class='form-group col-md-2'>
                                                <label class='control-label'>Und</label>
                                                <input type='text' class='form-control' name='undProduto' id='undProduto' disabled style='background-color: white; color: black;'>
                                            </div>

                                            <div class='form-group col-md-3'>
                                                <label class="control-label">&nbsp;</label>

                                                <button type='button' class='form-control btn btn-success' 
                                                onClick='confirma_nutricao()'>Confirmar Inclusão
                                                </button>
                                            </div>
                                        </div>

                                        <hr style='border-top: 1px solid #eee'>

                                        <div class="table-responsive" id='tabelaProdutos'
                                        style="border: none;">
                                        </div>

                                    </div> <!-- fim tab-pane active-->
                                </div> <!-- Fim tab-content -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class='modal fade' id='modal_nascimento' tabindex='-1' role='dialog' aria-labelledby='modal_incluirCenterTitle' data-backdrop='static'>

                <div class='modal-dialog modal-dialog-centered modal-lg' role='document' style='width: 100%;'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h4 class='modal-title' id='modal_incluirLabel'>Mapa de Gado - Nascimento</h4>
                        </div>

                        <div class='modal-body'>
                            <form method='POST' action='gravar_nascimento.php' enctype='multipart/form-data' id='form_gravar_animal'>
                                <input type='hidden' name='grupo_usuario' id='grupo_usuario' <?php echo "value='".$grupo_usuario."'";?> >
                                  
                                <input type='hidden' name='tipo_gravacao' id='tipo_gravacao' value='0'>

                                <input type='hidden' name='num_mov_nascimento'  id='num_mov_nascimento' value='0'>

                                <input name='codigo_mae_animal' type='hidden' id='codigo_mae_animal'>

                                <input name='dias_nascimento' type='hidden' id='dias_nascimento'>

                                <input name='cobertura_id' type='hidden' id='cobertura_id'>

                                <input name='item_cobertura' type='hidden' id='item_cobertura'>

                                <input name="tipo_cobertura" type="hidden" id="tipo_cobertura">

                                <input name="data_prenhes" type="hidden" id="data_prenhes">

                                <input name='estacao_monta_id' type='hidden' id='estacao_monta_id'>

                                <input name='data_inseminacao' type='hidden' id='data_inseminacao'>

                                <input type='hidden' id='data_hoje' <?php echo "value='".$data_sistema."'";?>>
                                   
                                <input name='local_id' type='hidden' id='local_id' <?php echo "value='".$local_id."'";?>>

                                <input name='pasto_id' type='hidden' id='pasto_id' <?php echo "value='".$pasto_id."'";?>>

                                <div class='tab-content'>
                                    <div id='dados' class='tab-pane active'>
                                        <button type='button' class='btn btn-info pull-right' data-dismiss='modal' onclick='location.reload();'>Voltar
                                        </button>

                                        <div class='row' style='padding: 0 14px 0 14px'>
                                            <p class='nome_fazenda'><?php echo $desc_local;?></p>

                                            <div class='info_pasto'>
                                                Pasto: <?php echo $desc_pasto;?>
                                            </div>
                                        </div>

                                        <hr style='border-top: 1px solid #eee'>

                                        <!-- Campos comuns-->
                                        <div class='row ocorrencias' hidden>
                                            <div class='col-md-3'>
                                                <label class='control-label'><span class='required'>*</span> Selecione uma opção</label>

                                                <div class='clearfix'></div>

                                                <label class='radio-inline'>
                                                    <input type='radio' name='opcao_nascimento' id='opcao_nascimento' value='N' class='opcao_nascimento_mapa'>Nascimento
                                                </label>
                                            </div>

                                            <div class='col-md-5'>
                                                <label class='control-label'>&nbsp;</label>

                                                <div class='clearfix'></div>

                                                <label class='radio-inline'>
                                                    <input type='radio' name='opcao_nascimento' id='opcao_aborto' value='A' class='opcao_nascimento_mapa'>Aborto
                                                </label>

                                                <label class='radio-inline'>
                                                    <input type='radio' name='opcao_nascimento' id='opcao_absorcao' value='B' class='opcao_nascimento_mapa'>Absorção
                                                </label>

                                                <label class='radio-inline'>
                                                    <input type='radio' name='opcao_nascimento' id='opcao_morte' value='M' class='opcao_nascimento_mapa'>Natimorto
                                                </label>
                                            </div>
                                        </div>

                                        <hr align='center'>

                                        <div class='campos_data_mae_pai' hidden>
                                            <div class='row'>
                                                <div class='form-group col-md-4'>
                                                    <label class='control-label label_data'>Data Nascimento</label>

                                                    <input name='nascimento_animal' type='date' class='form-control' id='nascimento_animal' <?php echo "value='".$data_sistema."'";?>>
                                                </div>

                                                <div class='form-group col-md-4 codigo_mae_animal'>
                                                    <label for='codigo_mae_consulta' class='control-label label_mae'><span class='required'>*</span> Nº Mãe</label>

                                                    <input name='codigo_mae_consulta' type='text' class='form-control' id='codigo_mae_consulta' autocomplete='off'
                                                    onchange='ler_animal_mae()' onkeypress='return desabilita_enter (this, event)'>
                                                </div>

                                                <div class='form-group col-md-4 codigo_pai_animal'>
                                                    <label class='control-label'>Pai Nº
                                                    </label>

                                                    <select class='form-control' id='codigo_pai_animal' name='codigo_pai_animal'>
                                                    
                                                    <option value='000000000'>...</option>

                                                    <optgroup label='SEMEM'>";

                                                    <?php while($reg_pai = mysqli_fetch_object($semem)) { ?>
                                                        <option value="<?php echo $reg_pai->tbl_semem_codigo_id ?>">
                                                                                                                                                            
                                                    <?php 
                                                        echo $reg_pai->tbl_semem_nome.' - '.$reg_pai->tab_descricao_raca;
                                                    ?>
                                                        </option> 
                                                    <?php } ?>
                                                    </optgroup>

                                                    <optgroup label='ANIMAIS'>";
                                                    <?php while($reg_pai = mysqli_fetch_object($pai)) { ?>
                                                        <option value="<?php echo $reg_pai->tbl_animal_codigo_id ?>">
                                                    <?php 
                                                        echo $reg_pai->tbl_animal_codigo_alfa.' '.
                                                             $reg_pai->tbl_animal_codigo_numerico.' - '.$reg_pai->tab_descricao_raca;
                                                    ?>
                                                        </option>
                                                    <?php } ?>
                                                    </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='nascimento_id' hidden>
                                            <div class='row'>
                                                <div class='form-group col-md-2 alfa_animal'>
                                                    <label for='alfa_animal' class='control-label'>Código Alfa</label>
                                                    <input name='alfa_animal' type='text' class='form-control' id='alfa_animal' maxlength='4' placeholder='Letras' 
                                                        onkeyup='maiuscula(this)'>

                                                    <input type='hidden' id='codigo_alfa_anterior' >
                                                </div>

                                                <div class='form-group col-md-2'>
                                                    <input type='hidden' name='codigo_animal_id' id='codigo_animal_id'>

                                                    <label for='codigo_numerico_animal' class='control-label'> Nº Animal</label>

                                                    <input name='codigo_numerico_animal' type='number' class='form-control' id='codigo_numerico_animal' maxlength='9' placeholder='Números'>

                                                    <input type='hidden' id='codigo_numerico_anterior'>
                                                </div>

                                                <div class='form-group col-md-4'>
                                                    <label class='control-label'>&nbsp;</label>

                                                    <h5>
                                                        <span class="tipo_estacao_monta">Estação de Monta:</span>
                                                        <span id='ultima_estacao' >
                                                        </span>
                                                        <a href='#' style='color: blue' onclick='lista_femeas_servidas()'>
                                                        <img src='img/exclamacao.png' class="fa fa-exclamation-triangle icon_nascimentos_previstos" data-toggle='tooltip' data-placement='right' title="Existem nascimentos dessa estação atrasados." width='25' height='28'/>

                                                        <!--<i class='icon_info_alt icon_nascimentos_previstos' data-toggle='tooltip' data-placement='right' title='Existem Nascimentos previstos para essa estação. Clique para ver.'></i>-->
                                                        </a> 
                                                    </h5>
                                                </div>

                                                <div class='form-group col-md-4'>
                                                    <label class='control-label'>&nbsp;</label>
                                                    <h5 class='desc_novo_nascimento' style='font-weight: 700;color: red; font-size: 13px;'>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='campos_id_aborto_lote' hidden>
                                            <div class='row'>
                                                <div class='form-group col-md-3 qtd_animal'>
                                                    <label class='control-label'><span class='required'>*</span> Qtd Animal</label>

                                                    <input name='qtd_animal' type='number' class='form-control' id='qtd_animal'
                                                    aria-describedby='arrobaHelpBlock'>

                                                    <small id='arrobaHelpBlock' class='form-text text-muted' style='color: #808080'>Nascimento e Sexo iguais</small>
                                                </div>

                                                <div class='form-group col-md-3'>
                                                    <label class='control-label'><span class='required'>*</span> Sexo</label>

                                                    <div class='clearfix'></div>

                                                    <label class='radio-inline'>
                                                    <input type='radio' name='sexo_animal' id='M' value='M'
                                                    class='sexo_animal'>Macho
                                                    </label>

                                                    <label class='radio-inline'>
                                                    <input type='radio' name='sexo_animal' id='F' value='F'
                                                    class='sexo_animal'>Fêmea
                                                    </label>
                                                </div>

                                                <div class='form-group col-md-3 raca_id'>
                                                    <?php
                                                        if ($controle_estoque=='I') :
                                                    ?>
                                                        <label for="raca_id" class="control-label"><span class="required">*</span> Raça</label>
                                                    <?php
                                                        else :
                                                    ?>
                                                        <label for="raca_id" class="control-label"> Raça</label>
                                                    <?php
                                                    endif;
                                                    ?>
                                                    <select class="form-control" name="raca_id" id="raca_id">
                                                        <option value="">...</option>
                                                        <?php while($reg_raca = mysqli_fetch_object($raca)) { ?>
                                                        <option value="<?php 
                                                            echo $reg_raca->tab_codigo_raca ?>">
                                                                            
                                                        <?php 
                                                            echo $reg_raca->tab_descricao_raca;
                                                        ?>
                                                        </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class='form-group col-md-3 pelagem_id'>
                                                    <label class='control-label'>Pelagem</label>
                                                    <select class='form-control' name='pelagem_id' id='pelagem_id'>
                                                    <option value=''>...</option>";

                                                    <?php while($reg_pelagem = mysqli_fetch_object($pelagem)) { ?>

                                                    <option value="<?php 
                                                        echo $reg_pelagem->tab_codigo_pelagem ?>">
                                                                                    
                                                        <?php 
                                                        echo $reg_pelagem->tab_descricao_pelagem;
                                                        ?>
                                                    </option>
                                                    <?php } ?>
                                                    </select>
                                                </div>

                                                <div class='form-group col-md-3 peso_animal'>
                                                    <?php
                                                        if ($controle_estoque=='I') {
                                                        echo '<label for="peso_animal" class="control-label"><span class="required">*</span> Peso</label>';
                                                        }
                                                        else {
                                                            echo '<label for="peso_animal" class="control-label"><span class="required">*</span> Peso Médio</label>';
                                                        }
                                                    ?>  

                                                    <input name='peso_animal' type='number' class='form-control' id='peso_animal'>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='row confirmar' hidden>  
                                            <div class='form-group col-md-12'>
                                                <button type='button' class='btn btn-success confirma_gravar' onClick='confirmar_nascimento()'>Confirmar</button>
                                            </div>
                                        </div>
                                    </div> <!-- Fim tab-pane active -->
                                </div> <!--Fim tab-content -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class='modal fade' id='modal_morte' tabindex='-1' role='dialog' 
            aria-labelledby='modal_incluirCenterTitle' aria-hidden='true'  data-backdrop='static'>
               <div class='modal-lg modal-dialog modal-dialog-centered' role='document' style='width: 100%;'>
                   <div class='modal-content'>
                       <div class='modal-header'>
                           <h4 class='modal-title' id='modal_incluirLabel'>Mapa de Gado - Morte </h4>
                       </div>

                       <div class='modal-body'>
                           <form method='POST' action='#' enctype='multipart/form-data' id='form_gravar_morte'>
                               <input name='codigo_id_morte' type='hidden' id='codigo_id_morte' value='0'>
                               <input name='sexo_animal_morte' type='hidden' id='sexo_animal_morte'>
                               <input name='peso_animal_morte' type='hidden' id='peso_animal_morte'>
                               <input name='nascimento_animal_morte' type='hidden' id='nascimento_animal_morte'>
                               <input name='raca_animal_morte' type='hidden' id='raca_animal_morte'>
                               <input name='pelagem_animal_morte' type='hidden' id='pelagem_animal_morte'>
                               <input name='mae_animal_morte' type='hidden' id='mae_animal_morte'>
                               <input name='motivo_animal_morte' type='hidden' id='motivo_animal_morte'>
                               <input name='codigo_motivo_morte' type='hidden' id='codigo_motivo_morte'>
                                <input type='hidden' name='sexo_morte' id='sexo_morte'>
                                <input type='hidden' name='categoria_digitada_morte' id='categoria_digitada_morte'>

                               <input name='array_itens' type='hidden' id='array_itens'>

                                <input name='local_morte' type='hidden' id='local_morte' <?php echo "value='".$local_id."'";?>>

                                <input name='pasto_morte' type='hidden' id='pasto_morte' <?php echo "value='".$pasto_id."'";?>>

                               <div class='tab-content'>
                                   <div class='alert alert-danger alert_erro_animal' id='alert_erro_animal' hidden='true'>
                                       <strong class='negrito'></strong><span></span>
                                   </div> 

                                   <div id='dados' class='tab-pane active'>
                                        <button type='button' class='btn btn-info pull-right' data-dismiss='modal' onclick='location.reload();'>Voltar</button>

                                        <div class='row' style='padding: 0 14px 0 14px'>
                                            <p class='nome_fazenda'><?php echo $desc_local;?></p>

                                            <div class='info_pasto'>
                                                Pasto: <?php echo $desc_pasto;?>
                                            </div>
                                        </div>

                                        <hr style='border-top: 1px solid #eee'>

                                       <div class='row'>
                                           <div class='form-group col-xs-6 col-md-4 id_animal'>
                                               <label for='id_animal_morte' class='control-label'><span class='required'>*</span> Nº Animal</label>
                                               <input name='id_animal_morte' type='text' class='form-control' id='id_animal_morte' autocomplete='off'
                                               onchange='ler_animal_morte();animal_sem_id();' >
                                           </div>

                                            <div class='form-group col-xs-6  col-md-8'>
                                                <label class='control-label'>&nbsp;</label>
                                                <p id='descricao_animal_morte' class='text-primary'></p>
                                            </div>
                                        </div>

                                       <div class='row'>
                                           <div class='form-group col-xs-6 col-md-7'>
                                                <label for='motivo_morte' class='control-label'><span class='required'>*</span> Motivo da Morte</label>
                                                <select class='form-control form-select' id='motivo_morte' name='motivo_morte'>

                                                <option value='000'>...</option>";
                                                <?php
                                                    while($reg_motivo = mysqli_fetch_object($tbl_motivo_morte)) {
                                                        
                                                        echo "<option value=$reg_motivo->tab_codigo_causa_morte>";
                                                        echo $reg_motivo->tab_descricao_causa_morte;
                                                        echo "</option>";
                                                    }
                                                ?> 
                                                </select>
                                           </div>

                                           <div class='form-group col-xs-6 col-md-5'>
                                               <label for='data_morte_animal' class='control-label'><span class='required'>*</span> Data da morte</label>

                                               <input name='data_morte_animal' type='date' class='form-control' id='data_morte_animal' <?php echo "value='".$data_sistema."'";?>>
                                            </div>
                                       </div>
                                    
                                        <div class='row'>
                                            <div class='form-group col-md-8 info_modal_morte' hidden>
                                               <label for='categoria_morte' class='control-label'><span class='required'>*</span> Categoria</label>
                                               <select class='form-control form-select' id='categoria_morte' name='categoria_morte'>

                                               <option value='000'>...</option>
                                               </select>

                                            </div>
                                        </div>

                                       <div class='row'>
                                           <div class='form-group col-md-12'>
                                               <label for='observacao_morte' class='control-label'>Observação</label>

                                               <textarea name='observacao_morte' type='text' class='form-control' id='observacao_morte' rows='3' onkeyup='maiuscula(this)'></textarea>
                                           </div>
                                       </div>

                                       <div class='row'>
                                           <div class='form-group col-md-12'>
                                               <button type='button' class='btn btn-success confirma_gravar_morte' onClick='salvar_morte()'>Confirmar</button>
                                           </div>
                                        </div>

                                   </div> <!-- fim tab-pane active-->
                               </div> <!-- Fim tab-content-->
                           </form>
                       </div>
                   </div>
               </div>
            </div>

            <div class='modal fade' id='mensagem_retorno_inclusao' tabindex='-1' role='dialog' 
                        aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='static'>
                <div class='modal-dialog modal-dialog-centered' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <!--<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>-->
                            <h4 class='modal-title'>Mapa de Gado - Nascimento </h4>
                        </div>
                        <div class='modal-body'></div>
                        <div class='modal-footer'>
                            <button class='btn btn-default' type='button' onclick='ler_animal_nascimento();'>Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class='modal fade' id='mensagem_retorno_morte' tabindex='-1' role='dialog' 
                aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='static'>
                <div class='modal-dialog modal-dialog-centered' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <!--<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>-->
                            <h4 class='modal-title'>Mapa de Gado - Morte </h4>
                        </div>
                        <div class='modal-body'></div>
                        <div class='modal-footer'>
                            <button data-dismiss='modal' class='btn btn-default' type='button' onclick='abrir_modal_morte();'>Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Mapa de Gado</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick='fecharNutricao()'>Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Mapa de Gado - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <!--<button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button> -->                           

                            <button class="btn btn-default" type="button" onclick="fechar_erro_gravar()">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_sucesso_mover_animais" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Mapa de Gado - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button class="btn btn-default" type="button" onclick="exibe_opcoes_desc_lote_pasto_destino()">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_mover_animais" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Mapa de Gado - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                        </div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>                          
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_pasto_vazio" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Mapa de Gado - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                            <p class="mensagem_pasto"></p>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_data" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Mapa de Gado - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_nascimento_nove_meses" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                            <p class="mensagem_nove_meses"></p>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="fechar_nascimento_nove_meses()">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="nascimento_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <p style="font-weight: bold;"><span id="tem_estacao">Estação </span><span id="estacao_nascimento"></span> Animal com ( <span id="calculo_dias_nascimento"></span> dias ) de gestação. Previsão (~ 282 dias)</p>    

                            <p class="mens_dias_gestacao" style="color: red;">Entre em contato com o Administrador do Sistema</p>

                            <p class="mens_alterar_prenhes" style="color: red;">Atenção! Ao confirmar o nascimento você estará alterando a data da prenhes.</p>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success gravar" type="button" onclick="gravar_nascimento();">Confirmar Nascimento</button>

                            <button class="btn btn-primary substituir" type="button" onclick="gravar_nascimento_monta_natural();">Substituir por Monta Natural</button>

                            <button data-dismiss="modal" class="btn btn-danger" type="button" onclick="fechar_nascimento_erro();">Não Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="nascimento_gemelar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <p class="desc_gemelar"></p>     
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="volta_nascimento_mensagem();">Voltar e conferir a Fêmea</button>

                            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true" onclick="nascimento_gemelar()">Nascimento Gemelar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="nascimento_aborto_natimorto" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                            <p class="mensagem_aborto_natimorto"></p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onclick="volta_nascimento_mensagem();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class='modal fade' id='modal_mover_todos' tabindex='-1' role='dialog' 
                aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='static'>
                <div class='modal-dialog modal-dialog-centered' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <!--<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>-->
                            <h4 class='modal-title'>Mapa de Gado - Mensagem</h4>
                        </div>
                        <div class='modal-body'>
                            <p id="primeira_mensagem"></p>
                            <p id="segunda_mensagem"></p>
                        </div>

                        <div class='modal-footer'>
                            <button class='btn btn-success' type='button' onclick='gravar_retirar_categoria();'>Sim</button>

                            <button data-dismiss='modal' class='btn btn-default' type='button'>Não</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class='modal fade' id='mensagem_manter_desc_pasto_destino' tabindex='-1' role='dialog' 
                aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='static'>
                <div class='modal-dialog modal-dialog-centered' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <!--<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>-->
                            <h4 class='modal-title'>Composição da Descrição do Lote</h4>
                        </div>
                        <div class='modal-body'>
                        </div>

                        <div class='modal-footer'>
                            <button class='btn btn-success' type='button' onclick='fechar_mensagem_sucesso();'>Sim</button>

                            <button data-dismiss='modal' class='btn btn-default' type='button' onclick='retorna_composicao_descricao_lote();'>Não</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class='modal fade' id='mensagem_levar_desc_pasto_destino' tabindex='-1' role='dialog' 
                aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='static'>
                <div class='modal-dialog modal-dialog-centered' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <!--<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>-->
                            <h4 class='modal-title'>Composição da Descrição do Lote</h4>
                        </div>
                        <div class='modal-body'>
                        </div>

                        <div class='modal-footer'>
                            <button class='btn btn-success' type='button' onclick='gravar_levar_descricao_lote_pasto_destino();'>Sim</button>

                            <button data-dismiss='modal' class='btn btn-default' type='button' onclick='retorna_composicao_descricao_lote();'>Não</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="aguardar" tabindex="-1" role="dialog"    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
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

            <div class="modal fade" id="modal_composicao_descricao_lote" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle myLargeModalLabel"  data-backdrop="static">
                <div class="modal-lg modal-dialog modal-dialog-centered" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->

                            <h4 class="modal-title">Composição da Descrição do Lote
                            </h4>

                            <input type="hidden" name="numero_item" id="numero_item">

                            <input type="hidden" name="edicao" id="edicao">

                            <input type="hidden" id="id_pasto_destino">
                            <input type="hidden" id="desc_pasto_destino">
                            <input type="hidden" id="desc_lote_destino">
                            <input type="hidden" id="qual_pasto">
                            <input type="hidden" id="qual_programa" value="movimentacao">
                            <input type="hidden" id="descricao_lote_montada">
                            <input type="hidden" id="pasto_destino_estava_vazio">
                        </div>

                        <div class="modal-body">
                            <div class="container">

                            <div class='row'>
                                <div class="col-xs-12 col-md-12 span_centro">
                                     <span class="info_pasto desc_pasto"><?php echo $desc_pasto;?>
                                    </span>
                                </div>                             
                            </div>

                            <div class='row'>
                                <div class="col-xs-12 col-md-12 span_centro">
                                    <span class="info_lote desc_lote"><?php echo $descricao_lote;?>
                                    </span>
                                </div>                             
                            </div>

                            <div class='row opcoes_descricao_lote' hidden>
                                <div class='col-xs-12 col-md-12'>
                                    <div class="form-check manter_lote">
                                        <input class="form-check-input opcao_lote" type="radio" name="opcao_lote" id="manter_lote" value="M">
                                        <label class="form-check-label" for="manter_lote"> Manter a Descrição do Lote
                                        </label>
                                    </div>

                                    <div class="form-check novo_lote">
                                        <input class="form-check-input opcao_lote" type="radio" name="opcao_lote" id="novo_lote" value="N">
                                        <label class="form-check-label" for="novo_lote"> Criar nova Descrição do Lote
                                        </label>
                                    </div>

                                    <div class="form-check levar_lote">
                                        <input class="form-check-input opcao_lote" type="radio" name="opcao_lote" id="levar_lote" value="L">
                                        <label class="form-check-label" for="levar_lote"> Levar a Descrição do Lote
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr class="linha_hr">

                            <div class="monta_descricao_lote" hidden>
                            <div class='row'>
                                <div class="form-group col-md-3 descricao_principal">
                                    <label class="control-label"><span class="required">*</span> Descrição do Lote</label>
                                    <select class="form-control" name="descricao_principal" id="descricao_principal" onchange="popular_situacao()">
                                    </select>
                                </div>

                                <div class='form-group col-md-3 exibir_parametro_2' hidden>
                                    <label class="control-label label_parametro_2">Situação</label>
                                    <select class="form-control" name="situacao_principal" id="situacao_principal" onchange="exibir_parametro_3()">
                                    </select>
                                </div>

                                <div class='form-group col-md-4 exibir_parametro_3' hidden>
                                    <label class="control-label label_parametro_3">Informar Data da Parição? </label>

                                    <div class="clearfix"></div>
                                    
                                    <label class="checkbox-inline">
                                        <input type="checkbox" id="com_data" name="data_paricao" value="S"> Sim
                                    </label>
                                </div>

                                <div class='col-md-3 exibir_parametro_4' hidden>
                                    <label class="control-label label_parametro_4">Mês/Ano da Parição</label>

                                    <input type="month" class="form-control" name="data_paricao_principal" id="data_paricao_principal" onchange="exibe_descricao_lote()">
                                </div>

                                <div class='col-md-3 exibir_parametro_4_data_mais' hidden>
                                    <label class="control-label label_parametro_4_mais">Mês/Ano da Parição</label>

                                    <input type="month" class="form-control" name="data_paricao_principal_mais" id="data_paricao_principal_mais" onchange="exibe_descricao_lote_mais_data()">
                                </div>

                                <div class='col-md-3 exibir_parametro_4_mais' hidden>
                                    <label class="control-label">&nbsp;</label>

                                    <a class='btn' href='#' style="font-size: 0.8em; font-weight: 500; color: #128cb8; float: right;" onclick="incluir_mais_data(1)"><i class="fa fa-plus" data-toggle='tooltip' data-placement='left' title='Informar mais datas'></i> Incluir mais Data</a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 exibir_incluir_mais">
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.8em; font-weight: 500; text-align: right; color: #128cb8;" onclick="incluir_mais_lote()"><i class="fa fa-plus" data-toggle='tooltip' data-placement='left' title=''></i> Incluir mais lote</a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao">
                                    <input type="text" id='descricao_novo_lote' class="form-control" readonly style="border: none; background-color: transparent;">
                                </div>
                                <div class="col-md-1 exibir_opcoes">
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(1)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao2" hidden> 
                                    <input type="text" id='descricao_novo_lote2' class="form-control" readonly style="border: none; background-color: transparent;">
                                </div>

                                <div class="col-md-1 exibir_opcoes2" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(2)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao3" hidden> 
                                    <input type="text" id='descricao_novo_lote3' class="form-control" readonly style="border: none; background-color: transparent;">
                                    
                                </div>

                                <div class="col-md-1 exibir_opcoes3" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(3)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao4" hidden> 
                                    <input type="text" id='descricao_novo_lote4' class="form-control" readonly style="border: none; background-color: transparent;">
                                    
                                </div>

                                <div class="col-md-1 exibir_opcoes4" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(4)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao5" hidden> 
                                    <input type="text" id='descricao_novo_lote5' class="form-control" readonly style="border: none; background-color: transparent;">
                                    
                                </div>

                                <div class="col-md-1 exibir_opcoes5" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(5)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao6" hidden> 
                                    <input type="text" id='descricao_novo_lote6' class="form-control" readonly style="border: none; background-color: transparent;">
                                    
                                </div>

                                <div class="col-md-1 exibir_opcoes6" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(6)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>
                            </div> <!--Fim monta descricao lote -->
                        </div> <!-- Fim container --> 
                        </div> <!-- Fim modal-body-->

                        <div class="modal-footer">
                            <div class=" monta_descricao_lote" hidden>
                                <button type="button" class="btn btn-primary confirma_composicao" onclick="confirma_composicao_descricao_lote()">Confirmar
                                </button>

                                <button type='button' class='btn btn-info pull-right voltar_descricao_lote' data-dismiss='modal'>Voltar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_descricao_lote" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Composição da Descrição do Lote - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onclick="fecha_mensagem_erro_descricao_lote();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_sucesso_descricao_lote_destino" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Composição da Descrição do Lote - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onclick="trocar_id_lote_pasto_origem();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_sucesso_descricao_lote_origem" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Composição da Descrição do Lote - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onclick="exibe_opcoes_desc_lote_pasto_origem();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_sucesso_descricao_novo_lote_destino" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Composição da Descrição do Lote - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onclick="fechar_mensagem_sucesso();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Esse modal é para a resposta de suscesso no nascimento e quando o pasto esta sem lote -->
            <div class="modal fade" id="mensagem_descricao_lote" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>

                        <div class="modal-body"></div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-info" type="button" onclick="abrir_modal_descricao_lote(1)">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_estacao" tabindex="-1" role="dialog" aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>

                                    <p class="desc_modal" style="font-weight: bold;">Essa Fêmea não está em estação de monta.</p>

                                    <p class="mens_administrador" style="color: red;">Entre em contato com o Administrador do Sistema</p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_1"></span></p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_2"></span></p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_3"></span></p>
                                </div>
                            </div>

                            <div class="row estacao_monta">
                                <div class="form-group col-md-6">
                                    <label for="estacao_monta" class="control-label"><span class="required">*</span> Nova Estação de Monta</label>

                                    <select class="form-control" id="estacao_monta" name="estacao_monta">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success outra_estacao" type="button" onclick="confirmaEstacao()">Confirma Estação de Monta
                            </button>

                            <button data-dismiss="modal" class="btn btn-primary substituir" type="button" onclick="substituir_por_monta_natural();">Substituir por Monta Natural</button>

                            <button class="btn btn-default alterar_diagnostico" type="button" onclick="alterardiagnostico()">Alterar Diagnóstico
                            </button>

                            <button data-dismiss="modal" class="btn btn-danger outra_femea" type="button" onclick="selecinarOutraFemea()">Selecione Outra Fêmea
                            </button>

                            <button data-dismiss="modal" class="btn btn-default voltar" type="button" onclick="voltarModalEstacao()">Voltar
                            </button>

                            <button data-dismiss="modal" class="btn btn-default fechar" type="button" onclick="fecharModalEstacao()">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="modal_sem_estacao" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <p style="font-weight: bold;">Atenção!</p>    
                            <p class="mens_sem_1">Não está em estação de monta;</p>    
                            <p class="mens_sem_2">Não está na lista de Monta Natural;</p>  

                            <p class="mens_administrador" style="color: red;">Entre em contato com o Administrador do Sistema</p>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success alterar_diagnostico" type="button" onclick="gravar_nascimento_monta_natural();">Confirmar Nascimento Monta Natural</button>

                            <button data-dismiss="modal" class="btn btn-danger outra_femea" type="button" onclick="fechar_nascimento_erro();">Não Confirmar</button>
 
                            <button data-dismiss="modal" class="btn btn-default fechar" type="button">Fechar</button>
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
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js?<?php echo Versao; ?>"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>

<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js"></script>
<script src='js/jquery.redirect.js'></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
<script src="js/mapa_gados.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript"></script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
<script src="js/typeahead.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
 	   $('[data-toggle="tooltip"]').tooltip();   
    });
</script>

<script>
    //$(document).ready(function(){
        $('#codigo_mae_consulta').typeahead('destroy').typeahead({
            source: function(query, result) {
                $.ajax({
                    url:"fetch_femeas_servidas.php",
                    method:"POST",
                    data:{query:query,
                          local: $('#local_origem').val()},
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


        $('#id_animal_morte').typeahead('destroy').typeahead({
            source: function(query, result) {
                $.ajax({
                    url:"fetch.php",
                    method:"POST",
                    data:{query:query,
                          local: $('#local_origem').val()},
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
        //});
</script>

<script>

    $(document).ready(function(){
        $("#codigo_mae_consulta").click(function(){
            $("#codigo_mae_consulta").val('');
            $("#codigo_mae_animal").val('');
            document.getElementById("codigo_mae_consulta").style.borderColor = "";
            $(".desc_novo_nascimento").html('');
            return;
        });

        $('#id_animal_morte').click(function(){
            $('#codigo_id_morte').val(0);
            $('#descricao_animal_morte').text('');
            $('.alert_erro_animal .negrito').html('');
            $('.alert_erro_animal span').html('');
            $('.alert_erro_animal').hide();
            return;
        });
    });

    function reseta_confirma(){
        clickedConfirm = true;
    }

    $(document).ready(function(){
        needToConfirm = false;
        clickedConfirm = false; 
        window.onbeforeunload = askConfirm;
    });

    function askConfirm() {
        var dispositivo = $("#dispositivo").val();

        if (dispositivo=='D') {
            var categoria_sexo = $("#categoria_sexo_d").val();
            var qtd_destino = $("#quantidade_d").val();
            var pasto_destino = $("#novo_pasto_d").val();
        }
        else {
            var categoria_sexo = $("#categoria_sexo_m").val();
            var qtd_destino = $("#quantidade_m").val();
            var pasto_destino = $("#novo_pasto_m").val();
        }

        if (categoria_sexo=='000000000' && pasto_destino=='000000000' 
            && (qtd_destino=='' || qtd_destino==0)) {
                needToConfirm = false;
        }
        else {
            needToConfirm = true;
        }

        if(clickedConfirm){
            needToConfirm = false;
        }

        if (needToConfirm==true) {
            return ''; 
        }
    }
</script>

</body>

</html>
