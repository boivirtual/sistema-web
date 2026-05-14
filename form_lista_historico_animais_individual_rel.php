<?php
    include "valida_sessao.inc";

    @ session_start(); 

    $data_sistema = date("Y-m-d");
    $origem_relatorio=$_REQUEST['origem_relatorio'];

    $_SESSION['local_pesagem']='';
    $_SESSION['categoria_historico_animais']='';

    $codigo_alfa_numerico = $_REQUEST['codigo_alfa_numerico']; 

    if ($codigo_alfa_numerico!='') {
        $codigo_numerico_consulta = substr($codigo_alfa_numerico, -9);

        if (strlen($codigo_alfa_numerico)!=9){
            $data = explode("-", $codigo_alfa_numerico);
            $codigo_alfa_consulta = $data[0];
        }
        else {
            $codigo_alfa_consulta = '';
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

  <!-- Bootstrap CSS 
  <link href="css/jquery-ui.css" rel="stylesheet" />-->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css?<?php echo Versao;?>" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php"; 
            include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; 
            include "limpar_secao_selecao_matrizes.php"; 
            include "limpar_secao_compra_venda.php";
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Histórico de Animais</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="far fa-file-alt"></i> Histórico de Animais</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data">

                            <div class="panel"> 
                            <!--    <div class=panel-body> -->
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="" style="padding-right: 15px; padding-left: 15px;">

                                                <input type="hidden" id="expande_tela" value="S">

                                                <input type="hidden" id="origem_relatorio" <?php echo "value='".$origem_relatorio."'";?>>        

                                        <input type="hidden" id="codigo_number_filtro"
                                            <?php echo "value='".$codigo_alfa_numerico."'";?>>


<?php
        $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
        INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_animal_codigo_fazenda
            WHERE tbl_animal_codigo_alfa = '$codigo_alfa_consulta' AND 
                  tbl_animal_codigo_numerico = '$codigo_numerico_consulta'");

        $num_rows_animais = mysqli_num_rows($tbl_animais);

        if ($num_rows_animais==0){
            echo '
            <script type="text/javascript">
                $("#aguardar").modal("hide");
            </script>
            ';

            mysqli_close($conector);

            echo '
                <div class="row">
                <div class="col-md-9" style="margin-bottom: 10px; margin-top: 10px;">
                <label class="label_consulta_rel_rel">Filtros:</label>
                <span>'.$descricao_filtro.' Registro não encontrado</span>
                </div>

                <div class="form-group col-md-3" style="padding-top: 10px;">  
                <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                </button>
                </div>
                </div>';
        }   
        else {
            $reg_animal = mysqli_fetch_object($tbl_animais);
            $codigo = $reg_animal->tbl_animal_codigo_id;
            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
            $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
            $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
            $ativo = $reg_animal->tbl_animal_ativo;
            $animal_situacao = $reg_animal->tbl_animal_situacao;
            $descarte = $reg_animal->tbl_animal_descarte_reproducao;
            $descarte_em = new DateTime($reg_animal->tbl_animal_descarte_em);
            $descarte_em_edi = $descarte_em->format('d/m/Y H:i:s');
            $descarte_por = 'Por ' . $reg_animal->tbl_animal_descarte_por .' em '. $descarte_em_edi;
            $nome_pessoa = $reg_animal->tbl_pessoa_nome; 
            $pai = $reg_animal->tbl_animal_codigo_pai;
            $mae =  $reg_animal->tbl_animal_codigo_mae;
            $codigo_origem = $reg_animal->tbl_animal_codigo_origem;
            $peso_desmama = intval($reg_animal->tbl_animal_peso_desmama);
            $peso_nasc = intval($reg_animal->tbl_animal_primeiro_peso);
            $ultimo_peso = intval($reg_animal->tbl_animal_ultimo_peso);

            if ($peso_nasc==0) {$peso_nasc='';}
            if ($peso_desmama==0) {$peso_desmama='';}
            if ($ultimo_peso==0) {$ultimo_peso='';}

            $codigo_compra = $reg_animal->tbl_animal_movimentacao_compra;
            $data_compra = $reg_animal->tbl_animal_data_compra;

            // AJUSTE DO PESO DE DESMAMA
            if ($peso_desmama!='' && $peso_desmama!=0) {
                if ($peso_nasc=='' || $peso_nasc==0) {
                    $peso_nasc = 30;
                    $peso_nasc_edi = number_format($peso_nasc,2,',','.');
                }
                        
                $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                $data_final = $reg_animal->tbl_animal_data_desmama;
                $diferenca = strtotime($data_final) - 
                             strtotime($data_inicial);
                $dias = floor($diferenca / (60 * 60 * 24));

                $diferenca_peso = $peso_desmama - $peso_nasc;
                $gmd = $diferenca_peso/$dias;

                $peso_desmama = $peso_nasc + ($gmd * 205);
                $peso_desmama_edi = number_format($peso_desmama,2,',','.');
            }
            else {
                $peso_desmama_edi = '';
            }
            // FIM AJUSTE DO PESO DE DESMAMA

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_semem_nome;
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . ltrim($reg->tbl_animal_codigo_numerico, "0");
                }
                else {
                    $descricao_pai = '';
                }
            }

            $tab_mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$mae'");
            $num_rows = mysqli_num_rows($tab_mae);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_mae);
                if ($reg->tbl_animal_codigo_alfa==''){
                    $descricao_mae = intval($reg->tbl_animal_codigo_numerico);
                }
                else {
                    $descricao_mae = $reg->tbl_animal_codigo_alfa.'-'. intval($reg->tbl_animal_codigo_numerico);
                }
            }
            else {
                $descricao_mae = '';
            }
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

            $idade_ano = $idade_acompanhamento->format('%Y');
            $idade_mes = $idade_acompanhamento->format('%m');
            $idade_animal = $idade_acompanhamento_mostra_anos+
                            $idade_acompanhamento_mostra_meses;

            if ($idade_ano==0 && $idade_mes!=0) {
                $desc_idade = $idade_mes . ' mes(es)';
            }
            else if ($idade_ano!=0 && $idade_mes==0){
                $desc_idade = $idade_ano . ' ano(s)';
            }
            else if ($idade_ano!=0 && $idade_mes!=0) {
                $desc_idade = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
            }
            else {
                $desc_idade = '';
            }

            $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
            $data_nascimento_edi = $data->format('d/m/Y');

            if ($reg_animal->tbl_animal_sexo=='M') {
                $sexo = 'Macho';
            }
            else {
                $sexo = 'Femea';
            }

            if ($codigo_alfa=='') {
                $codigo_edi = intval($codigo_numerico);
            }
            else {
                $codigo_edi = $codigo_alfa.'-'.intval($codigo_numerico);
            }

            if ($ativo=='N') {
                $ativo = 'Não';
            }
            else {
                $ativo = 'Sim';
            }

            switch ($animal_situacao) {
            case 'T':
                $animal_situacao='Aguardando Transferência';
                break;
            case 'V':
                $animal_situacao='Vendido';
                break;
            case 'M':   
                $animal_situacao='Morte';
                break;
            case 'S':   
                $animal_situacao='Outra Saída';
                break;
            } 

            $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                    WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($categoria);    

            if ($num_rows!=0) {
                while ($reg_categoria = mysqli_fetch_object($categoria)) {
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade_animal >= $idade_de && 
                        $idade_animal <= $idade_ate) {
                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                    }
                }
            }                   

            switch ($codigo_categoria) {
                case '001':
                    $desc_categoria= '00 a 07 meses';
                    break;
                case '002':
                    $desc_categoria= '08 a 12 meses';
                    break;
                case '003':
                    $desc_categoria= '13 a 24 meses';
                    break;
            case '004':
                    $desc_categoria= '25 a 36 meses';
                    break;
                case '005':
                    $desc_categoria= '> 36 meses';
                    break;
            }     

            $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_origem'");
                
            $num_rows = mysqli_num_rows($tab_fazenda);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_fazenda);
                $desc_origem = $reg->tbl_pessoa_nome;
            }
            else {
                $desc_origem = '';
            }

            $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
                
            $num_rows = mysqli_num_rows($tab_raca);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_raca);
                $descricao_raca = $reg->tab_descricao_raca;
            }
            else {
                $descricao_raca = '';
            }

            $tab_pelagem = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem ='$codigo_pelagem'");
                
            $num_rows = mysqli_num_rows($tab_pelagem);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pelagem);
                $descricao_pelagem = $reg->tab_descricao_pelagem;
            }
            else {
                $descricao_pelagem = '';
            }

            echo '
                <div class="row">
                <div class="col-md-9" style="padding-top: 10px; margin-bottom: 10px; font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Nº Animal:&nbsp;</label>
                    <span>'.$codigo_edi . ' - ' . $sexo .'
                    </span>
                </div>

                <div class="form-group col-md-3" style="padding-top: 10px;">  
                <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                </button>

                <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" onClick="lista_historico_animais_individual_excel()">Excel
                    </button>
                </div>
                </div>';

            echo '<div class="row">
                  <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Nascimento:&nbsp;</label>
                    <span>'.$data_nascimento_edi.'</span>
                </div>

                  <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Idade:&nbsp;</label>
                    <span>'.$desc_idade.'</span>
                </div>
                </div>';

            echo '<div class="row">
                <div class="col-md-8" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">&nbsp;Categoria:&nbsp;</label>
                    <span>'.$desc_categoria.'</span>
                </div>
            </div>';

            echo '<div class="row">
                  <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Raça:&nbsp;</label>
                    <span>'.$descricao_raca.'</span>
                </div>

                  <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Pelagem:&nbsp;</label>
                    <span>'.$descricao_pelagem.'</span>
                </div>
                </div>';

            echo '<div class="row">
                <div class="col-md-9" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">&nbsp;Fazenda:&nbsp;</label>
                    <span>'.$nome_pessoa.'</span>
                </div>
            </div>';

            echo '<div class="row">
                <div class="col-md-9" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">&nbsp;Origem:&nbsp;</label>
                    <span>'.$desc_origem.'</span>
                </div>
            </div>';

            echo '<div class="row">
                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Animal Ativo:&nbsp;</label>';
                    
                if ($ativo == "Sim") {
                    echo '<span style="color: green;">'.$ativo.'</span>';
                }
                else {
                    echo '<span style="color: red;">'.$ativo.'</span>';
                }   

                echo '</div>';

                if ($ativo == "Não") {
                    echo '<div class="col-md-2"style="font-size: 14px;">
                        <label class="label_situacao_reprodutiva_rel">Situação:&nbsp;</label>
                        <span style="color: red;">'.$animal_situacao.'</span>
                    </div>';
                }
            echo '</div>';

            echo '<div class="row">
                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Pai:&nbsp;</label>
                    <span>'.$descricao_pai.'</span>
                </div>
            </div>';

            echo '<div class="row">
                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Mãe:&nbsp;</label>
                    <span>'.$descricao_mae.'</span>
                </div>
            </div>';    

            echo '<div class="row">';

            if ($descarte == 'S') {
            echo '<div class="col-md-8" style="font-size: 14px;">
                <label class="label_situacao_reprodutiva_rel">Descartado para reprodução:&nbsp;</label>
                <span style="color: red">'.$descarte_por.'</span>
            </div>';
            echo '</div> <hr>';
            }

            // MOVIMENTAÇÕES
            echo '<div class="row">
                    <div class="col-md-1"></div>

                    <div class="col-md-3">
                        <label class="label_situacao_reprodutiva_rel" style="font-size: 14px; font-weight: 500;">Histórico das Movimentações</label>
                    </div>
                </div>';

            echo '<div class="row">
                    <div class="col-md-1"></div>

                    <div class="col-md-10">
                        <table class="table table-striped table-advance table-hover" id="tbl_historico_movimentacao" style="font-size: 12px;" width="100%">
                                                 
                        <thead>
                            <tr>
                                <th> Data</th>
                                <th> Movimentação</th>
                                <th> Origem</th>
                                <th> Destino</th>
                            </tr>
                        </thead>';  

            echo '<tbody>';

            $sql = "select * from tbl_item_movimentacao 
                       inner join tbl_movimentacao
                               on tbl_movimentacao_id = tbl_ite_movimentacao_numero_id 
                            where tbl_ite_movimentacao_codigo_id_animal='$codigo'
                            order by tbl_movimentacao_id DESC"; 
            $rs = mysqli_query($conector, $sql); 

            while ($reg_ite_mov = mysqli_fetch_object($rs)){
                $codigo_animal = $reg_ite_mov->tbl_ite_movimentacao_codigo_animal;
                $data = new DateTime($reg_ite_mov->tbl_ite_movimentacao_data_emissao); 
                $data_edi = $data->format('d/m/Y');
                $tipo = $reg_ite_mov->tbl_movimentacao_tipo; 
                $origem = $reg_ite_mov->tbl_movimentacao_codigo_local_origem; 
                $destino = $reg_ite_mov->tbl_movimentacao_codigo_local_destino; 

                switch ($tipo) {
                    case 3:
                        $descricao_tipo = 'Venda';
                        break;
                    case 4:
                        $descricao_tipo = 'Compra';
                        break;
                    case 5:
                        $descricao_tipo = 'Transferência';
                        break;
                    case 888:
                        $descricao_tipo = 'Morte';
                        break;
                    default:
                        $descricao_tipo = 'Outras Saídas';
                        break;
                }

                $tab_origem = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$origem'");
                $num_rows = mysqli_num_rows($tab_origem);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_origem);
                    $desc_origem = $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_origem = '';
                }

                $tab_destino = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$destino'");
                $num_rows = mysqli_num_rows($tab_destino);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_destino);
                    $desc_destino = $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_destino= '';
                }

                echo "<tr>";
                echo "<td width='8%'>".$data_edi."</td>";
                echo "<td width='15%''>".$descricao_tipo."</td>";
                echo "<td width='25%'>".$desc_origem."</td>";
                echo "<td width='24%'>".$desc_destino."</td>";
                echo "</tr>";
            }

            if ($codigo_compra != '' && $codigo_compra != 0) {
                $nascimento = new DateTime($data_nascimento);
                $referencia  = new DateTime($data_compra);

                $intervalo = $nascimento->diff($referencia);

                $meses = ($intervalo->y * 12) + $intervalo->m;

                $meses_arredondado = $meses;

                if ($intervalo->d > 0) {
                    $meses_arredondado++;
                }

                $tbl_item_movimentacao = mysqli_query($conector, "
                    SELECT * 
                    FROM tbl_item_movimentacao 
                    INNER JOIN tbl_movimentacao
                        ON tbl_movimentacao_id = tbl_ite_movimentacao_numero_id 
                    INNER JOIN tbl_pessoa
                            ON tbl_movimentacao_codigo_local_origem = tbl_pessoa_id
                    WHERE tbl_movimentacao_id = '$codigo_compra'
                      AND tbl_movimentacao_lixeira = 0
                      AND tbl_ite_idade_meses_compra IN ($meses, $meses_arredondado)
                "); 

                if ($tbl_item_movimentacao && mysqli_num_rows($tbl_item_movimentacao) > 0) {
                    while ($reg_ite_mov = mysqli_fetch_object($tbl_item_movimentacao)) {
                        $data_compra_edi = $referencia->format('d/m/Y');
                        $local_destino = intval($reg_ite_mov->tbl_movimentacao_codigo_local_destino);

                    $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$local_destino'");
                        
                    $num_rows = mysqli_num_rows($tab_fazenda);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_fazenda);
                        $desc_local_destino= $reg->tbl_pessoa_nome;
                    }
                    else {
                        $desc_local_destino = '';
                    }

                        echo "<tr>";
                        echo "<td width='8%'>".$data_compra_edi."</td>";
                        echo "<td width='15%'>Compra</td>";
                        echo "<td width='25%'>".$reg_ite_mov->tbl_pessoa_nome."</td>";
                        echo "<td width='25%'>".$desc_local_destino."</td>";
                        echo "</tr>";
                        break;
                    }
                }
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div></div>';

            echo '<hr>';

            echo '<div class="row">
                <div class="col-md-1"></div>

                <div class="col-md-3" >
                    <label class="label_situacao_reprodutiva_rel" style="font-size: 14px; font-weight: 500;">Histórico das Pesagens</label>
                </div>

                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Peso Nascimento:&nbsp;</label>
                    <span>'.$peso_nasc.'</span>
                </div>

                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Peso Desmama:&nbsp;</label>
                    <span>'.$peso_desmama_edi.'</span>
                </div>

                <div class="col-md-2" style="font-size: 14px;">
                    <label class="label_situacao_reprodutiva_rel">Último Peso:&nbsp;</label>
                    <span>'.$ultimo_peso.'</span>
                </div>
                </div>

                <div class="row">
                    <div class="col-md-1"></div>

                    <div class="col-md-10">
                        <table class="table table-striped table-advance table-hover" id="tbl_historico_peso" style="font-size: 12px;" width="100%">
                                                 
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Motivo da Pesagem</th>
                                <th>Fazenda</th>
                                <th>Peso</th>
                            </tr>
                        </thead>';  

            echo '<tbody>';

            if ($codigo_compra != '' && $codigo_compra != 0) {
                $nascimento = new DateTime($data_nascimento);
                $referencia  = new DateTime($data_compra);

                $intervalo = $nascimento->diff($referencia);

                $meses = ($intervalo->y * 12) + $intervalo->m;

                $meses_arredondado = $meses;

                if ($intervalo->d > 0) {
                    $meses_arredondado++;
                }

                $tbl_item_movimentacao = mysqli_query($conector, "
                    SELECT * 
                    FROM tbl_item_movimentacao 
                    INNER JOIN tbl_movimentacao
                        ON tbl_movimentacao_id = tbl_ite_movimentacao_numero_id 
                    INNER JOIN tbl_pessoa
                            ON tbl_movimentacao_codigo_local_origem = tbl_pessoa_id
                    WHERE tbl_movimentacao_id = '$codigo_compra'
                      AND tbl_movimentacao_lixeira = 0
                      AND tbl_ite_idade_meses_compra IN ($meses, $meses_arredondado)
                "); 

                if ($tbl_item_movimentacao && mysqli_num_rows($tbl_item_movimentacao) > 0) {
                    while ($reg_ite_mov = mysqli_fetch_object($tbl_item_movimentacao)) {
                        $data_compra_edi = $referencia->format('d/m/Y');
                        $peso_compra = intval($reg_ite_mov->tbl_ite_movimentacao_peso);

                        echo "<tr>";
                        echo "<td width='8%'>".$data_compra_edi."</td>";
                        echo "<td width='15%'>Compra</td>";
                        echo "<td width='25%'>".$reg_ite_mov->tbl_pessoa_nome."</td>";
                        echo "<td width='25%'>".$peso_compra." Kg</td>";
                        echo "</tr>";
                        break;
                    }
                }
            }

            $tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem 
                INNER JOIN tbl_pesagem
                        ON tbl_pesagem_id = tbl_ite_pesagem_numero_id   
                WHERE tbl_ite_pesagem_codigo_id_animal='$codigo' and 
                      tbl_ite_pesagem_peso!=0 and tbl_pesagem_finalizada='S'
                ORDER BY tbl_ite_pesagem_data_emissao DESC, tbl_pesagem_id DESC"); 

            $num_rows_pesagem = mysqli_num_rows($tbl_pesagem);

            if ($num_rows_pesagem!=0) {
                while ($reg_ite_peso = mysqli_fetch_object($tbl_pesagem)){
                    $data = new DateTime($reg_ite_peso->tbl_ite_pesagem_data_emissao); 
                    $data_edi = $data->format('d/m/Y');
                    $epoca = $reg_ite_peso->tbl_pesagem_codigo_epoca; 
                    $origem = $reg_ite_peso->tbl_pesagem_codigo_local; 
                    $peso = intval($reg_ite_peso->tbl_ite_pesagem_peso); 

                    $tab_origem = mysqli_query($conector, "SELECT * FROM tbl_pessoa WHERE tbl_pessoa_id='$origem'");
                    $num_rows = mysqli_num_rows($tab_origem);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_origem);
                        $desc_origem = $reg->tbl_pessoa_nome;
                    }
                    else {
                        $desc_origem = '';
                    }

                    $tab_epoca = mysqli_query($conector, "SELECT * FROM tabela_epoca_pesagem WHERE tab_codigo_epoca_pesagem ='$epoca'");
                    $num_rows = mysqli_num_rows($tab_epoca);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_epoca);
                        $desc_epoca = $reg->tab_descricao_epoca_pesagem;
                    }
                    else {
                        $desc_epoca= '';
                    }

                    echo "<tr>";
                    echo "<td width='8%'>".$data_edi."</td>";
                    echo "<td width='15%'>".$desc_epoca."</td>";
                    echo "<td width='25%'>".$desc_origem."</td>";
                    echo "<td width='25%'>".$peso." Kg</td>";
                    echo "</tr>";
                } 
            }

            $data = new DateTime($reg_animal->tbl_animal_data_primeiro_peso); 
            $data_edi = $data->format('d/m/Y');

            $sql = "SELECT * FROM tbl_movimentacao_estoque
                INNER JOIN tbl_pessoa
                        ON tbl_pessoa_id = tbl_mov_estoque_local   
                WHERE tbl_mov_estoque_codigo_id_animal='$codigo' AND 
                      tbl_mov_estoque_entrada_saida='E' AND 
                      (tbl_mov_estoque_tipo_movimentacao='N' || tbl_mov_estoque_tipo_movimentacao='C')"; 

            $rs = mysqli_query($conector, $sql); 
            $num_rows = mysqli_num_rows($rs);

            if ($num_rows!=0) {
                $reg_estoque = mysqli_fetch_object($rs);
                $desc_origem = $reg_estoque->tbl_pessoa_nome;
            }
            else {
                $desc_origem = '';
            }

            if ($peso_nasc!=0 || $peso_nasc!=null) {
                echo "<tr>";
                echo "<td width='8%'>".$data_edi."</td>";
                echo "<td width='15%'>Nascimento</td>";
                echo "<td width='25%'>".$desc_origem."</td>";
                echo "<td width='25%'>".$peso_nasc." Kg</td>";
                echo "</tr>";
            }

            echo '</tbody></table></div></div>';    
        }   
?>

                                            </div> <!-- fim container -->
                                        </div> <!-- dados-->
                                    </div> <!--tab-content -->

                              <!--  </div> panel-body -->
                            </div> <!--panel -->      
                        </form>
                    </section> <!-- panel-group -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->


            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Listagem Animal - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="aguardar" tabindex="-1" role="dialog"    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p class="aguardar">Aguarde <i class='fa fa-spinner fa-spin fa-2x' ></i></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> <!-- wrapper -->
    </section><!--main-content -->

<?php 
  $javascript_file_name = 'relatorio_historico_animais.js';
  require 'rodape.php';
?>
