<?php
class PastoDao{

    private $con;
    private $systemDateHour;

    /** $con: conexão já aberta para reaproveitar (ex: várias DAOs na mesma
     *  transação); sem ela, abre uma nova como sempre. */
    public function __construct($banco, $con = null){
        if ($con) {
            $this->con = $con;
        } else {
            require __DIR__ . "/../../conecta_mysql_credenciais.inc";
            $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        }
        $this->systemDateHour = date('Y-m-d H:i:s');
    }

    public function getConexao(){
        return $this->con;
    }

    private function fillField($pasto){
        $obj = new Pasto();
        $obj->setId($pasto->tbl_pasto_id);

        $obj->getModulo()->setId($pasto->tbl_modulo_id);
        $obj->getModulo()->setDescricao($pasto->tbl_modulo_descricao);
        $obj->getModulo()->setIncluidoEm($pasto->tbl_modulo_incluido_em);
        $obj->getModulo()->setIncluidoPor($pasto->tbl_modulo_incluido_por);
        $obj->getModulo()->setAlteradoEm($pasto->tbl_modulo_alterado_em);
        $obj->getModulo()->setAlteradoPor($pasto->tbl_modulo_alterado_por);
        $obj->getModulo()->setLixeira($pasto->tbl_modulo_lixeira);
        $obj->getModulo()->setLixeiraEm($pasto->tbl_modulo_lixeira_em);
        $obj->getModulo()->setLixeiraPor($pasto->tbl_modulo_lixeira_por);

        $obj->getLocal()->setId($pasto->tbl_pessoa_id);
        $obj->getLocal()->setClasse($pasto->tbl_pessoa_classe);
        $obj->getLocal()->setCpfCnpj($pasto->tbl_pessoa_cpf_cnpj);
        $obj->getLocal()->setTipo($pasto->tbl_pessoa_tipo_pessoa);
        $obj->getLocal()->setInscEstadual($pasto->tbl_pessoa_insc_estadual);
        $obj->getLocal()->setInscMunicipal($pasto->tbl_pessoa_insc_municipal);
        $obj->getLocal()->setNome($pasto->tbl_pessoa_nome);
        $obj->getLocal()->setContato($pasto->tbl_pessoa_contato);
        $obj->getLocal()->setCargoContato($pasto->tbl_pessoa_cargo_contato);
        $obj->getLocal()->setDdd($pasto->tbl_pessoa_ddd);
        $obj->getLocal()->setTelefone($pasto->tbl_pessoa_telefone);
        $obj->getLocal()->setEmail($pasto->tbl_pessoa_email);
        $obj->getLocal()->getEndereco()->setCep($pasto->tbl_pessoa_cep);
        $obj->getLocal()->getEndereco()->setEndereco($pasto->tbl_pessoa_endereco);
        $obj->getLocal()->getEndereco()->setNumero($pasto->tbl_pessoa_numero);
        $obj->getLocal()->getEndereco()->setComplemento($pasto->tbl_pessoa_complemento);
        $obj->getLocal()->getEndereco()->setBairro($pasto->tbl_pessoa_bairro);
        $obj->getLocal()->getEndereco()->setCidade($pasto->tbl_pessoa_municipio);
        $obj->getLocal()->getEndereco()->setEstado($pasto->tbl_pessoa_estado);
        $obj->getLocal()->setIncluidoEm($pasto->tbl_pessoa_incluido_em);
        $obj->getLocal()->setIncluidoPor($pasto->tbl_pessoa_incluido_por);
        $obj->getLocal()->setAlteradoEm($pasto->tbl_pessoa_alterado_em);
        $obj->getLocal()->setAlteradoPor($pasto->tbl_pessoa_alterado_por);
        $obj->getLocal()->setLixeira($pasto->tbl_pessoa_lixeira);
        $obj->getLocal()->setLixeiraEm($pasto->tbl_pessoa_lixeira_em);
        $obj->getLocal()->setLixeiraPor($pasto->tbl_pessoa_lixeira_por);
        $obj->getLocal()->setObservacao($pasto->tbl_pessoa_observacao);
        $obj->getLocal()->setAtivo($pasto->tbl_pessoa_ativo);

        $obj->setDescricao($pasto->tbl_pasto_descricao);
        $obj->setLatitude($pasto->tbl_pasto_latitude);
        $obj->setLongitude($pasto->tbl_pasto_longitude);
        $obj->setArea($pasto->tbl_pasto_area);

        $obj->getCapim()->setId($pasto->tbl_tipo_capim_id);
        $obj->getCapim()->setDescricao($pasto->tbl_tipo_capim_descricao);
        $obj->getCapim()->setIncluidoEm($pasto->tbl_tipo_capim_incluido_em);
        $obj->getCapim()->setIncluidoPor($pasto->tbl_tipo_capim_incluido_por);
        $obj->getCapim()->setAlteradoEm($pasto->tbl_tipo_capim_alterado_em);
        $obj->getCapim()->setAlteradoPor($pasto->tbl_tipo_capim_incluido_por);
        $obj->getCapim()->setLixeira($pasto->tbl_tipo_capim_lixeira);
        $obj->getCapim()->setLixeiraEm($pasto->tbl_tipo_capim_lixeira_em);
        $obj->getCapim()->setLixeiraPor($pasto->tbl_tipo_capim_lixeira_por);
        
        $obj->setArrayCategoria($pasto->tbl_pasto_array_categoria);
        $obj->setObservacao($pasto->tbl_pasto_descricao_lote);
        $obj->setCurral($pasto->tbl_pasto_tipo_curral);
        $obj->setIncluidoEm($pasto->tbl_pasto_incluido_em);
        $obj->setIncluidoPor($pasto->tbl_pasto_incluido_por);
        $obj->setAlteradoEm($pasto->tbl_pasto_alterado_em);
        $obj->setAlteradoPor($pasto->tbl_pasto_alterado_por);
        $obj->setLixeira($pasto->tbl_pasto_lixeira);
        $obj->setLixeiraEm($pasto->tbl_pasto_lixeira_em);
        $obj->setLixeiraPor($pasto->tbl_pasto_lixeira_por);

        return $obj;
    }

