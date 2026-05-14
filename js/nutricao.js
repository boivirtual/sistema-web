/**NUTRIÇÃO*/
var controle_estoque = $("#controle_estoque").val();

window.addEventListener("load", function(event) {
    $.post("lista_produto.php", {}, function(valor){
        $("select[name=codigo_produto]").html(valor);
        $('.selectpicker').selectpicker('refresh');            
    });

    var expande_tela = $("#expande_tela").val();

    if (expande_tela=="S"){
        if (jQuery('#sidebar > ul').is(":visible") === true) {
            jQuery('#main-content').css({
                'margin-left': '0px'
            });
            jQuery('#sidebar').css({
                'margin-left': '-180px'
            });
            jQuery('#sidebar > ul').hide();
            jQuery("#container").addClass("sidebar-closed");
        }
    }

    var local = $("#codigo_local").val();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var tipo_periodo_lote = $("input[name='tipo_rel']:checked").val();

    if (tipo_periodo_lote==undefined) {
        tipo_periodo_lote='P';
    }

    if (local!='' && local!=0) {
        $.post("lista_lotes_nutricao.php", {local:local, data_inicial:data_inicial, data_final:data_final, tipo_rel:tipo_periodo_lote}, function(valor){
            $("select[name=descricao_lote]").html(valor);
            $('.selectpicker').selectpicker('refresh');            

            $("select[name=um_lote]").html(valor);
        });

        $.post("lista_pasto_nutricao.php", {local:local}, function(valor){
            $("select[name=codigo_pasto]").html(valor);
            $('.selectpicker').selectpicker('refresh');            
        });

        if (tipo_periodo_lote=='P') {
            $('.descricao_lote').show();
            $('.um_lote').hide();
            $('.label_data_inicial').html('* Data Inicial');
            $('.label_data_final').html('* Data Final');
        }
        else {
            $('.descricao_lote').hide();
            $('.um_lote').show();
            $('.label_data_inicial').html('Data Inicial');
            $('.label_data_final').html('Data Final');
        }
    }

    var local = $("#codigo_local_filtro").val();

    if (local==null || local=='000000000') {
        local=[''];
    }

    $.post("lista_pasto_rel.php", {local:local}, function(valor){
        $("select[name=codigo_pasto_filtro]").html(valor);
        $('.selectpicker').selectpicker('refresh');            
    });

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

function validar_data_final_consulta_nutricao() {
    var data_InicioInput = $('#data_inicial').val();
    var partesDaData =data_InicioInput.split('-');
    var anoInicio = partesDaData[0];    
    var mesInicio = partesDaData[1];    
    var diaInicio = partesDaData[2];    

    var data_FimInput = $('#data_final').val();
    var partesDaData =data_FimInput.split('-');
    var anoFim = partesDaData[0];    
    var mesFim = partesDaData[1];    
    var diaFim = partesDaData[2];    

    if (anoFim<1900 || anoFim>2100) {
        return;
    }

    if (data_InicioInput > data_FimInput) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('A Data Final não pode ser anterior à Data Inicial.');
        document.getElementById("data_final").focus();
        document.getElementById("data_final").style.borderColor = "red";
        return;
    }

    const dataFim = new Date(data_FimInput);
    const hoje = new Date();

    if (dataFim > hoje) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('A Data Final não pode ser uma data futura.');
        document.getElementById("data_final").focus();
        document.getElementById("data_final").style.borderColor = "red";
       return;
    }
}

