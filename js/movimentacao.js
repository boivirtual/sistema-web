/**TABELA DE MOVIMENTACAO*/
let controle_estoque = $("#controle_estoque").val();
let divFiltroReproducaoVisivel = false;

window.addEventListener("load", function(event) {
    // Exibe filtros FAZENDA quando faz reload
    var filtro_local = $("#exibe_local").val();
 
    if (filtro_local!='' && filtro_local!=null) {
        var filtro_local = filtro_local.split(',');

        $.each(filtro_local, function(idx, val) {
            $('#codigo_local option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_local').selectpicker('refresh');
    }

    $('.mens_reprodutor').hide();

    /*$.post("lista_estacao_monta_descricao.php", {}, function(valor){
        $("select[name=codigo_estacao_filtro]").html(valor);
    });*/

    var voltar_movimentacao = $("#voltar_movimentacao").val();
    var ultimo_cliente_cadastrado = $("#ultimo_cliente_cadastrado").val();
    var controle_estoque = $("#controle_estoque").val();

    if (voltar_movimentacao==5) {
        $(".selecionar_pesagem").hide();
        $(".incluir_espacos").hide();
        $(".filtro_movimentacao").show();
        $(".listar_animais_transferencia").show();
        $(".mais_opcoes").hide();
        $(".entrada_rapida").hide();

        exibe_filtro();

        var compra = document.getElementById('compra');
        compra.checked = true;

        var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();

        if (tipo_movimentacao=='C') {
            $(".incluir_mais_origem").show();
            $(".incluir_mais_destino").hide();
        }
        else if (tipo_movimentacao=='V') {
            $(".incluir_mais_origem").hide();
            $(".incluir_mais_destino").show();
        }
        else {
            $(".incluir_mais_origem").hide();
            $(".incluir_mais_destino").hide();
        }

        $.post("lista_local_movimentacao.php", {tipo_movimentacao:tipo_movimentacao, local_origem:1}, function(valor){
            $("select[name=local_origem]").html(valor);
            $("select[name=codigo_local_filtro]").html(valor);
            var ultimo_cliente_cadastrado = $("#ultimo_cliente_cadastrado").val();
            $("#local_origem").val(ultimo_cliente_cadastrado);
        });

        $.post("lista_local_movimentacao.php", {tipo_movimentacao:tipo_movimentacao, local_origem:2}, function(valor){
            $("select[name=local_destino]").html(valor);
        });
    }

    if (voltar_movimentacao==6) {
        $(".selecionar_pesagem").hide();
        $(".incluir_espacos").hide();
        $(".filtro_movimentacao").show();
        $(".listar_animais_transferencia").show();
        $(".mais_opcoes").hide();
        $(".entrada_rapida").hide();

        exibe_filtro();

        var venda = document.getElementById('venda');
        venda.checked = true;

        var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();

        if (tipo_movimentacao=='C') {
            $(".incluir_mais_origem").show();
            $(".incluir_mais_destino").hide();
        }
        else if (tipo_movimentacao=='V') {
            $(".incluir_mais_origem").hide();
            $(".incluir_mais_destino").show();
        }
        else {
            $(".incluir_mais_origem").hide();
            $(".incluir_mais_destino").hide();
        }

        $.post("lista_local_movimentacao.php", {tipo_movimentacao:tipo_movimentacao, local_origem:1}, function(valor){
            $("select[name=local_origem]").html(valor);
            $("select[name=codigo_local_filtro]").html(valor);
        });

        $.post("lista_local_movimentacao.php", {tipo_movimentacao:tipo_movimentacao, local_origem:2}, function(valor){
            $("select[name=local_destino]").html(valor);
            var ultimo_cliente_cadastrado = $("#ultimo_cliente_cadastrado").val();
            $("#local_destino").val(ultimo_cliente_cadastrado);
        });
    }

    var lista_movimentacao_automatico = $("#lista_movimentacao_automatico").val();

    if (lista_movimentacao_automatico=="S") {
        consultar();
    }

   // LER ITENS MOVIMENTACAO NA EDIÇÃO
   
    var numero_doc=$("#numero_movimentacao_id").text();

    $.post("ler_itens_movimentacao_animais.php", {numero_doc: numero_doc}, function (dados_retorno){
        if (dados_retorno!=0) {

            var txt = dados_retorno;
            var php = txt.split("<|>");

            var numero_itens = php.length;

            var controle_estoque=$("#controle_estoque").val();

            if (controle_estoque=='I') {
                html = "";
                html += '<table class="table table-striped table-advance table-hover" id="tabela_itens_digitados" width="100%">';
                html += '<thead>';
                html += '<tr>';
                html += '<th>' + ' Id' + '</th>';
                html += '<th>' + ' Peso' + '</th>';
                html += '<th>' + ' Sexo' + '</th>';
                html += '<th>' + ' Nascimento' + '</th>';
                html += '<th>' + ' Raça' + '</th>';
                html += '<th>' + ' Pelagem' + '</th>';
                html += '<th>' + ' Mãe' + '</th>';
                html += '<th>' + ' Observação' + '</th>';
                html += '<th>' + ' <i class="icon_cogs"></i> Ações' + '</th>';
                html += '<th  hidden="">' + ' Id Animal' + '</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                for (var i = 0; i < numero_itens; i++) {
                    var itens = php[i].split("|");

                    var codigo_id = itens[0];
                    var codigo_animal = itens[1];
                    var peso = itens[2];
                    var sexo= itens[3];
                    var nascimento = itens[4];
                    var raca = itens[5];
                    var pelagem = itens[6];
                    var mae = itens[7];
                    var observacao = itens[8];

                    html += '<tr>';
                    html +="<td width='12%' class='id_animal'>" + codigo_animal + "</td>";
                    html +="<td width='8%' class='peso_animal'>" + peso + "</td>";
                    html +="<td width='8%' class='sexo_animal'>" + sexo + "</td>";
                    html +="<td width='8%' class='nascimento_animal'>" + nascimento + "</td>";
                    html +="<td width='10%' class='raca_animal'>" + raca + "</td>";
                    html +="<td width='8%' class='pelagem_animal'>" + pelagem + "</td>";
                    html +="<td width='8%' class='mae_animal'>" + mae + "</td>";
                    html +="<td width='18%' class='observacao'>" + observacao + "</td>";
                    html +="<td width='8%' hidden='' class='codigo_id'>" + codigo_id + "</td>";
                    html +="<td hidden='' class='excluir'>" + 'N' + "</td>";
                    html +="<td width='12%' class='botoes'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnexcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='excluir'></i></a></div></td>";
                    html += '</tr>';
                }

                html += '</tbody>';
                html += '</table>';
                document.getElementById('tabela_itens_digitados').innerHTML = html;
                 
                $(".btnEditar").bind("click", modal_editar_item);
                $(".btnexcluir").bind("click", excluir_edicao);
            }
            else {
                html = "";
                html += '<table class="table table-striped table-advance table-hover" id="tabela_itens_digitados" width="100%">';
                html += '<thead>';
                html += '<tr>';
                html += '<th>' + ' Categoria' + '</th>';
                html += '<th>' + ' Qtde' + '</th>';
                html += '<th>' + ' Sexo' + '</th>';
                html += '<th>' + ' Peso Kg' + '</th>';
                html += '<th>' + ' Peso Médio Kg' + '</th>';
                html += '<th>' + ' Peso @' + '</th>';
                html += '<th>' + ' Peso Médio @' + '</th>';
                html += '<th>' + ' Observação' + '</th>';
                //html += '<th>' + ' <i class="icon_cogs"></i> Ações' + '</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                for (var i = 0; i < numero_itens; i++) {
                    var itens = php[i].split("|");

                    var item = itens[9];

                    var peso = formatMoney(itens[2]);
                    var sexo = itens[3];
                    var item= itens[9];
                    var categoria= itens[10];
                    var peso_medio = formatMoney(itens[11]);
                    var peso_arroba = formatMoney(itens[12]);
                    var peso_medio_arroba = formatMoney(itens[13]);
                    var qtd_animal = itens[14];
                    var desc_categoria = itens[15];
                    var observacao = itens[8];

                    html += '<tr>';
                    html += '<td hidden="" class="item_animal input_forma">' + item + '</td>';
                    html += '<td width="17%" class="desc_categoria input_forma">' + desc_categoria + '</td>';
                    html += '<td width="5%" class="qtd_animal input_forma">' + qtd_animal + '</td>';
                    html += '<td width="5%" class="sexo_animal input_forma">' + sexo + '</td>';
                    html += '<td width="8%" class="peso_animal input_forma">' + peso + '</td>';
                    html += '<td width="12%" class="peso_medio input_forma">' + peso_medio + '</td>';
                    html += '<td width="8%" class="peso_arroba input_forma">' + peso_arroba + '</td>';
                    html += '<td width="12%" class="peso_medio_arroba input_forma">' + peso_medio_arroba + '</td>';
                    html += '<td width="33%" class="observacao input_forma">' + observacao + '</td>';
                    html += '<td hidden="" class="codigo_categoria input_forma">' + categoria + '</td>';
                    html += '</tr>';
                }

                html += '</tbody>';
                html += '</table>';
                document.getElementById('tabela_itens_digitados').innerHTML = html;
                 
                //$(".btnEditar").bind("click", modal_editar_item);
                //$(".btnexcluir").bind("click", excluir_edicao);
            }
        }
    });

    $("#botao_confirma").attr("disabled", true);
    $('#botao_confirma').removeClass('btn-primary').addClass('btn-secondary');
//        document.getElementById('botao_lista').style.backgroundColor = '#ccc';
//        document.getElementById('botao_excel').style.backgroundColor = '#ccc';
//        document.getElementById("botao_lista").style.borderColor = '#ccc';
//        document.getElementById("botao_excel").style.borderColor = '#ccc';
//        document.getElementById('botao_lista').style.color = '#737070';
//        document.getElementById('botao_excel').style.color = '#737070';


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

function informacoes_uso() {
    $("#ajuda").modal();
}

function consultar() {
    var local = $("#codigo_local").val();
    var tipo = $("#tipo_movimentacao").val();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();

    if (data_inicial!=undefined) {
        if (data_inicial > data_final) {
            alert ('Informe a Data Inicial e Final corretamente!');
            return;
        }

        if (local==null) {
            local=[''];
        }

        if (tipo==null) {
            tipo=[''];
        }

        var options = $("#codigo_local option:selected");
        var codigo_local_filtro = [];

        $(options).each(function () {
            var desc = $(this).bind("#codigo_local").text();
            codigo_local_filtro.push(desc.trim());
        });

        if (codigo_local_filtro != "") {
            codigo_local_filtro = "Fazenda: " + codigo_local_filtro + "->";
        } else {
            codigo_local_filtro = "Fazenda: Todas->";
        }

        var options = $("#tipo_movimentacao option:selected");
        var tipo_movimentacao_filtro = [];

        $(options).each(function () {
            var desc = $(this).bind("#tipo_movimentacao").text();
            tipo_movimentacao_filtro.push(desc.trim());
        });

        if (tipo_movimentacao_filtro != "") {
            tipo_movimentacao_filtro = "Tipo Movimentação: " + tipo_movimentacao_filtro + "->";
        } else {
            tipo_movimentacao_filtro = "Tipo Movimentação: Todos->";
        }

        var data_ini = data_inicial.split("-");
        var dia_ini = data_ini[2];
        var mes_ini = data_ini[1];
        var ano_ini = data_ini[0];

        var data_fim = data_final.split("-");
        var dia_fim = data_fim[2];
        var mes_fim = data_fim[1];
        var ano_fim = data_fim[0];
        periodo =
            "Período: de " +
            dia_ini +
            "/" +
            mes_ini +
            "/" +
            ano_ini +
            " ate " +
            dia_fim +
            "/" +
            mes_fim +
            "/" +
            ano_fim;

        var descricao_filtro =
            codigo_local_filtro +
            tipo_movimentacao_filtro +
            periodo;

        $(".digitar_filtros").hide();
        $(".filtros_consulta").show();
        $(".mais_filtros").show();
        $(".menos_filtros").hide();
        $(".consultar").show();
        $(".descricao_filtro").html(descricao_filtro);

        $('#aguardar').modal('show');

        $.post("form_lista_movimentacao.php", {local:local, tipo:tipo, data_inicial:data_inicial, data_final:data_final },
            function(valor){ 
            $('#aguardar').modal('hide');
            $("div[id=lista_movimentacoes]").html(valor);
        });
        return;
    }
    else {
        return;
    }
}

function exibe_mais_filtros() {
    $(".digitar_filtros").show();
    $(".mais_filtros").hide();
    $(".menos_filtros").show();
    $(".consultar").hide();
    $(".lista_contas").hide();
}

function exibe_menos_filtros() {
    $(".digitar_filtros").hide();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".lista_contas").show();
}

$(document).ready(function(){
    $('#tabela_movimentacao').DataTable({
        responsive: true,
        paging: false,
        ordering: true,
        info: true,
        order: [[ 0, "desc" ]],
        language: {
            sSearch: "Busca:",
            zeroRecords: "Nada encontrado",
            info: "Registros encontrados: _END_ ",
            infoEmpty: "Nenhum registro disponível",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
        },
        columnDefs: [
            { type: 'date-br', targets: 0 }
        
        ],
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    // Acende o botão consultar se houver alteracao nos filtros da pesagem
    $('#data_inicial').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });

    $('#data_final').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });

    $('#codigo_local').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });

    $('#tipo_movimentacao').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });

    // Fim acendo botão 

    $('.tipo_movimentacao').click(function(event) {
        var data_movimentacao = $("#data_movimentacao").val();

        if (data_movimentacao == '') {
            $("#compra").prop("checked", false);
            $("#transferencia").prop("checked", false);
            $("#venda").prop("checked", false);
            $("#morte").prop("checked", false);
            $("#outras").prop("checked", false);

            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html("Informe a Data da Movimentação");
            return;
        }

        $(".selecionar_pesagem").hide();
        $(".incluir_espacos").hide();
        $(".filtro_movimentacao").show();
        $(".listar_animais_transferencia").show();
        $(".mais_opcoes").hide();
        $(".entrada_rapida").hide();
        $("#voltar_movimentacao").val('');
        $("#ultimo_cliente_cadastrado").val('000000000');

        var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();

        $(".local_origem").show();

        if (tipo_movimentacao=='C' || tipo_movimentacao=='V' || 
            tipo_movimentacao=='T') {
            $(".local_destino").show();
        }
        else {
            $(".local_destino").hide();
        }

        if (tipo_movimentacao=='C') {
            $(".destino").text('* Fazenda de Destino');
            $(".origem").text('* Local de Origem');
            $(".incluir_mais_origem").show();
            $(".incluir_mais_destino").hide();
        }
        else if (tipo_movimentacao=='V') {
            $(".destino").text('* Local de Destino');
            $(".origem").text('* Fazenda de Origem');
            $(".incluir_mais_origem").hide();
            $(".incluir_mais_destino").show();

            $('#modal_pesagem .modal-title').html('Movimentação - Venda');
            $('#modal_pesagem .titulo_pesagem').html('* A pesagem para a VENDA dos animais foi registrada?');
            $('#modal_pesagem').modal('show');
        }
        else if (tipo_movimentacao=='T') {
            $(".destino").text('* Fazenda de Destino');
            $(".origem").text('* Fazenda de Origem');
            $('#modal_pesagem .modal-title').html('Movimentação - Transferência');
            $('#modal_pesagem .titulo_pesagem').html('* A pesagem para a TRANSFERÊNCIA dos animais foi registrada?');
            $('#modal_pesagem').modal('show');
        }
        else {
            $(".origem").text('* Fazenda de Origem');
            $(".incluir_mais_origem").hide();
            $(".incluir_mais_destino").hide();
        }

        $.post("lista_local_movimentacao.php", {tipo_movimentacao:tipo_movimentacao, local_origem:1}, function(valor){
            $("select[name=local_origem]").html(valor);
            $("select[name=codigo_local_filtro]").html(valor);
        });

        $.post("lista_local_movimentacao.php", {tipo_movimentacao:tipo_movimentacao, local_origem:2}, function(valor){
            $("select[name=local_destino]").html(valor);
        });

        exibe_filtro();
    });

    $("#femea").click(function(){
        var femea = $('#femea');
        
        if (femea.is(":checked")){
            $('.abrir_filtro_reproducao').show();
            $('.filtro_reproducao').hide();
            divFiltroReproducaoVisivel = false;
        }
        else {
            $('.abrir_filtro_reproducao').hide();
            $('.filtro_reproducao').hide();
            divFiltroReproducaoVisivel = false;
        }
        limpar_filtros_reproducao();
        exibe_filtro();
    });

    $("#macho").click(function(){
        var macho = $('#macho');
        var femea = $('#femea');
        
        if (macho.is(":checked") && femea.is(":checked")){
            $('.abrir_filtro_reproducao').show();
            $('.filtro_reproducao').hide();
            divFiltroReproducaoVisivel = false;
        }

        limpar_filtros_reproducao();
        exibe_filtro();
    });

    $("#vacas_paridas").click(function(){
        $("#positivo").prop("checked", false);
        $("#negativo").prop("checked", false);
        $("#iatf").prop("checked", false);
        $("#monta_natural").prop("checked", false);
        $("#codigo_estacao_filtro").empty();
        $("#codigo_estacao_filtro").val([]);
        $('#codigo_estacao_filtro').selectpicker('val', '');
        $('.selectpicker').selectpicker('refresh');
    });

    $("#vacas_solteiras").click(function(){
        $("#positivo").prop("checked", false);
        $("#negativo").prop("checked", false);
        $("#iatf").prop("checked", false);
        $("#monta_natural").prop("checked", false);
        $("#codigo_estacao_filtro").empty();
        $("#codigo_estacao_filtro").val([]);
        $('#codigo_estacao_filtro').selectpicker('val', '');
        $('.selectpicker').selectpicker('refresh');
    });

    $("#vacas_prenhes").click(function(){
        $("#positivo").prop("checked", false);
        $("#negativo").prop("checked", false);
        $("#iatf").prop("checked", false);
        $("#monta_natural").prop("checked", false);
        $("#codigo_estacao_filtro").empty();
        $("#codigo_estacao_filtro").val([]);
        $('#codigo_estacao_filtro').selectpicker('val', '');
        $('.selectpicker').selectpicker('refresh');
    });

    $('#positivo').click(function(){
        $("#vacas_paridas").prop("checked", false);
        $("#vacas_solteiras").prop("checked", false);
        $("#vacas_prenhes").prop("checked", false);

        if ($("#positivo").is(":checked") == false){
            $("#iatf").prop("checked", false);
            $("#monta_natural").prop("checked", false);
            $("#codigo_estacao_filtro").empty();
            $('.selectpicker').selectpicker('refresh');
        }
    });

    $('#negativo').click(function(){
        $("#vacas_paridas").prop("checked", false);
        $("#vacas_solteiras").prop("checked", false);
        $("#vacas_prenhes").prop("checked", false);

        /*if ($("#positivo").is(":checked") == false &&
            $("#negativo").is(":checked") == false ){
            //$("#monta_natural").prop("checked", false);
            //$("#monta_natural").prop("disabled", false);
        }*/

        /*if ($("#negativo").is(":checked") == true ){
            //$("#monta_natural").prop("checked", true);
            //$("#monta_natural").prop("disabled", true);
        }*/
    });

    $("#iatf").click(function(){
        $("#vacas_paridas").prop("checked", false);
        $("#vacas_solteiras").prop("checked", false);
        $("#vacas_prenhes").prop("checked", false);

        if ($("#iatf").is(":checked") == true){
            $("#positivo").prop("checked", true);
            document.getElementById("codigo_estacao_filtro").focus();
            $.post("lista_estacao_monta_descricao.php", {}, function(valor){
                $("select[name=codigo_estacao_filtro]").html(valor);
                $("#codigo_estacao_filtro").val('');
                $('.selectpicker').selectpicker('refresh');
            });
        }

        if ($("#iatf").is(":checked") == false){
            $("#codigo_estacao_filtro").empty();
            $('.selectpicker').selectpicker('refresh');
        }
    });

    $("#monta_natural").click(function(){
        $("#vacas_paridas").prop("checked", false);
        $("#vacas_solteiras").prop("checked", false);
        $("#vacas_prenhes").prop("checked", false);

        if ($("#monta_natural").is(":checked") == true){
            $("#positivo").prop("checked", true);
        }
    });

    $('#codigo_estacao_filtro').on('change', function() {
        $('#codigo_estacao_filtro').closest('.bootstrap-select').removeClass('selectpicker-erro');
    });

    $('#codigo_categoria_filtro').change(function(){
        var categoriasSelecionadas = $("#codigo_categoria_filtro").val(); 

        if (categoriasSelecionadas && categoriasSelecionadas.includes('001') && divFiltroReproducaoVisivel) {
            divFiltroReproducaoVisivel=true;
            $("#mensagem_filtro_reproducao").modal();
            return; 
        }

        aplicar_filtros();
    });
});

function fechar_modal_pesagem() {
    $(".mais_opcoes").hide();
    $(".selecionar_pesagem").hide();
    $(".incluir_espacos").hide();
    $(".local_origem").hide();
    $(".local_destino").hide();
    $(".incluir_mais_destino").hide();
    $("#compra").prop("checked", false);
    $("#transferencia").prop("checked", false);
    $("#venda").prop("checked", false);
    $("#morte").prop("checked", false);
    $("#outras").prop("checked", false);
    $("#peso_registrado").prop("checked", false);
    $("#registrar_peso_sim").prop("checked", false);
    $("#registrar_peso_nao").prop("checked", false);
    $(".filtro_primeira_tela").hide();
}

function confirmar_opcaoes_pesagem() {
    var opcao_pesagem = $("input[name='opcao_pesagem']:checked").val();

    if (opcao_pesagem==undefined) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione uma opção!');
        return;
    }   
    else if (opcao_pesagem=='R') {
        location.href= "form_pesagem_animais_incluir.php";
    }

    $('#modal_pesagem').modal('hide');
}

Number.prototype.AddZero= function(b,c){
    var  l= (String(b|| 10).length - String(this).length)+1;
    return l> 0? new Array(l).join(c|| '0')+this : this;
}

