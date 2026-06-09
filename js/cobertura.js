/**COBERTURA DE ANIMAIS*/
window.addEventListener("load", function(){
    if ($("input[name='tipo_cobertura']:checked").val()=='I') {
        $('.tipo').show();
        $('.opcao').hide();
        $('.local').show();
        $('.estacao_monta').show();
        $('.periodo').hide();
        $('.diagnostico').show();
        $('.previsao').show();
        $('.nascido').show();
    }
    else {
        $('.tipo').show();
        $('.opcao').show();
        $('.local').show();
        $('.estacao_monta').hide();
        $('.periodo').hide();
        $('.diagnostico').hide();
        $('.previsao').hide();
        $('.nascido').hide();
    }

    $.post("lista_local.php", {tipo:1}, function(valor){
        $("select[name=codigo_local]").html(valor);
        $("option[value=000000000]").html("...");

        var local_femeas_servidas = $('#local_request').val();
        var estacao_monta = $('#estacao_request').val();
        var id_estacao_monta = $('#codigo_estacao_request').val();
        var tipo_registro = $('#tipo_registro').val();
        var diagnostico = $('#diagnostico_request').val();

        if (local_femeas_servidas!=0 && local_femeas_servidas!=undefined){
            $("#codigo_local").val(local_femeas_servidas);
            $(".data_estacao_monta").text(estacao_monta);

            if (diagnostico=='N') {
                var estacao_monta = $('#codigo_estacao_request').val();
            }
            else {
                var estacao_monta = $("#estacao_monta_anterior").val();
            }

            $.post("lista_estacao_monta.php", {local:local_femeas_servidas, estacao_monta: estacao_monta}, function(valor){
                $("select[name=estacao_monta]").html(valor);
                //$("select[name=estacao_monta_servidas]").html(valor);

                if (tipo_registro=='C') {
                    listar_coberturas($("#codigo_local").val());
                }
                else {
                   popular_select_estacao_monta($("#codigo_local").val());
                    if (diagnostico=='N') {
                        listar_femeas_servidas_estacao(diagnostico);
                    }
                }
            });
        } 
        else if($('#codigo_local option').length == 1){
            var local = $('#codigo_local option').val();

            $("#codigo_local").val(local);

            if (diagnostico=='N') {
                var estacao_monta = $('#codigo_estacao_request').val();
            }
            else {
                var estacao_monta = $("#estacao_monta_anterior").val();
            }

            $.post("lista_estacao_monta.php", {local:local, estacao_monta: estacao_monta}, function(valor){
                $("select[name=estacao_monta]").html(valor);
                //$("select[name=estacao_monta_servidas]").html(valor);

                if (tipo_registro=='C') {
                    listar_coberturas($("#codigo_local").val());
                }
                else {
                    popular_select_estacao_monta($("#codigo_local").val());
                }
            });
        }
    });

    /*var lista_cobertura_automatico = $("#lista_cobertura_automatico").val();

    if (lista_cobertura_automatico=="S") {
        consultar_cobertura();
    }*/
});

$(window).unload(function (e){
    alert(checarJanela());
});

function agenda_protocolo(){
    var local = $("#codigo_local").val();
    location.href= "form_agenda_protocolos.php?local=" + local;   
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

function informacoes_uso() {
    $("#ajuda").modal();
}

function editar_novo_grupo_estacao($codigo_grupo, $descricao) {
    $(".novo_grupo").show();
    $("#codigo_novo_grupo").val($codigo_grupo);
    $("#descricao_novo_grupo").val($descricao);
    $("#codigo_estacao_novo_grupo").val('');
    $("#codigo_local_novo_grupo").val('');
    $("#tipo_gravacao_novo_grupo").val(1);

    document.getElementById("codigo_novo_grupo").readOnly = true;
    $('#descricao_novo_grupo').focus();
}

function excluir_novo_grupo_estacao($codigo_grupo, $descricao) {
    $("#codigo_novo_grupo").val($codigo_grupo);
    $("#descricao_novo_grupo").val($descricao);
    $("#codigo_estacao_novo_grupo").val('');
    $("#codigo_local_novo_grupo").val('');
    $("#tipo_gravacao_novo_grupo").val(2);

    if (window.confirm("Confirma excluir esse grupo? " + $descricao)) {  
        gravar_novo_grupo(); 
    }  
}

function modal_novo_grupo_estacao() {
    $(".novo_grupo").hide();

    var local = $("#codigo_local").val();
    var id_parametro_estacao = $("#estacao_monta").val();

    var nome_fazenda = ""; // 1. Inicialize a variável aqui para evitar o erro de undefined

    // Verifique se o elemento existe antes de tentar ler as propriedades
    var selectElement = document.getElementById('codigo_local');

    if (selectElement && selectElement.selectedIndex !== -1) {
        nome_fazenda = selectElement.options[selectElement.selectedIndex].text;
    }

    $(".nome_fazenda").text(nome_fazenda);

    $("#modal_diagnostico_negativo").modal('hide');
    $('#modal_novo_grupo_estacao').modal('show');

    $.post("form_lista_grupos_estacao.php", {local:local, id_parametro_estacao:id_parametro_estacao, flag:2},
        function(valor){ 

        $("div[id=lista_grupos_estacao]").html(valor); 

        //popular_select_grupo(local, id_parametro_estacao);
    });
}

/*function popular_select_grupo(local, id_parametro_estacao) {
    $.post("popular_grupo_lista_matrizes.php", {local:local, estacao_monta: id_parametro_estacao}, function(retorno){
        const grupo_select = document.getElementsByName("grupo_select");
        for (var i = 0; i < grupo_select.length; i++) {
            //if (grupo_select[i].checked == true) {
                grupo_selecionado = grupo_select[i].value;
            //}

            grupo_select[i].innerHTML = retorno;
            grupo_select[i].value = grupo_selecionado;
        }
    });
}*/

function gravar_novo_grupo(){
    var codigo_grupo = $("#codigo_novo_grupo").val();
    var descricao_grupo = $("#descricao_novo_grupo").val();
    var local = $("#codigo_local").val();
    var id_parametro_estacao = $("#estacao_monta").val();

    if (codigo_grupo=='') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Código.');
        return;
    }

    if (descricao_grupo=='') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Descrição.');
        return;
    }

    $("#codigo_local_novo_grupo").val(local);
    $("#codigo_estacao_novo_grupo").val(id_parametro_estacao);

    var dados = $('#form_grupo_novo_estacao').serialize();

    $(".gravar_novo_grupo").attr("disabled", true);

    $.ajax({
        type: "POST",
        url: 'gravar_grupo_estacao_monta.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $(".gravar_novo_grupo").attr("disabled", false);
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $(".gravar_novo_grupo").attr("disabled", false);
                $("#mensagem_retorno_novo_grupo").modal();
                $("#mensagem_retorno_novo_grupo .modal-body").html(data.message);
            }
        }
    });
}

function servidas() {
    var local = $("#codigo_local").val();
    var estacao_monta = $(".data_estacao_monta").text();
    var id_estacao_monta = $("#codigo_estacao_request").val();

    location.href= "form_cobertura_animais_diagnostico.php?local=" + local + "&estacao=" + estacao_monta + "&id_estacao=" + id_estacao_monta;
}

function voltar_cobertura() {
    var local = $("#codigo_local").val();
    var estacao_monta = $(".data_estacao_monta").text();
    var id_estacao_monta = $("#codigo_estacao_request").val();

    location.href= "form_cobertura_animais.php?local=" + local+ "&estacao=" + estacao_monta + "&id_estacao=" + id_estacao_monta;
}

function checarJanela(){
    let needConfirm = false;
    $("#tabelaMatriz input:visible, #tabelaMatriz select").each(function(){
        switch(this.type){
            case "checkbox":
                if(this.checked){
                    needConfirm = true;
                    return;
                }
                break;
            case "select-one":
                if(this.value != "000000000"){
                    needConfirm = true;
                    return;
                }
                break;
            case "text":
                if(this.value != ""){
                    needConfirm = true;
                    return;
                }
                break;
            case "radio":
                if(this.checked){
                    needConfirm = true;
                    return;
                }
                break;
        }
    });

    if(needConfirm){
        return '';
    }
}

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