function consultar_primeiro_filtro() {
    var local = $("#codigo_local").val();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var descricao_lote = $("#descricao_lote").val();
    var pasto = $("#codigo_pasto").val();
    var produto = $("#codigo_produto").val();

    if (pasto==null || pasto=='000000000') {
        pasto=[''];
    }

    if (descricao_lote==null) {
        descricao_lote=[''];
    }

    if (data_inicial > data_final) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Data Inicial e Final corretamente!');
        return;
    }

    if (data_inicial=='' && descricao_lote==null) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Período e ou os Lotes!');
        return;
    }


    if (local == '000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Fazenda!');
        return;
    }

    var options = $("#codigo_local option:selected");
    var codigo_local_filtro = '';

    $(options).each(function () {
        var desc = $(this).bind("#codigo_local").text();
        codigo_local_filtro = "Fazenda: " + desc.trim();
    });

    var options = $("#descricao_lote option:selected");
    var descricao_lote_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#descricao_lote").text();
        descricao_lote_filtro.push(desc.trim());
    });

    if (descricao_lote_filtro=='') {
        descricao_lote_filtro = '->Lote:Todos';
    }
    else {
        descricao_lote_filtro = '->Lote:' + descricao_lote_filtro;
    }

    if (data_inicial!= '') {
        var data_ini = data_inicial.split("-");
        var dia_ini = data_ini[2];
        var mes_ini = data_ini[1];
        var ano_ini = data_ini[0];

        var data_fim = data_final.split("-");
        var dia_fim = data_fim[2];
        var mes_fim = data_fim[1];
        var ano_fim = data_fim[0];
        periodo =
            "->Período: de " +
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
    }
    else {
        periodo = '';
    }

    var descricao_filtro =
        codigo_local_filtro +
        periodo +
        descricao_lote_filtro;

    $(".primeiro_filtro").hide();
    $(".filtros_consulta").show();
    $(".segundo_filtro").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".descricao_filtro").html(descricao_filtro);
    $('.voltar').show();

    $("#aguardar").modal('show');

    $.post("form_lista_nutricao_primeiro_filtro.php", {local:local, data_inicial:data_inicial, data_final:data_final, descricao_lote:descricao_lote, pasto:pasto, produto:produto},
        function(valor){ 

        $("div[id=lista_nutricao]").html(valor);
        $("#aguardar").modal('hide');

        var local = $("#codigo_local").val();
        var data_inicial = $("#data_inicial").val();
        var data_final = $("#data_final").val();
    });

    return;
}

function consultar_segundo_filtro() {
    var local = $("#codigo_local").val();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var descricao_lote = $("#descricao_lote").val();
    var pasto = $("#codigo_pasto").val();
    var produto = $("#codigo_produto").val();

    if (descricao_lote==null) {
        descricao_lote=[''];
    }

    if (pasto==null || pasto=='000000000') {
        pasto=[''];
    }

    if (local == '000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Fazenda!');
        return;
    }

    var options = $("#codigo_local option:selected");
    var codigo_local_filtro = '';

    $(options).each(function () {
        var desc = $(this).bind("#codigo_local").text();
        codigo_local_filtro = "Fazenda: " + desc.trim();
    });

    var options = $("#descricao_lote option:selected");
    var descricao_lote_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#descricao_lote").text();
        descricao_lote_filtro.push(desc.trim());
    });

    if (descricao_lote_filtro=='') {
        descricao_lote_filtro = '->Lote:Todos';
    }
    else {
        descricao_lote_filtro = '->Lote:' + descricao_lote_filtro;
    }

    var options = $("#codigo_pasto option:selected");
    var codigo_pasto_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_pasto").text();
        codigo_pasto_filtro.push(desc.trim());
    });

    if (codigo_pasto_filtro=='') {
        codigo_pasto_filtro = '->Pasto:Todos';
    }
    else {
        codigo_pasto_filtro = "->Pasto:" + codigo_pasto_filtro;
    }

    var options = $("#codigo_produto option:selected");
    var codigo_produto_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_produto").text();
        codigo_produto_filtro.push(desc.trim());
    });

    if (codigo_produto_filtro=='') {
        codigo_produto_filtro = '->Produto:Todos';
    }
    else {
        codigo_produto_filtro = "->Produto:" + codigo_produto_filtro;
    }

    if (data_inicial!= '') {
        var data_ini = data_inicial.split("-");
        var dia_ini = data_ini[2];
        var mes_ini = data_ini[1];
        var ano_ini = data_ini[0];

        var data_fim = data_final.split("-");
        var dia_fim = data_fim[2];
        var mes_fim = data_fim[1];
        var ano_fim = data_fim[0];
        periodo =
            "->Período: de " +
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
    }
    else {
        periodo = '';
    }

    var descricao_filtro =
        codigo_local_filtro +
        periodo + 
        descricao_lote_filtro +
        codigo_pasto_filtro + 
        codigo_produto_filtro;

    $(".descricao_filtro").html(descricao_filtro);

    $("#aguardar").modal('show');

    $.post("form_lista_nutricao_segundo_filtro.php", {local:local, data_inicial:data_inicial, data_final:data_final, pasto:pasto, produto:produto, descricao_lote:descricao_lote},
        function(valor){ 

        $("div[id=lista_nutricao]").html(valor);
        $("#aguardar").modal('hide');
    });

    return;
}


function exibe_mais_filtros() {
    $(".primeiro_filtro").show();
    $(".segundo_filtro").hide();
    $(".filtro_relatorio").show();
    $(".mais_filtros").hide();
    $(".menos_filtros").show();
    $(".lista_contas").hide();
    $('.voltar').hide();
}

