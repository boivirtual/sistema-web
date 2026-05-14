/**MAPA DE GADO*/
var adicionar_mais_data = '';
let autoScrollInterval;

window.addEventListener("load", function() {
    $.post("lista_local.php", {tipo:1}, function(valor){
        $("select[name=codigo_local]").html(valor);
        //$("option[value=000000000]").html("...");
        
        if ($('#codigo_local option').length == 1){
            var local = $('#codigo_local option').val();

            $("#codigo_local").val(local);
            consultar_mapa();
            initMap();
        }

        var local = $("#local_sessao").val(); 

        if (local != ''){
            $("#codigo_local").val(local);
            consultar_mapa();
        }
    });  
});


    $(document).ready(function(){

        var controle_estoque = $('#controle_estoque').val();

        if (controle_estoque=='I') {
            $('.pelagem_id').show();
            $('.qtd_animal').hide();
        }
        else {
            $('.pelagem_id').hide();
            $('.qtd_animal').show();
        }

        var local_id = $('#local_id').val();
        $('#F').prop('checked', false);
        $('#M').prop('checked', false);

        $.post('ler_parametro_nascimento.php',{local_id:local_id}, function(valor){
            var php = valor.split('<|>');

            if (php[0]!=''){
                if (php[3]!='') {
                    $('#alfa_animal').val(php[3]);
                    $('#codigo_alfa_anterior').val(php[3]);
                    $('.alfa_animal').show();
                }
                else {
                    $('#alfa_animal').val('');
                    $('#codigo_alfa_anterior').val('');
                    $('.alfa_animal').hide();
                }

                if (php[4]!='') {
                    $('#codigo_numerico_animal').val(php[4]);
                    $('#codigo_numerico_anterior').val(php[4]);
                    $('.codigo_numerico_animal').show();
                    $('.codigo_mae_animal').show();
                    $('.codigo_pai_animal').show();
                    $('#ultima_estacao').html(php[6]);
                    $('#estacao_monta_id').val(php[5]);
                    if (php[7]=='S') {
                        $(".icon_nascimentos_previstos").show();
                    }
                    else {
                        $(".icon_nascimentos_previstos").hide();
                    }
                }
                else {
                    $('#codigo_numerico_animal').val('');
                    $('#codigo_numerico_anterior').val('');
                    $('.codigo_numerico_animal').hide();
                    $('.codigo_mae_animal').hide();
                    $('.codigo_pai_animal').hide();
                }
            }
            else {
                $('#alfa_animal').val('');
                $('#codigo_alfa_anterior').val('');
                $('#codigo_numerico_animal').val('');
                $('#codigo_numerico_anterior').val('');
                $('.alfa_animal').hide();
                $('.codigo_numerico_animal').hide();
                $('.codigo_mae_animal').hide();
                $('.codigo_pai_animal').hide();
            }
        })
    })

function informacoes_uso() {
    $("#ajuda").modal();
}

function digita_valor(){
    $('#qtdProduto').bind('keypress',mask.money);
}

function exibe_qtdProduto(){
    var qtdProduto = $("#qtdProduto").val();

    if (verifica_virgula(qtdProduto)==',') {
        qtdProduto = replace_valor(qtdProduto);
    }

    $("#qtdProduto").val(formatMoney(qtdProduto));
}


function consultar_mapa(){
    var local_id = $("#codigo_local").val();

    $("div#consulta_contas").html("");
    $.ajax({
        type: 'post',
        url: 'ler_mapa_gados.php',
        data: {
            'local_id': local_id
        },
        success: function(data) {
            $("div#consulta_contas").html(data);
            $("div.esconder").show(); 

            var tipo_mapa_gado = $("#tipo_mapa_gado").val();
           
            if (tipo_mapa_gado=='T') {
                $("div#consulta_contas").show();
                $("div#map").hide();

                $("li#mapa_tabuleiro").hide();
                $("li#mapa_satelite").show();
            }
            else {
                $("div#consulta_contas").hide();
                $("div#map").show();

                $("li#mapa_tabuleiro").show();
                $("li#mapa_satelite").hide();
            }

            var totalAnimais = $("#totalAnimaisFazenda").val();
            $("label#totalAnimais").html("Total de animais: "+totalAnimais);
        }
    });
}

function mapa_tabuleiro() {
    $("div#consulta_contas").show();
    $("div#map").hide();

    $("li#mapa_tabuleiro").hide();
    $("li#mapa_satelite").show();

    var tipo_mapa = 'T';
    //$("#tipo_mapa_gado").val('T');

    $.ajax({
        type: "POST",
        url: 'marcar_tipo_mapa_sessao.php',
        data: {
            'tipo_mapa': tipo_mapa
        }
        /*,
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $('#mensagem_erro .modal-title').html('Nascimento - Mensagem');
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        }*/
    });
}

function mapa_satelite() {
    $("div#consulta_contas").hide();
    $("div#map").show();

    $("li#mapa_tabuleiro").show();
    $("li#mapa_satelite").hide();

    var tipo_mapa = 'M';
    //$("#tipo_mapa_gado").val('M');

    $.ajax({
        type: "POST",
        url: 'marcar_tipo_mapa_sessao.php',
        data: {
            'tipo_mapa': tipo_mapa
        }
        /*,
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $('#mensagem_erro .modal-title').html('Nascimento - Mensagem');
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        }*/
    });
}

function retirar_por_categoria(){
    var pasto_origem = $("#pasto_origem").val();
    var desc_pasto_origem = $("#desc_pasto_origem").val();
    var total_animais_origem = $("#totalAnimais").val();
    var dispositivo = $("#dispositivo").val();

    if (dispositivo=='D') {
        var categoria_sexo = $("#categoria_sexo_d").val();
        var qtd_destino = $("#quantidade_d").val();
        var pasto_destino = $("#novo_pasto_d").val();
        var descricao_lote = $("#descricao_lote_d").val();
        descricao_lote = descricao_lote.trim();

        var options = $('#categoria_sexo_d option:selected');
        $(options).each(function(){
            desc = $(this).bind('#categoria_sexo_d').text();
            desc_categoria_destino = desc.trim();
        });

        var options = $('#novo_pasto_d option:selected');
        $(options).each(function(){
            desc = $(this).bind('#novo_pasto_d').text();
            desc_pasto_destino = desc.trim();
        });
    }
    else {
        var categoria_sexo = $("#categoria_sexo_m").val();
        var qtd_destino = $("#quantidade_m").val();
        var pasto_destino = $("#novo_pasto_m").val();
        var descricao_lote = $("#descricao_lote_m").val();
        descricao_lote = descricao_lote.trim();

        var options = $('#categoria_sexo_m option:selected');
        $(options).each(function(){
            desc = $(this).bind('#categoria_sexo_m').text();
            desc_categoria_destino = desc.trim();
        });

        var options = $('#novo_pasto_m option:selected');
        $(options).each(function(){
            desc = $(this).bind('#novo_pasto_m').text();
            desc_pasto_destino = desc.trim();
        });
    }

    // Informações para o modal_composicao_descricao_lote
    $("#id_pasto_destino").val(pasto_destino);
    $("#desc_pasto_destino").val(desc_pasto_destino);

    if (categoria_sexo=='000000000') {
        $("#mensagem_mover_animais").modal(); 
        $("#mensagem_mover_animais .modal-body").html('Selecione a Qual Categoria.');
        return;
    }

    if (qtd_destino==0 || qtd_destino=='') {
        $("#mensagem_mover_animais").modal(); 
        $("#mensagem_mover_animais .modal-body").html('Informe a Quantidade para transferir.');
        return;
    }

    if (pasto_destino=='000000000') {
        $("#mensagem_mover_animais").modal(); 
        $("#mensagem_mover_animais .modal-body").html('Selecione o Novo Pasto.');
        return;
    }

    if (descricao_lote=='') {
        $("#mensagem_mover_animais").modal(); 
        $("#mensagem_mover_animais .modal-body").html('Informe a Descrição do Lote.');
        return;
    }

    var sexo = categoria_sexo.substr(0, 1);  

    if (sexo=='F' || sexo=='M') {
        var categoria_destino = categoria_sexo.substr(1, 3); 
        var qtd_origem = categoria_sexo.substr(4, 4);     
    }   
    else {
        var categoria_destino = categoria_sexo.substr(0, 3); 
        var qtd_origem = categoria_sexo.substr(3, 4); 
        var sexo = '';    
    } 

    if (parseInt(qtd_destino)>parseInt(qtd_origem)) {
        $("#mensagem_mover_animais").modal(); 
        $("#mensagem_mover_animais .modal-body").html('A quantidade de animais para transferir da categoria '+desc_categoria_destino+' é insuficiente.');
        return;
    }

    if (total_animais_origem==qtd_destino) {
        /*  verifica se o pasto destino esta vazio
        Se sim
        Premissa 1 - Levar todos os animais para outro pasto vazio:
                     Mover a Descrição do Lote para o Pasto Destino
                     Limpar a Descrição do Lote do Pasto Origem
                     Mover a nutrição do dia para pasto Destino 
        Se não
        Premissa 6 - Levar todos os animais para outro pasto com animais:
                     Exibir Mensagem para a Descrição do Lote do Pasto Destino
                     Limpar a Descrição do Lote do Pasto Origem
                     Mover a nutrição do dia para pasto Destino
        */

        $.ajax({
            type: 'post',
            url: 'ler_pasto_destino.php',
            data: {
                'id_destino': pasto_destino
                },
            success: function(data){
                if (data.message=='' || data.message==null) {
                    $("#pasto_destino_estava_vazio").val('S');
                }
                else {
                    $("#pasto_destino_estava_vazio").val('N');
                    /*var elem = document.getElementById("primeira_mensagem");
                    elem.style.fontWeight = 'normal';

                    var elem = document.getElementById("segunda_mensagem");
                    elem.style.color = 'red';
                    elem.style.fontWeight = 'bold';

                    $("#modal_mover_todos").modal(); 
                    $(".modal-body #primeira_mensagem").html("Mover TODOS os animais do pasto "+desc_pasto_origem+" para o pasto "+desc_pasto_destino+"?");
                    $(".modal-body #segunda_mensagem").html("Será mantida a DESCRIÇÃO DO LOTE do pasto "+desc_pasto_destino+".");
                    */
                }

                $("#modal_mover_todos").modal(); 
                $(".modal-body #primeira_mensagem").html("Mover TODOS os animais do pasto "+desc_pasto_origem+" para o pasto "+desc_pasto_destino+"?");
                //$(".modal-body #segunda_mensagem").html("");

            }
        });
    }
    else {
        $("#modal_mover_todos").modal(); 
        $(".modal-body #primeira_mensagem").html("Mover "+qtd_destino+" animais da Categoria "+desc_categoria_destino+" para o Pasto "+desc_pasto_destino+"?");
        //$(".modal-body #segunda_mensagem").html("");
    }
}

/*function confirma_mover_descricao_lote() {
    var opcao_descricao = '';

    var radios = document.getElementsByName("opcao_descricao");
    for (var i = 0; i < radios.length; i++) {
        if (radios[i].checked) {
            opcao_descricao = radios[i].value;
        }
    }
    
    if (opcao_descricao=='') {
        $(".alert_descricao_lote .negrito").html('');
        $(".alert_descricao_lote span").html('Selecione uma opção quanto a DESCRIÇÃO de lote');
        $(".alert_descricao_lote").show();
        return;
    }
    else {
        gravar_retirar_categoria();
    }
}*/

function gravar_retirar_categoria() {
    var opcao_descricao = '';

    var radios = document.getElementsByName("opcao_descricao");
    for (var i = 0; i < radios.length; i++) {
        if (radios[i].checked) {
            opcao_descricao = radios[i].value;
        }
    }

    if (opcao_descricao=='') {
        opcao_descricao=1;
    }

    var dispositivo = $("#dispositivo").val();
    var descricao_lote = $("#descricao_lote").val();

    if (dispositivo=='D') {
        var categoria_sexo = $("#categoria_sexo_d").val();
        var qtd_destino = parseInt($("#quantidade_d").val());
        var pasto_destino = $("#novo_pasto_d").val();
    }
    else {
        var categoria_sexo = $("#categoria_sexo_m").val();
        var qtd_destino = parseInt($("#quantidade_m").val());
        var pasto_destino = $("#novo_pasto_m").val();
    }

    var sexo_destino = categoria_sexo.substr(0, 1);  

    if (sexo_destino=='F' || sexo_destino=='M') {
        var categoria_destino = categoria_sexo.substr(1, 3); 
    }   
    else {
        var categoria_destino = categoria_sexo.substr(0, 3); 
        var sexo_destino = '';    
    } 

    var id_lote = $("#id_lote").val();
    var ano_lote = $("#ano_lote").val();
    var descricao_lote_1 = $("#descricao_lote_1").val();
    var descricao_lote_2 = $("#descricao_lote_2").val();
    var descricao_lote_3 = $("#descricao_lote_3").val();
    var descricao_lote_4 = $("#descricao_lote_4").val();
    var descricao_lote_5 = $("#descricao_lote_5").val();
    var descricao_lote_6 = $("#descricao_lote_6").val();
    var total_animais = parseInt($("#totalAnimais").val());
    var pasto_origem = $("#pasto_origem").val();

    $.ajax({
        type: 'post',
        url: 'remover_animais_categoria.php',
        data: {
            'pasto_origem': pasto_origem,
            'total_animais': total_animais,
            'qtde_destino': qtd_destino,
            'pasto_destino': pasto_destino,
            'categoria_destino': categoria_destino,
            'sexo_destino': sexo_destino,
            'descricao_lote': descricao_lote,
            'opcao_descricao_lote': opcao_descricao,
            'id_lote': id_lote,
            'ano_lote': ano_lote,
            'descricao_lote_1': descricao_lote_1,
            'descricao_lote_2': descricao_lote_2,
            'descricao_lote_3': descricao_lote_3,
            'descricao_lote_4': descricao_lote_4,
            'descricao_lote_5': descricao_lote_5,
            'descricao_lote_6': descricao_lote_6
            },
            success: function(data){
                if (data.error) { 
                    $('#modal_mover_todos').modal('hide');  
                    $("#mensagem_erro").modal(); 
                    $("#mensagem_erro .modal-body").html(data.message);
                    return;
                }
                else if (data.success) {
                    $('#modal_mover_todos').modal('hide');  
                    $("#desc_lote_destino").val(data.descricao_lote_pasto_destino);
                    $("#descricao_lote").val(data.descricao_lote_pasto_origem);
                    //$("#mensagem_sucesso_mover_animais").modal(); 
                    //$("#mensagem_sucesso_mover_animais .modal-body").html(data.message);
                    exibe_opcoes_desc_lote_pasto_destino();
                }
            }
    });

    //var local = $('#local_origem').val();
    //$.redirect('form_mapa_gados.php', {'mapa_local_id': local});
    //$.redirect('form_mapa_gados.php', {'mapa_local_id': local});
}

// Fecha a mesagem de sucesso na transferencia dos animais
// Abre as opções para a Descrição do Lote para o Pasto Destino
function exibe_opcoes_desc_lote_pasto_destino() {
    $('#mensagem_sucesso_mover_animais').modal('hide');  

    var desc_pasto_destino = $("#desc_pasto_destino").val();
    $(".desc_pasto").html(desc_pasto_destino);

    var desc_lote_pasto_destino = $("#desc_lote_destino").val();
    var desc_lote_pasto_origem = $("#descricao_lote").val();
    var pasto_destino_estava_vazio = $("#pasto_destino_estava_vazio").val();

    // Se o pasto Origem ficou vazio e o pasto Destino estava vazio, então não precisa fazer mais nada
    if (desc_lote_pasto_origem=='' && pasto_destino_estava_vazio=='S') {
        $("#categoria_sexo_d").val('000000000');
        $("#quantidade_d").val('');
        $("#novo_pasto_d").val('000000000');
        $("#categoria_sexo_m").val('000000000');
        $("#quantidade_m").val('');
        $("#novo_pasto_m").val('000000000');

        var pasto_origem = $("#pasto_origem").val();
        $.redirect('form_mapa_gados_movimentacao.php', {'pasto_id': pasto_origem});
    }
    else {
        $("#qual_pasto").val('destino');

        if (desc_lote_pasto_destino=='') {
            $(".desc_lote").html('');
            $(".manter_lote").hide();
            $(".novo_lote").show();
            $(".levar_lote").show();
        }
        else {
            $(".desc_lote").html(desc_lote_pasto_destino);
            $(".manter_lote").show();
            $(".novo_lote").show();
            $(".levar_lote").hide();
        }

        $(".opcoes_descricao_lote").show();
        $(".monta_descricao_lote").hide();
        $(".linha_hr").hide();

        $("#levar_lote").prop("checked", false);
        $("#manter_lote").prop("checked", false);
        $("#novo_lote").prop("checked", false);

        $('#modal_composicao_descricao_lote').modal('show');
    }
}

