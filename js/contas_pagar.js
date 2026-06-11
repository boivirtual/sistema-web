/**CONTAS A PAGAR*/
const idConta = [];
const selectedConta = [];

// Filtro ativo pelos cards de resumo (null = sem filtro = Total do Período)
var ctpFiltroAtivo = null;

// Converte dd/mm/yyyy para objeto Date (sem problema de fuso horário)
function ctpParseDate(str) {
    if (!str) return null;
    var s = str.replace(/<[^>]*>/g, '').trim();
    var p = s.split('/');
    if (p.length < 3) return null;
    return new Date(parseInt(p[2]), parseInt(p[1]) - 1, parseInt(p[0]));
}

// Registra filtro customizado do DataTables para os cards
$.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    // Aplica somente na tabela de contas a pagar
    if (settings.nTable.id !== 'tabela_contas_pagar') return true;
    if (!ctpFiltroAtivo) return true;

    var hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    // Col 7 = Vencimento (índice 7), Col 9 = Pagamento (índice 9)
    var dtVenc    = ctpParseDate(data[7]);
    var dtPgto    = ctpParseDate(data[9]);
    var foiPago   = (dtPgto !== null);

    if (ctpFiltroAtivo === 'vencidos') {
        return !foiPago && dtVenc !== null && dtVenc < hoje;
    }
    if (ctpFiltroAtivo === 'vencem_hoje') {
        return !foiPago && dtVenc !== null && dtVenc.getTime() === hoje.getTime();
    }
    if (ctpFiltroAtivo === 'a_vencer') {
        return !foiPago && dtVenc !== null && dtVenc > hoje;
    }
    if (ctpFiltroAtivo === 'pagos') {
        return foiPago;
    }
    return true;
});

