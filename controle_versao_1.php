<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";

    // Busca todas as versões que não estão na lixeira, da mais nova para a mais antiga
    $sql = "SELECT * FROM tbl_versao 
            WHERE tbl_versao_lixeira = 0 OR tbl_versao_lixeira IS NULL 
            ORDER BY tbl_versao_codigo_id DESC";
    
    $resultado = mysqli_query($conector, $sql);

    // Armazena os dados em um array para facilitar a exibição
    $versoes = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $versoes[] = $linha;
    }

    // A primeira versão do array (índice 0) é a mais recente devido ao ORDER BY DESC
    $versao_atual = !empty($versoes) ? $versoes[0] : null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Versões do Sistema</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f6fa; margin: 0; padding: 20px; }
    .container { max-width: 500px; margin: auto; }
    .card { background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
    .version { font-size: 28px; font-weight: bold; color: #2f80ed; }
    .status { margin-top: 8px; color: #27ae60; font-size: 14px; }
    .btn { display: inline-block; margin-top: 15px; padding: 10px 15px; background: #2f80ed; color: white; border-radius: 8px; text-decoration: none; cursor: pointer; border: none; }
    .btn:hover { background: #1366d6; }
    .changelog { display: none; }
    .version-item { margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
    .version-header { font-weight: bold; margin-bottom: 5px; }
    .date { font-size: 12px; color: #888; }
    .desc-text { font-size: 14px; color: #444; margin-top: 5px; white-space: pre-line; }
  </style>
</head>
<body>

<div class="container">

  <?php if ($versao_atual): ?>
  <div class="card">
    <div class="title">Boi Virtual</div>
    <div class="version">Versão <?php echo $versao_atual['tbl_versao_numero']; ?></div>
    <div class="status">✔ Você está usando a versão mais recente</div>

    <button class="btn" onclick="toggleChangelog()">
      Ver histórico e detalhes
    </button>
  </div>
  <?php else: ?>
    <div class="card">Nenhuma versão encontrada no banco de dados.</div>
  <?php endif; ?>

  <div class="card changelog" id="changelog">
    <div class="title">Histórico de versões</div>

    <?php foreach ($versoes as $v): ?>
    <div class="version-item">
      <div class="version-header">
        Versão <?php echo $v['tbl_versao_numero']; ?>
        <span class="date">- <?php echo date('d/m/Y', strtotime($v['tbl_versao_data'])); ?></span>
      </div>
      <div class="desc-text">
        <?php echo $v['tbl_versao_descricao']; ?>
      </div>
    </div>
    <?php endforeach; ?>

  </div>

</div>

<script>
  function toggleChangelog() {
    const el = document.getElementById("changelog");
    el.style.display = (el.style.display === "block") ? "none" : "block";
  }
</script>

</body>
</html>