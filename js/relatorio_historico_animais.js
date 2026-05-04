/**TABELA DE ANIMAIS*/
let divFiltroReproducaoVisivel = false;

window.addEventListener("load", function(event) {
    // Exibe filtros quando faz reload
    var filtro_local = $("#exibe_local").val(); 
 
    if (filtro_local!='' && filtro_local!=null) {
        var filtro_local = filtro_local.split(',');

        $.each(filtro_local, function(idx, val) {
            $('#codigo_local_filtro option[value=' + val + ']').attr('selected', true);
            $('#codigo_local_filtro_filtro option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_local_filtro').selectpicker('refresh');
        $('#codigo_local_filtro_filtro').selectpicker('refresh');
    }

    var filtro_categoria = $("#exibe_categoria").val();
 
    if (filtro_categoria!='' && filtro_categoria!=null) {
        var filtro_categoria = filtro_categoria.split(',');

        $.each(filtro_categoria, function(idx, val) {
            $('#codigo_categoria_filtro option[value=' + val + ']').attr('selected', true);
            $('#codigo_categoria_filtro_filtro option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_categoria_filtro').selectpicker('refresh');
        $('#codigo_categoria_filtro_filtro').selectpicker('refresh');
    }
    // Fim exibe filtros

    $("#vendido").prop("checked", false);
    $("#morte").prop("checked", false);
    $("#outro").prop("checked", false);
    $('.situacao').hide();

    $(".reprod").hide();

    var controle_estoque = $("#controle_estoque").val();
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

$(document).ready(function(){
    $(".aba_dados").click(function(){
        $('a[href="#dados"]').tab('show');
    });

    $(".aba_registros").click(function(){
        $('a[href="#registros"]').tab('show');
    });

    $('#tabela_animais').DataTable({
        "responsive": true,
        "paging":   true,
        "ordering": true,
        "info":     true,
        "pageLength": 100,
        "order": [[ 1, "asc" ], [ 0, 'asc' ]],
        "language": {
        "oPaginate": {
            "sFirst": "Primeira",
            "sLast": "Última",
            "sNext": "Próxima",
            "sPrevious": "Anterior"
        },
        "sSearch": "Busca:",
        "lengthMenu": "Mostrando _MENU_ registros por página",
        "zeroRecords": "Nada encontrado",
        "info": "Registros Listados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });

    $(".tipo_rel").click(function(){
        var tipo_rel = $("input[name='tipo_rel']:checked").val();

        if (tipo_rel=='I') {
            $('.geral').hide();
            $('.codigo').show();
            $('.filtro_aplicado').hide();
        }
        else {
            $('.geral').show();
            $('.codigo').hide();
            aplicar_filtros();
        }
    });

    $('#macho').on('change', function() {
        var macho = $('#macho');
        var femea = $('#femea');

        if (!macho.is(":checked") && !femea.is(":checked")) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe o sexo!');
            return;
        }

        if (macho.is(":checked") && !femea.is(":checked")){
            $('.abrir_filtro_reproducao').hide();
            $('.filtro_reproducao').hide();
            divFiltroReproducaoVisivel = false;
        }

        if (femea.is(":checked")){
            $('.abrir_filtro_reproducao').show();
            $('.filtro_reproducao').hide();
            divFiltroReproducaoVisivel = false;

        }

        if ((macho.is(":checked") && femea.is(":checked")) || 
            (!macho.is(":checked") && femea.is(":checked"))){

            var ativo_nao = $('#nao_filtro');

            if (ativo_nao.is(":checked")) {
                $('.abrir_filtro_reproducao').hide();
                $('.filtro_reproducao').hide();
                divFiltroReproducaoVisivel = false;

            }
            else {
                $('.abrir_filtro_reproducao').show();
                $('.filtro_reproducao').hide();
                divFiltroReproducaoVisivel = false;

            }
        }

        limpar_filtros_reproducao();
        exibe_filtro();
    });

    $('#femea').on('change', function() {
        var femea = $('#femea');
        var macho = $('#macho');

        if (!macho.is(":checked") && !femea.is(":checked")) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe o sexo!');
            return;
        }

        if (macho.is(":checked") && !femea.is(":checked")){
            $('.abrir_filtro_reproducao').hide();
            $('.filtro_reproducao').hide();
            divFiltroReproducaoVisivel = false;

        }

        if (femea.is(":checked")){
            $('.abrir_filtro_reproducao').show();
            $('.filtro_reproducao').hide();
            divFiltroReproducaoVisivel = false;

        }

        if ((macho.is(":checked") && femea.is(":checked")) || 
            (!macho.is(":checked") && femea.is(":checked"))){

            var ativo_nao = $('#nao_filtro');

            if (ativo_nao.is(":checked")) {
                $('.abrir_filtro_reproducao').hide();
                $('.filtro_reproducao').hide();
                divFiltroReproducaoVisivel = false;

            }
            else {
                $('.abrir_filtro_reproducao').show();
                $('.filtro_reproducao').hide();
                divFiltroReproducaoVisivel = false;

            }
        }

        limpar_filtros_reproducao();
        exibe_filtro();
    });

    $('#nao_filtro').change(function(){
        var ativo_nao = $('#nao_filtro');

        if (ativo_nao.is(":checked")) {
            $("#vendido").prop("checked", true);
            $("#morte").prop("checked", true);
            $("#outro").prop("checked", true);
            $('.situacao').show();
            $('.abrir_filtro_reproducao').hide();
            $('.filtro_reproducao').hide();
            divFiltroReproducaoVisivel = false;

        }
        else {
            $("#vendido").prop("checked", false);
            $("#morte").prop("checked", false);
            $("#outro").prop("checked", false);
            $('.situacao').hide();

            var femea = $('#femea');
            var macho = $('#macho');

            if (macho.is(":checked") && !femea.is(":checked")){
                $('.abrir_filtro_reproducao').hide();
                $('.filtro_reproducao').hide();
                divFiltroReproducaoVisivel = false;

            }

            if (femea.is(":checked")){
                $('.abrir_filtro_reproducao').show();
                $('.filtro_reproducao').hide();
                divFiltroReproducaoVisivel = false;

            }
        }

        limpar_filtros_reproducao();
        exibe_filtro();
    });

    $('#vendido').change(function(){
        var vendido = $('#vendido');
        var morte = $('#morte');
        var outra = $('#outro');

        if (vendido.is(":checked") || morte.is(":checked") || outra.is(":checked")) {
            $('.situacao').show();
        }
        else {
            $('.situacao').hide();
            $("#nao_filtro").prop("checked", false);

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
        }
    });

    $('#morte').change(function(){
        var vendido = $('#vendido');
        var morte = $('#morte');
        var outra = $('#outro');

        if (vendido.is(":checked") || morte.is(":checked") || outra.is(":checked")) {
            $('.situacao').show();
        }
        else {
            $('.situacao').hide();
            $("#nao_filtro").prop("checked", false);

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
        }
    });

    $('#outro').change(function(){
        var vendido = $('#vendido');
        var morte = $('#morte');
        var outra = $('#outro');

        if (vendido.is(":checked") || morte.is(":checked") || outra.is(":checked")) {
            $('.situacao').show();
        }
        else {
            $('.situacao').hide();
            $("#nao_filtro").prop("checked", false);

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
        }
    });

    $('#codigo_local_filtro').change(function(){
        $("#codigo_local_filtro_filtro").val([]);
        $('#codigo_local_filtro_filtro').selectpicker('val', '');

        var local = $("#codigo_local_filtro").val();
        $('#codigo_local_filtro_filtro').selectpicker('val', local);
        aplicar_filtros();

        var ativo_sim = '';
        var ativo_nao = '';

        var options = $('#codigo_local_filtro option:selected');
        $(options).each(function(){
            var local = $(this).bind('#codigo_local_filtro').val();

            if (local!='9999999999') {
                ativo_sim = 'S';
            }
            else if (local=='9999999999') {
                ativo_nao = 'S';
            }
        });

        if (ativo_sim == 'S') {
            $("#sim_filtro").prop("checked", true);
            $("#vendido").prop("checked", false);
            $("#morte").prop("checked", false);
            $("#outro").prop("checked", false);
            $('.situacao').hide();
        }

        if (ativo_nao == 'S') {
            $("#nao_filtro").prop("checked", true);
            $("#vendido").prop("checked", true);
            $("#morte").prop("checked", true);
            $("#outro").prop("checked", true);
            $('.situacao').show();
        }

        if (ativo_sim == '' && ativo_nao == '') {
            $("#sim_filtro").prop("checked", true);
            $("#nao_filtro").prop("checked", false);
            $("#vendido").prop("checked", false);
            $("#morte").prop("checked", false);
            $("#outro").prop("checked", false);
            $('.situacao').hide();
        }

        //listar_historico_animais(3); // exibe filtros
    });

    $('#codigo_local_filtro_filtro').change(function(){
        $("#codigo_local_filtro").val([]);
        $('#codigo_local_filtro').selectpicker('val', '');

        var local = $("#codigo_local_filtro_filtro").val();
        $('#codigo_local_filtro').selectpicker('val', local);

        var ativo_sim = '';
        var ativo_nao = '';

        var options = $('#codigo_local_filtro_filtro option:selected');
        $(options).each(function(){
            var local = $(this).bind('#codigo_local_filtro_filtro').val();

            if (local!='9999999999') {
                ativo_sim = 'S';
            }
            else if (local=='9999999999') {
                ativo_nao = 'S';
            }
        });

            if (ativo_sim == 'S') {
                $("#sim_filtro").prop("checked", true);
                $("#vendido").prop("checked", false);
                $("#morte").prop("checked", false);
                $("#outro").prop("checked", false);
                $('.situacao').hide();
            }

            if (ativo_nao == 'S') {
                $("#nao_filtro").prop("checked", true);
                $("#vendido").prop("checked", true);
                $("#morte").prop("checked", true);
                $("#outro").prop("checked", true);
                $('.situacao').show();
            }

            if (ativo_sim == '' && ativo_nao == '') {
                $("#sim_filtro").prop("checked", true);
                $("#nao_filtro").prop("checked", false);
                $("#vendido").prop("checked", false);
                $("#morte").prop("checked", false);
                $("#outro").prop("checked", false);
                $('.situacao').hide();
            }

        //listar_historico_animais(3); // exibe filtros
    });

    $('#codigo_categoria_filtro').change(function(){
        $("#codigo_categoria_filtro_filtro").val([]);

        var categoriasSelecionadas = $("#codigo_categoria_filtro").val(); 
        $('#codigo_categoria_filtro_filtro').selectpicker('val', categoriasSelecionadas);

        if (categoriasSelecionadas && categoriasSelecionadas.includes('001') && divFiltroReproducaoVisivel) {
            divFiltroReproducaoVisivel=true;
            $("#mensagem_filtro_reproducao").modal();
            return; 
        }

        aplicar_filtros();
    });

    $('#codigo_categoria_filtro_filtro').change(function(){
        $("#codigo_categoria_filtro").val([]);

        var categoriasSelecionadas = $("#codigo_categoria_filtro_filtro").val(); 
        $('#codigo_categoria_filtro').selectpicker('val', categoriasSelecionadas);

        if (categoriasSelecionadas && categoriasSelecionadas.includes('001') && divFiltroReproducaoVisivel) {
            divFiltroReproducaoVisivel = true;
            $("#mensagem_filtro_reproducao").modal();
            return; 
        }

        aplicar_filtros();
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

});

function filtros() {
    $("#codigo_number_filtro").val('');
    $('#modal_filtros').modal('show');
}

function limpar_filtros(){
    $("#macho").prop("checked", true);
    $("#femea").prop("checked", true);
    $("#sim_filtro").prop("checked", true);
    $("#vendido").prop("checked", false);
    $("#morte").prop("checked", false);
    $("#outro").prop("checked", false);
    $('.situacao').hide();
    $("#nao_filtro").prop("checked", false);
    $("#codigo_local_filtro_filtro").val([]);
    $('#codigo_local_filtro_filtro').selectpicker('val', '');
    $("#codigo_local_filtro").val([]);
    $('#codigo_local_filtro').selectpicker('val', '');
    $("#codigo_estacao_filtro").val([]);
    $('#codigo_estacao_filtro').selectpicker('val', '');
    $("#codigo_categoria_filtro_filtro").val([]);
    $('#codigo_categoria_filtro_filtro').selectpicker('val', '');
    $("#codigo_categoria_filtro").val([]);
    $('#codigo_categoria_filtro').selectpicker('val', '');
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
    $("#num_natimorto_de_filtro").val('');
    $("#num_natimorto_ate_filtro").val('');
    $("#vacas_paridas").prop("checked", false);
    $("#paridas_ate").val('');
    $("#vacas_solteiras").prop("checked", false);
    $("#vacas_prenhes").prop("checked", false);
    $("#descarte").prop("checked", false);
    $("#descarte_nao").prop("checked", false);
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
    $("#num_natimorto_de_filtro").val('');
    $("#num_natimorto_ate_filtro").val('');
    $("#vacas_paridas").prop("checked", false);
    $("#vacas_solteiras").prop("checked", false);
    $("#vacas_prenhes").prop("checked", false);
    $("#descarte").prop("checked", false);
    $("#descarte_nao").prop("checked", false);
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

function abrir_filtro_reproducao() {
    var femea = $('#femea');
    var macho = $('#macho');
    var selectElement = document.getElementById('codigo_categoria_filtro_filtro');
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

    var selectElement = document.getElementById('codigo_categoria_filtro');
    var optionToDeselect = selectElement.querySelector('option[value="001"]');
    if (optionToDeselect) {
        optionToDeselect.selected = false;
        $('#codigo_categoria_filtro').selectpicker('refresh');    
    }

    var selectElement = document.getElementById('codigo_categoria_filtro_filtro');
    var optionToDeselect = selectElement.querySelector('option[value="001"]');
    if (optionToDeselect) {
        optionToDeselect.selected = false;
        $('#codigo_categoria_filtro_filtro').selectpicker('refresh');    
    }

    let valoresParaMarcar = ['002', '003', '004', '005'];

    var selectId = '#codigo_categoria_filtro_filtro';
    $(selectId).selectpicker();
    $(selectId).selectpicker('refresh');
    var valoresSelecionados = $(selectId).val() || [];
    
    if (valoresSelecionados.length === 0) {
        $(selectId).val(valoresParaMarcar);
        $(selectId).selectpicker('refresh'); 
    }

    $("#codigo_categoria_filtro").val([]);

    var categoriasSelecionadas = $("#codigo_categoria_filtro_filtro").val(); 
    $('#codigo_categoria_filtro').selectpicker('val', categoriasSelecionadas);
    exibe_filtro(); 
}

function aplicar_filtros() {
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
        var num_natimorto_de = $("#num_natimorto_de_filtro").val();
        var num_natimorto_ate = $("#num_natimorto_ate_filtro").val();
        var previsao_parto_de = $("#previsao_parto_de_filtro").val();
        var previsao_parto_ate = $("#previsao_parto_ate_filtro").val();
        var data_paricao_de = $("#data_paricao_de_filtro").val();
        var data_paricao_ate = $("#data_paricao_ate_filtro").val();
        var filtro_estacao = $("#codigo_estacao_filtro").val();

        var ativo_sim = $('#sim_filtro');
        var ativo_nao = $('#nao_filtro');

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

        if (num_natimorto_de!='' || num_natimorto_ate!='') {
            if (num_natimorto_de=='') {
                $("#mensagem_erro_filtro").modal();
                $("#mensagem_erro_filtro .modal-body").html('Nº Natimortos (de) não pode ser vazio!');
                document.getElementById("num_natimorto_de_filtro").focus();
                document.getElementById("num_natimorto_de_filtro").style.borderColor = "red";
                return;
            }

            if (num_natimorto_ate=='') {
                $("#mensagem_erro_filtro").modal();
                $("#mensagem_erro_filtro .modal-body").html('Nº Natimortos (até) não pode ser vazio!');
                document.getElementById("num_natimorto_ate_filtro").focus();
                document.getElementById("num_natimorto_ate_filtro").style.borderColor = "red";
                return;
            }

            var num_natimorto_de = parseInt($("#num_natimorto_de_filtro").val());
            var num_natimorto_ate = parseInt($("#num_natimorto_ate_filtro").val());

            if (num_natimorto_de > num_natimorto_ate) {
                $("#mensagem_erro_filtro").modal();
                $("#mensagem_erro_filtro .modal-body").html('Nº Natimortos (até) não pode ser menor que o Nº Natimortos (de)!');
                document.getElementById("num_natimorto_ate_filtro").focus();
                document.getElementById("num_natimorto_ate_filtro").style.borderColor = "red";
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

function exibe_filtro(){
    var peso_nasc_inicial = $("#peso_inicial_nasc_filtro").val();
    var peso_nasc_final = $("#peso_final_nasc_filtro").val();
    var peso_desmama_inicial = $("#peso_inicial_desmama_filtro").val();
    var peso_desmama_final = $("#peso_final_desmama_filtro").val();
    var peso_ult_inicial = $("#peso_inicial_ultimo_filtro").val();
    var peso_ult_final = $("#peso_final_ultimo_filtro").val();
    var data_nasc_inicial = $("#data_nasc_inicial_filtro").val();
    var data_nasc_final = $("#data_nasc_final_filtro").val();
    var estacao = $("#codigo_estacao_filtro").val();

    var options = $('#codigo_local_filtro_filtro option:selected');
    var local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_local_filtro_filtro').text();
        local_filtro.push( desc.trim() );
    });

    if (local_filtro!=''){
        local_filtro = local_filtro;
    }
    else {
        local_filtro = '';
    }

    var options = $('#codigo_categoria_filtro_filtro option:selected');
    var categoria_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_categoria_filtro_filtro').text();
        categoria_filtro.push( desc.trim() );
    });

    if (categoria_filtro!=''){
        categoria_filtro = '->Categorias:'+categoria_filtro;
    }
    else {
        categoria_filtro = '';
    }

    var ativo_sim = $('#sim_filtro');
    var ativo_nao = $('#nao_filtro');

    if (ativo_sim.is(":checked") && ativo_nao.is(":checked")) {
        var ativo=['Todos'];
    }
    else if (ativo_sim.is(":checked")){
        var ativo=['S'];
    }
    else if (ativo_nao.is(":checked")){
        var ativo=['N'];
    }

    if (ativo=='S') {
        filtro_ativo = 'Ativo:Sim';
    }
    else if (ativo=='N'){
        filtro_ativo = 'Ativo:Não';
    } 
    else {
        filtro_ativo = 'Ativo:Sim;Não';
    }

    if (local_filtro != '' || categoria_filtro != ''){
        filtro_ativo = '->' + filtro_ativo;
    }

    var vendido = $('#vendido');
    if (vendido.is(":checked")){
        var situacao_vendido = 'S';
    }
    else {
        var situacao_vendido = 'N';
    }

    var morte = $('#morte');
    if (morte.is(":checked")){
        var situacao_morte = 'S';
    }
    else {
        var situacao_morte = 'N';
    }

    var outra = $('#outro');
    if (outra.is(":checked")){
        var situacao_outra = 'S';
    }
    else {
        var situacao_outra = 'N';
    }

    if (ativo!='S') {
        if (situacao_vendido=='S') {
            filtro_vendido = '->Vendidos:Sim';
        }
        else {
            filtro_vendido = '->Vendidos:Não';
        }

        if (situacao_morte=='S') {
            filtro_morte = '->Mortos:Sim';
        }
        else {
            filtro_morte = '->Mortos:Não';
        }

        if (situacao_outra=='S') {
            filtro_outra = '->Outra Saídas:Sim';
        }
        else {
            filtro_outra = '->Outra Saídas:Não';
        }
    }
    else {
        filtro_vendido = '';
        filtro_morte = '';
        filtro_outra = '';
    }

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

    var options = $('#codigo_categoria_filtro_filtro option:selected');
    var categoria_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_categoria_filtro_filtro').text();
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

    var num_natimorto_de = $("#num_natimorto_de_filtro").val();
    var num_natimorto_ate = $("#num_natimorto_ate_filtro").val();

    if (num_natimorto_de!='' || num_natimorto_ate!='') {
        natimorto_filtro = '->Natimortos: de ' + num_natimorto_de + ' ate ' + num_natimorto_ate;
    }
    else {
        natimorto_filtro = '';
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

    if ($("#descarte").is(":checked") == true && 
        $("#descarte_nao").is(":checked") == true) {
        filtro_descarte = '->Descarte:S;N';
    }
    else if ($("#descarte").is(":checked") == true){
        filtro_descarte = '->Descarte:S';
    }
    else if ($("#descarte_nao").is(":checked") == true){
        filtro_descarte = '->Descarte:N';
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

    if ($("#monta_natural").is(":checked") == true){
        filtro_monta_natural = '->Monta Natural';
    }
    else {
        filtro_monta_natural = '';
    }

    if ($("#iatf").is(":checked") == true){
        filtro_iatf = '->IATF';
    }
    else {
        filtro_iatf = '';
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

    var descricao_filtro = local_filtro+
            categoria_filtro+
            filtro_ativo+
            filtro_vendido+
            filtro_morte+
            filtro_outra+
            '->Sexo:'+sexo+
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
            aborto_filtro+
            natimorto_filtro;

    $("#filtro_aplicado").html('Filtros: ' + descricao_filtro);
    $('.filtro_aplicado').show();
}

function redigita_animal_filtro(){
    $('#modal_filtros').modal('show');
}

document.addEventListener('DOMContentLoaded', function() {
    // Seleciona todos os botões com a classe 'btn-editar' (você pode dar essa classe a eles)
    const botoesEditar = document.querySelectorAll('.btn-editar');

    botoesEditar.forEach(botao => {
        // Adiciona um evento de clique a cada botão
        botao.addEventListener('click', function(event) {
            // Evita que o link abra uma nova página
            event.preventDefault();

            // Pega o valor do atributo 'data-animal' (que é a nossa string JSON)
            const jsonString = this.getAttribute('data-animal');

            // Converte a string JSON para um objeto JavaScript
            const animalObjeto = JSON.parse(jsonString);

            // Chama a função de edição, passando o objeto completo
            editar_animal(animalObjeto);
        });
    });
});



function digita_valor(){
    $('#peso_inicial_nasc_filtro').bind('keypress',mask.money);
    $('#peso_final_nasc_filtro').bind('keypress',mask.money);
    $('#peso_inicial_desmama_filtro').bind('keypress',mask.money);
    $('#peso_final_desmama_filtro').bind('keypress',mask.money);
    $('#peso_inicial_ultimo_filtro').bind('keypress',mask.money);
    $('#peso_final_ultimo_filtro').bind('keypress',mask.money);
}

function peso_inicial_nasc_filtro(){
    var peso_inicial_nasc_filtro = $("#peso_inicial_nasc_filtro").val();
    if (verifica_virgula(peso_inicial_nasc_filtro)==',') {
        peso_inicial_nasc_filtro = replace_valor(peso_inicial_nasc_filtro);
    }

    $("#peso_inicial_nasc_filtro").val(formatMoney(peso_inicial_nasc_filtro));
}

function peso_final_nasc_filtro(){
    var peso_final_nasc_filtro = $("#peso_final_nasc_filtro").val();
    if (verifica_virgula(peso_final_nasc_filtro)==',') {
        peso_final_nasc_filtro = replace_valor(peso_final_nasc_filtro);
    }

    $("#peso_final_nasc_filtro").val(formatMoney(peso_final_nasc_filtro));
}

function peso_inicial_desmama_filtro(){
    var peso_inicial_desmama_filtro = $("#peso_inicial_desmama_filtro").val();
    if (verifica_virgula(peso_inicial_desmama_filtro)==',') {
        peso_inicial_desmama_filtro = replace_valor(peso_inicial_desmama_filtro);
    }

    $("#peso_inicial_desmama_filtro").val(formatMoney(peso_inicial_desmama_filtro));
}

function peso_final_desmama_filtro(){
    var peso_final_desmama_filtro = $("#peso_final_desmama_filtro").val();
    if (verifica_virgula(peso_final_desmama_filtro)==',') {
        peso_final_desmama_filtro = replace_valor(peso_final_desmama_filtro);
    }

    $("#peso_final_desmama_filtro").val(formatMoney(peso_final_desmama_filtro));
}

function peso_inicial_ultimo_filtro(){
    var peso_inicial_ultimo_filtro = $("#peso_inicial_ultimo_filtro").val();
    if (verifica_virgula(peso_inicial_ultimo_filtro)==',') {
        peso_inicial_ultimo_filtro = replace_valor(peso_inicial_ultimo_filtro);
    }

    $("#peso_inicial_ultimo_filtro").val(formatMoney(peso_inicial_ultimo_filtro));
}

function peso_final_ultimo_filtro(){
    var peso_final_ultimo_filtro = $("#peso_final_ultimo_filtro").val();
    if (verifica_virgula(peso_final_ultimo_filtro)==',') {
        peso_final_ultimo_filtro = replace_valor(peso_final_ultimo_filtro);
    }

    $("#peso_final_ultimo_filtro").val(formatMoney(peso_final_ultimo_filtro));
}

function exibe_peso_desmama(){
    var peso_desmama_animal = $("#peso_desmama_animal").val();
    if (verifica_virgula(peso_desmama_animal)==',') {
        peso_desmama_animal = replace_valor(peso_desmama_animal);
    }

    $("#peso_desmama_animal").val(formatMoney(peso_desmama_animal));
}

function exibe_ultimo_peso(){
    var ultimo_peso_animal = $("#ultimo_peso_animal").val();
    if (verifica_virgula(ultimo_peso_animal)==',') {
        ultimo_peso_animal = replace_valor(ultimo_peso_animal);
    }

    $("#ultimo_peso_animal").val(formatMoney(ultimo_peso_animal));
}

// RELATORIO


function veriricar_tipo_relatorio(opcao) {

    var tipo_rel = $("input[name='tipo_rel']:checked").val();

    if (tipo_rel=='I') {
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
                    listar_historico_animais(opcao);
                }
            });
        }
        else {
            $("#mensagem_erro_animal_filtro").modal();
            $("#codigo_number_filtro").val('');
            document.getElementById("codigo_number_filtro").focus();
            document.getElementById("codigo_number_filtro").style.borderColor = "red";
            return;
        }
    }
    else {
        listar_historico_animais(opcao);
    }
}

function listar_historico_animais(opcao){
    var tipo_rel = $("input[name='tipo_rel']:checked").val();
    var codigo_alfa_numerico = $("#codigo_number_filtro").val();
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
    var num_natimorto_de = $("#num_natimorto_de_filtro").val();
    var num_natimorto_ate =$("#num_natimorto_ate_filtro").val();
    var filtro_estacao = $("#codigo_estacao_filtro").val();
    var descricao_filtro_codificada = $("#filtro_aplicado").html();
    var descricao_filtro = descricao_filtro_codificada
    .replace(/&gt;/g, '>')  // Substitui todas as ocorrências de '&gt;' por '>'
    .replace(/&lt;/g, '<')  // (Opcional, mas boa prática) Substitui '&lt;' por '<'
    .replace(/&amp;/g, '&'); // (Opcional) Se tiver & (E comercial)

    if (filtro_estacao==null) {
        var array_estacao= new Array();
    }
    else {
        var array_estacao = new Array();
        var valor = new Array();

        for (i = 0; i <= filtro_estacao.length; i++) {
            valor[i]=filtro_estacao[i];
        }

        var array_estacao=valor.join(",");
    }

    var todos_os_locais = [];

    $("#codigo_local_filtro option").each(function() {
        todos_os_locais.push($(this).val());
    });

    var array_local;

    if (local == null || local.length === 0) {
        array_local = todos_os_locais.join(",");
    }
    else {
        array_local = local.join(",");
    }

    if (origem==null) {
        var array_origem= new Array();
    }
    else {
        var array_origem = new Array();
        var valor = new Array();

        for (i = 0; i <= origem.length; i++) {
            valor[i]=origem[i];
        }
        var array_origem=valor.join(",");
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

    if (codigos_racas==null) {
        var array_raca= new Array();
    }
    else {
        var array_raca = new Array();
        var valor = new Array();

        for (i = 0; i <= codigos_racas.length; i++) {
            valor[i]=codigos_racas[i];
        }

        var array_raca=valor.join(",");
    }

    if (codigos_pais==null) {
        var array_pai= new Array();
    }
    else {
        var array_pai = new Array();
        var valor = new Array();

        for (i = 0; i <= codigos_pais.length; i++) {
            valor[i]=codigos_pais[i];
        }

        var array_pai=valor.join(",");
    }

    if (codigos_maes==null) {
        var array_mae= new Array();
    }
    else {
        var array_mae = new Array();
        var valor = new Array();

        for (i = 0; i <= codigos_maes.length; i++) {
            valor[i]=codigos_maes[i];
        }

        var array_mae=valor.join(",");
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

    var ativo_sim = $('#sim_filtro');
    var ativo_nao = $('#nao_filtro');

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
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe Ativo Sim ou Não!');
        return;
    }

    var vendido = $('#vendido');

    if (vendido.is(":checked")){
        var situacao_vendido = 'S';
    }
    else {
        var situacao_vendido = 'N';
    }

    var morte = $('#morte');
    if (morte.is(":checked")){
        var situacao_morte = 'S';
    }
    else {
        var situacao_morte = 'N';
    }

    var outra = $('#outro');
    if (outra.is(":checked")){
        var situacao_outra = 'S';
    }
    else {
        var situacao_outra = 'N';
    }

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

    if (num_natimorto_de!='' && num_natimorto_ate!='') {
        var filtro_num_natimorto = 'S';
        filtro_reproducao = 'S'
    }
    else {
        var filtro_num_natimorto = 'N';
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

    if ($("#descarte").is(":checked") == true && 
        $("#descarte_nao").is(":checked") == true) {
        var filtro_descarte = '';
    }
    else if ($("#descarte").is(":checked") == true){
        var filtro_descarte = 'S';
        filtro_reproducao = 'S';
    }
    else if ($("#descarte_nao").is(":checked") == true){
        var filtro_descarte = 'N';
        filtro_reproducao = 'S';
    }
    else {
        var filtro_descarte = '';
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

    if (tipo_rel=='I') {
        opc_rel_filtro='Individual';
    }
    else{
        opc_rel_filtro='Geral';
    }

    var origem_relatorio = $("#origem_relatorio").val();

    $("#aguardar").modal();

    if (opcao=='1') {
        if (tipo_rel=='I') {
            location.href='form_lista_historico_animais_individual_rel.php?codigo_alfa_numerico=' + codigo_alfa_numerico +
            '&origem_relatorio=' + origem_relatorio;
        }
        else {
            location.href='form_lista_historico_animais_geral_rel.php?descricao_filtro=' + descricao_filtro + 
            '&tipo_rel=' + tipo_rel + 
            '&local=' + array_local + 
            '&origem=' + array_origem + 
            '&categoria=' + array_categoria + 
            '&codigos_racas=' + array_raca + 
            '&codigos_pais=' + array_pai + 
            '&codigos_maes=' + array_mae + 
            '&peso_nasc_inicial=' + peso_nasc_inicial + 
            '&peso_nasc_final=' + peso_nasc_final + 
            '&peso_desmama_inicial=' + peso_desmama_inicial + 
            '&peso_desmama_final=' + peso_desmama_final + 
            '&peso_ult_inicial=' + peso_ult_inicial + 
            '&peso_ult_final=' + peso_ult_final + 
            '&data_nasc_inicial=' + data_nasc_inicial + 
            '&data_nasc_final=' + data_nasc_final + 
            '&ativo=' + ativo + 
            '&num_parto_de=' + num_parto_de +
            '&num_parto_ate=' + num_parto_ate +
            '&num_aborto_de=' + num_aborto_de + 
            '&num_aborto_ate=' + num_aborto_ate +
            '&num_natimorto_de=' + num_natimorto_de + 
            '&num_natimorto_ate=' + num_natimorto_ate +
            '&previsao_parto_de=' + previsao_parto_de +
            '&previsao_parto_ate=' + previsao_parto_ate + 
            '&data_paricao_de=' + data_paricao_de +
            '&data_paricao_ate=' +  data_paricao_ate + 
            '&filtro_positivas=' + filtro_positivas +
            '&filtro_negativas=' + filtro_negativas + 
            '&filtro_estacao=' + array_estacao +
            '&sexo=' + sexo + 
            '&filtro_reproducao=' +  filtro_reproducao +
            '&filtro_num_parto=' + filtro_num_parto +    
            '&filtro_num_aborto=' + filtro_num_aborto +
            '&filtro_num_natimorto=' + filtro_num_natimorto +
            '&filtro_previsao_parto=' +  filtro_previsao_parto +
            '&filtro_data_paricao=' +  filtro_data_paricao +
            '&filtro_vacas_paridas=' +  filtro_vacas_paridas +
            '&filtro_vacas_solteiras=' +  filtro_vacas_solteiras +
            '&filtro_vacas_prenhas=' +  filtro_vacas_prenhas +
            '&filtro_descarte=' +  filtro_descarte + 
            '&filtro_monta_natural=' + filtro_monta_natural +
            '&situacao_vendido=' + situacao_vendido +
            '&situacao_morte=' + situacao_morte +
            '&situacao_outra=' + situacao_outra + 
            '&origem_relatorio=' + origem_relatorio;
        }
    }
    else {
        if (tipo_rel=='I') {
            location.href='rel_lista_historico_animais_individual_excel.php?codigo_alfa_numerico=' + codigo_alfa_numerico;
            limpar_filtros();
            tout = setTimeout('limpar_tela()', 2000);
        }
        else {
            location.href='rel_lista_historico_animais_geral_excel.php?descricao_filtro=' + descricao_filtro + 
            '&tipo_rel=' + tipo_rel + 
            '&local=' + array_local + 
            '&origem=' + array_origem + 
            '&categoria=' + array_categoria + 
            '&codigos_racas=' + array_raca + 
            '&codigos_pais=' + array_pai + 
            '&codigos_maes=' + array_mae + 
            '&peso_nasc_inicial=' + peso_nasc_inicial + 
            '&peso_nasc_final=' + peso_nasc_final + 
            '&peso_desmama_inicial=' + peso_desmama_inicial + 
            '&peso_desmama_final=' + peso_desmama_final + 
            '&peso_ult_inicial=' + peso_ult_inicial + 
            '&peso_ult_final=' + peso_ult_final + 
            '&data_nasc_inicial=' + data_nasc_inicial + 
            '&data_nasc_final=' + data_nasc_final + 
            '&ativo=' + ativo + 
            '&num_parto_de=' + num_parto_de +
            '&num_parto_ate=' + num_parto_ate +
            '&num_aborto_de=' + num_aborto_de + 
            '&num_aborto_ate=' + num_aborto_ate +
            '&num_natimorto_de=' + num_natimorto_de + 
            '&num_natimorto_ate=' + num_natimorto_ate +
            '&previsao_parto_de=' + previsao_parto_de +
            '&previsao_parto_ate=' + previsao_parto_ate + 
            '&data_paricao_de=' + data_paricao_de +
            '&data_paricao_ate=' +  data_paricao_ate + 
            '&filtro_positivas=' + filtro_positivas +
            '&filtro_negativas=' + filtro_negativas + 
            '&filtro_estacao=' + array_estacao +
            '&sexo=' + sexo + 
            '&filtro_reproducao=' +  filtro_reproducao +
            '&filtro_num_parto=' + filtro_num_parto +    
            '&filtro_num_aborto=' + filtro_num_aborto +
            '&filtro_num_natimorto=' + filtro_num_natimorto +
            '&filtro_previsao_parto=' +  filtro_previsao_parto +
            '&filtro_data_paricao=' +  filtro_data_paricao +
            '&filtro_vacas_paridas=' +  filtro_vacas_paridas +
            '&filtro_vacas_solteiras=' +  filtro_vacas_solteiras +
            '&filtro_vacas_prenhas=' +  filtro_vacas_prenhas +
            '&filtro_descarte=' +  filtro_descarte + 
            '&filtro_monta_natural=' + filtro_monta_natural +
            '&situacao_vendido=' + situacao_vendido +
            '&situacao_morte=' + situacao_morte +
            '&situacao_outra=' + situacao_outra + 
            '&origem_relatorio=' + origem_relatorio;

            limpar_filtros();
            tout = setTimeout('limpar_tela()', 4000);
        }
    }
}

function lista_historico_animais_individual_excel() {
    var codigo_alfa_numerico = $("#codigo_number_filtro").val();

    $("#aguardar").modal();

    location.href='rel_lista_historico_animais_individual_excel.php?codigo_alfa_numerico=' + codigo_alfa_numerico;
    
    tout = setTimeout('limpar_tela()', 3000);
}

function lista_historico_animais_excel(){
    var tipo_rel = $("#tipo_rel").val();
    var array_local = $("#codigo_local_filtro").val();
    var array_categoria = $("#codigo_categoria_filtro").val();
    var array_origem = $("#codigo_origem_filtro").val();
    var array_mae = $("#codigo_mae_filtro").val();
    var array_pai = $("#codigo_pai_filtro").val();
    var array_raca = $("#codigo_raca_filtro").val();
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
    var num_natimorto_de = $("#num_natimorto_de_filtro").val();
    var num_natimorto_ate =$("#num_natimorto_ate_filtro").val();
    var ativo =$("#ativo").val();
    var sexo =$("#sexo").val();
    var array_estacao = $("#codigo_estacao_filtro").val();
    var filtro_positivas = $("#filtro_positivas").val();
    var filtro_negativas = $("#filtro_negativas").val();
    var filtro_reproducao= $("#filtro_reproducao").val();
    var filtro_num_parto= $("#filtro_num_parto").val();   
    var filtro_num_aborto= $("#filtro_num_aborto").val();
    var filtro_num_natimorto= $("#filtro_num_natimorto").val();
    var filtro_previsao_parto=  $("#filtro_previsao_parto").val();
    var filtro_data_paricao=  $("#filtro_data_paricao").val();
    var filtro_vacas_paridas=  $("#filtro_vacas_paridas").val();
    var filtro_vacas_solteiras=  $("#filtro_vacas_solteiras").val();
    var filtro_vacas_prenhas=  $("#filtro_vacas_prenhas").val();
    var filtro_descarte=  $("#filtro_descarte").val();
    var filtro_monta_natural= $("#filtro_monta_natural").val();
    var situacao_vendido = $("#situacao_vendido").val();
    var situacao_morte = $("#situacao_morte").val();
    var situacao_outra = $("#situacao_outra").val();
    var descricao_filtro_codificada = $("#filtro_aplicado").val();
    var descricao_filtro = descricao_filtro_codificada
    .replace(/&gt;/g, '>')  // Substitui todas as ocorrências de '&gt;' por '>'
    .replace(/&lt;/g, '<')  // (Opcional, mas boa prática) Substitui '&lt;' por '<'
    .replace(/&amp;/g, '&'); // (Opcional) Se tiver & (E comercial)

    $("#aguardar").modal();

    location.href='rel_lista_historico_animais_geral_excel.php?descricao_filtro=' + descricao_filtro + 
            '&tipo_rel=' + tipo_rel + 
            '&local=' + array_local + 
            '&origem=' + array_origem + 
            '&categoria=' + array_categoria + 
            '&codigos_racas=' + array_raca + 
            '&codigos_pais=' + array_pai + 
            '&codigos_maes=' + array_mae + 
            '&peso_nasc_inicial=' + peso_nasc_inicial + 
            '&peso_nasc_final=' + peso_nasc_final + 
            '&peso_desmama_inicial=' + peso_desmama_inicial + 
            '&peso_desmama_final=' + peso_desmama_final + 
            '&peso_ult_inicial=' + peso_ult_inicial + 
            '&peso_ult_final=' + peso_ult_final + 
            '&data_nasc_inicial=' + data_nasc_inicial + 
            '&data_nasc_final=' + data_nasc_final + 
            '&ativo=' + ativo + 
            '&num_parto_de=' + num_parto_de +
            '&num_parto_ate=' + num_parto_ate +
            '&num_aborto_de=' + num_aborto_de + 
            '&num_aborto_ate=' + num_aborto_ate +
            '&num_natimorto_de=' + num_natimorto_de + 
            '&num_natimorto_ate=' + num_natimorto_ate +
            '&previsao_parto_de=' + previsao_parto_de +
            '&previsao_parto_ate=' + previsao_parto_ate + 
            '&data_paricao_de=' + data_paricao_de +
            '&data_paricao_ate=' +  data_paricao_ate + 
            '&filtro_positivas=' + filtro_positivas +
            '&filtro_negativas=' + filtro_negativas + 
            '&filtro_estacao=' + array_estacao +
            '&sexo=' + sexo + 
            '&filtro_reproducao=' +  filtro_reproducao +
            '&filtro_num_parto=' + filtro_num_parto +    
            '&filtro_num_aborto=' + filtro_num_aborto +
            '&filtro_num_natimorto=' + filtro_num_natimorto +
            '&filtro_previsao_parto=' +  filtro_previsao_parto +
            '&filtro_data_paricao=' +  filtro_data_paricao +
            '&filtro_vacas_paridas=' +  filtro_vacas_paridas +
            '&filtro_vacas_solteiras=' +  filtro_vacas_solteiras +
            '&filtro_vacas_prenhas=' +  filtro_vacas_prenhas +
            '&filtro_descarte=' +  filtro_descarte + 
            '&filtro_monta_natural=' + filtro_monta_natural +
            '&situacao_vendido=' + situacao_vendido +
            '&situacao_morte=' + situacao_morte +
            '&situacao_outra=' + situacao_outra + 
            '&origem_relatorio=' + origem_relatorio;
    tout = setTimeout('limpar_tela()', 6000);
}

function exibir_filtro(){
    $('.filtro_exibido').show();
    $('.esconder').show();
    $('.exibir').hide();
    $('.voltar').hide();
}

function esconder_filtro(){
    $('.filtro_exibido').hide();
    $('.esconder').hide();
    $('.exibir').show();
    $('.voltar').show();
}

function limpar_tela(){
    $('#aguardar').modal('hide');
}

function voltar_filtro() {
    var origem_relatorio = $("#origem_relatorio").val();
    location.href='form_rel_historico_animais.php?tipo='+origem_relatorio;
}

function voltar_relatorios() {
    var origem_relatorio = $("#origem_relatorio").val();

    if (origem_relatorio==1) {
        location.href='form_relatorios_produtivos.php?tipo='+origem_relatorio;
    }
    else {
        location.href='form_pesagem_animais.php?tipo='+origem_relatorio;
    }
}

/*function limpar_selecao_local() {
    $('#codigo_local_filtro_filtro').val(''); // Ou $('#meuSelect').val([]);
    $('#codigo_local_filtro_filtro').selectpicker('refresh');

    $('#codigo_local_filtro').val(''); // Ou $('#meuSelect').val([]);
    $('#codigo_local_filtro').selectpicker('refresh');

    const linkElement = document.querySelector('.informacao');
    if (linkElement) {
        linkElement.style.color = 'darkgray';
    }
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
    $('#codigo_categoria_filtro_filtro').val(''); // Ou $('#meuSelect').val([]);
    $('#codigo_categoria_filtro_filtro').selectpicker('refresh');

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


