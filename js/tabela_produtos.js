/**TABELA DE ANIMAIS*/
window.addEventListener("load", function(event) {
    consultar();
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
    $('#tabela_produtos').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
          "sSearch": "Busca:",
          "zeroRecords": "Nada encontrado",
          "info": "Registros encontrados: _END_ ",
          "infoEmpty": "Nenhum registro disponível",
          "infoFiltered": "(filtrado de _MAX_ registros no total)",
        },

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
        
    });

    $('#grupo').change(function(){
        var codigo_modalidade = $("#grupo").val();

        $.post("lista_descricao_padrao.php", {modalidade:codigo_modalidade}, function(valor){
            $("select[name=descricao_padrao]").html(valor);
        });
    });

    $('#apresentacao').change(function(){
        var descricao_apresentacao = $('#apresentacao').find(":selected").text();
        $('.apresentacao_estoque').html(descricao_apresentacao);
    });

    $('#unidade').change(function(){
        var descricao_unidade = $('#unidade').find(":selected").text();
        $('.apresentacao_estoque_atual').html(descricao_unidade);
    });

});

function sair_inclusao() {
    location.href='form_cadastro_produtos.php';
}

function consultar() {
	$.post("form_lista_produtos.php", {}, function(valor){
        $("div[id=lista_produtos]").html(valor); 
    });
}

function incluir_novo() {
    $("#codigo_produto").val(0);
    $("#descricao_complementar").val('');
    $("#apresentacao").val('001');
    $('#unidade').val('001');
    $("#qtd_uni").val('');
    $("#observacao").val('');

    $("#array_codigo_fazenda").val('');
    $("#array_estoque_atual").val('');

    var codigo_fazenda = document.getElementsByName("codigo_fazenda");
    var qtd_entrada_digitado = document.getElementsByName("qtd_entrada");
    var estoque_atual_digitado = document.getElementsByName("qtd_estoque_atual");
    var estoque_anterior_digitado = document.getElementsByName("qtd_estoque_anterior");
    var estoque_atual_apr_digitado = document.getElementsByName("qtd_estoque_atual_apr");

    for (var i = 0; i < codigo_fazenda.length; i++) {
        qtd_entrada_digitado[i].value = '';
        estoque_atual_digitado[i].value = '';
        estoque_anterior_digitado[i].value = 0;
        estoque_atual_apr_digitado[i].value = '';
    }

    $("#tipo_gravacao").val(0);

    var und_entrada_estoque = $('#apresentacao').find(":selected").text();
    $('.apresentacao_estoque').html(und_entrada_estoque);

    var und_estoque_atual = $('#unidade').find(":selected").text();
    $('.apresentacao_estoque_atual').html(und_estoque_atual);

    $('#modal_incluir .modal-title').html('Produto - Incluir');
    $('.confirma_gravar').html('Confirmar Inclusão').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    $('#modal_incluir').modal('show');

    var codigo_modalidade = $("#grupo").val();

    $.post("lista_descricao_padrao.php", {modalidade:codigo_modalidade}, function(valor){
        $("select[name=descricao_padrao]").html(valor);
    });
}