$(window).load(function(){
    // Exibe filtros quando faz reload
    var filtro_local = $("#exibe_local").val();
 
    if (filtro_local!='' && filtro_local!=null) {
        var filtro_local = filtro_local.split(',');

        $.each(filtro_local, function(idx, val) {
            $('#codigo_fazenda option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_fazenda').selectpicker('refresh');
    }

    var filtro_fornecedor = $("#exibe_fornecedor").val();
 
    if (filtro_fornecedor!='' && filtro_fornecedor!=null) {
        var filtro_fornecedor = filtro_fornecedor.split(',');

        $.each(filtro_fornecedor, function(idx, val) {
            $('#razao_nome option[value=' + val + ']').attr('selected', true);
        });

        $('#razao_nome').selectpicker('refresh');
    }

    var filtro_cc = $("#exibe_cc").val();

    if (filtro_cc!='' && filtro_cc!=null) {
        var filtro_cc = filtro_cc.split(',');

        $.each(filtro_cc, function(idx, val) {
            $('#codigo_cc option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_cc').selectpicker('refresh');
    }

    var filtro_conta = $("#exibe_conta").val();
    var limpa_filtro_contas = $("#limpar_filtro_contas").val();

    if (limpa_filtro_contas=='S') {
        $("#contas_selecionadas").val('Todas ou (Clique p/ selecionar contas)');

        $.ajax({
            type: "POST",
            url: "lista_conta_contabil.php",
            data: {
            'tipo_conta': 'D'
            },
            success: function (data) {
                $("#modal_conta_info").html(data);

                $('input[name="conta_option"]').each(function (element) {
                    idConta.push($(this).attr("id"));
                });

                $("#modal_conta").modal("show");
                $('.consultar').show();
                $('.filtros').hide();
            },
        });
    }
    else if (filtro_conta!='' && filtro_conta!=null) {
        $.ajax({
            type: "POST",
            url: "lista_conta_contabil.php",
            data: {
            'tipo_conta': 'D'
            },
            success: function (data) {
                $("#modal_conta_info").html(data);

                $('input[name="conta_option"]').each(function (element) {
                    idConta.push($(this).attr("id"));
                });

                var filtro_conta = $("#exibe_conta").val();
                var filtro_conta = filtro_conta.split(',');

                $.each(filtro_conta, function(idx, val) {
                    document.getElementById(`${val}`).checked = true;
                });

                var aChk = document.getElementsByName("conta_option");
                var tem_conta = '';
                var conta_filtro = [];

                for (var i = 0; i < aChk.length; i++) {
                    if (aChk[i].checked == true) {
                        tem_conta = 'S';
                    }
                }

                if (tem_conta=='') {
                    conta_filtro = "Todas ou (Clique p/ selecionar contas)";
                }
                else {
                    for (var i = 0; i < aChk.length; i++) {
                        if (aChk[i].checked == true) {
                            var desc = aChk[i].className;
                            conta_filtro.push(desc.trim());
                        }
                    }
                }

                $("#contas_selecionadas").val(conta_filtro);

                consultar_ctp();
            },
        });
    }
    else {
        // auto-load feito pelo document.ready em form_contas_pagar.php
    }

    // Fim exibe filtros 

    var situacao_conta = $("#desc_situacao").val();

    if (situacao_conta!='Pago') {
        $('#baixa_conta_pagar').show();
    }
    else {
        $('#baixa_pagar').hide();
    }

    // lista caixa ao iniciar o programa
    var lista_ao_entrar = $("#lista_ao_entrar").val();

    if (lista_ao_entrar=="S") {
        $('#aguardar').show();

        var ano = $("#ano_diario").val();
        var mes = $("#mes_diario").val();
        var opc_rel = $("#opc_diario").val();
        var forma_pag = $("#forma_pagto_diario").val();
        $('#lista_caixa').load('form_lista_caixa_diario.php?opc_rel=' + opc_rel + '&mes=' + mes + '&ano=' + ano + '&forma_pag=' + forma_pag);
        
        tout = setTimeout('limpar_tela()', 1000);
        $("#lista_ao_entrar").val('N');
    }
    // Fim lista caixa

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

function informacoes_uso() {
    $("#ajuda").modal();
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


function replace_valor(valor_replace){
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

function digita_valor(){
    $('#vlr_primeira_parcela').bind('keypress',mask.money);
    $('#vlr_parcela').bind('keypress',mask.money);
    $('#vlr_parcela_fixa').bind('keypress',mask.money);
    $('#vlr_compra').bind('keypress',mask.money);
    $('#vlr_juros').bind('keypress',mask.money);
    $('#vlr_desconto').bind('keypress',mask.money);
    $('#vlr_acrescimo').bind('keypress',mask.money);
    $('#vlr_pagamento').bind('keypress',mask.money);
    $('#valor_pagamento').bind('keypress',mask.money);
}

function digita_valor_custo(ordem){
    $(`#primeira_parcela${ordem}`).bind('keypress',mask.money);
    $(`#parcela_restante${ordem}`).bind('keypress',mask.money);

    $('#tabela_fazendas tbody tr').each(function(){
        $(this).find('.percentual').val('');
    });
}

function exibe_primeira_parcela_custo(ordem){
    var primeira_parcela = $(`#primeira_parcela${ordem}`).val();
    if (verifica_virgula(primeira_parcela)==',') {
        primeira_parcela = replace_valor(primeira_parcela);
    }

    $(`#primeira_parcela${ordem}`).val(formatMoney(primeira_parcela));
}

function exibe_parcela_restante_custo(ordem){
    var parcela_restante = $(`#parcela_restante${ordem}`).val();
    if (verifica_virgula(parcela_restante)==',') {
        parcela_restante = replace_valor(parcela_restante);
    }

    $(`#parcela_restante${ordem}`).val(formatMoney(parcela_restante));

    /*$('#tabela_fazendas tbody tr').each(function(){
        $(this).find('.percentual').val('');
    });*/
}

function exibe_valor_parcela(){
    var vlr_parcela = $("#vlr_parcela").val();
    if (verifica_virgula(vlr_parcela)==',') {
        vlr_parcela = replace_valor(vlr_parcela);
    }
    $("#vlr_parcela").val(formatMoney(vlr_parcela));
}

function exibe_valor_primeira_parcela(){
    var vlr_primeira_parcela = $("#vlr_primeira_parcela").val();
    if (verifica_virgula(vlr_primeira_parcela)==',') {
        vlr_primeira_parcela = replace_valor(vlr_primeira_parcela);
    }
    $("#vlr_primeira_parcela").val(formatMoney(vlr_primeira_parcela));
}

function exibe_valor_parcela_fixa(){
    var vlr_parcela_fixa = $("#vlr_parcela_fixa").val();
    if (verifica_virgula(vlr_parcela_fixa)==',') {
        vlr_parcela_fixa = replace_valor(vlr_parcela_fixa);
    }
    $("#vlr_parcela_fixa").val(formatMoney(vlr_parcela_fixa));
}

function exibe_valor_compra(){
    var vlr_compra = $("#vlr_compra").val();
    if (verifica_virgula(vlr_compra)==',') {
        vlr_compra = replace_valor(vlr_compra);
    }
    $("#vlr_compra").val(formatMoney(vlr_compra));
}

function exibe_valor_juros(){
    var vlr_juros = $("#vlr_juros").val();
    if (verifica_virgula(vlr_juros)==',') {
        vlr_juros = replace_valor(vlr_juros);
    }
    $("#vlr_juros").val(formatMoney(vlr_juros));
}

function exibe_valor_desconto(){
    var vlr_desconto = $("#vlr_desconto").val();
    if (verifica_virgula(vlr_desconto)==',') {
        vlr_desconto = replace_valor(vlr_desconto);
    }
    $("#vlr_desconto").val(formatMoney(vlr_desconto));
}

function exibe_valor_acrescimo(){
    var vlr_acrescimo = $("#vlr_acrescimo").val();
    if (verifica_virgula(vlr_acrescimo)==',') {
        vlr_acrescimo = replace_valor(vlr_acrescimo);
    }
    $("#vlr_acrescimo").val(formatMoney(vlr_acrescimo));
}

function exibe_valor_pagamento(){
    var valor_pagamento = $("#valor_pagamento").val();
    if (verifica_virgula(valor_pagamento)==',') {
        valor_pagamento = replace_valor(valor_pagamento);
    }
    $("#valor_pagamento").val(formatMoney(valor_pagamento));
}

function exibe_vlr_pagamento(){
    var valor_pagamento = $("#vlr_pagamento").val();
    if (verifica_virgula(valor_pagamento)==',') {
        valor_pagamento = replace_valor(valor_pagamento);
    }
    $("#vlr_pagamento").val(formatMoney(valor_pagamento));
}

//
/** chamada da rotina para enviar registro de contas a pagar para lixeira*/
function enviar_lixeira($id,$doc,$parcela,$opcao){
  
    var opcao = $opcao;

	switch (opcao) {
    case 1:
		if (window.confirm("Confirma excluir esse registro permanentemente?" + " " + $doc + "/" + $parcela)) {     
            $.post("excluir_contas_pagar.php",{id: $id, opcao: opcao}, function(valor){
         
                var php = valor.split("<|>");

                if (php[0]==9){
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(php[1]);
                    return;
                }
                else if (php[0]==0){
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(php[1]);
                    return;
                }
           });
	  	}
        break;
	} 
}

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
 "date-br-pre": function ( a ) {
  if (a == null || a == "") {
   return 0;
  }
  // Remove HTML tags e espaços extras para garantir somente "dd/mm/aaaa"
  var text = a.replace(/<[^>]*>/g, '').trim();
  if (text == "") return 0;
  var brDatea = text.split('/');
  if (brDatea.length < 3) return 0;
  return (brDatea[2] + brDatea[1] + brDatea[0]) * 1;
 },

 "date-br-asc": function ( a, b ) {
  return ((a < b) ? -1 : ((a > b) ? 1 : 0));
 },

 "date-br-desc": function ( a, b ) {
  return ((a < b) ? 1 : ((a > b) ? -1 : 0));
 }
} );

function exibe_filtros_aceite() {
    $("#modal_filtro_aceite").modal("show");
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
    $('#baixar_selecionadas').on('click', function() {
        $(this).prop({
            disabled: true,
            innerHTML: 'Aguarde...'
      });
    });

    $('#tabela_contas_pagar').DataTable({
        "paging":   false,
        "ordering": true,
        "order":    [],
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
            null,
            null,
            null,
            null,
            null,
            null,
            { "sType": "date-br" },
            { "sType": "date-br" },
            null,
            { "sType": "date-br" },
            null,
            null
        ],

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>><'#ctp-cards-container'>t",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
            var cardsHtml = $('#ctp-cards-source').html();
            if (cardsHtml) {
                $('#ctp-cards-container').html(cardsHtml);

                // Filtro por card: ao clicar aplica filtro no DataTable
                $(document).on('click', '#ctp-cards-container .ctp-card-total', function () {
                    var filtro = $(this).data('filtro');

                    // Se clicar no mesmo card ativo ou no Total, remove o filtro
                    if (ctpFiltroAtivo === filtro || filtro === 'total_periodo') {
                        ctpFiltroAtivo = null;
                        $('#ctp-cards-container .ctp-card-total').removeClass('ativo');
                    } else {
                        ctpFiltroAtivo = filtro;
                        $('#ctp-cards-container .ctp-card-total').removeClass('ativo');
                        $(this).addClass('ativo');
                    }

                    $('#tabela_contas_pagar').DataTable().draw();
                });
            }
            // Remove borda do topo do thead (entre cards e cabeçalho das colunas)
            $('#tabela_contas_pagar thead tr:first-child th').css('border-top', '0');
            $('#tabela_contas_pagar').css('margin-top', '0');
        },

    });


    $('#tabela_aceite_contas').DataTable({
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
            null,
            null,
            null,
            null,
            { "sType": "date-br" },
            null,
            { "sType": "date-br" },
            null,
            null,
            null
        ],
        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function() {
            $('table.dataTable').css("width", "100%");
          }

    });

    $('#tabela_caixa_diario').DataTable({
        "paging":   false,
        "ordering": false,
        "info":     true,
        "scrollY": 600,
        "scrollX": true,
        "language": {
          "sSearch": "Buscar na lista:",
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

    $(".fecha_editar_dados").click(function(){
		location.href='form_contas_pagar.php'
    });

    $(".fecha_dados_baixa").click(function(){
        $('.dados_baixa').modal('hide');
    });

    $(".exibir_filtro").click(function(){
        $('.filtro_escondido').hide();
        $('.filtro_exibido').show();
    });

    $(".esconder_filtro").click(function(){
        var tipo_caixa = $("#tipo_caixa").val();

        if (tipo_caixa=="M") {
            $('.opcao_pdf').hide();
        }
        else {
            $('.opcao_pdf').show();
        }

        $('.filtro_escondido').show();
        $('.filtro_exibido').hide();
    });

    // grava ctp na edição
    $('.confirma_gravar_ctp').click(function(){
        $("#errors").html('');
 
        var dados = $('#form_gravar_contas_pagar').serialize();

        $.ajax({
            type: "POST",
            url: 'gravar_contas_pagar.php',
            data: dados,
            success: function(data){
               // alert (data);
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else {
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }
        });
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

    $('.radio-inline').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#codigo_fazenda').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#codigo_cc').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    $('#razao_nome').change(function(){
        $('.consultar').show();
        $('.filtros').hide();
    });

    // Fim acendo botão 

    $('#classe_pessoa').change(function(){
        var classe_pessoa = $('#classe_pessoa').val();

        if (classe_pessoa == 02) {
            $('#servico_fornecedor').show();
        }
        else {
            $('#servico_fornecedor').hide();
        }
    });

    $('#codigo_forma_rec').change(function(){
        var codigo = $('#codigo_forma_rec').val();

        if (document.getElementById('pago').checked){
            var pago="S";
        }
        else {
            var pago="N";
        }

        $.post("ler_conta_pagamento.php",{codigo: codigo}, function(valor){
      
            var php = valor.split("<|>");

            if (php[0]==9){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(php[1]);
                return;
            }

            if (php[2]!=0 && pago=="S"){
                $('.cheque').show();
            }
            else {
                $('.cheque').hide();
            }
            return;
        });
    });

    $('#codigo_forma_rec_age').change(function(){
        var codigo = $('#codigo_forma_rec_age').val();

        $.post("ler_conta_pagamento.php",{codigo: codigo}, function(valor){
      
            var php = valor.split("<|>");

            if (php[0]==9){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(php[1]);
                return;
            }

            if (php[2]!=0){
                $('.cheque').show();
            }
            else {
                $('.cheque').hide();
            }
            return;
        });
    });

    $('#codigo_forma_agen').change(function(){
        var codigo = $('#codigo_forma_agen').val();

        $.post("ler_conta_pagamento.php",{codigo: codigo}, function(valor){
      
            var php = valor.split("<|>");

            if (php[0]==9){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(php[1]);
                return;
            }

            if (php[2]!=0){
                $('.cheque').show();
            }
            else {
                $('.cheque').hide();
            }
            return;
        });
    });

    $('#pago').click(function(event) {
        if(this.checked) {
            $('.checkbox1').each(function() {
                this.checked = true; 
                $('#dados_pagamento').show(); 
                var data_pagamento = $("#data_vencimento").val();  
                var valor_pago = $("#vlr_primeira_parcela").val();  
                if (verifica_virgula(valor_pago)==',') {
                    valor_pago = replace_valor(valor_pago);
                }
                $("#data_pagamento").val(data_pagamento);           
                $("#vlr_pagamento").val(formatMoney(valor_pago));         
            });
        }else{
            $('.checkbox1').each(function() {
                this.checked = false;  
                $('#dados_pagamento').hide();      
                $("#data_pagamento").val('');           
            });         
        }
    });

    $('#vlr_desconto').change(function(){
        var vlr_desconto = $('#vlr_desconto').val();
        var vlr_juros = $('#vlr_juros').val();
        var vlr_pago = $('#vlr_primeira_parcela').val();

        if (vlr_desconto==''){
            vlr_desconto = 0.00;
        }
        else {
            if (verifica_virgula(vlr_desconto)==',') {
                vlr_desconto = replace_valor(vlr_desconto);
            }
        }

        if (vlr_juros==''){
            vlr_juros = 0.00;
        }
        else {
            if (verifica_virgula(vlr_juros)==',') {
                vlr_juros = replace_valor(vlr_juros);
            }
        }

        if (verifica_virgula(vlr_pago)==',') {
            vlr_pago = replace_valor(vlr_pago);
        }

        var vrl_juros_soma = parseFloat(vlr_juros);
        var vrl_desconto_sub = parseFloat(vlr_desconto);
        var vlr_pago_result = parseFloat(vlr_pago);

        vlr_pago = vlr_pago_result - vrl_desconto_sub + vrl_juros_soma;

        $("#vlr_pagamento").val(formatMoney(vlr_pago));         

    });

    $('#vlr_juros').change(function(){
        var vlr_desconto = $('#vlr_desconto').val();
        var vlr_juros = $('#vlr_juros').val();
        var vlr_pago = $('#vlr_primeira_parcela').val();

        if (vlr_desconto==''){
            vlr_desconto = 0.00;
        }
        else {
            if (verifica_virgula(vlr_desconto)==',') {
                vlr_desconto = replace_valor(vlr_desconto);
            }
        }

        if (vlr_juros==''){
            vlr_juros = 0.00;
        }
        else {
            if (verifica_virgula(vlr_juros)==',') {
                vlr_juros = replace_valor(vlr_juros);
            }
        }

        if (verifica_virgula(vlr_pago)==',') {
            vlr_pago = replace_valor(vlr_pago);
        }

        var vrl_juros_soma = parseFloat(vlr_juros);
        var vrl_desconto_sub = parseFloat(vlr_desconto);
        var vlr_pago_result = parseFloat(vlr_pago);

        vlr_pago = vlr_pago_result - vrl_desconto_sub + vrl_juros_soma;

        $("#vlr_pagamento").val(formatMoney(vlr_pago));         

    });


    $('.tipo_inclusao').click(function(event) {
        var tipo_inclusao = $("input[name='tipo_inclusao']:checked").val();

        if(tipo_inclusao=="F") {
            $('#incluir_valor_fixo').show();   
            $('#incluir_prazo').hide(); 

        }else{
            $('#incluir_prazo').show();   
            $('#incluir_valor_fixo').hide(); 
        }
        
    });

    $('#seleciona_todos_aceite').click(function(event) {
        if(this.checked) {
            $('.checkbox1').each(function() {
                this.checked = true; 
            });
        }else{
            $('.checkbox1').each(function() {
                this.checked = false;  
            });         
        }
    });

    $('#seleciona_todos_somar').click(function(event) {
        if(this.checked) {
            $('.checkbox2').each(function() {
                this.checked = true; 
            });

        }else{
            $('.checkbox2').each(function() {
                this.checked = false;  
            });         
        }

        somar_total_para_baixar();
    });


    $("#contas_selecionadas").click(() => {
        $("#modal_conta").modal("show");
        $('.consultar').show();
        $('.filtros').hide();
    });

    $.ajax({
        type: "POST",
        url: "lista_conta_contabil.php",
        data: {
            'tipo_conta': 'D'
        },
        success: function (data) {
            $("#modal_conta_info").html(data);

            $('input[name="conta_option"]').each(function (element) {
                idConta.push($(this).attr("id"));
            });

            var filtro_conta = $("#exibe_conta").val();

            if (filtro_conta!='' && filtro_conta!=null) {
                var filtro_conta = filtro_conta.split(',');

                $.each(filtro_conta, function(idx, val) {
                    document.getElementById(`${val}`).checked = true;
                });
            }
        },
    });
});

function compareNumbers(a, b) {
    return a - b;
}

function get_marked_boxes(id, el, nivel) {
    if (nivel == 1) {
        if (el.checked) {
            if (selectedConta.indexOf(id) <= -1) {
                selectedConta.push(id);
            }
            for (let conta of idConta) {
                if (conta.charAt(0) == id.charAt(0) && conta != id) {
                    document.getElementById(`${conta}`).checked = true;
                }
                if (
                    selectedConta.indexOf(conta) <= -1 &&
                    conta.charAt(0) == id.charAt(0) &&
                    conta != id
                ) {
                    selectedConta.push(conta);
                }
            }
            selectedConta.sort(compareNumbers);
            return;
        }
        selectedConta.splice(selectedConta.indexOf(id), 1);

        for (let conta of idConta) {
            if (conta.charAt(0) == id.charAt(0) && conta != id) {
                document.getElementById(`${conta}`).checked = false;
                selectedConta.splice(selectedConta.indexOf(conta), 1);
            }
        }
        selectedConta.sort(compareNumbers);
        return;
    }

    if (nivel == 2) {
        let paiId = id.charAt(0) + "000000";
        if (el.checked) {

            if (selectedConta.indexOf(paiId) <= -1) {
                selectedConta.push(paiId);
            }

            if (selectedConta.indexOf(id) <= -1) {
               selectedConta.push(id);
            }

            for (let conta of idConta) {
                if (
                    conta.charAt(0) == id.charAt(0) &&
                    conta.charAt(2) == id.charAt(2) &&
                    selectedConta.indexOf(conta) <= -1 &&
                    conta != id
                ) {
                    document.getElementById(`${conta}`).checked = true;
                    selectedConta.push(conta);
                }
            }
            selectedConta.sort(compareNumbers);
            return;
        }

        selectedConta.splice(selectedConta.indexOf(id), 1);
        selectedConta.splice(selectedConta.indexOf(paiId), 1);

        for (let conta of idConta) {
            if (
                conta.charAt(2) == id.charAt(2) &&
                conta.charAt(0) == id.charAt(0) &&
                conta != id
            ) {
                document.getElementById(`${conta}`).checked = false;
                selectedConta.splice(selectedConta.indexOf(conta), 1);
            }
        }
        selectedConta.sort(compareNumbers);
        return;
    }

    if (nivel == 3) {
        let avoId = id.charAt(0) + "000000";
        let paiId = id.charAt(0) + "0" + id.charAt(2) + "0000";
        if (el.checked) {
            if (selectedConta.indexOf(paiId) <= -1) {
                selectedConta.push(paiId);
            }
            if (selectedConta.indexOf(avoId) <= -1) {
                selectedConta.push(avoId);
            }
            selectedConta.push(id);
            selectedConta.sort(compareNumbers);
            return;
        }

        if (
            selectedConta[selectedConta.indexOf(id) + 1].charAt(0) ==
                id.charAt(0) &&
            selectedConta[selectedConta.indexOf(id) + 1].charAt(2) ==
                id.charAt(2)
        ) {
            selectedConta.splice(selectedConta.indexOf(id), 1);
            return;
        }
        selectedConta.splice(selectedConta.indexOf(avoId), 1);
        selectedConta.splice(selectedConta.indexOf(paiId), 1);
        selectedConta.sort(compareNumbers);
    }
}

function exibe_contas_selecionadas() {
    var aChk = document.getElementsByName("conta_option");
    var tem_conta = '';
    var conta_filtro = [];

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            tem_conta = 'S';
        }
    }

    if (tem_conta=='') {
        conta_filtro = "Todas ou (Clique p/ selecionar contas)";
    }
    else {
        for (var i = 0; i < aChk.length; i++) {
            if (aChk[i].checked == true) {
                var desc = aChk[i].className;
                conta_filtro.push(desc.trim());
            }
        }
    }

    $("#contas_selecionadas").val(conta_filtro);
}

function limpa_contas_selecionadas() {
    $("#exibe_conta").val('');

    $.ajax({
        type: 'post',
        url: 'gera_secao_limpa_conta.php',
        data: {limpa: "S"},
        success: function(data) {
            location.href='form_contas_pagar.php';
        },
    });
}

function consultar_ctp() {
    // Reset do filtro de card ao fazer nova consulta
    ctpFiltroAtivo = null;
    $('#ctp-cards-container .ctp-card-total').removeClass('ativo');

    $('#btn_consultar_filtro').hide();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var tipo_data = $("#tipo_data").val();
    var razao_nome = $("#razao_nome").val();
    var codigo_fazenda = $("#codigo_fazenda").val();
    var codigo_cc = $("#codigo_cc").val();

    var aChk = document.getElementsByName("conta_option");
    var tem_conta = '';
    var j = 0;
    var conta_filtro = [];

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            tem_conta = 'S';
        }
    }

    if (tem_conta=='') {
        var array_conta= new Array();
        conta_filtro = "Conta: Todas";
    }
    else {
        var array_conta = new Array();
        var valor = new Array();

        for (var i = 0; i < aChk.length; i++) {
            if (aChk[i].checked == true) {
                valor[j] = aChk[i].value;
                j++;

                var desc = aChk[i].className;
                conta_filtro.push(desc.trim());
            }
        }
        var array_conta=valor.join(",");
    }

    conta_filtro = "Conta: " + conta_filtro;

    if (razao_nome==null) {
        var array_fornecedor= new Array();
    }
    else {
        var array_fornecedor = new Array();
        var valor = new Array();

        for (i = 0; i <= razao_nome.length; i++) {
            valor[i]=razao_nome[i];
        }

        var array_fornecedor=valor.join(",");
    }

    if (codigo_fazenda==null) {
        var array_fazenda= new Array();
    }
    else {
        var array_fazenda = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_fazenda.length; i++) {
            valor[i]=codigo_fazenda[i];
        }

        var array_fazenda=valor.join(",");
    }

    if (codigo_cc==null) {
        var array_cc= new Array();
    }
    else {
        var array_cc = new Array();
        var valor = new Array();

        for (i = 0; i <= codigo_cc.length; i++) {
            valor[i]=codigo_cc[i];
        }

        var array_cc=valor.join(",");
    }

    var options = $("#razao_nome option:selected");
    var fornecedor_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#razao_nome").text();
        fornecedor_filtro.push(desc.trim());
    });

    if (fornecedor_filtro.length > 0 && fornecedor_filtro[0] != "") {
        fornecedor_filtro = "Fornecedor: " + fornecedor_filtro + "->";
    } else {
        fornecedor_filtro = "Fornecedor: Todos->";
    }

    var options = $("#codigo_fazenda option:selected");
    var codigo_local_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_fazenda").text();
        codigo_local_filtro.push(desc.trim());
    });

    if (codigo_local_filtro != "") {
        codigo_local_filtro = "Local: " + codigo_local_filtro + "->";
    } else {
        codigo_local_filtro = "Local: Todos->";
    }

    var options = $("#codigo_cc option:selected");
    var codigo_cc_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_cc").text();
        codigo_cc_filtro.push(desc.trim());
    });

    if (codigo_cc_filtro != "") {
        codigo_cc_filtro = "C.Custos: " + codigo_cc_filtro + "->";
    } else {
        codigo_cc_filtro = "C.Custos: Todos->";
    }

    var data_ini = data_inicial.split("-");
    var dia_ini = data_ini[2];
    var mes_ini = data_ini[1];
    var ano_ini = data_ini[0];

    var data_fim = data_final.split("-");
    var dia_fim = data_fim[2];
    var mes_fim = data_fim[1];
    var ano_fim = data_fim[0];

    var periodo_label = $("#periodo_label").val();
    var datas_str = dia_ini + "/" + mes_ini + "/" + ano_ini + " ate " + dia_fim + "/" + mes_fim + "/" + ano_fim + "->";
    periodo = (periodo_label ? periodo_label + "-> " : "") + datas_str;

    if (tipo_data == "V") {
        opc_data_filtro = "Dt Vencimento->";
    } else if (tipo_data == "E") {
        opc_data_filtro = "Dt Emissão->";
    } else {
        opc_data_filtro = "Dt Pagamento->";
    }

    var descricao_filtro =
        periodo +
        opc_data_filtro +
        codigo_local_filtro +
        fornecedor_filtro +
        codigo_cc_filtro +
        conta_filtro;

    $(".digitar_filtros").hide();
    $(".filtros").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".descricao_filtro").html(descricao_filtro);
    $(".voltar").show();

    $("#exibe_conta").val(array_conta);

    $("#aguardar").modal("show");

    $('#lista_contas_pagar').load('form_lista_contas_pagar.php?data_inicial=' + data_inicial +
     '&data_final=' + data_final +
     '&tipo_data=' + tipo_data +
     '&array_fornecedor=' + array_fornecedor  +
     '&array_conta=' + array_conta  +
     '&array_fazenda=' + array_fazenda +
     '&array_cc=' + array_cc);
}

