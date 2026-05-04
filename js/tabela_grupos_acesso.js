/**TABELA DE BANCOS*/

$(window).load(function(){
    $('#confirmar').attr("disabled", true);

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
    $('#tabela_grupos_acesso').DataTable({
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
        var formID = document.getElementById("gravar_grupo");
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
    var codigo = button.data('codigo') // Extract info from data-* attributes
    var descricao = button.data('descricao')
    var grupo_array_manejo_animais = button.data('manejo_animais')
    var grupo_array_manejo_reprodutivo = button.data('manejo_reprodutivo')
    var grupo_array_suplemento_alimentar = button.data('suplemento_alimentar')
    var grupo_array_controle_sanitario = button.data('controle_sanitario')
    var grupo_array_gestao_adm = button.data('gestao_adm')
    var grupo_array_cadastro = button.data('cadastro')
    var grupo_array_parametro = button.data('parametro')
    var grupo_array_relatorios = button.data('relatorios')

    var array_manejo_animais = grupo_array_manejo_animais.split('!');
    var array_manejo_reprodutivo = grupo_array_manejo_reprodutivo.split('!');
    var array_suplemento_alimentar = grupo_array_suplemento_alimentar.split('!');
    var array_controle_sanitario = grupo_array_controle_sanitario.split('!');
    var array_gestao_adm = grupo_array_gestao_adm.split('!');
    var array_cadastro = grupo_array_cadastro.split('!');
    var array_parametro = grupo_array_parametro.split('!');
    var array_relatorios = grupo_array_relatorios.split('!');
    var modal = $(this)

    //modal.find('.modal-title').text('Edição da raça - código: ' + codigo)
    //modal.find('.modal-title').text('Edição da raça')
    modal.find('#codigo_grupo').val(codigo)
    modal.find('#descricao_grupo').val(descricao)

    if (array_manejo_animais[0]==1){modal.find("#opc101").attr("checked",true)} else {modal.find("#opc101").attr("checked",false)}
    if (array_manejo_animais[1]==1){modal.find("#opc102").attr("checked",true)} else {modal.find("#opc102").attr("checked",false)}
    if (array_manejo_animais[2]==1){modal.find("#opc103").attr("checked",true)} else {modal.find("#opc103").attr("checked",false)}
    if (array_manejo_animais[3]==1){modal.find("#opc104").attr("checked",true)} else {modal.find("#opc104").attr("checked",false)}
   // if (array_manejo_animais[4]==1){modal.find("#opc105").attr("checked",true)} else {modal.find("#opc105").attr("checked",false)}
   // if (array_manejo_animais[5]==1){modal.find("#opc106").attr("checked",true)} else {modal.find("#opc106").attr("checked",false)}

    if (array_manejo_reprodutivo[0]==1){modal.find("#opc201").attr("checked",true)} else {modal.find("#opc201").attr("checked",false)}
    if (array_manejo_reprodutivo[1]==1){modal.find("#opc202").attr("checked",true)} else {modal.find("#opc202").attr("checked",false)}
    if (array_manejo_reprodutivo[2]==1){modal.find("#opc203").attr("checked",true)} else {modal.find("#opc203").attr("checked",false)}
    if (array_manejo_reprodutivo[3]==1){modal.find("#opc204").attr("checked",true)} else {modal.find("#opc204").attr("checked",false)}

    if (array_suplemento_alimentar[0]==1){modal.find("#opc301").attr("checked",true)} else {modal.find("#opc301").attr("checked",false)}
    if (array_suplemento_alimentar[1]==1){modal.find("#opc302").attr("checked",true)} else {modal.find("#opc302").attr("checked",false)}

    if (array_controle_sanitario[0]==1){modal.find("#opc401").attr("checked",true)} else {modal.find("#opc401").attr("checked",false)}
    if (array_controle_sanitario[1]==1){modal.find("#opc402").attr("checked",true)} else {modal.find("#opc402").attr("checked",false)}

    if (array_gestao_adm[0]==1){modal.find("#opc501").attr("checked",true)} else {modal.find("#opc501").attr("checked",false)}
    if (array_gestao_adm[1]==1){modal.find("#opc502").attr("checked",true)} else {modal.find("#opc502").attr("checked",false)}
    if (array_gestao_adm[2]==1){modal.find("#opc503").attr("checked",true)} else {modal.find("#opc503").attr("checked",false)}
    if (array_gestao_adm[3]==1){modal.find("#opc504").attr("checked",true)} else {modal.find("#opc504").attr("checked",false)}
    if (array_gestao_adm[4]==1){modal.find("#opc505").attr("checked",true)} else {modal.find("#opc505").attr("checked",false)}
    if (array_gestao_adm[5]==1){modal.find("#opc506").attr("checked",true)} else {modal.find("#opc506").attr("checked",false)}
    if (array_gestao_adm[6]==1){modal.find("#opc507").attr("checked",true)} else {modal.find("#opc507").attr("checked",false)}

    if (array_cadastro[0]==1){modal.find("#opc701").attr("checked",true)} else {modal.find("#opc701").attr("checked",false)}
    if (array_cadastro[1]==1){modal.find("#opc702").attr("checked",true)} else {modal.find("#opc702").attr("checked",false)}
    if (array_cadastro[2]==1){modal.find("#opc703").attr("checked",true)} else {modal.find("#opc703").attr("checked",false)}
    if (array_cadastro[3]==1){modal.find("#opc704").attr("checked",true)} else {modal.find("#opc704").attr("checked",false)}
    if (array_cadastro[4]==1){modal.find("#opc705").attr("checked",true)} else {modal.find("#opc705").attr("checked",false)}
    if (array_cadastro[5]==1){modal.find("#opc706").attr("checked",true)} else {modal.find("#opc706").attr("checked",false)}
    if (array_cadastro[6]==1){modal.find("#opc707").attr("checked",true)} else {modal.find("#opc707").attr("checked",false)}

    if (array_parametro[0]==1){modal.find("#opc800").attr("checked",true)} else {modal.find("#opc800").attr("checked",false)}
    if (array_parametro[1]==1){modal.find("#opc801").attr("checked",true)} else {modal.find("#opc801").attr("checked",false)}
    if (array_parametro[2]==1){modal.find("#opc802").attr("checked",true)} else {modal.find("#opc802").attr("checked",false)}
    if (array_parametro[3]==1){modal.find("#opc803").attr("checked",true)} else {modal.find("#opc803").attr("checked",false)}
    if (array_parametro[4]==1){modal.find("#opc804").attr("checked",true)} else {modal.find("#opc804").attr("checked",false)}
    if (array_parametro[5]==1){modal.find("#opc805").attr("checked",true)} else {modal.find("#opc805").attr("checked",false)}
    if (array_parametro[6]==1){modal.find("#opc806").attr("checked",true)} else {modal.find("#opc806").attr("checked",false)}
    if (array_parametro[7]==1){modal.find("#opc807").attr("checked",true)} else {modal.find("#opc807").attr("checked",false)}
    if (array_parametro[8]==1){modal.find("#opc808").attr("checked",true)} else {modal.find("#opc808").attr("checked",false)}
    if (array_parametro[9]==1){modal.find("#opc809").attr("checked",true)} else {modal.find("#opc809").attr("checked",false)}
    if (array_parametro[10]==1){modal.find("#opc810").attr("checked",true)} else {modal.find("#opc810").attr("checked",false)}
    if (array_parametro[11]==1){modal.find("#opc811").attr("checked",true)} else {modal.find("#opc811").attr("checked",false)}
    if (array_parametro[12]==1){modal.find("#opc812").attr("checked",true)} else {modal.find("#opc812").attr("checked",false)}
    if (array_parametro[13]==1){modal.find("#opc813").attr("checked",true)} else {modal.find("#opc813").attr("checked",false)}
    if (array_parametro[14]==1){modal.find("#opc814").attr("checked",true)} else {modal.find("#opc814").attr("checked",false)}
    if (array_parametro[15]==1){modal.find("#opc815").attr("checked",true)} else {modal.find("#opc815").attr("checked",false)}
    if (array_parametro[16]==1){modal.find("#opc816").attr("checked",true)} else {modal.find("#opc816").attr("checked",false)}
    if (array_parametro[17]==1){modal.find("#opc817").attr("checked",true)} else {modal.find("#opc817").attr("checked",false)}
    if (array_parametro[18]==1){modal.find("#opc818").attr("checked",true)} else {modal.find("#opc818").attr("checked",false)}
    if (array_parametro[19]==1){modal.find("#opc819").attr("checked",true)} else {modal.find("#opc819").attr("checked",false)}
    if (array_parametro[20]==1){modal.find("#opc820").attr("checked",true)} else {modal.find("#opc820").attr("checked",false)}
    if (array_parametro[21]==1){modal.find("#opc821").attr("checked",true)} else {modal.find("#opc821").attr("checked",false)}
    if (array_parametro[22]==1){modal.find("#opc822").attr("checked",true)} else {modal.find("#opc822").attr("checked",false)}

    if (array_relatorios[0]==1){modal.find("#opc901").attr("checked",true)} else {modal.find("#opc901").attr("checked",false)}
    if (array_relatorios[1]==1){modal.find("#opc902").attr("checked",true)} else {modal.find("#opc902").attr("checked",false)}
    if (array_relatorios[2]==1){modal.find("#opc903").attr("checked",true)} else {modal.find("#opc903").attr("checked",false)}

})

$('#modal_excluir').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var codigo = button.data('codigo') // Extract info from data-* attributes
    var descricao = button.data('descricao')
    var tipo_gravacao = button.data('whatevertipo')
    var modal = $(this)
    if (tipo_gravacao==3){
        modal.find('.modal-title').text('Grupos de Acesso - Remover da lixeira ')
    }
    else if (tipo_gravacao==2){
        modal.find('.modal-title').text('Grupos de Acesso - Enviar para lixeira ')
    }
    //modal.find('.modal-title').text('Edição da raça - código: ' + recipient)
   // modal.find('.modal-title').text('Edição da raça')
    modal.find('#codigo_grupo').val(codigo)
    modal.find('#descricao_grupo').val(descricao)
    modal.find('#tipo_gravacao').val(tipo_gravacao)
})

$('#modal_incluir').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    //var recipient = button.data('whatever') // Extract info from data-* attributes
    //var recipientdescricao = button.data('whateverdescricao')
    var modal = $(this)
    //modal.find('.modal-title').text('Edição da raça - código: ' + recipient)
    // modal.find('.modal-title').text('Edição da raça')
    // modal.find('#codigo_grupo').val(recipient)
   //  modal.find('#descricao_grupo').val(recipientdescricao)
})

