<?php
// DIAGNOSTICO MONTA
include "conecta_mysql.inc";
$data_sistema = date("Y-m-d");

$codigo_local = $_POST["local"];
$previsao_parto_de = $_POST["previsao_parto_de"];
$previsao_parto_ate = $_POST["previsao_parto_ate"];
$diagnostico = $_POST["diagnostico"];
$periodo_de = $_POST["filtro_periodo_de"];
$periodo_ate = $_POST["filtro_periodo_ate"];
$opcao_monta = $_POST["opcao_monta"];

/*if ($previsao_parto_de=='') {
    $previsao_parto_de = '0000-00-00';
    $previsao_parto_ate = '9999-12-31';
}

if ($periodo_de=='') {
    $periodo_de = '0000-00-00';
    $periodo_ate = '9999-12-31';
}*/

$wsituacao  = "";
if (isset($_POST['situacao'])) {
    $situacao  = $_POST['situacao'];

    if(in_array("",  $situacao)) {
        $wsituacao ='';
    }
    else {
        $array_situacao = implode(',', $situacao);
        $array_situacao = explode(',', $array_situacao);

        if ($array_situacao[0]==' ') {
            $wsituacao  = " AND (tbl_ite_cobertura_nascido IN(";
            for ($i=0; $i < count($array_situacao); $i++) { 
                $wsituacao .= "'".$array_situacao[$i]."',";
            }
            $wsituacao = substr($wsituacao,0, -1);
            $wsituacao .= ") OR tbl_ite_cobertura_nascido IS NULL) ";
        }
        else {
            $wsituacao  = " AND tbl_ite_cobertura_nascido IN(";
            for ($i=0; $i < count($array_situacao); $i++) { 
                $wsituacao .= "'".$array_situacao[$i]."',";
            }
            $wsituacao = substr($wsituacao,0, -1);
            $wsituacao .= ") ";
        }
    }
}

$tipo_cobertura = $_POST['tipo_cobertura'];

$_SESSION['cobertura_controle']=$tipo_cobertura;
$_SESSION['local_cobertura_diagnostico']=$codigo_local;


$tem_thead = 'N';

/*$tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais 
    WHERE tbl_animal_codigo_id = 217 OR tbl_animal_codigo_id = 245 OR 
    tbl_animal_codigo_id = 5392
    ORDER BY tbl_animal_codigo_id ASC");*/


$tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais
    WHERE tbl_animal_sexo = 'F'
    ORDER BY tbl_animal_codigo_numerico ASC");

