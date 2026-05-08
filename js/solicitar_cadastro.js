const firstForm = document.getElementById("firstForm");
const secondForm = document.getElementById("secondForm");
const thirdForm = document.getElementById("thirdForm");

const submitFirstForm = document.getElementById("submitFirstForm");
const submitSecondForm = document.getElementById("submitSecondForm");
const submitThirdForm = document.getElementById("submitThirdForm");

const thirdFormTitle = document.getElementById("fazenda_titulo");

const empresa_produtor_info = document.getElementById("empresa_produtor_info");
const userp_info = document.getElementById("userp_info");
const fazenda_info = document.getElementById("fazenda_info");
const fazenda_edit = document.getElementById("fazenda_edit");

const edit_produtor_info = document.getElementById("edit_produtor_info");
const edit_userp_info = document.getElementById("edit_userp_info");
const edit_fazenda_info = document.getElementById("edit_fazenda_info");

const modal_edit = $("#edit_fazenda_modal");
const select_edit = $("#s_fazenda_edit");
const button_fazenda_edit = document.getElementById("select_fazenda_edit");

const div_enviar_formulario = document.getElementById("div_enviar_formulario");

const checks = {
    empresaR: false,
    userpR: false,
    fazendaR: false,
};

let firstFormData = new FormData();
let secondFormData = new FormData();
let isEditingThirdForm = {
    isEditing: false,
    index: 0,
};
const thirdFormData = [];
const elementsArray = [];

let fazendaCounter = 0;
let fazendaAux = 0;

submitFirstForm.addEventListener("click", (e) => {
    e.preventDefault();

    firstFormData = new FormData(firstForm);

    let radio = $("#firstForm").find("input[type=radio]");
    $.each(radio, function (key, val) {
        if (!firstFormData.has(val.name)) {
            firstFormData.append($(val).attr("name"), $(val).is(":checked"));
        }
    });

    const check = checkEmptyFirstForm(firstFormData);

    if (check.error) {
        $("#mensagem_erro").modal("show");
        $("#log_error").html(check.message);
        return;
    }

    let control = "";

    if (firstFormData.get("controle_estoque") === "I") {
        control = "Controle Individual";
    } else {
        control = "Controle por Lote";
    }

    document.getElementById("nomeCpfProdutor").innerHTML =
        firstFormData.get("nome_empresa") +
        " - " +
        firstFormData.get("cpf_cnpj");

    document.getElementById("enderecoProdutor").innerHTML =
        firstFormData.get("endereco_pessoa") +
        ", " +
        firstFormData.get("num_pessoa") +
        " " +
        firstFormData.get("complemento_pessoa");

    document.getElementById("cepProdutor").innerHTML =
        "CEP " + firstFormData.get("cep_pessoa");

    document.getElementById("BCEProdutor").innerHTML =
        firstFormData.get("bairro_pessoa") +
        " - " +
        firstFormData.get("cidade_pessoa") +
        " - " +
        firstFormData.get("estado_pessoa");

    document.getElementById("controlTypeFazendas").innerHTML =
        control + " - " + firstFormData.get("qtdeFazenda") + " Fazendas";

    empresa_produtor_info.style = "display: block";
    empresa_produtor_info.style = "background-color: rgb(250,250,250)";

    firstForm.style.display = "none";

    edit_userp_info.style = "display: block";
    edit_userp_info.style = "margin-top: 1em";

    edit_fazenda_info.style = "display: block";
    edit_fazenda_info.style = "margin-top: 1em";

    if (firstFormData.get("cpf_cnpj").length == 14) {
        document.getElementById("userp_cpf").value =
            firstFormData.get("cpf_cnpj");
        document.getElementById("userp_nome").value =
            firstFormData.get("nome_empresa");
    }
    document.getElementById("fazenda_cpf_cnpj").value =
        firstFormData.get("cpf_cnpj");

    if (fazendaAux != firstFormData.get("qtdeFazenda")) {
        fazendaAux = firstFormData.get("qtdeFazenda");
    }

    checks.empresaR = true;

    if (secondFormData.has("userp_cpf") && fazendaCounter < fazendaAux) {
        thirdForm.style.display = "block";

        thirdFormTitle.innerHTML = "Fazenda 0" + (fazendaCounter + 1);

        $("#nome_fazenda").focus();

        clearThirdForm();

        return;
    } else if (secondFormData.has("userp_cpf") && fazendaCounter > fazendaAux) {
        for (
            let diff = fazendaCounter - fazendaAux;
            diff > fazendaAux - 1;
            diff--
        ) {
            elementsArray.pop().remove();
            thirdFormData.pop();
        }
    }

    if (userp_info.style.display == "none") {
        secondForm.style.display = "block";
    }

    showSubmit();
});

