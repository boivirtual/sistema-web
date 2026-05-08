<?php
    include "conecta_mysql.inc";

    $id_animal = $_POST['id_animal'];
    $id_local = $_POST['id_local'];
    $id_estacao = $_POST['id_estacao'];

	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" style="font-size: 11px" id="tabela_cobertura">';
                          
    echo '<tbody>'; 
  
    $tbl_cobertura = mysqli_query($conector, "select * from tbl_item_cobertura
               inner join tbl_cobertura
                       on tbl_ite_cobertura_numero_id = tbl_cobertura_id
               inner join tbl_protocoloiatf
                       on tbl_protocoloiatf_id = tbl_cobertura_protocoloiatf   
                    where tbl_cobertura_lixeira=0 and 
                          tbl_cobertura_controle='C' and 
                          tbl_ite_cobertura_codigo_id_animal = '$id_animal' and 
                          tbl_ite_cobertura_numero_cobertura !=0 and 
                          tbl_cobertura_codigo_estacao_monta='$id_estacao'
                    order by tbl_ite_cobertura_numero_id DESC");  

                    //order by tbl_ite_cobertura_numero_cobertura DESC");  

    $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);
    $numero_cobertura = $num_rows_cobertura;

    if ($num_rows_cobertura!=0) {
        while ($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){

            $negativo_em = '';
            $negativo_por = '';

            $cobertura = $reg_cobertura->tbl_cobertura_id;
            $protocolo = $reg_cobertura->tbl_cobertura_protocoloiatf;

            $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura'");
            $num_rows_protocolo = mysqli_num_rows($sql);
            $reg_protocolo_cobertura = mysqli_fetch_object($sql);

            $tbl_item_iatf = mysqli_query($conector, "select * from tbl_item_protocoloiatf where tbl_ite_protocoloiatf_protocolo_id='$protocolo'
                order by tbl_ite_protocoloiatf_id ASC");
            $qtd_item_iatf = mysqli_num_rows($tbl_item_iatf);

            $tem_inseminacao = '';
            
            while ($reg_itens_iatf = mysqli_fetch_object($tbl_item_iatf)) {
                $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);

                $data = date("d/m/Y", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

                $data_inseminacao = date("Y-m-d", strtotime(str_replace('/', '-', $data)));
            }

            $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

            $tem_d0 = $reg_cobertura->tbl_ite_cobertura_dia_1;

            if ($qtd_item_iatf==2) {
                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_2;
            }

            if ($qtd_item_iatf==3) {
                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_3;
            }

            if ($qtd_item_iatf==4) {
                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_4;
            }

            if ($qtd_item_iatf==5) {
                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_5;
            }

            if ($qtd_item_iatf==6) {
                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_6;
            }

            if ($tem_inseminacao=='S' && $diagnostico!='P' && $diagnostico!='N') {
                $desc_diagnostico = 'Aguardando Diagnostico';
            }
            else if ($diagnostico=='P') {
                $desc_diagnostico = 'Diagnostico Positivo';
            }
            else if ($diagnostico=='N'){
                $desc_diagnostico = 'Diagnostico Negativo';

                if ($reg_cobertura->tbl_ite_cobertura_negativo_por!='') {
                    $negativo_em = date("d/m/Y", strtotime($reg_cobertura->tbl_ite_cobertura_negativo_em));
                    $negativo_por = $reg_cobertura->tbl_ite_cobertura_negativo_por;
                }
            }
            else {
                $desc_diagnostico = 'Aguardando Inseminação';
            } 

            $id_touro_semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

            $tbl_semen = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$id_touro_semen'");
            $num_rows = mysqli_num_rows($tbl_semen);

            if ($num_rows!=0) {
                $reg_touro_semen = mysqli_fetch_object($tbl_semen);

                if ($reg_touro_semen->tbl_semem_nome!='') {
                    $desc_touro_semen = $reg_touro_semen->tbl_semem_nome .'-'.
                                        $reg_touro_semen->tbl_semem_nome;
                }
                else {
                    $desc_touro_semen = $reg_touro_semen->tbl_semem_nome;
                }
            }
            else {
                $tbl_touro = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$id_touro_semen'");
                $num_rows = mysqli_num_rows($tbl_touro);

                if ($num_rows!=0) {
                    $reg_touro_semen = mysqli_fetch_object($tbl_touro);

                    if ($reg_touro_semen->tbl_animal_codigo_alfa!='') {
                        $desc_touro_semen = $reg_touro_semen->tbl_animal_codigo_alfa .'-'.
                                            $reg_touro_semen->tbl_animal_codigo_numerico;
                    }
                    else {
                        $desc_touro_semen = $reg_touro_semen->tbl_animal_codigo_numerico;
                    }
                }
                else {
                    $desc_touro_semen = '';
                }
            }

            if ($tem_d0=='S') {
                echo "<tr style='font-size: 10px;'>";
                echo "<td class='numero_cobertura' width='8%'>".$numero_cobertura."</td>";
                echo "<td width='10%'>".$data."</td>";
                echo "<td width='32%'>".$desc_touro_semen."</td>";
                echo "<td width='30%'>".$desc_diagnostico."</td>";
                echo "<td style='color: red;' width='10%'>".$negativo_em."</td>";
                echo "<td style='color: red;' width='10%'>".$negativo_por."</td>";
                echo "</tr>";
            }

            $numero_cobertura--;
        } 
    }

    $tbl_cobertura = mysqli_query($conector, "select * from tbl_historico_monta_natural
                    where tbl_historico_monta_codigo_id_mae = '$id_animal'
                    order by tbl_historico_monta_data_diagnostico DESC");  

    $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);
    $numero_cobertura = $num_rows_cobertura;

    if ($num_rows_cobertura!=0) {
        while ($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){

            $desc_diagnostico = 'Diagnostico Positivo';
            $desc_touro_semen = 'Monta Natural';
            $data = date("d/m/Y", strtotime($reg_cobertura->tbl_historico_monta_data_diagnostico));

            echo "<tr style='font-size: 10px;'>";
            echo "<td width='8%'></td>";
            echo "<td width='10%'>".$data."</td>";
            echo "<td width='30%'>".$desc_touro_semen."</td>";
            echo "<td width='32%'>".$desc_diagnostico."</td>";
            echo "<td width='10%'></td>";
            echo "<td width='10%'></td>";
            echo "</tr>";
        } 
    }

    mysqli_close($conector);
            
    echo '</tbody>';
    echo '</table>';

    echo '</section>';
?>

                
                
