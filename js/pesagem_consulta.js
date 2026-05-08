/**TABELA DE PESAGEM*/
let num_pesagem = 0;
let filtro_pesagem = '';
let filtro_local = '';
let filtro_epoca = '';
let divFiltroReproducaoVisivel = false;
var fazenda_selecionada_global = "";
var nome_fazenda_global = "";        
var finalizarInabilitado = '';

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

var configDataTable = {
    responsive: {
        details: {
            type: 'column',
            target: 12
        }
    },
    autoWidth: false,
    paging: false,
    ordering: true,
    order: [],
    info: false,
    language: { 
        sSearch: "Busca:", 
        zeroRecords: "Nada encontrado" 
    },
    dom: "<'row'<'col-sm-6'<'#container_filtro'>><'col-sm-6'f>>t",

    search: {
        smart: false,
        caseInsensitive: true
    },

    columnDefs: [
        // coluna do checkbox
        {
            className: "text-center coluna_selecao_motivo",
            targets: [0],
            orderable: false,
            searchable: false,
            visible: false
        },
        // colunas principais
        { responsivePriority: 1, targets: [1, 11, 12] },

        // controle de expansão (+)
        { className: 'dtr-control', targets: 12, orderable: false },

        // datas no padrão brasileiro
        { type: "date-br", targets: [5, 7] },

        // colunas que aparecem só no expand
        { className: 'none', targets: [13, 14, 15, 16, 17, 19] },

        // centralização
        { className: "text-center", targets: [0, 2, 3, 4, 10, 12] },

        { visible: false, searchable: false, targets: [18, 20] }
    ],

    initComplete: function () {
        $('#tabela_itens').css('width', '100%');

        var estaDesabilitado = $('.finalizar').is(':disabled');
        var corFrase = estaDesabilitado ? "color: red; font-weight: 400;" : "display: none;";

        $("#container_filtro").html(`
            <a href="#" style="font-size: 0.9em; font-weight: 500; color: #128cb8; line-height: 35px; margin-right: 15px;"
               onclick="filtro_apartacao()" data-toggle="tooltip" title="Filtro Apartação">
               <i class="fas fa-filter"></i> Filtro Apartação
            </a>

            <a href="#" id="btn_novo_motivo"
               style="font-size: 0.9em; font-weight: 500; color: #128cb8; line-height: 35px; margin-right: 10px;"
               onclick="alternarModoNovoMotivo(); return false;"
               data-toggle="tooltip" title="Selecionar animais para nova lista por motivo">
               <i class="fas fa-times"></i> Cancelar criar lista com Novo Motivo
            </a>

            <button type="button" id="btn_confirmar_novo_motivo"
                class="btn btn-success"
                style="display:none; margin-left: 5px;"
                onclick="abrirModalNovoMotivo()">
                Confirmar Itens Selecionados
            </button>

            <p style="${corFrase}">Existem animais repetidos nessa lista ou em outra pesagem não finalizada</p>
        `);
        atualizarVisibilidadeSelecaoMotivo();
    }
};

window.addEventListener("load", function (event) {
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

    var erro_importar_pesagem = $("#erro_importar_pesagem").val();

    if (erro_importar_pesagem != "" && erro_importar_pesagem != undefined) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(erro_importar_pesagem);
        $("#erro_importar_pesagem").val("");
    }

    $.post("lista_local.php", { tipo: 0 }, function (valor) {
        $("select[name=local_pesagem]").html(valor);
        $("select[name=codigo_local_filtro]").html(valor);
    });

    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque == "I") {
        $(".pasto").hide();
        $(".filtros").show();

        var local = $("#local_pesagem").val();
    } else {
        $(".pasto").show();
        $(".filtros").hide();
    }

    // Exibe filtros quando faz reload
    var filtro_local = $("#exibe_local").val();
 
    if (filtro_local!='' && filtro_local!=null) {
        var filtro_local = filtro_local.split(',');

        $.each(filtro_local, function(idx, val) {
            $('#codigo_local option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_local').selectpicker('refresh');
    }

    var filtro_categoria_rel = $("#exibe_categorias_rel").val();
 
    if (filtro_categoria_rel!='' && filtro_categoria_rel!=null) {
        var filtro_categoria_rel = filtro_categoria_rel.split(',');

        $.each(filtro_categoria_rel, function(idx, val) {
            $('#codigo_categoria_filtro option[value=' + val + ']').attr('selected', true);
        });

        $('#codigo_categoria_filtro').selectpicker('refresh');
    }

    var filtro_local_rel = $("#exibe_local_rel").val();
 
    if (filtro_local_rel!='' && filtro_local_rel!=null) {
        $("#codigo_fazenda").val(filtro_local_rel);
    }

    var lista_pesagem_automatico = $("#lista_pesagem_automatico").val();

    if (lista_pesagem_automatico=="S") {
        consultar();
    }
    
    if ($("#consultar_pesagem").val()=='S') {
        monta_lista_consultar_pesagem();
    }
});

function monta_lista_consultar_pesagem() {
    var numero_pesagem_id = $("#numero_pesagem_id").text();

    $.post("ler_pesagem_consulta.php", { pesagem_id: numero_pesagem_id,}, function (valor) {
        var php = valor.split("<|>");
        var php_array_itens = php[8].split("<!>");

        $(".descricao_filtro").text(php[0]);
        $(".descricao_filtro").val(php[0]);
        $(".descricao_lote").val(php[1]);
        $(".data_pesagem").val(php[2]);
        $("#data_pesagem").val(php[10]);
        $(".total_a_pesar").text(php[11]);
        $(".total_a_pesar").val(php[11]);
        $(".total_pesados").text(php[3]);
        $(".total_pesados").val(php[3]);
        $(".peso_total_kg").text(formatMoney(php[4]));
        $(".peso_total_kg").val(php[4]);
        $(".peso_total_arroba").text(formatMoney(php[5]));
        $(".peso_total_arroba").val(php[5]);
        $(".peso_medio_kg").text(formatMoney(php[6]));
        $(".peso_medio_kg").val(php[6]);
        $(".peso_medio_arroba").text(formatMoney(php[7]));
        $(".peso_medio_arroba").val(php[7]);
        $("#qtd_a_pesar").val(php[11]);
        $("#qtd_pesado").val(php[3]);

        // Monta Criterios de apartação do banco para o criterio dos itens de pesagem
        var criterios_string = php[12];

        if (criterios_string) {
            var array_criterios = criterios_string.split(",");
            $("#apartacao_item").empty();
            $("#apartacao_item").append('<option value="">...</option>');

            $.each(array_criterios, function(i, item) {
                var nome_limpo = item.trim(); // .trim() remove espaços extras se houver
                $("#apartacao_item").append($('<option>', { 
                    value: nome_limpo,
                    text : nome_limpo 
                }));
            });
        }

        finalizarInabilitado = '';

        html = "";
        for (var i = 0; i < php_array_itens.length; i++) {
            var itens = php_array_itens[i].split("|");

            if (itens[0]!=0 && itens[0]!='') {
                var codigo_animal = itens[0];
                var peso = itens[1];
                var sexo= itens[2];
                var nascimento = itens[3];
                var raca = itens[4];
                var pelagem = itens[5];
                var mae = itens[6];
                var obs_pesagem = itens[7];
                var codigo_id = itens[8];
                var apartacao = itens[15];
                var diferencaPeso = itens[16];
                var ultimo_peso = itens[17];
                var data_ultimo_peso = itens[18];
                var idade_meses = itens[19];
                var categoria = itens[20];
                var observacao = itens[21];
                var pai = itens[22];
                var mens_repetido = itens[24];
                var id_repetido = itens[25];

                if (itens[23]>1) {
                    var animal_repetido = 'S';
                    var finalizarInabilitado = 'S';
                }
                else {
                    var animal_repetido = 'N'; 
                }

                if (animal_repetido == 'S') {
                    var mensTooltip = (mens_repetido || '')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');

                    html += "<tr style='color:red;' class='linha_repetida' data-toggle='tooltip' data-placement='top' title='" + mensTooltip + "'>";
                } else {
                    html += "<tr>";
                }

                html += "<td class='coluna_selecao_motivo' style='text-align:center;'>" +
                        "<input type='checkbox' class='check_item_motivo' value='" + codigo_id + "' data-id-animal='" + codigo_id + "'>" +
                        "</td>";

                html +="<td class='id_animal'>" + codigo_animal + "</td>";
                html +="<td class='peso_animal' >" + peso + "</td>";
                html +="<td class='ganho'>" + diferencaPeso + "</td>";
                html +="<td class='ultimo_peso'>" + ultimo_peso + "</td>";
                html +="<td class='data_ultimo_peso' >" + data_ultimo_peso + "</td>";
                html +="<td class='sexo_animal'>" + sexo + "</td>";
                html +="<td class='nascimento_animal'>" + nascimento + "</td>";
                html +="<td class='apartacao'>" + apartacao + "</td>";
                html +="<td class='obs_pesagem'>" + obs_pesagem + "</td>";
                html +="<td class='mae_animal'>" + mae + "</td>";
                html +="<td class='categoria'>" + categoria + "</td>";
                html += "<td></td>";
                html +="<td class='idade_meses'>" + idade_meses + "</td>";
                html +="<td class='raca_animal'>" + raca + "</td>";
                html +="<td class='pelagem_animal'>" + pelagem + "</td>";
                html +="<td class='pai_animal'>" + pai + "</td>";
                html +="<td class='observacao'>" + observacao + "</td>";
                html +="<td class='codigo_id'>" + codigo_id + "</td>";
                html +="<td class='mens_repetido'>" + mens_repetido + "</td>";
                html +="<td class='id_repetido'>" + id_repetido + "</td>";
                html += '</tr>';
            }
        }

        if (finalizarInabilitado=='S') {
            $('.finalizar').prop('disabled', true);
        }
        else {
           $('.finalizar').prop('disabled', false);
        }

        if ($.fn.DataTable.isDataTable('#tabela_itens')) {
            $('#tabela_itens').DataTable().destroy();
            $('#tabela_itens tbody').empty();
        }

        $("#tabela_itens tbody").html(html);

        $('#tabela_itens tbody [data-toggle="tooltip"]').tooltip({
            container: 'body',
            trigger: 'hover'
        });

        $("#itens").show();
        $("#codigo_number_filtro").val("");
        $("#codigo_id").val(0);
        $("#peso_animal").val("");
        $("#observacao").val("");
        $("#descricao_animal").text("");
        $("#ultimo_peso").text("");
        $("#ult_peso_calculo").val("");
        $("#data_ult_peso").text("");
        $("#desc_descarte").text("");

        $("#tabela_itens").DataTable(configDataTable);
        atualizarVisibilidadeSelecaoMotivo();
        atualizarBotaoConfirmarNovoMotivo();
    })
}

var modoNovoMotivoAtivo = false;

function alternarModoNovoMotivo() {
    modoNovoMotivoAtivo = !modoNovoMotivoAtivo;

    atualizarVisibilidadeSelecaoMotivo();
    atualizarBotaoConfirmarNovoMotivo();
}

function atualizarVisibilidadeSelecaoMotivo() {

    if (!$.fn.DataTable.isDataTable('#tabela_itens')) {
        return;
    }

    var tabela = $('#tabela_itens').DataTable();

    if (modoNovoMotivoAtivo) {

        // MOSTRA a coluna do checkbox
        tabela.column(0).visible(true);

        $('#btn_novo_motivo').html(
            '<i class="fas fa-times"></i> Cancelar criar lista com Novo Motivo'
        );

    } else {

        // ESCONDE a coluna do checkbox
        tabela.column(0).visible(false);

        // limpa seleção
        $('#check_todos_motivo').prop('checked', false);
        $('.check_item_motivo').prop('checked', false);
        $('#btn_confirmar_novo_motivo').hide();

        $('#btn_novo_motivo').html(
            '<i class="fas fa-list-check"></i> Criar lista com novo Motivo'
        );
    }

    // reajusta tabela
    tabela.columns.adjust().responsive.recalc();
}

$(document).on('change', '#check_todos_motivo', function () {
    $('.check_item_motivo:visible').prop('checked', $(this).is(':checked'));
    atualizarBotaoConfirmarNovoMotivo();
});

$(document).on('change', '.check_item_motivo', function () {
    var totalVisiveis = $('.check_item_motivo:visible').length;
    var totalMarcados = $('.check_item_motivo:visible:checked').length;

    $('#check_todos_motivo').prop('checked', totalVisiveis > 0 && totalVisiveis === totalMarcados);

    atualizarBotaoConfirmarNovoMotivo();
});

function abrirModalNovoMotivo() {
    var selecionados = obterAnimaisSelecionadosNovoMotivo();

    if (selecionados.length === 0) {
        alert('Selecione pelo menos um item.');
        return;
    }

    $.post('modal_novo_motivo.php', {
        total_selecionados: selecionados.length,
        itens_selecionados: selecionados.join(',')
    }, function(retorno) {
        $('#modal_novo_motivo .modal-body').html(retorno);
        $('#modal_novo_motivo').modal('show');
    }).fail(function() {
        alert('Ocorreu um erro ao carregar o modal do novo motivo.');
    });
}

function obterAnimaisSelecionadosNovoMotivo() {
    var selecionados = [];

    $('.check_item_motivo:checked').each(function () {
        selecionados.push($(this).val());
    });

    return selecionados;
}

function atualizarBotaoConfirmarNovoMotivo() {
    var totalSelecionados = $('.check_item_motivo:checked').length;

    if (modoNovoMotivoAtivo && totalSelecionados > 0) {
        $('#btn_confirmar_novo_motivo').show();
    } else {
        $('#btn_confirmar_novo_motivo').hide();
    }
}

function montarArrayItensSelecionados() {
    var array_tabela_itens = [];
    var grupo_itens = "";

    $("#tabela_itens tbody tr").each(function () {

        // pega o checkbox da linha
        var checkbox = $(this).find(".check_item_motivo");

        // se não estiver marcado, ignora
        if (!checkbox.is(":checked")) return;

        var codigo = $(this).find(".id_animal").html();

        // segurança
        if (!codigo) return;

        var peso = $(this).find(".peso_animal").html();
        var sexo = $(this).find(".sexo_animal").html();
        var nascimento = $(this).find(".nascimento_animal").html();
        var pelagem = $(this).find(".pelagem_animal").html();
        var raca = $(this).find(".raca_animal").html();
        var mae = $(this).find(".mae_animal").html();
        var observacao = $(this).find(".obs_pesagem").html();
        var apartacao = $(this).find(".apartacao").html();
        var ultimo_peso = $(this).find(".ultimo_peso").html();
        var codigo_id = checkbox.data('id-animal') || 0;
        var mens_repetido = $(this).find(".mens_repetido").html() || "";
        var id_repetido = $(this).find(".id_repetido").html() || "";

        var valor = [
            codigo,
            peso,
            sexo,
            nascimento,
            raca,
            pelagem,
            mae,
            observacao,
            codigo_id,
            apartacao,
            mens_repetido,
            id_repetido,
            ultimo_peso
        ];

        array_tabela_itens.push(valor.join("|"));
    });

    grupo_itens = array_tabela_itens.join("<|>");

    return grupo_itens;
}

function calcularTotaisSelecionados() {
    var pesoTotal = 0;
    var qtdItensSelecionados = 0;

    $("#tabela_itens tbody tr").each(function () {

        var checkbox = $(this).find(".check_item_motivo");

        if (!checkbox.is(":checked")) return;

        var pesoTexto = $(this).find(".peso_animal").html() || "0";

        // trata número com vírgula ou ponto
        var peso = parseInt(pesoTexto, 10) || 0;

        pesoTotal += peso;
        qtdItensSelecionados++;
    });

    var pesoArr = pesoTotal / 30;
    var medioKg = qtdItensSelecionados > 0 ? (pesoTotal / qtdItensSelecionados) : 0;
    var medioArr = medioKg / 30;

    pesoArr = arredondarParaCima2Casas(pesoArr);
    medioKg = arredondarParaCima2Casas(medioKg);
    medioArr = arredondarParaCima2Casas(medioArr);

    return {
        pesoTotal: pesoTotal,
        pesoArr: pesoArr,
        medioKg: medioKg,
        medioArr: medioArr,
        qtd: qtdItensSelecionados
    };
}

// arredonda os calculos dos pesos
function arredondarParaCima2Casas(valor) {
    return Math.ceil(valor * 100) / 100;
}

