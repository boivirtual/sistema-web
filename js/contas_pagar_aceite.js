/**CONTAS A PAGAR ACEITE*/

function toggleRateio(id) {
    $('#modal_rateio_aceite_dyn').remove();

    var modalHtml =
        '<div class="modal fade" id="modal_rateio_aceite_dyn" tabindex="-1" role="dialog" data-backdrop="static">' +
        '<div class="modal-dialog" style="width:92%;max-width:940px;" role="document">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
        '<h4 class="modal-title"><i class="fas fa-sitemap" style="color:#337ab7;margin-right:6px;"></i>Distribuição do Rateio</h4>' +
        '</div>' +
        '<div class="modal-body" style="overflow-x:auto;padding:12px 16px;" id="rateio_aceite_body">' +
        '<i class="fas fa-spinner fa-spin"></i> Carregando...' +
        '</div>' +
        '<div class="modal-footer"><button class="btn btn-default" type="button" data-dismiss="modal">Fechar</button></div>' +
        '</div></div></div>';

    $('body').append(modalHtml);
    $('#modal_rateio_aceite_dyn').modal('show');
    $('#modal_rateio_aceite_dyn').on('hidden.bs.modal', function () { $(this).remove(); });

    $.ajax({
        type: 'POST',
        url: 'get_rateio_aceite.php',
        data: { ctp_id: id },
        success: function (data) {
            $('#rateio_aceite_body').html(data);
        },
        error: function () {
            $('#rateio_aceite_body').html('<p style="color:red;">Erro ao carregar os dados do rateio.</p>');
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
            null, null, null, null, null,
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
        '&limpa_filtros='    + flag
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
