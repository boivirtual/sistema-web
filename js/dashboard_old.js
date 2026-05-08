/**DASHBOARD*/
window.addEventListener("load", function(event) {
    var controle_estoque = $("#tipo_controle_estoque").val();
    var abrir_agenda = $("#abrir_agenda").val();

    if (controle_estoque=='I') {
        $.ajax({
            type: 'post',
            url: 'ajustar_categoria_animal_pasto.php',
            data: {},
            success: function(data) {
                if (data.error) { 
                    alert (data.message);
                }               
            }
        });
    }

    $("#data_atual").hide();

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

    $.post("lista_local.php", {tipo:0}, function(valor){
        $("select[name=codigo_local_chuva]").html(valor);
    });

    $.post("lista_local.php", {tipo:1}, function(valor){
        $("select[name=codigo_local]").html(valor);
        $("select[name=codigo_local_agenda]").html(valor);
    });

    var aceite_termos = $("#aceite_por").val();

    if (aceite_termos!='') {
        $(".gravar_termo_uso").hide();
        $("#aceite_termos").prop('disabled', true);
    }
    else {
        $(".gravar_termo_uso").show();
        $("#aceite_termos").attr('checked', false);
        $("#aceite_termos").prop('disabled', false);
        $('#termo').modal({backdrop: 'static', keyboard: false});
    }

    consultar_fazenda();

    if (abrir_agenda=='') {
        consultar_agenda();
    }

});        

$(document).ready(function(){
    $('#codigo_local_chuva').change(function(event) {
        mostrar_dias_chuva();
    });

    $('#codigo_local_agenda').change(function(event) {
        consultar_agenda();
    });

});

function incluir_nova() { 
    $("#local").val('[]');
    $("#local").prop("disabled", false);
    $('#local').selectpicker('refresh');
    $("#atividade").val('0');
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
    $('.confirma_gravar').show();
    $("#tipo_gravacao").val(0);

    $("#atividade").prop("disabled", false);
    $("#titulo_agenda").prop("disabled", false);
    $("#descricao_agenda").prop("disabled", false);
    $("#dia_inteiro").prop("disabled", false);
    $("#data_agenda_inicio").prop("disabled", false);
    $("#data_agenda_fim").prop("disabled", false);
    $("#data_hora_agenda_inicio").prop("disabled", false);
    $("#data_hora_agenda_fim").prop("disabled", false);

    $('#modal_incluir').modal('show');
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

    $("#dia_inteiro").click(function(){
        $(".datas").val('');

        if ($("#dia_inteiro").is(":checked") == true){
            $(".data_hora").hide();
            $(".data").show();

        }
        else {
            $(".data_hora").show();
            $(".data").hide();
        }
    });

});

function gravar_evento() {
    var dados = $('#form_incluir').serialize();
    $.ajax({
        type: "POST",
        url: 'gravar_eventos_agenda_incluir.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                consultar_agenda();
                $('#modal_incluir').modal('hide');
                $("#mensagem_retorno_agenda").modal();
                $("#mensagem_retorno_agenda .modal-body").html(data.message);
            }
        }
    });
}

