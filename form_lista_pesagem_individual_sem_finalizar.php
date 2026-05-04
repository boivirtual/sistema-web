<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    @ session_start(); 

    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = "SELECT * FROM usuario WHERE id_usuario = '$codigo_usuario' AND 
                                           lixeira_usuario=0 ";  
    $query = mysqli_query($conector_acesso, $tbl_usuario);

    $num_rows_usuario = mysqli_num_rows($query);

    if ($num_rows_usuario!=0){
        $reg_usuario = mysqli_fetch_assoc($query);

        $array_locais_usuario = explode(',', $reg_usuario['local_usuario']);
        $qtd_locais_usuario = count($array_locais_usuario);

        if ($qtd_locais_usuario==0) {
            $array_locais_usuario='';
        }
    }
    else {
        $array_locais_usuario='';
    }
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
	echo '<section class="panel lista_contas">';

    $sql = "SELECT * from tbl_pesagem 
        WHERE tbl_pesagem_lixeira=0 AND 
              tbl_pesagem_finalizada='N'
        ORDER BY tbl_pesagem_data DESC, tbl_pesagem_id DESC"; 
    $rs = mysqli_query($conector, $sql); 

    $num_rows_pesagem = mysqli_num_rows($rs);

    if ($num_rows_pesagem!=0) {
        echo "<input type='hidden' class='tem_pesagem' value='S'>";

        echo '<table class="table-hover" style="font-size: 11px"';
            echo '<tbody>';
                while ($reg_pesagem = mysqli_fetch_object($rs)){
                    $codigo = $reg_pesagem->tbl_pesagem_id;
                    $controle_estoque = $reg_pesagem->tbl_pesagem_controle;
                    $codigo_epoca = $reg_pesagem->tbl_pesagem_codigo_epoca;
                    $codigo_local = $reg_pesagem->tbl_pesagem_codigo_local;
                    $lote = $reg_pesagem->tbl_pesagem_lote;
                    $qtd_animais = $reg_pesagem->tbl_pesagem_qtd_animais_pesados;
                    $peso_kg = $reg_pesagem->tbl_pesagem_peso_kg;
                    $peso_arroba= $reg_pesagem->tbl_pesagem_peso_arroba;
                    $filtros = $reg_pesagem->tbl_pesagem_filtros; 
                    $tipo_registo = $reg_pesagem->tbl_pesagem_tipo_registro;
                    $desc_situacao = 'Não Finalizada';

                    $tab_itens = mysqli_query($conector, "select * from tbl_item_pesagem where tbl_ite_pesagem_numero_id ='$codigo'");
                    $qtd_animais = mysqli_num_rows($tab_itens);

                    $tab_epoca = mysqli_query($conector, "select * from tabela_epoca_pesagem where tab_codigo_epoca_pesagem='$codigo_epoca'");
                    $num_rows = mysqli_num_rows($tab_epoca);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_epoca);
                        $descricao_epoca = $reg->tab_descricao_epoca_pesagem;
                    }
                    else {
                        $descricao_epoca = '';
                    }

                    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_local'");
                    $num_rows = mysqli_num_rows($tbl_local);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_local);
                        $desc_local = $reg->tbl_pessoa_nome;
                    }
                    else {
                        $desc_local = '';
                    }

                    $data_pesagem = new DateTime($reg_pesagem->tbl_pesagem_data);
                    $data_pesagem_edi = $data_pesagem->format('d/m/Y');

                    foreach ($array_locais_usuario as $value) {
                        $value = ltrim($value);
                        $value = rtrim($value);
                        if ($value==$codigo_local) {
                            echo "<tr>";
                                echo "<td class='status_nao' width='10%'>".$data_pesagem_edi."</td>";
                                echo "<td class='status_nao' width='25%'>".$desc_local."</td>";
                                echo "<td class='status_nao' width='10%'>".$descricao_epoca."</td>";
                                echo "<td class='status_nao' align='right' width='10%'>".$qtd_animais."</td>";
                                echo "<td class='status_nao' align='right' width='10%'>".number_format($peso_kg,2,',','.')."</td>";
                                echo "<td class='status_nao' align='right' width='10%'>".number_format($peso_arroba,2,',','.')."</td>";
                                echo "<td width='15%'>";    
                                echo "<div class='btn-group'>";

                                if ($tipo_registo=='OFFLINE') {
                                    if ($controle_estoque=='I') {
                                        echo "<a class='btn tooltips' href='form_pesagem_animais_editar_offline.php?id=".$codigo."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar e digitar o peso manualmente' o></i></a>"; 
                                    }
                                    else {
                                        echo "<a class='btn tooltips' href='form_pesagem_animais_editar_lote_offline.php?id=".$codigo."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' o></i></a>"; 
                                    }

                                    echo "<a class='btn' href='#'>
                                        <i class='fa fa-file-excel-o' data-toggle='tooltip' data-placement='left' title='Importar excel da pesagem individual' onClick='importar_excel_pesagem(\"{$codigo}\",\"{$desc_local}\",\"{$descricao_epoca}\",\"{$qtd_animais}\",\"{$codigo_local}\",\"{$codigo_epoca}\")'>
                                        </i></a>";
                                }
                                else {
                                    echo "<a class='btn tooltips' href='form_pesagem_animais_editar_online.php?id=".$codigo."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar e digitar o peso manualmente' o></i></a>"; 
                                }

                                echo "<a class='btn' href='#'>
                                    <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='enviar_pesagem_lixeira(\"{$codigo}\",\"{$desc_local}\",\"{$descricao_epoca}\",1)'>
                                    </i></a>";
                                echo "</div>";
                                echo "</td>";
                            echo "</tr>";
                        }
                    }
                }
                mysqli_close($conector);
                
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Data</th>
                    <th> Local</th>
                    <th> Motivo</th>
                    <th style="text-align:right;"> Qtd Animais</th>
                    <th style="text-align:right;"> Kg</th>
                    <th style="text-align:right;"> Arroba</th>
                    <th style="text-align:center;"><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';
    }
    else {
        echo "<input type='hidden' class='tem_pesagem' value=''>";
    }

    echo '</section>';
?>

    <script src="js/pesagem.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html> 

                
                