// Grava nova pesagem com o novo motivo
$(document).on('click', '#btn_modal_confirmar_novo_motivo', function () {
    var grupo_itens = montarArrayItensSelecionados();
    var motivo = $("#novo_motivo_select").val();

    if (!grupo_itens) {
        alert("Nenhum item selecionado.");
        return;
    }

    if (!motivo) {
        $("#novo_motivo_select").focus();
        return;
    }

    var totais = calcularTotaisSelecionados();

    var local_pesagem = $("#local_pesagem").val();
    var descricao_lote= $("#descricao_lote").val();
    var descricao_filtro= $("#descricao_filtro").val();
    var data_pesagem = $("#data_pesagem").val();

    $.ajax({
        url: "gravar_pesagem_novo_motivo.php",
        type: "POST",
        data: {
            local_pesagem: local_pesagem,
            array_itens: grupo_itens,
            epoca_pesagem: motivo,
            descricao_filtro: descricao_filtro,
            descricao_lote: descricao_lote,
            data_pesagem: data_pesagem,
            peso_total_kg: totais.pesoTotal,
            peso_total_arroba: totais.pesoArr,
            peso_medio_kg: totais.medioKg,
            peso_medio_arroba: totais.medioArr,
            total_pesados: totais.qtd,
            total_a_pesar: totais.qtd
        },
        success: function (data) {
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            } else {
                $("#modal_novo_motivo").modal('hide');
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        },
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

function consultar() {
    var controle_estoque = $("#controle_estoque").val();
    var local = $("#codigo_local").val();
    var epoca = $("#codigo_pesagem").val();
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();

    if (data_inicial==undefined) {
        alert("Informe a Data Inicial e Final!");
        return;
    }

    if (data_inicial > data_final) {
        alert("Informe a Data Inicial e Final corretamente!");
        return;
    }

    //if (local == null || local == undefined) {
    //    local = '';
    //}

    if (local == null || local == undefined) {
        var array_fazenda= '';
    }
    else {
        var array_fazenda = new Array();
        var valor = new Array();

        for (i = 0; i <= local.length; i++) {
            valor[i]=local[i];
        }

        var array_fazenda=valor.join(",");
    }

    if (epoca == null) {
        epoca = [""];
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

    var options = $("#codigo_pesagem option:selected");
    var codigo_epoca_filtro = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_pesagem").text();
        codigo_epoca_filtro.push(desc.trim());
    });

    if (codigo_epoca_filtro != "") {
        codigo_epoca_filtro = "Motivo da Pesagem: " + codigo_epoca_filtro + "->";
    } else {
        codigo_epoca_filtro = "Motivo da Pesagem: Todos->";
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
        codigo_epoca_filtro +
        periodo;

    $(".digitar_filtros").hide();
    $(".filtros_consulta").show();
    $(".mais_filtros").show();
    $(".menos_filtros").hide();
    $(".consultar").show();
    $(".descricao_filtro").html(descricao_filtro);
    $('.voltar').show();
    
    $("#aguardar").modal();

    if (controle_estoque == "I") {
        $.post(
            "form_lista_pesagem_individual.php",
            {
                local: array_fazenda,
                epoca: epoca,
                data_inicial: data_inicial,
                data_final: data_final,
            },
            function (valor) {
                $('#aguardar').modal('hide');
                $("div[id=lista_pesagem]").html(valor);
            }
        );
        return;
    } else {
        $.post(
            "form_lista_pesagem_lote.php",
            {
                local: array_fazenda,
                epoca: epoca,
                data_inicial: data_inicial,
                data_final: data_final,
            },
            function (valor) {
                $('#aguardar').modal('hide');
                $("div[id=lista_pesagem]").html(valor);
            }
        );
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

function ler_animal_aplicar_filtro() {
    tout = setTimeout("ler_animal()", 500);

   /* var codigo_numerico = $("#codigo_number_filtro").val();
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
            $("#codigo_id").val(valor);
            ler_animal();
        }
    });*/
}

function ler_animal() {
    var id_animal = $("#codigo_number_filtro").val();

    $("#tabela_itens tbody tr").each(function () {
        var id_lista = $(this).find(".id_animal").html();

        if (id_lista == id_animal) {
            $("#codigo_number_filtro").val("");
            $("#peso_animal").val("");
            $("#observacao").val("");
            $("#descricao_animal").text("");
            $("#ultimo_peso").text("");
            $("#data_ult_peso").text("");
            $("#desc_descarte").text("");
            $("#alert_erro_animal .negrito").html("");
            $("#alert_erro_animal span").html(
                "Animal já consta na lista de pesagem!");
            $(".alert_erro_animal").show();
            document.getElementById("descricao_animal").text(" ");
            return;
        }
    });

    var codigo_alfa_numerico = $("#codigo_number_filtro").val();

    if (!codigo_alfa_numerico) {
        return
    }

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

    $.post(
        "ler_animal_filtros.php",
        {
            codigo_alfa_numerico:codigo_alfa_numerico, 
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
            num_natimorto_de: num_natimorto_de,
            num_natimorto_ate: num_natimorto_ate,
            filtro_num_natimorto:filtro_num_natimorto,
            previsao_parto_de: previsao_parto_de,
            previsao_parto_ate: previsao_parto_ate,
            filtro_previsao_parto:filtro_previsao_parto,
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
        function (valor) {
            var php = valor.split("<|>");

            if (php[0].trim() == "Nao tem animal") {
                $("#mensagem_erro_animal_filtro").modal();
                $("#codigo_number_filtro").val('');
                document.getElementById("codigo_number_filtro").focus();
                document.getElementById("codigo_number_filtro").style.borderColor = "red";
                return;
            } else {
                $("#descricao_animal").text(php[6]);
                $("#ultimo_peso").text('Último Peso: ' + php[28] + ' ');
                $("#data_ult_peso").text(php[33]);
                $("#desc_descarte").text(php[29]);
                $("#codigo_id").val(php[0]);
                $("#sexo_animal").val(php[1]);
                $("#nascimento_animal").val(php[2]);
                $("#raca_animal").val(php[3]);
                $("#pelagem_animal").val(php[4]);
                $("#mae_animal").val(php[5]);
                $("#peso_animal").focus();
            }

            if (php[7] == "N" && categoria != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "A Categoria do animal não consta no filtro de categorias selecionadas"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[8] == "N" && sexo != "Todos") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "A Sexo do animal não corresponde ao filtro selecionado"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[9] == "N" && codigos_racas != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "A Raça do animal não consta no filtro de raças selecionadas"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[10] == "N" && codigos_pais != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "O Pai do animal não consta no filtro de pais selecionados"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[11] == "N" && codigos_maes != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "A Mãe do animal não consta no filtro de mães selecionadas"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[12] == "N" && data_nasc_inicial != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "A Data de Nascimento do animal não consta no filtro de datas selecionadas"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[13] == "N" && peso_nasc_inicial != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "O Peso de Nascimento do animal não consta no filtro de pesos selecionados"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[14] == "N" && peso_desmama_inicial != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "O Peso da Desmama do animal não consta no filtro de pesos selecionados"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[15] == "N" && peso_ult_inicial != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "O Último Peso do animal não consta no filtro de pesos selecionados"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[19] == "N" && filtro_vacas_solteiras == "S") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "Fêmea não está solteira ou animal não corresponde ao filtro informado!"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[20] == "N" && filtro_descarte == "S") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "Este animal não é para descarte!");
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[20] == "S" && filtro_descarte == "N") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "Este animal é para descarte!");
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[21] == "N" && filtro_vacas_paridas == "S") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "Fêmea não está parida ou animal não corresponde ao filtro informado!"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[22] == "N" && num_parto_de != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "Nº partos ou animal não corresponde ao filtro informado!"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[23] == "N" && num_aborto_de != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "Nº abortos ou animal não corresponde ao filtro informado!"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[32] == "N" && num_natimorto_de != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "Nº Natimortos ou animal não corresponde ao filtro informado!"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[24] == "N" && previsao_parto_de != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "Previsão de Parto não corresponde ao filtro informado!"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[25] == "N" && origem != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "Origem do animal não corresponde ao filtro informado!"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            /*if (
                (filtro_positivas == "S" && filtro_negativas == "S") ||
                (filtro_positivas == "" && filtro_negativas == "")
            ) {
                $("#peso_animal").focus();
                return;
            }*/

            if (filtro_positivas == "S" && filtro_negativas == "N") {
                if (php[26] == "N") {
                    $("#alert_erro_animal .negrito").html("");
                    $("#alert_erro_animal span").html(
                        "Diagnostico não corresponde ao filtro informado!"
                    );
                    $(".alert_erro_animal").show();
                }
            }

            if (filtro_positivas == "N" && filtro_negativas == "S") {
                if (php[27] == "N") {
                    $("#alert_erro_animal .negrito").html("");
                    $("#alert_erro_animal span").html(
                        "Diagnostico não corresponde ao filtro informado!"
                    );
                    $(".alert_erro_animal").show();
                }
            }

            if (php[30] == "N" && data_paricao_de != "") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "Data da Parição não corresponde ao filtro informado!"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }

            if (php[31] == "N" && filtro_vacas_prenhas == "S") {
                $("#alert_erro_animal .negrito").html("");
                $("#alert_erro_animal span").html(
                    "Fêmea não está prenha ou animal não corresponde ao filtro informado!"
                );
                $(".alert_erro_animal").show();
                $("#peso_animal").focus();
                return;
            }
        }
    );
}

function ler_animal_editar_online() {
    tout = setTimeout("ler_animal_editar_apos_time()", 500);
}

function ler_animal_editar_apos_time() {
    var id_animal = $("#codigo_number_filtro").val();

    $("#tabela_itens tbody tr").each(function () {
        var id_lista =$(this).find(".id_animal").html();

        if (id_lista == id_animal) {
            id_animal='';
            $("#codigo_id").val("");
            $("#codigo_number_filtro").val("");
            $("#peso_animal").val("");
            $("#observacao").val("");
            $("#descricao_animal").text("");
            $("#ultimo_peso").text("");
            $("#ult_peso_calculo").val("");
            $("#data_ult_peso").text("");
            $("#desc_descarte").text("");
            $("#alert_erro_animal .negrito").html("");
            $("#alert_erro_animal span").html(
                "Animal já consta na lista de pesagem!");
            $(".alert_erro_animal").show();
            document.getElementById("descricao_animal").text(" ");
            return;
        }
    });

    if (id_animal!='') {
        var codigo_alfa_numerico = $("#codigo_number_filtro").val();
        var local = $("#local_pesagem").val();
        var origem = [""];
        var categoria = [""];
        var codigos_racas = [""];
        var codigos_pais = [""];
        var codigos_maes = [""];
        var sexo=['Todos'];
        var data_nasc_inicial = '';
        var data_nasc_final = '';
        var peso_nasc_inicial = '';
        var peso_nasc_final = '';
        var peso_desmama_inicial = '';
        var peso_desmama_final = '';
        var peso_ult_inicial = '';
        var peso_ult_final = '';
        var num_parto_de = '';
        var num_parto_ate = '';
        var num_aborto_de = '';
        var num_aborto_ate = '';
        var num_natimorto_de = '';
        var num_natimorto_ate = '';
        var previsao_parto_de = '';
        var previsao_parto_ate = '';
        var positivo = "";
        var negativo = "";
        var estacao = '';
        var data_paricao_de = '';
        var data_paricao_ate = '';
        var filtro_estacao=[''];
        var ativo='S';
        var filtro_reproducao = 'N';
        var filtro_num_parto = 'N';
        var filtro_num_aborto = 'N';
        var filtro_num_natimorto = 'N';
        var filtro_previsao_parto = 'N';
        var filtro_data_paricao = 'N';
        var filtro_vacas_paridas='N';
        var filtro_vacas_solteiras = 'N';
        var filtro_vacas_prenhas = 'N';
        var filtro_descarte = '';
        var filtro_positivas = 'N';
        var filtro_negativas = 'N';
        var filtro_monta_natural = 'N';

        $.post(
            "ler_animal_filtros.php",
            {
                codigo_alfa_numerico:codigo_alfa_numerico, 
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
                num_natimorto_de: num_natimorto_de,
                num_natimorto_ate: num_natimorto_ate,
                filtro_num_natimorto:filtro_num_natimorto,
                previsao_parto_de: previsao_parto_de,
                previsao_parto_ate: previsao_parto_ate,
                filtro_previsao_parto:filtro_previsao_parto,
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
            function (valor) {
                var php = valor.split("<|>");

                if (php[0].trim() == "Nao tem animal") {
                    $("#mensagem_erro_animal_filtro").modal();
                    $("#codigo_number_filtro").val('');
                    document.getElementById("codigo_number_filtro").focus();
                    document.getElementById("codigo_number_filtro").style.borderColor = "red";
                    return;
                } else {
                    $("#alert_erro_animal .negrito").html("");
                    $("#alert_erro_animal span").html("");
                    $(".alert_erro_animal").hide();
                    $("#descricao_animal").text(php[6]);
                    $("#ultimo_peso").text('Último Peso: ' + php[28] + ' ');
                    $("#ult_peso_calculo").val(php[28]);
                    $("#data_ult_peso").text(php[33]);
                    $("#desc_descarte").text(php[29]);
                    $("#codigo_id").val(php[0]);
                    $("#sexo_animal").val(php[1]);
                    $("#nascimento_animal").val(php[2]);
                    $("#raca_animal").val(php[3]);
                    $("#pelagem_animal").val(php[4]);
                    $("#mae_animal").val(php[5]);
                    $("#pai_animal").val(php[34]);
                    $("#desc_categoria").val(php[17]);
                    $("#idade_meses").val(php[36]);
                    $("#observacao_cadastro").val(php[35]);

                    $("#peso_animal").focus();
                }

            }
        );
    }
}

function seleciona_filtros() {
    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque == "I") {
        var fechar = exibe_filtro();

        if (fechar) {
            $("#modal_filtros").modal("hide");
        }
    } else {
        var fechar = exibe_filtro_lote();

        if (fechar) {
            $("#modal_filtros").modal("hide");
        }
    }
}

/*function exibe_filtro() {
    var local = "";
    var epoca = "";
    var categorias = "";
    var data_filtro = "";
    var peso_nasc_filtro = "";
    var peso_desmama_filtro = "";
    var peso_ult_filtro = "";
    var fechar = false;

    var select = $("#local_pesagem").val();

    if (select != 0 && select != null) {
        select = document.getElementById("local_pesagem");
        local = select.options[select.selectedIndex].text;
    }

    var select = $("#epoca_pesagem").val();

    if (select != 0) {
        select = document.getElementById("epoca_pesagem");
        epoca = "->" + select.options[select.selectedIndex].text;
    }

    var options = $("#codigo_categoria_filtro option:selected");
    var categorias = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_categoria_filtro").text();
        categorias.push(desc.trim());
    });

    if (categorias != "") {
        categorias = "->" + categorias;
    }

    var options = $("#codigo_origem_filtro option:selected");
    var origens = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_origem_filtro").text();
        origens.push(desc.trim());
    });

    if (origens != "") {
        origens = "->Origem:" + origens;
    }

    if ($("#macho").is(":checked")==true && $("#femea").is(":checked")==true) {
        sexo = ["Todos"];
    } else if ($("#macho").is(":checked")==true) {
        sexo = ["Machos"];
    } else if ($("#femea").is(":checked")==true) {
        sexo = ["Femeas"];
    }

    var options = $("#codigo_raca_filtro option:selected");
    var racas = [];

    $(options).each(function () {
        var desc_raca = $(this).bind("#codigo_raca_filtro").text();
        racas.push(desc_raca.trim());
    });

    if (racas != "") {
        racas = "->" + racas;
    }

    var options = $("#codigo_pai_filtro option:selected");
    var pai = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_pai_filtro").text();
        pai.push(desc.trim());
    });

    if (pai != "") {
        pai = "->Pai:" + pai;
    }

    var options = $("#codigo_mae_filtro option:selected");
    var mae = [];

    $(options).each(function () {
        var desc = $(this).bind("#codigo_mae_filtro").text();
        mae.push(desc.trim());
    });

    if (mae != "") {
        mae = "->Mãe:" + mae;
    }

    var data_nasc_inicial = $("#data_nasc_inicial_filtro").val();
    var data_nasc_final = $("#data_nasc_final_filtro").val();

    if (data_nasc_inicial > data_nasc_final) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Informe a Data de Nascimento Inicial e Final corretamente!"
        );
        return;
    }

    if (data_nasc_inicial != 0 && data_nasc_final != 0) {
        var data_ini = data_nasc_inicial.split("-");
        var dia_ini = data_ini[2];
        var mes_ini = data_ini[1];
        var ano_ini = data_ini[0];

        var data_fim = data_nasc_final.split("-");
        var dia_fim = data_fim[2];
        var mes_fim = data_fim[1];
        var ano_fim = data_fim[0];
        data_filtro =
            "->Data Nasc: de " +
            dia_ini +
            "/" +
            mes_ini +
            "/" +
            ano_ini +
            " até " +
            dia_fim +
            "/" +
            mes_fim +
            "/" +
            ano_fim;
    }

    var peso_nasc_inicial = $("#peso_inicial_nasc_filtro").val();
    var peso_nasc_final = $("#peso_final_nasc_filtro").val();

    if (peso_nasc_inicial != "" || peso_nasc_final != "") {
        var peso_nasc_inicial = parseFloat(
            $("#peso_inicial_nasc_filtro").val()
        );
        var peso_nasc_final = parseFloat($("#peso_final_nasc_filtro").val());

        if (peso_nasc_inicial > peso_nasc_final) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Informe o Peso de Nascimento Inicial e Final corretamente!"
            );
            return;
        }

        peso_nasc_filtro =
            "->Peso Nasc: de " +
            peso_nasc_inicial +
            " até " +
            peso_nasc_final +
            " Kg";
    }

    var peso_desmama_inicial = $("#peso_inicial_desmama_filtro").val();
    var peso_desmama_final = $("#peso_final_desmama_filtro").val();

    if (peso_desmama_inicial != "" || peso_desmama_final != "") {
        var peso_desmama_inicial = parseFloat(
            $("#peso_inicial_desmama_filtro").val()
        );
        var peso_desmama_final = parseFloat(
            $("#peso_final_desmama_filtro").val()
        );

        if (peso_desmama_inicial > peso_desmama_final) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Informe o Peso de Desmama Inicial e Final corretamente!"
            );
            return;
        }

        peso_desmama_filtro =
            "->Peso Desmama: de " +
            peso_desmama_inicial +
            " até " +
            peso_desmama_final +
            " Kg";
    }

    var peso_ult_inicial = $("#peso_inicial_ultimo_filtro").val();
    var peso_ult_final = $("#peso_final_ultimo_filtro").val();

    if (peso_ult_inicial != "" || peso_ult_final != "") {
        var peso_ult_inicial = parseFloat(
            $("#peso_inicial_ultimo_filtro").val()
        );
        var peso_ult_final = parseFloat($("#peso_final_ultimo_filtro").val());

        if (peso_ult_inicial > peso_ult_final) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Informe o Último Peso Inicial e Final corretamente!"
            );
            return;
        }

        peso_ult_filtro =
            "->Últ Peso : de " +
            peso_ult_inicial +
            " até " +
            peso_ult_final +
            " Kg";
    }

    var data_parto_de = $("#previsao_parto_de_filtro").val();
    var data_parto_ate = $("#previsao_parto_ate_filtro").val();

    if (data_parto_de != "" || data_parto_ate != "") {
        if (data_parto_de == "") {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Previsão do Parto (de) não pode ser vazio!"
            );
            return;
        }
        ("");

        if (data_parto_ate == "") {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Previsão do Parto (até) não pode ser vazio!"
            );
            return;
        }

        if (data_parto_de > data_parto_ate) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Informe a Data Previsão de Parto De e Até corretamente!"
            );
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
        previsao_filtro =
            "->Previsao Parto: de " +
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
    } else {
        previsao_filtro = "";
    }

    var parto_de = $("#num_parto_de_filtro").val();
    var parto_ate = $("#num_parto_ate_filtro").val();

    if (parto_de != "" || parto_ate != "") {
        if (parto_de == "") {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Nº Partos (de) não pode ser vazio!"
            );
            return;
        }

        if (parto_ate == "") {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Nº Partos (até) não pode ser vazio!"
            );
            return;
        }

        if (parto_de > parto_ate) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Informe o Nº de Partos De e Até corretamente!"
            );
            return;
        }

        partos_filtro = "->Partos: de " + parto_de + " ate " + parto_ate;
    } else {
        partos_filtro = "";
    }

    var aborto_de = $("#num_aborto_de_filtro").val();
    var aborto_ate = $("#num_aborto_ate_filtro").val();

    if (aborto_de != "" || aborto_ate != "") {
        if (aborto_de == "") {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Nº Abortos (de) não pode ser vazio!"
            );
            return;
        }

        if (aborto_ate == "") {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Nº Abortos (até) não pode ser vazio!"
            );
            return;
        }

        if (aborto_de > aborto_ate) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "Informe o Nº de Abortos De e Até corretamente!"
            );
            return;
        }

        aborto_filtro = "->Abortos: de " + aborto_de + " ate " + aborto_ate;
    } else {
        aborto_filtro = "";
    }

    if ($("#vacas_paridas").is(":checked") == true) {
        paridas = "VP";
    } else {
        paridas = "";
    }

    var paridas_ate = $("#paridas_ate").val();

    if (paridas == "VP" && paridas_ate == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe Paridase Até!");
        return;
    }

    if (paridas == "VP") {
        var data_fim = paridas_ate.split("-");
        var dia_fim = data_fim[2];
        var mes_fim = data_fim[1];
        var ano_fim = data_fim[0];
        filtro_paridas =
            "->Paridas ate " + dia_fim + "/" + mes_fim + "/" + ano_fim;
    } else {
        filtro_paridas = "";
    }

    if ($("#vacas_solteiras").is(":checked") == true) {
        solteiras = "VS";
    } else {
        solteiras = "";
    }

    if (solteiras == "VS") {
        filtro_solteiras = "->Solteiras";
    } else {
        filtro_solteiras = "";
    }

    if ($("#descarte").is(":checked") == true) {
        descarte = "DC";
    } else {
        descarte = "";
    }

    if (descarte == "DC") {
        filtro_descarte = "->Descarte";
    } else {
        filtro_descarte = "";
    }

    if ($("#positivo").is(":checked") == true) {
        positivo = "DP";
    } else {
        positivo = "";
    }

    if (positivo == "DP") {
        filtro_positivo = "->Diagnostico Positivo";
    } else {
        filtro_positivo = "";
    }

    if ($("#negativo").is(":checked") == true) {
        negativo = "DN";
    } else {
        negativo = "";
    }

    if (negativo == "DN") {
        filtro_negativo = "->Diagnostico Negativo";
    } else {
        filtro_negativo = "";
    }

    var estacao = $("#codigo_estacao_filtro").val();

    if (estacao!='') {
        filtro_estacao = 'Estação:' + estacao + '->';
    }
    else {
        filtro_estacao = '';
    }

    var descricao_filtro =
        filtro_estacao+
        local +
        epoca +
        categorias +
        origens +
        "->Sexo:" +
        sexo +
        racas +
        pai +
        mae +
        data_filtro +
        peso_nasc_filtro +
        peso_desmama_filtro +
        peso_ult_filtro +
        previsao_filtro +
        partos_filtro +
        aborto_filtro +
        filtro_paridas +
        filtro_solteiras +
        filtro_descarte +
        filtro_positivo +
        filtro_negativo;

    $(".descricao_filtro").val(descricao_filtro);
    $(".descricao_filtro").text(descricao_filtro);
    fechar = true;
    return fechar;
}*/

