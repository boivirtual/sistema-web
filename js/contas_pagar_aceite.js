/**CONTAS A PAGAR ACEITE*/

function toggleRateio(id) {
    $.ajax({
        type: 'POST',
        url: 'get_rateio_aceite.php',
        data: { ctp_id: id },
        timeout: 10000,
        success: function (data) {
            $('#modal_rateio_aceite_dyn').remove();
            var corpo = data || '<p style="color:#888;">Sem dados de rateio.</p>';
            var modalHtml =
                '<div class="modal fade" id="modal_rateio_aceite_dyn" tabindex="-1" role="dialog" data-backdrop="static">' +
                '<div class="modal-dialog" style="width:92%;max-width:940px;" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                '<h4 class="modal-title"><i class="fas fa-sitemap" style="color:#337ab7;margin-right:6px;"></i>Distribuição do Rateio</h4>' +
                '</div>' +
                '<div class="modal-body" style="overflow-x:auto;padding:12px 16px;">' + corpo + '</div>' +
                '<div class="modal-footer">' +
                '<button class="btn btn-primary" type="button" style="float:left;" onclick="$(\'#modal_rateio_aceite_dyn\').modal(\'hide\');abrirEditarRateio(' + id + ');">Editar</button>' +
                '<button class="btn btn-default" type="button" data-dismiss="modal">Fechar</button>' +
                '</div>' +
                '</div></div></div>';
            $('body').append(modalHtml);
            $('#modal_rateio_aceite_dyn').modal('show');
            $('#modal_rateio_aceite_dyn').on('hidden.bs.modal', function () { $(this).remove(); });
        },
        error: function (xhr, status, err) {
            alert('Erro ao carregar rateio: ' + status + (err ? ' — ' + err : ''));
        }
    });
}

const idConta = [];
const selectedConta = [];

$(window).load(function () {
    // Restaura filtro de local
    var filtro_local = $("#exibe_local").val();
    if (filtro_local != '' && filtro_local != null) {
        filtro_local = filtro_local.split(',');
        $.each(filtro_local, function (idx, val) {
            $('#codigo_fazenda option[value=' + val + ']').attr('selected', true);
        });
        $('#codigo_fazenda').selectpicker('refresh');
    }

    // Restaura filtro de fornecedor
    var filtro_fornecedor = $("#exibe_fornecedor").val();
    if (filtro_fornecedor != '' && filtro_fornecedor != null) {
        filtro_fornecedor = filtro_fornecedor.split(',');
        $.each(filtro_fornecedor, function (idx, val) {
            $('#razao_nome option[value=' + val + ']').attr('selected', true);
        });
        $('#razao_nome').selectpicker('refresh');
    }

    var filtro_conta = $("#exibe_conta").val();
    var limpa_filtro_contas = $("#limpar_filtro_contas").val();

    if (limpa_filtro_contas == 'S') {
        $("#contas_selecionadas").val('Todas ou (Clique p/ selecionar contas)');
        $.ajax({
            type: "POST",
            url: "lista_conta_contabil.php",
            data: { 'tipo_conta': 'D' },
            success: function (data) {
                $("#modal_conta_info").html(data);
                $('input[name="conta_option"]').each(function () {
                    idConta.push($(this).attr("id"));
                });
                $("#modal_filtro_aceite").modal("show");
            },
        });
    } else if (filtro_conta != '' && filtro_conta != null) {
        $.ajax({
            type: "POST",
            url: "lista_conta_contabil.php",
            data: { 'tipo_conta': 'D' },
            success: function (data) {
                $("#modal_conta_info").html(data);
                $('input[name="conta_option"]').each(function () {
                    idConta.push($(this).attr("id"));
                });

                filtro_conta = $("#exibe_conta").val().split(',');
                $.each(filtro_conta, function (idx, val) {
                    document.getElementById(`${val}`).checked = true;
                });

                var aChk = document.getElementsByName("conta_option");
                var tem_conta = '';
                var conta_filtro = [];
                for (var i = 0; i < aChk.length; i++) {
                    if (aChk[i].checked == true) tem_conta = 'S';
                }
                if (tem_conta == '') {
                    conta_filtro = "Todas ou (Clique p/ selecionar contas)";
                } else {
                    for (var i = 0; i < aChk.length; i++) {
                        if (aChk[i].checked == true) conta_filtro.push(aChk[i].className.trim());
                    }
                }
                $("#contas_selecionadas").val(conta_filtro);
                consultar_ctp(1);
            },
        });
    } else {
        consultar_ctp(1);
    }
});

function ler_busca() {
    var digitado = $("#nome_pesquisa").val();
    if (digitado == '') {
        $("#tela_busca").hide();
    } else {
        $.post("fetch_busca.php", {query: digitado}, function (valor) {
            $("div[id=lido]").html(valor);
            $("#tela_busca").show();
        });
    }
}

