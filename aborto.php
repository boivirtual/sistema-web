<?php
  include "valida_sessao.inc";

  ob_start();
  header('Content-Type: text/html; charset=utf-8');

  @ session_start(); 
 
  $servidor = "127.0.0.1";
  $usuario_bd = "root";
  $senha_bd = "a2ngei9Mxh";
  $banco = 97174041604;
   
  $conector = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
  
  if (mysqli_connect_error()) {
      printf("Falha na conexão: ", mysqli_connect_error());
      exit();
  }

  $bancoselecionado = mysqli_select_db($conector,$banco);

  if ($bancoselecionado === FALSE) {
      printf("Falha na seleção do banco de dados: ", mysqli_error($conector));
      exit();
  }

  $servidor = "127.0.0.1";
  $usuario_bd = "root";
  $senha_bd = "a2ngei9Mxh";
  $banco = "acesso_boi_virtual";
   
  $conector_acesso = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
  
  if (mysqli_connect_error()) {
      printf("Falha na conexão: ", mysqli_connect_error());
      exit();
  }

  $bancoselecionado = mysqli_select_db($conector_acesso,$banco);

  if ($bancoselecionado === FALSE) {
      exit ("Falha na seleção do banco de dados: " . mysqli_error($conector_acesso));
  }

@ session_start();   


$local = mysqli_query($conector, "select * from tbl_pessoa 
    where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

if(isset($_REQUEST['local'])) {
    $local_id = $_REQUEST['local'];
    $codigo_id = $_REQUEST['codigo_id'];
    $codigo_consulta = $_REQUEST['codigo_consulta'];
}
else {
   $local_id = 56;
   $codigo_id = '';
   $codigo_consulta = '';
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
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 

</head>

<body>

<h1>Entrada rápida Aborto/Absorção</h1>
<hr>

<form method="POST" action="gravar_aborto_rapido.php" enctype="multipart/form-data" id="form_gravar_animal">

<div class="form-group col-md-12">

    <div class="row">

        <div class="form-group col-md-4">
            <label class="control-label">Local:</label>

            <select class="form-control" id="codigo_local" name="codigo_local">

            <?php while($reg_local = mysqli_fetch_object($local)) { ?>

            <option value="<?php 
                echo $reg_local->tbl_pessoa_id ?>"

            <?php 
                if($reg_local->tbl_pessoa_id==$local_id) 
                    { echo "selected"; }
            ?>>

            <?php 
                echo $reg_local->tbl_pessoa_nome;
            ?>
            </option>
            <?php } ?>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-4">
            <label class="control-label">Nº Mãe</label>
            <input class="form-control" name="codigo_mae_consulta" type="text" id="codigo_mae_consulta" autocomplete="off" onchange="ler_animal_mae()" required <?php echo "value='".$codigo_consulta."'"?>>

            <input name="codigo_mae_animal" type="hidden" id="codigo_mae_animal" <?php echo "value='".$codigo_id."'"?>>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-4">
            <label class="radio-inline">
                <input type="radio" name="opcao_nascimento" id="opcao_aborto" value="A" class="opcao_nascimento" checked="checked">Aborto
            </label>

            <label class="radio-inline">
                <input type="radio" name="opcao_nascimento" id="opcao_absorcao" value="B" class="opcao_nascimento">Absorção
            </label>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-4">
            <label class="control-label">Data Aborto:</label>
            <input class="form-control" name="data_aborto" type="date" id="data_aborto" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Confirma</button>
</div>
</form>

<!-- javascripts -->
<script src="js/jquery.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.scrollTo.min.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js?<?php echo Versao; ?>"></script>
<script src="js/ga.js?<?php echo Versao; ?>" type="text/javascript" ></script>
<script src="js/bootstrap-switch.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.tagsinput.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.hotkeys.js?<?php echo Versao; ?>"></script>
<script src="js/bootstrap-wysiwyg.js?<?php echo Versao; ?>"></script>
<script src="js/bootstrap-wysiwyg-custom.js?<?php echo Versao; ?>"></script>
<script src="js/moment.js?<?php echo Versao; ?>"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js"></script>

<script src="js/nascimento.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
<script src="js/typeahead.js"></script>

<script>
    window.addEventListener("load", function(){
        var codigo_consulta = $("#codigo_mae_consulta").val();

        if (codigo_consulta=='') {
            document.getElementById("codigo_mae_consulta").focus();
        }
        else {
            document.getElementById("data_aborto").focus();
        }
    });

    $('#codigo_local').change(function(){
        $("#codigo_mae_consulta").val('');
        $("#codigo_mae_animal").val('');
        $("#data_aborto").val('');
    });

    $(document).ready(function(){
        $('#codigo_mae_consulta').typeahead({
            source: function(query, result) {
                $.ajax({
                    url:"fetch_femeas_servidas.php",
                    method:"POST",
                    data:{query:query,
                          local: $('#codigo_local').val()},
                    dataType:"json",
                    success:function(data)
                    {
                        result($.map(data, function(item){
                        return item;
                        }));
                    }
                })
            }
        });

        $("#codigo_mae_consulta").click(function(){
            $("#codigo_mae_consulta").val('');
            $("#codigo_mae_animal").val('');
            return;
        });

    });

    function ler_animal_mae(){
        var id_animal= $('#codigo_mae_consulta').val();
        var local = $('#codigo_local').val();
        var data_nascimento = $('#data_aborto').val();

        if (id_animal.length < 5) {
            return;
        } 

        $.post("ler_animal_femea_nascimento.php", {id_animal:id_animal, local:local, data_nascimento:data_nascimento}, function(valor){
            var php = valor.split("<|>");

            if (php[0]=='Nao tem animal') {
                $("#codigo_mae_consulta").val('Não encontrado');
                return;
            }
            else {
                $("#codigo_mae_consulta").val(id_animal + ' - ' +  php[2]);
                $("#codigo_mae_animal").val(php[0]);
                //$("#codigo_pai_animal").val(php[3]);
                //$("#cobertura_id").val(php[4]);
                //$("#item_cobertura").val(php[5]);
                //$("#data_inseminacao").val(php[6]);
                //$("#dias_nascimento").val(php[7]);
                //$("#estacao_monta_id").val(php[8]);
            }

            /*var opcao_nascimento = $("input[name='opcao_nascimento']:checked").val();

            if (opcao_nascimento=='N') {
                if (php[4]==0) {

                    if (window.confirm("Atenção! Essa Fêmea não está em estação de monta. Confirmar assim mesmo?")) {
                        return;
                    }
                    else {
                        $("#codigo_mae_consulta").val('');
                        $("#codigo_mae_animal").val('');
                        $("#codigo_pai_animal").val('000000000');
                        return;
                    }
                }

                if (php[10]==1) {
                    $('#nascimento_aborto_natimorto').modal('show');
                    return;
                }

                if (php[9]!=0) {
                    $('#nascimento_gemelar').modal('show');
                    return;
                }
            }
            else {
                if (php[10]==1) {
                    $("#nascimento_aborto_natimorto .modal-body").html('Essa fêmea teve aborto ou natimorto nessa estação');
                    $('#nascimento_aborto_natimorto').modal('show');
                    return;
                }

                if (php[9]!=0) {
                    $("#nascimento_aborto_natimorto .modal-body").html('Essa fêmea teve nascimento nessa estação');
                    $('#nascimento_aborto_natimorto').modal('show');
                    return;
                }

            }*/
        });
    }

</script>

</body>
</html>

