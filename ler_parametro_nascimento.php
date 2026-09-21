<?php
include "conecta_mysql.inc";

$str='';
$valor[0] = 0;
$valor[1] = 0;
$valor[2] = 0;
$valor[3] = 0;
$valor[4] = 0;
$valor[5] = 0;
$valor[6] = 0;
$valor[7] = 0;

$codigo_local = $_POST['local_id'];

$tbl_par = "SELECT * FROM tbl_parametro_nascimento WHERE 
      tbl_par_codigo_local='$codigo_local' AND tbl_par_lixeira=0";  
$qr = mysqli_query($conector, $tbl_par);
$num_rows = mysqli_num_rows($qr);

if ($num_rows!=0){
	$reg_para = mysqli_fetch_object($qr);

	$valor[0] = $reg_para->tbl_par_estacao_monta_inicial;
	$valor[1] = $reg_para->tbl_par_estacao_monta_final;
	$valor[2] = $reg_para->tbl_par_codigo_local;
	$valor[3] = $reg_para->tbl_par_codigo_alfa;
	$valor[4] = $reg_para->tbl_par_codigo_numerico;
}

$data_hoje = date('Y-m-d');

$sql = mysqli_query($conector, "SELECT * FROM tbl_cobertura 
      INNER JOIN tbl_item_cobertura 
              ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
      WHERE tbl_cobertura_lixeira=0 AND 
            tbl_cobertura_codigo_local = '$codigo_local' AND 
            tbl_cobertura_controle='C' AND 
            tbl_ite_cobertura_resultado_diagnostico='P' AND
            (tbl_ite_cobertura_nascido is null OR 
             tbl_ite_cobertura_nascido='')");  
$num_rows = mysqli_num_rows($sql);

if($num_rows != 0){
      $reg_item_cobertura = mysqli_fetch_object($sql);
      $id_estacao = $reg_item_cobertura->tbl_cobertura_codigo_estacao_monta;
      $cobertura_id = $reg_item_cobertura->tbl_cobertura_id;
}
else {
      $id_estacao=0;
      $cobertura_id=0;
}

if ($id_estacao!=0) {
    $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
        WHERE tbl_par_estacao_id = '$id_estacao'");  
}
else {
    $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
        WHERE tbl_par_codigo_local = '$codigo_local' AND 
              tbl_par_lixeira=0 AND 
              tbl_par_estacao_monta_inicial<='$data_hoje' AND 
              tbl_par_estacao_monta_final>='$data_hoje'");  
}

$num_rows = mysqli_num_rows($sql);

if($num_rows != 0){
      $reg_estacao = mysqli_fetch_object($sql);
      $id_estacao = $reg_estacao->tbl_par_estacao_id;
      $desc_estacao = $reg_estacao->tbl_par_estacao_nome;
}
else {
      $id_estacao=0;
      $desc_estacao = '';
}

// VERIFICA SE TEM ANIMAIS NÃO NASCIDO NA ESTACAO COM MAIS DE 303 DIAS DE GESTAÇÃO
$tem_animais_atrasados = 'S';
$dias_previsao_parto = 282;

if ($id_estacao!=0) {
    $sql = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
          INNER JOIN tbl_cobertura 
                  ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
          WHERE tbl_cobertura_lixeira=0 AND 
                tbl_cobertura_codigo_local = '$codigo_local' AND
                tbl_cobertura_codigo_estacao_monta='$id_estacao' AND
                tbl_cobertura_controle='C' AND 
                tbl_ite_cobertura_resultado_diagnostico='P' AND
                (tbl_ite_cobertura_nascido is null OR 
                 tbl_ite_cobertura_nascido='')");  

    $num_rows = mysqli_num_rows($sql);

    if ($num_rows!=0) {
        while ($reg_item = mysqli_fetch_object($sql)) {
            $cobertura_id=$reg_item->tbl_cobertura_id;
            $protocolo_id = $reg_item->tbl_cobertura_protocoloiatf;

            $sql_protocolo = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");
            $reg_protocolo_cobertura = mysqli_fetch_object($sql_protocolo);

            $sql_iatf =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
                WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                    tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id'
                ORDER BY tbl_ite_protocoloiatf_id ASC");

            while($reg_iatf = mysqli_fetch_object($sql_iatf)){
                $dias = substr($reg_iatf->tbl_ite_protocoloiatf_descricao, 3);

                $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

                $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
            }

            $firstDate  = new DateTime($data_servico);
            $secondDate = new DateTime($data_hoje);
            $intvl = $firstDate->diff($secondDate);
            $dias_calculados = $intvl->days;

            if ($dias_calculados<303) {
                $tem_animais_atrasados='N';
            }
        }
    }
}

$valor[5] = $id_estacao;
$valor[6] = $desc_estacao;
$valor[7] = $tem_animais_atrasados;

$str=$valor[0].'<|>'.$valor[1].'<|>'.$valor[2].'<|>'.$valor[3].'<|>'.$valor[4].'<|>'.$valor[5].'<|>'.$valor[6].'<|>'.$valor[7];
echo $str; 

?>