<?php
    function listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria) {

        $data_peso_nascimento = 0;
        
        if ($animal['dataPrimeiroPeso']!='') {
            $data_primeiro_peso = substr($animal['dataPrimeiroPeso'], 0, 10);

            if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                $data_peso_nascimento = $data_primeiro_peso;
                $peso_nascimento = $animal['primeiroPeso'];
                    }
        }
        else {
            if ($animal['codigoMovimentacaoCompra']!='') {
                $data_primeiro_peso = $animal['dataCompra'];

                if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                    $data_peso_nascimento = $data_primeiro_peso;
                    $peso_nascimento = $animal['primeiroPeso'];
                }
            }
        }

        if ($data_peso_nascimento!=0) {
            $data_peso_inicial = $data_peso_nascimento;
            $peso_inicial = $peso_nascimento;
        }
        else {
            $data_peso_inicial='0000-00-00';
            $peso_inicial = 9999;
        }

        $data_peso_final = '0000-00-00';
        $peso_final = 9999;

        if (!empty($dados_pesos_do_animal)) {

            if ($data_peso_nascimento!=0) {
                $partes = explode("-", $data_peso_nascimento);

                for ($i=0; $i < $qtd_meses; $i++) { 
                    if ($array_mes[$i]==$partes[1] && $array_ano[$i]==$partes[0]) {
                        $peso_inicial=$peso_nascimento;
                    }
                }
            }
            foreach ($dados_pesos_do_animal as $reg_peso) {
                $data_peso = $reg_peso->tbl_ite_pesagem_data_emissao;
                $peso = $reg_peso->tbl_ite_pesagem_peso;

                if ($peso == 0) {
                    $peso = 9999;
                }

                $partes = explode("-", $data_peso_inicial);
                $ano_mes_peso_inicial = $partes[0].$partes[1];

                $partes = explode("-", $data_peso_final);
                $ano_mes_peso_final = $partes[0].$partes[1];

                $partes = explode("-", $data_peso);
                $ano_mes_peso = $partes[0].$partes[1];

                if ($data_peso_inicial=='0000-00-00') {
                    $data_peso_inicial=$data_peso;
                    $peso_inicial=$peso;
                }

                if ($ano_mes_peso_inicial==$ano_mes_peso) {
                    if ($peso_inicial==9999) {
                        if ($peso<$peso_inicial && $peso!=0) {
                            $data_peso_inicial=$data_peso;
                            $peso_inicial = $peso;
                        }
                    }
                }

                if ($ano_mes_peso_inicial!=$ano_mes_peso) {
                    if ($ano_mes_peso_final==$ano_mes_peso) {
                        if ($peso<$peso_final && $peso!=0) {
                            $data_peso_final=$data_peso;
                            $peso_final = $peso;
                        }
                    }
                    else {
                        $data_peso_final=$data_peso;
                        $peso_final = $peso;
                    }
                }
            }

            if ($peso_inicial==9999) {
                $peso_inicial = 0;
            }

            if ($peso_final==9999) {
                $peso_final = 0;
            }

            $diferenca = strtotime($data_peso_final) - strtotime($data_peso_inicial);
            $dias = floor($diferenca / (60 * 60 * 24)); 

            if ($peso_final && $peso_inicial) {
                $ganho = $peso_final - $peso_inicial;
            }
            else {
                $ganho = 0;
            }

            if ($ganho!=0 && $dias!=0) {
                $gmd = $ganho / $dias;
            }
            else {
                $gmd=0;
            }

            if ($gmd!=0) {
                if ($animal['sexo']=="M") {
                    $array_gmd_macho_categoria[$animal['codigoCategoria']]+=$gmd;
                    $array_qtd_macho_categoria[$animal['codigoCategoria']]++;
                }
                else {
                    $array_gmd_femea_categoria[$animal['codigoCategoria']]+=$gmd;
                    $array_qtd_femea_categoria[$animal['codigoCategoria']]++;
                }
            }
        }

        return [
        'arrayGmdMachoCategoria' => $array_gmd_macho_categoria,
        'arrayQtdMachoCategoria' => $array_qtd_macho_categoria,
        'arrayGmdFemeaCategoria' => $array_gmd_femea_categoria,
        'arrayQtdFemeaCategoria' => $array_qtd_femea_categoria

        ];
    }

    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_inicial = $_REQUEST['data_inicial'];
    $partes = explode("-", $data_inicial);
    $ano_inicial = $partes[0];
    $mes_inicial = $partes[1];
    $dia_inicial = '01';

    $data_final = $_REQUEST['data_final'];
    $partes = explode("-", $data_final);
    $ano_final = $partes[0];
    $mes_final = $partes[1];
    $dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);

    $data1 = new DateTime($data_inicial);
    $data2 = new DateTime($data_final);
    $intervalo = $data1->diff($data2);
    $qtd_meses = $intervalo->y * 12 + $intervalo->m + $intervalo->d/30 + $intervalo->h / 24;
    $qtd_meses++;
    $ano_atual = $ano_inicial;

    if ($qtd_meses>12) {
    echo '
       <script type="text/javascript">
            alert ("Selecione no máximo 12 meses.");
            location.href="form_rel_gmd.php";
       </script>';
        exit;
    }

    $data_array=new DateTime($data_inicial);

    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
    $mes_extenco = ucfirst(utf8_encode($mes_extenco));
    $array_mes_extenco[0]=$mes_extenco.'/'.$ano_atual;

    $array_mes[0]=$data_array->format('m');
    $array_ano[0]=$data_array->format('Y');

    $ano_mes = $data_array->format('Y').$data_array->format('m');
    $array_mes_ano[$ano_mes]=$ano_mes;
    $array_peso[$ano_mes]=0;

    for ($i=1; $i < $qtd_meses; $i++) { 
        $proximo_mes=1;
        $data_array->add(new DateInterval('P'.$proximo_mes.'M'));

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
        $mes_extenco = ucfirst(utf8_encode($mes_extenco));
        $array_mes_extenco[$i]=$mes_extenco.'/'.$ano_atual;

        if ($mes_extenco == 'Dezembro') {
            $ano_atual++;
        }

        $array_mes[$i]=$data_array->format('m');
        $array_ano[$i]=$data_array->format('Y');
        $ano_mes = $data_array->format('Y').$data_array->format('m');
        $array_mes_ano[$ano_mes]=$ano_mes;
        $array_peso[$ano_mes]=0;
    } 

    @ session_start(); 

    $codigo_alfa_numerico = $_REQUEST['codigo_alfa_numerico']; 

    if ($codigo_alfa_numerico!='') {
        $codigo_numerico_consulta = substr($codigo_alfa_numerico, -9);

        if (strlen($codigo_alfa_numerico)!=9){
            $data = explode("-", $codigo_alfa_numerico);
            $codigo_alfa_consulta = $data[0];
        }
        else {
            $codigo_alfa_consulta = '';
        }
    }

    $tipo_rel= $_REQUEST["tipo_rel"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];
    $num_parto_de = $_REQUEST['num_parto_de'];
    $num_parto_ate = $_REQUEST['num_parto_ate'];
    $num_aborto_de = $_REQUEST['num_aborto_de'];
    $num_aborto_ate = $_REQUEST['num_aborto_ate'];
    $num_natimorto_de = $_REQUEST['num_natimorto_de'];
    $num_natimorto_ate = $_REQUEST['num_natimorto_ate'];
    $previsao_parto_de = $_REQUEST["previsao_parto_de"];
    $previsao_parto_ate = $_REQUEST["previsao_parto_ate"];
    $data_paricao_de = $_REQUEST["data_paricao_de"];
    $data_paricao_ate = $_REQUEST["data_paricao_ate"];
    $filtro_reproducao = $_REQUEST["filtro_reproducao"];
    $filtro_num_parto = $_REQUEST['filtro_num_parto'];
    $filtro_num_aborto = $_REQUEST['filtro_num_aborto'];
    $filtro_num_natimorto = $_REQUEST['filtro_num_natimorto'];
    $filtro_previsao_parto = $_REQUEST['filtro_previsao_parto'];
    $filtro_data_paricao = $_REQUEST['filtro_data_paricao'];
    $filtro_vacas_paridas = $_REQUEST['filtro_vacas_paridas'];
    $filtro_vacas_solteiras = $_REQUEST['filtro_vacas_solteiras'];
    $filtro_vacas_prenhas = $_REQUEST['filtro_vacas_prenhas'];
    $filtro_descarte = $_REQUEST["filtro_descarte"];
    $filtro_positivas = $_REQUEST["filtro_positivas"];
    $filtro_negativas = $_REQUEST["filtro_negativas"];
    $filtro_monta_natural = $_REQUEST["filtro_monta_natural"];

    // Inicia campos vazios 
    if ($num_parto_de=='') {
        $num_parto_de = 0;
        $num_parto_ate = 999;
    }

    if ($num_aborto_de=='') {
        $num_aborto_de = 0;
        $num_aborto_ate = 999;
    }

    if ($num_natimorto_de=='') {
        $num_natimorto_de = 0;
        $numn_atimorto_ate = 999;
    }

    $array_codigos_estacao = [];
    $westacao = "";

    // 1. Verifica se existe e se não está vazio/em branco.
    if (isset($_REQUEST['filtro_estacao']) && !empty($_REQUEST['filtro_estacao'])) {
        
        // 2. SE for uma STRING, a convertemos para um ARRAY
        if (is_string($_REQUEST['filtro_estacao'])) {
            // Explode a string pelo delimitador ','
            $estacoes = explode(',', $_REQUEST['filtro_estacao']);
            
            // Remove strings vazias resultantes de vírgulas extras (ex: a vírgula final)
            $estacoes = array_filter($estacoes, 'trim'); 
        } 
        // 3. SE já for um ARRAY (caso venha de um formulário com múltiplos checkboxes, por exemplo)
        else if (is_array($_REQUEST['filtro_estacao'])) {
            $estacoes = $_REQUEST['filtro_estacao'];
        } else {
            // Não é string nem array (ignora o bloco, a condição continua falhando)
            $estacoes = [];
        }

        // Garante que $estacoes ainda tem itens após a conversão
        if (!empty($estacoes)) {
            
            // O restante do seu código pode ser mantido aqui:
            $estacoes_formatadas = array_map(function($estacao) use ($conector) {
                $estacao_limpa = trim($estacao); 
                return "'" . mysqli_real_escape_string($conector, $estacao_limpa) . "'";
            }, $estacoes);

            $in_estacoes = implode(',', $estacoes_formatadas);
            // ... (resto do código para montar $westacao)
            
            $query = "SELECT tbl_par_estacao_id FROM tbl_parametro_estacao_monta
                      WHERE tbl_par_estacao_nome IN ({$in_estacoes})
                      ORDER BY tbl_par_estacao_id ASC";

            $sql = mysqli_query($conector, $query);

            if ($sql && mysqli_num_rows($sql) != 0) {
                while ($reg_estacao = mysqli_fetch_object($sql)){
                    $array_codigos_estacao[] = $reg_estacao->tbl_par_estacao_id;
                }
            }
            
            if (!empty($array_codigos_estacao)) {
                $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(" . implode(',', $array_codigos_estacao) . ")";
            }

        } // Fim do if (!empty($estacoes))
    } else {
        $westacao = ""; // Sem filtro
    }

    // 2. Lógica para definir o controle da cobertura (Corrigido e Simplificado)

    // Se o array de códigos de estação está vazio, o bloco `if` não será executado
    if (empty($array_codigos_estacao)) {
        // Caso 1: SEM filtro de estação
        if ($filtro_monta_natural == 'S') {
            $cobertura_controle = " AND tbl_cobertura_controle = 'M'"; 
        } else {
            // Se filtro_monta_natural for 'N' e não houver estação, buscamos tudo
            $cobertura_controle = "";
        }
    } else {
        // Caso 2: COM filtro de estação (e $westacao já está preenchido corretamente)
        if ($filtro_monta_natural == 'N') {
            // Filtra apenas por Cobertura Controlada (C) E as Estações
            $cobertura_controle = " AND tbl_cobertura_controle = 'C' " . $westacao;
            $westacao = ""; // Limpa $westacao para não duplicar na query final
        } else {
            // Filtra por Monta Natural (M) OU (Controlada (C) E as Estações)
            $cobertura_controle = " AND (tbl_cobertura_controle = 'M' OR (tbl_cobertura_controle = 'C' " . $westacao . "))";
            $westacao = ""; // Limpa $westacao para não duplicar na query final
        }
    }

    $wlocal = '';
    $wlocal_anterior = '';

    if (isset($_REQUEST['local']) && !empty($_REQUEST['local'])) {
        $local_filtro = $_REQUEST['local'];
        $string_limpa = trim($local_filtro, " ,"); 
        
        $matriz_itens = explode(",", $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $local_para_query = implode(',', $itens_limpos);

            $wlocal = " AND tbl_animal_codigo_fazenda IN({$local_para_query})";

            $wlocal_anterior = " AND (tbl_animal_codigo_fazenda IN({$local_para_query}) OR (tbl_animal_codigo_origem IN({$local_para_query}) 
                AND tbl_animal_situacao='V'))";
        }
    }

    $worigem='';

    if (isset($_REQUEST['origem']) && !empty($_REQUEST['origem'])) {
        $origem_filtro = $_REQUEST['origem'];
        $string_limpa = trim($origem_filtro, " ,"); 
        
        $matriz_itens = explode(",", $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $origem_para_query = implode(',', $itens_limpos);

            $worigem = " AND tbl_animal_codigo_origem IN({$origem_para_query})";
        }
    }

    $wsexo = "";

    if (isset($_REQUEST['sexo']) && !empty($_REQUEST['sexo'])) {
        $sexo_filtro = $_REQUEST['sexo'];
        $string_limpa = trim($sexo_filtro, " ,"); 

        if ($string_limpa!='Todos') {
            $matriz_itens = explode(",", $string_limpa);
            
            $itens_limpos = array_map('trim', $matriz_itens);
            
            $itens_validos = array_filter($itens_limpos);

            if (!empty($itens_validos)) {
                $itens_com_aspas = array_map(function($item) {
                    // **IMPORTANTE**: Use aspas simples dentro da string
                    return "'" . $item . "'"; 
                }, $itens_validos);

                $sexo_para_query = implode(',', $itens_com_aspas);
                $wsexo = " AND tbl_animal_sexo IN({$sexo_para_query})";
            }
        }   
    }

    $wmae = "";

    if (isset($_REQUEST['codigos_maes']) && !empty($_REQUEST['codigos_maes'])) {
        $mae_filtro = $_REQUEST['codigos_maes'];
        $string_limpa = trim($mae_filtro, " ,"); 
        
        $matriz_itens = explode(",", $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $mae_para_query = implode(',', $itens_limpos);

            $wmae = " AND tbl_animal_codigo_mae IN({$mae_para_query})";
        }
    }

    $wpai = "";

    if (isset($_REQUEST['codigos_pais']) && !empty($_REQUEST['codigos_pais'])) {
        $pai_filtro = $_REQUEST['codigos_pais'];
        $string_limpa = trim($pai_filtro, " ,"); 
        
        $matriz_itens = explode(",", $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $pai_para_query = implode(',', $itens_limpos);

            $wpai = " AND tbl_animal_codigo_pai IN({$pai_para_query})";
        }
    }

    $wraca = "";

    if (isset($_REQUEST['codigos_racas']) && !empty($_REQUEST['codigos_racas'])) {
        $raca_filtro = $_REQUEST['codigos_racas'];
        $string_limpa = trim($raca_filtro, " ,"); 
        
        $matriz_itens = explode(",", $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $raca_para_query = implode(',', $itens_limpos);

            $wraca = " AND tbl_animal_codigo_raca IN({$raca_para_query})";
        }
    }

    $data_nasc_inicial = $_REQUEST["data_nasc_inicial"];
    $data_nasc_final = $_REQUEST["data_nasc_final"];

    if ($data_nasc_inicial==0 && $data_nasc_final==0){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
    }

    $situacao_vendido = $_REQUEST['situacao_vendido'];
    $situacao_morte = $_REQUEST['situacao_morte'];
    $situacao_outra = $_REQUEST['situacao_outra'];
    $ativo_filtro = $_REQUEST['ativo'];

    if ($ativo_filtro=='Todos') {
        $wativo='';
    }
    else {
        $wativo = " AND tbl_animal_ativo IN(";
        $wativo .= "'" . $ativo_filtro . "'";
        $wativo.= ")";
    }
    
    $peso_nasc_inicial = $_REQUEST["peso_nasc_inicial"];
    $peso_nasc_final = $_REQUEST["peso_nasc_final"];

    $peso_desmama_inicial = $_REQUEST["peso_desmama_inicial"];
    $peso_desmama_final = $_REQUEST["peso_desmama_final"];

    $peso_ult_inicial = $_REQUEST["peso_ult_inicial"];
    $peso_ult_final = $_REQUEST["peso_ult_final"];

    if ($peso_nasc_inicial==0 && $peso_nasc_final==0){
        $wpeso_nasc = '';
    }
    else {
        $wpeso_nasc = " AND tbl_animal_primeiro_peso >= '$peso_nasc_inicial' AND tbl_animal_primeiro_peso <= '$peso_nasc_final'";
    }

    if ($peso_desmama_inicial==0 && $peso_desmama_final==0){
        $wpeso_desmama = '';
    }
    else {
        $wpeso_desmama = " AND tbl_animal_peso_desmama >= '$peso_desmama_inicial' AND tbl_animal_peso_desmama <= '$peso_desmama_final'";
    }

    if ($peso_ult_inicial==0 && $peso_ult_final==0){
        $wpeso_ult = '';
    }
    else {
        $wpeso_ult = " AND tbl_animal_ultimo_peso >= '$peso_ult_inicial' AND tbl_animal_ultimo_peso <= '$peso_ult_final'";
    }

    $wcategoria = "";

    if (isset($_REQUEST['categoria']) && !empty($_REQUEST['categoria'])) {
        $categoria_filtro = $_REQUEST['categoria'];
        $string_limpa = trim($categoria_filtro, " ,"); 
        $matriz_itens = explode(',', $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $wcategoria = $itens_limpos;
        }
    } 

    $desc_categoria = [];
    $arrayCategorias = [];
    $descricaoCategorias = [];

    $sql = "SELECT * FROM tabela_categoria_idade 
        WHERE tab_registro_lixeira_categoria_idade='0'"; 
        
    $rs = mysqli_query($conector,$sql); 

    while ($fila = mysqli_fetch_object($rs)){
        $codigo_id = $fila->tab_codigo_categoria_idade;
        $idade_de = $fila->tab_categoria_idade_de;
        $idade_ate = $fila->tab_categoria_idade_ate;

        if ($idade_ate==999999999){
            array_push($desc_categoria, '> 36 m');
                $descricaoCategorias = [
                    "id" => $codigo_id,
                    "idade_de" => $idade_de,
                    "idade_ate" => $idade_ate
            ];
            array_push($arrayCategorias, $descricaoCategorias);
        }
        else {
            array_push($desc_categoria, $idade_de . ' a ' . $idade_ate . ' m');
            $descricaoCategorias = [
                "id" => $codigo_id,
                "idade_de" => $idade_de,
                "idade_ate" => $idade_ate
            ];
            array_push($arrayCategorias, $descricaoCategorias);
        }
    }