$(document).ready(function(){
    $('#data_movimentacao').blur(function(event) {

        var data_movimentacao = $("#data_movimentacao").val();

        if (data_movimentacao == '') {
            const date = new Date();
            const data_atual = date.getFullYear()+'-'+
                              (date.getMonth()+1).AddZero()+'-'+
                              date.getDate().AddZero();

            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html("A Data precisa ser informada!");
            $("#data_movimentacao").val(data_atual);
            document.getElementById("data_movimentacao").style.borderColor = "#0076d7";

            return;
        }

        var data_digitada = data_movimentacao.split("-");
        var mesano_digitado = data_digitada[0]+data_digitada[1];

        data_hoje = new Date();
        mesano_hoje = data_hoje.getFullYear()+(data_hoje.getMonth()+1).AddZero();

        if (mesano_digitado<mesano_hoje) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('O mês ' + data_digitada[1]+'/'+data_digitada[0]+' já foi encerrado!');
            data_hoje = data_hoje.getFullYear()+'-'+(data_hoje.getMonth()+1).AddZero()+'-'+data_hoje.getDate().AddZero();
            $("#data_movimentacao").val(data_hoje);
            return;
        }
    }); 

    $('#data_movimentacao').change(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const data_movimentacao = $("#data_movimentacao").val();

        if (data_movimentacao>data_atual) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('A Data não pode ser maior que a data atual!');
            $("#data_movimentacao").val(data_atual);
            document.getElementById("data_movimentacao").style.borderColor = "#0076d7";
        }
    });

    $("#data_movimentacao").click(function () {
        document.getElementById("data_movimentacao").style.borderColor = "";
    });

    $('#local_origem').change(function(event) {
        var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();
        var controle_estoque = $("#controle_estoque").val();

        if (tipo_movimentacao!='C') {
            var local = $("#local_origem").val();

            $("#codigo_local_filtro").val('');
            $('#codigo_local_filtro').val(local);

            $.post("ler_pasto_venda.php", {local: local}, function (dados_retorno){
                var php = dados_retorno.split("<|>");
                var qtd_animais_pasto = php[0];

                var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();
                var local = $("#local_origem").val();

                if (qtd_animais_pasto==0 && (tipo_movimentacao=='V' || tipo_movimentacao=='T') && local!='000000000') {
                    $("#local_origem").val('000000000');
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html('Primeiro movimente os animais para a Saída no Mapa de Gado');
                    return
                }
                else {
                    var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();

                    if (tipo_movimentacao=="M") {
                        var local = $("#local_origem").val();

                        $.post("lista_pasto.php", {local:local}, function(valor){
                            $("select[name=pasto_morte]").html(valor);

                            if (controle_estoque=='L') {
                                $(".id_animal").hide();
                                $(".info_modal_morte").show();
                            }
                            else {
                                $(".id_animal").show();
                                $(".info_modal_morte").hide();
                            }

                            $('#modal_morte').modal('show');
                        });
                    }
                    else if (tipo_movimentacao=="O") {
                        var local = $("#local_origem").val();

                        $.post("lista_pasto.php", {local:local}, function(valor){
                            $("select[name=pasto_outra]").html(valor);

                            if (controle_estoque=='L') {
                                $(".id_animal").hide();
                                $(".info_modal_outra").show();
                            }
                            else {
                                $(".id_animal").show();
                                $(".info_modal_outra").hide();
                            }

                            $('#modal_outra_saida').modal('show');
                        });
                    }
                    else if (tipo_movimentacao=="V" || tipo_movimentacao=="T") {
                        var local = $("#local_origem").val();
                        var pasto = 999999999;
                        
                        $.post("lista_pesagem.php", {tipo_movimentacao:tipo_movimentacao, local:local}, function(valor){
                            $("select[name=pesagem]").html(valor);
                            var sexo='T';
                            var categoria=[''];
                            $.post("lista_categoria_pasto.php", {local:local, pasto:pasto, categoria:categoria, sexo:sexo}, function(valor){
                                $("select[name=codigo_categoria_individual]").html(valor);
                            });
                        });
                    }
                }
            });
        }
    });

    $('#local_destino').change(function(event) {
        var local = $("#local_origem").val();

        if (local=='000000000') {
            $("#local_destino").val('000000000');
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Selecione o Local Origem!');
            return
        }

        var controle_estoque = $("#controle_estoque").val();
        var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();
        var controle_estoque = $("#controle_estoque").val();

        if (tipo_movimentacao=="C") {
            var local = $("#local_destino").val();
            if (controle_estoque=='I') {
                document.getElementById('span_odigo_raca_entrada').innerHTML = "*";
                $('.codigo_pelagem_entrada').show();
                $('.sequencia_id').show();
                $(".entrada_rapida").show();
                $(".peso_medio").hide();
            }
            else {
                document.getElementById('span_odigo_raca_entrada').innerHTML = " ";
                $('.codigo_pelagem_entrada').hide();
                $('.sequencia_id').hide();
                $(".peso_medio").show();
                $(".entrada_rapida").show();
                $('#modal_entrada_rapida').modal('show');
            }
        }
        else {
            var local = $("#local_origem").val();
            $.post("lista_pesagem.php", {tipo_movimentacao:tipo_movimentacao, local:local}, function(valor){
                $("select[name=pesagem]").html(valor);
                var opcao_pesagem = $("input[name='opcao_pesagem']:checked").val();
                if (opcao_pesagem=='S') {
                    $(".selecionar_pesagem").show();
                    $(".filtro_movimentacao").hide();
                    $(".listar_animais_transferencia").hide();
                    $(".incluir_espacos").show();
                    $(".desc_pesagem").html('* Selecione uma Pesagem');
                }
                else {
                    $(".selecionar_pesagem").show();
                    $(".filtro_movimentacao").show();
                    $(".listar_animais_transferencia").show();
                    $(".incluir_espacos").hide();
                    $(".desc_pesagem").html('Selecione uma Pesagem');
                }

                $(".mais_opcoes").show();

            });
        }
    });


    $('#codigo_local_filtro').change(function(event) {
        var controle_estoque = $("#controle_estoque").val();

        $("#local_origem").val('');
        var local = $("#codigo_local_filtro").val();
        $('#local_origem').val(local);

        var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();

        if (tipo_movimentacao!='C') {
            var local = $("#local_origem").val();

            $.post("ler_pasto_venda.php", {local: local}, function (dados_retorno){
                var php = dados_retorno.split("<|>");
                var qtd_animais_pasto = php[0];

                var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();

                if (qtd_animais_pasto==0 && (tipo_movimentacao=='V' || tipo_movimentacao=='T')) {
                    $("#local_origem").val('000000000');
                    $("#local_destino").val('000000000');
                    $(".-").hide();
                    $(".incluir_espacos").hide();
                    $(".filtro_movimentacao").show();
                    $(".listar_animais_transferencia").show();
                    $(".mais_opcoes").hide();
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html('Primeiro movimente os animais para a Saída no Mapa de Gado');
                    return
                }
                else {
                    var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();

                    if (tipo_movimentacao=="M") {
                        var local = $("#local_origem").val();

                        $.post("lista_pasto.php", {local:local}, function(valor){
                            $("select[name=pasto_morte]").html(valor);

                            if (controle_estoque=='L') {
                                $(".id_animal").hide();
                                $(".info_modal_morte").show();
                            }
                            else {
                                $(".id_animal").show();
                                $(".info_modal_morte").hide();
                            }

                            $('#modal_morte').modal('show');
                        });

                    }
                    else if (tipo_movimentacao=="O") {
                        var local = $("#local_origem").val();

                        $.post("lista_pasto.php", {local:local}, function(valor){
                            $("select[name=pasto_outra]").html(valor);

                            if (controle_estoque=='L') {
                                $(".id_animal").hide();
                                $(".info_modal_outra").show();
                            }
                            else {
                                $(".id_animal").show();
                                $(".info_modal_outra").hide();
                            }

                            $('#modal_outra_saida').modal('show');
                        });
                    }
                    else if (tipo_movimentacao=="V" || tipo_movimentacao=="T") {
                        var local = $("#local_origem").val();
                        var pasto = 999999999;
                        
                        $.post("lista_pesagem.php", {tipo_movimentacao:tipo_movimentacao, local:local}, function(valor){
                            $("select[name=pesagem]").html(valor);

                            var opcao_pesagem = $("input[name='opcao_pesagem']:checked").val();
                            if (opcao_pesagem=='S') {
                                $(".selecionar_pesagem").show();
                                $(".filtro_movimentacao").hide();
                                $(".listar_animais_transferencia").hide();
                                $(".incluir_espacos").show();
                                $(".desc_pesagem").html('* Selecione uma Pesagem');
                            }
                            else {
                                $(".selecionar_pesagem").show();
                                $(".filtro_movimentacao").show();
                                $(".listar_animais_transferencia").show();
                                $(".incluir_espacos").hide();
                                $(".desc_pesagem").html('Selecione uma Pesagem');
                            }
                            $(".mais_opcoes").show();

                            var sexo='T';
                            var categoria=[''];
                            $.post("lista_categoria_pasto.php", {local:local, pasto:pasto, categoria:categoria, sexo:sexo}, function(valor){
                                $("select[name=codigo_categoria_individual]").html(valor);
                            });
                        });
                    }
                }
            });
        }
    });

    $('#pesagem').change(function(event) {
        codigo_pesagem = $("#pesagem").val();

        if (codigo_pesagem==0) {
            $("#itens").hide();
            $(".iniciar_movimentacao").show();
            //$(".filtro_movimentacao").show();
            return;
        }

        $.post("ler_pesagem.php", {id:codigo_pesagem}, function(valor){

            var php = valor.split("<|>");

            if (php[0]==999999999) {
                alert ('Erro ao acessar a pesagem. Verifique com o suporte.');
                $("#pesagem").val('000000000');
                return;
            }
            else if (php[0]=='S') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('ATENÇÃO: Transfira animais machos das seguintes categorias para o para a Saída:<br>' + php[1]);

                //alert ('ATENÇÃO: Transfira animais machos das seguintes categorias para o para a Saída: ' + php[1]);
                $("#pesagem").val('000000000');
                return;
            }
            else if (php[2]=='S') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('ATENÇÃO: Transfira animais fêmeas das seguintes categorias para o para a Saída:<br>' + php[3]);
                //alert ('ATENÇÃO: Transfira animais fêmeas das seguintes categorias para o para a Saída: ' + php[3]);
                $("#pesagem").val('000000000');
                return;
            }
            else {
                var local_destino = $("#local_destino").val();
                var select = $("#local_destino").val();

                if (select!=0) {
                    select = document.getElementById('local_destino');
                    desc_destino = select.options[select.selectedIndex].text;
                }

                $(".descricao_filtro").text('Local Origem: ' +  php[0]);
                $(".descricao_filtro").val(php[0]);
                
                $(".descricao_destino").text('Local Destino: ' +  desc_destino);
                $("#descricao_destino").val(local_destino);

                $("#descricao_lote").text('Lote: ' + php[1]);
                $(".descricao_lote").val(php[1]);
                $("#data_pesados").text('Data: ' + php[2]);
                $(".total_pesados").text('Animais Pesados: ' + php[3]);
                $(".total_pesados").val(php[3]);
                $(".peso_total_kg").text('Peso Total Kg: ' + formatMoney(php[4]));
                $(".peso_total_kg").val(php[4]);
                $(".peso_total_arroba").text('Peso Total @: ' + formatMoney(php[5]));
                $(".peso_total_arroba").val(php[5]);
                $(".peso_medio_kg").text('Peso Médio Kg: ' + formatMoney(php[6]));
                $(".peso_medio_kg").val(php[6]);
                $(".peso_medio_arroba").text('Peso Médio @: ' + formatMoney(php[7]));
                $(".peso_medio_arroba").val(php[7]);

                var php_item = php[8].split("<!>");
                var numero_itens = php_item.length;
                var controle_estoque = $("#controle_estoque").val();

                if (controle_estoque=='I') {
                    html = "";
                    html += '<table class="table table-striped table-advance table-hover" id="tabela_itens" width="100%">';
                    html += '<thead>';
                    html += '<tr>';
                    html += '<th>' + ' Id' + '</th>';
                    html += '<th>' + ' Peso' + '</th>';
                    html += '<th>' + ' Categoria' + '</th>';
                    html += '<th>' + ' Sexo' + '</th>';
                    html += '<th>' + ' Nascimento' + '</th>';
                    html += '<th>' + ' Raça' + '</th>';
                    html += '<th>' + ' Pelagem' + '</th>';
                    html += '<th>' + ' Mãe' + '</th>';
                    html += '<th>' + ' Observação' + '</th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';

                    for (var i = 0; i < numero_itens; i++) {
                        var itens = php_item[i].split("|");
                        if (itens[16]=='') {
                            var codigo = itens[0];
                        }
                        else {
                            var codigo = itens[16]+'-'+itens[0];
                        }
                        
                        var peso = formatMoney(itens[1]);
                        var sexo = itens[2];
                        var nascimento= itens[3];
                        var raca = itens[4];
                        var pelagem = itens[5];
                        var mae = itens[6];
                        var observacao = itens[7];
                        var id_animal = itens[8];
                        var categoria= itens[10];
                        var qtd_animal = itens[14];

                        switch (categoria) {
                        case '001':
                            desc_categoria = '00 a 07 meses';
                            break;
                        case '002':
                            desc_categoria = '08 a 12 meses';
                            break;
                        case '003':   
                            desc_categoria = '13 a 24 meses';
                            break;
                        case '004':   
                            desc_categoria = '25 a 36 meses';
                            break;
                        case '005':   
                            desc_categoria = '> 36 meses';
                            break;
                        } 

                        html += '<tr>';
                        html += '<td width="12%" class="id_animal input_forma">' + codigo + '</td>';
                        html += '<td width="8%" class="peso_animal input_forma">' + peso + '</td>';
                        html += '<td width="8%" class="desc_categoria input_forma">' + desc_categoria + '</td>';
                        html += '<td width="8%" class="sexo_animal input_forma">' + sexo + '</td>';
                        html += '<td width="8%" class="nascimento_animal input_forma" align="center">' + nascimento + '</td>';
                        html += '<td width="10%" class="raca_animal input_forma">' + raca + '</td>';
                        html += '<td width="8%" class="pelagem_animal input_forma">' + pelagem + '</td>';
                        html += '<td width="8%" class="mae_animal input_forma">' + mae + '</td>';
                        html += '<td width="18%" class="observacao input_forma">' + observacao + '</td>';
                        html += '<td width="8%" hidden="" class="codigo_id input_forma">' + id_animal + '</td>';
                        html += '<td width="8%" hidden="" class="codigo_categoria input_forma">' + categoria + '</td>';
                        html += '<td width="8%" hidden="" class="qtd_animal input_forma">' + qtd_animal + '</td>';
                        html += "<td width='12%'></td>";
                        html += '</tr>';
                    }

                    html += '</tbody>';

                    html += '</table>';
                    document.getElementById('tabela_itens').innerHTML = html;

                    $("#itens").show();
                    $("#dados_consulta").hide();
                }
                else {
                    html = "";
                    html += '<table class="table table-striped table-advance table-hover" id="tabela_itens" width="100%">';
                    html += '<thead>';
                    html += '<tr>';
                    html += '<th>' + ' Categoria' + '</th>';
                    html += '<th>' + ' Qtde' + '</th>';
                    html += '<th>' + ' Sexo' + '</th>';
                    html += '<th>' + ' Peso Kg' + '</th>';
                    html += '<th>' + ' Peso Médio Kg' + '</th>';
                    html += '<th>' + ' Peso @' + '</th>';
                    html += '<th>' + ' Peso Médio @' + '</th>';
                    html += '<th>' + ' Observação' + '</th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';

                    for (var i = 0; i < numero_itens; i++) {
                        var itens = php_item[i].split("|");
                        var item = itens[9];
                        var peso = formatMoney(itens[1]);
                        var sexo = itens[2];
                        var categoria= itens[10];
                        var peso_medio = formatMoney(itens[11]);
                        var peso_arroba = formatMoney(itens[12]);
                        var peso_medio_arroba = formatMoney(itens[13]);
                        var qtd_animal = itens[14];
                        var desc_categoria = itens[15];
                        var observacao = itens[7];

                        html += '<tr>';
                        html += '<td hidden="" class="item_animal input_forma">' + item + '</td>';
                        html += '<td width="17%" class="desc_categoria input_forma">' + desc_categoria + '</td>';
                        html += '<td width="5%" class="qtd_animal input_forma">' + qtd_animal + '</td>';
                        html += '<td width="5%" class="sexo_animal input_forma">' + sexo + '</td>';
                        html += '<td width="8%" class="peso_animal input_forma">' + peso + '</td>';
                        html += '<td width="12%" class="peso_medio input_forma">' + peso_medio + '</td>';
                        html += '<td width="8%" class="peso_arroba input_forma">' + peso_arroba + '</td>';
                        html += '<td width="12%" class="peso_medio_arroba input_forma">' + peso_medio_arroba + '</td>';
                        html += '<td width="33%" class="observacao input_forma">' + observacao + '</td>';
                        html += '<td hidden="" class="codigo_categoria input_forma">' + categoria + '</td>';
                        html += '</tr>';
                    }

                    html += '</tbody>';

                    html += '</table>';
                    document.getElementById('tabela_itens').innerHTML = html;

                    $("#itens").show();
                    $("#dados_consulta").hide();
                }
            }
        });
    });

    $('#motivo_morte').change(function(event) {
        var controle_estoque = $("#controle_estoque").val();

        var animal_codigo_id = $("#codigo_id_morte").val();

        if (animal_codigo_id==0 && controle_estoque=='I') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Id do animal não cadastrado.');
            $(".alert_erro_animal").show();
            $('#id_animal_morte').val('');
            $('#motivo_morte').val(000);
            document.getElementById("id_animal_morte").focus();
            return;
        }
    }); 

    $('#pasto_morte').change(function(event) {
        var local = $("#local_origem").val();
        var pasto = $("#pasto_morte").val();
        var controle_estoque = $("#controle_estoque").val();

        if (controle_estoque=='L') {
            $.post("lista_categoria_pasto_morte_outra.php", {local:local, pasto:pasto}, function(valor){

                if (valor=='N') {
                    $(".alert_erro_animal .negrito").html('');
                    $(".alert_erro_animal span").html('Não existem animais nesse pasto.');
                    $(".alert_erro_animal").show();
                    $("#pasto_morte").val('000000000');
                    return;
                }
                else {
                    $("select[name=categoria_morte]").html(valor);
                }
            });
        }
        else {
            var codigo_categoria = $('#categoria_digitada_morte').val();
            var sexo_morte = $('#sexo_animal_morte').val();
            var desc_categoria = $('#desc_categoria_digitada_morte').val();

            if (sexo_morte=='Macho' || sexo_morte=='M') {
                var sexo = 'M';
            }
            else {
                var sexo = 'F';
            }

            $.post("ler_animal_categoria_pasto.php", {local:local, sexo:sexo, codigo_categoria:codigo_categoria, pasto:pasto}, function(valor){
                var php_pasto = valor.split("<|>");

                if (php_pasto[0]=='N') {
                    if (php_pasto[3]=='') {
                        $(".alert_erro_animal .negrito").html('');
                        $(".alert_erro_animal span").html('Não existem animais no pasto selecionado');
                        $(".alert_erro_animal").show();
                        $("#pasto_morte").val('000000000');
                        document.getElementById("pasto_morte").focus();
                        return;
                    }
                    else {
                        $(".alert_erro_animal .negrito").html('');
                        $(".alert_erro_animal span").html('Não consta no pasto seleionado animais com a categoria ' + desc_categoria + ' ' + sexo_morte);
                        $(".alert_erro_animal").show();
                        $("#pasto_morte").val('000000000');
                        document.getElementById("pasto_morte").focus();
                        return;
                    }
                }
            });

        } 
    }); 

    $('#categoria_morte').change(function(event) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();

        var categoria = $('#categoria_morte').val();

        $('#sexo_morte').val(categoria.substr(0, 1));      
        $('#categoria_digitada_morte').val(categoria.substr(1, 3));      
        $('#qtd_morte').val(categoria.substr(4));      
    }); 

    $('#pasto_outra').change(function(event) {
        var local = $("#local_origem").val();
        var pasto = $("#pasto_outra").val();
        var controle_estoque = $("#controle_estoque").val();

        if (controle_estoque=='L') {
            $.post("lista_categoria_pasto_morte_outra.php", {local:local, pasto:pasto}, function(valor){

                if (valor=='N') {
                    $(".alert_erro_animal .negrito").html('');
                    $(".alert_erro_animal span").html('Não existem animais nesse pasto.');
                    $(".alert_erro_animal").show();
                    $("#pasto_outra").val('000000000');
                    return;
                }
                else {
                    $("select[name=categoria_outra]").html(valor);
                }
            });
        }
        else {
            var codigo_categoria = $('#categoria_digitada_outra').val();
            var sexo_morte = $('#sexo_animal_outra').val();
            var desc_categoria = $('#desc_categoria_digitada_outra').val();

            if (sexo_morte=='Macho' || sexo_morte=='M') {
                var sexo = 'M';
            }
            else {
                var sexo = 'F';
            }

            $.post("ler_animal_categoria_pasto.php", {local:local, sexo:sexo, codigo_categoria:codigo_categoria, pasto:pasto}, function(valor){
                var php_pasto = valor.split("<|>");

                if (php_pasto[0]=='N') {
                    if (php_pasto[3]=='') {
                        $(".alert_erro_animal .negrito").html('');
                        $(".alert_erro_animal span").html('Não existem animais no pasto selecionado');
                        $(".alert_erro_animal").show();
                        $("#pasto_outra").val('000000000');
                        document.getElementById("pasto_outra").focus();
                        return;
                    }
                    else {
                        $(".alert_erro_animal .negrito").html('');
                        $(".alert_erro_animal span").html('Não consta no pasto seleionado animais com a categoria ' + desc_categoria + ' ' + sexo_morte);
                        $(".alert_erro_animal").show();
                        $("#pasto_outra").val('000000000');
                        document.getElementById("pasto_outra").focus();
                        return;
                    }
                }
            });
        } 

/*        $.post("lista_categoria_pasto_morte_outra.php", {local:local, pasto:pasto}, function(valor){

            if (valor=='N') {
                $(".alert_erro_animal .negrito").html('');
                $(".alert_erro_animal span").html('Não existem animais nesse pasto.');
                $(".alert_erro_animal").show();
                $("#pasto_outra").val('000000000');
            }
            else {
                $("select[name=categoria_outra]").html(valor);
            }
        });*/
    }); 

    $('#categoria_outra').change(function(){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();

        var categoria = $('#categoria_outra').val();

        $('#sexo_outra').val(categoria.substr(0, 1));      
        $('#categoria_digitada_outra').val(categoria.substr(1, 3));      
        $('#qtd_outra').val(categoria.substr(4));      
    });

    $('#qtd_a_digitar').change(function(event) {
        var local_origem = $("#local_origem").val();
        var qtd_a_digitar = parseInt($('#qtd_a_digitar').val());

        $.post("ler_pasto_venda.php", {local: local_origem}, function (dados_retorno){

            var php = dados_retorno.split("<|>");

            var qtd_animais_pasto = parseInt(php[0]);

            if (qtd_animais_pasto<qtd_a_digitar) {
                $(".alert_erro_animal .negrito").html('');
                $(".alert_erro_animal span").html('A quantidade digitada não existe no curral de saida. Movimente os animais para o para a Saída.');
                $(".alert_erro_animal").show();
                $('#qtd_a_digitar').val('');
                return
            }
        });
    }); 

    $('#codigo_categoria_individual').change(function(){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();

        var categoria = $('#codigo_categoria_individual').val();

        $('#sexo_lote').val(categoria.substr(0, 1));      
        $('#categoria_lote').val(categoria.substr(1, 3));      
        $('#qtd_lote').val(categoria.substr(4));      
        $('#qtd_digitado_anterior').val('');      
    });

    $('#qtd_cat_individual').click(function(){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
    });


    $('#qtd_a_digitar').click(function(event) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
    });

    $('#id_animal').click(function(event) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
    });

    $('#observacao').change(function(event) {
        var animal_codigo_id = $("#codigo_id").val();
        var controle_estoque = $("#controle_estoque").val();

        if (animal_codigo_id==0 && controle_estoque=='I') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Id do animal não cadastrado.');
            $(".alert_erro_animal").show();
            $('#id_animal').val('');
        }
    }); 

    $("#categoria_morte").click(function(){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return;
    });

    $("#pasto_morte").click(function(){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return;
    });

    $("#categoria_outra").click(function(){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return;
    });

    $("#pasto_outra").click(function(){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return;
    });

    /*$('#seleciona_todos_aceite').click(function(event) {
        if(this.checked) {
            $('.checkbox1').each(function() {
                this.checked = true; 
            });
        }else{
            $('.checkbox1').each(function() {
                this.checked = false;  
            });         
        }
    });*/

    $('.entrada_animais').click(function(event) {
        //var entrada_animais = $("input[name='entrada_animais']:checked").val();
        var entrada_animais = 'S';

        switch (entrada_animais) {
        case 'S':
            html = "";
            html += '<table class="table table-striped table-advance table-hover" id="tabela_itens_digitados_entrada" width="100%">';
            html += '<thead>';
            html += '<tr>';
            html += '<th>' + ' Categoria' + '</th>';
            html += '<th>' + ' Idade (meses)' + '</th>';
            html += '<th>' + ' Sexo' + '</th>';
            html += '<th>' + ' Raça' + '</th>';
            html += '<th>' + ' Pelagem' + '</th>';
            html += '<th>' + ' Qtde Categoria' + '</th>';
            html += '<th>' + ' Seq Númerica' + '</th>';
            html += '<th>' + ' Marcação Alfa' + '</th>';
            html += '<th>' + ' Peso Médio' + '</th>';
            html += '<th><i class="icon_cogs"></i> Ações</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            html += '</tbody>';
            html += '</table>';
            document.getElementById('tabela_itens_digitados_entrada').innerHTML = html;

            $("#qtd_total_animais").val('');
            $("#qtd_total_digitado").val('');
            $("#codigo_categoria_entrada").val('000');
            $("#idade_entrada").val('');
            $("#macho_entrada").prop("checked", false);
            $("#femea_entrada").prop("checked", false);
            $("#codigo_raca_entrada").val('000');
            $("#codigo_pelagem_entrada").val('000');
            $("#qtd_cat_entrada").val('');
            $("#sequencia_numeria_entrada").val('');
            $("#marcacao_alfa_entrada").val('');
            $("#editar_entrada").hide();
            $("#incluir_entrada").show();
            $("#sequenciaHelpBlock").hide();
            $(".mens_reprodutor").hide();

            $('#modal_entrada_rapida').modal('show');
            break;
        case 'N':
            location.href= "form_cadastro_animais.php";
            break;
        } 
    });

    $('#qtd_cat_entrada').click(function(event) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return false;
    });

    $('#codigo_categoria_entrada').click(function(event) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return false;
    });

    $('#idade_entrada').click(function(event) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return false;
    });

    $('.sexo_entrada').change(function(event) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return false;
    });

    $(".sexo_entrada").click(function(){
        var macho = $('#macho_entrada');
        var femea = $('#femea_entrada');
        
        if (macho.is(":checked") && controle_estoque=='I'){
            $('.mens_reprodutor').show();
        }
        else {
            $('.mens_reprodutor').hide();
        }
    });

    $('#codigo_raca_entrada').click(function(event) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return false;
    });

    $('#sequencia_numeria_entrada').click(function(event) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return false;
    });

    $('#peso_entrada').click(function(event) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return false;
    });

    $('#peso_medio').click(function(event) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('');
        $(".alert_erro_animal").hide();
        return false;
    });

    $("#vacas_paridas").click(function(){

        if ($("#vacas_paridas").is(":checked") == false){
            $("#paridas_ate").val('');
            document.getElementById("paridas_ate").style.borderColor = "";
        }

        if ($("#vacas_paridas").is(":checked") == true){
            document.getElementById("paridas_ate").focus();
            document.getElementById("paridas_ate").style.borderColor = "#2556db";
        }
    });

    $("#paridas_ate").change(function () {
        document.getElementById("paridas_ate").style.borderColor = "";

        if ($("#paridas_ate").val()=='') {
            $("#vacas_paridas").prop("checked", false);
        }
        else {
            $("#vacas_paridas").prop("checked", true);
        }   
    });

    $("#codigo_estacao_filtro").change(function () {
        document.getElementById("codigo_estacao_filtro").style.borderColor = "";
    });

});

function voltar_movimentacao_selecionar_digitados() {
    if (verificarSelecaoNaTabela()) {
        $("#mensagem_sair_sem_confirmar").modal();
        $("#mensagem_sair_sem_confirmar .modal-body").html('Exitem animais selecionados, ao confirmar a digitação será perdida.');
        //if (window.confirm("Exitem animais digitados, ao confirmar essa opção a digitação será perdida.")) {
        //    location.href='form_movimentacao_animais_incluir.php';
        //} 
    } else {
        //location.href='form_movimentacao_animais_incluir.php';
        $("#itens_digitados").hide();
        $("#dados_consulta").show();
    }
}

function verificarSelecaoNaTabela() {
    // Pega todos os checkboxes com a classe 'checkbox1'
    const checkboxes = document.querySelectorAll('.checkbox1');

    // Itera sobre os checkboxes
    for (let i = 0; i < checkboxes.length; i++) {
        // Se encontrar um checkbox marcado, retorna true imediatamente
        if (checkboxes[i].checked) {
            return true;
        }
    }
    // Se nenhum checkbox estiver marcado após verificar todos, retorna false
    return false;
}

function voltar_movimentacao_compras() {
    var tem_item="";        

    $('#tabela_itens_digitados_entrada tbody tr').each(function(){
        var codigo = $(this).find('.codigo_categoria_entrada').html();
        if (codigo!=undefined){
            tem_item="S";
        }
    });

    if (tem_item=="") {
        $("#itens").hide();
        $("#itens_digitados_entrada").hide();
        $("#entrada_sim").prop("checked", false);
        //$("#entrada_nao").prop("checked", false);
        $("#dados_consulta").show();
        $("#pesagem").val('000000000');    
    } else {
        if (window.confirm("Exitem animais digitados, ao confirmar essa opção a digitação será perdida.")) {
            $("#itens").hide();
            $("#itens_digitados_entrada").hide();
            $("#entrada_sim").prop("checked", false);
            //$("#entrada_nao").prop("checked", false);
            $("#dados_consulta").show();
            $("#pesagem").val('000000000');    
        } 
    }
}

function voltar_movimentacao_selecionar() {
    $("#itens").hide();
    $("#itens_digitados").hide();
    $("#dados_consulta").show();
    $("#pesagem").val('000000000');    
}

function ler_animal_morte(){
    //$(".alert_erro_animal .negrito").html('');
    //$(".alert_erro_animal span").html('');
    //$(".alert_erro_animal").hide();

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
    var data_paridas = '';
    var num_parto_de = '';
    var num_parto_ate = '';
    var parto = '';
    var num_aborto_de = '';
    var num_aborto_ate = '';
    var aborto = '';
    var previsao_parto_de = '';
    var previsao_parto_ate = '';
    var positivo='';
    var negativo='';
    var origem='';

    if (id_animal.length < 5) {
        return;
    } 

    $.post("ler_animal_movimentacao_morte_outra.php", {
        id_animal:id_animal, 
        local:local, 
        origem:origem,
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
        data_paridas:data_paridas,
        num_parto_de:num_parto_de,
        num_parto_ate:num_parto_ate,
        parto:parto,
        num_aborto_de:num_aborto_de,
        num_aborto_ate:num_aborto_ate,
        aborto:aborto,
        previsao_parto_de:previsao_parto_de,
        previsao_parto_ate:previsao_parto_ate,
        positivo:positivo,
        negativo:negativo
        }, function(valor){

        var php = valor.split("<|>");

        if (php[0]=='Nao tem animal') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html(php[1]);
            $(".alert_erro_animal").show();
            //$('#id_animal_morte').val('');
            //document.getElementById("id_animal_morte").focus();
            return;
        }
        else {

            var dia  = php[2].split("/")[0];
            var mes  = php[2].split("/")[1];
            var ano  = php[2].split("/")[2];

            var data_nascimento = ano + '-' + mes + '-' + dia;

            var data_morte = $('#data_movimentacao').val();

            $("#descricao_animal_morte").text(php[6]);
            $("#codigo_id_morte").val(php[0]);
            $("#sexo_animal_morte").val(php[1]);
            $("#nascimento_animal_morte").val(php[2]);
            $("#raca_animal_morte").val(php[3]);
            $("#pelagem_animal_morte").val(php[4]);
            $("#mae_animal_morte").val(php[5]);
            $("#categoria_digitada_morte").val(php[16]);
            $("#desc_categoria_digitada_morte").val(php[17]);

            if (data_morte<data_nascimento){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('A Data da Movimentação (Morte) não pode ser menor que a Data do Nascimento.');
                $('#id_animal_morte').val('');
                document.getElementById("id_animal_morte").focus();
                return;
            }
        }

        if (php[18]=='S') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Animal em Estação de Monta.');
            $(".alert_erro_animal").show();
        }

    });
}

function ler_animal_outra(){
    var id_animal= $('#id_animal_outra').val();
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
    var data_paridas = '';
    var num_parto_de = '';
    var num_parto_ate = '';
    var parto = '';
    var num_aborto_de = '';
    var num_aborto_ate = '';
    var aborto = '';
    var previsao_parto_de = '';
    var previsao_parto_ate = '';
    var positivo='';
    var negativo='';
    var origem='';

    if (id_animal.length < 5) {
        return;
    } 

    $.post("ler_animal_movimentacao_morte_outra.php", {
        id_animal:id_animal, 
        local:local, 
        origem:origem,
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
        data_paridas:data_paridas,
        num_parto_de:num_parto_de,
        num_parto_ate:num_parto_ate,
        parto:parto,
        num_aborto_de:num_aborto_de,
        num_aborto_ate:num_aborto_ate,
        aborto:aborto,
        previsao_parto_de:previsao_parto_de,
        previsao_parto_ate:previsao_parto_ate,
        positivo:positivo,
        negativo:negativo
        }, function(valor){

        var php = valor.split("<|>");

        if (php[0]=='Nao tem animal') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html(php[1]);
            $(".alert_erro_animal").show();
            //$('#id_animal_outra').val('');
            //document.getElementById("id_animal_outra").focus();
            return;
        }
        else {
            $("#descricao_animal_outra").text(php[6]);
            $("#codigo_id_outra").val(php[0]);
            $("#sexo_animal_outra").val(php[1]);
            $("#nascimento_animal_outra").val(php[2]);
            $("#raca_animal_outra").val(php[3]);
            $("#pelagem_animal_outra").val(php[4]);
            $("#mae_animal_outra").val(php[5]);
            $("#categoria_digitada_outra").val(php[16]);
            $("#desc_categoria_digitada_outra").val(php[17]);
        }

        if (php[18]=='S') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Animal em Estação de Monta.');
            $(".alert_erro_animal").show();
        }
    });
}


