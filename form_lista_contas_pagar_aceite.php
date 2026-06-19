<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_inicial = $_REQUEST["data_inicial"];
    $data_final   = $_REQUEST["data_final"];
    $tipo_data    = $_REQUEST["tipo_data"];
    $array_fornecedor = $_REQUEST["array_fornecedor"];
    $array_fazenda    = $_REQUEST["array_fazenda"];
    $limpa_filtros    = $_REQUEST["limpa_filtros"];
    $array_conta      = $_REQUEST["array_conta"];

    // Validação de datas
    $data_inicial = preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_inicial) ? $data_inicial : '';
    $data_final   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_final)   ? $data_final   : '';

    // Validação do tipo de data
    $tipo_data = in_array($tipo_data, ['V', 'E']) ? $tipo_data : 'V';

    // Monta filtro de contas — mantém lógica original (filtra por sub-conta) com cast para int
    $conta_ids = [];
    foreach (explode(",", $array_conta) as $item) {
        $item = trim($item);
        if ($item !== '' && substr($item, 3, 4) != 0) {
            $conta_ids[] = intval($item);
        }
    }
    $wconta = '';
    if ($array_conta !== '' && !empty($conta_ids)) {
        $wconta = " AND ctp_codigo_conta IN(" . implode(',', $conta_ids) . ")";
    }

    // Monta filtro de fornecedor
    $fornecedor_ids = [];
    foreach (explode(",", $array_fornecedor) as $item) {
        $id = intval(trim($item));
        if ($id > 0) $fornecedor_ids[] = $id;
    }
    $wfornecedor = '';
    if ($array_fornecedor !== '' && !empty($fornecedor_ids)) {
        $wfornecedor = " AND ctp_codigo_fornecedor IN(" . implode(',', $fornecedor_ids) . ")";
    }

    // Monta filtro de fazenda — inclui registros com rateio (ctp_codigo_fazenda IS NULL)
    $fazenda_ids = [];
    foreach (explode(",", $array_fazenda) as $item) {
        $id = intval(trim($item));
        if ($id > 0) $fazenda_ids[] = $id;
    }
    $wfazenda = '';
    if ($array_fazenda !== '' && !empty($fazenda_ids)) {
        $ids_str = implode(',', $fazenda_ids);
        $wfazenda = " AND (ctp_codigo_fazenda IN($ids_str) OR (ctp_codigo_fazenda IS NULL AND ctp_id IN (SELECT rc_ctp_id FROM tbl_ctp_rateio WHERE rc_codigo_local IN ($ids_str))))";
    }

    @ session_start();

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];

    $_SESSION['data_inicial_aceite']    = $data_inicial;
    $_SESSION['data_final_aceite']      = $data_final;
    $_SESSION['tipo_data_aceite']       = $tipo_data;
    $_SESSION['codigo_fornecedor_aceite'] = $array_fornecedor;
    $_SESSION['codigo_local_aceite']    = $array_fazenda;
    $_SESSION['codigo_conta_aceite']    = $array_conta;

    $select_fields = "SELECT cp.ctp_id, cp.ctp_data_emissao, cp.ctp_data_vencimento,
                             cp.ctp_codigo_banco, cp.ctp_numero_cheque,
                             cp.ctp_numero_doc, cp.ctp_numero_documento,
                             cp.ctp_parcela, cp.ctp_codigo_fornecedor, cp.ctp_nome_fornecedor,
                             cp.ctp_codigo_fazenda, cp.ctp_codigo_conta,
                             cp.ctp_descricao_compra, cp.ctp_situacao,
                             cp.ctp_valor_parcela, cp.ctp_valor_juros,
                             cp.ctp_valor_desconto, cp.ctp_outro_valor,
                             p.tbl_pessoa_nome, pc.tbl_plano_contas_descricao";
    $from_join = " FROM contas_pagar cp
                   LEFT JOIN tbl_pessoa p ON p.tbl_pessoa_id = cp.ctp_codigo_fazenda
                   LEFT JOIN tbl_plano_contas pc ON pc.tbl_plano_contas_codigo_id = cp.ctp_codigo_conta";
    $where_base = " WHERE (cp.ctp_aceite = '' OR cp.ctp_aceite IS NULL)";
    $filtros    = $wfornecedor . $wfazenda . $wconta;
    $order_venc = " ORDER BY CASE WHEN cp.ctp_numero_doc IS NULL OR TRIM(cp.ctp_numero_doc) = '' THEN 1 ELSE 0 END ASC,
                              CAST(NULLIF(TRIM(cp.ctp_numero_doc), '') AS UNSIGNED) ASC,
                              cp.ctp_codigo_fazenda ASC,
                              cp.ctp_id ASC,
                              cp.ctp_parcela ASC";
    $order_emis = " ORDER BY CASE WHEN cp.ctp_numero_doc IS NULL OR TRIM(cp.ctp_numero_doc) = '' THEN 1 ELSE 0 END ASC,
                              CAST(NULLIF(TRIM(cp.ctp_numero_doc), '') AS UNSIGNED) ASC,
                              cp.ctp_codigo_fazenda ASC,
                              cp.ctp_id ASC,
                              cp.ctp_parcela ASC";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>
  <style>
    #tabela_aceite_contas tr[id^="rateio-row-"] td { background: #f5f8ff; }
  </style>