// Fecha a mesagem de sucesso na transferencia dos animais do Tabuleiro
// Abre as opções para a Descrição do Lote para o Pasto Destino
function exibe_opcoes_desc_lote_pasto_destino_tabuleiro() {
    $('#mensagem_sucesso_mover_animais').modal('hide');  

    var desc_pasto_destino = $("#desc_pasto_destino").val();
    $(".desc_pasto").html(desc_pasto_destino);

    var desc_lote_pasto_destino = $("#desc_lote_destino").val();
    var desc_lote_pasto_origem = $("#descricao_lote").val();
    var pasto_destino_estava_vazio = $("#pasto_destino_estava_vazio").val();

    // Se o pasto Origem ficou vazio e o pasto Destino estava vazio, então não precisa fazer mais nada
    if (desc_lote_pasto_origem=='' && pasto_destino_estava_vazio=='S') {
        consultar_mapa();
    }
    else {
        $("#qual_pasto").val('destino');

        if (desc_lote_pasto_destino=='') {
            $(".desc_lote").html('');
            $(".manter_lote").hide();
            $(".novo_lote").show();
            $(".levar_lote").show();
        }
        else {
            $(".desc_lote").html(desc_lote_pasto_destino);
            $(".manter_lote").show();
            $(".novo_lote").show();
            $(".levar_lote").hide();
        }

        $(".opcoes_descricao_lote").show();
        $(".monta_descricao_lote").hide();
        $(".linha_hr").hide();

        $("#levar_lote").prop("checked", false);
        $("#manter_lote").prop("checked", false);
        $("#novo_lote").prop("checked", false);

        $('#modal_composicao_descricao_lote').modal('show');
    }
}

// Fecha as opções para a Descrição do Lote para o Pasto Destino
// Abre as opções para a Descrição do Lote para o Pasto Origem
/*function exibe_opcoes_desc_lote_pasto_origem() {
    $('#mensagem_erro_descricao_lote').modal('hide');  

    var desc_pasto_origem = $("#desc_pasto_origem").val();
    $(".desc_pasto").html(desc_pasto_origem);

    var desc_lote_pasto_origem = $("#descricao_lote").val();
    $(".desc_lote").html(desc_lote_pasto_origem);

    if (desc_lote_pasto_origem=='') {
        $("#categoria_sexo_d").val('000000000');
        $("#quantidade_d").val('');
        $("#novo_pasto_d").val('000000000');
        $("#categoria_sexo_m").val('000000000');
        $("#quantidade_m").val('');
        $("#novo_pasto_m").val('000000000');

        var pasto_origem = $("#pasto_origem").val();
        $.redirect('form_mapa_gados_movimentacao.php', {'pasto_id': pasto_origem});
    }
    else {
        $("#qual_pasto").val('origem');
        $(".levar_lote").hide();
        $(".manter_lote").show();

        $(".opcoes_descricao_lote").show();
        $(".monta_descricao_lote").hide();

        $("#levar_lote").prop("checked", false);
        $("#manter_lote").prop("checked", false);
        $("#novo_lote").prop("checked", false);

        $('#modal_composicao_descricao_lote').modal('show');
    }
}*/

function ler_animal_morte(){
    var id_animal= $('#id_animal_morte').val();
    var local = $('#local_origem').val();
    var categoria = '';
    var sexo = '';
    var raca = '';
    var pai = '';
    var mae = '';
    var data_nasc_inicial = '';
    var data_nasc_final = '';
    var peso_nasc_inicial = '';
    var peso_nasc_final = '';
    var peso_desmama_inicial = '';
    var peso_desmama_final = '';
    var peso_ult_inicial = '';
    var peso_ult_final = '';
    var solteiras = '';
    var descarte = '';
    var paridas = '';
    var prenhes= '';
    var data_paridas = '';
    var num_parto_de = '';
    var num_parto_ate = '';
    var num_aborto_de = '';
    var num_aborto_ate = '';
    var previsao_parto_de = '';
    var previsao_parto_ate = '';
    var estacao_monta = '';
    var positivo='';
    var negativo='';

    if (id_animal.length < 5) {
        return;
    } 

    $.post("ler_animal_movimentacao_morte_outra.php", {
        id_animal:id_animal, 
        local:local, 
        categoria:categoria, 
        sexo:sexo, raca:raca, 
        pai:pai, 
        mae:mae, 
        data_nasc_inicial:data_nasc_inicial, 
        data_nasc_final:data_nasc_final, 
        peso_nasc_inicial:peso_nasc_inicial, 
        peso_nasc_final:peso_nasc_final, 
        peso_desmama_inicial:peso_desmama_inicial, 
        peso_desmama_final:peso_desmama_final,
        peso_ult_inicial:peso_ult_inicial, 
        peso_ult_final:peso_ult_final, 
        solteiras:solteiras, 
        descarte:descarte, 
        paridas:paridas, 
        prenhes:prenhes,
        data_paridas:data_paridas, 
        num_parto_de:num_parto_de, 
        num_parto_ate:num_parto_ate,
        num_aborto_de:num_aborto_de, 
        num_aborto_ate:num_aborto_ate,
        previsao_parto_de:previsao_parto_de,
        previsao_parto_ate:previsao_parto_ate,
        estacao_monta:estacao_monta,
        positivo:positivo,
        negativo:negativo

        }, function(valor){

        var php = valor.split("<|>");

        if (php[0]==9 || php[0]==999999999) {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html(php[1]);
            $(".alert_erro_animal").show();
            return;
        }
        else {
            $("#descricao_animal_morte").text(php[6]);
            $("#codigo_id_morte").val(php[0]);
            $("#sexo_animal_morte").val(php[1]);
            $("#nascimento_animal_morte").val(php[2]);
            $("#raca_animal_morte").val(php[3]);
            $("#pelagem_animal_morte").val(php[4]);
            $("#mae_animal_morte").val(php[5]);
            $("#categoria_digitada_morte").val(php[16]);
        }

        if (php[18]=='S') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Animal em Estação de Monta.');
            $(".alert_erro_animal").show();
        }
    });
}

function ler_animal_nascimento(){
    var local_id = $('#local_id').val();

    $.post('ler_parametro_nascimento.php',{local_id:local_id}, function(valor){
        var php = valor.split('<|>');

        if (php[0]!=''){
            if (php[3]!='') {
                $('#alfa_animal').val(php[3]);
                $("#codigo_alfa_anterior").val(php[3]);
                $('.alfa_animal').show();
            }
            else {
                $('#alfa_animal').val('');
                $("#codigo_alfa_anterior").val('');
                $('.alfa_animal').hide();
            }

            if (php[4]!='') {
                $('#codigo_numerico_animal').val(php[4]);
                $("#codigo_numerico_anterior").val(php[4]);
                $('.codigo_numerico_animal').show();
                $('.codigo_mae_animal').show();
                $('.codigo_pai_animal').show();
                $('#ultima_estacao').html(php[6]);
                $('#estacao_monta_id').val(php[5]);

                if (php[7]=='S') {
                    $(".icon_nascimentos_previstos").show();
                }
                else {
                    $(".icon_nascimentos_previstos").hide();
                }
            }
            else {
                $('#codigo_numerico_animal').val('');
                $("#codigo_numerico_anterior").val('');
                $('.codigo_numerico_animal').hide();
                $('.codigo_mae_animal').hide();
                $('.codigo_pai_animal').hide();
            }
        }
        else {
            $('#alfa_animal').val('');
            $("#codigo_alfa_anterior").val('');
            $('#codigo_numerico_animal').val('');
            $("#codigo_numerico_anterior").val('');
            $('.alfa_animal').hide();
            $('.codigo_numerico_animal').hide();
            $('.codigo_mae_animal').hide();
            $('.codigo_pai_animal').hide();
        }
    });

    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='I') {
        $(".ocorrencias").show();
        $(".fazenda_pasto").hide();
        $(".confirmar").hide();
        $(".campos_data_mae_pai").hide();
        $(".campos_id_aborto_lote").hide();
        $(".campos_id_lote").hide();
        $(".nascimento_id").hide();
        $("#opcao_nascimento").prop("checked", false);
        $("#opcao_absorcao").prop("checked", false);
        $("#opcao_aborto").prop("checked", false);
        $("#opcao_morte").prop("checked", false);
    }
    else {
        $(".parametros").hide();
        $(".ocorrencias").hide();
        $(".fazenda_pasto").show();
        $(".confirmar").show();
        $(".nascimento_id").hide();
        $(".qtd_animal").show();
        $(".pelagem_id").hide();
        $(".codigo_pai_animal").hide();
        $(".codigo_mae_animal").hide();
        $(".campos_data_mae_pai").show();
        $(".campos_id_aborto_lote").show();
        $(".campos_id_lote").show();
        $(".raca_id").show();
        $(".peso_animal").show();
        $(".label_data").html('* Data Nascimento');
        $(".label_pasto").html('* Pasto');
        $(".label_mae").html('* Nº Mãe');
    }

    $("#F").prop("checked", false);
    $("#M").prop("checked", false);
    $("#pelagem_id").val('');
    $("#raca_id").val('');
    $("#codigo_pai_animal").val('');
    $("#codigo_mae_animal").val('');
    $("#codigo_mae_consulta").val('');
    $("#peso_animal").val('');
    $("#qtd_animal").val('');

    $('#mensagem_retorno_inclusao').modal('hide');        
    abrir_modal_nascimento();
    return;
}

function abrir_modal_morte(){
    $("#id_animal_morte").val('');
    $("#sexo_animal_morte").val('');
    $("#nascimento_animal_morte").val('');
    $("#raca_animal_morte").val('');
    $("#pelagem_animal_morte").val('');
    $("#mae_animal_morte").val('');
    $("#observacao_morte").val('');
    $("#descricao_animal_morte").text('');
    $("#codigo_id_morte").val(0);
    $("#motivo_animal_morte").val('');
    $("#codigo_motivo_morte").val('');
    $("#motivo_morte").val('000');    
    $("#categoria_morte").val('000');   
    $("#categoria_digitada_morte").val('');   
    $("#sexo_morte").val('');   

    $("#array_itens").val('');

    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='L') {
        var local = $("#local_morte").val();
        var pasto = $("#pasto_morte").val();

        $.post("lista_categoria_pasto_morte_outra.php", {local:local, pasto:pasto}, function(valor){

            if (valor=='N') {
                $(".alert_erro_animal .negrito").html('');
                $(".alert_erro_animal span").html('Não existem animais nesse pasto.');
                $(".alert_erro_animal").show();
                return;
            }
            else {
                $("select[name=categoria_morte]").html(valor);
            }
        });
    } 

    $('#modal_morte').modal('show');
}

function abrir_modal_nascimento(){
    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='I') {
        $(".ocorrencias").show();
    }
    else {
        $(".parametros").hide();
        $(".ocorrencias").hide();
        $(".fazenda_pasto").show();
        $(".confirmar").show();
        $(".nascimento_id").hide();
        $(".qtd_animal").show();
        $(".pelagem_id").hide();
        $(".codigo_pai_animal").hide();
        $(".codigo_mae_animal").hide();
        $(".campos_data_mae_pai").show();
        $(".campos_id_aborto_lote").show();
        $(".campos_id_lote").show();
        $(".raca_id").show();
        $(".peso_animal").show();
        $(".label_data").html('* Data Nascimento');
        $(".label_pasto").html('* Pasto');
        $(".label_mae").html('* Nº Mãe');
    }

    listar_estacao();

    $('#modal_nascimento').modal('show');
}

function abrir_modal_nutricao(){
    var controle_estoque = $("#controle_estoque").val();

    $.ajax({
        type: "POST",
        url: "ler_score_cocho.php",
        success: function(data){
            $("#slctCocho").html(data);
        }
    });

    $.ajax({
        type: "POST",
        url: "ler_produto_nutricao.php",
        success: function(data){
            $("#nomeProduto").html(data);
        }
    });

    $('#modal_nutricao').modal('show');

    lerNutricao();
}

// Funcões para a montagem da descrição dos lotes de animais no pasto
function abrir_modal_descricao_lote(qual_mensagem) {

    if (qual_mensagem==1) {
        $(".voltar_descricao_lote").hide();
        $("#qual_pasto").val('destino');
    }
    else {
        $(".voltar_descricao_lote").show();
    }

    var desc_pasto_origem = $("#desc_pasto_origem").val();
    $(".desc_pasto").html(desc_pasto_origem);

    $(".desc_lote").hide();
    $(".opcoes_descricao_lote").hide();
    $(".monta_descricao_lote").show();
    $(".linha_hr").hide();

    $("#levar_lote").prop("checked", false);
    $("#manter_lote").prop("checked", false);
    $("#novo_lote").prop("checked", false);
    
    $('#modal_composicao_descricao_lote').modal('show');

    popular_descricao();

    $("#data_paricao_principal").val('');
    $("#situacao_principal").html('0');
    $("#com_data").prop("checked", false);
    $(".descricao_principal").hide();
    $(".exibir_incluir_mais").hide();
    $(".exibir_parametro_2").hide();
    $(".exibir_parametro_3").hide();
    $(".exibir_parametro_4").hide();
    $(".exibir_parametro_4_mais").hide();
    $(".exibir_parametro_4_data_mais").hide();
    $('.label_parametro_3').html('Informar Data da Parição?');
    $('.label_parametro_4').html('Mês/Ano da Parição?');
    $('.label_parametro_4_mais').html('Mês/Ano da Parição?');

    $("#descricao_novo_lote").val($("#descricao_lote_1").val());
    $("#descricao_novo_lote2").val($("#descricao_lote_2").val());
    $("#descricao_novo_lote3").val($("#descricao_lote_3").val());
    $("#descricao_novo_lote4").val($("#descricao_lote_4").val());
    $("#descricao_novo_lote5").val($("#descricao_lote_5").val());
    $("#descricao_novo_lote6").val($("#descricao_lote_6").val());

    var numero_item = 0;
    $("#edicao").val('');

    if ($("#descricao_novo_lote").val()) {
        $(".exibir_descricao").show();
        $(".exibir_opcoes").show();
        $(".exibir_incluir_mais").show();
        $("#edicao").val('S');
        numero_item++;
    }
    else {
        $(".exibir_descricao").hide();
        $(".exibir_opcoes").hide();
    }

    if ($("#descricao_novo_lote2").val()) {
        $(".exibir_descricao2").show();
        $(".exibir_opcoes2").show();
        numero_item++;
    }
    else {
        $(".exibir_descricao2").hide();
        $(".exibir_opcoes2").hide();
    }

    if ($("#descricao_novo_lote3").val()) {
        $(".exibir_descricao3").show();
        $(".exibir_opcoes3").show();
        numero_item++;
    }
    else {
        $(".exibir_descricao3").hide();
        $(".exibir_opcoes3").hide();
    }

    if ($("#descricao_novo_lote4").val()) {
        $(".exibir_descricao4").show();
        $(".exibir_opcoes4").show();
        numero_item++;
    }
    else {
        $(".exibir_descricao4").hide();
        $(".exibir_opcoes4").hide();
    }

    if ($("#descricao_novo_lote5").val()) {
        $(".exibir_descricao5").show();
        $(".exibir_opcoes5").show();
        numero_item++;
    }
    else {
        $(".exibir_descricao5").hide();
        $(".exibir_opcoes5").hide();
    }

    if ($("#descricao_novo_lote6").val()) {
        $(".exibir_descricao6").show();
        $(".exibir_opcoes6").show();
        numero_item++;
    }
    else {
        $(".exibir_descricao6").hide();
        $(".exibir_opcoes6").hide();
    }

    if (numero_item==0) {
        $(".descricao_principal").show();
        numero_item=1;
    }

    $("#numero_item").val(numero_item);
} 

function confirma_composicao_descricao_lote() {
    var descricao_id = $("#descricao_principal").val();
    var parametro_2 = $("#situacao_principal").val();
    var edicao = $("#edicao").val();
    var itens = $("#numero_item").val();

    if (edicao=='' && itens<6) {
        if (descricao_id == 0) {
            $("#mensagem_erro_descricao_lote").modal(); 
            $("#mensagem_erro_descricao_lote .modal-body").html('Selecione a Descrição do Lote');
            return;
        }

        if (parametro_2 == 0 && (descricao_id==1 || 
            descricao_id==2 || descricao_id==7 || descricao_id==8)) {
            $("#mensagem_erro_descricao_lote").modal(); 
            $("#mensagem_erro_descricao_lote .modal-body").html('Selecione a Situação.');
            $("#situacao_principal").css("border", "1px solid red");
            $("#situacao_principal").focus();            
            return;
        }

        if (parametro_2 == 0 && descricao_id==3) {
            $("#mensagem_erro_descricao_lote").modal(); 
            $("#mensagem_erro_descricao_lote .modal-body").html('Selecione o Sexo.');
            $("#situacao_principal").css("border", "1px solid red");
            $("#situacao_principal").focus();            
            return;
        }
    }

    var descricao_novo_lote = $("#descricao_novo_lote").val();
    var descricao_novo_lote2 = $("#descricao_novo_lote2").val();
    var descricao_novo_lote3 = $("#descricao_novo_lote3").val();
    var descricao_novo_lote4 = $("#descricao_novo_lote4").val();
    var descricao_novo_lote5 = $("#descricao_novo_lote5").val();
    var descricao_novo_lote6 = $("#descricao_novo_lote6").val();

    var descricao_lote_montada = '';

    if (descricao_novo_lote) {
        descricao_lote_montada+=descricao_novo_lote;
    }

    if (descricao_novo_lote2) {
        if (descricao_lote_montada) {
            descricao_lote_montada+='-' + descricao_novo_lote2;
        }
        else {
            descricao_lote_montada+=descricao_novo_lote2;
        }
    }

    if (descricao_novo_lote3) {
        if (descricao_lote_montada) {
            descricao_lote_montada+='-' + descricao_novo_lote3;
        }
        else {
            descricao_lote_montada+=descricao_novo_lote3;
        }
    }

    if (descricao_novo_lote4) {
        if (descricao_lote_montada) {
            descricao_lote_montada+='-' + descricao_novo_lote4;
        }
        else {
            descricao_lote_montada+=descricao_novo_lote4;
        }
    }

    if (descricao_novo_lote5) {
        if (descricao_lote_montada) {
            descricao_lote_montada+='-' + descricao_novo_lote5;
        }
        else {
            descricao_lote_montada+=descricao_novo_lote5;
        }
    }

    if (descricao_novo_lote6) {
        if (descricao_lote_montada) {
            descricao_lote_montada+='-' + descricao_novo_lote6;
        }
        else {
            descricao_lote_montada+=descricao_novo_lote6;
        }
    }

    if (descricao_lote_montada=='') {
        $("#mensagem_erro_descricao_lote").modal(); 
        $("#mensagem_erro_descricao_lote .modal-body").html('A Descrição do Lote não pode ser vazia.');
        return;
    }

    $("#descricao_lote_montada").val(descricao_lote_montada);

    var qual_pasto = $("#qual_pasto").val();

    if (qual_pasto=='' || qual_pasto=='origem') {
        $("#descricao_lote_m").val(descricao_lote_montada);
        $("#descricao_lote_d").val(descricao_lote_montada);
    }

    $('#modal_composicao_descricao_lote').modal('hide');

    gravar_descricao_lote_digitacao();
}

