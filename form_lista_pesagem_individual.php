<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $array_fazenda = $_POST['local'];  
    $fazenda= array();
    $matriz_itens = explode(",", $array_fazenda);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $fazenda[$i]=$matriz_itens[$i];
    }

    $fazenda = implode(',', $fazenda);
    $fazenda = substr($fazenda,0, -1);

    $wlocal = '';

    if ($array_fazenda!='') {
        $wlocal = " AND tbl_pesagem_codigo_local IN(";
        $wlocal.= $fazenda;
        $wlocal.= ")";
    }

    $wepoca = "";
    if (isset($_POST['epoca'])) {
        $epoca = $_POST['epoca'];

        if(in_array("", $epoca)) {
            $wepoca='';
        }
        else {
            $wepoca = " AND tbl_pesagem_codigo_epoca IN(";
            $wepoca.= implode(',', $epoca);
            $wepoca.= ")";
            }
    }
    else {
        $wepoca='';
    }


    $data_inicial = $_POST["data_inicial"];
    $data_final = $_POST["data_final"];

    if ($data_inicial==0 && $data_final==0){
        $wperiodo = '';
    }
    else {
        $wperiodo = " AND tbl_pesagem_data >= '$data_inicial' AND tbl_pesagem_data <= '$data_final'";
    }

    @ session_start(); 

    $_SESSION['local_pesagem']=$array_fazenda;
    $_SESSION['epoca_pesagem']=$epoca;
    $_SESSION['data_inicial_pesagem']=$data_inicial; 
    $_SESSION['data_final_pesagem']=$data_final; 
    $_SESSION['lista_pesagem']='S';

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
    echo '<table class="table table-striped table-advance table-hover" id="tabela_pesagem" style="font-size: 12px">';
                          
            echo '<tbody>';
          
                $sql = "SELECT * from tbl_pesagem 
                    WHERE tbl_pesagem_lixeira=0 AND 
                          tbl_pesagem_controle='I'" . $wlocal . $wepoca . $wperiodo .
                    " ORDER BY tbl_pesagem_data DESC, tbl_pesagem_id DESC"; 

                $rs = mysqli_query($conector, $sql); 

                while ($reg_pesagem = mysqli_fetch_object($rs)){
                    $codigo = $reg_pesagem->tbl_pesagem_id;
                    $codigo_epoca = $reg_pesagem->tbl_pesagem_codigo_epoca;
                    $codigo_local = $reg_pesagem->tbl_pesagem_codigo_local;
                    $lote = $reg_pesagem->tbl_pesagem_lote;
                    $qtd_animais = $reg_pesagem->tbl_pesagem_qtd_animais_pesados;
                    $peso_kg = $reg_pesagem->tbl_pesagem_peso_kg;
                    $peso_arroba= $reg_pesagem->tbl_pesagem_peso_arroba;
                    $lixeira = $reg_pesagem->tbl_pesagem_lixeira; 
                    $pesagem_finalizada = $reg_pesagem->tbl_pesagem_finalizada; 
                    $movimentacao = $reg_pesagem->tbl_pesagem_codigo_movimentacao; 
                    $filtros = $reg_pesagem->tbl_pesagem_filtros; 
                    $tipo_registo = $reg_pesagem->tbl_pesagem_tipo_registro;

                    if ($pesagem_finalizada=="S") {
                        $desc_situacao = 'Finalizada';
                    }
                    else {
                        $desc_situacao = 'Não Finalizada';
                        $tab_itens = mysqli_query($conector, "select * from tbl_item_pesagem where tbl_ite_pesagem_numero_id ='$codigo'");
                        $qtd_animais = mysqli_num_rows($tab_itens);
                    }

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

                            if ($movimentacao!=0 || ($codigo_epoca!=3 && $codigo_epoca!=4 && $codigo_epoca!=5 && $pesagem_finalizada=='S')) {
                                echo "<td width='8%' style='color:#b8b6b6'>".$data_pesagem_edi."</td>";
                                echo "<td width='8%' style='color:#b8b6b6'>".$lote."</td>";
                                echo "<td width='13%' style='color:#b8b6b6'>".$desc_local."</td>";
                                echo "<td width='12%' style='color:#b8b6b6'>".$descricao_epoca."</td>";
                                echo "<td width='13%' style='color:#b8b6b6'>".$qtd_animais."</td>";
                                echo "<td width='8%' style='color:#b8b6b6'>".number_format($peso_kg,2,',','.')."</td>";
                                echo "<td width='8%' style='color:#b8b6b6'>".number_format($peso_arroba,2,',','.')."</td>";
                                echo "<td width='8%' style='color:#b8b6b6'>Finalizada</td>";
                                echo "<td width='8%' style='color:#b8b6b6'>";    
                                echo "<div width='14%' class='btn-group'>";
                                echo "<a class='btn' href='form_pesagem_animais_consultar_individual.php?id=".$codigo."'><i class='icon_search_alt' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>"; 
                                echo "</div>";
                                echo "</td>";
                            }
                            else if ($pesagem_finalizada=='N'){
                                echo "<td width='8%' class='status_nao'>".$data_pesagem_edi."</td>";
                                echo "<td width='13%' class='status_nao'>".$lote."</td>";
                                echo "<td width='13%' class='status_nao'>".$desc_local."</td>";
                                echo "<td width='12%' class='status_nao'>".$descricao_epoca."</td>";
                                echo "<td width='8%' class='status_nao'>".$qtd_animais."</td>";
                                echo "<td width='8%' class='status_nao'>".number_format($peso_kg,2,',','.')."</td>";
                                echo "<td width='8%' class='status_nao'>".number_format($peso_arroba,2,',','.')."</td>";
                                echo "<td width='8%' class='status_nao'>".$desc_situacao."</td>";
                                echo "<td width='14%'>";    
                                echo "<div class='btn-group'>";

                                if ($tipo_registo=='OFFLINE') {
                                    echo "<a class='btn tooltips' href='form_pesagem_animais_editar_offline.php?id=".$codigo."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar e digitar o peso manualmente' o></i></a>"; 

                                    echo "<a class='btn' href='#'>
                                        <i class='fa fa-file-excel-o' data-toggle='tooltip' data-placement='left' title='Importar excel da pesagem individual' onClick='importar_excel_pesagem(\"{$codigo}\",\"{$desc_local}\",\"{$descricao_epoca}\",\"{$qtd_animais}\",\"{$codigo_local}\",\"{$codigo_epoca}\")'>
                                        </i></a>";
                                }
                                else {
                                    echo "<a class='btn tooltips' href='form_pesagem_animais_editar_online.php?id=".$codigo."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar e digitar o peso manualmente' o></i></a>"; 
                                }

                                echo "<a class='btn' href='#'>
                                    <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Excluir esse registro' onClick='enviar_pesagem_lixeira(\"{$codigo}\",\"{$desc_local}\",\"{$descricao_epoca}\",1)'>
                                    </i></a>";
                                echo "</div>";
                                echo "</td>";
                            }
                            else {
                                echo "<td width='8%'>".$data_pesagem_edi."</td>";
                                echo "<td width='13%'>".$lote."</td>";
                                echo "<td width='13%'>".$desc_local."</td>";
                                echo "<td width='12%'>".$descricao_epoca."</td>";
                                echo "<td width='8%'>".$qtd_animais."</td>";
                                echo "<td width='8%'>".number_format($peso_kg,2,',','.')."</td>";
                                echo "<td width='8%'>".number_format($peso_arroba,2,',','.')."</td>";
                                echo "<td width='8%'>Aguardando Movimentação</td>";
                                echo "<td width='14%'>";    
                                echo "<div class='btn-group'>";
                                echo "<a class='btn' href='form_pesagem_animais_consultar_individual.php?id=".$codigo."'><i class='icon_search_alt' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' o></i></a>"; 
                                echo "<a class='btn' href='#'>
                                    <i class='icon_document_alt' data-toggle='tooltip' data-placement='left'  title='Imprimir tabela Excel' onClick='imprimir_pesagem(\"{$codigo}\")' >
                                    </i></a>";
                                echo "</div>";
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                    }
                } 
                mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Data</th>
                    <th> Lote</th>
                    <th> Local</th>
                    <th> Motivo</th>
                    <th> Qtd Animais</th>
                    <th> Kg</th>
                    <th> Arroba</th>
                    <th> Pesagem</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

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

                
                