function sair_busca() {
    $("#nome_pesquisa").val('');
    $("div[id=lido]").html('');
    $("#tela_busca").hide();
}

function informacoes_uso() {
    $("#ajuda").modal();
}

function formatMoney(n, c, d, t) {
    c = isNaN(c = Math.abs(c)) ? 2 : c;
    d = d == undefined ? "," : d;
    t = t == undefined ? "." : t;
    s = n < 0 ? "-" : "";
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
    j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-br-pre": function (a) {
        if (a == null || a == "") return 0;
        var brDatea = a.split('/');
        return (brDatea[2] + brDatea[1] + brDatea[0]) * 1;
    },
    "date-br-asc": function (a, b) { return ((a < b) ? -1 : ((a > b) ? 1 : 0)); },
    "date-br-desc": function (a, b) { return ((a < b) ? 1 : ((a > b) ? -1 : 0)); }
});

function exibe_filtros_aceite() {
    $("#modal_filtro_aceite").modal("show");
}

$(document).ready(function () {
    $.fn.selectpicker.defaults = {
        deselectAllText: 'Limpar Seleção',
        actionsBox: true,
    };

    $('.selectpicker').each(function () {
        const $selectElement = $(this);
        $selectElement.selectpicker();

        const $dropdownMenu = $selectElement.closest('.bootstrap-select').find('.dropdown-menu');
        const $actionsBox = $dropdownMenu.find('.bs-actionsbox');
        if (!$actionsBox.length) return;

        $actionsBox.css('text-align', 'right');
        $actionsBox.find('.bs-select-all').remove();

        function atualizarVisibilidadeActionsBox() {
            const selecoes = $selectElement.val();
            if (selecoes && selecoes.length > 0) {
                $actionsBox.show();
            } else {
                $actionsBox.hide();
            }
        }

        atualizarVisibilidadeActionsBox();
        $selectElement.on('changed.bs.select', atualizarVisibilidadeActionsBox);
    });

    $('#tabela_aceite_contas').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
            "sSearch":       "Buscar na lista:",
            "zeroRecords":   "Nada encontrado",
            "info":          "Registros encontrados: _END_ ",
            "infoEmpty":     "Nenhum registro disponível",
            "infoFiltered":  "(filtrado de _MAX_ registros no total)",
            "decimal":       ",",
            "thousands":     ".",
        },
        "aoColumns": [
            null, null, { "orderable": false }, null, null,
            { "sType": "date-br" },
            null,
            { "sType": "date-br" },
            null, null, null
        ],
        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $('table.dataTable').css("width", "100%");
        }
    });

    $('#seleciona_todos_aceite').click(function (event) {
        if (this.checked) {
            $('.checkbox1').each(function () { this.checked = true; });
        } else {
            $('.checkbox1').each(function () { this.checked = false; });
        }
        somar_total_para_baixar();
    });

    $("#contas_selecionadas").click(function () {
        $("#modal_conta").modal("show");
    });

    $.ajax({
        type: "POST",
        url: "lista_conta_contabil.php",
        data: { 'tipo_conta': 'D' },
        success: function (data) {
            $("#modal_conta_info").html(data);
            $('input[name="conta_option"]').each(function () {
                idConta.push($(this).attr("id"));
            });
            var filtro_conta = $("#exibe_conta").val();
            if (filtro_conta != '' && filtro_conta != null) {
                filtro_conta = filtro_conta.split(',');
                $.each(filtro_conta, function (idx, val) {
                    document.getElementById(`${val}`).checked = true;
                });
            }
        },
    });
});

function compareNumbers(a, b) { return a - b; }