    public function fillFields($objPasto){
        $aObj = [];

        foreach($objPasto as $pasto){
            $obj = new Pasto();
            $obj->setId($pasto->tbl_pasto_id);

            $obj->getModulo()->setId($pasto->tbl_modulo_id);
            $obj->getModulo()->setDescricao($pasto->tbl_modulo_descricao);
            $obj->getModulo()->setIncluidoEm($pasto->tbl_modulo_incluido_em);
            $obj->getModulo()->setIncluidoPor($pasto->tbl_modulo_incluido_por);
            $obj->getModulo()->setAlteradoEm($pasto->tbl_modulo_alterado_em);
            $obj->getModulo()->setAlteradoPor($pasto->tbl_modulo_alterado_por);
            $obj->getModulo()->setLixeira($pasto->tbl_modulo_lixeira);
            $obj->getModulo()->setLixeiraEm($pasto->tbl_modulo_lixeira_em);
            $obj->getModulo()->setLixeiraPor($pasto->tbl_modulo_lixeira_por);

            $obj->getLocal()->setId($pasto->tbl_pessoa_id);
            $obj->getLocal()->setClasse($pasto->tbl_pessoa_classe);
            $obj->getLocal()->setCpfCnpj($pasto->tbl_pessoa_cpf_cnpj);
            $obj->getLocal()->setTipo($pasto->tbl_pessoa_tipo_pessoa);
            $obj->getLocal()->setInscEstadual($pasto->tbl_pessoa_insc_estadual);
            $obj->getLocal()->setInscMunicipal($pasto->tbl_pessoa_insc_municipal);
            $obj->getLocal()->setNome($pasto->tbl_pessoa_nome);
            $obj->getLocal()->setContato($pasto->tbl_pessoa_contato);
            $obj->getLocal()->setCargoContato($pasto->tbl_pessoa_cargo_contato);
            $obj->getLocal()->setDdd($pasto->tbl_pessoa_ddd);
            $obj->getLocal()->setTelefone($pasto->tbl_pessoa_telefone);
            $obj->getLocal()->setEmail($pasto->tbl_pessoa_email);
            $obj->getLocal()->getEndereco()->setCep($pasto->tbl_pessoa_cep);
            $obj->getLocal()->getEndereco()->setEndereco($pasto->tbl_pessoa_endereco);
            $obj->getLocal()->getEndereco()->setNumero($pasto->tbl_pessoa_numero);
            $obj->getLocal()->getEndereco()->setComplemento($pasto->tbl_pessoa_complemento);
            $obj->getLocal()->getEndereco()->setBairro($pasto->tbl_pessoa_bairro);
            $obj->getLocal()->getEndereco()->setCidade($pasto->tbl_pessoa_municipio);
            $obj->getLocal()->getEndereco()->setEstado($pasto->tbl_pessoa_estado);
            $obj->getLocal()->setIncluidoEm($pasto->tbl_pessoa_incluido_em);
            $obj->getLocal()->setIncluidoPor($pasto->tbl_pessoa_incluido_por);
            $obj->getLocal()->setAlteradoEm($pasto->tbl_pessoa_alterado_em);
            $obj->getLocal()->setAlteradoPor($pasto->tbl_pessoa_alterado_por);
            $obj->getLocal()->setLixeira($pasto->tbl_pessoa_lixeira);
            $obj->getLocal()->setLixeiraEm($pasto->tbl_pessoa_lixeira_em);
            $obj->getLocal()->setLixeiraPor($pasto->tbl_pessoa_lixeira_por);
            $obj->getLocal()->setObservacao($pasto->tbl_pessoa_observacao);
            $obj->getLocal()->setAtivo($pasto->tbl_pessoa_ativo);

            $obj->setDescricao($pasto->tbl_pasto_descricao);
            $obj->setLatitude($pasto->tbl_pasto_latitude);
            $obj->setLongitude($pasto->tbl_pasto_longitude);
            $obj->setArea($pasto->tbl_pasto_area);

            $obj->getCapim()->setId($pasto->tbl_tipo_capim_id);
            $obj->getCapim()->setDescricao($pasto->tbl_tipo_capim_descricao);
            $obj->getCapim()->setIncluidoEm($pasto->tbl_tipo_capim_incluido_em);
            $obj->getCapim()->setIncluidoPor($pasto->tbl_tipo_capim_incluido_por);
            $obj->getCapim()->setAlteradoEm($pasto->tbl_tipo_capim_alterado_em);
            $obj->getCapim()->setAlteradoPor($pasto->tbl_tipo_capim_incluido_por);
            $obj->getCapim()->setLixeira($pasto->tbl_tipo_capim_lixeira);
            $obj->getCapim()->setLixeiraEm($pasto->tbl_tipo_capim_lixeira_em);
            $obj->getCapim()->setLixeiraPor($pasto->tbl_tipo_capim_lixeira_por);
            
            $obj->setArrayCategoria($pasto->tbl_pasto_array_categoria);
            $obj->setObservacao($pasto->tbl_pasto_descricao_lote);
            $obj->setCurral($pasto->tbl_pasto_tipo_curral);
            $obj->setIncluidoEm($pasto->tbl_pasto_incluido_em);
            $obj->setIncluidoPor($pasto->tbl_pasto_incluido_por);
            $obj->setAlteradoEm($pasto->tbl_pasto_alterado_em);
            $obj->setAlteradoPor($pasto->tbl_pasto_alterado_por);
            $obj->setLixeira($pasto->tbl_pasto_lixeira);
            $obj->setLixeiraEm($pasto->tbl_pasto_lixeira_em);
            $obj->setLixeiraPor($pasto->tbl_pasto_lixeira_por);

            array_push($aObj, $obj);
        }

        return $aObj;
    }

