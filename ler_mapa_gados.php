<?php 
    include "conecta_mysql.inc";

    @ session_start();
    $controle_estoque = $_SESSION['controle_estoque'];
    $cnpj_cliente = $_SESSION['id_cliente'];

    if (isset($_POST["local_id"]) && $_POST["local_id"] !='' && $_POST["local_id"] !=0) {
        $local_id = $_POST["local_id"];
        $arquivo = 'mapa/'.$cnpj_cliente.'/'.$local_id.'.json';
        $json_data = json_decode(file_get_contents($arquivo));
        $local_digitado = mysqli_real_escape_string($conector, $_POST["local_id"]); 
    } 
    else if (isset($_SESSION["local_id"]) && $_SESSION["local_id"] !='' && $_SESSION["local_id"] !=0) {
        $local_id = $_SESSION["local_id"];
        $arquivo = 'mapa/'.$cnpj_cliente.'/'.$local_id.'.json'; 
        $json_data = json_decode(file_get_contents($arquivo));
        $local_digitado = mysqli_real_escape_string($conector, $_SESSION["local_id"]);
    }
    else {
        $local_digitado = 0;
    }

    $track_modulo_id = 0;
    $total_animais_fazenda = 0;

    $i_cores = 0;
    $cores = ['#7FFFD4', '#66CDAA', '#40E0D0', '#00FF7F', '#48D1CC', '#3CB371', '#00CED1', '#2E8B57'];

    $tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
        WHERE tbl_pasto_lixeira=0 AND 
              tbl_pasto_modulo = '999' AND 
              tbl_pasto_codigo_local = '$local_digitado' 
        ORDER BY tbl_pasto_modulo, tbl_pasto_codigo_local ASC");

    $num_rows_pasto = mysqli_num_rows($tbl_pasto);

    if ($num_rows_pasto!=0) {
        while($reg_pasto = mysqli_fetch_object($tbl_pasto)){
            $pasto_id = $reg_pasto->tbl_pasto_id;
            $pasto_nome = $reg_pasto->tbl_pasto_descricao;
            $modulo_id = $reg_pasto->tbl_pasto_modulo;
            $capim_id= $reg_pasto->tbl_pasto_tipo_capim;

            //pegando as categorias
            $array_categoria = explode("!", $reg_pasto->tbl_pasto_array_categoria);
            $arrayCategorias = [];

            for($i = 0; $i < count($array_categoria); $i++){
                $codigo_categoria = $array_categoria[$i];
            
                $ssql = "SELECT * FROM tabela_categoria_idade 
                WHERE tab_codigo_categoria_idade ='$codigo_categoria' and 
                      tab_registro_lixeira_categoria_idade='0'"; 
                
                $query = mysqli_query($conector,$ssql); 
                $fila = mysqli_fetch_object($query);
            
                $codigo_id = $fila->tab_codigo_categoria_idade ;
                $idade_de = $fila->tab_categoria_idade_de;
                $idade_ate = $fila->tab_categoria_idade_ate;
            
                if ($idade_ate==999999999){
                    $descricaoCategorias = [
                        "id" => $codigo_id,
                        "idade_de" => $idade_de,
                        "idade_ate" => $idade_ate
                    ];
                    array_push($arrayCategorias, $descricaoCategorias);
                }
                else {
                    $descricaoCategorias = [
                        "id" => $codigo_id,
                        "idade_de" => $idade_de,
                        "idade_ate" => $idade_ate
                    ];
                    array_push($arrayCategorias, $descricaoCategorias);
                }
            }

            $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_id AND tbl_animal_pasto_situacao = 'A'";
            $query = mysqli_query($conector, $sql);

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

            while($reg_animais = mysqli_fetch_object($query)){
                $sexo = $reg_animais->tbl_animal_pasto_sexo;
                $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($data_nascimento); // Data de Nascimento
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                if ($controle_estoque=='I') {
                    for($i = 0; $i < count($arrayCategorias); $i++){
                        $idade_de = $arrayCategorias[$i]['idade_de'];
                        $idade_ate = $arrayCategorias[$i]['idade_ate'];
                        if($meses >= $idade_de && $meses <= $idade_ate && $sexo == "F"){
                            $arrayFemea[$i] += 1;
                        }elseif($meses >= $idade_de && $meses <= $idade_ate && $sexo == "M"){
                            $arrayMacho[$i] += 1;
                        }else{
                            $arrayFemea[$i] += 0;
                            $arrayMacho[$i] += 0;
                        }
                    }
                }
                else {
                    $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    for($i = 0; $i < count($arrayCategorias); $i++){
                        $idade_de = $arrayCategorias[$i]['idade_de'];
                        $idade_ate = $arrayCategorias[$i]['idade_ate'];
                        if($meses >= $idade_de && $meses <= $idade_ate && $sexo == "F"){
                            $arrayFemea[$i] += 1;
                        }elseif($meses >= $idade_de && $meses <= $idade_ate && $sexo == "M"){
                            $arrayMacho[$i] += 1;
                        }else{
                            $arrayFemea[$i] += 0;
                            $arrayMacho[$i] += 0;
                        }
                    }
                }
            }

            $total_animais = 0;
            $bezerros = 0;
            $femeas = 0;
            $machos = 0;

            for($i = 0; $i < count($arrayCategorias); $i++){
                if($arrayMacho[$i] != 0 && $i == 0){
                    $total_animais += $arrayMacho[$i];
                    $bezerros += $arrayMacho[$i];
                }else{
                    $total_animais += $arrayMacho[$i];
                    $machos += $arrayMacho[$i];
                }

                if($arrayFemea[$i] != 0 && $i == 0){
                    $total_animais += $arrayFemea[$i];
                    $bezerros += $arrayFemea[$i];
                }else{
                    $total_animais += $arrayFemea[$i];
                    $femeas += $arrayFemea[$i];
                }
            }

            $total_animais_fazenda += $femeas + $machos + $bezerros;

            //pegando o tipo do capim de cada pasto
            $tbl_tipo_capim = mysqli_query($conector, "select * from tbl_tipo_capim where tbl_tipo_capim_id='$capim_id'");
            $num_rows = mysqli_num_rows($tbl_tipo_capim);

            if($num_rows!=0){
                $reg = mysqli_fetch_object($tbl_tipo_capim);
                $tipo_capim = $reg->tbl_tipo_capim_descricao;
            }else{
                $tipo_capim = '';
            }

            if($total_animais == 0){
                echo "<div class='col-lg-1 col-md-2 col-sm-3 col-xs-3 item_mapa' ondrop='drop(event, this.id, this)' ondragover='allowDrop(event)' onclick='mais_info(this.id)' id='\"{$pasto_id}\"' style='background-color: #fffff;'>
                    <div class='pasto_titulo'>
                        <strong>$pasto_nome</strong>
                    </div></div>";
            }else{
                echo "<div class='col-lg-1 col-md-2 col-sm-3 col-xs-3 item_mapa' draggable='true' ondragstart='drag(event, this)' ondrop='drop(event, this.id, this)' ondragover='allowDrop(event)' onclick='mais_info(this.id)' id='\"{$pasto_id}\"' style='background-color: #fffff;'>
                    <div class='pasto_titulo'>
                        <strong>$pasto_nome</strong>
                    </div>";
                    if($bezerros != 0){
                        echo "<div class='img_mapa'>
                                <img src='img/bezerro.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$bezerros</p>
                            </div>";
                    }
                    if($femeas != 0){
                        echo "<div class='img_mapa' style='top: 42.5%;'>
                                <img src='img/vaca.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$femeas</p>
                            </div>";
                    }
                    if($machos != 0){
                        echo "<div class='img_mapa' style='top: 70%;'>
                                <img src='img/gado.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$machos</p>
                            </div>";
                    }
                echo "<div class='vl'></div>
                    <div class='qtde_animais_pasto'>
                        <span draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>$total_animais</span>
                    </div>
                </div>";
            }
        }
    }

    $sql = "SELECT * FROM tbl_pasto 
        WHERE tbl_pasto_lixeira=0 AND 
              tbl_pasto_codigo_local = $local_digitado AND 
              tbl_pasto_modulo != '999' AND 
              tbl_pasto_modulo != '1006' AND 
              tbl_pasto_modulo != '1007'
        ORDER BY tbl_pasto_modulo, tbl_pasto_codigo_local ASC";
    $rs = mysqli_query($conector, $sql);
    $num_rows_pasto = mysqli_num_rows($rs);

    if ($num_rows_pasto!=0) {
        while($reg_pasto = mysqli_fetch_object($rs)){
            $pasto_id = $reg_pasto->tbl_pasto_id;
            $pasto_nome = $reg_pasto->tbl_pasto_descricao;
            $modulo_id = $reg_pasto->tbl_pasto_modulo;
            $capim_id= $reg_pasto->tbl_pasto_tipo_capim;

            //pegando as categorias
            $array_categoria = explode("!", $reg_pasto->tbl_pasto_array_categoria);
            $arrayCategorias = [];

            for($i = 0; $i < count($array_categoria); $i++){
                $codigo_categoria = $array_categoria[$i];
            
                $ssql = "SELECT * FROM tabela_categoria_idade 
                WHERE tab_codigo_categoria_idade ='$codigo_categoria' and 
                      tab_registro_lixeira_categoria_idade='0'"; 
                
                $query = mysqli_query($conector,$ssql); 
                $fila = mysqli_fetch_object($query);
            
                $codigo_id = $fila->tab_codigo_categoria_idade ;
                $idade_de = $fila->tab_categoria_idade_de;
                $idade_ate = $fila->tab_categoria_idade_ate;
            
                if ($idade_ate==999999999){
                    $descricaoCategorias = [
                        "id" => $codigo_id,
                        "idade_de" => $idade_de,
                        "idade_ate" => $idade_ate
                    ];
                    array_push($arrayCategorias, $descricaoCategorias);
                }
                else {
                    $descricaoCategorias = [
                        "id" => $codigo_id,
                        "idade_de" => $idade_de,
                        "idade_ate" => $idade_ate
                    ];
                    array_push($arrayCategorias, $descricaoCategorias);
                }
            }

            $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_id AND tbl_animal_pasto_situacao = 'A'";
            $query = mysqli_query($conector, $sql);

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

            while($reg_animais = mysqli_fetch_object($query)){
                $sexo = $reg_animais->tbl_animal_pasto_sexo;
                $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($data_nascimento); // Data de Nascimento
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                if ($controle_estoque=='I') {
                    for($i = 0; $i < count($arrayCategorias); $i++){
                        $idade_de = $arrayCategorias[$i]['idade_de'];
                        $idade_ate = $arrayCategorias[$i]['idade_ate'];
                        if($meses >= $idade_de && $meses <= $idade_ate && $sexo == "F"){
                            $arrayFemea[$i] += 1;
                        }elseif($meses >= $idade_de && $meses <= $idade_ate && $sexo == "M"){
                            $arrayMacho[$i] += 1;
                        }else{
                            $arrayFemea[$i] += 0;
                            $arrayMacho[$i] += 0;
                        }
                    }
                }
                else {
                    $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    for($i = 0; $i < count($arrayCategorias); $i++){
                        $idade_de = $arrayCategorias[$i]['idade_de'];
                        $idade_ate = $arrayCategorias[$i]['idade_ate'];
                        if($meses >= $idade_de && $meses <= $idade_ate && $sexo == "F"){
                            $arrayFemea[$i] += 1;
                        }elseif($meses >= $idade_de && $meses <= $idade_ate && $sexo == "M"){
                            $arrayMacho[$i] += 1;
                        }else{
                            $arrayFemea[$i] += 0;
                            $arrayMacho[$i] += 0;
                        }
                    }
                }
            }

            $total_animais = 0;
            $bezerros = 0;
            $femeas = 0;
            $machos = 0;

            for($i = 0; $i < count($arrayCategorias); $i++){
                if($arrayMacho[$i] != 0 && $i == 0){
                    $total_animais += $arrayMacho[$i];
                    $bezerros += $arrayMacho[$i];
                }else{
                    $total_animais += $arrayMacho[$i];
                    $machos += $arrayMacho[$i];
                }

                if($arrayFemea[$i] != 0 && $i == 0){
                    $total_animais += $arrayFemea[$i];
                    $bezerros += $arrayFemea[$i];
                }else{
                    $total_animais += $arrayFemea[$i];
                    $femeas += $arrayFemea[$i];
                }
            }

            $total_animais_fazenda += $femeas + $machos + $bezerros;
            
            //pegando o tipo do capim de cada pasto
            $tbl_tipo_capim = mysqli_query($conector, "select * from tbl_tipo_capim where tbl_tipo_capim_id='$capim_id'");
            $num_rows = mysqli_num_rows($tbl_tipo_capim);

            if($num_rows!=0){
                $reg = mysqli_fetch_object($tbl_tipo_capim);
                $tipo_capim = $reg->tbl_tipo_capim_descricao;
            }else{
                $tipo_capim = '';
            }

            //$pasto_existe = 'N';        
            $pasto_existe = 'S';

            foreach ($json_data->features as $data) {
                $pasto = mb_strtoupper($data->properties->name, 'UTF-8');

                if ($pasto==$pasto_nome) {
                    $pasto_existe = 'S';
                    break;
                }
            }

            if($track_modulo_id == 0){
                 //reseta o contador das cores
                if($i_cores > 7){
                    $i_cores = 0;
                }
                if($total_animais == 0){
                    if ($pasto_existe=='S') {
                        echo "<div class='col-lg-1 col-md-2 col-sm-3 col-xs-3 item_mapa' ondrop='drop(event, this.id, this)' ondragover='allowDrop(event)' onclick='mais_info(this.id)' id='\"{$pasto_id}\"' style='background-color: $cores[$i_cores];'>
                                <div class='pasto_titulo'>
                                    <strong>$pasto_nome</strong>
                                </div>
                                <div class='tipo_capim'>
                                    <span>$tipo_capim</span>
                                </div>
                            </div>";
                    }
                }else{
                    if ($pasto_existe=='S') {
                        echo "<div class='col-lg-1 col-md-2 col-sm-3 col-xs-3 item_mapa' draggable='true' ondragstart='drag(event, this)' ondrop='drop(event, this.id, this)' ondragover='allowDrop(event)' onclick='mais_info(this.id)' id='\"{$pasto_id}\"' style='background-color: $cores[$i_cores];'>
                                <div class='pasto_titulo'>
                                    <strong>$pasto_nome</strong>
                                </div>
                                <div class='tipo_capim'>
                                    <span>$tipo_capim</span>
                                </div>";
                    }
                    if($bezerros != 0){
                        echo "<div class='img_mapa'>
                                <img src='img/bezerro.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$bezerros</p>
                            </div>";
                    }
                    if($femeas != 0){
                        echo "<div class='img_mapa' style='top: 42.5%;'>
                                <img src='img/vaca.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$femeas</p>
                            </div>";
                    }
                    if($machos != 0){
                        echo "<div class='img_mapa' style='top: 70%;'>
                                <img src='img/gado.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$machos</p>
                            </div>";
                    }
                    
                    echo "<div class='vl'></div>
                            <div class='qtde_animais_pasto'>
                                <span draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>$total_animais</span>
                            </div>
                        </div>";
                }
                                            
                $track_modulo_id = $modulo_id;
            }else if($modulo_id > $track_modulo_id){
                $i_cores++;
        
                //reseta o contador das cores
                if($i_cores > 7){
                    $i_cores = 0;
                }
        
                if($total_animais == 0){
                    if ($pasto_existe=='S') {
                        echo "<div class='col-lg-1 col-md-2 col-sm-3 col-xs-3 item_mapa' ondrop='drop(event, this.id, this)' ondragover='allowDrop(event)' onclick='mais_info(this.id)' id='\"{$pasto_id}\"' style='background-color: $cores[$i_cores];'>
                                <div class='pasto_titulo'>
                                    <strong>$pasto_nome</strong>
                                </div>
                                <div class='tipo_capim'>
                                    <span>$tipo_capim</span>
                                </div>
                            </div>";
                    }
                }else{
                    if ($pasto_existe=='S') {
                        echo "<div class='col-lg-1 col-md-2 col-sm-3 col-xs-3 item_mapa' draggable='true' ondragstart='drag(event, this)' ondrop='drop(event, this.id, this)' ondragover='allowDrop(event)' onclick='mais_info(this.id)' id='\"{$pasto_id}\"' style='background-color: $cores[$i_cores];'>
                                <div class='pasto_titulo'>
                                    <strong>$pasto_nome</strong>
                                </div>
                                <div class='tipo_capim'>
                                    <span>$tipo_capim</span>
                                </div>";
                    }
                    if($bezerros != 0){
                        echo "<div class='img_mapa'>
                                <img src='img/bezerro.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$bezerros</p>
                            </div>";
                    }
                    if($femeas != 0){
                        echo "<div class='img_mapa' style='top: 42.5%;'>
                                <img src='img/vaca.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$femeas</p>
                            </div>";
                    }
                    if($machos != 0){
                        echo "<div class='img_mapa' style='top: 70%;'>
                                <img src='img/gado.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$machos</p>
                            </div>";
                    }
                    
                    echo "<div class='vl'></div>
                            <div class='qtde_animais_pasto'>
                                <span draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>$total_animais</span>
                            </div>
                        </div>";
                }
            }else{
                //reseta o contador das cores
                if($i_cores > 7){
                    $i_cores = 0;
                }
        
                if($total_animais == 0){
                    if ($pasto_existe=='S') {
                        echo "<div class='col-lg-1 col-md-2 col-sm-3 col-xs-3 item_mapa' ondrop='drop(event, this.id, this)' ondragover='allowDrop(event)' onclick='mais_info(this.id)' id='\"{$pasto_id}\"' style='background-color: $cores[$i_cores];'>
                                <div class='pasto_titulo'>
                                    <strong>$pasto_nome</strong>
                                </div>
                                <div class='tipo_capim'>
                                    <span>$tipo_capim</span>
                                </div>
                            </div>";
                    }
                }else{
                    if ($pasto_existe=='S') {
                        echo "<div class='col-lg-1 col-md-2 col-sm-3 col-xs-3 item_mapa' draggable='true' ondragstart='drag(event, this)' ondrop='drop(event, this.id, this)' ondragover='allowDrop(event)' onclick='mais_info(this.id)' id='\"{$pasto_id}\"' style='background-color: $cores[$i_cores];'>
                                <div class='pasto_titulo'>
                                    <strong>$pasto_nome</strong>
                                </div>
                                <div class='tipo_capim'>
                                    <span>$tipo_capim</span>
                            </div>";
                    }
                    
                    if($bezerros != 0){
                        echo "<div class='img_mapa'>
                                <img src='img/bezerro.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$bezerros</p>
                            </div>";
                    }
                    if($femeas != 0){
                        echo "<div class='img_mapa' style='top: 42.5%;'>
                                <img src='img/vaca.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$femeas</p>
                            </div>";
                    }
                    if($machos != 0){
                        echo "<div class='img_mapa' style='top: 70%;'>
                                <img src='img/gado.png' class='fotos_mapa' draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>
                                <p>$machos</p>
                            </div>";
                    }
                    if($machos == 0 && $femeas == 0 && $bezerros == 0 && $total_animais != 0){
                        echo "<div class='img_mapa'>
                                <img src='img/gado.png' class='fotos_mapa'>
                                <p draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>$total_animais</p>
                            </div>";
                    }
                    echo "<div class='vl'></div>
                            <div class='qtde_animais_pasto'>
                                <p draggable='true' ondragstart='drag(event)' id='{$pasto_id}'>$total_animais</p>
                            </div>
                        </div>";
                }
            }
            $track_modulo_id = $modulo_id;
        }

    }
    
    mysqli_close($conector);

    echo "<input value='$total_animais_fazenda' hidden='true' id='totalAnimaisFazenda'></input>";

    echo "<script src='js/jquery.redirect.js'></script>
            <script>
                function mais_info(clicked_id){
                    $.redirect('form_mapa_gados_movimentacao.php', {'pasto_id': clicked_id});
                }

                $(document).ready(function() {
                    if (window.innerWidth <= 459) 
                        $('div.item_mapa').addClass('col-xs-4'),
                        $('div.item_mapa').css('margin-bottom', '5%');
                    else 
                        $('div.item_mapa').removeClass('col-xs-4'),
                        $('div.item_mapa').css('margin-bottom', '1%');
                });

                $(document).ready(function() {
                    if (window.innerWidth <= 375) 
                        $('div.item_mapa').addClass('col-xs-6'),
                        $('div.item_mapa').css('margin-bottom', '5%');
                    else 
                        $('div.item_mapa').removeClass('col-xs-6'),
                        $('div.item_mapa').css('margin-bottom', '1%');
                });

                $(document).ready(function() {
                    if (window.innerWidth <= 270) 
                        $('div.item_mapa').addClass('col-xs-12'),
                        $('div.item_mapa').css('margin-bottom', '5%');
                    else 
                        $('div.item_mapa').removeClass('col-xs-12'),
                        $('div.item_mapa').css('margin-bottom', '1%');
                });
            </script>";
?>