/** VALIDAR CLIENTE BOI VIRTUAL*/
window.addEventListener("load", function (event) {
    $(document).ready(function () {
        $("#tabela_clientes").DataTable({
            paging: false,
            ordering: false,
            info: true,
            language: {
                sSearch: "Buscar na lista:",
                zeroRecords: "Nada encontrado",
                info: "Registros encontrados: _END_ ",
                infoEmpty: "Nenhum registro disponível",
                infoFiltered: "(filtrado de _MAX_ registros no total)",
            },
        });

        $(".sair_programa").click(function () {
            location.href = "form_cliente_boi_virtual.php";
        });

        $("#nome_fantasia").dblclick(function () {
            var nome = $("#nome_empresa").val();
            $("#nome_fantasia").val(nome);
        });
    });

    $("#cpf_adm").dblclick(function () {
        var cpf_cnpj = $("#cpf_cnpj").val();
        $("#cpf_adm").val(cpf_cnpj);
    });

    $("#nome_empresa").click(function () {
        document.getElementById("nome_empresa").style.borderColor = "";
    });

    $("#nome_fantasia").click(function () {
        document.getElementById("nome_fantasia").style.borderColor = "";
    });

    $("#cpf_cnpj").click(function () {
        document.getElementById("cpf_cnpj").style.borderColor = "";
    });

    $("#cep_pessoa").click(function () {
        document.getElementById("cep_pessoa").style.borderColor = "";
    });

    $("#nome_fazenda_01").click(function () {
        document.getElementById("nome_fazenda_01").style.borderColor = "";
    });

    $("#cpf_cnpj_01").click(function () {
        document.getElementById("cpf_cnpj_01").style.borderColor = "";
    });

    $("#cep_01").click(function () {
        document.getElementById("cep_01").style.borderColor = "";
    });

    $("#nome_fazenda_02").click(function () {
        document.getElementById("nome_fazenda_02").style.borderColor = "";
    });

    $("#cpf_cnpj_02").click(function () {
        document.getElementById("cpf_cnpj_02").style.borderColor = "";
    });
    $("#cep_02").click(function () {
        document.getElementById("cep_02").style.borderColor = "";
    });

    $("#nome_fazenda_03").click(function () {
        document.getElementById("nome_fazenda_03").style.borderColor = "";
    });

    $("#cpf_cnpj_03").click(function () {
        document.getElementById("cpf_cnpj_03").style.borderColor = "";
    });

    $("#cep_03").click(function () {
        document.getElementById("cep_03").style.borderColor = "";
    });

    $("#nome_fazenda_04").click(function () {
        document.getElementById("nome_fazenda_04").style.borderColor = "";
    });

    $("#cpf_cnpj_04").click(function () {
        document.getElementById("cpf_cnpj_04").style.borderColor = "";
    });

    $("#cep_04").click(function () {
        document.getElementById("cep_04").style.borderColor = "";
    });

    $("#nome_fazenda_05").click(function () {
        document.getElementById("nome_fazenda_05").style.borderColor = "";
    });

    $("#cpf_cnpj_05").click(function () {
        document.getElementById("cpf_cnpj_05").style.borderColor = "";
    });

    $("#cep_05").click(function () {
        document.getElementById("cep_05").style.borderColor = "";
    });

    $("#nome_adm").click(function () {
        document.getElementById("nome_adm").style.borderColor = "";
    });

    $("#cpf_adm").click(function () {
        document.getElementById("cpf_adm").style.borderColor = "";
    });

    $("#email_adm").click(function () {
        document.getElementById("email_adm").style.borderColor = "";
    });

    $("#ddd_adm").click(function () {
        document.getElementById("ddd_adm").style.borderColor = "";
    });

    $("#telefone_adm").click(function () {
        document.getElementById("telefone_adm").style.borderColor = "";
    });

    $("#cep_pessoa").blur(function () {
        var cep = $(this).val().replace(/\D/g, "");
        if (cep != "") {
            var validacep = /^[0-9]{8}$/;
            if (validacep.test(cep)) {
                $("#endereco_pessoa").val("...");
                $("#num_pessoa").val("...");
                $("#complemento_pessoa").val("...");
                $("#bairro_pessoa").val("...");
                $("#cidade_pessoa").val("...");
                $("#estado_pessoa").val("");
                $.getJSON(
                    "https://viacep.com.br/ws/" + cep + "/json/?callback=?",
                    function (dados) {
                        if (!("erro" in dados)) {
                            $("#endereco_pessoa").val(
                                dados.logradouro.toUpperCase()
                            );
                            $("#num_pessoa").val("");
                            $("#complemento_pessoa").val("");
                            $("#bairro_pessoa").val(dados.bairro.toUpperCase());
                            $("#cidade_pessoa").val(
                                dados.localidade.toUpperCase()
                            );
                            $("#estado_pessoa").val(dados.uf);
                            //$("#ibge").val(dados.ibge);

                            $("select[name=lista_municipio]").html(
                                '<option value="">Carregando...</option>'
                            );

                            $.post(
                                "lista_municipios.php",
                                { estado: dados.uf },
                                function (valor) {
                                    $("select[name=lista_municipio]").html(
                                        valor
                                    );
                                }
                            );

                            $("#num_pessoa").focus();
                        } else {
                            limpa_formulário_cep();
                            alert("CEP não encontrado.");
                        }
                    }
                );
            } else {
                limpa_formulário_cep();
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html(
                    "Formato de CEP inválido."
                );
            }
        } else {
            limpa_formulário_cep();
        }
    });

    $("select[name=estado_pessoa]").change(function () {
        $("select[name=lista_municipio]").html(
            '<option value="">Aguarde...</option>'
        );
        $("#cidade_pessoa").val("");
        var estado = $(this).val();

        $.post("lista_municipios.php", { estado: estado }, function (valor) {
            $("select[name=lista_municipio]").html(valor);
        });

        //tout = setTimeout('exibe_cidade()', 1000);
    });

    $("#lista_municipio").change(function () {
        var municipio_selecioando = $("#lista_municipio").val();
        $("#cidade_pessoa").val(municipio_selecioando);
        $("#lista_municipio").val("");
    });
});