function exibe_filtro_lote() {
    var local = "";
    var epoca = "";
    var pastos = "";
    var categorias = "";
    var data_filtro = "";

    var select = $("#local_pesagem").val();

    if (select != 0) {
        select = document.getElementById("local_pesagem");
        local = select.options[select.selectedIndex].text;
    }

    var select = $("#epoca_pesagem").val();

    if (select != 0) {
        select = document.getElementById("epoca_pesagem");
        epoca = "->" + select.options[select.selectedIndex].text;
    }

    var options = $("#pasto option:selected");
    var pastos = [];

    $(options).each(function () {
        var desc = $(this).bind("#pasto").text();
        pastos.push(desc.trim());
    });

    if (pastos != "") {
        pastos = "->Pasto:" + pastos;
    } else {
        pastos = "->Pasto:Todos";
    }

    var options = $("#categoria_filtro option:selected");
    var categorias = [];

    $(options).each(function () {
        var desc = $(this).bind("#categoria_filtro").text();
        categorias.push(desc.trim());
    });

    if (categorias != "") {
        categorias = "->" + categorias;
    }

    if ($("#macho").is(":checked")==true && $("#femea").is(":checked")==true) {
        sexo = ["Todos"];
    } else if ($("#macho").is(":checked")==true) {
        sexo = ["Machos"];
    } else if ($("#femea").is(":checked")==true) {
        sexo = ["Femeas"];
    }

    var descricao_filtro =
        local + epoca + pastos + categorias +"->Sexo:" + sexo;

    $(".descricao_filtro").val(descricao_filtro);
    $(".descricao_filtro").text(descricao_filtro);
    fechar = true;
    return fechar;
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
    const valoresSelecionados = $(selectId).val() || [];

    if (valoresSelecionados.length === 0) {
        $(selectId).val(valoresParaMarcar);
        $(selectId).selectpicker('refresh'); 
    }

    exibe_filtro(); 
}

function aplicar_filtros() {
    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque=='I') {
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
        var num_natimorto_ate =$("#num_natimorto_ate_filtro").val();
        var previsao_parto_de = $("#previsao_parto_de_filtro").val();
        var previsao_parto_ate = $("#previsao_parto_ate_filtro").val();
        var data_paricao_de = $("#data_paricao_de_filtro").val();
        var data_paricao_ate = $("#data_paricao_ate_filtro").val();
        var filtro_estacao = $("#codigo_estacao_filtro").val();
        var ativo='S';

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
    else {
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
        else {
            $("#mensagem_erro_filtro").modal();
            $("#mensagem_erro_filtro .modal-body").html('Informe o Sexo!');
            return;
        }
        
        exibe_filtro();
    }
}
function redigita_animal_filtro(){
    $('#modal_filtros').modal('show');
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

    var options = $('#codigo_local_filtro option:selected');
    var local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_local_filtro').text();
        local_filtro.push( desc.trim() );
    });

    if (local_filtro!=''){
        local_filtro = local_filtro;
    }
    else {
        local_filtro = '';
    }

    var options = $('#epoca_pesagem_filtro option:selected');

    var motivo_filtro = [];

    $(options).each(function(){
        var optionValue = $(this).val();

        if (optionValue !== "" && optionValue !== "000") {
            var desc = $(this).text();
            motivo_filtro.push( desc.trim() );
        }
    });

    if (motivo_filtro!=''){
        motivo_filtro = '->Motivo Pesagem:'+motivo_filtro;
    }
    else {
        motivo_filtro = '';
    }

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

        var descricao_filtro = 'Filtros:' + local_filtro+
            motivo_filtro+
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
            aborto_filtro+
            natimorto_filtro;

    $(".descricao_filtro").val(descricao_filtro);
    $(".descricao_filtro").text(descricao_filtro);
    fechar = true;
    return fechar;
}

