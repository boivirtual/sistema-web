/**TABELA DE MATRIZES*/
window.addEventListener("load", function(){
    if ($("input[name='tipo_registro']:checked").val()=='C') {
        $('.estacao_monta').show();
    }

    var erro_importar_excel =  $("#erro_importar_excel").val();

    if (erro_importar_excel!='' && erro_importar_excel!=undefined) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(erro_importar_excel);
        $("#erro_importar_excel").val('');
    }

    $.post("lista_local.php", {tipo:0}, function(valor){
        $("select[name=local_id]").html(valor);
    });

    var local = $("#codigo_local").val();

    if (local!=undefined) {
        var estacao_monta = $("#estacao_monta_anterior").val();
        $.post("lista_estacao_monta.php", {local:local, estacao_monta: estacao_monta}, function(valor){
            $("select[name=estacao_monta]").html(valor);

            var listar = $('#lista_automatica').val();

            if (listar=='S') {
                consultar();
            }
        });
    }
    else {
        var listar = $('#lista_automatica').val();

        if (listar=='S') {
            consultar();
        }
    }

    $.post("lista_parametro_estacao.php",{}, function(valor){
        var php = valor.split("<|>");
        var array_data_inicial = php[0].split('!');
        var array_data_final= php[1].split('!');
        var array_codigo_local = php[2].split('!');
        var array_codigo_id = php[5].split('!');
        var array_nome_estacao = php[6].split('!');
        var array_estacao_atual = php[7].split('!');
    
        var data_inicial = document.getElementsByName("inicio_estacao");
        var data_final = document.getElementsByName("fim_estacao");
        var codigo_id = document.getElementsByName("id_parametro");
        var nome_estacao = document.getElementsByName("nome_estacao");
        var codigo_local = document.getElementsByName("codigo_fazenda");
        var selecao_estacao = document.getElementsByName("lista_estacoes");

        for (var i = 0; i < array_codigo_id.length; i++) {

            for (var j = 0; j < array_codigo_local.length; j++) {
                if (codigo_local[i].value==array_codigo_local[j] && 
                    array_estacao_atual[j]=='S') {

                    data_inicial[i].value=array_data_inicial[j];
                    data_final[i].value=array_data_final[j];
                    codigo_id[i].value=array_codigo_id[j];
                    nome_estacao[i].value=array_nome_estacao[j];
                }

                local = codigo_local[i].value;
                estacao_monta = codigo_id[i].value;
                listarEstacao(local, estacao_monta, i);       
            }
        }
    })

    var id_cobertura_lista_sem_grupo = $("#id_cobertura_lista_sem_grupo").val();

    if (id_cobertura_lista_sem_grupo!='' && id_cobertura_lista_sem_grupo!=undefined) {
        $('#aguardar').modal();

        $.post("form_lista_animais_matrizes_sem_grupo.php", {id_cobertura:id_cobertura_lista_sem_grupo},
            function(valor){ 

            $('#aguardar').modal('hide');

            $("div[id=lista_animais]").html(valor); 

            var local = $('#id_local').val();
            $('#local_id').val(local);

            $(".filtrar").hide();
            $(".listar").show();
        });
    }

    if ($("input[name='vacas_paridas']:checked").val()==undefined &&
        $("input[name='vacas_solteiras']:checked").val()==undefined &&
        $("input[name='novilhas']:checked").val()==undefined) {

        $(".botoes_confirma").attr("disabled", true);
        $('#botao_lista').removeClass('btn-primary').addClass('btn-secondary');
        $('#botao_excel').removeClass('btn-success').addClass('btn-secondary');
        document.getElementById('botao_lista').style.backgroundColor = '#ccc';
        document.getElementById('botao_excel').style.backgroundColor = '#ccc';
        document.getElementById("botao_lista").style.borderColor = '#ccc';
        document.getElementById("botao_excel").style.borderColor = '#ccc';
        document.getElementById('botao_lista').style.color = '#737070';
        document.getElementById('botao_excel').style.color = '#737070';
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

// funcao para listar estacoes anteriores no paramatro
function listarEstacao(l, e, i){
    const selecao_estacao = document.getElementsByName("lista_estacoes");
    
    $.post("lista_estacao_monta.php", {local:l, estacao_monta: e}, function(retorno){
                        
        selecao_estacao[i].innerHTML = retorno;
    });
}


/* chamada da rotina para excluir uma matriz da lista, programa form_selecao_matrizes_consultar*/
function excluir_matriz_lista(id_cobertura,$numero_item,$codigo_animal_id,$codigo_animal) {
    if (window.confirm("Confirma excluir o registro código " + $codigo_animal + " dessa lista?")) {     
        $.post("excluir_matrizes_cobertura_individual.php",{id_cobertura: id_cobertura, numero_item: $numero_item, id_animal: $codigo_animal_id}, function(valor){
         
            var php = valor.split("<|>");

            if (php[0]==9){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(php[1]);
                return;
            }
            else if (php[0]==99){
                $("#mensagem_retorno_sair").modal();
                $("#mensagem_retorno_sair .modal-body").html(php[1]);
                return;
            }
            else if (php[0]==0){
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(php[1]);
                return;
            }
        });
    }
}

/* chamada da rotina para excluir animais para reproducao, programa form_lista_matrizes*/
function excluir_lista_cobertura($id,$local,$qtd){
    alert ('excluir_matrizes_cobertura_geral.php');

    if (window.confirm("Confirma excluir essa lista? " + $local + " " + $qtd + " Fêmeas")) {     

        $("#aguardar").modal();

        $.post("excluir_matrizes_cobertura_geral.php",{id: $id}, function(valor){

            $('#aguardar').modal('hide');
         
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
}

$(document).ready(function(){
    $('#tabela_itens_consulta').DataTable({
        responsive: true,
        paging: false,
        ordering: true,
        info: false,
        language: {
            sSearch: "Busca:",
            zeroRecords: "Nada encontrado",
            info: "Registros encontrados: _END_ ",
            infoEmpty: "Nenhum registro disponível",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
        },
        order: [[ 1, "asc" ]],

        /*"responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     false,
        "language": {
        "oPaginate": {
            "sFirst": "Primeira",
            "sLast": "Última",
            "sNext": "Próxima",
            "sPrevious": "Anterior"
        },
        "sDom": 'lfr<"table_overflow"t>ip',
        "sSearch": "Busca na lista:",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        },*/
    });

    $("#tabela_lista_matrizes").DataTable({
        responsive: true,
        paging: false,
        ordering: true,
        info: false,
        "bFilter": false, // Inibir a busca
        language: {
            sSearch: "Busca:",
            zeroRecords: "Nada encontrado",
            info: "Registros encontrados: _END_ ",
            infoEmpty: "Nenhum registro disponível",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
        },
        
        columnDefs: [
            { type: 'date-br', targets: 2 }
        
        ],
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $("#tabela_lista_matrizes_monta").DataTable({
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
        
        columnDefs: [
            { type: 'date-br', targets: 2 },
            { type: 'date-br', targets: 7 },
            { type: 'date-br', targets: 8 }
        
        ],

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $("#tabela_lista_matrizes_descarte").DataTable({
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
        
        columnDefs: [
            { type: 'date-br', targets: 2 },
        ],

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $('.tipo_registro').click(function(){
        if ($("input[name='tipo_registro']:checked").val()=='C') {
            $('.estacao_monta').show();
        }
        else {
            $('.estacao_monta').hide();
            $("#codigo_numerico").val('');
        }
    });

    $('.tipo_registro_matrizes').click(function(){
        if ($("input[name='tipo_registro_matrizes']:checked").val()=='I') {
            $("#local_id").val("000000000");
            $(".data_estacao_monta").text('');
            $('.estacao_monta').show();

            var local = $("#local_id").val();

            if (local==0) {
                $('.grupo_monta').hide();
            }
        }
        else {
            $('.estacao_monta').hide();
            $('.grupo_monta').hide();
        }
    });

    $('#vacas_paridas').click(function(){
        var vacas_paridas = $("input[name='vacas_paridas']:checked").val();
        $('#paridas_ate').val('');

        if ($("input[name='vacas_paridas']:checked").val()=='VP') {

            $(".botoes_confirma").attr("disabled", false);
            document.getElementById('botao_lista').style.backgroundColor = '';
            document.getElementById('botao_excel').style.backgroundColor = '';
            document.getElementById("botao_lista").style.borderColor = '';
            document.getElementById("botao_excel").style.borderColor = '';
            document.getElementById('botao_lista').style.color = '';
            document.getElementById('botao_excel').style.color = '';
            $('#botao_lista').removeClass('btn-secondary').addClass('btn-primary');
            $('#botao_excel').removeClass('btn-secondary').addClass('btn-success');
        }
        else if ($("input[name='vacas_paridas']:checked").val()==undefined &&
            $("input[name='vacas_solteiras']:checked").val()==undefined &&
            $("input[name='novilhas']:checked").val()==undefined) {
        
            $(".botoes_confirma").attr("disabled", true);
            $('#botao_lista').removeClass('btn-primary').addClass('btn-secondary');
            $('#botao_excel').removeClass('btn-success').addClass('btn-secondary');
            document.getElementById('botao_lista').style.backgroundColor = '#ccc';
            document.getElementById('botao_excel').style.backgroundColor = '#ccc';
            document.getElementById("botao_lista").style.borderColor = '#ccc';
            document.getElementById("botao_excel").style.borderColor = '#ccc';
            document.getElementById('botao_lista').style.color = '#737070';
            document.getElementById('botao_excel').style.color = '#737070';
        }
    });

    $('#vacas_solteiras').click(function(){
        var vacas_solteiras = $("input[name='vacas_solteiras']:checked").val();
        //$('#paridas_ate').val('');

        if ($("input[name='vacas_solteiras']:checked").val()=='VS') {

            $(".botoes_confirma").attr("disabled", false);
            document.getElementById('botao_lista').style.backgroundColor = '';
            document.getElementById('botao_excel').style.backgroundColor = '';
            document.getElementById("botao_lista").style.borderColor = '';
            document.getElementById("botao_excel").style.borderColor = '';
            document.getElementById('botao_lista').style.color = '';
            document.getElementById('botao_excel').style.color = '';
            $('#botao_lista').removeClass('btn-secondary').addClass('btn-primary');
            $('#botao_excel').removeClass('btn-secondary').addClass('btn-success');
        }
        else if ($("input[name='vacas_paridas']:checked").val()==undefined &&
            $("input[name='vacas_solteiras']:checked").val()==undefined &&
            $("input[name='novilhas']:checked").val()==undefined) {
        
            $(".botoes_confirma").attr("disabled", true);
            $('#botao_lista').removeClass('btn-primary').addClass('btn-secondary');
            $('#botao_excel').removeClass('btn-success').addClass('btn-secondary');
            document.getElementById('botao_lista').style.backgroundColor = '#ccc';
            document.getElementById('botao_excel').style.backgroundColor = '#ccc';
            document.getElementById("botao_lista").style.borderColor = '#ccc';
            document.getElementById("botao_excel").style.borderColor = '#ccc';
            document.getElementById('botao_lista').style.color = '#737070';
            document.getElementById('botao_excel').style.color = '#737070';
        }
    });

    $('#novilhas').click(function(){
        var novilhas = $("input[name='novilhas']:checked").val();
        //$('#paridas_ate').val('');

        if ($("input[name='novilhas']:checked").val()=='NO') {

            $(".botoes_confirma").attr("disabled", false);
            document.getElementById('botao_lista').style.backgroundColor = '';
            document.getElementById('botao_excel').style.backgroundColor = '';
            document.getElementById("botao_lista").style.borderColor = '';
            document.getElementById("botao_excel").style.borderColor = '';
            document.getElementById('botao_lista').style.color = '';
            document.getElementById('botao_excel').style.color = '';
            $('#botao_lista').removeClass('btn-secondary').addClass('btn-primary');
            $('#botao_excel').removeClass('btn-secondary').addClass('btn-success');
        }
        else if ($("input[name='vacas_paridas']:checked").val()==undefined &&
            $("input[name='vacas_solteiras']:checked").val()==undefined &&
            $("input[name='novilhas']:checked").val()==undefined) {
        
            $(".botoes_confirma").attr("disabled", true);
            $('#botao_lista').removeClass('btn-primary').addClass('btn-secondary');
            $('#botao_excel').removeClass('btn-success').addClass('btn-secondary');
            document.getElementById('botao_lista').style.backgroundColor = '#ccc';
            document.getElementById('botao_excel').style.backgroundColor = '#ccc';
            document.getElementById("botao_lista").style.borderColor = '#ccc';
            document.getElementById("botao_excel").style.borderColor = '#ccc';
            document.getElementById('botao_lista').style.color = '#737070';
            document.getElementById('botao_excel').style.color = '#737070';
            $("#idade_ate").val('');
            $("#idade_de").val('');
        }
        else {
            $("#idade_ate").val('');
            $("#idade_de").val('');
        }
    });

    $(".aba_dados").click(function(){
        $('a[href="#dados"]').tab('show');
    });

    $("#codigo_local").change(function(){
        var local = $("#codigo_local").val();

        $.post("lista_estacao_monta.php", {local:local}, function(valor){
            $("select[name=estacao_monta]").html(valor);
        });
    });

    $("#paridas_ate").change(function(){
        var paridas_ate = $("#paridas_ate").val();

        if (paridas_ate!='' && paridas_ate!=0) {
            var vacas_paridas = document.getElementById('vacas_paridas');
            vacas_paridas.checked = true;

            $(".botoes_confirma").attr("disabled", false);
            document.getElementById('botao_lista').style.backgroundColor = '';
            document.getElementById('botao_excel').style.backgroundColor = '';
            document.getElementById("botao_lista").style.borderColor = '';
            document.getElementById("botao_excel").style.borderColor = '';
            document.getElementById('botao_lista').style.color = '';
            document.getElementById('botao_excel').style.color = '';
            $('#botao_lista').removeClass('btn-secondary').addClass('btn-primary');
            $('#botao_excel').removeClass('btn-secondary').addClass('btn-success');
        }
        else {
            var vacas_paridas = document.getElementById('vacas_paridas');
            vacas_paridas.checked = false;

            if ($("input[name='vacas_paridas']:checked").val()==undefined &&
                $("input[name='vacas_solteiras']:checked").val()==undefined &&
                $("input[name='novilhas']:checked").val()==undefined) {
        
                $(".botoes_confirma").attr("disabled", true);
                $('#botao_lista').removeClass('btn-primary').addClass('btn-secondary');
                $('#botao_excel').removeClass('btn-success').addClass('btn-secondary');
                document.getElementById('botao_lista').style.backgroundColor = '#ccc';
                document.getElementById('botao_excel').style.backgroundColor = '#ccc';
                document.getElementById("botao_lista").style.borderColor = '#ccc';
                document.getElementById("botao_excel").style.borderColor = '#ccc';
                document.getElementById('botao_lista').style.color = '#737070';
                document.getElementById('botao_excel').style.color = '#737070';
            }
        }
    });

    $("#idade_de").change(function(){
        var idade_de = $("#idade_de").val();

        if (idade_de!='' && idade_de!=0) {
            var novihas = document.getElementById('novilhas');
            novihas.checked = true;

            $(".botoes_confirma").attr("disabled", false);
            document.getElementById('botao_lista').style.backgroundColor = '';
            document.getElementById('botao_excel').style.backgroundColor = '';
            document.getElementById("botao_lista").style.borderColor = '';
            document.getElementById("botao_excel").style.borderColor = '';
            document.getElementById('botao_lista').style.color = '';
            document.getElementById('botao_excel').style.color = '';
            $('#botao_lista').removeClass('btn-secondary').addClass('btn-primary');
            $('#botao_excel').removeClass('btn-secondary').addClass('btn-success');
        }
        else {
            var idade_ate = $("#idade_ate").val();

            if (idade_ate=='') {
                var novilhas = document.getElementById('novilhas');
                novilhas.checked = false;

                if ($("input[name='vacas_paridas']:checked").val()==undefined &&
                    $("input[name='vacas_solteiras']:checked").val()==undefined &&
                    $("input[name='novilhas']:checked").val()==undefined) {
            
                    $(".botoes_confirma").attr("disabled", true);
                    $('#botao_lista').removeClass('btn-primary').addClass('btn-secondary');
                    $('#botao_excel').removeClass('btn-success').addClass('btn-secondary');
                    document.getElementById('botao_lista').style.backgroundColor = '#ccc';
                    document.getElementById('botao_excel').style.backgroundColor = '#ccc';
                    document.getElementById("botao_lista").style.borderColor = '#ccc';
                    document.getElementById("botao_excel").style.borderColor = '#ccc';
                    document.getElementById('botao_lista').style.color = '#737070';
                    document.getElementById('botao_excel').style.color = '#737070';
                }
            }
        }
    });

    $("#idade_ate").change(function(){
        var idade_ate = $("#idade_ate").val();

        if (idade_ate!='' && idade_ate!=0) {
            var novihas = document.getElementById('novilhas');
            novihas.checked = true;

            $(".botoes_confirma").attr("disabled", false);
            document.getElementById('botao_lista').style.backgroundColor = '';
            document.getElementById('botao_excel').style.backgroundColor = '';
            document.getElementById("botao_lista").style.borderColor = '';
            document.getElementById("botao_excel").style.borderColor = '';
            document.getElementById('botao_lista').style.color = '';
            document.getElementById('botao_excel').style.color = '';
            $('#botao_lista').removeClass('btn-secondary').addClass('btn-primary');
            $('#botao_excel').removeClass('btn-secondary').addClass('btn-success');
        }
        else {
            var idade_de = $("#idade_de").val();

            if (idade_de=='') {
                var novilhas = document.getElementById('novilhas');
                novilhas.checked = false;

                if ($("input[name='vacas_paridas']:checked").val()==undefined &&
                    $("input[name='vacas_solteiras']:checked").val()==undefined &&
                    $("input[name='novilhas']:checked").val()==undefined) {
            
                    $(".botoes_confirma").attr("disabled", true);
                    $('#botao_lista').removeClass('btn-primary').addClass('btn-secondary');
                    $('#botao_excel').removeClass('btn-success').addClass('btn-secondary');
                    document.getElementById('botao_lista').style.backgroundColor = '#ccc';
                    document.getElementById('botao_excel').style.backgroundColor = '#ccc';
                    document.getElementById("botao_lista").style.borderColor = '#ccc';
                    document.getElementById("botao_excel").style.borderColor = '#ccc';
                    document.getElementById('botao_lista').style.color = '#737070';
                    document.getElementById('botao_excel').style.color = '#737070';
                }
            }
        }
    });

    $("#local_id").change(function(){
        var data_inicial = document.getElementsByName("inicio_estacao");
        var data_final = document.getElementsByName("fim_estacao");
        var codigo_local = document.getElementsByName("codigo_fazenda");
        var id_parametro = document.getElementsByName("id_parametro");
        var nome_estacao = document.getElementsByName("nome_estacao");
        var tipo_registro = $("input[name='tipo_registro_matrizes']:checked").val();

        var local = $("#local_id").val();

        if (local==0) {
            $(".data_estacao_monta").text('');
            $('.estacao_monta').hide();
            $('.grupo_monta').hide();
            return;
        }

        for (var j = 0; j < codigo_local.length; j++) {
            if (codigo_local[j].value==local) {
                var estacao_inicial = data_inicial[j].value;
                var estacao_final = data_final[j].value;
                var estacao_nome = nome_estacao[j].value;
                $("#id_estacao_monta").val(id_parametro[j].value);
            }
        }

        if (estacao_inicial=='' && tipo_registro=='I') {
            var select = $("#local_id").val();

            if (select!=0) {
                select = document.getElementById('local_id');
                nome_fazenda = select.options[select.selectedIndex].text;
            }

            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Não existem Parametros da Estação de Monta para: ' + nome_fazenda);
            $("#local_id").val("000000000");
            $('a[href="#parametros"]').tab('show');
            return;
        }

        var partes_inicial = estacao_inicial.split("-");
        var partes_final = estacao_final.split("-");
        var periodo_estacao = 'Estação de monta: ' + estacao_nome +' - '+
                               partes_inicial[2]+'/'+partes_inicial[1]+'/'+
                               partes_inicial[0]+ ' até '+partes_final[2]+'/'+
                               partes_final[1]+'/'+partes_final[0];

        if ($("input[name='tipo_registro_matrizes']:checked").val()=='I') {
            $('.estacao_monta').show();
            $('.grupo_monta').show();
        }
        else {
            $('.estacao_monta').hide();
            $('.grupo_monta').hide();
        }    

        $(".data_estacao_monta").text(periodo_estacao);
        var local = $("#local_id").val();
        var id_parametro_estacao = $("#id_estacao_monta").val();

        $.post("ler_grupo_estacao_monta.php", {local:local, id_parametro_estacao:id_parametro_estacao},
            function(valor){ 

            var php = valor.split("<|>");

            if (php[1]==''){
                var tipo_registro = $("input[name='tipo_registro_matrizes']:checked").val();

                if (tipo_registro=='I') {
                    var select = $("#local_id").val();

                    if (select!=0) {
                        select = document.getElementById('local_id');
                        nome_fazenda = select.options[select.selectedIndex].text;
                    }

                    $("#proximo_grupo").val(1);

                    $("#mensagem_retorno_grupo").modal();
                    $("#mensagem_retorno_grupo .modal-body").html('Não exitem Grupos da Estação de Monta para: ' + nome_fazenda);
                    return;
                }
            }
            else {
                var proximo_grupo = php[1];
                proximo_grupo++;
                $("#proximo_grupo").val(proximo_grupo);
        
                //modal_grupo_estacao();
            }
        });

    });

    $("#btnAdicionar").click(function(){
        var local = $("#local_id").val();
        var id_parametro_estacao = $("#id_estacao_monta").val();

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
            $("#codigo_grupo").val($("#proximo_grupo").val());
            $("#descricao_grupo").val('');
            $("#codigo_estacao_grupo").val('');
            $("#codigo_local_grupo").val('');
            $("#tipo_gravacao_grupo").val(0);
            document.getElementById("codigo_grupo").readOnly = false;
        });
    });

    // Acende o botão consultar se houver alteracao nos filtros da pesagem
    $('#codigo_local').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });

    $('#estacao_monta').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });

    $('.radio-inline').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });
    // Fim acendo botão 

    // Soma total selecionado quando clicar em selecionar todos na inclusão de femeas Monta
    $('.seleciona_todos').click(function(event) {
        var total_selecionados = 0;

        if(this.checked) {
            $('.checkbox1').each(function() {
                this.checked = true;
                total_selecionados++; 
            });
        }else{
            $('.checkbox1').each(function() {
                this.checked = false;  
            });         
        }

        $('#total_selecionados').val(total_selecionados);
    });

    // Soma total selecionado com selecionar cada anaimal na inclusão de femeas Monta
    $('.checkbox1').click(function(event) {
        var total = $('#total_selecionados').val();    

        if (total=='' || total==0) {
            total_selecionados = 0;
        }
        else {
            total_selecionados = total;
        }

        if(this.checked==true) {
            total_selecionados++; 
        }else{
            total_selecionados--;
        }

        $('#total_selecionados').val(total_selecionados);
    });
});

function show_consulta() {
    $('.busca_consultar').show();
    //$('.filtros_consulta').hide();
}

function limpar_filtros_animal() {
    $("#codigo_numerico").val('');
    consultar();
}

function informacoes_uso() {
    $("#ajuda").modal();
}

function editar_grupo_estacao($codigo_grupo, $descricao) {
    $(".novo_grupo").show();
    $("#codigo_grupo").val($codigo_grupo);
    $("#descricao_grupo").val($descricao);
    $("#codigo_estacao_grupo").val('');
    $("#codigo_local_grupo").val('');
    $("#tipo_gravacao_grupo").val(1);

    document.getElementById("codigo_grupo").readOnly = true;
    $('#descricao_grupo').focus();
}

function excluir_grupo_estacao($codigo_grupo, $descricao) {
    $("#codigo_grupo").val($codigo_grupo);
    $("#descricao_grupo").val($descricao);
    $("#codigo_estacao_grupo").val('');
    $("#codigo_local_grupo").val('');
    $("#tipo_gravacao_grupo").val(2);

    if (window.confirm("Confirma excluir esse grupo? " + $descricao)) {  
        gravar_grupo(); 
    }  
}

function modal_grupo_estacao() {
    $(".novo_grupo").hide();

    var local = $("#local_id").val();
    var id_parametro_estacao = $("#id_estacao_monta").val();

    var select = $("#local_id").val();
    if (select!=0) {
        select = document.getElementById('local_id');
        nome_fazenda = select.options[select.selectedIndex].text;
    }

    $(".nome_fazenda").text(nome_fazenda);
    $('#modal_grupo_estacao').modal('show');

    $.post("form_lista_grupos_estacao.php", {local:local, id_parametro_estacao:id_parametro_estacao},
        function(valor){ 

        $("div[id=lista_grupos_estacao]").html(valor); 

        popular_select_grupo(local, id_parametro_estacao);
    });
}

//function somar_selecionados() { // Quando seleciona o grupo
/*    var table = $('#tabela_matrizes').DataTable();
    table.search('').draw();

    var aChk = document.getElementsByName("grupo_select");
    
    var total_selecionados=0;
    
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].value != '000') {
            total_selecionados++;
        }
    }

    if (total_selecionados>0) {
        $("#total_selecionados").val(total_selecionados);
        $("#total_selecionados").html(total_selecionados);
    }
    else {
        $("#total_selecionados").val('');
        $("#total_selecionados").html('');
    }*/
//}

function somar_selecionados() {
    // 1. Encontrar o elemento que está realmente rolando.
    // Usamos o seletor EXATO com base no HTML que você forneceu.
    // O div com a classe 'table_overflow' é o container de rolagem.
    var scrollContainer = $('.table_overflow'); // Seleciona o div com a classe 'table_overflow'

    // Verificação de segurança: se por algum motivo ele não for encontrado
    // ou não tiver conteúdo suficiente para rolar, volta para o window.
    if (scrollContainer.length === 0 || scrollContainer[0].scrollHeight <= scrollContainer[0].clientHeight) {
        scrollContainer = $(window);
        console.log("DEBUG: Rolagem na janela principal (fallback).");
    } else {
        console.log("DEBUG: Rolagem no container '.table_overflow'.");
        console.log("DEBUG: Elemento de rolagem:", scrollContainer[0]); // Mostra o elemento no console
        console.log("DEBUG: Posição de scroll ANTES:", scrollContainer.scrollTop());
    }

    // 2. Salvar a posição de rolagem atual antes de qualquer operação
    var currentScrollPosition;
    if (scrollContainer.is($(window))) {
        currentScrollPosition = window.scrollY || document.documentElement.scrollTop;
    } else {
        currentScrollPosition = scrollContainer.scrollTop();
    }
    
    // Seu código original:
    var table = $('#tabela_matrizes').DataTable();
    // Esta linha força o redesenho e é a causa do problema.
    // No entanto, ela é necessária se você estiver usando o recurso de busca
    // ou se o DataTables precisar atualizar o estado da tabela.
    table.search('').draw();

    var aChk = document.getElementsByName("grupo_select");
    
    var total_selecionados = 0;
    
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].value != '000') { // Assumindo '000' é o valor "nenhum selecionado"
            total_selecionados++;
        }
    }

    if (total_selecionados > 0) {
        $("#total_selecionados").val(total_selecionados);
        $("#total_selecionados").html(total_selecionados);
    } else {
        $("#total_selecionados").val('');
        $("#total_selecionados").html('');
    }

    // 3. Restaurar a posição de rolagem após o redesenho do DataTables
    if (scrollContainer.is($(window))) {
        window.scrollTo(0, currentScrollPosition);
    } else {
        scrollContainer.scrollTop(currentScrollPosition);
        console.log("DEBUG: Posição de scroll DEPOIS:", scrollContainer.scrollTop());
    }
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
    $("#codigo_animal_id").val(array_edicao[0]);

    $("#num_mov_nascimento").val(array_edicao[14]);
    $("#tipo_gravacao").val(1);

    if (array_edicao[3]=='F') {
        $("#F").prop("checked", true);
    }
    else {
        $("#M").prop("checked", true);
    }
    $("#raca_id").val(array_edicao[4]);
    $("#pelagem_id").val(array_edicao[5]);
    $("#peso_animal").val(array_edicao[11]);
    $("#nascimento_animal").val(array_edicao[6]);
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
                $("#codigo_numerico_animal").val(array_edicao[2]);
                $(".codigo_numerico_animal").show();
                $("#codigo_pai_animal").val(array_edicao[9]);
                $("#codigo_mae_animal").val(array_edicao[10]);
                $("#codigo_mae_consulta").val(array_edicao[12]);
                $(".codigo_mae_animal").show();
                $(".codigo_pai_animal").show();
            }
            else {
                $("#codigo_numerico_animal").val('');
                $("#codigo_pai_animal").val('000000000');
                $("#codigo_mae_animal").val('');
                $("#codigo_mae_consulta").val('');
                $(".codigo_numerico_animal").hide();
                $(".codigo_mae_animal").hide();
                $(".codigo_pai_animal").hide();
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
            $(".codigo_mae_animal").hide();
            $(".codigo_pai_animal").hide();
        }

        document.getElementById('alfa_animal').readOnly = true;
        document.getElementById('codigo_numerico_animal').readOnly = true;
        $('#modal_incluir .modal-title').html('Nascimento - Editar');
        $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-success');
        $('#modal_incluir').modal('show');
    })
}

