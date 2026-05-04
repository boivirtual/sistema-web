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
    $('#tabela_plano_contas').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
          "sSearch": "Busca:",
          "zeroRecords": "Nada encontrado",
          "info": "Registros encontrados: _END_ ",
          "infoEmpty": "Nenhum registro disponível",
          "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });

    $(".fecha_editar_dados").click(function(){
        location.href='form_tabela_plano_contas.php';
    });
  
    $('.confirma_gravar_plano').click(function(){
        $("#errors").html('');

        var deb_cred = $("input[name='debito_credito']:checked").val();
        var ana_sim = $("input[name='analitico_sintetico']:checked").val();
        var descricao = $("#descricao_plano_contas").val();
        var codigo_pri = $("#codigo_pri").val();
        var codigo_seg = $("#codigo_seg").val();
        var codigo_ter = $("#codigo_ter").val();

        if (codigo_pri == '' && codigo_seg == '' && codigo_ter == ''){
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe pelo menos um Nível de Código.');
            return;
        }


        if (descricao == ''){
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe a Descrição da Conta.');
            return;
        }

        if (deb_cred == undefined){
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe Opções de Conta, Débito ou Crédito.');
            return;
        }

        if (ana_sim == undefined){
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe Opções de Conta, Analítico ou Sintético.');
            return;
        }

        var dados = $('#form_gravar_plano_contas').serialize();
        $.ajax({
            type: "POST",
            url: 'gravar_plano_contas.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    if (data.error==true) {
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html(data.message);
                    }
                    else {
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html(data.error);
                    }
                }
                else {
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }
        });
    });

    $('#selecione_pri').change(function(){
        var codigo_nivel_pri = Number.parseInt($('#selecione_pri').val());

        $("#codigo_pri").val(codigo_nivel_pri);
        var codigo_pri=0;

        $.post("lista_plano_contas.php", {codigo_pri: codigo_pri, codigo_pla: codigo_nivel_pri, nivel: 2},
            function(valor){
            $("select[name=selecione_seg]").html(valor); 
        });

        $('#nivel_seg').show();

    });

    $('#codigo_pri').click(function(){
            $('#nivel_seg').hide();
            $('#nivel_ter').hide();
            $("#selecione_pri").val('0');
            $("#codigo_seg").val('');
            $("#codigo_ter").val('');
    });

    $('#selecione_seg').change(function(){
        var codigo_nivel_seg = $('#selecione_seg').val();
    
        $("#codigo_seg").val(codigo_nivel_seg);
        
        codigo_nivel_seg = Number.parseInt(codigo_nivel_seg);
        var codigo_pri = Number.parseInt($("#codigo_pri").val());

        $.post("lista_plano_contas.php", {codigo_pri: codigo_pri, codigo_pla: codigo_nivel_seg, nivel: 3},
            function(valor){

            $("div[name=select_ter]").html(valor); 

            var codigo_ultimo = $("#codigo_ultimo").val();
            $("#codigo_ter").val(codigo_ultimo);
        });

        $('#nivel_ter').show();
    });

    $('#codigo_seg').click(function(){
            $('#nivel_ter').hide();
            $("#selecione_seg").val('00');
            $("#codigo_seg").val('');
            $("#codigo_ter").val('');
    });

});


$('#modal_editar').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('whatever') // Extract info from data-* attributes
    var recipientnome = button.data('whatevernome')
    var debito_credito = button.data('whateverdeb_cre')
    var fixa_variavel = button.data('whateverfix_var')
    var analitico_sintetico = button.data('whateverana_sin')
    var modal = $(this)
    //modal.find('.modal-title').text('Edição da raça - código: ' + recipient)
   //modal.find('.modal-title').text('Edição da raça')
    modal.find('#codigo_plano_contas').val(recipient)
    modal.find('#descricao_plano_contas').val(recipientnome)


    if (debito_credito=="D"){
         modal.find("#debito").prop("checked",true);
    }
    else {
         modal.find("#credito").prop("checked",true);
    }

    if (fixa_variavel=="F"){
         modal.find("#fixa").prop("checked",true);
    }
    else {
         modal.find("#variavel").prop("checked",true);
    }

    if (analitico_sintetico=="A"){
         modal.find("#analitico").prop("checked",true);
    }
    else {
         modal.find("#sintetico").prop("checked",true);
    }
})

/** chamada da rotina para enviar registro de clientes para lixeira*/
function enviar_lixeira($id,$opcao){
  
    var opcao = $opcao;

    switch (opcao) {
    case 0:
        if (window.confirm("Confirma enviar esse registro para lixeira?" + " " + $id)) {     
            $.post("excluir_plano_contas.php",{id: $id, opcao: opcao}, function(valor){
         
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
    case 1:
        if (window.confirm("Confirma excluir esse registro permanentemente?" + " " + $id)) {     
            location.href= "excluir_plano_contas.php?id=" + $id + "&opcao=" + opcao;
        }
        break;
    case 2:   
        if (window.confirm("Confirma restaurar esse registro da lixeira?" + " " + $id)) {     
            $.post("excluir_plano_contas.php",{id: $id, opcao: opcao}, function(valor){
         
                var txt = valor;
                var php = txt.split("<|>");

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
