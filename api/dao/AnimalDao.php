<?php
include_once __DIR__ . "/../../conecta_mysql_credenciais.inc";
class AnimalDao{
    
    private $con;

    public function __construct($banco){
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
    }

    private function fillField($animal){
        $obj = new Animal();

        $obj->setId($animal->tbl_animal_codigo_id);
        $obj->setAlfa($animal->tbl_animal_codigo_alfa);
        $obj->setNumerico($animal->tbl_animal_codigo_numerico);
        $obj->setReprodutor($animal->tbl_animal_reprodutor);
        $obj->setNome($animal->tbl_animal_nome);
        $obj->setNascimento($animal->tbl_animal_data_nascimento);
        $obj->setSexo($animal->tbl_animal_sexo);
        $obj->setGrauSangue($animal->tbl_animal_grau_sangue);
        $obj->setIdMae($animal->tbl_animal_codigo_mae);
        $obj->setNomeMae($animal->tbl_animal_nome_mae);
        $obj->setIdPai($animal->tbl_animal_codigo_pai);
        $obj->setNomePai($animal->tbl_animal_nome_pai);
        $obj->setPrimeiroPeso($animal->tbl_animal_primeiro_peso);
        $obj->setLotePrimeiroPeso($animal->tbl_animal_lote_primeiro_peso);
        $obj->setDataPrimeiroPeso($animal->tbl_animal_data_primeiro_peso);
        $obj->setPesoDesmama($animal->tbl_animal_peso_desmama);
        $obj->setLoteDesmama($animal->tbl_animal_lote_desmama);
        $obj->setDataDesmama($animal->tbl_animal_data_desmama);
        $obj->setUltimoPeso($animal->tbl_animal_ultimo_peso);
        $obj->setLoteUltimo($animal->tbl_animal_lote_ultimo);
        $obj->setDataUltimo($animal->tbl_animal_data_ultimo);

        $obj->getRaca()->setId($animal->tab_codigo_raca);
        $obj->getRaca()->setDescricao($animal->tab_descricao_raca);
        $obj->getRaca()->setIncluidoEm($animal->tab_raca_incluido_em);
        $obj->getRaca()->setIncluidoPor($animal->tab_raca_incluido_por);
        $obj->getRaca()->setAlteradoEm($animal->tab_raca_alterado_em);
        $obj->getRaca()->setAlteradoPor($animal->tab_raca_alterado_por);
        $obj->getRaca()->setLixeira($animal->tab_registro_lixeira_raca);
        $obj->getRaca()->setLixeiraEm($animal->tab_raca_lixeira_em);
        $obj->getRaca()->setLixeiraPor($animal->tab_raca_lixeira_por);

        $obj->getFazenda()->setId($animal->tbl_pessoa_id);
        $obj->getFazenda()->setClasse($animal->tbl_pessoa_classe);
        $obj->getFazenda()->setCpfCnpj($animal->tbl_pessoa_cpf_cnpj);
        $obj->getFazenda()->setTipo($animal->tbl_pessoa_tipo_pessoa);
        $obj->getFazenda()->setInscEstadual($animal->tbl_pessoa_insc_estadual);
        $obj->getFazenda()->setInscMunicipal($animal->tbl_pessoa_insc_municipal);
        $obj->getFazenda()->setNome($animal->tbl_pessoa_nome);
        $obj->getFazenda()->setContato($animal->tbl_pessoa_contato);
        $obj->getFazenda()->setCargoContato($animal->tbl_pessoa_cargo_contato);
        $obj->getFazenda()->setDdd($animal->tbl_pessoa_ddd);
        $obj->getFazenda()->setTelefone($animal->tbl_pessoa_telefone);
        $obj->getFazenda()->setEmail($animal->tbl_pessoa_email);
        $obj->getFazenda()->getEndereco()->setCep($animal->tbl_pessoa_cep);
        $obj->getFazenda()->getEndereco()->setEndereco($animal->tbl_pessoa_endereco);
        $obj->getFazenda()->getEndereco()->setNumero($animal->tbl_pessoa_numero);
        $obj->getFazenda()->getEndereco()->setComplemento($animal->tbl_pessoa_complemento);
        $obj->getFazenda()->getEndereco()->setBairro($animal->tbl_pessoa_bairro);
        $obj->getFazenda()->getEndereco()->setCidade($animal->tbl_pessoa_municipio);
        $obj->getFazenda()->getEndereco()->setEstado($animal->tbl_pessoa_estado);
        $obj->getFazenda()->setIncluidoEm($animal->tbl_pessoa_incluido_em);
        $obj->getFazenda()->setIncluidoPor($animal->tbl_pessoa_incluido_por);
        $obj->getFazenda()->setAlteradoEm($animal->tbl_pessoa_alterado_em);
        $obj->getFazenda()->setAlteradoPor($animal->tbl_pessoa_alterado_por);
        $obj->getFazenda()->setLixeira($animal->tbl_pessoa_lixeira);
        $obj->getFazenda()->setLixeiraEm($animal->tbl_pessoa_lixeira_em);
        $obj->getFazenda()->setLixeiraPor($animal->tbl_pessoa_lixeira_por);
        $obj->getFazenda()->setObservacao($animal->tbl_pessoa_observacao);
        $obj->getFazenda()->setAtivo($animal->tbl_pessoa_ativo);

        $obj->getPelagem()->setId($animal->tab_codigo_pelagem);
        $obj->getPelagem()->setDescricao($animal->tab_descricao_pelagem);
        $obj->getPelagem()->setIncluidoEm($animal->tab_pelagem_incluido_em);
        $obj->getPelagem()->setIncluidoPor($animal->tab_pelagem_incluido_por);
        $obj->getPelagem()->setAlteradoEm($animal->tab_pelagem_alterado_em);
        $obj->getPelagem()->setAlteradoPor($animal->tab_pelagem_alterado_por);
        $obj->getPelagem()->setLixeira($animal->tab_registro_lixeira_pelagem);
        $obj->getPelagem()->setLixeiraEm($animal->tab_pelagem_lixeira_em);
        $obj->getPelagem()->setLixeiraPor($animal->tab_pelagem_lixeira_por);

        $obj->setOrigem($animal->tbl_animal_codigo_origem);
        $obj->setMarca($animal->tbl_animal_marca);
        $obj->setRegistroRen($animal->tbl_animal_registro_ren);
        $obj->setRegistroRgd($animal->tbl_animal_registro_rgd);
        $obj->setRegistroSisbov($animal->tbl_animal_registro_sisbov);
        $obj->setCertificadora($animal->tbl_animal_certificadora);
        $obj->setObservacao($animal->tbl_animal_observacao);
        $obj->setAtivo($animal->tbl_animal_ativo);
        $obj->setIncluidoEm($animal->tbl_animal_incluido_em);
        $obj->setIncluidoPor($animal->tbl_animal_incluido_por);
        $obj->setAlteradoEm($animal->tbl_animal_alterado_em);
        $obj->setAlteradoPor($animal->tbl_animal_alterado_por);
        $obj->setLixeira($animal->tbl_animal_lixeira);
        $obj->setLixeiraEm($animal->tbl_animal_lixeira_em);
        $obj->setLixeiraPor($animal->tbl_animal_lixeira_por);
        $obj->setBaixadoEm($animal->tbl_animal_baixado_em);
        $obj->setBaixadoPor($animal->tbl_animal_baixado_por);
        $obj->setSituacao($animal->tbl_animal_situacao);
        $obj->setOrigemAnterior($animal->tbl_animal_codigo_origem_anterior);
        $obj->setFazendaAnterior($animal->tbl_animal_codigo_fazenda_anterior);
        $obj->setEmEstacaoMonta($animal->tbl_animal_em_estacao_monta);
        $obj->setAguardandoDiagnostico($animal->tbl_animal_aguardando_diagnostico);
        $obj->setPrenhe($animal->tbl_animal_prenhe);
        $obj->setParida($animal->tbl_animal_parida);
        $obj->setSolteira($animal->tbl_animal_solteira);
        $obj->setCoberturas($animal->tbl_animal_numero_coberturas);
        $obj->setPartos($animal->tbl_animal_numero_partos);
        $obj->setAbortos($animal->tbl_animal_numero_abortos);
        $obj->setDescarteReproducao($animal->tbl_animal_descarte_reproducao);
        $obj->setDescarteEm($animal->tbl_animal_descarte_em);
        $obj->setDescartePor($animal->tbl_animal_descarte_por);
        $obj->setSelecionadaReproducao($animal->tbl_animal_selecioanada_reproducao);

        return $obj;
    }

