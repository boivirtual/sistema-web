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

<!-- Modal: Visualizar Anexos / Links -->
<div class="modal fade" id="modal_anexos" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document" style="width:600px;max-width:96%;">
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
                <div id="modal_anexos_body">
                    <p class="text-center text-muted" style="padding:20px 0;">
                        <i class="fas fa-spinner fa-spin"></i> Carregando...
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
var _ctpAnexosParams = {};

function abrirModalAnexos(numero_doc, codigo_fornecedor, ctp_id, doc_display) {
    _ctpAnexosParams = { numero_doc: numero_doc, codigo_fornecedor: codigo_fornecedor, ctp_id: ctp_id, doc_display: doc_display };
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
</script>