function limpar_filtros() {
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
    $("#num_natimorto_de_filtro").val('');
    $("#num_natimorto_ate_filtro").val('');
    $("#vacas_paridas").prop("checked", false);
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

function filtros() {
    $("#modal_filtros").modal("show");
}

function Salvar() {
    $("#alert_erro_animal .negrito").html("");
    $("#alert_erro_animal span").html("");
    $(".alert_erro_animal").hide();

    if ($("#lote").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe a Descrição do Lote!");
        $(".alert_erro_animal").show();
        return;
    }

    if ($("#qtd_a_pesar").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe a Quantidade de Animais para Pesar!");
        $(".alert_erro_animal").show();
        return;
    }

    var animal_codigo_id = $("#codigo_id").val();

    if (animal_codigo_id == 0) {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Id do animal não Informado.");
        $(".alert_erro_animal").show();
        return;
    }

    if ($("#peso_animal").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe o Peso do animal!");
        $(".alert_erro_animal").show();
        return;
    }

    var qtd_pesar = $("#qtd_a_pesar").val();
    var qtd_pesado = $("#qtd_pesado").val();

    qtd_pesado++;

    if (qtd_pesado > qtd_pesar && qtd_pesar != 0) {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html(
            "Quantida de animais pesados está maior que a quantidade informada!"
        );
        document.getElementById("qtd_pesado").style.borderColor = "red";
        $(".alert_erro_animal").show();
    }

    $("#qtd_pesado").val(qtd_pesado);
 
    // 1. Pega a instância do DataTable
    var t = $('#tabela_itens').DataTable();

    // 2. Adiciona os dados na tabela e renderiza
    var novaLinha = t.row.add([
        removerZeros($("#codigo_number_filtro").val()), // 0
        $("#peso_animal").val(),                        // 1
        $("#sexo_animal").val(),                        // 2
        $("#nascimento_animal").val(),                  // 3
        $("#raca_animal").val(),                        // 4
        $("#pelagem_animal").val(),                     // 5
        $("#mae_animal").val(),                         // 6
        $("#observacao").val(),                         // 7
        $("#codigo_id").val(),                          // 8
        "<div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div>" // 9
    ]).draw(false).node();

    // 3. Mapeia todas as colunas para colocar as classes (Uso o $td para facilitar)
    var $td = $(novaLinha).find('td');

    $td.eq(0).addClass('id_animal');
    $td.eq(1).addClass('peso_animal');
    $td.eq(2).addClass('sexo_animal');
    $td.eq(3).addClass('nascimento_animal');
    $td.eq(4).addClass('raca_animal');
    $td.eq(5).addClass('pelagem_animal');
    $td.eq(6).addClass('mae_animal');
    $td.eq(7).addClass('obs_pesagem');
    $td.eq(8).addClass('codigo_id');

    // 4. Reatribui os eventos de clique para os botões Editar e Excluir
    $(novaLinha).find(".btnEditar").on("click", modal_editar_item);
    $(novaLinha).find(".btnExcluir").on("click", excluir_item);

    t.columns.adjust().draw();

    /*$("#tabela_itens tbody").append(
        "<tr>" +
            "<td width='8%' class='id_animal'>" + 
            removerZeros($("#codigo_number_filtro").val()) +
            "</td>" +
            "<td width='8%' class='peso_animal'>" +
            $("#peso_animal").val() +
            "</td>" +
            "<td width='8%' class='sexo_animal'>" +
            $("#sexo_animal").val() +
            "</td>" +
            "<td width='8%' class='nascimento_animal'>" +
            $("#nascimento_animal").val() +
            "</td>" +
            "<td width='10%' class='raca_animal'>" +
            $("#raca_animal").val() +
            "</td>" +
            "<td width='8%' class='pelagem_animal'>" +
            $("#pelagem_animal").val() +
            "</td>" +
            "<td width='8%' class='mae_animal'>" +
            $("#mae_animal").val() +
            "</td>" +
            "<td width='18%' class='obs_pesagem'>" +
            $("#observacao").val() +
            "</td>" +
            "<td width='8%' hidden='' class='codigo_id'>" +
            $("#codigo_id").val() +
            "</td>" +
            "<td width='12%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>" +
            "</tr>"
    );
    $(".btnEditar").bind("click", modal_editar_item);
    $(".btnExcluir").bind("click", excluir_item);*/

    $("#itens").show();
    $("#codigo_number_filtro").val("");
    $("#codigo_id").val(0);
    $("#peso_animal").val("");
    $("#observacao").val("");
    $("#descricao_animal").text("");
    $("#ultimo_peso").text("");
    $("#ult_peso_calculo").val("");
    $("#data_ult_peso").text("");
    $("#desc_descarte").text("");

    somar_totais();
    gravar_pesagem(1);
    document.getElementById("codigo_number_filtro").focus();
}

// Incluida no programa form_pesagem_animais_editar_online.php
/*function SalvarIncluirEdicao() {
    $("#alert_erro_animal .negrito").html("");
    $("#alert_erro_animal span").html("");
    $(".alert_erro_animal").hide();

    if ($("#lote").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe a Descrição do Lote!");
        $(".alert_erro_animal").show();
        return;
    }

    if ($("#qtd_a_pesar").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe a Quantidade de Animais para Pesar!");
        $(".alert_erro_animal").show();
        return;
    }

    var animal_codigo_id = $("#codigo_id").val();

    if (animal_codigo_id == 0) {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Id do animal não Informado.");
        $(".alert_erro_animal").show();
        return;
    }

    if ($("#peso_animal").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe o Peso do animal!");
        $(".alert_erro_animal").show();
        return;
    }

    var qtd_pesar = $("#qtd_a_pesar").val();
    var qtd_pesado = $("#qtd_pesado").val();

    qtd_pesado++;

    if (qtd_pesado > qtd_pesar && qtd_pesar != 0) {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html(
            "Quantida de animais pesados está maior que a quantidade informada!"
        );
        document.getElementById("qtd_pesado").style.borderColor = "red";
        $(".alert_erro_animal").show();
    }

    $("#qtd_pesado").val(qtd_pesado);
 
    var peso = $("#peso_animal").val();
    var ultimoPeso = $("#ult_peso_calculo").val();
    var diferencaPeso = peso - ultimoPeso;

    // 1. Pega a instância do DataTable
    var t = $('#tabela_itens').DataTable();

    // 2. Adiciona os dados na tabela e renderiza
    var novaLinha = t.row.add([
        removerZeros($("#codigo_number_filtro").val()), // 0
        $("#peso_animal").val(),                        // 1
        diferencaPeso,                                  // 2
        $("#ult_peso_calculo").val(),                   // 3 (ultimo_peso)
        $("#data_ult_peso").text(),                     // 4
        $("#sexo_animal").val(),                        // 5
        $("#nascimento_animal").val(),                  // 6
        $("#apartacao_item").val(),                     // 7
        $("#observacao").val(),                         // 8
        $("#mae_animal").val(),                         // 9
        $("#desc_categoria").val(),                     // 10
        "",                                             // 11 (Bolinha/Controle)
        $("#idade_meses").val(),                        // 12
        $("#raca_animal").val(),                        // 13
        $("#pelagem_animal").val(),                     // 14
        $("#pai_animal").val(),                         // 15
        $("#observacao_cadastro").val(),                // 16
        $("#codigo_id").val(),                          // 17
        '',                                             // 18 mens_repetido
        '',                                             // 19 id_repetido
        // VER AQUI
        "<div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div>" // 19
    ]).draw(false).node();

    // 3. Mapeia todas as colunas para colocar as classes (Uso o $td para facilitar)
    var $td = $(novaLinha).find('td');

    $td.eq(0).addClass('id_animal');
    $td.eq(1).addClass('peso_animal');
    $td.eq(2).addClass('ganho');
    $td.eq(3).addClass('ultimo_peso');
    $td.eq(4).addClass('data_ultimo_peso');
    $td.eq(5).addClass('sexo_animal');
    $td.eq(6).addClass('nascimento_animal');
    $td.eq(7).addClass('apartacao');
    $td.eq(8).addClass('obs_pesagem');
    $td.eq(9).addClass('mae_animal');
    $td.eq(10).addClass('categoria');
    // eq(11) é a bolinha, não precisa de classe de dado
    $td.eq(12).addClass('idade_meses');
    $td.eq(13).addClass('raca_animal');
    $td.eq(14).addClass('pelagem_animal');
    $td.eq(15).addClass('pai_animal');
    $td.eq(16).addClass('observacao');
    $td.eq(17).addClass('codigo_id').attr('hidden', true);
    $td.eq(18).addClass('mens_repetido');
    $td.eq(19).addClass('id_repetido').attr('hidden', true);

    // 4. Reatribui os eventos de clique para os botões Editar e Excluir
    $(novaLinha).find(".btnEditar").on("click", modal_editar_item);
    $(novaLinha).find(".btnExcluir").on("click", excluirItemEdicao);

    // 5. Verificação de Animal Repetido (Pintar de Vermelho se necessário)
    // Aqui você pode adicionar a lógica se o animal for repetido:
    // $(novaLinha).css('color', 'red');

    $("#itens").show();
    $("#codigo_number_filtro").val("");
    $("#codigo_id").val(0);
    $("#peso_animal").val("");
    $("#observacao").val("");
    $("#descricao_animal").text("");
    $("#ultimo_peso").text("");
    $("#ult_peso_calculo").val("");
    $("#data_ult_peso").text("");
    $("#desc_descarte").text("");

    somar_totais();
    gravar_pesagem(2);
    document.getElementById("codigo_number_filtro").focus();
}*/

function SalvarIncluirEdicao() {
    $("#alert_erro_animal .negrito").html("");
    $("#alert_erro_animal span").html("");
    $(".alert_erro_animal").hide();

    if ($("#lote").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe a Descrição do Lote!");
        $(".alert_erro_animal").show();
        return;
    }

    if ($("#qtd_a_pesar").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe a Quantidade de Animais para Pesar!");
        $(".alert_erro_animal").show();
        return;
    }

    var animal_codigo_id = $("#codigo_id").val();

    if (animal_codigo_id == 0) {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Id do animal não Informado.");
        $(".alert_erro_animal").show();
        return;
    }

    if ($("#peso_animal").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe o Peso do animal!");
        $(".alert_erro_animal").show();
        return;
    }

    var qtd_pesar = parseInt($("#qtd_a_pesar").val(), 10) || 0;
    var qtd_pesado = parseInt($("#qtd_pesado").val(), 10) || 0;

    qtd_pesado++;

    if (qtd_pesado > qtd_pesar && qtd_pesar != 0) {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html(
            "Quantida de animais pesados está maior que a quantidade informada!"
        );
        document.getElementById("qtd_pesado").style.borderColor = "red";
        $(".alert_erro_animal").show();
    }

    $("#qtd_pesado").val(qtd_pesado);

    var peso = parseFloat($("#peso_animal").val()) || 0;
    var ultimoPeso = parseFloat($("#ult_peso_calculo").val()) || 0;
    var diferencaPeso = peso - ultimoPeso;

    var t = $('#tabela_itens').DataTable();

    var checkboxHtml =
        "<input type='checkbox' class='check_item_motivo' value='" + $("#codigo_id").val() + "'>";

    var acoesHtml =
        "<div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div>" +
        "<div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div>";

    var novaLinha = t.row.add([
        checkboxHtml,                                  // 0 checkbox
        removerZeros($("#codigo_number_filtro").val()),// 1
        $("#peso_animal").val(),                       // 2
        diferencaPeso,                                 // 3
        $("#ult_peso_calculo").val(),                  // 4
        $("#data_ult_peso").text(),                    // 5
        $("#sexo_animal").val(),                       // 6
        $("#nascimento_animal").val(),                 // 7
        $("#apartacao_item").val(),                    // 8
        $("#observacao").val(),                        // 9
        $("#mae_animal").val(),                        // 10
        $("#desc_categoria").val(),                    // 11
        "",                                            // 12 bolinha/controle
        $("#idade_meses").val(),                       // 13
        $("#raca_animal").val(),                       // 14
        $("#pelagem_animal").val(),                    // 15
        $("#pai_animal").val(),                        // 16
        $("#observacao_cadastro").val(),               // 17
        $("#codigo_id").val(),                         // 18
        "",                                            // 19 mens_repetido
        "",                                            // 20 id_repetido
        acoesHtml                                      // 21 ações
    ]).draw(false).node();

    var $td = $(novaLinha).find("td");

    $td.eq(0).addClass("coluna_selecao_motivo");
    $td.eq(1).addClass("id_animal");
    $td.eq(2).addClass("peso_animal");
    $td.eq(3).addClass("ganho");
    $td.eq(4).addClass("ultimo_peso");
    $td.eq(5).addClass("data_ultimo_peso");
    $td.eq(6).addClass("sexo_animal");
    $td.eq(7).addClass("nascimento_animal");
    $td.eq(8).addClass("apartacao");
    $td.eq(9).addClass("obs_pesagem");
    $td.eq(10).addClass("mae_animal");
    $td.eq(11).addClass("categoria");
    $td.eq(13).addClass("idade_meses");
    $td.eq(14).addClass("raca_animal");
    $td.eq(15).addClass("pelagem_animal");
    $td.eq(16).addClass("pai_animal");
    $td.eq(17).addClass("observacao");
    $td.eq(18).addClass("codigo_id").hide();
    $td.eq(19).addClass("mens_repetido").hide();
    $td.eq(20).addClass("id_repetido").hide();

    if (!modoNovoMotivoAtivo) {
        $td.eq(0).hide();
    }

    $(novaLinha).find(".btnEditar").off("click").on("click", modal_editar_item);
    $(novaLinha).find(".btnExcluir").off("click").on("click", excluirItemEdicao);

    $("#itens").show();
    $("#codigo_number_filtro").val("");
    $("#codigo_id").val(0);
    $("#peso_animal").val("");
    $("#observacao").val("");
    $("#descricao_animal").text("");
    $("#ultimo_peso").text("");
    $("#ult_peso_calculo").val("");
    $("#data_ult_peso").text("");
    $("#desc_descarte").text("");

    if (typeof atualizarVisibilidadeSelecaoMotivo === "function") {
        atualizarVisibilidadeSelecaoMotivo();
    }

    if (typeof atualizarBotaoConfirmarNovoMotivo === "function") {
        atualizarBotaoConfirmarNovoMotivo();
    }

    somar_totais();
    gravar_pesagem(2);
    document.getElementById("codigo_number_filtro").focus();
}

function Salvar_estimada() {
    $(".alert_erro_estimado .negrito").html("");
    $(".alert_erro_estimado span").html("");
    $(".alert_erro_estimado").hide();

    if ($("#lote_pesagem_lote").val() == "") {
        $("#alert_erro_estimado .negrito").html("");
        $("#alert_erro_estimado span").html("Informe a Descrição do Lote!");
        $(".alert_erro_estimado").show();
        return;
    }

    var codigo_categoria = $("#codigo_categoria").val();

    if (codigo_categoria == "000") {
        $(".alert_erro_estimado .negrito").html("");
        $(".alert_erro_estimado span").html("Selecione uma Categoria/Sexo.");
        $(".alert_erro_estimado").show();
        return;
    }

    var select = $("#codigo_categoria").val();

    if (select != "000") {
        select = document.getElementById("codigo_categoria");
        desc_categoria = select.options[select.selectedIndex].text;
    }

    var qtd_estimada = $("#qtd_estimada").val();

    if (qtd_estimada == "" || qtd_estimada == 0) {
        $(".alert_erro_estimado .negrito").html("");
        $(".alert_erro_estimado span").html("Informe a quantidade.");
        $(".alert_erro_estimado").show();
        return;
    }

    var peso = $("#peso_estimado").val();

    if (peso == "") {
        $(".alert_erro_estimado .negrito").html("");
        $(".alert_erro_estimado span").html("Informe o Peso (Kg).");
        $(".alert_erro_estimado").show();
        return;
    }

    var qtd_pesar = $("#qtd_a_pesar_pesagem_lote").val();
    var qtd_pesado_lote = $("#qtd_pesado_pesagem_lote").val();
    var qtd_estimada = $("#qtd_estimada").val();
    var grupo_destino = $("#grupo_destino").val();

    if (qtd_pesado_lote == "") {
        qtd_pesado_lote = 0;
    }

    qtd_pesado = parseFloat(qtd_pesado_lote) + parseFloat(qtd_estimada);

    if (qtd_pesado > qtd_pesar && qtd_pesar != 0) {
        $("#alert_erro_estimado .negrito").html("");
        $("#alert_erro_estimado span").html(
            "Quantida de animais pesados está maior que a quantidade informada!"
        );
        document.getElementById("qtd_pesado_pesagem_lote").style.borderColor =
            "red";
        $(".alert_erro_estimado").show();
    }

    $("#qtd_pesado_pesagem_lote").val(qtd_pesado);

    $("#tabela_itens_pesagem_lote tbody").append(
        "<tr>" +
            "<td width='12%' class='desc_categoria'>" +
            desc_categoria +
            "</td>" +
            "<td width='8%' class='qtd_animal'>" +
            $("#qtd_estimada").val() +
            "</td>" +
            "<td width='8%' class='peso_animal'>" +
            $("#peso_estimado").val() +
            "</td>" +
            "<td width='8%' class='peso_medio'>" +
            $("#peso_medio_estimado").val() +
            "</td>" +
            "<td width='8%' class='peso_arroba'>" +
            $("#peso_estimado_arroba").val() +
            "</td>" +
            "<td width='8%' class='peso_medio_arroba'>" +
            $("#peso_medio_estimado_arroba").val() +
            "</td>" +
            "<td width='8%' hidden='' class='id_categoria'>" +
            $("#categoria_lote").val() +
            "</td>" +
            "<td width='8%' hidden='' class='sexo_animal'>" +
            $("#sexo_lote").val() +
            "</td>" +
            "<td width='8%' hidden='' class='qtd_lote'>" +
            $("#qtd_lote").val() +
            "</td>" +
            "<td width='8%' class='grupo_destino'>" +
            $("#grupo_destino").val() +
            "</td>" +
            "<td width='12%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>" +
            "</tr>"
    );

    $(".btnEditar").bind("click", modal_editar_item_pesagem_lote);
    $(".btnExcluir").bind("click", excluir_item_pesagem_lote);
    $("#itens_pesagem_lote").show();
    $("#codigo_categoria").val("00");
    $("#categoria_lote").val("");
    $("#qtd_estimada").val("");
    $("#qtd_lote").val("");
    $("#peso_estimado").val("");
    $("#sexo_lote").val("");
    $("#peso_medio_estimado").val("");
    $("#peso_estimado_arroba").val("");
    $("#peso_medio_estimado_arroba").val("");
    $("#grupo_destino").val("");

    somar_totais_lote();

    document.getElementById("codigo_categoria").focus();
}

function Salvar_editar() {
    if ($("#lote").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe a Descrição do Lote!");
        $(".alert_erro_animal").show();
        return;
    }

    if ($("#qtd_a_pesar").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe a Quantidade de Animais para Pesar!");
        $(".alert_erro_animal").show();
        return;
    }

    if ($("#codigo_number_filtro").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe a ID do animal!");
        $(".alert_erro_animal").show();
        return;
    }

    if ($("#peso_animal").val() == "") {
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("Informe o Peso do animal!");
        $(".alert_erro_animal").show();
        return;
    }

    $("#tabela_itens tbody tr").each(function () {
        row_index_salvar = $(this).index();

        var peso = $("#peso_animal").val();
        var ultimoPeso = $("#ult_peso_calculo").val();
        var diferencaPeso = peso - ultimoPeso;

        if (row_index_salvar == row_index) {
            $(this).find(".id_animal").html(removerZeros($("#codigo_number_filtro").val()));
            $(this).find(".peso_animal").html($("#peso_animal").val());
            $(this).find(".sexo_animal").html($("#sexo_animal").val());
            $(this)
                .find(".nascimento_animal")
                .html($("#nascimento_animal").val());
            $(this).find(".raca_animal").html($("#raca_animal").val());
            $(this).find(".pelagem_animal").html($("#pelagem_animal").val());
            $(this).find(".mae_animal").html($("#mae_animal").val());
            $(this).find(".obs_pesagem").html($("#observacao").val());
            $(this).find(".codigo_id").html($("#codigo_id").val());
            $(this).find(".apartacao").html($("#apartacao_item").val());
            $(this).find(".ganho").html(diferencaPeso);
            $(this).find(".ultimo_peso").html(ultimoPeso);
            $(this).find(".idade_meses").html($("#idade_meses").val());
            $(this).find(".categoria").html($("#desc_categoria").val());
            $(this).find(".pai_animal").html($("#pai_animal").val());
            $(this).find(".observacao").html($("#observacao_cadastro").val());
            $(this).find(".mens_repetido").html($("#mens_repetido").val());
            $(this).find(".id_repetido").html($("#id_repetido").val());
        }
    });

    // VER AQUI

    $(".btnEditar").bind("click", modal_editar_item);
    $(".btnExcluir").bind("click", excluir_item);

    $("#modal_pesar_individual").modal("hide");
    $("#editar").hide();
    $("#incluir").show();

    if ($.fn.DataTable.isDataTable('#tabela_itens')) {
        $('#tabela_itens').DataTable().destroy();
    }

    var array_tabela_itens = new Array();
    var valor = new Array();
    var grupo_itens = "";

    $("#tabela_itens tbody tr").each(function () {
        for (i = 0; i <= 2; i++) {
            valor[i] = 0;
        }

        var codigo = $(this).find(".id_animal").html();
        var peso = $(this).find(".peso_animal").html();
        var sexo = $(this).find(".sexo_animal").html();
        var nascimento = $(this).find(".nascimento_animal").html();
        var pelagem = $(this).find(".pelagem_animal").html();
        var raca = $(this).find(".raca_animal").html();
        var mae = $(this).find(".mae_animal").html();
        var observacao = $(this).find(".obs_pesagem").html();
        var codigo_id = $(this).find(".codigo_id").html();
        var apartacao = $(this).find(".apartacao").html();
        var mens_repetido = $(this).find(".mens_repetido").html();
        var id_repetido = $(this).find(".id_repetido").html();
        
        if (codigo != undefined && codigo != 0) {
            valor[0] = codigo;
            valor[1] = peso;
            valor[2] = sexo;
            valor[3] = nascimento;
            valor[4] = raca;
            valor[5] = pelagem;
            valor[6] = mae;
            valor[7] = observacao;
            valor[8] = codigo_id;
            valor[9] = apartacao;
            valor[10] = mens_repetido;
            valor[11] = id_repetido;

            var tabela_itens = valor.join("|");
            array_tabela_itens.push(tabela_itens);
            grupo_itens = array_tabela_itens.join("<|>");
        }
    });

    $("#array_itens").val(grupo_itens);

    // Reinicia com a config padrão
    $("#tabela_itens").DataTable(configDataTable);

    somar_totais();
    gravar_pesagem(2);
}

function Salvar_editar_estimado() {
    $(".alert_erro_estimado .negrito").html("");
    $(".alert_erro_estimado span").html("");
    $(".alert_erro_estimado").hide();

    if ($("#lote_pesagem_lote").val() == "") {
        $("#alert_erro_estimado .negrito").html("");
        $("#alert_erro_estimado span").html("Informe a Descrição do Lote!");
        $(".alert_erro_estimado").show();
        return;
    }

    var codigo_categoria = $("#codigo_categoria").val();

    if (codigo_categoria == "000") {
        $(".alert_erro_estimado .negrito").html("");
        $(".alert_erro_estimado span").html("Selecione uma Categoria/Sexo.");
        $(".alert_erro_estimado").show();
        return;
    }

    var select = $("#codigo_categoria").val();

    if (select != "000") {
        select = document.getElementById("codigo_categoria");
        desc_categoria = select.options[select.selectedIndex].text;
    }

    var qtd_estimada = $("#qtd_estimada").val();

    if (qtd_estimada == "" || qtd_estimada == 0) {
        $(".alert_erro_estimado .negrito").html("");
        $(".alert_erro_estimado span").html("Informe a quantidade.");
        $(".alert_erro_estimado").show();
        return;
    }

    var peso = $("#peso_estimado").val();

    if (peso == "") {
        $(".alert_erro_estimado .negrito").html("");
        $(".alert_erro_estimado span").html("Informe o Peso (Kg).");
        $(".alert_erro_estimado").show();
        return;
    }

    $("#tabela_itens_pesagem_lote tbody tr").each(function () {
        row_index_salvar = $(this).index();

        if (row_index_salvar == row_index) {
            $(this).find(".desc_categoria").html(desc_categoria);
            $(this).find(".qtd_animal").html($("#qtd_estimada").val());
            $(this).find(".peso_animal").html($("#peso_estimado").val());
            $(this).find(".peso_medio").html($("#peso_medio_estimado").val());
            $(this).find(".peso_arroba").html($("#peso_estimado_arroba").val());
            $(this)
                .find(".peso_medio_arroba")
                .html($("#peso_medio_estimado_arroba").val());
            $(this).find(".id_categoria").html($("#categoria_lote").val());
            $(this).find(".sexo_animal").html($("#sexo_lote").val());
            $(this).find(".qtd_lote").html($("#qtd_lote").val());
            $(this).find(".grupo_destino").html($("#grupo_destino").val());
        }
    });

    var qtd_pesar = $("#qtd_a_pesar_pesagem_lote").val();
    var qtd_pesado = 0;
    $("#tabela_itens_pesagem_lote tbody tr").each(function () {
        var categoria = $(this).find(".id_categoria").html();

        if (categoria != undefined) {
            var qtd_animal = parseFloat($(this).find(".qtd_animal").html());
            qtd_pesado += qtd_animal;
        }
    });

    if (qtd_pesado > qtd_pesar && qtd_pesar != 0) {
        $("#alert_erro_estimado .negrito").html("");
        $("#alert_erro_estimado span").html(
            "Quantida de animais pesados está maior que a quantidade informada!"
        );
        document.getElementById("qtd_pesado_pesagem_lote").style.borderColor =
            "red";
        $(".alert_erro_estimado").show();
    }

    $("#qtd_pesado_pesagem_lote").val(qtd_pesado);

    $(".btnEditar").bind("click", modal_editar_item_pesagem_lote);
    $(".btnExcluir").bind("click", excluir_item_pesagem_lote);

    $("#modal_pesar_estimada").modal("hide");
    $("#editar_lote").hide();
    $("#incluir_lote").show();

    somar_totais_lote();
}

function modal_editar_item() {
    row_index = $(this).parent().parent().index();

    var par = $(this).closest("tr"); // Busca o "pai" TR mais próximo, mais seguro que .parent().parent()

    var tdCodigo      = par.find(".id_animal");
    var tdPeso        = par.find(".peso_animal");
    var tdSexo        = par.find(".sexo_animal");
    var tdNascimento  = par.find(".nascimento_animal");
    var tdApartacao   = par.find(".apartacao");
    var tdObsPesagem  = par.find(".obs_pesagem");
    var tdRaca        = par.find(".raca_animal");
    var tdPelagem     = par.find(".pelagem_animal");
    var tdMae         = par.find(".mae_animal");
    var tdPai         = par.find(".pai_animal");
    var tdObservacao  = par.find(".observacao");
    var tdCodigo_id   = par.find(".codigo_id");
    var tdUltimoPeso  = par.find(".ultimo_peso");
    var tdIdadeMeses  = par.find(".idade_meses");
    var tdDescCategoria  = par.find(".categoria");
    var tdMensRepetido  = par.find(".mens_repetido");
    var tdIdRepetido  = par.find(".id_repetido");


    $("#codigo_number_filtro").val(tdCodigo.html());
    $("#peso_animal").val(tdPeso.html());
    $("#sexo_animal").val(tdSexo.html());
    $("#nascimento_animal").val(tdNascimento.html());
    $("#raca_animal").val(tdRaca.html());
    $("#pelagem_animal").val(tdPelagem.html());
    $("#mae_animal").val(tdMae.html());
    $("#apartacao_item").val(tdApartacao.html());
    $("#observacao").val(tdObsPesagem.html());
    $("#codigo_id").val(tdCodigo_id.html());
    $("#ult_peso_calculo").val(tdUltimoPeso.html());
    $("#pai_animal").val(tdPai.html());
    $("#desc_categoria").val(tdDescCategoria.html());
    $("#idade_meses").val(tdIdadeMeses.html());
    $("#observacao_cadastro").val(tdObservacao.html());
    $("#mens_repetido").val(tdMensRepetido.html());
    $("#id_repetido").val(tdIdRepetido.html());

    $("#modal_pesar_individual .modal-title").html(
        "Pesagem - Individual - Editar"
    );

    $("#lote").val($(".descricao_lote").val());

    $("#modal_pesar_individual").modal("show");
    $("#alert_erro_animal .negrito").html("");
    $("#alert_erro_animal span").html("");
    $(".alert_erro_animal").hide();

    $("#editar").show();
    $("#incluir").hide();
}

function modal_editar_item_pesagem_lote() {
    row_index = $(this).parent().parent().index();

    var par = $(this).parent().parent(); //tr
    var tdQtdAnimal = par.children("td:nth-child(2)");
    var tdPeso = par.children("td:nth-child(3)");
    var tdPesoMedio = par.children("td:nth-child(4)");
    var tdPesoArroba = par.children("td:nth-child(5)");
    var tdPesoMedioArroba = par.children("td:nth-child(6)");
    var tdCategoria = par.children("td:nth-child(7)");
    var tdSexo = par.children("td:nth-child(8)");
    var tdQtdLote = par.children("td:nth-child(9)");
    var tdGrupoDestino = par.children("td:nth-child(10)");

    var codigo_categoria =
        tdSexo.html() + tdCategoria.html() + tdQtdLote.html();

    $("#codigo_categoria").val(codigo_categoria);
    $("#qtd_estimada").val(tdQtdAnimal.html());
    $("#peso_estimado").val(tdPeso.html());
    $("#peso_medio_estimado").val(tdPesoMedio.html());
    $("#peso_estimado_arroba").val(tdPesoArroba.html());
    $("#peso_medio_estimado_arroba").val(tdPesoMedioArroba.html());
    $("#sexo_lote").val(tdSexo.html());
    $("#categoria_lote").val(tdCategoria.html());
    $("#qtd_lote").val(tdQtdLote.html());
    $("#qtd_digitado_anterior").val(tdQtdAnimal.html());
    $("#grupo_destino").val(tdGrupoDestino.html());

    $("#modal_pesar_estimada .modal-title").html(
        "Pesagem - Individual - Editar"
    );
    $("#modal_pesar_estimada").modal("show");
    $("#alert_erro_estimado .negrito").html("");
    $("#alert_erro_estimado span").html("");
    $(".alert_erro_estimado").hide();

    $("#editar_lote").show();
    $("#incluir_lote").hide();
}

function excluir_item() {
    var par = $(this).closest('tr'); // Pega a linha (tr) de forma mais segura
    var t = $('#tabela_itens').DataTable(); // Pega a instância do DataTable
    
    var tdCodigo = par.find(".id_animal"); // Usa a classe que já mapeamos

    if (window.confirm("Confirma enviar esse registro para lixeira? " + tdCodigo.html())) {
        
        // CORREÇÃO AQUI: Remove da memória do DataTable e redesenha
        t.row(par).remove().draw(false);

        // Atualiza as quantidades
        var qtd_pesado = parseInt($("#qtd_pesado").val()) || 0;
        qtd_pesado--;
        $("#qtd_pesado").val(qtd_pesado);

        somar_totais();
        gravar_pesagem(1); 
        return;
    }
}

// Esta função é para a exclusao de item quando editar as pesagens não finalizadas ou incluir animal nessa pesagem
function excluirItemEdicao() {
    var par = $(this).closest('tr'); // Pega a linha (tr) de forma mais segura
    var t = $('#tabela_itens').DataTable(); // Pega a instância do DataTable
    var tdCodigo = par.find(".id_animal"); // Usa a classe que já mapeamos

    if (window.confirm("Confirma enviar esse registro para lixeira? " + tdCodigo.html())) {

        par.tooltip('hide');
        par.tooltip('destroy');

        // Garante limpar qualquer tooltip solto no body
        $('body > .tooltip').remove();
        $('.tooltip').remove();

        // CORREÇÃO AQUI: Remove da memória do DataTable e redesenha
        t.row(par).remove().draw(false);

        // Atualiza as quantidades
        var qtd_pesado = parseInt($("#qtd_pesado").val()) || 0;
        qtd_pesado--;
        $("#qtd_pesado").val(qtd_pesado);

        somar_totais();
        gravar_pesagem(2); 
        return;
    }
}

function excluir_item_pesagem_lote() {
    var par = $(this).parent().parent(); //tr
    var tdCategoria = par.children("td:nth-child(1)");

    if (
        window.confirm(
            "Confirma enviar esse registro para lixeira? "  +
                tdCategoria.html()
        )
    ) {
        par.remove();

        var qtd_pesado = 0;
        $("#tabela_itens_pesagem_lote tbody tr").each(function () {
            var categoria = $(this).find(".id_categoria").html();

            if (categoria != undefined) {
                var qtd_animal = parseFloat($(this).find(".qtd_animal").html());
                qtd_pesado += qtd_animal;
            }
        });

        $("#qtd_pesado_pesagem_lote").val(qtd_pesado);

        somar_totais_lote();
    }
}

function iniciar_pesagem() {
    var controle_estoque = $("#controle_estoque").val();
    var local_pesagem = $("#local_pesagem").val();
    var epoca_pesagem = $("#epoca_pesagem").val();
    var data_pesagem = $("#data_pesagem").val();

    if (data_pesagem == '') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe a Data");
        return;
    }

    if (local_pesagem == 0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe a Fazenda");
        return;
    }

    if (epoca_pesagem == 0) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe o Motivo da Pesagem");
        return;
    }

    if (controle_estoque == "I") {
        if ($("#positivo").is(":checked") == true || $("#negativo").is(":checked") == true) {
            var estacao = $("#codigo_estacao_filtro").val();

            if (estacao=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Para Filtros Positivas ou Negativas, selecione a estação de monta!');
                return;
            }
        }

        var raca = $("#codigo_raca_filtro").val();
        var data_nasc_inicial = $("#data_nasc_inicial_filtro").val();
        var data_nasc_final = $("#data_nasc_final_filtro").val();

        if (raca == null) {
            raca = [""];
        }

        $(".descricao_filtro_modal").html($(".descricao_filtro").val());

        $("#codigo_number_filtro").val("");
        $("#peso_animal").val("");
        $("#observacao").val("");
        $("#descricao_animal").text("");
        $("#ultimo_peso").text("");
        $("#ult_peso_calculo").val("");
        $("#data_ult_peso").text("");
        $("#desc_descarte").text("");
        $("#modal_pesar_individual").modal("show");
        $("#alert_erro_animal .negrito").html("");
        $("#alert_erro_animal span").html("");
        $(".alert_erro_animal").hide();
    } 
    else {
        var local = $("#local_pesagem").val();
        var pasto = $("#pasto").val();
        var categoria = $("#categoria_filtro").val();
        if (pasto == null) {
            pasto = [""];
        }

        if (categoria == null) {
            categoria = [""];
        }

        if (
            $("#macho").is(":checked") == true &&
            $("#femea").is(":checked") == true
        ) {
            sexo = "T";
        } else if ($("#macho").is(":checked") == true) {
            sexo = "M";
        } else if ($("#femea").is(":checked") == true) {
            sexo = "F";
        }

        $.post(
            "lista_categoria_pasto.php",
            {
                local: local,
                pasto: pasto,
                categoria: categoria,
                sexo: sexo,
            },
            function (valor) {
                if (valor == "N") {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(
                        "Não existem animais no(s) pasto(s) selecionado(s)."
                    );
                    return;
                } 
                else {
                    $("select[name=codigo_categoria]").html(valor);

                    $("#data_pesagem_lote").val($("#data_pesagem").val());
                    $("#local_pesagem_lote").val($("#local_pesagem").val());
                    $("#epoca_pesagem_lote").val($("#epoca_pesagem").val());
                    $("#filtros_lote").val($("#descricao_filtro").val());
                    $("#alert_erro_estimado .negrito").html("");
                    $("#alert_erro_estimado span").html("");
                    $(".alert_erro_estimado").hide();

                    var select = $("#epoca_pesagem").val();

                    if (select != 0) {
                        select = document.getElementById("epoca_pesagem");
                        epoca = select.options[select.selectedIndex].text;

                        if (epoca == "Venda") {
                            $(".mensagem_venda").show();
                        } else {
                            $(".mensagem_venda").hide();
                        }
                    }

                    $("#modal_pesar_estimada").modal("show");
                }
            }
        );
    }
}

