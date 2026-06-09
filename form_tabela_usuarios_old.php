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
  <link href="css/jquery-ui.css" rel="stylesheet" />
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <link href="css/daterangepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-datepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet" >
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <link rel="stylesheet" href="css/select-1.13.14.css"> 

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_parametros'])) {
        $array_parametros = explode("!",$_SESSION['menu_parametros']);

        if ($array_parametros[1] == 0){
            echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
            echo '<strong class="negrito">Atenção! </strong><span>Você não tem acesso a esse programa!</span>';  
            echo '</div>';         
            exit;
        }
    }
    else {
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Você não efetuol o login!</span>';  
        echo '</div>';         
        exit;
    }

    $cnpj_cliente = $_SESSION['id_cliente'];
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-users"></i> Usuários</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Novo" onclick="incluir_novo()"/>
                        </a>
                    </div> 

		       		<section class="panel">
                        <table class="table table-striped table-advance table-hover table-bordered" 
                          id="tabela_usuarios">

                        <thead>
                        	<tr>
			                    <th> Código</th>
			                    <th> Nome</th>
                                <th> Grupo</th>
                                <th> Situação</th>
			                    <th><i class="icon_cogs"></i> Ações</th>
                            </tr>
                        </thead>
                          

		                <tbody>
                            <?php 
                                include "conecta_mysql.inc";

                                $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

                                $tbl_local_editar = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

                                $ssql = "select * from usuario where cnpj_cpf_empresa_usuario='$cnpj_cliente'"; 
                                $rs = mysqli_query($conector_acesso, $ssql); 
                     
                                while ($registro_tabela = mysqli_fetch_object($rs)){
                                    $codigo = $registro_tabela->id_usuario;
                                    $nome = $registro_tabela->nome_usuario; 
                                    $codigo_grupo = $registro_tabela->grupo_usuario; 

                                    $grupo_acesso = mysqli_query($conector, "select * from grupos_acessos
                                                             where codigo_grupo_acesso = '$codigo_grupo'"); 
                                    $reg_grupo = mysqli_fetch_object($grupo_acesso); 

                                    $descricao_grupo = $reg_grupo->descricao_grupo_acesso; 
                                    $email = $registro_tabela->email_usuario; 
                                    $cpf = $registro_tabela->cpf_usuario; 
                                    $cpf_editado = substr($cpf,0,3) . "." . substr($cpf,3,3) . "." . 
                                                   substr($cpf,6,3) . "-" . substr($cpf,9,2);

                                    $pass_usuario = $registro_tabela->senha_usuario; 
                                    $situacao = $registro_tabela->situacao_usuario; 
                                    $local = explode(', ', $registro_tabela->local_usuario);
                                    $quantidade_local = count($local);
                                    $locais = $registro_tabela->local_usuario;
                                    $lixeira = $registro_tabela->lixeira_usuario; 

                                    if ($situacao=="A"){
                                        $desc_situacao = 'Ativo';
                                    }
                                    else {
                                        $desc_situacao = 'Desligado';
                                    }

                                    if ($lixeira==1){
                                        echo '<tr>';
                                        echo '<td style="color:#ccc">'.$codigo.'</td>';
                                        echo '<td style="color:#ccc">'.$nome.'</td>';
                                        echo '<td style="color:#ccc">'.$descricao_grupo.'</td>';
                                        echo '<td style="color:#ccc">'.$desc_situacao.'</td>';
                                        echo '<td>
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_excluir" 
                                              data-whatever="'.$codigo.'"
                                              data-whatevernome="'.$nome.'"
                                              data-whatevercpf="'.$cpf_editado.'"
                                              data-whatevertipo="3">
                                              <i class="icon_refresh" data-toggle="tooltip" 
                                               data-placement="left" title="Remover esse registro da lixeira" ></i>
                                              </a>
                                              </td>';
                                        echo '</tr>'; 
                                    }
                                    else {
                                        echo '<tr>';
                                        echo '<td>'.$codigo.'</td>';
                                        echo '<td>'.$nome.'</td>';
                                        echo '<td>'.$descricao_grupo.'</td>';
                                        echo '<td>'.$desc_situacao.'</td>';
                                        echo '<td>
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_editar" 
                                              data-whatever="'.$codigo.'"
                                              data-whatevernome="'.$nome.'"
                                              data-whatevergrupo="'.$codigo_grupo.'"
                                              data-whateveremail="'.$email.'"
                                              data-whatevercpf="'.$cpf_editado.'"
                                              data-whateversituacao="'.$situacao.'"
                                              data-whateverlocal="'.$locais.'"
                                              >
                                              <i class="icon_pencil" data-toggle="tooltip" 
                                              data-placement="left" title="Editar esse registro" ></i>
                                              </a>
                                        
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_excluir" 
                                              data-whatever="'.$codigo.'"
                                              data-whatevernome="'.$nome.'"
                                              data-whatevercpf="'.$cpf_editado.'"
                                              data-whatevertipo="2">
                                              <i class="icon_trash_alt" data-toggle="tooltip" data-placement="left"
                                               title="Enviar esse registro para lixeira"></i>
                                              </a></td>';
                                        echo '</tr>'; 
                                    }
                                } 
                                
                                //mysqli_close($conector);
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
                            <h4 class="modal-title" id="modal_incluirLabel">Usuários - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_usuario.php" id="form_gravar" 
                            enctype="multipart/form-data">
                                <div class="form-group col-md-12">
                                    <label for="codigo_usuario" class="control-label"></label>
                                    <input name="codigo_usuario" type="hidden" class="form-control" id="codigo_usuario" required="" >
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="nome_usuario" class="control-label"><span class="required">*</span>Nome</label>
                                        <input name="nome_usuario" type="text" class="form-control" id="nome_usuario" required
                                        onkeyup="maiuscula(this)">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="cpf_usuario"><span class="required">*</span>CPF</label>
                                        <input type="text" class="form-control" id="cpf_usuario" name="cpf_usuario" required onBlur="validar_cpf_usuario(this);">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="email_usuario"><span class="required">*</span>E-mail</label>
                                        <input type="email" class="form-control" id="email_usuario" name="email_usuario" required
                                        onkeyup="minuscula(this)">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="grupo"><span class="required">*</span>Grupo de Acesso
                                        </label>
                                        <select class="form-control" id="grupo" name="grupo" required
                                        onclick="destrava_alteracao()" >
                                            <?php
                                                $sql = "select * FROM grupos_acessos where registro_lixeira_grupo_acesso = 0 
                                                        order by descricao_grupo_acesso ASC";  
                                                $qr = mysqli_query($conector, $sql);
                                            
                                                if(mysqli_num_rows($qr) == 0){
                                                    echo  '<option value="">'.htmlentities('Sem grupos cadastrados').'</option>';
                                                }
                                                else{
                                                    echo '<option value="">...</option>';
                                                    
                                                    while($ln = mysqli_fetch_assoc($qr)){
                                                        $codigo_grupo=$ln['codigo_grupo_acesso'];
                                                        $descricao_grupo=$ln['descricao_grupo_acesso'];
                                                        echo '<option value="'.$codigo_grupo.'">'.$descricao_grupo.'</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="local" class="control-label">Locais</label>
                                        <select class="form-control selectpicker" multiple id="local" name="local">
                                                          
                                            <?php while($reg_local = mysqli_fetch_object($tbl_local)) { ?>

                                            <option value="<?php 
                                                echo $reg_local->tbl_pessoa_id ?>">
                                                            
                                                <?php 
                                                    echo $reg_local->tbl_pessoa_nome
                                                ?>
                                            </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>
                                <input type="hidden" name="status_erro"  size="100" id="status_erro"
                                <?php echo "value='".$erro_mysql."'";?>>

                                <div class="form-group col-md-12">
                                    <button type="button" class="btn btn-primary" onclick="gravar()">Confirmar Inclusão</button>
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
                            <h4 class="modal-title" id="exampleModalLabel">Usuários - Editar</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_usuario.php" id="form_gravar" enctype="multipart/form-data">
                                <div class="form-group col-md-12">
                                    <input name="codigo_usuario" type="hidden" class="form-control" id="codigo_usuario" 
                                    readonly="">
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="nome_usuario" class="control-label"><span class="required">*</span>Nome</label>
                                        <input name="nome_usuario" type="text" class="form-control" id="nome_usuario" required="" onkeyup="destrava_alteracao()" >
                                    </div>
                                </div>

                                <div class="row">    
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="situacao_usuario" class="control-label">
                                            <span class="required">*</span>Situação do Usuário
                                        </label>
                                    </div>

                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="situacao"></label>

                                        <label class="radio-inline">
                                            <input type="radio" name="situacao" id="ativo" value="A" 
                                             required onclick="destrava_alteracao()"> Ativo
                                        </label>

                                        <label class="radio-inline">
                                            <input type="radio" name="situacao" id="desligado" value="D"
                                             required onclick="destrava_alteracao()"> Desligado
                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="cpf_usuario"><span class="required">*</span>CPF</label>
                                        <input type="text" class="form-control" id="cpf_usuario" name="cpf_usuario" onkeyup="destrava_alteracao()" onBlur="validar_cpf_usuario(this);">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="email_usuario"><span class="required">*</span>E-mail</label>
                                        <input type="email" class="form-control" id="email_usuario" name="email_usuario" required onkeyup="destrava_alteracao()">
                                    </div> 
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="grupo"><span class="required">*</span>Grupo de Acesso
                                        </label>
                                        <select class="form-control" id="grupo" name="grupo" required
                                        onclick="destrava_alteracao()" >
                                            <?php
                                                $sql = "select * FROM grupos_acessos where registro_lixeira_grupo_acesso = 0 
                                                        order by descricao_grupo_acesso ASC";  
                                                $qr = mysqli_query($conector, $sql);
                                            
                                                if(mysqli_num_rows($qr) == 0){
                                                    echo  '<option value="">'.htmlentities('Sem grupos cadastrados').'</option>';
                                                }
                                                else{
                                                    echo '<option value="">...</option>';
                                                    
                                                    while($ln = mysqli_fetch_assoc($qr)){
                                                        $codigo_grupo=$ln['codigo_grupo_acesso'];
                                                        $descricao_grupo=$ln['descricao_grupo_acesso'];
                                                        echo '<option value="'.$codigo_grupo.'">'.$descricao_grupo.'</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="local" class="control-label">Locais</label>
                                        <select class="form-control selectpicker" multiple id="local" name="local[]" onclick="destrava_alteracao()">
                                                          
                                            <?php while($reg_local = mysqli_fetch_object($tbl_local_editar)) { ?>

                                            <option value="<?php 
                                                echo $reg_local->tbl_pessoa_id ?>"

                                                    <?php 
                                                        if ($local!="") {
                                                            for($i=0; $i < $quantidade_local; $i++) {
                                                                if ($local[$i]==$reg_local->tbl_pessoa_id) 
                                                                { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>
                                                            
                                                <?php 
                                                    echo $reg_local->tbl_pessoa_nome
                                                ?>
                                            </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="1">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>
                                
                                <div class="row">                                
                                    <div class="form-group col-md-12">
                                        <button type="button" id="confirmar" class="btn btn-primary"
                                        onclick="gravar()">Confirmar Alteração</button>
                                        <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar
                                        </button>
                                    </div>
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
                            <h4 class="modal-title" id="modal_excluirLabel">Usuários - Excluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="excluir_usuario.php" enctype="multipart/form-data">
                                <div class="form-group col-md-12">
                                    <input name="codigo_usuario" type="hidden" class="form-control" id="codigo_usuario" 
                                    readonly="">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="cpf_usuario" class="control-label">CPF</label>
                                    <input name="cpf_usuario" type="text" class="form-control" id="cpf_usuario" 
                                    readonly="">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="nome_usuario" class="control-label">Nome</label>
                                    <input name="nome_usuario" type="text" class="form-control" id="nome_usuario" readonly="">
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
                            <h4 class="modal-title">Usuários</h4>
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
                            <h4 class="modal-title">Usuários</h4>
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
                            <h4 class="modal-title">Usuários</h4>
                        </div>
                        <div class="modal-body">

                        Registro restaurado da lixeira

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
                            <h4 class="modal-title">Usuários</h4>
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
            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Usuário </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Animais - Erro</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>

<?php 

  $javascript_file_name = 'tabela_usuarios.js';
  require 'rodape.php';
?>

