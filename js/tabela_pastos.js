/**TABELA PASTOS*/

var controle_estoque = $("#controle_estoque").val();

window.addEventListener("load", function(event) {
    if ($("#codigo_local_filtro").val()!='000000000'){
        listar_pastos();
    }
});        

$(document).ready(function(){
    $('#codigo_local_filtro').change(function(){
       listar_pastos();
    });

    $(".exibir_filtro").click(function(){
        $('.filtro_escondido').hide();
        $('.filtro_exibido').show();
    });

    $(".esconder_filtro").click(function(){
        var tipo_caixa = $("#tipo_caixa").val();

        if (tipo_caixa=="M") {
            $('.opcao_pdf').hide();
        }
        else {
            $('.opcao_pdf').show();
        }

        $('.filtro_escondido').show();
        $('.filtro_exibido').hide();
    });

    $('.qtd_categoria').change(function(){
        var categoria = new Array();
        var array_categoria = "";
        var codigo_cat = document.getElementsByName("codigo_categoria");

        var qtd_categoria_macho = new Array();
        var array_qtd_categoria_macho = "";
        var qtd_cat_macho = document.getElementsByName("qtd_categoria_macho");

        var qtd_categoria_femea = new Array();
        var array_qtd_categoria_femea = "";
        var qtd_cat_femea = document.getElementsByName("qtd_categoria_femea");

        for (var i = 0; i < codigo_cat.length; i++) {
            codigo_categoria = codigo_cat[i].value;
            categoria.push(codigo_categoria);
            array_categoria = categoria.join("!");

            quantidade_categoria_macho = qtd_cat_macho[i].value;
            qtd_categoria_macho.push(quantidade_categoria_macho);
            array_qtd_categoria_macho = qtd_categoria_macho.join("!");

            quantidade_categoria_femea = qtd_cat_femea[i].value;
            qtd_categoria_femea.push(quantidade_categoria_femea);
            array_qtd_categoria_femea = qtd_categoria_femea.join("!");

        }

        $("#array_codigo_categoria").val(array_categoria);
        $("#array_qtd_categoria_macho").val(array_qtd_categoria_macho);
        $("#array_qtd_categoria_femea").val(array_qtd_categoria_femea);
    });

});

function listar_pastos(){
    
    var local = $("#codigo_local_filtro").val();

    //if (local==null) {
    //    local=[''];
    //}

    $.post("form_lista_pastos.php", {local:local},
        function(valor){ $("div[id=lista_pastos]").html(valor); 
    });
}

$(document).ready(function(){
    $('#tabela_pastos').DataTable({
        "responsive": true,
        "paging":   false,
        "ordering": true,
        "info":     true,
        "language": {
        "sSearch": "Busca:",
        "zeroRecords": "Nada encontrado",
        "info": "Registros encontrados: _END_",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(Filtrado de _MAX_ registros no total)",
        },
        "dom": "<'row'<'col-lg-6 col-md-6 col-sm-6'i><'col-lg-6 col-md-6 col-sm-6'f>>",
        initComplete: function() {
            $('table.dataTable').css("width", "100%");
          }
    });

    $('.gravar').on('click', function() {
        var formID = document.getElementById("gravar_mapa");
        var send = $("#botao_gravar");

        $(formID).submit(function(event){
            if (formID.checkValidity()) {
                send.attr('disabled', 'disabled');
            }
        });
    });

});

function voltar_inclusao(){
    location.href='form_tabela_pastos.php';
}

function incluir_novo() {
    $(".gravar").attr("disabled", false);
    $("#codigo_local").val('000000000');
    $('#modal_importar_mapa').modal('show');
}

