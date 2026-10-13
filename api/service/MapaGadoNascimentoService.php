<?php
/**
 * Mapa de Gado (tela do pasto) — botão NASCIMENTO, chamado pelo aplicativo
 * (só online). Mesmas regras de form_mapa_gados_movimentacao.php do
 * sistema web.
 */
class MapaGadoNascimentoService{

    /**
     * Opções dos selects da tela: "Pai Nº" (grupos SEMEM e ANIMAIS),
     * "Raça" e "Pelagem".
     */
    public function listas($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        if ($bd === '') {
            return ["success" => false, "message" => "Informe o bd."];
        }
        $dao = new NascimentoDao($bd);
        return [
            "success"  => true,
            "semens"   => $dao->listarSemens(),
            "machos"   => $dao->listarMachos(),
            "racas"    => $dao->listarRacas(),
            "pelagens" => $dao->listarPelagens(),
        ];
    }

    /** Dias entre duas datas (DateTime::diff->days, como no web). Data
     *  vazia vale "agora", que é o que o PHP faz com new DateTime(''). */
    private function intervalo($de, $ate){
        $a = new DateTime((string) $de);
        $b = new DateTime((string) $ate);
        return $a->diff($b);
    }

    /**
     * Parâmetros de nascimento da fazenda — ler_parametro_nascimento.php:
     * código alfa e próximo número do animal, a estação de monta e se há
     * prenhes atrasadas (ícone de nascimentos previstos). Vai junto o
     * grupo de acesso do usuário, que o web tem na sessão.
     */
    public function parametros($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        $local = (int) ($dados['fazenda'] ?? 0);
        if ($bd === '' || $local <= 0) {
            return ["success" => false, "message" => "Informe bd e fazenda."];
        }
        $dao = new NascimentoDao($bd);

        // sem parâmetro cadastrado o web devolve 0 nos dois campos
        $alfa = '0';
        $numerico = '0';
        $par = $dao->parametroNascimento($local);
        if ($par) {
            $alfa = (string) $par['tbl_par_codigo_alfa'];
            $numerico = (string) $par['tbl_par_codigo_numerico'];
        }

        $dataHoje = date('Y-m-d');
        $prenhes = $dao->prenhesSemNascimento($local);
        $idEstacao = $prenhes ? $prenhes[0]['tbl_cobertura_codigo_estacao_monta'] : 0;

        $estacao = $idEstacao != 0 ? $dao->estacaoPorId($idEstacao)
                                   : $dao->estacaoVigente($local, $dataHoje);
        if ($estacao) {
            $idEstacao = $estacao['tbl_par_estacao_id'];
            $descEstacao = (string) $estacao['tbl_par_estacao_nome'];
        } else {
            $idEstacao = 0;
            $descEstacao = '';
        }

        // Tem prenhe ainda sem nascimento com menos de 303 dias de gestação?
        $temAtrasados = 'S';
        if ($idEstacao != 0) {
            $dataServico = '';
            foreach ($dao->prenhesSemNascimento($local, $idEstacao) as $item) {
                $dataProtocolo = $dao->dataDoProtocoloDaCobertura($item['tbl_cobertura_id']);
                foreach ($dao->descricoesDoProtocoloIatf($item['tbl_cobertura_protocoloiatf']) as $descricao) {
                    $dias = substr($descricao, 3);
                    $dataServico = date("Y-m-d", strtotime($dataProtocolo . "+{$dias} days"));
                }
                if ($this->intervalo($dataServico, $dataHoje)->days < 303) {
                    $temAtrasados = 'N';
                }
            }
        }

        return [
            "success"               => true,
            "codigo_alfa"           => $alfa,
            "codigo_numerico"       => $numerico,
            "estacao_id"            => (string) $idEstacao,
            "estacao"               => $descEstacao,
            "nascimentos_previstos" => $temAtrasados,
            "grupo_usuario"         => $dao->grupoDoUsuario($dados['usuario_id'] ?? 0),
        ];
    }

