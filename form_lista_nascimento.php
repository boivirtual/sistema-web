<?php
    function verificar_estacao($conector, $cobertura_id, $array_estacao){
        // Caminho rápido: usa o conjunto pré-carregado de coberturas cujo nome de
        // estação está entre os selecionados. Esse conjunto é montado com
        // exatamente o mesmo JOIN/filtro usado abaixo, então o resultado é idêntico.
        if (isset($GLOBALS['nasc_cobertura_estacao_ok'])) {
            return isset($GLOBALS['nasc_cobertura_estacao_ok'][$cobertura_id]) ? 'S' : 'N';
        }

        // Cache do nome da estação por cobertura (a consulta só depende de $cobertura_id)
        static $cache_nome = [];

        if (!array_key_exists($cobertura_id, $cache_nome)) {
            $sql = "SELECT tbl_par_estacao_nome FROM tbl_cobertura
                INNER JOIN tbl_parametro_estacao_monta
                        ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_cobertura_id = '" . mysqli_real_escape_string($conector, $cobertura_id) . "'";

            $tbl_cobertura = mysqli_query($conector, $sql);
            $reg_cobertura = mysqli_fetch_object($tbl_cobertura);
            $cache_nome[$cobertura_id] = $reg_cobertura ? $reg_cobertura->tbl_par_estacao_nome : null;
        }

        $tem_cobertura = 'N';

        if ($cache_nome[$cobertura_id] !== null) {
            $nome_estacao = $cache_nome[$cobertura_id];
            $quantidade_itens = count($array_estacao);

            for ($i=0; $i < $quantidade_itens; $i++) {
                if ($array_estacao[$i]==$nome_estacao) {
                    $tem_cobertura = 'S';
                }
            }
        }

        return $tem_cobertura;
    }

    include "conecta_mysql.inc";

    // ===================================================================
    // Acesso aos dados auxiliares da listagem.
    // 1º) Se a pré-carga em lote (nasc_preload) já rodou, os helpers só
    //     consultam os mapas em memória -> ZERO consultas dentro do laço.
    // 2º) Caso contrário, caem no caminho memoizado (1 consulta por chave).
    // Em ambos os casos a consulta e a semântica são as MESMAS do código
    // original; nenhuma regra ou saída HTML muda.
    // ===================================================================
    function nasc_esc($conector, $v) {
        return mysqli_real_escape_string($conector, $v);
    }

    // Monta lista para IN (...) escapando cada item
    function nasc_in($conector, $valores) {
        $itens = array();
        foreach ($valores as $v) {
            $itens[] = "'" . mysqli_real_escape_string($conector, $v) . "'";
        }
        return implode(',', $itens);
    }

    function nasc_desc_local($conector, $id) {
        if (isset($GLOBALS['NASC_M'])) {
            return isset($GLOBALS['NASC_M']['pessoa'][$id]) ? $GLOBALS['NASC_M']['pessoa'][$id] : '';
        }
        static $c = [];
        if (!array_key_exists($id, $c)) {
            $r = mysqli_query($conector, "select tbl_pessoa_nome from tbl_pessoa where tbl_pessoa_id='" . nasc_esc($conector, $id) . "'");
            $row = mysqli_fetch_object($r);
            $c[$id] = $row ? $row->tbl_pessoa_nome : '';
        }
        return $c[$id];
    }

    function nasc_desc_pasto($conector, $id) {
        if (isset($GLOBALS['NASC_M'])) {
            return isset($GLOBALS['NASC_M']['pasto'][$id]) ? $GLOBALS['NASC_M']['pasto'][$id] : '';
        }
        static $c = [];
        if (!array_key_exists($id, $c)) {
            $r = mysqli_query($conector, "select tbl_pasto_descricao from tbl_pasto where tbl_pasto_id ='" . nasc_esc($conector, $id) . "'");
            $row = mysqli_fetch_object($r);
            $c[$id] = $row ? $row->tbl_pasto_descricao : '';
        }
        return $c[$id];
    }

    function nasc_animal($conector, $id) {
        if (isset($GLOBALS['NASC_M'])) {
            return isset($GLOBALS['NASC_M']['animal'][$id]) ? $GLOBALS['NASC_M']['animal'][$id] : null;
        }
        static $c = [];
        if (!array_key_exists($id, $c)) {
            $r = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='" . nasc_esc($conector, $id) . "'");
            $c[$id] = mysqli_fetch_object($r) ?: null;
        }
        return $c[$id];
    }

    function nasc_desc_estacao($conector, $id) {
        if (isset($GLOBALS['NASC_M'])) {
            return isset($GLOBALS['NASC_M']['estacao'][$id]) ? $GLOBALS['NASC_M']['estacao'][$id] : '';
        }
        static $c = [];
        if (!array_key_exists($id, $c)) {
            $r = mysqli_query($conector, "select tbl_par_estacao_nome from tbl_parametro_estacao_monta where tbl_par_estacao_id='" . nasc_esc($conector, $id) . "'");
            $row = mysqli_fetch_object($r);
            $c[$id] = $row ? $row->tbl_par_estacao_nome : '';
        }
        return $c[$id];
    }

    function nasc_desc_raca($conector, $id) {
        if (isset($GLOBALS['NASC_M'])) {
            return isset($GLOBALS['NASC_M']['raca'][$id]) ? $GLOBALS['NASC_M']['raca'][$id] : '';
        }
        static $c = [];
        if (!array_key_exists($id, $c)) {
            $r = mysqli_query($conector, "select tab_descricao_raca from tabela_racas where tab_codigo_raca='" . nasc_esc($conector, $id) . "'");
            $row = mysqli_fetch_object($r);
            $c[$id] = $row ? $row->tab_descricao_raca : '';
        }
        return $c[$id];
    }

    function nasc_desc_cor($conector, $id) {
        if (isset($GLOBALS['NASC_M'])) {
            return isset($GLOBALS['NASC_M']['pelagem'][$id]) ? $GLOBALS['NASC_M']['pelagem'][$id] : '';
        }
        static $c = [];
        if (!array_key_exists($id, $c)) {
            $r = mysqli_query($conector, "select tab_descricao_pelagem from tabela_pelagens where tab_codigo_pelagem ='" . nasc_esc($conector, $id) . "'");
            $row = mysqli_fetch_object($r);
            $c[$id] = $row ? $row->tab_descricao_pelagem : '';
        }
        return $c[$id];
    }

    function nasc_mae($conector, $id) {
        if (isset($GLOBALS['NASC_M'])) {
            return isset($GLOBALS['NASC_M']['mae'][$id]) ? $GLOBALS['NASC_M']['mae'][$id] : null;
        }
        static $c = [];
        if (!array_key_exists($id, $c)) {
            $r = mysqli_query($conector, "select * from tbl_animais
                inner join tabela_racas
                        on tab_codigo_raca=tbl_animal_codigo_raca
                where tbl_animal_codigo_id='" . nasc_esc($conector, $id) . "'");
            $c[$id] = mysqli_fetch_object($r) ?: null;
        }
        return $c[$id];
    }

    function nasc_semem($conector, $id) {
        if (isset($GLOBALS['NASC_M'])) {
            return isset($GLOBALS['NASC_M']['semem'][$id]) ? $GLOBALS['NASC_M']['semem'][$id] : null;
        }
        static $c = [];
        if (!array_key_exists($id, $c)) {
            $r = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='" . nasc_esc($conector, $id) . "'");
            $c[$id] = mysqli_fetch_object($r) ?: null;
        }
        return $c[$id];
    }

    function nasc_itens_cobertura($conector, $cobertura_id, $item) {
        if (isset($GLOBALS['NASC_M'])) {
            $todos = isset($GLOBALS['NASC_M']['itens_cob'][$cobertura_id]) ? $GLOBALS['NASC_M']['itens_cob'][$cobertura_id] : array();
            $out = array();
            foreach ($todos as $row) {
                if ($row->tbl_ite_cobertura_numero_item == $item) {
                    $out[] = $row;
                }
            }
            return $out;
        }
        static $c = [];
        $k = $cobertura_id . '|' . $item;
        if (!array_key_exists($k, $c)) {
            $r = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                     WHERE tbl_cobertura_lixeira=0 AND
                           tbl_ite_cobertura_numero_id='" . nasc_esc($conector, $cobertura_id) . "' AND
                           tbl_ite_cobertura_numero_item='" . nasc_esc($conector, $item) . "'");
            $linhas = [];
            while ($row = mysqli_fetch_object($r)) {
                $linhas[] = $row;
            }
            $c[$k] = $linhas;
        }
        return $c[$k];
    }

    function nasc_protocolo_cobertura($conector, $cobertura_id) {
        if (isset($GLOBALS['NASC_M'])) {
            return isset($GLOBALS['NASC_M']['protocolo_cob'][$cobertura_id]) ? $GLOBALS['NASC_M']['protocolo_cob'][$cobertura_id] : null;
        }
        static $c = [];
        if (!array_key_exists($cobertura_id, $c)) {
            $r = mysqli_query($conector, "SELECT tbl_protocolo_cobertura_data FROM tbl_protocolo_cobertura
                WHERE tbl_protocolo_cobertura_codigo_id = '" . nasc_esc($conector, $cobertura_id) . "'");
            $c[$cobertura_id] = mysqli_fetch_object($r) ?: null;
        }
        return $c[$cobertura_id];
    }

    function nasc_itens_protocolo($conector, $protocolo_id) {
        if (isset($GLOBALS['NASC_M'])) {
            return isset($GLOBALS['NASC_M']['itens_protocolo'][$protocolo_id]) ? $GLOBALS['NASC_M']['itens_protocolo'][$protocolo_id] : array();
        }
        static $c = [];
        if (!array_key_exists($protocolo_id, $c)) {
            $r = mysqli_query($conector, "SELECT tbl_ite_protocoloiatf_descricao FROM tbl_item_protocoloiatf
                WHERE tbl_ite_protocoloiatf_lixeira = 0 AND
                      tbl_ite_protocoloiatf_protocolo_id = '" . nasc_esc($conector, $protocolo_id) . "'
                ORDER BY tbl_ite_protocoloiatf_id ASC");
            $linhas = [];
            while ($row = mysqli_fetch_object($r)) {
                $linhas[] = $row;
            }
            $c[$protocolo_id] = $linhas;
        }
        return $c[$protocolo_id];
    }

    // Pré-carga em lote: recebe as linhas de tbl_movimentacao_estoque já
    // buscadas e monta todos os mapas auxiliares com pouquíssimas consultas
    // IN (...). Depois disso os helpers acima só leem memória.
    function nasc_preload($conector, $movs) {
        $M = array(
            'pessoa' => array(), 'pasto' => array(), 'animal' => array(),
            'estacao' => array(), 'raca' => array(), 'pelagem' => array(),
            'mae' => array(), 'semem' => array(), 'itens_cob' => array(),
            'protocolo_cob' => array(), 'itens_protocolo' => array()
        );

        $faz = array(); $pas = array(); $ani = array(); $cob = array(); $maes = array();
        $cob_item = array(); // pares exatos (cobertura_id, numero_item) que aparecem nos movs
        foreach ($movs as $m) {
            if ($m->tbl_mov_estoque_local !== null && $m->tbl_mov_estoque_local !== '') $faz[$m->tbl_mov_estoque_local] = true;
            if ($m->tbl_mov_estoque_codigo_pasto !== null && $m->tbl_mov_estoque_codigo_pasto !== '') $pas[$m->tbl_mov_estoque_codigo_pasto] = true;
            if ($m->tbl_mov_estoque_codigo_id_animal !== null && $m->tbl_mov_estoque_codigo_id_animal !== '') $ani[$m->tbl_mov_estoque_codigo_id_animal] = true;
            if ($m->tbl_mov_estoque_cobertura_numero_id !== null && $m->tbl_mov_estoque_cobertura_numero_id !== '') {
                $cob[$m->tbl_mov_estoque_cobertura_numero_id] = true;
                $cob_item[$m->tbl_mov_estoque_cobertura_numero_id . '|' . $m->tbl_mov_estoque_cobertura_numero_item] =
                    array($m->tbl_mov_estoque_cobertura_numero_id, $m->tbl_mov_estoque_cobertura_numero_item);
            }
            if ($m->tbl_mov_estoque_codigo_mae !== null && $m->tbl_mov_estoque_codigo_mae !== '') $maes[$m->tbl_mov_estoque_codigo_mae] = true;
        }

        // Tabelas pequenas: carrega inteiras (o original consultava sem filtro de lixeira)
        $q = mysqli_query($conector, "SELECT tab_codigo_raca, tab_descricao_raca FROM tabela_racas");
        while ($r = mysqli_fetch_object($q)) $M['raca'][$r->tab_codigo_raca] = $r->tab_descricao_raca;

        $q = mysqli_query($conector, "SELECT tab_codigo_pelagem, tab_descricao_pelagem FROM tabela_pelagens");
        while ($r = mysqli_fetch_object($q)) $M['pelagem'][$r->tab_codigo_pelagem] = $r->tab_descricao_pelagem;

        $q = mysqli_query($conector, "SELECT tbl_par_estacao_id, tbl_par_estacao_nome FROM tbl_parametro_estacao_monta");
        while ($r = mysqli_fetch_object($q)) $M['estacao'][$r->tbl_par_estacao_id] = $r->tbl_par_estacao_nome;

        if (!empty($faz)) {
            $in = nasc_in($conector, array_keys($faz));
            $q = mysqli_query($conector, "SELECT tbl_pessoa_id, tbl_pessoa_nome FROM tbl_pessoa WHERE tbl_pessoa_id IN ($in)");
            while ($r = mysqli_fetch_object($q)) $M['pessoa'][$r->tbl_pessoa_id] = $r->tbl_pessoa_nome;
        }

        if (!empty($pas)) {
            $in = nasc_in($conector, array_keys($pas));
            $q = mysqli_query($conector, "SELECT tbl_pasto_id, tbl_pasto_descricao FROM tbl_pasto WHERE tbl_pasto_id IN ($in)");
            while ($r = mysqli_fetch_object($q)) $M['pasto'][$r->tbl_pasto_id] = $r->tbl_pasto_descricao;
        }

        $pais = array();
        if (!empty($ani)) {
            $in = nasc_in($conector, array_keys($ani));
            $q = mysqli_query($conector, "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa, tbl_animal_codigo_numerico,
                    tbl_animal_codigo_raca, tbl_animal_codigo_pelagem, tbl_animal_sexo, tbl_animal_codigo_mae,
                    tbl_animal_codigo_pai, tbl_animal_situacao, tbl_animal_estacao_monta_nascimento
                FROM tbl_animais WHERE tbl_animal_codigo_id IN ($in)");
            while ($r = mysqli_fetch_object($q)) {
                $M['animal'][$r->tbl_animal_codigo_id] = $r;
                if ($r->tbl_animal_codigo_mae !== null && $r->tbl_animal_codigo_mae !== '') $maes[$r->tbl_animal_codigo_mae] = true;
                if ($r->tbl_animal_codigo_pai !== null && $r->tbl_animal_codigo_pai !== '') $pais[$r->tbl_animal_codigo_pai] = true;
            }
        }

        if (!empty($pais)) {
            $in = nasc_in($conector, array_keys($pais));
            // pai como touro (fallback) — mesmo mapa 'animal', sem sobrescrever bezerro já carregado
            $q = mysqli_query($conector, "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa, tbl_animal_codigo_numerico
                FROM tbl_animais WHERE tbl_animal_codigo_id IN ($in)");
            while ($r = mysqli_fetch_object($q)) {
                if (!isset($M['animal'][$r->tbl_animal_codigo_id])) $M['animal'][$r->tbl_animal_codigo_id] = $r;
            }
            // pai como sêmen
            $q = mysqli_query($conector, "SELECT tbl_semem_codigo_id, tbl_semem_nome FROM tbl_semem WHERE tbl_semem_codigo_id IN ($in)");
            while ($r = mysqli_fetch_object($q)) $M['semem'][$r->tbl_semem_codigo_id] = $r;
        }

        if (!empty($maes)) {
            $in = nasc_in($conector, array_keys($maes));
            $q = mysqli_query($conector, "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa, tbl_animal_codigo_numerico,
                    tab_descricao_raca
                FROM tbl_animais
                INNER JOIN tabela_racas ON tab_codigo_raca=tbl_animal_codigo_raca
                WHERE tbl_animal_codigo_id IN ($in)");
            while ($r = mysqli_fetch_object($q)) $M['mae'][$r->tbl_animal_codigo_id] = $r;
        }

        $protos = array();
        if (!empty($cob)) {
            // Só os itens (cobertura, numero_item) que realmente aparecem — usa a PK
            // composta (numero_id, numero_item). Colunas só as usadas (nada de SELECT *).
            $pares = array();
            foreach ($cob_item as $par) {
                $pares[] = "('" . mysqli_real_escape_string($conector, $par[0]) . "','" .
                                  mysqli_real_escape_string($conector, $par[1]) . "')";
            }
            $inpares = implode(',', $pares);
            $q = mysqli_query($conector, "SELECT tbl_ite_cobertura_numero_id, tbl_ite_cobertura_numero_item,
                    tbl_ite_cobertura_data_prenhes, tbl_cobertura_codigo_estacao_monta,
                    tbl_cobertura_protocoloiatf, tbl_cobertura_controle
                FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      (tbl_ite_cobertura_numero_id, tbl_ite_cobertura_numero_item) IN ($inpares)");
            while ($r = mysqli_fetch_object($q)) {
                $M['itens_cob'][$r->tbl_ite_cobertura_numero_id][] = $r;
                if ($r->tbl_cobertura_protocoloiatf !== null && $r->tbl_cobertura_protocoloiatf !== '') $protos[$r->tbl_cobertura_protocoloiatf] = true;
            }

            $in = nasc_in($conector, array_keys($cob));
            $q = mysqli_query($conector, "SELECT tbl_protocolo_cobertura_codigo_id, tbl_protocolo_cobertura_data
                FROM tbl_protocolo_cobertura WHERE tbl_protocolo_cobertura_codigo_id IN ($in)");
            while ($r = mysqli_fetch_object($q)) {
                if (!isset($M['protocolo_cob'][$r->tbl_protocolo_cobertura_codigo_id])) {
                    $M['protocolo_cob'][$r->tbl_protocolo_cobertura_codigo_id] = $r;
                }
            }
        }

        if (!empty($protos)) {
            $in = nasc_in($conector, array_keys($protos));
            $q = mysqli_query($conector, "SELECT tbl_ite_protocoloiatf_protocolo_id, tbl_ite_protocoloiatf_descricao
                FROM tbl_item_protocoloiatf
                WHERE tbl_ite_protocoloiatf_lixeira = 0 AND tbl_ite_protocoloiatf_protocolo_id IN ($in)
                ORDER BY tbl_ite_protocoloiatf_id ASC");
            while ($r = mysqli_fetch_object($q)) {
                $M['itens_protocolo'][$r->tbl_ite_protocoloiatf_protocolo_id][] = $r;
            }
        }

        return $M;
    }

    $wlocal = "";
    $wlocal_cobertura='';

    if (isset($_POST['local'])) {
        $local = $_POST['local'];

        if(in_array("", $local)) {
            $wlocal='';
        }
        else {
            $wlocal = " AND tbl_mov_estoque_local IN(";
            $wlocal.= implode(',', $local);
            $wlocal.= ")";

            $wlocal_cobertura = " AND tbl_cobertura_codigo_local IN(";
            $wlocal_cobertura.= implode(',', $local);
            $wlocal_cobertura.= ")";
            }
    }
    else {
        $wlocal='';
        $wlocal_cobertura='';
    }

    $westacao = "";
    if (isset($_POST['estacao'])) {
        $estacao = $_POST['estacao'];

        if(in_array("", $estacao)) {
            $westacao='';
        }
        else {
            $westacao = " tbl_par_estacao_id  IN(";
            $westacao.= implode(',', $estacao);
            $westacao.= ")";
            }
    }
    else {
        $westacao='';
    }

    // Monta array estacao 
    if ($westacao!='') {
        $array_estacao = array();
        $estacao_anterior = '';

        $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
            WHERE tbl_par_lixeira=0 AND " . $westacao . 
            "ORDER BY tbl_par_estacao_nome ASC"); 

        $num_rows = mysqli_num_rows($sql);

        if($num_rows != 0){
            while($ln = mysqli_fetch_assoc($sql)){
                $nome = $ln['tbl_par_estacao_nome'];

                if ($estacao_anterior!=$nome) {
                    $array_estacao[]=$nome;
                    $estacao_anterior=$nome;
                }
            }
        }
    }
    // Fim array estacao

    // ===================================================================
    // PRÉ-CARGA (lote) para a listagem POR ESTAÇÃO DE MONTA.
    // Antes: a consulta principal trazia TODO o histórico de nascimentos das
    // fazendas (sem filtro de data/estação) e, para cada linha, rodava uma
    // consulta em verificar_estacao() só para descobrir se a cobertura era
    // da estação escolhida. Agora: um único SELECT levanta todas as coberturas
    // cujo nome de estação está entre os selecionados; esse conjunto vira
    // (a) um filtro direto na consulta principal e (b) o mapa usado por
    // verificar_estacao(). Mesmo resultado, sem o N+1.
    // ===================================================================
    $GLOBALS['nasc_cobertura_estacao_ok'] = null;
    $wcobertura_estacao = '';

    if ($westacao != '') {
        $GLOBALS['nasc_cobertura_estacao_ok'] = array();

        if (!empty($array_estacao)) {
            $nomes_in = array();
            foreach ($array_estacao as $ne) {
                $nomes_in[] = "'" . mysqli_real_escape_string($conector, $ne) . "'";
            }
            $nomes_in = implode(',', $nomes_in);

            $q = mysqli_query($conector, "SELECT tbl_cobertura_id
                FROM tbl_cobertura
                INNER JOIN tbl_parametro_estacao_monta
                        ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
                WHERE tbl_cobertura_lixeira=0 AND tbl_par_estacao_nome IN ($nomes_in)");
            while ($r = mysqli_fetch_object($q)) {
                $GLOBALS['nasc_cobertura_estacao_ok'][$r->tbl_cobertura_id] = true;
            }
        }

        if (!empty($GLOBALS['nasc_cobertura_estacao_ok'])) {
            $lista_cob = array();
            foreach (array_keys($GLOBALS['nasc_cobertura_estacao_ok']) as $cid) {
                $lista_cob[] = "'" . mysqli_real_escape_string($conector, $cid) . "'";
            }
            $wcobertura_estacao = " AND tbl_mov_estoque_cobertura_numero_id IN (" . implode(',', $lista_cob) . ")";
        }
        else {
            $wcobertura_estacao = " AND 1=0";
        }
    }

    $wtipo = "";
    if (isset($_POST['tipo'])) {
        $tipo = $_POST['tipo'];

        if(in_array("", $tipo)) {
            $wtipo='';
        }
        else {
            $wtipo = " tbl_mov_estoque_tipo_movimentacao IN(";
            $wtipo.= implode(',', $tipo);
            $wtipo.= ")";
        }
    }
    else {
        $wtipo='';
    }

    $data_nasc_inicial = $_POST["data_inicial"];
    $data_nasc_final = $_POST["data_final"];

    if ($data_nasc_inicial=='' && $data_nasc_final==''){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " tbl_mov_estoque_nascimento >= '$data_nasc_inicial' AND tbl_mov_estoque_nascimento <= '$data_nasc_final' AND";
    }

    @ session_start(); 

    $_SESSION['local_nascimento']=implode(',', $local);
    $_SESSION['ocorrencia_nascimento']=$tipo;
    $_SESSION['data_inicial_nascimento']=$data_nasc_inicial; 
    $_SESSION['data_final_nascimento']=$data_nasc_final; 
    $_SESSION['lista_nascimento']='S';
    $_SESSION['estacao_nascimento']=implode(',', $estacao);

    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = "SELECT * FROM usuario 
        WHERE id_usuario = '$codigo_usuario' AND 
              lixeira_usuario=0 ";  
    $query = mysqli_query($conector_acesso, $tbl_usuario);

    $num_rows_usuario = mysqli_num_rows($query);

    if ($num_rows_usuario!=0){
        $reg_usuario = mysqli_fetch_assoc($query);

        $array_locais_usuario = explode(',', $reg_usuario['local_usuario']);
        $qtd_locais_usuario = count($array_locais_usuario);

        if ($qtd_locais_usuario==0) {
            $array_locais_usuario='';
        }
    }
    else {
        $array_locais_usuario='';
    }

    $controle_estoque = $_SESSION['controle_estoque'];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Creative - Bootstrap 3 Responsive Admin Template">
  <meta name="author" content="GeeksLabs">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet">
  <link href="css/font-awesome.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet">
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

</head>

<body> 

    <td align="center"></td>
  <?php    
	echo '<section class="panel lista_contas">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_nascimento" style="font-size: 11px">';
                          
    echo '<tbody>';
    if ($westacao=='') {
        // LISTA PELO PERIODO DO NASCIMENTO
        if ($wtipo=='') {
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE" . $wdata_nasc . " 
                    (tbl_mov_estoque_tipo_movimentacao='N' OR 
                     tbl_mov_estoque_tipo_movimentacao='A' OR
                     tbl_mov_estoque_tipo_movimentacao='B')" . $wlocal .
                " ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N')") { 
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                    " AND tbl_mov_estoque_codigo_id_animal!=999999999
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','M')") {
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                    " AND tbl_mov_estoque_entrada_saida='E'
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','B')") {
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                      " AND (tbl_mov_estoque_tipo_movimentacao='N' AND
                             tbl_mov_estoque_codigo_id_animal!=999999999) 
                        OR (tbl_mov_estoque_entrada_saida='A' AND tbl_mov_estoque_tipo_movimentacao='B')
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','A')") {
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                      " AND (tbl_mov_estoque_tipo_movimentacao='N' AND
                       tbl_mov_estoque_codigo_id_animal!=999999999) 
                      OR (tbl_mov_estoque_entrada_saida='A' AND tbl_mov_estoque_tipo_movimentacao='A')
               ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','B','A')") {
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                      " AND (tbl_mov_estoque_tipo_movimentacao='N' AND
                             tbl_mov_estoque_codigo_id_animal!=999999999) 
                        OR (tbl_mov_estoque_entrada_saida='A' AND 
                            tbl_mov_estoque_tipo_movimentacao='A')
                        OR (tbl_mov_estoque_entrada_saida='A' AND 
                            tbl_mov_estoque_tipo_movimentacao='B')
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','B','A','M')" || 
                 $wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','B','M')" || 
                 $wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','A','M')") { 
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                      " AND tbl_mov_estoque_entrada_saida!='S'
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('M')" || 
                 $wtipo=="  tbl_mov_estoque_tipo_movimentacao IN('A','M')" || 
                 $wtipo=="  tbl_mov_estoque_tipo_movimentacao IN('B','A','M')" || 
                 $wtipo=="  tbl_mov_estoque_tipo_movimentacao IN('B','M')"){ 
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wdata_nasc . $wtipo  . $wlocal .
                      " AND tbl_mov_estoque_codigo_id_animal=999999999
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
                }
        else {
        $sql = "SELECT * FROM tbl_movimentacao_estoque 
            WHERE " . $wdata_nasc . $wtipo  . $wlocal .
            " ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }

        $rs = mysqli_query($conector, $sql);
        $num_rows_estoque = mysqli_num_rows($rs);
        $total_nascimento = 0;
        $total_natimorto = 0;
        $total_absorcao = 0;
        $total_aborto = 0;

        // Buffer + pré-carga em lote de todos os dados auxiliares
        $movs = array();
        while ($reg_nasc = mysqli_fetch_object($rs)) { $movs[] = $reg_nasc; }
        $GLOBALS['NASC_M'] = nasc_preload($conector, $movs);

        foreach ($movs as $reg_nasc){
            $codigo_fazenda = $reg_nasc->tbl_mov_estoque_local;

            foreach ($array_locais_usuario as $value) {
                $value = ltrim($value);
                $value = rtrim($value);

                if ($value==$codigo_fazenda) {
                    $num_mov_id = $reg_nasc->tbl_mov_estoque_numero_id ;
                    $codigo = $reg_nasc->tbl_mov_estoque_codigo_id_animal;
                    $data_emissao = $reg_nasc->tbl_mov_estoque_data_emissao;
                    $data_nascimento = new DateTime($reg_nasc->tbl_mov_estoque_nascimento);
                    $nascimento = $reg_nasc->tbl_mov_estoque_nascimento;
                    $codigo_pasto = $reg_nasc->tbl_mov_estoque_codigo_pasto;
                    $peso = $reg_nasc->tbl_mov_estoque_primeiro_peso;
                    $tipo_movimentacao = $reg_nasc->tbl_mov_estoque_tipo_movimentacao;
                    $cobertura_id = $reg_nasc->tbl_mov_estoque_cobertura_numero_id; 
                    $item_cobertura = $reg_nasc->tbl_mov_estoque_cobertura_numero_item;
                    $monta_natural = $reg_nasc->tbl_mov_estoque_cobertura_monta_natural;

                    if ($tipo_movimentacao=='A') {
                        $desc_tipo = 'Aborto';
                        $ocorrencia = 'A';
                        $total_aborto++;
                    }
                    else if ($tipo_movimentacao=='B') {
                        $desc_tipo = 'Absorção';
                        $ocorrencia = "B";
                        $total_absorcao++;
                    }
                    else if ($tipo_movimentacao=='M' || ($tipo_movimentacao=='N' && $codigo==999999999)) { 
                        $desc_tipo = '';
                        $ocorrencia = 'M';
                    }
                    else {
                        $desc_tipo = '';
                        $ocorrencia = 'N';
                    }

                    $desc_local = nasc_desc_local($conector, $codigo_fazenda);

                    $desc_pasto = nasc_desc_pasto($conector, $codigo_pasto);

                    $reg_animal = nasc_animal($conector, $codigo);

                    if ($reg_animal !== null){
                        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                        $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
                        $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
                        $codigo_cor = $reg_animal->tbl_animal_codigo_pelagem;
                        $sexo = $reg_animal->tbl_animal_sexo; 
                        $mae = $reg_animal->tbl_animal_codigo_mae; 
                        $pai = $reg_animal->tbl_animal_codigo_pai; 
                        $situacao = $reg_animal->tbl_animal_situacao;
                        $estacao_monta = $reg_animal->tbl_animal_estacao_monta_nascimento;

                        $descricao_estacao = nasc_desc_estacao($conector, $estacao_monta);

                        if ($sexo=='N') {
                            $desc_sexo = '';
                        }
                        else {
                            $desc_sexo = $sexo;
                        }

                        $descricao_raca = nasc_desc_raca($conector, $codigo_raca);

                        $descricao_cor = nasc_desc_cor($conector, $codigo_cor);

                        $reg_mae_j = nasc_mae($conector, $mae);

                        if ($reg_mae_j !== null){
                            $descricao_mae = $reg_mae_j->tbl_animal_codigo_alfa. ' ' . $reg_mae_j->tbl_animal_codigo_numerico;
                            $mae_raca = $reg_mae_j->tbl_animal_codigo_alfa. ' ' . $reg_mae_j->tbl_animal_codigo_numerico . ' - ' . $reg_mae_j->tab_descricao_raca;
                        }
                        else {
                            $descricao_mae = '';
                            $mae='';
                            $mae_raca='';
                        }

                        $reg_semem_pai = nasc_semem($conector, $pai);

                        if ($reg_semem_pai !== null){
                            $descricao_pai = $reg_semem_pai->tbl_semem_nome;
                            $pai = $reg_semem_pai->tbl_semem_codigo_id;
                        }
                        else {
                            $reg_animal_pai = nasc_animal($conector, $pai);

                            if ($reg_animal_pai !== null){
                                $descricao_pai = $reg_animal_pai->tbl_animal_codigo_alfa. ' ' . $reg_animal_pai->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_pai = '';
                                $pai='000000000';
                            }
                        }
                    }
                    else {
                        $codigo_raca = $reg_nasc->tbl_mov_estoque_codigo_raca;
                        $codigo_cor = $reg_nasc->tbl_mov_estoque_codigo_pelagem;
                        $sexo = $reg_nasc->tbl_mov_estoque_sexo;
                        $mae = $reg_nasc->tbl_mov_estoque_codigo_mae; 

                        if ($sexo=='N') {
                            $desc_sexo = '';
                        }
                        else {
                            $desc_sexo = $sexo;
                        }
                                
                        $descricao_raca = nasc_desc_raca($conector, $codigo_raca);

                        $descricao_cor = nasc_desc_cor($conector, $codigo_cor);

                        $reg_mae_j = nasc_mae($conector, $mae);

                        if ($reg_mae_j !== null){
                            $descricao_mae = $reg_mae_j->tbl_animal_codigo_alfa. ' ' . $reg_mae_j->tbl_animal_codigo_numerico;
                            $mae_raca = $reg_mae_j->tbl_animal_codigo_alfa. ' ' . $reg_mae_j->tbl_animal_codigo_numerico . ' - ' . $reg_mae_j->tab_descricao_raca;
                        }
                        else {
                            $descricao_mae = '';
                            $mae='';
                            $mae_raca='';
                        }

                        $codigo_alfa = '';
                        $codigo_numerico = $codigo;
                        $descricao_pai = '';
                        $pai='000000000';
                        $situacao = '';
                        $estacao_monta = 0;
                        $descricao_estacao = '';
                    }

                    if ($codigo_alfa=='') {
                        $codigo_edi = $codigo_numerico;
                    }
                    else {
                        $codigo_edi = $codigo_alfa .'-'. $codigo_numerico;
                    }
        
                    // Calcula dias de Gestação para Nascimento, Aborto, Natimorto - 09/01/2024
                    $itens_cob = nasc_itens_cobertura($conector, $cobertura_id, $item_cobertura);

                    $num_rows = count($itens_cob);

                    if ($num_rows!=0) {
                        foreach ($itens_cob as $reg_item) {
                            //$cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
                            //$item_cobertura = $reg_item->tbl_ite_cobertura_numero_item;
                            $estacao_monta_id = $reg_item->tbl_cobertura_codigo_estacao_monta;
                            $protocolo_id = $reg_item->tbl_cobertura_protocoloiatf;
                            $controle = $reg_item->tbl_cobertura_controle;
                            $data_servico = $reg_item->tbl_ite_cobertura_data_prenhes;
                        }
                    }
                    else {
                        //$cobertura_id = 0;
                        //$item_cobertura = 0;
                        $estacao_monta_id = 0;  
                        $protocolo_id = 0;
                        $dias_gestacao = 0;   
                        $data_servico = 0;
                        $controle = '';
                    }

                    if ($cobertura_id!=0) {
                        if ($controle=='C') {
                            $reg_protocolo_cobertura = nasc_protocolo_cobertura($conector, $cobertura_id);

                            foreach (nasc_itens_protocolo($conector, $protocolo_id) as $reg_itens){
                                $dias = substr($reg_itens->tbl_ite_protocoloiatf_descricao, 3);
                                $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));
                            }
                        }

                        if ($data_servico=='' || $data_servico==0 || $data_servico=='0000-00-00') {
                            $dias_gestacao = 0;
                        }
                        else {
                            $firstDate  = new DateTime($data_servico);
                            $secondDate = new DateTime($nascimento);
                            $intvl = $firstDate->diff($secondDate);
                            $dias_gestacao = $intvl->days;
                        }
                    }

                    $array_animal = array(
                        $codigo,
                        $codigo_alfa,
                        $codigo_numerico,
                        $sexo,
                        $codigo_raca,
                        $codigo_cor,
                        $reg_nasc->tbl_mov_estoque_nascimento,
                        $codigo_fazenda,
                        $codigo_pasto,
                        $pai,
                        $mae,
                        $peso,
                        $mae_raca,
                        $desc_local,
                        $num_mov_id,
                        $desc_pasto,
                        $data_emissao,
                        $tipo_movimentacao,
                        $ocorrencia,
                        $descricao_estacao,
                        $cobertura_id, 
                        $item_cobertura 
                    );   
                                            
                    $string_array = implode('|', $array_animal);

                    echo "<tr>";

                    if ($controle_estoque == 'I') {
                        if ($situacao=='') {
                            if ($tipo_movimentacao=='A' || $tipo_movimentacao=='B') {
                                echo "<td width='5%'></td>";
                                echo "<td width='8%'>".$desc_tipo."</td>";
                            }
                            else if ($tipo_movimentacao=='N' && $codigo_numerico!=999999999) {
                                echo "<td width='5%'>".$codigo_alfa."</td>";
                                echo "<td width='8%'>".$codigo_numerico."</td>";
                                $total_nascimento++;
                            }
                            else if ($codigo_numerico==999999999) {
                                echo "<td width='5%'></td>";
                                echo "<td width='8%'>Natimorto</td>";
                                $total_natimorto++;
                            }                                
                            else {
                                echo "<td width='5%'>".$codigo_alfa."</td>";
                                echo "<td width='8%'>".$codigo_numerico."</td>";
                                $total_nascimento++;
                            }
                        }
                        else {
                            if ($tipo_movimentacao=='A' || $tipo_movimentacao=='B') {
                                echo "<td style='color: red;' width='5%'></td>";
                                echo "<td style='color: red;' width='8%'>".$desc_tipo."</td>";
                                }
                            else if ($tipo_movimentacao=='N' && $codigo_numerico!=999999999) {
                                echo "<td style='color: red;' width='5%'>".$codigo_alfa."</td>";
                                echo "<td style='color: red;' width='8%'>".$codigo_numerico."</td>";
                                $total_nascimento++;
                            }
                            else if ($codigo_numerico==999999999) {
                                echo "<td style='color: red;' width='5%'></td>";
                                echo "<td style='color: red;' width='8%'>Natimorto</td>";
                                $total_natimorto++;
                            }                                
                            else {
                                echo "<td style='color: red;' width='5%'>".$codigo_alfa."</td>";
                                echo "<td style='color: red;' width='8%'>".$codigo_numerico."</td>";
                                $total_nascimento++;
                            }
                        }
                    }
                    else {
                        echo "<td width='5%'></td>";
                        echo "<td width='8%'>".$desc_tipo."</td>";
                        $total_nascimento++;
                    }

                    if ($situacao=='') {
                        echo "<td align='center' width='4%'>".$data_nascimento->format('d/m/Y')."</td>";

                        if ($dias_gestacao>=252 && $dias_gestacao<=303) {
                            if ($monta_natural=='S') {
                                echo "<td align='center' style='color: red;' title='Monta Natural' width='4%'>".$dias_gestacao."</td>";
                            }
                            else {
                                echo "<td align='center' width='4%'>".$dias_gestacao."</td>";
                            }
                        }
                        else {
                            echo "<td align='center' style='color: red;' width='4%'>".$dias_gestacao."</td>";
                        }

                        echo "<td align='center' width='2%' >". $desc_sexo."</td>";
                        echo "<td width='3%'>".$peso."</td>";
                        echo "<td width='10%'>".$descricao_raca."</td>";
                        echo "<td width='8%'>".$descricao_cor."</td>";
                        echo "<td width='15%'>".$desc_local."</td>";
                        echo "<td width='13%'>".$desc_pasto."</td>";
                        echo "<td width='8%'>".$descricao_mae."</td>";
                        echo "<td width='8%'>".$descricao_pai."</td>";
                    }
                    else {
                        echo "<td align='center' style='color: red;' width='4%'>".$data_nascimento->format('d/m/Y')."</td>";
                        echo "<td align='center' style='color: red;' width='4%' >".$dias_gestacao."</td>";
                        echo "<td align='center' style='color: red;' width='2%' >".$desc_sexo."</td>";
                        echo "<td style='color: red;' width='3%'>".$peso."</td>";
                        echo "<td style='color: red;' width='10%'>".$descricao_raca."</td>";
                        echo "<td style='color: red;' width='8%'>".$descricao_cor."</td>";
                        echo "<td style='color: red;' width='15%'>".$desc_local."</td>";
                        echo "<td style='color: red;' width='13%'>".$desc_pasto."</td>";
                        echo "<td style='color: red;' width='8%'>".$descricao_mae."</td>";
                        echo "<td style='color: red;' width='8%'>".$descricao_pai."</td>";
                    }
                    
                    echo "<td width='12%'>";    
                    echo "<div class='btn-group'>";
 
                    if ($controle_estoque == 'I') {
                        if ($tipo_movimentacao=='A' || $tipo_movimentacao=='B' || $codigo_numerico==999999999) {
                            echo "<a class='btn' href='#'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' onClick='editar_animal(\"{$string_array}\")' ></i></a>"; 
                        }
                        else {
                            echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\")' ></i></a>"; 
                        }
                    }
                    else {
                        echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\")' ></i></a>"; 
                    }
                    echo "<a class='btn' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                } // fim do if fazenda
            } // fim do foreach 
        } // fim do while

    }
    else { 
        // LISTA PELA ESTACAO DE MONTA
        if ($wtipo=='') {
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE (tbl_mov_estoque_tipo_movimentacao='N' OR 
                     tbl_mov_estoque_tipo_movimentacao='A' OR
                     tbl_mov_estoque_tipo_movimentacao='B')" . $wlocal .
                " ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N')") { 
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wtipo  . $wlocal .
                    " AND tbl_mov_estoque_codigo_id_animal!=999999999
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','M')") {
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wtipo  . $wlocal .
                    " AND tbl_mov_estoque_entrada_saida='E'
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','B')") {
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wtipo  . $wlocal .
                      " AND (tbl_mov_estoque_tipo_movimentacao='N' AND
                             tbl_mov_estoque_codigo_id_animal!=999999999) 
                        OR (tbl_mov_estoque_entrada_saida='A' AND tbl_mov_estoque_tipo_movimentacao='B')
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','A')") {
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wtipo  . $wlocal .
                      " AND (tbl_mov_estoque_tipo_movimentacao='N' AND
                       tbl_mov_estoque_codigo_id_animal!=999999999) 
                      OR (tbl_mov_estoque_entrada_saida='A' AND tbl_mov_estoque_tipo_movimentacao='A')
               ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','B','A')") {
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wtipo  . $wlocal .
                      " AND (tbl_mov_estoque_tipo_movimentacao='N' AND
                             tbl_mov_estoque_codigo_id_animal!=999999999) 
                        OR (tbl_mov_estoque_entrada_saida='A' AND 
                            tbl_mov_estoque_tipo_movimentacao='A')
                        OR (tbl_mov_estoque_entrada_saida='A' AND 
                            tbl_mov_estoque_tipo_movimentacao='B')
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','B','A','M')" || 
                 $wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','B','M')" || 
                 $wtipo==" tbl_mov_estoque_tipo_movimentacao IN('N','A','M')") { 
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wtipo  . $wlocal .
                      " AND tbl_mov_estoque_entrada_saida!='S'
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
        }
        else if ($wtipo==" tbl_mov_estoque_tipo_movimentacao IN('M')" || 
                 $wtipo=="  tbl_mov_estoque_tipo_movimentacao IN('A','M')" || 
                 $wtipo=="  tbl_mov_estoque_tipo_movimentacao IN('B','A','M')" || 
                 $wtipo=="  tbl_mov_estoque_tipo_movimentacao IN('B','M')"){ 
            $sql = "SELECT * FROM tbl_movimentacao_estoque 
                WHERE " . $wtipo  . $wlocal .
                      " AND tbl_mov_estoque_codigo_id_animal=999999999
                ORDER BY tbl_mov_estoque_data_emissao ASC"; 
                }
        else {
            $sql = "SELECT * FROM tbl_movimentacao_estoque
                WHERE " . $wtipo  . $wlocal .
                " ORDER BY tbl_mov_estoque_data_emissao ASC";
        }

        // Restringe a consulta às coberturas da(s) estação(ões) escolhida(s)
        // (mesmo conjunto que verificar_estacao() considera 'S').
        $sql = str_replace(
            "ORDER BY tbl_mov_estoque_data_emissao ASC",
            $wcobertura_estacao . " ORDER BY tbl_mov_estoque_data_emissao ASC",
            $sql
        );

        $rs = mysqli_query($conector, $sql);
        $num_rows_estoque = mysqli_num_rows($rs);
        $total_nascimento = 0;
        $total_natimorto = 0;
        $total_absorcao = 0;
        $total_aborto = 0;

        // Buffer + pré-carga em lote de todos os dados auxiliares
        $movs = array();
        while ($reg_nasc = mysqli_fetch_object($rs)) { $movs[] = $reg_nasc; }
        $GLOBALS['NASC_M'] = nasc_preload($conector, $movs);

        foreach ($movs as $reg_nasc){
            $codigo_fazenda = $reg_nasc->tbl_mov_estoque_local;

            foreach ($array_locais_usuario as $value) {
                $value = ltrim($value);
                $value = rtrim($value);

                if ($value==$codigo_fazenda) {
                    $num_mov_id = $reg_nasc->tbl_mov_estoque_numero_id ;
                    $codigo = $reg_nasc->tbl_mov_estoque_codigo_id_animal;
                    $data_emissao = $reg_nasc->tbl_mov_estoque_data_emissao;
                    $data_nascimento = new DateTime($reg_nasc->tbl_mov_estoque_nascimento);
                    $nascimento = $reg_nasc->tbl_mov_estoque_nascimento;
                    $codigo_pasto = $reg_nasc->tbl_mov_estoque_codigo_pasto;
                    $peso = $reg_nasc->tbl_mov_estoque_primeiro_peso;
                    $tipo_movimentacao = $reg_nasc->tbl_mov_estoque_tipo_movimentacao;
                    $cobertura_id = $reg_nasc->tbl_mov_estoque_cobertura_numero_id;
                    $item_cobertura = $reg_nasc->tbl_mov_estoque_cobertura_numero_item;
                    $monta_natural = $reg_nasc->tbl_mov_estoque_cobertura_monta_natural;

                    $estacao_correta = verificar_estacao($conector, $cobertura_id, $array_estacao);

                    if ($estacao_correta=='S') {
                        if ($tipo_movimentacao=='A') {
                            $desc_tipo = 'Aborto';
                            $ocorrencia = 'A';
                            $total_aborto++;
                        }
                        else if ($tipo_movimentacao=='B') {
                            $desc_tipo = 'Absorção';
                            $ocorrencia = "B";
                            $total_absorcao++;
                        }
                        else if ($tipo_movimentacao=='M' || ($tipo_movimentacao=='N' && $codigo==999999999)) { 
                            $desc_tipo = '';
                            $ocorrencia = 'M';
                        }
                        else {
                            $desc_tipo = '';
                            $ocorrencia = 'N';
                        }

                        $desc_local = nasc_desc_local($conector, $codigo_fazenda);

                        $desc_pasto = nasc_desc_pasto($conector, $codigo_pasto);

                        $reg_animal = nasc_animal($conector, $codigo);

                        if ($reg_animal !== null){
                            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                            $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
                            $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
                            $codigo_cor = $reg_animal->tbl_animal_codigo_pelagem;
                            $sexo = $reg_animal->tbl_animal_sexo; 
                            $mae = $reg_animal->tbl_animal_codigo_mae; 
                            $pai = $reg_animal->tbl_animal_codigo_pai; 
                            $situacao = $reg_animal->tbl_animal_situacao;
                            $estacao_monta = $reg_animal->tbl_animal_estacao_monta_nascimento;

                            $descricao_estacao = nasc_desc_estacao($conector, $estacao_monta);

                            if ($sexo=='N') {
                                $desc_sexo = '';
                            }
                            else {
                                $desc_sexo = $sexo;
                            }

                            $descricao_raca = nasc_desc_raca($conector, $codigo_raca);

                            $descricao_cor = nasc_desc_cor($conector, $codigo_cor);

                            $reg_mae_j = nasc_mae($conector, $mae);

                            if ($reg_mae_j !== null){
                                $descricao_mae = $reg_mae_j->tbl_animal_codigo_alfa. ' ' . $reg_mae_j->tbl_animal_codigo_numerico;
                                $mae_raca = $reg_mae_j->tbl_animal_codigo_alfa. ' ' . $reg_mae_j->tbl_animal_codigo_numerico . ' - ' . $reg_mae_j->tab_descricao_raca;
                            }
                            else {
                                $descricao_mae = '';
                                $mae='';
                                $mae_raca='';
                            }

                            $reg_semem_pai = nasc_semem($conector, $pai);

                            if ($reg_semem_pai !== null){
                                $descricao_pai = $reg_semem_pai->tbl_semem_nome;
                                $pai = $reg_semem_pai->tbl_semem_codigo_id;
                            }
                            else {
                                $reg_animal_pai = nasc_animal($conector, $pai);

                                if ($reg_animal_pai !== null){
                                    $descricao_pai = $reg_animal_pai->tbl_animal_codigo_alfa. ' ' . $reg_animal_pai->tbl_animal_codigo_numerico;
                                }
                                else {
                                    $descricao_pai = '';
                                    $pai='000000000';
                                }
                            }
                        }
                        else {
                            $codigo_raca = $reg_nasc->tbl_mov_estoque_codigo_raca;
                            $codigo_cor = $reg_nasc->tbl_mov_estoque_codigo_pelagem;
                            $sexo = $reg_nasc->tbl_mov_estoque_sexo;
                            $mae = $reg_nasc->tbl_mov_estoque_codigo_mae; 

                            if ($sexo=='N') {
                                $desc_sexo = '';
                            }
                            else {
                                $desc_sexo = $sexo;
                            }
                                    
                            $descricao_raca = nasc_desc_raca($conector, $codigo_raca);

                            $descricao_cor = nasc_desc_cor($conector, $codigo_cor);

                            $reg_mae_j = nasc_mae($conector, $mae);

                            if ($reg_mae_j !== null){
                                $descricao_mae = $reg_mae_j->tbl_animal_codigo_alfa. ' ' . $reg_mae_j->tbl_animal_codigo_numerico;
                                $mae_raca = $reg_mae_j->tbl_animal_codigo_alfa. ' ' . $reg_mae_j->tbl_animal_codigo_numerico . ' - ' . $reg_mae_j->tab_descricao_raca;
                            }
                            else {
                                $descricao_mae = '';
                                $mae='';
                                $mae_raca='';
                            }

                            $codigo_alfa = '';
                            $codigo_numerico = $codigo;
                            $descricao_pai = '';
                            $pai='000000000';
                            $situacao = '';
                            $estacao_monta = 0;
                            $descricao_estacao = '';                            
                        }

                        if ($codigo_alfa=='') {
                            $codigo_edi = $codigo_numerico;
                        }
                        else {
                            $codigo_edi = $codigo_alfa .'-'. $codigo_numerico;
                        }
            
                        // Calcula dias de Gestação para Nascimento, Aborto, Natimorto - 09/01/2024
                        $itens_cob = nasc_itens_cobertura($conector, $cobertura_id, $item_cobertura);

                        $num_rows = count($itens_cob);

                        if ($num_rows!=0) {
                            foreach ($itens_cob as $reg_item) {
                                $cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
                                $item_cobertura = $reg_item->tbl_ite_cobertura_numero_item;
                                $estacao_monta_id = $reg_item->tbl_cobertura_codigo_estacao_monta;
                                $protocolo_id = $reg_item->tbl_cobertura_protocoloiatf;
                            }
                        }
                        else {
                            //$cobertura_id = 0;
                            //$item_cobertura = 0;
                            $estacao_monta_id = 0;  
                            $protocolo_id = 0;
                            $dias_gestacao = 0;   
                            $data_servico = 0;
                        }

                        if ($cobertura_id!=0) {
                            $reg_protocolo_cobertura = nasc_protocolo_cobertura($conector, $cobertura_id);

                            foreach (nasc_itens_protocolo($conector, $protocolo_id) as $reg_itens){
                                $dias = substr($reg_itens->tbl_ite_protocoloiatf_descricao, 3);
                                $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));
                            }

                            $firstDate  = new DateTime($data_servico);
                            $secondDate = new DateTime($nascimento);
                            $intvl = $firstDate->diff($secondDate);
                            $dias_gestacao = $intvl->days;
                        }

                        $array_animal = array(
                            $codigo,
                            $codigo_alfa,
                            $codigo_numerico,
                            $sexo,
                            $codigo_raca,
                            $codigo_cor,
                            $reg_nasc->tbl_mov_estoque_nascimento,
                            $codigo_fazenda,
                            $codigo_pasto,
                            $pai,
                            $mae,
                            $peso,
                            $mae_raca,
                            $desc_local,
                            $num_mov_id,
                            $desc_pasto,
                            $data_emissao,
                            $tipo_movimentacao,
                            $ocorrencia,
                            $descricao_estacao,
                            $cobertura_id, 
                            $item_cobertura 
                        );   
                                                
                        $string_array = implode('|', $array_animal);

                        echo "<tr>";

                        if ($controle_estoque == 'I') {
                            if ($situacao=='') {
                                if ($tipo_movimentacao=='A' || $tipo_movimentacao=='B') {
                                    echo "<td width='5%'></td>";
                                    echo "<td width='8%'>".$desc_tipo."</td>";
                                }
                                else if ($tipo_movimentacao=='N' && $codigo_numerico!=999999999) {
                                    echo "<td width='5%'>".$codigo_alfa."</td>";
                                    echo "<td width='8%'>".$codigo_numerico."</td>";
                                    $total_nascimento++;
                                }
                                else if ($codigo_numerico==999999999) {
                                    echo "<td width='5%'></td>";
                                    echo "<td width='8%'>Natimorto</td>";
                                    $total_natimorto++;
                                }                                
                                else {
                                    echo "<td width='5%'>".$codigo_alfa."</td>";
                                    echo "<td width='8%'>".$codigo_numerico."</td>";
                                    $total_nascimento++;
                                }
                            }
                            else {
                                if ($tipo_movimentacao=='A' || $tipo_movimentacao=='B') {
                                    echo "<td style='color: red;' width='5%'></td>";
                                    echo "<td style='color: red;' width='8%'>".$desc_tipo."</td>";
                                    }
                                else if ($tipo_movimentacao=='N' && $codigo_numerico!=999999999) {
                                    echo "<td style='color: red;' width='5%'>".$codigo_alfa."</td>";
                                    echo "<td style='color: red;' width='8%'>".$codigo_numerico."</td>";
                                    $total_nascimento++;
                                }
                                else if ($codigo_numerico==999999999) {
                                    echo "<td style='color: red;' width='5%'></td>";
                                    echo "<td style='color: red;' width='8%'>Natimorto</td>";
                                    $total_natimorto++;
                                }                                
                                else {
                                    echo "<td style='color: red;' width='5%'>".$codigo_alfa."</td>";
                                    echo "<td style='color: red;' width='8%'>".$codigo_numerico."</td>";
                                    $total_nascimento++;
                                }
                            }
                        }
                        else {
                            echo "<td width='5%'></td>";
                            echo "<td width='8%'>".$desc_tipo."</td>";
                            $total_nascimento++;
                        }

                        if ($situacao=='') {
                            echo "<td align='center' width='4%'>".$data_nascimento->format('d/m/Y')."</td>";

                            if ($dias_gestacao>=252 && $dias_gestacao<=303) {
                                if ($monta_natural=='S') {
                                    echo "<td align='center' style='color: red;' title='Monta natural' width='4%'>".$dias_gestacao."</td>";
                                }
                                else {
                                    echo "<td align='center' width='4%'>".$dias_gestacao."</td>";
                                }
                            }
                            else {
                                echo "<td align='center' style='color: red;' width='4%'>".$dias_gestacao."</td>";
                            }

                            echo "<td align='center' width='2%' >". $desc_sexo."</td>";
                            echo "<td width='3%'>".$peso."</td>";
                            echo "<td width='10%'>".$descricao_raca."</td>";
                            echo "<td width='8%'>".$descricao_cor."</td>";
                            echo "<td width='15%'>".$desc_local."</td>";
                            echo "<td width='13%'>".$desc_pasto."</td>";
                            echo "<td width='8%'>".$descricao_mae."</td>";
                            echo "<td width='8%'>".$descricao_pai."</td>";
                        }
                        else {
                            echo "<td align='center' style='color: red;' width='4%'>".$data_nascimento->format('d/m/Y')."</td>";
                            echo "<td align='center' style='color: red;' width='4%' >".$dias_gestacao."</td>";
                            echo "<td align='center' style='color: red;' width='2%' >".$desc_sexo."</td>";
                            echo "<td style='color: red;' width='3%'>".$peso."</td>";
                            echo "<td style='color: red;' width='10%'>".$descricao_raca."</td>";
                            echo "<td style='color: red;' width='8%'>".$descricao_cor."</td>";
                            echo "<td style='color: red;' width='15%'>".$desc_local."</td>";
                            echo "<td style='color: red;' width='13%'>".$desc_pasto."</td>";
                            echo "<td style='color: red;' width='8%'>".$descricao_mae."</td>";
                            echo "<td style='color: red;' width='8%'>".$descricao_pai."</td>";
                        }
                        
                        echo "<td width='12%'>";    
                        echo "<div class='btn-group'>";
     
                        if ($controle_estoque == 'I') {
                            if ($tipo_movimentacao=='A' || $tipo_movimentacao=='B' || $codigo_numerico==999999999) {
                                echo "<a class='btn' href='#'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' onClick='editar_animal(\"{$string_array}\")' ></i></a>"; 
                            }
                            else {
                                echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\")' ></i></a>"; 
                            }
                        }
                        else {
                            echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\")' ></i></a>"; 
                        }
                        echo "<a class='btn' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } // fim do if fazenda
            } // fim do foreach 
        } // fim do while
    }

    mysqli_close($conector);
    echo '</tbody>';

    if ($controle_estoque == 'I') {
        echo '
        <thead>
            <tr>
                <div class="row col-md-12" style="padding-top: 20px;">
                <div class="form-group col-md-12">
                    <p>
                        Totais - Nascimentos: '.$total_nascimento.'&nbsp; Absorção: '.$total_absorcao.'&nbsp; Aborto: '.$total_aborto.'&nbsp; Natimorto: '.$total_natimorto.'
                    </p>
                </div>
                </div>
            </tr>

            <tr>
                <th style="vertical-align: middle;text-align:center;"> Código Alfa</th>
                <th style="vertical-align: middle;"> Nº Animal</th>
                <th style="vertical-align: middle;"> Data</th>
                <th style="vertical-align: middle;text-align:center;"> Dias Gestação</th>
                <th style="vertical-align: middle;"> Sexo</th>
                <th style="vertical-align: middle;"> Peso</th>
                <th style="vertical-align: middle;"> Raça</th>
                <th style="vertical-align: middle;"> Cor</th>
                <th style="vertical-align: middle;"> Local</th>
                <th style="vertical-align: middle;"> Pasto</th>
                <th style="vertical-align: middle;"> Mãe</th>
                <th style="vertical-align: middle;"> Pai</th>
                <th style="vertical-align: middle;"><i class="icon_cogs"></i> Ações</th>
            </tr>
        </thead>';
    }
    else {
        echo '
        <thead>
            <tr>
                <th></th>
                <th style="vertical-align: middle;text-align:center;"> Data</th>
                <th> Sexo</th>
                <th> Peso</th>
                <th> Raça</th>
                <th> Cor</th>
                <th> Local</th>
                <th> Pasto</th>
                <th> Mãe</th>
                <th> Pai</th>
                <th><i class="icon_cogs"></i> Ações</th>
            </tr>
        </thead>';
    }
    echo '</table>';
    echo '</section>';
?>

    <script src="js/nascimento.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      // Tooltip delegado: cria só no hover, sem inicializar centenas de uma vez
      $('#tabela_nascimento').tooltip({ selector: '[data-toggle="tooltip"]', container: 'body' });
    });
    </script>

</body>
</html>


                
                