submitSecondForm.addEventListener("click", (e) => {
    e.preventDefault();
    secondFormData = new FormData(secondForm);

    const check = checkEmptySecondForm(secondFormData);
    if (check.error) {
        $("#mensagem_erro").modal("show");
        $("#log_error").html(check.message);
        return;
    }

    document.getElementById("nomeCpfUserp").innerText =
        secondFormData.get("userp_nome") +
        " - " +
        secondFormData.get("userp_cpf");

    document.getElementById("emailUserp").innerText =
        secondFormData.get("userp_email");

    document.getElementById("telefoneUserp").innerText =
        "(" +
        secondFormData.get("userp_ddd") +
        ") " +
        secondFormData.get("userp_telefone");

    userp_info.style = "display: block";
    userp_info.style = "background-color: rgb(250,250,250)";

    secondForm.style.display = "none";
    if (fazendaCounter < fazendaAux) {
        thirdForm.style.display = "block";

        $("#nome_fazenda").focus();
    }

    edit_produtor_info.style = "display: block";
    edit_produtor_info.style = "margin-top: 1em";

    edit_fazenda_info.style = "display: block";
    edit_fazenda_info.style = "margin-top: 1em";

    checks.userpR = true;

    showSubmit();
});

submitThirdForm.addEventListener("click", (e) => {
    e.preventDefault();

    let checkbox = $("#thirdForm").find("input[type=checkbox]");
    const fd = new FormData(thirdForm);
    const check = checkEmptyThirdForm(fd);

    if (check.error) {
        $("#mensagem_erro").modal("show");
        $("#log_error").html(check.message);
        return;
    }

    $.each(checkbox, function (key, val) {
        if (fd.has(val.name)) {
            fd.delete(val.name);
        }
        fd.append($(val).attr("name"), $(val).is(":checked"));
    });

    if (fazendaCounter == 0) {
        fazenda_info.style.display = "block";
        fazenda_info.style = "background-color: rgb(250,250,250)";
    }

    if (!isEditingThirdForm.isEditing) {
        thirdFormData.push(fd);

        const nomeFazenda = document.createTextNode(
            thirdFormData[fazendaCounter].get("nome_fazenda")
        );

        const p = document.createElement("p");
        p.setAttribute("id", `fazenda${fazendaCounter}`);
        p.style.fontSize = "9pt";
        p.appendChild(nomeFazenda);

        const d = document.createElement("div");
        d.setAttribute("class", "col-md-12");
        d.appendChild(p);
        elementsArray.push(d);
        fazenda_edit.append(d);

        const text = document.createTextNode(
            thirdFormData[fazendaCounter].get("nome_fazenda")
        );

        const opt = document.createElement("option");
        opt.setAttribute("value", fazendaCounter);
        opt.setAttribute("id", `opt${fazendaCounter}`);
        opt.appendChild(text);

        select_edit.append(opt);

        fazendaCounter += 1;
    } else {
        thirdFormData[isEditingThirdForm.index] = fd;

        const opt = document.getElementById(`opt${isEditingThirdForm.index}`);
        opt.text = fd.get("nome_fazenda");

        const $p = $(`#fazenda${isEditingThirdForm.index}`);
        $p.contents()[0].textContent = fd.get("nome_fazenda");
        isEditingThirdForm.isEditing = false;
    }

    edit_produtor_info.style = "display: block";
    edit_produtor_info.style = "margin-top: 1em";

    edit_userp_info.style = "display: block";
    edit_userp_info.style = "margin-top: 1em";

    edit_fazenda_info.style = "display: block";
    edit_fazenda_info.style = "margin-top: 1em";

    if (fazendaCounter < fazendaAux) {
        thirdFormTitle.innerHTML = "Fazenda 0" + (fazendaCounter + 1);

        $("#nome_fazenda").focus();
    }

    if (fazendaCounter == fazendaAux) {
        thirdForm.style.display = "none";
        div_enviar_formulario.style.display = "block";
        checks.fazendaR = true;
        return;
    }

    clearThirdForm();

    document.getElementById("fazenda_cpf_cnpj").value =
        firstFormData.get("cpf_cnpj");
});

