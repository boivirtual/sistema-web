<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $tipo_registro = $_POST['tipo_registro']; // C=IATF/Descarte; M=Monta
    $local = $_POST['local'];

    if (isset($_POST["estacao_monta"])) {
        $estacao_monta = $_POST["estacao_monta"];
    }
    else {
        $estacao_monta = '';
    }
    $codigo_alfa_numerico = $_POST["codigo_alfa_numerico"];

    @ session_start();

    $_SESSION['local_matrizes']=$local;
    $_SESSION['estacao_monta_matrizes']=$estacao_monta;
    $_SESSION['tipo_registro_matrizes']=$tipo_registro;
    $_SESSION['codigo_numerico_matrizes']=$codigo_alfa_numerico;
    $_SESSION['lista_matrizes']='S';

    // Versões tratadas para uso em SQL (não altera os valores gravados na sessão)
    $local_sql = mysqli_real_escape_string($conector, $local);
    $estacao_monta_sql = mysqli_real_escape_string($conector, $estacao_monta);
    $codigo_alfa_numerico_sql = mysqli_real_escape_string($conector, $codigo_alfa_numerico);

    // Monta a lista de valores para cláusulas IN (...) já escapando cada item
    if (!function_exists('lm_in_list')) {
        function lm_in_list($conector, $valores) {
            $itens = array();
            foreach ($valores as $v) {
                $itens[] = "'" . mysqli_real_escape_string($conector, $v) . "'";
            }
            return implode(',', $itens);
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
  <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" rel="stylesheet"  integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

  <style>
/*      .table_overflow table thead th{
        position: sticky;
        top: 0;
        z-index: 1;
      }
      .table_overflow th{
        background-color: #eee;
      }

      table.dataTable.no-footer{
          border: none;
      }

      #tabela_cobertura_wrapper{
          width: 100% !important;
          overflow-x: scroll !important;
          overflow-y: scroll !important;
          max-height: 300px;
      }

      #tabela_cobertura_filter{
          float: right;
      }*/
  </style>

</head>

<body>

  <?php
    echo '<section class="panel lista_contas">';

    if ($tipo_registro=='C') { // IATF
        echo '<table class="table table-striped table-advance table-hover" id="tabela_lista_matrizes" style="font-size: 12px">';

        echo '<tbody>';

        if ($codigo_alfa_numerico!='') {
            $tbl_matrizes = mysqli_query($conector, "SELECT tbl_cobertura_id, tbl_cobertura_codigo_local,
                       tbl_cobertura_codigo_grupo, tbl_cobertura_codigo_estacao_monta, tbl_cobertura_qtd_animais,
                       tbl_cobertura_protocoloiatf, tbl_cobertura_planilha_processada, tbl_cobertura_data
                FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_cobertura_controle='C' AND
                      tbl_cobertura_codigo_local='$local_sql' AND
                      tbl_cobertura_codigo_estacao_monta='$estacao_monta_sql' AND
                      tbl_ite_cobertura_codigo_animal='$codigo_alfa_numerico_sql'
                ORDER BY tbl_cobertura_codigo_local, tbl_cobertura_codigo_grupo ASC");
        }
        else {
            $tbl_matrizes = mysqli_query($conector, "SELECT tbl_cobertura_id, tbl_cobertura_codigo_local,
                       tbl_cobertura_codigo_grupo, tbl_cobertura_codigo_estacao_monta, tbl_cobertura_qtd_animais,
                       tbl_cobertura_protocoloiatf, tbl_cobertura_planilha_processada, tbl_cobertura_data
                FROM tbl_cobertura
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_cobertura_controle='C' AND
                      tbl_cobertura_codigo_local='$local_sql' AND
                      tbl_cobertura_codigo_estacao_monta='$estacao_monta_sql'
                ORDER BY tbl_cobertura_codigo_local, tbl_cobertura_codigo_grupo ASC");
        }

        // Carrega o resultado em memória e coleta as chaves para as consultas em lote
        $rows_matrizes = array();
        $ids_cobertura = array();
        $ids_grupo = array();
        $ids_protocolo = array();

        while ($reg_matrizes = mysqli_fetch_object($tbl_matrizes)){
            $rows_matrizes[] = $reg_matrizes;
            $ids_cobertura[$reg_matrizes->tbl_cobertura_id] = true;
            $ids_grupo[$reg_matrizes->tbl_cobertura_codigo_grupo] = true;
            if ($reg_matrizes->tbl_cobertura_protocoloiatf !== null && $reg_matrizes->tbl_cobertura_protocoloiatf !== '') {
                $ids_protocolo[$reg_matrizes->tbl_cobertura_protocoloiatf] = true;
            }
        }

        $num_rows_matrizes = count($rows_matrizes);

        // Local (o filtro garante que todas as coberturas pertencem ao mesmo local)
        $desc_local = '';
        $tbl_local = mysqli_query($conector, "select tbl_pessoa_nome from tbl_pessoa where tbl_pessoa_id='$local_sql'");
        if ($reg = mysqli_fetch_object($tbl_local)) {
            $desc_local = $reg->tbl_pessoa_nome;
        }

        // Mapa de descrições de grupo (mesmo local e mesma estação de monta do filtro)
        $mapa_grupo = array();
        $q_grupo = mysqli_query($conector, "select tbl_grupo_id, tbl_grupo_descricao from tbl_grupo_estacao_monta
                where tbl_grupo_codigo_local='$local_sql' and
                      tbl_grupo_codigo_estacao_monta='$estacao_monta_sql'");
        while ($r = mysqli_fetch_object($q_grupo)) {
            $mapa_grupo[$r->tbl_grupo_id] = $r->tbl_grupo_descricao;
        }

        // Mapa de descrições de protocolo
        $mapa_protocolo = array();
        if (count($ids_protocolo) > 0) {
            $lista_protocolo = lm_in_list($conector, array_keys($ids_protocolo));
            $q_protocolo = mysqli_query($conector, "select tbl_protocoloiatf_id, tbl_protocoloiatf_descricao
                    from tbl_protocoloiatf where tbl_protocoloiatf_id IN ($lista_protocolo)");
            while ($r = mysqli_fetch_object($q_protocolo)) {
                $mapa_protocolo[$r->tbl_protocoloiatf_id] = $r->tbl_protocoloiatf_descricao;
            }
        }

        // Mapa "iniciou protocolo": alguma linha do item com dia_1='S' ou dia_2='S'
        $mapa_iniciou = array();
        if (count($ids_cobertura) > 0) {
            $lista_cobertura = lm_in_list($conector, array_keys($ids_cobertura));
            $q_iniciou = mysqli_query($conector, "SELECT tbl_ite_cobertura_numero_id,
                        MAX(tbl_ite_cobertura_dia_1='S' OR tbl_ite_cobertura_dia_2='S') AS iniciou
                    FROM tbl_item_cobertura
                    WHERE tbl_ite_cobertura_numero_id IN ($lista_cobertura)
                    GROUP BY tbl_ite_cobertura_numero_id");
            while ($r = mysqli_fetch_object($q_iniciou)) {
                $mapa_iniciou[$r->tbl_ite_cobertura_numero_id] = $r->iniciou;
            }
        }

        if ($num_rows_matrizes!=0) {
            foreach ($rows_matrizes as $reg_matrizes){
                $codigo = $reg_matrizes->tbl_cobertura_id;
                $codigo_local = $reg_matrizes->tbl_cobertura_codigo_local;
                $codigo_grupo = $reg_matrizes->tbl_cobertura_codigo_grupo;
                $codigo_estacao = $reg_matrizes->tbl_cobertura_codigo_estacao_monta;
                $qtd_animais = $reg_matrizes->tbl_cobertura_qtd_animais;
                $protocolo = $reg_matrizes->tbl_cobertura_protocoloiatf;
                $iniciou_protocolo = '';
                $planilha_processada = $reg_matrizes->tbl_cobertura_planilha_processada;

                $desc_grupo = isset($mapa_grupo[$codigo_grupo]) ? $mapa_grupo[$codigo_grupo] : '';

                if (isset($mapa_iniciou[$codigo]) && $mapa_iniciou[$codigo]) {
                    $iniciou_protocolo = 'S';
                }

                $desc_protocolo = isset($mapa_protocolo[$protocolo]) ? $mapa_protocolo[$protocolo] : '';

                $data_matrizes = new DateTime($reg_matrizes->tbl_cobertura_data);
                $data_cobertura_edi = $data_matrizes->format('d/m/Y');

                echo "<tr>";

                if ($codigo_grupo==0){
                    echo "<td width='5%' class='status_nao'>".$codigo_grupo."</td>";
                    echo "<td width='10%' class='status_nao'>".$desc_grupo."</td>";
                    echo "<td width='10%' class='status_nao'>".$data_cobertura_edi."</td>";
                    echo "<td width='15%' class='status_nao'>".$desc_local."</td>";
                    echo "<td width='8%' class='status_nao'>".$qtd_animais."</td>";
                    echo "<td width='20%' class='status_nao'>".$desc_protocolo."</td>";
                    echo "<td width='17%' class='status_nao'>Definir Grupos para Reprodução</td>";
                    echo "<td width='15%'>";
                    echo "<div class='btn-group'>";

                    echo "<a class='btn' href='#'>
                        <i class='icon_pencil' data-toggle='tooltip' data-placement='left'  title='Registrar grupos' onClick='registrar_grupos_cobertura(\"{$codigo}\")'>
                        </i></a>";

                    if ($planilha_processada=='') {
                        echo "<a class='btn' href='#'>
                            <i class='fa fa-file-excel-o' data-toggle='tooltip' data-placement='left' title='Importar excel da lista de Fêmeas' onClick='importar_excel_lista_femeas(\"{$codigo}\",\"{$desc_local}\",\"{$qtd_animais}\",\"{$codigo_local}\")'>
                                        </i></a>";
                    }

                    echo "<a class='btn' href='#'>
                        <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Estornar esse registro' onClick='excluir_lista_cobertura(\"{$codigo}\",\"{$desc_local}\",\"{$qtd_animais}\")'>
                            </i></a>";
                    echo "</div>";
                    echo "</td>";
                }
                else {
                    echo "<td width='5%'>".$codigo_grupo."</td>";
                    echo "<td width='10%'>".$desc_grupo."</td>";
                    echo "<td width='10%'>".$data_cobertura_edi."</td>";
                    echo "<td width='15%'>".$desc_local."</td>";
                    echo "<td width='8%'>".$qtd_animais."</td>";
                    echo "<td width='20%'>".$desc_protocolo."</td>";
                    echo "<td width='17%'></td>";
                    echo "<td width='15%'>";
                    echo "<div class='btn-group'>";
                    echo "<a class='btn' href='form_selecao_matrizes_consultar.php?id=".$codigo."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro'></i></a>";

                    if ($iniciou_protocolo=='') {
                        echo "<a class='btn' href='#'>
                            <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Estornar esse registro' onClick='excluir_lista_cobertura(\"{$codigo}\",\"{$desc_local}\",\"{$qtd_animais}\")'>
                            </i></a>";
                    }
                    echo "</div>";
                    echo "</td>";
                }
                echo "</tr>";
            }
        }

        mysqli_close($conector);
        echo '</tbody>';
        echo '<thead>';
        echo '
            <tr>
            <th> Grupo</th>
            <th> Descrição</th>
            <th> Data</th>
            <th> Local</th>
            <th> Qtd Animais</th>
            <th> Protocolo</th>
            <th></th>
            <th><i class="icon_cogs"></i> Ações</th>
            </tr>';
        echo '</thead>';
        echo '</table>';
        echo '</section>';
    }
    else if ($tipo_registro=='M'){ // Monta
        $tbl_local = mysqli_query($conector, "select tbl_pessoa_nome from tbl_pessoa where tbl_pessoa_id='$local_sql'");
        $num_rows = mysqli_num_rows($tbl_local);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tbl_local);
            $desc_local = $reg->tbl_pessoa_nome;
        }
        else {
            $desc_local = '';
        }

        /*echo '<section class="panel lista_contas">';

        echo '<div class="row">
            <div class="form-group col-md-6">
                <label class="label_consulta">Local:&nbsp;</label>
                <span class="desc_local">'.$desc_local.'</span>
            </div>
        </div>';
        */


        // Verifica se tem lista de animais em excel para processar
        $tbl_matrizes = mysqli_query($conector, "SELECT tbl_cobertura_id, tbl_cobertura_planilha_processada,
                  tbl_cobertura_qtd_animais, tbl_cobertura_data
            FROM tbl_cobertura
            WHERE tbl_cobertura_lixeira=0 AND
                  tbl_cobertura_controle='M' AND
                  tbl_cobertura_planilha_processada='A' AND
                  tbl_cobertura_codigo_local='$local_sql'
            ORDER BY tbl_cobertura_data DESC");

        $num_rows_matrizes = mysqli_num_rows($tbl_matrizes);

        if ($num_rows_matrizes!=0) {
            echo '<table class="table table-striped table-advance table-hover" style="font-size: 12px">';

            echo '<tbody>';

            while ($reg_matrizes = mysqli_fetch_object($tbl_matrizes)){
                $cobertura_id = $reg_matrizes->tbl_cobertura_id;
                $planilha_processada = $reg_matrizes->tbl_cobertura_planilha_processada;
                $qtd_animais = $reg_matrizes->tbl_cobertura_qtd_animais;
                $data_cobertura = new DateTime($reg_matrizes->tbl_cobertura_data);
                $data_cobertura_edi = $data_cobertura->format('d/m/Y');

                echo '<tr>';
                echo '<td>'.$data_cobertura_edi.'</td>';
                echo '<td>'.$desc_local.'</td>';
                echo '<td>'.$qtd_animais.'</td>';
                echo "<td width='15%'>";
                echo "<div class='btn-group'>";

                echo "<a class='btn' href='#'>
                    <i class='icon_pencil' data-toggle='tooltip' data-placement='left'  title='Confirmar as Fêmeas da Lista' onClick='registrar_grupos_cobertura(\"{$cobertura_id}\")'>
                            </i></a>";

                        //if ($planilha_processada=='') {
                echo "<a class='btn' href='#'>
                    <i class='fa fa-file-excel-o' data-toggle='tooltip' data-placement='left' title='Importar excel da lista de Fêmeas' onClick='importar_excel_lista_femeas(\"{$cobertura_id}\",\"{$desc_local}\",\"{$qtd_animais}\",\"{$local}\")'>
                                            </i></a>";
                        //}

                echo "<a class='btn' href='#'>
                    <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Estornar esse registro' onClick='excluir_lista_cobertura(\"{$cobertura_id}\",\"{$desc_local}\",\"{$qtd_animais}\")'>
                                </i></a>";
                echo "</div>";
                echo "</td>";
                echo "</tr>";
            }

            echo '</tbody>';
            echo '<thead>';
            echo '
                <tr>
                <th> Data</th>
                <th> Local</th>
                <th> Qtd Animais</th>
                <th><i class="icon_cogs"></i> Ações</th>
                </tr>';
            echo '</thead>';
            echo '</table>';
        }

        echo '
        <div class="row">
            <div class="form-group col-md-5">
                <label class="control-label">&nbsp;</label>
                <p onclick="modal_inserir_nova_matriz_monta()" style="color: #1E90FF; cursor: pointer; font-size: 15px;">
                <i class="fa fa-plus"></i>
                &nbsp;Inserir Fêmea</p>
            </div>

            <div class="form-group col-md-5">
            </div>

            <div class="form-group col-md-2">
                <label class="control-label">&nbsp;</label>
                <button type="button" class="form-control btn btn-info pull-right" onclick="diagnostico_monta()">Diagnóstico</button>
            </div>
        </div>';

        // Lista animais em Monta ja confirmados
        if ($codigo_alfa_numerico!='') {
            $tbl_matrizes = mysqli_query($conector, "SELECT tbl_cobertura_id, tbl_ite_cobertura_numero_item,
                      tbl_cobertura_planilha_processada, tbl_cobertura_qtd_animais,
                      tbl_ite_cobertura_codigo_id_animal, tbl_ite_cobertura_codigo_animal,
                      tbl_ite_cobertura_codigo_alfa, tbl_ite_cobertura_codigo_numerico,
                      tbl_cobertura_data, tbl_ite_cobertura_data_prenhes, tbl_ite_cobertura_previsao_parto
                FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_cobertura_controle='M' AND
                       tbl_cobertura_planilha_processada!='A' AND
                      tbl_cobertura_codigo_local='$local_sql' AND
                      tbl_ite_cobertura_codigo_animal='$codigo_alfa_numerico_sql'
                ORDER BY tbl_ite_cobertura_codigo_numerico ASC");
        }
        else {
            $tbl_matrizes = mysqli_query($conector, "SELECT tbl_cobertura_id, tbl_ite_cobertura_numero_item,
                      tbl_cobertura_planilha_processada, tbl_cobertura_qtd_animais,
                      tbl_ite_cobertura_codigo_id_animal, tbl_ite_cobertura_codigo_animal,
                      tbl_ite_cobertura_codigo_alfa, tbl_ite_cobertura_codigo_numerico,
                      tbl_cobertura_data, tbl_ite_cobertura_data_prenhes, tbl_ite_cobertura_previsao_parto
                FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_cobertura_controle='M' AND
                      (tbl_cobertura_planilha_processada='S' OR
                       tbl_cobertura_planilha_processada='' OR
                       tbl_cobertura_planilha_processada IS NULL) AND
                      tbl_cobertura_codigo_local='$local_sql' AND
                      (tbl_ite_cobertura_resultado_diagnostico='P' OR
                        tbl_ite_cobertura_resultado_diagnostico='' OR
                        tbl_ite_cobertura_resultado_diagnostico is null) AND
                      (tbl_ite_cobertura_nascido = '' OR tbl_ite_cobertura_nascido IS NULL)
                ORDER BY tbl_ite_cobertura_codigo_numerico ASC");
        }

        // Carrega o resultado em memória e coleta os códigos de animal para consultas em lote
        $rows_matrizes = array();
        $ids_animal = array();

        while ($reg_matrizes = mysqli_fetch_object($tbl_matrizes)){
            $rows_matrizes[] = $reg_matrizes;
            if ($reg_matrizes->tbl_ite_cobertura_codigo_id_animal !== null) {
                $ids_animal[$reg_matrizes->tbl_ite_cobertura_codigo_id_animal] = true;
            }
        }

        $num_rows_matrizes = count($rows_matrizes);

        // Mapas: dados do animal, raça, nº de partos (filhos) e nº de abortos
        $mapa_animal = array();
        $mapa_raca = array();
        $mapa_partos = array();
        $mapa_abortos = array();

        if (count($ids_animal) > 0) {
            $lista_animal = lm_in_list($conector, array_keys($ids_animal));

            $q_animal = mysqli_query($conector, "select tbl_animal_codigo_id, tbl_animal_data_nascimento,
                    tbl_animal_baixado_em, tbl_animal_codigo_raca
                from tbl_animais where tbl_animal_codigo_id IN ($lista_animal)");
            $ids_raca = array();
            while ($r = mysqli_fetch_object($q_animal)) {
                $mapa_animal[$r->tbl_animal_codigo_id] = $r;
                if ($r->tbl_animal_codigo_raca !== null && $r->tbl_animal_codigo_raca !== '') {
                    $ids_raca[$r->tbl_animal_codigo_raca] = true;
                }
            }

            if (count($ids_raca) > 0) {
                $lista_raca = lm_in_list($conector, array_keys($ids_raca));
                $q_raca = mysqli_query($conector, "select tab_codigo_raca, tab_descricao_raca from tabela_racas
                    where tab_registro_lixeira_raca = 0 and tab_codigo_raca IN ($lista_raca)");
                while ($r = mysqli_fetch_object($q_raca)) {
                    $mapa_raca[$r->tab_codigo_raca] = $r->tab_descricao_raca;
                }
            }

            $q_partos = mysqli_query($conector, "select tbl_animal_codigo_mae, COUNT(*) AS c from tbl_animais
                where tbl_animal_codigo_mae IN ($lista_animal)
                group by tbl_animal_codigo_mae");
            while ($r = mysqli_fetch_object($q_partos)) {
                $mapa_partos[$r->tbl_animal_codigo_mae] = $r->c;
            }

            $q_abortos = mysqli_query($conector, "SELECT tbl_mov_estoque_codigo_mae, COUNT(*) AS c
                FROM tbl_movimentacao_estoque
                WHERE tbl_mov_estoque_codigo_mae IN ($lista_animal) AND
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='A' AND
                      (tbl_mov_estoque_tipo_movimentacao='A' OR
                       tbl_mov_estoque_tipo_movimentacao='B')
                GROUP BY tbl_mov_estoque_codigo_mae");
            while ($r = mysqli_fetch_object($q_abortos)) {
                $mapa_abortos[$r->tbl_mov_estoque_codigo_mae] = $r->c;
            }
        }

        echo '<table class="table table-striped table-advance table-hover" id="tabela_lista_matrizes_monta" style="font-size: 12px">';

        echo '<tbody>';

        if ($num_rows_matrizes!=0) {
            foreach ($rows_matrizes as $reg_matrizes){
                $cobertura_id = $reg_matrizes->tbl_cobertura_id;
                $numero_item = $reg_matrizes->tbl_ite_cobertura_numero_item;
                $planilha_processada = $reg_matrizes->tbl_cobertura_planilha_processada;
                $qtd_animais = $reg_matrizes->tbl_cobertura_qtd_animais;

                $codigo_animal_id = $reg_matrizes->tbl_ite_cobertura_codigo_id_animal;
                $codigo_animal = $reg_matrizes->tbl_ite_cobertura_codigo_animal;

                $codigo_alfa = $reg_matrizes->tbl_ite_cobertura_codigo_alfa;
                $codigo_numerico = intval($reg_matrizes->tbl_ite_cobertura_codigo_numerico);

                if (isset($mapa_animal[$codigo_animal_id])) {
                    $reg_animal = $mapa_animal[$codigo_animal_id];
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
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    $idade_ano_mes = $idade_acompanhamento->format('%Y') .' a/ '. str_pad($idade_acompanhamento->format('%m') , 2 , '0' , STR_PAD_LEFT) . ' m';

                    $numero_abortos = 0;
                    $dias_ultimo_parto = 0;
                    $coberturas_estacao = 0;

                    $codigo_raca= $reg_animal->tbl_animal_codigo_raca;

                    $desc_raca = isset($mapa_raca[$codigo_raca]) ? $mapa_raca[$codigo_raca] : '';

                    $numero_partos = isset($mapa_partos[$codigo_animal_id]) ? (int)$mapa_partos[$codigo_animal_id] : 0;

                    $numero_abortos = isset($mapa_abortos[$codigo_animal_id]) ? (int)$mapa_abortos[$codigo_animal_id] : 0;
                }


                $data_cobertura = new DateTime($reg_matrizes->tbl_cobertura_data);
                $data_cobertura_edi = $data_cobertura->format('d/m/Y');

                $data_prenhes = $reg_matrizes->tbl_ite_cobertura_data_prenhes;

                $data_previsao_parto = $reg_matrizes->tbl_ite_cobertura_previsao_parto;

                if ($data_prenhes=='') {
                    $data_prenhes_edi = '';
                }
                else {
                    $data_prenhes = new DateTime($reg_matrizes->tbl_ite_cobertura_data_prenhes);
                    $data_prenhes_edi = $data_prenhes->format('d/m/Y');
                }

                if ($data_previsao_parto=='') {
                    $data_previsao_parto_edi = '';
                }
                else {
                    $data_previsao_parto = new DateTime($reg_matrizes->tbl_ite_cobertura_previsao_parto);
                    $data_previsao_parto_edi = $data_previsao_parto->format('d/m/Y');
                }

                if ($data_baixa!='') {
                    echo '<tr style="color: red;">';
                }
                else {
                    echo '<tr>';
                }

                echo '<td align="right">'.$codigo_alfa.'</td>';
                echo '<td align="left">'.$codigo_numerico.'</td>';
                echo '<td>'.$data_cobertura_edi.'</td>';
                echo '<td>'.$desc_raca.'</td>';
                echo '<td>'.$numero_partos.'</td>';
                echo '<td>'.$numero_abortos.'</td>';
                echo '<td>'.$idade_ano_mes.'</td>';
                echo '<td>'.$data_prenhes_edi.'</td>';
                echo '<td>'.$data_previsao_parto_edi.'</td>';
                echo "<td width='15%'>";
                echo "<div class='btn-group'>";
                echo "<a class='btn' href='#'>
                    <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir essa fêmea' onClick='excluir_matriz_lista(\"{$cobertura_id}\",\"{$numero_item}\",\"{$codigo_animal_id}\",\"{$codigo_animal}\")'>
                            </i></a>";
                echo "</div>";
                echo "</td>";

                echo '</tr>';
            }
        }

        mysqli_close($conector);
        echo '</tbody>';
        echo '<thead>';
        echo '
        <tr>
        <th>
        <i class="fa fa-sort-alpha-asc"></i></th>
        <th> Nº da Fêmea</th>
        <th> Data</th>
        <th> Raça</th>
        <th> Nº Partos</th>
        <th> Nº Abortos</th>
        <th> Idade mês/ano</th>
        <th> Data Prenhes</th>
        <th> Previsão Parto</th>
        <th><i class="icon_cogs"></i> Ações</th>
        </tr>';

        echo '</thead>';
        echo '</table>';
        echo '</section>';
    }
    else { // Tipo Registro Descarte
        echo '
        <div class="row">
            <div class="form-group col-md-5">
                <label class="control-label">&nbsp;</label>
                <p onclick="modal_inserir_nova_matriz_descarte()" style="color: #1E90FF; cursor: pointer; font-size: 15px;">
                <i class="fa fa-plus"></i>
                &nbsp;Inserir Fêmea</p>
            </div>

            <div class="form-group col-md-5">
            </div>
        </div>';

        echo '<table class="table table-striped table-advance table-hover" id="tabela_lista_matrizes_descarte" style="font-size: 12px">';

        echo '<tbody>';

        $tbl_matrizes = mysqli_query($conector, "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa,
                tbl_animal_codigo_numerico, tab_descricao_raca, tbl_animal_data_nascimento, tbl_animal_baixado_em
            FROM tbl_animais
            INNER JOIN tabela_racas
                    ON tab_codigo_raca = tbl_animal_codigo_raca
            WHERE tbl_animal_ativo='S' AND
                  tbl_animal_codigo_fazenda = '$local_sql' AND
                  tbl_animal_descarte_reproducao='S'
            ORDER BY tbl_animal_codigo_numerico ASC");

        // Carrega o resultado em memória e coleta os códigos de animal para consultas em lote
        $rows_matrizes = array();
        $ids_animal = array();

        while ($reg_matrizes = mysqli_fetch_object($tbl_matrizes)){
            $rows_matrizes[] = $reg_matrizes;
            if ($reg_matrizes->tbl_animal_codigo_id !== null) {
                $ids_animal[$reg_matrizes->tbl_animal_codigo_id] = true;
            }
        }

        $num_rows_matrizes = count($rows_matrizes);

        // Mapas: nº de partos (filhos), nº de abortos e último item de cobertura de descarte
        $mapa_partos = array();
        $mapa_abortos = array();
        $mapa_ultimo_item = array();

        if (count($ids_animal) > 0) {
            $lista_animal = lm_in_list($conector, array_keys($ids_animal));

            $q_partos = mysqli_query($conector, "SELECT tbl_animal_codigo_mae, COUNT(*) AS c FROM tbl_animais
                WHERE tbl_animal_codigo_mae IN ($lista_animal)
                GROUP BY tbl_animal_codigo_mae");
            while ($r = mysqli_fetch_object($q_partos)) {
                $mapa_partos[$r->tbl_animal_codigo_mae] = $r->c;
            }

            $q_abortos = mysqli_query($conector, "SELECT tbl_mov_estoque_codigo_mae, COUNT(*) AS c
                FROM tbl_movimentacao_estoque
                WHERE tbl_mov_estoque_codigo_mae IN ($lista_animal) AND
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='A' AND
                      (tbl_mov_estoque_tipo_movimentacao='A' OR
                       tbl_mov_estoque_tipo_movimentacao='B')
                GROUP BY tbl_mov_estoque_codigo_mae");
            while ($r = mysqli_fetch_object($q_abortos)) {
                $mapa_abortos[$r->tbl_mov_estoque_codigo_mae] = $r->c;
            }

            $q_itens = mysqli_query($conector, "SELECT tbl_ite_cobertura_codigo_id_animal, tbl_cobertura_id,
                    tbl_ite_cobertura_numero_item, tbl_ite_cobertura_codigo_animal, tbl_ite_cobertura_data_emissao
                FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura
                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                  tbl_cobertura_controle='D' AND
                  tbl_ite_cobertura_codigo_id_animal IN ($lista_animal)
                ORDER BY tbl_cobertura_id DESC");
            while ($r = mysqli_fetch_object($q_itens)) {
                // Como está ordenado por tbl_cobertura_id DESC, a 1ª ocorrência de cada animal é a mais recente (equivale ao LIMIT 1 original)
                if (!isset($mapa_ultimo_item[$r->tbl_ite_cobertura_codigo_id_animal])) {
                    $mapa_ultimo_item[$r->tbl_ite_cobertura_codigo_id_animal] = $r;
                }
            }
        }

        if ($num_rows_matrizes!=0) {
            foreach ($rows_matrizes as $reg_matrizes){
                $codigo_animal_id = $reg_matrizes->tbl_animal_codigo_id;
                $codigo_alfa = $reg_matrizes->tbl_animal_codigo_alfa;
                $codigo_numerico = intval($reg_matrizes->tbl_animal_codigo_numerico);
                $desc_raca = $reg_matrizes->tab_descricao_raca;

                $data_nascimento = $reg_matrizes->tbl_animal_data_nascimento;
                $data_baixa = $reg_matrizes->tbl_animal_baixado_em;

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
                $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                $idade_ano_mes = $idade_acompanhamento->format('%Y') .' a/ '. str_pad($idade_acompanhamento->format('%m') , 2 , '0' , STR_PAD_LEFT) . ' m';

                $numero_abortos = 0;
                $numero_abortos = 0;

                $numero_partos = isset($mapa_partos[$codigo_animal_id]) ? (int)$mapa_partos[$codigo_animal_id] : 0;

                $numero_abortos = isset($mapa_abortos[$codigo_animal_id]) ? (int)$mapa_abortos[$codigo_animal_id] : 0;

                if (isset($mapa_ultimo_item[$codigo_animal_id])) {
                    $reg_itens = $mapa_ultimo_item[$codigo_animal_id];
                    $cobertura_id = $reg_itens->tbl_cobertura_id;
                    $numero_item = $reg_itens->tbl_ite_cobertura_numero_item;
                    $codigo_animal = $reg_itens->tbl_ite_cobertura_codigo_animal;
                    $data_cobertura = new DateTime($reg_itens->tbl_ite_cobertura_data_emissao);
                    $data_cobertura_edi = $data_cobertura->format('d/m/Y');
                }
                else {
                    $cobertura_id = 0;
                    $numero_item=0;
                    $codigo_animal=0;
                    $data_cobertura_edi='';
                }

                if ($data_baixa!='') {
                    echo '<tr style="color: red;">';
                }
                else {
                    echo '<tr>';
                }

                echo '<td align="right">'.$codigo_alfa.'</td>';
                echo '<td align="left">'.$codigo_numerico.'</td>';
                echo '<td>'.$data_cobertura_edi.'</td>';
                echo '<td>'.$desc_raca.'</td>';
                echo '<td>'.$numero_partos.'</td>';
                echo '<td>'.$numero_abortos.'</td>';
                echo '<td>'.$idade_ano_mes.'</td>';
                echo "<td width='15%'>";
                echo "<div class='btn-group'>";
                echo "<a class='btn' href='#'>
                        <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir essa fêmea' onClick='excluir_matriz_lista(\"{$cobertura_id}\",\"{$numero_item}\",\"{$codigo_animal_id}\",\"{$codigo_animal}\")'>
                        </i></a>";
                echo "</div>";
                echo "</td>";
                echo '</tr>';
            }
        }

        mysqli_close($conector);
        echo '</tbody>';
        echo '<thead>';
        echo '
        <tr>
        <th>
        <i class="fa fa-sort-alpha-asc"></i></th>
        <th> Nº da Fêmea</th>
        <th> Descarte em</th>
        <th> Raça</th>
        <th> Nº Partos</th>
        <th> Nº Abortos</th>
        <th> Idade mês/ano</th>
        <th><i class="icon_cogs"></i> Ações</th>
        </tr>';

        echo '</thead>';
        echo '</table>';
        echo '</section>';

    }
?>

<script src="js/matrizes.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

</body>
</html>


