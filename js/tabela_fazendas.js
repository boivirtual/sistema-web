/**TABELA DE FAZENDAS*/

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
    $('#tabela_fazenda').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     true,
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
});

$('#modal_editar').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('whatever') // Extract info from data-* attributes
    var recipientnome = button.data('whatevernome')
    var area_total = button.data('whateverareatotal') 
    var area_total_construida = button.data('whateverareatotalconstruida') 
    var modal = $(this)
        //modal.find('.modal-title').text('Edição da raça - código: ' + recipient)
       //modal.find('.modal-title').text('Edição da raça')
    modal.find('#codigo_fazenda').val(recipient)
    modal.find('#nome_fazenda').val(recipientnome)
    modal.find('#area_total').val(area_total)
    modal.find('#area_total_construida').val(area_total_construida)
})

$('#modal_excluir').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('whatever') // Extract info from data-* attributes
    var recipientnome = button.data('whatevernome')
    var tipo_gravacao = button.data('whatevertipo')
    var modal = $(this)
    if (tipo_gravacao==3){
        modal.find('.modal-title').text('Fazendas - Remover da lixeira ')
    }
    else if (tipo_gravacao==2){
        modal.find('.modal-title').text('Fazendas - Enviar para lixeira ')
    }
        //modal.find('.modal-title').text('Edição da raça - código: ' + recipient)
       // modal.find('.modal-title').text('Edição da raça')
    modal.find('#codigo_fazenda').val(recipient)
    modal.find('#nome_fazenda').val(recipientnome)
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