function get_marked_boxes(id, el, nivel) {
    if (nivel == 1) {
        if (el.checked) {
            if (selectedConta.indexOf(id) <= -1) selectedConta.push(id);
            for (let conta of idConta) {
                if (conta.charAt(0) == id.charAt(0) && conta != id) {
                    document.getElementById(`${conta}`).checked = true;
                }
                if (selectedConta.indexOf(conta) <= -1 && conta.charAt(0) == id.charAt(0) && conta != id) {
                    selectedConta.push(conta);
                }
            }
            selectedConta.sort(compareNumbers);
            return;
        }
        selectedConta.splice(selectedConta.indexOf(id), 1);
        for (let conta of idConta) {
            if (conta.charAt(0) == id.charAt(0) && conta != id) {
                document.getElementById(`${conta}`).checked = false;
                selectedConta.splice(selectedConta.indexOf(conta), 1);
            }
        }
        selectedConta.sort(compareNumbers);
        return;
    }

    if (nivel == 2) {
        let paiId = id.charAt(0) + "000000";
        if (el.checked) {
            if (selectedConta.indexOf(paiId) <= -1) selectedConta.push(paiId);
            if (selectedConta.indexOf(id) <= -1) selectedConta.push(id);
            for (let conta of idConta) {
                if (conta.charAt(0) == id.charAt(0) && conta.charAt(2) == id.charAt(2) &&
                    selectedConta.indexOf(conta) <= -1 && conta != id) {
                    document.getElementById(`${conta}`).checked = true;
                    selectedConta.push(conta);
                }
            }
            selectedConta.sort(compareNumbers);
            return;
        }
        selectedConta.splice(selectedConta.indexOf(id), 1);
        selectedConta.splice(selectedConta.indexOf(paiId), 1);
        for (let conta of idConta) {
            if (conta.charAt(2) == id.charAt(2) && conta.charAt(0) == id.charAt(0) && conta != id) {
                document.getElementById(`${conta}`).checked = false;
                selectedConta.splice(selectedConta.indexOf(conta), 1);
            }
        }
        selectedConta.sort(compareNumbers);
        return;
    }

    if (nivel == 3) {
        let avoId = id.charAt(0) + "000000";
        let paiId = id.charAt(0) + "0" + id.charAt(2) + "0000";
        if (el.checked) {
            if (selectedConta.indexOf(paiId) <= -1) selectedConta.push(paiId);
            if (selectedConta.indexOf(avoId) <= -1) selectedConta.push(avoId);
            selectedConta.push(id);
            selectedConta.sort(compareNumbers);
            return;
        }
        if (selectedConta[selectedConta.indexOf(id) + 1].charAt(0) == id.charAt(0) &&
            selectedConta[selectedConta.indexOf(id) + 1].charAt(2) == id.charAt(2)) {
            selectedConta.splice(selectedConta.indexOf(id), 1);
            return;
        }
        selectedConta.splice(selectedConta.indexOf(avoId), 1);
        selectedConta.splice(selectedConta.indexOf(paiId), 1);
        selectedConta.sort(compareNumbers);
    }
}

function exibe_contas_selecionadas() {
    var aChk = document.getElementsByName("conta_option");
    var tem_conta = '';
    var conta_filtro = [];
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) tem_conta = 'S';
    }
    if (tem_conta == '') {
        conta_filtro = "Todas ou (Clique p/ selecionar contas)";
    } else {
        for (var i = 0; i < aChk.length; i++) {
            if (aChk[i].checked == true) conta_filtro.push(aChk[i].className.trim());
        }
    }
    $("#contas_selecionadas").val(conta_filtro);
}

function limpa_contas_selecionadas() {
    $("#exibe_conta").val('');
    $.ajax({
        type: 'post',
        url: 'gera_secao_limpa_conta.php',
        data: {limpa: "S"},
        success: function (data) {
            location.href = 'form_contas_pagar_aceite.php';
        },
    });
}

function consultar_ctp(flag) {
    $("#modal_filtro_aceite").modal("hide");

    var data_inicial    = $("#data_inicial").val();
    var data_final      = $("#data_final").val();
    var tipo_data       = $("input[name='tipo_data']:checked").val();
    var razao_nome      = $("#razao_nome").val();
    var codigo_fazenda  = $("#codigo_fazenda").val();

    // Monta filtro de contas
    var aChk = document.getElementsByName("conta_option");
    var array_conta = '';
    var valor_conta = [];
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked) valor_conta.push(aChk[i].value);
    }
    if (valor_conta.length > 0) array_conta = valor_conta.join(",");

    // Monta filtro de fornecedor
    var array_fornecedor = (razao_nome !== null && razao_nome.length > 0)
        ? razao_nome.join(",")
        : '';

    // Monta filtro de fazenda
    var array_fazenda = (codigo_fazenda !== null && codigo_fazenda.length > 0)
        ? codigo_fazenda.join(",")
        : '';

    $("#exibe_conta").val(array_conta);

    $('#lista_contas_pagar').load(
        'form_lista_contas_pagar_aceite.php?data_inicial=' + data_inicial +
        '&data_final='       + data_final +
        '&tipo_data='        + tipo_data +
        '&array_fornecedor=' + array_fornecedor +
        '&array_conta='      + array_conta +
        '&array_fazenda='    + array_fazenda +
        '&limpa_filtros='    + flag,
        function() { $('[data-toggle="tooltip"]').tooltip(); }
    );
}

