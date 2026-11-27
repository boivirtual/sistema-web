// Relatórios da Fazenda
// Filtros: Fazenda (lista de seleção única, mesmo layout da Agenda) + período (Diário / Semanal / Mensal) com navegação igual à Agenda.

var relfazPeriodo = 'D';          // D = Diário, S = Semanal, M = Mensal
var relfazDataBase = relfazHoje(); // data de referência do período exibido

var RELFAZ_MESES = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
                    'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
var RELFAZ_MESES_ABREV = ['jan.', 'fev.', 'mar.', 'abr.', 'mai.', 'jun.',
                          'jul.', 'ago.', 'set.', 'out.', 'nov.', 'dez.'];

$(document).ready(function() {

    relfazAtualizarTitulo();

    $(document).on('click', '#relfaz_hoje', function() {
        relfazDataBase = relfazHoje();
        relfazAtualizar();
    });

    $(document).on('click', '#relfaz_anterior', function() {
        relfazNavegar(-1);
    });

    $(document).on('click', '#relfaz_proximo', function() {
        relfazNavegar(1);
    });

    $(document).on('click', '.relfaz-view-btn', function() {
        $('.relfaz-view-btn').removeClass('active');
        $(this).addClass('active');

        relfazPeriodo = $(this).data('periodo');
        relfazAtualizar();
    });

    $(document).on('change', '.relfaz-fazenda-radio', function() {
        relfazAtualizar();
    });

    // Mudou pasto / Nutrição: a seta abre e fecha a lista do item
    $(document).on('click', '.relfaz-item-expansivel .relfaz-item-linha', function() {
        var $item = $(this).closest('.relfaz-item');
        $item.find('.relfaz-item-lista').stop(true, true).slideToggle(150);
        $item.toggleClass('relfaz-item-aberto');
    });

    // Diário: clique no ícone abre o seletor de data do navegador
    $(document).on('click', '#relfaz_calendario i', function() {
        var campo = document.getElementById('relfaz_data_dia');
        if (typeof campo.showPicker === 'function') {
            try { campo.showPicker(); return; } catch (e) {}
        }
        campo.focus();
        campo.click();
    });

    // Diário: data escolhida no calendário
    $(document).on('change', '#relfaz_data_dia', function() {
        var partes = (this.value || '').split('-');
        if (partes.length != 3) {
            return;
        }
        relfazDataBase = new Date(+partes[0], +partes[1] - 1, +partes[2]);
        relfazAtualizar();
    });
});

function relfazHoje() {
    var d = new Date();
    return new Date(d.getFullYear(), d.getMonth(), d.getDate());
}

function relfazNavegar(sentido) {
    var d = relfazDataBase;

    if (relfazPeriodo == 'D') {
        relfazDataBase = new Date(d.getFullYear(), d.getMonth(), d.getDate() + sentido);
    }
    else if (relfazPeriodo == 'S') {
        relfazDataBase = new Date(d.getFullYear(), d.getMonth(), d.getDate() + (7 * sentido));
    }
    else {
        relfazDataBase = new Date(d.getFullYear(), d.getMonth() + sentido, 1);
    }

    relfazAtualizar();
}

// Retorna { inicio: Date, fim: Date } do período exibido (semana começa no domingo, como na Agenda)
function relfazIntervalo() {
    var d = relfazDataBase;

    if (relfazPeriodo == 'D') {
        return { inicio: d, fim: d };
    }

    if (relfazPeriodo == 'S') {
        var inicio = new Date(d.getFullYear(), d.getMonth(), d.getDate() - d.getDay());
        var fim = new Date(inicio.getFullYear(), inicio.getMonth(), inicio.getDate() + 6);
        return { inicio: inicio, fim: fim };
    }

    return {
        inicio: new Date(d.getFullYear(), d.getMonth(), 1),
        fim: new Date(d.getFullYear(), d.getMonth() + 1, 0)
    };
}

// Data no formato AAAA-MM-DD (para enviar ao backend futuramente)
function relfazDataISO(d) {
    var mes = ('0' + (d.getMonth() + 1)).slice(-2);
    var dia = ('0' + d.getDate()).slice(-2);
    return d.getFullYear() + '-' + mes + '-' + dia;
}