$(document).ready(function() {
    $('.btn-voltar-grupos').off('click').on('click', function() {
        // Fecha o segundo modal
        $('#modal_novo_grupo_estacao').modal('hide'); 

        var local = $("#codigo_local").val();
        var estacao = $("#estacao_monta").val();

        $.post("lista_grupo_estacao_monta.php", {local:local, estacao:estacao}, function(valor){
            $("select[name=grupo_nova_cobertura]").html(valor);
        });

        $("#modal_diagnostico_negativo").modal('show');
    });
    
    $("#btnAdicionar_grupo").click(function(){
        var local = $("#codigo_local").val();
        var id_parametro_estacao = $("#estacao_monta").val();

        $.post("ler_grupo_estacao_monta.php", {local:local, id_parametro_estacao:id_parametro_estacao},
            function(valor){ 

            var php = valor.split("<|>");

            if (php[1]==''){
                $("#proximo_grupo").val(1);
            }
            else {
                var proximo_grupo = php[1];
                proximo_grupo++;
                $("#proximo_grupo").val(proximo_grupo);
            }

            $(".novo_grupo").show();
            $("#codigo_novo_grupo").val($("#proximo_grupo").val());
            $("#descricao_novo_grupo").val('');
            $("#codigo_estacao_novo_grupo").val('');
            $("#codigo_local_novo_grupo").val('');
            $("#tipo_gravacao_novo_grupo").val(0);
            document.getElementById("codigo_novo_grupo").readOnly = false;
        });
    });

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
    var myTable;

    // Verifica se a DataTable já existe
    if ($.fn.DataTable.isDataTable("#tabela_femeas_servidas_monta")) { 
        $("#tabela_femeas_servidas_monta").DataTable().destroy();
    }

    myTable = $("#tabela_femeas_servidas_monta").DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        language: {
        sSearch: "Busca:",
        zeroRecords: "Nada encontrado",
        info: "Registros encontrados: _END_ ",
        infoEmpty: "Nenhum registro disponível",
        infoFiltered: "(filtrado de _MAX_ registros no total)",
        },
        "aoColumns": [
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            { "sType": "date-br" },
            { "sType": "date-br" },
            null,
            null,
            null
        ],

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function() {
            $('table.dataTable').css("width", "100%");
          }
    });

    $('#tabela_femeas_servidas').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "aoColumns": [
            null,
            null,
            null,
            null,
            null,
            null,
            { "sType": "date-br" },
            null,
            { "sType": "date-br" },
            null,
            null,
            null,
            null
        ],
        "language": {
            "sSearch": "Buscar na lista:",
            "zeroRecords": "Nada encontrado",
            "info": "Registros encontrados: _END_ ",
            "infoEmpty": "Nenhum registro disponível",
            "infoFiltered": "(filtrado de _MAX_ registros no total)",
            "decimal": ",",
            "thousands": ".",
        },
        order: [[ 1, "asc" ]],

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",

        initComplete: function(settings, json) {
            $('table.dataTable').css("width", "100%");
            if($("#diagnosticoT").val() == "N"){
                $('div.dataTables_info').html(`Registros encontrados: ${settings.aiDisplayMaster.length}`);
            }
        },
    });

    /*$('#tabela_cobertura').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": false,
        "info":     true,
        "language": {
            "sSearch": "Buscar na lista:",
            "zeroRecords": "Nada encontrado",
            "info": "Registros encontrados: _END_ ",
            "infoEmpty": "Nenhum registro disponível",
            "infoFiltered": "(filtrado de _MAX_ registros no total)",
            "decimal": ",",
            "thousands": ".",
        },

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });*/

    /*$("#estacao_monta_servidas").change(function(){
        $("div[id=lista_femeas_servidas]").html('');
    });*/

    $("#estacao_monta").change(function(){
        $("div[id=lista_femeas_servidas]").html('');
    });

    $("#previsao_parto_de_filtro").change(function(){
        $("div[id=lista_femeas_servidas]").html('');
    });

    $("#previsao_parto_ate_filtro").change(function(){
        $("div[id=lista_femeas_servidas]").html('');
    });

    $("#situacao_monta_servidas").change(function(){
        $("div[id=lista_femeas_servidas]").html('');
    });

    $("#codigo_local").change(function(){
        var qual_programa = $("#cobertura_programa").val();

        if (qual_programa=='S') {
            var local = $("#codigo_local").val();

            if (local==0) {
                $(".data_estacao_monta").text('');
                return;
            }

            $('.confirma').show();

            if ($("input[name='tipo_cobertura']:checked").val()=='I') {
                var estacao_monta = '';
                $.post("ler_parametro_estacao.php", {local:local, estacao_monta:estacao_monta}, function(valor){ 
                    var php = valor.split("<|>");

                    if (valor==''){
                        var select = $("#codigo_local").val();

                        if (select!=0) {
                            select = document.getElementById('codigo_local');
                            nome_fazenda = select.options[select.selectedIndex].text;
                        }

                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html('Não existe parâmetro atual para estação de monta: ' + nome_fazenda);
                        return;
                    }
                    else {
                        var partes_inicial = php[2].split("-");
                        var partes_final = php[3].split("-");
                        var periodo_estacao = 'Estação de monta: '+php[1]+' - '+partes_inicial[2]+'/'+partes_inicial[1]+'/'+partes_inicial[0]+
                                            ' até '+partes_final[2]+'/'+partes_final[1]+'/'+partes_final[0];

                        $(".data_estacao_monta").text(periodo_estacao);
                        $("#codigo_estacao_request").val(php[0]);
                    }
                });
            }
        }
    });

    $(".tipo_cobertura").change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });

    $("#grupo_nova_cobertura").change(function(){
        var novo_grupo = $("#grupo_nova_cobertura").val(); 
        
        if (novo_grupo=='000') {
            $('#novo_grupo').prop("checked", false);
        }     
        else {
            $('#novo_grupo').prop("checked", true);
        }  
    });


    $(".tipo_cobertura").change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });

    $('.nova_cobertura').click(function(){
        let opcao_nova_cobertura = $(`input[name='opcao_nova_cobertura']:checked`).val();

        if (opcao_nova_cobertura=='G') {
            $(".grupo_nova_cobertura").show();
            $('#grupo_nova_cobertura').focus();
            //const divElement = document.querySelector('.label_nova_cobertura');
            //divElement.classList.remove('form-group');
        }
        else {
            //const divElement = document.querySelector('.label_nova_cobertura');
            //divElement.classList.add('form-group');

            $("#grupo_nova_cobertura").val('000');
            //$(".grupo_nova_cobertura").hide();
        }
    });

    $('.tipo_diagnostico').click(function(){
        if ($("input[name='tipo_diagnostico']:checked").val()=='P') {
            $('.label_periodo_de').html('Período Prenhez (de)');
            $('.label_periodo_ate').text('Período Prenhez (até)');
            $('.previsao').show();
            $('.nascido').show();
        }
        else {
            $('.label_periodo_de').html('Período (de)');
            $('.label_periodo_ate').text('Período (até)');
            $('.previsao').hide();
            $('.nascido').hide();
            $('#previsao_parto_de_filtro').val('');
            $('#previsao_parto_ate_filtro').val('');
        }
    });

    $('.tipo_cobertura').click(function(){
        if ($("input[name='tipo_cobertura']:checked").val()=='I') {
            $('.opcao').hide();
            $('.local').show();
            $('.estacao_monta').show();
            $('.periodo').hide();
            $('.diagnostico').show();
            $('.previsao').show();
            $('.nascido').show();
            $('.confirmar').show();
            $('#codigo_local').val('000000000');
            $('#estacao_monta').val('000000000');
            $('.ou').text('');
        }
        else {
            $('.opcao').show();
            $('.local').hide();
            $('.estacao_monta').hide();
            $('.periodo').hide();
            $('.diagnostico').hide();
            $('.previsao').hide();
            $('.nascido').hide();
            $('.confirmar').hide();
            $('#C').prop("checked", false);
            $('#I').prop("checked", false);
            $('.ou').text('Ou');
        }
    });

    $('.opcao_diagnostico_monta').click(function(){
        if ($("input[name='opcao_diagnostico_monta']:checked").val()=='C') {
            $('.local').show();
            $('.estacao_monta').hide();
            $('.periodo').show();
            $('.diagnostico').show();
            $('.previsao').show();
            $('.nascido').show();
            $('.confirmar').show();

            if ($("input[name='tipo_diagnostico']:checked").val()=='P') {
                $('.label_periodo_de').html('Período Prenhez (de)');
                $('.label_periodo_ate').text('Período Prenhez (até)');
                $('.previsao').show();
                $('.nascido').show();
            }
            else {
                $('.label_periodo_de').html('Período (de)');
                $('.label_periodo_ate').text('Período (até)');
                $('.previsao').hide();
                $('.nascido').hide();
            }
        }
        else {
            $('.local').show();
            $('.estacao_monta').hide();
            $('.periodo').hide();
            $('.diagnostico').hide();
            $('.previsao').hide();
            $('.nascido').hide();
            $('#periodo_de').val('');
            $('#periodo_ate').val('');
            $('#previsao_parto_de_filtro').val('');
            $('#previsao_parto_ate_filtro').val('');
            $('.confirmar').show();
        }
    });

    $('#periodo_de').change(function(){
        $('#previsao_parto_de_filtro').val('');
        $('#previsao_parto_ate_filtro').val('');
    });

    $('#previsao_parto_de_filtro').change(function(){
        $('#periodo_de').val('');
        $('#periodo_ate').val('');
    });

    $('#periodo_ate').change(function(){
        $('.confirma').show();
    });
});

function listar_coberturas(value){
    $('.consultar').show();
    $('.filtros_consulta').hide();

    var estacao_monta = '';
    //$("select[name=estacao_monta_servidas]").html('');
    $("select[name=estacao_monta]").html('');

    $.post("lista_estacao_monta.php", {local:value, estacao_monta: estacao_monta}, function(valor){
        $("select[name=estacao_monta]").html(valor);
        //$("select[name=estacao_monta_servidas]").html(valor);

        /*let id_estacao = $("#estacao_monta").val();
        let codigo_alfa = $("#codigo_alfa").val();
        let codigo_numerico = $("#codigo_numerico").val();
        let checkBoxes = document.getElementsByName("tipo_cobertura");
        noCheckedBoxes = true;

        for (i = 0; i < checkBoxes.length; ++i) {
            if(checkBoxes[i].checked) {
                noCheckedBoxes = false;
            }
        }

        if (noCheckedBoxes) {
            alert ('Nenhum Tipo foi selecionado.');
            return;
        }

        var iatf = $('#I');
        var monta = $('#M');
        var te = $('#T');
        var array_tipo = new Array();
        var valor = new Array();
        var i = 0;

        if (monta.is(":checked")){
            valor[i]='M';
            var array_tipo=valor.join(",");
            i++;
        }

        if (iatf.is(":checked")){
            valor[i]='I';
            var array_tipo=valor.join(",");
            i++;
        }

        if (te.is(":checked")){
            valor[i]='T';
            var array_tipo=valor.join(",");
            i++;
        }

        if(value != 0){
            $("#aguardar").modal();

            $.post("form_lista_cobertura.php", {
                'local':value, 
                'estacao':id_estacao, 
                'tipo':array_tipo,
                'codigo_alfa': codigo_alfa,
                'codigo_numerico': codigo_numerico
                }, function(valor){ 
                $("div[id=lista_cobertura]").html(valor);
                $("#aguardar").modal('hide');
                select_protocolo();
            });
        }*/
    });
}

function listar_cobertura_estacao(value){
    $('.consultar').show();
    $('.filtros_consulta').hide();

    $("div[id=lista_cobertura]").html('');

    /*let local = $("#codigo_local").val();
    let codigo_alfa = $("#codigo_alfa").val();
    let codigo_numerico = $("#codigo_numerico").val();
    let checkBoxes = document.getElementsByName("tipo_cobertura");
    noCheckedBoxes = true;

    for (i = 0; i < checkBoxes.length; ++i) {
        if(checkBoxes[i].checked) {
            noCheckedBoxes = false;
        }
    }

    if (noCheckedBoxes) {
        alert ('Nenhum Tipo foi selecionado.');
        return;
    }

    var iatf = $('#I');
    var monta = $('#M');
    var te = $('#T');
    var array_tipo = new Array();
    var valor = new Array();
    var i = 0;

    if (monta.is(":checked")){
        valor[i]='M';
        var array_tipo=valor.join(",");
        i++;
    }

    if (iatf.is(":checked")){
        valor[i]='I';
        var array_tipo=valor.join(",");
        i++;
    }

    if (te.is(":checked")){
        valor[i]='T';
        var array_tipo=valor.join(",");
        i++;
    }

    if(value != 0){
        $("#aguardar").modal();

        $.post("form_lista_cobertura.php", {
            'local':local, 
            'estacao':value, 
            'tipo':array_tipo,
            'codigo_alfa': codigo_alfa,
            'codigo_numerico': codigo_numerico
            }, function(valor){ 
            $("div[id=lista_cobertura]").html(valor);
            $("#aguardar").modal('hide');
            select_protocolo();
        });
    }*/
}

function consultar_cobertura(){
    //$("div[id=lista_cobertura]").html('');

    let local = $("#codigo_local").val();
    let estacao = $("#estacao_monta").val();
    let codigo_alfa_numerico = $("#codigo_numerico").val();

    if (codigo_alfa_numerico!='') {
        $('.busca_consultar').hide();
        $('.busca_limpar').show();
    } 
    else {
        $('.busca_consultar').hide();
        $('.busca_limpar').hide();
    }

    if (local=='000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione a Fazenda.');
        return;
    }

    if (estacao=='null' || estacao=='000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione a Estação.');
        return;
    }

    var options = $("#codigo_local option:selected");
    var codigo_local_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_local").text();
        codigo_local_filtro.push(desc.trim());
    });

    codigo_local_filtro = "Fazenda: " + codigo_local_filtro + "->";

    var options = $("#estacao_monta option:selected");
    var estacao_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#estacao_monta").text();
        estacao_filtro.push(desc.trim());
    });

    estacao_filtro = "Estação de Monta: " + estacao_filtro;

    if (codigo_alfa_numerico!='') {
        codigo_filtro = '->Codigo Fêmea: '+codigo_alfa_numerico;
    }
    else {
        codigo_filtro = '';
    }

    var descricao_filtro =
        codigo_local_filtro +
        estacao_filtro +
        codigo_filtro;

    $(".digitar_filtros").hide();
    $(".busca_animal").show();
    $(".filtros_consulta").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $('.voltar').show();
    $(".descricao_filtro").html(descricao_filtro);

    //$("#aguardar").modal();

    $.post("form_lista_cobertura.php", {
        'local':local, 
        'estacao':estacao, 
        'codigo_alfa_numerico': codigo_alfa_numerico
        }, function(valor){ 
        $("div[id=lista_cobertura]").html(valor);
        //$("#aguardar").modal('hide');
        select_protocolo();
    });
}

function show_consulta() {
    $('.busca_consultar').show();
    //$('.filtros_consulta').hide();
}

function limpar_filtros_animal() {
    $("#codigo_numerico").val('');
    consultar_cobertura();
}