// FUNÇÃO NÃO UTILIZADA 
/*function ler_animal(){
   // $(".alert_erro_animal .negrito").html('');
   // $(".alert_erro_animal span").html('');
   // $(".alert_erro_animal").hide();
    var id_animal= $('#id_animal').val();
    var local = $('#local_origem').val();
    var origem = $('#codigo_origem_filtro').val();
    var categoria = $('#codigo_categoria_filtro').val();
    var raca = $('#codigo_raca_filtro').val();
    var pai = $('#codigo_pai_filtro').val();
    var mae = $('#codigo_mae_filtro').val();
    var data_nasc_inicial = $("#data_nasc_inicial_filtro").val();
    var data_nasc_final = $("#data_nasc_final_filtro").val();
    var peso_nasc_inicial = $("#peso_inicial_nasc_filtro").val();
    var peso_nasc_final = $("#peso_final_nasc_filtro").val();
    var peso_desmama_inicial = $("#peso_inicial_desmama_filtro").val();
    var peso_desmama_final = $("#peso_final_desmama_filtro").val();
    var peso_ult_inicial = $("#peso_inicial_ultimo_filtro").val();
    var peso_ult_final = $("#peso_final_ultimo_filtro").val();

    if (origem==null) {
        origem=[''];
    }

    if (categoria==null) {
        categoria=[''];
    }

    if (raca==null) {
        raca=[''];
    }

    if (pai==null) {
        pai=[''];
    }

    if (mae==null) {
        mae=[''];
    }

    var macho = $('#macho');
    var femea = $('#femea');

    if (macho.is(":checked") && femea.is(":checked")) {
        sexo='Todos';
    }
    else if (macho.is(":checked")){
        sexo='M';
    }
    else if (femea.is(":checked")){
        sexo='F';
    }

    if ($("#vacas_solteiras").is(":checked") == true){
        solteiras='S';
    }
    else {
        solteiras='';
    }

    if ($("#descarte").is(":checked") == true){
        descarte='S';
    }
    else {
        descarte='';
    }

    if ($("#vacas_paridas").is(":checked") == true){
        paridas='S';
    }
    else {
        paridas='';
    }

    var data_paridas = '';

    if (paridas=='S'){
        var data_paridas = $('#paridas_ate').val();

        if (data_paridas=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Para seleção Vacas Paridas informe a data Paridas Até');
            return;
        }
    }

    const num_parto_de = $('#num_parto_de_filtro').val();
    const num_parto_ate = $('#num_parto_ate_filtro').val();

    const num_aborto_de = $('#num_aborto_de_filtro').val();
    const num_aborto_ate = $('#num_aborto_ate_filtro').val();

    const previsao_parto_de = $("#previsao_parto_de_filtro").val();
    const previsao_parto_ate = $("#previsao_parto_ate_filtro").val();

    if ($("#positivo").is(":checked") == true){
        positivo='S';
    }
    else {
        positivo='';
    }

    if ($("#negativo").is(":checked") == true){
        negativo='S';
    }
    else {
        negativo='';
    }

    if (id_animal.length < 5) {
        return;
    } 

    $.post("ler_animal_filtros.php", {
        id_animal:id_animal, 
        local:local, 
        origem:origem,
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
        data_paridas:data_paridas, 
        num_parto_de:num_parto_de, 
        num_parto_ate:num_parto_ate,
        num_aborto_de:num_aborto_de, 
        num_aborto_ate:num_aborto_ate,
        previsao_parto_de:previsao_parto_de,
        previsao_parto_ate:previsao_parto_ate,
        positivo:positivo,
        negativo:negativo
        }, function(valor){

        var php = valor.split("<|>");

        if (php[0]=='Nao tem animal') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html(php[1]);
            $(".alert_erro_animal").show();
            //$('#id_animal').val('');
            //document.getElementById("id_animal").focus();
            return;
        }
        else {
            $("#descricao_animal").text(php[6]);
            $("#codigo_id").val(php[0]);
            $("#sexo_animal").val(php[1]);
            $("#nascimento_animal").val(php[2]);
            $("#raca_animal").val(php[3]);
            $("#pelagem_animal").val(php[4]);
            $("#mae_animal").val(php[5]);
            $("#categoria_animal").val(php[16]);
        }

        var codigo_categoria = php[16];
        var desc_categoria = php[17];
        var pasto = 0;

        if (php[1]=='Macho' || php[1]=='M') {
            var sexo = 'M';
        }
        else {
            var sexo = 'F';
        }

        $.post("ler_animal_categoria_pasto.php", {local:local, sexo:sexo, codigo_categoria:codigo_categoria, pasto:pasto}, function(valor){
            var php_pasto = valor.split("<|>");

            if (php_pasto[0]=='N') {
                $(".alert_erro_animal .negrito").html('');
                $(".alert_erro_animal span").html('Não consta no para a Saída animais com a categoria ' + desc_categoria + ' ' + php[1]);
                $(".alert_erro_animal").show();
                $("#codigo_id").val('');
                $("#sexo_animal").val('');
                $('#id_animal').val('');
                document.getElementById("btn_salvar_individual").focus();
                return;
            }
        });

        if (php[7]=='N' && categoria!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('A Categoria do animal não consta no filtro de categorias selecionadas');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[8]=='N' && sexo!='Todos') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('A Sexo do animal não corresponde ao filtro selecionado');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[9]=='N' && raca!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('A Raça do animal não consta no filtro de raças selecionadas');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[10]=='N' && pai!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('O Pai do animal não consta no filtro de pais selecionados');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[11]=='N' && mae!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('A Mãe do animal não consta no filtro de mães selecionadas');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[12]=='N' && data_nasc_inicial!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('A Data de Nascimento do animal não consta no filtro de datas selecionadas');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[13]=='N' && peso_nasc_inicial!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('O Peso de Nascimento do animal não consta no filtro de pesos selecionados');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[14]=='N' && peso_desmama_inicial!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('O Peso da Desmama do animal não consta no filtro de pesos selecionados');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[15]=='N' && peso_ult_inicial!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('O Último Peso do animal não consta no filtro de pesos selecionados');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[19]=='N' && solteiras=='S') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Fêmea não está solteira ou animal não corresponde ao filtro informado!');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[20]=='N' && descarte=='S') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Este animal não é para descarte!');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[21]=='N' && paridas=='S') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Fêmea não está parida ou animal não corresponde ao filtro informado!');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[22]=="N" && num_parto_de!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Nº partos ou animal não corresponde ao filtro informado!');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[23]=="N" && num_aborto_de!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Nº abortos ou animal não corresponde ao filtro informado!');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[24]=="N" && previsao_parto_de!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Previsão de Parto não corresponde ao filtro informado!');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        if (php[25]=="N" && origem!='') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Origem do animal não corresponde ao filtro informado!');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
            return;
        }

        //if ((positivo=='S' && negativo=='S') || (positivo=='' && negativo=='')) {
        //    return;
        //}

        if (positivo=='S' && negativo=='') {
            if (php[26]=="N" && positivo=='S') {
                $(".alert_erro_animal .negrito").html('');
                $(".alert_erro_animal span").html('Diagnostico não corresponde ao filtro informado!');
                $(".alert_erro_animal").show();
                $('#observacao').focus();
                return;
            }
        }

        if (positivo=='' && negativo=='S') {
            if (php[27]=="N" && negativo=='S') {
                $(".alert_erro_animal .negrito").html('');
                $(".alert_erro_animal span").html('Diagnostico não corresponde ao filtro informado!');
                $(".alert_erro_animal").show();
                $('#observacao').focus();
                return;
            }
        }

        if (php[18]=='S') {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Animal em Estação de Monta.');
            $(".alert_erro_animal").show();
            $('#observacao').focus();
        }

    });
}*/

// FUNÇÃO NÃO UTILIZADA MAIS
// Ler animal quando digitado na tela de filtros Venda/Transferencia do form_movimentacao_animais_incluir.php
/*function ler_animal_filtro(){
    var id_animal= $('#codigo_number_filtro').val();

    if (id_animal!='') {
        const id_animal= $('#codigo_number_filtro').val();
        const local = $('#local_origem').val();
        const data_nasc_inicial = '';
        const data_nasc_final = '';
        const peso_nasc_inicial = '';
        const peso_nasc_final = '';
        const peso_desmama_inicial = '';
        const peso_desmama_final = '';
        const peso_ult_inicial = '';
        const peso_ult_final = '';
        const origem=[''];
        const categoria=[''];
        const raca=[''];
        const pai=[''];
        const mae=[''];
        const sexo='Todos';
        const solteiras='';
        const descarte='';
        const paridas='';
        const data_paridas='';
        const num_parto_de = '';
        const num_parto_ate = '';
        const num_aborto_de = '';
        const num_aborto_ate = '';
        const previsao_parto_de = '';
        const previsao_parto_ate = '';
        const positivo='';
        const negativo='';

        $.post("ler_animal_filtros.php", {
            id_animal:id_animal, 
            local:local, 
            origem:origem,
            categoria:categoria, 
            sexo:sexo, 
            raca:raca, 
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
            data_paridas:data_paridas, 
            num_parto_de:num_parto_de, 
            num_parto_ate:num_parto_ate,
            num_aborto_de:num_aborto_de, 
            num_aborto_ate:num_aborto_ate,
            previsao_parto_de:previsao_parto_de,
            previsao_parto_ate:previsao_parto_ate,
            positivo:positivo,
            negativo:negativo
            }, function(valor){

            var php = valor.split("<|>");

            if (php[0]=='Nao tem animal') {
                $("#mensagem_erro_animal_filtro").modal();
                $("#codigo_number_filtro").val('');
                document.getElementById("codigo_number_filtro").focus();
                document.getElementById("codigo_number_filtro").style.borderColor = "red";
                return;
            }
            else {
                exibe_filtro();
            }
        });
    }
    else {
        exibe_filtro();
    }
}*/

/*function exibe_filtro(){
    var controle_estoque = $("#controle_estoque").val();
    var peso_nasc_filtro = '';
    var peso_desmama_filtro = '';
    var peso_ult_filtro = '';

    var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();

    if (tipo_movimentacao=='V') {
        var desc_movimentacao = 'Venda';
    }    
    else if (tipo_movimentacao=='T'){
        var desc_movimentacao = 'Transferência';
    }
    else {
        var desc_movimentacao = '';
    }

    var options = $('#codigo_categoria_filtro option:selected');
    var categorias = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_categoria_filtro').text();
        categorias.push( desc.trim() );
    });

    if (categorias!=''){
        categorias = '->Categoria: ' + categorias;
    }
    else {
        categorias = '';
    }

    var macho = $('#macho');
    var femea = $('#femea');

    if (macho.is(":checked") && femea.is(":checked")) {
        sexo=['Todos'];
    }
    else if (macho.is(":checked")){
        sexo=['Machos'];
    }
    else if (femea.is(":checked")){
        sexo=['Femeas'];
    }


    if (controle_estoque=='I') {
        var codigo_numerico = $("#codigo_number_filtro").val();

        var options = $('#codigo_raca_filtro option:selected');
        var racas = [];

        $(options).each(function(){
            var desc_raca = $(this).bind('#codigo_raca_filtro').text();
            racas.push( desc_raca.trim() );
        });

        if (racas!=''){
            racas = '->'+racas;
        }
        else {
            racas = '';
        }

        var data_nasc_inicial = $("#data_nasc_inicial_filtro").val();
        var data_nasc_final = $("#data_nasc_final_filtro").val();

        if (data_nasc_inicial > data_nasc_final) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe a Data de Nascimento Inicial e Final corretamente!');
            return;
        }

        if (data_nasc_inicial!=0 && data_nasc_final!=0) {
            var data_ini = data_nasc_inicial.split("-");
            var dia_ini = data_ini[2];
            var mes_ini = data_ini[1];
            var ano_ini = data_ini[0];

            var data_fim = data_nasc_final.split("-");
            var dia_fim = data_fim[2];
            var mes_fim = data_fim[1];
            var ano_fim = data_fim[0];
            data_filtro = '->Data Nasc: de ' + dia_ini + "/" + mes_ini + "/" + ano_ini + ' até ' +
                                              dia_fim + "/" + mes_fim + "/" + ano_fim;
        }
        else {
            data_filtro = '';
        }

        var peso_nasc_inicial = $("#peso_inicial_nasc_filtro").val();
        var peso_nasc_final = $("#peso_final_nasc_filtro").val();

        if (peso_nasc_inicial!='' || peso_nasc_final!=''){
            var peso_nasc_inicial = parseFloat($("#peso_inicial_nasc_filtro").val());
            var peso_nasc_final = parseFloat($("#peso_final_nasc_filtro").val());

            if (peso_nasc_inicial > peso_nasc_final) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe o Peso de Nascimento Inicial e Final corretamente!');
                return;
            }

            peso_nasc_filtro = '->Peso Nasc: de ' + peso_nasc_inicial +' até ' + peso_nasc_final +' Kg';
        }
        else {
            peso_nasc_filtro = '';
        }

        var peso_desmama_inicial = $("#peso_inicial_desmama_filtro").val();
        var peso_desmama_final = $("#peso_final_desmama_filtro").val();

        if (peso_desmama_inicial!='' || peso_desmama_final!=''){
            var peso_desmama_inicial = parseFloat($("#peso_inicial_desmama_filtro").val());
            var peso_desmama_final = parseFloat($("#peso_final_desmama_filtro").val());

            if (peso_desmama_inicial > peso_desmama_final) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe o Peso de Desmama Inicial e Final corretamente!');
                return;
            }

            peso_desmama_filtro = '->Peso Desmama: de ' + peso_desmama_inicial +' até ' + peso_desmama_final +' Kg';
        }
        else {
            peso_desmama_filtro = '';
        }

        var peso_ult_inicial = $("#peso_inicial_ultimo_filtro").val();
        var peso_ult_final = $("#peso_final_ultimo_filtro").val();

        if (peso_ult_inicial!='' || peso_ult_final!='') {
            var peso_ult_inicial = parseFloat($("#peso_inicial_ultimo_filtro").val());
            var peso_ult_final = parseFloat($("#peso_final_ultimo_filtro").val());

            if (peso_ult_inicial > peso_ult_final) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe o Último Peso Inicial e Final corretamente!');
                return;
            }

            peso_ult_filtro = '->Últ Peso : de ' + peso_ult_inicial +' até ' + peso_ult_final +' Kg';
        }
        else {
            peso_ult_filtro = '';
        }

        var options = $('#codigo_origem_filtro option:selected');
        var origem = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_origem_filtro').text();
            origem.push( desc.trim() );
        });

        if (origem!=''){
            origem = '->Origem:' + origem;
        }
        else {
            origem = '';
        }

        var options = $('#codigo_pai_filtro option:selected');
        var pai = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_pai_filtro').text();
            pai.push( desc.trim() );
        });

        if (pai!=''){
            pai = '->Pai:' + pai;
        }
        else {
            pai ='';
        }

        var options = $('#codigo_mae_filtro option:selected');
        var mae = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_mae_filtro').text();
            mae.push( desc.trim() );
        });

        if (mae!=''){
            mae = '->Mãe:' + mae;
        }
        else {
            mae = '';
        }

        var data_parto_de = $("#previsao_parto_de_filtro").val();
        var data_parto_ate = $("#previsao_parto_ate_filtro").val();

        if (data_parto_de!='' || data_parto_ate!='') {
            if (data_parto_de=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Previsão do Parto (de) não pode ser vazio!');
                return;
            }''

            if (data_parto_ate=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Previsão do Parto (até) não pode ser vazio!');
                return;
            }

            if (data_parto_de > data_parto_ate) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe a Data Previsão de Parto De e Até corretamente!');
                return;
            }

            var data_ini = data_parto_de.split("-");
            var dia_ini = data_ini[2];
            var mes_ini = data_ini[1];
            var ano_ini = data_ini[0];

            var data_fim = data_parto_ate.split("-");
            var dia_fim = data_fim[2];
            var mes_fim = data_fim[1];
            var ano_fim = data_fim[0];
            previsao_filtro = '->Previsão Parto: de ' + dia_ini + "/" + mes_ini + "/" + ano_ini + ' até ' +
                                              dia_fim + "/" + mes_fim + "/" + ano_fim;
        }
        else {
            previsao_filtro = '';
        }

        var parto_de = $("#num_parto_de_filtro").val();
        var parto_ate = $("#num_parto_ate_filtro").val();

        if (parto_de!='' || parto_ate!='') {
            if (parto_de=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Nº Partos (de) não pode ser vazio!');
                return;
            }

            if (parto_ate=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Nº Partos (até) não pode ser vazio!');
                return;
            }

            if (parto_de > parto_ate) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe o Nº de Partos De e Até corretamente!');
                return;
            }

            partos_filtro = '->Partos: de ' + parto_de + ' ate ' + parto_ate;
        }
        else {
            partos_filtro = '';
        }

        var aborto_de = $("#num_aborto_de_filtro").val();
        var aborto_ate = $("#num_aborto_ate_filtro").val();

        if (aborto_de!='' || aborto_ate!='') {
            if (aborto_de=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Nº Abortos (de) não pode ser vazio!');
                return;
            }

            if (aborto_ate=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Nº Abortos (até) não pode ser vazio!');
                return;
            }

            if (aborto_de > aborto_ate) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe o Nº de Abortos De e Até corretamente!');
                return;
            }

            aborto_filtro = '->Abortos: de ' + aborto_de + ' ate ' + aborto_ate;
        }
        else {
            aborto_filtro = '';
        }

        if ($("#vacas_paridas").is(":checked") == true){
            paridas='VP';
        }
        else {
            paridas='';
        }

        var paridas_ate = $("#paridas_ate").val();

        if (paridas=='VP' && paridas_ate=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe Paridase Até!');
            return;
        }

        if (paridas=='VP') {
            var data_fim = paridas_ate.split("-");
            var dia_fim = data_fim[2];
            var mes_fim = data_fim[1];
            var ano_fim = data_fim[0];
            filtro_paridas = '->Paridas ate ' + dia_fim + "/" + mes_fim + "/" + ano_fim;
        }
        else {
            filtro_paridas = '';
        }

        if ($("#vacas_solteiras").is(":checked") == true){
            solteiras='VS';
        }
        else {
            solteiras='';
        }

        if (solteiras=='VS') {
            filtro_solteiras = '->Solteiras';
        }
        else {
            filtro_solteiras = '';
        }

        if ($("#descarte").is(":checked") == true){
            descarte='DC';
        }
        else {
            descarte='';
        }

        if (descarte=='DC') {
            filtro_descarte = '->Descarte';
        }
        else {
            filtro_descarte = '';
        }

        if ($("#positivo").is(":checked") == true){
            positivo='DP';
        }
        else {
            positivo='';
        }

        if (positivo=='DP') {
            filtro_positivo = '->Diagnóstico Positivo';
        }
        else {
            filtro_positivo = '';
        }

        if ($("#negativo").is(":checked") == true){
            negativo='DN';
        }
        else {
            negativo='';
        }

        if (negativo=='DN') {
            filtro_negativo = '->Diagnóstico Negativo';
        }
        else {
            filtro_negativo = '';
        }

        if (positivo=='' && negativo=='') {
            filtro_estacao = '';
        }
        else {
            var estacao = $("#codigo_estacao_filtro").val();

            if (estacao!='') {
                filtro_estacao = '->Estação:' + estacao ;
            }
            else {
                filtro_estacao = '';
            }
        }

        if (codigo_numerico!='') {
            var descricao_filtro = 'Nº do Animal: ' + codigo_numerico;
        }
        else {
            var descricao_filtro = desc_movimentacao+origem+categorias+'->Sexo:'+sexo
            +racas+pai+mae+data_filtro+peso_nasc_filtro+peso_desmama_filtro+
            peso_ult_filtro+
            previsao_filtro+partos_filtro+aborto_filtro+filtro_paridas+filtro_solteiras+
            filtro_descarte+filtro_positivo+filtro_negativo+filtro_estacao;
        }
    }
    else {
        var descricao_filtro = desc_movimentacao+categorias+'Sexo:'+sexo;
    }

    $("#descricao_filtro_dig").val(descricao_filtro);
    $(".descricao_filtro_dig").text(descricao_filtro);
    $(".filtro_primeira_tela").show();

    $('#modal_filtros').modal('hide');
}*/


function aplicar_filtros() {
    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='I') {
        var codigo_numerico = $("#codigo_number_filtro").val();

        if (codigo_numerico!='') {
            $.post("ler_animal_aplicar_filtro.php", {
                id_animal:codigo_numerico 
                }, function(valor){

                if (valor==0) {
                    $("#mensagem_erro_animal_filtro").modal();
                    $("#codigo_number_filtro").val('');
                    document.getElementById("codigo_number_filtro").focus();
                    document.getElementById("codigo_number_filtro").style.borderColor = "red";
                    return;
                }
                else {
                    $("#macho").prop("checked", true);
                    $("#femea").prop("checked", true);
                    $("#sim_filtro").prop("checked", true);
                    //$("#nao_filtro").prop("checked", false);
                    $("#codigo_categoria_filtro").val('');
                    $("#codigo_origem_filtro").val([]);
                    $('#codigo_origem_filtro').selectpicker('val', '');
                    $("#codigo_raca_filtro").val([]);
                    $('#codigo_raca_filtro').selectpicker('val', '');
                    $("#codigo_pai_filtro").val([]);
                    $('#codigo_pai_filtro').selectpicker('val', '');
                    $("#codigo_mae_filtro").val([]);
                    $('#codigo_mae_filtro').selectpicker('val', '');
                    $("#peso_inicial_nasc_filtro").val('');
                    $("#peso_final_nasc_filtro").val('');
                    $("#peso_inicial_desmama_filtro").val('');
                    $("#peso_final_desmama_filtro").val('');
                    $("#peso_inicial_ultimo_filtro").val('');
                    $("#peso_final_ultimo_filtro").val('');
                    $("#data_nasc_inicial_filtro").val('');
                    $("#data_nasc_final_filtro").val('');
                    $("#previsao_parto_de_filtro").val('');
                    $("#previsao_parto_ate_filtro").val('');
                    $("#data_paricao_de_filtro").val('');
                    $("#data_paricao_ate_filtro").val('');
                    $("#num_parto_de_filtro").val('');
                    $("#num_parto_ate_filtro").val('');
                    $("#num_aborto_de_filtro").val('');
                    $("#num_aborto_ate_filtro").val('');
                    $("#vacas_paridas").prop("checked", false);
                    $("#paridas_ate").val('');
                    $("#vacas_solteiras").prop("checked", false);
                    $("#vacas_prenhes").prop("checked", false);
                    $("#descarte").prop("checked", false);
                    $("#positivo").prop("checked", false);
                    $("#negativo").prop("checked", false);
                    $("#iatf").prop("checked", false);
                    $("#monta_natural").prop("checked", false);
                    $('.selectpicker').selectpicker('refresh');
                    $('.filtro_reproducao').show();
                    divFiltroReproducaoVisivel = true;
                    exibe_filtro();
                }
            });
        }
        else {
            var peso_nasc_inicial =$("#peso_inicial_nasc_filtro").val();
            var peso_nasc_final = $("#peso_final_nasc_filtro").val();
            var peso_desmama_inicial = $("#peso_inicial_desmama_filtro").val();
            var peso_desmama_final = $("#peso_final_desmama_filtro").val();
            var peso_ult_inicial = $("#peso_inicial_ultimo_filtro").val();
            var peso_ult_final = $("#peso_final_ultimo_filtro").val();
            var data_nasc_inicial = $("#data_nasc_inicial_filtro").val();
            var data_nasc_final = $("#data_nasc_final_filtro").val();
            var num_parto_de = $("#num_parto_de_filtro").val();
            var num_parto_ate = $("#num_parto_ate_filtro").val();
            var num_aborto_de = $("#num_aborto_de_filtro").val();
            var num_aborto_ate = $("#num_aborto_ate_filtro").val();
            var previsao_parto_de = $("#previsao_parto_de_filtro").val();
            var previsao_parto_ate = $("#previsao_parto_ate_filtro").val();
            var data_paricao_de = $("#data_paricao_de_filtro").val();
            var data_paricao_ate = $("#data_paricao_ate_filtro").val();
            var filtro_estacao = $("#codigo_estacao_filtro").val();

            var ativo_sim = $('#sim_filtro');
            var ativo='S';

            /*var ativo_nao = $('#nao_filtro');

            if (ativo_sim.is(":checked") && ativo_nao.is(":checked")) {
                var ativo='Todos';
            }
            else if (ativo_sim.is(":checked")){
                var ativo='S';
            }
            else if (ativo_nao.is(":checked")){
                var ativo='N';
            }
            else {
                $("#mensagem_erro_filtro").modal();
                $("#mensagem_erro_filtro .modal-body").html('Informe Ativo Sim ou Não!');
                return;
            }*/

            var macho = $('#macho');
            var femea = $('#femea');

            if (macho.is(":checked") && femea.is(":checked")) {
                sexo=['Todos'];
            }
            else if (macho.is(":checked")){
                sexo=['M'];
            }
            else if (femea.is(":checked")){
                sexo=['F'];
            }
            else {
                $("#mensagem_erro_filtro").modal();
                $("#mensagem_erro_filtro .modal-body").html('Informe o Sexo!');
                return;
            }

            if (data_nasc_inicial!='' || data_nasc_final!='') {
                if (data_nasc_inicial=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Data de Nascimento Inicial não pode ser vazio!');
                    document.getElementById("data_nasc_inicial_filtro").focus();
                    document.getElementById("data_nasc_inicial_filtro").style.borderColor = "red";
                    return;
                }

                if (data_nasc_final=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Data de Nascimento Final não pode ser vazio!');
                    document.getElementById("data_nasc_final_filtro").focus();
                    document.getElementById("data_nasc_final_filtro").style.borderColor = "red";
                    return;
                }

                if (data_nasc_inicial > data_nasc_final) {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('A Data Final não pode ser menor que a Data Inicial!');
                    document.getElementById("data_nasc_final_filtro").focus();
                    document.getElementById("data_nasc_final_filtro").style.borderColor = "red";
                    return;
                }
            }

            if (peso_nasc_inicial!='' || peso_nasc_final!='') {
                if (peso_nasc_inicial=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Peso do Nascimento Inicial não pode ser vazio!');
                    document.getElementById("peso_inicial_nasc_filtro").focus();
                    document.getElementById("peso_inicial_nasc_filtro").style.borderColor = "red";
                    return;
                }

                if (peso_nasc_final=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Peso do Nascimento Final não pode ser vazio!');
                    document.getElementById("peso_final_nasc_filtro").focus();
                    document.getElementById("peso_final_nasc_filtro").style.borderColor = "red";
                    return;
                }

                var peso_nasc_inicial = parseInt($("#peso_inicial_nasc_filtro").val());
                var peso_nasc_final = parseInt($("#peso_final_nasc_filtro").val());

                if (peso_nasc_inicial > peso_nasc_final) {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Peso do Nascimento Final não pode ser menor que o Peso do Nascimento Inicial!');
                    document.getElementById("peso_final_nasc_filtro").focus();
                    document.getElementById("peso_final_nasc_filtro").style.borderColor = "red";
                    return;
                }
            }

            if (peso_desmama_inicial!='' || peso_desmama_final!='') {
                if (peso_desmama_inicial=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Peso da Desmama Inicial não pode ser vazio!');
                    document.getElementById("peso_inicial_desmama_filtro").focus();
                    document.getElementById("peso_inicial_desmama_filtro").style.borderColor = "red";
                    return;
                }

                if (peso_desmama_final=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Peso da Desmama Final não pode ser vazio!');
                    document.getElementById("peso_final_desmama_filtro").focus();
                    document.getElementById("peso_final_desmama_filtro").style.borderColor = "red";
                    return;
                }

                var peso_desmama_inicial = parseInt($("#peso_inicial_desmama_filtro").val());
                var peso_desmama_final = parseInt($("#peso_final_desmama_filtro").val());

                if (peso_desmama_inicial > peso_desmama_final) {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Peso da Desmama Final não pode ser menor que o Peso da Desmama Inicial!');
                    document.getElementById("peso_final_desmama_filtro").focus();
                    document.getElementById("peso_final_desmama_filtro").style.borderColor = "red";
                    return;
                }
            }

            if (peso_ult_inicial!='' || peso_ult_final!='') {
                if (peso_ult_inicial=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Último Peso Inicial não pode ser vazio!');
                    document.getElementById("peso_inicial_ultimo_filtro").focus();
                    document.getElementById("peso_inicial_ultimo_filtro").style.borderColor = "red";
                    return;
                }

                if (peso_ult_final=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Último Peso Final não pode ser vazio!');
                    document.getElementById("peso_final_ultimo_filtro").focus();
                    document.getElementById("peso_final_ultimo_filtro").style.borderColor = "red";
                    return;
                }

                var peso_ult_inicial = parseInt($("#peso_inicial_ultimo_filtro").val());
                var peso_ult_final = parseInt($("#peso_final_ultimo_filtro").val());

                if (peso_ult_inicial > peso_ult_final) {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Último Peso Final não pode ser menor que o Último Peso Inicial!');
                    document.getElementById("peso_final_ultimo_filtro").focus();
                    document.getElementById("peso_final_ultimo_filtro").style.borderColor = "red";
                    return;
                }
            }

            if (previsao_parto_de!='' || previsao_parto_ate!='') {
                if (previsao_parto_de=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('A Previsão de Parto (de) não pode ser vazio!');
                    document.getElementById("previsao_parto_de_filtro").focus();
                    document.getElementById("previsao_parto_de_filtro").style.borderColor = "red";
                    return;
                }

                if (previsao_parto_ate=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('A Previsão de Parto (até) não pode ser vazio!');
                    document.getElementById("previsao_parto_ate_filtro").focus();
                    document.getElementById("previsao_parto_ate_filtro").style.borderColor = "red";
                    return;
                }

                if (previsao_parto_de > previsao_parto_ate) {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('A Previsão de Parto (até) não pode ser menor que a Previsão de Parto (de)!');
                    document.getElementById("previsao_parto_ate_filtro").focus();
                    document.getElementById("previsao_parto_ate_filtro").style.borderColor = "red";
                    return;
                }
            }

            if (data_paricao_de!='' || data_paricao_ate!='') {
                if (data_paricao_de=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('A Data de Parição (de) não pode ser vazio!');
                    document.getElementById("data_paricao_de_filtro").focus();
                    document.getElementById("data_paricao_de_filtro").style.borderColor = "red";
                    return;
                }

                if (data_paricao_ate=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('A Data de Parição (até) não pode ser vazio!');
                    document.getElementById("data_paricao_ate_filtro").focus();
                    document.getElementById("data_paricao_ate_filtro").style.borderColor = "red";
                    return;
                }

                if (data_paricao_de > data_paricao_ate) {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('A Data de Parição (até) não pode ser menor que a Data de Parição (de)!');
                    document.getElementById("data_paricao_ate_filtro").focus();
                    document.getElementById("data_paricao_ate_filtro").style.borderColor = "red";
                    return;
                }
            }

            if (num_parto_de!='' || num_parto_ate!='') {
                if (num_parto_de=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Nº Partos (de) não pode ser vazio!');
                    document.getElementById("num_parto_de_filtro").focus();
                    document.getElementById("num_parto_de_filtro").style.borderColor = "red";
                    return;
                }

                if (num_parto_ate=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Nº Partos (até) não pode ser vazio!');
                    document.getElementById("num_parto_ate_filtro").focus();
                    document.getElementById("num_parto_ate_filtro").style.borderColor = "red";
                    return;
                }

                var num_parto_de = parseInt($("#num_parto_de_filtro").val());
                var num_parto_ate = parseInt($("#num_parto_ate_filtro").val());

                if (num_parto_de > num_parto_ate) {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Nº Partos (até) não pode ser menor que o Nº Partos (de)!');
                    document.getElementById("num_parto_ate_filtro").focus();
                    document.getElementById("num_parto_ate_filtro").style.borderColor = "red";
                    return;
                }
            }

            if (num_aborto_de!='' || num_aborto_ate!='') {
                if (num_aborto_de=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Nº Abortos (de) não pode ser vazio!');
                    document.getElementById("num_aborto_de_filtro").focus();
                    document.getElementById("num_aborto_de_filtro").style.borderColor = "red";
                    return;
                }

                if (num_aborto_ate=='') {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Nº Abortos (até) não pode ser vazio!');
                    document.getElementById("num_aborto_ate_filtro").focus();
                    document.getElementById("num_aborto_ate_filtro").style.borderColor = "red";
                    return;
                }

                var num_aborto_de = parseInt($("#num_aborto_de_filtro").val());
                var num_aborto_ate = parseInt($("#num_aborto_ate_filtro").val());

                if (num_aborto_de > num_aborto_ate) {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Nº Abortos (até) não pode ser menor que o Nº Abortos (de)!');
                    document.getElementById("num_aborto_ate_filtro").focus();
                    document.getElementById("num_aborto_ate_filtro").style.borderColor = "red";
                    return;
                }
            }

            if ($("#positivo").is(":checked") == true){
                positivo='S';
            }
            else {
                positivo='';
            }

            if ($("#negativo").is(":checked") == true){
                negativo='S';
            }
            else {
                negativo='';
            }

            if ($("#iatf").is(":checked") == true){
                iatf = 'S';
            }
            else {
                iatf = '';
            }

            if ($("#monta_natural").is(":checked") == true){
                monta_natural = 'S';
            }
            else {
                monta_natural = '';
            }

            if (positivo=="S" && (iatf=='' && monta_natural=='')) {
                $("#mensagem_erro_filtro").modal();
                $("#mensagem_erro_filtro .modal-body").html('Para Filtro Positivo, selecione IATF e ou Monta Natural!');
                return;
            }

            if (iatf=='S') {
                if (filtro_estacao==null) {
                    $("#mensagem_erro_filtro").modal();
                    $("#mensagem_erro_filtro .modal-body").html('Para Filtro IATF, selecione a Estação de Monta!');
                    $('#codigo_estacao_filtro').closest('.bootstrap-select').addClass('selectpicker-erro');
                    document.getElementById("codigo_estacao_filtro").focus();
                    return;
                }
                else {
                    $('#codigo_estacao_filtro').closest('.bootstrap-select').removeClass('selectpicker-erro');
                }            
            }
            exibe_filtro();
        }
    }
    else {
        var macho = $('#macho');
        var femea = $('#femea');

        if (macho.is(":checked") && femea.is(":checked")) {
            sexo=['Todos'];
        }
        else if (macho.is(":checked")){
            sexo=['M'];
        }
        else if (femea.is(":checked")){
            sexo=['F'];
        }
        else {
            $("#mensagem_erro_filtro").modal();
            $("#mensagem_erro_filtro .modal-body").html('Informe o Sexo!');
            return;
        }
        
        exibe_filtro();
    }
}

