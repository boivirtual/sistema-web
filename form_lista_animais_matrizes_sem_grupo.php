<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";

    $data_hoje = date("Y-m-d");
    $controle = 'C';
    $animais_listados = 0;

    if (isset($_POST['id_cobertura'])) {
        $id_cobertura = $_POST['id_cobertura'];

        $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_cobertura_id = '$id_cobertura'");

        $num_row = mysqli_num_rows($tbl_cobertura);

        if ($num_row!=0) {
            $reg_item = mysqli_fetch_object($tbl_cobertura);
            $controle = $reg_item->tbl_cobertura_controle;
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
  <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style-responsive.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

  <style>
      .table_overflow table thead th{
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

      #tabela_matrizes_wrapper{
          width: 100% !important;
          /*overflow-x: scroll !important;*/
          overflow-y: scroll !important;
          max-height: 300px;
      }

      #tabela_matrizes_filter{
          float: right;
      }
  </style>

</head>

<body> 

<?php
	echo '<section class="panel table-responsive">';
    
    //echo '<table class="table table-striped table-advance table-hover" style="font-size: 11px; width: 100%;" id="tabela_matrizes">';

    if ($controle=='C') {
        echo '<table class="table table-striped table-advance table-hover" style="font-size: 11px; width: 100%;" id="tabela_matrizes">';
    }
    else {
        echo '<table class="table table-striped table-advance table-hover" style="font-size: 11px; width: 100%;" id="tabela_matrizes_monta">';
    }
                          
    echo '<tbody>';

    if ($id_cobertura!=0) {
        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
            INNER JOIN tbl_cobertura
                    on tbl_cobertura_id = tbl_ite_cobertura_numero_id 
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_ite_cobertura_numero_id = '$id_cobertura'");

        $num_row = mysqli_num_rows($tbl_item_cobertura);

        if ($num_row!=0) {
            while ($reg_item = mysqli_fetch_object($tbl_item_cobertura)){
                $codigo_id = $reg_item->tbl_ite_cobertura_codigo_id_animal;
                $codigo_ed = $reg_item->tbl_ite_cobertura_codigo_animal;
                $codigo_alfa= $reg_item->tbl_ite_cobertura_codigo_alfa;
                $codigo_numerico= intval($reg_item->tbl_ite_cobertura_codigo_numerico);
                $id_parametro_estacao = $reg_item->tbl_cobertura_codigo_estacao_monta;
                $local = $reg_item->tbl_cobertura_codigo_local;
                $numero_partos = 0;
                $desc_raca = '';
                $numero_abortos = 0;
                $idade = 0;
                $ultimo_parto_edi = '';
                $data_aptidao_edi = '';
                $descricao_pai = '';
                $coberturas_estacao = 0;
                $data_aborto = '0000-00-00';
                $data_natimorto = '0000-00-00';
                $ultimo_parto = '0000-00-00';

                $sql = mysqli_query($conector, "SELECT * FROM tbl_animais 
                                WHERE tbl_animal_codigo_id = '$codigo_id'"); 
                $num_row = mysqli_num_rows($sql);

                if ($num_row!=0) {
                    $reg_animal = mysqli_fetch_object($sql);
                    $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
                    $codigo_raca= $reg_animal->tbl_animal_codigo_raca;
                    $descarte = $reg_animal->tbl_animal_descarte_reproducao;

                    $tbl_raca = mysqli_query($conector,"SELECT * FROM tabela_racas 
                        WHERE tab_codigo_raca ='$codigo_raca' AND 
                              tab_registro_lixeira_raca = 0"); 
                    $num_row_raca = mysqli_num_rows($tbl_raca);

                    if ($num_row_raca!=0) {
                        $reg_raca = mysqli_fetch_object($tbl_raca);
                        $desc_raca = $reg_raca->tab_descricao_raca;
                    }
                    else {
                        $desc_raca = '';
                    }

                    $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
                    $idade_ano_mes = $idade_acompanhamento->format('%Y') .' a '. 
                    str_pad($idade_acompanhamento->format('%m') , 2 , '0' , STR_PAD_LEFT) . ' m';
                }

                $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura
                        INNER JOIN tbl_item_cobertura 
                                on tbl_ite_cobertura_numero_id = tbl_cobertura_id
                        WHERE tbl_cobertura_lixeira=0 AND 
                              tbl_cobertura_codigo_local = '$local' AND 
                              tbl_cobertura_codigo_estacao_monta='$id_parametro_estacao' AND 
                              tbl_ite_cobertura_codigo_id_animal='$codigo_id' AND 
                              (tbl_ite_cobertura_resultado_diagnostico='P' or 
                               tbl_ite_cobertura_resultado_diagnostico='N')");

                $coberturas_estacao = mysqli_num_rows($tbl_cobertura);

                $tbl_filhos = mysqli_query($conector,"SELECT * FROM tbl_animais 
                                    WHERE tbl_animal_codigo_mae='$codigo_id'
                                    ORDER BY tbl_animal_codigo_numerico ASC"); 
                $numero_partos = mysqli_num_rows($tbl_filhos);

                if ($numero_partos!=0) {
                    while ($reg_filhos = mysqli_fetch_object($tbl_filhos)){
                        $codigo_pai=$reg_filhos->tbl_animal_codigo_pai;
                        $bezerro_ativo = $reg_filhos->tbl_animal_ativo;
                        $ultimo_parto=new DateTime($reg_filhos->tbl_animal_data_nascimento);
                        $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');

                        $data_aptidao_edi = date("d/m/Y", strtotime($reg_filhos->tbl_animal_data_nascimento . "+ 35 days"));

                        $ultimo_parto=$reg_filhos->tbl_animal_data_nascimento;

                        $data_nascimento= $reg_filhos->tbl_animal_data_nascimento;
                        $data_acompanhamento_calculo = date("Y-m-d");
                        $date = new DateTime($data_nascimento); // Data de Nascimento
                        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade_bezerro = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
                    }
                }
                else {
                    $codigo_pai = 0;
                }

                $tab_pai = mysqli_query($conector, "SELECT * FROM tbl_semem WHERE tbl_semem_codigo_id='$codigo_pai'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_semem_nome;
                }
                else {
                    $tab_pai = mysqli_query($conector, "SELECT * FROM tbl_animais WHERE tbl_animal_codigo_id='$codigo_pai'");
                    $num_rows_pai = mysqli_num_rows($tab_pai);

                    if ($num_rows_pai!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                    }
                    else {
                        $descricao_pai = '';
                    }
                }

                // verifica NatMorto
                $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                    WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
                          tbl_mov_estoque_codigo_id_animal=999999999 AND
                          tbl_mov_estoque_entrada_saida='S' AND 
                          tbl_mov_estoque_tipo_movimentacao='M'
                    ORDER BY tbl_mov_estoque_nascimento DESC");

                $num_natimorto = mysqli_num_rows($tbl_natimorto);

                if ($num_natimorto==0) {
                    $data_natimorto = '0000-00-00';
                }
                else {
                    $reg_natimorto = mysqli_fetch_object($tbl_natimorto);

                    $data_natimorto=$reg_natimorto->tbl_mov_estoque_nascimento;
                    $descricao_pai = '';
                    $numero_partos+=$num_natimorto; 
                }

                // verifica Aborto/Absorção
                $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                    WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
                          tbl_mov_estoque_codigo_id_animal=999999999 AND
                          tbl_mov_estoque_entrada_saida='A' AND 
                          (tbl_mov_estoque_tipo_movimentacao='A' OR
                           tbl_mov_estoque_tipo_movimentacao='B') 
                    ORDER BY tbl_mov_estoque_nascimento DESC");

                $num_aborto = mysqli_num_rows($tbl_aborto);

                if ($num_aborto==0) {
                    $data_aborto = '0000-00-00';
                }
                else {
                    $reg_aborto = mysqli_fetch_object($tbl_aborto);
                    $data_aborto=$reg_aborto->tbl_mov_estoque_nascimento;
                    $descricao_pai = '';
                }

                if ($data_natimorto>$ultimo_parto || $data_aborto>$ultimo_parto) {
                    if ($data_natimorto!='0000-00-00') {
                        $ultimo_parto=new DateTime($reg_natimorto->tbl_mov_estoque_nascimento);
                        $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');

                        $data_aptidao_edi = date("d/m/Y", strtotime($reg_natimorto->tbl_mov_estoque_nascimento . "+ 35 days"));

                        $ultimo_parto=$reg_natimorto->tbl_mov_estoque_nascimento;
                    }
                    else if ($data_aborto!='000-00-00') {
                        $ultimo_parto=new DateTime($reg_aborto->tbl_mov_estoque_nascimento);
                        $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');

                        $ultimo_parto=$reg_aborto->tbl_mov_estoque_nascimento;

                        $data_aptidao_edi = date("d/m/Y", strtotime($reg_aborto->tbl_mov_estoque_nascimento . "+ 35 days"));
                    }
                }

                echo "<tr>";

                if ($controle=='C') {
                    if ($descarte=='S') {
                        echo "<td width='16%' style='color: red;'>DESCARTADA</td>";
                    }
                    else {
                        echo "<td width='16%'>";
                        echo '<select class="form-control grupo_select" name="grupo_select" style="height: 2.1em;" onchange="somar_selecionados()">';

                        echo '<option value="000">...</option>';

                            $grupo_estacao = mysqli_query($conector, "SELECT * FROM tbl_grupo_estacao_monta 
                                    WHERE tbl_grupo_codigo_estacao_monta ='$id_parametro_estacao' AND 
                                          tbl_grupo_codigo_local='$local'
                                    ORDER BY tbl_grupo_id  ASC"); 
                            $num_grupo = mysqli_num_rows($grupo_estacao);

                            if ($num_grupo!=0) {
                                while ($reg_grupo = mysqli_fetch_object($grupo_estacao)){
                                    $codigo_grupo = $reg_grupo->tbl_grupo_id;
                                    $desc_grupo = $reg_grupo->tbl_grupo_descricao;

                                    $tbl_cobertura = mysqli_query($conector,"SELECT * FROM tbl_cobertura 
                                        WHERE tbl_cobertura_lixeira=0 AND 
                                              tbl_cobertura_codigo_grupo='$codigo_grupo' AND 
                                              tbl_cobertura_codigo_estacao_monta='$id_parametro_estacao' AND 
                                              tbl_cobertura_codigo_local='$local'"); 

                                    $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);

                                    if ($num_rows_cobertura==0 || $codigo_grupo==999) {
                                        echo '<option value="'.$codigo_grupo.'">' .$desc_grupo. '</option>';
                                    }
                                    else if ($num_rows_cobertura!=0) {
                                        $reg_cobertura = mysqli_fetch_object($tbl_cobertura);
                                        $qtd_animais = trim($reg_cobertura->tbl_cobertura_qtd_animais, "0");
                                        $protocolo_iatf = $reg_cobertura->tbl_cobertura_protocoloiatf;

                                        if ($protocolo_iatf==0) {
                                            echo '<option value="'.$codigo_grupo.'">' .$desc_grupo. ' - ' . $qtd_animais .' Fêmea(s)</option>';
                                        }
                                    }
                                }
                            }
                        echo '</select>';
                        echo "</td>";
                    }

                    echo "<td>".$codigo_alfa."</td>";
                    echo "<td width='9%'> 
                        <input type='hidden' name='id_animal' value='".$codigo_id."'>" .$codigo_numerico. "</td>";
                    echo "<td width='11%'>".$desc_raca."</td>";
                    echo "<td width='9%'>".$idade_ano_mes."</td>";
                    echo "<td width='8%' align='center'>".$numero_partos."</td>";
                    echo "<td width='8%' align='center'>".$numero_abortos."</td>";
                    echo "<td width='9%'>".$ultimo_parto_edi."</td>";
                    echo "<td width='9%'>".$descricao_pai."</td>";
                    echo "<td width='9%'>".$data_aptidao_edi."</td>";
                    echo "<td width='12%' align='center'>".$coberturas_estacao."</td>";

                    echo "</tr>";
                    $animais_listados++;
                }
                else {
                    if ($descarte=='S') {
                        echo "<td width='4%' style='color: red;' >DESCARTADA</td>";
                        echo "<td width='4%'>".$codigo_alfa."</td>";
                        echo "<td width='8%'>" .$codigo_numerico. "</td>";
                    }
                    else {
                        echo '<td width="4%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value="'.$codigo_id.'"></td>';

                        echo "<td width='4%'>".$codigo_alfa."</td>";
                        echo "<td width='8%'>".$codigo_numerico. "</td>";
                    }
                    echo "<td width='11%'>".$desc_raca."</td>";
                    echo "<td width='9%'>".$idade_ano_mes."</td>";
                    echo "<td width='8%' align='center'>".$numero_partos."</td>";
                    echo "<td width='8%' align='center'>".$numero_abortos."</td>";
                    echo "<td width='9%'>".$ultimo_parto_edi."</td>";
                    echo "<td width='9%'>".$descricao_pai."</td>";
                    echo "<td width='9%'>".$data_aptidao_edi."</td>";

                    echo "</tr>";
                    $animais_listados++;
                }
            }
        }
    }   
    mysqli_close($conector);

    echo '</tbody>';

/*    echo '<thead>
        <tr>
            <div class="row" id="total_contas">
                <div class="form-group col-md-2">
                    <label class="control-label">Fêmeas Listadas</label>
                    <input class="form-control" type="text" id="femeas_listadas" readonly="" value="'.$animais_listados.'">
                </div>

                <div class="form-group col-md-2">
                    <label class="control-label">Fêmeas Selecionadas</label>
                    <input class="form-control" type="text" id="total_selecionados" readonly="">
                </div>

                <div class="form-group col-md-3">
                    <label class="control-label">&nbsp;</label>
                    <button type="button" class="form-control btn btn-success confirma_gravar" onClick="confirmar_grupos()">Confirmar Fêmeas Selecionadas</button>
                </div>

                <div class="col-md-5">
                    <label class="control-label">&nbsp;</label>
                    <p class="grupos_estacao"  
                        onclick="modal_grupo_estacao()" style="color: #1E90FF; cursor: pointer; font-size: 15px;">
                        <i class="fa fa-edit"></i>
                        &nbsp;Incluir/Editar Grupos</p>
                </div>
            </div>
        </tr>

        <tr>
            <th> Grupo</th>
            <th> #</th>
            <th> Nº da Fêmea</th>
            <th> Raça</th>
            <th> Idade (ano/mes)</th>
            <th> Nº de Partos</th>
            <th> Nº de Abortos</th>
            <th> Último Parto</th>
            <th> Pai do último parto</th>
            <th> Data Aptidão</th>
            <th style="vertical-align: middle;text-align:center;"> Coberturas nessa estação</th>
        </tr>
        </thead>';*/

    if ($controle=='C') {
        echo '<thead>
            
            <tr>
                <div class="row" id="total_contas">
                    <div class="form-group col-md-2">
                        <label class="control-label">Fêmeas Listadas</label>
                        <input class="form-control" type="text" id="femeas_listadas" readonly="" value="'.$animais_listados.'">
                    </div>

                    <div class="form-group col-md-2">
                        <label class="control-label">Fêmeas Selecionadas</label>
                        <input class="form-control" type="text" id="total_selecionados" readonly="">
                    </div>

                    <div class="form-group col-md-3">
                        <label class="control-label">&nbsp;</label>
                        <button type="button" class="form-control btn btn-success confirma_gravar" onClick="confirmar_grupos()">Confirmar Fêmeas Selecionadas</button>
                    </div>

                    <div class="col-md-5">
                        <label class="control-label">&nbsp;</label>
                        <p class="grupos_estacao"  
                            onclick="modal_grupo_estacao()" style="color: #1E90FF; cursor: pointer; font-size: 15px;">
                            <i class="fa fa-edit"></i>
                            &nbsp;Incluir/Editar Grupos</p>
                    </div>
                </div>
            </tr>

            <tr>
                <th> Grupo</th>
                <th> <i class="fa fa-sort-alpha-asc"</th>
                <th> Nº da Fêmea</th>
                <th> Raça</th>
                <th> Idade (ano/mes)</th>
                <th> Nº de Partos</th>
                <th> Nº de Abortos</th>
                <th> Último Parto</th>
                <th> Pai do último parto</th>
                <th> Data Aptidão</th>
                <th style="vertical-align: middle;text-align:center;"> Coberturas nessa estação</th>
            </tr>
            </thead>';
    }
    else {
        echo '<thead>
            <tr>
                <div class="row" id="total_contas">
                    <div class="form-group col-md-2">
                        <label class="control-label">Fêmeas Listadas</label>
                        <input class="form-control" type="text" id="femeas_listadas" readonly="" value="'.$animais_listados.'">
                    </div>

                    <div class="form-group col-md-2">
                        <label class="control-label">Fêmeas Selecionadas</label>
                        <input class="form-control" type="text" id="total_selecionados" readonly="">
                    </div>

                    <div class="form-group col-md-3">
                        <label class="control-label">&nbsp;</label>
                        <button type="button" class="form-control btn btn-success confirma_gravar" onClick="confirmar_femeas_monta()">Confirmar Fêmeas Selecionadas</button>
                    </div>
                </div>
            </tr>

            <tr>
                <th><input type="checkbox" class="seleciona_todos" data-toggle="tooltip" data-placement="right" title="Selecionar Todos"></th>
                <th> <i class="fa fa-sort-alpha-asc"</th>
                <th> Nº da Fêmea</th>
                <th> Raça</th>
                <th> Idade (ano/mes)</th>
                <th> Nº de Partos</th>
                <th> Nº de Abortos</th>
                <th> Último Parto</th>
                <th> Pai do último parto</th>
                <th> Data Aptidão</th>
            </tr>
            </thead>';
    }

   echo '</table>';

   echo '</section>';
?>

    <script src="js/matrizes.js" charset="utf-8" type="text/javascript" ></script>

    <script>
        $(document).ready(function() {
            var table = $('#tabela_matrizes').DataTable( {
                sDom: 'lfr<"table_overflow"t>ip',
                paging:   false,
                search:   true,
                info: true,
                ordering: true,
                "columns": [
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    { "type": "date-br" },
                    null,
                    { "type": "date-br" },
                    null
                ],
                language: {
                  sSearch: "Buscar na lista:",
                  zeroRecords: "Nada encontrado",
                  info: "Animais listados: _END_ ",
                  infoEmpty: "Nenhum registro disponível",
                  infoFiltered: "(filtrado de _MAX_ registros no total)",
                },
                order: [[ 2, "asc" ]],
            });
        });


        $(document).ready(function() {
            var table = $('#tabela_matrizes_monta').DataTable( {
                sDom: 'lfr<"table_overflow"t>ip',
                paging:   false,
                search:   true,
                info: true,
                ordering: true,
                "columns": [
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    { "type": "date-br" },
                    null,
                    { "type": "date-br" }
                ],
                language: {
                  sSearch: "Buscar na lista:",
                  zeroRecords: "Nada encontrado",
                  info: "Animais listados: _END_ ",
                  infoEmpty: "Nenhum registro disponível",
                  infoFiltered: "(filtrado de _MAX_ registros no total)",
                },
                order: [[ 2, "asc" ]],
            });
        });

        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });

        jQuery.extend(jQuery.fn.dataTableExt.oSort, {
            "date-br-pre": function ( a ) {
                if (a == null || a == "") {
                    return 0;
                }
                var brDatea = a.split('/');
                return (brDatea[2] + brDatea[1] + brDatea[0]) * 1;
            },

            "date-br-asc": function ( a, b ) {
                return ((a < b) ? -1 : ((a > b) ? 1 : 0));
            },

            "date-br-desc": function ( a, b ) {
                return ((a < b) ? 1 : ((a > b) ? -1 : 0));
            }
        });
    </script>

</body>
</html> 

                
                
