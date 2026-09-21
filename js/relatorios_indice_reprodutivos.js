/**RELATORIOS REPRODUTIVOS*/
window.addEventListener("load", function(event) {
    if ($("input[name='tipo_cobertura']:checked").val()=='I') {
        $('.estacao_monta').show();
        $('.data').hide();
    }
    else {
        $('.estacao_monta').hide();
        $('.data').show();
    }

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
    $('.tipo_cobertura').click(function(){
        if ($("input[name='tipo_cobertura']:checked").val()=='I') {
            $('.estacao_monta').show();
            $('.data').hide();
        }
        else {
            $('.estacao_monta').hide();
            $('.data').show();
        }
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
        "info": "Registros encontrados: _END_ ",
        "infoEmpty": "Nenhum registro disponível",
        "infoFiltered": "(filtrado de _MAX_ registros no total)",
        }
    });

    $('#codigo_local').change(function(){
        var local = $("#codigo_local").val();

        if (local==null) {
            local=[''];
        }

        $.post("lista_estacao_monta_rel_indices.php", {local:local}, function(valor){
            $("select[name=codigo_estacao_monta]").html(valor);
            $('.selectpicker').selectpicker('refresh');
        });
    });
});


// RELATORIO

function finalizar_sair() {
    location.href='form_rel_indices_reprodutivos.php';
}

function listar_indices(opcao){
    var local = $("#codigo_local").val();
    var codigo_estacao_monta = $("#codigo_estacao_monta").val();
    var tipo_cobertura = $("input[name='tipo_cobertura']:checked").val();

    if (local==null) {
        $("#mensagem_erro").modal();
        $("#mensagem_erro .modal-body").html('Selecione a(s) Fazenda(s)');
        return;
    }
    else {
        var array_local = new Array();
        var valor = new Array();

        for (i = 0; i <= local.length; i++) {
            valor[i]=local[i];
        }

        var array_local=valor.join(",");
    }

    var options = $('#codigo_local option:selected');
    var local_filtro = [];

    $(options).each(function(){
        var desc = $(this).bind('#codigo_local').text();
        local_filtro.push( desc.trim() );
    });

    if (local_filtro!=''){
        local_filtro = 'Local:'+local_filtro+'->';
    }
    else {
        local_filtro = '';
    }

    if (tipo_cobertura=='I') {
        if (codigo_estacao_monta==null) {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Selecione a Estação de Monta');
            return;
        }
        else {
            var array_estacao_monta = new Array();
            var valor = new Array();

            for (i = 0; i <= codigo_estacao_monta.length; i++) {
                valor[i]=codigo_estacao_monta[i];
            }

            var array_estacao_monta=valor.join(",");
        }

        var options = $('#codigo_estacao_monta option:selected');
        var estacao_monta_filtro = [];

        $(options).each(function(){
            var desc = $(this).bind('#codigo_estacao_monta').text();
            estacao_monta_filtro.push( desc.trim() );
        });

        if (estacao_monta_filtro!=''){
            estacao_monta_filtro = 'Estação Monta:'+estacao_monta_filtro;
        }
        else {
            estacao_monta_filtro = '';
        }

        var tipo_filtro = 'IATF->';

        var descricao_filtro = tipo_filtro+estacao_monta_filtro;

        var periodo_de = '';
        var periodo_ate = '';
    }
    else {
        var periodo_de = $("#periodo_de").val();
        var periodo_ate = $("#periodo_ate").val();

        if (periodo_de=='' || periodo_ate=='') {
            $("#mensagem_erro").modal();
            $("#mensagem_erro .modal-body").html('Informe o Período corretamente.');
            return;
        }

        var data_ini = periodo_de.split("-");
        var dia_ini = data_ini[2];
        var mes_ini = data_ini[1];
        var ano_ini = data_ini[0];

        var data_fim = periodo_ate.split("-");
        var dia_fim = data_fim[2];
        var mes_fim = data_fim[1];
        var ano_fim = data_fim[0];
        
        periodo_filtro =
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

        var tipo_filtro = 'Monta->';
        var descricao_filtro = tipo_filtro+local_filtro+periodo_filtro;

        var array_estacao_monta = new Array();
    }

    /*let checkBoxes = document.getElementsByName("tipo_cobertura");
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
    var tipo_filtro = 'Tipo:';

    if (monta.is(":checked")){
        valor[i]='M';
        var array_tipo=valor.join(",");
        tipo_filtro+= 'Monta/';
        i++;
    }

    if (iatf.is(":checked")){
        valor[i]='I';
        var array_tipo=valor.join(",");
        tipo_filtro+= 'IATF/';
        i++;
    }

    if (te.is(":checked")){
        valor[i]='T';
        var array_tipo=valor.join(",");
        tipo_filtro+= 'TE/';
        i++;
    }*/

    $("#aguardar").modal();

    if (opcao=='1') {
        location.href='form_indices_reprodutivos_rel.php?descricao_filtro=' + descricao_filtro +
        '&local=' + array_local + '&estacao_monta=' + array_estacao_monta + 
        '&tipo_cobertura=' + tipo_cobertura + 
        '&periodo_de=' + periodo_de + 
        '&periodo_ate=' + periodo_ate;
    }
    else {
        location.href='rel_indices_reprodutivos_excel.php?descricao_filtro=' + descricao_filtro +
        '&local=' + array_local + '&estacao_monta=' + array_estacao_monta + 
        '&tipo_cobertura=' + tipo_cobertura + 
        '&periodo_de=' + periodo_de + 
        '&periodo_ate=' + periodo_ate;

        tout = setTimeout('limpar_tela()', 5000);
    }
}

function lista_indice_excel(){
    var local = $("#codigo_local").val();
    var descricao_filtro = $("#descricao_filtro").val();
    var estacao_monta = $("#codigo_estacao_monta").val();
    var tipo_cobertura = $("#tipo_cobertura").val();
    var periodo_de = $("#periodo_de").val();
    var periodo_ate = $("#periodo_ate").val();
    $("#aguardar").modal();

    location.href='rel_indices_reprodutivos_excel.php?descricao_filtro=' + descricao_filtro + 
    '&local=' + local + '&estacao_monta=' + estacao_monta+ 
    '&tipo_cobertura=' + tipo_cobertura + 
    '&periodo_de=' + periodo_de + 
    '&periodo_ate=' + periodo_ate;

    tout = setTimeout('limpar_tela()', 5000);
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
    location.href='form_rel_indices_reprodutivos.php';
}

function voltar_relatorios() {
    location.href='form_relatorios_produtivos.php';
}


// FIM RELATORIO

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

