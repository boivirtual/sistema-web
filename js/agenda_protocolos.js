/**AGENDA*/
window.addEventListener("load", function (event) {
    consultar();
})

function incluir_nova() {
    $("#local").val('[]');
    $("#local").prop("disabled", false);
    $('#local').selectpicker('refresh');
    $("#atividade").prop("disabled", false);

    var $opcaoAtividadeUnica = $('#atividade option[value!="0"]');

    if ($opcaoAtividadeUnica.length === 1) {
        $("#atividade").val($opcaoAtividadeUnica.val());
        $("#titulo_agenda").val($opcaoAtividadeUnica.text().trim() + '-');
    }
    else {
        $("#atividade").val('0');
        $("#titulo_agenda").val('');
    }
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
    var local = $("#local_request").val();
    var estacao_monta_id = $("#estacao_monta_id_request").val();

    location.href= "form_cobertura_animais.php?local=" + local + "&estacao_monta_id=" + estacao_monta_id + "&voltar_protocolo=1";
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

    $(document).on('change', '.agenda-fazenda-check', function() {
        if (agendaCalendar) {
            agendaCalendar.refetchEvents();
        }
    });

    $(document).on('click', '#agenda_hoje', function() {
        if (agendaCalendar) {
            agendaCalendar.today();
        }
    });

    $(document).on('click', '#agenda_anterior', function() {
        if (agendaCalendar) {
            agendaCalendar.prev();
        }
    });

    $(document).on('click', '#agenda_proximo', function() {
        if (agendaCalendar) {
            agendaCalendar.next();
        }
    });

    $(document).on('click', '.agenda-view-btn', function() {
        $('.agenda-view-btn').removeClass('active');
        $(this).addClass('active');

        if (agendaCalendar) {
            agendaCalendar.changeView($(this).data('view'));
        }
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
    var id_evento = $("#idEvento").val();
    $("#modal_incluir").modal('hide');
    mostrarConfirmarExclusao(id_evento);
}

function excluirEventoDoPreview(){
    var id_evento = $("#idEventoPreview").val();
    $("#modalPreviewEvento").modal('hide');
    mostrarConfirmarExclusao(id_evento);
}

function formatarDataHoraBanco(dataStr){
    if (!dataStr) {
        return '';
    }

    var partes = dataStr.split(' ');
    var data = partes[0].split('-');
    var hora = partes[1];
    var dt = new Date(data[0], data[1] - 1, data[2]);
    var opcoesDia = { weekday: 'long', day: 'numeric', month: 'long' };
    var dataFormatada = dt.toLocaleDateString('pt-BR', opcoesDia);
    dataFormatada = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1);

    if (!hora || hora === '00:00:00') {
        return dataFormatada;
    }

    return dataFormatada + ' · ' + hora.substring(0, 5);
}

function mostrarConfirmarExclusao(id_evento){
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
                return;
            }

            $("#idEventoExclusao").val(id_evento);
            $("#exclusao_evento_titulo").text(data.tbl_agenda_titulo);
            $("#exclusao_evento_data").text(formatarDataHoraBanco(data.tbl_agenda_data_inicial));
            $("#exclusao_evento_atividade").text('Atividade: Reprodução');
            $("#exclusao_evento_descricao").text(data.tbl_agenda_descricao ? data.tbl_agenda_descricao : 'Sem descrição.');

            $("#modalConfirmarExclusao").modal('show');
        }
    });
}

function confirmarExclusaoEvento(){
    $("#idEvento").val($("#idEventoExclusao").val());
    $("#tipo_gravacao").val(2);
    $("#modalConfirmarExclusao").modal('hide');
    gravar_evento();
}

var _gravarEventoEmAndamento = false;