function exibe_menos_filtros() {
    $(".primeiro_filtro").hide();
    $(".segundo_filtro").show();
    $(".filtro_relatorio").hide();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".lista_contas").show();
    $('.voltar').show();
}

/*function limpar_selecao_pasto() {
    $('#codigo_pasto').val(''); // Ou $('#meuSelect').val([]);
    $('#codigo_pasto').selectpicker('refresh');

    const linkElement = document.querySelector('.informacao');
    if (linkElement) {
        linkElement.style.color = 'darkgray';
    }
}

function limpar_selecao_lote() {
    $('#descricao_lote').val(''); // Ou $('#meuSelect').val([]);
    $('#descricao_lote').selectpicker('refresh');

    const linkElement = document.querySelector('.informacao');
    if (linkElement) {
        linkElement.style.color = 'darkgray';
    }
}

function limpar_selecao_produto() {
    $('#codigo_produto').val(''); // Ou $('#meuSelect').val([]);
    $('#codigo_produto').selectpicker('refresh');

    const linkElement = document.querySelector('.informacao');
    if (linkElement) {
        linkElement.style.color = 'darkgray';
    }
}*/

$(document).ready(function() {
    
    // =================================================================================
    // 1. CONFIGURAÇÃO GLOBAL (Define o texto de "Limpar Seleção" para todos)
    //    Isso elimina a necessidade do atributo data-deselect-all-text no HTML.
    // =================================================================================
    $.fn.selectpicker.defaults = {
        deselectAllText: 'Limpar Seleção',
        actionsBox: true, // Garante que a caixa de ações esteja habilitada
        // Não vamos definir selectAllText aqui para garantir que o elemento
        // seja criado e possamos removê-lo de forma explícita na etapa 3.
    };
    
    // =================================================================================
    // 2. LÓGICA DE VISIBILIDADE E REMOÇÃO (Aplicada a cada selectpicker)
    // =================================================================================
    $('.selectpicker').each(function() {
        
        const $selectElement = $(this);
        
        // Inicializa o selectpicker (usa as defaults acima)
        $selectElement.selectpicker();
        
        // Localiza a barra de ações gerada pelo plugin (onde está o botão Limpar Seleção)
        const $dropdownMenu = $selectElement.closest('.bootstrap-select').find('.dropdown-menu');
        const $actionsBox = $dropdownMenu.find('.bs-actionsbox');

        // Se a barra de ações não for encontrada, paramos para este elemento
        if (!$actionsBox.length) {
            return; 
        }

        // NOVO: Aplica o alinhamento à direita para a barra de ações
        $actionsBox.css('text-align', 'right');
        
        // -----------------------------------------------------------
        // 3. REMOÇÃO FORÇADA do "Selecionar Todos"
        // Esta linha é mantida, pois garante a remoção do botão indesejado,
        // limpando o espaço visual.
        // -----------------------------------------------------------
        $actionsBox.find('.bs-select-all').remove();
        
        // -----------------------------------------------------------
        // 4. FUNÇÃO DE VISIBILIDADE (Mostrar/Ocultar o container da barra de ações)
        // -----------------------------------------------------------
        function atualizarVisibilidadeActionsBox() {
            // Pega o array de valores selecionados
            const selecoes = $selectElement.val(); 
            
            // Se há seleções, mostra a barra de ações; caso contrário, esconde.
            if (selecoes && selecoes.length > 0) {
                $actionsBox.show(); 
            } else {
                $actionsBox.hide(); 
            }
        }
        
        // 5. Executa na inicialização (Estado inicial do formulário)
        atualizarVisibilidadeActionsBox();

        // 6. Monitore o evento de mudança
        $selectElement.on('changed.bs.select', atualizarVisibilidadeActionsBox);
    });
});