function relfazAtualizarTitulo() {
    var intervalo = relfazIntervalo();
    var ini = intervalo.inicio;
    var fim = intervalo.fim;
    var titulo = '';

    if (relfazPeriodo == 'D') {
        titulo = ini.getDate() + ' de ' + RELFAZ_MESES[ini.getMonth()] + ' de ' + ini.getFullYear();
    }
    else if (relfazPeriodo == 'S') {
        if (ini.getFullYear() != fim.getFullYear()) {
            titulo = ini.getDate() + ' de ' + RELFAZ_MESES_ABREV[ini.getMonth()] + ' de ' + ini.getFullYear()
                   + ' – ' + fim.getDate() + ' de ' + RELFAZ_MESES_ABREV[fim.getMonth()] + ' de ' + fim.getFullYear();
        }
        else if (ini.getMonth() != fim.getMonth()) {
            titulo = ini.getDate() + ' de ' + RELFAZ_MESES_ABREV[ini.getMonth()]
                   + ' – ' + fim.getDate() + ' de ' + RELFAZ_MESES_ABREV[fim.getMonth()] + ' de ' + fim.getFullYear();
        }
        else {
            titulo = ini.getDate() + ' – ' + fim.getDate() + ' de ' + RELFAZ_MESES_ABREV[ini.getMonth()] + ' de ' + ini.getFullYear();
        }
    }
    else {
        titulo = RELFAZ_MESES[ini.getMonth()] + ' de ' + ini.getFullYear();
    }

    $('#relfaz_periodo_titulo').text(titulo);

    // Diário: calendário depois da data no lugar das setas; Semanal/Mensal: setas
    var diario = (relfazPeriodo == 'D');
    $('#relfaz_anterior, #relfaz_proximo').toggle(!diario);
    $('#relfaz_calendario').css('display', diario ? 'inline-block' : 'none');
    $('#relfaz_data_dia').val(relfazDataISO(relfazDataBase));
}

function relfazAtualizar() {
    relfazAtualizarTitulo();
    relfazCarregarDados();
}

var relfazRequisicao = null; // consulta em andamento (cancelada se o filtro mudar)

function relfazEscapar(texto) {
    return $('<div>').text(texto == null ? '' : texto).html();
}

// Área de dados: a cada troca de filtro limpa tudo e consulta a tbl_resumo_diario do período.
function relfazCarregarDados() {
    var fazenda = $('.relfaz-fazenda-radio:checked').val();

    if (relfazRequisicao) {
        relfazRequisicao.abort();
        relfazRequisicao = null;
    }

    $('#relfaz_dados').html('');

    if (!fazenda) {
        $('#relfaz_dados').html('<div class="relfaz-mensagem-vazia">Selecione uma Fazenda</div>');
        return;
    }

    var intervalo = relfazIntervalo();
    var periodo = relfazPeriodo;

    $('#relfaz_dados').html('<p class="text-center" style="padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Consultando...</p>');

    relfazRequisicao = $.post('ler_rel_fazenda.php', {
        fazenda: fazenda,
        inicio: relfazDataISO(intervalo.inicio),
        fim: relfazDataISO(intervalo.fim)
    }, function(retorno) {
        relfazRequisicao = null;

        if (!retorno || !retorno.ok) {
            $('#relfaz_dados').html('<div class="relfaz-mensagem-vazia">'
                + relfazEscapar(retorno && retorno.erro ? retorno.erro : 'Não foi possível consultar os dados.') + '</div>');
            return;
        }

        relfazMontarDados(retorno, periodo);
    }, 'json').fail(function(xhr, status) {
        if (status === 'abort') {
            return;
        }
        relfazRequisicao = null;
        $('#relfaz_dados').html('<div class="relfaz-mensagem-vazia">Não foi possível consultar os dados.</div>');
    });
}

function relfazMontarDados(retorno, periodo) {
    var titulo = relfazEscapar(retorno.fazenda_nome);

    // Diário: nome do usuário responsável pelo resumo na mesma linha da fazenda
    if (periodo == 'D' && retorno.registros.length && retorno.registros[0].usuario) {
        titulo += ' - ' + relfazEscapar(retorno.registros[0].usuario);
    }

    var html = '<h4 class="relfaz-fazenda-titulo">' + titulo + '</h4>';

    if (!retorno.registros.length) {
        html += '<div class="relfaz-mensagem-vazia">Nenhum registro encontrado para este período.</div>';
    }
    // Diário com o dia lançado como Folga no app (tipo 'F'): só o aviso no centro
    else if (periodo == 'D' && retorno.registros[0].tipo == 'F') {
        html += '<div class="relfaz-mensagem-vazia">Dia de folga.</div>';
    }
    // Diário com Resumo: cards lado a lado
    else if (periodo == 'D') {
        html += relfazCardsDiario(retorno.registros[0], retorno);
    }

    $('#relfaz_dados').html(html);
}

