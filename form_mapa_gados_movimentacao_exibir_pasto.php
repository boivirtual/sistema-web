<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

@ session_start();
$controle_estoque = $_SESSION['controle_estoque'];

//pegando info do pasto do banco
$pasto_id = $_POST["pasto_id"];

$query = "SELECT * FROM tbl_pasto 
    WHERE tbl_pasto_id = $pasto_id AND tbl_pasto_lixeira = 0";

$request = mysqli_query($conector, $query);
$pasto = mysqli_fetch_object($request);

// PEGA O ID E ANO DO LOTE DE ANIMAIS 29/10/2024
if ($pasto->tbl_pasto_id_lote!=0) {
    $id_lote = $pasto->tbl_pasto_id_lote;
    $ano_lote = $pasto->tbl_pasto_ano_lote;
    $desc_id_lote = 'L-'.$id_lote.'/'.substr($ano_lote, 2, 2);
}
else {
    $desc_id_lote = '';
}
//----------------------------------------------------------

$_SESSION["pasto_id"] = $pasto_id;
$total_animais = 0;

$array_categoria = explode("!", $pasto->tbl_pasto_array_categoria);
$desc_categoria = [];
$arrayCategorias = [];
$descricaoCategorias = [];

for($i = 0; $i < count($array_categoria); $i++){
    $codigo_categoria = $array_categoria[$i];

    $ssql = "SELECT * FROM tabela_categoria_idade 
    WHERE tab_codigo_categoria_idade ='$codigo_categoria' and 
          tab_registro_lixeira_categoria_idade='0'"; 
    
    $rs = mysqli_query($conector,$ssql); 
    $fila = mysqli_fetch_object($rs);

    $codigo_id = $fila->tab_codigo_categoria_idade;
    $idade_de = $fila->tab_categoria_idade_de;
    $idade_ate = $fila->tab_categoria_idade_ate;

    if ($idade_ate==999999999){
        array_push($desc_categoria, '> 36 meses');
        $descricaoCategorias = [
            "id" => $codigo_id,
            "idade_de" => $idade_de,
            "idade_ate" => $idade_ate
        ];
        array_push($arrayCategorias, $descricaoCategorias);
    }
    else {
        array_push($desc_categoria, $idade_de . ' a ' . $idade_ate . ' meses');
        $descricaoCategorias = [
            "id" => $codigo_id,
            "idade_de" => $idade_de,
            "idade_ate" => $idade_ate
        ];
        array_push($arrayCategorias, $descricaoCategorias);
    }
}

//pegando info do nascimento dos animais no pasto
$sql = "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = $pasto_id AND tbl_animal_pasto_situacao = 'A'";
$rs = mysqli_query($conector, $sql);

$arrayMacho = [
    0 => 0,
    1 => 0,
    2 => 0,
    3 => 0,
    4 => 0
];
$arrayFemea = [
    0 => 0,
    1 => 0,
    2 => 0,
    3 => 0,
    4 => 0
];

if(mysqli_num_rows($rs) > 0){
    while($reg_animais = mysqli_fetch_object($rs)){
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        //$codigo_categoria = $reg_animais->tbl_animal_pasto_categoria;
    
        $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); 
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        //$meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        for($i = 0; $i < count($arrayCategorias); $i++){
            $id_categoria = $arrayCategorias[$i]['id'];
            $idade_de = $arrayCategorias[$i]['idade_de'];
            $idade_ate = $arrayCategorias[$i]['idade_ate'];
    
            if ($controle_estoque=='I') {
                if($idade >= $idade_de && $idade <= $idade_ate && $sexo == "F"){
                    $arrayFemea[$i] += 1;
                    $total_animais += 1;
                }elseif($idade >= $idade_de && $idade <= $idade_ate && $sexo == "M"){
                    $arrayMacho[$i] += 1;
                    $total_animais += 1;
                }else{
                    $arrayFemea[$i] += 0;
                    $arrayMacho[$i] += 0;
                }

                /*if($id_categoria==$codigo_categoria && $sexo == "F"){
                    $arrayFemea[$i] += 1;
                    $total_animais += 1;
                }elseif($id_categoria==$codigo_categoria && $sexo == "M"){
                    $arrayMacho[$i] += 1;
                    $total_animais += 1;
                }else{
                    $arrayFemea[$i] += 0;
                    $arrayMacho[$i] += 0;
                }*/
            }
            else {
                if($idade >= $idade_de && $idade <= $idade_ate && $sexo == "F"){
                    $arrayFemea[$i] += 1;
                    $total_animais += 1;
                }elseif($idade >= $idade_de && $idade <= $idade_ate && $sexo == "M"){
                    $arrayMacho[$i] += 1;
                    $total_animais += 1;
                }else{
                    $arrayFemea[$i] += 0;
                    $arrayMacho[$i] += 0;
                }
            }
        }
    }
}

// REVER ESSA ROTINA
else {
    $dataAtual = new DateTime();
    $dataSem = new DateTime($pasto->tbl_pasto_data_sem_animais);
    $diff = $dataAtual->diff($dataSem);
    $tempoPasto = "Pasto vazio há " . $diff->days . " dia(s)";
}

//pegando info do capim do banco
$capim_id = $pasto->tbl_pasto_tipo_capim;
$query = "SELECT * FROM tbl_tipo_capim WHERE tbl_tipo_capim_id = $capim_id AND tbl_tipo_capim_lixeira = 0";
$request = mysqli_query($conector, $query);
$num_rows = mysqli_num_rows($request);   

if ($num_rows!=0) {
    $capim = mysqli_fetch_object($request);
    $descricao_capim = $capim->tbl_tipo_capim_descricao;
}
else {
    $descricao_capim = '';
}

//pegando info do local/fazenda do banco
$local_id = $pasto->tbl_pasto_codigo_local;
$query = "SELECT * FROM tbl_pessoa WHERE tbl_pessoa_id = $local_id AND tbl_pessoa_lixeira = 0";
$request = mysqli_query($conector, $query);
$local = mysqli_fetch_object($request);

//dados para o modal de nascimento
$tbl_motivo_morte = mysqli_query($conector, "select * from tabela_causa_morte where tab_registro_lixeira_causa_morte=0"); 