function expande_tela(expandir) {
    if (expandir) {
        jQuery('#main-content').css({'margin-left': '0px'});
        jQuery('#sidebar').css({'margin-left': '-180px'});
        jQuery('#sidebar > ul').hide();
        jQuery("#container").addClass("sidebar-closed");
    } else {
        jQuery('#main-content').css({'margin-left': '180px'});
        jQuery('#sidebar > ul').show();
        jQuery('#sidebar').css({'margin-left': '0'});
        jQuery("#container").removeClass("sidebar-closed");
    }
}

function aceite_sair() {
    location.href = 'form_contas_pagar.php';
}

function confirmar_aceite() {
    var aChk = document.getElementsByName("id_ctp");
    var tem_conta = "";
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) tem_conta = "S";
    }
    if (tem_conta == "") {
        alert('Não existe contas selecionadas para o aceite.');
        return;
    }

    var contas = [];
    var grupo_contas = "";
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            contas.push(aChk[i].value);
            grupo_contas = contas.join("<|>");
        }
    }

    $.post("aceite_contas_pagar_selecionadas.php", {grupo_contas: grupo_contas}, function (get_retorno) {
        if (get_retorno != 0) {
            alert(get_retorno);
        } else {
            $("#mensagem_retorno").modal();
            $("#mensagem_retorno .modal-body").html('Aceite efetuado com sucesso.');
        }
    });
}

function fechar_aceite_sucesso() {
    document.location.href = "form_contas_pagar_aceite.php";
}

function somar_total_para_baixar() {
    var aChk = document.getElementsByName("id_ctp");
    var tem_conta = "";
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) tem_conta = "S";
    }
    if (tem_conta == "") {
        $("#total_selecionado").val('');
        return;
    }

    var contas = [];
    var grupo_contas = "";
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            contas.push(aChk[i].value);
            grupo_contas = contas.join("<|>");
        }
    }

    $.post("somar_contas_pagar_selecionadas_aceite.php", {grupo_contas: grupo_contas}, function (get_retorno) {
        if (get_retorno != '') {
            $("#total_selecionado").val(formatMoney(get_retorno));
        }
    });
}

function aplicar_filtros() {
    consultar_ctp(2);
}

function limpar_filtros() {
    $("#data_inicial").val('');
    $("#data_final").val('');
    $("#vencimento").prop("checked", true);
    $("#emissao").prop("checked", false);
    $("#codigo_fazenda").val([]);
    $('#codigo_fazenda').selectpicker('val', '');
    $("#razao_nome").val([]);
    $('#razao_nome').selectpicker('val', '');
    $("#contas_selecionadas").val('Todas ou (Clique p/ selecionar contas)');
    $('.selectpicker').selectpicker('refresh');
}

function limpar_filtros_tela_inicial() {
    $("#data_inicial").val('');
    $("#data_final").val('');
    $("#vencimento").prop("checked", true);
    $("#emissao").prop("checked", false);
    $("#codigo_fazenda").val([]);
    $('#codigo_fazenda').selectpicker('val', '');
    $("#razao_nome").val([]);
    $('#razao_nome').selectpicker('val', '');
    $("#contas_selecionadas").val('Todas ou (Clique p/ selecionar contas)');
    $('.selectpicker').selectpicker('refresh');
    $("#exibe_conta").val('');

    $.ajax({
        type: 'post',
        url: 'gera_secao_limpa_conta.php',
        data: {limpa: "S"},
        success: function (data) {
            $.ajax({
                type: "POST",
                url: "lista_conta_contabil.php",
                data: { 'tipo_conta': 'D' },
                success: function (data) {
                    $("#modal_conta_info").html(data);
                    $('input[name="conta_option"]').each(function () {
                        idConta.push($(this).attr("id"));
                    });
                    consultar_ctp(1);
                },
            });
        },
    });
}

/* ================================================================
   EDITOR DE RATEIO — funções (contas_pagar_aceite.js)
   ================================================================ */

var _eratCtpId       = 0;
var _eratPrimeiroCtp = 0;
var _eratValorTotal  = 0;