function gravar_alteracao(opcao) {
    var nome_empresa = $("#nome_empresa").val();

    $("#opcao_validar").val(opcao);

    if (nome_empresa == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Informe o Nome da Empresa ou Produtor."
        );
        $("#nome_empresa").focus();
        document.getElementById("nome_empresa").style.borderColor = "#FF0000";
        return;
    }

    var nome_fantasia = $("#nome_fantasia").val();

    if (nome_fantasia == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe o Nome Fantasia.");
        $("#nome_fantasia").focus();
        document.getElementById("nome_fantasia").style.borderColor = "#FF0000";
        return;
    }

    var cpf_cnpj = $("#cpf_cnpj").val();

    if (cpf_cnpj == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Informe o CPF ou CNPJ da Empresa."
        );
        $("#cpf_cnpj").focus();
        document.getElementById("cpf_cnpj").style.borderColor = "#FF0000";
        return;
    }

    var cep_pessoa = $("#cep_pessoa").val();

    if (cep_pessoa == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe o CEP da Empresa.");
        $("#cep_pessoa").focus();
        document.getElementById("cep_pessoa").style.borderColor = "#FF0000";
        return;
    }

    var nome_fazenda_01 = $("#nome_fazenda_01").val();

    if (nome_fazenda_01 == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe o Nome da Fazenda 01.");
        $("#nome_fazenda_01").focus();
        document.getElementById("nome_fazenda_01").style.borderColor =
            "#FF0000";
        return;
    }

    var cpf_cnpj_01 = $("#cpf_cnpj_01").val();

    if (cpf_cnpj_01 == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Informe o CPF ou CNPJ da Fazenda 01."
        );
        $("#cpf_cnpj_01").focus();
        document.getElementById("cpf_cnpj_01").style.borderColor = "#FF0000";
        return;
    }

    var cep_01 = $("#cep_01").val();

    if (cep_01 == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe o CEP da Fazenda 01.");
        $("#cep_01").focus();
        document.getElementById("cep_01").style.borderColor = "#FF0000";
        return;
    }

    var nome_adm = $("#nome_adm").val();

    if (nome_adm == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Informe o Nome do Administrador."
        );
        $("#nome_adm").focus();
        document.getElementById("nome_adm").style.borderColor = "#FF0000";
        return;
    }

    var cpf_adm = $("#cpf_adm").val();

    if (cpf_adm == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe o CPF do Administrador.");
        $("#cpf_adm").focus();
        document.getElementById("cpf_adm").style.borderColor = "#FF0000";
        return;
    }

    var email_adm = $("#email_adm").val();

    if (email_adm == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Informe o E-mail do Administrador."
        );
        $("#email_adm").focus();
        document.getElementById("email_adm").style.borderColor = "#FF0000";
        return;
    }

    var ddd_adm = $("#ddd_adm").val();

    if (ddd_adm == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("Informe o DDD do Administrador.");
        $("#ddd_adm").focus();
        document.getElementById("ddd_adm").style.borderColor = "#FF0000";
        return;
    }

    var telefone_adm = $("#telefone_adm").val();

    if (telefone_adm == "") {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "Informe o Telefone do Administrador."
        );
        $("#telefone_adm").focus();
        document.getElementById("telefone_adm").style.borderColor = "#FF0000";
        return;
    }

    var dados = $("#form_gravar_cliente").serialize();
    $.ajax({
        type: "POST",
        url: "gravar_cliente_boi_virtual.php",
        data: dados,
        success: function (data) {
            if (data.error) {
                if (data.error == true) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                } else {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.error);
                }
            } else {
                $("#mensagem_retorno").modal();
                $("#mensagem_retorno .modal-body").html(data.message);
            }
        },
    });
}

