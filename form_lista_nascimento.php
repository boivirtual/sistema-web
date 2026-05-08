<?php
    function verificar_estacao($conector, $cobertura_id, $array_estacao){
        $sql = "SELECT * FROM tbl_cobertura
            INNER JOIN tbl_parametro_estacao_monta
                    ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta  
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_cobertura_id = '$cobertura_id'"; 

        $tbl_cobertura = mysqli_query($conector, $sql);
        $num_rows = mysqli_num_rows($tbl_cobertura);
        $tem_cobertura = 'N';

        if ($num_rows!=0) {
            $reg_cobertura = mysqli_fetch_object($tbl_cobertura);
            $nome_estacao = $reg_cobertura->tbl_par_estacao_nome;
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

        while ($reg_nasc = mysqli_fetch_object($rs)){
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

                    $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_fazenda'");
                    $num_rows = mysqli_num_rows($tab_fazenda);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_fazenda);
                        $desc_local = $reg->tbl_pessoa_nome;
                    }
                    else {
                        $desc_local = '';
                    }

                    $tab_pasto = mysqli_query($conector, "select * from tbl_pasto where tbl_pasto_id ='$codigo_pasto'");
                    $num_rows = mysqli_num_rows($tab_pasto);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pasto);
                        $desc_pasto = $reg->tbl_pasto_descricao;
                    }
                    else {
                        $desc_pasto = '';
                    }

                    $tab_animal = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$codigo'");
                    $num_rows = mysqli_num_rows($tab_animal);

                    if ($num_rows!=0){
                        $reg_animal = mysqli_fetch_object($tab_animal);
                        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                        $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
                        $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
                        $codigo_cor = $reg_animal->tbl_animal_codigo_pelagem;
                        $sexo = $reg_animal->tbl_animal_sexo; 
                        $mae = $reg_animal->tbl_animal_codigo_mae; 
                        $pai = $reg_animal->tbl_animal_codigo_pai; 
                        $situacao = $reg_animal->tbl_animal_situacao;
                        $estacao_monta = $reg_animal->tbl_animal_estacao_monta_nascimento;

                        $tab_estacao = mysqli_query($conector, "select * from tbl_parametro_estacao_monta where tbl_par_estacao_id='$estacao_monta'");
                        $num_rows_estacao = mysqli_num_rows($tab_estacao);

                        if ($num_rows_estacao!=0){
                            $reg = mysqli_fetch_object($tab_estacao);
                            $descricao_estacao = $reg->tbl_par_estacao_nome;
                        }
                        else {
                            $descricao_estacao = '';
                        }

                        if ($sexo=='N') {
                            $desc_sexo = '';
                        }
                        else {
                            $desc_sexo = $sexo;
                        }

                        $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
                        $num_rows_raca = mysqli_num_rows($tab_raca);

                        if ($num_rows_raca!=0){
                            $reg = mysqli_fetch_object($tab_raca);
                            $descricao_raca = $reg->tab_descricao_raca;
                        }
                        else {
                            $descricao_raca = '';
                        }

                        $tab_cor = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem ='$codigo_cor'");
                        $num_rows_cor = mysqli_num_rows($tab_cor);

                        if ($num_rows_cor!=0){
                            $reg = mysqli_fetch_object($tab_cor);
                            $descricao_cor = $reg->tab_descricao_pelagem;
                        }
                        else {
                            $descricao_cor = '';
                        }

                        $tab_mae = mysqli_query($conector, "select * from tbl_animais 
                                    inner join tabela_racas
                                            on tab_codigo_raca=tbl_animal_codigo_raca
                                    where tbl_animal_codigo_id='$mae'");
                        $num_rows_mae = mysqli_num_rows($tab_mae);

                        if ($num_rows_mae!=0){
                            $reg = mysqli_fetch_object($tab_mae);
                            $descricao_mae = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                            $mae_raca = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico . ' - ' . $reg->tab_descricao_raca;
                        }
                        else {
                            $descricao_mae = '';
                            $mae='';
                            $mae_raca='';
                        }

                        $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
                        $num_rows_pai = mysqli_num_rows($tab_pai);

                        if ($num_rows_pai!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            $descricao_pai = $reg->tbl_semem_nome;
                            $pai = $reg->tbl_semem_codigo_id;
                        }
                        else {
                            $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                            $num_rows_pai = mysqli_num_rows($tab_pai);

                            if ($num_rows_pai!=0){
                                $reg = mysqli_fetch_object($tab_pai);
                                $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
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
                                
                        $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
                        $num_rows_raca = mysqli_num_rows($tab_raca);

                        if ($num_rows_raca!=0){
                            $reg = mysqli_fetch_object($tab_raca);
                            $descricao_raca = $reg->tab_descricao_raca;
                        }
                        else {
                            $descricao_raca = '';
                        }

                        $tab_cor = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem ='$codigo_cor'");
                        $num_rows_cor = mysqli_num_rows($tab_cor);

                        if ($num_rows_cor!=0){
                            $reg = mysqli_fetch_object($tab_cor);
                            $descricao_cor = $reg->tab_descricao_pelagem;
                        }
                        else {
                            $descricao_cor = '';
                        }

                        $tab_mae = mysqli_query($conector, "select * from tbl_animais 
                                    inner join tabela_racas
                                            on tab_codigo_raca=tbl_animal_codigo_raca
                                    where tbl_animal_codigo_id='$mae'");
                        $num_rows_mae = mysqli_num_rows($tab_mae);

                        if ($num_rows_mae!=0){
                            $reg = mysqli_fetch_object($tab_mae);
                            $descricao_mae = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                            $mae_raca = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico . ' - ' . $reg->tab_descricao_raca;
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
                    $tbl_item_cobertura = mysqli_query($conector,"SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                         WHERE tbl_cobertura_lixeira=0 AND 
                               tbl_ite_cobertura_numero_id='$cobertura_id' AND 
                               tbl_ite_cobertura_numero_item='$item_cobertura'"); 
 
                    $num_rows = mysqli_num_rows($tbl_item_cobertura);

                    if ($num_rows!=0) {
                        while ($reg_item = mysqli_fetch_object($tbl_item_cobertura)) {
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
                            $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                                WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");
                            $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                            $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
                                WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                                      tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                                ORDER BY tbl_ite_protocoloiatf_id ASC");
                                
                            while($reg_itens = mysqli_fetch_object($sql)){
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

        $rs = mysqli_query($conector, $sql); 
        $num_rows_estoque = mysqli_num_rows($rs);
        $total_nascimento = 0;
        $total_natimorto = 0;
        $total_absorcao = 0;
        $total_aborto = 0;

        while ($reg_nasc = mysqli_fetch_object($rs)){
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

                        $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_fazenda'");
                        $num_rows = mysqli_num_rows($tab_fazenda);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_fazenda);
                            $desc_local = $reg->tbl_pessoa_nome;
                        }
                        else {
                            $desc_local = '';
                        }

                        $tab_pasto = mysqli_query($conector, "select * from tbl_pasto where tbl_pasto_id ='$codigo_pasto'");
                        $num_rows = mysqli_num_rows($tab_pasto);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pasto);
                            $desc_pasto = $reg->tbl_pasto_descricao;
                        }
                        else {
                            $desc_pasto = '';
                        }

                        $tab_animal = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$codigo'");
                        $num_rows = mysqli_num_rows($tab_animal);

                        if ($num_rows!=0){
                            $reg_animal = mysqli_fetch_object($tab_animal);
                            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                            $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
                            $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
                            $codigo_cor = $reg_animal->tbl_animal_codigo_pelagem;
                            $sexo = $reg_animal->tbl_animal_sexo; 
                            $mae = $reg_animal->tbl_animal_codigo_mae; 
                            $pai = $reg_animal->tbl_animal_codigo_pai; 
                            $situacao = $reg_animal->tbl_animal_situacao;
                            $estacao_monta = $reg_animal->tbl_animal_estacao_monta_nascimento;

                            $tab_estacao = mysqli_query($conector, "select * from tbl_parametro_estacao_monta where tbl_par_estacao_id='$estacao_monta'");
                            $num_rows_estacao = mysqli_num_rows($tab_estacao);

                            if ($num_rows_estacao!=0){
                                $reg = mysqli_fetch_object($tab_estacao);
                                $descricao_estacao = $reg->tbl_par_estacao_nome;
                            }
                            else {
                                $descricao_estacao = '';
                            }

                            if ($sexo=='N') {
                                $desc_sexo = '';
                            }
                            else {
                                $desc_sexo = $sexo;
                            }

                            $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
                            $num_rows_raca = mysqli_num_rows($tab_raca);

                            if ($num_rows_raca!=0){
                                $reg = mysqli_fetch_object($tab_raca);
                                $descricao_raca = $reg->tab_descricao_raca;
                            }
                            else {
                                $descricao_raca = '';
                            }

                            $tab_cor = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem ='$codigo_cor'");
                            $num_rows_cor = mysqli_num_rows($tab_cor);

                            if ($num_rows_cor!=0){
                                $reg = mysqli_fetch_object($tab_cor);
                                $descricao_cor = $reg->tab_descricao_pelagem;
                            }
                            else {
                                $descricao_cor = '';
                            }

                            $tab_mae = mysqli_query($conector, "select * from tbl_animais 
                                        inner join tabela_racas
                                                on tab_codigo_raca=tbl_animal_codigo_raca
                                        where tbl_animal_codigo_id='$mae'");
                            $num_rows_mae = mysqli_num_rows($tab_mae);

                            if ($num_rows_mae!=0){
                                $reg = mysqli_fetch_object($tab_mae);
                                $descricao_mae = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                                $mae_raca = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico . ' - ' . $reg->tab_descricao_raca;
                            }
                            else {
                                $descricao_mae = '';
                                $mae='';
                                $mae_raca='';
                            }

                            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
                            $num_rows_pai = mysqli_num_rows($tab_pai);

                            if ($num_rows_pai!=0){
                                $reg = mysqli_fetch_object($tab_pai);
                                $descricao_pai = $reg->tbl_semem_nome;
                                $pai = $reg->tbl_semem_codigo_id;
                            }
                            else {
                                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                                $num_rows_pai = mysqli_num_rows($tab_pai);

                                if ($num_rows_pai!=0){
                                    $reg = mysqli_fetch_object($tab_pai);
                                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
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
                                    
                            $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
                            $num_rows_raca = mysqli_num_rows($tab_raca);

                            if ($num_rows_raca!=0){
                                $reg = mysqli_fetch_object($tab_raca);
                                $descricao_raca = $reg->tab_descricao_raca;
                            }
                            else {
                                $descricao_raca = '';
                            }

                            $tab_cor = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem ='$codigo_cor'");
                            $num_rows_cor = mysqli_num_rows($tab_cor);

                            if ($num_rows_cor!=0){
                                $reg = mysqli_fetch_object($tab_cor);
                                $descricao_cor = $reg->tab_descricao_pelagem;
                            }
                            else {
                                $descricao_cor = '';
                            }

                            $tab_mae = mysqli_query($conector, "select * from tbl_animais 
                                        inner join tabela_racas
                                                on tab_codigo_raca=tbl_animal_codigo_raca
                                        where tbl_animal_codigo_id='$mae'");
                            $num_rows_mae = mysqli_num_rows($tab_mae);

                            if ($num_rows_mae!=0){
                                $reg = mysqli_fetch_object($tab_mae);
                                $descricao_mae = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                                $mae_raca = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico . ' - ' . $reg->tab_descricao_raca;
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
                        $tbl_item_cobertura = mysqli_query($conector,"SELECT * FROM tbl_item_cobertura 
                        INNER JOIN tbl_cobertura
                                ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                             WHERE tbl_cobertura_lixeira=0 AND 
                                   tbl_ite_cobertura_numero_id='$cobertura_id' AND 
                                   tbl_ite_cobertura_numero_item='$item_cobertura'"); 
     
                        $num_rows = mysqli_num_rows($tbl_item_cobertura);

                        if ($num_rows!=0) {
                            while ($reg_item = mysqli_fetch_object($tbl_item_cobertura)) {
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
                            $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                                WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");
                            $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                            $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
                                WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                                      tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                                ORDER BY tbl_ite_protocoloiatf_id ASC");
                                
                            while($reg_itens = mysqli_fetch_object($sql)){
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
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html> 


                
                
