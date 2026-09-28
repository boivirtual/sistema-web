/** EDITOR DE MAPA DOS PASTOS (Leaflet + Leaflet.Draw) */
var editorMapa = {
    map: null,
    grupo: null,
    itens: [],
    pontos: [],
    pastosSistema: {},
    modulos: [],
    legenda: null,
    versao: '',
    local: '',
    selecionado: null,
    editando: false,
    novoPendente: null,
    modoNome: '',
    snapshot: '',
    desenho: null
};

var EDITOR_COR_OK = '#2E8B57';
var EDITOR_COR_SEM_CADASTRO = '#ff8f00';
var EDITOR_COR_NOVO = '#128cb8';

function editor_carregar_script(url, sucesso, erro) {
    var s = document.createElement('script');
    s.src = url;
    s.onload = sucesso;
    s.onerror = erro;
    document.head.appendChild(s);
}

function editor_carregar_bibliotecas(sucesso) {
    if (window.L && L.Draw) {
        sucesso();
        return;
    }

    var erro = function() {
        editor_mostrar_erro('Não foi possível carregar a biblioteca de mapas. Verifique a conexão com a internet.');
    };

    editor_carregar_script('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', function() {
        editor_carregar_script('https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js', sucesso, erro);
    }, erro);
}

function editor_mostrar_erro(mensagem) {
    $("#mensagem_erro").modal();
    $("#mensagem_erro .modal-body").html(mensagem);
}

var editorTemporizadorAviso = null;
var editorAvisoEhDica = false;

// Mensagens de sucesso somem sozinhas; segundos pode ser informado para outros tipos
function editor_aviso(tipo, html, segundos) {
    clearTimeout(editorTemporizadorAviso);
    editorAvisoEhDica = false;

    $("#editor_mapa_aviso").removeClass("alert-info alert-success alert-warning alert-danger")
        .addClass("alert-" + tipo).html(html).show();
    editor_ajustar_altura();

    if (tipo == 'success' && !segundos) {
        segundos = 5;
    }

    if (segundos) {
        editorTemporizadorAviso = setTimeout(function() {
            $("#editor_mapa_aviso").hide();
            editor_ajustar_altura();

            // Depois de uma mensagem de sucesso (salvar, excluir), volta a dica de selecao
            if (tipo == 'success' && editorMapa.selecionado === null) {
                editor_mostrar_dica();
            }
        }, segundos * 1000);
    }
}

// Dica padrao do editor com o mapa carregado; some quando um pasto e selecionado
function editor_mostrar_dica() {
    if (editorMapa.local == '') {
        return;
    }

    editor_aviso('info', 'Clique em um pasto no mapa para selecioná-lo.');
    editorAvisoEhDica = true;
}

// O mapa ocupa todo o espaco vertical que sobra na tela de trabalho
function editor_ajustar_altura() {
    var $mapa = $("#editor_mapa");

    if (!$("#editor_mapa_tela").is(':visible')) {
        return;
    }

    var topo = $mapa[0].getBoundingClientRect().top;
    var altura = Math.max(300, window.innerHeight - topo - $("#editor_info").outerHeight(true) - 20);

    $mapa.css('height', altura + 'px');

    if (editorMapa.map !== null) {
        editorMapa.map.invalidateSize();
    }
}

var editorSaindo = false;
var editorAcaoSair = null;
var editorAcaoNaoSair = null;

// Se houver ajustes nao salvos, pergunta antes de executar a acao que descartaria o trabalho
function editor_confirmar_saida(aoConfirmar, aoCancelar) {
    if (!editor_tem_alteracoes()) {
        aoConfirmar();
        return;
    }

    editorAcaoSair = aoConfirmar;
    editorAcaoNaoSair = aoCancelar || null;
    $("#modal_editor_sair").modal('show');
}

function editor_sair_sim() {
    var acao = editorAcaoSair;

    editorAcaoSair = null;
    editorAcaoNaoSair = null;
    $("#modal_editor_sair").modal('hide');

    if (acao) {
        acao();
    }
}

function editor_sair_nao() {
    var acao = editorAcaoNaoSair;

    editorAcaoSair = null;
    editorAcaoNaoSair = null;
    $("#modal_editor_sair").modal('hide');

    if (acao) {
        acao();
    }
}

