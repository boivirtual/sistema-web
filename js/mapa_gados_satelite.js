/** MAPA DE GADO - SATELITE (Leaflet) */
var mapaGadoSatelite = {
    map: null,
    poligonos: [],
    dragHoverLayer: null,
    origemToque: null,
    localDesenhado: null
};

// Transferencia de animais por arraste no mapa satelite - mesma logica/telas do Mapa Tabuleiro
// (drag() e drop() sao as funcoes globais do Tabuleiro, em mapa_gados.js; aqui so criamos os
// elementos de origem (icones arrastaveis) e destino (poligono) no mesmo formato que elas esperam.

// Desabilitar o dragging do mapa so' no mousedown do icone nao e' suficiente: o Leaflet ainda assim
// comeca a arrastar o mapa (reage a outro evento alem do mousedown). Por isso desabilitamos assim que
// o MOUSE ENTRA na area do icone (antes de qualquer clique) e so' devolvemos ao normal quando o mouse
// sai dali - mas nunca enquanto o botao estiver pressionado (senao um arraste que passa por cima de
// varios elementos reativaria o pan do mapa no meio do proprio arraste).
var _satelitePanSuspenso = false;
var _sateliteBotaoPressionado = false;
var SATELITE_SELETOR_ARRASTAVEL = '.satelite-pasto-badge .linha, .satelite-pasto-badge .total';

function _sateliteSuspenderPan() {
    if (mapaGadoSatelite.map && mapaGadoSatelite.map.dragging.enabled()) {
        mapaGadoSatelite.map.dragging.disable();
        _satelitePanSuspenso = true;
    }
}

function _sateliteRetomarPan() {
    if (_satelitePanSuspenso && mapaGadoSatelite.map) {
        mapaGadoSatelite.map.dragging.enable();
        _satelitePanSuspenso = false;
    }
}

document.addEventListener('mousedown', function(ev) {
    _sateliteBotaoPressionado = true;

    if (ev.target.closest && ev.target.closest(SATELITE_SELETOR_ARRASTAVEL)) {
        ev.stopPropagation();
        _sateliteSuspenderPan();
    }
}, true);

document.addEventListener('mouseup', function() {
    _sateliteBotaoPressionado = false;
    _sateliteRetomarPan();
});

$(document).on('mouseenter', SATELITE_SELETOR_ARRASTAVEL, _sateliteSuspenderPan);
$(document).on('mouseleave', SATELITE_SELETOR_ARRASTAVEL, function() {
    // enquanto o botao estiver pressionado, quem decide quando retomar o pan e' o mouseup/dragend
    if (!_sateliteBotaoPressionado) {
        _sateliteRetomarPan();
    }
});

$(document).on('dragend', SATELITE_SELETOR_ARRASTAVEL, function() {
    _sateliteBotaoPressionado = false;
    _sateliteRetomarPan();

    if (mapaGadoSatelite.dragHoverLayer) {
        mapaGadoSatelite.dragHoverLayer.setStyle(mapa_gado_satelite_estilo_padrao(mapaGadoSatelite.dragHoverLayer._nomePasto));
        mapaGadoSatelite.dragHoverLayer = null;
    }
});

// Auto-rolagem do mapa durante o arraste: com zoom aproximado o pasto de destino pode estar fora da
// tela; segurando o icone perto da borda do mapa, ele se move naquela direcao (como o Tabuleiro faz
// com a rolagem da pagina). Mais perto da borda = mais rapido.
var SATELITE_BORDA_AUTOPAN = 80;
var SATELITE_VELOCIDADE_AUTOPAN = 22;
var _sateliteArrasteAtivo = false;
var _sateliteAutoPanTimer = null;
var _sateliteAutoPanVetor = { x: 0, y: 0 };

function _sateliteParaAutoPan() {
    clearInterval(_sateliteAutoPanTimer);
    _sateliteAutoPanTimer = null;
    _sateliteAutoPanVetor = { x: 0, y: 0 };
}

function _sateliteIntensidadeBorda(pos, inicio, fim) {
    if (pos < inicio + SATELITE_BORDA_AUTOPAN) {
        return -Math.min(1, (inicio + SATELITE_BORDA_AUTOPAN - pos) / SATELITE_BORDA_AUTOPAN);
    }
    if (pos > fim - SATELITE_BORDA_AUTOPAN) {
        return Math.min(1, (pos - (fim - SATELITE_BORDA_AUTOPAN)) / SATELITE_BORDA_AUTOPAN);
    }
    return 0;
}

