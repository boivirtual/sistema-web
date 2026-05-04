/**CONTAS A RECEBER*/
const idConta = [];

$(window).load(function () {
    // Exibe filtros quando faz reload
    var filtro_local = $("#exibe_local").val();
 
    if (filtro_local!='' && filtro_local!=null) {
        var filtro_local = filtro_local.split(',');

        $.each(filtro_local, function(idx, val) {
            $('#codigo_fazenda option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_fazenda').selectpicker('refresh');
    }

    var filtro_cliente = $("#exibe_cliente").val();
 
    if (filtro_cliente!='' && filtro_cliente!=null) {
        var filtro_cliente = filtro_cliente.split(',');

        $.each(filtro_cliente, function(idx, val) {
            $('#razao_nome option[value=' + val + ']').attr('selected', true);
        });

        $('#razao_nome').selectpicker('refresh');
    }

    var filtro_cc = $("#exibe_cc").val();

    if (filtro_cc!='' && filtro_cc!=null) {
        var filtro_cc = filtro_cc.split(',');

        $.each(filtro_cc, function(idx, val) {
            $('#codigo_cc option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_cc').selectpicker('refresh');
    }

    var filtro_conta = $("#exibe_conta").val();
    var limpa_filtro_contas = $("#limpar_filtro_contas").val();

    if (limpa_filtro_contas=='S') {
        $("#contas_selecionadas").val('Todas ou (Clique p/ selecionar contas)');

        $.ajax({
            type: "POST",
            url: "lista_conta_contabil.php",
            data: {
            'tipo_conta': 'C'
            },
            success: function (data) {
                $("#modal_conta_info").html(data);

                $('input[name="conta_option"]').each(function (element) {
                    idConta.push($(this).attr("id"));
                });

                $("#modal_conta").modal("show");
                $('.consultar').show();
                $('.filtros').hide();
            },
        });
    }
    else if (filtro_conta!='' && filtro_conta!=null) {
        $.ajax({
            type: "POST",
            url: "lista_conta_contabil.php",
            data: {
            'tipo_conta': 'C'
            },
            success: function (data) {
                $("#modal_conta_info").html(data);

                $('input[name="conta_option"]').each(function (element) {
                    idConta.push($(this).attr("id"));
                });

                var filtro_conta = $("#exibe_conta").val();
                var filtro_conta = filtro_conta.split(',');

                $.each(filtro_conta, function(idx, val) {
                    document.getElementById(`${val}`).checked = true;
                });

                var aChk = document.getElementsByName("conta_option");
                var tem_conta = '';
                var conta_filtro = [];

                for (var i = 0; i < aChk.length; i++) {
                    if (aChk[i].checked == true) {
                        tem_conta = 'S';
                    }
                }

                if (tem_conta=='') {
                    conta_filtro = "Todas ou (Clique p/ selecionar contas)";
                }
                else {
                    for (var i = 0; i < aChk.length; i++) {
                        if (aChk[i].checked == true) {
                            var desc = aChk[i].className;
                            conta_filtro.push(desc.trim());
                        }
                    }
                }

                $("#contas_selecionadas").val(conta_filtro);

                var lista_ctr_automatico = $("#lista_ctr_automatico").val();

                if (lista_ctr_automatico=="S") {
                    consultar_ctr();
                }
            },
        });
    }
    else {
        var lista_ctr_automatico = $("#lista_ctr_automatico").val();

        if (lista_ctr_automatico=="S") {
            consultar_ctr();
        }
    }
    // Fim exibe filtros 

    $.post("lista_local.php", { tipo: 1 }, function (valor) {
        $("select[name=codigo_local]").html(valor);
        $("option[value=000000000]").html("...");
    });
    var situacao_conta = $("#desc_situacao").val();

    if (situacao_conta != "Pago") {
        $("#baixa_conta_receber").show();
    } else {
        $("#baixa_conta_receber").hide();
    }
});

function ler_busca() {
    var digitado = $("#nome_pesquisa").val();

    if (digitado=='') {
        $("#tela_busca").hide();
    }
    else {
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

var mask = {
    money: function () {
        var el = this,
            exec = function (v) {
                v = v.replace(/\D/g, "");
                v = new String(Number(v));
                var len = v.length;
                if (1 == len) v = v.replace(/(\d)/, "0.0$1");
                else if (2 == len) v = v.replace(/(\d)/, "0.$1");
                else if (len > 2) {
                    v = v.replace(/(\d{2})$/, ".$1");
                }
                return v;
            };

        setTimeout(function () {
            el.value = exec(el.value);
        }, 1);
    },
};

function formatMoney(n, c, d, t) {
    (c = isNaN((c = Math.abs(c))) ? 2 : c),
        (d = d == undefined ? "," : d),
        (t = t == undefined ? "." : t),
        (s = n < 0 ? "-" : ""),
        (i = parseInt((n = Math.abs(+n || 0).toFixed(c))) + ""),
        (j = (j = i.length) > 3 ? j % 3 : 0);
    return (
        s +
        (j ? i.substr(0, j) + t : "") +
        i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) +
        (c
            ? d +
              Math.abs(n - i)
                  .toFixed(c)
                  .slice(2)
            : "")
    );
}

function replace_valor(valor_replace) {
    valor_replace = valor_replace.replace(".", "");
    valor_replace = valor_replace.replace(",", ".");
    return valor_replace;
}

function verifica_virgula(vlr) {
    var virgula = "";

    for (i = 0; i < vlr.length; i++) {
        if (vlr.charAt(i) == ",") {
            virgula = ",";
        }
    }
    return virgula;
}

function digita_valor() {
    $("#vlr_parcela").bind("keypress", mask.money);
    $("#valor_mensal").bind("keypress", mask.money);
    $("#vlr_juros").bind("keypress", mask.money);
    $("#vlr_desconto").bind("keypress", mask.money);
    $("#vlr_acrescimo").bind("keypress", mask.money);
    $("#valor_pagamento").bind("keypress", mask.money);
    $("#vlr_pagamento").bind("keypress", mask.money);
}

function exibe_valor_parcela() {
    var vlr_parcela = $("#vlr_parcela").val();
    if (verifica_virgula(vlr_parcela) == ",") {
        vlr_parcela = replace_valor(vlr_parcela);
    }
    $("#vlr_parcela").val(formatMoney(vlr_parcela));
}

function exibe_valor_mensal() {
    var valor_mensal = $("#valor_mensal").val();
    if (verifica_virgula(valor_mensal) == ",") {
        valor_mensal = replace_valor(valor_mensal);
    }
    $("#valor_mensal").val(formatMoney(valor_mensal));
}

function exibe_valor_juros() {
    var vlr_juros = $("#vlr_juros").val();
    if (verifica_virgula(vlr_juros) == ",") {
        vlr_juros = replace_valor(vlr_juros);
    }
    $("#vlr_juros").val(formatMoney(vlr_juros));
}

function exibe_valor_desconto() {
    var vlr_desconto = $("#vlr_desconto").val();
    if (verifica_virgula(vlr_desconto) == ",") {
        vlr_desconto = replace_valor(vlr_desconto);
    }
    $("#vlr_desconto").val(formatMoney(vlr_desconto));
}

function exibe_valor_acrescimo() {
    var vlr_acrescimo = $("#vlr_acrescimo").val();
    if (verifica_virgula(vlr_acrescimo) == ",") {
        vlr_acrescimo = replace_valor(vlr_acrescimo);
    }
    $("#vlr_acrescimo").val(formatMoney(vlr_acrescimo));
}

function exibe_valor_pagamento() {
    var valor_pagamento = $("#valor_pagamento").val();
    if (verifica_virgula(valor_pagamento) == ",") {
        valor_pagamento = replace_valor(valor_pagamento);
    }
    $("#valor_pagamento").val(formatMoney(valor_pagamento));
}

function exibe_vlr_pagamento() {
    var vlr_pagamento = $("#vlr_pagamento").val();
    if (verifica_virgula(vlr_pagamento) == ",") {
        vlr_pagamento = replace_valor(vlr_pagamento);
    }
    $("#vlr_pagamento").val(formatMoney(vlr_pagamento));
}

/** chamada da rotina para enviar registro de clientes para lixeira*/
function enviar_lixeira($doc, $parcela, $id_ctr) {

    if (window.confirm("Confirma excluir esse registro? "+$doc+"/"+$parcela)) {
        $.post(
            "excluir_contas_receber.php",
            { id_ctr: $id_ctr },
            function (valor) {
                var php = valor.split("<|>");

                if (php[0] == 9) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(php[1]);
                    return;
                } else if (php[0] == 0) {
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(php[1]);
                    return;
                }
            }
        );
    }
            
    
}

jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-br-pre": function (a) {
        if (a == null || a == "") {
            return 0;
        }
        var brDatea = a.split("/");
        return (brDatea[2] + brDatea[1] + brDatea[0]) * 1;
    },

    "date-br-asc": function (a, b) {
        return a < b ? -1 : a > b ? 1 : 0;
    },

    "date-br-desc": function (a, b) {
        return a < b ? 1 : a > b ? -1 : 0;
    },
});

$(document).ready(function () {
    $("#tabela_contas_receber").DataTable({
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
          "sSearch": "Buscar na lista:",
          "zeroRecords": "Nada encontrado",
          "info": "Registros encontrados: _END_ ",
          "infoEmpty": "Nenhum registro disponível",
          "infoFiltered": "(filtrado de _MAX_ registros no total)",
        "decimal": ",",
        "thousands": ".",
        },
       "aoColumns": [
            null,
            null,
            null,
            null,
            { sType: "date-br" },
            { sType: "date-br" },
            null,
            null,
            { sType: "date-br" },
            null,
            null,
        ],
    });

    $(".fecha_editar_dados").click(function () {
        location.href = "form_contas_receber.php";
    });

    $(".fecha_dados_baixa").click(function () {
        $(".dados_baixa").modal("hide");
    });

    $(".exibir_filtro").click(function () {
        $(".filtro_escondido").hide();
        $(".filtro_exibido").show();
    });

    $(".esconder_filtro").click(function () {
        $(".filtro_escondido").show();
        $(".filtro_exibido").hide();
    });

    $(".confirma_gravar_ctr").click(function () {
        $("#errors").html("");
        let dados = $("#form_gravar_contas_receber").serialize();

        $(".confirma_gravar_ctr").attr("disabled", true); 
               
        $.ajax({
            type: "POST",
            url: "gravar_contas_receber.php",
            data: dados,
            success: function (data) {
                if (data.error) {
                    $(".confirma_gravar_ctr").attr("disabled", false); 
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                } else {
                    $(".confirma_gravar_ctr").attr("disabled", false); 
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            },
        });
    });

    $("#repetir").click(function (event) {
        if (this.checked) {
            $(".checkbox1").each(function () {
                this.checked = true;
                $("#dados_mensalidades").show();
                $("#sel_frequencia").show();
                $("#contas_mensalidades").show();
            });
        } else {
            $(".checkbox1").each(function () {
                this.checked = false;
                $("#dados_mensalidades").hide();
                $("#sel_frequencia").hide();
                $("#contas_mensalidades").hide();
                $("#frequencia").val(0);
                $("#ocorrencias").val("");
                $("#valor_mensal").val("");
                $("#conta_mensal").val(0);
                $("#data_inicial").val("");
                $("#forma_pgto_mensal").val(0);
                $("#conta_pgto_mensal").val(0);
            });
        }
    });

    $("#pago").click(function (event) {
        if (this.checked) {
            $(".checkbox3").each(function () {
                this.checked = true;
                $("#dados_pagamento").show();
                var data_pagamento = $("#vencimento_primeira_parcela").val();
                var valor_pago = $("#vlr_parcela").val();
                if (verifica_virgula(valor_pago) == ",") {
                    valor_pago = replace_valor(valor_pago);
                }
                $("#data_pagamento").val(data_pagamento);
                $("#vlr_pagamento").val(formatMoney(valor_pago));
            });
        } else {
            $(".checkbox3").each(function () {
                this.checked = false;
                $("#dados_pagamento").hide();
                $("#data_pagamento").val("");
            });
        }
    });

    $("#vlr_desconto").change(function () {
        var vlr_desconto = $("#vlr_desconto").val();
        var vlr_juros = $("#vlr_juros").val();
        var vlr_pago = $("#vlr_parcela").val();

        if (vlr_desconto == "") {
            vlr_desconto = 0.0;
        } else {
            if (verifica_virgula(vlr_desconto) == ",") {
                vlr_desconto = replace_valor(vlr_desconto);
            }
        }

        if (vlr_juros == "") {
            vlr_juros = 0.0;
        } else {
            if (verifica_virgula(vlr_juros) == ",") {
                vlr_juros = replace_valor(vlr_juros);
            }
        }

        if (verifica_virgula(vlr_pago) == ",") {
            vlr_pago = replace_valor(vlr_pago);
        }

        var vrl_juros_soma = parseFloat(vlr_juros);
        var vrl_desconto_sub = parseFloat(vlr_desconto);
        var vlr_pago_result = parseFloat(vlr_pago);

        vlr_pago = vlr_pago_result - vrl_desconto_sub + vrl_juros_soma;

        $("#vlr_pagamento").val(formatMoney(vlr_pago));
    });

    $("#vlr_juros").change(function () {
        var vlr_desconto = $("#vlr_desconto").val();
        var vlr_juros = $("#vlr_juros").val();
        var vlr_pago = $("#vlr_parcela").val();

        if (vlr_desconto == "") {
            vlr_desconto = 0.0;
        } else {
            if (verifica_virgula(vlr_desconto) == ",") {
                vlr_desconto = replace_valor(vlr_desconto);
            }
        }

        if (vlr_juros == "") {
            vlr_juros = 0.0;
        } else {
            if (verifica_virgula(vlr_juros) == ",") {
                vlr_juros = replace_valor(vlr_juros);
            }
        }

        if (verifica_virgula(vlr_pago) == ",") {
            vlr_pago = replace_valor(vlr_pago);
        }

        var vrl_juros_soma = parseFloat(vlr_juros);
        var vrl_desconto_sub = parseFloat(vlr_desconto);
        var vlr_pago_result = parseFloat(vlr_pago);

        vlr_pago = vlr_pago_result - vrl_desconto_sub + vrl_juros_soma;

        $("#vlr_pagamento").val(formatMoney(vlr_pago));
    });

    $("#codigo_conta_rec").change(function () {
        var codigo = $("#codigo_conta_rec").val();

        $.post("ler_conta_pagamento.php", { codigo: codigo }, function (valor) {
            var php = valor.split("<|>");

            /* if (php[0]==9){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(php[1]);
                return;
            }
          */
            if (php[2] != 0) {
                $(".cheque").show();
            } else {
                $(".cheque").hide();
            }
            return;
        });
    });

    $("#classe_pessoa").change(function () {
        var classe_pessoa = $("#classe_pessoa").val();

        if (classe_pessoa == 02) {
            $("#servico_fornecedor").show();
        } else {
            $("#servico_fornecedor").hide();
        }
    });

    $("#seleciona_todos_somar").click(function (event) {
        if (this.checked) {
            $(".checkbox1").each(function () {
                this.checked = true;
            });
        } else {
            $(".checkbox1").each(function () {
                this.checked = false;
            });
        }
        somar_total_para_baixar();
    });

    // Acende o botão consultar se houver alteracao nos filtros de Contas a Pagar
    $('#data_inicial').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#data_final').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('.radio-inline').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#codigo_fazenda').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#codigo_cc').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#razao_nome').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });
    // Fim acendo botão 

    $("#contas_selecionadas").click(() => {
        $("#modal_conta").modal("show");
        $('.consultar').show();
        $('.filtros').hide();

    });

    $.ajax({
        type: "POST",
        url: "lista_conta_contabil.php",
        data: {
            'tipo_conta': 'C'
        },
        success: function (data) {
            $("#modal_conta_info").html(data);

            $('input[name="conta_option"]').each(function (element) {
                idConta.push($(this).attr("id"));
            });

            var filtro_conta = $("#exibe_conta").val();

            if (filtro_conta!='' && filtro_conta!=null) {
                var filtro_conta = filtro_conta.split(',');

                $.each(filtro_conta, function(idx, val) {
                    document.getElementById(`${val}`).checked = true;
                });
            }
        },
    });
});

const selectedConta = [];

function compareNumbers(a, b) {
    return a - b;
}

function get_marked_boxes(id, el, nivel) {
    if (nivel == 1) {
        if (el.checked) {
            if (selectedConta.indexOf(id) <= -1) {
                selectedConta.push(id);
            }
            for (let conta of idConta) {
                if (conta.charAt(0) == id.charAt(0) && conta != id) {
                    document.getElementById(`${conta}`).checked = true;
                }
                if (
                    selectedConta.indexOf(conta) <= -1 &&
                    conta.charAt(0) == id.charAt(0) &&
                    conta != id
                ) {
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
            if (selectedConta.indexOf(paiId) <= -1) {
                selectedConta.push(paiId);
            }
            if (selectedConta.indexOf(id) <= -1) {
                selectedConta.push(id);
            }
            for (let conta of idConta) {
                if (
                    conta.charAt(0) == id.charAt(0) &&
                    conta.charAt(2) == id.charAt(2) &&
                    selectedConta.indexOf(conta) <= -1 &&
                    conta != id
                ) {
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
            if (
                conta.charAt(2) == id.charAt(2) &&
                conta.charAt(0) == id.charAt(0) &&
                conta != id
            ) {
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
            if (selectedConta.indexOf(paiId) <= -1) {
                selectedConta.push(paiId);
            }
            if (selectedConta.indexOf(avoId) <= -1) {
                selectedConta.push(avoId);
            }
            selectedConta.push(id);
            selectedConta.sort(compareNumbers);
            return;
        }

        if (
            selectedConta[selectedConta.indexOf(id) + 1].charAt(0) ==
                id.charAt(0) &&
            selectedConta[selectedConta.indexOf(id) + 1].charAt(2) ==
                id.charAt(2)
        ) {
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
        if (aChk[i].checked == true) {
            tem_conta = 'S';
        }
    }

    if (tem_conta=='') {
        conta_filtro = "Todas ou (Clique p/ selecionar contas)";
    }
    else {
        for (var i = 0; i < aChk.length; i++) {
            if (aChk[i].checked == true) {
                var desc = aChk[i].className;
                conta_filtro.push(desc.trim());
            }
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
        success: function(data) {
            location.href='form_contas_receber.php';
        },
    });
}

function consultar_ctr() {
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var tipo_data = $("input[name='tipo_data']:checked").val();
    var razao_nome = $("#razao_nome").val();
    var codigo_fazenda = $("#codigo_fazenda").val();
    var codigo_cc = $("#codigo_cc").val();

    var aChk = document.getElementsByName("conta_option");
    var tem_conta = '';
    var j = 0;
    var conta_filtro = [];

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            tem_conta = 'S';
        }
    }

    if (tem_conta=='') {
        var array_conta= new Array();
        conta_filtro = "Conta: Todas";
    }
    else {
        var array_conta = new Array();
        var valor = new Array();

        for (var i = 0; i < aChk.length; i++) {
            if (aChk[i].checked == true) {
                valor[j] = aChk[i].value;
                j++;

                var desc = aChk[i].className;
                conta_filtro.push(desc.trim());
            }
        }
        var array_conta=valor.join(",");
    }

    conta_filtro = "Conta: " + conta_filtro;

    if (razao_nome==null) {
        var array_cliente= new Array();
    }
    else {
        var array_cliente = new Array();
        var valor = new Array();

        for (i = 0; i <= razao_nome.length; i++) {
            valor[i]=razao_nome[i];
        }

        var array_cliente=valor.join(",");
    }

    if (codigo_fazenda==null) {
        var array_fazenda= new Array();
    }
    else {
        var array_fazenda = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_fazenda.length; i++) {
            valor[i]=codigo_fazenda[i];
        }

        var array_fazenda=valor.join(",");
    }

    if (codigo_cc==null) {
        var array_cc= new Array();
    }
    else {
        var array_cc = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_cc.length; i++) {
            valor[i]=codigo_cc[i];
        }

        var array_cc=valor.join(",");
    }

    if (data_inicial == "" && data_final == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe as Datas para Consulta.");
        return;
    }

    var options = $("#razao_nome option:selected");
    var fornecedor_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#razao_nome").text();
        fornecedor_filtro.push(desc.trim());
    });

    if (fornecedor_filtro != "") {
        fornecedor_filtro = fornecedor_filtro + "->";
    } else {
        fornecedor_filtro = "";
    }

    var options = $("#codigo_fazenda option:selected");
    var codigo_local_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_fazenda").text();
        codigo_local_filtro.push(desc.trim());
    });

    if (codigo_local_filtro != "") {
        codigo_local_filtro = "Local: " + codigo_local_filtro + "->";
    } else {
        codigo_local_filtro = "Local: Todos->";
    }

    var options = $("#codigo_cc option:selected");
    var codigo_cc_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_cc").text();
        codigo_cc_filtro.push(desc.trim());
    });

    if (codigo_cc_filtro != "") {
        codigo_cc_filtro = "C.Custos: " + codigo_cc_filtro + "->";
    } else {
        codigo_cc_filtro = "C.Custos: Todos->";
    }

    var data_ini = data_inicial.split("-");
    var dia_ini = data_ini[2];
    var mes_ini = data_ini[1];
    var ano_ini = data_ini[0];

    var data_fim = data_final.split("-");
    var dia_fim = data_fim[2];
    var mes_fim = data_fim[1];
    var ano_fim = data_fim[0];
    periodo =
        "Período: de " +
        dia_ini +
        "/" +
        mes_ini +
        "/" +
        ano_ini +
        " ate " +
        dia_fim +
        "/" +
        mes_fim +
        "/" +
        ano_fim +
        "->";

    if (tipo_data == "V") {
        opc_data_filtro = "Dt Vencimento->";
    } else if (tipo_data == "E") {
        opc_data_filtro = "Dt Emissão->";
    } else {
        opc_data_filtro = "Dt Pagamento->";
    }

    var descricao_filtro =
        codigo_local_filtro +
        codigo_cc_filtro +
        periodo +
        opc_data_filtro +
        fornecedor_filtro +
        conta_filtro;

    $(".digitar_filtros").hide();
    $(".filtros").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".descricao_filtro").html(descricao_filtro);

    $("#exibe_conta").val(array_conta);

    $("#aguardar").modal("show");

    $('#lista_contas_receber').load('form_lista_contas_receber.php?data_inicial=' + data_inicial + 
     '&data_final=' + data_final + 
     '&tipo_data=' + tipo_data + 
     '&array_cliente=' + array_cliente + 
     '&array_fazenda=' + array_fazenda + 
     '&array_cc=' + array_cc + 
     '&array_conta=' + array_conta);
    return;
}

