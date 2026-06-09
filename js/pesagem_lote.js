/**TABELA DE PESAGEM PARA SISTEMA POR LOTE*/
var num_pesagem = 0;
var filtro_pesagem = '';
var filtro_local = '';
var filtro_epoca = '';

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

    $.post("lista_estacao_monta_descricao.php", {}, function(valor){
        $("select[name=codigo_estacao_filtro]").html(valor);
    });

    var erro_importar_pesagem = $("#erro_importar_pesagem").val();

    if (erro_importar_pesagem != "" && erro_importar_pesagem != undefined) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(erro_importar_pesagem);
        $("#erro_importar_pesagem").val("");
    }

    $.post("lista_local.php", { tipo: 0 }, function (valor) {
        $("select[name=local_pesagem]").html(valor);
    });

    var controle_estoque = $("#controle_estoque").val();

    var editar_online = $("#editar_online").val();

    if (editar_online=="S") {
        monta_lista_editar_online();
    }

    consulta_pesagem_sem_finalizar();    

});

function monta_lista_editar_online() {
    var numero_pesagem_id = $("#numero_pesagem_id").val();

    $.post("ler_pesagem_online.php", { pesagem_id: numero_pesagem_id,}, function (valor) {
        var php = valor.split("<|>");
        var php_array_itens = php[8].split("<!>");

        $(".descricao_filtro").text(php[0]);
        $("#descricao_filtro").val(php[0]);
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

        for (var i = 0; i < php_array_itens.length; i++) {
            var php_item = php_array_itens[i].split("|");

            $("#tabela_itens tbody").append(
                "<tr>" +
                    "<td width='12%' class='id_animal'>" +
                    php_item[0] +
                    "</td>" +
                    "<td width='8%' class='peso_animal'>" +
                    php_item[1] +
                    "</td>" +
                    "<td width='8%' class='sexo_animal'>" +
                    php_item[2] +
                    "</td>" +
                    "<td width='8%' class='nascimento_animal'>" +
                    php_item[3] +
                    "</td>" +
                    "<td width='10%' class='raca_animal'>" +
                    php_item[4] +
                    "</td>" +
                    "<td width='8%' class='pelagem_animal'>" +
                    php_item[5] +
                    "</td>" +
                    "<td width='8%' class='mae_animal'>" +
                    php_item[6] +
                    "</td>" +
                    "<td width='18%' class='observacao'>" +
                    php_item[7] +
                    "</td>" +
                    "<td width='8%' hidden='' class='codigo_id'>" +
                    php_item[8] +
                    "</td>" +
                    "<td width='12%'><div class='btn-group btnEditar'><a class='btn' href='#'><i class='icon_pencil' title='Editar'></i></a></div><div class='btn-group btnExcluir'><a class='btn' href='#'><i class='icon_trash_alt' title='Excluir'></i></a></div></td>" +
                    "</tr>"
            );
        }
        $(".btnEditar").bind("click", modal_editar_item);
        $(".btnExcluir").bind("click", excluir_item);
        $("#itens").show();
        $("#id_animal").val("");
        $("#codigo_id").val(0);
        $("#peso_animal").val("");
        $("#observacao").val("");
        $("#descricao_animal").text("");
        $("#ultimo_peso").text("");

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
            var observacao = $(this).find(".observacao").html();
            var codigo_id = $(this).find(".codigo_id").html();
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

                var tabela_itens = valor.join("|");
                array_tabela_itens.push(tabela_itens);
                grupo_itens = array_tabela_itens.join("<|>");
            }
        });

        $("#array_itens").val(grupo_itens);
    })
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

/* chamada da rotina para enviar registro de pesagem para lixeira*/
function enviar_pesagem_lixeira($id, $local, $epoca, $opcao) {
    var opcao = $opcao;

    if (window.confirm("Confirma excluir esse registro? "+$local+" "+$epoca)) {
        $.post("excluir_pesagem.php",{ id: $id},function (valor) {
            var php = valor.split("<|>");

            if (php[0] == 9) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(php[1]);
                return;
            } else if (php[0] == 0) {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(php[1]);
                return;
            }
        });
    }
}

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

    var select = $("#epoca_pesagem_filtro").val();

    if (select != 0) {
        select = document.getElementById("epoca_pesagem_filtro");
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

// PESAGEM ON-LINE POR LOTE - TODAS AS FUNCIONALIDADES DA TELA
function iniciar_pesagem() {
    var controle_estoque = $("#controle_estoque").val();
    var local_pesagem = $("#local_pesagem").val();
    var epoca_pesagem = $("#epoca_pesagem_filtro").val();
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
                $("#epoca_pesagem").val($("#epoca_pesagem_filtro").val());
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

// Salvar item digitado na pesagem on-line por lote
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
            "<td width='8%' hidden=''class='sexo_animal'>" +
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
    gravar_pesagem_lote();

    document.getElementById("codigo_categoria").focus();
}

// Salvar item digitado na pesagem on-line por lote quando editado
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
    gravar_pesagem_lote();
}

