<?php
class PesagemDao {
    private $con;

    public function __construct($banco) {
        include_once __DIR__ . "/../../conecta_mysql_credenciais.inc";
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        mysqli_set_charset($this->con, "utf8");
    }

    // ADICIONE ESTE MÉTODO AQUI:
    public function getConexao() {
        return $this->con;
    }

    // Função para calcular idade em meses e buscar categoria
    private function buscarCategoriaPorNascimento($nascimento) {
        $data_obj = DateTime::createFromFormat('d/m/Y', $nascimento);
        if (!$data_obj) return 0;

        $data_nascimento = $data_obj->format('Y-m-d');
        $hoje = new DateTime(date("Y-m-d"));
        $diff = $data_obj->diff($hoje);
        
        $idadeMeses = ($diff->y * 12) + $diff->m;

        $sql = "SELECT tab_codigo_categoria_idade FROM tabela_categoria_idade 
                WHERE tab_registro_lixeira_categoria_idade = '0' 
                AND $idadeMeses >= tab_categoria_idade_de 
                AND $idadeMeses <= tab_categoria_idade_ate LIMIT 1";
        
        $res = mysqli_query($this->con, $sql);
        if ($row = mysqli_fetch_assoc($res)) {
            return $row['tab_codigo_categoria_idade'];
        }
        return 0;
    }

