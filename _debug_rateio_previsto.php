<?php
include_once "conecta_mysql_credenciais.inc";
$banco = "97174041604";

$conector = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
if (mysqli_connect_error()) {
    die("Falha na conexao: " . mysqli_connect_error());
}

$data_inicial = '2026-01-01';
$data_final = '2026-12-31';

$sql = "SELECT * FROM baixa_contas_pagar
    inner join contas_pagar
            on ctp_id=bcp_id
    WHERE bcp_data_pagamento >='$data_inicial' AND
          bcp_data_pagamento <='$data_final'";

$res = mysqli_query($conector, $sql);
if (!$res) {
    die("Erro SQL: " . mysqli_error($conector));
}

$achou = false;
while ($row = mysqli_fetch_object($res)) {
    if ($row->ctp_numero_doc == '125') {
        $achou = true;
        echo "ENCONTROU doc 125 no resultado da query principal:\n";
        echo "ctp_id={$row->ctp_id} bcp_data_pagamento={$row->bcp_data_pagamento} bcp_valor_pagamento={$row->bcp_valor_pagamento} ctp_codigo_conta=" . var_export($row->ctp_codigo_conta, true) . "\n";
    }
}

if (!$achou) {
    echo "NAO encontrou o doc 125 na query principal (bcp_data_pagamento entre $data_inicial e $data_final)\n";
}

mysqli_close($conector);
