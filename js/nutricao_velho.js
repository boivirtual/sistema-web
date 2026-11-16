/**NUTRIÇÃO*/
var controle_estoque = $("#controle_estoque").val();

window.addEventListener("load", function(event) {
    $.post("lista_produto.php", {}, function(valor){
        $("select[name=codigo_produto]").html(valor);
        $('.selectpicker').selectpicker('refresh');            
    });

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

    var local = $("#codigo_local").val();

    if (local!='' && local!=0) {
        $.post("lista_pasto.php", {local:local}, function(valor){
            $("select[name=codigo_pasto]").html(valor);
            $('.selectpicker').selectpicker('refresh');            
        });
    }

    var local = $("#codigo_local_filtro").val();

    if (local==null || local=='000000000') {
        local=[''];
    }

    $.post("lista_pasto_rel.php", {local:local}, function(valor){
        $("select[name=codigo_pasto_filtro]").html(valor);
        $('.selectpicker').selectpicker('refresh');            
    });

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

function consultar() {
    var local = $("#codigo_local").val();
    var pasto = $("#codigo_pasto").val();
    var produto = $("#codigo_produto").val();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();

    if (data_inicial > data_final) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Data Inicial e Final corretamente!');
        return;
    }

    if (local == '000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Fazenda!');
        return;
    }

    if (pasto==null || pasto=='000000000') {
        pasto=[''];
    }

    $("#aguardar").modal('show');

    $.post("form_lista_nutricao.php", {local:local, pasto:pasto, produto:produto, data_inicial:data_inicial, data_final:data_final },
        function(valor){ 
        $("div[id=lista_nutricao]").html(valor);
        $("#aguardar").modal('hide');

    });

    return;
}

