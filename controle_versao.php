<?php
include "conecta_mysql.inc";

$sql = "SELECT * FROM tbl_versao 
        WHERE tbl_versao_lixeira = 0 
        ORDER BY tbl_versao_codigo_id DESC";

$res = mysqli_query($conector, $sql);

$versoes = [];

while ($row = mysqli_fetch_array($res)) {
    $versoes[] = $row;
}

if (empty($versoes)) {
    die("Nenhuma versão cadastrada no sistema.");
}

$atual = $versoes[0];
$data_atual_br = date('d/m/Y', strtotime($atual['tbl_versao_data']));
$exibir_historico = isset($_GET['view']) && $_GET['view'] == 'historico';

function tratarDescricaoVersao($texto) {
    $texto = trim($texto);

    // Permite apenas tags HTML simples e seguras para a nota da versão
    $tags_permitidas = '<h4><h5><p><br><ul><ol><li><strong><b><em><i>';

    return strip_tags($texto, $tags_permitidas);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boi Virtual - Versões</title>

    <style>
        @font-face {
            font-family: 'FuturaStd-Light';
            src: url('fonts/FuturaStd-Light.otf') format('opentype');
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            background-color: #f0f2f5;
            font-family: 'FuturaStd-Light', Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .janela-modal {
            width: 580px;
            height: 560px;
            background: white;
            border-radius: 0;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .topo-modal {
            padding: 12px 16px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #2878a8;
            font-size: 1.2em;
            flex-shrink: 0;
        }

        .fechar-topo {
            text-decoration: none;
            color: #ccc;
            font-size: 1.2em;
        }

        .conteudo-principal {
            padding: 12px 20px 0 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1;
            overflow: hidden;
        }

        .logo-img {
            width: 75px;
            height: auto;
            margin-bottom: 6px;
            flex-shrink: 0;
        }

        .titulo-versao {
            font-size: 1.25em;
            color: #111;
            font-weight: bold;
            margin: 0;
            line-height: 1.1;
        }

        .data-versao {
            color: #666;
            font-size: 0.82em;
            margin-bottom: 14px;
        }

        .secao-novidades {
            text-align: left;
            background: #fff;
            padding: 0 14px 14px 14px;
            border-radius: 6px;
            border: 1px solid #eee;
            width: 100%;
            flex-grow: 1;
            overflow-y: auto;
        }

        .secao-novidades h4 {
            margin: 0;
            padding: 14px 0 8px 0;
            color: #333;
            font-size: 0.95em;
            font-weight: normal;
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 10;
        }

        .texto-descricao {
            line-height: 1.45;
            color: #444;
            font-size: 0.86em;
            margin-top: 8px;
            text-align: left;
            white-space: normal;
        }

        .texto-descricao h5 {
            margin: 16px 0 7px 0;
            font-size: 1em;
            color: #333;
            font-weight: bold;
        }

        .texto-descricao h5:first-child {
            margin-top: 4px;
        }

        .texto-descricao p {
            margin: 10px 0 6px 0;
        }

        .texto-descricao strong {
            font-weight: bold;
            color: #333;
        }

        .texto-descricao ul {
            list-style-type: disc;
            list-style-position: outside;
            margin: 5px 0 12px 20px;
            padding-left: 18px;
        }
        .texto-descricao li {
            margin-bottom: 5px;
            padding-left: 2px;
        }

        .rodape-modal {
            padding: 10px 16px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            background: #fff;
            flex-shrink: 0;
        }

        .btn-fechar {
            background: #fff;
            color: #555;
            border: 1px solid #ccc;
            padding: 8px 14px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85em;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }

        .btn-historico {
            display: block;
            background-color: #2da44e;
            color: white;
            padding: 10px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 10px;
            font-size: 0.85em;
            width: 100%;
            text-align: center;
            flex-shrink: 0;
        }

        .historico-scroll {
            flex-grow: 1;
            width: 100%;
            overflow-y: auto;
            padding: 20px 20px 20px 45px;
        }

        .timeline {
            position: relative;
            width: 100%;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 5px;
            bottom: 0;
            width: 2px;
            background: #e1e4e8;
        }

        .card-historico {
            position: relative;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
            background: #fff;
            width: 100%;
            text-align: left;
        }

        .dot {
            position: absolute;
            left: -31px;
            top: 18px;
            width: 10px;
            height: 10px;
            background: #fff;
            border: 2px solid #e1e4e8;
            border-radius: 50%;
            z-index: 2;
        }

        .card-historico.atual {
            border-color: #2da44e;
        }

        .card-historico.atual .dot {
            background: #2da44e;
            border-color: #2da44e;
        }

        .voltar {
            color: #2da44e;
            text-decoration: none;
            font-size: 0.85em;
            margin: 10px 0;
            display: block;
            font-weight: bold;
            text-align: center;
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: #aaa;
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="janela-modal">

    <div class="topo-modal">
        <span><?php echo $exibir_historico ? 'Histórico de Alterações' : 'Notas da Versão'; ?></span>
        <a href="?" class="fechar-topo">×</a>
    </div>

    <?php if (!$exibir_historico): ?>

        <div class="conteudo-principal">

            <img src="img/boi_virtual_preto.png" alt="Boi Virtual" class="logo-img">

            <div class="titulo-versao">
                Versão <?php echo $atual['tbl_versao_numero']; ?>
            </div>

            <div class="data-versao">
                <?php echo $data_atual_br; ?>
            </div>

            <div class="secao-novidades">
                <h4>Novidades:</h4>

                <div class="texto-descricao">
                    <?php echo tratarDescricaoVersao($atual['tbl_versao_descricao']); ?>
                </div>
            </div>

        </div>

        <div class="rodape-modal">
            <a href="?" class="btn-fechar">Fechar1</a>
        </div>

    <?php else: ?>

        <div class="historico-scroll">
            <div class="timeline">

                <?php foreach ($versoes as $index => $v): 
                    $data_br = date('d/m/Y', strtotime($v['tbl_versao_data']));
                ?>

                    <div class="card-historico <?php echo $index === 0 ? 'atual' : ''; ?>">
                        <div class="dot"></div>

                        <strong style="font-size: 0.9em;">
                            Versão <?php echo $v['tbl_versao_numero']; ?>
                        </strong>

                        <span style="color:#888; font-size:0.75em; margin-left:8px;">
                            <?php echo $data_br; ?>
                        </span>

                        <div class="texto-descricao" style="margin-top:8px; border-top:1px solid #f2f2f2; padding-top:8px;">
                            <?php echo tratarDescricaoVersao($v['tbl_versao_descricao']); ?>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

            <a href="?" class="voltar">← Voltar para resumo</a>
        </div>

    <?php endif; ?>

</div>

</body>
</html>