function exibe_mais_filtros() {
    $(".digitar_filtros").show();
    $(".mais_filtros").hide();
    $(".menos_filtros").show();
    $(".consultar").hide();
    $(".lista_contas").hide();
    $(".voltar").hide();
}

function exibe_menos_filtros() {
    $(".digitar_filtros").hide();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".lista_contas").show();
    $(".voltar").show();
}

function expande_tela(expandir) {
    if (expandir) {
        jQuery('#main-content').css({
            'margin-left': '0px'
        });
        jQuery('#sidebar').css({
            'margin-left': '-180px'
        });
        jQuery('#sidebar > ul').hide();
        jQuery("#container").addClass("sidebar-closed");
    }
    else {
        jQuery('#main-content').css({
            'margin-left': '180px'
        });
        jQuery('#sidebar > ul').show();
        jQuery('#sidebar').css({
            'margin-left': '0'
        });
        jQuery("#container").removeClass("sidebar-closed");
    }
}

function mais_relatorios() {
    location.href= 'form_rel_analise_pagamento.php?tipo=2';
}

function confirmar_fazendas() {
    var codigo_fazenda = $("#codigo_fazenda").val();

    if (codigo_fazenda==null) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a(s) Fazenda(s)');
        return;
    }

    if (codigo_fazenda.length==1) {
        gravar_conta();
    }
    else {
        var divisao = Math.round(100/codigo_fazenda.length);
        var soma = divisao * codigo_fazenda.length;
        var resto = 100 - soma;

        var vlr_primeira_parcela = $("#vlr_primeira_parcela").val();
        if (verifica_virgula(vlr_primeira_parcela)==',') {
            vlr_primeira_parcela = replace_valor(vlr_primeira_parcela);
        }

        vlr_primeira_parcela = parseFloat(vlr_primeira_parcela);

        var ocorrencias = $("#qtd_parcelas").val();
        var tipo_inclusao = $("input[name='tipo_inclusao']:checked").val();

        if (tipo_inclusao=='F') {
            var parcelas_restantes = $("#vlr_parcela_fixa").val();
            if (verifica_virgula(parcelas_restantes)==',') {
                parcelas_restantes = replace_valor(parcelas_restantes);
            }

            vlr_compra = (parcelas_restantes * ocorrencias);
            vlr_compra+= vlr_primeira_parcela;

        }
        else if (tipo_inclusao=='P'){
            var vlr_compra = $("#vlr_compra").val();
            if (verifica_virgula(vlr_compra)==',') {
                vlr_compra = replace_valor(vlr_compra);
            }

            var parcelas_restantes = (vlr_compra - vlr_primeira_parcela)/ocorrencias;
        }
        else {
            vlr_compra = vlr_primeira_parcela;
        }

        $(".total_compra").text('Total Compra: R$ ' + formatMoney(vlr_compra));
        $(".primeira_parcela").text('Primeira Parcela: R$ ' + formatMoney(vlr_primeira_parcela));

        if (ocorrencias!='') {
            $(".parcelas").text('Nº Parcelas Restantes: ' + ocorrencias);
            $(".vlr_parcelas").text('Valor das Parcelas: R$ ' + formatMoney(parcelas_restantes));
        }

        html = "";
        html += '<table class="table table-striped table-advance table-hover"  id="tabela_fazendas" width="100%">';
        html += '<thead>';
        html += '<tr>';
        html += '<th >' + 'Fazenda' + '</th>';
        html += '<th style="text-align: center;">' + '%' + '</th>';
        html += '<th style="text-align: right;">' + 'Primeira Parcela' + '</th>';
        html += '<th style="text-align: right;">' + 'Valor das Parcelas' + '</th>';
        html += '<th hidden="">' + ' Código' + '</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';

        var options = $('#codigo_fazenda option:selected');

        $(options).each(function(i,val){
            i = parseInt(i);
            var vlr_primeira_parcela = $("#vlr_primeira_parcela").val();

            if (verifica_virgula(vlr_primeira_parcela)==',') {
                vlr_primeira_parcela = replace_valor(vlr_primeira_parcela);
            }

            var codigo = $(this).bind('#codigo_fazenda').val();
            var desc = $(this).bind('#codigo_fazenda').text();
            html += '<tr>';
            html +="<td width='30%'>" + desc + "</td>";

            if (i==0 && resto!=0) {
                var percentual = divisao + resto;
                primeira_parcela = (vlr_primeira_parcela * percentual)/100;
                restante_parcelas = (parcelas_restantes * percentual)/100;
                html +=`<td width="16%" align="center"><input style="width: 5em" class="form-control input-sm percentual" name="percentual" id='percentual${i}' type="text" onkeypress = "return numeros(this, event)" onchange="change_percentual(this.id, this.value);" value="${percentual}"></td>`;
                html +=`<td width="27%" align="right"><input style="width: 10em" class="form-control input-sm primeira_parcela" name="primeira_parcela" id='primeira_parcela${i}' type="text" onkeypress="digita_valor_custo(${i})" onblur="exibe_primeira_parcela_custo(${i})" value="${formatMoney(primeira_parcela)}"> </td>`;
                html +=`<td width="27%" align="right"><input style="width: 10em" class="form-control input-sm parcela_restante" name="parcela_restante" id='parcela_restante${i}' type="text" onkeypress="digita_valor_custo(${i})" onblur="exibe_parcela_restante_custo(${i})" value="${formatMoney(restante_parcelas)}"> </td>`;
            }
            else {
                var percentual = divisao;
                primeira_parcela = (vlr_primeira_parcela * percentual)/100;
                restante_parcelas = (parcelas_restantes * percentual)/100;
                html +=`<td width="16%" align="center"><input style="width: 5em" class="form-control input-sm percentual" name="percentual" id='percentual${i}' type="text" onkeypress = "return numeros(this, event)" onchange="change_percentual(this.id, this.value);" value="${percentual}"></td>`;
                html +=`<td width="27%" align="right"><input style="width: 10em" class="form-control input-sm primeira_parcela" name="primeira_parcela" id='primeira_parcela${i}' type="text" onkeypress="digita_valor_custo(${i})" onblur="exibe_primeira_parcela_custo(${i})" value="${formatMoney(primeira_parcela)}"> </td>`;
                html +=`<td width="27%" align="right"><input style="width: 10em" class="form-control input-sm parcela_restante" name="parcela_restante" id='parcela_restante${i}' type="text" onkeypress="digita_valor_custo(${i})" onblur="exibe_parcela_restante_custo(${i})" value="${formatMoney(restante_parcelas)}"> </td>`;
            }
            html +="<td class='codigo_id' hidden=''>" + codigo + "</td>";
            html += '</tr>';

        });

        html += '</tbody>';
        html += '</table>';
        document.getElementById('tabela_fazendas').innerHTML = html;

        $("#modal_fazendas").modal();
    }   
}

