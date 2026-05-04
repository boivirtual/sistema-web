/**TABELA PASTOS*/
window.addEventListener("load", function(event) {
    listar_capim();
});        

function listar_capim(){
    
    var local = $("#codigo_local_filtro").val();

    if (local==null) {
        local=[''];
    }

    $.post("form_lista_capim.php", {local:local}, function(valor){ 
        $("div[id=lista_capim]").html(valor); 
    });
}

$(document).ready(function(){
    $('#tabela_capim').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": false,
        "info":     true,
        "language": {
        "sSearch": "Busca:",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(Filtrado de _MAX_ registros no total)",
        },
        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function() {
            $('table.dataTable').css("width", "100%");
          }
    });
});

function voltar_inclusao(){
    location.href='form_tabela_capim.php';
}

function incluir_novo() {
    $("#codigo_conta").val('000000000');
    $("#descricao").val('');
    $("#tipo_gravacao").val(0);
    $("#informacao").hide();    

    $('#modal_incluir .modal-title').html('Tipo de Forragem - Incluir');
    $('.confirma_gravar').html('Confirmar Inclusão').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    $('.voltar_inclusao').show();
    $('.voltar').hide();
    $('#modal_incluir').modal('show');
}

function editar_capim(array_registro) {
    $array_conta = array_registro.split('|');
    $("#codigo_conta").val($array_conta[0]);
    $("#descricao").val($array_conta[1]);

    $("#tipo_gravacao").val(1);

    $('#modal_incluir .modal-title').html('Tipo de Forragem - Editar');
    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    $('.voltar_inclusao').hide();
    $('.voltar').show();
    $("#informacao").show();    

    if ($array_conta[4]=='') {
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
    $("#descricao").val($array_conta[1]);

    if ($array_conta[4]=='') {
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
        $('#modal_incluir .modal-title').html('Tipo de Forragem - Enviar para Lixeira');
        $(".confirma_gravar").html('Enviar para Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }
    else {
        $('#modal_incluir .modal-title').html('Tipo de Forragem - Remover da Lixeira');
        $(".confirma_gravar").html('Remover da Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }

    $('.confirma_gravar').show();
    $('.voltar_inclusao').hide();
    $('.voltar').show();
    $('#modal_incluir').modal('show');
}

function gravar_capim() {
    var tipo_gravacao = $("#tipo_gravacao").val();
    if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_capim').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_capim.php',
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
            var dados = $('#form_gravar_capim').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_capim.php',
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
        var dados = $('#form_gravar_capim').serialize();
        $.ajax({
            type: "POST",
            url: 'gravar_capim.php',
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
        var dados = $('#form_gravar_capim').serialize();

        $(".confirma_gravar").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: 'gravar_capim.php',
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

$(window).resize(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input").addClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input").removeClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});

$(document).ready(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input").addClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input").removeClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});