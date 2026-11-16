<?php
session_write_close();
include "conecta_mysql.inc";
@ session_start();
$nomeusuario = $_SESSION['nome_usuario'] ?? 'sistema';

header('Content-type: application/json');

$primeiro_ctr_id = isset($_POST['primeiro_ctr_id']) ? intval($_POST['primeiro_ctr_id']) : 0;
$json            = isset($_POST['rateio_json'])      ? trim($_POST['rateio_json'])       : '';

if ($primeiro_ctr_id <= 0) {
    echo json_encode(['error' => true, 'message' => 'ID de rateio inválido.']);
    exit;
}
if (empty($json) || $json === '[]' || $json === 'null') {
    echo json_encode(['error' => true, 'message' => 'Nenhuma linha de rateio informada.']);
    exit;
}

$locais = json_decode($json, true);
if (!is_array($locais) || count($locais) === 0) {
    echo json_encode(['error' => true, 'message' => 'JSON de rateio inválido.']);
    exit;
}

$data_sistema = date('Y-m-d H:i:s');
$usuario_esc  = mysqli_real_escape_string($conector, $nomeusuario);

// Remove rateio atual
if (!mysqli_query($conector, "DELETE FROM tbl_ctr_rateio WHERE rc_ctr_id = '$primeiro_ctr_id'")) {
    echo json_encode(['error' => true, 'message' => 'Erro ao remover rateio antigo: ' . mysqli_error($conector)]);
    exit;
}

// Regrava com a mesma lógica de salvar_rateio() em gravar_contas_receber.php
foreach ($locais as $loc) {
    $rc_cod_local  = (int)($loc['id']    ?? 0);
    $rc_nom_local  = mysqli_real_escape_string($conector, $loc['nome']  ?? '');
    $rc_perc_local = (float)($loc['perc']  ?? 0);
    $rc_val_local  = (float)($loc['valor'] ?? 0);

    $ccs = $loc['ccs'] ?? [];
    if (count($ccs) === 0) {
        mysqli_query($conector,
            "INSERT INTO tbl_ctr_rateio
                 (rc_ctr_id, rc_codigo_local, rc_nome_local, rc_perc_local, rc_valor_local,
                  rc_incluido_em, rc_incluido_por)
             VALUES
                 ('$primeiro_ctr_id','$rc_cod_local','$rc_nom_local','$rc_perc_local','$rc_val_local',
                  '$data_sistema','$usuario_esc')");
        continue;
    }

    foreach ($ccs as $cc) {
        $rc_cod_cc  = mysqli_real_escape_string($conector, $cc['id']    ?? '');
        $rc_nom_cc  = mysqli_real_escape_string($conector, $cc['nome']  ?? '');
        $rc_perc_cc = (float)($cc['perc']  ?? 0);
        $rc_val_cc  = (float)($cc['valor'] ?? 0);

        $contas = $cc['contas'] ?? [];
        if (count($contas) === 0) {
            mysqli_query($conector,
                "INSERT INTO tbl_ctr_rateio
                     (rc_ctr_id, rc_codigo_local, rc_nome_local, rc_perc_local, rc_valor_local,
                      rc_codigo_cc, rc_nome_cc, rc_perc_cc, rc_valor_cc,
                      rc_incluido_em, rc_incluido_por)
                 VALUES
                     ('$primeiro_ctr_id','$rc_cod_local','$rc_nom_local','$rc_perc_local','$rc_val_local',
                      '$rc_cod_cc','$rc_nom_cc','$rc_perc_cc','$rc_val_cc',
                      '$data_sistema','$usuario_esc')");
            continue;
        }

        foreach ($contas as $ct) {
            $rc_cod_conta  = mysqli_real_escape_string($conector, $ct['id']    ?? '');
            $rc_nom_conta  = mysqli_real_escape_string($conector, $ct['nome']  ?? '');
            $rc_perc_conta = (float)($ct['perc']  ?? 0);
            $rc_val_conta  = (float)($ct['valor'] ?? 0);

            mysqli_query($conector,
                "INSERT INTO tbl_ctr_rateio
                     (rc_ctr_id, rc_codigo_local, rc_nome_local, rc_perc_local, rc_valor_local,
                      rc_codigo_cc, rc_nome_cc, rc_perc_cc, rc_valor_cc,
                      rc_codigo_conta, rc_nome_conta, rc_perc_conta, rc_valor_conta,
                      rc_incluido_em, rc_incluido_por)
                 VALUES
                     ('$primeiro_ctr_id','$rc_cod_local','$rc_nom_local','$rc_perc_local','$rc_val_local',
                      '$rc_cod_cc','$rc_nom_cc','$rc_perc_cc','$rc_val_cc',
                      '$rc_cod_conta','$rc_nom_conta','$rc_perc_conta','$rc_val_conta',
                      '$data_sistema','$usuario_esc')");
        }
    }
}

echo json_encode(['success' => true, 'message' => 'Rateio atualizado com sucesso.']);
mysqli_close($conector);
