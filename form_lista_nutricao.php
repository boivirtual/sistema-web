<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $local = $_POST['local'];

    if ($local!='000000000') {
        $wlocal = " AND tbl_nutricao_codigo_local = '$local'";
    }
    else {
        $wlocal = '';
    }

    $wpasto = "";
    if (isset($_POST['pasto'])) {
        $pasto = $_POST['pasto'];

        if(in_array("", $pasto)) {
            $wpasto='';
        }
        else {
            $wpasto = " AND tbl_nutricao_codigo_pasto IN(";
            $wpasto.= implode(',', $pasto);
            $wpasto.= ")";
            }
    }
    else {
        $wpasto='';
    }

    $produto = $_POST['produto'];
    $wproduto = "";

    if ($produto!='000000000') {
        $wproduto = " AND tbl_nutricao_codigo_produto = '$produto'";
    }

    $data_inicial = $_POST["data_inicial"];
    $data_final = $_POST["data_final"];

    if ($data_inicial==0 && $data_final==0){
        $wperiodo = '';
    }
    else {
        $wperiodo = " AND tbl_nutricao_data >= '$data_inicial' AND tbl_nutricao_data <= '$data_final'";
    }

    @ session_start(); 

    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = "SELECT * FROM usuario WHERE id_usuario = '$codigo_usuario' AND 
                                           lixeira_usuario=0 ";  
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

    $_SESSION['local_nutricao']=$local;
    $_SESSION['produto_nutricao']=$produto;
    $_SESSION['data_inicial_nutricao']=$data_inicial; 
    $_SESSION['data_final_nutricao']=$data_final; 
?>

<!--<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 

</head>