function exibe_mais_filtros() {
    $('.tipo').show();
    $('.voltar').hide();

    if ($("input[name='tipo_cobertura']:checked").val()=='I') {
        $('.opcao').hide();
        $('.local').show();
        $('.estacao_monta').show();
        $('.periodo').hide();
        $('.diagnostico').show();
        $('.previsao').show();
        $('.nascido').show();
        $('.confirmar').show();
    }
    else {
        $('.opcao').show();
        if ($("input[name='opcao_diagnostico_monta']:checked").val()=='C') {
            $('.local').show();
            $('.estacao_monta').hide();
            $('.periodo').show();
            $('.diagnostico').show();
            $('.previsao').show();
            $('.nascido').show();
            $('.confirmar').show();

            if ($("input[name='tipo_diagnostico']:checked").val()=='P') {
                $('.label_periodo_de').html('Período Prenhez (de)');
                $('.label_periodo_ate').text('Período Prenhez (até)');
                $('.previsao').show();
                $('.nascido').show();
            }
            else {
                $('.label_periodo_de').html('Período (de)');
                $('.label_periodo_ate').text('Período (até)');
                $('.previsao').hide();
                $('.nascido').hide();
            }
        } 
        else {
            $('.local').show();
            $('.estacao_monta').hide();
            $('.periodo').hide();
            $('.diagnostico').hide();
            $('.previsao').hide();
            $('.nascido').hide();
            $('.confirmar').show();
        }
    }

    $(".digitar_filtros").show();
    $(".mais_filtros").hide();
    $(".menos_filtros").show();
    //$(".consultar").hide();
    $(".busca_animal").hide();
    $('.busca_consultar').hide();
    $('.busca_limpar').hide();
    $(".lista_contas").hide();
}

function exibe_menos_filtros() {
    // programa diagnostico
    $('.tipo').hide();
    $('.opcao').hide();
    $('.local').hide();
    $('.estacao_monta').hide();
    $('.periodo').hide();
    $('.diagnostico').hide();
    $('.previsao').hide();
    $('.nascido').hide();
    $('.confirmar').hide();
    $('.voltar').show();

    $(".digitar_filtros").hide();
    $(".busca_animal").show();

    let codigo_alfa_numerico = $("#codigo_numerico").val();

    if (codigo_alfa_numerico!='') {
        $('.busca_consultar').hide();
        $('.busca_limpar').show();
    } 
    else {
        $('.busca_consultar').hide();
        $('.busca_limpar').hide();
    }

    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    //$(".consultar").hide();
    $(".lista_contas").show();
}

function popular_select_estacao_monta(value) {
    if ($("input[name='tipo_cobertura']:checked").val()=='I') {
        $("div[id=lista_femeas_servidas]").html('');
        var diagnostico = $('#diagnostico_request').val();

        if (diagnostico=='N') {
            var estacao_monta = $('#codigo_estacao_request').val();
        }
        else {
            var estacao_monta = $("#estacao_monta_anterior").val();
        }

        $.post("lista_estacao_monta.php", {local:value, estacao_monta: estacao_monta}, function(valor){
            $("select[name=estacao_monta]").html(valor);
            //$("select[name=estacao_monta_servidas]").html(valor);
        });
    }
}

// chamado pelo programa form_cobertura_animais_diagnostico.php (menu)
function listar_femeas_servidas_diagnostico() {
    $("div[id=lista_femeas_servidas]").html('');

    var local = $("#codigo_local").val();
    //var id_estacao = $("#estacao_monta_servidas").val();
    var id_estacao = $("#estacao_monta").val();
    var previsao_parto_de = $("#previsao_parto_de_filtro").val();
    var previsao_parto_ate = $("#previsao_parto_ate_filtro").val();
    var situacao = $("#situacao_monta_servidas").val();
    var tipo_registro = $("input[name='tipo_cobertura']:checked").val();
    var diagnostico = $("input[name='tipo_diagnostico']:checked").val();
    var opcao_monta = $("input[name='opcao_diagnostico_monta']:checked").val();
    var periodo_de = $("#periodo_de").val();
    var periodo_ate = $("#periodo_ate").val();

    if (tipo_registro=='M' && opcao_monta==undefined) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione Consultar ou Indicar Diagnóstico!');
        return;
    }

    if (situacao==null) {
        situacao=[''];
    }

    if (local=='000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione a Fazenda!');
        return;
    }

    if (previsao_parto_de!='') {
        if (previsao_parto_ate<previsao_parto_de) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe a Previsão do Parto até corretamente!');
            return;
        }
    }

    var options = $("#codigo_local option:selected");
    var codigo_local_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_local").text();
        codigo_local_filtro.push(desc.trim());
    });

    codigo_local_filtro = codigo_local_filtro;

    //var options = $("#estacao_monta_servidas option:selected");
    var options = $("#estacao_monta option:selected");
    var estacao_filtro = [];

    $(options).each(function () {
        //var desc = $(this).bind("#estacao_monta_servidas").text();
        var desc = $(this).bind("#estacao_monta").text();
        estacao_filtro.push(desc.trim());
    });

    if (tipo_registro=='M') {
        estacao_filtro = "";
    }
    else {
        estacao_filtro = "->Estação de Monta: " + estacao_filtro;
    }

    if (tipo_registro == "M") {
        if (opcao_monta=='C') {
            if (periodo_de=='' && previsao_parto_de=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe o Período ou a Previsão de Parto!');
                return;
            }

            if (periodo_de > periodo_ate) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe o Período Até corretamente!');
                return;
            }
        }
    }

    if (tipo_registro == "I" || (tipo_registro == "M" && opcao_monta=='C')) {
        if (tipo_registro == "I") {
            tipo_filtro = "IATF->";
        }
        else {
            tipo_filtro = "Monta->";
        }
        var options = $("#situacao_monta_servidas option:selected");
        var situacao_filtro = [];

        $(options).each(function () {
            var desc = $(this).bind("#situacao_monta_servidas").text();
            situacao_filtro.push(desc.trim());
        });

        if (situacao_filtro!='') {
            situacao_filtro = "->Situação: " + situacao_filtro;
        }
        else {
            situacao_filtro = "->Situação: Todas";
        }
    } else {
        tipo_filtro = "Monta->";
        situacao_filtro = '';
    }

    if (tipo_registro == "M") {
        if (opcao_monta=='C') {
            if (diagnostico=='P') {
                var filtro_diagnostico = '->Positivas';
            }
            else {
                var filtro_diagnostico = '->Negativas';
            }
        }
        else {
            var filtro_diagnostico = '->Indicar Diagnóstico';
        }
    }
    else {
        if (diagnostico=='P') {
            var filtro_diagnostico = '->Positivas';
        }
        else {
            var filtro_diagnostico = '->Negativas';
        }
    }

    if (previsao_parto_de!='') {
        var data_ini = previsao_parto_de.split("-");
        var dia_ini = data_ini[2];
        var mes_ini = data_ini[1];
        var ano_ini = data_ini[0];

        var data_fim = previsao_parto_ate.split("-");
        var dia_fim = data_fim[2];
        var mes_fim = data_fim[1];
        var ano_fim = data_fim[0];
        previsao_parto_filtro =
            "->Previsão do Parto: de " +
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
        previsao_parto_filtro = '';
    }

    if (periodo_de!='') {
        var data_ini = periodo_de.split("-");
        var dia_ini = data_ini[2];
        var mes_ini = data_ini[1];
        var ano_ini = data_ini[0];

        var data_fim = periodo_ate.split("-");
        var dia_fim = data_fim[2];
        var mes_fim = data_fim[1];
        var ano_fim = data_fim[0];
        periodo_filtro =
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
        periodo_filtro = '';
    }

    if (tipo_registro == "I") {
        var descricao_filtro =
            tipo_filtro +
            codigo_local_filtro +
            filtro_diagnostico +
            estacao_filtro +
            previsao_parto_filtro +
            situacao_filtro;
    }
    else {
        var descricao_filtro =
            tipo_filtro +
            codigo_local_filtro +
            filtro_diagnostico +
            estacao_filtro +
            previsao_parto_filtro +
            situacao_filtro +
            periodo_filtro;
    }

    // programa diagnostico
    $('.tipo').hide();
    $('.opcao').hide();
    $('.local').hide();
    $('.estacao_monta').hide();
    $('.periodo').hide();
    $('.diagnostico').hide();
    $('.previsao').hide();
    $('.nascido').hide();
    $('.confirmar').hide();
    $('.voltar').show();
    $(".digitar_filtros").hide();
    $(".filtros_consulta").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".confirma").hide();

    $(".descricao_filtro").html(descricao_filtro);
    $("#descricao_filtro").val(descricao_filtro);

    $("#aguardar").modal();

    if (tipo_registro=='M') {
        $.post("form_lista_femeas_servidas_monta.php", {
            'local':local, 
            'tipo_cobertura':tipo_registro, 
            'id_estacao': id_estacao,
            'previsao_parto_de': previsao_parto_de,
            'previsao_parto_ate': previsao_parto_ate,
            'situacao': situacao,
            'diagnostico': diagnostico,
            'filtro_periodo_de': periodo_de,
            'filtro_periodo_ate': periodo_ate,
            'opcao_monta': opcao_monta

        }, function(valor){ 
            $("#aguardar").modal('hide');
            $("div[id=lista_femeas_servidas]").html(valor);
        });
    }
    else {
        $.post("form_lista_femeas_servidas_iatf.php", {
            'local':local, 
            'tipo_cobertura':tipo_registro, 
            'id_estacao': id_estacao,
            'previsao_parto_de': previsao_parto_de,
            'previsao_parto_ate': previsao_parto_ate,
            'situacao': situacao,
            'diagnostico': diagnostico            

        }, function(valor){ 
            $("#aguardar").modal('hide');
            $("div[id=lista_femeas_servidas]").html(valor);
        });
    }
 }

// chamado pelo programa form_lista_femeas_servidas_iatf.php
function listar_femeas_servidas_estacao(diagnostico) {
    $("div[id=lista_femeas_servidas]").html('');

    var local = $("#codigo_local").val();
    //var id_estacao = $("#estacao_monta_servidas").val();
    var id_estacao = $("#estacao_monta").val();
    var previsao_parto_de = $("#previsao_parto_de_filtro").val();
    var previsao_parto_ate = $("#previsao_parto_ate_filtro").val();
    var situacao = $("#situacao_monta_servidas").val();
    var tipo_registro = $("input[name='tipo_cobertura']:checked").val();

    if (situacao==null) {
        situacao=[''];
    }

    if (local=='000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione a Fazenda.');
        return;
    }

    if (previsao_parto_de!='') {
        if (previsao_parto_ate<previsao_parto_de) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe a previsão do Parto até corretamente.');
            return;
        }
    }

    var options = $("#codigo_local option:selected");
    var codigo_local_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_local").text();
        codigo_local_filtro.push(desc.trim());
    });

    codigo_local_filtro = codigo_local_filtro;

    //var options = $("#estacao_monta_servidas option:selected");
    var options = $("#estacao_monta option:selected");
    var estacao_filtro = [];

    $(options).each(function () {
        //var desc = $(this).bind("#estacao_monta_servidas").text();
        var desc = $(this).bind("#estacao_monta").text();
        estacao_filtro.push(desc.trim());
    });

    if (tipo_registro=='M') {
        estacao_filtro = "";
    }
    else {
        estacao_filtro = "->Estação de Monta: " + estacao_filtro;
    }

    if (tipo_registro == "I") {
        tipo_filtro = "IATF->";
    } else {
        tipo_filtro = "Monta->";
    }

    var options = $("#situacao_monta_servidas option:selected");
    var situacao_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#situacao_monta_servidas").text();
        situacao_filtro.push(desc.trim());
    });

    if (situacao_filtro!='') {
        situacao_filtro = "->Situação: " + situacao_filtro;
    }
    else {
        situacao_filtro = "->Situação: Todas";
    }

    if (previsao_parto_de!='') {
        var data_ini = previsao_parto_de.split("-");
        var dia_ini = data_ini[2];
        var mes_ini = data_ini[1];
        var ano_ini = data_ini[0];

        var data_fim = previsao_parto_ate.split("-");
        var dia_fim = data_fim[2];
        var mes_fim = data_fim[1];
        var ano_fim = data_fim[0];
        previsao_parto_filtro =
            "->Previsão do Parto: de " +
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
        previsao_parto_filtro = '';
    }

    var descricao_filtro =
        tipo_filtro +
        codigo_local_filtro +
        estacao_filtro +
        previsao_parto_filtro +
        situacao_filtro;

    // programa diagnostico
    $('.tipo').hide();
    $('.opcao').hide();
    $('.local').hide();
    $('.estacao_monta').hide();
    $('.periodo').hide();
    $('.diagnostico').hide();
    $('.previsao').hide();
    $('.nascido').hide();
    $('.confirmar').hide();

    $(".digitar_filtros").hide();
    $(".filtros_consulta").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".descricao_filtro").html(descricao_filtro);
    $("#descricao_filtro").val(descricao_filtro);

    $("#aguardar").modal();

    $.post("form_lista_femeas_servidas_iatf.php", {
        'local':local, 
        'tipo_cobertura':tipo_registro, 
        'id_estacao': id_estacao,
        'previsao_parto_de': previsao_parto_de,
        'previsao_parto_ate': previsao_parto_ate,
        'situacao': situacao,
        'diagnostico': diagnostico

    }, function(valor){ 
        $("#aguardar").modal('hide');
        $("div[id=lista_femeas_servidas]").html(valor);
    });
}