// Volta o editor ao estado inicial, sem nada carregado
function editor_limpar_tudo() {
    editor_parar_edicao();

    if (editorMapa.desenho) {
        editorMapa.desenho.disable();
        editorMapa.desenho = null;
    }

    if (editorMapa.grupo) {
        editorMapa.grupo.clearLayers();
    }

    editorMapa.itens = [];
    editorMapa.pontos = [];
    editorMapa.pastosSistema = {};
    editorMapa.selecionado = null;
    editorMapa.novoPendente = null;
    editorMapa.local = '';
    editorMapa.versao = '';
    editorMapa.snapshot = '';
    editorMapa.modoNome = '';

    $("#editor_local").val('');
    $("#editor_busca").val('');
    $("#editor_lista_pastos").empty();
    $("#editor_nome_pasto, #editor_senha_excluir").val('');
    $("#editor_painel_nome, #editor_painel_excluir").hide();
    $("#editor_btn_novo").removeClass('active');

    clearTimeout(editorTemporizadorAviso);
    editor_aviso('info', 'Selecione a Fazenda para carregar o mapa.');
    editor_atualizar_botoes();

    if (editorMapa.map !== null) {
        editorMapa.map.setView([-15.8, -47.9], 4);
    }
}

function abrir_editor_mapa() {
    $("#pastos_cabecalho, #pastos_conteudo").hide();
    $("#editor_mapa_tela").show();
    editor_limpar_tudo();
    editor_ajustar_altura();
    editor_preparar_mapa(function() {
        editor_ajustar_altura();
    });
}

// Este script e carregado antes do jQuery (rodape.php), entao o registro espera o load da pagina
window.addEventListener('load', function() {
    $(window).on('resize', editor_ajustar_altura);

    // Mostra no mapa a cor do modulo escolhido para o pasto que esta sendo nomeado
    $(document).on('change', '#editor_modulo_pasto', function() {
        var m = editor_modulo($(this).val());

        if (editorMapa.novoPendente && m) {
            editorMapa.novoPendente.setStyle({ fillColor: m.cor, fillOpacity: 0.6 });
        }
    });
});

// Ultima barreira (fechar aba, digitar outro endereco): aviso nativo do navegador
window.addEventListener('beforeunload', function(ev) {
    if (!editorSaindo && editor_tem_alteracoes()) {
        ev.preventDefault();
        ev.returnValue = '';
    }
});

// Cliques em links (menu, logotipo, etc.) que sairiam da tela com ajustes nao salvos
document.addEventListener('click', function(ev) {
    if (typeof $ == 'undefined' || !$("#editor_mapa_tela").is(':visible') || !editor_tem_alteracoes()) {
        return;
    }

    var a = ev.target.closest ? ev.target.closest('a[href]') : null;

    if (!a || a.closest('#editor_mapa_tela') || a.closest('.modal')) {
        return;
    }

    var href = a.getAttribute('href');

    if (!href || href.charAt(0) == '#' || href.indexOf('javascript:') === 0 ||
        a.target == '_blank' || a.hasAttribute('data-toggle')) {
        return;
    }

    ev.preventDefault();
    ev.stopPropagation();

    editor_confirmar_saida(function() {
        editorSaindo = true;
        window.location.href = a.href;
    });
}, true);

function editor_preparar_mapa(sucesso) {
    editor_carregar_bibliotecas(function() {
        if (editorMapa.map === null) {
            editor_iniciar_mapa();
        }

        editorMapa.map.invalidateSize();
        sucesso();
    });
}