function exibe_filtro(){
    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='I') {
        var codigo_numerico = $("#codigo_number_filtro").val();
        var peso_nasc_inicial = $("#peso_inicial_nasc_filtro").val();
        var peso_nasc_final = $("#peso_final_nasc_filtro").val();
        var peso_desmama_inicial = $("#peso_inicial_desmama_filtro").val();
        var peso_desmama_final = $("#peso_final_desmama_filtro").val();
        var peso_ult_inicial = $("#peso_inicial_ultimo_filtro").val();
        var peso_ult_final = $("#peso_final_ultimo_filtro").val();
        var data_nasc_inicial = $("#data_nasc_inicial_filtro").val();
        var data_nasc_final = $("#data_nasc_final_filtro").val();
        var estacao = $("#codigo_estacao_filtro").val();
        filtro_ativo = 'Ativo:Sim';

        var macho = $('#macho');
        var femea = $('#femea');

        if (macho.is(":checked") && femea.is(":checked")) {
            sexo=['M;F'];
        }
        else if (macho.is(":checked")){
            sexo=['M'];
        }
        else if (femea.is(":checked")){
            sexo=['F'];
        }

        if (peso_nasc_inicial!='' && peso_nasc_inicial!=0) {
            peso_nasc_filtro = '->Peso Nasc: ' + peso_nasc_inicial + ' até ' + peso_nasc_final;
        }
        else {
            peso_nasc_filtro = '';
        }

        if (peso_desmama_inicial!='' && peso_desmama_inicial!=0) {
            peso_desmama_filtro = '->Peso Desmama: ' + peso_desmama_inicial + ' até ' + peso_desmama_final;
        }
        else {
            peso_desmama_filtro = '';
        }

        if (peso_ult_inicial!='' && peso_ult_inicial!=0) {
            peso_ult_filtro = '->Último Peso: ' + peso_ult_inicial + ' até ' + peso_ult_final;
        }
        else {
            peso_ult_filtro = '';
        }

        if (data_nasc_inicial!='' && data_nasc_inicial!=0) {
            var data_ini = data_nasc_inicial.split("-");
            var data_fim = data_nasc_final.split("-");

            data_nasc_filtro = '->Data Nasc: ' + data_ini[2]+'/'+data_ini[1]+'/'+data_ini[0] + ' até ' + data_fim[2]+'/'+data_fim[1]+'/'+data_fim[0];
        }
        else {
            data_nasc_filtro = '';
        }

        var options = $('#codigo_origem_filtro option:selected');
        var origem_filtro = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_origem_filtro').text();
            origem_filtro.push( desc.trim() );
        });

        if (origem_filtro!=''){
            origem_filtro = '->Origem:'+origem_filtro;
        }
        else {
            origem_filtro = '';
        }

        var options = $('#codigo_categoria_filtro option:selected');
        var categoria_filtro = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_categoria_filtro').text();
            categoria_filtro.push( desc.trim() );
        });

        if (categoria_filtro!=''){
            categoria_filtro = '->Categorias:'+categoria_filtro;
        }
        else {
            categoria_filtro = '';
        }

        var options = $('#codigo_raca_filtro option:selected');
        var raca_filtro = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_raca_filtro').text();
            raca_filtro.push( desc.trim() );
        });

        if (raca_filtro!=''){
            raca_filtro = '->Raça:'+raca_filtro;
        }
        else {
            raca_filtro = '';
        }

        var options = $('#codigo_pai_filtro option:selected');
        var pai_filtro = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_pai_filtro').text();
            pai_filtro.push( desc.trim() );
        });

        if (pai_filtro!=''){
            pai_filtro = '->Pai:'+pai_filtro;
        }
        else {
            pai_filtro = '';
        }

        var options = $('#codigo_mae_filtro option:selected');
        var mae_filtro = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_mae_filtro').text();
            mae_filtro.push( desc.trim() );
        });

        if (mae_filtro!=''){
            mae_filtro = '->Mãe:'+mae_filtro;
        }
        else {
            mae_filtro = '';
        }

        var previsao_parto_de = $("#previsao_parto_de_filtro").val();
        var previsao_parto_ate = $("#previsao_parto_ate_filtro").val();

        if (previsao_parto_de!='' && previsao_parto_ate!=0) {
            var data_ini = previsao_parto_de.split("-");
            var dia_ini = data_ini[2];
            var mes_ini = data_ini[1];
            var ano_ini = data_ini[0];

            var data_fim = previsao_parto_ate.split("-");
            var dia_fim = data_fim[2];
            var mes_fim = data_fim[1];
            var ano_fim = data_fim[0];
            previsao_filtro = '->Previsao Parto: de ' + dia_ini + "/" + mes_ini + "/" + ano_ini + ' ate ' +
                                                  dia_fim + "/" + mes_fim + "/" + ano_fim;
        }
        else {
            previsao_filtro = '';
        }

        var data_paricao_de = $("#data_paricao_de_filtro").val();
        var data_paricao_ate = $("#data_paricao_ate_filtro").val();

        if (data_paricao_de!='' && data_paricao_ate!=0) {
            var data_ini = data_paricao_de.split("-");
            var dia_ini = data_ini[2];
            var mes_ini = data_ini[1];
            var ano_ini = data_ini[0];

            var data_fim = data_paricao_ate.split("-");
            var dia_fim = data_fim[2];
            var mes_fim = data_fim[1];
            var ano_fim = data_fim[0];
            data_paricao_filtro = '->Data Parição: de ' + dia_ini + "/" + mes_ini + "/" + ano_ini + ' ate ' +
                                                  dia_fim + "/" + mes_fim + "/" + ano_fim;
        }
        else {
            data_paricao_filtro = '';
        }

        var num_parto_de = $("#num_parto_de_filtro").val();
        var num_parto_ate = $("#num_parto_ate_filtro").val();

        if (num_parto_de!='' || num_parto_ate!='') {
            partos_filtro = '->Partos: de ' + num_parto_de + ' ate ' + num_parto_ate;
        }
        else {
            partos_filtro = '';
        }

        var num_aborto_de = $("#num_aborto_de_filtro").val();
        var num_aborto_ate = $("#num_aborto_ate_filtro").val();

        if (num_aborto_de!='' || num_aborto_ate!='') {
            aborto_filtro = '->Abortos: de ' + num_aborto_de + ' ate ' + num_aborto_ate;
        }
        else {
            aborto_filtro = '';
        }

        if ($("#vacas_paridas").is(":checked") == true){
            filtro_paridas = '->Paridas';
        }
        else {
            filtro_paridas = '';
            //data_paridas = '';
        }

        if ($("#vacas_solteiras").is(":checked") == true){
            filtro_solteiras = '->Solteiras';
        }
        else {
            filtro_solteiras = '';
        }

        if ($("#vacas_prenhes").is(":checked") == true){
            filtro_prenhas = '->Prenhas';
        }
        else {
            filtro_prenhas = '';
        }

        if ($("#descarte").is(":checked") == true){
            filtro_descarte = '->Descarte';
        }
        else {
            filtro_descarte = '';
        }

        if ($("#positivo").is(":checked") == true){
            filtro_positivo = '->Diagnostico Positivo';
        }
        else {
            filtro_positivo = '';
        }

        if ($("#negativo").is(":checked") == true){
            filtro_negativo = '->Diagnostico Negativo';
        }
        else {
            filtro_negativo = '';
        }

        if ($("#iatf").is(":checked") == true){
            filtro_iatf = '->IATF';
        }
        else {
            filtro_iatf = '';
        }

        if ($("#monta_natural").is(":checked") == true){
            filtro_monta_natural = '->Monta Natural';
        }
        else {
            filtro_monta_natural = '';
        }

        var options = $('#codigo_estacao_filtro option:selected');
        var filtro_estacao = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_estacao_filtro').text();
            filtro_estacao.push( desc.trim() );
        });

        if (filtro_estacao!=''){
            filtro_estacao = '->Estação:'+filtro_estacao;
        }
        else {
            filtro_estacao = '';
        }

        if (codigo_numerico!='') {
            var descricao_filtro = 'Nº do Animal: ' + codigo_numerico;
        }
        else {
        var descricao_filtro = 
            filtro_ativo+
            '->Sexo:'+sexo+
            categoria_filtro+
            origem_filtro+
            raca_filtro+
            pai_filtro+
            mae_filtro+
            data_nasc_filtro+
            peso_nasc_filtro+
            peso_desmama_filtro+
            peso_ult_filtro+
            filtro_paridas+
            filtro_solteiras+
            filtro_prenhas+
            filtro_positivo+
            filtro_iatf+
            filtro_estacao+
            filtro_monta_natural+
            filtro_negativo+
            filtro_descarte+
            previsao_filtro+
            data_paricao_filtro+
            partos_filtro+
            aborto_filtro;
        }
    }
    else {
        filtro_ativo = '->Ativo:Sim';
        var macho = $('#macho');
        var femea = $('#femea');

        if (macho.is(":checked") && femea.is(":checked")) {
            sexo=['M;F'];
        }
        else if (macho.is(":checked")){
            sexo=['M'];
        }
        else if (femea.is(":checked")){
            sexo=['F'];
        }

        var options = $('#codigo_local_filtro option:selected');
        var local_filtro = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_local_filtro').text();
            local_filtro.push( desc.trim() );
        });

        if (local_filtro!=''){
            local_filtro = local_filtro+'->';
        }
        else {
            local_filtro = '';
        }

        var options = $('#codigo_categoria_filtro option:selected');
        var categoria_filtro = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_categoria_filtro').text();
            categoria_filtro.push( desc.trim() );
        });

        if (categoria_filtro!=''){
            categoria_filtro = 'Categorias:'+categoria_filtro+'->';
        }
        else {
            categoria_filtro = '';
        }

        var descricao_filtro = desc_movimentacao+
            categoria_filtro+
            'Sexo:'+sexo+
            filtro_ativo;
    }

    $("#descricao_filtro_dig").val(descricao_filtro);
    $(".descricao_filtro_dig").text(descricao_filtro);
    $(".filtro_primeira_tela").show();

    //$("div[id=lista_animais]").html('');
}

function redigita_animal_filtro(){
    $('#modal_filtros').modal('show');
}

function limpa_radio_tipo_movimentacao() {
    tem_itens = 'N';

    $('#tabela_itens tbody tr').each(function(){
        var id_lista = $(this).find('.id_animal').html();

        if (id_lista!=''){
            tem_itens = 'S';
        }
    });

    if (tem_itens=='N'){
        $("#compra").prop("checked", false);
        $("#transferencia").prop("checked", false);
        $("#venda").prop("checked", false);
        $("#morte").prop("checked", false);
        $("#outras").prop("checked", false);
        $("select[name=local_origem]").html('');
        $("select[name=local_destino]").html('');
    }
}

function abrir_filtro_reproducao() {
    var femea = $('#femea');
    var macho = $('#macho');
    var selectElement = document.getElementById('codigo_categoria_filtro');
    var optionToDeselect = selectElement.querySelector('option[value="001"]');
        
    if ((femea.is(":checked") && macho.is(":checked")) || optionToDeselect){
        $("#mensagem_filtro_reproducao").modal();
        return;
    }
    else {
        $('.abrir_filtro_reproducao').hide();
        $('.filtro_reproducao').show();
        divFiltroReproducaoVisivel = true;
    }
}

function fechar_filtro_reproducao() {
    $('.abrir_filtro_reproducao').show();
    $('.filtro_reproducao').hide();
    divFiltroReproducaoVisivel = false;
    limpar_filtros_reproducao();
}

function abrir_filtro_reproducao_continuar() {
    $('.abrir_filtro_reproducao').hide();
    $('.filtro_reproducao').show();
    divFiltroReproducaoVisivel = true;
    $("#macho").prop("checked", false);

    const selectElement = document.getElementById('codigo_categoria_filtro');
    const optionToDeselect = selectElement.querySelector('option[value="001"]');
    if (optionToDeselect) {
        optionToDeselect.selected = false;
        $('#codigo_categoria_filtro').selectpicker('refresh');    
    }

    let valoresParaMarcar = ['002', '003', '004', '005'];

    var selectId = '#codigo_categoria_filtro';
    $(selectId).selectpicker();
    $(selectId).selectpicker('refresh');
    var valoresSelecionados = $(selectId).val() || [];
    
    if (valoresSelecionados.length === 0) {
        $(selectId).val(valoresParaMarcar);
        $(selectId).selectpicker('refresh'); 
    }

    exibe_filtro();    
}

function limpar_filtros(){
    //$("#local_origem").val('000000000');
    $("#codigo_number_filtro").val('');
    $("#macho").prop("checked", true);
    $("#femea").prop("checked", true);
    $("#codigo_origem_filtro").val([]);
    $('#codigo_origem_filtro').selectpicker('val', '');
    $("#codigo_categoria_filtro").val([]);
    $('#codigo_categoria_filtro').selectpicker('val', '');
    $("#codigo_raca_filtro").val([]);
    $('#codigo_raca_filtro').selectpicker('val', '');
    $("#codigo_pai_filtro").val([]);
    $('#codigo_pai_filtro').selectpicker('val', '');
    $("#codigo_mae_filtro").val([]);
    $('#codigo_mae_filtro').selectpicker('val', '');
    $("#peso_inicial_nasc_filtro").val('');
    $("#peso_final_nasc_filtro").val('');
    $("#peso_inicial_desmama_filtro").val('');
    $("#peso_final_desmama_filtro").val('');
    $("#peso_inicial_ultimo_filtro").val('');
    $("#peso_final_ultimo_filtro").val('');
    $("#data_nasc_inicial_filtro").val('');
    $("#data_nasc_final_filtro").val('');
    $(".descricao_filtro_dig").val('');
    $(".descricao_filtro_dig").text('');
    $("#previsao_parto_de_filtro").val('');
    $("#previsao_parto_ate_filtro").val('');
    $("#data_paricao_de_filtro").val('');
    $("#data_paricao_ate_filtro").val('');
    $("#num_parto_de_filtro").val('');
    $("#num_parto_ate_filtro").val('');
    $("#num_aborto_de_filtro").val('');
    $("#num_aborto_ate_filtro").val('');
    $("#vacas_paridas").prop("checked", false);
    $("#vacas_solteiras").prop("checked", false);
    $("#vacas_prenhes").prop("checked", false);
    $("#descarte").prop("checked", false);
    $("#positivo").prop("checked", false);
    $("#negativo").prop("checked", false);
    $("#codigo_estacao_filtro").empty();
    $("#codigo_estacao_filtro").val([]);
    $('#codigo_estacao_filtro').selectpicker('val', '');
    $("#iatf").prop("checked", false);
    $("#monta_natural").prop("checked", false);
    $('.filtro_reproducao').hide();
    divFiltroReproducaoVisivel = false;
    $('.abrir_filtro_reproducao').show();
}

function limpar_filtros_reproducao(){
    $("#previsao_parto_de_filtro").val('');
    $("#previsao_parto_ate_filtro").val('');
    $("#data_paricao_de_filtro").val('');
    $("#data_paricao_ate_filtro").val('');
    $("#num_parto_de_filtro").val('');
    $("#num_parto_ate_filtro").val('');
    $("#num_aborto_de_filtro").val('');
    $("#num_aborto_ate_filtro").val('');
    $("#vacas_paridas").prop("checked", false);
    $("#vacas_solteiras").prop("checked", false);
    $("#vacas_prenhes").prop("checked", false);
    $("#descarte").prop("checked", false);
    $("#positivo").prop("checked", false);
    $("#negativo").prop("checked", false);
    $("#iatf").prop("checked", false);
    $("#monta_natural").prop("checked", false);
    $("#codigo_estacao_filtro").empty();
    $("#codigo_estacao_filtro").val([]);
    $('#codigo_estacao_filtro').selectpicker('val', '');
    $('.selectpicker').selectpicker('refresh');
    aplicar_filtros();
}

function filtros() {
    $('#modal_filtros').modal('show');
}

function salvar(){
    tout = setTimeout('incluir_animal_lista()', 800);
}

function incluir_animal_lista(){
    var controle_estoque = $("#controle_estoque").val();

    $(".alert_erro_animal").hide();
    var qtd_a_digitar = $("#qtd_a_digitar").val();
    var animal_codigo_id = $("#codigo_id").val();

    if (qtd_a_digitar=='' || qtd_a_digitar==0) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a quantidade de animais a digitar.');
        $(".alert_erro_animal").show();
        document.getElementById("qtd_a_digitar").focus();
        return;
    }

    if (animal_codigo_id==0 && controle_estoque=='I') {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Animal não consta no local ou Id não cadastrado.');
        $(".alert_erro_animal").show();
        $('#id_animal').val('');
        document.getElementById("id_animal").focus();
        return;
    }

    if (controle_estoque=='I') {
        var id_animal= $('#id_animal').val();

        $('#tabela_itens_digitados tbody tr').each(function(){
            var id_lista = $(this).find('.id_animal').html();

            if (id_lista==id_animal){
                $("#peso_animal").val('');
                $("#observacao").val('');
                $("#descricao_animal").text('');
                $(".alert_erro_animal .negrito").html('');
                $(".alert_erro_animal span").html('Animal já consta na lista!');
                $(".alert_erro_animal").show();
                document.getElementById("descricao_animal").text(' ');
                return;
            }
        });
    }

    var a_digitar = $("#qtd_a_digitar").val();
    var qtd_digitado = $("#qtd_digitado").val()*1;

    if (controle_estoque=='L') {
        var qtd_cat_individual=$("#qtd_cat_individual").val()*1;
        qtd_digitado = qtd_digitado + qtd_cat_individual;
    }
    else {
        qtd_digitado++;
    }

    if (qtd_digitado>a_digitar && a_digitar!=0) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Quantida de animais digitados está maior que a quantidade informada!');
        document.getElementById("qtd_digitado").style.borderColor = "red";
        $(".alert_erro_animal").show();
    }

    $("#qtd_digitado").val(qtd_digitado);

    if (controle_estoque=='L') {
        var codigo_categoria = $("#codigo_categoria_individual").val();

        if (codigo_categoria=='000'){
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Selecione uma Categoria/Sexo.');
            $(".alert_erro_animal").show();
            return;
        }

        var select = $("#codigo_categoria_individual").val();

        if (select!='000') {
            select = document.getElementById('codigo_categoria_individual');
            desc_categoria = select.options[select.selectedIndex].text;
        }

        var qtd_cat_individual = $("#qtd_cat_individual").val();

        if (qtd_cat_individual=='' || qtd_cat_individual==0){
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Informe a quantidade.');
            $(".alert_erro_animal").show();
            return;
        }
    }

    if (controle_estoque=='I') {
        var codigo_categoria = $("#categoria_animal").val();
        switch (codigo_categoria) {
        case '001':
            desc_categoria = '00 a 07 meses';
            break;
        case '002':
            desc_categoria = '08 a 12 meses';
            break;
        case '003':   
            desc_categoria = '13 a 24 meses';
            break;
        case '004':   
            desc_categoria = '25 a 36 meses';
            break;
        case '005':   
            desc_categoria = '> 36 meses';
            break;
        } 

        $("#tabela_itens_digitados tbody").append(
            "<tr>"+
            "<td width='12%' class='id_animal'>" + $("#id_animal").val() + "</td>"+
            "<td width='8%' class='peso_animal'>" + 0 + "</td>"+
            "<td width='8%' class='desc_categoria'>" + desc_categoria + "</td>"+
            "<td width='8%' class='sexo_animal'>" + $("#sexo_animal").val() + "</td>"+
            "<td width='8%' class='nascimento_animal'>" + $("#nascimento_animal").val() + "</td>"+
            "<td width='10%' class='raca_animal'>" + $("#raca_animal").val() + "</td>"+
            "<td width='8%' class='pelagem_animal'>" + $("#pelagem_animal").val() + "</td>"+
            "<td width='8%' class='mae_animal'>" + $("#mae_animal").val() + "</td>"+
            "<td width='18%' class='observacao'>" + $("#observacao").val() + "</td>"+
            "<td width='8%' hidden='' class='codigo_id'>" + $("#codigo_id").val() + "</td>"+
            "<td width='8%' hidden='' class='codigo_categoria'>" + $("#categoria_animal").val() + "</td>"+
            "<td width='12%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnexcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='excluir'></i></a></div></td>"+
            "</tr>");
    }
    else {
        $("#tabela_itens_digitados tbody").append(
            "<tr>"+
            "<td width='12%' class='desc_categoria'>" + desc_categoria + "</td>"+
            "<td width='8%' class='qtd_animal'>" + $("#qtd_cat_individual").val() + "</td>"+
            "<td width='18%' class='observacao'>" + $("#observacao").val() + "</td>"+
            "<td width='8%' hidden='' class='id_categoria'>" + $("#categoria_lote").val() + "</td>"+
            "<td width='8%' hidden='' class='sexo_animal'>" + $("#sexo_lote").val() + "</td>"+
            "<td width='8%' hidden='' class='qtd_lote'>" + $("#qtd_lote").val() + "</td>"+
            "<td width='12%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnexcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='excluir'></i></a></div></td>"+
            "</tr>");
    }

    var local_origem = $("#local_origem").val();
    var select = $("#local_origem").val();

    if (select!=0) {
        select = document.getElementById('local_origem');
        desc_origem = select.options[select.selectedIndex].text;
    }

    var local_destino = $("#local_destino").val();
    var select = $("#local_destino").val();

    if (select!=0) {
        select = document.getElementById('local_destino');
        desc_destino = select.options[select.selectedIndex].text;
    }

    $(".descricao_origem_dig").text('Local Origem: ' +  desc_origem);
    $("#descricao_origem_dig").val(desc_origem);
               
    $(".descricao_destino_dig").text('Local Destino: ' +  desc_destino);
    $("#descricao_destino_dig").val(local_destino);

    $(".btnEditar").bind("click", modal_editar_item);
    $(".btnexcluir").bind("click", excluir);
    $(".lista_animais").text('Animais Digitados');
    $("#itens_digitados").show();
    $("#dados_consulta").hide();
    //$(".botoes_venda").show();
    //$(".botoes_transferencia").hide();
    $("#id_animal").val('');
    $("#peso_animal").val('');
    $("#observacao").val('');
    $("#descricao_animal").text('');
    $("#codigo_id").val(0);
    $("#codigo_categoria_individual").val('000');
    $("#qtd_cat_individual").val('');
    $("#categoria_lote").val('');
    $("#qtd_lote").val('');
    $("#sexo_lote").val('');

    somar_totais();    

    document.getElementById("id_animal").focus();
};

