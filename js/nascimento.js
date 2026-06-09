/**NASCIMENTO DE ANIMAIS*/
window.addEventListener("load", function(){
    $.post("lista_local.php", {tipo:0}, function(valor){
        $("select[name=local_id]").html(valor);
        consultar_pastos();
    });

    // Exibe filtros no reload da consulta nascimento 
    var filtro_local = $("#exibe_local").val();
 
    if (filtro_local!='' && filtro_local!=null) {
        var filtro_local = filtro_local.split(',');

        $.each(filtro_local, function(idx, val) {
            $('#codigo_local option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_local').selectpicker('refresh');

        var local = $("#codigo_local").val();

        if (local==null) {
            local=[''];
        }

        $.post("lista_estacao_monta_rel_indices.php", {local:local}, function(valor){
            $("select[name=codigo_estacao_monta]").html(valor);
            $('.selectpicker').selectpicker('refresh');

            var filtro_estacao = $("#exibe_estacao").val();
                 
            if (filtro_estacao!='' && filtro_estacao!=null) {
                var filtro_estacao = filtro_estacao.split(',');

                $.each(filtro_estacao, function(idx, val) {
                    $('#codigo_estacao_monta option[value=' + val + ']').attr('selected', true);
                });

                $('#codigo_estacao_monta').selectpicker('refresh');
            }

            var lista_nascimento_automatico = $("#lista_nascimento_automatico").val();

            if (lista_nascimento_automatico=="S") {
                consultar();
            }
        });
    }
    // Fim exibe filtros

    var grupo_usuario = $("#grupo_usuario").val();
    var controle_estoque = $("#controle_estoque").val();

    if ((grupo_usuario==01 || grupo_usuario==02) && controle_estoque=='I') {
        $(".parametros").show();
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

        var largura = screen.width;

        if( largura > 767 ) {  
            var objDiv = document.getElementById("modal_incluir");
            objDiv.scrollTop = objDiv.scrollHeight;
        }

        $('#modal_incluir').animate({scrollTop: 100},600);

        if (opcao_nascimento=='N') {
            $(".campos_data_mae_pai").show();
            $(".campos_id_aborto_lote").show();
            $(".campos_id_lote").show();
            $(".raca_id").show();
            $(".peso_animal").show();
            $(".label_data").html('* Data Nascimento');
            $(".label_pasto").html('* Pasto');
            $(".label_mae").html('* Nº Mãe');

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

    $('#tabela_nascimento').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "pageLength": 100,
        "order": [[ 2, "desc" ], [ 0, 'desc' ],[ 1, "desc" ]],
        "language": {
        "sSearch": "Busca:",
        "zeroRecords": "Nada encontrado",
        "info": "Total Registros: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        },

        "aoColumns": [
            null,
            null,
            { "sType": "date-br" },
            null,
            null,
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

    $('#tabela_nascimento_lote').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "pageLength": 100,
        "order": [ 0, 'desc' ],
        "language": {
        "sSearch": "Busca:",
        "zeroRecords": "Nada encontrado",
        "info": "Total Registros: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        },

        "aoColumns": [
            { "sType": "date-br" },
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

    $('#nascimento_animal').change(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const nascimento_animal = $("#nascimento_animal").val();

        if (nascimento_animal>data_atual) {
            $("#mensagem_erro_data").modal();
            $("#mensagem_erro_data .modal-body").html('A Data não pode ser maior que a data atual!');
            $("#nascimento_animal").val(data_atual);
            document.getElementById("nascimento_animal").style.borderColor = "#0076d7";
        }

        $("#codigo_mae_consulta").val('');
        $("#codigo_mae_animal").val('');
        $("#codigo_pai_animal").val('000000000');
        var local_id = $("#local_id").val();
        listar_estacao(local_id);
    });    

    $('#nascimento_animal').blur(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const nascimento_animal = $("#nascimento_animal").val();

        if (nascimento_animal=='') {
            $("#mensagem_erro_data").modal();
            $("#mensagem_erro_data .modal-body").html('A Data precisa ser informada!');
            $("#nascimento_animal").val(data_atual);
            document.getElementById("nascimento_animal").style.borderColor = "#0076d7";
        }
    });    

    $('#nascimento_animal').click(function(){
        $("#nascimento_animal").val('');
        document.getElementById("nascimento_animal").style.borderColor = "";
    });    

    $('#codigo_numerico_animal').change(function(){
        var codigo_alfa= $('#alfa_animal').val();
        var codigo_animal= $('#codigo_numerico_animal').val();
        var codigo_mae = $("#codigo_mae_animal").val();

        $.post("ler_animal_inclusao.php",{codigo_alfa: codigo_alfa,codigo_animal: codigo_animal, codigo_mae: codigo_mae}, function(valor){
            var php = valor.split("<|>");

            if (php[0]==1){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('O código ' + codigo_alfa + codigo_animal + ' já existe cadastrado no sistema para essa Mãe.');
                $('#alfa_animal').val($('#codigo_alfa_anterior').val());
                $('#codigo_numerico_animal').val($('#codigo_numerico_anterior').val());
                document.getElementById("codigo_numerico_animal").focus();
                return;
            }
        });
    });

    $('#alfa_animal').change(function(){
        var codigo_alfa= $('#alfa_animal').val();
        var codigo_animal= $('#codigo_numerico_animal').val();
        var codigo_mae = $("#codigo_mae_animal").val();

        $.post("ler_animal_inclusao.php",{codigo_alfa: codigo_alfa,codigo_animal: codigo_animal, codigo_mae: codigo_mae}, function(valor){
            var php = valor.split("<|>");

            if (php[0]==1){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('O código ' + codigo_alfa + codigo_animal + ' já existe cadastrado no sistema para essa Mãe.');
                $('#alfa_animal').val($('#codigo_alfa_anterior').val());
                $('#codigo_numerico_animal').val($('#codigo_numerico_anterior').val());
                document.getElementById("codigo_numerico_animal").focus();
                return;
            }
        });
    });

    $('.sexo_animal').change(function(){
        var codigo_alfa= $('#alfa_animal').val();
        var codigo_animal= $('#codigo_numerico_animal').val();
        var codigo_mae = $("#codigo_mae_animal").val();

        $.post("ler_animal_inclusao.php",{codigo_alfa: codigo_alfa,codigo_animal: codigo_animal, codigo_mae: codigo_mae}, function(valor){
            var php = valor.split("<|>");

            if (php[0]==1){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('O código ' + codigo_alfa + codigo_animal + ' já existe cadastrado no sistema para essa Mãe.');
                $('#alfa_animal').val($('#codigo_alfa_anterior').val());
                $('#codigo_numerico_animal').val($('#codigo_numerico_anterior').val());
                $("#F").prop("checked", false);
                $("#M").prop("checked", false);
                document.getElementById("codigo_numerico_animal").focus();
                return;
            }
        });
    });

    $('.cod_numerico').change(function(){
        var fazenda = new Array();
        var array_fazenda = "";
        var codigo_fazenda = document.getElementsByName("codigo_fazenda");

        var alfa = new Array();
        var array_alfa = "";
        var codigo_alfa = document.getElementsByName("cod_alfa");

        var numerico = new Array();
        var array_numerico = "";
        var codigo_numerico = document.getElementsByName("cod_numerico");


        for (var i = 0; i < codigo_fazenda.length; i++) {
            cod_fazenda = codigo_fazenda[i].value;
            fazenda.push(cod_fazenda);
            array_fazenda = fazenda.join("!");

            cod_alfa = codigo_alfa[i].value;
            alfa.push(cod_alfa);
            array_alfa = alfa.join("!");

            cod_numerico = codigo_numerico[i].value;
            numerico.push(cod_numerico);
            array_numerico = numerico.join("!");
        }

        $("#array_codigo_fazenda").val(array_fazenda);
        $("#array_codigo_alfa").val(array_alfa);
        $("#array_codigo_numerico").val(array_numerico);
    });

    // Acende o botão consultar se houver alteracao nos filtros da pesagem
    $('#data_inicial').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();

        var data_inicial = $('#data_inicial').val();

        if (data_inicial!='') {
            var local = $("#codigo_local").val();

            if (local==null) {
                local=[''];
            }

            $.post("lista_estacao_monta_rel_indices.php", {local:local}, function(valor){
                $("select[name=codigo_estacao_monta]").html(valor);
                $('.selectpicker').selectpicker('refresh');
            });
        }
    });

    $('#data_final').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });

    $('#codigo_estacao_monta').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();

        $('#data_inicial').val('');
        $('#data_final').val('');
    });

    $('#codigo_local').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();

        var local = $("#codigo_local").val();

        if (local==null) {
            local=[''];
        }

        $.post("lista_estacao_monta_rel_indices.php", {local:local}, function(valor){
            $("select[name=codigo_estacao_monta]").html(valor);
            $('.selectpicker').selectpicker('refresh');

        });
    });

    $('#tipo_ocorrencia').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });

    // Fim acendo botão 

    $("#situacao_principal").on("click input", function() {
        $(this).css("border", ""); 
    });

});

