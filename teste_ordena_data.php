<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_nasc_inicial = '2023-11-01';
    $data_nasc_final = '2023-12-31';
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
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

</head>

<body> 
    <section id="container" class="">
    <section id="main-content">    
    <div id="lista_nascimentos">
                    
    <table class="table table-striped table-advance table-hover" id="tabela" style="font-size: 11px">

    <tbody>
        <?php    
            $tbl_nascimento = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_entrada_saida='E' AND 
                      tbl_mov_estoque_tipo_movimentacao='N' AND 
                      tbl_mov_estoque_nascimento>='$data_nasc_inicial' AND 
                      tbl_mov_estoque_nascimento<='$data_nasc_final'
                ORDER BY tbl_mov_estoque_nascimento ASC"); 

            $num_rows_estoque = mysqli_num_rows($tbl_nascimento);

            if ($num_rows_estoque!=0) {
                while ($reg_nasc = mysqli_fetch_object($tbl_nascimento)){
                    $nascimento = $reg_nasc->tbl_mov_estoque_nascimento;
                    $data_nascimento = new DateTime($reg_nasc->tbl_mov_estoque_nascimento);
                    $data_nasc_edi = $data_nascimento->format('d/m/Y');
                    $codigo = $reg_nasc->tbl_mov_estoque_codigo_id_animal;

                    echo '<tr>';
                    echo "<td width='8%'>".$codigo."</td>";
                    echo "<td width='8%'>".$data_nascimento->format('d/m/Y')."</td>";
                    echo '</tr>';
                }
            }

        ?>
    </tbody>

    <thead>
        <tr>
            <th>Código</th>
            <th>Data</th>
        </tr>
    </thead>

    </table>
    </div>
    </section>
    </section>


<div class="text-center">
    <div class="credits">
        <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2024</p></font>
    </div>
</div>

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

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
<script src="js/typeahead.js"></script>

<script src="https://cdn.datatables.net/plug-ins/1.10.12/sorting/date-eu.js"></script>

<script>
    $(document).ready(function(){
       $('[data-toggle="tooltip"]').tooltip();   

        $('#tabela').DataTable({
            "responsive": true,
            "paging":   false,
            "ordering": true,
            "info":     true,
            "pageLength": 100,
            //"order": [[ 2, "desc" ], [ 0, 'desc' ],[ 1, "desc" ]],
            "language": {
            "sSearch": "Busca:",
            "zeroRecords": "Nada encontrado",
            "info": "Total Registros: _END_ ",
            "infoEmpty": "Nenhum registro disponível",
            "infoFiltered": "(filtrado de _MAX_ registros no total)",
            },

            "columns": [
                null,
                  { "type": "date-eu" }
                ],
            "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
            //initComplete: function() {
                //$('table.dataTable').css("width", "100%");
             // }
        });

    });




</script>

</body>
</html> 


                
                