function listar_animais() {
    var id_estacao_monta = $("#id_estacao_monta").val();
    var local = $('#local_id').val();
    var vacas_paridas = $("input[name='vacas_paridas']:checked").val();
    var vacas_solteiras = $("input[name='vacas_solteiras']:checked").val();
    var novilhas = $("input[name='novilhas']:checked").val();
    var peso_acima = $('#peso_acima').val();
    var descricao_estacao = $(".data_estacao_monta").text();
    var tipo_registro = $("input[name='tipo_registro_matrizes']:checked").val();

    if (local =="000000000") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Fazenda');
        return;
    }

    var options = $('#local_id option:selected');
    var filtro_local = [];

    $(options).each(function(){
        var desc = $(this).bind('#local_id').text();
        filtro_local.push( desc.trim() );
    });

    if (vacas_paridas=='VP'){
        var data_paridas = $('#paridas_ate').val();

        if (data_paridas=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Para seleção Vacas Paridas informe a data Aptas Em');
            return;
        }

        var data_paridas = $('#paridas_ate').val().split("-");
        var filtro_paridas_ate = 'Vacas aptas em: ' + data_paridas[2]+'/'+data_paridas[1]+'/'+data_paridas[0] + '->';
        var data_paridas = $('#paridas_ate').val();
    }
    else {
        var vacas_paridas='';
        var filtro_paridas_ate = '';
        var data_paridas = '';
    }

    if (vacas_solteiras=='VS'){
        var filtro_solteiras = 'Vacas solteiras->';
    }
    else {
        vacas_solteiras='';
        var filtro_solteiras = '';
    }

    if (novilhas=='NO'){
        var idade_de = $('#idade_de').val();
        var idade_ate = $('#idade_ate').val();

        if (idade_de=='') {
            idade_de = 12;
        }

        if (idade_de < 12 && idade_de>0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Idade de não pode ser < 12 meses');
            return;
        }

        if (idade_ate < idade_de && idade_de>0 && idade_ate>0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Idade até não pode ser < idade de');
            return;
        }

        if (idade_de=='' && idade_ate=='') {
            var idade = 'Idade > 12 meses';
        }
        else if (idade_de=='' && idade_ate!='') {
            var idade = 'Idade > 12 até ' + idade_ate + ' meses';
        }
        else if (idade_de!='' && idade_ate!='') {
            var idade = 'Idade de ' + idade_de + ' até ' + idade_ate + ' meses';
        }
        else if (idade_de!='' && idade_ate=='') {
            var idade = 'Idade >=' + idade_de + ' meses';
        }
        
        var filtro_novilhas = 'Novilhas:'+idade;
    }
    else {
        novilhas = '';
        var filtro_novilhas = '';
        var idade_de =0;
        var idade_ate = 0;
    }

    if (peso_acima!='') {
        var filtro_peso = 'Peso Acima de:'+peso_acima+'Kg';
    }
    else {
        var filtro_peso = '';
    }

    if (tipo_registro=='I') {
        var filtros = 'Filtros: IATF->' + descricao_estacao +'->'+ filtro_local+'->'+filtro_paridas_ate+filtro_solteiras+filtro_novilhas+filtro_peso;
    }
    else {
        var filtros = 'Filtros: Monta->'+ filtro_local+'->'+filtro_paridas_ate+filtro_solteiras+filtro_novilhas+filtro_peso;
    }

    $("#aguardar").modal();

    $.post("form_lista_animais_matrizes.php", {
        tipo_registro: tipo_registro,
        id_estacao_monta:id_estacao_monta, 
        local:local, 
        vacas_paridas:vacas_paridas, 
        data_paridas:data_paridas, 
        vacas_solteiras:vacas_solteiras, 
        novilhas:novilhas, 
        idade_de:idade_de, 
        idade_ate: idade_ate, 
        peso_acima: peso_acima},
        function(valor){ 

        $('#aguardar').modal('hide');

        $("div[id=lista_animais]").html(valor); 

        $(".filtrar").hide();
        $(".filtros").text(filtros);
        $(".filtros").val(filtros);
        $(".listar").show();
    });
}

