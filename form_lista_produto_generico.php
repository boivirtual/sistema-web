<?php
    include "conecta_mysql.inc";

    @ session_start();

?>

  <?php
	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_produto_generico">';

            echo '<tbody>';
                $sql = "SELECT tabela_produto_generico.*, tbl_modalidade_produto.tbl_descricao_modalidade
                        FROM tabela_produto_generico
                        LEFT JOIN tbl_modalidade_produto
                           ON tbl_modalidade_produto.tbl_codigo_modalidade = tabela_produto_generico.pro_codigo_modalidade
                        WHERE tabela_produto_generico.pro_generico_registro_lixeira=0
                        ORDER BY tabela_produto_generico.pro_generico_descricao";
                $rs = mysqli_query($conector, $sql);

                while($reg_produto_generico = mysqli_fetch_object($rs)){
                    $id = $reg_produto_generico->pro_generico_codigo;
                    $descricao = $reg_produto_generico->pro_generico_descricao;
                    $modalidade = $reg_produto_generico->pro_codigo_modalidade;
                    $descricao_modalidade = $reg_produto_generico->tbl_descricao_modalidade;
                    $lixeira = $reg_produto_generico->pro_generico_registro_lixeira;

                    $incluido_em = new DateTime($reg_produto_generico->pro_generico_incluido_em);
                    $incluido_por = $reg_produto_generico->pro_generico_incluido_por;
                    $alterado_em = ($reg_produto_generico->pro_generico_alterado_em != null) ? new DateTime($reg_produto_generico->pro_generico_alterado_em) : null;
                    $alterado_por = $reg_produto_generico->pro_generico_alterado_por;
                    $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
                    $alterado_em_edi = ($alterado_em != null) ? $alterado_em->format('d/m/Y H:i:s') : '';

                    $array_conta = array(
                        $id,
                        $descricao,
                        $modalidade,
                        $incluido_em_edi,
                        $incluido_por,
                        $alterado_em_edi,
                        $alterado_por
                    );

                    $string_array = implode('|', $array_conta);

                    echo "<tr>";
                    echo "<td width='40%'>".$descricao."</td>";
                    echo "<td width='35%'>".$descricao_modalidade."</td>";
                    echo "<td width='10%'>";
                    echo "<div class='btn-group'>";
                    echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_produto_generico(\"{$string_array}\");' ></i></a>";
                    echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2);' ></i></a>";
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                }
                mysqli_close($conector);

            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Descrição</th>
                    <th> Modalidade</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

    echo '<script src="js/tabela_produto_generico.js" charset="utf-8" type="text/javascript" ></script>';

?>