function limpa_formulário_cep() {
    $("#endereco_pessoa").val("");
    $("#num_pessoa").val("");
    $("#complemento_pessoa").val("");
    $("#bairro_pessoa").val("");
    $("#cidade_pessoa").val("");
    $("#estado_pessoa").val("");
}

function limpa_formulário_cep_01() {
    $("#endereco_01").val("");
    $("#num_01").val("");
    $("#complemento_01").val("");
    $("#bairro_01").val("");
    $("#cidade_01").val("");
    $("#estado_01").val("");
}

// a função principal de validação CPF e CNPJ
function valida_cpf_cnpj(obj) {
    // recebe um objeto
    var s = obj.value.replace(/\D/g, "");

    if (s == "") {
        return false;
    }

    var tam = s.length; // removendo os caracteres não numéricos
    if (!(tam == 11 || tam == 14)) {
        // validando o tamanho
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html(
            "'" + s + "' Não é um CPF ou um CNPJ válido!"
        );

        // alert("'"+s+"' Não é um CPF ou um CNPJ válido!" ); // tamanho inválido
        document.getElementById("cpf_cnpj").value = "";
        document.getElementById("cpf_cnpj").focus();
        return;
    }

    if (tam == 11) {
        if (!validaCPF(s)) {
            // chama a função que valida o CPF
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "'" + s + "' Não é um código CPF válido!"
            );
            //alert("'"+s+"' Não é um código CPF válido!" ); // se quiser mostrar o erro
            document.getElementById("cpf_cnpj").value = "";
            document.getElementById("cpf_cnpj").focus();
            return false;
        } else {
            obj.value = maskCPF(s); // se validou o CPF mascaramos corretamente
            return true;
        }
    } else if (tam == 14) {
        if (!validaCNPJ(s)) {
            // chama a função que valida o CNPJ
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html(
                "'" + s + "' Não é um código CNPJ válido!"
            );
            //alert("'"+s+"' Não é um código CNPJ válido!" ); // se quiser mostrar o erro
            document.getElementById("cpf_cnpj").value = "";
            document.getElementById("cpf_cnpj").focus();
            return false;
        } else {
            obj.value = maskCNPJ(s); // se validou o CNPJ mascaramos corretamente
            return true;
        }
    } else {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html("CPF/CNPJ Inválido!");
        //alert("CPF/CNPJ Inválido");
        document.getElementById("cpf_cnpj").value = "";
        document.getElementById("cpf_cnpj").focus();
        return false;
    }
}
// fim da funcao valida_cpf_cnpj()

