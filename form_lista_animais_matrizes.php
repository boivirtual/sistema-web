<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";

    $data_hoje = date("Y-m-d");

    $tipo_registro = ltrim($_POST['tipo_registro']);
    $local = ltrim($_POST['local']);
    $vacas_paridas = $_POST['vacas_paridas'];
    //$data_paridas_ate = $_POST['data_paridas'];
    $vacas_solteiras = $_POST['vacas_solteiras'];
    $novilhas = $_POST['novilhas'];
    $idade_de = $_POST['idade_de'];
    $idade_ate = $_POST['idade_ate'];
    $peso_acima = $_POST['peso_acima'];
    $id_parametro_estacao = $_POST['id_estacao_monta'];
    $id_cobertura = 0;

    // Em 21/03/2025 a data Paridas Até passou a ser calculado 35 dias a menos da data digita na tela no input Apta Em conforme o trello:

    // Cartão: MELHORIA NA REPRODUÇÃO (PARA DEPOIS DA ESTAÇÃO 24/2025)
    // Cheklist: MELHORAR A USABILIDADE: DIMINUIR CLIQUES E MELHORAR MENSAGENS NA TELA 

    // Subtrair 35 dias da Data digitada
    $aptasEm = $_POST['data_paridas'];
    $data_paridas_ate = date("Y-m-d", strtotime($aptasEm . "- 35 days"));

    if ($vacas_paridas=='VP') {
        $filtro_paridas='S';
    }
    else {
        $filtro_paridas='';
    }

    if ($vacas_solteiras=='VS') {
        $filtro_solteiras='S';
    }
    else {
        $filtro_solteiras='';
    }

    if ($novilhas=='NO') {
        $filtro_novilhas='S';
    }
    else {
        $filtro_novilhas='';
    }

    if ($filtro_paridas=='' && $filtro_solteiras=='' && $filtro_novilhas=='') {
        $sem_filtros = 'S';
    }
    else {
        $sem_filtros = '';
    }

    if ($idade_ate=='') {
        $idade_ate=9999;
    }

    if ($peso_acima==0) {
        $peso_acima=1;
    }

    $peso_acima = floatval($peso_acima);

    //$data_paridas_de = date("Y-m-d", strtotime('-8 month',strtotime($data_paridas_ate)));
    //$data_paridas_de = date("Y-m-d", strtotime('-1 day',strtotime($data_paridas_de)));

   // pega periodo da estacao de monta atual

    $tbl_estacao_monta = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
            WHERE tbl_par_estacao_id = '$id_parametro_estacao'");

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
  <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" rel="stylesheet" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

  <style>
    /* MANTENHA ESTES PARA O CABEÇALHO FIXO */
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

    /* ESTE É O CSS CRUCIAL PARA A ROLAGEM DA TABELA */
    .table_overflow {
        max-height: 300px; /* Ajuste esta altura conforme a necessidade. */
        overflow-y: auto;  /* Ou 'scroll' se preferir a barra sempre visível */
        /* overflow-x: hidden; */
    }

    /* AJUSTES DE LAYOUT GERAIS */
    #tabela_matrizes_wrapper,  #tabela_matrizes_monta_wrapper{
        width: 100%; /* Garante que o wrapper ocupe 100% da largura disponível */
    }

    /* ESTE É O BLOCO CRÍTICO AGORA PARA O ALINHAMENTO SUPERIOR */
    .dataTables_wrapper .top {
        display: flex; /* Habilita o Flexbox para o contêiner 'top' */
        justify-content: space-between; /* Distribui o espaço: 'l' à esquerda, 'f' à direita */
        align-items: center; /* Alinha verticalmente se houver diferença de altura */
        margin-bottom: 10px; /* Mantém o espaço abaixo dos controles */
        width: 100%; /* Garante que o contêiner ocupe a largura total */
    }

    /* Remova as regras anteriores que tentavam alinhar a busca */
    /* Você pode remover ou comentar as linhas abaixo se ainda existirem: */
    /* .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        width: 100%;
        float: none;
        margin-bottom: 10px;
    } */

    /*#tabela_matrizes_filter {
        text-align: right; 
    }*/ 

    /* Mantenha esses para a estilização interna do input, mas sem o float */
    #tabela_matrizes_filter label,  #tabela_matrizes_monta_filter label{
        display: inline-flex;
        align-items: center;
    }
    #tabela_matrizes_filter input, #tabela_matrizes_monta_filter input {
        width: auto;
        display: inline-block;
        margin-left: 5px;
    }
    /* Fim das regras anteriores que podem ser removidas/comentadas */

    /* Remove o float anterior se ainda existir */
    /* #tabela_matrizes_filter {
        float: right; /* REMOVA OU COMENTE ESTA LINHA SE VOCÊ A TINHA ANTERIORMENTE NO SEU CSS */
    /* } */

    /* Garante que o info e pagination fiquem em suas próprias linhas na parte inferior */
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        width: 100%;
        float: none;
        text-align: left; /* Ajuste conforme desejar */
        margin-top: 10px; /* Espaço acima */
    }

    .dataTables_wrapper .dataTables_paginate {
        text-align: right; /* Alinha a paginação à direita */
    }

    /* Se você tem outras tabelas, mantenha o CSS para elas 
    #tabela_matrizes_monta_wrapper{
        width: 100% !important;
        overflow-y: scroll !important;
        max-height: 300px;*/
    }

    #tabela_matrizes_monta_filter{
        float: right;
    }

    /* Mantenha também os estilos para tabela_matrizes_monta_wrapper e _filter se existirem */

    /*  .table_overflow table thead th{
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
          overflow-y: scroll !important;
          max-height: 300px;
      }

      #tabela_matrizes_filter{
          float: right;
      }

      #tabela_matrizes_monta_wrapper{
          width: 100% !important;
          overflow-y: scroll !important;
          max-height: 300px;
      }

      #tabela_matrizes_monta_filter{
          float: right;
      }*/
  </style>