function consultar_fazenda() {
    var local = $("#codigo_local").val();
    $("#codigo_local_chuva").val(local).change();

    $.post("ler_animais_local_dashboard.php",{local: local}, function(valor){
        var php = valor.split("<|>");

        if (php[6]=='S') {
            $("#transferencia").show();
        }
        else {
            $("#transferencia").hide();
        }

        $("#qtd_animais").text(php[0]);
        var quantidade_animais = php[0];

        var desc_cat = php[1].split("|");
        var total_cat = php[2].split("|");
        var perc_cat = php[3].split("|");
        var total_m = php[4].split("|");
        var total_f = php[5].split("|");
        var numero_itens = desc_cat.length;

        html = "";
        html += '<table class="table table-advance" id="tabela_categorias"';
        html += '<thead>';
        html += '<tr>';
        html += '<th>' + ' Idade' + '</th>';
        html += '<th style="text-align: right;">' + ' Macho' + '</th>';
        html += '<th style="text-align: right;">' + ' Fêmea' + '</th>';
        html += '<th style="text-align: right;">' + ' Total' + '</th>';
        html += '<th style="text-align: right;">' + ' % do total' + '</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';

        for (var i = 0; i < numero_itens; i++) {
            var descricao = desc_cat[i];
            var total = total_cat[i];
            var macho = total_m[i];
            var femea = total_f[i];

            var percentual = (total * 100) / quantidade_animais;
            percentual = formatMoney(percentual);

            if (descricao!='') {
                html += '<tr>';
                html += '<td>' + descricao + '</td>';
                html += '<td style="text-align: right;">' + macho + '</td>';
                html += '<td style="text-align: right;">' + femea + '</td>';
                html += '<td style="text-align: right;">' + total + '</td>';
                html += '<td style="text-align: right;">' + percentual + '%</td>';
                html += '</tr>';
            }
        }

        html += '</tbody>';
        html += '</table>';
        document.getElementById('tabela_categorias').innerHTML = html;

        if (window.matchMedia("(max-width: 600px)").matches) {
            setTimeout(function() { 
                var alturaDivTxt2 = 150;
                $(".mapa_gado").css({ "height": +alturaDivTxt2+"px" });
            }, 600);
        }
   });

// MONDA MAPA DE GADO
    $.post("ler_mapa_gado_dashboard.php",{local: local}, function(valor){
        var php = valor.split("<|>");
       
        var fazenda_id = php[0].split("|");
        var desc_fazenda = php[1].split("|");
        var total_fazenda = php[2].split("|");
        var ultima_data = php[3].split("|");
        var numero_itens = fazenda_id.length;

        html = "";
        html += '<table class="table table-advance table-hover" id="tabela_mapa" style="font-size: 12px;"';
        html += '<thead>';
        html += '<tr>';
        html += '<th>' + ' Local' + '</th>';
        html += '<th style="text-align: center;">' + ' Última Atualização' + '</th>';
        html += '<th style="text-align: right;">' + ' Total de Animais' + '</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';

        for (var i = 0; i < numero_itens; i++) {
            var id = fazenda_id[i];
            var descricao = desc_fazenda[i];
            var data = ultima_data[i].substr(0, 10);
            var hora = ultima_data[i].substr(11, 5);
            var data = data.split("-");
            var ano = data[0];
            var mes = data[1];
            var dia = data[2];
            var data_edi = dia+'/'+mes+'/'+ano+' '+hora;
            var total = total_fazenda[i];

            if (descricao!='') {
                html += '<tr id='+id+' style="cursor: pointer;" onclick="abrir_mapa_gados(this.id)">';
                html += '<td>'+ descricao +'</td>';
                html += '<td style="text-align: center;">'+ data_edi +'</td>';
                html += '<td style="text-align: right;">'+ total +'</td>';
                html += '</tr>';
            }
        }

        html += '</tbody>';
        html += '</table>';
        document.getElementById('tabela_mapa').innerHTML = html;
   });
}

function consultar_agenda(){
    var local = $("#codigo_local_agenda").val();

    //$("#agenda").modal('show');
    startCalendar(local);
}

function startCalendar(local){
    var source = [
        {
            url: "ler_eventos_agenda_dashboard.php",
            method: "POST",
            extraParams: {
                "local" : local
            }
        }
    ];

    var tipo = 'dayGridMonth'

    var calendarEl = document.getElementById("calendar");

    var calendar = new FullCalendar.Calendar(calendarEl, {
        contentHeight: 200,
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
            /*$("#descricaoEvento").val(info.event.title);
            var d = info.event.start;
            var inicio = [d.getFullYear(),
                (d.getMonth()+1).AddZero(),
                d.getDate().AddZero()].join('-')+'T'+
               [d.getHours().AddZero(),
                d.getMinutes().AddZero()].join(':');
            $("#dataHoraEvento").val(inicio);
            $("#modalEditarEvento").modal('show');*/
            editar_evento();

        },
        eventMouseEnter: function(info){
            info.el.style.cursor = 'pointer';
        }
    });
    
    $("#agenda").modal(open).show();    

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

                $("#atividade").prop("disabled", true);
                $("#titulo_agenda").prop("disabled", true);
                $("#descricao_agenda").prop("disabled", true);
                $("#dia_inteiro").prop("disabled", true);
                $("#data_agenda_inicio").prop("disabled", true);
                $("#data_agenda_fim").prop("disabled", true);
                $("#data_hora_agenda_inicio").prop("disabled", true);
                $("#data_hora_agenda_fim").prop("disabled", true);

                $('#modal_incluir .modal-title').html('Agenda');
                $('.confirma_gravar').hide();
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

