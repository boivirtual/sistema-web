/**AGENDA*/
window.addEventListener("load", function (event) {
    // Exibe filtro local (vindo do form_cobertura_animais) ao carregar o programa
    var filtro_local = $("#local_request").val(); 
 
    if (filtro_local!='' && filtro_local!=null) {
        $('#codigo_local option[value=' + filtro_local + ']').attr('selected', true);
        $('#codigo_local').selectpicker('refresh');
    }

    consultar();
})

function incluir_nova() { 
    $("#local").val('[]');
    $("#local").prop("disabled", false);
    $('#local').selectpicker('refresh');
    $("#atividade").val('0');
    $("#atividade").prop("disabled", false);
    $("#titulo_agenda").val('');
    document.getElementById("descricao_agenda").value='';
    $(".datas").val('');
    $("#dia_inteiro").prop("checked", true);
    $(".dia_todo").show();
    $(".data_hora").hide();
    $(".data").show();
    $(".confirma_gravar").attr("disabled", false);
    $('#modal_incluir .modal-title').html('Agenda - Incluir');
    $(".confirma_exclusao").hide();
    $('.confirma_gravar').html('Confirmar').removeClass('btn-danger').addClass('btn-primary');
    $("#tipo_gravacao").val(0);

    var filtro_local = $("#local_request").val(); 
 
    if (filtro_local!='' && filtro_local!=null) {
        $('#local option[value=' + filtro_local + ']').attr('selected', true);
        $('#local').selectpicker('refresh');
    }

    $('#modal_incluir').modal('show');
}

function voltar_protocolo() {
    location.href= "form_cobertura_animais.php";
}

function fechar_editar() {
    $(".confirma_gravar").attr("disabled", false);
    $('#mensagem_retorno_editar').modal('hide');
    $('#modal_incluir').modal('hide');
    consultar();
}

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
    $('.confirma_gravar').on('click', function() {
        $(this).prop({
            disabled: true,
            innerHTML: 'Aguarde...'
      });
    });

    $('#atividade').change(function(){

        select = document.getElementById('atividade');
        desc_titulo = select.options[select.selectedIndex].text+'-';

        $("#titulo_agenda").val(desc_titulo);
    });

    $('#tipo_agenda').change(function(){
        consultar();
    });

    $('#codigo_local').change(function(){
        consultar();
    });

    $("#dia_inteiro").click(function(){
        var tipo_gravacao = $("#tipo_gravacao").val();

        if (tipo_gravacao==1) {

            if ($("#dia_inteiro").is(":checked") == true){
                $(".data_hora").hide();
                $(".data").show();
            }
            else {
                $(".data_hora").show();
                $(".data").hide();
            }

        }
        else {
            $(".datas").val('');

            if ($("#dia_inteiro").is(":checked") == true){
                $(".data_hora").hide();
                $(".data").show();
            }
            else {
                $(".data_hora").show();
                $(".data").hide();
            }
        }
    });

});

function excluir_evento() {
    $("#tipo_gravacao").val(2);

    if(confirm("Confirma excluir este evento? Após excluir, o registro não poderá ser recuperado pelo sistema")){      
        gravar_evento();
    }
}

function gravar_evento() {
    var dados = $('#form_incluir').serialize();

    $.ajax({
        type: "POST",
        url: 'gravar_eventos_agenda_incluir.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $(".confirma_gravar").attr("disabled", false);
                var tipo_gravacao = $("#tipo_gravacao").val();

                if (tipo_gravacao==0) {
                    $('.confirma_gravar').html('Confirmar').removeClass('btn-danger').addClass('btn-primary');
                }
                else {
                    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');
                }

                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                var tipo_gravacao = $("#tipo_gravacao").val();

                if (tipo_gravacao==0) {
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
                else {
                    $("#mensagem_retorno_editar").modal();
                    $("#mensagem_retorno_editar .modal-body").html(data.message);
                }
            }
        }
    });
}

function consultar(){
    var tipoAgenda = $("#tipo_agenda").val();
    var local = $("#codigo_local").val();
    var atividade = 2;

    if(local == null){   
        local = '';
    }  

    startCalendar(tipoAgenda, local, atividade);
    $("#exibir_agenda").css({
        "background-color": "#ffffff", "padding-top": "15px", "padding-bottom": "15px"});
}

