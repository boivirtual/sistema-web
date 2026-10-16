<?php
/**
 * Consultas do NASCIMENTO (tela do pasto do Mapa de Gado), usadas por
 * MapaGadoNascimentoService — as mesmas de form_mapa_gados_movimentacao.php
 * do sistema web.
 */
class NascimentoDao{

    private $con;

    /** $con: conexão já aberta (a transação é do service); sem ela, abre
     *  uma nova. */
    public function __construct($banco, $con = null){
        if ($con) {
            $this->con = $con;
        } else {
            require __DIR__ . "/../../conecta_mysql_credenciais.inc";
            $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        }
        mysqli_set_charset($this->con, "utf8");
    }

    private function listar($sql, $montar){
        $a = [];
        $r = mysqli_query($this->con, $sql);
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $a[] = $montar($row);
            }
        }
        return $a;
    }

    /** Grupo "SEMEM" do select "Pai Nº": todo sêmen fora da lixeira. */
    public function listarSemens(){
        return $this->listar(
            "SELECT tbl_semem_codigo_id, tbl_semem_nome, tab_descricao_raca
               FROM tbl_semem
              INNER JOIN tabela_racas ON tab_codigo_raca = tbl_semem_codigo_raca
              WHERE tbl_semem_lixeira = 0",
            function ($row) {
                return [
                    "id"   => (string) $row['tbl_semem_codigo_id'],
                    "nome" => (string) $row['tbl_semem_nome'],
                    "raca" => (string) $row['tab_descricao_raca'],
                ];
            }
        );
    }

    /** Grupo "ANIMAIS" do select "Pai Nº": todo macho fora da lixeira (de
     *  qualquer fazenda, ativo ou não — igual ao web), pelo número. */
    public function listarMachos(){
        return $this->listar(
            "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa,
                    tbl_animal_codigo_numerico, tab_descricao_raca
               FROM tbl_animais
              INNER JOIN tabela_racas ON tab_codigo_raca = tbl_animal_codigo_raca
              WHERE tbl_animal_lixeira = 0
                AND tbl_animal_sexo = 'M'
              ORDER BY tbl_animal_codigo_numerico",
            function ($row) {
                return [
                    "id"       => (string) $row['tbl_animal_codigo_id'],
                    "alfa"     => (string) $row['tbl_animal_codigo_alfa'],
                    "numerico" => (string) $row['tbl_animal_codigo_numerico'],
                    "raca"     => (string) $row['tab_descricao_raca'],
                ];
            }
        );
    }

    /** Opções de "Raça". */
    public function listarRacas(){
        return $this->listar(
            "SELECT tab_codigo_raca, tab_descricao_raca
               FROM tabela_racas
              WHERE tab_registro_lixeira_raca = 0",
            function ($row) {
                return [
                    "id"        => (string) $row['tab_codigo_raca'],
                    "descricao" => (string) $row['tab_descricao_raca'],
                ];
            }
        );
    }

    /** Opções de "Pelagem". */
    public function listarPelagens(){
        return $this->listar(
            "SELECT tab_codigo_pelagem, tab_descricao_pelagem
               FROM tabela_pelagens
              WHERE tab_registro_lixeira_pelagem = 0",
            function ($row) {
                return [
                    "id"        => (string) $row['tab_codigo_pelagem'],
                    "descricao" => (string) $row['tab_descricao_pelagem'],
                ];
            }
        );
    }

    // ---------------------------------------------------------------------
    // Seleção da mãe (ler_parametro_nascimento.php,
    // ler_animal_femea_nascimento.php e
    // ler_animal_femea_ultimo_nascimento.php do web)
    // ---------------------------------------------------------------------

    private function esc($valor){
        return mysqli_real_escape_string($this->con, (string) $valor);
    }

    private function linha($sql){
        $r = mysqli_query($this->con, $sql);
        return $r ? mysqli_fetch_assoc($r) : null;
    }

    private function linhas($sql){
        $a = [];
        $r = mysqli_query($this->con, $sql);
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $a[] = $row;
            }
        }
        return $a;
    }

    /** Grupo de acesso do usuário (o web usa $_SESSION['grupo_usuario']). */
    public function grupoDoUsuario($idUsuario){
        require __DIR__ . "/../../conecta_mysql_credenciais.inc";
        $con = mysqli_connect($servidor, $usuario_bd, $senha_bd, 'acesso_boi_virtual');
        if (!$con) {
            return '';
        }
        $id = (int) $idUsuario;
        $r = mysqli_query($con, "SELECT grupo_usuario FROM usuario WHERE id_usuario = $id");
        $row = $r ? mysqli_fetch_assoc($r) : null;
        mysqli_close($con);
        return $row ? (string) $row['grupo_usuario'] : '';
    }

    /** Parâmetro de nascimento da fazenda (código alfa e próximo número). */
    public function parametroNascimento($local){
        $local = $this->esc($local);
        return $this->linha("SELECT tbl_par_estacao_monta_inicial, tbl_par_estacao_monta_final,
                tbl_par_codigo_local, tbl_par_codigo_alfa, tbl_par_codigo_numerico
            FROM tbl_parametro_nascimento
            WHERE tbl_par_codigo_local = '$local' AND tbl_par_lixeira = 0");
    }

    /** Coberturas (estação) da fazenda com diagnóstico positivo e ainda sem
     *  nascimento; $estacao filtra uma estação. */
    public function prenhesSemNascimento($local, $estacao = null){
        $local = $this->esc($local);
        $filtro = $estacao === null ? ''
            : " AND tbl_cobertura_codigo_estacao_monta = '" . $this->esc($estacao) . "'";
        return $this->linhas("SELECT tbl_cobertura_id, tbl_cobertura_codigo_estacao_monta,
                tbl_cobertura_protocoloiatf
            FROM tbl_cobertura
            INNER JOIN tbl_item_cobertura ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
            WHERE tbl_cobertura_lixeira = 0
              AND tbl_cobertura_codigo_local = '$local'
              AND tbl_cobertura_controle = 'C'
              AND tbl_ite_cobertura_resultado_diagnostico = 'P'
              AND (tbl_ite_cobertura_nascido IS NULL OR tbl_ite_cobertura_nascido = '')" . $filtro);
    }

    public function estacaoPorId($id){
        $id = $this->esc($id);
        return $this->linha("SELECT tbl_par_estacao_id, tbl_par_estacao_nome
            FROM tbl_parametro_estacao_monta WHERE tbl_par_estacao_id = '$id'");
    }

    /** Estação de monta da fazenda vigente na data. */
    public function estacaoVigente($local, $data){
        $local = $this->esc($local);
        $data = $this->esc($data);
        return $this->linha("SELECT tbl_par_estacao_id, tbl_par_estacao_nome
            FROM tbl_parametro_estacao_monta
            WHERE tbl_par_codigo_local = '$local'
              AND tbl_par_lixeira = 0
              AND tbl_par_estacao_monta_inicial <= '$data'
              AND tbl_par_estacao_monta_final >= '$data'");
    }

    /** Última estação de monta cadastrada para a fazenda. */
    public function ultimaEstacaoDaFazenda($local){
        $local = $this->esc($local);
        return $this->linha("SELECT tbl_par_estacao_id
            FROM tbl_parametro_estacao_monta
            WHERE tbl_par_codigo_local = '$local' AND tbl_par_lixeira = 0
            ORDER BY tbl_par_estacao_id DESC LIMIT 1");
    }

    public function dataDoProtocoloDaCobertura($coberturaId){
        $id = $this->esc($coberturaId);
        $row = $this->linha("SELECT tbl_protocolo_cobertura_data
            FROM tbl_protocolo_cobertura
            WHERE tbl_protocolo_cobertura_codigo_id = '$id'");
        return $row ? $row['tbl_protocolo_cobertura_data'] : null;
    }

    /** Descrições dos itens do protocolo IATF (fora da lixeira), em ordem. */
    public function descricoesDoProtocoloIatf($protocolo){
        $p = $this->esc($protocolo);
        $a = [];
        foreach ($this->linhas("SELECT tbl_ite_protocoloiatf_descricao
            FROM tbl_item_protocoloiatf
            WHERE tbl_ite_protocoloiatf_lixeira = 0
              AND tbl_ite_protocoloiatf_protocolo_id = '$p'
            ORDER BY tbl_ite_protocoloiatf_id ASC") as $row) {
            $a[] = (string) $row['tbl_ite_protocoloiatf_descricao'];
        }
        return $a;
    }

    /** Quantidade de itens do protocolo IATF (com os da lixeira, como o web). */
    public function contarItensDoProtocoloIatf($protocolo){
        $p = $this->esc($protocolo);
        $row = $this->linha("SELECT COUNT(*) AS qtd FROM tbl_item_protocoloiatf
            WHERE tbl_ite_protocoloiatf_protocolo_id = '$p'");
        return $row ? (int) $row['qtd'] : 0;
    }

    /** Animal ativo, fora da lixeira, pelo código (letras + número). */
    public function animalAtivoPorCodigo($alfa, $numerico){
        $alfa = $this->esc($alfa);
        $numerico = $this->esc($numerico);
        return $this->linha("SELECT tbl_animal_codigo_id, tbl_animal_codigo_raca,
                tbl_animal_codigo_fazenda
            FROM tbl_animais
            WHERE tbl_animal_codigo_alfa = '$alfa'
              AND tbl_animal_codigo_numerico = '$numerico'
              AND tbl_animal_ativo = 'S'
              AND tbl_animal_lixeira = 0");
    }

    public function descricaoDaRaca($raca){
        $raca = $this->esc($raca);
        $row = $this->linha("SELECT tab_descricao_raca FROM tabela_racas
            WHERE tab_codigo_raca = '$raca'");
        return $row ? (string) $row['tab_descricao_raca'] : '';
    }

    /** Última cobertura (estação 'C' ou monta 'M') da fêmea. */
    public function ultimaCoberturaDaFemea($animalId){
        $id = $this->esc($animalId);
        return $this->linha("SELECT tbl_ite_cobertura_numero_id, tbl_ite_cobertura_numero_item,
                tbl_ite_cobertura_nascido, tbl_ite_cobertura_aborto_natimorto,
                tbl_ite_cobertura_resultado_diagnostico, tbl_ite_cobertura_negativo_em,
                tbl_ite_cobertura_data_prenhes, tbl_ite_cobertura_codigo_touro_semen,
                tbl_ite_cobertura_dia_2, tbl_ite_cobertura_dia_3, tbl_ite_cobertura_dia_4,
                tbl_ite_cobertura_dia_5, tbl_ite_cobertura_dia_6,
                tbl_cobertura_controle, tbl_cobertura_codigo_estacao_monta,
                tbl_cobertura_protocoloiatf
            FROM tbl_item_cobertura
            INNER JOIN tbl_cobertura ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
            WHERE tbl_cobertura_lixeira = 0
              AND tbl_ite_cobertura_codigo_id_animal = '$id'
              AND (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M')
              AND (tbl_ite_cobertura_resultado_diagnostico = 'P'
                OR tbl_ite_cobertura_resultado_diagnostico = ''
                OR tbl_ite_cobertura_resultado_diagnostico = 'N'
                OR tbl_ite_cobertura_resultado_diagnostico IS NULL)
            ORDER BY tbl_cobertura_incluido_em DESC LIMIT 1");
    }

    /** Data do último aborto/natimorto lançado para a fêmea. */
    public function dataDoUltimoAbortoNatimorto($animalId){
        $id = $this->esc($animalId);
        return $this->linha("SELECT tbl_mov_estoque_nascimento
            FROM tbl_movimentacao_estoque
            WHERE tbl_mov_estoque_codigo_mae = '$id'
              AND (tbl_mov_estoque_entrada_saida = 'A'
                OR (tbl_mov_estoque_entrada_saida = 'S' AND tbl_mov_estoque_tipo_movimentacao = 'M'))
            ORDER BY tbl_mov_estoque_numero_id DESC LIMIT 1");
    }

    /** Raça do pai: procura primeiro no sêmen, depois nos animais. */
    public function racaDoPai($codigo){
        $c = $this->esc($codigo);
        $row = $this->linha("SELECT tbl_semem_codigo_raca AS raca FROM tbl_semem
            WHERE tbl_semem_codigo_id = '$c'");
        if (!$row) {
            $row = $this->linha("SELECT tbl_animal_codigo_raca AS raca FROM tbl_animais
                WHERE tbl_animal_codigo_id = '$c'");
        }
        return $row ? (string) $row['raca'] : '';
    }

    /** Filhos da fêmea nascidos na estação: [quantidade, nascimento do 1º]. */
    public function filhosNaEstacao($mae, $estacao){
        $mae = $this->esc($mae);
        $estacao = $this->esc($estacao);
        $rows = $this->linhas("SELECT tbl_animal_data_nascimento FROM tbl_animais
            WHERE tbl_animal_codigo_mae = '$mae'
              AND tbl_animal_estacao_monta_nascimento = '$estacao'");
        return [count($rows), $rows ? $rows[0]['tbl_animal_data_nascimento'] : null];
    }

    /** Nascimento do último filho cadastrado da fêmea (null se não tem). */
    public function nascimentoDoUltimoFilho($mae){
        $mae = $this->esc($mae);
        $row = $this->linha("SELECT tbl_animal_data_nascimento FROM tbl_animais
            WHERE tbl_animal_codigo_mae = '$mae'
            ORDER BY tbl_animal_codigo_id DESC LIMIT 1");
        return $row ? [$row['tbl_animal_data_nascimento']] : null;
    }

    /**
     * "Registros de Hoje" da tela do Nascimento: nascimentos, abortos,
     * absorções e natimortos da fazenda com a data (do nascimento ou da
     * ocorrência) igual a $data — mesma seleção da lista de nascimentos do
     * web (form_lista_nascimento.php, tipos N, A e B; natimorto = N com o
     * animal 999999999), em ordem de lançamento.
     */
    public function registrosDoDia($local, $data){
        $local = $this->esc($local);
        $data = $this->esc($data);
        return $this->linhas("SELECT m.tbl_mov_estoque_numero_id AS id,
                m.tbl_mov_estoque_codigo_id_animal AS animal,
                m.tbl_mov_estoque_nascimento AS data,
                m.tbl_mov_estoque_tipo_movimentacao AS tipo,
                m.tbl_mov_estoque_sexo AS sexo_mov,
                a.tbl_animal_codigo_alfa AS alfa,
                a.tbl_animal_codigo_numerico AS numerico,
                a.tbl_animal_sexo AS sexo_animal,
                COALESCE(ma.tbl_animal_codigo_alfa, mm.tbl_animal_codigo_alfa) AS mae_alfa,
                COALESCE(ma.tbl_animal_codigo_numerico, mm.tbl_animal_codigo_numerico) AS mae_numerico,
                p.tbl_pasto_descricao AS pasto
            FROM tbl_movimentacao_estoque m
            LEFT JOIN tbl_animais a ON a.tbl_animal_codigo_id = m.tbl_mov_estoque_codigo_id_animal
            LEFT JOIN tbl_animais ma ON ma.tbl_animal_codigo_id = a.tbl_animal_codigo_mae
            LEFT JOIN tbl_animais mm ON mm.tbl_animal_codigo_id = m.tbl_mov_estoque_codigo_mae
            LEFT JOIN tbl_pasto p ON p.tbl_pasto_id = m.tbl_mov_estoque_codigo_pasto
            WHERE m.tbl_mov_estoque_nascimento = '$data'
              AND m.tbl_mov_estoque_local = '$local'
              AND m.tbl_mov_estoque_tipo_movimentacao IN ('N', 'A', 'B')
            ORDER BY m.tbl_mov_estoque_data_emissao ASC, m.tbl_mov_estoque_numero_id ASC");
    }
}
