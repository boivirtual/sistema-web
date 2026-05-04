/**TABELA PREVISAO CONTAS*/
const idConta = [];
const selectedConta = [];

window.addEventListener("load", function(event) {
    // Exibe filtros quando faz reload
    var filtro_local = $("#exibe_local").val();
 
    if (filtro_local!='' && filtro_local!=null) {
        var filtro_local = filtro_local.split(',');

        $.each(filtro_local, function(idx, val) {
            $('#codigo_local_filtro option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_local_filtro').selectpicker('refresh');
    }

    var filtro_conta = $("#exibe_conta").val();
    var limpa_filtro_contas = $("#limpar_filtro_contas").val();

    if (limpa_filtro_contas=='S') {
        $("#contas_selecionadas").val('Todas ou (Clique p/ selecionar contas)');

        $.ajax({
            type: "POST",
            url: "lista_conta_contabil.php",
            data: {
            'tipo_conta': 'T'
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
            'tipo_conta': 'T'
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

                var lista_previsao_automatico = $("#lista_previsao_automatico").val();

                if (lista_previsao_automatico=="S") {
                    consultar();
                }
            },
        });
    }
    else {
        var lista_previsao_automatico = $("#lista_previsao_automatico").val();

        if (lista_previsao_automatico=="S") {
            consultar();
        }
    }

    // Fim exibe filtros 
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

function listar_contas(){

 //   $('.filtro_escondido').show();
    $('.filtro_exibido').hide();

    var ano = $("#ano_mensal").val();
    var opc_rel = $("#opc_mensal").val();
    var forma_pag = $("#forma_pagto_mensal").val();
    var local = $("#codigo_local_filtro").val();

    if (forma_pag==null) {
        forma_pag=[''];
    }

    if (local==null) {
        local=[''];
    }

    var options = $('#forma_pagto_mensal option:selected');
    var forma_pag_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#forma_pagto_mensal').text();
        forma_pag_filtro.push( desc.trim() );
    });

    if (forma_pag_filtro!=''){
            forma_pag_filtro = '->' + forma_pag_filtro;
    }

    var options = $('#codigo_local_filtro option:selected');
    var local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_local_filtro').text();
        local_filtro.push( desc.trim() );
    });

    if (local_filtro!=''){
            local_filtro = '->' + local_filtro;
    }

    var options = $('#opc_mensal option:selected');
    var opc_rel_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_pai_filtro').text();
        opc_rel_filtro.push( desc.trim() );
    });

    var descricao_filtro = 'Filtros: '+ano+'->'+opc_rel_filtro+local_filtro+forma_pag_filtro;

    if (jQuery('#sidebar > ul').is(":visible") === true) {
        jQuery('#main-content').css({
           'margin-left': '0px'
        });
        jQuery('#sidebar').css({
           'margin-left': '-180px'
        });
        jQuery('#sidebar > ul').hide();
        jQuery("#container").addClass("sidebar-closed");
    }

    $('.filtro_exibido').hide();
    $("#aguardar").modal();

    $.post("form_lista_contas_previsto_realizado.php", {opc_rel:opc_rel, ano:ano, forma_pag:forma_pag, local:local},
        function(valor){ 
            $("div[id=lista_analise_contas]").html(valor); 
            $("#descricao_filtro").text(descricao_filtro);
            $('.esconder').hide();
            $('#aguardar').modal('hide');
    });
}

$(document).ready(function(){
    $('#tabela_previsao_conta').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
        "sSearch": "Busca:",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        "decimal": ",",
        "thousands": ".",
        }
    });
});


$(document).ready(function(){
});

$(document).ready(function(){
    $('#ano_filtro').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#codigo_conta_contabil').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#codigo_local_filtro').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });
    // Fim acendo botão 
});

$(document).ready(function(){
    $('#conta_contabil_id').change(function(){

        var codigo_conta = $("#conta_contabil_id").val();
        $.post("ler_plano_contas.php",{id: codigo_conta}, function(valor){
            var php = valor.split("<|>");

            if (php[0]==2){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Selecione uma Conta Analítica');
                $("#conta_contabil_id").val('0000000');
                return;
            }
        });
    });
});