function _eratFmtMoney(n) {
    n = parseFloat(n) || 0;
    return n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function abrirEditarRateio(ctp_id) {
    _eratCtpId = ctp_id;
    $('#erat_titulo_doc').text('');
    $('#modal_editar_rateio').modal('show');

    $.ajax({
        type: 'POST',
        url: 'get_rateio_json.php',
        data: { ctp_id: ctp_id },
        dataType: 'json',
        timeout: 15000,
        success: function (resp) {
            if (!resp.error) {
                _eratPrimeiroCtp = resp.primeiro_ctp_id;
                _eratValorTotal  = resp.valor_total || 0;
                $('#erat_titulo_doc').text('Documento Nº ' + resp.numero_doc + ' | Valor Total: R$ ' + _eratFmtMoney(_eratValorTotal));
            }
        }
    });
}

function eratSalvar() {
}

// ── RESERVADO: _eratGerarLinha ──
function _eratGerarLinha(ln) {
    ln = ln || {};
    var localId   = ln.local_id   || '';
    var localNome = ln.local_nome || '';
    var ccId      = ln.cc_id      || '';
    var ccNome    = ln.cc_nome    || '';
    var contaId   = ln.conta_id   || '';
    var contaNome = ln.conta_nome || '';
    var valor     = ln.conta_valor > 0 ? _eratFmtMoney(ln.conta_valor) : '';
    var perc      = ln.conta_perc > 0  ? ln.conta_perc.toFixed(2).replace('.', ',') + '%' : '';

    var tr = '<tr class="linha-valor-rateio"' +
        ' data-local-id="'   + localId   + '"' +
        ' data-local-nome="' + localNome.replace(/"/g, '&quot;') + '"' +
        ' data-cc-id="'      + ccId      + '"' +
        ' data-cc-nome="'    + ccNome.replace(/"/g, '&quot;') + '"' +
        ' data-conta-id="'   + contaId   + '"' +
        ' data-conta-nome="' + contaNome.replace(/"/g, '&quot;') + '">';

    tr += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
        '<span class="lbl-parcela">' + localNome + '</span>' +
        ' <a href="#" onclick="eratEditarLocal(this);return false;" style="color:#aaa;font-size:11px;margin-left:4px;" data-toggle="tooltip" title="Editar Local"><i class="far fa-edit"></i></a>' +
        '<input type="hidden" class="erat-local-id"   value="' + localId   + '">' +
        '<input type="hidden" class="erat-local-nome" value="' + localNome + '">' +
        '</td>';

    tr += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
        '<span class="lbl-parcela">' + ccNome + '</span>' +
        ' <a href="#" onclick="eratEditarCC(this);return false;" style="color:#aaa;font-size:11px;margin-left:4px;" data-toggle="tooltip" title="Editar Centro de Custo"><i class="far fa-edit"></i></a>' +
        '<input type="hidden" class="erat-cc-id"   value="' + ccId   + '">' +
        '<input type="hidden" class="erat-cc-nome" value="' + ccNome + '">' +
        '</td>';

    tr += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
        '<span class="lbl-parcela">' + contaNome + '</span>' +
        ' <a href="#" onclick="eratEditarConta(this);return false;" style="color:#aaa;font-size:11px;margin-left:4px;" data-toggle="tooltip" title="Editar Conta Contábil"><i class="far fa-edit"></i></a>' +
        '<input type="hidden" class="erat-conta-id"   value="' + contaId   + '">' +
        '<input type="hidden" class="erat-conta-nome" value="' + contaNome + '">' +
        '</td>';

    tr += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
        '<input type="text" class="form-control rat-valor" placeholder="0,00"' +
        ' value="' + valor + '" style="height:30px;font-size:13px;text-align:right;"></td>';

    tr += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
        '<input type="text" class="form-control rat-perc" placeholder="0,00%"' +
        ' value="' + perc + '" style="height:30px;font-size:13px;text-align:right;"></td>';

    tr += '<td style="text-align:center;vertical-align:middle;">' +
        '<button type="button" class="btn btn-danger btn-xs" onclick="eratRemoverLinha(this)" title="Remover linha"><i class="fas fa-times"></i></button>' +
        '</td>';

    tr += '</tr>';
    return tr;
}

// ── Gera linha manual com selects simples (botão Adicionar Linha) ──
function _eratGerarLinhaManual() {
    var locais = typeof _eratLocais !== 'undefined' ? _eratLocais : [];
    var ccs    = typeof _eratCC    !== 'undefined' ? _eratCC    : [];
    var contas = typeof _eratContas !== 'undefined' ? _eratContas : [];

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
          '<button type="button" class="btn btn-primary btn-xs" onclick="eratConfirmarLinhaManual(this)" style="white-space:nowrap;font-size:11px;padding:3px 7px;margin-bottom:2px;">OK</button> ' +
          '<button type="button" class="btn btn-danger btn-xs" onclick="eratRemoverLinha(this)"><i class="fas fa-times"></i></button>' +
          '</td>';
    tr += '</tr>';
    return tr;
}

function eratConfirmarLinhaManual(btn) {
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

    var novaLinha = $(_eratGerarLinha({
        local_id: localId, local_nome: localNm,
        cc_id: ccId, cc_nome: ccNm,
        conta_id: contaId, conta_nome: contaNm,
        conta_valor: _eratParseVal(valor), conta_perc: 0
    }));
    $tr.replaceWith(novaLinha);
    eratRecalcular();
}

function eratAdicionarLinha() {
    var html = _eratGerarLinhaManual();
    $('#tbody_erat').append(html);
}

function eratRemoverLinha(btn) {
    $(btn).closest('tr').remove();
    eratRecalcular();
}

// ── Inline editor: Local ──
function eratEditarLocal(link) {
    var $td     = $(link).closest('td');
    var $tr     = $td.closest('tr');
    var localId = $tr.find('.erat-local-id').val();
    var locais  = typeof _eratLocais !== 'undefined' ? _eratLocais : [];

    var optLocal = '';
    for (var i = 0; i < locais.length; i++) {
        var sel = (String(locais[i].id) === String(localId)) ? ' selected' : '';
        optLocal += '<option value="' + locais[i].id + '"' + sel + '>' + locais[i].nome + '</option>';
    }

    $td.data('orig-html', $td.html()).html(
        '<select class="form-control" style="height:30px;font-size:12px;display:inline-block;width:auto;max-width:180px;">' + optLocal + '</select>' +
        ' <button type="button" class="btn btn-primary btn-xs" onclick="eratConfirmarLocal(this)">OK</button>' +
        ' <button type="button" class="btn btn-default btn-xs"  onclick="eratCancelarEdicao(this)">X</button>'
    );
}

function eratConfirmarLocal(btn) {
    var $td     = $(btn).closest('td');
    var $tr     = $td.closest('tr');
    var $sel    = $td.find('select');
    var localId = $sel.val();
    var localNm = $sel.find('option:selected').text().trim();

    $tr.find('.erat-local-id').val(localId);
    $tr.find('.erat-local-nome').val(localNm);
    $tr.attr('data-local-id', localId).attr('data-local-nome', localNm);

    $td.html(
        '<span class="lbl-parcela">' + localNm + '</span>' +
        ' <a href="#" onclick="eratEditarLocal(this);return false;" style="color:#aaa;font-size:11px;margin-left:4px;"><i class="far fa-edit"></i></a>' +
        '<input type="hidden" class="erat-local-id"   value="' + localId + '">' +
        '<input type="hidden" class="erat-local-nome" value="' + localNm + '">'
    );
}

// ── Inline editor: Centro de Custo ──
function eratEditarCC(link) {
    var $td  = $(link).closest('td');
    var $tr  = $td.closest('tr');
    var ccId = $tr.find('.erat-cc-id').val();
    var ccs  = typeof _eratCC !== 'undefined' ? _eratCC : [];

    var optCC = '';
    for (var i = 0; i < ccs.length; i++) {
        var sel = (String(ccs[i].id) === String(ccId)) ? ' selected' : '';
        optCC += '<option value="' + ccs[i].id + '"' + sel + '>' + ccs[i].nome + '</option>';
    }

    $td.data('orig-html', $td.html()).html(
        '<select class="form-control" style="height:30px;font-size:12px;display:inline-block;width:auto;max-width:180px;">' + optCC + '</select>' +
        ' <button type="button" class="btn btn-primary btn-xs" onclick="eratConfirmarCC(this)">OK</button>' +
        ' <button type="button" class="btn btn-default btn-xs"  onclick="eratCancelarEdicao(this)">X</button>'
    );
}

function eratConfirmarCC(btn) {
    var $td  = $(btn).closest('td');
    var $tr  = $td.closest('tr');
    var $sel = $td.find('select');
    var ccId = $sel.val();
    var ccNm = $sel.find('option:selected').text().trim();

    $tr.find('.erat-cc-id').val(ccId);
    $tr.find('.erat-cc-nome').val(ccNm);
    $tr.attr('data-cc-id', ccId).attr('data-cc-nome', ccNm);

    $td.html(
        '<span class="lbl-parcela">' + ccNm + '</span>' +
        ' <a href="#" onclick="eratEditarCC(this);return false;" style="color:#aaa;font-size:11px;margin-left:4px;"><i class="far fa-edit"></i></a>' +
        '<input type="hidden" class="erat-cc-id"   value="' + ccId + '">' +
        '<input type="hidden" class="erat-cc-nome" value="' + ccNm + '">'
    );
}

// ── Inline editor: Conta Contábil ──
function eratEditarConta(link) {
    var $td     = $(link).closest('td');
    var $tr     = $td.closest('tr');
    var contaId = $tr.find('.erat-conta-id').val();
    var contas  = typeof _eratContas !== 'undefined' ? _eratContas : [];

    var optConta = '';
    for (var i = 0; i < contas.length; i++) {
        var ct  = contas[i];
        var sel = (String(ct.id) === String(contaId)) ? ' selected' : '';
        if (ct.nivel === 1)      optConta += '<option value="' + ct.id + '" disabled' + sel + ' style="color:#777;font-weight:600;">' + ct.nome + '</option>';
        else if (ct.nivel === 2) optConta += '<option value="' + ct.id + '" disabled' + sel + ' style="color:#888;">&nbsp;&nbsp;&nbsp;&nbsp;' + ct.nome + '</option>';
        else                     optConta += '<option value="' + ct.id + '"' + sel + '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + ct.nome + '</option>';
    }

    $td.data('orig-html', $td.html()).html(
        '<select class="form-control" style="height:30px;font-size:12px;display:inline-block;width:auto;max-width:260px;">' + optConta + '</select>' +
        ' <button type="button" class="btn btn-primary btn-xs" onclick="eratConfirmarConta(this)">OK</button>' +
        ' <button type="button" class="btn btn-default btn-xs"  onclick="eratCancelarEdicao(this)">X</button>'
    );
}

function eratConfirmarConta(btn) {
    var $td     = $(btn).closest('td');
    var $tr     = $td.closest('tr');
    var $sel    = $td.find('select');
    var contaId = $sel.val();
    var contaNm = $sel.find('option:selected').text().trim();

    $tr.find('.erat-conta-id').val(contaId);
    $tr.find('.erat-conta-nome').val(contaNm);
    $tr.attr('data-conta-id', contaId).attr('data-conta-nome', contaNm);

    $td.html(
        '<span class="lbl-parcela">' + contaNm + '</span>' +
        ' <a href="#" onclick="eratEditarConta(this);return false;" style="color:#aaa;font-size:11px;margin-left:4px;"><i class="far fa-edit"></i></a>' +
        '<input type="hidden" class="erat-conta-id"   value="' + contaId + '">' +
        '<input type="hidden" class="erat-conta-nome" value="' + contaNm + '">'
    );
}

function eratCancelarEdicao(btn) {
    var $td = $(btn).closest('td');
    $td.html($td.data('orig-html'));
}

// ── Controle de modo valor/% ──
function _eratSetModo(modo) {
    _eratModo = modo;
    if (modo === 'valor') {
        $('#tbl_erat .rat-valor').prop('readonly', false).css({'background':'','color':''});
        $('#tbl_erat .rat-perc').prop('readonly', true).css({'background':'#f9f9f9','color':'#555'});
    } else if (modo === 'perc') {
        $('#tbl_erat .rat-valor').prop('readonly', true).css({'background':'#f9f9f9','color':'#555'});
        $('#tbl_erat .rat-perc').prop('readonly', false).css({'background':'','color':''});
    } else {
        $('#tbl_erat .rat-valor').prop('readonly', false).css({'background':'','color':''});
        $('#tbl_erat .rat-perc').prop('readonly', false).css({'background':'','color':''});
    }
}

function eratRecalcular() {
    var total = _eratValorTotal;
    var soma  = 0;
    if (_eratModo === 'perc') {
        $('#tbl_erat .rat-perc').each(function () {
            var pct   = _eratParseVal($(this).val());
            var valor = total > 0 ? (pct / 100 * total) : 0;
            $(this).closest('tr').find('.rat-valor').val(valor > 0 ? _eratFmtMoney(valor) : '');
            soma += valor;
        });
    } else {
        $('#tbl_erat .rat-valor').each(function () {
            soma += _eratParseVal($(this).val());
        });
        $('#tbl_erat .rat-valor').each(function () {
            var v   = _eratParseVal($(this).val());
            var pct = total > 0 ? (v / total * 100) : 0;
            $(this).closest('tr').find('.rat-perc').val(pct > 0 ? pct.toFixed(2).replace('.', ',') + '%' : '');
        });
    }
    var rest = total - soma;
    $('#span_rat_total').text('R$ ' + _eratFmtMoney(soma));
    var cor = (Math.abs(rest) < 0.01) ? '#27ae60' : '#c0392b';
    $('#td_rat_vlr_rest').text('R$ ' + _eratFmtMoney(rest)).css('color', cor);
    $('#td_rat_pct_rest').text((total > 0 ? rest / total * 100 : 0).toFixed(2).replace('.', ',') + '%').css('color', cor);
    _eratSetModo(_eratModo);
}

function abrirEditarRateio(ctp_id) {
    _eratCtpId = ctp_id;
    _eratModo  = null;
    $('#erat_aviso').hide();
    $('#tbody_erat').html('<tr><td colspan="6" style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Carregando...</td></tr>');
    $('#erat_titulo_doc').text('');
    $('#span_rat_total').text('R$ 0,00');
    $('#td_rat_vlr_rest').text('R$ 0,00').css('color', '');
    $('#td_rat_pct_rest').text('0,00%').css('color', '');
    $('#modal_editar_rateio').modal('show');

    $.ajax({
        type: 'POST',
        url: 'get_rateio_json.php',
        data: { ctp_id: ctp_id },
        dataType: 'json',
        timeout: 15000,
        success: function (resp) {
            if (resp.error) {
                $('#erat_aviso').text(resp.message).show();
                $('#tbody_erat').html('');
                return;
            }
            _eratPrimeiroCtp = resp.primeiro_ctp_id;
            _eratValorTotal  = resp.valor_total || 0;
            $('#erat_titulo_doc').text('Documento Nº ' + resp.numero_doc + ' | Valor Total: R$ ' + _eratFmtMoney(_eratValorTotal));

            var linhas = resp.linhas || [];
            if (linhas.length === 0) {
                $('#tbody_erat').html('<tr><td colspan="6" style="text-align:center;color:#888;padding:16px;">Sem dados de rateio.</td></tr>');
                return;
            }
            var html = '';
            for (var i = 0; i < linhas.length; i++) { html += _eratGerarLinha(linhas[i]); }
            $('#tbody_erat').html(html);
            _eratSetModo(null);
            eratRecalcular();
        },
        error: function (xhr, status, err) {
            $('#erat_aviso').text('Erro ao carregar rateio: ' + status).show();
            $('#tbody_erat').html('');
        }
    });
}

$(document).on('keypress', '#tbl_erat .rat-valor', function (e) {
    var c = e.which;
    if (c === 0 || c === 8) return true;
    if (c === 44) { return $(this).val().indexOf(',') === -1; }
    if (c < 48 || c > 57) return false;
    if (_eratModo !== 'valor') _eratSetModo('valor');
    return true;
});
$(document).on('blur', '#tbl_erat .rat-valor', function () {
    var n = _eratParseVal($(this).val());
    $(this).val(n > 0 ? _eratFmtMoney(n) : '');
    eratRecalcular();
});
$(document).on('keypress', '#tbl_erat .rat-perc', function (e) {
    var c = e.which;
    if (c === 0 || c === 8) return true;
    if (c === 44) { return $(this).val().replace('%','').indexOf(',') === -1; }
    if (c < 48 || c > 57) return false;
    if (_eratModo !== 'perc') _eratSetModo('perc');
    return true;
});
$(document).on('blur', '#tbl_erat .rat-perc', function () {
    var raw = $(this).val().replace('%','').replace(',','.');
    var n   = parseFloat(raw) || 0;
    $(this).val(n > 0 ? n.toFixed(2).replace('.', ',') + '%' : '');
    eratRecalcular();
});

function eratSalvar() {
    $('#erat_aviso').hide();
    var linhas = [];
    var valido = true;

    $('#tbody_erat tr.linha-valor-rateio').each(function () {
        var $tr = $(this);

        if ($tr.hasClass('linha-manual')) {
            valido = false;
            $('#erat_aviso').text('Clique em "OK" para confirmar todas as linhas manuais antes de salvar.').show();
            return false;
        }

        var localId   = $tr.find('.erat-local-id').val()   || '';
        var localNome = $tr.find('.erat-local-nome').val() || '';
        var ccId      = $tr.find('.erat-cc-id').val()      || '';
        var ccNome    = $tr.find('.erat-cc-nome').val()    || '';
        var contaId   = $tr.find('.erat-conta-id').val()   || '';
        var contaNome = $tr.find('.erat-conta-nome').val() || '';
        var valor     = _eratParseVal($tr.find('.rat-valor').val());
        var perc      = _eratParseVal($tr.find('.rat-perc').val());

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
        if (!$('#erat_aviso').is(':visible')) {
            $('#erat_aviso').text('Preencha Local e Conta Contábil em todas as linhas.').show();
        }
        return;
    }
    if (linhas.length === 0) {
        $('#erat_aviso').text('Adicione pelo menos uma linha de rateio.').show();
        return;
    }

    var somaVal = 0;
    $('#tbl_erat .rat-valor').each(function() { somaVal += _eratParseVal($(this).val()); });
    var rest = _eratValorTotal - somaVal;
    if (Math.abs(rest) > 0.05) {
        if (!confirm('O valor restante a distribuir é R$ ' + _eratFmtMoney(rest) + '. Deseja salvar mesmo assim?')) return;
    }

    $.ajax({
        type: 'POST',
        url: 'salvar_rateio_editar.php',
        data: { primeiro_ctp_id: _eratPrimeiroCtp, rateio_json: JSON.stringify(linhas) },
        dataType: 'json',
        timeout: 15000,
        success: function (resp) {
            if (resp.error) {
                $('#erat_aviso').text(resp.message).show();
                return;
            }
            $('#modal_editar_rateio').modal('hide');
            setTimeout(function () { toggleRateio(_eratCtpId); }, 400);
        },
        error: function () {
            $('#erat_aviso').text('Erro de comunicação ao salvar rateio.').show();
        }
    });
}
