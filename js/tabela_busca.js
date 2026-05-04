/**TABELA DE AJUDA*/
window.addEventListener("load", function(event) {
    //var programas = $("#programas").val();
    //programas = programas.split(',');

    /*$.each(programas, function(idx, val) {
    $('.programa option[value=' + val + ']').attr('selected', true);
    });

    $('.programa').selectpicker('refresh');*/


    //var id_url = $("#codigo_selecionado").val();

    //$.post("lista_programas_ajuda.php", {id_url:id_url}, function(valor){
        //alert (valor);

        //$("select[name=programa]").html(valor);
    //});

    consultar();
});        

function incluir_novo() {
    $("#palavras").val("");
    $("#programa").val("");
    $('.programa').selectpicker('refresh');
    $("#tipo_gravacao").val(0);

    $('#modal_incluir .modal-title').html('Busca - Incluir');
    $('.confirma_gravar').html('Confirmar Inclusão').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    $('#modal_incluir').modal('show');
    document.getElementById("palavras").focus();
}

$(document).ready(function(){
    $('#tabela_ajuda').DataTable({
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
});

function sair_inclusao() {
    location.href='form_cadastro_busca.php';
}

function consultar() {
	$.post("form_lista_busca.php", {}, function(valor){
        $("div[id=lista_ajuda]").html(valor); 
    });
}

function editar_ajuda(array_ajuda) {
    array_ajuda = array_ajuda.split('|');
    $("#codigo_id").val(array_ajuda[0]);
    $("#palavras").val(array_ajuda[2]);
    $("#palavra_anterior").val(array_ajuda[2]);

    var programas = array_ajuda[1].split(',');
    $.each(programas, function(idx, val) {
    $('.programa option[value=' + val + ']').attr('selected', true);
    });

    $('.programa').selectpicker('refresh');

    $("#tipo_gravacao").val(1);

    $('#modal_incluir .modal-title').html('Ajuda - Editar');
    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');
    $('#modal_incluir').modal('show');
}

function enviar_lixeira(array_ajuda, opcao) {
    array_ajuda = array_ajuda.split('|');
    var id_ajuda = array_ajuda[2];

    if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo. Confirmar assim mesmo?")) {
        $.ajax({
            type: "POST",
            url: 'excluir_busca.php',
            data: {
            'id_ajuda': id_ajuda
            },
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    $("#mensagem_retorno_edicao").modal();
                    $("#mensagem_retorno_edicao .modal-body").html(data.message);
                }
            }
        });
    }
}

function gravar_ajuda() {
    var dados = $('#form_gravar_busca').serialize();

    $(".confirma_gravar").attr("disabled", true);

    $.ajax({
        type: "POST",
        url: 'gravar_busca.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $(".confirma_gravar").attr("disabled", false);
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                var tipo_gravacao = $("#tipo_gravacao").val();
            
                if (tipo_gravacao==1) {
                    $(".confirma_gravar").attr("disabled", false);
                    $("#mensagem_retorno_edicao").modal();
                    $("#mensagem_retorno_edicao .modal-body").html(data.message);
                }
                else {
                    $(".confirma_gravar").attr("disabled", false);
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }
        }
    });
}