$(document).ready(function(){
    $("#contas_selecionadas").click(() => {
        $("#modal_conta").modal("show");
        $('.consultar').show();
        $('.filtros').hide();
    });

    $.ajax({
        type: "POST",
        url: "lista_conta_contabil.php",
        data: {
            'tipo_conta': 'T'
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
            location.href='form_previsao_contas.php';
        },
    });
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

function exibir_filtro(){
    $('.filtro_exibido').show();
    $('.esconder').show();
    $('.exibir').hide();
    $('.voltar').hide();
}

function esconder_filtro(){
    $('.filtro_exibido').hide();
    $('.esconder').hide();
    $('.exibir').show();
    $('.voltar').show();
}

function filtros() {
//    $("#codigo_alfa_filtro").val('');
//    $("#codigo_number_filtro").val('');
//    $("#codigo_raca_filtro").val('');

    $('#modal_filtros').modal('show');
}

function voltar_inclusao(){
    location.href='form_previsao_contas.php';
}

function voltar_relatorios(){
    location.href='form_relatorios_financeiros.php'
}


function consultar() {
    var mes = $("#mes_filtro").val();
    var ano = $("#ano_filtro").val();
    var codigo_local = $("#codigo_local_filtro").val();

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

    if (codigo_local==null) {
        var array_fazenda= new Array();
    }
    else {
        var array_fazenda = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_local.length; i++) {
            valor[i]=codigo_local[i];
        }

        var array_fazenda=valor.join(",");
    }

    var options = $("#codigo_local_filtro option:selected");
    var codigo_local_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_local_filtro").text();
        codigo_local_filtro.push(desc.trim());
    });

    if (codigo_local_filtro != "") {
        codigo_local_filtro = "Local: " + codigo_local_filtro + "->";
    } else {
        codigo_local_filtro = "Local: Todos->";
    }

    var descricao_filtro =
        codigo_local_filtro +
        'Ano: ' + ano + '->' +
        conta_filtro;

    $(".digitar_filtros").hide();
    $(".filtros").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".descricao_filtro").html(descricao_filtro);

    $("#exibe_conta").val(array_conta);

    $("#aguardar").modal("show");

    $('#lista_contas').load('form_lista_previsao_contas.php?ano=' + ano +
     '&array_conta=' + array_conta  + 
     '&array_fazenda=' + array_fazenda);
    return;

	/*$.post("form_lista_previsao_contas.php", {codigo_conta:array_conta, mes:mes, ano:ano, codigo_local:array_fazenda},
  		function(valor){ $("div[id=lista_contas]").html(valor); 
    });

    return;*/
}

function incluir_nova() {

    $("#codigo_conta").val('000000000');
    $("#conta_contabil_id").val('0000000');
    $("#codigo_local").val('000000000');
    $("#valor_previsto_jan").val('');
    $("#valor_previsto_fev").val('');
    $("#valor_previsto_mar").val('');
    $("#valor_previsto_abr").val('');
    $("#valor_previsto_mai").val('');
    $("#valor_previsto_jun").val('');
    $("#valor_previsto_jul").val('');
    $("#valor_previsto_ago").val('');
    $("#valor_previsto_set").val('');
    $("#valor_previsto_out").val('');
    $("#valor_previsto_nov").val('');
    $("#valor_previsto_dez").val('');
    $("#tipo_gravacao").val(0);
    $("#informacao").hide();    

    $('#modal_incluir .modal-title').html('Previsão de Contas - Incluir');
    $('.confirma_gravar').html('Confirmar Inclusão').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    $('.voltar_inclusao').show();
    $('.voltar').hide();
    $('#modal_incluir').modal('show');
}

function editar_conta(array_registro) {
    $array_conta = array_registro.split('|');
    $("#codigo_conta").val($array_conta[0]);
    $("#conta_contabil_id").val($array_conta[1]);
    $("#codigo_local").val($array_conta[19]);
    $("#ano_conta").val($array_conta[2]);
    $("#valor_previsto_jan").val(formatMoney($array_conta[3]));
    $("#valor_previsto_fev").val(formatMoney($array_conta[4]));
    $("#valor_previsto_mar").val(formatMoney($array_conta[5]));
    $("#valor_previsto_abr").val(formatMoney($array_conta[6]));
    $("#valor_previsto_mai").val(formatMoney($array_conta[7]));
    $("#valor_previsto_jun").val(formatMoney($array_conta[8]));
    $("#valor_previsto_jul").val(formatMoney($array_conta[9]));
    $("#valor_previsto_ago").val(formatMoney($array_conta[10]));
    $("#valor_previsto_set").val(formatMoney($array_conta[11]));
    $("#valor_previsto_out").val(formatMoney($array_conta[12]));
    $("#valor_previsto_nov").val(formatMoney($array_conta[13]));
    $("#valor_previsto_dez").val(formatMoney($array_conta[14]));
    $("#incluido_em").text($array_conta[15]);
    $("#incluido_por").text($array_conta[16]);
    $("#alterado_em").text($array_conta[17]);
    $("#alterado_por").text($array_conta[18]);

    $("#tipo_gravacao").val(1);

    $('#modal_incluir .modal-title').html('Previsão de Contas - Editar');
    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    $('.voltar_inclusao').hide();
    $('.voltar').show();
    $("#informacao").show();    

    if ($array_conta[18]=='') {
        $("#registro_alterado").hide();
        $("#alterado_por").hide();
        $("#alterado_em").hide();
    }
    else {
        $("#registro_alterado").show();        
        $("#alterado_por").show();
        $("#alterado_em").show();
    }

    $('#modal_incluir').modal('show');

}