function editor_iniciar_mapa() {
    var mapa = L.map('editor_mapa', { zoomSnap: 0.5, maxZoom: 21 }).setView([-15.8, -47.9], 4);

    var satelite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxNativeZoom: 19,
        maxZoom: 21,
        attribution: 'Imagens &copy; Esri, Maxar, Earthstar Geographics'
    });

    var ruas = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxNativeZoom: 19,
        maxZoom: 21,
        attribution: '&copy; OpenStreetMap'
    });

    satelite.addTo(mapa);
    L.control.layers({ 'Satélite': satelite, 'Ruas': ruas }, null, { position: 'topright' }).addTo(mapa);

    editorMapa.grupo = L.featureGroup().addTo(mapa);
    editorMapa.map = mapa;

    var Legenda = L.Control.extend({
        options: { position: 'bottomleft' },
        onAdd: function() {
            var div = L.DomUtil.create('div', 'editor-legenda');
            L.DomEvent.disableClickPropagation(div);
            L.DomEvent.disableScrollPropagation(div);
            return div;
        }
    });

    editorMapa.legenda = new Legenda();
    editorMapa.legenda.addTo(mapa);

    mapa.on(L.Draw.Event.CREATED, function(ev) {
        editorMapa.desenho = null;
        editor_novo_desenhado(ev.layer);
    });

    // Disparado ao terminar, cancelar (botao ou tecla Esc) ou interromper o desenho
    mapa.on('draw:drawstop', function() {
        editorMapa.desenho = null;

        if (editorMapa.novoPendente === null) {
            editor_mostrar_dica();
        }

        editor_atualizar_botoes();
    });

    // Rotulos so aparecem com zoom suficiente, para nao poluir o mapa
    var ajustarRotulos = function() {
        $("#editor_mapa").toggleClass('editor-sem-rotulos', mapa.getZoom() < 15);
    };

    mapa.on('zoomend', ajustarRotulos);
    ajustarRotulos();
}

function editor_ha(anel) {
    var R = 6378137.0;
    var soma = 0;
    var rad = Math.PI / 180;

    for (var i = 0; i < anel.length - 1; i++) {
        soma += (anel[i + 1][0] - anel[i][0]) * rad *
            (2 + Math.sin(anel[i][1] * rad) + Math.sin(anel[i + 1][1] * rad));
    }

    return Math.abs(soma * R * R / 2) / 10000;
}

function editor_carregar_mapa() {
    var local = $("#editor_local").val();

    if (local == '' || local == null) {
        return;
    }

    if (editor_tem_alteracoes()) {
        $("#editor_local").val(editorMapa.local);

        editor_confirmar_saida(function() {
            $("#editor_local").val(local);
            editor_carregar_mapa_agora(local);
        });
        return;
    }

    editor_carregar_mapa_agora(local);
}

function editor_carregar_mapa_agora(local) {
    editor_aviso('info', 'Carregando o mapa...');

    editor_preparar_mapa(function() {
        $.ajax({
            type: 'post',
            url: 'mapa_pastos_ler.php',
            dataType: 'json',
            data: { 'local': local },
            success: function(data) {
                if (data.error) {
                    editor_aviso('danger', data.message);
                    return;
                }

                editor_montar(data, local);
            },
            error: function() {
                editor_aviso('danger', 'Não foi possível carregar o mapa. Tente novamente.');
            }
        });
    });
}

function editor_montar(data, local) {
    editor_parar_edicao();

    if (editorMapa.desenho) {
        editorMapa.desenho.disable();
        editorMapa.desenho = null;
    }

    editorMapa.grupo.clearLayers();
    editorMapa.itens = [];
    editorMapa.pontos = [];
    editorMapa.selecionado = null;
    editorMapa.novoPendente = null;
    editorMapa.local = local;
    editorMapa.versao = data.versao;
    editorMapa.pastosSistema = data.pastos;
    editorMapa.modulos = data.modulos;
    editor_preencher_modulos();

    $("#editor_painel_nome").hide();

    var features = data.geojson.features;

    for (var i = 0; i < features.length; i++) {
        var f = features[i];

        if (f.geometry.type == 'Point') {
            editorMapa.pontos.push(f);
            continue;
        }

        if (f.geometry.type != 'Polygon') {
            continue;
        }

        var anel = f.geometry.coordinates[0];
        var latlngs = [];

        for (var j = 0; j < anel.length - 1; j++) {
            latlngs.push([anel[j][1], anel[j][0]]);
        }

        var nome = f.properties.name;

        editor_registrar_item({
            layer: L.polygon(latlngs),
            nome: nome,
            nomeOriginal: nome,
            novo: false,
            modificado: false,
            orig: f
        });
    }

    var datalist = $("#editor_lista_pastos").empty();

    for (var k = 0; k < editorMapa.itens.length; k++) {
        datalist.append($("<option>").attr("value", editorMapa.itens[k].nome));
    }

    if (editorMapa.itens.length > 0) {
        editorMapa.map.fitBounds(editorMapa.grupo.getBounds(), { maxZoom: 17 });
    }

    editorMapa.snapshot = JSON.stringify(editor_serializar());

    $("#editor_busca").val('');
    editor_atualizar_botoes();

    var semCadastro = 0;
    editorMapa.itens.forEach(function(it) {
        if (editor_status(it) == 'sem') {
            semCadastro++;
        }
    });

    var dica = 'Clique em um pasto no mapa para selecioná-lo.';

    if (editorMapa.itens.length == 0) {
        editor_aviso('info', 'Essa fazenda ainda não tem mapa. Use <strong>Novo pasto</strong> para desenhar o primeiro (comece por ENTRADA e SAIDA).');
    }
    else if (semCadastro > 0) {
        editor_aviso('info', dica + '<br><strong>' + semCadastro + '</strong> pasto(s) sem cadastro no sistema (contorno laranja tracejado) serão criados ao salvar.');
    }
    else {
        editor_aviso('info', dica);
        editorAvisoEhDica = true;
    }
}

