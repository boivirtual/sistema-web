<?php
/**
 * modal_anexos.php
 * Include reutilizável: modal Bootstrap 3 + função JS abrirModalAnexos().
 *
 * Como usar em qualquer programa:
 *   1. No PHP da página: <?php include "modal_anexos.php"; ?>
 *   2. No onclick do ícone: abrirModalAnexos(numero_doc, codigo_fornecedor, ctp_id, doc_display)
 *
 * Parâmetros de abrirModalAnexos():
 *   numero_doc        — número do documento (string)
 *   codigo_fornecedor — código do fornecedor (int)
 *   ctp_id            — id da parcela (int, fallback quando numero_doc vazio)
 *   doc_display       — texto exibido no cabeçalho do modal (ex: "000003916")
 */
?>

<style>
.btn-ma-add {
    background: none;
    border: none;
    color: #337ab7;
    font-size: 20px;
    padding: 0 4px;
    cursor: pointer;
    vertical-align: middle;
    line-height: 1;
}
.btn-ma-add:hover { color: #23527c; }
</style>

<!-- Modal: Visualizar Anexos / Links -->
<div class="modal fade" id="modal_anexos" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
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

                <p id="modal_anexos_doc" style="margin:0 0 10px 0;font-size:14px;color:#333;font-weight:500;"></p>

                <!-- Lista de anexos existentes (recarregada por AJAX) -->
                <div id="modal_anexos_body">
                    <p class="text-center text-muted" style="padding:20px 0;">
                        <i class="fas fa-spinner fa-spin"></i> Carregando...
                    </p>
                </div>

                <!-- Seção: adicionar novos anexos/links -->
                <div id="modal_ma_area" style="padding:4px 0 0 0;">
                    <hr style="margin:10px 0 8px 0;">

                    <a href="#" id="modal_ma_toggle" onclick="_maAbrirInputs(); return false;"
                       style="font-size:0.9em;font-weight:500;color:#128cb8;">
                        <i class="fa fa-plus"></i> Anexos
                    </a>

                    <div id="modal_ma_inputs" style="display:none;">
                        <div style="display:flex;align-items:flex-start;gap:48px;flex-wrap:wrap;margin-top:6px;">
                            <div>
                                <label style="font-size:12px;color:#555;font-weight:600;display:block;margin-bottom:4px;">
                                    <i class="fas fa-paperclip" style="color:#337ab7;"></i> Anexar Documento
                                </label>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <input type="file" id="modal_ma_picker" class="form-control" style="max-width:280px;" onchange="_maOnPickerChange(this)">
                                </div>
                                <div id="modal_ma_lista_anexos"></div>
                            </div>
                            <div style="margin-left:16px;">
                                <label style="font-size:12px;color:#555;font-weight:600;display:block;margin-bottom:4px;">
                                    <i class="fas fa-link" style="color:#337ab7;"></i> Anexar Link
                                </label>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <input type="text" id="modal_ma_link_desc" class="form-control" placeholder="Descrição do link" style="max-width:180px;">
                                    <input type="url" id="modal_ma_link_url" class="form-control" placeholder="https://..." style="max-width:220px;" onkeydown="_maOnLinkUrlKeydown(event)" onblur="_maOnLinkUrlBlur()">
                                </div>
                                <div id="modal_ma_lista_links"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="btn_confirmar_ma" class="btn btn-primary" style="display:none;" onclick="_maConfirmar()">Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
var _ctpAnexosParams     = {};
var _ctpAnexosHandler    = false;
var _ctpAnexosAutoInputs = false;

function abrirModalAnexos(numero_doc, codigo_fornecedor, ctp_id, doc_display, abrirInputs) {
    _ctpAnexosParams = { numero_doc: numero_doc, codigo_fornecedor: codigo_fornecedor, ctp_id: ctp_id, doc_display: doc_display };

    if (!_ctpAnexosHandler) {

        $(document).on('click', '.btn-excluir-anexo', function () {
            var id   = $(this).data('id');
            var nome = $(this).data('nome');
            if (!confirm('Deseja excluir o anexo/link:\n"' + nome + '"?')) return;

            $.ajax({
                type:     'POST',
                url:      'api/excluir_anexo.php',
                data:     { anexo_id: id },
                dataType: 'json',
                success: function (resp) {
                    if (resp.ok) {
                        _carregarAnexos();
                    } else {
                        alert('Erro: ' + (resp.msg || 'Não foi possível excluir.'));
                    }
                },
                error: function () {
                    alert('Erro ao comunicar com o servidor.');
                }
            });
        });

        $('#modal_anexos').on('hidden.bs.modal', function () {
            _maResetar();
        });

        _ctpAnexosHandler = true;
    }

    _ctpAnexosAutoInputs = !!abrirInputs;
    $('#modal_anexos_doc').text('Documento Nº: ' + doc_display);
    _maResetar();
    $('#modal_anexos').modal('show');
    _carregarAnexos();
}

function _carregarAnexos() {
    $('#modal_anexos_body').html(
        '<p class="text-center text-muted" style="padding:20px 0;">' +
        '<i class="fas fa-spinner fa-spin"></i> Carregando...</p>'
    );
    $.ajax({
        type: 'GET',
        url:  'api/get_anexos.php',
        data: {
            numero_doc:        _ctpAnexosParams.numero_doc,
            codigo_fornecedor: _ctpAnexosParams.codigo_fornecedor,
            ctp_id:            _ctpAnexosParams.ctp_id
        },
        success: function (html) {
            $('#modal_anexos_body').html(html);
            $('[data-toggle="tooltip"]').tooltip();
            if (_ctpAnexosAutoInputs && $('#modal_anexos_body li').length === 0) {
                _maAbrirInputs();
            }
            _ctpAnexosAutoInputs = false;
        },
        error: function () {
            $('#modal_anexos_body').html(
                '<p class="text-danger" style="padding:10px 0;">Erro ao carregar os anexos.</p>'
            );
        }
    });
}

/* ── Funções da seção de adição ── */

function _maAbrirInputs() {
    $('#modal_ma_toggle').hide();
    $('#modal_ma_inputs').show();
}

function _maResetar() {
    $('#modal_ma_picker').val('');
    $('#modal_ma_link_desc').val('');
    $('#modal_ma_link_url').val('');
    $('#modal_ma_lista_anexos').empty();
    $('#modal_ma_lista_links').empty();
    $('#btn_confirmar_ma').hide();
    $('#modal_ma_inputs').hide();
    $('#modal_ma_toggle').show();
}

function _maCriarBotaoRemover(onRemove) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn-ma-add';
    btn.title = 'Remover';
    btn.setAttribute('data-toggle', 'tooltip');
    btn.setAttribute('data-placement', 'top');
    btn.innerHTML = '<i class="fas fa-trash" style="font-size:12px; color:#337ab7;"></i>';
    btn.onclick = onRemove;
    $(btn).tooltip();
    return btn;
}