document.addEventListener('dragstart', function(ev) {
    _sateliteArrasteAtivo = !!(ev.target.closest && ev.target.closest(SATELITE_SELETOR_ARRASTAVEL));
}, true);

// Liga/desliga a auto-rolagem conforme a posicao do cursor (usada pelos dois tipos de arraste: o dos
// icones - HTML5 - e o do corpo do pasto - ver satelite_iniciar_arraste_pasto)
function _sateliteAtualizarAutoPan(clientX, clientY) {
    if (!mapaGadoSatelite.map) {
        return;
    }

    var r = mapaGadoSatelite.map.getContainer().getBoundingClientRect();
    var dentro = clientX >= r.left && clientX <= r.right && clientY >= r.top && clientY <= r.bottom;
    var ix = dentro ? _sateliteIntensidadeBorda(clientX, r.left, r.right) : 0;
    var iy = dentro ? _sateliteIntensidadeBorda(clientY, r.top, r.bottom) : 0;

    _sateliteAutoPanVetor = { x: ix * SATELITE_VELOCIDADE_AUTOPAN, y: iy * SATELITE_VELOCIDADE_AUTOPAN };

    if (ix === 0 && iy === 0) {
        clearInterval(_sateliteAutoPanTimer);
        _sateliteAutoPanTimer = null;
    }
    else if (_sateliteAutoPanTimer === null) {
        _sateliteAutoPanTimer = setInterval(function() {
            if (mapaGadoSatelite.map) {
                mapaGadoSatelite.map.panBy([_sateliteAutoPanVetor.x, _sateliteAutoPanVetor.y], { animate: false });
                // o mapa andou com o cursor parado: o pasto sob o cursor pode ter mudado
                _sateliteAtualizarAlvoPasto();
            }
        }, 30);
    }
}

document.addEventListener('dragover', function(ev) {
    if (_sateliteArrasteAtivo) {
        _sateliteAtualizarAutoPan(ev.clientX, ev.clientY);
    }
}, true);

['dragend', 'drop'].forEach(function(evento) {
    document.addEventListener(evento, function() {
        _sateliteArrasteAtivo = false;
        _sateliteParaAutoPan();
    }, true);
});

// Arraste pelo CORPO do pasto (clicar e arrastar em qualquer ponto do pasto com animais), alem do
// arraste pelos icones. Um elemento SVG nao e' arrastavel pelo HTML5, entao aqui o arraste e' feito
// com o mouse (mousedown/mousemove/mouseup) e, ao soltar sobre outro pasto, chama a mesma drop() do
// Tabuleiro com um "evento" que traz o id e o nome da origem no mesmo formato do dataTransfer.
var _sateliteArrastePasto = null;
var _sateliteSuprimirClique = false;
var SATELITE_DISTANCIA_INICIO_ARRASTE = 6;

function satelite_iniciar_arraste_pasto(ev, poligono) {
    // no "Mover por toque" o clique seleciona origem/destino; aqui nao ha arraste
    if (ev.button !== 0 || (typeof _tabuleiroModoToque !== 'undefined' && _tabuleiroModoToque)) {
        return;
    }

    // sem isso o mapa comeca a se mover (pan) junto com o clique, e o navegador seleciona texto
    ev.preventDefault();
    L.DomEvent.stopPropagation(ev);

    _sateliteArrastePasto = {
        origem: poligono,
        x0: ev.clientX,
        y0: ev.clientY,
        x: ev.clientX,
        y: ev.clientY,
        ativo: false,
        fantasma: null,
        alvo: null
    };
}