function enviar_lixeira(array_registro, opcao) {
    $array_conta = array_registro.split('|');
    $("#codigo_conta").val($array_conta[0]);
    $("#conta_contabil_id").val($array_conta[1]);
    $("#codigo_local").val($array_conta[19]);
    $("#ano_conta").val($array_conta[2]);
    $("#valor_previsto_jan").val(formatMoney($array_conta[3]));
    $("#valor_previsto_fev").val(formatMoney($array_conta[4]));
    $("#valor_previsto_mar").val(formatMoney($array_conta[5]));
    $("#valor_previsto_abr").val(formatMoney($array_conta[6]));
    $("#valor_previsto_mai").val(formatMoney($array_conta[7]));
    $("#valor_previsto_jun").val(formatMoney($array_conta[8]));
    $("#valor_previsto_jul").val(formatMoney($array_conta[9]));
    $("#valor_previsto_ago").val(formatMoney($array_conta[10]));
    $("#valor_previsto_set").val(formatMoney($array_conta[11]));
    $("#valor_previsto_out").val(formatMoney($array_conta[12]));
    $("#valor_previsto_nov").val(formatMoney($array_conta[13]));
    $("#valor_previsto_dez").val(formatMoney($array_conta[14]));
    $("#incluido_em").text($array_conta[15]);
    $("#incluido_por").text($array_conta[16]);
    $("#alterado_em").text($array_conta[17]);
    $("#alterado_por").text($array_conta[18]);

    if ($array_conta[18]=='') {
        $("#registro_alterado").hide();
        $("#alterado_por").hide();
        $("#alterado_em").hide();
    }
    else {
        $("#registro_alterado").show();        
        $("#alterado_por").show();
        $("#alterado_em").show();
    }

    $("#tipo_gravacao").val(opcao);

    if (opcao==2) {
        $('#modal_incluir .modal-title').html('Precisão de Contas - Enviar para Lixeira');
        $(".confirma_gravar").html('Enviar para Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }
    else {
        $('#modal_incluir .modal-title').html('Precisão de Contas - Remover da Lixeira');
        $(".confirma_gravar").html('Remover da Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }

    $('.confirma_gravar').show();
    $('.voltar_inclusao').hide();
    $('.voltar').show();
    $('#modal_incluir').modal('show');
}

function gravar_conta() {
    var tipo_gravacao = $("#tipo_gravacao").val();
    if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_conta').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_previsao_contas.php',
                data: dados,
                success: function(data){
                    if (data.error) {
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html(data.message);
                    }
                    else if (data.success){
                        $("#mensagem_retorno").modal();
                        $("#mensagem_retorno .modal-body").html(data.message);
                    }
                }
            });
        }
    }
    else if (tipo_gravacao==3) {
        if (window.confirm("Confirma remover esse registro da lixeira?")) {
            var dados = $('#form_gravar_conta').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_previsao_contas.php',
                data: dados,
                success: function(data){
                    if (data.error) {
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html(data.message);
                    }
                    else if (data.success){
                        $("#mensagem_retorno").modal();
                        $("#mensagem_retorno .modal-body").html(data.message);
                    }
                }
            });
        }
    }
    else if (tipo_gravacao==1){
        var dados = $('#form_gravar_conta').serialize();
        $.ajax({
            type: "POST",
            url: 'gravar_previsao_contas.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }
        });
    }
    else {
        var dados = $('#form_gravar_conta').serialize();

        $(".confirma_gravar").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: 'gravar_previsao_contas.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    $(".confirma_gravar").attr("disabled", false);
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    $(".confirma_gravar").attr("disabled", false);
                    $("#mensagem_retorno_inclusao").modal();
                    $("#mensagem_retorno_inclusao .modal-body").html(data.message);
                }
            }
        });
    }
}

function digita_valor(){
    $('#valor_previsto_jan').bind('keypress',mask.money);
    $('#valor_previsto_fev').bind('keypress',mask.money);
    $('#valor_previsto_mar').bind('keypress',mask.money);
    $('#valor_previsto_abr').bind('keypress',mask.money);
    $('#valor_previsto_mai').bind('keypress',mask.money);
    $('#valor_previsto_jun').bind('keypress',mask.money);
    $('#valor_previsto_jul').bind('keypress',mask.money);
    $('#valor_previsto_ago').bind('keypress',mask.money);
    $('#valor_previsto_set').bind('keypress',mask.money);
    $('#valor_previsto_out').bind('keypress',mask.money);
    $('#valor_previsto_nov').bind('keypress',mask.money);
    $('#valor_previsto_dez').bind('keypress',mask.money);
}