$(document).ready(function(){
    var table = $('#tabela_nutricao').DataTable({
        responsive: true,
        paging:   false,
        ordering: true,
        info:     true,
        order: [[ 0, "desc" ], [ 1, "desc" ] ],
        language: {
        sSearch: "Busca:",
        zeroRecords: "Nada encontrado",
        info: "Registros encontrados: _END_ ",
        infoEmpty: "Nenhum registro disponível",
        infoFiltered: "(filtrado de _MAX_ registros no total)",
        },
        "aoColumns": [
            null,
            { "sType": "date-br" },
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        ],

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function() {
            $('table.dataTable').css("width", "100%");
          }
    });

    var table = $("#tabela_nutricao_periodo").DataTable({
        responsive: true,
        paging:   false,
        ordering: true,
        info:     true,
        order: [[ 1, "asc" ]],
        language: {
        sSearch: "Busca:",
        zeroRecords: "Nada encontrado",
        info: "Registros encontrados: _END_ ",
        infoEmpty: "Nenhum registro disponível",
        infoFiltered: "(filtrado de _MAX_ registros no total)",
        },
        "dom": "<\'row\'<\'col-lg-6 col-md-6 col-sm-6\'i><\'col-lg-6 col-md-6 col-sm-6\'f>>",
        initComplete: function() {
            $("table.dataTable").css("width", "100%");
          }
    });

    var table = $('#tabela_nutricao_lote').DataTable({
        responsive: true,
        paging:   false,
        ordering: true,
        info:     true,
        order: [[ 0, "desc" ], [ 1, "desc" ] ],
        language: {
        sSearch: "Busca:",
        zeroRecords: "Nada encontrado",
        info: "Registros encontrados: _END_ ",
        infoEmpty: "Nenhum registro disponível",
        infoFiltered: "(filtrado de _MAX_ registros no total)",
        },
        "aoColumns": [
            null,
            { "sType": "date-br" },
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        ],

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function() {
            $('table.dataTable').css("width", "100%");
          }
    });

    // Adiciona o evento de clique ao botão
    $('#btnOrdenarPorId').on('click', function() {
        // Obtém a ordenação atual da primeira coluna
        var currentOrder = table.order();
        var newDirection = 'asc'; // Padrão: ascendente

        // Verifica se a primeira coluna já está sendo ordenada e inverte a direção
        if (currentOrder.length > 0 && currentOrder[0][0] === 0) {
            newDirectionId = (currentOrder[0][1] === 'asc') ? 'desc' : 'asc';
        }

        // Define a ordenação:
        // 1. Pela coluna 0 ($id_lote), com a direção que alterna a cada clique (newDirectionId)
        // 2. Pela coluna 1 ($data_nutricao_edi), SEMPRE em 'desc'
        table.order([
            [0, newDirectionId], // $id_lote: alterna asc/desc
            //[5, 'desc'],         // $codigo_produto: sempre desc
            [1, 'desc']         // $data_nutricao_edi: sempre desc
        ]).draw(); // Redesenha a tabela com a nova ordenação
    });    


    $('#codigo_local').change(function(event) {
        var local = $("#codigo_local").val();
        var data_inicial = $("#data_inicial").val();
        var data_final = $("#data_final").val();
        var tipo_periodo_lote = $("input[name='tipo_rel']:checked").val();

        if (tipo_periodo_lote==undefined) {
            tipo_periodo_lote='P';
        }

        if (local!='' && local!=0) {
            $.post("lista_lotes_nutricao.php", {local:local, data_inicial:data_inicial, data_final:data_final, tipo_rel:tipo_periodo_lote}, function(valor){
                $("select[name=descricao_lote]").html(valor);
                $('.selectpicker').selectpicker('refresh');            

                $("select[name=um_lote]").html(valor);
            });

            $.post("lista_pasto_nutricao.php", {local:local}, function(valor){
                $("select[name=codigo_pasto]").html(valor);
                $('.selectpicker').selectpicker('refresh');            
            });
        }
    });

    $('#data_inicial').change(function(event) {
        var local = $("#codigo_local").val();
        var data_inicial = $("#data_inicial").val();
        var data_final = $("#data_final").val();
        var tipo_periodo_lote = $("input[name='tipo_rel']:checked").val();

        if (tipo_periodo_lote==undefined) {
            tipo_periodo_lote='P';
        }

        $.post("lista_lotes_nutricao.php", {local:local, data_inicial:data_inicial, data_final:data_final, tipo_rel:tipo_periodo_lote}, function(valor){
            $("select[name=descricao_lote]").html(valor);
            $('.selectpicker').selectpicker('refresh');            

            $("select[name=um_lote]").html(valor);
        });
    });

    $('#data_final').change(function(event) {
        var local = $("#codigo_local").val();
        var data_inicial = $("#data_inicial").val();
        var data_final = $("#data_final").val();
        var tipo_periodo_lote = $("input[name='tipo_rel']:checked").val();

        if (tipo_periodo_lote==undefined) {
            tipo_periodo_lote='P';
        }

        $.post("lista_lotes_nutricao.php", {local:local, data_inicial:data_inicial, data_final:data_final, tipo_rel:tipo_periodo_lote}, function(valor){
            $("select[name=descricao_lote]").html(valor);
            $('.selectpicker').selectpicker('refresh');            

            $("select[name=um_lote]").html(valor);
        });
    });

    $('#data_encerramento').change(function(event) {
        $('.dias_encerramento').html(''); // Limpa a mensagem anterior
        $('#dias_nutricao').val('');

        const dataEncerramentoVal = $('#data_encerramento').val();
        const ultimaNutricaoVal = $('#ultima_nutricao').val();
        const partesDaData = dataEncerramentoVal.split('-');
        const anoencerramento = partesDaData[0];    

        if (anoencerramento<1900 || anoencerramento>2100) {
            return;
        }
        else {
            // 4. Pega a data atual no formato AAAA-MM-DD (para comparação)
            const dataAtualObj = new Date();
            const anoAtual = dataAtualObj.getFullYear();
            const mesAtual = (dataAtualObj.getMonth() + 1).toString().padStart(2, '0');
            const diaAtual = dataAtualObj.getDate().toString().padStart(2, '0');
            const data_atual_string = `${anoAtual}-${mesAtual}-${diaAtual}`;

            // 5. Converte as datas de input e a data atual para objetos Date
            // É essencial que os inputs retornem no formato YYYY-MM-DD para new Date() funcionar consistentemente.
            const d1 = new Date(dataEncerramentoVal); // data_encerramento
            const d2 = new Date(ultimaNutricaoVal);   // ultima_nutricao
            const data_atual_obj = new Date(data_atual_string); // Data atual como objeto Date

            // Verifica se as datas parseadas são válidas (evita 'Invalid Date' de inputs mal formatados ou navegador)
            if (isNaN(d1.getTime()) || isNaN(d2.getTime())) {
                $('.dias_encerramento').html('<span style="color: red;">Formato de data inválido. Use AAAA-MM-DD.</span>');
                $('#dias_nutricao').val('');
                return;
            }

            // 6. Limpa os componentes de hora para comparar apenas as datas (DIA, MÊS, ANO)
            d1.setHours(0, 0, 0, 0);
            d2.setHours(0, 0, 0, 0);
            data_atual_obj.setHours(0, 0, 0, 0);

            // 7. Aplica as regras de validação de datas
            if (d1 < d2) {
                $('.dias_encerramento').html('<span style="color: red;">A Data de Encerramento não pode ser anterior à Última Nutrição.</span>');
                $('#dias_nutricao').val('');
                return;
            }

            // Conforme a data atual é 2025-07-11
            if (d1 > data_atual_obj) {
                $('.dias_encerramento').html('<span style="color: red;">A Data de Encerramento não pode ser futura em relação à data atual.</span>');
                $('#dias_nutricao').val('');
                return;
            }

            // 8. Se todas as validações passarem, calcula a diferença de dias
            const diferencaEmMs = Math.abs(d1.getTime() - d2.getTime());
            const milissegundosPorDia = 1000 * 60 * 60 * 24;

            let dias = Math.ceil(diferencaEmMs / milissegundosPorDia);

            if (dias==0) {
                dias=1;
            }

            $('#dias_nutricao').val(dias);
            $('.dias_encerramento').html(`Encerramento ${dias} dia(s) após a última distribuição`);
        }
    });

    $('#codigo_local_filtro').change(function(event) {
        var local = $("#codigo_local_filtro").val();

        if (local==null || local=='000000000') {
            local=[''];
        }

        $.post("lista_pasto_rel.php", {local:local}, function(valor){
            $("select[name=codigo_pasto_filtro]").html(valor);
            $('.selectpicker').selectpicker('refresh');            
        });
    });

    $('#codigo_local_distribuir').change(function(event) {
        var local = $("#codigo_local_distribuir").val();

        $.post("lista_pasto.php", {local:local}, function(valor){
            $("select[name=codigo_pasto_distribuir]").html(valor);
        });
    });

    $('#data_final').click(function(event) {
        document.getElementById("data_final").style.borderColor = "";
    });

    $('.tipo_rel').click(function(event) {
        var tipo_periodo_lote = $("input[name='tipo_rel']:checked").val();

        if (tipo_periodo_lote=='P') {
            $('.descricao_lote').show();
            $('.um_lote').hide();
            $('.label_data_inicial').html('* Data Inicial');
            $('.label_data_final').html('* Data Final');
        }
        else {
            $('.descricao_lote').hide();
            $('.um_lote').show();
            $('.label_data_inicial').html('Data Inicial');
            $('.label_data_final').html('Data Final');
        }

        $("#codigo_local").val('000000000');
        $("#data_inicial").val('');
        $("#data_final").val('');
        $('#codigo_pasto').val(''); 
        $('#codigo_pasto').selectpicker('refresh');
        $('#codigo_produto').val(''); 
        $('#codigo_produto').selectpicker('refresh');
        $("select[name=descricao_lote]").html('');
        $('.selectpicker').selectpicker('refresh');            
        $("select[name=um_lote]").html('');
    });

});

