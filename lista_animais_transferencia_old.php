<?php
    include "conecta_mysql.inc";

    for ($i=1; $i<=21; $i++){
        $valor[$i]=0;
    }

    $matriz_com_itens = 0;
    $numero_do_item=0;
    $animais_listados=0;
    $matriz_itens= array();

    $codigo_alfa_numerico = $_POST["codigo_alfa_numerico"];

    $local = $_POST['local'];

    $worigem = "";
    if (isset($_POST['origem'])) {
        $origem = $_POST['origem'];

        if(in_array("", $origem)) {
            $worigem='';
        }
        else {
            $worigem = " AND tbl_animal_codigo_origem IN(";
            $worigem.= implode(',', $origem);
            $worigem.= ")";
            }
    }
    else {
        $worigem='';
    }

    $wsexo = "";
    if (isset($_POST['sexo'])) {
        $sexo = $_POST['sexo'];

        if(in_array("Todos", $sexo)) {
            $wsexo='';
        }
        else {
            $wsexo = " AND tbl_animal_sexo IN(";
            $wsexo .= "'" . implode("','", $sexo) . "'";
            $wsexo.= ")";
            }
    }
    else {
        $wsexo='';
    }

    $wraca = "";
    if (isset($_POST['raca'])) {
        $raca = $_POST['raca'];

        if(in_array("", $raca)) {
            $wraca='';
        }
        else {
            $wraca = " AND tbl_animal_codigo_raca IN(";
            $wraca.= implode(',', $raca);
            $wraca.= ")";
            }
    }
    else {
        $wraca='';
    }

    $wpai = "";
    if (isset($_POST['pai'])) {
        $pai = $_POST['pai'];

        if(in_array("", $pai)) {
            $wpai='';
        }
        else {
            $wpai = " AND tbl_animal_codigo_pai IN(";
            $wpai.= implode(',', $pai);
            $wpai.= ")";
            }
    }
    else {
        $wpai='';
    }

    $wmae = "";
    if (isset($_POST['mae'])) {
        $mae = $_POST['mae'];

        if(in_array("", $mae)) {
            $wmae='';
        }
        else {
            $wmae = " AND tbl_animal_codigo_mae IN(";
            $wmae.= implode(',', $mae);
            $wmae.= ")";
            }
    }
    else {
        $wmae='';
    }

    $wcategoria = "";
    if (isset($_POST['categoria'])) {
        $categoria_filtro = $_POST['categoria'];

        if(in_array("", $categoria_filtro)) {
            $wcategoria='';
        }
        else {
            //$wcategoria= explode(',', $categoria_filtro);
            $wcategoria= $categoria_filtro;
       }
    }
    else {
        $wcategoria='';
    }

    $peso_nasc_inicial = $_POST["peso_nasc_inicial"];
    $peso_nasc_final = $_POST["peso_nasc_final"];

    $peso_desmama_inicial = $_POST["peso_desmama_inicial"];
    $peso_desmama_final = $_POST["peso_desmama_final"];

    $peso_ult_inicial = $_POST["peso_ult_inicial"];
    $peso_ult_final = $_POST["peso_ult_final"];

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

    $data_nasc_inicial = $_POST["data_nasc_inicial"];
    $data_nasc_final = $_POST["data_nasc_final"];

    if ($data_nasc_inicial==0 && $data_nasc_final==0){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
    }

    $num_parto_de = $_POST['num_parto_de'];
    $num_parto_ate = $_POST['num_parto_ate'];
    $num_aborto_de = $_POST['num_aborto_de'];
    $num_aborto_ate = $_POST['num_aborto_ate'];
    $solteiras_filtro = $_POST["solteiras"];
    $descarte_filtro = $_POST["descarte"];
    $paridas_filtro = $_POST["paridas"];
    $data_paridas_ate = $_POST["data_paridas"];
    $previsao_parto_de = $_POST["previsao_parto_de"];
    $previsao_parto_ate = $_POST["previsao_parto_ate"];
    $positivo = $_POST["positivo"];
    $negativo = $_POST["negativo"];

    if (isset($_POST['estacao'])) {
        $estacao_filtro = $_POST['estacao'];

        $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
            WHERE tbl_par_estacao_nome='$estacao_filtro'
            ORDER BY tbl_par_estacao_id ASC");  

        $num_rows = mysqli_num_rows($sql);
        $array_estacao = array();

        if ($num_rows!=0) {
            while ($reg_estacao = mysqli_fetch_object($sql)){
                $codigo_estacao = $reg_estacao->tbl_par_estacao_id;
                $array_estacao[] = $codigo_estacao;
            }

            $array_estacao = implode(',', $array_estacao);
        }
    }

    $westacao = "";
    if (!empty ($array_estacao)) {
        
        $array_estacao = explode(',', $array_estacao);

        $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(";
        $westacao.= implode(',', $array_estacao);
        $westacao.= ")";
    }

// incializa campos vazios    
    $data_previsao_parto = '0000-00-00';

    if ($previsao_parto_de=='') {
        $previsao_parto_de = '0000-00-00';
        $previsao_parto_ate = '9999-99-99';
    }

    if ($num_parto_de=='') {
        $num_parto_de = 0;
        $num_parto_ate = 999;
    }

    if ($num_aborto_de=='') {
        $num_aborto_de = 0;
        $num_aborto_ate = 999;
    }

    if ($data_paridas_ate=='') {
        $data_paridas_ate='9999-99-99';
        $data_paridas_de='0000-00-00';
    }
    else {
        $data_paridas_de = date("Y-m-d", strtotime('-8 month',strtotime($data_paridas_ate)));
        $data_paridas_de = date("Y-m-d", strtotime('-1 day',strtotime($data_paridas_de)));
    }

    $tem_descarte = '';
    $tem_negativo = '';
    $tem_positivo = '';

    // pega o codigo do pasto curral de saida do local

    $sql = mysqli_query($conector, "SELECT * FROM tbl_pasto
        WHERE tbl_pasto_codigo_local='$local' AND 
              tbl_pasto_modulo=999 AND 
              tbl_pasto_tipo_curral='S'"); 

    $num_rows = mysqli_num_rows($sql);

    if ($num_rows!=0){
        $reg_pasto = mysqli_fetch_object($sql);
        $pasto = $reg_pasto->tbl_pasto_id;
    }
    else {
        $pasto = 0;
    }

    // popular array com animais do pasto de saida do local

    for ($i = 1; $i <=5; $i++) {
        $j = str_pad($i, 3, "0", STR_PAD_LEFT);
        $total_cat_macho[$j]=0;
        $total_cat_femea[$j]=0;
    }

    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_situacao='A' AND
              tbl_animal_pasto_local='$local' AND 
              tbl_animal_pasto_id='$pasto'"); 

    $num_rows = mysqli_num_rows($sql);

    if ($num_rows!=0){
        while ($reg_animal = mysqli_fetch_object($sql)){
            $sexo_animal = $reg_animal->tbl_animal_pasto_sexo;
            //$codigo_categoria = $reg_animal->tbl_animal_pasto_categoria;

            $data_nascimento = $reg_animal->tbl_animal_pasto_nascimento;  
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($categoria);    

            if ($num_rows!=0) {
                while ($reg_categoria = mysqli_fetch_object($categoria)) {
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade >= $idade_de && $idade <= $idade_ate) {
                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

                        if ($sexo_animal=='M') {
                            $total_cat_macho[$codigo_categoria]++;
                        }
                        else {
                            $total_cat_femea[$codigo_categoria]++;
                        }
                    }
                }                   
            }

            /*if ($sexo=='M') {
                $total_cat_macho[$codigo_categoria]++;
            }
            else {
                $total_cat_femea[$codigo_categoria]++;
            }*/
        }
    }

    // Pega estacao atual da fazenda (ultima estacao)
    $tbl_estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
        WHERE tbl_par_codigo_local='$local' AND 
              tbl_par_lixeira=0
        ORDER BY tbl_par_estacao_id DESC LIMIT 1");

    $num_rows_estacao = mysqli_num_rows($tbl_estacao);

    if ($num_rows_estacao!=0) {
        $reg_estacao = mysqli_fetch_object($tbl_estacao);
        $id_estacao_atual = $reg_estacao->tbl_par_estacao_id;
        $desc_estacao_atual = $reg_estacao->tbl_par_estacao_nome;
    }
    else {
        $id_estacao_atual = 0;
    }

    if ($codigo_alfa_numerico!='') {
        $codigo_numerico_consulta = substr($codigo_alfa_numerico, -9);

        if (strlen($codigo_alfa_numerico)!=9){
            $data = explode("-", $codigo_alfa_numerico);
            $codigo_alfa_consulta = $data[0];
        }
        else {
            $codigo_alfa_consulta = '';
        }

        $sql = "SELECT * FROM tbl_animais 
            WHERE tbl_animal_lixeira=0 AND 
                  tbl_animal_codigo_alfa='$codigo_alfa_consulta' AND 
                  tbl_animal_codigo_numerico='$codigo_numerico_consulta' AND 
                  tbl_animal_ativo='S' AND 
                  tbl_animal_codigo_fazenda='$local'"; 
    } 
    else {
        $sql = "SELECT * FROM tbl_animais 
            WHERE tbl_animal_lixeira=0 AND 
                  tbl_animal_ativo='S' AND
                  tbl_animal_codigo_fazenda='$local'" . 
                $worigem . $wraca . $wsexo . $wpai . $wmae . 
                $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .
            " ORDER BY tbl_animal_codigo_numerico ASC"; 

    }

    $rs = mysqli_query($conector, $sql); 

    $num_rows_animais = mysqli_num_rows($rs);

    if ($num_rows_animais==0) {
        echo $num_rows_animais;
        exit;
    }

    echo '
    <table class="table table-striped table-advance table-hover" id="tabela_itens_digitados" width="100%" style="font-size: 13px;">
    <thead>
        <tr>
        <th colspan="12" style="vertical-align: middle; text-align:left; font-size: 10px;">Legenda:&nbsp;&nbsp;<i class="fa fa-square text-primary"></i> &nbsp;Em Estação de Monta '.$desc_estacao_atual.' &nbsp;&nbsp;<i class="fa fa-square" style="color: #060c54;"></i> &nbsp;Esta na Lista Monta Natural</th>
        </tr>

        <tr>
        <th><input type="checkbox" class="seleciona_todos" data-toggle="tooltip" data-placement="right" title="Selecionar Todos"></th>
        <th> <i class="fa fa-sort-alpha-asc"</th>
        <th> Código Numérico</th>
        <th> Categoria</th>
        <th> Sexo</th>
        <th> Nascimento</th>
        <th> Raça</th>
        <th> Pelagem</th>
        <th> Mãe</th>
        <th> Descarte</th>
        <th hidden></th>
        <th hidden></th>
        <th hidden></th>
        <th hidden></th>
        </tr>
    </thead>
    <tbody>
    ';

    while ($reg_animal = mysqli_fetch_object($rs)){
        $codigo_local = $reg_animal->tbl_animal_codigo_fazenda;
        $codigo = $reg_animal->tbl_animal_codigo_id;
        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
        $codigo_numerico = intval($reg_animal->tbl_animal_codigo_numerico);
        $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
        $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
        $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
        $sexo = $reg_animal->tbl_animal_sexo; 
        $mae = $reg_animal->tbl_animal_codigo_mae; 
        $pai = $reg_animal->tbl_animal_codigo_pai; 
        $lixeira = $reg_animal->tbl_animal_lixeira; 
        $situacao = $reg_animal->tbl_animal_situacao; 
        $descarte_reproducao = $reg_animal->tbl_animal_descarte_reproducao; 
        $reprodutor = $reg_animal->tbl_animal_reprodutor; 
        $observacao = $reg_animal->tbl_animal_observacao;
        $observacao = ltrim($observacao);
        $observacao = rtrim($observacao);
        $nascimento = new DateTime($reg_animal->tbl_animal_data_nascimento);

        if ($codigo_alfa=='') {
            $codigo_animal_edi=$codigo_numerico;
        }
        else {
            $codigo_animal_edi=$codigo_alfa.'-'.$codigo_numerico;
        }

        if ($descarte_reproducao=='S') {
            $animal_descarte = 'Sim';
        }
        else {
            $animal_descarte = '';   
        }

        if ($descarte_filtro=='S') {
            if ($descarte_reproducao=='S') {
                $tem_descarte = 'S';
            }
            else {
                $tem_descarte = '';
            }
        }
        else {
            $tem_descarte = '';
        }

        if ($reg_animal->tbl_animal_sexo=='M') {
            $sexo = 'Macho';
        }
        else {
            $sexo = 'Fêmea';
        }

        if ($codigo_pelagem == '' || $codigo_pelagem==999) {
            $codigo_pelagem = '000';
        }

        switch ($situacao) {
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

        $tab_mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$mae'");
        $num_rows = mysqli_num_rows($tab_mae);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_mae);

            if ($reg->tbl_animal_codigo_alfa=='') {
                $descricao_mae = intval($reg->tbl_animal_codigo_numerico);
            }
            else {
                $descricao_mae = $reg->tbl_animal_codigo_alfa. '-' .intval($reg->tbl_animal_codigo_numerico);
            }
        }
        else {
            $descricao_mae = '';
            $mae='000000000';
        }

        /*$tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
        $num_rows = mysqli_num_rows($tab_pai);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_pai);
            $descricao_pai = $reg->tbl_semem_nome;
            $pai = $reg->tbl_semem_codigo_id;
        }
        else {
            $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
            $num_rows = mysqli_num_rows($tab_pai);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pai);
                $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . intval($reg->tbl_animal_codigo_numerico);
            }
            else {
                $descricao_pai = '';
                $pai='000000000';
            }
        }*/

        $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
            $num_rows = mysqli_num_rows($tab_raca);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_raca);
            $descricao_raca = $reg->tab_descricao_raca;
        }
        else {
            $descricao_raca = '';
        }

        $tab_pelagem = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem='$codigo_pelagem'");
            $num_rows = mysqli_num_rows($tab_pelagem);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_pelagem);
            $descricao_pelagem = $reg->tab_descricao_pelagem;
        }
        else {
            $descricao_pelagem = '';
        }

        $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_fazenda'");
        $num_rows = mysqli_num_rows($tab_fazenda);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_fazenda);
            $desc_local = $reg->tbl_pessoa_nome;
        }
        else {
            $desc_local = '';
        }

        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');

        $idade_ano = $idade_acompanhamento->format('%Y');
        $idade_mes = $idade_acompanhamento->format('%m');

        if ($idade_ano==0 && $idade_mes!=0) {
            $idade_animal = $idade_mes . ' mes(es)';
        }
        else if ($idade_ano!=0 && $idade_mes==0){
            $idade_animal = $idade_ano . ' ano(s)';
        }
        else if ($idade_ano!=0 && $idade_mes!=0) {
            $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
        }
        else {
            $idade_animal = '';
        }

        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");

        $num_rows = mysqli_num_rows($categoria);    

        if ($num_rows!=0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $idade_de = $reg_categoria->tab_categoria_idade_de;
                $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                if ($idade >= $idade_de && $idade <= $idade_ate) {
                    $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

                    if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                        $desc_categoria=' > 36 meses';
                    }
                    else {
                        $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
                    }
                }
            }
        }                   

        // primeiro verifica quantos partos
        $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
            WHERE tbl_animal_codigo_mae='$codigo'");

        $num_partos = mysqli_num_rows($tbl_filhos);

        // agora verifica qual o ultimo parto para saber a idade
        $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
            WHERE tbl_animal_codigo_mae='$codigo'
            ORDER BY tbl_animal_data_nascimento DESC limit 1");

        $ultimo_filho = mysqli_num_rows($tbl_filhos);

        if ($paridas_filtro=='S' || $solteiras_filtro=='S') {
            $parida = '';
            $solteira = '';

            if ($ultimo_filho!=0) {
                $reg_filhos = mysqli_fetch_object($tbl_filhos);
                $nascimento_filho = $reg_filhos->tbl_animal_data_nascimento;  
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($nascimento_filho); // Data de Nascimento
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade_ano = $idade_acompanhamento->format('%Y');
                $idade_mes = $idade_acompanhamento->format('%m');
                $idade_filho = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                if ($idade_filho < 8 && $nascimento_filho>=$data_paridas_de && $nascimento_filho<=$data_paridas_ate) {
                    $parida = 'S';
                }
                else {
                    $solteira = 'S';
                }

                $parida_lida = $parida;
                $solteira_lida = $solteira;
            }
            else {
                $parida = '';
                $parida_lida = '';
                $solteira = '';
                $solteira_lida = '';
            }
        }
        else {
            $parida_lida = '';
            $solteira_lida = '';
            $parida = '';
            $solteira = '';

            if ($ultimo_filho!=0) {
                $reg_filhos = mysqli_fetch_object($tbl_filhos);
                $nascimento_filho = $reg_filhos->tbl_animal_data_nascimento;  
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($nascimento_filho); // Data de Nascimento
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade_ano = $idade_acompanhamento->format('%Y');
                $idade_mes = $idade_acompanhamento->format('%m');
                $idade_filho = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                if ($idade_filho < 8 && $nascimento_filho>=$data_paridas_de && $nascimento_filho<=$data_paridas_ate) {
                    $parida = 'S';
                }
                else {
                    $solteira = 'S';
                }
            }
            else {
                $parida = '';
                $solteira = '';
            }
        }

        // verifica a cobertura do animal
        $sql = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
            INNER JOIN tbl_cobertura
                    ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
            INNER JOIN tbl_parametro_estacao_monta
                    ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_ite_cobertura_codigo_id_animal='$codigo'" . $westacao . "
            ORDER BY tbl_ite_cobertura_numero_id DESC limit 1");

        $num_rows = mysqli_num_rows($sql);

        if ($num_rows!=0) {
            $reg_cobertura = mysqli_fetch_object($sql);
            $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
            $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
            $estacao_monta = $reg_cobertura->tbl_par_estacao_nome;
        }
        else {
            $codigo_local = 0;
            $estacao_animal = 0;
            $estacao_monta = '';
        }

        // Verifica diagnostico
        if ($positivo=='S' || $negativo=='S'){

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      ((tbl_cobertura_controle = 'C' AND 
                       tbl_cobertura_codigo_estacao_monta = '$estacao_animal') OR
                      (tbl_cobertura_controle = 'M' AND 
                       tbl_cobertura_codigo_estacao_monta=tbl_cobertura_id))
                ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

            $num_rows = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

                if ($diagnostico=='P'){
                    $tem_positivo = 'S';
                    $tem_negativo = '';
                } 
                else if ($diagnostico=='N') {
                    $tem_negativo = 'S';
                    $tem_positivo = '';
                }
                else {
                    $tem_negativo = '';
                    $tem_positivo = '';
                    $codigo_categoria=0;
                }
            }
            else {
                $tem_negativo = '';
                $tem_positivo = '';
                $codigo_categoria=0;
            }
        }
        else {
            $tem_negativo = '';
            $tem_positivo = '';
        }

        // verifica natimortos, nascidos ou abortos na estacao

        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
            WHERE tbl_cobertura_lixeira=0 AND
                  tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                  ((tbl_cobertura_controle = 'C' AND 
                   tbl_cobertura_codigo_estacao_monta = '$estacao_animal') OR
                  (tbl_cobertura_controle = 'M' AND 
                   tbl_cobertura_codigo_estacao_monta=tbl_cobertura_id)) AND 
                  tbl_ite_cobertura_resultado_diagnostico = 'P' 
            ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

        /*$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
            WHERE tbl_cobertura_lixeira=0 AND
                  tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                  tbl_cobertura_controle = 'C' AND 
                  tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                  tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
            ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); */

        $num_rows_item = mysqli_num_rows($tbl_item_cobertura);

        if ($num_rows_item!=0) {
            $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
            $nascido_aborto = $reg_cobertura->tbl_ite_cobertura_nascido;
        }
        else {
            $nascido_aborto = '';
        }

        if ($positivo=='S' AND 
            $nascido_aborto!='') {
            $tem_positivo='';
        }

        $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
            WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                  tbl_mov_estoque_codigo_id_animal=999999999 AND 
                  tbl_mov_estoque_entrada_saida='E' AND
                  tbl_mov_estoque_tipo_movimentacao='N'");
        $num_natimorto = mysqli_num_rows($tbl_natimorto);

        $num_partos = $num_partos + $num_natimorto;

        /*$data_hoje = date('Y-m-d');

        $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
            WHERE tbl_par_codigo_local = '$codigo_local' AND 
                  tbl_par_lixeira=0 AND 
                  tbl_par_estacao_monta_inicial<='$data_hoje' AND 
                  tbl_par_estacao_monta_final>='$data_hoje'");  

        $num_rows = mysqli_num_rows($sql);

        if($num_rows != 0){
            $reg_estacao = mysqli_fetch_object($sql);
            $id_estacao = $reg_estacao->tbl_par_estacao_id;
            $desc_estacao = $reg_estacao->tbl_par_estacao_nome;
        }
        else {
            $id_estacao=0;
            $desc_estacao = '';
        }*/

        // Verifica previsão de parto
        if ($previsao_parto_de!='' && $previsao_parto_ate!='') {

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      (tbl_cobertura_controle = 'C' OR
                      tbl_cobertura_controle = 'M') AND 
                      tbl_ite_cobertura_resultado_diagnostico = 'P' 
                ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

            /*$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira=0 AND
                          tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          tbl_cobertura_controle = 'C' AND 
                          tbl_ite_cobertura_resultado_diagnostico = 'P'
                ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); */
            
            $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_coberturas!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                $cobertura_id = $reg_cobertura->tbl_cobertura_id;
                $controle = $reg_cobertura->tbl_cobertura_controle;

                if ($controle=='C') {
                    $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                            WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");

                    $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                    $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf
                        WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                              tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                        ORDER BY tbl_ite_protocoloiatf_id ASC");

                    $dias_previsao_parto = 282;

                    while($reg_itens_iatf = mysqli_fetch_object($sql)){
                        $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);

                        $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

                        $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                    }
                }
                else {
                    $data_previsao_parto = $reg_cobertura->tbl_ite_cobertura_previsao_parto;
                }
            }
            else {
                $data_previsao_parto = '0000-00-00';
            }
        }

        $estacao_monta = $reg_animal->tbl_animal_em_estacao_monta;
        $num_coberturas = $reg_animal->tbl_animal_numero_coberturas;
        $num_abortos = $reg_animal->tbl_animal_numero_abortos;

        if ($estacao_monta == ''){
            $estacao_monta = 'N';
        }

        if ($num_coberturas == ''){
            $num_coberturas = 0;
        }

        if($num_abortos == ''){
            $num_abortos = 0;
        }

        // VERIFICA SE O ANIMAL ESTA EM ESTACAO DE MONTA OU NA LISTA DA MONTA NATURAL

        $femea_selecionada_cobertura='';

        //VERIFICA SE A FÊMEA JA FOI SELECIONADA NA ULTIMA ESTACAO 4ª PREMISSA

        $tbl_selecao = mysqli_query($conector, "SELECT  * FROM tbl_cobertura
            INNER JOIN tbl_item_cobertura 
                    ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                 WHERE tbl_cobertura_lixeira=0 AND 
                      (tbl_cobertura_controle = 'C' OR  
                       tbl_cobertura_controle = 'M') AND                        
                       tbl_cobertura_codigo_local = '$local' AND 
                       tbl_ite_cobertura_codigo_id_animal = '$codigo'
              ORDER BY tbl_cobertura_incluido_em DESC LIMIT 1");

        $num_row_item_cobertura = mysqli_num_rows($tbl_selecao);

        if ($num_row_item_cobertura!=0) {

            $reg_selecao = mysqli_fetch_object($tbl_selecao);

            $id_cobertura = $reg_selecao->tbl_cobertura_id;
            $id_estacao = $reg_selecao->tbl_cobertura_codigo_estacao_monta;
            $controle = $reg_selecao->tbl_cobertura_controle;
            $diagnostico_selecao = $reg_selecao->tbl_ite_cobertura_resultado_diagnostico;
            $nascido = $reg_selecao->tbl_ite_cobertura_nascido;

            if ($controle=='C') {
                if ($id_estacao==$id_estacao_atual) {
                    //print_r('animal iatf: ' . $codigo_numerico . ' diagnostico: ' . $diagnostico_selecao . ' Nascido: ' . $nascido . ' Cobertura: '. $id_cobertura.'</br>');

                    $femea_selecionada_cobertura = 'S';

                    if ($diagnostico_selecao=='N' || $nascido!='') {
                        $femea_selecionada_cobertura = '';
                    }
                }
            }
            else {
                //print_r('animal monta: ' . $codigo_numerico . ' diagnostico: ' . $diagnostico_selecao . ' Nascido: ' . $nascido . ' Cobertura: '. $id_cobertura.'</br>');
                $femea_selecionada_cobertura = 'S';

                if ($diagnostico_selecao=='N' || $nascido!='') {
                    $femea_selecionada_cobertura = '';
                }
            }
        } 

        if ($wcategoria=="") {
            if ($data_previsao_parto>=$previsao_parto_de && 
                $data_previsao_parto<=$previsao_parto_ate &&
                $num_partos>=$num_parto_de && 
                $num_partos<=$num_parto_ate &&
                $num_abortos>=$num_aborto_de && 
                $num_abortos<=$num_aborto_ate && 
                ($paridas_filtro==$parida_lida || 
                $solteiras_filtro==$solteira_lida) &&
                $tem_descarte==$descarte_filtro && 
                ($positivo==$tem_positivo && 
                $negativo==$tem_negativo)
            ) {

                if ($femea_selecionada_cobertura=='S') {
                    if ($controle=='C') {
                        echo '<tr class="text-primary">';
                    }
                    else {
                        echo '<tr style="color: #060c54;">';
                    }
                }
                else {
                    echo '<tr>';
                }

                echo '
                <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value="' .$codigo.'"></td>
                <td width="4%" class="id_animal_alfa">'.$codigo_alfa.'</td>
                <td width="8%" class="id_animal">'.$codigo_numerico.'</td>
                <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                <td width="8%" class="sexo_animal">'.$sexo.'</td>
                <td width="8%" class="nascimento_animal">'.$nascimento->format('d/m/Y').'</td>
                <td width="10%" class="raca_animal">'.$descricao_raca.'</td>
                <td width="10%" class="pelagem_animal">'.$descricao_pelagem.'</td>
                <td width="12%" class="mae_animal">'.$descricao_mae.'</td>
                <td width="3%" style="text-align: center; color: red;">'.$animal_descarte.'</td>
                <td hidden class="animal_id">'.$codigo.'</td>
                <td hidden class="codigo_categoria">'.$codigo_categoria.'</td>
                <td hidden class="femea_selecionada">'.$femea_selecionada_cobertura.'</td>
                <td hidden class="controle">'.$controle.'</td>
                </tr>
                ';
                $animais_listados++;
            }
        }
        else {
            foreach ($wcategoria as $value) {
                if ($value==$codigo_categoria) {
                    if ($data_previsao_parto>=$previsao_parto_de && 
                        $data_previsao_parto<=$previsao_parto_ate &&
                        $num_partos>=$num_parto_de && 
                        $num_partos<=$num_parto_ate && 
                        $num_abortos>=$num_aborto_de && 
                        $num_abortos<=$num_aborto_ate && 
                        ($paridas_filtro==$parida_lida || 
                        $solteiras_filtro==$solteira_lida) &&
                        $tem_descarte==$descarte_filtro && 
                        ($positivo==$tem_positivo || 
                        $negativo==$tem_negativo)
                        ) {

                        if ($femea_selecionada_cobertura=='S') {
                            if ($controle=='C') {
                                echo '<tr class="text-primary">';
                            }
                            else {
                                echo '<tr style="color: #060c54;">';
                            }
                        }
                        else {
                            echo '<tr>';
                        }

                        echo '
                        <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value="' .$codigo.'"></td>
                        <td width="4%" class="id_animal_alfa">'.$codigo_alfa.'</td>
                        <td width="8%" class="id_animal">'.$codigo_numerico.'</td>
                        <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                        <td width="8%" class="sexo_animal">'.$sexo.'</td>
                        <td width="8%" class="nascimento_animal">'.$nascimento->format('d/m/Y').'</td>
                        <td width="10%" class="raca_animal">'.$descricao_raca.'</td>
                        <td width="10%" class="pelagem_animal">'.$descricao_pelagem.'</td>
                        <td width="12%" class="mae_animal">'.$descricao_mae.'</td>
                        <td width="3%" style="text-align: center; color: red;">'.$animal_descarte.'</td>
                        <td hidden class="animal_id">'.$codigo.'</td>
                        <td hidden class="codigo_categoria">'.$codigo_categoria.'</td>
                        <td hidden class="femea_selecionada">'.$femea_selecionada_cobertura.'</td>
                        <td hidden class="controle">'.$controle.'</td>
                        </tr>
                        ';
                        $animais_listados++;
                    }
                }
            }
        }

    } // Fim while

    echo '
        </tbody>
        </table>
    ';

    echo "<script>
        $(document).ready(function(){
            $('#tabela_itens_digitados').DataTable({
                'responsive': true,
                'paging':   false,
                'ordering': true,
                'info':     true,
                'language': {
                    'sSearch': 'Busca:',
                    'zeroRecords': 'Nada encontrado',
                    'info': '',
                    'infoEmpty': 'Nenhum registro disponível',
                    'infoFiltered': '(filtrado de _MAX_ registros no total)',
                },
                initComplete: function() {
                    $('table.dataTable').css('width', '100%');
                }
            });



            $('.seleciona_todos').click(function(event) {
                var total_selecionados = 0;

                const isMasterCheckboxChecked = this.checked; // Verifica se o checkbox 'Marcar Todos' foi marcado
                let femeaComSEncontrada = false; // Flag para verificar se encontrou 'S'

                // Itera sobre cada checkbox individual com a classe 'checkbox1'
                $('.checkbox1').each(function() {
                    // Marca ou desmarca o checkbox individual
                    this.checked = isMasterCheckboxChecked;

                    // Se o checkbox mestre está marcando todos (this.checked === true)
                    // e ainda não encontramos uma fêmea com 'S', faz a verificação.
                    if (isMasterCheckboxChecked && !femeaComSEncontrada) {
                        // Pega a linha (tr) pai do checkbox individual
                        const row = $(this).closest('tr');

                        if (row.length) { // Garante que a linha foi encontrada
                            const femeaSelecionadaElement = row.find('.femea_selecionada');
                            const femeaSelecionadaValue = femeaSelecionadaElement.text().trim();

                            if (femeaSelecionadaValue === 'S') {
                                femeaComSEncontrada = true; // Define a flag como true se encontrar 'S'
                            }
                        }
                    }
                });

                // Após marcar/desmarcar todos os checkboxes individuais:
                // Se o checkbox mestre foi marcado E uma fêmea com 'S' foi encontrada, emite o alert.
                if (isMasterCheckboxChecked && femeaComSEncontrada) {
                    $('#mensagem_erro_atencao').modal();
                    $('#mensagem_erro_atencao .modal-body .desc_modal').html('Exitem Fêmeas em Estação de Monta ou na Lista de Monta Natural!');
                }

                // Atualiza a contagem de selecionados
                // Se o master foi marcado, total_selecionados é o número total de checkboxes 'checkbox1'.
                // Se o master foi desmarcado, total_selecionados é 0.
                total_selecionados = isMasterCheckboxChecked ? $('.checkbox1').length : 0;

                // Atualiza os elementos que exibem o total
                $('.total_digitados').text('Animais Selecionados: ' + total_selecionados);
                $('.total_digitados').val(total_selecionados);
            });


            // Inicializa total_selecionados fora do listener
            let total_selecionados = 0;

            // Seleciona todos os checkboxes com a classe 'checkbox1'
            const checkboxes = document.querySelectorAll('.checkbox1');

            // ANEXA O EVENT LISTENER UMA ÚNICA VEZ PARA CADA CHECKBOX
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // 'this' aqui se refere ao checkbox que FOI CLICADO e teve seu estado alterado.

                    // Pega a linha (tr) pai do checkbox clicado
                    const row = this.closest('tr');

                    if (row) {
                        const idAnimalElement = row.querySelector('.id_animal');
                        const femeaSelecionadaElement = row.querySelector('.femea_selecionada');
                        const controleElement = row.querySelector('.controle');

                        const idAnimal = idAnimalElement ? idAnimalElement.textContent.trim() : '';
                        const femeaSelecionada = femeaSelecionadaElement ? femeaSelecionadaElement.textContent.trim() : '';
                        const controle = controleElement ? controleElement.textContent.trim() : '';

                        // Verifica se o checkbox está MARCADO e exibe o alert UMA VEZ
                        if (this.checked) {
                            total_selecionados++;

                            if (femeaSelecionada=='S') {
                                if (controle=='C') {
                                    $('#mensagem_erro_atencao').modal();
                                    $('#mensagem_erro_atencao .modal-body .desc_modal').html('Esta Fêmea está em Estação de Monta!');
                                }
                                else {
                                    $('#mensagem_erro_atencao').modal();
                                    $('#mensagem_erro_atencao .modal-body .desc_modal').html('Esta Fêmea está na Lista de Monta Natural!');
                                }
                            }
                        } else {
                            total_selecionados--;
                        }

                        // Atualiza o total exibido
                        $('.total_digitados').text('Animais Selecionados: ' + total_selecionados);
                        $('.total_digitados').val(total_selecionados);
                    }
                });
            });
        });
            
    </script>";

    mysqli_close($conector);
            
?>


                
                
