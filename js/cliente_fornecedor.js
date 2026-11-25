/**CLIENTE/FORNECEDORES*/
window.addEventListener("load", function(event) {
    var cpf_cnpj_empresa = $("#cpf_cnpj_empresa").val();
    var classe_pessoa = $("#classe_cliente").val();

    if (cpf_cnpj_empresa!=97174041604 && cpf_cnpj_empresa!=71746307668 && classe_pessoa==4){
        $('.esconde_classe').hide();
    }
    else {
        $('.esconde_classe').show();
    }

    if (classe_pessoa == 04) {
        $('#dados_meus_locais').show();
    }
    else {
        $('#dados_meus_locais').hide();
    }

    posicionar_classe_inclusao();
});

// Só na inclusão de Pessoas: a Classe fica ao lado da Razão Social; se for
// Funcionário (05) ela desce para a linha de baixo, ao lado da Fazenda.
// Mover o próprio bloco mantém o valor selecionado e os eventos do select.
function posicionar_classe_inclusao() {
    if (!$('#linha_classe_fazenda').length) {
        return;
    }

    if ($('#classe_pessoa').val() == 05) {
        $('#grupo_classe_pessoa').appendTo('#slot_classe_baixo');
        $('#linha_classe_fazenda').show();
    }
    else {
        $('#grupo_classe_pessoa').appendTo('#slot_classe_topo');
        $('#linha_classe_fazenda').hide();
    }
}

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