function change_percentual(id, value) {
    $('#tabela_fazendas tbody tr').each(function(){
        var percentual = $(this).find('.percentual').val();

        if(percentual == value){
            var primeira_parcela = $("#vlr_primeira_parcela").val();
            if (verifica_virgula(primeira_parcela)==',') {
                primeira_parcela = replace_valor(primeira_parcela);
            }

            var ocorrencias = $("#qtd_parcelas").val();
            var tipo_inclusao = $("input[name='tipo_inclusao']:checked").val();

            if (tipo_inclusao=='F') {
                var parcelas_restantes = $("#vlr_parcela_fixa").val();
                if (verifica_virgula(parcelas_restantes)==',') {
                    parcelas_restantes = replace_valor(parcelas_restantes);
                }
            }
            else if (tipo_inclusao=='P'){
                var vlr_compra = $("#vlr_compra").val();
                if (verifica_virgula(vlr_compra)==',') {
                    vlr_compra = replace_valor(vlr_compra);
                }

                var parcelas_restantes = (vlr_compra - primeira_parcela)/ocorrencias;
            }

            primeira_parcela = (primeira_parcela * percentual)/100;
            restante_parcelas = (parcelas_restantes * percentual)/100;

            $(this).find('.primeira_parcela').val(formatMoney(primeira_parcela));
            $(this).find('.parcela_restante').val(formatMoney(restante_parcelas));
        }
    });
}