// Grava a Descrição do Lote quando for digitado 
// Chamada quando clicar na descricao do lote ou Criar novo lote na transferencia
function gravar_descricao_lote_digitacao() {
    var qual_pasto = $("#qual_pasto").val();

    if (qual_pasto=='destino') {
        var pasto_origem = $("#id_pasto_destino").val();
        var id_lote = 0;
        var ano_lote = 0;
        var novo_id = 'S';
    }
    /*else if (qual_pasto=='origem'){
        var pasto_origem = $("#pasto_origem").val();
        var id_lote = 0;
        var ano_lote = 0;
        var novo_id = 'S';
    }*/
    else {
        var pasto_origem = $("#pasto_origem").val();
        var id_lote = $("#id_lote").val();
        var ano_lote = $("#ano_lote").val();
        var novo_id = 'N';
    }

    /*var dispositivo = $("#dispositivo").val();

    if (dispositivo=='D') {
        var descricao_lote = $("#descricao_lote_d").val();
    }
    else {
        var descricao_lote = $("#descricao_lote_m").val();
    }*/

    var descricao_lote = $("#descricao_lote_montada").val();
    var descricao_novo_lote1 = $("#descricao_novo_lote").val();
    var descricao_novo_lote2 = $("#descricao_novo_lote2").val();
    var descricao_novo_lote3 = $("#descricao_novo_lote3").val();
    var descricao_novo_lote4 = $("#descricao_novo_lote4").val();
    var descricao_novo_lote5 = $("#descricao_novo_lote5").val();
    var descricao_novo_lote6 = $("#descricao_novo_lote6").val();

    $.ajax({
        type: 'post',
        url: 'gravar_alterar_descricao_lote.php',
        data: {
            'pasto_origem': pasto_origem,
            'id_lote': id_lote,
            'novo_id': novo_id,
            'ano_lote': ano_lote,
            'descricao_lote': descricao_lote,
            'descricao_lote1': descricao_novo_lote1,
            'descricao_lote2': descricao_novo_lote2,
            'descricao_lote3': descricao_novo_lote3,
            'descricao_lote4': descricao_novo_lote4,
            'descricao_lote5': descricao_novo_lote5,
            'descricao_lote6': descricao_novo_lote6
            },
            success: function(data){
                if (data.error) {  
                    $("#mensagem_erro_descricao_lote").modal(); 
                    $("#mensagem_erro_descricao_lote .modal-body").html(data.message);
                    return;
                }
                else {
                    var qual_pasto = $("#qual_pasto").val();

                    if (qual_pasto=='') {
                        //$("#mensagem_erro_descricao_lote").modal(); 
                        //$("#mensagem_erro_descricao_lote .modal-body").html(data.message);
                        $("#descricao_lote_d").val(data.descricao_com_id);
                        $("#descricao_lote_m").val(data.descricao_com_id);
                        $("#descricao_lote").val(data.descricao_lote);
                        $("#descricao_lote_1").val(data.descricao_lote1);
                        $("#descricao_lote_2").val(data.descricao_lote2);
                        $("#descricao_lote_3").val(data.descricao_lote3);
                        $("#descricao_lote_4").val(data.descricao_lote4);
                        $("#descricao_lote_5").val(data.descricao_lote5);
                        $("#descricao_lote_6").val(data.descricao_lote6);
                        $("#id_lote").val(data.id_lote);
                        $("#ano_lote").val(data.ano_lote);
                        fecha_mensagem_erro_descricao_lote();
                        return;
                    }
                    else if (qual_pasto=='destino') {
                        var qual_programa = $("#qual_programa").val();

                        if (qual_programa=='tabuleiro') {
                            fechar_mensagem_sucesso_tabuleiro();
                        }
                        else {
                            fechar_mensagem_sucesso();
                        } 
                    }
                    else {
                        fechar_mensagem_sucesso();
                    }
                }
            }
    });
}

// Grava a Descrição do Lote no Pasto Destino na transferencia dos animais
// Altera o Id do pasto Origem
// Chamada quando clicar na na opção 'Levar a Descrição do Lote do Pasto Origem'
function gravar_levar_descricao_lote_pasto_destino() {
    var id_lote = $("#id_lote").val();
    var ano_lote = $("#ano_lote").val();
    var dispositivo = $("#dispositivo").val();

    if (dispositivo=='D') {
        var pasto_destino = $("#novo_pasto_d").val();
    }
    else {
        var pasto_destino = $("#novo_pasto_m").val();
    }


    var descricao_lote = $("#descricao_lote").val();
    var descricao_novo_lote1 = $("#descricao_lote_1").val();
    var descricao_novo_lote2 = $("#descricao_lote_2").val();
    var descricao_novo_lote3 = $("#descricao_lote_3").val();
    var descricao_novo_lote4 = $("#descricao_lote_4").val();
    var descricao_novo_lote5 = $("#descricao_lote_5").val();
    var descricao_novo_lote6 = $("#descricao_lote_6").val();
    $.ajax({
        type: 'post',
        url: 'gravar_alterar_descricao_lote.php',
        data: {
            'pasto_origem': pasto_destino,
            'id_lote': id_lote,
            'novo_id': 'N',
            'ano_lote': ano_lote,
            'descricao_lote': descricao_lote,
            'descricao_lote1': descricao_novo_lote1,
            'descricao_lote2': descricao_novo_lote2,
            'descricao_lote3': descricao_novo_lote3,
            'descricao_lote4': descricao_novo_lote4,
            'descricao_lote5': descricao_novo_lote5,
            'descricao_lote6': descricao_novo_lote6
            },
            success: function(data){
                if (data.error) {  
                    $("#mensagem_erro_descricao_lote").modal(); 
                    $("#mensagem_erro_descricao_lote .modal-body").html(data.message);
                    return;
                }
                else {
                    //$("#mensagem_sucesso_descricao_lote_destino").modal(); 
                    //$("#mensagem_sucesso_descricao_lote_destino .modal-body").html(data.message);
                    trocar_id_lote_pasto_origem();
                }
            }
    });
}

// Troca o ID do Lote no Pasto Origem na transferencia dos animais
// Chamada quando clicar na na opção 'Manter a Descrição do Pasto Origem'
function trocar_id_lote_pasto_origem() {
    var id_lote = $("#id_lote").val();
    var ano_lote = $("#ano_lote").val();
    var dispositivo = $("#dispositivo").val();
    var pasto_origem = $("#pasto_origem").val();
    var descricao_lote = $("#descricao_lote").val();
    var descricao_novo_lote1 = $("#descricao_lote_1").val();
    var descricao_novo_lote2 = $("#descricao_lote_2").val();
    var descricao_novo_lote3 = $("#descricao_lote_3").val();
    var descricao_novo_lote4 = $("#descricao_lote_4").val();
    var descricao_novo_lote5 = $("#descricao_lote_5").val();
    var descricao_novo_lote6 = $("#descricao_lote_6").val();

    /* 
    Se a Descricao do Pasto Origem foi transferida para o Pasto Destino
    então o Pasto Origem irá receber um novo ID
    Caso contrario será mantido o mesmo ID 
    */

    var novo_id = 'S';
    
    $.ajax({
        type: 'post',
        url: 'gravar_alterar_descricao_lote.php',
        data: {
            'pasto_origem': pasto_origem,
            'id_lote': id_lote,
            'novo_id': novo_id,
            'ano_lote': ano_lote,
            'descricao_lote': descricao_lote,
            'descricao_lote1': descricao_novo_lote1,
            'descricao_lote2': descricao_novo_lote2,
            'descricao_lote3': descricao_novo_lote3,
            'descricao_lote4': descricao_novo_lote4,
            'descricao_lote5': descricao_novo_lote5,
            'descricao_lote6': descricao_novo_lote6
            },
            success: function(data){
                if (data.error) {  
                    $("#mensagem_erro_descricao_lote").modal(); 
                    $("#mensagem_erro_descricao_lote .modal-body").html(data.message);
                    return;
                }
                else {
                    $("#descricao_lote_d").val(data.descricao_com_id);
                    $("#descricao_lote_m").val(data.descricao_com_id);
                    $("#descricao_lote").val(data.descricao_lote);
                    $("#descricao_lote_1").val(data.descricao_lote1);
                    $("#descricao_lote_2").val(data.descricao_lote2);
                    $("#descricao_lote_3").val(data.descricao_lote3);
                    $("#descricao_lote_4").val(data.descricao_lote4);
                    $("#descricao_lote_5").val(data.descricao_lote5);
                    $("#descricao_lote_6").val(data.descricao_lote6);
                    $("#id_lote").val(data.id_lote);
                    $("#ano_lote").val(data.ano_lote);

                    $("#categoria_sexo_d").val('000000000');
                    $("#quantidade_d").val('');
                    $("#novo_pasto_d").val('000000000');
                    $("#categoria_sexo_m").val('000000000');
                    $("#quantidade_m").val('');
                    $("#novo_pasto_m").val('000000000');

                    var pasto_origem = $("#pasto_origem").val();
                    $.redirect('form_mapa_gados_movimentacao.php', {'pasto_id': pasto_origem});
                }
            }
    });
}

function fechar_mensagem_sucesso() {
    $("#categoria_sexo_d").val('000000000');
    $("#quantidade_d").val('');
    $("#novo_pasto_d").val('000000000');
    $("#categoria_sexo_m").val('000000000');
    $("#quantidade_m").val('');
    $("#novo_pasto_m").val('000000000');

    var pasto_origem = $("#pasto_origem").val();
    $.redirect('form_mapa_gados_movimentacao.php', {'pasto_id': pasto_origem});
}

function fechar_mensagem_sucesso_tabuleiro() {
    $("#mensagem_manter_desc_pasto_destino").modal('hide'); 
    $("#mensagem_sucesso_descricao_novo_lote_destino").modal('hide'); 
    consultar_mapa();    
}

function popular_descricao() {
    $.post("popular_descricao_lote.php", {}, function(valor){
        $("select[name=descricao_principal]").html(valor);
    });  
}

function popular_situacao() {
    var descricao_id = $("#descricao_principal").val();

    $("#data_paricao_principal").val('');
    $("#situacao_principal").html('0');
    $("#com_data").prop("checked", false);
    $(".exibir_parametro_2").hide();
    $(".exibir_parametro_3").hide();
    $(".exibir_parametro_4").hide();
    $(".exibir_parametro_4_mais").hide();
    $(".exibir_parametro_4_data_mais").hide();
    $('.label_parametro_3').html('Informar Data da Parição?');
    $('.label_parametro_4').html('Mês/Ano da Parição?');
    $('.label_parametro_4_mais').html('Mês/Ano da Parição?');

    switch(descricao_id) {
        case '00':
            $("#situacao_principal").html('');
            $(".exibir_parametro_2").hide();
            $(".exibir_parametro_3").hide();
            $(".exibir_descricao").hide();
            break;
        case '01':
            $('.label_parametro_2').html('* Situação');
            $("#situacao_principal").html('');

            $('#situacao_principal').append('<option value="0" selected disabled>' + 'Selecione' + '</option>');
            $('#situacao_principal').append('<option value="1">' + 'VAZIAS' + '</option>');
            $('#situacao_principal').append('<option value="2">' + 'CHEIAS' + '</option>');
            $('#situacao_principal').append('<option value="3">' + 'MOJANDO' + '</option>');
            $('#situacao_principal').append('<option value="4">' + 'PARIDAS' + '</option>');
            $('#situacao_principal').append('<option value="5">' + 'DESCARTE' + '</option>');
            $(".exibir_parametro_2").show();
            document.getElementById('situacao_principal').focus();
            exibe_descricao_lote();
            break;
        case '02':
            $('.label_parametro_2').html('* Situação');
            $("#situacao_principal").html('');

            $('#situacao_principal').append('<option value="0" selected disabled>' + 'Selecione' + '</option>');
            $('#situacao_principal').append('<option value="1">' + 'VAZIAS' + '</option>');
            $('#situacao_principal').append('<option value="2">' + 'CHEIAS' + '</option>');
            $('#situacao_principal').append('<option value="3">' + 'MOJANDO' + '</option>');
            $('#situacao_principal').append('<option value="4">' + 'PARIDAS' + '</option>');
            $('#situacao_principal').append('<option value="5">' + 'DESCARTE' + '</option>');
            $(".exibir_parametro_2").show();
            document.getElementById('situacao_principal').focus();
            exibe_descricao_lote();
             break;
        case '03':
            $('.label_parametro_2').html('* Sexo');
            $("#situacao_principal").html('');

            $('#situacao_principal').append('<option value="0" selected disabled>' + 'Selecione' + '</option>');
            $('#situacao_principal').append('<option value="3">' + 'MACHO/FÊMEA' + '</option>');
            $('#situacao_principal').append('<option value="1">' + 'MACHO' + '</option>');
            $('#situacao_principal').append('<option value="2">' + 'FÊMEA' + '</option>');
            $(".exibir_parametro_2").show();
            document.getElementById('situacao_principal').focus();
            exibe_descricao_lote();
            break;
        case '04':
            $(".exibir_incluir_mais").show();
            $("#situacao_principal").html('');
            $(".exibir_parametro_2").hide();
            exibe_descricao_lote();
            break;
        case '05':
            $(".exibir_incluir_mais").show();
            $("#situacao_principal").html('');
            $(".exibir_parametro_2").hide();
            exibe_descricao_lote();
            break;
        case '06':
            $(".exibir_incluir_mais").show();
            $("#situacao_principal").html('');
            $(".exibir_parametro_2").hide();
            exibe_descricao_lote();
            break;
        case '07':
            $('.label_parametro_2').html('* Situação');
            $("#situacao_principal").html('');

            $('#situacao_principal').append('<option value="0" selected disabled>' + 'Selecione' + '</option>');
            $('#situacao_principal').append('<option value="1">' + 'VAZIAS' + '</option>');
            $('#situacao_principal').append('<option value="2">' + 'CHEIAS' + '</option>');
            $('#situacao_principal').append('<option value="3">' + 'MOJANDO' + '</option>');
            $('#situacao_principal').append('<option value="4">' + 'PARIDAS' + '</option>');
            $('#situacao_principal').append('<option value="5">' + 'DESCARTE' + '</option>');
            $(".exibir_parametro_2").show();
            document.getElementById('situacao_principal').focus();
            exibe_descricao_lote();
            break;
        case '08': 
            $('.label_parametro_2').html('* Situação');
            $("#situacao_principal").html('');

            $('#situacao_principal').append('<option value="0" selected disabled>' + 'Selecione' + '</option>');
            $('#situacao_principal').append('<option value="1">' + 'VAZIAS' + '</option>');
            $('#situacao_principal').append('<option value="2">' + 'CHEIAS' + '</option>');
            $('#situacao_principal').append('<option value="3">' + 'MOJANDO' + '</option>');
            $('#situacao_principal').append('<option value="4">' + 'PARIDAS' + '</option>');
            $('#situacao_principal').append('<option value="5">' + 'DESCARTE' + '</option>');
            $(".exibir_parametro_2").show();
            document.getElementById('situacao_principal').focus();
            exibe_descricao_lote();
            break;
    }
}

function exibir_parametro_3() {
    $(".exibir_incluir_mais").show();
    //$("div.descricao_principal").removeClass('form-group');

    var descricao_id = $("#descricao_principal").val();
    var situacao = $("#situacao_principal").val();
    $("#data_paricao_principal").val('');

    if (descricao_id==1 || descricao_id==2 || 
        descricao_id==7 || descricao_id==8) {
        if (situacao!=0) {
            if (situacao!=1 && situacao!=5) {
                $(".exibir_parametro_3").show();
            }
            else {
                $(".exibir_parametro_3").hide();
            }
        }
        else {
            $(".exibir_parametro_3").hide();
        }
    }
    else if (descricao_id==3) {
        $('.label_parametro_3').html('Informar Data do Nascimento?');
        $('.label_parametro_4').html('Mês/Ano do Nascimento?');
        $('.label_parametro_4_mais').html('Mês/Ano do Nascimento?');
        $(".exibir_parametro_3").show();
    }
    else {
        $('.label_parametro_3').html('Informar Data da Parição?');
        $('.label_parametro_4').html('Mês/Ano da Parição?');
        $('.label_parametro_4_mais').html('Mês/Ano da Parição?');
        $(".exibir_parametro_3").hide();
    }

    exibe_descricao_lote();
}

