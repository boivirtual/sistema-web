<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $local = $_POST['local'];
    $data_inicial = $_POST["data_inicial"];
    $data_final = $_POST["data_final"];

    $wlote = "";
    if (isset($_POST['descricao_lote'])) {
        $descricao_lote = $_POST['descricao_lote'];

        if(in_array("", $descricao_lote)) {
            $wlote='';
        }
        else {
            $wlote = " AND tbl_nutricao_id_lote IN(";
            $wlote.= implode(',', $descricao_lote);
            $wlote.= ")";
        }
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

    if ($data_inicial=='' && $data_final==''){
        $wperiodo = '';
    }
    else {
        $wperiodo = " AND tbl_nutricao_data >= '$data_inicial' AND tbl_nutricao_data <= '$data_final'";
    }

    $_SESSION['local_nutricao']=$local;
    $_SESSION['data_inicial_nutricao']=$data_inicial; 
    $_SESSION['data_final_nutricao']=$data_final; 
?>

<?php  
	echo '<section class="panel lista_contas">';

    echo '<table class="table table-striped table-advance table-hover" id="tabela_nutricao" style="font-size: 12px">';
                          
        echo '<tbody>';
          
            $sql = "SELECT * FROM tbl_nutricao
                INNER JOIN tbl_pasto 
                        ON tbl_pasto_id = tbl_nutricao_codigo_pasto
                WHERE tbl_nutricao_lixeira=0 AND 
                      tbl_nutricao_codigo_local = '$local'" . $wperiodo . $wlote . $wpasto . $wproduto .
                "ORDER BY tbl_nutricao_id_lote DESC";

            //ORDER BY tbl_nutricao_data DESC, tbl_nutricao_id DESC
            $rs = mysqli_query($conector, $sql); 

            while ($reg_nut = mysqli_fetch_object($rs)){
                $codigo_id = $reg_nut->tbl_nutricao_id;
                $codigo_local = $reg_nut->tbl_nutricao_codigo_local;
                $codigo_produto = $reg_nut->tbl_nutricao_codigo_produto;
                $codigo_pasto = $reg_nut->tbl_nutricao_codigo_pasto;
                $desc_pasto = $reg_nut->tbl_pasto_descricao;
                $lote = $reg_nut->tbl_nutricao_lote_pasto;
                $id_lote = $reg_nut->tbl_nutricao_id_lote;
                
                $dias_consumo = $reg_nut->tbl_nutricao_dias_consumo;
                $encerrada = $reg_nut->tbl_nutricao_encerrada;
                $consumo_cabeca_dia = $reg_nut->tbl_nutricao_consumo_cabeca_dia;

                $consumo_cabeca_dia = number_format($consumo_cabeca_dia, 0, ",", ".");

                $qtd_animais = intval($reg_nut->tbl_nutricao_qtd_animais); 
                $qtd_produto = $reg_nut->tbl_nutricao_quantidade_produto; 
                $qtd_produto_edi = number_format($qtd_produto, 2, ",", ".");

                $data_nutricao = new DateTime($reg_nut->tbl_nutricao_data);
                $data_nutricao_edi = $data_nutricao->format('d/m/Y');
                $data_nutricao = $reg_nut->tbl_nutricao_data;

                $consumo_cabeca = '';

                if ($encerrada=='S') {
                    $desc_score = 'Nutrição encerrada';
                    $consumo_cabeca = $consumo_cabeca_dia .' g em ' . $dias_consumo . ' dia(s)';
                }
                else {
                    $calculos = calcular_consumo($conector, $codigo_id, $codigo_local, $id_lote, $data_nutricao, $qtd_animais, $qtd_produto);

                    $dias_consumo = $calculos[0];
                    $consumo_cabeca_dia = number_format($calculos[1], 0, ",", ".");

                    $codigo_score = $calculos[2];

                    if ($dias_consumo==0) {
                        $consumo_cabeca = '';
                    }
                    else {
                        $consumo_cabeca = $consumo_cabeca_dia .' g em ' . $dias_consumo . ' dia(s)';
                    }

                    $tbl_score = mysqli_query($conector, "select * from tbl_score_cocho where tbl_score_id='$codigo_score'");
                    $num_rows = mysqli_num_rows($tbl_score);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_score);
                        $desc_score = $reg->tbl_score_descricao;
                    }
                    else {
                        $desc_score = '';
                    }
                }

                $qtd_cabeca = ($qtd_produto / $qtd_animais)*1000;

                $qtd_cabeca_edi = number_format($qtd_cabeca, 2, ",", ".").' g';

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
                        $lote = $reg->tbl_pasto_descricao_lote;

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

                    //$desc_produto = $reg_prod->tbl_produto_descricao . ' ' . $apresentacao;
                    $desc_produto = $reg_prod->tbl_produto_descricao;
                }
                else {
                    $desc_produto = '';
                    $simbolo_unidade = '';
                    $desc_apresentacao = '';
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
                        $codigo_produto,
                        $id_lote,
                        $data_nutricao
                );   
                                    
                $string_array = implode('|', $array_animal);

                echo "<tr>";
                echo "<td width='8%'>".$id_lote."</td>";
                echo "<td width='8%'>".$data_nutricao_edi."</td>";
                echo "<td width='13%'>".$lote."</td>";
                echo "<td align='center' width='8%'>".$qtd_animais."</td>";
                echo "<td width='14%'>".$desc_pasto."</td>";
                echo "<td width='14%'>".$desc_produto."</td>";
                echo "<td align='center' width='8%'>".$qtd_produto_edi." ".$simbolo_unidade."</td>";

                    echo "<td style='vertical-align: middle;text-align:center;' width='8%'>";
                    echo $qtd_cabeca_edi; 
                    echo "</td>";                

                if ($consumo_cabeca=='') {
                    echo "<td style='vertical-align: middle;text-align:center;' width='15%'></td>";                
                }
                else {
                    echo "<td style='vertical-align: middle;text-align:center;' width='15%'>";
                    echo $consumo_cabeca; // Mantém o valor original
                    echo "<i class='icon_info_alt btn' data-toggle='tooltip' data-placement='left' title='Cocho: ".$desc_score."' style='font-size: 10px;'></i>"; // Ícone com o tooltip
                    echo "</td>";                
                }

                echo "<td align='center' width='8%'>"; 
                echo "<div class='btn-group'>";

                if ($consumo_cabeca=='') {
                    echo "<a class='btn' href='#'>
                        <i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Encerrar Nutrição' onClick='encerrar_nutricao(\"{$string_array}\")' style='font-size: 12px;'>
                   </i></a>";

                    echo "<a class='btn' href='#'>
                        <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='excluir_nutricao(\"{$string_array}\",2)' style='font-size: 12px;'>
                   </i></a>";
                }
                else {
                    echo "<a class='btn' href='#'>
                        <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='excluir_nutricao(\"{$string_array}\",2)' style='font-size: 12px;'>
                   </i></a>";
                }
                echo "</div>";
                echo "</td>";

            }                    
            mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>';

            echo '  <tr>
                    <th style="vertical-align: middle;text-align:center;"><i class="fa fa-sort-numeric-desc"></i></th>
                    <th style="vertical-align: middle;text-align:center;">Data</th>
                    <th style="vertical-align: middle;text-align:center;">Descrição do Lote</th>
                    <th style="vertical-align: middle;text-align:center;">Nº de Cabeças</th>
                    <th style="vertical-align: middle;text-align:center;">Pasto</th>
                    <th style="vertical-align: middle;text-align:center;">Produto</th>
                    <th style="vertical-align: middle;text-align:center;">Quantidade Colocada no Cocho (Kg)</th>
                    <th style="vertical-align: middle;text-align:center;">Qtde/Cabeça</th>
                    <th style="vertical-align: middle;text-align:center;">Consumo Cabeça g/dia</th>
                    <th style="vertical-align: middle;text-align:center;"><i class="icon_cogs"></i> Ações</th>
                    </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

    function calcular_consumo($conector, $codigo_id, $codigo_local, $id_lote, $data_nutricao, $qtd_animais, $qtd_produto ){
        $dias = 0;
        $consumo = 0;
        $codigo_score = 0;

        $sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
            WHERE tbl_nutricao_lixeira = 0 AND 
                  tbl_nutricao_codigo_local = '$codigo_local' AND 
                  tbl_nutricao_id_lote = '$id_lote' AND 
                  tbl_nutricao_data > '$data_nutricao' 
            ORDER BY tbl_nutricao_data ASC"); 

        $num_rows = mysqli_num_rows($sql);

        if ($num_rows>0) {
            $reg = mysqli_fetch_object($sql);
            $data_posterior = $reg->tbl_nutricao_data;
            $codigo_score = $reg->tbl_nutricao_codigo_score_cocho; 
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

            //$consumo = number_format($consumo, 0, ",", ".");
            //$consumo = intval($consumo);
        } 

        return [$dias, $consumo, $codigo_score];
    }

    function busca_score($conector, $codigo_local, $id_lote, $data_nutricao) {
        $sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
            WHERE tbl_nutricao_lixeira = 0 AND 
                  tbl_nutricao_codigo_local = '$codigo_local' AND 
                  tbl_nutricao_id_lote = '$id_lote' 
            ORDER BY tbl_nutricao_data ASC"); 

        $num_rows = mysqli_num_rows($sql);

        if ($num_rows>0) {
            while ($reg = mysqli_fetch_object($sql)){
                $data_anterior = $reg->tbl_nutricao_data;

                if ($data_anterior<=$data_nutricao) {
                    $codigo_score = $reg->tbl_nutricao_codigo_score_cocho; 
                }
            }
        }
        else {
            $codigo_score = 0;
        }

        return $codigo_score;
    }

    echo '<script 
            src="js/nutricao.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript">
            </script>';
    echo '
        <script>
            $(document).ready(function(){
                $("[data-toggle=\'tooltip\']").tooltip();
            });
        </script>';

    echo "
        <script>
            if (jQuery('#sidebar > ul').is(':visible') === true) {
                jQuery('#main-content').css({
                    'margin-left': '0px'
                });
                jQuery('#sidebar').css({
                    'margin-left': '-180px'
                });
                jQuery('#sidebar > ul').hide();
                jQuery('#container').addClass('sidebar-closed');
            }
        </script>";

?>      