function _sateliteAtualizarAlvoPasto() {
    var st = _sateliteArrastePasto;

    if (!st || !st.ativo || !mapaGadoSatelite.map) {
        return;
    }

    var map = mapaGadoSatelite.map;
    var r = map.getContainer().getBoundingClientRect();
    var alvo = null;

    if (st.x >= r.left && st.x <= r.right && st.y >= r.top && st.y <= r.bottom) {
        var ponto = map.containerPointToLayerPoint(L.point(st.x - r.left, st.y - r.top));

        for (var i = 0; i < mapaGadoSatelite.poligonos.length; i++) {
            var camada = mapaGadoSatelite.poligonos[i].layer;

            if (camada !== st.origem && camada._elementoToque && camada._containsPoint(ponto)) {
                alvo = camada;
                break;
            }
        }
    }

    if (alvo !== st.alvo) {
        if (st.alvo) {
            st.alvo.setStyle(mapa_gado_satelite_estilo_padrao(st.alvo._nomePasto));
        }
        if (alvo) {
            alvo.setStyle({ color: '#128cb8', weight: 4 });
        }
        st.alvo = alvo;
    }
}

function _sateliteEncerrarArrastePasto() {
    var st = _sateliteArrastePasto;
    _sateliteArrastePasto = null;
    _sateliteParaAutoPan();
    document.body.classList.remove('satelite-arrastando-pasto');

    if (!st) {
        return null;
    }

    if (st.fantasma) {
        st.fantasma.remove();
    }
    if (st.ativo) {
        st.origem.setStyle(mapa_gado_satelite_estilo_padrao(st.origem._nomePasto));
        if (st.alvo) {
            st.alvo.setStyle(mapa_gado_satelite_estilo_padrao(st.alvo._nomePasto));
        }
    }

    return st;
}

document.addEventListener('mousemove', function(ev) {
    var st = _sateliteArrastePasto;

    if (!st) {
        return;
    }

    st.x = ev.clientX;
    st.y = ev.clientY;

    if (!st.ativo) {
        if (Math.abs(st.x - st.x0) < SATELITE_DISTANCIA_INICIO_ARRASTE && Math.abs(st.y - st.y0) < SATELITE_DISTANCIA_INICIO_ARRASTE) {
            return;
        }

        st.ativo = true;
        st.fantasma = document.createElement('div');
        st.fantasma.className = 'satelite-arraste-fantasma';
        st.fantasma.textContent = st.origem._nomePasto + ' - ' + st.origem._infoAnimal.total_animais + ' animais';
        document.body.appendChild(st.fantasma);
        document.body.classList.add('satelite-arrastando-pasto');
        st.origem.setStyle(SATELITE_ESTILO_ORIGEM_TOQUE);
    }

    st.fantasma.style.left = st.x + 'px';
    st.fantasma.style.top = st.y + 'px';

    _sateliteAtualizarAutoPan(st.x, st.y);
    _sateliteAtualizarAlvoPasto();
});

document.addEventListener('mouseup', function() {
    var st = _sateliteEncerrarArrastePasto();

    if (!st || !st.ativo) {
        // Escape cancelou o arraste: o mouseup (e o click) que vem depois nao pode entrar no pasto
        if (_sateliteSuprimirClique) {
            setTimeout(function() { _sateliteSuprimirClique = false; }, 0);
        }
        return; // senao foi so' um clique: segue o fluxo normal (entrar no pasto)
    }

    // o navegador ainda dispara o "click" logo apos o mouseup: nao pode contar como clique no pasto
    _sateliteSuprimirClique = true;
    setTimeout(function() { _sateliteSuprimirClique = false; }, 0);

    if (st.alvo) {
        var origem = st.origem;
        var dados = { text: origem._elementoToque.id, nome: origem._nomePasto };

        drop({
            preventDefault: function() {},
            dataTransfer: { getData: function(chave) { return dados[chave] || ''; } }
        }, st.alvo._elementoToque.id, st.alvo._elementoToque);
    }
});

document.addEventListener('keydown', function(ev) {
    if (ev.key === 'Escape' && _sateliteArrastePasto) {
        var st = _sateliteEncerrarArrastePasto();
        if (st && st.ativo) {
            _sateliteSuprimirClique = true;
        }
    }
});

// Estilo do poligono considerando o termo de busca atual (usado tambem para "desfazer" o destaque de arraste)
function mapa_gado_satelite_estilo_padrao(nome) {
    var termo = ($('#buscar_pasto_tabuleiro').val() || '').toUpperCase();
    var combina = termo !== '' && nome.indexOf(termo) !== -1;

    // dashArray: null remove o pontilhado do pasto de origem do arraste/toque (setStyle so' mexe no que for informado)
    return {
        color: combina ? '#ffeb3b' : '#ffffff',
        weight: combina ? 3 : 1,
        dashArray: null
    };
}

