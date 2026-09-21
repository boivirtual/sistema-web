<?php
    include "conecta_mysql.inc";

    @ session_start();

?>

  <?php
	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_modalidade_produto">';

            echo '<tbody>';
                $sql = "SELECT * FROM tbl_modalidade_produto WHERE tbl_modalidade_lixeira=0 ORDER BY tbl_codigo_modalidade";
                $rs = mysqli_query($conector, $sql);

                while($reg_modalidade = mysqli_fetch_object($rs)){
                    $id = $reg_modalidade->tbl_codigo_modalidade;
                    $descricao = $reg_modalidade->tbl_descricao_modalidade;
                    $lixeira = $reg_modalidade->tbl_modalidade_lixeira;

                    $incluido_em = new DateTime($reg_modalidade->tbl_modalidade_incluido_em);
                    $incluido_por = $reg_modalidade->tbl_modalidade_incluido_por;
                    $alterado_em = ($reg_modalidade->tbl_modalidade_alterado_em != null) ? new DateTime($reg_modalidade->tbl_modalidade_alterado_em) : null;
                    $alterado_por = $reg_modalidade->tbl_modalidade_alterado_por;
                    $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
                    $alterado_em_edi = ($alterado_em != null) ? $alterado_em->format('d/m/Y H:i:s') : '';

                    $array_conta = array(
                        $id,
                        $descricao,
                        $incluido_em_edi,
                        $incluido_por,
                        $alterado_em_edi,
                        $alterado_por
                    );

                    $string_array = implode('|', $array_conta);

                    echo "<tr>";
                    echo "<td width='45%'>".$descricao."</td>";
                    echo "<td width='10%'>";
                    echo "<div class='btn-group'>";
                    echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_modalidade_produto(\"{$string_array}\");' ></i></a>";
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
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

    echo '<script src="js/tabela_modalidade_produto.js" charset="utf-8" type="text/javascript" ></script>';

?>
