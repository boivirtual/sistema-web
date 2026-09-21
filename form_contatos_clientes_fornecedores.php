<?php
	//include "valida_sessao.inc";
    include "conecta_mysql.inc";
	$codigo_cliente_fornecedor=$_REQUEST['id'];

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Creative - Bootstrap 3 Responsive Admin Template">
  <meta name="author" content="GeeksLabs">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet">
  <link href="css/font-awesome.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet">
  <!-- <link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css" rel="stylesheet"> -->
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>
    <div class="row">
        <div class="col-lg-12">
        	
            <div class="form-group">
              <button type="button" class="btn btn-primary" id="botao_incluir_contato">Incluir Novo</button>
            </div> 

            <section class="panel">
            	<table class="table table-advance table-hover" id="tabela_contatos">
                <thead>
                	<tr>
                </thead>
                <tbody>
                	<?php 
	                    $sql = "select * from contatos_cliente_fornecedor 
	                                    where contato_cliente_id='$codigo_cliente_fornecedor'"; 
	                    $rs = mysqli_query($conector, $sql); 
	                    while ($registro_contato = mysqli_fetch_object($rs)) : 

                        if (strlen($registro_contato->contato_cliente_telefone)==9) {
                            $telefone = '(' . $registro_contato->contato_cliente_ddd . ') ' . 
                            substr($registro_contato->contato_cliente_telefone, 0, 5) . '-' . 
                            substr($registro_contato->contato_cliente_telefone, 5, 4);
                        }
                        else {
                            $telefone = '(' . $registro_contato->contato_cliente_ddd . ') ' . 
                            substr($registro_contato->contato_cliente_telefone, 0, 4) . '-' . 
                            substr($registro_contato->contato_cliente_telefone, 4, 4);
                        }

	                ?>
                    <tr>
                        <?php if ($registro_contato->contato_cliente_registro_lixeira==1) : ?>
                        <td style="color: #ccc"><?= $registro_contato->contato_cliente_nome ?></td>
	                    <td style="color: #ccc"><?= $telefone ?></td>
	                    <td style="color: #ccc"><?= $registro_contato->contato_cliente_email ?></td>
                        <?php else : ?> 
                        <td>
                            <a class="btn editar_contato" href="javascript:void(0);" 
                                data-codigo_contato="<?= $registro_contato->contato_id ?>"
                                data-nome_contato="<?= $registro_contato->contato_cliente_nome ?>"
                                data-cargo_contato="<?= $registro_contato->contato_cliente_cargo ?>"
                                data-ddd_contato="<?= $registro_contato->contato_cliente_ddd ?>"
                                data-telefone_contato="<?= $registro_contato->contato_cliente_telefone ?>"
                                data-email_contato="<?= $registro_contato->contato_cliente_email ?>">
                                <?= $registro_contato->contato_cliente_nome ?>
                            </a>                        
                        </td>
                        <td>
                            <a class="btn editar_contato" href="javascript:void(0);"
                                data-codigo_contato="<?= $registro_contato->contato_id ?>"
                                data-nome_contato="<?= $registro_contato->contato_cliente_nome ?>"
                                data-cargo_contato="<?= $registro_contato->contato_cliente_cargo ?>"
                                data-ddd_contato="<?= $registro_contato->contato_cliente_ddd ?>"
                                data-telefone_contato="<?= $registro_contato->contato_cliente_telefone ?>"
                                data-email_contato="<?= $registro_contato->contato_cliente_email ?>">
                                <?= $telefone ?>
                            </a>                        
                        </td>
                        <td>
                            <a class="btn editar_contato" href="javascript:void(0);"
                                data-codigo_contato="<?= $registro_contato->contato_id ?>"
                                data-nome_contato="<?= $registro_contato->contato_cliente_nome ?>"
                                data-cargo_contato="<?= $registro_contato->contato_cliente_cargo ?>"
                                data-ddd_contato="<?= $registro_contato->contato_cliente_ddd ?>"
                                data-telefone_contato="<?= $registro_contato->contato_cliente_telefone ?>"
                                data-email_contato="<?= $registro_contato->contato_cliente_email ?>">
                                <?= $registro_contato->contato_cliente_email ?>
                            </a>                        
                        </td>
                        <?php endif; ?>

                        <?php if ($registro_contato->contato_cliente_registro_lixeira==1) : ?>
                            <td>
                                <a class="btn restaurar_contato" href="#" 
                                    data-codigo_contato="<?= $registro_contato->contato_id ?>"
                                    data-nome_contato="<?= $registro_contato->contato_cliente_nome ?>"
                                    data-cargo_contato="<?= $registro_contato->contato_cliente_cargo ?>"
                                    data-ddd_contato="<?= $registro_contato->contato_cliente_ddd ?>"
                                    data-telefone_contato="<?= $registro_contato->contato_cliente_telefone ?>"
                                    data-email_contato="<?= $registro_contato->contato_cliente_email ?>">
                                    <i class="icon_refresh" data-toggle='tooltip' data-placement='left' title="Remover esse registro da lixeira" ></i>
                                </a>
                            </td>
                        <?php else : ?> 
    	                    <td>
    	                        <a class="btn excluir_contato" href="#"
                                    data-codigo_contato="<?= $registro_contato->contato_id ?>"
                                    data-nome_contato="<?= $registro_contato->contato_cliente_nome ?>"
                                    data-cargo_contato="<?= $registro_contato->contato_cliente_cargo ?>"
                                    data-ddd_contato="<?= $registro_contato->contato_cliente_ddd ?>"
                                    data-telefone_contato="<?= $registro_contato->contato_cliente_telefone ?>"
                                    data-email_contato="<?= $registro_contato->contato_cliente_email ?>">
    	                            <i class="icon_trash_alt" data-toggle='tooltip' data-placement='left' title="Enviar esse registro para lixeira"></i>
    	                        </a>                        
    	                    </td>
                        <?php endif; ?>
                    </tr>  
                  	<?php
                    endwhile;
                  	?>
                </tbody>
                <tfoot>
                </tfoot>
                </table>    
            </section>  

            <div id="dados_editar" hidden="true">
                <form method="POST" action="gravar_contato_clientes.php" enctype="multipart/form-data" id="form_gravar_contato_cliente">
                    <input name="codigo_cliente" type="hidden" id="codigo_cliente"
                    <?php echo "value='".$codigo_cliente_fornecedor."'";?>>
                    <input name="codigo_contato" type="hidden" id="codigo_contato">
                    <input name="tipo_gravacao_contato" type="hidden" id="tipo_gravacao_contato">
                  
                    <div class="row" id="errors"></div>

                    <hr>
                  
                    <div class="tab-content">
                        <div id="dados_contatos" class="tab-pane active">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="nome_contato" class="control-label"><span class="required">*</span>Nome</label>
                                    <input name="nome_contato" type="text" class="form-control" id="nome_contato" required="" onkeyup="maiuscula(this)">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="cargo_contato" class="control-label">Cargo</label>
                                    <input name="cargo_contato" type="text" class="form-control" id="cargo_contato" onkeyup="maiuscula(this)">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="ddd_contato" class="control-label"><span class="required">*</span>DDD</label>
                                    <input name="ddd_contato" type="text" class="form-control" id="ddd_contato" placeholder="##" required="">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="telefone_contato" class="control-label"><span class="required">*</span>Telefone</label>
                                    <input name="telefone_contato" type="text" class="form-control" id="telefone_contato" placeholder="#########" required="">
                                </div>
                            </div>
                                  
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="email_contato" class="control-label">Email</label>
                                    <input name="email_contato" type="text" class="form-control" id="email_contato" onkeyup="minuscula(this)">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <button type="button" class="btn btn-primary" id="confirma_gravar_contato_cliente">Confirmar Inclusão</button>
                                    <button type="button" class="btn btn-info pull-right"
                                     id="fecha_dados_editar">Voltar</button>
                                </div>
                            </div>
                        </div> <!-- dados_contatos -->
                    </div>   <!-- tab-content-->
                </form>  
            </div> <!--modal-body -->

            <div class="modal fade" id="mensagem_retorno_contato" tabindex="-1" role="dialog" 
            aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Contatos</h4>
                        </div>

                        <div class="modal-body"></div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>


<!-- javascripts -->
<!--<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="js/ga.js" type="text/javascript" ></script>
<script src="js/bootstrap-switch.js"></script>
<script src="js/jquery.tagsinput.js"></script>
<script src="js/jquery.hotkeys.js"></script>
<script src="js/bootstrap-wysiwyg.js"></script>
<script src="js/bootstrap-wysiwyg-custom.js"></script>
<script src="js/scripts.js"></script>
<script src="DataTables-1.10.18/js/jquery.dataTables.min.js"></script>
<script src="DataTables-1.10.18/js/dataTables.bootstrap4.min.js"></script> -->
<script src="js/contato_cliente.js" charset="utf-8" type="text/javascript" ></script>
<script>
    $(document).ready(function(){
       $('[data-toggle="tooltip"]').tooltip();   
    });
</script>

</body>
</html>