function _maOnPickerChange(input) {
    if (!input.files || !input.files.length) return;
    _maCriarLinhaAnexoArquivo(input.files[0]);
    input.value = ''; // limpa para permitir escolher o próximo arquivo
    _maAtualizarBotao();
}

function _maCriarLinhaAnexoArquivo(file) {
    var dt = new DataTransfer();
    dt.items.add(file);

    var hidden = document.createElement('input');
    hidden.type = 'file';
    hidden.className = 'ma-file-input';
    hidden.style.display = 'none';
    hidden.files = dt.files;

    var nome = document.createElement('span');
    nome.textContent = file.name;
    nome.style.cssText = 'max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;';

    var icone = document.createElement('i');
    icone.className = 'fas fa-paperclip';
    icone.style.cssText = 'color:#337ab7;font-size:14px;flex-shrink:0;';

    var div = document.createElement('div');
    div.className = 'linha-anexo-arquivo';
    div.style.cssText = 'display:flex;align-items:center;gap:8px;margin-top:6px;';

    var btnRemover = _maCriarBotaoRemover(function () { _maRemoverLinha(div); });

    div.appendChild(icone);
    div.appendChild(nome);
    div.appendChild(btnRemover);
    div.appendChild(hidden);
    $('#modal_ma_lista_anexos').append(div);
}

