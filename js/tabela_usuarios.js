/**TABELA DE BANCOS*/

window.addEventListener("load", function(event) {
    $.post("form_lista_usuarios.php", {},
        function(valor){ $("div[id=lista_usuarios]").html(valor); 
    });
});        

function incluir_novo() {
    $("#codigo_usuario").val("");
    $("#nome_usuario").val("");
    $("#cpf_usuario").val("");
    $("#grupo").val("");
    $("#local").val("");
    $("#ativo").prop("checked", true);

    $("#tipo_gravacao").val(0);

    $('#modal_incluir .modal-title').html('Usuário - Incluir');
    $('.confirma_gravar').html('Confirmar Inclusão').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    $('#modal_incluir').modal('show');
    document.getElementById("nome_usuario").focus();
}

function sair_inclusao() {
    location.href='form_tabela_usuarios.php';
}

function editar_animal(array_animal) {
    array_usuario = array_animal.split('|');
    $("#codigo_usuario").val(array_usuario[0]);
    $("#nome_usuario").val(array_usuario[1]);
    $("#grupo").val(array_usuario[2]);
    $("#cpf_usuario").val(array_usuario[3]);
    $("#email_usuario").val(array_usuario[4]);

    var locais = array_usuario[6].split(',');
    $.each(locais, function(idx, val) {
    $('.local option[value=' + val + ']').attr('selected', true);
    });

    $('.local').selectpicker('refresh');

    if (array_usuario[5]=='A') {
        $("#ativo").prop("checked", true);
    }
    else {
        $("#desligado").prop("checked", true);
    }
    $("#tipo_gravacao").val(1);

    $('#modal_incluir .modal-title').html('Usuário - Editar');
    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');
    $('#modal_incluir').modal('show');

}

function enviar_lixeira(array_animal, opcao) {
    array_usuario = array_animal.split('|');
    $("#codigo_usuario").val(array_usuario[0]);
    $("#nome_usuario").val(array_usuario[1]);
    $("#grupo").val(array_usuario[2]);
    $("#cpf_usuario").val(array_usuario[3]);
    $("#email_usuario").val(array_usuario[4]);
    $("#local").val(array_usuario[6]);

    if (array_usuario[5]=='A') {
        $("#ativo").prop("checked", true);
    }
    else {
        $("#desligado").prop("checked", true);
    }

    $("#tipo_gravacao").val(opcao);

    if (opcao==2) {
        $('#modal_incluir .modal-title').html('Animal - Enviar para Lixeira');
        $(".confirma_gravar").html('Enviar para Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }
    else {
        $('#modal_incluir .modal-title').html('Animal - Remover da Lixeira');
        $(".confirma_gravar").html('Remover da Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }

    $('.confirma_gravar').show();
    $('#modal_incluir').modal('show');
}

$(document).ready(function(){
    $('#tabela_usuarios').DataTable({
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
});

function gravar_usuario() {
    var tipo_gravacao = $("#tipo_gravacao").val();

    if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_usuario').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_usuario.php',
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
            var dados = $('#form_gravar_usuario').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_usuario.php',
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
    else {
        var dados = $('#form_gravar_usuario').serialize();

        $(".confirma_gravar").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: 'gravar_usuario.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    $(".confirma_gravar").attr("disabled", false);
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    $(".confirma_gravar").attr("disabled", false);
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }
        });
    }
}

function validar_cpf_usuario(obj) { 
    var s = (obj.value).replace(/\D/g,'');

    if (s==""){
        return false;
    }
    
    var tam=(s).length; // removendo os caracteres não numéricos
    if (!(tam==11)){ // validando o tamanho
        alert("'"+s+"' Não é um CPF ou um CNPJ válido!" ); // tamanho inválido
        document.getElementById("cpf_usuario").value="";
        document.getElementById("cpf_usuario").focus();
    }

    if (tam==11){
        if (!validaCPF(s)){ // chama a função que valida o CPF
            alert ("'"+s+"' Não é um código válido!");
            document.getElementById("cpf_usuario").value="";
            document.getElementById("cpf_usuario").focus();
            return false;
        }
        else {
            obj.value=maskCPF(s); 
                
            $.post("ler_cliente.php",{idcnpj_cpf: idcnpj_cpf, codigo_cliente: codigo_cliente, tipo: tipo}, function(valor){
                var php=valor.split("<|>");
                    
                var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 

                if(cnpj_cpf_lido==s){
                    alet ("Usuário já existe para esse CPF!");
                    document.getElementById("cpf_usuario").value="";
                    document.getElementById("cpf_usuario").focus();
                    return false
                }
                else {
                    return true;
                }                   
            });
        }
    }

}

function validaCPF(s) {
    var c = s.substr(0,9);
    var dv = s.substr(9,2);
    var d1 = 0;
    for (var i=0; i<9; i++) {
        d1 += c.charAt(i)*(10-i);
    }
    if (d1 == 0) return false;
    d1 = 11 - (d1 % 11);
    if (d1 > 9) d1 = 0;
    if (dv.charAt(0) != d1){
        return false;
    }
    d1 *= 2;
    for (var i = 0; i < 9; i++) {
        d1 += c.charAt(i)*(11-i);
    }
    d1 = 11 - (d1 % 11);
    if (d1 > 9) d1 = 0;
    if (dv.charAt(1) != d1){
        return false;
    }
    return true;
}


//  função que mascara o CPF
function maskCPF(CPF){
    var cpf_cnpj = CPF;
    cpf_cnpj_editado = cpf_cnpj.substring(0,3) +"."+ 
                       cpf_cnpj.substring(3,6) +"."+ 
                       cpf_cnpj.substring(6,9) +"-"+ 
                       cpf_cnpj.substring(9,11);

    return cpf_cnpj_editado;
}

