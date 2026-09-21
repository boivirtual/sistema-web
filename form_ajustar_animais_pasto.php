<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";
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
    <link href="css/bootstrap.min.css?<?php echo Versao; ?>" rel="stylesheet">
    <link href="css/bootstrap-theme.css?<?php echo Versao; ?>" rel="stylesheet">
    <link href="css/elegant-icons-style.css" rel="stylesheet" />
    <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
    <link href="css/style-responsive.css?<?php echo Versao; ?>" rel="stylesheet" />
    <!--<link href="assets/materialize/css/materialize.css?<?php echo Versao; ?>" rel="stylesheet" media="screen,projection" />-->
    <link href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="css/select-1.13.14.css" rel="stylesheet" > 

    <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

    <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>
    <form method="POST" action="importar_animais_pasto_excel.php" enctype="multipart/form-data">    
        <div class="row form-group">
            <div class="form-group col-md-6">
                <label for="arquivo_excel"><span class="required">*</span> Informe o arquivo excel</label>
                <input type="file" class="form-control-file" name="arquivo_excel" required>
            </div>
        </div>
        
        <div class="row">  
            <div class="form-group col-xs-12 col-md-12">
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </form>
</body>
<?php 
  require 'rodape.php';
?>

</html>