// Edita o item digitado na pesagem on-line por lote
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


// Exclui o item digitado na pesagem on-line por lote
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
        gravar_pesagem_lote();
    }
}

// chamado do botão Finalizar Pesagem Lote on-line Inclusão e Edição
function terminar_pesagem_lote() {
    var data_pesagem = $("#data_pesagem").val();

    if (data_pesagem=='') {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe a Data da pesagem.");
        return;
    }

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
        $(".total_a_pesar").text(qtd_a_pesar);
        $(".total_a_pesar").val(qtd_a_pesar);

        $(".total_pesados").text(qtd_pesados);
        $(".total_pesados").val(qtd_pesados);

        var total_arroba = total_peso / 30;
        var total_medio = total_peso / total_pesados;
        var total_medio_arroba = total_arroba / total_pesados;

        $(".peso_total_kg").text(formatMoney(total_peso));
        $(".peso_total_kg").val(total_peso.toFixed(2));

        $(".peso_total_arroba").text(formatMoney(total_arroba));
        $(".peso_total_arroba").val(total_arroba.toFixed(2));

        $(".peso_medio_kg").text(formatMoney(total_medio));
        $(".peso_medio_kg").val(total_medio.toFixed(2));

        $(".peso_medio_arroba").text(formatMoney(total_medio_arroba));
        $(".peso_medio_arroba").val(total_medio_arroba.toFixed(2));

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

        $(".total_a_pesar").text("");
        $(".total_a_pesar").val("");

        $(".total_pesados").text('');
        $(".total_pesados").val('');
        $(".peso_total_kg").text('');
        $(".peso_total_kg").val('');

        $(".peso_total_arroba").text('');
        $(".peso_total_arroba").val('');

        $(".peso_medio_kg").text('');
        $(".peso_medio_kg").val('');

        $(".peso_medio_arroba").text('');
        $(".peso_medio_arroba").val('');
        $(".botoes_final").hide();
    }

    var data = $("#data_pesagem").val();

    var dia = data.split("-")[2];
    var mes = data.split("-")[1];
    var ano = data.split("-")[0];

    var str_data =
        ("0" + dia).slice(-2) + "/" + ("0" + mes).slice(-2) + "/" + ano;

    var descricao_lote = $("#lote_pesagem_lote").val();

    $(".descricao_lote_lote").text(descricao_lote);
    $(".descricao_lote").val(descricao_lote);
    $(".data_pesagem").val(data);
}

// FIM PESAGEM ON-LINE POR LOTE


// Confirma finalizar pesagem on-line Inclusão e Edição
/*function terminar_pesagem() {
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
}*/

// ROTINAS DA PESAGEM OF-LINE (EXCEL) POR LOTE

// funcao chamada pelo botão Finalizar Pesagem  

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

function imprimir_pesagem_excel_lote(num_pesagem, filtro_pesagem, filtro_local, filtro_epoca) {
    location.href='rel_exportar_pesagem_excel_lote_imprimir.php?desc_filtro=' + filtro_pesagem + 
    '&num_pesagem=' + num_pesagem + '&desc_local=' + filtro_local + '&desc_epoca=' + filtro_epoca;
    tout = setTimeout("finalizar_sair()", 3000);
}