/** chamada da rotina para enviar registro de clientes para lixeira*/
function enviar_lixeira($id,$opcao){
  
    var opcao = $opcao;

	switch (opcao) {
    case 0:
		if (window.confirm("Confirma enviar esse registro para lixeira?" + " " + $id)) {     
            $.post("excluir_cliente_fornecedor.php",{id: $id, opcao: opcao}, function(valor){
         
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
	        location.href= "excluir_cliente_fornecedor.php?id=" + $id + "&opcao=" + opcao;
	  	}
        break;
    case 2:   
		if (window.confirm("Confirma restaurar esse registro da lixeira?" + " " + $id)) {     
            $.post("excluir_cliente_fornecedor.php",{id: $id, opcao: opcao}, function(valor){
         
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
    location.href= "form_cliente_fornecedor.php?manter=1";
}

/** LISTA DE PESSOAS COM FILTROS (form_cliente_fornecedor.php) */
function iniciar_tabela_pessoas() {
    $('#tabela_clientes').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
          "sSearch": "Busca:",
          "zeroRecords": "Nada encontrado",
          "info": "Registros encontrados: _END_ ",
          "infoEmpty": "Nenhum registro disponível",
          "infoFiltered": "(filtrado de _MAX_ registros no total)",
        },

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $('#lista_pessoas [data-toggle="tooltip"]').tooltip();
}

// Fazenda só aparece quando Funcionário (05) está entre as classes escolhidas.
function mostrar_fazenda_filtro() {
    var classes = $('#classe_filtro').val() || [];
    var tem_funcionario = classes.some(function (c) { return parseInt(c, 10) === 5; });

    if (tem_funcionario) {
        $('#div_fazenda_filtro').show();
    }
    else {
        $('#div_fazenda_filtro').hide();
        if (($('#fazenda_filtro').val() || []).length) {
            $('#fazenda_filtro').selectpicker('deselectAll');
        }
    }
}

function consultar_pessoas() {
    var classes = $('#classe_filtro').val() || [];
    var fazendas = $('#div_fazenda_filtro').is(':visible') ? ($('#fazenda_filtro').val() || []) : [];

    var ativo = [];
    if ($('#ativo_sim_filtro').is(':checked')) { ativo.push('S'); }
    if ($('#ativo_nao_filtro').is(':checked')) { ativo.push('N'); }

    // Com a lista na tela, o Consultar some; volta quando algum filtro for alterado.
    $('#btn_consultar_pessoas').hide();
    $('#lista_pessoas').html('<p class="text-center" style="padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Consultando...</p>');

    $.post("ler_lista_pessoas.php", {classe: classes.join(','), fazenda: fazendas.join(','), ativo: ativo.join(',')}, function (html) {
        $('#lista_pessoas').html(html);
        iniciar_tabela_pessoas();
    }).fail(function () {
        $('#lista_pessoas').html('');
        $('#btn_consultar_pessoas').show();
    });
}

// Filtro alterado: descarta a lista da consulta anterior e mostra o Consultar de novo.
function filtro_pessoas_alterado() {
    if ($.fn.DataTable.isDataTable('#lista_pessoas table')) {
        $('#lista_pessoas table').DataTable().destroy();
    }
    $('#lista_pessoas').html('');
    $('#btn_consultar_pessoas').show();
}

$(document).ready(function(){
    if ($('#classe_filtro').length) {
        // Mesmo comportamento dos filtros do Cadastro de Animais: só "Limpar Seleção",
        // visível apenas quando há algo selecionado; sem nada = "Todas" (title do select).
        $('.filtro_pessoas').each(function () {
            var $select = $(this);
            $select.selectpicker({ actionsBox: true, deselectAllText: 'Limpar Seleção' });

            var $actionsBox = $select.closest('.bootstrap-select').find('.bs-actionsbox');
            $actionsBox.find('.bs-select-all').remove();

            var atualizar = function () {
                if (($select.val() || []).length) { $actionsBox.show(); } else { $actionsBox.hide(); }
            };
            atualizar();
            $select.on('changed.bs.select', atualizar);
        });

        $('#classe_filtro').on('changed.bs.select', mostrar_fazenda_filtro);
        mostrar_fazenda_filtro();

        // Voltando da edição/inclusão: refaz a última consulta.
        if ($('#pessoas_consultou').val() == 1) {
            consultar_pessoas();
        }
    }

    $(".fecha_editar_dados").click(function(){
        var voltar = $("#voltar").val();

        if (voltar == 0) {
            location.href='form_cliente_fornecedor.php?manter=1';
        }
        else if (voltar == 1) {
            location.href='form_contas_receber_incluir.php';
        }
        else if (voltar == 2) {
            location.href='form_contas_receber_editar.php';
        }
        else if (voltar == 3) {
            location.href='form_contas_pagar_incluir.php';
        }
        else if (voltar == 4) {
            location.href='form_contas_pagar_editar.php';
        }
        else if (voltar == 5 || voltar == 6) {
            location.href='form_movimentacao_animais_incluir.php';
        }
    });
  
    $(".fecha_editar_dados_incluidos").click(function(){
        var voltar = $("#voltar").val();

        if (voltar == 0) {
            location.href='form_cliente_fornecedor_incluir.php';
        }
        else if (voltar == 1) {
            location.href='form_contas_receber_incluir.php';
        }
        else if (voltar == 2) {
            location.href='form_contas_receber_editar.php';
        }
        else if (voltar == 3) {
            location.href='form_contas_pagar_incluir.php';
        }
        else if (voltar == 4) {
            location.href='form_contas_pagar_editar.php';
        }
        else if (voltar == 5 || voltar == 6) {
            location.href='form_movimentacao_animais_incluir.php';
        }
    });


    $(".editar_cliente").click(function(){
    });

    $('.confirma_gravar_cliente').click(function(){
        $("#errors").html('');

        var dados = $('#form_gravar_cliente').serialize();

        $(".confirma_gravar_cliente").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: 'gravar_cliente_fornecedor.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    if (data.error==true) {
                        $(".confirma_gravar_cliente").attr("disabled", false);
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html(data.message);
                    }
                    else {
                        $(".confirma_gravar_cliente").attr("disabled", false);
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html(data.error);
                    }
                }
                else {
                    $(".confirma_gravar_cliente").attr("disabled", false);
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }
        });
    });

    $('#classe_pessoa').change(function(){
        var classe_pessoa = $('#classe_pessoa').val();

        if (classe_pessoa == 02) {
            $('#servico_fornecedor').show();
        }
        else {
            $('#servico_fornecedor').hide();
        }

        if (classe_pessoa == 01) {
            $('#tem_consultor').show();
        }
        else {
            $('#tem_consultor').hide();
        }

        if (classe_pessoa == 04) {
            $('#dados_meus_locais').show();
        }
        else {
            $('#dados_meus_locais').hide();
        }

        // Fazenda só para Funcionário (classe 05); não é obrigatória.
        if (classe_pessoa == 05) {
            $('#div_fazenda_funcionario').show();
        }
        else {
            $('#div_fazenda_funcionario').hide();
            $('#id_fazenda').val('');
        }

        posicionar_classe_inclusao();
    });

    $("select[name=estado_pessoa]").change(function(){
         $("select[name=lista_municipio]").html('<option value="">Aguarde...</option>');
         $("#cidade_pessoa").val("");
         var estado = $(this).val();

         $.post("lista_municipios.php", {estado:estado},
            function(valor){

            $("select[name=lista_municipio]").html(valor); });

        //tout = setTimeout('exibe_cidade()', 1000);
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
    //    $("#ibge").val("");
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

function monta_array_servicos(){
    var servicos = new Array();
    var array_servicos = "";
    var aChk = document.getElementsByName("grupo_servico_fornecedor");

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            codigo_servico = aChk[i].value;
            servicos.push(codigo_servico);
            array_servicos = servicos.join("!");
        }
    }

    $("#array_servico_fornecedor").val(array_servicos);
}

// PROPRIEDADES DA FAZENDA

function editar_propriedade($id_propriedade,$id_cliente){
    var codigo_propriedade=$id_propriedade;
    var codigo_cliente=$id_cliente;
    $("#botao_incluir_propriedade").hide();
    $("#tabela_propriedade").hide();
    $("#editar_propriedades").show();

    $('#editar_propriedades').load('form_propriedade_editar.php?editar=true&id_propriedade=' + codigo_propriedade + '&id_cliente=' + codigo_cliente);
}

function incluir_propriedade(){
    var codigo_cliente=$("#codigo_cliente_propriedade").val();;
    $("#botao_incluir_propriedade").hide();
    $("#tabela_propriedade").hide();
    $("#incluir_propriedades").show();

    $('#incluir_propriedades').load('form_propriedade_incluir.php?editar=true&id_cliente=' + codigo_cliente);
}

/** chamada da rotina para enviar registro de clientes para lixeira*/
function enviar_propriedade_lixeira($id_propriedade,$codigo_cliente,$nome,$opcao){
  
    var opcao = $opcao;

    switch (opcao) {
    case 0:
        if (window.confirm("Confirma enviar esse registro para lixeira?" + " " + $nome)) {     
            $.post("excluir_propriedade_cliente.php",{id_propriedade: $id_propriedade, id_cliente: $codigo_cliente, opcao: opcao}, function(valor){
         
                var php = valor.split("<|>");

                if (php[0]==9){
                    $("#mensagem_erro_propriedade").modal();
                    $("#mensagem_erro_propriedade .modal-body").html(php[1]);
                    return;
                }
                else if (php[0]==0){
                    $("#mensagem_retorno_propriedade").modal();
                    $("#mensagem_retorno_propriedade .modal-body").html(php[1]);
                    return;
                }
           });
        }
        break;
    case 2:   
        if (window.confirm("Confirma restaurar esse registro da lixeira?" + " " + $nome)) {     
            $.post("excluir_propriedade_cliente.php",{id_propriedade: $id_propriedade, id_cliente: $codigo_cliente, opcao: opcao}, function(valor){
         
                var txt = valor;
                var php = txt.split("<|>");

                if (php[0]==9){
                    $("#mensagem_erro_propriedade").modal();
                    $("#mensagem_erro_propriedade .modal-body").html(php[1]);
                    return;
                }
                else if (php[0]==0){
                    $("#mensagem_retorno_propriedade").modal();
                    $("#mensagem_retorno_propriedade .modal-body").html(php[1]);
                    return;
                }
            });
        }
        break;
    } 
}

$(document).ready(function(){

    $(".fecha_dados_editar_propriedade").click(function(){
        $("#editar_propriedades").hide();
        $("#incluir_propriedades").hide();
        $("#tabela_propriedade").show();
        $("#botao_incluir_propriedade").show();
    });

    $("select[name=estado_propriedade]").change(function(){
         $("select[name=lista_municipio_propriedade]").html('<option value="">Aguarde...</option>');
         $("#cidade_propriedade").val("");
         var estado = $(this).val();

         $.post("lista_municipios.php", {estado:estado},
            function(valor){

            $("select[name=lista_municipio_propriedade]").html(valor); });
    });


    $('.confirma_gravar_propriedade_cliente').click(function(){
        $("#errors").html('');

        var dados = $('#form_gravar_propriedade_cliente').serialize();
        $.ajax({
            type: "POST",
            url: 'gravar_propriedade_clientes.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro_propriedade").modal();
                    $("#mensagem_erro_propriedade .modal-body").html(data.message);
                }
                else {
                    $("#mensagem_retorno_propriedade").modal();
                    $("#mensagem_retorno_propriedade .modal-body").html(data.message);
                }
            }
        });
    });
});

$(document).ready(function() {
    $('#lista_municipio_propriedade').change(function(){
        var municipio_selecioando = $('#lista_municipio_propriedade').val();
        $("#cidade_propriedade").val(municipio_selecioando);
        $("#lista_municipio_propriedade").val('');
    });

    function limpa_formulario_cep_propriedade() {
        // Limpa valores do formulário de cep.
        $("#endereco_propriedade").val("");
        $("#numero_propriedade").val("");
        $("#complemento_propriedade").val("");
        $("#bairro_propriedade").val("");
        $("#cidade_propriedade").val("");
        $("#estado_propriedade").val("");
    //    $("#ibge").val("");
    }
            
    //Quando o campo cep perde o foco.
    $("#cep_propriedade").blur(function() {

    //Nova variável "cep" somente com dígitos.
        var cep = $(this).val().replace(/\D/g, '');

    //Verifica se campo cep possui valor informado.
        if (cep != "") {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if(validacep.test(cep)) {

                //Preenche os campos com "..." enquanto consulta webservice.
                $("#endereco_propriedade").val("...");
                $("#numero_propriedade").val("...");
                $("#complemento_propriedade").val("...");
                $("#bairro_propriedade").val("...");
                $("#cidade_propriedade").val("...");
                $("#estado_propriedade").val("");
                //$("#ibge").val("...");

                //Consulta o webservice viacep.com.br/
                $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                    if (!("erro" in dados)) {
                        //Atualiza os campos com os valores da consulta.
                        $("#endereco_propriedade").val(dados.logradouro.toUpperCase());
                        $("#numero_propriedade").val("");
                        $("#complemento_propriedade").val("");
                        $("#bairro_propriedade").val(dados.bairro.toUpperCase());
                        $("#cidade_propriedade").val(dados.localidade.toUpperCase());
                        $("#estado_propriedade").val(dados.uf);
                        //$("#ibge").val(dados.ibge);
                   
                        $("select[name=lista_municipio_propriedade]").html('<option value="">Carregando...</option>');
                                  
                        $.post("lista_municipios.php", {estado:dados.uf},
                            function(valor){ $("select[name=lista_municipio_propriedade]").html(valor); 
                        });

                        $('#numero_propriedade').focus();
                    } //end if.
                    else {
                        //CEP pesquisado não foi encontrado.
                        limpa_formulario_cep_propriedade();
                        alert("CEP não encontrado.");
                    }
                });
            } //end if.
            else {
                //cep é inválido.
                limpa_formulario_cep_propriedade();
                alert("Formato de CEP inválido.");
            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            limpa_formulario_cep_propriedade();
        }
    });
});

