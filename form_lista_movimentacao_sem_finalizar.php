<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    @ session_start(); 

    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = "SELECT * FROM usuario 
        WHERE id_usuario = '$codigo_usuario' AND 
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

    // Primeiro exclui todas as movimentações sem item selecionado
    $sql = "SELECT * from tbl_movimentacao 
        WHERE tbl_movimentacao_lixeira=0 AND 
              tbl_movimentacao_situacao=''
        ORDER BY tbl_movimentacao_id  DESC"; 
    $rs = mysqli_query($conector, $sql); 

    $num_rows_movimentacao = mysqli_num_rows($rs);

    if ($num_rows_movimentacao!=0) {
        while ($reg_movimentacao = mysqli_fetch_object($rs)){
            $codigo_id = $reg_movimentacao->tbl_movimentacao_id;

            $tab_itens = mysqli_query($conector, "select * from tbl_item_movimentacao 
                where tbl_ite_movimentacao_numero_id='$codigo_id' and 
                      tbl_ite_movimentacao_selecionado='S'");
            
            $num_rows = mysqli_num_rows($tab_itens);

            if ($num_rows==0) {
                $sql = ("DELETE FROM tbl_item_movimentacao
                    WHERE tbl_ite_movimentacao_numero_id ='$codigo_id'");
                $resultado = mysqli_query($conector,$sql);

                $sql = ("DELETE FROM tbl_movimentacao
                    WHERE tbl_movimentacao_id ='$codigo_id'");
                $resultado = mysqli_query($conector,$sql);
            }
        }        
    }

    $sql = "SELECT * from tbl_movimentacao 
        WHERE tbl_movimentacao_lixeira=0 AND 
              tbl_movimentacao_situacao=''
        ORDER BY tbl_movimentacao_id  DESC"; 
    $rs = mysqli_query($conector, $sql); 

    $num_rows_movimentacao = mysqli_num_rows($rs);

    if ($num_rows_movimentacao!=0) {
        echo "<input type='hidden' class='tem_movimentacao' value='S'>";

        echo '<table class="table-hover" style="font-size: 11px"';
            echo '<tbody>';
                while ($reg_movimentacao = mysqli_fetch_object($rs)){
                    $codigo_id = $reg_movimentacao->tbl_movimentacao_id;
                    $controle_estoque = $reg_movimentacao->tbl_movimentacao_controle;
                    $tipo_movimentacao = $reg_movimentacao->tbl_movimentacao_tipo;
                    $codigo_local = $reg_movimentacao->tbl_movimentacao_codigo_local_origem;
                    $codigo_destino = $reg_movimentacao->tbl_movimentacao_codigo_local_destino;
                    $filtros = $reg_movimentacao->tbl_movimentacao_filtros; 
                    $tipo_registo = $reg_movimentacao->tbl_movimentacao_tipo;

                    $tab_itens = mysqli_query($conector, "select * from tbl_item_movimentacao 
                        where tbl_ite_movimentacao_numero_id  ='$codigo_id' and 
                              tbl_ite_movimentacao_selecionado='S'");
                    $qtd_animais = mysqli_num_rows($tab_itens);

                    switch ($tipo_movimentacao) {
                        case '003':
                            $desc_tipo = 'Venda';
                            break;
                        case '005':
                            $desc_tipo = 'Transferência';
                            break;
                        default:
                            $desc_tipo = '';
                            break;                    
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

                    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_destino'");
                    $num_rows = mysqli_num_rows($tbl_local);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_local);
                        $desc_destino = $reg->tbl_pessoa_nome;
                    }
                    else {
                        $desc_destino = '';
                    }

                    $data_movimentacao = new DateTime($reg_movimentacao->tbl_movimentacao_data);
                    $data_movimentacao_edi = $data_movimentacao->format('d/m/Y');

                    foreach ($array_locais_usuario as $value) {
                        $value = ltrim($value);
                        $value = rtrim($value);
                        if ($value==$codigo_local) {
                            echo "<tr class='status_nao'>";
                                echo "<td width='10%'>".$data_movimentacao_edi."</td>";
                                echo "<td width='15%'>".$desc_tipo."</td>";
                                echo "<td width='25%'>".$desc_local."</td>";
                                echo "<td width='25%'>".$desc_destino."</td>";
                                echo "<td align='right' width='10%'>".$qtd_animais."</td>";
                                echo "<td  align='center' width='15%'>";    
                                echo "<div class='btn-group'>";

                                echo "<a class='btn' href='#'>
                                    <i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar e confirmar a baixa do estoque' onClick='editar_movimentacao(\"{$codigo_id}\")'></i></a>";

                                echo "<a class='btn' href='#'>
                                    <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='excluir_movimentacao(\"{$codigo_id}\",2)'></i></a>";
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
                    <th> Movimentação</th>
                    <th> Origem</th>
                    <th> Destino</th>
                    <th style="text-align:right;"> Qtd Animais</th>
                    <th style="text-align:center;"><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';
    }
    else {
        echo "<input type='hidden' class='tem_movimentacao' value=''>";
    }

    echo '</section>';
?>

    <script src="js/movimentacao.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html> 

                
                
