/**TABELA DE ANIMAIS*/
window.addEventListener("load", function(){
    listar_protocoloiatf();
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
    $('#tabela_protocolo').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": false,
        "info":     true,
        "language": {
        //"order": [[ 0, "desc" ], [ 1, 'desc' ]],
     // "oPaginate": {
     //   "sFirst": "Primeira",
     //   "sLast": "Última",
     //   "sNext": "Próxima",
     //   "sPrevious": "Anterior"
    //},
        "sSearch": "Busca:",
       // "lengthMenu": "Mostrando _MENU_ registros por página",
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
    $("#enviar_lixeira_1").hide();
    $("#enviar_lixeira_2").hide();
    $("#enviar_lixeira_3").hide();
    $("#enviar_lixeira_4").hide();
});

function listar_protocoloiatf(){
    
    var local = $("#codigo_local_filtro").val();

    if (local==null) {
        local=[''];
    }
    $.post("form_lista_protocolo_IATF.php", {local:local},
        function(valor){ $("div[id=lista_protocolos]").html(valor); 
    });
}

function mostrar_dias(id){
    if(id == "mais_med_0_N" && $("#numero_dias_0").val() != '' && $("#numero_dias_0").val() != 0){
        $("#lbl_1").text("Dia " + $("#numero_dias_0").val());
        $("#descricao_1").val("Dia " + $("#numero_dias_0").val());
        $("#div_1").show();
        $("#hr_1").show();
    }else if(id == "mais_med_0_1_N" && $("#numero_dias_0").val() != '' && $("#numero_dias_0").val() != 0){
        $("#lbl_1").text("Dia " + $("#numero_dias_0").val());
        $("#descricao_1").val("Dia " + $("#numero_dias_0").val());
        $("#div_1").show();
        $("#hr_1").show();
    }else if(id == "mais_med_0_2_N" && $("#numero_dias_0").val() != '' && $("#numero_dias_0").val() != 0){
        $("#lbl_1").text("Dia " + $("#numero_dias_0").val());
        $("#descricao_1").val("Dia " + $("#numero_dias_0").val());
        $("#div_1").show();
        $("#hr_1").show();
    }else if(id == "mais_med_0_3_N" && $("#numero_dias_0").val() != '' && $("#numero_dias_0").val() != 0){
        $("#lbl_1").text("Dia " + $("#numero_dias_0").val());
        $("#descricao_1").val("Dia " + $("#numero_dias_0").val());
        $("#div_1").show();
        $("#hr_1").show();
    }else if(id == "mais_med_1_N" && $("#numero_dias_1").val() != '' && $("#numero_dias_1").val() != 0){
        $("#lbl_2").text("Dia " + $("#numero_dias_1").val());
        $("#descricao_2").val("Dia " + $("#numero_dias_1").val());
        $("#div_2").show();
        $("#hr_2").show();
    }else if(id == "mais_med_1_1_N" && $("#numero_dias_1").val() != '' && $("#numero_dias_1").val() != 0){
        $("#lbl_2").text("Dia " + $("#numero_dias_1").val());
        $("#descricao_2").val("Dia " + $("#numero_dias_1").val());
        $("#div_2").show();
        $("#hr_2").show();
    }else if(id == "mais_med_1_2_N" && $("#numero_dias_1").val() != '' && $("#numero_dias_1").val() != 0){
        $("#lbl_2").text("Dia " + $("#numero_dias_1").val());
        $("#descricao_2").val("Dia " + $("#numero_dias_1").val());
        $("#div_2").show();
        $("#hr_2").show();
    }else if(id == "mais_med_1_3_N" && $("#numero_dias_1").val() != '' && $("#numero_dias_1").val() != 0){
        $("#lbl_2").text("Dia " + $("#numero_dias_1").val());
        $("#descricao_2").val("Dia " + $("#numero_dias_1").val());
        $("#div_2").show();
        $("#hr_2").show();
    }else if(id == "mais_med_2_N" && $("#numero_dias_2").val() != '' && $("#numero_dias_2").val() != 0){
        $("#lbl_3").text("Dia " + $("#numero_dias_2").val());
        $("#descricao_3").val("Dia " + $("#numero_dias_2").val());
        $("#div_3").show();
        $("#hr_3").show();
    }else if(id == "mais_med_2_1_N" && $("#numero_dias_2").val() != '' && $("#numero_dias_2").val() != 0){
        $("#lbl_3").text("Dia " + $("#numero_dias_2").val());
        $("#descricao_3").val("Dia " + $("#numero_dias_2").val());
        $("#div_3").show();
        $("#hr_3").show();
    }else if(id == "mais_med_2_2_N" && $("#numero_dias_2").val() != '' && $("#numero_dias_2").val() != 0){
        $("#lbl_3").text("Dia " + $("#numero_dias_2").val());
        $("#descricao_3").val("Dia " + $("#numero_dias_2").val());
        $("#div_3").show();
        $("#hr_3").show();
    }else if(id == "mais_med_2_3_N" && $("#numero_dias_2").val() != '' && $("#numero_dias_2").val() != 0){
        $("#lbl_3").text("Dia " + $("#numero_dias_2").val());
        $("#descricao_3").val("Dia " + $("#numero_dias_2").val());
        $("#div_3").show();
        $("#hr_3").show();
    }else if(id == "mais_med_3_N" && $("#numero_dias_3").val() != '' && $("#numero_dias_3").val() != 0){
        $("#lbl_4").text("Dia " + $("#numero_dias_3").val());
        $("#descricao_4").val("Dia " + $("#numero_dias_3").val());
        $("#div_4").show();
        $("#hr_4").show();
    }else if(id == "mais_med_3_1_N" && $("#numero_dias_3").val() != '' && $("#numero_dias_3").val() != 0){
        $("#lbl_4").text("Dia " + $("#numero_dias_3").val());
        $("#descricao_4").val("Dia " + $("#numero_dias_3").val());
        $("#div_4").show();
        $("#hr_4").show();
    }else if(id == "mais_med_3_2_N" && $("#numero_dias_3").val() != '' && $("#numero_dias_3").val() != 0){
        $("#lbl_4").text("Dia " + $("#numero_dias_3").val());
        $("#descricao_4").val("Dia " + $("#numero_dias_3").val());
        $("#div_4").show();
        $("#hr_4").show();
    }else if(id == "mais_med_3_3_N" && $("#numero_dias_3").val() != '' && $("#numero_dias_3").val() != 0){
        $("#lbl_4").text("Dia " + $("#numero_dias_3").val());
        $("#descricao_4").val("Dia " + $("#numero_dias_3").val());
        $("#div_4").show();
        $("#hr_4").show();
    }
}

function mostrar_linhas(id, valor){
    if((valor != '' && valor == 0) || valor == ''){
        if(id == "numero_dias_0"){
            $("#lbl_1").text("Dia X1");
            $("#div_1").hide();
            $("#hr_1").hide();
            $("#div_1 input[type='text']").val("");
            $("#div_1 input[type='number']").val("");
            $("#div_1 input[type='radio']").attr("checked", false);
            $("#div_1_linha_1").hide();
            $("#div_1_linha_1 input[type='text']").val("");
            $("#div_1_linha_1 input[type='number']").val("");
            $("#div_1_linha_1 input[type='radio']").attr("checked", false);
            $("#div_1_linha_2").hide();
            $("#div_1_linha_2 input[type='text']").val("");
            $("#div_1_linha_2 input[type='number']").val("");
            $("#div_1_linha_2 input[type='radio']").attr("checked", false);
            $("#div_1_linha_3").hide();
            $("#div_1_linha_3 input[type='text']").val("");
            $("#div_1_linha_3 input[type='number']").val("");
            $("#descricao_1").val("");
            $("#codigo_item_1").val('');
            $("#ler_1").hide();
            $("#ler_1_1").hide();
            $("#ler_1_2").hide();
            $("#ler_1_2").hide();
            $("#ler_1 span").text("");
            $("#ler_1_1 span").text("");
            $("#ler_1_2 span").text("");
            $("#ler_1_3 span").text("");
            $("#r1").hide();
            //div2
            $("#lbl_2").text("Dia X2");
            $("#div_2").hide();
            $("#hr_2").hide();
            $("#div_2 input[type='text']").val("");
            $("#div_2 input[type='number']").val("");
            $("#div_2 input[type='radio']").attr("checked", false);
            $("#div_2_linha_1").hide();
            $("#div_2_linha_1 input[type='text']").val("");
            $("#div_2_linha_1 input[type='number']").val("");
            $("#div_2_linha_1 input[type='radio']").attr("checked", false);
            $("#div_2_linha_2").hide();
            $("#div_2_linha_2 input[type='text']").val("");
            $("#div_2_linha_2 input[type='number']").val("");
            $("#div_2_linha_2 input[type='radio']").attr("checked", false);
            $("#div_2_linha_3").hide();
            $("#div_2_linha_3 input[type='text']").val("");
            $("#div_2_linha_3 input[type='number']").val("");
            $("#descricao_2").val("");
            $("#codigo_item_2").val('');
            $("#ler_2").hide();
            $("#ler_2_1").hide();
            $("#ler_2_2").hide();
            $("#ler_2_2").hide();
            $("#ler_2 span").text("");
            $("#ler_2_1 span").text("");
            $("#ler_2_2 span").text("");
            $("#ler_2_3 span").text("");
            $("#r2").hide();
            //div3
            $("#lbl_3").text("Dia X3");
            $("#div_3").hide();
            $("#hr_3").hide();
            $("#div_3 input[type='text']").val("");
            $("#div_3 input[type='number']").val("");
            $("#div_3 input[type='radio']").attr("checked", false);
            $("#div_3_linha_1").hide();
            $("#div_3_linha_1 input[type='text']").val("");
            $("#div_3_linha_1 input[type='number']").val("");
            $("#div_3_linha_1 input[type='radio']").attr("checked", false);
            $("#div_3_linha_2").hide();
            $("#div_3_linha_2 input[type='text']").val("");
            $("#div_3_linha_2 input[type='number']").val("");
            $("#div_3_linha_2 input[type='radio']").attr("checked", false);
            $("#div_3_linha_3").hide();
            $("#div_3_linha_3 input[type='text']").val("");
            $("#div_3_linha_3 input[type='number']").val("");
            $("#descricao_3").val("");
            $("#codigo_item_3").val('');
            $("#ler_3").hide();
            $("#ler_3_1").hide();
            $("#ler_3_2").hide();
            $("#ler_3_2").hide();
            $("#ler_3 span").text("");
            $("#ler_3_1 span").text("");
            $("#ler_3_2 span").text("");
            $("#ler_3_3 span").text("");
            $("#r3").hide();
            //div4
            $("#lbl_4").text("Dia X4");
            $("#div_4").hide();
            $("#hr_4").hide();
            $("#div_4 input[type='text']").val("");
            $("#div_4 input[type='number']").val("");
            $("#div_4 input[type='radio']").attr("checked", false);
            $("#div_4_linha_1").hide();
            $("#div_4_linha_1 input[type='text']").val("");
            $("#div_4_linha_1 input[type='number']").val("");
            $("#div_4_linha_1 input[type='radio']").attr("checked", false);
            $("#div_4_linha_2").hide();
            $("#div_4_linha_2 input[type='text']").val("");
            $("#div_4_linha_2 input[type='number']").val("");
            $("#div_4_linha_2 input[type='radio']").attr("checked", false);
            $("#div_4_linha_3").hide();
            $("#div_4_linha_3 input[type='text']").val("");
            $("#div_4_linha_3 input[type='number']").val("");
            $("#descricao_4").val("");
            $("#codigo_item_4").val('');
            $("#ler_4").hide();
            $("#ler_4_1").hide();
            $("#ler_4_2").hide();
            $("#ler_4_2").hide();
            $("#ler_4 span").text("");
            $("#ler_4_1 span").text("");
            $("#ler_4_2 span").text("");
            $("#ler_4_3 span").text("");
            $("#r4").hide();
        }
        if(id == "numero_dias_1"){
            $("#lbl_2").text("Dia X2");
            $("#div_2").hide();
            $("#hr_2").hide();
            $("#div_2 input[type='text']").val("");
            $("#div_2 input[type='number']").val("");
            $("#div_2 input[type='radio']").attr("checked", false);
            $("#div_2_linha_1").hide();
            $("#div_2_linha_1 input[type='text']").val("");
            $("#div_2_linha_1 input[type='number']").val("");
            $("#div_2_linha_1 input[type='radio']").attr("checked", false);
            $("#div_2_linha_2").hide();
            $("#div_2_linha_2 input[type='text']").val("");
            $("#div_2_linha_2 input[type='number']").val("");
            $("#div_2_linha_2 input[type='radio']").attr("checked", false);
            $("#div_2_linha_3").hide();
            $("#div_2_linha_3 input[type='text']").val("");
            $("#div_2_linha_3 input[type='number']").val("");
            $("#descricao_2").val("");
            $("#codigo_item_2").val('');
            $("#ler_2").hide();
            $("#ler_2_1").hide();
            $("#ler_2_2").hide();
            $("#ler_2_2").hide();
            $("#ler_2 span").text("");
            $("#ler_2_1 span").text("");
            $("#ler_2_2 span").text("");
            $("#ler_2_3 span").text("");
            $("#r2").hide();
            //div3
            $("#lbl_3").text("Dia X3");
            $("#div_3").hide();
            $("#hr_3").hide();
            $("#div_3 input[type='text']").val("");
            $("#div_3 input[type='number']").val("");
            $("#div_3 input[type='radio']").attr("checked", false);
            $("#div_3_linha_1").hide();
            $("#div_3_linha_1 input[type='text']").val("");
            $("#div_3_linha_1 input[type='number']").val("");
            $("#div_3_linha_1 input[type='radio']").attr("checked", false);
            $("#div_3_linha_2").hide();
            $("#div_3_linha_2 input[type='text']").val("");
            $("#div_3_linha_2 input[type='number']").val("");
            $("#div_3_linha_2 input[type='radio']").attr("checked", false);
            $("#div_3_linha_3").hide();
            $("#div_3_linha_3 input[type='text']").val("");
            $("#div_3_linha_3 input[type='number']").val("");
            $("#descricao_3").val("");
            $("#codigo_item_3").val('');
            $("#ler_3").hide();
            $("#ler_3_1").hide();
            $("#ler_3_2").hide();
            $("#ler_3_2").hide();
            $("#ler_3 span").text("");
            $("#ler_3_1 span").text("");
            $("#ler_3_2 span").text("");
            $("#ler_3_3 span").text("");
            $("#r3").hide();
            //div4
            $("#lbl_4").text("Dia X4");
            $("#div_4").hide();
            $("#hr_4").hide();
            $("#div_4 input[type='text']").val("");
            $("#div_4 input[type='number']").val("");
            $("#div_4 input[type='radio']").attr("checked", false);
            $("#div_4_linha_1").hide();
            $("#div_4_linha_1 input[type='text']").val("");
            $("#div_4_linha_1 input[type='number']").val("");
            $("#div_4_linha_1 input[type='radio']").attr("checked", false);
            $("#div_4_linha_2").hide();
            $("#div_4_linha_2 input[type='text']").val("");
            $("#div_4_linha_2 input[type='number']").val("");
            $("#div_4_linha_2 input[type='radio']").attr("checked", false);
            $("#div_4_linha_3").hide();
            $("#div_4_linha_3 input[type='text']").val("");
            $("#div_4_linha_3 input[type='number']").val("");
            $("#descricao_4").val("");
            $("#codigo_item_4").val('');
            $("#ler_4").hide();
            $("#ler_4_1").hide();
            $("#ler_4_2").hide();
            $("#ler_4_2").hide();
            $("#ler_4 span").text("");
            $("#ler_4_1 span").text("");
            $("#ler_4_2 span").text("");
            $("#ler_4_3 span").text("");
            $("#r4").hide();
        }
        if(id == "numero_dias_2"){
            $("#lbl_3").text("Dia X3");
            $("#div_3").hide();
            $("#hr_3").hide();
            $("#div_3 input[type='text']").val("");
            $("#div_3 input[type='number']").val("");
            $("#div_3 input[type='radio']").attr("checked", false);
            $("#div_3_linha_1").hide();
            $("#div_3_linha_1 input[type='text']").val("");
            $("#div_3_linha_1 input[type='number']").val("");
            $("#div_3_linha_1 input[type='radio']").attr("checked", false);
            $("#div_3_linha_2").hide();
            $("#div_3_linha_2 input[type='text']").val("");
            $("#div_3_linha_2 input[type='number']").val("");
            $("#div_3_linha_2 input[type='radio']").attr("checked", false);
            $("#div_3_linha_3").hide();
            $("#div_3_linha_3 input[type='text']").val("");
            $("#div_3_linha_3 input[type='number']").val("");
            $("#descricao_3").val("");
            $("#codigo_item_3").val('');
            $("#ler_3").hide();
            $("#ler_3_1").hide();
            $("#ler_3_2").hide();
            $("#ler_3_2").hide();
            $("#ler_3 span").text("");
            $("#ler_3_1 span").text("");
            $("#ler_3_2 span").text("");
            $("#ler_3_3 span").text("");
            $("#r3").hide();
            //div4
            $("#lbl_4").text("Dia X4");
            $("#div_4").hide();
            $("#hr_4").hide();
            $("#div_4 input[type='text']").val("");
            $("#div_4 input[type='number']").val("");
            $("#div_4 input[type='radio']").attr("checked", false);
            $("#div_4_linha_1").hide();
            $("#div_4_linha_1 input[type='text']").val("");
            $("#div_4_linha_1 input[type='number']").val("");
            $("#div_4_linha_1 input[type='radio']").attr("checked", false);
            $("#div_4_linha_2").hide();
            $("#div_4_linha_2 input[type='text']").val("");
            $("#div_4_linha_2 input[type='number']").val("");
            $("#div_4_linha_2 input[type='radio']").attr("checked", false);
            $("#div_4_linha_3").hide();
            $("#div_4_linha_3 input[type='text']").val("");
            $("#div_4_linha_3 input[type='number']").val("");
            $("#descricao_4").val("");
            $("#codigo_item_4").val('');
            $("#ler_4").hide();
            $("#ler_4_1").hide();
            $("#ler_4_2").hide();
            $("#ler_4_2").hide();
            $("#ler_4 span").text("");
            $("#ler_4_1 span").text("");
            $("#ler_4_2 span").text("");
            $("#ler_4_3 span").text("");
            $("#r4").hide();
        }
        if(id == "numero_dias_3"){
            $("#lbl_4").text("Dia X4");
            $("#div_4").hide();
            $("#hr_4").hide();
            $("#div_4 input[type='text']").val("");
            $("#div_4 input[type='number']").val("");
            $("#div_4 input[type='radio']").attr("checked", false);
            $("#div_4_linha_1").hide();
            $("#div_4_linha_1 input[type='text']").val("");
            $("#div_4_linha_1 input[type='number']").val("");
            $("#div_4_linha_1 input[type='radio']").attr("checked", false);
            $("#div_4_linha_2").hide();
            $("#div_4_linha_2 input[type='text']").val("");
            $("#div_4_linha_2 input[type='number']").val("");
            $("#div_4_linha_2 input[type='radio']").attr("checked", false);
            $("#div_4_linha_3").hide();
            $("#div_4_linha_3 input[type='text']").val("");
            $("#div_4_linha_3 input[type='number']").val("");
            $("#descricao_4").val("");
            $("#codigo_item_4").val('');
            $("#ler_4").hide();
            $("#ler_4_1").hide();
            $("#ler_4_2").hide();
            $("#ler_4_2").hide();
            $("#ler_4 span").text("");
            $("#ler_4_1 span").text("");
            $("#ler_4_2 span").text("");
            $("#ler_4_3 span").text("");
            $("#r4").hide();
        }
    }else if(valor != '' && valor != 0){
        if((id == "numero_dias_0") && ($("#mais_med_0_N").is(":checked") || $("#mais_med_0_1_N").is(":checked") || $("#mais_med_0_2_N").is(":checked") || $("#mais_med_0_2_O").is(":checked") || $("#mais_med_0_2_M").is(":checked"))){
            $("#lbl_1").text("Dia " + valor);
            $("#descricao_1").val("Dia " + valor);
            $("#div_1").show();
            $("#hr_1").show();
        }else if((id == "numero_dias_1") && ($("#mais_med_1_N").is(":checked") || $("#mais_med_1_1_N").is(":checked") || $("#mais_med_1_2_N").is(":checked") || $("#mais_med_1_2_O").is(":checked") || $("#mais_med_1_2_M").is(":checked"))){
            $("#lbl_2").text("Dia " + valor);
            $("#descricao_2").val("Dia " + valor);
            $("#div_2").show();
            $("#hr_2").show();
        }else if((id == "numero_dias_2") && ($("#mais_med_2_N").is(":checked") || $("#mais_med_2_1_N").is(":checked") || $("#mais_med_2_2_N").is(":checked") || $("#mais_med_2_2_O").is(":checked") || $("#mais_med_2_2_M").is(":checked"))){
            $("#lbl_3").text("Dia " + valor);
            $("#descricao_3").val("Dia " + valor);
            $("#div_3").show();
            $("#hr_3").show();
        }else if((id == "numero_dias_3") && ($("#mais_med_3_N").is(":checked") || $("#mais_med_3_1_N").is(":checked") || $("#mais_med_3_2_N").is(":checked") || $("#mais_med_3_2_O").is(":checked") || $("#mais_med_3_2_M").is(":checked"))){
            $("#lbl_4").text("Dia " + valor);
            $("#descricao_4").val("Dia " + valor);
            $("#div_4").show();
            $("#hr_4").show();
        }
        /* if(id == "numero_dias_0"){
            $("#lbl_1").text("Dia " + valor);
            $("#descricao_1").val("Dia " + valor);
            $("#div_1").show();
            $("#hr_1").show();
        }
        if(id == "numero_dias_1"){
            $("#lbl_2").text("Dia " + valor);
            $("#descricao_2").val("Dia " + valor);
            $("#div_2").show();
            $("#hr_2").show();
        }
        if(id == "numero_dias_2"){
            $("#lbl_3").text("Dia " + valor);
            $("#descricao_3").val("Dia " + valor);
            $("#div_3").show();
            $("#hr_3").show();
        }
        if(id == "numero_dias_3"){
            $("#lbl_4").text("Dia " + valor);
            $("#descricao_4").val("Dia " + valor);
            $("#div_4").show();
            $("#hr_4").show();
        } */
    }
}

function mais_med(nome, valor){
    //div 0 linhas 1, 2 e 3
    if(nome == 'mais_med_0' && valor == 'M'){
        $("#div_0_linha_1").show();
        $("#div_qtd_0_1").show();
        $("#div_qtd_0_1 input[type='number']").val("");
        $("#div_und_0_1").show();
        $("#div_und_0_1 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_0' && valor == 'O'){
        $("#div_0_linha_1").show();
        $("#div_qtd_0_1").hide();
        $("#div_und_0_1").hide();
    }else if(nome == 'mais_med_0' && valor == 'N'){
        $("#div_0_linha_1").hide();
        $("#div_0_linha_1 input[type='text']").val("");
        $("#div_0_linha_1 input[type='number']").val("");
        $("#div_0_linha_1 input[type='radio']").attr("checked", false);

        $("#div_0_linha_2").hide();
        $("#div_0_linha_2 input[type='text']").val("");
        $("#div_0_linha_2 input[type='number']").val("");
        $("#div_0_linha_2 input[type='radio']").attr("checked", false);

        $("#div_0_linha_3").hide();
        $("#div_0_linha_3 input[type='text']").val("");
        $("#div_0_linha_3 input[type='number']").val("");
    }

    if(nome == 'mais_med_0_1' && valor == 'M'){
        $("#div_0_linha_2").show();
        $("#div_qtd_0_2").show();
        $("#div_qtd_0_2 input[type='number']").val("");
        $("#div_und_0_2").show();
        $("#div_und_0_2 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_0_1' && valor == 'O'){
        $("#div_0_linha_2").show();
        $("#div_qtd_0_2").hide();
        $("#div_und_0_2").hide();
    }else if(nome == 'mais_med_0_1' && valor == 'N'){
        $("#div_0_linha_2").hide();
        $("#div_0_linha_2 input[type='text']").val("");
        $("#div_0_linha_2 input[type='number']").val("");
        $("#div_0_linha_2 input[type='radio']").attr("checked", false);

        $("#div_0_linha_3").hide();
        $("#div_0_linha_3 input[type='text']").val("");
        $("#div_0_linha_3 input[type='number']").val("");
    }

    if(nome == 'mais_med_0_2' && valor == 'M'){
        $("#div_0_linha_3").show();
        $("#div_qtd_0_3").show();
        $("#div_qtd_0_3 input[type='number']").val("");
        $("#div_und_0_3").show();
        $("#div_und_0_3 input[type='radio']").attr("checked", false);
        $("#mais_med_0_3_N").click();
    }else if(nome == 'mais_med_0_2' && valor == 'O'){
        $("#div_0_linha_3").show();
        $("#div_qtd_0_3").hide();
        $("#div_und_0_3").hide();
        $("#mais_med_0_3_N").click;
    }else if(nome == 'mais_med_0_2' && valor == 'N'){
        $("#div_0_linha_3").hide();
        $("#div_0_linha_3 input[type='text']").val("");
        $("#div_0_linha_3 input[type='number']").val("");
    }

    //div 1
    if(nome == 'mais_med_1' && valor == 'M'){
        $("#div_1_linha_1").show();
        $("#div_qtd_1_1").show();
        $("#div_qtd_1_1 input[type='number']").val("");
        $("#div_und_1_1").show();
        $("#div_und_1_1 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_1' && valor == 'O'){
        $("#div_1_linha_1").show();
        $("#div_qtd_1_1").hide();
        $("#div_und_1_1").hide();
    }else if(nome == 'mais_med_1' && valor == 'N'){
        $("#div_1_linha_1").hide();
        $("#div_1_linha_1 input[type='text']").val("");
        $("#div_1_linha_1 input[type='number']").val("");
        $("#div_1_linha_1 input[type='radio']").attr("checked", false);

        $("#div_1_linha_2").hide();
        $("#div_1_linha_2 input[type='text']").val("");
        $("#div_1_linha_2 input[type='number']").val("");
        $("#div_1_linha_2 input[type='radio']").attr("checked", false);

        $("#div_1_linha_3").hide();
        $("#div_1_linha_3 input[type='text']").val("");
        $("#div_1_linha_3 input[type='number']").val("");
    }

    if(nome == 'mais_med_1_1' && valor == 'M'){
        $("#div_1_linha_2").show();
        $("#div_qtd_1_2").show();
        $("#div_qtd_1_2 input[type='number']").val("");
        $("#div_und_1_2").show();
        $("#div_und_1_2 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_1_1' && valor == 'O'){
        $("#div_1_linha_2").show();
        $("#div_qtd_1_2").hide();
        $("#div_und_1_2").hide();
    }else if(nome == 'mais_med_1_1' && valor == 'N'){
        $("#div_1_linha_2").hide();
        $("#div_1_linha_2 input[type='text']").val("");
        $("#div_1_linha_2 input[type='number']").val("");
        $("#div_1_linha_2 input[type='radio']").attr("checked", false);

        $("#div_1_linha_3").hide();
        $("#div_1_linha_3 input[type='text']").val("");
        $("#div_1_linha_3 input[type='number']").val("");
    }

    if(nome == 'mais_med_1_2' && valor == 'M'){
        $("#div_1_linha_3").show();
        $("#div_qtd_1_3").show();
        $("#div_qtd_1_3 input[type='number']").val("");
        $("#div_und_1_3").show();
        $("#div_und_1_3 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_1_2' && valor == 'O'){
        $("#div_1_linha_3").show();
        $("#div_qtd_1_3").hide();
        $("#div_und_1_3").hide();
    }else if(nome == 'mais_med_1_2' && valor == 'N'){
        $("#div_1_linha_3").hide();
        $("#div_1_linha_3 input[type='text']").val("");
        $("#div_1_linha_3 input[type='number']").val("");
    }

    //div 2
    if(nome == 'mais_med_2' && valor == 'M'){
        $("#div_2_linha_1").show();
        $("#div_qtd_2_1").show();
        $("#div_qtd_2_1 input[type='number']").val("");
        $("#div_und_2_1").show();
        $("#div_und_2_1 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_2' && valor == 'O'){
        $("#div_2_linha_1").show();
        $("#div_qtd_2_1").hide();
        $("#div_und_2_1").hide();
    }else if(nome == 'mais_med_2' && valor == 'N'){
        $("#div_2_linha_1").hide();
        $("#div_2_linha_1 input[type='text']").val("");
        $("#div_2_linha_1 input[type='number']").val("");
        $("#div_2_linha_1 input[type='radio']").attr("checked", false);

        $("#div_2_linha_2").hide();
        $("#div_2_linha_2 input[type='text']").val("");
        $("#div_2_linha_2 input[type='number']").val("");
        $("#div_2_linha_2 input[type='radio']").attr("checked", false);

        $("#div_2_linha_3").hide();
        $("#div_2_linha_3 input[type='text']").val("");
        $("#div_2_linha_3 input[type='number']").val("");
    }

    if(nome == 'mais_med_2_1' && valor == 'M'){
        $("#div_2_linha_2").show();
        $("#div_qtd_2_2").show();
        $("#div_qtd_2_2 input[type='number']").val("");
        $("#div_und_2_2").show();
        $("#div_und_2_2 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_2_1' && valor == 'O'){
        $("#div_2_linha_2").show();
        $("#div_qtd_2_2").hide();
        $("#div_und_2_2").hide();
    }else if(nome == 'mais_med_2_1' && valor == 'N'){
        $("#div_2_linha_2").hide();
        $("#div_2_linha_2 input[type='text']").val("");
        $("#div_2_linha_2 input[type='number']").val("");
        $("#div_2_linha_2 input[type='radio']").attr("checked", false);

        $("#div_2_linha_3").hide();
        $("#div_2_linha_3 input[type='text']").val("");
        $("#div_2_linha_3 input[type='number']").val("");
    }

    if(nome == 'mais_med_2_2' && valor == 'M'){
        $("#div_2_linha_3").show();
        $("#div_qtd_2_3").show();
        $("#div_qtd_2_3 input[type='number']").val("");
        $("#div_und_2_3").show();
        $("#div_und_2_3 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_2_2' && valor == 'O'){
        $("#div_2_linha_3").show();
        $("#div_qtd_2_3").hide();
        $("#div_und_2_3").hide();
    }else if(nome == 'mais_med_2_2' && valor == 'N'){
        $("#div_2_linha_3").hide();
        $("#div_2_linha_3 input[type='text']").val("");
        $("#div_2_linha_3 input[type='number']").val("");
    }

    //div 3
    if(nome == 'mais_med_3' && valor == 'M'){
        $("#div_3_linha_1").show();
        $("#div_qtd_3_1").show();
        $("#div_qtd_3_1 input[type='number']").val("");
        $("#div_und_3_1").show();
        $("#div_und_3_1 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_3' && valor == 'O'){
        $("#div_3_linha_1").show();
        $("#div_qtd_3_1").hide();
        $("#div_und_3_1").hide();
    }else if(nome == 'mais_med_3' && valor == 'N'){
        $("#div_3_linha_1").hide();
        $("#div_3_linha_1 input[type='text']").val("");
        $("#div_3_linha_1 input[type='number']").val("");
        $("#div_3_linha_1 input[type='radio']").attr("checked", false);

        $("#div_3_linha_2").hide();
        $("#div_3_linha_2 input[type='text']").val("");
        $("#div_3_linha_2 input[type='number']").val("");
        $("#div_3_linha_2 input[type='radio']").attr("checked", false);

        $("#div_3_linha_3").hide();
        $("#div_3_linha_3 input[type='text']").val("");
        $("#div_3_linha_3 input[type='number']").val("");
    }

    if(nome == 'mais_med_3_1' && valor == 'M'){
        $("#div_3_linha_2").show();
        $("#div_qtd_3_2").show();
        $("#div_qtd_3_2 input[type='number']").val("");
        $("#div_und_3_2").show();
        $("#div_und_3_2 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_3_1' && valor == 'O'){
        $("#div_3_linha_2").show();
        $("#div_qtd_3_2").hide();
        $("#div_und_3_2").hide();
    }else if(nome == 'mais_med_3_1' && valor == 'N'){
        $("#div_3_linha_2").hide();
        $("#div_3_linha_2 input[type='text']").val("");
        $("#div_3_linha_2 input[type='number']").val("");
        $("#div_3_linha_2 input[type='radio']").attr("checked", false);

        $("#div_3_linha_3").hide();
        $("#div_3_linha_3 input[type='text']").val("");
        $("#div_3_linha_3 input[type='number']").val("");
    }

    if(nome == 'mais_med_3_2' && valor == 'M'){
        $("#div_3_linha_3").show();
        $("#div_qtd_3_3").show();
        $("#div_qtd_3_3 input[type='number']").val("");
        $("#div_und_3_3").show();
        $("#div_und_3_3 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_3_2' && valor == 'O'){
        $("#div_3_linha_3").show();
        $("#div_qtd_3_3").hide();
        $("#div_und_3_3").hide();
    }else if(nome == 'mais_med_3_2' && valor == 'N'){
        $("#div_3_linha_3").hide();
        $("#div_3_linha_3 input[type='text']").val("");
        $("#div_3_linha_3 input[type='number']").val("");
    }

    //div 4
    if(nome == 'mais_med_4' && valor == 'M'){
        $("#div_4_linha_1").show();
        $("#div_qtd_4_1").show();
        $("#div_qtd_4_1 input[type='number']").val("");
        $("#div_und_4_1").show();
        $("#div_und_4_1 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_4' && valor == 'O'){
        $("#div_4_linha_1").show();
        $("#div_qtd_4_1").hide();
        $("#div_und_4_1").hide();
    }else if(nome == 'mais_med_4' && valor == 'N'){
        $("#div_4_linha_1").hide();
        $("#div_4_linha_1 input[type='text']").val("");
        $("#div_4_linha_1 input[type='number']").val("");
        $("#div_4_linha_1 input[type='radio']").attr("checked", false);

        $("#div_4_linha_2").hide();
        $("#div_4_linha_2 input[type='text']").val("");
        $("#div_4_linha_2 input[type='number']").val("");
        $("#div_4_linha_2 input[type='radio']").attr("checked", false);

        $("#div_4_linha_3").hide();
        $("#div_4_linha_3 input[type='text']").val("");
        $("#div_4_linha_3 input[type='number']").val("");
    }

    if(nome == 'mais_med_4_1' && valor == 'M'){
        $("#div_4_linha_2").show();
        $("#div_qtd_4_2").show();
        $("#div_qtd_4_2 input[type='number']").val("");
        $("#div_und_4_2").show();
        $("#div_und_4_2 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_4_1' && valor == 'O'){
        $("#div_4_linha_2").show();
        $("#div_qtd_4_2").hide();
        $("#div_und_4_2").hide();
    }else if(nome == 'mais_med_4_1' && valor == 'N'){
        $("#div_4_linha_2").hide();
        $("#div_4_linha_2 input[type='text']").val("");
        $("#div_4_linha_2 input[type='number']").val("");
        $("#div_4_linha_2 input[type='radio']").attr("checked", false);

        $("#div_4_linha_3").hide();
        $("#div_4_linha_3 input[type='text']").val("");
        $("#div_4_linha_3 input[type='number']").val("");
    }

    if(nome == 'mais_med_4_2' && valor == 'M'){
        $("#div_4_linha_3").show();
        $("#div_qtd_4_3").show();
        $("#div_qtd_4_3 input[type='number']").val("");
        $("#div_und_4_3").show();
        $("#div_und_4_3 input[type='radio']").attr("checked", false);
    }else if(nome == 'mais_med_4_2' && valor == 'O'){
        $("#div_4_linha_3").show();
        $("#div_qtd_4_3").hide();
        $("#div_und_4_3").hide();
    }else if(nome == 'mais_med_4_2' && valor == 'N'){
        $("#div_4_linha_3").hide();
        $("#div_4_linha_3 input[type='text']").val("");
        $("#div_4_linha_3 input[type='number']").val("");
    }
}

function ler_itens_protocolo(){
    $.post("ler_itens_protocoloIATF.php", {protocolo_id: $("#codigo_conta").val()},
        function(data){
            var array_retorno = data.split('|');
            if(array_retorno[0] == 1){
                mostrar_linhas("numero_dias_0", "");
                //produto 0
                $("#codigo_item_0").val(array_retorno[1]);
                $("#numero_dias_0").val("");
                $("#nome_prod_0").val(array_retorno[3]);
                $("#lbl_nome_prod_0").text(array_retorno[3]);
                if(array_retorno[4] == '0.000'){
                    $("#lbl_quantidade_0").text('');
                    $("#quantidade_0").val('');
                }else{
                    $("#lbl_quantidade_0").text(array_retorno[4]);      
                    $("#quantidade_0").val(array_retorno[4]);
                }
                if(array_retorno[5] != ''){
                    $(`input[name='unidade_0'][value=${array_retorno[5]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_0']`).prop("checked", false);
                }
                $("#lbl_unidade_0").text(array_retorno[5]);

                if(array_retorno[6] != '' && array_retorno[7] != '0.000'){
                    $("#mais_med_0_M").click();
                    $("#nome_prod_0_1").val(array_retorno[6]);
                    $("input#quantidade_0_1").val(array_retorno[7]);
                    $(`input[name='unidade_0_1'][value=${array_retorno[8]}]`).click();

                    $("#lbl_nome_prod_0_1").text(array_retorno[6]);
                    $("#lbl_quantidade_0_1").text(array_retorno[7]);
                    $("#lbl_unidade_0_1").text(array_retorno[8]);
                    $("#ler_0_1").show();
                }else if(array_retorno[6] != '' && array_retorno[7] == '0.000'){
                    $("#mais_med_0_O").click();
                    $("#nome_prod_0_1").val(array_retorno[6]);

                    $("#lbl_nome_prod_0_1").text(array_retorno[6]);
                    $("#lbl_quantidade_0_1").text('');
                    $("#lbl_unidade_0_1").text(array_retorno[8]);

                    $("#ler_0_1").show();
                }else{
                    $("#mais_med_0_N").click();
                    $("#lbl_nome_prod_0_1").text('');
                    $("#lbl_quantidade_0_1").text('');
                    $("#lbl_unidade_0_1").text('');

                    $("#ler_0_1").hide();
                }
                if(array_retorno[9] != '' && array_retorno[10] != '0.000'){
                    $("#mais_med_0_1_M").click();
                    $("#nome_prod_0_2").val(array_retorno[9]);
                    $("#quantidade_0_2").val(array_retorno[10]);
                    $(`input[name='unidade_0_2'][value=${array_retorno[11]}]`).click();

                    $("#lbl_nome_prod_0_2").text(array_retorno[9]);
                    $("#lbl_quantidade_0_2").text(array_retorno[10]);
                    $("#lbl_unidade_0_2").text(array_retorno[11]);

                    $("#ler_0_2").show();
                }else if(array_retorno[9] != '' && array_retorno[10] == '0.000'){
                    $("#mais_med_0_1_O").click();
                    $("#nome_prod_0_2").val(array_retorno[9]);

                    $("#lbl_nome_prod_0_2").text(array_retorno[9]);
                    $("#lbl_quantidade_0_2").text('');
                    $("#lbl_unidade_0_2").text(array_retorno[11]);

                    $("#ler_0_2").show();
                }else{
                    $("#mais_med_0_1_N").click();
                    $("#lbl_nome_prod_0_2").text('');
                    $("#lbl_quantidade_0_2").text('');
                    $("#lbl_unidade_0_2").text('');

                    $("#ler_0_2").hide();
                }
                if(array_retorno[12] != '' && array_retorno[13] != '0.000'){
                    $("#mais_med_0_2_M").click();
                    $("#nome_prod_0_3").val(array_retorno[12]);
                    $("#quantidade_0_3").val(array_retorno[13]);
                    $(`input[name='unidade_0_3'][value=${array_retorno[14]}]`).prop("checked", true);

                    $("#lbl_quantidade_0_3").text(array_retorno[13]);
                    $("#lbl_nome_prod_0_3").text(array_retorno[12]);
                    $("#lbl_unidade_0_3").text(array_retorno[14]);

                    $("#ler_0_3").show();
                }else if(array_retorno[12] != '' && array_retorno[13] == '0.000'){
                    $("#mais_med_0_2_O").click();
                    $("#nome_prod_0_3").val(array_retorno[12]);

                    $("#lbl_nome_prod_0_3").text(array_retorno[12]);
                    $("#lbl_quantidade_0_3").text('');
                    $("#lbl_unidade_0_3").text(array_retorno[14]);

                    $("#ler_0_3").show();
                }else{
                    $("#mais_med_0_2_N").click();
                    $("#lbl_nome_prod_0_3").text('');
                    $("#lbl_quantidade_0_3").text('');
                    $("#lbl_unidade_0_3").text('');

                    $("#ler_0_3").hide();
                }
            }else if(array_retorno[0] == 2){
                //produto 0
                $("#codigo_item_0").val(array_retorno[1]);
                /* $("#numero_dias_0").val(""); */
                var descricao = array_retorno[16]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_0").val(descCorrigida);
                $("#numero_dias_0").change();
                $("#numero_dias_1").val("");

                $("#nome_prod_0").val(array_retorno[3]);
                $("#lbl_nome_prod_0").text(array_retorno[3]);
                if(array_retorno[4] == '0.000'){
                    $("#lbl_quantidade_0").text('');
                    $("#quantidade_0").val('');
                }else{
                    $("#lbl_quantidade_0").text(array_retorno[4]);
                    $("#quantidade_0").val(array_retorno[4]);
                }
                if(array_retorno[5] != ''){
                    $(`input[name='unidade_0'][value=${array_retorno[5]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_0']`).prop("checked", false);
                }
                $("#lbl_unidade_0").text(array_retorno[5]);

                if(array_retorno[6] != '' && array_retorno[7] != '0.000'){
                    $("#mais_med_0_M").click();
                    $("#nome_prod_0_1").val(array_retorno[6]);
                    $("input#quantidade_0_1").val(array_retorno[7]);
                    $(`input[name='unidade_0_1'][value=${array_retorno[8]}]`).click();

                    $("#lbl_nome_prod_0_1").text(array_retorno[6]);
                    $("#lbl_quantidade_0_1").text(array_retorno[7]);
                    $("#lbl_unidade_0_1").text(array_retorno[8]);

                    $("#ler_0_1").show();
                }else if(array_retorno[6] != '' && array_retorno[7] == '0.000'){
                    $("#mais_med_0_O").click();
                    $("#nome_prod_0_1").val(array_retorno[6]);

                    $("#lbl_nome_prod_0_1").text(array_retorno[6]);
                    $("#lbl_quantidade_0_1").text('');
                    $("#lbl_unidade_0_1").text(array_retorno[8]);

                    $("#ler_0_1").show();
                }else{
                    $("#mais_med_0_N").click();
                    $("#lbl_nome_prod_0_1").text('');
                    $("#lbl_quantidade_0_1").text('');
                    $("#lbl_unidade_0_1").text('');

                    $("#ler_0_1").hide();
                }
                if(array_retorno[9] != '' && array_retorno[10] != '0.000'){
                    $("#mais_med_0_1_M").click();
                    $("#nome_prod_0_2").val(array_retorno[9]);
                    $("#quantidade_0_2").val(array_retorno[10]);
                    $(`input[name='unidade_0_2'][value=${array_retorno[11]}]`).click();

                    $("#lbl_nome_prod_0_2").text(array_retorno[9]);
                    $("#lbl_quantidade_0_2").text(array_retorno[10]);
                    $("#lbl_unidade_0_2").text(array_retorno[11]);

                    $("#ler_0_2").show();
                }else if(array_retorno[9] != '' && array_retorno[10] == '0.000'){
                    $("#mais_med_0_1_O").click();
                    $("#nome_prod_0_2").val(array_retorno[9]);

                    $("#lbl_nome_prod_0_2").text(array_retorno[9]);
                    $("#lbl_quantidade_0_2").text('');
                    $("#lbl_unidade_0_2").text(array_retorno[11]);

                    $("#ler_0_2").show();
                }else{
                    $("#mais_med_0_1_N").click();
                    $("#lbl_nome_prod_0_2").text('');
                    $("#lbl_quantidade_0_2").text('');
                    $("#lbl_unidade_0_2").text('');

                    $("#ler_0_2").hide();
                }
                if(array_retorno[12] != '' && array_retorno[13] != '0.000'){
                    $("#mais_med_0_2_M").click();
                    $("#mais_med_0_3_N").click();
                    $("#nome_prod_0_3").val(array_retorno[12]);
                    $("#quantidade_0_3").val(array_retorno[13]);
                    $(`input[name='unidade_0_3'][value=${array_retorno[14]}]`).prop("checked", true);

                    $("#lbl_nome_prod_0_3").text(array_retorno[12]);
                    $("#lbl_quantidade_0_3").text(array_retorno[13]);
                    $("#lbl_unidade_0_3").text(array_retorno[14]);

                    $("#ler_0_3").show();
                }else if(array_retorno[12] != '' && array_retorno[13] == '0.000'){
                    $("#mais_med_0_2_O").click();
                    $("#mais_med_0_3_N").click();
                    $("#nome_prod_0_3").val(array_retorno[12]);

                    $("#lbl_nome_prod_0_3").text(array_retorno[12]);
                    $("#lbl_quantidade_0_3").text('');
                    $("#lbl_unidade_0_3").text(array_retorno[14]);

                    $("#ler_0_3").show();
                }else{
                    $("#mais_med_0_2_N").click();
                    $("#lbl_nome_prod_0_3").text('');
                    $("#lbl_quantidade_0_3").text('');
                    $("#lbl_unidade_0_3").text('');

                    $("#ler_0_3").hide();
                }
                //produto 1
                if($("#tipo_gravacao").val() == '1'){
                    $("#enviar_lixeira_1").show();
                    $("#lixeira_item_1").val('');
                }
                mostrar_linhas("numero_dias_1", "");
                $("div#r1").show();
                $("div#ler_1").show();
                $("#codigo_item_1").val(array_retorno[15]);
                $("#lbl_descricao_1").text(array_retorno[16]);
                /* var descricao = array_retorno[16]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_0").val(descCorrigida);
                $("#numero_dias_1").val("");
                $("#numero_dias_0").change(); */
                $("#nome_prod_1").val(array_retorno[17]);
                $("#lbl_nome_prod_1").text(array_retorno[17]);
                if(array_retorno[18] == '0.000'){
                    $("#lbl_quantidade_1").text('');
                    $("#quantidade_1").val('');
                }else{
                    $("#lbl_quantidade_1").text(array_retorno[18]);
                    $("#quantidade_1").val(array_retorno[18]);
                }
                if(array_retorno[19] != ''){
                    $(`input[name='unidade_1'][value=${array_retorno[19]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_1']`).prop("checked", false);
                }
                $("#lbl_unidade_1").text(array_retorno[19]);

                if(array_retorno[20] != '' && array_retorno[21] != '0.000'){
                    $("#mais_med_1_M").click();
                    $("#nome_prod_1_1").val(array_retorno[20]);
                    $("input#quantidade_1_1").val(array_retorno[21]);
                    $(`input[name='unidade_1_1'][value=${array_retorno[22]}]`).click();

                    $("#lbl_nome_prod_1_1").text(array_retorno[20]);
                    $("#lbl_quantidade_1_1").text(array_retorno[21]);
                    $("#lbl_unidade_1_1").text(array_retorno[22]);

                    $("#ler_1_1").show();
                }else if(array_retorno[20] != '' && array_retorno[21] == '0.000'){
                    $("#mais_med_1_O").click();
                    $("#nome_prod_1_1").val(array_retorno[20]);

                    $("#lbl_nome_prod_1_1").text(array_retorno[20]);
                    $("#lbl_quantidade_1_1").text('');
                    $("#lbl_unidade_1_1").text(array_retorno[22]);

                    $("#ler_1_1").show();
                }else{
                    $("#mais_med_1_N").click();
                    $("#lbl_nome_prod_1_1").text('');
                    $("#lbl_quantidade_1_1").text('');
                    $("#lbl_unidade_1_1").text('');

                    $("#ler_1_1").hide();
                }
                if(array_retorno[23] != '' && array_retorno[24] != '0.000'){
                    $("#mais_med_1_1_M").click();
                    $("#nome_prod_1_2").val(array_retorno[23]);
                    $("#quantidade_1_2").val(array_retorno[24]);
                    $(`input[name='unidade_1_2'][value=${array_retorno[25]}]`).click();

                    $("#lbl_nome_prod_1_2").text(array_retorno[23]);
                    $("#lbl_quantidade_1_2").text(array_retorno[24]);
                    $("#lbl_unidade_1_2").text(array_retorno[25]);

                    $("#ler_1_2").show();
                }else if(array_retorno[23] != '' && array_retorno[24] == '0.000'){
                    $("#mais_med_1_1_O").click();
                    $("#nome_prod_1_2").val(array_retorno[23]);

                    $("#lbl_nome_prod_1_2").text(array_retorno[23]);
                    $("#lbl_quantidade_1_2").text('');
                    $("#lbl_unidade_1_2").text(array_retorno[25]);

                    $("#ler_1_2").show();
                }else{
                    $("#mais_med_1_1_N").click();
                    $("#lbl_nome_prod_1_2").text('');
                    $("#lbl_quantidade_1_2").text('');
                    $("#lbl_unidade_1_2").text('');

                    $("#ler_1_2").hide();
                }
                if(array_retorno[26] != '' && array_retorno[27] != '0.000'){
                    $("#mais_med_1_2_M").click();
                    $("#nome_prod_1_3").val(array_retorno[26]);
                    $("#quantidade_1_3").val(array_retorno[27]);
                    $(`input[name='unidade_1_3'][value=${array_retorno[28]}]`).prop("checked", true);

                    $("#lbl_nome_prod_1_3").text(array_retorno[26]);
                    $("#lbl_quantidade_1_3").text(array_retorno[27]);
                    $("#lbl_unidade_1_3").text(array_retorno[28]);

                    $("#ler_1_3").show();
                }else if(array_retorno[26] != '' && array_retorno[27] == '0.000'){
                    $("#mais_med_1_2_O").click();
                    $("#nome_prod_1_3").val(array_retorno[26]);

                    $("#lbl_nome_prod_1_3").text(array_retorno[26]);
                    $("#lbl_quantidade_1_3").text('');
                    $("#lbl_unidade_1_3").text(array_retorno[28]);

                    $("#ler_1_3").show();
                }
                else{
                    $("#mais_med_1_2_N").click();
                    $("#lbl_nome_prod_1_3").text('');
                    $("#lbl_quantidade_1_3").text('');
                    $("#lbl_unidade_1_3").text('');

                    $("#ler_1_3").hide();
                }
            }else if(array_retorno[0] == 3){
                //produto 0
                $("#codigo_item_0").val(array_retorno[1]);
                /* $("#numero_dias_0").val(""); */
                var descricao = array_retorno[16]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_0").val(descCorrigida);
                $("#numero_dias_1").val("");
                $("#numero_dias_0").change();

                $("#nome_prod_0").val(array_retorno[3]);
                $("#lbl_nome_prod_0").text(array_retorno[3]);
                if(array_retorno[4] == '0.000'){
                    $("#lbl_quantidade_0").text('');
                    $("#quantidade_0").val('');
                }else{
                    $("#lbl_quantidade_0").text(array_retorno[4]);
                    $("#quantidade_0").val(array_retorno[4]);
                }
                if(array_retorno[5] != ''){
                    $(`input[name='unidade_0'][value=${array_retorno[5]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_0']`).prop("checked", false);
                }
                $("#lbl_unidade_0").text(array_retorno[5]);

                if(array_retorno[6] != '' && array_retorno[7] != '0.000'){
                    $("#mais_med_0_M").click();
                    $("#nome_prod_0_1").val(array_retorno[6]);
                    $("#lbl_nome_prod_0_1").text(array_retorno[6]);
                    $("input#quantidade_0_1").val(array_retorno[7]);
                    $("#lbl_quantidade_0_1").text(array_retorno[7]);
                    $(`input[name='unidade_0_1'][value=${array_retorno[8]}]`).click();
                    $("#lbl_unidade_0_1").text(array_retorno[8]);

                    $("#ler_0_1").show();
                }else if(array_retorno[6] != '' && array_retorno[7] == '0.000'){
                    $("#mais_med_0_O").click();
                    $("#nome_prod_0_1").val(array_retorno[6]);
                    $("#lbl_nome_prod_0_1").text(array_retorno[6]);
                    $("#lbl_quantidade_0_1").text('');
                    $("#lbl_unidade_0_1").text(array_retorno[8]);

                    $("#ler_0_1").show();
                }else{
                    $("#mais_med_0_N").click();
                    $("#lbl_nome_prod_0_1").text('');
                    $("#lbl_quantidade_0_1").text('');
                    $("#lbl_unidade_0_1").text('');

                    $("#ler_0_1").hide();
                }
                if(array_retorno[9] != '' && array_retorno[10] != '0.000'){
                    $("#mais_med_0_1_M").click();
                    $("#nome_prod_0_2").val(array_retorno[9]);
                    $("#lbl_nome_prod_0_2").text(array_retorno[9]);
                    $("#quantidade_0_2").val(array_retorno[10]);
                    $("#lbl_quantidade_0_2").text(array_retorno[10]);
                    $(`input[name='unidade_0_2'][value=${array_retorno[11]}]`).click();
                    $("#lbl_unidade_0_2").text(array_retorno[11]);

                    $("#ler_0_2").show();
                }else if(array_retorno[9] != '' && array_retorno[10] == '0.000'){
                    $("#mais_med_0_1_O").click();
                    $("#nome_prod_0_2").val(array_retorno[9]);
                    $("#lbl_nome_prod_0_2").text(array_retorno[9]);
                    $("#lbl_quantidade_0_2").text('');
                    $("#lbl_unidade_0_2").text(array_retorno[11]);

                    $("#ler_0_2").show();
                }else{
                    $("#mais_med_0_1_N").click();
                    $("#lbl_nome_prod_0_2").text('');
                    $("#lbl_quantidade_0_2").text('');
                    $("#lbl_unidade_0_2").text('');

                    $("#ler_0_2").hide();
                }
                if(array_retorno[12] != '' && array_retorno[13] != '0.000'){
                    $("#mais_med_0_2_M").click();
                    $("#mais_med_0_3_N").click();
                    $("#nome_prod_0_3").val(array_retorno[12]);
                    $("#lbl_nome_prod_0_3").text(array_retorno[12]);
                    $("#quantidade_0_3").val(array_retorno[13]);
                    $("#lbl_quantidade_0_3").text(array_retorno[13]);
                    $(`input[name='unidade_0_3'][value=${array_retorno[14]}]`).prop("checked", true);
                    $("#lbl_unidade_0_3").text(array_retorno[14]);

                    $("#ler_0_3").show();
                }else if(array_retorno[12] != '' && array_retorno[13] == '0.000'){
                    $("#mais_med_0_2_O").click();
                    $("#mais_med_0_3_N").click();
                    $("#nome_prod_0_3").val(array_retorno[12]);
                    $("#lbl_nome_prod_0_3").text(array_retorno[12]);
                    $("#lbl_quantidade_0_3").text('');
                    $("#lbl_unidade_0_3").text(array_retorno[14]);

                    $("#ler_0_3").show();
                }else{
                    $("#mais_med_0_2_N").click();
                    $("#lbl_nome_prod_0_3").text('');
                    $("#lbl_quantidade_0_3").text('');
                    $("#lbl_unidade_0_3").text('');

                    $("#ler_0_3").hide();
                }
                //produto 1
                if($("#tipo_gravacao").val() == '1'){
                    $("#enviar_lixeira_1").show();
                    $("#lixeira_item_1").val('');
                }
                $("div#r1").show();
                $("div#ler_1").show();
                $("#codigo_item_1").val(array_retorno[15]);
                $("#lbl_descricao_1").text(array_retorno[16]);
                /* var descricao = array_retorno[16]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_0").val(descCorrigida);
                $("#numero_dias_1").val("");
                $("#numero_dias_0").change(); */

                var descricao = array_retorno[30]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_1").val(descCorrigida);
                $("#numero_dias_2").val("");
                $("#numero_dias_1").change();

                $("#nome_prod_1").val(array_retorno[17]);
                $("#lbl_nome_prod_1").text(array_retorno[17]);
                if(array_retorno[18] == '0.000'){
                    $("#lbl_quantidade_1").text('');
                    $("#quantidade_1").val('');
                }else{
                    $("#lbl_quantidade_1").text(array_retorno[18]);
                    $("#quantidade_1").val(array_retorno[18]);
                }
                if(array_retorno[19] != ''){
                    $(`input[name='unidade_1'][value=${array_retorno[19]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_1']`).prop("checked", false);
                }
                $("#lbl_unidade_1").text(array_retorno[19]);

                if(array_retorno[20] != '' && array_retorno[21] != '0.000'){
                    $("#mais_med_1_M").click();
                    $("#nome_prod_1_1").val(array_retorno[20]);
                    $("#lbl_nome_prod_1_1").text(array_retorno[20]);
                    $("input#quantidade_1_1").val(array_retorno[21]);
                    $("#lbl_quantidade_1_1").text(array_retorno[21]);
                    $(`input[name='unidade_1_1'][value=${array_retorno[22]}]`).click();
                    $("#lbl_unidade_1_1").text(array_retorno[22]);

                    $("#ler_1_1").show();
                }else if(array_retorno[20] != '' && array_retorno[21] == '0.000'){
                    $("#mais_med_1_O").click();
                    $("#nome_prod_1_1").val(array_retorno[20]);
                    $("#lbl_nome_prod_1_1").text(array_retorno[20]);
                    $("#lbl_quantidade_1_1").text('');
                    $("#lbl_unidade_1_1").text(array_retorno[22]);

                    $("#ler_1_1").show();
                }else{
                    $("#mais_med_1_N").click();
                    $("#lbl_nome_prod_1_1").text('');
                    $("#lbl_quantidade_1_1").text('');
                    $("#lbl_unidade_1_1").text('');

                    $("#ler_1_1").hide();
                }
                if(array_retorno[23] != '' && array_retorno[24] != '0.000'){
                    $("#mais_med_1_1_M").click();
                    $("#nome_prod_1_2").val(array_retorno[23]);
                    $("#lbl_nome_prod_1_2").text(array_retorno[23]);
                    $("#quantidade_1_2").val(array_retorno[24]);
                    $("#lbl_quantidade_1_2").text(array_retorno[24]);
                    $(`input[name='unidade_1_2'][value=${array_retorno[25]}]`).click();
                    $("#lbl_unidade_1_2").text(array_retorno[25]);

                    $("#ler_1_2").show();
                }else if(array_retorno[23] != '' && array_retorno[24] == '0.000'){
                    $("#mais_med_1_1_O").click();
                    $("#nome_prod_1_2").val(array_retorno[23]);
                    $("#lbl_nome_prod_1_2").text(array_retorno[23]);
                    $("#lbl_quantidade_1_2").text('');
                    $("#lbl_unidade_1_2").text(array_retorno[25]);

                    $("#ler_1_2").show();
                }else{
                    $("#mais_med_1_1_N").click();
                    $("#lbl_nome_prod_1_2").text('');
                    $("#lbl_quantidade_1_2").text('');
                    $("#lbl_unidade_1_2").text('');

                    $("#ler_1_2").hide();
                }
                if(array_retorno[26] != '' && array_retorno[27] != '0.000'){
                    $("#mais_med_1_2_M").click();
                    $("#mais_med_1_3_N").click();
                    $("#nome_prod_1_3").val(array_retorno[26]);
                    $("#lbl_nome_prod_1_3").text(array_retorno[26]);
                    $("#quantidade_1_3").val(array_retorno[27]);
                    $("#lbl_quantidade_1_3").text(array_retorno[27]);
                    $(`input[name='unidade_1_3'][value=${array_retorno[28]}]`).prop("checked", true);
                    $("#lbl_unidade_1_3").text(array_retorno[28]);

                    $("#ler_1_3").show();
                }else if(array_retorno[26] != '' && array_retorno[27] == '0.000'){
                    $("#mais_med_1_2_O").click();
                    $("#mais_med_1_3_N").click();
                    $("#nome_prod_1_3").val(array_retorno[26]);
                    $("#lbl_nome_prod_1_3").text(array_retorno[26]);
                    $("#lbl_quantidade_1_3").text('');
                    $("#lbl_unidade_1_3").text(array_retorno[28]);

                    $("#ler_1_3").show();
                }else{
                    $("#mais_med_1_2_N").click();
                    $("#lbl_nome_prod_1_3").text('');
                    $("#lbl_quantidade_1_3").text('');
                    $("#lbl_unidade_1_3").text('');

                    $("#ler_1_3").hide();
                }
                //produto 2
                if($("#tipo_gravacao").val() == '1'){
                    $("#enviar_lixeira_2").show();
                    $("#lixeira_item_2").val('');
                }
                mostrar_linhas("numero_dias_2", "");
                $("div#r2").show();
                $("div#ler_2").show();
                $("#codigo_item_2").val(array_retorno[29]);
                $("#lbl_descricao_2").text(array_retorno[30]);
                /* var descricao = array_retorno[30]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_1").val(descCorrigida);
                $("#numero_dias_2").val("");
                $("#numero_dias_1").change(); */
                $("#nome_prod_2").val(array_retorno[31]);
                $("#lbl_nome_prod_2").text(array_retorno[31]);
                if(array_retorno[32] == '0.000'){
                    $("#lbl_quantidade_2").text('');
                    $("#quantidade_2").val('');
                }else{
                    $("#lbl_quantidade_2").text(array_retorno[32]);
                    $("#quantidade_2").val(array_retorno[32]);
                }
                if(array_retorno[33] != ''){
                    $(`input[name='unidade_2'][value=${array_retorno[33]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_2']`).prop("checked", false);
                }
                $("#lbl_unidade_2").text(array_retorno[33]);

                if(array_retorno[34] != '' && array_retorno[35] != '0.000'){
                    $("#mais_med_2_M").click();
                    $("#nome_prod_2_1").val(array_retorno[34]);
                    $("#lbl_nome_prod_2_1").text(array_retorno[34]);
                    $("input#quantidade_2_1").val(array_retorno[35]);
                    $("#lbl_quantidade_2_1").text(array_retorno[35]);
                    $(`input[name='unidade_2_1'][value=${array_retorno[36]}]`).click();
                    $("#lbl_unidade_2_1").text(array_retorno[36]);

                    $("#ler_2_1").show();
                }else if(array_retorno[34] != '' && array_retorno[35] == '0.000'){
                    $("#mais_med_2_O").click();
                    $("#nome_prod_2_1").val(array_retorno[34]);
                    $("#lbl_nome_prod_2_1").text(array_retorno[34]);
                    $("#lbl_quantidade_2_1").text('');
                    $("#lbl_unidade_2_1").text(array_retorno[36]);

                    $("#ler_2_1").show();
                }else{
                    $("#mais_med_2_N").click();
                    $("#lbl_nome_prod_2_1").text('');
                    $("#lbl_quantidade_2_1").text('');
                    $("#lbl_unidade_2_1").text('');

                    $("#ler_2_1").hide();
                }
                if(array_retorno[37] != '' && array_retorno[38] != '0.000'){
                    $("#mais_med_2_1_M").click();
                    $("#nome_prod_2_2").val(array_retorno[37]);
                    $("#lbl_nome_prod_2_2").text(array_retorno[37]);
                    $("#quantidade_2_2").val(array_retorno[38]);
                    $("#lbl_quantidade_2_2").text(array_retorno[38]);
                    $(`input[name='unidade_2_2'][value=${array_retorno[39]}]`).click();
                    $("#lbl_unidade_2_2").text(array_retorno[39]);

                    $("#ler_2_2").show();
                }else if(array_retorno[37] != '' && array_retorno[38] == '0.000'){
                    $("#mais_med_2_1_O").click();
                    $("#nome_prod_2_2").val(array_retorno[37]);
                    $("#lbl_nome_prod_2_2").text(array_retorno[37]);
                    $("#lbl_quantidade_2_2").text('');
                    $("#lbl_unidade_2_2").text(array_retorno[39]);

                    $("#ler_2_2").show();
                }else{
                    $("#mais_med_2_1_N").click();
                    $("#lbl_nome_prod_2_2").text('');
                    $("#lbl_quantidade_2_2").text('');
                    $("#lbl_unidade_2_2").text('');

                    $("#ler_2_2").hide();
                }
                if(array_retorno[40] != '' && array_retorno[41] != '0.000'){
                    $("#mais_med_2_2_M").click();
                    $("#nome_prod_2_3").val(array_retorno[40]);
                    $("#lbl_nome_prod_2_3").text(array_retorno[40]);
                    $("#quantidade_2_3").val(array_retorno[41]);
                    $("#lbl_quantidade_2_3").text(array_retorno[41]);
                    $(`input[name='unidade_2_3'][value=${array_retorno[42]}]`).prop("checked", true);
                    $("#lbl_unidade_2_3").text(array_retorno[42]);

                    $("#ler_2_3").show();
                }else if(array_retorno[40] != '' && array_retorno[41] == '0.000'){
                    $("#mais_med_2_2_O").click();
                    $("#nome_prod_2_3").val(array_retorno[40]);
                    $("#lbl_nome_prod_2_3").text(array_retorno[40]);
                    $("#lbl_quantidade_2_3").text('');
                    $("#lbl_unidade_2_3").text(array_retorno[42]);

                    $("#ler_2_3").show();
                }else{
                    $("#mais_med_2_2_N").click();
                    $("#lbl_nome_prod_2_3").text('');
                    $("#lbl_quantidade_2_3").text('');
                    $("#lbl_unidade_2_3").text('');

                    $("#ler_2_3").hide();
                }
            }else if(array_retorno[0] == 4){
                //produto 0
                $("#codigo_item_0").val(array_retorno[1]);
                /* $("#numero_dias_0").val(""); */
                var descricao = array_retorno[16]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_0").val(descCorrigida);
                $("#numero_dias_1").val("");
                $("#numero_dias_0").change();

                $("#nome_prod_0").val(array_retorno[3]);
                $("#lbl_nome_prod_0").text(array_retorno[3]);
                if(array_retorno[4] == '0.000'){
                    $("#lbl_quantidade_0").text('');
                    $("#quantidade_0").val('');
                }else{
                    $("#lbl_quantidade_0").text(array_retorno[4]);
                    $("#quantidade_0").val(array_retorno[4]);
                }
                if(array_retorno[5] != ''){
                    $(`input[name='unidade_0'][value=${array_retorno[5]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_0']`).prop("checked", false);
                }
                $("#lbl_unidade_0").text(array_retorno[5]);

                if(array_retorno[6] != '' && array_retorno[7] != '0.000'){
                    $("#mais_med_0_M").click();
                    $("#nome_prod_0_1").val(array_retorno[6]);
                    $("#lbl_nome_prod_0_1").text(array_retorno[6]);
                    $("input#quantidade_0_1").val(array_retorno[7]);
                    $("#lbl_quantidade_0_1").text(array_retorno[7]);
                    $(`input[name='unidade_0_1'][value=${array_retorno[8]}]`).click();
                    $("#lbl_unidade_0_1").text(array_retorno[8]);

                    $("#ler_0_1").show();
                }else if(array_retorno[6] != '' && array_retorno[7] == '0.000'){
                    $("#mais_med_0_O").click();
                    $("#nome_prod_0_1").val(array_retorno[6]);
                    $("#lbl_nome_prod_0_1").text(array_retorno[6]);
                    $("#lbl_quantidade_0_1").text('');
                    $("#lbl_unidade_0_1").text(array_retorno[8]);

                    $("#ler_0_1").show();
                }else{
                    $("#mais_med_0_N").click();
                    $("#lbl_nome_prod_0_1").text('');
                    $("#lbl_quantidade_0_1").text('');
                    $("#lbl_unidade_0_1").text('');

                    $("#ler_0_1").hide();
                }
                if(array_retorno[9] != '' && array_retorno[10] != '0.000'){
                    $("#mais_med_0_1_M").click();
                    $("#nome_prod_0_2").val(array_retorno[9]);
                    $("#lbl_nome_prod_0_2").text(array_retorno[9]);
                    $("#quantidade_0_2").val(array_retorno[10]);
                    $("#lbl_quantidade_0_2").text(array_retorno[10]);
                    $(`input[name='unidade_0_2'][value=${array_retorno[11]}]`).click();
                    $("#lbl_unidade_0_2").text(array_retorno[11]);

                    $("#ler_0_2").show();
                }else if(array_retorno[9] != '' && array_retorno[10] == '0.000'){
                    $("#mais_med_0_1_O").click();
                    $("#nome_prod_0_2").val(array_retorno[9]);
                    $("#lbl_nome_prod_0_2").text(array_retorno[9]);
                    $("#lbl_quantidade_0_2").text('');
                    $("#lbl_unidade_0_2").text(array_retorno[11]);

                    $("#ler_0_2").show();
                }else{
                    $("#mais_med_0_1_N").click();
                    $("#lbl_nome_prod_0_2").text('');
                    $("#lbl_quantidade_0_2").text('');
                    $("#lbl_unidade_0_2").text('');

                    $("#ler_0_2").hide();
                }
                if(array_retorno[12] != '' && array_retorno[13] != '0.000'){
                    $("#mais_med_0_2_M").click();
                    $("#mais_med_0_3_N").click();
                    $("#nome_prod_0_3").val(array_retorno[12]);
                    $("#lbl_nome_prod_0_3").text(array_retorno[12]);
                    $("#quantidade_0_3").val(array_retorno[13]);
                    $("#lbl_quantidade_0_3").text(array_retorno[13]);
                    $(`input[name='unidade_0_3'][value=${array_retorno[14]}]`).prop("checked", true);
                    $("#lbl_unidade_0_3").text(array_retorno[14]);

                    $("#ler_0_3").show();
                }else if(array_retorno[12] != '' && array_retorno[13] == '0.000'){
                    $("#mais_med_0_2_O").click();
                    $("#mais_med_0_3_N").click();
                    $("#nome_prod_0_3").val(array_retorno[12]);
                    $("#lbl_nome_prod_0_3").text(array_retorno[12]);
                    $("#lbl_quantidade_0_3").text('');
                    $("#lbl_unidade_0_3").text(array_retorno[14]);

                    $("#ler_0_3").show();
                }else{
                    $("#mais_med_0_2_N").click();
                    $("#lbl_nome_prod_0_3").text('');
                    $("#lbl_quantidade_0_3").text('');
                    $("#lbl_unidade_0_3").text('');

                    $("#ler_0_3").hide();
                }
                //produto 1
                if($("#tipo_gravacao").val() == '1'){
                    $("#enviar_lixeira_1").show();
                    $("#lixeira_item_1").val('');
                }
                $("div#r1").show();
                $("div#ler_1").show();
                $("#codigo_item_1").val(array_retorno[15]);
                $("#lbl_descricao_1").text(array_retorno[16]);
                /* var descricao = array_retorno[16]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_0").val(descCorrigida);
                $("#numero_dias_1").val("");
                $("#numero_dias_0").change(); */

                var descricao = array_retorno[30]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_1").val(descCorrigida);
                $("#numero_dias_2").val("");
                $("#numero_dias_1").change();

                $("#nome_prod_1").val(array_retorno[17]);
                $("#lbl_nome_prod_1").text(array_retorno[17]);
                if(array_retorno[18] == '0.000'){
                    $("#lbl_quantidade_1").text('');
                    $("#quantidade_1").val('');
                }else{
                    $("#lbl_quantidade_1").text(array_retorno[18]);
                    $("#quantidade_1").val(array_retorno[18]);
                }
                if(array_retorno[19] != ''){
                    $(`input[name='unidade_1'][value=${array_retorno[19]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_1']`).prop("checked", false);
                }
                $("#lbl_unidade_1").text(array_retorno[19]);

                if(array_retorno[20] != '' && array_retorno[21] != '0.000'){
                    $("#mais_med_1_M").click();
                    $("#nome_prod_1_1").val(array_retorno[20]);
                    $("#lbl_nome_prod_1_1").text(array_retorno[20]);
                    $("input#quantidade_1_1").val(array_retorno[21]);
                    $("#lbl_quantidade_1_1").text(array_retorno[21]);
                    $(`input[name='unidade_1_1'][value=${array_retorno[22]}]`).click();
                    $("#lbl_unidade_1_1").text(array_retorno[22]);

                    $("#ler_1_1").show();
                }else if(array_retorno[20] != '' && array_retorno[21] == '0.000'){
                    $("#mais_med_1_O").click();
                    $("#nome_prod_1_1").val(array_retorno[20]);
                    $("#lbl_nome_prod_1_1").text(array_retorno[20]);
                    $("#lbl_quantidade_1_1").text('');
                    $("#lbl_unidade_1_1").text(array_retorno[22]);

                    $("#ler_1_1").show();
                }else{
                    $("#mais_med_1_N").click();
                    $("#lbl_nome_prod_1_1").text('');
                    $("#lbl_quantidade_1_1").text('');
                    $("#lbl_unidade_1_1").text('');

                    $("#ler_1_1").hide();
                }
                if(array_retorno[23] != '' && array_retorno[24] != '0.000'){
                    $("#mais_med_1_1_M").click();
                    $("#nome_prod_1_2").val(array_retorno[23]);
                    $("#lbl_nome_prod_1_2").text(array_retorno[23]);
                    $("#quantidade_1_2").val(array_retorno[24]);
                    $("#lbl_quantidade_1_2").text(array_retorno[24]);
                    $(`input[name='unidade_1_2'][value=${array_retorno[25]}]`).click();
                    $("#lbl_unidade_1_2").text(array_retorno[25]);

                    $("#ler_1_2").show();
                }else if(array_retorno[23] != '' && array_retorno[24] == '0.000'){
                    $("#mais_med_1_1_O").click();
                    $("#nome_prod_1_2").val(array_retorno[23]);
                    $("#lbl_nome_prod_1_2").text(array_retorno[23]);
                    $("#lbl_quantidade_1_2").text('');
                    $("#lbl_unidade_1_2").text(array_retorno[25]);

                    $("#ler_1_2").show();
                }else{
                    $("#mais_med_1_1_N").click();
                    $("#lbl_nome_prod_1_2").text('');
                    $("#lbl_quantidade_1_2").text('');
                    $("#lbl_unidade_1_2").text('');

                    $("#ler_1_2").hide();
                }
                if(array_retorno[26] != '' && array_retorno[27] != '0.000'){
                    $("#mais_med_1_2_M").click();
                    $("#mais_med_1_3_N").click();
                    $("#nome_prod_1_3").val(array_retorno[26]);
                    $("#lbl_nome_prod_1_3").text(array_retorno[26]);
                    $("#quantidade_1_3").val(array_retorno[27]);
                    $("#lbl_quantidade_1_3").text(array_retorno[27]);
                    $(`input[name='unidade_1_3'][value=${array_retorno[28]}]`).prop("checked", true);
                    $("#lbl_unidade_1_3").text(array_retorno[28]);

                    $("#ler_1_3").show();
                }else if(array_retorno[26] != '' && array_retorno[27] == '0.000'){
                    $("#mais_med_1_2_O").click();
                    $("#mais_med_1_3_N").click();
                    $("#nome_prod_1_3").val(array_retorno[26]);
                    $("#lbl_nome_prod_1_3").text(array_retorno[26]);
                    $("#lbl_quantidade_1_3").text('');
                    $("#lbl_unidade_1_3").text(array_retorno[28]);

                    $("#ler_1_3").show();
                }else{
                    $("#mais_med_1_2_N").click();
                    $("#lbl_nome_prod_1_3").text('');
                    $("#lbl_quantidade_1_3").text('');
                    $("#lbl_unidade_1_3").text('');

                    $("#ler_1_3").hide();
                }
                //produto 2
                if($("#tipo_gravacao").val() == '1'){
                    $("#enviar_lixeira_2").show();
                    $("#lixeira_item_2").val('');
                }
                $("div#r2").show();
                $("div#ler_2").show();
                $("#codigo_item_2").val(array_retorno[29]);
                $("#lbl_descricao_2").text(array_retorno[30]);
                /* var descricao = array_retorno[30]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_1").val(descCorrigida);
                $("#numero_dias_2").val("");
                $("#numero_dias_1").change(); */

                var descricao = array_retorno[44]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_2").val(descCorrigida);
                $("#numero_dias_3").val("");
                $("#numero_dias_2").change();

                $("#nome_prod_2").val(array_retorno[31]);
                $("#lbl_nome_prod_2").text(array_retorno[31]);
                if(array_retorno[32] == '0.000'){
                    $("#lbl_quantidade_2").text('');
                    $("#quantidade_2").val('');
                }else{
                    $("#lbl_quantidade_2").text(array_retorno[32]);
                    $("#quantidade_2").val(array_retorno[32]);
                }
                if(array_retorno[33] != ''){
                    $(`input[name='unidade_2'][value=${array_retorno[33]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_2']`).prop("checked", false);
                }
                $("#lbl_unidade_2").text(array_retorno[33]);

                if(array_retorno[34] != '' && array_retorno[35] != '0.000'){
                    $("#mais_med_2_M").click();
                    $("#nome_prod_2_1").val(array_retorno[34]);
                    $("#lbl_nome_prod_2_1").text(array_retorno[34]);
                    $("input#quantidade_2_1").val(array_retorno[35]);
                    $("#lbl_quantidade_2_1").text(array_retorno[35]);
                    $(`input[name='unidade_2_1'][value=${array_retorno[36]}]`).click();
                    $("#lbl_unidade_2_1").text(array_retorno[36]);

                    $("#ler_2_1").show();
                }else if(array_retorno[34] != '' && array_retorno[35] == '0.000'){
                    $("#mais_med_2_O").click();
                    $("#nome_prod_2_1").val(array_retorno[34]);
                    $("#lbl_nome_prod_2_1").text(array_retorno[34]);
                    $("#lbl_quantidade_2_1").text('');
                    $("#lbl_unidade_2_1").text(array_retorno[36]);

                    $("#ler_2_1").show();
                }else{
                    $("#mais_med_2_N").click();
                    $("#lbl_nome_prod_2_1").text('');
                    $("#lbl_quantidade_2_1").text('');
                    $("#lbl_unidade_2_1").text('');

                    $("#ler_2_1").hide();
                }
                if(array_retorno[37] != '' && array_retorno[38] != '0.000'){
                    $("#mais_med_2_1_M").click();
                    $("#nome_prod_2_2").val(array_retorno[37]);
                    $("#lbl_nome_prod_2_2").text(array_retorno[37]);
                    $("#quantidade_2_2").val(array_retorno[38]);
                    $("#lbl_quantidade_2_2").text(array_retorno[38]);
                    $(`input[name='unidade_2_2'][value=${array_retorno[39]}]`).click();
                    $("#lbl_unidade_2_2").text(array_retorno[39]);

                    $("#ler_2_2").show();
                }else if(array_retorno[37] != '' && array_retorno[38] == '0.000'){
                    $("#mais_med_2_1_O").click();
                    $("#nome_prod_2_2").val(array_retorno[37]);
                    $("#lbl_nome_prod_2_2").text(array_retorno[37]);
                    $("#lbl_quantidade_2_2").text('');
                    $("#lbl_unidade_2_2").text(array_retorno[39]);

                    $("#ler_2_2").show();
                }else{
                    $("#mais_med_2_1_N").click();
                    $("#lbl_nome_prod_2_2").text('');
                    $("#lbl_quantidade_2_2").text('');
                    $("#lbl_unidade_2_2").text('');

                    $("#ler_2_2").hide();
                }
                if(array_retorno[40] != '' && array_retorno[41] != '0.000'){
                    $("#mais_med_2_2_M").click();
                    $("#mais_med_2_3_N").click();
                    $("#nome_prod_2_3").val(array_retorno[40]);
                    $("#lbl_nome_prod_2_3").text(array_retorno[40]);
                    $("#quantidade_2_3").val(array_retorno[41]);
                    $("#lbl_quantidade_2_3").text(array_retorno[41]);
                    $(`input[name='unidade_2_3'][value=${array_retorno[42]}]`).prop("checked", true);
                    $("#lbl_unidade_2_3").text(array_retorno[42]);

                    $("#ler_2_3").show();
                }else if(array_retorno[40] != '' && array_retorno[41] == '0.000'){
                    $("#mais_med_2_2_O").click();
                    $("#mais_med_2_3_N").click();
                    $("#nome_prod_2_3").val(array_retorno[40]);
                    $("#lbl_nome_prod_2_3").text(array_retorno[40]);
                    $("#lbl_quantidade_2_3").text('');
                    $("#lbl_unidade_2_3").text(array_retorno[42]);

                    $("#ler_2_3").show();
                }else{
                    $("#mais_med_2_2_N").click();
                    $("#lbl_nome_prod_2_3").text('');
                    $("#lbl_quantidade_2_3").text('');
                    $("#lbl_unidade_2_3").text('');
                    $("#ler_2_3").hide();
                }
                //produto 3
                if($("#tipo_gravacao").val() == '1'){
                    $("#enviar_lixeira_3").show();
                    $("#lixeira_item_3").val('');
                }
                mostrar_linhas("numero_dias_3", "");
                $("div#r3").show();
                $("div#ler_3").show();
                $("#codigo_item_3").val(array_retorno[43]);
                $("#lbl_descricao_3").text(array_retorno[44]);
                /* var descricao = array_retorno[44]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_2").val(descCorrigida);
                $("#numero_dias_3").val("");
                $("#numero_dias_2").change(); */
                $("#nome_prod_3").val(array_retorno[45]);
                $("#lbl_nome_prod_3").text(array_retorno[45]);
                if(array_retorno[46] == '0.000'){
                    $("#lbl_quantidade_3").text('');
                    $("#quantidade_3").val('');
                }else{
                    $("#lbl_quantidade_3").text(array_retorno[46]);
                    $("#quantidade_3").val(array_retorno[46]);
                }
                if(array_retorno[47] != ''){
                    $(`input[name='unidade_3'][value=${array_retorno[47]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_3']`).prop("checked", false);
                }
                $("#lbl_unidade_3").text(array_retorno[47]);

                if(array_retorno[48] != '' && array_retorno[49] != '0.000'){
                    $("#mais_med_3_M").click();
                    $("#nome_prod_3_1").val(array_retorno[48]);
                    $("#lbl_nome_prod_3_1").text(array_retorno[48]);
                    $("input#quantidade_3_1").val(array_retorno[49]);
                    $("#lbl_quantidade_3_1").text(array_retorno[49]);
                    $(`input[name='unidade_3_1'][value=${array_retorno[50]}]`).click();
                    $("#lbl_unidade_3_1").text(array_retorno[50]);

                    $("#ler_3_1").show();
                }else if(array_retorno[48] != '' && array_retorno[49] == '0.000'){
                    $("#mais_med_3_O").click();
                    $("#nome_prod_3_1").val(array_retorno[48]);
                    $("#lbl_nome_prod_3_1").text(array_retorno[48]);
                    $("#lbl_quantidade_3_1").text('');
                    $("#lbl_unidade_3_1").text(array_retorno[50]);

                    $("#ler_3_1").show();
                }else{
                    $("#mais_med_3_N").click();
                    $("#lbl_nome_prod_3_1").text('');
                    $("#lbl_quantidade_3_1").text('');
                    $("#lbl_unidade_3_1").text('');

                    $("#ler_3_1").hide();
                }
                if(array_retorno[51] != '' && array_retorno[52] != '0.000'){
                    $("#mais_med_3_1_M").click();
                    $("#nome_prod_3_2").val(array_retorno[51]);
                    $("#lbl_nome_prod_3_2").text(array_retorno[51]);
                    $("#quantidade_3_2").val(array_retorno[52]);
                    $("#lbl_quantidade_3_2").text(array_retorno[52]);
                    $(`input[name='unidade_3_2'][value=${array_retorno[53]}]`).click();
                    $("#lbl_unidade_3_2").text(array_retorno[53]);

                    $("#ler_3_2").show();
                }else if(array_retorno[51] != '' && array_retorno[52] == '0.000'){
                    $("#mais_med_3_1_O").click();
                    $("#nome_prod_3_2").val(array_retorno[51]);
                    $("#lbl_nome_prod_3_2").text(array_retorno[51]);
                    $("#lbl_quantidade_3_2").text('');
                    $("#lbl_unidade_3_2").text(array_retorno[53]);

                    $("#ler_3_2").show();
                }else{
                    $("#mais_med_3_1_N").click();
                    $("#lbl_nome_prod_3_2").text('');
                    $("#lbl_quantidade_3_2").text('');
                    $("#lbl_unidade_3_2").text('');

                    $("#ler_3_2").hide();
                }
                if(array_retorno[54] != '' && array_retorno[55] != '0.000'){
                    $("#mais_med_3_2_M").click();
                    $("#nome_prod_3_3").val(array_retorno[54]);
                    $("#lbl_nome_prod_3_3").text(array_retorno[54]);
                    $("#quantidade_3_3").val(array_retorno[55]);
                    $("#lbl_quantidade_3_3").text(array_retorno[55]);
                    $(`input[name='unidade_3_3'][value=${array_retorno[56]}]`).prop("checked", true);
                    $("#lbl_unidade_3_3").text(array_retorno[56]);

                    $("#ler_3_3").show();
                }else if(array_retorno[54] != '' && array_retorno[55] == '0.000'){
                    $("#mais_med_3_2_O").click();
                    $("#nome_prod_3_3").val(array_retorno[54]);
                    $("#lbl_nome_prod_3_3").text(array_retorno[54]);
                    $("#lbl_quantidade_3_3").text('');
                    $("#lbl_unidade_3_3").text(array_retorno[56]);

                    $("#ler_3_3").show();
                }else{
                    $("#mais_med_3_2_N").click();
                    $("#lbl_nome_prod_3_3").text('');
                    $("#lbl_quantidade_3_3").text('');
                    $("#lbl_unidade_3_3").text('');

                    $("#ler_3_3").hide();
                }
            }else{
                //produto 0
                $("#codigo_item_0").val(array_retorno[1]);
                /* $("#numero_dias_0").val(""); */
                var descricao = array_retorno[16]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_0").val(descCorrigida);
                $("#numero_dias_1").val("");
                $("#numero_dias_0").change();

                $("#nome_prod_0").val(array_retorno[3]);
                $("#lbl_nome_prod_0").text(array_retorno[3]);
                if(array_retorno[4] == '0.000'){
                    $("#lbl_quantidade_0").text('');
                    $("#quantidade_0").val('');
                }else{
                    $("#lbl_quantidade_0").text(array_retorno[4]);
                    $("#quantidade_0").val(array_retorno[4]);
                }
                if(array_retorno[5] != ''){
                    $(`input[name='unidade_0'][value=${array_retorno[5]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_0']`).prop("checked", false);
                }
                $("#lbl_unidade_0").text(array_retorno[5]);

                if(array_retorno[6] != '' && array_retorno[7] != '0.000'){
                    $("#mais_med_0_M").click();
                    $("#nome_prod_0_1").val(array_retorno[6]);
                    $("#lbl_nome_prod_0_1").text(array_retorno[6]);
                    $("input#quantidade_0_1").val(array_retorno[7]);
                    $("#lbl_quantidade_0_1").text(array_retorno[7]);
                    $(`input[name='unidade_0_1'][value=${array_retorno[8]}]`).click();
                    $("#lbl_unidade_0_1").text(array_retorno[8]);

                    $("#ler_0_1").show();
                }else if(array_retorno[6] != '' && array_retorno[7] == '0.000'){
                    $("#mais_med_0_O").click();
                    $("#nome_prod_0_1").val(array_retorno[6]);
                    $("#lbl_nome_prod_0_1").text(array_retorno[6]);
                    $("#lbl_quantidade_0_1").text('');
                    $("#lbl_unidade_0_1").text(array_retorno[8]);

                    $("#ler_0_1").show();
                }else{
                    $("#mais_med_0_N").click();
                    $("#lbl_nome_prod_0_1").text('');
                    $("#lbl_quantidade_0_1").text('');
                    $("#lbl_unidade_0_1").text('');

                    $("#ler_0_1").hide();
                }
                if(array_retorno[9] != '' && array_retorno[10] != '0.000'){
                    $("#mais_med_0_1_M").click();
                    $("#nome_prod_0_2").val(array_retorno[9]);
                    $("#lbl_nome_prod_0_2").text(array_retorno[9]);
                    $("#quantidade_0_2").val(array_retorno[10]);
                    $("#lbl_quantidade_0_2").text(array_retorno[10]);
                    $(`input[name='unidade_0_2'][value=${array_retorno[11]}]`).click();
                    $("#lbl_unidade_0_2").text(array_retorno[11]);

                    $("#ler_0_2").show();
                }else if(array_retorno[9] != '' && array_retorno[10] == '0.000'){
                    $("#mais_med_0_1_O").click();
                    $("#nome_prod_0_2").val(array_retorno[9]);
                    $("#lbl_nome_prod_0_2").text(array_retorno[9]);
                    $("#lbl_quantidade_0_2").text('');
                    $("#lbl_unidade_0_2").text(array_retorno[11]);

                    $("#ler_0_2").show();
                }else{
                    $("#mais_med_0_1_N").click();
                    $("#lbl_nome_prod_0_2").text('');
                    $("#lbl_quantidade_0_2").text('');
                    $("#lbl_unidade_0_2").text('');

                    $("#ler_0_2").hide();
                }
                if(array_retorno[12] != '' && array_retorno[13] != '0.000'){
                    $("#mais_med_0_2_M").click();
                    $("#mais_med_0_3_N").click();
                    $("#nome_prod_0_3").val(array_retorno[12]);
                    $("#lbl_nome_prod_0_3").text(array_retorno[12]);
                    $("#quantidade_0_3").val(array_retorno[13]);
                    $("#lbl_quantidade_0_3").text(array_retorno[13]);
                    $(`input[name='unidade_0_3'][value=${array_retorno[14]}]`).prop("checked", true);
                    $("#lbl_unidade_0_3").text(array_retorno[14]);

                    $("#ler_0_3").show();
                }else if(array_retorno[12] != '' && array_retorno[13] == '0.000'){
                    $("#mais_med_0_2_O").click();
                    $("#mais_med_0_3_N").click();
                    $("#nome_prod_0_3").val(array_retorno[12]);
                    $("#lbl_nome_prod_0_3").text(array_retorno[12]);
                    $("#lbl_quantidade_0_3").text('');
                    $("#lbl_unidade_0_3").text(array_retorno[14]);

                    $("#ler_0_3").show();
                }else{
                    $("#mais_med_0_2_N").click();
                    $("#lbl_nome_prod_0_3").text('');
                    $("#lbl_quantidade_0_3").text('');
                    $("#lbl_unidade_0_3").text('');

                    $("#ler_0_3").hide();
                }
                //produto 1
                if($("#tipo_gravacao").val() == '1'){
                    $("#enviar_lixeira_1").show();
                    $("#lixeira_item_1").val('');
                }
                $("div#r1").show();
                $("div#ler_1").show();
                $("#codigo_item_1").val(array_retorno[15]);
                $("#lbl_descricao_1").text(array_retorno[16]);

                var descricao = array_retorno[30]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_1").val(descCorrigida);
                $("#numero_dias_2").val("");
                $("#numero_dias_1").change();

                $("#nome_prod_1").val(array_retorno[17]);
                $("#lbl_nome_prod_1").text(array_retorno[17]);
                if(array_retorno[18] == '0.000'){
                    $("#lbl_quantidade_1").text('');
                    $("#quantidade_1").val('');
                }else{
                    $("#lbl_quantidade_1").text(array_retorno[18]);
                    $("#quantidade_1").val(array_retorno[18]);
                }
                if(array_retorno[19] != ''){
                    $(`input[name='unidade_1'][value=${array_retorno[19]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_1']`).prop("checked", false);
                }
                $("#lbl_unidade_1").text(array_retorno[19]);

                if(array_retorno[20] != '' && array_retorno[21] != '0.000'){
                    $("#mais_med_1_M").click();
                    $("#nome_prod_1_1").val(array_retorno[20]);
                    $("#lbl_nome_prod_1_1").text(array_retorno[20]);
                    $("input#quantidade_1_1").val(array_retorno[21]);
                    $("#lbl_quantidade_1_1").text(array_retorno[21]);
                    $(`input[name='unidade_1_1'][value=${array_retorno[22]}]`).click();
                    $("#lbl_unidade_1_1").text(array_retorno[22]);

                    $("#ler_1_1").show();
                }else if(array_retorno[20] != '' && array_retorno[21] == '0.000'){
                    $("#mais_med_1_O").click();
                    $("#nome_prod_1_1").val(array_retorno[20]);
                    $("#lbl_nome_prod_1_1").text(array_retorno[20]);
                    $("#lbl_quantidade_1_1").text('');
                    $("#lbl_unidade_1_1").text(array_retorno[22]);

                    $("#ler_1_1").show();
                }else{
                    $("#mais_med_1_N").click();
                    $("#lbl_nome_prod_1_1").text('');
                    $("#lbl_quantidade_1_1").text('');
                    $("#lbl_unidade_1_1").text('');

                    $("#ler_1_1").hide();
                }
                if(array_retorno[23] != '' && array_retorno[24] != '0.000'){
                    $("#mais_med_1_1_M").click();
                    $("#nome_prod_1_2").val(array_retorno[23]);
                    $("#lbl_nome_prod_1_2").text(array_retorno[23]);
                    $("#quantidade_1_2").val(array_retorno[24]);
                    $("#lbl_quantidade_1_2").text(array_retorno[24]);
                    $(`input[name='unidade_1_2'][value=${array_retorno[25]}]`).click();
                    $("#lbl_unidade_1_2").text(array_retorno[25]);

                    $("#ler_1_2").show();
                }else if(array_retorno[23] != '' && array_retorno[24] == '0.000'){
                    $("#mais_med_1_1_O").click();
                    $("#nome_prod_1_2").val(array_retorno[23]);
                    $("#lbl_nome_prod_1_2").text(array_retorno[23]);
                    $("#lbl_quantidade_1_2").text('');
                    $("#lbl_unidade_1_2").text(array_retorno[25]);

                    $("#ler_1_2").show();
                }else{
                    $("#mais_med_1_1_N").click();
                    $("#lbl_nome_prod_1_2").text('');
                    $("#lbl_quantidade_1_2").text('');
                    $("#lbl_unidade_1_2").text('');

                    $("#ler_1_2").hide();
                }
                if(array_retorno[26] != '' && array_retorno[27] != '0.000'){
                    $("#mais_med_1_2_M").click();
                    $("#mais_med_1_3_N").click();
                    $("#nome_prod_1_3").val(array_retorno[26]);
                    $("#lbl_nome_prod_1_3").text(array_retorno[26]);
                    $("#quantidade_1_3").val(array_retorno[27]);
                    $("#lbl_quantidade_1_3").text(array_retorno[27]);
                    $(`input[name='unidade_1_3'][value=${array_retorno[28]}]`).prop("checked", true);
                    $("#lbl_unidade_1_3").text(array_retorno[28]);

                    $("#ler_1_3").show();
                }else if(array_retorno[26] != '' && array_retorno[27] == '0.000'){
                    $("#mais_med_1_2_O").click();
                    $("#mais_med_1_3_N").click();
                    $("#nome_prod_1_3").val(array_retorno[26]);
                    $("#lbl_nome_prod_1_3").text(array_retorno[26]);
                    $("#lbl_quantidade_1_3").text('');
                    $("#lbl_unidade_1_3").text(array_retorno[28]);

                    $("#ler_1_3").show();
                }else{
                    $("#mais_med_1_2_N").click();
                    $("#lbl_nome_prod_1_3").text('');
                    $("#lbl_quantidade_1_3").text('');
                    $("#lbl_unidade_1_3").text('');

                    $("#ler_1_3").hide();
                }
                //produto 2
                if($("#tipo_gravacao").val() == '1'){
                    $("#enviar_lixeira_2").show();
                    $("#lixeira_item_2").val('');
                }
                $("div#r2").show();
                $("div#ler_2").show();
                $("#codigo_item_2").val(array_retorno[29]);
                $("#lbl_descricao_2").text(array_retorno[30]);

                var descricao = array_retorno[44]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_2").val(descCorrigida);
                $("#numero_dias_3").val("");
                $("#numero_dias_2").change();

                $("#nome_prod_2").val(array_retorno[31]);
                $("#lbl_nome_prod_2").text(array_retorno[31]);
                if(array_retorno[32] == '0.000'){
                    $("#lbl_quantidade_2").text('');
                    $("#quantidade_2").val('');
                }else{
                    $("#lbl_quantidade_2").text(array_retorno[32]);
                    $("#quantidade_2").val(array_retorno[32]);
                }
                if(array_retorno[33] != ''){
                    $(`input[name='unidade_2'][value=${array_retorno[33]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_2']`).prop("checked", false);
                }
                $("#lbl_unidade_2").text(array_retorno[33]);

                if(array_retorno[34] != '' && array_retorno[35] != '0.000'){
                    $("#mais_med_2_M").click();
                    $("#nome_prod_2_1").val(array_retorno[34]);
                    $("#lbl_nome_prod_2_1").text(array_retorno[34]);
                    $("input#quantidade_2_1").val(array_retorno[35]);
                    $("#lbl_quantidade_2_1").text(array_retorno[35]);
                    $(`input[name='unidade_2_1'][value=${array_retorno[36]}]`).click();
                    $("#lbl_unidade_2_1").text(array_retorno[36]);

                    $("#ler_2_1").show();
                }else if(array_retorno[34] != '' && array_retorno[35] == '0.000'){
                    $("#mais_med_2_O").click();
                    $("#nome_prod_2_1").val(array_retorno[34]);
                    $("#lbl_nome_prod_2_1").text(array_retorno[34]);
                    $("#lbl_quantidade_2_1").text('');
                    $("#lbl_unidade_2_1").text(array_retorno[36]);

                    $("#ler_2_1").show();
                }else{
                    $("#mais_med_2_N").click();
                    $("#lbl_nome_prod_2_1").text('');
                    $("#lbl_quantidade_2_1").text('');
                    $("#lbl_unidade_2_1").text('');

                    $("#ler_2_1").hide();
                }
                if(array_retorno[37] != '' && array_retorno[38] != '0.000'){
                    $("#mais_med_2_1_M").click();
                    $("#nome_prod_2_2").val(array_retorno[37]);
                    $("#lbl_nome_prod_2_2").text(array_retorno[37]);
                    $("#quantidade_2_2").val(array_retorno[38]);
                    $("#lbl_quantidade_2_2").text(array_retorno[38]);
                    $(`input[name='unidade_2_2'][value=${array_retorno[39]}]`).click();
                    $("#lbl_unidade_2_2").text(array_retorno[39]);

                    $("#ler_2_2").show();
                }else if(array_retorno[37] != '' && array_retorno[38] == '0.000'){
                    $("#mais_med_2_1_O").click();
                    $("#nome_prod_2_2").val(array_retorno[37]);
                    $("#lbl_nome_prod_2_2").text(array_retorno[37]);
                    $("#lbl_quantidade_2_2").text('');
                    $("#lbl_unidade_2_2").text(array_retorno[39]);

                    $("#ler_2_2").show();
                }else{
                    $("#mais_med_2_1_N").click();
                    $("#lbl_nome_prod_2_2").text('');
                    $("#lbl_quantidade_2_2").text('');
                    $("#lbl_unidade_2_2").text('');

                    $("#ler_2_2").hide();
                }
                if(array_retorno[40] != '' && array_retorno[41] != '0.000'){
                    $("#mais_med_2_2_M").click();
                    $("#mais_med_2_3_N").click();
                    $("#nome_prod_2_3").val(array_retorno[40]);
                    $("#lbl_nome_prod_2_3").text(array_retorno[40]);
                    $("#quantidade_2_3").val(array_retorno[41]);
                    $("#lbl_quantidade_2_3").text(array_retorno[41]);
                    $(`input[name='unidade_2_3'][value=${array_retorno[42]}]`).prop("checked", true);
                    $("#lbl_unidade_2_3").text(array_retorno[42]);

                    $("#ler_2_3").show();
                }else if(array_retorno[40] != '' && array_retorno[41] == '0.000'){
                    $("#mais_med_2_2_O").click();
                    $("#mais_med_2_3_N").click();
                    $("#nome_prod_2_3").val(array_retorno[40]);
                    $("#lbl_nome_prod_2_3").text(array_retorno[40]);
                    $("#lbl_quantidade_2_3").text('');
                    $("#lbl_unidade_2_3").text(array_retorno[42]);

                    $("#ler_2_3").show();
                }else{
                    $("#mais_med_2_2_N").click();
                    $("#lbl_nome_prod_2_3").text('');
                    $("#lbl_quantidade_2_3").text('');
                    $("#lbl_unidade_2_3").text('');

                    $("#ler_2_3").hide();
                }
                //produto 3
                if($("#tipo_gravacao").val() == '1'){
                    $("#enviar_lixeira_3").show();
                    $("#lixeira_item_3").val('');
                }
                $("div#r3").show();
                $("div#ler_3").show();
                $("#codigo_item_3").val(array_retorno[43]);
                $("#lbl_descricao_3").text(array_retorno[44]);

                var descricao = array_retorno[58]
                var descCorrigida = descricao.split(" ")[1];
                $("#numero_dias_3").val(descCorrigida);
                /* $("#numero_dias_4").val(""); */
                $("#numero_dias_3").change();

                $("#nome_prod_3").val(array_retorno[45]);
                $("#lbl_nome_prod_3").text(array_retorno[45]);
                if(array_retorno[46] == '0.000'){
                    $("#lbl_quantidade_3").text('');
                    $("#quantidade_3").val('');
                }else{
                    $("#lbl_quantidade_3").text(array_retorno[46]);
                    $("#quantidade_3").val(array_retorno[46]);
                }
                if(array_retorno[47] != ''){
                    $(`input[name='unidade_3'][value=${array_retorno[47]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_3']`).prop("checked", false);
                }
                $("#lbl_unidade_3").text(array_retorno[47]);

                if(array_retorno[48] != '' && array_retorno[49] != '0.000'){
                    $("#mais_med_3_M").click();
                    $("#nome_prod_3_1").val(array_retorno[48]);
                    $("#lbl_nome_prod_3_1").text(array_retorno[48]);
                    $("input#quantidade_3_1").val(array_retorno[49]);
                    $("#lbl_quantidade_3_1").text(array_retorno[49]);
                    $(`input[name='unidade_3_1'][value=${array_retorno[50]}]`).click();
                    $("#lbl_unidade_3_1").text(array_retorno[50]);

                    $("#ler_3_1").show();
                }else if(array_retorno[48] != '' && array_retorno[49] == '0.000'){
                    $("#mais_med_3_O").click();
                    $("#nome_prod_3_1").val(array_retorno[48]);
                    $("#lbl_nome_prod_3_1").text(array_retorno[48]);
                    $("#lbl_quantidade_3_1").text('');
                    $("#lbl_unidade_3_1").text(array_retorno[50]);

                    $("#ler_3_1").show();
                }else{
                    $("#mais_med_3_N").click();
                    $("#lbl_nome_prod_3_1").text('');
                    $("#lbl_quantidade_3_1").text('');
                    $("#lbl_unidade_3_1").text('');

                    $("#ler_3_1").hide();
                }
                if(array_retorno[51] != '' && array_retorno[52] != '0.000'){
                    $("#mais_med_3_1_M").click();
                    $("#nome_prod_3_2").val(array_retorno[51]);
                    $("#lbl_nome_prod_3_2").text(array_retorno[51]);
                    $("#quantidade_3_2").val(array_retorno[52]);
                    $("#lbl_quantidade_3_2").text(array_retorno[52]);
                    $(`input[name='unidade_3_2'][value=${array_retorno[53]}]`).click();
                    $("#lbl_unidade_3_2").text(array_retorno[53]);

                    $("#ler_3_2").show();
                }else if(array_retorno[51] != '' && array_retorno[52] == '0.000'){
                    $("#mais_med_3_1_O").click();
                    $("#nome_prod_3_2").val(array_retorno[51]);
                    $("#lbl_nome_prod_3_2").text(array_retorno[51]);
                    $("#lbl_quantidade_3_2").text('');
                    $("#lbl_unidade_3_2").text(array_retorno[53]);

                    $("#ler_3_2").show();
                }else{
                    $("#mais_med_3_1_N").click();
                    $("#lbl_nome_prod_3_2").text('');
                    $("#lbl_quantidade_3_2").text('');
                    $("#lbl_unidade_3_2").text('');

                    $("#ler_3_2").hide();
                }
                if(array_retorno[54] != '' && array_retorno[55] != '0.000'){
                    $("#mais_med_3_2_M").click();
                    $("#mais_med_3_3_N").click();
                    $("#nome_prod_3_3").val(array_retorno[54]);
                    $("#lbl_nome_prod_3_3").text(array_retorno[54]);
                    $("#quantidade_3_3").val(array_retorno[55]);
                    $("#lbl_quantidade_3_3").text(array_retorno[55]);
                    $(`input[name='unidade_3_3'][value=${array_retorno[56]}]`).prop("checked", true);
                    $("#lbl_unidade_3_3").text(array_retorno[56]);

                    $("#ler_3_3").show();
                }else if(array_retorno[54] != '' && array_retorno[55] == '0.000'){
                    $("#mais_med_3_2_O").click();
                    $("#mais_med_3_3_N").click();
                    $("#nome_prod_3_3").val(array_retorno[54]);
                    $("#lbl_nome_prod_3_3").text(array_retorno[54]);
                    $("#lbl_quantidade_3_3").text('');
                    $("#lbl_unidade_3_3").text(array_retorno[56]);

                    $("#ler_3_3").show();
                }else{
                    $("#mais_med_3_2_N").click();
                    $("#lbl_nome_prod_3_3").text('');
                    $("#lbl_quantidade_3_3").text('');
                    $("#lbl_unidade_3_3").text('');

                    $("#ler_3_3").hide();
                }
                //produto 4
                if($("#tipo_gravacao").val() == '1'){
                    $("#enviar_lixeira_4").show();
                    $("#lixeira_item_4").val('');
                }
                $("div#r4").show();
                $("div#ler_4").show();
                $("#codigo_item_4").val(array_retorno[57]);
                $("#lbl_descricao_4").text(array_retorno[58]);

                $("#nome_prod_4").val(array_retorno[59]);
                $("#lbl_nome_prod_4").text(array_retorno[59]);
                if(array_retorno[60] == '0.000'){
                    $("#lbl_quantidade_4").text('');
                    $("#quantidade_4").val('');
                }else{
                    $("#lbl_quantidade_4").text(array_retorno[60]);
                    $("#quantidade_4").val(array_retorno[60]);
                }
                if(array_retorno[61] != ''){
                    $(`input[name='unidade_4'][value=${array_retorno[61]}]`).prop("checked", true);
                }else{
                    $(`input[name='unidade_4']`).prop("checked", false);
                }
                $("#lbl_unidade_4").text(array_retorno[61]);

                if(array_retorno[62] != '' && array_retorno[63] != '0.000'){
                    $("#mais_med_4_M").click();
                    $("#nome_prod_4_1").val(array_retorno[62]);
                    $("#lbl_nome_prod_4_1").text(array_retorno[62]);
                    $("input#quantidade_4_1").val(array_retorno[63]);
                    $("#lbl_quantidade_4_1").text(array_retorno[63]);
                    $(`input[name='unidade_4_1'][value=${array_retorno[64]}]`).click();
                    $("#lbl_unidade_4_1").text(array_retorno[64]);

                    $("#ler_4_1").show();
                }else if(array_retorno[62] != '' && array_retorno[63] == '0.000'){
                    $("#mais_med_4_O").click();
                    $("#nome_prod_4_1").val(array_retorno[62]);
                    $("#lbl_nome_prod_4_1").text(array_retorno[62]);
                    $("#lbl_quantidade_4_1").text('');
                    $("#lbl_unidade_4_1").text(array_retorno[64]);

                    $("#ler_4_1").show();
                }else{
                    $("#mais_med_4_N").click();
                    $("#lbl_nome_prod_4_1").text('');
                    $("#lbl_quantidade_4_1").text('');
                    $("#lbl_unidade_4_1").text('');

                    $("#ler_4_1").hide();
                }
                if(array_retorno[65] != '' && array_retorno[66] != '0.000'){
                    $("#mais_med_4_1_M").click();
                    $("#nome_prod_4_2").val(array_retorno[65]);
                    $("#lbl_nome_prod_4_2").text(array_retorno[65]);
                    $("#quantidade_4_2").val(array_retorno[66]);
                    $("#lbl_quantidade_4_2").text(array_retorno[66]);
                    $(`input[name='unidade_4_2'][value=${array_retorno[67]}]`).click();
                    $("#lbl_unidade_4_2").text(array_retorno[67]);

                    $("#ler_4_2").show();
                }else if(array_retorno[65] != '' && array_retorno[66] == '0.000'){
                    $("#mais_med_4_1_O").click();
                    $("#nome_prod_4_2").val(array_retorno[65]);
                    $("#lbl_nome_prod_4_2").text(array_retorno[65]);
                    $("#lbl_quantidade_4_2").text('');
                    $("#lbl_unidade_4_2").text(array_retorno[67]);

                    $("#ler_4_2").show();
                }else{
                    $("#mais_med_4_1_N").click();
                    $("#lbl_nome_prod_4_2").text('');
                    $("#lbl_quantidade_4_2").text('');
                    $("#lbl_unidade_4_2").text('');

                    $("#ler_4_2").hide();
                }
                if(array_retorno[68] != '' && array_retorno[69] != '0.000'){
                    $("#mais_med_4_2_M").click();
                    $("#nome_prod_4_3").val(array_retorno[68]);
                    $("#lbl_nome_prod_4_3").text(array_retorno[68]);
                    $("#quantidade_4_3").val(array_retorno[69]);
                    $("#lbl_quantidade_4_3").text(array_retorno[69]);
                    $(`input[name='unidade_4_3'][value=${array_retorno[70]}]`).prop("checked", true);
                    $("#lbl_unidade_4_3").text(array_retorno[70]);

                    $("#ler_4_3").show();
                }else if(array_retorno[68] != '' && array_retorno[69] == '0.000'){
                    $("#mais_med_4_2_O").click();
                    $("#nome_prod_4_3").val(array_retorno[68]);
                    $("#lbl_nome_prod_4_3").text(array_retorno[68]);
                    $("#lbl_quantidade_4_3").text('');
                    $("#lbl_unidade_4_3").text(array_retorno[70]);

                    $("#ler_4_3").show();
                }else{
                    $("#mais_med_4_2_N").click();
                    $("#lbl_nome_prod_4_3").text('');
                    $("#lbl_quantidade_4_3").text('');
                    $("#lbl_unidade_4_3").text('');

                    $("#ler_4_3").hide();
                }
            }
        }
    );
}

function enviar_item_lixeira(id){
    if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")){
        if(id == 'enviar_lixeira_1'){
            $("#lixeira_item_1").val('1');
        }else if(id == 'enviar_lixeira_2'){
            $("#lixeira_item_2").val('1');
        }else if(id == 'enviar_lixeira_3'){
            $("#lixeira_item_3").val('1');
        }else if(id == 'enviar_lixeira_4'){
            $("#lixeira_item_4").val('1');
        }

        var dados = {
            "lixeira_item_1": $("#lixeira_item_1").val(),
            "codigo_item_1":  $("#codigo_item_1").val(),
            "lixeira_item_2": $("#lixeira_item_2").val(),
            "codigo_item_2":  $("#codigo_item_2").val(),
            "lixeira_item_3": $("#lixeira_item_3").val(),
            "codigo_item_3":  $("#codigo_item_3").val(),
            "lixeira_item_4": $("#lixeira_item_4").val(),
            "codigo_item_4":  $("#codigo_item_4").val()
        };

        $.post("excluir_item_protocoloIATF.php", dados, function(data){
            if(data.success){
                alert(data.message);
            }else if(data.error){
                alert(data.message);
            }
            ler_itens_protocolo();
            $("#lixeira_item_1").val('');
            $("#lixeira_item_2").val('');
            $("#lixeira_item_3").val('');
            $("#lixeira_item_4").val('');
        });
    }
}

function incluir_novo() {
    $("#codigo_conta").val('000000000');
    $("#nome_protocolo").val('');
    $("#quant_protocolo").val('');
    $("#qtd_dias").val('');
    $("#nome_prod_0").val('');
    $("#quantidade_0").val('');
    $("#numero_dias_0").val('');
    $("#codigo_item_0").val('');
    mostrar_linhas("numero_dias_0", "");
    mais_med("mais_med_0", "N");
    $("#mais_med_0_O").attr("checked", false);
    $("#mais_med_0_M").attr("checked", false);
    $("#mais_med_0_N").attr("checked", false);
    $('input[name="unidade_0"]').attr("checked", false);
    $("#tipo_gravacao").val(0);
    
    $("#enviar_lixeira_1").hide();
    $("#enviar_lixeira_2").hide();
    $("#enviar_lixeira_3").hide();
    $("#enviar_lixeira_4").hide();

    $('#modal_incluir .modal-title').html('Protocolos IATF - Incluir');
    $('.confirma_gravar').html('Confirmar Inclusão').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    /* $('.voltar_inclusao').show();
    $('.voltar').hide(); */
    $("#modal_incluir").modal("show");
}

function editar_protocolo(array_registro){
    $array_conta = array_registro.split('|');
    $("#codigo_conta").val($array_conta[0]);
    $("#nome_protocolo").val($array_conta[1]);
    $("#lbl_nome_protocolo").text($array_conta[1]);
    $("#quant_protocolo").val($array_conta[2]);
    $("#lbl_quant_protocolo").text($array_conta[2]);
    $("#qtd_dias").val($array_conta[7]);
    $("#lbl_dias_diagnostico").text($array_conta[7]);

    $("#tipo_gravacao").val(1);

    ler_itens_protocolo();

    /* mais_med("mais_med_0", "N"); */
    $("#lixeira_item_1").val('');
    $("#lixeira_item_2").val('');
    $("#lixeira_item_3").val('');
    $("#lixeira_item_4").val('');

    
    $('#modal_incluir .modal-title').html('Protocolos IATF - Editar');
    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    /* $('.voltar_inclusao').hide();
    $('.voltar').show(); */
    
    /* $('#modal_incluir').modal('show'); */
    $('#modal_ler_dados').modal('show');
}

function botao_modal_ler(){
    $('#modal_ler_dados').modal('hide');
    $('#modal_incluir').modal('show');
}

function enviar_lixeira(array_registro, opcao){
    $array_conta = array_registro.split('|');
    $("#codigo_conta").val($array_conta[0]);
    $("#nome_protocolo").val($array_conta[1]);
    $("#quant_protocolo").val($array_conta[2]);
    $("#qtd_dias").val($array_conta[7]);

    $("#tipo_gravacao").val(opcao);

    ler_itens_protocolo();

    $("#enviar_lixeira_1").hide();
    $("#enviar_lixeira_2").hide();
    $("#enviar_lixeira_3").hide();
    $("#enviar_lixeira_4").hide();

    if (opcao==2) {
        $('#modal_incluir .modal-title').html('Protocolos IATF - Enviar para Lixeira');
        $(".confirma_gravar").html('Enviar para Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }
    else {
        $('#modal_incluir .modal-title').html('Protocolos IATF - Remover da Lixeira');
        $(".confirma_gravar").html('Remover da Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }

    $('#modal_incluir').modal('show');
}

function troca_virgula(id){
    var valor = $(`#${id}`).val();
    valor = valor.replace(",", ".");
}

function gravar_protocolo(){
    var dados = $("#form_gravar_protocolo").serialize();

    //protocolo
    if($("#nome_protocolo").val() == '' || $("#quant_protocolo").val() == '' || $("#qtd_dias").val() == ''){
        alert("Preencha o nome, a quantidade do protocolo e os dias para diagnótico!");
    }//produto 0
    else if($("#descricao_0").val() != '' && $("#nome_prod_0").val() == ''){
        alert("Preencha o nome do produto ou observação no Dia 0!");
    }else if($("#quantidade_0").val() != '' && $("#quantidade_0").val() != '0.000'  && !$("input[name='unidade_0']").is(":checked")){
        alert("Selecione a unidade do produto no Dia 0!");
    }//produto 0_1
    else if($("#mais_med_0_O").is(":checked") && $("#nome_prod_0_1").val() == ''){
        alert("Preencha a 1° orientação adicional no Dia 0!");
    }else if($("#mais_med_0_M").is(":checked") && $("#nome_prod_0_1").val() == ''){
        alert("Preencha o nome do 1° produto adicional no Dia 0!");
    }else if($("#mais_med_0_M").is(":checked") && $("#quantidade_0_1").val() == ''){
        alert("Preencha a quantidade do 1° produto adicional no Dia 0!");
    }else if($("#mais_med_0_M").is(":checked") && !$("input[name='unidade_0_1']").is(":checked")){
        alert("Selecione a unidade do 1° produto adicional no Dia 0!");
    }//produto 0_2
    else if($("#mais_med_0_1_O").is(":checked") && $("#nome_prod_0_2").val() == ''){
        alert("Preencha a 2° orientação adicional no Dia 0!");
    }else if($("#mais_med_0_1_M").is(":checked") && $("#nome_prod_0_2").val() == ''){
        alert("Preencha o nome do 2° produto adicional no Dia 0!");
    }else if($("#mais_med_0_1_M").is(":checked") && $("#quantidade_0_2").val() == ''){
        alert("Preencha a quantidade do 2° produto adicional no Dia 0!");
    }else if($("#mais_med_0_1_M").is(":checked") && !$("input[name='unidade_0_2']").is(":checked")){
        alert("Selecione a unidade do 2° produto adicional no Dia 0!");
    }//produto 0_3
    else if($("#mais_med_0_2_O").is(":checked") && $("#nome_prod_0_3").val() == ''){
        alert("Preencha a 3° orientação adicional no Dia 0!");
    }else if($("#mais_med_0_2_M").is(":checked") && $("#nome_prod_0_3").val() == ''){
        alert("Preencha o nome do 3° produto adicional no Dia 0!");
    }else if($("#mais_med_0_2_M").is(":checked") && $("#quantidade_0_3").val() == ''){
        alert("Preencha a quantidade do 3° produto adicional no Dia 0!");
    }else if($("#mais_med_0_2_M").is(":checked") && !$("input[name='unidade_0_3']").is(":checked")){
        alert("Selecione a unidade do 3° produto adicional no Dia 0!");
    }
    
    //produto 1
    else if($("#descricao_1").val() != '' && $("#nome_prod_1").val() == ''){
        alert("Preencha o nome do produto ou observação no " + $("#descricao_1").val() + "!");
    }else if($("#quantidade_1").val() != '' && !$("input[name='unidade_1']").is(":checked")){
        alert("Selecione a unidade do produto no " + $("#descricao_1").val() + "!");
    }//produto 1_1
    else if($("#mais_med_1_O").is(":checked") && $("#nome_prod_1_1").val() == ''){
        alert("Preencha a 1° orientação adicional no " + $("#descricao_1").val() + "!");
    }else if($("#mais_med_1_M").is(":checked") && $("#nome_prod_1_1").val() == ''){
        alert("Preencha o nome do 1° produto adicional no " + $("#descricao_1").val() + "!");
    }else if($("#mais_med_1_M").is(":checked") && $("#quantidade_1_1").val() == ''){
        alert("Preencha a quantidade do 1° produto adicional no " + $("#descricao_1").val() + "!");
    }else if($("#mais_med_1_M").is(":checked") && !$("input[name='unidade_1_1']").is(":checked")){
        alert("Selecione a unidade do 1° produto adicional no " + $("#descricao_1").val() + "!");
    }//produto 1_2
    else if($("#mais_med_1_1_O").is(":checked") && $("#nome_prod_1_2").val() == ''){
        alert("Preencha a 2° orientação adicional no " + $("#descricao_1").val() + "!");
    }else if($("#mais_med_1_1_M").is(":checked") && $("#nome_prod_1_2").val() == ''){
        alert("Preencha o nome do 2° produto adicional no " + $("#descricao_1").val() + "!");
    }else if($("#mais_med_1_1_M").is(":checked") && $("#quantidade_1_2").val() == ''){
        alert("Preencha a quantidade do 2° produto adicional no " + $("#descricao_1").val() + "!");
    }else if($("#mais_med_1_1_M").is(":checked") && !$("input[name='unidade_1_2']").is(":checked")){
        alert("Selecione a unidade do 2° produto adicional no " + $("#descricao_1").val() + "!");
    }//produto 1_3
    else if($("#mais_med_1_2_O").is(":checked") && $("#nome_prod_1_3").val() == ''){
        alert("Preencha a 3° orientação adicional no " + $("#descricao_1").val() + "!");
    }else if($("#mais_med_1_2_M").is(":checked") && $("#nome_prod_1_3").val() == ''){
        alert("Preencha o nome do 3° produto adicional no " + $("#descricao_1").val() + "!");
    }else if($("#mais_med_1_2_M").is(":checked") && $("#quantidade_1_3").val() == ''){
        alert("Preencha a quantidade do 3° produto adicional no " + $("#descricao_1").val() + "!");
    }else if($("#mais_med_1_2_M").is(":checked") && !$("input[name='unidade_1_3']").is(":checked")){
        alert("Selecione a unidade do 3° produto adicional no " + $("#descricao_1").val() + "!");
    }

    //produto 2
    else if($("#descricao_2").val() != '' && $("#nome_prod_2").val() == ''){
        alert("Preencha o nome do produto ou observação no " + $("#descricao_2").val() + "!");
    }else if($("#quantidade_2").val() != '' && !$("input[name='unidade_2']").is(":checked")){
        alert("Selecione a unidade do produto no " + $("#descricao_2").val() + "!");
    }//produto 2_1
    else if($("#mais_med_2_O").is(":checked") && $("#nome_prod_2_1").val() == ''){
        alert("Preencha a 1° orientação adicional no " + $("#descricao_2").val() + "!");
    }else if($("#mais_med_2_M").is(":checked") && $("#nome_prod_2_1").val() == ''){
        alert("Preencha o nome do 1° produto adicional no " + $("#descricao_2").val() + "!");
    }else if($("#mais_med_2_M").is(":checked") && $("#quantidade_2_1").val() == ''){
        alert("Preencha a quantidade do 1° produto adicional no " + $("#descricao_2").val() + "!");
    }else if($("#mais_med_2_M").is(":checked") && !$("input[name='unidade_2_1']").is(":checked")){
        alert("Selecione a unidade do 1° produto adicional no " + $("#descricao_2").val() + "!");
    }//produto 2_2
    else if($("#mais_med_2_1_O").is(":checked") && $("#nome_prod_2_2").val() == ''){
        alert("Preencha a 2° orientação adicional no " + $("#descricao_2").val() + "!");
    }else if($("#mais_med_2_1_M").is(":checked") && $("#nome_prod_2_2").val() == ''){
        alert("Preencha o nome do 2° produto adicional no " + $("#descricao_2").val() + "!");
    }else if($("#mais_med_2_1_M").is(":checked") && $("#quantidade_2_2").val() == ''){
        alert("Preencha a quantidade do 2° produto adicional no " + $("#descricao_2").val() + "!");
    }else if($("#mais_med_2_1_M").is(":checked") && !$("input[name='unidade_2_2']").is(":checked")){
        alert("Selecione a unidade do 2° produto adicional no " + $("#descricao_2").val() + "!");
    }//produto 2_3
    else if($("#mais_med_2_2_O").is(":checked") && $("#nome_prod_2_3").val() == ''){
        alert("Preencha a 3° orientação adicional no " + $("#descricao_2").val() + "!");
    }else if($("#mais_med_2_2_M").is(":checked") && $("#nome_prod_2_3").val() == ''){
        alert("Preencha o nome do 3° produto adicional no " + $("#descricao_2").val() + "!");
    }else if($("#mais_med_2_2_M").is(":checked") && $("#quantidade_2_3").val() == ''){
        alert("Preencha a quantidade do 3° produto adicional no " + $("#descricao_2").val() + "!");
    }else if($("#mais_med_2_2_M").is(":checked") && !$("input[name='unidade_2_3']").is(":checked")){
        alert("Selecione a unidade do 3° produto adicional no " + $("#descricao_2").val() + "!");
    }
    
    //produto 3
    else if($("#descricao_3").val() != '' && $("#nome_prod_3").val() == ''){
        alert("Preencha o nome do produto ou observação no " + $("#descricao_3").val() + "!");
    }else if($("#quantidade_3").val() != '' && !$("input[name='unidade_3']").is(":checked")){
        alert("Selecione a unidade do produto no " + $("#descricao_3").val() + "!");
    }//produto 3_1
    else if($("#mais_med_3_O").is(":checked") && $("#nome_prod_3_1").val() == ''){
        alert("Preencha a 1° orientação adicional no " + $("#descricao_3").val() + "!");
    }else if($("#mais_med_3_M").is(":checked") && $("#nome_prod_3_1").val() == ''){
        alert("Preencha o nome do 1° produto adicional no " + $("#descricao_3").val() + "!");
    }else if($("#mais_med_3_M").is(":checked") && $("#quantidade_3_1").val() == ''){
        alert("Preencha a quantidade do 1° produto adicional no " + $("#descricao_3").val() + "!");
    }else if($("#mais_med_3_M").is(":checked") && !$("input[name='unidade_3_1']").is(":checked")){
        alert("Selecione a unidade do 1° produto adicional no " + $("#descricao_3").val() + "!");
    }//produto 3_2
    else if($("#mais_med_3_1_O").is(":checked") && $("#nome_prod_3_2").val() == ''){
        alert("Preencha a 2° orientação adicional no " + $("#descricao_3").val() + "!");
    }else if($("#mais_med_3_1_M").is(":checked") && $("#nome_prod_3_2").val() == ''){
        alert("Preencha o nome do 2° produto adicional no " + $("#descricao_3").val() + "!");
    }else if($("#mais_med_3_1_M").is(":checked") && $("#quantidade_3_2").val() == ''){
        alert("Preencha a quantidade do 2° produto adicional no " + $("#descricao_3").val() + "!");
    }else if($("#mais_med_3_1_M").is(":checked") && !$("input[name='unidade_3_2']").is(":checked")){
        alert("Selecione a unidade do 2° produto adicional no " + $("#descricao_3").val() + "!");
    }//produto 3_3
    else if($("#mais_med_3_2_O").is(":checked") && $("#nome_prod_3_3").val() == ''){
        alert("Preencha a 3° orientação adicional no " + $("#descricao_3").val() + "!");
    }else if($("#mais_med_3_2_M").is(":checked") && $("#nome_prod_3_3").val() == ''){
        alert("Preencha o nome do 3° produto adicional no " + $("#descricao_3").val() + "!");
    }else if($("#mais_med_3_2_M").is(":checked") && $("#quantidade_3_3").val() == ''){
        alert("Preencha a quantidade do 3° produto adicional no " + $("#descricao_3").val() + "!");
    }else if($("#mais_med_3_2_M").is(":checked") && !$("input[name='unidade_3_3']").is(":checked")){
        alert("Selecione a unidade do 3° produto adicional no " + $("#descricao_3").val() + "!");
    }
    
    //produto 4
    else if($("#descricao_4").val() != '' && $("#nome_prod_4").val() == ''){
        alert("Preencha o nome do produto ou observação no " + $("#descricao_4").val() + "!");
    }else if($("#quantidade_4").val() != '' && !$("input[name='unidade_4']").is(":checked")){
        alert("Selecione a unidade do produto no " + $("#descricao_4").val() + "!");
    }//produto 4_1
    else if($("#mais_med_4_O").is(":checked") && $("#nome_prod_4_1").val() == ''){
        alert("Preencha a 1° orientação adicional no " + $("#descricao_4").val() + "!");
    }else if($("#mais_med_4_M").is(":checked") && $("#nome_prod_4_1").val() == ''){
        alert("Preencha o nome do 1° produto adicional no " + $("#descricao_4").val() + "!");
    }else if($("#mais_med_4_M").is(":checked") && $("#quantidade_4_1").val() == ''){
        alert("Preencha a quantidade do 1° produto adicional no " + $("#descricao_4").val() + "!");
    }else if($("#mais_med_4_M").is(":checked") && !$("input[name='unidade_4_1']").is(":checked")){
        alert("Selecione a unidade do 1° produto adicional no " + $("#descricao_4").val() + "!");
    }//produto 4_2
    else if($("#mais_med_4_1_O").is(":checked") && $("#nome_prod_4_2").val() == ''){
        alert("Preencha a 2° orientação adicional no " + $("#descricao_4").val() + "!");
    }else if($("#mais_med_4_1_M").is(":checked") && $("#nome_prod_4_2").val() == ''){
        alert("Preencha o nome do 2° produto adicional no " + $("#descricao_4").val() + "!");
    }else if($("#mais_med_4_1_M").is(":checked") && $("#quantidade_4_2").val() == ''){
        alert("Preencha a quantidade do 2° produto adicional no " + $("#descricao_4").val() + "!");
    }else if($("#mais_med_4_1_M").is(":checked") && !$("input[name='unidade_4_2']").is(":checked")){
        alert("Selecione a unidade do 2° produto adicional no " + $("#descricao_4").val() + "!");
    }//produto 4_3
    else if($("#mais_med_4_2_O").is(":checked") && $("#nome_prod_4_3").val() == ''){
        alert("Preencha a 3° orientação adicional no " + $("#descricao_4").val() + "!");
    }else if($("#mais_med_4_2_M").is(":checked") && $("#nome_prod_4_3").val() == ''){
        alert("Preencha o nome do 3° produto adicional no " + $("#descricao_4").val() + "!");
    }else if($("#mais_med_4_2_M").is(":checked") && $("#quantidade_4_3").val() == ''){
        alert("Preencha a quantidade do 3° produto adicional no " + $("#descricao_4").val() + "!");
    }else if($("#mais_med_4_2_M").is(":checked") && !$("input[name='unidade_4_3']").is(":checked")){
        alert("Selecione a unidade do 3° produto adicional no " + $("#descricao_4").val() + "!");
    }
    else {
        if ($("#tipo_gravacao").val() == 0){
            $("#gravar").attr("disabled", true);

            $.ajax({
                type: "POST",
                url: "gravar_protocoloIATF.php",
                data: dados
            });

            //$("#gravar").attr("disabled", false);

            $("#mensagem_retorno").modal();
            $("#mensagem_retorno .modal-body").html("Registro incluído com sucesso!");
        }
        else if ($("#tipo_gravacao").val() == 1){
            $.ajax({
                type: "POST",
                url: "gravar_protocoloIATF.php",
                data: dados
                });
            
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html("Registro alterado com sucesso!");
        }
        else if ($("#tipo_gravacao").val() == 2){
            if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")){
                $.ajax({
                    type: "POST",
                    url: "gravar_protocoloIATF.php",
                    data: dados
                    });

                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html("Registro excluído com sucesso!");
            }
        }
    }
}

$(document).ready(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});

$(window).resize(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});

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
    else {
        return true;
    }
}      

function digita_valor(){
    $('#quantidade_0').bind('keypress',mask.money);
    $('#quantidade_0_1').bind('keypress',mask.money);
    $('#quantidade_0_2').bind('keypress',mask.money);
    $('#quantidade_0_3').bind('keypress',mask.money);
    $('#quantidade_1').bind('keypress',mask.money);
    $('#quantidade_1_1').bind('keypress',mask.money);
    $('#quantidade_1_2').bind('keypress',mask.money);
    $('#quantidade_1_3').bind('keypress',mask.money);
    $('#quantidade_2').bind('keypress',mask.money);
    $('#quantidade_2_1').bind('keypress',mask.money);
    $('#quantidade_2_2').bind('keypress',mask.money);
    $('#quantidade_2_3').bind('keypress',mask.money);
    $('#quantidade_3').bind('keypress',mask.money);
    $('#quantidade_3_1').bind('keypress',mask.money);
    $('#quantidade_3_2').bind('keypress',mask.money);
    $('#quantidade_3_3').bind('keypress',mask.money);
    $('#quantidade_4').bind('keypress',mask.money);
    $('#quantidade_4_1').bind('keypress',mask.money);
    $('#quantidade_4_2').bind('keypress',mask.money);
    $('#quantidade_4_3').bind('keypress',mask.money);
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
   valor_replace = valor_replace.replace(",",".");
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