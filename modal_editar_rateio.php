<?php
/*
 * modal_editar_rateio.php — Include reutilizável do Editor de Rateio
 *
 * Requisitos do arquivo que incluir este arquivo:
 *   - conecta_mysql.inc já carregado ($conector e $conector_acesso disponíveis)
 *   - Sessão iniciada ($_SESSION['id_usuario'] disponível)
 *   - Após o include, definir o callback pós-salvar em JS:
 *       _eratCallbackPosSalvar = function(id) { ... };
 *
 * Se $array_locais_usuario já estiver definido pelo arquivo pai,
 * este include o reutiliza sem fazer nova query.
 */

if (!isset($array_locais_usuario)) {
    $codigo_usuario_erat = intval($_SESSION['id_usuario'] ?? 0);
    $array_locais_usuario = '';
    if ($codigo_usuario_erat > 0) {
        $q_usr = mysqli_query($conector_acesso,
            "SELECT local_usuario FROM usuario
             WHERE id_usuario = $codigo_usuario_erat AND lixeira_usuario = 0");
        $r_usr = $q_usr ? mysqli_fetch_assoc($q_usr) : null;
        if ($r_usr && !empty($r_usr['local_usuario'])) {
            $array_locais_usuario = explode(',', $r_usr['local_usuario']);
        }
    }
}

$arr_local_rat_js = [];
$rs_loc_erat = mysqli_query($conector,
    "SELECT tbl_pessoa_id, tbl_pessoa_nome FROM tbl_pessoa
     WHERE tbl_pessoa_classe=4 AND tbl_pessoa_lixeira=0
     ORDER BY tbl_pessoa_nome");
while ($r = mysqli_fetch_object($rs_loc_erat)) {
    if (is_array($array_locais_usuario)) {
        foreach ($array_locais_usuario as $v) {
            if (trim($v) == $r->tbl_pessoa_id) {
                $arr_local_rat_js[] = ['id' => (int)$r->tbl_pessoa_id, 'nome' => $r->tbl_pessoa_nome];
                break;
            }
        }
    } else {
        $arr_local_rat_js[] = ['id' => (int)$r->tbl_pessoa_id, 'nome' => $r->tbl_pessoa_nome];
    }
}

$arr_cc_rat_js = [];
$rs_cc_erat = mysqli_query($conector,
    "SELECT tbl_cc_codigo_id, tbl_cc_descricao FROM tbl_centro_custo
     WHERE tbl_cc_lixeira=0 ORDER BY tbl_cc_codigo_id");
while ($r = mysqli_fetch_object($rs_cc_erat)) {
    $arr_cc_rat_js[] = ['id' => $r->tbl_cc_codigo_id, 'nome' => $r->tbl_cc_descricao];
}

$arr_conta_rat_js = [];
$rs_cta_erat = mysqli_query($conector,
    "SELECT tbl_plano_contas_codigo_id, tbl_plano_contas_descricao, tbl_plano_contas_nivel
     FROM tbl_plano_contas
     WHERE tbl_plano_contas_debito_credito='D' AND tbl_plano_contas_lixeira=0
     ORDER BY tbl_plano_contas_codigo_id");
while ($r = mysqli_fetch_object($rs_cta_erat)) {
    $arr_conta_rat_js[] = [
        'id'    => $r->tbl_plano_contas_codigo_id,
        'nome'  => $r->tbl_plano_contas_descricao,
        'nivel' => (int)$r->tbl_plano_contas_nivel
    ];
}
?>

<style>
    /* Botão do selectpicker dentro da tabela: ocupa só o espaço flex:1 da célula (~140px),
       deixando espaço para os botões Confirmar/Fechar ao lado */
    #tbl_erat .bootstrap-select { width: 100% !important; }
    #tbl_erat .bootstrap-select > .dropdown-toggle { height: 30px; font-size: 12px; padding: 4px 8px; }

    /* Popup do dropdown (vai para body via data-container):
       largura controlada pelo JS em rateio_editor.js (shown.bs.select handler) */
    body > .bs-container .dropdown-menu { overflow-x: hidden; }
    body > .bs-container .dropdown-menu li a span.text { white-space: nowrap !important; overflow: hidden; text-overflow: ellipsis; display: block; }

    /* Tabela de rateio */
    .tbl-parcelas { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
    .tbl-parcelas th { font-size: 12px; color: #666; font-weight: 600; padding: 6px 8px; border-bottom: 2px solid #ddd; white-space: nowrap; background: #f7f7f7; }
    .tbl-parcelas td { padding: 5px 6px; vertical-align: middle; }
    .tbl-parcelas tbody tr:nth-child(even) td { background-color: #fafafa; }
    .lbl-parcela { font-size: 12px; color: #555; font-weight: 600; white-space: nowrap; }
    .tbl-parcelas input.form-control { height: 30px; font-size: 13px; padding: 4px 8px; }
</style>

<!-- Arrays para o editor de rateio (locais / CC / contas) -->
<script>
var _eratLocais = <?php echo json_encode($arr_local_rat_js, JSON_UNESCAPED_UNICODE); ?>;
var _eratCC     = <?php echo json_encode($arr_cc_rat_js,    JSON_UNESCAPED_UNICODE); ?>;
var _eratContas = <?php echo json_encode($arr_conta_rat_js, JSON_UNESCAPED_UNICODE); ?>;
</script>

<!-- Modal: Editar Rateio -->
<div class="modal fade" id="modal_editar_rateio" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="margin-left:215px;margin-right:5px;width:auto;max-width:1130px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fas fa-edit" style="color:#337ab7;margin-right:6px;"></i>Editar Rateio</h4>
            </div>
            <div class="modal-body" id="erat_body" style="padding:10px 16px;">
                <div id="erat_aviso" class="alert alert-danger" style="display:none;margin-bottom:8px;"></div>
                <p id="erat_titulo_doc" style="margin:0 0 10px 0;font-size:14px;color:#333;"></p>
                <div style="overflow-x:auto;">
                    <table class="tbl-parcelas" id="tbl_erat">
                        <colgroup>
                            <col style="width:26%">
                            <col style="width:26%">
                            <col style="width:28%">
                            <col style="width:14%">
                            <col style="width:6%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Centro de Custos</th>
                                <th>Conta Contábil</th>
                                <th style="text-align:right;">Valor (R$)</th>
                                <th style="text-align:right;">%</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_erat"></tbody>
                        <tbody id="tbody_erat_footer">
                            <tr id="tr_erat_restante">
                                <td colspan="3" style="text-align:right;font-size:12px;color:#666;padding:6px 8px;border-top:1px solid #ddd;">
                                    Total Digitado: <span id="span_rat_total" style="color:#27ae60;font-weight:600;font-size:13px;margin-right:14px;">R$ 0,00</span>
                                    &nbsp;&nbsp;&nbsp;Restante a distribuir:
                                </td>
                                <td id="td_rat_vlr_rest" style="font-size:13px;font-weight:600;color:#c0392b;text-align:right;padding:6px 8px;white-space:nowrap;border-top:1px solid #ddd;">R$ 0,00</td>
                                <td id="td_rat_pct_rest" style="font-size:13px;font-weight:600;color:#c0392b;text-align:right;padding:6px 8px;border-top:1px solid #ddd;">0,00%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="eratSalvar()" style="float:left;">Salvar Rateio</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
