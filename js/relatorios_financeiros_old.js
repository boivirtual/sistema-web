/**RELATORIOS FINANCEIROS*/
window.addEventListener("load", function(event) {
    var expande_tela = $("#expande_tela").val();

    if (expande_tela=="S"){
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
    }
});        

$(document).ready(function(){
    $('#tabela_analise_recebimento').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": false,
        "info":     false,
        language: {
          sSearch: "Buscar na lista:",
          zeroRecords: "Nada encontrado",
          info: "Registros encontrados: _END_ ",
          infoEmpty: "Nenhum registro disponível",
          infoFiltered: "(filtrado de _MAX_ registros no total)",
        },

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function() {
            $('table.dataTable').css("width", "100%");
        }
    });

    $('#tabela_analise_pagamento').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": false,
        "info":     false,
        language: {
          sSearch: "Buscar na lista:",
          zeroRecords: "Nada encontrado",
          info: "Registros encontrados: _END_ ",
          infoEmpty: "Nenhum registro disponível",
          infoFiltered: "(filtrado de _MAX_ registros no total)",
        },

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function() {
            $('table.dataTable').css("width", "100%");
        }
    });
});


function imprimir_fluxo_caixa(opcao) {
    //var tipo_caixa = $("#tipo_caixa").val();
    $('#aguardar').show();

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
        if (opcao==1){
            var width = 350;
            var height = 500;
            var left = 40;
            var top = 40;
            window.open('rel_fluxo_caixa_diario_pdf.php?mes=' + mes + "&ano=" + ano + "&opc_rel=" + opc_rel + "&forma_pag=" + forma_pag, 'janela', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ', scrollbars=yes, status=yes, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=yes');
                     
            tout = setTimeout('limpar_tela()', 1000);
        }
        else {
            location.href='rel_fluxo_caixa_diario_excel.php?mes=' + mes + "&ano=" + ano + "&opc_rel=" + opc_rel + "&forma_pag=" + forma_pag;

            tout = setTimeout('limpar_tela()', 1000);
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

function listar_fluxo_caixa_tela(){
    var ano = $("#ano_diario").val();
    var mes = $("#mes_diario").val();
    var tipo_rel = $("#tipo_rel").val();
    var conta_pagamento = $("#conta_pagamento").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_local = $("#codigo_fazenda").val();

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

    if (conta_pagamento==null) {
        var array_conta= new Array();
    }
    else {
        var array_conta = new Array();
        var valor = new Array();

        for (i = 0; i <= conta_pagamento.length; i++) {
            valor[i]=conta_pagamento[i];
        }

        var array_conta=valor.join(",");
    }

    if (tipo_rel==2) {
        opc_rel_filtro='Realizado->';
    }
    else{
        opc_rel_filtro='Não Realizado->';
    }

    if (conta_pagamento!=0){
        var options = $('#conta_pagamento option:selected');
        var conta_filtro = [];

        $(options).each(function(){
            var desc = $(this).bind('#conta_pagamento').text();
            conta_filtro.push( desc.trim() );
        });
        conta_filtro = 'Conta Pag: '+conta_filtro+'->';
    }
    else {
        conta_filtro = 'Conta Pag: Todas';
    }

    var options = $('#codigo_cc option:selected');
    var cc_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_cc').text();
        cc_filtro.push( desc.trim() );
    });

    if (cc_filtro!=''){
        cc_filtro = 'C.Custo: '+cc_filtro +'->';
    }
    else {
        cc_filtro = 'C.Custo: Todos->';
    }

    periodo = 'Período: ' + mes + "/" + ano +'->';

    var options = $('#codigo_fazenda option:selected');
    var local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_fazenda').text();
        local_filtro.push( desc.trim() );
    });

    if (local_filtro!=''){
        local_filtro = 'Local: '+local_filtro+'->';
    }
    else {
        local_filtro = '';
    }


    var descricao_filtro = local_filtro+opc_rel_filtro+periodo+
          cc_filtro+conta_filtro;

    $("#aguardar").modal();

    location.href='form_lista_fluxo_caixa_rel.php?descricao_filtro=' + descricao_filtro + 
    '&tipo_rel=' + tipo_rel + '&ano=' + ano + 
    '&mes=' + mes + '&conta_pagamento=' + array_conta + 
    '&c_custo=' + array_cc  + 
    '&fazenda=' + array_fazenda;
}

