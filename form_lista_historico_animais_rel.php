
<?php
    include "valida_sessao.inc";

    @ session_start(); 

    $data_sistema = date("Y-m-d");
    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];
    $controle_estoque = $_SESSION['controle_estoque'];
    $origem_relatorio=$_REQUEST['origem_relatorio'];
    $tipo_rel= $_REQUEST["tipo_rel"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

    $_SESSION['tipo_rel_historico_animais']=$tipo_rel;

    if ($tipo_rel=='G') {
        $local_filtro = $_REQUEST["local"];
        $origem_filtro = $_REQUEST["origem"];
        $raca_filtro = $_REQUEST["raca"];
        $categoria_filtro = $_REQUEST["categoria"];
        $pai_filtro = $_REQUEST["pai"];
        $mae_filtro = $_REQUEST["mae"];
        $sexo_filtro = $_REQUEST["sexo"];
        $peso_nasc_inicial = $_REQUEST["peso_nasc_inicial"];
        $peso_nasc_final = $_REQUEST["peso_nasc_final"];
        $peso_desmama_inicial = $_REQUEST["peso_desmama_inicial"];
        $peso_desmama_final = $_REQUEST["peso_desmama_final"];
        $peso_ult_inicial = $_REQUEST["peso_ult_inicial"];
        $peso_ult_final = $_REQUEST["peso_ult_final"];
        $data_nasc_inicial = $_REQUEST["data_nasc_inicial"];
        $data_nasc_final = $_REQUEST["data_nasc_final"];
        $ativo_filtro = $_REQUEST['ativo'];
        $situacao_vendido = $_REQUEST['situacao_vendido'];
        $situacao_morte = $_REQUEST['situacao_morte'];
        $situacao_outra = $_REQUEST['situacao_outra'];
        $solteiras = $_REQUEST["solteiras"];
        $descarte = $_REQUEST["descarte"];
        $paridas = $_REQUEST["paridas"];
        $data_paridas_ate = $_REQUEST["data_paridas"];
        $parto = $_REQUEST["parto"];
        $num_parto_de = $_REQUEST['num_parto_de'];
        $num_parto_ate = $_REQUEST['num_parto_ate'];
        $aborto = $_REQUEST["aborto"];
        $num_aborto_de = $_REQUEST['num_aborto_de'];
        $num_aborto_ate = $_REQUEST['num_aborto_ate'];
        $previsao_parto_de = $_REQUEST['previsao_parto_de'];
        $previsao_parto_ate = $_REQUEST['previsao_parto_ate'];
        $positivo = $_REQUEST['positivo'];
        $negativo = $_REQUEST['negativo'];

        $_SESSION['local_pesagem']=$local_filtro;
        $_SESSION['categoria_historico_animais']=$categoria_filtro;

        include "conecta_mysql.inc";

        if (isset($_REQUEST['estacao'])) {
            $estacao_filtro = $_REQUEST['estacao'];

            $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
                WHERE tbl_par_estacao_nome='$estacao_filtro'
                ORDER BY tbl_par_estacao_id ASC");  

            $num_rows = mysqli_num_rows($sql);
            $array_estacao = array();

            if ($num_rows!=0) {
                while ($reg_estacao = mysqli_fetch_object($sql)){
                    $codigo_estacao = $reg_estacao->tbl_par_estacao_id;
                    $array_estacao[] = $codigo_estacao;
                }

                $array_estacao = implode(',', $array_estacao);
            }
        }

        $westacao = "";
        if (!empty ($array_estacao)) {
        
            $array_estacao = explode(',', $array_estacao);

            $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(";
            $westacao.= implode(',', $array_estacao);
            $westacao.= ")";
        }

        $local= array();
        $matriz_itens = explode(",", $local_filtro);
        $quantidade_itens = count($matriz_itens);

        for($i=0; $i < $quantidade_itens; $i++) {
            $local[$i]=$matriz_itens[$i];
        }

        $local = implode(',', $local);
        $local = substr($local,0, -1);

        $wlocal = '';
        $wlocal_anterior = '';

        if ($local_filtro!='') {
            $wlocal = " AND tbl_animal_codigo_fazenda IN(";
            $wlocal.= $local;
            $wlocal.= ")";

            $wlocal_anterior = " AND (tbl_animal_codigo_fazenda IN(";
            $wlocal_anterior.= $local;
            $wlocal_anterior.= ")";
            $wlocal_anterior.= " OR (tbl_animal_codigo_origem IN(";
            $wlocal_anterior.= $local;
            $wlocal_anterior.= ") AND tbl_animal_situacao='V'))";
        }

        $origem= array();
        $matriz_itens = explode(",", $origem_filtro);
        $quantidade_itens = count($matriz_itens);

        for($i=0; $i < $quantidade_itens; $i++) {
            $origem[$i]=$matriz_itens[$i];
        }

        $origem = implode(',', $origem);
        $origem = substr($origem,0, -1);

        $worigem = '';

        if ($origem_filtro!='') {
            $worigem = " AND tbl_animal_codigo_origem IN(";
            $worigem.= $origem;
            $worigem.= ")";
        }

        $wsexo='';
        if ($sexo_filtro=='Todos') {
            $wsexo='';
        }
        else {
            $wsexo = " AND tbl_animal_sexo IN(";
            $wsexo .= "'" . $sexo_filtro . "'";
            $wsexo.= ")";
        }

        if ($ativo_filtro=='Todos') {
            $wativo='';
        }
        else {
            $wativo = " AND tbl_animal_ativo IN(";
            $wativo .= "'" . $ativo_filtro . "'";
            $wativo.= ")";
        }

        $wsituacao='';
        $situacoes='';
        
        if ($ativo_filtro=='Todos') {
            if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'N') {
                $situacoes = "''".','."'V'";
            }
            else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'N') {
                $situacoes = "''".','."'V'".','."'M'";
            }    
            else if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'S') {
                $situacoes = "''".','."'V'".','."'O'";
            }    
            else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'N') {
                $situacoes = "''".','."'M'";
            }    
            else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'S') {
                $situacoes = "''".','."'M'".','."'O'";
            }    
            else if ($situacao_vendido == 'N' && $situacao_morte == 'N' && $situacao_outra == 'S') {
                $situacoes = "''".','."'O'";
            }    
            else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'S') {
               $situacoes = "''".','."'V'".','."'M'".','."'O'";
            }    
        }
        else if ($ativo_filtro=='N') {
            if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'N') {
                $situacoes = "'V'";
            }
            else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'N') {
                $situacoes = "'V'".','."'M'";
            }    
            else if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'S') {
                $situacoes = "'V'".','."'O'";
            }    
            else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'N') {
                $situacoes = "'M'";
            }    
            else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'S') {
                $situacoes = "'M'".','."'O'";
            }    
            else if ($situacao_vendido == 'N' && $situacao_morte == 'N' && $situacao_outra == 'S') {
                $situacoes = "'O'";
            }    
            else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'S') {
               $situacoes = "'V'".','."'M'".','."'O'";
            }    
        }

        if ($situacoes!='') {
            $wsituacao = " AND tbl_animal_situacao IN(";
            $wsituacao.=$situacoes;
            $wsituacao.= ")";
        }

        $raca= array();
        $matriz_itens = explode(",", $raca_filtro);
        $quantidade_itens = count($matriz_itens);

        for($i=0; $i < $quantidade_itens; $i++) {
            $raca[$i]=$matriz_itens[$i];
        }

        $raca = implode(',', $raca);
        $raca = substr($raca,0, -1);

        $wraca = '';

        if ($raca_filtro!='') {
            $wraca = " AND tbl_animal_codigo_raca IN(";
            $wraca.= $raca;
            $wraca.= ")";
        }

        $categoria= array();
        $matriz_itens = explode(",", $categoria_filtro);
        $quantidade_categoria = count($matriz_itens);

        for($i=0; $i < $quantidade_categoria; $i++) {
            $categoria[$i]=$matriz_itens[$i];
        }

        $categoria = implode(',', $categoria);
        $categoria = substr($categoria,0, -1);
        $quantidade_categoria--;

        $wcategoria = '';

        if ($categoria_filtro!='') {
            $wcategoria = explode(",", $categoria);
        }

        $pai= array();
        $matriz_itens = explode(",", $pai_filtro);
        $quantidade_itens = count($matriz_itens);

        for($i=0; $i < $quantidade_itens; $i++) {
            $pai[$i]=$matriz_itens[$i];
        }

        $pai = implode(',', $pai);
        $pai = substr($pai,0, -1);

        $wpai = '';

        if ($pai_filtro!='') {
            $wpai = " AND tbl_animal_codigo_pai IN(";
            $wpai.= $pai;
            $wpai.= ")";
        }

        $mae= array();
        $matriz_itens = explode(",", $mae_filtro);
        $quantidade_itens = count($matriz_itens);

        for($i=0; $i < $quantidade_itens; $i++) {
            $mae[$i]=$matriz_itens[$i];
        }

        $mae = implode(',', $mae);
        $mae = substr($mae,0, -1);

        $wmae = '';

        if ($mae_filtro!='') {
            $wmae = " AND tbl_animal_codigo_mae IN(";
            $wmae.= $mae;
            $wmae.= ")";
        }

        if ($peso_nasc_inicial==0 && $peso_nasc_final==0){
            $wpeso_nasc = '';
        }
        else {
            $wpeso_nasc = " AND tbl_animal_primeiro_peso >= '$peso_nasc_inicial' AND tbl_animal_primeiro_peso <= '$peso_nasc_final'";
        }

        if ($peso_desmama_inicial==0 && $peso_desmama_final==0){
            $wpeso_desmama = '';
        }
        else {
            $wpeso_desmama = " AND tbl_animal_peso_desmama >= '$peso_desmama_inicial' AND tbl_animal_peso_desmama <= '$peso_desmama_final'";
        }

        if ($peso_ult_inicial==0 && $peso_ult_final==0){
            $wpeso_ult = '';
        }
        else {
            $wpeso_ult = " AND tbl_animal_ultimo_peso >= '$peso_ult_inicial' AND tbl_animal_ultimo_peso <= '$peso_ult_final'";
        }

        if ($data_nasc_inicial==0 && $data_nasc_final==0){
            $wdata_nasc = '';
        }
        else {
            $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
        }

        if ($_REQUEST["solteiras"]=='' && $_REQUEST["paridas"]=='') {
            $solteiras='';
            $paridas='';
        }
        else {
            if ($solteiras=='') {
                $solteiras='N';
            }

            if ($paridas=='') {
                $paridas='N';
            }
        }

        if ($data_paridas_ate=='') {
            $data_paridas_ate='9999-99-99';
            $data_paridas_de='0000-00-00';
        }
        else {
            $data_paridas_de = date("Y-m-d", strtotime('-8 month',strtotime($data_paridas_ate)));
            $data_paridas_de = date("Y-m-d", strtotime('-1 day',strtotime($data_paridas_de)));
        }

        if ($previsao_parto_de=='') {
            $previsao_parto_de = '0000-00-00';
            $previsao_parto_ate = '9999-99-99';
        }

        $vaca_solteira = '';
        $vaca_parida = '';
        $vaca_descarte = '';
        $tem_parto = '';
        $tem_aborto = '';
        $tem_previsao_parto = '';
        $ultimo_parto = '0000-00-00';
        $data_previsao_parto = '0000-00-00';
        $tem_positivo = '';
        $tem_negativo = '';
    }
    else {
        $_SESSION['local_pesagem']='';
        $_SESSION['categoria_historico_animais']='';

        //$codigo_alfa_filtro = $_REQUEST['codigo_alfa'];
        $codigo_alfa_numerico = $_REQUEST['codigo_alfa_numerico']; 

        if ($codigo_alfa_numerico!='') {
            $codigo_numerico_consulta = substr($codigo_alfa_numerico, -9);

            if (strlen($codigo_alfa_numerico)!=9){
                $data = explode("-", $codigo_alfa_numerico);
                $codigo_alfa_consulta = $data[0];
            }
            else {
                $codigo_alfa_consulta = '';
            }
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
  <link href="css/style.css?<?php echo Versao;?>" rel="stylesheet">
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
            include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php";
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Histórico de Animais</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="far fa-file-alt"></i> Histórico de Animais</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data">

                            <div class="panel"> 
                            <!--    <div class=panel-body> -->
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="" style="padding-right: 15px; padding-left: 15px;">

                                                <input type="hidden" id="expande_tela" value="S">

                                                <input type="hidden" id="origem_relatorio" <?php echo "value='".$origem_relatorio."'";?>>        

                                                <input type="hidden" id="tipo_rel"
                                                <?php echo "value='".$tipo_rel."'";?>>

                                                <input type="hidden" id="descricao_filtro"
                                                <?php echo "value='".$descricao_filtro."'";?>>

                                                <?php if ($tipo_rel == 'I') : ?>

                                                <input type="hidden" id="codigo_numerico_filtro" 
                                                <?php echo "value='".$codigo_alfa_numerico."'";?>>

                                                <?php else : ?>

                                                <input type="hidden" id="codigo_local"
                                                <?php echo "value='".$local_filtro."'";?>>

                                                <input type="hidden" id="codigo_origem"
                                                <?php echo "value='".$origem_filtro."'";?>>

                                                <input type="hidden" id="codigo_raca"
                                                <?php echo "value='".$raca_filtro."'";?>>

                                                <input type="hidden" id="codigo_categoria"
                                                <?php echo "value='".$categoria_filtro."'";?>>

                                                <input type="hidden" id="codigo_pai"
                                                <?php echo "value='".$pai_filtro."'";?>>

                                                <input type="hidden" id="codigo_mae"
                                                <?php echo "value='".$mae_filtro."'";?>>

                                                <input type="hidden" id="sexo"
                                                <?php echo "value='".$sexo_filtro."'";?>>

                                                <input type="hidden" id="peso_nasc_inicial"
                                                <?php echo "value='".$peso_nasc_inicial."'";?>>

                                                <input type="hidden" id="peso_nasc_final"
                                                <?php echo "value='".$peso_nasc_final."'";?>>

                                                <input type="hidden" id="peso_desmama_inicial"
                                                <?php echo "value='".$peso_desmama_inicial."'";?>>

                                                <input type="hidden" id="peso_desmama_final"
                                                <?php echo "value='".$peso_desmama_final."'";?>>

                                                <input type="hidden" id="peso_ult_inicial"
                                                <?php echo "value='".$peso_ult_inicial."'";?>>

                                                <input type="hidden" id="peso_ult_final"
                                                <?php echo "value='".$peso_ult_final."'";?>>

                                                <input type="hidden" id="data_nasc_inicial"
                                                <?php echo "value='".$data_nasc_inicial."'";?>>

                                                <input type="hidden" id="data_nasc_final"
                                                <?php echo "value='".$data_nasc_final."'";?>>

                                                <input type="hidden" id="ativo"
                                                <?php echo "value='".$ativo_filtro."'";?>>

                                                <input type="hidden" id="situacao_vendido"
                                                    <?php echo "value='".$situacao_vendido."'";?>>

                                                <input type="hidden" id="situacao_morte"
                                                    <?php echo "value='".$situacao_morte."'";?>>

                                                <input type="hidden" id="situacao_outra"
                                                    <?php echo "value='".$situacao_outra."'";?>>

                                                <input type="hidden" id="solteiras"
                                                <?php echo "value='".$solteiras."'";?>>

                                                <input type="hidden" id="descarte"
                                                <?php echo "value='".$descarte."'";?>>

                                                <input type="hidden" id="paridas"
                                                <?php echo "value='".$paridas."'";?>>

                                                <input type="hidden" id="data_paridas_ate"
                                                <?php echo "value='".$_REQUEST["data_paridas"]."'";?>>

                                                <input type="hidden" id="parto"
                                                <?php echo "value='".$parto."'";?>>

                                                <input type="hidden" id="num_parto_de"
                                                <?php echo "value='".$num_parto_de."'";?>>

                                                <input type="hidden" id="num_parto_ate"
                                                <?php echo "value='".$num_parto_ate."'";?>>

                                                <input type="hidden" id="aborto"
                                                <?php echo "value='".$aborto."'";?>>

                                                <input type="hidden" id="num_aborto_de"
                                                <?php echo "value='".$num_aborto_de."'";?>>

                                                <input type="hidden" id="num_aborto_ate"
                                                <?php echo "value='".$num_aborto_ate."'";?>>

                                                <input type="hidden" id="previsao_parto_de"
                                                <?php echo "value='".$_REQUEST['previsao_parto_de']."'";?>>

                                                <input type="hidden" id="previsao_parto_ate"
                                                <?php echo "value='".$_REQUEST['previsao_parto_ate']."'";?>>

                                                <input type="hidden" id="positivo"
                                                <?php echo "value='".$positivo."'";?>>
                                                
                                                <input type="hidden" id="negativo"
                                                <?php echo "value='".$negativo."'";?>>

                                                <input type="hidden" id="codigo_estacao_filtro"
                                                <?php echo "value='".$estacao_filtro."'";?>>

                                                <?php endif; ?>

<?php
    if ($tipo_rel=='I') {
        $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
        INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_animal_codigo_fazenda
            WHERE tbl_animal_codigo_alfa = '$codigo_alfa_consulta' AND 
                  tbl_animal_codigo_numerico = '$codigo_numerico_consulta'");

        $num_rows_animais = mysqli_num_rows($tbl_animais);

        if ($num_rows_animais==0){
            echo '
            <script type="text/javascript">
                $("#aguardar").modal("hide");
            </script>
            ';

            mysqli_close($conector);

            echo '
                <div class="row">
                <div class="col-md-9" style="margin-bottom: 10px; margin-top: 10px;">
                <label class="label_consulta_rel_rel">Filtros:</label>
                <span>'.$descricao_filtro.' Registro não encontrado</span>
                </div>

                <div class="form-group col-md-3" style="padding-top: 10px;">  
                <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                </button>
                </div>
                </div>';
        }   
        else {
            $reg_animal = mysqli_fetch_object($tbl_animais);
            $codigo = $reg_animal->tbl_animal_codigo_id;
            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
            $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
            $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
            $ativo = $reg_animal->tbl_animal_ativo;
            $animal_situacao = $reg_animal->tbl_animal_situacao;
            $descarte = $reg_animal->tbl_animal_descarte_reproducao;
            $descarte_em = new DateTime($reg_animal->tbl_animal_descarte_em);
            $descarte_em_edi = $descarte_em->format('d/m/Y H:i:s');
            $descarte_por = 'Por ' . $reg_animal->tbl_animal_descarte_por .' em '. $descarte_em_edi;
            $nome_pessoa = $reg_animal->tbl_pessoa_nome; 
            $pai = $reg_animal->tbl_animal_codigo_pai;
            $mae =  $reg_animal->tbl_animal_codigo_mae;
            $codigo_origem = $reg_animal->tbl_animal_codigo_origem;
            $peso_desmama = $reg_animal->tbl_animal_peso_desmama;
            $peso_nasc = $reg_animal->tbl_animal_primeiro_peso;
            $ultimo_peso = $reg_animal->tbl_animal_ultimo_peso;

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_semem_nome;
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . ltrim($reg->tbl_animal_codigo_numerico, "0");
                }
                else {
                    $descricao_pai = '';
                }
            }

            $tab_mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$mae'");
            $num_rows = mysqli_num_rows($tab_mae);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_mae);
                if ($reg->tbl_animal_codigo_alfa==''){
                    $descricao_mae = intval($reg->tbl_animal_codigo_numerico);
                }
                else {
                    $descricao_mae = $reg->tbl_animal_codigo_alfa.'-'. intval($reg->tbl_animal_codigo_numerico);
                }
            }
            else {
                $descricao_mae = '';
            }
            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
            $data_baixa = $reg_animal->tbl_animal_baixado_em;

            if ($data_baixa!='') {
                $data_acompanhamento_calculo = date($data_baixa);
            }
            else {
                $data_acompanhamento_calculo = date("Y-m-d");
            }

            $date = new DateTime($data_nascimento);

            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');

            $idade_ano = $idade_acompanhamento->format('%Y');
            $idade_mes = $idade_acompanhamento->format('%m');
            $idade_animal = $idade_acompanhamento_mostra_anos+
                            $idade_acompanhamento_mostra_meses;

            if ($idade_ano==0 && $idade_mes!=0) {
                $desc_idade = $idade_mes . ' mes(es)';
            }
            else if ($idade_ano!=0 && $idade_mes==0){
                $desc_idade = $idade_ano . ' ano(s)';
            }
            else if ($idade_ano!=0 && $idade_mes!=0) {
                $desc_idade = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
            }
            else {
                $desc_idade = '';
            }

            $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
            $data_nascimento_edi = $data->format('d/m/Y');

            if ($reg_animal->tbl_animal_sexo=='M') {
                $sexo = 'Macho';
            }
            else {
                $sexo = 'Femea';
            }

            if ($codigo_alfa=='') {
                $codigo_edi = intval($codigo_numerico);
            }
            else {
                $codigo_edi = $codigo_alfa.'-'.intval($codigo_numerico);
            }

            if ($ativo=='N') {
                $ativo = 'Não';
            }
            else {
                $ativo = 'Sim';
            }

            switch ($animal_situacao) {
            case 'T':
                $animal_situacao='Aguardando Transferência';
                break;
            case 'V':
                $animal_situacao='Vendido';
                break;
            case 'M':   
                $animal_situacao='Morte';
                break;
            case 'S':   
                $animal_situacao='Outra Saída';
                break;
            } 

            $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                    WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($categoria);    

            if ($num_rows!=0) {
                while ($reg_categoria = mysqli_fetch_object($categoria)) {
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade_animal >= $idade_de && 
                        $idade_animal <= $idade_ate) {
                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                    }
                }
            }                   

            switch ($codigo_categoria) {
                case '001':
                    $desc_categoria= '00 a 07 meses';
                    break;
                case '002':
                    $desc_categoria= '08 a 12 meses';
                    break;
                case '003':
                    $desc_categoria= '13 a 24 meses';
                    break;
            case '004':
                    $desc_categoria= '25 a 36 meses';
                    break;
                case '005':
                    $desc_categoria= '> 36 meses';
                    break;
            }     

            $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_origem'");
                
            $num_rows = mysqli_num_rows($tab_fazenda);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_fazenda);
                $desc_origem = $reg->tbl_pessoa_nome;
            }
            else {
                $desc_origem = '';
            }

            $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
                
            $num_rows = mysqli_num_rows($tab_raca);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_raca);
                $descricao_raca = $reg->tab_descricao_raca;
            }
            else {
                $descricao_raca = '';
            }

            $tab_pelagem = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem ='$codigo_pelagem'");
                
            $num_rows = mysqli_num_rows($tab_pelagem);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pelagem);
                $descricao_pelagem = $reg->tab_descricao_pelagem;
            }
            else {
                $descricao_pelagem = '';
            }

            echo '
                <div class="row">
                <div class="col-md-9" style="padding-top: 10px; margin-bottom: 10px; font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Nº Animal:&nbsp;</label>
                    <span>'.$codigo_edi . ' - ' . $sexo .'
                    </span>
                </div>

                <div class="form-group col-md-3" style="padding-top: 10px;">  
                <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                </button>

                <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" onClick="lista_historico_animais_excel()">Excel
                </button>
                </div>
                </div>';

            echo '<div class="row">
                  <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Nascimento:&nbsp;</label>
                    <span>'.$data_nascimento_edi.'</span>
                </div>

                  <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Idade:&nbsp;</label>
                    <span>'.$desc_idade.'</span>
                </div>
                </div>';

            echo '<div class="row">
                <div class="col-md-8" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">&nbsp;Categoria:&nbsp;</label>
                    <span>'.$desc_categoria.'</span>
                </div>
            </div>';

            echo '<div class="row">
                  <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Raça:&nbsp;</label>
                    <span>'.$descricao_raca.'</span>
                </div>

                  <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Pelagem:&nbsp;</label>
                    <span>'.$descricao_pelagem.'</span>
                </div>
                </div>';

            echo '<div class="row">
                <div class="col-md-9" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">&nbsp;Fazenda:&nbsp;</label>
                    <span>'.$nome_pessoa.'</span>
                </div>
            </div>';

            echo '<div class="row">
                <div class="col-md-9" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">&nbsp;Origem:&nbsp;</label>
                    <span>'.$desc_origem.'</span>
                </div>
            </div>';

            echo '<div class="row">
                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Animal Ativo:&nbsp;</label>';
                    
                if ($ativo == "Sim") {
                    echo '<span style="color: green;">'.$ativo.'</span>';
                }
                else {
                    echo '<span style="color: red;">'.$ativo.'</span>';
                }   

                echo '</div>';

                if ($ativo == "Não") {
                    echo '<div class="col-md-2"style="font-size: 14px;">
                        <label class="label_situacao_reprodutiva_rel">Situação:&nbsp;</label>
                        <span style="color: red;">'.$animal_situacao.'</span>
                    </div>';
                }
            echo '</div>';

            echo '<div class="row">
                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Pai:&nbsp;</label>
                    <span>'.$descricao_pai.'</span>
                </div>
            </div>';

            echo '<div class="row">
                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Mãe:&nbsp;</label>
                    <span>'.$descricao_mae.'</span>
                </div>
            </div>';    

            echo '<div class="row">';

            if ($descarte == 'S') {
            echo '<div class="col-md-8" style="font-size: 14px;">
                <label class="label_situacao_reprodutiva_rel">Descartado para reprodução:&nbsp;</label>
                <span style="color: red">'.$descarte_por.'</span>
            </div>';
            echo '</div> <hr>';
            }

            echo '<div class="row">
                <div class="col-md-1"></div>

                <div class="col-md-3" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Histórico das Pasagens</label>
                </div>

                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Peso Nascimento:&nbsp;</label>
                    <span>'.$peso_nasc.'</span>
                </div>

                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Peso Desmama:&nbsp;</label>
                    <span>'.$peso_desmama.'</span>
                </div>

                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Último Peso:&nbsp;</label>
                    <span>'.$ultimo_peso.'</span>
                </div>
                </div>

                <div class="row">
                    <div class="col-md-1"></div>

                    <div class="col-md-10">
                        <table class="table table-striped table-advance table-hover" id="tabela_estacoes" style="font-size: 12px;" width="100%">
                                                 
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Motivo da Pesagem</th>
                                <th>Fazenda</th>
                                <th>Peso</th>
                            </tr>
                        </thead>';  

            echo '<tbody>';

            $tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem 
                INNER JOIN tbl_pesagem
                        ON tbl_pesagem_id = tbl_ite_pesagem_numero_id   
                WHERE tbl_ite_pesagem_codigo_id_animal='$codigo' and 
                      tbl_ite_pesagem_peso!=0 and tbl_pesagem_finalizada='S'
                ORDER BY tbl_ite_pesagem_data_emissao DESC, tbl_pesagem_id DESC"); 

            $num_rows_pesagem = mysqli_num_rows($tbl_pesagem);

            if ($num_rows_pesagem!=0) {
                while ($reg_ite_peso = mysqli_fetch_object($tbl_pesagem)){
                    $data = new DateTime($reg_ite_peso->tbl_ite_pesagem_data_emissao); 
                    $data_edi = $data->format('d/m/Y');
                    $epoca = $reg_ite_peso->tbl_pesagem_codigo_epoca; 
                    $origem = $reg_ite_peso->tbl_pesagem_codigo_local; 
                    $peso = $reg_ite_peso->tbl_ite_pesagem_peso; 

                    $tab_origem = mysqli_query($conector, "SELECT * FROM tbl_pessoa WHERE tbl_pessoa_id='$origem'");
                    $num_rows = mysqli_num_rows($tab_origem);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_origem);
                        $desc_origem = $reg->tbl_pessoa_nome;
                    }
                    else {
                        $desc_origem = '';
                    }

                    $tab_epoca = mysqli_query($conector, "SELECT * FROM tabela_epoca_pesagem WHERE tab_codigo_epoca_pesagem ='$epoca'");
                    $num_rows = mysqli_num_rows($tab_epoca);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_epoca);
                        $desc_epoca = $reg->tab_descricao_epoca_pesagem;
                    }
                    else {
                        $desc_epoca= '';
                    }

                    echo "<tr>";
                    echo "<td width='8%'>".$data_edi."</td>";
                    echo "<td width='25%'>".$desc_epoca."</td>";
                    echo "<td width='25%'>".$desc_origem."</td>";
                    echo "<td width='25%'>".$peso." Kg</td>";
                    //echo "<td width='17%'></td>";
                    echo "</tr>";
                } 
            }
            echo '</tbody></table></div></div>';    
        }   
    }
    else {
        /*$sql = "SELECT * from tbl_animais 
            WHERE tbl_animal_lixeira=0" .
                  $wativo . $wlocal . $worigem . $wsexo . 
                  $wraca . $wpai . $wmae . $wpeso_nasc . $wpeso_desmama . 
                  $wpeso_ult . $wdata_nasc .
            " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC"; 
        */
        if ($situacao_vendido == 'S') {
            $sql = "SELECT * from tbl_animais 
                WHERE 
                tbl_animal_lixeira=0" .
                $wativo . 
                $wsituacao .
                $wlocal_anterior . 
                $wsexo . 
                $wraca . 
                $wpai . 
                $wmae . 
                $wpeso_nasc . 
                $wpeso_desmama . 
                $wpeso_ult . 
                $wdata_nasc .
                " ORDER BY 
                tbl_animal_codigo_fazenda, 
                tbl_animal_codigo_numerico ASC"; 
        }
        else {
            $sql = "SELECT * from tbl_animais 
                WHERE 
                tbl_animal_lixeira=0" .
                $wativo . 
                $wsituacao .
                $wlocal . 
                $wsexo . 
                $wraca . 
                $wpai . 
                $wmae . 
                $wpeso_nasc . 
                $wpeso_desmama . 
                $wpeso_ult . 
                $wdata_nasc .
                " ORDER BY 
                tbl_animal_codigo_fazenda, 
                tbl_animal_codigo_numerico ASC"; 
        }

        $tbl_animais = mysqli_query($conector, $sql);
        $num_rows_animais = mysqli_num_rows($tbl_animais);

        if ($num_rows_animais==0){
            echo '
            <script type="text/javascript">
                $("#aguardar").modal("hide");
            </script>
            ';
        }   
        else {
            echo '
                <div class="row">
                <div class="col-md-9" style="margin-bottom: 10px; margin-top: 10px;">
                <label class="label_consulta_rel_rel">Filtros:</label>
                <span>'.$descricao_filtro.'</span>
                </div>

                <div class="col-md-3" style="padding-top: 10px;">  
                <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                </button>

                <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" onClick="lista_historico_animais_excel()">Excel
                </button>
                </div>
                </div>';

            echo '<table class="table table-striped table-advance table-hover" id="tabela_itens_consulta" width="100%" style="font-size: 10px;">';

            echo '<tbody>';

            $total_peso_nasc = 0;
            $qtd_peso_nasc = 0;
            $total_peso_ultimo = 0;
            $qtd_peso_ultimo = 0;
            $animais_listados = 0;
            $total_peso_desmama = 0;
            $qtd_peso_desmama = 0;

            while ($reg_animal = mysqli_fetch_object($tbl_animais)){
                $codigo = $reg_animal->tbl_animal_codigo_id;
                $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
                $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
                $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
                $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;

                if ($reg_animal->tbl_animal_sexo=='M') {
                    $sexo = 'Macho';
                }
                else {
                    $sexo = 'Femea';
                }

                $mae = $reg_animal->tbl_animal_codigo_mae; 
                $pai = $reg_animal->tbl_animal_codigo_pai; 
                $ativo = $reg_animal->tbl_animal_ativo; 
                $situacao = $reg_animal->tbl_animal_situacao; 
                $animal_descarte = $reg_animal->tbl_animal_descarte_reproducao;

                if ($animal_descarte=='S') {
                    $animal_descarte = 'Sim';
                }
                else {
                    $animal_descarte = '';   
                }

                $tem_negativo = '';
                $tem_positivo = '';
                $vaca_descarte = '';

                if ($descarte=='S') {
                    if ($animal_descarte=='Sim') {
                        $vaca_descarte = 'S';
                    }
                }

                $data_nasc = new DateTime($reg_animal->tbl_animal_data_nascimento);
                $data_nasc_edi = $data_nasc->format('d/m/Y');
                $peso_nasc = $reg_animal->tbl_animal_primeiro_peso; 
                $peso_nasc_edi = number_format($peso_nasc,2,',','.');
                $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
                $peso_desmama_edi = number_format($peso_desmama,2,',','.');
                $peso_ultimo = $reg_animal->tbl_animal_ultimo_peso; 
                $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
                $data_ultimo = new DateTime($reg_animal->tbl_animal_data_ultimo);
                $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                $observacao = ltrim($reg_animal->tbl_animal_observacao); 
                $observacao = rtrim($observacao); 

                if ($codigo_alfa=='') {
                    $codigo_edi = intval($codigo_numerico);
                }
                else {
                    $codigo_edi = $codigo_alfa.'-'.intval($codigo_numerico);
                }

                $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_fazenda'");
                
                $num_rows = mysqli_num_rows($tab_fazenda);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_fazenda);
                    $desc_local = $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_local = '';
                }

                $tab_mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$mae'");
                
                $num_rows = mysqli_num_rows($tab_mae);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_mae);
                    if ($reg->tbl_animal_codigo_alfa=='') {
                        $descricao_mae = intval($reg->tbl_animal_codigo_numerico);
                    }
                    else {
                        $descricao_mae = $reg->tbl_animal_codigo_alfa. '-' . intval($reg->tbl_animal_codigo_numerico);
                    }
                }
                else {
                    $descricao_mae = '';
                }

                $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
                
                $num_rows = mysqli_num_rows($tab_pai);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_semem_nome;
                    $pai = $reg->tbl_semem_codigo_id;
                }
                else {
                    $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                    }
                    else {
                        $descricao_pai = '';
                    }
                }

                $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
                
                $num_rows = mysqli_num_rows($tab_raca);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_raca);
                    $descricao_raca = $reg->tab_descricao_raca;
                }
                else {
                    $descricao_raca = '';
                }

                $tab_pelagem = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem ='$codigo_pelagem'");
                
                $num_rows = mysqli_num_rows($tab_pelagem);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_pelagem);
                    $descricao_pelagem = $reg->tab_descricao_pelagem;
                }
                else {
                    $descricao_pelagem = '';
                }

                $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
                $data_baixa = $reg_animal->tbl_animal_baixado_em;

                if ($data_baixa!='') {
                    $data_acompanhamento_calculo = date($data_baixa);
                }
                else {
                    $data_acompanhamento_calculo = date("Y-m-d");
                }

                $date = new DateTime($data_nascimento); 
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade_animal = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                    WHERE tab_registro_lixeira_categoria_idade='0'");

                $num_rows = mysqli_num_rows($categoria);    

                if ($num_rows!=0) {
                    while ($reg_categoria = mysqli_fetch_object($categoria)) {
                        $idade_de = $reg_categoria->tab_categoria_idade_de;
                        $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                        if ($idade_animal >= $idade_de && 
                            $idade_animal <= $idade_ate) {
                            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                        }
                    }
                }                   

                switch ($codigo_categoria) {
                    case '001':
                        $desc_categoria= '00 a 07';
                        break;
                    case '002':
                        $desc_categoria= '08 a 12';
                        break;
                    case '003':
                        $desc_categoria= '13 a 24';
                        break;
                    case '004':
                        $desc_categoria= '25 a 36';
                        break;
                    case '005':
                        $desc_categoria= '> 36';
                        break;
                }     

                // verifica a cobertura do animal
                $sql = mysqli_query($conector, "SELECT * FROM
                    tbl_item_cobertura
                    INNER JOIN tbl_cobertura
                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                    INNER JOIN tbl_parametro_estacao_monta
                        ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
                    WHERE tbl_cobertura_lixeira=0 AND 
                          tbl_ite_cobertura_codigo_id_animal='$codigo'".
                          $westacao."
                    ORDER BY tbl_ite_cobertura_numero_id DESC limit 1");

                $num_rows = mysqli_num_rows($sql);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($sql);
                    $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
                    $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
                    $estacao_monta = $reg_cobertura->tbl_par_estacao_nome;
                }
                else {
                    $codigo_local = 0;
                    $estacao_animal = 0;
                    $estacao_monta = '';
                }

                // verifica vacas solteiras
                if ($solteiras=='S' || $paridas=='S') {
                    $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                        WHERE tbl_animal_codigo_mae='$codigo'
                        ORDER BY tbl_animal_data_nascimento DESC limit 1");

                    $ultimo_filho = mysqli_num_rows($tbl_filhos);

                    if ($ultimo_filho!=0) {
                        $reg_filhos = mysqli_fetch_object($tbl_filhos);
                        $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento; 

                        $data_acompanhamento_calculo = date("Y-m-d");
                        $date = new DateTime($ultimo_parto); // Data de Nascimento
                        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade_ano = $idade_acompanhamento->format('%Y');
                        $idade_mes = $idade_acompanhamento->format('%m');
                        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                        if ($idade < 8) {
                            $vaca_parida = 'S';
                            $vaca_solteira = '';
                        }
                        else {
                            $vaca_solteira = 'S';
                            $vaca_parida = '';
                        }
                    }
                    else {
                        $ultimo_parto = '0000-00-00';
                    }
                }

                // verifica partos
                if ($sexo == 'Femea' && $num_parto_de!='' && $num_parto_ate!='') {
                    $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                        WHERE tbl_animal_codigo_mae='$codigo'");

                    $num_partos = mysqli_num_rows($tbl_filhos);

                    if ($num_partos>=$num_parto_de && 
                        $num_partos<=$num_parto_ate && $idade_animal>=8) {
                        $tem_parto = "S";
                    }
                    else {
                        $tem_parto = "";
                    }
                }

                // verifica abortos
                if ($sexo == 'Femea' && $num_aborto_de!='' && $num_aborto_ate!='') {
                    $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                        WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                              tbl_mov_estoque_codigo_id_animal=999999999 AND
                              (tbl_mov_estoque_entrada_saida='A' OR 
                               tbl_mov_estoque_entrada_saida='S') AND 
                              (tbl_mov_estoque_tipo_movimentacao='M' OR
                               tbl_mov_estoque_tipo_movimentacao='A' OR
                               tbl_mov_estoque_tipo_movimentacao='B')");

                    $num_natimorto = mysqli_num_rows($tbl_natimorto);

                    if ($num_natimorto>=$num_aborto_de && 
                        $num_natimorto<=$num_aborto_ate) {
                        $tem_aborto = "S";
                    }
                    else {
                        $tem_aborto = "";
                    }
                }       

                // Verifica previsão de parto
                if ($previsao_parto_de!='' && $previsao_parto_ate!='') {
                    $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                        INNER JOIN tbl_cobertura
                                ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                        WHERE tbl_cobertura_lixeira=0 AND 
                              tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                              tbl_cobertura_controle = 'C' AND 
                              tbl_ite_cobertura_resultado_diagnostico = 'P'
                        ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                    $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

                    if ($num_rows_coberturas!=0) {
                        $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                        $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                        $cobertura_id = $reg_cobertura->tbl_cobertura_id;
                        $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;

                        $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                                WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");

                        $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                        $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
                            WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                                  tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                            ORDER BY tbl_ite_protocoloiatf_id ASC");

                        $dias_previsao_parto = 282;

                        while($reg_itens_iatf = mysqli_fetch_object($sql)){
                            $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);

                            $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

                            $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                        }
                    }
                    else {
                        $data_previsao_parto = '0000-00-00';
                    }
                }

                // verifica natimortos, nascidos ou abortos na estacao
                    $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                        INNER JOIN tbl_cobertura
                                ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                        WHERE tbl_cobertura_lixeira=0 AND
                              tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                              tbl_cobertura_controle = 'C' AND 
                              tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                              tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
                        ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                $num_rows_item = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows_item!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $nascido_aborto = $reg_cobertura->tbl_ite_cobertura_nascido;
                }
                else {
                    $nascido_aborto = '';
                }
                        
                // Verifica diagnostico
                if ($positivo=='S' || $negativo=='S'){
                    $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                        INNER JOIN tbl_cobertura
                                ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                        WHERE tbl_cobertura_lixeira=0 AND 
                              tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                              tbl_cobertura_controle = 'C' AND 
                              tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
                        ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

                    $num_rows = mysqli_num_rows($tbl_item_cobertura);

                    if ($num_rows!=0) {
                        $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                        $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

                        if ($diagnostico=='P'){
                            $tem_positivo = 'S';
                            $tem_negativo = '';
                        } 
                        else if ($diagnostico=='N') {
                            $tem_negativo = 'S';
                            $tem_positivo = '';
                        }
                        else {
                            $tem_negativo = '';
                            $tem_positivo = '';
                        }
                    }
                    else {
                        $tem_negativo = '';
                        $tem_positivo = '';
                    }
                }
                else {
                    $tem_negativo = '';
                    $tem_positivo = '';
                }

                if ($positivo=='S' AND 
                    $nascido_aborto!='') {
                    $tem_positivo='';
                }

                if ($data_previsao_parto!='0000-00-00' AND 
                    $nascido_aborto!='') {
                    $data_previsao_parto='0000-00-00';
                }

                if ($wcategoria=="" && 
                    $descarte==$vaca_descarte && 
                    $data_previsao_parto>=$previsao_parto_de && 
                    $data_previsao_parto<=$previsao_parto_ate && 
                    (($solteiras==$vaca_solteira && ($data_previsao_parto=='0000-00-00'
                     || ($nascido=='N' || $nascido=='A' || 
                    $nascido=='M' || $nascido=='O'))) || 
                    ($paridas==$vaca_parida && 
                    $ultimo_parto>=$data_paridas_de && 
                    $ultimo_parto<=$data_paridas_ate)) &&
                    $parto==$tem_parto &&
                    $aborto==$tem_aborto && 
                    $positivo==$tem_positivo && 
                    $negativo==$tem_negativo) {

                    // AJUSTE DO PESO DE DESMAMA
                    if ($peso_desmama!='' && $peso_desmama!=0) {
                        if ($peso_nasc=='' || $peso_nasc==0) {
                            $peso_nasc = 30;
                            $peso_nasc_edi = number_format($peso_nasc,2,',','.');
                        }
                        $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                        $data_final = $reg_animal->tbl_animal_data_desmama;
                        $diferenca = strtotime($data_final) - 
                                     strtotime($data_inicial);
                        $dias = floor($diferenca / (60 * 60 * 24));

                        $diferenca_peso = $peso_desmama - $peso_nasc;
                        $gmd = $diferenca_peso/$dias;

                        $peso_desmama = $peso_nasc + ($gmd * 205);
                        $peso_desmama_edi = number_format($peso_desmama,2,',','.');
                    }
                    // FIM AJUSTE DO PESO DE DESMAMA

                    if ($peso_nasc!='' && $peso_nasc!=0){
                        $total_peso_nasc+= $peso_nasc;
                        $qtd_peso_nasc++;
                    }

                    if ($peso_desmama!='' && $peso_desmama!=0){
                        $total_peso_desmama+= $peso_desmama;
                        $qtd_peso_desmama++;
                    }

                    // ULTIMO PESO
                    if ($peso_ultimo=='' || $peso_ultimo==0){
                        if ($peso_desmama!='' && $peso_desmama!=0) {
                            $peso_ultimo = $peso_desmama;
                            $data_ultimo = new DateTime($reg_animal->tbl_animal_data_desmama);
                            $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
                            $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                            $total_peso_ultimo+= $peso_ultimo;
                            $qtd_peso_ultimo++;
                        }
                        else if ($peso_nasc!='' && $peso_nasc!=0){
                            $peso_ultimo = $peso_nasc;
                            $data_ultimo = new DateTime($reg_animal->tbl_animal_data_primeiro_peso);
                            $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
                            $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                            $total_peso_ultimo+= $peso_ultimo;
                            $qtd_peso_ultimo++;
                        }
                        else {
                            $data_ultimo_edi = '';
                        }
                    } 
                    else {
                        $total_peso_ultimo+= $peso_ultimo;
                        $qtd_peso_ultimo++;
                    }
                    // FIM ULTIMO PESO

                    if ($ativo=='N') {
                        if ($situacao=='V') {
                            echo '<tr style="color: blue;">';
                            echo '<td width="7%">'.$codigo_edi.'</td>';    
                            echo '<td width="10%">'.$desc_local.'</td>';    
                            echo '<td width="3%" style="text-align: center;">'.$sexo.'</td>';   
                            echo '<td width="6%" style="text-align: center;">'.$data_nasc_edi.'</td>';
                            echo '<td width="6%" style="text-align: center;">'.$idade_animal.'</td>';
                            echo '<td width="6%" >'.$desc_categoria.'</td>';
                            echo '<td width="7%">'.$descricao_raca.'</td>';    
                            echo '<td width="7%">'.$descricao_pelagem.'</td>';    
                            echo '<td width="7%">'.$descricao_mae.'</td>';    
                            echo '<td width="7%">'.$descricao_pai.'</td>';    
                            echo '<td width="11%" style="white-space: normal;">'.$observacao.'</td>';    
                            echo '<td width="6%" style="text-align: right;">'.$peso_nasc_edi.'</td>'; 
                            echo '<td width="6%" style="text-align: right;">'.$peso_desmama_edi.'</td>';    
                            echo '<td width="6%" style="text-align: right;">'.$peso_ultimo_edi.'</td>';    
                            echo '<td width="6%" style="text-align: center;">'.$data_ultimo_edi.'</td>';    
                            echo '<td width="3%" style="text-align: center; color: red;">'.$animal_descarte.'</td>';    
                            echo '</tr>';
                        }
                        else {
                            echo '<tr style="color: red;">';
                            echo '<td width="7%">'.$codigo_edi.'</td>';    
                            echo '<td width="10%">'.$desc_local.'</td>';    
                            echo '<td width="3%" style="text-align: center;">'.$sexo.'</td>';   
                            echo '<td width="6%" style="text-align: center;">'.$data_nasc_edi.'</td>';
                            echo '<td width="6%" style="text-align: center;">'.$idade_animal.'</td>';
                            echo '<td width="6%" >'.$desc_categoria.'</td>';
                            echo '<td width="7%">'.$descricao_raca.'</td>';    
                            echo '<td width="7%">'.$descricao_pelagem.'</td>';    
                            echo '<td width="7%">'.$descricao_mae.'</td>';    
                            echo '<td width="7%">'.$descricao_pai.'</td>';    
                            echo '<td width="11%" style="white-space: normal;">'.$observacao.'</td>';    
                            echo '<td width="6%" style="text-align: right;">'.$peso_nasc_edi.'</td>'; 
                            echo '<td width="6%" style="text-align: right;">'.$peso_desmama_edi.'</td>';    
                            echo '<td width="6%" style="text-align: right;">'.$peso_ultimo_edi.'</td>';    
                            echo '<td width="6%" style="text-align: center;">'.$data_ultimo_edi.'</td>';    
                            echo '<td width="3%" style="text-align: center; color: red;">'.$animal_descarte.'</td>';    
                            echo '</tr>';
                        }
                    }
                    else {
                        echo '<tr>';
                        echo '<td width="7%">'.$codigo_edi.'</td>';    
                        echo '<td width="10%">'.$desc_local.'</td>';    
                        echo '<td width="3%" style="text-align: center;">'.$sexo.'</td>';   
                        echo '<td width="6%" style="text-align: center;">'.$data_nasc_edi.'</td>';
                        echo '<td width="6%" style="text-align: center;">'.$idade_animal.'</td>';
                        echo '<td width="6%" >'.$desc_categoria.'</td>';
                        echo '<td width="7%">'.$descricao_raca.'</td>';    
                        echo '<td width="7%">'.$descricao_pelagem.'</td>';    
                        echo '<td width="7%">'.$descricao_mae.'</td>';    
                        echo '<td width="7%">'.$descricao_pai.'</td>';    
                        echo '<td width="11%" style="white-space: normal;">'.$observacao.'</td>';    
                        echo '<td width="6%" style="text-align: right;">'.$peso_nasc_edi.'</td>'; 
                        echo '<td width="6%" style="text-align: right;">'.$peso_desmama_edi.'</td>';    
                        echo '<td width="6%" style="text-align: right;">'.$peso_ultimo_edi.'</td>';    
                        echo '<td width="6%" style="text-align: center;">'.$data_ultimo_edi.'</td>';    
                        echo '<td width="3%" style="text-align: center; color: red;">'.$animal_descarte.'</td>';    
                        echo '</tr>';
                    }
                    $animais_listados++;
                }
                else {
                    for ($k=0; $k < $quantidade_categoria; $k++) { 
                        $value = $wcategoria[$k];
                        if ($value==$codigo_categoria &&
                            $descarte==$vaca_descarte && 
                            $data_previsao_parto>=$previsao_parto_de && 
                            $data_previsao_parto<=$previsao_parto_ate && 
                            (($solteiras==$vaca_solteira && ($data_previsao_parto=='0000-00-00' || ($nascido=='N' || $nascido=='A' || 
                                            $nascido=='M' || $nascido=='O'))) || 
                                        ($paridas==$vaca_parida && 
                            $ultimo_parto>=$data_paridas_de && 
                            $ultimo_parto<=$data_paridas_ate)) &&
                            $parto==$tem_parto &&
                            $aborto==$tem_aborto && 
                            $positivo==$tem_positivo && 
                            $negativo==$tem_negativo
                            ) {

                            // AJUSTE DO PESO DE DESMAMA
                            if ($peso_desmama!='' && $peso_desmama!=0) {
                                if ($peso_nasc=='' || $peso_nasc==0) {
                                    $peso_nasc = 30;
                                    $peso_nasc_edi = number_format($peso_nasc,2,',','.');
                                }

                                $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                                $data_final = $reg_animal->tbl_animal_data_desmama;
                                $diferenca = strtotime($data_final) - 
                                             strtotime($data_inicial);
                                $dias = floor($diferenca / (60 * 60 * 24));

                                $diferenca_peso = $peso_desmama - $peso_nasc;
                                $gmd = $diferenca_peso/$dias;

                                $peso_desmama = $peso_nasc + ($gmd * 205);
                                $peso_desmama_edi = number_format($peso_desmama,2,',','.');
                            }
                            // FIM AJUSTE DO PESO DE DESMAMA

                            if ($peso_nasc!='' && $peso_nasc!=0){
                                $total_peso_nasc+= $peso_nasc;
                                $qtd_peso_nasc++;
                            }

                            if ($peso_desmama!='' && $peso_desmama!=0){
                                $total_peso_desmama+= $peso_desmama;
                                $qtd_peso_desmama++;
                            }

                            // ULTIMO PESO
                            if ($peso_ultimo=='' || $peso_ultimo==0){
                                if ($peso_desmama!='' && $peso_desmama!=0) {
                                    $peso_ultimo = $peso_desmama;
                                    $data_ultimo = new DateTime($reg_animal->tbl_animal_data_desmama);
                                    $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
                                    $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                                    $total_peso_ultimo+= $peso_ultimo;
                                    $qtd_peso_ultimo++;
                                }
                                else if ($peso_nasc!='' && $peso_nasc!=0){
                                    $peso_ultimo = $peso_nasc;
                                    $data_ultimo = new DateTime($reg_animal->tbl_animal_data_primeiro_peso);
                                    $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
                                    $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                                    $total_peso_ultimo+= $peso_ultimo;
                                    $qtd_peso_ultimo++;
                                }
                                else {
                                    $data_ultimo_edi = '';
                                }
                            } 
                            else {
                                $total_peso_ultimo+= $peso_ultimo;
                                $qtd_peso_ultimo++;
                            }
                            // FIM ULTIMO PESO

                            if ($ativo=='N') {
                                if ($situacao=='V') {
                                    echo '<tr style="color: blue;">';
                                    echo '<td width="7%">'.$codigo_edi.'</td>';    
                                    echo '<td width="10%">'.$desc_local.'</td>';    
                                    echo '<td width="3%" style="text-align: center;">'.$sexo.'</td>';   
                                    echo '<td width="6%" style="text-align: center;">'.$data_nasc_edi.'</td>';
                                    echo '<td width="6%" style="text-align: center;">'.$idade_animal.'</td>';
                                    echo '<td width="6%" >'.$desc_categoria.'</td>';
                                    echo '<td width="7%">'.$descricao_raca.'</td>';    
                                    echo '<td width="7%">'.$descricao_pelagem.'</td>';    
                                    echo '<td width="7%">'.$descricao_mae.'</td>';    
                                    echo '<td width="7%">'.$descricao_pai.'</td>';    
                                    echo '<td width="11%" style="white-space: normal;">'.$observacao.'</td>';    
                                    echo '<td width="6%" style="text-align: right;">'.$peso_nasc_edi.'</td>'; 
                                    echo '<td width="6%" style="text-align: right;">'.$peso_desmama_edi.'</td>';    
                                    echo '<td width="6%" style="text-align: right;">'.$peso_ultimo_edi.'</td>';    
                                    echo '<td width="6%" style="text-align: center;">'.$data_ultimo_edi.'</td>';    
                                    echo '<td width="3%" style="text-align: center; color: red;">'.$animal_descarte.'</td>';    
                                    echo '</tr>';
                                }
                                else {
                                    echo '<tr style="color: red;">';
                                    echo '<td width="7%">'.$codigo_edi.'</td>';    
                                    echo '<td width="10%">'.$desc_local.'</td>';    
                                    echo '<td width="3%" style="text-align: center;">'.$sexo.'</td>';   
                                    echo '<td width="6%" style="text-align: center;">'.$data_nasc_edi.'</td>';
                                    echo '<td width="6%" style="text-align: center;">'.$idade_animal.'</td>';
                                    echo '<td width="6%" >'.$desc_categoria.'</td>';
                                    echo '<td width="7%">'.$descricao_raca.'</td>';    
                                    echo '<td width="7%">'.$descricao_pelagem.'</td>';    
                                    echo '<td width="7%">'.$descricao_mae.'</td>';    
                                    echo '<td width="7%">'.$descricao_pai.'</td>';    
                                    echo '<td width="11%" style="white-space: normal;">'.$observacao.'</td>';    
                                    echo '<td width="6%" style="text-align: right;">'.$peso_nasc_edi.'</td>'; 
                                    echo '<td width="6%" style="text-align: right;">'.$peso_desmama_edi.'</td>';    
                                    echo '<td width="6%" style="text-align: right;">'.$peso_ultimo_edi.'</td>';    
                                    echo '<td width="6%" style="text-align: center;">'.$data_ultimo_edi.'</td>';    
                                    echo '<td width="3%" style="text-align: center; color: red;">'.$animal_descarte.'</td>';    
                                    echo '</tr>';
                                }
                            }
                            else {
                                echo '<tr>';
                                echo '<td width="7%">'.$codigo_edi.'</td>';    
                                echo '<td width="10%">'.$desc_local.'</td>';    
                                echo '<td width="3%" style="text-align: center;">'.$sexo.'</td>';   
                                echo '<td width="6%" style="text-align: center;">'.$data_nasc_edi.'</td>';
                                echo '<td width="6%" style="text-align: center;">'.$idade_animal.'</td>';
                                echo '<td width="6%" >'.$desc_categoria.'</td>';
                                echo '<td width="7%">'.$descricao_raca.'</td>';    
                                echo '<td width="7%">'.$descricao_pelagem.'</td>';    
                                echo '<td width="7%">'.$descricao_mae.'</td>';    
                                echo '<td width="7%">'.$descricao_pai.'</td>';    
                                echo '<td width="11%" style="white-space: normal;">'.$observacao.'</td>';    
                                echo '<td width="6%" style="text-align: right;">'.$peso_nasc_edi.'</td>'; 
                                echo '<td width="6%" style="text-align: right;">'.$peso_desmama_edi.'</td>';    
                                echo '<td width="6%" style="text-align: right;">'.$peso_ultimo_edi.'</td>';    
                                echo '<td width="6%" style="text-align: center;">'.$data_ultimo_edi.'</td>';    
                                echo '<td width="3%" style="text-align: center; color: red;">'.$animal_descarte.'</td>';    
                                echo '</tr>';
                            }
                            $animais_listados++;
                        }
                    }
                }
            }

            echo '</tbody>';
            echo '<thead>';

            if ($total_peso_nasc!=0){
                $media_total_peso_nasc = $total_peso_nasc/$qtd_peso_nasc;
                $media_total_peso_nasc_edi = number_format($media_total_peso_nasc,2,',','.');
            }
            else {
                $media_total_peso_nasc_edi = '';
            }

            if ($total_peso_desmama!=0){
                $media_total_peso_desmama = $total_peso_desmama/$qtd_peso_desmama;
                $media_total_peso_desmama_edi = number_format($media_total_peso_desmama,2,',','.');
            }
            else {
                $media_total_peso_desmama_edi = '';
            }

            if ($total_peso_ultimo!=0){
                $media_total_peso_ultimo = $total_peso_ultimo/$qtd_peso_ultimo;
                $total_peso_ultimo_edi = number_format($total_peso_ultimo,2,',','.');
                $media_total_peso_ultimo_edi = number_format($media_total_peso_ultimo,2,',','.');
            }
            else {
                $total_peso_ultimo_edi = '';  
                $media_total_peso_ultimo_edi = ''; 
            }

            echo '<tr>
                <th style="vertical-align: middle;text-align:center;">Animais</th>

                <th rowspan="2" colspan="4" style="vertical-align: middle;text-align:center;">Legenda:&nbsp;<i class="fa fa-square" style="color: blue;"></i> &nbsp;Vendidos &nbsp;<i class="fa fa-square" style="color: red"></i> &nbsp;Morte/Outras Saídas</th>

                <th rowspan="2" colspan="6"></th>
                <th style="vertical-align: middle;text-align:center;">Média Nasc</th>
                <th style="vertical-align: middle;text-align:center;">Média Desmama</th>
                <th style="vertical-align: middle;text-align:center;">Peso Médio</th>
                <th style="vertical-align: middle;text-align:center;">Peso Total</th>
                <th rowspan="2"></th>
            </tr>';

            echo '<tr>
                <td style="text-align: center" class="animais_listados">'.$animais_listados.'</td>
                <td style="text-align: right;">'.$media_total_peso_nasc_edi.'</td>
                <td style="text-align: right;">'.$media_total_peso_desmama_edi.'</td>
                <td style="text-align: right;">'.$media_total_peso_ultimo_edi.'</td>
                <td style="text-align: right;">'.$total_peso_ultimo_edi.'</td>
            </tr>';

            echo '<tr>
                <th style="vertical-align: middle;text-align:center;">Id Animal</th>
                <th style="vertical-align: middle;text-align:center;">Fazenda</th>
                <th style="vertical-align: middle;text-align:center;">Sexo</th>
                <th style="vertical-align: middle;text-align:center;">Nascimento</th>
                <th style="vertical-align: middle;text-align:center;">Idade (meses)</th>
                <th style="vertical-align: middle;text-align:center;">Categoria</th>
                <th style="vertical-align: middle;text-align:center;">Raça</th>
                <th style="vertical-align: middle;text-align:center;">Pelagem</th>
                <th style="vertical-align: middle;text-align:center;">Mãe Id</th>
                <th style="vertical-align: middle;text-align:center;">Pai Id</th>
                <th style="vertical-align: middle;text-align:center;">Observação</th>
                <th style="vertical-align: middle;text-align:center;">Peso Nasc Kg</th>
                <th style="vertical-align: middle;text-align:center;">Peso Desmama Kg</th>
                <th style="vertical-align: middle;text-align:center;">Peso Atual Kg</th>
                <th style="vertical-align: middle;text-align:center;">Última Pesagem</th>
                <th style="vertical-align: middle;text-align:center;">Descarte</th>
            </tr>';

            echo '</thead>';
            echo '</table>';

            echo '
                <div class="row">  
                <div class="col-md-12">
                <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                </button>
                <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                    onClick="lista_historico_animais_excel()">Excel</button>
                </div>
                </div>';

            echo 
                '<script type="text/javascript">
                    $("#aguardar").modal("hide");
                </script>
            ';
        }
    }
?>

                                            </div> <!-- fim container -->
                                        </div> <!-- dados-->
                                    </div> <!--tab-content -->

                              <!--  </div> panel-body -->
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
                            <h4 class="modal-title">Listagem Animal</h4>
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
                            <h4 class="modal-title">Listagem Animal - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
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
        </section> <!-- wrapper -->
    </section><!--main-content -->


<?php 
  $javascript_file_name = 'tabela_animais.js';
  require 'rodape.php';
?>