// Número no padrão brasileiro, sem casas decimais sobrando (12.5 -> "12,5"; 10 -> "10")
function relfazNumero(valor) {
    return Number(valor).toLocaleString('pt-BR', { maximumFractionDigits: 2 });
}

// Modelo B (igual ao desenho sugerido): ícone redondo colorido + texto + marcador à direita.
// Item sem atividade: ícone e texto apagados (cinza) e marcador "Não".
// cor: azul | roxo | verde | amarelo | vermelho | cinza
// lista (opcional): HTML exibido abaixo do item ao clicar na seta (pastos trocados, nutrição)
function relfazItem(icone, cor, texto, badge, corBadge, detalhe, lista) {
    var inativo = (cor == 'cinza');

    return '<li class="relfaz-item' + (inativo ? ' relfaz-item-inativo' : '') + (lista ? ' relfaz-item-expansivel' : '') + '">'
         +   '<div class="relfaz-item-linha">'
         +     '<span class="relfaz-item-icone relfaz-cor-' + cor + '"><i class="' + icone + '"></i></span>'
         +     '<span class="relfaz-item-texto">' + texto
         +       (lista ? ' <i class="fas fa-chevron-down relfaz-item-seta"></i>' : '')
         +       (detalhe ? '<span class="relfaz-item-detalhe">' + detalhe + '</span>' : '')
         +     '</span>'
         +     (badge ? '<span class="relfaz-badge relfaz-cor-' + (corBadge || cor) + '">' + badge + '</span>' : '')
         +   '</div>'
         +   (lista ? '<div class="relfaz-item-lista">' + lista + '</div>' : '')
         + '</li>';
}

// Pastos com troca de animais no dia (um por linha)
function relfazListaPastos(pastos) {
    if (!pastos || !pastos.length) {
        return '';
    }
    return '<ul class="relfaz-lista-pastos">'
         + $.map(pastos, function(nome) { return '<li>' + relfazEscapar(nome) + '</li>'; }).join('')
         + '</ul>';
}

// Nutrição do dia: Pasto | Produto | Quantidade (o pasto aparece só na 1ª linha do grupo, como no app)
function relfazListaNutricao(nutricoes) {
    if (!nutricoes || !nutricoes.length) {
        return '';
    }
    var linhas = '';
    var pastoAnterior = null;

    $.each(nutricoes, function(i, n) {
        linhas += '<tr' + (n.pasto !== pastoAnterior && i > 0 ? ' class="relfaz-nutricao-grupo"' : '') + '>'
               +   '<td>' + (n.pasto !== pastoAnterior ? relfazEscapar(n.pasto) : '') + '</td>'
               +   '<td>' + relfazEscapar(n.produto) + '</td>'
               +   '<td class="text-right">' + relfazNumero(n.quantidade) + (n.unidade ? ' ' + relfazEscapar(n.unidade) : '') + '</td>'
               + '</tr>';
        pastoAnterior = n.pasto;
    });

    return '<table class="relfaz-lista-nutricao">' + linhas + '</table>';
}

// Atividade com quantidade (nascimentos, abortos, mortes): mostra o número; sem ocorrência = "Não"
function relfazItemQuantidade(icone, cor, texto, quantidade) {
    if (quantidade !== null && quantidade > 0) {
        return relfazItem(icone, cor, texto, relfazNumero(quantidade));
    }
    return relfazItem(icone, 'cinza', texto, 'Não');
}

// Atividade Sim/Não (mudou pasto, nutrição)
function relfazItemSimNao(icone, cor, texto, valor, lista) {
    if (valor == 'S') {
        return relfazItem(icone, cor, texto, 'Sim', null, null, lista);
    }
    return relfazItem(icone, 'cinza', texto, 'Não');
}