function finalizar_sair(){
    location.href= "form_movimentacao_animais.php";
}

function relatorio_consumo_nutricao(){
    location.href= "form_rel_consumo_nutricao.php?tipo=2";
}

function excluir_nutricao($array_nutricao, opcao) {
    array_nutricao = $array_nutricao.split('|');
    $("#id_nutricao").val(array_nutricao[0]);
    $("#tipo_gravacao").val(2);
    $("#id_local").val(array_nutricao[1]);
    $("#id_produto").val(array_nutricao[16]);
    $("#quantidade").val(array_nutricao[7]);
    $("#ultima_nutricao").val(array_nutricao[18]);

    const dataUltima = array_nutricao[18];
    const partes = dataUltima.split('-');
    const ano = partes[0];
    const mes = partes[1];
    const dia = partes[2];
    const data_ultima_string = `${dia}/${mes}/${ano}`;

    $(".desc_lote").html(array_nutricao[10]+' - '+Number(array_nutricao[9])+' Animais');
    $(".desc_ultima").html(data_ultima_string);
    $(".desc_pasto").html(array_nutricao[4]);
    $(".desc_produto").html(array_nutricao[5]+ ' - '+array_nutricao[7]+''+array_nutricao[6]);

    $('#excluir_nutricao').modal('show');
}