function continuar_pesagem() {
    var data_pesagem = $("#data_pesagem").val();

    if (data_pesagem=='') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe a Data da pesagem.");
        return;
    }

    $("#alert_erro_animal .negrito").html("");
    $("#alert_erro_animal span").html("");
    $(".alert_erro_animal").hide();

    $("#codigo_id").val("");
    $("#codigo_number_filtro").val("");
    $("#peso_animal").val("");
    $("#observacao").val("");
    $("#descricao_animal").text("");
    $("#ultimo_peso").text("");
    $("#ult_peso_calculo").val("");
    $("#data_ult_peso").text("");
    $("#desc_descarte").text("");

    $("#lote").val($(".descricao_lote").val());
    $("#qtd_a_pesar").val($("#total_a_pesar").val());

    $("#modal_pesar_individual .modal-title").html(
        "Pesagem - Individual - Incluir"
    );
    $("#modal_pesar_individual").modal("show");
    document.getElementById("codigo_number_filtro").focus();
}

function continuar_pesagem_lote() {
    $("#codigo_categoria").val("000");
    $("#qtd_estimada").val("");
    $("#peso_estimado").val("");
    $("#peso_medio_estimado").val("");
    $("#peso_estimado_arroba").val("");
    $("#peso_medio_estimado_arroba").val("");
    $("#sexo_lote").val("");
    $("#categoria_lote").val("");
    $("#qtd_lote").val("");
    $("#qtd_digitado_anterior").val("");

    $("#modal_pesar_estimada .modal-title").html("Pesagem - Incluir");
    $("#modal_pesar_estimada").modal("show");
    $("#editar_lote").hide();
    $("#incluir_lote").show();

    document.getElementById("codigo_categoria").focus();
}

function pausar_pesagem() {
    $("#alert_erro_animal .negrito").html("");
    $("#alert_erro_animal span").html("");
    $(".alert_erro_animal").hide();

    tem_itens = "N";

    $("#tabela_itens tbody tr").each(function () {
        var id_lista = $(this).find(".id_animal").html();

        if (id_lista != "") {
            tem_itens = "S";
        }
    });

    if (tem_itens == "N") {
        $("#individual").prop("checked", false);
        $("#estimada").prop("checked", false);
        $("#modal_pesar_individual").modal("hide");
        return;
    }

    $("#modal_pesar_individual").modal("hide");
    $(".selecionar_dados_pesagem").hide();
}

function pausar_pesagem_estimada() {
    $(".alert_erro_estimado .negrito").html("");
    $(".alert_erro_estimado span").html("");
    $(".alert_erro_estimado").hide();

    tem_itens = "N";

    $("#itens_pesagem_lote tbody tr").each(function () {
        var categoria = $(this).find(".id_categoria").html();

        if (categoria != "") {
            tem_itens = "S";
        }
    });

    if (tem_itens == "N") {
        $("#modal_pesar_estimada").modal("hide");
        return;
    }

    $("#modal_pesar_estimada").modal("hide");
    $(".selecionar_dados_pesagem").hide();
}

// Confirma finalizar pesagem on-line Inclusão e Edição
function terminar_pesagem() {
    limparBusca();
    fecharTodasLinhas();

    var data_pesagem = $("#data_pesagem").val();

    if (data_pesagem=='') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe a Data da pesagem.");
        return;
    }

    var tem_item = "";

    $("#tabela_itens tbody tr").each(function () {
        var codigo = $(this).find(".id_animal").html();
        if (codigo != undefined) {
            tem_item = "S";
        }
    });

    var a_pesar = parseFloat($("#qtd_a_pesar").val());
    var pesados = parseFloat($("#qtd_pesado").val());

    if (tem_item == '') {
        $("#modal_finalizar").modal();
        $("#modal_finalizar .modal-body").html("Atenção! Não existem animais pesados. Confirma assim mesmo?");
    }
    else if (a_pesar > pesados) {
        $("#modal_finalizar").modal();
        $("#modal_finalizar .modal-body").html("Animais pesados menor que animais a pesar. Finalizar assim mesmo?");
    }
    else {
        $("#modal_finalizar").modal();
        $("#modal_finalizar .modal-body").html("Confirma Finalizar a Pesagem?");
    }
}

// chamado do botão Finalizar Pesagem Lote on-line Inclusão e Edição
function terminar_pesagem_lote() {
    var tem_item = "";

    $("#tabela_itens_pesagem_lote tbody tr").each(function () {
        var categoria = $(this).find(".id_categoria").html();
        if (categoria != undefined) {
            tem_item = "S";
        }
    });

    var a_pesar = parseFloat($("#qtd_a_pesar_pesagem_lote").val());
    var pesados = parseFloat($("#qtd_pesado_pesagem_lote").val());

    if (tem_item == "") {
        if (
            window.confirm(
                "Atenção! Não existem animais pesados. Finalizar assim mesmo?"
            )
        ) {
            $("#mensagem_sair_retorno").modal();
            $("#mensagem_sair_retorno .modal-body").html(
                "Pesagem finalizada sem animais pesados."
            );
        }
    } else if (a_pesar > pesados) {
        if (
            window.confirm(
                "Animais pesados menor que animais a pesar. Finalizar assim mesmo?"
            )
        ) {
            //$("#mensagem_sair_retorno").modal();
            //$("#mensagem_sair_retorno .modal-body").html("Pesagem finalizada com sucesso.");
            gravar_pesagem_lote();
        }
    } else {
        if (window.confirm("Confirma finalizar a pesagem?")) {
            //$("#mensagem_sair_retorno").modal();
            //$("#mensagem_sair_retorno .modal-body").html("Pesagem finalizada com sucesso.");
            gravar_pesagem_lote();
        }
    }
}

// funcao chamada pelo botão Finalizar Pesagem da digitação editar lista gerada pelo excel
function finalizar_pesagem_editar() {
    var lote = $("#lote").val();
    var data_pesagem = $("#data_pesagem").val();

    if (lote=='') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe o Lote da pesagem.");
        return;
    }

    if (data_pesagem=='') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe a Data da pesagem.");
        return;
    }

    var tem_item = "S";
    var animais_sem_peso = 0;

    $("#tabela_itens_editar tbody tr").each(function () {
        var peso = $(this).find(".peso").find("input").val();

        if (peso == undefined || peso == "" || peso == 0) {
            animais_sem_peso++;
            tem_item = "N";
        }
    });

    var a_pesar = $("#qtd_a_pesar").val();
    var pesados = $("#qtd_pesado").val();

    if (tem_item == "N") {
        $("#modal_finalizar").modal();
        $("#modal_finalizar .modal-body").html(
            "Atenção! Existe(m) " +
                animais_sem_peso +
                " animais sem o peso digitado. Ao confirmar a finalizãção, esses animais serão excluidos dessa lista. Finalizar assim mesmo?"
        );
    } else {
        $("#modal_finalizar").modal();
        $("#modal_finalizar .modal-body").html("Confirma finalizar a pesagem?");
    }
}

function imprimir_pesagem_excel(num_pesagem, filtro_pesagem, filtro_local, filtro_epoca) {
    location.href='rel_exportar_pesagem_excel_imprimir.php?desc_filtro=' + filtro_pesagem + 
    '&num_pesagem=' + num_pesagem + '&desc_local=' + filtro_local + '&desc_epoca=' + filtro_epoca;
    tout = setTimeout("finalizar_sair()", 3000);
}

function imprimir_pesagem_excel_lote(num_pesagem, filtro_pesagem, filtro_local, filtro_epoca) {
    location.href='rel_exportar_pesagem_excel_lote_imprimir.php?desc_filtro=' + filtro_pesagem + 
    '&num_pesagem=' + num_pesagem + '&desc_local=' + filtro_local + '&desc_epoca=' + filtro_epoca;
    tout = setTimeout("finalizar_sair()", 3000);
}

function gerar_excel_pesagem_sem_finalizar() {
    const rows = document.querySelectorAll("tbody tr"); // Pega apenas as linhas do corpo da tabela
    let fazendas = [];

    rows.forEach(row => {
        let codFazenda = row.querySelector(".codigo_local")?.innerText.trim();
        let nomeFazenda = row.querySelector(".desc_local")?.innerText.trim();
        
        if (codFazenda) {
            if (!fazendas.find(f => f.id === codFazenda)) {
                fazendas.push({ id: codFazenda, nome: nomeFazenda });
            }
        }
    });

    if (fazendas.length === 0) return alert("Nenhum registro encontrado.");

    if (fazendas.length === 1) {
        // Se só tem uma fazenda, envia todos os IDs de pesagem da tabela
        enviarDadosExcel(fazendas[0].id, fazendas[0].nome);
    } else {
        exibirSelecaoFazenda(fazendas);
    }
}

// Função auxiliar para filtrar os IDs de pesagem da fazenda selecionada e abrir o Excel
function enviarDadosExcel(idFazenda, nomeFazenda) {
    const rows = document.querySelectorAll("tbody tr");
    let idsPesagem = [];

    rows.forEach(row => {
        let codFazendaLinha = row.querySelector(".codigo_local")?.innerText.trim();
        let idPesagemLinha = row.querySelector(".pesagem_id")?.innerText.trim();

        // Só adiciona o ID da pesagem se a linha pertencer à fazenda selecionada
        if (codFazendaLinha === idFazenda && idPesagemLinha) {
            idsPesagem.push(idPesagemLinha);
        }
    });

    if (idsPesagem.length > 0) {
        // Remove duplicados caso o mesmo ID de pesagem apareça em mais de uma linha
        let listaUnica = [...new Set(idsPesagem)];
        let listaFinal = listaUnica.join(",");
        
        window.open(`rel_lista_pesagem_sem_finalizar_excel.php?codigo_local=${idFazenda}&nome_fazenda=${encodeURIComponent(nomeFazenda)}&lista_final=${listaFinal}`);
    } else {
        alert("Não foram encontrados IDs de pesagem para esta fazenda.");
    }
}

function exibirSelecaoFazenda(fazendas) {
let overlay = document.createElement('div');
    // Adicionamos 'box-sizing' e garantimos que o overlay ocupe o viewport exato
    overlay.style = "position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.6); z-index:10000; display:flex; align-items:center; justify-content:center; box-sizing:border-box;";
    
    let box = document.createElement('div');
    // Adicionamos um min-width e garantimos que a margem interna não quebre o layout
    box.style = "background:#fff; padding:25px; border-radius:8px; width:100%; max-width:400px; text-align:center; box-shadow: 0 10px 25px rgba(0,0,0,0.5); font-family: 'Open Sans', sans-serif; box-sizing:border-box;";    

    // Note que adicionei o ID no select e o botão 'Gerar Excel' começa com 'disabled' e uma cor mais clara
    let html = `<h4 style="margin-top:0; color:#333;">Selecione uma Fazenda</h4>
                <select class="form-control" id="sel_fazenda_excel_dinamico" style="width:100%; height:40px; margin-bottom:20px;">
                    <option value="000000000" selected disabled>...</option>`;    
    
    fazendas.forEach(f => {
        html += `<option value="${f.id.trim()}">${f.nome.trim()}</option>`;
    });
    
    html += `</select>
             <div style="display:flex; justify-content: space-between;">
                <button type="button" class="btn btn-success" id="btn_confirm_excel" disabled>Gerar Excel</button>
                <button type="button" class="btn btn-default" id="btn_cancel_excel">Cancelar</button>
             </div>`;
    
    box.innerHTML = html;
    overlay.appendChild(box);
    document.body.appendChild(overlay);

    const selectFazenda = document.getElementById('sel_fazenda_excel_dinamico');
    const btnConfirmar = document.getElementById('btn_confirm_excel');

    // Monitora a mudança no select
    selectFazenda.onchange = function() {
        if (this.value !== "000000000") {
            // Habilita o botão e muda o estilo para parecer ativo
            btnConfirmar.disabled = false;
        }
    };

    btnConfirmar.onclick = function() {
        let codigo = selectFazenda.value;
        let nome = selectFazenda.options[selectFazenda.selectedIndex].text;
        enviarDadosExcel(codigo, nome); // Chama a função que filtra os IDs
        document.body.removeChild(overlay);
    };

    document.getElementById('btn_cancel_excel').onclick = function() {
        document.body.removeChild(overlay);
    };
}