function relfazCard(icone, cor, titulo, itens) {
    return '<div class="col-md-6">'
         +   '<div class="relfaz-card">'
         +     '<div class="relfaz-card-titulo"><i class="' + icone + ' relfaz-texto-' + cor + '"></i> ' + titulo + '</div>'
         +     '<ul class="relfaz-card-lista">' + itens + '</ul>'
         +   '</div>'
         + '</div>';
}

function relfazCardsDiario(r, retorno) {
    // Card 1 - Atividades registradas
    var atividades = '';

    if (r.chuva_mm !== null && r.chuva_mm > 0) {
        atividades += relfazItem('fas fa-cloud-showers-heavy', 'azul', 'Choveu', relfazNumero(r.chuva_mm) + ' mm');
    }
    else {
        atividades += relfazItem('fas fa-cloud-showers-heavy', 'cinza', 'Choveu', 'Não');
    }

    atividades += relfazItemSimNao('fas fa-exchange-alt', 'azul', 'Mudou pasto', r.mudou_pasto, relfazListaPastos(retorno.pastos_alterados));
    atividades += relfazItemSimNao('fas fa-seedling', 'verde', 'Nutrição', r.nutricao, relfazListaNutricao(retorno.nutricoes));
    atividades += relfazItemQuantidade('fas fa-baby', 'verde', 'Nascimentos', r.nascimentos);
    atividades += relfazItemQuantidade('fas fa-heart-broken', 'amarelo', 'Aborto / Natimorto', r.abortos);
    atividades += relfazItemQuantidade('fas fa-skull-crossbones', 'vermelho', 'Morreu', r.mortes);

    // Card 2 - Conferência do rebanho
    var conferencia = '';

    // Encontrado / Esperado: verde quando bate, vermelho quando há diferença
    var badgeGado = '';
    var corGado = 'verde';
    if (r.animais_esperados !== null || r.animais_encontrados !== null) {
        badgeGado = (r.animais_encontrados !== null ? relfazNumero(r.animais_encontrados) : '-')
                  + ' / ' + (r.animais_esperados !== null ? relfazNumero(r.animais_esperados) : '-');

        if (r.animais_esperados !== r.animais_encontrados) {
            corGado = 'vermelho';
        }
    }

    // Pastos onde o encontrado não bateu com o esperado (em vermelho, abaixo do item)
    var diferenca = '';
    if (r.pastos_diferenca && r.pastos_diferenca.length) {
        diferenca = '<span class="relfaz-detalhe-diferenca"><i class="fas fa-map-marker-alt"></i> Diferença: '
                  + $.map(r.pastos_diferenca, relfazEscapar).join(', ') + '</span>';
    }

    if (r.conferiu_gado == 'T') {
        conferencia += relfazItem(corGado == 'verde' ? 'fas fa-check' : 'fas fa-exclamation-triangle',
                                  corGado, 'Conferiu o gado todo', badgeGado, corGado, diferenca);
    }
    else if (r.conferiu_gado == 'P') {
        var pastos = '';
        if (r.pastos_conferidos && r.pastos_conferidos.length) {
            pastos = 'Pastos ' + $.map(r.pastos_conferidos, relfazEscapar).join(', ');
        }
        conferencia += relfazItem(corGado == 'verde' ? 'fas fa-check' : 'fas fa-exclamation-triangle',
                                  corGado, 'Conferiu parte do gado', badgeGado, corGado, pastos + diferenca);
    }
    else {
        conferencia += relfazItem('fas fa-exclamation-triangle', 'amarelo', 'Gado', 'Não conferiu');
    }

    if (r.conferiu_prenhas == 'S') {
        conferencia += relfazItem('fas fa-check', 'verde', 'Vacas prenhas', 'Conferiu');
    }
    else {
        conferencia += relfazItem('fas fa-exclamation-triangle', 'amarelo', 'Vacas prenhas', 'Não conferiu');
    }

    // Observação da conferência do rebanho (digitada no app)
    if (r.obs_rebanho) {
        conferencia += relfazItem('fas fa-comment-alt', 'azul', 'Observação', null, null,
                                  '<span class="relfaz-detalhe-obs">' + relfazEscapar(r.obs_rebanho) + '</span>');
    }

    return '<div class="row relfaz-cards">'
         + relfazCard('fas fa-clipboard-list', 'azul', 'Atividades registradas', atividades)
         + relfazCard('fas fa-clipboard-check', 'verde', 'Conferência do rebanho', conferencia)
         + '</div>';
}