function calcMeses(idPasto){
    $.ajax({
        type: "POST",
        url: 'ler_animal_pasto_meses.php',
        data: {
            "idPasto": idPasto
        },
        success: function(data){
            $array_conta = data.split("|");
            $arrayCategoria = $array_conta[0].split("!");
            $arrayMeses = $array_conta[1].split("!");
            $arrayIdade = $array_conta[2].split("!");

            for(let i = 0; i < $arrayMeses.length; i++){
                if ($arrayMeses[i].length == 1) {
                    $arrayMeses[i] = '0'+$arrayMeses[i];
                }

                if($arrayMeses[i] >= $arrayCategoria[0] && $arrayMeses[i] <= $arrayCategoria[1]){
                    $('#selectIdade001').append($('<option>', {
                        value: $arrayMeses[i],
                        text: $arrayMeses[i] + ' - ' + $arrayIdade[i]
                    }));
                }else if($arrayMeses[i] >= $arrayCategoria[2] && $arrayMeses[i] <= 12){
                    $('#selectIdade002').append($('<option>', {
                        value: $arrayMeses[i],
                        text: $arrayMeses[i] + ' - ' + $arrayIdade[i]
                    }));
                }else if($arrayMeses[i] >= 13 && $arrayMeses[i] <= 24){
                    $('#selectIdade003').append($('<option>', {
                        value: $arrayMeses[i],
                        text: $arrayMeses[i] + ' - ' + $arrayIdade[i]
                    }));
                }else if($arrayMeses[i] >= $arrayCategoria[6] && $arrayMeses[i] <= $arrayCategoria[7]){
                    $('#selectIdade004').append($('<option>', {
                        value: $arrayMeses[i],
                        text: $arrayMeses[i] + ' - ' + $arrayIdade[i]
                    }));
                }else if($arrayMeses[i] >= 37 && $arrayMeses[i] <= 9999){
                    $('#selectIdade005').append($('<option>', {
                        value: $arrayMeses[i],
                        text: $arrayMeses[i] + ' - ' + $arrayIdade[i]
                    }));
                }
            }
        }
    });
}

function preencherNascimento(id, valor){
    $.ajax({
        type: "POST",
        url: "ler_animal_pasto_meses.php",
        data: {
            "numMeses": valor,
            "pastoID" : $("#codigo_conta").val()
        },
        success: function(data){
            if(id == "selectIdade001"){
                $('#selectNascimento001').find('option').not(':first').remove();
            }else if(id == "selectIdade002"){
                $('#selectNascimento002').find('option').not(':first').remove();
            }else if(id == "selectIdade003"){
                $('#selectNascimento003').find('option').not(':first').remove();
            }else if(id == "selectIdade004"){
                $('#selectNascimento004').find('option').not(':first').remove();
            }else if(id == "selectIdade005"){
                $('#selectNascimento005').find('option').not(':first').remove();
            }
            $array_conta = data.split("|");
            $arrayID = $array_conta[0].split("!");
            $arrayIdade= $array_conta[1].split("!");
            $arraySexo = $array_conta[2].split("!");
            for(let i = 0; i < $arrayID.length; i++){
                if(id == "selectIdade001"){
                    $('#selectNascimento001').append($('<option>', {
                        value: $arrayID[i],
                        text: $arrayIdade[i] + ' ' + $arraySexo[i]
                    }));
                }else if(id == "selectIdade002"){
                    $('#selectNascimento002').append($('<option>', {
                        value: $arrayID[i],
                        text: $arrayIdade[i] + ' ' + $arraySexo[i]
                    }));
                }else if(id == "selectIdade003"){
                    $('#selectNascimento003').append($('<option>', {
                        value: $arrayID[i],
                        text: $arrayIdade[i] + ' ' + $arraySexo[i]
                    }));
                }else if(id == "selectIdade004"){
                    $('#selectNascimento004').append($('<option>', {
                        value: $arrayID[i],
                        text: $arrayIdade[i] + ' ' + $arraySexo[i]
                    }));
                }else if(id == "selectIdade005"){
                    $('#selectNascimento005').append($('<option>', {
                        value: $arrayID[i],
                        text: $arrayIdade[i] + ' ' + $arraySexo[i]
                    }));
                }
            }
        }
    });
}

function editarAnimal(valor){
    if(valor != 0){
        $("#idAnimal").val(valor);
        $.ajax({
            type: "POST",
            url: "ler_animal_pasto_meses.php",
            data:{
                "idAnimal": valor,
                "pastoID" : $("#codigo_conta").val()
            },
            success:function(data){
                $array_conta = data.split("|");
                $("#sexoAnimal").val($array_conta[0]).change();
                $("#nascimentoAnimal").val($array_conta[1]);
            }
        });
    
        $('#modalEditarAnimal').modal('show');
    }
}