edit_produtor_info.addEventListener("click", (e) => {
    firstForm.style.display = "block";
    secondForm.style.display = "none";
    thirdForm.style.display = "none";

    empresa_produtor_info.style = "display: none";
    edit_userp_info.style = "display: none";
    edit_fazenda_info.style = "display: none";

    div_enviar_formulario.style.display = "none";

    $("#nome_empresa").focus();
});

edit_userp_info.addEventListener("click", (e) => {
    firstForm.style.display = "none";
    secondForm.style.display = "block";
    thirdForm.style.display = "none";

    userp_info.style = "display: none";
    edit_produtor_info.style = "display: none";
    edit_fazenda_info.style = "display: none";

    div_enviar_formulario.style.display = "none";

    $("#userp_nome").focus();
});

edit_fazenda_info.addEventListener("click", (e) => {
    modal_edit.modal("show");
});

button_fazenda_edit.addEventListener("click", (e) => {
    const val = select_edit.val();
    select_edit.val("").change();
    editThirdForm(val);
});

function showSubmit() {
    if (checks.empresaR && checks.userpR && checks.fazendaR) {
        div_enviar_formulario.style.display = "block";
    }
}

function copyCpfCnpj(element) {
    element.value = firstFormData.get("cpf_cnpj");
}

function copyOwnerCpf(element) {
    element.value = firstFormData.get("cpf_cnpj");
}

function clearThirdForm() {
    $("#nome_fazenda").val("");
    $("#fazenda_cpf_cnpj").val("");
    $("#fazenda_insc_est").val("");
    $("#fazenda_cep").val("");
    $("#fazenda_endereco").val("");
    $("#fazenda_num").val("");
    $("#fazenda_complemento").val("");
    $("#fazenda_bairro").val("");
    $("#fazenda_estado").val("");
    $("#fazenda_cidade").val("");
    $("#fazenda_municipio").val("");
    $("#fazenda_area").val("");
    $("#fazenda_area_util").val("");
    //$("#fazenda_localizacao").val("");
    $("#fazenda_latitude").val("");
    $("#fazenda_longitude").val("");
    $("#atv_pec_corte").prop("checked", false);
    $("#atv_pec_leite").prop("checked", false);
    $("#atv_agricultura").prop("checked", false);
    $("#atv_outra").prop("checked", false);
    $("#descricao_atv_agricola").val("");
    $("#descricao_atv_outra").val("");
}

function editThirdForm(id) {
    firstForm.style.display = "none";
    secondForm.style.display = "none";

    edit_userp_info.style = "display: none";
    edit_produtor_info.style = "display: none";
    edit_fazenda_info.style = "display: none";

    div_enviar_formulario.style.display = "none";

    const fd = thirdFormData[id];
    isEditingThirdForm.isEditing = true;
    isEditingThirdForm.index = id;

    $("#nome_fazenda").val(fd.get("nome_fazenda"));
    $("#fazenda_cpf_cnpj").val(fd.get("fazenda_cpf_cnpj"));
    $("#fazenda_insc_est").val(fd.get("fazenda_insc_est"));
    $("#fazenda_cep").val(fd.get("fazenda_cep"));
    $("#fazenda_endereco").val(fd.get("fazenda_endereco"));
    $("#fazenda_num").val(fd.get("fazenda_num"));
    $("#fazenda_complemento").val(fd.get("fazenda_complemento"));
    $("#fazenda_bairro").val(fd.get("fazenda_bairro"));
    $("#fazenda_estado").val(fd.get("fazenda_estado"));
    $("#fazenda_cidade").val(fd.get("fazenda_cidade"));
    $("#fazenda_municipio").val(fd.get("fazenda_municipio"));
    $("#fazenda_area").val(fd.get("fazenda_area"));
    $("#fazenda_area_util").val(fd.get("fazenda_area_util"));
    //$("#fazenda_localizacao").val(fd.get("fazenda_localizacao"));

    $("#fazenda_latitude").val(fd.get("fazenda_latitude"));
    $("#fazenda_longitude").val(fd.get("fazenda_longitude"));

    $("#atv_pec_corte").prop(
        "checked",
        fd.get("atv_pec_corte") === "true" ? true : false
    );
    $("#atv_pec_leite").prop(
        "checked",
        fd.get("atv_pec_leite") === "true" ? true : false
    );
    $("#atv_agricultura").prop(
        "checked",
        fd.get("atv_agricultura") === "true" ? true : false
    );
    $("#atv_outra").prop(
        "checked",
        fd.get("atv_outra") === "true" ? true : false
    );
    $("#descricao_atv_agricola").val(fd.get("descricao_atv_agricola"));
    $("#descricao_atv_outra").val(fd.get("descricao_atv_outra"));

    id++;

    thirdFormTitle.innerHTML = "Fazenda 0" + id;

    thirdForm.style.display = "block";
}

