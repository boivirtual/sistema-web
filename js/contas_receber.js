/**CONTAS A RECEBER*/
const idConta = [];

// Filtro ativo pelos cards de resumo (null = sem filtro = Total do Período)
var ctrFiltroAtivo = null;

// Registra filtro customizado do DataTables para os cards
$.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    // Aplica somente na tabela de contas a receber
    if (settings.nTable.id !== 'tabela_contas_receber') return true;
    if (!ctrFiltroAtivo) return true;

    // Lê a categoria calculada pelo PHP no atributo data-categoria da linha
    var row = settings.aoData[dataIndex].nTr;
    var categoria = row ? $(row).attr('data-categoria') : null;

    if (ctrFiltroAtivo === 'vencidos')    return categoria === 'vencido';
    if (ctrFiltroAtivo === 'vencem_hoje') return categoria === 'vencem_hoje';
    if (ctrFiltroAtivo === 'a_vencer')    return categoria === 'a_vencer';
    if (ctrFiltroAtivo === 'pagos')       return categoria === 'pago';
    return true;
});

function toggleRateioCtr(id) {
    $.ajax({
        type: 'POST',
        url: 'get_rateio_ctr.php',
        data: { ctr_id: id },
        timeout: 10000,
        success: function (data) {
            $('#modal_rateio_ctr_dyn').remove();
            var corpo = data || '<p style="color:#888;">Sem dados de rateio.</p>';
            var modalHtml =
                '<div class="modal fade" id="modal_rateio_ctr_dyn" tabindex="-1" role="dialog" data-backdrop="static">' +
                '<div class="modal-dialog" style="width:92%;max-width:940px;" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                '<h4 class="modal-title"><i class="fas fa-sitemap" style="color:#337ab7;margin-right:6px;"></i>Distribuição do Rateio</h4>' +
                '</div>' +
                '<div class="modal-body" style="overflow-x:auto;padding:12px 16px;">' + corpo + '</div>' +
                '<div class="modal-footer">' +
                '<button class="btn btn-primary" type="button" style="float:left;" onclick="$(\'#modal_rateio_ctr_dyn\').modal(\'hide\');abrirEditarRateioCtr(' + id + ');">Editar</button>' +
                '<button class="btn btn-default" type="button" data-dismiss="modal">Fechar</button>' +
                '</div>' +
                '</div></div></div>';
            $('body').append(modalHtml);
            $('#modal_rateio_ctr_dyn').modal('show');
            $('#modal_rateio_ctr_dyn').on('hidden.bs.modal', function () { $(this).remove(); });
        },
        error: function (xhr, status, err) {
            alert('Erro ao carregar rateio: ' + status + (err ? ' — ' + err : ''));
        }
    });
}

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

                consultar_ctr();
                atualizarLinkLimparFiltros();
            },
        });
    }
    else {
        // auto-load feito pelo document.ready em form_contas_receber.php
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
    $("#vlr_primeira_parcela").bind("keypress", mask.money); // tela Contas a Receber - Incluir (novo layout)
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

$(document).ready(function() {
    
    // =================================================================================
    // 1. CONFIGURAÇÃO GLOBAL (Define o texto de "Limpar Seleção" para todos)
    //    Isso elimina a necessidade do atributo data-deselect-all-text no HTML.
    // =================================================================================
    $.fn.selectpicker.defaults = {
        deselectAllText: 'Limpar Seleção',
        actionsBox: true, // Garante que a caixa de ações esteja habilitada
        // Não vamos definir selectAllText aqui para garantir que o elemento
        // seja criado e possamos removê-lo de forma explícita na etapa 3.
    };
    
    // =================================================================================
    // 2. LÓGICA DE VISIBILIDADE E REMOÇÃO (Aplicada a cada selectpicker)
    // =================================================================================
    $('.selectpicker').each(function() {
        
        const $selectElement = $(this);
        
        // Inicializa o selectpicker (usa as defaults acima)
        $selectElement.selectpicker();
        
        // Localiza a barra de ações gerada pelo plugin (onde está o botão Limpar Seleção)
        const $dropdownMenu = $selectElement.closest('.bootstrap-select').find('.dropdown-menu');
        const $actionsBox = $dropdownMenu.find('.bs-actionsbox');

        // Se a barra de ações não for encontrada, paramos para este elemento
        if (!$actionsBox.length) {
            return; 
        }

        // NOVO: Aplica o alinhamento à direita para a barra de ações
        $actionsBox.css('text-align', 'right');
        
        // -----------------------------------------------------------
        // 3. REMOÇÃO FORÇADA do "Selecionar Todos"
        // Esta linha é mantida, pois garante a remoção do botão indesejado,
        // limpando o espaço visual.
        // -----------------------------------------------------------
        $actionsBox.find('.bs-select-all').remove();
        
        // -----------------------------------------------------------
        // 4. FUNÇÃO DE VISIBILIDADE (Mostrar/Ocultar o container da barra de ações)
        // -----------------------------------------------------------
        function atualizarVisibilidadeActionsBox() {
            // Pega o array de valores selecionados
            const selecoes = $selectElement.val(); 
            
            // Se há seleções, mostra a barra de ações; caso contrário, esconde.
            if (selecoes && selecoes.length > 0) {
                $actionsBox.show(); 
            } else {
                $actionsBox.hide(); 
            }
        }
        
        // 5. Executa na inicialização (Estado inicial do formulário)
        atualizarVisibilidadeActionsBox();

        // 6. Monitore o evento de mudança
        $selectElement.on('changed.bs.select', atualizarVisibilidadeActionsBox);
    });
});

$(document).ready(function(){
    // Salva ctr_id no sessionStorage ao clicar em "Editar" para restaurar scroll ao voltar
    $(document).off('click.ctrEdit').on('click.ctrEdit', 'a[href*="form_contas_receber_editar.php"]', function() {
        var m = ($(this).attr('href') || '').match(/[?&]id_ctr=([^&]+)/);
        if (m) sessionStorage.setItem('ctr_retorno_id', m[1]);
    });

    // Salva scroll ANTES do modal abrir (após abrir, Bootstrap põe overflow:hidden no body e scrollTop vira 0)
    // Se o modal fechar sem reload (botão X), limpa para não restaurar posição antiga na próxima vez
    $('#mensagem_retorno')
        .off('show.bs.modal.ctrScroll hidden.bs.modal.ctrScroll')
        .on('show.bs.modal.ctrScroll', function() {
            sessionStorage.setItem('ctr_scroll_pos', window.scrollY !== undefined ? window.scrollY : $(window).scrollTop());
        })
        .on('hidden.bs.modal.ctrScroll', function() {
            sessionStorage.removeItem('ctr_scroll_pos');
        });

    // Filtro por card — .off().on() garante apenas UM handler mesmo com múltiplos reloads do script
    $(document).off('click.ctrCard').on('click.ctrCard', '#ctr-cards-container .ctr-card-total', function () {
        var filtro = $(this).data('filtro');

        if (ctrFiltroAtivo === filtro || filtro === 'total_periodo') {
            // Deseleciona ou clica no Total → mostra tudo, destaca Total
            ctrFiltroAtivo = null;
            $('#ctr-cards-container .ctr-card-total').removeClass('ativo');
            $('#ctr-cards-container [data-filtro="total_periodo"]').addClass('ativo');
        } else {
            ctrFiltroAtivo = filtro;
            $('#ctr-cards-container .ctr-card-total').removeClass('ativo');
            $(this).addClass('ativo');
        }

        $('#tabela_contas_receber').DataTable().draw();
    });

    $("#tabela_contas_receber").DataTable({
        "paging":   false,
        "ordering": true,
        "order":    [],
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
            { "orderable": false },
            null,
            null,
            null,
            { "sType": "date-br" },
            { "sType": "date-br" },
            null,
            { "sType": "date-br" },
            null,
            null
        ],

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>><'#ctr-cards-container'>t",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
            var cardsHtml = $('#ctr-cards-source').html();
            if (cardsHtml) {
                $('#ctr-cards-container').html(cardsHtml);
            }
            // Destaca o card Total do Período como padrão (mostra tudo)
            $('#ctr-cards-container [data-filtro="total_periodo"]').addClass('ativo');
            // Remove borda do topo do thead (entre cards e cabeçalho das colunas)
            $('#tabela_contas_receber thead tr:first-child th').css('border-top', '0');
            $('#tabela_contas_receber').css('margin-top', '0');
        },
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
    var aChk = document.getElementsByName("conta_option");
    for (var i = 0; i < aChk.length; i++) {
        aChk[i].checked = false;
    }
    $("#contas_selecionadas").val('Todas ou (Clique p/ selecionar contas)');
    $("#exibe_conta").val('');
    consultar_ctr();
    atualizarLinkLimparFiltros();
}

function ctrRestaurarPosicao() {
    var ctrId     = sessionStorage.getItem('ctr_retorno_id');
    var scrollPos = sessionStorage.getItem('ctr_scroll_pos');

    if (ctrId) {
        sessionStorage.removeItem('ctr_retorno_id');
        // 500ms: aguarda modal "Aguarde" terminar animação (300ms) antes de rolar
        setTimeout(function() {
            var $row = $('tr[data-ctr-id="' + ctrId + '"]');
            if ($row.length) {
                $row[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                $row.addClass('ctr-destaque');
                setTimeout(function() { $row.removeClass('ctr-destaque'); }, 2500);
            }
        }, 500);
    } else if (scrollPos) {
        sessionStorage.removeItem('ctr_scroll_pos');
        setTimeout(function() {
            window.scrollTo({ top: parseInt(scrollPos), behavior: 'smooth' });
        }, 500);
    }
}

function consultar_ctr() {
    // Reset do filtro de card ao fazer nova consulta
    ctrFiltroAtivo = null;
    $('#ctr-cards-container .ctr-card-total').removeClass('ativo');

    $('#btn_consultar_filtro').hide();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var tipo_data = $("#tipo_data").val();
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

        for (i = 0; i < razao_nome.length; i++) {
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

        for (i = 0; i < codigo_fazenda.length; i++) {
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

        for (i = 0; i < codigo_cc.length; i++) {
            valor[i]=codigo_cc[i];
        }

        var array_cc=valor.join(",");
    }

    var options = $("#razao_nome option:selected");
    var fornecedor_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#razao_nome").text();
        fornecedor_filtro.push(desc.trim());
    });

    if (fornecedor_filtro.length > 0 && fornecedor_filtro[0] != "") {
        fornecedor_filtro = "Cliente: " + fornecedor_filtro + "->";
    } else {
        fornecedor_filtro = "Cliente: Todos->";
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

    var periodo_label = $("#periodo_label").val();
    var datas_str = dia_ini + "/" + mes_ini + "/" + ano_ini + " ate " + dia_fim + "/" + mes_fim + "/" + ano_fim + "->";
    periodo = (periodo_label ? periodo_label + "-> " : "") + datas_str;

    if (tipo_data == "V") {
        opc_data_filtro = "Dt Vencimento->";
    } else if (tipo_data == "E") {
        opc_data_filtro = "Dt Emissão->";
    } else {
        opc_data_filtro = "Dt Pagamento->";
    }

    var descricao_filtro =
        periodo +
        opc_data_filtro +
        codigo_local_filtro +
        fornecedor_filtro +
        codigo_cc_filtro +
        conta_filtro;

    $(".digitar_filtros").hide();
    $(".filtros").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".voltar").show();
    $(".descricao_filtro").html(descricao_filtro);

    $("#exibe_conta").val(array_conta);

    $("#aguardar").modal("show");

    $('#lista_contas_receber').load('form_lista_contas_receber.php?data_inicial=' + data_inicial +
     '&data_final=' + data_final +
     '&tipo_data=' + tipo_data +
     '&array_cliente=' + array_cliente +
     '&array_conta=' + array_conta +
     '&array_fazenda=' + array_fazenda +
     '&array_cc=' + array_cc +
     '&periodo_label=' + encodeURIComponent(periodo_label), function() {
        $('#lista_contas_receber').show();
        ctrRestaurarPosicao();
        $('[data-toggle="tooltip"]').tooltip();
    });
}

function exibe_mais_filtros() {
    $(".digitar_filtros").show();
    $(".mais_filtros").hide();
    $(".menos_filtros").show();
    $(".consultar").hide();
    $(".lista_contas").hide();
    $(".voltar").hide();
}

function exibe_menos_filtros() {
    $(".digitar_filtros").hide();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".lista_contas").show();
    $(".voltar").show();
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

/* ================================================================
   CONTAS A RECEBER — INCLUIR (novo layout, espelhado do Contas a Pagar)
   Parcelamento dinâmico, bloco "Pago", anexos e editor inline de
   "Distribuir Rateio". Todas as funções abaixo usam o sufixo/prefixo
   "Ctr"/"CTR_" (equivalente ao "Ctp"/"CTP_" usado no Contas a Pagar)
   para não colidir com o Contas a Pagar quando ambas as telas estão
   abertas em abas diferentes do mesmo navegador.
   Fase 1: somente front-end — o botão Confirmar valida os campos e
   exibe um alerta; a gravação será implementada em uma fase futura.
================================================================ */

// ----------------------------------------------------------------
// Helpers de formatação / parse de moeda BR
// ----------------------------------------------------------------
function ctrFormatMoney(n) {
    if (isNaN(n) || n === '' || n === null) n = 0;
    return parseFloat(n).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function ctrParseMoney(str) {
    if (!str) return 0;
    str = String(str).trim();
    if (str.indexOf(',') !== -1) {
        // Formato BR ("4.000,00"): remove pontos de milhar e troca vírgula por ponto
        str = str.replace(/\./g, '').replace(',', '.');
    }
    // Sem vírgula: já é um número puro (ex.: "4000.00" vindo da máscara antes do blur reformatar)
    var v = parseFloat(str);
    return isNaN(v) ? 0 : v;
}

function ctrGetValorTotal() {
    return ctrParseMoney($('#vlr_primeira_parcela').val());
}

// ----------------------------------------------------------------
// Soma ordinal: "1º", "2º" … com acento correto
// ----------------------------------------------------------------
function ordinal(n) {
    return n + 'º';
}

// ----------------------------------------------------------------
// Soma de dias a uma data (YYYY-MM-DD) sem fuso horário
// ----------------------------------------------------------------
function addDias(dataStr, dias) {
    var p = dataStr.split('-');
    var d = new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
    d.setDate(d.getDate() + parseInt(dias));
    var mm = String(d.getMonth() + 1).padStart(2, '0');
    var dd = String(d.getDate()).padStart(2, '0');
    return d.getFullYear() + '-' + mm + '-' + dd;
}

// ----------------------------------------------------------------
// Calcula 1º vencimento padrão: emissão + 30 dias
// ----------------------------------------------------------------
function calcPrimeiroVencimentoCtr() {
    var emissao = $('#data_emissao').val();
    if (!emissao) return '';
    return addDias(emissao, 30);
}

// Chamado ao alterar data de emissão
function onEmissaoChangeCtr() {
    var modo = $('#sel_modo_parc').val();
    if (modo === 'avista') {
        $('#data_vencimento').val($('#data_emissao').val());
    } else if (modo === 'uma_parcela') {
        var emissao = $('#data_emissao').val();
        if (emissao) $('#data_vencimento').val(addDias(emissao, 30));
    } else {
        $('#primeiro_vencimento').val(calcPrimeiroVencimentoCtr());
        recalcularDatasCtr();
    }
}

// Chamado ao sair do campo Valor total
function onValorTotalBlurCtr() {
    exibe_valor_primeira_parcela_ctr(); // formata exibição
    var n = parseInt($('#parcelamento').val());
    if (n > 0) {
        zerarValoresParcelasCtr(n);
    }

    // Rateio: zera os valores digitados mantendo Local/CC/Conta já configurados
    if ($('#habilitar_rateio').is(':checked')) {
        if ($('#secao_distribuir_rateio').is(':visible')) {
            zerarValoresRateioCtr();
        } else if ($('#rateio_status').is(':visible')) {
            editarRateioCtr();
            zerarValoresRateioCtr();
        }
    }
}

// Formata o campo de Valor total (equivalente a exibe_valor_primeira_parcela do Contas a Pagar)
function exibe_valor_primeira_parcela_ctr() {
    var vlr = $('#vlr_primeira_parcela').val();
    if (verifica_virgula(vlr) === ',') {
        vlr = replace_valor(vlr);
    }
    $('#vlr_primeira_parcela').val(formatMoney(vlr));
}

// Zera os valores/percentuais do rateio (mantém as linhas Local/CC/Conta)
function zerarValoresRateioCtr() {
    $('.rat-valor').val('');
    $('.rat-perc').val('');
    recalcularRateioCtr();
}

// Zera os valores/percentuais das parcelas (mantém datas, banco, tipo doc e pago)
function zerarValoresParcelasCtr(n) {
    for (var i = 0; i < n; i++) {
        $('#parc_valor_' + i).val('');
        $('#parc_perc_' + i).val('');
    }
    atualizarTotaisCtr(n);
}

// ----------------------------------------------------------------
// Replica valor para parcelas seguintes (com confirmação)
// ----------------------------------------------------------------
function replicarSeDesejadoCtr(tipo, el, idx) {
    var n = parseInt($('#parcelamento').val());
    if (idx >= n - 1) return; // já é a última parcela, nada a replicar

    var resposta = confirm('Deseja replicar esta seleção para as ' + (n - idx - 1) + ' parcela(s) seguinte(s)?');
    if (!resposta) return;

    for (var i = idx + 1; i < n; i++) {
        if (tipo === 'banco') {
            $('#parc_banco_' + i).val($(el).val());
        } else if (tipo === 'formapag') {
            $('#parc_formapag_' + i).val($(el).val());
        } else if (tipo === 'tipodoc') {
            $('#parc_tipodoc_' + i).val($(el).val());
        } else if (tipo === 'pago') {
            $('#parc_pago_' + i).prop('checked', $(el).is(':checked'));
        }
    }
}

// ----------------------------------------------------------------
// Monta o HTML de um <select> de bancos
// ----------------------------------------------------------------
function buildSelectBancoCtr(name, id, val, idx) {
    var html = '<select class="form-control" name="' + name + '" id="' + id + '" style="height:30px;font-size:13px;padding:2px 6px;" onchange="replicarSeDesejadoCtr(\'banco\', this, ' + idx + ')">';
    html += '<option value="0">...</option>';
    CTR_BANCOS.forEach(function(b) {
        var sel = (val && String(val) === String(b.id)) ? ' selected' : '';
        html += '<option value="' + b.id + '"' + sel + '>' + b.desc + '</option>';
    });
    html += '</select>';
    return html;
}

// ----------------------------------------------------------------
// Monta o HTML de um <select> de tipo documento
// ----------------------------------------------------------------
function buildSelectTipoDocCtr(name, id, val, idx) {
    var html = '<select class="form-control" name="' + name + '" id="' + id + '" style="height:30px;font-size:13px;padding:2px 6px;" onchange="replicarSeDesejadoCtr(\'tipodoc\', this, ' + idx + ')">';
    html += '<option value="00">...</option>';
    CTR_TIPODOCS.forEach(function(t) {
        var sel = (val && String(val) === String(t.id)) ? ' selected' : '';
        html += '<option value="' + t.id + '"' + sel + '>' + t.desc + '</option>';
    });
    html += '</select>';
    return html;
}

// ----------------------------------------------------------------
// Monta o HTML de um <select> de forma de pagamento
// ----------------------------------------------------------------
function buildSelectFormaPagCtr(name, id, val, idx) {
    var html = '<select class="form-control" name="' + name + '" id="' + id + '" style="height:30px;font-size:13px;padding:2px 6px;" onchange="replicarSeDesejadoCtr(\'formapag\', this, ' + idx + ')">';
    html += '<option value="00">...</option>';
    CTR_FORMASPAG.forEach(function(f) {
        var sel = (val && String(val) === String(f.id)) ? ' selected' : '';
        html += '<option value="' + f.id + '"' + sel + '>' + f.desc + '</option>';
    });
    html += '</select>';
    return html;
}

// ----------------------------------------------------------------
// Gera / Regera a tabela de parcelas
// ----------------------------------------------------------------
function gerarTabelaParcelasCtr(n) {
    var total      = ctrGetValorTotal();
    var vlrParc    = (n > 0 && total > 0) ? total / n : 0;
    var percParc   = (n > 0) ? 100 / n : 0;
    var primVenc   = $('#primeiro_vencimento').val();
    var intervalo  = parseInt($('#intervalo').val()) || 30;

    var tbody = $('#tbody_parcelas');
    tbody.empty();

    for (var i = 0; i < n; i++) {
        // Calcula data desta parcela
        var dataParc = '';
        if (primVenc) {
            dataParc = (i === 0) ? primVenc : addDias(primVenc, intervalo * i);
        }

        // Arredonda — última parcela absorve centavos
        var vlrEsta  = (i < n - 1) ? Math.round(vlrParc * 100) / 100 : Math.round((total - vlrParc * (n - 1)) * 100) / 100;
        var percEsta = (i < n - 1) ? Math.round(percParc * 100) / 100 : Math.round((100 - percParc * (n - 1)) * 100) / 100;

        var tr = '<tr id="parc_row_' + i + '">';
        tr += '<td><span class="lbl-parcela">' + ordinal(i + 1) + ' Vencimento</span></td>';
        tr += '<td><input type="date" class="form-control parc-data" name="parcela[' + i + '][data_vencimento]" id="parc_data_' + i + '" value="' + dataParc + '" style="height:30px;font-size:13px;padding:2px 6px;"></td>';
        tr += '<td><input type="text"  class="form-control parc-valor" name="parcela[' + i + '][valor]" id="parc_valor_' + i + '" value="' + ctrFormatMoney(vlrEsta) + '" onblur="recalcularPorValorCtr(' + i + ')" onkeypress="mask.money.call(this, event)"></td>';
        tr += '<td><input type="text"  class="form-control parc-perc"  name="parcela[' + i + '][percentual]" id="parc_perc_' + i + '"  value="' + ctrFormatMoney(percEsta) + '" readonly style="background:#f5f5f5;color:#777;"></td>';
        tr += '<td>' + buildSelectBancoCtr('parcela[' + i + '][banco_conta]', 'parc_banco_' + i, '', i) + '</td>';
        tr += '<td>' + buildSelectFormaPagCtr('parcela[' + i + '][forma_pagamento]', 'parc_formapag_' + i, '', i) + '</td>';
        tr += '<td>' + buildSelectTipoDocCtr('parcela[' + i + '][tipo_doc]', 'parc_tipodoc_' + i, '', i) + '</td>';
        tr += '<td class="pago-parc" style="text-align:center;"><input type="checkbox" name="parcela[' + i + '][pago]" id="parc_pago_' + i + '" value="S" onchange="togglePagoParcCtr(' + i + ')"></td>';
        tr += '</tr>';
        tr += '<tr id="parc_pago_row_' + i + '" style="display:none; background:#fffde7;">';
        tr += '<td style="padding:0; background:#fffde7;"></td>';
        tr += '<td style="padding:0; background:#fffde7;"></td>';
        tr += '<td colspan="2" style="padding:4px 8px;"><small style="color:#888;">Data Recebimento</small><br>';
        tr += '<input type="date" class="form-control" name="parcela[' + i + '][data_pagamento]" id="parc_dt_pag_' + i + '" style="height:28px;font-size:12px;padding:2px 6px;"></td>';
        tr += '<td style="padding:4px 8px;"><div style="display:flex;gap:6px;">';
        tr += '<div style="flex:1;"><small style="color:#888;">Desconto</small><br>';
        tr += '<input type="text" class="form-control" name="parcela[' + i + '][desconto]" id="parc_desconto_' + i + '" placeholder="0,00" style="height:28px;font-size:12px;padding:2px 6px;" onkeypress="mask.money.call(this, event)" onblur="recalcularValorPagoCtr(' + i + ')"></div>';
        tr += '<div style="flex:1;"><small style="color:#888;">Juros</small><br>';
        tr += '<input type="text" class="form-control" name="parcela[' + i + '][juros]" id="parc_juros_' + i + '" placeholder="0,00" style="height:28px;font-size:12px;padding:2px 6px;" onkeypress="mask.money.call(this, event)" onblur="recalcularValorPagoCtr(' + i + ')"></div>';
        tr += '</div></td>';
        tr += '<td style="padding:0; background:#fffde7;"></td>';
        tr += '<td style="padding:4px 8px;"><small style="color:#888;">Valor Recebido</small><br>';
        tr += '<input type="text" class="form-control" name="parcela[' + i + '][valor_pago]" id="parc_vlr_pago_' + i + '" placeholder="0,00" style="height:28px;font-size:12px;padding:2px 6px;background:#f0f8e8;font-weight:600;" readonly></td>';
        tr += '<td style="padding:0; background:#fffde7;"></td>';
        tr += '</tr>';

        tbody.append(tr);
    }

    atualizarTotaisCtr(n);
}

// ----------------------------------------------------------------
// Recalcular todas as datas (ao alterar 1º vencimento ou intervalo)
// ----------------------------------------------------------------
function recalcularDatasCtr() {
    var n         = parseInt($('#parcelamento').val());
    var primVenc  = $('#primeiro_vencimento').val();
    var intervalo = parseInt($('#intervalo').val()) || 30;

    if (!primVenc || n < 1) return;

    for (var i = 0; i < n; i++) {
        var dataParc = (i === 0) ? primVenc : addDias(primVenc, intervalo * i);
        $('#parc_data_' + i).val(dataParc);
    }
}

// ----------------------------------------------------------------
// Ao alterar o intervalo: atualiza 1º Vencimento = emissão + intervalo
// ----------------------------------------------------------------
function onIntervaloChangeCtr() {
    var emissao   = $('#data_emissao').val();
    var intervalo = parseInt($('#intervalo').val()) || 30;
    if (emissao) {
        $('#primeiro_vencimento').val(addDias(emissao, intervalo));
    }
    recalcularDatasCtr();
}

// ----------------------------------------------------------------
// Recalcular ao alterar VALOR de uma parcela
// ----------------------------------------------------------------
function recalcularPorValorCtr(idx) {
    var n     = parseInt($('#parcelamento').val());
    var total = ctrGetValorTotal();
    if (n < 1 || total === 0) return;

    var novoVlr = parseMoneyValCtr($('#parc_valor_' + idx).val());
    // Atualiza percentual desta parcela
    var novoPerc = total > 0 ? (novoVlr / total) * 100 : 0;
    $('#parc_perc_' + idx).val(ctrFormatMoney(novoPerc));
    $('#parc_valor_' + idx).val(ctrFormatMoney(novoVlr));

    // Distribui o restante igualmente entre as demais
    var somaFixa = novoVlr;
    var restantes = n - 1;
    if (restantes > 0) {
        var vlrRestante = (total - novoVlr) / restantes;
        for (var i = 0; i < n; i++) {
            if (i === idx) continue;
            var vlrI = (i === n - 1 && i !== idx)
                ? Math.round((total - somaFixa) * 100) / 100
                : Math.round(vlrRestante * 100) / 100;
            somaFixa += vlrI;
            var percI = total > 0 ? (vlrI / total) * 100 : 0;
            $('#parc_valor_' + i).val(ctrFormatMoney(vlrI));
            $('#parc_perc_' + i).val(ctrFormatMoney(percI));
        }
    }

    atualizarTotaisCtr(n);
}

// ----------------------------------------------------------------
// Recalcular ao alterar PERCENTUAL de uma parcela
// ----------------------------------------------------------------
function recalcularPorPercentualCtr(idx) {
    var n     = parseInt($('#parcelamento').val());
    var total = ctrGetValorTotal();
    if (n < 1 || total === 0) return;

    var novoPerc = ctrParseMoney($('#parc_perc_' + idx).val());
    var novoVlr  = (novoPerc / 100) * total;
    $('#parc_valor_' + idx).val(ctrFormatMoney(novoVlr));
    $('#parc_perc_'  + idx).val(ctrFormatMoney(novoPerc));

    // Distribui percentual restante igualmente entre as demais
    var percRestante = (100 - novoPerc) / (n - 1);
    var somaPerc = novoPerc;
    for (var i = 0; i < n; i++) {
        if (i === idx) continue;
        var percI = (i === n - 1 && i !== idx)
            ? Math.round((100 - somaPerc) * 100) / 100
            : Math.round(percRestante * 100) / 100;
        somaPerc += percI;
        var vlrI = (percI / 100) * total;
        $('#parc_valor_' + i).val(ctrFormatMoney(vlrI));
        $('#parc_perc_'  + i).val(ctrFormatMoney(percI));
    }

    atualizarTotaisCtr(n);
}

// ----------------------------------------------------------------
// Atualiza linha de totais abaixo da tabela
// ----------------------------------------------------------------
function atualizarTotaisCtr(n) {
    var total    = ctrGetValorTotal();
    var somaVlr  = 0;
    var somaPerc = 0;

    for (var i = 0; i < n; i++) {
        somaVlr  += ctrParseMoney($('#parc_valor_' + i).val());
        somaPerc += ctrParseMoney($('#parc_perc_'  + i).val());
    }

    somaVlr  = Math.round(somaVlr  * 100) / 100;
    somaPerc = Math.round(somaPerc * 100) / 100;

    var okVlr  = Math.abs(somaVlr  - total) <= 0.02;
    var okPerc = Math.abs(somaPerc - 100)   <= 0.02;

    var clVlr  = okVlr  ? 'valor-ok' : 'valor-err';
    var clPerc = okPerc ? 'valor-ok' : 'valor-err';

    $('#parc_totais').html(
        'Total das parcelas: <span class="' + clVlr  + '">R$ ' + ctrFormatMoney(somaVlr)  + '</span> &nbsp;|&nbsp; ' +
        'Total %: <span class="' + clPerc + '">'       + ctrFormatMoney(somaPerc) + '%</span>' +
        (okVlr && okPerc ? '' : ' &nbsp;<span style="color:#c0392b;">⚠ Ajuste os valores antes de confirmar</span>')
    );
}

// ----------------------------------------------------------------
// Alterna entre À Vista, 1 Parcela e Parcelado em 2x ou mais
// ----------------------------------------------------------------
function onParcelamentoChangeCtr() {
    var modo = $('#sel_modo_parc').val();

    // Sempre reseta o bloco de recebimento ao trocar de modo
    $('#pago').prop('checked', false);
    $('#pago_data_pagamento').val('');
    $('#pago_desconto').val('');
    $('#pago_juros').val('');
    $('#pago_valor_pago').val('');
    $('#bloco_pago_avista').hide();

    if (modo === 'avista') {
        $('#parcelamento').val(0);
        $('#bloco_qtd_parcelas').hide();
        $('#qtd_parcelas_input').val('');
        $('#bloco_avista').show();
        $('#bloco_parc_header').hide();
        $('#bloco_parcelas').hide();
        $('#tbody_parcelas').empty();
        $('#parc_totais').empty();
        var emissao = $('#data_emissao').val();
        if (emissao) $('#data_vencimento').val(emissao);
    } else if (modo === 'uma_parcela') {
        $('#parcelamento').val(0);
        $('#bloco_qtd_parcelas').hide();
        $('#qtd_parcelas_input').val('');
        $('#bloco_avista').show();
        $('#bloco_parc_header').hide();
        $('#bloco_parcelas').hide();
        $('#tbody_parcelas').empty();
        $('#parc_totais').empty();
        var emissao = $('#data_emissao').val();
        if (emissao) $('#data_vencimento').val(addDias(emissao, 30));
    } else {
        // Parcelado em 2x ou mais
        $('#parcelamento').val(0);
        $('#bloco_qtd_parcelas').show();
        $('#qtd_parcelas_input').attr('min', 2).val('');
        $('#bloco_avista').hide();
        $('#bloco_parc_header').hide();
        $('#bloco_parcelas').hide();
        $('#tbody_parcelas').empty();
        $('#parc_totais').empty();
        setTimeout(function(){ $('#qtd_parcelas_input').focus(); }, 50);
    }
}

function onQtdParcelasChangeCtr(val) {
    var n = parseInt(val);
    if (!n || n < 1) {
        $('#parcelamento').val(0);
        $('#bloco_parc_header').hide();
        $('#bloco_parcelas').hide();
        $('#tbody_parcelas').empty();
        return;
    }
    if (n < 2) {
        $('#mensagem_erro').modal();
        $('#mensagem_erro .modal-body').html('Para "Parcelado em 2x ou mais", informe no mínimo 2 parcelas.');
        $('#qtd_parcelas_input').val('');
        $('#parcelamento').val(0);
        $('#bloco_parc_header').hide();
        $('#bloco_parcelas').hide();
        $('#tbody_parcelas').empty();
        return;
    }
    // Atualiza o hidden que as funções JS leem
    $('#parcelamento').val(n);

    $('#bloco_parc_header').show();
    $('#bloco_parcelas').show();
    if (!$('#primeiro_vencimento').val()) {
        $('#primeiro_vencimento').val(calcPrimeiroVencimentoCtr());
    }
    gerarTabelaParcelasCtr(n);
}

// ----------------------------------------------------------------
// Validação na hora de confirmar
// ----------------------------------------------------------------
function validarParcelamentoCtr() {
    var n = parseInt($('#parcelamento').val());
    if (n === 0) return true; // À Vista — sem validação extra

    var total    = ctrGetValorTotal();
    var somaVlr  = 0;
    var somaPerc = 0;

    for (var i = 0; i < n; i++) {
        var banco = $('#parc_banco_' + i).val();
        if (!banco || banco === '0') {
            $('#mensagem_erro').modal();
            $('#mensagem_erro .modal-body').html('Informe o Banco/Conta Pagamento da parcela ' + (i + 1) + '.');
            return false;
        }
        var formaPag = $('#parc_formapag_' + i).val();
        if (!formaPag || formaPag === '00') {
            $('#mensagem_erro').modal();
            $('#mensagem_erro .modal-body').html('Informe a Forma Pagamento da parcela ' + (i + 1) + '.');
            return false;
        }
        somaVlr  += ctrParseMoney($('#parc_valor_' + i).val());
        somaPerc += ctrParseMoney($('#parc_perc_'  + i).val());
    }

    somaVlr  = Math.round(somaVlr  * 100) / 100;
    somaPerc = Math.round(somaPerc * 100) / 100;

    if (Math.abs(somaVlr - total) > 0.02) {
        $('#mensagem_erro').modal();
        $('#mensagem_erro .modal-body').html(
            'A soma das parcelas (R$ ' + ctrFormatMoney(somaVlr) + ') é diferente do valor total (R$ ' + ctrFormatMoney(total) + ').'
        );
        return false;
    }

    if (Math.abs(somaPerc - 100) > 0.02) {
        $('#mensagem_erro').modal();
        $('#mensagem_erro .modal-body').html(
            'A soma dos percentuais (' + ctrFormatMoney(somaPerc) + '%) deve ser igual a 100%.'
        );
        return false;
    }

    return true;
}

// ----------------------------------------------------------------
// Validação dos campos obrigatórios, na ordem exigida pelo negócio:
// Cliente, Emissão, Descrição, Valor, (sem rateio) Local, Centro
// de Custos, Código Contábil, Vencimento, Banco/Conta Pagamento.
// Número do Documento NÃO é obrigatório.
// ----------------------------------------------------------------
function validarCamposObrigatoriosCtr() {
    function erro(msg, $campo) {
        $('#mensagem_erro').modal();
        $('#mensagem_erro .modal-body').html(msg);
        if ($campo && $campo.length) {
            $('#mensagem_erro').one('hidden.bs.modal', function () {
                $campo.focus();
            });
        }
        return false;
    }

    var codigoCli = $('#codigo_cli_for').val();
    var nomeCliManual = ($('#nome_cli').val() || '').trim();
    if ((!codigoCli || codigoCli === '999999999') && !nomeCliManual) {
        return erro('Informe o Cliente ou digite o nome do cliente não cadastrado.', $('#codigo_cli_for'));
    }
    if (!$('#data_emissao').val()) {
        return erro('Informe a Data de Emissão.', $('#data_emissao'));
    }
    if (!$('#descricao_compra').val().trim()) {
        return erro('Informe a Descrição.', $('#descricao_compra'));
    }
    if (ctrGetValorTotal() <= 0) {
        return erro('Informe o Valor.', $('#vlr_primeira_parcela'));
    }

    if (!$('#habilitar_rateio').is(':checked')) {
        var local = $('#codigo_local').val();
        if (!local || (Array.isArray(local) && local.length === 0)) {
            return erro('Informe o Local.', $('#codigo_local'));
        }
        if (!$('#codigo_cc').val()) {
            return erro('Informe o Centro de Custos.', $('#codigo_cc'));
        }
        var conta = $('#codigo_conta').val();
        if (!conta || conta === '0000000') {
            return erro('Informe o Código Contábil.', $('#codigo_conta'));
        }
    }

    var n = parseInt($('#parcelamento').val());
    if (n === 0) {
        if (!$('#data_vencimento').val()) {
            return erro('Informe o Vencimento.', $('#data_vencimento'));
        }
        var banco = $('#codigo_conta_rec').val();
        if (!banco || banco === '0') {
            return erro('Informe o Banco/Conta Pagamento.', $('#codigo_conta_rec'));
        }
        var formaPag = $('#codigo_forma_rec').val();
        if (!formaPag || formaPag === '00') {
            return erro('Informe a Forma Pagamento.', $('#codigo_forma_rec'));
        }
    } else if (n >= 1) {
        if (!$('#primeiro_vencimento').val()) {
            return erro('Informe o Vencimento.', $('#primeiro_vencimento'));
        }
        // Banco/Conta Pagamento de cada parcela é validado em validarParcelamentoCtr()
    }

    // Descrição do link digitada sem o link confirmado (não vira anexo_link_url[])
    if (($('#link_desc_input').val() || '').trim() && !($('#link_url_input').val() || '').trim()) {
        return erro('Informe o Link (https://) ou apague a Descrição do Link.', $('#link_url_input'));
    }

    return true;
}

// Ponto de entrada do botão Confirmar — valida campos obrigatórios e parcelamento.
// Fase 1: apenas front-end — não grava, apenas exibe um alerta de confirmação.
function confirmar_incluir_ctr() {
    if (!validarCamposObrigatoriosCtr()) return;
    if (!validarParcelamentoCtr()) return;
    gravar_conta_ctr();
}

// ----------------------------------------------------------------
// Gravação — envia o formulário (com anexos) para gravar_contas_receber.php
// ----------------------------------------------------------------
function gravar_conta_ctr() {
    var form = document.getElementById('form_gravar_contas_receber');
    var formData = new FormData(form);

    $('.confirmar_gravar_ctr').attr('disabled', true);

    $.ajax({
        type: 'POST',
        url: 'gravar_contas_receber.php',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (data) {
            $('.confirmar_gravar_ctr').attr('disabled', false);
            if (data && data.error) {
                $('#mensagem_erro').modal();
                $('#mensagem_erro .modal-body').html(data.message);
            } else {
                $('#mensagem_retorno').modal();
                $('#mensagem_retorno .modal-body').html((data && data.message) || 'Conta incluída com sucesso.');
            }
        },
        error: function () {
            $('.confirmar_gravar_ctr').attr('disabled', false);
            $('#mensagem_erro').modal();
            $('#mensagem_erro .modal-body').html('Erro de comunicação com o servidor.');
        }
    });
}

// ----------------------------------------------------------------
// Anexos
// ----------------------------------------------------------------
function onAnexoPickerChangeCtr(input) {
    if (!input.files || !input.files.length) return;
    criarLinhaAnexoArquivoCtr(input.files[0]);
    input.value = ''; // limpa para permitir escolher o próximo arquivo
}

function criarLinhaAnexoArquivoCtr(file) {
    var dt = new DataTransfer();
    dt.items.add(file);

    var hidden = document.createElement('input');
    hidden.type = 'file';
    hidden.name = 'anexo[]';
    hidden.style.display = 'none';
    hidden.files = dt.files;

    var nome = document.createElement('span');
    nome.textContent = file.name;
    nome.style.cssText = 'max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;';

    var btnRemover = criarBotaoRemoverCtr(function () { removerAnexoCtr(btnRemover); });

    var div = document.createElement('div');
    div.className = 'linha-anexo-arquivo';
    div.style.cssText = 'display:flex;align-items:center;gap:8px;margin-top:6px;';
    div.appendChild(nome);
    div.appendChild(btnRemover);
    div.appendChild(hidden);
    document.getElementById('lista_anexos').appendChild(div);
}

function criarBotaoRemoverCtr(onRemove) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn-anexo-add';
    btn.title = 'Remover';
    btn.setAttribute('data-toggle', 'tooltip');
    btn.setAttribute('data-placement', 'top');
    btn.innerHTML = '<i class="fas fa-trash" style="font-size:12px; color:#337ab7;"></i>';
    btn.onclick = onRemove;
    $(btn).tooltip();
    return btn;
}

// Enter no campo URL sai do foco (dispara onLinkUrlBlurCtr) em vez de tentar submeter o formulário.
function onLinkUrlKeydownCtr(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        event.target.blur();
    }
}

// Ao sair do foco da URL, se preenchida, cria a linha de exibição do
// link (igual ao chip do anexo de arquivo) e limpa os inputs fixos
// de Descrição/URL para permitir digitar o próximo link direto.
function onLinkUrlBlurCtr() {
    var inputDesc = document.getElementById('link_desc_input');
    var inputUrl  = document.getElementById('link_url_input');
    var url = inputUrl.value.trim();
    if (!url) return; // nada digitado

    var desc = inputDesc.value.trim() || url;
    criarLinhaLinkCtr(desc, url);

    inputDesc.value = '';
    inputUrl.value = '';
}

function criarLinhaLinkCtr(desc, url) {
    var hiddenDesc = document.createElement('input');
    hiddenDesc.type = 'hidden';
    hiddenDesc.name = 'anexo_link_desc[]';
    hiddenDesc.value = desc;

    var hiddenUrl = document.createElement('input');
    hiddenUrl.type = 'hidden';
    hiddenUrl.name = 'anexo_link_url[]';
    hiddenUrl.value = url;

    var texto = document.createElement('span');
    texto.textContent = desc + ' — ' + url;
    texto.style.cssText = 'max-width:400px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;';

    var div = document.createElement('div');
    div.className = 'linha-anexo-link';
    div.style.cssText = 'display:flex;align-items:center;gap:8px;margin-top:6px;';

    var btnRemover = criarBotaoRemoverCtr(function () { removerAnexoCtr(btnRemover); });

    div.appendChild(texto);
    div.appendChild(btnRemover);
    div.appendChild(hiddenDesc);
    div.appendChild(hiddenUrl);
    document.getElementById('lista_links').appendChild(div);
}

function removerAnexoCtr(btn) {
    btn.parentElement.remove();
}

// ----------------------------------------------------------------
// Pago checkbox — bloco À Vista / 1 Parcela
// ----------------------------------------------------------------
function onPagoAvistaChangeCtr() {
    var checked = $('#pago').is(':checked');
    if (checked) {
        $('#pago_data_pagamento').val($('#data_vencimento').val());
        $('#pago_desconto').val('');
        $('#pago_juros').val('');
        calcularValorPagoAvistaCtr();
        $('#bloco_pago_avista').show();
    } else {
        $('#pago_data_pagamento').val('');
        $('#pago_desconto').val('');
        $('#pago_juros').val('');
        $('#pago_valor_pago').val('');
        $('#bloco_pago_avista').hide();
    }
}

// Parseia valor digitado — suporta tanto "30.00" (US, durante digitação)
// quanto "3.000,00" (BR, após formatação).
function parseMoneyValCtr(v) {
    if (!v) return 0;
    if (verifica_virgula(v) === ',') v = replace_valor(v);
    return parseFloat(v) || 0;
}

function calcularValorPagoAvistaCtr() {
    var vlrTotal = ctrGetValorTotal();
    var dv = $('#pago_desconto').val();
    var jv = $('#pago_juros').val();
    var d = parseMoneyValCtr(dv);
    var j = parseMoneyValCtr(jv);
    if (dv) $('#pago_desconto').val(ctrFormatMoney(d));
    if (jv) $('#pago_juros').val(ctrFormatMoney(j));
    var vlrPago = vlrTotal - d + j;
    if (vlrPago < 0) vlrPago = 0;
    $('#pago_valor_pago').val(ctrFormatMoney(vlrPago));
}

// ----------------------------------------------------------------
// Pago checkbox — tabela de parcelas
// ----------------------------------------------------------------
function togglePagoParcCtr(idx) {
    var checked = $('#parc_pago_' + idx).is(':checked');
    var $sub = $('#parc_pago_row_' + idx);
    if (checked) {
        $('#parc_dt_pag_' + idx).val($('#parc_data_' + idx).val());
        $('#parc_desconto_' + idx).val('');
        $('#parc_juros_' + idx).val('');
        recalcularValorPagoCtr(idx);
        $sub.show();
    } else {
        $('#parc_dt_pag_' + idx).val('');
        $('#parc_desconto_' + idx).val('');
        $('#parc_juros_' + idx).val('');
        $('#parc_vlr_pago_' + idx).val('');
        $sub.hide();
    }
}

function recalcularValorPagoCtr(idx) {
    var vlrParc = parseMoneyValCtr($('#parc_valor_' + idx).val());
    var dv = $('#parc_desconto_' + idx).val();
    var jv = $('#parc_juros_' + idx).val();
    var d = parseMoneyValCtr(dv);
    var j = parseMoneyValCtr(jv);
    if (dv) $('#parc_desconto_' + idx).val(ctrFormatMoney(d));
    if (jv) $('#parc_juros_' + idx).val(ctrFormatMoney(j));
    var vlrPago = vlrParc - d + j;
    if (vlrPago < 0) vlrPago = 0;
    $('#parc_vlr_pago_' + idx).val(ctrFormatMoney(vlrPago));
}

/* ================================================================
   EDITOR INLINE DE RATEIO (Local → Centro de Custos → Conta Contábil)
   Adaptado do Contas a Pagar. Usa CTR_CCS / CTR_CONTAS_RAT (dados
   PHP exportados em form_contas_receber_incluir.php) e o select
   #codigo_local (equivalente ao #codigo_fazenda do Contas a Pagar).
================================================================ */

var _locaisAntesEdicaoCtr = [];
var _replicarContaPendenteCtr = false;
var _modoRateioCtr = null; // null | 'valor' | 'perc'

// ── FASE 1: Confirma locais → tabela com selectpicker CC por local (1 confirmar global) ──
function confirmarLocaisRateioCtr() {
    var $local = $('#codigo_local');
    var selecionados = $local.val();
    if (!selecionados || selecionados.length === 0) return;

    // Se seleção não mudou, apenas fecha o selector sem reconstruir
    var novosSorted   = selecionados.slice().sort();
    var antesSorted   = _locaisAntesEdicaoCtr.slice().sort();
    var semMudanca    = (novosSorted.length === antesSorted.length &&
                        novosSorted.every(function(v, i) { return v === antesSorted[i]; }));
    if (semMudanca && _locaisAntesEdicaoCtr.length > 0) {
        $('#btn_fechar_local').remove();
        $('#tr_local_input').hide();
        return;
    }

    // ── Se Phase 3 está ativa, apenas adiciona/remove locais incrementalmente ──
    var $linhasFase3 = $('#tbl_rateio tbody tr.linha-valor-rateio');
    if ($linhasFase3.length > 0) {
        var locaisJaPresentes = [];
        $linhasFase3.each(function() {
            var lid = String($(this).data('local-id'));
            if (locaisJaPresentes.indexOf(lid) === -1) locaisJaPresentes.push(lid);
        });
        // Também conta as linhas tr-novo-local pendentes
        $('#tbl_rateio tbody tr.tr-novo-local').each(function() {
            var lid = String($(this).data('local-id'));
            if (locaisJaPresentes.indexOf(lid) === -1) locaisJaPresentes.push(lid);
        });

        // Remove locais que foram desmarcados
        var locaisRemovidos = locaisJaPresentes.filter(function(id) {
            return selecionados.indexOf(id) === -1 && selecionados.indexOf(Number(id)) === -1;
        });
        $.each(locaisRemovidos, function(i, localId) {
            $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"]').remove();
            $('#tbl_rateio tbody tr.tr-novo-local[data-local-id="' + localId + '"]').remove();
        });

        // Adiciona locais novos
        var locaisNovos = selecionados.filter(function(id) {
            return locaisJaPresentes.indexOf(String(id)) === -1;
        });
        $.each(locaisNovos, function(i, idLocal) {
            var $opt = $local.find('option[value="' + idLocal + '"]');
            var nomeLocal = $opt.data('nome') || $opt.text();
            _adicionarNovoLocalFase3Ctr(idLocal, nomeLocal);
        });

        $('#btn_fechar_local').remove();
        $('#tr_local_input').hide();
        fixarIconeSelecLocaisCtr();
        recalcularRateioCtr();
        return;
    }

    $('#linhas_rateio').hide().empty();
    $('#rodape_fase2').remove();
    $('#rodape_rateio').remove();
    $('#col_btn_confirmar_locais').hide();
    $('#rodape_fase1').remove();

    var optionsCC = '';
    $.each(CTR_CCS, function(i, cc) {
        optionsCC += '<option value="' + cc.id + '">' + cc.nome + '</option>';
    });

    var html = '<table class="tbl-parcelas" id="tbl_rateio" style="width:100%;table-layout:fixed;">';
    html += '<colgroup><col style="width:16%"><col style="width:16%"><col style="width:26%"><col style="width:14%"><col style="width:9%"><col style="width:9%"></colgroup><tbody>';

    $.each(selecionados, function(i, idLocal) {
        var $opt = $local.find('option[value="' + idLocal + '"]');
        var nomeLocal = $opt.data('nome') || $opt.text();
        var idxCC = 'cc_rateio_' + i;
        var isLast = (i === selecionados.length - 1);
        html += '<tr class="linha-fase1" data-local-id="' + idLocal + '" data-local-nome="' + nomeLocal.replace(/"/g,'&quot;') + '">';
        html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + nomeLocal + '</span></td>';
        html += '<td style="vertical-align:middle;padding:4px 8px;"><select class="selectpicker fase1-cc" id="' + idxCC + '" multiple data-live-search="true" data-size="8" data-width="100%">' + optionsCC + '</select></td>';
        if (isLast) {
            html += '<td style="vertical-align:middle;padding:4px 8px;"><button type="button" class="btn btn-primary" onclick="confirmarTodoCCCtr()">Confirmar</button></td><td colspan="3"></td>';
        } else {
            html += '<td colspan="4"></td>';
        }
        html += '</tr>';
    });

    html += '</tbody></table>';
    $('#linhas_rateio').html(html);

    $('#linhas_rateio .fase1-cc').each(function() {
        var $s = $(this);
        $s.find('option:first').prop('selected', true);
        $s.selectpicker({ actionsBox: true, noneSelectedText: '...', selectedTextFormat: 'values' });
        var $bs = $s.closest('.bootstrap-select');
        $bs.css({ 'width': '100%', 'display': 'block' });
        $bs.find('.bs-select-all').hide();
        $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
        $bs.find('.dropdown-menu').css({ 'min-width': '280px', 'width': 'auto' });
    });

    $('#btn_fechar_local').remove();
    $('#tr_local_input').hide();
    $('#linhas_rateio').show();
    fixarIconeSelecLocaisCtr();
}

// ── FASE 2: Lê CC de todas as linhas → tabela com selectpicker Conta por linha Local+CC ──
function confirmarTodoCCCtr() {
    document.activeElement && document.activeElement.blur();
    var linhas = [];
    var valido = true;

    $('#tbl_rateio tbody tr.linha-fase1').each(function() {
        var localId   = $(this).data('local-id');
        var localNome = $(this).data('local-nome');
        var ccIds = [];
        $(this).find('.fase1-cc option:selected').each(function() {
            ccIds.push($(this).val());
        });
        if (ccIds.length === 0) {
            alert('Selecione pelo menos um Centro de Custos para cada local.');
            valido = false; return false;
        }
        $.each(ccIds, function(j, ccId) {
            var ccNome = ccId;
            $.each(CTR_CCS, function(k, cc) { if (String(cc.id) === String(ccId)) { ccNome = cc.nome; return false; } });
            linhas.push({ localId: localId, localNome: localNome, ccId: ccId, ccNome: ccNome });
        });
    });

    if (!valido) return;
    $('#rodape_fase1').remove();

    var optionsConta = '';
    $.each(CTR_CONTAS_RAT, function(k, cta) {
        if (cta.nivel === 1)      optionsConta += '<option value="' + cta.id + '" disabled style="color:#777;font-weight:600;">' + cta.nome + '</option>';
        else if (cta.nivel === 2) optionsConta += '<option value="' + cta.id + '" disabled style="color:#888;">&nbsp;&nbsp;&nbsp;&nbsp;' + cta.nome + '</option>';
        else                      optionsConta += '<option value="' + cta.id + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + cta.nome + '</option>';
    });

    var html = '<table class="tbl-parcelas" id="tbl_rateio" style="width:100%;table-layout:fixed;">';
    html += '<colgroup><col style="width:16%"><col style="width:16%"><col style="width:26%"><col style="width:14%"><col style="width:9%"><col style="width:9%"></colgroup><tbody>';

    var lastLocalId = null;
    $.each(linhas, function(i, ln) {
        var idxConta = 'conta_rateio_' + i;
        var showLocal = (ln.localId !== lastLocalId);
        lastLocalId = ln.localId;
        html += '<tr class="linha-fase2"';
        html += ' data-local-id="'   + ln.localId   + '"';
        html += ' data-local-nome="' + ln.localNome.replace(/"/g,'&quot;') + '"';
        html += ' data-cc-id="'      + ln.ccId      + '"';
        html += ' data-cc-nome="'    + ln.ccNome.replace(/"/g,'&quot;') + '">';
        if (showLocal) {
            html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + ln.localNome + '</span></td>';
            html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + ln.ccNome + '</span>' +
                    ' <a href="#" onclick="editarCCDoLocalCtr(\'' + ln.localId + '\',\'' + ln.localNome.replace(/'/g,"\\'") + '\');return false;" data-toggle="tooltip" data-placement="top" title="Selecione Centro de Custos" style="color:#337ab7;font-size:11px;margin-left:4px;">' +
                    '<i class="fas fa-pen"></i></a></td>';
        } else {
            html += '<td style="vertical-align:middle;padding:4px 8px;"></td>';
            html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + ln.ccNome + '</span></td>';
        }
        html += '<td style="vertical-align:middle;padding:4px 8px;"><select class="selectpicker fase2-conta" id="' + idxConta + '" multiple data-live-search="true" data-size="8" data-width="100%">';
        html += '<option value="" disabled>...</option>' + optionsConta;
        html += '</select></td>';
        html += '<td class="td-confirmar-conta" style="vertical-align:middle;padding:4px 8px;"></td>';
        html += '<td colspan="2"></td>';
        html += '</tr>';
    });

    html += '</tbody></table>';
    $('#linhas_rateio').html(html);

    $('#linhas_rateio .fase2-conta').each(function() {
        var $s = $(this);
        $s.selectpicker({ actionsBox: true, noneSelectedText: '...', selectedTextFormat: 'values' });
        var $bs = $s.closest('.bootstrap-select');
        $bs.css({ 'width': '100%', 'display': 'block' });
        $bs.find('.bs-select-all').hide();
        $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
        $bs.find('.dropdown-menu').css({ 'min-width': '360px', 'width': 'auto' });
    });
    $('#linhas_rateio .fase2-conta').each(function() {
        _bindReplicarContaCtr($(this));
    });
    $('#linhas_rateio [data-toggle="tooltip"]').tooltip();
    fixarConfirmarContaButtonCtr();
    fixarIconeSelecLocaisCtr();
}

// ── FASE 3: Lê Conta de todas as linhas → tabela final com Valor/% ──
function confirmarTodaContaCtr() {
    // Se a Fase 2 já não existe mais (ex.: retry tardio de uma chamada anterior que já
    // concluiu via replicação automática de contas), não há nada a confirmar. Sem essa
    // guarda, esse retry reconstrói a tabela vazia e duplica o rodapé Confirmar/Fechar.
    if ($('#tbl_rateio tbody tr.linha-fase2').length === 0) return;

    // Se algum dropdown de conta está aberto, fecha-o primeiro
    // (isso dispara hidden.bs.select → diálogo de replicação aparece antes da validação)
    var $dropdownAberto = $('#tbl_rateio .bootstrap-select.open');
    if ($dropdownAberto.length) {
        $dropdownAberto.find('button.dropdown-toggle').trigger('click');
        setTimeout(confirmarTodaContaCtr, 50);
        return;
    }
    // Aguarda o diálogo de replicação ser processado antes de validar
    if (_replicarContaPendenteCtr) return;

    var linhas = [];
    var valido = true;

    $('#tbl_rateio tbody tr.linha-fase2').each(function() {
        var localId   = $(this).data('local-id');
        var localNome = $(this).data('local-nome');
        var ccId      = $(this).data('cc-id');
        var ccNome    = $(this).data('cc-nome');
        var contaIds = [];
        $(this).find('.fase2-conta option:selected').each(function() {
            if ($(this).val()) contaIds.push($(this).val());
        });
        if (contaIds.length === 0) {
            alert('Selecione pelo menos uma Conta Contábil para cada linha.');
            valido = false; return false;
        }
        $.each(contaIds, function(k, contaId) {
            var contaNome = contaId;
            $.each(CTR_CONTAS_RAT, function(m, ct) { if (String(ct.id) === String(contaId)) { contaNome = ct.nome; return false; } });
            linhas.push({ localId: localId, localNome: localNome, ccId: ccId, ccNome: ccNome, contaId: contaId, contaNome: contaNome });
        });
    });

    if (!valido) return;
    $('#rodape_fase2').remove();

    var html = '<table class="tbl-parcelas" id="tbl_rateio" style="width:100%;table-layout:fixed;">';
    html += '<colgroup><col style="width:16%"><col style="width:16%"><col style="width:26%"><col style="width:14%"><col style="width:9%"><col style="width:9%"></colgroup><tbody>';

    var lastLocalId = null, lastGroupKey = null;
    $.each(linhas, function(i, ln) {
        var groupKey    = ln.localId + '_' + ln.ccId;
        var showLocal   = (ln.localId !== lastLocalId);
        var showCC      = (groupKey !== lastGroupKey);
        lastLocalId     = ln.localId;
        lastGroupKey    = groupKey;
        var localNomeJs = ln.localNome.replace(/'/g,"\\'");
        var ccNomeJs    = ln.ccNome.replace(/'/g,"\\'");

        html += '<tr class="linha-valor-rateio"' +
            ' data-local-id="'   + ln.localId   + '"' +
            ' data-local-nome="' + ln.localNome.replace(/"/g,'&quot;') + '"' +
            ' data-cc-id="'      + ln.ccId      + '"' +
            ' data-cc-nome="'    + ln.ccNome.replace(/"/g,'&quot;') + '"' +
            ' data-conta-id="'   + ln.contaId   + '"' +
            ' data-conta-nome="' + ln.contaNome.replace(/"/g,'&quot;') + '">';

        if (showLocal) {
            html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + ln.localNome + '</span>' +
                '<input type="hidden" name="rat2_local_id[]" value="' + ln.localId + '">' +
                '<input type="hidden" name="rat2_local_nome[]" value="' + ln.localNome + '">' +
                '</td>';
        } else {
            html += '<td style="vertical-align:middle;padding:4px 8px;">' +
                '<input type="hidden" name="rat2_local_id[]" value="' + ln.localId + '">' +
                '<input type="hidden" name="rat2_local_nome[]" value="' + ln.localNome + '">' +
                '</td>';
        }

        if (showCC) {
            html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + ln.ccNome + '</span>' +
                (showLocal ? ' <a href="#" onclick="editarCCDoLocalFase3Ctr(\'' + ln.localId + '\',\'' + localNomeJs + '\');return false;"' +
                ' data-toggle="tooltip" data-placement="top" title="Selecionar Centro de Custos"' +
                ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' : '') +
                '<input type="hidden" name="rat2_cc_id[]" value="' + ln.ccId + '">' +
                '<input type="hidden" name="rat2_cc_nome[]" value="' + ln.ccNome + '">' +
                '</td>';
        } else {
            html += '<td style="vertical-align:middle;padding:4px 8px;">' +
                '<input type="hidden" name="rat2_cc_id[]" value="' + ln.ccId + '">' +
                '<input type="hidden" name="rat2_cc_nome[]" value="' + ln.ccNome + '">' +
                '</td>';
        }

        if (showCC) {
            html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + ln.contaNome + '</span>' +
                ' <a href="#" onclick="editarContaDoCCCtr(\'' + ln.localId + '\',\'' + ln.ccId + '\',\'' + localNomeJs + '\',\'' + ccNomeJs + '\');return false;"' +
                ' data-toggle="tooltip" data-placement="top" title="Selecionar Contas"' +
                ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' +
                '<input type="hidden" name="rat2_conta_id[]" value="' + ln.contaId + '">' +
                '<input type="hidden" name="rat2_conta_nome[]" value="' + ln.contaNome + '">' +
                '</td>';
        } else {
            html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + ln.contaNome + '</span>' +
                '<input type="hidden" name="rat2_conta_id[]" value="' + ln.contaId + '">' +
                '<input type="hidden" name="rat2_conta_nome[]" value="' + ln.contaNome + '">' +
                '</td>';
        }

        html += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
            '<input type="text" class="form-control rat-valor" placeholder="0,00" name="rat2_valor[]"' +
            ' style="height:30px;font-size:13px;text-align:right;"></td>';
        html += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
            '<input type="text" class="form-control rat-perc" placeholder="0,00%" name="rat2_perc[]"' +
            ' style="height:30px;font-size:13px;text-align:right;"></td>';
        html += '<td></td></tr>';
    });

    html += '<tr id="tr_rateio_restante">';
    html += '<td colspan="4" style="text-align:right;font-size:12px;color:#666;padding:6px 8px;border-top:1px solid #ddd;">Total Digitado: <span id="span_rat_total" style="color:#27ae60;font-weight:600;font-size:13px;margin-right:14px;">R$ 0,00</span>&nbsp;&nbsp;&nbsp;Restante a distribuir:</td>';
    html += '<td id="td_rat_vlr_rest" style="font-size:13px;font-weight:600;color:#c0392b;text-align:right;padding:6px 8px;white-space:nowrap;border-top:1px solid #ddd;">R$ 0,00</td>';
    html += '<td id="td_rat_pct_rest" style="font-size:13px;font-weight:600;color:#c0392b;text-align:right;padding:6px 8px;border-top:1px solid #ddd;">0,00%</td>';
    html += '</tr></tbody></table>';

    $('#linhas_rateio').html(html);
    $('#linhas_rateio').after(
        '<div id="rodape_rateio" style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding:4px 2px;">' +
        '<button type="button" id="btn_confirmar_rateio_final" class="btn btn-success" onclick="confirmarRateioFinalCtr()">Confirmar Rateio</button>' +
        '<button type="button" class="btn btn-default" onclick="voltarRateioCtr()">Fechar</button>' +
        '</div>'
    );

    $('#tbl_rateio [data-toggle="tooltip"]').tooltip();
    fixarIconeSelecLocaisCtr();
    _modoRateioCtr = null; // libera ambos os campos ao iniciar a fase de valores
    recalcularRateioCtr();
}

// ── Abre seletor de Contas para reeditar um grupo Local+CC ──
function editarContaDoCCCtr(localId, ccId, localNome, ccNome) {
    if (_temEditorAbertoCtr()) return;
    var gKey     = (localId + '_' + ccId).replace(/\W/g,'_');
    var editorId = 'tr_editar_conta_' + gKey;
    if ($('#' + editorId).length) return;

    var $linhasDoGrupo = $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"][data-cc-id="' + ccId + '"]');
    var contaIdsAtuais = [], valoresAtuais = {};
    $linhasDoGrupo.each(function() {
        var cid = String($(this).data('conta-id'));
        contaIdsAtuais.push(cid);
        valoresAtuais[cid] = $(this).find('.rat-valor').val();
    });

    var optionsConta = '';
    $.each(CTR_CONTAS_RAT, function(k, ct) {
        optionsConta += '<option value="' + ct.id + '">' + ct.nome + '</option>';
    });

    var selectId    = 'editar_conta_sel_' + gKey;
    var localNomeJs = localNome.replace(/'/g,"\\'");
    var ccNomeJs    = ccNome.replace(/'/g,"\\'");

    var editorHtml = '<tr id="' + editorId + '" class="tr-editar-conta"' +
        ' data-local-id="' + localId + '" data-cc-id="' + ccId + '"' +
        ' data-local-nome="' + localNome.replace(/"/g,'&quot;') + '" data-cc-nome="' + ccNome.replace(/"/g,'&quot;') + '">' +
        '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
        '<span class="lbl-parcela">' + localNome + '</span></td>' +
        '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
        '<span class="lbl-parcela">' + ccNome + '</span></td>' +
        '<td style="vertical-align:middle;padding:4px 8px;">' +
        '<select class="selectpicker" id="' + selectId + '" multiple data-live-search="true" data-size="8" data-width="100%">' + optionsConta + '</select></td>' +
        '<td style="vertical-align:middle;padding:4px 8px;white-space:nowrap;" colspan="3">' +
        '<button type="button" class="btn btn-primary" onmousedown="confirmarContaDoCCCtr(\'' + localId + '\',\'' + ccId + '\',\'' + localNomeJs + '\',\'' + ccNomeJs + '\')">Confirmar</button>' +
        ' <button type="button" class="btn btn-default" onclick="fecharEdicaoContaCtr(\'' + localId + '\',\'' + ccId + '\')">Fechar</button></td></tr>';

    var $firstRow = $linhasDoGrupo.first();
    if ($firstRow.length) { $firstRow.before(editorHtml); } else { $('#tr_rateio_restante').before(editorHtml); }

    var $s = $('#' + selectId);
    $s.selectpicker({ actionsBox: true, noneSelectedText: '...', selectedTextFormat: 'values' });
    $s.val(contaIdsAtuais);
    $s.selectpicker('refresh');
    var $bs = $s.closest('.bootstrap-select');
    $bs.css({ 'width': '100%', 'display': 'block' });
    $bs.find('.bs-select-all').hide();
    $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
    $bs.find('.dropdown-menu').css({ 'min-width': '360px', 'width': 'auto' });
    $('#' + editorId).data('valores-atuais', valoresAtuais).data('conta-ids-antes', contaIdsAtuais);

    fixarIconeSelecLocaisCtr();
}

// ── Confirma reedição de Contas de um grupo Local+CC ──
function confirmarContaDoCCCtr(localId, ccId, localNome, ccNome) {
    var gKey   = (localId + '_' + ccId).replace(/\W/g,'_');
    var $edRow = $('#tr_editar_conta_' + gKey);
    var valoresAtuais  = $edRow.data('valores-atuais') || {};
    var contaIdsAntes  = $edRow.data('conta-ids-antes') || [];

    var contaIds = [];
    $('#editar_conta_sel_' + gKey + ' option:selected').each(function() {
        if ($(this).val()) contaIds.push($(this).val());
    });
    if (contaIds.length === 0) { alert('Selecione pelo menos uma Conta Contábil.'); return; }

    var novosSorted = contaIds.slice().sort();
    var antesSorted = contaIdsAntes.slice().sort();
    var semMudanca  = (novosSorted.length === antesSorted.length &&
                      novosSorted.every(function(v, i) { return v === antesSorted[i]; }));
    if (semMudanca && contaIdsAntes.length > 0) {
        $edRow.remove();
        fixarIconeSelecLocaisCtr();
        return;
    }

    var $linhasDoGrupo = $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"][data-cc-id="' + ccId + '"]');
    var $insertBefore  = $linhasDoGrupo.length > 0 ? $linhasDoGrupo.last().next('tr') : $edRow.next('tr');
    $edRow.remove();
    $linhasDoGrupo.remove();

    var showLocal    = ($('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"]').length === 0);
    var localNomeEsc = localNome.replace(/"/g,'&quot;');
    var ccNomeEsc    = ccNome.replace(/"/g,'&quot;');
    var localNomeJs  = localNome.replace(/'/g,"\\'");
    var ccNomeJs     = ccNome.replace(/'/g,"\\'");

    var newRowsHtml = '';
    $.each(contaIds, function(i, contaId) {
        var contaNome = '';
        $.each(CTR_CONTAS_RAT, function(m, ct) { if (String(ct.id) === String(contaId)) { contaNome = ct.nome; return false; } });
        var valorSalvo = valoresAtuais[contaId] || '';
        var isFirst    = (i === 0);

        newRowsHtml += '<tr class="linha-valor-rateio"' +
            ' data-local-id="' + localId + '" data-cc-id="' + ccId + '"' +
            ' data-conta-id="' + contaId + '"' +
            ' data-local-nome="' + localNomeEsc + '" data-cc-nome="' + ccNomeEsc + '"' +
            ' data-conta-nome="' + contaNome.replace(/"/g,'&quot;') + '">';

        if (showLocal && isFirst) {
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + localNome + '</span>' +
                '<input type="hidden" name="rat2_local_id[]" value="' + localId + '">' +
                '<input type="hidden" name="rat2_local_nome[]" value="' + localNome + '">' +
                '</td>';
        } else {
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;">' +
                '<input type="hidden" name="rat2_local_id[]" value="' + localId + '">' +
                '<input type="hidden" name="rat2_local_nome[]" value="' + localNome + '">' +
                '</td>';
        }

        if (isFirst) {
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + ccNome + '</span>' +
                ' <a href="#" onclick="editarCCDoLocalFase3Ctr(\'' + localId + '\',\'' + localNomeJs + '\');return false;"' +
                ' data-toggle="tooltip" data-placement="top" title="Selecionar Centro de Custos"' +
                ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' +
                '<input type="hidden" name="rat2_cc_id[]" value="' + ccId + '">' +
                '<input type="hidden" name="rat2_cc_nome[]" value="' + ccNome + '">' +
                '</td>';
        } else {
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;">' +
                '<input type="hidden" name="rat2_cc_id[]" value="' + ccId + '">' +
                '<input type="hidden" name="rat2_cc_nome[]" value="' + ccNome + '">' +
                '</td>';
        }

        if (isFirst) {
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + contaNome + '</span>' +
                ' <a href="#" onclick="editarContaDoCCCtr(\'' + localId + '\',\'' + ccId + '\',\'' + localNomeJs + '\',\'' + ccNomeJs + '\');return false;"' +
                ' data-toggle="tooltip" data-placement="top" title="Selecionar Contas"' +
                ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' +
                '<input type="hidden" name="rat2_conta_id[]" value="' + contaId + '">' +
                '<input type="hidden" name="rat2_conta_nome[]" value="' + contaNome + '">' +
                '</td>';
        } else {
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + contaNome + '</span>' +
                '<input type="hidden" name="rat2_conta_id[]" value="' + contaId + '">' +
                '<input type="hidden" name="rat2_conta_nome[]" value="' + contaNome + '">' +
                '</td>';
        }

        newRowsHtml += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
            '<input type="text" class="form-control rat-valor" placeholder="0,00" name="rat2_valor[]"' +
            ' style="height:30px;font-size:13px;text-align:right;"' +
            (valorSalvo ? ' value="' + valorSalvo + '"' : '') + '></td>';
        newRowsHtml += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
            '<input type="text" class="form-control rat-perc" placeholder="0,00%" name="rat2_perc[]"' +
            ' style="height:30px;font-size:13px;text-align:right;"></td>';
        newRowsHtml += '<td></td></tr>';
    });

    if ($insertBefore.length) { $insertBefore.before(newRowsHtml); } else { $('#tr_rateio_restante').before(newRowsHtml); }

    $('#tbl_rateio [data-toggle="tooltip"]').tooltip();
    fixarIconeSelecLocaisCtr();
    _sincronizarIconesCCCtr();
    recalcularRateioCtr();
}

// ── Gera HTML de uma linha de valor/rateio ──
function gerarLinhaValorRateioCtr(localId, localNome, ccId, ccNome, contaId, contaNome) {
    var html = '<tr class="linha-valor-rateio">';
    html += '<td><span class="lbl-parcela">' + localNome + '</span>' +
                '<input type="hidden" name="rat2_local_id[]"   value="' + localId   + '">' +
                '<input type="hidden" name="rat2_local_nome[]" value="' + localNome + '">' +
            '</td>';
    html += '<td><span class="lbl-parcela">' + ccNome + '</span>' +
                '<input type="hidden" name="rat2_cc_id[]"   value="' + ccId   + '">' +
                '<input type="hidden" name="rat2_cc_nome[]" value="' + ccNome + '">' +
            '</td>';
    html += '<td><span class="lbl-parcela">' + contaNome + '</span>' +
                '<input type="hidden" name="rat2_conta_id[]"   value="' + contaId   + '">' +
                '<input type="hidden" name="rat2_conta_nome[]" value="' + contaNome + '">' +
            '</td>';
    html += '<td style="text-align:right;">' +
                '<input type="text" class="form-control rat-valor" placeholder="0,00" name="rat2_valor[]"' +
                ' style="height:30px;font-size:13px;text-align:right;">' +
            '</td>';
    html += '<td style="text-align:right;">' +
                '<input type="text" class="form-control rat-perc" placeholder="0,00%" name="rat2_perc[]"' +
                ' style="height:30px;font-size:13px;text-align:right;">' +
            '</td>';
    html += '<td style="text-align:center;">' +
                '<button type="button" class="btn btn-xs" onclick="excluirLinhaRateioCtr(this)" title="Remover" style="background:transparent;border:none;color:#2980b9;padding:2px 6px;">' +
                '<i class="fas fa-trash"></i></button>' +
            '</td>';
    html += '</tr>';
    return html;
}

// ── Remove uma linha de rateio ──
function excluirLinhaRateioCtr(btn) {
    $(btn).closest('tr').remove();
    recalcularRateioCtr();
    // Se não restam linhas, reabilita Confirmar Rateio
    if ($('.linha-valor-rateio').length === 0) {
        $('#btn_confirmar_rateio_final').removeClass('btn-default').addClass('btn-success')
            .text('Confirmar Rateio').prop('disabled', false);
    }
}

// ── Adiciona linha em branco para distribuição manual ──
function adicionarLinhaRateioCtr() {
    // Linha com selects simples para escolher Local, CC e Conta
    var optLocal = '', optCC = '', optConta = '';

    // Opções de Local (das opções disponíveis no select principal)
    $('#codigo_local option:not([disabled])').each(function() {
        optLocal += '<option value="' + $(this).val() + '" data-nome="' + $(this).data('nome') + '">' + $(this).text() + '</option>';
    });
    $.each(CTR_CCS, function(i, cc) {
        optCC += '<option value="' + cc.id + '"' + (cc.id==='001'?' selected':'') + '>' + cc.nome + '</option>';
    });
    $.each(CTR_CONTAS_RAT, function(i, ct) {
        if (ct.nivel === 1) optConta += '<option value="' + ct.id + '" disabled style="color:#777;font-weight:600;">' + ct.nome + '</option>';
        else if (ct.nivel === 2) optConta += '<option value="' + ct.id + '" disabled style="color:#888;">    ' + ct.nome + '</option>';
        else optConta += '<option value="' + ct.id + '">        ' + ct.nome + '</option>';
    });

    var uid = 'manual_' + Date.now();
    var html = '<tr class="linha-valor-rateio linha-manual" id="tr_' + uid + '">';
    html += '<td><select class="form-control sel-local-manual" style="height:30px;font-size:12px;">' +
            '<option value="">...</option>' + optLocal + '</select></td>';
    html += '<td><select class="form-control sel-cc-manual" style="height:30px;font-size:12px;">' +
            optCC + '</select></td>';
    html += '<td><select class="form-control sel-conta-manual" style="height:30px;font-size:12px;">' +
            '<option value="">...</option>' + optConta + '</select></td>';
    html += '<td style="text-align:right;">' +
            '<input type="text" class="form-control rat-valor" placeholder="0,00" name="rat2_valor[]"' +
            ' style="height:30px;font-size:13px;text-align:right;"></td>';
    html += '<td><input type="text" class="form-control rat-perc" placeholder="0,00%" name="rat2_perc[]"' +
            ' style="height:30px;font-size:13px;text-align:right;"></td>';
    html += '<td style="text-align:center;"><button type="button" class="btn btn-primary btn-xs"' +
            ' onclick="confirmarLinhaManualCtr(this)" style="white-space:nowrap; font-size:11px; padding:3px 7px;">Confirmar</button></td>';
    html += '</tr>';

    // Insere antes da linha de totais
    $('#tr_rateio_restante').before(html);
    recalcularRateioCtr();
}

// ── Confirma linha adicionada manualmente ──
function confirmarLinhaManualCtr(btn) {
    var $tr = $(btn).closest('tr');

    var $selLocal = $tr.find('.sel-local-manual');
    var $selCC    = $tr.find('.sel-cc-manual');
    var $selConta = $tr.find('.sel-conta-manual');

    var localId   = $selLocal.val();
    var localNome = $selLocal.find('option:selected').data('nome') || $selLocal.find('option:selected').text();
    var ccId      = $selCC.val();
    var ccNome    = $selCC.find('option:selected').text();
    var contaId   = $selConta.val();
    var contaNome = $selConta.find('option:selected').text();

    if (!localId || localId === '') { alert('Selecione o Local.'); return; }
    if (!ccId   || ccId   === '') { alert('Selecione o Centro de Custos.'); return; }
    if (!contaId || contaId === '') { alert('Selecione a Conta Contábil.'); return; }

    // Pega o valor já digitado na linha antes de substituir
    var valorAtual = $tr.find('.rat-valor').val() || '';

    // Gera linha normal (com lixeira)
    var novaLinha = $(gerarLinhaValorRateioCtr(localId, localNome, ccId, ccNome, contaId, contaNome));
    if (valorAtual) {
        novaLinha.find('.rat-valor').val(valorAtual);
    }
    $tr.replaceWith(novaLinha);
    recalcularRateioCtr();
}

// ── Recalcula restante e percentuais em tempo real ──
function recalcularRateioCtr() {
    var total = ctrGetValorTotal();
    if (!total || total <= 0) total = 0;

    var somaValores = 0, restante = 0;

    if (_modoRateioCtr === 'perc') {
        // Modo %: calcula Valor a partir do percentual digitado
        $('.rat-perc').each(function() {
            var raw = $(this).val().replace('%','').replace(',','.');
            var pct = parseFloat(raw) || 0;
            var valor = total > 0 ? (pct / 100 * total) : 0;
            $(this).closest('tr').find('.rat-valor').val(valor > 0 ? formatMoney(valor) : '');
            somaValores += valor;
        });
    } else {
        // Modo Valor (ou sem modo): calcula % a partir do valor digitado
        $('.rat-valor').each(function() {
            somaValores += ctrParseMoney($(this).val());
        });
        // Atualiza percentuais de cada linha
        $('.rat-valor').each(function() {
            var $row = $(this).closest('tr');
            var v = ctrParseMoney($(this).val());
            var pct = total > 0 ? (v / total * 100) : 0;
            $row.find('.rat-perc').val(pct > 0 ? pct.toFixed(2).replace('.',',') + '%' : '');
        });
    }

    restante = total - somaValores;

    // Atualiza soma distribuída e restante
    $('#span_rat_total').text('R$ ' + somaValores.toFixed(2).replace('.',','));
    var corRest = (Math.abs(restante) < 0.01) ? '#27ae60' : '#c0392b';
    $('#td_rat_vlr_rest').text('R$ ' + restante.toFixed(2).replace('.',',')).css('color', corRest);
    $('#td_rat_pct_rest').text((total > 0 ? restante/total*100 : 0).toFixed(2).replace('.',',') + '%').css('color', corRest);

    // Garante que novas linhas recebam o estado correto de readonly
    _setModoRateioCtr(_modoRateioCtr);
}

// ── Valida e confirma o rateio completo ──
function confirmarRateioFinalCtr() {
    // Verifica se ainda há linhas de CC ou Conta pendentes de confirmação
    if ($('.linha-conta-rateio').length > 0) {
        alert('Confirme todos os Centros de Custos antes de fechar o rateio.');
        return;
    }

    var total = ctrGetValorTotal();
    var somaValores = 0;
    $('.rat-valor').each(function() {
        var raw = $(this).val();
        var v = raw.indexOf(',') !== -1
            ? raw.replace(/\./g,'').replace(',','.')
            : raw;
        somaValores += parseFloat(v) || 0;
    });

    if (Math.abs(total - somaValores) > 0.01) {
        alert('O valor distribuído (R$ ' + somaValores.toFixed(2).replace('.',',') + ') não corresponde ao valor total (R$ ' + total.toFixed(2).replace('.',',') + ').\nAjuste os valores antes de confirmar.');
        return;
    }

    if ($('.rat-valor').length === 0) {
        alert('Nenhuma distribuição informada.');
        return;
    }

    // Monta rateio_json no formato esperado pelo backend (local → ccs → contas)
    var locaisMap = {};
    var locaisOrder = [];

    $('.linha-valor-rateio').each(function() {
        var localId   = $(this).find('input[name="rat2_local_id[]"]').val();
        var localNome = $(this).find('input[name="rat2_local_nome[]"]').val();
        var ccId      = $(this).find('input[name="rat2_cc_id[]"]').val();
        var ccNome    = $(this).find('input[name="rat2_cc_nome[]"]').val();
        var contaId   = $(this).find('input[name="rat2_conta_id[]"]').val();
        var contaNome = $(this).find('input[name="rat2_conta_nome[]"]').val();
        var raw       = $(this).find('.rat-valor').val() || '0';
        var v         = raw.indexOf(',') !== -1
                        ? parseFloat(raw.replace(/\./g,'').replace(',','.'))
                        : (parseFloat(raw) || 0);
        var perc      = total > 0 ? parseFloat((v / total * 100).toFixed(4)) : 0;

        if (!locaisMap[localId]) {
            locaisMap[localId] = { id: localId, nome: localNome, valor: 0, perc: 0, ccs: {}, ccsOrder: [] };
            locaisOrder.push(localId);
        }
        locaisMap[localId].valor += v;

        var ccKey = ccId;
        if (!locaisMap[localId].ccs[ccKey]) {
            locaisMap[localId].ccs[ccKey] = { id: ccId, nome: ccNome, valor: 0, perc: 0, contas: [] };
            locaisMap[localId].ccsOrder.push(ccKey);
        }
        locaisMap[localId].ccs[ccKey].valor += v;
        locaisMap[localId].ccs[ccKey].contas.push({ id: contaId, nome: contaNome, valor: v, perc: perc });
    });

    var locaisArr = [];
    $.each(locaisOrder, function(i, localId) {
        var loc = locaisMap[localId];
        loc.perc = total > 0 ? parseFloat((loc.valor / total * 100).toFixed(4)) : 0;
        var ccsArr = [];
        $.each(loc.ccsOrder, function(j, ccKey) {
            var cc = loc.ccs[ccKey];
            cc.perc = total > 0 ? parseFloat((cc.valor / total * 100).toFixed(4)) : 0;
            ccsArr.push({ id: cc.id, nome: cc.nome, valor: cc.valor, perc: cc.perc, contas: cc.contas });
        });
        locaisArr.push({ id: loc.id, nome: loc.nome, valor: loc.valor, perc: loc.perc, ccs: ccsArr });
    });

    $('#rateio_json').val(JSON.stringify(locaisArr));

    // Tudo ok — oculta a seção de distribuição e exibe status "Rateio Configurado"
    $('#secao_distribuir_rateio').hide();
    $('#col_local').hide();
    $('#col_btn_confirmar_locais').hide();
    $('#rateio_status').show();
    $('#habilitar_rateio').prop('checked', true); // garante que o flag está ativo
}

// ── Coloca ícone Selecionar Locais após o nome do primeiro Local da tabela ──
function fixarIconeSelecLocaisCtr() {
    $('#tbl_rateio .ico-selec-locais').remove();
    var $td = $('#tbl_rateio tbody tr:first td:first');
    if ($td.length) {
        $td.append(
            '<a href="#" onclick="editarLocaisRateioCtr();return false;" class="ico-selec-locais"' +
            ' data-toggle="tooltip" data-placement="top" title="Selecionar Locais"' +
            ' style="color:#337ab7;font-size:11px;margin-left:6px;">' +
            '<i class="fas fa-pen"></i></a>'
        );
        $td.find('.ico-selec-locais').tooltip();
    }
}

// Garante que o ícone de editar CC aparece apenas na primeira linha de cada local
function _sincronizarIconesCCCtr() {
    var seenLocalIds = {};
    $('#tbl_rateio tbody tr.linha-valor-rateio').each(function() {
        var $tr        = $(this);
        var localId    = String($tr.data('local-id'));
        var localNome  = String($tr.data('local-nome') || '');
        var localNomeJs = localNome.replace(/'/g, "\\'");
        var $ccTd      = $tr.find('td').eq(1);
        var $ccSpan    = $ccTd.find('span.lbl-parcela');
        $ccTd.find('a').remove();
        if ($ccSpan.length && !seenLocalIds[localId]) {
            $ccSpan.after(
                '<a href="#" onclick="editarCCDoLocalFase3Ctr(\'' + localId + '\',\'' + localNomeJs + '\');return false;"' +
                ' data-toggle="tooltip" data-placement="top" title="Selecionar Centro de Custos"' +
                ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>'
            );
            seenLocalIds[localId] = true;
        }
    });
    $('#tbl_rateio [data-toggle="tooltip"]').tooltip();
}

// ── Move botão Confirmar Conta para a última linha-fase2 ──
function fixarConfirmarContaButtonCtr() {
    $('#tbl_rateio tbody tr.linha-fase2 .td-confirmar-conta').html('');
    $('#tbl_rateio tbody tr.linha-fase2:last .td-confirmar-conta').html(
        '<button type="button" class="btn btn-primary" onclick="confirmarTodaContaCtr()">Confirmar</button>'
    );
}

function _bindReplicarContaCtr($s) {
    var _ultimaSelecao = null;
    $s.on('hidden.bs.select', function() {
        var vals = $(this).val();
        if (!vals || vals.length === 0) return;

        var valsKey = vals.slice().sort().join(',');
        if (valsKey === _ultimaSelecao) return;
        _ultimaSelecao = valsKey;

        var selfEl = this;
        _replicarContaPendenteCtr = true; // bloqueia confirmarTodaContaCtr até o diálogo ser exibido
        setTimeout(function() {
            _replicarContaPendenteCtr = false;
            // Usa 'select.fase2-conta' para não contar os wrappers do bootstrap-select
            var $vazios = $('#tbl_rateio select.fase2-conta').not(selfEl).filter(function() {
                var v = $(this).val();
                return !v || v.length === 0;
            });
            if ($vazios.length === 0) return;

            var msg = 'Deseja replicar esta seleção para as ' + $vazios.length + ' linha(s) seguinte(s)?';
            if (!confirm(msg)) return;

            $vazios.each(function() {
                $(this).val(vals).selectpicker('refresh');
            });

            var $aindaVazios = $('#tbl_rateio select.fase2-conta').filter(function() {
                var v = $(this).val();
                return !v || v.length === 0;
            });
            if ($aindaVazios.length === 0) {
                confirmarTodaContaCtr();
            }
        }, 0);
    });
}

function _setModoRateioCtr(modo) {
    _modoRateioCtr = modo;
    if (modo === 'valor') {
        $('.rat-valor').prop('readonly', false).css({'background': '', 'color': ''});
        $('.rat-perc').prop('readonly', true).css({'background': '#f9f9f9', 'color': '#555'});
    } else if (modo === 'perc') {
        $('.rat-valor').prop('readonly', true).css({'background': '#f9f9f9', 'color': '#555'});
        $('.rat-perc').prop('readonly', false).css({'background': '', 'color': ''});
    } else {
        $('.rat-valor').prop('readonly', false).css({'background': '', 'color': ''});
        $('.rat-perc').prop('readonly', false).css({'background': '', 'color': ''});
    }
}

function _temEditorAbertoCtr() {
    if ($('#tr_local_input').is(':visible')) return true;
    if ($('#tbl_rateio .tr-editar-cc, #tbl_rateio .tr-editar-conta, #tbl_rateio .tr-novo-local').length > 0) return true;
    return false;
}

// ── Reabre seleção de CC para um Local específico (fase 2) ──
function editarCCDoLocalCtr(localId, localNome) {
    if (_temEditorAbertoCtr()) return;
    var editorId = 'tr_editar_cc_' + localId;
    if ($('#' + editorId).length) return;

    var $linhasDoLocal = $('#tbl_rateio tbody tr.linha-fase2[data-local-id="' + localId + '"]');
    var ccIdsAtuais = [];
    $linhasDoLocal.each(function() { ccIdsAtuais.push(String($(this).data('cc-id'))); });

    var optionsCC = '';
    $.each(CTR_CCS, function(k, cc) {
        optionsCC += '<option value="' + cc.id + '">' + cc.nome + '</option>';
    });

    var selectId    = 'editar_cc_sel_' + localId;
    var localNomeJs = localNome.replace(/'/g,"\\'");
    var editorHtml  = '<tr id="' + editorId + '" class="tr-editar-cc"' +
        ' data-local-id="' + localId + '" data-local-nome="' + localNome.replace(/"/g,'&quot;') + '">' +
        '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + localNome + '</span></td>' +
        '<td style="vertical-align:middle;padding:4px 8px;"><select class="selectpicker" id="' + selectId + '" multiple data-live-search="true" data-size="8" data-width="100%">' + optionsCC + '</select></td>' +
        '<td style="vertical-align:middle;padding:4px 8px;white-space:nowrap;">' +
        '<button type="button" class="btn btn-primary" onclick="confirmarCCDoLocalCtr(\'' + localId + '\',\'' + localNomeJs + '\')">Confirmar</button>' +
        ' <button type="button" class="btn btn-default" onclick="fecharEdicaoCCFase2Ctr(\'' + localId + '\')">Fechar</button></td>' +
        '<td colspan="3"></td></tr>';

    var $firstRow = $linhasDoLocal.first();
    if ($firstRow.length) { $firstRow.before(editorHtml); } else { $('#tbl_rateio tbody').append(editorHtml); }

    var $s = $('#' + selectId);
    $s.selectpicker({ actionsBox: true, noneSelectedText: '...', selectedTextFormat: 'values' });
    $s.val(ccIdsAtuais);
    $s.selectpicker('refresh');
    var $bs = $s.closest('.bootstrap-select');
    $bs.css({ 'width': '100%', 'display': 'block' });
    $bs.find('.bs-select-all').hide();
    $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
    $bs.find('.dropdown-menu').css({ 'min-width': '280px', 'width': 'auto' });
    $('#' + editorId).data('cc-ids-antes', ccIdsAtuais);
}

function fecharEdicaoCCFase2Ctr(localId) {
    var $s = $('#editar_cc_sel_' + localId);
    if ($s.length) $s.selectpicker('destroy');
    $('#tr_editar_cc_' + localId).remove();
    fixarConfirmarContaButtonCtr();
    fixarIconeSelecLocaisCtr();
}

// ── Confirma nova seleção de CC para um Local e reconstrói suas linhas ──
function confirmarCCDoLocalCtr(localId, localNome) {
    var selectId = 'editar_cc_sel_' + localId;
    var $select  = $('#' + selectId);
    var newCcIds = $select.val();
    if (!newCcIds || newCcIds.length === 0) { alert('Selecione pelo menos um Centro de Custos.'); return; }

    var $edRow     = $('#tr_editar_cc_' + localId);
    var ccIdsAntes = $edRow.data('cc-ids-antes') || [];
    var novosSorted = newCcIds.slice().sort();
    var antesSorted = ccIdsAntes.slice().sort();
    var semMudanca  = (novosSorted.length === antesSorted.length &&
                      novosSorted.every(function(v, i) { return v === antesSorted[i]; }));
    if (semMudanca && ccIdsAntes.length > 0) {
        $select.selectpicker('destroy');
        $edRow.remove();
        fixarConfirmarContaButtonCtr();
        fixarIconeSelecLocaisCtr();
        return;
    }

    var $linhasDoLocal = $('#tbl_rateio tbody tr.linha-fase2[data-local-id="' + localId + '"]');
    var $insertBefore  = $linhasDoLocal.length > 0 ? $linhasDoLocal.last().next('tr') : $edRow.next('tr');
    $linhasDoLocal.each(function() {
        var $sp = $(this).find('.selectpicker');
        if ($sp.length) $sp.selectpicker('destroy');
    });
    $linhasDoLocal.remove();

    var optionsConta = '';
    $.each(CTR_CONTAS_RAT, function(k, c) { optionsConta += '<option value="' + c.id + '">' + c.nome + '</option>'; });

    var localNomeEsc = localNome.replace(/"/g,'&quot;');
    var localNomeJs  = localNome.replace(/'/g,"\\'");
    var newRowsHtml  = '';

    $.each(newCcIds, function(i, ccId) {
        var ccNome = ccId;
        $.each(CTR_CCS, function(k, cc) { if (String(cc.id) === String(ccId)) { ccNome = cc.nome; return false; } });
        var idxConta = 'conta_edit_' + localId + '_' + ccId;
        newRowsHtml += '<tr class="linha-fase2"' +
            ' data-local-id="' + localId + '" data-local-nome="' + localNomeEsc + '"' +
            ' data-cc-id="' + ccId + '" data-cc-nome="' + ccNome.replace(/"/g,'&quot;') + '">';
        if (i === 0) {
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + localNome + '</span></td>';
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
                           '<span class="lbl-parcela">' + ccNome + '</span>' +
                           ' <a href="#" onclick="editarCCDoLocalCtr(\'' + localId + '\',\'' + localNomeJs + '\');return false;" data-toggle="tooltip" data-placement="top" title="Selecione Centro de Custos" style="color:#337ab7;font-size:11px;margin-left:4px;">' +
                           '<i class="fas fa-pen"></i></a></td>';
        } else {
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;"></td>';
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + ccNome + '</span></td>';
        }
        newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;"><select class="selectpicker fase2-conta" id="' + idxConta + '" multiple data-live-search="true" data-size="8" data-width="100%">';
        newRowsHtml += '<option value="" disabled>...</option>' + optionsConta + '</select></td>';
        newRowsHtml += '<td class="td-confirmar-conta" style="vertical-align:middle;padding:4px 8px;"></td>';
        newRowsHtml += '<td colspan="2"></td></tr>';
    });

    $select.selectpicker('destroy');
    $edRow.remove();
    if ($insertBefore.length && $insertBefore.is('tr')) { $insertBefore.before(newRowsHtml); } else { $('#tbl_rateio tbody').append(newRowsHtml); }

    $.each(newCcIds, function(i, ccId) {
        var $s = $('#conta_edit_' + localId + '_' + ccId);
        $s.selectpicker({ actionsBox: true, noneSelectedText: '...', selectedTextFormat: 'values' });
        var $bs = $s.closest('.bootstrap-select');
        $bs.css({ 'width': '100%', 'display': 'block' });
        $bs.find('.bs-select-all').hide();
        $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
        $bs.find('.dropdown-menu').css({ 'min-width': '360px', 'width': 'auto' });
        _bindReplicarContaCtr($s);
    });

    $('#tbl_rateio [data-toggle="tooltip"]').tooltip();
    fixarConfirmarContaButtonCtr();
    fixarIconeSelecLocaisCtr();
}

// ── Abre seletor de CC para reeditar um local dentro da fase 3 ──
function editarCCDoLocalFase3Ctr(localId, localNome) {
    if (_temEditorAbertoCtr()) return;
    var editorId = 'tr_editar_cc_f3_' + String(localId).replace(/\W/g,'_');
    if ($('#' + editorId).length) return;

    var $linhasDoLocal = $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"]');
    var ccIdsAtuais = [];
    $linhasDoLocal.each(function() {
        var cid = String($(this).data('cc-id'));
        if (ccIdsAtuais.indexOf(cid) === -1) ccIdsAtuais.push(cid);
    });

    var optionsCC = '';
    $.each(CTR_CCS, function(k, cc) {
        optionsCC += '<option value="' + cc.id + '">' + cc.nome + '</option>';
    });

    var selectId    = 'editar_cc_f3_sel_' + String(localId).replace(/\W/g,'_');
    var localNomeJs = localNome.replace(/'/g,"\\'");
    var editorHtml  = '<tr id="' + editorId + '" class="tr-editar-cc"' +
        ' data-local-id="' + localId + '" data-local-nome="' + localNome.replace(/"/g,'&quot;') + '">' +
        '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + localNome + '</span></td>' +
        '<td style="vertical-align:middle;padding:4px 8px;"><select class="selectpicker" id="' + selectId + '" multiple data-live-search="true" data-size="8" data-width="100%">' + optionsCC + '</select></td>' +
        '<td style="vertical-align:middle;padding:4px 8px;white-space:nowrap;">' +
        '<button type="button" class="btn btn-primary" onclick="confirmarCCDoLocalFase3Ctr(\'' + localId + '\',\'' + localNomeJs + '\')">Confirmar</button>' +
        ' <button type="button" class="btn btn-default" onclick="fecharEdicaoCCCtr(\'' + localId + '\')">Fechar</button></td>' +
        '<td colspan="3"></td></tr>';

    var $firstRow = $linhasDoLocal.first();
    if ($firstRow.length) { $firstRow.before(editorHtml); } else { $('#tr_rateio_restante').before(editorHtml); }

    var $s = $('#' + selectId);
    $s.selectpicker({ actionsBox: true, noneSelectedText: '...', selectedTextFormat: 'values' });
    $s.val(ccIdsAtuais);
    $s.selectpicker('refresh');
    var $bs = $s.closest('.bootstrap-select');
    $bs.css({ 'width': '100%', 'display': 'block' });
    $bs.find('.bs-select-all').hide();
    $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
    $bs.find('.dropdown-menu').css({ 'min-width': '280px', 'width': 'auto' });
    $('#' + editorId).data('cc-ids-antes', ccIdsAtuais);
    fixarIconeSelecLocaisCtr();
}

// ── Confirma CC re-selecionado na fase 3 → preserva CCs existentes, abre editor só para CCs novos ──
function confirmarCCDoLocalFase3Ctr(localId, localNome) {
    var selectId = 'editar_cc_f3_sel_' + String(localId).replace(/\W/g,'_');
    var ccIds = [];
    $('#' + selectId + ' option:selected').each(function() {
        if ($(this).val()) ccIds.push($(this).val());
    });
    if (ccIds.length === 0) { alert('Selecione pelo menos um Centro de Custos.'); return; }

    var $edRow     = $('#tr_editar_cc_f3_' + String(localId).replace(/\W/g,'_'));
    var ccIdsAntes = $edRow.data('cc-ids-antes') || [];

    var novosSorted = ccIds.slice().sort();
    var antesSorted = ccIdsAntes.slice().sort();
    var semMudanca  = (novosSorted.length === antesSorted.length &&
                      novosSorted.every(function(v, i) { return v === antesSorted[i]; }));
    if (semMudanca && ccIdsAntes.length > 0) {
        $edRow.remove();
        fixarIconeSelecLocaisCtr();
        return;
    }

    // CCs que foram adicionados e CCs que foram removidos
    var ccsNovos     = ccIds.filter(function(id) { return ccIdsAntes.indexOf(id) === -1; });
    var ccsRemovidos = ccIdsAntes.filter(function(id) { return ccIds.indexOf(id) === -1; });

    // Remove apenas as linhas dos CCs que foram desmarcados
    $.each(ccsRemovidos, function(i, ccId) {
        $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"][data-cc-id="' + ccId + '"]').remove();
    });

    // Ponto de inserção: após os rows existentes do local (que foram mantidos)
    var $linhasRestantes = $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"]');
    var $insertBefore    = $linhasRestantes.length > 0 ? $linhasRestantes.last().next('tr') : $edRow.next('tr');
    $edRow.remove();

    // Se não há CCs novos, apenas atualiza ícones e encerra
    if (ccsNovos.length === 0) {
        fixarIconeSelecLocaisCtr();
        _sincronizarIconesCCCtr();
        return;
    }

    // Monta editor de conta apenas para os CCs novos
    var optionsConta = '';
    $.each(CTR_CONTAS_RAT, function(k, ct) {
        optionsConta += '<option value="' + ct.id + '">' + ct.nome + '</option>';
    });

    var localNomeJs    = localNome.replace(/'/g,"\\'");
    var temLinhasLocal = $linhasRestantes.length > 0;
    var newRowsHtml = '';
    $.each(ccsNovos, function(j, ccId) {
        var ccNome = '';
        $.each(CTR_CCS, function(k, cc) { if (String(cc.id) === String(ccId)) { ccNome = cc.nome; return false; } });
        var ccNomeJs = ccNome.replace(/'/g,"\\'");
        var gKey     = (localId + '_' + ccId).replace(/\W/g,'_');
        newRowsHtml += '<tr id="tr_editar_conta_' + gKey + '" class="tr-editar-conta"' +
            ' data-local-id="' + localId + '" data-cc-id="' + ccId + '"' +
            ' data-local-nome="' + localNome.replace(/"/g,'&quot;') + '" data-cc-nome="' + ccNome.replace(/"/g,'&quot;') + '">' +
            '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
            (!temLinhasLocal && j === 0 ? '<span class="lbl-parcela">' + localNome + '</span>' : '') + '</td>' +
            '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
            '<span class="lbl-parcela">' + ccNome + '</span></td>' +
            '<td style="vertical-align:middle;padding:4px 8px;">' +
            '<select class="selectpicker" id="editar_conta_sel_' + gKey + '" multiple data-live-search="true" data-size="8" data-width="100%">' + optionsConta + '</select></td>' +
            '<td style="vertical-align:middle;padding:4px 8px;white-space:nowrap;" colspan="3">' +
            '<button type="button" class="btn btn-primary" onmousedown="confirmarContaDoCCCtr(\'' + localId + '\',\'' + ccId + '\',\'' + localNomeJs + '\',\'' + ccNomeJs + '\')">Confirmar</button>' +
            ' <button type="button" class="btn btn-default" onclick="fecharEdicaoContaCtr(\'' + localId + '\',\'' + ccId + '\')">Fechar</button></td></tr>';
    });

    if ($insertBefore.length) { $insertBefore.before(newRowsHtml); } else { $('#tr_rateio_restante').before(newRowsHtml); }

    $.each(ccsNovos, function(j, ccId) {
        var gKey = (localId + '_' + ccId).replace(/\W/g,'_');
        var $s = $('#editar_conta_sel_' + gKey);
        $s.selectpicker({ actionsBox: true, noneSelectedText: '...', selectedTextFormat: 'values' });
        var $bs = $s.closest('.bootstrap-select');
        $bs.css({ 'width': '100%', 'display': 'block' });
        $bs.find('.bs-select-all').hide();
        $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
        $bs.find('.dropdown-menu').css({ 'min-width': '360px', 'width': 'auto' });
        $('#tr_editar_conta_' + gKey).data('valores-atuais', {});
    });

    fixarIconeSelecLocaisCtr();
    _sincronizarIconesCCCtr();
}

function editarLocaisRateioCtr() {
    if (_temEditorAbertoCtr()) return;
    _locaisAntesEdicaoCtr = [];
    $('#tbl_rateio tbody tr.linha-fase1, #tbl_rateio tbody tr.linha-fase2, #tbl_rateio tbody tr.linha-valor-rateio, #tbl_rateio tbody tr.tr-novo-local').each(function() {
        var localId = String($(this).data('local-id'));
        if (localId && _locaisAntesEdicaoCtr.indexOf(localId) === -1) _locaisAntesEdicaoCtr.push(localId);
    });

    $('#tr_local_input').show();
    var $local = $('#codigo_local');
    $local.val(_locaisAntesEdicaoCtr);
    $local.selectpicker('refresh');
    $('#btn_confirmar_locais').show();
    if (!$('#btn_fechar_local').length) {
        $('#td_local_confirm').append('<button type="button" id="btn_fechar_local" class="btn btn-default" style="margin-left:6px;" onclick="fecharEdicaoLocalCtr()">Fechar</button>');
    }
}

function _executarRateioOffCtr() {
    var $local = $('#codigo_local');
    $('#col_cc').show();
    $('#col_conta').show();
    $local.off('changed.bs.select.rateio');
    if ($local.hasClass('selectpicker')) { $local.selectpicker('destroy'); }
    $local.removeAttr('multiple').removeAttr('data-live-search').removeAttr('data-size')
          .removeClass('selectpicker').addClass('form-control');
    $local.val('');
    $('#col_local label').after($local);
    $('#btn_fechar_local').remove();
    $('#col_btn_confirmar_locais').append($('#btn_confirmar_locais'));
    $('#tr_local_input').show();
    $('#col_btn_confirmar_locais').hide();
    $('#secao_distribuir_rateio').hide();
    $('#linhas_rateio').hide().empty();
    $('#rodape_rateio').remove();
    $('#rateio_status').hide();
    $('#col_local').show();
    $('#rateio_json').val('');
}

function confirmarFecharRateioCtr() {
    $('#modal_fechar_rateio').modal('hide');
    $('#habilitar_rateio').prop('checked', false);
    _executarRateioOffCtr();
}

function fecharEdicaoLocalCtr() {
    $('#btn_fechar_local').remove();
    $('#tr_local_input').hide();
}

// ── Insere editor inline para novo local adicionado em Phase 3 ──
function _adicionarNovoLocalFase3Ctr(localId, localNome) {
    var safeId = String(localId).replace(/\W/g,'_');
    if ($('#tr_novo_local_' + safeId).length) return;

    var primeiroCCId   = CTR_CCS.length > 0 ? String(CTR_CCS[0].id)   : '';
    var primeiroCCNome = CTR_CCS.length > 0 ? String(CTR_CCS[0].nome) : '';

    var optionsConta = '';
    $.each(CTR_CONTAS_RAT, function(k, cta) {
        if (cta.nivel === 1)      optionsConta += '<option value="' + cta.id + '" disabled style="color:#777;font-weight:600;">' + cta.nome + '</option>';
        else if (cta.nivel === 2) optionsConta += '<option value="' + cta.id + '" disabled style="color:#888;">&nbsp;&nbsp;&nbsp;&nbsp;' + cta.nome + '</option>';
        else                      optionsConta += '<option value="' + cta.id + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + cta.nome + '</option>';
    });

    var optionsCC = '';
    $.each(CTR_CCS, function(k, cc) {
        optionsCC += '<option value="' + cc.id + '"' + (String(cc.id) === primeiroCCId ? ' selected' : '') + '>' + cc.nome + '</option>';
    });

    var localNomeEsc = localNome.replace(/"/g,'&quot;');
    var ccNomeEsc    = primeiroCCNome.replace(/"/g,'&quot;');

    var html = '<tr id="tr_novo_local_' + safeId + '" class="tr-novo-local"' +
        ' data-local-id="' + localId + '" data-local-nome="' + localNomeEsc + '"' +
        ' data-cc-id="' + primeiroCCId + '" data-cc-nome="' + ccNomeEsc + '">' +
        '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
        '<span class="lbl-parcela">' + localNome + '</span>' +
        '</td>' +
        '<td style="vertical-align:top;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
        '<span class="cc-nome-nv lbl-parcela">' + primeiroCCNome + '</span>' +
        ' <a href="#" onclick="_editarCCNovoLocalCtr(\'' + localId + '\');return false;"' +
        ' data-toggle="tooltip" data-placement="top" title="Selecionar Centro de Custos"' +
        ' style="color:#337ab7;font-size:11px;margin-left:4px;" class="ico-editar-cc-nv"><i class="fas fa-pen"></i></a>' +
        '<div class="cc-editor-nv" style="display:none;margin-top:4px;">' +
        '<select class="form-control cc-select-nv" style="height:30px;font-size:13px;">' + optionsCC + '</select>' +
        '<div style="margin-top:4px;">' +
        '<button type="button" class="btn btn-primary btn-xs" onclick="_confirmarCCNovoLocalCtr(\'' + localId + '\')">OK</button>' +
        ' <button type="button" class="btn btn-default btn-xs" onclick="_fecharCCNovoLocalCtr(\'' + localId + '\')">Fechar</button>' +
        '</div></div>' +
        '</td>' +
        '<td style="vertical-align:middle;padding:4px 8px;">' +
        '<select class="selectpicker conta-sel-nv" id="conta_nv_' + safeId + '" multiple data-live-search="true" data-size="8" data-width="100%">' +
        '<option value="" disabled>...</option>' + optionsConta +
        '</select>' +
        '</td>' +
        '<td style="vertical-align:middle;padding:4px 8px;white-space:nowrap;" colspan="3">' +
        '<button type="button" class="btn btn-primary" onclick="confirmarNovoLocalFase3Ctr(\'' + localId + '\')">Confirmar</button>' +
        '</td>' +
        '</tr>';

    $('#tr_rateio_restante').before(html);

    var $s = $('#conta_nv_' + safeId);
    $s.selectpicker({ actionsBox: true, noneSelectedText: '...', selectedTextFormat: 'values' });
    var $bs = $s.closest('.bootstrap-select');
    $bs.css({ 'width': '100%', 'display': 'block' });
    $bs.find('.bs-select-all').hide();
    $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
    $bs.find('.dropdown-menu').css({ 'min-width': '360px', 'width': 'auto' });

    $('#tr_novo_local_' + safeId + ' [data-toggle="tooltip"]').tooltip();
}

function _editarCCNovoLocalCtr(localId) {
    var safeId = String(localId).replace(/\W/g,'_');
    var $row = $('#tr_novo_local_' + safeId);
    $row.find('.cc-nome-nv').hide();
    $row.find('.ico-editar-cc-nv').hide();
    $row.find('.cc-editor-nv').show();
}

function _fecharCCNovoLocalCtr(localId) {
    var safeId = String(localId).replace(/\W/g,'_');
    var $row = $('#tr_novo_local_' + safeId);
    $row.find('.cc-editor-nv').hide();
    $row.find('.cc-nome-nv').show();
    $row.find('.ico-editar-cc-nv').show();
}

function _confirmarCCNovoLocalCtr(localId) {
    var safeId = String(localId).replace(/\W/g,'_');
    var $row = $('#tr_novo_local_' + safeId);
    var $sel = $row.find('.cc-select-nv');
    var ccId   = $sel.val();
    var ccNome = $sel.find('option:selected').text();
    $row.attr('data-cc-id', ccId).attr('data-cc-nome', ccNome.replace(/"/g,'&quot;'));
    $row.find('.cc-nome-nv').text(ccNome);
    _fecharCCNovoLocalCtr(localId);
}

function confirmarNovoLocalFase3Ctr(localId) {
    var safeId = String(localId).replace(/\W/g,'_');
    var $row = $('#tr_novo_local_' + safeId);

    var localNome = String($row.attr('data-local-nome') || '');
    var ccId      = String($row.attr('data-cc-id')    || '');
    var ccNome    = String($row.attr('data-cc-nome')  || '');

    var contaIds = [];
    $('#conta_nv_' + safeId + ' option:selected').each(function() {
        if ($(this).val()) contaIds.push($(this).val());
    });
    if (contaIds.length === 0) { alert('Selecione pelo menos uma Conta Contábil.'); return; }

    $('#conta_nv_' + safeId).selectpicker('destroy');

    var localNomeEsc = localNome.replace(/"/g,'&quot;');
    var ccNomeEsc    = ccNome.replace(/"/g,'&quot;');
    var localNomeJs  = localNome.replace(/'/g,"\\'");
    var ccNomeJs     = ccNome.replace(/'/g,"\\'");

    var newRowsHtml = '';
    $.each(contaIds, function(i, contaId) {
        var contaNome = '';
        $.each(CTR_CONTAS_RAT, function(m, ct) { if (String(ct.id) === String(contaId)) { contaNome = ct.nome; return false; } });
        var isFirst = (i === 0);

        newRowsHtml += '<tr class="linha-valor-rateio"' +
            ' data-local-id="' + localId + '" data-cc-id="' + ccId + '"' +
            ' data-conta-id="' + contaId + '"' +
            ' data-local-nome="' + localNomeEsc + '" data-cc-nome="' + ccNomeEsc + '"' +
            ' data-conta-nome="' + contaNome.replace(/"/g,'&quot;') + '">';

        if (isFirst) {
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + localNome + '</span>' +
                '<input type="hidden" name="rat2_local_id[]" value="' + localId + '">' +
                '<input type="hidden" name="rat2_local_nome[]" value="' + localNome + '">' +
                '</td>';
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + ccNome + '</span>' +
                ' <a href="#" onclick="editarCCDoLocalFase3Ctr(\'' + localId + '\',\'' + localNomeJs + '\');return false;"' +
                ' data-toggle="tooltip" data-placement="top" title="Selecionar Centro de Custos"' +
                ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' +
                '<input type="hidden" name="rat2_cc_id[]" value="' + ccId + '">' +
                '<input type="hidden" name="rat2_cc_nome[]" value="' + ccNome + '">' +
                '</td>';
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + contaNome + '</span>' +
                ' <a href="#" onclick="editarContaDoCCCtr(\'' + localId + '\',\'' + ccId + '\',\'' + localNomeJs + '\',\'' + ccNomeJs + '\');return false;"' +
                ' data-toggle="tooltip" data-placement="top" title="Selecionar Contas"' +
                ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' +
                '<input type="hidden" name="rat2_conta_id[]" value="' + contaId + '">' +
                '<input type="hidden" name="rat2_conta_nome[]" value="' + contaNome + '">' +
                '</td>';
        } else {
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;">' +
                '<input type="hidden" name="rat2_local_id[]" value="' + localId + '">' +
                '<input type="hidden" name="rat2_local_nome[]" value="' + localNome + '">' +
                '</td>';
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;">' +
                '<input type="hidden" name="rat2_cc_id[]" value="' + ccId + '">' +
                '<input type="hidden" name="rat2_cc_nome[]" value="' + ccNome + '">' +
                '</td>';
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + contaNome + '</span>' +
                '<input type="hidden" name="rat2_conta_id[]" value="' + contaId + '">' +
                '<input type="hidden" name="rat2_conta_nome[]" value="' + contaNome + '">' +
                '</td>';
        }

        newRowsHtml += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
            '<input type="text" class="form-control rat-valor" placeholder="0,00" name="rat2_valor[]"' +
            ' style="height:30px;font-size:13px;text-align:right;"></td>';
        newRowsHtml += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
            '<input type="text" class="form-control rat-perc" placeholder="0,00%" name="rat2_perc[]"' +
            ' style="height:30px;font-size:13px;text-align:right;"></td>';
        newRowsHtml += '<td></td></tr>';
    });

    $row.before(newRowsHtml);
    $row.remove();

    $('#tbl_rateio [data-toggle="tooltip"]').tooltip();
    fixarIconeSelecLocaisCtr();
    _sincronizarIconesCCCtr();
    recalcularRateioCtr();
}

function fecharEdicaoCCCtr(localId) {
    $('#tr_editar_cc_f3_' + String(localId).replace(/\W/g,'_')).remove();
    fixarIconeSelecLocaisCtr();
}

function fecharEdicaoContaCtr(localId, ccId) {
    var gKey = (localId + '_' + ccId).replace(/\W/g,'_');
    $('#tr_editar_conta_' + gKey).remove();
    fixarIconeSelecLocaisCtr();
}

// ── Reabre a configuração do rateio para edição ──
function editarRateioCtr() {
    $('#rateio_status').hide();
    $('#col_local').show();
    $('#secao_distribuir_rateio').show();
    // Reabilita o botão Confirmar Rateio
    $('#btn_confirmar_rateio_final')
        .removeClass('btn-default').addClass('btn-success')
        .text('Confirmar Rateio').prop('disabled', false);
}

function voltarRateioCtr() {
    if ($('#rateio_json').val()) {
        $('#secao_distribuir_rateio').hide();
        $('#col_local').hide();
        $('#rateio_status').show();
    } else {
        $('#habilitar_rateio').prop('checked', false);
        _executarRateioOffCtr();
    }
}

// ================================================================
// Bindings dependentes de DOM/jQuery já carregado — tela de Inclusão
// (todos escopados para não afetar as demais telas que usam este
// mesmo arquivo js/contas_receber.js)
// ================================================================
$(document).ready(function () {

    if (!$('#form_gravar_contas_receber').length || !$('#habilitar_rateio').length) {
        return; // não é a tela de Inclusão — nada a fazer aqui
    }

    // ── Cliente não cadastrado: input digitável embutido no próprio dropdown ──
    (function () {
        var $selCli = $('#codigo_cli_for');
        var suprimirLimpeza = false;

        function getDropdownMenuCli() {
            return $selCli.closest('.bootstrap-select').find('.dropdown-menu').first();
        }

        function atualizarBadgeCli(texto) {
            var $badge = $('#nome_cli_badge');
            texto = (texto || '').trim();
            if (texto !== '') {
                $badge.text('Cliente: ' + texto).show();
            } else {
                $badge.hide();
            }
        }

        function garantirBoxManualCli() {
            var $dropdownMenu = getDropdownMenuCli();
            if (!$dropdownMenu.length) return;

            if (!$dropdownMenu.find('.cliente-manual-box').length) {
                var valorAtual = $('#nome_cli').val() || '';
                var $box = $(
                    '<div class="cliente-manual-box" style="padding:8px 10px;border-bottom:1px solid #e5e5e5;background:#f9f9f9;">' +
                        '<label style="font-weight:600;font-size:12px;margin-bottom:3px;display:block;color:#555;">' +
                            '<i class="fas fa-pen"></i> Para cliente não cadastrado, digite o nome abaixo:' +
                        '</label>' +
                        '<input type="text" class="form-control input-sm" id="nome_cli_inline" placeholder="Digite o nome do cliente..." autocomplete="off">' +
                    '</div>'
                );
                // Prepend no .dropdown-menu (não no .inner) para ficar antes da caixa de busca
                $dropdownMenu.prepend($box);
                $box.find('#nome_cli_inline').val(valorAtual);
            }

            // Foca no campo de nome manual em vez da busca padrão do bootstrap-select
            setTimeout(function () {
                $dropdownMenu.find('#nome_cli_inline').trigger('focus');
            }, 0);
        }

        $selCli.on('shown.bs.select', garantirBoxManualCli);

        // Digitação → grava no hidden #nome_cli e força a seleção do "..."
        $(document).on('input', '#nome_cli_inline', function () {
            var texto = $(this).val();
            $('#nome_cli').val(texto);
            atualizarBadgeCli(texto);
            if ($selCli.val() !== '999999999') {
                suprimirLimpeza = true;
                $selCli.selectpicker('val', '999999999');
                suprimirLimpeza = false;
            }
        });

        // Evita que teclas (setas, Enter, etc.) sejam capturadas pela navegação da lista
        $(document).on('keydown', '#nome_cli_inline', function (e) {
            e.stopPropagation();
            if (e.key === 'Enter') {
                e.preventDefault();
                $selCli.selectpicker('toggle'); // fecha o dropdown
            }
        });

        // Ao escolher um cliente real da lista, limpa o nome digitado manualmente
        $selCli.on('changed.bs.select', function () {
            if (suprimirLimpeza) return;
            if ($selCli.val() !== '999999999') {
                $('#nome_cli').val('');
                $('#nome_cli_inline').val('');
                atualizarBadgeCli('');
            }
        });
    })();

    // Máscara money nos campos de valor do rateio (delegada — funciona em linhas dinâmicas)
    $(document).on('keypress', '.rat-valor', function(e) {
        mask.money.call(this, e);
        if (_modoRateioCtr !== 'valor') _setModoRateioCtr('valor');
    });
    $(document).on('blur', '.rat-valor', function() {
        // Ao sair: converte formato US → BR e recalcula
        var n = ctrParseMoney($(this).val());
        $(this).val(formatMoney(n));
        recalcularRateioCtr();
    });
    // Máscara e handler do campo % — permite dígitos e vírgula
    $(document).on('keypress', '.rat-perc', function(e) {
        var c = e.which;
        if (c === 0 || c === 8) return true;
        if (c === 44) { return $(this).val().replace('%','').indexOf(',') === -1; } // permite 1 vírgula
        if (c < 48 || c > 57) return false;
        if (_modoRateioCtr !== 'perc') _setModoRateioCtr('perc');
        return true;
    });
    $(document).on('blur', '.rat-perc', function() {
        var raw = $(this).val().replace('%','').replace(',','.');
        var n = parseFloat(raw) || 0;
        $(this).val(n > 0 ? n.toFixed(2).replace('.', ',') + '%' : '');
        recalcularRateioCtr();
    });

    // Ao carregar: pré-preenche vencimento conforme modo selecionado
    (function() {
        var modo = $('#sel_modo_parc').val();
        var emissao = $('#data_emissao').val();
        if (modo === 'avista' && emissao) {
            $('#data_vencimento').val(emissao);
        } else if (modo === 'uma_parcela' && emissao) {
            $('#data_vencimento').val(addDias(emissao, 30));
        }
    })();

    $('#habilitar_rateio').on('change', function () {
        var on = $(this).is(':checked');
        var $local = $('#codigo_local');

        if (on) {
            // Valida se o valor foi digitado antes de habilitar o rateio
            var vlrTotal = ctrGetValorTotal();
            if (!vlrTotal || vlrTotal <= 0) {
                $(this).prop('checked', false);
                alert('Digite o Valor da conta antes de habilitar o Rateio.');
                $('#vlr_primeira_parcela').focus();
                return;
            }
            // Rateio ON → oculta Local (label + select), CC e Conta Contábil da linha 2;
            // o select Local é movido para dentro do fieldset "Distribuir Rateio" abaixo
            $('#col_local').hide();
            $('#col_cc').hide();
            $('#col_conta').hide();

            // Restaurar linha de input e limpar fases anteriores
            $('#tr_local_input').show();
            $('#linhas_rateio').hide().empty();
            // Mover select Local para dentro da tabela (coluna Local) e botão para célula ao lado
            $('#td_local_select').append($local);
            $('#td_local_confirm').append($('#col_btn_confirmar_locais button')).children().hide();
            $('#secao_distribuir_rateio').show();

            $local.find('option').prop('selected', false);
            $local.attr('multiple', 'multiple')
                  .attr('data-live-search', 'true')
                  .attr('data-size', '8')
                  .addClass('selectpicker');
            $local.selectpicker({ actionsBox: true, width: '100%', noneSelectedText: '...' });
            $local.val([]);
            $local.selectpicker('refresh');

            var $bs = $local.closest('.bootstrap-select');
            $bs.css('width', '100%');
            $bs.find('.bs-select-all').hide();
            $bs.find('.dropdown-menu').css({ 'min-width': '250px', 'max-width': 'none', 'width': 'auto' });

            // Monitora seleção para mostrar/ocultar coluna do botão Confirmar
            $local.on('changed.bs.select.rateio', function () {
                var selecionados = $local.val();
                if (selecionados && selecionados.length > 0) {
                    $('#btn_confirmar_locais').show();
                } else {
                    $('#btn_confirmar_locais').hide();
                    $('#linhas_rateio').hide().empty();
                    $('#rodape_fase1, #rodape_fase2, #rodape_rateio').remove();
                    $('#tr_local_input').show();
                }
            });

        } else {
            // Se rateio já está configurado, pede confirmação antes de perder os dados
            if ($('#rateio_status').is(':visible') || $('#linhas_rateio').children().length > 0) {
                $(this).prop('checked', true);
                $('#modal_fechar_rateio').modal('show');
                return;
            }
            _executarRateioOffCtr();
        }
    });

    // ── ENTER navega como TAB, apenas dentro do formulário de Inclusão ──
    $(document).on('keydown', 'input, select', function(e) {
        if (e.key !== 'Enter') return;
        if (!$(this).closest('#form_gravar_contas_receber').length) return; // não mexe em outras telas
        e.preventDefault();

        // Dentro da seção de rateio: navega apenas entre os campos do rateio;
        // no último campo aciona o botão Confirmar Rateio.
        if ($(this).closest('#secao_distribuir_rateio').length) {
            var $rateioFields = $('#secao_distribuir_rateio')
                .find('input:not([disabled]):not([readonly]), select:not([disabled])')
                .filter(':visible');
            var rIdx = $rateioFields.index(this);
            if (rIdx >= 0 && rIdx < $rateioFields.length - 1) {
                $rateioFields.eq(rIdx + 1).focus();
            } else {
                var $btnRateio = $('#btn_confirmar_rateio_final');
                if ($btnRateio.length && $btnRateio.is(':visible')) {
                    $btnRateio.focus();
                }
            }
            return;
        }

        var focusable = $('#form_gravar_contas_receber').find('input:not([disabled]):not([readonly]), select:not([disabled]), textarea:not([disabled])').filter(':visible');
        var idx = focusable.index(this);
        if (idx >= 0 && idx < focusable.length - 1) {
            focusable.eq(idx + 1).focus();
        }
    });
});