function gravar_evento() {
    if (_gravarEventoEmAndamento) return;
    _gravarEventoEmAndamento = true;
    $(".confirma_gravar").attr("disabled", true);

    var dados = $('#form_incluir').serialize();

    $.ajax({
        type: "POST",
        url: 'gravar_eventos_agenda_incluir.php',
        data: dados,
        success: function(data){
            _gravarEventoEmAndamento = false;
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
                $(".confirma_gravar").attr("disabled", false);
                var tipo_gravacao = $("#tipo_gravacao").val();

                $("#modal_incluir").modal('hide');

                if (tipo_gravacao==0) {
                    $("#mensagem_retorno").modal('show');
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
                else {
                    $("#mensagem_retorno_editar").modal('show');
                    $("#mensagem_retorno_editar .modal-body").html(data.message);
                }
            }
        },
        error: function(){
            _gravarEventoEmAndamento = false;
            $(".confirma_gravar").attr("disabled", false);
        }
    });
}

var agendaCalendar = null;

function consultar(){
    if (agendaCalendar) {
        agendaCalendar.refetchEvents();
    }
    else {
        iniciarCalendarioAgenda();
    }
}

function medirLarguraTextoIsolada(texto, elementoReferencia){
    var estiloRef = window.getComputedStyle(elementoReferencia);
    var temp = document.createElement('span');
    temp.style.cssText = 'position:absolute; visibility:hidden; white-space:nowrap; left:-9999px; top:-9999px;';
    temp.style.fontSize = estiloRef.fontSize;
    temp.style.fontFamily = estiloRef.fontFamily;
    temp.style.fontWeight = estiloRef.fontWeight;
    temp.style.letterSpacing = estiloRef.letterSpacing;
    temp.textContent = texto;
    document.body.appendChild(temp);
    var largura = temp.getBoundingClientRect().width;
    document.body.removeChild(temp);
    return largura;
}

function agendarAplicacaoTooltips(){
    aplicarTooltipsEventos();
    aplicarTooltipsColunasHora();
    setTimeout(function(){ aplicarTooltipsEventos(); aplicarTooltipsColunasHora(); }, 50);
    setTimeout(function(){ aplicarTooltipsEventos(); aplicarTooltipsColunasHora(); }, 200);
    setTimeout(function(){ aplicarTooltipsEventos(); aplicarTooltipsColunasHora(); }, 500);
}

function aplicarTooltipsColunasHora(){
    var hojeStr = formatarDataInputDate(new Date());

    document.querySelectorAll('.fc-timegrid-col[data-date]').forEach(function(col){
        var dataCol = col.getAttribute('data-date');
        configurarTooltipCliqueIncluir(col, dataCol >= hojeStr);
    });
}

$(document).on('hidden.bs.modal', '.modal', function(){
    $(this).find('[data-toggle="tooltip"], .agenda-tooltip-ancora, .fc-event-title').each(function(){
        if ($(this).data('bs.tooltip')) {
            $(this).tooltip('hide');
        }
    });

    $('.fc-daygrid-day, .fc-timegrid-col').each(function(){
        if ($(this).data('agendaTooltipCliqueVisivel')) {
            $(this).tooltip('hide');
            $(this).data('agendaTooltipCliqueVisivel', false);
        }
    });
});

function aplicarTooltipsEventos(){
    document.querySelectorAll('.fc-event').forEach(function(el){
        var tituloEl = el.querySelector('.fc-event-title');

        if (!tituloEl) {
            return;
        }

        var ancoraAntiga = tituloEl.querySelector('.agenda-tooltip-ancora');
        var $alvoAntigo = ancoraAntiga ? $(ancoraAntiga) : $(tituloEl);

        if ($alvoAntigo.data('bs.tooltip')) {
            $alvoAntigo.tooltip('destroy');
        }

        if (ancoraAntiga) {
            ancoraAntiga.remove();
        }

        var tituloEvento = tituloEl.textContent;
        var larguraTextoReal = medirLarguraTextoIsolada(tituloEvento, tituloEl);
        var larguraDisponivel = tituloEl.getBoundingClientRect().width;

        var cortado = larguraTextoReal > larguraDisponivel + 1;
        var textoTooltip = cortado ? (tituloEvento + '\nClique para mais informações') : 'Clique para mais informações';
        var alvo = tituloEl;

        if (!cortado) {
            var ancora = document.createElement('span');
            ancora.className = 'agenda-tooltip-ancora';
            ancora.style.cssText = 'position:absolute; left:0; top:0; width:' + Math.min(larguraTextoReal, larguraDisponivel) + 'px; height:100%;';

            if (window.getComputedStyle(tituloEl).position === 'static') {
                tituloEl.style.position = 'relative';
            }

            tituloEl.appendChild(ancora);
            alvo = ancora;
        }

        $(alvo).attr('title', textoTooltip);
        $(alvo).tooltip({ placement: 'top', container: 'body' });
    });
}

