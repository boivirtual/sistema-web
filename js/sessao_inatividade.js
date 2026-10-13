//  Controle de inatividade para telas de digitação longa (inclusão de Compra e
//  de Venda de animais). Sem isto, a sessão expirava no servidor mas a tela
//  continuava aceitando digitação, e o usuário só descobria ao gravar.
//
//  - Enquanto o usuário digita/clica na tela, renova a sessão no servidor
//    (digitar É atividade, mesmo sem nenhuma gravação no meio).
//  - Parou de mexer: no instante em que o prazo vence, consulta o servidor;
//    se a sessão expirou, o backend devolve HTTP 401 e o scripts.js bloqueia
//    a tela com o modal "Sessão Expirada" e manda para o login.
//
//  Depende de: jQuery, js/scripts.js (ajaxError 401) e verificar_sessao.php.
(function ($) {
    var URL_VERIFICAR         = 'verificar_sessao.php';
    var timerExpiracao        = null;
    var ultimaRenovacao       = Date.now();   // a carga da página já contou como atividade
    var intervaloRenovacaoMs  = 60000;        // ajustado para 1/3 do limite, no máximo 1 minuto
    var consultando           = false;
    var expirada              = false;

    function agendarConsulta(segundos) {
        clearTimeout(timerExpiracao);
        timerExpiracao = setTimeout(function () {
            consultarSessao(false);
        }, (segundos + 2) * 1000);
    }

    function consultarSessao(renovar) {
        if (consultando || expirada) {
            return;
        }
        consultando = true;

        $.ajax({
            url: URL_VERIFICAR,
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: { renovar: renovar ? 1 : 0 }
        }).done(function (resposta) {
            if (!resposta || !resposta.ativa) {
                return;
            }
            if (renovar) {
                ultimaRenovacao = Date.now();
            }
            intervaloRenovacaoMs = Math.min(60000, Math.floor(resposta.limite * 1000 / 3));
            agendarConsulta(resposta.restante);
        }).fail(function (jqxhr) {
            if (jqxhr.status === 401) {
                // o modal é exibido pelo ajaxError global do scripts.js;
                // aqui só tira o cursor do campo para não aceitar mais digitação
                expirada = true;
                clearTimeout(timerExpiracao);
                if (document.activeElement) {
                    document.activeElement.blur();
                }
            } else {
                // falha de rede/servidor: tenta de novo em instantes
                agendarConsulta(30);
            }
        }).always(function () {
            consultando = false;
        });
    }

    // Uso da tela = atividade. Renova no máximo uma vez a cada "intervaloRenovacaoMs".
    $(document).on('keydown mousedown change', function () {
        if (Date.now() - ultimaRenovacao >= intervaloRenovacaoMs) {
            consultarSessao(true);
        }
    });

    // Voltou para a aba/janela: confere na hora (timers podem atrasar em aba em segundo plano)
    $(window).on('focus', function () {
        consultarSessao(false);
    });
    $(document).on('visibilitychange', function () {
        if (!document.hidden) {
            consultarSessao(false);
        }
    });

    $(function () {
        consultarSessao(false);
    });
})(jQuery);