function exibe_mais_filtros() {
    $(".digitar_filtros").show();
    $(".mais_filtros").hide();
    $(".menos_filtros").show();
    $(".consultar").hide();
    $(".lista_contas").hide();
}

function exibe_menos_filtros() {
    $(".digitar_filtros").hide();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".lista_contas").show();
}

function mais_relatorios() {
    location.href= 'form_rel_analise_recebimento.php?tipo=2';
}

function somar_total_para_baixar() {
    var aChk = document.getElementsByName("id_ctr");

    var tem_conta = "";

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            tem_conta = "S";
        }
    }
    if (tem_conta == "") {
        $("#total_baixar").val("");
        $(".dados_baixa").modal("hide");
        $(".confirmar_baixa_selecionados").hide();
        return;
    }

    var contas = new Array();
    var grupo_contas = "";
    var aChk = document.getElementsByName("id_ctr");

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            chave_ctr = aChk[i].value;
            contas.push(chave_ctr);
            grupo_contas = contas.join("<|>");
        }
    }

    $.post(
        "somar_contas_receber_selecionadas.php",
        { grupo_contas: grupo_contas },
        function (get_retorno) {
            if (get_retorno == 999) {
                $("#total_baixar").val("");
                $(".dados_baixa").modal("hide");
                $(".checkbox1").each(function () {
                    this.checked = false;
                });
                $("#seleciona_todos_somar").each(function () {
                    this.checked = false;
                });
            } else if (get_retorno == 9) {
                alert("ATENCAO! Selecione contas com vencimentos iguais.");
                $("#total_baixar").val("");
                $(".dados_baixa").modal("hide");
                $(".confirmar_baixa_selecionados").hide();
                //$('.checkbox1').each(function() {
                //this.checked = false;
                //});
                //$('#seleciona_todos_somar').each(function() {
                //  this.checked = false;
                //});
            } else if (get_retorno == 99) {
                alert(
                    "ATENCAO! Selecione contas com contas de pagamento iguais."
                );
                $("#total_baixar").val("");
                $(".dados_baixa").modal("hide");
                $(".confirmar_baixa_selecionados").hide();
                //$('.checkbox1').each(function() {
                // this.checked = false;
                // });
                //$('#seleciona_todos_somar').each(function() {
                // this.checked = false;
                //});
            } else {
                var php = get_retorno.split("<|>");
                var total_baixar = formatMoney(php[0]);
                var data_vencimento = php[1];
                var conta_pagamento = php[2];

                $("#total_baixar").val(total_baixar);
                $("#data_pagamento").val(data_vencimento);
                $("#codigo_conta_rec").val(conta_pagamento);
                $(".confirmar_baixa_selecionados").show();
            }
        }
    );
}

