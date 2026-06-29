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
                    <span id="modal_anexos_doc" style="font-size:13px;font-weight:400;color:#888;margin-left:6px;"></span>
                </h4>
            </div>
            <div class="modal-body" style="min-height:60px;padding:0 15px 10px 15px;">

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
                        <div style="display:flex;align-items:flex-end;gap:24px;flex-wrap:wrap;margin-top:6px;">
                            <div>
                                <label style="font-size:12px;color:#555;font-weight:600;display:block;margin-bottom:4px;">Anexar Documento</label>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <input type="file" id="modal_ma_file_0" class="form-control ma-file-input" style="max-width:280px;">
                                    <button type="button" class="btn-ma-add" onclick="_maAdicionarArquivo()" data-toggle="tooltip" data-placement="top" title="Adicionar mais documentos">
                                        <i class="far fa-plus-square"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label style="font-size:12px;color:#555;font-weight:600;display:block;margin-bottom:4px;">Anexar Link</label>
                                <button type="button" class="btn-ma-add" onclick="_maAdicionarLink()" data-toggle="tooltip" data-placement="top" title="Adicionar link">
                                    <i class="fas fa-link"></i>
                                </button>
                            </div>
                        </div>
                        <div id="modal_ma_extra"></div>
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
var _ctpAnexosParams  = {};
var _ctpAnexosHandler = false;

function abrirModalAnexos(numero_doc, codigo_fornecedor, ctp_id, doc_display) {
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

        $('#modal_ma_file_0').on('change', _maAtualizarBotao);

        $('#modal_anexos').on('hidden.bs.modal', function () {
            _maResetar();
        });

        _ctpAnexosHandler = true;
    }

    $('#modal_anexos_doc').text('— Documento: ' + doc_display);
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
        },
        error: function () {
            $('#modal_anexos_body').html(
                '<p class="text-danger" style="padding:10px 0;">Erro ao carregar os anexos.</p>'
            );
        }
    });
}

/* ── Funções da seção de adição ── */

function _maResetar() {
    $('#modal_ma_file_0').val('');
    $('#modal_ma_extra').empty();
    $('#btn_confirmar_ma').hide();
}

function _maAdicionarArquivo() {
    var div = $('<div>').css({ display: 'flex', alignItems: 'center', gap: '6px', marginTop: '6px' });
    div.html(
        '<input type="file" class="form-control ma-file-input" style="max-width:280px;">' +
        '<button type="button" class="btn-ma-add" onclick="_maRemover(this)" title="Remover">' +
        '<i class="far fa-times-circle" style="font-size:16px;color:#c0392b;"></i></button>'
    );
    div.find('.ma-file-input').on('change', _maAtualizarBotao);
    $('#modal_ma_extra').append(div);
}

function _maAdicionarLink() {
    var div = $('<div class="ma-link-row">').css({ display: 'flex', alignItems: 'center', gap: '6px', marginTop: '6px', flexWrap: 'nowrap' });
    div.html(
        '<i class="fas fa-link" style="color:#337ab7;font-size:14px;flex-shrink:0;"></i>' +
        '<input type="text" class="form-control ma-link-desc" placeholder="Descrição do link" style="max-width:180px;">' +
        '<input type="url"  class="form-control ma-link-url"  placeholder="https://..." style="max-width:260px;">' +
        '<button type="button" class="btn-ma-add" onclick="_maRemover(this)" title="Remover">' +
        '<i class="far fa-times-circle" style="font-size:16px;color:#c0392b;"></i></button>'
    );
    div.find('.ma-link-url').on('input', _maAtualizarBotao);
    $('#modal_ma_extra').append(div);
}

function _maRemover(btn) {
    $(btn).closest('div').remove();
    _maAtualizarBotao();
}

function _maAtualizarBotao() {
    var temAlgo = false;
    $('.ma-file-input').each(function () {
        if (this.files && this.files.length > 0) { temAlgo = true; return false; }
    });
    if (!temAlgo) {
        $('.ma-link-url').each(function () {
            if ($(this).val().trim()) { temAlgo = true; return false; }
        });
    }
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

    $('#modal_ma_extra .ma-link-row').each(function () {
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
