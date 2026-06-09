/**MAPA DE GADOS RELATORIOS */
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

    $(".tipo_rel").click(function(){
        var tipo_rel = $("input[name='tipo_rel']:checked").val();   

        if (tipo_rel=='C') {
            $('.modelo').show();
        }
        else {
            $('.modelo').hide();
        }
    });

});

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

// RELATORIOS
function listar_mapa_gado(opcao){
    var tipo_rel = $("input[name='tipo_rel']:checked").val();
    var modelo_rel = $("input[name='modelo_rel']:checked").val();
    var local = $("#codigo_local_filtro").val();

    if (local == 0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Local.');
        return;
    }

    var options = $('#codigo_local_filtro option:selected');
    var local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_local_filtro').text();
        local_filtro.push( desc.trim() );
    });

    if (local_filtro!=''){
        local_filtro = local_filtro;
    }
    else {
        local_filtro = '';
    }

    if (tipo_rel=='C') {
        if (modelo_rel=='R') {
            opc_rel_filtro='Pasto com Animais->Resumido->';
        }
        else {
            opc_rel_filtro='Pasto com Animais->Completo->';
        }
    }
    else{
        opc_rel_filtro='Pasto sem Animais->';
    }

    var descricao_filtro = opc_rel_filtro+local_filtro;

    $("#aguardar").modal();

    if (opcao=='1') {
        if (tipo_rel=='C') {
            if (modelo_rel=='R') {
                location.href='form_lista_mapa_gado_resumido_rel.php?local=' + local + '&tipo_rel=' + tipo_rel + '&descricao_filtro=' + descricao_filtro + '&modelo_rel=' + modelo_rel;

            }
            else {
                location.href='form_lista_mapa_gado_rel.php?local=' + local + '&tipo_rel=' + tipo_rel + '&descricao_filtro=' + descricao_filtro + '&modelo_rel=' + modelo_rel;
            }
        }
        else {
            location.href='form_lista_mapa_gado_sem_rel.php?local=' + local + '&tipo_rel=' + tipo_rel + '&descricao_filtro=' + descricao_filtro;
        }

        tout = setTimeout('limpar_tela()', 5000);
    }
    else {
        if (tipo_rel=='C') {
            if (modelo_rel=='R') {
                location.href='rel_mapa_gado_resumido_excel.php?descricao_filtro=' + descricao_filtro + '&local=' + local;
            }
            else {
                location.href='rel_mapa_gado_excel.php?descricao_filtro=' + descricao_filtro + '&local=' + local;
            }
        }
        else {
            location.href='rel_mapa_gado_sem_excel.php?descricao_filtro=' + descricao_filtro + '&local=' + local;
        }
        tout = setTimeout('limpar_tela()', 3000);
    }
}

function lista_mapa_gado_excel(){
    var local = $("#codigo_local").val();
    var descricao_filtro = $("#descricao_filtro").val();
    var tipo_rel = $("#tipo_rel").val();
    var modelo_rel = $("#modelo_rel").val();

    $("#aguardar").modal();

    if (tipo_rel=='C') {
        if (modelo_rel=='R') {
            location.href='rel_mapa_gado_resumido_excel.php?descricao_filtro=' + descricao_filtro + '&local=' + local;
        }
        else {
            location.href='rel_mapa_gado_excel.php?descricao_filtro=' + descricao_filtro + '&local=' + local;
        }
    }
    else {
        location.href='rel_mapa_gado_sem_excel.php?descricao_filtro=' + descricao_filtro + '&local=' + local;
    }
    tout = setTimeout('limpar_tela()', 3000);
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

function limpar_tela(){
    $('#aguardar').modal('hide');
}

function voltar_filtro() {
    location.href='form_rel_mapa_gados.php';
}

function voltar_relatorios() {
    location.href='form_relatorios_produtivos.php';
}
// FIM RELATORIOS