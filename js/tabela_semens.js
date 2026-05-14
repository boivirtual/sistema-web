/**TABELA DE SEMENS*/
$(window).load(function(){
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
    $('#confirma_gravar_semens').click(function(){
        $("#errors").html('');

        if ($("#form_gravar_semens")[0].checkValidity()){
            var dados = $('#form_gravar_semens').serialize();

            $("#confirma_gravar_semens").attr("disabled", true);

            $.ajax({
                type: "POST",
                url: 'gravar_semens.php',
                data: dados,
                success: function(data){

                    $("#confirma_gravar_semens").attr("disabled", false);

                    $("#modal_incluir").modal('hide');
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
              
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
  
    $("#botao_incluir").click(function(){
        $("#codigo_semem").val("");
        $("#codigo_alfa").val("");
        $("#nome_semem").val("");
        $("#S").prop("checked", true);
        $("#raca_id").val("");
        $("#registro_semem").val("");
        $("#tipo_gravacao").val("");
        $("#confirma_gravar_semens").html('Confirmar Inclusão').removeClass('btn-danger').addClass('btn-primary');
        $("#modal_incluir").modal();
        $("#modal_incluir .modal-title").html('Semens - Incluir');
        $("#form_gravar_semens :input:not(button):not([type=hidden])").prop("disabled", false);
    });
  
    $(".editar_semem").click(function(){
        popular_campos($(this), 'Semens - Editar', 'Confirmar Edição', 'btn-danger', 'btn-primary', false);
        $("#tipo_gravacao").val("");
    });
  
    $(".excluir_semem").click(function(){
        popular_campos($(this), 'Semens - Excluir', 'Confirmar Exclusão', 'btn-primary', 'btn-danger', false);
        $("#tipo_gravacao").val(2);
    });
  
    $(".restaurar_semem").click(function(){
        popular_campos($(this), 'Semens - Restaurar', 'Confirmar Restauração', 'btn-primary', 'btn-danger', false);
        $("#tipo_gravacao").val(3);
    });
  
    $('#tabela_semens').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": false,
        "info":     true,
        "language": {
        "sSearch": "Busca:",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(Filtrado de _MAX_ registros no total)",
        },
        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function() {
            $('table.dataTable').css("width", "100%");
          }
    });

});

function gerar_alerta(mensagem){
  return '<div class="col-md-12"><div class="alert alert-danger fade in">' + 
    '<button data-dismiss="alert" class="close close-sm" type="button">' +
      '<span aria-hidden="true">×</span>' +
    '</button>' +
    mensagem
  '</div></div>';
}

function popular_campos(field, modal_title, button_text, remove_class, add_class, disable_inputs){
    $("#codigo_semem").val(field.data('codigo'));
    //$("#codigo_alfa").val(field.data('codigoalfa'));
    $("#nome_semem").val(field.data('nome'));
    $("#raca_id").val(field.data('raca-id'));
    $("#registro_semem").val(field.data('registro'));

    if (field.data('ativo')=='S') {
        $("#S").prop("checked", true);
        //$('#confirma_gravar_semens').show();
    }
    else {
        $("#N").prop("checked", true);
        //$('#confirma_gravar_semens').hide();
    }

  $("#modal_incluir").modal();
  $("#modal_incluir .modal-title").html(modal_title);
  $("#confirma_gravar_semens").html(button_text).removeClass(remove_class).addClass(add_class);
  $("#form_gravar_semens :input:not(button):not([type=hidden])").prop("disabled", disable_inputs);
}
