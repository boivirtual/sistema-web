<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $local_filtro = $_POST['local'];
    $data_inicial = $_POST["data_inicial"];
    $data_final = $_POST["data_final"];
    $analitico_sintetico = $_POST["analitico_sintetico"];

    $wlote = "";
    if (isset($_POST['lote'])) {
        $descricao_lote = $_POST['lote'];

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

    $wproduto = "";
    if (isset($_POST['produto'])) {
        $produto = $_POST['produto'];

        if(in_array("", $produto)) {
            $produto='';
        }
        else {
            $produto = " AND tbl_nutricao_codigo_produto IN(";
            $produto.= implode(',', $produto);
            $produto.= ")";
            }
    }
    else {
        $wpasto='';
    }

    $_SESSION['local_nutricao']=$local_filtro;
    $_SESSION['data_inicial_nutricao']=$data_inicial; 
    $_SESSION['data_final_nutricao']=$data_final; 
?>

<?php  
    echo '<section class="panel lista_contas">';

    echo '<table class="table table-striped table-advance table-hover" id="tabela_nutricao_rel" width="100%" style="font-size: 12px;">';
                          
    echo '<tbody>';

    $tbl_nutricao = mysqli_query($conector, "SELECT * from tbl_nutricao
        INNER JOIN tbl_pessoa
                ON tbl_nutricao_codigo_local = tbl_pessoa_id
        INNER JOIN tbl_produto
                ON tbl_nutricao_codigo_produto = tbl_produto_codigo_id 
        INNER JOIN tbl_pasto
                ON tbl_nutricao_codigo_pasto = tbl_pasto_id  

        WHERE tbl_nutricao_lixeira=0 AND 
              tbl_nutricao_codigo_local='$local_filtro' AND 
              tbl_nutricao_data>='$data_inicial' AND 
              tbl_nutricao_data<='$data_final' AND 
              (tbl_nutricao_id_lote=372025 OR 
               tbl_nutricao_id_lote=422025 OR 
               tbl_nutricao_id_lote=572025 OR 
               tbl_nutricao_id_lote=702025)
          ORDER BY tbl_nutricao_id_lote DESC, tbl_nutricao_data ASC"); 
          
    /*$tbl_nutricao = mysqli_query($conector, "SELECT * from tbl_nutricao
        INNER JOIN tbl_pessoa
                ON tbl_nutricao_codigo_local = tbl_pessoa_id
        INNER JOIN tbl_produto
                ON tbl_nutricao_codigo_produto = tbl_produto_codigo_id 
        INNER JOIN tabela_unidade_produtos
                ON tab_codigo_unidade_id = tbl_produto_unidade 
        INNER JOIN tbl_apresentacao_produtos
                ON tab_codigo_apresentacao_id = tbl_produto_apresentacao 
        INNER JOIN tbl_pasto
                ON tbl_nutricao_codigo_pasto = tbl_pasto_id  

        WHERE tbl_nutricao_lixeira=0 AND 
              tbl_nutricao_codigo_local='$local_filtro' AND 
              tbl_nutricao_data>='$data_inicial' AND 
              tbl_nutricao_data<='$data_final'" . $wlote . $wproduto . $wpasto .
        " ORDER BY tbl_nutricao_id_lote DESC, tbl_nutricao_data ASC"); */

    $num_rows_nutricao = mysqli_num_rows($tbl_nutricao);

    $lote_anterior = 0;
    $descricao_pasto = '';
    $descricao_lote = '';
    $total_nutricao_dia = 0;
    $numero_dias = 0;
    $data_anterior = 0;

    if ($num_rows_nutricao!=0) {
        while ($reg_nutricao = mysqli_fetch_object($tbl_nutricao)) {
            $qtd_produto = $reg_nutricao->tbl_nutricao_quantidade_produto;
            $qtd_animais = $reg_nutricao->tbl_nutricao_qtd_animais;
            $consumo_cabeca_gramas = ($qtd_produto/$qtd_animais)*1000;
            $codigo_produto = $reg_nutricao->tbl_nutricao_codigo_produto;
            $codigo_pasto = $reg_nutricao->tbl_nutricao_codigo_pasto;
            $lote_id = $reg_nutricao->tbl_nutricao_id_lote;

            if ($lote_id!=$lote_anterior) {
                if ($lote_anterior==0) {
                    $lote_anterior=$lote_id;
                    $descricao_pasto = $reg_nutricao->tbl_pasto_descricao;
                    $descricao_lote = $reg_nutricao->tbl_nutricao_lote_pasto;

                    if ($descricao_lote=='') {
                        $descricao_lote = $reg_nutricao->tbl_pasto_descricao_lote;
                    }

                    $descricao_produto = $reg_nutricao->tbl_produto_descricao;

                    $total_nutricao_dia = $consumo_cabeca_gramas;

                    if ($reg_nutricao->tbl_nutricao_data_encerramento!='') {
                        $data_encerramento = $reg_nutricao->tbl_nutricao_data_encerramento;
                    }
                    else {
                        $data_encerramento = $reg_nutricao->tbl_nutricao_data;
                    }
                } 
                else {
                    // Imprime lote

                    $data_calculo_inicial = new DateTime($data_inicial);
                    $data_calculo_final = new DateTime($data_encerramento);
                    $intervalo = $data_calculo_inicial->diff($data_calculo_final);
                    $quantidade_dias = $intervalo->days + 1;

                    $media_consumo = $total_nutricao_dia/$quantidade_dias;
                    $consumo_edi = number_format($media_consumo, 0, ",", ".");

                    echo '<tr>';
                    echo '<td width="20%">'.$descricao_lote.'</td>';
                    echo '<td width="20%">'.$descricao_pasto.'</td>';
                    echo '<td width="20%">'.$descricao_produto.'</td>';
                    echo '<td width="20%">'.$numero_dias.'</td>';
                    echo '<td width="20%" style="text-align: right;">'.$consumo_edi.'</td>';
                    echo '</tr>';                        

                    $lote_anterior=$lote_id;
                    $descricao_pasto = $reg_nutricao->tbl_pasto_descricao;
                    $descricao_lote = $reg_nutricao->tbl_nutricao_lote_pasto;

                    if ($descricao_lote=='') {
                        $descricao_lote = $reg_nutricao->tbl_pasto_descricao_lote;
                    }

                    $descricao_produto = $reg_nutricao->tbl_produto_descricao;;

                    $total_nutricao_dia = $consumo_cabeca_gramas;

                    if ($reg_nutricao->tbl_nutricao_data_encerramento!='') {
                        $data_encerramento = $reg_nutricao->tbl_nutricao_data_encerramento;
                    }
                    else {
                        $data_encerramento = $reg_nutricao->tbl_nutricao_data;
                    }
                }
            }
            else {
                // faz contas aqui
                $total_nutricao_dia+=$consumo_cabeca_gramas;
                $numero_dias++;

                if ($reg_nutricao->tbl_nutricao_data_encerramento!='') {
                    $data_encerramento = $reg_nutricao->tbl_nutricao_data_encerramento;
                }
                else {
                    $data_encerramento = $reg_nutricao->tbl_nutricao_data;
                }
            }
        }

        // Imprime lote final do while

        $data_calculo_inicial = new DateTime($data_inicial);
        $data_calculo_final = new DateTime($data_encerramento);
        $intervalo = $data_calculo_inicial->diff($data_calculo_final);
        $quantidade_dias = $intervalo->days + 1;
        $media_consumo =$total_nutricao_dia/$quantidade_dias;
        $consumo_edi = number_format($media_consumo, 0, ",", ".");

        echo '<tr>';
        echo '<td width="20%">'.$descricao_lote.'</td>';
        echo '<td width="20%">'.$descricao_pasto.'</td>';
        echo '<td width="20%">'.$descricao_produto.'</td>';
        echo '<td width="20%">'.$numero_dias.'</td>';
        echo '<td width="20%" style="text-align: right;">'.$consumo_edi.'</td>';
        echo '</tr>';                        
    }

    mysqli_close($conector);
            
    echo '</tbody>';
    echo '<thead>';

    if ($analitico_sintetico=='S') {
        echo '
            <tr>
            <th style="vertical-align: middle;text-align:left;">Descrição do Lote</th>
            <th style="vertical-align: middle;text-align:left;">Pasto Atual</th>
            <th style="vertical-align: middle;text-align:left;">Tipo de Nutrição</th>
            <th style="vertical-align: middle;text-align:center;">Nº Dias</th>
            <th style="vertical-align: middle;text-align:center;">Média Consumo</th>
            </tr>
        ';

    }
    else {
        echo '
        <tr>
        <th style="vertical-align: middle;text-align:left;">Descrição do Lote</th>
        <th style="vertical-align: middle;text-align:left;">Pasto Atual</th>
        <th style="vertical-align: middle;text-align:left;">Tipo de Nutrição</th>
        <th colspan="31" style="vertical-align: middle;text-align:left;">Dias</th>
        <th style="vertical-align: middle;text-align:center;">Nº Dias</th>
        <th style="vertical-align: middle;text-align:center;">Média Consumo</th>
        </tr>
        ';
    }

    echo '</thead>';
    echo '</table>';
    echo '</section>';

    function calcular_dias(){
        $dias = 0;
        $consumo = 0;
        $codigo_score = 0;

    /*    $sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
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
            $consumo = intval($consumo);
        } 

        return [$dias, $consumo, $codigo_score];*/
    }

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

    echo '
        <script>    
            var table = $("#tabela_nutricao_rel").DataTable({
                responsive: true,
                paging:   false,
                ordering: true,
                info:     true,
                //order: [[ 0, "desc" ], [ 1, "desc" ] ],
                language: {
                sSearch: "Busca:",
                zeroRecords: "Nada encontrado",
                info: "Registros encontrados: _END_ ",
                infoEmpty: "Nenhum registro disponível",
                infoFiltered: "(filtrado de _MAX_ registros no total)",
                },
                "dom": "<\'row\'<\'col-lg-6 col-md-6 col-sm-6\'i><\'col-lg-6 col-md-6 col-sm-6\'f>>",
                initComplete: function() {
                    $("table.dataTable").css("width", "100%");
                  }
            });
        </script>';
?>      




