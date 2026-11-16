<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";
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
  <?php    

    echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_ajuda" style="font-size: 12px">';
                          
            echo '<tbody>';
          
                $sql = "SELECT * from tbl_ajuda
                    INNER JOIN tbl_ajuda_url
                            ON tbl_id_url  = codigo_url_ajuda
                      ORDER BY palavra_chave_ajuda ASC"; 
                $rs = mysqli_query($conector, $sql); 

                $palavra_chave_anterior = '';

                while ($reg_ajuda = mysqli_fetch_object($rs)){
                    $codigo = $reg_ajuda->id_ajuda;
                    $codigo_url = $reg_ajuda->codigo_url_ajuda;
                    $palavra_chave_ajuda = $reg_ajuda->palavra_chave_ajuda;
                    $descricao_programa = $reg_ajuda->tbl_nome_programa_url;
                    $programas = $reg_ajuda->array_programas_ajuda;

                    $array_ajuda = array(
                        $codigo,
                        $programas,
                        $palavra_chave_ajuda
                    );   
                                    
                    $string_array = implode('|', $array_ajuda);

                    if ($palavra_chave_anterior!=$palavra_chave_ajuda) {
                        $palavra_chave_anterior=$palavra_chave_ajuda;
                        echo "<tr>";

                            echo "<td width='10%'>".$palavra_chave_ajuda."</td>";
                            //echo "<td width='10%'>".$descricao_programa."</td>";
                            echo "<td width='10%'>";    
                            echo "<div class='btn-group'>";
                            echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_ajuda(\"{$string_array}\");";
                            echo "' ></i></a>"; 

                            echo "<a class='btn' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                            echo "</div>";
                            echo "</td>";
                        echo "</tr>";
                    }
                } 
                mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Palavras-chave</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';
?>

    <script src="js/tabela_busca.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
    </script>

</body>
</html> 


                
                
