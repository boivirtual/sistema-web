<?php
    include "conecta_mysql.inc";

    $wcliente = "";
    if (isset($_POST['cliente'])) {
        $cliente = $_POST['cliente'];

        if(in_array("", $cliente)) {
            $wcliente='';
        }
        else {
            $wcliente = " AND tbl_embriao_codigo_cliente IN(";
            $wcliente.= implode(',', $cliente);
            $wcliente.= ")";
            }
    }
    else {
        $wcliente='';
    }

    @ session_start(); 

    $_SESSION['array_cliente_embrioes']=$cliente;
    $_SESSION['lista_embrioes']='S';
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
    echo '<table class="table table-striped table-advance table-hover" id="tabela_animais" style="font-size: 13px">';
                          
            echo '<tbody>';
          
                $sql = "SELECT * FROM tbl_embriao
                    WHERE tbl_embriao_lixeira=0" . $wcliente .
                    " ORDER BY tbl_embriao_id ASC"; 

                $rs = mysqli_query($conector, $sql); 

                while ($reg_embriao = mysqli_fetch_object($rs)){
                    $codigo_id = $reg_embriao->tbl_embriao_id;
                    $lote = $reg_embriao->tbl_embriao_lote;
                    $doadora = $reg_embriao->tbl_embriao_doadora;
                    $touro = $reg_embriao->tbl_embriao_touro;
                    $laboratorio_aspirador = $reg_embriao->tbl_embriao_laboratorio_aspirador;
                    $raca = $reg_embriao->tbl_embriao_codigo_raca;
                    $cliente = $reg_embriao->tbl_embriao_codigo_cliente;
                    $fazenda = $reg_embriao->tbl_embriao_fazenda;
                    $tipo_1 = $reg_embriao->tbl_embriao_tipo_1;
                    $tipo_2 = $reg_embriao->tbl_embriao_tipo_2;
                    $lixeira = $reg_embriao->tbl_embriao_lixeira;

                    switch ($tipo_1) {
                        case '1':
                            $desc_tipo_1 = 'FIV';
                            break;
                        case '2':
                            $desc_tipo_1 = 'Convencional';
                            break;
                        case '3':
                            $desc_tipo_1 = 'Clone';
                            break;
                        default:
                            $desc_tipo_1 = '';
                            break;
                    }

                    switch ($tipo_2) {
                        case '1':
                            $desc_tipo_2 = 'Fresco';
                            break;
                        case '2':
                            $desc_tipo_2 = 'Descongelado';
                            break;
                        case '3':
                            $desc_tipo_2 = 'Desvitrificado';
                            break;
                        default:
                            $desc_tipo_2 = '';
                            break;
                    }

                    $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$raca'");
                    $num_rows = mysqli_num_rows($tab_raca);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_raca);
                        $descricao_raca = $reg->tab_descricao_raca;
                    }
                    else {
                        $descricao_raca = '';
                    }

                    $tbl_pessoa = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$cliente'");
                    $num_rows = mysqli_num_rows($tbl_pessoa);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_pessoa);
                        $desc_cliente = $reg->tbl_pessoa_nome;
                    }
                    else {
                        $desc_cliente = '';
                    }

                    $incluido_em=new DateTime($reg_embriao->tbl_embriao_incluido_em);
                    $incluido_por=$reg_embriao->tbl_embriao_incluido_por; 
                    $alterado_em=new DateTime($reg_embriao->tbl_embriao_alterado_em);
                    $alterado_por=$reg_embriao->tbl_embriao_alterado_por; 
                    $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
                    $alterado_em_edi = $alterado_em->format('d/m/Y H:i:s');

                    $array_animal = array(
                        $codigo_id,
                        $lote,
                        $doadora,
                        $touro,
                        $laboratorio_aspirador,
                        $raca,
                        $cliente,
                        $fazenda,
                        $tipo_1,
                        $tipo_2,
                        $incluido_em_edi,
                        $incluido_por,
                        $alterado_em_edi,
                        $alterado_por
                    );   
                                    
                    $string_array = implode('|', $array_animal);

                    echo "<tr>";
                    echo "<td width='10%'>".$lote."</td>";
                    echo "<td width='10%'>".$descricao_raca."</td>";
                    echo "<td width='10%'>".$doadora."</td>";
                    echo "<td width='10%'>".$touro."</td>";
                    echo "<td width='15%'>".$desc_cliente."</td>";
                    echo "<td width='10%'>".$fazenda."</td>";
                    echo "<td width='10%'>";    
                    echo "<div class='btn-group'>";
                    echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\")'></i></a>";
                        echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)'></i></a>"; 
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                } 
                mysqli_close($conector);
        
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Lote</th>
                    <th> Raça</th>
                    <th> Doadora</th>
                    <th> Touro</th>
                    <th> Cliente</th>
                    <th> Fazenda</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';
?>

    <script src="js/tabela_embriao.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
    </script>

</body>
</html> 


                
                
