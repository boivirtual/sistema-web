/**RELATORIOS FINANCEIROS*/
window.addEventListener("load", function (event) {
    // Exibe filtros quando faz reload
    var filtro_local = $("#exibe_local").val();
 
    if (filtro_local!='' && filtro_local!=null) {
        var filtro_local = filtro_local.split(',');

        $.each(filtro_local, function(idx, val) {
            $('#codigo_fazenda option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_fazenda').selectpicker('refresh');
    }

    var filtro_fornecedor = $("#exibe_fornecedor").val();
 
    if (filtro_fornecedor!='' && filtro_fornecedor!=null) {
        var filtro_fornecedor = filtro_fornecedor.split(',');

        $.each(filtro_fornecedor, function(idx, val) {
            $('#codigo_fornecedor option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_fornecedor').selectpicker('refresh');
    }

    var filtro_cliente = $("#exibe_cliente").val();
 
    if (filtro_cliente!='' && filtro_cliente!=null) {
        var filtro_cliente = filtro_cliente.split(',');

        $.each(filtro_cliente, function(idx, val) {
            $('#codigo_cliente option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_cliente').selectpicker('refresh');
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
    var tipo_conta = $("#tipo_conta").val();
    var limpa_filtro_contas = $("#limpar_filtro_contas").val();

    if (limpa_filtro_contas=='S') {
        $("#contas_selecionadas").val('Todas ou (Clique p/ selecionar contas)');

        $.ajax({
            type: "POST",
            url: "lista_conta_contabil.php",
            data: {
            'tipo_conta': tipo_conta
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
                'tipo_conta': tipo_conta
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
            },
        });
    }

    // Fim exibe filtros 


    var expande_tela = $("#expande_tela").val();

    if (expande_tela == "S") {
        if (jQuery("#sidebar > ul").is(":visible") === true) {
            jQuery("#main-content").css({
                "margin-left": "0px",
            });
            jQuery("#sidebar").css({
                "margin-left": "-180px",
            });
            jQuery("#sidebar > ul").hide();
            jQuery("#container").addClass("sidebar-closed");
        }
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

const idConta = [];

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

$(document).ready(function () {
    $("#tabela_analise_recebimento").DataTable({
        responsive: true,
        paging: false,
        ordering: false,
        info: false,
        language: {
            sSearch: "Buscar na lista:",
            zeroRecords: "Nada encontrado",
            info: "Registros encontrados: _END_ ",
            infoEmpty: "Nenhum registro disponível",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
        },

        dom: "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $("#tabela_analise_pagamento").DataTable({
        responsive: true,
        paging: false,
        ordering: false,
        info: false,
        language: {
            sSearch: "Buscar na lista:",
            zeroRecords: "Nada encontrado",
            info: "Registros encontrados: _END_ ",
            infoEmpty: "Nenhum registro disponível",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
        },

        dom: "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $("#contas_selecionadas").click(() => {
        $("#modal_conta").modal("show");
    });

    var tipo_conta = $("#tipo_conta").val();

    $.ajax({
        type: "POST",
        url: "lista_conta_contabil.php",
        data: {
            'tipo_conta': tipo_conta
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

    console.log(idConta);
    
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

function limpa_contas_selecionadas_ctp() {
    $("#exibe_conta").val('');

    $.ajax({
        type: 'post',
        url: 'gera_secao_limpa_conta.php',
        data: {limpa: "S"},
        success: function(data) {
            var tipo_relatorio = $("#tipo_relatorio").val();
            location.href='form_rel_analise_pagamento.php?tipo='+tipo_relatorio;
        },
    });
}

function limpa_contas_selecionadas_ctr() {
    $("#exibe_conta").val('');

    $.ajax({
        type: 'post',
        url: 'gera_secao_limpa_conta.php',
        data: {limpa: "S"},
        success: function(data) {
            var tipo_relatorio = $("#tipo_relatorio").val();

            location.href='form_rel_analise_recebimento.php?tipo='+tipo_relatorio;
        },
    });
}

function imprimir_fluxo_caixa(opcao) {
    //var tipo_caixa = $("#tipo_caixa").val();
    $("#aguardar").show();

    //if (tipo_caixa == 'D'){
    var ano = $("#ano_diario").val();
    var mes = $("#mes_diario").val();
    var opc_rel = $("#opc_diario").val();
    var forma_pag = $("#forma_pagto_diario").val();
    //}
    //else {
    //    var ano = $("#ano_mensal").val();
    //    var mes = "Todos";
    //    var opc_rel = $("#opc_mensal").val();
    //    var forma_pag = $("#forma_pagto_mensal").val();
    //}

    //if (tipo_caixa == 'D'){
    if (opcao == 1) {
        var width = 350;
        var height = 500;
        var left = 40;
        var top = 40;
        window.open(
            "rel_fluxo_caixa_diario_pdf.php?mes=" +
                mes +
                "&ano=" +
                ano +
                "&opc_rel=" +
                opc_rel +
                "&forma_pag=" +
                forma_pag,
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
            "rel_fluxo_caixa_diario_excel.php?mes=" +
            mes +
            "&ano=" +
            ano +
            "&opc_rel=" +
            opc_rel +
            "&forma_pag=" +
            forma_pag;

        tout = setTimeout("limpar_tela()", 1000);
    }
    //}
    /*else {
        if (opcao==1){
            var width = 350;
            var height = 500;
            var left = 40;
            var top = 40;
            window.open('rel_fluxo_caixa_mensal_pdf.php?mes=' + mes + "&ano=" + ano + "&opc_rel=" + opc_rel + "&forma_pag=" + forma_pag, 'janela', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ', scrollbars=yes, status=yes, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=yes');
                     
            tout = setTimeout('limpar_tela()', 1000);
        }
        else {
            location.href='rel_fluxo_caixa_mensal_excel.php?mes=' + mes + "&ano=" + ano + "&opc_rel=" + opc_rel + "&forma_pag=" + forma_pag;

            tout = setTimeout('limpar_tela()', 1000);
        }

    }*/
}

function listar_fluxo_caixa_tela(opcao) {
    var ano = $("#ano_diario").val();
    var mes = $("#mes_diario").val();
    var tipo_rel = $("#tipo_rel").val();
    var conta_pagamento = $("#conta_pagamento").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_local = $("#codigo_fazenda").val();

    if (codigo_local == null) {
        var array_fazenda = new Array();
    } else {
        var array_fazenda = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_local.length; i++) {
            valor[i] = codigo_local[i];
        }

        var array_fazenda = valor.join(",");
    }

    if (codigo_cc == null) {
        var array_cc = new Array();
    } else {
        var array_cc = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_cc.length; i++) {
            valor[i] = codigo_cc[i];
        }

        var array_cc = valor.join(",");
    }

    if (conta_pagamento == null) {
        var array_conta = new Array();
    } else {
        var array_conta = new Array();
        var valor = new Array();

        for (i = 0; i <= conta_pagamento.length; i++) {
            valor[i] = conta_pagamento[i];
        }
        var array_conta = valor.join(",");
    }

    if (tipo_rel == 2) {
        opc_rel_filtro = "Realizado->";
    } else {
        opc_rel_filtro = "Não Realizado->";
    }

    var options = $("#conta_pagamento option:selected");
    var conta_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#conta_pagamento").text();
        conta_filtro.push(desc.trim());
    });

    if (conta_filtro!='') {
        conta_filtro = "Conta Pag: " + conta_filtro + "->";
    } else {
        conta_filtro = "Conta Pag: Todas";
    }

    var options = $("#codigo_cc option:selected");
    var cc_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_cc").text();
        cc_filtro.push(desc.trim());
    });

    if (cc_filtro != "") {
        cc_filtro = "C.Custo: " + cc_filtro + "->";
    } else {
        cc_filtro = "C.Custo: Todos->";
    }

    periodo = "Período: " + mes + "/" + ano + "->";

    var options = $("#codigo_fazenda option:selected");
    var local_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_fazenda").text();
        local_filtro.push(desc.trim());
    });

    if (local_filtro != "") {
        local_filtro = "Local: " + local_filtro + "->";
    } else {
        local_filtro = "Local: Todos";
    }

    var descricao_filtro =
        local_filtro + opc_rel_filtro + periodo + cc_filtro + conta_filtro;

    $("#aguardar").modal();

    if (opcao=='1') {
        location.href =
            "form_lista_fluxo_caixa_rel.php?descricao_filtro=" +
            descricao_filtro +
            "&tipo_rel=" +
            tipo_rel +
            "&ano=" +
            ano +
            "&mes=" +
            mes +
            "&conta_pagamento=" +
            array_conta +
            "&c_custo=" +
            array_cc +
            "&fazenda=" +
            array_fazenda;
    }
    else {
        location.href =
            "rel_fluxo_caixa_diario_excel.php?descricao_filtro=" +
            descricao_filtro +
            "&tipo_rel=" +
            tipo_rel +
            "&ano=" +
            ano +
            "&mes=" +
            mes +
            "&conta_pagamento=" +
            array_conta +
            "&c_custo=" +
            array_cc +
            "&fazenda=" +
            array_fazenda;

        tout = setTimeout("limpar_tela()", 5000);
    }
}

function listar_fluxo_caixa_excel() {
    var ano = $("#ano_diario").val();
    var mes = $("#mes_diario").val();
    var tipo_rel = $("#tipo_rel").val();
    var conta_pagamento = $("#conta_pagamento").val();
    var codigo_cc = $("#codigo_cc").val();
    var descricao_filtro = $("#descricao_filtro").val();
    var codigo_local = $("#codigo_fazenda").val();

    $("#aguardar").modal();

    location.href =
        "rel_fluxo_caixa_diario_excel.php?descricao_filtro=" +
        descricao_filtro +
        "&tipo_rel=" +
        tipo_rel +
        "&ano=" +
        ano +
        "&mes=" +
        mes +
        "&conta_pagamento=" +
        conta_pagamento +
        "&c_custo=" +
        codigo_cc +
        "&fazenda=" +
        codigo_local;

    tout = setTimeout("limpar_tela()", 5000);
}

function listar_contas_receber_tela(opcao) {
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_cliente = $("#codigo_cliente").val();
    var tipo_data = $("input[name='tipo_data']:checked").val();
    var tipo_rel = $("input[name='tipo_rel']:checked").val();
    var codigo_local = $("#codigo_fazenda").val();

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
        conta_filtro = "Conta: Todas->";
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

    conta_filtro = "Conta: " + conta_filtro + '->';

    if (data_inicial == 0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Informe a data inicial do período."
        );
        return;
    }

    if (data_inicial != 0) {
        if (data_final == 0 || data_final < data_inicial) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Informe a data final do período corretamente."
            );
            return;
        }
    }

    if (codigo_cc == null) {
        var array_cc = new Array();
    } else {
        var array_cc = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_cc.length; i++) {
            valor[i] = codigo_cc[i];
        }

        var array_cc = valor.join(",");
    }

    if (codigo_cliente == null) {
        var array_cliente = new Array();
    } else {
        var array_cliente = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_cliente.length; i++) {
            valor[i] = codigo_cliente[i];
        }

        var array_cliente = valor.join(",");
    }

    if (codigo_local == null) {
        var array_fazenda = new Array();
    } else {
        var array_fazenda = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_local.length; i++) {
            valor[i] = codigo_local[i];
        }
        var array_fazenda = valor.join(",");
    }

    if (tipo_rel == "A") {
        opc_rel_filtro = "Analítico->";
    } else {
        opc_rel_filtro = "Sintético->";
    }

    if (tipo_data == "V") {
        opc_data_filtro = "Dt Vencimento->";
    } else if (tipo_data == "E") {
        opc_data_filtro = "Dt Emissão->";
    } else {
        opc_data_filtro = "Dt Recemimento->";
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
        codigo_local_filtro = "";
    }

    var options = $("#codigo_cliente option:selected");
    var cliente_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_cliente").text();
        cliente_filtro.push(desc.trim());
    });

    if (cliente_filtro != "") {
        cliente_filtro = "Cliente:" + cliente_filtro + "->";
    } else {
        cliente_filtro = "Cliente:Todos->";
    }

    var options = $("#codigo_cc option:selected");
    var cc_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_cc").text();
        cc_filtro.push(desc.trim());
    });

    if (cc_filtro != "") {
        cc_filtro = "C.Custos:" + cc_filtro;
    } else {
        cc_filtro = "C.Custos:Todos";
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

    var descricao_filtro =
        codigo_local_filtro +
        periodo +
        opc_data_filtro +
        opc_rel_filtro +
        cliente_filtro +
        conta_filtro +
        cc_filtro;

    var tipo_relatorio = $("#tipo_relatorio").val();

    $("#aguardar").modal();

    if (opcao=='1') {
        location.href =
            "form_lista_analise_recebimento_rel.php?descricao_filtro=" +
            descricao_filtro +
            "&tipo_rel=" + tipo_rel +
            "&tipo_data=" + tipo_data +
            "&data_inicial=" + data_inicial +
            "&data_final=" + data_final +
            "&array_fazenda=" + array_fazenda +
            "&cliente=" + array_cliente +
            "&conta=" + array_conta +
            "&c_custo=" + array_cc + 
            "&tipo=" + tipo_relatorio;
    }
    else {
        location.href =
            "rel_analise_recebimentos_excel.php?descricao_filtro=" +
            descricao_filtro +
            "&tipo_rel=" + tipo_rel +
            "&tipo_data=" + tipo_data +
            "&data_inicial=" + data_inicial +
            "&data_final=" + data_final +
            "&array_fazenda=" + array_fazenda +
            "&cliente=" + array_cliente +
            "&conta=" + array_conta +
            "&c_custo=" + array_cc + 
            "&tipo=" + tipo_relatorio;

        tout = setTimeout("limpar_tela()", 5000);
    }
}