function editar_animal(array_animal) {
    array_produtos = array_animal.split('|');

    var array_fazenda = array_produtos[10].split('!');
    var qtd_fazendas = array_fazenda.length;

    if (qtd_fazendas==1) {
        $('.exibe_totais').hide();
    }   
    else {
        $('.exibe_totais').show();
    } 

    var array_estoque_atual = array_produtos[11].split('!');

    var total_estoque_fazendas = 0;
    var total_estoque_fazendas_apr = 0;

    if (array_estoque_atual!='') {
        var codigo_fazenda = document.getElementsByName("codigo_fazenda");
        var estoque_atual_digitado = document.getElementsByName("qtd_estoque_atual");
        var estoque_anterior_digitado = document.getElementsByName("qtd_estoque_anterior");
        var estoque_atual_apr_digitado = document.getElementsByName("qtd_estoque_atual_apr");
        var qtd_entrada_digitado = document.getElementsByName("qtd_entrada");

        for (var i = 0; i < qtd_fazendas; i++) {
            if (array_fazenda[i] == codigo_fazenda[i].value) {
                estoque_atual_apresentacao = array_estoque_atual[i] / array_produtos[4];

                estoque_atual_digitado[i].value = formatMoney(array_estoque_atual[i]);
                estoque_anterior_digitado[i].value = array_estoque_atual[i];
                qtd_entrada_digitado[i].value = '';
                estoque_atual_apr_digitado[i].value = formatMoney(estoque_atual_apresentacao);

                total_estoque_fazendas+=parseFloat(array_estoque_atual[i]);
                total_estoque_fazendas_apr=total_estoque_fazendas / parseFloat(array_produtos[4]);
            }
        }
    }
    else {
        var codigo_fazenda = document.getElementsByName("codigo_fazenda");
        var qtd_entrada_digitado = document.getElementsByName("qtd_entrada");
        var estoque_atual_digitado = document.getElementsByName("qtd_estoque_atual");
        var estoque_anterior_digitado = document.getElementsByName("qtd_estoque_anterior");
        var estoque_atual_apr_digitado = document.getElementsByName("qtd_estoque_atual_apr");

        for (var i = 0; i < codigo_fazenda.length; i++) {
            qtd_entrada_digitado[i].value = '';
            estoque_atual_digitado[i].value = '';
            estoque_anterior_digitado[i].value = 0;
            estoque_atual_apr_digitado[i].value = '';
        }
    }

    $("#total_estoque").val(formatMoney(total_estoque_fazendas));
    $("#total_estoque_apr").val(formatMoney(total_estoque_fazendas_apr));

    $("#codigo_produto").val(array_produtos[0]);
    $("#grupo").val(array_produtos[1]);
    $("#apresentacao").val(array_produtos[3]);
    $("#qtd_uni").val(formatMoney(array_produtos[4]));
    $('#unidade').val(array_produtos[5]);
    $("#observacao").val(array_produtos[6]);
    $("#descricao_complementar").val(array_produtos[8]);
    $("#descricao_anterior").val(array_produtos[2]);
    $("#apresentacao_anterior").val(array_produtos[3]);
    $("#qtd_anterior").val(array_produtos[4]);
    $('#unidade_anterior').val(array_produtos[5]);

    var und_entrada_estoque = $('#apresentacao').find(":selected").text();
    $('.apresentacao_estoque').html(und_entrada_estoque);

    var und_estoque_atual = $('#unidade').find(":selected").text();
    $('.apresentacao_estoque_atual').html(und_estoque_atual);

    $("#tipo_gravacao").val(1);

    $('#modal_incluir .modal-title').html('Produto - Editar');
    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');

    $('#modal_incluir').modal('show');

    var codigo_modalidade = $("#grupo").val();

    $.post("lista_descricao_padrao.php", {modalidade:codigo_modalidade}, function(valor){
        $("select[name=descricao_padrao]").html(valor);

        $("#descricao_padrao").val(array_produtos[9]);
    });
}

