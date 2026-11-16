<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";

    $data_sistema = date("Y-m-d");

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $tbl_local_chuva = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    @ session_start(); 
    $controle_estoque = $_SESSION['controle_estoque'];

    $cnpj_empresa = $_SESSION['id_cliente'];

    $tbl_empresa = mysqli_query($conector, "select * from tbl_empresa 
        where tbl_empresa_cpf_cnpj='$cnpj_empresa'"); 
    $registro_emp = mysqli_fetch_object($tbl_empresa);

    $aceite_termo = new DateTime($registro_emp->tbl_empresa_termo_uso_confirmado_em);
    $aceite_termo_edi = $aceite_termo->format('d/m/Y H:i:s');
    $aceite_por = $registro_emp->tbl_empresa_termo_uso_confirmado_por;

    if ($aceite_por!='') {
        $aceite = 'Aceite dos termos confirmado por ' . $aceite_por . ' em ' . $aceite_termo_edi;
    }
    else {
        $aceite = '';
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
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/style.css?<?php echo Versao;?>" rel="stylesheet">
  <link href="css/style-responsive.css?<?php echo Versao;?>" rel="stylesheet" />
  <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" rel="stylesheet"  integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <link href="assets/materialize/css/materialize.css?<?php echo Versao;?>" rel="stylesheet"  media="screen,projection" />

  <link href="css/select-1.13.14.css" rel="stylesheet" >
  <link rel="stylesheet" href="css/fullcalendarmain.css">
  <script src="js/fullcalendarmain.js"></script>
  <script src="js/locales-all.js"></script>

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

</head>

<body>
  <!-- container section start -->
  <section id="container" class="">

  <?php
    include "cabecalho.php";
    include "opcoes_menu.php";
    include "start_session.php";
  ?>

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;"> 
            <span class="caminho-programa">Home<i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Painel

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            </span></span>


        <!--overview start-->
        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li id="painel"><i class="fa fa-laptop"></i>Painel</li>

                    <!--<li id="fazendas-select">
                        <label>Fazendas 1:</label>
                        <select class="select-empresa-menu-control" id="codigo_local" name="codigo_local" onchange="consultar_fazenda()"> 
                            <option value="0">Todas</option>
                        </select>
                    </li>-->

                    <a href="#" style="float: right; color: gray" data-toggle='tooltip' data-placement='left' title="Termos de Uso do Software e Politica de Privacidade" onclick="termo_uso_software()"><i class="fas fa-shield-alt"></i></a>
                </ol> 

                <!--<div class="col-xs-12" id="divSelect" style="background-color: white; text-align: center;" hidden>
                    <label>Fazendas 2:</label>
                    <select class="select-empresa-menu-control" id="codigo_local" name="codigo_local" onchange="consultar_fazenda()"> 
                            <option value="0">Todas</option>
                    </select>

                </div>-->
            </div>
        </div> 

       <div id="page-inner"> 
            <div class="dashboard-cards"> 
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6 animais">
                        <div class="card horizontal" style="height: 335px;">
                            <div class="card-mapa-gado blue-grey-text text-lighten-1">
                                <strong> MAPA DE GADO</strong>
                            </div>

                            <div class="">
                                <img src="img/gado_pasto.png" class="img-gado" alt="" style="width: 16%;">
                            </div>

                            <div class="card-tabela-mapa" style="max-height: 200px; overflow-y: auto;">

                                <input type="hidden" name="tipo_controle_estoque" id="tipo_controle_estoque"
                                <?php echo "value='".$controle_estoque."'";?>>

                                <table class="table table-advance table-hover" style="font-size: 12px;" id="tabela_mapa">
                                    <thead>
                                        <tr> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr> 
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-6 agenda">
                        <div class="card horizontal">
                            <div class="card-image grey">
                                <img src="img/agenda.png" class="img-agenda" alt="" style="width: 50%;">
                            </div>

                            <div class="card-stacked grey">
                                <div class="card-action" style="text-align: left;">
                                    <strong style="color: #689489;">AGENDA</strong>
                                </div>

                                <ol class="breadcrumb grey">
                                    <li id="fazendas-select" >
                                        <label style="color: #656769; font-size: 14px;">Fazendas:
                                        </label>
                                        <select class="select-empresa-menu-control" id="codigo_local_agenda" name="codigo_local_agenda" onchange="consultar_fazenda()"> 
                                            <option value="0">Todas</option>
                                        </select>
                                    </li>
                                </ol> 

                                <div class="calendar">
                                    <div class="col-lg-12" id="calendar" >
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6 categorias">
                        <div class="card horizontal">
                            <div class="card-image blue-grey darken-1">
                                <img src="img/categoria.png" class="img-categoria" alt="" style="width: 50%">

                                <div class="card-content darken-1">
                                    <h3 id="qtd_animais"></h3> 
                                </div> 

                                <div class="card-content" id="transferencia" hidden="">
                                    <p >
                                    <a href='form_movimentacao_animais_aceite_transferencia.php' style="color: #fff">
                                    Existe transferência para confirmar</a></p> 
                                </div>

                            </div>

                            <div class="card-stacked blue-grey darken-1">
                                <div class="card-action" style="text-align: left;">
                                    <strong>CATEGORIAS</strong>
                                </div>

                                <ol class="breadcrumb blue-grey darken-1">
                                    <li id="fazendas-select" >
                                        <label style="color: #fff; font-size: 14px;">Fazendas:
                                        </label>
                                        <select class="select-empresa-menu-control" id="codigo_local" name="codigo_local" onchange="consultar_fazenda()"> 
                                            <option value="0">Todas</option>
                                        </select>
                                    </li>
                                </ol> 

                                <div class="card-content" style="overflow-x: auto;">
                                    <table class="table table-advance" id="tabela_categorias">
                                        <thead>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> 
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="card horizontal">
                            <div class="card-image-2 teal">
                            <!--    <table class="table table-advance" id="tabela_chuva" style="color: #fff; font-size: 11px;" width="100%" >
                                    <tbody>
                                        <tr>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            -->
                                <p style="color: black">Mês:<span style="color: #ffffff" class="mes_atual"></span></p>

                                <p style="color: black">Dias Chuva:<span style="color: #ffffff" class="dias_chuva"></span></p>

                                <p style="color: black">mm Mês:<span style="color: #ffffff" class="mm_mes"></span></p>

                                <p style="color: black">mm Ano:<span style="color: #ffffff" class="mm_ano"></span></p>

                                <a href="#" onclick="listar_chuvas_dashboad()"><i class="far fa-file-alt"></i>
                                Relatório</a>
                            </div>

                            <div class="card-stacked teal">
                                <div class="card-action">
                                    <strong>CHUVAS</strong>
                                </div>

                                <div class="card-content">
                                	<form method="POST" action="#" enctype="multipart/form-data" id="form_volume_chuva">

                                        <input type="hidden" name="bd" id="bd" value="<?= $cnpj_empresa ?>">
                                        <input type="hidden" name="user" id="user" value="<?= $nome_usuario ?>">

	                                    <div class="row">
	                                        <div class="form-group col-xs-12 col-sm-3 col-md-3">
	                                            <label class="control-label" style="color: #ffffff"><span class="required">*</span> Data:</label>
	                                            <input type="date" class="select-empresa-menu-control-chuva" id="data_chuva" name="data_chuva" <?php echo "value='".$data_sistema."'";?>> 

	                                            <input type="date" id="data_atual" 
	                                             <?php echo "value='".$data_sistema."'";?>> 
	                                        </div>

	                                        <div class="form-group col-xs-12 col-sm-3 col-md-3">
	                                            <label class="control-label" style="color: #ffffff"><span class="required">*</span> Local:</label>
	                                            <select class="select-empresa-menu-control-chuva" id="codigo_local_chuva" name="codigo_local_chuva"> 
	                                            
	                                            </select>
	                                        </div>

	                                        <div class="form-group col-xs-12 col-sm-3 col-md-3">
	                                            <label class="control-label" style="color: #ffffff"><span class="required">*</span> Volume (mm):</label>
	                                            <input type="number" class="select-empresa-menu-control-chuva" id="volume_chuva" name="volume_chuva"> 
	                                        </div>

	                                        <div class="form-group col-xs-12 col-sm-2 col-md-2">
	                                            <label class="control-label">&nbsp;</label>
	                                            <button type="button" class="form-control btn btn-info pull-right" onclick="verificar_gravar_chuva()">Gravar</button>
	                                        </div>
	                                    </div>
                                	</form>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
    	</div> 

        <div class="modal fade" id="termos_uso" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Termo de Uso do Software</h4>
                    </div>
                    <div class="modal-body nao-pode-selecionar"">
                        <div class="row">
                        </div>    

                        <div class="row">
                        </div>    

                        <div class="row">
                            <div class="form-group col-md-1">
                            </div>

                            <div class="form-group col-md-5">
                                <a href="#" style="color: gray;" data-toggle="modal" data-target="#termo"><img src="img/termos-de-uso.png" style="padding-right: 5px;">Termo de Uso</a>

                                <!--<button type="button" class="form-control btn btn-primary pull-right" data-toggle="modal" data-target="#termo">Termo de Uso
                                </button>-->
                            </div>

                            <div class="form-group col-md-6">
                                <a href="#" style="color: gray;" data-toggle="modal" data-target="#politica"><img src="img/politica-de-privacidade.png" style="padding-right: 5px;">Política de Privacidade</a>

                                <!--<button type="button" class="form-control btn btn-primary pull-right" onclick="onclick=politica()">Política de Privacidade
                                </button>-->
                            </div>
                        </div>    
                    </div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="termo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074">Termos e condições gerais de uso do software boivirtual.com.br</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar"" style="color:black;">
                        <p style="text-align: justify;">Os serviços do Software boivirtual.com.br são fornecidos pela pessoa jurídica, Cláudia Carvalho Empreendimentos Patrimoniais com a seguinte nome fantasia: Boi Virtual, inscrito no CNPJ sob o nº 44.593.948/0001-73, titular da propriedade intelectual sobre software. 
                        </p>
                                
                        <h4><strong><em><p style="color:#002060">1. Do Objeto</p></em></strong></h4>
                        <p>O software fornece ferramentas para auxiliar e dinamizar o dia a dia dos seus usuários.</p>

                        <p style="text-align: justify;">Caracteriza-se pela prestação do seguinte serviço: Coleta de dados para auxiliar na gestão de fazendas de gado, trabalhando os pilares Gestão Financeira, Gestão de Estoque e Gestão de Atividades da Fazenda. Busca ajudar o produtor bem como os funcionários da Fazenda a registrar atividades de forma organizada e gerar as informações necessárias para apuração de seus resultados. </p>
       
                        <h4><strong><em><p style="color:#002060">2. Da aceitação</p></em></strong></h4>
                        <p style="text-align: justify;">O presente termo estabelece obrigações contratadas de livre e espontânea vontade, por tempo de 03 meses, entre o software e as pessoas físicas ou jurídicas, usuárias do site.</p>

                        <p style="text-align: justify;">Ao utilizar o software o usuário aceita integralmente as presentes normas e se compromete a observá-las, sob o risco de aplicação das penalidade cabíveis.</p>

                        <p style="text-align: justify;">A aceitação do presente instrumento é imprescindível para o acesso e para a utilização de quaisquer serviços fornecidos pela empresa. Caso não concorde com as disposições deste instrumento, o usuário não deve utilizá-los.</p>

                        <h4><strong><em><p style="color:#002060">3. Do acesso dos usuários</p></em></strong></h4>
                        <p style="text-align: justify;">Serão utilizadas todas as soluções técnicas à disposição do responsável pelo software para permitir o acesso ao serviço 24 (vinte e quatro) horas por dia, 7 (sete) dias por semana. No entanto, a navegação no software ou em alguma de suas páginas poderá ser interrompida, limitada ou suspensa para atualizações, modificações ou qualquer ação necessária ao seu bom funcionamento.</p>

                        <h4><strong><em><p style="color:#002060">4. Do cadastro</p></em></strong></h4>
                        <p style="text-align: justify;">O acesso às funcionalidades do software exigirá a realização de um cadastro prévio, neste momento será gerado um usuário Administrador da fazenda que será o responsável pelo acesso ao Sistema.</p>
                        <p style="text-align: justify;">Ao se cadastrar o usuário responsável deverá informar dados completos, recentes e válidos, sendo de sua exclusiva responsabilidade manter referidos dados atualizados, bem como o usuário se compromete com a veracidade dos dados fornecidos.</p>
                        <p style="text-align: justify;">O usuário responsável se compromete a não informar seus dados cadastrais e/ou de acesso ao software à terceiros, responsabilizando-se integralmente pelo uso que deles seja feito.</p>
                        <p style="text-align: justify;">Menores de 18 anos e aqueles que não possuírem plena capacidade civil deverão obter previamente o consentimento expresso de seus responsáveis legais para utilização do software, sendo de responsabilidade exclusiva dos mesmos o eventual acesso por menores de idade e por aqueles que não possuem plena capacidade civil sem a prévia autorização.</p>
                        <p style="text-align: justify;">Mediante a realização do cadastro o usuário responsável declara e garante expressamente ser plenamente capaz, podendo exercer e usufruir livremente dos serviços.</p>
                        <p style="text-align: justify;">O usuário responsável deverá fornecer um endereço de e-mail e um telefone válidos, através dos quais o software realizará todas comunicações necessárias.</p>
                        <p style="text-align: justify;">Após a confirmação do cadastro, o usuário responsável possuirá um login e uma senha pessoal, a qual assegura a ele o acesso individual à mesma. Desta forma, compete ao usuário responsável exclusivamente a manutenção de referida senha de maneira confidencial e segura, evitando o acesso indevido às informações pessoais.</p>
                        <p style="text-align: justify;">O usuário responsável poderá ainda cadastrar outros usuários dentro deste mesmo acesso, se tornando responsável pelo uso e acesso destes novos membros.</p>
                        <p style="text-align: justify;">Toda e qualquer atividade realizada com o uso da senha será de responsabilidade do usuário, que deverá informar prontamente em caso de uso indevido da respectiva senha.</p>
                        <p style="text-align: justify;">Não será permitido ceder, vender, alugar ou transferir, de qualquer forma, a conta, que é pessoal e intransferível.</p>
                        <p style="text-align: justify;">Caberá ao usuário assegurar que o(s) seu(s) equipamento(s) seja(m) compatível(eis) com as características técnicas que viabilize a utilização do software.</p>
                        <p style="text-align: justify;">O usuário poderá, a qualquer tempo, requerer o cancelamento de seu cadastro junto ao site. O seu descadastramento será realizado o mais rapidamente possível.</p>
                        <p style="text-align: justify;">O usuário, ao aceitar os Termos e Política de Privacidade, autoriza expressamente o software a coletar, usar, armazenar, tratar, ceder ou utilizar as informações derivadas do uso dos serviços do software, incluindo todas as informações preenchidas pelo usuário no momento em que realizar ou atualizar seu cadastro, além de outras expressamente descritas na Política de Privacidade que deverá ser autorizada pelo usuário.</p>

                        <h4><strong><em><p style="color:#002060">5. Dos serviços</p></em></strong></h4>
                        <p style="text-align: justify;">O Software disponibilizará, ao usuário cadastrado, um conjunto específico de funcionalidades e ferramentas para otimizar a gestão geral da fazenda, conforme descrito no ítem 1.</p>
                        <p style="text-align: justify;">O software será acessado através de  navegadores de internet em dispositivos desktop e mobile, após digitado login e senha aprovados conforme item 4.</p>

                        <h4><strong><em><p style="color:#002060">6. Dos preços</p></em></strong></h4>
                        <p style="text-align: justify;">O software  será fornecido gratuitamente por 3 meses, desde de que o usuário utilize as funcionalidade referentes a gestão administrativa e controle de animais.</p>
                        <p style="text-align: justify;">Caso o usuário não utilize o software de forma completa (Gestão administrativa e controle de animais) em 3 meses o seu acesso poderá ser cancelado automaticamente sem aviso prévio.</p>
                        <p style="text-align: justify;">A contratação dos serviços NÃO será renovada automaticamente. Ao final de 60 dias o usuário será comunicado do vencimento com 30 dias de antecedência e  lhe será apresentado os novos termos de uso e preços. Caso ele não concorde com o novo formato de contratação poderá optar por se desligar do software.</p>

                        <h4><strong><em><p style="color:#002060">7. Do cancelamento</p></em></strong></h4>
                        <p style="text-align: justify;">O usuário poderá cancelar a contratação  uso do software a qualquer momento mediante contato através do telefone (31) 99772-1904.</p>
                        <p style="text-align: justify;">O serviço poderá ser cancelado por parte do proprietário do software se houver violação dos Termos de Uso. </p>

                        <h4><strong><em><p style="color:#002060">8. Do suporte</p></em></strong></h4>
                        <p style="text-align: justify;">Em caso de qualquer dúvida, sugestão ou problema com a utilização do software, o usuário poderá entrar em contato com o suporte, através do email falecomboivirtual@gmail.com  OU telefone (31) 99772-1904</p>
                        <p style="text-align: justify;">Estes serviços de atendimento ao usuário estarão disponíveis nos seguintes dias úteis nos seguintes horários:  De Segunda à sexta-feira de 08:00 às 16:00.</p>

                        <h4><strong><em><p style="color:#002060">9. Das responsabilidades</p></em></strong></h4>
                        <p style="text-align: justify;">É de responsabilidade do usuário:</p>
                        <p style="text-align: justify;">a) defeitos ou vícios técnicos originados no próprio sistema do usuário;</p>
                        <p style="text-align: justify;">b) a correta utilização do software;</p>
                        <p style="text-align: justify;">c) pelo cumprimento e respeito ao conjunto de regras disposto nesse Termo de Condições Geral de Uso, na respectiva Política de Privacidade e na legislação nacional e internacional;</p>
                        <p style="text-align: justify;">d) pela proteção aos dados de acesso à sua conta/perfil (login e senha).</p>
                        <p style="text-align: justify;">É de responsabilidade do software:</p>
                        <p style="text-align: justify;">a) Orientar o uso do software;</p>
                        <p style="text-align: justify;">b) os defeitos e vícios encontrados no software, desde que lhe tenha dado causa;</p>
                        <p style="text-align: justify;">c) as informações que foram por ele divulgadas, sendo que os comentários ou informações divulgadas por usuários são de inteira responsabilidade dos próprios usuários;</p>
                        <p style="text-align: justify;">d) os conteúdos ou atividades ilícitas praticadas através do seu software.</p>
                        <p style="text-align: justify;">Não poderão ser incluídos links externos ou páginas que sirvam para fins comerciais ou publicitários ou quaisquer informações ilícitas, violentas, polêmicas, pornográficas, xenofóbicas, discriminatórias ou ofensivas.</p>

                        <h4><strong><em><p style="color:#002060">10. Dos direitos autorais</p></em></strong></h4>
                        <p style="text-align: justify;">O presente Termo de Uso concede aos usuários uma licença não exclusiva, não transferível e não sublicenciável, para acessar e fazer uso do software.</p>
                        <p style="text-align: justify;">A estrutura do site, as marcas, logotipos, nomes comerciais, layouts, gráficos e design de interface, imagens, ilustrações, fotografias, apresentações, vídeos, conteúdos escritos e de som e áudio, programas de computador, banco de dados, arquivos de transmissão e quaisquer outras informações e direitos de propriedade intelectual da razão social Cláudia Carvalho, observados os termos da Lei da Propriedade Industrial (Lei nº 9.279/96), Lei de Direitos Autorais (Lei nº 9.610/98) e Lei do Software (Lei nº 9.609/98), estão devidamente reservados.</p>
                        <p style="text-align: justify;">Este Termos de Uso não cede ou transfere ao usuário qualquer direito, de modo que o acesso não gera qualquer direito de propriedade intelectual ao usuário, exceto pela licença limitada ora concedida.</p>
                        <p style="text-align: justify;">O uso do software pelo usuário é pessoal, individual e intransferível, sendo vedado qualquer uso não autorizado, comercial ou não-comercial. Tais usos consistirão em violação dos direitos de propriedade intelectual Cláudia Carvalho, puníveis nos termos da legislação aplicável.</p>

                        <h4><strong><em><p style="color:#002060">11. Das sanções</p></em></strong></h4>
                        <p style="text-align: justify;">Sem prejuízo das demais medidas legais cabíveis, a Cláudia Carvalho poderá, a qualquer momento, advertir, suspender ou cancelar a conta do usuário:</p>
                        <p style="text-align: justify;">a) que violar qualquer dispositivo do presente Termo;</p>
                        <p style="text-align: justify;">b) que descumprir os seus deveres de usuário;</p>
                        <p style="text-align: justify;">c) que tiver qualquer comportamento fraudulento, doloso ou que ofenda a terceiros.</p>

                        <h4><strong><em><p style="color:#002060">12. Da rescisão</p></em></strong></h4>
                        <p style="text-align: justify;">A não observância das obrigações pactuadas neste Termo de Uso ou da legislação aplicável poderá, sem prévio aviso, ensejar à imediata rescisão unilateral por parte da razão social Cláudia Carvalho e o bloqueio de todos os serviços prestados ao usuário.</p>

                        <h4><strong><em><p style="color:#002060">13. Das alterações</p></em></strong></h4>
                        <p style="text-align: justify;">Os itens descritos no presente instrumento poderão sofrer alterações, unilateralmente e a qualquer tempo, por parte de Cláudia Carvalho, para adequar ou modificar os serviços, bem como para atender novas exigências legais. As alterações serão veiculadas pelo software e o usuário poderá optar por aceitar o novo conteúdo ou por cancelar o uso dos serviços, caso seja assinante de algum serviço.</p>

                        <h4><strong><em><p style="color:#002060">14. Da política de privacidade</p></em></strong></h4>
                        <p style="text-align: justify;">Além do presente Termo, o usuário deverá consentir com as disposições contidas na respectiva Política de Privacidade a ser apresentada a todos os interessados dentro da interface do software.</p>

                        <h4><strong><em><p style="color:#002060">15. Do foro</p></em></strong></h4>
                        <p style="text-align: justify;">Para a solução de controvérsias decorrentes do presente instrumento será aplicado integralmente o Direito brasileiro.</p>
                        <p style="text-align: justify;">Os eventuais litígios deverão ser apresentados no foro da comarca em que se encontra a sede da empresa em Belo Horizonte, Minas Gerais.</p>

                        <hr>

                        <form method="POST" action="#" enctype="multipart/form-data" id="form_aceite_termos">
                            <div class="row">
                                <label for="aceite_termos" style="font-size: 16px; color: #0066bc; padding-right: 10px;"><span class="required">*</span> Confirmar a leitura dos termos:</label>
                            
                                <label class="checkbox-inline" style="font-size: 16px; color:black">
                                    <input type="checkbox" name="aceite_termos" id="aceite_termos" value="S" <?php if ($aceite_por != '') { echo "checked"; } ?>>Li e Concordo

                                    <input type="hidden" name="aceite_por" id="aceite_por"<?php echo "value='".$aceite_por."'";?>>
                                </label>
                                
                                <p class="aceite_por" style="color: green; font-size: 11px"><?php echo $aceite; ?></p>
                            </div>
                        </form>    
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary gravar_termo_uso" onclick="gravar_termo_uso()">Confirmar</button>

                        <button type="button" class="btn btn-default" onclick="sair_sem_gravar_termo_uso()">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div class="modal fade" id="politica" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title" id="myModalLabel" style="color:#0a5074">Política de Privacidade - Versão 00 – 02/2022</h3>
                    </div>

                    <div class="modal-body nao-pode-selecionar"" style="color:black;">
                        <h4><p>Prezado usuário</p></h4>

                        <p style="text-align: justify;">Assim como você, nós do boivirtual.com.br nos preocupamos com a sua privacidade!</p>

                        <p style="text-align: justify;">Afinal, privacidade é um direito previsto na <em><u>Constituição Brasileira</u></em>. Assim, o software boivirtual.com.br respeita a sua privacidade em relação a qualquer informação sua que possamos coletar.</p>

                        <p style="text-align: justify;">É importante lembrar que o nosso site pode conter links para sites externos que não são operados por nós. Esteja ciente de que não temos controle sobre o conteúdo e práticas desses sites e não podemos aceitar responsabilidade por suas respectivas políticas de privacidade.</p>

                        <h4><strong><em><p style="color:#002060">1. Quais tipos de dados que coletamos?</p></em></strong></h4>

                        <p style="text-align: justify;">Nós recebemos, coletamos e arquivamos as informações que você adiciona em nosso software ou nos fornece de qualquer outra forma. Além disso, nós coletamos o endereço IP utilizado para conectar o seu computador à Internet; informações do computador e internet. Nós poderemos utilizar ferramentas para medir e coletar informações de navegação, incluindo o tempo de resposta das páginas, tempo total da visita em determinadas páginas, informações de interação com página e os métodos utilizados para deixar a página.</p>

                        <p style="text-align: justify;">Nós também coletamos informações de identificação pessoal (incluindo nome, e-mail, CPF e meios de comunicação com você); comentários e feedbacks.</p>

                        <h4><strong><em><p style="color:#002060">2. Como coletamos esses dados pessoais?</p></em></strong></h4>

                        <p style="text-align: justify;">Quando você se cadastra para uso do software, como parte do procedimento, nós coletamos as informações pessoais fornecidas como: o seu nome, CPF, telefone e endereço de e-mail. As suas informações pessoais serão utilizadas para as ações específicas, citadas no item 4, apenas.</p>
                        
                        <h4><strong><em><p style="color:#002060">3. Porque coletamos esses dados pessoais?</p></em></strong></h4>

                        <p style="text-align: justify;">Solicitamos informações pessoais apenas quando realmente precisamos delas para lhe prestar um serviço, com o seu conhecimento e consentimento. Em resumo, coletamos esses dados para criar dados e informações sobre a sua fazenda para você, geradas em seus relatórios e dashboard do software. Usamos também para gerar dados estatísticos e outras informações não pessoais agregadas e/ou inferidas, que podem ser usadas por nós ou por nossos parceiros comerciais para prestar e melhorar nossos respectivos serviços, mas sem vínculo pessoal, sempre cumprindo quaisquer leis e regulamentos aplicáveis. </p>

                        <h4><strong><em><p style="color:#002060">4. Onde armazenamos, compartilhamos ou utilizamos os dados?</p></em></strong></h4>

                        <p style="text-align: justify;">As informações coletadas são utilizadas e armazenadas em nuvem pelo período de 05 anos e somente são compartilhadas com pessoas jurídicas parceiras. Não compartilhamos informações de identificação pessoal publicamente ou com terceiros, exceto quando exigido por lei.</p>

                        <p style="text-align: justify;">Nosso escritório é hospedado na plataforma task.com.br, por isso as suas informações também podem ser armazenadas no banco de dados da Task.com.br em servidores seguros por firewall. Vale lembrar que a task.com.br está em conformidade com as regras do PCI DSS (Payment Card Industry Data Security Standards (PCI DSS) e é reconhecida como fornecedor nível 1.</p>

                        <h4><strong><em><p style="color:#002060">5. Como nos comunicamos com nossos clientes? </p></em></strong></h4>

                        <p style="text-align: justify;">Nós poderemos entrar em contato com você para envio das faturas mensais, notificá-lo ou esclarecer alguma informação relacionada ao serviço prestado ou eventual processo judicial patrocinado por nós; para ajudá-lo a resolver alguma questão relacionada ao processo ou serviço prestado; para notificá-lo sobre faturas ou honorários advocatícios, inclusive por meio de empresa terceirizada; para pesquisas ou questionários; para novidades sobre nossa atuação ou para qualquer outro motivo que seja necessário revisar o nosso contrato, de acordo com as leis locais. Para isso, poderemos entrar em contato via e-mail, telefone, mensagens de texto e/ou correio.</p>

                        <h4><strong><em><p style="color:#002060">Política de Cookies</p></em></strong></h4>

                        <h4><strong><em><p style="color:#002060">1. O que são cookies?</p></em></strong></h4>

                        <p style="text-align: justify;">Como é prática comum em quase todos os sites profissionais, este site usa cookies, que são pequenos arquivos baixados no seu computador, para melhorar sua experiência. Esta página descreve quais informações eles coletam, como as usamos e por que às vezes precisamos armazenar esses cookies. Você pode impedir que esses cookies sejam armazenados, alterando as configurações do seu navegador, no entanto, isso pode fazer o downgrade ou 'quebrar' certos elementos da funcionalidade do site.</p>

                        <h4><strong><em><p style="color:#002060">2. Como usamos os cookies?</p></em></strong></h4>

                        <p style="text-align: justify;">Usamos cookies e outras tecnologias semelhantes, como tags de pixel e web beacons.</p>

                        <p style="text-align: justify;">Usamos essas tecnologias por vários motivos, como permitir que mostremos conteúdos mais relevantes para você; melhorar os nossos produtos e serviços; e ajudar a manter os nossos serviços seguros.</p>

                        <p style="text-align: justify;">Por exemplo, Cookies e tecnologias semelhantes nos avisam quando você está conectado ao Sistema. Nós também usamos essas informações para entender como as pessoas usam nossa Plataforma e outros aplicativos e serviços. </p>

                        <h4><strong><em><p style="color:#002060">Atualizações da Política de Privacidade </p></em></strong></h4>

                        <p style="text-align: justify;">Nós temos o direito de modificar essa política de privacidade a qualquer momento, portanto consulte-a regularmente. As alterações serão imediatamente colocadas em práticas após a alteração em nosso site. Caso realizemos mudanças referentes aos materiais dessa política, você será notificado para que esteja ciente das informações que coletamos e como as utilizamos.</p>

                        <h4><strong><em><p style="color:#002060">Mais informações</p></em></strong></h4>

                        <p style="text-align: justify;">Caso você não queira mais que seja possível para nós coletarmos as suas informações pessoais, por favor entre em contato através do telefone (31) 99772-1904 ou nos envie uma mensagem para falecomboivirtual@gmail.com.</p>

                        <p style="text-align: justify;">Se você tiver quaisquer dúvidas sobre esta Política de Privacidade, ou queira acessar, corrigir ou deletar qualquer informação que tenhamos coletado sobre você, fique à vontade para contatar-nos pelo e-mail falecomboivirtual@gmail.com.</p>
                    </div>

                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-default">Fechar</button>
                    </div>
                </div>
            </div>  
        </div>  

        <div>
            <?php
                include "ajuda.php";
            ?>
        </div>

        <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Termo de Uso - Mensagem</h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
             aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Termo de Uso</h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

    </section> <!--main content end-->
  </section>
  <!-- container section start -->

 <div class="text-center">
     <div class="credits">
         <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2023</p></font>
     </div>
 </div>

</section> <!-- container section start end -->

<script src="js/jquery.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.scrollTo.min.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js?<?php echo Versao; ?>"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>
<script src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js" type="text/javascript" ></script>
<script src="js/dashboard.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>
<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>
<script src='js/jquery.redirect.js'></script>

<script src="js/select-1.13.14.js"></script>

<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
</script>

</body>

</html>