function gravar_conta() {
    var array_fazendas = new Array();
    var valor = new Array();
    var grupo_itens = "";
    var total_percentual = 0;
    var total_primeira_parcela = 0;
    var total_parcelas_restantes = 0;

    var vlr_primeira_parcela = $("#vlr_primeira_parcela").val();
    if (verifica_virgula(vlr_primeira_parcela)==',') {
        vlr_primeira_parcela = replace_valor(vlr_primeira_parcela);
    }

    var ocorrencias = $("#qtd_parcelas").val();
    var tipo_inclusao = $("input[name='tipo_inclusao']:checked").val();

    if (tipo_inclusao=='F') {
        var parcelas_restantes = $("#vlr_parcela_fixa").val();
        if (verifica_virgula(parcelas_restantes)==',') {
            parcelas_restantes = replace_valor(parcelas_restantes);
        }
    }
    else if (tipo_inclusao=='P'){
        var vlr_compra = $("#vlr_compra").val();
        if (verifica_virgula(vlr_compra)==',') {
            vlr_compra = replace_valor(vlr_compra);
        }
            var parcelas_restantes = (vlr_compra - vlr_primeira_parcela)/ocorrencias;
    }

    $('#tabela_fazendas tbody tr').each(function(){
        for (i = 0; i <= 3; i++) {
            valor[i]=0;
        }

        var codigo = $(this).find('.codigo_id').html();
        var percentual = $(this).find('.percentual').val();

        if (percentual!='') {
            total_percentual+= parseFloat(percentual);
        }

        var primeira_parcela = $(this).find('.primeira_parcela').val();
        var parcela_restante = $(this).find('.parcela_restante').val();

        if (verifica_virgula(primeira_parcela)==',') {
            primeira_parcela = replace_valor(primeira_parcela);
        }

        //primeira_parcela = parseFloat(primeira_parcela);

        total_primeira_parcela+= primeira_parcela*1;

        if (verifica_virgula(parcela_restante)==',') {
            parcela_restante = replace_valor(parcela_restante);
        }

        total_parcelas_restantes+= parseFloat(parcela_restante);

        if (codigo!=undefined && codigo!=0){
            valor[0]=codigo;
            valor[1]=percentual;
            valor[2]=primeira_parcela;
            valor[3]=parcela_restante;

            var tabela_itens=valor.join("|");
            array_fazendas.push(tabela_itens);
            grupo_itens=array_fazendas.join("<|>");
        }
    });

    if (total_percentual!=100 && total_percentual!=0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Total do Percentual das Fazendas inválido.');
        return;
    }

    if (total_primeira_parcela!=vlr_primeira_parcela && total_primeira_parcela!=0 && total_percentual==0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Total da Primeira Parcela das Fazendas inválido.');
        return;
    }

    if (total_parcelas_restantes!=parcelas_restantes && total_parcelas_restantes!=0 && total_percentual==0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Total do Valor das Parcelas das Fazendas inválido.');
        return;
    }

    $("#array_fazendas").val(grupo_itens);
    var dados = $('#form_gravar_contas_pagar').serialize();

    $(".confirmar_gravar").attr("disabled", true);

    $.ajax({
        type: "POST",
        url: 'gravar_contas_pagar.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $(".confirmar_gravar").attr("disabled", false);
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else {
                $(".confirmar_gravar").attr("disabled", false);
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        }
    });
}

