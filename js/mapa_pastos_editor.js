/** EDITOR DE MAPA DOS PASTOS (Leaflet + Leaflet.Draw) */
var editorMapa = {
    map: null,
    grupo: null,
    itens: [],
    pontos: [],
    pastosSistema: [],
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

// Mensagens de sucesso somem sozinhas; segundos pode ser informado para outros tipos
function editor_aviso(tipo, html, segundos) {
    clearTimeout(editorTemporizadorAviso);

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
        }, segundos * 1000);
    }
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

function abrir_editor_mapa() {
    $("#editor_local").val('');
    $("#pastos_cabecalho, #pastos_conteudo").hide();
    $("#editor_mapa_tela").show();
    editor_ajustar_altura();
    editor_preparar_mapa(function() {
        editor_ajustar_altura();
    });
}

// Este script e carregado antes do jQuery (rodape.php), entao o registro espera o load da pagina
window.addEventListener('load', function() {
    $(window).on('resize', editor_ajustar_altura);
});

window.addEventListener('beforeunload', function(ev) {
    if (editor_tem_alteracoes()) {
        ev.preventDefault();
        ev.returnValue = '';
    }
});

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

    mapa.on(L.Draw.Event.CREATED, function(ev) {
        editorMapa.desenho = null;
        $("#editor_btn_novo").removeClass('active');
        editor_novo_desenhado(ev.layer);
    });

    mapa.on('draw:drawstop', function() {
        editorMapa.desenho = null;
        $("#editor_btn_novo").removeClass('active');
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

    if (editor_tem_alteracoes() && !confirm('Existem alterações não salvas. Deseja descartá-las e carregar outro mapa?')) {
        $("#editor_local").val(editorMapa.local);
        return;
    }

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

    var texto = editorMapa.itens.length + ' pasto(s) no mapa.';

    if (semCadastro > 0) {
        texto += ' <strong>' + semCadastro + '</strong> sem cadastro no sistema (em laranja) serão criados ao salvar.';
    }

    if (editorMapa.itens.length == 0) {
        texto = 'Essa fazenda ainda não tem mapa. Use <strong>Novo pasto</strong> para desenhar o primeiro (comece por ENTRADA e SAIDA).';
    }

    editor_aviso('info', texto);
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

    return editorMapa.pastosSistema.indexOf(nome) !== -1 ? 'ok' : 'sem';
}

function editor_estilizar(item) {
    var status = editor_status(item);
    var cor = status == 'novo' ? EDITOR_COR_NOVO : (status == 'sem' ? EDITOR_COR_SEM_CADASTRO : EDITOR_COR_OK);
    var selecionado = (editorMapa.selecionado === item);

    item.layer.setStyle({
        color: selecionado ? '#ffeb3b' : '#ffffff',
        weight: selecionado ? 4 : 1.5,
        fillColor: cor,
        fillOpacity: selecionado ? 0.5 : 0.3
    });

    if (selecionado) {
        item.layer.bringToFront();
    }
}

function editor_selecionar(item) {
    editor_parar_edicao();

    var anterior = editorMapa.selecionado;
    editorMapa.selecionado = item;

    if (anterior !== item) {
        editor_cancelar_exclusao();
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

    return JSON.stringify(editor_serializar()) !== editorMapa.snapshot;
}

function editor_atualizar_botoes() {
    var item = editorMapa.selecionado;
    var pronto = editorMapa.local != '';
    var ocupado = editorMapa.desenho !== null || editorMapa.novoPendente !== null;

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

        $("#editor_info").text(item.nome + ' — ' + ha.toFixed(2).replace('.', ',') + ' ha — ' + textoStatus);
    }
    else {
        $("#editor_info").text('Clique em um pasto no mapa para selecioná-lo.');
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

    $("#editor_btn_novo").addClass('active');
    editor_atualizar_botoes();
    editor_aviso('info', 'Clique no mapa para marcar cada ponto do pasto. Para terminar, clique no primeiro ponto (ou dê duplo clique no último).');
}

function editor_novo_desenhado(layer) {
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
    $("#editor_painel_nome label").text(titulo);
    $("#editor_nome_pasto").val(valor);
    $("#editor_painel_nome").show();
    $("#editor_nome_pasto").trigger('focus');
    editor_ajustar_altura();
    editor_atualizar_botoes();
}

function editor_confirmar_nome() {
    var nome = $("#editor_nome_pasto").val().trim().toUpperCase().replace(/\s+/g, ' ');

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
        if (editorMapa.itens[i] !== atual && editorMapa.itens[i].nome.toUpperCase() == nome) {
            editor_aviso('warning', 'Já existe um pasto chamado "' + nome + '" nesse mapa.');
            return;
        }
    }

    if (editorMapa.modoNome == 'novo') {
        var item = {
            layer: editorMapa.novoPendente,
            nome: nome,
            nomeOriginal: nome,
            novo: true,
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
    }

    $("#editor_painel_nome").hide();
    editor_aviso('info', 'Alteração feita no mapa. Clique em <strong>Salvar alterações</strong> para gravar.', 6);
    editor_atualizar_botoes();
}

function editor_cancelar_nome() {
    if (editorMapa.modoNome == 'novo' && editorMapa.novoPendente) {
        editorMapa.novoPendente = null;
    }

    $("#editor_painel_nome").hide();
    editor_ajustar_altura();
    editor_atualizar_botoes();
}

function editor_alternar_tracado() {
    var item = editorMapa.selecionado;

    if (!item) {
        return;
    }

    if (editorMapa.editando) {
        editor_parar_edicao();
        $("#editor_mapa_aviso").hide();
        editor_ajustar_altura();
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
            'renomeados': JSON.stringify(renomeados)
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
    if (editor_tem_alteracoes() && !confirm('Existem alterações não salvas. Deseja sair mesmo assim?')) {
        return;
    }

    editor_parar_edicao();

    if (editorMapa.desenho) {
        editorMapa.desenho.disable();
        editorMapa.desenho = null;
    }

    editorMapa.local = '';
    $("#editor_mapa_tela").hide();
    $("#pastos_cabecalho, #pastos_conteudo").show();
    window.scrollTo(0, 0);
}
