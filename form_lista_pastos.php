<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $codigo_local = $_POST['local'];

   @ session_start(); 
    $_SESSION['local_pastos'] = $codigo_local;
    $contorle_estoque = $_SESSION['controle_estoque'];
 
    $cnpj_cliente = $_SESSION['id_cliente'];
    $arquivo = 'mapa/'.$cnpj_cliente.'/'.$codigo_local.'.json';
    $json_data = json_decode(file_get_contents($arquivo));
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

</head>

<body>
    <section class="panel">

  <?php    
    echo '<table class="table table-striped table-advance table-hover" id="tabela_pastos">';
                          
            echo '<tbody>';
                if ($codigo_local=='000000000') {
                    $sql = "SELECT * FROM tbl_pasto 
                                    WHERE tbl_pasto_lixeira=0 
                                 ORDER BY tbl_pasto_codigo_local ASC"; 
                }
                else {
                    $sql = "SELECT * FROM tbl_pasto 
                                    WHERE tbl_pasto_lixeira=0 AND 
                                          tbl_pasto_codigo_local = '$codigo_local' 
                                 ORDER BY tbl_pasto_codigo_local ASC"; 
                }

                $rs = mysqli_query($conector, $sql); 
                     
                while ($reg_pasto = mysqli_fetch_object($rs)){
                    $codigo = $reg_pasto->tbl_pasto_id;
                    $codigo_local = $reg_pasto->tbl_pasto_codigo_local;
                    $descricao_pasto = $reg_pasto->tbl_pasto_descricao;
                    $area = $reg_pasto->tbl_pasto_area;
                    $modulo = $reg_pasto->tbl_pasto_modulo;
                    $obs = $reg_pasto->tbl_pasto_descricao_lote;
                    $lixeira = $reg_pasto->tbl_pasto_lixeira;

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

                    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_local'");
                    $num_rows = mysqli_num_rows($tbl_local);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_local);
                        $nome = $reg->tbl_pessoa_nome;
                    }
                    else {
                        $nome = '';
                    }

                    $tbl_modulo = mysqli_query($conector, "select * from tbl_modulo_pasto where tbl_modulo_id='$modulo'");
                    $num_rows = mysqli_num_rows($tbl_modulo);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_modulo);
                        $desc_modulo = $reg->tbl_modulo_descricao;
                    }
                    else {
                        $desc_modulo = '';
                    }

                    $sql = "SELECT * FROM tbl_animal_pasto 
                        WHERE tbl_animal_pasto_id = $codigo AND 
                              tbl_animal_pasto_situacao = 'A'";

                    $tbl_animal_pasto = mysqli_query($conector, $sql);

                    $arrayAnimais = [
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0
                    ];

                    $arrayMachos = [
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0
                    ];
            
                    $arrayFemeas = [
                        0 => 0,
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0
                    ];

                    while($reg_animais = mysqli_fetch_object($tbl_animal_pasto)){
                        $sexo = $reg_animais->tbl_animal_pasto_sexo;

                        $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;
                        $data_acompanhamento_calculo = date("Y-m-d");
                        $date = new DateTime($data_nascimento); 
                        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                        if ($contorle_estoque=='I') {
                            for($i = 0; $i < count($arrayCategorias); $i++){
                                $idade_de = $arrayCategorias[$i]['idade_de'];
                                $idade_ate = $arrayCategorias[$i]['idade_ate'];
                                if($idade >= $idade_de && $idade <= $idade_ate){
                                    $arrayAnimais[$i] += 1;

                                    if ($sexo=='M') {
                                        $arrayMachos[$i] += 1;
                                    }
                                    else {
                                        $arrayFemeas[$i] += 1;
                                    }
                                }
                            }
                        }
                        else {
                            for($i = 0; $i < count($arrayCategorias); $i++){
                                $idade_de = $arrayCategorias[$i]['idade_de'];
                                $idade_ate = $arrayCategorias[$i]['idade_ate'];
                                if($idade >= $idade_de && $idade <= $idade_ate){
                                    $arrayAnimais[$i] += 1;
                                }else{
                                    $arrayAnimais[$i] += 0;
                                }
                            }
                        }
                    }

                    $arrayAnimaisCat = implode('!', $arrayAnimais);
                    $arrayAnimaisMachos = implode('!', $arrayMachos);
                    $arrayAnimaisFemeas = implode('!', $arrayFemeas);
                    $total_animais = mysqli_num_rows($tbl_animal_pasto);

                    $incluido_em=new DateTime($reg_pasto->tbl_pasto_incluido_em);
                    $incluido_por=$reg_pasto->tbl_pasto_incluido_por; 
                    $alterado_em=new DateTime($reg_pasto->tbl_pasto_alterado_em);
                    $alterado_por=$reg_pasto->tbl_pasto_alterado_por; 
                    $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
                    $alterado_em_edi = $alterado_em->format('d/m/Y H:i:s');

                    $pasto_existe = 'N';

                    foreach ($json_data->features as $data) {
                        $pasto = mb_strtoupper($data->properties->name, 'UTF-8');

                        if ($pasto==$descricao_pasto) {
                            $pasto_existe = 'S';
                            break;
                        }
                    }

                    $array_conta = array(
                        $reg_pasto->tbl_pasto_id,
                        $reg_pasto->tbl_pasto_codigo_local,
                        $reg_pasto->tbl_pasto_descricao,
                        $reg_pasto->tbl_pasto_latitude,
                        $reg_pasto->tbl_pasto_longitude,
                        $reg_pasto->tbl_pasto_area,
                        $incluido_em_edi,
                        $incluido_por,
                        $alterado_em_edi,
                        $alterado_por,
                        $reg_pasto->tbl_pasto_modulo,
                        $reg_pasto->tbl_pasto_tipo_capim,
                        $reg_pasto->tbl_pasto_array_qtd_animais_macho,
                        $reg_pasto->tbl_pasto_array_qtd_animais_femea,
                        $reg_pasto->tbl_pasto_array_qtd_animais_ambos,
                        $reg_pasto->tbl_pasto_array_categoria,
                        $obs,
                        $arrayAnimaisCat,
                        $arrayAnimaisMachos,
                        $arrayAnimaisFemeas
                    );   
                                    
                    $string_array = implode('|', $array_conta);

                        echo "<tr>";
                        echo "<td width='25%'>".$nome."</td>";
                        echo "<td width='20%'>".$desc_modulo."</td>";
                        echo "<td width='15%'>".$descricao_pasto."</td>";
                        if ($pasto_existe == 'N' && $modulo!=999) {
                            if ($total_animais==0) {
                                echo "<td width='5%'><i class='fa fa-frown-o' data-toggle='tooltip'='tooltip' data-placement='top' title='Este Pasto nao existe no mapa' style='color: red; text-align: center;'></i></td>";
                            }
                            else {
                                echo "<td width='5%'><i class='fa fa-frown-o' data-toggle='tooltip'='tooltip' data-placement='top' title='Este Pasto nao existe no mapa. Para excluir, primeiro transfira os animais' style='color: red; text-align: center;'></i></td>";
                            }
                        }
                        else {
                            echo "<td width='5%'></td>";
                        }
                        echo "<td width='15%'>".$total_animais."</td>";
                        echo "<td width='10%'>".number_format($area, 2, ",", ".")."</td>";
                        echo "<td width='10%'>";    
                        echo "<div class='btn-group'>";
                        echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_pasto(\"{$string_array}\")' ></i></a>"; 
                        if ($modulo!=999) {
                            if (($pasto_existe == 'N' && $total_animais==0)){
                                echo "<a class='btn' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left'title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                            }
                        }
                        echo "</div>";
                        echo "</td>";
                } 
                mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Local</th>
                    <th> Módulo</th>
                    <th> Descrição Pasto</th>
                    <th> &nbsp;</th>
                    <th> Qtde Animais</th>
                    <th> Área (ha)</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    //echo '</section>';

    //echo '<script src="js/tabela_pastos.js" charset="utf-8" type="text/javascript" ></script>';

?>

    </section>

    <script src="js/tabela_pastos.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>


                
                