function listar_animais_venda_transferencia(){
    exibe_filtro();

    var codigo_numerico = $("#codigo_number_filtro").val();
    var local = $("#codigo_local_filtro").val();
    var categoria = $("#codigo_categoria_filtro").val();
    var origem = $("#codigo_origem_filtro").val();
    var codigos_maes = $("#codigo_mae_filtro").val();
    var codigos_pais = $("#codigo_pai_filtro").val();
    var codigos_racas = $("#codigo_raca_filtro").val();
    var peso_nasc_inicial = $("#peso_inicial_nasc_filtro").val();
    var peso_nasc_final = $("#peso_final_nasc_filtro").val();
    var peso_desmama_inicial = $("#peso_inicial_desmama_filtro").val();
    var peso_desmama_final = $("#peso_final_desmama_filtro").val();
    var peso_ult_inicial = $("#peso_inicial_ultimo_filtro").val();
    var peso_ult_final = $("#peso_final_ultimo_filtro").val();
    var data_nasc_inicial = $("#data_nasc_inicial_filtro").val();
    var data_nasc_final = $("#data_nasc_final_filtro").val();
    var previsao_parto_de = $("#previsao_parto_de_filtro").val();
    var previsao_parto_ate = $("#previsao_parto_ate_filtro").val();
    var data_paricao_de = $("#data_paricao_de_filtro").val();
    var data_paricao_ate = $("#data_paricao_ate_filtro").val();
    var num_parto_de = $("#num_parto_de_filtro").val();
    var num_parto_ate =$("#num_parto_ate_filtro").val();
    var num_aborto_de = $("#num_aborto_de_filtro").val();
    var num_aborto_ate =$("#num_aborto_ate_filtro").val();
    var filtro_estacao = $("#codigo_estacao_filtro").val();

    if (categoria==null) {
        categoria=[''];
    }

    if (origem==null) {
        origem=[''];
    }

    if (codigos_maes==null) {
        codigos_maes=[''];
    }

    if (codigos_pais==null) {
        codigos_pais=[''];
    }

    if (codigos_racas==null) {
        codigos_racas=[''];
    }

    if (($("#positivo").is(":checked") == false && 
        $("#negativo").is(":checked") == false) || filtro_estacao==null){
        filtro_estacao=[''];
    }

    var macho = $('#macho');
    var femea = $('#femea');

    if (macho.is(":checked") && femea.is(":checked")) {
        sexo=['Todos'];
    }
    else if (macho.is(":checked")){
        sexo=['M'];
    }
    else if (femea.is(":checked")){
        sexo=['F'];
    }
    else {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Sexo!');
        return;
    }

    var ativo='S';
    var filtro_reproducao = 'N';

    if (num_parto_de!='' && num_parto_ate!='') {
        var filtro_num_parto = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_num_parto = 'N';
    }

    if (num_aborto_de!='' && num_aborto_ate!='') {
        var filtro_num_aborto = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_num_aborto = 'N';
    }

    if (previsao_parto_de!='' && previsao_parto_ate!='') {
        var filtro_previsao_parto = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_previsao_parto = 'N';
    }

    if (data_paricao_de!='' && data_paricao_ate!='') {
        var filtro_data_paricao = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_data_paricao = 'N';
    }

    if ($("#vacas_paridas").is(":checked") == true){
        var filtro_vacas_paridas='S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_vacas_paridas='N';
    }

    if ($("#vacas_solteiras").is(":checked") == true){
        var filtro_vacas_solteiras = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_vacas_solteiras = 'N';
    }

    if ($("#vacas_prenhes").is(":checked") == true){
        var filtro_vacas_prenhas = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_vacas_prenhas = 'N';
    }

    if ($("#descarte").is(":checked") == true){
        var filtro_descarte = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_descarte = 'N';
    }

    if ($("#positivo").is(":checked") == true){
        var filtro_positivas = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_positivas = 'N';
    }

    if ($("#negativo").is(":checked") == true){
        var filtro_negativas = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_negativas = 'N';
    }

    if ($("#monta_natural").is(":checked") == true){
        var filtro_monta_natural = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_monta_natural = 'N';
    }

    //$('#modal_filtros').modal('hide');

    $("#aguardar").modal();

    $.post("lista_animais_transferencia.php", {
        codigo_alfa_numerico:codigo_numerico, 
        local:local, 
        sexo:sexo, 
        ativo:ativo,
        categoria:categoria,
        origem:origem,
        codigos_maes: codigos_maes,
        codigos_pais: codigos_pais,
        codigos_racas: codigos_racas,
        data_nasc_inicial: data_nasc_inicial,
        data_nasc_final: data_nasc_final,
        peso_nasc_inicial: peso_nasc_inicial,
        peso_nasc_final: peso_nasc_final,
        peso_desmama_inicial: peso_desmama_inicial,
        peso_desmama_final: peso_desmama_final,
        peso_ult_inicial: peso_ult_inicial,
        peso_ult_final: peso_ult_final,
        filtro_reproducao: filtro_reproducao,
        num_parto_de: num_parto_de,
        num_parto_ate: num_parto_ate,
        filtro_num_parto:filtro_num_parto,    
        num_aborto_de: num_aborto_de,
        num_aborto_ate: num_aborto_ate,
        filtro_num_aborto:filtro_num_aborto,
        previsao_parto_de: previsao_parto_de,
        previsao_parto_ate: previsao_parto_ate,
        filtro_previsao_parto: filtro_previsao_parto,
        data_paricao_de: data_paricao_de,
        data_paricao_ate: data_paricao_ate,  
        filtro_data_paricao: filtro_data_paricao,
        filtro_vacas_paridas: filtro_vacas_paridas,
        filtro_vacas_solteiras: filtro_vacas_solteiras,
        filtro_vacas_prenhas: filtro_vacas_prenhas,
        filtro_descarte: filtro_descarte,
        filtro_positivas: filtro_positivas,
        filtro_negativas: filtro_negativas,
        filtro_monta_natural: filtro_monta_natural,
        filtro_estacao: filtro_estacao
         },
        function(valor){ 
            $('#aguardar').modal('hide');

            if (valor==0) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Não existem animais para listar com esse filtro!');
                return;
            }
            else {
                $('#modal_filtros').modal('hide');

                var controle_estoque=$("#controle_estoque").val();

                if (controle_estoque=='I') {
                    $("div[id=itens_listados]").html(valor);

                    $(".total_digitados").text('Animais Selecionados: ');
                    $(".total_digitados").val('');

                    var total_listados = 0;

                    $('#tabela_itens_digitados tbody tr').each(function(){
                        var id_lista = $(this).find('.id_animal').html();

                        if (id_lista!='' && id_lista!=undefined){
                            total_listados++;
                        }
                    });

                    $(".total_a_digitar").text('Animais Listados: ' + total_listados);
                    $(".total_a_digitar").val(total_listados);

                    var data = $("#data_movimentacao").val();

                    var dia  = data.split("-")[2];
                    var mes  = data.split("-")[1];
                    var ano  = data.split("-")[0];

                    var str_data = ("0"+dia).slice(-2) + '/' + ("0"+mes).slice(-2) + '/' + ano;    

                    $("#data_digitados").text('Data: ' + str_data);

                    var local = $("#codigo_local_filtro").val();

                    $.post("lista_animais_pasto_transferencia.php", {
                        local:local,}, function(valor){ 

                        var php = valor.split("<|>");
                        var numero_itens = php.length;

                        $("#cat_pasto_m1").val(php[0]);
                        $("#cat_pasto_m2").val(php[1]);
                        $("#cat_pasto_m3").val(php[2]);
                        $("#cat_pasto_m4").val(php[3]);
                        $("#cat_pasto_m5").val(php[4]);
                        $("#cat_pasto_f1").val(php[5]);
                        $("#cat_pasto_f2").val(php[6]);
                        $("#cat_pasto_f3").val(php[7]);
                        $("#cat_pasto_f4").val(php[8]);
                        $("#cat_pasto_f5").val(php[9]);
                    });
                }

                var local_origem = $("#local_origem").val();
                var select = $("#local_origem").val();

                if (select!=0) {
                    select = document.getElementById('local_origem');
                    desc_origem = select.options[select.selectedIndex].text;
                }

                var local_destino = $("#local_destino").val();
                var select = $("#local_destino").val();

                if (select!=0) {
                    select = document.getElementById('local_destino');
                    desc_destino = select.options[select.selectedIndex].text;
                }

                $(".descricao_origem_dig").text(desc_origem);
                $("#descricao_origem_dig").val(desc_origem);
                           
                $(".descricao_destino_dig").text(desc_destino);
                $("#descricao_destino_dig").val(local_destino);

                $(".btnEditar").bind("click", modal_editar_item);
                $(".btnexcluir").bind("click", excluir);
                $(".lista_animais").text('Animais Listados');

                var tipo_movimentacao = $("input[name='tipo_movimentacao']:checked").val();

                if (tipo_movimentacao=='V') {
                    $(".descricao_movimentacao").text('Venda');
                }
                else if (tipo_movimentacao=='T') {
                    $(".descricao_movimentacao").text('Transferência');
                }

                $("#itens_digitados").show();
                $("#dados_consulta").hide();
            }
        });
};

function listar_animais_venda_transferencia_lote(){
    exibe_filtro();

    var categoria = $("#codigo_categoria_filtro").val();
    var raca = $("#codigo_raca_filtro").val();
    var peso_nasc_inicial = $("#peso_inicial_nasc_filtro").val();
    var peso_nasc_final = $("#peso_final_nasc_filtro").val();
    var peso_desmama_inicial = $("#peso_inicial_desmama_filtro").val();
    var peso_desmama_final = $("#peso_final_desmama_filtro").val();
    var data_nasc_inicial = $("#data_nasc_inicial_filtro").val();
    var data_nasc_final = $("#data_nasc_final_filtro").val();
    var local = $("#codigo_local_filtro").val();

    var macho = $('#macho');
    var femea = $('#femea');

    if (macho.is(":checked") && femea.is(":checked")) {
        sexo=['Todos'];
    }
    else if (macho.is(":checked")){
        sexo=['M'];
    }
    else if (femea.is(":checked")){
        sexo=['F'];
    }
    else {
        alert ('Informe o sexo!');
        return;
    }

    if (data_nasc_inicial!='' || data_nasc_final!='') {
        if (data_nasc_inicial=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Data de Nascimento Inicial não pode ser vazio!');
            return;
        }

        if (data_nasc_final=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Data de Nascimento Final não pode ser vazio!');
            return;
        }

        if (data_nasc_inicial > data_nasc_final) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe a Data de Nascimento Inicial e Final corretamente!');
            return;
        }
    }

    if (peso_nasc_inicial!='' || peso_nasc_final!='') {
        if (peso_nasc_inicial=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Peso do Nascimento Inicial não pode ser vazio!');
            return;
        }

        if (peso_nasc_final=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Peso do Nascimento Final não pode ser vazio!');
            return;
        }

        if (peso_nasc_inicial > peso_nasc_final) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe o Peso do Nascimento Inicial e Final corretamente!');
            return;
        }
    }

    if (peso_desmama_inicial!='' || peso_desmama_final!='') {
        if (peso_desmama_inicial=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Peso da Desmama Inicial não pode ser vazio!');
            return;
        }

        if (peso_desmama_final=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Peso da Desmama Final não pode ser vazio!');
            return;
        }

        if (peso_desmama_inicial > peso_desmama_final) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe o Peso da Desmama Inicial e Final corretamente!');
            return;
        }
    }

    if (categoria==null) {
        categoria=[''];
    }

    if (raca==null) {
        raca=[''];
    }

    $('#modal_filtros').modal('hide');
    $("#aguardar").modal();

    $.post("lista_animais_transferencia_lote.php", {
        local:local, 
        categoria:categoria,
        raca:raca, 
        sexo:sexo, 
        peso_nasc_inicial:peso_nasc_inicial, 
        peso_nasc_final:peso_nasc_final, 
        peso_desmama_inicial:peso_desmama_inicial, 
        peso_desmama_final:peso_desmama_final, 
        data_nasc_inicial:data_nasc_inicial, 
        data_nasc_final:data_nasc_final,
         },
        function(valor){ 

            $('#aguardar').modal('hide');

            $("div[id=itens_listados]").html(valor);

            $(".total_digitados").text('Animais Selecionados: ');
            $(".total_digitados").val('');

            var total_listados = 0;

            $('#tabela_itens_digitados tbody tr').each(function(){
                var qtd_categoria =  parseInt($(this).find('.qtd_categoria').html());

                if (qtd_categoria!='' && qtd_categoria!=undefined){
                    total_listados+=qtd_categoria;
                }
            });

            $(".total_a_digitar").text('Animais Listados: ' + total_listados);
            $(".total_a_digitar").val(total_listados);

            var data = $("#data_movimentacao").val();

            var dia  = data.split("-")[2];
            var mes  = data.split("-")[1];
            var ano  = data.split("-")[0];

            var str_data = ("0"+dia).slice(-2) + '/' + ("0"+mes).slice(-2) + '/' + ano;    

            $("#data_digitados").text('Data: ' + str_data);

            var local = $("#codigo_local_filtro").val();

            $.post("lista_animais_pasto_transferencia.php", {
                local:local,}, function(valor){ 

                var php = valor.split("<|>");
                var numero_itens = php.length;

                $("#cat_pasto_m1").val(php[0]);
                $("#cat_pasto_m2").val(php[1]);
                $("#cat_pasto_m3").val(php[2]);
                $("#cat_pasto_m4").val(php[3]);
                $("#cat_pasto_m5").val(php[4]);
                $("#cat_pasto_f1").val(php[5]);
                $("#cat_pasto_f2").val(php[6]);
                $("#cat_pasto_f3").val(php[7]);
                $("#cat_pasto_f4").val(php[8]);
                $("#cat_pasto_f5").val(php[9]);
            });
    });

    var local_origem = $("#local_origem").val();
    var select = $("#local_origem").val();

    if (select!=0) {
        select = document.getElementById('local_origem');
        desc_origem = select.options[select.selectedIndex].text;
    }

    var local_destino = $("#local_destino").val();
    var select = $("#local_destino").val();

    if (select!=0) {
        select = document.getElementById('local_destino');
        desc_destino = select.options[select.selectedIndex].text;
    }

    $(".descricao_origem_dig").text('Local Origem: ' +  desc_origem);
    $("#descricao_origem_dig").val(desc_origem);
               
    $(".descricao_destino_dig").text('Local Destino: ' +  desc_destino);
    $("#descricao_destino_dig").val(local_destino);

    $(".btnEditar").bind("click", modal_editar_item);
    $(".btnexcluir").bind("click", excluir);
    $(".lista_animais").text('Animais Listados');

    $("#itens_digitados").show();
    $("#dados_consulta").hide();
}

function salvar_morte(){
    var controle_estoque = $("#controle_estoque").val();

    var animal_codigo_id = $("#codigo_id_morte").val();
    var codigo_animal = $("#id_animal_morte").val();

    if (animal_codigo_id==0 && controle_estoque=='I'){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Id do animal não cadastrado.');
        $(".alert_erro_animal").show();
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

    var pasto_morte = $("#pasto_morte").val();

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

    if (controle_estoque=='L'){
        $("#sexo_animal_morte").val(sexo_morte);
    }

    if (window.confirm("Confirmar a MORTE do animal " + codigo_animal + "?")) {    
        html = "";
        html += '<table class="table table-striped table-advance table-hover" id="tabela_itens" width="100%">';
        html += '<thead>';
        html += '<tr>';
        html += '<th>' + ' Id' + '</th>';
        html += '<th>' + ' Peso' + '</th>';
        html += '<th>' + ' Sexo' + '</th>';
        html += '<th>' + ' Nascimento' + '</th>';
        html += '<th>' + ' Raça' + '</th>';
        html += '<th>' + ' Pelagem' + '</th>';
        html += '<th>' + ' Mãe' + '</th>';
        html += '<th>' + ' Observação' + '</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        html += '</tbody>';
        html += '</table>';
        document.getElementById('tabela_itens').innerHTML = html;

        $("#tabela_itens tbody").append(
            "<tr>"+
            "<td width='12%' class='id_animal'>" + $("#id_animal_morte").val() + "</td>"+
            "<td width='8%' class='peso_animal'>" + 0 + "</td>"+
            "<td width='8%' class='sexo_animal'>" + $("#sexo_animal_morte").val() + "</td>"+
            "<td width='8%' class='nascimento_animal'>" + $("#nascimento_animal_morte").val() + "</td>"+
            "<td width='10%' class='raca_animal'>" + $("#raca_animal_morte").val() + "</td>"+
            "<td width='8%' class='pelagem_animal'>" + $("#pelagem_animal_morte").val() + "</td>"+
            "<td width='8%' class='mae_animal'>" + $("#mae_animal_morte").val() + "</td>"+
            "<td width='18%' class='observacao'>" + $("#observacao_morte").val() + "</td>"+
            "<td width='8%' hidden='' class='codigo_id'>" + $("#codigo_id_morte").val() + "</td>"+
            "<td width='8%' hidden='' class='motivo_morte'>" + $("#motivo_animal_morte").val() + "</td>"+
            "<td width='8%' hidden='' class='codigo_motivo_morte'>" + $("#codigo_motivo_morte").val() + "</td>"+
            "<td width='8%' hidden='' class='pasto_morte'>" + $("#pasto_morte").val() + "</td>"+
            "<td width='8%' hidden='' class='categoria_morte'>" + $("#categoria_digitada_morte").val() + "</td>"+
            "</tr>");

        $("#id_animal_morte").val('');
        $("#sexo_animal_morte").val('');
        $("#nascimento_animal_morte").val('');
        $("#raca_animal_morte").val('');
        $("#pelagem_animal_morte").val('');
        $("#mae_animal_morte").val('');
        $("#observacao_morte").val('');
        $("#descricao_animal_morte").text('');
        $("#codigo_id_morte").val('');
        $("#motivo_animal_morte").val('');
        $("#codigo_motivo_morte").val('');
        $("#motivo_morte").val('000');    
        $("#pasto_morte").val('000000000');    
        $("#categoria_morte").val('000');  
        $("#categoria_digitada_morte").val('');  
        $("#sexo_morte").val('');  
        $("#qtd_morte").val('');  

        somar_totais_morte();    
        gravar_morte();
    } 
};

function salvar_outra(){
    var controle_estoque = $("#controle_estoque").val();
    var animal_codigo_id = $("#codigo_id_outra").val();
    var codigo_animal = $("#id_animal_outra").val();

    if (animal_codigo_id==0 && controle_estoque=='I'){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Id do animal não cadastrado.');
        $(".alert_erro_animal").show();
        return;
    }

    var pasto_outra = $("#pasto_outra").val();

    if (pasto_outra==0){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe o Pasto!');
        $(".alert_erro_animal").show();
        return;
    }

    var categoria_outra = $("#categoria_digitada_outra").val();

    if (categoria_outra==0 && controle_estoque=='L'){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Categoria!');
        $(".alert_erro_animal").show();
        return;
    }

    var sexo_outra = $("#sexo_outra").val();

    if (controle_estoque=='L'){
        $("#sexo_animal_outra").val(sexo_outra);
    }

    var observacao_outra = $("#observacao_outra").val();

    if (observacao_outra==''){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Observação!');
        $(".alert_erro_animal").show();
        return;
    }

    if (window.confirm("Confirmar a SAÍDA do animal " + codigo_animal + "?")) {    
        html = "";
        html += '<table class="table table-striped table-advance table-hover" id="tabela_itens" width="100%">';
        html += '<thead>';
        html += '<tr>';
        html += '<th>' + ' Id' + '</th>';
        html += '<th>' + ' Peso' + '</th>';
        html += '<th>' + ' Sexo' + '</th>';
        html += '<th>' + ' Nascimento' + '</th>';
        html += '<th>' + ' Raça' + '</th>';
        html += '<th>' + ' Pelagem' + '</th>';
        html += '<th>' + ' Mãe' + '</th>';
        html += '<th>' + ' Observação' + '</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        html += '</tbody>';
        html += '</table>';
        document.getElementById('tabela_itens').innerHTML = html;

        $("#tabela_itens tbody").append(
            "<tr>"+
            "<td width='12%' class='id_animal'>" + $("#id_animal_outra").val() + "</td>"+
            "<td width='8%' class='peso_animal'>" + 0 + "</td>"+
            "<td width='8%' class='sexo_animal'>" + $("#sexo_animal_outra").val() + "</td>"+
            "<td width='8%' class='nascimento_animal'>" + $("#nascimento_animal_outra").val() + "</td>"+
            "<td width='10%' class='raca_animal'>" + $("#raca_animal_outra").val() + "</td>"+
            "<td width='8%' class='pelagem_animal'>" + $("#pelagem_animal_outra").val() + "</td>"+
            "<td width='8%' class='mae_animal'>" + $("#mae_animal_outra").val() + "</td>"+
            "<td width='18%' class='observacao'>" + $("#observacao_outra").val() + "</td>"+
            "<td width='8%' hidden='' class='codigo_id'>" + $("#codigo_id_outra").val() + "</td>"+
            "<td width='8%' hidden='' class='pasto_outra'>" + $("#pasto_outra").val() + "</td>"+
            "<td width='8%' hidden='' class='categoria_outra'>" + $("#categoria_digitada_outra").val() + "</td>"+
            "</tr>");

        $("#id_animal_outra").val('');
        $("#sexo_animal_outra").val('');
        $("#nascimento_animal_outra").val('');
        $("#raca_animal_outra").val('');
        $("#pelagem_animal_outra").val('');
        $("#mae_animal_outra").val('');
        $("#observacao_outra").val('');
        $("#descricao_animal_outra").text('');
        $("#codigo_id_outra").val('');
        $("#motivo_animal_outra").val('');
        $("#codigo_motivo_outra").val('');
        $("#motivo_outra").val('000');    
        $("#pasto_outra").val('000000000');    
        $("#categoria_outra").val('000');  
        $("#categoria_digitada_outra").val('');  
        $("#sexo_outra").val('');  
        $("#qtd_outra").val('');  

        somar_totais_morte();    
        gravar_outra();
    } 
};

function salvar_entrada(){
    var qtd_total_animais = $("#qtd_total_animais").val();
    var qtd_total_digitado = Number($("#qtd_total_digitado").val());
    var qtd_cat_entrada = Number($("#qtd_cat_entrada").val());
    var sequencia_entrada = Number($("#sequencia_numeria_entrada").val());
    var idade = $("#idade_entrada").val();
    var controle_estoque = $("#controle_estoque").val();
    var categoria = $("#codigo_categoria_entrada").val();

    if (controle_estoque=='I') {
        var peso_entrada = $("#peso_entrada").val();
    }
    else {
        var peso_entrada = $("#peso_medio").val();
    }

    if (categoria==0){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Categoria.');
        $(".alert_erro_animal").show();
        return;
    }
    else {
        select = document.getElementById('codigo_categoria_entrada');
        desc_categoria = select.options[select.selectedIndex].text;
    }

    if (idade==''){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Idade em Meses.');
        $(".alert_erro_animal").show();
        return;
    }

    if (categoria==1) {
        if (idade>7) {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Informe a Idade entre 1 e 7 meses.');
            $(".alert_erro_animal").show();
            return;
        }
    }
    else if (categoria==2) {
        if (idade<8 || idade>12) {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Informe a Idade entre 8 e 12 meses.');
            $(".alert_erro_animal").show();
            return;
        }
    }
    else if (categoria==3) {
        if (idade<13 || idade>24) {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Informe a Idade entre 13 e 24 meses.');
            $(".alert_erro_animal").show();
            return;
        }
    }
    else if (categoria==4) {
        if (idade<25 || idade>36) {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Informe a Idade entre 25 e 36 meses.');
            $(".alert_erro_animal").show();
            return;
        }
    }
    else if (categoria==5) {
        if (idade<37) {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Informe a Idade > 36 meses.');
            $(".alert_erro_animal").show();
            return;
        }
    }

    var macho = $('#macho_entrada');
    var femea = $('#femea_entrada');

    if (macho.is(":checked")){
        sexo=['M'];
    }
    else if (femea.is(":checked")){
        sexo=['F'];
    }
    else {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe o Sexo.');
        $(".alert_erro_animal").show();
        return;
    }

    var raca = $("#codigo_raca_entrada").val();

    if (raca==0 && controle_estoque=="I"){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Raça.');
        $(".alert_erro_animal").show();
        return;
    }
    else {
        select = document.getElementById('codigo_raca_entrada');
        desc_raca = select.options[select.selectedIndex].text;
    }


    if (controle_estoque=="I"){
        var pelagem = $("#codigo_pelagem_entrada").val();
        select = document.getElementById('codigo_pelagem_entrada');
        desc_pelagem = select.options[select.selectedIndex].text;
    }
    else {
        var pelagem = 0;
        desc_pelagem = '';
    }

    if (qtd_cat_entrada=='') {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Quantidade da Categoria.');
        $(".alert_erro_animal").show();
        return;
    }

    if ((qtd_total_digitado + qtd_cat_entrada)>qtd_total_animais) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Quantida de animais digitados será maior que a quantidade total de animais!');
        $(".alert_erro_animal").show();
        return;
    }

    if (sequencia_entrada=='' && controle_estoque=='I') {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Sequência Numérica Inicial.');
        $(".alert_erro_animal").show();
        return;
    }

    if (peso_entrada==0 || peso_entrada==''){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe o Peso Médio da Categoria.');
        $(".alert_erro_animal").show();
        return;
    }

    if (controle_estoque=='I') {
        $("#tabela_itens_digitados_entrada tbody").append(
            "<tr>"+
            "<td width='10%' class='desc_categoria'>" + desc_categoria + "</td>"+
            "<td width='12%' class='idade_entrada' style='text-align: left;'>" + $("#idade_entrada").val() + "</td>"+
            "<td width='8%' class='sexo_entrada' style='text-align: left;'>" + sexo + "</td>"+
            "<td width='12%' class='desc_raca'>" + desc_raca + "</td>"+
            "<td width='10%' class='desc_pelagem'>" + desc_pelagem + "</td>"+
            "<td width='10%' class='qtd_cat_entrada' style='text-align: right;'>" + $("#qtd_cat_entrada").val() + "</td>"+
            "<td width='10%' class='sequencia_numeria_entrada' style='text-align: right;'>" + $("#sequencia_numeria_entrada").val() + "</td>"+
            "<td width='10%' class='marcacao_alfa_entrada'>" + $("#marcacao_alfa_entrada").val() + "</td>"+
            "<td hidden='' class='codigo_categoria_entrada'>" + $("#codigo_categoria_entrada").val() + "</td>"+
            "<td hidden='' class='codigo_raca_entrada'>" + $("#codigo_raca_entrada").val() + "</td>"+
            "<td hidden='' class='codigo_pelagem_entrada'>" + $("#codigo_pelagem_entrada").val() + "</td>"+
            "<td width='8%' class='peso_entrada'>" + $("#peso_entrada").val() + "</td>"+
            "<td width='10%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnexcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='excluir'></i></a></div></td>"+
            "</tr>");
    }
    else {
        $("#tabela_itens_digitados_entrada tbody").append(
            "<tr>"+
            "<td width='12%' class='desc_categoria'>" + desc_categoria + "</td>"+
            "<td width='12%' class='idade_entrada' style='text-align: center;'>" + $("#idade_entrada").val() + "</td>"+
            "<td width='7%' class='sexo_entrada' style='text-align: center;'>" + sexo + "</td>"+
            "<td width='12%' class='desc_raca'>" + desc_raca + "</td>"+
            "<td hidden='' class='desc_pelagem'>" + desc_pelagem + "</td>"+
            "<td width='10%' class='qtd_cat_entrada' style='text-align: right;'>" + $("#qtd_cat_entrada").val() + "</td>"+
            "<td hidden='' class='sequencia_numeria_entrada' style='text-align: right;'>" + $("#sequencia_numeria_entrada").val() + "</td>"+
            "<td hidden='' class='marcacao_alfa_entrada'>" + $("#marcacao_alfa_entrada").val() + "</td>"+
            "<td hidden='' class='codigo_categoria_entrada'>" + $("#codigo_categoria_entrada").val() + "</td>"+
            "<td hidden='' class='codigo_raca_entrada'>" + $("#codigo_raca_entrada").val() + "</td>"+
            "<td hidden='' class='codigo_pelagem_entrada'>" + $("#codigo_pelagem_entrada").val() + "</td>"+
            "<td width='10%' class='peso_entrada' style='text-align: right;'>" + $("#peso_medio").val() + "</td>"+
            "<td width='37%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnexcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='excluir'></i></a></div></td>"+
            "</tr>");
    }

    var local_origem = $("#local_origem").val();
    var select = $("#local_origem").val();

    if (select!=0) {
        select = document.getElementById('local_origem');
        desc_origem = select.options[select.selectedIndex].text;
    }

    var local_destino = $("#local_destino").val();
    var select = $("#local_destino").val();

    if (select!=0) {
        select = document.getElementById('local_destino');
        desc_destino = select.options[select.selectedIndex].text;
    }

    $(".descricao_filtro_dig_entrada").text('Local Origem: ' +  desc_origem);
    $(".descricao_destino_dig").text('Local Destino: ' +  desc_destino);
    $("#descricao_destino_dig").val(local_destino);

    var total_digitados= 0;

    $('#tabela_itens_digitados_entrada tbody tr').each(function(){
        var qtd_cat_entrada = $(this).find('.qtd_cat_entrada').html();

        if (qtd_cat_entrada!=''){
            total_digitados+= Number(qtd_cat_entrada);
        }
    });

    $(".total_digitados_entrada").text('Animais Digitados: ' + total_digitados);
    $(".total_digitados_entrada").val(total_digitados);
    $("#qtd_total_digitado").val(total_digitados);

    $(".total_a_digitar_entrada").text('Quantidade Total de Animais: ' + qtd_total_animais);
    $(".total_a_digitar_entrada").val(qtd_total_animais);

    var qtd_total_restante = qtd_total_animais - total_digitados;
    $(".total_restante_entrada").text('Faltam Digitar: ' + qtd_total_restante);
    $("#qtd_total_restante").val(qtd_total_restante);
    
    var data = $("#data_movimentacao").val();

    var dia  = data.split("-")[2];
    var mes  = data.split("-")[1];
    var ano  = data.split("-")[0];

    var str_data = ("0"+dia).slice(-2) + '/' + ("0"+mes).slice(-2) + '/' + ano;    

    $("#data_digitados_entrada").text('Data: ' + str_data);

    $(".btnEditar").bind("click", modal_editar_entrada);
    $(".btnexcluir").bind("click", excluir_entrada);
    $("#itens_digitados_entrada").show();
    $("#dados_consulta").hide();

    $("#codigo_categoria_entrada").val('000');
    $("#idade_entrada").val('');
    $("#macho_entrada").prop("checked", false);
    $("#femea_entrada").prop("checked", false);
    $("#codigo_raca_entrada").val('000');
    $("#codigo_pelagem_entrada").val('000');
    $("#qtd_cat_entrada").val('');
    $("#sequencia_numeria_entrada").val('');
    $("#marcacao_alfa_entrada").val('');
    $("#peso_entrada").val('');
    $("#peso_medio").val('');
    document.getElementById("codigo_categoria_entrada").focus();

    if (total_digitados==qtd_total_animais) {
        pausar_digitacao_entrada();
    }

    if (controle_estoque=='I') {
        somar_sequencia_numerica();
    }
};