function enviar_lixeira(array_animal, opcao) {
    array_produtos = array_animal.split('|');

    var array_fazenda = array_produtos[10].split('!');
    var qtd_fazendas = array_fazenda.length;

    if (qtd_fazendas==1) {
        $('.exibe_totais').hide();
    }   
    else {
        $('.exibe_totais').show();
    } 

    var array_estoque_atual = array_produtos[11].split('!');

    var total_estoque_fazendas = 0;
    var total_estoque_fazendas_apr = 0;

    if (array_estoque_atual!='') {
        var codigo_fazenda = document.getElementsByName("codigo_fazenda");
        var estoque_atual_digitado = document.getElementsByName("qtd_estoque_atual");
        var estoque_anterior_digitado = document.getElementsByName("qtd_estoque_anterior");
        var estoque_atual_apr_digitado = document.getElementsByName("qtd_estoque_atual_apr");
        var qtd_entrada_digitado = document.getElementsByName("qtd_entrada");

        for (var i = 0; i < qtd_fazendas; i++) {
            if (array_fazenda[i] == codigo_fazenda[i].value) {
                estoque_atual_apresentacao = array_estoque_atual[i] / array_produtos[4];

                estoque_atual_digitado[i].value = formatMoney(array_estoque_atual[i]);
                estoque_anterior_digitado[i].value = array_estoque_atual[i];
                qtd_entrada_digitado[i].value = '';
                estoque_atual_apr_digitado[i].value = formatMoney(estoque_atual_apresentacao);

                total_estoque_fazendas+=parseFloat(array_estoque_atual[i]);
                total_estoque_fazendas_apr=total_estoque_fazendas / parseFloat(array_produtos[4]);
            }
        }
    }
    else {
        var codigo_fazenda = document.getElementsByName("codigo_fazenda");
        var qtd_entrada_digitado = document.getElementsByName("qtd_entrada");
        var estoque_atual_digitado = document.getElementsByName("qtd_estoque_atual");
        var estoque_anterior_digitado = document.getElementsByName("qtd_estoque_anterior");
        var estoque_atual_apr_digitado = document.getElementsByName("qtd_estoque_atual_apr");

        for (var i = 0; i < codigo_fazenda.length; i++) {
            qtd_entrada_digitado[i].value = '';
            estoque_atual_digitado[i].value = '';
            estoque_anterior_digitado[i].value = 0;
            estoque_atual_apr_digitado[i].value = '';
        }
    }

    $("#total_estoque").val(formatMoney(total_estoque_fazendas));
    $("#total_estoque_apr").val(formatMoney(total_estoque_fazendas_apr));

    $("#codigo_produto").val(array_produtos[0]);
    $("#grupo").val(array_produtos[1]);
    $("#descricao_complementar").val(array_produtos[8]);
    $("#apresentacao").val(array_produtos[3]);
    $("#qtd_uni").val(formatMoney(array_produtos[4]));
    $('#unidade').val(array_produtos[5]);
    $("#observacao").val(array_produtos[6]);

    var und_entrada_estoque = $('#apresentacao').find(":selected").text();
    $('.apresentacao_estoque').html(und_entrada_estoque);

    var und_estoque_atual = $('#unidade').find(":selected").text();
    $('.apresentacao_estoque_atual').html(und_estoque_atual);

    $("#tipo_gravacao").val(opcao);

    if (opcao==2) {
        $('#modal_incluir .modal-title').html('Produto - Enviar para Lixeira');
        $(".confirma_gravar").html('Enviar para Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }
    else {
        $('#modal_incluir .modal-title').html('Produto - Remover da Lixeira');
        $(".confirma_gravar").html('Remover da Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }

    $('.confirma_gravar').show();
    $('#modal_incluir').modal('show');

    var codigo_modalidade = $("#grupo").val();

    $.post("lista_descricao_padrao.php", {modalidade:codigo_modalidade}, function(valor){
        $("select[name=descricao_padrao]").html(valor);

        $("#descricao_padrao").val(array_produtos[9]);
    });

}

function gravar_produtos() {
    var fazenda = new Array();
    var array_fazenda = "";
    var codigo_fazenda = document.getElementsByName("codigo_fazenda");

    var estoque_atual = new Array();
    var array_estoque_atual = "";
    var qtd_estoque_atual = document.getElementsByName("qtd_estoque_atual");

    for (var i = 0; i < codigo_fazenda.length; i++) {
        cod_fazenda = codigo_fazenda[i].value;
        fazenda.push(cod_fazenda);
        array_fazenda = fazenda.join("!");

        qtd_estoque_atual_dig = qtd_estoque_atual[i].value;
        estoque_atual.push(qtd_estoque_atual_dig);
        array_estoque_atual = estoque_atual.join("!");
    }

    $("#array_codigo_fazenda").val(array_fazenda);
    $("#array_estoque_atual").val(array_estoque_atual);

    var tipo_gravacao = $("#tipo_gravacao").val();

    if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_produto').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_produtos.php',
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
            var dados = $('#form_gravar_produto').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_produtos.php',
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
        var dados = $('#form_gravar_produto').serialize();

        $(".confirma_gravar").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: 'gravar_produtos.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    $(".confirma_gravar").attr("disabled", false);
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    if (tipo_gravacao==1) {
                        $(".confirma_gravar").attr("disabled", false);
                        $("#mensagem_retorno_edicao").modal();
                        $("#mensagem_retorno_edicao .modal-body").html(data.message);
                    }
                    else {
                        $(".confirma_gravar").attr("disabled", false);
                        $("#mensagem_retorno").modal();
                        $("#mensagem_retorno .modal-body").html(data.message);
                    }
                }
            }
        });
    }
}