function incluir_novo() { 

    var grupo_usuario = $("#grupo_usuario").val();
    var controle_estoque = $("#controle_estoque").val();

    if ((grupo_usuario==01 || grupo_usuario==02) && controle_estoque=='I') {
        $(".parametros").show();
    }
    else {
        $(".parametros").hide();
    }

    $('a[href="#nascimento"]').tab('show');

    $("#local_id").show();
    $("#desc_local").hide();
    $("#pasto_id").show();
    $("#desc_pasto").hide();
    $('#mudar_sexo').hide();
    $(".confirma_gravar").attr("disabled", false);

    $("#dias_nascimento").val('');
    $("#cobertura_id").val('');
    $("#item_cobertura").val('');
    $("#data_inseminacao").val('');
    $("#num_mov_nascimento").val(0);
    $("#tipo_gravacao").val(0);
    $(".desc_novo_nascimento").html('');
    $(".tipo_estacao_monta").html('Estação de Monta:');

    $.post("lista_parametro_nascimento.php",{}, function(valor){
        var php = valor.split("<|>");

        if (php[0]==''){
            $("#F").prop("checked", false);
            $("#M").prop("checked", false);

            $("#pasto_id").val('000000000');
            $("#codigo_mae_animal").val('');
            $("#codigo_mae_consulta").val('');
            $("#codigo_pai_animal").val('000000000');
            $("#raca_id").val('');
            $("#pelagem_id").val('');

            $("#peso_animal").val('');
            $("#qtd_animal").val('');
            $('#modal_incluir .modal-title').html('Nascimento - Incluir');
            $('.confirma_gravar').html('Confirmar').removeClass('btn-danger').addClass('btn-primary');
            $(".confirma_gravar").attr("disabled", false);
            $('.confirma_gravar').show();
            $('.voltar_inclusao').show();
            $('.voltar').hide();

            $('#modal_incluir').modal('show');

            consultar_pastos();
        }
        else {
            var grupo_usuario = $("#grupo_usuario").val();
            if (grupo_usuario==01 || grupo_usuario==02) {

                var array_alfa = php[3].split('!');
                var array_numerico = php[4].split('!');
                var codigo_alfa = document.getElementsByName("cod_alfa");
                var codigo_numerico = document.getElementsByName("cod_numerico");

                for (var i = 0; i < array_numerico.length; i++) {
                    if (array_numerico[i]!='' && array_numerico[i]!=undefined) {
                        codigo_alfa[i].value=array_alfa[i];
                        codigo_numerico[i].value=array_numerico[i];
                    }
                }
            }

            $("#F").prop("checked", false);
            $("#M").prop("checked", false);
            $("#pasto_id").val('000000000');
            $("#codigo_mae_animal").val('');
            $("#codigo_mae_consulta").val('');
            $("#codigo_pai_animal").val('000000000');
            $("#raca_id").val('');
            $("#pelagem_id").val('');
            $("#peso_animal").val('');
            $("#qtd_animal").val('');
            $("#alfa_animal").val('');
            $("#codigo_numerico_animal").val('');
            $(".alfa_animal").hide();
            $(".codigo_numerico_animal").hide();
            $("#array_codigo_fazenda").val(php[2]);
            $("#array_codigo_alfa").val(php[3]);
            $("#array_codigo_numerico").val(php[4]);

            document.getElementById('alfa_animal').readOnly = false;
            document.getElementById('codigo_numerico_animal').readOnly = false;

            $('#modal_incluir .modal-title').html('Nascimento - Incluir');
            $('.confirma_gravar').html('Confirmar').removeClass('btn-danger').addClass('btn-success');
            $(".confirma_gravar").attr("disabled", false);
            $('.confirma_gravar').show();
            $('#modal_incluir').modal('show');
            consultar_pastos();
        }
    });
}

