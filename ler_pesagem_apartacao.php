<?php
include "conecta_mysql.inc";

$codigo_pesagem = $_POST['codigo_pesagem'];

$sql = "SELECT tbl_ite_pesagem_criterio_apartacao as criterio, COUNT(*) as total 
        FROM tbl_item_pesagem 
        WHERE tbl_ite_pesagem_numero_id = '$codigo_pesagem' 
        GROUP BY tbl_ite_pesagem_criterio_apartacao 
        ORDER BY total DESC";

$rs = mysqli_query($conector, $sql);

$html = '<div style="padding: 0 10px;">';
$html .= '<ul class="list-group" style="box-shadow: none; border: none; margin-bottom: 0;">';

$total_geral = 0;

if (mysqli_num_rows($rs) > 0) {
    while ($linha = mysqli_fetch_array($rs)) {
        $criterio = $linha['criterio'] ? $linha['criterio'] : "Não Definido";
        $qtd = $linha['total'];
        $total_geral += $qtd;

        // Item da lista com linha separadora cinza claro
        $html .= '<li style="list-style: none; border-bottom: 1px solid #eeeeee; padding: 12px 5px; display: flex; justify-content: space-between; align-items: center;">';
        
        // Nome da Apartação (Lado Esquerdo)
        $html .= '  <span style="font-size: 14px; color: #333333; font-weight: 300;">' . $criterio . '</span>';
        
        // Quantidade em Azul Transparente (Lado Direito)
        $html .= '  <span style="color: #128cb8; font-size: 16px; font-weight: 300;">' . $qtd . '</span>';
        
        $html .= '</li>';
    }
} else {
    $html .= '<li style="list-style: none; padding: 20px; text-align: center; color: #999;">Nenhum registro encontrado.</li>';
}

$html .= '</ul>';

// Rodapé Totalizador (Seguindo o estilo limpo)
$html .= '<div style="padding: 15px 5px; display: flex; justify-content: space-between; align-items: center;">';
$html .= '  <span style="color: #000; font-size: 14px; font-weight: 400;">TOTAL GERAL</span>';
$html .= '  <span style="color: #128cb8; font-size: 18px; font-weight: 400;">' . $total_geral . '</span>';
$html .= '</div>';

$html .= '</div>';

echo $html;
?>