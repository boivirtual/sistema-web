<?php
    include "conecta_mysql.inc";

    $codigo_id = $_POST['codigo_id'];

	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" style="font-size: 11px">';
                          
    echo '<tbody>';
          
    $sql = "select * from tbl_item_movimentacao 
               inner join tbl_movimentacao
                       on tbl_movimentacao_id = tbl_ite_movimentacao_numero_id 
                    where tbl_ite_movimentacao_codigo_id_animal='$codigo_id'
                    order by tbl_movimentacao_id DESC"; 
    $rs = mysqli_query($conector, $sql); 

    while ($reg_ite_mov = mysqli_fetch_object($rs)){
        $codigo_animal = $reg_ite_mov->tbl_ite_movimentacao_codigo_animal;
        $data = new DateTime($reg_ite_mov->tbl_ite_movimentacao_data_emissao); 
        $data_edi = $data->format('d/m/Y');
        $tipo = $reg_ite_mov->tbl_movimentacao_tipo; 
        $origem = $reg_ite_mov->tbl_movimentacao_codigo_local_origem; 
        $destino = $reg_ite_mov->tbl_movimentacao_codigo_local_destino; 

        switch ($tipo) {
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
            default:
                $descricao_tipo = 'Outras Saídas';
                break;
        }

        $tab_origem = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$origem'");
        $num_rows = mysqli_num_rows($tab_origem);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_origem);
            $desc_origem = $reg->tbl_pessoa_nome;
        }
        else {
            $desc_origem = '';
        }

        $tab_destino = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$destino'");
        $num_rows = mysqli_num_rows($tab_destino);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_destino);
            $desc_destino = $reg->tbl_pessoa_nome;
        }
        else {
            $desc_destino= '';
        }

        echo "<tr>";
        echo "<td width='8%'>".$data_edi."</td>";
        echo "<td width='8%'>".$descricao_tipo."</td>";
        echo "<td width='25%'>".$desc_origem."</td>";
        echo "<td width='35%'>".$desc_destino."</td>";
        echo "<td width='24%'></td>";
        echo "</tr>";
    } 
    mysqli_close($conector);
            
    echo '</tbody>';
    echo '<thead>
        <tr">
            <th class="sem_borda_top"> Data</th>
            <th class="sem_borda_top"> Movimentação</th>
            <th class="sem_borda_top"> Origem</th>
            <th class="sem_borda_top"> Destino</th>
            <th class="sem_borda_top"></th>
        </tr>
        </thead>';
   echo '</table>';

   echo '</section>';
?>

                
                