// Chamada do programa form_lista_femeas_servidas_monta.php 
/*function ver_previsao() {
    let checkBoxes = document.getElementsByName("com_sem_previsao");
    noCheckedBoxes = true;

    for (i = 0; i < checkBoxes.length; ++i) {
        if(checkBoxes[i].checked) {
            noCheckedBoxes = false;
        }
    }

    if (noCheckedBoxes) {
        alert ('Selecione Com ou Sem Previsão.');
        return;
    }

    var com_previsao = $('#comprevisao');
    var sem_previsao = $('#semprevisao');

    if (com_previsao.is(":checked")){
        filtro_com_previsao = 'S';
    }
    else {
        filtro_com_previsao = '';
    }

    if (sem_previsao.is(":checked")){
        filtro_sem_previsao = 'S';
    }
    else {
        filtro_sem_previsao = '';
    }

    var local = $("#codigo_local").val();
    var id_estacao = $("#estacao_monta_servidas").val();
    var previsao_parto_de = $("#previsao_parto_de_filtro").val();
    var previsao_parto_ate = $("#previsao_parto_ate_filtro").val();
    var situacao = $("#situacao_monta_servidas").val();
    var tipo_registro = $("input[name='tipo_cobertura']:checked").val();
    var diagnostico = $("input[name='tipo_diagnostico']:checked").val();
    var periodo_de = $("#periodo_de").val();
    var periodo_ate = $("#periodo_ate").val();
    var opcao_monta = $("input[name='opcao_diagnostico_monta']:checked").val();

    if (situacao==null) {
        situacao=[''];
    }

    $("#aguardar").modal();

    $.post("form_lista_femeas_servidas_monta.php", {
        'local':local, 
        'tipo_cobertura':tipo_registro, 
        'id_estacao': id_estacao,
        'previsao_parto_de': previsao_parto_de,
        'previsao_parto_ate': previsao_parto_ate,
        'situacao': situacao,
        'diagnostico': diagnostico,
        'filtro_com_previsao': filtro_com_previsao,
        'filtro_sem_previsao': filtro_sem_previsao,
        'filtro_periodo_de': periodo_de,
        'filtro_periodo_ate': periodo_ate,
        'opcao_monta': opcao_monta
    }, function(valor){ 
        $("#aguardar").modal('hide');
        $("div[id=lista_femeas_servidas]").html(valor);
    });
}*/

function listar_femeas_servidas_excel() {
    var tipo_cobertura = $("input[name='tipo_cobertura']:checked").val();
    var local = $("#codigo_local").val();
    //var id_estacao = $("#estacao_monta_servidas").val();
    var id_estacao = $("#estacao_monta").val();
    var previsao_parto_de = $("#previsao_parto_de_filtro").val();
    var previsao_parto_ate = $("#previsao_parto_ate_filtro").val();
    var situacao = $("#situacao_monta_servidas").val();
    var diagnostico = $("#diagnosticoT").val();

    if (situacao==null) {
        var array_situacao= new Array();
    }
    else {
        var array_situacao = new Array();
        var valor = new Array();

        for (i = 0; i <= situacao.length; i++) {
            valor[i]=situacao[i];
        }
        var array_situacao=valor.join(",");
    }

    var descricao_filtro = $("#descricao_filtro").val();

    $("#aguardar").modal();

    location.href='rel_femeas_servidas_excel.php?tipo_cobertura=' + tipo_cobertura +
    '&local=' + local + 
    '&id_estacao=' + id_estacao + 
    '&array_situacao=' + array_situacao +
    '&previsao_parto_de=' + previsao_parto_de + 
    '&previsao_parto_ate=' + previsao_parto_ate+
    '&filtro=' + descricao_filtro+
    '&diagnostico=' + diagnostico;

    tout = setTimeout('limpar_tela()', 4000);
}

function listar_femeas_servidas_monta_excel() {
    var local = $("#codigo_local").val();
    var previsao_parto_de = $("#previsao_parto_de_filtro").val();
    var previsao_parto_ate = $("#previsao_parto_ate_filtro").val();
    var diagnostico = $("input[name='tipo_diagnostico']:checked").val();
    var periodo_de = $("#periodo_de").val();
    var periodo_ate = $("#periodo_ate").val();
    var opcao_monta = $("input[name='opcao_diagnostico_monta']:checked").val();
    var situacao = $("#situacao_monta_servidas").val();

    if (situacao==null) {
        var array_situacao= new Array();
    }
    else {
        var array_situacao = new Array();
        var valor = new Array();

        for (i = 0; i <= situacao.length; i++) {
            valor[i]=situacao[i];
        }
        var array_situacao=valor.join(",");
    }

    var descricao_filtro = $("#descricao_filtro").val();

    $("#aguardar").modal();

    location.href='rel_femeas_servidas_monta_excel.php?local=' + local + 
    '&previsao_parto_de=' + previsao_parto_de + 
    '&previsao_parto_ate=' + previsao_parto_ate+
    '&descricao_filtro=' + descricao_filtro+
    '&diagnostico=' + diagnostico+
    '&periodo_de=' + periodo_de+
    '&periodo_ate=' + periodo_ate+
    '&opcao_monta=' + opcao_monta+
    '&array_situacao=' + array_situacao;

    tout = setTimeout('limpar_tela()', 8000);
}

function limpar_tela(){
    $('#aguardar').modal('hide');
}

function select_protocolo(){
    $.post("lista_protocolo_select.php", function(valor){
        $("select[name=lista_protocolo]").html(valor);
    });
}

function confirma_cobertura(id){
    document.getElementById('confirma_protocolo').innerHTML = 'Aguarde...';
    document.getElementById("confirma_protocolo").disabled = true;

    let local = $("#codigo_local").val();
    let protocolo = $(`#SelectProtocolo${id}`).val();
    let data = $(`#Dia0_${id}`).val();
    let cobertura = id;

    if (protocolo=='000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Nome do Protocolo.');
        document.getElementById('confirma_protocolo').innerHTML = 'Confirma';
        document.getElementById("confirma_protocolo").disabled = false;
        return;
    }  

    if (data=='') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a data para D0.');
        document.getElementById('confirma_protocolo').innerHTML = 'Confirma';
        document.getElementById("confirma_protocolo").disabled = false;
        return;
    }  

    $.post("gravar_cobertura.php", {'tipo_gravacao': 3, 'protocolo': protocolo, 'data': data, 'cobertura': cobertura}, function(){
        document.getElementById('confirma_protocolo').innerHTML = 'Confirma';
        document.getElementById("confirma_protocolo").disabled = false;

        $.ajax({
            type: "POST",
            url: "gravar_eventos_agenda.php",
            data: {
                tipoGravacao: 0,
                local: local,
                protocolo: protocolo,
                data: data,
                cobertura: id
            }
        });

        alert ('Protocolo gravado com sucesso.');
        consultar_cobertura();
    });
}

function enviar_lixeira(array_conta, tipo_gravacao){
    let local = $("#codigo_local").val();
    var array_registro = array_conta.split('|');
    var cobertura = array_registro[0];
    var grupo = array_registro[2];

    if(confirm(`Deseja excluir o protocolo do grupo ${grupo}?`)){
        $.post("gravar_cobertura.php", {'tipo_gravacao': tipo_gravacao, 'cobertura': cobertura}, function(retorno){

            if (retorno!='') {
                alert (retorno);
            }    

            let local = $("#codigo_local").val();
            consultar_cobertura();
        });
    }
}

function atualizar_lista(){
    let needConfirm = false;
    $("#tabelaMatriz input:visible, #tabelaMatriz select").each(function(){
        switch(this.type){
            case "checkbox":
                if(this.checked){
                    needConfirm = true;
                    return;
                }
                break;
            case "select-one":
                if(this.value != "000000000"){
                    needConfirm = true;
                    return;
                }
                break;
            case "text":
                if(this.value != ""){
                    needConfirm = true;
                    return;
                }
                break;
            case "radio":
                if(this.checked){
                    needConfirm = true;
                    return;
                }
                break;
        }
    });

    //if(needConfirm){
        //if(confirm("Deseja fechar a aba de edição? As alterações feitas não serão salvas!")){
           // $("#modal_editar").modal('hide');
           // let local = $("#codigo_local").val();
           // listar_coberturas(local);
        //}
    //}else{
        $("#modal_editar").modal('hide');
        let local = $("#codigo_local").val();

        //alert (#codigo_local);

        //listar_coberturas(local);
        consultar_cobertura();
   //}
}

function gerar_lista_excel() {
    var cobertura_id = $("#cobertura_id").val();
    $("#aguardar").modal();

    location.href='rel_itens_cobertura_excel.php?cobetura_id=' + cobertura_id;

    tout = setTimeout('limpar_tela()', 8000);
}

