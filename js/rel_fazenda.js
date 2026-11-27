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

    $('#relfaz_dados').html(html);
}
