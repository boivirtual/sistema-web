<?php
    include "conecta_mysql.inc";

$lidos = 0; 

    $wlocal = "";
    if (isset($_POST['local'])) {
        $local = $_POST['local'];

        if(in_array("", $local)) {
            $wlocal='';
            $wlocal_anterior = '';
        }
        else {
            $wlocal = " AND tbl_animal_codigo_fazenda IN(";
            $wlocal.= implode(',', $local);
            $wlocal.= ")";

            $wlocal_anterior = " AND (tbl_animal_codigo_fazenda IN(";
            $wlocal_anterior.= implode(',', $local);
            $wlocal_anterior.= ")";
            $wlocal_anterior.= " OR (tbl_animal_codigo_origem IN(";
            $wlocal_anterior.= implode(',', $local);
            $wlocal_anterior.= ") AND tbl_animal_situacao='V'))";

        }
    }
    else {
        $wlocal='';
        $wlocal_anterior = '';
    }

    if (isset($_POST['estacao'])) {
        $estacao = $_POST['estacao'];

        $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
            WHERE tbl_par_estacao_nome='$estacao'
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

    $worigem = "";
    if (isset($_POST['origem'])) {
        $origem = $_POST['origem'];

        if(in_array("", $origem)) {
            $worigem='';
        }
        else {
            $worigem = " AND tbl_animal_codigo_origem IN(";
            $worigem.= implode(',', $origem);
            $worigem.= ")";
            }
    }
    else {
        $worigem='';
    }

    $wsexo = "";
    if (isset($_POST['sexo'])) {
        $sexo = $_POST['sexo'];

        if(in_array("Todos", $sexo)) {
            $wsexo='';
        }
        else {
            $wsexo = " AND tbl_animal_sexo IN(";
            $wsexo .= "'" . implode("','", $sexo) . "'";
            $wsexo.= ")";
            }
    }
    else {
        $wsexo='';
    }

    $wraca = "";
    if (isset($_POST['raca'])) {
        $raca = $_POST['raca'];

        if(in_array("", $raca)) {
            $wraca='';
        }
        else {
            $wraca = " AND tbl_animal_codigo_raca IN(";
            $wraca.= implode(',', $raca);
            $wraca.= ")";
            }
    }
    else {
        $wraca='';
    }

    $wpai = "";
    if (isset($_POST['pai'])) {
        $pai = $_POST['pai'];

        if(in_array("", $pai)) {
            $wpai='';
        }
        else {
            $wpai = " AND tbl_animal_codigo_pai IN(";
            $wpai.= implode(',', $pai);
            $wpai.= ")";
            }
    }
    else {
        $wpai='';
    }

    $wmae = "";
    if (isset($_POST['mae'])) {
        $mae = $_POST['mae'];

        if(in_array("", $mae)) {
            $wmae='';
        }
        else {
            $wmae = " AND tbl_animal_codigo_mae IN(";
            $wmae.= implode(',', $mae);
            $wmae.= ")";
            }
    }
    else {
        $wmae='';
    }

    $wcategoria = "";
    if (isset($_POST['categoria'])) {
        $categoria_filtro = $_POST['categoria'];

        if(in_array("", $categoria_filtro)) {
            $wcategoria='';
        }
        else {
            //$wcategoria= explode(',', $categoria_filtro);
            $wcategoria= $categoria_filtro;
       }
    }
    else {
        $wcategoria='';
    }


    //$codigo_alfa_consulta = $_POST["codigo_alfa"];
    $codigo_alfa_numerico = $_POST["codigo_alfa_numerico"];

    $peso_nasc_inicial = $_POST["peso_nasc_inicial"];
    $peso_nasc_final = $_POST["peso_nasc_final"];

    $peso_desmama_inicial = $_POST["peso_desmama_inicial"];
    $peso_desmama_final = $_POST["peso_desmama_final"];

    $peso_ult_inicial = $_POST["peso_ult_inicial"];
    $peso_ult_final = $_POST["peso_ult_final"];

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

    $data_nasc_inicial = $_POST["data_nasc_inicial"];
    $data_nasc_final = $_POST["data_nasc_final"];

    if ($data_nasc_inicial==0 && $data_nasc_final==0){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
    }

    $num_parto_de = $_POST['num_parto_de'];
    $num_parto_ate = $_POST['num_parto_ate'];
    $num_aborto_de = $_POST['num_aborto_de'];
    $num_aborto_ate = $_POST['num_aborto_ate'];
    $solteiras_filtro = $_POST["solteiras"];
    $descarte_filtro = $_POST["descarte"];
    $paridas_filtro = $_POST["paridas"];
    $data_paridas_ate = $_POST["data_paridas"];
    $previsao_parto_de = $_POST["previsao_parto_de"];
    $previsao_parto_ate = $_POST["previsao_parto_ate"];
    $positivo = $_POST["positivo"];
    $negativo = $_POST["negativo"];
    $situacao_vendido = $_POST['situacao_vendido'];
    $situacao_morte = $_POST['situacao_morte'];
    $situacao_outra = $_POST['situacao_outra'];
    $ativo_filtro = $_POST['ativo'];

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

    @ session_start(); 

    /*$_SESSION['raca']=$raca;
    $_SESSION['pai']=$pai;
    $_SESSION['mae']=$mae;
    $_SESSION['sexo']=$sexo;
    $_SESSION['ativo']=$wativo;
    $_SESSION['local']=$local;
    $_SESSION['origem']=$origem;
    $_SESSION['categoria']=$categoria_filtro;
    $_SESSION['codigo_alfa']=$codigo_alfa_consulta;
    $_SESSION['codigo_numerico']=$codigo_alfa_numerico; 
    $_SESSION['peso_nasc_inicial']=$peso_nasc_inicial; 
    $_SESSION['peso_desmama_inicial']=$peso_desmama_inicial; 
    $_SESSION['peso_ultimo_inicial']=$peso_ult_inicial; 
    $_SESSION['peso_nasc_final']=$peso_nasc_final; 
    $_SESSION['peso_desmama_final']=$peso_desmama_final; 
    $_SESSION['peso_ultimo_final']=$peso_ult_final; 
    $_SESSION['data_nasc_inicial']=$data_nasc_inicial; 
    $_SESSION['data_nasc_final']=$data_nasc_final; 
    $_SESSION['lista_animais']='S';
    $_SESSION['previsao_parto_de']=$previsao_parto_de;
    $_SESSION['previsao_parto_ate']=$previsao_parto_ate;
    $_SESSION['num_parto_de']=$num_parto_de;
    $_SESSION['num_parto_ate']=$num_parto_ate;
    $_SESSION['num_aborto_de']=$num_aborto_de;
    $_SESSION['num_aborto_ate']=$num_aborto_ate;
    $_SESSION['solteiras']=$solteiras_filtro;
    $_SESSION['descarte']=$descarte_filtro;
    $_SESSION['paridas']=$paridas_filtro;
    $_SESSION['data_paridas_ate']=$data_paridas_ate;
    $_SESSION['positivo']=$positivo;
    $_SESSION['negativo']=$negativo;*/

// incializa campos vazios    
    $data_previsao_parto = '0000-00-00';

    if ($previsao_parto_de=='') {
        $previsao_parto_de = '0000-00-00';
        $previsao_parto_ate = '9999-99-99';
    }

    if ($num_parto_de=='') {
        $num_parto_de = 0;
        $num_parto_ate = 999;
    }

    if ($num_aborto_de=='') {
        $num_aborto_de = 0;
        $num_aborto_ate = 999;
    }

    if ($data_paridas_ate=='') {
        $data_paridas_ate='9999-99-99';
        $data_paridas_de='0000-00-00';
    }
    else {
        $data_paridas_de = date("Y-m-d", strtotime('-8 month',strtotime($data_paridas_ate)));
        $data_paridas_de = date("Y-m-d", strtotime('-1 day',strtotime($data_paridas_de)));
    }

    $tem_descarte = '';
    $tem_negativo = '';
    $tem_positivo = '';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Creative - Bootstrap 3 Responsive Admin Template">
  <meta name="author" content="GeeksLabs">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet">
  <link href="css/font-awesome.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet">
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

</head>

<body> 
  <?php    
	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_animais" style="font-size: 13px">';
                          
            echo '<tbody>';
          
                if ($codigo_alfa_numerico!='') {
                    $codigo_numerico_consulta = substr($codigo_alfa_numerico, -9);

                    if (strlen($codigo_alfa_numerico)!=9){
                        $data = explode("-", $codigo_alfa_numerico);
                        $codigo_alfa_consulta = $data[0];
                    }
                    else {
                        $codigo_alfa_consulta = '';
                    }

                    $sql = "SELECT * FROM tbl_animais 
                        WHERE tbl_animal_lixeira=0 AND 
                              tbl_animal_codigo_alfa='$codigo_alfa_consulta' AND 
                              tbl_animal_codigo_numerico='$codigo_numerico_consulta'"; 
                }
                else {
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
                            tbl_animal_codigo_numerico ASC"; 
                    }
                }

                //print_r($sql . '</br>');
                
                $rs = mysqli_query($conector, $sql); 

                $num_rows = mysqli_num_rows($rs);

                //print_r($num_rows);

               // exit;

                while ($reg_animal = mysqli_fetch_object($rs)){
                    $codigo_local = $reg_animal->tbl_animal_codigo_fazenda;
                    $codigo = $reg_animal->tbl_animal_codigo_id;
                    $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                    $codigo_numerico = intval($reg_animal->tbl_animal_codigo_numerico);
                    $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
                    $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
                    $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
                    $codigo_origem = $reg_animal->tbl_animal_codigo_origem;
                    $sexo = $reg_animal->tbl_animal_sexo; 
                    $mae = $reg_animal->tbl_animal_codigo_mae; 
                    $pai = $reg_animal->tbl_animal_codigo_pai; 
                    $lixeira = $reg_animal->tbl_animal_lixeira; 
                    $ativo = $reg_animal->tbl_animal_ativo; 
                    $situacao = $reg_animal->tbl_animal_situacao; 
                    $descarte_reproducao = $reg_animal->tbl_animal_descarte_reproducao; 
                    $reprodutor = $reg_animal->tbl_animal_reprodutor; 
                    $obs = '';
                    $observacao = $reg_animal->tbl_animal_observacao;

                    $movimentacao_compra = $reg_animal->tbl_animal_movimentacao_compra;

                    if ($movimentacao_compra!='') {
                        $tbl_compra = mysqli_query($conector, "select * from tbl_movimentacao
                            where tbl_movimentacao_id='$movimentacao_compra'");
                        $num_rows = mysqli_num_rows($tbl_compra);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tbl_compra);
                            $data_compra = new DateTime($reg->tbl_movimentacao_data);
                            $data_compra = $data_compra->format('d/m/Y');
                            $origem_compra = $reg->tbl_movimentacao_codigo_local_origem;
                            $destino_compra = $reg->tbl_movimentacao_codigo_local_destino;

                            $tbl_origem = mysqli_query($conector, "select * from tbl_pessoa
                                where tbl_pessoa_id='$origem_compra'");
                            $num_rows = mysqli_num_rows($tbl_origem);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tbl_origem);
                                $desc_origem_compra = $reg->tbl_pessoa_nome;
                            }
                            else {
                                $desc_origem_compra = '';
                            }

                            $tbl_destino = mysqli_query($conector, "select * from tbl_pessoa
                                where tbl_pessoa_id='$destino_compra'");
                            $num_rows = mysqli_num_rows($tbl_destino);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tbl_destino);
                                $desc_destino_compra = $reg->tbl_pessoa_nome;
                            }
                            else {
                                $desc_destino_compra = '';
                            }
                        }
                        else {
                            $data_compra = '';
                            $desc_origem_compra = '';
                            $desc_destino_compra = '';
                        }
                    }
                    else {
                        $data_compra = '';
                        $desc_origem_compra = '';
                        $desc_destino_compra = '';
                    }

                    if ($descarte_filtro=='S') {
                        if ($descarte_reproducao=='S') {
                            $tem_descarte = 'S';
                        }
                        else {
                            $tem_descarte = '';
                        }
                    }
                    else {
                        $tem_descarte = '';
                    }

                    if ($codigo_pelagem == '' || $codigo_pelagem==999) {
                        $codigo_pelagem = '000';
                    }

                    switch ($situacao) {
                        case 'V':
                            $desc_situacao = 'Vendido';
                            break;
                        case 'M':
                            $desc_situacao = 'Morte';
                            break;
                        case 'T':
                            $desc_situacao = 'Aguardando Transf';
                            break;
                        case 'S':
                            $desc_situacao = 'Outra Saída';
                            break;
                        default:
                            $desc_situacao = '';
                            break;
                    }

                    $tab_mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$mae'");
                    $num_rows = mysqli_num_rows($tab_mae);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_mae);
                        $descricao_mae = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                    }
                    else {
                        $descricao_mae = '';
                        $mae='000000000';
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
                            $pai='000000000';
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

                    $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_fazenda'");
                    $num_rows = mysqli_num_rows($tab_fazenda);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_fazenda);
                        $desc_local = $reg->tbl_pessoa_nome;
                    }
                    else {
                        $desc_local = '';
                    }

                    $tbl_origem = mysqli_query($conector, "select * from tbl_pessoa
                        where tbl_pessoa_id='$codigo_origem'");
                    $num_rows = mysqli_num_rows($tbl_origem);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_origem);
                        $desc_origem = $reg->tbl_pessoa_nome;
                    }
                    else {
                        $desc_origem = '';
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
                    $idade_acompanhamento_mostra_dias = $idade_acompanhamento->format('%d');

                    $idade_ano = $idade_acompanhamento->format('%Y');
                    $idade_mes = $idade_acompanhamento->format('%m');
                    $idade_dia = $idade_acompanhamento->format('%d');

                    if ($idade_ano==0 && $idade_mes!=0) {
                        $idade_animal = $idade_mes . ' mes(es)';
                    }
                    else if ($idade_ano!=0 && $idade_mes==0){
                        $idade_animal = $idade_ano . ' ano(s)';
                    }
                    else if ($idade_ano!=0 && $idade_mes!=0) {
                        $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
                    }
                    else if ($idade_ano==0 && $idade_mes==0){
                        $idade_animal = $idade_dia . ' dia(s)';
                    }
                    else {
                        $idade_animal = '';
                    }

                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                        WHERE tab_registro_lixeira_categoria_idade='0'");

                    $num_rows = mysqli_num_rows($categoria);    

                    if ($num_rows!=0) {
                        while ($reg_categoria = mysqli_fetch_object($categoria)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

                                if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                                    $desc_categoria=' > 36 meses';
                                }
                                else {
                                $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
                                }
                            }
                        }
                    }                   

                    // primeiro verifica quantos partos
                    $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                        WHERE tbl_animal_codigo_mae='$codigo'");

                    $num_partos = mysqli_num_rows($tbl_filhos);

                    // agora verifica qual o ultimo parto para saber a idade
                    $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                        WHERE tbl_animal_codigo_mae='$codigo'
                        ORDER BY tbl_animal_data_nascimento DESC limit 1");

                    $ultimo_filho = mysqli_num_rows($tbl_filhos);

                    if ($paridas_filtro=='S' || $solteiras_filtro=='S') {
                        $parida = '';
                        $solteira = '';

                        if ($ultimo_filho!=0) {
                            $reg_filhos = mysqli_fetch_object($tbl_filhos);
                            $bezerro_ativo = $reg_filhos->tbl_animal_ativo;

                            $nascimento_filho = $reg_filhos->tbl_animal_data_nascimento;  
                            $data_acompanhamento_calculo = date("Y-m-d");
                            $date = new DateTime($nascimento_filho); // Data de Nascimento
                            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                            $idade_ano = $idade_acompanhamento->format('%Y');
                            $idade_mes = $idade_acompanhamento->format('%m');
                            $idade_filho = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                            if ($idade_filho < 8 && $nascimento_filho>=$data_paridas_de && $nascimento_filho<=$data_paridas_ate) {

                                if ($bezerro_ativo=='S') {
                                    $parida = 'S';
                                }
                                else {
                                    $solteira = 'S';
                                }
                            }
                            else {
                                $solteira = 'S';
                            }

                            $parida_lida = $parida;
                            $solteira_lida = $solteira;
                        }
                        else {
                            $parida = '';
                            $parida_lida = '';
                            $solteira = '';
                            $solteira_lida = '';
                        }
                    }
                    else {
                        $parida_lida = '';
                        $solteira_lida = '';

                        $parida = '';
                        $solteira = '';

                        if ($ultimo_filho!=0) {
                            $reg_filhos = mysqli_fetch_object($tbl_filhos);
                            $bezerro_ativo = $reg_filhos->tbl_animal_ativo;

                            $nascimento_filho = $reg_filhos->tbl_animal_data_nascimento;  
                            $data_acompanhamento_calculo = date("Y-m-d");
                            $date = new DateTime($nascimento_filho); // Data de Nascimento
                            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                            $idade_ano = $idade_acompanhamento->format('%Y');
                            $idade_mes = $idade_acompanhamento->format('%m');
                            $idade_filho = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                            if ($idade_filho < 8 && $nascimento_filho>=$data_paridas_de && $nascimento_filho<=$data_paridas_ate) {
                                if ($bezerro_ativo=='S') {
                                    $parida = 'S';
                                }
                                else {
                                    $solteira = 'S';
                                }
                            }
                            else {
                                $solteira = 'S';
                            }
                        }
                        else {
                            $parida = '';
                            $solteira = '';
                        }
                    }

                    // verifica a cobertura do animal
                    $sql = mysqli_query($conector, "SELECT * FROM
                            tbl_item_cobertura
                        INNER JOIN tbl_cobertura
                                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                        WHERE tbl_cobertura_lixeira=0 AND 
                                  tbl_ite_cobertura_codigo_id_animal='$codigo'" . $westacao . "
                        ORDER BY tbl_ite_cobertura_numero_id DESC limit 1");

                    $num_rows = mysqli_num_rows($sql);

                    if ($num_rows!=0) {
                        $reg_cobertura = mysqli_fetch_object($sql);
                        $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
                        $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
                    }
                    else {
                        $codigo_local = 0;
                        $estacao_animal = 0;
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
                                  tbl_cobertura_controle = 'C'   AND 
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
                        ($nascido_aborto!='')){
                        $tem_positivo='';
                    }

                    $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                        where tbl_mov_estoque_codigo_mae='$codigo' and 
                              tbl_mov_estoque_codigo_id_animal=999999999 and 
                              tbl_mov_estoque_entrada_saida='E' and 
                              tbl_mov_estoque_tipo_movimentacao='N'");
                    $num_natimorto = mysqli_num_rows($tbl_natimorto);

                    $num_partos = $num_partos + $num_natimorto;

                    $data_hoje = date('Y-m-d');
 
                    $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
                        WHERE tbl_par_codigo_local = '$codigo_local' AND 
                              tbl_par_lixeira=0 AND 
                              tbl_par_estacao_monta_inicial<='$data_hoje' AND 
                              tbl_par_estacao_monta_final>='$data_hoje'");  

                    $num_rows = mysqli_num_rows($sql);

                    if($num_rows != 0){
                        $reg_estacao = mysqli_fetch_object($sql);
                        $id_estacao = $reg_estacao->tbl_par_estacao_id;
                        $desc_estacao = $reg_estacao->tbl_par_estacao_nome;
                    }
                    else {
                        $id_estacao=0;
                        $desc_estacao = '';
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

                    if ($data_previsao_parto!='0000-00-00' AND 
                        $nascido_aborto!='') {
                        $data_previsao_parto='0000-00-00';
                    }

                    $incluido_em=new DateTime($reg_animal->tbl_animal_incluido_em);
                    $incluido_por=$reg_animal->tbl_animal_incluido_por; 
                    $alterado_em=new DateTime($reg_animal->tbl_animal_alterado_em);
                    $alterado_por=$reg_animal->tbl_animal_alterado_por; 
                    $baixado_em=new DateTime($reg_animal->tbl_animal_baixado_em);
                    $baixado_por=$reg_animal->tbl_animal_baixado_por; 
                    $descarte_em=new DateTime($reg_animal->tbl_animal_descarte_em);
                    $descarte_por=$reg_animal->tbl_animal_descarte_por; 

                    $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
                    $alterado_em_edi = $alterado_em->format('d/m/Y H:i:s');
                    $baixado_em_edi = $baixado_em->format('d/m/Y H:i:s');
                    $descarte_em_edi = $descarte_em->format('d/m/Y H:i:s');

                    if ($reg_animal->tbl_animal_data_primeiro_peso=='') {
                        $data_peso_nasc_edi = '00/00/0000 00:00:00';
                    }
                    else {
                        $data_peso_nasc=new DateTime($reg_animal->tbl_animal_data_primeiro_peso);
                        $data_peso_nasc_edi = $data_peso_nasc->format('d/m/Y H:i:s');
                    } 
                    $lote_peso_nasc = $reg_animal->tbl_animal_lote_primeiro_peso;

                    if ($reg_animal->tbl_animal_data_desmama=='') {
                        $data_peso_desmama_edi = '00/00/0000 00:00:00';
                    }
                    else {
                        $data_peso_desmama=new DateTime($reg_animal->tbl_animal_data_desmama);
                        $data_peso_desmama_edi = $data_peso_desmama->format('d/m/Y H:i:s');
                    } 

                    $lote_peso_desmama = $reg_animal->tbl_animal_lote_desmama;

                    if ($reg_animal->tbl_animal_data_ultimo=='') {
                        $data_peso_ultimo_edi = '00/00/0000 00:00:00';
                    }
                    else {
                        $data_peso_ultimo=new DateTime($reg_animal->tbl_animal_data_ultimo);
                        $data_peso_ultimo_edi = $data_peso_ultimo->format('d/m/Y H:i:s');
                    } 

                    $lote_peso_ultimo = $reg_animal->tbl_animal_lote_ultimo;

                    $em_estacao_monta = $reg_animal->tbl_animal_em_estacao_monta;

                    // Não usa mais como opção na tela aba reprodução 
                    $aguardando_diagnostico = $reg_animal->tbl_animal_aguardando_diagnostico;
                    $prenhe = $reg_animal->tbl_animal_prenhe;
                    // ----------------------------------------------

                    $num_coberturas = $reg_animal->tbl_animal_numero_coberturas;

                    $num_abortos = $reg_animal->tbl_animal_numero_abortos;

                    if($em_estacao_monta == ''){
                        $em_estacao_monta = 'N';
                    }

                    // Não usa mais como opção na tela aba reprodução 
                    if($aguardando_diagnostico == ''){
                        $aguardando_diagnostico = 'N';
                    }
                    if($prenhe == ''){
                        $prenhe = 'N';
                    }
                    // ----------------------------------------------

                    if($num_coberturas == ''){
                        $num_coberturas = 0;
                    }

                    if($num_abortos == ''){
                        $num_abortos = 0;
                    }

                    $array_animal = array(
                        $reg_animal->tbl_animal_codigo_id,
                        $reg_animal->tbl_animal_codigo_numerico,
                        $reg_animal->tbl_animal_sexo,
                        $reg_animal->tbl_animal_codigo_raca,
                        $codigo_pelagem,
                        $reg_animal->tbl_animal_data_nascimento,
                        $reg_animal->tbl_animal_grau_sangue,
                        $reg_animal->tbl_animal_codigo_fazenda,
                        $desc_origem,
                        $pai,
                        $reg_animal->tbl_animal_nome_pai,
                        $mae,
                        $reg_animal->tbl_animal_nome_mae,
                        $reg_animal->tbl_animal_primeiro_peso,
                        $reg_animal->tbl_animal_peso_desmama,
                        $reg_animal->tbl_animal_ultimo_peso,
                        $reg_animal->tbl_animal_nome,
                        $reg_animal->tbl_animal_registro_ren,
                        $reg_animal->tbl_animal_registro_rgd,
                        $reg_animal->tbl_animal_registro_sisbov,
                        $reg_animal->tbl_animal_certificadora,
                        $obs,
                        $reg_animal->tbl_animal_ativo,
                        $reg_animal->tbl_animal_codigo_alfa,
                        $desc_categoria,
                        $idade_animal,
                        $incluido_em_edi,
                        $incluido_por,
                        $alterado_em_edi,
                        $alterado_por,
                        $baixado_em_edi,
                        $baixado_por,
                        $lote_peso_nasc,
                        $data_peso_nasc_edi,
                        $lote_peso_desmama,
                        $data_peso_desmama_edi,
                        $lote_peso_ultimo,
                        $data_peso_ultimo_edi,
                        $desc_situacao,
                        $em_estacao_monta,
                        // Não usa mais como opção na tela aba reprodução 
                        $aguardando_diagnostico, 
                        $prenhe,
                        //-----------------------------------------------

                        $parida,
                        $solteira,
                        $num_coberturas,
                        $num_partos,
                        $num_abortos,
                        $descarte_reproducao,
                        $descarte_em_edi,
                        $descarte_por,
                        $reprodutor,
                        $desc_estacao,
                        $id_estacao,
                        $data_compra,
                        $desc_origem_compra,
                        $desc_destino_compra,
                        $movimentacao_compra
                    );   
                                    
                    $string_array = implode('|', $array_animal);

                    /*if ($reg_animal->tbl_animal_codigo_numerico==5) {
                        var_dump($array_animal);
                    }*/

                    if ($codigo_alfa_numerico!='') {
                        echo "<tr>";

                        if ($ativo=="N") {
                            echo "<td align='right' style='color:#FF9393'>".$codigo_alfa."</td>";
                            echo "<td style='color:#FF9393'>".$codigo_numerico."</td>";
                            if ($descarte_reproducao=='S') {
                                echo "<td style='color:#FF9393'>Sim</td>";
                            }
                            else {
                                echo "<td style='color:#FF9393'></td>";
                            }
                            echo "<td style='color:#FF9393'>".$descricao_raca."</td>";
                            echo "<td style='color:#FF9393'>".$desc_categoria."</td>";
                            echo "<td style='color:#FF9393'>".$sexo."</td>";
                            echo "<td style='color:#FF9393'>".$desc_local."</td>";
                            echo "<td style='color:#FF9393'>".$descricao_mae."</td>";
                            echo "<td style='color:#FF9393'>".$descricao_pai."</td>";
                            echo "<td style='color:#FF9393'>".$desc_situacao."</td>";
                            echo "<td width='10%'>";    
                            echo "<div class='btn-group'>";
                            echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\");";
                            if($sexo == 'F'){
                                echo "mostrar_reproducao()";
                            }else{
                                echo "esconder_reproducao()";
                            }
                            echo "' ></i></a>"; 
                            //echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                            echo "</div>";
                            echo "</td>";
                        }
                        else {
                            echo "<td align='right' width='4%'>".$codigo_alfa."</td>";
                            echo "<td width='16%'>".$codigo_numerico."</td>";
                            if ($descarte_reproducao=='S') {
                                echo "<td width='5%' 
                                style='color:red'>Sim</td>";
                            }
                            else {
                                echo "<td width='5%'
                                 style='color:red'></td>";
                            }
                            echo "<td width='10%'>".$descricao_raca."</td>";
                            echo "<td width='10%'>".$desc_categoria."</td>";
                            echo "<td width='5%'>".$sexo."</td>";
                            echo "<td width='10%'>".$desc_local."</td>";
                            echo "<td width='10%'>".$descricao_mae."</td>";
                            echo "<td width='10%'>".$descricao_pai."</td>";
                            echo "<td width='10%'>".$desc_situacao."</td>";
                            echo "<td width='10%'>";    
                            echo "<div class='btn-group'>";
                            echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\");";
                            if($sexo == 'F'){
                                echo "mostrar_reproducao()";
                            }else{
                                echo "esconder_reproducao()";
                            }
                            echo "' ></i></a>"; 
                            //echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                            echo "</div>";
                            echo '<input type="hidden" id="obs" value="'.$observacao.'"';
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    else if ($wcategoria=="") {
                        $lidos++; 
                        if ($data_previsao_parto>=$previsao_parto_de && 
                            $data_previsao_parto<=$previsao_parto_ate &&
                            $num_partos>=$num_parto_de && 
                            $num_partos<=$num_parto_ate &&
                            $num_abortos>=$num_aborto_de && 
                            $num_abortos<=$num_aborto_ate && 
                            ($paridas_filtro==$parida_lida || 
                            $solteiras_filtro==$solteira_lida) &&
                            $tem_descarte==$descarte_filtro && 
                            ($positivo==$tem_positivo && 
                            $negativo==$tem_negativo)
                            ) {

                            echo "<tr>";

                            if ($ativo=="N") {
                                echo "<td align='right' style='color:#FF9393'>".$codigo_alfa."</td>";
                                echo "<td style='color:#FF9393'>".$codigo_numerico."</td>";
                                if ($descarte_reproducao=='S') {
                                    echo "<td width='5%' 
                                    style='color:#FF9393'>Sim</td>";
                                }
                                else {
                                    echo "<td width='5%'
                                     style='color:#FF9393'></td>";
                                }
                                echo "<td style='color:#FF9393'>".$descricao_raca."</td>";
                                echo "<td style='color:#FF9393'>".$desc_categoria."</td>";
                                echo "<td style='color:#FF9393'>".$sexo."</td>";
                                echo "<td style='color:#FF9393'>".$desc_local."</td>";
                                echo "<td style='color:#FF9393'>".$descricao_mae."</td>";
                                echo "<td style='color:#FF9393'>".$descricao_pai."</td>";
                                echo "<td style='color:#FF9393'>".$desc_situacao."</td>";
                                echo "<td width='10%'>";    
                                echo "<div class='btn-group'>";
                                echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\");";
                                if($sexo == 'F'){
                                    echo "mostrar_reproducao()";
                                }else{
                                    echo "esconder_reproducao()";
                                }
                                echo "' ></i></a>"; 
                                //echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                                echo "</div>";
                                echo "</td>";
                            }
                            else {
                                echo "<td align='right' width='4%'>".$codigo_alfa."</td>";
                                echo "<td width='16%'>".$codigo_numerico."</td>";
                                if ($descarte_reproducao=='S') {
                                    echo "<td width='5%' 
                                    style='color:red'>Sim</td>";
                                }
                                else {
                                    echo "<td width='5%'
                                     style='color:red'></td>";
                                }
                                echo "<td width='10%'>".$descricao_raca."</td>";
                                echo "<td width='10%'>".$desc_categoria."</td>";
                                echo "<td width='5%'>".$sexo."</td>";
                                echo "<td width='10%'>".$desc_local."</td>";
                                echo "<td width='10%'>".$descricao_mae."</td>";
                                echo "<td width='10%'>".$descricao_pai."</td>";
                                echo "<td width='10%'>".$desc_situacao."</td>";
                                echo "<td width='10%'>";    
                                echo "<div class='btn-group'>";
                                echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\");";
                                if($sexo == 'F'){
                                    echo "mostrar_reproducao()";
                                }else{
                                    echo "esconder_reproducao()";
                                }
                                echo "' ></i></a>"; 
                                echo "</div>";
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                    }
                    else {
                        foreach ($wcategoria as $value) {
                            if ($value==$codigo_categoria) {

                                if ($data_previsao_parto>=$previsao_parto_de && 
                                    $data_previsao_parto<=$previsao_parto_ate &&
                                    $num_partos>=$num_parto_de && 
                                    $num_partos<=$num_parto_ate && 
                                    $num_abortos>=$num_aborto_de && 
                                    $num_abortos<=$num_aborto_ate && 
                                    ($paridas_filtro==$parida_lida || 
                                    $solteiras_filtro==$solteira_lida) &&
                                    $tem_descarte==$descarte_filtro && 
                                    ($positivo==$tem_positivo || 
                                    $negativo==$tem_negativo)
                                    ) {

                                    echo "<tr>";

                                    if ($ativo=="N") {
                                        echo "<td align='right' style='color:#FF9393'>".$codigo_alfa."</td>";
                                        echo "<td style='color:#FF9393'>".$codigo_numerico."</td>";
                                        if ($descarte_reproducao=='S') {
                                            echo "<td width='5%' 
                                            style='color:#FF9393'>Sim</td>";
                                        }
                                        else {
                                            echo "<td width='5%'
                                             style='color:#FF9393'></td>";
                                        }
                                        echo "<td style='color:#FF9393'>".$descricao_raca."</td>";
                                        echo "<td style='color:#FF9393'>".$desc_categoria."</td>";
                                        echo "<td style='color:#FF9393'>".$sexo."</td>";
                                        echo "<td style='color:#FF9393'>".$desc_local."</td>";
                                        echo "<td style='color:#FF9393'>".$descricao_mae."</td>";
                                        echo "<td style='color:#FF9393'>".$descricao_pai."</td>";
                                        echo "<td style='color:#FF9393'>".$desc_situacao."</td>";
                                        echo "<td width='10%'>";    
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\");";
                                        if($sexo == 'F'){
                                            echo "mostrar_reproducao()";
                                        }else{
                                            echo "esconder_reproducao()";
                                        }
                                        echo "' ></i></a>"; 
                                        //echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                                        echo "</div>";
                                        echo "</td>";
                                    }
                                    else {
                                        echo "<td align='right' width='4%'>".$codigo_alfa."</td>";
                                        echo "<td width='16%'>".$codigo_numerico."</td>";
                                        if ($descarte_reproducao=='S') {
                                            echo "<td width='5%' 
                                            style='color:red'>Sim</td>";
                                        }
                                        else {
                                            echo "<td width='5%'
                                             style='color:red'></td>";
                                        }
                                        echo "<td width='10%'>".$descricao_raca."</td>";
                                        echo "<td width='10%'>".$desc_categoria."</td>";
                                        echo "<td width='5%'>".$sexo."</td>";
                                        echo "<td width='10%'>".$desc_local."</td>";
                                        echo "<td width='10%'>".$descricao_mae."</td>";
                                        echo "<td width='10%'>".$descricao_pai."</td>";
                                        echo "<td width='10%'>".$desc_situacao."</td>";
                                        echo "<td width='10%'>";    
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\");";
                                        if($sexo == 'F'){
                                            echo "mostrar_reproducao()";
                                        }else{
                                            echo "esconder_reproducao()";
                                        }
                                        echo "' ></i></a>"; 
                                        //echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                                        echo "</div>";
                                        echo "</td>";
                                    }
                                    echo "</tr>";
                                }
                            }
                        }
                    }
                } 
                mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> <i class="fa fa-sort-alpha-asc"></i></th>
                    <th> Código Numérico</th>
                    <th> Descarte</th>
                    <th> Raça</th>
                    <th> Categoria</th>
                    <th> Sexo</th>
                    <th> Local</th>
                    <th> Mãe</th>
                    <th> Pai</th>
                    <th> Situação</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

   // echo '<script src="js/tabela.js" charset="utf-8" type="text/javascript" ></script>'

?>

    <script src="js/tabela_animais.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
    </script>

</body>
</html> 


                
                
