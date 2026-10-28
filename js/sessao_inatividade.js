//  Controle de inatividade para telas de digitação longa (inclusão de Compra e
//  de Venda de animais). Sem isto, a sessão expirava no servidor mas a tela
//  continuava aceitando digitação, e o usuário só descobria ao gravar.
//
//  - Enquanto o usuário digita/clica na tela, renova a sessão no servidor
//    (digitar É atividade, mesmo sem nenhuma gravação no meio).
//  - Parou de mexer: no instante em que o prazo vence, consulta o servidor;
//    se a sessão expirou, o backend devolve HTTP 401 e o scripts.js chama o
//    modal "Sessão Expirada" daqui, que pede a senha na própria tela.
//  - Senha confirmada (reautenticar_sessao.php): a sessão é montada de novo e o
//    usuário continua de onde parou, sem perder o que já digitou.
//    "Ir para o Login" sai da tela e perde a digitação.
//
//  Depende de: jQuery, Bootstrap 3, js/scripts.js (ajaxError 401),
//  verificar_sessao.php e reautenticar_sessao.php.
(function ($) {
    var URL_VERIFICAR         = 'verificar_sessao.php';
    var URL_REAUTENTICAR      = 'reautenticar_sessao.php';
    var URL_LOGIN             = '../index.php?expirou=1';
    var timerExpiracao        = null;
    var ultimaRenovacao       = Date.now();   // a carga da página já contou como atividade
    var intervaloRenovacaoMs  = 60000;        // ajustado para 1/3 do limite, no máximo 1 minuto
    var consultando           = false;
    var expirada              = false;
    var idUsuario             = null;         // quem está logado na tela (vem do verificar_sessao.php)
    var nomeUsuario           = '';

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
            if (resposta.usuario) {
                idUsuario   = resposta.usuario;
                nomeUsuario = resposta.nome || '';
            }
            if (renovar) {
                ultimaRenovacao = Date.now();
            }
            intervaloRenovacaoMs = Math.min(60000, Math.floor(resposta.limite * 1000 / 3));
            agendarConsulta(resposta.restante);
        }).fail(function (jqxhr) {
            if (jqxhr.status !== 401) {
                // falha de rede/servidor: tenta de novo em instantes
                // (no 401 o modal é aberto pelo ajaxError global do scripts.js)
                agendarConsulta(30);
            }
        }).always(function () {
            consultando = false;
        });
    }

    // MODAL "SESSÃO EXPIRADA" COM SENHA
    function montarModalSenha() {
        if ($('#modal_sessao_senha').length) {
            return;
        }

        // z-index acima de qualquer outro modal/overlay aberto na tela (mesmo valor do scripts.js)
        $('body').append(
            '<style>' +
                '#modal_sessao_senha{z-index:20050;}' +
                '.modal-backdrop.backdrop_sessao_senha{z-index:20040;}' +
            '</style>' +
            '<div class="modal fade" id="modal_sessao_senha" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">' +
                '<div class="modal-dialog" role="document">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<h4 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Sessão Expirada</h4>' +
                        '</div>' +
                        '<div class="modal-body">' +
                            '<p id="modal_sessao_senha_mensagem"></p>' +
                            '<p>Para continuar de onde parou, <strong>sem perder o que já foi digitado</strong>, informe sua senha.</p>' +
                            '<div class="form-group">' +
                                '<label class="control-label">Usuário</label>' +
                                '<input type="text" class="form-control" id="modal_sessao_senha_usuario" readonly>' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label class="control-label" for="modal_sessao_senha_campo">Senha</label>' +
                                '<input type="password" class="form-control" id="modal_sessao_senha_campo" maxlength="8" autocomplete="current-password">' +
                            '</div>' +
                            '<div class="alert alert-danger" id="modal_sessao_senha_erro" style="display:none; margin-bottom:0;"></div>' +
                            '<p class="text-muted" style="margin-top:10px; margin-bottom:0;">' +
                                '<small>Ao clicar em <strong>Ir para o Login</strong>, os dados digitados nesta tela serão perdidos.</small>' +
                            '</p>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-default" id="btn_modal_sessao_senha_login">Ir para o Login</button>' +
                            '<button type="button" class="btn btn-primary" id="btn_modal_sessao_senha_confirmar"><i class="fas fa-check"></i> Confirmar Senha</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        $('#btn_modal_sessao_senha_confirmar').on('click', confirmarSenha);

        $('#modal_sessao_senha_campo').on('keydown', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                confirmarSenha();
            }
        });

        $('#btn_modal_sessao_senha_login').on('click', function () {
            window.location.href = URL_LOGIN;
        });

        $('#modal_sessao_senha').on('shown.bs.modal', function () {
            $('.modal-backdrop').last().addClass('backdrop_sessao_senha');
            $('#modal_sessao_senha_campo').trigger('focus');
        });

        $('#modal_sessao_senha').on('hidden.bs.modal', function () {
            // se havia outro modal aberto por baixo (ex.: inclusão de item), mantém a rolagem dele
            if ($('.modal.in').length) {
                $('body').addClass('modal-open');
            }
        });
    }

    function mostrarErroSenha(mensagem) {
        $('#modal_sessao_senha_erro').html(mensagem).show();
    }

    function confirmarSenha() {
        var senha = $('#modal_sessao_senha_campo').val();

        if (senha === '') {
            mostrarErroSenha('Informe a senha.');
            $('#modal_sessao_senha_campo').trigger('focus');
            return;
        }

        $('#modal_sessao_senha_erro').hide();
        $('#btn_modal_sessao_senha_confirmar').attr('disabled', true);

        $.ajax({
            url: URL_REAUTENTICAR,
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: { id_usuario: idUsuario, senha: senha }
        }).done(function (resposta) {
            if (resposta && resposta.success) {
                expirada        = false;
                ultimaRenovacao = Date.now();
                $('#modal_sessao_senha_campo').val('');
                $('#modal_sessao_senha').modal('hide');
                $(document).trigger('sessaoRenovada');
                consultarSessao(false);
            } else {
                mostrarErroSenha(resposta && resposta.message ? resposta.message : 'Não foi possível confirmar a senha.');
                $('#modal_sessao_senha_campo').val('').trigger('focus');
            }
        }).fail(function () {
            mostrarErroSenha('Não foi possível confirmar a senha. Verifique a conexão e tente novamente.');
        }).always(function () {
            $('#btn_modal_sessao_senha_confirmar').attr('disabled', false);
        });
    }

    // chamado pelo exibirModalSessaoExpirada() do scripts.js em qualquer 401 desta tela
    window.exibirModalSenhaSessaoExpirada = function (mensagem) {
        expirada = true;
        clearTimeout(timerExpiracao);

        // sem saber quem estava logado não há como confirmar a senha: só resta o login
        if (!idUsuario) {
            window.location.href = URL_LOGIN;
            return;
        }

        montarModalSenha();

        if ($('#modal_sessao_senha').hasClass('in')) {
            return;
        }

        // tira o cursor do campo da tela para não aceitar mais digitação por baixo do modal
        if (document.activeElement) {
            document.activeElement.blur();
        }

        // a mensagem do servidor termina com "Faça login novamente.", que aqui não se aplica
        $('#modal_sessao_senha_mensagem').text((mensagem || 'Sua sessão expirou.').replace(/\s*Faça login novamente\.?\s*$/, ''));
        $('#modal_sessao_senha_usuario').val(nomeUsuario);
        $('#modal_sessao_senha_campo').val('');
        $('#modal_sessao_senha_erro').hide();
        $('#modal_sessao_senha').modal('show');
    };

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