function editor_registrar_item(item) {
    item.layer.addTo(editorMapa.grupo);
    item.layer.bindTooltip(item.nome, { permanent: true, direction: 'center', className: 'editor-pasto-label' });
    item.layer.on('click', function(ev) {
        L.DomEvent.stopPropagation(ev);
        if (editorMapa.desenho === null && editorMapa.novoPendente === null) {
            editor_selecionar(item);
        }
    });
    item.layer.on('edit', function() {
        item.modificado = true;
        editor_atualizar_botoes();
    });

    editorMapa.itens.push(item);
    editor_estilizar(item);
}

function editor_status(item) {
    if (item.novo) {
        return 'novo';
    }

    var nome = item.nomeOriginal.toUpperCase();

    return editorMapa.pastosSistema.hasOwnProperty(nome) ? 'ok' : 'sem';
}

// Id do modulo do pasto: o escolhido (novo) ou o gravado no sistema
function editor_modulo_do_item(item) {
    if (item.novo) {
        return item.modulo;
    }

    if (item.moduloAlterado !== undefined && item.moduloAlterado !== null) {
        return item.moduloAlterado;
    }

    return editor_modulo_original(item);
}

// Modulo gravado no sistema (ignora alteracao ainda nao salva)
function editor_modulo_original(item) {
    var nome = item.nomeOriginal.toUpperCase();

    return editorMapa.pastosSistema.hasOwnProperty(nome) ? editorMapa.pastosSistema[nome] : null;
}

function editor_tem_modulo_alterado() {
    return editorMapa.itens.some(function(it) {
        return !it.novo && it.moduloAlterado !== undefined && it.moduloAlterado !== null &&
            it.moduloAlterado !== editor_modulo_original(it);
    });
}

function editor_modulo(id) {
    for (var i = 0; i < editorMapa.modulos.length; i++) {
        if (editorMapa.modulos[i].id == id) {
            return editorMapa.modulos[i];
        }
    }

    return null;
}

function editor_estilizar(item) {
    var status = editor_status(item);
    var modulo = editor_modulo(editor_modulo_do_item(item));
    var selecionado = (editorMapa.selecionado === item);

    if (status == 'sem' || modulo === null) {
        // sem cadastro (ou modulo desconhecido): cinza com contorno laranja tracejado
        item.layer.setStyle({
            color: selecionado ? '#ffeb3b' : EDITOR_COR_SEM_CADASTRO,
            weight: selecionado ? 4 : 3,
            dashArray: selecionado ? null : '6 4',
            fillColor: '#9E9E9E',
            fillOpacity: selecionado ? 0.6 : 0.35
        });
    }
    else {
        item.layer.setStyle({
            color: selecionado ? '#ffeb3b' : '#ffffff',
            weight: selecionado ? 4 : 1.5,
            dashArray: null,
            fillColor: modulo.cor,
            fillOpacity: selecionado ? 0.7 : 0.5
        });
    }

    if (selecionado) {
        item.layer.bringToFront();
    }
}

function editor_preencher_modulos() {
    var select = $("#editor_modulo_pasto").empty();

    select.append($("<option>").attr("value", "").text("Selecione..."));

    editorMapa.modulos.forEach(function(m) {
        if (m.id != 999) {
            select.append($("<option>").attr("value", m.id).text(m.descricao));
        }
    });

    select.selectpicker('refresh');
}

