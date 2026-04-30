<?php
    include "conecta_mysql.inc";

    $codigo_id = $_POST['codigo_id'];

	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" style="font-size: 11px">';
                          
    echo '<tbody>';

    $sql = "select * from tbl_item_pesagem 
               inner join tbl_pesagem
                       on tbl_pesagem_id = tbl_ite_pesagem_numero_id   
                    where tbl_ite_pesagem_codigo_id_animal='$codigo_id' and 
                          tbl_ite_pesagem_peso!=0 and tbl_pesagem_finalizada='S'
                    order by tbl_ite_pesagem_data_emissao DESC, tbl_pesagem_id DESC"; 
    $rs = mysqli_query($conector, $sql); 

    while ($reg_ite_peso = mysqli_fetch_object($rs)){
        $codigo_animal = $reg_ite_peso->tbl_ite_pesagem_codigo_animal;
        $data = new DateTime($reg_ite_peso->tbl_ite_pesagem_data_emissao); 
        $data_edi = $data->format('d/m/Y');
        $epoca = $reg_ite_peso->tbl_pesagem_codigo_epoca; 
        $origem = $reg_ite_peso->tbl_pesagem_codigo_local; 
        $peso = $reg_ite_peso->tbl_ite_pesagem_peso; 

        $tab_origem = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$origem'");
        $num_rows = mysqli_num_rows($tab_origem);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_origem);
            $desc_origem = $reg->tbl_pessoa_nome;
        }
        else {
            $desc_origem = '';
        }

        $tab_epoca = mysqli_query($conector, "select * from tabela_epoca_pesagem where tab_codigo_epoca_pesagem ='$epoca'");
        $num_rows = mysqli_num_rows($tab_epoca);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_epoca);
            $desc_epoca = $reg->tab_descricao_epoca_pesagem;
        }
        else {
            $desc_epoca= '';
        }

        echo "<tr>";
        echo "<td width='8%'>".$data_edi."</td>";
        echo "<td width='25%'>".$desc_epoca."</td>";
        echo "<td width='25%'>".$desc_origem."</td>";
        echo "<td width='25%'>".$peso." Kg</td>";
        echo "<td width='17%'></td>";
        echo "</tr>";
    } 

    $sql = "SELECT * FROM tbl_animais 
        WHERE tbl_animal_codigo_id='$codigo_id'"; 

    $rs = mysqli_query($conector, $sql); 

    $reg_animal = mysqli_fetch_object($rs);
    $primeiro_peso = $reg_animal->tbl_animal_primeiro_peso;
    $data = new DateTime($reg_animal->tbl_animal_data_primeiro_peso); 
    $data_edi = $data->format('d/m/Y');

    $sql = "SELECT * FROM tbl_movimentacao_estoque
        INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_mov_estoque_local   
        WHERE tbl_mov_estoque_codigo_id_animal='$codigo_id' AND 
              tbl_mov_estoque_entrada_saida='E' AND 
              (tbl_mov_estoque_tipo_movimentacao='N' || tbl_mov_estoque_tipo_movimentacao='C')"; 

    $rs = mysqli_query($conector, $sql); 
    $num_rows = mysqli_num_rows($rs);

    if ($num_rows!=0) {
        $reg_estoque = mysqli_fetch_object($rs);
        $desc_origem = $reg_estoque->tbl_pessoa_nome;
    }
    else {
        $desc_origem = '';
    }

    if ($primeiro_peso!=0 || $primeiro_peso!=null) {
        echo "<tr>";
        echo "<td width='8%'>".$data_edi."</td>";
        echo "<td width='25%'>Nascimento</td>";
        echo "<td width='25%'>".$desc_origem."</td>";
        echo "<td width='25%'>".$primeiro_peso." Kg</td>";
        echo "<td width='17%'></td>";
        echo "</tr>";
    }

    mysqli_close($conector);
            
    echo '</tbody>';
    echo '</table>';
    echo '</section>';
?>

                
                