function modal_baixar() {
    $(".dados_baixa").modal("show");
}

$("#modal_baixar").on("show.bs.modal", function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var doc_id = button.data("wid");
    var num_doc = button.data("wdoc");
    var num_parcela = button.data("wparcela");
    var valor = formatMoney(button.data("wvalor"));
    var vencimento = button.data("wvencimento");
    var conta_pag = button.data("wcontapag");
    var modal = $(this);

    modal.find("#id_baixar").val(doc_id);
    modal.find("#number_doc_baixar").val(num_doc);
    modal.find("#number_parcela_baixar").val(num_parcela);
    modal.find("#vlr_total_baixar").val(valor);
    modal.find("#data_pagamento_baixar").val(vencimento);
    modal.find("#codigo_conta_pagto_baixar").val(conta_pag);
});

function baixar_conta_selecionada() {
    var ctr_id = $("#id_baixar").val();
    var numero_doc = $("#number_doc_baixar").val();
    var parcela_doc = $("#number_parcela_baixar").val();
    var total_baixar = $("#vlr_total_baixar").val();
    var data_pagamento = $("#data_pagamento_baixar").val();
    var conta_pag = $("#codigo_conta_pagto_baixar").val();

    if (numero_doc == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Infome o número do documento");
        return;
    }

    $.post(
        "gravar_baixa_contas_receber_selecionada.php",
        {   ctr_id: ctr_id,
            num_doc: numero_doc,
            num_parcela: parcela_doc,
            data_pagamento: data_pagamento,
            conta_pag: conta_pag,
        },
        function (get_retorno) {
            if (get_retorno != 0) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(get_retorno);
            } else {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(
                    "Baixa efetuada com sucesso"
                );
            }
        }
    );
}

