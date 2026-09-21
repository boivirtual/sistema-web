/**TABELA DE COMPRA/VENDA*/
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

    // Exibe filtros quando faz reload
    var filtro_local_origem = $("#exibe_local_origem").val();
 
    if (filtro_local_origem!='' && filtro_local_origem!=null) {
        var filtro_local_origem = filtro_local_origem.split(',');

        $.each(filtro_local_origem, function(idx, val) {
            $('#codigo_local_origem option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_local_origem').selectpicker('refresh');

        /*$.each(filtro_local_origem, function(idx, val) {
            $('#codigo_local option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_local').selectpicker('refresh');*/
    }

    var filtro_local_destino = $("#exibe_local_destino").val();
 
    if (filtro_local_destino!='' && filtro_local_destino!=null) {
        var filtro_local_destino = filtro_local_destino.split(',');

        $.each(filtro_local_destino, function(idx, val) {
            $('#codigo_local_destino option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_local_destino').selectpicker('refresh');
    }

    var filtro_compra_venda = $("#exibe_compra_venda").val();

    if (filtro_compra_venda!='' && filtro_compra_venda!=null) {
        var filtro_compra_venda = filtro_compra_venda.split(',');

        $.each(filtro_compra_venda, function(idx, val) {
            $('#tipo_movimentacao option[value=' + val + ']').attr('selected', true);
        });

        $('#tipo_movimentacao').selectpicker('refresh');
    }

    var lista_reg_automatico = $("#lista_reg_automatico").val();

    if (lista_reg_automatico=="S") {
        consultar();
    }
    // Fim exibe filtros

    //  alimenta campo local origem na Venda
    $.post("lista_local.php", {tipo:0}, function(valor){
        $("select[name=local]").html(valor);
    });

    //  alimenta campo local_destino na compra
    $.post("lista_local.php", {tipo:0}, function(valor){
        $("select[name=local_destino]").html(valor);
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

function consultar() {
    var local_origem = $("#codigo_local_origem").val();
    var local_destino = $("#codigo_local_destino").val();
    var tipo = $("#tipo_movimentacao").val();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();

    if (data_inicial > data_final) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Data Inicial e Final corretamente!');
        return;
    }

    if (local_origem==null) {
        var array_local_origem= new Array();
    }
    else {
        var array_local_origem = new Array();
        var valor = new Array();

        for (i = 0; i <= local_origem.length; i++) {
            valor[i]=local_origem[i];
        }

        var array_local_origem=valor.join(",");
    }

    if (local_destino==null) {
        var array_local_destino= new Array();
    }
    else {
        var array_local_destino = new Array();
        var valor = new Array();

        for (i = 0; i <= local_destino.length; i++) {
            valor[i]=local_destino[i];
        }

        var array_local_destino=valor.join(",");
    }

    if (tipo==null) {
        var array_tipo= new Array();
    }
    else {
        var array_tipo = new Array();
        var valor = new Array();

        for (i = 0; i <= tipo.length; i++) {
            valor[i]=tipo[i];
        }

        var array_tipo=valor.join(",");
    }

    var options = $("#codigo_local_origem option:selected");
    var local_origem_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_local_origem").text();
        local_origem_filtro.push(desc.trim());
    });

    if (local_origem_filtro != "") {
        local_origem_filtro = "Local Origem: " + local_origem_filtro + "->";
    } else {
        local_origem_filtro = "Local Origem: Todos->";
    }

    var options = $("#codigo_local_destino option:selected");
    var local_destino_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_local_destino").text();
        local_destino_filtro.push(desc.trim());
    });

    if (local_destino_filtro != "") {
        local_destino_filtro = "Local Destino: " + local_destino_filtro + "->";
    } else {
        local_destino_filtro = "Local Destino: Todos->";
    }

    var options = $("#tipo_movimentacao option:selected");
    var compra_venda_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#tipo_movimentacao").text();
        compra_venda_filtro.push(desc.trim());
    });

    if (compra_venda_filtro != "") {
        compra_venda_filtro = "Compra/Venda: " + compra_venda_filtro + "->";
    } else {
        compra_venda_filtro = "Compra/Venda: Todos->";
    }

    var data_ini = data_inicial.split("-");
    var dia_ini = data_ini[2];
    var mes_ini = data_ini[1];
    var ano_ini = data_ini[0];

    var data_fim = data_final.split("-");
    var dia_fim = data_fim[2];
    var mes_fim = data_fim[1];
    var ano_fim = data_fim[0];
    periodo =
        "Período: de " +
        dia_ini +
        "/" +
        mes_ini +
        "/" +
        ano_ini +
        " ate " +
        dia_fim +
        "/" +
        mes_fim +
        "/" +
        ano_fim;

    var descricao_filtro =
        local_origem_filtro +
        local_destino_filtro +
        compra_venda_filtro +
        periodo;

    $(".digitar_filtros").hide();
    $(".filtros").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".voltar").show();
    $(".descricao_filtro").html(descricao_filtro);

    $('#lista_movimentacoes').load('form_lista_compra_venda.php?data_inicial=' + data_inicial +
     '&data_final=' + data_final + 
     '&tipo=' + array_tipo + 
     '&local_origem=' + array_local_origem  + 
     '&local_destino=' + array_local_destino);

    /*$.post("form_lista_compra_venda.php", 
        {local_origem:local_origem, 
         local_destino:local_destino, 
         tipo:tipo, 
         data_inicial:data_inicial, 
         data_final:data_final },
        function(valor){ 
        $("div[id=lista_movimentacoes]").html(valor);
    });

    return;*/
}

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

function exibe_mais_filtros() {
    $(".digitar_filtros").show();
    $(".mais_filtros").hide();
    $(".menos_filtros").show();
    $(".consultar").hide();
    $(".voltar").hide();
    $(".lista_contas").hide();
}

function exibe_menos_filtros() {
    $(".digitar_filtros").hide();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".voltar").show();
    $(".lista_contas").show();
}

function mais_relatorios() {
    location.href= 'form_rel_compra_venda.php?tipo=2';
}

$(document).ready(function() {
    
    // =================================================================================
    // 1. CONFIGURAÇÃO GLOBAL (Define o texto de "Limpar Seleção" para todos)
    //    Isso elimina a necessidade do atributo data-deselect-all-text no HTML.
    // =================================================================================
    $.fn.selectpicker.defaults = {
        deselectAllText: 'Limpar Seleção',
        actionsBox: true, // Garante que a caixa de ações esteja habilitada
        // Não vamos definir selectAllText aqui para garantir que o elemento
        // seja criado e possamos removê-lo de forma explícita na etapa 3.
    };
    
    // =================================================================================
    // 2. LÓGICA DE VISIBILIDADE E REMOÇÃO (Aplicada a cada selectpicker)
    // =================================================================================
    $('.selectpicker').each(function() {
        
        const $selectElement = $(this);
        
        // Inicializa o selectpicker (usa as defaults acima)
        $selectElement.selectpicker();
        
        // Localiza a barra de ações gerada pelo plugin (onde está o botão Limpar Seleção)
        const $dropdownMenu = $selectElement.closest('.bootstrap-select').find('.dropdown-menu');
        const $actionsBox = $dropdownMenu.find('.bs-actionsbox');

        // Se a barra de ações não for encontrada, paramos para este elemento
        if (!$actionsBox.length) {
            return; 
        }

        // NOVO: Aplica o alinhamento à direita para a barra de ações
        $actionsBox.css('text-align', 'right');
        
        // -----------------------------------------------------------
        // 3. REMOÇÃO FORÇADA do "Selecionar Todos"
        // Esta linha é mantida, pois garante a remoção do botão indesejado,
        // limpando o espaço visual.
        // -----------------------------------------------------------
        $actionsBox.find('.bs-select-all').remove();
        
        // -----------------------------------------------------------
        // 4. FUNÇÃO DE VISIBILIDADE (Mostrar/Ocultar o container da barra de ações)
        // -----------------------------------------------------------
        function atualizarVisibilidadeActionsBox() {
            // Pega o array de valores selecionados
            const selecoes = $selectElement.val(); 
            
            // Se há seleções, mostra a barra de ações; caso contrário, esconde.
            if (selecoes && selecoes.length > 0) {
                $actionsBox.show(); 
            } else {
                $actionsBox.hide(); 
            }
        }
        
        // 5. Executa na inicialização (Estado inicial do formulário)
        atualizarVisibilidadeActionsBox();

        // 6. Monitore o evento de mudança
        $selectElement.on('changed.bs.select', atualizarVisibilidadeActionsBox);
    });
});

$(document).ready(function(){
    $('#tabela_compra_venda').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
          "sSearch": "Buscar na lista:",
          "zeroRecords": "Nada encontrado",
          "info": "Registros encontrados: _END_ ",
          "infoEmpty": "Nenhum registro disponível",
          "infoFiltered": "(filtrado de _MAX_ registros no total)",
        "decimal": ",",
        "thousands": ".",
        },

       "aoColumns": [
            { "sType": "date-br" },
            null,
            null,
            null,
            null,
            null
        ],

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
        
    });

    // Acende o botão consultar se houver alteracao nos filtros de Contas a Pagar
    $('#data_inicial').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#data_final').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#codigo_local_origem').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#codigo_local_destino').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#tipo_movimentacao').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });
    // Fim acendo botão 
});

function informacoes_uso() {
    $("#ajuda").modal();
}

function finalizar_sair(){
    location.href= "form_compra_venda_animais.php";
}

function voltar_relatorios() {
    var tipo_relatorio = $("#tipo_relatorio").val();

    if (tipo_relatorio==1) {
        location.href='form_relatorios_financeiros.php';
    }
    else {
        location.href='form_compra_venda_animais.php';
    }
}

function voltar_compa_venda(){
    var origem_relatorio = $("#origem_relatorio").val();

    location.href='form_rel_compra_venda.php?tipo='+origem_relatorio;
}

// VENDAS
$(document).ready(function(){
    $(".aba_dados").click(function(){
        $('a[href="#dados"]').tab('show');
    });

    $(".aba_totais").click(function(){
        var tem_valor = 'S';
        var tipo_compra = $("input[name='tipo_compra']:checked").val();
        var tipo_venda = $("input[name='tipo_venda']:checked").val();

        switch (tipo_compra) {
            case 'V':
                $('.tabela_itens_vivo tbody tr').each(function(){
                    var vlr_unit = $(this).find('.valor_unit_vivo').html();

                    if (vlr_unit==''){
                        tem_valor="N";
                    }
                });
            break;

            case 'C':
                $('.tabela_itens_cabeca tbody tr').each(function(){
                    var vlr_unit = $(this).find('.valor_unit_cabeca').html();

                    if (vlr_unit==''){
                        tem_valor="N";
                    }

                });
            break;
        }

        switch (tipo_venda) {
            case 'V':
                $('.tabela_itens_vivo tbody tr').each(function(){
                    var vlr_unit = $(this).find('.valor_unit_vivo').html();

                    if (vlr_unit==''){
                        tem_valor="N";
                    }
                });
            break;
            case 'M':
                $('.tabela_itens_morto tbody tr').each(function(){
                    var vlr_unit = $(this).find('.valor_unit_morto').html();

                    if (vlr_unit==''){
                        tem_valor="N";
                    }
                });
            break;
            case 'C':
                $('.tabela_itens_cabeca tbody tr').each(function(){
                    var vlr_unit = $(this).find('.valor_unit_cabeca').html();

                    if (vlr_unit==''){
                        tem_valor="N";
                    }
                });
            break;
        }

        if (tem_valor=='N'){
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Existe item sem o valor unitário informado. Verificar os itens na aba Dados.');
            $('a[href="#dados"]').tab('show');
            return;
        }
        else {
            $('a[href="#totais"]').tab('show');
        }
    });

    $(".aba_transporte").click(function(){
        $('a[href="#transporte"]').tab('show');
    });

    $(".exibe_tela_dados").click(function(){
        var tem_itens = $("#tem_itens").val();

        if (tem_itens=="S") {
            if (window.confirm("Existe item digitado! Deseja realmente sair sem salvar?")) {     
                location.href= "form_venda_animais_incluir.php";
            }
        }
        else {
            location.href= "form_venda_animais_incluir.php";
        }
    });

    $('.tipo_venda').click(function(event) {
        var local_origem = $("#local").val();
        var local_destino = $("#codigo_cliente").val();

        if (local_origem==0 || local_destino==0){
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe o Local e o Comprador');
            $(".tipo_venda").prop("checked", false)
            return;
        }

        var data = $("#data_venda").val();

        dia  = data.substring(8, 10);
        mes  = data.substring(5, 7);
        ano  = data.substring(0, 4);
        $(".data_venda").text('Data: ' + dia+"/"+mes+"/"+ano);

        var itemSelecionado = $("#local option:selected");
        var desc_local = itemSelecionado.text().trim();
        $(".local_venda").text('Local: ' + desc_local);
        
        var itemSelecionado = $("#codigo_cliente option:selected");
        var desc_cliente = itemSelecionado.text().trim();
        $(".cliente_venda").text('Comprador: ' + desc_cliente);

        $(".tela_peso_vivo").hide();
        $(".tela_peso_morto").hide();
        $(".tela_cabeca").hide();

        var tem_movimentacao = $("input[name='tem_movimentacao']:checked").val();
        var tipo_venda = $("input[name='tipo_venda']:checked").val();

        switch (tipo_venda) {
            case 'V':
                $(".tipo_venda").text('Venda: Peso Vivo');
                if (tem_movimentacao=="S") {
                    var exibe_itens = exibe_itens_movimentacao();
                    document.getElementById("categoria_vivo").focus();
                }
                else {
                    //$('#modal_peso_vivo').modal('show');
                    $(".tela_dados").hide();
                    $(".linha_escondida").show();
                    $(".tela_peso_vivo").show();
                    $(".editar").hide();
                    $(".incluir").show();
                    document.getElementById("categoria_vivo").focus();
                }
                break;
            case 'M':
                $(".tipo_venda").text('Venda: Peso Morto');
                if (tem_movimentacao=="S") {
                    var exibe_itens = exibe_itens_movimentacao();
                    document.getElementById("qtd_morto").focus();
                }
                else {
                    $(".tela_dados").hide();
                    $(".linha_escondida").show();
                    $(".tela_peso_morto").show();
                    $(".editar").hide();
                    $(".incluir").show();
                    document.getElementById("qtd_morto").focus();
                }
                break;
            case 'C':   
                $(".tipo_venda").text('Venda: Peso Cabeça');
                if (tem_movimentacao=="S") {
                    var exibe_itens = exibe_itens_movimentacao();
                    document.getElementById("categoria_cabeca").focus();
                }
                else {
                    $(".tela_dados").hide();
                    $(".linha_escondida").show();
                    $(".tela_cabeca").show();
                    $(".editar").hide();
                    $(".incluir").show();
                    document.getElementById("categoria_cabeca").focus();
                }
                break;
        } 

    });

    $('.tem_movimentacao').click(function(event) {
        //$(".fazer_movimentacao").prop("checked", false);
        $(".tipo_venda").prop("checked", false);
        $(".local_comprador").hide();
        $("#local").val('000000000');
        $("#codigo_cliente").val('000000000');

        var tem_movimentacao = $("input[name='tem_movimentacao']:checked").val();

        switch (tem_movimentacao) {
        case 'S':
            $(".lista_movimentacao").show();
           // $(".opcao_fazer_movimentacao").hide();
            $.post("lista_movimentacao.php", {tipo:3}, function(valor){
                $("select[name=lista_movimentacao]").html(valor);
            });
            break;
        case 'N':
            $(".lista_movimentacao").hide();
            //$(".opcao_fazer_movimentacao").show();
            $(".local_comprador").show();
            break;
        } 
    });

   $('#lista_movimentacao').change(function(event) {
        $(".local_comprador").show();

        var num_doc = $("#lista_movimentacao").val();
        $.post("ler_movimentacao_venda.php", {num_doc:num_doc}, function(valor){

            if (valor==0) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Não exitem dados para essa movimentação.');
                return;
            }

            var php = valor.split("<|>");
            $("#local").val(php[0]);
            $("#codigo_cliente").val(php[1]);
        });
    });

    $('#categoria_vivo').change(function(event) {
        document.getElementById("sexo_vivo").focus();
    });

    $('#sexo_vivo').change(function(event) {
        document.getElementById("qtd_vivo").focus();
    });

   $('#peso_categoria_vivo').change(function(event) {
        soma_total_item_vivo();
   });

   $('#fator_arroba_vivo').change(function(event) {
        soma_total_item_vivo();
   });

   /*$('#arroba_categoria_vivo').change(function(event) {
        soma_total_item_vivo();
   });*/

   $('#unidade_vivo').change(function(event) {
        document.getElementById("valor_unitario_vivo").focus();
        soma_total_item_vivo();
   });

   $('#valor_unitario_vivo').change(function(event) {
        soma_total_item_vivo();
   });

   $('#total_vivo').focus(function(event) {
        document.getElementById("conta_vivo").focus();
   });

    $('#sexo_morto').change(function(event) {
        document.getElementById("peso_categoria_morto").focus();
    });

    $('#unidade_morto').change(function(event) {
        document.getElementById("peso_abate_morto").focus();
    });

   $('#rendimento_morto').focus(function(event) {
        document.getElementById("conta_morto").focus();
   });

   $('#valor_unitario_morto').change(function(event) {
        soma_total_item_morto();
   });

   $('#peso_categoria_ajustado_morto').change(function(event) {
        soma_total_item_morto();
   });

   $('#peso_abate_morto').change(function(event) {
        soma_total_item_morto();
   });

   $('#arroba_abate_morto').change(function(event) {
        soma_total_item_morto();
   });

   $('#unidade_morto').change(function(event) {
        soma_total_item_morto();
   });

    $('#categoria_cabeca').change(function(event) {
        document.getElementById("sexo_cabeca").focus();
    });

    $('#sexo_cabeca').change(function(event) {
        document.getElementById("qtd_cabeca").focus();
    });

   $('#qtd_cabeca').change(function(event) {
        soma_total_item_cabeca();
   });

   $('#peso_cabeca').change(function(event) {
        soma_total_item_cabeca();
   });

   $('#valor_unitario_cabeca').change(function(event) {
        soma_total_item_cabeca();
   });

   $('#total_cabeca').focus(function(event) {
        document.getElementById("conta_cabeca").focus();
   });

    $(".fecha_inserir_parcela").click(function(){
        $('.modal_inserir_parcela').modal('hide');
    });

    $('#data_venda').change(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const data_venda = $("#data_venda").val();

        if (data_venda>data_atual) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('A Data não pode ser maior que a data atual!');
            $("#data_venda").val(data_atual);
            document.getElementById("data_venda").style.borderColor = "#0076d7";
        }
    });    

    $('#data_venda').blur(function(){
        const data_venda = $("#data_venda").val();

        if (data_venda=='') {
            const date = new Date();
            const data_atual = date.getFullYear()+'-'+
                              (date.getMonth()+1).AddZero()+'-'+
                              date.getDate().AddZero();

            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('A Data da Venda precisa ser informada!');
            $("#data_venda").val(data_atual);
            document.getElementById("data_venda").style.borderColor = "#0076d7";
        }
    });    
});

