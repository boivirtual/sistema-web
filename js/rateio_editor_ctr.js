/* ================================================================
   EDITOR DE RATEIO (CONTAS A RECEBER) — arquivo autônomo e reutilizável
   Incluído automaticamente via modal_editar_rateio_ctr.php
   Versão de js/rateio_editor.js adaptada para Contas a Receber — todos os
   identificadores globais/ids/funções levam o sufixo "Ctr" para não colidir
   com a versão de Contas a Pagar caso as duas telas coexistam em abas do navegador.
   ================================================================ */

// Callback chamado após salvar com sucesso.
// Defina na página antes de chamar abrirEditarRateioCtr():
//   _eratCallbackPosSalvarCtr = function(id) { ... };
var _eratCallbackPosSalvarCtr = null;

// Corrige a largura e posição do .bs-container após o Bootstrap Select
// ter calculado/aplicado o offsetWidth do botão como largura inline.
// Chamado no evento shown.bs.select — roda depois que o plugin posicionou tudo.
function _eratFixDropdownPosCtr() {
    var $c = $('body > .bs-container');
    if (!$c.length) return;
    // Sobrescreve a largura inline que o plugin calculou com um valor fixo legível
    $c.css('width', '260px');
    // Garante que não ultrapassa a borda direita da janela
    var winW  = $(window).width();
    var cLeft = parseFloat($c.css('left')) || 0;
    if (cLeft + 260 > winW - 5) {
        $c.css('left', Math.max(0, winW - 265) + 'px');
    }
}

var _eratCtrId             = 0;
var _eratPrimeiroCtr       = 0;
var _eratValorTotalCtr     = 0;
var _eratModoCtr           = null; // null | 'valor' | 'perc'
var _eratOrigLocalIdsCtr   = []; // IDs de locais que estavam na tabela quando o editor de local foi aberto
var _eratOrigCcIdsCtr      = []; // IDs de CCs que estavam na tabela quando o editor de CC foi aberto

$(document).ready(function () {
    $('#modal_editar_rateio_ctr').on('hide.bs.modal', function () {
        var $sp = $('#tbody_erat_ctr').find('.selectpicker');
        if ($sp.length) {
            try { $sp.selectpicker('destroy'); } catch (e) {}
            var $td = $sp.closest('td');
            var orig = $td.data('orig-html');
            if (orig) $td.html(orig);
        }
        $('body > .bs-container').remove();
        $('.tooltip').remove();
    });

    // Corrige overflow de qualquer dropdown do editor de rateio
    $(document).on('shown.bs.select', '#tbl_erat_ctr select, #tbl_erat_ctr .selectpicker', function () {
        _eratFixDropdownPosCtr();
    });
});

