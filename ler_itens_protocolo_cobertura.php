<?php
    include "conecta_mysql.inc";

    $protocolo_id = $_POST["protocolo_id"];
    $cobertura_id = $_POST["cobertura_id"];
    $tipoCobertura = 'I';

    //$tipoCobertura = $_POST["tipoCobertura"];

    // VERIFICAR O TIPO AQUI QUANDO FOR FIVE

    /*$wtipo = "";
    $tipo = $_POST['tipoCobertura'];
    $tipo = explode(',', $tipo);

    $_SESSION['tipo_monta']='';
    $_SESSION['tipo_iatf']='';
    $_SESSION['tipo_te']='';

    $wtipo = " AND tbl_protocoloiatf_tipo IN(";

    for ($i=0; $i < count($tipo) ; $i++) { 
        $wtipo.= "'" . $tipo[$i] . "',";

        if ($tipo[$i]=='M'){
            $_SESSION['tipo_monta']='M';
        }
        else if ($tipo[$i]=='I'){
            $_SESSION['tipo_iatf']='I';
        }
        else if ($tipo[$i]=='T'){
            $_SESSION['tipo_te']='T';
        }
    }

    $wtipo = substr($wtipo,0, -1);
    $wtipo.= ")";*/

    $sql = mysqli_query($conector,"SELECT * FROM tbl_cobertura 
        WHERE tbl_cobertura_lixeira=0 AND
              tbl_cobertura_id = $cobertura_id");

    $reg_cobertura = mysqli_fetch_object($sql);
    $local_cobertura = $reg_cobertura->tbl_cobertura_codigo_local;
    $estacao_monta = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
    $grupo_cobertura = $reg_cobertura->tbl_cobertura_codigo_grupo;
    $encerrada = $reg_cobertura->tbl_cobertura_encerrada;

    $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
        WHERE tbl_protocolo_cobertura_codigo_id = $cobertura_id");
    $reg_protocolo_cobertura = mysqli_fetch_object($sql);

    $sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
        WHERE tbl_protocoloiatf_id = $protocolo_id AND tbl_protocoloiatf_lixeira = 0");
    $reg_protocolo_iatf = mysqli_fetch_object($sql);

    $dias_diagnostico = $reg_protocolo_iatf->tbl_protocoloiatf_dias_diagnostico;

    $regProt = mysqli_fetch_object(mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf WHERE tbl_protocoloiatf_id = $protocolo_id"));

    $sql = mysqli_query($conector,"SELECT * FROM tbl_grupo_estacao_monta 
        WHERE tbl_grupo_id='$grupo_cobertura' AND 
              tbl_grupo_codigo_estacao_monta='$estacao_monta' AND 
              tbl_grupo_codigo_local='$local_cobertura'");
    $reg_grupo = mysqli_fetch_object($sql);
    $descricao_grupo = $grupo_cobertura . ' - ' . $reg_grupo->tbl_grupo_descricao;

    $raca_touro_semen_id = '';

    echo "<h4 style='text-align: center;'>Grupo: {$descricao_grupo} - {$reg_cobertura->tbl_cobertura_qtd_animais} fêmea(s)</h4>";

    echo "<h6 style='text-align: center; color: #ccc'>{$reg_cobertura->tbl_cobertura_filtros}</h6>";

    echo "<h4 style='text-align: left;'>{$regProt->tbl_protocoloiatf_descricao}</h4>";

    echo "<input type='hidden' id='cobertura_id' value='$cobertura_id'>";
    echo "<input type='hidden' id='local_id' value='$local_cobertura'>";
    echo "<input type='hidden' id='estacao_id' value='$estacao_monta'>";
    echo "<input type='hidden' id='grupo_id' value='$grupo_cobertura'>";

    echo '<table class="table table-striped table-advance table-hover">';
    echo "<thead>
            <tr>
                <th></th>
                <th> Data</th>
                <th> Item 1</th>
                <th> Item 2</th>
                <th> Item 3</th>
                <th> Item 4</th>
            </tr>
          </thead>";

    $idCobF = "";

    $sql = "SELECT * FROM tbl_item_cobertura 
        WHERE tbl_ite_cobertura_numero_id = $cobertura_id 
        ORDER BY tbl_ite_cobertura_codigo_numerico ASC";
        //ORDER BY tbl_ite_cobertura_codigo_animal ASC";
        
        //ORDER BY tbl_ite_cobertura_numero_item ASC";
    
    $r = mysqli_query($conector, $sql);

    $numReg = mysqli_num_rows($r);
    $aDias = [
        0 => 0,
        1 => 0,
        2 => 0,
        3 => 0,
        4 => 0,
        5 => 0
    ];

    $diagnostico_realizado=0;

    while($reg = mysqli_fetch_object($r)){

        if ($diagnostico_realizado==0) {
            $diagnostico_realizado = $reg->tbl_ite_cobertura_data_diagnostico;
        }

        if($reg->tbl_ite_cobertura_dia_1 == 'S'){
            $aDias[0]++;
        }

        if($reg->tbl_ite_cobertura_dia_2 == 'S'){
            $aDias[1]++;
        }

        if($reg->tbl_ite_cobertura_dia_3 == 'S'){
            $aDias[2]++;
        }

        if($reg->tbl_ite_cobertura_dia_4 == 'S'){
            $aDias[3]++;
        }

        if($reg->tbl_ite_cobertura_dia_5 == 'S'){
            $aDias[4]++;
        }

        if($reg->tbl_ite_cobertura_dia_6 == 'S'){
            $aDias[5]++; 
        }
        $idCobF = $reg->tbl_ite_cobertura_numero_id;
    }

    $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
        WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
              tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
        ORDER BY tbl_ite_protocoloiatf_id ASC");

    $iDias = 0;
    $iCheck = true;
    $data_protocolo = [];
    $index_data_protocolo = 0;

    echo "<tbody>";
    while($reg_itens = mysqli_fetch_object($sql)){
        $descricao = $reg_itens->tbl_ite_protocoloiatf_descricao;
        if($aDias[$iDias] == $numReg && $iCheck){
            echo "<tr style='color: green'>";
            echo "<td>$descricao <i class='icon_check'></i></td>";
        }elseif($aDias[$iDias] == $numReg && $iCheck){
            echo "<tr style='color: red'>";
            $iCheck = false;
            echo "<td>$descricao</td>";
        }else{
            echo "<tr>";
            echo "<td>$descricao</td>";
        }
        $iDias++;
        $dias = substr($reg_itens->tbl_ite_protocoloiatf_descricao, 3);
        $prod1 = '';
        $prod2 = '';
        $prod3 = '';
        $prod4 = '';
        if($reg_itens->tbl_ite_protocoloiatf_qtde_1 == 0.000 && $reg_itens->tbl_ite_protocoloiatf_medicamento_1 != ''){
            $prod1 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_1}";
        }elseif($reg_itens->tbl_ite_protocoloiatf_medicamento_1 != ''){
            $prod1 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_1} {$reg_itens->tbl_ite_protocoloiatf_qtde_1}{$reg_itens->tbl_ite_protocoloiatf_unidade_1}";
        }
        if($reg_itens->tbl_ite_protocoloiatf_qtde_2 == 0.000 && $reg_itens->tbl_ite_protocoloiatf_medicamento_2 != ''){
            $prod2 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_2}";
        }elseif($reg_itens->tbl_ite_protocoloiatf_medicamento_2 != ''){
            $prod2 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_2} {$reg_itens->tbl_ite_protocoloiatf_qtde_2}{$reg_itens->tbl_ite_protocoloiatf_unidade_2}";
        }
        if($reg_itens->tbl_ite_protocoloiatf_qtde_3 == 0.000 && $reg_itens->tbl_ite_protocoloiatf_medicamento_3 != ''){
            $prod3 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_3}";
        }elseif($reg_itens->tbl_ite_protocoloiatf_medicamento_3 != ''){
            $prod3 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_3} {$reg_itens->tbl_ite_protocoloiatf_qtde_3}{$reg_itens->tbl_ite_protocoloiatf_unidade_3}";
        }
        if($reg_itens->tbl_ite_protocoloiatf_qtde_4 == 0.000 && $reg_itens->tbl_ite_protocoloiatf_medicamento_4 != ''){
            $prod4 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_4}";
        }elseif($reg_itens->tbl_ite_protocoloiatf_medicamento_4 != ''){
            $prod4 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_4} {$reg_itens->tbl_ite_protocoloiatf_qtde_4}{$reg_itens->tbl_ite_protocoloiatf_unidade_4}";
        }

        $data = date("d/m/Y", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

        $data_inseminacao = date("Y-m-d", strtotime(str_replace('/', '-', $data)));
        $data_diagnostico = date("d/m/Y", strtotime($data_inseminacao . "+{$dias_diagnostico} days"));

        $index_data_protocolo++;
        $data_protocolo[$index_data_protocolo]=date("Y-m-d", strtotime(str_replace('/', '-', $data)));

        echo "<td>$data</td>";
        echo "<td>$prod1</td>";

        if($prod2 != ''){
            echo "<td>$prod2</td>";
        }else{
            echo "<td></td>";
        }
        if($prod3 != ''){
            echo "<td>$prod3</td>";
        }else{
            echo "<td></td>";
        }
        if($prod4 != ''){
            echo "<td>$prod4</td>";
        }else{
            echo "<td></td>";
        }
        echo "</tr>";
    }

    echo "<tr>";
    echo "<td>Diagnóstivo Previsto</td>";
    echo "<td>$data_diagnostico</td>";

    $diagnostico_realizado = str_replace("/", "-", $data_diagnostico);
    $diagnostico_realizado = date('Y-m-d', strtotime($diagnostico_realizado));

    // Diagnostico realizado inibido na tela e recebe a data do previsto em 22/06/2023 conforme trello quadro: MELHORIAS - REPRODUÇÃO / Item: ERRO NOS REGISTROS COBERTURA nº 137

    if ($encerrada=='S') {
        echo "<td hidden>
            <div class='form-group'>
                <div class='col-sm-4' style='padding:0;'>Diagnóstico Realizado:</div>
                    <div class='col-sm-4' style='padding:0;'>          
                        <input type='date' class='form-control' id='data_diagnostico_realizado' value='$diagnostico_realizado' readonly=''>
                    </div>
                </div>
            </td>";
    }
    else {
        echo "<td hidden>
                <div class='form-group'>
                    <div class='col-sm-4' style='padding:0;'>Diagnóstico Realizado:</div>
                    <div class='col-sm-4' style='padding:0;'>          
                        <input type='date' class='form-control' id='data_diagnostico_realizado' value='$diagnostico_realizado' onClick='limpa_borda()' onblur='limpa_borda()'>
                    </div>
                </div>
            </td>";
    }

    echo "<td></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";

    $sql = "SELECT * FROM tbl_item_protocoloiatf 
            WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                 tbl_ite_protocoloiatf_protocolo_id = $protocolo_id 
            ORDER BY tbl_ite_protocoloiatf_id ASC";
    $rs = mysqli_query($conector, $sql);

    if ($encerrada!='S') {
        echo "<div><button class='btn btn-success' style='margin-left: 15px;' onclick='confirmaMatriz(\"$idCobF\", {$numReg}, {$iDias})'>Confirma</button></div>";
    }

    echo '<table class="table table-striped table-advance table-hover" style="font-size: 12px;" id="tabelaMatriz">';

    echo "<thead>";
    echo "<tr id='header'>";
    echo "<th> N° Fêmea</th>";
    echo "<th> Raça</th>";
    echo "<th> Pai</th>";
    if($tipoCobertura == 'I'){
        echo "<th> Touro/Sêmen</th>";
    }else{
        echo "<th> Embrião</th>";
    }
    echo "<th> Raça Touro</th>";
    if($tipoCobertura == 'I'){
        echo "<th> Lote do Sêmen</th>";
    }else{
        echo "<th> Lote do Embrião</th>";
    }
    
    $count_dias = 0;

    while($reg_itens = mysqli_fetch_object($rs)){
        $dias = trim(substr($reg_itens->tbl_ite_protocoloiatf_descricao, 3));
        $count_dias++;
        echo "<th> <input type='checkbox' id='checkAllDias$count_dias' onclick='checkAllDias($count_dias)'>D$dias</th>";
    }

    echo "<th> Inseminador</th>";
    echo "<th></th>";
    echo "<th> Resultado</th>";
    echo "<th> Nº Coberturas</th>";
    echo "<th></th>";
    echo "<th></th>";
    echo "</tr>";
    echo "</thead>";

    $sql = "SELECT * FROM tbl_item_cobertura 
        WHERE tbl_ite_cobertura_numero_id = $cobertura_id 
        ORDER BY tbl_ite_cobertura_codigo_numerico ASC";
        //ORDER BY tbl_ite_cobertura_codigo_animal ASC";
        
        //ORDER BY tbl_ite_cobertura_numero_item ASC";
    $rs = mysqli_query($conector, $sql);

    echo "<tbody>";

    while($reg_itensCobertura = mysqli_fetch_object($rs)){
        echo "<tr>";

        $ordem = $reg_itensCobertura->tbl_ite_cobertura_numero_item;
        $animal_id = $reg_itensCobertura->tbl_ite_cobertura_codigo_id_animal;
        $codigo_alfa = $reg_itensCobertura->tbl_ite_cobertura_codigo_alfa;
        $codigo_numerico = $reg_itensCobertura->tbl_ite_cobertura_codigo_numerico;

        if ($codigo_alfa=='') {
            $matriz = intval($codigo_numerico);
        }
        else {
            $matriz = $codigo_alfa.'-'.intval($codigo_numerico);
        }

//        $matriz = $reg_itensCobertura->tbl_ite_cobertura_codigo_animal;
        $touro_semem = $reg_itensCobertura->tbl_ite_cobertura_codigo_touro_semen;
        $lote_semem = $reg_itensCobertura->tbl_ite_cobertura_lote_semen;
        $resultado = $reg_itensCobertura->tbl_ite_cobertura_resultado_diagnostico;
        $inseminador = $reg_itensCobertura->tbl_ite_cobertura_nome_inseminador;
        $destino = $reg_itensCobertura->tbl_ite_cobertura_destino;

        $sql = "SELECT * FROM tbl_animais WHERE tbl_animal_codigo_id = $animal_id";
        $res = mysqli_query($conector, $sql);
        $reg_animal = mysqli_fetch_object($res);

        //$numero_coberturas = $reg_animal->tbl_animal_numero_coberturas;
        $raca_id = $reg_animal->tbl_animal_codigo_raca;
        $pai_id = $reg_animal->tbl_animal_codigo_pai;

        // VERIFICA O NUMERO DE COBERTURAS NA ESTACAO

        $tbl_cobertura = mysqli_query($conector, "select * from tbl_cobertura
            inner join tbl_item_cobertura 
                    on tbl_ite_cobertura_numero_id = tbl_cobertura_id
            where tbl_cobertura_lixeira=0 and 
                  tbl_cobertura_codigo_local = '$local_cobertura' and 
                  tbl_cobertura_codigo_estacao_monta = '$estacao_monta' and 
                  tbl_ite_cobertura_codigo_id_animal='$animal_id' and 
                  tbl_ite_cobertura_dia_1='S'");

        $numero_coberturas = mysqli_num_rows($tbl_cobertura);

        $sql = "SELECT * FROM tabela_racas WHERE tab_codigo_raca = $raca_id AND tab_registro_lixeira_raca = 0";
        $res = mysqli_query($conector, $sql);
        $reg_raca = mysqli_fetch_object($res);

        $raca = $reg_raca->tab_descricao_raca;

        echo "<td align='left' width='8%'>$matriz</td>";
        echo "<td width='8%'>$raca</td>";

        // Exibe pai
        $semem = mysqli_query($conector, "select * from tbl_semem 
            where tbl_semem_lixeira=0 and 
                  tbl_semem_ativo='S' and  
                  tbl_semem_codigo_id=' $pai_id'"); 

        $num_row = mysqli_num_rows($semem);

        if ($num_row!=0) {
            $reg = mysqli_fetch_object($semem);

            echo "<td width='10%'>$reg->tbl_semem_nome</td>";
        }
        else {
            $touro = mysqli_query($conector, "select * from tbl_animais 
                where tbl_animal_lixeira=0 and 
                      tbl_animal_sexo='M' and 
                      tbl_animal_reprodutor='S' and
                      tbl_animal_ativo = 'S' and
                      tbl_animal_codigo_id = ' $pai_id'"); 
            $num_row = mysqli_num_rows($touro);

            if ($num_row!=0) {
                $reg = mysqli_fetch_object($touro);

                if ($reg->tbl_animal_nome == "") {
                    echo "<td width='10%'>$reg->tbl_animal_codigo_alfa - $reg->tbl_animal_codigo_numerico</td>";
                }
                else {
                    echo "<td width='10%'>$reg->tbl_animal_codigo_alfa - $reg->tbl_animal_codigo_numerico - $reg->tbl_animal_nome</td>";
                }
            }
            else {
                echo "<td width='10%'></td>";
            }
        }
        // Fim exibe pai

        // Exibe Select Touro/Semen
        if($tipoCobertura == "I"){
            echo "<td  width='13%'>
                <select name='lista_semem' id='lista_semem$ordem' class='form-control' onchange='raca_touroSemem(this.id, this.value);'>
                        <option value='000000000'>...</option>

                <optgroup label='SEMEM'>";

                $semem = mysqli_query($conector, "select * from tbl_semem where tbl_semem_lixeira=0 and tbl_semem_ativo='S' order by tbl_semem_nome asc"); 
                    
                while($reg = mysqli_fetch_object($semem)) { 
                    echo "<option value='$reg->tbl_semem_codigo_id'";

                    if ($reg->tbl_semem_codigo_id==$touro_semem) { 
                        $raca_touro_semen_id = $reg->tbl_semem_codigo_raca;
                        echo "selected"; 
                    } 

                    echo ">";
                    echo "$reg->tbl_semem_nome";
                    echo "</option>";
                }

                echo "</optgroup>";

                echo "<optgroup label='TOUROS'>";

                $touro = mysqli_query($conector, "select * from tbl_animais 
                    where tbl_animal_lixeira=0 and 
                        tbl_animal_sexo='M' and 
                        tbl_animal_reprodutor='S' and
                        tbl_animal_ativo = 'S' 
                    order by ISNULL(tbl_animal_nome) asc, tbl_animal_nome asc, tbl_animal_codigo_numerico asc"); 
                            
                while($reg = mysqli_fetch_object($touro)) { 
                    echo "<option value='$reg->tbl_animal_codigo_id'";

                    if ($reg->tbl_animal_codigo_id==$touro_semem) { 
                        $raca_touro_semen_id = $reg->tbl_animal_codigo_raca;
                        echo "selected"; 
                    } 
                    echo ">";

                    if($reg->tbl_animal_nome == ""){
                        echo $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                    }
                    else{
                        echo $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico . ' - ' . $reg->tbl_animal_nome;
                    }
                    
                    echo "</option>";
                }
                echo "</optgroup>";
            echo "</select>
            </td>";
        //Fim Select Touro/Semen  
        }else{
            echo "<td  width='13%'>
                <select name='lista_semem' id='lista_semem$ordem' class='form-control' onchange='raca_touroSemem(this.id, this.value);'>
                        <option value='000000000'>...</option>";

            $embriao = mysqli_query($conector, "SELECT * FROM tbl_embriao WHERE tbl_embriao_lixeira = 0");

            while($e = mysqli_fetch_object($embriao)){
                echo "<option value = '$e->tbl_embriao_id'>";
                    echo $e->tbl_embriao_doadora;
                echo "</option>";
            }

        }

        // Exibe Raça Touro/Semen
        if ($raca_touro_semen_id!='' && $tipoCobertura == "I") {
            $sql = mysqli_query($conector, "SELECT * FROM tabela_racas 
                WHERE tab_codigo_raca = $raca_touro_semen_id AND 
                      tab_registro_lixeira_raca = 0");
            $reg_raca = mysqli_fetch_object($sql);
            $desc_raca_touro_semen = $reg_raca->tab_descricao_raca;
        }  
        else {
            $desc_raca_touro_semen = '';
        }               

        echo "<td width='8%'><label id='racaTouro$ordem'>$desc_raca_touro_semen</label>
        <input type='hidden' id='raca_touro$ordem' value='$raca_touro_semen_id'></td>"; 
        // Fim Exibe Raça Touro/Semen

        // Exibe Lote Semen
        if ($encerrada=='S' && $tipoCobertura == "I") {
            echo "<td style='width: 10%;'><input type='text' class='form-control' name='lote_semem' id='lote_semem$ordem' value='$lote_semem' readonly=''></td>";
        }elseif($tipoCobertura == "T"){
            echo "<td style='width: 10%;'><input type='text' class='form-control' name='lote_semem' id='lote_embriao$ordem' value='' readonly></td>";
        }
        else {
            echo "<td style='width: 10%;'><input type='text' class='form-control' name='lote_semem' id='lote_semem$ordem' value='$lote_semem' onchange='lote_semem(this.id, this.value, {$numReg});'></td>";
        }
        // Fim Exibe Lote Semen

        // Exibe dias de protocolo
        if(1 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_1 == 'S'){
            echo "<td style='width: 3%;'>
                    <input type='checkbox' disabled checked id='diaProtocolo{$ordem}_1' name='diaProtocolo{$ordem}_1'>
                <input type='hidden' id='dataProtocolo{$ordem}_1' class='dataProtocolo1' name='dataProtocolo{$ordem}_1' value='$data_protocolo[1]'>
            </td>"; 
        }elseif(1 <= $count_dias){
            echo "<td style='width: 3%;'>
                <input type='checkbox' id='diaProtocolo{$ordem}_1' class='diaProtocolo1' name='diaProtocolo{$ordem}_1'>
                <input type='hidden' id='dataProtocolo{$ordem}_1' class='dataProtocolo1' name='dataProtocolo{$ordem}_1' value='$data_protocolo[1]'>
            </td>";
        }

        if(2 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_2 == 'S'){
            echo "<td style='width: 3%;'>
                    <input type='checkbox' disabled checked id='diaProtocolo{$ordem}_2' name='diaProtocolo{$ordem}_2'>
                    <input type='hidden' id='dataProtocolo{$ordem}_2' class='dataProtocolo2' name='dataProtocolo{$ordem}_2' value='$data_protocolo[2]'>
            </td>"; 
        }elseif(2 <= $count_dias){
            echo "<td style='width: 3%;'>
                <input type='checkbox' id='diaProtocolo{$ordem}_2' class='diaProtocolo2' name='diaProtocolo{$ordem}_2'>
                <input type='hidden' id='dataProtocolo{$ordem}_2' class='dataProtocolo2' name='dataProtocolo{$ordem}_2' value='$data_protocolo[2]'>
            </td>";
        }

        if(3 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_3 == 'S'){
            echo "<td style='width: 3%;'>
                    <input type='checkbox' disabled checked id='diaProtocolo{$ordem}_3' name='diaProtocolo{$ordem}_3'>
                    <input type='hidden' id='dataProtocolo{$ordem}_3' class='dataProtocolo3' name='dataProtocolo{$ordem}_3' value='$data_protocolo[3]'>
            </td>"; 
        }elseif(3 <= $count_dias){
            echo "<td style='width: 3%;'>
                <input type='checkbox' id='diaProtocolo{$ordem}_3' class='diaProtocolo3' name='diaProtocolo{$ordem}_3'>
                <input type='hidden' id='dataProtocolo{$ordem}_3' class='dataProtocolo3' name='dataProtocolo{$ordem}_3' value='$data_protocolo[3]'>
            </td>";
        }

        if(4 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_4 == 'S'){
            echo "<td style='width: 3%;'>
                    <input type='checkbox' disabled checked id='diaProtocolo{$ordem}_4' name='diaProtocolo{$ordem}_4'>
                    <input type='hidden' id='dataProtocolo{$ordem}_4' class='dataProtocolo4' name='dataProtocolo{$ordem}_4' value='$data_protocolo[4]'>
            </td>"; 
        }elseif(4 <= $count_dias){
            echo "<td style='width: 3%;'>
                <input type='checkbox' id='diaProtocolo{$ordem}_4' class='diaProtocolo4' name='diaProtocolo{$ordem}_4'>

                <input type='hidden' id='dataProtocolo{$ordem}_4' class='dataProtocolo4' name='dataProtocolo{$ordem}_4' value='$data_protocolo[4]'>
            </td>";
        }

        if(5 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_5 == 'S'){
            echo "<td style='width: 3%;'>
                    <input type='checkbox' disabled checked id='diaProtocolo{$ordem}_5' name='diaProtocolo{$ordem}_5'>
                    <input type='hidden' id='dataProtocolo{$ordem}_5' class='dataProtocolo5' name='dataProtocolo{$ordem}_5' value='$data_protocolo[5]'>
            </td>"; 
        }elseif(5 <= $count_dias){
            echo "<td style='width: 3%;'>
                <input type='checkbox' id='diaProtocolo{$ordem}_5' class='diaProtocolo5' name='diaProtocolo{$ordem}_5'>
                <input type='hidden' id='dataProtocolo{$ordem}_5' class='dataProtocolo5' name='dataProtocolo{$ordem}_5' value='$data_protocolo[5]'>
            </td>";
        }

        if(6 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_6 == 'S'){
            echo "<td style='width: 3%;'>
                    <input type='checkbox' disabled checked id='diaProtocolo{$ordem}_6' name='diaProtocolo{$ordem}'>
                    <input type='hidden' id='dataProtocolo{$ordem}_6' class='dataProtocolo6' name='dataProtocolo{$ordem}_6' value='$data_protocolo[6]'>
            </td>"; 
        }elseif(6 <= $count_dias){
            echo "<td style='width: 3%;'>
                <input type='checkbox' id='diaProtocolo{$ordem}_6' class='diaProtocolo6' name='diaProtocolo{$ordem}_6'>
                <input type='hidden' id='dataProtocolo{$ordem}_6' class='dataProtocolo6' name='dataProtocolo{$ordem}_6' value='$data_protocolo[6]'>
            </td>";
        }
        // Fim exibe dias de protocolos

        // Exibe nome do inseminador
        if ($encerrada=='S') {
            echo "<td style='width: 10%;'>
                <input type='text' class='form-control' name='inseminador' id='inseminador$ordem' value='$inseminador' style='width: 10em;' readonly=''></td>";
            echo "<td style='width: 5%;'></td>";
        }
        else {
            echo "<td style='width: 10%;'>
                <input type='text' class='form-control' name='inseminador' id='inseminador$ordem' value='$inseminador' maxlength='30' style='width: 10em;' onchange='inseminador(this.id, this.value, {$numReg});'></td>";
        }
        // Fim exibe nome do inseminador

        // Exibe Select do inseminador
        if ($encerrada!='S') {
            echo "<td style='width: 5%;'>
            <select name='lista_funcionario' id='lista_funcionario$ordem' class='form-control' onchange='lista_funcionario(this.id, this.value);' style='width: 1.6em; margin-left: -30px;'>
                        <option value='000000000'>...</option>";

            $pessoa = mysqli_query($conector, "select * from tbl_pessoa 
                where tbl_pessoa_lixeira=0 and 
                      tbl_pessoa_classe=5 
                order by tbl_pessoa_nome asc"); 
                    
            while($reg = mysqli_fetch_object($pessoa)) { 
                echo "<option value='$reg->tbl_pessoa_id'";

                    if ($reg->tbl_pessoa_nome==$inseminador) { 
                        echo "selected"; 
                    } 
                    echo ">";
                    echo "$reg->tbl_pessoa_nome";
                echo "</option>";
            }
            echo "</select></td>";
        }
        // Fim exibe select do inseminador

        // Exibe resultado
        if($resultado == 'N'){
            echo "<td style='width: 7%;'>
                <label><input type='radio' class='resultadoP$cobertura_ordem' name='resultado$ordem' id='resultadoP$ordem' value='P' disabled data-toggle='tooltip' data-placement='right' title='A alteração de Diagnóstico Positivo para Negativo só poderá ser feita na tela: Reprodução>Diagnóstico.'> Positivo</label>
                <label><input type='radio' class='resultadoN$cobertura_ordem' name='resultado$ordem' id='resultadoN$ordem' value='N' disabled checked data-toggle='tooltip' data-placement='right' title='A alteração de Diagnóstico Negativo para Positivo só poderá ser feita na tela: Reprodução>Diagnóstico.'> Negativo</label>
            </td>";
        }elseif($resultado == 'P'){
            echo "<td style='width: 7%;'>
                <label><input type='radio' class='resultadoP$cobertura_ordem' name='resultado$ordem' id='resultadoP$ordem' value='P' disabled checked data-toggle='tooltip' data-placement='right' title='A alteração de Diagnóstico Positivo para Negativo só poderá ser feita na tela: Reprodução>Diagnóstico.'> Positivo</label>
                <label><input type='radio' class='resultadoN$cobertura_ordem' name='resultado$ordem' id='resultadoN$ordem' value='N' disabled data-toggle='tooltip' data-placement='right' title='A alteração de Diagnóstico Negativo para Positivo só poderá ser feita na tela: Reprodução>Diagnóstico.'> Negativo</label>
            </td>";
        }else{
            echo "<td style='width: 7%;'>
                <label><input type='radio' class='resultadoP$cobertura_ordem' name='resultado$ordem' id='resultadoP$ordem' value='P' onclick='resultadoCobertura(this.id, this.value)'> Positivo</label>
                <label><input type='radio' class='resultadoN$cobertura_ordem' name='resultado$ordem' id='resultadoN$ordem' value='N' onclick='resultadoCobertura(this.id, this.value)'> Negativo</label>
            </td>";
        }
        // Fim exibe resultado

        // Exibe Numero de Coberturas
        echo "<td width='13%' align='center'>$numero_coberturas</td>";
        // Fim exibe numero de Coberturas

        echo "<td><input type='hidden' id='animal_id$ordem' value='$animal_id'></td>";
        echo "<td><input type='hidden' id='animal_codigo$ordem' value='$matriz'></td>";

        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";

    if ($encerrada!='S') {
        echo "<button class='btn btn-success' style='margin-left: 15px' onclick='confirmaMatriz(\"$idCobF\", {$numReg}, {$iDias})'>Confirma</button>";
    }

    echo "<script>
        $(document).ready(function(){
            $('#tabelaMatriz').DataTable({
                'responsive': true,
                'paging':   false,
                'ordering': false,
                'info':     true,
                'language': {
                    'sSearch': 'Busca:',
                    'zeroRecords': 'Nada encontrado',
                    'info': '',
                    'infoEmpty': 'Nenhum registro disponível',
                    'infoFiltered': '(filtrado de _MAX_ registros no total)',
                },
                initComplete: function() {
                    $('table.dataTable').css('width', '100%');
                }
            });
        });
    </script>";

    echo '<script>
            $(document).ready(function(){
                $("[data-toggle=\\"tooltip\\"]").tooltip();
            });
        </script>';

?>