function baixar_contas_selecionadas() {
    var total_baixar = $("#total_baixar").val();
    var data_pagamento = $("#data_pagamento").val();
    var conta_rec = $("#codigo_conta_rec").val();

    if (total_baixar == 0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Informe o valor total para baixar"
        );
        return;
    }

    if (data_pagamento == 0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe a data do pagamento");
        return;
    }

    if (conta_rec == 0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe a conta do pagamento");
        return;
    }

    var contas = new Array();
    var grupo_contas = "";
    var aChk = document.getElementsByName("id_ctr");

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            chave_ctr = aChk[i].value;

            contas.push(chave_ctr);
            grupo_contas = contas.join("<|>");
        }
    }

    $.post(
        "gravar_baixa_contas_receber_selecionadas.php",
        {
            grupo_contas: grupo_contas,
            data_pagamento: data_pagamento,
            conta_rec: conta_rec,
        },
        function (get_retorno) {
            if (get_retorno != 0) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(get_retorno);
            } else {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(
                    "Baixas efetuadas com sucesso"
                );
            }
        }
    );
}

function estornar_baixa_contas_receber($bcr_id, $numero_doc, $numero_parcela, $sequencia_baixa) {
    if (
        window.confirm(
            "Confirma o estorno dessa baixa?" + " " + $numero_doc +" - "+ $numero_parcela
        )
    ) {
        $.post(
            "estornar_baixa_contas_receber.php",
            { bcr_id: $bcr_id, numero_parcela: $numero_parcela, sequencia_baixa:$sequencia_baixa},
            function (get_retorno) {
                if (get_retorno == 0) {
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(
                        "Estorno efetuado com sucesso"
                    );
                } else {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(get_retorno);
                }
            }
        );
    }
}