    public function getPastoById($id){
        $sql = "SELECT * 
        FROM tbl_pasto
        JOIN tbl_modulo_pasto ON tbl_pasto_modulo = tbl_modulo_id
        LEFT OUTER JOIN tbl_tipo_capim ON tbl_pasto_tipo_capim = tbl_tipo_capim_id
        JOIN tbl_pessoa ON tbl_pasto_codigo_local = tbl_pessoa_id
        WHERE tbl_pasto_id = '$id' AND tbl_pasto_lixeira = 0";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);

        return $this->fillField(mysqli_fetch_object($r));
    }

    public function getPasto($local){
        $a = [];

        $sql = "SELECT *
                FROM tbl_pasto
                JOIN tbl_modulo_pasto ON tbl_pasto_modulo = tbl_modulo_id
                LEFT OUTER JOIN tbl_tipo_capim ON tbl_pasto_tipo_capim = tbl_tipo_capim_id
                JOIN tbl_pessoa ON tbl_pasto_codigo_local = tbl_pessoa_id
                WHERE tbl_pasto_codigo_local = '$local'
                ORDER BY tbl_pasto_tipo_curral DESC";
        
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        while($obj = mysqli_fetch_object($r)){
            array_push($a, $obj);
        }
        
        return $this->fillFields($a);
    }

    // ---------------------------------------------------------------------
    // Mapa de Gado (Tabuleiro) — usado pelo aplicativo (MapaGadoService).
    // Campos sempre especificados e valores sempre tratados antes do SQL.
    // ---------------------------------------------------------------------

