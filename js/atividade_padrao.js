/**TABELA DE ATIVIDADE PADRAO*/

$(window).load(function(){
	var status_gravacao =  $("#status_gravacao").val();
	var erro_mysql =  $("#status_erro").val();

	$('#modal_incluir').modal('hide');
    $('#modal_editar').modal('hide');
    $('#modal_excluir').modal('hide');

    if (status_gravacao=='I'){
        $(".gravar").attr("disabled", false);
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
        $("#mensagem_erro").modal('show');
        $("#mensagem_erro .modal-body").html(erro_mysql);
	}
});

function abrir_modal_incluir() {
	$('#modal_incluir').modal('show');
}

$(document).ready(function(){
    $('#tabela_atividade_padrao').DataTable({
        responsive: true,
        paging: false,
        ordering: true,
        info: true,
        language: {
            sSearch: "Busca:",
            zeroRecords: "Nada encontrado",
            info: "Registros encontrados: _END_ ",
            infoEmpty: "Nenhum registro disponível",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
        },

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $('.gravar').on('click', function() {
        var formID = document.getElementById("gravar_atividade");
        var send = $("#botao_gravar");

        $(formID).submit(function(event){
            if (formID.checkValidity()) {
                send.attr('disabled', 'disabled');
            }
        });
    });

});

$('#modal_editar').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('whatever') // Extract info from data-* attributes
    var recipientnome = button.data('whatevernome')
    var recipientcor = button.data('whatevercor')
    var modal = $(this)
    modal.find('#codigo_id').val(recipient)
    modal.find('#descricao').val(recipientnome)
    modal.find('#cor_tipo').val(recipientcor)
})

$('#modal_excluir').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('whatever') // Extract info from data-* attributes
    var recipientnome = button.data('whatevernome')
    var recipientcor = button.data('whatevercor')
    var tipo_gravacao = button.data('whatevertipo')
    var modal = $(this)
    if (tipo_gravacao==3){
        modal.find('.modal-title').text('Pelagens - Remover da lixeira ')
    }
    else if (tipo_gravacao==2){
        modal.find('.modal-title').text('Pelagens - Enviar para lixeira ')
    }
    modal.find('#codigo_id').val(recipient)
    modal.find('#descricao').val(recipientnome)
    modal.find('#cor_tipo').val(recipientcor)
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