    public function salvarSomentePesagem($pesagem) {
        mysqli_begin_transaction($this->con);
        try {
            $sqlP = "INSERT INTO tbl_pesagem (
                tbl_pesagem_controle,
                tbl_pesagem_data,
                tbl_pesagem_codigo_local,
                tbl_pesagem_codigo_epoca,
                tbl_pesagem_lote,
                tbl_pesagem_filtros,
                tbl_pesagem_finalizada,
                tbl_pesagem_incluido_em,
                tbl_pesagem_incluido_por,
                tbl_pesagem_lixeira,
                tbl_pesagem_tipo_registro,
                tbl_pesagem_origem,
                tbl_pesagem_qtd_animais_a_pesar,
                tbl_pesagem_qtd_animais_pesados,
                tbl_pesagem_peso_kg,
                tbl_pesagem_peso_arroba,
                tbl_pesagem_peso_medio_kg,
                tbl_pesagem_peso_medio_arroba,
                tbl_pesagem_criterios_apartacao
            ) VALUES (
                'I',
                '{$pesagem->getData()}',
                '{$pesagem->getLocal()}',
                '{$pesagem->getEpoca()}',
                '{$pesagem->getLote()}',
                '{$pesagem->getFiltro()}',
                'N',
                NOW(),
                '{$pesagem->getIncluidoPor()}',
                0,
                'ONLINE',
                'APP',
                '{$pesagem->getQuantidadeAnimais()}',
                0,
                0,
                0,
                0,
                0,
                '{$pesagem->getCriteriosApartacao()}'
            )";

            if (!mysqli_query($this->con, $sqlP)) {
                throw new Exception("Erro ao criar pesagem");
            }

            $idPesagem = mysqli_insert_id($this->con);

            mysqli_commit($this->con);
            return $idPesagem;
        } catch (Exception $e) {
            mysqli_rollback($this->con);
            return false;
        }
    }

    public function updatePesagemCabecalho($dados)
    {
        try {
            $pesagemId = (int)($dados['pesagem_id'] ?? 0);
            $localId = trim((string)($dados['local_id'] ?? ''));
            $epocaId = trim((string)($dados['epoca_id'] ?? ''));
            $lote = trim((string)($dados['lote'] ?? ''));
            $qtdAPesar = (int)($dados['qtd_a_pesar'] ?? 0);

            $filtroDesc = trim((string)($dados['filtro_desc'] ?? ''));
            $filtroDesc = str_replace('➔', '->', $filtroDesc);

            $bloquearFazenda = !empty($dados['bloquear_fazenda']);

            $criteriosLista = '';
            if (isset($dados['criterios_lista']) && is_array($dados['criterios_lista'])) {
                $criteriosLimpos = array_filter(array_map('trim', $dados['criterios_lista']));
                $criteriosLista = implode(', ', $criteriosLimpos);
            }

            if ($pesagemId <= 0) {
                return [
                    "success" => false,
                    "message" => "ID da pesagem inválido."
                ];
            }

            if ($epocaId === '' || $lote === '') {
                return [
                    "success" => false,
                    "message" => "Dados obrigatórios não informados."
                ];
            }

            if ($bloquearFazenda) {
                $sql = "UPDATE tbl_pesagem
                           SET tbl_pesagem_codigo_epoca = ?,
                               tbl_pesagem_lote = ?,
                               tbl_pesagem_qtd_animais_a_pesar = ?,
                               tbl_pesagem_filtros = ?,
                               tbl_pesagem_criterios_apartacao = ?
                         WHERE tbl_pesagem_id = ?";
            } else {
                $sql = "UPDATE tbl_pesagem
                           SET tbl_pesagem_codigo_local = ?,
                               tbl_pesagem_codigo_epoca = ?,
                               tbl_pesagem_lote = ?,
                               tbl_pesagem_qtd_animais_a_pesar = ?,
                               tbl_pesagem_filtros = ?,
                               tbl_pesagem_criterios_apartacao = ?
                         WHERE tbl_pesagem_id = ?";
            }

            $stmt = mysqli_prepare($this->con, $sql);

            if (!$stmt) {
                return [
                    "success" => false,
                    "message" => "Erro no prepare: " . mysqli_error($this->con)
                ];
            }

            if ($bloquearFazenda) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "ssissi",
                    $epocaId,
                    $lote,
                    $qtdAPesar,
                    $filtroDesc,
                    $criteriosLista,
                    $pesagemId
                );
            } else {
                mysqli_stmt_bind_param(
                    $stmt,
                    "sssissi",
                    $localId,
                    $epocaId,
                    $lote,
                    $qtdAPesar,
                    $filtroDesc,
                    $criteriosLista,
                    $pesagemId
                );
            }

            if (!mysqli_stmt_execute($stmt)) {
                $erro = mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);

                return [
                    "success" => false,
                    "message" => "Erro ao executar update: " . $erro
                ];
            }

            $linhasAfetadas = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);

            return [
                "success" => true,
                "message" => "Pesagem atualizada com sucesso.",
                "rows" => $linhasAfetadas
            ];
        } catch (Throwable $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function salvarPesagemEItem($pesagem, $item) {
        mysqli_begin_transaction($this->con);
        try {
            $idPesagem = (int)$pesagem->getId();

            if ($idPesagem == 0) {
                $sqlP = "INSERT INTO tbl_pesagem (
                    tbl_pesagem_controle,
                    tbl_pesagem_data,
                    tbl_pesagem_codigo_local, 
                    tbl_pesagem_codigo_epoca,
                    tbl_pesagem_lote,
                    tbl_pesagem_filtros,
                    tbl_pesagem_finalizada,
                    tbl_pesagem_incluido_em,
                    tbl_pesagem_incluido_por,
                    tbl_pesagem_lixeira,
                    tbl_pesagem_tipo_registro,
                    tbl_pesagem_origem,
                    tbl_pesagem_qtd_animais_a_pesar,
                    tbl_pesagem_criterios_apartacao
                ) VALUES (
                    'I',
                    '{$pesagem->getData()}',
                    '{$pesagem->getLocal()}',
                    '{$pesagem->getEpoca()}',
                    '{$pesagem->getLote()}',
                    '{$pesagem->getFiltro()}',
                    'N',
                    NOW(),
                    '{$pesagem->getIncluidoPor()}',
                    0,
                    'ONLINE',
                    'APP',
                    '{$pesagem->getQuantidadeAnimais()}',
                    '{$pesagem->getCriteriosApartacao()}'
                )";

                if (!mysqli_query($this->con, $sqlP)) {
                    throw new Exception("Erro ao inserir cabeçalho da pesagem: " . mysqli_error($this->con));
                }

                $idPesagem = mysqli_insert_id($this->con);
            } else {
                if (!$this->pesagemPermiteAcessoApp($idPesagem)) {
                    throw new Exception("Pesagem não encontrada ou não pertence ao aplicativo.");
                }

                $sqlUpP = "UPDATE tbl_pesagem SET
                           tbl_pesagem_criterios_apartacao = '{$pesagem->getCriteriosApartacao()}'
                           WHERE tbl_pesagem_id = $idPesagem";

                if (!mysqli_query($this->con, $sqlUpP)) {
                    throw new Exception("Erro ao atualizar cabeçalho da pesagem: " . mysqli_error($this->con));
                }
            }

            $categoriaId = $this->buscarCategoriaPorNascimento($item->getNascimento());

            $sqlMax = "SELECT COALESCE(MAX(tbl_ite_pesagem_numero_item), 0) + 1 as prox
                       FROM tbl_item_pesagem
                       WHERE tbl_ite_pesagem_numero_id = $idPesagem";

            $resMax = mysqli_query($this->con, $sqlMax);
            if (!$resMax) {
                throw new Exception("Erro ao buscar próximo número do item: " . mysqli_error($this->con));
            }

            $rowMax = mysqli_fetch_assoc($resMax);
            $proxItem = (int)($rowMax['prox'] ?? 1);

            $sqlI = "INSERT INTO tbl_item_pesagem (
                tbl_ite_pesagem_numero_id,
                tbl_ite_pesagem_numero_item,
                tbl_ite_pesagem_codigo_id_animal,
                tbl_ite_pesagem_codigo_animal,
                tbl_ite_pesagem_data_emissao,
                tbl_ite_pesagem_categoria,
                tbl_ite_pesagem_qtd_animais,
                tbl_ite_pesagem_sexo,
                tbl_ite_pesagem_peso,
                tbl_ite_pesagem_arroba,
                tbl_ite_pesagem_peso_medio,
                tbl_ite_pesagem_arroba_media,
                tbl_ite_pesagem_nascimento,
                tbl_ite_pesagem_raca,
                tbl_ite_pesagem_pelagem,
                tbl_ite_pesagem_mae,
                tbl_ite_pesagem_observacao,
                tbl_ite_pesagem_mens_repetido,
                tbl_ite_pesagem_id_repetido,
                tbl_ite_pesagem_criterio_apartacao,
                tbl_ite_pesagem_ultimo_peso
            ) VALUES (
                $idPesagem,
                $proxItem,
                '{$item->getIdAnimal()}',
                '{$item->getAnimal()}',
                '{$pesagem->getData()}',
                $categoriaId,
                1,
                '{$item->getSexo()}',
                '{$item->getPeso()}',
                '{$item->getArroba()}',
                '{$item->getPesoMedio()}',
                '{$item->getArrobaMedio()}',
                '{$item->getNascimento()}',
                '{$item->getRaca()}',
                '{$item->getPelagem()}',
                '{$item->getMae()}',
                '{$item->getObservacao()}',
                '{$item->getMensItemRepetido()}',
                '{$item->getIdPesagemItemRepetido()}',
                '{$item->getCriterioApartacao()}',
                '{$item->getUltimoPeso()}'
            )";

        if (!mysqli_query($this->con, $sqlI)) {
            throw new Exception("Erro ao inserir item da pesagem: " . mysqli_error($this->con));
        }

        $this->recalcularTotais($idPesagem);

        mysqli_commit($this->con);

        // recalcula os repetidos já fora da transação principal
        $this->recalcularItensRepetidosPorAnimal($item->getIdAnimal());

        return $idPesagem;

        } catch (Exception $e) {
            mysqli_rollback($this->con);
            error_log("salvarPesagemEItem: " . $e->getMessage());
            return false;
        }
    }

    public function excluirItem($pesagemId, $numeroItem) {
        mysqli_begin_transaction($this->con);

        try {
            $pesagemId = (int)$pesagemId;
            $numeroItem = (int)$numeroItem;

            $sqlBuscaAnimal = "
                SELECT tbl_ite_pesagem_codigo_id_animal
                FROM tbl_item_pesagem
                WHERE tbl_ite_pesagem_numero_id = $pesagemId
                  AND tbl_ite_pesagem_numero_item = $numeroItem
                LIMIT 1
            ";

            $resBuscaAnimal = mysqli_query($this->con, $sqlBuscaAnimal);
            if (!$resBuscaAnimal) {
                throw new Exception("Erro ao buscar animal do item: " . mysqli_error($this->con));
            }

            $rowAnimal = mysqli_fetch_assoc($resBuscaAnimal);
            if (!$rowAnimal) {
                throw new Exception("Item não encontrado para exclusão.");
            }

            $idAnimal = trim((string)$rowAnimal['tbl_ite_pesagem_codigo_id_animal']);

            $sql = "DELETE FROM tbl_item_pesagem
                    WHERE tbl_ite_pesagem_numero_id = $pesagemId
                      AND tbl_ite_pesagem_numero_item = $numeroItem";

            if (!mysqli_query($this->con, $sql)) {
                throw new Exception("Erro ao excluir item: " . mysqli_error($this->con));
            }

            $this->recalcularTotais($pesagemId);

            mysqli_commit($this->con);

            $this->recalcularItensRepetidosPorAnimal($idAnimal);

            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->con);
            error_log("excluirItem: " . $e->getMessage());
            return false;
        }
    }

    public function alterarItem($pesagemId, $numeroItem, $novoPeso, $novaObs, $novoCriterio) {
        mysqli_begin_transaction($this->con);
        try {
            $arroba = $novoPeso / 30;

            $sql = "UPDATE tbl_item_pesagem SET 
                    tbl_ite_pesagem_peso = '$novoPeso',
                    tbl_ite_pesagem_arroba = '$arroba',
                    tbl_ite_pesagem_peso_medio = '$novoPeso',
                    tbl_ite_pesagem_arroba_media = '$arroba',
                    tbl_ite_pesagem_observacao = '$novaObs',
                    tbl_ite_pesagem_criterio_apartacao = '$novoCriterio'
                    WHERE tbl_ite_pesagem_numero_id = $pesagemId 
                    AND tbl_ite_pesagem_numero_item = $numeroItem";
            
            if (!mysqli_query($this->con, $sql)) {
                throw new Exception("Erro ao alterar item");
            }

            $this->recalcularTotais($pesagemId);

            mysqli_commit($this->con);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->con);
            return false;
        }
    }
        
    private function recalcularTotais($id) {
        $id = (int)$id;

        $sqlSum = "SELECT 
                        COUNT(*) as qtd, 
                        COALESCE(SUM(tbl_ite_pesagem_peso), 0) as soma 
                   FROM tbl_item_pesagem 
                   WHERE tbl_ite_pesagem_numero_id = $id";

        $res = mysqli_query($this->con, $sqlSum);

        if (!$res) {
            throw new Exception("Erro ao recalcular totais: " . mysqli_error($this->con));
        }

        $row = mysqli_fetch_assoc($res);

        $qtd = (int)($row['qtd'] ?? 0);
        $pesoKg = (float)($row['soma'] ?? 0);

        $pesoArr = $pesoKg / 30;
        $medioKg = $qtd > 0 ? $pesoKg / $qtd : 0;
        $medioArr = $qtd > 0 ? $medioKg / 30 : 0;

        $sqlUp = "UPDATE tbl_pesagem SET 
            tbl_pesagem_qtd_animais_pesados = $qtd,
            tbl_pesagem_peso_kg = $pesoKg,
            tbl_pesagem_peso_arroba = $pesoArr,
            tbl_pesagem_peso_medio_kg = $medioKg,
            tbl_pesagem_peso_medio_arroba = $medioArr
            WHERE tbl_pesagem_id = $id";

        if (!mysqli_query($this->con, $sqlUp)) {
            throw new Exception("Erro ao atualizar totais da pesagem: " . mysqli_error($this->con));
        }
    }
    private function escapar($valor) {
        return mysqli_real_escape_string($this->con, (string)$valor);
    }

    // Retorna true se a pesagem pode ser acessada/alterada: já finalizada (regra atual,
    // sem checagem de origem) ou ainda aberta e de origem 'APP'.
    private function pesagemPermiteAcessoApp($idPesagem) {
        $idPesagem = (int)$idPesagem;

        $sql = "SELECT tbl_pesagem_finalizada, tbl_pesagem_origem
                FROM tbl_pesagem
                WHERE tbl_pesagem_id = $idPesagem
                LIMIT 1";

        $res = mysqli_query($this->con, $sql);
        if (!$res) {
            return false;
        }

        $row = mysqli_fetch_assoc($res);
        if (!$row) {
            return false;
        }

        if ($row['tbl_pesagem_finalizada'] === 'S') {
            return true;
        }

        return $row['tbl_pesagem_origem'] === 'APP';
    }

    public function recalcularItensRepetidosPorAnimal($idAnimal) {
        mysqli_begin_transaction($this->con);

        try {
            $idAnimal = trim((string)$idAnimal);

            if ($idAnimal === '') {
                throw new Exception("ID do animal não informado para recalcular repetidos.");
            }

            $idAnimalSql = $this->escapar($idAnimal);

            $sqlBusca = "
                SELECT
                    i.tbl_ite_pesagem_numero_id AS pesagem_id,
                    i.tbl_ite_pesagem_numero_item AS numero_item,
                    i.tbl_ite_pesagem_codigo_id_animal AS id_animal,
                    COALESCE(p.tbl_pesagem_lote, '') AS lote
                FROM tbl_item_pesagem i
                INNER JOIN tbl_pesagem p
                    ON p.tbl_pesagem_id = i.tbl_ite_pesagem_numero_id
                WHERE i.tbl_ite_pesagem_codigo_id_animal = '{$idAnimalSql}'
                  AND IFNULL(p.tbl_pesagem_lixeira, 0) = 0
                  AND IFNULL(p.tbl_pesagem_finalizada, 'N') = 'N'
                  AND p.tbl_pesagem_origem = 'APP'
                ORDER BY i.tbl_ite_pesagem_numero_id
            ";
            
            $res = mysqli_query($this->con, $sqlBusca);
            if (!$res) {
                throw new Exception("Erro ao buscar itens repetidos do animal: " . mysqli_error($this->con));
            }

            $itens = [];
            while ($row = mysqli_fetch_assoc($res)) {
                $itens[] = [
                    'pesagem_id' => (string)$row['pesagem_id'],
                    'numero_item' => (string)$row['numero_item'],
                    'id_animal'   => (string)$row['id_animal'],
                    'lote'        => trim((string)$row['lote']),
                ];
            }

            if (count($itens) === 0) {
                mysqli_commit($this->con);
                return true;
            }

            foreach ($itens as $itemBase) {
                $lotesOutros = [];
                $idsOutros = [];

                foreach ($itens as $itemOutro) {
                    $mesmoRegistro =
                        $itemOutro['pesagem_id'] === $itemBase['pesagem_id'] &&
                        $itemOutro['numero_item'] === $itemBase['numero_item'];

                    if ($mesmoRegistro) {
                        continue;
                    }

                    if ($itemOutro['lote'] !== '') {
                        $lotesOutros[] = $itemOutro['lote'];
                    }

                    $idsOutros[] = $itemOutro['pesagem_id'];
                }

                $lotesOutros = array_values(array_unique($lotesOutros));
                $idsOutros = array_values(array_unique($idsOutros));

                $mensagem = '';
                $idsTexto = '';

                if (count($idsOutros) > 0) {
                    $mensagem = 'Repetido em: ' . implode(', ', $lotesOutros);
                    $idsTexto = implode(',', $idsOutros);
                }

                $mensagemSql = $this->escapar($mensagem);
                $idsTextoSql = $this->escapar($idsTexto);
                $pesagemId = (int)$itemBase['pesagem_id'];
                $numeroItem = (int)$itemBase['numero_item'];

                $sqlUpdate = "
                    UPDATE tbl_item_pesagem
                       SET tbl_ite_pesagem_mens_repetido = '{$mensagemSql}',
                           tbl_ite_pesagem_id_repetido = '{$idsTextoSql}'
                     WHERE tbl_ite_pesagem_numero_id = {$pesagemId}
                       AND tbl_ite_pesagem_numero_item = {$numeroItem}
                ";

                if (!mysqli_query($this->con, $sqlUpdate)) {
                    throw new Exception("Erro ao atualizar repetidos do item {$pesagemId}/{$numeroItem}: " . mysqli_error($this->con));
                }
            }

            mysqli_commit($this->con);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->con);
            error_log("recalcularItensRepetidosPorAnimal: " . $e->getMessage());
            return false;
        }
    }

    public function adicionarObservacaoItemRepetido($pesagemId, $idAnimal, $mensagemRepetido, $idPesagemRepetido) {
        mysqli_begin_transaction($this->con);

        try {
            $pesagemId = (int)$pesagemId;
            $idAnimal = trim((string)$idAnimal);
            $mensagemRepetido = trim((string)$mensagemRepetido);
            $idPesagemRepetido = (int)$idPesagemRepetido;

            if ($pesagemId <= 0 || $idAnimal === '') {
                throw new Exception("Dados inválidos para atualizar item repetido.");
            }

            $sqlUpdate = "UPDATE tbl_item_pesagem
                          SET tbl_ite_pesagem_mens_repetido = ?,
                              tbl_ite_pesagem_id_repetido = ?
                          WHERE tbl_ite_pesagem_numero_id = ?
                            AND tbl_ite_pesagem_codigo_id_animal = ?";

            $stmtUpdate = mysqli_prepare($this->con, $sqlUpdate);
            if (!$stmtUpdate) {
                throw new Exception("Erro no prepare do update: " . mysqli_error($this->con));
            }

            mysqli_stmt_bind_param(
                $stmtUpdate,
                "siis",
                $mensagemRepetido,
                $idPesagemRepetido,
                $pesagemId,
                $idAnimal
            );

            if (!mysqli_stmt_execute($stmtUpdate)) {
                throw new Exception("Erro ao atualizar item repetido: " . mysqli_stmt_error($stmtUpdate));
            }

            mysqli_stmt_close($stmtUpdate);

            mysqli_commit($this->con);

            return [
                "success" => true,
                "message" => "Campos de repetido atualizados com sucesso.",
                "mens_repetido" => $mensagemRepetido,
                "id_pesagem_repetido" => $idPesagemRepetido
            ];
        } catch (Exception $e) {
            mysqli_rollback($this->con);

            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}