function confirmaMatriz(c, q, d){

    var data_protocolo_1 = '';
    var data_protocolo_2 = '';
    var data_protocolo_3 = '';
    var data_protocolo_4 = '';
    var data_protocolo_5 = '';
    var data_protocolo_6 = '';
    var desabilitado = '';
    var qual_dia = 0;   
    var a = [];
    var check = true;

    for(let i = 0; i < q; i++){

        indice = i+1;
        indice = ("0000" + indice).slice(-4); 

        /*let sTemp = "";
        if(q < 10){
            sTemp = "000";
        }else if(q < 100){
            sTemp = "00";
        }else if(q < 1000){
            sTemp = "0";
        }*/

        let dia1 = '';
        let dia2 = '';
        let dia3 = '';
        let dia4 = '';
        let dia5 = '';
        let dia6 = '';

        let touro_semem = $(`#lista_semem${indice}`).val();
        let touroSemem = $(`#codTouro${indice}`).val() ? $(`#codTouro${indice}`).val() : "";
        let raca_touro = $(`#raca_touro${indice}`).val();
        let lote_semem = $(`#lote_semem${indice}`).val();
        let animal_id = $(`#animal_id${indice}`).val();

        switch(d){
            case 2:
                if($(`input[name= 'diaProtocolo${indice}_1']`).is(":disabled") && !$(`input[name= 'diaProtocolo${indice}_2']`).is(":checked") && touro_semem == '000000000' || (touro_semem == '000000000')){
                    check = false;
                }
                break;
            case 3:
                if($(`input[name= 'diaProtocolo${indice}_2']`).is(":disabled") && !$(`input[name= 'diaProtocolo${indice}_3']`).is(":checked") && touro_semem == '000000000' || (touro_semem == '000000000')){
                    check = false;
                }                
                break;
            case 4:
                if($(`input[name= 'diaProtocolo${indice}_3']`).is(":disabled") && !$(`input[name= 'diaProtocolo${indice}_4']`).is(":checked") && touro_semem == '000000000' || (touro_semem == '000000000')){
                    check = false;
                }
                break;
            case 5:
                if($(`input[name= 'diaProtocolo${indice}_4']`).is(":disabled") && !$(`input[name= 'diaProtocolo${indice}_5']`).is(":checked") && touro_semem == '000000000' || (touro_semem == '000000000')){
                    check = false;
                }
                break;
            default:
                break;
        }


        if($(`input[name= 'diaProtocolo${indice}_1']`).is(":disabled")) {
            desabilitado = 'S';
        }

        if($(`input[name= 'diaProtocolo${indice}_1']`).is(":checked")){
            dia1 = 'S';
            qual_dia = 1;
            data_protocolo_1 = $(`#dataProtocolo${indice}_1`).val();
        }
        if($(`input[name= 'diaProtocolo${indice}_2']`).is(":checked")){
            dia2 = 'S';
            qual_dia = 2;
            data_protocolo_2 = $(`#dataProtocolo${indice}_2`).val();
        }
        if($(`input[name= 'diaProtocolo${indice}_3']`).is(":checked")){
            dia3 = 'S';
            qual_dia = 3;
            data_protocolo_3 = $(`#dataProtocolo${indice}_3`).val();
        }
        if($(`input[name= 'diaProtocolo${indice}_4']`).is(":checked")){
            dia4 = 'S';
            qual_dia = 4;
            data_protocolo_4 = $(`#dataProtocolo${indice}_4`).val();
        }
        if($(`input[name= 'diaProtocolo${indice}_5']`).is(":checked")){
            dia5 = 'S';
            qual_dia = 5;
            data_protocolo_5 = $(`#dataProtocolo${indice}_5`).val();
        }
        if($(`input[name= 'diaProtocolo${indice}_6']`).is(":checked")){
            dia6 = 'S';
            qual_dia = 6;
            data_protocolo_6 = $(`#dataProtocolo${indice}_6`).val();
        }

        //let data_diagnostico = $(`#data_diagnostico${indice}`).val();

        let data_diagnostico = $('#data_diagnostico_realizado').val();

        let inseminador = $(`#inseminador${indice}`).val();
        let result_diagnostico = $(`input[name='resultado${indice}']:checked`).val() ? $(`input[name='resultado${indice}']:checked`).val() : "";
        let destino = $(`input[name='destino${indice}']:checked`).val() ? $(`input[name='destino${indice}']:checked`).val() : "";

        let obj = {
            'cobertura': c,
            'ordem': indice,
            'touro_semem': touro_semem,
            'touroSemem': touroSemem,
            'raca_touro': raca_touro,
            'dia_1': dia1,
            'dia_2': dia2,
            'dia_3': dia3,
            'dia_4': dia4,
            'dia_5': dia5,
            'dia_6': dia6,
            'data_diagnostico': data_diagnostico,
            'inseminador': inseminador,
            'resultado_diagnostico': result_diagnostico,
            'destino': destino,
            'lote_semem': lote_semem,
            'animal_id': animal_id,
            'quantos_dias' : d,
            'qual_dia' : qual_dia
        };

        a[i] = obj;
    }

    if(!check && d==qual_dia){
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione o Touro/Sêmen para todos os animais que estão no último dia do protocolo!');
        return;
    }

    var headerArray = [];
    $('#header').children().each(function(){
        headerArray.push($(this).text());
    });

    var data = new Date();
    var dia = String(data.getDate()).padStart(2, '0');
    var mes = String(data.getMonth() + 1).padStart(2, '0');
    var ano = data.getFullYear();
    data_hoje = ano + mes + dia;

    if (data_protocolo_1!='') {
        data_protocolo_1 = data_protocolo_1.replace(/[^\d]+/g,'');
        data_protocolo_1_edi = data_protocolo_1.substring(6, 8) + '/' + data_protocolo_1.substring(4, 6) + '/' + data_protocolo_1.substring(0, 4);

        if (data_protocolo_1>data_hoje) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(headerArray[5] + ' em ' + data_protocolo_1_edi);
            $(`#checkAllDias${1}`).prop("checked", false);
            $(`.diaProtocolo${1}`).prop("checked", false);
            return;
        }
    }

    if (data_protocolo_2!='') {
        data_protocolo_2 = data_protocolo_2.replace(/[^\d]+/g,'');
        data_protocolo_2_edi = data_protocolo_2.substring(6, 8) + '/' + data_protocolo_2.substring(4, 6) + '/' + data_protocolo_2.substring(0, 4);

        if (data_protocolo_2>data_hoje) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(headerArray[6] + ' em ' + data_protocolo_2_edi);
            $(`#checkAllDias${2}`).prop("checked", false);
            $(`.diaProtocolo${2}`).prop("checked", false);
            return;
        }
    }

    if (data_protocolo_3!='') {
        data_protocolo_3 = data_protocolo_3.replace(/[^\d]+/g,'');
        data_protocolo_3_edi = data_protocolo_3.substring(6, 8) + '/' + data_protocolo_3.substring(4, 6) + '/' + data_protocolo_3.substring(0, 4);

        if (data_protocolo_3>data_hoje) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(headerArray[7] + ' em ' + data_protocolo_3_edi);
            $(`#checkAllDias${3}`).prop("checked", false);
            $(`.diaProtocolo${3}`).prop("checked", false);
            return;
        }
    }

    if (data_protocolo_4!='') {
        data_protocolo_4 = data_protocolo_4.replace(/[^\d]+/g,'');
        data_protocolo_4_edi = data_protocolo_4.substring(6, 8) + '/' + data_protocolo_4.substring(4, 6) + '/' + data_protocolo_4.substring(0, 4);

        if (data_protocolo_4>data_hoje) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(headerArray[8] + ' em ' + data_protocolo_4_edi);
            $(`#checkAllDias${4}`).prop("checked", false);
            $(`.diaProtocolo${4}`).prop("checked", false);
            return;
        }
    }

    if (data_protocolo_5!='') {
        data_protocolo_5 = data_protocolo_5.replace(/[^\d]+/g,'');
        data_protocolo_5_edi = data_protocolo_5.substring(6, 8) + '/' + data_protocolo_5.substring(4, 6) + '/' + data_protocolo_5.substring(0, 4);

        if (data_protocolo_5>data_hoje) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(headerArray[9] + ' em ' + data_protocolo_5_edi);
            $(`#checkAllDias${5}`).prop("checked", false);
            $(`.diaProtocolo${5}`).prop("checked", false);
            return;
        }
    }

    if (data_protocolo_6!='') {
        data_protocolo_6 = data_protocolo_6.replace(/[^\d]+/g,'');
        data_protocolo_6_edi = data_protocolo_6.substring(6, 8) + '/' + data_protocolo_6.substring(4, 6) + '/' + data_protocolo_6.substring(0, 4);

        if (data_protocolo_6>data_hoje) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(headerArray[10] + ' em ' + data_protocolo_6_edi);
            $(`#checkAllDias${6}`).prop("checked", false);
            $(`.diaProtocolo${6}`).prop("checked", false);
            return;
        }
    }

    if (data_protocolo_1!='') {
        data_protocolo_1 = data_protocolo_1.replace(/[^\d]+/g,'');
        data_protocolo_1_edi = data_protocolo_1.substring(6, 8) + '/' + data_protocolo_1.substring(4, 6) + '/' + data_protocolo_1.substring(0, 4);


        if (data_protocolo_1<data_hoje && desabilitado=='') {
            if (window.confirm('D0 esta agendado para ' + data_protocolo_1_edi + ' confirma o inicio do protocolo nessa data?')) {     
                $.ajax({
                    type: 'POST',
                    url: 'gravar_cobertura.php',
                    data: {
                        tipo_gravacao: 0,
                        vetObj: a
                    },
                    success: function(data){
                        /*alert (data.success);
                        if (data.success) {
                            alert (data.message);
                        }*/
                        alert("Gravado com sucesso!");
                        atualizarData(data);
                    }
                });   
            }
            else {
                $(`#checkAllDias${1}`).prop("checked", false);
                $(`.diaProtocolo${1}`).prop("checked", false);
                $("#volta_lista_cobertura").modal();
                $("#volta_lista_cobertura .modal-body").html('Excluir o Protocolo e Agendar nova Data.');
                return;
            }
        }
        else {
            $.ajax({
                type: 'POST',
                url: 'gravar_cobertura.php',
                data: {
                    tipo_gravacao: 0,
                    vetObj: a
                },
                success: function(data){
                    /*alert (data.success);
                    if (data.success) {
                        alert (data.message);
                    }*/
                    alert("Gravado com sucesso!");
                    atualizarData(data);
                }
            });   
        }
    }
    else {
        $.ajax({
            type: 'POST',
            url: 'gravar_cobertura.php',
            data: {
                tipo_gravacao: 0,
                vetObj: a
            },
            success: function(data){
                /*alert (data.success);
                if (data.success) {
                    alert (data.message);
                }*/
                alert("Gravado com sucesso!");
                atualizarData(data);
            }
        });   
    }
}

function fechar_lista() {
    $("#modal_editar").modal('hide');
    let local = $("#codigo_local").val();
    listar_coberturas(local);
}

function atualizarData(array_conta){

    var array_registro = array_conta.split('|');
    var cob = array_registro[0].split("\n").pop();
    let tipoCobertura = $("input[name='tipo_cobertura']:checked").val();

    if (cob==0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(array_registro[1]);
        return;
    }

    $.post("ler_itens_protocolo_cobertura.php", {protocolo_id: array_registro[1], cobertura_id: cob, tipoCobertura: tipoCobertura},
    function(data){
        $("#lista_dias_protocolo").html(data);
    });
}

function editar_cobertura(array_conta){
    var array_registro = array_conta.split('|');

    $("#aguardar").modal('show');

    $.post("ler_itens_protocolo_cobertura.php", {protocolo_id: array_registro[1], cobertura_id: array_registro[0]},
    function(data){
        $("#lista_dias_protocolo").html(data);
        $("#aguardar").modal('hide');
        $("#modal_editar").modal('show');
    });
}

function checkAllDias(d){

    if($(`#checkAllDias${d}`).is(":checked")){
        $(`.diaProtocolo${d}`).prop("checked", true);
    }else{
        $(`.diaProtocolo${d}`).prop("checked", false);
    }
}

function resultadoCobertura(id, value){
    let data_diagnostico_realizado = $("#data_diagnostico_realizado").val();

    if (data_diagnostico_realizado=='') {
        alert ('Informe a data do diagnóstico realizado.');
        $('#data_diagnostico_realizado').focus();
        document.getElementById("data_diagnostico_realizado");
        return;
    }   

    if (value == 'N') {
        let ordem = id.split("resultadoN").pop();

        $("#ordem_negativo").val(ordem);

        let codigo_animal = $(`#animal_codigo${ordem}`).val();
        $(".codigo_matriz").html('Nº Fêmea: ' + codigo_animal);

        let local = $("#local_id").val();
        let estacao = $("#estacao_id").val();

        $.post("lista_grupo_estacao_monta.php", {local:local, estacao:estacao}, function(valor){
            $("select[name=grupo_nova_cobertura]").html(valor);
        });

        $("#modal_diagnostico_negativo").modal('show');
    }
}

function resultadoCoberturaMonta(id, value){
    let id_cobertura = id.split("resultadoN").pop();

    var resultadoAnterior = $(`#resultadoAnterior${id_cobertura}`).val();

    if ($(`#codigo_alfa_femea${id_cobertura}`).val()=='') {
        var codigo_animal = $(`#codigo_num_femea${id_cobertura}`).val();
    }
    else {
        var codigo_animal = $(`#codigo_alfa_femea${id_cobertura}`).val()+'-'+
                            $(`#codigo_num_femea${id_cobertura}`).val();
    }

    $(`#id_cobertura_monta`).val(id_cobertura);
    $(`#resultado_anterior`).val(resultadoAnterior);
    $(".codigo_matriz_monta").html('Nº Fêmea: ' + codigo_animal);
    $("#modal_diagnostico_negativo_monta").modal('show');
}

