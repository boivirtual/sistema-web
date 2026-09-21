<?php
/**
 * modal_anexos_ctr.php
 * Include reutilizável: modal Bootstrap 3 + função JS abrirModalAnexosCtr().
 * Equivalente a modal_anexos.php, mas para Contas a Receber (ctr_id / tbl_ctr_anexos).
 *
 * Como usar em qualquer programa:
 *   1. No PHP da página: <?php include "modal_anexos_ctr.php"; ?>
 *   2. No onclick do ícone: abrirModalAnexosCtr(numero_doc, codigo_cliente, ctr_id, doc_display)
 *
 * Parâmetros de abrirModalAnexosCtr():
 *   numero_doc     — número do documento (string)
 *   codigo_cliente — código do cliente/fornecedor (int)
 *   ctr_id         — id da parcela (int, fallback quando numero_doc vazio)
 *   doc_display    — texto exibido no cabeçalho do modal (ex: "000003916")
 */
?>

<style>
.btn-ma-add-ctr {
    background: none;
    border: none;
    color: #337ab7;
    font-size: 20px;
    padding: 0 4px;
    cursor: pointer;
    vertical-align: middle;
    line-height: 1;
}
.btn-ma-add-ctr:hover { color: #23527c; }
</style>

<!-- Modal: Visualizar Anexos / Links (Contas a Receber) -->
<div class="modal fade" id="modal_anexos_ctr" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document" style="width:620px;max-width:96%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    <i class="fas fa-paperclip" style="color:#337ab7;margin-right:6px;"></i>
                    Anexos / Links
                </h4>
            </div>
            <div class="modal-body" style="min-height:60px;padding:10px 15px 10px 15px;">

                <p id="modal_anexos_doc_ctr" style="margin:0 0 10px 0;font-size:14px;color:#333;font-weight:500;"></p>

                <!-- Lista de anexos existentes (recarregada por AJAX) -->
                <div id="modal_anexos_body_ctr">
                    <p class="text-center text-muted" style="padding:20px 0;">
                        <i class="fas fa-spinner fa-spin"></i> Carregando...
                    </p>
                </div>

                <!-- Seção: adicionar novos anexos/links -->
                <div id="modal_ma_area_ctr" style="padding:4px 0 0 0;">
                    <hr style="margin:10px 0 8px 0;">

                    <a href="#" id="modal_ma_toggle_ctr" onclick="_maAbrirInputsCtr(); return false;"
                       style="font-size:0.9em;font-weight:500;color:#128cb8;">
                        <i class="fa fa-plus"></i> Anexos
                    </a>

                    <div id="modal_ma_inputs_ctr" style="display:none;">
                        <div style="display:flex;align-items:flex-start;gap:48px;flex-wrap:wrap;margin-top:6px;">
                            <div>
                                <label style="font-size:12px;color:#555;font-weight:600;display:block;margin-bottom:4px;">
                                    <i class="fas fa-paperclip" style="color:#337ab7;"></i> Anexar Documento
                                </label>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <input type="file" id="modal_ma_picker_ctr" class="form-control" style="max-width:280px;" onchange="_maOnPickerChangeCtr(this)">
                                </div>
                                <div id="modal_ma_lista_anexos_ctr"></div>
                            </div>
                            <div>
                                <label style="font-size:12px;color:#555;font-weight:600;display:block;margin-bottom:4px;">
                                    <i class="fas fa-link" style="color:#337ab7;"></i> Anexar Link
                                </label>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <input type="text" id="modal_ma_link_desc_ctr" class="form-control" placeholder="Descrição do link" style="max-width:180px;">
                                    <input type="url" id="modal_ma_link_url_ctr" class="form-control" placeholder="https://..." style="max-width:220px;" onkeydown="_maOnLinkUrlKeydownCtr(event)" onblur="_maOnLinkUrlBlurCtr()" data-toggle="tooltip" data-placement="top" title="Após digitar o https://, tecle ENTER para confirmar o Link">
                                </div>
                                <div id="modal_ma_lista_links_ctr"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="btn_confirmar_ma_ctr" class="btn btn-primary" style="display:none;" onclick="_maConfirmarCtr()">Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
var _ctrAnexosParams     = {};
var _ctrAnexosHandler    = false;
var _ctrAnexosAutoInputs = false;

function abrirModalAnexosCtr(numero_doc, codigo_cliente, ctr_id, doc_display, abrirInputs) {
    _ctrAnexosParams = { numero_doc: numero_doc, codigo_cliente: codigo_cliente, ctr_id: ctr_id, doc_display: doc_display };

    if (!_ctrAnexosHandler) {

        $(document).on('click', '.btn-excluir-anexo-ctr', function () {
            var id   = $(this).data('id');
            var nome = $(this).data('nome');
            if (!confirm('Deseja excluir o anexo/link:\n"' + nome + '"?')) return;

            $.ajax({
                type:     'POST',
                url:      'api/excluir_anexo_ctr.php',
                data:     { anexo_id: id },
                dataType: 'json',
                success: function (resp) {
                    if (resp.ok) {
                        _carregarAnexosCtr();
                    } else {
                        alert('Erro: ' + (resp.msg || 'Não foi possível excluir.'));
                    }
                },
                error: function () {
                    alert('Erro ao comunicar com o servidor.');
                }
            });
        });

        $('#modal_anexos_ctr').on('hidden.bs.modal', function () {
            _maResetarCtr();
        });

        _ctrAnexosHandler = true;
    }

    _ctrAnexosAutoInputs = !!abrirInputs;
    $('#modal_anexos_doc_ctr').text('Documento Nº: ' + doc_display);
    _maResetarCtr();
    $('#modal_anexos_ctr').modal('show');
    _carregarAnexosCtr();
}

function _carregarAnexosCtr() {
    $('#modal_anexos_body_ctr').html(
        '<p class="text-center text-muted" style="padding:20px 0;">' +
        '<i class="fas fa-spinner fa-spin"></i> Carregando...</p>'
    );
    $.ajax({
        type: 'GET',
        url:  'api/get_anexos_ctr.php',
        data: {
            numero_doc:     _ctrAnexosParams.numero_doc,
            codigo_cliente: _ctrAnexosParams.codigo_cliente,
            ctr_id:         _ctrAnexosParams.ctr_id
        },
        success: function (html) {
            $('#modal_anexos_body_ctr').html(html);
            $('[data-toggle="tooltip"]').tooltip();
            if (_ctrAnexosAutoInputs && $('#modal_anexos_body_ctr li').length === 0) {
                _maAbrirInputsCtr();
            }
            _ctrAnexosAutoInputs = false;
        },
        error: function () {
            $('#modal_anexos_body_ctr').html(
                '<p class="text-danger" style="padding:10px 0;">Erro ao carregar os anexos.</p>'
            );
        }
    });
}

/* ── Funções da seção de adição ── */

function _maAbrirInputsCtr() {
    $('#modal_ma_toggle_ctr').hide();
    $('#modal_ma_inputs_ctr').show();
}

function _maResetarCtr() {
    $('#modal_ma_picker_ctr').val('');
    $('#modal_ma_link_desc_ctr').val('');
    $('#modal_ma_link_url_ctr').val('');
    $('#modal_ma_lista_anexos_ctr').empty();
    $('#modal_ma_lista_links_ctr').empty();
    $('#btn_confirmar_ma_ctr').hide();
    $('#modal_ma_inputs_ctr').hide();
    $('#modal_ma_toggle_ctr').show();
}

function _maCriarBotaoRemoverCtr(onRemove) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn-ma-add-ctr';
    btn.title = 'Remover';
    btn.setAttribute('data-toggle', 'tooltip');
    btn.setAttribute('data-placement', 'top');
    btn.innerHTML = '<i class="fas fa-trash" style="font-size:12px; color:#337ab7;"></i>';
    btn.onclick = onRemove;
    $(btn).tooltip();
    return btn;
}

