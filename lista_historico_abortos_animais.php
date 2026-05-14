<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";

    $codigo_id = $_POST['codigo_id'];

	echo '<section class="panel">';
    echo '<table class="table table-hover" style="font-size: 10px" id="tabela_abortos">';
                          
    echo '<tbody>';

    $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
        where tbl_mov_estoque_codigo_mae='$codigo_id' and 
              tbl_mov_estoque_codigo_id_animal=999999999 and 
              tbl_mov_estoque_entrada_saida='A'");

    $num_natimorto = mysqli_num_rows($tbl_natimorto);

    if ($num_natimorto!=0) {
        while ($reg_natimorto = mysqli_fetch_object($tbl_natimorto)) {
            $codigo_fazenda = $reg_natimorto->tbl_mov_estoque_local;
            $data = new DateTime($reg_natimorto->tbl_mov_estoque_nascimento); 
            $data_edi = $data->format('d/m/Y');
            $data_nascimento = $reg_natimorto->tbl_mov_estoque_nascimento; 
            $sexo = $reg_natimorto->tbl_mov_estoque_sexo; 
            $tipo_ocorrencia = $reg_natimorto->tbl_mov_estoque_tipo_movimentacao; 

            if ($sexo == 'N') {
                $sexo = 'Sexo não identificado';
            }

            if ($tipo_ocorrencia=='A') {
                $codigo_edi = 'Aborto';
            }
            else {
                $codigo_edi = 'Absorção';
            }

            $tab_origem = mysqli_query($conector, "select * from tbl_pessoa 
                where tbl_pessoa_id='$codigo_fazenda'");
            $num_rows = mysqli_num_rows($tab_origem);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_origem);
                $desc_fazenda = $reg->tbl_pessoa_nome;
            }
            else {
                $desc_fazenda = '';
            }

            $desc_categoria = '';
            $descricao_pai = '';

            echo "<tr>";
            echo "<td width='8%' hidden>".$data_nascimento."</td>";
            echo "<td width='8%'>".$codigo_edi."</td>";
            echo "<td width='8%'>".$data_edi."</td>";
            echo "<td width='20%'>".$desc_categoria."</td>";
            echo "<td width='5%'>".$sexo."</td>";
            echo "<td width='15%'>".$descricao_pai."</td>";
            echo "<td width='44%'>".$desc_fazenda."</td>";
            echo "</tr>";
        }
    }
          
    mysqli_close($conector);
            
    echo '</tbody>';

    echo ' <tr>
        <th hidden></th>
        <th hidden></th>
        <th hidden></th>
        <th hidden></th>
        <th hidden></th>
        <th hidden></th>
        <th hidden></th>
        </tr>
        </thead>';

    echo '</table>';

    echo '</section>';
?>

                
                
