<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";

    $codigo_id = $_POST['codigo_id'];

	echo '<section class="panel">';
    echo '<table class="table table-hover" style="font-size: 10px" id="tabela_partos">';
                          
    echo '<tbody>';

    $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
        where tbl_mov_estoque_codigo_mae='$codigo_id' and 
              tbl_mov_estoque_codigo_id_animal=999999999 and 
              tbl_mov_estoque_entrada_saida='E' and 
              tbl_mov_estoque_tipo_movimentacao='N'");

    $num_natimorto = mysqli_num_rows($tbl_natimorto);

    if ($num_natimorto!=0) {
        while ($reg_natimorto = mysqli_fetch_object($tbl_natimorto)) {
            $codigo_fazenda = $reg_natimorto->tbl_mov_estoque_local;
            $codigo_edi = 'Natimorto';
            $data = new DateTime($reg_natimorto->tbl_mov_estoque_nascimento); 
            $data_edi = $data->format('d/m/Y');
            $data_nascimento = $reg_natimorto->tbl_mov_estoque_nascimento; 
            $sexo = $reg_natimorto->tbl_mov_estoque_sexo; 

            if ($sexo == 'N') {
                $sexo = 'Não identificado';
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
            echo "<td width='10%'>".$codigo_edi."</td>";
            echo "<td width='8%'>".$data_edi."</td>";
            echo "<td width='20%'>".$desc_categoria."</td>";
            echo "<td width='5%'>".$sexo."</td>";
            echo "<td width='15%'>".$descricao_pai."</td>";
            echo "<td width='42%'>".$desc_fazenda."</td>";
            echo "</tr>";
        }
    }
          
    $sql = "select * from tbl_animais 
               inner join tabela_racas
                       on tab_codigo_raca = tbl_animal_codigo_raca   
                    where tbl_animal_codigo_mae='$codigo_id'
                    order by tbl_animal_data_nascimento DESC"; 
    $rs = mysqli_query($conector, $sql); 

    while ($reg_animal = mysqli_fetch_object($rs)){
        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
        $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
        $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
        $data_edi = $data->format('d/m/Y');
        $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
        $sexo = $reg_animal->tbl_animal_sexo; 
        $raca = $reg_animal->tab_descricao_raca;
        $ativo = $reg_animal->tbl_animal_ativo;;
        $pai = $reg_animal->tbl_animal_codigo_pai; 
        $estacao_nascido = $reg_animal->tbl_animal_estacao_monta_nascimento; 

        if ($codigo_alfa=='') {
            $codigo_edi = intval($codigo_numerico);
        }
        else {
            $codigo_edi = $codigo_alfa . '-' . intval($codigo_numerico);
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

        $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
        $num_rows_pai = mysqli_num_rows($tab_pai);

        if ($num_rows_pai!=0){
            $reg = mysqli_fetch_object($tab_pai);
            $descricao_pai = $reg->tbl_semem_nome;
        }
        else {
            $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
            }
            else {
                $descricao_pai = '';
            }
        }

        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");
        $num_rows = mysqli_num_rows($categoria);    

        if ($num_rows!=0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $idade_de = $reg_categoria->tab_categoria_idade_de;
                $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                if ($idade >= $idade_de && $idade <= $idade_ate) {
                    if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                        $desc_categoria=' > 36 meses';
                    }
                    else {
                        $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
                    }
                }
            }
        }                   

        $estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
            WHERE tbl_par_estacao_id='$estacao_nascido'");
        $num_rows = mysqli_num_rows($estacao);    

        if ($num_rows!=0) {
            $reg_estacao = mysqli_fetch_object($estacao);
            $desc_estacao = $reg_estacao->tbl_par_estacao_nome;
        }
        else {
            $desc_estacao = '';
        }

        echo "<tr>";
        if ($ativo=='S') {
            echo "<td width='8%' hidden>".$data_nascimento."</td>";
            echo "<td width='10%'>".$codigo_edi."</td>";
            echo "<td width='8%'>".$data_edi."</td>";
            echo "<td width='20%'>".$desc_categoria."</td>";
            echo "<td width='5%'>".$sexo."</td>";
            echo "<td width='10%'>".$descricao_pai."</td>";
            echo "<td width='32%'>".$desc_fazenda."</td>";
            echo "<td width='15%'>".$desc_estacao."</td>";
        }
        else {
            echo "<td width='8%' hidden>".$data_nascimento."</td>";
            echo "<td width='10%' style='color: red;'>".$codigo_edi."</td>";
            echo "<td width='8%' style='color: red;'>".$data_edi."</td>";
            echo "<td width='20%' style='color: red;'>".$desc_categoria."</td>";
            echo "<td width='5%' style='color: red;'>".$sexo."</td>";
            echo "<td width='10%' style='color: red;'>".$descricao_pai."</td>";
            echo "<td width='32%' style='color: red;'>".$desc_fazenda."</td>";
            echo "<td width='15%' style='color: red;'>".$desc_estacao."</td>";
        }
        echo "</tr>";
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
        <th hidden></th>
        </tr>
        </thead>';

    echo '</table>';

    echo '</section>';
?>

                
                