<body> -->
<?php  
	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_nutricao" style="font-size: 12px">';
                          
        echo '<tbody>';
          
            $sql = "SELECT * FROM tbl_nutricao
                INNER JOIN tbl_pasto 
                        ON tbl_pasto_id = tbl_nutricao_codigo_pasto
                WHERE tbl_nutricao_lixeira=0 " . $wlocal . $wpasto . $wproduto . $wperiodo .
                " ORDER BY tbl_nutricao_data DESC";

            $rs = mysqli_query($conector, $sql); 

            //$total_produto = 0;
            //$total_cabeca = 0;
            //$qtd_media = 0;
            //$total_media = 0;

            while ($reg_nut = mysqli_fetch_object($rs)){
                $codigo_id = $reg_nut->tbl_nutricao_id;
                $codigo_local = $reg_nut->tbl_nutricao_codigo_local;
                $codigo_produto = $reg_nut->tbl_nutricao_codigo_produto;
                $codigo_pasto = $reg_nut->tbl_nutricao_codigo_pasto;
                $desc_pasto = $reg_nut->tbl_pasto_descricao;
                $lote = $reg_nut->tbl_nutricao_lote_pasto;
                $codigo_score = $reg_nut->tbl_nutricao_codigo_score_cocho; 
                $dias_consumo = $reg_nut->tbl_nutricao_dias_consumo;
                $encerrada = $reg_nut->tbl_nutricao_encerrada;
                $consumo_cabeca_dia = number_format($reg_nut->tbl_nutricao_consumo_cabeca_dia, 0, ",", ".");
                $qtd_animais = $reg_nut->tbl_nutricao_qtd_animais; 
                $qtd_produto = $reg_nut->tbl_nutricao_quantidade_produto; 
                $qtd_produto_edi = number_format($qtd_produto, 2, ",", ".");

                $data_nutricao = new DateTime($reg_nut->tbl_nutricao_data);
                $data_nutricao_edi = $data_nutricao->format('d/m/Y');
                $data_nutricao = $reg_nut->tbl_nutricao_data;

                //$qtd_media++;
                //$total_media+=$reg_nut->tbl_nutricao_media_cabeca;  

                if ($dias_consumo==999) {
                    $calculos = calcular_consumo($conector, $codigo_id, $codigo_local, $codigo_pasto, $data_nutricao, $qtd_animais, $qtd_produto);

                    $dias_consumo = $calculos[0];
                    $consumo_cabeca_dia = $calculos[1];
                }

                if ($dias_consumo==0) {
                    $consumo_cabeca = '';
                }
                else {
                    $consumo_cabeca = $consumo_cabeca_dia .' g em ' . $dias_consumo . ' dia(s)';
                }

                $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_local'");
                $num_rows = mysqli_num_rows($tbl_local);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tbl_local);
                    $desc_local = $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_local = '';
                }

                if ($lote=='') {
                    $tbl_pasto = mysqli_query($conector, "select * from tbl_pasto where tbl_pasto_id='$codigo_pasto'");
                    $num_rows = mysqli_num_rows($tbl_pasto);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_pasto);
                        $lote = $reg->tbl_pasto_observacao;

                    }
                    else {
                        $lote = '';
                    }
                }

                $tbl_produto = mysqli_query($conector, "select * from tbl_produto where tbl_produto_codigo_id='$codigo_produto'");
                $num_rows = mysqli_num_rows($tbl_produto);

                if ($num_rows!=0){
                    $reg_prod = mysqli_fetch_object($tbl_produto);
                   
                    $qtd_apresentacao = $reg_prod->tbl_produto_qtd_unidade; 
                    $codigo_unidade = $reg_prod->tbl_produto_unidade;
                    $codigo_apresentacao = $reg_prod->tbl_produto_apresentacao;

                    $tab_unidade = mysqli_query($conector, "select * from tabela_unidade_produtos where tab_codigo_unidade_id='$codigo_unidade'");
                    $num_rows = mysqli_num_rows($tab_unidade);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_unidade);
                        $simbolo_unidade = $reg->tab_codigo_unidade_produtos;
                    }
                    else {
                        $simbolo_unidade = '';
                    }

                    $tab_apresentacao = mysqli_query($conector, "select * from tbl_apresentacao_produtos where tab_codigo_apresentacao_id='$codigo_apresentacao'");
                    $num_rows = mysqli_num_rows($tab_apresentacao);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_apresentacao);
                        $desc_apresentacao = $reg->tab_descricao_apresentacao_produtos;
                    }
                    else {
                        $desc_apresentacao = '';
                    }

                    $apresentacao = $desc_apresentacao . ' ' . number_format($qtd_apresentacao, 2, ",", ".") . ' ' . $simbolo_unidade;

                    $desc_produto = $reg_prod->tbl_produto_descricao . ' ' . $apresentacao;
                }
                else {
                    $desc_produto = '';
                    $simbolo_unidade = '';
                    $desc_apresentacao = '';
                }

                /*if ($produto!='000000000') {
                    $total_produto+= $reg_nut->tbl_nutricao_quantidade_produto;
                    $total_cabeca+= $reg_nut->tbl_nutricao_qtd_animais;
                    $unidade_total = $simbolo_unidade;
                }*/

                $tbl_score = mysqli_query($conector, "select * from tbl_score_cocho where tbl_score_id='$codigo_score'");
                $num_rows = mysqli_num_rows($tbl_score);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tbl_score);
                    $desc_score = $reg->tbl_score_descricao;
                }
                else {
                    $desc_score = '';
                }

                if ($encerrada=='S') {
                    $desc_score = 'Nutrição encerrada';
                }

                $incluido_em=new DateTime($reg_nut->tbl_nutricao_incluido_em);
                $incluido_por=$reg_nut->tbl_nutricao_incluido_por; 
                $alterado_em=new DateTime($reg_nut->tbl_nutricao_alterado_em);
                $alterado_por=$reg_nut->tbl_nutricao_alterado_por; 

                $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
                $alterado_em_edi = $alterado_em->format('d/m/Y H:i:s');

                $array_animal = array(
                        $codigo_id,
                        $codigo_local,
                        $desc_local,
                        $codigo_pasto,
                        $desc_pasto,
                        $desc_produto,
                        $simbolo_unidade,
                        $reg_nut->tbl_nutricao_quantidade_produto,
                        $reg_nut->tbl_nutricao_media_cabeca,
                        $qtd_animais,
                        $lote,
                        $desc_score,
                        $incluido_em_edi,
                        $incluido_por,
                        $alterado_em_edi,
                        $alterado_por,
                        $codigo_produto
                );   
                                    
                $string_array = implode('|', $array_animal);

                echo "<tr>";
                echo "<td hidden width='8%'>".$data_nutricao."</td>";
                echo "<td width='8%'>".$data_nutricao_edi."</td>";
                echo "<td width='10%'>".$desc_pasto."</td>";
                echo "<td width='16%'>".$desc_produto."</td>";
                echo "<td style='vertical-align: middle;text-align:center;' width='8%'>".$consumo_cabeca."</td>";
                echo "<td align='center' width='8%'>".$qtd_produto_edi." ".$simbolo_unidade."</td>";
                echo "<td align='center' width='8%'>".$qtd_animais."</td>";
                echo "<td width='13%'>".$lote."</td>";
                echo "<td width='13%'>".$desc_score."</td>";
                echo "<td align='center' width='8%'>";    
                echo "<div class='btn-group'>";
                echo "<a class='btn' href='#'>
                    <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='excluir_nutricao(\"{$string_array}\",2)'>
               </i></a>";
                echo "</div>";
                echo "</td>";

            }                    
            mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>';

            /*if ($total_produto!=0) {
                $total_media = $total_media/$qtd_media;

                $total_produto = number_format($total_produto, 2, ",", ".");
                $total_media = number_format($total_media, 2, ",", "."); 

                echo '
                    <tr>
                        <div class="row" id="total_contas">
                            <div class="form-group col-md-2">
                                <label class="control-label">Qtd Total</label>
                                <input class="form-control" type="text" readonly="" value="'.$total_produto.' '.$unidade_total.'">
                            </div>

                            <div class="form-group col-md-2">
                                <label class="control-label">Qtd/Cabeças</label>
                                <input class="form-control" type="text" readonly="" value="'.$total_media.' '.$unidade_total.'">
                            </div>
                        </div>
                    </tr>
                ';
            }*/

            echo '  <tr>
                    <th hidden></th>
                    <th style="vertical-align: middle;text-align:center;">Data</th>
                    <th style="vertical-align: middle;text-align:center;">Pasto</th>
                    <th style="vertical-align: middle;text-align:center;">Produto</th>
                    <th style="vertical-align: middle;text-align:center;">Consumo Cabeça g/dia</th>
                    <th style="vertical-align: middle;text-align:center;">Quantidade Colocada no Cocho (Kg)</th>
                    <th style="vertical-align: middle;text-align:center;">Nº de Cabeças</th>
                    <th style="vertical-align: middle;text-align:center;">Lote</th>
                    <th style="vertical-align: middle;text-align:center;">Score do Cocho</th>
                    <th style="vertical-align: middle;text-align:center;"><i class="icon_cogs"></i> Ações</th>
                    </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

    function calcular_consumo($conector, $codigo_id, $codigo_local, $codigo_pasto, $data_nutricao, $qtd_animais, $qtd_produto ){
        $dias = 0;
        $consumo = 0;

        $sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
            WHERE tbl_nutricao_lixeira = 0 AND 
                  tbl_nutricao_codigo_local = '$codigo_local' AND 
                  tbl_nutricao_codigo_pasto = '$codigo_pasto' AND 
                  tbl_nutricao_data > '$data_nutricao' 
            ORDER BY tbl_nutricao_data ASC"); 

        $num_rows = mysqli_num_rows($sql);

        if ($num_rows>0) {
            $reg = mysqli_fetch_object($sql);
            $data_posterior = $reg->tbl_nutricao_data;

            $firstDate  = new DateTime($data_nutricao);
            $secondDate = new DateTime($data_posterior);
            $intvl = $firstDate->diff($secondDate);
            $dias = $intvl->days;

            if ($dias==0) {
                $dias = 1;
            }

            $consumo = ($qtd_produto/$qtd_animais/$dias)*1000;

            $sql = ("UPDATE tbl_nutricao SET
                tbl_nutricao_dias_consumo='$dias',
                tbl_nutricao_consumo_cabeca_dia='$consumo'
                WHERE tbl_nutricao_id='$codigo_id'");

            $resultado = mysqli_query($conector,$sql);

            $consumo = number_format($consumo, 0, ",", ".");
        }

        return [$dias, $consumo];

    }

    echo '<script src="js/nutricao.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>';

?>
<!--    <script src="js/nutricao.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html> -->

                
                