function listar_fluxo_caixa_excel(){
    var ano = $("#ano_diario").val();
    var mes = $("#mes_diario").val();
    var tipo_rel = $("#tipo_rel").val();
    var conta_pagamento = $("#conta_pagamento").val();
    var codigo_cc = $("#codigo_cc").val();
    var descricao_filtro = $("#descricao_filtro").val();
    var codigo_local = $("#codigo_fazenda").val();

    $("#aguardar").modal();

    location.href='rel_fluxo_caixa_diario_excel.php?descricao_filtro=' + descricao_filtro + 
    '&tipo_rel=' + tipo_rel + '&ano=' + ano + 
    '&mes=' + mes + '&conta_pagamento=' + conta_pagamento + 
    '&c_custo=' + codigo_cc  + 
    '&fazenda=' + codigo_local;

    tout = setTimeout('limpar_tela()', 5000);
}

function listar_contas_receber_tela(){
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var codigo_conta = $("#codigo_conta").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_cliente = $("#codigo_cliente").val();
    var tipo_data = $("input[name='tipo_data']:checked").val();
    var tipo_rel = $("input[name='tipo_rel']:checked").val();

    if (data_inicial==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a data inicial do período.');
        return;
    }

    if (data_inicial!=0){
        if (data_final==0 || data_final<data_inicial){
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe a data final do período corretamente.');
            return;
        }
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

    if (codigo_cliente==null) {
        var array_cliente= new Array();
    }
    else {
        var array_cliente = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_cliente.length; i++) {
            valor[i]=codigo_cliente[i];
        }

        var array_cliente=valor.join(",");
    }

    if (tipo_rel=='A') {
        opc_rel_filtro='Analítico->';
    }
    else{
        opc_rel_filtro='Sintético->';
    }

    if (tipo_data=='V') {
        opc_data_filtro='Dt Vencimento->';
    }
    else if (tipo_data=='E'){
        opc_data_filtro='Dt Emissão->';
    }
    else {
        opc_data_filtro='Dt Recemimento->';
    }

    var options = $('#codigo_cliente option:selected');
    var cliente_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_cliente').text();
        cliente_filtro.push( desc.trim() );
    });

    if (cliente_filtro!=''){
        cliente_filtro = cliente_filtro+'->';
    }
    else {
        cliente_filtro = '';
    }

    if (codigo_conta!=0){
        var options = $('#codigo_conta option:selected');
        var conta_filtro = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_conta').text();
            conta_filtro.push( desc.trim() );
        });
        conta_filtro = 'Conta: '+conta_filtro+'->';
    }
    else {
        conta_filtro = '';
    }

    var options = $('#codigo_cc option:selected');
    var cc_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_cc').text();
        cc_filtro.push( desc.trim() );
    });

    if (cc_filtro!=''){
        cc_filtro = 'C.Custo: '+cc_filtro;
    }
    else {
        cc_filtro = '';
    }

    var data_ini = data_inicial.split("-");
    var dia_ini = data_ini[2];
    var mes_ini = data_ini[1];
    var ano_ini = data_ini[0];

    var data_fim = data_final.split("-");
    var dia_fim = data_fim[2];
    var mes_fim = data_fim[1];
    var ano_fim = data_fim[0];
    periodo = 'Período: de ' + dia_ini + "/" + mes_ini + "/" + ano_ini + ' ate ' +
                               dia_fim + "/" + mes_fim + "/" + ano_fim+'->';


    var descricao_filtro = periodo+opc_data_filtro+opc_rel_filtro+
          cliente_filtro+conta_filtro+cc_filtro;

    $("#aguardar").modal();

    location.href='form_lista_analise_recebimento_rel.php?descricao_filtro=' + descricao_filtro + 
    '&tipo_rel=' + tipo_rel + '&tipo_data=' + tipo_data + '&data_inicial=' + data_inicial + 
    '&data_final=' + data_final +'&cliente=' + array_cliente + '&conta=' + codigo_conta + 
    '&c_custo=' + array_cc;
}

