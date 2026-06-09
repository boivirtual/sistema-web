<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_inicial = $_REQUEST["data_inicial"];
    $data_final = $_REQUEST["data_final"];
    $array_tipo = $_REQUEST["tipo"];
    $array_origem = $_REQUEST["local_origem"];
    $array_destino = $_REQUEST["local_destino"];

    $local_origem= array();
    $matriz_itens = explode(",", $array_origem);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $local_origem[$i]=$matriz_itens[$i];
    }

    $local_origem = implode(',', $local_origem);
    $local_origem = substr($local_origem,0, -1);

    $wlocal_origem = '';

    if ($array_origem!='') {
        $wlocal_origem = " AND tbl_venda_codigo_local_origem IN(";
        $wlocal_origem.= $local_origem;
        $wlocal_origem.= ")";
    }

    $local_destino= array();
    $matriz_itens = explode(",", $array_destino);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $local_destino[$i]=$matriz_itens[$i];
    }

    $local_destino = implode(',', $local_destino);
    $local_destino = substr($local_destino,0, -1);

    $wlocal_destino = "";

    if ($array_destino!='') {
        $wlocal_destino = " AND tbl_venda_codigo_local_destino IN(";
        $wlocal_destino.= $local_destino;
        $wlocal_destino.= ")";
    }

    $tipo= array();
    $matriz_itens = explode(",", $array_tipo);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $tipo[$i]=$matriz_itens[$i];
    }

    $tipo = implode(',', $tipo);
    $tipo = substr($tipo,0, -1);

    $wtipo = "";

    if ($array_tipo!='') {
        $wtipo = " AND tbl_venda_categoria IN(";
        $wtipo.= $tipo;
        $wtipo.= ")";
    }

    if ($data_inicial==0 && $data_final==0){
        $wperiodo = '';
    }
    else {
        $wperiodo = " AND tbl_venda_emissao >= '$data_inicial' AND tbl_venda_emissao <= '$data_final'";
    }

    @ session_start(); 

    $_SESSION['local_origem_compra_venda']=$array_origem;
    $_SESSION['local_destino_compra_venda']=$array_destino;
    $_SESSION['tipo_compra_venda']=$array_tipo;
    $_SESSION['data_inicial_compra_venda']=$data_inicial; 
    $_SESSION['data_final_compra_venda']=$data_final; 
    $_SESSION['lista_compra_venda']='S'; 
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
    echo '<table class="table table-striped table-advance table-hover" id="tabela_compra_venda" style="font-size: 12px">';
                          
        echo '<tbody>';
          
            $sql = "SELECT * from tbl_venda 
                WHERE tbl_venda_lixeira=0" . $wlocal_origem . $wlocal_destino . $wtipo . $wperiodo .
                " ORDER BY tbl_venda_emissao DESC"; 

            $rs = mysqli_query($conector, $sql); 

            while ($reg_venda = mysqli_fetch_object($rs)){
                $codigo = $reg_venda->tbl_venda_id ;
                $codigo_origem = $reg_venda->tbl_venda_codigo_local_origem;
                $codigo_destino = $reg_venda->tbl_venda_codigo_local_destino;
                $lixeira = $reg_venda->tbl_venda_lixeira; 
                $situacao = $reg_venda->tbl_venda_situacao; 
                $categoria = $reg_venda->tbl_venda_categoria; 

                if ($situacao=="N") {
                    $desc_mov = 'Pendente';
                }
                else {
                    $desc_mov = 'Baixado';
                }

                if ($categoria==2) {
                    $desc_categoria = 'Compra';
                }
                else {
                    $desc_categoria = 'Venda';
                }

                $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_origem'");
                $num_rows = mysqli_num_rows($tbl_local);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tbl_local);
                    $desc_origem = $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_origem = '';
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

                $data_venda = new DateTime($reg_venda->tbl_venda_emissao);
                $data_venda_edi = $data_venda->format('d/m/Y');

                echo "<tr>";
                echo "<td width='12%'>".$data_venda_edi."</td>";
                echo "<td width='15%' class='situacao_nao'>".$desc_origem."</td>";
                echo "<td width='15%' class='situacao_nao'>".$desc_destino."</td>";
                echo "<td width='14%' class='situacao_nao'>".$desc_categoria."</td>";
                echo "<td width='14%' class='situacao_nao'>".$desc_mov."</td>";
                echo "<td width='10%'>";    
                echo "<div class='btn-group'>";
                echo "<a class='btn tooltips' href='form_venda_animais_consultar.php?id=".$codigo."'><i class='icon_search_alt' data-toggle='tooltip' data-placement='left' title='Editar esse registro' o></i></a>"; 

                echo "<a class='btn' href='#'>
                    <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Excluir esse registro' onClick='excluir_venda(\"{$codigo}\")'>
                        </i></a>";
                echo "</div>";
                echo "</td>";
                echo "</tr>";
            } 
            mysqli_close($conector);
            
        echo '</tbody>';

        echo '<thead>
            <tr>
                <th> Data</th>
                <th> Origem</th>
                <th> Destino</th>
                <th> Compra/Venda</th>
                <th> Movimentação</th>
                <th><i class="icon_cogs"></i> Ações</th>
            </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

?>
    <script src="js/compra_venda.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html> 

                
                
