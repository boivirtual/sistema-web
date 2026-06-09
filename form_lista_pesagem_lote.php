<?php
    include "conecta_mysql.inc";

/*    $wlocal = '';
    $local = '';

    if (isset($_POST['local']) && $_POST['local']!='') {
        $local = $_POST['local'];

        if(in_array("", $local)) {
            $wlocal='';
        }
        else {
            $wlocal = " AND tbl_pesagem_codigo_local IN(";
            $wlocal.= implode(',', $local);
            $wlocal.= ")";
            }
    }
    else {
        $wlocal='';
    }
*/

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
    $_SESSION['local_pesagem_rel']='';
    $_SESSION['array_categoria_pesagem_rel']='';
    $_SESSION['sexo_pesagem_rel']='';

    $_SESSION['lista_pesagem']='S';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>


</head>

<body> 
  <?php    
	echo '<section class="panel lista_contas">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_pesagem" style="font-size: 12px">';
                          
            echo '<tbody>';
          
                $sql = "SELECT * from tbl_pesagem 
                    WHERE tbl_pesagem_lixeira=0 AND 
                          tbl_pesagem_controle='L'" . 
                          $wlocal . $wepoca . $wperiodo .
                    " ORDER BY tbl_pesagem_data DESC, tbl_pesagem_id DESC"; 

                $rs = mysqli_query($conector, $sql); 

                while ($reg_pesagem = mysqli_fetch_object($rs)){
                    $codigo = $reg_pesagem->tbl_pesagem_id;
                    $codigo_epoca = $reg_pesagem->tbl_pesagem_codigo_epoca;
                    $codigo_local = $reg_pesagem->tbl_pesagem_codigo_local;
                    $codigo_pasto = $reg_pesagem->tbl_pesagem_pasto;
                    $codigo_categoria = $reg_pesagem->tbl_pesagem_categoria;
                    $qtd_animais = $reg_pesagem->tbl_pesagem_qtd_animais_pesados;
                    $peso_kg = number_format($reg_pesagem->tbl_pesagem_peso_kg,2,',','.');
                    $peso_arroba= number_format($reg_pesagem->tbl_pesagem_peso_arroba,2,',','.');
                    $lixeira = $reg_pesagem->tbl_pesagem_lixeira; 
                    $pesagem_finalizada = $reg_pesagem->tbl_pesagem_finalizada; 
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

                    $tbl_pasto = mysqli_query($conector, "select * from tbl_pasto where tbl_pasto_id='$codigo_pasto'");
                    $num_rows = mysqli_num_rows($tbl_pasto);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_pasto);
                        $desc_pasto = $reg->tbl_pasto_descricao;
                    }
                    else {
                        $desc_pasto = '';
                    }

                    $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade where tab_codigo_categoria_idade='$codigo_categoria'");
                    $num_rows = mysqli_num_rows($tbl_categoria);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_categoria);
                        if ($reg->tab_categoria_idade_ate==999999999) {
                            $desc_categoria = '> 36 meses'; 
                        }
                        else {
                            $desc_categoria = $reg->tab_categoria_idade_de . ' a ' . 
                                          $reg->tab_categoria_idade_ate . ' meses';
                        }
                    }
                    else {
                        $desc_categoria = '';
                    }

                    $data_pesagem = new DateTime($reg_pesagem->tbl_pesagem_data);
                    $data_pesagem_edi = $data_pesagem->format('d/m/Y');