function encerrar_nutricao($array_nutricao) {
    var array_nutricao = $array_nutricao.split('|');
    $("#id_nutricao").val(array_nutricao[0]);
    $("#tipo_gravacao").val(1);

    $("#id_local").val(array_nutricao[1]);
    $("#id_produto").val(array_nutricao[16]);
    $("#quantidade").val(array_nutricao[7]);
    $("#qtd_animais").val(array_nutricao[9]);
    $("#ultima_nutricao").val(array_nutricao[18]);
    $("#data_encerramento").val('');
    $("#dias_nutricao").val('');

    const dataUltima = array_nutricao[18];
    const partes = dataUltima.split('-');
    const ano = partes[0];
    const mes = partes[1];
    const dia = partes[2];
    const data_ultima_string = `${dia}/${mes}/${ano}`;

    $(".desc_lote").html(array_nutricao[10]+' - '+Number(array_nutricao[9])+' Animais');
    $(".desc_ultima").html(data_ultima_string);
    $(".desc_pasto").html(array_nutricao[4]);
    $(".desc_produto").html(array_nutricao[5]+ ' - '+array_nutricao[7]+''+array_nutricao[6]);
    $(".dias_encerramento").html('');

    $('#encerrar_nutricao').modal('show');
}

function gravar_nutricao() {
    var tipo_gravacao = $("#tipo_gravacao").val();
    var id_nutricao = $("#id_nutricao").val();
    var id_produto = $("#id_produto").val();
    var id_local = $("#id_local").val();
    var quantidade = $("#quantidade").val();

    if (tipo_gravacao==1) {
        var data_encerramento = $("#data_encerramento").val();
        var qtd_animais = $("#qtd_animais").val();
        var ultima_nutricao = $("#ultima_nutricao").val();
        var dias_nutricao = $("#dias_nutricao").val();
    }
    else {
        var data_encerramento = '';
        var qtd_animais = '';
        var ultima_nutricao = '';
        var dias_nutricao = '';
    }

    $.ajax({
        type: "POST",
        url: 'gravar_nutricao_alterar.php',
        data: {
            'tipo_gravacao': tipo_gravacao,
            'id_nutricao': id_nutricao,
            'id_produto': id_produto,
            'id_local': id_local,
            'quantidade': quantidade,
            'data_encerramento': data_encerramento,
            'qtd_animais': qtd_animais,
            'ultima_nutricao': ultima_nutricao,
            'dias_nutricao': dias_nutricao
            },
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $('#excluir_nutricao').modal('hide');
                $('#encerrar_nutricao').modal('hide');
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        }
    });

    /*if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_nutricao').serialize();
        }
    }
    else {
        var dados = $('#form_gravar_nutricao').serialize();
        $.ajax({
            type: "POST",
            url: 'gravar_nutricao_alterar.php',
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
    }*/
}


function digita_valor(){
    $('#quantidade').bind('keypress',mask.money);
    $('#qtd_media').bind('keypress',mask.money);
}

function exibe_quantidade(){
    var quantidade = $("#quantidade").val();
    if (verifica_virgula(quantidade)==',') {
        quantidade = replace_valor(quantidade);
    }

    $("#quantidade").val(formatMoney(quantidade));

    var qtd_animais = $("#qtd_cabecas").val();
    var quantidade = replace_valor($("#quantidade").val());

    var qtd_media = quantidade / qtd_animais;
    $("#qtd_media").val(formatMoney(qtd_media));

}

