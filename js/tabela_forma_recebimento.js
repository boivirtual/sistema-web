/**TABELA DE RACAS*/
$(window).load(function(){
    $('#confirmar').attr("disabled", true);

	var status_gravacao =  $("#status_gravacao").val();
	var erro_mysql =  $("#status_erro").val();

	$('#modal_incluir').modal('hide');
    $('#modal_editar').modal('hide');
    $('#modal_excluir').modal('hide');

    if (status_gravacao=='I'){
    	$('#mensagem_inclusao').modal('show');
    }
    else if (status_gravacao=='A') {
	   	$('#mensagem_edicao').modal('show');
    }
    else if (status_gravacao=='EL') {
	   	$('#mensagem_enviado').modal('show');
    }
    else if (status_gravacao=='RL') {
	   	$('#mensagem_removido').modal('show');
    }
    else if (status_gravacao=='E') {

		$('#mensagem_erro').on('show.bs.modal', function (event) {
	    	var modal = $(this);
       	 	modal.find('.modal-body').text(erro_mysql);
		})
		$('#mensagem_erro').modal('show');
	}
});

function abrir_modal_incluir() {
	$('#modal_incluir').modal('show');
}

function trava_alteracao(){
    $('#confirmar').attr("disabled", true);
}

function destrava_alteracao(){
    $('#confirmar').attr("disabled", false);
}

$(document).ready(function(){
    $('#tabela_forma_rec_pag').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     true,
        "order": [[ 4, "asc" ]],
        "language": {
     // "oPaginate": {
     //   "sFirst": "Primeira",
     //   "sLast": "Última",
     //   "sNext": "Próxima",
     //   "sPrevious": "Anterior"
    // },
        "sSearch": "Busca:",
       // "lengthMenu": "Mostrando _MENU_ registros por página",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });

    $('#tipo_conta').change(function(){
        var tipo_conta = $('#tipo_conta').val();

        switch(tipo_conta) {
            case '1':
                $("#tipo_conta_corrente").show();
                $("#tipo_cartao").hide();
                $("#saldo_data").show();
                break;
            case '2':
                $('#tipo_conta_corrente').hide();
                $('#tipo_cartao').hide();
                $('#saldo_data').show();
                break;
            case '3':
                $('#tipo_conta_corrente').hide();
                $('#tipo_cartao').show();
                $('#saldo_data').hide();
                break;
            case '4':
                $('#tipo_conta_corrente').show();
                $('#tipo_cartao').hide();
                $('#saldo_data').show();
          //default:
           // alert ('nao achei');
        }
    });

});

$('#modal_editar').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var codigo = button.data('whatever') // Extract info from data-* attributes
    var descricao = button.data('descricao')
    var tipo = button.data('tipo')
    var banco = button.data('banco')
    var conta = button.data('conta')
    var agencia = button.data('agencia')
    var cartao = button.data('cartao')
    var saldo = button.data('saldo')
    saldo = parseFloat(saldo)
    saldo = saldo.toFixed(2)
    var datasaldo = button.data('datasaldo')
    var modal = $(this)

    modal.find('#codigo_tipo_editar').val(codigo)
    modal.find('#descricao_tipo_editar').val(descricao)
    modal.find('#tipo_conta_editar').val(tipo)
    modal.find('#codigo_banco_editar').val(banco)
    modal.find('#codigo_agencia_editar').val(agencia)
    modal.find('#num_conta_editar').val(conta)
    modal.find('#num_cartao_editar').val(cartao)
    modal.find("#saldo_inicial_editar").val(formatMoney(saldo));
    modal.find('#data_saldo_editar').val(datasaldo)


    var tipo_conta = modal.find('#tipo_conta_editar').val()

    switch(tipo_conta) {
        case '1':
            modal.find("#tipo_conta_corrente").show();
            modal.find("#tipo_cartao").hide();
            modal.find("#saldo_data").show();
            break;
        case '2':
            modal.find('#tipo_conta_corrente').hide();
            modal.find('#tipo_cartao').hide();
            modal.find('#saldo_data').show();
            break;
        case '3':
            modal.find('#tipo_conta_corrente').hide();
            modal.find('#tipo_cartao').show();
            modal.find('#saldo_data').hide();
            break;
        case '4':
            modal.find('#tipo_conta_corrente').show();
            modal.find('#tipo_cartao').hide();
            modal.find('#saldo_data').show();
       //default:
        // alert ('nao achei');
    }

})

$('#modal_excluir').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('whatever') // Extract info from data-* attributes
    var recipientnome = button.data('whatevernome')
    var tipo_gravacao = button.data('whatevertipo')
    var modal = $(this)
    if (tipo_gravacao==3){
        modal.find('.modal-title').text('Formas de Recebimento/Pagamento - Restaurar da lixeira ')
    }
    else if (tipo_gravacao==2){
        modal.find('.modal-title').text('Formas de Recebimento/Pagamento - Enviar para lixeira ')
    }
    modal.find('#codigo_tipo').val(recipient)
    modal.find('#descricao_tipo').val(recipientnome)
    modal.find('#tipo_gravacao').val(tipo_gravacao)
})

$('#modal_incluir').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    //var recipient = button.data('whatever') // Extract info from data-* attributes
    //var recipientnome = button.data('whatevernome')
    var modal = $(this)
    //modal.find('.modal-title').text('Edição da raça - código: ' + recipient)
    // modal.find('.modal-title').text('Edição da raça')
    // modal.find('#codigo_bancos').val(recipient)
   //  modal.find('#nome_bancos').val(recipientnome)
})

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
    $('#saldo_inicial').bind('keypress',mask.money);
    $('#saldo_inicial_editar').bind('keypress',mask.money);
}

function exibe_valor(){
    var saldo_inicial = $("#saldo_inicial").val();
    if (verifica_virgula(saldo_inicial)==',') {
        saldo_inicial = replace_valor(saldo_inicial);
    }
    $("#saldo_inicial").val(formatMoney(saldo_inicial));
}

function exibe_valor_editar(){
    var saldo_inicial = $("#saldo_inicial_editar").val();
    if (verifica_virgula(saldo_inicial)==',') {
        saldo_inicial = replace_valor(saldo_inicial);
    }
    $("#saldo_inicial_editar").val(formatMoney(saldo_inicial));
}
