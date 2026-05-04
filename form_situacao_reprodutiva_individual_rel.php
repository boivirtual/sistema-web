<?php
    // AJUSTE DO PESO DE DESMAMA
    function calcular_peso_desmama($peso_desmama, $peso_nasc, $data_inicial, $data_final) {
        if ($peso_desmama!='' && $peso_desmama!=0) {
            if ($peso_nasc=='' || $peso_nasc==0) {
                $peso_nasc = 30;
            }

            $diferenca = strtotime($data_final) - strtotime($data_inicial);
            $dias = floor($diferenca / (60 * 60 * 24));

            $diferenca_peso = $peso_desmama - $peso_nasc;
            $gmd = $diferenca_peso/$dias;

            $peso_desmama = $peso_nasc + ($gmd * 205);
            $peso_desmama_edi = number_format($peso_desmama,2,',','.');
        }
        else {
            $peso_desmama_edi = '';
        }

        return $peso_desmama_edi;
    }
    // FIM AJUSTE DO PESO DE DESMAMA

    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $data_hoje = date("Y-m-d");

    @ session_start(); 

    $_SESSION['opcao_situacao_reprodutica_rel']='I';
    
    //$codigo_alfa = $_REQUEST["codigo_alfa"];
    $codigo_alfa_numerico = $_REQUEST["codigo_alfa_numerico"];

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

    /*if ($codigo_alfa=='') {
        $codigo_consulta = $codigo_numerico;            
    }
    else {
        $codigo_consulta = $codigo_alfa . '-' . $codigo_numerico;
    }*/

    $mensagem = '';

    $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
        INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_animal_codigo_fazenda
        WHERE tbl_animal_codigo_alfa='$codigo_alfa_consulta' AND 
              tbl_animal_codigo_numerico='$codigo_numerico_consulta' AND
              tbl_animal_sexo='F'"); 

    $num_rows_animais = mysqli_num_rows($tbl_animais);

    if ($num_rows_animais!=0) {
        $reg_animal = mysqli_fetch_object($tbl_animais);
        $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
        $ativo = $reg_animal->tbl_animal_ativo;
        $animal_situacao = $reg_animal->tbl_animal_situacao;
        $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
        $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
        $descarte = $reg_animal->tbl_animal_descarte_reproducao;

        if ($reg_animal->tbl_animal_descarte_em!=null) {
            $descarte_em = new DateTime($reg_animal->tbl_animal_descarte_em);
            $descarte_em_edi = $descarte_em->format('d/m/Y H:i:s');
        }
        else {
            $descarte_em_edi = '';
        }
        $descarte_por = 'Por ' . $reg_animal->tbl_animal_descarte_por .' em '. $descarte_em_edi;
        $nome_pessoa = $reg_animal->tbl_pessoa_nome; 
        $descricao_filtro = $nome_pessoa;     
        $codigo_origem = $reg_animal->tbl_animal_codigo_origem;
        $pai = $reg_animal->tbl_animal_codigo_pai;

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

        $mae =  $reg_animal->tbl_animal_codigo_mae;

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

        if ($reg_animal->tbl_animal_em_estacao_monta=='S') {
            $em_estacao_monta ='SIM';
        }
        else {
            $em_estacao_monta ='NÃO';
        }

        // Verifica quantas estações teve para a femea
        $qtd_estacoes = 0;
        $id_estacao_ant = 0;

        $tbl_item_cobertura = mysqli_query($conector, "SELECT * from tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
            WHERE tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' AND   
                  tbl_cobertura_controle = 'C' AND 
                  tbl_cobertura_lixeira = 0
            ORDER BY tbl_cobertura_codigo_estacao_monta ASC"); 

        $num_rows_itens = mysqli_num_rows($tbl_item_cobertura);

        if ($num_rows_itens!=0) {
            while ($reg_item = mysqli_fetch_object($tbl_item_cobertura)) {
                $id_estacao = $reg_item->tbl_cobertura_codigo_estacao_monta;

                if ($id_estacao != $id_estacao_ant) {
                    $qtd_estacoes++;
                    $id_estacao_ant=$id_estacao;
                }
            }
        }

        $tbl_item_cobertura = mysqli_query($conector, "SELECT * from tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
            INNER JOIN tbl_parametro_estacao_monta
                    ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
            WHERE tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' AND 
                  tbl_cobertura_controle = 'C' AND 
                   tbl_cobertura_lixeira = 0
            ORDER BY tbl_ite_cobertura_numero_id DESC LIMIT 1"); 
            
        $num_rows_itens = mysqli_num_rows($tbl_item_cobertura);

        if ($num_rows_itens!=0) {
            $reg_item = mysqli_fetch_object($tbl_item_cobertura);
            $diagnostico = $reg_item->tbl_ite_cobertura_resultado_diagnostico;
            $nascido = $reg_item->tbl_ite_cobertura_nascido;

            $id_estacao = $reg_item->tbl_cobertura_codigo_estacao_monta;
            $estacao_monta = $reg_item->tbl_par_estacao_nome;
        }
        else {
            $estacao_monta = '';
            $id_estacao = 0;
            $diagnostico = '';
            $nascido = '';
        }

        // verifica abortos/absorsão
        $tbl_aborto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
            where tbl_mov_estoque_codigo_mae='$codigo_animal_id' and 
                  tbl_mov_estoque_entrada_saida='A'");

        $qtd_abortos = mysqli_num_rows($tbl_aborto);

        // primeiro verifica quantos partos
        $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
            WHERE tbl_animal_codigo_mae='$codigo_animal_id'");

        $qtd_partos = mysqli_num_rows($tbl_filhos);

        // verifica parto natimorto
        $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
            where tbl_mov_estoque_codigo_mae='$codigo_animal_id' and 
                  tbl_mov_estoque_codigo_id_animal=999999999 and 
                  tbl_mov_estoque_entrada_saida='E' and 
                  tbl_mov_estoque_tipo_movimentacao='N'");
        $qtd_natimorto = mysqli_num_rows($tbl_natimorto);

        // Verifica situação da Fêmea
        $situacao = '';

        if ($diagnostico=='P' && $nascido=='') {
            $situacao = 'Prenha';
        }
        else {
            // Verifica qual o ultimo parto para saber a idade
            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo_animal_id'
                ORDER BY tbl_animal_data_nascimento DESC limit 1");

            $ultimo_filho = mysqli_num_rows($tbl_filhos);

            if ($ultimo_filho!=0) {
                $reg_filhos = mysqli_fetch_object($tbl_filhos);
                $nascimento_filho = $reg_filhos->tbl_animal_data_nascimento;  
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($nascimento_filho); // Data de Nascimento
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade_ano = $idade_acompanhamento->format('%Y');
                $idade_mes = $idade_acompanhamento->format('%m');
                $idade_filho = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                $data_ref = date("Y-m-d");
                $diferenca = strtotime($data_ref) - strtotime($nascimento_filho);
                $dias_nascimento = floor($diferenca / (60 * 60 * 24));

                $bezerro_ativo = $reg_filhos->tbl_animal_ativo;

                if ($idade_filho < 8) {
                    if ($bezerro_ativo=='S') {
                        $situacao = 'Parida';
                    }
                    else {
                        if ($dias_nascimento<=35) {
                            $situacao = 'Parida';
                        }
                        else {
                            $situacao = 'Solteira';
                        }
                    }
                }
                else {
                    $situacao = 'Solteira';
                }
            }
            else {
                $situacao = 'Solteira';
            }
        }
    }
    else {
        $mensagem = ' - Registro não encontrado';
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

    <style type="text/css">
        /*.card-title {
            color: #939ba2;
            opacity: 0.8;
            font-size: 1.525rem;
            font-weight: 500;
            padding-left: 10px;
            padding-top: 10px;
        }*/

        .label_situacao_reprodutiva_rel{
          font-weight: 600;
          text-align: left !important;
        }

        table.dataTable thead th { border-bottom: 0;}
    </style>

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php";
            include "opcoes_menu.php"; 
            include "limpar_secao_compra_venda.php"; 
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php"; 
            include "limpar_secao_pesagem.php"; 
            include "limpar_secao_movimentacao.php"; 
            include "limpar_secao_nutricao.php"; 
            include "limpar_secao_nascimento.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Reprodutivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Situação Reprodutiva</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="far fa-file-alt"></i> Situação Reprodutiva</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data">

                            <div class="panel"> 
                                <div class="tab-content">
                                    <div id="dados" class="tab-pane active" style="padding-right: 15px; padding-left: 15px;">
                                        
                                        <input type="hidden" id="expande_tela" value="S">
                                        <!--<input type="hidden" id="codigo_alfa_filtro"
                                            >-->
                                        <input type="hidden" id="codigo_number_filtro"
                                            <?php echo "value='".$codigo_alfa_numerico."'";?>>


<?php 
    if ($num_rows_animais==0) :
        mysqli_close($conector);
?>
    <div class="row">
        <div class="col-md-9" style="padding-top: 10px; margin-bottom: 10px; text-align: center; font-size: 16px;">
            <label>
                    Fêmea: <?php echo $codigo_alfa_numerico . $mensagem;?>
            </label>
        </div>

        <div class="col-md-3" style="padding-top: 10px; padding-bottom: 10px;">  
            <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
            </button>
        </div>
    </div>
<?php else : ?>

    <div class="row">
        <div class="col-md-9" style="padding-top: 10px; margin-bottom: 10px; font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Fêmea:&nbsp;</label>
            <span> <?php echo $codigo_alfa_numerico . ' - ' . $situacao;?>
            </span>
        </div>

        <div class="col-md-3" style="padding-top: 10px; padding-bottom: 5px;">  
            <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
            </button>

            <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" onClick="situacao_reprodutiva_individual_excel()">Excel
                </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Nascimento:&nbsp;</label>
            <span><?php echo $data_nascimento_edi;?></span>
        </div>

        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">&nbsp;Idade:&nbsp;</label>
            <span><?php echo $desc_idade;?></span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Categoria:&nbsp;</label>
            <span><?php echo $desc_categoria;?></span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Raça:&nbsp;</label>
            <span><?php echo $descricao_raca;?></span>
        </div>

        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Pelagem:&nbsp;</label>
            <span><?php echo $descricao_pelagem;?></span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Fazenda:&nbsp;</label>
            <span><?php echo $descricao_filtro;?></span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Origem:&nbsp;</label>
            <span><?php echo $desc_origem;?></span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Animal Ativo:&nbsp;</label>
            <?php
            if ($ativo == 'Sim') :
            ?>

            <span style="color: green;"><?php echo $ativo;?></span>

            <?php
            else :
            ?>

            <span style="color: red;"><?php echo $ativo;?></span>

            <?php
            endif;
            ?>
        </div>

        <?php
            if ($ativo == 'Não') :
        ?>
            <div class="col-md-2"style="font-size: 14px;">
                <label class="label_situacao_reprodutiva_rel">Situação:&nbsp;</label>
                <span style="color: red;"><?php echo $animal_situacao;?></span>
            </div>

        <?php
            endif;
        ?>
    </div>

    <div class="row">
        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Pai:&nbsp;</label>
            <span><?php echo $descricao_pai;?></span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Mãe:&nbsp;</label>
            <span><?php echo $descricao_mae;?></span>
        </div>
    </div>    

    <div class="row">
        <?php
            if ($descarte == 'S') :
        ?>
            <div class="col-md-8" style="font-size: 14px;">
                <label class="label_situacao_reprodutiva_rel">Descartado para reprodução:&nbsp;</label>
                <span style="color: red"><?php echo $descarte_por;?></span>
            </div>
        <?php
            endif;
        ?>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-1"></div>

        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Estações de Monta:&nbsp;</label>
            <span><?php echo $qtd_estacoes;?></span>
        </div>

        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Partos:&nbsp;</label>
            <span><?php echo $qtd_partos;?></span>
        </div>

        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Natimorto:&nbsp;</label>
            <span><?php echo $qtd_natimorto;?></span>
        </div>

        <div class="col-md-2" style="font-size: 14px;">
            <label class="label_situacao_reprodutiva_rel">Abortos:&nbsp;</label>
            <span><?php echo $qtd_abortos;?></span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-advance table-hover" id="tabela_estacoes" style="font-size: 12px;" width="100%">
                                     
            <thead>
                <tr>
                    <th>Estação</th>
                    <th>Data</th>
                    <th>Fazenda</th>
                    <th style="text-align:center;">Cobertura(s)</th>
                    <th>Diagnostico Final</th>
                    <th>Touro/Sêmen</th>
                    <th style="text-align:center;">Nascimento Bezerro</th>
                    <th style="text-align:center;">ID</th>
                    <th style="text-align:center;">Sexo</th>
                    <th>Peso Desmama</th>
                    <th>Raça</th>
                    <th>Idade</th>
                </tr>
            </thead>  

            <tbody>

<?php
    $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        INNER JOIN tbl_cobertura
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
        INNER JOIN tbl_pessoa
                ON tbl_cobertura_codigo_local = tbl_pessoa_id 
        WHERE tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' AND 
              (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M') AND 
              tbl_cobertura_lixeira = 0
        ORDER BY tbl_cobertura_codigo_estacao_monta DESC, tbl_cobertura_id ASC");

    $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);  

    if ($num_rows_cobertura!=0) {
        $estacao_anterior = 0;
        $numero_coberturas = 0;

        while ($reg_cobertura = mysqli_fetch_object($tbl_cobertura)) {
            $cobertura = $reg_cobertura->tbl_cobertura_id;
            $controle = $reg_cobertura->tbl_cobertura_controle;
            $item = $reg_cobertura->tbl_ite_cobertura_numero_item;
            $estacao = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
            $protocolo = $reg_cobertura->tbl_cobertura_protocoloiatf;
            $nome_fazenda = $reg_cobertura->tbl_pessoa_nome;
            $id_touro_semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;
            $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;
            $situacao_femea = $reg_cobertura->tbl_ite_cobertura_situacao_femea_nascido_outro;

            $tab_parametro = mysqli_query($conector, "select * from tbl_parametro_estacao_monta 
                where tbl_par_estacao_id='$estacao'");
            $num_rows_parametro = mysqli_num_rows($tab_parametro);

            if ($num_rows_parametro!=0){
                $reg_estacao = mysqli_fetch_object($tab_parametro);
                $desc_estacao = $reg_estacao->tbl_par_estacao_nome;
                $data_parametro_estacao = $reg_estacao->tbl_par_estacao_monta_inicial;
            }
            else {
                $desc_estacao = 'Monta';
                $data_parametro_estacao = 0;
            }

            $data_emissao_edi = '';

            if ($controle=='C') {
                if ($data_parametro_estacao!=0) {
                    $data_emissao = new DateTime($data_parametro_estacao);
                    $data_emissao_edi = $data_emissao->format('d/m/Y');
                }
            }
            else if ($controle=='M'){
                $data_prenhes = $reg_cobertura->tbl_ite_cobertura_data_prenhes;
                $dataObj = DateTime::createFromFormat('Y-m-d', $data_prenhes);

                if ($dataObj !== false) {
                    $data_emissao = new DateTime($data_prenhes);
                    $data_emissao_edi = $data_emissao->format('d/m/Y');
                } 
                else {
                    $data_emissao = new DateTime($reg_cobertura->tbl_ite_cobertura_data_emissao);
                    $data_emissao_edi = $data_emissao->format('d/m/Y');
                }
            }

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$id_touro_semen'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_semem_nome;
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$id_touro_semen'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . ltrim($reg->tbl_animal_codigo_numerico, "0");
                }
                else {
                    $descricao_pai = '';
                }
            }

            $tbl_item_iatf = mysqli_query($conector, "SELECT * FROM tbl_item_protocoloiatf WHERE tbl_ite_protocoloiatf_protocolo_id='$protocolo'");
            $qtd_item_iatf = mysqli_num_rows($tbl_item_iatf);

            $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
            $tem_d0 = $reg_cobertura->tbl_ite_cobertura_dia_1;

            $tem_inseminacao = '';
            
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

            if (($tem_inseminacao=='S' && $diagnostico!='P' && $diagnostico!='N') || ($controle=='M' && $diagnostico!='P' && $diagnostico!='N')) {
                $desc_diagnostico = 'Aguardando Diagnóstico';
            }
            else if ($diagnostico=='P') {
                $desc_diagnostico = 'Positivo';
            }
            else if ($diagnostico=='N'){
                $desc_diagnostico = 'Negativo';
            }
            else {
                $desc_diagnostico = 'Aguardando Inseminação';
            } 

            if ($estacao!=$estacao_anterior && $estacao_anterior!=0) {
                // pega dados do nascimento do bezzero
                $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
                    INNER JOIN tbl_pessoa
                            ON tbl_pessoa_id = tbl_animal_codigo_fazenda
                    WHERE tbl_animal_codigo_mae='$codigo_animal_id' AND 
                          (tbl_animal_estacao_monta_nascimento='$estacao_anterior' OR 
                           tbl_animal_codigo_cobertura = '$cobertura_anterior') 
                    ORDER BY tbl_animal_codigo_id DESC"); 

                $num_rows_animais = mysqli_num_rows($tbl_animais);
                $gemelar=0;

                if ($num_rows_animais!=0) {
                    while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                        $codigo_numerico = ltrim($reg_animal->tbl_animal_codigo_numerico, "0");
                        $ativo = $reg_animal->tbl_animal_ativo;
                        $raca_id = $reg_animal->tbl_animal_codigo_raca;
                        $sexo = $reg_animal->tbl_animal_sexo;
                        $peso_desmama = $reg_animal->tbl_animal_peso_desmama;
                        $peso_nasc = $reg_animal->tbl_animal_primeiro_peso;
                        $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                        $data_final = $reg_animal->tbl_animal_data_desmama;
                        $situacao = $reg_animal->tbl_animal_situacao;

                        $peso_desmama_edi = calcular_peso_desmama($peso_desmama, $peso_nasc, $data_inicial, $data_final);

                        if ($codigo_alfa=='') {
                            $codigo_animal_edi = $codigo_numerico;            
                        }
                        else {
                            $codigo_animal_edi = $codigo_alfa . '-' . $codigo_numerico;
                        }

                        $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca ='$raca_id'");
                        $num_rows_raca = mysqli_num_rows($tab_raca);

                        if ($num_rows_raca!=0){
                            $reg = mysqli_fetch_object($tab_raca);
                            $descricao_raca = $reg->tab_descricao_raca;
                        }
                        else{
                            $descricao_raca = '';
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
                        $idade_acompanhamento_mostra_dia = $idade_acompanhamento->format('%d');

                        $idade_ano = $idade_acompanhamento->format('%Y');
                        $idade_mes = $idade_acompanhamento->format('%m');
                        $idade_dia = $idade_acompanhamento->format('%d');

                        if ($idade_ano==0 && $idade_mes!=0) {
                            $idade_animal = $idade_mes . ' mes(es)';
                        }
                        else if ($idade_ano!=0 && $idade_mes==0){
                            $idade_animal = $idade_ano . ' ano(s)';
                        }
                        else if ($idade_ano!=0 && $idade_mes!=0) {
                            $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
                        }
                        else if ($idade_ano==0 && $idade_mes==0){
                            $idade_animal = $idade_dia . ' dia(s)';
                        }
                        else {
                            $idade_animal = '';
                        }

                        $data = new DateTime($reg_animal->tbl_animal_data_nascimento);
                        $data_nascimento_edi = $data->format('d/m/Y');

                        $data_ref = date("Y-m-d");
                        $diferenca = strtotime($data_ref) - strtotime($reg_animal->tbl_animal_data_nascimento);
                        $dias_parto = floor($diferenca / (60 * 60 * 24));

                        echo '<tr>';

                        if ($gemelar==0) {
                            echo '<td width="7%">'.$desc_estacao_anterior.'</td>';
                            echo '<td width="8%">'.$data_emissao_edi_anterior.'</td>';
                            echo '<td width="14%">'.$nome_fazenda_anterior.'</td>';
                            echo '<td align="center" width="5%">'.$numero_coberturas.'</td>';
                            echo '<td width="10%">'.$diagnostico_anterior.'</td>';
                            echo '<td width="7%">'.$descricao_pai_anterior.'</td>';
                            $gemelar++;
                        }
                        else {
                            echo '<td width="7%"></td>';
                            echo '<td width="8%"></td>';
                            echo '<td width="14%"></td>';
                            echo '<td align="center" width="5%"></td>';
                            echo '<td width="10%"></td>';
                            echo '<td width="7%"></td>';
                        }

                        if ($situacao!='') {
                            echo '<td align="center" style="color: red;" width="12%">'.$data_nascimento_edi.'</td>';
                            echo '<td align="center" style="color: red;" width="8%">'.$codigo_animal_edi.'</td>';
                            echo '<td align="center" style="color: red;" width="5%">'.$sexo.'</td>';
                            echo '<td align="right" style="color: red;" width="8%">'.$peso_desmama_edi.'</td>';
                            echo '<td style="color: red;" width="8%">'.$descricao_raca.'</td>';
                            //echo '<td style="color: red;" width="8%">'.$idade_animal.'</td>';
                            echo '<td  style="color: red;" width="8%">';
                            echo $idade_animal;
                            echo '&nbsp;&nbsp;<i class="icon_info_alt" style="color: blue;" data-toggle="tooltip" data-placement="left" title="Dias do Parto: ' . $dias_parto . '"></i>';
                            echo '</td>';
                        }
                        else {
                            echo '<td align="center" width="12%">'.$data_nascimento_edi.'</td>';
                            echo '<td align="center" width="8%">'.$codigo_animal_edi.'</td>';
                            echo '<td align="center"  width="5%">'.$sexo.'</td>';
                            echo '<td align="right" width="8%">'.$peso_desmama_edi.'</td>';
                            echo '<td width="8%">'.$descricao_raca.'</td>';
                            //echo '<td width="8%">'.$idade_animal.'</td>';
                            echo '<td width="8%">';
                            echo $idade_animal;
                            echo '&nbsp;&nbsp;<i class="icon_info_alt" style="color: blue;" data-toggle="tooltip" data-placement="left" title="Dias do Parto: ' . $dias_parto . '"></i>';
                            echo '</td>';
                        }
                        echo '</tr>';
                    }
                }
                else {
                    if ($nascido_anterior=='M') {
                        $codigo_animal_edi='Natimorto';
                    }
                    else if ($nascido_anterior=='A') {
                        $codigo_animal_edi='Aborto';
                    }
                    else if ($situacao_femea_anterior=='M'){
                        $codigo_animal_edi='F Morreu';
                    }
                    else if ($situacao_femea_anterior=='V'){
                        $codigo_animal_edi='F Vendida';
                    }
                    else {
                        $codigo_animal_edi='';
                    }

                    // data da movimentacao na tabela de estoque

                    $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_movimentacao_estoque 
                        WHERE tbl_mov_estoque_cobertura_numero_id='$cobertura_anterior' AND 
                              tbl_mov_estoque_cobertura_numero_item='$item_anterior'"); 

                    $num_rows_estoque = mysqli_num_rows($tbl_estoque);

                    if ($num_rows_estoque!=0) {
                        $reg_estoque = mysqli_fetch_object($tbl_estoque);
                        $data = new DateTime($reg_estoque->tbl_mov_estoque_nascimento); 
                        $data_nascimento_edi = $data->format('d/m/Y');
                        $sexo = $reg_estoque->tbl_mov_estoque_sexo;
                    }
                    else {
                        $data_nascimento_edi = '';
                        $sexo = '';
                    }
                    
                    $descricao_raca = '';
                    $idade_animal = '';
                    $peso_desmama_edi = '';

                    echo '<tr>';
                    echo '<td width="7%">'.$desc_estacao_anterior.'</td>';
                    echo '<td width="8%">'.$data_emissao_edi_anterior.'</td>';
                    echo '<td width="14%">'.$nome_fazenda_anterior.'</td>';
                    echo '<td align="center" width="5%">'.$numero_coberturas.'</td>';
                    echo '<td width="10%">'.$diagnostico_anterior.'</td>';
                    echo '<td width="7%">'.$descricao_pai_anterior.'</td>';
                    echo '<td align="center" width="12%">'.$data_nascimento_edi.'</td>';
                    echo '<td align="center" width="8%">'.$codigo_animal_edi.'</td>';
                    echo '<td align="center"  width="5%">'.$sexo.'</td>';
                    echo '<td align="right" width="8%">'.$peso_desmama_edi.'</td>';
                    echo '<td width="8%">'.$descricao_raca.'</td>';
                    echo '<td width="8%">'.$idade_animal.'</td>';
                    echo '</tr>';
                }
                // fim dados nascimento do bezzero                

                $estacao_anterior=$estacao;
                $numero_coberturas = 1;
                $diagnostico_anterior = $desc_diagnostico;
                $desc_estacao_anterior = $desc_estacao;
                $nome_fazenda_anterior = $nome_fazenda;
                $descricao_pai_anterior = $descricao_pai;
                $nascido_anterior = $nascido;
                $situacao_femea_anterior = $situacao_femea;
                $cobertura_anterior = $cobertura;
                $item_anterior = $item;
                $data_emissao_edi_anterior = $data_emissao_edi;

                if ($controle=='M' && $desc_diagnostico=='Aguardando Diagnóstico') {
                    $numero_coberturas--;
                }
            }
            else {
                $estacao_anterior=$estacao;
                $numero_coberturas++;
                $diagnostico_anterior = $desc_diagnostico;
                $desc_estacao_anterior = $desc_estacao;
                $nome_fazenda_anterior = $nome_fazenda;
                $descricao_pai_anterior = $descricao_pai;
                $nascido_anterior = $nascido;
                $situacao_femea_anterior = $situacao_femea;
                $cobertura_anterior = $cobertura;
                $item_anterior = $item;
                $data_emissao_edi_anterior = $data_emissao_edi;

                if ($controle=='M' && $desc_diagnostico=='Aguardando Diagnóstico') {
                    $numero_coberturas--;
                }
            }
        }

        // pega dados do nascimento do bezzero

        $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
            INNER JOIN tbl_pessoa
                    ON tbl_pessoa_id = tbl_animal_codigo_fazenda
            WHERE tbl_animal_codigo_mae='$codigo_animal_id' AND 
                  (tbl_animal_estacao_monta_nascimento='$estacao_anterior' OR 
                  tbl_animal_codigo_cobertura = '$cobertura_anterior')
            ORDER BY tbl_animal_codigo_id DESC"); 

        $num_rows_animais = mysqli_num_rows($tbl_animais);
        $gemelar = 0;

        if ($num_rows_animais!=0) {
            while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                $codigo_numerico = ltrim($reg_animal->tbl_animal_codigo_numerico, "0");
                $ativo = $reg_animal->tbl_animal_ativo;
                $situacao = $reg_animal->tbl_animal_situacao;
                $raca_id = $reg_animal->tbl_animal_codigo_raca;
                $sexo = $reg_animal->tbl_animal_sexo;
                $peso_desmama = $reg_animal->tbl_animal_peso_desmama;
                $peso_nasc = $reg_animal->tbl_animal_primeiro_peso;
                $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                $data_final = $reg_animal->tbl_animal_data_desmama;

                $peso_desmama_edi = calcular_peso_desmama($peso_desmama, $peso_nasc, $data_inicial, $data_final);

                if ($codigo_alfa=='') {
                    $codigo_animal_edi = $codigo_numerico;            
                }
                else {
                    $codigo_animal_edi = $codigo_alfa . '-' . $codigo_numerico;
                }

                $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca ='$raca_id'");
                $num_rows_raca = mysqli_num_rows($tab_raca);

                if ($num_rows_raca!=0){
                    $reg = mysqli_fetch_object($tab_raca);
                    $descricao_raca = $reg->tab_descricao_raca;
                }
                else{
                    $descricao_raca = '';
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
                $idade_acompanhamento_mostra_dia = $idade_acompanhamento->format('%d');

                $idade_ano = $idade_acompanhamento->format('%Y');
                $idade_mes = $idade_acompanhamento->format('%m');
                $idade_dia = $idade_acompanhamento->format('%d');

                if ($idade_ano==0 && $idade_mes!=0) {
                    $idade_animal = $idade_mes . ' mes(es)';
                }
                else if ($idade_ano!=0 && $idade_mes==0){
                    $idade_animal = $idade_ano . ' ano(s)';
                }
                else if ($idade_ano!=0 && $idade_mes!=0) {
                    $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
                }
                else if ($idade_ano==0 && $idade_mes==0){
                    $idade_animal = $idade_dia . ' dia(s)';
                }
                else {
                    $idade_animal = '';
                }

                $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
                $data_nascimento_edi = $data->format('d/m/Y');

                $data_ref = date("Y-m-d");
                $diferenca = strtotime($data_ref) - strtotime($reg_animal->tbl_animal_data_nascimento);
                $dias_parto = floor($diferenca / (60 * 60 * 24));

                echo '<tr>';

                if ($gemelar==0) {
                    echo '<td width="7%">'.$desc_estacao_anterior.'</td>';
                    echo '<td width="8%">'.$data_emissao_edi_anterior.'</td>';
                    echo '<td width="14%">'.$nome_fazenda_anterior.'</td>';
                    echo '<td align="center" width="5%">'.$numero_coberturas.'</td>';
                    echo '<td width="10%">'.$diagnostico_anterior.'</td>';
                    echo '<td width="7%">'.$descricao_pai_anterior.'</td>';
                    $gemelar++;
                }
                else {
                    echo '<td width="7%"></td>';
                    echo '<td width="8%"></td>';
                    echo '<td width="14%"></td>';
                    echo '<td align="center" width="5%"></td>';
                    echo '<td width="10%"></td>';
                    echo '<td width="7%"></td>';
                }

                if ($situacao!='') {
                    echo '<td align="center" style="color: red;" width="12%">'.$data_nascimento_edi.'</td>';
                    echo '<td align="center" style="color: red;" width="8%">'.$codigo_animal_edi.'</td>';
                    echo '<td align="center" style="color: red;" width="5%">'.$sexo.'</td>';
                    echo '<td align="right" style="color: red;" width="8%">'.$peso_desmama_edi.'</td>';
                    echo '<td style="color: red;" width="8%">'.$descricao_raca.'</td>';
                    //echo '<td style="color: red;" width="8%">'.$idade_animal.'</td>';
                    echo '<td  style="color: red;" width="8%">';
                    echo $idade_animal;
                    echo '&nbsp;&nbsp;<i class="icon_info_alt" style="color: blue;" data-toggle="tooltip" data-placement="left" title="Dias do Parto: ' . $dias_parto . '"></i>';
                    echo '</td>';
                }
                else {
                    echo '<td align="center" width="12%">'.$data_nascimento_edi.'</td>';
                    echo '<td align="center" width="8%">'.$codigo_animal_edi.'</td>';
                    echo '<td align="center"  width="5%">'.$sexo.'</td>';
                    echo '<td align="right" width="8%">'.$peso_desmama_edi.'</td>';
                    echo '<td width="8%">'.$descricao_raca.'</td>';
                    //echo '<td width="8%">'.$idade_animal.'</td>';
                    echo '<td width="8%">';
                    echo $idade_animal;
                    echo '&nbsp;&nbsp;<i class="icon_info_alt" style="color: blue;" data-toggle="tooltip" data-placement="left" title="Dias do Parto: ' . $dias_parto . '"></i>';
                    echo '</td>';
                }
                echo '</tr>';
            }
        }
        else {
            if ($nascido_anterior=='M') {
                $codigo_animal_edi='Natimorto';
            }
            else if ($nascido_anterior=='A') {
                $codigo_animal_edi='Aborto';
            }
            else if ($situacao_femea_anterior=='M'){
                $codigo_animal_edi='F Morreu';
            }
            else if ($situacao_femea_anterior=='V'){
                $codigo_animal_edi='F Vendida';
            }
            else {
                $codigo_animal_edi='';
            }

            // data da movimentacao na tabela de estoque

            $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_cobertura_numero_id='$cobertura_anterior' AND 
                      tbl_mov_estoque_cobertura_numero_item='$item_anterior'"); 

            $num_rows_estoque = mysqli_num_rows($tbl_estoque);

            if ($num_rows_estoque!=0) {
                $reg_estoque = mysqli_fetch_object($tbl_estoque);
                $data = new DateTime($reg_estoque->tbl_mov_estoque_nascimento); 
                $data_nascimento_edi = $data->format('d/m/Y');
                $sexo = $reg_estoque->tbl_mov_estoque_sexo;
            }
            else {
                $data_nascimento_edi = '';
                $sexo = '';
            }

            $descricao_raca = '';
            $idade_animal = '';
            $peso_desmama_edi = '';
            $situacao = '';

            echo '<tr>';
            echo '<td width="7%">'.$desc_estacao_anterior.'</td>';
            echo '<td width="8%">'.$data_emissao_edi_anterior.'</td>';
            echo '<td width="14%">'.$nome_fazenda_anterior.'</td>';
            echo '<td align="center" width="5%">'.$numero_coberturas.'</td>';
            echo '<td width="10%">'.$diagnostico_anterior.'</td>';
            echo '<td width="7%">'.$descricao_pai_anterior.'</td>';

            if ($situacao!='') {
                echo '<td align="center" style="color: red;" width="12%">'.$data_nascimento_edi.'</td>';
                echo '<td align="center" style="color: red;" width="8%">'.$codigo_animal_edi.'</td>';
                echo '<td align="center" style="color: red;" width="5%">'.$sexo.'</td>';
                echo '<td align="right" style="color: red;" width="8%">'.$peso_desmama_edi.'</td>';
                echo '<td style="color: red;" width="8%">'.$descricao_raca.'</td>';
                echo '<td style="color: red;" width="8%">'.$idade_animal.'</td>';
            }
            else {
                echo '<td align="center" width="12%">'.$data_nascimento_edi.'</td>';
                echo '<td align="center" width="8%">'.$codigo_animal_edi.'</td>';
                echo '<td align="center"  width="5%">'.$sexo.'</td>';
                echo '<td align="right" width="8%">'.$peso_desmama_edi.'</td>';
                echo '<td width="8%">'.$descricao_raca.'</td>';
                echo '<td width="8%">'.$idade_animal.'</td>';
            }
            echo '</tr>';
        }
        // fim dados nascimento do bezzero                
    }  

    // Pega animais sem estação de monta
    $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
        INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_animal_codigo_fazenda
        INNER JOIN tabela_racas
                ON tbl_animal_codigo_raca = tab_codigo_raca 
        WHERE tbl_animal_codigo_mae='$codigo_animal_id' AND 
              (tbl_animal_estacao_monta_nascimento='' OR tbl_animal_estacao_monta_nascimento is null)
        ORDER BY tbl_animal_codigo_id DESC"); 

    $num_rows_animais = mysqli_num_rows($tbl_animais);

    if ($num_rows_animais!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico = ltrim($reg_animal->tbl_animal_codigo_numerico, "0");
            $ativo = $reg_animal->tbl_animal_ativo;
            $situacao = $reg_animal->tbl_animal_situacao;
            $nome_fazenda = $reg_animal->tbl_pessoa_nome;
            $pai = $reg_animal->tbl_animal_codigo_pai;
            $descricao_raca = $reg_animal->tab_descricao_raca;
            $raca_id = $reg_animal->tbl_animal_codigo_raca;
            $sexo = $reg_animal->tbl_animal_sexo;
            $peso_desmama = $reg_animal->tbl_animal_peso_desmama;
            $peso_nasc = $reg_animal->tbl_animal_primeiro_peso;
            $data_inicial = $reg_animal->tbl_animal_data_nascimento;
            $data_final = $reg_animal->tbl_animal_data_desmama;

            $peso_desmama_edi = calcular_peso_desmama($peso_desmama, $peso_nasc, $data_inicial, $data_final);

            if ($codigo_alfa=='') {
                $codigo_animal_edi = $codigo_numerico;            
            }
            else {
                $codigo_animal_edi = $codigo_alfa . '-' . $codigo_numerico;
            }

            $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca ='$raca_id'");
            $num_rows_raca = mysqli_num_rows($tab_raca);

            if ($num_rows_raca!=0){
                $reg = mysqli_fetch_object($tab_raca);
                $descricao_raca = $reg->tab_descricao_raca;
            }
            else{
                $descricao_raca = '';
            }

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
            $idade_acompanhamento_mostra_dia = $idade_acompanhamento->format('%d');

            $idade_ano = $idade_acompanhamento->format('%Y');
            $idade_mes = $idade_acompanhamento->format('%m');
            $idade_dia = $idade_acompanhamento->format('%d');

            if ($idade_ano==0 && $idade_mes!=0) {
                $idade_animal = $idade_mes . ' mes(es)';
            }
            else if ($idade_ano!=0 && $idade_mes==0){
                $idade_animal = $idade_ano . ' ano(s)';
            }
            else if ($idade_ano!=0 && $idade_mes!=0) {
                $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
            }
            else if ($idade_ano==0 && $idade_mes==0){
                $idade_animal = $idade_dia . ' dia(s)';
            }
            else {
                $idade_animal = '';
            }

            $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
            $data_nascimento_edi = $data->format('d/m/Y');

            if ($situacao!='') {
                echo '<tr>';
                echo '<td width="7%">Sem Estação</td>';
                echo '<td width="8%"></td>';
                echo '<td width="14%"></td>';
                echo '<td align="center" style="color: red;" width="5%"></td>';
                echo '<td width="10%"></td>';
                echo '<td width="7%">'.$descricao_pai.'</td>';
                echo '<td style="color: red;" align="center" width="12%">'.$data_nascimento_edi.'</td>' ;
                echo '<td align="center" style="color: red;" width="8%">'.$codigo_animal_edi.'</td>';
                echo '<td style="color: red;" align="center" width="5%">'.$sexo.'</td>';
                echo '<td align="right" style="color: red;" width="8%">'.$peso_desmama_edi.'</td>';
                echo '<td style="color: red;" width="8%">'.$descricao_raca.'</td>';
                echo '<td style="color: red;" width="8%">'.$idade_animal.'</td>';
                echo '</tr>';
            }
            else {
                echo '<tr>';
                echo '<td width="7%">Sem Estação</td>';
                echo '<td width="8%"></td>';
                echo '<td width="14%"></td>';
                echo '<td align="center" width="5%"></td>';
                echo '<td width="10%"></td>';
                echo '<td width="7%">'.$descricao_pai.'</td>';
                echo '<td align="center" width="12%">'.$data_nascimento_edi.'</td>';
                echo '<td align="center" width="8%">'.$codigo_animal_edi.'</td>';
                echo '<td align="center" width="5%">'.$sexo.'</td>';
                echo '<td align="right" width="8%">'.$peso_desmama_edi.'</td>';
                echo '<td width="8%">'.$descricao_raca.'</td>';
                echo '<td width="8%">'.$idade_animal.'</td>';
                echo '</tr>';
            }
        }
    }

    // Pega abortos sem estação de monta
    $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_movimentacao_estoque 
        INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_mov_estoque_local
        WHERE tbl_mov_estoque_codigo_mae='$codigo_animal_id' AND 
              tbl_mov_estoque_entrada_saida='A' AND 
              (tbl_mov_estoque_cobertura_numero_id='' OR 
               tbl_mov_estoque_cobertura_numero_id is null)              
        ORDER BY tbl_mov_estoque_numero_id DESC"); 

    $num_rows_estoque = mysqli_num_rows($tbl_estoque);

    if ($num_rows_estoque!=0) {
        while ($reg_estoque = mysqli_fetch_object($tbl_estoque)) {
            $nome_fazenda = $reg_estoque->tbl_pessoa_nome;
            $tipo_movimentacao = $reg_estoque->tbl_mov_estoque_tipo_movimentacao;

            if ($tipo_movimentacao=='A'){
                $codigo_animal_edi = 'Aborto';            
            }
            else {
                $codigo_animal_edi = 'Absorção';
            }

            $descricao_pai = '';
            $idade_animal = '';
            $descricao_raca = '';

            $data = new DateTime($reg_estoque->tbl_mov_estoque_nascimento); 
            $data_nascimento_edi = $data->format('d/m/Y');

            echo '<tr>';
            echo '<td width="7%">Sem Estação</td>';
            echo '<td width="8%"></td>';
            echo '<td width="14%">'.$nome_fazenda.'</td>';
            echo '<td align="center" width="5%"></td>';
            echo '<td width="10%"></td>';
            echo '<td width="7%">'.$descricao_pai.'</td>';
            echo '<td align="center" width="12%">'.$data_nascimento_edi.'</td>';
            echo '<td align="center" width="8%">'.$codigo_animal_edi.'</td>';
            echo '<td width="5%"></td>';
            echo '<td width="8%"></td>';
            echo '<td width="8%">'.$descricao_raca.'</td>';
            echo '<td width="8%">'.$idade_animal.'</td>';
            echo '</tr>';
        }
    }

?>
            </tbody>
            </table>
        </div>

        <div class="col-md-1"></div>
    </div>
<?php endif; ?>





                                    </div> <!-- tab-pane active -->
                                </div> <!-- tab-content -->
                            </div> <!-- panel -->
                        </form>
                    </section>
                </div>
            </div>
        </section>
    </section>

    <script>
        $(document).ready(function(){
           $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>

    <?php 
      $javascript_file_name = 'relatorios_reprodutivos.js';
      require 'rodape.php';
    ?>