$(document).ready(function(){

    // Popular o select Pasto para transferencia dos animais no pasto
    // Não exibe o Pasto Origem
    var local_id = $("#local_origem").val();
    var pasto_origem = $("#pasto_origem").val();

    if (local_id!=undefined && pasto_origem!=undefined) {
        $.ajax({
            type: 'post',
            url: 'popular_select_pasto.php',
            data: {
                'local_id': local_id,
                'pasto_origem': pasto_origem
            },
            success: function(data){
                $('.novo_pasto').html(data);
            }
        });
    }

    // Popular o select Categoria e Sexo para transferencia dos animais no pasto
    var pasto_origem = $("#pasto_origem").val();

    if (pasto_origem!=undefined) {
        $.ajax({
            type: 'post',
            url: 'popular_select_categoria_sexo.php',
            data: {
                'pasto_origem': pasto_origem
            },
            success: function(data){
                $('.categoria_sexo').html(data);
            }
        });
    }

     $('#slctCocho').on('change', function() {
        var totalAnimais = $("#totalAnimais").val();

        if (totalAnimais==0) {
            $("#mensagem_pasto_vazio").modal();
            $("#mensagem_pasto_vazio .modal-body .mensagem_pasto").html('Este pasto está vazio!');
            $("#slctCocho").val('000000000');
            $("#nomeProduto").val('000000000');
            $("#qtdProduto").val('');
            $("#undProduto").val('');
            needToConfirm = false;
            return;
        }                         
    });  

     $('#descricao_principal').on('change', function() {
        exibe_descricao_lote();            
    });  

    $("#situacao_principal").on("click input", function() {
        $(this).css("border", ""); 
    });

    $('#com_data').on('change', function() {

        var com_data = $('#com_data');

        if (com_data.is(":checked")) {
            $(".exibir_parametro_3").hide();
            $(".exibir_parametro_4").show();
            $(".exibir_parametro_4_mais").show();
            document.getElementById('data_paricao_principal').focus();
        }
        else {
            $(".exibir_parametro_4").hide();
            $(".exibir_parametro_4_mais").hide();
        }
    });  

    $('#quantidade_d').on('click', function() {
        var categoria_sexo = $("#categoria_sexo_d").val();

        if (categoria_sexo=='000000000') {
            $("#mensagem_mover_animais").modal(); 
            $("#mensagem_mover_animais .modal-body").html('Selecione a Qual Categoria.');
            return;
        }
    });  

    $('#quantidade_m').on('click', function() {
        var categoria_sexo = $("#categoria_sexo_m").val();

        if (categoria_sexo=='000000000') {
            $("#mensagem_mover_animais").modal(); 
            $("#mensagem_mover_animais .modal-body").html('Selecione a Qual Categoria.');
            return;
        }
    });  

    $('#novo_pasto_d').on('click', function() {
        var qtd_destino = $("#quantidade_d").val();

        if (qtd_destino==0 || qtd_destino=='') {
            $("#mensagem_mover_animais").modal(); 
            $("#mensagem_mover_animais .modal-body").html('Informe a Quantidade para transferir.');
            return;
        }
    });  

    $('#novo_pasto_m').on('click', function() {
        var qtd_destino = $("#quantidade_m").val();

        if (qtd_destino==0 || qtd_destino=='') {
            $("#mensagem_mover_animais").modal(); 
            $("#mensagem_mover_animais .modal-body").html('Informe a Quantidade para transferir.');
            return;
        }
    });  

    // Opções para a Descrição do Lote 
    $(".opcao_lote").click(function(){
        var opcao_descricao_lote = $("input[name='opcao_lote']:checked").val();
        var qual_pasto = $("#qual_pasto").val();

        if (qual_pasto=='destino') {
            if (opcao_descricao_lote=='L') { // levar descricao do pasto origem
                $(".monta_descricao_lote").hide();
                $(".linha_hr").hide();
                $('#modal_composicao_descricao_lote').modal('hide'); 
                var desc_pasto_destino = $("#desc_pasto_destino").val();
                $("#mensagem_levar_desc_pasto_destino").modal(); 
                $("#mensagem_levar_desc_pasto_destino .modal-body").html('Confirma Levar a Descrição do Lote para o Pasto ' + desc_pasto_destino);
            }
            else if (opcao_descricao_lote=='M') { // Manter a Descrição do Lote
                $('#modal_composicao_descricao_lote').modal('hide'); 
                var desc_pasto_destino = $("#desc_pasto_destino").val();
                $("#mensagem_manter_desc_pasto_destino").modal(); 
                $("#mensagem_manter_desc_pasto_destino .modal-body").html('Confirma Manter a Descrição do Lote do Pasto ' + desc_pasto_destino);
            }
            else if (opcao_descricao_lote=='N') { // Criar nova descricao lote para o pasto Destino
                $(".monta_descricao_lote").show();
                $(".linha_hr").show();
                $('#modal_composicao_descricao_lote').modal('show');

                popular_descricao();

                $("#data_paricao_principal").val('');
                $("#situacao_principal").html('0');
                $("#com_data").prop("checked", false);
                $(".descricao_principal").hide();
                $(".exibir_incluir_mais").hide();
                $(".exibir_parametro_2").hide();
                $(".exibir_parametro_3").hide();
                $(".exibir_parametro_4").hide();
                $(".exibir_parametro_4_mais").hide();
                $(".exibir_parametro_4_data_mais").hide();
                $('.label_parametro_3').html('Informar Data da Parição?');
                $('.label_parametro_4').html('Mês/Ano da Parição?');
                $('.label_parametro_4_mais').html('Mês/Ano da Parição?');

                $("#descricao_novo_lote").val('');
                $("#descricao_novo_lote2").val('');
                $("#descricao_novo_lote3").val('');
                $("#descricao_novo_lote4").val('');
                $("#descricao_novo_lote5").val('');
                $("#descricao_novo_lote6").val('');
                $("#edicao").val('');
                $(".exibir_descricao").hide();
                $(".exibir_opcoes").hide();
                $(".exibir_descricao2").hide();
                $(".exibir_opcoes2").hide();
                $(".exibir_descricao3").hide();
                $(".exibir_opcoes3").hide();
                $(".exibir_descricao4").hide();
                $(".exibir_opcoes4").hide();
                $(".exibir_descricao5").hide();
                $(".exibir_opcoes5").hide();
                $(".exibir_descricao6").hide();
                $(".exibir_opcoes6").hide();
                $(".descricao_principal").show();
                var numero_item=1;
                $("#numero_item").val(numero_item);
                $(".voltar_descricao_lote").hide();
                document.getElementById('descricao_principal').focus();
            }
        }
    });
})

function retorna_composicao_descricao_lote() {
    $("#levar_lote").prop("checked", false);
    $("#manter_lote").prop("checked", false);
    $("#novo_lote").prop("checked", false);

    $('#modal_composicao_descricao_lote').modal('show');
}

function exibe_descricao_lote() {
    var descricao_lote = montar_descricao_lote();
    var numero_item = $("#numero_item").val();;
    $("#edicao").val('');

    switch(numero_item) {
        case '1':
            $("#descricao_novo_lote").val(descricao_lote);
            $(".exibir_descricao").show();
            $(".exibir_opcoes").show();
            break;
        case '2':
            $("#descricao_novo_lote2").val(descricao_lote);
            $(".exibir_descricao2").show();
            $(".exibir_opcoes2").show();
            break;
        case '3':
            $("#descricao_novo_lote3").val(descricao_lote);
            $(".exibir_descricao3").show();
            $(".exibir_opcoes3").show();
            break;
        case '4':
            $("#descricao_novo_lote4").val(descricao_lote);
            $(".exibir_descricao4").show();
            $(".exibir_opcoes4").show();
            break;
        case '5':
            $("#descricao_novo_lote5").val(descricao_lote);
            $(".exibir_descricao5").show();
            $(".exibir_opcoes5").show();
            break;
        case '6':
            $("#descricao_novo_lote6").val(descricao_lote);
            $(".exibir_descricao6").show();
            $(".exibir_opcoes6").show();
            break;
    }
}

function exibe_descricao_lote_mais_data() {
    var numero_item = $("#numero_item").val();

    var data_paricao = $("#data_paricao_principal_mais").val();

    if (data_paricao!='') {
        var data = data_paricao.split("-");

        data = data[1]+'/'+data[0].substring(2, 4);
    }
    else {
        data = '';
    }

    switch(numero_item) {
        case '1':
            descricao_lote = $("#descricao_novo_lote").val();

            if (adicionar_mais_data==1) {
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }
            else {
                descricao_lote = descricao_lote.slice(0, descricao_lote.length - 6);
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }

            $("#descricao_novo_lote").val(descricao_lote);
            break;
        case '2':
            descricao_lote = $("#descricao_novo_lote2").val();

            if (adicionar_mais_data==1) {
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }
            else {
                descricao_lote = descricao_lote.slice(0, descricao_lote.length - 6);
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }

            $("#descricao_novo_lote2").val(descricao_lote);
            break;
        case '3':
            descricao_lote = $("#descricao_novo_lote3").val();

            if (adicionar_mais_data==1) {
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }
            else {
                descricao_lote = descricao_lote.slice(0, descricao_lote.length - 6);
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }

            $("#descricao_novo_lote3").val(descricao_lote);
            break;
        case '4':
            descricao_lote = $("#descricao_novo_lote4").val();

            if (adicionar_mais_data==1) {
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }
            else {
                descricao_lote = descricao_lote.slice(0, descricao_lote.length - 6);
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }

            $("#descricao_novo_lote4").val(descricao_lote);
            break;
        case '5':
            descricao_lote = $("#descricao_novo_lote5").val();

            if (adicionar_mais_data==1) {
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }
            else {
                descricao_lote = descricao_lote.slice(0, descricao_lote.length - 6);
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }

            $("#descricao_novo_lote5").val(descricao_lote);
            break;
        case '6':
            descricao_lote = $("#descricao_novo_lote6").val();

            if (adicionar_mais_data==1) {
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }
            else {
                descricao_lote = descricao_lote.slice(0, descricao_lote.length - 6);
                descricao_lote+= '-' + data;
                adicionar_mais_data='';
            }

            $("#descricao_novo_lote6").val(descricao_lote);
            break;
    }
}

function montar_descricao_lote() {
    var options = $('#descricao_principal option:selected');

    $(options).each(function(){
        var desc = $(this).bind('#descricao_principal').text();
        descricao_lote = desc.trim();
    });

    var situacao = $('#situacao_principal').val();

    if (situacao=='0' || situacao==undefined) {
        var parametro_2 = '';
    }
    else {
        var options = $('#situacao_principal option:selected');

        $(options).each(function(){
            var desc = $(this).bind('#situacao_principal').text();
            parametro_2 = desc.trim();
        });
    }

    var data_paricao = $("#data_paricao_principal").val();

    if (data_paricao!='') {
        var data = data_paricao.split("-");

        data = data[1]+'/'+data[0].substring(2, 4);
    }
    else {
        data = '';
    }

    if (data) {
        descricao_lote+= ' ' + parametro_2 + ' ' + data;
    }
    else {
        descricao_lote+= ' ' + parametro_2;
    }
    return descricao_lote;
}   

function incluir_mais_data(adicionar){
    adicionar_mais_data = adicionar;
    $("#data_paricao_principal").val('');
    $("#data_paricao_principal_mais").val('');
    $(".exibir_parametro_4").hide();
    $(".exibir_parametro_4").hide();
    $(".exibir_parametro_4_data_mais").show();
    document.getElementById('data_paricao_principal_mais').focus();

}

function incluir_mais_lote() {
    var descricao_id = $("#descricao_principal").val();
    var parametro_2 = $("#situacao_principal").val();
    var edicao = $("#edicao").val();

    if (edicao=='') {
        if (descricao_id == 0) {
            $("#mensagem_erro_descricao_lote").modal(); 
            $("#mensagem_erro_descricao_lote .modal-body").html('Selecione a Descrição do Lote');
            return;
        }

        if (parametro_2 == 0 && (descricao_id==1 || 
            descricao_id==2 || descricao_id==7 || descricao_id==8)) {
            $("#mensagem_erro_descricao_lote").modal(); 
            $("#mensagem_erro_descricao_lote .modal-body").html('Selecione a Situação.');
            $("#situacao_principal").css("border", "1px solid red");
            $("#situacao_principal").focus();            
            return;
        }

        if (parametro_2 == 0 && descricao_id==3) {
            $("#mensagem_erro_descricao_lote").modal(); 
            $("#mensagem_erro_descricao_lote .modal-body").html('Selecione o Sexo.');
            $("#situacao_principal").css("border", "1px solid red");
            $("#situacao_principal").focus();            
            return;
        }
    }

    $("#descricao_principal").val('00');
    $("#data_paricao_principal").val('');
    $("#situacao_principal").html('0');
    $("#edicao").val('');
    $("#com_data").prop("checked", false);
    $(".descricao_principal").show();
    $(".exibir_parametro_2").hide();
    $(".exibir_parametro_3").hide();
    $(".exibir_parametro_4").hide();
    $(".exibir_parametro_4_mais").hide();
    $(".exibir_parametro_4_data_mais").hide();

    if ($('#descricao_novo_lote').val()=='') {
        $("#numero_item").val(1);   
        $(".exibir_descricao").show();
        document.getElementById('descricao_principal').focus();
    }
    else if ($("#descricao_novo_lote2").val()==''){
        $("#numero_item").val(2);   
        $(".linha2").show();
        $(".exibir_descricao2").show();
        document.getElementById('descricao_principal').focus();
    }        
    else if ($("#descricao_novo_lote3").val()==''){
        $("#numero_item").val(3);   
        $(".linha3").show();
        $(".exibir_descricao3").show();
        document.getElementById('descricao_principal').focus();
    }        
    else if ($("#descricao_novo_lote4").val()==''){
        $("#numero_item").val(4);   
        $(".linha4").show();
        $(".exibir_descricao4").show();
        document.getElementById('descricao_principal').focus();
    }        
    else if ($("#descricao_novo_lote5").val()==''){
        $("#numero_item").val(5);   
        $(".linha5").show();
        $(".exibir_descricao5").show();
        document.getElementById('descricao_principal').focus();
    }        
    else if ($("#descricao_novo_lote6").val()==''){
        $("#numero_item").val(6);   
        $(".linha6").show();
        $(".exibir_descricao6").show();
        document.getElementById('descricao_principal').focus();
    }
    else {
        $("#mensagem_erro_descricao_lote").modal(); 
        $("#mensagem_erro_descricao_lote .modal-body").html('Só é possível incluir seis lotes de animais.');
        return;
    }        
}

function excluir_lote(numero_item) {
    switch(numero_item) {
        case 1:
            $("#descricao_novo_lote").val('');
            $("#numero_item").val(numero_item);   
            $(".exibir_opcoes").hide();
            break;
        case 2:
            $("#descricao_novo_lote2").val('');
            $("#numero_item").val(numero_item);   
            $(".exibir_opcoes2").hide();
            break;
        case 3:
            $("#descricao_novo_lote3").val('');
            $("#numero_item").val(numero_item);   
            $(".exibir_opcoes3").hide();
            break;
        case 4:
            $("#descricao_novo_lote4").val('');
            $("#numero_item").val(numero_item);   
            $(".exibir_opcoes4").hide();
            break;
        case 5:
            $("#descricao_novo_lote5").val('');
            $("#numero_item").val(numero_item);   
            $(".exibir_opcoes5").hide();
            break;
        case 6:
            $("#descricao_novo_lote6").val('');
            $("#numero_item").val(numero_item);   
            $(".exibir_opcoes6").hide();
            break;
    }

    $("#descricao_principal").val('00');
    $("#data_paricao_principal").val('');
    $("#situacao_principal").html('0');
    $("#com_data").prop("checked", false);
    $(".descricao_principal").show();
    $(".exibir_parametro_2").hide();
    $(".exibir_parametro_3").hide();
    $(".exibir_parametro_4").hide();
    $(".exibir_parametro_4_mais").hide();
    $(".exibir_parametro_4_data_mais").hide();
    $('.label_parametro_3').html('Informar Data da Parição?');
    $('.label_parametro_4').html('Mês/Ano da Parição?');
    $('.label_parametro_4_mais').html('Mês/Ano da Parição?');
    document.getElementById('descricao_principal').focus();
}

function fecha_mensagem_erro_descricao_lote() {
    $("#mensagem_erro_descricao_lote").modal('hide'); 
}

// FIM Funcões para a montagem da descrição dos lotes de animais no pasto