function listar_contas_receber_excel() {
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var tipo_rel = $("#tipo_rel").val();
    var tipo_data = $("#tipo_data").val();
    var descricao_filtro = $("#descricao_filtro").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_conta = $("#codigo_conta").val();
    var codigo_cliente = $("#codigo_cliente").val();
    var codigo_local = $("#codigo_fazenda").val();

    $("#aguardar").modal();

    location.href =
        "rel_analise_recebimentos_excel.php?descricao_filtro=" +
        descricao_filtro +
        "&tipo_rel=" +
        tipo_rel +
        "&tipo_data=" +
        tipo_data +
        "&fazendas=" +
        codigo_local + 
        "&c_custo=" +
        codigo_cc +
        "&conta=" +
        codigo_conta +
        "&cliente=" +
        codigo_cliente +
        "&data_inicial=" +
        data_inicial +
        "&data_final=" +
        data_final;

    tout = setTimeout("limpar_tela()", 5000);
}

function listar_contas_pagar_tela(opcao) {
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var codigo_conta = $("#codigo_conta").val();
    var codigo_local = $("#codigo_fazenda").val();
    var codigo_fornecedor = $("#codigo_fornecedor").val();
    var codigo_cc = $("#codigo_cc").val();
    var tipo_data = $("input[name='tipo_data']:checked").val();
    var tipo_rel = $("input[name='tipo_rel']:checked").val();

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

    if (data_inicial == 0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Informe a data inicial do período."
        );
        return;
    }

    if (data_inicial != 0) {
        if (data_final == 0 || data_final < data_inicial) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Informe a data final do período corretamente."
            );
            return;
        }
    }

    if (codigo_local == null) {
        var array_fazenda = new Array();
    } else {
        var array_fazenda = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_local.length; i++) {
            valor[i] = codigo_local[i];
        }

        var array_fazenda = valor.join(",");
    }

    if (codigo_cc == null) {
        var array_cc = new Array();
    } else {
        var array_cc = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_cc.length; i++) {
            valor[i] = codigo_cc[i];
        }

        var array_cc = valor.join(",");
    }

    if (codigo_fornecedor == null) {
        var array_fornecedor = new Array();
    } else {
        var array_fornecedor = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_fornecedor.length; i++) {
            valor[i] = codigo_fornecedor[i];
        }

        var array_fornecedor = valor.join(",");
    }

    if (tipo_rel == "A") {
        opc_rel_filtro = "Analítico->";
    } else {
        opc_rel_filtro = "Sintético->";
    }

    if (tipo_data == "V") {
        opc_data_filtro = "Dt Vencimento->";
    } else if (tipo_data == "E") {
        opc_data_filtro = "Dt Emissão->";
    } else {
        opc_data_filtro = "Dt Recemimento->";
    }

    var options = $("#codigo_fornecedor option:selected");
    var fornecedor_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_fornecedor").text();
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
        codigo_local_filtro = "Local: Todas->";
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

    var descricao_filtro =
        codigo_local_filtro +
        codigo_cc_filtro +
        periodo +
        opc_data_filtro +
        opc_rel_filtro +
        fornecedor_filtro +
        conta_filtro;

    var tipo_relatorio = $("#tipo_relatorio").val();

    $("#aguardar").modal();

    if (opcao=='1') {

        if (tipo_data=='P') {
            location.href =
            "form_lista_analise_pagamento_pagos_rel.php?descricao_filtro=" +
            descricao_filtro +
            "&tipo_rel=" +
            tipo_rel +
            "&tipo_data=" +
            tipo_data +
            "&data_inicial=" +
            data_inicial +
            "&data_final=" +
            data_final +
            "&fornecedor=" +
            array_fornecedor +
            "&conta=" +
            array_conta +
            "&fazendas=" +
            array_fazenda +
            "&codigo_cc=" +
            array_cc + 
            "&tipo=" + tipo_relatorio;
        }
        else {
            location.href =
            "form_lista_analise_pagamento_rel.php?descricao_filtro=" +
            descricao_filtro +
            "&tipo_rel=" +
            tipo_rel +
            "&tipo_data=" +
            tipo_data +
            "&data_inicial=" +
            data_inicial +
            "&data_final=" +
            data_final +
            "&fornecedor=" +
            array_fornecedor +
            "&conta=" +
            array_conta +
            "&fazendas=" +
            array_fazenda +
            "&codigo_cc=" +
            array_cc + 
            "&tipo=" + tipo_relatorio;
        }
    }
    else {
        if (tipo_data=='P') {
            location.href =
                "rel_analise_pagamentos_pagos_excel.php?descricao_filtro=" +
                descricao_filtro +
                "&tipo_rel=" +
                tipo_rel +
                "&tipo_data=" +
                tipo_data +
                "&data_inicial=" +
                data_inicial +
                "&data_final=" +
                data_final +
                "&fornecedor=" +
                array_fornecedor +
                "&conta=" +
                array_conta +
                "&fazendas=" +
                array_fazenda +
                "&codigo_cc=" +
                array_cc + 
                "&tipo=" + tipo_relatorio;
        }
        else {
            location.href =
                "rel_analise_pagamentos_excel.php?descricao_filtro=" +
                descricao_filtro +
                "&tipo_rel=" +
                tipo_rel +
                "&tipo_data=" +
                tipo_data +
                "&data_inicial=" +
                data_inicial +
                "&data_final=" +
                data_final +
                "&fornecedor=" +
                array_fornecedor +
                "&conta=" +
                array_conta +
                "&fazendas=" +
                array_fazenda +
                "&codigo_cc=" +
                array_cc + 
                "&tipo=" + tipo_relatorio;
        }
    
        tout = setTimeout("limpar_tela()", 5000);
    }
}