function listar_contas_receber_excel(){
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var tipo_rel = $("#tipo_rel").val();
    var tipo_data = $("#tipo_data").val();
    var descricao_filtro = $("#descricao_filtro").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_conta = $("#codigo_conta").val();
    var codigo_cliente = $("#codigo_cliente").val();

    $("#aguardar").modal();

    location.href='rel_analise_recebimentos_excel.php?descricao_filtro=' + descricao_filtro + 
    '&tipo_rel=' + tipo_rel + '&tipo_data=' + tipo_data + '&c_custo=' + codigo_cc + 
    '&conta=' + codigo_conta + '&cliente=' + codigo_cliente +  
    '&data_inicial=' + data_inicial + '&data_final=' + data_final;

    tout = setTimeout('limpar_tela()', 5000);
}

function listar_contas_pagar_tela(){
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var codigo_conta = $("#codigo_conta").val();
    var codigo_local = $("#codigo_fazenda").val();
    var codigo_fornecedor = $("#codigo_fornecedor").val();
    var codigo_cc = $("#codigo_cc").val();
    var tipo_data = $("input[name='tipo_data']:checked").val();
    var tipo_rel = $("input[name='tipo_rel']:checked").val();

    if (data_inicial==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a data inicial do período.');
        return;
    }

    if (data_inicial!=0){
        if (data_final==0 || data_final<data_inicial){
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe a data final do período corretamente.');
            return;
        }
    } 

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

    if (codigo_fornecedor==null) {
        var array_fornecedor= new Array();
    }
    else {
        var array_fornecedor = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_fornecedor.length; i++) {
            valor[i]=codigo_fornecedor[i];
        }

        var array_fornecedor=valor.join(",");
    }

    if (tipo_rel=='A') {
        opc_rel_filtro='Analítico->';
    }
    else{
        opc_rel_filtro='Sintético->';
    }

    if (tipo_data=='V') {
        opc_data_filtro='Dt Vencimento->';
    }
    else if (tipo_data=='E'){
        opc_data_filtro='Dt Emissão->';
    }
    else {
        opc_data_filtro='Dt Recemimento->';
    }

    var options = $('#codigo_fornecedor option:selected');
    var fornecedor_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_fornecedor').text();
        fornecedor_filtro.push( desc.trim() );
    });

    if (fornecedor_filtro!=''){
        fornecedor_filtro = fornecedor_filtro+'->';
    }
    else {
        fornecedor_filtro = '';
    }

    if (codigo_conta!=0){
        var options = $('#codigo_conta option:selected');
        var conta_filtro = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_conta').text();
            conta_filtro.push( desc.trim() );
        });
        conta_filtro = 'Conta: '+conta_filtro;
    }
    else {
        conta_filtro = '';
    }

    var options = $('#codigo_fazenda option:selected');
    var codigo_local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_fazenda').text();
        codigo_local_filtro.push( desc.trim() );
    });

    if (codigo_local_filtro!=''){
        codigo_local_filtro = 'Local: '+codigo_local_filtro+'->';
    }
    else {
        codigo_local_filtro = '';
    }

    var options = $('#codigo_cc option:selected');
    var codigo_cc_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_cc').text();
        codigo_cc_filtro.push( desc.trim() );
    });

    if (codigo_cc_filtro!=''){
        codigo_cc_filtro = 'C.Custos: '+codigo_cc_filtro+'->';
    }
    else {
        codigo_cc_filtro = '';
    }

    var data_ini = data_inicial.split("-");
    var dia_ini = data_ini[2];
    var mes_ini = data_ini[1];
    var ano_ini = data_ini[0];

    var data_fim = data_final.split("-");
    var dia_fim = data_fim[2];
    var mes_fim = data_fim[1];
    var ano_fim = data_fim[0];
    periodo = 'Período: de ' + dia_ini + "/" + mes_ini + "/" + ano_ini + ' ate ' +
                               dia_fim + "/" + mes_fim + "/" + ano_fim+'->';


    var descricao_filtro = codigo_local_filtro+codigo_cc_filtro+periodo+opc_data_filtro+opc_rel_filtro+
          fornecedor_filtro+conta_filtro;

    $("#aguardar").modal();

    location.href='form_lista_analise_pagamento_rel.php?descricao_filtro=' + descricao_filtro + 
    '&tipo_rel=' + tipo_rel + '&tipo_data=' + tipo_data + '&data_inicial=' + data_inicial + 
    '&data_final=' + data_final +'&fornecedor=' + array_fornecedor + '&conta=' + codigo_conta + 
    '&fazendas=' + array_fazenda + '&codigo_cc=' + array_cc;
}