function gravarAnimal(){
    if($("#sexoAnimal").val() == 0 || $("#nascimentoAnimal").val() == ''){
        alert("Selecione o sexo e o nascimento do animal!");
    }else{
        let idAnimal = $("#idAnimal").val();
        let idPasto = $("#codigo_conta").val();
        let sexoAnimal = $("#sexoAnimal").val();
        let idadeAnimal = $("#nascimentoAnimal").val();

        $.ajax({
            type: "POST",
            url: "gravar_animal_pasto.php",
            data:{
                "idAnimal": idAnimal,
                "idPasto": idPasto,
                "sexoAnimal": sexoAnimal,
                "idadeAnimal": idadeAnimal
            },
            success: function(){
                location.reload();
            }
        });
    }
}

function editar_pasto(array_registro) {
    $array_conta = array_registro.split('|');
    $("#codigo_conta").val($array_conta[0]);
    $("#codigo_local").val($array_conta[1]);
    $("#descricao").val($array_conta[2]);
    $("#descricao_anterior").val($array_conta[2]);
    //$("#latitude").val($array_conta[3]);
    //$("#logitude").val($array_conta[4]);

    if ($array_conta[5]==0.00) {
        $("#area").val('');
    }
    else {
        $("#area").val(formatMoney($array_conta[5]));
    }

    $("#modulo").val($array_conta[10]);
    $("#capim").val($array_conta[11]);
    $("#observacao").val($array_conta[16]);

    if ($array_conta[10]==999) {
        var desc = $("#codigo_local option:selected").text();
        desc = desc.trim();
        $("#local_readonly").val(desc);

        var desc = $("#modulo option:selected").text();
        desc = desc.trim();
        $("#modulo_readonly").val(desc);

        var desc = $("#capim option:selected").text();
        desc = desc.trim();
        $("#capim_readonly").val(desc);

        document.getElementById('descricao').readOnly = true;

        $("#local_readonly").show();    
        $("#codigo_local").hide();    

        $("#modulo_readonly").show();    
        $("#modulo").hide();    

        $("#capim_readonly").show();    
        $("#capim").hide();    
    }
    else {
        document.getElementById('descricao').readOnly = false;

        $("#local_readonly").hide();    
        $("#codigo_local").show();    

        $("#modulo_readonly").hide();    
        $("#modulo").show();    

        $("#capim_readonly").hide();    
        $("#capim").show();    
    }

    if (controle_estoque=='L') {
        $arrayAnimais = $array_conta[17].split('!');
        var qtdeAnimaisPasto = document.getElementsByName("qtdeAnimaisPasto");

        for(let i = 0; i < $arrayAnimais.length; i++){
            qtdeAnimaisPasto[i].innerHTML = $arrayAnimais[i];
        }

        $('#selectIdade001').find('option').not(':first').remove();
        $('#selectIdade002').find('option').not(':first').remove();
        $('#selectIdade003').find('option').not(':first').remove();
        $('#selectIdade004').find('option').not(':first').remove();
        $('#selectIdade005').find('option').not(':first').remove();

        $('#selectNascimento001').find('option').not(':first').remove();
        $('#selectNascimento002').find('option').not(':first').remove();
        $('#selectNascimento003').find('option').not(':first').remove();
        $('#selectNascimento004').find('option').not(':first').remove();
        $('#selectNascimento005').find('option').not(':first').remove();

        calcMeses($array_conta[0]);
    }
    else {
        $arrayAnimais = $array_conta[17].split('!');
        $arrayAnimaisMachos= $array_conta[18].split('!');
        $arrayAnimaisFemeas = $array_conta[19].split('!');

        var qtdeAnimaisPasto = document.getElementsByName("qtdeAnimaisPasto");
        var qtd_categoria_macho = document.getElementsByName("qtd_categoria_macho");
        var qtd_categoria_femea = document.getElementsByName("qtd_categoria_femea");

        var qtd_macho_anterior = document.getElementsByName("qtd_macho_anterior");
        var qtd_femea_anterior = document.getElementsByName("qtd_femea_anterior");

        for(let i = 0; i < $arrayAnimais.length; i++){
            qtdeAnimaisPasto[i].innerHTML = $arrayAnimais[i];
            qtd_categoria_macho[i].value = $arrayAnimaisMachos[i];
            qtd_categoria_femea[i].value = $arrayAnimaisFemeas[i];

            qtd_macho_anterior[i].value = $arrayAnimaisMachos[i];
            qtd_femea_anterior[i].value = $arrayAnimaisFemeas[i];
        }
    }

    $("#tipo_gravacao").val(1);

    $('#modal_incluir .modal-title').html('Pastos - Editar');
    $('.confirma_gravar').html('Confirmar Edição').removeClass('btn-danger').addClass('btn-primary');
    $('.confirma_gravar').show();
    $('.voltar_inclusao').hide();
    $(".mens_descricao").show(); 

    $('.voltar').show();
    $("#informacao").show();    

    if ($array_conta[9]=='') {
        $("#registro_alterado").hide();
        $("#alterado_por").hide();
        $("#alterado_em").hide();
    }
    else {
        $("#registro_alterado").show();        
        $("#alterado_por").show();
        $("#alterado_em").show();
    }

    $('#modal_incluir').modal('show');

}