$(document).ready(function(){
    // Rotinas incluidas por George em 03/08/2022 - Novas rotinas para nascimento 
    $('.confirma_gravar').on('click', function() {
        $(this).prop({
            disabled: true,
            innerHTML: 'Aguarde...'
        });
    });

    $(".opcao_nascimento").click(function(){
        var opcao_nascimento = $("input[name='opcao_nascimento']:checked").val();
        var controle_estoque = $("#controle_estoque").val();
        $(".fazenda_pasto").show();
        $(".confirmar").show();

        $("#dias_nascimento").val('');
        $("#cobertura_id").val('');
        $("#item_cobertura").val('');
        $("#estacao_monta_id").val('');
        $("#ultima_estacao").html('');
        $(".icon_nascimentos_previstos").hide();
        $("#data_inseminacao").val('');
        $("#num_mov_nascimento").val(0);
        $("#tipo_gravacao").val(0);
        $("#F").prop("checked", false);
        $("#M").prop("checked", false);
        $("#local_id").val('000000000');
        $("#pasto_id").val('000000000');
        $("#codigo_mae_animal").val('');
        $("#codigo_mae_consulta").val('');
        $("#codigo_pai_animal").val('000000000');
        $("#raca_id").val('');
        $("#pelagem_id").val('');
        $("#peso_animal").val('');
        $("#qtd_animal").val('');

        var objDiv = document.getElementById("modal_nascimento");
        objDiv.scrollTop = objDiv.scrollHeight;

        if (opcao_nascimento=='N') {
            $(".campos_data_mae_pai").show();
            $(".campos_id_aborto_lote").show();
            $(".campos_id_lote").show();
            $(".raca_id").show();
            $(".peso_animal").show();
            $(".label_data").html('* Data Nascimento');
            $(".label_pasto").html('* Pasto');
            $(".label_mae").html('* Nº Mãe');
            $(".tipo_estacao_monta").html('Estação de Monta:');

            if (controle_estoque=='I') {
                $(".nascimento_id").show();
                $(".qtd_animal").hide();
                $(".pelagem_id").show();
                $(".codigo_pai_animal").show();
                $(".codigo_mae_animal").show();
            }
            else {
                $(".nascimento_id").hide();
                $(".qtd_animal").show();
                $(".pelagem_id").hide();
                $(".codigo_pai_animal").hide();
                $(".codigo_mae_animal").hide();
            }
        }
        else {
            $(".nascimento_id").hide();
            $(".campos_data_mae_pai").show();
            $(".campos_id_aborto_lote").show();
            $(".campos_id_lote").hide();
            $(".codigo_pai_animal").hide();
            $(".codigo_mae_animal").show();
            $(".qtd_animal").hide();
            $(".raca_id").hide();
            $(".pelagem_id").hide();
            $(".peso_animal").hide();
            $(".label_data").html('* Data Ocorrência');
            $(".label_pasto").html('Pasto');
            $(".label_mae").html('* Nº Fêmea');
        }
    });


    $(".opcao_nascimento_mapa").click(function(){
        var opcao_nascimento = $("input[name='opcao_nascimento']:checked").val();
        var controle_estoque = $("#controle_estoque").val();
        $(".fazenda_pasto").show();
        $(".confirmar").show();

        $("#dias_nascimento").val("");
        $("#cobertura_id").val("");
        $("#item_cobertura").val("");
        $("#data_inseminacao").val("");
        $("#num_mov_nascimento").val(0);
        $("#tipo_gravacao").val(0);
        $("#F").prop("checked", false);
        $("#M").prop("checked", false);
        $("#codigo_mae_animal").val("");
        $("#codigo_mae_consulta").val("");
        $("#codigo_pai_animal").val("000000000");
        $("#raca_id").val("");
        $("#pelagem_id").val("");
        $("#peso_animal").val("");
        $("#qtd_animal").val("");

        var objDiv = document.getElementById("modal_nascimento");
        objDiv.scrollTop = objDiv.scrollHeight;

        if (opcao_nascimento=="N") {
            $(".campos_data_mae_pai").show();
            $(".campos_id_aborto_lote").show();
            $(".campos_id_lote").show();
            $(".raca_id").show();
            $(".peso_animal").show();
            $(".label_data").html("* Data Nascimento");
            $(".label_pasto").html("* Pasto");
            $(".label_mae").html("* Nº Mãe");

            if (controle_estoque=="I") {
                $(".nascimento_id").show();
                $(".qtd_animal").hide();
                $(".pelagem_id").show();
                $(".codigo_pai_animal").show();
                $(".codigo_mae_animal").show();
            }
            else {
                $(".nascimento_id").hide();
                $(".qtd_animal").show();
                $(".pelagem_id").hide();
                $(".codigo_pai_animal").hide();
                $(".codigo_mae_animal").hide();
            }
        }
        else {
            $(".nascimento_id").hide();
            $(".campos_data_mae_pai").show();
            $(".campos_id_aborto_lote").show();
            $(".campos_id_lote").hide();
            $(".codigo_pai_animal").hide();
            $(".codigo_mae_animal").show();
            $(".qtd_animal").hide();
            $(".raca_id").hide();
            $(".pelagem_id").hide();
            $(".peso_animal").hide();
            $(".label_data").html("* Data Ocorrência");
            $(".label_pasto").html("Pasto");
            $(".label_mae").html("* Nº Fêmea");
        }
    });

    $('#nascimento_animal').change(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const nascimento_animal = $('#nascimento_animal').val();

        if (nascimento_animal>data_atual) {
            $('#mensagem_erro_data').modal();
            $('#mensagem_erro_data .modal-body').html('A Data não pode ser maior que a data atual!');
            $('#nascimento_animal').val(data_atual);
            document.getElementById('nascimento_animal').style.borderColor = '#0076d7';
        }

        $('#codigo_mae_consulta').val('');
        $('#codigo_mae_animal').val('');
        $('#codigo_pai_animal').val('000000000');
        listar_estacao();
    });    

    $('#nascimento_animal').blur(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const nascimento_animal = $('#nascimento_animal').val();

        if (nascimento_animal=='') {
            $('#mensagem_erro_data').modal();
            $('#mensagem_erro_data .modal-body').html('A Data precisa ser informada!');
            $('#nascimento_animal').val(data_atual);
            document.getElementById('nascimento_animal').style.borderColor = '#0076d7';
        }
    });    

    $('#nascimento_animal').click(function(){
        document.getElementById("nascimento_animal").style.borderColor = "";
    });    

    $('#data_morte_animal').change(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const data_morte_animal = $('#data_morte_animal').val();

        if (data_morte_animal>data_atual) {
            $('#mensagem_erro_data').modal();
            $('#mensagem_erro_data .modal-body').html('A Data não pode ser maior que a data atual!');
            $('#data_morte_animal').val(data_atual);
            document.getElementById('data_morte_animal').style.borderColor = '#0076d7';
        }
    });    

    $('#data_morte_animal').blur(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const data_morte_animal = $('#data_morte_animal').val();

        if (data_morte_animal=='') {
            $('#mensagem_erro_data').modal();
            $('#mensagem_erro_data .modal-body').html('A Data precisa ser informada!');
            $('#data_morte_animal').val(data_atual);
            document.getElementById('data_morte_animal').style.borderColor = '#0076d7';
        }
    });    

    $('#dataNutricao').change(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const dataNutricao = $('#dataNutricao').val();

        if (dataNutricao>data_atual) {
            $('#mensagem_erro_data').modal();
            $('#mensagem_erro_data .modal-body').html('A Data não pode ser maior que a data atual!');
            $('#dataNutricao').val(data_atual);
            document.getElementById('dataNutricao').style.borderColor = '#0076d7';
        }
    });    

    $('#dataNutricao').blur(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const dataNutricao = $('#dataNutricao').val();

        if (dataNutricao=='') {
            $('#mensagem_erro_data').modal();
            $('#mensagem_erro_data .modal-body').html('A Data precisa ser informada!');
            $('#dataNutricao').val(data_atual);
            document.getElementById('dataNutricao').style.borderColor = '#0076d7';
        }
    });    

    $('#codigo_numerico_animal').change(function(){
        var codigo_alfa= $('#alfa_animal').val();
        var codigo_animal= $('#codigo_numerico_animal').val();
        var codigo_mae = $('#codigo_mae_animal').val();

        $.post('ler_animal_inclusao.php',{codigo_alfa: codigo_alfa,codigo_animal: codigo_animal, codigo_mae: codigo_mae}, function(valor){
            var php = valor.split('<|>');

            if (php[0]==1){
                $('#mensagem_erro').modal();
                $('#mensagem_erro .modal-body').html('O código ' + codigo_alfa + codigo_animal + ' já existe cadastrado no sistema para essa Mãe.');
                $('#alfa_animal').val($('#codigo_alfa_anterior').val());
                $('#codigo_numerico_animal').val($('#codigo_numerico_anterior').val());
                document.getElementById('codigo_numerico_animal').focus();
                return;
            }
        });
    });

    $('#alfa_animal').change(function(){
        var codigo_alfa= $('#alfa_animal').val();
        var codigo_animal= $('#codigo_numerico_animal').val();
        var codigo_mae = $('#codigo_mae_animal').val();

        $.post('ler_animal_inclusao.php',{codigo_alfa: codigo_alfa,codigo_animal: codigo_animal, codigo_mae: codigo_mae}, function(valor){
            var php = valor.split('<|>');

            if (php[0]==1){
                $('#mensagem_erro').modal();
                $('#mensagem_erro .modal-body').html('O código ' + codigo_alfa + codigo_animal + ' já existe cadastrado no sistema para essa Mãe.');
                $('#alfa_animal').val($('#codigo_alfa_anterior').val());
                $('#codigo_numerico_animal').val($('#codigo_numerico_anterior').val());
                document.getElementById('codigo_numerico_animal').focus();
                return;
            }
        });
    });

    $('.sexo_animal').change(function(){
        var codigo_alfa= $('#alfa_animal').val();
        var codigo_animal= $('#codigo_numerico_animal').val();
        var codigo_mae = $('#codigo_mae_animal').val();

        $.post('ler_animal_inclusao.php',{codigo_alfa: codigo_alfa,codigo_animal: codigo_animal, codigo_mae: codigo_mae}, function(valor){
            var php = valor.split('<|>');

            if (php[0]==1){
                $('#mensagem_erro').modal();
                $('#mensagem_erro .modal-body').html('O código ' + codigo_alfa + codigo_animal + ' já existe cadastrado no sistema para essa Mãe.');
                $('#alfa_animal').val($('#codigo_alfa_anterior').val());
                $('#codigo_numerico_animal').val($('#codigo_numerico_anterior').val());
                $('#F').prop('checked', false);
                $('#M').prop('checked', false);
                document.getElementById('codigo_numerico_animal').focus();
                return;
            }
        });
    });

    $('#categoria_morte').change(function(event) {
        var categoria = $('#categoria_morte').val();
        $('#sexo_morte').val(categoria.substr(0, 1));      
        $('#categoria_digitada_morte').val(categoria.substr(1, 3));      
    }); 

    $('#categoria_morte').click(function(){
        $('#alert_erro_animal .negrito').html('');
        $('#alert_erro_animal span').html('');
        $('.alert_erro_animal').hide();
        return;
    });


    $('#motivo_morte').click(function(event) {
        $('#alert_erro_animal .negrito').html('');
        $('#alert_erro_animal span').html('');
        $('.alert_erro_animal').hide();

        var controle_estoque = $("#controle_estoque").val();
        var animal_codigo_id = $("#codigo_id_morte").val();

        if (animal_codigo_id==0 && controle_estoque=='I'){
            $("#mensagem_erro_animal_filtro").modal();
            $("#id_animal_morte").val('');
            $('#motivo_morte').val('000');
            document.getElementById("id_animal_morte").focus();
            document.getElementById("id_animal_morte").style.borderColor = "red";
            return;
        }
    }); 

    $('#observacao_morte').click(function(event) {
        var controle_estoque = $("#controle_estoque").val();
        var animal_codigo_id = $("#codigo_id_morte").val();

        if (animal_codigo_id==0 && controle_estoque=='I'){
            $("#mensagem_erro_animal_filtro").modal();
            $("#id_animal_morte").val('');
            $("#observacao_morte").val('');
            document.getElementById("id_animal_morte").focus();
            document.getElementById("id_animal_morte").style.borderColor = "red";
            return;
        }
    }); 

    /*$('#data_morte_animal').click(function(event) {
        var controle_estoque = $("#controle_estoque").val();
        var animal_codigo_id = $("#codigo_id_morte").val();

        if (animal_codigo_id==0 && controle_estoque=='I'){
            $("#mensagem_erro_animal_filtro").modal();
            $("#id_animal_morte").val('');
            $("#data_morte_animal").val('');
            document.getElementById("id_animal_morte").focus();
            document.getElementById("id_animal_morte").style.borderColor = "red";
            $("#qualModal").val('M');
            return;
        }
    }); */

});

