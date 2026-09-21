<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    // Monta lista de valores para cláusula IN (...) já escapando cada item
    if (!function_exists('flc_in_list')) {
        function flc_in_list($conector, $valores) {
            $itens = array();
            foreach ($valores as $v) {
                $itens[] = "'" . mysqli_real_escape_string($conector, $v) . "'";
            }
            return implode(',', $itens);
        }
    }

    $fazenda = $_POST["local"];
    $estacao = $_POST["estacao"];

    if (isset($_POST["codigo_alfa_numerico"])) {
        $codigo_alfa_numerico = $_POST["codigo_alfa_numerico"];
    }
    else {
        $codigo_alfa_numerico = '';
    }

    $protocolo_anterior = 0;
    $print_cabecalho = 0;
    $print_tabela = 0;

    $_SESSION['estacao_monta_cobertura']=$estacao;
    $_SESSION['lista_cobertura'] = 'S';
    
    $wtipo = "";
    /*$tipo = $_POST['tipo'];
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

    if ($codigo_alfa_numerico!='') {
        $sql = "SELECT * FROM tbl_cobertura 
            INNER JOIN tbl_grupo_estacao_monta
                    ON tbl_grupo_id = tbl_cobertura_codigo_grupo AND 
                       tbl_grupo_codigo_estacao_monta=tbl_cobertura_codigo_estacao_monta AND 
                       tbl_grupo_codigo_local = tbl_cobertura_codigo_local
            INNER JOIN tbl_item_cobertura
                    ON tbl_ite_cobertura_numero_id = tbl_cobertura_id           
            WHERE tbl_cobertura_lixeira=0 AND
                  tbl_cobertura_codigo_local = '$fazenda' AND
                  tbl_cobertura_codigo_estacao_monta = '$estacao' AND 
                  tbl_cobertura_controle = 'C' AND 
                  (tbl_cobertura_protocoloiatf = 000000000 OR tbl_cobertura_protocoloiatf IS NULL) AND 
                  tbl_ite_cobertura_codigo_animal='$codigo_alfa_numerico'
            ORDER BY tbl_cobertura_codigo_grupo ASC, tbl_cobertura_protocoloiatf ASC"; 
    }
    else {
        $sql = "SELECT * FROM tbl_cobertura 
            INNER JOIN tbl_grupo_estacao_monta
                    ON tbl_grupo_id = tbl_cobertura_codigo_grupo AND 
                       tbl_grupo_codigo_estacao_monta=tbl_cobertura_codigo_estacao_monta AND 
                       tbl_grupo_codigo_local = tbl_cobertura_codigo_local
            WHERE tbl_cobertura_lixeira=0 AND
                  tbl_cobertura_codigo_local = '$fazenda' AND
                  tbl_cobertura_codigo_estacao_monta = '$estacao' AND 
                  tbl_cobertura_controle = 'C' AND 
                  (tbl_cobertura_protocoloiatf = 000000000 OR tbl_cobertura_protocoloiatf IS NULL)
            ORDER BY tbl_cobertura_codigo_grupo ASC, tbl_cobertura_protocoloiatf ASC"; 
    }

    $rs = mysqli_query($conector, $sql);

    if (mysqli_num_rows($rs) > 0){
        echo '<section class="panel lista_contas">';
        echo "<h5 style='font-weight: bold; color:#128cb8;'>Grupos sem Protocolo</h5>";
        echo '<table class="table table-striped table-advance table-hover" id="tabela_cobertura">';
        echo "<thead>
            <tr>
                <th> Grupo</th>
                <th> Data do grupo</th>
                <th> Qtde Matrizes</th>
                <th> Nome do protocolo</th>
                <th> D0</th>
                <th></th>
            </tr>
        </thead>";
                                        
        echo '<tbody>';
        while($reg_cobertura = mysqli_fetch_object($rs)){
            $cobertura_id = $reg_cobertura->tbl_cobertura_id;
            $cobertura_data = date('d/m/Y', strtotime($reg_cobertura->tbl_cobertura_data));
            $cobertura_grupo = $reg_cobertura->tbl_cobertura_codigo_grupo;
            $qtd_animais = $reg_cobertura->tbl_cobertura_qtd_animais;
            $desc_grupo = $reg_cobertura->tbl_grupo_descricao;
            echo "<tr>";
            echo "<td width='15%'>".$cobertura_grupo .' - '.$desc_grupo."</td>";
            echo "<td width='10%'>".$cobertura_data."</td>";
            echo "<td width='10%'>".$qtd_animais."</td>";
            echo "<td width='15%'><select name='lista_protocolo' id='SelectProtocolo{$cobertura_id}' class='form-control'></select></td>";
            echo "<td width='15%'><input id='Dia0_{$cobertura_id}' class='form-control' type='date'></input></td>";
            echo "<td width='10%' style='text-align: center'><button type='button' class='btn btn-primary' id='confirma_protocolo' onclick='confirma_cobertura(`{$cobertura_id}`)'>Confirma</button>";
        }  

        echo '</tbody>';
        echo '</table>';
        echo '</section>';
    }

    if ($codigo_alfa_numerico!='') {
        $sql = "SELECT * FROM tbl_cobertura 
            INNER JOIN tbl_grupo_estacao_monta
                    ON tbl_grupo_id = tbl_cobertura_codigo_grupo AND 
                       tbl_grupo_codigo_estacao_monta=tbl_cobertura_codigo_estacao_monta AND 
                       tbl_grupo_codigo_local = tbl_cobertura_codigo_local
            INNER JOIN tbl_protocoloiatf
                    ON tbl_protocoloiatf_id = tbl_cobertura_protocoloiatf
            INNER JOIN tbl_item_cobertura
                    ON tbl_ite_cobertura_numero_id = tbl_cobertura_id           
            WHERE tbl_cobertura_lixeira=0 AND
                  tbl_cobertura_codigo_local = '$fazenda' AND 
                  tbl_cobertura_codigo_estacao_monta = '$estacao' AND 
                  tbl_cobertura_controle = 'C' AND 
                  tbl_ite_cobertura_codigo_animal='$codigo_alfa_numerico' AND 
                  (tbl_cobertura_protocoloiatf != '' OR tbl_cobertura_protocoloiatf != 0)" . $wtipo . 
                  "ORDER BY tbl_cobertura_protocoloiatf ASC, tbl_cobertura_codigo_grupo ASC"; 
    }
    else {
        $sql = "SELECT * FROM tbl_cobertura 
            INNER JOIN tbl_grupo_estacao_monta
                    ON tbl_grupo_id = tbl_cobertura_codigo_grupo AND 
                       tbl_grupo_codigo_estacao_monta=tbl_cobertura_codigo_estacao_monta AND 
                       tbl_grupo_codigo_local = tbl_cobertura_codigo_local
            INNER JOIN tbl_protocoloiatf
                    ON tbl_protocoloiatf_id = tbl_cobertura_protocoloiatf
            WHERE tbl_cobertura_lixeira=0 AND
                  tbl_cobertura_codigo_local = '$fazenda' AND 
                  tbl_cobertura_codigo_estacao_monta = '$estacao' AND 
                  tbl_cobertura_controle = 'C' AND 
                  (tbl_cobertura_protocoloiatf != '' OR tbl_cobertura_protocoloiatf != 0)" . $wtipo . 
                  "ORDER BY tbl_cobertura_protocoloiatf ASC, tbl_cobertura_codigo_grupo ASC"; 
    }

    $query = mysqli_query($conector, $sql);

    // ===================================================================
    // PRÉ-CARGA EM LOTE — as 4 consultas que rodavam uma vez por cobertura
    // dentro do while passam a ser feitas uma única vez. Nenhuma regra de
    // negócio ou saída HTML muda.
    // ===================================================================
    $flc_coberturas_rows = array();
    $flc_protocolo_ids = array();
    $flc_cobertura_ids = array();
    while ($reg_cobertura = mysqli_fetch_object($query)) {
        $flc_coberturas_rows[] = $reg_cobertura;
        if ($reg_cobertura->tbl_cobertura_protocoloiatf !== null && $reg_cobertura->tbl_cobertura_protocoloiatf !== '') {
            $flc_protocolo_ids[$reg_cobertura->tbl_cobertura_protocoloiatf] = true;
        }
        if ($reg_cobertura->tbl_cobertura_id !== null) {
            $flc_cobertura_ids[$reg_cobertura->tbl_cobertura_id] = true;
        }
    }

    $flc_protocolo   = array(); // protocolo (não-lixeira) por id
    $flc_pc          = array(); // tbl_protocolo_cobertura por cobertura_id
    $flc_dias        = array(); // arrayDias por protocolo_id
    $flc_dias_idx    = array();
    $flc_tem_dia2    = array(); // cobertura_id => tem algum item com dia_2='S'

    if (!empty($flc_protocolo_ids)) {
        $in_prot = flc_in_list($conector, array_keys($flc_protocolo_ids));

        $q = mysqli_query($conector, "SELECT tbl_protocoloiatf_id, tbl_protocoloiatf_descricao,
                tbl_protocoloiatf_tipo
            FROM tbl_protocoloiatf
            WHERE tbl_protocoloiatf_id IN ($in_prot) AND tbl_protocoloiatf_lixeira = 0");
        while ($rr = mysqli_fetch_object($q)) {
            if (!isset($flc_protocolo[$rr->tbl_protocoloiatf_id])) {
                $flc_protocolo[$rr->tbl_protocoloiatf_id] = $rr;
            }
        }

        $q = mysqli_query($conector, "SELECT tbl_ite_protocoloiatf_protocolo_id,
                tbl_ite_protocoloiatf_descricao
            FROM tbl_item_protocoloiatf
            WHERE tbl_ite_protocoloiatf_lixeira = 0 AND
                  tbl_ite_protocoloiatf_protocolo_id IN ($in_prot)
            ORDER BY tbl_ite_protocoloiatf_id ASC");
        while ($rr = mysqli_fetch_object($q)) {
            $pid = $rr->tbl_ite_protocoloiatf_protocolo_id;
            if (!isset($flc_dias[$pid])) {
                $flc_dias[$pid] = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0);
                $flc_dias_idx[$pid] = 0;
            }
            $flc_dias[$pid][$flc_dias_idx[$pid]] = substr($rr->tbl_ite_protocoloiatf_descricao, 3);
            $flc_dias_idx[$pid]++;
        }
    }

    if (!empty($flc_cobertura_ids)) {
        $in_cob = flc_in_list($conector, array_keys($flc_cobertura_ids));

        $q = mysqli_query($conector, "SELECT tbl_protocolo_cobertura_codigo_id,
                tbl_protocolo_cobertura_data
            FROM tbl_protocolo_cobertura
            WHERE tbl_protocolo_cobertura_codigo_id IN ($in_cob)");
        while ($rr = mysqli_fetch_object($q)) {
            if (!isset($flc_pc[$rr->tbl_protocolo_cobertura_codigo_id])) {
                $flc_pc[$rr->tbl_protocolo_cobertura_codigo_id] = $rr;
            }
        }

        $q = mysqli_query($conector, "SELECT tbl_ite_cobertura_numero_id,
                MAX(tbl_ite_cobertura_dia_2='S') AS tem
            FROM tbl_item_cobertura
            WHERE tbl_ite_cobertura_numero_id IN ($in_cob)
            GROUP BY tbl_ite_cobertura_numero_id");
        while ($rr = mysqli_fetch_object($q)) {
            $flc_tem_dia2[$rr->tbl_ite_cobertura_numero_id] = $rr->tem;
        }
    }

    foreach ($flc_coberturas_rows as $reg_cobertura){
        $cobertura_id = $reg_cobertura->tbl_cobertura_id;
        $cobertura_data = date('d/m/Y', strtotime($reg_cobertura->tbl_cobertura_data));
        $cobertura_grupo = $reg_cobertura->tbl_cobertura_codigo_grupo;
        $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
        $qtd_animais = $reg_cobertura->tbl_cobertura_qtd_animais;
        $desc_grupo = $reg_cobertura->tbl_grupo_descricao;
        $encerrada = $reg_cobertura->tbl_cobertura_encerrada;

        if ($encerrada=='S') {
            $desc_situacao = 'Encerrada';
        }
        else {
            $desc_situacao = 'Em Aberto';
        }   
                                                     
        $reg_protocolo = isset($flc_protocolo[$protocolo_id]) ? $flc_protocolo[$protocolo_id] : null;

        $protocolo_nome = $reg_protocolo->tbl_protocoloiatf_descricao;
        $protocolo_tipo = $reg_protocolo->tbl_protocoloiatf_tipo;

        $reg_pc = isset($flc_pc[$cobertura_id]) ? $flc_pc[$cobertura_id] : null;

        $dia_0 = date('d/m/Y', strtotime($reg_pc->tbl_protocolo_cobertura_data));

        $check_lixeira = false;

        $arrayDias = isset($flc_dias[$protocolo_id]) ? $flc_dias[$protocolo_id] : [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0
        ];

        if (isset($flc_tem_dia2[$cobertura_id]) && $flc_tem_dia2[$cobertura_id]) {
            $check_lixeira = true;
        }

        $data_1 = date('d/m/Y', strtotime($reg_pc->tbl_protocolo_cobertura_data."+{$arrayDias[1]} days"));
        $data_2 = date('d/m/Y', strtotime($reg_pc->tbl_protocolo_cobertura_data."+{$arrayDias[2]} days"));
        $data_3 = date('d/m/Y', strtotime($reg_pc->tbl_protocolo_cobertura_data."+{$arrayDias[3]} days"));
        $data_4 = date('d/m/Y', strtotime($reg_pc->tbl_protocolo_cobertura_data."+{$arrayDias[4]} days"));

        $array_conta = array(
            $cobertura_id,
            $protocolo_id,
            $cobertura_grupo
        );

        $string_array = implode('|', $array_conta);
        
        if ($protocolo_anterior == 0){
            echo '<section class="panel lista_contas">'; 
            if ($protocolo_tipo=='M') {
                echo "<h5 style='font-weight: bold; color:#128cb8;'>".$protocolo_nome."</h5>";
            }
            else {
                echo "<h5 style='font-weight: bold; color:#128cb8;'>".$protocolo_nome."</h5>";
            }

            echo '<table class="table table-striped table-advance table-hover" id="tabela_cobertura" >';
            if($print_cabecalho == 0){
                echo "<thead>
                <tr>
                    <th>Grupo</th>
                    <th>Qtde Fêmeas</th>
                    <th> D0</th>";
                    if($arrayDias[4] == 0 && $arrayDias[3] != 0){
                        echo "<th> D{$arrayDias[1]}º</th>";
                        echo "<th> D{$arrayDias[2]}º</th>";
                        echo "<th> D{$arrayDias[3]}º - Inseminação</th>";
                        echo "<th></th>";
                    }elseif($arrayDias[3] == 0 && $arrayDias[2] != 0){
                        echo "<th> D{$arrayDias[1]}º</th>";
                        echo "<th> D{$arrayDias[2]}º - Inseminação</th>";
                        echo "<th></th>";
                        echo "<th></th>";
                    }elseif($arrayDias[2] == 0){
                        echo "<th> D{$arrayDias[1]}º - Inseminação</th>";
                        echo "<th></th>";
                        echo "<th></th>";
                        echo "<th></th>";
                    }else{
                        echo "<th> D{$arrayDias[1]}º</th>";
                        echo "<th> D{$arrayDias[2]}º</th>";
                        echo "<th> D{$arrayDias[3]}º</th>";
                        echo "<th> D{$arrayDias[4]}º - Inseminação</th>";
                    }
                echo    "<th> Situação</th>
                         <th><i class='icon_cogs'></i> Ações</th>
                    </tr>
                </thead>";
                $print_cabecalho = 1;
            }
            echo '<tbody>';
            echo "<tr>";
            echo "<td width='15%'>".$cobertura_grupo .' - '.$desc_grupo."</td>";
            echo "<td width='10%'>".$qtd_animais."</td>";
            echo "<td width='8%'>".$dia_0."</td>";
            if($data_1 != $dia_0){
                echo "<td width='8%'>".$data_1."</td>";
            }else{
                echo "<td width='8%'></td>";
            }
            if($data_2 != $dia_0){
                echo "<td width='8%'>".$data_2."</td>";
            }else{
                echo "<td width='8%'></td>";
            }
            if($data_3 != $dia_0){
                echo "<td width='8%'>".$data_3."</td>";
            }else{
                echo "<td width='8%'></td>";
            }
            if($data_4 != $dia_0){
                echo "<td width='8%'>".$data_4."</td>";
            }else{
                echo "<td width='8%'></td>";
            }

            echo "<td width='8%'>".$desc_situacao."</td>";


            echo "<td width='15%'>";    
            echo "<div class='btn-group'>";

            if ($encerrada=='S') {
                echo "<a class='btn' href='#'><i class='icon_search' data-toggle='tooltip' data-placement='left' id='teste$cobertura_id' title='Consultar Etapas/Diagnóstico' onClick='editar_cobertura(\"{$string_array}\");'></i></a>";
            }
            else {
                echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' id='teste$cobertura_id' title='Registrar Etapas/Diagnóstico' onClick='editar_cobertura(\"{$string_array}\");'></i></a>";
            }

            if($check_lixeira == false){
                echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Excluir Protocolo' onClick='enviar_lixeira(\"{$string_array}\",2);'></i></a>";
            }
            echo "</div>";
            echo "</td>";

            $protocolo_anterior = $protocolo_id;

        }
        else if($protocolo_id > $protocolo_anterior){
            echo '</tbody>';
            echo '</table>';
            echo '</section>';
            $print_cabecalho = 0;

            echo '<section class="panel lista_contas">';
            if ($protocolo_tipo=='M') {
                echo "<h5 style='font-weight: bold; color:#128cb8;'>".$protocolo_nome."</h5>";
            }
            else {
                echo "<h5 style='font-weight: bold; color:#128cb8;'>".$protocolo_nome." </h5>";
            }
            echo '<table class="table table-striped table-advance table-hover" id="tabela_cobertura" >';

            if ($print_cabecalho == 0){
                echo "<thead>
                <tr>
                    <th>Grupo</th>
                    <th>Qtde Fêmeas</th>
                    <th> D0</th>";
                    if($arrayDias[4] == 0 && $arrayDias[3] != 0){
                        echo "<th> D{$arrayDias[1]}º</th>";
                        echo "<th> D{$arrayDias[2]}º</th>";
                        echo "<th> D{$arrayDias[3]}º - Inseminação</th>";
                        echo "<th></th>";
                    }elseif($arrayDias[3] == 0 && $arrayDias[2] != 0){
                        echo "<th> D{$arrayDias[1]}º</th>";
                        echo "<th> D{$arrayDias[2]}º - Inseminação</th>";
                        echo "<th></th>";
                        echo "<th></th>";
                    }elseif($arrayDias[2] == 0){
                        echo "<th> D{$arrayDias[1]}º - Inseminação</th>";
                        echo "<th></th>";
                        echo "<th></th>";
                        echo "<th></th>";
                    }else{
                        echo "<th> D{$arrayDias[1]}º</th>";
                        echo "<th> D{$arrayDias[2]}º</th>";
                        echo "<th> D{$arrayDias[3]}º</th>";
                        echo "<th> D{$arrayDias[4]}º - Inseminação</th>";
                    }
                echo    "<th>Situação</th>
                         <th><i class='icon_cogs'></i> Ações</th>
                    </tr>
                </thead>";
                $print_cabecalho = 1;
            }
            echo '<tbody>';
            echo "<tr>";
            echo "<td width='15%'>".$cobertura_grupo .' - '.$desc_grupo."</td>";
            echo "<td width='10%'>".$qtd_animais."</td>";
            echo "<td width='8%'>".$dia_0."</td>";

            if ($data_1 != $dia_0){
                echo "<td width='8%'>".$data_1."</td>";
            }
            else {
                echo "<td width='8%'></td>";
            }

            if ($data_2 != $dia_0){
                echo "<td width='8%'>".$data_2."</td>";
            }
            else {
                echo "<td width='8%'></td>";
            }

            if ($data_3 != $dia_0){
                echo "<td width='8%'>".$data_3."</td>";
            }
            else {
                echo "<td width='8%'></td>";
            }

            if ($data_4 != $dia_0){
                echo "<td width='8%'>".$data_4."</td>";
            }
            else{
                echo "<td width='8%'></td>";
            }

            echo "<td width='8%'>".$desc_situacao."</td>";
            echo "<td width='15%'>";    
            echo "<div class='btn-group'>";

            if ($encerrada=='S') {
                echo "<a class='btn' href='#'><i class='icon_search' data-toggle='tooltip' data-placement='left' id='teste$cobertura_id' title='Consultar Etapas/Diagnóstico' onClick='editar_cobertura(\"{$string_array}\");'></i></a>";
            }
            else {
                echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' id='teste$cobertura_id' title='Registrar Etapas/Diagnóstico' onClick='editar_cobertura(\"{$string_array}\");'></i></a>";
            }

            if($check_lixeira == false){
                echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Excluir Protocolo' onClick='enviar_lixeira(\"{$string_array}\",2)'></i></a>";
            }
            echo "</div>";
            echo "</td>";
            echo "</tr>";
            $print_tabela = 1;
            $protocolo_anterior = $protocolo_id;
        }
        else {
            $print_tabela = 0;
            $print_cabecalho = 0;
            echo "<tr>";
            echo "<td width='15%'>".$cobertura_grupo .' - '.$desc_grupo."</td>";
            echo "<td width='10%'>".$qtd_animais."</td>";
            echo "<td width='8%'>".$dia_0."</td>";
            if ($data_1 != $dia_0){
                echo "<td width='8%'>".$data_1."</td>";
            }
            else {
                echo "<td width='8%'></td>";
            }

            if ($data_2 != $dia_0){
                echo "<td width='8%'>".$data_2."</td>";
            }
            else {
                echo "<td width='8%'></td>";
            }

            if ($data_3 != $dia_0){
                echo "<td width='8%'>".$data_3."</td>";
            }
            else {
                echo "<td width='8%'></td>";
            }

            if ($data_4 != $dia_0){
                echo "<td width='8%'>".$data_4."</td>";
            }
            else {
                echo "<td width='8%'></td>";
            }

            echo "<td width='8%'>".$desc_situacao."</td>";
            echo "<td width='15%'>";    
            echo "<div class='btn-group'>";

            if ($encerrada=='S') {
                echo "<a class='btn' href='#'><i class='icon_search' data-toggle='tooltip' data-placement='left' id='teste$cobertura_id' title='Consultar Etapas/Diagnóstico' onClick='editar_cobertura(\"{$string_array}\");'></i></a>";
            }
            else {
                echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' id='teste$cobertura_id' title='Registrar Etapas/Diagnóstico' onClick='editar_cobertura(\"{$string_array}\");'></i></a>";
            }

            if($check_lixeira == false){
                echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Excluir Protocolo' onClick='enviar_lixeira(\"{$string_array}\",2);'></i></a>";
            }
            echo "</div>";
            echo "</td>";
            echo "</tr>";
        }
    }
    echo "</tbody>";
    echo "</table>";
    echo "</section>";

    ?>

    <script src="js/cobertura.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

<!--
    <script src="js/tabela_animais.js" charset="utf-8" type="text/javascript" ></script>


</body>
</html>-->


                
                