function _maOnPickerChangeCtr(input) {
    if (!input.files || !input.files.length) return;
    _maCriarLinhaAnexoArquivoCtr(input.files[0]);
    input.value = ''; // limpa para permitir escolher o próximo arquivo
    _maAtualizarBotaoCtr();
}

function _maCriarLinhaAnexoArquivoCtr(file) {
    var dt = new DataTransfer();
    dt.items.add(file);

    var hidden = document.createElement('input');
    hidden.type = 'file';
    hidden.className = 'ma-file-input-ctr';
    hidden.style.display = 'none';
    hidden.files = dt.files;

    var nome = document.createElement('span');
    nome.textContent = file.name;
    nome.style.cssText = 'max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;';

    var div = document.createElement('div');
    div.className = 'linha-anexo-arquivo-ctr';
    div.style.cssText = 'display:flex;align-items:center;gap:8px;margin-top:6px;';

    var btnRemover = _maCriarBotaoRemoverCtr(function () { _maRemoverLinhaCtr(div); });

    div.appendChild(nome);
    div.appendChild(btnRemover);
    div.appendChild(hidden);
    $('#modal_ma_lista_anexos_ctr').append(div);
}

// Enter no campo URL sai do foco (dispara _maOnLinkUrlBlurCtr) em vez de tentar submeter algo.
function _maOnLinkUrlKeydownCtr(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        event.target.blur();
    }
}