    /**
     * Fêmea escolhida no "Nº Mãe" / "Nº Fêmea" —
     * ler_animal_femea_nascimento.php. Devolve em "php" os mesmos valores,
     * nas mesmas posições (0 a 23), que o web devolve separados por "<|>",
     * para o aplicativo aplicar a mesma árvore de decisão de
     * ler_animal_mae() (js/mapa_gados.js):
     *
     *    0 id da fêmea            12 nome da estação
     *    1 diagnóstico            13 nascimento do último filho
     *    2 raça da fêmea          14 última estação da fazenda
     *    3 pai                    15 controle (C estação, M monta)
     *    4 cobertura              16 data do serviço
     *    5 item da cobertura      17 meses desde o último filho
     *    6 data do serviço        18 dias desde o último aborto
     *    7 dias calculados        19 dias desde o último filho
     *    8 estação                20 item da cobertura já nascido
     *    9 filhos na estação      21 meses desde o último aborto
     *   10 aborto na estação      22 meses do diagnóstico negativo
     *   11 raça do pai            23 data do diagnóstico negativo
     */
    public function mae($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        $idAnimal = trim((string) ($dados['codigo'] ?? ''));
        $local = (int) ($dados['fazenda'] ?? 0);
        $dataNascimento = DateTime::createFromFormat('!Y-m-d', (string) ($dados['data'] ?? ''));
        if ($bd === '' || $idAnimal === '' || $local <= 0 || !$dataNascimento) {
            return ["success" => false, "message" => "Informe bd, fazenda, codigo e data."];
        }
        $dataNascimento = $dataNascimento->format('Y-m-d');
        $dao = new NascimentoDao($bd);

        $codigoNumerico = substr($idAnimal, -9);
        if (strlen($idAnimal) != 9) {
            $partes = explode("-", $idAnimal);
            $codigoAlfa = $partes[0];
        } else {
            $codigoAlfa = '';
        }

        $ultima = $dao->ultimaEstacaoDaFazenda($local);
        $idUltimaEstacao = $ultima ? $ultima['tbl_par_estacao_id'] : 0;

        $animal = $dao->animalAtivoPorCodigo($codigoAlfa, $codigoNumerico);
        if (!$animal) {
            return ["success" => true, "encontrado" => false,
                    "message" => "Animal não cadastrado com esse código."];
        }
        $codigoIdAnimal = $animal['tbl_animal_codigo_id'];
        $descRaca = $dao->descricaoDaRaca($animal['tbl_animal_codigo_raca']);

        $controle = '';
        $diasCalculadosAborto = 0;
        $mesesCalculadosAborto = 0;
        $dataServico = '';
        $diasCalculados = 0;
        $dataDiagnosticoNegativo = 0;

        $item = $dao->ultimaCoberturaDaFemea($codigoIdAnimal);
        if ($item) {
            $coberturaId = $item['tbl_ite_cobertura_numero_id'];
            $itemCobertura = $item['tbl_ite_cobertura_numero_item'];
            $itemCoberturaNascido = $item['tbl_ite_cobertura_nascido'];
            $abortoEstacao = $item['tbl_ite_cobertura_aborto_natimorto'];
            $diagnostico = $item['tbl_ite_cobertura_resultado_diagnostico'];
            $controle = $item['tbl_cobertura_controle'];

            // Teve aborto ou natimorto nos últimos 90 dias?
            $aborto = $dao->dataDoUltimoAbortoNatimorto($codigoIdAnimal);
            if ($aborto) {
                $diff = $this->intervalo($aborto['tbl_mov_estoque_nascimento'], $dataNascimento);
                $diasCalculadosAborto = $diff->days;
                $mesesCalculadosAborto = $diff->m + ($diff->y * 12);
            }

            if ($controle == 'M') {
                $estacaoMontaId = 0;
                $descEstacao = '';
                $codigoPai = '000000000';
                $racaPai = '';
                $protocoloId = 0;
                if ($diagnostico != 'P' && $diagnostico != 'N') {
                    $diagnostico = ''; // sem data da prenhes digitada
                }
                if ($diagnostico == 'N') {
                    $dataDiagnosticoNegativo = $item['tbl_ite_cobertura_negativo_em'];
                }
                $dataServico = $item['tbl_ite_cobertura_data_prenhes'];
            } else {
                $estacaoMontaId = $item['tbl_cobertura_codigo_estacao_monta'];
                $codigoPai = $item['tbl_ite_cobertura_codigo_touro_semen'];
                $protocoloId = $item['tbl_cobertura_protocoloiatf'];

                $qtdItemIatf = $dao->contarItensDoProtocoloIatf($protocoloId);
                $temInseminacao = '';
                if ($qtdItemIatf >= 2 && $qtdItemIatf <= 6) {
                    $temInseminacao = $item['tbl_ite_cobertura_dia_' . $qtdItemIatf];
                }

                if ($temInseminacao == 'S' && $diagnostico != 'P' && $diagnostico != 'N') {
                    $diagnostico = 'D'; // aguardando diagnóstico
                } else if ($diagnostico == 'P') {
                    $diagnostico = 'P'; // positivo
                } else if ($diagnostico == 'N') {
                    $diagnostico = 'N'; // negativo
                    $dataDiagnosticoNegativo = $item['tbl_ite_cobertura_negativo_em'];
                } else {
                    $diagnostico = 'I'; // aguardando inseminação
                }

                $estacao = $dao->estacaoPorId($estacaoMontaId);
                $descEstacao = $estacao ? (string) $estacao['tbl_par_estacao_nome'] : 'Não encontrada';

                if ($codigoPai == '') {
                    $codigoPai = '000000000';
                    $racaPai = '';
                } else {
                    $racaPai = $dao->racaDoPai($codigoPai);
                }
            }
        } else {
            $codigoPai = '000000000';
            $racaPai = '';
            $coberturaId = 0;
            $itemCobertura = 0;
            $itemCoberturaNascido = '';
            $protocoloId = 0;
            $estacaoMontaId = 0;
            $abortoEstacao = 0;
            $descEstacao = '';
            $diagnostico = '';
        }

        // meses desde o diagnóstico negativo
        if ($diagnostico == 'N' && $dataDiagnosticoNegativo != 0) {
            $diferenca = (new DateTime((string) $dataDiagnosticoNegativo))->diff(new DateTime());
            $mesesCalculadosNegativo = ($diferenca->y * 12) + $diferenca->m;
        } else {
            $mesesCalculadosNegativo = 0;
        }

        if ($coberturaId != 0) {
            if ($controle == 'C') {
                $dataProtocolo = $dao->dataDoProtocoloDaCobertura($coberturaId);
                foreach ($dao->descricoesDoProtocoloIatf($protocoloId) as $descricao) {
                    $dias = substr($descricao, 3);
                    $dataServico = date("Y-m-d", strtotime($dataProtocolo . "+{$dias} days"));
                }
            }
            $diasCalculados = $this->intervalo($dataServico, $dataNascimento)->days;
        }

        $nascidosEstacao = 0;
        $dataNascimentoUltimo = 0;
        $mesesCalculados = 0;
        $diasNascimento = '';
        $ultimoFilho = null;
        if ($estacaoMontaId != 0) {
            list($nascidosEstacao, $primeiro) = $dao->filhosNaEstacao($codigoIdAnimal, $estacaoMontaId);
            if ($nascidosEstacao != 0) {
                $ultimoFilho = $primeiro;
            }
        } else if ($controle == 'M') {
            $filho = $dao->nascimentoDoUltimoFilho($codigoIdAnimal);
            if ($filho) {
                $nascidosEstacao = 1;
                $ultimoFilho = $filho[0];
            }
        }
        if ($nascidosEstacao != 0) {
            $dataNascimentoUltimo = $ultimoFilho;
            $diff = $this->intervalo($dataNascimentoUltimo, $dataNascimento);
            $mesesCalculados = $diff->m + ($diff->y * 12);
            $diasNascimento = $diff->days;
        }

        $valor = [
            0  => $codigoIdAnimal,
            1  => $diagnostico,
            2  => $descRaca,
            3  => $codigoPai,
            4  => $coberturaId,
            5  => $itemCobertura,
            6  => $dataServico,
            7  => $diasCalculados,
            8  => $estacaoMontaId,
            9  => $nascidosEstacao,
            10 => $abortoEstacao,
            11 => $racaPai,
            12 => $descEstacao,
            13 => $dataNascimentoUltimo,
            14 => $idUltimaEstacao,
            15 => $controle,
            16 => $dataServico,
            17 => $mesesCalculados,
            18 => $diasCalculadosAborto,
            19 => $diasNascimento,
            20 => $itemCoberturaNascido,
            21 => $mesesCalculadosAborto,
            22 => $mesesCalculadosNegativo,
            23 => $dataDiagnosticoNegativo,
        ];
        // texto, como o web devolve (null vira '')
        return ["success" => true, "encontrado" => true,
                "php" => array_map(function ($v) { return (string) $v; }, $valor)];
    }

    /**
     * Meses entre o último filho da fêmea e a data informada —
     * ler_animal_femea_ultimo_nascimento.php (999 se não tem filho;
     * negativo se o filho nasceu depois da data).
     */
    public function ultimoNascimento($dados){
        $bd = trim((string) ($dados['bd'] ?? ''));
        $mae = trim((string) ($dados['mae'] ?? ''));
        $data = DateTime::createFromFormat('!Y-m-d', (string) ($dados['data'] ?? ''));
        if ($bd === '' || $mae === '' || !$data) {
            return ["success" => false, "message" => "Informe bd, mae e data."];
        }
        $dao = new NascimentoDao($bd);
        $meses = 999;
        $filho = $dao->nascimentoDoUltimoFilho($mae);
        if ($filho) {
            $primeira = new DateTime((string) $filho[0]);
            $diff = $primeira->diff($data);
            $meses = $diff->m + ($diff->y * 12);
            if ($primeira > $data) {
                $meses = -$meses;
            }
        }
        return ["success" => true, "meses" => $meses];
    }
}
