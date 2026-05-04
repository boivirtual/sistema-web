/**OPCOES DE MENU*/
/** exibe acessos de menus conforme grupo do usuario*/

$(window).on("load", function () {
    var array_manejo_animais = $("#array_manejo_animais").val().split("!");
    var array_manejo_reprodutivo = $("#array_manejo_reprodutivo")
        .val()
        .split("!");
    var array_suplemento_alimentar = $("#array_suplemento_alimentar")
        .val()
        .split("!");
    var array_controle_sanitario = $("#array_controle_sanitario")
        .val()
        .split("!");
    var array_gestao_adm = $("#array_gestao_adm").val().split("!");
    var array_cadastro = $("#array_cadastro").val().split("!");
    var array_parametros = $("#array_parametros").val().split("!");
    var array_relatorios = $("#array_relatorios").val().split("!");

    if (array_manejo_animais[0] == 1) {
        $("#opc_manejo").show();
        $("#opc_manejo_d").hide();
    } else {
        $("#opc_manejo").hide();
        $("#opc_manejo_d").show();
    }
    if (array_manejo_animais[1] == 1) {
        $("#opc_pesagem").show();
        $("#opc_pesagem_d").hide();
    } else {
        $("#opc_pesagem").hide();
        $("#opc_pesagem_d").show();
    }
    if (array_suplemento_alimentar[1] == 1) {
        $("#opc_nutricao").show();
        $("#opc_nutricao_d").hide();
    } else {
        $("#opc_nutricao").hide();
        $("#opc_nutricao_d").show();
    }
    if (array_controle_sanitario[1] == 1) {
        $("#opc_sanidade").show();
        $("#opc_sanidade_d").hide();
    } else {
        $("#opc_sanidade").hide();
        $("#opc_sanidade_d").show();
    }
    if (array_manejo_reprodutivo[1] == 1) {
        $("#opc_reproducao").show();
        $("#opc_reproducao_d").hide();
    } else {
        $("#opc_reproducao").hide();
        $("#opc_reproducao_d").show();
    }

    if (array_manejo_animais[0] == 1) {
        $("#opc101").show();
        $("#opc101d").hide();
    } else {
        $("#opc101").hide();
        $("#opc101d").show();
    }
    if (array_manejo_animais[1] == 1) {
        $("#opc102").show();
        $("#opc102d").hide();
    } else {
        $("#opc102").hide();
        $("#opc102d").show();
    }
    if (array_manejo_animais[2] == 1) {
        $("#opc103").show();
        $("#opc103d").hide();
    } else {
        $("#opc103").hide();
        $("#opc103d").show();
    }
    if (array_manejo_animais[3] == 1) {
        $("#opc104").show();
        $("#opc104d").hide();
    } else {
        $("#opc104").hide();
        $("#opc104d").show();
    }
    //if (array_manejo_animais[4]==1){$('#opc105').show();$('#opc105d').hide();}else{$('#opc105').hide();$('#opc105d').show();}
    //    if (array_manejo_animais[5]==1){$('#opc106').show();$('#opc106d').hide();}else{$('#opc106').hide();$('#opc106d').show();}

    if (array_manejo_animais[2] == 1) {
        $("#opc_compra_venda").show();
        $("#opc_compra_venda_d").hide();
    } else {
        $("#opc_compra_venda").hide();
        $("#opc_compra_venda_d").show();
    }

    if (array_manejo_reprodutivo[0] == 1) {
        $("#opc201").show();
        $("#opc201d").hide();
    } else {
        $("#opc201").hide();
        $("#opc201d").show();
    }
    if (array_manejo_reprodutivo[1] == 1) {
        $("#opc202").show();
        $("#opc202d").hide();
    } else {
        $("#opc202").hide();
        $("#opc202d").show();
    }
    if (array_manejo_reprodutivo[2] == 1) {
        $("#opc203").show();
        $("#opc203d").hide();
    } else {
        $("#opc203").hide();
        $("#opc203d").show();
    }
    if (array_manejo_reprodutivo[3] == 1) {
        $("#opc204").show();
        $("#opc204d").hide();
    } else {
        $("#opc204").hide();
        $("#opc204d").show();
    }

    if (array_suplemento_alimentar[0] == 1) {
        $("#opc301").show();
        $("#opc301d").hide();
    } else {
        $("#opc301").hide();
        $("#opc301d").show();
    }
    if (array_suplemento_alimentar[1] == 1) {
        $("#opc302").show();
        $("#opc302d").hide();
    } else {
        $("#opc302").hide();
        $("#opc302d").show();
    }

    if (array_controle_sanitario[0] == 1) {
        $("#opc401").show();
        $("#opc401d").hide();
    } else {
        $("#opc401").hide();
        $("#opc401d").show();
    }
    if (array_controle_sanitario[1] == 1) {
        $("#opc402").show();
        $("#opc402d").hide();
    } else {
        $("#opc402").hide();
        $("#opc402d").show();
    }

    if (array_gestao_adm[0] == 1) {
        $("#opc501").show();
        $("#opc501d").hide();
    } else {
        $("#opc501").hide();
        $("#opc501d").show();
    }
    if (array_gestao_adm[1] == 1) {
        $("#opc502").show();
        $("#opc502d").hide();
    } else {
        $("#opc502").hide();
        $("#opc502d").show();
    }
    if (array_gestao_adm[2] == 1) {
        $("#opc503").show();
        $("#opc503d").hide();
    } else {
        $("#opc503").hide();
        $("#opc503d").show();
    }
    if (array_gestao_adm[3] == 1) {
        $("#opc504").show();
        $("#opc504d").hide();
    } else {
        $("#opc504").hide();
        $("#opc504d").show();
    }
    if (array_gestao_adm[4] == 1) {
        $("#opc505").show();
        $("#opc505d").hide();
    } else {
        $("#opc505").hide();
        $("#opc505d").show();
    }
    if (array_gestao_adm[5] == 1) {
        $("#opc506").show();
        $("#opc506d").hide();
    } else {
        $("#opc506").hide();
        $("#opc506d").show();
    }
    if (array_gestao_adm[6] == 1) {
        $("#opc507").show();
        $("#opc507d").hide();
    } else {
        $("#opc507").hide();
        $("#opc507d").show();
    }

    if (array_cadastro[0] == 1) {
        $("#opc701").show();
        $("#opc701d").hide();
    } else {
        $("#opc701").hide();
        $("#opc701d").show();
    }
    if (array_cadastro[1] == 1) {
        $("#opc702").show();
        $("#opc702d").hide();
    } else {
        $("#opc702").hide();
        $("#opc702d").show();
    }
    if (array_cadastro[2] == 1) {
        $("#opc703").show();
        $("#opc703d").hide();
    } else {
        $("#opc703").hide();
        $("#opc703d").show();
    }
    if (array_cadastro[3] == 1) {
        $("#opc704").show();
        $("#opc704d").hide();
    } else {
        $("#opc704").hide();
        $("#opc704d").show();
    }
    if (array_cadastro[4] == 1) {
        $("#opc705").show();
        $("#opc705d").hide();
    } else {
        $("#opc705").hide();
        $("#opc705d").show();
    }
    if (array_cadastro[5] == 1) {
        $("#opc706").show();
        $("#opc706d").hide();
    } else {
        $("#opc706").hide();
        $("#opc706d").show();
    }
    if (array_cadastro[6] == 1) {
        $("#opc707").show();
        $("#opc707d").hide();
    } else {
        $("#opc707").hide();
        $("#opc707d").show();
    }

    if (array_parametros[0] == 1) {
        $("#opc800").show();
        $("#opc800d").hide();
    } else {
        $("#opc800").hide();
        $("#opc800d").show();
    }
    if (array_parametros[1] == 1) {
        $("#opc801").show();
        $("#opc801d").hide();
    } else {
        $("#opc801").hide();
        $("#opc801d").show();
    }
    if (array_parametros[2] == 1) {
        $("#opc802").show();
        $("#opc802d").hide();
    } else {
        $("#opc802").hide();
        $("#opc802d").show();
    }
    if (array_parametros[3] == 1) {
        $("#opc803").show();
        $("#opc803d").hide();
    } else {
        $("#opc803").hide();
        $("#opc803d").show();
    }
    if (array_parametros[4] == 1) {
        $("#opc804").show();
        $("#opc804d").hide();
    } else {
        $("#opc804").hide();
        $("#opc804d").show();
    }
    if (array_parametros[5] == 1) {
        $("#opc805").show();
        $("#opc805d").hide();
    } else {
        $("#opc805").hide();
        $("#opc805d").show();
    }
    if (array_parametros[6] == 1) {
        $("#opc806").show();
        $("#opc806d").hide();
    } else {
        $("#opc806").hide();
        $("#opc806d").show();
    }
    if (array_parametros[7] == 1) {
        $("#opc807").show();
        $("#opc807d").hide();
    } else {
        $("#opc807").hide();
        $("#opc807d").show();
    }
    if (array_parametros[8] == 1) {
        $("#opc808").show();
        $("#opc808d").hide();
    } else {
        $("#opc808").hide();
        $("#opc808d").show();
    }
    if (array_parametros[9] == 1) {
        $("#opc809").show();
        $("#opc809d").hide();
    } else {
        $("#opc809").hide();
        $("#opc809d").show();
    }
    if (array_parametros[10] == 1) {
        $("#opc810").show();
        $("#opc810d").hide();
    } else {
        $("#opc810").hide();
        $("#opc810d").show();
    }
    if (array_parametros[11] == 1) {
        $("#opc811").show();
        $("#opc811d").hide();
    } else {
        $("#opc811").hide();
        $("#opc811d").show();
    }
    if (array_parametros[12] == 1) {
        $("#opc812").show();
        $("#opc812d").hide();
    } else {
        $("#opc812").hide();
        $("#opc812d").show();
    }
    if (array_parametros[13] == 1) {
        $("#opc813").show();
        $("#opc813d").hide();
    } else {
        $("#opc813").hide();
        $("#opc813d").show();
    }
    if (array_parametros[14] == 1) {
        $("#opc814").show();
        $("#opc814d").hide();
    } else {
        $("#opc814").hide();
        $("#opc814d").show();
    }
    if (array_parametros[15] == 1) {
        $("#opc815").show();
        $("#opc815d").hide();
    } else {
        $("#opc815").hide();
        $("#opc815d").show();
    }
    if (array_parametros[16] == 1) {
        $("#opc816").show();
        $("#opc816d").hide();
    } else {
        $("#opc816").hide();
        $("#opc816d").show();
    }
    if (array_parametros[17] == 1) {
        $("#opc817").show();
        $("#opc817d").hide();
    } else {
        $("#opc817").hide();
        $("#opc817d").show();
    }
    if (array_parametros[18] == 1) {
        $("#opc818").show();
        $("#opc818d").hide();
    } else {
        $("#opc818").hide();
        $("#opc818d").show();
    }
    if (array_parametros[19] == 1) {
        $("#opc819").show();
        $("#opc819d").hide();
    } else {
        $("#opc819").hide();
        $("#opc819d").show();
    }
    if (array_parametros[20] == 1) {
        $("#opc820").show();
        $("#opc820d").hide();
    } else {
        $("#opc820").hide();
        $("#opc820d").show();
    }
    if (array_parametros[21] == 1) {
        $("#opc821").show();
        $("#opc821d").hide();
    } else {
        $("#opc821").hide();
        $("#opc821d").show();
    }
    if (array_parametros[22] == 1) {
        $("#opc822").show();
        $("#opc822d").hide();
    } else {
        $("#opc822").hide();
        $("#opc822d").show();
    }

    if (array_relatorios[0] == 1) {
        $("#opc901").show();
        $("#opc901d").hide();
    } else {
        $("#opc901").hide();
        $("#opc901d").show();
    }
    if (array_relatorios[1] == 1) {
        $("#opc902").show();
        $("#opc902d").hide();
    } else {
        $("#opc902").hide();
        $("#opc902d").show();
    }
    if (array_relatorios[2] == 1) {
        $("#opc903").show();
        $("#opc903d").hide();
    } else {
        $("#opc903").hide();
        $("#opc903d").show();
    }

    lista_contatos();
});

/** Carrega lista de contatos clientes/fornecedores para o script form_cliente-fornecedor_editar.php*/
function lista_contatos() {
    id_cliente_fornecedor = $("#codigo_pessoa").val();
    $("#outros_contatos").load(
        "form_contatos_clientes_fornecedores.php?editar=true&id=" +
            id_cliente_fornecedor
    );
    return;
}

$(window).resize(function () {
    if (window.innerWidth <= 768) {
        $("#sidebar").hide();
        $("#abre_sidebar").click(function () {
            $("#sidebar").show();
        });
    } else {
        $("#sidebar").show();
        $("#abre_sidebar").unbind("click");
    }
});

$(document).ready(function () {
    if (window.innerWidth <= 768) {
        $("#sidebar").hide();
        $("#abre_sidebar").click(function () {
            $("#sidebar").show();
        });
    } else {
        $("#sidebar").show();
        $("#abre_sidebar").unbind("click");
    }
});

function ler_ajuda(){
    var descricao=$("#nome_pesquisa").val();
    //alert (descricao);
}