function listar_contas_pagar_excel() {
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var tipo_rel = $("#tipo_rel").val();
    var tipo_data = $("#tipo_data").val();
    var descricao_filtro = $("#descricao_filtro").val();
    var codigo_local = $("#codigo_fazenda").val();
    var codigo_conta = $("#codigo_conta").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_fornecedor = $("#codigo_fornecedor").val();

    $("#aguardar").modal();

    if (tipo_data=='P') {
        location.href =
            "rel_analise_pagamentos_pagos_excel.php?descricao_filtro=" +
            descricao_filtro +
            "&tipo_rel=" +
            tipo_rel +
            "&tipo_data=" +
            tipo_data +
            "&fazendas=" +
            codigo_local +
            "&conta=" +
            codigo_conta +
            "&fornecedor=" +
            codigo_fornecedor +
            "&data_inicial=" +
            data_inicial +
            "&data_final=" +
            data_final +
            "&codigo_cc=" +
            codigo_cc;
    }
    else {
        location.href =
            "rel_analise_pagamentos_excel.php?descricao_filtro=" +
            descricao_filtro +
            "&tipo_rel=" +
            tipo_rel +
            "&tipo_data=" +
            tipo_data +
            "&fazendas=" +
            codigo_local +
            "&conta=" +
            codigo_conta +
            "&fornecedor=" +
            codigo_fornecedor +
            "&data_inicial=" +
            data_inicial +
            "&data_final=" +
            data_final +
            "&codigo_cc=" +
            codigo_cc;
    }

    tout = setTimeout("limpar_tela()", 5000);
}