function ler_animal_mae(){
    var codigo_mae= $('#codigo_mae_animal').val();

    if (codigo_mae!="") {
        return;
    }

    var id_animal= $('#codigo_mae_consulta').val();
    var local = $('#local_id').val();
    var data_nascimento = $('#nascimento_animal').val();

    if (id_animal.length < 5) {
        return;
    } 

    $.post("ler_animal_femea_nascimento.php", {id_animal:id_animal, local:local, data_nascimento:data_nascimento}, function(valor){
        var php = valor.split("<|>");

        if (php[12]!=''){
            var select = document.getElementById("estacao_monta");

            for(var i=0; i<select.options.length; i++) {
                if(select.options[i].text <= php[12]) {
                    select.options[i].remove();
                    i--;
                    //break;
                }
            }
        }

        var grupo_usuario = $("#grupo_usuario").val();

        if (php[0]=='Nao tem animal') {
            $("#codigo_mae_consulta").val('Não encontrado');
            $("#codigo_mae_animal").val('');
            return;
        }
        else if (php[1]=='N') {
            $("#codigo_mae_consulta").val(id_animal + ' - ' +  php[2]);
            $("#codigo_mae_animal").val(php[0]);
            $("#estacao_monta_id").val(php[8]);
            $("#ultima_estacao").html(php[12]);
            $(".tipo_estacao_monta").html('Estação de Monta:');

            if (php[8] == php[14] && php[15]=='C') { // negativo na estacao atual 
                if ((grupo_usuario==01 || grupo_usuario==03)) {
                    $('#modal_estacao .modal-body .desc_modal').html('Diagnóstico negativo estação ' + php[12]); 
                    $('#modal_estacao .modal-body .desc_modal_1').html('-Clique em Substituir por Monta Natural OU;');
                    $('#modal_estacao .modal-body .desc_modal_2').html('-Clique em ALTERAR DIAGNÓSTICO OU;'); 
                    $('#modal_estacao .modal-body .desc_modal_3').html('-Selecione outra fêmea.'); 
                    $('.mens_administrador').hide(); 
                    $('.estacao_monta').hide();
                    $('#modal_estacao .outra_estacao').hide(); 
                    $('#modal_estacao .substituir').show(); 
                    $('#modal_estacao .alterar_diagnostico').show(); 
                    $('#modal_estacao .outra_femea').show(); 
                    $('#modal_estacao .voltar').hide(); 
                    $('#modal_estacao .fechar').hide(); 
                    $('#modal_estacao').modal('show'); 
                    return;
                }
                else {
                    $('#modal_estacao .modal-body .desc_modal').html('Diagnóstico negativo estação ' + php[12]); 
                    $('#modal_estacao .modal-body .desc_modal_1').html('');
                    $('#modal_estacao .modal-body .desc_modal_2').html(''); 
                    $('#modal_estacao .modal-body .desc_modal_3').html(''); 
                    $('.mens_administrador').show(); 
                    $('.estacao_monta').hide();
                    $('#modal_estacao .outra_estacao').hide(); 
                    $('#modal_estacao .substituir').hide(); 
                    $('#modal_estacao .alterar_diagnostico').hide(); 
                    $('#modal_estacao .outra_femea').hide(); 
                    $('#modal_estacao .voltar').hide(); 
                    $('#modal_estacao .fechar').show(); 
                    $('#modal_estacao').modal('show'); 
                    return;
                }
            }
            else if (php[15]=='C'){ // negativo na estacao anterior e não entrou na estacao atual
                if (php[22]<9) { // diagnostivo negativo < 9 meses

                    if ((grupo_usuario==01 || grupo_usuario==03)) {
                        $('#modal_estacao .modal-body .desc_modal').html('Esta Fêmea não está em estação de monta. Diagnóstico negativo estação ' + php[12]); 
                        $('#modal_estacao .modal-body .desc_modal_1').html('-Clique em Substituir por Monta Natural OU;');
                        $('#modal_estacao .modal-body .desc_modal_2').html('-Clique em ALTERAR DIAGNÓSTICO OU;'); 
                        $('#modal_estacao .modal-body .desc_modal_3').html('-Selecione outra fêmea.'); 
                        $('.mens_administrador').hide(); 
                        $('.estacao_monta').hide();
                        $('#modal_estacao .outra_estacao').hide(); 
                        $('#modal_estacao .alterar_diagnostico').show(); 
                        $('#modal_estacao .substituir').show(); 
                        $('#modal_estacao .outra_femea').show(); 
                        $('#modal_estacao .voltar').hide(); 
                        $('#modal_estacao .fechar').hide(); 
                        $('#modal_estacao').modal('show'); 
                        return;
                    }
                    else {
                        $('#modal_estacao .modal-body .desc_modal').html('Esta Fêmea não está em estação de monta. Diagnóstico negativo estação ' + php[12]); 
                        $('#modal_estacao .modal-body .desc_modal_1').html('');
                        $('#modal_estacao .modal-body .desc_modal_2').html(''); 
                        $('#modal_estacao .modal-body .desc_modal_3').html(''); 
                        $('.mens_administrador').show(); 
                        $('.estacao_monta').hide();
                        $('#modal_estacao .outra_estacao').hide(); 
                        $('#modal_estacao .alterar_diagnostico').hide(); 
                        $('#modal_estacao .substituir').hide(); 
                        $('#modal_estacao .outra_femea').hide(); 
                        $('#modal_estacao .voltar').hide(); 
                        $('#modal_estacao .fechar').show(); 
                        $('#modal_estacao').modal('show'); 
                        return;
                    }
                }
                else {
                    // diagnostico negativo >= 9 meeses - Monta Natural
                    php[4] = '';
                    php[5] = '';
                    php[8] = '';
                    php[16] = '';
                    $("#codigo_pai_animal").val('000000000');
                    $("#raca_id").val('');
                    $("#cobertura_id").val(0);
                    $("#item_cobertura").val('');
                    $("#estacao_monta_id").val('');
                    $("#ultima_estacao").html('');
                    $("#tipo_gravacao").val(0);
                    $("#tipo_cobertura").val('M');
                    $(".tipo_estacao_monta").html('Monta:');
                    $("#data_prenhes").val('');
                    $(".icon_nascimentos_previstos").hide();
                    return;
                }
            }
        }
        else if (php[1]=='D' && php[15]=='C') {
            $("#mensagem_erro").modal(); 
            $('#mensagem_erro .modal-title').html('Nascimento - Mensagem');
            $("#mensagem_erro .modal-body").html('Estação: ' + php[12] + ' Aguardando Diagnóstico.');
            return;
        }
        else if (php[1]=='I' && php[15]=='C') {
            $("#mensagem_erro").modal(); 
            $('#mensagem_erro .modal-title').html('Nascimento - Mensagem');
            $("#mensagem_erro .modal-body").html('Estação: ' + php[12] + ' Aguardando Inseminação.');
            return;
        }
        else if (php[1]=='P'){
            $("#codigo_mae_consulta").val(id_animal + ' - ' +  php[2]);
            $("#codigo_mae_animal").val(php[0]);
            $("#codigo_pai_animal").val(php[3]);
            $("#raca_id").val(php[11]);
            $("#cobertura_id").val(php[4]);
            $("#item_cobertura").val(php[5]);
            $("#data_inseminacao").val(php[6]);
            $("#dias_nascimento").val(php[7]);
            $(".tipo_estacao_monta").html('Estação de Monta:');
            $("#estacao_monta_id").val(php[8]);
            $("#ultima_estacao").html(php[12]);
            $("#tipo_gravacao").val(0);
            $("#tipo_cobertura").val(php[15]);
            $("#data_prenhes").val(php[16]);

            if (php[12]=='') {
                $(".icon_nascimentos_previstos").hide();
            }

            if (php[4]!=0 && php[20]=='A') {
                $("#cobertura_id").val(0);
                $("#item_cobertura").val('');
            }
        } 

        var opcao_nascimento = $("input[name='opcao_nascimento']:checked").val();

        if (opcao_nascimento=='N') {
            if (php[4]==0 || php[15]=='M') {
                $("#codigo_mae_animal").val(php[0]);
                $("#codigo_mae_consulta").val(id_animal + ' - ' +  php[2]);
                $("#codigo_mae_animal").val(php[0]);
                $("#codigo_pai_animal").val(php[3]);
                $("#raca_id").val(php[11]);
                $("#cobertura_id").val(php[4]);
                $("#item_cobertura").val(php[5]);
                $("#data_inseminacao").val(php[6]);
                $("#dias_nascimento").val(php[7]);
                $("#estacao_monta_id").val(php[8]);
                $("#ultima_estacao").html(php[12]);
                $("#tipo_gravacao").val(0);
                $("#tipo_cobertura").val(php[15]);
                $("#data_prenhes").val(php[16]);
                $(".tipo_estacao_monta").html('Monta:');
                $("#ultima_estacao").html('');
                $(".icon_nascimentos_previstos").hide(); 

                if (php[20]=='A') {
                    $("#cobertura_id").val(0);
                    $("#item_cobertura").val('');
                }
            }

            if (php[10]==1 && php[18]<=90) {
                $("#nascimento_aborto_natimorto .modal-body .mensagem_aborto_natimorto").html('Essa fêmea teve aborto ou natimorto nos últimos 90 dias');
                $('#nascimento_aborto_natimorto').modal('show');
                return;
            }

            if (php[10]==1 && php[21]<9) {
                $("#nascimento_aborto_natimorto .modal-body .mensagem_aborto_natimorto").html('Essa fêmea teve aborto ou natimorto nos últimos 9 meses');
                $('#nascimento_aborto_natimorto').modal('show');
                return;
            }

            if (php[9]!=0) {
                if (php[15]=='C') {
                    var data_nascimento = $("#nascimento_animal").val();

                    if (data_nascimento == php[13]) {
                        $('#nascimento_gemelar .modal-body .desc_gemelar').html('Fêmea já possui nascimento na estação ' + php[12]); 
                        $('#nascimento_gemelar').modal('show');
                        return;
                    }
                    else {
                        // Remove a estação atual do select "estacao_monta" para o usuário não selecionar a mesma estacao
                        var select = document.getElementById("estacao_monta");

                        for(var i=0; i<select.options.length; i++) {
                            if(select.options[i].text <= php[12]) {
                                select.options[i].remove();
                                i--;
                                //break;
                            }
                        }

                        if (php[17]<9) {
                            if ((grupo_usuario==01 || grupo_usuario==03)) {
                                $('#modal_estacao .modal-body .desc_modal').html('Esta fêmea teve nascimento na estação  ' + php[12]); 
                                $('#modal_estacao .modal-body .desc_modal_1').html('-Para GEMELAR, VOLTAR e informar a mesma data de nascimento OU;');
                                $('#modal_estacao .modal-body .desc_modal_2').html('-Clique em Substituir por Monta Natural OU;'); 
                                $('#modal_estacao .modal-body .desc_modal_3').html('-Selecione outra fêmea.'); 
                                $('.mens_administrador').hide(); 
                                $('.estacao_monta').hide();
                                $('#modal_estacao .outra_estacao').hide(); 
                                $('#modal_estacao .alterar_diagnostico').hide(); 
                                $('#modal_estacao .substituir').show(); 
                                $('#modal_estacao .outra_femea').show(); 
                                $('#modal_estacao .voltar').hide(); 
                                $('#modal_estacao .fechar').hide(); 
                                $('#modal_estacao').modal('show'); 
                                return;
                            }
                            else {
                                $('#modal_estacao .modal-body .desc_modal').html('Esta fêmea teve nascimento na estação  ' + php[12]); 
                                $('#modal_estacao .modal-body .desc_modal_1').html('');
                                $('#modal_estacao .modal-body .desc_modal_2').html(''); 
                                $('#modal_estacao .modal-body .desc_modal_3').html(''); 
                                $('.mens_administrador').show(); 
                                $('.estacao_monta').hide();
                                $('#modal_estacao .outra_estacao').hide(); 
                                $('#modal_estacao .substituir').hide(); 
                                $('#modal_estacao .alterar_diagnostico').hide(); 
                                $('#modal_estacao .outra_femea').hide(); 
                                $('#modal_estacao .voltar').hide(); 
                                $('#modal_estacao .fechar').show(); 
                                $('#modal_estacao').modal('show'); 
                                return;
                            }
                        }
                        else {
                            // Limpa dados da estacao para Monta
                            php[4] = '';
                            php[5] = '';
                            php[8] = '';
                            php[16] = '';
                            $("#codigo_pai_animal").val('000000000');
                            $("#raca_id").val('');
                            $("#cobertura_id").val(0);
                            $("#item_cobertura").val('');
                            $("#estacao_monta_id").val('');
                            $("#ultima_estacao").html('');
                            $("#tipo_gravacao").val(0);
                            $("#tipo_cobertura").val('M');
                            $(".tipo_estacao_monta").html('Monta:');
                            $("#data_prenhes").val('');
                            $(".icon_nascimentos_previstos").hide();
                            return;
                        }
                    }
                }
                else if (php[15]=='M') {
                    var data_nascimento = $("#nascimento_animal").val();

                    if (data_nascimento == php[13]) {
                        $('#nascimento_gemelar .modal-body .desc_gemelar').html('Fêmea já possui nascimento nessa data.'); 
                        $('#nascimento_gemelar').modal('show');
                        return;
                    }
                    else {
                        if (php[10]==1 && php[18]<=90) {
                            $("#nascimento_aborto_natimorto .modal-body .mensagem_aborto_natimorto").html('Essa fêmea teve aborto ou natimorto nos últimos 90 dias');
                            $('#nascimento_aborto_natimorto').modal('show');
                            return;
                        }
                        else {
                            if (php[17]<9) {
                                if ((grupo_usuario==01 || grupo_usuario==03)) {
                                    $('#modal_estacao .modal-body .desc_modal').html('Essa fêmea possui bezerro nascido há menos de 9 meses.'); 
                                    $('#modal_estacao .modal-body .desc_modal_1').html('-Para GEMELAR, VOLTAR e informar a mesma data de nascimento OU;');
                                    $('#modal_estacao .modal-body .desc_modal_2').html('-Selecione outra fêmea.;'); 
                                    $('#modal_estacao .modal-body .desc_modal_3').html(''); 
                                    $('.mens_administrador').hide(); 
                                    $('.estacao_monta').hide();
                                    $('#modal_estacao .outra_estacao').hide(); 
                                    $('#modal_estacao .substituir').hide(); 
                                    $('#modal_estacao .alterar_diagnostico').hide(); 
                                    $('#modal_estacao .outra_femea').show(); 
                                    $('#modal_estacao .voltar').show(); 
                                    $('#modal_estacao .fechar').hide(); 
                                    $('#modal_estacao').modal('show'); 
                                    return;
                                }
                                else {
                                    $('#modal_estacao .modal-body .desc_modal').html('Essa fêmea possui bezerro nascido há menos de 9 meses.'); 
                                    $('#modal_estacao .modal-body .desc_modal_1').html('');
                                    $('#modal_estacao .modal-body .desc_modal_2').html(''); 
                                    $('#modal_estacao .modal-body .desc_modal_3').html(''); 
                                    $('.mens_administrador').show(); 
                                    $('.estacao_monta').hide();
                                    $('#modal_estacao .outra_estacao').hide(); 
                                    $('#modal_estacao .substituir').hide(); 
                                    $('#modal_estacao .alterar_diagnostico').hide(); 
                                    $('#modal_estacao .outra_femea').hide(); 
                                    $('#modal_estacao .voltar').hide(); 
                                    $('#modal_estacao .fechar').show(); 
                                    $('#modal_estacao').modal('show'); 
                                    return;
                                }
                            }
                        }
                    }
                }
            }
        }
        else {
            if (php[10]==1) {
                if (php[15]=='C' && php[18]<=90) {
                    $("#nascimento_aborto_natimorto .modal-body .mensagem_aborto_natimorto").html('Essa fêmea teve aborto ou natimorto na estação ' + php[12] + ' nos últimos 90 dias');
                    $('#nascimento_aborto_natimorto').modal('show');
                    return;
                }
                else if (php[15]=='M' && php[18]<=90) {
                    $("#nascimento_aborto_natimorto .modal-body .mensagem_aborto_natimorto").html('Essa fêmea teve aborto ou natimorto nos últimos 90 dias');
                    $('#nascimento_aborto_natimorto').modal('show');
                    return;
                }
            }

            if (php[9]!=0 && php[19]!='') {
                //$("#nascimento_aborto_natimorto .modal-body .mensagem_aborto_natimorto").html('Essa fêmea teve nascimento na estação ' + php[12]);
                //$('#nascimento_aborto_natimorto').modal('show');
                //return;

                if (php[15]=='C' && php[19]<=90) {
                    $("#nascimento_aborto_natimorto .modal-body .mensagem_aborto_natimorto").html('Essa fêmea teve nascimento na estação ' + php[12] + ' nos últimos 90 dias');
                    $('#nascimento_aborto_natimorto').modal('show');
                    return;
                }
                else if (php[15]=='M' && php[19]<=90) {
                    $("#nascimento_aborto_natimorto .modal-body .mensagem_aborto_natimorto").html('Essa fêmea teve nascimento nos últimos 90 dias');
                    $('#nascimento_aborto_natimorto').modal('show');
                    return;
                }
            }

            // Se a ultima movimentacao foi aborto/natimorto e foi a mais de 90 dias, 
            // então será permidido outro aborto ou natimorto por MONTA
            if (php[10]==1 && php[18]>90) {
                $("#codigo_mae_animal").val(php[0]);
                $("#codigo_mae_consulta").val(id_animal + ' - ' +  php[2]);
                $("#cobertura_id").val(0);
                $("#item_cobertura").val('');
                $("#estacao_monta_id").val('');
                $("#tipo_gravacao").val(0);
                $("#tipo_cobertura").val('M');
                $("#data_prenhes").val('');
                $(".icon_nascimentos_previstos").hide();    
            }
            else {
                $("#codigo_mae_animal").val(php[0]);
                $("#codigo_mae_consulta").val(id_animal + ' - ' +  php[2]);
                $("#cobertura_id").val(php[4]);
                $("#item_cobertura").val(php[5]);
                $("#estacao_monta_id").val(php[8]);
                $("#tipo_gravacao").val(0);
                $("#tipo_cobertura").val('M');
                $("#data_prenhes").val(php[16]);
                $(".icon_nascimentos_previstos").hide();    
            }

            // se tem item de cobertura e nascido na estacao == N, entao limpa o codigo e item da cobertura 

            if (php[4]!=0 && php[20]=='N') {
                $("#cobertura_id").val(0);
                $("#item_cobertura").val('');
            }
        }
    });
}

function listar_estacao(){
    var data_nascimento = $("#nascimento_animal").val();
    var local = $('#local_id').val();

    if (local==undefined)  {
        var local = $("#codigo_local").val();

        if (local==undefined) {
            var local = 0;
        }
    }

    if (data_nascimento==undefined) {
        data_nascimento='0000-00-00';
    }

    $("select[name=estacao_monta]").html('');

    $.post("lista_estacao_monta_nascimento.php", {local:local, data_nascimento: data_nascimento}, function(valor){
        $("select[name=estacao_monta]").html(valor);
    });
}

// Selecionar outra femea, saida do modal selecionar estacao #modal_estacao
function selecinarOutraFemea(){
    $("#codigo_mae_consulta").val('');
    $("#codigo_mae_animal").val('');
    $("#codigo_pai_animal").val('000000000');
    $('#modal_estacao').modal('hide'); 
    document.getElementById("codigo_mae_consulta").style.borderColor = "red";
    $(".desc_novo_nascimento").html('');
    return;
}

// Confirma a estacao de monta do modal selecionar estacao #modal_estacao
function confirmaEstacao() {
    var estacao = $("#estacao_monta").val();

    if (estacao=='000000000') {
        alert ('Selecione a Estação de Monta');
        return;
    }
    else {
        var options = $('#estacao_monta option:selected');

        $(options).each(function(){
            var desc = $(this).bind('#estacao_monta').text();
            $("#ultima_estacao").text(desc);
            $(".icon_nascimentos_previstos").hide();
        });

        $("#estacao_monta_id").val(estacao);
        $('#modal_estacao').modal('hide');

        // zerar esses campos para estacao monta natural quando vier do modal_estacao 
        $("#tipo_gravacao").val(0);
        $("#cobertura_id").val(0);
        $("#raca_id").val('');
        $("#codigo_pai_animal").val('000000000');
        $(".desc_novo_nascimento").html('Monta Natural para esse nascimento.');

        return;
    }
}

function alterardiagnostico() {
    var local = $("#local_id").val();
    var id_estacao =  $("#estacao_monta_id").val();
    var desc_estacao = $("#ultima_estacao").html();
    $(".desc_novo_nascimento").html('');

    location.href='form_cobertura_animais_diagnostico.php?local=' + local + 
    '&id_estacao=' + id_estacao + 
    '&estacao=' + desc_estacao +
    '&diagnostico=' + 'N';
}

function nascimento_gemelar() {
    $(".desc_novo_nascimento").html('Nascimento Gemelar.');
}

function voltarModalEstacao() {
    $("#codigo_mae_consulta").val('');
    $("#codigo_mae_animal").val('');
    $("#codigo_pai_animal").val('000000000');
    $('#modal_estacao').modal('hide'); 
    document.getElementById("nascimento_animal").style.borderColor = "red";
    $(".desc_novo_nascimento").html('');
    return;

}

function fecharModalEstacao() {
    $("#codigo_mae_consulta").val('');
    $("#codigo_mae_animal").val('');
    $("#codigo_pai_animal").val('000000000');
    $('#modal_estacao').modal('hide'); 
    document.getElementById("codigo_mae_consulta").style.borderColor = "red";
    $(".desc_novo_nascimento").html('');
    return;
}