function exibe_valor_previsto_jan(){
    var valor_previsto = $("#valor_previsto_jan").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_jan").val(formatMoney(valor_previsto));
}

function exibe_valor_previsto_fev(){
    var valor_previsto = $("#valor_previsto_fev").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_fev").val(formatMoney(valor_previsto));
}
function exibe_valor_previsto_mar(){
    var valor_previsto = $("#valor_previsto_mar").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_mar").val(formatMoney(valor_previsto));
}
function exibe_valor_previsto_abr(){
    var valor_previsto = $("#valor_previsto_abr").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_abr").val(formatMoney(valor_previsto));
}
function exibe_valor_previsto_mai(){
    var valor_previsto = $("#valor_previsto_mai").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_mai").val(formatMoney(valor_previsto));
}
function exibe_valor_previsto_jun(){
    var valor_previsto = $("#valor_previsto_jun").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_jun").val(formatMoney(valor_previsto));
}
function exibe_valor_previsto_jul(){
    var valor_previsto = $("#valor_previsto_jul").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_jul").val(formatMoney(valor_previsto));
}
function exibe_valor_previsto_ago(){
    var valor_previsto = $("#valor_previsto_ago").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_ago").val(formatMoney(valor_previsto));
}
function exibe_valor_previsto_set(){
    var valor_previsto = $("#valor_previsto_set").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_set").val(formatMoney(valor_previsto));
}
function exibe_valor_previsto_out(){
    var valor_previsto = $("#valor_previsto_out").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_out").val(formatMoney(valor_previsto));
}
function exibe_valor_previsto_nov(){
    var valor_previsto = $("#valor_previsto_nov").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_nov").val(formatMoney(valor_previsto));
}
function exibe_valor_previsto_dez(){
    var valor_previsto = $("#valor_previsto_dez").val();
    if (verifica_virgula(valor_previsto)==',') {
        valor_previsto = replace_valor(valor_previsto);
    }

    $("#valor_previsto_dez").val(formatMoney(valor_previsto));
}

function imprimir_analize_contas() {
    var ano = $("#ano_mensal").val();
    var opc_rel = $("#opc_mensal").val();
    var conta_pag = $("#forma_pagto_mensal").val();
    var local = $("#codigo_local_filtro").val();

    if (local==null) {
        var array_local = new Array();
    }
    else {
        var array_local = new Array();
        var valor = new Array();

        for (i = 0; i <= local.length; i++) {
            valor[i]=local[i];
        }

        var array_local=valor.join(",");
    }

    if (conta_pag==null) {
        var array_conta= new Array();
    }
    else {
        var array_conta = new Array();
        var valor = new Array();

        for (i = 0; i <= conta_pag.length; i++) {
            valor[i]=conta_pag[i];
        }

        var array_conta=valor.join(",");
    }

    location.href='rel_lista_contas_previsto_realizado_excel.php?ano=' + ano + '&opc_rel=' + opc_rel + '&conta_pag=' + array_conta + '&local=' + array_local;
}

var mask = {
     money: function() {
        var el = this
        ,exec = function(v) {
        v = v.replace(/\D/g,"");
        v = new String(Number(v));
        var len = v.length;
        if (1== len)
        v = v.replace(/(\d)/,"0.0$1");
        else if (2 == len)
        v = v.replace(/(\d)/,"0.$1");
        else if (len > 2) {
        v = v.replace(/(\d{2})$/,'.$1');
        }
        return v;
        };

        setTimeout(function(){
        el.value = exec(el.value);
        },1);
     }
}

var mask2 = {
     money: function() {
        var el = this
        ,exec = function(v) {
        v = v.replace(/\D/g,"");
        v = new String(Number(v));
        var len = v.length;
        if (1== len)
        v = v.replace(/(\d)/,"0.0$1");
        else if (2 == len)
        v = v.replace(/(\d)/,"0.$1");
        else if (3 == len)
        v = v.replace(/(\d)/,"0.$1");
        else if (len > 3) {
        v = v.replace(/(\d{3})$/,'.$1');
        }
        return v;
        };

        setTimeout(function(){
        el.value = exec(el.value);
        },1);
     }
}

function formatMoney(n, c, d, t) {
  c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
  return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

function formatMoney2(n, c, d, t) {
  c = isNaN(c = Math.abs(c)) ? 3 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
  return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

function replace_valor(valor_replace){
    valor_replace = valor_replace.replace(".","");
    valor_replace = valor_replace.replace(".","");
    valor_replace = valor_replace.replace(".","");
    valor_replace =valor_replace.replace(",",".");
    return valor_replace;
}

function verifica_virgula(vlr){
    var virgula = '';

    for (i=0; i<vlr.length; i++) {
        if (vlr.charAt(i) ==',') {
            virgula = ',';
        }
    }   
    return virgula;
}

function limpar_tela(){
    $('#aguarde').hide();
}
