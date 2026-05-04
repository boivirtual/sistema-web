<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";

    $data_hoje = date("Y-m-d");

    if (isset($_POST['id_cobertura'])) {
        $id_cobertura = $_POST['id_cobertura'];
    }
    else {
        $local = ltrim($_POST['local']);
        $vacas_paridas = $_POST['vacas_paridas'];
        $data_paridas_ate = $_POST['data_paridas'];
        $vacas_solteiras = $_POST['vacas_solteiras'];
        $novilhas = $_POST['novilhas'];
        $idade_de = $_POST['idade_de'];
        $idade_ate = $_POST['idade_ate'];
        $peso_acima = $_POST['peso_acima'];
        $id_parametro_estacao = $_POST['id_estacao_monta'];
        $id_cobertura = 0;

        if ($idade_ate=='') {
            $idade_ate=9999;
        }

        if ($peso_acima==0) {
            $peso_acima=1;
        }

        $peso_acima = floatval($peso_acima);

        if ($data_paridas_ate=='') {
          $data_paridas_ate='9999-99-99';
          $data_paridas_de='0000-00-00';
        }
        else {
            $data_paridas_de = date("Y-m-d", strtotime('-8 month',strtotime($data_paridas_ate)));
            $data_paridas_de = date("Y-m-d", strtotime('-1 day',strtotime($data_paridas_de)));
        }

       // pega periodo da estacao de monta atual

        $tbl_estacao_monta = mysqli_query($conector, "select * from tbl_parametro_estacao_monta
            where tbl_par_estacao_id = '$id_parametro_estacao'");

        $num_row = mysqli_num_rows($tbl_estacao_monta);

        if ($num_row!=0) {
            $reg_estacao = mysqli_fetch_object($tbl_estacao_monta);
            $inicio_estacao = $reg_estacao->tbl_par_estacao_monta_inicial;
            $fim_estacao = $reg_estacao->tbl_par_estacao_monta_final;
        }
        else {
            $inicio_estacao = '0000-00-00';
            $fim_estacao = '9999-99-99';
        }
    }

    $animais_listados = 0;
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
    echo '<table class="table table-striped table-advance table-hover" style="font-size: 11px; width: 100%;" id="tabela_matrizes">';
                          
    echo '<tbody>';

    if ($id_cobertura!=0) {
        $tbl_item_cobertura = mysqli_query($conector, "select * from tbl_item_cobertura
            inner join tbl_cobertura
                    on tbl_cobertura_id = tbl_ite_cobertura_numero_id 
            where tbl_ite_cobertura_numero_id = '$id_cobertura'");

        $num_row = mysqli_num_rows($tbl_item_cobertura);

        if ($num_row!=0) {
            while ($reg_item = mysqli_fetch_object($tbl_item_cobertura)){
                $codigo_id = $reg_item->tbl_ite_cobertura_codigo_id_animal;
                $codigo_ed = $reg_item->tbl_ite_cobertura_codigo_animal;
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

                $sql = mysqli_query($conector, "select * from tbl_animais 
                                where tbl_animal_codigo_id = '$codigo_id'"); 
                $num_row = mysqli_num_rows($sql);

                if ($num_row!=0) {
                    $reg_animal = mysqli_fetch_object($sql);
                    $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
                    $codigo_raca= $reg_animal->tbl_animal_codigo_raca;

                    $tbl_raca = mysqli_query($conector,"select * from tabela_racas 
                        where tab_codigo_raca ='$codigo_raca' and 
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

                $tbl_aborto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                    where tbl_mov_estoque_codigo_mae='$codigo_id' and 
                          tbl_mov_estoque_codigo_id_animal=999999999 and 
                          tbl_mov_estoque_entrada_saida='A'");

                $numero_abortos = mysqli_num_rows($tbl_aborto);

                $tbl_cobertura = mysqli_query($conector, "select * from tbl_cobertura
                        inner join tbl_item_cobertura 
                                on tbl_ite_cobertura_numero_id = tbl_cobertura_id
                        where tbl_cobertura_codigo_local = '$local' and 
                              tbl_cobertura_codigo_estacao_monta = '$id_parametro_estacao' and 
                              tbl_ite_cobertura_codigo_id_animal='$codigo_id' and 
                              (tbl_ite_cobertura_resultado_diagnostico='P' or 
                               tbl_ite_cobertura_resultado_diagnostico='N')");

                $coberturas_estacao = mysqli_num_rows($tbl_cobertura);

                $tbl_filhos = mysqli_query($conector,"select * from tbl_animais 
                                    where tbl_animal_codigo_mae='$codigo_id'
                                    order by tbl_animal_codigo_numerico ASC"); 
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

                $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$codigo_pai'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_semem_codigo_alfa;
                }
                else {
                    $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$codigo_pai'");
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

                    // INFORMAR O PAI DO NATIMORTO AQUI
                    // COPIAR ESSA ROTINA NO RELATORIO EXCEL
                }

                echo "<tr>";

                echo "<td width='16%'>";
                echo '<select class="form-control grupo_select" name="grupo_select" style="height: 2.1em;" onchange="somar_selecionados()">';

                echo '<option value="000">...</option>';

                    $grupo_estacao = mysqli_query($conector, "select * from tbl_grupo_estacao_monta 
                            where tbl_grupo_codigo_estacao_monta ='$id_parametro_estacao' and 
                                  tbl_grupo_codigo_local='$local'
                            order by tbl_grupo_id  ASC"); 
                    $num_grupo = mysqli_num_rows($grupo_estacao);

                    if ($num_grupo!=0) {
                        while ($reg_grupo = mysqli_fetch_object($grupo_estacao)){
                            $codigo_grupo = $reg_grupo->tbl_grupo_id;
                            $desc_grupo = $reg_grupo->tbl_grupo_descricao;

                            $tbl_cobertura = mysqli_query($conector,"select * from tbl_cobertura 
                                where tbl_cobertura_codigo_grupo='$codigo_grupo' and 
                                      tbl_cobertura_codigo_estacao_monta='$id_parametro_estacao' and 
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
                echo "<td width='9%'> 
                              <input type='hidden' name='id_animal' value='".$codigo_id."'>" .$codigo_ed. "</td>";
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
        }
    }   
    else {
        $sql = mysqli_query($conector, "select * from tbl_animais
            where tbl_animal_sexo='F' and 
                  tbl_animal_ativo='S' and
                  tbl_animal_lixeira=0 and 
                  tbl_animal_codigo_fazenda='$local'
            order by tbl_animal_codigo_numerico ASC"); 
        $num_row = mysqli_num_rows($sql);

        if ($num_row!=0) {
            while ($reg_animal = mysqli_fetch_object($sql)){
                $codigo_id= $reg_animal->tbl_animal_codigo_id;
                $codigo_alfa= $reg_animal->tbl_animal_codigo_alfa;
                $codigo_numerico= $reg_animal->tbl_animal_codigo_numerico;
                $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
                $ultimo_peso=  floatval($reg_animal->tbl_animal_ultimo_peso);
                $codigo_raca= $reg_animal->tbl_animal_codigo_raca;
                $prenhe = $reg_animal->tbl_animal_prenhe;
                $descarte = $reg_animal->tbl_animal_descarte_reproducao;

                $tbl_raca = mysqli_query($conector,"select * from tabela_racas 
                                    where tab_codigo_raca ='$codigo_raca' and 
                                          tab_registro_lixeira_raca = 0"); 
                $num_row_raca = mysqli_num_rows($tbl_raca);

                if ($num_row_raca!=0) {
                    $reg_raca = mysqli_fetch_object($tbl_raca);
                    $desc_raca = $reg_raca->tab_descricao_raca;
                }
                else {
                    $desc_raca = '';
                }

                if ($codigo_alfa=='') {
                    $codigo_ed = $codigo_numerico;
                }
                else {
                    $codigo_ed = $codigo_alfa.'-'.$codigo_numerico;
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

                $numero_abortos = 0;
                $dias_ultimo_parto = 0;
                $coberturas_estacao = 0;
                $descricao_pai = '';
                $ultimo_parto_edi='';
                $data_aptidao_edi='';
                $idade_bezerro=0;
                $bezerro_ativo='';
                $data_aborto = '0000-00-00';
                $data_natimorto = '0000-00-00';
                $ultimo_parto = '0000-00-00';
                $dias_natimorto = '';
                $dias_aborto = 0;
                $num_aborto = 0;
                $num_natimorto = 0;

                if (($vacas_paridas=='VP' || $novilhas=='NO' || $vacas_solteiras=='VS') || ($vacas_paridas=='' && $novilhas=='' && $vacas_solteiras=='' && $idade>=12)) {

                    //VERIFICA SE A FÊMEA JA FOI SELECIONADA NESSA ESTACAO

                    $femea_selecionada = '';

                    $tbl_selecao = mysqli_query($conector, "select * from tbl_cobertura
                        inner join tbl_item_cobertura 
                                on tbl_ite_cobertura_numero_id = tbl_cobertura_id
                        where tbl_cobertura_codigo_local = '$local' and 
                              tbl_cobertura_codigo_estacao_monta = '$id_parametro_estacao' and 
                              tbl_ite_cobertura_codigo_id_animal='$codigo_id'
                              order by tbl_ite_cobertura_numero_id DESC LIMIT 1");

                    $selecionada_estacao = mysqli_num_rows($tbl_selecao);

                    if ($selecionada_estacao!=0) {

                        $femea_selecionada = 'S';

                        $reg_selecao = mysqli_fetch_object($tbl_selecao);
                        $diagnostico_selecao = $reg_selecao->tbl_ite_cobertura_resultado_diagnostico;

                        if ($diagnostico_selecao=='N') {
                            $femea_selecionada = '';
                        }
                    } 

                    $tbl_aborto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                        where tbl_mov_estoque_codigo_mae='$codigo_id' and 
                          tbl_mov_estoque_codigo_id_animal=999999999 and 
                          tbl_mov_estoque_entrada_saida='A'");

                    $numero_abortos = mysqli_num_rows($tbl_aborto);

                    $tbl_cobertura = mysqli_query($conector, "select * from tbl_cobertura
                        inner join tbl_item_cobertura 
                                on tbl_ite_cobertura_numero_id = tbl_cobertura_id
                        where tbl_cobertura_codigo_local = '$local' and 
                              tbl_cobertura_codigo_estacao_monta = '$id_parametro_estacao' and 
                              tbl_ite_cobertura_codigo_id_animal='$codigo_id' and 
                              (tbl_ite_cobertura_resultado_diagnostico='P' or 
                               tbl_ite_cobertura_resultado_diagnostico='N')");

                    $coberturas_estacao = mysqli_num_rows($tbl_cobertura);

                    // verifica vaca prenhe
                    $tbl_prenhe = mysqli_query($conector, "select * from tbl_cobertura
                        inner join tbl_item_cobertura 
                                on tbl_ite_cobertura_numero_id = tbl_cobertura_id
                        where tbl_ite_cobertura_codigo_id_animal='$codigo_id' and 
                              tbl_ite_cobertura_resultado_diagnostico='P' and 
                              (tbl_ite_cobertura_nascido='' or 
                               tbl_ite_cobertura_nascido is null)");

                    $num_rows_prenhe = mysqli_num_rows($tbl_prenhe);

                    if ($num_rows_prenhe!=0) {
                        $prenhe='S';
                    }
                    else {
                        $prenhe='';
                    }

                    $tbl_filhos = mysqli_query($conector,"select * from tbl_animais 
                        where tbl_animal_codigo_mae='$codigo_id'
                        order by tbl_animal_codigo_numerico ASC"); 
                    $numero_partos = mysqli_num_rows($tbl_filhos);

                    if ($numero_partos!=0) {
                        while ($reg_filhos = mysqli_fetch_object($tbl_filhos)){
                            $codigo_pai=$reg_filhos->tbl_animal_codigo_pai;
                            $bezerro_ativo = $reg_filhos->tbl_animal_ativo;
                            $bezerro_situacao = $reg_filhos->tbl_animal_situacao;

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

                            if ($bezerro_ativo=='N' && $bezerro_situacao=='M') {
                                $data_inicial = $reg_filhos->tbl_animal_baixado_em;
                                $data_final = date("Y-m-d");
                                $diferenca = strtotime($data_final) - 
                                     strtotime($data_inicial);
                                $dias_natimorto = floor($diferenca / (60 * 60 * 24));

                                if ($dias_natimorto<=0) {
                                    $dias_natimorto=1;
                                }
                            }
                        }
                    }
                    else {
                        $codigo_pai = 0;
                    }

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$codigo_pai'");
                    $num_rows_pai = mysqli_num_rows($tab_pai);

                    if ($num_rows_pai!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai = $reg->tbl_semem_codigo_alfa;
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$codigo_pai'");
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

                        $data_inicial = $reg_natimorto->tbl_mov_estoque_nascimento;
                        $data_final = date("Y-m-d");
                        $diferenca = strtotime($data_final) - 
                                     strtotime($data_inicial);
                        $dias_natimorto = floor($diferenca / (60 * 60 * 24));
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

                        $data_inicial = $reg_aborto->tbl_mov_estoque_nascimento;
                        $data_final = date("Y-m-d");
                        $diferenca = strtotime($data_final) - 
                                     strtotime($data_inicial);
                        $dias_aborto = floor($diferenca / (60 * 60 * 24));

                        if ($data_aborto>=$inicio_estacao && $data_aborto<=$fim_estacao) {
                            $dias_aborto=0;
                        }
                    }
                }
                else {
                    $descricao_pai = '';
                    $numero_partos='';
                    $numero_abortos='';
                    $ultimo_parto_edi='';
                    $data_aptidao_edi = '';
                    $idade_bezerro=0;
                    $bezerro_ativo='';
                    $coberturas_estacao = 0;
                    $data_aborto = '0000-00-00';
                    $data_natimorto = '0000-00-00';
                    $ultimo_parto = '0000-00-00';
                    $dias_natimorto = '';
                    $dias_aborto = 0;
                    $num_aborto = 0;
                    $num_natimorto = 0;
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


                    // INFORMAR O PAI DO NATIMORTO AQUI
                    // COPIAR ESSA ROTINA NO RELATORIO EXCEL
                }

                /*$solteira = '';
                $parida = '';
                $novilha = '';

                // Seleção de solteiras
                // 1º caso
                if ($numero_partos!=0 && $idade_bezerro>=8 && 
                    $bezerro_ativo=='S' && 
                    $teve_aborto_apos_nascimento=='') {
                    $solteira = 'S';
                }

                // 2º caso
                if ($numero_partos!=0 && $idade_bezerro>=8 && 
                    $bezerro_ativo=='S') {

                    if ($teve_aborto_apos_nascimento=='S')

                }  
                    $teve_aborto_apos_nascimento=='' {
                    $solteira = 'S';
                }
                // Fim seleção de solteiras


                // Seleção de paridas
                // Fim seleção de paridas

                // Seleção de novilhas
                if ($idade>=$idade_de && $idade<=$idade_ate && 
                    $numero_partos==0 && $dias_aborto==0 && 
                    $dias_natimorto=='') {
                    $novilha = 'S';
                }
                // Fim seleção de novilhas
                */

                if ( ($vacas_paridas=='VP' && $ultimo_parto>=$data_paridas_de && 
                      $ultimo_parto<=$data_paridas_ate && $numero_partos!=0 && $prenhe!='S') ||

                     ($novilhas=='NO' && $idade>=$idade_de && $idade<=$idade_ate && $numero_partos==0 && $dias_aborto==0 && $prenhe!='S') || 

                     ($vacas_paridas=='' && $novilhas=='' && $vacas_solteiras=='' && $idade>=12 && $prenhe!='S') ||

                     ($vacas_solteiras=='VS' && $numero_partos!=0 && $idade_bezerro>=8 && $prenhe!='S' && ($dias_aborto==0 || $dias_aborto>35) && ($dias_natimorto=='' || $dias_natimorto>35)) || 

                     ($vacas_solteiras=='VS' && $numero_partos!=0 && $idade_bezerro<8 && $bezerro_ativo=='N' && $prenhe!='S' && ($dias_aborto==0 || $dias_aborto>35) && ($dias_natimorto=='' || $dias_natimorto>35)) 
                     ||

                     ($vacas_solteiras=='VS' && $prenhe!='S' && $dias_aborto>35) 

                    ) {

                    if ($ultimo_peso>=$peso_acima && $femea_selecionada=='') {
                        echo "<tr>";

                        if ($descarte=='S') {
                            //echo "<td width='16%'>Descartada</td>";
                            echo "<td width='16%'>";
                            echo '<select class="form-control grupo_select" name="grupo_select" style="height: 2.1em;">';

                            echo '<option value="000">Descartada</option>';
                            echo '</select>';
                        }
                        else {
                            echo "<td width='16%'>";
                            echo '<select class="form-control grupo_select" name="grupo_select" style="height: 2.1em;" onchange="somar_selecionados()">';

                            echo '<option value="000">...</option>';

                            $grupo_estacao = mysqli_query($conector, "select * from tbl_grupo_estacao_monta 
                                where tbl_grupo_codigo_estacao_monta ='$id_parametro_estacao' and 
                                      tbl_grupo_codigo_local='$local'
                                order by tbl_grupo_id  ASC"); 
                            $num_grupo = mysqli_num_rows($grupo_estacao);

                            if ($num_grupo!=0) {
                                while ($reg_grupo = mysqli_fetch_object($grupo_estacao)){
                                    $codigo_grupo = $reg_grupo->tbl_grupo_id;
                                    $desc_grupo = $reg_grupo->tbl_grupo_descricao;

                                    $tbl_cobertura = mysqli_query($conector,"select * from tbl_cobertura 
                                        where tbl_cobertura_codigo_grupo='$codigo_grupo' and 
                                              tbl_cobertura_codigo_estacao_monta='$id_parametro_estacao' and 
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

                        echo "<td width='9%'> 
                              <input type='hidden' name='id_animal' value='".$codigo_id."'>" .$codigo_ed. "</td>";
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
                }
            } 
        }
    }   // fim do else id_cobertura    

    mysqli_close($conector);

    echo '</tbody>';

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
   echo '</table>';

   echo '</section>';
?>

    <script src="js/matrizes.js" charset="utf-8" type="text/javascript" ></script>

    <script>
        $(document).ready(function() {
            var table = $('#tabela_matrizes').DataTable( {
                sDom: 'lfr<"table_overflow"t>ip',
                /* scrollY: "200px", */
                paging:   false,
                search:   true,
                info: false,
                ordering: false,
                language: {
                  sSearch: "Buscar na lista:",
                  zeroRecords: "Nada encontrado",
                  info: "Animais listados: _END_ ",
                  infoEmpty: "Nenhum registro disponível",
                  infoFiltered: "(filtrado de _MAX_ registros no total)",
                }
            });
        });


    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
    </script>

</body>
</html> 

                
                