function listar_contas_pagar_excel(){
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

    location.href='rel_analise_pagamentos_excel.php?descricao_filtro=' + descricao_filtro + 
    '&tipo_rel=' + tipo_rel + '&tipo_data=' + tipo_data + '&fazendas=' + codigo_local + 
    '&conta=' + codigo_conta + '&fornecedor=' + codigo_fornecedor +  
    '&data_inicial=' + data_inicial + '&data_final=' + data_final + '&codigo_cc=' + codigo_cc;

    tout = setTimeout('limpar_tela()', 5000);
}

function listar_previsto_realizado_tela(){
    var ano = $("#ano_mensal").val();
    var tipo_rel = $("#tipo_rel").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_local = $("#codigo_fazenda").val();

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

    if (codigo_cc==null) {
        var array_codigo_cc= new Array();
    }
    else {
        var array_codigo_cc = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_cc.length; i++) {
            valor[i]=codigo_cc[i];
        }

        var array_codigo_cc=valor.join(",");
    }

    var options = $('#codigo_cc option:selected');
    var codigo_cc_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_cc').text();
        codigo_cc_filtro.push( desc.trim() );
    });

    if (codigo_cc_filtro!=''){
            codigo_cc_filtro = codigo_cc_filtro + '->';
    }

    var options = $('#codigo_fazenda option:selected');
    var codigo_local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_fazenda').text();
        codigo_local_filtro.push( desc.trim() );
    });

    if (codigo_local_filtro!=''){
        codigo_local_filtro = 'Local: '+codigo_local_filtro+'->';
    }
    else {
        codigo_local_filtro = '';
    }

    var options = $('#tipo_rel option:selected');
    var tipo_rel_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#tipo_rel').text();
        tipo_rel_filtro.push( desc.trim());
    });

    if (tipo_rel_filtro!=''){
            tipo_rel_filtro = tipo_rel_filtro;
    }

    var descricao_filtro = codigo_local_filtro+'Ano: '+ano+'->'+codigo_cc_filtro+tipo_rel_filtro;

    $("#aguardar").modal();

    location.href='form_lista_analise_previsto_realizado_rel.php?descricao_filtro=' + descricao_filtro + 
    '&tipo_rel=' + tipo_rel + '&codigo_cc=' + array_codigo_cc + '&fazendas=' + array_fazenda +
    '&ano=' + ano;

}

function listar_previsao_excel() {
    var ano = $("#ano_mensal").val();
    var tipo_rel = $("#tipo_rel").val();
    var codigo_cc = $("#codigo_cc").val();
    var codigo_local = $("#codigo_fazenda").val();
    var descricao_filtro = $("#descricao_filtro").val();

    $("#aguardar").modal();

    location.href='rel_lista_contas_previsto_realizado_excel.php?descricao_filtro=' + descricao_filtro + 
    '&tipo_rel=' + tipo_rel + '&fazendas=' + codigo_local + 
    '&codigo_cc=' + codigo_cc + '&ano=' + ano;

    tout = setTimeout('limpar_tela()', 5000);

}

function limpar_tela(){
    $('#aguardar').modal('hide');
}

function voltar_filtro_caixa() {
    location.href='form_rel_fluxo_caixa.php';
}

function voltar_filtro() {
    location.href='form_rel_analise_recebimento.php';
}

function voltar_filtro_pagar() {
    location.href='form_rel_analise_pagamento.php';
}

function voltar_filtro_previsao() {
    location.href='form_rel_analise_previsto_realizado.php';
}

function voltar_relatorios(){
    location.href='form_relatorios_financeiros.php'
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

function formatMoney(n, c, d, t) {
  c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
  return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}


function replace_valor(valor_replace){
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
