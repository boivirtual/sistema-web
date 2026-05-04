<?php
    include "conecta_mysql.inc";
    
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

	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_produtos" style="font-size: 12px">';
                          
            echo '<tbody>';
          
                $sql = "SELECT * from tbl_produto
                                WHERE tbl_produto_lixeira = 0
                             ORDER BY tbl_produto_descricao ASC"; 
                $rs = mysqli_query($conector, $sql); 

                while ($reg_produto = mysqli_fetch_object($rs)){
                    $codigo = $reg_produto->tbl_produto_codigo_id;
                    $codigo_modalidade = $reg_produto->tbl_produto_codigo_modalidade;
                    $descricao_produto = $reg_produto->tbl_produto_descricao;
                    $codigo_apresentacao = $reg_produto->tbl_produto_apresentacao;
                    $qtd_apresentacao = $reg_produto->tbl_produto_qtd_unidade; 
                    $codigo_unidade = $reg_produto->tbl_produto_unidade; 
                    $obs = $reg_produto->tbl_produto_observacao; 
                    $lixeira = $reg_produto->tbl_produto_lixeira; 
                    $descricao_complementar = $reg_produto->tbl_produto_complemento_descricao;
                    $codigo_padrao = $reg_produto->tbl_produto_codigo_generico;

                    $tab_unidade = mysqli_query($conector, "select * from tabela_unidade_produtos where tab_codigo_unidade_id='$codigo_unidade'");
                    $num_rows = mysqli_num_rows($tab_unidade);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_unidade);
                        $simbolo_unidade = $reg->tab_codigo_unidade_produtos;
                    }
                    else {
                        $simbolo_unidade = '';
                    }

                    $tab_apresentacao = mysqli_query($conector, "select * from tbl_apresentacao_produtos where tab_codigo_apresentacao_id='$codigo_apresentacao'");
                    $num_rows = mysqli_num_rows($tab_apresentacao);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_apresentacao);
                        $desc_apresentacao = $reg->tab_descricao_apresentacao_produtos;
                    }
                    else {
                        $desc_apresentacao = '';
                    }

                    $tab_modalidade = mysqli_query($conector, "select * from tbl_modalidade_produto where tbl_codigo_modalidade='$codigo_modalidade'");
                    $num_rows = mysqli_num_rows($tab_modalidade);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_modalidade);
                        $desc_modalidade = $reg->tbl_descricao_modalidade;
                    }
                    else {
                        $desc_modalidade = '';
                    }

                    $apresentacao = $desc_apresentacao . ' ' . number_format($qtd_apresentacao, 2, ",", ".") . ' ' . $simbolo_unidade;


                    $incluido_em=new DateTime($reg_produto->tbl_produto_incluido_em);
                    $incluido_por=$reg_produto->tbl_produto_incluido_por; 
                    $alterado_em=new DateTime($reg_produto->tbl_produto_alterado_em);
                    $alterado_por=$reg_produto->tbl_produto_alterado_por; 

                    $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
                    $alterado_em_edi = $alterado_em->format('d/m/Y H:i:s');

                    $codigo_local = array();
                    $estoque_atual = array();

                    $total_estoque = 0;

                    $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_produto_estoque
                                    WHERE tbl_produto_estoque_codigo_id='$codigo' AND 
                                          tbl_produto_estoque_lixeira = 0"); 
                    $num_rows = mysqli_num_rows($tbl_estoque);

                    if ($num_rows!=0) {
                        while ($reg_est = mysqli_fetch_object($tbl_estoque)){
                            $id_local = $reg_est->tbl_produto_estoque_codigo_local;
                            $qtd_estoque = $reg_est->tbl_produto_estoque_atual;

                            foreach ($array_locais_usuario as $value) {
                                $value = ltrim($value);
                                $value = rtrim($value);

                                if ($value==$id_local) {
                                    $codigo_local[] = $id_local;
                                    $estoque_atual[] = $qtd_estoque;
                                    $total_estoque+=$qtd_estoque;
                                }
                            }
                        }
                        $array_fazendas = implode("!", $codigo_local);
                        $array_estoque_atual = implode("!", $estoque_atual);
                    }
                    else {
                        $array_fazendas = '';
                        $array_estoque_atual = '';
                    }

                    $total_estoque_edi = number_format($total_estoque, 2, ",", ".");

                    if ($total_estoque>0) {
                        $total_estoque_apr = $total_estoque / $qtd_apresentacao;
                    }
                    else {
                        $total_estoque_apr=0;
                    }
                    
                    $total_estoque_apr_edi = number_format($total_estoque_apr, 2, ",", ".");

                    $array_animal = array(
                        $codigo,
                        $codigo_modalidade,
                        $descricao_produto,
                        $codigo_apresentacao,
                        $qtd_apresentacao,
                        $codigo_unidade,
                        $obs,
                        $total_estoque,
                        $descricao_complementar,
                        $codigo_padrao,
                        $array_fazendas,
                        $array_estoque_atual
                    );   
                                    
                    $string_array = implode('|', $array_animal);

                    echo "<tr>";

                    if ($lixeira==0) {
                        echo "<td width='10%'>".$desc_modalidade."</td>";
                        echo "<td width='10%'>".$descricao_produto."</td>";
                        echo "<td width='10%'>".$apresentacao."</td>";
                        echo "<td width='10%'>".$total_estoque_apr_edi." ".$desc_apresentacao."</td>";
                        echo "<td width='10%'>".$total_estoque_edi." ".$simbolo_unidade."</td>";
                        echo "<td width='10%'>";    
                        echo "<div class='btn-group'>";
                        echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\");";
                        echo "' ></i></a>"; 

                        echo "<a class='btn' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                        echo "</div>";
                        echo "</td>";
                    }
                    else {
                        echo "<td width='10%' style='color: #ccc;'>".$desc_modalidade."</td>";
                        echo "<td width='10%' style='color: #ccc;'>".$descricao_produto."</td>";
                        echo "<td width='10%' style='color: #ccc;'>".$apresentacao."</td>";
                        echo "<td width='10%' style='color: #ccc;'>".$total_estoque_edi." ".$simbolo_unidade."</td>";
                        echo "<td width='10%' style='color: #ccc;'>";    
                        echo "<div class='btn-group'>";
                        echo "<a class='btn' href='#'><i class='icon_refresh' data-toggle='tooltip' data-placement='left' title='Remover da lixeira' onClick='enviar_lixeira(\"{$string_array}\",3)' ></i></a>"; 
                        echo "</div>";
                        echo "</td>";
                    }
                    echo "</tr>";
                } 
                mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Modalidade</th>
                    <th> Descrição</th>
                    <th> Apresentação</th>
                    <th> Estoque Total Apresentação</th>
                    <th> Estoque Total Unidade</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';
?>

    <script src="js/tabela_produtos.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
    </script>

</body>
</html> 


                
                