function listar_animais_excel() {
    var id_estacao_monta = $("#id_estacao_monta").val();
    var local = $('#local_id').val();
    var vacas_paridas = $("input[name='vacas_paridas']:checked").val();
    var vacas_solteiras = $("input[name='vacas_solteiras']:checked").val();
    var novilhas = $("input[name='novilhas']:checked").val();
    var peso_acima = $('#peso_acima').val();
    var descricao_estacao = $(".data_estacao_monta").text();
    var tipo_registro = $("input[name='tipo_registro_matrizes']:checked").val();

    if (local =="000000000") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Fazenda');
        return;
    }

    var options = $('#local_id option:selected');
    var filtro_local = [];

    $(options).each(function(){
        var desc = $(this).bind('#local_id').text();
        filtro_local.push( desc.trim() );
    });

    if (vacas_paridas=='VP'){
        var data_paridas = $('#paridas_ate').val();

        if (data_paridas=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Para seleção Vacas Paridas informe a data Aptas Em');
            return;
        }

        var data_paridas = $('#paridas_ate').val().split("-");
        var filtro_paridas_ate = '->Vacas aptas em: ' + data_paridas[2]+'/'+data_paridas[1]+'/'+data_paridas[0] + '->';
        var data_paridas = $('#paridas_ate').val();
    }
    else {
        var vacas_paridas='';
        var filtro_paridas_ate = '';
        var data_paridas = '';
    }

    if (vacas_solteiras=='VS'){
        var filtro_solteiras = 'Vacas solteiras->';
    }
    else {
        vacas_solteiras='';
        var filtro_solteiras = '';
    }

    if (novilhas=='NO'){
        var idade_de = $('#idade_de').val();
        var idade_ate = $('#idade_ate').val();

        if (idade_de=='') {
            idade_de = 12;
        }

        if (idade_de < 12 && idade_de>0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Idade de não pode ser < 12 meses');
            return;
        }

        if (idade_ate < idade_de && idade_de>0 && idade_ate>0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Idade até não pode ser < idade de');
            return;
        }

        if (idade_de=='' && idade_ate=='') {
            var idade = 'Idade > 12 meses';
        }
        else if (idade_de=='' && idade_ate!='') {
            var idade = 'Idade > 12 até ' + idade_ate + ' meses';
        }
        else if (idade_de!='' && idade_ate!='') {
            var idade = 'Idade de ' + idade_de + ' até ' + idade_ate + ' meses';
        }
        else if (idade_de!='' && idade_ate=='') {
            var idade = 'Idade >=' + idade_de + ' meses';
        }
        
        var filtro_novilhas = 'Novilhas:'+idade;
    }
    else {
        novilhas = '';
        var filtro_novilhas = '';
        var idade_de =0;
        var idade_ate = 0;
    }

    if (peso_acima!='') {
        var filtro_peso = 'Peso Acima de:'+peso_acima+'Kg';
    }
    else {
        var filtro_peso = '';
    }

    if (tipo_registro=='I') {
        var filtros = 'Filtros: IATF->' + descricao_estacao +'->'+ filtro_local+'->'+filtro_paridas_ate+filtro_solteiras+filtro_novilhas+filtro_peso;
    }
    else {
        var filtros = 'Filtros: Monta->'+ filtro_local+'->'+filtro_paridas_ate+filtro_solteiras+filtro_novilhas+filtro_peso;
    }

    $('#aguardar').modal();

    location.href='rel_exportar_lista_matrizs_excel.php?id_estacao_monta=' + id_estacao_monta +
        '&tipo_registro=' + tipo_registro + '&local=' + local + '&vacas_paridas=' + vacas_paridas + 
        '&data_paridas=' + data_paridas + '&vacas_solteiras=' + vacas_solteiras + '&novilhas=' + novilhas + 
        '&idade_de=' + idade_de + '&idade_ate=' + idade_ate + '&peso_acima=' + peso_acima + '&filtros=' + filtros;

    tout = setTimeout('volta_filtros()', 10000);

}