// Enter no campo URL sai do foco (dispara _maOnLinkUrlBlur) em vez de tentar submeter algo.
function _maOnLinkUrlKeydown(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        event.target.blur();
    }
}

// Ao sair do foco da URL, se preenchida, cria a linha de exibição do link
// e limpa os inputs fixos de Descrição/URL para o próximo link.
function _maOnLinkUrlBlur() {
    var $desc = $('#modal_ma_link_desc');
    var $url  = $('#modal_ma_link_url');
    var url = $url.val().trim();
    if (!url) return; // nada digitado

    var desc = $desc.val().trim() || url;
    _maCriarLinhaLink(desc, url);

    $desc.val('');
    $url.val('');
    _maAtualizarBotao();
}

function _maCriarLinhaLink(desc, url) {
    var hiddenDesc = document.createElement('input');
    hiddenDesc.type = 'hidden';
    hiddenDesc.className = 'ma-link-desc';
    hiddenDesc.value = desc;

    var hiddenUrl = document.createElement('input');
    hiddenUrl.type = 'hidden';
    hiddenUrl.className = 'ma-link-url';
    hiddenUrl.value = url;

    var icone = document.createElement('i');
    icone.className = 'fas fa-link';
    icone.style.cssText = 'color:#337ab7;font-size:14px;flex-shrink:0;';

    var texto = document.createElement('span');
    texto.textContent = desc + ' — ' + url;
    texto.style.cssText = 'max-width:360px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;';

    var div = document.createElement('div');
    div.className = 'ma-link-row';
    div.style.cssText = 'display:flex;align-items:center;gap:8px;margin-top:6px;';

    var btnRemover = _maCriarBotaoRemover(function () { _maRemoverLinha(div); });

    div.appendChild(icone);
    div.appendChild(texto);
    div.appendChild(btnRemover);
    div.appendChild(hiddenDesc);
    div.appendChild(hiddenUrl);
    $('#modal_ma_lista_links').append(div);
}

function _maRemoverLinha(div) {
    $(div).remove();
    _maAtualizarBotao();
}

function _maAtualizarBotao() {
    var temAlgo = $('#modal_ma_lista_anexos').children().length > 0
               || $('#modal_ma_lista_links').children().length > 0;
    $('#btn_confirmar_ma').toggle(temAlgo);
}

function _maConfirmar() {
    var fd = new FormData();
    fd.append('ctp_id', _ctpAnexosParams.ctp_id);

    $('.ma-file-input').each(function () {
        if (this.files && this.files[0]) {
            fd.append('anexo[]', this.files[0]);
        }
    });

    $('#modal_ma_lista_links .ma-link-row').each(function () {
        var url  = $(this).find('.ma-link-url').val().trim();
        var desc = $(this).find('.ma-link-desc').val().trim();
        if (url) {
            fd.append('anexo_link_url[]', url);
            fd.append('anexo_link_desc[]', desc || url);
        }
    });

    var $btn = $('#btn_confirmar_ma');
    $btn.prop('disabled', true).text('Salvando...');

    $.ajax({
        type:        'POST',
        url:         'api/salvar_anexos_modal.php',
        data:        fd,
        processData: false,
        contentType: false,
        dataType:    'json',
        success: function (resp) {
            if (resp.ok) {
                _maResetar();
                _carregarAnexos();
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