$(document).ready(function(){
    $('#tabela_nutricao').DataTable({
        responsive: true,
        paging:   false,
        ordering: true,
        info:     true,
        order: [[ 2, "asc" ]],
        language: {
        sSearch: "Busca:",
        zeroRecords: "Nada encontrado",
        info: "Registros encontrados: _END_ ",
        infoEmpty: "Nenhum registro disponível",
        infoFiltered: "(filtrado de _MAX_ registros no total)",
        },
        columnDefs: [
            { type: 'date-br', targets: 1 }
        ],
        initComplete: function() {
            $('table.dataTable').css("width", "100%");
          }
    });

    $('#tabela_itens_consulta').DataTable({
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

    $('#codigo_local').change(function(event) {
        var local = $("#codigo_local").val();

        $.post("lista_pasto.php", {local:local}, function(valor){
            $("select[name=codigo_pasto]").html(valor);
            $('.selectpicker').selectpicker('refresh');            
        });
    });

    $('#codigo_local_filtro').change(function(event) {
        var local = $("#codigo_local_filtro").val();

        if (local==null || local=='000000000') {
            local=[''];
        }

        $.post("lista_pasto_rel.php", {local:local}, function(valor){
            $("select[name=codigo_pasto_filtro]").html(valor);
            $('.selectpicker').selectpicker('refresh');            
        });
    });

    $('#codigo_local_distribuir').change(function(event) {
        var local = $("#codigo_local_distribuir").val();

        $.post("lista_pasto.php", {local:local}, function(valor){
            $("select[name=codigo_pasto_distribuir]").html(valor);
        });
    });


});

function finalizar_sair(){
    location.href= "form_movimentacao_animais.php";
}

function relatorio_consumo_nutricao(){
    location.href= "form_rel_consumo_nutricao.php?tipo=2";
}

function editar_nutricao(array_animal) {
    array_nutricao = array_animal.split('|');
    $("#id_nutricao").val(array_nutricao[0]);
    $("#descricao_local").val(array_nutricao[2]);
    $("#descricao_pasto").val(array_nutricao[4]);
    $("#descricao_produto").val(array_nutricao[5]);
    $("#apresentacao_estoque").html(array_nutricao[6]);
    $("#quantidade").val(formatMoney(array_nutricao[7]));
    $("#qtd_media").val(formatMoney(array_nutricao[8]));
    $("#qtd_cabecas").val(array_nutricao[9]);
    $("#observacao").val(array_nutricao[10]);
    $("#descricao_cocho").val(array_nutricao[11]);
    $("#incluido_em").html(array_nutricao[12]);
    $("#incluido_por").html(array_nutricao[13]);
    $("#alterado_em").html(array_nutricao[14]);
    $("#alterado_por").html(array_nutricao[15]);

    $("#tipo_gravacao").val(1);

    $('#modal_nutricao .modal-title').html('Nutrição - Editar');
    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');


    if (array_nutricao[15]=='') {
        $(".registro_alterado").hide();
    }
    else {
        $('.registro_alterado').show();
    }

    $('.confirma_gravar').show();
    $('#modal_nutricao').modal('show');

}

function excluir_nutricao(array_animal, opcao) {
    array_nutricao = array_animal.split('|');
    $("#id_nutricao").val(array_nutricao[0]);
    $("#id_local").val(array_nutricao[1]);
    $("#descricao_local").val(array_nutricao[2]);
    $("#descricao_pasto").val(array_nutricao[4]);
    $("#id_produto").val(array_nutricao[16]);
    $("#descricao_produto").val(array_nutricao[5]);
    $("#apresentacao_estoque").html(array_nutricao[6]);
    $("#quantidade").val(formatMoney(array_nutricao[7]));
    $("#qtd_media").val(formatMoney(array_nutricao[8]));
    $("#qtd_cabecas").val(array_nutricao[9]);
    $("#observacao").val(array_nutricao[10]);
    $("#descricao_cocho").val(array_nutricao[11]);
    $("#incluido_em").html(array_nutricao[12]);
    $("#incluido_por").html(array_nutricao[13]);
    $("#alterado_em").html(array_nutricao[14]);
    $("#alterado_por").html(array_nutricao[15]);

    $("#tipo_gravacao").val(opcao);

    if (array_nutricao[15]=='') {
        $(".registro_alterado").hide();
    }
    else {
        $('.registro_alterado').show();
    }

    $('#modal_nutricao .modal-title').html('Nutrição - Enviar para Lixeira');
    $(".confirma_gravar").html('Enviar para Lixeira').removeClass('btn-primary').addClass('btn-danger');

    $('.confirma_gravar').show();
    $('#modal_nutricao').modal('show');
}

function gravar_nutricao() {
    var tipo_gravacao = $("#tipo_gravacao").val();

    if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_nutricao').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_nutricao_alterar.php',
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
    else {
        var dados = $('#form_gravar_nutricao').serialize();
        $.ajax({
            type: "POST",
            url: 'gravar_nutricao_alterar.php',
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


function digita_valor(){
    $('#quantidade').bind('keypress',mask.money);
    $('#qtd_media').bind('keypress',mask.money);
}

function exibe_quantidade(){
    var quantidade = $("#quantidade").val();
    if (verifica_virgula(quantidade)==',') {
        quantidade = replace_valor(quantidade);
    }

    $("#quantidade").val(formatMoney(quantidade));

    var qtd_animais = $("#qtd_cabecas").val();
    var quantidade = replace_valor($("#quantidade").val());

    var qtd_media = quantidade / qtd_animais;
    $("#qtd_media").val(formatMoney(qtd_media));

}

function exibe_qtd_media(){
    var qtd_media = $("#qtd_media").val();
    if (verifica_virgula(qtd_media)==',') {
        qtd_media = replace_valor(qtd_media);
    }

    $("#qtd_media").val(formatMoney(qtd_media));
}

// RELATORIOS
function listar_consumo_nutricao(opcao){
    var local = $("#codigo_local_filtro").val();
    var pasto = $("#codigo_pasto_filtro").val();
    var produto = $("#codigo_produto_filtro").val();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();

    if (data_inicial == '' || data_final == '') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Data Inicial e Final.');
        return;
    }

    if (data_inicial > data_final) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Data Inicial e Final Corretamente.');
        return;
    }

    if (local==null) {
        var array_local= new Array();
    }
    else {
        var array_local = new Array();
        var valor = new Array();

        for (i = 0; i <= local.length; i++) {
            valor[i]=local[i];
        }

        var array_local=valor.join(",");
    }

    if (pasto==null) {
        var array_pasto= new Array();
    }
    else {
        var array_pasto = new Array();
        var valor = new Array();

        for (i = 0; i <= pasto.length; i++) {
            valor[i]=pasto[i];
        }

        var array_pasto=valor.join(",");
    }

    if (produto==null) {
        var array_produto= new Array();
    }
    else {
        var array_produto = new Array();
        var valor = new Array();

        for (i = 0; i <= produto.length; i++) {
            valor[i]=produto[i];
        }

        var array_produto=valor.join(",");
    }

    var options = $('#codigo_local_filtro option:selected');
    var local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_local_filtro').text();
        local_filtro.push( desc.trim() );
    });

    if (local_filtro!=''){
        local_filtro = local_filtro+'->';
    }
    else {
        local_filtro = '';
    }

    var options = $('#codigo_pasto_filtro option:selected');
    var pasto_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_pasto_filtro').text();
        pasto_filtro.push( desc.trim() );
    });

    if (pasto_filtro!=''){
        pasto_filtro = 'Pastos:'+pasto_filtro+'->';
    }
    else {
        pasto_filtro = '';
    }

    var options = $('#codigo_produto_filtro option:selected');
    var produto_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_produto_filtro').text();
        produto_filtro.push( desc.trim() );
    });

    if (produto_filtro!=''){
        produto_filtro = produto_filtro+'->';
    }
    else {
        produto_filtro = '';
    }

    if (data_inicial!='' && data_inicial!=0) {
        var data_ini = data_inicial.split("-");
        var data_fim = data_final.split("-");

        data_filtro = 'Período: ' + data_ini[2]+'/'+data_ini[1]+'/'+data_ini[0] + ' até ' + data_fim[2]+'/'+data_fim[1]+'/'+data_fim[0];
    }
    else {
        data_filtro = '';
    }

    var descricao_filtro = local_filtro+pasto_filtro+produto_filtro+data_filtro;

    var tipo_relatorio = $("#tipo_relatorio").val();

    $("#aguardar").modal();

    if (opcao=='1') {
        location.href='form_lista_consumo_nutricao_rel.php?descricao_filtro=' + descricao_filtro + 
            '&local=' + array_local  + 
            '&pasto=' + array_pasto + '&produto=' + array_produto  + '&data_inicial=' + data_inicial + 
            '&data_final=' + data_final + '&tipo=' + tipo_relatorio;
    }
    else {
        location.href='rel_lista_consumo_nutricao_excel.php?descricao_filtro=' + descricao_filtro + 
            '&local=' + array_local  + 
            '&pasto=' + array_pasto + '&produto=' + array_produto  + '&data_inicial=' + data_inicial + 
            '&data_final=' + data_final + '&tipo=' + tipo_relatorio;

        tout = setTimeout('limpar_tela()', 5000);
    }
}