function registrar_grupos_cobertura(id_cobertura) {
    location.href='form_selecao_matrizes_incluir.php?editar=true&id_cobertura=' + id_cobertura;
} 

function volta_filtros(){
    $('#aguardar').modal('hide');

    var id_cobertura_lista_sem_grupo = $("#id_cobertura_lista_sem_grupo").val();

    if (id_cobertura_lista_sem_grupo!=''){
        location.href= "form_selecao_matrizes.php";
    }
    else{
        //location.href= "form_selecao_matrizes.php";
        $(".filtrar").show();
        $(".listar").hide();
    }
}

function finalizar_sair(){
    location.href= "form_selecao_matrizes.php";
}

function selecao_matrizes_incluir() {
    location.href= "form_selecao_matrizes_incluir.php";
}

function mais_relatorios() {
    location.href= 'form_rel_situacao_reprodutiva.php?tipo=2';
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
    $("#codigo_animal_id").val(array_edicao[0]);

    $("#num_mov_nascimento").val(array_edicao[14]);
    $("#tipo_gravacao").val(2);

    if (array_edicao[3]=='F') {
        $("#F").prop("checked", true);
    }
    else {
        $("#M").prop("checked", true);
    }
    $("#raca_id").val(array_edicao[4]);
    $("#pelagem_id").val(array_edicao[5]);
    $("#peso_animal").val(array_edicao[11]);
    $("#nascimento_animal").val(array_edicao[6]);
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
                $("#codigo_numerico_animal").val(array_edicao[2]);
                $(".codigo_numerico_animal").show();
                $("#codigo_pai_animal").val(array_edicao[9]);
                $("#codigo_mae_animal").val(array_edicao[10]);
                $("#codigo_mae_consulta").val(array_edicao[12]);
                $(".codigo_mae_animal").show();
                $(".codigo_pai_animal").show();
            }
            else {
                $("#codigo_numerico_animal").val('');
                $("#codigo_pai_animal").val('000000000');
                $("#codigo_mae_animal").val('');
                $("#codigo_mae_consulta").val('');
                $(".codigo_numerico_animal").hide();
                $(".codigo_mae_animal").hide();
                $(".codigo_pai_animal").hide();
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
            $(".codigo_mae_animal").hide();
            $(".codigo_pai_animal").hide();
        }

        document.getElementById('alfa_animal').readOnly = true;
        document.getElementById('codigo_numerico_animal').readOnly = true;
        $('#modal_incluir .modal-title').html('Nascimento - Excluir');
        $('.confirma_gravar').html('Confirmar Exclusão').removeClass('btn-danger').addClass('btn-danger');
        $('#modal_incluir').modal('show');
    })