// FIM ROTINAS DA PESAGEM OF-LINE (EXCEL)

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
    if (window.confirm("Confirma sair sem Finalizar?")) {
        location.href = "form_pesagem_animais.php";
    }
}




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
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $("#tabela_itens_consulta").DataTable({
        responsive: true,
        paging: false,
        ordering: true,
        info: true,
        //order: [[ 0, "desc" ]],
        language: {
            sSearch: "Busca:",
            zeroRecords: "Nada encontrado",
            info: "Registros: _END_ ",
            infoEmpty: "Nenhum registro disponível",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
        },
        columnDefs: [
            { type: 'date-br', targets: 3 }
        
        ],
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $("#tabela_itens_editar").DataTable({
        responsive: true,
        paging: false,
        ordering: true,
        info: true,
        //order: [[ 0, "desc" ]],
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
        initComplete: function () {
            $("table.dataTable").css("width", "100%");
        },
    });

    $("#local_pesagem").change(function () {
        $("#codigo_local_filtro").val("");

        var local = $("#local_pesagem").val();
        $("#codigo_local_filtro").val(local);

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
    });

    $("#epoca_pesagem_filtro").change(function () {
        $("#epoca_pesagem").val("");

        var epoca = $("#epoca_pesagem_filtro").val();
        $("#epoca_pesagem").val(epoca);

        var select = $("#epoca_pesagem_filtro").val();

        if (select != 0) {
            select = document.getElementById("epoca_pesagem_filtro");
            epoca = select.options[select.selectedIndex].text;
            $("#descricao_epoca").val(epoca);
        }

        exibe_filtro_lote();
    });

    $("#epoca_pesagem").change(function () {
        $("#epoca_pesagem_filtro").val("");

        var epoca = $("#epoca_pesagem").val();
        $("#epoca_pesagem_filtro").val(epoca);

        var select = $("#epoca_pesagem").val();

        if (select != 0) {
            select = document.getElementById("epoca_pesagem");
            epoca = select.options[select.selectedIndex].text;
            $("#descricao_epoca").val(epoca);
        }
        exibe_filtro_lote();
    });

    $("#pasto").change(function () {
        $("#alert_erro_estimado .negrito").html("");
        $("#alert_erro_estimado span").html("");
        $(".alert_erro_estimado").hide();

        var select = $("#pasto").val();

        if (select != null) {
            var options = $("#pasto option:selected");
            var pastos = [];

            $(options).each(function () {
                var desc = $(this).bind("#pasto").text();
                pastos.push(desc.trim());
            });

            $("#descricao_pasto").val(pastos);
        }
        else {
            $("#descricao_pasto").val('Todos');
        }

        exibe_filtro_lote();
    });

    $("#descricao_lote").change(function () {
        $("#lote_pesagem_lote").val($("#descricao_lote").val());
    });

    $("#categoria_filtro").change(function () {
        exibe_filtro_lote();
    });

    $("#macho").click(function () {
        exibe_filtro_lote();
    });

    $("#femea").click(function () {
        exibe_filtro_lote();
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
function gravar_pesagem() {
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
        var observacao = $(this).find(".observacao").html();
        var codigo_id = $(this).find(".codigo_id").html();
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

            var tabela_itens = valor.join("|");
            array_tabela_itens.push(tabela_itens);
            grupo_itens = array_tabela_itens.join("<|>");
        }
    });

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
            } else if (data.success) {
                $("#numero_pesagem_id").val(data.numero_doc);
                $("#tipo_gravacao").val(2);
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

    var dados = $("#form_gravar_pesagem").serialize();

    alert (dados);

    $.ajax({
        type: "POST",
        url: "gravar_pesagem_lote.php",
        data: dados,
        success: function (data) {
            alert (data.error);
            alert (data.success);

            if (data.error) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(data.message);
            } else if (data.success) {
                //$("#mensagem_sair_retorno").modal();
                //$("#mensagem_sair_retorno .modal-body").html("Pesagem finalizada com sucesso.");
                $("#numero_pesagem_id").val(data.numero_doc);
                $("#tipo_gravacao").val(2);
            }
        },
    });
}

function listar_grupo_destino(id_pesagem) {
    $("#id_pesagem").val(id_pesagem);

    $.post("lista_grupo_pesagem_lote.php", {id_pesagem: id_pesagem}, function (valor) {

        $("div[id=lista_grupos]").html(valor);
        $("#modal_listar_grupo_destino").modal("show");
    });
}

function exportar_excel_pesagem() {
    let dataObj = {};
    var desc_filtro = $("#descricao_filtro").val();
    var data_pesagem = $("#data_pesagem").val();
    var local = $("#local_pesagem").val();
    var epoca = $("#epoca_pesagem").val();
    var pasto = $("#pasto").val();
    var categoria = $("#categoria_filtro").val();

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