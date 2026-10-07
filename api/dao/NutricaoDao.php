<?php
class NutricaoDao{

    private $con;

    /** $con: conexão já aberta para reaproveitar (ex: várias DAOs na mesma
     *  transação); sem ela, abre uma nova. */
    public function __construct($banco, $con = null){
        if ($con) {
            $this->con = $con;
        } else {
            require __DIR__ . "/../../conecta_mysql_credenciais.inc";
            $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        }
    }

    /** Nutrição lançada no pasto de origem no dia da transferência passa
     *  para o pasto destino (Mapa de Gado — mover todos os animais). */
    public function transferirNutricaoDoDia($origem, $destino, $data){
        $origem = (int) $origem;
        $destino = (int) $destino;
        $data = mysqli_real_escape_string($this->con, $data);

        $ok = mysqli_query($this->con, "UPDATE tbl_nutricao SET tbl_nutricao_codigo_pasto = {$destino}
            WHERE tbl_nutricao_codigo_pasto = {$origem} AND tbl_nutricao_data = '{$data}'");

        return $ok ? ["error" => false, "message" => ""]
                   : ["error" => true, "message" => "Ocorreu um erro ao transferir a nutrição!"];
    }
    // ---------------------------------------------------------------------
    // Botão Nutrição da tela do pasto (aplicativo) — as mesmas consultas e
    // gravações de gravar_nutricao.php / ler_itens_nutricao.php do web.
    // ---------------------------------------------------------------------

    private function esc($valor){
        return mysqli_real_escape_string($this->con, (string) $valor);
    }

    private function resultado($ok){
        return $ok ? ["error" => false, "message" => ""]
                   : ["error" => true, "message" => mysqli_error($this->con)];
    }

    private function linha($sql){
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        return $r ? mysqli_fetch_assoc($r) : null;
    }

    /** Opções de "Situação do Cocho" (ler_score_cocho.php). */
    public function listarScoresCocho(){
        $a = [];
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, "SELECT tbl_score_id, tbl_score_descricao FROM tbl_score_cocho");
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $a[] = ["id" => (int) $row['tbl_score_id'], "descricao" => (string) $row['tbl_score_descricao']];
            }
        }
        return $a;
    }

    /** Opções de "Produto" (ler_produto_nutricao.php) com a unidade
     *  (ler_unidade_produto.php). */
    public function listarProdutos(){
        $a = [];
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, "SELECT tbl_produto_codigo_id, tbl_produto_descricao,
                tab_codigo_unidade_produtos
            FROM tbl_produto
            LEFT JOIN tabela_unidade_produtos ON tbl_produto_unidade = tab_codigo_unidade_id
            WHERE tbl_produto_lixeira = 0");
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $a[] = [
                    "id" => (int) $row['tbl_produto_codigo_id'],
                    "descricao" => (string) $row['tbl_produto_descricao'],
                    "unidade" => (string) ($row['tab_codigo_unidade_produtos'] ?? ''),
                ];
            }
        }
        return $a;
    }

    /** Itens da tabela do modal (ler_itens_nutricao.php). Filtro: fazendas
     *  + data inicial (lista para o cache do aplicativo) ou pasto + data. */
    public function listarItens($fazendas, $desde = null, $pasto = null, $data = null){
        $ids = array_values(array_filter(array_map('intval', (array) $fazendas), function ($id) {
            return $id > 0;
        }));
        if (count($ids) === 0) {
            return [];
        }
        $filtro = "";
        if ($desde !== null) {
            $filtro .= " AND tbl_nutricao_data >= '" . $this->esc($desde) . "'";
        }
        if ($pasto !== null) {
            $filtro .= " AND tbl_nutricao_codigo_pasto = " . (int) $pasto;
        }
        if ($data !== null) {
            $filtro .= " AND tbl_nutricao_data = '" . $this->esc($data) . "'";
        }

        $a = [];
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, "SELECT tbl_nutricao_id, tbl_nutricao_data,
                tbl_nutricao_codigo_local, tbl_nutricao_codigo_pasto, tbl_nutricao_codigo_produto,
                tbl_nutricao_qtd_animais, tbl_nutricao_quantidade_produto, tbl_nutricao_media_cabeca,
                tbl_produto_descricao, tab_codigo_unidade_produtos
            FROM tbl_nutricao
            JOIN tbl_produto ON tbl_nutricao_codigo_produto = tbl_produto_codigo_id
            JOIN tabela_unidade_produtos ON tbl_produto_unidade = tab_codigo_unidade_id
            WHERE tbl_nutricao_codigo_local IN (" . implode(',', $ids) . ") AND
                  tbl_nutricao_lixeira = 0{$filtro}
            ORDER BY tbl_nutricao_id");
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $a[] = [
                    "id" => (int) $row['tbl_nutricao_id'],
                    "data" => (string) $row['tbl_nutricao_data'],
                    "local" => (int) $row['tbl_nutricao_codigo_local'],
                    "pasto" => (int) $row['tbl_nutricao_codigo_pasto'],
                    "produto" => (int) $row['tbl_nutricao_codigo_produto'],
                    "produto_descricao" => (string) $row['tbl_produto_descricao'],
                    "unidade" => (string) $row['tab_codigo_unidade_produtos'],
                    "quantidade" => (float) $row['tbl_nutricao_quantidade_produto'],
                    "qtd_animais" => (int) $row['tbl_nutricao_qtd_animais'],
                    "media_cabeca" => (float) $row['tbl_nutricao_media_cabeca'],
                ];
            }
        }
        return $a;
    }

    /** Nutrição já gravada por essa mesma ação do aplicativo (usuário +
     *  data/hora da ação) — reenvio seguro. Devolve o id ou 0. */
    public function buscarDaAcao($local, $pasto, $produto, $usuario, $dataHora){
        $row = $this->linha("SELECT tbl_nutricao_id FROM tbl_nutricao
            WHERE tbl_nutricao_codigo_local = " . (int) $local . " AND
                  tbl_nutricao_codigo_pasto = " . (int) $pasto . " AND
                  tbl_nutricao_codigo_produto = " . (int) $produto . " AND
                  tbl_nutricao_incluido_em = '" . $this->esc($dataHora) . "' AND
                  tbl_nutricao_incluido_por = '" . $this->esc($usuario) . "'
            LIMIT 1");
        return $row ? (int) $row['tbl_nutricao_id'] : 0;
    }

    /** Nutrição mais recente do pasto ANTES da data (data e "encerrada"). */
    public function buscarAnterior($local, $pasto, $data){
        return $this->linha("SELECT tbl_nutricao_data, tbl_nutricao_encerrada FROM tbl_nutricao
            WHERE tbl_nutricao_codigo_local = " . (int) $local . " AND
                  tbl_nutricao_codigo_pasto = " . (int) $pasto . " AND
                  tbl_nutricao_data < '" . $this->esc($data) . "'
            ORDER BY tbl_nutricao_data DESC LIMIT 1");
    }

    /** Inclui a nutrição; devolve também "id". */
    public function incluir($n){
        mysqli_set_charset($this->con, "utf8");
        $ok = mysqli_query($this->con, "INSERT INTO tbl_nutricao (
                tbl_nutricao_data, tbl_nutricao_codigo_local, tbl_nutricao_codigo_pasto,
                tbl_nutricao_codigo_produto, tbl_nutricao_qtd_animais, tbl_nutricao_quantidade_produto,
                tbl_nutricao_media_cabeca, tbl_nutricao_codigo_score_cocho,
                tbl_nutricao_dias_consumo, tbl_nutricao_consumo_cabeca_dia,
                tbl_nutricao_id_lote, tbl_nutricao_ano_lote, tbl_nutricao_lote_pasto,
                tbl_nutricao_incluido_em, tbl_nutricao_incluido_por, tbl_nutricao_lixeira
            ) VALUES (
                '" . $this->esc($n['data']) . "', " . (int) $n['local'] . ", " . (int) $n['pasto'] . ",
                " . (int) $n['produto'] . ", '" . (int) $n['qtd_animais'] . "', '" . $this->esc($n['quantidade']) . "',
                NULL, '" . (int) $n['score_cocho'] . "',
                0, 0.00,
                '" . $this->esc($n['id_ano_lote']) . "', '" . $this->esc($n['ano_lote']) . "', '" . $this->esc($n['lote']) . "',
                '" . $this->esc($n['data_hora']) . "', '" . $this->esc($n['usuario']) . "', 0
            )");
        $r = $this->resultado($ok);
        $r["id"] = $ok ? (int) mysqli_insert_id($this->con) : 0;
        return $r;
    }

    /** Nutrições do pasto numa data (para atualizar o consumo da anterior). */
    public function listarDoPastoNaData($local, $pasto, $data){
        $a = [];
        $r = mysqli_query($this->con, "SELECT tbl_nutricao_id, tbl_nutricao_quantidade_produto,
                tbl_nutricao_qtd_animais
            FROM tbl_nutricao
            WHERE tbl_nutricao_codigo_local = " . (int) $local . " AND
                  tbl_nutricao_codigo_pasto = " . (int) $pasto . " AND
                  tbl_nutricao_data = '" . $this->esc($data) . "'");
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $a[] = $row;
            }
        }
        return $a;
    }

    public function atualizarConsumo($id, $scoreCocho, $dias, $consumo){
        return $this->resultado(mysqli_query($this->con, "UPDATE tbl_nutricao SET
                tbl_nutricao_codigo_score_cocho = '" . (int) $scoreCocho . "',
                tbl_nutricao_dias_consumo = '" . (int) $dias . "',
                tbl_nutricao_consumo_cabeca_dia = '" . $this->esc($consumo) . "'
            WHERE tbl_nutricao_id = " . (int) $id));
    }

    public function buscarPorId($id){
        return $this->linha("SELECT tbl_nutricao_id, tbl_nutricao_codigo_produto,
                tbl_nutricao_codigo_local, tbl_nutricao_quantidade_produto
            FROM tbl_nutricao WHERE tbl_nutricao_id = " . (int) $id . " FOR UPDATE");
    }

    public function excluir($id){
        return $this->resultado(mysqli_query($this->con, "DELETE FROM tbl_nutricao WHERE tbl_nutricao_id = " . (int) $id));
    }

    /**
     * Soma (ou subtrai, com valor negativo) a quantidade no estoque do
     * produto na fazenda. "encontrado" = false quando o produto não tem
     * estoque cadastrado nessa fazenda (nada é alterado).
     */
    public function movimentarEstoque($produto, $local, $quantidade, $usuario, $dataHora){
        $row = $this->linha("SELECT tbl_produto_estoque_atual FROM tbl_produto_estoque
            WHERE tbl_produto_estoque_codigo_id = " . (int) $produto . " AND
                  tbl_produto_estoque_codigo_local = " . (int) $local . " AND
                  tbl_produto_estoque_lixeira = 0
            LIMIT 1 FOR UPDATE");
        if (!$row) {
            return ["error" => false, "message" => "", "encontrado" => false];
        }
        $novo = ((float) $row['tbl_produto_estoque_atual']) + (float) $quantidade;
        $r = $this->resultado(mysqli_query($this->con, "UPDATE tbl_produto_estoque SET
                tbl_produto_estoque_atual = '{$novo}',
                tbl_produto_estoque_alterado_em = '" . $this->esc($dataHora) . "',
                tbl_produto_estoque_alterado_por = '" . $this->esc($usuario) . "'
            WHERE tbl_produto_estoque_codigo_id = " . (int) $produto . " AND
                  tbl_produto_estoque_codigo_local = " . (int) $local));
        $r["encontrado"] = true;
        return $r;
    }
}