// Legenda com as cores dos modulos que aparecem no mapa carregado
function editor_atualizar_legenda() {
    if (editorMapa.legenda === null) {
        return;
    }

    var contagem = {};
    var semCadastro = 0;

    editorMapa.itens.forEach(function(it) {
        var m = editor_modulo(editor_modulo_do_item(it));

        if (editor_status(it) == 'sem' || m === null) {
            semCadastro++;
        }
        else {
            contagem[m.id] = (contagem[m.id] || 0) + 1;
        }
    });

    var html = '';

    editorMapa.modulos.forEach(function(m) {
        if (contagem[m.id]) {
            html += '<div><span class="editor-legenda-cor" style="background:' + m.cor + '"></span>' +
                $('<div>').text(m.descricao).html() + ' (' + contagem[m.id] + ')</div>';
        }
    });

    if (semCadastro > 0) {
        html += '<div><span class="editor-legenda-cor editor-legenda-sem"></span>Sem cadastro (' + semCadastro + ')</div>';
    }

    $(editorMapa.legenda.getContainer()).html(html).toggle(html != '');
}

function editor_selecionar(item) {
    editor_parar_edicao();

    var anterior = editorMapa.selecionado;
    editorMapa.selecionado = item;

    if (anterior !== item) {
        editor_cancelar_exclusao();
    }

    // A dica de "clique em um pasto" some assim que o primeiro pasto e selecionado
    if (item && editorAvisoEhDica) {
        editorAvisoEhDica = false;
        $("#editor_mapa_aviso").hide();
        editor_ajustar_altura();
    }

    if (anterior) {
        editor_estilizar(anterior);
    }

    if (item) {
        editor_estilizar(item);
    }

    editor_atualizar_botoes();
}

function editor_buscar_pasto() {
    var nome = $("#editor_busca").val().trim().toUpperCase();

    for (var i = 0; i < editorMapa.itens.length; i++) {
        if (editorMapa.itens[i].nome.toUpperCase() == nome) {
            editor_selecionar(editorMapa.itens[i]);
            editorMapa.map.fitBounds(editorMapa.itens[i].layer.getBounds(), { maxZoom: 18 });
            return;
        }
    }
}

function editor_coordenadas(item) {
    var latlngs = item.layer.getLatLngs()[0];
    var anel = [];

    for (var i = 0; i < latlngs.length; i++) {
        anel.push([latlngs[i].lng, latlngs[i].lat, 0]);
    }

    anel.push(anel[0]);

    return anel;
}

function editor_serializar() {
    var features = editorMapa.itens.map(function(it) {
        var geometria;

        if (it.modificado || it.novo) {
            var aneis = [editor_coordenadas(it)];

            if (it.orig) {
                aneis = aneis.concat(it.orig.geometry.coordinates.slice(1));
            }

            geometria = { type: 'Polygon', coordinates: aneis };
        }
        else {
            geometria = it.orig.geometry;
        }

        return { type: 'Feature', properties: { name: it.nome }, geometry: geometria };
    });

    return { type: 'FeatureCollection', features: features.concat(editorMapa.pontos) };
}

function editor_tem_alteracoes() {
    if (editorMapa.map === null || editorMapa.local == '') {
        return false;
    }

    return JSON.stringify(editor_serializar()) !== editorMapa.snapshot || editor_tem_modulo_alterado();
}

function editor_atualizar_botoes() {
    var item = editorMapa.selecionado;
    var pronto = editorMapa.local != '';
    var ocupado = editorMapa.desenho !== null || editorMapa.novoPendente !== null;

    $("#editor_total_pastos").text(pronto ? editorMapa.itens.length + ' pasto(s) no mapa' : '');
    editor_atualizar_legenda();

    $("#editor_btn_novo").prop('disabled', !pronto || ocupado || editorMapa.editando);
    $("#editor_btn_tracado").prop('disabled', !item || ocupado);
    $("#editor_btn_renomear").prop('disabled', !item || ocupado || editorMapa.editando);
    $("#editor_btn_remover").prop('disabled', !item || !item.novo || ocupado || editorMapa.editando);

    $("#editor_btn_excluir").prop('disabled', !item || item.novo || ocupado || editorMapa.editando);
    $("#editor_btn_salvar").prop('disabled', !pronto || ocupado || editorMapa.editando || !editor_tem_alteracoes());

    if (item) {
        var ha = editor_ha(editor_coordenadas(item));
        var status = editor_status(item);
        var textoStatus = status == 'novo' ? 'novo (será criado ao salvar)' :
            (status == 'sem' ? 'sem cadastro no sistema (será criado ao salvar)' : 'cadastrado no sistema');

        var moduloItem = editor_modulo(editor_modulo_do_item(item));
        var textoModulo = moduloItem ? moduloItem.descricao + ' — ' : '';

        $("#editor_info").text(item.nome + ' — ' + textoModulo + ha.toFixed(2).replace('.', ',') + ' ha — ' + textoStatus);
    }
    else {
        $("#editor_info").html('&nbsp;');
    }
}