function salvar_editar(){
    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='I') {
        var animal_codigo_id = $("#codigo_id").val();

        if (animal_codigo_id==0) {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Id do animal não cadastrado.');
            $(".alert_erro_animal").show();
            $('#id_animal').val('');
            document.getElementById("id_animal").focus();
            return;
        }

        $('#tabela_itens_digitados tbody tr').each(function(){
            row_index_salvar = $(this).index();

            if (row_index_salvar==row_index){
                $(this).find('.id_animal').html($("#id_animal").val());
                $(this).find('.peso_animal').html($("#peso_animal").val());
                $(this).find('.sexo_animal').html($("#sexo_animal").val());
                $(this).find('.nascimento_animal').html($("#nascimento_animal").val());
                $(this).find('.categoria_animal').html($("#categoria_animal").val());
                $(this).find('.raca_animal').html($("#raca_animal").val());
                $(this).find('.pelagem_animal').html($("#pelagem_animal").val());
                $(this).find('.mae_animal').html($("#mae_animal").val());
                $(this).find('.observacao').html($("#observacao").val());
                $(this).find('.codigo_id').html($("#codigo_id").val());
            }
        });
    } 
    else {
        var codigo_categoria = $("#codigo_categoria_individual").val();

        if (codigo_categoria=='000'){
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Selecione uma Categoria/Sexo.');
            $(".alert_erro_animal").show();
            return;
        }

        var select = $("#codigo_categoria_individual").val();

        if (select!='000') {
            select = document.getElementById('codigo_categoria_individual');
            desc_categoria = select.options[select.selectedIndex].text;
        }

        var qtd_cat_individual = $("#qtd_cat_individual").val();

        if (qtd_cat_individual=='' || qtd_cat_individual==0){
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Informe a quantidade.');
            $(".alert_erro_animal").show();
            return;
        }


        $('#tabela_itens_digitados tbody tr').each(function(){
            row_index_salvar = $(this).index();

            if (row_index_salvar==row_index){
                $(this).find('.desc_categoria').html(desc_categoria);
                $(this).find('.qtd_animal').html($("#qtd_cat_individual").val());
                $(this).find('.observacao').html($("#observacao").val());
                $(this).find('.id_categoria').html($("#categoria_lote").val());
                $(this).find('.sexo_animal').html($("#sexo_lote").val());
                $(this).find('.qtd_lote').html($("#qtd_lote").val());
            }
        });
    }

    $(".btnEditar").bind("click", modal_editar_item);
    $(".btnexcluir").bind("click", excluir);

    $('#modal_individual').modal('hide');
    $(".alert_erro_animal .negrito").html('');
    $(".alert_erro_animal span").html('');
    $(".alert_erro_animal").hide();
    $("#codigo_categoria_individual").val('000');
    $("#qtd_cat_individual").val('');
    $("#categoria_lote").val('');
    $("#qtd_lote").val('');
    $("#sexo_lote").val('');
    $("#editar").hide();
    $("#incluir").show();

    somar_totais();    

    $("#codigo_id").val(0);
};

function salvar_editar_edicao(){
    var animal_codigo_id = $("#codigo_id").val();

    if (animal_codigo_id==0) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Id do animal não cadastrado.');
        $(".alert_erro_animal").show();
        $('#id_animal').val('');
        document.getElementById("id_animal").focus();
        return;
    }

    $('#tabela_itens_digitados tbody tr').each(function(){
        row_index_salvar = $(this).index();

        if (row_index_salvar==row_index){
            $(this).find('.id_animal').html($("#id_animal").val());
            $(this).find('.peso_animal').html($("#peso_animal").val());
            $(this).find('.sexo_animal').html($("#sexo_animal").val());
            $(this).find('.nascimento_animal').html($("#nascimento_animal").val());
            $(this).find('.categoria_animal').html($("#categoria_animal").val());
            $(this).find('.raca_animal').html($("#raca_animal").val());
            $(this).find('.pelagem_animal').html($("#pelagem_animal").val());
            $(this).find('.mae_animal').html($("#mae_animal").val());
            $(this).find('.observacao').html($("#observacao").val());
            $(this).find('.codigo_id').html($("#codigo_id").val());
        }
    });

    $(".btnEditar").bind("click", modal_editar_item);
    $(".btnexcluir").bind("click", excluir_edicao);

    $('#modal_individual').modal('hide');
    $(".alert_erro_animal .negrito").html('');
    $(".alert_erro_animal span").html('');
    $(".alert_erro_animal").hide();
    $("#editar").hide();
    $("#incluir").show();

    somar_totais();    

    $("#codigo_id").val(0);
};

function salvar_editar_entrada(){
    var qtd_total_animais = $("#qtd_total_animais").val();
    var qtd_total_digitado = Number($("#qtd_total_digitado").val());
    var qtd_cat_entrada = Number($("#qtd_cat_entrada").val());
    var qtd_cat_anterior = Number($("#qtd_cat_anterior").val());
    var sequencia_entrada = Number($("#sequencia_numeria_entrada").val());
    var idade = $("#idade_entrada").val();
    var controle_estoque = $("#controle_estoque").val();
    var categoria = $("#codigo_categoria_entrada").val();

    if (controle_estoque=='I') {
        var peso_entrada = $("#peso_entrada").val();
    }
    else {
        var peso_entrada = $("#peso_medio").val();
    }

    if (categoria==0){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Categoria.');
        $(".alert_erro_animal").show();
        return;
    }
    else {
        select = document.getElementById('codigo_categoria_entrada');
        desc_categoria = select.options[select.selectedIndex].text;
    }

    if (idade==''){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Idade em Meses.');
        $(".alert_erro_animal").show();
        return;
    }

    var macho = $('#macho_entrada');
    var femea = $('#femea_entrada');

    if (macho.is(":checked")){
        sexo=['M'];
    }
    else if (femea.is(":checked")){
        sexo=['F'];
    }
    else {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe o Sexo.');
        $(".alert_erro_animal").show();
        return;
    }

    var raca = $("#codigo_raca_entrada").val();

    if (raca==0 && controle_estoque=='I'){
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Raça.');
        $(".alert_erro_animal").show();
        return;
    }
    else {
        select = document.getElementById('codigo_raca_entrada');
        desc_raca = select.options[select.selectedIndex].text;
    }

    if (controle_estoque=='I') {
        var pelagem = $("#codigo_pelagem_entrada").val();
        select = document.getElementById('codigo_pelagem_entrada');
        desc_pelagem = select.options[select.selectedIndex].text;
    }
    else {
        var pelagem = 0;
        desc_pelagem = '';
    }

    if (qtd_cat_entrada=='') {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Quantidade da Categoria.');
        $(".alert_erro_animal").show();
        return;
    }

    if ((qtd_total_digitado + qtd_cat_entrada - qtd_cat_anterior)>qtd_total_animais) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Quantida de animais digitados será maior que a quantidade total de animais!');
        $(".alert_erro_animal").show();
        return;
    }

    if (peso_entrada=='' || peso_entrada==0) {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe o Peso Médio da Categoria.');
        $(".alert_erro_animal").show();
        return;
    }

    if (sequencia_entrada=='' && controle_estoque=='I') {
        $(".alert_erro_animal .negrito").html('');
        $(".alert_erro_animal span").html('Informe a Sequência Numérica Inicial.');
        $(".alert_erro_animal").show();
        return;
    }

    $('#tabela_itens_digitados_entrada tbody tr').each(function(){
        row_index_salvar = $(this).index();

        if (row_index_salvar==row_index){
            $(this).find('.desc_categoria').html(desc_categoria);
            $(this).find('.idade_entrada').html($("#idade_entrada").val());
            $(this).find('.sexo_entrada').html(sexo);
            $(this).find('.desc_raca').html(desc_raca);
            $(this).find('.desc_pelagem').html(desc_pelagem);
            $(this).find('.qtd_cat_entrada').html($("#qtd_cat_entrada").val());
            $(this).find('.sequencia_numeria_entrada').html($("#sequencia_numeria_entrada").val());
            $(this).find('.marcacao_alfa_entrada').html($("#marcacao_alfa_entrada").val());
            $(this).find('.codigo_categoria_entrada').html($("#codigo_categoria_entrada").val());
            $(this).find('.codigo_raca_entrada').html($("#codigo_raca_entrada").val());
            $(this).find('.codigo_pelagem_entrada').html($("#codigo_pelagem_entrada").val());
            if (controle_estoque=='I') {
                $(this).find('.peso_entrada').html($("#peso_entrada").val());
            }
            else {
                $(this).find('.peso_entrada').html($("#peso_medio").val());
            }
        }
    });

    var total_digitados= 0;

    $('#tabela_itens_digitados_entrada tbody tr').each(function(){
        var qtd_cat_entrada = $(this).find('.qtd_cat_entrada').html();

        if (qtd_cat_entrada!=''){
            total_digitados+= Number(qtd_cat_entrada);
        }
    });

    $(".total_digitados_entrada").text('Animais Digitados: ' + total_digitados);
    $(".total_digitados_entrada").val(total_digitados);
    $("#qtd_total_digitado").val(total_digitados);

    $(".total_a_digitar_entrada").text('Quantidade Total de Animais: ' + qtd_total_animais);
    $(".total_a_digitar_entrada").val(qtd_total_animais);

    var qtd_total_restante = qtd_total_animais - total_digitados;
    $(".total_restante_entrada").text('Faltam Digitar: ' + qtd_total_restante);
    $("#qtd_total_restante").val(qtd_total_restante);

    $("#codigo_categoria_entrada").val('000');
    $("#idade_entrada").val('');
    $("#macho_entrada").prop("checked", false);
    $("#femea_entrada").prop("checked", false);
    $("#codigo_raca_entrada").val('000');
    $("#codigo_pelagem_entrada").val('000');
    $("#qtd_cat_entrada").val('');
    $("#sequencia_numeria_entrada").val('');
    $("#marcacao_alfa_entrada").val('');
    $("#peso_entrada").val('');
    $("#peso_medio").val('');
    $('#modal_entrada_rapida').modal('hide');
    $(".alert_erro_animal .negrito").html('');
    $(".alert_erro_animal span").html('');
    $(".alert_erro_animal").hide();
    $("#editar_entrada").hide();
    $("#incluir_entrada").show();

    if (controle_estoque=='I') {
        somar_sequencia_numerica();
    }
};

function modal_editar_item() {
    var controle_estoque = $("#controle_estoque").val();
    row_index = $(this).parent().parent().index();

    if (controle_estoque=='I') {
        var par = $(this).parent().parent(); //tr
        var tdCodigo = par.children("td:nth-child(1)");
        var tdPeso = par.children("td:nth-child(2)");
        var tdSexo = par.children("td:nth-child(4)");
        var tdNascimento = par.children("td:nth-child(5)");
        var tdRaca = par.children("td:nth-child(6)");
        var tdPelagem = par.children("td:nth-child(7)");
        var tdMae = par.children("td:nth-child(8)");
        var tdObservacao = par.children("td:nth-child(9)");
        var tdCodigo_id = par.children("td:nth-child(10)");
        var tdCategoria = par.children("td:nth-child(11)");

        $("#id_animal").val(tdCodigo.html());
        $("#sexo_animal").val(tdSexo.html());
        $("#nascimento_animal").val(tdNascimento.html());
        $("#categoria_animal").val(tdCategoria.html());
        $("#raca_animal").val(tdRaca.html());
        $("#pelagem_animal").val(tdPelagem.html());
        $("#mae_animal").val(tdMae.html());
        $("#observacao").val(tdObservacao.html());
        $("#codigo_id").val(tdCodigo_id.html());
    }
    else {
        var par = $(this).parent().parent(); //tr
        var tdQtdAnimal = par.children("td:nth-child(2)");
        var tdObservacao = par.children("td:nth-child(3)");
        var tdCategoria = par.children("td:nth-child(4)");
        var tdSexo = par.children("td:nth-child(5)");
        var tdQtdLote = par.children("td:nth-child(6)");

        var codigo_categoria = tdSexo.html()+tdCategoria.html()+tdQtdLote.html();

        $("#codigo_categoria_individual").val(codigo_categoria);
        $("#qtd_cat_individual").val(tdQtdAnimal.html());
        $("#observacao").val(tdObservacao.html());
        $("#sexo_lote").val(tdSexo.html());
        $("#categoria_lote").val(tdCategoria.html());
        $("#qtd_lote").val(tdQtdLote.html());
        $("#qtd_digitado_anterior").val(tdQtdAnimal.html());
    }

    $('#modal_individual .modal-title').html('Movimentação - Individual - Editar');
    $('#modal_individual').modal('show');
    $(".alert_erro_animal .negrito").html('');
    $(".alert_erro_animal span").html('');
    $(".alert_erro_animal").hide();

    $("#editar").show();
    $("#incluir").hide();
}

function modal_editar_entrada() {
    row_index = $(this).parent().parent().index();
    var par = $(this).parent().parent(); //tr
    var tdcategoria = par.children("td:nth-child(9)");
    var tdidade = par.children("td:nth-child(2)");
    var tdSexo = par.children("td:nth-child(3)");
    var tdRaca = par.children("td:nth-child(10)");
    var tdPelagem = par.children("td:nth-child(11)");
    var tdQtd_entrada = par.children("td:nth-child(6)");
    var tdSeqNumerica = par.children("td:nth-child(7)");
    var tdAlfa = par.children("td:nth-child(8)");
    var tdPeso = par.children("td:nth-child(12)");

    $("#codigo_categoria_entrada").val(tdcategoria.html());
    $("#idade_entrada").val(tdidade.html());
    if (tdSexo.html()=='M') {
        $("#macho_entrada").prop("checked", true);
    }
    else {
        $("#femea_entrada").prop("checked", true);
    }

    $("#codigo_raca_entrada").val(tdRaca.html());
    $("#codigo_pelagem_entrada").val(tdPelagem.html());
    $("#qtd_cat_entrada").val(tdQtd_entrada.html());
    $("#qtd_cat_anterior").val(tdQtd_entrada.html());
    $("#sequencia_numeria_entrada").val(tdSeqNumerica.html());
    $("#marcacao_alfa_entrada").val(tdAlfa.html());
    $("#peso_entrada").val(tdPeso.html());

    $('#modal_entrada_rapida .modal-title').html('Movimentação - Entrada Rápida de Animais ao Cadastro - Editar');
    $('#modal_entrada_rapida').modal('show');
    $(".alert_erro_animal .negrito").html('');
    $(".alert_erro_animal span").html('');
    $(".alert_erro_animal").hide();

    $("#editar_entrada").show();
    $("#incluir_entrada").hide();
}


function excluir(){
    var controle_estoque = $("#controle_estoque").val();
    row_index = $(this).parent().parent().index();
    var par = $(this).parent().parent(); //tr
    var tdCodigo = par.children("td:nth-child(1)");
    var tdCodigo_id = par.children("td:nth-child(10)");

    if (window.confirm("Confirma remover esse registro da lista?" + " " + tdCodigo.html())) {     
        par.remove();

        var qtd_digitar = $("#qtd_a_digitar").val();
        var qtd_digitado = 0;

        $('#tabela_itens_digitados tbody tr').each(function(){
            if (controle_estoque=='I') {
                var id_lista = $(this).find('.id_animal').html();

                if (id_lista!=undefined){
                    qtd_digitado++;
                }
            }   
            else {
                var id_lista = $(this).find('.id_categoria').html();
                var qtd = $(this).find('.qtd_animal').html();

                if (id_lista!=undefined){
                    qtd_digitado+=parseInt(qtd);
                }
            }
        });

        if (qtd_digitado>qtd_digitar && qtd_digitar!=0) {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('Quantida de animais digitados está maior que a quantidade informada!');
            document.getElementById("qtd_digitado").style.borderColor = "red";
            $(".alert_erro_animal").show();
        }
        else {
            $(".alert_erro_animal .negrito").html('');
            $(".alert_erro_animal span").html('');
            document.getElementById("qtd_digitado").style.borderColor = "#ccc";
            $(".alert_erro_animal").hide();

        }

        $("#qtd_digitado").val(qtd_digitado);

        somar_totais(); 
    }
};


function excluir_entrada(){
    row_index = $(this).parent().parent().index();
    var par = $(this).parent().parent(); //tr
    var tddesc_categoria = par.children("td:nth-child(1)");
    var tdQtd_entrada = par.children("td:nth-child(6)");

    if (window.confirm("Confirma remover esse registro da lista?" + " " + tddesc_categoria.html() + ' Qtde: ' + tdQtd_entrada.html())) {     
        par.remove();

        var total_digitados= 0;

        $('#tabela_itens_digitados_entrada tbody tr').each(function(){
            var qtd_cat_entrada = $(this).find('.qtd_cat_entrada').html();

            if (qtd_cat_entrada!=''){
                total_digitados+= Number(qtd_cat_entrada);
            }
        });

        var qtd_total_animais = $("#qtd_total_animais").val();

        $(".total_digitados_entrada").text('Animais Digitados: ' + total_digitados);
        $(".total_digitados_entrada").val(total_digitados);
        $("#qtd_total_digitado").val(total_digitados);

        $(".total_a_digitar_entrada").text('Quantidade Total de Animais: ' + qtd_total_animais);
        $(".total_a_digitar_entrada").val(qtd_total_animais);

        var qtd_total_restante = qtd_total_animais - total_digitados;
        $(".total_restante_entrada").text('Faltam Digitar: ' + qtd_total_restante);
        $("#qtd_total_restante").val(qtd_total_restante);

        somar_sequencia_numerica();
    }
};

function excluir_edicao(){
    row_index = $(this).parent().parent().index();

    var par = $(this).parent().parent(); //tr
    var tdCodigo = par.children("td:nth-child(1)");

    //if (window.confirm("Confirma excluir esse registro " + tdCodigo.html() + "?")) { 
        var tdPeso = par.children("td:nth-child(2)");
        var tdSexo = par.children("td:nth-child(3)");
        var tdNascimento = par.children("td:nth-child(4)");
        var tdRaca = par.children("td:nth-child(5)");
        var tdPelagem = par.children("td:nth-child(6)");
        var tdMae = par.children("td:nth-child(7)");
        var tdObservacao = par.children("td:nth-child(8)");
        var tdCodigo_id = par.children("td:nth-child(9)");
        var tdCategoria = par.children("td:nth-child(10)");

        $('#tabela_itens_digitados tbody tr').each(function(){
            row_index_salvar = $(this).index();

            if (row_index_salvar==row_index){
                $(this).find('.id_animal').html(tdCodigo.html());
                $(this).find('.peso_animal').html(tdPeso.html());
                $(this).find('.sexo_animal').html(tdSexo.html());
                $(this).find('.nascimento_animal').html(tdNascimento.html());
                $(this).find('.categoria_animal').html(tdCategoria.html());
                $(this).find('.raca_animal').html(tdRaca.html());
                $(this).find('.pelagem_animal').html(tdPelagem.html());
                $(this).find('.mae_animal').html(tdMae.html());
                $(this).find('.observacao').html(tdObservacao.html());
                $(this).find('.codigo_id').html(tdCodigo_id.html());
                $(this).find('.excluir').html('S');
                $(this).find('.botoes').html("<div class='btn-group btnexcluir'><a class='btn' href='#'><i class='icon_refresh' title='Estornar exclusão'></i></a></div>");
                $(".btnexcluir").bind("click", estornar_edicao);
                return;
            }
        });
        somar_totais();    
    //}    
}

function estornar_edicao(){
    row_index = $(this).parent().parent().index();

    var par = $(this).parent().parent(); //tr
    var tdCodigo = par.children("td:nth-child(1)");

        var tdPeso = par.children("td:nth-child(2)");
        var tdSexo = par.children("td:nth-child(4)");
        var tdNascimento = par.children("td:nth-child(5)");
        var tdRaca = par.children("td:nth-child(6)");
        var tdPelagem = par.children("td:nth-child(7)");
        var tdMae = par.children("td:nth-child(8)");
        var tdObservacao = par.children("td:nth-child(9)");
        var tdCodigo_id = par.children("td:nth-child(10)");
        var tdCategoria = par.children("td:nth-child(11)");

        $('#tabela_itens_digitados tbody tr').each(function(){
            row_index_salvar = $(this).index();

            if (row_index_salvar==row_index){
                $(this).find('.id_animal').html(tdCodigo.html());
                $(this).find('.peso_animal').html(tdPeso.html());
                $(this).find('.sexo_animal').html(tdSexo.html());
                $(this).find('.nascimento_animal').html(tdNascimento.html());
                $(this).find('.categoria_animal').html(tdCategoria.html());
                $(this).find('.raca_animal').html(tdRaca.html());
                $(this).find('.pelagem_animal').html(tdPelagem.html());
                $(this).find('.mae_animal').html(tdMae.html());
                $(this).find('.observacao').html(tdObservacao.html());
                $(this).find('.codigo_id').html(tdCodigo_id.html());
                $(this).find('.excluir').html('N');
                $(this).find('.botoes').html("<div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnexcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='excluir'></i></a></div>");
                $(".btnEditar").bind("click", modal_editar_item);
                $(".btnexcluir").bind("click", excluir_edicao);
                return;
            }
        });

        somar_totais();    
}

function excluir_movimentacao(id_excluir){
    $('#id_excluir').val(id_excluir);

    $("#confirma_excluir").modal();
    $("#confirma_excluir .modal-body").html('Confirma estornar esse registro?');
}

function confirmar_excluir_movimentacao(){
    var id_excluir = $('#id_excluir').val();
    
    $.ajax({
        type: "POST", 
        url: 'excluir_movimentacao_animais.php',
        data: {
                id_excluir: id_excluir
            },
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $('#id_pasto').val(data.id_pasto);
                $('#descricao_pasto').val(data.descricao_pasto);
                $('#descricao_lote').val(data.descricao_lote);

                if (data.descricao_lote=='') {
                    $("#mensagem_descricao_lote").modal();
                    $("#mensagem_descricao_lote .modal-body").html(data.message + '<br>Necessário incluir a Descrição do Lote para o Pasto: ' + data.descricao_pasto);
                }
                else {
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }
        }
    });
}

// Funcões para a montagem da descrição dos lotes de animais no pasto
function abrir_modal_descricao_lote() {
    var id_pasto = $('#id_pasto').val();
    var desc_pasto = $("#descricao_pasto").val();
    $(".desc_pasto").html(desc_pasto);

    $(".monta_descricao_lote").show();
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
    $(".exibir_opcoes").hide();
    $("#descricao_novo_lote2").val('');
    $(".exibir_opcoes2").hide();
    $("#descricao_novo_lote3").val('');
    $(".exibir_opcoes3").hide();
    $("#descricao_novo_lote4").val('');
    $(".exibir_opcoes4").hide();
    $("#descricao_novo_lote5").val('');
    $(".exibir_opcoes5").hide();
    $("#descricao_novo_lote6").val('');
    $(".exibir_opcoes6").hide();

    $(".descricao_principal").show();
    numero_item=1;
    $("#numero_item").val(numero_item);
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

            $('#situacao_principal').append('<option value="0">' + 'Selecione' + '</option>');
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

            $('#situacao_principal').append('<option value="0">' + 'Selecione' + '</option>');
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

            $('#situacao_principal').append('<option value="0">' + 'Selecione' + '</option>');
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

            $('#situacao_principal').append('<option value="0">' + 'Selecione' + '</option>');
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

            $('#situacao_principal').append('<option value="0">' + 'Selecione' + '</option>');
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

function exibe_descricao_lote() {
    var descricao_lote = montar_descricao_lote();
    var numero_item = $("#numero_item").val();;

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

    if (descricao_id == 0) {
        $("#mensagem_erro_descricao_lote").modal(); 
        $("#mensagem_erro_descricao_lote .modal-body").html('Selecione a Descrição do Lote');
        return;
    }

    if (parametro_2 == 0 && (descricao_id==1 || 
        descricao_id==2 || descricao_id==7 || descricao_id==8)) {
        $("#mensagem_erro_descricao_lote").modal(); 
        $("#mensagem_erro_descricao_lote .modal-body").html('Selecione a Situação.');
        return;
    }

    if (parametro_2 == 0 && descricao_id==3) {
        $("#mensagem_erro_descricao_lote").modal(); 
        $("#mensagem_erro_descricao_lote .modal-body").html('Selecione o Sexo.');
        return;
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

function confirma_composicao_descricao_lote() {
    var descricao_id = $("#descricao_principal").val();
    var parametro_2 = $("#situacao_principal").val();
    var itens = $("#numero_item").val();

    /*if (itens<6) {
        if (descricao_id == 0) {
            $("#mensagem_erro_descricao_lote").modal(); 
            $("#mensagem_erro_descricao_lote .modal-body").html('Selecione a Descrição do Lote');
            return;
        }

        if (parametro_2 == 0 && (descricao_id==1 || 
            descricao_id==2 || descricao_id==7 || descricao_id==8)) {
            $("#mensagem_erro_descricao_lote").modal(); 
            $("#mensagem_erro_descricao_lote .modal-body").html('Selecione a Situação.');
            return;
        }

        if (parametro_2 == 0 && descricao_id==3) {
            $("#mensagem_erro_descricao_lote").modal(); 
            $("#mensagem_erro_descricao_lote .modal-body").html('Selecione o Sexo.');
            return;
        }
    }*/

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
    $('#modal_composicao_descricao_lote').modal('hide');

    gravar_descricao_lote_digitacao();
}

// Grava a Descrição do Lote quando for digitado 
// Chamada quando clicar na descricao do lote ou Criar novo lote na transferencia
function gravar_descricao_lote_digitacao() {
    var pasto_origem = $("#id_pasto").val();
    var id_lote = 0;
    var ano_lote = 0;
    var novo_id = 'S';

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
                    $("#mensagem_retorno").modal(); 
                    $("#mensagem_retorno .modal-body").html(data.message);
                    return;
                }
            }
    });
}

$(document).ready(function(){
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

})

// FIM Funcões para a montagem da descrição dos lotes de animais no pasto

function iniciar_movimentacao(){
    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='I') {
        html = "";
        html += '<table class="table table-striped table-advance table-hover" id="tabela_itens_digitados" width="100%">';
        html += '<thead>';
        html += '<tr>';
        html += '<th>' + ' Id' + '</th>';
        html += '<th>' + ' Peso' + '</th>';
        html += '<th>' + ' Categoria' + '</th>';
        html += '<th>' + ' Sexo' + '</th>';
        html += '<th>' + ' Nascimento' + '</th>';
        html += '<th>' + ' Raça' + '</th>';
        html += '<th>' + ' Pelagem' + '</th>';
        html += '<th>' + ' Mãe' + '</th>';
        html += '<th>' + ' Observação' + '</th>';
        html += '<th  hidden="">' + ' Id Animal' + '</th>';
        html += '<th>' + ' <i class="icon_cogs"></i> Ações' + '</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        html += '</tbody>';
        html += '</table>';
        document.getElementById('tabela_itens_digitados').innerHTML = html;
    }
    else {
        html = "";
        html += '<table class="table table-striped table-advance table-hover" id="tabela_itens_digitados" width="100%">';
        html += '<thead>';
        html += '<tr>';
        html += '<th>' + ' Categoria' + '</th>';
        html += '<th>' + ' Qtde' + '</th>';
        html += '<th>' + ' Observação' + '</th>';
        html += '<th  hidden="">' + ' Id Categoria' + '</th>';
        html += '<th  hidden="">' + ' Sexo' + '</th>';
        html += '<th  hidden="">' + ' Qtd Lote' + '</th>';
        html += '<th>' + ' <i class="icon_cogs"></i> Ações' + '</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        html += '</tbody>';
        html += '</table>';
        document.getElementById('tabela_itens_digitados').innerHTML = html;
    }

    var local_origem = $("#local_origem").val();
    var local_destino = $("#local_destino").val();

    if (local_origem==0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Local Origem');
        return;
    }

    if (local_destino==0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Local Destino');
        return;
    }

    $("#qtd_a_digitar").val('');
    $("#qtd_digitado").val('');
    $("#id_animal").val('');
    $("#observacao").val('');
    $("#codigo_categoria_individual").val('000');
    $("#qtd_cat_individual").val('');
    $("#descricao_animal").text('');
    $(".alert_erro_animal .negrito").html('');
    $(".alert_erro_animal span").html('');
    $(".alert_erro_animal").hide();

    if (controle_estoque=='I') {
        $(".digitacao_lote_individual").hide();
        $(".id_animal").show();
    }
    else {
        $(".digitacao_lote_individual").show();
        $(".id_animal").hide();
    }

    $('#modal_individual').modal('show');
}

function continuar_digitacao(){
    $("#id_animal").val('');
    $("#peso_animal").val('');
    $("#observacao").val('');
    $("#descricao_animal").text('');

    $('#modal_individual .modal-title').html('Movimentação - Individual - Incluir');
    $('#modal_individual').modal('show');
    document.getElementById("id_animal").focus();
}

function continuar_digitacao_entrada(){
    $("#codigo_categoria_entrada").val('000');
    $("#idade_entrada").val('');
    $("#macho_entrada").prop("checked", false);
    $("#femea_entrada").prop("checked", false);
    $("#codigo_raca_entrada").val('000');
    $("#codigo_pelagem_entrada").val('000');
    $("#qtd_cat_entrada").val('');
    $("#sequencia_numeria_entrada").val('');
    $("#marcacao_alfa_entrada").val('');
    $("#editar_entrada").hide();
    $("#incluir_entrada").show();

    $('#modal_entrada_rapida').modal('show');
    document.getElementById("codigo_categoria_entrada").focus();
}