    private function fillFields($aObj){
        $a = [];

        foreach($aObj as $animal){
            $obj = new Animal();

            $obj->setId($animal->tbl_animal_codigo_id);
            $obj->setAlfa($animal->tbl_animal_codigo_alfa);
            $obj->setNumerico($animal->tbl_animal_codigo_numerico);
            $obj->setReprodutor($animal->tbl_animal_reprodutor);
            $obj->setNome($animal->tbl_animal_nome);
            $obj->setNascimento($animal->tbl_animal_data_nascimento);
            $obj->setSexo($animal->tbl_animal_sexo);
            $obj->setGrauSangue($animal->tbl_animal_grau_sangue);
            $obj->setIdMae($animal->tbl_animal_codigo_mae);
            $obj->setNomeMae($animal->tbl_animal_nome_mae);
            $obj->setIdPai($animal->tbl_animal_codigo_pai);
            $obj->setNomePai($animal->tbl_animal_nome_pai);
            $obj->setPrimeiroPeso($animal->tbl_animal_primeiro_peso);
            $obj->setLotePrimeiroPeso($animal->tbl_animal_lote_primeiro_peso);
            $obj->setDataPrimeiroPeso($animal->tbl_animal_data_primeiro_peso);
            $obj->setPesoDesmama($animal->tbl_animal_peso_desmama);
            $obj->setLoteDesmama($animal->tbl_animal_lote_desmama);
            $obj->setDataDesmama($animal->tbl_animal_data_desmama);
            $obj->setUltimoPeso($animal->tbl_animal_ultimo_peso);
            $obj->setLoteUltimo($animal->tbl_animal_lote_ultimo);
            $obj->setDataUltimo($animal->tbl_animal_data_ultimo);

            $obj->getRaca()->setId($animal->tab_codigo_raca);
            $obj->getRaca()->setDescricao($animal->tab_descricao_raca);
            $obj->getRaca()->setIncluidoEm($animal->tab_raca_incluido_em);
            $obj->getRaca()->setIncluidoPor($animal->tab_raca_incluido_por);
            $obj->getRaca()->setAlteradoEm($animal->tab_raca_alterado_em);
            $obj->getRaca()->setAlteradoPor($animal->tab_raca_alterado_por);
            $obj->getRaca()->setLixeira($animal->tab_registro_lixeira_raca);
            $obj->getRaca()->setLixeiraEm($animal->tab_raca_lixeira_em);
            $obj->getRaca()->setLixeiraPor($animal->tab_raca_lixeira_por);

            $obj->getFazenda()->setId($animal->tbl_pessoa_id);
            $obj->getFazenda()->setClasse($animal->tbl_pessoa_classe);
            $obj->getFazenda()->setCpfCnpj($animal->tbl_pessoa_cpf_cnpj);
            $obj->getFazenda()->setTipo($animal->tbl_pessoa_tipo_pessoa);
            $obj->getFazenda()->setInscEstadual($animal->tbl_pessoa_insc_estadual);
            $obj->getFazenda()->setInscMunicipal($animal->tbl_pessoa_insc_municipal);
            $obj->getFazenda()->setNome($animal->tbl_pessoa_nome);
            $obj->getFazenda()->setContato($animal->tbl_pessoa_contato);
            $obj->getFazenda()->setCargoContato($animal->tbl_pessoa_cargo_contato);
            $obj->getFazenda()->setDdd($animal->tbl_pessoa_ddd);
            $obj->getFazenda()->setTelefone($animal->tbl_pessoa_telefone);
            $obj->getFazenda()->setEmail($animal->tbl_pessoa_email);
            $obj->getFazenda()->getEndereco()->setCep($animal->tbl_pessoa_cep);
            $obj->getFazenda()->getEndereco()->setEndereco($animal->tbl_pessoa_endereco);
            $obj->getFazenda()->getEndereco()->setNumero($animal->tbl_pessoa_numero);
            $obj->getFazenda()->getEndereco()->setComplemento($animal->tbl_pessoa_complemento);
            $obj->getFazenda()->getEndereco()->setBairro($animal->tbl_pessoa_bairro);
            $obj->getFazenda()->getEndereco()->setCidade($animal->tbl_pessoa_municipio);
            $obj->getFazenda()->getEndereco()->setEstado($animal->tbl_pessoa_estado);
            $obj->getFazenda()->setIncluidoEm($animal->tbl_pessoa_incluido_em);
            $obj->getFazenda()->setIncluidoPor($animal->tbl_pessoa_incluido_por);
            $obj->getFazenda()->setAlteradoEm($animal->tbl_pessoa_alterado_em);
            $obj->getFazenda()->setAlteradoPor($animal->tbl_pessoa_alterado_por);
            $obj->getFazenda()->setLixeira($animal->tbl_pessoa_lixeira);
            $obj->getFazenda()->setLixeiraEm($animal->tbl_pessoa_lixeira_em);
            $obj->getFazenda()->setLixeiraPor($animal->tbl_pessoa_lixeira_por);
            $obj->getFazenda()->setObservacao($animal->tbl_pessoa_observacao);
            $obj->getFazenda()->setAtivo($animal->tbl_pessoa_ativo);

            $obj->getPelagem()->setId($animal->tab_codigo_pelagem);
            $obj->getPelagem()->setDescricao($animal->tab_descricao_pelagem);
            $obj->getPelagem()->setIncluidoEm($animal->tab_pelagem_incluido_em);
            $obj->getPelagem()->setIncluidoPor($animal->tab_pelagem_incluido_por);
            $obj->getPelagem()->setAlteradoEm($animal->tab_pelagem_alterado_em);
            $obj->getPelagem()->setAlteradoPor($animal->tab_pelagem_alterado_por);
            $obj->getPelagem()->setLixeira($animal->tab_registro_lixeira_pelagem);
            $obj->getPelagem()->setLixeiraEm($animal->tab_pelagem_lixeira_em);
            $obj->getPelagem()->setLixeiraPor($animal->tab_pelagem_lixeira_por);

            $obj->setOrigem($animal->tbl_animal_codigo_origem);
            $obj->setMarca($animal->tbl_animal_marca);
            $obj->setRegistroRen($animal->tbl_animal_registro_ren);
            $obj->setRegistroRgd($animal->tbl_animal_registro_rgd);
            $obj->setRegistroSisbov($animal->tbl_animal_registro_sisbov);
            $obj->setCertificadora($animal->tbl_animal_certificadora);
            $obj->setObservacao($animal->tbl_animal_observacao);
            $obj->setAtivo($animal->tbl_animal_ativo);
            $obj->setIncluidoEm($animal->tbl_animal_incluido_em);
            $obj->setIncluidoPor($animal->tbl_animal_incluido_por);
            $obj->setAlteradoEm($animal->tbl_animal_alterado_em);
            $obj->setAlteradoPor($animal->tbl_animal_alterado_por);
            $obj->setLixeira($animal->tbl_animal_lixeira);
            $obj->setLixeiraEm($animal->tbl_animal_lixeira_em);
            $obj->setLixeiraPor($animal->tbl_animal_lixeira_por);
            $obj->setBaixadoEm($animal->tbl_animal_baixado_em);
            $obj->setBaixadoPor($animal->tbl_animal_baixado_por);
            $obj->setSituacao($animal->tbl_animal_situacao);
            $obj->setOrigemAnterior($animal->tbl_animal_codigo_origem_anterior);
            $obj->setFazendaAnterior($animal->tbl_animal_codigo_fazenda_anterior);
            $obj->setEmEstacaoMonta($animal->tbl_animal_em_estacao_monta);
            $obj->setAguardandoDiagnostico($animal->tbl_animal_aguardando_diagnostico);
            $obj->setPrenhe($animal->tbl_animal_prenhe);
            $obj->setParida($animal->tbl_animal_parida);
            $obj->setSolteira($animal->tbl_animal_solteira);
            $obj->setCoberturas($animal->tbl_animal_numero_coberturas);
            $obj->setPartos($animal->tbl_animal_numero_partos);
            $obj->setAbortos($animal->tbl_animal_numero_abortos);
            $obj->setDescarteReproducao($animal->tbl_animal_descarte_reproducao);
            $obj->setDescarteEm($animal->tbl_animal_descarte_em);
            $obj->setDescartePor($animal->tbl_animal_descarte_por);
            $obj->setSelecionadaReproducao($animal->tbl_animal_selecioanada_reproducao);

            array_push($a, $obj);
        }

        return $a;
    }