// Altera o diagnóstico negativo para positivo
function alterarDiagnosticoParaPositivo(id, value){
    let ordem = id.split("resultadoA").pop();
    $("#ordem_positivo").val(ordem);

    let codigo_animal = $(`#animal_codigo${ordem}`).val();
    $(".codigo_matriz").html('Nº Fêmea: ' + codigo_animal);

    $("#modal_diagnostico_positivo").modal('show');
}

function opcao_diagnostico(id, value) {
    if (value == 'N') {
        $(".nova_cobertura").show();
        $(`#novo_grupo`).prop("checked", false);
        $(`#liberar_matriz`).prop("checked", false);
    }
    else {
        $(".nova_cobertura").hide();
        $(`#novo_grupo`).prop("checked", false);
        $(`#liberar_matriz`).prop("checked", false);
    }
}

// Essa função foi inserida no dia 28/01/2026 chamada do programa form_cobertura_animais_diagnostico.php
// Conforme o trello Cartão: OBSERVAÇÕES NO USO DA REPRODUÇÃO, Checklist: AJUSTE REUNIAO 27/01/2026
function gravar_diagnostico_negativo_confirmacao() {
    let cobertura = $("#cobertura_id").val();
    let ordem = $("#ordem_negativo").val();
    let local = $("#local_id").val();
    let estacao = $("#estacao_id").val();
    let animal_id = $(`#animal_id${ordem}`).val();
    let codigo_animal = $(`#animal_codigo${ordem}`).val();

    if (ordem.length>4) {
        cobertura = ordem.slice(0, 9);
        ordem = ordem.slice(-3);
    }

    opcao_nova_cobertura = 'L';

    $.ajax({
        type: "POST",
        url: 'gravar_cobertura_diagnostico_negativo.php',
        data: {
            cobertura_numero_id: cobertura,
            codigo_id: animal_id,
            codigo_animal: codigo_animal,
            estacao_monta: estacao,
            local: local,
            ordem: ordem,
            opcao_nova_cobertura: opcao_nova_cobertura,
        },
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else {
                $("#modal_diagnostico_negativo").modal('hide');
                var ordem = $("#ordem_negativo").val();
                var elemento = document.getElementById('resultadoP' + ordem);
                elemento.setAttribute('data-toggle', 'tooltip');
                elemento.setAttribute('data-placement', 'right');
                elemento.setAttribute('title', 'A alteração do Diagnóstico Negativo para Positivo só poderá ser feita clicando na opção Diagnóstico Negativo (acima).');                    
                $(elemento).tooltip();

                var elemento = document.getElementById('resultadoN' + ordem);
                elemento.setAttribute('data-toggle', 'tooltip');
                elemento.setAttribute('data-placement', 'right');
                elemento.setAttribute('title', 'A alteração do Diagnóstico Negativo para Positivo só poderá ser feita clicando na opção Diagnóstico Negativo (acima).');                    
                $(elemento).tooltip();

                var opcaoP = document.querySelector(`#resultadoP${ordem}`);
                opcaoP.disabled = true;

                var opcaoN = document.querySelector(`#resultadoN${ordem}`);
                opcaoN.disabled = true;
            }
        }
    });
}

function gravar_diagnostico_negativo() {
    let cobertura = $("#cobertura_id").val();
    let ordem = $("#ordem_negativo").val();
    let local = $("#local_id").val();
    let estacao = $("#estacao_id").val();
    let animal_id = $(`#animal_id${ordem}`).val();
    let codigo_animal = $(`#animal_codigo${ordem}`).val();

    if (ordem.length>4) {
        cobertura = ordem.slice(0, 9);
        ordem = ordem.slice(-3);
    }

    let opcao_nova_cobertura = $(`input[name='opcao_nova_cobertura']:checked`).val();
    let grupo = $("#grupo_nova_cobertura").val();

    if (opcao_nova_cobertura=='G' && grupo=='000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione um Grupo para a nova cobertura.');
        return;
    }

    if (opcao_nova_cobertura=='G') {
        $.ajax({
            type: "POST",
            url: 'gravar_matrizes_inserir_nova.php',
            data: {
                cobertura_numero_id: cobertura,
                codigo_id: animal_id,
                codigo_grupo: grupo,
                tipo_inserir: 1,
                estacao_monta: estacao,
                local: local,
                ordem: ordem,
            },
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else {
                    //$(".grupo_nova_cobertura").hide();
                    $(`#novo_grupo`).prop("checked", false);
                    $(`#liberar_matriz`).prop("checked", false);
                    $(`#descartar`).prop("checked", false);
                    const divElement = document.querySelector('.label_nova_cobertura');
                    divElement.classList.add('form-group');

                    $("#modal_diagnostico_negativo").modal('hide');

                    var ordem = $("#ordem_negativo").val();

                    var elemento = document.getElementById('resultadoP' + ordem);
                    elemento.setAttribute('data-toggle', 'tooltip');
                    elemento.setAttribute('data-placement', 'right');
                    elemento.setAttribute('title', 'A alteração do Diagnóstico Negativo para Positivo só poderá ser feita clicando na opção Diagnóstico Negativo (acima).');                    
                    $(elemento).tooltip();

                    var elemento = document.getElementById('resultadoN' + ordem);
                    elemento.setAttribute('data-toggle', 'tooltip');
                    elemento.setAttribute('data-placement', 'right');
                    elemento.setAttribute('title', 'A alteração do Diagnóstico Negativo para Positivo só poderá ser feita clicando na opção Diagnóstico Negativo (acima).');                    
                    $(elemento).tooltip();

                    var opcaoP = document.querySelector(`#resultadoP${ordem}`);
                    opcaoP.disabled = true;

                    var opcaoN = document.querySelector(`#resultadoN${ordem}`);
                    opcaoN.disabled = true;

                    //$("#mensagem_retorno").modal();
                    //$("#mensagem_retorno .modal-body").html('Gravado em novo grupo com sucesso!');
                }
            }
        });
    }
    else {
        $.ajax({
            type: "POST",
            url: 'gravar_cobertura_diagnostico_negativo.php',
            data: {
                cobertura_numero_id: cobertura,
                codigo_id: animal_id,
                codigo_animal: codigo_animal,
                estacao_monta: estacao,
                local: local,
                ordem: ordem,
                opcao_nova_cobertura: opcao_nova_cobertura,
            },
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else {
                    //$(".grupo_nova_cobertura").hide();
                    $(`#novo_grupo`).prop("checked", false);
                    $(`#liberar_matriz`).prop("checked", false);
                    $(`#descartar`).prop("checked", false);
                    $("#modal_diagnostico_negativo").modal('hide');
                    const divElement = document.querySelector('.label_nova_cobertura');
                    divElement.classList.add('form-group');

                    var ordem = $("#ordem_negativo").val();

                    var elemento = document.getElementById('resultadoP' + ordem);
                    elemento.setAttribute('data-toggle', 'tooltip');
                    elemento.setAttribute('data-placement', 'right');
                    elemento.setAttribute('title', 'A alteração do Diagnóstico Negativo para Positivo só poderá ser feita clicando na opção Diagnóstico Negativo (acima).');                    
                    $(elemento).tooltip();

                    var elemento = document.getElementById('resultadoN' + ordem);
                    elemento.setAttribute('data-toggle', 'tooltip');
                    elemento.setAttribute('data-placement', 'right');
                    elemento.setAttribute('title', 'A alteração do Diagnóstico Negativo para Positivo só poderá ser feita clicando na opção Diagnóstico Negativo (acima).');                    
                    $(elemento).tooltip();

                    var opcaoP = document.querySelector(`#resultadoP${ordem}`);
                    opcaoP.disabled = true;

                    var opcaoN = document.querySelector(`#resultadoN${ordem}`);
                    opcaoN.disabled = true;

                    //$("#mensagem_retorno").modal();
                    //$("#mensagem_retorno .modal-body").html('Gravado com sucesso!');
                }
                //$(".nova_cobertura").hide();
                //$(`#novo_grupo`).prop("checked", false);
                //$(`#liberar_matriz`).prop("checked", false);
                //$(`#nova_cobertura`).prop("checked", false);
                //$(`#descartar`).prop("checked", false);
                //$("#modal_diagnostico_negativo").modal('hide');
                //alert("Gravado com sucesso!");
            }
        });
    }
}

function gravar_diagnostico_alterar_para_positivo_femeas_servidas() {
    let local = $("#local_id").val();
    let estacao = $("#estacao_id").val();
    let ordem = $("#ordem_positivo").val();
    let animal_id = $(`#animal_id${ordem}`).val();
    let codigo_animal = $(`#animal_codigo${ordem}`).val();
    let cobertura = ordem.substring(0, 9);
    let item = ordem.substring(9, 13); 

    $.ajax({
        type: "POST",
        url: 'gravar_cobertura_diagnostico_positivo_alteracao.php',
        data: {
            cobertura_numero_id: cobertura,
            ordem: item,
            codigo_id: animal_id,
            codigo_animal: codigo_animal,
            estacao_monta: estacao,
            local: local,
        },
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $("#modal_diagnostico_positivo").modal('hide');
                var opcaoA = document.querySelector(`#resultadoA${ordem}`);
                opcaoA.disabled = true;

                $("#mensagem_retorno_positiva").modal();
                $("#mensagem_retorno_positiva .modal-body").html(data.message);
                //var diagnostico = 'N';
                //listar_femeas_servidas_estacao(diagnostico);
            }
        }
    });
}

function gravar_diagnostico_negativo_femeas_servidas() {
    let local = $("#local_id").val();
    let estacao = $("#estacao_id").val();
    let opcao_nova_cobertura = $(`input[name='opcao_nova_cobertura']:checked`).val();
    let grupo = $("#grupo_nova_cobertura").val();

    if (opcao_nova_cobertura=='G' && grupo=='000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione um Grupo para a nova cobertura.');
        return;
    }

    let ordem = $("#ordem_negativo").val();
    let animal_id = $(`#animal_id${ordem}`).val();
    let codigo_animal = $(`#animal_codigo${ordem}`).val();
    let cobertura = ordem.substring(0, 9);
    let item = ordem.substring(9, 13);

    if (opcao_nova_cobertura=='G') {
        $.ajax({
            type: "POST",
            url: 'gravar_matrizes_inserir_nova.php',
            data: {
                cobertura_numero_id: cobertura,
                codigo_id: animal_id,
                codigo_grupo: grupo,
                tipo_inserir: 1,
                estacao_monta: estacao,
                local: local,
                ordem: item,
            },
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else {
                    //$(".grupo_nova_cobertura").hide();
                    $(`#novo_grupo`).prop("checked", false);
                    $(`#liberar_matriz`).prop("checked", false);
                    $(`#descartar`).prop("checked", false);
                    $("#modal_diagnostico_negativo").modal('hide');
                    const divElement = document.querySelector('.label_nova_cobertura');
                    divElement.classList.add('form-group');

                    let ordem = $("#ordem_negativo").val();

                    var elemento = document.querySelector(`.resultadoP${ordem}`);
                    elemento.setAttribute('data-toggle', 'tooltip');
                    elemento.setAttribute('data-placement', 'right');
                    elemento.setAttribute('title', 'A alteração de Diagnóstico Negativo para Positivo só poderá ser feita pela lista dos Diagnósticos Negativos.');                    

                    var elemento = document.querySelector(`.resultadoN${ordem}`);
                    elemento.setAttribute('data-toggle', 'tooltip');
                    elemento.setAttribute('data-placement', 'right');
                    elemento.setAttribute('title', 'A alteração de Diagnóstico Negativo para Positivo só poderá ser feita pela lista dos Diagnósticos Negativos.');                    

                    $('[data-toggle="tooltip"]').tooltip();

                    var opcaoP = document.querySelector(`#resultadoP${ordem}`);
                    opcaoP.disabled = true;

                    var opcaoN = document.querySelector(`#resultadoN${ordem}`);
                    opcaoN.disabled = true;
                    //$("#mensagem_retorno").modal();
                    //$("#mensagem_retorno .modal-body").html('Gravado em novo grupo com sucesso!');
                }
            }
        });
    }
    else {
        $.ajax({
            type: "POST",
            url: 'gravar_cobertura_diagnostico_negativo.php',
            data: {
                cobertura_numero_id: cobertura,
                codigo_id: animal_id,
                codigo_animal: codigo_animal,
                estacao_monta: estacao,
                local: local,
                ordem: item,
                opcao_nova_cobertura: opcao_nova_cobertura,
            },
            success: function(data){
                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else {
                    //$(".grupo_nova_cobertura").hide();
                    $(`#novo_grupo`).prop("checked", false);
                    $(`#liberar_matriz`).prop("checked", false);
                    $(`#descartar`).prop("checked", false);
                    $("#modal_diagnostico_negativo").modal('hide');
                    const divElement = document.querySelector('.label_nova_cobertura');
                    divElement.classList.add('form-group');

                    let ordem = $("#ordem_negativo").val();

                    var elemento = document.querySelector(`.resultadoP${ordem}`);
                    elemento.setAttribute('data-toggle', 'tooltip');
                    elemento.setAttribute('data-placement', 'right');
                    elemento.setAttribute('title', 'A alteração de Diagnóstico Negativo para Positivo só poderá ser feita pela lista dos Diagnósticos Negativos.');                    

                    var elemento = document.querySelector(`.resultadoN${ordem}`);
                    elemento.setAttribute('data-toggle', 'tooltip');
                    elemento.setAttribute('data-placement', 'right');
                    elemento.setAttribute('title', 'A alteração de Diagnóstico Negativo para Positivo só poderá ser feita pela lista dos Diagnósticos Negativos.');                    

                    $('[data-toggle="tooltip"]').tooltip();

                    var opcaoP = document.querySelector(`#resultadoP${ordem}`);
                    opcaoP.disabled = true;

                    var opcaoN = document.querySelector(`#resultadoN${ordem}`);
                    opcaoN.disabled = true;

                    //$("#mensagem_retorno").modal();
                    //$("#mensagem_retorno .modal-body").html('Gravado com sucesso!');
                }

                /*$(".nova_cobertura").hide();
                $(`#novo_grupo`).prop("checked", false);
                $(`#liberar_matriz`).prop("checked", false);
                $(`#nova_cobertura`).prop("checked", false);
                $(`#descartar`).prop("checked", false);
                $("#modal_diagnostico_negativo").modal('hide');
                alert("Gravado com sucesso!");*/
            }
        });
    }
}

