<?php
    include "valida_sessao.inc";

    if(isset($_REQUEST["editar"]) && $_REQUEST["editar"] == true) {
            $status_gravacao = $_REQUEST["status_gravacao"];
            $erro_mysql = $_REQUEST["erro_mysql"];
    }
    else {
        $status_gravacao = '';
        $erro_mysql = '';
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
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <link href="css/daterangepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-datepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css"></script>
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet" >

</head>

<body>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
	<?php
        include "cabecalho.php";
    	include "opcoes_menu.php"; 
        include "limpar_secao_selecao_matrizes.php"; 
        include "limpar_secao_compra_venda.php"; 
        include "limpar_secao_ctp.php"; 
        include "limpar_secao_ctr.php"; 
        include "limpar_secao_pesagem.php"; 
        include "limpar_secao_movimentacao.php"; 
        include "limpar_secao_nutricao.php"; 
        include "limpar_secao_nascimento.php";
	?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">


           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-keyboard-o"></i> Grupos de Acessos</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" 
                            data-toggle="modal" 
                            data-target="#modal_incluir" 
                            value="Incluir Novo"/>
                        </a>

                    </div> 

		       		<section class="panel">
                        <table class="table table-striped table-advance table-hover table-bordered" 
                          id="tabela_grupos_acesso">

                        <thead>
                        	<tr>
			                    <th> Código</th>
			                    <th> Descrição</th>
			                    <th><i class="icon_cogs"></i> Ações</th>
                            </tr>
                        </thead>
                          

		                <tbody>
                            <?php 
                                include "conecta_mysql.inc";
                                $ssql = "select * from grupos_acessos"; 
                                $rs = mysqli_query($conector, $ssql); 
                     
                                while ($registro_tabela = mysqli_fetch_object($rs)){
                                    $codigo = $registro_tabela->codigo_grupo_acesso;
                                    $descricao = $registro_tabela->descricao_grupo_acesso; 

                                    $grupo_array_manejo_animais = $registro_tabela->array_menu_manejo_animais_grupo_acesso;
                                    $grupo_array_manejo_reprodutivo = $registro_tabela->array_menu_manejo_reprodutivo_grupo_acesso;
                                    $grupo_array_suplemento_alimentar = $registro_tabela->array_menu_suplemento_alimentar_grupo_acesso;
                                    $grupo_array_controle_sanitario = $registro_tabela->array_menu_controle_sanitario_grupo_acesso;
                                    $grupo_array_gestao_adm = $registro_tabela->array_menu_gestao_adm_grupo_acesso;
                                    $grupo_array_cadastro = $registro_tabela->array_menu_cadastro_grupo_acesso;
                                    $grupo_array_parametro = $registro_tabela->array_menu_parametro_grupo_acesso;
                                    $grupo_array_relatorios = $registro_tabela->array_menu_relatorios_grupo_acesso;
                                    $lixeira = $registro_tabela->registro_lixeira_grupo_acesso; 

                                    if ($lixeira==1){
                                        echo '<tr>';
                                        echo '<td style="color:#ccc">'.$codigo.'</td>';
                                        echo '<td style="color:#ccc">'.$descricao.'</td>';
                                        echo '<td>
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_excluir" 
                                              data-codigo="'.$codigo.'"
                                              data-descricao="'.$descricao.'"
                                              data-whatevertipo="3">
                                              <i class="icon_refresh" title="Remover esse registro da lixeira" ></i>
                                              </a>
                                              </td>';
                                        echo '</tr>'; 
                                    }
                                    else {
                                        echo '<tr>';
                                        echo '<td>'.$codigo.'</td>';
                                        echo '<td>'.$descricao.'</td>';
                                        echo '<td>
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_editar" 
                                              data-codigo="'.$codigo.'"
                                              data-descricao="'.$descricao.'"
                                              data-manejo_animais="'.$grupo_array_manejo_animais.'"
                                              data-manejo_reprodutivo="'.$grupo_array_manejo_reprodutivo.'"
                                              data-suplemento_alimentar="'.$grupo_array_suplemento_alimentar.'"
                                              data-controle_sanitario="'.$grupo_array_controle_sanitario.'"
                                              data-gestao_adm="'.$grupo_array_gestao_adm.'"
                                              data-cadastro="'.$grupo_array_cadastro.'"
                                              data-parametro="'.$grupo_array_parametro.'"
                                              data-relatorios="'.$grupo_array_relatorios.'"
                                                    >
                                              <i class="icon_pencil" title="Editar esse registro" ></i>
                                              </a>
                                        
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_excluir" 
                                              data-codigo="'.$codigo.'"
                                              data-descricao="'.$descricao.'"
                                              data-whatevertipo="2">
                                              <i class="icon_trash_alt" 
                                               title="Enviar esse registro para lixeira"></i>
                                              </a></td>';
                                        echo '</tr>'; 
                                    }
                                } 
                                
                                mysqli_close($conector);
                            ?>
                        </tbody>
		                </table>
		            </section>
		        </div>
	        </div>

        <!-- page end-->
            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true" data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Grupos de Acesso - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_grupos_acesso.php" enctype="multipart/form-data" id="gravar_grupo">
                                <div class="form-group col-md-12">
                                    <label for="codigo_grupo" class="control-label"></label>
                                    <input name="codigo_grupo" type="hidden" class="form-control" id="codigo_grupo" >
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="descricao_grupo" class="control-label"><span class="required">*</span>Descrição</label>
                                    <input name="descricao_grupo" type="text" class="form-control" id="descricao_grupo" required="">
                                </div>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading"> Animais
                                    </header>

                                    <div class=panel-body id="manejos">
                                       
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc101" name="opc101" value="">  Mapa de Gados
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc102" name="opc102"  value=""> Pesagem
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc103" name="opc103" value=""> Movimentações
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc104" name="opc104" value=""> Nutrição
                                            </label>
                                        </div>
<!--
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc105" name="opc105" value=""> Mortes e outras saídas
                                            </label>
                                        </div> -->
                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                        Reprodução
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc201" name="opc201" value=""> Seleção de Fêmeas
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc202" name="opc202" value=""> Protocolo IATF
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc204" name="opc204" value=""> Diagnóstico
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc203" name="opc203" value=""> Nascimento
                                            </label>
                                        </div>
                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                        Suplementação Alimentar
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc301" name="opc301" value=""> ###
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc302" name="opc302" value=""> ###
                                            </label>
                                        </div>
                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                       Controle Sanitário
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc401" name="opc401" value=""> ####
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc402" name="opc402" value=""> ####
                                            </label>
                                        </div>
                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                       Gestão Administrativa
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc503" name="opc503" value=""> Aceite Contas a Pagar
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc502" name="opc502" value=""> Contas a Pagar
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc504" name="opc504" value=""> Contas a Receber
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc501" name="opc501" value=""> Compra/Vendas Animais
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc506" name="opc506" value=""> Previsão de Contas
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc505" name="opc505" value=""> Agenda de Atividades
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc507" name="opc507" value=""> ###
                                            </label>
                                        </div>

                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                       Cadastros
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc701" name="opc701" value=""> Pessoas
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc702" name="opc702" value=""> Animais
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc703" name="opc703" value=""> Lotes de Animais
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc704" name="opc704" value=""> Semen
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc705" name="opc705" value=""> Produtos
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc706" name="opc706" value=""> Protocolos AITF
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc707" name="opc707" value=""> Embrião
                                            </label>
                                        </div>
                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                       Parâmetros
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc800" name="opc800" value=""> Empresa
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc801" name="opc801" value=""> Usuários
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc802" name="opc802" value=""> Grupos de Acesso
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc820" name="opc820" value=""> Módulo Pasto
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc821" name="opc821" value=""> Tipo de Forragem
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc822" name="opc822" value=""> Atividade Padrão
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc803" name="opc803" value=""> Pastos
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc804" name="opc804" value=""> Classe de Pessoas
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc805" name="opc805" value=""> Conta Pagamento
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc806" name="opc806" value=""> Bancos
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc807" name="opc807" value=""> Plano de Contas
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc808" name="opc808" value=""> Centro de Custos
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc809" name="opc809" value=""> Tipos de Documento
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc810" name="opc810" value=""> Raça de Animais
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc811" name="opc811" value=""> Categorias
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc812" name="opc812" value=""> Pelagem
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc813" name="opc813" value=""> Motivo da Pesagem
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc814" name="opc814" value=""> Procedimentos Sanitário
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc815" name="opc815" value=""> Causa da Morte
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc816" name="opc816" value=""> Grupos de Produtos
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc817" name="opc817" value=""> Unidade de Produtos
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc818" name="opc818" value=""> Via de Uso de Produtos
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc819" name="opc819" value=""> Forma Pagamento
                                            </label>
                                        </div>
                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                        Relatórios
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc901" name="opc901" value=""> Produtivos
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc903" name="opc903" value=""> Financeiros
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc902" name="opc902" value=""> Painel Estratégico
                                            </label>
                                        </div>
                                    </div>
                                </section>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>
                                <input type="hidden" name="status_erro"  size="100" id="status_erro"
                                <?php echo "value='".$erro_mysql."'";?>>

                                <div class="form-group col-md-12">
                                    <button type="submit" class="btn btn-primary gravar" id="botao_gravar">Confirmar Inclusão</button>
                                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar
                                    </button>
                                </div>
                            </form>
                        </div>
              
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" 
             aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="exampleModalLabel">Grupos de Acesso - Editar</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_grupos_acesso.php" enctype="multipart/form-data">
                                <div class="form-group col-md-12">
                                    <label for="codigo_grupo" class="control-label">Código</label>
                                    <input name="codigo_grupo" type="text" class="form-control" id="codigo_grupo" 
                                    readonly="">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="descricao_grupo" class="control-label"><span class="required">*</span>Descrição</label>
                                    <input name="descricao_grupo" type="text" class="form-control" id="descricao_grupo" required="" onkeyup="destrava_alteracao()" >
                                </div>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                        Animais
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc101" name="opc101" onclick=" destrava_alteracao()"> Mapa de Gados
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc102" name="opc102" onclick=" destrava_alteracao()"> Pesagem
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc103" name="opc103" onclick=" destrava_alteracao()"> Movimentações
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc104" name="opc104" onclick=" destrava_alteracao()"> Nutrição
                                            </label>
                                        </div>
<!--
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc105" name="opc105" 
                                            onclick=" destrava_alteracao()"> Mortes e outras saídas
                                            </label>
                                        </div> -->

                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                        Reprodução
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc201" name="opc201" value="" onclick=" destrava_alteracao()"> Seleção de Fêmeas
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc202" name="opc202" value="" onclick=" destrava_alteracao()"> Protocolo IATF
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc204" name="opc204" value="" onclick=" destrava_alteracao()">Diagnóstico
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc203" name="opc203" value="" onclick=" destrava_alteracao()"> Nascimento
                                            </label>
                                        </div>

                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                        Suplementação Alimentar
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc301" name="opc301" value="" onclick=" destrava_alteracao()"> ###
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc302" name="opc302" value="" onclick=" destrava_alteracao()"> ###
                                            </label>
                                        </div>
                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                       Controle Sanitário
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc401" name="opc401" value="" onclick=" destrava_alteracao()"> ####
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc402" name="opc402" value="" onclick=" destrava_alteracao()"> ####
                                            </label>
                                        </div>
                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                       Gestão Administrativa
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc503" name="opc503" value="" onclick=" destrava_alteracao()"> Aceite Contas a Pagar
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc502" name="opc502" value="" onclick=" destrava_alteracao()"> Contas a Pagar
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc504" name="opc504" value="" onclick=" destrava_alteracao()"> Contas a Receber
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc501" name="opc501" value="" onclick=" destrava_alteracao()"> Compra/Vendas Animais
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc506" name="opc506" value="" onclick=" destrava_alteracao()"> Previsão de Contas
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc505" name="opc505" value="" onclick=" destrava_alteracao()"> Agenda de Atividades
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc507" name="opc507" value="" onclick=" destrava_alteracao()"> ###
                                            </label>
                                        </div>

                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                       Cadastros
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc701" name="opc701" value="" onclick=" destrava_alteracao()"> Pessoas
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc702" name="opc702" value="" onclick=" destrava_alteracao()"> Animais
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc703" name="opc703" value="" onclick=" destrava_alteracao()"> Lotes de Animais
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc704" name="opc704" value="" onclick=" destrava_alteracao()"> Semen
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc705" name="opc705" value="" onclick=" destrava_alteracao()"> Produtos
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc706" name="opc706" value="" onclick=" destrava_alteracao()"> Protocolos AITF
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc707" name="opc707" value="" onclick=" destrava_alteracao()"> Embrião
                                            </label>
                                        </div>
                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                       Parâmetros
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc800" name="opc800" value="" onclick=" destrava_alteracao()"> Empresa
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc801" name="opc801" value="" onclick=" destrava_alteracao()"> Usuários
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc802" name="opc802" value="" onclick=" destrava_alteracao()"> Grupos de Acesso
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc820" name="opc820" value="" onclick=" destrava_alteracao()"> Módulo Pasto
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc821" name="opc821" value="" onclick=" destrava_alteracao()"> Tipo de Forragem
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc822" name="opc822" value="" onclick=" destrava_alteracao()"> Atividade Padrão
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc803" name="opc803" value="" onclick=" destrava_alteracao()"> Pastos
                                            </label>
                                        </div>


                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc804" name="opc804" value="" onclick=" destrava_alteracao()"> Classe de Pessoas
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc805" name="opc805" value="" onclick=" destrava_alteracao()"> Conta Pagamento
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc806" name="opc806" value="" onclick=" destrava_alteracao()"> Bancos
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc807" name="opc807" value="" onclick=" destrava_alteracao()"> Plano de Contas
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc808" name="opc808" value="" onclick=" destrava_alteracao()"> Centro de Custos
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc809" name="opc809" value="" onclick=" destrava_alteracao()"> Tipos de Documentos
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc810" name="opc810" value="" onclick=" destrava_alteracao()"> Raça de Animais
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc811" name="opc811" value="" onclick=" destrava_alteracao()"> Categorias
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc812" name="opc812" value="" onclick=" destrava_alteracao()"> Pelagem
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc813" name="opc813" value="" onclick=" destrava_alteracao()"> Motivo da Pesagem
                                            </label>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc814" name="opc814" value="" onclick=" destrava_alteracao()"> Procedimentos Sanitários
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc815" name="opc815" value="" onclick=" destrava_alteracao()"> Causa da Morte
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc816" name="opc816" value="" onclick=" destrava_alteracao()"> Grupos de Produtos
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc817" name="opc817" value="" onclick=" destrava_alteracao()"> Unidade de Produtos
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc818" name="opc818" value="" onclick=" destrava_alteracao()"> Via de Uso de Produtos
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc819" name="opc819" value="" onclick=" destrava_alteracao()"> Forma Pagamento
                                            </label>
                                        </div>
                                    </div>
                                </section>

                                <section class="panel panel-default"> 
                                    <header class="panel-heading">
                                        Relatórios
                                    </header>

                                    <div class=panel-body>
                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc901" name="opc901" value="" onclick=" destrava_alteracao()"> Produtivos
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc903" name="opc903" value="" onclick=" destrava_alteracao()"> Financeiros
                                            </label>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="checkbox-inline">
                                            <input type="checkbox" id="opc902" name="opc902" value="" onclick=" destrava_alteracao()"> Painel Estratégico
                                            </label>
                                        </div>

                                    </div>
                                </section>


                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="1">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>
                                
                                <div class="form-group col-md-12">
                                    <button type="submit" id="confirmar" class="btn btn-primary">Confirmar Alteração</button>
                                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal"
                                      onClick='trava_alteracao()'>Voltar
                                    </button>
                                </div>
                            </form>
                        </div>
              
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_excluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_excluirCenterTitle" aria-hidden="true" data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_excluirLabel">Grupos de Acesso - Excluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="excluir_grupos_acesso.php" enctype="multipart/form-data">
                                <div class="form-group col-md-12">
                                    <label for="codigo_grupo" class="control-label">Código</label>
                                    <input name="codigo_grupo" type="text" class="form-control" id="codigo_grupo" 
                                    readonly="">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="descricao_grupo" class="control-label">Descrição</label>
                                    <input name="descricao_grupo" type="text" class="form-control" id="descricao_grupo" readonly="">
                                </div>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>

                                <div class="form-group col-md-12">
                                    <button type="submit" class="btn btn-danger">Confirmar</button>
                                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_inclusao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Grupos de Acesso</h4>
                        </div>
                        <div class="modal-body">

                        Registro incluido com sucesso

                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button"
                            onclick="abrir_modal_incluir()">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_edicao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Grupos de Acesso</h4>
                        </div>
                        <div class="modal-body">

                        Registro alterado com sucesso

                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_removido" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Grupos de Acesso</h4>
                        </div>
                        <div class="modal-body">

                        Registro removido da lixeira

                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_enviado" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Grupos de Acesso</h4>
                        </div>
                        <div class="modal-body">

                        Registro enviado para lixeira

                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Grupos de Acesso</h4>
                        </div>
                        <div class="modal-body" id="erro_mysql">
                        </div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

<?php 
  $javascript_file_name = 'tabela_grupos_acesso.js';
  require 'rodape.php';
?>


                
                