function aceite_sair(){
    location.href='form_contas_pagar.php'
}

function voltar_filtro() {
    location.href='form_rel_analise_pagamento.php';
}

function voltar_relatorios(){
    location.href='form_relatorios_financeiros.php'
}


function confirmar_aceite() {
    var aChk = document.getElementsByName("id_ctp");
    
    var tem_conta= "";
    
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            tem_conta="S";
        }
    }
    if (tem_conta=="") {
        alert ('Não existe contas selecionadas para o aceite.');
        return;
    }
    
    var contas = new Array();
    var grupo_contas = "";
    var aChk = document.getElementsByName("id_ctp");

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            chave_ctp = aChk[i].value;
            contas.push(chave_ctp);
            grupo_contas = contas.join("<|>");
        }
    }

    $.post("aceite_contas_pagar_selecionadas.php", {grupo_contas: grupo_contas}, function (get_retorno) {
            if (get_retorno != 0) {
            }
            else {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html('Aceite efetuado com sucesso.');
            }
        });
}

function fechar_aceite_sucesso() {
    document.location.href = "form_contas_pagar_aceite.php";
}

$('#modal_baixar').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var ctp_id = button.data('wctpid') // Extract info from data-* attributes
    var num_doc = button.data('wdoc')
    var num_parcela = button.data('wparcela')
    var valor = formatMoney(button.data('wvalor'))
    var vencimento = button.data('wvencimento')
    var forma_pag = button.data('wformapag')
    var modal = $(this)

    modal.find('#chave_ind').val(ctp_id)
    modal.find('#number_doc_baixar').val(num_doc)
    modal.find('#number_parcela_baixar').val(num_parcela)
    modal.find('#vlr_total_baixar').val(valor)
    modal.find('#data_pagamento_baixar').val(vencimento)
    modal.find('#codigo_forma_pagto_baixar').val(forma_pag)
})