function formatarDataPreviewEvento(evento){
    var opcoesDia = { weekday: 'long', day: 'numeric', month: 'long' };
    var dataFormatada = evento.start.toLocaleDateString('pt-BR', opcoesDia);
    dataFormatada = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1);

    if (evento.allDay) {
        return dataFormatada;
    }

    var horaInicio = evento.start.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
    var horaFim = evento.end ? evento.end.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'}) : '';

    return dataFormatada + ' · ' + horaInicio + (horaFim ? ' - ' + horaFim : '');
}

function formatarDataInputDate(data){
    return data.getFullYear() + '-' + (data.getMonth() + 1).AddZero() + '-' + data.getDate().AddZero();
}

function formatarDataInputDateTime(data){
    return formatarDataInputDate(data) + 'T' + data.getHours().AddZero() + ':' + data.getMinutes().AddZero();
}

function tratarCliqueData(info){
    var agora = new Date();

    if (info.allDay) {
        var hojeSemHora = new Date(agora.getFullYear(), agora.getMonth(), agora.getDate());

        if (info.date < hojeSemHora) {
            return;
        }
    }
    else if (info.date < agora) {
        return;
    }

    incluir_nova();

    if (info.allDay) {
        $("#dia_inteiro").prop("checked", true);
        $(".data_hora").hide();
        $(".data").show();
        $("#data_agenda_inicio").val(formatarDataInputDate(info.date));
    }
    else {
        $("#dia_inteiro").prop("checked", false);
        $(".data_hora").show();
        $(".data").hide();
        $("#data_hora_agenda_inicio").val(formatarDataInputDateTime(info.date));
    }
}

function aplicarTooltipCelula(el, data, ehDiaTodo){
    var agora = new Date();
    var valido;

    if (ehDiaTodo) {
        var hojeSemHora = new Date(agora.getFullYear(), agora.getMonth(), agora.getDate());
        valido = data >= hojeSemHora;
    }
    else {
        valido = data >= agora;
    }

    configurarTooltipCliqueIncluir(el, valido);
}

function esconderTooltipsCliqueIncluir(){
    $('.fc-daygrid-day, .fc-timegrid-col').each(function(){
        if ($(this).data('agendaTooltipCliqueVisivel')) {
            $(this).tooltip('hide');
            $(this).data('agendaTooltipCliqueVisivel', false);
        }
    });
}

function configurarTooltipCliqueIncluir(el, valido){
    var $el = $(el);

    if (!valido) {
        el.style.cursor = '';

        if ($el.data('agendaTooltipCliqueConfigurado')) {
            $el.tooltip('destroy');
            $el.off('.agendaTooltipClique');
            $el.removeData('agendaTooltipCliqueConfigurado');
            $el.removeData('agendaTooltipCliqueVisivel');
        }

        return;
    }

    el.style.cursor = 'pointer';

    if ($el.data('agendaTooltipCliqueConfigurado')) {
        return;
    }

    $el.attr('title', 'Clique para incluir um evento').tooltip({ placement: 'top', container: 'body', trigger: 'manual' });
    $el.data('agendaTooltipCliqueConfigurado', true);
    $el.data('agendaTooltipCliqueVisivel', false);

    $el.on('mousemove.agendaTooltipClique', function(e){
        var sobreEvento = !!$(e.target).closest('.fc-event').length;
        var visivel = $el.data('agendaTooltipCliqueVisivel');

        if (sobreEvento && visivel) {
            $el.tooltip('hide');
            $el.data('agendaTooltipCliqueVisivel', false);
        }
        else if (!sobreEvento && !visivel) {
            $el.tooltip('show');
            $el.data('agendaTooltipCliqueVisivel', true);
        }
    });

    $el.on('mouseleave.agendaTooltipClique', function(){
        if ($el.data('agendaTooltipCliqueVisivel')) {
            $el.tooltip('hide');
            $el.data('agendaTooltipCliqueVisivel', false);
        }
    });
}