while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
    $codigo_id = $reg_animal->tbl_animal_codigo_id;
    $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
    $codigo_numerico = intval($reg_animal->tbl_animal_codigo_numerico);

    $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
    $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
    $descarte_reproducao = $reg_animal->tbl_animal_descarte_reproducao; 
    $data_baixa = $reg_animal->tbl_animal_baixado_em;
    $ativo = $reg_animal->tbl_animal_ativo;

    if ($descarte_reproducao=='S') {
        $descarte = 'Sim';
    }
    else {
        $descarte = '';
    }

    // No Diagnostico Monta o periodo será pela DATA DA PRENHEZ conforme reuniao do trello
    // Cartão: MELHORIA NA REPRODUÇÃO (PARA DEPOIS DA ESTAÇÃO 24/2025)
    // Cheklist: AJUSTE REUNIAO 16/06/2025

    if ($opcao_monta=='I') {
        $sql =  "SELECT * FROM tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                  tbl_cobertura_codigo_local = '$codigo_local' AND 
                  tbl_ite_cobertura_previsao_parto is null AND
                  tbl_cobertura_controle = 'M' AND 
                  (tbl_ite_cobertura_resultado_diagnostico='' OR 
                   tbl_ite_cobertura_resultado_diagnostico is null) AND 
                  (tbl_ite_cobertura_nascido='' OR 
                   tbl_ite_cobertura_nascido is null)" . $wsituacao; 
    }
    else {
        if ($diagnostico=='P') {
            if ($periodo_de!='') {
                $sql =  "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                    WHERE tbl_cobertura_lixeira=0 AND
                          tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                          tbl_cobertura_codigo_local = '$codigo_local' AND 
                          tbl_cobertura_controle = 'M' AND
                          tbl_ite_cobertura_data_prenhes>='$periodo_de' AND
                          tbl_ite_cobertura_data_prenhes<='$periodo_ate' AND 
                          tbl_ite_cobertura_resultado_diagnostico='P'" . 
                          $wsituacao; 
            }
            else {
                $sql =  "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                    WHERE tbl_cobertura_lixeira=0 AND
                          tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                          tbl_cobertura_codigo_local = '$codigo_local' AND 
                          tbl_cobertura_controle = 'M' AND
                          tbl_ite_cobertura_resultado_diagnostico='P' AND 
                          tbl_ite_cobertura_previsao_parto>='$previsao_parto_de' AND 
                          tbl_ite_cobertura_previsao_parto<='$previsao_parto_ate'" . 
                          $wsituacao; 
            }
        }
        else {
            $sql =  "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                      tbl_cobertura_codigo_local = '$codigo_local' AND 
                      tbl_cobertura_controle = 'M' AND 
                      DATE(tbl_ite_cobertura_negativo_em)>='$periodo_de' AND
                      DATE(tbl_ite_cobertura_negativo_em)<='$periodo_ate' AND 
                      tbl_ite_cobertura_resultado_diagnostico='N'"; 
        }
    }

    $tbl_cobertura = mysqli_query($conector, $sql);
    $num_rows_coberturas = mysqli_num_rows($tbl_cobertura);

        if ($tem_thead == 'N'){
            echo '<section class="panel-group lista_contas">
                    <form method="POST" action="#" enctype="multipart/form-data">';

            echo '<div class="row">
                  <div class="form-group col-md-12">'; 

            echo '<button type="button" class="btn btn-success pull-right excel" style="margin-right: 6px;" 
                    onClick="listar_femeas_servidas_monta_excel()">Excel</button>
                </div>
                </div>';

            echo '<table class="table table-striped table-advance table-hover" id="tabela_femeas_servidas_monta" width="100%" style="font-size: 12px;">';

            if ($opcao_monta=='I') {
                echo "
                <thead>
                    <tr>
                        <th hidden></th>
                        <th><i class='fa fa-sort-alpha-asc'></i></th>
                        <th> Nº Fêmea</th>
                        <th> Raça</th>
                        <th style='text-align: center;'> Idade (meses)</th>
                        <th style='text-align: center;'> Nº Partos</th>
                        <th style='text-align: center;'> Nº Abortos</th>
                        <th style='text-align: center;'> Data Prenhes</th>
                        <th style='text-align: center;'> Previsão do Parto</th>
                        <th style='text-align: center;'> Confirmar Diagnóstico</th>
                        <th style='text-align: center;'></th>
                        <th style='text-align: center;'></th>
                    </tr>
                </thead>";
            }
            else {
                if ($diagnostico=='P') {
                    echo "
                    <thead>
                        <tr>
                            <th hidden></th>
                            <th><i class='fa fa-sort-alpha-asc'></i></th>
                            <th> Nº Fêmea</th>
                            <th> Raça</th>
                            <th style='text-align: center;'> Idade (meses)</th>
                            <th style='text-align: center;'> Nº Partos</th>
                            <th style='text-align: center;'> Nº Abortos</th>
                            <th style='text-align: center;'> Data Prenhes</th>
                            <th style='text-align: center;'> Previsão do Parto</th>
                            <th style='text-align: center;'> Confirmar Diagnóstico</th>
                            <th style='text-align: center;'> Nascido</th>
                            <th style='text-align: center;'> Descarte</th>
                        </tr>
                    </thead>";
                }
                else {
                    echo "
                    <thead>
                        <tr>
                            <th hidden></th>
                            <th><i class='fa fa-sort-alpha-asc'></i></th>
                            <th> Nº Fêmea</th>
                            <th> Raça</th>
                            <th style='text-align: center;'> Idade (meses)</th>
                            <th style='text-align: center;'> Nº Partos</th>
                            <th style='text-align: center;'> Nº Abortos</th>
                            <th style='text-align: center;'> Negativo em</th>
                            <th style='text-align: center;'> Descarte</th>
                            <th style='text-align: center;'> </th>
                            <th style='text-align: center;'> </th>
                            <th style='text-align: center;'> </th>
                        </tr>
                    </thead>";
                }
            }

            $tem_thead = 'S';
            echo '<tbody>';
        }

        if ($num_rows_coberturas > 0) {
            while($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){
                $cobertura_id=$reg_cobertura->tbl_cobertura_id;
                $numero_item=$reg_cobertura->tbl_ite_cobertura_numero_item;
                $cobertura_ordem = $cobertura_id . $numero_item;

                $diagnostico=$reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                $nascido=$reg_cobertura->tbl_ite_cobertura_nascido;
                $nascido_outro = $reg_cobertura->tbl_ite_cobertura_situacao_femea_nascido_outro;

                $data_prenhes = $reg_cobertura->tbl_ite_cobertura_data_prenhes;
                $data_previsao_parto = $reg_cobertura->tbl_ite_cobertura_previsao_parto;
                $data_negativo = $reg_cobertura->tbl_ite_cobertura_negativo_em;

                if ($data_negativo!='') {
                    $data_negativo_edi = date("d/m/Y", strtotime(str_replace('-', '/', $data_negativo)));
                }
                else {
                    $data_negativo_edi = '';
                }

                if ($data_prenhes!='') {
                    $data_prenhes_edi = date("d/m/Y", strtotime(str_replace('-', '/', $data_prenhes)));
                }
                else {
                    $data_prenhes_edi = '';
                }

                if ($data_previsao_parto!='') {
                    $data_previsao_parto_edi = date("d/m/Y", strtotime(str_replace('-', '/', $data_previsao_parto)));
                }
                else {
                    $data_previsao_parto_edi = '';
                }

                if ($data_prenhes!='') {
                    $data_acompanhamento_calculo = date("Y-m-d", strtotime($reg_cobertura->tbl_ite_cobertura_data_prenhes));
                }
                else {
                    $data_acompanhamento_calculo = date("Y-m-d");
                }

                switch ($nascido) {
                    case 'N':
                        $desc_nascido = 'Sim';
                        break;
                    case 'A':
                        $desc_nascido = 'Aborto';
                        break;
                    case 'M':
                        $desc_nascido = 'Natimorto';
                        break;
                    case 'O':
                       $desc_nascido = 'Outro';
                        break;
                    default:
                       $desc_nascido = '';
                       break;
                }

                if ($desc_nascido == 'Outro') {
                    switch ($nascido_outro) {
                        case 'V':
                            $desc_nascido = 'Fêmea Vendida';
                            break;
                        case 'M':
                            $desc_nascido = 'Fêmea Morreu';
                            break;
                        case 'O':
                           $desc_nascido = 'Fêmea Outra Saída';
                            break;
                        default:
                           $desc_nascido = 'Fêmea Outra Saída';
                           break;
                    }
                }

                $date = new DateTime($data_nascimento);
                                
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                $tbl_raca = mysqli_query($conector, "SELECT * FROM tabela_racas 
                    WHERE tab_codigo_raca='$codigo_raca' AND 
                          tab_registro_lixeira_raca=0");  

                $num_rows = mysqli_num_rows($tbl_raca);

                if ($num_rows!=0){
                    $reg_raca = mysqli_fetch_object($tbl_raca);
                    $desc_raca = $reg_raca->tab_descricao_raca;
                }
                else {
                    $desc_raca = '';
                }

                $numero_partos = 0;
                $num_abortos = 0;

                // primeiro verifica quantos partos
                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo_id'");

                $numero_partos = mysqli_num_rows($tbl_filhos);

                // verifica abortos/absorsão
                $tbl_aborto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                    where tbl_mov_estoque_codigo_mae='$codigo_id' and 
                          tbl_mov_estoque_entrada_saida='A'");

                $num_abortos = mysqli_num_rows($tbl_aborto);

                echo "<tr>";
                echo "<td hidden=''><input type='text' 
                    id='codigo_id$cobertura_id' value='$codigo_id'></td>";

                echo "<td align='right' width='4%'><input type='hidden' 
                    id='codigo_alfa_femea$cobertura_id' value='$codigo_alfa'>".$codigo_alfa."</td>";

                echo "<td align='left' width='6%'><input type='hidden' 
                    id='codigo_num_femea$cobertura_id' value='$codigo_numerico'>".$codigo_numerico."</td>";
                echo "<td width='10%'>".$desc_raca."</td>";
                echo "<td align='center' width='8%'>".$idade."</td>";
                echo "<td align='center' width='8%'>".$numero_partos."</td>";
                echo "<td align='center' width='8%'>".$num_abortos."</td>";

                if ($nascido == '') {
                    if ($diagnostico=='P') {
                        echo "<td align='center' width='11%'><input type='date' name='data_prenhes$cobertura_id' 
                                id='data_prenhes$cobertura_id' class='form-control
                                input-sm' value='$data_prenhes' 
                                onkeypress='return desabilita_enter(this, event)' 
                                onchange='prenhes_limpa_positiva(this.id, 
                                this.value), calcular_data_previsao(this.id, this.value)'></td>";

                        echo "<td align='center' width='11%'><input type='date' name='data_previsao$cobertura_id' id='data_previsao$cobertura_id' class='form-control input-sm' value='$data_previsao_parto' onkeypress='return desabilita_enter(this, event)' onchange='previsao_limpa_positiva(this.id, this.value), calcular_data_prenhes(this.id, this.value)'></td>";

                        echo "<td align='center' width='17%'>
                        <label><input type='radio' 
                        name='resultado$cobertura_id' 
                        id='resultadoP$cobertura_id' value='P' checked
                        onclick='gravar_diagnostico_positivo_femeas_servidas_monta(this.id, this.value)'> Positivo</label>&nbsp;&nbsp;
                                
                        <label><input type='radio'
                        name='resultado$cobertura_id' 
                        id='resultadoN$cobertura_id' value='N' onclick='resultadoCoberturaMonta(
                        this.id,this.value)'> Negativo</label>

                        <input type='hidden' 
                        id='resultadoAnterior$cobertura_id' value='$diagnostico'>
                        </td>";
                        echo "<td align='center' width='9%'></td>";
                        echo "<td align='center' style='color:red' width='8%'>".$descarte."</td>";
                    }
                    else if ($diagnostico=='N'){
                        echo "<td align='center' width='11%'>".$data_negativo_edi."</td>";
                        echo "<td align='center' style='color:red' width='11%'>".$descarte."</td>";
                        echo "<td align='center' width='17%'></td>";
                        echo "<td align='center' width='8%'></td>";
                        echo "<td align='center' width='9%'></td>";
                    }
                    else {
                        echo "<td align='center' width='11%'><input type='date' name='data_prenhes$cobertura_id' 
                                id='data_prenhes$cobertura_id' class='form-control
                                input-sm classe-data-prenhes' value='$data_prenhes' 
                                onkeypress='return desabilita_enter(this, event)' 
                                onchange='prenhes_limpa_positiva(this.id, this.value), calcular_data_previsao(this.id, this.value), verificarPreenchimentoEmMassa(this.id, this.value, \"classe-data-prenhes\")'></td>";

                        echo "<td align='center' width='11%'><input type='date' name='data_previsao$cobertura_id' id='data_previsao$cobertura_id' class='form-control input-sm classe-data-previsao' value='$data_previsao_parto' onkeypress='return desabilita_enter(this, event)' onchange='previsao_limpa_positiva(this.id, this.value), calcular_data_prenhes(this.id, this.value), verificarPreenchimentoEmMassa(this.id, this.value, \"classe-data-previsao\") '></td>";

                        echo "<td align='center' width='17%'>
                        <label><input type='radio' 
                        name='resultado$cobertura_id'
                        id='resultadoP$cobertura_id' value='P'
                        onclick='gravar_diagnostico_positivo_femeas_servidas_monta(this.id, this.value)'> Positivo</label>&nbsp;&nbsp;
                                
                        <label><input type='radio'
                        name='resultado$cobertura_id' 
                        id='resultadoN$cobertura_id' value='N'
                        onclick='resultadoCoberturaMonta(this.id, this.value)'>
                        Negativo</label>

                        <input type='hidden' 
                        id='resultadoAnterior$cobertura_id' value='$diagnostico'>

                        </td>";
                        echo "<td align='center' width='9%'></td>";
                        echo "<td align='center' style='color:red' width='8%'>".$descarte."</td>";
                    }

                }
                else {
                    if ($diagnostico=='N') {
                        echo "<td align='center' width='11%'>".$data_negativo_edi."</td>";
                        echo "<td align='center' style='color:red' width='11%'>".$descarte."</td>";
                        echo "<td align='center' width='17%'></td>";
                        echo "<td align='center' width='8%'></td>";
                        echo "<td align='center' width='9%'></td>";
                    }
                    else {
                        echo "<td align='center' width='11%'>".$data_prenhes_edi."</td>";
                        echo "<td align='center' width='11%'>".$data_previsao_parto_edi."</td>";
                        echo "<td align='center' width='17%'></td>";
                        echo "<td align='center' width='9%'>".$desc_nascido."</td>";
                        echo "<td align='center' style='color:red' width='8%'>".$descarte."</td>";
                    }
                }

                echo "</tr>";
            }
        }
    

} // Fim do while tbl_animais

if ($tem_thead =='S') {
    echo '</tbody>';
    echo "</table>";

    echo '<div class="row">
        <div class="form-group col-md-12">  
            <button type="button" class="btn btn-success pull-right excel" style="margin-right: 6px;" 
            onClick="listar_femeas_servidas_monta_excel()">Excel</button>
        </div>
        </div>';

    echo '</form></section>';
}

echo '<script src="js/cobertura.js" charset="utf-8" type="text/javascript"></script>'; 


?>