function digita_valor(){
    var codigo_fazenda = document.getElementsByName("codigo_fazenda");
    var qtd_entrada = document.getElementsByName("qtd_entrada");

   // for (var i = 0; i < codigo_fazenda.length; i++) {
   //     qtd_entrada[i].bind('keypress',mask.money);
  //  }

    $('#qtd_uni').bind('keypress',mask.money);
    $('.qtd_entrada').bind('keypress',mask.money);
}

function exibe_qtd_uni(){
    var qtd_uni = $("#qtd_uni").val();
    if (verifica_virgula(qtd_uni)==',') {
        qtd_uni = replace_valor(qtd_uni);
    }

    $("#qtd_uni").val(formatMoney(qtd_uni));
}

function exibe_qtd_entrada(){
    var codigo_fazenda = document.getElementsByName("codigo_fazenda");
    var qtd_entrada_digitado = document.getElementsByName("qtd_entrada");
    var estoque_atual_digitado = document.getElementsByName("qtd_estoque_atual");
    var estoque_anterior_digitado = document.getElementsByName("qtd_estoque_anterior");
    var estoque_atual_apr_digitado = document.getElementsByName("qtd_estoque_atual_apr");

    var qtd_apresentacao = $("#qtd_uni").val();
    if (verifica_virgula(qtd_apresentacao)==',') {
        qtd_apresentacao = replace_valor(qtd_apresentacao);
    }

    total_estoque_fazendas=0;
    total_estoque_fazendas_apr=0;

    for (var i = 0; i < codigo_fazenda.length; i++) {
        var qtd_entrada = qtd_entrada_digitado[i].value;
        var estoque_atual = estoque_anterior_digitado[i].value;

        if (verifica_virgula(qtd_entrada)==',') {
            qtd_entrada = replace_valor(qtd_entrada);
        }

        if (verifica_virgula(estoque_atual)==',') {
            estoque_atual = replace_valor(estoque_atual);
        }

        if (qtd_entrada!='') {
            qtd_entrada = parseFloat(qtd_entrada);
            qtd_apresentacao = parseFloat(qtd_apresentacao);
            estoque_atual = parseFloat(estoque_atual);

            estoque_atual_unidade = (qtd_entrada * qtd_apresentacao);
            estoque_atual_unidade+=estoque_atual;
            estoque_atual_apresentacao = estoque_atual_unidade / qtd_apresentacao;

            total_estoque_fazendas+=estoque_atual_unidade;
            total_estoque_fazendas_apr+=estoque_atual_apresentacao;

            qtd_entrada = formatMoney(qtd_entrada);
            qtd_entrada_digitado[i].value = qtd_entrada;

            estoque_atual_unidade = formatMoney(estoque_atual_unidade);
            estoque_atual_digitado[i].value = estoque_atual_unidade;

            estoque_atual_apresentacao = formatMoney(estoque_atual_apresentacao);
            estoque_atual_apr_digitado[i].value = estoque_atual_apresentacao;
        }
        else {
            qtd_apresentacao = parseFloat(qtd_apresentacao);
            estoque_atual = parseFloat(estoque_atual);

            total_estoque_fazendas+=estoque_atual;
            total_estoque_fazendas_apr = total_estoque_fazendas / qtd_apresentacao;
        }

        $("#total_estoque").val(formatMoney(total_estoque_fazendas));
        $("#total_estoque_apr").val(formatMoney(total_estoque_fazendas_apr));
    }
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

function mostrar_reproducao(){
    $(".reprod").show();
}

function esconder_reproducao(){
    $(".reprod").hide();
}

/*    var array_tabela_itens = new Array();
    var valor = new Array();
    var grupo_itens = "";

    var tipo_rel = $("input[name='tipo_rel']:checked").val();

    if (tipo_rel=='C') {
        alert ('entrei lista completa');
    }
    else {
        alert ('entrei lista resumida');
    }

    animais_listados=$('#tabela_lista_animais thead td').eq(0).text();
    animais_listatos_peso=$('#tabela_lista_animais thead td').eq(1).text();
    peso_medio_nasc=$('#tabela_lista_animais thead td').eq(3).text();
    peso_medio_desmama=$('#tabela_lista_animais thead td').eq(4).text();
    peso_medio_total=$('#tabela_lista_animais thead td').eq(5).text();
    peso_total=$('#tabela_lista_animais thead td').eq(6).text();

    if (verifica_virgula(animais_listados)==',') {
        animais_listados = replace_valor(animais_listados);
    }

    if (verifica_virgula(animais_listatos_peso)==',') {
        animais_listatos_peso = replace_valor(animais_listatos_peso);
    }

    if (verifica_virgula(peso_medio_nasc)==',') {
        peso_medio_nasc = replace_valor(peso_medio_nasc);
    }

    if (verifica_virgula(peso_medio_desmama)==',') {
        peso_medio_desmama = replace_valor(peso_medio_desmama);
    }

    if (verifica_virgula(peso_medio_total)==',') {
        peso_medio_total = replace_valor(peso_medio_total);
    }

    if (verifica_virgula(peso_total)==',') {
        peso_total = replace_valor(peso_total);
    }

    $('#tabela_lista_animais tbody tr').each(function(){
        for (i = 0; i <= 12; i++) {
            valor[i]=0;
        }

        var codigo = $(this).find('.codigo').html();  
        var local = $(this).find('.local').html(); 
        var sexo = $(this).find('.sexo').html();
        var data_nasc = $(this).find('.data_nasc').html();
        var raca = $(this).find('.raca').html();
        var pelagem = $(this).find('.pelagem').html();
        var mae = $(this).find('.mae').html();
        var pai = $(this).find('.pai').html();
        var peso_nasc = $(this).find('.peso_nasc').html();
        var peso_desmama = $(this).find('.peso_desmama').html();
        var peso_ult = $(this).find('.peso_ult').html();
        var data_ult = $(this).find('.data_ult').html();

        if (verifica_virgula(peso_nasc)==',') {
            peso_nasc = replace_valor(peso_nasc);
        }

        if (verifica_virgula(peso_desmama)==',') {
            peso_desmama = replace_valor(peso_desmama);
        }

        if (verifica_virgula(peso_ult)==',') {
            peso_ult = replace_valor(peso_ult);
        }

        if (codigo!=undefined && codigo!=''){
            valor[0]=codigo;
            valor[1]=local;
            valor[2]=sexo;
            valor[3]=data_nasc;
            valor[4]=raca;
            valor[5]=pelagem;
            valor[6]=mae;
            valor[7]=pai;
            valor[8]=peso_nasc;
            valor[9]=peso_desmama;
            valor[10]=peso_ult;
            valor[11]=data_ult;

            var tabela_itens=valor.join("|");
            array_tabela_itens.push(tabela_itens);
            grupo_itens=array_tabela_itens.join("<|>");
        }
   });
*/

