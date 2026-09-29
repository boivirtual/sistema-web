/** MAPA DE GADO - SATELITE (Leaflet, somente visualizacao) */
var mapaGadoSatelite = {
    map: null
};

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

    if (!pastosAnimais.length) {
        return;
    }

    var latitude = parseFloat(pastosAnimais[0].latitude);
    var longitude = parseFloat(pastosAnimais[0].longitude);

    var map = L.map('map', { zoomSnap: 0.5 }).setView([latitude, longitude], 13);
    mapaGadoSatelite.map = map;

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxNativeZoom: 18,
        maxZoom: 20,
        attribution: 'Imagens &copy; Esri, Maxar, Earthstar Geographics'
    }).addTo(map);

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

        var infoAnimal = null;

        for (var i = 0; i < pastosAnimais.length; i++) {
            if (pastosAnimais[i].descricao == nome) {
                infoAnimal = pastosAnimais[i];
                break;
            }
        }

        poligono.on('click', function(ev) {
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

        if (infoAnimal && infoAnimal.tem_animal == 'S') {
            var centro = mapa_gado_satelite_centroide(anel);
            var linhas = '';

            // mesmos icones e a mesma regra do Tabuleiro: só mostra a linha da categoria que tiver animal
            if (infoAnimal.bezerros != 0) {
                linhas += '<div class="linha"><img src="img/bezerro.png"><span>' + infoAnimal.bezerros + '</span></div>';
            }

            if (infoAnimal.femeas != 0) {
                linhas += '<div class="linha"><img src="img/vaca.png"><span>' + infoAnimal.femeas + '</span></div>';
            }

            if (infoAnimal.machos != 0) {
                linhas += '<div class="linha"><img src="img/gado.png"><span>' + infoAnimal.machos + '</span></div>';
            }

            var html = '<div class="satelite-pasto-badge">' + linhas +
                '<div class="total">' + infoAnimal.total_animais + '</div></div>';

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
}

function mais_info_mapa_satelite(clicked_id) {
    $.redirect('form_mapa_gados_movimentacao.php', { 'pasto_id': clicked_id });
}
