<?php
    include "conecta_mysql.inc";

    $wlocal = "";
    if (isset($_POST['local'])) {
        $local = $_POST['local'];

        if(in_array("", $local)) {
            $wlocal='';
        }
        else {
            $wlocal = " AND tbl_mov_estoque_local IN(";
            $wlocal.= implode(',', $local);
            $wlocal.= ")";
            }
    }
    else {
        $wlocal='';
    }

    $wtipo = "";
    if (isset($_POST['tipo'])) {
        $tipo = $_POST['tipo'];

        if(in_array("", $tipo)) {
            $wtipo='';
        }
        else {
            $wtipo = " AND tbl_mov_estoque_tipo_movimentacao IN(";
            $wtipo.= implode(',', $tipo);
            $wtipo.= ")";
        }
    }
    else {
        $wtipo='';
    }

    $data_nasc_inicial = $_POST["data_inicial"];
    $data_nasc_final = $_POST["data_final"];

    if ($data_nasc_inicial==0 && $data_nasc_final==0){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " tbl_mov_estoque_nascimento >= '$data_nasc_inicial' AND tbl_mov_estoque_nascimento <= '$data_nasc_final'";
    }

    @ session_start(); 

    $_SESSION['local']=$local;
    $_SESSION['data_inicial_nascimento']=$data_nasc_inicial; 
    $_SESSION['data_final_nascimento']=$data_nasc_final; 
    $_SESSION['lista_nascimento']='S';

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

    $controle_estoque = $_SESSION['controle_estoque'];

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

    <td align="center"></td>
  <?php    
	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_nascimento_lote">';
                          
            echo '<tbody>';
                if ($wtipo=='') {
                    $sql = "SELECT * FROM tbl_movimentacao_estoque 
                                WHERE" . $wdata_nasc . " AND 
                                (tbl_mov_estoque_tipo_movimentacao='N' OR 
                                 tbl_mov_estoque_tipo_movimentacao='A' OR
                                 tbl_mov_estoque_tipo_movimentacao='B')" . 
                                $wlocal .
                                " ORDER BY tbl_mov_estoque_data_emissao ASC"; 
                }
                else if ($wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('N')") { 
                    $sql = "SELECT * FROM tbl_movimentacao_estoque 
                                    WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                                     " AND tbl_mov_estoque_codigo_id_animal!=999999999
                                     ORDER BY tbl_mov_estoque_data_emissao ASC"; 
                                                         
                }
                else if ($wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('N','M')") {
                    $sql = "SELECT * FROM tbl_movimentacao_estoque 
                                    WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                                     " AND tbl_mov_estoque_entrada_saida='E'
                                     ORDER BY tbl_mov_estoque_data_emissao ASC"; 
                }
                else if ($wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('N','B')") {
                    $sql = "SELECT * FROM tbl_movimentacao_estoque 
                                    WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                                     " AND (tbl_mov_estoque_tipo_movimentacao='N' AND
                                            tbl_mov_estoque_codigo_id_animal!=999999999) 
                                       OR (tbl_mov_estoque_entrada_saida='A' AND tbl_mov_estoque_tipo_movimentacao='B')
                                     ORDER BY tbl_mov_estoque_data_emissao ASC"; 
                }
                else if ($wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('N','A')") {
                    $sql = "SELECT * FROM tbl_movimentacao_estoque 
                                    WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                                     " AND (tbl_mov_estoque_tipo_movimentacao='N' AND
                                            tbl_mov_estoque_codigo_id_animal!=999999999) 
                                       OR (tbl_mov_estoque_entrada_saida='A' AND tbl_mov_estoque_tipo_movimentacao='A')
                                     ORDER BY tbl_mov_estoque_data_emissao ASC"; 
                }
                else if ($wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('N','B','A')") {
                    $sql = "SELECT * FROM tbl_movimentacao_estoque 
                                    WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                                     " AND (tbl_mov_estoque_tipo_movimentacao='N' AND
                                            tbl_mov_estoque_codigo_id_animal!=999999999) 
                                       OR (tbl_mov_estoque_entrada_saida='A' AND 
                                           tbl_mov_estoque_tipo_movimentacao='A')
                                       OR (tbl_mov_estoque_entrada_saida='A' AND 
                                           tbl_mov_estoque_tipo_movimentacao='B')
                                     ORDER BY tbl_mov_estoque_data_emissao ASC"; 
                }

                else if ($wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('N','B','A','M')" || $wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('N','B','M')" || $wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('N','A','M')") { 
                    $sql = "SELECT * FROM tbl_movimentacao_estoque 
                                    WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                                     " AND tbl_mov_estoque_entrada_saida!='S'
                                     ORDER BY tbl_mov_estoque_data_emissao ASC"; 
                }
                else if ($wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('M')"
                     || $wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('A','M')" 
                     || $wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('B','A','M')" 
                     || $wtipo==" AND tbl_mov_estoque_tipo_movimentacao IN('B','M')"){ 
                    $sql = "SELECT * FROM tbl_movimentacao_estoque 
                                    WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                                     " AND tbl_mov_estoque_codigo_id_animal=999999999
                                     ORDER BY tbl_mov_estoque_data_emissao ASC"; 
                }
                else {
                    $sql = "SELECT * FROM tbl_movimentacao_estoque 
                                    WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                                     " ORDER BY tbl_mov_estoque_data_emissao ASC"; 
                }

                $rs = mysqli_query($conector, $sql); 
                $num_rows_estoque = mysqli_num_rows($rs);
                $total_nascimento = 0;

                while ($reg_nasc = mysqli_fetch_object($rs)){
                    $codigo_fazenda = $reg_nasc->tbl_mov_estoque_local;

                    foreach ($array_locais_usuario as $value) {
                        $value = ltrim($value);
                        $value = rtrim($value);

                        if ($value==$codigo_fazenda) {
                            $num_mov_id = $reg_nasc->tbl_mov_estoque_numero_id ;
                            $codigo = $reg_nasc->tbl_mov_estoque_codigo_id_animal;
                            $data_emissao = $reg_nasc->tbl_mov_estoque_data_emissao;
                            $data_nascimento = new DateTime($reg_nasc->tbl_mov_estoque_nascimento);
                            $nascimento = $reg_nasc->tbl_mov_estoque_nascimento;
                            $codigo_pasto = $reg_nasc->tbl_mov_estoque_codigo_pasto;
                            $peso = $reg_nasc->tbl_mov_estoque_primeiro_peso;
                            $tipo_movimentacao = $reg_nasc->tbl_mov_estoque_tipo_movimentacao;

                            if ($tipo_movimentacao=='A') {
                                $desc_tipo = 'Aborto';
                                $ocorrencia = 'A';
                                $total_aborto++;
                            }
                            else if ($tipo_movimentacao=='B') {
                                $desc_tipo = 'Absorção';
                                $ocorrencia = "B";
                                $total_absorcao++;
                            }
                            else if ($tipo_movimentacao=='M' || ($tipo_movimentacao=='N' && $codigo==999999999)) { 
                                $desc_tipo = '';
                                $ocorrencia = 'M';
                            }
                            else {
                                $desc_tipo = '';
                                $ocorrencia = 'N';
                            }

                            $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_fazenda'");
                            $num_rows = mysqli_num_rows($tab_fazenda);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tab_fazenda);
                                $desc_local = $reg->tbl_pessoa_nome;
                            }
                            else {
                                $desc_local = '';
                            }

                            $tab_pasto = mysqli_query($conector, "select * from tbl_pasto where tbl_pasto_id ='$codigo_pasto'");
                            $num_rows = mysqli_num_rows($tab_pasto);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tab_pasto);
                                $desc_pasto = $reg->tbl_pasto_descricao;
                            }
                            else {
                                $desc_pasto = '';
                            }

                            $codigo_raca = $reg_nasc->tbl_mov_estoque_codigo_raca;
                            $codigo_pelagem = $reg_nasc->tbl_mov_estoque_codigo_pelagem;
                            $sexo = $reg_nasc->tbl_mov_estoque_sexo;
                            
                            $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
                                $num_rows_raca = mysqli_num_rows($tab_raca);

                            if ($num_rows_raca!=0){
                                $reg = mysqli_fetch_object($tab_raca);
                                $descricao_raca = $reg->tab_descricao_raca;
                            }
                            else {
                                $descricao_raca = '';
                            }

                            $tab_pelagem = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem ='$codigo_pelagem'");
                            $num_rows_pelagem = mysqli_num_rows($tab_pelagem);

                            if ($num_rows_pelagem!=0){
                                $reg = mysqli_fetch_object($tab_pelagem);
                                $descricao_pelagem = $reg->tab_descricao_pelagem;
                            }
                            else {
                                $descricao_pelagem = '';
                            }

                            $codigo_alfa='';
                            $codigo_numerico=0;
                            $pai=0;
                            $mae=0;
                            $mae_raca='';

                            $array_animal = array(
                                $codigo,
                                $codigo_alfa,
                                $codigo_numerico,
                                $sexo,
                                $codigo_raca,
                                $codigo_pelagem,
                                $reg_nasc->tbl_mov_estoque_nascimento,
                                $codigo_fazenda,
                                $codigo_pasto,
                                $pai,
                                $mae,
                                $peso,
                                $mae_raca,
                                $desc_local,
                                $num_mov_id,
                                $desc_pasto,
                                $data_emissao,
                                $tipo_movimentacao,
                                $ocorrencia
                            );   
                                                
                            $string_array = implode('|', $array_animal);
                            $total_nascimento++;

                            echo "<tr>";
                            echo "<td align='center' width='10%'>".$data_nascimento->format('d/m/Y')."</td>";
                            echo "<td align='center' width='10%' >".$sexo."</td>";
                            echo "<td width='10%'>".$peso."</td>";
                            echo "<td  width='10%'>".$descricao_raca."</td>";
                            echo "<td  width='10%'>".$descricao_pelagem."</td>";
                            echo "<td  width='15%'>".$desc_local."</td>";
                            echo "<td  width='15%'>".$desc_pasto."</td>";
                            echo "<td width='20%'>";    
                            echo "<div class='btn-group'>";
                            echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\")' ></i></a>"; 
                            echo "<a class='btn' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
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
                <th style="vertical-align: middle;text-align:center;"> Data</th>
                <th> Sexo</th>
                <th> Peso</th>
                <th> Raça</th>
                <th> Pelagem</th>
                <th> Local</th>
                <th> Pasto</th>
                <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
            
       echo '</table>';

    echo '</section>';
?>

    <script src="js/nascimento.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html> 


                
                
