/**EMPRESAS*/

$(window).load(function(){

});

/** chamada da rotina para enviar registro de clientes para lixeira*/
function enviar_lixeira($id,$opcao,$nome){
  
    var opcao = $opcao;

	switch (opcao) {
    case 0:
		if (window.confirm("Confirma enviar esse registro para lixeira?" + " " + $nome)) {     
            $.post("excluir_empresas.php",{id: $id, opcao: opcao}, function(valor){
         
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
		if (window.confirm("Confirma excluir esse registro permanentemente?" + " " + $nome)) {     
	        location.href= "excluir_empresas.php?id=" + $id + "&opcao=" + opcao;
	  	}
        break;
    case 2:   
		if (window.confirm("Confirma restaurar esse registro da lixeira?" + " " + $nome)) {     
            $.post("excluir_empresas.php",{id: $id, opcao: opcao}, function(valor){
         
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

function recarregar_tela() {
    location.href= "form_pessoas.php";
}

$(document).ready(function(){
    $('#tabela_empresa').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        "scrollY":        "300px",
        "scrollCollapse": true, 
        "language": {
          "sSearch": "Busca:",
          "zeroRecords": "Nada encontrado",
          "info": "Registros encontrados: _END_ ",
          "infoEmpty": "Nenhum registro disponível",
          "infoFiltered": "(filtrado de _MAX_ registros no total)",
       	} 
    });

    $(".fecha_editar_dados").click(function(){
        location.href='form_empresas.php';
    });
  
    $('.confirma_gravar_empresa').click(function(){
        $("#errors").html('');
            var dados = $('#form_gravar_empresa').serialize();
            $(".confirma_gravar_empresa").attr("disabled", true);

            $.ajax({
                type: "POST",
                url: 'gravar_empresas.php',
                data: dados,
                success: function(data){
                    if (data.error) {
                        if (data.error==true) {
                            $(".confirma_gravar_empresa").attr("disabled", false);
                            $("#mensagem_erro").modal();
                            $("#mensagem_erro .modal-body").html(data.message);
                        }
                        else {
                            $(".confirma_gravar_empresa").attr("disabled", false);
                            $("#mensagem_erro").modal();
                            $("#mensagem_erro .modal-body").html(data.error);
                        }
                    }
                    else {
                        $(".confirma_gravar_empresa").attr("disabled", false);
                        $("#mensagem_retorno").modal();
                        $("#mensagem_retorno .modal-body").html(data.message);
                    }
                }
            });
    });

    $("select[name=estado_pessoa]").change(function(){
         $("select[name=lista_municipio]").html('<option value="">Aguarde...</option>');
         $("#cidade_pessoa").val("");
         var estado = $(this).val();

         $.post("lista_municipios.php", {estado:estado},
            function(valor){

            $("select[name=lista_municipio]").html(valor); });
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

$(document).ready(function() {
    $('#lista_municipio').change(function(){
        var municipio_selecioando = $('#lista_municipio').val();
        $("#cidade_pessoa").val(municipio_selecioando);
        $("#lista_municipio").val('');
    });

    function limpa_formulário_cep() {
        // Limpa valores do formulário de cep.
        $("#endereco_pessoa").val("");
        $("#numero_pessoa").val("");
        $("#complemento_pessoa").val("");
        $("#bairro_pessoa").val("");
        $("#cidade_pessoa").val("");
        $("#estado_pessoa").val("");
    }
            
    //Quando o campo cep perde o foco.
    $("#cep_pessoa").blur(function() {

    //Nova variável "cep" somente com dígitos.
        var cep = $(this).val().replace(/\D/g, '');

    //Verifica se campo cep possui valor informado.
        if (cep != "") {

    	    //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if(validacep.test(cep)) {

                //Preenche os campos com "..." enquanto consulta webservice.
                $("#endereco_pessoa").val("...");
		        $("#numero_pessoa").val("...");
		        $("#complemento_pessoa").val("...");
                $("#bairro_pessoa").val("...");
                $("#cidade_pessoa").val("...");
                $("#estado_pessoa").val("");
                //$("#ibge").val("...");

                //Consulta o webservice viacep.com.br/
                $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

	                if (!("erro" in dados)) {
	                    //Atualiza os campos com os valores da consulta.
	                    $("#endereco_pessoa").val(dados.logradouro.toUpperCase());
        				$("#numero_pessoa").val("");
       					$("#complemento_pessoa").val("");
	                    $("#bairro_pessoa").val(dados.bairro.toUpperCase());
	                    $("#cidade_pessoa").val(dados.localidade.toUpperCase());
	                    $("#estado_pessoa").val(dados.uf);
	                    //$("#ibge").val(dados.ibge);
				   
					    $("select[name=lista_municipio]").html('<option value="">Carregando...</option>');
					              
					    $.post("lista_municipios.php", {estado:dados.uf},
					        function(valor){ $("select[name=lista_municipio]").html(valor); 
					    });

					    $('#numero_pessoa').focus();
	                } //end if.
	                else {
	                    //CEP pesquisado não foi encontrado.
	                    limpa_formulário_cep();
	                    alert("CEP não encontrado.");
	                }
                });
            } //end if.
            else {
                //cep é inválido.
                limpa_formulário_cep();
                alert("Formato de CEP inválido.");
            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            limpa_formulário_cep();
        }
    });
});

var mask = {
     money: function() {
        var el = this
        ,exec = function(v) {
        v = v.replace(/\D/g,"");
        v = new String(Number(v));
        var len = v.length;
        if (1== len)
        v = v.replace(/(\d)/,"0.0$1");
        else if (2 == len)
        v = v.replace(/(\d)/,"0.$1");
        else if (len > 2) {
        v = v.replace(/(\d{2})$/,'.$1');
        }
        return v;
        };

        setTimeout(function(){
        el.value = exec(el.value);
        },1);
     }
}

function formatMoney(n, c, d, t) {
  c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
  return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}


function replace_valor(valor_replace){
    valor_replace = valor_replace.replace(".","");
    valor_replace =valor_replace.replace(",",".");
    return valor_replace;
}

function verifica_virgula(vlr){
    var virgula = '';

    for (i=0; i<vlr.length; i++) {
        if (vlr.charAt(i) ==',') {
            virgula = ',';
        }
    }   
    return virgula;
}

function digita_valor(){
    $('#per_max_desconto').bind('keypress',mask.money);
}

function exibe_per_desconto(){
    var per_max_desconto = $("#per_max_desconto").val();
    if (verifica_virgula(per_max_desconto)==',') {
        per_max_desconto = replace_valor(per_max_desconto);
    }
    $("#per_max_desconto").val(formatMoney(per_max_desconto));
}