function abrir_mapa_gados(id){
    $.redirect('form_mapa_gados.php', {'mapa_local_id': id});
}
/*
function verificar_gravar_chuva() {
    let local = $("#codigo_local_chuva").val();
    let data_chuva = $("#data_chuva").val();
    let volume = $("#volume_chuva").val();
    let bd = $("#bd").val();

    $.ajax({
        type: "GET",
        url: 'api/rest/chuva/volume',
        data: {
            'local': local,
            'data': data_chuva,
            'volume': volume,
            'bd': bd
        },
        success: function(data){
            let r = JSON.parse(data);
            if (r.success) {
                gravar_chuva();
            }else if(r.error){
                alert(r.message);
            }else{
                if (confirm(`Volume de chuva já cadastrado para essa data. Volume cadastrado: ${r.volume}mm. Deseja alterar para o volume atual?`))
                    gravar_chuva();
            }
        }
    });
}

function gravar_chuva() {
    var local = $("#codigo_local_chuva").val();
    var dados = $('#form_volume_chuva').serialize();
    $.ajax({
        type: "POST",
        url: 'api/rest/chuva/create',
        data: dados,
        success: function(data){
            let r = JSON.parse(data);
            alert (r.message);
            if (!r.error){
                $("#codigo_local_chuva").val(local);
                $("#volume_chuva").val('');
                $("#data_chuva").val($("#data_atual").val());
                mostrar_dias_chuva();
            }
        }
    });
}

function mostrar_dias_chuva() {
    var local = $("#codigo_local_chuva").val();
    let bd = $("#bd").val();
    $.ajax({
        type: "GET",
        url: "api/rest/chuva/dia",
        data: {
            'local': local,
            'bd': bd
        },
        success: function(data){
            let r = JSON.parse(data);
            if(r.error){
                $(".mes_atual").text('');
                $(".dias_chuva").text('');
                $(".mm_mes").text('');
                $(".mm_ano").text('');
            }else{
                $(".mes_atual").text(r.mes);
                $(".dias_chuva").text(r.diasChuva);
                $(".mm_mes").text(r.volumeMes);
                $(".mm_ano").text(r.volumeAno);
            }
        }
    });
}
*/

function verificar_gravar_chuva() {
    var local = $("#codigo_local_chuva").val();
    var volume_chuva = $("#volume_chuva").val();
    var data_chuva = $("#data_chuva").val();

    if (data_chuva==''){
        alert ('Informe a Data.');
        return;
    }

    if (local=='000000000'){
        alert ('Informe o Local.');
        return;
    }

    if (volume_chuva==''){
        alert ('Informe o Volume.');
        return;
    }

    $.ajax({
        type: "POST",
        url: 'ler_volume_chuva.php',
        data: {
            'local_id': local,
            'data_chuva': data_chuva
        },
        success: function(data){
            if (data.error) {
                if (window.confirm(data.message)) {     
                    gravar_chuva();
                }
            }
            else if (data.success){
                gravar_chuva();
            }
        }
    });
}

function gravar_chuva() {
    var local = $("#codigo_local_chuva").val();
    var dados = $('#form_volume_chuva').serialize();
    $.ajax({
        type: "POST",
        url: 'gravar_volume_chuva.php',
        data: dados,
        success: function(data){
            if (data.error) {
                alert (data.message);
            }
            else if (data.success){
                alert (data.message);
                $("#codigo_local_chuva").val(local);
                $("#volume_chuva").val('');
                $("#data_chuva").val($("#data_atual").val());
                mostrar_dias_chuva();
            }
        }
    });
}

function mostrar_dias_chuva() {
    var local = $("#codigo_local_chuva").val();
    $.ajax({
        type: "POST",
        url: 'ler_dias_chuva.php',
        data: {
            'local_id': local
        },
        success: function(data){
            if (data.success) {
                $(".mes_atual").text(data.mes_extenco);
                $(".dias_chuva").text(data.dias_chuva);
                $(".mm_mes").text(data.volume_mes);
                $(".mm_ano").text(data.volume_ano);
            }
            else {
                $(".mes_atual").text('');
                $(".dias_chuva").text('');
                $(".mm_mes").text('');
                $(".mm_ano").text('');
            }
        }
    });
}