function gerar_alerta(mensagem) {
    return (
        '<div class="col-md-12"><div class="alert alert-danger fade in">' +
        '<button data-dismiss="alert" class="close close-sm" type="button">' +
        '<span aria-hidden="true">×</span>' +
        "</button>" +
        mensagem
    );
    ("</div></div>");
}

function baixar_conta_receber() {
    var grupo_usuario = $("#grupo_usuario").val();

    if (grupo_usuario == 1 || grupo_usuario == 2) {
        var dados = $("#form_gravar_contas_receber").serialize();

        $.ajax({
            type: "POST",
            url: "gravar_contas_receber.php",
            data: dados,
            success: function (data) {
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
            },
        });
    }

    $("#img_aguarde_baixa").show();

    tout = setTimeout("exibir_dados_baixa()", 1000);
}

function exibir_dados_baixa() {
    $("#img_aguarde_baixa").hide();
    $("#baixar_conta").show();
    $("#baixa_conta_receber").hide();
    $("#confirma_gravar_ctr").hide();

    var data_pagamento = $("#data_vencimento").val();

    $("#data_pagamento").val(data_pagamento);

    var numero_ctr = $("#number_doc").val();
    var parcela_ctr = $("#number_parcela").val();
    var ctr_id = $("#id_ctr").val();

    $.post(
        "ler_baixa_contas_receber.php",
        { numero_ctr: numero_ctr, parcela_ctr: parcela_ctr, ctr_id: ctr_id},
        function (valor) {
            var php = valor.split("<|>");

            var valor_parcela = formataValor(php[0]);
            var valor_desconto = formataValor(php[1]);
            var valor_juros = formataValor(php[2]);
            var valor_acrescimo = formataValor(php[3]);
            var valor_pago = formataValor(php[4]);
            var valor_total = 0;

            valor_total = valor_parcela - valor_desconto;
            valor_total = valor_total + valor_juros;
            valor_total = valor_total + valor_acrescimo;
            valor_apagar = valor_total - valor_pago;

            $("#valor_pagamento").val(formatMoney(valor_apagar));

            var historico_pagamento = "Recebimento de: " + php[9];
            $("#historico").val(historico_pagamento);

            document.getElementById("historico").focus();
        }
    );
}

