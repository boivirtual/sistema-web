<?php
    include "conecta_mysql.inc";

    // 1. Coleta e sanitização de dados do POST
    $codigo_alfa_numerico = $_POST["codigo_alfa_numerico"] ?? '';
    $local_filtro = $_POST['local'] ?? null;
    $sexo_filtro = $_POST['sexo'] ?? [];
    $ativo_filtro = $_POST['ativo'] ?? 'S';
    $situacao_vendido = $_POST['situacao_vendido'] ?? 'N';
    $situacao_morte = $_POST['situacao_morte'] ?? 'N';
    $situacao_outra = $_POST['situacao_outra'] ?? 'N';
    $categoria_filtro = $_POST['categoria'] ?? [];
    
    // 2. Pré-carregue dados de tabelas relacionadas (menos consultas)
    $racas = [];
    $rs_racas = mysqli_query($conector, "SELECT * FROM tabela_racas");
    while ($reg = mysqli_fetch_object($rs_racas)) {
        $racas[$reg->tab_codigo_raca] = $reg->tab_descricao_raca;
    }
    
    $fazendas = [];
    $rs_fazendas = mysqli_query($conector, "SELECT * FROM tbl_pessoa");
    while ($reg = mysqli_fetch_object($rs_fazendas)) {
        $fazendas[$reg->tbl_pessoa_id] = $reg->tbl_pessoa_nome;
    }

    $semens = [];
    $rs_semens = mysqli_query($conector, "SELECT * FROM tbl_semem");
    while ($reg = mysqli_fetch_object($rs_semens)) {
        $semens[$reg->tbl_semem_codigo_id] = $reg->tbl_semem_nome;
    }

    // 3. Constrói a consulta SQL de forma eficiente
    $where_clauses = ["tbl_animal_lixeira=0"];

    if ($codigo_alfa_numerico != '') {
        $codigo_numerico_consulta = substr($codigo_alfa_numerico, -9);
        $codigo_alfa_consulta = (strlen($codigo_alfa_numerico) != 9) ? explode("-", $codigo_alfa_numerico)[0] : '';
        $where_clauses[] = "tbl_animal_codigo_alfa='$codigo_alfa_consulta'";
        $where_clauses[] = "tbl_animal_codigo_numerico='$codigo_numerico_consulta'";
    } else {
        // Filtro de Local
        if (!in_array("", $local_filtro) && !empty($local_filtro)) {
            $locais_str = "'" . implode("','", $local_filtro) . "'";
            if ($situacao_vendido == 'S') {
                $where_clauses[] = "(tbl_animal_codigo_fazenda IN($locais_str) OR (tbl_animal_codigo_origem IN($locais_str) AND tbl_animal_situacao='V'))";
            } else {
                $where_clauses[] = "tbl_animal_codigo_fazenda IN($locais_str)";
            }
        }        

        // Filtro de Sexo
        if (is_array($sexo_filtro) && !in_array("Todos", $sexo_filtro)) {
            $sexo_str = "'" . implode("','", $sexo_filtro) . "'";
            $where_clauses[] = "tbl_animal_sexo IN($sexo_str)";
        }
        
        // Filtro de Ativo
        if ($ativo_filtro != 'Todos') {
            $where_clauses[] = "tbl_animal_ativo='$ativo_filtro'";
        }
        
        // Filtro de Situação
        $situacoes = [];
        if ($situacao_vendido == 'S') $situacoes[] = 'V';
        if ($situacao_morte == 'S') $situacoes[] = 'M';
        if ($situacao_outra == 'S') $situacoes[] = 'O';
        
        if (!empty($situacoes)) {
            $situacoes_str = "'" . implode("','", $situacoes) . "'";
            $where_clauses[] = "tbl_animal_situacao IN($situacoes_str)";
        }
    }
    
    $sql = "SELECT * FROM tbl_animais WHERE " . implode(" AND ", $where_clauses) . " ORDER BY tbl_animal_codigo_numerico ASC";
    
    $rs = mysqli_query($conector, $sql);
    
    print_r($sql);
    
    $animais_listados = 0;
    $animais = [];
    
    while ($reg_animal = mysqli_fetch_object($rs)) {
        // Cálculo de idade e categoria
        $data_nascimento = new DateTime($reg_animal->tbl_animal_data_nascimento);
        $data_acompanhamento_calculo = ($reg_animal->tbl_animal_baixado_em != '') ? new DateTime($reg_animal->tbl_animal_baixado_em) : new DateTime();
        $idade_acompanhamento = $data_nascimento->diff($data_acompanhamento_calculo);
        $idade_meses = ($idade_acompanhamento->format('%Y') * 12) + $idade_acompanhamento->format('%m');
    
        $desc_categoria = '';
        $codigo_categoria = '';
        // Categoria de idade
        if ($idade_meses >= 0 && $idade_meses <= 12) {
            $desc_categoria = '0 a 12 meses';
            $codigo_categoria = '1';
        } elseif ($idade_meses > 12 && $idade_meses <= 24) {
            $desc_categoria = '13 a 24 meses';
            $codigo_categoria = '2';
        } elseif ($idade_meses > 24 && $idade_meses <= 36) {
            $desc_categoria = '25 a 36 meses';
            $codigo_categoria = '3';
        } elseif ($idade_meses > 36) {
            $desc_categoria = ' > 36 meses';
            $codigo_categoria = '4';
        }
    
        // Filtragem por categoria após o cálculo
        if (!in_array("", $categoria_filtro) && !in_array($codigo_categoria, $categoria_filtro)) {
            continue; // Pula para o próximo registro se não corresponder à categoria
        }

        $desc_situacao = '';
        switch ($reg_animal->tbl_animal_situacao) {
            case 'V':
                $desc_situacao = 'Vendido';
                break;
            case 'M':
                $desc_situacao = 'Morte';
                break;
            case 'T':
                $desc_situacao = 'Aguardando Transf';
                break;
            case 'S':
                $desc_situacao = 'Outra Saída';
                break;
            default:
                $desc_situacao = '';
                break;
        }
        
        $animal = [
            'codigoAlfa' => $reg_animal->tbl_animal_codigo_alfa,
            'codigoNumerico' => intval($reg_animal->tbl_animal_codigo_numerico),
            'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
            'descricaoRaca' => $racas[$reg_animal->tbl_animal_codigo_raca] ?? '',
            'descricaoCategoria' => $desc_categoria,
            'sexo' => $reg_animal->tbl_animal_sexo,
            'nomeFazenda' => $fazendas[$reg_animal->tbl_animal_codigo_fazenda] ?? '',
            'codigoMaeAlfaNumerico' => ($reg_animal->tbl_animal_codigo_mae > 0) ? (
                ($mae = mysqli_fetch_object(mysqli_query($conector, "SELECT tbl_animal_codigo_alfa, tbl_animal_codigo_numerico FROM tbl_animais WHERE tbl_animal_codigo_id='{$reg_animal->tbl_animal_codigo_mae}'"))) ? $mae->tbl_animal_codigo_alfa . ' ' . $mae->tbl_animal_codigo_numerico : ''
            ) : '',
            'codigoPaiAlfaNumerico' => ($reg_animal->tbl_animal_codigo_pai > 0) ? ($semens[$reg_animal->tbl_animal_codigo_pai] ?? (
                ($pai = mysqli_fetch_object(mysqli_query($conector, "SELECT tbl_animal_codigo_alfa, tbl_animal_codigo_numerico FROM tbl_animais WHERE tbl_animal_codigo_id='{$reg_animal->tbl_animal_codigo_pai}'"))) ? $pai->tbl_animal_codigo_alfa . ' ' . $pai->tbl_animal_codigo_numerico : ''
            )) : '',

            'descSituacao' => $desc_situacao,            
            'ativo' => $reg_animal->tbl_animal_ativo,
            'observacao' => $reg_animal->tbl_animal_observacao,
            'situacao' => $reg_animal->tbl_animal_situacao
        ];

        // Adicione outras propriedades necessárias para o JSON
        $animal['codigoAnimal'] = $reg_animal->tbl_animal_codigo_id;
        $animal['codigoLocal'] = $reg_animal->tbl_animal_codigo_fazenda;
        $animal['codigoRaca'] = $reg_animal->tbl_animal_codigo_raca;
        $animal['codigoPelagem'] = $reg_animal->tbl_animal_codigo_pelagem != '' && $reg_animal->tbl_animal_codigo_pelagem != 999 ? $reg_animal->tbl_animal_codigo_pelagem : '000';
        $animal['codigoMae'] = $reg_animal->tbl_animal_codigo_mae;
        $animal['codigoPai'] = $reg_animal->tbl_animal_codigo_pai;
        $animal['idadeAnimal'] = $idade_acompanhamento->format('%Y') > 0 ? $idade_acompanhamento->format('%Y') . ' ano(s) ' : '';
        $animal['idadeAnimal'] .= $idade_acompanhamento->format('%m') > 0 ? $idade_acompanhamento->format('%m') . ' mes(es)' : ($idade_acompanhamento->format('%d') . ' dia(s)');
        $animal['descarte'] = $reg_animal->tbl_animal_descarte_reproducao;

        $animais[] = $animal;
        $animais_listados++;
    }

    // Geração do HTML (fora do loop)
    echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_animais" style="font-size: 13px">';
    echo '<tbody>';
    
    foreach ($animais as $animal) {
        $json_animal = json_encode($animal);
        $onclick_action = "editar_animal(JSON.parse(this.dataset.animal));";
        $onclick_action .= ($animal['sexo'] == 'F') ? "mostrar_reproducao();" : "esconder_reproducao();";
        
        $row_color = ($animal['ativo'] == "N") ? "style='color:#FF9393'" : "";
        
        echo "<tr {$row_color}>";
        echo "<td align='right' width='4%'>{$animal['codigoAlfa']}</td>";
        echo "<td width='10%'>{$animal['codigoNumerico']}</td>";
        echo "<td width='5%' " . ($animal['descarte'] == 'S' ? "style='color:red'" : "") . ">" . ($animal['descarte'] == 'S' ? 'Sim' : '') . "</td>";
        echo "<td width='10%'>{$animal['descricaoRaca']}</td>";
        echo "<td width='10%'>{$animal['descricaoCategoria']}</td>";
        echo "<td width='5%'>{$animal['sexo']}</td>";
        echo "<td width='16%'>{$animal['nomeFazenda']}</td>";
        echo "<td width='10%'>{$animal['codigoMaeAlfaNumerico']}</td>";
        echo "<td width='10%'>{$animal['codigoPaiAlfaNumerico']}</td>";
        echo "<td width='10%'>{$animal['descSituacao']}</td>";
        echo "<td width='10%'>";
        echo "<div class='btn-group'>";
        echo "<a class='btn' href='#' data-animal='" . htmlspecialchars($json_animal, ENT_QUOTES) . "' onClick=\"{$onclick_action}\">";
        echo "<i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro'></i>";
        echo "</a>";
        echo "</div>";
        echo '<input type="hidden" id="obs" value="'.htmlspecialchars($animal['observacao'], ENT_QUOTES).'" />';
        echo "</td>";
        echo "</tr>";
    }

    mysqli_close($conector);
    
    echo '</tbody>';
    echo '<thead>';
    echo '
        <tr>
        <th colspan="11" style="text-align:left; border-top: none;">Total de Registros Encontrados:&nbsp;&nbsp;'.$animais_listados.'</th>
        </tr>
    
        <tr>
        <th> <i class="fa fa-sort-alpha-asc"></i></th>
        <th style="text-align: center;">Nº Animal</th>
        <th>Descarte</th>
        <th>Raça</th>
        <th>Categoria</th>
        <th>Sexo</th>
        <th>Local</th>
        <th>Mãe</th>
        <th>Pai</th>
        <th>Situação</th>
        <th><i class="icon_cogs"></i>Ações</th>
        </tr>';
    echo '</thead>';
    echo '</table>';
    echo '</section>';
?>

<script src="js/tabela_animais.js" charset="utf-8" type="text/javascript" ></script>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>