function imprimir_grupo_pesagem() {

    var id_pesagem = $("#id_pesagem").val();
    
    $("#aguardar").modal("show");

    var width = 350;
    var height = 500;
    var left = 40;
    var top = 40;
    
    window.open("rel_lista_grupo_pesagem_pdf.php?id_pesagem=" + id_pesagem,
                "janela",
                "width=" + width +
                ", height=" + height +
                ", top=" + top +
                ", left=" + left +
                ", scrollbars=yes, status=yes, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=yes"
    );

    tout = setTimeout("limpar_tela()", 1000);
}

function limpar_tela() {
    $("#aguardar").modal("hide");
}

function finalizar_sair() {
    $("#aguardar").modal("hide");
    location.href = "form_pesagem_animais.php";
}

function sair_sem_gravar() {
    if (window.confirm("Confirma sair sem gravar a pesagem?")) {
        location.href = "form_pesagem_animais.php";
    }
}

function fecha_consultar_pesagem() {
    var tipo_pesagem = $("#tipo_pesagem").val();

    if (tipo_pesagem=='ONLINE') {
        if (window.confirm("Confirma sair sem Finalizar?")) {
            limparBusca();
            fecharTodasLinhas();
            gravar_pesagem(1);
            location.href = "form_pesagem_animais.php";
        }
    }
    else {
        if (window.confirm("Confirma sair sem Finalizar?")) {
            gravar_pesagem_sem_finalizar_offline();
            location.href = "form_pesagem_animais.php";
        }
    }
}

function limparBusca() {
    var table = $('#tabela_itens').DataTable();
    
    // Limpa o campo de busca interno do DataTable
    table.search('').draw();
    
    // Opcional: Se você quiser garantir que o campo visual (input) também fique vazio
    //$('.dataTables_filter input').val('');
}
function mais_relatorios() {
    var controle_estoque = $("#controle_estoque").val();

    if (controle_estoque == "I") {
        location.href= 'form_rel_historico_animais.php?tipo=2';
    }
    else {
        location.href= 'form_rel_historico_pesagem_lote.php?tipo=2';
    }
}

// Fecha todas as linhas da tabela de itens que estão aberta (icone (-) vermelhor aparecendo)
function fecharTodasLinhas() {
    // Recupera a instância do DataTable
    var table = $('#tabela_itens').DataTable();

    // Percorre todas as linhas e fecha os detalhes (child rows)
    table.rows().every(function () {
        if (this.child.isShown()) {
            this.child.hide();
            $(this.node()).removeClass('parent'); // Remove a classe que deixa a bolinha vermelha
        }
    });
}
function somar_totais() {
    total_peso = 0;
    tem_itens = "N";

    $("#tabela_itens tbody tr").each(function () {
        var id_lista = $(this).find(".id_animal").html();

        if (id_lista != undefined) {
            var peso = parseFloat($(this).find(".peso_animal").html());
            total_peso += peso;
            tem_itens = "S";
        }
    });

    if (tem_itens == "S") {
        var qtd_pesados = parseFloat($("#qtd_pesado").val());
        var qtd_a_pesar = parseFloat($("#qtd_a_pesar").val());
        var peso_total_arroba = total_peso / 30;
        var peso_medio_kg = total_peso / qtd_pesados;
        var peso_medio_arroba = peso_total_arroba / qtd_pesados;
        $(".total_a_pesar").text(qtd_a_pesar);
        $(".total_a_pesar").val(qtd_a_pesar);
        $(".total_pesados").text(qtd_pesados);
        $(".total_pesados").val(qtd_pesados);
        $(".peso_total_kg").text(formatMoney(total_peso));
        $(".peso_total_kg").val(total_peso.toFixed(2));
        $(".peso_total_arroba").text(formatMoney(peso_total_arroba));
        $(".peso_total_arroba").val(peso_total_arroba.toFixed(2));
        $(".peso_medio_kg").text(formatMoney(peso_medio_kg));
        $(".peso_medio_kg").val(peso_medio_kg.toFixed(2));
        $(".peso_medio_arroba").text(formatMoney(peso_medio_arroba));
        $(".peso_medio_arroba").val(peso_medio_arroba.toFixed(2));
        if (qtd_pesados>7) {
            $(".botoes_final").show();
        }
        else {
            $(".botoes_final").hide();
        }
    } else {
        var qtd_pesados = "";
        var peso_total_arroba = "";
        var peso_medio_kg = "";
        var peso_medio_arroba = "";
        $(".total_pesados").text("");
        $(".peso_total_kg").text("");
        $(".peso_total_arroba").text("");
        $(".peso_medio_kg").text("");
        $(".peso_medio_arroba").text("");
        $(".total_pesados").val("");
        $(".peso_total_kg").val("");
        $(".peso_total_arroba").val("");
        $(".peso_medio_kg").val("");
        $(".peso_medio_arroba").val("");
        $(".botoes_final").hide();
    }

    var data = $("#data_pesagem").val();

    var dia = data.split("-")[2];
    var mes = data.split("-")[1];
    var ano = data.split("-")[0];

    var str_data =
        ("0" + dia).slice(-2) + "/" + ("0" + mes).slice(-2) + "/" + ano;

    var descricao_lote = $("#lote").val();

    if (descricao_lote!='') {
        $(".descricao_lote").text(descricao_lote);
        $(".descricao_lote").val(descricao_lote);
    }

    $(".data_pesagem").val(data);
}

function soma_total_item_lote() {
    $("#alert_erro_estimado .negrito").html("");
    $("#alert_erro_estimado span").html("");
    $(".alert_erro_estimado").hide();

    var total_peso = 0;
    var tem_itens = "N";
    var qtd_no_pasto = $("#qtd_lote").val();
    var qtd_estimada = parseInt($("#qtd_estimada").val());
    var qtd_digitada_anterior = $("#qtd_digitado_anterior").val();

    if (qtd_digitada_anterior == "") {
        qtd_digitada_anterior = 0;
    }

    var categoria_digitada = $("#categoria_lote").val();
    var sexo_digitado = $("#sexo_lote").val();

    $("#itens_pesagem_lote tbody tr").each(function () {
        var categoria = $(this).find(".id_categoria").html();
        var sexo = $(this).find(".sexo_animal").html();
        var qtd_ja_digitada = $(this).find(".qtd_animal").html();

        if (categoria != "") {
            if (categoria == categoria_digitada && sexo == sexo_digitado) {
                qtd_estimada -= parseInt(qtd_digitada_anterior);
                qtd_estimada += parseInt(qtd_ja_digitada);
            }
        }
    });

    if (qtd_estimada > qtd_no_pasto) {
        $("#alert_erro_estimado span").html(
            "Quantidade digitada esta maior que a quantidade de animais no pasto. Quantide no pasto: " +
                qtd_no_pasto
        );
        $(".alert_erro_estimado").show();
        $("#qtd_estimada").val("");
        $("#peso_medio_estimado").val("");
        $("#peso_estimado_arroba").val("");
        $("#peso_medio_estimado_arroba").val("");
        return;
    }

    var qtd_estimada = $("#qtd_estimada").val();
    var peso_estimado = $("#peso_estimado").val();
    var peso_medio_estimado = peso_estimado / qtd_estimada;
    $("#peso_medio_estimado").val(peso_medio_estimado.toFixed(2));

    var peso_total_arroba = peso_estimado / 30;
    var peso_medio_arroba = peso_total_arroba / qtd_estimada;
    $("#peso_estimado_arroba").val(peso_total_arroba.toFixed(2));
    $("#peso_medio_estimado_arroba").val(peso_medio_arroba.toFixed(2));
}

function somar_totais_lote() {
    var total_pesados = 0;
    var total_medio = 0;
    var total_arroba = 0;
    var total_medio_arroba = 0;
    var total_peso = 0;
    var tem_itens = "N";
    var linhas_pesados = 0;

    $("#itens_pesagem_lote tbody tr").each(function () {
        var categoria = $(this).find(".id_categoria").html();

        if (categoria != undefined && categoria != "") {
            var peso = parseFloat($(this).find(".peso_animal").html());
            var qtd_pesados = parseFloat($(this).find(".qtd_animal").html());
            var peso_medio = parseFloat($(this).find(".peso_medio").html());
            var peso_arroba = parseFloat($(this).find(".peso_arroba").html());
            var peso_medio_arroba = parseFloat(
                $(this).find(".peso_medio_arroba").html()
            );

            total_pesados += qtd_pesados;
            total_peso += peso;
            total_medio += peso_medio;
            total_medio_arroba += peso_medio_arroba;
            tem_itens = "S";
            linhas_pesados++;
        }
    });

    if (tem_itens == "S") {
        var qtd_pesados = parseFloat($("#qtd_pesado_pesagem_lote").val());
        var qtd_a_pesar = parseFloat($("#qtd_a_pesar_pesagem_lote").val());
        $(".total_a_pesar_pesagem_lote").text(
            "Animais para Pesar: " + qtd_a_pesar
        );
        $(".total_pesados_pesagem_lote").text(
            "Animais Pesados: " + qtd_pesados
        );
        $(".total_pesados_pesagem_lote").val(qtd_pesados);
        var total_arroba = total_peso / 30;
        var total_medio = total_peso / total_pesados;
        var total_medio_arroba = total_arroba / total_pesados;
        $(".peso_total_kg_pesagem_lote").text(
            "Peso Total Kg: " + formatMoney(total_peso)
        );
        $(".peso_total_kg_pesagem_lote").val(total_peso.toFixed(2));
        $(".peso_total_arroba_pesagem_lote").text(
            "Peso Total @: " + formatMoney(total_arroba)
        );
        $(".peso_total_arroba_pesagem_lote").val(total_arroba.toFixed(2));
        $(".peso_medio_kg_pesagem_lote").text(
            "Peso Médio Kg: " + formatMoney(total_medio)
        );
        $(".peso_medio_kg_pesagem_lote").val(total_medio.toFixed(2));
        $(".peso_medio_arroba_pesagem_lote").text(
            "Peso Médio @: " + formatMoney(total_medio_arroba)
        );
        $(".peso_medio_arroba_pesagem_lote").val(total_medio_arroba.toFixed(2));

        if (linhas_pesados>4) {
            $(".botoes_final").show();
        }
        else {
            $(".botoes_final").hide();
        }

    } else {
        var qtd_pesados = "";
        var peso_total_arroba = "";
        var peso_medio_kg = "";
        var peso_medio_arroba = "";
        $(".total_pesados_pesagem_lote").text("");
        $(".peso_total_kg_pesagem_lote").text("");
        $(".peso_total_arroba_pesagem_lote").text("");
        $(".peso_medio_kg_pesagem_lote").text("");
        $(".peso_medio_arroba_pesagem_lote").text("");
        $(".total_pesados_pesagem_lote").val("");
        $(".peso_total_kg_pesagem_lote").val("");
        $(".peso_total_arroba_pesagem_lote").val("");
        $(".peso_medio_kg_pesagem_lote").val("");
        $(".peso_medio_arroba_pesagem_lote").val("");
        $(".botoes_final").hide();
    }

    var data = $("#data_pesagem").val();

    var dia = data.split("-")[2];
    var mes = data.split("-")[1];
    var ano = data.split("-")[0];

    var str_data =
        ("0" + dia).slice(-2) + "/" + ("0" + mes).slice(-2) + "/" + ano;

    /*$("#descricao_lote_pesagem_lote").text(
        "Lote: " + $("#lote_pesagem_lote").val()
    );
    $(".descricao_lote_pesagem_lote").val($("#lote_pesagem_lote").val());
    $("#data_pesados_pesagem_lote").text("Data: " + str_data);*/

    var descricao_lote = $("#lote_pesagem_lote").val();

    if (descricao_lote!='') {
        $(".descricao_lote_lote").text(descricao_lote);
        $(".descricao_lote_lote").val(descricao_lote);
    }

    $(".data_pesagem").val(data);
}