function confirmar_nascimento() {
    var opcao_nascimento = $("input[name='opcao_nascimento']:checked").val();
    var tipo_gravacao = $("#tipo_gravacao").val();
    var cobertura_id = $("#cobertura_id").val();
    var tipo_cobertura = $("#tipo_cobertura").val();
    var data_prenhes = $("#data_prenhes").val();

    if (tipo_gravacao==1 || tipo_gravacao==2) {
        gravar_nascimento();
        return;
    }
    else if (opcao_nascimento!='N') {
        gravar_aborto_natimorto(); 
        return;
    }

    if ((tipo_cobertura == 'M' && data_prenhes == '') || cobertura_id==0) {
        gravar_nascimento_monta_natural();
        return;
    }
 
    var dias_nascimento = $("#dias_nascimento").val();

    if (dias_nascimento>=252 && dias_nascimento<=303) {
        var nascimento_ok = true;
    }
    else {
        var nascimento_ok = false;
    }

    if (!nascimento_ok) {
        $("#calculo_dias_nascimento").html(dias_nascimento);

        var grupo_usuario = $("#grupo_usuario").val();
        var controle_estoque = $("#controle_estoque").val();
        var tipo_cobertura = $("#tipo_cobertura").val();

        if (tipo_cobertura=='C') {
            $("#tem_estacao").html('Estação ');
            $("#estacao_nascimento").html($("#ultima_estacao").html() +': ');
        }
        else {
            $("#tem_estacao").html('');
            $("#estacao_nascimento").html('');
        }

        $(".mens_dias_gestacao").hide();
        $(".mens_alterar_prenhes").hide();

        if ((grupo_usuario==01 || grupo_usuario==03) && controle_estoque=='I') {
            $(".gravar").show();
            if (tipo_cobertura=='C') {
                $(".substituir").show();
            }
            else {
                $(".mens_alterar_prenhes").show();
                $(".substituir").hide();
            }
        }
        else if (controle_estoque=='I') {
            $(".mens_dias_gestacao").show();
            $(".gravar").hide();
            $(".substituir").hide();
        }     
        else {
            $(".gravar").show();
            $(".substituir").show();
        }   
        
        $('#nascimento_erro').modal('show');
    }
    else {
        gravar_nascimento();
    }
}

function gravar_nascimento() {
    var tipo_gravacao = $("#tipo_gravacao").val();
    var opcao_nascimento = $("input[name='opcao_nascimento']:checked").val();
    var cobertura_id = $("#cobertura_id").val();
    var tipo_cobertura = $("#tipo_cobertura").val();

    $('#nascimento_erro').modal('hide');

    if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_animal').serialize();
            $.ajax({
                type: "POST",
                url: 'excluir_nascimento.php',
                data: dados,
                success: function(data){
                    if (data.error) {
                        $("#mensagem_erro").modal();
                        $('#mensagem_erro .modal-title').html('Nascimento - Mensagem');
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
        if (tipo_cobertura == 'M') {
            gravar_nascimento_monta_natural();
            return;
        }

        var dados = $('#form_gravar_animal').serialize();

        $("#aguardar").modal();

        $.ajax({
            type: "POST",
            url: 'gravar_nascimento.php',
            data: dados,
            success: function(data){
                $('#aguardar').modal('hide');

                $('.confirma_gravar').attr('disabled', false);
                $('.confirma_gravar').html('Confirmar');

                if (data.error) {
                    $("#tipo_gravacao").val(0);
                    $("#mensagem_erro").modal();
                    $('#mensagem_erro .modal-title').html('Nascimento - Mensagem');
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    if (tipo_gravacao==1) {
                        $("#mensagem_retorno").modal();
                        $('#mensagem_retorno .modal-title').html('Nascimento - Mensagem');
                        $("#mensagem_retorno .modal-body").html(data.message);
                    }
                    else {
                        $('#id_pasto_destino').val(data.id_pasto);
                        $('#desc_pasto_destino').val(data.descricao_pasto);
                        $('#desc_lote_destino').val(data.descricao_lote);

                        if (data.descricao_lote=='') {
                            $('#modal_nascimento').modal('hide');

                            $("#mensagem_descricao_lote").modal();
                            $("#mensagem_descricao_lote .modal-body").html(data.message + '<br>Necessário incluir a Descrição do Lote para o Pasto: ' + data.descricao_pasto);
                        }
                        else {
                            $("#mensagem_retorno_inclusao").modal();
                            $('#mensagem_retorno_inclusao .modal-title').html('Nascimento - Inclusão');
                            $("#mensagem_retorno_inclusao .modal-body").html(data.message);
                        }
                    }
                }
            }
        });
    }
}

function substituir_por_monta_natural() {
    var codigo_mae = $("#codigo_mae_animal").val();
    var data_nascimento = $("#nascimento_animal").val();

    var opcao_nascimento = $("input[name='opcao_nascimento']:checked").val();

    if (opcao_nascimento=='N') {
        verificar_ultimo_nascimento(codigo_mae, data_nascimento).then(function(resultado) {
            if (resultado<9) {
                //$(`#data_prenhes${id_cobertura}`).val('');
                $('#mensagem_nascimento_nove_meses .modal-body .mensagem_nove_meses').html('Essa fêmea possui bezerro nascido há menos de 9 meses.'); 
                $('#mensagem_nascimento_nove_meses').modal('show'); 
                return;
            }
            else {
                $("#tipo_gravacao").val(3);
                $(".tipo_estacao_monta").html('Monta Natural');
                $("#ultima_estacao").html('');
                $(".icon_nascimentos_previstos").hide(); 
                $("#cobertura_id").val(0);
                $("#tipo_cobertura").val('M');
                $("#data_prenhes").val('');
                $("#codigo_pai_animal").val('000000000');
            }
        })
        .catch(function(erro) {
                $('#mensagem_nascimento_nove_meses .modal-body .mensagem_nove_meses').html('Erro não previsto: ' + erro); 
                $('#mensagem_nascimento_nove_meses').modal('show'); 
                return;
        });
    }
    else {
        $("#tipo_gravacao").val(0);
        $(".tipo_estacao_monta").html('Monta Natural');
        $("#ultima_estacao").html('');
        $(".icon_nascimentos_previstos").hide(); 
        $("#cobertura_id").val(0);
        $("#tipo_cobertura").val('M');
        $("#data_prenhes").val('');
        $("#codigo_pai_animal").val('000000000');
    }
} 

function verificar_ultimo_nascimento(codigo_mae, data_previsao) {
    return new Promise(function(resolve, reject) {
        $.post("ler_animal_femea_ultimo_nascimento.php", {id_animal: codigo_mae, data_previsao: data_previsao}, function(valor) {
            var php = valor.split("<|>");
            resolve(php[0]);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            reject(errorThrown);
        });
    });
}

function fechar_nascimento_nove_meses(){
    $("#F").prop("checked", false);
    $("#M").prop("checked", false);
    $("#codigo_mae_animal").val('');
    $("#codigo_mae_consulta").val('');
    $("#codigo_pai_animal").val('000000000');
    $("#raca_id").val('');
    $("#pelagem_id").val('');
    $("#peso_animal").val('');
    $("#qtd_animal").val('');
    $("#tipo_gravacao").val(0);
    document.getElementById("codigo_mae_consulta").focus();
    document.getElementById("codigo_mae_consulta").style.borderColor = "red";    
}

function gravar_nascimento_monta_natural() {
    $("#tipo_gravacao").val(3);

    var dados = $('#form_gravar_animal').serialize();

    $("#aguardar").modal();

    $.ajax({
        type: "POST",
        url: 'gravar_nascimento.php',
        data: dados,
        success: function(data){
            $('#aguardar').modal('hide');

            $('.confirma_gravar').attr('disabled', false);
            $('.confirma_gravar').html('Confirmar');
            $('#nascimento_erro').modal('hide');

            if (data.error) {
                $("#tipo_gravacao").val(0);
                $("#mensagem_erro").modal();
                $('#mensagem_erro .modal-title').html('Nascimento - Mensagem');
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                if (tipo_gravacao==1) {
                    $("#mensagem_retorno").modal();
                    $('#mensagem_retorno .modal-title').html('Nascimento - Mensagem');
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
                else {
                    $('#id_pasto_destino').val(data.id_pasto);
                    $('#desc_pasto_destino').val(data.descricao_pasto);
                    $('#desc_lote_destino').val(data.descricao_lote);

                    if (data.descricao_lote=='') {
                        $('#modal_nascimento').modal('hide');
                        $("#mensagem_descricao_lote").modal();
                        $("#mensagem_descricao_lote .modal-body").html(data.message + '<br>Necessário incluir a Descrição do Lote para o Pasto: ' + data.descricao_pasto);
                    }
                    else {
                        $("#mensagem_retorno_inclusao").modal();
                        $('#mensagem_retorno_inclusao .modal-title').html('Nascimento - Inclusão');
                        $("#mensagem_retorno_inclusao .modal-body").html(data.message);
                    }
                }
            }
        }
    });
}

function gravar_aborto_natimorto() {
    var tipo_gravacao = $("#tipo_gravacao").val();

    if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_animal').serialize();
            $.ajax({
                type: "POST",
                url: 'excluir_nascimento_aborto_natimorto.php',
                data: dados,
                success: function(data){
                    if (data.error) {
                        $("#mensagem_erro").modal();
                        $('#mensagem_erro .modal-title').html('Aborto/natimorto - Mensagem');
                        $("#mensagem_erro .modal-body").html(data.message);
                    }
                    else if (data.success){
                        $("#mensagem_retorno").modal();
                        $('#mensagem_retorno .modal-title').html('Aborto/natimorto');
                        $("#mensagem_retorno .modal-body").html(data.message);
                    }
                }
            });
        }
    }
    else {
        var dados = $('#form_gravar_animal').serialize();
        $.ajax({
            type: "POST",
            url: 'gravar_nascimento_aborto_natimorto.php',
            data: dados,
            success: function(data){
                $(".confirma_gravar").attr("disabled", false);
                $('.confirma_gravar').html('Confirmar');

                if (data.error) {
                    $("#mensagem_erro").modal();
                    $('#mensagem_erro .modal-title').html('Aborto/natimorto - Mensagem');
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else {
                    if (tipo_gravacao==1) {
                        $("#mensagem_retorno").modal();
                        $('#mensagem_retorno .modal-title').html('Aborto/natimorto - Mensagem');
                        $("#mensagem_retorno .modal-body").html(data.message);
                    }
                    else {
                        $("#mensagem_retorno_inclusao").modal();
                        $('#mensagem_retorno_inclusao .modal-title').html('Aborto/natimorto - Mesagem');
                        $("#mensagem_retorno_inclusao .modal-body").html(data.message);
                    }
                }
            }
        });
    }
}

function fechar_erro_gravar() {
    var codigo_mae = $("#codigo_mae_animal").val();

    if (codigo_mae=='' || codigo_mae==0) {
        $("#codigo_mae_consulta").val('');
        $("#codigo_pai_animal").val('000000000');
        document.getElementById("codigo_mae_consulta").focus();
        document.getElementById("codigo_mae_consulta").style.borderColor = "red";
    }

    $("#mensagem_erro").modal('hide');
}

function fechar_nascimento_erro(){
    $("#F").prop("checked", false);
    $("#M").prop("checked", false);
    $("#codigo_mae_animal").val('');
    $("#codigo_mae_consulta").val('');
    $("#codigo_pai_animal").val('000000000');
    $("#raca_id").val('');
    $("#pelagem_id").val('');
    $("#peso_animal").val('');
    $("#qtd_animal").val('');
    $("#tipo_gravacao").val(0);
    $('.confirma_gravar').attr('disabled', false);
    $('.confirma_gravar').html('Confirmar');
    $('#nascimento_erro').modal('hide');
}

function volta_nascimento_mensagem(){
    $('#nascimento_gemelar').modal('hide');
    $('#nascimento_aborto_natimorto').modal('hide');
    $("#codigo_mae_consulta").val('');
    $("#codigo_mae_animal").val('');
    $("#codigo_pai_animal").val('000000000');
    $("#cobertura_id").val('');
    $("#item_cobertura").val('');
    $("#tipo_gravacao").val(0);
}

// Fim das inclusoes em 03/08/2022

function allowDrop(ev){
    ev.preventDefault();
}

function drag(ev, divPasto){
    ev.dataTransfer.setData("text", ev.target.id);


    if (divPasto == null){
        return;
    }
    var nomePasto = divPasto.getElementsByTagName('strong')[0].innerHTML;
    ev.dataTransfer.setData("nome", nomePasto);
}

function drop(ev, id, elemento){
    ev.preventDefault();
    clearInterval(autoScrollInterval);
    var id_target = id;
    var nome = ev.dataTransfer.getData("nome");
    var nomeRecebe = elemento.getElementsByTagName('strong')[0].innerHTML;
    var id_pai = ev.dataTransfer.getData("text");

    var id_pasto_destino = id_target.replaceAll('"', '');
    $("#id_pasto_destino").val(id_pasto_destino);

    $("#desc_pasto_destino").val(nomeRecebe);
    $("#id_entrada").val(id_target);
    $("#id_saida").val(id_pai);

    /*  verifica se o pasto destino esta vazio
    Se sim
    Premissa 1 - Levar todos os animais para outro pasto vazio:
                 Mover a Descrição do Lote para o Pasto Destino
                 Limpar a Descrição do Lote do Pasto Origem
                 Mover a nutrição do dia para pasto Destino 
    Se não
    Premissa 6 - Levar todos os animais para outro pasto com animais:
                 Exibir Mensagem para a Descrição do Lote do Pasto Destino
                 Limpar a Descrição do Lote do Pasto Origem
                 Mover a nutrição do dia para pasto Destino
    */

    $.ajax({
        type: 'post',
        url: 'ler_pasto_destino.php',
        data: {
            'id_destino': id_target
        },
        success: function(data){
            if (data.message=='' || data.message==null) {
                    $("#pasto_destino_estava_vazio").val('S');
                }
                else {
                    $("#pasto_destino_estava_vazio").val('N');

            /*    $("#modal_mover_todos_tabuleiro").modal(); 

                $(".modal-body #primeira_mensagem").html("Mover TODOS os animais do pasto "+nome+" para o pasto "+nomeRecebe+"?");
                $(".modal-body #segunda_mensagem").html("");
            }
            else {
                var elem = document.getElementById("primeira_mensagem");
                elem.style.fontWeight = 'normal';

                var elem = document.getElementById("segunda_mensagem");
                elem.style.color = 'red';
                elem.style.fontWeight = 'bold';

                $("#modal_mover_todos_tabuleiro").modal(); 
                $(".modal-body #primeira_mensagem").html("Mover TODOS os animais do pasto "+nome+" para o pasto "+nomeRecebe+"?");
                $(".modal-body #segunda_mensagem").html("Será mantida a DESCRIÇÃO DO LOTE do pasto "+nomeRecebe+".");
            */
            }

            if (id_target.replace(/[^0-9]/g, '')!=id_pai.replace(/[^0-9]/g, '')) {
                $("#modal_mover_todos_tabuleiro").modal(); 
                $(".modal-body #primeira_mensagem").html("Mover TODOS os animais do pasto "+nome+" para o pasto "+nomeRecebe+"?");
            }
        }
    });

    /*$.ajax({
        type: 'POST',
        url: 'ler_nutricao_transferencia.php',
        data: {
            'pasto': id_pai
        },
        success: function(data) {
            if(data > 0 && nome != nomeRecebe){
                if(confirm("Deseja mover as nutrições referentes ao dia de hoje junto com os animais?")){
                    if(confirm("Deseja mover todos os animais do pasto "+nome+" para o pasto "+nomeRecebe+"?")){
                        $.ajax({
                            type: 'post',
                            url: 'transferir_tudo_mapa_gados.php',
                            data: {
                                'id_entrada': id_target,
                                'id_saida': id_pai,
                                'transfere': 1
                            },
                            success: function(data) {
                                consultar_mapa();
                            }
                        });
                    }
                }else{
                    if(confirm("Deseja mover todos os animais do pasto "+nome+" para o pasto "+nomeRecebe+"?")){

                        $.ajax({
                            type: 'post',
                            url: 'transferir_tudo_mapa_gados.php',
                            data: {
                                'id_entrada': id_target,
                                'id_saida': id_pai,
                                'transfere': 0
                            },
                            success: function(data) {
                                consultar_mapa();
                            }
                        });
                    }
                }
            }else if(nome != nomeRecebe){
                if(confirm("Deseja mover todos os animais do pasto "+nome+" para o pasto "+nomeRecebe+"?")){

                    $.ajax({
                        type: 'post',
                        url: 'transferir_tudo_mapa_gados.php',
                        data: {
                            'id_entrada': id_target,
                            'id_saida': id_pai,
                            'transfere': 0
                        },
                        success: function(data) {
                            alert (data.message);
                            consultar_mapa();
                        }
                    });
                }
            }
        }
    });*/
}

function gravar_retirar_tudo_tabuleiro() {
    var id_target = $("#id_entrada").val();
    var id_pai = $("#id_saida").val();

    $.ajax({
        type: 'post',
        url: 'transferir_tudo_mapa_gados.php',
        data: {
            'id_entrada': id_target,
            'id_saida': id_pai
        },
        success: function(data) {
            if (data.error) { 
                $('#modal_mover_todos_tabuleiro').modal('hide');  
                $("#mensagem_erro").modal(); 
                $("#mensagem_erro .modal-body").html(data.message);
                return;
            }
            else if (data.success) {
                $('#modal_mover_todos_tabuleiro').modal('hide');            
                $("#desc_lote_destino").val(data.descricao_lote_pasto_destino);
                $("#descricao_lote").val(data.descricao_lote_pasto_origem);
                //$("#mensagem_sucesso_mover_animais").modal(); 
                //$("#mensagem_sucesso_mover_animais .modal-body").html(data.message);
                exibe_opcoes_desc_lote_pasto_destino_tabuleiro();
            }
        }
    });
}

function animal_sem_id(){
    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='L') {
        $(".id_animal").hide();
        $("div.info_modal_morte").show();
    }
    else {
        $(".id_animal").show();
        $("div.info_modal_morte").hide();
    }
}

function salvar_morte(){
    var controle_estoque = $("#controle_estoque").val();
    var animal_codigo_id = $("#codigo_id_morte").val();
    var codigo_animal = $("#id_animal_morte").val();

    if (animal_codigo_id==0 && controle_estoque=='I'){
        $("#mensagem_erro_animal_filtro").modal();
        $("#id_animal_morte").val('');
        document.getElementById("id_animal_morte").focus();
        document.getElementById("id_animal_morte").style.borderColor = "red";
        return;
    }

    var motivo_morte = $("#motivo_morte").val();

    if (motivo_morte==0){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe o Motivo da Morte!');
        $(".alert_erro_animal").show();
        return;
    }
    else {
        var options = $('#motivo_morte option:selected');

        $(options).each(function(){
            var motivo_morte = $(this).bind('#motivo_morte').text();
            $("#motivo_animal_morte").val(motivo_morte.trim());
            $("#codigo_motivo_morte").val($("#motivo_morte").val());
        })
    }

    var pasto_morte = $("#pasto_origem").val();

    if (pasto_morte==0){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe o Pasto!');
        $(".alert_erro_animal").show();
        return;
    }

    var categoria_morte = $("#categoria_digitada_morte").val();

    if (categoria_morte==0 && controle_estoque=='L'){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Categoria!');
        $(".alert_erro_animal").show();
        return;
    }

    var sexo_morte = $("#sexo_morte").val();

    if (sexo_morte=='' && controle_estoque=='L') {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe o Sexo!');
        $(".alert_erro_animal").show();
        return;
    }

    if (controle_estoque=='I') {
        var data_nascimento = $('#nascimento_animal_morte').val();
        var data_morte = $('#data_morte_animal').val();

        var dia  = data_nascimento.split('/')[0];
        var mes  = data_nascimento.split('/')[1];
        var ano  = data_nascimento.split('/')[2];

        var data_nascimento = ano + '-' + mes + '-' + dia;

        if (data_morte<data_nascimento){
            $('#mensagem_erro').modal();
            $('#mensagem_erro .modal-body').html('A Data da Morte não pode ser menor que a Data do Nascimento.');
            document.getElementById('data_morte_animal').focus();
            return;
        }
    }

    gravar_morte();
};

function gravar_morte() {

    var array_tabela_itens = new Array();
    var valor = new Array();
    var grupo_itens = "";
 
    var codigo = $("#id_animal_morte").val();
    var motivo_morte = $("#codigo_motivo_morte").val();
    var controle_estoque = $("#controle_estoque").val();

    if (codigo!='' && controle_estoque=='I'){
        valor[0]=$("#id_animal_morte").val();
        valor[1]=0;

        if ($("#sexo_animal_morte").val()=='Macho') {
            valor[2]='M';
        }
        else {
           valor[2]='F';
        }
        
        valor[3]=$("#nascimento_animal_morte").val();
        valor[4]=$("#raca_animal_morte").val();
        valor[5]=$("#pelagem_animal_morte").val();
        valor[6]=$("#mae_animal_morte").val();
        valor[7]=$("#observacao_morte").val();
        valor[8]=$("#codigo_id_morte").val();
        valor[9]=$("#motivo_animal_morte").val();
        valor[10]=$("#codigo_motivo_morte").val();
        valor[11]=$('#pasto_origem').val();
        valor[12]=$("#categoria_digitada_morte").val();

        var tabela_itens=valor.join("|");
        array_tabela_itens.push(tabela_itens);
        grupo_itens=array_tabela_itens.join("<|>");
    }
    else if (motivo_morte!=0 && controle_estoque=='L'){
        valor[0]=$("#id_animal_morte").val();
        valor[1]=0;
        valor[2]=$("#sexo_morte").val();
        valor[3]=$("#nascimento_animal_morte").val();
        valor[4]=$("#raca_animal_morte").val();
        valor[5]=$("#pelagem_animal_morte").val();
        valor[6]=$("#mae_animal_morte").val();
        valor[7]=$("#observacao_morte").val();
        valor[8]=$("#codigo_id_morte").val();
        valor[9]=$("#motivo_animal_morte").val();
        valor[10]=$("#codigo_motivo_morte").val();
        valor[11]=$('#pasto_origem').val();
        valor[12]=$("#categoria_digitada_morte").val();

        var tabela_itens=valor.join("|");
        array_tabela_itens.push(tabela_itens);
        grupo_itens=array_tabela_itens.join("<|>");
    }

    $("#array_itens").val(grupo_itens);

    var dados = $('#form_gravar_morte').serialize();

    $.ajax({
        type: "POST",
        url: 'gravar_morte.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else{
                $("#mensagem_retorno_morte").modal();
                $("#mensagem_retorno_morte .modal-body").html(data.message);
            }
        }
    });
}

