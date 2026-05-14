<?php
    include "conecta_mysql.inc";

    $wlocal = "";
    $wlocal_destino = "";
    if (isset($_POST['local'])) {
        $local = $_POST['local'];

        if(in_array("", $local)) {
            $wlocal='';
            $wlocal_destino = "";
        }
        else {
            $wlocal = " AND (tbl_movimentacao_codigo_local_origem IN(";
            $wlocal.= implode(',', $local);
            $wlocal.= ") OR tbl_movimentacao_codigo_local_destino IN(";
            $wlocal.= implode(',', $local);
            $wlocal.= "))";
        }
    }

    $wtipo = "";
    if (isset($_POST['tipo'])) {
        $tipo = $_POST['tipo'];

        if(in_array("", $tipo)) {
            $wtipo='';
        }
        else {
            if ($tipo==999){
                $wtipo = " AND tbl_movimentacao_tipo!=3 AND tbl_movimentacao_tipo!=4 AND tbl_movimentacao_tipo!=5";
            }
            else {
                $wtipo = " AND tbl_movimentacao_tipo IN(";
                $wtipo.= implode(',', $tipo);
                $wtipo.= ")";
            }
        }
    }
    else {
        $wtipo='';
    }


    $data_inicial = $_POST["data_inicial"];
    $data_final = $_POST["data_final"];

    if ($data_inicial==0 && $data_final==0){
        $wperiodo = '';
    }
    else {
        $wperiodo = " AND tbl_movimentacao_data >= '$data_inicial' AND tbl_movimentacao_data <= '$data_final'";
    }

    @ session_start(); 

    $_SESSION['local_movimentacao']=implode(',', $local);
    $_SESSION['tipo_movimentacao']=$tipo;
    $_SESSION['data_inicial_movimentacao']=$data_inicial; 
    $_SESSION['data_final_movimentacao']=$data_final; 
    $_SESSION['lista_movimentacao']='S';

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
  <link rel="shortcut icon" href="img/logo_verde_marron.ico">
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
    echo '<table class="table table-striped table-advance table-hover" id="tabela_movimentacao" style="font-size: 12px">';
                          
            echo '<tbody>';
                $sql = "SELECT * from tbl_movimentacao 
                    WHERE tbl_movimentacao_lixeira=0" . $wlocal . $wtipo . $wperiodo 
                    . " ORDER BY tbl_movimentacao_id DESC"; 

                $rs = mysqli_query($conector, $sql); 

                while ($reg_mov = mysqli_fetch_object($rs)){
                    $codigo = $reg_mov->tbl_movimentacao_id;
                    $codigo_tipo = $reg_mov->tbl_movimentacao_tipo;
                    $codigo_origem = $reg_mov->tbl_movimentacao_codigo_local_origem;
                    $codigo_destino = $reg_mov->tbl_movimentacao_codigo_local_destino;
                    $lixeira = $reg_mov->tbl_movimentacao_lixeira; 
                    $aceite_financeiro = $reg_mov->tbl_movimentacao_aceite_financeiro_em; 
                    $aceite_transferencia = $reg_mov->tbl_movimentacao_aceite_transferencia_em; 
                    $codigo_pesagem = $reg_mov->tbl_movimentacao_codigo_pesagem;
                    $situacao = $reg_mov->tbl_movimentacao_situacao;

                    if ($aceite_transferencia=='') {
                        if ($situacao=='') {
                            $desc_situacao_transf = 'Aguardando Finalizar a Movimentação';
                        }
                        else {
                            $desc_situacao_transf = 'Aguardando Aceite';
                        }
                    }
                    else {
                        $desc_situacao_transf = 'Transferido';
                    }

                    if ($codigo_origem==999999999) {
                        $desc_situacao_financeira = 'Confirmado';
                    }
                    else if ($aceite_financeiro=='') {
                        if ($situacao=='') {
                            $desc_situacao_financeira = 'Aguardando Finalizar a Movimentação';
                        }
                        else {
                            $desc_situacao_financeira = 'Aguardando Faturamento';
                        }
                    }
                    else {
                        $desc_situacao_financeira = 'Faturado';
                    }

                    $desc_situacao='Confirmado';

                    switch ($codigo_tipo) {
                        case 3:
                            $descricao_tipo = 'Venda';
                            break;
                        case 4:
                            $descricao_tipo = 'Compra';
                            break;
                        case 5:
                            $descricao_tipo = 'Transferência';
                            break;
                        case 888:
                            $descricao_tipo = 'Morte';
                            break;
                        case 881:
                            $descricao_tipo = 'Natimorto';
                            break;
                        default:
                            $descricao_tipo = 'Outras Saídas';
                            break;
                    }

                    if ($codigo_origem==999999999) {
                        $desc_origem = 'ACERTO INICIAL DO ESTOQUE';
                    }
                    else {
                        $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_origem'");
                        $num_rows = mysqli_num_rows($tbl_local);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tbl_local);
                            $desc_origem = $reg->tbl_pessoa_nome;
                        }
                        else {
                            $desc_origem = '';
                        }
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

                    $data_movimentacao = new DateTime($reg_mov->tbl_movimentacao_data);
                    $data_movimentacao_edi = $data_movimentacao->format('d/m/Y');

                    $partes = explode("/", $data_movimentacao_edi);
                    $mes = $partes[1];
                    $ano = $partes[2];

                    $anomes_mov = $ano.$mes;

                    $data_sistema = date("Y-m-d");
                    $partes = explode("-", $data_sistema);
                    $mes = $partes[1];
                    $ano = $partes[0];

                    $anomes_sistema = $ano.$mes;

                    if ($codigo_tipo==3 || $codigo_tipo==5) {
                        $tab_itens = mysqli_query($conector, "select * from tbl_item_movimentacao 
                            where 
                            tbl_ite_movimentacao_numero_id='$codigo' and 
                            tbl_ite_movimentacao_selecionado='S'");
            
                        $qtd_animais = mysqli_num_rows($tab_itens);
                    }
                    else {
                        $qtd_animais = intval($reg_mov->tbl_movimentacao_qtd_animais_pesados);
                    }

                    if ($codigo_tipo==4) {
                        echo "<tr>";
                       // echo "<td width='8%'>".$codigo."</td>";
                        echo "<td width='8%'>".$data_movimentacao_edi."</td>";
                        echo "<td width='20%'>".$desc_origem."</td>";
                        echo "<td width='20%'>".$desc_destino."</td>";
                        echo "<td width='12%'>".$descricao_tipo."</td>";
                        echo "<td width='12%'>".$desc_situacao_financeira."</td>";
                        echo "<td width='8%' align='center'>".$qtd_animais."</td>";
                        if ($aceite_financeiro==''){
                            echo "<td width='14%'>";    
                            echo "<div class='btn-group'>";
                            echo "<a class='btn' href='form_movimentacao_animais_consultar_individual.php?id=".$codigo."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' o></i></a>"; 

                            if ($codigo_origem!=999999999) {
                                echo "<a class='btn' href='#'>
                                    <i class='fa fa-retweet' data-toggle='tooltip' data-placement='left'  title='Confirmar o faturamento' onClick='modal_faturamento_compra(\"{$codigo}\",\"{$qtd_animais}\")'></i></a>";
                            }

                            if ($anomes_mov>=$anomes_sistema) {
                                echo "<a class='btn' href='#'>
                                    <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Excluir esse registro' onClick='excluir_movimentacao(\"{$codigo}\",1)'>
                                            </i></a>";
                            }

                            echo "</div>";
                            echo "</td>";
                        }
                        else {
                            echo "<td width='14%'>";    
                            echo "<div class='btn-group'>";
                            echo "<a class='btn' href='form_movimentacao_animais_consultar_individual.php?id=".$codigo."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' o></i></a>";

                            echo "<a class='btn' href='#'>
                                <i class='icon_document_alt' data-toggle='tooltip' data-placement='left'  title='Imprimir tabela Excel' onClick='imprimir_movimentacao(\"{$codigo}\")' >
                                        </i></a>";
                            echo "</div>";
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    else {
                        foreach ($array_locais_usuario as $value) {
                            $value = ltrim($value);
                            $value = rtrim($value);
                            if ($value==$codigo_origem) {

                                if ($situacao=='') {
                                    echo "<tr class='status_nao'>";
                                }
                                else {
                                    echo "<tr>";
                                }

                                if ($lixeira==1) {
                                    echo "<td width='8%' style='color:#ccc'>".$data_movimentacao_edi."</td>";
                                    echo "<td width='20%' style='color:#ccc'>".$desc_origem."</td>";
                                    echo "<td width='20%' style='color:#ccc'>".$desc_destino."</td>";
                                    echo "<td width='20%' style='color:#ccc'>".$descricao_tipo."</td>";
                                    echo "<td width='12%' style='color:#ccc'>".$desc_situacao."</td>";
                                    echo "<td width='12%' style='color:#ccc'>";    
                                    echo "<td width='8%' align='center'>".$qtd_animais."</td>";
                                    echo "<div width='14%' class='btn-group'>";
                                    echo "<a class='btn' href='form_movimentacao_animais_consultar_individual.php?id=".$codigo."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>"; 
                                    echo "</div>";
                                    echo "</td>";
                                }
                                else if ($aceite_transferencia=='' && $codigo_tipo==5){
                                    echo "<td width='8%' class='situacao_nao'>".$data_movimentacao_edi."</td>";
                                    echo "<td width='20%' class='situacao_nao'>".$desc_origem."</td>";
                                    echo "<td width='20%' class='situacao_nao'>".$desc_destino."</td>";
                                    echo "<td width='12%' class='situacao_nao'>".$descricao_tipo."</td>";
                                    echo "<td width='12%' class='situacao_nao'>".$desc_situacao_transf."</td>";
                                    echo "<td width='8%' align='center'>".$qtd_animais."</td>";
                                    echo "<td width='14%'>";    
                                    echo "<div class='btn-group'>";

                                    if ($situacao=='') {
                                        echo "<a class='btn' href='#'>
                                            <i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar e confirmar a baixa do estoque' onClick='editar_movimentacao(\"{$codigo}\")'></i></a>";

                                        echo "<a class='btn' href='#'>
                                            <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Excluir esse registro' onClick='excluir_movimentacao(\"{$codigo}\",2)'>
                                                </i></a>";
                                    }
                                    else {
                                        echo "<a class='btn' href='form_movimentacao_animais_consultar_individual.php?id=".$codigo."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>"; 
                                        echo "<a class='btn' href='#'>
                                            <i class='fa fa-retweet' data-toggle='tooltip' data-placement='left' title='Aceite da transferência' onClick='confirmar_aceite_transferencia(\"{$codigo}\",\"{$qtd_animais}\", \"{$desc_origem}\", \"{$desc_destino}\")'></i></a>";

                                        if ($anomes_mov>=$anomes_sistema) {
                                            echo "<a class='btn' href='#'>
                                                <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Excluir esse registro' onClick='excluir_movimentacao(\"{$codigo}\",1)'>
                                                </i></a>";
                                        }
                                    }

                                    echo "</div>";
                                    echo "</td>";
                                }
                                else if ($aceite_transferencia!='' && $codigo_tipo==5){
                                    echo "<td width='8%' class='situacao_nao'>".$data_movimentacao_edi."</td>";
                                    echo "<td width='20%' class='situacao_nao'>".$desc_origem."</td>";
                                    echo "<td width='20%' class='situacao_nao'>".$desc_destino."</td>";
                                    echo "<td width='12%' class='situacao_nao'>".$descricao_tipo."</td>";
                                    echo "<td width='12%' class='situacao_nao'>".$desc_situacao_transf."</td>";
                                    echo "<td width='8%' align='center'>".$qtd_animais."</td>";
                                    echo "<td width='14%'>";    
                                    echo "<div class='btn-group'>";

                                    echo "<a class='btn' href='form_movimentacao_animais_consultar_individual.php?id=".$codigo."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' o></i></a>";

                                    echo "<a class='btn' href='#'>
                                        <i class='icon_document_alt' data-toggle='tooltip' data-placement='left'  title='Imprimir tabela Excel' onClick='imprimir_movimentacao(\"{$codigo}\")' >
                                        </i></a>";
                                    echo "</div>";
                                    echo "</td>";
                                }
                                else if ($aceite_financeiro=='' && $codigo_tipo==3){
                                    echo "<td width='8%' class='situacao_nao'>".$data_movimentacao_edi."</td>";
                                    echo "<td width='20%' class='situacao_nao'>".$desc_origem."</td>";
                                    echo "<td width='20%' class='situacao_nao'>".$desc_destino."</td>";
                                    echo "<td width='12%' class='situacao_nao'>".$descricao_tipo."</td>";
                                    echo "<td width='12%' class='situacao_nao'>".$desc_situacao_financeira."</td>";
                                    echo "<td width='8%' align='center'>".$qtd_animais."</td>";
                                    echo "<td width='14%'>";    
                                    echo "<div class='btn-group'>";

                                    if ($situacao=='') {
                                        echo "<a class='btn' href='#'>
                                            <i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar e confirmar a baixa do estoque' onClick='editar_movimentacao(\"{$codigo}\")'></i></a>";

                                        echo "<a class='btn' href='#'>
                                            <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='excluir_movimentacao(\"{$codigo}\",2)'>
                                                </i></a>";
                                    }
                                    else {
                                        echo "<a class='btn' href='form_movimentacao_animais_consultar_individual.php?id=".$codigo."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>"; 

                                        echo "<a class='btn' href='#'>
                                            <i class='fa fa-retweet' data-toggle='tooltip' data-placement='left' title='Confirmar o faturamento' onClick='modal_faturamento_venda(\"{$codigo}\",\"{$qtd_animais}\")'></i></a>";

                                        if ($anomes_mov>=$anomes_sistema) {
                                            echo "<a class='btn' href='#'>
                                                <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='excluir_movimentacao(\"{$codigo}\",1)'>
                                                </i></a>";
                                        }
                                    }

                                    echo "</div>";
                                    echo "</td>";
                                }
                                else if ($aceite_financeiro!='' && $codigo_tipo==3){
                                    echo "<td width='8%' class='situacao_nao'>".$data_movimentacao_edi."</td>";
                                    echo "<td width='20%' class='situacao_nao'>".$desc_origem."</td>";
                                    echo "<td width='20%' class='situacao_nao'>".$desc_destino."</td>";
                                    echo "<td width='12%' class='situacao_nao'>".$descricao_tipo."</td>";
                                    echo "<td width='12%' class='situacao_nao'>".$desc_situacao_financeira."</td>";
                                    echo "<td width='8%' align='center'>".$qtd_animais."</td>";
                                    echo "<td width='14%'>";    
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' href='form_movimentacao_animais_consultar_individual.php?id=".$codigo."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' o></i></a>";

                                    echo "<a class='btn' href='#'>
                                        <i class='icon_document_alt' data-toggle='tooltip' data-placement='left'  title='Imprimir tabela Excel' onClick='imprimir_movimentacao(\"{$codigo}\")' >
                                        </i></a>";
                                    echo "</div>";
                                    echo "</td>";
                                }
                                else if ($codigo_tipo!=3 && $codigo_tipo!=4 && $codigo_tipo!=5){
                                    echo "<td width='8%' class='situacao_nao'>".$data_movimentacao_edi."</td>";
                                    echo "<td width='20%' class='situacao_nao'>".$desc_origem."</td>";
                                    echo "<td width='20%' class='situacao_nao'>".$desc_destino."</td>";
                                    echo "<td width='12%' class='situacao_nao'>".$descricao_tipo."</td>";
                                    echo "<td width='12%' class='situacao_nao'>".$desc_situacao."</td>";
                                    echo "<td width='8%' align='center'>".$qtd_animais."</td>";
                                    echo "<td width='14%'>";    
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' href='form_movimentacao_animais_consultar_individual.php?id=".$codigo."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' o></i></a>";

                                    echo "<a class='btn' href='#'>
                                        <i class='icon_document_alt' data-toggle='tooltip' data-placement='left'  title='Imprimir tabela Excel' onClick='imprimir_movimentacao(\"{$codigo}\")' >
                                        </i></a>";

                                    if ($anomes_mov>=$anomes_sistema) {
                                        if ($codigo_tipo==888 || $codigo_tipo==999) {
                                            echo "<a class='btn' href='#'>
                                                <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Excluir esse registro' onClick='excluir_movimentacao(\"{$codigo}\",1)'>
                                                </i></a>";
                                        }
                                    }

                                    echo "</div>";
                                    echo "</td>";
                                }
                                else {
                                    echo "<td width='8%'>".$data_movimentacao_edi."</td>";
                                    echo "<td width='20%'>".$desc_origem."</td>";
                                    echo "<td width='20%'>".$desc_destino."</td>";
                                    echo "<td width='12%'>".$descricao_tipo."</td>";
                                    echo "<td width='12%'>".$desc_situacao."</td>";
                                    echo "<td width='8%' align='center'>".$qtd_animais."</td>";
                                    echo "<td width='14%'>";    
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' href='form_movimentacao_animais_consultar_individual.php?id=".$codigo."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' o></i></a>";

                                    echo "<a class='btn' href='#'>
                                        <i class='icon_document_alt' data-toggle='tooltip' data-placement='left'  title='Imprimir tabela Excel' onClick='imprimir_movimentacao(\"{$codigo}\")' >
                                        </i></a>";
                                    echo "</div>";
                                    echo "</td>";
                                }
                                echo "</tr>";
                            }
                        }
                    }
                } 
                mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th>Data</th>
                    <th>Origem</th>
                    <th>Destino</th>
                    <th>Movimentacao</th>
                    <th>Situação</th>
                    <th>Qtde Animais</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

//    echo '<script src="js/tabela.js" charset="utf-8" type="text/javascript" ></script>';

?>
    <script src="js/movimentacao.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html> 

                
                