//    if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
//    }

}

function confirmar_grupos() {
    var matrizes_selecionadas = $("#total_selecionados").val();

    $('.animais_selecionados').html('Total selecionadas: ' + matrizes_selecionadas);

    var aChk = document.getElementsByName("grupo_select");

    var qtd_grupo=[];
    var grupo=[];

    for (var i = 0; i <= 999; i++) {
        qtd_grupo.push(0);
        grupo.push(0);
    }
    
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].value != '000') {
            codigo_grupo = aChk[i].value;
            qtd_grupo[codigo_grupo]=0;
            grupo[codigo_grupo]=0;
        }
    }

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].value != '000') {
            codigo_grupo = aChk[i].value;
            qtd_grupo[codigo_grupo]++;
            grupo[codigo_grupo]=codigo_grupo;
        }
    }

    html = "";
    html += '<table class="table table-striped table-advance table-hover" id="itens_grupo" width="100%">';
    html += '<thead>';
    html += '<tr>';
    html += '<th>' + ' Grupo' + '</th>';
    html += '<th>' + ' Fêmeas' + '</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';


    for (var i = 0; i <= 999; i++) {
        var j = ("000" + i).slice(-3);

        if (grupo[j]!=undefined && grupo[j]!=0) {
            html += '<tr>';
            if (grupo[j]!=999) {
                html +="<td width='12%' class='grupo_matrizes'>" + grupo[j] + "</td>";
                html +="<td width='8%' class='qtde_matrizes'>" + qtd_grupo[j] + "</td>";
            }
            else {
                html +="<td width='12%' class='grupo_matrizes'>Descarte</td>";
                html +="<td width='8%' class='qtde_matrizes'>" + qtd_grupo[j] + "</td>";
            }
            html += '</tr>';
        }
    }

    html += '</tbody>';
    html += '</table>';
    document.getElementById('grupos_selecionados').innerHTML = html;

    $('#confirmar_grupo').modal('show');
}