function repeatName(v) {
    $("#nome_fantasia").val(v).change();
}

function checkRequiredInput(v, e) {
    if (v === "") {
        e.target.classList.add("input_vazio");
    } else {
        e.target.classList.remove("input_vazio");
    }
}

function checkEmptyFirstForm(fd) {
    if (fd.get("nome_empresa") === "") {
        $("#nome_empresa")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Preencha o nome da empresa!",
        };
    }

    if (fd.get("nome_fantasia") === "") {
        $("#nome_fantasia")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Preencha o nome fantasia!",
        };
    }

    if (fd.get("cpf_cnpj") === "") {
        $("#cpf_cnpj")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Preencha o CPF/CNPJ!",
        };
    }

    if (fd.get("cep_pessoa") === "") {
        $("#cep_pessoa")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Preencha o CEP!",
        };
    }

    if (fd.get("qtdeFazenda") === "" || fd.get("qtdeFazenda") === "0") {
        $("#qtdeFazenda")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Preencha a quantidade de fazendas!",
        };
    }

    if (fd.get("controle_estoque") === "false") {
        return {
            error: true,
            message: "Selecione o tipo de controle de estoque!",
        };
    }

    return {
        error: false,
        message: "",
    };
}

function checkEmptySecondForm(fd) {
    if (fd.get("userp_nome") == "") {
        $("#userp_nome")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Digite o nome do usuário principal!",
        };
    }

    if (fd.get("userp_cpf") == "") {
        $("#userp_cpf")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Digite o CPF do usuário principal!",
        };
    }

    if (fd.get("userp_email") == "") {
        $("#userp_email")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Digite o email do usuário principal!",
        };
    }

    if (fd.get("userp_ddd") == "") {
        $("#userp_ddd")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Digite o DDD do usuário principal!",
        };
    }

    if (fd.get("userp_telefone") == "") {
        $("#userp_telefone")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Digite o telefone do usuário principal!",
        };
    }

    return {
        error: false,
        message: "",
    };
}

function checkEmptyThirdForm(fd) {
    if (fd.get("nome_fazenda") == "") {
        $("#nome_fazenda")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Digite o nome da fazenda!",
        };
    }

    if (fd.get("fazenda_cpf_cnpj") == "") {
        $("#fazenda_cpf_cnpj")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Digite o CPF/CNPJ da fazenda!",
        };
    }

    if (fd.get("fazenda_cep") == "") {
        $("#fazenda_cep")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Digite o CEP da fazenda!",
        };
    }

    if (fd.get("fazenda_area") == "" || fd.get("fazenda_area_util") == "0,00") {
        $("#fazenda_area")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Digite a área da fazenda!",
        };
    }

    if (
        fd.get("fazenda_area_util") == "" ||
        fd.get("fazenda_area_util") == "0,00"
    ) {
        $("#fazenda_area_util")
            .focus(function () {
                $(this).addClass("input_vazio");
            })
            .focus();
        return {
            error: true,
            message: "Digite a área útil da fazenda!",
        };
    }

    return {
        error: false,
        message: "",
    };
}

$("#enviar_formulario").on("click", () => {
    const empresa = {};
    firstFormData.forEach((value, key) => (empresa[key] = value));

    const user = {};
    secondFormData.forEach((value, key) => (user[key] = value));

    const fazenda = [];
    thirdFormData.forEach((value, key) => {
        let fd = {};
        thirdFormData[key].forEach((v, k) => {
            /*if (k == "fazenda_area" || k == "fazenda_area_util") {
                v = v.replace(/,/g, ".");
                alert (v);
            }*/
            fd[k] = v;
        });
        fazenda.push(fd);
    });

    const obj = {
        fazenda: fazenda,
        empresa: empresa,
        user: user,
        tipo_gravacao: 1,
    };

    //console.log(obj);

    $.ajax({
        method: "POST",
        url: "gravar_cliente_boi_virtual.php",
        data: { data: JSON.stringify(obj) },
        success: (data) => {
            $("#mensagem_gravar").html(data.message);
            $("#msg_gravar").modal("show");
        },
    });
});