function gravar_diagnostico_negativo_femeas_servidas_monta(){
    let local = $("#codigo_local").val();
    let id_cobertura = $("#id_cobertura_monta").val();
    //let estacao_monta = $("#estacao_monta_servidas").val();
    let estacao_monta = $("#estacao_monta").val();
    let opcao_diagnostico = $(`input[name='opcao_diagnostico']:checked`).val();

    if (opcao_diagnostico==undefined) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Seleciome uma opção!');
        return;
    }

    $.ajax({
        type: "POST",
        url: 'gravar_cobertura_diagnostico_negativo_monta.php',
        data: {
            cobertura_numero_id: id_cobertura,
            opcao_diagnostico: opcao_diagnostico,
            local: local,
            estacao_monta: estacao_monta
        },
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else {
                $("#modal_diagnostico_negativo_monta").modal('hide');
                $("#mensagem_retorno_monta").modal();
                $("#mensagem_retorno_monta .modal-body").html(data.message);
            }
        }
    });
}

function gravar_diagnostico_positivo_femeas_servidas(id, value){
    let local = $("#local_id").val();
    let estacao = $("#estacao_id").val();

    let ordem = id.split("resultadoP").pop();
    $("#ordem_negativo").val(ordem);

    let codigo_animal = $(`#animal_codigo${ordem}`).val();
    let animal_id = $(`#animal_id${ordem}`).val();
    let cobertura = ordem.substring(0, 9);
    let item = ordem.substring(9, 13);

    $.ajax({
        type: "POST",
        url: 'gravar_cobertura_diagnostico_positivo.php',
        data: {
            cobertura_numero_id: cobertura,
            codigo_id: animal_id,
            codigo_animal: codigo_animal,
            estacao_monta: estacao,
            local: local,
            ordem: item,
        },
        success: function(data){
            var array_registro = data.split('|');
            var cob = array_registro[0].split("\n").pop();

            if (cob==0) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(array_registro[1]);
                return;
            }
            else {
                qtd_diagnostico = ("00" + cob).slice(-2); 
                $(`#qtd_diagnosticos${ordem}`).text(qtd_diagnostico);

                var opcaoP = document.querySelector(`#resultadoP${ordem}`);
                opcaoP.disabled = true;

                var opcaoN = document.querySelector(`#resultadoN${ordem}`);
                opcaoN.disabled = true;
                alert("Gravado com sucesso!");
            }
        }
    });
}

function gravar_diagnostico_positivo_femeas_servidas_monta(id, value){
    let local = $("#local_id_monta").val();
    let id_cobertura = id.split("resultadoP").pop();
    let data_prenhes = $(`#data_prenhes${id_cobertura}`).val();
    let previsao_parto = $(`#data_previsao${id_cobertura}`).val();

    if (data_prenhes=='') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Data da Prenhes!');
        $(`#resultadoP${id_cobertura}`).prop("checked", false);
        return;
    } 
    else {
        $(`#resultadoP${id_cobertura}`).prop("checked", true);
    }

    $.ajax({
        type: "POST",
        url: 'gravar_cobertura_diagnostico_positivo_monta.php',
        data: {
            cobertura_numero_id: id_cobertura,
            local: local,
            data_prenhes: data_prenhes,
            previsao_parto: previsao_parto
        },
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            /*else {
                $("#mensagem_retorno_positiva").modal();
                $("#mensagem_retorno_positiva .modal-body").html(data.message);
            }*/
        }
    });
}

function gravar_limpa_diagnostico_positivo_femeas_servidas_monta(id_cobertura) {
    let local = $("#local_id_monta").val();
    let data_prenhes = $(`#data_prenhes${id_cobertura}`).val();
    let previsao_parto = $(`#data_previsao${id_cobertura}`).val();

    $.ajax({
        type: "POST",
        url: 'gravar_limpa_cobertura_diagnostico_positivo_monta.php',
        data: {
            cobertura_numero_id: id_cobertura,
            local: local,
            data_prenhes: data_prenhes,
            previsao_parto: previsao_parto
        },
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            /*else {
                $("#mensagem_retorno_positiva").modal();
                $("#mensagem_retorno_positiva .modal-body").html(data.message);
            }*/
        }
    });

}

function fechar_modal_diagnostico_negativo() {
    let ordem = $("#ordem_negativo").val();
    $(`#resultadoN${ordem}`).prop("checked", false);

    const divElement = document.querySelector('.label_nova_cobertura');
    divElement.classList.add('form-group');
    
    /*let opcao_nova_cobertura = $(`input[name='opcao_nova_cobertura']:checked`).val();

        if (opcao_nova_cobertura=='G') {
            $(".grupo_nova_cobertura").show();
            $('#grupo_nova_cobertura').focus();
        }
        else {
            const divElement = document.querySelector('.label_nova_cobertura');
            divElement.classList.add('form-group');

            $("#grupo_nova_cobertura").val('000');
            $(".grupo_nova_cobertura").hide();
        }
    */
    $(".nova_cobertura").prop("checked", false);
    //$(".grupo_nova_cobertura").hide();
}

function fechar_modal_diagnostico_negativo_monta() {
    let id_cobertura = $("#id_cobertura_monta").val();
    let resultadoAnterior = $("#resultado_anterior").val();

    $(`#resultadoN${id_cobertura}`).prop("checked", false);

    if (resultadoAnterior=='P') {
        $(`#resultadoP${id_cobertura}`).prop("checked", true);
    }

    $(`#descartar_monta`).prop("checked", false);
    $(`#liberar_matriz_monta`).prop("checked", false);
}

function fechar_modal_diagnostico_positivo() {
    let ordem = $("#ordem_positivo").val();
    $(`#resultadoA${ordem}`).prop("checked", false);
}

function raca_touroSemem(id, value){
    let selects = $("select[name='lista_semem']");
    let verificacao = true; 

    let label = $(`#${id} :selected`).parent().attr('label');
    let ordem = id.split("lista_semem").pop();

    //if(id == "lista_semem0001"){
        selects.each(function(i){
            if(selects[i].id != id && selects[i].value != 000000000){
                verificacao = false;
            }
        });

        if(verificacao && confirm("Deseja selecionar este Touro/Sêmen para todos os registros?")){

            /*if(label == 'SEMEM'){
                selects.each(function(i){
                    let temp = selects[i].id.split("lista_semem").pop();
                    $(`#lote_semem${temp}`).prop("disabled", false);
                });
                $(`#lote_semem${ordem}`).prop("disabled", false);
            }else{
                $(`#lote_semem${ordem}`).prop("disabled", true);
                $(`#lote_semem${ordem}`).val("");
            }*/
        
            $.post("ler_raca_touro_semem.php", {'identificador': label, 'codigo': value},
            function(data){
                $array_conta = data.split("|");

                $(`#racaTouro${ordem}`).text($array_conta[0]);
                $(`#raca_touro${ordem}`).val($array_conta[1]);

                selects.each(function(i){
                    if(selects[i].id != id){
                        var o = selects[i].id.split("lista_semem").pop();
                        selects[i].value = value;
                        $(`#racaTouro${o}`).text($array_conta[0]);
                        $(`#raca_touro${o}`).val($array_conta[1]);
                    }
                });
            });
        }
    //}

    /*if(label == 'SEMEM'){
        $(`#lote_semem${ordem}`).prop("disabled", false);
    }else{
        $(`#lote_semem${ordem}`).prop("disabled", true);
        $(`#lote_semem${ordem}`).val("");
    }*/

    $.post("ler_raca_touro_semem.php", {'identificador': label, 'codigo': value},
    function(data){
        $array_conta = data.split("|");
        $(`#racaTouro${ordem}`).text($array_conta[0]);
        $(`#raca_touro${ordem}`).val($array_conta[1]);
    });
}

function lote_semem(id, value, qtd_registros){
    let inputs = $("input[name='lote_semem']");
    let verificacao = true;

    //if (id == 'lote_semem0001' && value!='') {

        inputs.each(function(i){
            if(inputs[i].id != id && inputs[i].value != ''){
                verificacao = false;
            }
        });

        if(verificacao && confirm("Deseja copiar este lote para todos os registros?")){
            for(let i = 0; i < qtd_registros; i++){
                indice = i+1;
                indice = ("0000" + indice).slice(-4); 

                $(`#lote_semem${indice}`).val(value);
            }
        }
    //}
}

function lista_funcionario(id, value){
    let selects = $("select[name='lista_funcionario']");
    let verificacao = true;
    let ordem = id.split("lista_funcionario").pop();

    let nome_inseminador = $(`#lista_funcionario${ordem}`).find(":selected").text();

    //if(id == "lista_funcionario0001"){
        selects.each(function(i){
            if(selects[i].id != id && selects[i].value != 000000000){
                verificacao = false;
            }
        });

        if(verificacao && confirm("Deseja selecionar este inseminador para todos os registros?")){
            selects.each(function(i){
                if(selects[i].id != id){
                    var o = selects[i].id.split("lista_funcionario").pop();
                    selects[i].value = value;
                    var nome_inseminador = selects[i].options[selects[i].selectedIndex].text;
                    $(`#inseminador${o}`).val(nome_inseminador);
                }
            });
        }
    //}

    $(`#inseminador${ordem}`).val(nome_inseminador);
}

function inseminador(id, value, qtd_registros){
    let inputs = $("input[name='inseminador']");
    let verificacao = true;

    //if (id == 'inseminador0001' && value!='') {

        inputs.each(function(i){
            if(inputs[i].id != id && inputs[i].value != ''){
                verificacao = false;
            }
        });

        if(verificacao && confirm("Deseja copiar este inseminador para todos os registros?")){
            for(let i = 1; i < qtd_registros; i++){
                indice = i+1;
                indice = ("0000" + indice).slice(-4); 

                $(`#inseminador${indice}`).val(value);
            }
        }
    //}
}

