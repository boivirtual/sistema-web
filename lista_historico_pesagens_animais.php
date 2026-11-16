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
        $peso = intval($reg_ite_peso->tbl_ite_pesagem_peso); 

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
        echo "<td width='35%'>".$desc_origem."</td>";
        echo "<td width='25%'>".$peso." Kg</td>";
        echo "<td width='7%'></td>";
        echo "</tr>";
    } 

    $sql = "SELECT * FROM tbl_animais 
        WHERE tbl_animal_codigo_id='$codigo_id'"; 

    $rs = mysqli_query($conector, $sql); 

    $reg_animal = mysqli_fetch_object($rs);
    $codigo_compra = $reg_animal->tbl_animal_movimentacao_compra;
    $data_compra = $reg_animal->tbl_animal_data_compra;
    $data_nascimento = $reg_animal->tbl_animal_data_nascimento;

    $primeiro_peso = intval($reg_animal->tbl_animal_primeiro_peso);
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
        echo "<td width='35%'>".$desc_origem."</td>";
        echo "<td width='25%'>".$primeiro_peso." Kg</td>";
        echo "<td width='7%'></td>";
        echo "</tr>";
    }

    if ($codigo_compra != '' && $codigo_compra != 0) {
        $nascimento = new DateTime($data_nascimento);
        $referencia  = new DateTime($data_compra);

        $intervalo = $nascimento->diff($referencia);
        $meses = ($intervalo->y * 12) + $intervalo->m;
        $meses_arredondado = $meses;

        if ($intervalo->d > 0) {
            $meses_arredondado++;
        }

        $tbl_item_movimentacao = mysqli_query($conector, "
            SELECT * 
            FROM tbl_item_movimentacao 
            INNER JOIN tbl_movimentacao
                ON tbl_movimentacao_id = tbl_ite_movimentacao_numero_id 
            INNER JOIN tbl_pessoa
                    ON tbl_movimentacao_codigo_local_origem = tbl_pessoa_id
            WHERE tbl_movimentacao_id = '$codigo_compra'
              AND tbl_movimentacao_lixeira = 0
              AND tbl_ite_idade_meses_compra IN ($meses, $meses_arredondado)
        "); 

        if ($tbl_item_movimentacao && mysqli_num_rows($tbl_item_movimentacao) > 0) {
            while ($reg_ite_mov = mysqli_fetch_object($tbl_item_movimentacao)) {
                $data_compra_edi = $referencia->format('d/m/Y');
                $peso_compra = intval($reg_ite_mov->tbl_ite_movimentacao_peso);

                echo "<tr>";
                echo "<td width='8%'>".$data_compra_edi."</td>";
                echo "<td width='25%'>Compra</td>";
                echo "<td width='35%'>".$reg_ite_mov->tbl_pessoa_nome."</td>";
                echo "<td width='25%'>".$peso_compra." Kg</td>";
                echo "<td width='7%'></td>";
                echo "</tr>";
                break;
            }
        }
    }

    mysqli_close($conector);
            
    echo '</tbody>';
    echo '</table>';
    echo '</section>';
?>

                
                
