<?php
// 1. Conexão com o Banco
include "conecta_mysql.inc";

$mensagem = "";

// Função para garantir que o texto entre limpo no banco (sem espaços/tabs no início)
function tratarTexto($texto) {
    $texto = preg_replace('/^[ \t]+/m', '', $texto);
    return trim($texto);
}

// 2. Lógica de Gravação
if (isset($_POST['btn_salvar'])) {
    // Escapa caracteres especiais para segurança do MySQL
    $numero      = mysqli_real_escape_string($conector, $_POST['txt_numero']);
    $data_v      = mysqli_real_escape_string($conector, $_POST['txt_data']);
    $descricao   = mysqli_real_escape_string($conector, tratarTexto($_POST['txt_descricao']));
    $usuario     = "George"; // Nome do autor da alteração
    $agora       = date('Y-m-d H:i:s');

    $sql_insert = "INSERT INTO tbl_versao (
                        tbl_versao_numero, 
                        tbl_versao_descricao, 
                        tbl_versao_data, 
                        tbl_versao_incluido_em, 
                        tbl_versao_incluido_por, 
                        tbl_versao_lixeira
                   ) VALUES (
                        '$numero', 
                        '$descricao', 
                        '$data_v', 
                        '$agora', 
                        '$usuario', 
                        0
                   )";

    if (mysqli_query($conector, $sql_insert)) {
        $mensagem = "<div class='alerta sucesso'>✅ Versão $numero lançada com sucesso!</div>";
    } else {
        $mensagem = "<div class='alerta erro'>❌ Erro ao gravar: " . mysqli_error($conector) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Boi Virtual - Painel de Versões</title>
    <style>
        @font-face {
            font-family: 'FuturaStd-Light';
            src: url('fonts/FuturaStd-Light.otf') format('opentype');
        }

        body { 
            font-family: 'FuturaStd-Light', Arial, sans-serif; 
            background-color: #f0f2f5; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
        }

        .container-cadastro { 
            width: 520px; 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            border: 1px solid #ddd;
        }

        h2 { color: #222; margin-top: 0; border-bottom: 2px solid #2da44e; padding-bottom: 10px; font-size: 1.5em; }

        .campo { margin-bottom: 20px; text-align: left; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 0.9em; }

        input[type="text"], input[type="date"], textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            font-family: inherit;
            font-size: 1em;
        }

        textarea { height: 120px; resize: vertical; }

        .btn-salvar { 
            background-color: #2da44e; 
            color: white; 
            border: none; 
            padding: 15px; 
            border-radius: 6px; 
            width: 100%; 
            font-weight: bold; 
            cursor: pointer; 
            font-size: 1em;
            transition: 0.3s;
        }
        .btn-salvar:hover { background-color: #248a41; }

        .alerta { padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: bold; font-size: 0.9em; }
        .sucesso { background: #e6ffed; color: #22863a; border: 1px solid #bef5cb; }
        .erro { background: #ffeef0; color: #d73a49; border: 1px solid #f9d7dc; }

        .link-ver { display: block; text-align: center; margin-top: 20px; color: #2da44e; text-decoration: none; font-size: 0.85em; font-weight: bold; }
    </style>
</head>
<body>

<div class="container-cadastro">
    <h2>🚀 Nova Atualização</h2>
    
    <?php echo $mensagem; ?>

    <form method="POST">
        <div class="campo">
            <label>Número da Versão</label>
            <input type="text" name="txt_numero" placeholder="Ex: 1.02" required>
        </div>

        <div class="campo">
            <label>Data de Lançamento</label>
            <input type="date" name="txt_data" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="campo">
            <label>Descrição das Mudanças (Release Notes)</label>
            <textarea name="txt_descricao" placeholder="Dica: Use 1 - Para listar itens..." required></textarea>
        </div>

        <button type="submit" name="btn_salvar" class="btn-salvar">Publicar no Boi Virtual</button>
    </form>

    <a href="controle_versao.php" target="_blank" class="link-ver">← Ver como ficou na tela do sistema</a>
</div>

</body>
</html>