function lista_consumo_nutricao_excel(){
    var local = $("#codigo_local_filtro").val();
    var pasto = $("#codigo_pasto_filtro").val();
    var produto = $("#codigo_produto_filtro").val();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var descricao_filtro = $("#descricao_filtro").val();

    $("#aguardar").modal();

    location.href='rel_lista_consumo_nutricao_excel.php?descricao_filtro=' + descricao_filtro +
    '&local=' + local  +
    '&pasto=' + pasto + '&produto=' + produto + '&data_inicial=' + data_inicial  + '&data_final=' + data_final;

    tout = setTimeout('limpar_tela()', 5000);

}

function limpar_tela(){
    $('#aguardar').modal('hide');
}

function voltar_relatorios() {
    var tipo_relatorio = $("#tipo_relatorio").val();

    if (tipo_relatorio==1) {
        location.href='form_relatorios_produtivos.php';
    }
    else {
        location.href='form_nutricao.php';
    }
}

function voltar_filtro() {
    var tipo_relatorio = $("#tipo_relatorio").val();

    location.href='form_rel_consumo_nutricao.php?tipo='+tipo_relatorio;
}


// FIM RELATORIO

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

/** permite digitar somente numeros nos campos numericos */
function numeros(field, event) {
    var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;

    if ((keyCode >= 48 && keyCode <= 57) || (keyCode == 8) || (keyCode == 9) || (keyCode == 13) || (keyCode == 46)) {
        if (keyCode == 13) {
            var i;
            for (i = 0; i < field.form.elements.length; i++)
                if (field == field.form.elements[i])
                    break;
            i = (i + 1) % field.form.elements.length;
            field.form.elements[i].focus();
            return false;
        } else
            return true;
    } else {
        return false;
    }
}

function desabilita_enter (field, event) {
    var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;

    if (keyCode == 13) {
        var i;
        for (i = 0; i < field.form.elements.length; i++)
            if (field == field.form.elements[i])
                break;
                i = (i + 1) % field.form.elements.length;
                field.form.elements[i].focus();
                return false;
        } 
    else
                return true;
}      

function adicionaZero(numero){
    if (numero <= 9) 
        return "0" + numero;
    else
        return numero; 
}

$(window).resize(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});

$(document).ready(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
 "date-br-pre": function ( a ) {
  if (a == null || a == "") {
   return 0;
  }
  var brDatea = a.split('/');
  return (brDatea[2] + brDatea[1] + brDatea[0]) * 1;
 },

 "date-br-asc": function ( a, b ) {
  return ((a < b) ? -1 : ((a > b) ? 1 : 0));
 },

 "date-br-desc": function ( a, b ) {
  return ((a < b) ? 1 : ((a > b) ? -1 : 0));
 }
} );