function editor_novo_pasto() {
    editor_parar_edicao();
    editor_selecionar(null);

    editorMapa.desenho = new L.Draw.Polygon(editorMapa.map, {
        allowIntersection: true,
        showArea: false,
        shapeOptions: { color: '#ffffff', fillColor: EDITOR_COR_NOVO, fillOpacity: 0.3, weight: 2 }
    });
    editorMapa.desenho.enable();

    editor_atualizar_botoes();
    editor_aviso('info', 'Clique no mapa para marcar cada ponto do pasto. Para terminar, clique no primeiro ponto (ou dê duplo clique no último). Clique em <strong>Esc</strong> para sair do modo Novo Pasto.');
}

function editor_novo_desenhado(layer) {
    // O contorno fica visivel no mapa enquanto o nome e digitado
    layer.setStyle({ color: '#ffeb3b', weight: 3, fillColor: EDITOR_COR_NOVO, fillOpacity: 0.4 });
    layer.addTo(editorMapa.grupo);

    editorMapa.novoPendente = layer;
    editorMapa.modoNome = 'novo';

    editor_abrir_painel_nome('', 'Nome do novo pasto');
}

function editor_renomear() {
    if (!editorMapa.selecionado) {
        return;
    }

    editorMapa.modoNome = 'renomear';
    editor_abrir_painel_nome(editorMapa.selecionado.nome, 'Novo nome do pasto');
}

function editor_abrir_painel_nome(valor, titulo) {
    clearTimeout(editorTemporizadorAviso);
    editorAvisoEhDica = false;
    $("#editor_mapa_aviso").hide();

    $("#editor_painel_nome label").first().text(titulo);
    var moduloAtual = '';
    var mostrarModulo = (editorMapa.modoNome == 'novo');

    // Ao renomear, o modulo tambem pode ser alterado (ENTRADA/SAIDA ficam sempre no 999)
    if (editorMapa.modoNome == 'renomear' && editorMapa.selecionado) {
        var atualId = editor_modulo_do_item(editorMapa.selecionado);

        mostrarModulo = (atualId !== 999);
        moduloAtual = (atualId === null || atualId === undefined) ? '' : String(atualId);
    }

    $("#editor_grupo_modulo").toggle(mostrarModulo);
    $("#editor_modulo_pasto").selectpicker('val', moduloAtual);
    $("#editor_nome_pasto").val(valor);
    $("#editor_painel_nome").show();
    $("#editor_nome_pasto").trigger('focus');
    editor_ajustar_altura();
    editor_atualizar_botoes();
}

function editor_confirmar_nome() {
    var nome = $("#editor_nome_pasto").val().trim().replace(/\s+/g, ' ');

    if (nome == '') {
        editor_aviso('warning', 'Informe o nome do pasto.');
        return;
    }

    if (nome.length > 60) {
        editor_aviso('warning', 'O nome do pasto pode ter no máximo 60 caracteres.');
        return;
    }

    var atual = editorMapa.modoNome == 'renomear' ? editorMapa.selecionado : null;

    for (var i = 0; i < editorMapa.itens.length; i++) {
        if (editorMapa.itens[i] !== atual && editorMapa.itens[i].nome.toUpperCase() == nome.toUpperCase()) {
            editor_aviso('warning', 'Já existe um pasto chamado "' + nome + '" nesse mapa.');
            return;
        }
    }

    if (editorMapa.modoNome == 'novo') {
        var nomeMaiusculo = nome.toUpperCase();
        var reservado = (nomeMaiusculo == 'ENTRADA' || nomeMaiusculo == 'SAIDA' || nomeMaiusculo == 'SAÍDA');
        var moduloEscolhido = reservado ? 999 : parseInt($("#editor_modulo_pasto").val(), 10);

        if (!reservado && isNaN(moduloEscolhido)) {
            editor_aviso('warning', 'Selecione o módulo do novo pasto.');
            return;
        }

        var item = {
            layer: editorMapa.novoPendente,
            nome: nome,
            nomeOriginal: nome,
            novo: true,
            modulo: moduloEscolhido,
            modificado: true,
            orig: null
        };

        editorMapa.novoPendente = null;
        editor_registrar_item(item);
        editor_selecionar(item);
        $("#editor_lista_pastos").append($("<option>").attr("value", nome));
    }
    else {
        atual.nome = nome;
        atual.layer.setTooltipContent(nome);

        if (atual.novo) {
            atual.nomeOriginal = nome;
        }

        var novoModulo = parseInt($("#editor_modulo_pasto").val(), 10);

        if ($("#editor_grupo_modulo").is(':visible') && !isNaN(novoModulo)) {
            if (atual.novo) {
                atual.modulo = novoModulo;
            }
            else {
                atual.moduloAlterado = (novoModulo === editor_modulo_original(atual)) ? null : novoModulo;
            }
        }

        editor_estilizar(atual);
    }

    $("#editor_painel_nome").hide();
    editor_aviso('info', 'Alteração feita no mapa. Clique em <strong>Salvar alterações</strong> para gravar.', 6);
    editor_atualizar_botoes();
}