function continuar_digitacao_morte(){
    $("#id_animal_morte").val('');
    $("#observacao_morte").val('');
    $("#descricao_animal_morte").text('');

    $('#modal_morte .modal-title').html('Movimentação - Morte');
    $('#modal_morte').modal('show');
    document.getElementById("id_animal_morte").focus();
}

function continuar_digitacao_outra(){
    $("#id_animal_outra").val('');
    $("#observacao_outra").val('');
    $("#descricao_animal_outra").text('');

    $('#modal_outra .modal-title').html('Movimentação - Outras saídas');
    $('#modal_outra').modal('show');
    document.getElementById("id_animal_outra").focus();
}

function pausar_digitacao(){
    tem_itens = 'N';

    $('#tabela_itens_digitados tbody tr').each(function(){
        var id_lista = $(this).find('.id_animal').html();

        if (id_lista!=''){
            tem_itens = 'S';
        }
    });

    if (tem_itens=='N'){
        $("#itens_digitados").hide();
        $("#dados_consulta").show();
    }

    somar_totais();
    $('#modal_individual').modal('hide');
}

function pausar_digitacao_entrada(){
    tem_itens = 'N';

    $('#tabela_itens_digitados_entrada tbody tr').each(function(){
        var qtd_cat_entrada = $(this).find('.qtd_cat_entrada').html();

        if (qtd_cat_entrada!=''){
            tem_itens = 'S';
        }
    });

    if (tem_itens=='N'){
        $("#itens_digitados_entrada").hide();
        $("#dados_consulta").show();
        //$("#entrada_sim").prop("checked", false);
        //$("#entrada_nao").prop("checked", false);
    }

    $('#modal_entrada_rapida').modal('hide');
}

function pausar_digitacao_morte(){
    tem_itens = 'N';

    $('#tabela_itens tbody tr').each(function(){
        var id_lista = $(this).find('.id_animal').html();

        if (id_lista!=''){
            tem_itens = 'S';
        }
    });

    if (tem_itens=='N'){
        $("#compra").prop("checked", false);
        $("#transferencia").prop("checked", false);
        $("#venda").prop("checked", false);
        $("#outras").prop("checked", false);
        $("select[name=local_origem]").html('');
        $("select[name=local_destino]").html('');
    }

    $('#modal_morte').modal('hide');
}

/*function finalizar_digitacao(){
    var controle_estoque = $("#controle_estoque").val();
    var tem_item="";        

    $('#tabela_itens_digitados tbody tr').each(function(){
        if (controle_estoque=='I') {
            var codigo = $(this).find('.id_animal').html();
            if (codigo!=undefined){
                tem_item="S";
            }
        }
        else {
            var categoria = $(this).find('.id_categoria').html();
            if (categoria!=undefined || categoria!=''){
                tem_item="S";
            }
        }
    });

    var a_digitar = $('#qtd_a_digitar').val();
    var digitados = $('#qtd_digitado').val();

    if (tem_item=="") {
        if (window.confirm("Atenção! Não existem animais digitados. Finalizar assim mesmo?")) {
            //$("#mensagem_sair_retorno").modal();
            //$("#mensagem_sair_retorno .modal-body").html('Movimentação finalizada sem animais pesados.');
            gravar_movimentacao_digitada();
        } 
    }
    else if (a_digitar>digitados){
        if (window.confirm("Animais digitados menor que animais a digitar. Finalizar assim mesmo?")) {     
            //$("#mensagem_sair_retorno").modal();
            //$("#mensagem_sair_retorno .modal-body").html('Movimentação finalizada com sucesso.');
            gravar_movimentacao_digitada();
        }
    } else {
        if (window.confirm("Confirma finalizar a movimentação?")) {
            //$("#mensagem_sair_retorno").modal();
            //$("#mensagem_sair_retorno .modal-body").html('Movimentação finalizada com sucesso.');
            gravar_movimentacao_digitada();
        } 
    }
}
*/

function finalizar_selecao_venda_transferencia() {

    var table = $('#tabela_itens_digitados').DataTable();
    table.search('').draw();

    var total_listados = $('.total_a_digitar').val();
    var total_selecionados = $('.total_digitados').val();
    var tem_item = '';
    var aChk = document.getElementsByName("id_animal_selecao");
        
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            tem_item = 'S';
        }
    }

    if (tem_item=="") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Não exitem animais selecionados.');
    } else {
        $("#modal_gravar_venda_transf").modal();
        $("#modal_gravar_venda_transf .modal-body").html('Confirma Gravar a Movimentação?');
    }
}

function finalizar_digitacao_entrada(){
    var tem_item="";        

    $('#tabela_itens_digitados_entrada tbody tr').each(function(){
        var qtd_cat_entrada = $(this).find('.qtd_cat_entrada').html();

        if (qtd_cat_entrada!=''){
            tem_item="S";;
        }
    });

    var a_digitar = parseInt($('.total_a_digitar_entrada').val());
    var digitados = parseInt($('.total_digitados_entrada').val());

    if (tem_item=="") {
        if (window.confirm("Atenção! Não existem itens digitados. Finalizar assim mesmo?")) {
            $("#itens_digitados_entrada").hide();
            $("#dados_consulta").show();
            //$("#entrada_sim").prop("checked", false);
            //$("#entrada_nao").prop("checked", false);
        } 
    }
    else if (a_digitar>digitados){
        if (window.confirm("Animais digitados menor que quantidade total de animais. Finalizar assim mesmo?")) {     
            gravar_movimentacao_digitada_entrada();
        }
    } else {
        if (window.confirm("Confirma finalizar a movimentação de compra?")) {
            gravar_movimentacao_digitada_entrada();
        } 
    }
}

function finalizar_digitacao_morte(){
    var tem_item="";        

    $('#tabela_itens tbody tr').each(function(){
        var codigo = $(this).find('.id_animal').html();
        if (codigo!=undefined){
            tem_item="S";
        }
    });

    if (tem_item=="") {
        if (window.confirm("Atenção! Não existem animais digitados. Finalizar assim mesmo?")) {
            $("#mensagem_sair_retorno").modal();
            $("#mensagem_sair_retorno .modal-body").html('Digitação finalizada sem animais.');
        } 
    } else {
        if (window.confirm("Confirma finalizar a digitação?")) {
            $("#mensagem_sair_retorno").modal();
            $("#mensagem_sair_retorno .modal-body").html('Digitação finalizada com sucesso.');
        } 
    }
}

function finalizar_movimentacao_editar(){
    var tem_item="N";        

    $('#tabela_itens_digitados tbody tr').each(function(){
        var excluir = $(this).find('.excluir').html();
        if (excluir=='N'){
            tem_item="S";
        }
    });

    if (tem_item=="N") {
        if (window.confirm("Atenção! Não existem animais digitados, a movimentação será excluida. Finalizar assim mesmo?")) {
            gravar_movimentacao_digitada_editar();
            //location.href= "form_movimentacao_animais.php";
        } 
    } else {
        if (window.confirm("Confirma finalizar a movimentação?")) {
            gravar_movimentacao_digitada_editar();
            //location.href= "form_movimentacao_animais.php";
        } 
    }
}

function finalizar_sair(){
    location.href= "form_movimentacao_animais.php";
}

function fecha_consultar_pesagem(){
    location.href= "form_movimentacao_animais.php";
}

function somar_sequencia_numerica(){
    var numeracao_sequencial = 0;

    $('#tabela_itens_digitados_entrada tbody tr').each(function(){
        numeracao_sequencial = $(this).find('.sequencia_numeria_entrada').html();
        qtd_categoria = $(this).find('.qtd_cat_entrada').html();

        if (numeracao_sequencial!=''){
            numeracao_sequencial = Number(numeracao_sequencial);
            qtd_categoria = Number(qtd_categoria);
        }
    });

    var numeracao_sugerida = numeracao_sequencial + qtd_categoria;
    $("#sequenciaHelpBlock").text('Próxima sequencia sugerida: ' + numeracao_sugerida);
    $("#sequenciaHelpBlock").show();
}

/*function somar_selecionado_transferencia() {
    var total = $(".total_digitados").val();    

    if (total=='' || total==0) {
        total_selecionados = 0;
    }

    alert (total_selecionados);

    var aChk = document.getElementsByName("id_animal_selecao");
       
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            total_selecionados++;
            alert (total_selecionados);
        }
    }

    $(".total_digitados").text('Animais Selecionados: ' + total_selecionados);
    $(".total_digitados").val(total_selecionados);
}
*/

function somar_totais(){
    var controle_estoque = $("#controle_estoque").val();
    var total_digitados= 0;
    var tem_itens = 'N';

    $('#tabela_itens_digitados tbody tr').each(function(){
        if (controle_estoque=='I') {
            var id_lista = $(this).find('.id_animal').html();

            if (id_lista!=undefined){
                total_digitados++;
                tem_itens='S';
            }
        }
        else {
            var id_lista = $(this).find('.id_categoria').html();
            var qtd_digitado = $(this).find('.qtd_animal').html();

            if (id_lista!=undefined){
                total_digitados+=parseInt(qtd_digitado);
                tem_itens='S';
            }
        }
    });

    $(".total_digitados").text('Animais Digitados: ' + total_digitados);
    $(".total_digitados").val(total_digitados);

    var total_a_digitar = $("#qtd_a_digitar").val();
    $(".total_a_digitar").text('Animais para Digitar: ' + total_a_digitar);
    $(".total_a_digitar").val(total_a_digitar);

    var data = $("#data_movimentacao").val();

    var dia  = data.split("-")[2];
    var mes  = data.split("-")[1];
    var ano  = data.split("-")[0];

    var str_data = ("0"+dia).slice(-2) + '/' + ("0"+mes).slice(-2) + '/' + ano;    

    $("#data_digitados").text('Data: ' + str_data);
}

function somar_totais_morte(){
    qtd_pesados= 0;
    tem_itens = 'N';

    $('#tabela_itens tbody tr').each(function(){
        var id_lista = $(this).find('.id_animal').html();

        if (id_lista!=undefined){
            qtd_pesados++;
            tem_itens='S';
        }
    });

    if (tem_itens=="S") {
        $(".total_pesados").text('Qtde Animais: ' + qtd_pesados);
        $(".total_pesados").val(qtd_pesados);
    }
    else {
        var qtd_pesados = '';
        $(".total_pesados").text('');
        $(".total_pesados").val('');
    }
}

function soma_total_item_lote() {
    $(".alert_erro_animal .negrito").html('');
    $(".alert_erro_animal span").html('');
    $(".alert_erro_animal").hide();

    var tem_itens = 'N';
    var qtd_no_pasto = $('#qtd_lote').val();   
    var qtd_no_pasto_calculada = $('#qtd_lote').val();   
    var qtd_cat_individual = parseInt($("#qtd_cat_individual").val());
    var qtd_digitada_anterior = $("#qtd_digitado_anterior").val();

    if (qtd_digitada_anterior==''){
        qtd_digitada_anterior=0
    }

    var categoria_digitada = $("#categoria_lote").val();
    var sexo_digitado = $("#sexo_lote").val();

    $('#tabela_itens_digitados tbody tr').each(function(){
        var categoria = $(this).find('.id_categoria').html();
        var sexo = $(this).find('.sexo_animal').html();
        var qtd_ja_digitada = $(this).find('.qtd_animal').html();

        if (categoria!=undefined){
            if (categoria==categoria_digitada && sexo==sexo_digitado) {
                qtd_cat_individual-=parseInt(qtd_digitada_anterior);
                qtd_cat_individual+=parseInt(qtd_ja_digitada);
                qtd_no_pasto_calculada-=parseInt(qtd_ja_digitada);
            }
        }
    });

    if (qtd_cat_individual>qtd_no_pasto) {
        $(".alert_erro_animal span").html('Quantidade digitada esta maior que a quantidade de animais no pasto. Quantide no pasto: ' + qtd_no_pasto_calculada);
        $(".alert_erro_animal").show();
        $("#qtd_cat_individual").val('');
        return;
    }
}

function editar_animal(array_animal) {
    array_produtos = array_animal.split('|');
    $("#codigo_animal").val(array_produtos[0]);
    $("#number_animal").val(array_produtos[1]);
    if (array_produtos[2]=='F') {
        $("#F").prop("checked", true);
    }
    else {
        $("#M").prop("checked", true);
    }
    $("#raca_id").val(array_produtos[3]);
    $("#pelagem_id").val(array_produtos[4]);
    $("#nascimento_animal").val(array_produtos[5]);
    $("#grau_sangue_animal").val(array_produtos[6]);
    $("#local_id").val(array_produtos[7]);
    $("#origem_id").val(array_produtos[8]);
    $("#number_pai_animal").val(array_produtos[9]);
    $("#nome_pai_animal").val(array_produtos[10]);
    $("#number_mae_animal").val(array_produtos[11]);
    $("#nome_mae_animal").val(array_produtos[12]);
    $("#primeiro_peso_animal").val(formatMoney2(array_produtos[13]));
    $("#peso_desmama_animal").val(formatMoney2(array_produtos[14]));
    $("#ultimo_peso_animal").val(formatMoney2(array_produtos[15]));
    $("#nome_registro_animal").val(array_produtos[16]);
    $("#ren_animal").val(array_produtos[17]);
    $("#rgd_animal").val(array_produtos[18]);
    $("#sisbov_animal").val(array_produtos[19]);
    $("#certificadora_animal").val(array_produtos[20]);
    $("#observacao_animal").val(array_produtos[21]);
    if (array_produtos[22]=='S') {
        $("#S").prop("checked", true);
        $('.confirma_gravar').show();
    }
    else {
        $("#N").prop("checked", true);
        $('.confirma_gravar').hide();
    }

    $("#alfa_animal").val(array_produtos[23]);
    $("#categoria_id").val(array_produtos[24]);
    $("#idade_animal").val(array_produtos[25]);

    $("#incluido_em").text(array_produtos[26]);
    $("#incluido_por").text(array_produtos[27]);
    $("#alterado_em").text(array_produtos[28]);
    $("#alterado_por").text(array_produtos[29]);
    $("#baixado_em").text(array_produtos[30]);
    $("#baixado_por").text(array_produtos[31]);

    $("#tipo_gravacao").val(1);

    $('#modal_incluir .modal-title').html('Animal - Editar');
    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');
    $('.ativo').show();
    $("#informacao").show();    

    if (array_produtos[29]=='') {
        $("#registro_alterado").hide();
        $("#alterado_por").hide();
        $("#alterado_em").hide();
    }
    else {
        $("#registro_alterado").show();        
        $("#alterado_por").show();
        $("#alterado_em").show();
    }

    if (array_produtos[31]=='') {
        $("#registro_baixado").hide();
        $("#baixado_por").hide();
        $("#baixado_em").hide();
    }
    else {
        $("#registro_baixado").show();        
        $("#baixado_por").show();
        $("#baixado_em").show();
    }

    $('#modal_incluir').modal('show');

}

function enviar_lixeira(array_animal, opcao) {
    array_produtos = array_animal.split('|');
    $("#codigo_animal").val(array_produtos[0]);
    $("#number_animal").val(array_produtos[1]);
    if (array_produtos[2]=='F') {
        $("#F").prop("checked", true);
    }
    else {
        $("#M").prop("checked", true);
    }
    $("#raca_id").val(array_produtos[3]);
    $("#pelagem_id").val(array_produtos[4]);
    $("#nascimento_animal").val(array_produtos[5]);
    $("#grau_sangue_animal").val(array_produtos[6]);
    $("#local_id").val(array_produtos[7]);
    $("#origem_id").val(array_produtos[8]);
    $("#number_pai_animal").val(array_produtos[9]);
    $("#nome_pai_animal").val(array_produtos[10]);
    $("#number_mae_animal").val(array_produtos[11]);
    $("#nome_mae_animal").val(array_produtos[12]);
    $("#primeiro_peso_animal").val(formatMoney2(array_produtos[13]));
    $("#peso_desmama_animal").val(formatMoney2(array_produtos[14]));
    $("#ultimo_peso_animal").val(formatMoney2(array_produtos[15]));
    $("#nome_registro_animal").val(array_produtos[16]);
    $("#ren_animal").val(array_produtos[17]);
    $("#rgd_animal").val(array_produtos[18]);
    $("#sisbov_animal").val(array_produtos[19]);
    $("#certificadora_animal").val(array_produtos[20]);
    $("#observacao_animal").val(array_produtos[21]);
    if (array_produtos[22]=='S') {
        $("#S").prop("checked", true);
    }
    else {
        $("#N").prop("checked", true);
    }

    $("#alfa_animal").val(array_produtos[23]);
    $("#categoria_id").val(array_produtos[24]);
    $("#idade_animal").val(array_produtos[25]);

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
    $('.ativo').show();
    $('#modal_incluir').modal('show');
}

function gravar_item_editar_pesagem() {
    var array_tabela_itens = new Array();
    var valor = new Array();
    var grupo_itens = "";

    $('#tabela_itens tbody tr').each(function(){
        for (i = 0; i <= 3; i++) {
            valor[i]=0;
        }

        var peso = $(this).find('.peso').find("input").val();
        var obs = $(this).find('.obs').find("input").val();
        var item = $(this).find('.item').text();
        var codigo_id = $(this).find('.codigo_id').text();

        if (peso!=undefined && peso!=''){
            valor[0]=codigo_id;
            valor[1]=item;
            valor[2]=peso;
            valor[3]=obs;

            var tabela_itens=valor.join("|");
            array_tabela_itens.push(tabela_itens);
            grupo_itens=array_tabela_itens.join("<|>");
        }
    });

    $("#array_itens").val(grupo_itens);

    var dados = $('#form_gravar_pesagem').serialize();
    $.ajax({
        type: "POST",
        url: 'gravar_pesagem_item.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                //$("#mensagem_retorno").modal();
                //$("#mensagem_retorno .modal-body").html(data.message);
            }
        }
    });
}

function gravar_movimentacao_pesagem() {
    var controle_estoque = $("#controle_estoque").val();

    if (window.confirm("Confirma essa movimentação?")) {    
        var array_tabela_itens = new Array();
        var valor = new Array();
        var grupo_itens = "";
        var controle_estoque = $("#controle_estoque").val();

        if (controle_estoque=='I') {
            $('#tabela_itens tbody tr').each(function(){
                for (i = 0; i <= 12; i++) {
                    valor[i]=0;
                }

                var codigo = $(this).find('.id_animal').html();
                var peso = $(this).find('.peso_animal').html();
                var sexo = $(this).find('.sexo_animal').html();
                var nascimento = $(this).find('.nascimento_animal').html();
                var pelagem = $(this).find('.pelagem_animal').html();
                var raca = $(this).find('.raca_animal').html();
                var mae = $(this).find('.mae_animal').html();
                var observacao = $(this).find('.observacao').html();
                var codigo_id = $(this).find('.codigo_id').html();
                var codigo_categoria = $(this).find('.codigo_categoria').html();
                var qtde = $(this).find('.qtd_animal').html();

                if (verifica_virgula(peso)==',') {
                    peso = replace_valor(peso);
                }

                if (codigo!=undefined && codigo!=0){
                    valor[0]=codigo;
                    valor[1]=peso;
                    valor[2]=sexo;
                    valor[3]=nascimento;
                    valor[4]=raca;
                    valor[5]=pelagem;
                    valor[6]=mae;
                    valor[7]=observacao;
                    valor[8]=codigo_id;
                    valor[9]='';
                    valor[10]=0;
                    valor[11]=0;
                    valor[12]=codigo_categoria;
                    valor[13]='';

                    var tabela_itens=valor.join("|");
                    array_tabela_itens.push(tabela_itens);
                    grupo_itens=array_tabela_itens.join("<|>");
                }
            });
        }
        else {
            $('#tabela_itens tbody tr').each(function(){
                for (i = 0; i <= 12; i++) {
                    valor[i]=0;
                }

                var item = $(this).find('.item_animal').html();
                var peso = $(this).find('.peso_animal').html();
                var sexo = $(this).find('.sexo_animal').html();
                var qtde = $(this).find('.qtd_animal').html();
                var peso_medio = $(this).find('.peso_medio').html();
                var peso_arroba = $(this).find('.peso_arroba').html();
                var peso_medio_arroba = $(this).find('.peso_medio_arroba').html();
                var observacao = $(this).find('.observacao').html();
                var codigo_categoria = $(this).find('.codigo_categoria').html();

                if (verifica_virgula(peso)==',') {
                    peso = replace_valor(peso);
                }

                if (verifica_virgula(peso_medio)==',') {
                    peso_medio = replace_valor(peso_medio);
                }

                if (verifica_virgula(peso_arroba)==',') {
                    peso_arroba = replace_valor(peso_arroba);
                }

                if (verifica_virgula(peso_medio_arroba)==',') {
                    peso_medio_arroba = replace_valor(peso_medio_arroba);
                }

                if (codigo_categoria!=undefined && codigo_categoria!=0){
                    valor[0]=item;
                    valor[1]=peso;
                    valor[2]=sexo;
                    valor[3]=qtde;
                    valor[4]=peso_medio;
                    valor[5]=peso_arroba;
                    valor[6]=peso_medio_arroba;
                    valor[7]=observacao;
                    valor[8]=0;
                    valor[9]=0;
                    valor[10]=0;
                    valor[11]=0;
                    valor[12]=codigo_categoria;
                    valor[13]='';

                    var tabela_itens=valor.join("|");
                    array_tabela_itens.push(tabela_itens);
                    grupo_itens=array_tabela_itens.join("<|>");
                }
            });
        }

        $("#array_itens").val(grupo_itens);
        var tipo_gravacao = $("#tipo_gravacao").val();
        
        //alert ('vou gravar com pesagem transferecia ou venda - gravar_movimentacao_individual.php'); 
        // Gravar movimentação com pesagem

        var dados = $('#form_gravar').serialize();
        
        $.ajax({
            type: "POST", 
            url: 'gravar_movimentacao_individual.php',
            data: dados,
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    //$("#numero_pesagem_id").val(data.numero_doc);
                    //$("#tipo_gravacao").val(2);
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }
        });
    }
}

/*function gravar_movimentacao_digitada() {
    var controle_estoque = $("#controle_estoque").val();
    var array_tabela_itens = new Array();
    var valor = new Array();
    var grupo_itens = "";
     
    $('#tabela_itens_digitados tbody tr').each(function(){
        for (i = 0; i <= 8; i++) {
            valor[i]=0;
        }

        if(controle_estoque=='I') {
            var codigo = $(this).find('.id_animal').html();
            var peso = $(this).find('.peso_animal').html();
            var sexo = $(this).find('.sexo_animal').html();
            var nascimento = $(this).find('.nascimento_animal').html();
            var pelagem = $(this).find('.pelagem_animal').html();
            var raca = $(this).find('.raca_animal').html();
            var mae = $(this).find('.mae_animal').html();
            var observacao = $(this).find('.observacao').html();
            var codigo_id = $(this).find('.codigo_id').html();
            var codigo_categoria = $(this).find('.codigo_categoria').html();

            if (codigo!=undefined && codigo!=0){
                valor[0]=codigo;
                valor[1]=peso;
                valor[2]=sexo;
                valor[3]=nascimento;
                valor[4]=raca;
                valor[5]=pelagem;
                valor[6]=mae;
                valor[7]=observacao;
                valor[8]=codigo_id;
                valor[12]=codigo_categoria;

                var tabela_itens=valor.join("|");
                array_tabela_itens.push(tabela_itens);
                grupo_itens=array_tabela_itens.join("<|>");
            }
        }
        else {
            var categoria = $(this).find('.id_categoria').html();
            var qtd_animal = $(this).find('.qtd_animal').html();
            var sexo = $(this).find('.sexo_animal').html();
            var observacao = $(this).find('.observacao').html();

            if (categoria!=undefined && categoria!=0){
                valor[0]=categoria;
                valor[1]=qtd_animal;
                valor[2]=sexo;
                valor[3]=observacao;

                var tabela_itens=valor.join("|");
                array_tabela_itens.push(tabela_itens);
                grupo_itens=array_tabela_itens.join("<|>");
            }
        }
    });

    $("#array_itens").val(grupo_itens);
    
    if (grupo_itens=='') {
        $("#mensagem_retorno").modal();
        $("#mensagem_retorno .modal-body").html('Movimentação finalizada sem gravar itens.');
        return;
    }

    var dados = $('#form_gravar').serialize();

    //alert ('vou gravar movimentacao transferencia ou venda com itens digitados'); 

    $.ajax({
    type: "POST",
        url: 'gravar_movimentacao_individual_digitada.php',
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
*/