    /** Colunas de descrição do lote que existem nesta conta (bancos antigos
     *  só têm tbl_pasto_descricao_lote). */
    public function colunasDescricaoLote(){
        $colunas = [];
        $r = mysqli_query($this->con, "SHOW COLUMNS FROM tbl_pasto LIKE 'tbl_pasto_descricao_lote%'");
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $colunas[] = $row['Field'];
            }
        }
        return $colunas;
    }

    /** Pastos de ENTRADA/SAÍDA (módulo 999) da fazenda — mesmo SELECT e
     *  ORDER BY de ler_mapa_gados.php (a ordem dos cards depende dele). */
    public function listarPastosEntradaSaida($local, $colunasLote = []){
        $local = (int) $local;
        return $this->listarPastosTabuleiro("p.tbl_pasto_lixeira = 0 AND
            p.tbl_pasto_modulo = '999' AND
            p.tbl_pasto_codigo_local = '{$local}'", $colunasLote);
    }

    /** Demais pastos da fazenda, sem os módulos 999 (ENTRADA/SAÍDA), 1006
     *  (NÃO UTILIZADO) e 1007 (ÁREA COMUM) — igual a ler_mapa_gados.php. */
    public function listarPastosModulos($local, $colunasLote = []){
        $local = (int) $local;
        return $this->listarPastosTabuleiro("p.tbl_pasto_lixeira = 0 AND
            p.tbl_pasto_codigo_local = {$local} AND
            p.tbl_pasto_modulo != '999' AND
            p.tbl_pasto_modulo != '1006' AND
            p.tbl_pasto_modulo != '1007'", $colunasLote);
    }

    /** Pastos dos módulos 1006 (NÃO UTILIZADO) e 1007 (ÁREA COMUM): ficam
     *  fora do Tabuleiro, mas aparecem no Mapa Satélite (o web lista todos
     *  os pastos da fazenda no satélite). */
    public function listarPastosForaTabuleiro($local, $colunasLote = []){
        $local = (int) $local;
        return $this->listarPastosTabuleiro("p.tbl_pasto_lixeira = 0 AND
            p.tbl_pasto_codigo_local = {$local} AND
            (p.tbl_pasto_modulo = '1006' OR p.tbl_pasto_modulo = '1007')", $colunasLote);
    }

    private function listarPastosTabuleiro($where, $colunasLote){
        $camposLote = '';
        foreach ($colunasLote as $coluna) {
            // nomes vindos de SHOW COLUMNS, nunca do cliente
            $camposLote .= ", p." . preg_replace('/[^a-z0-9_]/', '', $coluna);
        }

        $sql = "SELECT p.tbl_pasto_id, p.tbl_pasto_codigo_local, p.tbl_pasto_descricao,
                       p.tbl_pasto_modulo, p.tbl_pasto_array_categoria,
                       p.tbl_pasto_data_com_animais, p.tbl_pasto_data_sem_animais,
                       c.tbl_tipo_capim_descricao {$camposLote}
                  FROM tbl_pasto p
             LEFT JOIN tbl_tipo_capim c ON c.tbl_tipo_capim_id = p.tbl_pasto_tipo_capim
                 WHERE {$where}
              ORDER BY p.tbl_pasto_modulo, p.tbl_pasto_codigo_local ASC";

        $a = [];
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $a[] = $row;
            }
        }
        return $a;
    }

    /** Pasto com os campos usados ao mover animais / gravar descrição do
     *  lote, travado até o fim da transação (FOR UPDATE). */
    public function buscarPastoParaMovimentacao($id){
        $id = (int) $id;
        $sql = "SELECT tbl_pasto_id, tbl_pasto_codigo_local,
                       tbl_pasto_descricao_lote, tbl_pasto_id_lote, tbl_pasto_ano_lote,
                       tbl_pasto_descricao_lote_1, tbl_pasto_descricao_lote_2,
                       tbl_pasto_descricao_lote_3, tbl_pasto_descricao_lote_4,
                       tbl_pasto_descricao_lote_5, tbl_pasto_descricao_lote_6,
                       tbl_pasto_data_com_animais, tbl_pasto_data_com_animais_anterior,
                       tbl_pasto_data_sem_animais, tbl_pasto_data_sem_animais_anterior,
                       tbl_pasto_alterado_em, tbl_pasto_alterado_por
                  FROM tbl_pasto
                 WHERE tbl_pasto_id = {$id} AND tbl_pasto_lixeira = 0
                   FOR UPDATE";
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        return $r ? mysqli_fetch_assoc($r) : null;
    }

    /** UPDATE dos campos informados (nome da coluna => valor; null grava
     *  NULL) + alterado em/por. Os nomes das colunas vêm só do service. */
    public function atualizarCamposPasto($id, $campos, $usuario, $dataHora){
        $id = (int) $id;
        $sets = [];
        foreach ($campos as $coluna => $valor) {
            $coluna = preg_replace('/[^a-z0-9_]/', '', $coluna);
            $sets[] = $valor === null
                ? "{$coluna} = NULL"
                : "{$coluna} = '" . mysqli_real_escape_string($this->con, (string) $valor) . "'";
        }
        $sets[] = "tbl_pasto_alterado_em = '" . mysqli_real_escape_string($this->con, $dataHora) . "'";
        $sets[] = "tbl_pasto_alterado_por = '" . mysqli_real_escape_string($this->con, $usuario) . "'";

        mysqli_set_charset($this->con, "utf8");
        $ok = mysqli_query($this->con, "UPDATE tbl_pasto SET " . implode(",\n", $sets) . " WHERE tbl_pasto_id = {$id}");
        return $ok ? ["error" => false, "message" => ""]
                   : ["error" => true, "message" => mysqli_error($this->con)];
    }
}