// função que valida CPF
function validaCPF(s) {
    var c = s.substr(0, 9);
    var dv = s.substr(9, 2);
    var d1 = 0;
    for (var i = 0; i < 9; i++) {
        d1 += c.charAt(i) * (10 - i);
    }
    if (d1 == 0) return false;
    d1 = 11 - (d1 % 11);
    if (d1 > 9) d1 = 0;
    if (dv.charAt(0) != d1) {
        return false;
    }
    d1 *= 2;
    for (var i = 0; i < 9; i++) {
        d1 += c.charAt(i) * (11 - i);
    }
    d1 = 11 - (d1 % 11);
    if (d1 > 9) d1 = 0;
    if (dv.charAt(1) != d1) {
        return false;
    }
    return true;
}

// Função que valida CNPJ
function validaCNPJ(CNPJ) {
    var a = new Array();
    var b = new Number();
    var c = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    for (i = 0; i < 12; i++) {
        a[i] = CNPJ.charAt(i);
        b += a[i] * c[i + 1];
    }
    if ((x = b % 11) < 2) {
        a[12] = 0;
    } else {
        a[12] = 11 - x;
    }
    b = 0;
    for (y = 0; y < 13; y++) {
        b += a[y] * c[y];
    }
    if ((x = b % 11) < 2) {
        a[13] = 0;
    } else {
        a[13] = 11 - x;
    }
    if (CNPJ.charAt(12) != a[12] || CNPJ.charAt(13) != a[13]) {
        return false;
    }
    return true;
}

//  função que mascara o CPF
function maskCPF(CPF) {
    var cpf_cnpj = CPF;
    cpf_cnpj_editado =
        cpf_cnpj.substring(0, 3) +
        "." +
        cpf_cnpj.substring(3, 6) +
        "." +
        cpf_cnpj.substring(6, 9) +
        "-" +
        cpf_cnpj.substring(9, 11);

    return cpf_cnpj_editado;
}

//  função que mascara o CPF de registros lidos do banco de dados
function maskCPFA(CPF) {
    return (
        CPF.substring(3, 6) +
        "." +
        CPF.substring(6, 9) +
        "." +
        CPF.substring(9, 12) +
        "-" +
        CPF.substring(12, 14)
    );
}

//  função que mascara o CNPJ
function maskCNPJ(CNPJ) {
    return (
        CNPJ.substring(0, 2) +
        "." +
        CNPJ.substring(2, 5) +
        "." +
        CNPJ.substring(5, 8) +
        "/" +
        CNPJ.substring(8, 12) +
        "-" +
        CNPJ.substring(12, 14)
    );
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

function digita_valor() {
    $("#area_01").bind("keypress", mask.money);
    $("#area_util_01").bind("keypress", mask.money);
}

function exibe_area_01() {
    var area_01 = $("#area_01").val();
    if (verifica_virgula(area_01) == ",") {
        area_01 = replace_valor(area_01);
    }

    $("#area_01").val(formatMoney(area_01));
}

function exibe_area_util_01() {
    var area_util_01 = $("#area_util_01").val();
    if (verifica_virgula(area_util_01) == ",") {
        area_util_01 = replace_valor(area_util_01);
    }

    $("#area_util_01").val(formatMoney(area_util_01));
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