function voltar(){

    var local = $('#local_origem').val();

    $.redirect('form_mapa_gados.php', {'mapa_local_id': local});
}

/*function distribuirNutricao(){
    $("#divNutricao").show();
    $.ajax({
        type: "POST",
        url: "ler_score_cocho.php",
        success: function(data){
            $("#slctCocho").html(data);
        }
    });

    $.ajax({
        type: "POST",
        url: "ler_produto_nutricao.php",
        success: function(data){
            $("#nomeProduto").html(data);
        }
    });
}*/

function limpa_campos_inclusao() {
    $("#nomeProduto").val('000000000');
    $("#qtdProduto").val('');    
    $("#undProduto").val('');   
} 

function confirma_nutricao(){
    if($("#slctCocho").val() != '000000000' && $("#dataNutricao").val() != '' && $("#nomeProduto").val() != '000000000' && $("#qtdProduto").val() != '' && $("#undProduto").val() != ''){
        var qtdProduto = $("#qtdProduto").val();

        if (verifica_virgula(qtdProduto)==',') {
            qtdProduto = replace_valor(qtdProduto);
        }

        var dispositivo = $("#dispositivo").val();
        var id_lote = $("#id_lote").val();
        var ano_lote = $("#ano_lote").val();

        if (dispositivo=='D') {
            var descricao_lote = $("#descricao_lote_d").val();
        }
        else {
            var descricao_lote = $("#descricao_lote_m").val();
        }

        $(".confirma_nutricao").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: "gravar_nutricao.php",
            data: {
                tipoGravacao: "0",
                local : $("#local_origem").val(),
                pasto : $("#pasto_origem").val(),
                dataNutricao : $("#dataNutricao").val(),
                idProduto : $("#nomeProduto").val(),
                qtdProduto : qtdProduto,
                undProduto : $("#undProduto").val(),
                qtdAnimais: $("#totalAnimais").val(),
                idCocho: $("#slctCocho").val(),
                lote: descricao_lote,
                id_lote: id_lote,
                ano_lote: ano_lote
            },
            success: function(data){
                if (data.error) {
                    $(".confirma_nutricao").attr("disabled", false);
                    $("#mensagem_erro_data").modal();
                    $("#mensagem_erro_data .modal-body").html(data.message);
                }
                else if (data.success){
                    $(".confirma_nutricao").attr("disabled", false);
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                    lerNutricao();
                }
            }
        });
    }
    else{
        $("#mensagem_erro_data").modal();
        $("#mensagem_erro_data .modal-body").html("Preencha todos os campos da nutrição!");
        return;
    }
}

function enviarLixeira(idNutricao){
    id = idNutricao;
    if(confirm("Tem certeza que deseja enviar o registro para a lixeira?")){
        $.ajax({
            type: "POST",
            url: "gravar_nutricao.php",
            data: {
                tipoGravacao: "2",
                idRegistro: id
            },
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                    lerNutricao();
                }
            }
        });
    }
}

function selecionouCocho(){
    if($("#slctCocho").val() != "000000000"){
        $("#dtNutricao").show();
    }else{
        $("#dtNutricao").hide();
    }
}

function selecionouProduto(){
    if($("#nomeProduto").val() != "000000000"){
        $.ajax({
            type: "POST",
            url: "ler_unidade_produto.php",
            data: {'idProduto': $("#nomeProduto").val()},
            success: function(data){
                $("#undProduto").val(data);
            }
        });
    }else{
        $("#undProduto").val("");
    }
}

function fecharNutricao(){
    $("#slctCocho").val('000000000');
    $("#nomeProduto").val('000000000');
    $("#qtdProduto").val('');
    $("#undProduto").val('');
    needToConfirm = false;

    //$("#divNutricao").hide();
    //$("#dtNutricao").hide();
}

function lerNutricao(){
    const date = new Date();
    const data_atual = date.getFullYear()+'-'+
                      (date.getMonth()+1).AddZero()+'-'+
                      date.getDate().AddZero();

    const dataNutricao = $('#dataNutricao').val();

    if (dataNutricao=='') {
        $('#dataNutricao').val(data_atual);
    }

    $.ajax({
        type: "POST",
        url: "ler_itens_nutricao.php",
        data: {
            'local': $("#local_origem").val(),
            'pasto': $("#pasto_origem").val(),
            'data': $("#dataNutricao").val()
        },
        success: function(data){
            $("#tabelaProdutos").html(data);
        }
    });
}

$(document).ready(function(){
    if($("#codigo_local").val() != ""){
        consultar_mapa();
    }
});


$(document).ready(function () {
    if (window.innerWidth <= 768) {
        const $divMapa = $("#consulta_contas");
        const scrollThreshold = 50;
        const scrollSpeed = 20;
        
        $divMapa.on("dragover", function (ev) {
            const mouseY = ev.originalEvent.clientY;
            const divOffset = $divMapa.offset();
            const divHeight = $divMapa.outerHeight();
            const divTop = divOffset.top;
            const divBottom = divTop + divHeight;

            if (mouseY > divTop && mouseY < divBottom) {
                if (mouseY < (divTop + scrollThreshold)) {
                    clearInterval(autoScrollInterval);
                    autoScrollInterval = setInterval(() => {
                        $divMapa.scrollTop($divMapa.scrollTop() - scrollSpeed);
                    }, 16);
                } else if (mouseY > (divBottom - scrollThreshold)) {
                    clearInterval(autoScrollInterval);
                    autoScrollInterval = setInterval(() => {
                        $divMapa.scrollTop($divMapa.scrollTop() + scrollSpeed);
                    }, 16);
                } else {
                    clearInterval(autoScrollInterval);
                }
            } else {
                clearInterval(autoScrollInterval);
            }
        });

        $divMapa.on("dragleave dragend drop", function () {
            clearInterval(autoScrollInterval);
        });
    }
});

// 
//responsivo

$(document).ready(function() {
    if (window.innerWidth <= 685) {
        $("ol.breadcrumb").css({"width": "100%", "text-align": "center"});
        $("ol.breadcrumb li#fazendas-select label").hide();
        //$(".mobile").show();
        //$(".desktop").hide();
    }else {
        $("ol.breadcrumb li#fazendas-select label").show();
        $("ol.breadcrumb").css({"width": "100%", "text-align": "left"});
        //$(".mobile").hide();
        //$(".desktop").show();
    }
});


$(document).ready(function(){
    if (window.innerWidth <= 685) 
        $(".voltar").addClass('input-lg'),
        $(".novo_pasto").addClass('input-lg'),
        $(".categoria_sexo").addClass('input-lg'),
        $(".quantidade").addClass('input-lg'),
        $(".confirma").addClass('input-lg'),
        $(".outras_atividades").addClass('input-lg'),
        $(".descricao_lote").addClass('input-lg'),

        $("#descricao_principal").addClass('input-lg'),
        $("#situacao_principal").addClass('input-lg'),
        $("#data_paricao_principal").addClass('input-lg'),
        $("#data_paricao_principal_mais").addClass('input-lg'),
        $(".confirma_composicao").addClass('input-lg'),
        $(".voltar_descricao_lote").addClass('input-lg'),
        $(".mobile").show(),
        $(".desktop").hide(),
        $("#dispositivo").val('M');
    else 
        $(".voltar").removeClass('input-lg'),
        $(".novo_pasto").removeClass('input-lg'),
        $(".categoria_sexo").removeClass('input-lg'),
        $(".quantidade").removeClass('input-lg'),
        $(".outras_atividades").addClass('input-lg'),
        $(".descricao_lote").removeClass('input-lg'),

        $("#descricao_principal").removeClass('input-lg'),
        $("#situacao_principal").removeClass('input-lg'),
        $("#data_paricao_principal").removeClass('input-lg'),
        $("#data_paricao_principal_mais").removeClass('input-lg'),
        $(".confirma_composicao").removeClass('input-lg'),
        $(".voltar_descricao_lote").removeClass('input-lg'),
        $(".mobile").hide(),
        $(".desktop").show(),
        $("#dispositivo").val('D');
});

$(document).ready(function() {
    if (window.innerWidth <= 685) 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select, .modal-body form .tab-content #dados .row .form-group button").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select, .modal-body form .tab-content #dados .row .form-group button").removeClass('input-lg');
});

$(document).ready(function() {
    if (window.innerWidth <= 375) {
        var altura = document.querySelector(".consulta_contas");
        altura.style.height = "300px";
    }
    /*else {
        var altura = document.querySelector(".consulta_contas");
        altura.style.height = "400px";
    }*/
});

$(window).resize(function() {
    if (window.innerWidth <= 459) 
        $("div.item_mapa").addClass('col-xs-4'),
        $("div.item_mapa").css('margin-bottom', '5%');
    else 
        $("div.item_mapa").removeClass('col-xs-4'),
        $("div.item_mapa").css('margin-bottom', '1%');
});

$(window).resize(function() {
    if (window.innerWidth <= 459) 
        $("div.descricao_principal").addClass('form-group'),
        $("div.exibir_parametro_2").addClass('form-group'),
        $("div.exibir_parametro_3").addClass('form-group'),
        $("div.exibir_parametro_4").addClass('form-group'),
        $("div.exibir_parametro_4_data_mais").addClass('form-group');
    else 
        $("div.descricao_principal").removeClass('form-group'),
        $("div.exibir_parametro_2").removeClass('form-group'),
        $("div.exibir_parametro_3").removeClass('form-group'),
        $("div.exibir_parametro_4").removeClass('form-group'),
        $("div.exibir_parametro_4_data_mais").removeClass('form-group');
});

$(window).resize(function() {
    if (window.innerWidth <= 270) 
        $("div.item_mapa").addClass('col-xs-12'),
        $("div.item_mapa").css('margin-bottom', '5%');
    else 
        $("div.item_mapa").removeClass('col-xs-12'),
        $("div.item_mapa").css('margin-bottom', '1%');
});

$(window).resize(function() {
    if (window.innerWidth <= 375) 
        $("div.item_mapa").addClass('col-xs-6'),
        $("div.item_mapa").css('margin-bottom', '5%');
    else 
        $("div.item_mapa").removeClass('col-xs-6'),
        $("div.item_mapa").css('margin-bottom', '1%');
});

$(window).resize(function() {
    if (window.innerWidth <= 462) {
        $("ol.breadcrumb").css({"width": "100%", "text-align": "center"});
        $("ol.breadcrumb li#fazendas-select label").hide();
    }else {
        $("ol.breadcrumb li#fazendas-select label").show();
        $("ol.breadcrumb").css({"width": "100%", "text-align": "left"});
    }
        
});

$(window).resize(function(){
    if(window.innerWidth <= 400){
        $("#divTotalAnimais").show();
        $("#totalAnimais").hide();
    }else{
        $("#divTotalAnimais").hide();
        $("#totalAnimais").show();
    }
});

$(document).ready(function(){
    if(window.innerWidth <= 400){
        $("#divTotalAnimais").show();
        $("#totalAnimais").hide();
    }else{
        $("#divTotalAnimais").hide();
        $("#totalAnimais").show();
    }
});


/* $(window).resize(function() {
    if (window.innerWidth <= 1199) 
        $('a.link').css({'margin-top': '3%'});
    else 
    $('a.link').css({'margin-top': '0'});
}); */

$(window).resize(function() {
    if (window.innerWidth <= 1370)
        $('#span_retirar').removeClass('col-lg-8'),
        $('#span_retirar').addClass('col-lg-12');
    else 
        $('#span_retirar').removeClass('col-lg-12'),
        $('#span_retirar').addClass('col-lg-8');
});

$(window).resize(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
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

function fechar_modal() {
    $('#modal_nascimento').modal('hide');
    $('#modal_morte').modal('hide');
    $('#modal_nutricao').modal('hide');
}

Number.prototype.AddZero= function(b,c){
    var  l= (String(b|| 10).length - String(this).length)+1;
    return l> 0? new Array(l).join(c|| '0')+this : this;
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


function formatMoney(n, c, d, t) {
  c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
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

/** permite digitar somente numeros nos campos numericos */
function numeros(field, event) {
    var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;

    if ((keyCode >= 48 && keyCode <= 57) || (keyCode == 8) || (keyCode == 9) || (keyCode == 13) || (keyCode == 46)) {
        if (keyCode == 13) {
            var i;
            for (i = 0; i < field.form.elements.length; i++)
                if (field == field.form.elements[i])
                    break;
            i = (i + 1) % field.form.elements.length;
            field.form.elements[i].focus();
            return false;
        } else
            return true;
    } else {
        return false;
    }
}

function desabilita_enter (field, event) {
    var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;

    if (keyCode == 13) {
        var i;
        for (i = 0; i < field.form.elements.length; i++)
            if (field == field.form.elements[i])
                break;
                i = (i + 1) % field.form.elements.length;
                field.form.elements[i].focus();
                return false;
        } 
    else
                return true;
}      

function adicionaZero(numero){
    if (numero <= 9) 
        return "0" + numero;
    else
        return numero; 
}

