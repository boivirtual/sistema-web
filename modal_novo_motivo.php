<?php
include "conecta_mysql.inc";

$totalSelecionados = isset($_POST['total_selecionados']) ? (int)$_POST['total_selecionados'] : 0;
$itensSelecionados = isset($_POST['itens_selecionados']) ? trim($_POST['itens_selecionados']) : '';

$html = '';

$html .= '
<div class="form-group" style="margin-bottom:10px;">
    <label style="margin-right:5px;">Itens Selecionados:</label>
    <span style="font-weight: bold;">' . $totalSelecionados . '</span>
</div>
';

$html .= '<div class="form-group">';
$html .= '<label for="novo_motivo_select">Selecione o Novo Motivo:</label>';
$html .= '<select class="form-control" id="novo_motivo_select" name="novo_motivo_select">';
$html .= '<option value="">...</option>';

$tbl_novo_motivo = mysqli_query($conector, "
    SELECT * 
    FROM tabela_epoca_pesagem 
    WHERE tab_registro_lixeira_epoca_pesagem = 0
");

if ($tbl_novo_motivo) {
    while ($reg = mysqli_fetch_object($tbl_novo_motivo)) {
        $codigo = $reg->tab_codigo_epoca_pesagem;
        $descricao = $reg->tab_descricao_epoca_pesagem;

        $html .= '<option value="' . htmlspecialchars($codigo) . '">' . htmlspecialchars($descricao) . '</option>';
    }
}

$html .= '</select>';
$html .= '</div>';

$html .= '<input type="hidden" id="itens_selecionados_novo_motivo" value="' . htmlspecialchars($itensSelecionados) . '">';

echo $html;
?>