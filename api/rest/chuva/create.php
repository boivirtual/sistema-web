<?php
/**
 * Grava (ou sobrescreve, se já existir na mesma data/local) um volume de
 * chuva. Usado pelo aplicativo — tanto pelo lançamento feito com internet
 * na hora, quanto pelo reenvio automático de um lançamento salvo offline
 * (ver ChuvaSyncService no app).
 *
 * Entrada — JSON no corpo do POST (mesmo padrão dos endpoints de pesagem):
 *   bd                 -> nome do banco da conta            (obrigatório)
 *   data_chuva          -> data no formato YYYY-MM-DD         (obrigatório)
 *   codigo_local_chuva  -> tbl_pessoa_id da fazenda           (obrigatório)
 *   volume_chuva        -> volume em mm                       (obrigatório)
 *   user                -> usuário que registrou              (opcional)
 *
 * Também aceita corpo form-urlencoded (compatibilidade), mas o app deve
 * enviar JSON.
 */

require_once __DIR__ . "/../../dao/ChuvaDao.php";
require_once __DIR__ . '/../../entitie/Chuva.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Endereco.php';
require_once __DIR__ . "/../../service/ChuvaService.php";

header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    $dados = $_POST;
}

if (isset($dados["bd"]) && isset($dados["data_chuva"]) &&
    isset($dados["codigo_local_chuva"]) && isset($dados["volume_chuva"])) {
    $obj = new ChuvaService();
    $r = $obj->createChuva($dados);
    if (!$r["error"]) {
        http_response_code(201);
    }
    echo json_encode($r);
} else {
    echo json_encode([
        "error"   => true,
        "message" => "Parâmetros insuficientes: informe bd, data_chuva, codigo_local_chuva e volume_chuva."
    ]);
}