function editar_animal(array_animal) {
    array_produtos = array_animal.split("|");
    $("#codigo_animal").val(array_produtos[0]);
    $("#number_animal").val(array_produtos[1]);
    if (array_produtos[2] == "F") {
        $("#F").prop("checked", true);
    } else {
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
    $("#primeiro_peso_animal").val(formatMoney(array_produtos[13]));
    $("#peso_desmama_animal").val(formatMoney(array_produtos[14]));
    $("#ultimo_peso_animal").val(formatMoney(array_produtos[15]));
    $("#nome_registro_animal").val(array_produtos[16]);
    $("#ren_animal").val(array_produtos[17]);
    $("#rgd_animal").val(array_produtos[18]);
    $("#sisbov_animal").val(array_produtos[19]);
    $("#certificadora_animal").val(array_produtos[20]);
    $("#observacao_animal").val(array_produtos[21]);
    if (array_produtos[22] == "S") {
        $("#S").prop("checked", true);
        $(".confirma_gravar").show();
    } else {
        $("#N").prop("checked", true);
        $(".confirma_gravar").hide();
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

    $("#modal_incluir .modal-title").html("Animal - Editar");
    $(".confirma_gravar")
        .html("Confirmar Edição")
        .removeClass("btn-danger")
        .addClass("btn-primary");
    $(".ativo").show();
    $("#informacao").show();

    if (array_produtos[29] == "") {
        $("#registro_alterado").hide();
        $("#alterado_por").hide();
        $("#alterado_em").hide();
    } else {
        $("#registro_alterado").show();
        $("#alterado_por").show();
        $("#alterado_em").show();
    }

    if (array_produtos[31] == "") {
        $("#registro_baixado").hide();
        $("#baixado_por").hide();
        $("#baixado_em").hide();
    } else {
        $("#registro_baixado").show();
        $("#baixado_por").show();
        $("#baixado_em").show();
    }

    $("#modal_incluir").modal("show");
}

function enviar_lixeira(array_animal, opcao) {
    array_produtos = array_animal.split("|");
    $("#codigo_animal").val(array_produtos[0]);
    $("#number_animal").val(array_produtos[1]);
    if (array_produtos[2] == "F") {
        $("#F").prop("checked", true);
    } else {
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
    $("#primeiro_peso_animal").val(formatMoney(array_produtos[13]));
    $("#peso_desmama_animal").val(formatMoney(array_produtos[14]));
    $("#ultimo_peso_animal").val(formatMoney(array_produtos[15]));
    $("#nome_registro_animal").val(array_produtos[16]);
    $("#ren_animal").val(array_produtos[17]);
    $("#rgd_animal").val(array_produtos[18]);
    $("#sisbov_animal").val(array_produtos[19]);
    $("#certificadora_animal").val(array_produtos[20]);
    $("#observacao_animal").val(array_produtos[21]);
    if (array_produtos[22] == "S") {
        $("#S").prop("checked", true);
    } else {
        $("#N").prop("checked", true);
    }

    $("#alfa_animal").val(array_produtos[23]);
    $("#categoria_id").val(array_produtos[24]);
    $("#idade_animal").val(array_produtos[25]);

    $("#tipo_gravacao").val(opcao);

    if (opcao == 2) {
        $("#modal_incluir .modal-title").html("Animal - Enviar para Lixeira");
        $(".confirma_gravar")
            .html("Enviar para Lixeira")
            .removeClass("btn-primary")
            .addClass("btn-danger");
    } else {
        $("#modal_incluir .modal-title").html("Animal - Remover da Lixeira");
        $(".confirma_gravar")
            .html("Remover da Lixeira")
            .removeClass("btn-primary")
            .addClass("btn-danger");
    }

    $(".confirma_gravar").show();
    $(".ativo").show();
    $("#modal_incluir").modal("show");
}

/* chamada da rotina para excluir item para pesar da tabela gerada em excel*/
// sem funcao no momento
function excluir_item_edicao($id, $item, $numero_pesagem, $codigo) {
    if (window.confirm("Confirma enviar esse item para lixeira? " + $codigo)) {
        var gravar_peso = gravar_item_alterado_editar_pesagem(
            $id,
            $item,
            0,
            0,
            "S",
            0
        );
    }
}

/*$(document).ready(function() {
    
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
});*/

$(document).ready(function () {
    $("#tabela_itens_editar tbody tr").change(function () {
        var controle_estoque = $("#controle_estoque").val();
        var data_pesagem = $("#data_pesagem").val();

        if (data_pesagem=='') {
            $(this).find(".peso").find("input").val('');
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html("Informe a Data da Pesagem");
            return;
        }

        if (controle_estoque=='L') {
            var grupo = $(this).find(".grupo").find("input").val();
        }
        else {
            var grupo = 0;
        }

        var peso = $(this).find(".peso").find("input").val();
        var codigo_id = $(this).find(".codigo_id").text();
        var obs = $(this).find(".obs").find("input").val();
        var item = $(this).find(".item").text();

        if (peso == "") {
            peso = 0;
            $(this).find(".peso").find("input").val(peso);
        }

        var gravar_peso = gravar_item_alterado_editar_pesagem(
            codigo_id,
            item,
            peso,
            obs,
            "N",
            grupo
        );
    });

    $('#data_pesagem').change(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const data_pesagem = $("#data_pesagem").val();

        if (data_pesagem>data_atual) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('A Data não pode ser maior que a data atual!');
            $("#data_pesagem").val(data_atual);
            document.getElementById("data_pesagem").style.borderColor = "#0076d7";
        }
    });

    $('.data_pesagem').change(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const data_pesagem = $("#data_pesagem").val();

        if (data_pesagem>data_atual) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('A Data não pode ser maior que a data atual!');
            $("#data_pesagem").val(data_atual);
            document.getElementById("data_pesagem").style.borderColor = "#0076d7";
        }
    });

    $('#data_pesagem').blur(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const data_pesagem = $("#data_pesagem").val();

        if (data_pesagem=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('A Data precisa ser informada!');
            $("#data_pesagem").val(data_atual);
            document.getElementById("data_pesagem").style.borderColor = "#0076d7";
        }
    });

    $('.data_pesagem').blur(function(){
        const date = new Date();
        const data_atual = date.getFullYear()+'-'+
                          (date.getMonth()+1).AddZero()+'-'+
                          date.getDate().AddZero();

        const data_pesagem = $("#data_pesagem").val();

        if (data_pesagem=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('A Data precisa ser informada!');
            $("#data_pesagem").val(data_atual);
            document.getElementById("data_pesagem").style.borderColor = "#0076d7";
        }
    });

    $("#data_pesagem").click(function () {
        document.getElementById("data_pesagem").style.borderColor = "";
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

    $('#codigo_pesagem').change(function(){
        $('.consultar').show();
        $('.filtros_consulta').hide();
    });

    // Fim acendo botão 


    $("#tabela_pesagem").DataTable({
        destroy: true,
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

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $("#tabela_itens_consulta").DataTable({
        responsive: true,
        paging: false,
        ordering: true,
        info: true,
        order: [[ 1, "asc" ]],
        language: {
            sSearch: "Busca:",
            zeroRecords: "Nada encontrado",
            info: "Registros: _END_ ",
            infoEmpty: "Nenhum registro disponível",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
        },
        columnDefs: [
            { type: 'date-br', targets: 4 }
        
        ],

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $("#tabela_itens_editar").DataTable({
        responsive: true,
        paging: false,
        ordering: true,
        info: true,
        order: [[ 1, "asc" ]],
        language: {
            sSearch: "Busca:",
            zeroRecords: "Nada encontrado",
            info: "Registros encontrados: _END_ ",
            infoEmpty: "Nenhum registro disponível",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
        },
        columnDefs: [
            { type: 'date-br', targets: 4 }
        
        ],

        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $("#nascimento_animal").change(function () {
        var data_nascimento = $("#nascimento_animal").val();

        $.post(
            "ler_categoria_animal.php",
            { data_nascimento: data_nascimento },
            function (valor) {
                var php = valor.split("<|>");

                if (php[0] == 1) {
                    $("#categoria_id").val(php[1]);
                    $("#idade_animal").val(php[2]);
                }
                return;
            }
        );
    });

    $("#peso_animal").change(function (event) {
        var animal_codigo_id = $("#codigo_id").val();

        if (animal_codigo_id == 0) {
            $(".alert_erro_animal .negrito").html("");
            $(".alert_erro_animal span").html("Id do animal não cadastrado.");
            $(".alert_erro_animal").show();
            $("#codigo_number_filtro").val("");
            $("#codigo_id").val(0);
            $("#peso_animal").val(000);
            //document.getElementById("id_animal").focus();
            return;
        }
    });

    $("#local_pesagem").change(function () {
        $("#codigo_local_filtro").val("");

        var local = $("#local_pesagem").val();
        $("#codigo_local_filtro").val(local);

        var controle_estoque = $("#controle_estoque").val();

        if (controle_estoque == "I") {
            var local = $("#local_pesagem").val();
            exibe_filtro();
        } 
        else {
            var select = $("#local_pesagem").val();

            if (select != 0) {
                select = document.getElementById("local_pesagem");
                local = select.options[select.selectedIndex].text;
                $("#descricao_local").val(local);
            }

            var local = $("#local_pesagem").val();
            $.post("lista_pasto.php", { local: local }, function (valor) {
                $("select[name=pasto]").html(valor);
                $(".selectpicker").selectpicker("refresh");
            });

            exibe_filtro_lote();
        }
    });

    $("#codigo_local_filtro").change(function () {
        $("#local_pesagem").val("");

        var local = $("#codigo_local_filtro").val();
        //$("#local_pesagem").selectpicker("val", local);
        $("#local_pesagem").val(local);
        var controle_estoque = $("#controle_estoque").val();

        if (controle_estoque == "I") {
            var local = $("#codigo_local_filtro").val();
        } else {
            var select = $("#local_pesagem").val();

            if (select != 0) {
                select = document.getElementById("local_pesagem");
                local = select.options[select.selectedIndex].text;
                $("#descricao_local").val(local);
            }

            var local = $("#local_pesagem").val();
            $.post("lista_pasto.php", { local: local }, function (valor) {
                $("select[name=pasto]").html(valor);
                $(".selectpicker").selectpicker("refresh");
            });

            exibe_filtro_lote();
        }
    });

    $("#epoca_pesagem_filtro").change(function () {
        $("#epoca_pesagem").val("");

        var epoca = $("#epoca_pesagem_filtro").val();
        $("#epoca_pesagem").val(epoca);
        $("#epoca_pesagem_lote").val(epoca);

        var controle_estoque = $("#controle_estoque").val();

        if (controle_estoque == "I") {
            exibe_filtro();
        } else {
            exibe_filtro_lote();
        }
    });

    $("#epoca_pesagem").change(function () {
        $("#epoca_pesagem_filtro").val("");

        var epoca = $("#epoca_pesagem").val();
        $("#epoca_pesagem_filtro").val(epoca);

        var controle_estoque = $("#controle_estoque").val();
        if (controle_estoque == "I") {
            exibe_filtro();
        } else {
            var select = $("#epoca_pesagem").val();

            if (select != 0) {
                select = document.getElementById("epoca_pesagem");
                epoca = select.options[select.selectedIndex].text;
                $("#descricao_epoca").val(epoca);
            }
            exibe_filtro_lote();
        }
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

    $("#pasto").change(function () {
        $("#alert_erro_estimado .negrito").html("");
        $("#alert_erro_estimado span").html("");
        $(".alert_erro_estimado").hide();

        var controle_estoque = $("#controle_estoque").val();

        if (controle_estoque == "L") {
            var select = $("#pasto").val();

            if (select != 0) {
                var options = $("#pasto option:selected");
                var pastos = [];

                $(options).each(function () {
                    var desc = $(this).bind("#pasto").text();
                    pastos.push(desc.trim());
                });

                $("#descricao_pasto").val(pastos);
            }

            exibe_filtro_lote();
        }
    });

    $("#categoria_filtro").change(function () {
        exibe_filtro_lote();
    });

    $("#femea").click(function(){
        var controle_estoque = $("#controle_estoque").val();

        if (controle_estoque == "L") {
            exibe_filtro_lote();
        }
        else {
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
        }
    });

    $("#macho").click(function(){
        var controle_estoque = $("#controle_estoque").val();

        if (controle_estoque == "L") {
            exibe_filtro_lote();
        }
        else {
            var macho = $('#macho');
            var femea = $('#femea');
            
            if (macho.is(":checked") && femea.is(":checked")){
                $('.abrir_filtro_reproducao').show();
                $('.filtro_reproducao').hide();
                divFiltroReproducaoVisivel = false;

            }

            limpar_filtros_reproducao();
            exibe_filtro();
        }
    });

    $("#codigo_categoria").change(function () {
        $("#alert_erro_estimado .negrito").html("");
        $("#alert_erro_estimado span").html("");
        $(".alert_erro_estimado").hide();

        var categoria = $("#codigo_categoria").val();

        $("#sexo_lote").val(categoria.substr(0, 1));
        $("#categoria_lote").val(categoria.substr(1, 3));
        $("#qtd_lote").val(categoria.substr(4));
        $("#qtd_digitado_anterior").val("");
    });

    $("#qtd_a_pesar_pesagem_lote").change(function () {
        $("#alert_erro_estimado .negrito").html("");
        $("#alert_erro_estimado span").html("");
        $(".alert_erro_estimado").hide();

        var qtd_a_pesar = parseFloat($("#qtd_a_pesar_pesagem_lote").val());
        $(".total_a_pesar_pesagem_lote").text(
            "Animais para Pesar: " + qtd_a_pesar
        );
    });

    $("#lote_pesagem_lote").click(function () {
        $("#alert_erro_estimado .negrito").html("");
        $("#alert_erro_estimado span").html("");
        $(".alert_erro_estimado").hide();
    });

    $("#qtd_estimada").click(function () {
        $("#alert_erro_estimado .negrito").html("");
        $("#alert_erro_estimado span").html("");
        $(".alert_erro_estimado").hide();
    });
});

function gravar_pasto_grupo_pesagem(pasto, grupo, id_pesagem){
    $.ajax({
        type: "POST",
        url: "gravar_pesagem_pasto_destino.php",
        data:{id_pesagem:id_pesagem,
              grupo: grupo,
              pasto: pasto},
        success: function (data) {
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
            /*else if (data.success) {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }*/
        },
    });
}


// Grava peso digitado na lista do excel
function gravar_item_alterado_editar_pesagem(
    codigo_id,
    item,
    peso,
    obs,
    excluir,
    grupo
) {

    var total_peso = 0;
    var animais_pesados = 0;
    var animais_para_pesar = 0;

    // a logica para excluir == 'S' não esta liberada por enquanto
    if (excluir == "S") {
        $("#tabela_itens_editar tbody tr").each(function () {
            var id_lista = $(this).find(".codigo_id").html();
            var peso = $(this).find(".peso").find("input").val();
            $("#excluir_id").val("S");

            if (peso != 0 && id_lista != codigo_id) {
                total_peso += parseFloat(peso);
                animais_pesados++;
            } else {
                animais_para_pesar++;
            }
        });
    } else {
        $("#tabela_itens_editar tbody tr").each(function () {
            var id_lista = $(this).find(".codigo_id").html();
            var peso = $(this).find(".peso").find("input").val();

            $("#excluir_id").val("N");

            if (peso != 0) {
                total_peso += parseFloat(peso);
                animais_pesados++;
            } else {
                animais_para_pesar++;
            }
        });
    }

    if (animais_pesados != 0) {
        $("#qtd_pesado").val(animais_pesados);
        $(".qtd_pesado").text(animais_pesados);

        $("#qtd_a_pesar").val(animais_para_pesar);
        $(".qtd_a_pesar").text(animais_para_pesar);

        $(".peso_total_kg").val(total_peso);
        $(".peso_total_kg").text(formatMoney(total_peso));

        var peso_total_arroba = total_peso / 30;
        $(".peso_total_arroba").val(peso_total_arroba);
        $(".peso_total_arroba").text(formatMoney(peso_total_arroba));

        var peso_medio_kg = total_peso / animais_pesados;
        $(".peso_medio_kg").val(peso_medio_kg);
        $(".peso_medio_kg").text(formatMoney(peso_medio_kg));

        var peso_medio_arroba = peso_total_arroba / animais_pesados;
        $(".peso_medio_arroba").val(peso_medio_arroba);
        $(".peso_medio_arroba").text(formatMoney(peso_medio_arroba));

        if (animais_pesados>7) {
            $(".botoes_final").show();
        }
        else {
            $(".botoes_final").hide();
        }
    }

    $("#codig_id").val(codigo_id);
    $("#item_id").val(item);
    $("#peso_id").val(peso);
    $("#obs_id").val(obs);
    $("#grupo_id").val(grupo);

    var dados = $("#form_gravar_pesagem").serialize();
    $.ajax({
        type: "POST",
        url: "gravar_pesagem_item.php",
        data: dados,
        success: function (data) {
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            } else if (data.success) {
                $("#mensagem_retorno_excluir").modal();
                $("#mensagem_retorno_excluir .modal-body").html(data.message);
            }
        },
    });
}

// Finalizar a pesagem on-line/of-line
function gravar_pesagem_finalizar() {
    $("#finalizar_pesagem").val("S");

    var dados = $("#form_gravar_pesagem").serialize();

    $.ajax({
        type: "POST",
        url: "gravar_pesagem_finalizar.php",
        data: dados,
        success: function (data) {
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            } else if (data.success) {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        },
    });
}

// Grava peso digitado na pesagem on-line
function gravar_pesagem(opcao) {
    var array_tabela_itens = [];
    var grupo_itens = "";

    $("#tabela_itens tbody tr").each(function () {

        var codigo = $(this).find(".id_animal").html();

        // ignora linhas inválidas (ex: DataTable "Nenhum registro")
        if (!codigo) return;

        var peso = $(this).find(".peso_animal").html();
        var sexo = $(this).find(".sexo_animal").html();
        var nascimento = $(this).find(".nascimento_animal").html();
        var pelagem = $(this).find(".pelagem_animal").html();
        var raca = $(this).find(".raca_animal").html();
        var mae = $(this).find(".mae_animal").html();
        var observacao = $(this).find(".obs_pesagem").html();
        var apartacao = $(this).find(".apartacao").html();

        var codigo_id = $(this).find(".codigo_id").html() || "";
        codigo_id = parseInt(codigo_id.toString().replace(/\D/g, ''), 10) || 0;

        var mens_repetido = $(this).find(".mens_repetido").html() || "";
        var id_repetido = $(this).find(".id_repetido").html() || "";

        var valor = [
            codigo,
            peso,
            sexo,
            nascimento,
            raca,
            pelagem,
            mae,
            observacao,
            codigo_id,
            apartacao,
            mens_repetido,
            id_repetido
        ];

        array_tabela_itens.push(valor.join("|"));
    });

    // monta string final
    grupo_itens = array_tabela_itens.join("<|>");

    $("#array_itens").val(grupo_itens);
    var dados = $("#form_gravar_pesagem").serialize();

    $.ajax({
        type: "POST",
        url: "gravar_pesagem_individual.php",
        data: dados,
        success: function (data) {
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            } else {
                if (data.numero_doc!=undefined) {
                    $("#numero_pesagem_id").val(data.numero_doc);
                }

                $("#tipo_gravacao").val(2);

                if (opcao==2) {
                    monta_lista_editar_online();
                }
            }
        },
    });
}

function gravar_pesagem_sem_finalizar_offline() {
    var dados = $("#form_gravar_pesagem").serialize();

    $.ajax({
        type: "POST",
        url: "gravar_pesagem_sem_finalizar_offline.php",
        data: dados,
        success: function (data) {
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            }
        },
    });
}

function gravar_pesagem_lote() {
    var array_tabela_itens = new Array();
    var valor = new Array();
    var grupo_itens = "";

    $("#itens_pesagem_lote tbody tr").each(function () {
        for (i = 0; i <= 8; i++) {
            valor[i] = 0;
        }

        var categoria = $(this).find(".id_categoria").html();
        var peso = $(this).find(".peso_animal").html();
        var sexo = $(this).find(".sexo_animal").html();
        var qtd_animais = $(this).find(".qtd_animal").html();
        var peso_medio = $(this).find(".peso_medio").html();
        var arroba = $(this).find(".peso_arroba").html();
        var arroba_media = $(this).find(".peso_medio_arroba").html();
        var grupo_destino = $(this).find(".grupo_destino").html();

        if (grupo_destino == '') {
            grupo_destino = 0;
        }

        if (categoria != undefined && categoria != 0) {
            valor[0] = categoria;
            valor[1] = peso;
            valor[2] = sexo;
            valor[3] = peso_medio;
            valor[4] = arroba;
            valor[5] = arroba_media;
            valor[6] = qtd_animais;
            valor[7] = grupo_destino;

            var tabela_itens = valor.join("|");
            array_tabela_itens.push(tabela_itens);
            grupo_itens = array_tabela_itens.join("<|>");
        }
    });

    $("#array_itens_pesagem_lote").val(grupo_itens);
    var tipo_gravacao = $("#tipo_gravacao").val();

    var dados = $("#form_gravar_pesagem").serialize();

    $.ajax({
        type: "POST",
        url: "gravar_pesagem_lote.php",
        data: dados,
        success: function (data) {
            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            } else if (data.success) {
                $("#mensagem_sair_retorno").modal();
                $("#mensagem_sair_retorno .modal-body").html("Pesagem finalizada com sucesso.");
                //$("#numero_pesagem_id").val(data.numero_doc);
                //$("#tipo_gravacao").val(2);
            }
        },
    });
}

function digita_valor() {
    $("#peso_inicial_nasc_filtro").bind("keypress", mask.money);
    $("#peso_final_nasc_filtro").bind("keypress", mask.money);
    $("#peso_inicial_desmama_filtro").bind("keypress", mask.money);
    $("#peso_final_desmama_filtro").bind("keypress", mask.money);
    $("#peso_inicial_ultimo_filtro").bind("keypress", mask.money);
    $("#peso_final_ultimo_filtro").bind("keypress", mask.money);
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

function exibe_peso_desmama() {
    var peso_desmama_animal = $("#peso_desmama_animal").val();
    if (verifica_virgula(peso_desmama_animal) == ",") {
        peso_desmama_animal = replace_valor(peso_desmama_animal);
    }

    $("#peso_desmama_animal").val(formatMoney(peso_desmama_animal));
}

function exibe_ultimo_peso() {
    var ultimo_peso_animal = $("#ultimo_peso_animal").val();
    if (verifica_virgula(ultimo_peso_animal) == ",") {
        ultimo_peso_animal = replace_valor(ultimo_peso_animal);
    }

    $("#ultimo_peso_animal").val(formatMoney(ultimo_peso_animal));
}

function listar_grupo_destino(id_pesagem) {

    $("#id_pesagem").val(id_pesagem);

    $.post("lista_grupo_pesagem_lote.php", {id_pesagem: id_pesagem}, function (valor) {

        $("div[id=lista_grupos]").html(valor);
        $("#modal_listar_grupo_destino").modal("show");
    });
}

function exportar_excel_pesagem() {
    var controle_estoque = $("#controle_estoque").val();
    let dataObj = {};

    if (controle_estoque == "I") {
        var data_pesagem = $("#data_pesagem").val();
        var local = $("#local_pesagem").val();
        var epoca = $("#epoca_pesagem").val();

        dataObj["local"] = local;
        dataObj["epoca"] = epoca;

        if (data_pesagem == '') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html("Informe a Data");
            return;
        }

        if (local == 0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html("Informe o Local");
            return;
        }

        if (epoca == 0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html("Informe a Epoca da Pesagem");
            return;
        }

        if ($("#positivo").is(":checked") == true) {
            var estacao = $("#codigo_estacao_filtro").val();

            if (estacao=='') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Para Filtros Positivas, selecione a estação de monta!');
                return;
            }
        }

        const desc_filtro = $("#descricao_filtro").val();
        const data_nasc_inicial = $("#data_nasc_inicial_filtro").val();
        const data_nasc_final = $("#data_nasc_final_filtro").val();
        const peso_nasc_inicial = $("#peso_inicial_nasc_filtro").val();
        const peso_nasc_final = $("#peso_final_nasc_filtro").val();
        const peso_desmama_inicial = $("#peso_inicial_desmama_filtro").val();
        const peso_desmama_final = $("#peso_final_desmama_filtro").val();
        const peso_ult_inicial = $("#peso_inicial_ultimo_filtro").val();
        const peso_ult_final = $("#peso_final_ultimo_filtro").val();

        dataObj["data_pesagem"] = data_pesagem;
        dataObj["data_nasc_inicial"] = data_nasc_inicial;
        dataObj["data_nasc_final"] = data_nasc_final;
        dataObj["peso_nasc_inicial"] = peso_nasc_inicial;
        dataObj["peso_nasc_final"] = peso_nasc_final;
        dataObj["peso_ult_inicial"] = peso_ult_inicial;
        dataObj["peso_ult_final"] = peso_ult_final;
        dataObj["peso_desmama_inicial"] = peso_desmama_inicial;
        dataObj["peso_desmama_final"] = peso_desmama_final;
        dataObj["desc_filtro"] = desc_filtro;
        dataObj["ativo"] = 'S';

        var macho = $("#macho");
        var femea = $("#femea");

        if (macho.is(":checked") && femea.is(":checked")) {
            sexo = ["Todos"];
        } else if (macho.is(":checked")) {
            sexo = ["M"];
        } else if (femea.is(":checked")) {
            sexo = ["F"];
        }

        dataObj["sexo"] = sexo;

        dataObj["filtro_reproducao"] = 'N';

        const filtro_estacao = $("#codigo_estacao_filtro").val();

        if (filtro_estacao === null) {
            var array_estacao = new Array();

            dataObj["filtro_estacao"] = array_estacao;
        } else {
            var array_estacao = new Array();
            var valor = new Array();

            for (i = 0; i < filtro_estacao.length; i++) {
                valor[i] = filtro_estacao[i];
            }
            dataObj["filtro_estacao"] = valor;
        }

        const categoria = $("#codigo_categoria_filtro").val();
        if (categoria === null) {
            dataObj["categoria"] = '';
        } else {
            var array_categoria = new Array();
            var valor = new Array();

            for (i = 0; i < categoria.length; i++) {
                valor[i] = categoria[i];
            }
            dataObj["categoria"] = valor;
        }

        const origem = $("#codigo_origem_filtro").val();

        if (origem == null) {
            var array_origem = new Array();

            dataObj["origem"] = array_origem;
        } else {
            var array_origem = new Array();
            var valor = new Array();

            for (i = 0; i < origem.length; i++) {
                valor[i] = origem[i];
            }
            dataObj["origem"] = valor;
        }

        var raca = $("#codigo_raca_filtro").val();

        if (raca == null) {
            var array_raca = new Array();

            dataObj["codigos_racas"] = array_raca;
        } else {
            var array_raca = new Array();
            var valor = new Array();

            for (i = 0; i < raca.length; i++) {
                valor[i] = raca[i];
            }
            dataObj["codigos_racas"] = valor;
        }

        const pai = $("#codigo_pai_filtro").val();

        if (pai == null) {
            var array_pai = new Array();

            dataObj["codigos_pais"] = array_pai;
        } else {
            var array_pai = new Array();
            var valor = new Array();

            for (i = 0; i <= pai.length; i++) {
                valor[i] = pai[i];
            }
            dataObj["codigos_pais"] = valor;
        }

        const mae = $("#codigo_mae_filtro").val();

        if (mae == null) {
            var array_mae = new Array();

            dataObj["codigos_maes"] = array_mae;
        } else {
            var array_mae = new Array();
            var valor = new Array();

            for (i = 0; i < mae.length; i++) {
                valor[i] = mae[i];
            }
            dataObj["codigos_maes"] = valor;
        }

        const num_parto_de = $("#num_parto_de_filtro").val();
        const num_parto_ate = $("#num_parto_ate_filtro").val();
        dataObj["num_parto_de"] = num_parto_de;
        dataObj["num_parto_ate"] = num_parto_ate;

        const num_aborto_de = $("#num_aborto_de_filtro").val();
        const num_aborto_ate = $("#num_aborto_ate_filtro").val();
        dataObj["num_aborto_de"] = num_aborto_de;
        dataObj["num_aborto_ate"] = num_aborto_ate;

        const num_natimorto_de = $("#num_natimorto_de_filtro").val();
        const num_natimorto_ate = $("#num_natimorto_ate_filtro").val();
        dataObj["num_natimorto_de"] = num_natimorto_de;
        dataObj["num_natimorto_ate"] = num_natimorto_ate;

        const previsao_parto_de = $("#previsao_parto_de_filtro").val();
        const previsao_parto_ate = $("#previsao_parto_ate_filtro").val();
        dataObj["previsao_parto_de"] = previsao_parto_de;
        dataObj["previsao_parto_ate"] = previsao_parto_ate;

        const data_paricao_de = $("#data_paricao_de_filtro").val();
        const data_paricao_ate = $("#data_paricao_ate_filtro").val();
        dataObj["data_paricao_de"] = data_paricao_de;
        dataObj["data_paricao_ate"] = data_paricao_ate;

        if (num_parto_de!='' && num_parto_ate!='') {
            dataObj["filtro_num_parto"]  = 'S';
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
            dataObj["filtro_num_parto"] = 'N';
        }

        if (num_aborto_de!='' && num_aborto_ate!='') {
            dataObj["filtro_num_aborto"] = 'S';
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
            dataObj["filtro_num_aborto"] = 'N';
        }

        if (num_natimorto_de!='' && num_natimorto_ate!='') {
            dataObj["filtro_num_natimorto"] = 'S';
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
            dataObj["filtro_num_natimorto"] = 'N';
        }

        if (previsao_parto_de!='' && previsao_parto_ate!='') {
            dataObj["filtro_previsao_parto"]  = 'S';
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
            dataObj["filtro_previsao_parto"] = 'N';
        }

        if (data_paricao_de!='' && data_paricao_ate!='') {
            dataObj["filtro_data_paricao"]  = 'S';
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
            dataObj["filtro_data_paricao"] = 'N';
        }

        if ($("#vacas_paridas").is(":checked") == true){
            dataObj["filtro_vacas_paridas"]='S';
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
             dataObj["filtro_vacas_paridas"]='N';
        }

        if ($("#vacas_solteiras").is(":checked") == true){
            dataObj["filtro_vacas_solteiras"]  = 'S';
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
            dataObj["filtro_vacas_solteiras"] = 'N';
        }

        if ($("#vacas_prenhes").is(":checked") == true){
            dataObj["filtro_vacas_prenhas"] = 'S';
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
            dataObj["filtro_vacas_prenhas"] = 'N';
        }

        if ($("#descarte").is(":checked") == true && 
            $("#descarte_nao").is(":checked") == true) {
            dataObj["filtro_descarte"] = '';
        }
        else if ($("#descarte").is(":checked") == true){
            dataObj["filtro_descarte"] = 'S';
            dataObj["filtro_reproducao"] = 'S';
        }
        else if ($("#descarte_nao").is(":checked") == true){
            dataObj["filtro_descarte"] = 'N';
            dataObj["filtro_reproducao"] = 'S';
        }
        else {
            dataObj["filtro_descarte"] = '';
        }

        /*if ($("#descarte").is(":checked") == true){
            dataObj["filtro_descarte"]  = 'S';
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
            dataObj["filtro_descarte"] = 'N';
        }*/

        if ($("#positivo").is(":checked") == true){
            dataObj["filtro_positivas"] = 'S';
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
            dataObj["filtro_positivas"] = 'N';
        }

        if ($("#negativo").is(":checked") == true){
            dataObj["filtro_negativas"] = 'S'
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
            dataObj["filtro_negativas"] = 'N';
        }

        if ($("#monta_natural").is(":checked") == true){
            dataObj["filtro_monta_natural"] = 'S';
            dataObj["filtro_reproducao"] = 'S'
        }
        else {
            dataObj["filtro_monta_natural"] = 'N';
        }

        $("#aguardar").modal('show');

        $.post(
            "rel_exportar_pesagem_excel.php",
            { dataObj: JSON.stringify(dataObj) },
            function (data) {
                if (data.error) {
                    $("#aguardar").modal('hide');
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else {
                    num_pesagem = data.num_pesagem;
                    filtro_pesagem = data.desc_filtro;
                    filtro_local = data.desc_local;
                    filtro_epoca = data.desc_epoca;
                    if (!num_pesagem) {
                        $("#aguardar").modal('hide');
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html('Não existem animais para listar!');
                        return;
                    }
                    else {
                        tout = setTimeout("imprimir_pesagem_excel(num_pesagem, filtro_pesagem, filtro_local, filtro_epoca)", 4000);
                    }
                }
            }
        );
    }
    else { // Pesagem excel lote
        var desc_filtro = $("#descricao_filtro").val();
        var data_pesagem = $("#data_pesagem").val();
        var local = $("#local_pesagem").val();
        var epoca = $("#epoca_pesagem").val();
        var pasto = $("#pasto").val();
        var categoria = $("#categoria_filtro").val();

        let dataObj = {};

        dataObj["local"] = local;
        dataObj["epoca"] = epoca;
        dataObj["desc_filtro"] = desc_filtro;

        if (data_pesagem == '') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html("Informe a Data");
            return;
        }

        if (local == 0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html("Informe o Local");
            return;
        }

        if (epoca == 0) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html("Informe a Epoca da Pesagem");
            return;
        }

        var valor = new Array();
        if (pasto != null) {
            for (i = 0; i <= pasto.length; i++) {
                valor[i] = pasto[i];
            }
        }
        dataObj["pasto_filtro"] = valor;

        var macho = $("#macho");
        var femea = $("#femea");

        if (macho.is(":checked") && femea.is(":checked")) {
            sexo = ["Todos"];
        } else if (macho.is(":checked")) {
            sexo = ["M"];
        } else if (femea.is(":checked")) {
            sexo = ["F"];
        }

        dataObj["sexo_filtro"] = sexo;

        var valor = new Array();
        if (categoria != null) {
            for (i = 0; i <= categoria.length; i++) {
                valor[i] = categoria[i];
            }
        }
        dataObj["categoria_filtro"] = valor;

        var data_pesagem = $("#data_pesagem").val();
        dataObj["data_pesagem"] = data_pesagem;


        $("#aguardar").modal('show');

        $.post(
            "rel_exportar_pesagem_excel_lote.php",
            { dataObj: JSON.stringify(dataObj) },
            function (data) {
                if (data.error) {
                    $("#aguardar").modal('hide');
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else {
                    num_pesagem = data.num_pesagem;
                    filtro_pesagem = data.desc_filtro;
                    filtro_local = data.desc_local;
                    filtro_epoca = data.desc_epoca;

                    if (!num_pesagem) {
                        $("#aguardar").modal('hide');
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html('Não existem animais para listar!');
                        return;
                    }
                    else {
                        tout = setTimeout("imprimir_pesagem_excel_lote(num_pesagem, filtro_pesagem, filtro_local, filtro_epoca)", 4000);
                    }
                }
            }
        );
    }
}

function importar_excel_pesagem(
    $codigo,
    $desc_local,
    $descricao_epoca,
    $qtd_animais,
    $codigo_local,
    $codigo_epoca
) {
    $(".local_pesado").val($desc_local);
    $(".epoca_pesado").val($descricao_epoca);
    $(".animais_pesados").val($qtd_animais);

    $("#local_pesado").val($codigo_local);
    $("#epoca_pesado").val($codigo_epoca);
    $("#numero_doc").val($codigo);
    $("#animais_pesados").val($qtd_animais);

    $("#modal_importar_excel").modal("show");
}

function imprimir_pesagem(pesagem_id) {
    location.href = "rel_pesagem_excel.php?pesagem_id=" + pesagem_id;
}

function imprimir_pesagem_lote(pesagem_id) {
    location.href = "rel_pesagem_excel_lote.php?pesagem_id=" + pesagem_id;
}

function voltar_relatorios() {
    var tipo_relatorio = $("#tipo_relatorio").val();

    if (tipo_relatorio==1) {
        location.href='form_relatorios_produtivos.php?tipo='+tipo_relatorio;
    }
    else {
        location.href='form_pesagem_animais.php?tipo='+tipo_relatorio;
    }
}

function voltar_filtro_historico_pesagem_lote() {
    var tipo_relatorio = $("#tipo_relatorio").val();
    location.href='form_rel_historico_pesagem_lote.php?tipo='+tipo_relatorio;
}

function listar_historico_pesagem_lote(){
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var local = $("#codigo_fazenda").val();
    var categoria = $("#codigo_categoria_filtro").val();

    if (data_inicial == '' || data_final == '') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Data Inicial e Final.');
        return;
    }

    if (data_inicial > data_final) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Informe a Data Inicial e Final Corretamente.');
        return;
    }

    if (local=='000000000') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione a Fazenda.');
        return;
    }

    var data_ini = data_inicial.split("-");
    var data_fim = data_final.split("-");
    var periodo_filtro = 'Período da Pesagem: ' + data_ini[2]+'/'+data_ini[1]+'/'+data_ini[0] + ' até ' + data_fim[2]+'/'+data_fim[1]+'/'+data_fim[0]+'->';

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
        $("#mensagem_erro .modal-body").html('Informe o sexo.');
        return;
    }

    var options = $('#codigo_fazenda option:selected');
    var local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_fazenda').text();
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

    var descricao_filtro = local_filtro+periodo_filtro+categoria_filtro+'Sexo:'+sexo;

    var tipo_relatorio = $("#tipo_relatorio").val();

    $('#modal_filtros').modal('hide');

    $("#aguardar").modal();

    location.href='form_lista_historico_pesagem_lote_rel.php?descricao_filtro=' + descricao_filtro + 
    '&local=' + local + '&categoria=' + array_categoria + 
    '&sexo=' + sexo + '&data_inicial=' + data_inicial + '&data_final=' + data_final + 
    '&tipo=' + tipo_relatorio;
}