function editar_animal(array_animal) {
    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='I') {
        $(".pelagem_id").show();
    }
    else {
        $(".pelagem_id").hide();
    }

    $(".parametros").hide();
    $(".qtd_animal").hide();

    array_edicao = array_animal.split('|');

    if (array_edicao[18]=='N') {
        $("#opcao_nascimento").prop("checked", true);
        $("#opcao_absorcao").prop("disabled", true);
        $("#opcao_aborto").prop("disabled", true);
        $("#opcao_morte").prop("disabled", true);
    }
    else if (array_edicao[18]=='B'){
        $("#opcao_absorcao").prop("checked", true);
        $("#opcao_nascimento").prop("disabled", true);
        $("#opcao_aborto").prop("disabled", true);
        $("#opcao_morte").prop("disabled", true);
    }
    else if (array_edicao[18]=='A'){
        $("#opcao_aborto").prop("checked", true);
        $("#opcao_nascimento").prop("disabled", true);
        $("#opcao_absorcao").prop("disabled", true);
        $("#opcao_morte").prop("disabled", true);
    }
    else {
        $("#opcao_morte").prop("checked", true);
        $("#opcao_nascimento").prop("disabled", true);
        $("#opcao_absorcao").prop("disabled", true);
        $("#opcao_aborto").prop("disabled", true);
    }

    $(".ocorrencias").show();
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
    $("#codigo_mae_consulta").prop("disabled", true);
    $("#codigo_pai_animal").val('000000000');
    $("#raca_id").val('');
    $("#pelagem_id").val('');
    $("#peso_animal").val('');
    $("#qtd_animal").val('');
    $(".label_opcao").html('Opção');

    if (array_edicao[18]=='N') {
        $(".campos_data_mae_pai").show();
        $(".campos_id_aborto_lote").show();
        $(".campos_id_lote").show();
        $(".raca_id").show();
        $(".peso_animal").show();
        $(".label_data").html('* Data Nascimento');
        $(".label_pasto").html('* Pasto');
        $(".label_mae").html('* Nº Mãe');

        if (controle_estoque=='I') {
            $(".ocorrencias").show();
            $(".nascimento_id").show();
            $(".qtd_animal").hide();
            $(".pelagem_id").show();
            $(".codigo_pai_animal").show();
            $(".codigo_mae_animal").show();
            $("#ultima_estacao").html(array_edicao[19]); 
        }
        else {
            $(".ocorrencias").hide();
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

    var tipo_movimentacao = array_edicao[17];

    $("#codigo_animal_id").val(array_edicao[0]);

    $("#num_mov_nascimento").val(array_edicao[14]);
    $("#tipo_gravacao").val(1);

    if (array_edicao[3]=='F') {
        $("#F").prop("checked", true);
    }
    else if (array_edicao[3]=='M'){
        $("#M").prop("checked", true);
    }

    if (controle_estoque=='I') {
        var input = document.querySelector("#F");
        input.disabled = true;    
        var input = document.querySelector("#M");
        input.disabled = true;  
        $('#mudar_sexo').show();
    }
    else {
        $('#mudar_sexo').hide();
    }  

    $("#raca_id").val(array_edicao[4]);
    $("#pelagem_id").val(array_edicao[5]);
    $("#peso_animal").val(array_edicao[11]);
    $("#nascimento_animal").val(array_edicao[6]);
    $("#data_ocorrencia").val(array_edicao[6]);
    $("#local_id").val(array_edicao[7]);
    $("#desc_local").val(array_edicao[13]);

    $("#local_id").hide();
    $("#desc_local").show();

    consultar_pastos_edicao(array_edicao[7], array_edicao[8]);

    $("#desc_pasto").val(array_edicao[15]);

    $("#pasto_id").hide();
    $("#desc_pasto").show();

    $.post("ler_parametro_nascimento.php",{local_id:array_edicao[7]}, function(valor){
        var php = valor.split("<|>");

        if (php[4]!=''){
            if (php[3]!='') {
                $("#alfa_animal").val(array_edicao[1]);
                $(".alfa_animal").show();
            }
            else {
                $("#alfa_animal").val('');
                $(".alfa_animal").hide();
            }

            if (php[4]!='') {
                $("#codigo_numerico_animal").val(Number(array_edicao[2]));
                $(".codigo_numerico_animal").show();
                $("#codigo_pai_animal").val(array_edicao[9]);
                $("#codigo_mae_animal").val(array_edicao[10]);
                $("#codigo_mae_consulta").val(array_edicao[12]);
            }
            else {
                $("#codigo_numerico_animal").val('');
                $("#codigo_pai_animal").val('000000000');
                $("#codigo_mae_animal").val('');
                $("#codigo_mae_consulta").val('');
                $(".codigo_numerico_animal").hide();
            }
        }
        else {
            $("#alfa_animal").val('');
            $("#codigo_numerico_animal").val('');
            $("#codigo_pai_animal").val('000000000');
            $("#codigo_mae_animal").val('');
            $("#codigo_mae_consulta").val('');
            $(".alfa_animal").hide();
            $(".codigo_numerico_animal").hide();
        }

        document.getElementById('alfa_animal').readOnly = true;
        document.getElementById('codigo_numerico_animal').readOnly = true;
        $('#modal_incluir .modal-title').html('Nascimento - Editar');
        $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-success');
        $('#modal_incluir').modal('show');

        if (tipo_movimentacao=='A' || tipo_movimentacao=='B' || array_edicao[0]==999999999) {
            //$('a[href="#natimorto"]').tab('show');
            //$('a[href="#nascimento"]').hide();
            $('#mudar_sexo').hide();
            $('.confirma_gravar').hide();
        }
        else {
            //$("#local_id_natimorto").val('000000000');
            //$("#pasto_id_natimorto").val('000000000');
            //$("#ocorrencia_natimorto").val('0');
            //$("#data_ocorrencia").val('');
            //$("#desc_local_natimorto").val('');
            //$("#desc_pasto_natimorto").val('');
            //$("#codigo_mae_consulta_natimorto").val('');
            //$("#codigo_mae_natimorto").val('');
            //$("#N").prop("checked", true);
            //$('a[href="#natimorto"]').hide();
            $('.confirma_gravar').show();
        }
    })
}

function enviar_lixeira(array_animal){
    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='I') {
        $(".pelagem_id").show();
    }
    else {
        $(".pelagem_id").hide();
    }

    $(".parametros").hide();
    $(".qtd_animal").hide();

    array_edicao = array_animal.split('|');

    if (array_edicao[18]=='N') {
        $("#opcao_nascimento").prop("checked", true);
        $("#opcao_absorcao").prop("disabled", true);
        $("#opcao_aborto").prop("disabled", true);
        $("#opcao_morte").prop("disabled", true);
    }
    else if (array_edicao[18]=='B'){
        $("#opcao_absorcao").prop("checked", true);
        $("#opcao_nascimento").prop("disabled", true);
        $("#opcao_aborto").prop("disabled", true);
        $("#opcao_morte").prop("disabled", true);
    }
    else if (array_edicao[18]=='A'){
        $("#opcao_aborto").prop("checked", true);
        $("#opcao_nascimento").prop("disabled", true);
        $("#opcao_absorcao").prop("disabled", true);
        $("#opcao_morte").prop("disabled", true);
    }
    else {
        $("#opcao_morte").prop("checked", true);
        $("#opcao_nascimento").prop("disabled", true);
        $("#opcao_absorcao").prop("disabled", true);
        $("#opcao_aborto").prop("disabled", true);
    }

    $(".ocorrencias").show();
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
    $("#codigo_mae_consulta").prop("disabled", true);
    $("#codigo_pai_animal").val('000000000');
    $("#codigo_pai_animal").prop("disabled", true);
    $("#raca_id").val('');
    $("#raca_id").prop('disabled',true);
    $("#pelagem_id").val('');
    $("#pelagem_id").prop('disabled',true);
    $("#peso_animal").val('');
    $("#peso_animal").prop('disabled',true);
    $("#qtd_animal").val('');
    $("#nascimento_animal").prop("disabled", true);

    if (array_edicao[18]=='N') {
        $(".campos_data_mae_pai").show();
        $(".campos_id_aborto_lote").show();
        $(".campos_id_lote").show();
        $(".raca_id").show();
        $(".peso_animal").show();
        $(".label_data").html('* Data Nascimento');
        $(".label_pasto").html('* Pasto');
        $(".label_mae").html('* Nº Mãe');

        if (controle_estoque=='I') {
            $(".ocorrencias").show();
            $(".nascimento_id").show();
            $(".qtd_animal").hide();
            $(".pelagem_id").show();
            $(".codigo_pai_animal").show();
            $(".codigo_mae_animal").show();
            $("#ultima_estacao").html(array_edicao[19]); 
        }
        else {
            $(".ocorrencias").hide();
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

    var tipo_movimentacao = array_edicao[17];

    $("#codigo_animal_id").val(array_edicao[0]);

    $("#num_mov_nascimento").val(array_edicao[14]);
    $("#tipo_gravacao").val(2);

    if (array_edicao[3]=='F') {
        $("#F").prop("checked", true);
    }
    else if (array_edicao[3]=='M'){
        $("#M").prop("checked", true);
    }

    if (controle_estoque=='I') {
        $('#mudar_sexo').show();
    }
    else {
        $('#mudar_sexo').hide();
    }  

    $("#raca_id").val(array_edicao[4]);
    $("#pelagem_id").val(array_edicao[5]);
    $("#peso_animal").val(array_edicao[11]);

    $("#nascimento_animal").val(array_edicao[6]);
    $("#local_id").val(array_edicao[7]);
    $("#desc_local").val(array_edicao[13]);

    $("#local_id").hide();
    $("#desc_local").show();

    $("#cobertura_id").val(array_edicao[20]);
    $("#item_cobertura").val(array_edicao[21]);

    consultar_pastos_edicao(array_edicao[7], array_edicao[8]);

    $('#mudar_sexo').hide();
    $("#desc_pasto").val(array_edicao[15]);
    $("#pasto_id").hide();
    $("#desc_pasto").show();

    $.post("ler_parametro_nascimento.php",{local_id:array_edicao[7]}, function(valor){
        var php = valor.split("<|>");

        if (php[4]!=''){
            if (php[3]!='') {
                $("#alfa_animal").val(array_edicao[1]);
                $(".alfa_animal").show();
            }
            else {
                $("#alfa_animal").val('');
                $(".alfa_animal").hide();
            }

            if (php[4]!='') {
                $("#codigo_numerico_animal").val(Number(array_edicao[2]));
                $(".codigo_numerico_animal").show();
                $("#codigo_pai_animal").val(array_edicao[9]);
                $("#codigo_mae_animal").val(array_edicao[10]);
                $("#codigo_mae_consulta").val(array_edicao[12]);
            }
            else {
                $("#codigo_numerico_animal").val('');
                $("#codigo_pai_animal").val('000000000');
                $("#codigo_mae_animal").val('');
                $("#codigo_mae_consulta").val('');
                $(".codigo_numerico_animal").hide();
            }
        }
        else {
            $("#alfa_animal").val('');
            $("#codigo_numerico_animal").val('');
            $("#codigo_pai_animal").val('000000000');
            $("#codigo_mae_animal").val('');
            $("#codigo_mae_consulta").val('');
            $(".alfa_animal").hide();
            $(".codigo_numerico_animal").hide();
        }

        document.getElementById('alfa_animal').readOnly = true;
        document.getElementById('codigo_numerico_animal').readOnly = true;
        $('#modal_incluir .modal-title').html('Nascimento - Excluir');
        $('.confirma_gravar').html('Confirmar Exclusão').removeClass('btn-danger').addClass('btn-danger');
        $('#modal_incluir').modal('show');
    })
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

function gravar_nascimento() {
    var tipo_gravacao = $("#tipo_gravacao").val();
    var opcao_nascimento = $("input[name='opcao_nascimento']:checked").val();
    var cobertura_id = $("#cobertura_id").val();
    var tipo_cobertura = $("#tipo_cobertura").val();

    $('#nascimento_erro').modal('hide');

    if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_animal').serialize();

            if (opcao_nascimento=='N') {
                $("#aguardar").modal();

                $.ajax({
                    type: "POST",
                    url: 'excluir_nascimento.php',
                    data: dados,
                    success: function(data){
                        $('#aguardar').modal('hide');
                        $('.confirma_gravar').attr('disabled', false);
                        $('.confirma_gravar').html('Confirmar Exclusão');
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
            else {
                $("#aguardar").modal();

                $.ajax({
                    type: "POST",
                    url: 'excluir_nascimento_aborto_natimorto.php',
                    data: dados,
                    success: function(data){
                        $('#aguardar').modal('hide');
                        $('.confirma_gravar').attr('disabled', false);
                        $('.confirma_gravar').html('Confirmar Exclusão');
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
                            $('#modal_incluir').modal('hide');
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
                        $('#modal_incluir').modal('hide');
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
                        $('#mensagem_retorno_inclusao .modal-title').html('Aborto/natimorto - Mensagem');
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

function gravar_parametros() {
    var fazenda = new Array();
    var array_fazenda = "";
    var codigo_fazenda = document.getElementsByName("codigo_fazenda");

    var alfa = new Array();
    var array_alfa = "";
    var codigo_alfa = document.getElementsByName("cod_alfa");

    var numerico = new Array();
    var array_numerico = "";
    var codigo_numerico = document.getElementsByName("cod_numerico");


    for (var i = 0; i < codigo_fazenda.length; i++) {
        cod_fazenda = codigo_fazenda[i].value;
        fazenda.push(cod_fazenda);
        array_fazenda = fazenda.join("!");

        cod_alfa = codigo_alfa[i].value;
        alfa.push(cod_alfa);
        array_alfa = alfa.join("!");

        cod_numerico = codigo_numerico[i].value;
        numerico.push(cod_numerico);
        array_numerico = numerico.join("!");
    }

    $("#array_codigo_fazenda").val(array_fazenda);
    $("#array_codigo_alfa").val(array_alfa);
    $("#array_codigo_numerico").val(array_numerico);

    var dados = $('#form_gravar_animal').serialize();
    $.ajax({
        type: "POST",
        url: 'gravar_parametro_nascimento.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $('#mensagem_erro .modal-title').html('Paramentro - Mensagem');
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else {
                $("#mensagem_retorno_inclusao").modal();
                $('#mensagem_retorno_inclusao .modal-title').html('Parametro');
                $("#mensagem_retorno_inclusao .modal-body").html(data.message);
            }
        }
    });
}

// validar se o codigo da mae foi selecionado na lista suspensa da digitacao
function validar_codigo_mae(){
    var codigo_mae= $('#codigo_mae_animal').val();

    if (codigo_mae=="" || codigo_mae==0) {
        $("#mensagem_erro").modal();
        $('#mensagem_erro .modal-title').html('Nascimento - Mensagem');
        $("#mensagem_erro .modal-body").html('Informe o codigo da mae');
        return;
    }

}

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
                $("#nascimento_aborto_natimorto .modal-body .mensagem_aborto_natimorto").html('Essa fêmea teve aborto ou natimorto há menos de 9 meses');
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
                    else if (php[10]==1 && php[18]<=90) {
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

function consultar(){

    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var tipo = $("#tipo_ocorrencia").val();
    var local = $("#codigo_local").val();

    if (local==null) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione a(s) Fazenda(s)');
        return;
    }

    if (tipo==null) {
        tipo=[''];
    }

    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='I') {
        var codigo_estacao_monta = $("#codigo_estacao_monta").val();

        if (codigo_estacao_monta==null && data_inicial=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Selecione a Estação de Monta ou informe o Período');
            return;
        }

        if (codigo_estacao_monta==null) {
            codigo_estacao_monta=[''];
            estacao_filtro='';

            if (data_inicial=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe o Período');
                return;
            }

            if (data_final<data_inicial) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('A Data Final não pode ser menor que a Data Inicial');
                return;
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
        }
        else {
            var options = $("#codigo_estacao_monta option:selected");
            var estacao_filtro = [];

            $(options).each(function () {
                var desc = $(this).bind("#codigo_estacao_monta").text();
                estacao_filtro.push(desc.trim());
            });

            if (estacao_filtro != "") {
                estacao_filtro = "Estação: " + estacao_filtro + "->";
            } 
            else {
                estacao_filtro = "Estação: Todas->";
            }

            var periodo = '';
        }
    }
    else {
        if (data_inicial=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe o Período');
            return;
        }

        if (data_final<data_inicial) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('A Data Final não pode ser menor que a Data Inicial');
            return;
        }

        var estacao_filtro = '';

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
    }

    var options = $("#codigo_local option:selected");
    var codigo_local_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_local").text();
        codigo_local_filtro.push(desc.trim());
    });

    if (codigo_local_filtro != "") {
        codigo_local_filtro = "Fazenda: " + codigo_local_filtro + "->";
    } 
    else {
        codigo_local_filtro = "Fazenda: Todas->";
    }

    var options = $("#tipo_ocorrencia option:selected");
    var ocorrencia_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#tipo_ocorrencia").text();
        ocorrencia_filtro.push(desc.trim());
    });

    if (ocorrencia_filtro != "") {
        ocorrencia_filtro = "Situação: " + ocorrencia_filtro + "->";
    } 
    else {
        ocorrencia_filtro = "Situação: Todas->";
    }

    var descricao_filtro =
        codigo_local_filtro +
        estacao_filtro +
        ocorrencia_filtro +
        periodo;

    $(".digitar_filtros").hide();
    $(".filtros_consulta").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".descricao_filtro").html(descricao_filtro);
    $('.voltar').show();

    $("#aguardar").modal();

    if (controle_estoque=='I') {
        $.post("form_lista_nascimento.php", {
            local:local, 
            tipo:tipo, data_inicial:data_inicial, 
            data_final:data_final,
            estacao:codigo_estacao_monta},
            function(valor){ 

            $('#aguardar').modal('hide');
            $("div[id=lista_nascimentos]").html(valor); 
        });
        return;
    }
    else {
        $.post("form_lista_nascimento_lote.php", {local:local, tipo:tipo, data_inicial:data_inicial, data_final:data_final},
            function(valor){ 

            $('#aguardar').modal('hide');
            $("div[id=lista_nascimentos]").html(valor); 
        });
        return;
    }
}

function exibe_mais_filtros() {
    $(".digitar_filtros").show();
    $(".mais_filtros").hide();
    $(".menos_filtros").show();
    $(".consultar").hide();
    $(".lista_contas").hide();
    $('.voltar').hide();
}

function exibe_menos_filtros() {
    $(".digitar_filtros").hide();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".lista_contas").show();
    $('.voltar').show();
}

function consultar_pastos(){
    var local_id = $("#local_id").val();
    $("#codigo_mae_consulta").val('');
    $("#codigo_mae_animal").val('');
    $("#codigo_pai_animal").val('000000000');
    $(".desc_novo_nascimento").html('');

    $.ajax({
        type: 'post',
        url: 'monta_select_pasto.php',
        data: {
            'local_id': local_id
        },
        success: function(data){
            $('#pasto_id').html(data);
            listar_estacao(local_id);
        }
    });

    if (local_id!=0) {
        $.post("ler_parametro_nascimento.php",{local_id:local_id}, function(valor){
            var php = valor.split("<|>");

            if (php[4]!=''){
                if (php[3]!='') {
                    $("#alfa_animal").val(php[3]);
                    $("#codigo_alfa_anterior").val(php[3]);
                    $(".alfa_animal").show();
                }
                else {
                    $("#alfa_animal").val('');
                    $("#codigo_alfa_anterior").val('');
                    $(".alfa_animal").hide();
                }

                if (php[4]!='') {
                    $("#codigo_numerico_animal").val(Number(php[4]));
                    $("#codigo_numerico_anterior").val(php[4]);
                    $("#ultima_estacao").html(php[6]);
                    $("#estacao_monta_id").val(php[5]);

                    if (php[7]=='S') {
                        $(".icon_nascimentos_previstos").show();
                    }
                    else {
                        $(".icon_nascimentos_previstos").hide();
                    }
                }
                else {
                    $("#codigo_numerico_animal").val('');
                    $("#codigo_numerico_anterior").val('');
                }
            }
            else {
                $("#alfa_animal").val('');
                $("#codigo_alfa_anterior").val('');
                $("#codigo_numerico_animal").val('');
                $("#codigo_numerico_anterior").val('');
            }
        })
    }
    else {
        $("#alfa_animal").val('');
        $("#codigo_alfa_anterior").val('');
        $("#codigo_numerico_animal").val('');
        $("#codigo_numerico_anterior").val('');
    }
}

function listar_estacao(value){
    var data_nascimento = $("#nascimento_animal").val();

    $("select[name=estacao_monta]").html('');

    $.post("lista_estacao_monta_nascimento.php", {local:value, data_nascimento: data_nascimento}, function(valor){
        $("select[name=estacao_monta]").html(valor);
    });
}


// lista femeas servidas chamado pela tela de nascimento
function lista_femeas_servidas() {
    var local = $("#local_id").val();
    var estacao = $("#ultima_estacao").html();
    var id_estacao = $("#estacao_monta_id").val();

    location.href='form_cobertura_animais_diagnostico.php?local=' + local +
     '&estacao=' + estacao + '&id_estacao=' + id_estacao;
}

function consultar_pastos_edicao(local_id, pasto_id){
    $.ajax({
        type: 'post',
        url: 'monta_select_pasto.php',
        data: {
            'local_id': local_id
        },
        success: function(data){
            $('#pasto_id').html(data);
            $("#pasto_id").val(pasto_id);        }
    });
}

// Funcões para a montagem da descrição dos lotes de animais no pasto
function abrir_modal_descricao_lote() {
    var id_pasto = $('#id_pasto_destino').val();
    var desc_pasto = $("#desc_pasto_destino").val();
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

    if (itens<6) {
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
    $('#modal_composicao_descricao_lote').modal('hide');

    gravar_descricao_lote_digitacao();
}

// Grava a Descrição do Lote quando for digitado 
// Chamada quando clicar na descricao do lote ou Criar novo lote na transferencia
function gravar_descricao_lote_digitacao() {
    var pasto_origem = $("#id_pasto_destino").val();
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
                    $("#mensagem_retorno_descricao_lote").modal(); 
                    $("#mensagem_retorno_descricao_lote .modal-body").html(data.message);
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

Number.prototype.AddZero= function(b,c){
    var  l= (String(b|| 10).length - String(this).length)+1;
    return l> 0? new Array(l).join(c|| '0')+this : this;
}
