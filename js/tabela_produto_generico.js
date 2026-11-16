/**TABELA DE DESCRICAO PADRAO DE PRODUTOS*/
window.addEventListener("load", function(event) {
    listar_produto_generico();
});

function listar_produto_generico(){
    $.post("form_lista_produto_generico.php", {}, function(valor){
        $("div[id=lista_produto_generico]").html(valor);
    });
}

$(document).ready(function(){
    $('#tabela_produto_generico').DataTable({
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

function voltar_inclusao(){
    location.href='form_tabela_produto_generico.php';
}

function incluir_novo() {
    $("#codigo_conta").val('000000000');
    $("#descricao").val('');
    $("#modalidade").val('').selectpicker('refresh');
    $("#tipo_gravacao").val(0);

    $('#modal_incluir .modal-title').html('Descrição Padrão de Produtos - Incluir');
    $('.confirma_gravar').html('Confirmar Inclusão').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    $('.voltar_inclusao').show();
    $('.voltar').hide();
    $('#modal_incluir').modal('show');
}

function editar_produto_generico(array_registro) {
    $array_conta = array_registro.split('|');
    $("#codigo_conta").val($array_conta[0]);
    $("#descricao").val($array_conta[1]);
    $("#modalidade").val($array_conta[2]).selectpicker('refresh');

    $("#tipo_gravacao").val(1);

    $('#modal_incluir .modal-title').html('Descrição Padrão de Produtos - Editar');
    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    $('.voltar_inclusao').hide();
    $('.voltar').show();

    $('#modal_incluir').modal('show');
}

function enviar_lixeira(array_registro, opcao) {
    $array_conta = array_registro.split('|');
    $("#codigo_conta").val($array_conta[0]);
    $("#descricao").val($array_conta[1]);
    $("#modalidade").val($array_conta[2]).selectpicker('refresh');

    $("#tipo_gravacao").val(opcao);

    if (opcao==2) {
        $('#modal_incluir .modal-title').html('Descrição Padrão de Produtos - Enviar para Lixeira');
        $(".confirma_gravar").html('Enviar para Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }
    else {
        $('#modal_incluir .modal-title').html('Descrição Padrão de Produtos - Remover da Lixeira');
        $(".confirma_gravar").html('Remover da Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }

    $('.confirma_gravar').show();
    $('.voltar_inclusao').hide();
    $('.voltar').show();
    $('#modal_incluir').modal('show');
}

function gravar_produto_generico() {
    var tipo_gravacao = $("#tipo_gravacao").val();
    if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_produto_generico').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_produto_generico.php',
                data: dados,
                success: function(data){
                    if (data.error) {
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html(data.message);
                    }
                    else if (data.success){
                        $("#mensagem_retorno").modal();
                        $("#mensagem_retorno .modal-body").html(data.message);
                    }
                }
            });
        }
    }
    else if (tipo_gravacao==3) {
        if (window.confirm("Confirma remover esse registro da lixeira?")) {
            var dados = $('#form_gravar_produto_generico').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_produto_generico.php',
                data: dados,
                success: function(data){
                    if (data.error) {
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html(data.message);
                    }
                    else if (data.success){
                        $("#mensagem_retorno").modal();
                        $("#mensagem_retorno .modal-body").html(data.message);
                    }
                }
            });
        }
    }
    else if (tipo_gravacao==1){
        var dados = $('#form_gravar_produto_generico').serialize();
        $.ajax({
            type: "POST",
            url: 'gravar_produto_generico.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }
        });
    }
    else {
        var dados = $('#form_gravar_produto_generico').serialize();

        $(".confirma_gravar").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: 'gravar_produto_generico.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    $(".confirma_gravar").attr("disabled", false);
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    $(".confirma_gravar").attr("disabled", false);
                    $("#mensagem_retorno_inclusao").modal();
                    $("#mensagem_retorno_inclusao .modal-body").html(data.message);
                }
            }
        });
    }
}

$(window).resize(function() {
    if (window.innerWidth <= 991)
        $(".modal-body form .tab-content #dados .row .form-group input").addClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else
        $(".modal-body form .tab-content #dados .row .form-group input").removeClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});

$(document).ready(function() {
    if (window.innerWidth <= 991)
        $(".modal-body form .tab-content #dados .row .form-group input").addClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else
        $(".modal-body form .tab-content #dados .row .form-group input").removeClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});
