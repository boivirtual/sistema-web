/**TABELA PREVISAO CONTAS*/
window.addEventListener("load", function(event) {
	consultar();

    // lista lista analise de contas previsto/realizado ao entrar no programa
    listar_contas();
    // Fim lista contas

});        

function listar_contas(){
    $('#aguardar').show();

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

    $.post("form_lista_contas_previsto_realizado.php", {opc_rel:opc_rel, ano:ano, forma_pag:forma_pag, local:local},
        function(valor){ $("div[id=lista_analise_contas]").html(valor); 
    });

    tout = setTimeout('limpar_tela()', 1000);
}

   // LER ITENS pesados NA CONSULTA
    var numero_doc=$("#num_pedido").text();

    $.post("ler_itens_pesagem_animais.php", {numero_pedido: numero_doc}, function (dados_retorno){

        if (dados_retorno!=0) {
            var txt = dados_retorno;
            var php = txt.split("<|>");

            var numero_itens = php.length;
     
            html = "";
            html += '<table class="table table-striped table-advance table-hover" id="tabela_itens_consulta" width="100%">';
            html += '<thead>';
            html += '<tr>';
            html += '<th>' + ' Código' + '</th>';
            html += '<th>' + ' Descricao' + '</th>';
            html += '<th>' + ' Unidade' + '</th>';
            html += '<th>' + ' Quantidade' + '</th>';
            html += '<th>' + ' Valor Unitário' + '</th>';
            html += '<th>' + ' % Desconto' + '</th>';
            html += '<th>' + ' Valor Desconto' + '</th>';
            html += '<th>' + ' Valor Total' + '</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            var total_pedido = 0.00;

            for (var i = 0; i < numero_itens; i++) {
                var itens = php[i].split("|");

                var codigo = itens[0];
                var descricao = itens[1];
                var unidade = itens[2];
                var qtd= itens[3];
                var vlr = itens[4];
                var valor_editado = formatMoney(vlr);
                var total = itens[5];
                var pde = formatMoney(itens[6]);
                var vde = formatMoney(itens[7]);
                var total_editado = formatMoney(total);

                html += '<tr>';
                html += '<td class="txtcodigo">' + codigo + '</td>';
                html += '<td class="txtdesc">' + descricao + '</td>';
                html += '<td class="txtuni">' + unidade + '</td>';
                html += '<td class="txtqtd" align="right">' + qtd + '</td>';
                html += '<td class="txtvlr" align="right">' + valor_editado + '</td>';
                html += '<td class="txtpde" align="right">' + pde + '</td>';
                html += '<td class="txtvde" align="right">' + vde + '</td>';
                html += '<td class="txttotal" align="right">' + total_editado + '</td>';
                html += '</tr>';
            }

            html += '</tbody>';

            html += '</table>';
            document.getElementById('tabela_itens_consulta').innerHTML = html;
        }
    });
    // FIM LER ITENS NA CONSULTA

};

$(document).ready(function(){
    $('#ano_filtro').change(function(){
       consultar();
    });
});

$(document).ready(function(){
    $('#codigo_conta_contabil').change(function(){
       consultar();
    });
});

$(document).ready(function(){
    $('#codigo_local_filtro').change(function(){
       consultar();
    });
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

    $(".exibir_filtro").click(function(){
        $('.filtro_escondido').hide();
        $('.filtro_exibido').show();
    });

    $(".esconder_filtro").click(function(){
        var tipo_caixa = $("#tipo_caixa").val();

        if (tipo_caixa=="M") {
            $('.opcao_pdf').hide();
        }
        else {
            $('.opcao_pdf').show();
        }

        $('.filtro_escondido').show();
        $('.filtro_exibido').hide();
    });

});


function filtros() {
//    $("#codigo_alfa_filtro").val('');
//    $("#codigo_number_filtro").val('');
//    $("#codigo_raca_filtro").val('');

    $('#modal_filtros').modal('show');
}

function voltar_inclusao(){
    location.href='form_previsao_contas.php';
}

function consultar() {
    var mes = $("#mes_filtro").val();
    var ano = $("#ano_filtro").val();
    var codigo_conta = $("#codigo_conta_contabil").val();
    var codigo_local = $("#codigo_local_filtro").val();

    if (codigo_conta==null) {
        codigo_conta=[''];
    }

    if (codigo_local==null) {
        codigo_local=[''];
    }

	$.post("form_lista_previsao_contas.php", {codigo_conta:codigo_conta, mes:mes, ano:ano, codigo_local:codigo_local},
  		function(valor){ $("div[id=lista_contas]").html(valor); 
    });

    return;
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

/*
function imprimir_analize_contas() {
    $('#aguardar').show();

    var ano = $("#ano_mensal").val();
    var mes = "Todos";
    var opc_rel = $("#opc_mensal").val();
    var forma_pag = $("#forma_pagto_mensal").val();
    var local = $("#codigo_local_filtro").val();

    if (forma_pag==null) {
        forma_pag=[''];
    }

    if (local==null) {
        local=[''];
    }

    $.post("rel_lista_contas_previsto_realizado_excel.php", {opc_rel:opc_rel, ano:ano, forma_pag:forma_pag, local:local},
        function(valor){  
            alert (valor);

    });

//    location.href='rel_lista_contas_previsto_realizado_excel.php?mes=' + mes + "&ano=" + ano + "&opc_rel=" + opc_rel + "&forma_pag=" + forma_pag + "&local=" + local;
    tout = setTimeout('limpar_tela()', 1000);
}
*/

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
    $('#aguardar').hide();
}
