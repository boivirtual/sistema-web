<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $ano = $_REQUEST['ano'];

    /*$wconta = "";
    if (isset($_POST['codigo_conta'])) {
        $codigo_conta = $_POST['codigo_conta'];

        if(in_array("", $codigo_conta)) {
            $wconta='';
        }
        else {
            $wconta = " AND tbl_previsao_conta_codigo IN(";
            $wconta.= implode(',', $codigo_conta);
            $wconta.= ")";
            }
    }
    else {
        $wconta='';
    }*/


    $array_conta = $_REQUEST["array_conta"];
    $conta = array();
    $matriz_itens = explode(",", $array_conta);
    $quantidade_itens = count($matriz_itens);

    // monta array das contas
    for($i=0; $i < $quantidade_itens; $i++) {

        if (substr($matriz_itens[$i], 3, 4) !=0) {
            $conta[$i]=$matriz_itens[$i];
        }
    }

    $conta = implode(',', $conta);

    $wconta = '';

    if ($array_conta!='') {
        $wconta = " AND tbl_previsao_conta_codigo IN(";
        $wconta.= $conta;
        $wconta.= ")";
    }

    $array_fazenda = $_REQUEST["array_fazenda"];
    $fazenda= array();
    $matriz_itens = explode(",", $array_fazenda);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $fazenda[$i]=$matriz_itens[$i];
    }

    $fazenda = implode(',', $fazenda);
    $fazenda = substr($fazenda,0, -1);

    $wlocal = '';

    if ($array_fazenda!='') {
        $wlocal = " AND tbl_previsao_conta_codigo_fazenda IN(";
        $wlocal.= $fazenda;
        $wlocal.= ")";
    }

    @ session_start(); 

    $_SESSION['lista_previsao']='S'; 
    $_SESSION['codigo_local_previsao']=$array_fazenda; 
    $_SESSION['codigo_conta_previsao']=$array_conta; 
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
	<section class="panel lista_contas">
    <table class="table table-striped table-advance table-hover" id="tabela_previsao_conta" style="font-size: 10px">
                          
        <tbody>
    <?php    
                $total_receitas = 0;
                $total_despesas = 0;
                $sql = "SELECT * FROM tbl_previsao_conta 
                                WHERE tbl_previsao_conta_lixeira=0 AND 
                                      tbl_previsao_conta_ano='$ano'" . 
                                $wconta . $wlocal . 
                                " ORDER BY tbl_previsao_conta_codigo, tbl_previsao_conta_ano ASC"; 

                $rs = mysqli_query($conector, $sql); 
                     
                while ($reg_conta = mysqli_fetch_object($rs)){
                    $codigo = $reg_conta->tbl_previsao_conta_id;
                    $codigo_conta = $reg_conta->tbl_previsao_conta_codigo;
                    $codigo_local = $reg_conta->tbl_previsao_conta_codigo_fazenda;
                    $ano_conta = $reg_conta->tbl_previsao_conta_ano;
                    $valor_jan = $reg_conta->tbl_previsao_conta_valor_jan;
                    $valor_fev = $reg_conta->tbl_previsao_conta_valor_fev;
                    $valor_mar = $reg_conta->tbl_previsao_conta_valor_mar;
                    $valor_abr = $reg_conta->tbl_previsao_conta_valor_abr;
                    $valor_mai = $reg_conta->tbl_previsao_conta_valor_mai;
                    $valor_jun = $reg_conta->tbl_previsao_conta_valor_jun;
                    $valor_jul = $reg_conta->tbl_previsao_conta_valor_jul;
                    $valor_ago = $reg_conta->tbl_previsao_conta_valor_ago;
                    $valor_set = $reg_conta->tbl_previsao_conta_valor_set;
                    $valor_out = $reg_conta->tbl_previsao_conta_valor_out;
                    $valor_nov = $reg_conta->tbl_previsao_conta_valor_nov;
                    $valor_dez = $reg_conta->tbl_previsao_conta_valor_dez;
                    $lixeira = $reg_conta->tbl_previsao_conta_lixeira; 

                    $codigo_edi = substr($codigo_conta, 0,1) .'.'.  
                                  substr($codigo_conta, 1,2) .'.'.  
                                  substr($codigo_conta, 3,4);

                    $tab_conta_contabil = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_codigo_id='$codigo_conta'");
                    $num_rows = mysqli_num_rows($tab_conta_contabil);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_conta_contabil);
                        $descricao_conta = $reg->tbl_plano_contas_descricao;
                        $deb_cre = $reg->tbl_plano_contas_debito_credito;
                    }
                    else {
                        $descricao_conta = '';
                        $deb_cre = '';
                    }

                    $tbl_pessoa = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_local'");
                    $num_rows = mysqli_num_rows($tbl_pessoa);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_pessoa);
                        $nome = $reg->tbl_pessoa_nome;
                    }
                    else {
                        $nome = '';
                    }

                    if ($deb_cre=="C") {
                        $total_receitas+=$valor_jan+$valor_fev+$valor_mar+$valor_abr+$valor_mai+$valor_jun+$valor_jul+$valor_ago+$valor_set+$valor_out+$valor_nov+$valor_dez;
                    }
                    else if ($deb_cre=="D") {
                        $total_despesas+=$valor_jan+$valor_fev+$valor_mar+$valor_abr+$valor_mai+$valor_jun+$valor_jul+$valor_ago+$valor_set+$valor_out+$valor_nov+$valor_dez;
                    }

                    $incluido_em=new DateTime($reg_conta->tbl_previsao_conta_incluido_em);
                    $incluido_por=$reg_conta->tbl_previsao_conta_incluido_por; 
                    $alterado_em=new DateTime($reg_conta->tbl_previsao_conta_alterado_em);
                    $alterado_por=$reg_conta->tbl_previsao_conta_alterado_por; 
                    $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
                    $alterado_em_edi = $alterado_em->format('d/m/Y H:i:s');

                    $array_conta = array(
                        $reg_conta->tbl_previsao_conta_id,
                        $reg_conta->tbl_previsao_conta_codigo,
                        $reg_conta->tbl_previsao_conta_ano,
                        $reg_conta->tbl_previsao_conta_valor_jan,
                        $reg_conta->tbl_previsao_conta_valor_fev,
                        $reg_conta->tbl_previsao_conta_valor_mar,
                        $reg_conta->tbl_previsao_conta_valor_abr,
                        $reg_conta->tbl_previsao_conta_valor_mai,
                        $reg_conta->tbl_previsao_conta_valor_jun,
                        $reg_conta->tbl_previsao_conta_valor_jul,
                        $reg_conta->tbl_previsao_conta_valor_ago,
                        $reg_conta->tbl_previsao_conta_valor_set,
                        $reg_conta->tbl_previsao_conta_valor_out,
                        $reg_conta->tbl_previsao_conta_valor_nov,
                        $reg_conta->tbl_previsao_conta_valor_dez,
                        $incluido_em_edi,
                        $incluido_por,
                        $alterado_em_edi,
                        $alterado_por,
                        $reg_conta->tbl_previsao_conta_codigo_fazenda
                    );   
                                    
                    $string_array = implode('|', $array_conta);

                    echo "<tr>";
                    echo "<td width='6%'>".$codigo_edi."</td>";
                    echo "<td width='12%'>".$descricao_conta."</td>";
                    echo "<td width='12%'>".$nome."</td>";
                    echo "<td width='5%'>".number_format($valor_jan, 2, ",", ".")."</td>";
                    echo "<td width='5%'>".number_format($valor_fev, 2, ",", ".")."</td>";
                    echo "<td width='5%'>".number_format($valor_mar, 2, ",", ".")."</td>";
                    echo "<td width='5%'>".number_format($valor_abr, 2, ",", ".")."</td>";
                    echo "<td width='5%'>".number_format($valor_mai, 2, ",", ".")."</td>";
                    echo "<td width='5%'>".number_format($valor_jun, 2, ",", ".")."</td>";
                    echo "<td width='5%'>".number_format($valor_jul, 2, ",", ".")."</td>";
                    echo "<td width='5%'>".number_format($valor_ago, 2, ",", ".")."</td>";
                    echo "<td width='5%'>".number_format($valor_set, 2, ",", ".")."</td>";
                    echo "<td width='5%'>".number_format($valor_out, 2, ",", ".")."</td>";
                    echo "<td width='5%'>".number_format($valor_nov, 2, ",", ".")."</td>";
                    echo "<td width='5%'>".number_format($valor_dez, 2, ",", ".")."</td>";
                        
                    echo "<td width='10%'>";    
                        echo "<div class='btn-group'>";
                        echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' style='font-size: 10px' onClick='editar_conta(\"{$string_array}\")' ></i></a>"; 
                        echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' style='font-size: 10px' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                        echo "</div>";
                        echo "</td>";
                } 
                mysqli_close($conector);

                echo '
                    <script type="text/javascript">
                        $("#aguardar").modal("hide");
                    </script>
                    ';
?>

            </tbody>

            <thead>
                <tr>
                    <div class="row col-md-8" id="total_contas">
                        <div class="form-group col-md-3">
                            <label class="control-label">Total de Receitas</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".number_format($total_receitas, 2, ",", ".")."'";?>>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="control-label">Total de Despesas</label>
                            <input class="form-control form-control-sm" type="text" readonly="" 
                            <?php echo "value='".number_format($total_despesas, 2, ",", ".")."'";?>>
                        </div>

                    </div>
                </tr>

                <tr>
                    <th> Conta</th>
                    <th> Descrição</th>
                    <th> Fazenda</th>
                    <th> Jan</th>
                    <th> Fev</th>
                    <th> Mar</th>
                    <th> Abr</th>
                    <th> Mai</th>
                    <th> Jun</th>
                    <th> Jul</th>
                    <th> Ago</th>
                    <th> Set</th>
                    <th> Out</th>
                    <th> Nov</th>
                    <th> Dez</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>
       </table>

    </section>

    <script src="js/tabela_previsao_contas.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>
                
                