function startCalendar(option, l, atividade){
    var local = l;
    var source = [
        {
            url: "ler_eventos_agenda.php",
            method: "POST",
            extraParams: {
                "local" : local,
                "atividade" : atividade
            }
        }
    ];

    var tipo = "";

    if(option == 2){
        tipo = 'timeGridWeek'
    }else{
        tipo = 'dayGridMonth'
    }

    if(option == 1){
        var calendarEl = document.getElementById("calendar");
        var calendar = new FullCalendar.Calendar(calendarEl, {
            //initialView: 'listDay',
            initialView: 'dayGridDay',
            locale: 'pt-br',
            buttonText:{
              today:    'Hoje',
              month:    'Mês',
              week:     'Semana',
              day:      'Dia'
            },
            eventSources: source,
            eventClick: function(info){
                $("#idEvento").val(info.event.id);
                editar_evento();

                /*$("#tituloEvento").val(info.event.title);
                $("#descricaoEvento").html(info.event.extendedProps.description);
                var d = info.event.end;
                var inicio = [d.getFullYear(),
                  (d.getMonth()+1).AddZero(),
                  d.getDate().AddZero()].join('-')+'T'+
                   [d.getHours().AddZero(),
                   d.getMinutes().AddZero()].join(':');
                $("#dataHoraEvento").val(inicio);
                $("#modalEditarEvento").modal('show');*/
            },

            eventMouseEnter: function(info){
                info.el.style.cursor = 'pointer';
            }
        });
    }else{
        var calendarEl = document.getElementById("calendar");
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: tipo,
            locale: 'pt-br',
            buttonText:{
              today:    'Hoje',
              month:    'Mês',
              week:     'Semana',
              day:      'Dia'
            },
            eventSources: source,
            eventClick: function(info){
                $("#idEvento").val(info.event.id);
                editar_evento();
                /*$("#tituloEvento").val(info.event.title);
                var d = info.event.end;
                var inicio = [d.getFullYear(),
                    (d.getMonth()+1).AddZero(),
                    d.getDate().AddZero()].join('-')+'T'+
                   [d.getHours().AddZero(),
                    d.getMinutes().AddZero()].join(':');
                $("#dataHoraEvento").val(inicio);
                $("#descricaoEvento").html(info.event.extendedProps.description);
                $("#modalEditarEvento").modal('show');*/
            },

            eventMouseEnter: function(info){
                info.el.style.cursor = 'pointer';
            },
            editable: true,
            eventDrop: function(info){
                if(confirm("Tem certeza que deseja alterar a data deste evento?")){
                    var e = info.event;
                    //var d = info.event.start;
                    //var inicio = [d.getFullYear(),
                    //    (d.getMonth()+1).AddZero(),
                    //    d.getDate().AddZero()].join('-')+'T'+
                    //   [d.getHours().AddZero(),
                    //    d.getMinutes().AddZero()].join(':');
                    //editarEvento(e.id, e.title, inicio);
                    editarEvento(e.id, e.title, e.start);
                }
            }
        });
    }

    calendar.render();
}

// Editar/Excluir Evento
function editar_evento(){
    var id_evento = $("#idEvento").val();

    $.ajax({
        type: "POST",
        url: 'ler_eventos_agenda_editar.php',
        dataType: "json",
        data: {
                "id_evento": id_evento
            },
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else {
                $("#local").val('[]');
                $("#local").val(data.tbl_agenda_local);
                $("#local").prop("disabled", true);
                $('#local').selectpicker('refresh');

                $("#atividade").val(data.tbl_agenda_atividade_padrao);
                $("#atividade").prop("disabled", true);

                $("#titulo_agenda").val(data.tbl_agenda_titulo);
                document.getElementById("descricao_agenda").value=data.tbl_agenda_descricao;

                var data_inicio = data.tbl_agenda_data_inicial.split(" ");

                if (data_inicio[1]=='00:00:00') {
                    $("#data_agenda_inicio").val(data_inicio[0]);
                    $("#dia_inteiro").prop("checked", true);
                    $(".data_hora").hide();
                    $(".data").show();
                }
                else {
                    $("#data_hora_agenda_inicio").val(data_inicio[0]+' '+data_inicio[1]);
                    $("#dia_inteiro").prop("checked", false);
                    $(".data_hora").show();
                    $(".data").hide();
                }

                if (data.tbl_agenda_data_final!=null && data.tbl_agenda_data_final!=''){
                    var data_fim = data.tbl_agenda_data_final.split(" ");

                    if (data_fim[1]=='00:00:00') {
                        var data_final = subtrair_dia(data_fim[0]);
                        $("#data_agenda_fim").val(data_final);
                    }
                    else {
                        $("#data_hora_agenda_fim").val(data_fim[0]+' '+data_fim[1]);
                    }
                }
                else {
                    $("#data_agenda_fim").val('');
                    $("#data_hora_agenda_fim").val('');
                }

                $('#modal_incluir .modal-title').html('Agenda - Editar');
                $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');
                $(".confirma_exclusao").show();
                $("#tipo_gravacao").val(1);

                $("#modal_incluir").modal('show');
            }
        }
    });
}

function subtrair_dia(data_fim) {
    d = data_fim.split("-");
    ano = d[0];
    mes = d[1] - 1;
    dia = d[2];
    inicial = new Date(ano, mes, dia);
    milissegundos_por_dia = 1000 * 60 * 60 * 24;
    data_final = new Date(inicial.getTime() - 1 * milissegundos_por_dia);
    timestamp = data_final.getTime();
    data_final = new Date(timestamp);


    data_final = data_final.getFullYear()+'-'+(data_final.getMonth()+1).AddZero()+'-'+data_final.getDate().AddZero();
    return data_final;
}

function informacoes_uso() {
    $("#ajuda").modal();
}

Number.prototype.AddZero= function(b,c){
    var  l= (String(b|| 10).length - String(this).length)+1;
    return l> 0? new Array(l).join(c|| '0')+this : this;
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