function enviar_lixeira(array_registro, opcao) {
    $array_conta = array_registro.split('|');
    $("#codigo_conta").val($array_conta[0]);
    $("#codigo_local").val($array_conta[1]);
    $("#descricao").val($array_conta[2]);
    //$("#latitude").val($array_conta[3]);
    //$("#logitude").val($array_conta[4]);

    if ($array_conta[5]==0.00) {
        $("#area").val('');
    }
    else {
        $("#area").val(formatMoney($array_conta[5]));
    }

    $("#incluido_em").text($array_conta[6]);
    $("#incluido_por").text($array_conta[7]);
    $("#alterado_em").text($array_conta[8]);
    $("#alterado_por").text($array_conta[9]);
    $("#modulo").val($array_conta[10]);
    $("#capim").val($array_conta[11]);
    $("#observacao").val($array_conta[16]);

    if ($array_conta[9]=='') {
        $("#registro_alterado").hide();
        $("#alterado_por").hide();
        $("#alterado_em").hide();
    }
    else {
        $("#registro_alterado").show();        
        $("#alterado_por").show();
        $("#alterado_em").show();
    }

    $("#tipo_gravacao").val(opcao);

    if (opcao==2) {
        $('#modal_incluir .modal-title').html('Pastos - Enviar para Lixeira');
        $(".confirma_gravar").html('Enviar para Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }
    else {
        $('#modal_incluir .modal-title').html('Pastos - Remover da Lixeira');
        $(".confirma_gravar").html('Remover da Lixeira').removeClass('btn-primary').addClass('btn-danger');
    }

    $("#local_readonly").hide();    
    $("#modulo_readonly").hide();    
    $("#capim_readonly").hide(); 
    $(".mens_descricao").hide(); 

    $('.confirma_gravar').show();
    $('.voltar_inclusao').hide();
    $('.voltar').show();
    $('#modal_incluir').modal('show');
}

function gravar_pasto() {
    if (controle_estoque=='I') {
        var categoria = new Array();
        var array_categoria = "";
        var codigo_cat = document.getElementsByName("codigo_categoria");

        var qtd_categoria_macho = new Array();
        var array_qtd_categoria_macho = "";
        var qtd_cat_macho = document.getElementsByName("qtd_categoria_macho");

        var qtd_categoria_macho_anterior = new Array();
        var array_qtd_categoria_macho_anterior = "";
        var qtd_cat_macho_anterior = document.getElementsByName("qtd_macho_anterior");

        var qtd_categoria_femea = new Array();
        var array_qtd_categoria_femea = "";
        var qtd_cat_femea = document.getElementsByName("qtd_categoria_femea");

        var qtd_categoria_femea_anterior = new Array();
        var array_qtd_categoria_femea_anterior = "";
        var qtd_cat_femea_anterior = document.getElementsByName("qtd_femea_anterior");

        for (var i = 0; i < codigo_cat.length; i++) {
            codigo_categoria = codigo_cat[i].value;
            categoria.push(codigo_categoria);
            array_categoria = categoria.join("!");

            quantidade_categoria_macho = qtd_cat_macho[i].value;
            qtd_categoria_macho.push(quantidade_categoria_macho);
            array_qtd_categoria_macho = qtd_categoria_macho.join("!");

            quantidade_categoria_macho_anterior = qtd_cat_macho_anterior[i].value;
            qtd_categoria_macho_anterior.push(quantidade_categoria_macho_anterior);
            array_qtd_categoria_macho_anterior = qtd_categoria_macho_anterior.join("!");

            quantidade_categoria_femea = qtd_cat_femea[i].value;
            qtd_categoria_femea.push(quantidade_categoria_femea);
            array_qtd_categoria_femea = qtd_categoria_femea.join("!");

            quantidade_categoria_femea_anterior = qtd_cat_femea_anterior[i].value;
            qtd_categoria_femea_anterior.push(quantidade_categoria_femea_anterior);
            array_qtd_categoria_femea_anterior = qtd_categoria_femea_anterior.join("!");
        }

        $("#array_codigo_categoria").val(array_categoria);
        $("#array_qtd_categoria_macho").val(array_qtd_categoria_macho);
        $("#array_qtd_categoria_femea").val(array_qtd_categoria_femea);

        $("#array_qtd_macho_anterior").val(array_qtd_categoria_macho_anterior);
        $("#array_qtd_femea_anterior").val(array_qtd_categoria_femea_anterior);

    }
    else {
        $("#array_codigo_categoria").val('');
        $("#array_qtd_categoria_macho").val('');
        $("#array_qtd_categoria_femea").val('');
        $("#array_qtd_macho_anterior").val('');
        $("#array_qtd_femea_anterior").val('');
    }

    var tipo_gravacao = $("#tipo_gravacao").val();

    if (tipo_gravacao==2) {
        if (window.confirm("Atenção! Ao confirmar enviar esse registro para lixeira, não será possível recupera-lo pelo sistema. Confirmar assim mesmo?")) {
            var dados = $('#form_gravar_pasto').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_pasto.php',
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
        }
    }
    else if (tipo_gravacao==3) {
        if (window.confirm("Confirma remover esse registro da lixeira?")) {
            var dados = $('#form_gravar_pasto').serialize();
            $.ajax({
                type: "POST",
                url: 'gravar_pasto.php',
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
        }
    }
    else if (tipo_gravacao==1){ // alterar
        var dados = $('#form_gravar_pasto').serialize();
        $.ajax({
            type: "POST",
            url: 'gravar_pasto.php',
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
    }
    else { // incluir
        var dados = $('#form_gravar_pasto').serialize();

        $.ajax({
            type: "POST",
            url: 'gravar_pasto.php',
            data: dados,
            success: function(data){

                if (data.error) {
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html(data.message);
                }
                else if (data.success){
                    $("#mensagem_retorno_inclusao").modal();
                    $("#mensagem_retorno_inclusao .modal-body").html(data.message);
                }
            }
        });
    }
}

function digita_valor(){
    $('#area').bind('keypress',mask.money);
}

function exibe_valor_area(){
    var area = $("#area").val();
    if (verifica_virgula(area)==',') {
        area = replace_valor(area);
    }

    $("#area").val(formatMoney(area));
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

function limpar_tela(){
    $('#aguardar').hide();
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

$(window).resize(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input").addClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input").removeClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});

$(document).ready(function() {
    if (window.innerWidth <= 991) 
        $(".modal-body form .tab-content #dados .row .form-group input").addClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").addClass('input-lg');
    else 
        $(".modal-body form .tab-content #dados .row .form-group input").removeClass('input-lg'),
        $(".modal-body form .tab-content #dados .row .form-group select").removeClass('input-lg');
});