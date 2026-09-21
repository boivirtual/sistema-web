/**USUARIOS*/

$(window).load(function(){
    //$('#confirmar').attr("disabled", true);

	var status_gravacao =  $("#status_gravacao").val();
	var erro_mysql =  $("#status_erro").val();

	//$('#modal_incluir').modal('hide');
    //$('#modal_editar').modal('hide');
    //$('#modal_excluir').modal('hide');
    
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

    $(".fecha_editar_dados").click(function(){
        location.href='menu.php'
    });

    $('.confirma_gravar_usuario').click(function(){
        $("#errors").html('');

        var dados = $('#form_gravar_usuario').serialize();
        $.ajax({
            type: "POST",
            url: 'gravar_alterar_usuario.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else {
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }
        });
    });

    $("select[name=estado_usuario]").change(function(){
        const sl = $("select[name=lista_municipio]");
        sl.html('<option value="">Aguarde...</option>');
        $("#cidade_usuario").val("");
        var estado = $(this).val();
        var bd = $("#banco").val();

        $.ajax({
            type: "GET",
            url: "api/rest/municipio/index",
            data:{
                "uf": estado,
                "bd": bd
            },
            success: function(data){
                const arr = JSON.parse(data);
                if(!arr.error){
                    sl.html('<option value="">...</option>');
                    arr.forEach(function(i){
                        const obj = JSON.parse(i);
                        sl.append(`<option value="${obj.id}">${obj.nome}</option>`);
                    });
                }else
                    sl.html('<option value="">...</option>');
            }
        });
    });

    $('#lista_municipio').change(function(){
        var municipio_selecioando = $('#lista_municipio').val();
        $("#cidade_usuario").val(municipio_selecioando);
        $("#lista_municipio").val('');
    });

    $('#file').change(function(){
        $(".confirma_foto").show();
    });


    $("#cep_usuario").blur(function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep != "") {
            var validacep = /^[0-9]{8}$/;
            if(validacep.test(cep)) {
                $("#endereco_usuario").val("...");
                $("#numero_usuario").val("...");
                $("#complemento_usuario").val("...");
                $("#bairro_usuario").val("...");
                $("#cidade_usuario").val("...");
                $("#estado_usuario").val("");

                $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                    if (!("erro" in dados)) {
                        $("#endereco_usuario").val(dados.logradouro.toUpperCase());
                        $("#numero_usuario").val("");
                        $("#complemento_usuario").val("");
                        $("#bairro_usuario").val(dados.bairro.toUpperCase());
                        $("#cidade_usuario").val(dados.localidade.toUpperCase());
                        $("#estado_usuario").val(dados.uf);
                   
                        $("select[name=lista_municipio]").html('<option value="">Carregando...</option>');
                                  
                        $.post("lista_municipios.php", {estado:dados.uf},
                            function(valor){ $("select[name=lista_municipio]").html(valor); 
                        });

                        $('#numero_usuario').focus();
                    } 
                    else {
                        limpa_formulário_cep();
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html('CEP não encontrado.');
                        return;
                    }
                });
            } 
            else {
                limpa_formulário_cep();
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('CEP não encontrado.');
            }
        } 
        else {
            limpa_formulário_cep();
        }
    });

});

function limpa_formulário_cep() {
    $("#endereco_usuario").val("");
    $("#numero_usuario").val("");
    $("#complemento_usuario").val("");
    $("#bairro_usuario").val("");
    $("#cidade_usuario").val("");
    $("#estado_usuario").val("");
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

function editUser(){
    let cd = $("input[name='codigo_usuario']").val();
    $.ajax({
        url: "api/rest/usuario/edit.php",
        type: "PATCH",
        data: $("#form_gravar_usuario").serialize(),
        success: function(d){
            var r = JSON.parse(d);
            alert(r.message);
            window.location = `form_usuario_editar.php?id=${cd}`;
        }
    });
}