function lista_historico_pesagem_lote_excel(){
    var data_inicial = $("#data_inicial").val();
    var data_final = $("#data_final").val();
    var local = $("#codigo_local").val();
    var categoria = $("#codigo_categoria").val();
    var descricao_filtro = $("#descricao_filtro").val();
    var sexo = $("#sexo").val();

    $("#aguardar").modal();

    location.href='rel_historico_pesagem_lote_excel.php?descricao_filtro=' + descricao_filtro + 
    '&local=' + local + '&categoria=' + categoria + 
    '&sexo=' + sexo + '&data_inicial=' + data_inicial + '&data_final=' + data_final;

    tout = setTimeout('limpar_tela()', 5000);

}

$("#tabela_itens").on("keyup", "input", function (event) {
    if (event.which == 13) {
        var generico = $("#tabela_itens").find("input:visible");
        var indice = generico.index(event.target) + 1;
        var seletor = $(generico[indice]).focus();

        if (seletor.length == 0) {
            event.target.focus();
        }
    }
});

function filtro_apartacao() {
    var codigo_pesagem = $("#numero_pesagem_id").val(); // Pega o ID da pesagem atual

    // Limpa o corpo do modal e coloca um carregando
    $("#modal_filtro_apartacao .modal-body").html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Carregando...</div>');
    
    // Abre o modal
    $("#modal_filtro_apartacao").modal('show');

    // Busca os dados agrupados
    $.post("ler_pesagem_apartacao.php", { codigo_pesagem: codigo_pesagem }, function(data) {
        // Insere a tabela gerada pelo PHP dentro do modal
        $("#modal_filtro_apartacao .modal-body").html(data);
    });
}

/*function limpar_selecao_origem() {
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

Number.prototype.AddZero= function(b,c){
    var  l= (String(b|| 10).length - String(this).length)+1;
    return l> 0? new Array(l).join(c|| '0')+this : this;
}

var mask = {
    money: function () {
        var el = this,
            exec = function (v) {
                v = v.replace(/\D/g, "");
                v = new String(Number(v));
                var len = v.length;
                if (1 == len) v = v.replace(/(\d)/, "0.0$1");
                else if (2 == len) v = v.replace(/(\d)/, "0.$1");
                else if (len > 2) {
                    v = v.replace(/(\d{2})$/, ".$1");
                }
                return v;
            };

        setTimeout(function () {
            el.value = exec(el.value);
        }, 1);
    },
};

var mask2 = {
    money: function () {
        var el = this,
            exec = function (v) {
                v = v.replace(/\D/g, "");
                v = new String(Number(v));
                var len = v.length;
                if (1 == len) v = v.replace(/(\d)/, "0.0$1");
                else if (2 == len) v = v.replace(/(\d)/, "0.$1");
                else if (3 == len) v = v.replace(/(\d)/, "0.$1");
                else if (len > 3) {
                    v = v.replace(/(\d{3})$/, ".$1");
                }
                return v;
            };

        setTimeout(function () {
            el.value = exec(el.value);
        }, 1);
    },
};

function formatMoney(n, c, d, t) {
    (c = isNaN((c = Math.abs(c))) ? 2 : c),
        (d = d == undefined ? "," : d),
        (t = t == undefined ? "." : t),
        (s = n < 0 ? "-" : ""),
        (i = parseInt((n = Math.abs(+n || 0).toFixed(c))) + ""),
        (j = (j = i.length) > 3 ? j % 3 : 0);
    return (
        s +
        (j ? i.substr(0, j) + t : "") +
        i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) +
        (c
            ? d +
              Math.abs(n - i)
                  .toFixed(c)
                  .slice(2)
            : "")
    );
}

function formatMoney2(n, c, d, t) {
    (c = isNaN((c = Math.abs(c))) ? 3 : c),
        (d = d == undefined ? "," : d),
        (t = t == undefined ? "." : t),
        (s = n < 0 ? "-" : ""),
        (i = parseInt((n = Math.abs(+n || 0).toFixed(c))) + ""),
        (j = (j = i.length) > 3 ? j % 3 : 0);
    return (
        s +
        (j ? i.substr(0, j) + t : "") +
        i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) +
        (c
            ? d +
              Math.abs(n - i)
                  .toFixed(c)
                  .slice(2)
            : "")
    );
}

function replace_valor(valor_replace) {
    valor_replace = valor_replace.replace(".", "");
    valor_replace = valor_replace.replace(".", "");
    valor_replace = valor_replace.replace(".", "");
    valor_replace = valor_replace.replace(",", ".");
    return valor_replace;
}

function verifica_virgula(vlr) {
    var virgula = "";

    for (i = 0; i < vlr.length; i++) {
        if (vlr.charAt(i) == ",") {
            virgula = ",";
        }
    }
    return virgula;
}

/** permite digitar somente numeros nos campos numericos */
function numeros(field, event) {
    var keyCode = event.keyCode
        ? event.keyCode
        : event.which
        ? event.which
        : event.charCode;

    if (
        (keyCode >= 48 && keyCode <= 57) ||
        keyCode == 8 ||
        keyCode == 9 ||
        keyCode == 13 ||
        keyCode == 46
    ) {
        if (keyCode == 13) {
            var i;
            for (i = 0; i < field.form.elements.length; i++)
                if (field == field.form.elements[i]) break;
            i = (i + 1) % field.form.elements.length;
            field.form.elements[i].focus();
            return false;
        } else return true;
    } else {
        return false;
    }
}

function desabilita_enter(field, event) {
    var keyCode = event.keyCode
        ? event.keyCode
        : event.which
        ? event.which
        : event.charCode;

    if (keyCode == 13) {
        var i;
        for (i = 0; i < field.form.elements.length; i++)
            if (field == field.form.elements[i]) break;
        i = (i + 1) % field.form.elements.length;
        field.form.elements[i].focus();
        return false;
    } else return true;
}

function adicionaZero(numero) {
    if (numero <= 9) return "0" + numero;
    else return numero;
}

function removerZeros(valor) {
    return valor.replace(/(^|-)0+/, '$1');
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