/*
                    $array_animal = array(
                        $reg_pesagem->tbl_animal_codigo_id,
                        $reg_pesagem->tbl_animal_codigo_numerico,
                        $reg_pesagem->tbl_animal_sexo,
                        $reg_pesagem->tbl_animal_codigo_epoca,
                        $reg_pesagem->tbl_animal_codigo_pelagem,
                        $reg_pesagem->tbl_animal_data_nascimento,
                        $reg_pesagem->tbl_animal_grau_sangue,
                        $reg_pesagem->tbl_animal_codigo_fazenda,
                        $reg_pesagem->tbl_animal_codigo_origem,
                        $pai,
                        $reg_pesagem->tbl_animal_nome_pai,
                        $reg_pesagem->tbl_animal_codigo_mae,
                        $reg_pesagem->tbl_animal_nome_mae,
                        $reg_pesagem->tbl_animal_primeiro_peso,
                        $reg_pesagem->tbl_animal_peso_desmama,
                        $reg_pesagem->tbl_animal_ultimo_peso,
                        $reg_pesagem->tbl_animal_nome,
                        $reg_pesagem->tbl_animal_registro_ren,
                        $reg_pesagem->tbl_animal_registro_rgd,
                        $reg_pesagem->tbl_animal_registro_sisbov,
                        $reg_pesagem->tbl_animal_certificadora,
                        $reg_pesagem->tbl_animal_observacao,
                        $reg_pesagem->tbl_animal_ativo,
                        $reg_pesagem->tbl_animal_codigo_alfa,
                        $desc_categoria,
                        $idade_animal,
                        $incluido_em_edi,
                        $incluido_por,
                        $alterado_em_edi,
                        $alterado_por,
                        $baixado_em_edi,
                        $baixado_por
                    );   
                                    
                    $string_array = implode('|', $array_animal);
*/
                    echo "<tr>";

                    if ($lixeira==1) {
                        echo "<td style='color:#ccc'>".$data_pesagem_edi."</td>";
                        echo "<td style='color:#ccc'>".$desc_local."</td>";
                        echo "<td style='color:#ccc'>".$desc_pasto."</td>";
                        echo "<td style='color:#ccc'>".$descricao_epoca."</td>";
                        echo "<td style='color:#ccc'>".$qtd_animais."</td>";
                        echo "<td style='color:#ccc'>".$peso_kg."</td>";
                        echo "<td style='color:#ccc'>".$peso_arroba."</td>";
                        echo "<td style='color:#ccc'>".$desc_situacao."</td>";
                        echo "<td style='color:#ccc'>";    
                        echo "<div class='btn-group'>";
                        echo "<a class='btn' href='form_pesagem_animais_consultar_lote.php?id=".$codigo."'><i class='icon_search_alt' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>"; 
                        echo "</div>";
                        echo "</td>";
                    }
                    else if ($pesagem_finalizada=='N'){
                        echo "<td width='8%' class='status_nao'>".$data_pesagem_edi."</td>";
                        echo "<td width='12%' class='status_nao'>".$desc_local."</td>";
                        echo "<td width='12%' class='status_nao'>".$desc_pasto."</td>";
                        echo "<td width='12%' class='status_nao'>".$descricao_epoca."</td>";
                        echo "<td width='10%' class='status_nao'>".$qtd_animais."</td>";
                        echo "<td width='8%' class='status_nao'>".$peso_kg."</td>";
                        echo "<td width='8%' class='status_nao'>".$peso_arroba."</td>";
                        echo "<td width='8%' class='status_nao'>".$desc_situacao."</td>";
                        echo "<td width='10%'>";    
                        echo "<div class='btn-group'>";

                        if ($tipo_registo=='OFFLINE') {
                            echo "<a class='btn tooltips' href='form_pesagem_animais_editar_lote_offline.php?id=".$codigo."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' o></i></a>"; 

                            echo "<a class='btn' href='#'>
                                <i class='fa fa-file-excel-o' data-toggle='tooltip' data-placement='left' title='Importar excel da pesagem individual' onClick='importar_excel_pesagem(\"{$codigo}\",\"{$desc_local}\",\"{$descricao_epoca}\",\"{$qtd_animais}\",\"{$codigo_local}\",\"{$codigo_epoca}\")'>
                                        </i></a>";
                        }
                        else {
                            echo "<a class='btn tooltips' href='form_pesagem_animais_editar_lote_online.php?id=".$codigo."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' o></i></a>"; 
                        }

                        echo "<a class='btn' href='#'>
                            <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Excluir esse registro' onClick='enviar_pesagem_lixeira(\"{$codigo}\",\"{$desc_local}\",\"{$descricao_epoca}\",1)'>
                            </i></a>";
                        echo "</div>";
                        echo "</td>";
                    }
                    else {
                        echo "<td width='8%'>".$data_pesagem_edi."</td>";
                        echo "<td width='12%'>".$desc_local."</td>";
                        echo "<td width='12%'>".$desc_pasto."</td>";
                        echo "<td width='12%'>".$descricao_epoca."</td>";
                        echo "<td width='10%'>".$qtd_animais."</td>";
                        echo "<td width='8%'>".$peso_kg."</td>";
                        echo "<td width='8%'>".$peso_arroba."</td>";
                        echo "<td width='8%'>".$desc_situacao."</td>";
                        echo "<td width='12%'>";    
                        echo "<div class='btn-group'>";
                        echo "<a class='btn' href='form_pesagem_animais_consultar_lote.php?id=".$codigo."'><i class='icon_search_alt' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' o></i></a>"; 
                        echo "<a class='btn' href='#'>
                            <i class='icon_document_alt' data-toggle='tooltip' data-placement='left'  title='Imprimir tabela Excel' onClick='imprimir_pesagem_lote(\"{$codigo}\")'>
                            </i></a>";
                        echo "<a class='btn' href='#'><i class='fa fa-server'  data-toggle='tooltip' data-placement='left' title='Indicar novos pastos' onClick='listar_grupo_destino(\"{$codigo}\")'></i></a>"; 
                        echo "</div>";
                        echo "</td>";
                    }
                    echo "</tr>";
                } 
                mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Data</th>
                    <th> Local</th>
                    <th> Pasto</th>
                    <th> Motivo</th>
                    <th> Qtd Animais</th>
                    <th> Total Kg</th>
                    <th> Total @</th>
                    <th> Pesagem</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

//    echo '<script src="js/tabela.js" charset="utf-8" type="text/javascript" ></script>';
?>
    <script src="js/pesagem.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html> 



                
                
