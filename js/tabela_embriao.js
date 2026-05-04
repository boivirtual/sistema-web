/**TABELA DE ANIMAIS*/
window.addEventListener("load", function(event) {
    var lista_animais_automatico = $("#lista_embrioes_automatico").val();

    if (lista_animais_automatico=='S'){
        consultar();
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

$(document).ready(function(){
    $('#tabela_animais').DataTable({
        "responsive": true,
        "paging":   true,
        "ordering": true,
        "info":     true,
        "pageLength": 100,
        //"order": [[ 1, "asc" ], [ 0, 'asc' ]],
        "language": {
        "oPaginate": {
            "sFirst": "Primeira",
            "sLast": "Última",
            "sNext": "Próxima",
            "sPrevious": "Anterior"
        },
        "sSearch": "Busca:",
        "lengthMenu": "Mostrando _MENU_ registros por página",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });

    $('#nascimento_animal').change(function(){
        var data_nascimento= $('#nascimento_animal').val();

        $.post("ler_categoria_animal.php",{data_nascimento: data_nascimento}, function(valor){
            var php = valor.split("<|>");

            if (php[0]==1){
                $("#categoria_id").val(php[1]);
                $('#idade_animal').val(php[2]);
            }
            return;
        });
    });

});


function sair_inclusao() {
    var tipo_gravacao = $("#tipo_gravacao").val();

    if (tipo_gravacao==0) {
        consultar();
        location.href='form_cadastro_embriao.php';
    }
    else {
        location.href='form_cadastro_embriao.php';
    }
}

function consultar() {
    var cliente = $("#codigo_cliente_filtro").val();

    if (cliente==null) {
        cliente=[''];
    }

    $('#modal_filtros').modal('hide');
    $("#aguardar").modal();

	$.post("form_lista_embrioes.php", {
        cliente:cliente 
         },
  		function(valor){ 

        $("div[id=lista_embrioes]").html(valor); 
        $('#aguardar').modal('hide');
    });
    return;

}

function incluir_novo() {
    $("#lote").val('');
    $("#raca_id").val('000000000');
    $("#tipo_1").val('');
    $("#tipo_2").val('');
    $("#doadora").val('');
    $("#touro").val('');
    $("#laboratorio").val('');
    $("#cliente").val('000000000');
    $("#fazenda").val('');
    $("#tipo_gravacao").val(0);

    $('#modal_incluir .modal-title').html('Embrião - Incluir');
    $('.confirma_gravar').html('Confirmar Inclusão').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    $("#informacao").hide();    

    $('#modal_incluir').modal('show');
    document.getElementById("lote").focus();
}

function editar_animal(array_embriao) {
    array_embriao = array_embriao.split('|');

    $("#codigo_embriao").val(array_embriao[0]);
    $("#lote").val(array_embriao[1]);
    $("#raca_id").val(array_embriao[5]);
    $("#tipo_1").val(array_embriao[8]);
    $("#tipo_2").val(array_embriao[9]);
    $("#doadora").val(array_embriao[2]);
    $("#touro").val(array_embriao[3]);
    $("#laboratorio").val(array_embriao[4]);
    $("#cliente").val(array_embriao[6]);
    $("#fazenda").val(array_embriao[7]);
    $("#incluido_em").text(array_embriao[10]);
    $("#incluido_por").text(array_embriao[11]);
    $("#alterado_em").text(array_embriao[12]);
    $("#alterado_por").text(array_embriao[13]);

    if (array_embriao[13]=='') {
        $("#registro_alterado").hide();
        $("#alterado_por").hide();
        $("#alterado_em").hide();
    }
    else {
        $("#registro_alterado").show();        
        $("#alterado_por").show();
        $("#alterado_em").show();
    }

    $("#tipo_gravacao").val(1);

    $('#modal_incluir .modal-title').html('Embrião - Editar');
    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');
    $("#informacao").show();    
    $('#modal_incluir').modal('show');
}

function enviar_lixeira(array_embriao, opcao) {
    array_embriao = array_embriao.split('|');

    $("#codigo_embriao").val(array_embriao[0]);
    $("#lote").val(array_embriao[1]);
    $("#raca_id").val(array_embriao[5]);
    $("#tipo_1").val(array_embriao[8]);
    $("#tipo_2").val(array_embriao[9]);
    $("#doadora").val(array_embriao[2]);
    $("#touro").val(array_embriao[3]);
    $("#laboratorio").val(array_embriao[4]);
    $("#cliente").val(array_embriao[6]);
    $("#fazenda").val(array_embriao[7]);
    $("#incluido_em").text(array_embriao[10]);
    $("#incluido_por").text(array_embriao[11]);
    $("#alterado_em").text(array_embriao[12]);
    $("#alterado_por").text(array_embriao[13]);

    if (array_embriao[13]=='') {
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
        $('#modal_incluir .modal-title').html('Embrião - Enviar para Lixeira');
        $(".confirma_gravar").html('Enviar para Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }
    else {
        $('#modal_incluir .modal-title').html('Embrião - Remover da Lixeira');
        $(".confirma_gravar").html('Remover da Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }

    $('.confirma_gravar').show();
    $('#modal_incluir').modal('show');
}


function gravar_embriao() {
    var tipo_gravacao = $("#tipo_gravacao").val();

    if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_embriao').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_embriao.php',
                data: dados,
                success: function(data){
                    if (data.error) {
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html(data.message);
                    }
                    else if (data.success){
                        $("#mensagem_retorno_edicao").modal();
                        $("#mensagem_retorno_edicao .modal-body").html(data.message);
                    }
                }
            });
        }
    }
    else if (tipo_gravacao==3) {
        if (window.confirm("Confirma remover esse registro da lixeira?")) {
            var dados = $('#form_gravar_embriao').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_embriao.php',
                data: dados,
                success: function(data){
                    if (data.error) {
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html(data.message);
                    }
                    else if (data.success){
                        $("#mensagem_retorno_edicao").modal();
                        $("#mensagem_retorno_edicao .modal-body").html(data.message);
                    }
                }
            });
        }
    }
    else {
        var dados = $('#form_gravar_embriao').serialize();

        $(".confirma_gravar").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: 'gravar_embriao.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    $(".confirma_gravar").attr("disabled", false);
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    $(".confirma_gravar").attr("disabled", false);
                    
                    if (tipo_gravacao==1) {
                        $("#mensagem_retorno_edicao").modal();
                        $("#mensagem_retorno_edicao .modal-body").html(data.message);
                    }
                    else {
                        $("#mensagem_retorno").modal();
                        $("#mensagem_retorno .modal-body").html(data.message);
                    }
                }
            }
        });
    }
}


// RELATORIO

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

function mostrar_reproducao(){
    $(".reprod").show();
}

function esconder_reproducao(){
    $(".reprod").hide();
}

