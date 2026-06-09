<?php
class PesagemService {
    public function criarPesagem($json, $db) {
        $dao = new PesagemDao($db);

        $p = new Pesagem();
        $p->setId(0);
        $p->setData(date('Y-m-d'));
        $p->setLocal($json['local_id']);
        $p->setEpoca($json['epoca_id']);
        $p->setLote($json['lote']);

        $partesFiltro = explode(' | ', $json['filtro_desc']);
        $filtroApenasNomes = $partesFiltro[0];
        $p->setFiltro(str_replace('➔', '->', $filtroApenasNomes));

        $p->setIncluidoPor(!empty($json['usuario']) ? $json['usuario'] : 'App User');

        $p->setQuantidadeAnimais($json['qtd_a_pesar']);

        if (isset($json['criterios_lista']) && is_array($json['criterios_lista'])) {
            $p->setCriteriosApartacao(implode(', ', $json['criterios_lista']));
        } else {
            $p->setCriteriosApartacao('');
        }

        return $dao->salvarSomentePesagem($p);
    }

    public function updatePesagemCabecalho($dados)
    {
        $dao = new PesagemDao($dados['bd']);
        return $dao->updatePesagemCabecalho($dados);
    }

    public function salvarItem($json, $db) {
        $dao = new PesagemDao($db);
        
        $p = new Pesagem();
        $p->setId($json['pesagem_id']);
        $p->setData(date('Y-m-d'));
        $p->setLocal($json['local_id']);
        $p->setEpoca($json['epoca_id']);
        $p->setLote($json['lote']);
        
        $partesFiltro = explode(' | ', $json['filtro_desc']);
        $filtroApenasNomes = $partesFiltro[0];
        $p->setFiltro(str_replace('➔', '->', $filtroApenasNomes));
        
        $p->setIncluidoPor($json['usuario']);
        $p->setQuantidadeAnimais($json['qtd_a_pesar']);

        if (isset($json['criterios_lista']) && is_array($json['criterios_lista'])) {
            $p->setCriteriosApartacao(implode(', ', $json['criterios_lista']));
        } else {
            $p->setCriteriosApartacao('');
        }

        $i = new ItemPesagem();
        $i->setIdAnimal($json['item']['id_animal']);
        $i->setAnimal($json['item']['codigo_animal']);
        $i->setPeso($json['item']['peso']);
        $i->setUltimoPeso($json['item']['ultimo_peso'] ?? 0);
        
        $sexoOriginal = $json['item']['sexo'];
        $sexoFormatado = ($sexoOriginal == 'M' || $sexoOriginal == 'Macho') ? 'Macho' : 'Fêmea';
        $i->setSexo($sexoFormatado);

        $i->setNascimento($json['item']['nascimento']);
        $i->setRaca($json['item']['raca']);
        $i->setPelagem($json['item']['pelagem']);
        
        $maeOriginal = $json['item']['mae'];
        $i->setMae(($maeOriginal == 'Não inf.') ? ' ' : $maeOriginal);

        // OBS normal digitada pelo usuário
        $i->setObservacao($json['item']['obs'] ?? '');

        // Dados de repetido separados
        $i->setMensItemRepetido($json['item']['mens_repetido'] ?? '');
        $i->setIdPesagemItemRepetido((int)($json['item']['id_pesagem_repetido'] ?? 0));
        $i->setCriterioApartacao($json['item']['criterio_apartacao'] ?? '');
        $i->setArroba($json['item']['peso'] / 30);
        $i->setPesoMedio($json['item']['peso']);
        $i->setArrobaMedio($json['item']['peso'] / 30);

        return $dao->salvarPesagemEItem($p, $i);
    }
}