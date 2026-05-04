/**TABELA DE CONTATOS*/
$(window).load(function(){
	//$("#dados_editar").hide();

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
    $("#botao_incluir_contato").click(function(){
        $("#nome_contato").val("");
        $("#cargo_contato").val("");
        $("#codigo_contato").val("");
        $("#ddd_contato").val("");
        $("#telefone_contato").val("");
        $("#email_contato").val("");
        $("#tipo_gravacao_contato").val("");
        $("#confirma_gravar_contato_cliente").html('Confirmar Inclusão').removeClass('btn-danger').addClass('btn-primary');
        //$("#modal_incluir_contato").modal();
        $("#modal_incluir_contato .modal-title").html('Contatos - Incluir');
        $("#form_gravar_contato_cliente :input:not(button):not([type=hidden])").prop("disabled", false);
        $("#dados_editar").show();
        $("#botao_incluir_contato").hide();
        $("#tabela_contatos").hide();

    });


    $(".editar_contato").click(function(){
        $("#tipo_gravacao_contato").val("");
        popular_campos_contato($(this), 'Contatos - Editar', 'Confirmar Edição', 'btn-danger', 'btn-primary', false);
    });

    $(".excluir_contato").click(function(){
        $("#tipo_gravacao_contato").val("2");
        popular_campos_contato($(this), 'Contatos - Excluir', 'Confirmar Exclusão', 'btn-primary', 'btn-danger', false);

    });

    $(".restaurar_contato").click(function(){
        $("#tipo_gravacao_contato").val("3");
        popular_campos_contato($(this), 'Contatos - Restaurar', 'Confirmar Restauração', 'btn-primary', 'btn-danger', false);
    });

    $("#fecha_dados_editar").click(function(){
        $("#botao_incluir_contato").show();
        $("#tabela_contatos").show();
		$("#dados_editar").hide();
    });

    $('#confirma_gravar_contato_cliente').click(function(){
        $("#errors").html('');

        if ($("#form_gravar_contato_cliente")[0].checkValidity()){
            var dados = $('#form_gravar_contato_cliente').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_contato_clientes.php',
                data: dados,
                success: function(data){
                    $("#botao_incluir_contato").modal('hide');
                    $("#mensagem_retorno_contato").modal();
                    $("#mensagem_retorno_contato .modal-body").html(data.message);
              
                    if (data.error) {
                        html_error = gerar_alerta(data.message);
                        $("#errors").html(html_error);
                    }
                }
            });
        } else {
            html_error = gerar_alerta('Preencha todos os campos obrigatórios.');
            $("#errors").html(html_error);
        }
    });

});

function popular_campos_contato(field, modal_title, button_text, remove_class, add_class, disable_inputs){
    $("#codigo_contato").val(field.data('codigo_contato'));
    $("#nome_contato").val(field.data('nome_contato'));
    $("#cargo_contato").val(field.data('cargo_contato'));
    $("#email_contato").val(field.data('email_contato'));
    $("#ddd_contato").val(field.data('ddd_contato'));
    $("#telefone_contato").val(field.data('telefone_contato'));

    //$("#modal_incluir_contato").modal();
    $("#modal_incluir_contato .modal-title").html(modal_title);
    $("#confirma_gravar_contato_cliente").html(button_text).removeClass(remove_class).addClass(add_class);
    $("#form_gravar_contato_cliente :input:not(button):not([type=hidden])").prop("disabled", disable_inputs);
    $("#dados_editar").show();
    $("#botao_incluir_contato").hide();
    $("#tabela_contatos").hide();

}

function gerar_alerta(mensagem){
  return '<div class="col-md-12"><div class="alert alert-danger fade in">' + 
    '<button data-dismiss="alert" class="close close-sm" type="button">' +
      '<span aria-hidden="true">×</span>' +
    '</button>' +
    mensagem
  '</div></div>';
}



