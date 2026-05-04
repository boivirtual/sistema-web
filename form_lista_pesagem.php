<?php
    include "conecta_mysql.inc";

    $wlocal = "";
    if (isset($_POST['local'])) {
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
        $wperiodo = " AND DATE(tbl_pesagem_incluido_em) >= '$data_inicial' AND DATE(tbl_pesagem_incluido_em) <= '$data_final'";
    }

    @ session_start(); 

    $_SESSION['local_pesagem']=$local;
    $_SESSION['epoca_pesagem']=$epoca;
    $_SESSION['data_inicial_pesagem']=$data_inicial; 
    $_SESSION['data_final_pesagem']=$data_final; 

?>

<!--
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>


</head>

<body> -->
  <?php    
	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_pesagem" style="font-size: 12px">';
                          
            echo '<tbody>';
          
                $sql = "SELECT * from tbl_pesagem 
                                WHERE tbl_pesagem_lixeira=0" . 
                                      $wlocal . $wepoca . $wperiodo .
                                 " ORDER BY tbl_pesagem_id ASC"; 

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
                    $status = $reg_pesagem->tbl_pesagem_finalizada; 

                    if ($status=="S") {
                        $desc_situacao = 'Finalizada';
                    }
                    else {
                        $desc_situacao = 'Não Finalizada';
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

                    $data_pesagem = new DateTime($reg_pesagem->tbl_pesagem_incluido_em);
                    $data_pesagem_edi = $data_pesagem->format('d/m/Y H:i:s');

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
                        echo "<td style='color:#ccc'>".$lote."</td>";
                        echo "<td style='color:#ccc'>".$desc_local."</td>";
                        echo "<td style='color:#ccc'>".$descricao_epoca."</td>";
                        echo "<td style='color:#ccc'>".$qtd_animais."</td>";
                        echo "<td style='color:#ccc'>".$peso_kg."</td>";
                        echo "<td style='color:#ccc'>".$peso_arroba."</td>";
                        echo "<td style='color:#ccc'>".$desc_situacao."</td>";
                        echo "<td style='color:#ccc'>";    
                        echo "<div class='btn-group'>";
                        echo "<a class='btn' href='form_pesagem_animais_consultar.php?id=".$codigo."'><i class='icon_search_alt' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>"; 
                       // echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Restaurar da lixeira' onClick='enviar_lixeira(\"{$codigo}\",2)' ></i></a>"; 
                        echo "</div>";
                        echo "</td>";
                    }
                    else if ($status=='N'){
                        echo "<td width='12%' class='status_nao'>".$data_pesagem_edi."</td>";
                        echo "<td width='15%' class='status_nao'>".$lote."</td>";
                        echo "<td width='15%' class='status_nao'>".$desc_local."</td>";
                        echo "<td width='14%' class='status_nao'>".$descricao_epoca."</td>";
                        echo "<td width='10%' class='status_nao'>".$qtd_animais."</td>";
                        echo "<td width='8%' class='status_nao'>".$peso_kg."</td>";
                        echo "<td width='8%' class='status_nao'>".$peso_arroba."</td>";
                        echo "<td width='8%' class='status_nao'>".$desc_situacao."</td>";
                        echo "<td width='10%'>";    
                        echo "<div class='btn-group'>";
                        echo "<a class='btn tooltips' href='form_pesagem_animais_editar.php?id=".$codigo."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' o></i></a>"; 
                        echo "<a class='btn' href='#'>
                            <i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'  title='Excluir esse registro' onClick='enviar_pesagem_lixeira(\"{$codigo}\",\"{$desc_local}\",\"{$descricao_epoca}\",1)'>
                            </i></a>";
                        echo "</div>";
                        echo "</td>";
                    }
                    else {
                        echo "<td width='12%'>".$data_pesagem_edi."</td>";
                        echo "<td width='15%'>".$lote."</td>";
                        echo "<td width='15%'>".$desc_local."</td>";
                        echo "<td width='14%'>".$descricao_epoca."</td>";
                        echo "<td width='10%'>".$qtd_animais."</td>";
                        echo "<td width='8%'>".$peso_kg."</td>";
                        echo "<td width='8%'>".$peso_arroba."</td>";
                        echo "<td width='8%'>".$desc_situacao."</td>";
                        echo "<td width='10%'>";    
                        echo "<div class='btn-group'>";
                        echo "<a class='btn' href='form_pesagem_animais_consultar.php?id=".$codigo."'><i class='icon_search_alt' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' o></i></a>"; 
                        echo "<a class='btn' href='#'>
                            <i class='icon_document_alt' data-toggle='tooltip' data-placement='left'  title='Imprimir tabela Excel' onClick='imprimir_pesagem(\"{$codigo}\")' >
                            </i></a>";
                        //echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$codigo}\",1)' ></i></a>"; 
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
                    <th> Lote</th>
                    <th> Local</th>
                    <th> Motivo</th>
                    <th> Qtd Animais</th>
                    <th> Kg</th>
                    <th> Arroba</th>
                    <th> Situação</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

    echo '<script src="js/tabela.js" charset="utf-8" type="text/javascript" ></script>';

?>

                
                
