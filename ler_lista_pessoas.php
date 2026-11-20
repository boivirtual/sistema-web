<?php
    // Lista do Cadastro de Pessoas (form_cliente_fornecedor.php), carregada pelo botão Consultar.
    // Filtros (múltiplos, vazios = Todas):
    //   classe  -> códigos de tabela_tipo_pessoas separados por vírgula
    //   fazenda -> tbl_pessoa_id das fazendas (classe 04); só vale para os Funcionários (classe 05)
    @ session_start();
    include "conecta_mysql.inc";

    function so_codigos($lista) {
        $codigos = array();
        foreach (explode(',', (string)$lista) as $valor) {
            $valor = preg_replace('/[^0-9]/', '', $valor);
            if ($valor !== '') {
                $codigos[] = (int)$valor;
            }
        }
        return array_unique($codigos);
    }

    $classes = so_codigos(isset($_POST['classe']) ? $_POST['classe'] : '');
    $fazendas = so_codigos(isset($_POST['fazenda']) ? $_POST['fazenda'] : '');

    // Guarda o filtro para remontar a consulta ao voltar da edição/inclusão.
    $_SESSION['pessoas_filtro_classe'] = implode(',', $classes);
    $_SESSION['pessoas_filtro_fazenda'] = implode(',', $fazendas);
    $_SESSION['pessoas_consultou'] = 1;

    $where = "";
    if (count($classes) > 0) {
        $where .= " AND tbl_pessoa_classe IN (" . implode(',', $classes) . ")";

        // A Fazenda só existe para Funcionário: as outras classes escolhidas não são afetadas.
        if (in_array(5, $classes) && count($fazendas) > 0) {
            $where .= " AND (tbl_pessoa_classe <> 5 OR tbl_pessoa_id_fazenda IN (" . implode(',', $fazendas) . "))";
        }
    }

    $ssql = "SELECT tbl_pessoa_id, tbl_pessoa_nome, tbl_pessoa_contato, tbl_pessoa_email,
                    tbl_pessoa_ddd, tbl_pessoa_telefone, tbl_pessoa_lixeira,
                    tab_descricao_tipo_pessoa
               FROM tbl_pessoa
              INNER JOIN tabela_tipo_pessoas ON tbl_pessoa_classe = tab_codigo_tipo_pessoa
              WHERE 1=1" . $where;
    $rs = mysqli_query($conector, $ssql);
?>
<section class="panel">
<table class="table table-striped table-advance table-hover" id="tabela_clientes" width="100%">
    <thead>
        <tr>
            <th> Razão Social/Nome</th>
            <th> Classe</th>
            <th> Contato</th>
            <th> Telefone</th>
            <th> Email</th>
            <th> <i class="icon_cogs"></i> Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php
        while ($rs && $registro_cliente = mysqli_fetch_object($rs)) {
            if (strlen($registro_cliente->tbl_pessoa_telefone)==9) {
                $telefone = '(' . $registro_cliente->tbl_pessoa_ddd . ') ' .
                substr($registro_cliente->tbl_pessoa_telefone, 0, 5) . '-' .
                substr($registro_cliente->tbl_pessoa_telefone, 5, 4);
            }
            else {
                $telefone = '(' . $registro_cliente->tbl_pessoa_ddd . ') ' .
                substr($registro_cliente->tbl_pessoa_telefone, 0, 4) . '-' .
                substr($registro_cliente->tbl_pessoa_telefone, 4, 4);
            }

            $codigo = $registro_cliente->tbl_pessoa_id;
            $descricao_classe = $registro_cliente->tab_descricao_tipo_pessoa;
            $razao = $registro_cliente->tbl_pessoa_nome;
            $contato = $registro_cliente->tbl_pessoa_contato;
            $email = $registro_cliente->tbl_pessoa_email;
            $lixeira = $registro_cliente->tbl_pessoa_lixeira;

            echo "<tr>";

            if ($lixeira==1){
                echo "<td width='30%' style='color:#ccc'>".$razao."</td>";
                echo "<td width='10%' style='color:#ccc'>".$descricao_classe."</td>";
                echo "<td width='10%' style='color:#ccc'>".$contato."</td>";
                echo "<td width='15%' style='color:#ccc'>".$telefone."</td>";
                echo "<td width='15%' style='color:#ccc'>".$email."</td>";
                echo "<td width='10%'>";
                echo "<div class='btn-group'>";
                echo "<a class='btn' href='#'><i class='icon_refresh' data-toggle='tooltip' data-placement='left'  title='Restaurar esse registro da lixeira' onClick='enviar_lixeira(\"{$codigo}\",2)' ></i></a>";
                echo "</div>";
                echo "</td>";
            }
            else {
                echo "<td width='30%'>".$razao."</td>";
                echo "<td width='10%'>".$descricao_classe."</td>";
                echo "<td width='10%'>".$contato."</td>";
                echo "<td width='15%'>".$telefone."</td>";
                echo "<td width='15%'>".$email."</td>";

                echo "<td width='10%'>";
                echo "<div class='btn-group'>";
                echo "<a class='btn' href='form_cliente_fornecedor_editar.php?id=".$codigo."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                echo "<a class='btn' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$codigo}\",0)' ></i></a>";
                echo "</div>";
                echo "</td>";
            }

            echo "</tr>";
        }
        mysqli_close($conector);
    ?>
    </tbody>
</table>
</section>