function mostrarPreviewEvento(evento){
    var id_evento = evento.id;

    $("#idEventoPreview").val(id_evento);
    $("#preview_evento_titulo").text(evento.title);
    $("#preview_evento_data").text(formatarDataPreviewEvento(evento));
    $("#preview_evento_cor").css('background', evento.backgroundColor || evento.borderColor || '#378ADD');
    $("#preview_evento_descricao").text('Carregando...');
    $("#modalPreviewEvento").modal('show');

    $.ajax({
        type: "POST",
        url: 'ler_eventos_agenda_editar.php',
        dataType: "json",
        data: {
                "id_evento": id_evento
            },
        success: function(data){
            if (data.error) {
                $("#modalPreviewEvento").modal('hide');
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else {
                $("#preview_evento_descricao").text(data.tbl_agenda_descricao ? data.tbl_agenda_descricao : 'Sem descrição.');
            }
        }
    });
}

function editarEventoDoPreview(){
    $("#modalPreviewEvento").modal('hide');
    $("#idEvento").val($("#idEventoPreview").val());
    editar_evento();
}

function iniciarCalendarioAgenda(){
    var calendarEl = document.getElementById("calendar");

    agendaCalendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'pt-br',
        headerToolbar: false,
        height: 'auto',
        allDayText: 'Dia todo',
        nowIndicator: true,
        dayMaxEvents: false,
        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        editable: true,
        eventSources: [
            {
                events: function(fetchInfo, successCallback, failureCallback) {
                    var locais = $('.agenda-fazenda-check:checked').map(function(){ return this.value; }).get();

                    if (locais.length === 0) {
                        successCallback([]);
                        return;
                    }

                    $.post('ler_eventos_agenda.php', {
                        local: locais.join(','),
                        atividade: '2'
                    }, function(data){
                        successCallback(data);
                    }, 'json').fail(function(){
                        failureCallback();
                    });
                }
            }
        ],
        eventClick: function(info){
            mostrarPreviewEvento(info.event);
        },
        dateClick: function(info){
            tratarCliqueData(info);
        },
        dayCellDidMount: function(arg){
            aplicarTooltipCelula(arg.el, arg.date, true);
        },
        eventMouseEnter: function(info){
            info.el.style.cursor = 'pointer';
        },
        eventDrop: function(info){
            if(confirm("Tem certeza que deseja alterar a data deste evento?")){
                var e = info.event;
                editarEvento(e.id, e.title, e.start);
            }
        },
        datesSet: function(info){
            $('#agenda_periodo_titulo').text(info.view.title);
            agendarAplicacaoTooltips();
        },
        eventsSet: function(events){
            agendarAplicacaoTooltips();
        }
    });

    agendaCalendar.render();

    ajustarAlturaCalendario();

    $(window).off('resize.agendaAltura').on('resize.agendaAltura', function(){
        ajustarAlturaCalendario();
    });

    $('#main-content').off('scroll.agendaPopover').on('scroll.agendaPopover', function(){
        $('.fc-popover').remove();
    });
}

function ajustarAlturaCalendario(){
    var $area = $('.agenda-calendar-area');

    if (!$area.length) {
        return;
    }

    var topo = $area.offset().top - $('#main-content').offset().top + $('#main-content').scrollTop();
    var alturaDisponivel = $('#main-content').height() - topo - 20;

    if (alturaDisponivel < 300) {
        alturaDisponivel = 300;
    }

    $area.css('max-height', alturaDisponivel + 'px');

    if (agendaCalendar) {
        agendaCalendar.updateSize();
    }
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
                $('#local').selectpicker('refresh');

                $("#atividade").val(data.tbl_agenda_atividade_padrao);

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