function exibe_itens_movimentacao() {
    var num_doc = $("#lista_movimentacao").val();
    $.post("ler_itens_movimentacao_venda.php", {num_doc:num_doc}, function(valor){
        if (valor==0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Não exitem dados para essa movimentação.');
            return;
        }

        var php = valor.split("<|>");
        var id_cat = php[0].split("|");
        var desc_cat = php[1].split("|");
        var total_cat = php[2].split("|");
        var total_m = php[3].split("|");
        var total_f = php[4].split("|");
        var peso_m = php[5].split("|");
        var peso_f = php[6].split("|");
        var numero_itens = desc_cat.length;

        var tipo_venda = $("input[name='tipo_venda']:checked").val();

        switch (tipo_venda) {
            case 'V':
                html = "";
                html += '<table class="table table-advance responsive-table tabela_itens_vivo" id="tabela_itens_vivo" width="100%" style="font-size: 13px;"';
                html += '<thead>';
                html += '<tr>';
                html += '<th>' + 'Categoria' + '</th>';
                html += '<th style="text-align: center;">' + 'Sexo' + '</th>';
                html += '<th style="text-align: right;">' + 'Qtde Animais' + '</th>';
                html += '<th style="text-align: right;">' + 'Peso Total kg' + '</th>';
                html += '<th style="text-align: right;">' + 'Peso @' + '</th>';
                html += '<th style="text-align: center;">' + 'Und Negociada' + '</th>';
                html += '<th style="text-align: right;">' + 'Valor Unitário' + '</th>';
                html += '<th style="text-align: right;">' + 'Valor Total' + '</th>';
                html += '<th>' + '<i class="icon_cogs"></i> Ações' + '</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                for (var i = 0; i < numero_itens; i++) {
                    var codigo_id = id_cat[i];
                    var descricao = desc_cat[i];
                    var total = total_cat[i];
                    var macho = total_m[i];
                    var femea = total_f[i];
                    var peso_macho = formatMoney(peso_m[i]);
                    var peso_femea = formatMoney(peso_f[i]);

                    if (descricao!='' && macho!=0) {
                        html += '<tr>';
                        html += '<td width="15%" class="categoria_vivo">' + descricao + '</td>';
                        html += '<td width="5%" class="sexo_vivo" style="text-align: center;">' + "M" + '</td>';
                        html += '<td width="10%" class="qtd_vivo" style="text-align: right;">' + macho + '</td>';
                        html += '<td width="10%" class="peso_vivo" style="text-align: right;">' + peso_macho + '</td>';
                        html += '<td width="10%" class="arroba_vivo" style="text-align: right;">' + '' + '</td>';
                        html += '<td width="10%" class="und_vivo"  style="text-align: center;">' + '' + '</td>';
                        html += '<td width="10%" class="valor_unit_vivo"  style="text-align: right;">' + '' + '</td>';
                        html += '<td width="10%" class="valor_total_vivo"  style="text-align: right;">' + '' + '</td>';
                        html += "<td hidden='' class='categoria_vivo_id'>" + codigo_id + "</td>";
                        html += "<td hidden='' class='und_vivo_id'>" + 0 + "</td>";
                        html += "<td hidden='' class='conta_vivo_id'>" + 0 + "</td>";
                        html += "<td hidden='' class='fator_arroba_vivo'>" + '' + "</td>";
                        html += "<td width='10%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>";
                        html += '</tr>';
                    }

                    if (descricao!='' && femea!=0) {
                        html += '<tr>';
                        html += '<td width="15%" class="categoria_vivo">' + descricao + '</td>';
                        html += '<td width="5%" class="sexo_vivo" style="text-align: center;">' + "F" + '</td>';
                        html += '<td width="10%" class="qtd_vivo" style="text-align: right;">' + femea + '</td>';
                        html += '<td width="10%" class="peso_vivo" style="text-align: right;">' + peso_femea + '</td>';
                        html += '<td width="10%" class="arroba_vivo" style="text-align: right;">' + '' + '</td>';
                        html += '<td width="15%" class="und_vivo"  style="text-align: center;">' + '' + '</td>';
                        html += '<td width="10%" class="valor_unit_vivo"  style="text-align: right;">' + '' + '</td>';
                        html += '<td width="10%" class="valor_total_vivo"  style="text-align: right;">' + '' + '</td>';
                        html += "<td hidden='' class='categoria_vivo_id'>" + codigo_id + "</td>";
                        html += "<td hidden='' class='und_vivo_id'>" + 0 + "</td>";
                        html += "<td hidden='' class='conta_vivo_id'>" + 0 + "</td>";
                        html += "<td hidden='' class='fator_arroba_vivo'>" + '' + "</td>";
                        html += "<td width='10%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>";
                        html += '</tr>';
                    }
                }

                html += '</tbody>';
                html += '</table>';
                document.getElementById('tabela_itens_vivo').innerHTML = html;

                $(".btnEditar").bind("click", editar_item_animal_vivo);
                $(".btnExcluir").bind("click", excuir_item_animal_vivo);
                $(".tela_dados").hide();
                $(".linha_escondida").hide();
                $(".tela_peso_vivo").show();
                $(".tabela_itens_vivo").show();
                $(".editar").hide();
                $(".incluir").hide();
            break;
            case 'M':
                html = "";
                html += '<table class="table table-advance responsive-table tabela_itens_morto" id="tabela_itens_morto" width="100%" style="font-size: 11px;"';
                html += '<thead>';
                html += '<tr>';
                html += '<th style="text-align: right;">' + 'Qtde Animais' + '</th>';
                html += '<th style="text-align: center;">' + 'Sexo' + '</th>';
                html += '<th style="text-align: right;">' + 'Peso Vivo Kg' + '</th>';
                html += '<th style="text-align: right;">' + 'Peso Ajustado Kg' + '</th>';
                html += '<th style="text-align: right;">' + 'Peso Morto Kg' + '</th>';
                html += '<th style="text-align: right;">' + 'Peso Morto @' + '</th>';
                html += '<th>' + 'Und Negociada' + '</th>';
                html += '<th>' + 'Valor Unitário' + '</th>';
                html += '<th>' + 'Valor Total' + '</th>';
                html += '<th>' + 'Rendimento Carcaça' + '</th>';
                html += '<th>' + '<i class="icon_cogs"></i> Ações' + '</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                var total_macho = 0;
                var total_peso_macho = 0;

                for (var i = 0; i < numero_itens; i++) {
                    var codigo_id = id_cat[i];
                    //var descricao = desc_cat[i];
                    //var total = total_cat[i];
                    total_macho+= parseFloat(total_m[i]);
                    //var femea = total_f[i];
                    total_peso_macho+= parseFloat(peso_m[i]);
                    //var peso_femea = peso_f[i];
                }

                if (total_macho!=0) {

                    total_peso_macho = formatMoney(total_peso_macho);

                    html += '<tr>';
                    html += '<td width="8%" class="qtd_morto" style="text-align: right;">' + total_macho + '</td>';
                    html += '<td width="8%" class="sexo_morto" style="text-align: center;">' + "M" + '</td>';
                    html += '<td width="8%" class="peso_morto" style="text-align: right;">' + total_peso_macho + '</td>';
                    html += '<td width="10%" class="peso_ajustado_morto" style="text-align: right;">' + '' + '</td>';
                    html += '<td width="9%" class="peso_abate_morto" style="text-align: right;">' + '' + '</td>';
                    html += '<td width="8%" class="arroba_abate_morto" style="text-align: right;">' + '' + '</td>';
                    html += '<td width="9%" class="und_morto" style="text-align: center;">' + '' + '</td>';
                    html += '<td width="8%" class="valor_unit_morto">' + '' + '</td>';
                    html += '<td width="8%" class="valor_total_morto">' + '' + '</td>';
                    html += '<td width="12%" class="rendimento_morto">' + '' + '</td>';
                    html += "<td hidden='' class='categoria_morto_id'>" + codigo_id + "</td>";
                    html += "<td hidden='' class='und_morto_id'>" + 0 + "</td>";
                    html += "<td hidden='' class='conta_morto_id'>" + 0 + "</td>";
                    html += "<td width='12%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>";
                    html += '</tr>';
                }

                var total_femea = 0;
                var total_peso_femea = 0;

                for (var i = 0; i < numero_itens; i++) {
                    var codigo_id = id_cat[i];
                    //var descricao = desc_cat[i];
                    //var total = total_cat[i];
                    total_femea+= parseFloat(total_f[i]);
                    //var femea = total_f[i];
                    total_peso_femea+= parseFloat(peso_f[i]);
                    //var peso_femea = peso_f[i];
                }

                if (total_femea!=0) {

                    total_peso_femea = formatMoney(total_peso_femea);

                    html += '<tr>';
                    html += '<td width="8%" class="qtd_morto" style="text-align: right;">' + total_femea + '</td>';
                    html += '<td width="8%" class="sexo_morto" style="text-align: center;">' + "F" + '</td>';
                    html += '<td width="8%" class="peso_morto" style="text-align: right;">' + total_peso_femea + '</td>';
                    html += '<td width="10%" class="peso_ajustado_morto" style="text-align: right;">' + '' + '</td>';
                    html += '<td width="9%" class="peso_abate_morto" style="text-align: right;">' + '' + '</td>';
                    html += '<td width="8%" class="arroba_abate_morto" style="text-align: right;">' + '' + '</td>';
                    html += '<td width="9%" class="und_morto" style="text-align: center;">' + '' + '</td>';
                    html += '<td width="8%" class="valor_unit_morto">' + '' + '</td>';
                    html += '<td width="8%" class="valor_total_morto">' + '' + '</td>';
                    html += '<td width="12%" class="rendimento_morto">' + '' + '</td>';
                    html += "<td hidden='' class='categoria_morto_id'>" + codigo_id + "</td>";
                    html += "<td hidden='' class='und_morto_id'>" + 0 + "</td>";
                    html += "<td hidden='' class='conta_morto_id'>" + 0 + "</td>";
                    html += "<td width='12%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>";
                    html += '</tr>';
                }

                html += '</tbody>';
                html += '</table>';

                document.getElementById('tabela_itens_morto').innerHTML = html;
                $(".btnEditar").bind("click", editar_item_animal_morto);
                $(".btnExcluir").bind("click", excuir_item_animal_morto);
                $(".tela_dados").hide();
                $(".linha_escondida").hide();
                $(".tela_peso_morto").show();
                $(".tabela_itens_morto").show();
                $(".editar").hide();
                $(".incluir").hide();
            break;
            case 'C':   
                html = "";
                html += '<table class="table table-advance responsive-table tabela_itens_cabeca" id="tabela_itens_cabeca" width="100%" style="font-size: 13px;"';
                html += '<thead>';
                html += '<tr>';
                html += '<th>' + 'Categoria' + '</th>';
                html += '<th style="text-align: center;">' + 'Sexo' + '</th>';
                html += '<th style="text-align: right;">' + 'Qtde Animais' + '</th>';
                html += '<th style="text-align: right;">' + 'Peso' + '</th>';
                html += '<th style="text-align: right;">' + 'Valor Unitário' + '</th>';
                html += '<th style="text-align: right;">' + 'Valor Total' + '</th>';
                html += '<th style="text-align: right;">' + 'R$/@ Aproximado' + '</th>';
                html += '<th>' + '<i class="icon_cogs"></i> Ações' + '</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                for (var i = 0; i < numero_itens; i++) {
                    var codigo_id = id_cat[i];
                    var descricao = desc_cat[i];
                    var total = total_cat[i];
                    var macho = total_m[i];
                    var femea = total_f[i];
                    var peso_macho = formatMoney(peso_m[i]);
                    var peso_femea = formatMoney(peso_f[i]);

                    if (descricao!='' && macho!=0) {
                        html += '<tr>';
                        html += '<td width="15%" class="categoria_cabeca">' + descricao + '</td>';
                        html += '<td width="5%" class="sexo_cabeca" style="text-align: center;">' + "M" + '</td>';
                        html += '<td width="10%" class="qtd_cabeca" style="text-align: right;">' + macho + '</td>';
                        html += '<td width="10%" class="peso_cabeca" style="text-align: right;">' + peso_macho + '</td>';
                        html += '<td width="10%" class="valor_unit_cabeca" style="text-align: right;">' + '' + '</td>';
                        html += '<td width="10%" class="valor_total_cabeca" style="text-align: right;">' + '' + '</td>';
                        html += '<td width="15%" class="arroba_cabeca" style="text-align: right;">' + '' + '</td>';
                        html += "<td hidden='' class='categoria_cabeca_id'>" + codigo_id + "</td>";
                        html += "<td hidden='' class='conta_cabeca_id'>" + 0 + "</td>";
                        html += "<td width='10%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>";
                        html += '</tr>';
                    }

                    if (descricao!='' && femea!=0) {
                        html += '<tr>';
                        html += '<td width="15%" class="categoria_cabeca">' + descricao + '</td>';
                        html += '<td width="5%" class="sexo_cabeca" style="text-align: center;">' + "F" + '</td>';
                        html += '<td width="10%" class="qtd_cabeca" style="text-align: right;">' + femea + '</td>';
                        html += '<td width="10%" class="peso_cabeca" style="text-align: right;">' + peso_femea + '</td>';
                        html += '<td width="10%" class="valor_unit_cabeca" style="text-align: right;">' + '' + '</td>';
                        html += '<td width="10%" class="valor_total_cabeca" style="text-align: right;">' + '' + '</td>';
                        html += '<td width="15%" class="arroba_cabeca" style="text-align: right;">' + '' + '</td>';
                        html += "<td hidden='' class='categoria_cabeca_id'>" + codigo_id + "</td>";
                        html += "<td hidden='' class='conta_cabeca_id'>" + 0 + "</td>";
                        html += "<td width='10%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>";
                        html += '</tr>';
                    }
                }
                html += '</tbody>';
                html += '</table>';
                document.getElementById('tabela_itens_cabeca').innerHTML = html;
                $(".btnEditar").bind("click", editar_item_animal_cabeca);
                $(".btnExcluir").bind("click", excuir_item_animal_cabeca);
                $(".tela_dados").hide();
                $(".linha_escondida").hide();
                $(".tela_cabeca").show();
                $(".tabela_itens_cabeca").show();
                $(".editar").hide();
                $(".incluir").hide();
            break;
        } 
    });
}