</head>

<body>
	<section class="panel">
        <table class="table table-borderless table-hover" width="100%"
            style="font-size: 12px" id="tabela_aceite_contas">

        <tbody>
            <?php
                $chave_anterior = '';
                $total_periodo  = 0;

                if ($data_inicial != '' && $data_final != '' && $tipo_data == "V") {
                    $rs = mysqli_query($conector,
                        $select_fields . $from_join . $where_base .
                        " AND cp.ctp_data_vencimento >= '$data_inicial'
                          AND cp.ctp_data_vencimento <= '$data_final'" .
                        $filtros . $order_venc);

                } elseif ($data_inicial != '' && $data_final != '' && $tipo_data == "E") {
                    $rs = mysqli_query($conector,
                        $select_fields . $from_join . $where_base .
                        " AND cp.ctp_data_emissao >= '$data_inicial'
                          AND cp.ctp_data_emissao <= '$data_final'" .
                        $filtros . $order_emis);

                } else {
                    $rs = mysqli_query($conector,
                        $select_fields . $from_join . $where_base .
                        $filtros . $order_venc);
                }

                while ($fila = mysqli_fetch_object($rs)) {
                    $ctp_id          = $fila->ctp_id;
                    $data_emissao    = new DateTime($fila->ctp_data_emissao);
                    $data_vencimento = new DateTime($fila->ctp_data_vencimento);

                    if (empty($fila->ctp_numero_doc)) {
                        $numero_id = $fila->ctp_numero_documento;
                    } else {
                        $numero_id = $fila->ctp_numero_doc;
                    }

                    $parcela          = $fila->ctp_parcela;
                    $codigo_for       = $fila->ctp_codigo_fornecedor;
                    $nome_for         = $fila->ctp_nome_fornecedor;
                    $codigo_fazenda   = $fila->ctp_codigo_fazenda;
                    $codigo_conta     = $fila->ctp_codigo_conta;
                    $descricao_compra = $fila->ctp_descricao_compra;
                    $situacao         = $fila->ctp_situacao;
                    $vlr_parcela      = $fila->ctp_valor_parcela;
                    $vlr_juros        = $fila->ctp_valor_juros;
                    $vlr_desconto     = $fila->ctp_valor_desconto;
                    $vlr_outro        = $fila->ctp_outro_valor;
                    $total_parcela    = $vlr_parcela - $vlr_desconto + $vlr_juros + $vlr_outro;
                    $total_periodo   += $total_parcela;

                    // Detecta rateio novo: ctp_codigo_fazenda IS NULL
                    $tem_rateio = is_null($codigo_fazenda);

                    if ($tem_rateio) {
                        // Localiza o primeiro ctp_id do documento para acessar tbl_ctp_rateio
                        $num_doc_esc = mysqli_real_escape_string($conector, $numero_id);
                        $for_esc     = mysqli_real_escape_string($conector, $codigo_for);
                        $rs_prim = mysqli_query($conector,
                            "SELECT MIN(ctp_id) AS primeiro_id FROM contas_pagar
                             WHERE ctp_numero_doc = '$num_doc_esc'
                               AND ctp_codigo_fornecedor = '$for_esc'
                               AND ctp_codigo_fazenda IS NULL");
                        $row_prim     = mysqli_fetch_object($rs_prim);
                        $primeiro_ctp = $row_prim ? (int)$row_prim->primeiro_id : $ctp_id;

                        // Locais distintos para exibição na coluna Local
                        $rs_locais = mysqli_query($conector,
                            "SELECT rc_codigo_local, rc_nome_local FROM tbl_ctp_rateio
                             WHERE rc_ctp_id = '$primeiro_ctp'
                             GROUP BY rc_codigo_local, rc_nome_local
                             ORDER BY MIN(rc_id) ASC");
                        $total_locais = mysqli_num_rows($rs_locais);
                        $first_local  = mysqli_fetch_object($rs_locais);
                        $desc_fazenda_plain = $first_local ? $first_local->rc_nome_local : 'Rateio';
                        $desc_fazenda = htmlspecialchars($desc_fazenda_plain);
                        if ($total_locais > 1) {
                            $desc_fazenda .= ' <span style="color:#337ab7;font-weight:600">+' . ($total_locais - 1) . '</span>';
                        }

                        // Contas contábeis distintas para exibição na coluna Conta
                        $rs_contas = mysqli_query($conector,
                            "SELECT rc_codigo_conta, rc_nome_conta FROM tbl_ctp_rateio
                             WHERE rc_ctp_id = '$primeiro_ctp'
                               AND rc_nome_conta IS NOT NULL AND rc_nome_conta != ''
                             GROUP BY rc_codigo_conta, rc_nome_conta
                             ORDER BY MIN(rc_id) ASC");
                        $total_contas = mysqli_num_rows($rs_contas);
                        $first_conta  = mysqli_fetch_object($rs_contas);
                        $desc_conta_plain = $first_conta ? $first_conta->rc_nome_conta : 'Rateio';
                        $desc_conta = htmlspecialchars($desc_conta_plain);
                        if ($total_contas > 1) {
                            $desc_conta .= ' +' . ($total_contas - 1);
                        }

                    } else {
                        $desc_fazenda_plain = $fila->tbl_pessoa_nome;
                        $desc_fazenda       = htmlspecialchars($desc_fazenda_plain ?? '');
                        $rateio_html        = '';

                        // Lógica legada: ctp_codigo_conta IS NULL com fazenda preenchida
                        if (is_null($codigo_conta)) {
                            $num_doc_esc = mysqli_real_escape_string($conector, $numero_id);
                            $parcela_esc = mysqli_real_escape_string($conector, $parcela);
                            $fazenda_esc = mysqli_real_escape_string($conector, ltrim($codigo_fazenda, '0'));
                            $rs_rat = mysqli_query($conector,
                                "SELECT rc_nome_conta FROM tbl_ctp_rateio
                                 WHERE rc_codigo_local = '$fazenda_esc'
                                   AND rc_ctp_id IN (
                                       SELECT ctp_id FROM contas_pagar
                                       WHERE ctp_numero_doc = '$num_doc_esc'
                                         AND ctp_parcela    = '$parcela_esc'
                                         AND ctp_codigo_conta IS NULL
                                   )
                                 ORDER BY rc_id ASC");
                            $total_rat = mysqli_num_rows($rs_rat);
                            $first_rat = mysqli_fetch_object($rs_rat);
                            $desc_conta = $first_rat ? htmlspecialchars($first_rat->rc_nome_conta) : 'Rateio';
                            if ($total_rat > 1) $desc_conta .= ' +' . ($total_rat - 1);
                        } else {
                            $desc_conta = htmlspecialchars($fila->tbl_plano_contas_descricao ?? '');
                        }
                    }

                    if ($situacao == "P") {
                        $desc_situacao = "Paga";
                    } elseif ($situacao == "C") {
                        $desc_situacao = "Paga Parc";
                    } else {
                        $desc_situacao = "";
                    }

                    if (empty($numero_id)) {
                        $registro = 'SEM_DOC_' . $ctp_id;
                    } else {
                        $registro = $numero_id . $codigo_fazenda . $codigo_for . $codigo_conta . str_replace('-', '', $fila->ctp_data_emissao . $descricao_compra);
                    }

                    $vlr_display = number_format($total_parcela, 2, ",", ".");

                    if ($chave_anterior != $registro) {
                        // Ícone de expansão do rateio — só na 1ª linha do documento
                        $icon_rateio = '';
                        if ($tem_rateio) {
                            $icon_rateio = ' <button type="button" onclick="toggleRateio(' . $ctp_id . ')" title="Ver distribuição do rateio" style="background:none;border:none;padding:0 3px;cursor:pointer;color:#337ab7;font-size:13px;"><i class="fas fa-sitemap"></i></button>';
                        }

                        echo "<tr>";
                        echo "<td width='2%'><input type='checkbox' class='checkbox1' name='id_ctp' value='" . $ctp_id . "' onClick='somar_total_para_baixar()'></td>";
                        echo "<td width='10%'>" . $numero_id . "</td>";
                        echo "<td width='3%' align='center'>" . $parcela . "</td>";
                        echo "<td width='15%'>" . $nome_for . "</td>";
                        echo "<td width='15%'>" . $desc_fazenda . "</td>";
                        echo "<td width='10%'>" . $desc_conta . "</td>";
                        echo "<td width='6%'>" . $data_emissao->format('d/m/Y') . "</td>";
                        echo "<td width='6%'>" . $data_vencimento->format('d/m/Y') . "</td>";
                        echo "<td width='12%'>" . $vlr_display . $icon_rateio . "</td>";
                        echo "<td width='19%' style='font-size: 10px;'>" . $descricao_compra . "</td>";
                        echo "<td width='10%'>" . $desc_situacao . "</td>";
                        echo "</tr>";

                        // Guarda o HTML do rateio para renderizar fora da tabela (evita erro no DataTables)
                        if ($tem_rateio) {
                            $rateio_divs[$ctp_id] = $rateio_html;
                        }

                        $chave_anterior = $registro;
                    } else {
                        echo "<tr>";
                        echo "<td style='color:#fff;' width='2%'><input type='checkbox' class='checkbox1' name='id_ctp' value='" . $ctp_id . "' onClick='somar_total_para_baixar()'></td>";
                        echo "<td width='10%'>" . $numero_id . "</td>";
                        echo "<td width='3%' align='center'>" . $parcela . "</td>";
                        echo "<td style='color:#fff;' width='15%'>" . $nome_for . "</td>";
                        echo "<td style='color:#fff;' width='15%'>" . strip_tags($desc_fazenda) . "</td>";
                        echo "<td style='color:#fff;' width='10%'>" . strip_tags($desc_conta) . "</td>";
                        echo "<td style='color:#fff;' width='6%'>" . $data_emissao->format('d/m/Y') . "</td>";
                        echo "<td width='6%'>" . $data_vencimento->format('d/m/Y') . "</td>";
                        echo "<td width='12%'>" . $vlr_display . "</td>";
                        echo "<td></td>";
                        echo "<td width='10%'>" . $desc_situacao . "</td>";
                        echo "</tr>";
                    }
                }
            ?>
        </tbody>

        <thead>
            <tr>
                <div class="row col-md-12" id="total_contas">
                    <div class="col-md-2">
                        <label class="control-label">Total Geral</label>
                        <input class="form-control form-control-sm" type="text" readonly="" <?php echo "value='" . number_format($total_periodo, 2, ",", ".") . "'"; ?>>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label">Total Selecionados</label>
                        <input class="form-control form-control-sm" type="text" readonly="" id="total_selecionado">
                    </div>

                    <div class="col-md-2">
                        <label class="control-label">&nbsp;</label>
                        <p>
                        <a href="#" onclick="exibe_filtros_aceite()">
                            <i class="fas fa-filter"></i> + Filtros
                        </a>
                        </p>
                    </div>

                    <div class="col-md-2 limpar_filtros">
                        <label class="control-label">&nbsp;</label>
                        <p>
                        <a href="#" onclick="limpar_filtros_tela_inicial()">Limpar Filtros
                        </a>
                        </p>
                    </div>

                    <div class="col-md-6"></div>
                </div>
            </tr>

            <tr>
                <th><input type="checkbox" class='checkbox1' id="seleciona_todos_aceite"></th>
                <th>Documento</th>
                <th>Parcela</th>
                <th>Fornecedor</th>
                <th>Local</th>
                <th>Conta</th>
                <th>Emissão</th>
                <th>Vencimentos</th>
                <th>Valor</th>
                <th>Descrição</th>
                <th>Pgto</th>
            </tr>
        </thead>
        <tfoot>
        </tfoot>
        </table>

        <?php if (!empty($rateio_divs)): ?>
        <!-- Dados de rateio fora da tabela — usados pelo toggleRateio() para montar o modal -->
        <?php foreach ($rateio_divs as $rid => $rhtml): ?>
        <div id="rateio-data-<?php echo $rid; ?>" style="display:none;"><?php echo $rhtml; ?></div>
        <?php endforeach; ?>
        <?php endif; ?>

    </section>

    <script src="js/contas_pagar_aceite.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript"></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
    </script>

</body>
</html>
