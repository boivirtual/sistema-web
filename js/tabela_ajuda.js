/**TABELA DE AJUDA*/
window.addEventListener("load", function(event) {
    var id_url = $("#codigo_selecionado").val();

    $.post("lista_programas_ajuda.php", {id_url:id_url}, function(valor){
        $("select[name=programa]").html(valor);
    });

    consultar();
});        

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

    $('#grupo').change(function(){
        var codigo_modalidade = $("#grupo").val();

        $.post("lista_descricao_padrao.php", {modalidade:codigo_modalidade}, function(valor){
            $("select[name=descricao_padrao]").html(valor);
        });
    });

    $('#apresentacao').change(function(){
        var descricao_apresentacao = $('#apresentacao').find(":selected").text();
        $('.apresentacao_estoque').html(descricao_apresentacao);
    });

    $('#unidade').change(function(){
        var descricao_unidade = $('#unidade').find(":selected").text();
        $('.apresentacao_estoque_atual').html(descricao_unidade);
    });

});

function sair_inclusao() {
    location.href='form_cadastro_ajuda.php';
}

function consultar() {
	$.post("form_lista_ajuda.php", {}, function(valor){
        $("div[id=lista_ajuda]").html(valor); 
    });
}

function editar_ajuda(array_ajuda) {
    array_ajuda = array_ajuda.split('|');

    var tipo_gravacao = 1;
    var id_ajuda = array_ajuda[0];
    var id_url = array_ajuda[1];
    var palavra = array_ajuda[2];

    location.href= "form_ajuda_incluir.php?editar=true&id_ajuda=" + id_ajuda + 
    "&codigo_url=" + id_url + "&palavra_chave=" + palavra;
}

function enviar_lixeira(array_ajuda, opcao) {
    array_ajuda = array_ajuda.split('|');
    var id_ajuda = array_ajuda[0];

    if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo. Confirmar assim mesmo?")) {
        $.ajax({
            type: "POST",
            url: 'excluir_ajuda.php',
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
    var dados = $('#form_gravar_ajuda').serialize();

    $(".confirma_gravar").attr("disabled", true);

    $.ajax({
        type: "POST",
        url: 'gravar_ajuda.php',
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