function executar_baixa_conta_receber_individual() {
    var dadosarray = new Array();
    dadosarray[0] = $("#number_doc").val();
    dadosarray[1] = $("#number_parcela").val();
    dadosarray[3] = $("#data_pagamento").val();
    dadosarray[4] = replace_valor($("#valor_pagamento").val());
    dadosarray[5] = $("#codigo_cli_for").val();
    dadosarray[7] = $("#historico").val();
    dadosarray[8] = $("#nome_cli").val();
    dadosarray[9] = $("#id_ctr").val();
    
    $.post(
        "gravar_baixa_contas_receber_individual.php",
        { dadosarray: dadosarray },
        function (get_retorno) {
            if (get_retorno == 0) {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(
                    "Baixa efetuada com sucesso"
                );
            } else {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(get_retorno);
            }
        }
    );
}

function formataValor(valor) {
    if (valor == "" || valor == null) {
        novo_valor = 0;
        return novo_valor;
    } else {
        valor = valor.replace(",", ".");
        valor = parseFloat(valor);
        return valor;
    }
}

function imprimir_contas_receber(opcao) {
    var data_inicio = $("#data_inicial").val();
    var data_fim = $("#data_final").val();
    var codigo_conta = $("#codigo_conta").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_cliente = $("#codigo_cliente").val();
    var tipo_data = $("input[name='tipo_data']:checked").val();

    if (tipo_data == undefined) {
        tipo_data = "";
    }

    var tipo_rel = $("input[name='tipo_rel']:checked").val();

    if (tipo_rel == undefined) {
        tipo_rel = "";
    }

    if (data_inicio == 0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Informe a data inicial do período."
        );
        return;
    }

    if (data_inicio != 0) {
        if (data_fim == 0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Informe a data final do período."
            );
            return;
        }

        if (tipo_data == "") {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html("Informe o tipo da data.");
            return;
        }
    }

    if (tipo_data != "" && data_inicio == 0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Se for informado o Tipo de Data tem que informar o período."
        );
        $("input[name='tipo_data']:checked").attr("checked", false);
        return;
    }

    if (tipo_rel == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe o Tipo do Relatório.");
        return;
    }

    $("#aguardar").modal("show");

    if (opcao == 1) {
        var width = 350;
        var height = 500;
        var left = 40;
        var top = 40;
        window.open(
            "rel_analise_recebimentos_pdf.php?data_inicio=" +
                data_inicio +
                "&data_fim=" +
                data_fim +
                "&conta=" +
                codigo_conta +
                "&tipo_data=" +
                tipo_data +
                "&tipo_rel=" +
                tipo_rel +
                "&centro_custos=" +
                codigo_cc +
                "&codigo_cliente=" +
                codigo_cliente,
            "janela",
            "width=" +
                width +
                ", height=" +
                height +
                ", top=" +
                top +
                ", left=" +
                left +
                ", scrollbars=yes, status=yes, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=yes"
        );

        tout = setTimeout("limpar_tela()", 1000);
    } else {
        location.href =
            "rel_analise_recebimentos_excel.php?data_inicio=" +
            data_inicio +
            "&data_fim=" +
            data_fim +
            "&conta=" +
            codigo_conta +
            "&tipo_data=" +
            tipo_data +
            "&tipo_rel=" +
            tipo_rel +
            "&centro_custos=" +
            codigo_cc +
            "&codigo_cliente=" +
            codigo_cliente;

        tout = setTimeout("limpar_tela()", 1000);
    }
}

function limpar_tela() {
    $("#aguardar").modal("hide");
}

function voltar_filtro() {
    location.href = "form_rel_analise_recebimento.php";
}

function voltar_relatorios() {
    location.href = "form_relatorios_financeiros.php";
}