function _eratFmtMoneyCtr(n) {
    n = parseFloat(n) || 0;
    return n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function _eratParseValCtr(s) {
    if (!s) return 0;
    s = String(s).replace('%', '').trim();
    if (s.indexOf(',') !== -1) s = s.replace(/\./g, '').replace(',', '.');
    return parseFloat(s) || 0;
}

// ── Controle de modo valor / % ──
function _eratSetModoCtr(modo) {
    _eratModoCtr = modo;
    if (modo === 'valor') {
        $('#tbl_erat_ctr .rat-valor').prop('readonly', false).css({'background':'','color':''});
        $('#tbl_erat_ctr .rat-perc').prop('readonly', true).css({'background':'#f9f9f9','color':'#555'});
    } else if (modo === 'perc') {
        $('#tbl_erat_ctr .rat-valor').prop('readonly', true).css({'background':'#f9f9f9','color':'#555'});
        $('#tbl_erat_ctr .rat-perc').prop('readonly', false).css({'background':'','color':''});
    } else {
        $('#tbl_erat_ctr .rat-valor').prop('readonly', false).css({'background':'','color':''});
        $('#tbl_erat_ctr .rat-perc').prop('readonly', false).css({'background':'','color':''});
    }
}

// ── Recalcula Total Digitado e Restante a distribuir ──
function eratRecalcularCtr() {
    var total = _eratValorTotalCtr;
    var soma  = 0;

    if (_eratModoCtr === 'perc') {
        $('#tbl_erat_ctr .rat-perc').each(function () {
            var pct   = _eratParseValCtr($(this).val());
            var valor = total > 0 ? (pct / 100 * total) : 0;
            $(this).closest('tr').find('.rat-valor').val(valor > 0 ? _eratFmtMoneyCtr(valor) : '');
            soma += valor;
        });
    } else {
        $('#tbl_erat_ctr .rat-valor').each(function () {
            soma += _eratParseValCtr($(this).val());
        });
        $('#tbl_erat_ctr .rat-valor').each(function () {
            var v   = _eratParseValCtr($(this).val());
            var pct = total > 0 ? (v / total * 100) : 0;
            $(this).closest('tr').find('.rat-perc').val(pct > 0 ? pct.toFixed(2).replace('.', ',') + '%' : '');
        });
    }

    var rest = total - soma;
    $('#span_rat_total_ctr').text('R$ ' + _eratFmtMoneyCtr(soma));
    var cor = (Math.abs(rest) < 0.01) ? '#27ae60' : '#c0392b';
    $('#td_rat_vlr_rest_ctr').text('R$ ' + _eratFmtMoneyCtr(rest)).css('color', cor);
    $('#td_rat_pct_rest_ctr').text((total > 0 ? rest / total * 100 : 0).toFixed(2).replace('.', ',') + '%').css('color', cor);
    _eratSetModoCtr(_eratModoCtr);
}

// ── Gera linha linha-valor-rateio a partir de objeto JSON ──
function _eratGerarLinhaCtr(ln, showLocal, showCC, showConta, showLocalIcon, showCCIcon) {
    if (showLocal     === undefined) showLocal     = true;
    if (showCC        === undefined) showCC        = true;
    if (showConta     === undefined) showConta     = true;
    if (showLocalIcon === undefined) showLocalIcon = showLocal;
    if (showCCIcon    === undefined) showCCIcon    = showCC;

    ln = ln || {};
    var localId   = ln.local_id   || '';
    var localNome = ln.local_nome  || '';
    var ccId      = ln.cc_id      || '';
    var ccNome    = ln.cc_nome    || '';
    var contaId   = ln.conta_id   || '';
    var contaNome = ln.conta_nome  || '';
    var valor     = ln.conta_valor > 0 ? _eratFmtMoneyCtr(ln.conta_valor) : '';
    var perc      = ln.conta_perc > 0  ? ln.conta_perc.toFixed(2).replace('.', ',') + '%' : '';

    var tr = '<tr class="linha-valor-rateio"' +
        ' data-local-id="'   + localId   + '"' +
        ' data-local-nome="' + localNome.replace(/"/g, '&quot;') + '"' +
        ' data-cc-id="'      + ccId      + '"' +
        ' data-cc-nome="'    + ccNome.replace(/"/g, '&quot;') + '"' +
        ' data-conta-id="'   + contaId   + '"' +
        ' data-conta-nome="' + contaNome.replace(/"/g, '&quot;') + '">';

    // Col Local
    if (showLocal) {
        var localIconHtml = showLocalIcon
            ? ' <a href="#" onclick="eratEditarLocalCtr(this);return false;" style="color:#337ab7;font-size:11px;margin-left:4px;" data-toggle="tooltip" title="Selecionar Local"><i class="fas fa-pen"></i></a>'
            : '';
        tr += '<td style="vertical-align:middle;padding:4px 8px;">' +
            '<div style="display:flex;align-items:center;gap:4px;">' +
            '<span class="lbl-parcela" style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + localNome + '</span>' +
            localIconHtml +
            '</div>' +
            '<input type="hidden" class="erat-local-id"   value="' + localId   + '">' +
            '<input type="hidden" class="erat-local-nome" value="' + localNome + '">' +
            '</td>';
    } else {
        tr += '<td style="vertical-align:middle;padding:4px 8px;">' +
            '<input type="hidden" class="erat-local-id"   value="' + localId   + '">' +
            '<input type="hidden" class="erat-local-nome" value="' + localNome + '">' +
            '</td>';
    }

    // Col CC
    if (showCC) {
        var ccIconHtml = showCCIcon
            ? ' <a href="#" onclick="eratEditarCCCtr(this);return false;" style="color:#337ab7;font-size:11px;margin-left:4px;" data-toggle="tooltip" title="Selecionar Centro de Custo"><i class="fas fa-pen"></i></a>'
            : '';
        tr += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
            '<span class="lbl-parcela">' + ccNome + '</span>' +
            ccIconHtml +
            '<input type="hidden" class="erat-cc-id"   value="' + ccId   + '">' +
            '<input type="hidden" class="erat-cc-nome" value="' + ccNome + '">' +
            '</td>';
    } else {
        tr += '<td style="vertical-align:middle;padding:4px 8px;">' +
            '<input type="hidden" class="erat-cc-id"   value="' + ccId   + '">' +
            '<input type="hidden" class="erat-cc-nome" value="' + ccNome + '">' +
            '</td>';
    }

    // Col Conta
    if (showConta) {
        tr += '<td style="vertical-align:middle;padding:4px 8px;">' +
            '<div style="display:flex;align-items:center;gap:4px;">' +
            '<span class="lbl-parcela" style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + contaNome + '</span>' +
            '<a href="#" onclick="eratEditarContaCtr(this);return false;" style="flex-shrink:0;color:#337ab7;font-size:11px;" data-toggle="tooltip" title="Selecionar Conta Contábil"><i class="fas fa-pen"></i></a>' +
            '</div>' +
            '<input type="hidden" class="erat-conta-id"   value="' + contaId   + '">' +
            '<input type="hidden" class="erat-conta-nome" value="' + contaNome + '">' +
            '</td>';
    } else {
        tr += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
            '<span class="lbl-parcela">' + contaNome + '</span>' +
            '<input type="hidden" class="erat-conta-id"   value="' + contaId   + '">' +
            '<input type="hidden" class="erat-conta-nome" value="' + contaNome + '">' +
            '</td>';
    }

    // Col Valor
    tr += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
        '<input type="text" class="form-control rat-valor" placeholder="0,00"' +
        ' value="' + valor + '" style="height:30px;font-size:13px;text-align:right;"></td>';

    // Col %
    tr += '<td style="vertical-align:middle;text-align:right;padding:4px 2px;">' +
        '<input type="text" class="form-control rat-perc" placeholder="0%"' +
        ' value="' + perc + '" style="height:30px;font-size:12px;text-align:right;padding:4px 4px;"></td>';

    tr += '</tr>';
    return tr;
}

// ── Reconstrói o tbody aplicando agrupamento visual Local/CC ──
function _eratRefreshGroupingCtr() {
    // Coleta dados das linhas nova-conta e destrói selectpickers antes de remover
    var novaContaList = [];
    $('#tbody_erat_ctr tr.linha-nova-conta').each(function () {
        var $tr = $(this);
        try { $tr.find('select.erat-sel-conta-nova').selectpicker('destroy'); } catch(e) {}
        novaContaList.push({
            localId:   String($tr.attr('data-local-id') || ''),
            localNome: $tr.attr('data-local-nome') || '',
            ccId:      String($tr.attr('data-cc-id') || ''),
            ccNome:    $tr.attr('data-cc-nome') || ''
        });
    });
    $('#tbody_erat_ctr tr.linha-nova-conta').remove();

    var rows = [];
    $('#tbody_erat_ctr tr.linha-valor-rateio').each(function () {
        var $tr = $(this);
        rows.push({
            local_id:    $tr.find('.erat-local-id').val()   || '',
            local_nome:  $tr.find('.erat-local-nome').val() || '',
            cc_id:       $tr.find('.erat-cc-id').val()      || '',
            cc_nome:     $tr.find('.erat-cc-nome').val()    || '',
            conta_id:    $tr.find('.erat-conta-id').val()   || '',
            conta_nome:  $tr.find('.erat-conta-nome').val() || '',
            conta_valor: _eratParseValCtr($tr.find('.rat-valor').val()),
            conta_perc:  _eratParseValCtr($tr.find('.rat-perc').val())
        });
    });
    var html = '';
    var prevLocalId = null, prevCcId = null;
    for (var i = 0; i < rows.length; i++) {
        var ln = rows[i];
        var showLocal     = (String(ln.local_id) !== String(prevLocalId));
        var showCC        = showLocal || (String(ln.cc_id) !== String(prevCcId));
        var showConta     = showCC;
        var showLocalIcon = (i === 0);
        var showCCIcon    = showLocal;
        html += _eratGerarLinhaCtr(ln, showLocal, showCC, showConta, showLocalIcon, showCCIcon);
        prevLocalId = ln.local_id;
        prevCcId    = ln.cc_id;
    }
    $('#tbody_erat_ctr').html(html);

    // Recria linhas nova-conta do zero (HTML + selectpicker frescos)
    for (var n = 0; n < novaContaList.length; n++) {
        var d = novaContaList[n];
        var $novaRow = $(_eratGerarLinhaNovaContaCtr(d.localId, d.localNome, d.ccId, d.ccNome));
        $('#tbody_erat_ctr').append($novaRow);
        $novaRow.find('select.erat-sel-conta-nova').each(function () {
            $(this).selectpicker({ dropdownAlignRight: 'auto' });
            var sid = $(this).attr('id');
            if (sid) _eratRemoveSelectAllCtr(sid);
        });
    }

    _eratSetModoCtr(_eratModoCtr);
    eratRecalcularCtr();
    $('#modal_editar_rateio_ctr [data-toggle="tooltip"]').tooltip();
}

// ── Remove linha ──
function eratRemoverLinhaCtr(btn) {
    $(btn).closest('tr').remove();
    _eratRefreshGroupingCtr();
}

// ── Gera linha manual com selects simples (botão + Adicionar Linha) ──
function _eratGerarLinhaManualCtr() {
    var locais = typeof _eratLocaisCtr !== 'undefined' ? _eratLocaisCtr : [];
    var ccs    = typeof _eratCCCtr    !== 'undefined' ? _eratCCCtr    : [];
    var contas = typeof _eratContasCtr !== 'undefined' ? _eratContasCtr : [];

    var optLocal = '<option value="">Local...</option>';
    for (var i = 0; i < locais.length; i++) {
        optLocal += '<option value="' + locais[i].id + '">' + locais[i].nome + '</option>';
    }
    var optCC = '';
    for (var j = 0; j < ccs.length; j++) {
        optCC += '<option value="' + ccs[j].id + '">' + ccs[j].nome + '</option>';
    }
    var optConta = '<option value="">Conta...</option>';
    for (var k = 0; k < contas.length; k++) {
        var ct = contas[k];
        if (ct.nivel === 1)      optConta += '<option value="' + ct.id + '" disabled style="color:#777;font-weight:600;">' + ct.nome + '</option>';
        else if (ct.nivel === 2) optConta += '<option value="' + ct.id + '" disabled style="color:#888;">&nbsp;&nbsp;&nbsp;&nbsp;' + ct.nome + '</option>';
        else                     optConta += '<option value="' + ct.id + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + ct.nome + '</option>';
    }

    var tr = '<tr class="linha-valor-rateio linha-manual">';
    tr += '<td><select class="form-control sel-erat-local" style="height:30px;font-size:12px;">' + optLocal + '</select></td>';
    tr += '<td><select class="form-control sel-erat-cc" style="height:30px;font-size:12px;">' + optCC + '</select></td>';
    tr += '<td><select class="form-control sel-erat-conta" style="height:30px;font-size:12px;">' + optConta + '</select></td>';
    tr += '<td style="text-align:right;padding:4px 8px;"><input type="text" class="form-control rat-valor" placeholder="0,00" style="height:30px;font-size:13px;text-align:right;"></td>';
    tr += '<td style="text-align:right;padding:4px 8px;"><input type="text" class="form-control rat-perc" placeholder="0,00%" style="height:30px;font-size:13px;text-align:right;"></td>';
    tr += '<td style="text-align:center;vertical-align:middle;">' +
          '<button type="button" class="btn btn-primary btn-xs" onclick="eratConfirmarLinhaManualCtr(this)" style="white-space:nowrap;font-size:11px;padding:3px 7px;margin-bottom:2px;">OK</button> ' +
          '<button type="button" class="btn btn-danger btn-xs" onclick="eratRemoverLinhaCtr(this)"><i class="fas fa-times"></i></button>' +
          '</td>';
    tr += '</tr>';
    return tr;
}

function eratAdicionarLinhaCtr() {
    $('#tr_erat_restante_ctr').before(_eratGerarLinhaManualCtr());
}

function eratConfirmarLinhaManualCtr(btn) {
    var $tr     = $(btn).closest('tr');
    var localId = $tr.find('.sel-erat-local').val();
    var localNm = $tr.find('.sel-erat-local option:selected').text().trim();
    var ccId    = $tr.find('.sel-erat-cc').val();
    var ccNm    = $tr.find('.sel-erat-cc option:selected').text().trim();
    var contaId = $tr.find('.sel-erat-conta').val();
    var contaNm = $tr.find('.sel-erat-conta option:selected').text().trim();
    var valor   = $tr.find('.rat-valor').val();

    if (!localId) { alert('Selecione o Local.'); return; }
    if (!ccId)    { alert('Selecione o Centro de Custo.'); return; }
    if (!contaId) { alert('Selecione a Conta Contábil.'); return; }

    var novaLinha = $(_eratGerarLinhaCtr({
        local_id: localId, local_nome: localNm,
        cc_id: ccId, cc_nome: ccNm,
        conta_id: contaId, conta_nome: contaNm,
        conta_valor: _eratParseValCtr(valor), conta_perc: 0
    }));
    $tr.replaceWith(novaLinha);
    _eratRefreshGroupingCtr();
}

function _eratRemoveSelectAllCtr(selId) {
    $('#' + selId).closest('.bootstrap-select').find('.bs-actionsbox .bs-select-all').remove();
    $('#' + selId).closest('.bootstrap-select').find('.bs-actionsbox').css('text-align', 'right');
}

function _eratTemEditorAbertoCtr() {
    return $('#tbody_erat_ctr tr:not(.linha-nova-conta) td .bootstrap-select').length > 0;
}

// ── Editor inline: Local ──
function eratEditarLocalCtr(link) {
    if (_eratTemEditorAbertoCtr()) return;
    var $td    = $(link).closest('td');
    var locais = typeof _eratLocaisCtr !== 'undefined' ? _eratLocaisCtr : [];
    var selId  = 'erat_sel_local_ctr_' + Date.now();

    var currentLocalIds = [];
    $('#tbody_erat_ctr tr.linha-valor-rateio').each(function () {
        var lid = String(parseInt($(this).attr('data-local-id') || $(this).find('.erat-local-id').val() || 0, 10) || 0);
        if (lid && lid !== '0' && currentLocalIds.indexOf(lid) === -1) currentLocalIds.push(lid);
    });
    _eratOrigLocalIdsCtr = currentLocalIds.slice(); // guarda cópia dos IDs originais

    var optLocal = '';
    for (var i = 0; i < locais.length; i++) {
        var locId = String(parseInt(locais[i].id, 10) || 0);
        var sel = currentLocalIds.indexOf(locId) !== -1 ? ' selected' : '';
        optLocal += '<option value="' + locId + '"' + sel + '>' + locais[i].nome + '</option>';
    }

    try { $('#modal_editar_rateio_ctr [data-toggle="tooltip"]').tooltip('destroy'); } catch (e) {}
    $('.tooltip').remove();
    $td.data('orig-html', $td.html()).html(
        '<div style="display:flex;align-items:center;gap:4px;width:100%;">' +
        '<div style="flex:1;min-width:0;">' +
        '<select id="' + selId + '" class="selectpicker" multiple data-live-search="true"' +
        ' data-container="body" title="Selecione o local...">' +
        optLocal + '</select></div>' +
        '<button type="button" class="btn btn-primary btn-sm" onclick="eratConfirmarLocalCtr(this)">Confirmar</button>' +
        '<button type="button" class="btn btn-default btn-sm" onclick="eratCancelarEdicaoCtr(this)">Fechar</button>' +
        '</div>'
    );
    $('#' + selId).selectpicker({ dropdownAlignRight: 'auto' });
    $('#' + selId).selectpicker('val', currentLocalIds);
    _eratRemoveSelectAllCtr(selId);
}

function eratConfirmarLocalCtr(btn) {
    var $td         = $(btn).closest('td');
    var $sel        = $td.find('select');
    var selectedIds = $sel.val() || [];

    if (!selectedIds || selectedIds.length === 0) {
        alert('Selecione pelo menos um Local.');
        return;
    }

    // IDs que estavam na tabela ANTES do usuário abrir o editor (gravados em _eratOrigLocalIdsCtr)
    var origLidSet = {};
    for (var i = 0; i < _eratOrigLocalIdsCtr.length; i++) origLidSet[_eratOrigLocalIdsCtr[i]] = true;

    var allLocalRows = {};
    $('#tbody_erat_ctr tr.linha-valor-rateio:not(.linha-nova-conta)').each(function () {
        var lid = String($(this).attr('data-local-id') || $(this).find('.erat-local-id').val() || '');
        if (!lid || lid === '0') return;
        if (!allLocalRows[lid]) allLocalRows[lid] = [];
        allLocalRows[lid].push({
            cc_id:       $(this).find('.erat-cc-id').val(),
            cc_nome:     $(this).find('.erat-cc-nome').val(),
            conta_id:    $(this).find('.erat-conta-id').val(),
            conta_nome:  $(this).find('.erat-conta-nome').val(),
            conta_valor: _eratParseValCtr($(this).find('.rat-valor').val()),
            conta_perc:  _eratParseValCtr($(this).find('.rat-perc').val())
        });
    });

    var localNames = {};
    for (var l = 0; l < selectedIds.length; l++) {
        localNames[selectedIds[l]] = $sel.find('option[value="' + selectedIds[l] + '"]').text().trim();
    }

    var $allRows = $('#tbody_erat_ctr tr.linha-valor-rateio');
    var $anchor  = $allRows.first();
    var newHtml  = '';
    var defaultCcId   = (_eratCCCtr && _eratCCCtr.length > 0) ? String(_eratCCCtr[0].id) : '';
    var defaultCcNome = (_eratCCCtr && _eratCCCtr.length > 0) ? _eratCCCtr[0].nome : '';

    for (var l = 0; l < selectedIds.length; l++) {
        var newLocalId = selectedIds[l];
        var newLocalNm = localNames[newLocalId];
        // Só preserva dados se o local JÁ ESTAVA selecionado quando o editor foi aberto
        var rows = origLidSet[newLocalId] ? (allLocalRows[newLocalId] || null) : null;
        if (rows && rows.length > 0) {
            for (var r = 0; r < rows.length; r++) {
                var ln = $.extend({}, rows[r]);
                ln.local_id   = newLocalId;
                ln.local_nome = newLocalNm;
                newHtml += _eratGerarLinhaCtr(ln);
            }
        } else {
            newHtml += _eratGerarLinhaNovaContaCtr(newLocalId, newLocalNm, defaultCcId, defaultCcNome);
        }
    }

    $('body > .bs-container').remove();
    if ($anchor.length) {
        $anchor.before(newHtml);
        $allRows.remove();
    } else {
        $('#tbody_erat_ctr').html(newHtml);
    }

    _eratRefreshGroupingCtr();
}

// ── Editor inline: Centro de Custo ──
function eratEditarCCCtr(link) {
    if (_eratTemEditorAbertoCtr()) return;
    var $td     = $(link).closest('td');
    var $tr     = $td.closest('tr');
    var localId = String($tr.attr('data-local-id') || $tr.find('.erat-local-id').val() || '');
    var ccs     = typeof _eratCCCtr !== 'undefined' ? _eratCCCtr : [];
    var selId   = 'erat_sel_cc_ctr_' + Date.now();

    var currentCcIds = [];
    $('#tbody_erat_ctr tr.linha-valor-rateio').each(function () {
        var lid = String($(this).attr('data-local-id') || $(this).find('.erat-local-id').val() || '');
        if (lid !== localId) return;
        var cid = String($(this).attr('data-cc-id') || $(this).find('.erat-cc-id').val() || '');
        if (cid && cid !== '0' && currentCcIds.indexOf(cid) === -1) currentCcIds.push(cid);
    });
    _eratOrigCcIdsCtr = currentCcIds.slice(); // guarda cópia dos IDs originais

    var optCC = '';
    for (var i = 0; i < ccs.length; i++) {
        var sel = currentCcIds.indexOf(String(ccs[i].id)) !== -1 ? ' selected' : '';
        optCC += '<option value="' + ccs[i].id + '"' + sel + '>' + ccs[i].nome + '</option>';
    }

    try { $('#modal_editar_rateio_ctr [data-toggle="tooltip"]').tooltip('destroy'); } catch (e) {}
    $('.tooltip').remove();
    $td.data('orig-html', $td.html()).html(
        '<div style="display:flex;align-items:center;gap:4px;width:100%;">' +
        '<div style="flex:1;min-width:0;">' +
        '<select id="' + selId + '" class="selectpicker" multiple data-live-search="true"' +
        ' data-container="body" title="Selecione o CC...">' +
        optCC + '</select></div>' +
        '<button type="button" class="btn btn-primary btn-sm" onclick="eratConfirmarCCCtr(this)">Confirmar</button>' +
        '<button type="button" class="btn btn-default btn-sm" onclick="eratCancelarEdicaoCtr(this)">Fechar</button>' +
        '</div>'
    );
    $('#' + selId).selectpicker({ dropdownAlignRight: 'auto' });
    $('#' + selId).selectpicker('val', currentCcIds);
    _eratRemoveSelectAllCtr(selId);
}

function eratConfirmarCCCtr(btn) {
    var $td         = $(btn).closest('td');
    var $tr         = $td.closest('tr');
    var $sel        = $td.find('select');
    var selectedIds = $sel.val() || [];

    if (!selectedIds || selectedIds.length === 0) {
        alert('Selecione pelo menos um Centro de Custo.');
        return;
    }

    var currentLocalId = String($tr.attr('data-local-id') || $tr.find('.erat-local-id').val() || '');
    var localNm        = $tr.attr('data-local-nome') || $tr.find('.erat-local-nome').val() || '';

    // CCs que estavam na tabela ANTES do usuário abrir o editor (gravados em _eratOrigCcIdsCtr)
    var origCidSet = {};
    for (var i = 0; i < _eratOrigCcIdsCtr.length; i++) origCidSet[_eratOrigCcIdsCtr[i]] = true;

    var allCcRows = {};
    $('#tbody_erat_ctr tr.linha-valor-rateio:not(.linha-nova-conta)').each(function () {
        var lid = String($(this).attr('data-local-id') || $(this).find('.erat-local-id').val() || '');
        if (lid !== currentLocalId) return;
        var cid = String($(this).attr('data-cc-id') || $(this).find('.erat-cc-id').val() || '');
        if (!cid || cid === '0') return;
        if (!allCcRows[cid]) allCcRows[cid] = [];
        allCcRows[cid].push({
            conta_id:    $(this).find('.erat-conta-id').val(),
            conta_nome:  $(this).find('.erat-conta-nome').val(),
            conta_valor: _eratParseValCtr($(this).find('.rat-valor').val()),
            conta_perc:  _eratParseValCtr($(this).find('.rat-perc').val())
        });
    });

    var ccNames = {};
    for (var l = 0; l < selectedIds.length; l++) {
        ccNames[selectedIds[l]] = $sel.find('option[value="' + selectedIds[l] + '"]').text().trim();
    }

    var $groupRows = $('#tbody_erat_ctr tr.linha-valor-rateio').filter(function () {
        var lid = String($(this).attr('data-local-id') || $(this).find('.erat-local-id').val() || '');
        return lid === currentLocalId;
    });
    var $anchor = $groupRows.first();
    var newHtml = '';

    for (var l = 0; l < selectedIds.length; l++) {
        var newCcId = selectedIds[l];
        var newCcNm = ccNames[newCcId];
        // Só preserva dados se o CC JÁ ESTAVA selecionado quando o editor foi aberto
        var rows = origCidSet[newCcId] ? (allCcRows[newCcId] || null) : null;
        if (rows && rows.length > 0) {
            for (var r = 0; r < rows.length; r++) {
                var ln = $.extend({}, rows[r]);
                ln.local_id   = currentLocalId;
                ln.local_nome = localNm;
                ln.cc_id      = newCcId;
                ln.cc_nome    = newCcNm;
                newHtml += _eratGerarLinhaCtr(ln);
            }
        } else {
            newHtml += _eratGerarLinhaNovaContaCtr(currentLocalId, localNm, newCcId, newCcNm);
        }
    }

    $('body > .bs-container').remove();
    $anchor.before(newHtml);
    $groupRows.remove();
    _eratRefreshGroupingCtr();
}

// ── Editor inline: Conta Contábil ──
function eratEditarContaCtr(link) {
    if (_eratTemEditorAbertoCtr()) return;
    var $td     = $(link).closest('td');
    var $tr     = $td.closest('tr');
    var localId = String($tr.attr('data-local-id') || $tr.find('.erat-local-id').val() || '');
    var ccId    = String($tr.attr('data-cc-id')    || $tr.find('.erat-cc-id').val()    || '');
    var contas  = typeof _eratContasCtr !== 'undefined' ? _eratContasCtr : [];
    var selId   = 'erat_sel_conta_ctr_' + Date.now();

    var groupContaIds = [];
    $('#tbody_erat_ctr tr.linha-valor-rateio').each(function () {
        var lid = String($(this).attr('data-local-id') || $(this).find('.erat-local-id').val() || '');
        var cid = String($(this).attr('data-cc-id')    || $(this).find('.erat-cc-id').val()    || '');
        if (lid === localId && cid === ccId) {
            groupContaIds.push(String($(this).find('.erat-conta-id').val()));
        }
    });

    var optConta = '';
    for (var i = 0; i < contas.length; i++) {
        var ct     = contas[i];
        var isSel  = groupContaIds.indexOf(String(ct.id)) !== -1;
        var selAttr = isSel ? ' selected' : '';
        if (ct.nivel === 1) {
            optConta += '<option value="' + ct.id + '" disabled data-nivel="1"' + selAttr + '>' + ct.nome + '</option>';
        } else if (ct.nivel === 2) {
            optConta += '<option value="' + ct.id + '" disabled data-nivel="2"' + selAttr + '>' + ct.nome + '</option>';
        } else {
            optConta += '<option value="' + ct.id + '" data-nivel="3"' + selAttr + '>' + ct.nome + '</option>';
        }
    }

    try { $('#modal_editar_rateio_ctr [data-toggle="tooltip"]').tooltip('destroy'); } catch (e) {}
    $('.tooltip').remove();
    $td.data('orig-html', $td.html()).html(
        '<div style="display:flex;align-items:center;gap:4px;width:100%;">' +
        '<div style="flex:1;min-width:0;">' +
        '<select id="' + selId + '" class="selectpicker" multiple data-live-search="true"' +
        ' data-container="body" title="Selecione as contas...">' +
        optConta + '</select></div>' +
        '<button type="button" class="btn btn-primary btn-sm" onclick="eratConfirmarContaCtr(this)">Confirmar</button>' +
        '<button type="button" class="btn btn-default btn-sm" onclick="eratCancelarEdicaoCtr(this)">Fechar</button>' +
        '</div>'
    );
    $('#' + selId).selectpicker({ dropdownAlignRight: 'auto' });
    $('#' + selId).selectpicker('val', groupContaIds);
    _eratRemoveSelectAllCtr(selId);
}

function eratConfirmarContaCtr(btn) {
    var $td         = $(btn).closest('td');
    var $tr         = $td.closest('tr');
    var $sel        = $td.find('select');
    var selectedIds = $sel.val() || [];

    if (!selectedIds || selectedIds.length === 0) {
        alert('Selecione pelo menos uma Conta Contábil.');
        return;
    }

    var localId = String($tr.attr('data-local-id') || $tr.find('.erat-local-id').val() || '');
    var localNm = $tr.attr('data-local-nome')     || $tr.find('.erat-local-nome').val() || '';
    var ccId    = String($tr.attr('data-cc-id')   || $tr.find('.erat-cc-id').val()    || '');
    var ccNm    = $tr.attr('data-cc-nome')         || $tr.find('.erat-cc-nome').val()   || '';

    var existing = {};
    $('#tbody_erat_ctr tr.linha-valor-rateio').each(function () {
        var lid  = String($(this).attr('data-local-id') || $(this).find('.erat-local-id').val() || '');
        var cid2 = String($(this).attr('data-cc-id')    || $(this).find('.erat-cc-id').val()    || '');
        if (lid === localId && cid2 === ccId) {
            var cid = String($(this).find('.erat-conta-id').val());
            existing[cid] = {
                valor: _eratParseValCtr($(this).find('.rat-valor').val()),
                perc:  _eratParseValCtr($(this).find('.rat-perc').val())
            };
        }
    });

    var $groupRows = $('#tbody_erat_ctr tr.linha-valor-rateio').filter(function () {
        var lid = String($(this).attr('data-local-id') || $(this).find('.erat-local-id').val() || '');
        var cid = String($(this).attr('data-cc-id')    || $(this).find('.erat-cc-id').val()    || '');
        return lid === localId && cid === ccId;
    });

    var $anchor = $groupRows.first();
    var newHtml = '';
    for (var i = 0; i < selectedIds.length; i++) {
        var contaId = selectedIds[i];
        var contaNm = $sel.find('option[value="' + contaId + '"]').text().trim();
        var prev    = existing[String(contaId)] || { valor: 0, perc: 0 };
        newHtml += _eratGerarLinhaCtr({
            local_id:    localId,
            local_nome:  localNm,
            cc_id:       ccId,
            cc_nome:     ccNm,
            conta_id:    contaId,
            conta_nome:  contaNm,
            conta_valor: prev.valor,
            conta_perc:  prev.perc
        });
    }

    $('body > .bs-container').remove();
    $anchor.before(newHtml);
    $groupRows.remove();
    _eratRefreshGroupingCtr();
}

function eratCancelarEdicaoCtr(btn) {
    var $td = $(btn).closest('td');
    var $sp = $td.find('.selectpicker');
    if ($sp.length) {
        try { $sp.selectpicker('destroy'); } catch (e) {}
    }
    $('body > .bs-container').remove();
    try { $('#modal_editar_rateio_ctr [data-toggle="tooltip"]').tooltip('destroy'); } catch (e) {}
    $('.tooltip').remove();
    $td.html($td.data('orig-html'));
    $('#modal_editar_rateio_ctr [data-toggle="tooltip"]').tooltip();
}

// ── Gera linha de seleção de conta para local/CC recém adicionado ──
function _eratGerarLinhaNovaContaCtr(localId, localNome, ccId, ccNome) {
    var contas = typeof _eratContasCtr !== 'undefined' ? _eratContasCtr : [];
    var selId  = 'erat_nova_conta_ctr_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

    var optConta = '';
    for (var i = 0; i < contas.length; i++) {
        var ct = contas[i];
        if (ct.nivel === 1) {
            optConta += '<option value="' + ct.id + '" disabled data-nivel="1">' + ct.nome + '</option>';
        } else if (ct.nivel === 2) {
            optConta += '<option value="' + ct.id + '" disabled data-nivel="2">' + ct.nome + '</option>';
        } else {
            optConta += '<option value="' + ct.id + '" data-nivel="3">' + ct.nome + '</option>';
        }
    }

    var tr = '<tr class="linha-valor-rateio linha-nova-conta"' +
        ' data-local-id="' + localId + '"' +
        ' data-local-nome="' + localNome.replace(/"/g, '&quot;') + '"' +
        ' data-cc-id="' + ccId + '"' +
        ' data-cc-nome="' + ccNome.replace(/"/g, '&quot;') + '">';

    tr += '<td style="vertical-align:middle;padding:4px 8px;">' +
        '<span class="lbl-parcela">' + localNome + '</span>' +
        '<input type="hidden" class="erat-local-id"   value="' + localId   + '">' +
        '<input type="hidden" class="erat-local-nome" value="' + localNome + '">' +
        '</td>';

    tr += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
        '<span class="lbl-parcela">' + ccNome + '</span>' +
        ' <a href="#" onclick="eratEditarCCCtr(this);return false;" style="color:#337ab7;font-size:11px;margin-left:4px;" data-toggle="tooltip" title="Selecionar Centro de Custo"><i class="fas fa-pen"></i></a>' +
        '<input type="hidden" class="erat-cc-id"   value="' + ccId   + '">' +
        '<input type="hidden" class="erat-cc-nome" value="' + ccNome + '">' +
        '</td>';

    tr += '<td colspan="3" style="vertical-align:middle;padding:4px 8px;">' +
        '<div style="display:flex;align-items:center;gap:6px;">' +
        '<div style="flex:1;min-width:0;">' +
        '<select id="' + selId + '" class="selectpicker erat-sel-conta-nova" multiple' +
        ' data-live-search="true" data-container="body"' +
        ' title="Selecione as contas...">' +
        optConta + '</select>' +
        '</div>' +
        '<button type="button" class="btn btn-primary btn-sm" style="white-space:nowrap;"' +
        ' onclick="eratConfirmarNovaContaLocalCtr(this)">Confirmar</button>' +
        '</div>' +
        '<input type="hidden" class="erat-conta-id"   value="">' +
        '<input type="hidden" class="erat-conta-nome" value="">' +
        '</td>';

    tr += '</tr>';
    return tr;
}

// ── Confirma seleção de conta em linha nova-conta ──
function eratConfirmarNovaContaLocalCtr(btn) {
    var $tr         = $(btn).closest('tr');
    var $sel        = $tr.find('select.erat-sel-conta-nova');
    var selectedIds = $sel.val() || [];

    if (!selectedIds || selectedIds.length === 0) {
        alert('Selecione pelo menos uma Conta Contábil.');
        return;
    }

    var localId = String($tr.attr('data-local-id') || $tr.find('.erat-local-id').val() || '');
    var localNm = $tr.attr('data-local-nome')     || $tr.find('.erat-local-nome').val() || '';
    var ccId    = String($tr.attr('data-cc-id')   || $tr.find('.erat-cc-id').val()    || '');
    var ccNm    = $tr.attr('data-cc-nome')         || $tr.find('.erat-cc-nome').val()   || '';

    var newHtml = '';
    for (var i = 0; i < selectedIds.length; i++) {
        var contaId = selectedIds[i];
        var contaNm = $sel.find('option[value="' + contaId + '"]').text().trim();
        newHtml += _eratGerarLinhaCtr({
            local_id:    localId,
            local_nome:  localNm,
            cc_id:       ccId,
            cc_nome:     ccNm,
            conta_id:    contaId,
            conta_nome:  contaNm,
            conta_valor: 0,
            conta_perc:  0
        });
    }

    try { $sel.selectpicker('destroy'); } catch (e) {}
    $('body > .bs-container').remove();
    $tr.before(newHtml);
    $tr.remove();
    _eratRefreshGroupingCtr();
}

// ── Abre o modal e carrega dados do rateio via AJAX ──
function abrirEditarRateioCtr(ctr_id) {
    _eratCtrId   = ctr_id;
    _eratModoCtr = null;
    $('#erat_aviso_ctr').hide();
    $('#tbody_erat_ctr').html(
        '<tr><td colspan="5" style="text-align:center;padding:20px;">' +
        '<i class="fas fa-spinner fa-spin"></i> Carregando...</td></tr>'
    );
    $('#erat_titulo_doc_ctr').text('');
    $('#span_rat_total_ctr').text('R$ 0,00');
    $('#td_rat_vlr_rest_ctr').text('R$ 0,00').css('color', '#c0392b');
    $('#td_rat_pct_rest_ctr').text('0,00%').css('color', '#c0392b');
    $('#modal_editar_rateio_ctr').modal('show');

    $.ajax({
        type: 'POST',
        url: 'get_rateio_json_ctr.php',
        data: { ctr_id: ctr_id },
        dataType: 'json',
        timeout: 15000,
        success: function (resp) {
            if (resp.error) {
                $('#erat_aviso_ctr').text(resp.message).show();
                $('#tbody_erat_ctr').html('');
                return;
            }
            _eratPrimeiroCtr   = resp.primeiro_ctr_id;
            _eratValorTotalCtr = resp.valor_total || 0;
            $('#erat_titulo_doc_ctr').text('Documento Nº ' + resp.numero_doc + ' | Valor Total: R$ ' + _eratFmtMoneyCtr(_eratValorTotalCtr));

            var linhas = resp.linhas || [];
            if (linhas.length === 0) {
                $('#tbody_erat_ctr').html('<tr><td colspan="5" style="text-align:center;color:#888;padding:16px;">Sem dados de rateio.</td></tr>');
                return;
            }
            var html = '';
            var prevLocalId = null, prevCcId = null;
            for (var i = 0; i < linhas.length; i++) {
                var ln = linhas[i];
                var showLocal     = (String(ln.local_id) !== String(prevLocalId));
                var showCC        = showLocal || (String(ln.cc_id) !== String(prevCcId));
                var showConta     = showCC;
                var showLocalIcon = (i === 0);
                var showCCIcon    = showLocal;
                html += _eratGerarLinhaCtr(ln, showLocal, showCC, showConta, showLocalIcon, showCCIcon);
                prevLocalId = ln.local_id;
                prevCcId    = ln.cc_id;
            }
            $('#tbody_erat_ctr').html(html);
            _eratSetModoCtr(null);
            eratRecalcularCtr();
            $('#modal_editar_rateio_ctr [data-toggle="tooltip"]').tooltip();
        },
        error: function (xhr, status, err) {
            $('#erat_aviso_ctr').text('Erro ao carregar rateio: ' + status).show();
            $('#tbody_erat_ctr').html('');
        }
    });
}

// ── Máscara monetária estilo calculadora ──
function _eratMaskMoneyCtr() {
    var el = this;
    setTimeout(function () {
        var v = el.value.replace(/\D/g, '');
        v = String(Number(v));
        var len = v.length;
        if (len === 1) v = v.replace(/(\d)/, '0.0$1');
        else if (len === 2) v = v.replace(/(\d)/, '0.$1');
        else v = v.replace(/(\d{2})$/, '.$1');
        el.value = v;
    }, 1);
}

// ── Handlers de teclado e blur para rat-valor e rat-perc ──
$(document).on('keypress', '#tbl_erat_ctr .rat-valor', function (e) {
    var c = e.which;
    if (c === 0 || c === 8) return true;
    if (c < 48 || c > 57) return false;
    if (_eratModoCtr !== 'valor') _eratSetModoCtr('valor');
    _eratMaskMoneyCtr.call(this);
    return true;
});
$(document).on('blur', '#tbl_erat_ctr .rat-valor', function () {
    var n = _eratParseValCtr($(this).val());
    $(this).val(n > 0 ? _eratFmtMoneyCtr(n) : '');
    eratRecalcularCtr();
});
$(document).on('keypress', '#tbl_erat_ctr .rat-perc', function (e) {
    var c = e.which;
    if (c === 0 || c === 8) return true;
    if (c === 44) { return $(this).val().replace('%','').indexOf(',') === -1; }
    if (c < 48 || c > 57) return false;
    if (_eratModoCtr !== 'perc') _eratSetModoCtr('perc');
    return true;
});
$(document).on('blur', '#tbl_erat_ctr .rat-perc', function () {
    var raw = $(this).val().replace('%','').replace(',','.');
    var n   = parseFloat(raw) || 0;
    $(this).val(n > 0 ? n.toFixed(2).replace('.', ',') + '%' : '');
    eratRecalcularCtr();
});

// ── Enter navega como Tab nos campos de valor/% do rateio ──
$(document).on('keydown', '#tbl_erat_ctr input.rat-valor, #tbl_erat_ctr input.rat-perc', function (e) {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    $(this).trigger('blur');
    var $inputs = $('#tbl_erat_ctr').find('input.rat-valor, input.rat-perc')
        .filter(':not([readonly]):not([disabled])').filter(':visible');
    var idx = $inputs.index(this);
    if (idx >= 0 && idx < $inputs.length - 1) {
        $inputs.eq(idx + 1).focus();
    }
});

// ── Salvar rateio ──
function eratSalvarCtr() {
    $('#erat_aviso_ctr').hide();
    var linhas = [];
    var valido = true;

    $('#tbody_erat_ctr tr.linha-valor-rateio').each(function () {
        var $tr = $(this);

        if ($tr.hasClass('linha-manual')) {
            valido = false;
            $('#erat_aviso_ctr').text('Clique em "OK" para confirmar todas as linhas manuais antes de salvar.').show();
            return false;
        }

        if ($tr.hasClass('linha-nova-conta')) {
            valido = false;
            $('#erat_aviso_ctr').text('Clique em "Confirmar" para selecionar a conta contábil antes de salvar.').show();
            return false;
        }

        var localId   = $tr.find('.erat-local-id').val()   || '';
        var localNome = $tr.find('.erat-local-nome').val() || '';
        var ccId      = $tr.find('.erat-cc-id').val()      || '';
        var ccNome    = $tr.find('.erat-cc-nome').val()    || '';
        var contaId   = $tr.find('.erat-conta-id').val()   || '';
        var contaNome = $tr.find('.erat-conta-nome').val() || '';
        var valor     = _eratParseValCtr($tr.find('.rat-valor').val());
        var perc      = _eratParseValCtr($tr.find('.rat-perc').val());

        if (!localId || !contaId) { valido = false; return; }

        var localExist = null;
        for (var i = 0; i < linhas.length; i++) {
            if (String(linhas[i].id) === String(localId)) { localExist = linhas[i]; break; }
        }
        if (!localExist) {
            localExist = { id: localId, nome: localNome, valor: 0, perc: 0, ccs: [] };
            linhas.push(localExist);
        }
        localExist.valor += valor;
        localExist.perc  += perc;

        var ccExist = null;
        for (var j = 0; j < localExist.ccs.length; j++) {
            if (String(localExist.ccs[j].id) === String(ccId)) { ccExist = localExist.ccs[j]; break; }
        }
        if (!ccExist) {
            ccExist = { id: ccId, nome: ccNome, valor: 0, perc: 0, contas: [] };
            localExist.ccs.push(ccExist);
        }
        ccExist.valor += valor;
        ccExist.perc  += perc;
        ccExist.contas.push({ id: contaId, nome: contaNome, valor: valor, perc: perc });
    });

    if (!valido) {
        if (!$('#erat_aviso_ctr').is(':visible')) {
            $('#erat_aviso_ctr').text('Preencha Local e Conta Contábil em todas as linhas.').show();
        }
        return;
    }
    if (linhas.length === 0) {
        $('#erat_aviso_ctr').text('Adicione pelo menos uma linha de rateio.').show();
        return;
    }

    var somaVal = 0;
    $('#tbl_erat_ctr .rat-valor').each(function() { somaVal += _eratParseValCtr($(this).val()); });
    var rest = _eratValorTotalCtr - somaVal;
    if (Math.abs(rest) > 0.05) {
        if (!confirm('O valor restante a distribuir é R$ ' + _eratFmtMoneyCtr(rest) + '. Deseja salvar mesmo assim?')) return;
    }

    $.ajax({
        type: 'POST',
        url: 'salvar_rateio_editar_ctr.php',
        data: { primeiro_ctr_id: _eratPrimeiroCtr, rateio_json: JSON.stringify(linhas) },
        dataType: 'json',
        timeout: 15000,
        success: function (resp) {
            if (resp.error) {
                $('#erat_aviso_ctr').text(resp.message).show();
                return;
            }
            $('#modal_editar_rateio_ctr').modal('hide');
            if (typeof _eratCallbackPosSalvarCtr === 'function') {
                setTimeout(function () { _eratCallbackPosSalvarCtr(_eratCtrId); }, 400);
            }
        },
        error: function () {
            $('#erat_aviso_ctr').text('Erro de comunicação ao salvar rateio.').show();
        }
    });
}