function gravar_termo_uso() {
    var concordo = $("input[name='aceite_termos']:checked").val();

    if (concordo==undefined) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Confirme a leitura dos Termos e condições gerais de uso do Software Boi Virtual.');
        return;
    }

    var dados = $('#form_aceite_termos').serialize();
    $.ajax({
        type: "POST",
        url: 'gravar_termo_uso.php',
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

function sair_sem_gravar_termo_uso(){
    var concordo = $("input[name='aceite_termos']:checked").val();

    if (concordo==undefined) {
        location.href= "../index.php";    
    }
    else {
        location.href= "menu.php"; 
    }

}
// RELATORIO
function listar_chuvas_dashboad(){
    var local = $("#codigo_local_chuva").val();
    var ano = new Date().getFullYear();

    if (local == '000000000') {
        alert ('Informe o Local');
        return;
    }

    var options = $('#codigo_local_chuva option:selected');
    var local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_local_chuva').text();
        local_filtro.push( desc.trim() );
    });

    var descricao_filtro = local_filtro+'->'+ano;

    location.href='form_lista_registro_chuva_rel_dashboard.php?ano='+ano+'&local='+local+'&descricao_filtro='+descricao_filtro;
}

function listar_chuvas(){
    var local = $("#codigo_local_filtro").val();
    var ano = $("#ano").val();

    if (local == '000000000') {
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

    var descricao_filtro = local_filtro+'->'+ano;

    location.href='form_lista_registro_chuva_rel.php?ano='+ano+'&local='+local+'&descricao_filtro='+descricao_filtro;
}

function lista_chuvas_excel(){
    var local = $("#local").val();
    var ano = $("#ano").val();
    var descricao_filtro = $("#descricao_filtro").val();

    if (local == '000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Local.');
        return;
    }

    $("#aguardar").modal();

    location.href='rel_registro_chuva_excel.php?descricao_filtro=' + descricao_filtro + '&ano=' + ano +
    '&local=' + local;

    tout = setTimeout('limpar_tela()', 5000);

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

function voltar_relatorios() {
    location.href='form_relatorios_produtivos.php';
}

function voltar_painel() {
    location.href='menu.php';
}

function voltar_filtro() {
    location.href='form_rel_registro_chuvas.php';
}

// FIM RELATORIO

function termo_uso_software() {
    $("#termos_uso").modal();
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

function formatMoney(n, c, d, t) {
  c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
  return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

// painel
$(window).resize(function() {
    if (window.innerWidth <= 424) 
        $("ol.breadcrumb li#painel").hide();
    else 
        $("ol.breadcrumb li#painel").show();
});

$(document).ready(function() {
    if (window.innerWidth <= 424) 
        $("ol.breadcrumb li#painel").hide();
    else 
        $("ol.breadcrumb li#painel").show();
});

// breadcrumb
$(window).resize(function() {
    if (window.innerWidth <= 424) 
        $("ol.breadcrumb").css({"width": "100%", "text-align": "center"});
    else 
        $("ol.breadcrumb").css({"width": "100%", "text-align": "left"});
});

$(document).ready(function() {
    if (window.innerWidth <= 339) 
        $("ol.breadcrumb").css({"width": "100%", "text-align": "center"});
    else 
        $("ol.breadcrumb").css({"width": "100%", "text-align": "left"});
});

// label fazendas
$(window).resize(function() {
    if (window.innerWidth <= 339) 
        $("ol.breadcrumb li#fazendas-select label").hide();
    else 
        $("ol.breadcrumb li#fazendas-select label").show();
});

$(document).ready(function() {
    if (window.innerWidth <= 339) 
        $("ol.breadcrumb li#fazendas-select label").hide();
    else 
        $("ol.breadcrumb li#fazendas-select label").show();
});

$(document).ready(function(){
    if(window.innerWidth <= 420){
        $("div.categorias div.card.horizontal").addClass("table-responsive");
    }else{
        $("div.categorias div.card.horizontal").removeClass("table-responsive");
    }
});

$(window).resize(function(){
    if(window.innerWidth <= 420){
        $("div.categorias div.card.horizontal").addClass("table-responsive");
    }else{
        $("div.categorias div.card.horizontal").removeClass("table-responsive");
    }
});

$(document).ready(function(){
    if(window.innerWidth <= 320){
        $("div.animais div.card.horizontal").addClass("table-responsive");
    }else{
        $("div.animais div.card.horizontal").removeClass("table-responsive");
    }
});

$(window).resize(function(){
    if(window.innerWidth <= 320){
        $("div.animais div.card.horizontal").addClass("table-responsive");
    }else{
        $("div.animais div.card.horizontal").removeClass("table-responsive");
    }
});