function editor_cancelar_nome() {
    if (editorMapa.modoNome == 'novo' && editorMapa.novoPendente) {
        editorMapa.grupo.removeLayer(editorMapa.novoPendente);
        editorMapa.novoPendente = null;
    }

    $("#editor_painel_nome").hide();
    editor_ajustar_altura();
    editor_atualizar_botoes();

    if (editorMapa.selecionado === null) {
        editor_mostrar_dica();
    }
}

function editor_alternar_tracado() {
    var item = editorMapa.selecionado;

    if (!item) {
        return;
    }

    if (editorMapa.editando) {
        editor_parar_edicao();

        if (editor_tem_alteracoes()) {
            editor_aviso('info', 'Alteração feita no mapa. Clique em <strong>Salvar alterações</strong> para gravar.', 6);
        }
        else {
            clearTimeout(editorTemporizadorAviso);
            $("#editor_mapa_aviso").hide();
            editor_ajustar_altura();
        }

        editor_atualizar_botoes();
        return;
    }

    item.layer.editing.enable();
    editorMapa.editando = true;
    $("#editor_btn_tracado").addClass('btn-success').removeClass('btn-default')
        .html('<i class="fa fa-check"></i> Concluir traçado');
    editor_aviso('info', 'Arraste os pontos brancos para mudar o traçado. Arraste os pontos menores do meio das linhas para criar um novo ponto. Clique em um ponto para removê-lo. Ao terminar, clique em <strong>Concluir traçado</strong>.');
    editor_atualizar_botoes();
}

function editor_parar_edicao() {
    if (editorMapa.editando && editorMapa.selecionado) {
        editorMapa.selecionado.layer.editing.disable();
    }

    editorMapa.editando = false;
    $("#editor_btn_tracado").addClass('btn-default').removeClass('btn-success')
        .html('<i class="fa fa-draw-polygon"></i> Editar traçado');
}

function editor_remover_novo() {
    var item = editorMapa.selecionado;

    if (!item || !item.novo) {
        return;
    }

    editor_parar_edicao();
    editorMapa.grupo.removeLayer(item.layer);
    editorMapa.itens.splice(editorMapa.itens.indexOf(item), 1);
    editorMapa.selecionado = null;
    editor_atualizar_botoes();
}

function editor_pedir_exclusao() {
    var item = editorMapa.selecionado;

    if (!item || item.novo) {
        return;
    }

    if (editor_tem_alteracoes()) {
        editor_aviso('warning', 'Salve (ou descarte) as alterações do mapa antes de excluir um pasto.');
        return;
    }

    $("#editor_texto_excluir").text('Excluir o pasto ' + item.nome + '? Só é possível se ele estiver vazio (sem animais). Digite sua senha para confirmar.');
    $("#editor_senha_excluir").val('');
    $("#editor_painel_excluir").show();
    $("#editor_senha_excluir").trigger('focus');
    editor_ajustar_altura();
}

function editor_cancelar_exclusao() {
    $("#editor_senha_excluir").val('');
    $("#editor_painel_excluir").hide();
    editor_ajustar_altura();
}

