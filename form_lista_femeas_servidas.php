<?php

include "conecta_mysql.inc";
$data_sistema = date("Y-m-d");

$codigo_local = $_POST["local"];
$id_estacao = $_POST["id_estacao"];

$previsao_parto_de = $_POST["previsao_parto_de"];
$previsao_parto_ate = $_POST["previsao_parto_ate"];

$diagnostico = $_POST["diagnostico"];

if ($previsao_parto_de=='') {
    $previsao_parto_de = '0000-00-00';
    $previsao_parto_ate = '9999-99-99';
}

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
else {
    $wsituacao ='';
}

$tipo_cobertura = $_POST['tipo'];
$tipo_cobertura = explode(',', $tipo_cobertura);

$_SESSION['tipo_monta']='';
$_SESSION['tipo_iatf']='';
$_SESSION['tipo_te']='';

for ($i=0; $i < count($tipo_cobertura) ; $i++) { 
    if ($tipo_cobertura[$i]=='M'){
        $_SESSION['tipo_monta']='M';
    }
    else if ($tipo_cobertura[$i]=='I'){
        $_SESSION['tipo_iatf']='I';
    }
    else if ($tipo_cobertura[$i]=='T'){
        $_SESSION['tipo_te']='T';
    }
}

if ($id_estacao==0) {
    $tbl_par = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
    WHERE tbl_par_codigo_local='$codigo_local' AND 
          tbl_par_lixeira=0 AND 
          tbl_par_estacao_monta_final>='$data_sistema'");  

    $num_rows = mysqli_num_rows($tbl_par);

    if ($num_rows!=0){
        $reg_para = mysqli_fetch_object($tbl_par);

        $id_estacao = $reg_para->tbl_par_estacao_id;
    }
}

// pega estacao atual
$id_estacao_atual = 0;

$tbl_par = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
    WHERE tbl_par_codigo_local='$codigo_local' AND 
          tbl_par_lixeira=0 AND 
          tbl_par_estacao_monta_final>='$data_sistema'");  

$num_rows = mysqli_num_rows($tbl_par);

if ($num_rows!=0){
    $reg_para = mysqli_fetch_object($tbl_par);
    $id_estacao_atual = $reg_para->tbl_par_estacao_id;
}

$tem_thead = 'N';

/*$tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais 
    WHERE tbl_animal_codigo_id = 1687
    ORDER BY tbl_animal_codigo_id ASC"); 
*/

$tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais 
    WHERE tbl_animal_sexo = 'F'
    ORDER BY tbl_animal_codigo_numerico ASC"); 