</head>

<body> 

<?php
	echo '<section class="panel table-responsive">';

    if ($tipo_registro=='I') {
        echo '<table class="table table-striped table-advance table-hover" style="font-size: 10px; width: 100%;" id="tabela_matrizes">';
    }
    else {
        echo '<table class="table table-striped table-advance table-hover" style="font-size: 10px; width: 100%;" id="tabela_matrizes_monta">';
    }
                          
    echo '<tbody>';

    // LISTA MATRIZES ON-LINE 1ª PREMISSA SEXO = F

    /*$sql = mysqli_query($conector, "SELECT * FROM tbl_animais
        WHERE tbl_animal_codigo_id=1512");*/

    $sql = mysqli_query($conector, "SELECT * FROM tbl_animais
        WHERE tbl_animal_sexo='F' AND 
              tbl_animal_ativo='S' AND
              tbl_animal_lixeira=0 AND 
              tbl_animal_codigo_fazenda='$local'
        ORDER BY tbl_animal_codigo_numerico ASC");
    $num_row = mysqli_num_rows($sql);

    //print_r('Registros encontrados aqui: ' . $num_row . '</br>');
    //exit;

    if ($num_row!=0) {
        while ($reg_animal = mysqli_fetch_object($sql)){
            $codigo_id= $reg_animal->tbl_animal_codigo_id;
            $codigo_alfa= $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico= $reg_animal->tbl_animal_codigo_numerico;
            $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
            $ultimo_peso=  floatval($reg_animal->tbl_animal_ultimo_peso);
            $codigo_raca= $reg_animal->tbl_animal_codigo_raca;
            $descarte = $reg_animal->tbl_animal_descarte_reproducao;

           // print_r('Id Animal: ' . $codigo_numerico . ' Local: ' . $local .' Estacao: '. $id_parametro_estacao . ' Descate: '. $descarte.'</br>');

            if ($ultimo_peso==0) {
                $ultimo_peso = 1;
            }

            // VERIFICA IDADE > 12 2ª PREMISSA

            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            $idade_ano_mes = $idade_acompanhamento->format('%Y') .' a/ '. 
                    str_pad($idade_acompanhamento->format('%m') , 2 , '0' , STR_PAD_LEFT) . ' m';

            // VERIFICA SE A VACA ESTA PRENHE 3º PREMISSA

            $tbl_prenhe = mysqli_query($conector, "SELECT * FROM tbl_cobertura
                INNER JOIN tbl_item_cobertura 
                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal='$codigo_id' AND  
                      tbl_ite_cobertura_resultado_diagnostico='P' AND  
                      (tbl_ite_cobertura_nascido='' OR 
                       tbl_ite_cobertura_nascido IS NULL)");

            $num_rows_prenhe = mysqli_num_rows($tbl_prenhe);

            //VERIFICA SE A FÊMEA JA FOI SELECIONADA NESSA ESTACAO 4ª PREMISSA

            /*$femea_selecionada = '';

            $tbl_selecao = mysqli_query($conector, "SELECT * FROM tbl_cobertura
                INNER JOIN tbl_item_cobertura 
                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                      (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M')
                ORDER BY tbl_cobertura_incluido_em DESC LIMIT 1");

            $selecionada_estacao = mysqli_num_rows($tbl_selecao);

            if ($selecionada_estacao!=0) {
                $reg_selecao = mysqli_fetch_object($tbl_selecao);
                $femea_selecionada = 'S';

                $diagnostico_selecao = $reg_selecao->tbl_ite_cobertura_resultado_diagnostico;
                $cobertura_controle = $reg_selecao->tbl_cobertura_controle;
                $nascido = $reg_selecao->tbl_ite_cobertura_nascido;
                $estacao = $reg_selecao->tbl_cobertura_codigo_estacao_monta;

                if ($cobertura_controle=='C') {
                    if ($estacao!=$id_parametro_estacao || $diagnostico_selecao=='N') {
                        $femea_selecionada = '';
                    }
                }
                else {
                    if ($diagnostico_selecao=='N') {
                        $femea_selecionada = '';
                    }
                }
            }*/ 

            // VERIFICA SE ANIMAL TEM PARTO A MENOS DE 35 DIAS
            $data_nasc_bezerro = '0000-00-00';

            $tbl_filhos = mysqli_query($conector,"SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo_id'
                ORDER BY tbl_animal_codigo_id  DESC LIMIT 1"); 

            $numero_rows_partos = mysqli_num_rows($tbl_filhos);

            if ($numero_rows_partos!=0) {
                $reg_parto = mysqli_fetch_object($tbl_filhos);
                $codigo_bezerro = $reg_parto->tbl_animal_codigo_numerico;
                $data_nasc_bezerro=$reg_parto->tbl_animal_data_nascimento;
                $bezerro_ativo = $reg_parto->tbl_animal_ativo;
                $bezerro_situacao = $reg_parto->tbl_animal_situacao;

                //$data_ref = date("Y-m-d", strtotime($data_hoje . "- 35 days"));
                $data_ref = date("Y-m-d");

                if ($bezerro_situacao=='M') {
                    $data_morte = substr($reg_parto->tbl_animal_baixado_em, 0, 10) ;
                }
                else {
                    $data_morte = '0000-00-00';
                }

                $diferenca = strtotime($data_ref) - strtotime($data_nasc_bezerro);
                $dias_parto = floor($diferenca / (60 * 60 * 24));

                $animal_tem_parto = 'S';

                /*if ($dias_parto>35 && $cobertura_controle=='M') {
                    $femea_selecionada = '';
                }*/

                //print_r ('Femea: ' . $codigo_numerico . ' Nascimento Bezzero: ' . $data_nasc_bezerro . ' Dias de parto: '. $dias_parto . '</br>');
            }
            else {
                $animal_tem_parto = 'N';
            }

            // VERIFICA TAMBEM SE TEVE NATIMORTO A MENOS 35 DIAS
            $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='S' AND 
                      tbl_mov_estoque_tipo_movimentacao='M' 
                ORDER BY tbl_mov_estoque_nascimento DESC");

            $num_natimorto = mysqli_num_rows($tbl_natimorto);

            if ($num_natimorto!=0) {
                $reg_natmorto = mysqli_fetch_object($tbl_natimorto);

                if ($reg_natmorto->tbl_mov_estoque_nascimento>$data_nasc_bezerro) {
                    $data_nasc_bezerro=$reg_natmorto->tbl_mov_estoque_nascimento;

                    //$data_ref = date("Y-m-d", strtotime($data_hoje . "- 35 days"));
                    $data_ref = date("Y-m-d");

                    $diferenca = strtotime($data_ref) - strtotime($data_nasc_bezerro);
                    $dias_parto = floor($diferenca / (60 * 60 * 24));

                    $animal_tem_parto = 'S';

                    /*if ($dias_parto>35 && $cobertura_controle=='M') {
                        $femea_selecionada = '';
                    }*/
                }
            }

            //VERIFICA SE A FÊMEA JA FOI SELECIONADA NESSA ESTACAO 4ª PREMISSA

            $femea_selecionada = '';

            $tbl_selecao = mysqli_query($conector, "SELECT * FROM tbl_cobertura
                INNER JOIN tbl_item_cobertura 
                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                      (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M')
                ORDER BY tbl_cobertura_incluido_em DESC LIMIT 1");

            $selecionada_estacao = mysqli_num_rows($tbl_selecao);

            if ($selecionada_estacao!=0) {
                $reg_selecao = mysqli_fetch_object($tbl_selecao);
                $femea_selecionada = 'S';

                $diagnostico_selecao = $reg_selecao->tbl_ite_cobertura_resultado_diagnostico;
                $cobertura_controle = $reg_selecao->tbl_cobertura_controle;
                $nascido = $reg_selecao->tbl_ite_cobertura_nascido;
                $estacao = $reg_selecao->tbl_cobertura_codigo_estacao_monta;

                if ($cobertura_controle=='C') {
                    if ($estacao!=$id_parametro_estacao || $diagnostico_selecao=='N') {
                        $femea_selecionada = '';
                    }
                }
                else {
                    if ($diagnostico_selecao=='N') {
                        $femea_selecionada = '';
                    }

                    if ($nascido) {
                        if ($dias_parto>35) {
                            $femea_selecionada = '';
                        }
                    }
                }
            } 

            // VERIFICAR FILTROS DE VACAS PARIDAS

            $vaca_parida='N';

            //print_r('Bezerro: ' . $data_nasc_bezerro. ' Paridas até:' . $data_paridas_ate .'</br>');

            if ($filtro_paridas=='S') {
                if ($animal_tem_parto=='S') {
                    if ($data_nasc_bezerro<=$data_paridas_ate) {
                        $date = new DateTime($data_nasc_bezerro); // Data de Nascimento do bezzero
                        $idade_acompanhamento = $date->diff(new DateTime($data_paridas_ate));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade_bezerro = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;


                        if ($idade_bezerro<8) {
                            if ($bezerro_situacao!='M') {

                                $aborto = VerAborto($conector, $codigo_id, $data_paridas_ate);

                                if ($aborto[0]=='S' && $aborto[2]>=$data_nasc_bezerro) {

                                    if ($aborto[2]<=$data_paridas_ate) {
                                        $vaca_parida='S';
                                    }
                                    else {
                                        $vaca_parida='N';
                                    }
                                }
                                else {
                                   $vaca_parida='S'; 
                                }
                            }
                            else {
                                $diferenca = strtotime($data_hoje) - strtotime($data_nasc_bezerro);
                                $dias_nascimento = floor($diferenca / (60 * 60 * 24));
                                //print_r('dias nascido: ' . $dias_nascimento.'</br>');

                                if ($dias_nascimento<35) {
                                    $vaca_parida='S';
                                }
                            }
                        }
                    }
                }
            }

            //print_r('Parida: ' . $vaca_parida . ' Idade Bezerro: '.$idade_bezerro.'</br>');

            // VERIFICAR FILTROS DE VACAS SOLTEIRAS

            $vaca_solteira='N';
            
            if ($filtro_solteiras=='S') {
                //print_r('Vou ver solteiras, tem partos?' . $animal_tem_parto . '</br>');

                if ($animal_tem_parto=='S') { // alternativas 1, 2, 3, 4, 5, 6
                    $date = new DateTime($data_nasc_bezerro); // Data de Nascimento do bezzero
                    $idade_acompanhamento = $date->diff(new DateTime($data_hoje));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade_bezerro = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    //print_r('Idade Bezzero: ' . $idade_bezerro . '</br>');

                    if ($idade_bezerro>=8) { // alternativas 1, 2, 3, 4

                        if ($bezerro_situacao!='M') { // 1, 2, bezerro vivo

                            $aborto = VerAborto($conector, $codigo_id, $data_hoje);
                            
                            if ($aborto[0]=='N') { // alternativa 1
                                $vaca_solteira='S';
                            }
                            else { // alternativa 2
                                $data_aborto = $aborto[2];
                                $data_ref = CalcularDataRef($data_hoje);

                                if ($data_aborto<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                        }
                        else { // 3, 4 bezzero não está vivo
                            $aborto = VerAborto($conector, $codigo_id, $data_hoje);

                            if ($aborto[0]=='S') { // alternativa 3
                                $data_aborto = $aborto[2];
                                $data_ref = CalcularDataRef($data_hoje);

                                if ($data_aborto<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                            else { // alternativa 4
                                $vaca_solteira='S';
                            }
                        }
                    }
                    else { // alternativas 5, 6
                        if ($bezerro_situacao=='M') { // Bezerro vivo não

                            //print_r('Situacao Bezerro: ' . $bezerro_situacao  . '</br>');

                            $aborto = VerAborto($conector, $codigo_id, $data_hoje);


                            if ($aborto[0]=='S') { // alternativa 5
                                $data_aborto = $aborto[2];
                                $data_ref = CalcularDataRef($data_hoje);

                                if ($data_aborto<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                            else { // alternativa 6
                                $data_ref = CalcularDataRef($data_hoje);

                                //print_r('Data Nascimento ' . $data_nasc_bezerro . ' Data Ref: ' . $data_ref . '</br>');

                                if ($data_nasc_bezerro<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                        }
                    }
                }
                else { // alternativa 7
                    $aborto = VerAborto($conector, $codigo_id, $data_hoje);

                    if ($aborto[0]=='S') {
                        $data_aborto = $aborto[2];
    
                        $data_ref = CalcularDataRef($data_hoje);

                        if ($data_aborto<=$data_ref) {
                            $vaca_solteira='S';
                        }
                    }

                    //print_r('Vou ver natimorto');

                    $natimorto = VerNatimorto($conector, $codigo_id, $data_hoje);

                    if ($natimorto[0]=='S') {
                        $data_natimorto = $natimorto[2];
    
                        $data_ref = CalcularDataRef($data_hoje);

                        //print_r('Natimorto: ' . $data_natimorto . 
                               // ' Data Ref: ' . $data_ref . 
                               // ' Dias: ' . $natimorto[1]);

                        if ($data_natimorto<=$data_ref) {
                            $vaca_solteira='S';
                        }
                    }
                    else {
                       $data_natimorto='0000-00-00'; 
                    }
                }
            }

            // VERIFICAR FILTROS DE NOVILHAS

            $novilha='N';
            
            if ($filtro_novilhas=='S') {
                if ($idade>=$idade_de && $idade<=$idade_ate) {
                    if ($animal_tem_parto=='N') {
                        $aborto = VerAborto($conector, $codigo_id, $data_hoje);
                        if ($aborto[0]=='N') {
                            $novilha='S';
                        }
                    }
                }
            }

            // VERIFICAR ANIMAIS SEM FILTRO
            $imp_esse_sem_filtro = '';

            if ($sem_filtros=='S') {
                if ($animal_tem_parto=='S') {

                    if ($dias_parto>=35) {
                        $aborto = VerAborto($conector, $codigo_id, $data_hoje);

                        if ($aborto[0]=='S') {
                            if ($aborto[1]>=35) {
                                $imp_esse_sem_filtro = 'S';
                            }
                            else {
                               $imp_esse_sem_filtro = 'N'; 
                            }
                        }
                        else {
                            $imp_esse_sem_filtro = 'S';
                        }
                    }
                    else {
                        $imp_esse_sem_filtro = 'N';
                    }
                }
                else {
                    $aborto = VerAborto($conector, $codigo_id, $data_hoje);
                    
                    if ($aborto[0]=='S') {
                        if ($aborto[1]>=35) {
                            $imp_esse_sem_filtro = 'S';
                        }
                        else {
                           $imp_esse_sem_filtro = 'N'; 
                        }
                    }
                    else {
                        $imp_esse_sem_filtro = 'S';
                    }
                }
            }

            // TESTAR PREMISSAS 2ª, 3ª, 4ª E PESO

            /*print_r('femeas: '. $codigo_numerico . 
                ' Selecionada: ' . $femea_selecionada . ' prenha: ' .  $num_rows_prenhe. '<br>');*/

            if ($num_rows_prenhe==0 && $idade>=12 && $femea_selecionada=='' && $ultimo_peso>=$peso_acima) {

                //print_r(' vou imprimir sem filtros');

                if ($sem_filtros=='S' && $imp_esse_sem_filtro=='S') {

                    $animais_listados = ImprimirFemea($conector, $animais_listados, $codigo_id, $codigo_alfa, $codigo_numerico, $data_nascimento, $ultimo_peso, $codigo_raca, $descarte, $local, $id_parametro_estacao, $idade_ano_mes, $tipo_registro);
                }
                else if ($filtro_solteiras==$vaca_solteira) {
                   //print_r('vou imprimir solteiras');

                    $animais_listados = ImprimirFemea($conector, $animais_listados, $codigo_id, $codigo_alfa, $codigo_numerico, $data_nascimento, $ultimo_peso, $codigo_raca, $descarte, $local, $id_parametro_estacao, $idade_ano_mes, $tipo_registro);
                }
                else if ($filtro_paridas==$vaca_parida) {
                    //print_r('vou imprimir parida');

                    $animais_listados = ImprimirFemea($conector, $animais_listados, $codigo_id, $codigo_alfa, $codigo_numerico, $data_nascimento, $ultimo_peso, $codigo_raca, $descarte, $local, $id_parametro_estacao, $idade_ano_mes, $tipo_registro);
                }
                else if ($filtro_novilhas==$novilha) {
                    //print_r('vou imprimir novilha');

                    $animais_listados = ImprimirFemea($conector, $animais_listados, $codigo_id, $codigo_alfa, $codigo_numerico, $data_nascimento, $ultimo_peso, $codigo_raca, $descarte, $local, $id_parametro_estacao, $idade_ano_mes, $tipo_registro);
                }
                
            } // FIM DO $num_rows_prenhe==0 && $idade>=12 && 
              //$femea_selecionada=='' && $ultimo_peso>=$peso_acima

        } // FIM DO while

    } // FIM DO $num_row!=0


// VERIFICA SE TEVE ABORTO
function VerAborto($conector, $codigo_id, $data_ref) {
    $teve_aborto = 'N';
    $dias_aborto = 0;
    $data_aborto = '0000.00.00';

    $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
        WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
              tbl_mov_estoque_codigo_id_animal=999999999 AND
              tbl_mov_estoque_entrada_saida='A' AND 
              (tbl_mov_estoque_tipo_movimentacao='A' OR
               tbl_mov_estoque_tipo_movimentacao='B') 
        ORDER BY tbl_mov_estoque_nascimento DESC");

    $num_aborto = mysqli_num_rows($tbl_aborto);

    if ($num_aborto!=0) {
        $teve_aborto = 'S';
    }

    if ($teve_aborto == 'S') {
        $reg_aborto = mysqli_fetch_object($tbl_aborto);

        $data_aborto=$reg_aborto->tbl_mov_estoque_nascimento;
        $data_ref = date("Y-m-d", strtotime($data_ref . "- 35 days"));

        $diferenca = strtotime($data_ref) - strtotime($data_aborto);
        $dias_aborto = floor($diferenca / (60 * 60 * 24));
    }

    return [$teve_aborto, $dias_aborto, $data_aborto];
}

// VERIFICA SE TEVE NATMORTO
function VerNatimorto($conector, $codigo_id, $data_ref) {
    $teve_natimorto = 'N';
    $dias_natimorto = 0;
    $data_natimorto = '0000.00.00';

    $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
        WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
              tbl_mov_estoque_codigo_id_animal=999999999 AND
              tbl_mov_estoque_entrada_saida='S' AND 
              tbl_mov_estoque_tipo_movimentacao='M' 
        ORDER BY tbl_mov_estoque_nascimento DESC");

    $num_natimorto = mysqli_num_rows($tbl_natimorto);

    if ($num_natimorto!=0) {
        $teve_natimorto = 'S';
    }

    if ($teve_natimorto == 'S') {
        $reg_natimorto = mysqli_fetch_object($tbl_natimorto);

        $data_natimorto=$reg_natimorto->tbl_mov_estoque_nascimento;
        $data_ref = date("Y-m-d", strtotime($data_ref . "- 35 days"));

        $diferenca = strtotime($data_ref) - strtotime($data_natimorto);
        $dias_natimorto = floor($diferenca / (60 * 60 * 24));
    }

    return [$teve_natimorto, $dias_natimorto, $data_natimorto];
}

// CALCULA A DATA DE REF - 35 DIAS
function CalcularDataRef($data_ref) {
    $data_ref = date("Y-m-d", strtotime($data_ref . "- 35 days"));
    return $data_ref;
}

// IMPRIME A FEMEA
function ImprimirFemea($conector, $animais_listados, $codigo_id, $codigo_alfa, $codigo_numerico, $data_nascimento, $ultimo_peso, $codigo_raca, $descarte, $local, $id_parametro_estacao, $idade_ano_mes,$tipo_registro) {

    $data_hoje = date("Y-m-d");

    if ($codigo_alfa=='') {
        $codigo_ed = $codigo_numerico;
    }
    else {
        $codigo_ed = $codigo_alfa.'-'.$codigo_numerico;
    }

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

    // VERIFICA NUMERO DE PARTOS 
    $tbl_filhos = mysqli_query($conector,"SELECT * FROM tbl_animais 
        WHERE tbl_animal_codigo_mae='$codigo_id'"); 

    $numero_partos = mysqli_num_rows($tbl_filhos);

    $tbl_filhos = mysqli_query($conector,"SELECT * FROM tbl_animais 
        WHERE tbl_animal_codigo_mae='$codigo_id'
        ORDER BY tbl_animal_codigo_id DESC LIMIT 1"); 

    $numero_rows_partos = mysqli_num_rows($tbl_filhos);

    if ($numero_rows_partos!=0) {
        while ($reg_filhos = mysqli_fetch_object($tbl_filhos)){
            $codigo_pai=$reg_filhos->tbl_animal_codigo_pai;
            $bezerro_ativo = $reg_filhos->tbl_animal_ativo;
            $bezerro_situacao = $reg_filhos->tbl_animal_situacao;

            $ultimo_parto=new DateTime($reg_filhos->tbl_animal_data_nascimento);
            $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');

            $data_aptidao_edi = date("d/m/Y", strtotime($reg_filhos->tbl_animal_data_nascimento . "+ 35 days"));

            $ultimo_parto=$reg_filhos->tbl_animal_data_nascimento;

            $data_nascimento = $reg_filhos->tbl_animal_data_nascimento;
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade_bezerro = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            if ($bezerro_ativo=='N' && $bezerro_situacao=='M') {
                $data_inicial = date($reg_filhos->tbl_animal_baixado_em);
                $data_final = date("Y-m-d");
                $diferenca = strtotime($data_final) - strtotime($data_inicial);
                $dias_natimorto = floor($diferenca / (60 * 60 * 24));

                if ($dias_natimorto<=0) {
                    $dias_natimorto=1;
                }
            }
        }
    }
    else {
        $codigo_pai = 0;
        $ultimo_parto_edi = '';
        $data_aptidao_edi = '';
        $ultimo_parto = '0000-00-00';
    }

    $tab_pai = mysqli_query($conector, "SELECT * FROM tbl_semem 
        WHERE tbl_semem_codigo_id='$codigo_pai'");

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

    $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
        WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
              tbl_mov_estoque_codigo_id_animal=999999999 AND 
              tbl_mov_estoque_entrada_saida='A'");

    $numero_abortos = mysqli_num_rows($tbl_aborto);

    // VERIFICA NATIMORTO E SOMA NO NUMERO DE PARTOS/DATA APTIDAO
    $natimorto = VerNatimorto($conector, $codigo_id, $data_hoje);

    if ($natimorto[0]=='S') {

        $data_natimorto = $natimorto[2];
        $numero_partos+=1;

        if ($data_natimorto > $ultimo_parto) {
            $ultimo_parto=new DateTime($data_natimorto);
            $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');
            $ultimo_parto=$data_natimorto;

            $tem_natimorto = 'S';
        }
        else {
            $tem_natimorto = 'N';
            $data_natimorto = '0000-00-00';
        }
    }
    else {
        $tem_natimorto = 'N';
        $data_natimorto = '0000-00-00';
    }

    // VERIFICA ABORTO PARA CALCULAR A DATA DE APTIDAO
    $aborto = VerAborto($conector, $codigo_id, $data_hoje);
    if ($aborto[0]=='S') {
        $data_aborto = $aborto[2];
    }
    else {
        $data_aborto = '0000-00-00';
    }

    // Verifica qual data será considerada para calcular a aptidao

    if ($data_natimorto=='0000-00-00' && $data_aborto=='0000-00-00') {
        $data_aborto_natimorto='0000-00-00';
    }
    else if ($data_natimorto>$data_aborto){
        $data_aborto_natimorto=$data_natimorto;
    }
    else if ($data_aborto>$data_natimorto) {
        $data_aborto_natimorto=$data_aborto;
    }

    if ($ultimo_parto!='0000-00-00') {
        $data_aptidao_edi = date("d/m/Y", strtotime($ultimo_parto . "+ 35 days"));
    }

    if ($data_aborto_natimorto!='0000-00-00' && $data_aborto_natimorto>$ultimo_parto) {
        $data_aptidao_edi = date("d/m/Y", strtotime($data_aborto_natimorto . "+ 35 days"));
    }

    $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura
        INNER JOIN tbl_item_cobertura 
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
        WHERE tbl_cobertura_lixeira=0 AND 
              tbl_cobertura_codigo_local = '$local' AND 
              tbl_cobertura_codigo_estacao_monta = '$id_parametro_estacao' AND 
              tbl_ite_cobertura_codigo_id_animal='$codigo_id' AND
              (tbl_ite_cobertura_resultado_diagnostico='P' or
               tbl_ite_cobertura_resultado_diagnostico='N')");

    $coberturas_estacao = mysqli_num_rows($tbl_cobertura);

    if ($tipo_registro=='I') {
        echo "<tr>";

        if ($descarte=='S') {
            echo "<td style='color: red;' width='16%' >DESCARTADA</td>";
            echo "<td>".$codigo_alfa."</td>";
            echo "<td width='9%'>" .$codigo_numerico. "</td>";
        }
        else {
            echo "<td width='16%'>";
            echo '<select class="form-control grupo_select" name="grupo_select" style="height: 2.1em;" onchange="somar_selecionados()">';

            echo '<option value="000">...</option>';

            $grupo_estacao = mysqli_query($conector, "SELECT * FROM tbl_grupo_estacao_monta 
                WHERE tbl_grupo_codigo_estacao_monta ='$id_parametro_estacao' AND 
                      tbl_grupo_codigo_local = '$local'
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
            echo "<td>".$codigo_alfa."</td>";
            echo "<td width='9%'> 
            <input type='hidden' name='id_animal' value='".$codigo_id."'>" .$codigo_numerico. "</td>";
        }

        echo "<td width='11%'>".$desc_raca."</td>";
        echo "<td width='9%'>".$idade_ano_mes."</td>";
        echo "<td align='center' width='8%' >".$numero_partos."</td>";
        echo "<td align='center' width='8%' >".$numero_abortos."</td>";

        if ($tem_natimorto=='S') {
            echo '<td width="9%" style="color: red;" data-toggle="tooltip" data-placement="right" title="Considerado aqui a data do Natimorto.">'.$ultimo_parto_edi.'</td>';
        }
        else {
            echo "<td width='9%'>".$ultimo_parto_edi."</td>";
        }

        echo "<td width='9%'>".$descricao_pai."</td>";
        echo "<td width='9%'>".$data_aptidao_edi."</td>";
        echo "<td align='center' width='12%' >".$coberturas_estacao."</td>";
        echo "</tr>";
    }
    else {
        echo "<tr>";
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
        echo "<td align='center' width='8%' >".$numero_partos."</td>";
        echo "<td align='center' width='8%' >".$numero_abortos."</td>";

        if ($tem_natimorto=='S') {
           echo '<td width="9%" style="color: red;" data-toggle="tooltip" data-placement="right" title="Considerado aqui a data do Natimorto.">'.$ultimo_parto_edi.'</td>';
        }
        else {
            echo "<td width='9%'>".$ultimo_parto_edi."</td>";
        }

        echo "<td width='9%'>".$descricao_pai."</td>";
        echo "<td width='9%'>".$data_aptidao_edi."</td>";
       echo "</tr>";
    }

    $animais_listados++;
    return $animais_listados;
}                

    mysqli_close($conector);

    echo '</tbody>';

    if ($tipo_registro=='I') {
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
    <script src="https://cdn.datatables.net/plug-ins/1.10.12/sorting/date-eu.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#tabela_matrizes').DataTable( {
                sDom: '<"top"lf>r<"table_overflow"t><"bottom"ip>', 
                paging: false,
                search: true,
                info: true,
                ordering: true,
                "columns": [
                    null, null, null, null, null, null, null,
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
    
            somar_selecionados();

            var table = $('#tabela_matrizes_monta').DataTable( {
                //sDom: 'lfr<"table_overflow"t>ip',
                sDom: '<"top"lf>r<"table_overflow"t><"bottom"ip>', 
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
    </script>

</body>
</html> 

                
                