function carregar_mapa_satelite_gado() {
    if (!$("#map").is(':visible')) {
        return; // sera carregado quando a aba Mapa Satelite for aberta
    }

    var local = $("#codigo_local").val();

    if (local == 0 || local == '') {
        local = $("#local_sessao").val();
    }

    if (!local) {
        return;
    }

    $.ajax({
        type: 'post',
        url: 'ler_animal_pasto_mapa_satelite.php',
        data: { local: local },
        dataType: 'json'
    }).done(function(pastosAnimais) {
        $.ajax({
            type: 'post',
            url: 'mapa_gados_satelite_ler.php',
            data: { local: local },
            dataType: 'json'
        }).done(function(dadosMapa) {
            if (dadosMapa.error) {
                return;
            }

            mapa_gado_satelite_desenhar(pastosAnimais, dadosMapa, local);
        });
    });
}

function mapa_gado_satelite_modulo(dadosMapa, id) {
    for (var i = 0; i < dadosMapa.modulos.length; i++) {
        if (dadosMapa.modulos[i].id == id) {
            return dadosMapa.modulos[i];
        }
    }

    return null;
}

// Centro geometrico do poligono (formula do "shoelace"). anel: lista de [lng, lat, alt].
function mapa_gado_satelite_centroide(anel) {
    var area = 0, cx = 0, cy = 0;

    for (var i = 0; i < anel.length - 1; i++) {
        var cruz = anel[i][0] * anel[i + 1][1] - anel[i + 1][0] * anel[i][1];
        area += cruz;
        cx += (anel[i][0] + anel[i + 1][0]) * cruz;
        cy += (anel[i][1] + anel[i + 1][1]) * cruz;
    }

    area = area / 2;

    if (Math.abs(area) < 1e-12) {
        // poligono degenerado (area ~0): usa a media simples dos pontos em vez de dividir por zero
        var somaLat = 0, somaLng = 0, qtd = anel.length - 1;

        for (var j = 0; j < qtd; j++) {
            somaLng += anel[j][0];
            somaLat += anel[j][1];
        }

        return [somaLat / qtd, somaLng / qtd];
    }

    return [cy / (6 * area), cx / (6 * area)]; // [lat, lng]
}

