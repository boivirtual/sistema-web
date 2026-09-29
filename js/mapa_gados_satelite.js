/** MAPA DE GADO - SATELITE (Leaflet) */
var mapaGadoSatelite = {
    map: null,
    poligonos: [],
    dragHoverLayer: null,
    origemToque: null
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

// Estilo do poligono considerando o termo de busca atual (usado tambem para "desfazer" o destaque de arraste)
function mapa_gado_satelite_estilo_padrao(nome) {
    var termo = ($('#buscar_pasto_tabuleiro').val() || '').toUpperCase();
    var combina = termo !== '' && nome.indexOf(termo) !== -1;

    return {
        color: combina ? '#ffeb3b' : '#ffffff',
        weight: combina ? 3 : 1
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

            mapa_gado_satelite_desenhar(pastosAnimais, dadosMapa);
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

function mapa_gado_satelite_desenhar(pastosAnimais, dadosMapa) {
    if (mapaGadoSatelite.map !== null) {
        mapaGadoSatelite.map.remove();
        mapaGadoSatelite.map = null;
    }

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
            if (map.getZoom() < 15) {
                map.setZoom(15, { animate: false });
            }
        }
        else {
            map.setView([latitude, longitude], 13, { animate: false });
        }
    }

    // O container pode ainda nao ter o tamanho definitivo no instante da criacao (troca de aba,
    // aba escondida no carregamento da pagina); sem isso o mapa pode desenhar tudo torto.
    setTimeout(function() {
        map.invalidateSize({ animate: false });
        ajustar_zoom_fazenda();
    }, 0);

    var popup = L.popup();
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

        var infoAnimal = null;

        for (var i = 0; i < pastosAnimais.length; i++) {
            if (pastosAnimais[i].descricao == nome) {
                infoAnimal = pastosAnimais[i];
                break;
            }
        }

        poligono.on('click', function(ev) {
            // Mover por toque (alternativa ao arraste, mesma regra/tela do Tabuleiro): enquanto o modo
            // estiver ativo, o clique no pasto seleciona origem/destino em vez de abrir o balao de info.
            if (typeof _tabuleiroModoToque !== 'undefined' && _tabuleiroModoToque) {
                satelite_selecionar_pasto_toque(poligono, infoAnimal);
                return;
            }

            var totalTexto = '';
            var situacaoTexto = 'Este pasto nao existe no sistema';

            if (infoAnimal) {
                if (infoAnimal.total_animais != 0) {
                    totalTexto = infoAnimal.total_animais + ' animais';
                    situacaoTexto = 'Animais no pasto há ' + infoAnimal.dias_com_animais + ' dia(s)';
                }
                else {
                    situacaoTexto = 'Pasto vazio há ' + infoAnimal.dias_sem_animais + ' dia(s)';
                }
            }

            var html = nome + '<br>' + totalTexto + '<br>' + situacaoTexto + '<br>' + (infoAnimal ? infoAnimal.descricao_capim : '');

            popup.setLatLng(ev.latlng).setContent(html).openOn(map);
        });

        poligono.on('dblclick', function(ev) {
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
                linhas += '<div class="linha" draggable="true" ondragstart="drag(event, ' + ORIGEM_ANCESTOR + ')" ' + idOrigem + '><span class="icone-animal bezerro"><img src="img/bezerro.png" draggable="false"></span><span>' + infoAnimal.bezerros + '</span></div>';
            }

            if (infoAnimal.femeas != 0) {
                linhas += '<div class="linha" draggable="true" ondragstart="drag(event, ' + ORIGEM_ANCESTOR + ')" ' + idOrigem + '><span class="icone-animal femea"><img src="img/vaca.png" draggable="false"></span><span>' + infoAnimal.femeas + '</span></div>';
            }

            if (infoAnimal.machos != 0) {
                linhas += '<div class="linha" draggable="true" ondragstart="drag(event, ' + ORIGEM_ANCESTOR + ')" ' + idOrigem + '><span class="icone-animal macho"><img src="img/gado.png" draggable="false"></span><span>' + infoAnimal.machos + '</span></div>';
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