while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
    $codigo_id = $reg_animal->tbl_animal_codigo_id;
    $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
    $codigo_numerico = intval($reg_animal->tbl_animal_codigo_numerico);

    $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
    //$num_coberturas = $reg_animal->tbl_animal_numero_coberturas;
    $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
    $descarte_reproducao = $reg_animal->tbl_animal_descarte_reproducao; 
    $data_baixa = $reg_animal->tbl_animal_baixado_em;
    $ativo = $reg_animal->tbl_animal_ativo;

    // Pega numero de coberturas na estação
    $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
        INNER JOIN tbl_cobertura
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
        WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
              tbl_cobertura_codigo_local = '$codigo_local' AND 
              tbl_cobertura_controle = 'C' AND 
              tbl_cobertura_codigo_estacao_monta = '$id_estacao' AND 
              tbl_ite_cobertura_dia_1 = 'S'" . $wsituacao); 

    $num_coberturas = mysqli_num_rows($tbl_cobertura);

    $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
        INNER JOIN tbl_cobertura
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
        WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
              tbl_cobertura_codigo_local = '$codigo_local' AND 
              tbl_cobertura_controle = 'C' AND 
              tbl_cobertura_codigo_estacao_monta = '$id_estacao' AND 
              tbl_cobertura_encerrada = 'S' " . $wsituacao .
        "ORDER BY tbl_cobertura_id DESC LIMIT 1"); 

    $num_rows_coberturas = mysqli_num_rows($tbl_cobertura);

    if ($num_rows_coberturas > 0 && $tem_thead == 'N'){
        echo    '<div class="row">
                    <div class="col-lg-12">
                        <section class="panel-group lista_contas">
                            <form method="POST" action="#" enctype="multipart/form-data">

                                <div class="panel"> 
                                    <div class=panel-body>
                                        <div class="tab-content">
                                            <div id="dados" class="tab-pane active">
                                                <div class="container" id="dados_cliente">';

        echo "<input type='hidden' id='local_id' value='$codigo_local'>";
        echo "<input type='hidden' id='estacao_id' value='$id_estacao'>";
        echo "<input type='hidden' id='diagnosticoT' value='$diagnostico'>";

        echo '<div class="row">
              <div class="form-group col-md-12">'; 

        if ($diagnostico=='P') {
            echo '<label class="radio-inline">
                 <input type="radio" name="diagnostico" value="P" checked> Diagnóstico Positivo 
                     </label>';

            echo '<label class="radio-inline">
                 <input type="radio" name="diagnostico" value="N" onclick="listar_femeas_servidas_estacao(\'N\')"> Diagnóstico Negativo 
                 </label>';
        }
        else {
            echo '<label class="radio-inline">
                 <input type="radio" name="diagnostico" value="P"
                     onclick="listar_femeas_servidas_estacao(\'P\')"> Diagnóstico Positivo 
                     </label>';
            echo '<label class="radio-inline">
                     <input type="radio" name="diagnostico" value="N" checked
                     > Diagnóstico Negativo 
                     </label>';
        }

        echo '<button type="button" class="btn btn-success pull-right excel" style="margin-right: 6px;" 
                            onClick="listar_femeas_servidas_excel()">Excel</button>
                    </div>
                </div>';

        echo '<table class="table table-striped table-advance table-hover" id="tabela_femeas_servidas" width="100%" style="font-size: 11px;">';

        if($diagnostico == "P"){
            echo "
            <thead>
                <tr>
                    <th><i class='fa fa-sort-alpha-asc'></i></th>
                    <th> Nº Fêmea</th>
                    <th> Raça</th>
                    <th style='text-align: center;'> Idade (meses)</th>
                    <th style='text-align: center;'> Nº Partos</th>
                    <th style='text-align: center;'> Nº Coberturas</th>
                    <th style='text-align: center;'> Data Serviço</th>
                    <th style='text-align: center;'> Semen</th>
                    <th style='text-align: center;'> Previsão do Parto</th>
                    <th style='text-align: center;'> Confirma Diagnóstico?</th>
                    <th style='text-align: center;'> Qtd Diagnósticos</th>
                    <th style='text-align: center;'> Nascido?</th>
                    <th style='text-align: center;'> Descarte</th>
                </tr>
            </thead>";
        }
        else {
            echo "
            <thead>
                <tr>
                    <th><i class='fa fa-sort-alpha-asc'></i></th>
                    <th> Nº Fêmea</th>
                    <th> Raça</th>
                    <th style='text-align: center;'> Idade (meses)</th>
                    <th style='text-align: center;'> Nº Partos</th>
                    <th style='text-align: center;'> Nº Coberturas</th>
                    <th style='text-align: center;'> Data Serviço</th>
                    <th style='text-align: center;'> Semen</th>
                    <th style='text-align: center;'> Previsão do Parto</th>
                    <th style='text-align: center;'> TROCAR este diagnóstico para POSITIVO?</th>
                    <th style='text-align: center;'> Descarte</th>
                    <th style='text-align: center;'></th>
                    <th style='text-align: center;'></th>
                </tr>
            </thead>";

        }
        echo '<tbody>';

        $tem_thead = 'S';
    }        

    while($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){
        $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
        $diagnostico_animal = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

        $sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
            WHERE tbl_protocoloiatf_id = '$protocolo_id' AND 
                  tbl_protocoloiatf_lixeira = 0");

        $num_rows_iatf = mysqli_num_rows($sql);  

        if ($num_rows_iatf!=0) {
            $reg_protocolo_iatf = mysqli_fetch_object($sql);
            $protocoloiatf_tipo = $reg_protocolo_iatf->tbl_protocoloiatf_tipo;

            for ($i=0; $i < count($tipo_cobertura) ; $i++) { 
                if ($protocoloiatf_tipo == $tipo_cobertura[$i]) {

                    if ($diagnostico_animal==$diagnostico) {
                        $cobertura_id = $reg_cobertura->tbl_cobertura_id;
                        
                        $numero_item = $reg_cobertura->tbl_ite_cobertura_numero_item;
                        $id_animal = $reg_cobertura->tbl_ite_cobertura_codigo_id_animal;
                        $codigo_animal = $reg_cobertura->tbl_ite_cobertura_codigo_animal;
                        
                        $qtd_diagnosticos = $reg_cobertura->tbl_ite_cobertura_qtd_diagnosticos_positivo;

                        $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;
                        $nascido_outro = $reg_cobertura->tbl_ite_cobertura_situacao_femea_nascido_outro;

                        if ($codigo_alfa=='') {
                            $codigo_edi = $codigo_numerico;
                        }
                        else {
                            $codigo_edi = $codigo_alfa . '-' . $codigo_numerico;
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

                        $tbl_filhos = mysqli_query($conector,"select * from tbl_animais 
                            where tbl_animal_codigo_mae='$id_animal'
                            order by tbl_animal_codigo_numerico ASC"); 
                            
                        $numero_partos = mysqli_num_rows($tbl_filhos);

                        $touro_semem = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                        if ($touro_semem!='') {
                            $semen = mysqli_query($conector, "select * from tbl_semem 
                                where tbl_semem_codigo_id='$touro_semem'"); 
                            
                            $reg_semen = mysqli_fetch_object($semen);

                            $num_rows = mysqli_num_rows($semen);

                            if ($num_rows!=0) {
                                if($reg_semen->tbl_semem_nome == ""){
                                    $desc_semen = $reg_semen->tbl_semem_nome;
                                }
                                else {
                                    $desc_semen = $reg_semen->tbl_semem_nome .'-'. $reg_semen->tbl_semem_nome;
                                }
                            }
                            else {
                                $desc_semen = '';
                            }
                        }
                        else {
                            $desc_semen = '';   
                        }

                        $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                            WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");
                            
                        $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                        $sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
                            WHERE tbl_protocoloiatf_id = '$protocolo_id' AND 
                                  tbl_protocoloiatf_lixeira = 0");
                            
                        $reg_protocolo_iatf = mysqli_fetch_object($sql);

                        $dias_diagnostico = $reg_protocolo_iatf->tbl_protocoloiatf_dias_diagnostico;

                        $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
                            WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                                  tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                            ORDER BY tbl_ite_protocoloiatf_id ASC");
                            
                        $dias_previsao_parto = 282;

                        while($reg_itens = mysqli_fetch_object($sql)){
                            $dias = substr($reg_itens->tbl_ite_protocoloiatf_descricao, 3);
                            $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));
                            $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                        }

                        $data_servico = date("d/m/Y", strtotime(str_replace('-', '/', $data_servico)));
                        $data_previsao_parto_ed = date("d/m/Y", strtotime(str_replace('-', '/', $data_previsao_parto)));
                        $cobertura_ordem = $cobertura_id . $numero_item;

                        // calcula a idade pela data do serviço conforme o trello (CORREÇÕES DA REPRODUÇÃO) 12/01/2024
                        $data_acompanhamento_calculo = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days")); // ESSA É A DATA DO SERVIÇO

                        $date = new DateTime($data_nascimento); // Data de Nascimento
                        
                        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                        // para diagnostico negativo, verifica se o animal esta tambem na estacao atual

                        $num_rows_coberturas_atual = 0;

                        if ($diagnostico == "N") {

                            $tbl_cobertura_atual = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                                INNER JOIN tbl_cobertura
                                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                                WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                                      tbl_cobertura_codigo_local = '$codigo_local' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_cobertura_codigo_estacao_monta = '$id_estacao_atual'
                                ORDER BY tbl_cobertura_id DESC LIMIT 1"); 

                            $num_rows_coberturas_atual = mysqli_num_rows($tbl_cobertura_atual);
                        }

                        if ($data_previsao_parto>=$previsao_parto_de && 
                            $data_previsao_parto<=$previsao_parto_ate) {

                            if($diagnostico == "P"){
                                echo "<tr>";
                                echo "<td align='right' width='4%'>".$codigo_alfa."</td>";
                                echo "<td align='left' width='6%'>".$codigo_numerico."</td>";
                                echo "<td width='9%'>".$desc_raca."</td>";
                                echo "<td align='center' width='5%'>".$idade."</td>";
                                echo "<td align='center' width='5%'>".$numero_partos."</td>";
                                echo "<td align='center' width='9%'>".$num_coberturas."</td>";
                                echo "<td align='center' width='9%'>".$data_servico."</td>";
                                echo "<td width='9%'>".$desc_semen."</td>";
                                echo "<td align='center' width='9%'>".$data_previsao_parto_ed."</td>";

                                if ($nascido=='N' || $nascido=='A' || $nascido=='M' || $nascido=='O') {
                                    echo "<td width='17%' align='center'> 
                                        <label><input type='radio' name='resultado$cobertura_ordem' id='resultadoP$cobertura_ordem' value='P' disabled> Sim</label>&nbsp;&nbsp;
                                            
                                        <label><input type='radio' name='resultado$cobertura_ordem' id='resultadoN$cobertura_ordem' value='N' disabled> Não</label>
                                            </td>";
                                }
                                else {
                                    echo "<td width='17%' align='center'> 
                                        <label><input type='radio' name='resultado$cobertura_ordem' id='resultadoP$cobertura_ordem' value='P' onclick='gravar_diagnostico_positivo_femeas_servidas(this.id, this.value)'> Sim</label>&nbsp;&nbsp;
                        
                                        <label><input type='radio' name='resultado$cobertura_ordem' id='resultadoN$cobertura_ordem' value='N' onclick='resultadoCobertura(this.id, this.value)'> Não</label>

                                        <input type='hidden' id='id_cobertura$cobertura_ordem' value='$cobertura_id'>
                                        <input type='hidden' id='animal_id$cobertura_ordem' value='$id_animal'>
                                        <input type='hidden' id='animal_codigo$cobertura_ordem' value='$codigo_animal'>

                                        </td>";
                                }
                        
                                echo "<td align='center' width='9%'  id='qtd_diagnosticos$cobertura_ordem'>".$qtd_diagnosticos."</td>";
                                echo "<td width='9%'>".$desc_nascido."</td>";

                                if ($descarte_reproducao=='S') {
                                    echo "<td width='5%' 
                                    style='color:red'>Sim</td>";
                                }
                                else {
                                    echo "<td width='5%'
                                    style='color:red'></td>";
                                }
                                echo "</tr>";
                            }
                            else {
                                echo "<tr>";
                                echo "<td align='right' width='4%'>".$codigo_alfa."</td>";
                                echo "<td align='left' width='6%'>".$codigo_numerico."</td>";
                                echo "<td width='9%'>".$desc_raca."</td>";
                                echo "<td align='center' width='5%'>".$idade."</td>";
                                echo "<td align='center' width='5%'>".$numero_partos."</td>";
                                echo "<td align='center' width='9%'>".$num_coberturas."</td>";
                                echo "<td align='center' width='9%'>".$data_servico."</td>";
                                echo "<td width='9%'>".$desc_semen."</td>";
                                echo "<td align='center' width='9%'>".$data_previsao_parto_ed."</td>";

                                if (($num_rows_coberturas_atual!=0 && $id_estacao_atual != $id_estacao) || $ativo == 'N') {
                                    echo "<td align='center' width='17%'>".$desc_nascido."</td>";
                                }
                                else {
                                    echo "<td align='center' width='17%'> 
                                        <label><input type='radio' name='resultado$cobertura_ordem' id='resultadoA$cobertura_ordem' value='S' onclick='alterarDiagnosticoParaPositivo(this.id, this.value)'> Sim</label>

                                        <input type='hidden' id='id_cobertura$cobertura_ordem' value='$cobertura_id'>
                                        <input type='hidden' id='animal_id$cobertura_ordem' value='$id_animal'>
                                        <input type='hidden' id='animal_codigo$cobertura_ordem' value='$codigo_animal'>
                                        </td>";
                                }

                                if ($descarte_reproducao=='S') {
                                    echo "<td width='5%' 
                                    style='color:red'>Sim</td>";
                                }
                                else {
                                    echo "<td width='5%'
                                    style='color:red'></td>";
                                }

                                echo "<td align='center' width='9%'></td>";
                                echo "<td width='9%'></td>";
                                echo "</tr>";
                            }
                        }
                    }       
                }
            }
        }              
    }
}

if ($tem_thead =='S') {
    echo '</tbody>';
    echo "</table>";

    echo '<div class="row">
        <div class="form-group col-md-12">  
            <button type="button" class="btn btn-success pull-right excel" style="margin-right: 6px;" 
            onClick="listar_femeas_servidas_excel()">Excel</button>
        </div>
        </div>';

    echo '</div> 
            </div> 
                </div> 
                    </div> 
                        </div> 
                            </form>
                              </section> 
                                </div> 
                                    </div> ';
}

echo '<script src="js/cobertura.js" charset="utf-8" type="text/javascript"></script>'; 

?>