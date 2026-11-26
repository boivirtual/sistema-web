// Relatórios da Fazenda
// Filtros: Fazenda (select simples) + período (Diário / Semanal / Mensal) com navegação igual à Agenda.

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

    $(document).on('change', '#relfaz_fazenda', function() {
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
}

function relfazAtualizar() {
    relfazAtualizarTitulo();
    relfazCarregarDados();
}

// Área de dados: por enquanto só trata a mensagem de "Selecione uma Fazenda".
// Os relatórios em si serão incluídos nas próximas etapas.
function relfazCarregarDados() {
    var fazenda = $('#relfaz_fazenda').val();

    if (!fazenda) {
        $('#relfaz_dados').html('<div class="relfaz-mensagem-vazia">Selecione uma Fazenda</div>');
        return;
    }

    $('#relfaz_dados').html('');
}