function gravar_movimentacao_venda_transferencia() {
    var controle_estoque = $("#controle_estoque").val();
    var array_tabela_itens = new Array();
    var valor = new Array();
    var grupo_itens = "";

    var cat_selecionada_m1 = 0;
    var cat_selecionada_m2 = 0;
    var cat_selecionada_m3 = 0;
    var cat_selecionada_m4 = 0;
    var cat_selecionada_m5 = 0;

    var cat_selecionada_f1 = 0;
    var cat_selecionada_f2 = 0;
    var cat_selecionada_f3 = 0;
    var cat_selecionada_f4 = 0;
    var cat_selecionada_f5 = 0;

    var total_listados = $('.total_a_digitar').val();
    var total_selecionados = $('.total_digitados').val();
    var tem_item = '';
    var aChk = document.getElementsByName("id_animal_selecao");

    if(controle_estoque=='I') {
        $('#tabela_itens_digitados tbody tr').each(function(){
            for (i = 0; i <= 8; i++) {
                valor[i]=0;
            }

            var codigo_alfa = $(this).find('.id_animal_alfa').html();
           

            if (codigo_alfa=='') {
                var codigo = $(this).find('.id_animal').html();
            }
            else {
                var codigo = codigo_alfa+'-'+$(this).find('.id_animal').html();
            }

            var codigo_id = $(this).find('.animal_id').html();

            for (var i = 0; i < aChk.length; i++) {
                if (aChk[i].checked == true) {
                    codigo_selecionado = aChk[i].value;

                    if (codigo_id==codigo_selecionado) {
                        //var peso = $(this).find('.peso_animal').html();
                        var peso = 0;
                        var sexo = $(this).find('.sexo_animal').html();
                        var nascimento = $(this).find('.nascimento_animal').html();
                        var pelagem = $(this).find('.pelagem_animal').html();
                        var raca = $(this).find('.raca_animal').html();
                        var mae = $(this).find('.mae_animal').html();
                        var codigo_categoria = $(this).find('.codigo_categoria').html();

                        if (sexo == 'Macho') {
                            switch (codigo_categoria) {
                                case '001':
                                    cat_selecionada_m1++;                                    
                                    break;
                                case '002':
                                    cat_selecionada_m2++;                                    
                                    break;
                                case '003':   
                                    cat_selecionada_m3++;                                    
                                    break;
                                case '004':   
                                    cat_selecionada_m4++;                                    
                                    break;
                                case '005':   
                                    cat_selecionada_m5++;                                    
                                    break;
                            } 
                        }
                        else {
                            switch (codigo_categoria) {
                                case '001':
                                    cat_selecionada_f1++;                                    
                                    break;
                                case '002':
                                    cat_selecionada_f2++;                                    
                                    break;
                                case '003':   
                                    cat_selecionada_f3++;                                    
                                    break;
                                case '004':   
                                    cat_selecionada_f4++;                                    
                                    break;
                                case '005':   
                                    cat_selecionada_f5++;                                    
                                    break;
                            } 
                        }

                        if (codigo!=undefined && codigo!=0){
                            valor[0]=codigo;
                            valor[1]=peso;
                            valor[2]=sexo;
                            valor[3]=nascimento;
                            valor[4]=raca;
                            valor[5]=pelagem;
                            valor[6]=mae;
                            valor[7]='';
                            valor[8]=codigo_id;
                            valor[12]=codigo_categoria;
                            valor[13]='';

                            var tabela_itens=valor.join("|");
                            array_tabela_itens.push(tabela_itens);
                            grupo_itens=array_tabela_itens.join("<|>");
                        }
                    }
                }
            }
        });

        var mens = '';
        var mens_lista = '';
        var tem_mens = '';
        mens+= 'ANIMAIS INSUFICIENTES!<br><br>';
        mens+= 'Transferir para a Saída:';

        mens_lista+= 'Categoria    Sexo       Quantidade<br>';

        var cat_pasto_m1 = $("#cat_pasto_m1").val();
        var cat_pasto_m2 = $("#cat_pasto_m2").val();
        var cat_pasto_m3 = $("#cat_pasto_m3").val();
        var cat_pasto_m4 = $("#cat_pasto_m4").val();
        var cat_pasto_m5 = $("#cat_pasto_m5").val();
        var cat_pasto_f1 = $("#cat_pasto_f1").val();
        var cat_pasto_f2 = $("#cat_pasto_f2").val();
        var cat_pasto_f3 = $("#cat_pasto_f3").val();
        var cat_pasto_f4 = $("#cat_pasto_f4").val();
        var cat_pasto_f5 = $("#cat_pasto_f5").val();

        if (cat_pasto_m1<cat_selecionada_m1) {
            mens_lista+= '00 a 07         Macho       ' + (cat_selecionada_m1 - cat_pasto_m1) + '<br>'; 
            tem_mens = 'S';  
        }

        if (cat_pasto_m2<cat_selecionada_m2) {
            mens_lista+= '08 a 12         Macho       ' + (cat_selecionada_m2 - cat_pasto_m2) + '<br>';   
            tem_mens = 'S';  
        }

        if (cat_pasto_m3<cat_selecionada_m3) {
            mens_lista+= '13 a 24         Macho       ' + (cat_selecionada_m3 - cat_pasto_m3) + '<br>';   
            tem_mens = 'S';  
        }

        if (cat_pasto_m4<cat_selecionada_m4) {
            mens_lista+= '25 a 36         Macho       ' + (cat_selecionada_m4 - cat_pasto_m4) + '<br>';   
            tem_mens = 'S';  
        }

        if (cat_pasto_m5<cat_selecionada_m5) {
            mens_lista+= '> 36                Macho       ' + (cat_selecionada_m5 - cat_pasto_m5) + '<br>';   
            tem_mens = 'S';  
        }

        if (cat_pasto_f1<cat_selecionada_f1) {
            mens_lista+= '00 a 07         Fêmea       ' + (cat_selecionada_f1 - cat_pasto_f1) + '<br>'; 
            tem_mens = 'S';  
        }

        if (cat_pasto_f2<cat_selecionada_f2) {
            mens_lista+= '08 a 12         Fêmea       ' + (cat_selecionada_f2 - cat_pasto_f2) + '<br>';   
            tem_mens = 'S';  
        }

        if (cat_pasto_f3<cat_selecionada_f3) {
            mens_lista+= '13 a 24         Fêmea       ' + (cat_selecionada_f3 - cat_pasto_f3) + '<br>';   
            tem_mens = 'S';  
        }

        if (cat_pasto_f4<cat_selecionada_f4) {
            mens_lista+= '25 a 36         Fêmea       ' + (cat_selecionada_f4 - cat_pasto_f4) + '<br>';   
            tem_mens = 'S';  
        }

        if (cat_pasto_f5<cat_selecionada_f5) {
            mens_lista+= '> 36                Fêmea       ' + (cat_selecionada_f5 - cat_pasto_f5) + '<br>';   
            tem_mens = 'S';  
        }

        if (tem_mens=='S') {
            $("#mensagem_erro_transferencia").modal();
            $(".mens").html(mens_lista);
            $("#mensagem_erro_transferencia .modal-body").html(mens);
            return;
        }
    }
    else { // Controle por lote
        $('#tabela_itens_digitados tbody tr').each(function(){
            for (i = 0; i <= 8; i++) {
                valor[i]=0;
            }

            var item = $(this).find('.checkbox1').val();
            var sexo = item.substr(0, 1);      
            var cate = item.substr(1, 3);      

            var sexo_categoria = sexo+cate;

            for (var i = 0; i < aChk.length; i++) {
                if (aChk[i].checked == true) {
                    sexo_categoria_selecionado = aChk[i].value;

                    if (sexo_categoria==sexo_categoria_selecionado) {
                        var sexo = $(this).find('.sexo_animal').html();
                        var codigo_categoria = $(this).find('.codigo_categoria').html();
                        var qtd_selecionada = parseInt($(this).find('.qtd_digitada').val());

                        if (sexo_categoria!=undefined && sexo_categoria!=''){
                            valor[0]=sexo_categoria;
                            valor[1]=qtd_selecionada;
                            valor[2]=sexo;
                            valor[3]='';
                            valor[4]=0;
                            valor[5]=0;
                            valor[6]=0;
                            valor[7]='';
                            valor[8]=0;
                            valor[12]=codigo_categoria;
                            valor[13]='';
                            var tabela_itens=valor.join("|");
                            array_tabela_itens.push(tabela_itens);
                            grupo_itens=array_tabela_itens.join("<|>");
                        }
                    }
                }
            }
        });
    }

    $("#array_itens").val(grupo_itens);
    
    if (grupo_itens=='') {
        $("#mensagem_retorno").modal();
        $("#mensagem_retorno .modal-body").html('Movimentação finalizada sem gravar itens.');
        return;
    }

    var dados = $('#form_gravar').serialize();

    $("#aguardar").modal();

    //alert ('vou gravar movimentacao transferencia ou venda com itens selecionados da lista'); 

    $.ajax({
    type: "POST",
        url: 'gravar_movimentacao_individual_digitada.php',
        data: dados,
        success: function(data){
            $('#aguardar').modal('hide');
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

function gravar_movimentacao_digitada_editar() {
    var array_tabela_itens = new Array();
    var valor = new Array();
    var grupo_itens = "";
    var tem_itens_gravar = 'N';
     
    $('#tabela_itens_digitados tbody tr').each(function(){
        for (i = 0; i <= 2; i++) {
            valor[i]=0;
        }

        var codigo = $(this).find('.id_animal').html();
        var peso = $(this).find('.peso_animal').html();
        var sexo = $(this).find('.sexo_animal').html();
        var nascimento = $(this).find('.nascimento_animal').html();
        var pelagem = $(this).find('.pelagem_animal').html();
        var raca = $(this).find('.raca_animal').html();
        var mae = $(this).find('.mae_animal').html();
        var observacao = $(this).find('.observacao').html();
        var codigo_id = $(this).find('.codigo_id').html();
        var situacao_excluir = $(this).find('.excluir').html();

        if (situacao_excluir=='N') {
            tem_itens_gravar = 'S';
        }

        if (codigo!=undefined && codigo!=0){
            valor[0]=codigo;
            valor[1]=peso;
            valor[2]=sexo;
            valor[3]=nascimento;
            valor[4]=raca;
            valor[5]=pelagem;
            valor[6]=mae;
            valor[7]=observacao;
            valor[8]=codigo_id;
            valor[9]=situacao_excluir;

            var tabela_itens=valor.join("|");
            array_tabela_itens.push(tabela_itens);
            grupo_itens=array_tabela_itens.join("<|>");
        }
    });

    $("#array_itens").val(grupo_itens);
    $("#tem_itens_gravar").val(tem_itens_gravar);
    
    var dados = $('#form_gravar').serialize();
    $.ajax({
        type: "POST",
        url: 'gravar_movimentacao_individual_digitada_edicao.php',
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

function gravar_movimentacao_digitada_entrada() {

    $("#aguardar").modal('show');

    var array_tabela_itens = new Array();
    var valor = new Array();
    var grupo_itens = "";
     
    $('#tabela_itens_digitados_entrada tbody tr').each(function(){
        for (i = 0; i <= 11; i++) {
            valor[i]=0;
        }

        var categoria = $(this).find('.codigo_categoria_entrada').html();
        var idade = $(this).find('.idade_entrada').html();
        var sexo = $(this).find('.sexo_entrada').html();
        var raca = $(this).find('.codigo_raca_entrada').html();
        var pelagem = $(this).find('.codigo_pelagem_entrada').html();
        var qtd = $(this).find('.qtd_cat_entrada').html();
        var sequencia_numerica = $(this).find('.sequencia_numeria_entrada').html();
        var marcacao_alfa = $(this).find('.marcacao_alfa_entrada').html();
        var desc_raca = $(this).find('.desc_raca').html();
        var desc_pelagem = $(this).find('.desc_pelagem').html();
        var desc_categoria = $(this).find('.desc_categoria').html();
        var peso_entrada = $(this).find('.peso_entrada').html();

        if (categoria!=undefined && categoria!=''){
            valor[0]=categoria;
            valor[1]=idade;
            valor[2]=sexo;
            valor[3]=raca;
            valor[4]=pelagem;
            valor[5]=Number(qtd);
            valor[6]=Number(sequencia_numerica);
            valor[7]=marcacao_alfa;
            valor[8]=desc_raca;
            valor[9]=desc_pelagem;
            valor[10]=desc_categoria;
            valor[11]=peso_entrada;

            var tabela_itens=valor.join("|");
            array_tabela_itens.push(tabela_itens);
            grupo_itens=array_tabela_itens.join("<|>");
        }
    });

    $("#array_itens").val(grupo_itens);

    var dados = $('#form_gravar').serialize();
    $.ajax({
        type: "POST",
        url: 'gravar_movimentacao_compra.php',
        data: dados,
        success: function(data){
            $("#aguardar").modal('hide');

            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $('#id_pasto').val(data.id_pasto);
                $('#descricao_pasto').val(data.descricao_pasto);
                $('#desc_lote_destino').val(data.descricao_lote);

                if (data.descricao_lote=='') {
                    $("#mensagem_descricao_lote").modal();
                    $("#mensagem_descricao_lote .modal-body").html(data.message + '<br>Necessário incluir a Descrição do Lote para o Pasto: ' + data.descricao_pasto);
                }
                else {
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }

            /*if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }*/
        }
    });
}

function gravar_morte() {
    var controle_estoque = $("#controle_estoque").val();

    var array_tabela_itens = new Array();
    var valor = new Array();
    var grupo_itens = "";
 
    $('#tabela_itens tbody tr').each(function(){
        for (i = 0; i <= 2; i++) {
            valor[i]=0;
        }

        var codigo = $(this).find('.id_animal').html();
        var peso = $(this).find('.peso_animal').html();
        var sexo = $(this).find('.sexo_animal').html();
        var nascimento = $(this).find('.nascimento_animal').html();
        var pelagem = $(this).find('.pelagem_animal').html();
        var raca = $(this).find('.raca_animal').html();
        var mae = $(this).find('.mae_animal').html();
        var observacao = $(this).find('.observacao').html();
        var codigo_id = $(this).find('.codigo_id').html();
        var motivo_morte = $(this).find('.motivo_morte').html();
        var codigo_motivo_morte = $(this).find('.codigo_motivo_morte').html();
        var pasto_morte = $(this).find('.pasto_morte').html();
        var categoria_morte = $(this).find('.categoria_morte').html();
        
        if (codigo!=undefined && codigo!=0 && controle_estoque=='I'){
            valor[0]=codigo;
            valor[1]=peso;
            valor[2]=sexo;
            valor[3]=nascimento;
            valor[4]=raca;
            valor[5]=pelagem;
            valor[6]=mae;
            valor[7]=observacao;
            valor[8]=codigo_id;
            valor[9]=motivo_morte;
            valor[10]=codigo_motivo_morte;
            valor[11]=pasto_morte;
            valor[12]=categoria_morte;
            valor[13]='';

            var tabela_itens=valor.join("|");
            array_tabela_itens.push(tabela_itens);
            grupo_itens=array_tabela_itens.join("<|>");
        }
        else if (motivo_morte!=undefined && motivo_morte!=0 && controle_estoque=='L'){
            valor[0]=codigo;
            valor[1]=peso;
            valor[2]=sexo;
            valor[3]=1;
            valor[4]=0;
            valor[5]=0;
            valor[6]=0;
            valor[7]=observacao;
            valor[8]=codigo_id;
            valor[9]=motivo_morte;
            valor[10]=codigo_motivo_morte;
            valor[11]=pasto_morte;
            valor[12]=categoria_morte;
            valor[13]='';

            var tabela_itens=valor.join("|");
            array_tabela_itens.push(tabela_itens);
            grupo_itens=array_tabela_itens.join("<|>");
        }
    });

    $("#array_itens").val(grupo_itens);

    //alert ('vou gravar morte - gravar_movimentacao_individual.php'); 
    // gravar morte

    var dados = $('#form_gravar').serialize();

    $.ajax({
        type: "POST", 
        url: 'gravar_movimentacao_individual.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $("#mensagem_retorno_morte").modal();
                $("#mensagem_retorno_morte .modal-body").html(data.message);
            }
        }
    });
}

function gravar_outra() {
    var controle_estoque = $("#controle_estoque").val();

    var array_tabela_itens = new Array();
    var valor = new Array();
    var grupo_itens = "";
 
    $('#tabela_itens tbody tr').each(function(){
        for (i = 0; i <= 2; i++) {
            valor[i]=0;
        }

        var codigo = $(this).find('.id_animal').html();
        var peso = $(this).find('.peso_animal').html();
        var sexo = $(this).find('.sexo_animal').html();
        var nascimento = $(this).find('.nascimento_animal').html();
        var pelagem = $(this).find('.pelagem_animal').html();
        var raca = $(this).find('.raca_animal').html();
        var mae = $(this).find('.mae_animal').html();
        var observacao = $(this).find('.observacao').html();
        var codigo_id = $(this).find('.codigo_id').html();
        var pasto = $(this).find('.pasto_outra').html();
        var categoria = $(this).find('.categoria_outra').html();
        
        if (codigo!=undefined && codigo!=0 && controle_estoque=='I'){
            valor[0]=codigo;
            valor[1]=peso;
            valor[2]=sexo;
            valor[3]=nascimento;
            valor[4]=raca;
            valor[5]=pelagem;
            valor[6]=mae;
            valor[7]=observacao;
            valor[8]=codigo_id;
            valor[9]='';
            valor[10]=0;
            valor[11]=pasto;
            valor[12]=categoria;
            valor[13]='';

            var tabela_itens=valor.join("|");
            array_tabela_itens.push(tabela_itens);
            grupo_itens=array_tabela_itens.join("<|>");
        }
        else if (categoria!=undefined && categoria!=0 && controle_estoque=='L'){
            valor[0]=codigo;
            valor[1]=peso;
            valor[2]=sexo;
            valor[3]=1;
            valor[4]=0;
            valor[5]=0;
            valor[6]=0;
            valor[7]=observacao;
            valor[8]=codigo_id;
            valor[9]='';
            valor[10]=0;
            valor[11]=pasto;
            valor[12]=categoria;
            valor[13]='';

            var tabela_itens=valor.join("|");
            array_tabela_itens.push(tabela_itens);
            grupo_itens=array_tabela_itens.join("<|>");
        }
    });

    $("#array_itens").val(grupo_itens);

    //alert ('vou gravar outra - gravar_movimentacao_individual.php'); 
    // gravar outra
    var dados = $('#form_gravar').serialize();
    $.ajax({
        type: "POST", 
        url: 'gravar_movimentacao_individual.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $("#mensagem_retorno_outra").modal();
                $("#mensagem_retorno_outra .modal-body").html(data.message);
            }
        }
    });
}

function digita_valor(){
    $('#peso_inicial_nasc_filtro').bind('keypress',mask.money);
    $('#peso_final_nasc_filtro').bind('keypress',mask.money);
    $('#peso_inicial_desmama_filtro').bind('keypress',mask.money);
    $('#peso_final_desmama_filtro').bind('keypress',mask.money);
    $('#peso_inicial_ultimo_filtro').bind('keypress',mask.money);
    $('#peso_final_ultimo_filtro').bind('keypress',mask.money);
}

function peso_inicial_nasc_filtro() {
    var peso_inicial_nasc_filtro = $("#peso_inicial_nasc_filtro").val();
    
    if (verifica_virgula(peso_inicial_nasc_filtro) == ",") {
        peso_inicial_nasc_filtro = replace_valor(peso_inicial_nasc_filtro);
    }

    $("#peso_inicial_nasc_filtro").val(formatMoney(peso_inicial_nasc_filtro));
}

function peso_final_nasc_filtro() {
    var peso_final_nasc_filtro = $("#peso_final_nasc_filtro").val();
    if (verifica_virgula(peso_final_nasc_filtro) == ",") {
        peso_final_nasc_filtro = replace_valor(peso_final_nasc_filtro);
    }

    $("#peso_final_nasc_filtro").val(formatMoney(peso_final_nasc_filtro));
}

function peso_inicial_desmama_filtro() {
    var peso_inicial_desmama_filtro = $("#peso_inicial_desmama_filtro").val();
    if (verifica_virgula(peso_inicial_desmama_filtro) == ",") {
        peso_inicial_desmama_filtro = replace_valor(peso_inicial_desmama_filtro);
    }

    $("#peso_inicial_desmama_filtro").val(formatMoney(peso_inicial_desmama_filtro));
}

function peso_final_desmama_filtro() {
    var peso_final_desmama_filtro = $("#peso_final_desmama_filtro").val();
    if (verifica_virgula(peso_final_desmama_filtro) == ",") {
        peso_final_desmama_filtro = replace_valor(peso_final_desmama_filtro);
    }

    $("#peso_final_desmama_filtro").val(formatMoney(peso_final_desmama_filtro));
}

function peso_inicial_ultimo_filtro() {
    var peso_inicial_ultimo_filtro = $("#peso_inicial_ultimo_filtro").val();
    if (verifica_virgula(peso_inicial_ultimo_filtro) == ",") {
        peso_inicial_ultimo_filtro = replace_valor(peso_inicial_ultimo_filtro);
    }

    $("#peso_inicial_ultimo_filtro").val(formatMoney(peso_inicial_ultimo_filtro));
}

function peso_final_ultimo_filtro() {
    var peso_final_ultimo_filtro = $("#peso_final_ultimo_filtro").val();
    if (verifica_virgula(peso_final_ultimo_filtro) == ",") {
        peso_final_ultimo_filtro = replace_valor(peso_final_ultimo_filtro);
    }

    $("#peso_final_ultimo_filtro").val(formatMoney(peso_final_ultimo_filtro));
}

function exibe_peso_desmama(){
    var peso_desmama_animal = $("#peso_desmama_animal").val();
    if (verifica_virgula(peso_desmama_animal)==',') {
        peso_desmama_animal = replace_valor(peso_desmama_animal);
    }

    $("#peso_desmama_animal").val(formatMoney2(peso_desmama_animal));
}

function exibe_ultimo_peso(){
    var ultimo_peso_animal = $("#ultimo_peso_animal").val();
    if (verifica_virgula(ultimo_peso_animal)==',') {
        ultimo_peso_animal = replace_valor(ultimo_peso_animal);
    }

    $("#ultimo_peso_animal").val(formatMoney2(ultimo_peso_animal));
}

function exportar_excel_pesagem() {
    var local = $("#local_pesagem").val();
    var epoca = $("#epoca_pesagem").val();
    var desc_filtro = $("#descricao_filtro").val();
    var raca = $("#codigo_raca_filtro").val();
    var pai = $("#codigo_pai_filtro").val();
    var mae = $("#codigo_mae_filtro").val();
    var categoria = $("#codigo_categoria_filtro").val();
    var data_nasc_inicial = $("#data_nasc_inicial_filtro").val();
    var data_nasc_final = $("#data_nasc_final_filtro").val();
    var peso_nasc_inicial = $("#peso_inicial_nasc_filtro").val();
    var peso_nasc_final = $("#peso_final_nasc_filtro").val();
    var peso_desmama_inicial = $("#peso_inicial_desmama_filtro").val();
    var peso_desmama_final = $("#peso_final_desmama_filtro").val();
    var peso_ult_inicial = $("#peso_inicial_ultimo_filtro").val();
    var peso_ult_final = $("#peso_final_ultimo_filtro").val();

    var macho = $('#macho');
    var femea = $('#femea');

    if (macho.is(":checked") && femea.is(":checked")) {
        sexo=['Todos'];
    }
    else if (macho.is(":checked")){
        sexo=['M'];
    }
    else if (femea.is(":checked")){
        sexo=['F'];
    }

    if (categoria==null) {
        var array_categoria= new Array();
    }
    else {
        var array_categoria = new Array();
        var valor = new Array();

        for (i = 0; i <= categoria.length; i++) {
            valor[i]=categoria[i];
        }

        var array_categoria=valor.join(",");
    }

    if (raca==null) {
        var array_raca= new Array();
    }
    else {
        var array_raca = new Array();
        var valor = new Array();

        for (i = 0; i <= raca.length; i++) {
            valor[i]=raca[i];
        }

        var array_raca=valor.join(",");
    }

    if (pai==null) {
        var array_pai= new Array();
    }
    else {
        var array_pai = new Array();
        var valor = new Array();

        for (i = 0; i <= pai.length; i++) {
            valor[i]=pai[i];
        }

        var array_pai=valor.join(",");
    }

    if (mae==null) {
        var array_mae= new Array();
    }
    else {
        var array_mae = new Array();
        var valor = new Array();

        for (i = 0; i <= mae.length; i++) {
            valor[i]=mae[i];
        }

        var array_mae=valor.join(",");
    }
   
    location.href='rel_exportar_pesagem_excel.php?local=' + local + '&epoca=' + epoca + '&sexo=' + sexo + 
    '&filtro=' + desc_filtro + '&raca=' + array_raca + '&categoria=' + array_categoria + 
    '&pai=' + array_pai + '&mae=' + array_mae + '&data_nasc_inicial=' + data_nasc_inicial + 
    '&data_nasc_final=' + data_nasc_final + '&peso_nasc_inicial=' + peso_nasc_inicial + 
    '&peso_nasc_final=' + peso_nasc_final+ '&peso_desmama_inicial=' + peso_desmama_inicial + 
    '&peso_desmama_final=' + peso_desmama_final + '&peso_ult_inicial=' + peso_ult_inicial + 
    '&peso_ult_final=' + peso_ult_final;

    tout = setTimeout('finalizar_sair()', 3000);

}

function imprimir_movimentacao(movimento_id) {
    location.href='rel_movimentacao_excel.php?movimento_id=' + movimento_id;
}

function modal_faturamento_venda($codigo, $qtd) {

    $('.numero_movimento').text('Movimentação nº: ' + $codigo + '   Qtde Animais: ' + $qtd);
    $('.numero_movimento').val($codigo);
    $('#modal_confirmar_venda').modal('show');
}

function confirmar_faturamento_venda($numero_faturamento) {
    var numero_movimento = $('.numero_movimento').val();

    if (window.confirm("Confirma linkar o faturamento " + $numero_faturamento + " com a movimentação " + numero_movimento + '?')) {     
        $.post("link_movimentacao_venda.php",{id_faturamento: $numero_faturamento, id_movimentacao: numero_movimento}, function(valor){
         
            if (valor!=0){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(valor);
                return;
            }
            else {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html('Faturamento confirmado com sucesso');
                return;
            }
       });
    }
}

function modal_faturamento_compra($codigo, $qtd) {

    $('.numero_movimento').text('Movimentação nº: ' + $codigo + '   Qtde Animais: ' + $qtd);
    $('.numero_movimento').val($codigo);
    $('#modal_confirmar_compra').modal('show');
}

function confirmar_faturamento_compra($numero_faturamento) {
    var numero_movimento = $('.numero_movimento').val();

    if (window.confirm("Confirma linkar o faturamento " + $numero_faturamento + " com a movimentação " + numero_movimento + '?')) {     
        $.post("link_movimentacao_venda.php",{id_faturamento: $numero_faturamento, id_movimentacao: numero_movimento}, function(valor){
         
            if (valor!=0){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(valor);
                return;
            }
            else {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html('Faturamento confirmado com sucesso');
                return;
            }
       });
    }
}

function confirmar_aceite_transferencia($numero_movimento, $qtd, $origem, $destino) {
    if (window.confirm("Confirma a transferência de " + $qtd + ' animais, da origem: ' + $origem + ' para o destino: '+ $destino + '?')) {     
        $.ajax({
            type: "POST",
            url: 'aceite_transferencia_individual.php',
            data: {
                'id_movimentacao': $numero_movimento
                },
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    $('#id_pasto').val(data.id_pasto);
                    $('#descricao_pasto').val(data.descricao_pasto);
                    $('#desc_lote_destino').val(data.descricao_lote);

                    if (data.descricao_lote=='') {
                        $("#mensagem_descricao_lote").modal();
                        $("#mensagem_descricao_lote .modal-body").html(data.message + '<br>Necessário incluir a Descrição do Lote para o Pasto: ' + data.descricao_pasto);
                    }
                    else {
                        $("#mensagem_retorno").modal();
                        $("#mensagem_retorno .modal-body").html(data.message);
                    }
                }
            }
        });

        /*$.post("aceite_transferencia_individual.php",{id_movimentacao: $numero_movimento}, function(valor){
            if (valor!=0){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(valor);
                return;
            }
            else {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html('Transferência confirmada com sucesso');
                return;
            }
       });*/
    }
}

$(document).ready(function(){
    $('.radiocheck').click(function(event) {
        $("#botao_confirma").attr("disabled", false);
        $('#botao_confirma').removeClass('btn-secondary').addClass('btn-primary');
    })
})

function confirmar_aceite_transferencia_selecionados() {
    var aChk = document.getElementsByName("id_mov");
    
    var tem_movimento= "";
    
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            tem_movimento="S";
        }
    }
    if (tem_movimento=="") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Não existem registros selecionados para o aceite.');
        return;
    }
    
    var contas = new Array();
    var grupo_registros = "";
    var aChk = document.getElementsByName("id_mov");

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            codigo_id = aChk[i].value;
            contas.push(codigo_id);
            grupo_registros = contas.join("<|>");
        }
    }

    $.ajax({
        type: "POST",
        url: 'aceite_transferencia_lista.php',
        data: {
            'grupo_registros': grupo_registros
            },
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $('#id_pasto').val(data.id_pasto);
                $('#descricao_pasto').val(data.descricao_pasto);
                $('#desc_lote_destino').val(data.descricao_lote);

                if (data.descricao_lote=='') {
                    $("#mensagem_descricao_lote").modal();
                    $("#mensagem_descricao_lote .modal-body").html(data.message + '<br>Necessário incluir a Descrição do Lote para o Pasto: ' + data.descricao_pasto);
                }
                else {
                    $("#mensagem_retorno").modal();
                    $("#mensagem_retorno .modal-body").html(data.message);
                }
            }
        }
    });

    /*$.post("aceite_transferencia_lista.php", {grupo_registros: grupo_registros}, function (get_retorno) {
        if (get_retorno != 0) {
            alert (get_retorno);
        }
        else {
            alert ('Aceite efetuado com sucesso.');
            document.location.href = "form_movimentacao_animais.php";
        }
    });*/
}

function limpar_selecao_origem() {
    $('#codigo_origem_filtro').val(''); // Ou $('#meuSelect').val([]);
    $('#codigo_origem_filtro').selectpicker('refresh');

    const linkElement = document.querySelector('.informacao');
    if (linkElement) {
        linkElement.style.color = 'darkgray';
    }
}

function limpar_selecao_categoria() {
    $('#codigo_categoria_filtro').val(''); // Ou $('#meuSelect').val([]);
    $('#codigo_categoria_filtro').selectpicker('refresh');

    const linkElement = document.querySelector('.informacao');
    if (linkElement) {
        linkElement.style.color = 'darkgray';
    }
}

function limpar_selecao_raca() {
    $('#codigo_raca_filtro').val(''); // Ou $('#meuSelect').val([]);
    $('#codigo_raca_filtro').selectpicker('refresh');

    const linkElement = document.querySelector('.informacao');
    if (linkElement) {
        linkElement.style.color = 'darkgray';
    }
}

function limpar_selecao_pai() {
    $('#codigo_pai_filtro').val(''); // Ou $('#meuSelect').val([]);
    $('#codigo_pai_filtro').selectpicker('refresh');

    const linkElement = document.querySelector('.informacao');
    if (linkElement) {
        linkElement.style.color = 'darkgray';
    }
}

function limpar_selecao_mae() {
    $('#codigo_mae_filtro').val(''); // Ou $('#meuSelect').val([]);
    $('#codigo_mae_filtro').selectpicker('refresh');

    const linkElement = document.querySelector('.informacao');
    if (linkElement) {
        linkElement.style.color = 'darkgray';
    }
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

$(window).resize(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});

$(document).ready(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
 "date-br-pre": function ( a ) {
  if (a == null || a == "") {
   return 0;
  }
  var brDatea = a.split('/');
  return (brDatea[2] + brDatea[1] + brDatea[0]) * 1;
 },

 "date-br-asc": function ( a, b ) {
  return ((a < b) ? -1 : ((a > b) ? 1 : 0));
 },

 "date-br-desc": function ( a, b ) {
  return ((a < b) ? 1 : ((a > b) ? -1 : 0));
 }
} );

/*document.addEventListener('DOMContentLoaded', function() {
    // Seleciona todos os checkboxes com a classe 'checkbox1'
    const checkboxes = document.querySelectorAll('.checkbox1');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Verifica se o checkbox está marcado
            if (this.checked) {
                // Pega a linha (tr) pai do checkbox
                const row = this.closest('tr');

                // Se a linha for encontrada, extrai os valores
                if (row) {
                    const idAnimalElement = row.querySelector('.id_animal');
                    //const femeaSelecionadaElement = row.querySelector('.femea_selecionada');
                    //const controlElement = row.querySelector('.controle');

                    const idAnimal = idAnimalElement ? idAnimalElement.textContent.trim() : '';
                    //const femeaSelecionada = femeaSelecionadaElement ? femeaSelecionadaElement.textContent.trim() : '';
                    //const controle = controlElement ? controlElement.textContent.trim() : '';

                    console.log('ID Animal:', idAnimal);
                    //console.log('Fêmea Selecionada:', femeaSelecionada);
                    //console.log('Controle:', controle);

                    // Aqui você pode fazer o que precisar com os valores, por exemplo:
                    // - Enviar para uma função
                    // - Atualizar um campo oculto no formulário
                    // - Exibir em algum lugar na página
                }
            } else {
                // O checkbox foi desmarcado, você pode adicionar lógica aqui se necessário
                console.log('Checkbox desmarcado.');
            }
        });
    });
}); */           