// Ao sair do foco da URL, se preenchida, cria a linha de exibição do link
// e limpa os inputs fixos de Descrição/URL para o próximo link.
function _maOnLinkUrlBlurCtr() {
    var $desc = $('#modal_ma_link_desc_ctr');
    var $url  = $('#modal_ma_link_url_ctr');
    var url = $url.val().trim();
    if (!url) return; // nada digitado

    var desc = $desc.val().trim() || url;
    _maCriarLinhaLinkCtr(desc, url);

    $desc.val('');
    $url.val('');
    _maAtualizarBotaoCtr();
}

function _maCriarLinhaLinkCtr(desc, url) {
    var hiddenDesc = document.createElement('input');
    hiddenDesc.type = 'hidden';
    hiddenDesc.className = 'ma-link-desc-ctr';
    hiddenDesc.value = desc;

    var hiddenUrl = document.createElement('input');
    hiddenUrl.type = 'hidden';
    hiddenUrl.className = 'ma-link-url-ctr';
    hiddenUrl.value = url;

    var texto = document.createElement('span');
    texto.textContent = desc + ' — ' + url;
    texto.style.cssText = 'max-width:360px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;';

    var div = document.createElement('div');
    div.className = 'ma-link-row-ctr';
    div.style.cssText = 'display:flex;align-items:center;gap:8px;margin-top:6px;';

    var btnRemover = _maCriarBotaoRemoverCtr(function () { _maRemoverLinhaCtr(div); });

    div.appendChild(texto);
    div.appendChild(btnRemover);
    div.appendChild(hiddenDesc);
    div.appendChild(hiddenUrl);
    $('#modal_ma_lista_links_ctr').append(div);
}

function _maRemoverLinhaCtr(div) {
    $(div).remove();
    _maAtualizarBotaoCtr();
}

function _maAtualizarBotaoCtr() {
    var temAlgo = $('#modal_ma_lista_anexos_ctr').children().length > 0
               || $('#modal_ma_lista_links_ctr').children().length > 0;
    $('#btn_confirmar_ma_ctr').toggle(temAlgo);
}

function _maConfirmarCtr() {
    // Descrição do link digitada sem o link confirmado (não vira uma linha em modal_ma_lista_links_ctr)
    var descPendente = $('#modal_ma_link_desc_ctr').val().trim();
    var urlPendente  = $('#modal_ma_link_url_ctr').val().trim();
    if (descPendente && !urlPendente) {
        alert('Informe o Link (https://) ou apague a Descrição do Link.');
        $('#modal_ma_link_url_ctr').focus();
        return;
    }

    var fd = new FormData();
    fd.append('ctr_id', _ctrAnexosParams.ctr_id);

    $('.ma-file-input-ctr').each(function () {
        if (this.files && this.files[0]) {
            fd.append('anexo[]', this.files[0]);
        }
    });

    $('#modal_ma_lista_links_ctr .ma-link-row-ctr').each(function () {
        var url  = $(this).find('.ma-link-url-ctr').val().trim();
        var desc = $(this).find('.ma-link-desc-ctr').val().trim();
        if (url) {
            fd.append('anexo_link_url[]', url);
            fd.append('anexo_link_desc[]', desc || url);
        }
    });

    var $btn = $('#btn_confirmar_ma_ctr');
    $btn.prop('disabled', true).text('Salvando...');

    $.ajax({
        type:        'POST',
        url:         'api/salvar_anexos_modal_ctr.php',
        data:        fd,
        processData: false,
        contentType: false,
        dataType:    'json',
        success: function (resp) {
            if (resp.ok) {
                _maResetarCtr();
                _carregarAnexosCtr();
            } else {
                alert('Erro: ' + (resp.msg || 'Não foi possível salvar.'));
            }
        },
        error: function () {
            alert('Erro ao comunicar com o servidor.');
        },
        complete: function () {
            $btn.prop('disabled', false).text('Confirmar');
        }
    });
}
</script>