function modal_inserir_nova_matriz() {
    var desc_grupo = $(".desc_grupo").html();
    var desc_animais = $(".qtd_animais").html();
    var desc_filtros = $(".desc_filtro").html();
    var cobertura_numero_id = $("#num_orc").html();
    $('#cobertura_numero_id').val(cobertura_numero_id);

    $('.grupo_matriz').html('Grupo: ' + desc_grupo);
    $('.animais_matriz').html('Qtde animais nesse grupo : ' + desc_animais);
    $('.filtro_matriz').html(desc_filtros);

    $("#alert_erro_animal .negrito").html('');
    $("#alert_erro_animal span").html('');
    $(".alert_erro_animal").hide();
    $('#id_animal').val('');
    $('#descricao_animal').text('');
    $(".gravar_inserir").hide();
    $('#inserir_nova_matriz').modal('show');
}

function modal_inserir_nova_matriz_monta() {
    $("#alert_erro_animal .negrito").html('');
    $("#alert_erro_animal span").html('');
    $(".alert_erro_animal").hide();
    $('#id_animal').val('');
    $('#descricao_animal').text('');
    $(".gravar_inserir").hide();
    $('#inserir_nova_matriz_monta').modal('show');
}

function modal_inserir_nova_matriz_descarte() {
    $("#alert_erro_animal .negrito").html('');
    $("#alert_erro_animal span").html('');
    $(".alert_erro_animal").hide();
    $('#id_animal').val('');
    $('#descricao_animal').text('');
    $(".gravar_inserir").hide();
    $('#inserir_nova_matriz_descarte').modal('show');
}

function ler_animal(){
    var id_animal= $('#id_animal').val();
    var local = $('#id_local').val();
    var estacao_monta = $('#estacao_monta').val();
    var grupo = $('#codigo_grupo').val();

    if (id_animal.length < 5) {
        return;
    } 

    $.post("ler_animal_selecao_matriz.php", {
        id_animal:id_animal, 
        local:local, 
        estacao_monta:estacao_monta,
        grupo:grupo}, function(valor){

        var php = valor.split("<|>");

        if (php[0]=='Nao tem animal') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html(php[1]);
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $(".gravar_inserir").hide();
            return;
        }
        else if (php[16]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea já está nesse grupo.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            $(".gravar_inserir").hide();
            return;
        }
        else if (php[7]<=12) {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea tem ' + php[7] + ' meses.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            $(".gravar_inserir").hide();
            return;
        }
        else if (php[10]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea esta Prenha.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            $(".gravar_inserir").hide();
            return;
        }
        else if (php[12]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea teve parto há menos de 35 dias.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            $(".gravar_inserir").hide();
            return;
        }
        else if (php[13]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea teve Natimorto há menos de 35 dias.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            $(".gravar_inserir").hide();
            return;
        }
        else if (php[14]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea teve Aborto há menos de 35 dias.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            $(".gravar_inserir").hide();
            return;
        }
        else if (php[11]=='S') {
            if (php[8]=='' || php[8]==0) {
                $("#alert_erro_animal span").html('Fêmea já selecionada como Monta.');
            }
            else {
                $("#alert_erro_animal span").html('Fêmea já selecionada nessa estação! Grupo: ' + php[8] + '-' + php[9]);
            }

            $("#alert_erro_animal .negrito").html('');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            $(".gravar_inserir").hide();
            return;
        }
        else {
            $("#descricao_animal").text(php[6]);
            $("#codigo_id").val(php[0]);
            $(".gravar_inserir").show();
        }

    }); 
}