    // VERSÃO AJUSTADA PARA BUSCA GLOBAL (MÃES) OU POR FAZENDA (PESAGEM)
    public function getAnimalByIdLike($id, $local){
        $a = [];
        $sql = "SELECT a.*, 
                mae.tbl_animal_codigo_alfa as mae_alfa, 
                mae.tbl_animal_codigo_numerico as mae_num,
                r.*, pel.*, f.*
                FROM tbl_animais a
                LEFT JOIN tbl_animais mae ON a.tbl_animal_codigo_mae = mae.tbl_animal_codigo_id
                LEFT JOIN tabela_racas r ON a.tbl_animal_codigo_raca = r.tab_codigo_raca
                LEFT JOIN tabela_pelagens pel ON a.tbl_animal_codigo_pelagem = pel.tab_codigo_pelagem
                LEFT JOIN tbl_pessoa f ON a.tbl_animal_codigo_fazenda = f.tbl_pessoa_id
                WHERE a.tbl_animal_lixeira = 0 
                AND a.tbl_animal_ativo = 'S' ";

        // SE LOCAL > 0: Comportamento padrão (Pesagem de Itens)
        if($local > 0){
            $sql .= " AND a.tbl_animal_codigo_fazenda = '$local' ";
        } 
        // SE LOCAL == 0: Busca Global para o Modal Mãe (Fêmeas > 12 meses)
        else {
            $sql .= " AND a.tbl_animal_sexo = 'F' 
                      AND (PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m'), DATE_FORMAT(a.tbl_animal_data_nascimento, '%Y%m')) > 12) ";
        }

        $sql .= " AND (a.tbl_animal_codigo_alfa LIKE '%".$id."%' OR a.tbl_animal_codigo_numerico LIKE '%".$id."%')
                  ORDER BY a.tbl_animal_codigo_numerico ASC LIMIT 10";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        
        if ($r) {
            while($dados = mysqli_fetch_object($r)){
                $obj = $this->fillField($dados);
                if (!empty($dados->mae_num)) {
                    $prefixo = !empty($dados->mae_alfa) ? $dados->mae_alfa . "-" : "";
                    $numeroLimpo = ltrim($dados->mae_num, '0');
                    if ($numeroLimpo === '') $numeroLimpo = '0';
                    $obj->setNomeMae($prefixo . $numeroLimpo); 
                } else {
                    $obj->setNomeMae("Não inf.");
                }
                array_push($a, $obj);
            }
        }
        return $a; 
    }      

    // ESSA É PARA OS DETALHES DO ANIMAL (Quando você clica no animal escolhido)
    public function getAnimalById($id, $local){
        $sql = "SELECT a.*, 
                mae.tbl_animal_codigo_alfa as mae_alfa, 
                mae.tbl_animal_codigo_numerico as mae_num,
                r.*, pel.*, f.*
                FROM tbl_animais a
                LEFT JOIN tbl_animais mae ON a.tbl_animal_codigo_mae = mae.tbl_animal_codigo_id
                LEFT JOIN tabela_racas r ON a.tbl_animal_codigo_raca = r.tab_codigo_raca
                LEFT JOIN tabela_pelagens pel ON a.tbl_animal_codigo_pelagem = pel.tab_codigo_pelagem
                LEFT JOIN tbl_pessoa f ON a.tbl_animal_codigo_fazenda = f.tbl_pessoa_id
                WHERE a.tbl_animal_lixeira = 0 
                AND a.tbl_animal_ativo = 'S' 
                AND a.tbl_animal_codigo_fazenda = '$local' 
                AND a.tbl_animal_codigo_id = $id";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        if (!$r) return null;

        $dados = mysqli_fetch_object($r);
        if (!$dados) return null;

        $obj = $this->fillField($dados);

        if (!empty($dados->mae_num)) {
            $prefixo = !empty($dados->mae_alfa) ? $dados->mae_alfa . "-" : "";
            $numeroLimpo = ltrim($dados->mae_num, '0');
            if ($numeroLimpo === '') $numeroLimpo = '0';
            $obj->setNomeMae($prefixo . $numeroLimpo); 
        } else {
            $obj->setNomeMae("Não inf.");
        }

        return $obj;
    }

    public function updateAnimalMorte($animal, $motivoMorte, $data, $obs, $user){
        $sql = "UPDATE tbl_animais SET
                tbl_animal_ativo = 'N',
                tbl_animal_baixado_em = '{$data}',
                tbl_animal_baixado_por = '{$user->getNome()}',
                tbl_animal_observacao = 'Motivo da morte: {$motivoMorte->getDescricao()}. Obs.: {$obs}',
                tbl_animal_codigo_origem_anterior = '{$animal->getOrigemAnterior()}',
                tbl_animal_codigo_fazenda_anterior = '{$animal->getFazendaAnterior()}',
                tbl_animal_situacao = 'M'
                WHERE tbl_animal_codigo_id = {$animal->getId()}";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);

        if(mysqli_error($this->con)){
            return[
                "error" => true,
                "message" => "Ocorreu um erro ao atualizar o cadastro do animal!"
            ];
        }

        return[
            "error" => false,
            "message" => ""
        ];
    }

    public function getLoteAbertoPorAnimal($idAnimal) {
        $sql = "SELECT 
                    p.tbl_pesagem_id,
                    p.tbl_pesagem_lote
                FROM tbl_item_pesagem i
                JOIN tbl_pesagem p ON i.tbl_ite_pesagem_numero_id = p.tbl_pesagem_id
                WHERE i.tbl_ite_pesagem_codigo_id_animal = $idAnimal
                  AND p.tbl_pesagem_finalizada = 'N'
                  AND IFNULL(p.tbl_pesagem_lixeira, 0) = 0
                ORDER BY p.tbl_pesagem_id DESC
                LIMIT 1";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);

        if ($r && mysqli_num_rows($r) > 0) {
            $dados = mysqli_fetch_assoc($r);
            return [
                "pesagem_id" => (int)$dados['tbl_pesagem_id'],
                "lote" => $dados['tbl_pesagem_lote']
            ];
        }

        return null;
    }

    public function getFilhosAtivosPorMae($idMae) {
        // SQL com JOIN para trazer o nome da fazenda (tbl_pessoa)
        $sql = "SELECT a.tbl_animal_codigo_id, a.tbl_animal_codigo_alfa, a.tbl_animal_codigo_numerico, 
                       a.tbl_animal_data_nascimento, a.tbl_animal_sexo, 
                       r.tab_descricao_raca as raca_nome, 
                       p.tab_descricao_pelagem as pelagem_nome,
                       f.tbl_pessoa_nome as local_nome 
                FROM tbl_animais a
                LEFT JOIN tabela_racas r ON a.tbl_animal_codigo_raca = r.tab_codigo_raca
                LEFT JOIN tabela_pelagens p ON a.tbl_animal_codigo_pelagem = p.tab_codigo_pelagem
                LEFT JOIN tbl_pessoa f ON a.tbl_animal_codigo_fazenda = f.tbl_pessoa_id
                WHERE a.tbl_animal_codigo_mae = '$idMae' 
                AND a.tbl_animal_ativo = 'S'
                AND a.tbl_animal_lixeira = 0
                ORDER BY tbl_animal_codigo_id DESC";
                
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        $filhos = [];
        if($r){
            while($row = mysqli_fetch_assoc($r)){
                $filhos[] = $row;
            }
        }
        return $filhos;
    }

    // BUSCA GLOBAL PELO ID (Usado para carregar os detalhes da Mãe no Modal)
    public function getAnimalByIdSemLocal($id){
        $sql = "SELECT a.*, 
                mae.tbl_animal_codigo_alfa as mae_alfa, 
                mae.tbl_animal_codigo_numerico as mae_num,
                r.*, pel.*, f.*
                FROM tbl_animais a
                LEFT JOIN tbl_animais mae ON a.tbl_animal_codigo_mae = mae.tbl_animal_codigo_id
                LEFT JOIN tabela_racas r ON a.tbl_animal_codigo_raca = r.tab_codigo_raca
                LEFT JOIN tabela_pelagens pel ON a.tbl_animal_codigo_pelagem = pel.tab_codigo_pelagem
                LEFT JOIN tbl_pessoa f ON a.tbl_animal_codigo_fazenda = f.tbl_pessoa_id
                WHERE a.tbl_animal_lixeira = 0 
                AND a.tbl_animal_ativo = 'S' 
                AND a.tbl_animal_codigo_id = $id";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        
        if (!$r) return null;

        $dados = mysqli_fetch_object($r);
        if (!$dados) return null;

        // Reutiliza o seu fillField para transformar o resultado em Objeto Animal
        $obj = $this->fillField($dados);

        // Ajusta o nome da mãe do objeto (brinco formatado)
        if (!empty($dados->mae_num)) {
            $prefixo = !empty($dados->mae_alfa) ? $dados->mae_alfa . "-" : "";
            $numeroLimpo = ltrim($dados->mae_num, '0');
            if ($numeroLimpo === '') $numeroLimpo = '0';
            $obj->setNomeMae($prefixo . $numeroLimpo); 
        } else {
            $obj->setNomeMae("Não inf.");
        }

        return $obj;
    }

    public function getBezerroNaoDesmamadoEmPesagemAberta($animalMae) {
        if (!$animalMae) return null;

        $idMae = (int)$animalMae->getId();

        if ($idMae <= 0) {
            return null;
        }

        $sql = "SELECT 
                    f.tbl_animal_codigo_id,
                    f.tbl_animal_codigo_alfa,
                    f.tbl_animal_codigo_numerico,
                    i.tbl_ite_pesagem_criterio_apartacao AS criterio
                FROM tbl_animais f
                INNER JOIN tbl_item_pesagem i
                    ON i.tbl_ite_pesagem_codigo_id_animal = f.tbl_animal_codigo_id
                INNER JOIN tbl_pesagem p
                    ON p.tbl_pesagem_id = i.tbl_ite_pesagem_numero_id
                WHERE f.tbl_animal_codigo_mae = $idMae
                  AND f.tbl_animal_ativo = 'S'
                  AND f.tbl_animal_lixeira = 0
                  AND TIMESTAMPDIFF(MONTH, f.tbl_animal_data_nascimento, CURDATE()) <= 7
                  AND (
                        f.tbl_animal_peso_desmama IS NULL
                        OR f.tbl_animal_peso_desmama = ''
                        OR f.tbl_animal_peso_desmama = 0
                      )
                  AND p.tbl_pesagem_finalizada = 'N'
                  AND IFNULL(p.tbl_pesagem_lixeira, 0) = 0
                  AND IFNULL(i.tbl_ite_pesagem_criterio_apartacao, '') <> ''
                ORDER BY p.tbl_pesagem_id DESC, i.tbl_ite_pesagem_numero_item DESC
                LIMIT 1";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);

        if ($r && mysqli_num_rows($r) > 0) {
            $dados = mysqli_fetch_assoc($r);

            $codigo = '';
            if ($dados['tbl_animal_codigo_alfa'] != '') {
                $codigo = $dados['tbl_animal_codigo_alfa'] . '-';
            }

            $numeroLimpo = ltrim($dados['tbl_animal_codigo_numerico'], '0');
            if ($numeroLimpo === '') $numeroLimpo = '0';

            $codigo .= $numeroLimpo;

            return [
                "codigo" => $codigo,
                "criterio" => $dados['criterio']
            ];
        }

        return null;
    }
    public function getCriterioApartacaoMaeBezerroNaoDesmamado($animal) {
        if (!$animal) return null;

        $idMae = $animal->getIdMae();

        if ($idMae == null || $idMae == '' || $idMae == 0) {
            return null;
        }

        $nascimento = $animal->getNascimento();

        if ($nascimento == null || $nascimento == '') {
            return null;
        }

        try {
            $dataNascimento = new DateTime($nascimento);
            $hoje = new DateTime();

            $idadeMeses = (($hoje->format('Y') - $dataNascimento->format('Y')) * 12)
                + ($hoje->format('m') - $dataNascimento->format('m'));

            if ($hoje->format('d') < $dataNascimento->format('d')) {
                $idadeMeses--;
            }

            if ($idadeMeses > 7) {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }

        $pesoDesmama = $animal->getPesoDesmama();

        if ($pesoDesmama !== null && $pesoDesmama !== '' && floatval($pesoDesmama) > 0) {
            return null;
        }

        $idMae = (int)$idMae;

        $sql = "SELECT 
                    i.tbl_ite_pesagem_criterio_apartacao AS criterio
                FROM tbl_item_pesagem i
                INNER JOIN tbl_pesagem p 
                    ON p.tbl_pesagem_id = i.tbl_ite_pesagem_numero_id
                WHERE i.tbl_ite_pesagem_codigo_id_animal = $idMae
                  AND p.tbl_pesagem_finalizada = 'N'
                  AND IFNULL(p.tbl_pesagem_lixeira, 0) = 0
                  AND IFNULL(i.tbl_ite_pesagem_criterio_apartacao, '') <> ''
                ORDER BY p.tbl_pesagem_id DESC, i.tbl_ite_pesagem_numero_item DESC
                LIMIT 1";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);

        if ($r && mysqli_num_rows($r) > 0) {
            $dados = mysqli_fetch_assoc($r);
            return $dados['criterio'];
        }

        return null;
    }    
}