$wsituacao='';
$situacoes='';

if ($ativo_filtro=='Todos') {
    if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'N') {
        $situacoes = "''".','."'V'";
    }
    else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'N') {
        $situacoes = "''".','."'V'".','."'M'";
    }    
    else if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'S') {
        $situacoes = "''".','."'V'".','."'S'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'N') {
        $situacoes = "''".','."'M'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'S') {
        $situacoes = "''".','."'M'".','."'S'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'N' && $situacao_outra == 'S') {
        $situacoes = "''".','."'S'";
    }    
    else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'S') {
       $situacoes = "''".','."'V'".','."'M'".','."'S'";
    }    
}
else if ($ativo_filtro=='N') {
    if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'N') {
        $situacoes = "'V'";
    }
    else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'N') {
        $situacoes = "'V'".','."'M'";
    }    
    else if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'S') {
        $situacoes = "'V'".','."'S'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'N') {
        $situacoes = "'M'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'S') {
        $situacoes = "'M'".','."'S'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'N' && $situacao_outra == 'S') {
        $situacoes = "'O'";
    }    
    else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'S') {
       $situacoes = "'V'".','."'M'".','."'S'";
    }    
}

if ($situacoes!='') {
    $wsituacao = " AND tbl_animal_situacao IN(";
    $wsituacao.=$situacoes;
    $wsituacao.= ")";
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS 
  <link href="css/jquery-ui.css" rel="stylesheet" />-->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css?<?php echo Versao;?>" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

    <style type="text/css">
        table.dataTable thead th { border-bottom: 0; }
    </style>

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php"; 
            include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; 
            include "limpar_secao_selecao_matrizes.php"; 
            include "limpar_secao_compra_venda.php"; 
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php"; 
            include "limpar_secao_pesagem.php"; 
            include "limpar_secao_movimentacao.php"; 
            include "limpar_secao_nutricao.php"; 
            include "limpar_secao_nascimento.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Ganho de Peso</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-money-check-alt"></i> Ganho de Peso</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data">

                            <div class="panel"> 
                                <div class=panel-body>
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="container" id="dados_cliente">

                                                <input type="hidden" id="expande_tela" value="S">

                                                <input type="hidden" id="tipo_rel"
                                                    <?php echo "value='".$_REQUEST["tipo_rel"]."'";?>>

                                                <input type="hidden" id="codigo_number_filtro"
                                                    <?php echo "value='".$_REQUEST['codigo_alfa_numerico']."'";?>>

                                                <input type="hidden" id="codigo_local_filtro"
                                                    <?php echo "value='".$_REQUEST['local']."'";?>>

                                                <input type="hidden" id="codigo_categoria_filtro"
                                                    <?php echo "value='".$_REQUEST['categoria']."'";?>>

                                                <input type="hidden" id="codigo_origem_filtro" <?php echo "value='".$_REQUEST['origem']."'";?>>

                                                <input type="hidden" id="codigo_mae_filtro"
                                                    <?php echo "value='".$_REQUEST['codigos_maes']."'";?>>

                                                <input type="hidden" id="codigo_pai_filtro"
                                                    <?php echo "value='".$_REQUEST['codigos_pais']."'";?>>

                                                <input type="hidden" id="codigo_raca_filtro"
                                                    <?php echo "value='".$_REQUEST['codigos_racas']."'";?>>

                                                <input type="hidden" id="peso_inicial_nasc_filtro"
                                                    <?php echo "value='".$_REQUEST["peso_nasc_inicial"]."'";?>>

                                                <input type="hidden" id="peso_final_nasc_filtro"
                                                    <?php echo "value='".$_REQUEST["peso_nasc_final"]."'";?>>

                                                <input type="hidden" id="peso_inicial_desmama_filtro"
                                                    <?php echo "value='".$_REQUEST["peso_desmama_inicial"]."'";?>>

                                                <input type="hidden" id="peso_final_desmama_filtro"
                                                    <?php echo "value='".$_REQUEST["peso_desmama_final"]."'";?>>

                                                <input type="hidden" id="peso_inicial_ultimo_filtro"
                                                    <?php echo "value='".$_REQUEST["peso_ult_inicial"]."'";?>>

                                                <input type="hidden" id="peso_final_ultimo_filtro"
                                                    <?php echo "value='".$_REQUEST["peso_ult_final"]."'";?>>

                                                <input type="hidden" id="data_nasc_inicial_filtro"
                                                    <?php echo "value='".$_REQUEST["data_nasc_inicial"]."'";?>>

                                                <input type="hidden" id="data_nasc_final_filtro"
                                                    <?php echo "value='".$_REQUEST["data_nasc_final"]."'";?>>

                                                <input type="hidden" id="previsao_parto_de_filtro"
                                                    <?php echo "value='".$_REQUEST["previsao_parto_de"]."'";?>>

                                                <input type="hidden" id="previsao_parto_ate_filtro"
                                                    <?php echo "value='".$_REQUEST["previsao_parto_ate"]."'";?>>

                                                <input type="hidden" id="data_paricao_de_filtro"
                                                    <?php echo "value='".$_REQUEST["data_paricao_de"]."'";?>>

                                                <input type="hidden" id="data_paricao_ate_filtro"
                                                    <?php echo "value='".$_REQUEST["data_paricao_ate"]."'";?>>

                                                <input type="hidden" id="num_parto_de_filtro"
                                                    <?php echo "value='".$_REQUEST['num_parto_de']."'";?>>

                                                <input type="hidden" id="num_parto_ate_filtro" <?php echo "value='".$_REQUEST['num_parto_ate']."'";?>>

                                                <input type="hidden" id="num_aborto_de_filtro"  <?php echo "value='".$_REQUEST['num_aborto_de']."'";?>>

                                                <input type="hidden" id="num_aborto_ate_filtro"
                                                    <?php echo "value='".$_REQUEST['num_aborto_ate']."'";?>>

                                                <input type="hidden" id="num_natimorto_de_filtro"  <?php echo "value='".$_REQUEST['num_natimorto_de']."'";?>>

                                                <input type="hidden" id="num_natimorto_ate_filtro"
                                                    <?php echo "value='".$_REQUEST['num_natimorto_ate']."'";?>>

                                                <input type="hidden" id="codigo_estacao_filtro"
                                                    <?php echo "value='".$_REQUEST['filtro_estacao']."'";?>>

                                                <input type="hidden" id="filtro_aplicado"
                                                    <?php echo "value='".$_REQUEST["descricao_filtro"]."'";?>>

                                                <input type="hidden" id="ativo"
                                                    <?php echo "value='".$_REQUEST['ativo']."'";?>>

                                                <input type="hidden" id="sexo"
                                                    <?php echo "value='".$_REQUEST['sexo']."'";?>>

                                                <input type="hidden" id="filtro_reproducao"
                                                    <?php echo "value='".$_REQUEST["filtro_reproducao"]."'";?>>

                                                <input type="hidden" id="filtro_previsao_parto"
                                                    <?php echo "value='".$_REQUEST['filtro_previsao_parto']."'";?>>

                                                <input type="hidden" id="filtro_data_paricao"
                                                    <?php echo "value='".$_REQUEST['filtro_data_paricao']."'";?>>

                                                <input type="hidden" id="filtro_monta_natural"
                                                    <?php echo "value='".$_REQUEST["filtro_monta_natural"]."'";?>>

                                                <input type="hidden" id="filtro_vacas_paridas"
                                                    <?php echo "value='".$_REQUEST['filtro_vacas_paridas']."'";?>>

                                                <input type="hidden" id="filtro_vacas_solteiras"
                                                    <?php echo "value='".$_REQUEST['filtro_vacas_solteiras']."'";?>>

                                                <input type="hidden" id="filtro_vacas_prenhas"
                                                    <?php echo "value='".$_REQUEST['filtro_vacas_prenhas']."'";?>>

                                                <input type="hidden" id="filtro_descarte"
                                                    <?php echo "value='".$_REQUEST["filtro_descarte"]."'";?>>

                                                <input type="hidden" id="filtro_num_parto"
                                                    <?php echo "value='".$_REQUEST['filtro_num_parto']."'";?>>

                                                <input type="hidden" id="filtro_num_aborto"
                                                    <?php echo "value='".$_REQUEST['filtro_num_aborto']."'";?>>

                                                <input type="hidden" id="filtro_num_natimorto"
                                                    <?php echo "value='".$_REQUEST['filtro_num_natimorto']."'";?>>

                                                <input type="hidden" id="filtro_positivas"
                                                    <?php echo "value='".$_REQUEST["filtro_positivas"]."'";?>>

                                                <input type="hidden" id="filtro_negativas"
                                                    <?php echo "value='".$_REQUEST["filtro_negativas"]."'";?>>

                                                <input type="hidden" id="vendido"
                                                    <?php echo "value='".$_REQUEST['situacao_vendido']."'";?>>

                                                <input type="hidden" id="morte"
                                                    <?php echo "value='".$_REQUEST['situacao_morte']."'";?>>

                                                <input type="hidden" id="outro"
                                                    <?php echo "value='".$_REQUEST['situacao_outra']."'";?>>

                                                <input type="hidden" id="data_inicial"
                                                    <?php echo "value='".$_REQUEST['data_inicial']."'";?>>

                                                <input type="hidden" id="data_final"
                                                    <?php echo "value='".$_REQUEST['data_final']."'";?>>

                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <label class="label_consulta_rel"></label>
                                                        <span><?php echo $descricao_filtro;?></span>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_gmd()">Voltar
                                                        </button>

                                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" onClick="lista_gmd_excel()">Excel
                                                        </button>
                                                    </div>
                                                </div>

                                            <hr align="center"> 

<table id="tabela_gmd_geral" class="table table-bordered table-advance table-hover" style="width:50%; font-size:10px; float: left; margin-left: 10px; border: none;">

<tbody>

<?php
    $data_inicial = $ano_inicial .'-'. $mes_inicial .'-01' ;
    $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
    $animais_listados=0;
    $gmd_total = 0;
    $numero_gmd = 0;

    $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
        WHERE tab_registro_lixeira_categoria_idade='0'");

    $num_rows = mysqli_num_rows($tbl_categoria);    

    if ($num_rows!=0) {
        while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
            $idade_de = $reg_categoria->tab_categoria_idade_de;
            $idade_ate = $reg_categoria->tab_categoria_idade_ate;
            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

            if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                $desc_categoria = ' > 36 meses';
            }
            else {
                $desc_categoria =  $reg_categoria->tab_categoria_idade_de . ' a ' .
                $reg_categoria->tab_categoria_idade_ate . ' meses';
            }

            $array_categoria[$codigo_categoria] = $codigo_categoria;
            $array_desc_categoria[$codigo_categoria] = $desc_categoria;
            $array_qtd_macho_categoria[$codigo_categoria] = 0;
            $array_qtd_femea_categoria[$codigo_categoria] = 0;
            $array_gmd_macho_categoria[$codigo_categoria] = 0;
            $array_gmd_femea_categoria[$codigo_categoria] = 0;
        }
    }   

    if ($codigo_alfa_numerico!='') {
        $sql = "SELECT * from tbl_animais 
            INNER JOIN tbl_pessoa
            ON tbl_pessoa_id = tbl_animal_codigo_fazenda 
            WHERE tbl_animal_codigo_alfa = '$codigo_alfa_consulta' AND 
                  tbl_animal_codigo_numerico = '$codigo_numerico_consulta'";
    }
    else {
        if ($situacao_vendido == 'S') {
            $sql = "SELECT * from tbl_animais 
                INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_animal_codigo_fazenda 
                WHERE 
                tbl_animal_lixeira=0" .
                $wativo . 
                $wsituacao .
                $wlocal_anterior . 
                $wsexo . 
                $wraca . 
                $wpai . 
                $wmae . 
                $wpeso_nasc . 
                $wpeso_desmama . 
                $wpeso_ult . 
                $wdata_nasc .
                " ORDER BY 
                tbl_animal_codigo_fazenda, 
                tbl_animal_codigo_numerico ASC"; 
        }
        else {
            $sql = "SELECT * from tbl_animais 
                INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_animal_codigo_fazenda 
                WHERE 
                tbl_animal_lixeira=0" .
                $wativo . 
                $wsituacao .
                $wlocal . 
                $wsexo . 
                $wraca . 
                $wpai . 
                $wmae . 
                $wpeso_nasc . 
                $wpeso_desmama . 
                $wpeso_ult . 
                $wdata_nasc .
            " ORDER BY 
                tbl_animal_codigo_fazenda, 
                tbl_animal_codigo_numerico ASC"; 
        }
    }

    $tbl_animais = mysqli_query($conector, $sql); 
    $num_rows_animais = mysqli_num_rows($tbl_animais);

    $codigos_pais = [];
    $codigos_maes = [];
    $codigos_racas = [];
    $codigos_pelagem = [];
    $codigos_origem = [];
    $ids_femeas = [];
    $dados_animais = [];
    $dados_descarte = [];

    // Passo 1: Coletar os dados e os IDs dos pais, mae, origem, raça, femeas para o cache
    while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
        $data_baixa = $reg_animal->tbl_animal_baixado_em;

        if ($data_baixa!='') {
            $data_acompanhamento_calculo = date($data_baixa);
        }
        else {
            $data_acompanhamento_calculo = date("Y-m-d");
        }

        $date = new DateTime($data_nascimento);
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade_acompanhamento_mostra_dias = $idade_acompanhamento->format('%d');

        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        $dados_animais[] = $reg_animal;

        if ($reg_animal->tbl_animal_codigo_mae) {
            $codigos_maes[] = $reg_animal->tbl_animal_codigo_mae;
        }

        if ($reg_animal->tbl_animal_codigo_pai) {
            $codigos_pais[] = $reg_animal->tbl_animal_codigo_pai;
        }

        if ($reg_animal->tbl_animal_codigo_raca) {
            $codigos_racas[] = $reg_animal->tbl_animal_codigo_raca;
        }

        if ($reg_animal->tbl_animal_codigo_pelagem) {
            $codigos_pelagem[] = $reg_animal->tbl_animal_codigo_pelagem;
        }

        if ($reg_animal->tbl_animal_codigo_origem) {
            $codigos_origem[] = $reg_animal->tbl_animal_codigo_origem;
        }

        if ($reg_animal->tbl_animal_sexo == 'F') {
            $ids_femeas[] = $reg_animal->tbl_animal_codigo_id;
        }

        if ($filtro_descarte=='S' && $reg_animal->tbl_animal_descarte_reproducao) {
            $dados_descarte[$reg_animal->tbl_animal_codigo_id] = 'S';
        }
        else if ($filtro_descarte=='N' && $reg_animal->tbl_animal_descarte_reproducao!="S") {
            $dados_descarte[$reg_animal->tbl_animal_codigo_id] = 'S';
        }
    }

    $dados_partos = [];
    $dados_abortos = [];
    $dados_natimortos = [];
    $dados_previsao_partos = [];
    $dados_data_partos = [];
    $ultimo_filho_cache = [];
    $num_partos = 0;
    $num_abortos = 0;
    $num_natimorto = 0;
    $data_ref = date("Y-m-d");
    $femeas_prenhes = [];
    $femeas_negativas = [];
    $femea_selecionada_cobertura = [];

    // Consulta otimizada para verificar se a femea esta na estacao atual

    if (!empty($ids_femeas)) {
        $ids_string = implode(',', array_map('intval', $ids_femeas));
  
        $sql = "SELECT *
                FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                 WHERE tbl_cobertura_lixeira=0 AND 
                      (tbl_cobertura_controle = 'C' OR  
                       tbl_cobertura_controle = 'M') AND                        
                       tbl_cobertura_codigo_local IN({$local_para_query}) AND 
                       tbl_ite_cobertura_codigo_id_animal IN ($ids_string)
                ORDER BY tbl_ite_cobertura_codigo_id_animal ASC, tbl_ite_cobertura_numero_id DESC"; // Ordena primeiro por fêmea, depois por ID de forma decrescente

        $tbl_item_cobertura = mysqli_query($conector, $sql);

        $femeas_ja_processadas = [];

        if ($tbl_item_cobertura && mysqli_num_rows($tbl_item_cobertura) > 0) {
            while ($registro = mysqli_fetch_assoc($tbl_item_cobertura)) {
                $codigo_animal = $registro['tbl_ite_cobertura_codigo_id_animal'];
                $diagnostico = $registro['tbl_ite_cobertura_resultado_diagnostico'];
                $id_estacao = $registro['tbl_cobertura_codigo_estacao_monta'];
                $controle = $registro['tbl_cobertura_controle'];
                $nascido = $registro['tbl_ite_cobertura_nascido'];
                $local =  $registro['tbl_cobertura_codigo_local'];

                // Pega estacao atual da fazenda 
                $tbl_estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
                    WHERE tbl_par_codigo_local = '$local' AND 
                          tbl_par_lixeira=0 AND 
                          tbl_par_estacao_id = '$id_estacao'
                    ORDER BY tbl_par_estacao_id DESC LIMIT 1");

                $num_rows_estacao = mysqli_num_rows($tbl_estacao);

                if ($num_rows_estacao!=0) {
                    $reg_estacao = mysqli_fetch_object($tbl_estacao);
                    //$id_estacao_atual = $reg_estacao->tbl_par_estacao_id;
                    $desc_estacao = $reg_estacao->tbl_par_estacao_nome;
                }
                else {
                    //$id_estacao_atual = 0;
                    $desc_estacao = 'Monta';
                }

                if (!isset($femeas_ja_processadas[$codigo_animal])) {
                    $femeas_ja_processadas[$codigo_animal] = true;

                    if ($controle=='C') {
                        //if ($id_estacao==$id_estacao_atual) {
                            $femea_selecionada_cobertura[$codigo_animal] = [
                            'selecionada' => 'S',
                            'controle' => 'C',
                            'diagnostico' => $diagnostico,
                            'nascido' => $nascido,
                            'estacao_id' => $id_estacao,
                            'desc_estacao' => $desc_estacao
                            ];
                            
                            if ($diagnostico=='N' || $nascido!='') {
                                $femea_selecionada_cobertura[$codigo_animal] = [
                                'selecionada' => '',        
                                'controle' => 'C',
                                'diagnostico' => $diagnostico,
                                'nascido' => $nascido,
                                'estacao_id' => $id_estacao,
                                'desc_estacao' => $desc_estacao
                                ];
                            }
                        //}
                    }
                    else {
                        $femea_selecionada_cobertura[$codigo_animal] = [
                                'selecionada' => 'S',        
                                'controle' => 'M',
                                'diagnostico' => $diagnostico,
                                'nascido' => $nascido,
                                'estacao_id' => $id_estacao,
                                'desc_estacao' => $desc_estacao
                                ];

                        if ($diagnostico=='N' || $nascido!='') {
                            $femea_selecionada_cobertura[$codigo_animal] = [
                                'selecionada' => '',        
                                'controle' => 'M',
                                'diagnostico' => $diagnostico,
                                'nascido' => $nascido,
                                'estacao_id' => $id_estacao,
                                'desc_estacao' => $desc_estacao
                                ];
                        }
                    }
                }
            }
        }
    }

    // Consulta otimizada para verificar as femeas negativas

    if ($filtro_reproducao == 'S') {
        if (!empty($ids_femeas)) {
            $ids_string = implode(',', array_map('intval', $ids_femeas));
            $sql = "SELECT *
                    FROM tbl_item_cobertura
                    INNER JOIN tbl_cobertura ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira = 0 AND
                          tbl_ite_cobertura_codigo_id_animal IN ($ids_string) AND 
                          (tbl_cobertura_controle = 'M' OR tbl_cobertura_controle = 'C')
                    ORDER BY tbl_ite_cobertura_codigo_id_animal ASC, tbl_ite_cobertura_numero_id DESC"; // Ordena primeiro por fêmea, depois por ID de forma decrescente

            $tbl_item_cobertura = mysqli_query($conector, $sql);

            $femeas_ja_processadas = [];

            if ($tbl_item_cobertura && mysqli_num_rows($tbl_item_cobertura) > 0) {
                while ($registro = mysqli_fetch_assoc($tbl_item_cobertura)) {
                    $codigo_animal = $registro['tbl_ite_cobertura_codigo_id_animal'];
                    $diagnostico = $registro['tbl_ite_cobertura_resultado_diagnostico'];

                    if (!isset($femeas_ja_processadas[$codigo_animal])) {
                        $femeas_ja_processadas[$codigo_animal] = true;
                        
                        if ($diagnostico == 'N') {
                            $femeas_negativas[$codigo_animal] = $codigo_animal;
                        }
                    }
                }
            }
        }
    }

    // Consulta otimizada para verificar as femeas prenhes
    if ($filtro_reproducao == 'S') {
        if (!empty($ids_femeas)) {
            $ids_string = implode(',', $ids_femeas);

            // 1. Consulta única com a cláusula IN
            $sql = "SELECT *
                FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira = 0
                    AND tbl_ite_cobertura_codigo_id_animal IN ($ids_string)
                    AND tbl_ite_cobertura_resultado_diagnostico = 'P'
                    AND (tbl_ite_cobertura_nascido='' OR tbl_ite_cobertura_nascido IS NULL)" . $cobertura_controle . "
                    ORDER BY tbl_ite_cobertura_numero_id DESC";

            $tbl_item_cobertura = mysqli_query($conector, $sql);

            // 2. Processar os resultados e armazená-los em um array
            if ($tbl_item_cobertura && mysqli_num_rows($tbl_item_cobertura) > 0) {
                while ($registro = mysqli_fetch_assoc($tbl_item_cobertura)) {
                    $codigo_animal = $registro['tbl_ite_cobertura_codigo_id_animal'];

                    // Se você quer apenas o registro mais recente (devido ao ORDER BY DESC),
                        // a lógica abaixo é a correta.
                    if (!isset($femeas_prenhes[$codigo_animal])) {
                        $femeas_prenhes[$codigo_animal] = $codigo_animal;
                    }
                }
            }
        }
    }

    // Consulta otimizada para verificar os dias de aborto para cada femea que teve aborto
    $dados_femeas_aborto_natimorto = [];

    if (!empty($ids_femeas)) {
        $ids_string = implode(',', $ids_femeas);

        // 1. Consulta otimizada
        $sql = "SELECT tbl_mov_estoque_codigo_mae, tbl_mov_estoque_nascimento
                FROM tbl_movimentacao_estoque
                WHERE tbl_mov_estoque_codigo_mae IN ($ids_string)
                AND tbl_mov_estoque_codigo_id_animal = 999999999
                AND (tbl_mov_estoque_entrada_saida = 'A'
                AND (tbl_mov_estoque_tipo_movimentacao = 'A' OR tbl_mov_estoque_tipo_movimentacao = 'B')) OR (tbl_mov_estoque_entrada_saida = 'S' AND tbl_mov_estoque_tipo_movimentacao = 'M')
                ORDER BY tbl_mov_estoque_nascimento DESC";

        $tbl_aborto = mysqli_query($conector, $sql);

        // 2. Processamento dos resultados
        if ($tbl_aborto && mysqli_num_rows($tbl_aborto) > 0) {
            // Inicializa o array com todos os IDs, assumindo que não houve aborto
            // Isso garante que você tenha um registro para cada fêmea, mesmo as que não estão na consulta.
            foreach ($ids_femeas as $id) {
                $dados_femeas_aborto_natimorto[$id] = [
                    'teve_aborto_natimorto' => 'N',
                    'dias_aborto_natimorto' => 0,
                    'data_aborto_natimorto' => 0
                ];
            }
            
            // Agora, itera sobre os resultados da consulta e atualiza o array
            while ($reg_aborto = mysqli_fetch_object($tbl_aborto)) {
                $codigo_mae = $reg_aborto->tbl_mov_estoque_codigo_mae;

                // Se a fêmea já foi processada (por ter mais de um aborto), pule para a próxima
                // Isso garante que você só pegue o aborto mais recente devido ao ORDER BY DESC
                if (isset($dados_femeas_aborto_natimorto[$codigo_mae]['teve_aborto_natimorto']) && $dados_femeas_aborto_natimorto[$codigo_mae]['teve_aborto_natimorto'] == 'S') {
                    continue;
                }

                $data_aborto = $reg_aborto->tbl_mov_estoque_nascimento;
                //$data_ref_ajustada = date("Y-m-d", strtotime($data_ref . "- 35 days"));

                $data_ref_ajustada = date("Y-m-d");

                /*if ($codigo_mae==2198) {
                    print_r('Data Aborto: ' . $data_aborto . ' Data Ref: ' . $data_ref_ajustada . '<br>');
                }*/

                $diferenca = strtotime($data_ref_ajustada) - strtotime($data_aborto);
                $dias_aborto_natimorto = floor($diferenca / (60 * 60 * 24));

                $dados_femeas_aborto_natimorto[$codigo_mae]['teve_aborto_natimorto'] = 'S';
                $dados_femeas_aborto_natimorto[$codigo_mae]['dias_aborto_natimorto'] = $dias_aborto_natimorto;
                $dados_femeas_aborto_natimorto[$codigo_mae]['data_aborto_natimorto'] = $data_aborto;
            }
        }
    }

    if (!empty($ids_femeas)) {
        $ids_string = implode(',', $ids_femeas);

        // Consulta otimizada para contar filhos de todas as mães
        $sql_filhos = "SELECT tbl_animal_codigo_mae, COUNT(*) as num_filhos
                       FROM tbl_animais
                       WHERE tbl_animal_codigo_mae IN ($ids_string)
                       GROUP BY tbl_animal_codigo_mae";
        $result_filhos = mysqli_query($conector, $sql_filhos);

        // Adicione os resultados a um array para fácil acesso
        $num_filhos_cache = [];
        while ($row = mysqli_fetch_assoc($result_filhos)) {
            $num_filhos_cache[$row['tbl_animal_codigo_mae']] = $row['num_filhos'];
        }

        // Consulta otimizada para contar natimortos de todas as mães
        $sql_natimorto = "SELECT tbl_mov_estoque_codigo_mae, COUNT(*) as num_natimorto
                          FROM tbl_movimentacao_estoque
                          WHERE tbl_mov_estoque_codigo_mae IN ($ids_string)
                          AND tbl_mov_estoque_codigo_id_animal=999999999
                          AND tbl_mov_estoque_entrada_saida='E'
                          AND tbl_mov_estoque_tipo_movimentacao='N'
                          GROUP BY tbl_mov_estoque_codigo_mae";
        $result_natimorto = mysqli_query($conector, $sql_natimorto);

        // Adicione os resultados a um array para fácil acesso
        $num_natimorto_cache = [];
        while ($row = mysqli_fetch_assoc($result_natimorto)) {
            $num_natimorto_cache[$row['tbl_mov_estoque_codigo_mae']] = $row['num_natimorto'];
        }

        // Percorrer o array de animais e combinar os dados
        foreach ($dados_animais as $reg_animal) {
            if ($reg_animal->tbl_animal_sexo == 'F') {
                $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
                $num_partos = ($num_filhos_cache[$codigo_animal_id] ?? 0) + ($num_natimorto_cache[$codigo_animal_id] ?? 0);
                $dados_partos[$codigo_animal_id] = $num_partos;
            }
        }

        // Percorrer o array de animais natimorto
        foreach ($dados_animais as $reg_animal) {
            if ($reg_animal->tbl_animal_sexo == 'F') {
                $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
                $num_natimorto = ($num_natimorto_cache[$codigo_animal_id] ?? 0);
                $dados_natimortos[$codigo_animal_id] = $num_natimorto;
            }
        }

        // Consulta otimizada para contar abortos de todas as mães
        $sql_aborto = "SELECT tbl_mov_estoque_codigo_mae, COUNT(*) as num_aborto
                          FROM tbl_movimentacao_estoque
                          WHERE tbl_mov_estoque_codigo_mae IN ($ids_string)
                          AND tbl_mov_estoque_codigo_id_animal=999999999
                          AND tbl_mov_estoque_entrada_saida='A'
                          GROUP BY tbl_mov_estoque_codigo_mae";
        $result_aborto = mysqli_query($conector, $sql_aborto);

        // Adicione os resultados a um array para fácil acesso
        $num_aborto_cache = [];
        while ($row = mysqli_fetch_assoc($result_aborto)) {
            $num_aborto_cache[$row['tbl_mov_estoque_codigo_mae']] = $row['num_aborto'];
        }

        // Percorrer o array de animais abortos
        foreach ($dados_animais as $reg_animal) {
            if ($reg_animal->tbl_animal_sexo == 'F') {
                $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
                $num_abortos = ($num_aborto_cache[$codigo_animal_id] ?? 0);
                $dados_abortos[$codigo_animal_id] = $num_abortos;
            }
        }

        // Consulta otimizada para verificar partos de todas as mães
        if ($filtro_data_paricao=='S') {
            $sql_partos = "SELECT tbl_mov_estoque_codigo_mae, tbl_mov_estoque_nascimento
                FROM tbl_movimentacao_estoque
                WHERE tbl_mov_estoque_codigo_mae IN ($ids_string) AND
                      tbl_mov_estoque_entrada_saida='E' AND
                      tbl_mov_estoque_tipo_movimentacao='N' AND 
                      tbl_mov_estoque_nascimento>='$data_paricao_de' AND 
                      tbl_mov_estoque_nascimento<='$data_paricao_ate'";
            $result_partos = mysqli_query($conector, $sql_partos);

            $num_rows_partos = mysqli_num_rows($result_partos);

            // Adicione os resultados a um array para fácil acesso

            if ($num_rows_partos>0) {
                $paricao_cache = [];

                while ($row = mysqli_fetch_assoc($result_partos)) {
                    $paricao_cache[$row['tbl_mov_estoque_codigo_mae']] = 'S';
                }
            }

            // Percorrer o array de animais
            foreach ($dados_animais as $reg_animal) {
                if ($reg_animal->tbl_animal_sexo == 'F') {
                    $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
                    if (array_key_exists($codigo_animal_id, $paricao_cache)) {
                        if ($paricao_cache[$codigo_animal_id]=='S') {
                            $dados_data_partos[$codigo_animal_id] = 'S';
                        }                        
                    }
                }
            }
        }

        //if ($filtro_vacas_paridas=='S' || $filtro_vacas_solteiras=='S') {
            $sql_ultimo_filho = "SELECT t1.tbl_animal_codigo_mae, t1.tbl_animal_codigo_id, t1.tbl_animal_data_nascimento, t1.tbl_animal_ativo, t1.tbl_animal_codigo_pai
            FROM tbl_animais t1
            INNER JOIN (
            SELECT tbl_animal_codigo_mae, MAX(tbl_animal_data_nascimento) as ultima_data_nascimento
            FROM tbl_animais
            
            WHERE tbl_animal_codigo_mae IN ($ids_string)
                GROUP BY tbl_animal_codigo_mae
                ) t2 ON t1.tbl_animal_codigo_mae = t2.tbl_animal_codigo_mae AND t1.tbl_animal_data_nascimento = t2.ultima_data_nascimento";

            $result_ultimo_filho = mysqli_query($conector, $sql_ultimo_filho);

            // Armazene os resultados para fácil acesso
            $ultimo_filho_cache = [];
            while ($row = mysqli_fetch_assoc($result_ultimo_filho)) {
                $data_ref = date("Y-m-d");
                $diferenca = strtotime($data_ref) - strtotime($row['tbl_animal_data_nascimento']);
                $dias_nascimento = floor($diferenca / (60 * 60 * 24));

                $ultimo_filho_cache[$row['tbl_animal_codigo_mae']] = [
                    'id_filho' => $row['tbl_animal_codigo_id'],
                    'data_nascimento' => $row['tbl_animal_data_nascimento'],
                    'ativo' => $row['tbl_animal_ativo'],
                    'pai_filho' => $row['tbl_animal_codigo_pai'],
                    'dias_nascimento_filho' => $dias_nascimento
                ];            
            }            
        } 
    //}

    if (!empty($ids_femeas)) {
        $dias_gestacao = 282;

        // Converte os IDs de fêmeas para uma string segura
        $ids_femeas_string = implode(',', array_map('intval', $ids_femeas));

        // Passo 1: Consulta Única para pegar a última cobertura de cada fêmea
        $sql_coberturas = "
            SELECT T1.*, T2.*
            FROM tbl_item_cobertura AS T1
            INNER JOIN tbl_cobertura AS T2 ON T2.tbl_cobertura_id = T1.tbl_ite_cobertura_numero_id
            INNER JOIN (
                SELECT tbl_ite_cobertura_codigo_id_animal, MAX(tbl_cobertura_incluido_em) AS ultimo_registro
                FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_ite_cobertura_codigo_id_animal IN ({$ids_femeas_string})
                    AND tbl_cobertura_lixeira = 0
                    AND tbl_ite_cobertura_resultado_diagnostico = 'P'" . $cobertura_controle . "
                GROUP BY tbl_ite_cobertura_codigo_id_animal
            ) AS subquery
            ON T1.tbl_ite_cobertura_codigo_id_animal = subquery.tbl_ite_cobertura_codigo_id_animal
            AND T2.tbl_cobertura_incluido_em = subquery.ultimo_registro";

           // print_r($sql_coberturas);

        $res_coberturas = mysqli_query($conector, $sql_coberturas);
        $coberturas_por_femea = [];
        $ids_coberturas_c = [];
        $ids_protocolos_c = [];

        while ($row = mysqli_fetch_object($res_coberturas)) {
            $coberturas_por_femea[$row->tbl_ite_cobertura_codigo_id_animal] = $row;
            // Coleta os IDs para as próximas consultas
            if ($row->tbl_cobertura_controle == 'C') {
                $ids_coberturas_c[] = $row->tbl_cobertura_id;
                $ids_protocolos_c[] = $row->tbl_cobertura_protocoloiatf;
            }
        }

        // Passo 2: Otimiza as consultas para o controle 'C' fora do loop
        $protocolos_cobertura = [];
        if (!empty($ids_coberturas_c)) {
            $ids_coberturas_c_string = implode(',', array_map('intval', $ids_coberturas_c));
            $sql_protocolo = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura WHERE tbl_protocolo_cobertura_codigo_id IN ({$ids_coberturas_c_string})");
            while ($reg = mysqli_fetch_object($sql_protocolo)) {
                $protocolos_cobertura[$reg->tbl_protocolo_cobertura_codigo_id] = $reg;
            }
        }

        $itens_protocolo = [];
        if (!empty($ids_protocolos_c)) {
            $ids_protocolos_c_string = implode(',', array_map('intval', $ids_protocolos_c));
            $sql_itens = mysqli_query($conector, "SELECT * FROM tbl_item_protocoloiatf WHERE tbl_ite_protocoloiatf_lixeira = 0 AND tbl_ite_protocoloiatf_protocolo_id IN ({$ids_protocolos_c_string}) ORDER BY tbl_ite_protocoloiatf_id ASC");
            while ($reg = mysqli_fetch_object($sql_itens)) {
                $itens_protocolo[$reg->tbl_ite_protocoloiatf_protocolo_id][] = $reg;
            }
        }
        
        // Passo 3: Agora, itere sobre as fêmeas e use os dados já carregados
        foreach ($ids_femeas as $femea) {
            $data_previsao_parto = 0;
            
            if (isset($coberturas_por_femea[$femea])) {
                $reg_cobertura = $coberturas_por_femea[$femea];
                
                if ($reg_cobertura->tbl_cobertura_controle == 'C') {
                    // Acesse os dados dos arrays pré-carregados
                    $reg_protocolo_cobertura = $protocolos_cobertura[$reg_cobertura->tbl_cobertura_id];
                    $data_protocolo_cobertura = $reg_protocolo_cobertura->tbl_protocolo_cobertura_data;
                    $reg_itens_iatf_array = $itens_protocolo[$reg_cobertura->tbl_cobertura_protocoloiatf];

                    foreach ($reg_itens_iatf_array as $reg_itens_iatf) {
                        $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);
                        $data_servico = date("Y-m-d", strtotime($data_protocolo_cobertura . "+{$dias} days"));
                        $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_gestacao} days"));
                    }
                } else {
                    $data_previsao_parto = $reg_cobertura->tbl_ite_cobertura_previsao_parto;
                }
            }
            
            $dados_previsao_partos[$femea] = $data_previsao_parto;
        }
    } else {
        $previsao_parto_de = '1900-01-01';
        $previsao_parto_ate = '9999-99-99';
        $data_previsao_parto = '1900-01-01';
    }    

    $dados_maes = [];

    if (!empty($codigos_maes)) {
        $sql_maes = "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa, tbl_animal_codigo_numerico FROM tbl_animais WHERE tbl_animal_codigo_id IN (" . implode(',', $codigos_maes) . ")";

        $rs_maes = mysqli_query($conector, $sql_maes);

        while ($reg_mae = mysqli_fetch_object($rs_maes)) {
            $dados_maes[$reg_mae->tbl_animal_codigo_id] = $reg_mae->tbl_animal_codigo_alfa . ' ' . intval($reg_mae->tbl_animal_codigo_numerico);
        }
    }

    $dados_pais_semem = [];
    $dados_pais_animal = [];

    if (!empty($codigos_pais)) {
        $sql_pais_semem = "SELECT tbl_semem_codigo_id, tbl_semem_nome FROM tbl_semem WHERE tbl_semem_codigo_id IN (" . implode(',', $codigos_pais) . ")";

        $rs_pais_semem = mysqli_query($conector, $sql_pais_semem);

        while ($reg_pai_semem = mysqli_fetch_object($rs_pais_semem)) {
            $dados_pais_semem[$reg_pai_semem->tbl_semem_codigo_id] = $reg_pai_semem->tbl_semem_nome;
        }

        $sql_pais_animal = "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa, tbl_animal_codigo_numerico FROM tbl_animais WHERE tbl_animal_codigo_id IN (" . implode(',', $codigos_pais) . ")";

        $rs_pais_animal = mysqli_query($conector, $sql_pais_animal);

        while ($reg_pai_animal = mysqli_fetch_object($rs_pais_animal)) {
            $dados_pais_animal[$reg_pai_animal->tbl_animal_codigo_id] = $reg_pai_animal->tbl_animal_codigo_alfa . ' ' . intval($reg_pai_animal->tbl_animal_codigo_numerico);
        }
    }

    $dados_racas = [];

    if (!empty($codigos_racas)) {
        $sql_racas = "SELECT tab_codigo_raca, tab_descricao_raca FROM tabela_racas WHERE tab_codigo_raca IN (" . implode(',', $codigos_racas) . ")";

        $rs_racas = mysqli_query($conector, $sql_racas);

        while ($reg_racas = mysqli_fetch_object($rs_racas)) {
            $dados_racas[$reg_racas->tab_codigo_raca] = $reg_racas->tab_descricao_raca;
        }
    }

    $dados_pelagem = [];

    if (!empty($codigos_pelagem)) {
        $sql_pelagem = "SELECT tab_codigo_pelagem , tab_descricao_pelagem FROM tabela_pelagens WHERE tab_codigo_pelagem IN (" . implode(',', $codigos_pelagem) . ")";

        $rs_pelagens = mysqli_query($conector, $sql_pelagem);

        while ($reg_pelagens = mysqli_fetch_object($rs_pelagens)) {
            $dados_pelagem[$reg_pelagens->tab_codigo_pelagem] = $reg_pelagens->tab_descricao_pelagem;
        }
    }

    $dados_origem = [];

    if (!empty($codigos_origem)) {
        $sql_origem = "SELECT tbl_pessoa_id , tbl_pessoa_nome FROM tbl_pessoa WHERE tbl_pessoa_id IN (" . implode(',', $codigos_origem) . ")";

        $rs_origem = mysqli_query($conector, $sql_origem);

        while ($reg_origem = mysqli_fetch_object($rs_origem)) {
            $dados_origem[$reg_origem->tbl_pessoa_id] = $reg_origem->tbl_pessoa_nome;
        }
    }

    $animais = [];
    $animais_listados=0;

    // Retornar a consulta para o início para poder reutilizar os dados
    //mysqli_data_seek($tbl_animais, 0);

    //while ($reg_animal = mysqli_fetch_object($tbl_animais)){

    mysqli_free_result($tbl_animais);
    unset($tbl_animais);    

    foreach ($dados_animais as $reg_animal) {
        $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
        $desc_local = $reg_animal->tbl_pessoa_nome;
        $codigo_mae_id = $reg_animal->tbl_animal_codigo_mae; 
        $codigo_pai_id = $reg_animal->tbl_animal_codigo_pai; 
        $codigo_origem_id = $reg_animal->tbl_animal_codigo_origem; 
        $descricao_origem = isset($dados_origem[$reg_animal->tbl_animal_codigo_origem]) ? $dados_origem[$reg_animal->tbl_animal_codigo_origem] : '';
        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
        $data_baixa = $reg_animal->tbl_animal_baixado_em;

        if ($data_baixa!='') {
            $data_acompanhamento_calculo = date($data_baixa);
        }
        else {
            $data_acompanhamento_calculo = date("Y-m-d");
        }

        $date = new DateTime($data_nascimento);
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade_acompanhamento_mostra_dias = $idade_acompanhamento->format('%d');

        $idade_ano = $idade_acompanhamento->format('%Y');
        $idade_mes = $idade_acompanhamento->format('%m');
        $idade_dia = $idade_acompanhamento->format('%d');

        if ($idade_ano==0 && $idade_mes!=0) {
            $idade_animal = $idade_mes . ' mes(es)';
        }
        else if ($idade_ano!=0 && $idade_mes==0){
            $idade_animal = $idade_ano . ' ano(s)';
        }
        else if ($idade_ano!=0 && $idade_mes!=0) {
            $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
        }
        else if ($idade_ano==0 && $idade_mes==0){
            $idade_animal = $idade_dia . ' dia(s)';
        }
        else {
            $idade_animal = '';
        }

        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        for($i = 0; $i < count($arrayCategorias); $i++){
            $id_categoria = $arrayCategorias[$i]['id'];
            $idade_de = $arrayCategorias[$i]['idade_de'];
            $idade_ate = $arrayCategorias[$i]['idade_ate'];

            if ($idade >= $idade_de && $idade <= $idade_ate) {
                $codigo_categoria = $id_categoria;

                if ($idade_ate==999999999) {
                    $desc_categoria=' > 36 meses';
                }
                else {
                    $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
                }
            }
        }                        

        $selecionadaCoberturaControle = '';
        $selecionadaCobertura = '';
        $diagnostico = '';
        $nascido = '';
        $id_estacao = '';
        $desc_estacao = '';

        // Verifica femea selecionada na estacao ou monta 
        if (!empty($femea_selecionada_cobertura)) {
            $id_animal = $reg_animal->tbl_animal_codigo_id;

            if (array_key_exists($id_animal, $femea_selecionada_cobertura)) {
                $dados = $femea_selecionada_cobertura[$id_animal];
                $selecionadaCobertura = $dados['selecionada'];
                $selecionadaCoberturaControle = $dados['controle'];
                $diagnostico = $dados['diagnostico'];
                $nascido = $dados['nascido'];
                $id_estacao = $dados['estacao_id'];
                $desc_estacao = $dados['desc_estacao'];
            }
            else {
                $selecionadaCobertura = '';
                $selecionadaCoberturaControle = '';
                $diagnostico = '';
                $nascido = '';
                $id_estacao = '';
                $desc_estacao = '';
            }
        }

        if ($filtro_descarte=='') {
            $filtroDescarte = 'S';
        }
        else {
            $filtroDescarte = 'N';

            if (!empty($dados_descarte)) {
                $id_animal = $reg_animal->tbl_animal_codigo_id;

                if (array_key_exists($id_animal, $dados_descarte)) {
                    if ($dados_descarte[$id_animal]=='S') {
                        $filtroDescarte = 'S';
                    }
                }
            }
        }

        if ($filtro_num_parto=='N') {
            $filtroNumPartos = 'S';
            $num_partos = 0;

            if (!empty($dados_partos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_partos = $dados_partos[$reg_animal->tbl_animal_codigo_id];
            }
        }
        else {
            $filtroNumPartos = 'N';
            $num_partos = 0;

            if (!empty($dados_partos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_partos = $dados_partos[$reg_animal->tbl_animal_codigo_id];

                if ($filtro_reproducao=='S') {
                    if ($num_partos>=$num_parto_de && $num_partos<=$num_parto_ate) {
                        $filtroNumPartos = 'S';
                    }
                }
            }
        }

        if ($filtro_num_aborto=='N') {
            $filtroNumAbortos = 'S';
            $num_abortos = 0;

            if (!empty($dados_abortos) && $reg_animal->tbl_animal_sexo=='F') {
                 $num_abortos = $dados_abortos[$reg_animal->tbl_animal_codigo_id];
            }
        }
        else {
            $filtroNumAbortos = 'N';
            $num_abortos = 0;

            if (!empty($dados_abortos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_abortos = $dados_abortos[$reg_animal->tbl_animal_codigo_id];

                if ($filtro_reproducao=='S') {
                    if ($num_abortos>=$num_aborto_de && $num_abortos<=$num_aborto_ate) {
                        $filtroNumAbortos = 'S';
                    }
                }
            }
        }

        // recupera numeros Natimortos
        if ($filtro_num_natimorto=='N') {
            $filtroNumNatimortos = 'S';
            $num_natimortos = 0;

            if (!empty($dados_natimortos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_natimortos = $dados_natimortos[$reg_animal->tbl_animal_codigo_id];
            }
        }
        else {
            $filtroNumNatimortos = 'N';
            $num_natimortos = 0;

            if (!empty($dados_natimortos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_natimortos = $dados_natimortos[$reg_animal->tbl_animal_codigo_id];
            
                if ($filtro_reproducao=='S') {
                    if ($num_natimortos>=$num_natimorto_de && $num_natimortos<=$num_natimorto_ate) {
                        $filtroNumNatimortos = 'S';
                    }
                }
            }
        }

        if ($filtro_previsao_parto=='N') {
            $filtroDataPrevisao = 'S';
            $data_previsao_parto = 0;

            if (!empty($dados_previsao_partos)) {
                if ($reg_animal->tbl_animal_sexo=='F') {
                    $data_previsao_parto = $dados_previsao_partos[$reg_animal->tbl_animal_codigo_id];
                }
            }
        }
        else {
            if (!empty($dados_previsao_partos)) {
                if ($reg_animal->tbl_animal_sexo=='F') {
                    $filtroDataPrevisao = 'N';
                    $data_previsao_parto = 0;

                    $data_previsao_parto = $dados_previsao_partos[$reg_animal->tbl_animal_codigo_id];

                    if ($filtro_reproducao=='S') {
                        if ($data_previsao_parto>=$previsao_parto_de && 
                            $data_previsao_parto<=$previsao_parto_ate) {
                            $filtroDataPrevisao = 'S';
                        }
                    }
                }
                else {
                    $filtroDataPrevisao = 'N';
                    $data_previsao_parto = 0;
                }
            }
            else {
                $filtroDataPrevisao = 'N';
                $data_previsao_parto = 0;
            }
        }

        if ($filtro_data_paricao=='N') {
            $filtroDataParicao = 'S';
        }
        else {
            $filtroDataParicao = 'N';

            if (!empty($dados_data_partos) && $reg_animal->tbl_animal_sexo=='F') {
                if ($filtro_reproducao=='S') {
                    if (array_key_exists($reg_animal->tbl_animal_codigo_id, $dados_data_partos)) {
                        if ($dados_data_partos[$reg_animal->tbl_animal_codigo_id]=='S') {
                            $filtroDataParicao = 'S';
                        }
                    }
                }
            }
        }

        // VACAS POSITIVAS
        if ($filtro_positivas=='N') {
            $filtroVacasPositivas = 'S';
        }
        else {
            $codigo_mae = $reg_animal->tbl_animal_codigo_id;

            if (array_key_exists($codigo_mae, $femeas_prenhes)) {
                $filtroVacasPositivas = 'S';
            }
            else {
                $filtroVacasPositivas = 'N';
            }
        }

        // VACAS NEGATIVAS
        if ($filtro_negativas=='N') {
            $filtroVacasNegativas = 'S';
        }
        else {
            $codigo_mae = $reg_animal->tbl_animal_codigo_id;

            if (array_key_exists($codigo_mae, $femeas_negativas)) {
                $filtroVacasNegativas = 'S';
            }
            else {
                $filtroVacasNegativas = 'N';
            }
        }

        // VACAS PRENHES
        if ($filtro_vacas_prenhas=='N') {
            $filtroVacasPrenhes = 'S';
        }
        else {
            $codigo_mae = $reg_animal->tbl_animal_codigo_id;

            if (array_key_exists($codigo_mae, $femeas_prenhes)) {
                $filtroVacasPrenhes = 'S';
            }
            else {
                $filtroVacasPrenhes = 'N';
            }
        }

        // VACAS PARIDAS
        if ($filtro_vacas_paridas=='N') {
            $filtroVacasParidas = 'S';
        }
        else {
            $filtroVacasParidas = 'N';

            if ($reg_animal->tbl_animal_sexo=='F' && $idade>=12) {

                $codigo_mae = $reg_animal->tbl_animal_codigo_id;

                if (!empty($dados_femeas_aborto_natimorto)) {
                    if (array_key_exists($codigo_mae, $dados_femeas_aborto_natimorto)) {
                        $dados_aborto = $dados_femeas_aborto_natimorto[$codigo_mae];
                        $teve_aborto_natimorto = $dados_aborto['teve_aborto_natimorto'];
                        $dias_aborto_natimorto = $dados_aborto['dias_aborto_natimorto'];
                    }
                    else {
                        $teve_aborto_natimorto = 'N';
                        $dias_aborto_natimorto = 0;
                    }
                }
                else {
                    $teve_aborto_natimorto = 'N';
                    $dias_aborto_natimorto = 0;
                }

                if (!empty($ultimo_filho_cache)) {
                    if (array_key_exists($codigo_mae, $ultimo_filho_cache)) {

                        $dados_filho = $ultimo_filho_cache[$codigo_mae];
                        $id_filho = $dados_filho['id_filho'];
                        $data_nascimento_filho = $dados_filho['data_nascimento'];
                        $filho_ativo = $dados_filho['ativo'];
                        $pai_filho = $dados_filho['pai_filho'];
                        $dias_nascimento = $dados_filho['dias_nascimento_filho'];

                        $data_acompanhamento_calculo = date("Y-m-d");
                        $date_nascimento = new DateTime($data_nascimento_filho);
                        $idade_acompanhamento = $date_nascimento->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade_filho = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                        if ($idade_filho < 8) {
                            if ($filho_ativo=='S') {
                                $filtroVacasParidas = 'S';
                            }
                            else {
                                if ($teve_aborto_natimorto=='S' && $dias_aborto_natimorto<=35) {
                                    $filtroVacasParidas = 'S';
                                }

                                if ($dias_nascimento<=35) {
                                    $filtroVacasParidas = 'S';
                                }
                            }
                        }
                        else {
                            if ($teve_aborto_natimorto=='S' && $dias_aborto_natimorto<=35) {
                                $filtroVacasParidas = 'S';
                            }
                        }
                    }
                    else {
                        if ($teve_aborto_natimorto=='S' && $dias_aborto_natimorto<=35) {
                            $filtroVacasParidas = 'S';
                        }
                    }
                }
                else {
                    if ($teve_aborto_natimorto=='S' && $dias_aborto_natimorto<=35) {
                        $filtroVacasParidas = 'S';
                    }
                }
            }
        }

        // DADOS ULTIMO FILHO
        $id_filho = 0;
        $data_nascimento_filho = 0;
        $filho_ativo = '';
        $pai_filho = 0;
        $dias_nascimento = 0;

        if ($reg_animal->tbl_animal_sexo=='F' && $idade>=12) {
            $codigo_mae = $reg_animal->tbl_animal_codigo_id;

            if (!empty($ultimo_filho_cache)) {
                if (array_key_exists($codigo_mae, $ultimo_filho_cache)) {
                    $dados_filho = $ultimo_filho_cache[$codigo_mae];
                    $id_filho = $dados_filho['id_filho'];
                    $data_nascimento_filho = $dados_filho['data_nascimento'];
                    $filho_ativo = $dados_filho['ativo'];
                    $pai_filho = $dados_filho['pai_filho'];
                    $dias_nascimento = $dados_filho['dias_nascimento_filho'];
                }
            }
        }

        // DADOS ABORTO/NATIMORTO
        $data_aborto_natimorto = 0;

        if ($reg_animal->tbl_animal_sexo=='F' && $idade>=12) {
            $codigo_mae = $reg_animal->tbl_animal_codigo_id;

            if (!empty($dados_femeas_aborto_natimorto)) {
                if (array_key_exists($codigo_mae, $dados_femeas_aborto_natimorto)) {
                    $dados_aborto_natimorto = $dados_femeas_aborto_natimorto[$codigo_mae];
            
                    $data_aborto_natimorto = $dados_aborto_natimorto['data_aborto_natimorto'];
                }
            }
        }

        // VACAS SOLTEIRAS
        if ($filtro_vacas_solteiras=='N') {
            $filtroVacasSolteiras = 'S';
        }
        else {
            $filtroVacasSolteiras = 'N';
            if ($reg_animal->tbl_animal_sexo=='F' && $idade>=12) {

                $codigo_mae = $reg_animal->tbl_animal_codigo_id;

                if (!empty($dados_femeas_aborto_natimorto)) {
                    if (array_key_exists($codigo_mae, $dados_femeas_aborto_natimorto)) {
                        $dados_aborto = $dados_femeas_aborto_natimorto[$codigo_mae];
                        $teve_aborto_natimorto = $dados_aborto['teve_aborto_natimorto'];
                        $dias_aborto_natimorto = $dados_aborto['dias_aborto_natimorto'];
                    }
                    else {
                        $teve_aborto_natimorto = 'N';
                        $dias_aborto_natimorto = 0;
                    }
                }
                else {
                    $teve_aborto_natimorto = 'N';
                    $dias_aborto_natimorto = 0;
                }

                if (!empty($ultimo_filho_cache)) {
                    if (array_key_exists($codigo_mae, $ultimo_filho_cache)) {

                        $dados_filho = $ultimo_filho_cache[$codigo_mae];
                        $id_filho = $dados_filho['id_filho'];
                        $data_nascimento_filho = $dados_filho['data_nascimento'];
                        $filho_ativo = $dados_filho['ativo'];

                        $data_acompanhamento_calculo = date("Y-m-d");
                        $date_nascimento = new DateTime($data_nascimento_filho);
                        $idade_acompanhamento = $date_nascimento->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade_filho = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                        $data_ref = date("Y-m-d");
                        $diferenca = strtotime($data_ref) - strtotime($data_nascimento_filho);
                        $dias_nascimento = floor($diferenca / (60 * 60 * 24));

                        if ($idade_filho < 8) {
                            if ($filho_ativo=='N') {

                                if ($dias_nascimento<=35) {
                                    $filtroVacasSolteiras = 'N';    
                                }
                                else {
                                    $filtroVacasSolteiras = 'S';    
                                }
                            }
                        }
                        else {
                            if ($teve_aborto_natimorto=='N' || $dias_aborto_natimorto>35) {
                                $filtroVacasSolteiras = 'S';
                            }
                        }
                    }
                    else {
                        if ($teve_aborto_natimorto=='N' || $dias_aborto_natimorto>35) {
                            $filtroVacasSolteiras = 'S';
                        }
                    }
                }
                else {
                    if ($teve_aborto_natimorto=='N' || $dias_aborto_natimorto>35) {
                        $filtroVacasSolteiras = 'S';
                    }
                }
            }

            if ($filtroVacasSolteiras == 'S') {
                if (!empty($femeas_prenhes)) {
                    if (array_key_exists($codigo_mae, $femeas_prenhes)) {
                        $filtroVacasSolteiras = 'N';
                    }
                }
            }
        }

        $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
        $descricao_raca = isset($dados_racas[$reg_animal->tbl_animal_codigo_raca]) ? $dados_racas[$reg_animal->tbl_animal_codigo_raca] : '';

        $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
        $descricao_pelagem = isset($dados_pelagem[$reg_animal->tbl_animal_codigo_pelagem]) ? $dados_pelagem[$reg_animal->tbl_animal_codigo_pelagem] : '';

        if ($codigo_pelagem == '' || 
            $codigo_pelagem==999) {
            $codigo_pelagem = '000';
        }

        $codigo_mae_alfa_numerico = isset($dados_maes[$reg_animal->tbl_animal_codigo_mae]) ? $dados_maes[$reg_animal->tbl_animal_codigo_mae] : '';

        $codigo_pai_alfa_numerico = '';

        if (isset($dados_pais_semem[$reg_animal->tbl_animal_codigo_pai])) {
            $codigo_pai_alfa_numerico = $dados_pais_semem[$reg_animal->tbl_animal_codigo_pai];
        } 
        elseif (isset($dados_pais_animal[$reg_animal->tbl_animal_codigo_pai])) {
            $codigo_pai_alfa_numerico = $dados_pais_animal[$reg_animal->tbl_animal_codigo_pai];
        }

        switch ($reg_animal->tbl_animal_situacao) {
            case 'V':
                $desc_situacao = 'Vendido';
                break;
            case 'M':
                $desc_situacao = 'Morte';
                break;
            case 'T':
                $desc_situacao = 'Aguardando Transf';
                break;
            case 'S':
                $desc_situacao = 'Outra Saída';
                break;
            default:
                $desc_situacao = '';
                break;
        }

        if ($reg_animal->tbl_animal_data_primeiro_peso=='') {
            $data_peso_nasc_edi = '';
        }
        else {
            $data_peso_nasc=new DateTime($reg_animal->tbl_animal_data_primeiro_peso);
            $data_peso_nasc_edi = $data_peso_nasc->format('d/m/Y');
        } 

        if ($reg_animal->tbl_animal_data_desmama=='') {
            $data_peso_desmama_edi = '';
        }
        else {
            $data_peso_desmama=new DateTime($reg_animal->tbl_animal_data_desmama);
            $data_peso_desmama_edi = $data_peso_desmama->format('d/m/Y');
        } 


        if ($reg_animal->tbl_animal_data_ultimo=='') {
            $data_peso_ultimo_edi = '';
        }
        else {
            $data_peso_ultimo=new DateTime($reg_animal->tbl_animal_data_ultimo);
            $data_peso_ultimo_edi = $data_peso_ultimo->format('d/m/Y');
        } 


        $incluido_em=new DateTime($reg_animal->tbl_animal_incluido_em);
        $incluido_por=$reg_animal->tbl_animal_incluido_por; 
        $alterado_em=new DateTime($reg_animal->tbl_animal_alterado_em);
        $alterado_por=$reg_animal->tbl_animal_alterado_por; 
        $baixado_em=new DateTime($reg_animal->tbl_animal_baixado_em);
        $baixado_por=$reg_animal->tbl_animal_baixado_por; 
        $descarte_em=new DateTime($reg_animal->tbl_animal_descarte_em);
        $descarte_por=$reg_animal->tbl_animal_descarte_por; 

        $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
        $alterado_em_edi = $alterado_em->format('d/m/Y H:i:s');
        $baixado_em_edi = $baixado_em->format('d/m/Y H:i:s');
        $descarte_em_edi = $descarte_em->format('d/m/Y H:i:s');


        $movimentacao_compra = $reg_animal->tbl_animal_movimentacao_compra;

        if ($movimentacao_compra!='') {
            $tbl_compra = mysqli_query($conector, "select * from tbl_movimentacao
                where tbl_movimentacao_id='$movimentacao_compra'");
            $num_rows = mysqli_num_rows($tbl_compra);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tbl_compra);
                $data_compra = new DateTime($reg->tbl_movimentacao_data);
                $data_compra = $data_compra->format('d/m/Y');
                $origem_compra = $reg->tbl_movimentacao_codigo_local_origem;
                $destino_compra = $reg->tbl_movimentacao_codigo_local_destino;

                $tbl_origem = mysqli_query($conector, "select * from tbl_pessoa
                    where tbl_pessoa_id='$origem_compra'");
                $num_rows = mysqli_num_rows($tbl_origem);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tbl_origem);
                    $desc_origem_compra = $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_origem_compra = '';
                }

                $tbl_destino = mysqli_query($conector, "select * from tbl_pessoa
                    where tbl_pessoa_id='$destino_compra'");
                    $num_rows = mysqli_num_rows($tbl_destino);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tbl_destino);
                    $desc_destino_compra = $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_destino_compra = '';
                }
            }
            else {
                $data_compra = '';
                $desc_origem_compra = '';
                $desc_destino_compra = '';
            }
        }
        else {
            $data_compra = '';
            $desc_origem_compra = '';
            $desc_destino_compra = '';
        }

        /*if ($wcategoria=="") {
            $animalAtual = [
            'codigoLocal' => $reg_animal->tbl_animal_codigo_fazenda,
            'nomeFazenda' => $desc_local,
            'codigoAnimal' => $reg_animal->tbl_animal_codigo_id,
            'codigoAlfa' => $reg_animal->tbl_animal_codigo_alfa,
            'codigoNumerico' => intval($reg_animal->tbl_animal_codigo_numerico),
            'sexo' => $reg_animal->tbl_animal_sexo,
            'ativo' => $reg_animal->tbl_animal_ativo,
            'situacao' => $desc_situacao,
            'descSituacao' => $desc_situacao,
            'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
            'codigoRaca' => $reg_animal->tbl_animal_codigo_raca,
            'descricaoRaca' => $descricao_raca,
            'codigoPelagem' => $codigo_pelagem,
            'descricaoPelagem' => $descricao_pelagem,
            'codigoMae' => $codigo_mae_id,
            'codigoMaeAlfaNumerico' => $codigo_mae_alfa_numerico,
            'nomeMae' => $reg_animal->tbl_animal_nome_mae,
            'codigoPai' => $codigo_pai_id,
            'codigoPaiAlfaNumerico' => $codigo_pai_alfa_numerico,
            'nomePai' => $reg_animal->tbl_animal_nome_pai,
            'idadeAnimal' => $idade_animal,
            'codigoCategoria' => $codigo_categoria,
            'descricaoCategoria' => $desc_categoria,
            'observacao' => $reg_animal->tbl_animal_observacao,
            'codigoOrigem' => $reg_animal->tbl_animal_codigo_origem,
            'descricaoOrigem' => $descricao_origem,
            'reprodutor' =>  $reg_animal->tbl_animal_reprodutor,
            'dataNascimento' => $reg_animal->tbl_animal_data_nascimento,
            'idadeMeses' => $idade,
            'primeiroPeso' => $reg_animal->tbl_animal_primeiro_peso,
            'dataPrimeiroPeso' => $reg_animal->tbl_animal_data_primeiro_peso,
            'pesoDesmama' => $reg_animal->tbl_animal_peso_desmama,
            'dataPesoDesmana' => $reg_animal->tbl_animal_data_desmama,
            'ultimoPeso' => $reg_animal->tbl_animal_ultimo_peso,
            'dataUltimoPeso' => $reg_animal->tbl_animal_data_ultimo,
            'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
            'incluidoEm' => $incluido_em_edi,
            'incluidoPor' => $incluido_por,
            'alteradoEm' =>  $alterado_em_edi,
            'alteradoPor' => $alterado_por,
            'baixadoEm' => $baixado_em_edi,
            'baixadoPor' => $baixado_por,
            'descarteEm' => $descarte_em_edi,
            'descartePor' => $descarte_por,
            'grauSangue' =>  $reg_animal->tbl_animal_grau_sangue,
            'filtroNumPartos' => $filtroNumPartos,
            'numPartos' => $num_partos,
            'filtroNumAbortos' => $filtroNumAbortos,
            'numAbortos' => $num_abortos,
            'numNatimortos' => $num_natmortos,
            'dataAbortoNatimorto' => $data_aborto_natimorto,
            'dataPrevisaoParto' => $data_previsao_parto,
            'filtroDataPrevisao' => $filtroDataPrevisao,
            'codigoMovimentacaoCompra' => $movimentacao_compra,
            'dataCompra' => $data_compra,
            'origemCompra' => $desc_origem_compra,
            'destinoCompra' => $desc_destino_compra,
            'filtroDataParicao' => $filtroDataParicao,
            'filtroVacasParidas' => $filtroVacasParidas,
            'filtroVacasSolteiras' => $filtroVacasSolteiras,
            'filtroDescarte' => $filtroDescarte,
            'filtroVacasPrenhes' => $filtroVacasPrenhes,
            'filtroVacasNegativas' => $filtroVacasNegativas,
            'filtroVacasPositivas' => $filtroVacasPositivas,
            'selecionadaCobertura' => $selecionadaCobertura,
            'selecionadaCoberturaControle' => $selecionadaCoberturaControle,
            'diagnostico' => $diagnostico,
            'nascido' => $nascido,
            'idFilho' => $id_filho,
            'dataNascimentoFilho' => $data_nascimento_filho,
            'filhoAtivo' => $filho_ativo,
            'paiFilho' => $pai_filho,
            'diasNascimentoFilho' => $dias_nascimento,
            'idEstacao' => $id_estacao,
            'desc_estacao' => $desc_estacao
            ];
            $animais[] = $animalAtual;
        }
        else {
            foreach ($wcategoria as $value) {
                if ($value==$codigo_categoria) {
                    $animalAtual = [
                    'codigoLocal' => $reg_animal->tbl_animal_codigo_fazenda,
                    'nomeFazenda' => $desc_local,
                    'codigoAnimal' => $reg_animal->tbl_animal_codigo_id,
                    'codigoAlfa' => $reg_animal->tbl_animal_codigo_alfa,
                    'codigoNumerico' => intval($reg_animal->tbl_animal_codigo_numerico),
                    'sexo' => $reg_animal->tbl_animal_sexo,
                    'ativo' => $reg_animal->tbl_animal_ativo,
                    'situacao' => $desc_situacao,
                    'descSituacao' => $desc_situacao,
                    'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
                    'codigoRaca' => $reg_animal->tbl_animal_codigo_raca,
                    'descricaoRaca' => $descricao_raca,
                    'codigoPelagem' => $codigo_pelagem,
                    'descricaoPelagem' => $descricao_pelagem,
                    'codigoMae' => $codigo_mae_id,
                    'codigoMaeAlfaNumerico' => $codigo_mae_alfa_numerico,
                    'codigoPai' => $codigo_pai_id,
                    'codigoPaiAlfaNumerico' => $codigo_pai_alfa_numerico,
                    'idadeAnimal' => $idade_animal,
                    'codigoCategoria' => $codigo_categoria,
                    'descricaoCategoria' => $desc_categoria,
                    'observacao' => $reg_animal->tbl_animal_observacao,           
                    'codigoOrigem' => $reg_animal->tbl_animal_codigo_origem,
                    'descricaoOrigem' => $descricao_origem,
                    'reprodutor' =>  $reg_animal->tbl_animal_reprodutor,
                    'dataNascimento' => $reg_animal->tbl_animal_data_nascimento,
                    'idadeMeses' => $idade,
                    'primeiroPeso' => $reg_animal->tbl_animal_primeiro_peso,
                    'dataPrimeiroPeso' => $reg_animal->tbl_animal_data_primeiro_peso,
                    'pesoDesmama' => $reg_animal->tbl_animal_peso_desmama,
                    'dataPesoDesmana' => $reg_animal->tbl_animal_data_desmama,
                    'ultimoPeso' => $reg_animal->tbl_animal_ultimo_peso,
                    'dataUltimoPeso' => $reg_animal->tbl_animal_data_ultimo,
                    'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
                    'incluidoEm' => $incluido_em_edi,
                    'incluidoPor' => $incluido_por,
                    'alteradoEm' =>  $alterado_em_edi,
                    'alteradoPor' => $alterado_por,
                    'baixadoEm' => $baixado_em_edi,
                    'baixadoPor' => $baixado_por,
                    'descarteEm' => $descarte_em_edi,
                    'descartePor' => $descarte_por,                       
                    'grauSangue' =>  $reg_animal->tbl_animal_grau_sangue,
                    'filtroNumPartos' => $filtroNumPartos,
                    'numPartos' => $num_partos,
                    'filtroNumAbortos' => $filtroNumAbortos,
                    'numAbortos' => $num_abortos,
                    'numNatimortos' => $num_natmortos,
                    'dataAbortoNatimorto' => $data_aborto_natimorto,
                    'dataPrevisaoParto' => $data_previsao_parto,
                    'filtroDataPrevisao' => $filtroDataPrevisao,
                    'codigoMovimentacaoCompra' => $movimentacao_compra,
                    'dataCompra' => $data_compra,
                    'origemCompra' => $desc_origem_compra,
                    'destinoCompra' => $desc_destino_compra,
                    'filtroDataParicao' => $filtroDataParicao,
                    'filtroVacasParidas' => $filtroVacasParidas,
                    'filtroVacasSolteiras' => $filtroVacasSolteiras,
                    'filtroDescarte' => $filtroDescarte,
                    'filtroVacasPrenhes' => $filtroVacasPrenhes,
                    'filtroVacasNegativas' => $filtroVacasNegativas,
                    'filtroVacasPositivas' => $filtroVacasPositivas,
                    'selecionadaCobertura' => $selecionadaCobertura,
                    'selecionadaCoberturaControle' => $selecionadaCoberturaControle,
                    'diagnostico' => $diagnostico,
                    'nascido' => $nascido,
                    'idFilho' => $id_filho,
                    'dataNascimentoFilho' => $data_nascimento_filho,
                    'filhoAtivo' => $filho_ativo,
                    'paiFilho' => $pai_filho,
                    'diasNascimentoFilho' => $dias_nascimento,
                    'idEstacao' => $id_estacao,
                    'desc_estacao' => $desc_estacao
                    ];
                    $animais[] = $animalAtual;
                }
            }
        }*/

        $adicionarAnimal = false; // <<< ESSA LINHA É A CHAVE!        

        // Verifica se o array de categorias ($wcategoria) está VAZIO
        if (empty($wcategoria)) {
            // 1. Se o filtro de categoria está vazio/não aplicado, inclui o animal
            $adicionarAnimal = true;
        } else {
            // 2. Se o filtro TEM categorias selecionadas (não está vazio),
            // verifica se a categoria atual ($codigo_categoria) está dentro delas.
            // É importante garantir que $wcategoria seja um array aqui.
            if (is_array($wcategoria) && in_array($codigo_categoria, $wcategoria)) {
                $adicionarAnimal = true;
            }
        }        

        // 2. Define o array do animal UMA ÚNICA VEZ
        $animalAtual = [
            'codigoLocal' => $reg_animal->tbl_animal_codigo_fazenda,
            'nomeFazenda' => $desc_local,
            'codigoAnimal' => $reg_animal->tbl_animal_codigo_id,
            'codigoAlfa' => $reg_animal->tbl_animal_codigo_alfa,
            'codigoNumerico' => intval($reg_animal->tbl_animal_codigo_numerico),
            'sexo' => $reg_animal->tbl_animal_sexo,
            'ativo' => $reg_animal->tbl_animal_ativo,
            'situacao' => $reg_animal->tbl_animal_situacao,
            'descSituacao' => $desc_situacao,
            'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
            'codigoRaca' => $reg_animal->tbl_animal_codigo_raca,
            'descricaoRaca' => $descricao_raca,
            'codigoPelagem' => $codigo_pelagem,
            'descricaoPelagem' => $descricao_pelagem,
            'codigoMae' => $codigo_mae_id,
            'codigoMaeAlfaNumerico' => $codigo_mae_alfa_numerico,
            'nomeMae' => $reg_animal->tbl_animal_nome_mae,
            'codigoPai' => $codigo_pai_id,
            'codigoPaiAlfaNumerico' => $codigo_pai_alfa_numerico,
            'nomePai' => $reg_animal->tbl_animal_nome_pai,
            'idadeAnimal' => $idade_animal,
            'codigoCategoria' => $codigo_categoria,
            'descricaoCategoria' => $desc_categoria,
            'observacao' => $reg_animal->tbl_animal_observacao,
            'codigoOrigem' => $reg_animal->tbl_animal_codigo_origem,
            'descricaoOrigem' => $descricao_origem,
            'reprodutor' => $reg_animal->tbl_animal_reprodutor,
            'dataNascimento' => $reg_animal->tbl_animal_data_nascimento,
            'idadeMeses' => $idade,
            'primeiroPeso' => $reg_animal->tbl_animal_primeiro_peso,
            'dataPrimeiroPeso' => $reg_animal->tbl_animal_data_primeiro_peso,
            'pesoDesmama' => $reg_animal->tbl_animal_peso_desmama,
            'dataPesoDesmana' => $reg_animal->tbl_animal_data_desmama,
            'ultimoPeso' => $reg_animal->tbl_animal_ultimo_peso,
            'dataUltimoPeso' => $reg_animal->tbl_animal_data_ultimo,
            'incluidoEm' => $incluido_em_edi,
            'incluidoPor' => $incluido_por,
            'alteradoEm' => $alterado_em_edi,
            'alteradoPor' => $alterado_por,
            'baixadoEm' => $baixado_em_edi,
            'baixadoPor' => $baixado_por,
            'descarteEm' => $descarte_em_edi,
            'descartePor' => $descarte_por,
            'grauSangue' => $reg_animal->tbl_animal_grau_sangue,
            'filtroNumPartos' => $filtroNumPartos,
            'numPartos' => $num_partos,
            'filtroNumAbortos' => $filtroNumAbortos,
            'numAbortos' => $num_abortos,
            'filtroNumNatimortos' => $filtroNumNatimortos,
            'numNatimortos' => $num_natimortos,
            'dataAbortoNatimorto' => $data_aborto_natimorto,
            'dataPrevisaoParto' => $data_previsao_parto,
            'filtroDataPrevisao' => $filtroDataPrevisao,
            'codigoMovimentacaoCompra' => $movimentacao_compra,
            'dataCompra' => $data_compra,
            'origemCompra' => $desc_origem_compra,
            'destinoCompra' => $desc_destino_compra,
            'filtroDataParicao' => $filtroDataParicao,
            'filtroVacasParidas' => $filtroVacasParidas,
            'filtroVacasSolteiras' => $filtroVacasSolteiras,
            'filtroDescarte' => $filtroDescarte,
            'filtroVacasPrenhes' => $filtroVacasPrenhes,
            'filtroVacasNegativas' => $filtroVacasNegativas,
            'filtroVacasPositivas' => $filtroVacasPositivas,
            'selecionadaCobertura' => $selecionadaCobertura,
            'selecionadaCoberturaControle' => $selecionadaCoberturaControle,
            'diagnostico' => $diagnostico,
            'nascido' => $nascido,
            'idFilho' => $id_filho,
            'dataNascimentoFilho' => $data_nascimento_filho,
            'filhoAtivo' => $filho_ativo,
            'paiFilho' => $pai_filho,
            'diasNascimentoFilho' => $dias_nascimento,
            'idEstacao' => $id_estacao,
            'desc_estacao' => $desc_estacao
        ];

        // 3. Adiciona ao array final somente se o animal atendeu aos filtros
        if ($adicionarAnimal) {
            $animais[] = $animalAtual;
        }        
    }

    $codigos_animais = array_column($animais, 'codigoAnimal');
    $codigos_str = "'" . implode("','", $codigos_animais) . "'";

    $sql_total_pesos = "
        SELECT 
            tbl_ite_pesagem_data_emissao,
            tbl_ite_pesagem_peso,
            tbl_ite_pesagem_codigo_id_animal AS codigo_animal
        FROM tbl_item_pesagem
        WHERE tbl_ite_pesagem_data_emissao >= '$data_inicial'
          AND tbl_ite_pesagem_data_emissao <= '$data_final'
          AND tbl_ite_pesagem_codigo_id_animal IN ($codigos_str)
          AND tbl_ite_pesagem_peso != 0
        ORDER BY tbl_ite_pesagem_data_emissao ASC
    ";

    $tbl_peso_total = mysqli_query($conector, $sql_total_pesos);

    // 4. Cria um array de lookup para os pesos
    $pesos_por_animal = [];
    while ($reg_peso = mysqli_fetch_object($tbl_peso_total)) {
        $codigo = $reg_peso->codigo_animal;
        if (!isset($pesos_por_animal[$codigo])) {
            $pesos_por_animal[$codigo] = [];
        }
        // Armazena todos os pesos para cada animal
        $pesos_por_animal[$codigo][] = $reg_peso;
    }

    foreach ($animais as $animal) {
        $codigo_animal = $animal['codigoAnimal'];
        $dados_pesos_do_animal = $pesos_por_animal[$codigo_animal] ?? [];

        if ($filtro_reproducao == 'S') {
            if ($filtro_negativas=='S' && $filtro_positivas=='S') {
                if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' && 
                    $filtro_vacas_prenhas == 'N') { 
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' 
                    && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasParidas']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' &&    $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasSolteiras']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasPrenhes']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                         $animal['filtroDescarte']=='S' &&
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' &&$filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }                
            }
            else {
                if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' && 
                    $filtro_vacas_prenhas == 'N') { 
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' 
                    && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasParidas']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' &&    $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasSolteiras']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasPrenhes']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                         $animal['filtroDescarte']=='S' &&
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' &&$filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 
                         
                        $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
                        $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria'];
                        $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria'];
                        $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria'];
                    }
                }                
            }
        }
        else {
            $dados = listar($conector, $animal, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $dados_pesos_do_animal, $array_gmd_macho_categoria, $array_qtd_macho_categoria, $array_gmd_femea_categoria, $array_qtd_femea_categoria); 

            $array_gmd_macho_categoria = $dados['arrayGmdMachoCategoria'];
            $array_qtd_macho_categoria = $dados['arrayQtdMachoCategoria']; 
            $array_gmd_femea_categoria = $dados['arrayGmdFemeaCategoria']; 
            $array_qtd_femea_categoria = $dados['arrayQtdFemeaCategoria']; 
        }
    }

    for ($i=1; $i <= 5; $i++) { 
        $j = str_pad($i, 3, "0", STR_PAD_LEFT);
        if ($array_qtd_macho_categoria[$j]!=0) {
            echo '<tr>';
            echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';
            echo '<td width="10%" style="text-align: center;">M</td>';
            echo '<td width="10%" style="text-align: center;">'.$array_qtd_macho_categoria[$j].'</td>';

            $gmd = $array_gmd_macho_categoria[$j]/
                   $array_qtd_macho_categoria[$j];

            $gmd_total+= $array_gmd_macho_categoria[$j];

            if ($gmd!=0) {
                $numero_gmd+= $array_qtd_macho_categoria[$j];
            }

            $gmd_edi = number_format($gmd,3,',','.');
            echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';    
            echo '<td style="border: none"></td>';    
            echo '<td style="border: none"></td>';    
            echo '<td style="border: none"></td>';    
            echo '</tr>';
            $animais_listados+=$array_qtd_macho_categoria[$j];
        }

        if ($array_qtd_femea_categoria[$j]!=0) {
            echo '<tr>';
            echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';
            echo '<td width="10%" style="text-align: center;">F</td>';
            echo '<td width="10%" style="text-align: center;">'.$array_qtd_femea_categoria[$j].'</td>';

            $gmd = $array_gmd_femea_categoria[$j]/
                   $array_qtd_femea_categoria[$j];

            $gmd_total+= $array_gmd_femea_categoria[$j];

            if ($gmd!=0) {
                $numero_gmd+= $array_qtd_femea_categoria[$j];
            }

            $gmd_edi = number_format($gmd,3,',','.');
            echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';
            echo '<td style="border: none"></td>';    
            echo '<td style="border: none"></td>';    
            echo '<td style="border: none"></td>';    
            echo '</tr>';
            $animais_listados+=$array_qtd_femea_categoria[$j];
        }
    }