function baixar_conta_selecionada() {
    var numero_doc=$("#number_doc_baixar").val();
    var chave = $("#chave_ind").val();
    var total_baixar=$("#vlr_total_baixar").val();
    var data_pagamento=$("#data_pagamento_baixar").val();
    var forma_pag=$("#codigo_forma_pagto_baixar").val();

    if (verifica_virgula(total_baixar)==',') {
        total_baixar = replace_valor(total_baixar);
    }

    if (forma_pag==0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Infome a forma de pagamento.');
        return;
    }

    $.post("gravar_baixa_conta_pagar_selecionada.php", {chave: chave, num_doc: numero_doc, data_pagamento: data_pagamento, forma_pag:forma_pag, total_baixar:total_baixar}, function (get_retorno) {
        if (get_retorno != 0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(get_retorno);
        }
        else {
            $("#mensagem_retorno").modal();
            $("#mensagem_retorno .modal-body").html('Baixa efetuada com sucesso');
        }
    });
}

function somar_total_para_baixar() {
    var aChk = document.getElementsByName("id_ctp");
    
    var tem_conta= "";
    
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            tem_conta="S";
        }
    }
    if (tem_conta=="") {
        $("#total_baixar").val('');
        $('#dados_baixa').hide();
        $('.confirmar_baixa_selecionados').hide();
        return;
    }
    
    var contas = new Array();
    var grupo_contas = "";
    var aChk = document.getElementsByName("id_ctp");

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            chave_ctp = aChk[i].value;
        
            contas.push(chave_ctp);
            grupo_contas = contas.join("<|>");
        }
    }

    $.post("somar_contas_pagar_selecionadas.php", {grupo_contas: grupo_contas}, function (get_retorno) {
            if (get_retorno == 999) {
                $('.dados_baixa').modal('hide');
                $('.checkbox2').each(function() {
                    this.checked = false;        
                });         
                $('#seleciona_todos_somar').each(function() {
                    this.checked = false;        
                });         
            }
            else if (get_retorno == 9) {
                alert ("ATENCAO! Selecione contas com vencimentos iguais.");
                $('.dados_baixa').modal('hide');
                $('.confirmar_baixa_selecionados').hide();

            }
            else if (get_retorno == 99) {
                alert ("ATENCAO! Selecione contas com formas de pagamentos iguais.");
                $('.dados_baixa').modal('hide');
                $('.confirmar_baixa_selecionados').hide();
            }
            else {
                var php = get_retorno.split("<|>");
                var total_baixar =formatMoney(php[0]);
                var data_vencimento =php[1];
                var forma_recebimento =php[2];
                
                $("#total_baixar").val(total_baixar);
                $("#data_pagamento").val(data_vencimento);
                $("#codigo_forma_rec").val(forma_recebimento);
                $('.confirmar_baixa_selecionados').show();
            }
        });
}

function modal_baixar() {
    $('.dados_baixa').modal('show');
}

function confirmar_programar_pagamento() {
    var aChk = document.getElementsByName("id_ctp");
    
    var tem_conta= "";
    
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            tem_conta="S";
        }
    }
    if (tem_conta=="") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Não existe contas selecionadas para o agendamento.');
        return;
    }
    
    var contas = new Array();
    var grupo_contas = "";
    var aChk = document.getElementsByName("id_ctp");

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            chave_ctp = aChk[i].value;
        
            contas.push(chave_ctp);
            grupo_contas = contas.join("<|>");
        }
    }

    var data_pagamento = $("#data_pagamento").val();
    var forma_pagamento = $("#codigo_forma_rec_age").val();
    var numero_cheque = $("#numero_cheque").val(); 
    var total_agendado = $("#total_baixar").val();
 
    if (data_pagamento==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a data para o pagamento.');
        return;
    }

    if (forma_pagamento==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a forma de pagamento.');
        return;
    }

    $.post("programar_pagamentos_contas_pagar_selecionadas.php", {grupo_contas: grupo_contas, data_pagamento: data_pagamento,
           forma_pagamento: forma_pagamento, numero_cheque: numero_cheque, total_agendado:total_agendado}, function (get_retorno) {
            if (get_retorno !=0) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(get_retorno);
            }
            else {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html('Agendamento efetuado com sucesso.');
            }
        });
}

function editar_agendamento(numero_agendamento){
    $('#consulta_contas').show();
    $('#dados_consulta').hide();
    $('#dados_agendamento').show();

    $("#numero_agendamento").val(numero_agendamento);    
    var num_agendamento = numero_agendamento;

    $.post("ler_agendamento_contas_pagar.php", {numero_agendamento: num_agendamento}, function (valor) {
        var txt = valor;
        var php = txt.split("<|>");
        
        $("#total_agendado").val(php[4]);

        $("#data_pag_agen").val(php[0]);
        $("#codigo_forma_agen").val(php[1]);
        $("#codigo_banco_agen").val(php[2]);
        $("#numero_cheque_agen").val(php[3]);
    });
}