$pai = mysqli_query($conector, "select * from tbl_animais 
    inner join tabela_racas
            on tab_codigo_raca=tbl_animal_codigo_raca
         where tbl_animal_lixeira=0  and tbl_animal_sexo='M'order by tbl_animal_codigo_numerico"); 

$semem = mysqli_query($conector, "select * from tbl_semem
    inner join tabela_racas
            on tab_codigo_raca=tbl_semem_codigo_raca
         where tbl_semem_lixeira=0"); 

$raca = mysqli_query($conector, "select * from tabela_racas where tab_registro_lixeira_raca=0");

$pelagem = mysqli_query($conector, "select * from tabela_pelagens where tab_registro_lixeira_pelagem=0");

$locais = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0");

$pastos = mysqli_query($conector, "select * from tbl_pasto where tbl_pasto_lixeira=0 and tbl_pasto_codigo_local = \"{$local_id}\"");

$categorias = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0");

$data_sistema = date("Y-m-d");
$ano = date("Y");
$mes = date("m");
$dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$grupo_usuario = $_SESSION['grupo_usuario'];
$codigo_usuario = $_SESSION['id_usuario'];

$tbl_usuario = "SELECT * FROM usuario WHERE id_usuario = '$codigo_usuario' AND lixeira_usuario=0 ";  
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

if (!isset($_SESSION['data_inicial_nascimento'])){
    $data_inicial = $ano . '-' . $mes . '-01';
}
else {
    $data_inicial =  $_SESSION['data_inicial_nascimento'];  
}

if (!isset($_SESSION['data_final_nascimento'])){
    $data_final = $ano . '-' . $mes . '-' . $dias_mes;
}
else {
    $data_final =  $_SESSION['data_final_nascimento'];   
} 

// Pega dias com animais no pasto
    $dias_pasto = 0;

    $dataAtual = new DateTime();
    $dataCom = new DateTime($pasto->tbl_pasto_data_com_animais);
    $diff = $dataAtual->diff($dataCom);
    $dias_pasto = $diff->days;
// Fim pega dias com animais no pasto


//inicio do html

echo "<input name='local_origem' type='hidden' id='local_origem' value='{$local_id}'>";
echo "<input name='pasto_origem' type='hidden' id='pasto_origem' value='{$pasto_id}'>";
echo "<input name='totalAnimais' type='hidden' id='totalAnimais' value='{$total_animais}'>";
echo "<input type='hidden' id='descPastoOrigem' value='{$pasto->tbl_pasto_descricao}'>";

echo "
<div class='nome_fazenda'>
    <button onclick='voltar()' class='pull-right btn btn-info'>Voltar</button>
    <span>$local->tbl_pessoa_nome</span>
</div>";

echo "<input name='controle_estoque' type='hidden' id='controle_estoque' value='{$controle_estoque}'>";

if($total_animais != 0){
    if($pasto->tbl_pasto_modulo != '999'){
        echo "<div class='info_pasto'>
                Pasto: $pasto->tbl_pasto_descricao - $descricao_capim - $total_animais animais - <span style='color: rgba(0, 0, 0, 1)'>Animais no pasto há $dias_pasto dias</span>.
                <button onclick='distribuirNutricao()' style='margin-left: 10px;' id='btnNutricao' class='btn btn-primary'>Distribuir Nutrição</button>
            </div>";
    }else{
        echo "<div class='info_pasto'>
                Pasto: $pasto->tbl_pasto_descricao - $total_animais animais - <span style='color: rgba(0, 0, 0, 1)'>Animais no pasto há $dias_pasto dias</span>.
                <button onclick='distribuirNutricao()' style='margin-left: 10px;' id='btnNutricao' class='btn btn-primary'>Distribuir Nutrição</button>
            </div>";
    }
    echo "<div class='row col-lg-12 col-md-12 col-sm-12' style='margin-top: 5px' id='divNutricao' hidden>
        <div class='col-md-3' style='padding: 0; margin-top: 10px'>
            <label for='slctCocho'>Situação do Cocho</label>
            <select name='slctCocho' id='slctCocho' class='form-control custom-select' onchange='selecionouCocho()'>
                <option value='000000000'>...</option>
            </select>
        </div>
        
        <div class='col-md-9' style='margin-top: 10px'>
            <fieldset class='scheduler-border' id='dtNutricao' hidden>
                <legend class='scheduler-border' style='font-size: 14px; margin-bottom: 0px'>
                    <label>Distribuir Nutrição</label>
                </legend>

                <div class='row'>
                    <div class='form-group col-md-3'>
                        <label>Data</label>
                        <input type='date' name='dataNutricao' id='dataNutricao' class='form-control' value='$data_sistema' onchange='lerNutricao()'>
                    </div>
                    <div class='form-group col-md-4'> 
                        <label>Produto</label>

                        <select name='nomeProduto' id='nomeProduto' class='form-control custom-select' onchange='selecionouProduto()'>
                            <option value='000000000'>...</option>
                        </select>
                    </div>
                    <div class='form-group col-md-3'>
                        <label>Quantidade</labeL>
                        <input type='text' class='form-control custom-select' name='qtdProduto' id='qtdProduto' placeholder='0,00' onkeypress='digita_valor()' onblur='exibe_qtdProduto()'>
                    </div>
                    <div class='form-group col-md-2'>
                        <label>Und</labeL>
                        <input type='text' class='form-control' name='undProduto' id='undProduto' disabled style='background-color: white; color: black;'>
                    </div>

                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        <button class='btn btn-success confirma_nutricao' onclick='confirmaNutricao()'>Confirma</button>

                        <button class='btn btn-info' onclick='fecharNutricao()'>Fechar</button>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>";
    echo "<div id='tabelaProdutos'></div>";
    echo "<hr style='border-top: 1px solid #eee'>";
    echo "
    <div class='row info_pasto col-lg-12 col-md-12 col-sm-12 outras_movimentacoes'>
        <p class='col-lg-12 col-md-12 col-xs-12' style='padding: 0'>Movimentação por Categoria/Cabeça:</p>
    </div>
    ";
}else{
    if($pasto->tbl_pasto_modulo != '999'){
        echo "<div class='info_pasto'>Pasto: $pasto->tbl_pasto_descricao - $descricao_capim - <span style='color: rgba(0, 0, 0, 1)'>{$tempoPasto}</span>.</div><hr style='border-top: 1px solid #eee'>";
    }else{
        echo "<div class='info_pasto'>Pasto: $pasto->tbl_pasto_descricao - <span style='color: rgba(0, 0, 0, 1)'>{$tempoPasto}</span>.</div><hr style='border-top: 1px solid #eee'>";
    }
}

$total_bezerros = 0;

for($i = 0; $i < count($array_categoria); $i++){
    if($arrayFemea[$i] != '' && $array_categoria[$i] == 001){
        $total_bezerros += $arrayFemea[$i];
    }

    if($arrayMacho[$i] != '' && $array_categoria[$i] == 001){
        $total_bezerros += $arrayMacho[$i];
    }
}

echo
"<div class='row col-lg-12 col-md-12 col-sm-12'>
    <div class='col-lg-12 col-md-12 col-sm-12 tabela table-responsive'>
        <table class='table col-lg-12'>
            <thead class='thead-dark'>
                <tr>
                    <th style='width: 6%;' scope='col'>Qtd</th>
                    <th style='width: 8%;' scope='col'>Categoria</th>
                    <th style='width: 1%;' scope='col'>Sexo</th>
                    <th style='width: 10%; text-align:center;' scope='col'>Quantidade</th>
                    <th style='width: 10%; text-align:center;' id='lblNovoPasto' scope='col'>Novo Pasto</th>
                </tr>
            </thead>
            <tbody>";
        for($i = 0; $i < count($desc_categoria); $i++){
            if($array_categoria[$i] == 001){
                if($total_bezerros != '0'){
                    echo 
                "<tr>
                <th scope='row'>$total_bezerros</th>
                <td>$desc_categoria[$i]</td>
                <td>M/F</td>
                <td><input class='select-empresa-menu-control custom-select' type='number' id='qtde_bezerro'></input></td>
                <td>
                    <select class='select-empresa-menu-control custom-select' id='select_pasto_bezerro'> 
                        <option value='0'>...</option>
                    </select>
                </td>
                <td class='tirar_border'></td>
                </tr>";
                }
            }elseif($array_categoria[$i] == 002){
                if($arrayFemea[$i] != '0'){
                    echo 
                    "<tr>
                    <th scope='row'>$arrayFemea[$i]</th>
                    <td>$desc_categoria[$i]</td>
                    <td>F</td>
                    <td><input class='select-empresa-menu-control custom-select' id='qtde_femea_002' type='number'></input></td>
                    <td>
                        <select class='select-empresa-menu-control custom-select' id='select_pasto_femea_002'> 
                            <option value='0'>...</option>
                        </select>
                    </td>
                    <td class='tirar_border'></td>
                    </tr>";
                }
                if($arrayMacho[$i] != '0'){
                    echo 
                    "<tr>
                    <th scope='row'>$arrayMacho[$i]</th>
                    <td>$desc_categoria[$i]</td>
                    <td>M</td>
                    <td><input class='select-empresa-menu-control custom-select' id='qtde_macho_002' type='number'></input></td>
                    <td>
                        <select class='select-empresa-menu-control custom-select' id='select_pasto_macho_002'> 
                            <option value='0'>...</option>
                        </select>
                    </td>
                    <td class='tirar_border'></td>
                    </tr>";
                }
            }elseif($array_categoria[$i] == 003){
                if($arrayFemea[$i] != '0'){
                    echo 
                    "<tr>
                    <th scope='row'>$arrayFemea[$i]</th>
                    <td>$desc_categoria[$i]</td>
                    <td>F</td>
                    <td><input class='select-empresa-menu-control custom-select' id='qtde_femea_003' type='number'></input></td>
                    <td>
                        <select class='select-empresa-menu-control custom-select' id='select_pasto_femea_003'> 
                            <option value='0'>...</option>
                        </select>
                    </td>
                    <td class='tirar_border'></td>
                    </tr>";
                }
                if($arrayMacho[$i] != '0'){
                    echo 
                    "<tr>
                    <th scope='row'>$arrayMacho[$i]</th>
                    <td>$desc_categoria[$i]</td>
                    <td>M</td>
                    <td><input class='select-empresa-menu-control custom-select' id='qtde_macho_003'  type='number'></input></td>
                    <td>
                        <select class='select-empresa-menu-control custom-select' id='select_pasto_macho_003'> 
                            <option value='0'>...</option>
                        </select>
                    </td>
                    <td class='tirar_border'></td>
                    </tr>";
                }
            }elseif($array_categoria[$i] == 004){
                if($arrayFemea[$i] != '0'){
                    echo 
                    "<tr>
                    <th scope='row'>$arrayFemea[$i]</th>
                    <td>$desc_categoria[$i]</td>
                    <td>F</td>
                    <td><input class='select-empresa-menu-control custom-select' id='qtde_femea_004' type='number'></input></td>
                    <td>
                        <select class='select-empresa-menu-control custom-select' id='select_pasto_femea_004'> 
                            <option value='0'>...</option>
                        </select>
                    </td>
                    <td class='tirar_border'></td>
                    </tr>";
                }
                if($arrayMacho[$i] != '0'){
                    echo 
                    "<tr>
                    <th scope='row'>$arrayMacho[$i]</th>
                    <td>$desc_categoria[$i]</td>
                    <td>M</td>
                    <td><input class='select-empresa-menu-control custom-select' id='qtde_macho_004'  type='number'></input></td>
                    <td>
                        <select class='select-empresa-menu-control custom-select' id='select_pasto_macho_004'> 
                            <option value='0'>...</option>
                        </select>
                    </td>
                    <td class='tirar_border'></td>
                    </tr>";
                }
            }elseif($array_categoria[$i] == 005){
                if($arrayFemea[$i] != '0'){
                    echo 
                    "<tr>
                    <th scope='row'>$arrayFemea[$i]</th>
                    <td>$desc_categoria[$i]</td>
                    <td>F</td>
                    <td><input class='select-empresa-menu-control custom-select' id='qtde_femea_005' type='number'></input></td>
                    <td>
                        <select class='select-empresa-menu-control custom-select' id='select_pasto_femea_005'> 
                            <option value='0'>...</option>
                        </select>
                    </td>
                    <td class='tirar_border'></td>
                    </tr>";
                }
                if($arrayMacho[$i] != '0'){
                    echo 
                    "<tr>
                    <th scope='row'>$arrayMacho[$i]</th>
                    <td>$desc_categoria[$i]</td>
                    <td>M</td>
                    <td><input class='select-empresa-menu-control custom-select' id='qtde_macho_005'  type='number'></input></td>
                    <td>
                        <select class='select-empresa-menu-control custom-select' id='select_pasto_macho_005'> 
                            <option value='0'>...</option>
                        </select>
                    </td>
                    <td class='tirar_border'></td>
                    </tr>";
                }
            }
        }
        echo
        "</tbody>
        </table>
    </div>
    <div class='row col-lg-12 col-md-12 col-sm-12'>
        <label for='descricao_lote' class='control-label'>Descrição do Lote</label>

        <input type='text' class='form-control col-md-12 custom-select' name='descricao_lote' id='descricao_lote' style='margin-bottom: 10px;' value='$pasto->tbl_pasto_descricao_lote $desc_id_lote' onkeyup='maiuscula(this)' onclick='abrir_modal_descricao_lote()'></input>

        <input name='descricao_lote_anterior' id='descricao_lote_anterior' value='$pasto->tbl_pasto_descricao_lote' type='hidden'></input>

        <input name='descricao_lote_gravar' id='descricao_lote_gravar' value='$pasto->tbl_pasto_descricao_lote' type='hidden'></input>

        <input id='descricao_lote_1' value='$pasto->tbl_pasto_descricao_lote_1' type='hidden'></input>
        <input id='descricao_lote_2' value='$pasto->tbl_pasto_descricao_lote_2' type='hidden'></input>
        <input id='descricao_lote_3' value='$pasto->tbl_pasto_descricao_lote_3' type='hidden'></input>
        <input id='descricao_lote_4' value='$pasto->tbl_pasto_descricao_lote_4' type='hidden'></input>
        <input id='descricao_lote_5' value='$pasto->tbl_pasto_descricao_lote_5' type='hidden'></input>
        <input id='descricao_lote_6' value='$pasto->tbl_pasto_descricao_lote_6' type='hidden'></input>
        <input id='id_lote' value='$pasto->tbl_pasto_id_lote' type='hidden'></input>
        <input id='ano_lote' value='$pasto->tbl_pasto_ano_lote' type='hidden'></input>

        <button id='btn_mudar_pasto' class='btn btn-success col-xs-4 col-md-2 col-lg-1' onclick='reseta_confirma();retirar_por_categoria();'>Confirma</button>
    </div>
</div>";

echo "
<div class='row info_pasto col-lg-12 col-md-12 col-sm-12 outras_movimentacoes'>
    <p class='col-lg-12 col-md-12 col-xs-12' style='padding: 0'>Outras opções de movimentação:</p>
    <div style='padding: 0' class='col-lg-8 col-md-12 col-xs-12'>
        <button onclick='abrir_modal_nascimento();' style='margin-right: 2px;' class='btn btn-primary col-lg-3 col-md-3 col-xs-4 link'>Nascimento</button>
        <button onclick='abrir_modal_morte();' class='btn btn-primary col-lg-2 col-md-3 col-xs-3 link'>Morte</button>
    </div>
</div>";

echo "<div class='modal fade' id='modal_morte' tabindex='-1' role='dialog' 
aria-labelledby='modal_incluirCenterTitle' aria-hidden='true'  data-backdrop='static'>

   <div class='modal-lg modal-dialog modal-dialog-centered' role='document' style='width: 100%;'>
       <div class='modal-content'>
           <div class='modal-header'>
               <h4 class='modal-title' id='modal_incluirLabel'>Mapa de Gado - Morte </h4>
           </div>

           <div class='modal-body'>
               <form method='POST' action='#' enctype='multipart/form-data' id='form_gravar_morte'>
                   <input name='codigo_id_morte' type='hidden' id='codigo_id_morte' value='0'>
                   <input name='sexo_animal_morte' type='hidden' id='sexo_animal_morte'>
                   <input name='peso_animal_morte' type='hidden' id='peso_animal_morte'>
                   <input name='nascimento_animal_morte' type='hidden' id='nascimento_animal_morte'>
                   <input name='raca_animal_morte' type='hidden' id='raca_animal_morte'>
                   <input name='pelagem_animal_morte' type='hidden' id='pelagem_animal_morte'>
                   <input name='mae_animal_morte' type='hidden' id='mae_animal_morte'>
                   <input name='motivo_animal_morte' type='hidden' id='motivo_animal_morte'>
                   <input name='codigo_motivo_morte' type='hidden' id='codigo_motivo_morte'>
                    <input type='hidden' name='sexo_morte' id='sexo_morte'>
                    <input type='hidden' name='categoria_digitada_morte' id='categoria_digitada_morte'>

                   <input name='array_itens' type='hidden' id='array_itens'>

                    <input name='local_morte' type='hidden' id='local_morte' value='{$local_id}'>

                    <input name='pasto_morte' type='hidden' id='pasto_morte' value='{$pasto_id}'>

                   <div class='tab-content'>
                       <div class='alert alert-danger alert_erro_animal' id='alert_erro_animal' hidden='true'>
                           <strong class='negrito'></strong><span></span>
                       </div> 

                       <div id='dados' class='tab-pane active'>
                            <button type='button' class='btn btn-info pull-right' data-dismiss='modal' onclick='location.reload();'>Voltar</button>

                            <div class='row' style='padding: 0 14px 0 14px'>
                                <p class='nome_fazenda'>$local->tbl_pessoa_nome</p>

                                <div class='info_pasto'>
                                    Pasto: $pasto->tbl_pasto_descricao
                                </div>
                            </div>

                            <hr style='border-top: 1px solid #eee'>

                           <div class='row'>
                               <div class='form-group col-xs-4 col-md-4 id_animal'>
                                   <label for='id_animal_morte' class='control-label'><span class='required'>*</span> Nº Animal</label>
                                   <input name='id_animal_morte' type='text' class='form-control' id='id_animal_morte' autocomplete='off'
                                   onchange='ler_animal_morte();animal_sem_id();' >
                               </div>

                                <div class='form-group col-md-8'>
                                    <label class='control-label'>&nbsp;</label>
                                    <p id='descricao_animal_morte' class='text-primary'></p>
                                </div>
                            </div>

                           <div class='row'>
                               <div class='form-group col-xs-6 col-md-7'>
                                   <label for='motivo_morte' class='control-label'><span class='required'>*</span> Motivo da Morte</label>
                                   <select class='form-control form-select' id='motivo_morte' name='motivo_morte'>

                                   <option value='000'>...</option>";

                                   while($reg_motivo = mysqli_fetch_object($tbl_motivo_morte)) {

                                       echo "<option value=$reg_motivo->tab_codigo_causa_morte>";
                                           
                                       
                                           echo $reg_motivo->tab_descricao_causa_morte;
                                       echo "</option>";
                                    } 

                                echo "</select>
                               </div>

                               <div class='form-group col-xs-6 col-md-5'>
                                   <label for='data_morte_animal' class='control-label'><span class='required'>*</span> Data da morte</label>";
                                   echo "<input name='data_morte_animal' type='date' class='form-control' id='data_morte_animal' value='$data_sistema';>";
                                echo "</div>

                           </div>
                        
                            <div class='row'>
                                <div class='form-group col-md-8 info_modal_morte' hidden>
                                   <label for='categoria_morte' class='control-label'><span class='required'>*</span> Categoria</label>
                                   <select class='form-control form-select' id='categoria_morte' name='categoria_morte'>

                                   <option value='000'>...</option>
                                   </select>

                                </div>
                            </div>

                           <div class='row'>
                               <div class='form-group col-md-12'>
                                   <label for='observacao_morte' class='control-label'>Observação</label>

                                   <textarea name='observacao_morte' type='text' class='form-control' id='observacao_morte' rows='3' onkeyup='maiuscula(this)'></textarea>
                               </div>
                           </div>

                           <div class='row'>
                               <div class='form-group col-md-12'>
                                   <button type='button' class='btn btn-success confirma_gravar_morte' onClick='salvar_morte()'>Confirmar</button>
                               </div>
                            </div>

                       </div> <!-- fim tab-pane active-->
                   </div> <!-- Fim tab-content-->
               </form>
           </div>
       </div>
   </div>
</div>";

        echo "<div class='modal fade' id='modal_nascimento' tabindex='-1' role='dialog' 
             aria-labelledby='modal_incluirCenterTitle' aria-hidden='true'  data-backdrop='static'>

                <div class='modal-dialog modal-dialog-centered modal-lg' role='document' style='width: 100%;'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h4 class='modal-title' id='modal_incluirLabel'>Mapa de Gado - Nascimento</h4>
                        </div>

                        <div class='modal-body'>
                            <form method='POST' action='gravar_nascimento.php' enctype='multipart/form-data' id='form_gravar_animal'>

                                <input type='hidden' name='grupo_usuario' id='grupo_usuario' value='{$grupo_usuario}'>
                              
                                <input type='hidden' name='tipo_gravacao' id='tipo_gravacao' value='0'>

                                <input type='hidden' name='num_mov_nascimento'  id='num_mov_nascimento' value='0'>

                                <input name='codigo_mae_animal' type='hidden' id='codigo_mae_animal'>

                                <input name='dias_nascimento' type='hidden'  id='dias_nascimento'>

                                <input name='cobertura_id' type='hidden'  id='cobertura_id'>

                                <input name='item_cobertura' type='hidden'  id='item_cobertura'>

                                <input name='estacao_monta_id' type='hidden'  id='estacao_monta_id'>

                                <input name='data_inseminacao' type='hidden'  id='data_inseminacao'>

                                <input type='hidden' id='data_hoje' value='{$data_sistema}'>
                               
                                <input name='local_id' type='hidden' id='local_id' value='{$local_id}'>

                                <input name='pasto_id' type='hidden' id='pasto_id' value='{$pasto_id}'>

                                <div class='tab-content'>
                                    <button type='button' class='btn btn-info pull-right' data-dismiss='modal' onclick='location.reload();'>Voltar</button>

                                    <div class='row' style='padding: 0 14px 0 14px'>
                                        <p class='nome_fazenda'>$local->tbl_pessoa_nome</p>

                                        <div class='info_pasto'>
                                            Pasto: $pasto->tbl_pasto_descricao
                                        </div>
                                    </div>

                                    <hr style='border-top: 1px solid #eee'>

                                        <!-- Campos comuns-->
                                        <div class='row ocorrencias' hidden>
                                            <div class='col-md-3'>
                                                <label class='control-label'><span class='required'>*</span> Selecione uma opção</label>

                                                <div class='clearfix'></div>

                                                <label class='radio-inline'>
                                                    <input type='radio' name='opcao_nascimento' id='opcao_nascimento' value='N' class='opcao_nascimento'>Nascimento
                                                </label>
                                            </div>

                                            <div class='col-md-5'>
                                                <label class='control-label'> &nbsp;</label>

                                                <div class='clearfix'></div>

                                                <label class='radio-inline'>
                                                    <input type='radio' name='opcao_nascimento' id='opcao_morte' value='M' class='opcao_nascimento'>Natimorto
                                                </label>

                                                <label class='radio-inline'>
                                                    <input type='radio' name='opcao_nascimento' id='opcao_aborto' value='A' class='opcao_nascimento'>Aborto
                                                </label>

                                                <label class='radio-inline'>
                                                    <input type='radio' name='opcao_nascimento' id='opcao_absorcao' value='B' class='opcao_nascimento'>Absorção
                                                </label>
                                            </div>
                                        </div>

                                        <hr align='center'>

                                        <div class='campos_data_mae_pai' hidden>
                                            <div class='row'>
                                                <div class='form-group col-md-4'>
                                                    <label class='control-label label_data'>Data Nascimento</label>";

                                                    echo "<input name='nascimento_animal' type='date' class='form-control' id='nascimento_animal' value='$data_sistema';>";
                                                echo "</div>

                                                <div class='form-group col-md-4 codigo_mae_animal'>
                                                    <label for='codigo_mae_consulta' class='control-label label_mae'><span class='required'>*</span> Nº Mãe</label>

                                                    <input name='codigo_mae_consulta' type='text' class='form-control' id='codigo_mae_consulta' autocomplete='off'
                                                    onchange='ler_animal_mae()' onkeypress='return desabilita_enter (this, event)'>
                                                </div>

                                                <div class='form-group col-md-4 codigo_pai_animal'>
                                                    <label class='control-label'>Pai Nº
                                                    </label>

                                                   <select class='form-control' id='codigo_pai_animal' name='codigo_pai_animal'>
                                                        <option value='000000000'>...</option>

                                                        <optgroup label='SEMEM'>";

                                                        while($reg_pai = mysqli_fetch_object($semem)) {
                                                            echo "<option value='$reg_pai->tbl_semem_codigo_id'>";
                                                                echo $reg_pai->tbl_semem_nome . ' - ' . $reg_pai->tab_descricao_raca;
                                                            echo "</option>";
                                                        }
                                                        echo "</optgroup>

                                                        <optgroup label='ANIMAIS'>";

                                                        while($reg_pai = mysqli_fetch_object($pai)) {
                                                            echo "<option value='$reg_pai->tbl_animal_codigo_id'>";
                                                                echo $reg_pai->tbl_animal_codigo_alfa. ' ' . $reg_pai->tbl_animal_codigo_numerico . ' - ' . $reg_pai->tab_descricao_raca;
                                                            echo "</option>";
                                                        }
                                                        echo "</optgroup>
                                                   </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='nascimento_id' hidden>
                                            <div class='row'>
                                                <div class='form-group col-md-2 alfa_animal'>
                                                    <label for='alfa_animal' class='control-label'>Código Alfa</label>
                                                    <input name='alfa_animal' type='text' class='form-control' id='alfa_animal' maxlength='4' placeholder='Letras' 
                                                    onkeyup='maiuscula(this)'>

                                                    <input type='hidden' id='codigo_alfa_anterior' >

                                                </div>

                                                <div class='form-group col-md-2'>
                                                    <input type='hidden' name='codigo_animal_id' id='codigo_animal_id'>

                                                    <label for='codigo_numerico_animal' class='control-label'> Nº Animal</label>

                                                    <input name='codigo_numerico_animal' type='number' class='form-control' id='codigo_numerico_animal' maxlength='9' placeholder='Números'>

                                                    <input type='hidden' id='codigo_numerico_anterior'>

                                                </div>

                                                <div class='form-group col-md-4'>
                                                    <label class='control-label'>&nbsp;</label>

                                                    <h5 class=''>Estação de Monta:
                                                        <span id='ultima_estacao' >
                                                        </span>
                                                        <a href='#' style='color: blue' onclick='lista_femeas_servidas()'>
                                                        <i class='icon_info_alt icon_nascimentos_previstos' data-toggle='tooltip' data-placement='right' title='Existem Nascimentos previstos para essa estação. Clique para ver.'></i>
                                                        </a> 
                                                    </h5>
                                                </div>

                                                <div class='form-group col-md-4'>
                                                    <label class='control-label'>&nbsp;</label>
                                                    <h5 class='desc_novo_nascimento' style='font-weight: 700;color: red; font-size: 13px;'>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='campos_id_aborto_lote' hidden>
                                            <div class='row'>
                                                <div class='form-group col-md-3 qtd_animal'>
                                                    <label class='control-label'><span class='required'>*</span> Qtd Animal</label>

                                                    <input name='qtd_animal' type='number' class='form-control' id='qtd_animal'
                                                    aria-describedby='arrobaHelpBlock'>

                                                    <small id='arrobaHelpBlock' class='form-text text-muted' style='color: #808080'>Nascimento e Sexo iguais</small>
                                                </div>

                                                <div class='form-group col-md-3'>
                                                    <label class='control-label'><span class='required'>*</span> Sexo</label>

                                                    <div class='clearfix'></div>

                                                    <label class='radio-inline'>
                                                      <input type='radio' name='sexo_animal' id='M' value='M'
                                                      class='sexo_animal'>Macho
                                                    </label>

                                                    <label class='radio-inline'>
                                                      <input type='radio' name='sexo_animal' id='F' value='F'
                                                      class='sexo_animal'>Fêmea
                                                    </label>
                                                </div>

                                                <div class='form-group col-md-3 raca_id'>";
                                                if ($controle_estoque=='I') {
                                                    echo "<label for='raca_id' class='control-label'><span class='required'>*</span> Raça</label>";
                                                }
                                                else {
                                                    echo "<label for='raca_id' class='control-label'>Raça</label>";
                                                }

                                                echo "
                                                 <select class='form-control' required='' name='raca_id' id='raca_id'>
                                                   <option value=''>...</option>";

                                                   while($reg_raca = mysqli_fetch_object($raca)) {

                                                   echo "<option value='$reg_raca->tab_codigo_raca'>";
                                                       echo $reg_raca->tab_descricao_raca;
                                                   echo "</option>";
                                                }
                                                  echo "</select>
                                                </div>

                                                <div class='form-group col-md-3 pelagem_id'>
                                                  <label class='control-label'>Pelagem</label>
                                                  <select class='form-control' name='pelagem_id' id='pelagem_id'>
                                                    <option value=''>...</option>";

                                                   while($reg_pelagem = mysqli_fetch_object($pelagem)) {

                                                   echo "<option value='$reg_pelagem->tab_codigo_pelagem'>";
                                                       echo $reg_pelagem->tab_descricao_pelagem;
                                                   echo "</option>";
                                                }
                                                echo "</select>
                                                </div>

                                                <div class='form-group col-md-3 peso_animal'>";
                                                    if ($controle_estoque=='I') {
                                                        echo "<label for='peso_animal' class='control-label'><span class='required'>*</span> Peso</label>";
                                                    }
                                                    else {
                                                        echo "<label for='peso_animal' class='control-label'><span class='required'>*</span> Peso Médio</label>";
                                                    }
                                                    echo "
                                                       <input name='peso_animal' type='number' class='form-control' id='peso_animal'>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='row confirmar' hidden>  
                                            <div class='form-group col-md-12'>
                                                <button type='button' class='btn btn-success confirma_gravar' onClick='confirmar_nascimento()'>Confirmar Inclusão</button>
                                            </div>
                                        </div>";// Fim novo nascimmento

echo '
            <div class="modal fade" id="modal_estacao" tabindex="-1" role="dialog" aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">


                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p class="desc_modal" style="font-weight: bold;">Atenção! Essa Fêmea não está em estação de monta.</p>

                                    <p class="mens_administrador" style="color: red;">Entre em contato com o Administrador do Sistema</p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_1"></span></p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_2"></span></p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_3"></span></p>
                                </div>
                            </div>

                            <div class="row estacao_monta">
                                <div class="form-group col-md-6">
                                    <label for="estacao_monta" class="control-label"><span class="required">*</span> Nova Estação de Monta</label>

                                    <select class="form-control" id="estacao_monta" name="estacao_monta">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success outra_estacao" type="button" onclick="confirmaEstacao()">Confirma Estação de Monta
                            </button>

                            <button class="btn btn-default alterar_diagnostico" type="button" onclick="alterardiagnostico()">Alterar Diagnóstico
                            </button>

                            <button class="btn btn-danger outra_femea" type="button" onclick="selecinarOutraFemea()">Selecione Outra Fêmea
                            </button>

                            <button class="btn btn-default voltar" type="button" onclick="voltarModalEstacao()">Voltar
                            </button>

                            <button class="btn btn-default fechar" type="button" onclick="fecharModalEstacao()">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
';

echo '<script>
    $(document).ready(function(){
        $(".opcao_nascimento").click(function(){
            var opcao_nascimento = document.querySelector("input[name=opcao_nascimento]:checked").value;

            var controle_estoque = $("#controle_estoque").val();
            $(".fazenda_pasto").show();
            $(".confirmar").show();

            $("#dias_nascimento").val("");
            $("#cobertura_id").val("");
            $("#item_cobertura").val("");
            $("#data_inseminacao").val("");
            $("#num_mov_nascimento").val(0);
            $("#tipo_gravacao").val(0);
            $("#F").prop("checked", false);
            $("#M").prop("checked", false);
            $("#codigo_mae_animal").val("");
            $("#codigo_mae_consulta").val("");
            $("#codigo_pai_animal").val("000000000");
            $("#raca_id").val("");
            $("#pelagem_id").val("");
            $("#peso_animal").val("");
            $("#qtd_animal").val("");

            var objDiv = document.getElementById("modal_nascimento");
            objDiv.scrollTop = objDiv.scrollHeight;

            if (opcao_nascimento=="N") {
                $(".campos_data_mae_pai").show();
                $(".campos_id_aborto_lote").show();
                $(".campos_id_lote").show();
                $(".raca_id").show();
                $(".peso_animal").show();
                $(".label_data").html("* Data Nascimento");
                $(".label_pasto").html("* Pasto");
                $(".label_mae").html("* Nº Mãe");


                if (controle_estoque=="I") {
                    $(".nascimento_id").show();
                    $(".qtd_animal").hide();
                    $(".pelagem_id").show();
                    $(".codigo_pai_animal").show();
                    $(".codigo_mae_animal").show();
                }
                else {
                    $(".nascimento_id").hide();
                    $(".qtd_animal").show();
                    $(".pelagem_id").hide();
                    $(".codigo_pai_animal").hide();
                    $(".codigo_mae_animal").hide();
                }
            }
            else {
                $(".nascimento_id").hide();
                $(".campos_data_mae_pai").show();
                $(".campos_id_aborto_lote").show();
                $(".campos_id_lote").hide();
                $(".codigo_pai_animal").hide();
                $(".codigo_mae_animal").show();
                $(".qtd_animal").hide();
                $(".raca_id").hide();
                $(".pelagem_id").hide();
                $(".peso_animal").hide();
                $(".label_data").html("* Data Ocorrência");
                $(".label_pasto").html("Pasto");
                $(".label_mae").html("* Nº Fêmea");
            }
        });
    });
</script>';

echo 
"<script>
    $(document).ready(function(){

        $('.confirma_gravar').on('click', function() {
            $(this).prop({
                disabled: true,
                innerHTML: 'Aguarde...'
          });
        });

        $('#select_pasto_bezerro').click(function(){
            $('#select_pasto_bezerro').css('outline', 'none');
        });
        $('#select_pasto_macho_002').click(function(){
            $('#select_pasto_macho_002').css('outline', 'none');
        });
        $('#select_pasto_femea_002').click(function(){
            $('#select_pasto_femea_002').css('outline', 'none');
        });
        $('#select_pasto_ambos_002').click(function(){
            $('#select_pasto_ambos_002').css('outline', 'none');
        });
        $('#select_pasto_macho_003').click(function(){
            $('#select_pasto_macho_003').css('outline', 'none');
        });
        $('#select_pasto_femea_003').click(function(){
            $('#select_pasto_femea_003').css('outline', 'none');
        });
        $('#select_pasto_ambos_003').click(function(){
            $('#select_pasto_ambos_003').css('outline', 'none');
        });
        $('#select_pasto_macho_24').click(function(){
            $('#select_pasto_macho_24').css('outline', 'none');
        });
        $('#select_pasto_femea_24').click(function(){
            $('#select_pasto_femea_24').css('outline', 'none');
        });
        $('#select_pasto_ambos_24').click(function(){
            $('#select_pasto_ambos_24').css('outline', 'none');
        });

        $('#codigo_mae_consulta').typeahead({
            source: function(query, result) {
                $.ajax({
                    url:'fetch_femeas_servidas.php',
                    method:'POST',
                    data:{query:query,
                          local: \"{$local_id}\"},
                    dataType:'json',
                    success:function(data)
                    {
                        result($.map(data, function(item){
                        return item;
                        }));
                    }
                })
            }
        });

        $('#codigo_mae_consulta').click(function(){
            $('#codigo_mae_consulta').val('');
            $('#codigo_mae_animal').val('');
            document.getElementById('codigo_mae_consulta').style.borderColor = '';
            $('.desc_novo_nascimento').html('');
            return;
        });

        $('#id_animal_morte').typeahead({
            source: function(query, result) {
                $.ajax({
                    url:'fetch.php',
                    method:'POST',
                    data:{query:query,
                          local: \"{$local_id}\"},
                    dataType:'json',
                    success:function(data)
                    {
                        result($.map(data, function(item){
                        return item;
                    }));
                    }
                })
            }
        });

        $('#id_animal_morte').click(function(){
            $('#codigo_id_morte').val(0);
            $('#descricao_animal_morte').text('');
            $('.alert_erro_animal .negrito').html('');
            $('.alert_erro_animal span').html('');
            $('.alert_erro_animal').hide();
            return;
        });

        $.ajax({
            type: 'post',
            url: 'monta_select_pasto.php',
            data: {
                'local_id': \"{$local_id}\"
            },
            success: function(data){
                $('select.select-empresa-menu-control').html(data);
            }
        });
    });

    function reseta_confirma(){
        clickedConfirm = true;
    }

    $(document).ready(function() {
        needToConfirm = false;
        clickedConfirm = false; 
        window.onbeforeunload = askConfirm;
    });

    function askConfirm() {
        var slctCocho = $('#slctCocho').val();
        var nomeProduto = $('#nomeProduto').val();
        var qtdProduto = $('#qtdProduto').val();
        var descricao_lote_gravar = $('#descricao_lote_gravar').val();
        var descricao_lote_anterior = $('#descricao_lote_anterior').val();
        var qtde_bezerro = $('#qtde_bezerro').val();
        var select_pasto_bezerro = $('#select_pasto_bezerro').val();

        var qtde_femea_002 = $('#qtde_femea_002').val();
        var qtde_macho_002 = $('#qtde_macho_002').val();
        var select_pasto_macho_002 = $('#select_pasto_macho_002').val();

        var qtde_femea_003 = $('#qtde_femea_003').val();
        var qtde_macho_003 = $('#qtde_macho_003').val();
        var select_pasto_macho_003 = $('#select_pasto_macho_003').val();

        var qtde_femea_004 = $('#qtde_femea_004').val();
        var qtde_macho_004 = $('#qtde_macho_004').val();
        var select_pasto_macho_004 = $('#select_pasto_macho_004').val();

        var qtde_femea_005 = $('#qtde_femea_005').val();
        var qtde_macho_005 = $('#qtde_macho_005').val();
        var select_pasto_macho_005 = $('#select_pasto_macho_005').val();

    if (slctCocho=='000000000' && nomeProduto=='000000000' 
        && (qtdProduto=='' || qtdProduto==0 || qtdProduto=='0,00' || qtdProduto=='0.00') 
        && descricao_lote_gravar==descricao_lote_anterior 
        && (qtde_bezerro=='' || qtde_bezerro==0 || qtde_bezerro==undefined) && (select_pasto_bezerro=='000000000' || select_pasto_bezerro==undefined) 
        && (qtde_femea_002=='' || qtde_femea_002==0 || qtde_femea_002==undefined) 
        && (qtde_macho_002=='' || qtde_macho_002==0 || qtde_macho_002==undefined) && (select_pasto_macho_002=='000000000' || select_pasto_macho_002==undefined)
        && (qtde_femea_003=='' || qtde_femea_003==0 || qtde_femea_003==undefined) 
        && (qtde_macho_003=='' || qtde_macho_003==0 || qtde_macho_003==undefined) && (select_pasto_macho_003=='000000000' || select_pasto_macho_003==undefined)
        && (qtde_femea_004=='' || qtde_femea_004==0 || qtde_femea_004==undefined) 
        && (qtde_macho_004=='' || qtde_macho_004==0 || qtde_macho_004==undefined) && (select_pasto_macho_004=='000000000' || select_pasto_macho_004==undefined)
        && (qtde_femea_005=='' || qtde_femea_005==0 || qtde_femea_005==undefined) 
        && (qtde_macho_005=='' || qtde_macho_005==0 || qtde_macho_005==undefined) && (select_pasto_macho_005=='000000000' || select_pasto_macho_005==undefined)) {
            needToConfirm = false;
        }
        else {
            needToConfirm = true;
        }

        if(clickedConfirm){
            needToConfirm = false;
        }
        if (needToConfirm==true) {
            return ''; 
        }
    }

    $('.custom-select,textarea').change(function() {
        needToConfirm = true;
    });

    $(document).ready(function() {
        if (window.innerWidth <= 1370)
            $('#span_retirar').removeClass('col-lg-8'),
            $('#span_retirar').addClass('col-lg-12');
        else 
            $('#span_retirar').removeClass('col-lg-12'),
            $('#span_retirar').addClass('col-lg-8');
    });

    $(document).ready(function(){
        animal_sem_id();
    });

    $(document).ready(function() {
        if (window.innerWidth <= 991) 
            $('.modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select').addClass('input-lg');
        else 
            $('.modal-body form .tab-content #dados .row .form-group input, .modal-body form .tab-content #dados .row .form-group select').removeClass('input-lg');
    });

    $(document).ready(function() {
        $('#nascimento_animal').change(function(){
            const date = new Date();
            const data_atual = date.getFullYear()+'-'+
                              (date.getMonth()+1).AddZero()+'-'+
                              date.getDate().AddZero();

            const nascimento_animal = $('#nascimento_animal').val();

            if (nascimento_animal>data_atual) {
                $('#mensagem_erro_data').modal();
                $('#mensagem_erro_data .modal-body').html('A Data não pode ser maior que a data atual!');
                $('#nascimento_animal').val(data_atual);
                document.getElementById('nascimento_animal').style.borderColor = '#0076d7';
            }

            $('#codigo_mae_consulta').val('');
            $('#codigo_mae_animal').val('');
            $('#codigo_pai_animal').val('000000000');
            listar_estacao();
        });    

        $('#nascimento_animal').blur(function(){
            const date = new Date();
            const data_atual = date.getFullYear()+'-'+
                              (date.getMonth()+1).AddZero()+'-'+
                              date.getDate().AddZero();

            const nascimento_animal = $('#nascimento_animal').val();

            if (nascimento_animal=='') {
                $('#mensagem_erro_data').modal();
                $('#mensagem_erro_data .modal-body').html('A Data precisa ser informada!');
                $('#nascimento_animal').val(data_atual);
                document.getElementById('nascimento_animal').style.borderColor = '#0076d7';
            }
        });    

        $('#data_morte_animal').change(function(){
            const date = new Date();
            const data_atual = date.getFullYear()+'-'+
                              (date.getMonth()+1).AddZero()+'-'+
                              date.getDate().AddZero();

            const data_morte_animal = $('#data_morte_animal').val();

            if (data_morte_animal>data_atual) {
                $('#mensagem_erro_data').modal();
                $('#mensagem_erro_data .modal-body').html('A Data não pode ser maior que a data atual!');
                $('#data_morte_animal').val(data_atual);
                document.getElementById('data_morte_animal').style.borderColor = '#0076d7';
            }
        });    

        $('#data_morte_animal').blur(function(){
            const date = new Date();
            const data_atual = date.getFullYear()+'-'+
                              (date.getMonth()+1).AddZero()+'-'+
                              date.getDate().AddZero();

            const data_morte_animal = $('#data_morte_animal').val();

            if (data_morte_animal=='') {
                $('#mensagem_erro_data').modal();
                $('#mensagem_erro_data .modal-body').html('A Data precisa ser informada!');
                $('#data_morte_animal').val(data_atual);
                document.getElementById('data_morte_animal').style.borderColor = '#0076d7';
            }
        });    

        $('#dataNutricao').change(function(){
            const date = new Date();
            const data_atual = date.getFullYear()+'-'+
                              (date.getMonth()+1).AddZero()+'-'+
                              date.getDate().AddZero();

            const dataNutricao = $('#dataNutricao').val();

            if (dataNutricao>data_atual) {
                $('#mensagem_erro_data').modal();
                $('#mensagem_erro_data .modal-body').html('A Data não pode ser maior que a data atual!');
                $('#dataNutricao').val(data_atual);
                document.getElementById('dataNutricao').style.borderColor = '#0076d7';
            }
        });    

        $('#dataNutricao').blur(function(){
            const date = new Date();
            const data_atual = date.getFullYear()+'-'+
                              (date.getMonth()+1).AddZero()+'-'+
                              date.getDate().AddZero();

            const dataNutricao = $('#dataNutricao').val();

            if (dataNutricao=='') {
                $('#mensagem_erro_data').modal();
                $('#mensagem_erro_data .modal-body').html('A Data precisa ser informada!');
                $('#dataNutricao').val(data_atual);
                document.getElementById('dataNutricao').style.borderColor = '#0076d7';
            }
        });    

        $('#codigo_numerico_animal').change(function(){
            var codigo_alfa= $('#alfa_animal').val();
            var codigo_animal= $('#codigo_numerico_animal').val();
            var codigo_mae = $('#codigo_mae_animal').val();

            $.post('ler_animal_inclusao.php',{codigo_alfa: codigo_alfa,codigo_animal: codigo_animal, codigo_mae: codigo_mae}, function(valor){
                var php = valor.split('<|>');

                if (php[0]==1){
                    $('#mensagem_erro').modal();
                    $('#mensagem_erro .modal-body').html('O código ' + codigo_alfa + codigo_animal + ' já existe cadastrado no sistema para essa Mãe.');
                    $('#alfa_animal').val($('#codigo_alfa_anterior').val());
                    $('#codigo_numerico_animal').val($('#codigo_numerico_anterior').val());
                    document.getElementById('codigo_numerico_animal').focus();
                    return;
                }
            });
        });

        $('#alfa_animal').change(function(){
            var codigo_alfa= $('#alfa_animal').val();
            var codigo_animal= $('#codigo_numerico_animal').val();
            var codigo_mae = $('#codigo_mae_animal').val();

            $.post('ler_animal_inclusao.php',{codigo_alfa: codigo_alfa,codigo_animal: codigo_animal, codigo_mae: codigo_mae}, function(valor){
                var php = valor.split('<|>');

                if (php[0]==1){
                    $('#mensagem_erro').modal();
                    $('#mensagem_erro .modal-body').html('O código ' + codigo_alfa + codigo_animal + ' já existe cadastrado no sistema para essa Mãe.');
                    $('#alfa_animal').val($('#codigo_alfa_anterior').val());
                    $('#codigo_numerico_animal').val($('#codigo_numerico_anterior').val());
                    document.getElementById('codigo_numerico_animal').focus();
                    return;
                }
            });
        });

        $('.sexo_animal').change(function(){
            var codigo_alfa= $('#alfa_animal').val();
            var codigo_animal= $('#codigo_numerico_animal').val();
            var codigo_mae = $('#codigo_mae_animal').val();

            $.post('ler_animal_inclusao.php',{codigo_alfa: codigo_alfa,codigo_animal: codigo_animal, codigo_mae: codigo_mae}, function(valor){
                var php = valor.split('<|>');

                if (php[0]==1){
                    $('#mensagem_erro').modal();
                    $('#mensagem_erro .modal-body').html('O código ' + codigo_alfa + codigo_animal + ' já existe cadastrado no sistema para essa Mãe.');
                    $('#alfa_animal').val($('#codigo_alfa_anterior').val());
                    $('#codigo_numerico_animal').val($('#codigo_numerico_anterior').val());
                    $('#F').prop('checked', false);
                    $('#M').prop('checked', false);
                    document.getElementById('codigo_numerico_animal').focus();
                    return;
                }
            });
        });

        $('#categoria_morte').change(function(event) {
            var categoria = $('#categoria_morte').val();

            $('#sexo_morte').val(categoria.substr(0, 1));      
            $('#categoria_digitada_morte').val(categoria.substr(1, 3));      
        }); 

        $('#categoria_morte').click(function(){
            $('#alert_erro_animal .negrito').html('');
            $('#alert_erro_animal span').html('');
            $('.alert_erro_animal').hide();
            return;
        });

        $('#motivo_morte').click(function(){
            $('#alert_erro_animal .negrito').html('');
            $('#alert_erro_animal span').html('');
            $('.alert_erro_animal').hide();
            return;
        });

    });

    $(document).ready(function(){
        var controle_estoque = $('#controle_estoque').val();

        if (controle_estoque=='I') {
            $('.pelagem_id').show();
            $('.qtd_animal').hide();
        }
        else {
            $('.pelagem_id').hide();
            $('.qtd_animal').show();
        }

        var local_id = $('#local_id').val();
        $('#F').prop('checked', false);
        $('#M').prop('checked', false);

        $.post('ler_parametro_nascimento.php',{local_id:local_id}, function(valor){
            var php = valor.split('<|>');

            if (php[0]!=''){
                if (php[3]!='') {
                    $('#alfa_animal').val(php[3]);
                    $('#codigo_alfa_anterior').val(php[3]);
                    $('.alfa_animal').show();
                }
                else {
                    $('#alfa_animal').val('');
                    $('#codigo_alfa_anterior').val('');
                    $('.alfa_animal').hide();
                }

                if (php[4]!='') {
                    $('#codigo_numerico_animal').val(php[4]);
                    $('#codigo_numerico_anterior').val(php[4]);
                    $('.codigo_numerico_animal').show();
                    $('.codigo_mae_animal').show();
                    $('.codigo_pai_animal').show();
                    $('#ultima_estacao').html(php[6]);
                    $('#estacao_monta_id').val(php[5]);
                    $('.icon_nascimentos_previstos').show();
                }
                else {
                    $('#codigo_numerico_animal').val('');
                    $('#codigo_numerico_anterior').val('');
                    $('.codigo_numerico_animal').hide();
                    $('.codigo_mae_animal').hide();
                    $('.codigo_pai_animal').hide();
                }
            }
            else {
                $('#alfa_animal').val('');
                $('#codigo_alfa_anterior').val('');
                $('#codigo_numerico_animal').val('');
                $('#codigo_numerico_anterior').val('');
                $('.alfa_animal').hide();
                $('.codigo_numerico_animal').hide();
                $('.codigo_mae_animal').hide();
                $('.codigo_pai_animal').hide();
            }
        })
    });

    $(document).ready(function(){
        lerNutricao();
    });

    Number.prototype.AddZero= function(b,c){
        var  l= (String(b|| 10).length - String(this).length)+1;
        return l> 0? new Array(l).join(c|| '0')+this : this;
    }

</script>";

?>