function listar_previsto_realizado_tela(opcao) {
    var ano = $("#ano_mensal").val();
    var tipo_rel = $("#tipo_rel").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_local = $("#codigo_fazenda").val();

    if (codigo_local == null) {
        var array_fazenda = new Array();
    } else {
        var array_fazenda = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_local.length; i++) {
            valor[i] = codigo_local[i];
        }

        var array_fazenda = valor.join(",");
    }

    if (codigo_cc == null) {
        var array_codigo_cc = new Array();
    } else {
        var array_codigo_cc = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_cc.length; i++) {
            valor[i] = codigo_cc[i];
        }

        var array_codigo_cc = valor.join(",");
    }

    var options = $("#codigo_cc option:selected");
    var codigo_cc_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_cc").text();
        codigo_cc_filtro.push(desc.trim());
    });

    if (codigo_cc_filtro != "") {
        codigo_cc_filtro = codigo_cc_filtro + "->";
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

    var options = $("#tipo_rel option:selected");
    var tipo_rel_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#tipo_rel").text();
        tipo_rel_filtro.push(desc.trim());
    });

    if (tipo_rel_filtro != "") {
        tipo_rel_filtro = tipo_rel_filtro;
    }

    var descricao_filtro =
        codigo_local_filtro +
        "Ano: " +
        ano +
        "->" +
        codigo_cc_filtro +
        tipo_rel_filtro;

    $("#aguardar").modal();

    if (opcao=='1') {
        location.href =
            "form_lista_analise_previsto_realizado_rel.php?descricao_filtro=" +
            descricao_filtro +
            "&tipo_rel=" +
            tipo_rel +
            "&codigo_cc=" +
            array_codigo_cc +
            "&fazendas=" +
            array_fazenda +
            "&ano=" +
            ano;
    }
    else {
        location.href =
            "rel_lista_contas_previsto_realizado_excel.php?descricao_filtro=" +
            descricao_filtro +
            "&tipo_rel=" +
            tipo_rel +
            "&codigo_cc=" +
            array_codigo_cc +
            "&fazendas=" +
            array_fazenda +
            "&ano=" +
            ano;
        tout = setTimeout("limpar_tela()", 5000);
    }
}