function alterar_agendamento() {
    var numero_agendamento=$("#numero_agendamento").val();
    var data_pagamento = $("#data_pag_agen").val();
    var forma_pagamento = $("#codigo_forma_agen").val();
    var numero_cheque = $("#numero_cheque_agen").val(); 
 
    if (data_pagamento==''){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a data para o pagamento');
        return;
    }

    if (forma_pagamento==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a forma de pagamento.');
        return;
    }

    $.post("gravar_alterar_agendamento_contas_pagar.php", {numero_agendamento: numero_agendamento, data_pagamento: data_pagamento,
           forma_pagamento: forma_pagamento, numero_cheque: numero_cheque}, function (get_retorno) {
            if (get_retorno != 0) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(get_retorno);
                return;
            }
            else {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html('Alteração do Agendamento efetuada com sucesso.');
                fecha_tela_programar();
            }
        });
}

function fecha_tela_programar(){
    $('#consulta_contas').hide();
}

function listar_agendamentos(){
    $('#consulta_contas').hide();
    $('.opcoes_topo').hide();

    $('#lista_contas_pagar').load('form_lista_agendamento_contas_pagar.php');
    return;

} 

function voltar_contas_pagar(){
    location.href = "form_contas_pagar.php";
}

function baixar_contas_selecionadas(){
    var total_baixar=$("#total_baixar").val();
    var data_pagamento=$("#data_pagamento").val();
    var forma_pag=$("#codigo_forma_rec").val();

    if (total_baixar==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o valor total para baixar');
        return;
    }

    if (data_pagamento==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a data do pagamento');
        return;
    }

    if (forma_pag==0){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a forma de pagamento');
        return;
    }

    var contas = new Array();
    var grupo_contas = "";
    var aChk = document.getElementsByName("id_ctp");

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            chave_ctp = aChk[i].value;
        
            contas.push(chave_ctp);
            grupo_contas = contas.join("<|>");
        }
    }
 
    $.post("gravar_baixa_contas_pagar_selecionadas.php", {grupo_contas: grupo_contas, data_pagamento: data_pagamento, forma_pag:forma_pag}, function (get_retorno) {
        if (get_retorno != 0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(get_retorno);
        }
        else {
            $("#mensagem_retorno").modal();
            $("#mensagem_retorno .modal-body").html('Registros baixados com suscesso.');
        }
    });
}

function estornar_baixa_contas_pagar(bcp_chave_baixa) {
    var doc = bcp_chave_baixa.substring(0, 11);
    var sequencia = bcp_chave_baixa.substring(11, 21);

    if (window.confirm("Confirma o estorno dessa baixa?")) {
        $.post("estornar_baixa_contas_pagar.php", {bcp_id: doc, bcp_sequencia: sequencia}, function (get_retorno) {
            if (get_retorno == 0) {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html('Registros estornados com suscesso.');
            } else {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(get_retorno);
            }
        });
    }
}

function cancelar_contas_agendadas(){
    var contas = new Array();
    var grupo_dados = "";
    var aChk = document.getElementsByName("id_ctp");

    var tem_conta= "";
    
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            tem_conta="S";
        }
    }

    if (tem_conta=="") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione agendamento(s) para cancelar.');
        return;
    }

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            numero_agendamento = aChk[i].value;
        
            contas.push(numero_agendamento);
            grupo_contas = contas.join("<|>");
        }
    }

    if (window.confirm("Confirma o cancelamento do(s) agendamento(s) selecionado(s)?")) {
        $.post("cancelar_agendamentos_contas_pagar_selecionadas.php", {grupo_contas: grupo_contas}, function (get_retorno) {
                if (get_retorno != 0) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(get_retorno);
                }
                else {
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html('Agendamento(s) cancelado(s) com sucesso.');
                }
        });
    }    
}

function gerar_alerta(mensagem){
  return '<div class="col-md-12"><div class="alert alert-danger fade in">' + 
    '<button data-dismiss="alert" class="close close-sm" type="button">' +
      '<span aria-hidden="true">×</span>' +
    '</button>' +
    mensagem
  '</div></div>';
}

function baixar_conta_pagar() {
    var dados = $('#form_gravar_contas_pagar').serialize();
    $.ajax({
        type: "POST",
        url: 'gravar_contas_pagar.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
        }
    });
    /* inserir a regravacao aqui */

    $('#img_aguarde_baixa').show();

    tout = setTimeout('exibir_dados_baixa()', 1000);
}

function exibir_dados_baixa() {
    $('#img_aguarde_baixa').hide();
    $('#baixar_conta').show();
    $('#baixa_conta_pagar').hide();
    $('#confirma_gravar_ctp').hide();

    var data_pagamento = $("#data_vencimento").val();
    
    $('#data_pagamento').val(data_pagamento);
    
    var ctp_id = $("#ctp_id").val();

    var numero_ctp = $("#doc_editar").val();
    var parcela_ctp = $("#parcela_editar").val();

    $.post("ler_baixa_contas_pagar.php", {ctp_id: ctp_id}, function (valor) {

        var php = valor.split("<|>");

        var valor_parcela = formataValor(php[0]);
        var valor_desconto = formataValor(php[1]);
        var valor_juros = formataValor(php[2]);
        var valor_acrescimo = formataValor(php[3]);
        var valor_pago = formataValor(php[4]);
        var valor_total = 0;

        valor_total = valor_parcela - valor_desconto;
        valor_total = valor_total + valor_juros;
        valor_total = valor_total + valor_acrescimo;
        valor_apagar = valor_total - valor_pago ;

        $("#valor_pagamento").val(formatMoney(valor_apagar));

        var historico_pagamento = "Pagamento de: " + php[9];
        $('#historico').val(historico_pagamento);

        document.getElementById("historico").focus();
    });

}

function formataValor(valor) {
    if (valor == '' || valor == null) {
        novo_valor = 0;
        return novo_valor;
    } else {
        valor = valor.replace(',', '.');
        valor = parseFloat(valor);
        return valor;
    }
}

function executar_baixa_conta_pagar_individual() {
    var dadosarray = new Array();
    dadosarray[0] = $("#doc_editar").val();
    dadosarray[1] = $("#parcela_editar").val();
    dadosarray[3] = $("#data_pagamento").val();
    dadosarray[4] = replace_valor($("#valor_pagamento").val());
    dadosarray[5] = $("#codigo_cli_for").val();
    dadosarray[7] = $("#historico").val();
    dadosarray[8] = $("#nome_for").val();
    dadosarray[9] = $("#ctp_id").val();

    $.post("gravar_baixa_contas_pagar_individual.php", {dadosarray: dadosarray}, function (get_retorno) {
        if (get_retorno == 0) {
            $("#mensagem_retorno").modal();
            $("#mensagem_retorno .modal-body").html('Baixa efetuada com sucesso');
        } else {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(get_retorno);
        }
    });
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