function monta_array_servicos_propriedade(){
    var servicos = new Array();
    var array_servicos = "";
    var aChk = document.getElementsByName("grupo_servico_propriedade");

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            codigo_servico = aChk[i].value;
            servicos.push(codigo_servico);
            array_servicos = servicos.join("!");
        }
    }

    $("#array_servico_propriedade").val(array_servicos);

}

function fecha_tela_editar_propriedade() {
    $("#editar_propriedades").hide();
    $("#incluir_propriedades").hide();
    $("#tabela_propriedade").show();
    $("#botao_incluir_propriedade").show();

    var codigo_cliente=$("#codigo_cliente_propriedade").val();

    location.href='form_cliente_fornecedor_editar.php?id=' + codigo_cliente;
} 

// a função principal de validação CPF e CNPJ propriedade
function validar_propriedade(obj) { // recebe um objeto
    
    var tipo_pessoa_propriedade = $("input[name='tipo_pessoa_propriedade']:checked").val();

    var s = (obj.value).replace(/\D/g,'');

    if (s==""){
        return false;
    }
    
    var tam=(s).length; // removendo os caracteres não numéricos
    if (!(tam==11 || tam==14)){ // validando o tamanho
        alert("'"+s+"' Não é um CPF ou um CNPJ válido!" ); // tamanho inválido
        document.getElementById("documento_pessoa_propriedade").value="";
        document.getElementById("documento_pessoa_propriedade").focus();
    }
    
// se for CPF

    if (tam==11){
        if (tipo_pessoa_propriedade=='F'){
            if (!validaCPF(s)){ // chama a função que valida o CPF
                alert("'"+s+"' Não é um código válido!" ); // se quiser mostrar o erro
                document.getElementById("documento_pessoa_propriedade").value="";
                document.getElementById("documento_pessoa_propriedade").focus();
                return false;
            }
            else {
            obj.value=maskCPF(s);   // se validou o CPF mascaramos corretamente
            
                if (flag_gravar=="I"){ 
                
                    var idcnpj_cpf=s;

                    if (for_cli=="C") {
                        var codigo_cliente=0;
                        var tipo="";
     
                        $.post("ler_cliente.php",{idcnpj_cpf: idcnpj_cpf, codigo_cliente: codigo_cliente, tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig= "000" + s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Cliente ja existe para esse CPF");
                                document.getElementById("documento_pessoa_propriedade").value="";
                                document.getElementById("documento_pessoa_propriedade").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else if (for_cli=="P") {

                        var codigo_fonte_pagadora=0;
                        var tipo="";
     
                        $.post("ler_fonte_pagadora.php",{idcnpj_cpf: idcnpj_cpf, 
                                                         idfontepagadora: codigo_fonte_pagadora, 
                                                         tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[2]).replace(/\D/g,''); 
                            var cnpj_cpf_dig= "000" + s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Fonte Pagadora ja existe para esse CPF");
                                document.getElementById("documento_pessoa_propriedade").value="";
                                document.getElementById("documento_pessoa_propriedade").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else if (for_cli=="F") {

                        var codigo_fornecedor=0;
                        var tipo="";
                        var numero_nota=0;
                        $.post("ler_fornecedor.php",{idcnpj_cpf: idcnpj_cpf, codigo_fornecedor: codigo_fornecedor, tipo: tipo, numero_nota:numero_nota}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig= "000" + s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Fornecedor ja existe para esse CPF");
                                document.getElementById("documento_pessoa_propriedade").value="";
                                document.getElementById("documento_pessoa_propriedade").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else if (for_cli=="T") {
                        var codigo_tra=0;
                        var tipo="";
                        $.post("ler_transportadora.php",{idcnpj_cpf: idcnpj_cpf, codigo_tra: codigo_tra, tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig= "000" + s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Transportadora ja existe para esse CPF");
                                document.getElementById("documento_pessoa_propriedade").value="";
                                document.getElementById("documento_pessoa_propriedade").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else {
                        $.post("ler_vendedor.php",{idcnpj_cpf: idcnpj_cpf}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig= "000" + s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Representente ja existe para esse CPF");
                                document.getElementById("documento_pessoa_propriedade").value="";
                                document.getElementById("documento_pessoa_propriedade").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                }
            }
        }
        else {
            alert("Código inválido para esse Tipo de pessoa");
            document.getElementById("documento_pessoa_propriedade").value="";
            document.getElementById("documento_pessoa_propriedade").focus();
            return false;
        }
    }
    
// se for CNPJ          
    if (tam==14){
        
        if (tipo_pessoa_propriedade=='J'){
            if(!validaCNPJ(s)){ // chama a função que valida o CNPJ
                alert("'"+s+"' Não é um código válido!" ); // se quiser mostrar o erro
                document.getElementById("documento_pessoa_propriedade").value="";
                document.getElementById("documento_pessoa_propriedade").focus();
                return false;           
            }
            else {
                obj.value=maskCNPJ(s);  // se validou o CNPJ mascaramos corretamente
                if (flag_gravar=="I"){ 
                
                    var idcnpj_cpf=s;
                    
                    if (for_cli=="C") {
                        var codigo_cliente=0;
                        var tipo="";
                        $.post("ler_cliente.php",{idcnpj_cpf: idcnpj_cpf, codigo_cliente: codigo_cliente, tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig=s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Cliente ja existe para esse CNPJ");
                                document.getElementById("documento_pessoa_propriedade").value="";
                                document.getElementById("documento_pessoa_propriedade").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }

                    else if (for_cli=="P") {

                        var codigo_fonte_pagadora=0;
                        var tipo="";
     
                        $.post("ler_fonte_pagadora.php",{idcnpj_cpf: idcnpj_cpf, 
                                                         idfontepagadora: codigo_fonte_pagadora, 
                                                         tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[2]).replace(/\D/g,''); 
                            var cnpj_cpf_dig=s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Fonte Pagadora ja existe para esse CNPJ");
                                document.getElementById("documento_pessoa_propriedade").value="";
                                document.getElementById("documento_pessoa_propriedade").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else if (for_cli=="F") {
                        var codigo_fornecedor=0;
                        var tipo="";
                        var numero_nota=0;
                        $.post("ler_fornecedor.php",{idcnpj_cpf: idcnpj_cpf, codigo_fornecedor: codigo_fornecedor, tipo: tipo, numero_nota:numero_nota}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig=s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Fornecedor ja existe para esse CNPJ");
                                document.getElementById("documento_pessoa_propriedade").value="";
                                document.getElementById("documento_pessoa_propriedade").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else if (for_cli=="T") {
                        var codigo_tra=0;
                        var tipo="";
                        $.post("ler_transportadora.php",{idcnpj_cpf: idcnpj_cpf, codigo_tra: codigo_tra, tipo: tipo}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig=s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Trasnportadora ja existe para esse CNPJ");
                                document.getElementById("documento_pessoa_propriedade").value="";
                                document.getElementById("documento_pessoa_propriedade").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                    }
                    else {
                        $.post("ler_vendedor.php",{idcnpj_cpf: idcnpj_cpf}, function(valor){
                            var txt = valor; 
                            var php=txt.split("<|>");
                
                            var cnpj_cpf_lido= (php[1]).replace(/\D/g,''); 
                            var cnpj_cpf_dig=s; 

                            if(cnpj_cpf_lido==cnpj_cpf_dig){
                                alert("Representante ja existe para esse CNPJ");
                                document.getElementById("documento_pessoa_propriedade").value="";
                                document.getElementById("documento_pessoa_propriedade").focus();
                                return false
                            }
                            else {
                                return true;
                            }                   
                        });
                        
                    }
                }
            }
        }
        else {
             alert("Código inválido para esse Tipo de pessoa");
             document.getElementById("documento_pessoa_propriedade").value="";
             document.getElementById("documento_pessoa_propriedade").focus();
             return false;
        }
    }
}
// fim da funcao validar()

    function digita_valor(){
        $('#area').bind('keypress',mask.money);
        $('#area_util').bind('keypress',mask.money);   
    }

    function exibe_area(){
        var area = $("#area").val();
        if (verifica_virgula(area)==',') {
            area = replace_valor(area);
        }

        $("#area").val(formatMoney(area));
    }

    function exibe_area_util(){
        var area_util = $("#area_util").val();
        if (verifica_virgula(area_util)==',') {
            area_util = replace_valor(area_util);
        }

        $("#area_util").val(formatMoney(area_util));
    }

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

    var mask2 = {
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
            else if (3 == len)
            v = v.replace(/(\d)/,"0.$1");
            else if (len > 3) {
            v = v.replace(/(\d{3})$/,'.$1');
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

    function formatMoney2(n, c, d, t) {
      c = isNaN(c = Math.abs(c)) ? 3 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
      return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    }

    function replace_valor(valor_replace){
        valor_replace = valor_replace.replace(".","");
        valor_replace = valor_replace.replace(".","");
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