function listar_previsao_excel() {
    var ano = $("#ano_mensal").val();
    var tipo_rel = $("#tipo_rel").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_local = $("#codigo_fazenda").val();
    var descricao_filtro = $("#descricao_filtro").val();

    $("#aguardar").modal();

    location.href =
        "rel_lista_contas_previsto_realizado_excel.php?descricao_filtro=" +
        descricao_filtro +
        "&tipo_rel=" +
        tipo_rel +
        "&fazendas=" +
        codigo_local +
        "&codigo_cc=" +
        codigo_cc +
        "&ano=" +
        ano;

    tout = setTimeout("limpar_tela()", 5000);
}

function limpar_tela() {
    $("#aguardar").modal("hide");
}

function voltar_filtro_caixa() {
    location.href = "form_rel_fluxo_caixa.php";
}

function voltar_filtro() {
    location.href = "form_rel_analise_recebimento.php";
}

function voltar_filtro_receber() {
    var tipo_relatorio = $("#tipo_relatorio").val();

    location.href = 'form_rel_analise_recebimento.php?tipo='+tipo_relatorio;
}

function voltar_filtro_pagar() {
    var tipo_relatorio = $("#tipo_relatorio").val();

    location.href='form_rel_analise_pagamento.php?tipo='+tipo_relatorio;
}

function voltar_filtro_previsao() {
    location.href = "form_rel_analise_previsto_realizado.php";
}

function voltar_relatorios() {
    location.href = "form_relatorios_financeiros.php";
}

function voltar_relatorios_pagar() {
    var tipo_relatorio = $("#tipo_relatorio").val();

    if (tipo_relatorio==1) {
        location.href='form_relatorios_financeiros.php';
    }
    else {
        location.href='form_contas_pagar.php';
    }
}

function voltar_relatorios_receber() {
    var tipo_relatorio = $("#tipo_relatorio").val();

    if (tipo_relatorio==1) {
        location.href='form_relatorios_financeiros.php';
    }
    else {
        location.href='form_contas_receber.php';
    }
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