function mapa_gado_satelite_desenhar(pastosAnimais, dadosMapa, local) {
    // Recarga da MESMA fazenda (ex.: apos confirmar uma transferencia): guarda onde o usuario estava
    // (centro e zoom) para devolver o mapa exatamente ali, em vez de voltar ao zoom de entrada.
    var vistaAnterior = null;

    if (mapaGadoSatelite.map !== null) {
        if (mapaGadoSatelite.localDesenhado === local) {
            vistaAnterior = { centro: mapaGadoSatelite.map.getCenter(), zoom: mapaGadoSatelite.map.getZoom() };
        }
        mapaGadoSatelite.map.remove();
        mapaGadoSatelite.map = null;
    }

    mapaGadoSatelite.localDesenhado = local;

    mapaGadoSatelite.poligonos = [];
    mapaGadoSatelite.dragHoverLayer = null;
    mapaGadoSatelite.origemToque = null;

    if (!pastosAnimais.length) {
        return;
    }

    var latitude = parseFloat(pastosAnimais[0].latitude);
    var longitude = parseFloat(pastosAnimais[0].longitude);

    // O Leaflet so' cria de verdade os elementos (path/SVG) dos poligonos que forem adicionados DEPOIS
    // de existir uma view - sem uma posicao inicial aqui, os pastos ficam "pendurados" sem elemento
    // proprio ate' a primeira setView, e o arraste (que depende do path de cada poligono) nao funciona.
    // Por isso a view inicial continua existindo, so' que sem animar a troca para a posicao final
    // (ajustar_zoom_fazenda), o usuario nunca chega a ver essa posicao provisoria na tela.
    var map = L.map('map', { zoomSnap: 0.5 }).setView([latitude, longitude], 13, { animate: false });
    mapaGadoSatelite.map = map;

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxNativeZoom: 18,
        maxZoom: 20,
        attribution: 'Imagens &copy; Esri, Maxar, Earthstar Geographics'
    }).addTo(map);

    // Nomes de cidades, rios, lagos e estradas sobre o satelite (mesmas camadas de referencia da Esri do
    // Editor de Mapa). Ficam num pane proprio, sem receber clique, para nao atrapalhar os pastos.
    map.createPane('rotulos');
    map.getPane('rotulos').style.zIndex = 450;
    map.getPane('rotulos').style.pointerEvents = 'none';

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
        pane: 'rotulos',
        maxNativeZoom: 15,
        maxZoom: 20,
        attribution: 'Nomes &copy; Esri'
    }).addTo(map);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', {
        pane: 'rotulos',
        maxNativeZoom: 14,
        maxZoom: 20
    }).addTo(map);

    // Ajusta o zoom para caber certinho nos limites dos pastos da fazenda, em vez de um zoom fixo
    // (que deixava tudo minusculo e ilegivel em fazendas grandes ou bem espalhadas). Sempre sem
    // animacao: como isso roda logo na abertura do mapa (e de novo apos o invalidateSize), animar
    // faria o mapa parecer "saltar" de posicao em vez de simplesmente aparecer no lugar certo.
    function ajustar_zoom_fazenda() {
        if (mapaGadoSatelite.poligonos.length) {
            var grupo = L.featureGroup(mapaGadoSatelite.poligonos.map(function(p) { return p.layer; }));
            map.fitBounds(grupo.getBounds(), { padding: [20, 20], maxZoom: 17, animate: false });

            // Se a fazenda tiver algum pasto bem isolado (ex.: Entrada/Saida longe do resto), o
            // fitBounds abriria demais o zoom so' para encaixar ele. Nesse caso preferimos manter
            // um zoom legivel no grupo principal, mesmo que o pasto isolado fique fora da tela inicial.
            // Zoom 14 e' o menor aceito: ja' mostra a fazenda inteira (fonte e selos se adaptam ao zoom).
            if (map.getZoom() < 14) {
                map.setZoom(14, { animate: false });
            }
        }
        else {
            map.setView([latitude, longitude], 13, { animate: false });
        }

        // Zoom de entrada: e' a "visao geral" da fazenda (ver modo_visao_geral)
        zoomInicial = map.getZoom();

        if (vistaAnterior) {
            map.setView(vistaAnterior.centro, vistaAnterior.zoom, { animate: false });
        }

        aplicar_escala_zoom(map.getZoom());
        atualizar_rotulos_zoom();
    }

    // Visao geral = zoom afastado (abaixo do 16) ou o mesmo zoom em que o mapa abriu (vale para fazendas
    // pequenas que abrem ja' aproximadas). Nela: sem nome dos pastos e selo so' com as bolinhas das categorias.
    var zoomInicial = null;
    function modo_visao_geral(zoom) {
        return zoom < 16 || (zoomInicial !== null && zoom <= zoomInicial);
    }

    // Fontes (nomes dos pastos) e icones das categorias acompanham o zoom: o CSS le --sat-escala.
    // Zoom 16 = tamanho original (1); a cada nivel de zoom varia 25%, limitado entre 0.45 e 1.6.
    function aplicar_escala_zoom(zoom) {
        var escala = Math.min(1.6, Math.max(0.45, 1 + (zoom - 16) * 0.25));
        map.getContainer().style.setProperty('--sat-escala', escala);
        L.DomUtil[modo_visao_geral(zoom) ? 'addClass' : 'removeClass'](map.getContainer(), 'satelite-zoom-baixo');
    }

    // Rotulo so' aparece se o nome couber dentro do proprio pasto na tela; os que nao cabem ficam
    // ocultos (aparecem ao passar o mouse sobre o pasto) para nao virar uma pilha de textos sobrepostos
    function atualizar_rotulos_zoom() {
        var medidas = [];
        mapaGadoSatelite.poligonos.forEach(function(p) {
            var tooltip = p.layer.getTooltip();
            var el = tooltip ? tooltip.getElement() : null;
            if (!el) { return; }
            L.DomUtil.removeClass(el, 'satelite-rotulo-oculto');
            tooltip.update();
            var limites = p.layer.getBounds();
            var a = map.latLngToContainerPoint(limites.getNorthWest());
            var b = map.latLngToContainerPoint(limites.getSouthEast());
            // na visao geral nenhum pasto mostra o nome (aparece ao passar o mouse)
            var mostrar = !modo_visao_geral(map.getZoom()) &&
                el.offsetWidth <= Math.abs(b.x - a.x) * 0.8;
            medidas.push({ el: el, cabe: mostrar });
        });
        medidas.forEach(function(m) {
            if (!m.cabe) { L.DomUtil.addClass(m.el, 'satelite-rotulo-oculto'); }
        });
    }

    // zoomanim traz o zoom de destino: ja' troca o tamanho no inicio da animacao em vez de so' no fim
    map.on('zoomanim', function(ev) {
        aplicar_escala_zoom(ev.zoom);
    });

    // No fim do zoom reaplica e recentraliza os rotulos (o Leaflet centraliza pelo tamanho medido do texto)
    map.on('zoomend', function() {
        aplicar_escala_zoom(map.getZoom());
        atualizar_rotulos_zoom();
    });

    // O container pode ainda nao ter o tamanho definitivo no instante da criacao (troca de aba,
    // aba escondida no carregamento da pagina); sem isso o mapa pode desenhar tudo torto.
    setTimeout(function() {
        map.invalidateSize({ animate: false });
        ajustar_zoom_fazenda();
    }, 0);

    var features = (dadosMapa.geojson && dadosMapa.geojson.features) ? dadosMapa.geojson.features : [];

    features.forEach(function(f) {
        if (!f.geometry || f.geometry.type != 'Polygon') {
            return;
        }

        var nome = (f.properties.name || '').toUpperCase();
        var anel = f.geometry.coordinates[0];
        var latlngs = anel.slice(0, anel.length - 1).map(function(p) { return [p[1], p[0]]; });

        var moduloId = dadosMapa.pastos[nome];
        var modulo = (moduloId !== undefined) ? mapa_gado_satelite_modulo(dadosMapa, moduloId) : null;
        var cor = modulo ? modulo.cor : '#9E9E9E';

        var poligono = L.polygon(latlngs, {
            color: '#ffffff',
            weight: 1,
            fillColor: cor,
            fillOpacity: 0.5
        }).addTo(map);

        poligono.bindTooltip(nome, { permanent: true, direction: 'center', className: 'editor-pasto-label' });

        mapaGadoSatelite.poligonos.push({ nome: nome, layer: poligono });

        // rotulo oculto por nao caber no pasto (ver atualizar_rotulos_zoom) aparece enquanto o mouse estiver sobre ele
        poligono.on('mouseover', function() {
            var el = poligono.getTooltip().getElement();
            if (el && L.DomUtil.hasClass(el, 'satelite-rotulo-oculto')) {
                L.DomUtil.removeClass(el, 'satelite-rotulo-oculto');
                el.dataset.revelado = '1';
            }
        });
        poligono.on('mouseout', function() {
            var el = poligono.getTooltip().getElement();
            if (el && el.dataset.revelado) {
                delete el.dataset.revelado;
                L.DomUtil.addClass(el, 'satelite-rotulo-oculto');
            }
        });

        var infoAnimal = null;

        for (var i = 0; i < pastosAnimais.length; i++) {
            if (pastosAnimais[i].descricao == nome) {
                infoAnimal = pastosAnimais[i];
                break;
            }
        }

        poligono.on('click', function(ev) {
            if (_sateliteSuprimirClique) {
                return;
            }

            // Mover por toque (alternativa ao arraste, mesma regra/tela do Tabuleiro): enquanto o modo
            // estiver ativo, o clique no pasto seleciona origem/destino em vez de entrar no pasto.
            if (typeof _tabuleiroModoToque !== 'undefined' && _tabuleiroModoToque) {
                satelite_selecionar_pasto_toque(poligono, infoAnimal);
                return;
            }

            if (infoAnimal) {
                mais_info_mapa_satelite('"' + infoAnimal.id_pasto + '"');
            }
        });

        // Pasto como destino do arraste (e tambem origem/destino do "Mover por toque"): usa as mesmas
        // drop()/mover_tabuleiro_toque() do Tabuleiro (js/mapa_gados.js), passando um elemento "por
        // fora" (nao inserido na tela) so' para dar a ele o id e o <strong> que essas funcoes leem.
        if (infoAnimal && poligono._path) {
            poligono._nomePasto = nome;
            poligono._infoAnimal = infoAnimal;

            var elementoDestino = document.createElement('div');
            elementoDestino.id = '"' + infoAnimal.id_pasto + '"';
            elementoDestino.appendChild(document.createElement('strong')).innerHTML = nome;
            poligono._elementoToque = elementoDestino;

            L.DomEvent.on(poligono._path, 'dragover', function(ev) {
                ev.preventDefault();

                if (mapaGadoSatelite.dragHoverLayer !== poligono) {
                    if (mapaGadoSatelite.dragHoverLayer) {
                        mapaGadoSatelite.dragHoverLayer.setStyle(mapa_gado_satelite_estilo_padrao(mapaGadoSatelite.dragHoverLayer._nomePasto));
                    }

                    mapaGadoSatelite.dragHoverLayer = poligono;
                    poligono.setStyle({ color: '#128cb8', weight: 4 });
                }
            });

            L.DomEvent.on(poligono._path, 'dragleave', function(ev) {
                if (mapaGadoSatelite.dragHoverLayer === poligono) {
                    poligono.setStyle(mapa_gado_satelite_estilo_padrao(nome));
                    mapaGadoSatelite.dragHoverLayer = null;
                }
            });

            L.DomEvent.on(poligono._path, 'drop', function(ev) {
                poligono.setStyle(mapa_gado_satelite_estilo_padrao(nome));
                mapaGadoSatelite.dragHoverLayer = null;

                drop(ev, elementoDestino.id, elementoDestino);
            });

            // Clicar e arrastar em qualquer ponto de um pasto COM animais move os animais (mesma regra do
            // Tabuleiro: pasto vazio nao e' origem de arraste).
            if (infoAnimal.tem_animal == 'S') {
                L.DomEvent.on(poligono._path, 'mousedown', function(ev) {
                    satelite_iniciar_arraste_pasto(ev, poligono);
                });
            }
        }

        if (infoAnimal && infoAnimal.tem_animal == 'S') {
            var centro = mapa_gado_satelite_centroide(anel);
            var linhas = '';

            // id no mesmo formato usado pelo Tabuleiro (com aspas literais - drag()/drop() dependem disso)
            var idOrigem = "id='\"" + infoAnimal.id_pasto + "\"'";

            // mesmos icones e a mesma regra do Tabuleiro: só mostra a linha da categoria que tiver animal.
            // Cada elemento chama drag(event, ancestor) diretamente, passando o "cartao" do selo (que
            // tem o <strong> com o nome do pasto) - sem depender do evento borbulhar ate' um segundo
            // ondragstart no elemento pai.
            //
            // ACHADO (via log de diagnostico): a <img> e' arrastavel por padrao do navegador. Quando o
            // clique cai bem em cima dela (nao na linha ao redor), o navegador assume o arraste NATIVO
            // da imagem em vez do nosso - o evento ainda borbulha e drag() ainda roda, mas ev.target vira
            // a <img> (sem id), entao dataTransfer.setData("text", "") grava vazio e o id do pasto de
            // origem chega em branco no servidor. draggable="false" na imagem resolve isso.
            var ORIGEM_ANCESTOR = "this.closest('.satelite-pasto-badge')";

            if (infoAnimal.bezerros != 0) {
                linhas += '<div class="linha" draggable="true" ondragstart="drag(event, ' + ORIGEM_ANCESTOR + ')" ' + idOrigem + '><span class="icone-animal bezerro"><img src="img/bezerro.png" draggable="false"><span class="qtd">' + infoAnimal.bezerros + '</span></span></div>';
            }

            if (infoAnimal.femeas != 0) {
                linhas += '<div class="linha" draggable="true" ondragstart="drag(event, ' + ORIGEM_ANCESTOR + ')" ' + idOrigem + '><span class="icone-animal femea"><img src="img/vaca.png" draggable="false"><span class="qtd">' + infoAnimal.femeas + '</span></span></div>';
            }

            if (infoAnimal.machos != 0) {
                linhas += '<div class="linha" draggable="true" ondragstart="drag(event, ' + ORIGEM_ANCESTOR + ')" ' + idOrigem + '><span class="icone-animal macho"><img src="img/gado.png" draggable="false"><span class="qtd">' + infoAnimal.machos + '</span></span></div>';
            }

            // total logo apos as categorias, centralizado verticalmente - igual ao card do Tabuleiro
            var html = '<div class="satelite-pasto-badge">' +
                '<strong style="display:none">' + nome + '</strong>' +
                '<div class="satelite-badge-categorias">' + linhas + '</div>' +
                '<div class="satelite-badge-divisor"></div>' +
                '<div class="total" draggable="true" ondragstart="drag(event, ' + ORIGEM_ANCESTOR + ')" ' + idOrigem + '>' + infoAnimal.total_animais + '</div>' +
                '</div>';

            L.marker(centro, {
                icon: L.divIcon({
                    className: 'satelite-pasto-badge-wrap',
                    html: html,
                    iconSize: [0, 0]
                }),
                interactive: false
            }).addTo(map);
        }
    });

    ajustar_zoom_fazenda();
    aplicar_escala_zoom(map.getZoom());
    atualizar_rotulos_zoom();

    // reaplica o termo de busca (se houver) nos poligonos recem desenhados
    if (typeof filtrar_pasto_tabuleiro === 'function') {
        filtrar_pasto_tabuleiro();
    }
}

