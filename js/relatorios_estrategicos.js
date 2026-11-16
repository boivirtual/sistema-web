/**DASHBOARD*/
window.addEventListener("load", function(event) {
    var controle_estoque = $("#tipo_controle_estoque").val();
    $(".dashboard-cards").hide();
    $(".sexo1").hide();
    $("#M001").prop("checked", false);
    $("#F001").prop("checked", false);
    $(".sexo2").hide();
    $("#M002").prop("checked", false);
    $("#F002").prop("checked", false);
    $(".sexo3").hide();
    $("#M003").prop("checked", false);
    $("#F003").prop("checked", false);
    $(".sexo4").hide();
    $("#M004").prop("checked", false);
    $("#F004").prop("checked", false);
    $(".sexo5").hide();
    $("#M005").prop("checked", false);
    $("#F005").prop("checked", false);
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

function voltar_menu(){
    jQuery('#main-content').css({
        'margin-left': '180px'
    });
    jQuery('#sidebar > ul').show();
    jQuery('#sidebar').css({
        'margin-left': '0'
    });
    jQuery("#container").removeClass("sidebar-closed");
}

$(document).ready(function(){
    $("#data_final").click(function () {
        document.getElementById("data_final").style.borderColor = "";
    });

    $("#data_inicial").click(function () {
        document.getElementById("data_inicial").style.borderColor = "";
    });

    $("#codigo_conta").click(function () {
        document.getElementById("codigo_conta").style.borderColor = "";
    });

    $('#categoria').click(function(){
        $('#categoria_gmd').modal('show');
    });

    $('#c001').click(function(){
        if ($("#c001").is(":checked") == true){
            $(".sexo1").show();
        }
        else{
            $(".sexo1").hide();
            $("#M001").prop("checked", false);
            $("#F001").prop("checked", false);
        }
    });

    $('#c002').click(function(){
        if ($("#c002").is(":checked") == true){
            $(".sexo2").show();
        }
        else{
            $(".sexo2").hide();
            $("#M002").prop("checked", false);
            $("#F002").prop("checked", false);
        }
    });

    $('#c003').click(function(){
        if ($("#c003").is(":checked") == true){
            $(".sexo3").show();
        }
        else{
            $(".sexo3").hide();
            $("#M003").prop("checked", false);
            $("#F003").prop("checked", false);
        }
    });

    $('#c004').click(function(){
        if ($("#c004").is(":checked") == true){
            $(".sexo4").show();
        }
        else{
            $(".sexo4").hide();
            $("#M004").prop("checked", false);
            $("#F004").prop("checked", false);
        }
    });

    $('#c005').click(function(){
        if ($("#c005").is(":checked") == true){
            $(".sexo5").show();
        }
        else{
            $(".sexo5").hide();
            $("#M005").prop("checked", false);
            $("#F005").prop("checked", false);
        }
    });

    $("#data_final").change(function () {
        $(".dashboard-cards").hide();
        //$(".mais_filtros").hide();
        $(".menos_filtros").hide();
    });

    $("#data_inicial").change(function () {
        $(".dashboard-cards").hide();
        //$(".mais_filtros").hide();
        $(".menos_filtros").hide();
    });

    $("#codigo_local").change(function () {
        $(".dashboard-cards").hide();
        //$(".mais_filtros").hide();
        $(".menos_filtros").hide();
    });

});

function gerar_categoria() {
    $(".dashboard-cards").hide();
    $(".mais_filtros").hide();
    $(".menos_filtros").hide();

    var filtro_categoria = "";

    if ($("#c001").is(":checked") == true){
        if ($("#M001").is(":checked") == true && 
            $("#F001").is(":checked") == true) {
            filtro_categoria+= '(00 a 07 Macho/Fêmea) ';
        }
        else if ($("#M001").is(":checked") == true) {
            filtro_categoria+= '(00 a 07 Macho) ';
        }
        else if ($("#F001").is(":checked") == true) {
            filtro_categoria+= '(00 a 07 Fêmea) ';
        }
        else {
            if ($("#M001").is(":checked") == false && 
                $("#F001").is(":checked") == false) {

                alert ('Selecione o sexo para a categoria 00 a 07 meses!');
                return;
            }
        }
    }

    if ($("#c002").is(":checked") == true){
        if ($("#M002").is(":checked") == true && $("#F002").is(":checked") == true) {
            filtro_categoria+= '(08 a 12 Macho/Fêmea) ';
        }
        else if ($("#M002").is(":checked") == true) {
            filtro_categoria+= '(08 a 12 Macho) ';
        }
        else if ($("#F002").is(":checked") == true) {
            filtro_categoria+= '(08 a 12 Fêmea) ';
        }
        else {
            if ($("#M002").is(":checked") == false && 
                $("#F002").is(":checked") == false) {

                alert ('Selecione o sexo para a categoria 08 a 12 meses!');
                return;
            }
        }
    }

    if ($("#c003").is(":checked") == true){
        if ($("#M003").is(":checked") == true && $("#F003").is(":checked") == true) {
            filtro_categoria+= '(13 a 24 Macho/Fêmea) ';
        }
        else if ($("#M003").is(":checked") == true) {
            filtro_categoria+= '(13 a 24 Macho) ';
        }
        else if ($("#F003").is(":checked") == true) {
            filtro_categoria+= '(13 a 24 Fêmea) ';
        }
        else {
            if ($("#M003").is(":checked") == false && 
                $("#F003").is(":checked") == false) {

                alert ('Selecione o sexo para a categoria 13 a 24 meses!');
                return;
            }
        }
    }

    if ($("#c004").is(":checked") == true){
        if ($("#M004").is(":checked") == true && $("#F004").is(":checked") == true) {
            filtro_categoria+= '(25 a 36 Macho/Fêmea) ';
        }
        else if ($("#M004").is(":checked") == true) {
            filtro_categoria+= '(25 a 36 Macho) ';
        }
        else if ($("#F004").is(":checked") == true) {
            filtro_categoria+= '(25 a 36 Fêmea) ';
        }
        else {
            if ($("#M004").is(":checked") == false && 
                $("#F004").is(":checked") == false) {

                alert ('Selecione o sexo para a categoria 25 a 36 meses!');
                return;
            }
        }
    }

    if ($("#c005").is(":checked") == true){
        if ($("#M005").is(":checked") == true && $("#F005").is(":checked") == true) {
            filtro_categoria+= '(> 36 Macho/Fêmea) ';
        }
        else if ($("#M005").is(":checked") == true) {
            filtro_categoria+= '(> 36 Macho) ';
        }
        else if ($("#F005").is(":checked") == true) {
            filtro_categoria+= '(> 36 Fêmea) ';
        }
        else {
            if ($("#M005").is(":checked") == false && 
                $("#F005").is(":checked") == false) {
                alert ('Selecione o sexo para a categoria > 36 meses!');
                return;
            }
        }
    }

    if (filtro_categoria=='') {
        filtro_categoria='Todas';
    }

    var options = $('#categoria option:selected');
    $(options).each(function(){
        $(this).bind('#categoria').text(filtro_categoria);
    });

    $('#categoria_gmd').modal('hide');
}

function categorias_selecionadas() {
    var c001 = '';
    var m001 = '';
    var f001 = '';
    var c002 = '';
    var m002 = '';
    var f002 = '';
    var c003 = '';
    var m003 = '';
    var f003 = '';
    var c004 = '';
    var m004 = '';
    var f004 = '';
    var c005 = '';
    var m005 = '';
    var f005 = '';

    if ($("#c001").is(":checked") == true){
        c001 = '001';
        if ($("#M001").is(":checked") == true) {
            m001 = 'M';
        }
        if ($("#F001").is(":checked") == true) {
            f001 = 'F';
        }
    }

    if ($("#c002").is(":checked") == true){
        c002 = '002';
        if ($("#M002").is(":checked") == true) {
            m002 = 'M';
        }
        if ($("#F002").is(":checked") == true) {
            f002 = 'F';
        }
    }

    if ($("#c003").is(":checked") == true){
        c003 = '003';
        if ($("#M003").is(":checked") == true) {
            m003 = 'M';
        }
        if ($("#F003").is(":checked") == true) {
            f003 = 'F';
        }
    }

    if ($("#c004").is(":checked") == true){
        c004 = '004';
        if ($("#M004").is(":checked") == true) {
            m004 = 'M';
        }
        if ($("#F004").is(":checked") == true) {
            f004 = 'F';
        }
    }

    if ($("#c005").is(":checked") == true){
        c005 = '005';
        if ($("#M005").is(":checked") == true) {
            m005 = 'M';
        }
        if ($("#F005").is(":checked") == true) {
            f005 = 'F';
        }
    }

    alert (c001+m001+f001 + ' ' +
           c002+m002+f002 + ' ' +
           c003+m003+f003 + ' ' +
           c004+m004+f004 + ' ' +
           c005+m005+f005 + ' ');
}

function exibe_mais_filtros() {
    $(".exibe_mais_filtros").show();
    $(".mais_filtros").hide();
    $(".menos_filtros").show();
}

function exibe_menos_filtros() {
    $(".exibe_mais_filtros").hide();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
}

function limpar_tela(){
    $('#aguardar').modal('hide');
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
        $("div.categorias div.card").addClass("table-responsive");
    }else{
        $("div.categorias div.card").removeClass("table-responsive");
    }
});

$(window).resize(function(){
    if(window.innerWidth <= 420){
        $("div.categorias div.card").addClass("table-responsive");
    }else{
        $("div.categorias div.card").removeClass("table-responsive");
    }
});

$(document).ready(function(){
    if(window.innerWidth <= 320){
        $("div.animais div.card").addClass("table-responsive");
    }else{
        $("div.animais div.card").removeClass("table-responsive");
    }
});

$(window).resize(function(){
    if(window.innerWidth <= 320){
        $("div.animais div.card").addClass("table-responsive");
    }else{
        $("div.animais div.card").removeClass("table-responsive");
    }
});