function ler_animal_monta(){
    var id_animal= $('#id_animal').val();
    var local = $('#codigo_local').val();
    var estacao_monta = $('#estacao_monta').val();

    if (id_animal.length < 5) {
        return;
    } 

    $.post("ler_animal_selecao_monta.php", {
        id_animal:id_animal, 
        local:local,
        estacao_monta:estacao_monta}, function(valor){

        var php = valor.split("<|>");

        if (php[0]=='Nao tem animal') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html(php[1]);
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            //$(".exibe_campos").hide();
            return;
        }
        else if (php[7]<=12) {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea tem ' + php[7] + ' meses.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else if (php[10]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea esta Prenha.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else if (php[12]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea teve parto há menos de 35 dias.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else if (php[13]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea teve Natimorto há menos de 35 dias.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else if (php[14]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea teve Aborto há menos de 35 dias.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else if (php[11]=='S') {
            if (php[8]=='' || php[8]==0) {
                $("#alert_erro_animal span").html('Fêmea já selecionada como Monta nessa lista.');
            }
            else {
                $("#alert_erro_animal span").html('Fêmea já selecionada nessa estação! Grupo: ' + php[8]);
            }

            $("#alert_erro_animal .negrito").html('');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else {
            $("#descricao_animal").text(php[6]);
            $("#codigo_id").val(php[0]);
            // A digitação das datas foram retiradas da inclusão de femeas em 18/03/2025
            $(".gravar_inserir").show(); 
            //$('#data_prenhes').focus();
        }
    }); 
}

function ler_animal_descarte(){
    var id_animal= $('#id_animal_d').val();
    var local = $('#codigo_local').val();
    var estacao_monta = $('#estacao_monta').val();

    if (id_animal.length < 5) {
        return;
    } 

    $.post("ler_animal_selecao_descarte.php", {
        id_animal:id_animal, 
        local:local,
        estacao_monta:estacao_monta}, function(valor){

        var php = valor.split("<|>");

        if (php[0]=='Nao tem animal') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html(php[1]);
            $(".alert_erro_animal").show();
            $('#descricao_animal_d').text('');
            return;
        }
        /*else if (php[16]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea já está nesse grupo.');
            $(".alert_erro_animal").show();
            $('#descricao_animal').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            $(".gravar_inserir").hide();
            return;
        }*/
        else if (php[7]<=12) {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea tem ' + php[7] + ' meses.');
            $(".alert_erro_animal").show();
            $('#descricao_animal_d').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else if (php[10]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea esta Prenha.');
            $(".alert_erro_animal").show();
            $('#descricao_animal_d').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else if (php[12]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea teve parto há menos de 35 dias.');
            $(".alert_erro_animal").show();
            $('#descricao_animal_d').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else if (php[13]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea teve Natimorto há menos de 35 dias.');
            $(".alert_erro_animal").show();
            $('#descricao_animal_d').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else if (php[14]=='S') {
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('Esta Fêmea teve Aborto há menos de 35 dias.');
            $(".alert_erro_animal").show();
            $('#descricao_animal_d').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else if (php[11]=='S') {
            if (php[8]=='') {
                $("#alert_erro_animal span").html('Esta Fêmea esta na lista de Monta Natural.');
            }
            else {
                $("#alert_erro_animal span").html('Fêmea já selecionada nessa estação! Grupo: ' + php[8]);
            }

            $("#alert_erro_animal .negrito").html('');
            $(".alert_erro_animal").show();
            $('#descricao_animal_d').text('');
            $('#voltar').focus();            
            $("#codigo_id").val('');
            //$(".exibe_campos").hide();
            return;
        }
        else {
            $("#descricao_animal_d").text(php[6]);
            $("#codigo_id").val(php[0]);
            $(".gravar_inserir").show(); 
        }
    }); 
}

// calcular previsao parto ao inserir femea na monta
function calcular_data_previsao() { 
    var data_prenhes = $("#data_prenhes").val();

    let date = new Date(data_prenhes);
    date.setDate(date.getDate() + 283); // para achar 282 dias tem que colocar 1 dia a mais no calculo

    n = date.getFullYear() +"-" + adicionaZero((date.getMonth() + 1)) + "-" + adicionaZero(date.getDate());
    $("#data_previsao").val(n);
}         
 
// calcular data prenhes ao inserir femea na monta
function calcular_data_prenhes() { 
    var data_previsao = $("#data_previsao").val();

    let date = new Date(data_previsao);
    date.setDate(date.getDate() - 281); // para achar 282 dias tem que colocar 1 dia a menos no calculo

    n = date.getFullYear() +"-" + adicionaZero((date.getMonth() + 1)) + "-" + adicionaZero(date.getDate());
    $("#data_prenhes").val(n);
} 

function verificar_femeas_gravar_matrizes() {
    var aChk = document.getElementsByName("grupo_select");
    
    var total_selecionados=0;
    var total_sem_selecao=0;
    
    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].value != '000') {
            total_selecionados++;
        }
        else {
            total_sem_selecao++;
        }
    }

    if (total_selecionados==0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe os grupos para os animais da lista.');
        return;
    }

    var id_cobertura_lista_sem_grupo = $("#id_cobertura_lista_sem_grupo").val();

    if (id_cobertura_lista_sem_grupo!='') {
        if (total_sem_selecao>0) {
            $('.id_cobertura').text('Nº Documeto: ' + id_cobertura_lista_sem_grupo);
            $('#confirmar_grupo').modal('hide');
            $('#confirmar_grupo_com_id').modal('show');
            return;
        }
        else {
            gravar_matrizes();
        }
    }
    else {
        gravar_matrizes();
    }
}

function confirmar_femeas_monta() {
    var table = $('#tabela_matrizes_monta').DataTable();
    table.search('').draw();

    $(".confirma_gravar").attr("disabled", true);

    var total_selecionados = $("#total_selecionados").val();
    
    if (total_selecionados==0) {
        $(".confirma_gravar").attr("disabled", false);
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione os animais da lista.');
        return;
    }

    var aChk = document.getElementsByName("id_animal_selecao");
    var array_tabela_itens = new Array();

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].checked == true) {
            codigo_selecionado = aChk[i].value;
            array_tabela_itens.push(codigo_selecionado);
            grupo_itens=array_tabela_itens.join("<|>");
        }
    }

    var codigo_local = $("#local_id").val();
    var id_cobertura_lista_sem_grupo = $("#id_cobertura_lista_sem_grupo").val();

    $.ajax({
        type: "POST",
        url: 'gravar_matrizes_inserir_nova_monta_selecionadas.php',
        data:  {
                codigo_local: codigo_local,
                id_cobertura: id_cobertura_lista_sem_grupo,
                array_itens: grupo_itens
            },
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

function gravar_matrizes() {
    var aChk = document.getElementsByName("grupo_select");
    
    var animais_selecionados = new Array();
    var array_animais_selecionados = "";
    var grupos_selecionados = new Array();
    var array_grupos_selecionados = "";

    var aChk = document.getElementsByName("id_animal");
    var bChk = document.getElementsByName("grupo_select");

    for (var i = 0; i < aChk.length; i++) {
        codigo_animal = aChk[i].value;
        grupo_animal = bChk[i].value;

        if (grupo_animal != '000') {
            animais_selecionados.push(codigo_animal);
            array_animais_selecionados = animais_selecionados.join("<|>");

            grupos_selecionados.push(grupo_animal);
            array_grupos_selecionados = grupos_selecionados.join("<|>");
        }
    }

    var aChk = document.getElementsByName("grupo_select");
    var grupo=[];
    var ordem_grupos = new Array();
    var array_ordem_grupos = "";

    for (var i = 0; i <= 999; i++) {
        grupo.push(0);
    }

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].value != '000') {
            codigo_grupo = aChk[i].value;
            grupo[codigo_grupo]=0;
        }
    }

    for (var i = 0; i < aChk.length; i++) {
        if (aChk[i].value != '000') {
            codigo_grupo = aChk[i].value;
            grupo[codigo_grupo]=codigo_grupo;
        }
    }

    for (var i = 0; i <= 999; i++) {
        var j = ("000" + i).slice(-3);

        if (grupo[j]!=undefined && grupo[j]!=0) {
            grupo_animal = grupo[j];
            ordem_grupos.push(grupo[j]);
            array_ordem_grupos = ordem_grupos.join("<|>");
        }
    }

    $("#array_matrizes").val(array_animais_selecionados);
    $("#array_grupos").val(array_grupos_selecionados);
    $("#ordem_grupos").val(array_ordem_grupos);

    var tipo_gravacao = $("#tipo_gravacao").val();
    var id_cobertura_lista_sem_grupo = $("#id_cobertura_lista_sem_grupo").val();
    var femeas_listadas = $("#femeas_listadas").val();
    var femeas_selecionadas = $("#total_selecionados").val();

    if (id_cobertura_lista_sem_grupo!='') {
        var opcao_gravar_grupo = $("input[name='opcao_gravar_grupo']:checked").val();

        if (femeas_listadas==femeas_selecionadas) {
            opcao_gravar_grupo = 'E';
        }

        if (opcao_gravar_grupo==undefined){
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Selecione uma opção!');
            return;
        }
    }
    else {
        var opcao_gravar_grupo = '';
    }

    $("#opcao_femeas_sem_grupo").val(opcao_gravar_grupo);

    var dados = $('#form_gravar_selecionados').serialize();

    $(".gravar_selecao").attr("disabled", true);

    $.ajax({
        type: "POST",
        url: 'gravar_matrizes_cobertura.php',
        data: dados,
        success: function(data){
            $("#id_cobertura_lista_sem_grupo").val('');

            if (data.error) {
                $(".gravar_selecao").attr("disabled", false);
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $(".gravar_selecao").attr("disabled", false);
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        }
    });
}

function gravar_inserir_matrizes() {
    var codigo_id = $("#codigo_id").val();
    
    if (codigo_id==0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Nº da Fêmea.');
        return;
    }

    var dados = $('#form_inserir_matriz').serialize();

    $(".gravar_inserir").attr("disabled", true);
    
    $.ajax({
        type: "POST",
        url: 'gravar_matrizes_inserir_nova.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $(".gravar_inserir").attr("disabled", false);
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $(".gravar_inserir").attr("disabled", false);
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        }
    });
}

function gravar_inserir_matrizes_monta() {
    var codigo_local = $("#codigo_local").val();
    var codigo_id = $("#id_animal").val();
    // A digitacao das datas foram retiradas do programa em 18/03/2025
    //var data_prenhes = $("#data_prenhes").val();
    //var previsao_parto = $("#data_previsao").val();

    var data_prenhes = '';
    var previsao_parto = '';

    if (codigo_id==0 || codigo_id==undefined) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Nº da Fêmea.');
        return;
    }

    var dados = $('#form_inserir_matriz').serialize();

    $(".gravar_inserir").attr("disabled", true);
    
    $.ajax({
        type: "POST",
        url: 'gravar_matrizes_inserir_nova_monta.php',
        data:  {
                codigo_local: codigo_local,
                codigo_id: codigo_id,
                data_prenhes: data_prenhes,
                previsao_parto: previsao_parto
            },
        success: function(data){
            if (data.error) {
                $(".gravar_inserir").attr("disabled", false);
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $(".gravar_inserir").attr("disabled", false);
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        }
    });
}

function gravar_inserir_matrizes_descarte() {
    var codigo_local = $("#codigo_local").val();
    var codigo_id = $("#id_animal_d").val();

    if (codigo_id==0 || codigo_id==undefined) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Nº da Fêmea.');
        return;
    }

    var dados = $('#form_inserir_matriz').serialize();

    $(".gravar_inserir").attr("disabled", true);
    
    $.ajax({
        type: "POST",
        url: 'gravar_matrizes_inserir_nova_descarte.php',
        data:  {
                codigo_local: codigo_local,
                codigo_id: codigo_id
            },
        success: function(data){
            if (data.error) {
                $(".gravar_inserir").attr("disabled", false);
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $(".gravar_inserir").attr("disabled", false);
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        }
    });
}

function ler_parametro(value) {
    var local = '';
    var estacao_monta = value;

    $.post("ler_parametro_estacao.php", {local:local, estacao_monta:estacao_monta}, function(valor){ 
        var php = valor.split("<|>");

        if (valor==''){
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Não exite Parametro da Estação de Monta para: ' + nome_fazenda);
            return;
        }
        else {
            var data_inicial = document.getElementsByName("inicio_estacao");
            var data_final = document.getElementsByName("fim_estacao");
            var codigo_id = document.getElementsByName("id_parametro");
            var nome_estacao = document.getElementsByName("nome_estacao");
            var selecao_estacao = document.getElementsByName("lista_estacoes");

            for (var i = 0; i < codigo_id.length; i++) {
                var estacao_selecionada = selecao_estacao[i].options[selecao_estacao[i].selectedIndex].value;

                if (estacao_selecionada==php[0]) {
                    codigo_id[i].value=php[0];
                    nome_estacao[i].value=php[1];
                    data_inicial[i].value=php[2];
                    data_final[i].value=php[3];
                }
            }
        }
    });
}

function gravar_parametros() {
    var fazenda = new Array();
    var array_fazenda = "";
    var codigo_fazenda = document.getElementsByName("codigo_fazenda");

    var nome_fazenda = document.getElementsByName("nome_fazenda");

    var codigo_parametro = new Array();
    var array_codigo_parametro = "";
    var id_parametro = document.getElementsByName("id_parametro");

    var estacao = new Array();
    var array_estacao = "";
    var nome_estacao = document.getElementsByName("nome_estacao");

    var inicio_estacao = new Array();
    var array_inicio_estacao = "";
    var data_inicio_estacao = document.getElementsByName("inicio_estacao");

    var fim_estacao = new Array();
    var array_fim_estacao = "";
    var data_fim_estacao = document.getElementsByName("fim_estacao");

    /*var alfa = new Array();
    var array_alfa = "";
    var codigo_alfa = document.getElementsByName("cod_alfa");

    var numerico = new Array();
    var array_numerico = "";
    var codigo_numerico = document.getElementsByName("cod_numerico");
    */

    for (var i = 0; i < codigo_fazenda.length; i++) {
        cod_fazenda = codigo_fazenda[i].value;
        fazenda.push(cod_fazenda);
        array_fazenda = fazenda.join("!");

        desc_fazenda = nome_fazenda[i].value;

        cod_id_parametro = id_parametro[i].value;
        codigo_parametro.push(cod_id_parametro);
        array_codigo_parametro = codigo_parametro.join("!");

        nom_estacao = nome_estacao[i].value;
        estacao.push(nom_estacao);
        array_estacao = estacao.join("!");

        data_ini_estacao = data_inicio_estacao[i].value;
        inicio_estacao.push(data_ini_estacao);
        array_inicio_estacao = inicio_estacao.join("!");

        data_f_estacao = data_fim_estacao[i].value;
        fim_estacao.push(data_f_estacao);
        array_fim_estacao = fim_estacao.join("!");

    /*    cod_alfa = codigo_alfa[i].value;
        alfa.push(cod_alfa);
        array_alfa = alfa.join("!");

        cod_numerico = codigo_numerico[i].value;
        numerico.push(cod_numerico);
        array_numerico = numerico.join("!");
    */
        if (nom_estacao!='') {
            if (data_ini_estacao=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe a data inicial da estação para a fazenda ' + desc_fazenda);
                return;
            }

            if (data_f_estacao=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe a data final da estação para a fazenda ' + desc_fazenda);
                return;
            }

            if (data_f_estacao<data_ini_estacao) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe as datas inicial e final corretamente para a fazenda ' + desc_fazenda);
                return;
            }

            /*if (cod_numerico=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe o código numérico da estação para a fazenda ' + desc_fazenda);
                return;
            }*/
        }

        //if (data_ini_estacao!='' || cod_numerico!='') {
        if (data_ini_estacao!='') {
            if (nom_estacao=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe o nome da estação para a fazenda ' + desc_fazenda);
                return;
            }
        }
    }

    $("#array_codigo_fazenda").val(array_fazenda);
    $("#array_codigo_parametro").val(array_codigo_parametro);
    $("#array_nome_estacao").val(array_estacao);
    $("#array_inicio_estacao").val(array_inicio_estacao);
    $("#array_fim_estacao").val(array_fim_estacao);
    //$("#array_codigo_alfa").val(array_alfa);
    //$("#array_codigo_numerico").val(array_numerico);

    var dados = $('#form_gravar_selecionados').serialize();
    $.ajax({
        type: "POST",
        url: 'gravar_parametro_estacao_monta.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else {
                $("#mensagem_retorno_parametro").modal();
                $("#mensagem_retorno_parametro .modal-body").html(data.message);
            }
        }
    });
}