function salvar_item_animal_vivo() {
    if ($("#categoria_vivo").val()==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a categoria');
        return;
    }

    var itemSelecionado = $("#categoria_vivo option:selected");
    var desc_categoria = itemSelecionado.text().trim();

    if ($("#sexo_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o sexo');
        return;
    }

    if ($("#qtd_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a quantidade');
        return;
    }

    if ($("#peso_categoria_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso Kg');
        return;
    }

    if ($("#arroba_categoria_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso @');
        return;
    }

    if ($("#unidade_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a unidade negociada');
        return;
    }

    var itemSelecionado = $("#unidade_vivo option:selected");
    var desc_unidade = itemSelecionado.text().trim();

    if ($("#valor_unitario_vivo").val()=='' || $("#valor_unitario_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o preço unitário');
        return;
    }

    if ($("#conta_vivo").val()=='0000000'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil');
        return;
    }

    $(".tabela_itens_vivo").show();

    $(".tabela_itens_vivo tbody").append(
        "<tr>"+
        "<td width='15%' class='categoria_vivo'>" + desc_categoria + "</td>"+
        "<td width='5%' class='sexo_vivo'>" + $("#sexo_vivo").val() + "</td>"+
        "<td width='10%' class='qtd_vivo' align='right'>" + $("#qtd_vivo").val() + "</td>"+
        "<td width='10%' class='peso_vivo' align='right'>" + $("#peso_categoria_vivo").val() + "</td>"+
        "<td width='10%' class='arroba_vivo' align='right'>" + $("#arroba_categoria_vivo").val() + "</td>"+
        "<td width='10%' class='und_vivo' align='center'>" + desc_unidade + "</td>"+
        "<td width='10%' class='valor_unit_vivo' align='right'>" + $("#valor_unitario_vivo").val() + "</td>"+
        "<td width='10%' class='valor_total_vivo' align='right'>" + $("#total_vivo").val() + "</td>"+
        "<td hidden='' class='categoria_vivo_id' align='right'>" + $("#categoria_vivo").val() + "</td>"+
        "<td hidden='' class='und_vivo_id'>" + $("#unidade_vivo").val() + "</td>"+
        "<td hidden='' class='conta_vivo_id'>" + $("#conta_vivo").val() + "</td>"+
        "<td hidden='' class='fator_arroba_vivo'>" + $("#fator_arroba_vivo").val() + "</td>"+
        "<td width='10%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>"+
        "</tr>");

    $(".btnEditar").bind("click", editar_item_animal_vivo);
    $(".btnExcluir").bind("click", excuir_item_animal_vivo);
    $("#categoria_vivo").val('000');
    $("#sexo_vivo").val('');
    $("#qtd_vivo").val('');
    $("#peso_categoria_vivo").val('');
    $("#fator_arroba_vivo").val('');
    $("#arroba_categoria_vivo").val('');
    $("#arrobaHelpBlock").text('');
    $("#unidade_vivo").val('');
    $("#conta_vivo").val('0000000');
    $("#valor_unitario_vivo").val('');
    $("#total_vivo").val('');
    document.getElementById("categoria_vivo").focus(); 

    somar_total_geral_vivo();
}

function salvar_editar_item_vivo(){
    if ($("#categoria_vivo").val()==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a categoria');
        return;
    }

    var itemSelecionado = $("#categoria_vivo option:selected");
    var desc_categoria = itemSelecionado.text().trim();

    if ($("#sexo_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o sexo');
        return;
    }

    if ($("#qtd_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a quantidade');
        return;
    }

    if ($("#peso_categoria_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso Kg');
        return;
    }

    if ($("#arroba_categoria_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso @');
        return;
    }

    if ($("#unidade_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a unidade negociada');
        return;
    }

    var itemSelecionado = $("#unidade_vivo option:selected");
    var desc_unidade = itemSelecionado.text().trim();

    if ($("#valor_unitario_vivo").val()==0 || $("#valor_unitario_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o preço unitário');
        return;
    }

    if ($("#conta_vivo").val()=='0000000'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil');
        return;
    }

    $('.tabela_itens_vivo tbody tr').each(function(){
        row_index_salvar = $(this).index();

        if (row_index_salvar==row_index){
            $(this).find('.categoria_vivo').html(desc_categoria);
            $(this).find('.sexo_vivo').html($("#sexo_vivo").val());
            $(this).find('.qtd_vivo').html($("#qtd_vivo").val());
            $(this).find('.peso_vivo').html($("#peso_categoria_vivo").val());
            $(this).find('.arroba_vivo').html($("#arroba_categoria_vivo").val());
            $(this).find('.und_vivo').html(desc_unidade);
            $(this).find('.valor_unit_vivo').html($("#valor_unitario_vivo").val());
            $(this).find('.valor_total_vivo').html($("#total_vivo").val());
            $(this).find('.categoria_vivo_id').html($("#categoria_vivo").val());
            $(this).find('.und_vivo_id').html($("#unidade_vivo").val());
            $(this).find('.conta_vivo_id').html($("#conta_vivo").val());
            $(this).find('.fator_arroba_vivo').html($("#fator_arroba_vivo").val());
        }
    });

    $(".btnEditar").bind("click", editar_item_animal_vivo);
    $(".btnExcluir").bind("click", excuir_item_animal_vivo);
    $("#categoria_vivo").val('000');
    $("#sexo_vivo").val('');
    $("#qtd_vivo").val('');
    $("#peso_categoria_vivo").val('');
    $("#fator_arroba_vivo").val('');
    $("#arroba_categoria_vivo").val('');
    $("#arrobaHelpBlock").text('');
    $("#unidade_vivo").val('');
    $("#valor_unitario_vivo").val('');
    $("#total_vivo").val('');
    $("#conta_vivo").val('0000000');
    $(".editar").hide();
    $(".incluir").show();

    var tem_movimentacao = $("input[name='tem_movimentacao']:checked").val();

    if (tem_movimentacao=="S") {
        $(".linha_escondida").hide();
    }

    document.getElementById("categoria_vivo").focus(); 
    somar_total_geral_vivo();
}

function salvar_item_animal_morto() {
    if ($("#sexo_morto").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o sexo');
        return;
    }

    if ($("#qtd_morto").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a quantidade');
        return;
    }

    if ($("#peso_abate_morto").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso morto Kg');
        return;
    }

    if ($("#arroba_abate_morto").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso morto @');
        return;
    }

    if ($("#unidade_morto").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a unidade negociada');
        return;
    }

    var itemSelecionado = $("#unidade_morto option:selected");
    var desc_unidade = itemSelecionado.text().trim();

    if ($("#valor_unitario_morto").val()==''|| $("#valor_unitario_morto").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o preço unitário');
        return;
    }

    if ($("#conta_morto").val()=='0000000'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil');
        return;
    }

    $(".tabela_itens_morto").show();

    $(".tabela_itens_morto tbody").append(
        "<tr>"+
        "<td width='8%' class='qtd_morto'>" + $("#qtd_morto").val() + "</td>"+
        "<td width='8%' class='sexo_morto'>" + $("#sexo_morto").val() + "</td>"+
        "<td width='8%' class='peso_morto'>" + $("#peso_categoria_morto").val() + "</td>"+
        "<td width='10%' class='peso_ajustado_morto'>" + $("#peso_categoria_ajustado_morto").val() + "</td>"+
        "<td width='9%' class='peso_abate_morto'>" + $("#peso_abate_morto").val() + "</td>"+
        "<td width='8%' class='arroba_abate_morto'>" + $("#arroba_abate_morto").val() + "</td>"+
        "<td width='9%' class='und_morto'>" + desc_unidade + "</td>"+
        "<td width='8%' class='valor_unit_morto'>" + $("#valor_unitario_morto").val() + "</td>"+
        "<td width='8%' class='valor_total_morto'>" + $("#total_morto").val() + "</td>"+
        "<td width='14%' class='rendimento_morto'>" + $("#rendimento_morto").val() + "</td>"+
        "<td hidden='' class='categoria_morto_id'>" + $("#categoria_morto").val() + "</td>"+
        "<td hidden='' class='und_morto_id'>" + $("#unidade_morto").val() + "</td>"+
        "<td hidden='' class='conta_morto_id'>" + $("#conta_morto").val() + "</td>"+
        "<td width='12%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>"+
        "</tr>");

    $(".btnEditar").bind("click", editar_item_animal_morto);
    $(".btnExcluir").bind("click", excuir_item_animal_morto);
    $("#categoria_morto").val('000');
    $("#sexo_morto").val('');
    $("#qtd_morto").val('');
    $("#peso_categoria_morto").val('');
    $("#peso_categoria_ajustado_morto").val('');
    $("#peso_abate_morto").val('');
    $("#arroba_abate_morto").val('');
    $("#arrobamortoHelpBlock").text('');
    $("#unidade_morto").val('');
    $("#conta_morto").val('0000000');
    $("#valor_unitario_morto").val('');
    //$("#qtd_total_morto").val('');
    $("#total_morto").val('');
    $("#rendimento_morto").val('');
    document.getElementById("qtd_morto").focus(); 
    somar_total_geral_morto();

}

function salvar_editar_item_morto(){
    if ($("#sexo_morto").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o sexo');
        return;
    }

    if ($("#qtd_morto").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a quantidade');
        return;
    }

    if ($("#peso_abate_morto").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso morto Kg');
        return;
    }

    if ($("#arroba_abate_morto").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso morto @');
        return;
    }

    if ($("#unidade_morto").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a unidade negociada');
        return;
    }

    var itemSelecionado = $("#unidade_morto option:selected");
    var desc_unidade = itemSelecionado.text().trim();

    if ($("#valor_unitario_morto").val()=='' || $("#valor_unitario_morto").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o preço unitário');
        return;
    }

    if ($("#total_morto").val()=='' || $("#total_morto").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o valor total');
        return;
    }

    if ($("#conta_morto").val()=='0000000'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil');
        return;
    }

    $('.tabela_itens_morto tbody tr').each(function(){
        row_index_salvar = $(this).index();

        if (row_index_salvar==row_index){
            $(this).find('.qtd_morto').html($("#qtd_morto").val());
            $(this).find('.sexo_morto').html($("#sexo_morto").val());
            $(this).find('.peso_morto').html($("#peso_categoria_morto").val());
            $(this).find('.peso_ajustado_morto').html($("#peso_categoria_ajustado_morto").val());
            $(this).find('.peso_abate_morto').html($("#peso_abate_morto").val());
            $(this).find('.arroba_abate_morto').html($("#arroba_abate_morto").val());
            $(this).find('.und_morto').html(desc_unidade);
            $(this).find('.valor_unit_morto').html($("#valor_unitario_morto").val());
            $(this).find('.valor_total_morto').html($("#total_morto").val());
            $(this).find('.rendimento_morto').html($("#rendimento_morto").val());
            $(this).find('.categoria_morto_id').html($("#categoria_morto").val());
            $(this).find('.und_morto_id').html($("#unidade_morto").val());
            $(this).find('.conta_morto_id').html($("#conta_morto").val());
        }
    });

    $(".btnEditar").bind("click", editar_item_animal_morto);
    $(".btnExcluir").bind("click", excuir_item_animal_morto);
    $("#categoria_morto").val('000');
    $("#sexo_morto").val('');
    $("#qtd_morto").val('');
    $("#peso_categoria_morto").val('');
    $("#peso_categoria_ajustado_morto").val('');
    $("#peso_abate_morto").val('');
    $("#arroba_abate_morto").val('');
    $("#arrobamortoHelpBlock").text('');
    $("#unidade_morto").val('');
    $("#valor_unitario_morto").val('');
    //$("#qtd_total_morto").val('');
    $("#total_morto").val('');
    $("#conta_morto").val('0000000');
    $("#rendimento_morto").val('');
    $(".editar").hide();
    $(".incluir").hide();

    var tem_movimentacao = $("input[name='tem_movimentacao']:checked").val();

    if (tem_movimentacao=="S") {
        $(".linha_escondida").hide();
    }

    document.getElementById("qtd_morto").focus(); 
    somar_total_geral_morto();
}

function salvar_item_animal_cabeca() {
    if ($("#categoria_cabeca").val()==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a categoria');
        return;
    }

    var itemSelecionado = $("#categoria_cabeca option:selected");
    var desc_categoria = itemSelecionado.text().trim();

    if ($("#sexo_cabeca").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o sexo');
        return;
    }

    if ($("#qtd_cabeca").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a quantidade');
        return;
    }

    if ($("#valor_unitario_cabeca").val()=='' || $("#valor_unitario_cabeca").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o preço unitário');
        return;
    }

    if ($("#conta_cabeca").val()=='0000000'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil');
        return;
    }

    $(".tabela_itens_cabeca").show();

    $(".tabela_itens_cabeca tbody").append(
        "<tr>"+
        "<td width='15%' class='categoria_cabeca'>" + desc_categoria + "</td>"+
        "<td width='5%' class='sexo_cabeca'>" + $("#sexo_cabeca").val() + "</td>"+
        "<td width='10%' class='qtd_cabeca'>" + $("#qtd_cabeca").val() + "</td>"+
        "<td width='10%' class='peso_cabeca'>" + $("#peso_cabeca").val() + "</td>"+
        "<td width='10%' class='valor_unit_cabeca'>" + $("#valor_unitario_cabeca").val() + "</td>"+
        "<td width='10%' class='valor_total_cabeca'>" + $("#total_cabeca").val() + "</td>"+
        "<td width='15%' class='arroba_cabeca'>" + $("#arroba_cabeca").val() + "</td>"+
        "<td hidden='' class='categoria_cabeca_id'>" + $("#categoria_cabeca").val() + "</td>"+
        "<td hidden='' class='conta_cabeca_id'>" + $("#conta_cabeca").val() + "</td>"+
        "<td width='12%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>"+
        "</tr>");

    $(".btnEditar").bind("click", editar_item_animal_cabeca);
    $(".btnExcluir").bind("click", excuir_item_animal_cabeca);
    $("#categoria_cabeca").val('000');
    $("#sexo_cabeca").val('');
    $("#qtd_cabeca").val('');
    $("#valor_unitario_cabeca").val('');
    $("#total_cabeca").val('');
    $("#arroba_cabeca").val('');
    $("#peso_cabeca").val('');
    $("#conta_cabeca").val('0000000');
    $(".editar").hide();
    $(".incluir").show();

    document.getElementById("categoria_cabeca").focus(); 
    somar_total_geral_cabeca();
}

function salvar_editar_item_cabeca() {
    if ($("#categoria_cabeca").val()==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a categoria');
        return;
    }

    var itemSelecionado = $("#categoria_cabeca option:selected");
    var desc_categoria = itemSelecionado.text().trim();

    if ($("#sexo_cabeca").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o sexo');
        return;
    }

    if ($("#qtd_cabeca").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a quantidade');
        return;
    }

    if ($("#valor_unitario_cabeca").val()=='' || $("#valor_unitario_cabeca").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o preço unitário');
        return;
    }

    if ($("#conta_cabeca").val()=='0000000'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil');
        return;
    }

    $('.tabela_itens_cabeca tbody tr').each(function(){
        row_index_salvar = $(this).index();

        if (row_index_salvar==row_index){
            $(this).find('.categoria_cabeca').html(desc_categoria);
            $(this).find('.sexo_cabeca').html($("#sexo_cabeca").val());
            $(this).find('.qtd_cabeca').html($("#qtd_cabeca").val());
            $(this).find('.peso_cabeca').html($("#peso_cabeca").val());
            $(this).find('.valor_unit_cabeca').html($("#valor_unitario_cabeca").val());
            $(this).find('.valor_total_cabeca').html($("#total_cabeca").val());
            $(this).find('.arroba_cabeca').html($("#arroba_cabeca").val());
            $(this).find('.categoria_cabeca_id').html($("#categoria_cabeca").val());
            $(this).find('.conta_cabeca_id').html($("#conta_cabeca").val());
        }
    });

    $(".btnEditar").bind("click", editar_item_animal_cabeca);
    $(".btnExcluir").bind("click", excuir_item_animal_cabeca);
    $("#categoria_cabeca").val('000');
    $("#sexo_cabeca").val('');
    $("#qtd_cabeca").val('');
    $("#valor_unitario_cabeca").val('');
    $("#total_cabeca").val('');
    $("#arroba_cabeca").val('');
    $("#peso_cabeca").val('');
    $("#conta_cabeca").val('000000');
    $(".editar").hide();
    $(".incluir").show();

    var tem_movimentacao = $("input[name='tem_movimentacao']:checked").val();

    if (tem_movimentacao=="S") {
        $(".linha_escondida").hide();
    }

    somar_total_geral_cabeca();
}

function editar_item_animal_vivo() {
    row_index = $(this).parent().parent().index();

    var par = $(this).parent().parent(); //tr
    var tdcategoria_vivo = par.children("td:nth-child(1)");
    var tdsexo_vivo = par.children("td:nth-child(2)");
    var tdqtd_vivo = par.children("td:nth-child(3)");
    var tdpeso_vivo = par.children("td:nth-child(4)");
    var tdarroba_vivo = par.children("td:nth-child(5)");
    var tdund_vivo = par.children("td:nth-child(6)");
    var tdvalor_unit_vivo = par.children("td:nth-child(7)");
    var tdvalor_total_vivo = par.children("td:nth-child(8)");
    var tdcategoria_vivo_id = par.children("td:nth-child(9)");
    var tdund_vivo_id = par.children("td:nth-child(10)");
    var tdconta_vivo_id = par.children("td:nth-child(11)");
    var tdfator_arroba_vivo = par.children("td:nth-child(12)");

    $("#categoria_vivo").val(tdcategoria_vivo_id.html());
    $("#sexo_vivo").val(tdsexo_vivo.html());
    $("#qtd_vivo").val(tdqtd_vivo.html());
    $("#peso_categoria_vivo").val(tdpeso_vivo.html());

    if (tdfator_arroba_vivo.html()!=0 && 
        tdfator_arroba_vivo.html()!='') {
        $("#fator_arroba_vivo").val(tdfator_arroba_vivo.html());
    }
    else {
        $("#fator_arroba_vivo").val(0.031777);
    }

    $("#arroba_categoria_vivo").val(tdarroba_vivo.html());
    $("#unidade_vivo").val(tdund_vivo_id.html());
    $("#valor_unitario_vivo").val(tdvalor_unit_vivo.html());
    $("#total_vivo").val(tdvalor_total_vivo.html());
    $("#conta_vivo").val(tdconta_vivo_id.html());
    $(".editar").show();
    $(".incluir").hide();
    $(".linha_escondida").show();

    var peso_kg = $("#peso_categoria_vivo").val();
    var fator_arroba = $("#fator_arroba_vivo").val();

    if (verifica_virgula(peso_kg)==',') {
        peso_kg = replace_valor(peso_kg);
    }

    if (fator_arroba!=0) {
        if (verifica_virgula(fator_arroba)==',') {
            fator_arroba = replace_valor(fator_arroba);
        }
    }
    else {
        fator_arroba=0.031777;
    }

    if (fator_arroba==0) {
        var total_arroba = peso_kg*fator_arroba;
        total_arroba = Math.round(total_arroba);
        $("#arrobaHelpBlock").text('Peso Total Kg*0,031777=' + total_arroba);
    }
    else {
        var total_arroba = peso_kg*fator_arroba;
        total_arroba = Math.round(total_arroba);
        $("#arrobaHelpBlock").text('Peso Total Kg/' + fator_arroba +'=' + total_arroba);

    }

    document.getElementById("fator_arroba_vivo").focus(); 
}

function editar_item_animal_morto() {
    row_index = $(this).parent().parent().index();

    var par = $(this).parent().parent(); //tr
    //var tdcategoria_morto = par.children("td:nth-child(1)");
    var tdqtd_morto = par.children("td:nth-child(1)");
    var tdsexo_morto = par.children("td:nth-child(2)");
    var tdpeso_morto = par.children("td:nth-child(3)");
    var tdpeso_ajustado_morto = par.children("td:nth-child(4)");
    var tdpeso_abate_morto = par.children("td:nth-child(5)");
    var tdarroba_abate_morto = par.children("td:nth-child(6)");
    var tdund_morto = par.children("td:nth-child(7)");
    var tdvalor_unit_morto = par.children("td:nth-child(8)");
    var tdvalor_total_morto = par.children("td:nth-child(9)");
    var tdrendimento_morto = par.children("td:nth-child(10)");
    var tdcategoria_morto_id = par.children("td:nth-child(11)");
    var tdund_morto_id = par.children("td:nth-child(12)");
    var tdconta_morto_id = par.children("td:nth-child(13)");

    $("#categoria_morto").val(tdcategoria_morto_id.html());
    $("#sexo_morto").val(tdsexo_morto.html());
    $("#qtd_morto").val(tdqtd_morto.html());
    $("#peso_categoria_morto").val(tdpeso_morto.html());
    $("#peso_categoria_ajustado_morto").val(tdpeso_ajustado_morto.html());
    $("#peso_abate_morto").val(tdpeso_abate_morto.html());
    $("#arroba_abate_morto").val(tdarroba_abate_morto.html());
    $("#unidade_morto").val(tdund_morto_id.html());
    $("#valor_unitario_morto").val(tdvalor_unit_morto.html());
   // $("#qtd_total_morto").val(tdtotal_und_morto.html());
    $("#total_morto").val(tdvalor_total_morto.html());
    $("#rendimento_morto").val(tdrendimento_morto.html());
    $("#conta_morto").val(tdconta_morto_id.html());
    $(".editar").show();
    $(".incluir").hide();
    $(".linha_escondida").show();

    var peso_kg = $("#peso_abate_morto").val();
    var total_arroba = peso_kg/30;
    total_arroba = total_arroba.toFixed(2);
    $("#arrobamortoHelpBlock").text('Peso Morto kg/30=' + formatMoney(total_arroba));

}

function editar_item_animal_cabeca() {
    row_index = $(this).parent().parent().index();

    var par = $(this).parent().parent(); //tr
    var tdcategoria_cabeca = par.children("td:nth-child(1)");
    var tdsexo_cabeca = par.children("td:nth-child(2)");
    var tdqtd_cabeca = par.children("td:nth-child(3)");
    var tdpeso_cabeca = par.children("td:nth-child(4)");
    var tdvalor_unit_cabeca = par.children("td:nth-child(5)");
    var tdvalor_total_cabeca = par.children("td:nth-child(6)");
    var tdarroba_cabeca = par.children("td:nth-child(7)");
    var tdcategoria_cabeca_id = par.children("td:nth-child(8)");
    var tdconta_cabeca = par.children("td:nth-child(9)");

    $("#categoria_cabeca").val(tdcategoria_cabeca_id.html());
    $("#sexo_cabeca").val(tdsexo_cabeca.html());
    $("#qtd_cabeca").val(tdqtd_cabeca.html());
    $("#valor_unitario_cabeca").val(tdvalor_unit_cabeca.html());
    $("#total_cabeca").val(tdvalor_total_cabeca.html());
    $("#arroba_cabeca").val(tdarroba_cabeca.html());
    $("#peso_cabeca").val(tdpeso_cabeca.html());
    $("#conta_cabeca").val(tdconta_cabeca.html());
    $(".editar").show();
    $(".incluir").hide();
    $(".linha_escondida").show();
}

function excuir_item_animal_vivo() {
    row_index = $(this).parent().parent().index();
    var par = $(this).parent().parent();
    var tdcategoria_cabeca = par.children("td:nth-child(1)");
    var tdsexo_cabeca = par.children("td:nth-child(2)");

    if (tdsexo_cabeca.html()=="M") {
        var desc_sexo = 'Macho';
    }
    else {
        var desc_sexo = 'Fêmea';
    }

    if (window.confirm("Confirma enviar esse registro para lixeira?" + " " + tdcategoria_cabeca.html() + ' - ' + desc_sexo)) {     
        par.remove();

        $("#categoria_vivo").val('000');
        $("#sexo_vivo").val('');
        $("#qtd_vivo").val('');
        $("#peso_categoria_vivo").val('');
        $("#arroba_categoria_vivo").val('');
        $("#fator_arroba_vivo").val('');
        $("#unidade_vivo").val('');
        $("#valor_unitario_vivo").val('');
        $("#total_vivo").val('');
        $(".editar").hide();
        $(".incluir").show();
        document.getElementById("categoria_vivo").focus(); 
        somar_total_geral_vivo();
    }
}

function excuir_item_animal_morto() {
    row_index = $(this).parent().parent().index();
    var par = $(this).parent().parent();

    var tdqtd_morto = par.children("td:nth-child(1)");
    var tdsexo_morto = par.children("td:nth-child(2)");

    if (tdsexo_morto.html()=="M") {
        var desc_sexo = 'Machos';
    }
    else {
        var desc_sexo = 'Fêmeas';
    }

    if (window.confirm("Confirma enviar esse registro para lixeira?" + " " + tdqtd_morto.html() + ' animais - ' + desc_sexo)) {     
        par.remove();
        $("#categoria_morto").val('000');
        $("#sexo_morto").val('');
        $("#qtd_morto").val('');
        $("#peso_categoria_morto").val('');
        $("#peso_categoria_ajustado_morto").val('');
        $("#peso_abate_morto").val('');
        $("#arroba_abate_morto").val('');
        $("#unidade_morto").val('');
        $("#valor_unitario_morto").val('');
        //$("#qtd_total_morto").val('');
        $("#total_morto").val('');
        $("#rendimento_morto").val('');
        $(".editar").hide();
        $(".incluir").show();
        somar_total_geral_morto();
    }
}

function excuir_item_animal_cabeca() {
    row_index = $(this).parent().parent().index();
    var par = $(this).parent().parent();
    var tdcategoria_cabeca = par.children("td:nth-child(1)");
    var tdsexo_cabeca = par.children("td:nth-child(2)");

    if (tdsexo_cabeca.html()=="M") {
        var desc_sexo = 'Macho';
    }
    else {
        var desc_sexo = 'Fêmea';
    }

    if (window.confirm("Confirma enviar esse registro para lixeira?" + " " + tdcategoria_cabeca.html() + ' - ' + desc_sexo)) {     
        par.remove();
        $("#categoria_cabeca").val('000');
        $("#sexo_cabeca").val('');
        $("#qtd_cabeca").val('');
        $("#valor_unitario_cabeca").val('');
        $("#total_cabeca").val('');
        $("#arroba_cabeca").val('');
        $("#peso_cabeca").val('');
        $(".editar").hide();
        $(".incluir").show();
        document.getElementById("categoria_cabeca").focus(); 
        somar_total_geral_cabeca();
    }
}

function gravar_venda() {
    var forma_pagto = $("#forma_pri").val();
    var conta_pagto = $("#conta_pri").val();
    var conta_contabil = $("#conta_contabil").val();
    var centro_custos = $("#centro_custos").val();
    var total_receber = $("#total_receber").val();
    var valor_pri = $("#valor_pri_parcela").val();
    var vencimento_pri = $("#vencimento_pri_parcela").val();

    if (total_receber=='' || total_receber=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Total a receber não pode ser zero!');
        return;
    }

    if (valor_pri=='' || valor_pri=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o valor da primeira parcela!');
        return;
    }

    if (vencimento_pri=='' || vencimento_pri==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Vencimento da primeira parcela!');
        return;
    }

    if (forma_pagto==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Forma de Pagamento da primeira parcela!');
        return;
    }

    if (conta_pagto==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Pagamento da primeira parcela!');
        return;
    }

    if (conta_contabil==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil!');
        return;
    }

    /*if (centro_custos==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Centro de Custos!');
        return;
    }*/

    if (verifica_virgula(total_receber)==',') {
        total_receber = replace_valor(total_receber);
    }

    if (verifica_virgula(valor_pri)==',') {
        valor_pri = replace_valor(valor_pri);
    }

    var total_parcelas = 0;

    $('#tabela_parcelas tbody tr').each(function(){
        var valor_parcela = $(this).find('.txparcela').html();

        if (valor_parcela!=undefined && valor_parcela!=''){
            if (verifica_virgula(valor_parcela)==',') {
               valor_parcela = replace_valor(valor_parcela);
            }

            total_parcelas+= parseFloat(valor_parcela);
        }
    });

    var restante_parcela = total_receber - valor_pri - total_parcelas;

    if (restante_parcela<0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Valor total das parcelas está maior que o total a receber!');
        return;
    }
    else if (restante_parcela>0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Valor total das parcelas está menor que o total a receber!');
        return;
    }

    var array_tabela_parcelas = new Array();
    var item_parcela = new Array();
    var grupo_parcelas = "";
 
    $('#tabela_parcelas tbody tr').each(function(){
        var prazo = $(this).find('.txtprazo').html();
        var valor = $(this).find('.txparcela').html();
        var forma = $(this).find('.txtcodigo_forma').html();
        var conta = $(this).find('.txtcodigo_conta').html();

        if (valor!=undefined && valor!=''){
            if (verifica_virgula(valor)==',') {
               valor = replace_valor(valor);
            }

            for (i = 0; i <= 4; i++) {
                item_parcela[i]=0;
            }

            item_parcela[0]=prazo;
            item_parcela[1]=valor;
            item_parcela[2]=forma;
            item_parcela[3]=conta;

            var tabela_itens=item_parcela.join("|");
            array_tabela_parcelas.push(tabela_itens);
            grupo_parcelas=array_tabela_parcelas.join("<|>");
        }
    });

    $("#array_parcelas").val(grupo_parcelas);

    var tipo_venda = $("input[name='tipo_venda']:checked").val();

    var array_tabela_itens = new Array();
    var item = new Array();
    var grupo_itens = "";
    var tem_valor = 'S';

    switch (tipo_venda) {
        case 'V':
            $('.tabela_itens_vivo tbody tr').each(function(){
                var categoria = $(this).find('.categoria_vivo_id').html();
                var sexo = $(this).find('.sexo_vivo').html();
                var qtd = $(this).find('.qtd_vivo').html();
                var peso = $(this).find('.peso_vivo').html();
                var arroba = $(this).find('.arroba_vivo').html();
                var und = $(this).find('.und_vivo_id').html();
                var vlr_unit = $(this).find('.valor_unit_vivo').html();
                var vlr_total = $(this).find('.valor_total_vivo').html();
                var conta_vivo = $(this).find('.conta_vivo_id').html();
                var fator_arroba_vivo = $(this).find('.fator_arroba_vivo').html();

                if (vlr_unit==''){
                    tem_valor="N";
                }

                if (categoria!=undefined && categoria!=''){
                    if (verifica_virgula(vlr_unit)==',') {
                       vlr_unit = replace_valor(vlr_unit);
                    }

                    if (verifica_virgula(vlr_total)==',') {
                       vlr_total = replace_valor(vlr_total);
                    }

                    if (verifica_virgula(peso)==',') {
                       peso = replace_valor(peso);
                    }

                    if (verifica_virgula(arroba)==',') {
                       arroba = replace_valor(arroba);
                    }

                    if (verifica_virgula(fator_arroba_vivo)==',') {
                       fator_arroba_vivo = replace_valor(fator_arroba_vivo);
                    }

                    for (i = 0; i <= 12; i++) {
                        item[i]=0;
                    }

                    item[0]=categoria;
                    item[1]=sexo;
                    item[2]=qtd;
                    item[3]=peso;
                    item[4]=0;
                    item[5]=und;
                    item[6]=vlr_unit;
                    item[7]=vlr_total;
                    item[8]=arroba;
                    item[9]=0;
                    item[10]=conta_vivo;
                    item[11]=0;
                    item[12]=fator_arroba_vivo;

                    var tabela_itens=item.join("|");
                    array_tabela_itens.push(tabela_itens);
                    grupo_itens=array_tabela_itens.join("<|>");
                }
            });
        break;
        case 'M':
            $('.tabela_itens_morto tbody tr').each(function(){
                //var categoria = $(this).find('.categoria_morto_id').html();
                var categoria = 0;
                var qtd = $(this).find('.qtd_morto').html();
                var sexo = $(this).find('.sexo_morto').html();
                var peso_vivo = $(this).find('.peso_morto').html();
                var peso_ajustado = $(this).find('.peso_ajustado_morto').html();
                var peso_morto = $(this).find('.peso_abate_morto').html();
                var arroba_morto = $(this).find('.arroba_abate_morto').html();
                var und = $(this).find('.und_morto_id').html();
                var vlr_unit = $(this).find('.valor_unit_morto').html();
                var vlr_total = $(this).find('.valor_total_morto').html();
                var rendimento = $(this).find('.rendimento_morto').html();
                var conta_morto = $(this).find('.conta_morto_id').html();

                if (vlr_unit==''){
                    tem_valor="N";
                }

                if (vlr_unit!=undefined && vlr_unit!=''){

                    if (verifica_virgula(vlr_unit)==',') {
                       vlr_unit = replace_valor(vlr_unit);
                    }

                    if (verifica_virgula(vlr_total)==',') {
                       vlr_total = replace_valor(vlr_total);
                    }

                    if (verifica_virgula(peso_vivo)==',') {
                       peso_vivo = replace_valor(peso_vivo);
                    }

                    if (verifica_virgula(peso_ajustado)==',') {
                       peso_ajustado = replace_valor(peso_ajustado);
                    }

                    if (verifica_virgula(peso_morto)==',') {
                       peso_morto = replace_valor(peso_morto);
                    }

                    if (verifica_virgula(arroba_morto)==',') {
                       arroba_morto = replace_valor(arroba_morto);
                    }

                    if (verifica_virgula(rendimento)==',') {
                       rendimento = replace_valor(rendimento);
                    }

                    for (i = 0; i <= 12; i++) {
                        item[i]=0;
                    }

                    item[0]=categoria;
                    item[1]=sexo;
                    item[2]=qtd;
                    item[3]=peso_vivo;
                    item[4]=peso_morto;
                    item[5]=und;
                    item[6]=vlr_unit;
                    item[7]=vlr_total;
                    item[8]=arroba_morto;
                    item[9]=rendimento;
                    item[10]=conta_morto;
                    item[11]=peso_ajustado;
                    item[12]=0;

                    var tabela_itens=item.join("|");
                    array_tabela_itens.push(tabela_itens);
                    grupo_itens=array_tabela_itens.join("<|>");
                }
            });
        break;
        case 'C':
            $('.tabela_itens_cabeca tbody tr').each(function(){
                var categoria = $(this).find('.categoria_cabeca_id').html();
                var sexo = $(this).find('.sexo_cabeca').html();
                var qtd = $(this).find('.qtd_cabeca').html();
                var peso = $(this).find('.peso_cabeca').html();
                var vlr_unit = $(this).find('.valor_unit_cabeca').html();
                var vlr_total = $(this).find('.valor_total_cabeca').html();
                var arroba = $(this).find('.arroba_cabeca').html();
                var conta_cabeca = $(this).find('.conta_cabeca_id').html();

                if (peso=='' || peso==undefined) {
                    peso = '0,00';
                }

                if (vlr_unit==''){
                    tem_valor="N";
                }

                if (categoria!=undefined && categoria!=''){
                    if (verifica_virgula(vlr_unit)==',') {
                       vlr_unit = replace_valor(vlr_unit);
                    }

                    if (verifica_virgula(vlr_total)==',') {
                       vlr_total = replace_valor(vlr_total);
                    }

                    if (verifica_virgula(arroba)==',') {
                       arroba = replace_valor(arroba);
                    }

                    if (verifica_virgula(peso)==',') {
                       peso = replace_valor(peso);
                    }

                    for (i = 0; i <= 12; i++) {
                        item[i]=0;
                    }

                    item[0]=categoria;
                    item[1]=sexo;
                    item[2]=qtd;
                    item[3]=peso;
                    item[4]=0;
                    item[5]=0;
                    item[6]=vlr_unit;
                    item[7]=vlr_total;
                    item[8]=arroba;
                    item[9]=0;
                    item[10]=conta_cabeca;
                    item[11]=0;
                    item[12]=0;

                    var tabela_itens=item.join("|");
                    array_tabela_itens.push(tabela_itens);
                    grupo_itens=array_tabela_itens.join("<|>");
                }
            });
        break;
    }

    if (tem_valor=='N'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Existe item sem o valor unitário informado!');
        return;
    }

    $("#array_itens").val(grupo_itens);

    var dados = $('#form_gravar_venda').serialize();

    $(".gravar_venda").attr("disabled", true);

    $.ajax({
        type: "POST",
        url: 'gravar_venda.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $(".gravar_venda").attr("disabled", false);
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $(".gravar_venda").attr("disabled", false);
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        }
    });
}

function digita_valor(){
    $('#total_vivo').bind('keypress',mask.money);
    $('#valor_unitario_morto').bind('keypress',mask.money);
    $('#total_morto').bind('keypress',mask.money);
    $('#peso_cabeca').bind('keypress',mask.money);
    $('#valor_unitario_cabeca').bind('keypress',mask.money);
    $('#total_cabeca').bind('keypress',mask.money);
    $('#desconto_final').bind('keypress',mask.money);
    $('#valor_pri_parcela').bind('keypress',mask.money);
    $('#valor_parcela').bind('keypress',mask.money);
    $('#peso_categoria_vivo').bind('keypress',mask.money);
    //$('#arroba_categoria_vivo').bind('keypress',mask.money);
    $('#peso_categoria_morto').bind('keypress',mask.money);
    $('#peso_categoria_ajustado_morto').bind('keypress',mask.money);
    $('#peso_abate_morto').bind('keypress',mask.money);
    $('#arroba_abate_morto').bind('keypress',mask.money);
}

function digita_valor_vivo(){
    $('#valor_unitario_vivo').bind('keypress',mask.money);
    //document.getElementById("conta_vivo").focus();
}

function exibe_valor_unitario_vivo(){
    var valor_unitario_vivo = $("#valor_unitario_vivo").val();
    if (verifica_virgula(valor_unitario_vivo)==',') {
        valor_unitario_vivo = replace_valor(valor_unitario_vivo);
    }
    $("#valor_unitario_vivo").val(formatMoney(valor_unitario_vivo));
}

function exibe_total_vivo(){
    var total_vivo = $("#total_vivo").val();
    if (verifica_virgula(total_vivo)==',') {
        total_vivo = replace_valor(total_vivo);
    }
    $("#total_vivo").val(formatMoney(total_vivo));
}

function exibe_valor_unitario_morto(){
    var valor_unitario_morto = $("#valor_unitario_morto").val();
    if (verifica_virgula(valor_unitario_morto)==',') {
        valor_unitario_morto = replace_valor(valor_unitario_morto);
    }
    $("#valor_unitario_morto").val(formatMoney(valor_unitario_morto));
}

function exibe_valor_total_morto(){
    var total_morto = $("#total_morto").val();
    if (verifica_virgula(total_morto)==',') {
        total_morto = replace_valor(total_morto);
    }
    $("#total_morto").val(formatMoney(total_morto));
}

function exibe_valor_unitario_cabeca(){
    var valor_unitario_cabeca = $("#valor_unitario_cabeca").val();
    if (verifica_virgula(valor_unitario_cabeca)==',') {
        valor_unitario_cabeca = replace_valor(valor_unitario_cabeca);
    }
    $("#valor_unitario_cabeca").val(formatMoney(valor_unitario_cabeca));
}

function exibe_peso_cabeca(){
    var peso_cabeca = $("#peso_cabeca").val();
    if (verifica_virgula(peso_cabeca)==',') {
        peso_cabeca = replace_valor(peso_cabeca);
    }
    $("#peso_cabeca").val(formatMoney(peso_cabeca));
}

function exibe_total_cabeca(){
    var total_cabeca = $("#total_cabeca").val();
    if (verifica_virgula(total_cabeca)==',') {
        total_cabeca = replace_valor(total_cabeca);
    }
    $("#total_cabeca").val(formatMoney(total_cabeca));
}

function exibe_peso_categoria_vivo(){
    var peso_categoria_vivo = $("#peso_categoria_vivo").val();

    if (verifica_virgula(peso_categoria_vivo)==',') {
        peso_categoria_vivo = replace_valor(peso_categoria_vivo);
    }

    $("#peso_categoria_vivo").val(formatMoney(peso_categoria_vivo));
}

/*function exibe_arroba_categoria_vivo(){
    var arroba_categoria_vivo = $("#arroba_categoria_vivo").val();
    if (verifica_virgula(arroba_categoria_vivo)==',') {
        arroba_categoria_vivo = replace_valor(arroba_categoria_vivo);
    }
    $("#arroba_categoria_vivo").val(formatMoney(arroba_categoria_vivo));
}*/

function exibe_peso_categoria_morto(){
    var peso_categoria_morto = $("#peso_categoria_morto").val();
    if (verifica_virgula(peso_categoria_morto)==',') {
        peso_categoria_morto = replace_valor(peso_categoria_morto);
    }
    $("#peso_categoria_morto").val(formatMoney(peso_categoria_morto));
}

function exibe_peso_categoria_ajustado_morto(){
    var peso_categoria_ajustado_morto = $("#peso_categoria_ajustado_morto").val();
    if (verifica_virgula(peso_categoria_ajustado_morto)==',') {
        peso_categoria_ajustado_morto = replace_valor(peso_categoria_ajustado_morto);
    }
    $("#peso_categoria_ajustado_morto").val(formatMoney(peso_categoria_ajustado_morto));
}

function exibe_peso_abate_morto(){
    var peso_abate_morto = $("#peso_abate_morto").val();
    if (verifica_virgula(peso_abate_morto)==',') {
        peso_abate_morto = replace_valor(peso_abate_morto);
    }
    $("#peso_abate_morto").val(formatMoney(peso_abate_morto));
}

function exibe_arroba_abate_morto(){
    var arroba_abate_morto = $("#arroba_abate_morto").val();
    if (verifica_virgula(arroba_abate_morto)==',') {
        arroba_abate_morto = replace_valor(arroba_abate_morto);
    }
    $("#arroba_abate_morto").val(formatMoney(arroba_abate_morto));
}

function exibe_fator_arroba(){
    var fator_arroba_vivo = $("#fator_arroba_vivo").val();
    if (verifica_virgula(fator_arroba_vivo)==',') {
        fator_arroba_vivo = replace_valor(fator_arroba_vivo);
    }
    $("#fator_arroba_vivo").val(formatMoney6(fator_arroba_vivo));

    document.getElementById("unidade_vivo").focus();    
    soma_total_item_vivo();
}

function exibe_desconto_final(){
    var desconto_final = $("#desconto_final").val();
    if (verifica_virgula(desconto_final)==',') {
        desconto_final = replace_valor(desconto_final);
    }
    $("#desconto_final").val(formatMoney(desconto_final));

    var total_venda = $("#total_venda").val();
    if (verifica_virgula(total_venda)==',') {
        total_venda = replace_valor(total_venda);
    }

    if (desconto_final!=0) {
        var total_receber = total_venda - desconto_final;
        $("#total_receber").val(formatMoney(total_receber));
    }
    else {
        $("#total_receber").val(formatMoney(total_venda));
    }
}

function exibe_valor_pri_parcela(){
    var valor_pri_parcela = $("#valor_pri_parcela").val();
    if (verifica_virgula(valor_pri_parcela)==',') {
        valor_pri_parcela = replace_valor(valor_pri_parcela);
    }
    $("#valor_pri_parcela").val(formatMoney(valor_pri_parcela));
}

function exibe_valor_parcela(){
    var valor_parcela = $("#valor_parcela").val();
    if (verifica_virgula(valor_parcela)==',') {
        valor_parcela = replace_valor(valor_parcela);
    }
    $("#valor_parcela").val(formatMoney(valor_parcela));
}

function soma_total_item_vivo() {
    var peso_kg = $("#peso_categoria_vivo").val();
    var arroba_kg = $("#arroba_categoria_vivo").val();
    var und_negociada = $("#unidade_vivo").val();
    var valor_unitario = $("#valor_unitario_vivo").val();
    var fator_arroba = $("#fator_arroba_vivo").val();

    if (verifica_virgula(valor_unitario)==',') {
        valor_unitario = replace_valor(valor_unitario);
    }

    if (verifica_virgula(peso_kg)==',') {
        peso_kg = replace_valor(peso_kg);
    }

    if (verifica_virgula(arroba_kg)==',') {
        arroba_kg = replace_valor(arroba_kg);
    }

    if (verifica_virgula(fator_arroba)==',') {
        fator_arroba = replace_valor(fator_arroba);
    }

    if (fator_arroba == 0) {
        var fator_arroba = 0.031777;
        $("#fator_arroba_vivo").val(fator_arroba);
        var total_arroba = peso_kg*fator_arroba;
        total_arroba = ((total_arroba + Number.EPSILON) * 100) / 100;
        total_arroba = total_arroba.toFixed(6);
        total_arroba = stripZeros(total_arroba);

        $("#arrobaHelpBlock").text('Peso Total Kg*'+fator_arroba+'=' + total_arroba);
        $("#arroba_categoria_vivo").val(total_arroba);
        arroba_kg = total_arroba;
    }
    else {
        var total_arroba = peso_kg*fator_arroba;
        total_arroba = ((total_arroba + Number.EPSILON) * 100) / 100;
        total_arroba = total_arroba.toFixed(6);
        total_arroba = stripZeros(total_arroba);
        $("#arrobaHelpBlock").text('Peso Total Kg*'+fator_arroba+'='+total_arroba);
        $("#arroba_categoria_vivo").val(total_arroba);
        arroba_kg = total_arroba;
    }

    switch (und_negociada) {
    case '1':
        var valor_total = valor_unitario * arroba_kg;
        $("#total_vivo").val(formatMoney(valor_total));
        break;
    case '2':
        var valor_total = valor_unitario * peso_kg;
        $("#total_vivo").val(formatMoney(valor_total));
        break;
    } 

}

function stripZeros(str) {
  return str.replace(/(^0+(?=\d))|(,?0+$)/g, '');
}

function round(num, decimalPlaces = 0) {
    if (num < 0)
        return -round(-num, decimalPlaces);
    var p = Math.pow(10, decimalPlaces);
    var n = num * p;
    var f = n - Math.floor(n);
    var e = Number.EPSILON * n;

    // Determine whether this fraction is a midpoint value.
    return (f >= .5 - e) ? Math.ceil(n) / p : Math.floor(n) / p;
}

function soma_total_item_morto() {
    var peso_vivo = $("#peso_categoria_ajustado_morto").val();
    var peso_kg = $("#peso_abate_morto").val();
    var und_negociada = $("#unidade_morto").val();
    var valor_unitario = $("#valor_unitario_morto").val();

    if (verifica_virgula(valor_unitario)==',') {
        valor_unitario = replace_valor(valor_unitario);
    }

    if (verifica_virgula(peso_vivo)==',') {
        peso_vivo = replace_valor(peso_vivo);
    }

    if (verifica_virgula(peso_kg)==',') {
        peso_kg = replace_valor(peso_kg);
    }

    var total_arroba = peso_kg/15;
    total_arroba = Math.round(total_arroba);
    $("#arrobamortoHelpBlock").text('Peso Morto Kg/15=' + total_arroba);

    if (peso_vivo!=0) {
        var redimento_medio = (peso_kg / peso_vivo)*100;
        $("#rendimento_morto").val(formatMoney(redimento_medio));
    }
    else {
        $("#rendimento_morto").val('');
    }

    switch (und_negociada) {
    case '1':
       // var total_arroba = peso_kg/15;
       // total_arroba = total_arroba.toFixed(2);
       // $("#qtd_total_morto").val(formatMoney(total_arroba));
       // var valor_total = valor_unitario * total_arroba;
       // $("#total_morto").val(formatMoney(valor_total));
        break;
    case '2':
       // $("#qtd_total_morto").val(peso_kg);
       // var valor_total = valor_unitario * peso_kg;
       // $("#total_morto").val(formatMoney(valor_total));
        break;
    } 
}

function soma_total_item_cabeca() {
    var qtd_cabeca = parseFloat($("#qtd_cabeca").val());
    var valor_unitario = $("#valor_unitario_cabeca").val();
    var peso = $("#peso_cabeca").val();

    if (verifica_virgula(valor_unitario)==',') {
        valor_unitario = replace_valor(valor_unitario);
    }

    var valor_total = valor_unitario * qtd_cabeca;
    $("#total_cabeca").val(formatMoney(valor_total));

    if (peso!=0) {
        if (verifica_virgula(peso)==',') {
            peso = replace_valor(peso);
        }

        var peso_arroba = peso/30;
        var valor_aproximado_arroba = valor_total / peso_arroba;
        valor_aproximado_arroba = valor_aproximado_arroba.toFixed(2);

        //var total_arroba = (valor_total/30);
        //total_arroba = total_arroba/qtd_cabeca;
        //total_arroba = total_arroba.toFixed(2);

        $("#arroba_cabeca").val(formatMoney(valor_aproximado_arroba));
    }
    else {
        var valor_aproximado_arroba=0;
        $("#arroba_cabeca").val(formatMoney(valor_aproximado_arroba));
    }
}

function somar_total_geral_vivo(){

    total_geral= 0;
    tem_itens = 'N';
    total_macho = 0;
    total_femea = 0;
    conta_macho = 0;
    conta_femea = 0;

    $('.tabela_itens_vivo tbody tr').each(function(){
        var id_categoria = $(this).find('.categoria_vivo_id').html();
        var valor_total_item = $(this).find('.valor_total_vivo').html();

        var sexo = $(this).find('.sexo_vivo').html();
        var conta_contabil = $(this).find('.conta_vivo_id').html();

        if (id_categoria!=undefined && valor_total_item!=''){
            var total_item = $(this).find('.valor_total_vivo').html();
            if (verifica_virgula(total_item)==',') {
               total_item = replace_valor(total_item);
            }

            total_geral+= parseFloat(total_item);
            tem_itens='S';

            if (sexo == "M") {
                total_macho+=parseFloat(total_item);
                conta_macho = conta_contabil;
            }
            else {
                total_femea+=parseFloat(total_item);
                conta_femea = conta_contabil;
            }
        }
    });

    if (total_macho > total_femea) {
        $("#conta_contabil").val(conta_macho);
    }
    else {
        $("#conta_contabil").val(conta_femea);
    }

    $("#tem_itens").val(tem_itens);
    $("#total_venda").val(formatMoney(total_geral));
    $("#total_receber").val(formatMoney(total_geral));
}

function somar_total_geral_morto(){
    total_geral= 0;
    tem_itens = 'N';
    total_macho = 0;
    total_femea = 0;
    conta_macho = 0;
    conta_femea = 0;

    $('.tabela_itens_morto tbody tr').each(function(){
        var id_categoria = $(this).find('.categoria_morto_id').html();
        var valor_total_item = $(this).find('.valor_total_morto').html();

        var sexo = $(this).find('.sexo_morto').html();
        var conta_contabil = $(this).find('.conta_morto_id').html();

        if (id_categoria!=undefined && valor_total_item!=''){
            var total_item = $(this).find('.valor_total_morto').html();
            if (verifica_virgula(total_item)==',') {
               total_item = replace_valor(total_item);
            }

            total_geral+= parseFloat(total_item);
            tem_itens='S';

            if (sexo == "M") {
                total_macho+=parseFloat(total_item);
                conta_macho = conta_contabil;
            }
            else {
                total_femea+=parseFloat(total_item);
                conta_femea = conta_contabil;
            }
        }
    });

    if (total_macho > total_femea) {
        $("#conta_contabil").val(conta_macho);
    }
    else {
        $("#conta_contabil").val(conta_femea);
    }

    $("#tem_itens").val(tem_itens);
    $("#total_venda").val(formatMoney(total_geral));
    $("#total_receber").val(formatMoney(total_geral));
}

function somar_total_geral_cabeca(){
    total_geral= 0;
    tem_itens = 'N';
    total_macho = 0;
    total_femea = 0;
    conta_macho = 0;
    conta_femea = 0;

    $('.tabela_itens_cabeca tbody tr').each(function(){
        var id_categoria = $(this).find('.categoria_cabeca_id').html();
        var valor_total_item = $(this).find('.valor_total_cabeca').html();

        var sexo = $(this).find('.sexo_cabeca').html();
        var conta_contabil = $(this).find('.conta_cabeca_id').html();

        if (id_categoria!=undefined && valor_total_item!=''){
            var total_item = $(this).find('.valor_total_cabeca').html();
            if (verifica_virgula(total_item)==',') {
               total_item = replace_valor(total_item);
            }

            total_geral+= parseFloat(total_item);
            tem_itens='S';

            if (sexo == "M") {
                total_macho+=parseFloat(total_item);
                conta_macho = conta_contabil;
            }
            else {
                total_femea+=parseFloat(total_item);
                conta_femea = conta_contabil;
            }
        }
    });

    if (total_macho > total_femea) {
        $("#conta_contabil").val(conta_macho);
    }
    else {
        $("#conta_contabil").val(conta_femea);
    }

    $("#tem_itens").val(tem_itens);
    $("#total_venda").val(formatMoney(total_geral));
    $("#total_receber").val(formatMoney(total_geral));
}


function modal_inserir_parcela() {
    var forma_pagto = $("#forma_pri").val();
    var conta_pagto = $("#conta_pri").val();
    var total_receber = $("#total_receber").val();
    var valor_pri = $("#valor_pri_parcela").val();
    var vencimento_pri = $("#vencimento_pri_parcela").val();

    if (valor_pri=='' || valor_pri=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Valor da primeira parcela!');
        return;
    }

    if (vencimento_pri=='' || vencimento_pri==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Vencimento da primeira parcela!');
        return;
    }

    if (forma_pagto==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Forma de Pagamento da primeira parcela!');
        return;
    }

    if (conta_pagto==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Pagamento da primeira parcela!');
        return;
    }

    if (verifica_virgula(total_receber)==',') {
        total_receber = replace_valor(total_receber);
    }

    if (verifica_virgula(valor_pri)==',') {
        valor_pri = replace_valor(valor_pri);
    }

    var total_parcelas = 0;

    $('#tabela_parcelas tbody tr').each(function(){
        var valor_parcela = $(this).find('.txparcela').html();

        if (valor_parcela!=undefined && valor_parcela!=''){
            if (verifica_virgula(valor_parcela)==',') {
               valor_parcela = replace_valor(valor_parcela);
            }

            total_parcelas+= parseFloat(valor_parcela);
        }
    });

    var restante_parcela = total_receber - valor_pri - total_parcelas;

    if (restante_parcela==0) {
        $('.modal_inserir_parcela').modal('hide');
        return;
    }

    $("#prazo").val('');
    $('#valor_parcela').val(formatMoney(restante_parcela));
    $("#forma_parcela").val(forma_pagto);
    $("#conta_parcela").val(conta_pagto);

    $('.modal_inserir_parcela .modal-title').html('Inserir Parcela');
    $('.modal_inserir_parcela').modal('show');
    $(".alert_erro_parcela").hide(); 
    $("#inserir").show();     
    $("#editar").hide();
}

function modal_editar_parcela() {
    row_index = $(this).parent().parent().index();

    var par = $(this).parent().parent(); //tr
    var tdPrazo = par.children("td:nth-child(1)");
    var tdValor = par.children("td:nth-child(2)");
    var tdForma = par.children("td:nth-child(5)");
    var tdConta = par.children("td:nth-child(6)");

    $("#prazo").val(tdPrazo.html());
    $("#valor_parcela").val(tdValor.html());
    $("#forma_parcela").val(tdForma.html());
    $("#conta_parcela").val(tdConta.html());

    $('.modal_inserir_parcela .modal-title').html('Editar Parcela');
    $('.modal_inserir_parcela').modal('show');
    $(".alert_erro_parcela").hide();    
    $("#inserir").hide();     
    $("#editar").show();
}

function modal_excluir_parcela() {
    var par = $(this).parent().parent(); //tr
    par.remove();
}

function salvar(){

    if ( $("#prazo").val()==''){
        $(".alert_erro_parcela .negrito").html('');
        $(".alert_erro_parcela span").html('Informe Prazo!');
        $(".alert_erro_parcela").show();
        return;
    }

    if ($("#valor_parcela").val()=='' || $("#valor_parcela").val()=='0,00'){
        $(".alert_erro_parcela .negrito").html('');
        $(".alert_erro_parcela span").html('Informe o Valor da Parcela!');
        $(".alert_erro_parcela").show();
        return;
    }

    if ($("#forma_parcela").val()==0){
        $(".alert_erro_parcela .negrito").html('');
        $(".alert_erro_parcela span").html('Informe a Forma de Pagamento!');
        $(".alert_erro_parcela").show();
        return;
    }

    var itemSelecionado = $("#forma_parcela option:selected");
    var desc_forma = itemSelecionado.text().trim();

    if ($("#conta_parcela").val()==0){
        $(".alert_erro_parcela .negrito").html('');
        $(".alert_erro_parcela span").html('Informe a Conta de Pagamento!');
        $(".alert_erro_parcela").show();
        return;
    }

    var itemSelecionado = $("#conta_parcela option:selected");
    var desc_conta = itemSelecionado.text().trim();

    $("#tabela_parcelas tbody").append(
        "<tr>"+
        "<td width='15%' class='txtprazo'>" + $("#prazo").val() + "</td>"+
        "<td width='15%' class='txparcela' align='right'>" + $("#valor_parcela").val() + "</td>"+
        "<td width='25%' class='txtforma'>" + desc_forma + "</td>"+
        "<td width='25%' class='txtconta'>" + desc_conta + "</td>"+
        "<td hidden='' class='txtcodigo_forma'>" + $("#forma_parcela").val() + "</td>"+
        "<td hidden='' class='txtcodigo_conta'>" + $("#conta_parcela").val() + "</td>"+
        "<td width='20%'><div class='btn-group btnEditarPar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluirPar'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>"+
        "</tr>");
    $(".btnEditarPar").bind("click", modal_editar_parcela);
    $(".btnExcluirPar").bind("click", modal_excluir_parcela);

    var total_receber = $("#total_receber").val();
    var valor_pri = $("#valor_pri_parcela").val();

    if (verifica_virgula(total_receber)==',') {
        total_receber = replace_valor(total_receber);
    }

    if (verifica_virgula(valor_pri)==',') {
        valor_pri = replace_valor(valor_pri);
    }

    var total_parcelas = 0;

    $('#tabela_parcelas tbody tr').each(function(){
        var valor_parcela = $(this).find('.txparcela').html();

        if (valor_parcela!=undefined && valor_parcela!=''){
            if (verifica_virgula(valor_parcela)==',') {
               valor_parcela = replace_valor(valor_parcela);
            }

            total_parcelas+= parseFloat(valor_parcela);
        }
    });

    var restante_parcela = total_receber - valor_pri - total_parcelas;

    if (restante_parcela==0) {
        $(".btnEditarPar").bind("click", modal_editar_parcela);
        $(".btnExcluirPar").bind("click", modal_excluir_parcela);
        $('.modal_inserir_parcela').modal('hide');
        return;
    }
    else if (restante_parcela<0){
        $(".alert_erro_parcela .negrito").html('');
        $(".alert_erro_parcela span").html('Valor total das parcelas está maior que o total a receber!');
        $(".alert_erro_parcela").show();
        return;
    }
    else {
        modal_inserir_parcela();
        return;
    }
};

function salvar_edicao() {
    if ( $("#prazo").val()==''){
        $(".alert_erro_parcela .negrito").html('');
        $(".alert_erro_parcela span").html('Informe Prazo!');
        $(".alert_erro_parcela").show();
        return;
    }

    if ($("#valor_parcela").val()=='' || $("#valor_parcela").val()=='0,00'){
        $(".alert_erro_parcela .negrito").html('');
        $(".alert_erro_parcela span").html('Informe o Valor da Parcela!');
        $(".alert_erro_parcela").show();
        return;
    }

    if ($("#forma_parcela").val()==0){
        $(".alert_erro_parcela .negrito").html('');
        $(".alert_erro_parcela span").html('Informe a Forma de Pagamento!');
        $(".alert_erro_parcela").show();
        return;
    }

    var itemSelecionado = $("#forma_parcela option:selected");
    var desc_forma = itemSelecionado.text().trim();

    if ($("#conta_parcela").val()==0){
        $(".alert_erro_parcela .negrito").html('');
        $(".alert_erro_parcela span").html('Informe a Conta de Pagamento!');
        $(".alert_erro_parcela").show();
        return;
    }

    var itemSelecionado = $("#conta_parcela option:selected");
    var desc_conta = itemSelecionado.text().trim();

    $('#tabela_parcelas tbody tr').each(function(){
        row_index_salvar = $(this).index();

        if (row_index_salvar==row_index){
            $(this).find('.txtprazo').html($("#prazo").val());
            $(this).find('.txparcela').html($("#valor_parcela").val());
            $(this).find('.txtforma').html(desc_forma);
            $(this).find('.txtconta').html(desc_conta);
            $(this).find('.txtcodigo_forma').html($("#forma_parcela").val());
            $(this).find('.txtcodigo_conta').html($("#conta_parcela").val());
        }
    });

    var total_receber = $("#total_receber").val();
    var valor_pri = $("#valor_pri_parcela").val();

    if (verifica_virgula(total_receber)==',') {
        total_receber = replace_valor(total_receber);
    }

    if (verifica_virgula(valor_pri)==',') {
        valor_pri = replace_valor(valor_pri);
    }

    var total_parcelas = 0;

    $('#tabela_parcelas tbody tr').each(function(){
        var valor_parcela = $(this).find('.txparcela').html();

        if (valor_parcela!=undefined && valor_parcela!=''){
            if (verifica_virgula(valor_parcela)==',') {
               valor_parcela = replace_valor(valor_parcela);
            }

            total_parcelas+= parseFloat(valor_parcela);
        }
    });

    var restante_parcela = total_receber - valor_pri - total_parcelas;

    if (restante_parcela==0) {
        $(".btnEditarPar").bind("click", modal_editar_parcela);
        $(".btnExcluirPar").bind("click", modal_excluir_parcela);
        $('.modal_inserir_parcela').modal('hide');
        return;
    }
    else if (restante_parcela<0){
        $(".alert_erro_parcela .negrito").html('');
        $(".alert_erro_parcela span").html('Valor total das parcelas está maior que o total a receber!');
        $(".alert_erro_parcela").show();
        return;
    }
    else {
        modal_inserir_parcela();
        return;
    }
}

function excluir_venda($numero_venda){
    if (window.confirm("Confirma excluir esse registro?")) {     

        $.post("excluir_venda_animais.php",{id: $numero_venda}, function(valor){
         
            var php = valor.split("<|>");

            if (php[0]==9){
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(php[1]);
                return;
            }
            else if (php[0]==0){
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(php[1]);
                return;
            }
        });
    }
}
// FIM VENDAS

// COMPRAS
$(document).ready(function(){
    $(".exibe_tela_dados_compra").click(function(){
        var tem_itens = $("#tem_itens").val();

        if (tem_itens=="S") {
            if (window.confirm("Existe item digitado! Deseja realmente sair sem salvar?")) {     
                location.href= "form_compra_animais_incluir.php";
            }
        }
        else {
            location.href= "form_compra_animais_incluir.php";
        }
    });

    $('.tem_movimentacao_compra').click(function(event) {
        $(".tipo_compra").prop("checked", false);
        $(".local_comprador_compra").hide();
        $("#local_origem").val('000000000');
        $("#local_destino").val('000000000');

        var tem_movimentacao_compra = $("input[name='tem_movimentacao_compra']:checked").val();

        switch (tem_movimentacao_compra) {
        case 'S':
            $(".lista_movimentacao_compra").show();
            $.post("lista_movimentacao.php", {tipo:4}, function(valor){
                $("select[name=lista_movimentacao_compra]").html(valor);
            });
            break;
        case 'N':
            $(".lista_movimentacao_compra").hide();
            $(".local_comprador_compra").show();
            break;
        } 
    });

   $('#lista_movimentacao_compra').change(function(event) {
        $(".local_comprador_compra").show();

        var num_doc = $("#lista_movimentacao_compra").val();
        $.post("ler_movimentacao_venda.php", {num_doc:num_doc}, function(valor){

            if (valor==0) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Não exitem dados para essa movimentação.');
                return;
            }

            var php = valor.split("<|>");
            $("#local_origem").val(php[0]);
            $("#local_destino").val(php[1]);
        });
   });

    $('.tipo_compra').click(function(event) {
        var local_origem = $("#local_origem").val();
        var local_destino = $("#local_destino").val();

        if (local_origem==0 || local_destino==0){
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe o Local Origem e o Local Destino');
            $(".tipo_compra").prop("checked", false)
            return;
        }

        var data = $("#data_compra").val();

        dia  = data.substring(8, 10);
        mes  = data.substring(5, 7);
        ano  = data.substring(0, 4);

        $(".data_compra").text('Data: ' + dia+"/"+mes+"/"+ano);

        var itemSelecionado = $("#local_origem option:selected");
        var desc_local_origem = itemSelecionado.text().trim();
        $(".local_origem").text('Local Origem: ' + desc_local_origem);
        
        var itemSelecionado = $("#local_destino option:selected");
        var desc_local_destino = itemSelecionado.text().trim();
        $(".local_destino").text('Local Destino: ' + desc_local_destino);

        $(".tela_peso_vivo").hide();
        $(".tela_cabeca").hide();

        var tem_movimentacao_compra = $("input[name='tem_movimentacao_compra']:checked").val();
        var tipo_compra = $("input[name='tipo_compra']:checked").val();

        switch (tipo_compra) {
            case 'V':
                $(".tipo_compra").text('Compra: Peso Vivo');
                if (tem_movimentacao_compra=="S") {
                    var exibe_itens = exibe_itens_movimentacao_compra();
                }
                else {
                    //$('#modal_peso_vivo').modal('show');
                    $(".tela_dados").hide();
                    $(".linha_escondida").show();
                    $(".tela_peso_vivo").show();
                    $(".editar").hide();
                    $(".incluir").show();
                }
                break;
            case 'C':   
                $(".tipo_compra").text('Compra: Cabeça');
                if (tem_movimentacao_compra=="S") {
                    var exibe_itens = exibe_itens_movimentacao_compra();
                }
                else {
                    $(".tela_dados").hide();
                    $(".linha_escondida").show();
                    $(".tela_cabeca").show();
                    $(".editar").hide();
                    $(".incluir").show();
                }
                break;
        } 
    });

    $('#data_compra').change(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const data_compra = $("#data_compra").val();

        if (data_compra>data_atual) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('A Data não pode ser maior que a data atual!');
            $("#data_compra").val(data_atual);
            document.getElementById("data_compra").style.borderColor = "#0076d7";
        }
    });    

    $('#data_compra').blur(function(){
        const data_compra = $("#data_compra").val();

        if (data_compra=='') {
            const date = new Date();
            const data_atual = date.getFullYear()+'-'+
                              (date.getMonth()+1).AddZero()+'-'+
                              date.getDate().AddZero();

            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('A Data da Compra precisa ser informada!');
            $("#data_compra").val(data_atual);
            document.getElementById("data_compra").style.borderColor = "#0076d7";
        }
    });    
});

function exibe_itens_movimentacao_compra() {
    var num_doc = $("#lista_movimentacao_compra").val();
    $.post("ler_itens_movimentacao_compra.php", {num_doc:num_doc}, function(valor){

        if (valor==0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Não exitem dados para essa movimentação.');
            return;
        }

        var php = valor.split("<|>");
        var id_cat = php[0].split("|");
        var desc_cat = php[1].split("|");
        var total_cat = php[2].split("|");
        var total_m = php[3].split("|");
        var total_f = php[4].split("|");
        var peso_m = php[5].split("|");
        var peso_f = php[6].split("|");
        var numero_itens = desc_cat.length;

        var tipo_compra = $("input[name='tipo_compra']:checked").val();

        switch (tipo_compra) {
            case 'V':
                html = "";
                html += '<table class="table table-advance responsive-table tabela_itens_vivo" id="tabela_itens_vivo" width="100%" style="font-size: 13px;"';
                html += '<thead>';
                html += '<tr>';
                html += '<th>' + 'Categoria' + '</th>';
                html += '<th style="text-align: center;">' + 'Sexo' + '</th>';
                html += '<th style="text-align: right;">' + 'Nº Cabeças' + '</th>';
                html += '<th style="text-align: right;">' + 'Peso Total kg' + '</th>';
                html += '<th style="text-align: right;">' + 'Peso @' + '</th>';
                html += '<th style="text-align: center;">' + 'Und Negociada' + '</th>';
                html += '<th style="text-align: right;">' + 'Valor Unitário' + '</th>';
                html += '<th style="text-align: right;">' + 'Valor Total' + '</th>';
                html += '<th>' + '<i class="icon_cogs"></i> Ações' + '</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                for (var i = 0; i < numero_itens; i++) {
                    var codigo_id = id_cat[i];
                    var descricao = desc_cat[i];
                    var total = total_cat[i];
                    var macho = total_m[i];
                    var femea = total_f[i];
                    var peso_macho = formatMoney(peso_m[i]);
                    var peso_femea = formatMoney(peso_f[i]);

                    if (descricao!='' && macho!=0) {
                        html += '<tr>';
                        html += '<td width="15%" class="categoria_vivo">' + descricao + '</td>';
                        html += '<td width="5%" class="sexo_vivo" style="text-align: center;">' + "M" + '</td>';
                        html += '<td width="10%" class="qtd_vivo" style="text-align: right;">' + macho + '</td>';
                        html += '<td width="10%" class="peso_vivo" style="text-align: right;">' + peso_macho + '</td>';
                        html += '<td width="10%" class="arroba_vivo" style="text-align: right;">' + '' + '</td>';
                        html += '<td width="10%" class="und_vivo"  style="text-align: center;">' + '' + '</td>';
                        html += '<td width="10%" class="valor_unit_vivo"  style="text-align: right;">' + '' + '</td>';
                        html += '<td width="10%" class="valor_total_vivo"  style="text-align: right;">' + '' + '</td>';
                        html += "<td hidden='' class='categoria_vivo_id'>" + codigo_id + "</td>";
                        html += "<td hidden='' class='und_vivo_id'>" + 0 + "</td>";
                        html += "<td hidden='' class='conta_vivo_id'>" + 0 + "</td>";
                        html += "<td hidden='' class='fator_arroba_vivo'>" + 0 + "</td>";
                        html += "<td width='10%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>";
                        html += '</tr>';
                    }

                    if (descricao!='' && femea!=0) {
                        html += '<tr>';
                        html += '<td width="15%" class="categoria_vivo">' + descricao + '</td>';
                        html += '<td width="5%" class="sexo_vivo" style="text-align: center;">' + "F" + '</td>';
                        html += '<td width="10%" class="qtd_vivo" style="text-align: right;">' + femea + '</td>';
                        html += '<td width="10%" class="peso_vivo" style="text-align: right;">' + peso_femea + '</td>';
                        html += '<td width="10%" class="arroba_vivo" style="text-align: right;">' + '' + '</td>';
                        html += '<td width="15%" class="und_vivo"  style="text-align: center;">' + '' + '</td>';
                        html += '<td width="10%" class="valor_unit_vivo"  style="text-align: right;">' + '' + '</td>';
                        html += '<td width="10%" class="valor_total_vivo"  style="text-align: right;">' + '' + '</td>';
                        html += "<td hidden='' class='categoria_vivo_id'>" + codigo_id + "</td>";
                        html += "<td hidden='' class='und_vivo_id'>" + 0 + "</td>";
                        html += "<td hidden='' class='conta_vivo_id'>" + 0 + "</td>";
                        html += "<td hidden='' class='fator_arroba_vivo'>" + 0 + "</td>";
                        html += "<td width='10%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>";
                        html += '</tr>';
                    }
                }

                html += '</tbody>';
                html += '</table>';
                document.getElementById('tabela_itens_vivo').innerHTML = html;
                $(".btnEditar").bind("click", editar_item_animal_vivo_compra);
                $(".btnExcluir").bind("click", excuir_item_animal_vivo_compra);
                $(".tela_dados").hide();
                $(".linha_escondida").hide();
                $(".tela_dados").hide();
                $(".tela_peso_vivo").show();
                $(".tabela_itens_vivo").show();
                $(".editar").hide();
                $(".incluir").show();
            break;
            case 'C':   
                html = "";
                html += '<table class="table table-advance responsive-table tabela_itens_cabeca" id="tabela_itens_cabeca" width="100%" style="font-size: 13px;"';
                html += '<thead>';
                html += '<tr>';
                html += '<th>' + 'Categoria' + '</th>';
                html += '<th style="text-align: center;">' + 'Sexo' + '</th>';
                html += '<th style="text-align: right;">' + 'Nº Cabeças' + '</th>';
                html += '<th>' + 'Valor Unitário' + '</th>';
                html += '<th>' + 'Valor Total' + '</th>';
                html += '<th>' + '<i class="icon_cogs"></i> Ações' + '</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                for (var i = 0; i < numero_itens; i++) {
                    var codigo_id = id_cat[i];
                    var descricao = desc_cat[i];
                    var total = total_cat[i];
                    var macho = total_m[i];
                    var femea = total_f[i];
                    var peso_macho = peso_m[i];
                    var peso_femea = peso_f[i];

                    if (descricao!='' && macho!=0) {
                        html += '<tr>';
                        html += '<td width="15%" class="categoria_cabeca">' + descricao + '</td>';
                        html += '<td width="5%" class="sexo_cabeca" style="text-align: center;">' + "M" + '</td>';
                        html += '<td width="10%" class="qtd_cabeca" style="text-align: right;">' + macho + '</td>';
                        html += '<td width="10%" class="valor_unit_cabeca">' + '' + '</td>';
                        html += '<td width="10%" class="valor_total_cabeca">' + '' + '</td>';
                        html += "<td hidden='' class='categoria_cabeca_id'>" + codigo_id + "</td>";
                        html += "<td hidden='' class='conta_cabeca_id'>" + 0 + "</td>";
                        html += "<td width='10%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>";
                        html += '</tr>';
                    }

                    if (descricao!='' && femea!=0) {
                        html += '<tr>';
                        html += '<td width="15%" class="categoria_cabeca">' + descricao + '</td>';
                        html += '<td width="5%" class="sexo_cabeca" style="text-align: center;">' + "F" + '</td>';
                        html += '<td width="10%" class="qtd_cabeca" style="text-align: right;">' + femea + '</td>';
                        html += '<td width="10%" class="valor_unit_cabeca">' + '' + '</td>';
                        html += '<td width="10%" class="valor_total_cabeca">' + '' + '</td>';
                        html += "<td hidden='' class='categoria_cabeca_id'>" + codigo_id + "</td>";
                        html += "<td hidden='' class='conta_cabeca_id'>" + 0 + "</td>";
                        html += "<td width='10%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>";
                        html += '</tr>';
                    }
                }
                html += '</tbody>';
                html += '</table>';
                document.getElementById('tabela_itens_cabeca').innerHTML = html;
                $(".btnEditar").bind("click", editar_item_animal_cabeca_compra);
                $(".btnExcluir").bind("click", excuir_item_animal_cabeca_compra);
                $(".tela_dados").hide();
                $(".linha_escondida").hide();
                $(".tela_cabeca").show();
                $(".tabela_itens_cabeca").show();
                $(".editar").hide();
                $(".incluir").show();
            break;
        } 
    });
}

function salvar_item_animal_vivo_compra() {
    if ($("#categoria_vivo").val()==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a categoria');
        return;
    }

    var itemSelecionado = $("#categoria_vivo option:selected");
    var desc_categoria = itemSelecionado.text().trim();

    if ($("#sexo_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o sexo');
        return;
    }

    if ($("#qtd_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Nº de cabeças');
        return;
    }

    if ($("#peso_categoria_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso Kg');
        return;
    }

    if ($("#arroba_categoria_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso @');
        return;
    }

    if ($("#unidade_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a unidade para pesar (Kg/@)');
        return;
    }

    var itemSelecionado = $("#unidade_vivo option:selected");
    var desc_unidade = itemSelecionado.text().trim();

    if ($("#valor_unitario_vivo").val()=='' || $("#valor_unitario_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o preço unitário');
        return;
    }

    if ($("#total_vivo").val()=='' || $("#total_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o valor total');
        return;
    }

    if ($("#conta_vivo").val()=='0000000'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil');
        return;
    }

    $(".tabela_itens_vivo").show();

    $(".tabela_itens_vivo tbody").append(
        "<tr>"+
        "<td width='15%' class='categoria_vivo'>" + desc_categoria + "</td>"+
        "<td width='5%' class='sexo_vivo'>" + $("#sexo_vivo").val() + "</td>"+
        "<td width='10%' class='qtd_vivo' align='right'>" + $("#qtd_vivo").val() + "</td>"+
        "<td width='10%' class='peso_vivo' align='right'>" + $("#peso_categoria_vivo").val() + "</td>"+
        "<td width='10%' class='arroba_vivo' align='right'>" + $("#arroba_categoria_vivo").val() + "</td>"+
        "<td width='10%' class='und_vivo' align='center'>" + desc_unidade + "</td>"+
        "<td width='10%' class='valor_unit_vivo' align='right'>" + $("#valor_unitario_vivo").val() + "</td>"+
        "<td width='10%' class='valor_total_vivo' align='right'>" + $("#total_vivo").val() + "</td>"+
        "<td hidden='' class='categoria_vivo_id' align='right'>" + $("#categoria_vivo").val() + "</td>"+
        "<td hidden='' class='und_vivo_id'>" + $("#unidade_vivo").val() + "</td>"+
        "<td hidden='' class='conta_vivo_id'>" + $("#conta_vivo").val() + "</td>"+
        "<td hidden='' class='fator_arroba_vivo'>" + $("#fator_arroba_vivo").val() + "</td>"+
        "<td width='10%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>"+
        "</tr>");

    $(".btnEditar").bind("click", editar_item_animal_vivo_compra);
    $(".btnExcluir").bind("click", excuir_item_animal_vivo_compra);
    $("#categoria_vivo").val('000');
    $("#sexo_vivo").val('');
    $("#qtd_vivo").val('');
    $("#peso_categoria_vivo").val('');
    $("#fator_arroba_vivo").val('');
    $("#arroba_categoria_vivo").val('');
    $("#arrobaHelpBlock").text('');
    $("#unidade_vivo").val('');
    $("#conta_vivo").val('0000000');
    $("#valor_unitario_vivo").val('');
    $("#total_vivo").val('');
    document.getElementById("categoria_vivo").focus(); 

    somar_total_geral_vivo();
}

function salvar_editar_item_vivo_compra(){
    if ($("#categoria_vivo").val()==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a categoria');
        return;
    }

    var itemSelecionado = $("#categoria_vivo option:selected");
    var desc_categoria = itemSelecionado.text().trim();

    if ($("#sexo_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o sexo');
        return;
    }

    if ($("#qtd_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Nº de cabeças');
        return;
    }

    if ($("#peso_categoria_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso Kg');
        return;
    }

    if ($("#arroba_categoria_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o peso @');
        return;
    }

    if ($("#unidade_vivo").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a unidade para pesar (Kg/@)');
        return;
    }

    var itemSelecionado = $("#unidade_vivo option:selected");
    var desc_unidade = itemSelecionado.text().trim();

    if ($("#valor_unitario_vivo").val()==0 || $("#valor_unitario_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o preço unitário');
        return;
    }

    if ($("#total_vivo").val()=='' || $("#total_vivo").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o valor total');
        return;
    }

    if ($("#conta_vivo").val()=='0000000'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil');
        return;
    }

    $('.tabela_itens_vivo tbody tr').each(function(){
        row_index_salvar = $(this).index();

        if (row_index_salvar==row_index){
            $(this).find('.categoria_vivo').html(desc_categoria);
            $(this).find('.sexo_vivo').html($("#sexo_vivo").val());
            $(this).find('.qtd_vivo').html($("#qtd_vivo").val());
            $(this).find('.peso_vivo').html($("#peso_categoria_vivo").val());
            $(this).find('.arroba_vivo').html($("#arroba_categoria_vivo").val());
            $(this).find('.und_vivo').html(desc_unidade);
            $(this).find('.valor_unit_vivo').html($("#valor_unitario_vivo").val());
            $(this).find('.valor_total_vivo').html($("#total_vivo").val());
            $(this).find('.categoria_vivo_id').html($("#categoria_vivo").val());
            $(this).find('.und_vivo_id').html($("#unidade_vivo").val());
            $(this).find('.conta_vivo_id').html($("#conta_vivo").val());
            $(this).find('.fator_arroba_vivo').html($("#fator_arroba_vivo").val());
        }
    });

    $(".btnEditar").bind("click", editar_item_animal_vivo_compra);
    $(".btnExcluir").bind("click", excuir_item_animal_vivo_compra);
    $("#categoria_vivo").val('000');
    $("#sexo_vivo").val('');
    $("#qtd_vivo").val('');
    $("#peso_categoria_vivo").val('');
    $("#fator_arroba_vivo").val('');
    $("#arroba_categoria_vivo").val('');
    $("#arrobaHelpBlock").text('');
    $("#unidade_vivo").val('');
    $("#conta_vivo").val('0000000');
    $("#valor_unitario_vivo").val('');
    $("#total_vivo").val('');
    $(".editar").hide();
    $(".incluir").show();

    var tem_movimentacao_compra = $("input[name='tem_movimentacao_compra']:checked").val();

    if (tem_movimentacao_compra=="S") {
        $(".linha_escondida").hide();
    }

    somar_total_geral_vivo();
}

function editar_item_animal_vivo_compra() {
    row_index = $(this).parent().parent().index();

    var par = $(this).parent().parent(); //tr
    var tdcategoria_vivo = par.children("td:nth-child(1)");
    var tdsexo_vivo = par.children("td:nth-child(2)");
    var tdqtd_vivo = par.children("td:nth-child(3)");
    var tdpeso_vivo = par.children("td:nth-child(4)");
    var tdarroba_vivo = par.children("td:nth-child(5)");
    var tdund_vivo = par.children("td:nth-child(6)");
    var tdvalor_unit_vivo = par.children("td:nth-child(7)");
    var tdvalor_total_vivo = par.children("td:nth-child(8)");
    var tdcategoria_vivo_id = par.children("td:nth-child(9)");
    var tdund_vivo_id = par.children("td:nth-child(10)");
    var tdconta_vivo_id = par.children("td:nth-child(11)");
    var tdfator_arroba_vivo = par.children("td:nth-child(12)");

    $("#categoria_vivo").val(tdcategoria_vivo_id.html());
    $("#sexo_vivo").val(tdsexo_vivo.html());
    $("#qtd_vivo").val(tdqtd_vivo.html());
    $("#peso_categoria_vivo").val(tdpeso_vivo.html());
    $("#fator_arroba_vivo").val(tdfator_arroba_vivo.html());
    $("#arroba_categoria_vivo").val(tdarroba_vivo.html());
    $("#unidade_vivo").val(tdund_vivo_id.html());
    $("#valor_unitario_vivo").val(tdvalor_unit_vivo.html());
    $("#total_vivo").val(tdvalor_total_vivo.html());
    $("#conta_vivo").val(tdconta_vivo_id.html());
    $(".editar").show();
    $(".incluir").hide();
    $(".linha_escondida").show();

    var peso_kg = $("#peso_categoria_vivo").val();
    var fator_arroba = $("#fator_arroba_vivo").val();

    if (verifica_virgula(peso_kg)==',') {
        peso_kg = replace_valor(peso_kg);
    }

    if (verifica_virgula(fator_arroba)==',') {
        fator_arroba = replace_valor(fator_arroba);
    }

    if (fator_arroba==0){
        var total_arroba = peso_kg*0.031777;
        total_arroba = Math.round(total_arroba);
        $("#arrobaHelpBlock").text('Peso Total Kg*0.031777=' + total_arroba);
    }
    else {
        var total_arroba = peso_kg/fator_arroba;
        total_arroba = Math.round(total_arroba);
        $("#arrobaHelpBlock").text('Peso Total Kg/' + fator_arroba + '=' + total_arroba);
    }
}

function excuir_item_animal_vivo_compra(){
    row_index = $(this).parent().parent().index();
    var par = $(this).parent().parent();
    var tdcategoria_cabeca = par.children("td:nth-child(1)");
    var tdsexo_cabeca = par.children("td:nth-child(2)");

    if (tdsexo_cabeca.html()=="M") {
        var desc_sexo = 'Macho';
    }
    else {
        var desc_sexo = 'Fêmea';
    }

    if (window.confirm("Confirma enviar esse registro para lixeira?" + " " + tdcategoria_cabeca.html() + ' - ' + desc_sexo)) {     
        par.remove();

        $("#categoria_vivo").val('000');
        $("#sexo_vivo").val('');
        $("#qtd_vivo").val('');
        $("#peso_categoria_vivo").val('');
        $("#fator_arroba_vivo").val('');
        $("#arroba_categoria_vivo").val('');
        $("#unidade_vivo").val('');
        $("#conta_vivo").val('0000000');
        $("#valor_unitario_vivo").val('');
        $("#total_vivo").val('');
        $(".editar").hide();
        $(".incluir").show();
        document.getElementById("categoria_vivo").focus(); 
        somar_total_geral_vivo();
    }
}

function salvar_item_animal_cabeca_compra() {
    if ($("#categoria_cabeca").val()==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a categoria');
        return;
    }

    var itemSelecionado = $("#categoria_cabeca option:selected");
    var desc_categoria = itemSelecionado.text().trim();

    if ($("#sexo_cabeca").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o sexo');
        return;
    }

    if ($("#qtd_cabeca").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Nº de cabeças');
        return;
    }

    if ($("#valor_unitario_cabeca").val()=='' || $("#valor_unitario_cabeca").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o preço unitário');
        return;
    }

    if ($("#total_cabeca").val()=='' || $("#total_cabeca").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o valor total');
        return;
    }

    if ($("#conta_cabeca").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil');
        return;
    }

    $(".tabela_itens_cabeca").show();

    $(".tabela_itens_cabeca tbody").append(
        "<tr>"+
        "<td width='15%' class='categoria_cabeca'>" + desc_categoria + "</td>"+
        "<td width='5%' class='sexo_cabeca'>" + $("#sexo_cabeca").val() + "</td>"+
        "<td width='10%' class='qtd_cabeca'>" + $("#qtd_cabeca").val() + "</td>"+
        "<td width='10%' class='valor_unit_cabeca'>" + $("#valor_unitario_cabeca").val() + "</td>"+
        "<td width='10%' class='valor_total_cabeca'>" + $("#total_cabeca").val() + "</td>"+
        "<td hidden='' class='categoria_cabeca_id'>" + $("#categoria_cabeca").val() + "</td>"+
        "<td hidden='' class='conta_cabeca_id'>" + $("#conta_cabeca").val() + "</td>"+
        "<td width='12%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>"+
        "</tr>");

    $(".btnEditar").bind("click", editar_item_animal_cabeca_compra);
    $(".btnExcluir").bind("click", excuir_item_animal_cabeca_compra);
    $("#categoria_cabeca").val('000');
    $("#sexo_cabeca").val('');
    $("#qtd_cabeca").val('');
    $("#conta_cabeca").val('0000000');
    $("#valor_unitario_cabeca").val('');
    $("#total_cabeca").val('');
    $(".editar").hide();
    $(".incluir").show();

    document.getElementById("categoria_cabeca").focus(); 
    somar_total_geral_cabeca();
}

function salvar_editar_item_cabeca_compra() {
    if ($("#categoria_cabeca").val()==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a categoria');
        return;
    }

    var itemSelecionado = $("#categoria_cabeca option:selected");
    var desc_categoria = itemSelecionado.text().trim();

    if ($("#sexo_cabeca").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o sexo');
        return;
    }

    if ($("#qtd_cabeca").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Nº de cabeças');
        return;
    }

    if ($("#valor_unitario_cabeca").val()=='' || $("#valor_unitario_cabeca").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o preço unitário');
        return;
    }

    if ($("#total_cabeca").val()=='' || $("#total_cabeca").val()=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o valor total');
        return;
    }

    if ($("#conta_cabeca").val()==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil');
        return;
    }

    $('.tabela_itens_cabeca tbody tr').each(function(){
        row_index_salvar = $(this).index();

        if (row_index_salvar==row_index){
            $(this).find('.categoria_cabeca').html(desc_categoria);
            $(this).find('.sexo_cabeca').html($("#sexo_cabeca").val());
            $(this).find('.qtd_cabeca').html($("#qtd_cabeca").val());
            $(this).find('.valor_unit_cabeca').html($("#valor_unitario_cabeca").val());
            $(this).find('.valor_total_cabeca').html($("#total_cabeca").val());
            $(this).find('.categoria_cabeca_id').html($("#categoria_cabeca").val());
            $(this).find('.conta_cabeca_id').html($("#conta_cabeca").val());
        }
    });

    $(".btnEditar").bind("click", editar_item_animal_cabeca_compra);
    $(".btnExcluir").bind("click", excuir_item_animal_cabeca_compra);
    $("#categoria_cabeca").val('000');
    $("#sexo_cabeca").val('');
    $("#qtd_cabeca").val('');
    $("#conta_cabeca").val('0000000');
    $("#valor_unitario_cabeca").val('');
    $("#total_cabeca").val('');
    $(".editar").hide();
    $(".incluir").show();

    var tem_movimentacao_compra = $("input[name='tem_movimentacao_compra']:checked").val();

    if (tem_movimentacao_compra=="S") {
        $(".linha_escondida").hide();
    }

    somar_total_geral_cabeca();
}

function editar_item_animal_cabeca_compra(){
    row_index = $(this).parent().parent().index();

    var par = $(this).parent().parent(); //tr
    var tdcategoria_cabeca = par.children("td:nth-child(1)");
    var tdsexo_cabeca = par.children("td:nth-child(2)");
    var tdqtd_cabeca = par.children("td:nth-child(3)");
    var tdvalor_unit_cabeca = par.children("td:nth-child(4)");
    var tdvalor_total_cabeca = par.children("td:nth-child(5)");
    var tdcategoria_cabeca_id = par.children("td:nth-child(6)");
    var tdconta_cabeca_id = par.children("td:nth-child(7)");

    $("#categoria_cabeca").val(tdcategoria_cabeca_id.html());
    $("#sexo_cabeca").val(tdsexo_cabeca.html());
    $("#qtd_cabeca").val(tdqtd_cabeca.html());
    $("#valor_unitario_cabeca").val(tdvalor_unit_cabeca.html());
    $("#total_cabeca").val(tdvalor_total_cabeca.html());
    $("#conta_cabeca").val(tdconta_cabeca_id.html());
    $(".editar").show();
    $(".incluir").hide();
    $(".linha_escondida").show();
}

function excuir_item_animal_cabeca_compra(){
    row_index = $(this).parent().parent().index();
    var par = $(this).parent().parent();
    var tdcategoria_cabeca = par.children("td:nth-child(1)");
    var tdsexo_cabeca = par.children("td:nth-child(2)");

    if (tdsexo_cabeca.html()=="M") {
        var desc_sexo = 'Macho';
    }
    else {
        var desc_sexo = 'Fêmea';
    }

    if (window.confirm("Confirma enviar esse registro para lixeira?" + " " + tdcategoria_cabeca.html() + ' - ' + desc_sexo)) {     
        par.remove();
        $("#categoria_cabeca").val('000');
        $("#sexo_cabeca").val('');
        $("#qtd_cabeca").val('');
        $("#valor_unitario_cabeca").val('');
        $("#total_cabeca").val('');
        $("#conta_cabeca").val('000000');
        $(".editar").hide();
        $(".incluir").show();
        document.getElementById("categoria_cabeca").focus(); 
        somar_total_geral_cabeca();
    }
}

function gravar_compra(){
    var forma_pagto = $("#forma_pri").val();
    var conta_pagto = $("#conta_pri").val();
    var conta_contabil = $("#conta_contabil").val();
    var centro_custos = $("#centro_custos").val();
    var total_receber = $("#total_receber").val();
    var valor_pri = $("#valor_pri_parcela").val();
    var vencimento_pri = $("#vencimento_pri_parcela").val();

    if (total_receber=='' || total_receber=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Total a receber não pode ser zero!');
        return;
    }

    if (valor_pri=='' || valor_pri=='0,00'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o valor da primeira parcela!');
        return;
    }

    if (vencimento_pri=='' || vencimento_pri==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Vencimento da primeira parcela!');
        return;
    }

    if (forma_pagto==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Forma de Pagamento da primeira parcela!');
        return;
    }

    if (conta_pagto==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Pagamento da primeira parcela!');
        return;
    }

    if (conta_contabil==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Conta Contábil!');
        return;
    }

    /*if (centro_custos==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Centro de Custos!');
        return;
    }*/

    if (verifica_virgula(total_receber)==',') {
        total_receber = replace_valor(total_receber);
    }

    if (verifica_virgula(valor_pri)==',') {
        valor_pri = replace_valor(valor_pri);
    }

    var total_parcelas = 0;

    $('#tabela_parcelas tbody tr').each(function(){
        var valor_parcela = $(this).find('.txparcela').html();

        if (valor_parcela!=undefined && valor_parcela!=''){
            if (verifica_virgula(valor_parcela)==',') {
               valor_parcela = replace_valor(valor_parcela);
            }

            total_parcelas+= parseFloat(valor_parcela);
        }
    });

    var restante_parcela = total_receber - valor_pri - total_parcelas;

    if (restante_parcela<0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Valor total das parcelas está maior que o total a receber!');
        return;
    }
    else if (restante_parcela>0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Valor total das parcelas está menor que o total a receber!');
        return;
    }

    var array_tabela_parcelas = new Array();
    var item_parcela = new Array();
    var grupo_parcelas = "";
 
    $('#tabela_parcelas tbody tr').each(function(){
        var prazo = $(this).find('.txtprazo').html();
        var valor = $(this).find('.txparcela').html();
        var forma = $(this).find('.txtcodigo_forma').html();
        var conta = $(this).find('.txtcodigo_conta').html();

        if (valor!=undefined && valor!=''){
            if (verifica_virgula(valor)==',') {
               valor = replace_valor(valor);
            }

            for (i = 0; i <= 4; i++) {
                item_parcela[i]=0;
            }

            item_parcela[0]=prazo;
            item_parcela[1]=valor;
            item_parcela[2]=forma;
            item_parcela[3]=conta;

            var tabela_itens=item_parcela.join("|");
            array_tabela_parcelas.push(tabela_itens);
            grupo_parcelas=array_tabela_parcelas.join("<|>");
        }
    });

    $("#array_parcelas").val(grupo_parcelas);

    var tipo_compra = $("input[name='tipo_compra']:checked").val();

    var array_tabela_itens = new Array();
    var item = new Array();
    var grupo_itens = "";
    var tem_valor = 'S';

    switch (tipo_compra) {
        case 'V':
            $('.tabela_itens_vivo tbody tr').each(function(){
                var categoria = $(this).find('.categoria_vivo_id').html();
                var sexo = $(this).find('.sexo_vivo').html();
                var qtd = $(this).find('.qtd_vivo').html();
                var peso = $(this).find('.peso_vivo').html();
                var arroba = $(this).find('.arroba_vivo').html();
                var und = $(this).find('.und_vivo_id').html();
                var vlr_unit = $(this).find('.valor_unit_vivo').html();
                var vlr_total = $(this).find('.valor_total_vivo').html();
                var conta = $(this).find('.conta_vivo_id').html();
                var fator_arroba = $(this).find('.fator_arroba_vivo').html();

                if (vlr_unit==''){
                    tem_valor="N";
                }

                if (categoria!=undefined && categoria!=''){
                    if (verifica_virgula(vlr_unit)==',') {
                       vlr_unit = replace_valor(vlr_unit);
                    }

                    if (verifica_virgula(vlr_total)==',') {
                       vlr_total = replace_valor(vlr_total);
                    }

                    if (verifica_virgula(peso)==',') {
                       peso = replace_valor(peso);
                    }

                    if (verifica_virgula(arroba)==',') {
                       arroba = replace_valor(arroba);
                    }

                    if (verifica_virgula(fator_arroba)==',') {
                       fator_arroba = replace_valor(fator_arroba);
                    }

                    for (i = 0; i <= 11; i++) {
                        item[i]=0;
                    }

                    item[0]=categoria;
                    item[1]=sexo;
                    item[2]=qtd;
                    item[3]=peso;
                    item[4]=0;
                    item[5]=und;
                    item[6]=vlr_unit;
                    item[7]=vlr_total;
                    item[8]=arroba;
                    item[9]=0;
                    item[10]=conta;
                    item[11]=fator_arroba;

                    var tabela_itens=item.join("|");
                    array_tabela_itens.push(tabela_itens);
                    grupo_itens=array_tabela_itens.join("<|>");
                }
            });
        break;
        case 'C':
            $('.tabela_itens_cabeca tbody tr').each(function(){
                var categoria = $(this).find('.categoria_cabeca_id').html();
                var sexo = $(this).find('.sexo_cabeca').html();
                var qtd = $(this).find('.qtd_cabeca').html();
                var vlr_unit = $(this).find('.valor_unit_cabeca').html();
                var vlr_total = $(this).find('.valor_total_cabeca').html();
                var conta = $(this).find('.conta_cabeca_id').html();

                if (vlr_unit==''){
                    tem_valor="N";
                }

                if (categoria!=undefined && categoria!=''){
                    if (verifica_virgula(vlr_unit)==',') {
                       vlr_unit = replace_valor(vlr_unit);
                    }

                    if (verifica_virgula(vlr_total)==',') {
                       vlr_total = replace_valor(vlr_total);
                    }

                    for (i = 0; i <= 10; i++) {
                        item[i]=0;
                    }

                    item[0]=categoria;
                    item[1]=sexo;
                    item[2]=qtd;
                    item[3]=0;
                    item[4]=0;
                    item[5]=0;
                    item[6]=vlr_unit;
                    item[7]=vlr_total;
                    item[8]=0;
                    item[9]=0;
                    item[10]=conta;
                    item[11]=0;

                    var tabela_itens=item.join("|");
                    array_tabela_itens.push(tabela_itens);
                    grupo_itens=array_tabela_itens.join("<|>");
                }
            });
        break;
    }

    if (tem_valor=='N'){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Existe item sem o valor unitário informado. Verificar os itens na aba Dados da Compra.');
        return;
    }

    $("#array_itens").val(grupo_itens);

    var dados = $('#form_gravar_compra').serialize();

    $(".gravar_compra").attr("disabled", true);

    $.ajax({
        type: "POST",
        url: 'gravar_compra.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $(".gravar_compra").attr("disabled", false);
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $(".gravar_compra").attr("disabled", false);
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        }
    });

}
// FIM COMPRAS

// RELATORIO
function listar_compra_venda_tela(opcao){
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var tipo_rel = $("input[name='tipo_rel']:checked").val();
    var local = $("#codigo_local").val();
    var centro_custo = $("#codigo_cc").val();

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

    if (tipo_rel == undefined) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o tipo de relatório.');
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

    if (centro_custo==null) {
        var array_cc= new Array();
    }
    else {
        var array_cc = new Array();
        var valor = new Array();

        for (i = 0; i <= centro_custo.length; i++) {
            valor[i]=centro_custo[i];
        }

        var array_cc=valor.join(",");
    }

    if (tipo_rel==2) {
        opc_rel_filtro='Compras->';
    }
    else{
        opc_rel_filtro='Vendas->';
    }

    if (data_inicial!='' && data_inicial!=0) {
        var data_ini = data_inicial.split("-");
        var data_fim = data_final.split("-");

        data_filtro = 'Período: ' + data_ini[2]+'/'+data_ini[1]+'/'+data_ini[0] + ' até ' + data_fim[2]+'/'+data_fim[1]+'/'+data_fim[0];
    }
    else {
        data_filtro = '';
    }

    var options = $('#codigo_local option:selected');
    var local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_local').text();
        local_filtro.push( desc.trim() );
    });

    if (local_filtro!=''){
            local_filtro = local_filtro+'->';
    }

    var options = $('#codigo_cc option:selected');
    var cc_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_cc').text();
        cc_filtro.push( desc.trim() );
    });

    if (cc_filtro!=''){
        cc_filtro = cc_filtro+'->';
    }

    var descricao_filtro = opc_rel_filtro+local_filtro+cc_filtro+data_filtro;
    var origem_relatorio = $("#tipo_relatorio").val();

    $("#aguardar").modal();

    if (opcao=='1') {
        location.href='form_lista_compra_venda_rel.php?descricao_filtro=' + descricao_filtro + 
        '&tipo_rel=' + tipo_rel + 
        '&codigo_local=' + array_local + 
        '&codigo_cc=' + array_cc + 
        '&data_inicial=' + data_inicial +
        '&data_final=' + data_final  + 
        "&origem_relatorio=" + origem_relatorio;
    }
    else {
        location.href='rel_compra_venda_excel.php?descricao_filtro=' + descricao_filtro + 
        '&tipo_rel=' + tipo_rel + 
        '&local=' + array_local + 
        '&codigo_cc=' + array_cc + 
        '&data_inicial=' + data_inicial +
        '&data_final=' + data_final;

        tout = setTimeout('limpar_tela()', 5000);
    }
}

function lista_compra_venda_excel(){
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var tipo_rel = $("#tipo_rel").val();
    var local = $("#codigo_local").val();
    var centro_custo = $("#codigo_cc").val();
    var descricao_filtro = $("#descricao_filtro").val();

    $("#aguardar").modal();

    location.href='rel_compra_venda_excel.php?descricao_filtro=' + descricao_filtro + 
    '&tipo_rel=' + tipo_rel +
    '&local=' + local + 
    '&codigo_cc=' + centro_custo + 
    '&data_inicial=' + data_inicial + 
    '&data_final=' + data_final;

    tout = setTimeout('limpar_tela()', 5000);
}

function limpar_tela(){
    $('#aguardar').modal('hide');
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

var mask3 = {
     money: function() {
        var el = this
        ,exec = function(v) {
        v = v.replace(/\D/g,"");
        v = new String(Number(v));
        var len = v.length;
        if (1== len)
        v = v.replace(/(\d)/,"0.0$1");
        else if (2 == len)
        v = v.replace(/(\d)/,"0.0$11");
        else if (3 == len)
        v = v.replace(/(\d)/,"0.0$111");
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

function formatMoney6(n, c, d, t) {
  c = isNaN(c = Math.abs(c)) ? 6 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
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

Number.prototype.AddZero= function(b,c){
    var  l= (String(b|| 10).length - String(this).length)+1;
    return l> 0? new Array(l).join(c|| '0')+this : this;
}