// calcular previsao parto ao inserir femea na monta
function calcular_data_previsao(id, value) { 
    var data_prenhes = value;
    var id_cobertura = id.substring(12);

    let date = new Date(data_prenhes);
    let ano = date.getFullYear();

    if (data_prenhes!='' && ano>1999) {
        $(`#data_previsao${id_cobertura}`).val('');

        let date = new Date(data_prenhes);
        date.setDate(date.getDate() + 283); // para achar 282 dias tem que colocar 1 dia a mais no calculo

        var data_previsao = date.getFullYear() +"-" + adicionaZero((date.getMonth() + 1)) + "-" + adicionaZero(date.getDate());

        var codigo_mae = $(`#codigo_id${id_cobertura}`).val();

        verificar_ultimo_nascimento(codigo_mae, data_previsao).then(function(resultado) {
            if (resultado<=9) {
                $(`#data_prenhes${id_cobertura}`).val('');
                $('#mensagem_erro_previsao .modal-body .mensagem_previsao').html('Essa fêmea possui bezerro nascido há menos de 9 meses.'); 
                $('#mensagem_erro_previsao').modal('show'); 
                return;
            }
            else {
                $(`#data_previsao${id_cobertura}`).val(data_previsao);
                gravar_diagnostico_positivo_femeas_servidas_monta(id_cobertura, value);
            }
        })
        .catch(function(erro) {
                $('#mensagem_erro_previsao .modal-body .mensagem_previsao').html('Erro não previsto: ' + erro); 
                $('#mensagem_erro_previsao').modal('show'); 
                return;
        });
    }
}         
 
// calcular data prenhes ao inserir femea na monta
function calcular_data_prenhes(id, value) { 
    var data_previsao = value;
    var id_cobertura = id.substring(13);

    let date = new Date(data_previsao);
    let ano = date.getFullYear();

    if (data_previsao!='' && ano>1999) {
        $(`#data_prenhes${id_cobertura}`).val('');

        var codigo_mae = $(`#codigo_id${id_cobertura}`).val();

        verificar_ultimo_nascimento(codigo_mae, data_previsao).then(function(resultado) {
            if (resultado<=9) {
                $(`#data_previsao${id_cobertura}`).val('');
                $('#mensagem_erro_previsao .modal-body .mensagem_previsao').html('Essa fêmea possui bezerro nascido há menos de 9 meses.'); 
                $('#mensagem_erro_previsao').modal('show'); 
                return;
            }
            else {
                let date = new Date(data_previsao);
                date.setDate(date.getDate() - 281); // para achar 282 dias tem que colocar 1 dia a menos no calculo

                var data_prenhes = date.getFullYear() +"-" + adicionaZero((date.getMonth() + 1)) + "-" + adicionaZero(date.getDate());
                $(`#data_prenhes${id_cobertura}`).val(data_prenhes);

                gravar_diagnostico_positivo_femeas_servidas_monta(id_cobertura, value);
            }
        })
        .catch(function(erro) {
                $('#mensagem_erro_previsao .modal-body .mensagem_previsao').html('Erro não previsto: ' + erro); 
                $('#mensagem_erro_previsao').modal('show'); 
                return;
        });
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

function prenhes_limpa_positiva(id, value) { 
    let id_cobertura = id.split("data_prenhes").pop();
    var opcaoP = document.querySelector(`#resultadoP${id_cobertura}`);
    opcaoP.checked = false; 

    if ($(`#data_prenhes${id_cobertura}`).val()=='') {
        $(`#data_previsao${id_cobertura}`).val('');
        gravar_limpa_diagnostico_positivo_femeas_servidas_monta(id_cobertura);
    }   
} 

function previsao_limpa_positiva(id, value) { 
    let id_cobertura = id.split("data_previsao").pop();
    var opcaoP = document.querySelector(`#resultadoP${id_cobertura}`);
    opcaoP.checked = false;    

    if ($(`#data_previsao${id_cobertura}`).val()=='') {
        $(`#data_prenhes${id_cobertura}`).val('');
        gravar_limpa_diagnostico_positivo_femeas_servidas_monta(id_cobertura);
    }   
} 

// limpa a opao Positiva para permitir gravar novamente caso tenha alterado 
// as Datas Prenhes e Previsão do Parto
// Garantimos que a variável seja global anexando-a ao objeto 'window'
/*window.dadosReplica = {
    valor: '',
    classe: '',
    idOrigem: ''
};

function verificarPreenchimentoEmMassa(idAtual, valorAtual, classeAlvo) {
    // 1. Só prossegue se o valor existir e tiver 10 caracteres (Ex: 2023-10-25)
    if (!valorAtual || valorAtual.length < 10) return;

    // 2. Validação extra: verifica se o ano é maior que 1900 para evitar datas parciais
    var ano = parseInt(valorAtual.split('-')[0]);
    if (ano < 1900) return;

    var todosInputs = document.querySelectorAll('.' + classeAlvo);
    var outrosVazios = true;

    for (var i = 0; i < todosInputs.length; i++) {
        // Se encontrarmos qualquer outro campo que já tenha valor, paramos tudo
        if (todosInputs[i].id !== idAtual && todosInputs[i].value !== "") {
            outrosVazios = false;
            break;
        }
    }

    // Só pergunta se houver mais de um campo e todos os outros estiverem limpos
    if (outrosVazios && todosInputs.length > 1) {
        window.dadosReplica = { 
            valor: valorAtual, 
            classe: classeAlvo, 
            idOrigem: idAtual 
        };
        
        $('#modalReplicarData').modal('show');
    }
}

// Inicialização dos eventos do Modal
$(document).ready(function() {
    // Usamos 'off' antes de 'on' para evitar que o evento seja registrado duplicado 
    // caso o script seja recarregado dinamicamente
    $(document).off('click', '#btnConfirmarReplica').on('click', '#btnConfirmarReplica', function() {
        var info = window.dadosReplica;
        var todosInputs = document.querySelectorAll('.' + info.classe);
        
        todosInputs.forEach(function(input) {
            if (input.id !== info.idOrigem) {
                input.value = info.valor;
                
                // Dispara o evento change para executar as funções de cálculo de cada linha
                // Usamos o disparador nativo para garantir compatibilidade com o onchange do PHP
                var evento = new Event('change');
                input.dispatchEvent(evento);
            }
        });

        $('#modalReplicarData').modal('hide');
    });
});*/

/*window.dadosReplica = {
    valor: '',
    classe: '',
    idOrigem: ''
};

// Variável de controle para o "Não perguntar novamente"
window.ignorarPergunta = false;

function verificarPreenchimentoEmMassa(idAtual, valorAtual, classeAlvo) {
    // SE o usuário marcou para não perguntar mais, saímos da função imediatamente
    if (window.ignorarPergunta) return;
    
    // 1. Só prossegue se a data estiver completa (10 caracteres: YYYY-MM-DD)
    if (!valorAtual || valorAtual.length < 10) return;

    var ano = parseInt(valorAtual.split('-')[0]);
    if (ano < 1900) return;

    var todosInputs = document.querySelectorAll('.' + classeAlvo);
    var camposVaziosEncontrados = 0;

    // 2. Percorre os campos para ver se existe ALGUÉM vazio
    for (var i = 0; i < todosInputs.length; i++) {
        if (todosInputs[i].id !== idAtual && (todosInputs[i].value === "" || todosInputs[i].value === null)) {
            camposVaziosEncontrados++;
        }
    }

    // 3. Se houver ao menos 1 campo vazio, abre o modal
    if (camposVaziosEncontrados > 0) {
        window.dadosReplica = { 
            valor: valorAtual, 
            classe: classeAlvo, 
            idOrigem: idAtual 
        };
        
        // Opcional: Atualiza o texto do modal para ser mais claro
        if(document.getElementById('textoModalReplicar')) {
            document.getElementById('textoModalReplicar').innerText = "Deseja replicar esta data para os outros " + camposVaziosEncontrados + " itens que estão vazios?";
        }

        $('#modalReplicarData').modal('show');
    }
}

// Inicialização dos eventos do Modal
$(document).ready(function() {
    $(document).off('click', '#btnConfirmarReplica').on('click', '#btnConfirmarReplica', function() {
        var info = window.dadosReplica;
        var todosInputs = document.querySelectorAll('.' + info.classe);
        
        todosInputs.forEach(function(input) {
            // SÓ preenche se o campo estiver vazio e não for o campo que o usuário acabou de digitar
            if (input.id !== info.idOrigem && (input.value === "" || input.value === null)) {
                input.value = info.valor;
                
                // Dispara o evento change para rodar os cálculos (ex: calcular_data_previsao)
                var evento = new Event('change');
                input.dispatchEvent(evento);
            }
        });

        $('#modalReplicarData').modal('hide');
    });
});
// Fim Algoritimo da IA
*/

// Novo Algorimo da IA com o check Box Não Fazar mais essa pergunta

window.dadosReplica = { valor: '', classe: '', idOrigem: '' };
window.ignorarPerguntaSempre = false;

function verificarPreenchimentoEmMassa(idAtual, valorAtual, classeAlvo) {
    if (window.ignorarPerguntaSempre) return;

    if (!valorAtual || valorAtual.length < 10) return;
    
    var todosInputs = document.querySelectorAll('.' + classeAlvo);
    var camposVaziosEncontrados = 0;

    for (var i = 0; i < todosInputs.length; i++) {
        if (todosInputs[i].id !== idAtual && (todosInputs[i].value === "" || todosInputs[i].value === null)) {
            camposVaziosEncontrados++;
        }
    }

    if (camposVaziosEncontrados > 0) {
        window.dadosReplica = { valor: valorAtual, classe: classeAlvo, idOrigem: idAtual };
        
        if(document.getElementById('textoModalReplicar')) {
            document.getElementById('textoModalReplicar').innerText = "Deseja replicar esta data para os outros " + camposVaziosEncontrados + " itens que estão vazios?";
        }

        // RESET: Volta o modal para o estado inicial toda vez que abre
        //$('#containerNaoPerguntar').hide();
        $('#chkNaoPerguntar').prop('checked', false);
        $('#btnNaoReplicar').text('Não'); 

        $('#modalReplicarData').modal('show');
    }
}

$(document).ready(function() {
    // Botão NÃO / FECHAR
    $(document).off('click', '#btnNaoReplicar').on('click', '#btnNaoReplicar', function() {
        //var container = $('#containerNaoPerguntar');
        
        //if (container.is(':hidden')) {
            // Primeiro clique: mostra o check e muda o texto
            //container.slideDown();
            //$(this).text('Fechar');
        //} else {
            // Segundo clique (já como "Fechar"): salva preferência se marcado e fecha
            if ($('#chkNaoPerguntar').is(':checked')) {
                window.ignorarPerguntaSempre = true;
            }
            $('#modalReplicarData').modal('hide');
        //}
    });

    // Botão SIM
    $(document).off('click', '#btnConfirmarReplica').on('click', '#btnConfirmarReplica', function() {
        var info = window.dadosReplica;
        
        // Se clicar em Sim com o check marcado, também não pergunta mais
        if ($('#chkNaoPerguntar').is(':checked')) {
            window.ignorarPerguntaSempre = true;
        }

        var todosInputs = document.querySelectorAll('.' + info.classe);
        todosInputs.forEach(function(input) {
            if (input.id !== info.idOrigem && (input.value === "" || input.value === null)) {
                input.value = info.valor;
                input.dispatchEvent(new Event('change'));
            }
        });

        $('#modalReplicarData').modal('hide');
    });
});

// Fim do Algoritimo da IA

$(document).ready(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});

$(window).resize(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});

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
    else {
        return true;
    }
}      

function adicionaZero(numero){
    if (numero <= 9) 
        return "0" + numero;
    else
        return numero; 
}