?>

</tbody>

<thead>
<?php
    if ($gmd_total!=0 && $numero_gmd>0) {
        $media_gmd = $gmd_total / $numero_gmd;
        $media_gmd_edi = number_format($media_gmd,3,',','.');
    }
    else {
        $media_gmd_edi = 0;
    }
?>
    <tr>
        <th style="text-align: center">Animais</th>
        <th colspan="3"></th>
        <th style="border: none"></th>
        <th style="border: none"></th>
        <th style="text-align: center">GMD Global</th>
    </tr>
    <tr>
        <td style="text-align: center" class="animais_listados"><?php echo $animais_listados?></td>
        <td colspan="3"></td>
        <td style="border: none"></td>
        <td style="border: none"></td>
        <td style="text-align: center;"><?php echo $media_gmd_edi?></td>
    </tr>
    <tr>
        <th>Categoria</th>
        <th>Sexo</th>
        <th>Qtde</th>
        <th>GMD</th>
        <th style="border: none"></th>
        <th style="border: none"></th>
        <th style="border: none"></th>
    </tr>
</thead>

</table>

                                            </div> <!-- fim container -->
                                        </div> <!-- dados-->
                                    </div> <!--tab-content -->

                                </div> <!--panel-body -->
                            </div> <!--panel -->      
                        </form>
                    </section> <!-- panel-group -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Ganho de Peso</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Ganho de Peso - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="aguardar" tabindex="-1" role="dialog"    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p class="aguardar">Aguarde <i class='fa fa-spinner fa-spin fa-2x' ></i></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> <!-- wrapper -->
    </section><!--main-content -->


<?php 
  $javascript_file_name = 'relatorios_gmd.js';
  require 'rodape.php';
?>