function exibe_qtd_media(){
    var qtd_media = $("#qtd_media").val();
    if (verifica_virgula(qtd_media)==',') {
        qtd_media = replace_valor(qtd_media);
    }

    $("#qtd_media").val(formatMoney(qtd_media));
}

// RELATORIOS
function validar_datas_consumo_nutricao() {
    var tipo_periodo_lote = $("input[name='tipo_rel']:checked").val();

    var data_InicioInput = $('#data_inicial').val();
    var partesDaData =data_InicioInput.split('-');
    var anoInicio = partesDaData[0];    
    var mesInicio = partesDaData[1];    
    var diaInicio = partesDaData[2];    

    var data_FimInput = $('#data_final').val();
    var partesDaData =data_FimInput.split('-');
    var anoFim = partesDaData[0];    
    var mesFim = partesDaData[1];    
    var diaFim = partesDaData[2];    

    if (anoFim<1900 || anoFim>2100) {
        return;
    }

    if (data_InicioInput > data_FimInput) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('A Data Final não pode ser anterior à Data Inicial.');
        document.getElementById("data_final").focus();
        document.getElementById("data_final").style.borderColor = "red";
        return;
    }

    if (tipo_periodo_lote=='P') {
        if (mesInicio != mesFim || anoInicio !== anoFim) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('As datas de Inicial e Final devem pertencer ao mesmo mês e ano.');
            document.getElementById("data_final").focus();
            document.getElementById("data_final").style.borderColor = "red";
            return;
        }
    }

    const dataFim = new Date(data_FimInput);
    const hoje = new Date();

    if (dataFim > hoje) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('A Data Final não pode ser uma data futura.');
        document.getElementById("data_final").focus();
        document.getElementById("data_final").style.borderColor = "red";
       return;
    }
}