function editor_confirmar_exclusao() {
    var item = editorMapa.selecionado;
    var senha = $("#editor_senha_excluir").val();

    if (!item) {
        return;
    }

    if (senha == '') {
        editor_aviso('warning', 'Digite sua senha para confirmar a exclusão.');
        return;
    }

    $.ajax({
        type: 'post',
        url: 'mapa_pastos_excluir.php',
        dataType: 'json',
        data: {
            'local': editorMapa.local,
            'versao': editorMapa.versao,
            'nome': item.nome,
            'senha': senha
        },
        success: function(data) {
            $("#editor_senha_excluir").val('');

            if (data.error) {
                editor_aviso('danger', data.message);
                return;
            }

            editor_cancelar_exclusao();

            var local = editorMapa.local;
            $.ajax({
                type: 'post',
                url: 'mapa_pastos_ler.php',
                dataType: 'json',
                data: { 'local': local },
                success: function(recarregado) {
                    if (!recarregado.error) {
                        editor_montar(recarregado, local);
                    }

                    editor_aviso('success', data.message);

                    if (typeof listar_pastos == 'function' && $("#codigo_local_filtro").val() == local) {
                        listar_pastos();
                    }
                }
            });
        },
        error: function() {
            editor_aviso('danger', 'Não foi possível excluir o pasto. Tente novamente.');
        }
    });
}

function editor_salvar() {
    if (editorMapa.editando) {
        editor_parar_edicao();
    }

    var renomeados = [];

    editorMapa.itens.forEach(function(it) {
        if (!it.novo && it.nome.toUpperCase() != it.nomeOriginal.toUpperCase()) {
            renomeados.push({ de: it.nomeOriginal, para: it.nome });
        }
    });

    // novos: pastos que serao criados; alterados: pastos ja cadastrados que mudaram de modulo
    var novos = {};
    var alterados = {};

    editorMapa.itens.forEach(function(it) {
        var chave = it.nome.toUpperCase();

        if (it.novo) {
            novos[chave] = it.modulo;
        }
        else if (it.moduloAlterado !== undefined && it.moduloAlterado !== null) {
            if (editor_status(it) == 'ok') {
                alterados[chave] = it.moduloAlterado;
            }
            else {
                novos[chave] = it.moduloAlterado;
            }
        }
    });

    $("#editor_btn_salvar").prop('disabled', true);
    editor_aviso('info', 'Gravando o mapa...');

    $.ajax({
        type: 'post',
        url: 'mapa_pastos_gravar.php',
        dataType: 'json',
        data: {
            'local': editorMapa.local,
            'versao': editorMapa.versao,
            'geojson': JSON.stringify(editor_serializar()),
            'renomeados': JSON.stringify(renomeados),
            'novos': JSON.stringify(novos),
            'alterados': JSON.stringify(alterados)
        },
        success: function(data) {
            if (data.error) {
                editor_aviso('danger', data.message);
                editor_atualizar_botoes();
                return;
            }

            var linhas = ['Mapa gravado com sucesso.'];

            if (data.criados.length > 0) {
                linhas.push('Pastos criados: ' + data.criados.join(', '));
            }

            if (data.renomeados.length > 0) {
                linhas.push('Renomeados: ' + data.renomeados.join(', '));
            }

            if (data.modulos.length > 0) {
                linhas.push('Módulo alterado: ' + data.modulos.join(', '));
            }

            if (data.atualizados.length > 0) {
                linhas.push('Área atualizada: ' + data.atualizados.join(', '));
            }

            var local = editorMapa.local;
            $.ajax({
                type: 'post',
                url: 'mapa_pastos_ler.php',
                dataType: 'json',
                data: { 'local': local },
                success: function(recarregado) {
                    if (!recarregado.error) {
                        editor_montar(recarregado, local);
                    }

                    editor_aviso('success', linhas.join('<br>'));

                    if (typeof listar_pastos == 'function' && $("#codigo_local_filtro").val() == local) {
                        listar_pastos();
                    }
                }
            });
        },
        error: function() {
            editor_aviso('danger', 'Não foi possível gravar o mapa. Tente novamente.');
            editor_atualizar_botoes();
        }
    });
}

function fechar_editor_mapa() {
    editor_confirmar_saida(function() {
        editor_limpar_tudo();
        $("#editor_mapa_tela").hide();
        $("#pastos_cabecalho, #pastos_conteudo").show();
        window.scrollTo(0, 0);
    });
}
