<?php
    include "conecta_mysql.inc";

    $codigo_id = $_POST['codigo_id'];
    $codigo_compra = $_POST['codigo_compra'];
    $data_nascimento = $_POST['data_nascimento'];
    $data_compra = $_POST['data_compra'];

    if ($data_compra) {
        $data_compra = DateTime::createFromFormat('d/m/Y', $data_compra)->format('Y-m-d');
    }

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
        echo "<td width='35%'>".$desc_origem."</td>";
        echo "<td width='35%'>".$desc_destino."</td>";
        echo "<td width='14%'></td>";
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
                $local_destino = intval($reg_ite_mov->tbl_movimentacao_codigo_local_destino);

                $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$local_destino'");
                
                $num_rows = mysqli_num_rows($tab_fazenda);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_fazenda);
                    $desc_local_destino= $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_local_destino = '';
                }

                echo "<tr>";
                echo "<td width='8%'>".$data_compra_edi."</td>";
                echo "<td width='8%'>Compra</td>";
                echo "<td width='35%'>".$reg_ite_mov->tbl_pessoa_nome."</td>";
                echo "<td width='35%'>".$desc_local_destino."</td>";
                echo "<td width='14%'></td>";
                echo "</tr>";
                break;
            }
        }
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

                
                
