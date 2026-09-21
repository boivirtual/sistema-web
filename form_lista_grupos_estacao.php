<?php
    include "conecta_mysql.inc";
    $data_hoje = date("Y-m-d");
    $local = ltrim($_POST['local']);
    $id_parametro_estacao = $_POST['id_parametro_estacao'];
    $flag = $_POST['flag'];

    // flag - 1 Para o programa form_selecao_matriz_incluir.php
    // flag - 2 Para os programas form_cobertura_animais.php e 
    //                            form_cobertura_animais_diagnostico.php
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
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet">
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

  <style>
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

      #tabela_grupos_wrapper{
          width: 100% !important;
          /*overflow-x: scroll !important;*/
          overflow-y: scroll !important;
          max-height: 200px;
      }

      #tabela_grupos_filter{
          float: right;
      }
  </style>

</head>

<body> 

<?php
    $sql = mysqli_query($conector, "SELECT * FROM tbl_grupo_estacao_monta 
        WHERE tbl_grupo_id = 999 AND 
              tbl_grupo_codigo_estacao_monta = '$id_parametro_estacao' AND 
              tbl_grupo_codigo_local = '$local'");

    $num_row = mysqli_num_rows($sql);

    if ($num_row==0) {
        $sql = "INSERT INTO tbl_grupo_estacao_monta (
            tbl_grupo_id,
            tbl_grupo_codigo_estacao_monta,
            tbl_grupo_descricao,
            tbl_grupo_codigo_local
            ) 
            VALUES (
            999,
            '$id_parametro_estacao',
            'DESCARTE',
            '$local'
            )";

        $resultado = mysqli_query($conector,$sql);
    }

	echo '<section class="panel table-responsive">';
    echo '<table class="table table-striped table-advance table-hover" style="font-size: 12px; width: 100%;" id="tabela_grupos">';
                          
    echo '<tbody>';
          
    $sql = mysqli_query($conector, "select * from tbl_grupo_estacao_monta 
        where tbl_grupo_codigo_estacao_monta ='$id_parametro_estacao' and 
              tbl_grupo_codigo_local='$local'
              order by tbl_grupo_id  ASC"); 
    $num_row = mysqli_num_rows($sql);

    if ($num_row!=0) {
        while ($reg_grupo = mysqli_fetch_object($sql)){
            $codigo_grupo= $reg_grupo->tbl_grupo_id;
            $nome_grupo= $reg_grupo->tbl_grupo_descricao;

            $tbl_cobertura = mysqli_query($conector,"select * from tbl_cobertura 
                    where tbl_cobertura_lixeira=0 and  
                          tbl_cobertura_codigo_grupo='$codigo_grupo' and 
                          tbl_cobertura_codigo_estacao_monta='$id_parametro_estacao' and 
                          tbl_cobertura_codigo_local='$local'"); 

            $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);

            if ($num_rows_cobertura!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_cobertura);
                $qtd_matrizes = $reg_cobertura->tbl_cobertura_qtd_animais;
            }
            else {
                $qtd_matrizes = 0;
            }

            echo "<tr>";
            echo "<td width='11%' class='txtCodigo_grupo'>".$codigo_grupo."</td>";
            echo "<td width='8%'>".$nome_grupo."</td>";
            echo "<td width='10%'>".$qtd_matrizes."</td>";
            echo "<td width='10%'>";    
            echo "<div class='btn-group'>";

            if ($codigo_grupo != 999) {
                if ($flag==1) {
                    echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar nome desse registro' onClick='editar_grupo_estacao(\"{$codigo_grupo}\",\"{$nome_grupo}\")'></i></a>"; 
                }
                else {
                    echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar nome desse registro' onClick='editar_novo_grupo_estacao(\"{$codigo_grupo}\",\"{$nome_grupo}\")'></i></a>"; 
                }
            }

            if ($qtd_matrizes==0 && $codigo_grupo != 999) {
                if ($flag==1) {
                    echo "<a class='btn' href='#'>
                    <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Excluir esse registro' onClick='excluir_grupo_estacao(\"{$codigo_grupo}\",\"{$nome_grupo}\")'>
                    </i></a>";
                }
                else {
                    echo "<a class='btn' href='#'>
                    <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Excluir esse registro' onClick='excluir_novo_grupo_estacao(\"{$codigo_grupo}\",\"{$nome_grupo}\")'>
                    </i></a>";
                }
            }
            echo "</div>";
            echo "</td>";

            echo "</tr>";
        } 
    }

    mysqli_close($conector);

    echo '</tbody>';
    echo '<thead>
        <tr>
            <th> Grupo</th>
            <th> Descrição</th>
            <th> Qtd Fêmeas</th>
            <th><i class="icon_cogs"></i> Ações</th>
        </tr>
        </thead>';
   echo '</table>';

   echo '</section>';
?>

    <script src="js/matrizes.js" charset="utf-8" type="text/javascript" ></script>

    <script>
        $(document).ready(function() {
            var table = $('#tabela').DataTable( {
                sDom: 'lfr<"table_overflow"t>ip',
                /* scrollY: "200px", */
                paging:   false,
                search:   false,
                info: false,
                ordering: false,
                language: {
                  sSearch: "Buscar na lista:",
                  zeroRecords: "Nada encontrado",
                  info: "Animais listados: _END_ ",
                  infoEmpty: "Nenhum registro disponível",
                  infoFiltered: "(filtrado de _MAX_ registros no total)",
                }
            });
        });


    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
    </script>

</body>
</html> 

                
                