function listar_consumo_nutricao(opcao){
    var local = $("#codigo_local").val();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var pasto = $("#codigo_pasto").val();
    var produto = $("#codigo_produto").val();
    var tipo_periodo_lote = $("input[name='tipo_rel']:checked").val();

    if (tipo_periodo_lote=='P') {
        if (data_inicial=='' && data_inicial=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe as Datas Inicial e Final!');
            return;
        }

        var lote = $("#descricao_lote").val();

        if (lote==null) {
            var array_lote= new Array();
        }
        else {
            var array_lote = new Array();
            var valor = new Array();

            for (i = 0; i <= lote.length; i++) {
                valor[i]=lote[i];
            }
            var array_lote=valor.join(",");
        }

        var options = $("#descricao_lote option:selected");
        var descricao_lote_filtro = [];

        $(options).each(function () {
            var desc = $(this).bind("#descricao_lote").text();
            descricao_lote_filtro.push(desc.trim());
        });

        if (descricao_lote_filtro=='') {
            descricao_lote_filtro = '->Lote:Todos';
        }
        else {
            descricao_lote_filtro = '->Lote:' + descricao_lote_filtro;
        }
    }
    else {
        var array_lote = $("#um_lote").val();

        if (array_lote == '00000000') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe o Lote!');
            return;
        }

        var options = $("#um_lote option:selected");
        var descricao_lote_filtro = [];

        $(options).each(function () {
            var desc = $(this).bind("#um_lote").text();
            descricao_lote_filtro.push(desc.trim());
        });

        descricao_lote_filtro = '->Lote:' + descricao_lote_filtro;
    }

    if (pasto==null || pasto=='000000000') {
        var array_pasto= new Array();
    }
    else {
        var array_pasto = new Array();
        var valor = new Array();

        for (i = 0; i <= pasto.length; i++) {
            valor[i]=pasto[i];
        }
        var array_pasto=valor.join(",");
    }

    if (produto==null || produto=='000000000') {
        var array_produto= new Array();
    }
    else {
        var array_produto = new Array();
        var valor = new Array();

        for (i = 0; i <= produto.length; i++) {
            valor[i]=produto[i];
        }
        var array_produto=valor.join(",");
    }

    if (data_inicial > data_final) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Data Inicial e Final corretamente!');
        return;
    }

    if (data_inicial=='' && descricao_lote==null) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Período e ou os Lotes!');
        return;
    }

    if (local == '000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Fazenda!');
        return;
    }

    var options = $("#codigo_local option:selected");
    var codigo_local_filtro = '';

    $(options).each(function () {
        var desc = $(this).bind("#codigo_local").text();
        codigo_local_filtro = "Fazenda: " + desc.trim();
    });


    var options = $("#codigo_pasto option:selected");
    var codigo_pasto_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_pasto").text();
        codigo_pasto_filtro.push(desc.trim());
    });

    if (codigo_pasto_filtro=='') {
        codigo_pasto_filtro = '->Pasto:Todos';
    }
    else {
        codigo_pasto_filtro = "->Pasto:" + codigo_pasto_filtro;
    }

    var options = $("#codigo_produto option:selected");
    var codigo_produto_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_produto").text();
        codigo_produto_filtro.push(desc.trim());
    });

    if (codigo_produto_filtro=='') {
        codigo_produto_filtro = '->Produto:Todos';
    }
    else {
        codigo_produto_filtro = "->Produto:" + codigo_produto_filtro;
    }

    if (data_inicial!= '') {
        var data_ini = data_inicial.split("-");
        var dia_ini = data_ini[2];
        var mes_ini = data_ini[1];
        var ano_ini = data_ini[0];

        var data_fim = data_final.split("-");
        var dia_fim = data_fim[2];
        var mes_fim = data_fim[1];
        var ano_fim = data_fim[0];
        periodo =
            "->Período: de " +
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
    }
    else {
        periodo = '';
    }

    if (tipo_periodo_lote=='P') {
        var tipo_rel_filtro = '->Tipo Relatório: Por Período';
    }
    else {
        var tipo_rel_filtro = '->Tipo Relatório: Por Lote';
    }
    var descricao_filtro =
        codigo_local_filtro +
        periodo + 
        descricao_lote_filtro +
        codigo_pasto_filtro + 
        codigo_produto_filtro +
        tipo_rel_filtro;

    $("#filtro_aplicado").html('Filtros: ' + descricao_filtro);
    $('.filtro_aplicado').show();

    var tipo_relatorio = $("#tipo_relatorio").val();

    $("#aguardar").modal();
    
    if (opcao=='1') {
        location.href='form_lista_consumo_nutricao_rel.php?descricao_filtro=' + descricao_filtro + 
        '&tipo_relatorio=' + tipo_relatorio + 
        '&local=' + local + 
        '&data_inicial=' + data_inicial + 
        '&data_final=' + data_final + 
        '&lote=' + array_lote + 
        '&pasto=' + array_pasto + 
        '&produto=' + array_produto + 
        '&tipo_periodo_lote=' + tipo_periodo_lote;
    }
    else {
        location.href='rel_lista_consumo_nutricao_excel.php?descricao_filtro=' + descricao_filtro + 
        '&tipo_relatorio=' + tipo_relatorio + 
        '&local=' + local + 
        '&data_inicial=' + data_inicial + 
        '&data_final=' + data_final + 
        '&lote=' + array_lote + 
        '&pasto=' + array_pasto + 
        '&produto=' + array_produto + 
        '&tipo_periodo_lote=' + tipo_periodo_lote;

        tout = setTimeout('limpar_tela()', 5000);
    }
}

function lista_consumo_nutricao_excel(){
    var local = $("#codigo_local").val();
    var array_pasto = $("#codigo_pasto").val();
    var array_produto = $("#codigo_produto").val();
    var array_lote = $("#descricao_lote").val();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var descricao_filtro = $("#descricao_filtro").val();
    var tipo_relatorio = $("#tipo_relatorio").val();
    var tipo_periodo_lote = $("#tipo_periodo_lote").val();

    $("#aguardar").modal();

    location.href='rel_lista_consumo_nutricao_excel.php?descricao_filtro=' + descricao_filtro +
        '&tipo_relatorio=' + tipo_relatorio + 
        '&local=' + local + 
        '&data_inicial=' + data_inicial + 
        '&data_final=' + data_final + 
        '&lote=' + array_lote + 
        '&pasto=' + array_pasto + 
        '&produto=' + array_produto + 
        '&tipo_periodo_lote=' + tipo_periodo_lote;

    tout = setTimeout('limpar_tela()', 5000);

}

function limpar_tela(){
    $('#aguardar').modal('hide');
}

function voltar_relatorios() {
    var tipo_relatorio = $("#tipo_relatorio").val();

    if (tipo_relatorio==1) {
        location.href='form_relatorios_produtivos.php';
    }
    else {
        location.href='form_nutricao.php';
    }
}

function voltar_filtro() {
    var tipo_relatorio = $("#tipo_relatorio").val();
    location.href='form_rel_consumo_nutricao.php?tipo='+tipo_relatorio;
}


// FIM RELATORIO

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