// Busca por nome do pasto no mapa satelite: destaca e da zoom no pasto encontrado (igual ao Editor de Mapa)
function buscar_pasto_satelite(termo) {
    if (!mapaGadoSatelite.map) {
        return;
    }

    var encontrado = null;

    mapaGadoSatelite.poligonos.forEach(function(p) {
        p.layer.setStyle(mapa_gado_satelite_estilo_padrao(p.nome));

        if (termo !== '' && p.nome.indexOf(termo) !== -1 && !encontrado) {
            encontrado = p;
        }
    });

    if (encontrado) {
        mapaGadoSatelite.map.fitBounds(encontrado.layer.getBounds(), { maxZoom: 17 });
    }
}

function mais_info_mapa_satelite(clicked_id) {
    $.redirect('form_mapa_gados_movimentacao.php', { 'pasto_id': clicked_id });
}

// Destaque do pasto escolhido como origem no "Mover por toque" (mesma cor/estilo do Tabuleiro: borda
// tracejada laranja em .pasto-origem-selecionada)
var SATELITE_ESTILO_ORIGEM_TOQUE = { color: '#ff8f00', weight: 4, dashArray: '8, 6' };

// Mover por toque no mapa satelite (alternativa ao arraste): toca no pasto de origem, depois no de
// destino. Reaproveita mover_tabuleiro_toque() (js/mapa_gados.js) - a mesma funcao/tela do Tabuleiro -
// passando os elementos "por fora" que cada poligono ja guarda para o arraste (poligono._elementoToque).
function satelite_selecionar_pasto_toque(poligono, infoAnimal) {
    if (!poligono._elementoToque) {
        return; // pasto sem cadastro no banco (geojson sem tbl_pasto correspondente)
    }

    if (mapaGadoSatelite.origemToque === null) {
        // so' pode ser origem quem tem animal - mesma regra do Tabuleiro (card sem draggable=true nao entra)
        if (!infoAnimal || infoAnimal.tem_animal != 'S') {
            return;
        }

        mapaGadoSatelite.origemToque = poligono;
        poligono.setStyle(SATELITE_ESTILO_ORIGEM_TOQUE);
        return;
    }

    if (mapaGadoSatelite.origemToque === poligono) {
        // tocar de novo na origem cancela a selecao
        poligono.setStyle(mapa_gado_satelite_estilo_padrao(poligono._nomePasto));
        mapaGadoSatelite.origemToque = null;
        return;
    }

    var origemPoligono = mapaGadoSatelite.origemToque;
    origemPoligono.setStyle(mapa_gado_satelite_estilo_padrao(origemPoligono._nomePasto));
    mapaGadoSatelite.origemToque = null;

    mover_tabuleiro_toque(origemPoligono._elementoToque, poligono._elementoToque);
}

// Chamada pelo alternar_modo_toque_tabuleiro() (js/mapa_gados.js) ao ligar/desligar o modo toque, para
// nao deixar um pasto de origem "preso" selecionado no satelite ao trocar de modo
function satelite_cancelar_toque() {
    if (mapaGadoSatelite.origemToque) {
        mapaGadoSatelite.origemToque.setStyle(mapa_gado_satelite_estilo_padrao(mapaGadoSatelite.origemToque._nomePasto));
        mapaGadoSatelite.origemToque = null;
    }
}