function gravar_grupo(){
    var codigo_grupo = $("#codigo_grupo").val();
    var descricao_grupo = $("#descricao_grupo").val();
    var local = $("#local_id").val();
    var id_parametro_estacao = $("#id_estacao_monta").val();

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

    $("#codigo_local_grupo").val(local);
    $("#codigo_estacao_grupo").val(id_parametro_estacao);

    var dados = $('#form_grupo_estacao').serialize();

    $(".gravar_grupo").attr("disabled", true);

    $.ajax({
        type: "POST",
        url: 'gravar_grupo_estacao_monta.php',
        data: dados,
        success: function(data){
            if (data.error) {
                $(".gravar_grupo").attr("disabled", false);
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            else if (data.success){
                $(".gravar_grupo").attr("disabled", false);
                $("#mensagem_retorno_grupo").modal();
                $("#mensagem_retorno_grupo .modal-body").html(data.message);
            }
        }
    });
}

function popular_select_grupo(local, id_parametro_estacao) {
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
}

function consultar(){
    var estacao_monta = $("#estacao_monta").val();
    var local = $("#codigo_local").val();
    var codigo_alfa_numerico = $("#codigo_numerico").val();
    var tipo_registro = $("input[name='tipo_registro']:checked").val();

    if (tipo_registro=='C') {
        $('.busca').show();
    }
    else {
        $('.busca').hide();
    }

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
        $("#mensagem_erro .modal-body").html('Informe a Fazenda.');
        return;
    }

    if (estacao_monta=='000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Estação de Monta.');
        return;
    }

    if (tipo_registro==undefined) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe o Tipo de Registro.');
        return;
    }

    var options = $("#codigo_local option:selected");
    var codigo_local_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_local").text();
        codigo_local_filtro.push(desc.trim());
    });

    codigo_local_filtro = codigo_local_filtro;

    var options = $("#estacao_monta option:selected");
    var estacao_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#estacao_monta").text();
        estacao_filtro.push(desc.trim());
    });

    if (tipo_registro=='C') {
        estacao_filtro = "->Estação de Monta: " + estacao_filtro;
    }
    else {
        estacao_filtro = "";
    }

    if (tipo_registro == "C") {
        tipo_filtro = "IATF->";
    } else if (tipo_registro == "M"){
        tipo_filtro = "Monta->";
    } else {
        tipo_filtro = "Descarte->";
    }

    if (codigo_alfa_numerico!='') {
        codigo_filtro = '->Codigo Fêmea: '+codigo_alfa_numerico;
    }
    else {
        codigo_filtro = '';
    }

    var descricao_filtro =
        tipo_filtro +
        codigo_local_filtro +
        estacao_filtro +
        codigo_filtro;

    $(".digitar_filtros").hide();
    $(".busca_animal").show();
    $(".filtros_consulta").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".descricao_filtro").html(descricao_filtro);

    $('#aguardar').modal();

    $.post("form_lista_matrizes.php", {local:local, 
        estacao_monta:estacao_monta, 
        tipo_registro:tipo_registro,
        codigo_alfa_numerico:codigo_alfa_numerico},
        function(valor){ 
            $('#aguardar').modal('hide');
            $("div[id=lista_matrizes]").html(valor); 
    });
}

function exibe_mais_filtros() {
    $(".digitar_filtros").show();
    $(".mais_filtros").hide();
    $(".menos_filtros").show();
    $(".consultar").hide();
    $(".busca_animal").hide();
    $('.busca_consultar').hide();
    $('.busca_limpar').hide();
    $(".lista_contas").hide();
}

function exibe_menos_filtros() {
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
    $(".consultar").show();
    $(".lista_contas").show();
}

function diagnostico_monta(){
    var tipo_registro = $("input[name='tipo_registro']:checked").val();
    var local = $("#codigo_local").val();

    location.href='form_cobertura_animais_diagnostico.php?tipo_registro=' + tipo_registro + 
    '&local=' + local;

}

function importar_excel_lista_femeas($codigo,$desc_local,$qtd_animais,$codigo_local){
    $('.numero_doc').val($codigo);
    $('.codigo_local').val($desc_local);
    $('.femeas_listadas').val($qtd_animais);

    $('#codigo_local').val($codigo_local);
    $('#numero_doc').val($codigo);
    $('#femeas_listadas').val($qtd_animais);

    $('#modal_importar_excel').modal('show');
}

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

function adicionaZero(numero){
    if (numero <= 9) 
        return "0" + numero;
    else
        return numero; 
}
