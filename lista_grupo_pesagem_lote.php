<?php
    include "conecta_mysql.inc";

    $id_pesagem = $_POST['id_pesagem'];

?>

<!--<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>
</head>

<body> -->
  <?php    
    echo '<section class="panel">';

    $sql = mysqli_query($conector, "SELECT * from tbl_pesagem
        WHERE tbl_pesagem_id ='$id_pesagem'");
    
    $reg_pesagem = mysqli_fetch_object($sql);

    $local = $reg_pesagem->tbl_pesagem_codigo_local;
    $lote = $reg_pesagem->tbl_pesagem_lote;
    $filtros=$reg_pesagem->tbl_pesagem_filtros;
    $animais_pesados = $reg_pesagem->tbl_pesagem_qtd_animais_pesados;
    $data_emissao = new DateTime($reg_pesagem->tbl_pesagem_data);
    $data_emissao_edi =$data_emissao->format('d/m/Y');

    echo '<div class="row">
         <div class="col-md-12">
         <label style="font-weight: bold; font-size: 14px;">Filtros:&nbsp;</label> 
         <span style="font-size: 14px;">'.$filtros.'</span>
         </div>
         </div>';

    echo '<div class="row">
         <div class="col-md-12">
         <label style="font-weight: bold; font-size: 14px;">Descrição Pesagem:&nbsp;</label>
         <span style="font-size: 14px;">'.$lote.'</span>
         </div>
         </div>';

    echo '<div class="row">
         <div class="form-group col-md-6">
         <label style="font-weight: bold; font-size: 14px;">Animais Pesados:&nbsp;</label>
         <span style="font-size: 14px;">'.$animais_pesados.'</span>
         </div>
         <div class="form-group col-md-6">
         <label style="font-weight: bold; font-size: 14px;">Data da Pesagem:&nbsp;</label>
         <span style="font-size: 14px;">'.$data_emissao_edi.'</span>
         </div>
         </div>';

    echo '<table class="table table-striped table-advance table-hover" id="tabela_grupo_pesagem">';
                          
    echo '<tbody>';
        
        $grupo_anterior = 999;
        $categoria_anterior = 0;
        $desc_categoria_anterior = '';
        $sexo_anterior = '';
        $total_animais = 0;

        $sql = mysqli_query($conector, "SELECT * from tbl_item_pesagem
            INNER JOIN tbl_pesagem 
                    ON tbl_pesagem_id = tbl_ite_pesagem_numero_id
            WHERE tbl_ite_pesagem_numero_id ='$id_pesagem'
            ORDER BY tbl_ite_pesagem_grupo_pasto_destino ASC,   
                     tbl_ite_pesagem_categoria ASC,
                     tbl_ite_pesagem_sexo ASC
                      ");

        while ($reg_pesagem = mysqli_fetch_object($sql)){
            $item = $reg_pesagem->tbl_ite_pesagem_numero_item;
            $grupo = $reg_pesagem->tbl_ite_pesagem_grupo_pasto_destino;
            $qtd = $reg_pesagem->tbl_ite_pesagem_qtd_animais;
            $pasto_destino = $reg_pesagem->tbl_ite_pesagem_pasto_destino;
            $sexo = $reg_pesagem->tbl_ite_pesagem_sexo;
            $categoria = $reg_pesagem->tbl_ite_pesagem_categoria;

            $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade where tab_codigo_categoria_idade='$categoria'");
            $num_rows = mysqli_num_rows($tbl_categoria);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tbl_categoria);
                if ($reg->tab_categoria_idade_ate==999999999) {
                    $desc_categoria = '> 36 meses'; 
                }
                else {
                    $desc_categoria = $reg->tab_categoria_idade_de . ' a ' . 
                                      $reg->tab_categoria_idade_ate . ' meses';
                }
            }
            else {
                $desc_categoria = '';
            }

            if ($grupo!=$grupo_anterior) {
                if ($grupo_anterior==999) {
                    $grupo_anterior=$grupo;
                    $pasto_anterior=$pasto_destino;
                    $total_animais = 0;
                    $categoria_anterior = 0;
                    $sexo_anterior = '';
                }
                else {
                    echo "<tr>";
                    echo "<td class='grupo' width='12%' >".$grupo_anterior."</td>";
                    echo "<td width='20%'>".$desc_categoria_anterior."</td>";
                    echo "<td width='8%'>".$sexo_anterior."</td>";
                    echo "<td width='12%'>".$total_animais."</td>";
                    echo "<td width='48%'>
                         <select class='form-control pasto' name='pasto' onchange='gravar_pasto_grupo_pesagem(this.value, \"{$grupo_anterior}\", \"{$id_pesagem}\")'>
                         <option value='000'>...</option>";

                    $tbl_pasto = mysqli_query($conector, "select * from tbl_pasto 
                    where tbl_pasto_codigo_local='$local' and tbl_pasto_lixeira=0"); 

                    while($reg_pasto = mysqli_fetch_object($tbl_pasto)) { 
                        if ($pasto_anterior==$reg_pasto->tbl_pasto_id) {
                            echo "<option value=".$reg_pasto->tbl_pasto_id ." selected>".$reg_pasto->tbl_pasto_descricao."</option>";
                        }
                        else {
                            echo "<option value=".$reg_pasto->tbl_pasto_id .">".$reg_pasto->tbl_pasto_descricao."</option>";
                        }
                    }

                    echo "</select>";
                    echo "</td>";
                    echo "</tr>";
                    $grupo_anterior=$grupo;
                    $pasto_anterior=$pasto_destino;
                    $total_animais = 0;
                    $categoria_anterior = 0;
                    $sexo_anterior = '';
                }
            }

            if ($categoria!=$categoria_anterior) {
                if ($categoria_anterior==0) {
                    $total_animais = 0;
                    $categoria_anterior = $categoria;
                    $desc_categoria_anterior = $desc_categoria;
                    $sexo_anterior = '';
                }
                else {
                    echo "<tr>";
                    echo "<td class='grupo' width='12%' >".$grupo_anterior."</td>";
                    echo "<td width='20%'>".$desc_categoria_anterior."</td>";
                    echo "<td width='8%'>".$sexo_anterior."</td>";
                    echo "<td width='12%'>".$total_animais."</td>";
                    echo "<td width='48%'>
                         <select class='form-control pasto' name='pasto' onchange='gravar_pasto_grupo_pesagem(this.value, \"{$grupo_anterior}\", \"{$id_pesagem}\")'>
                         <option value='000'>...</option>";

                    $tbl_pasto = mysqli_query($conector, "select * from tbl_pasto 
                    where tbl_pasto_codigo_local='$local' and tbl_pasto_lixeira=0"); 

                    while($reg_pasto = mysqli_fetch_object($tbl_pasto)) { 
                        if ($pasto_anterior==$reg_pasto->tbl_pasto_id) {
                            echo "<option value=".$reg_pasto->tbl_pasto_id ." selected>".$reg_pasto->tbl_pasto_descricao."</option>";
                        }
                        else {
                            echo "<option value=".$reg_pasto->tbl_pasto_id .">".$reg_pasto->tbl_pasto_descricao."</option>";
                        }
                    }

                    echo "</select>";
                    echo "</td>";
                    echo "</tr>";
                    $total_animais = 0;
                    $categoria_anterior = $categoria;
                    $desc_categoria_anterior = $desc_categoria;
                    $sexo_anterior = '';

                }
            }

            if ($sexo!=$sexo_anterior) {
                if ($sexo_anterior=='') {
                    $total_animais = 0;
                    $sexo_anterior = $sexo;
                }
                else {
                    echo "<tr>";
                    echo "<td class='grupo' width='12%' >".$grupo_anterior."</td>";
                    echo "<td width='20%'>".$desc_categoria_anterior."</td>";
                    echo "<td width='8%'>".$sexo_anterior."</td>";
                    echo "<td width='12%'>".$total_animais."</td>";
                    echo "<td width='48%'>
                         <select class='form-control pasto' name='pasto' onchange='gravar_pasto_grupo_pesagem(this.value, \"{$grupo_anterior}\", \"{$id_pesagem}\")'>
                         <option value='000'>...</option>";

                    $tbl_pasto = mysqli_query($conector, "select * from tbl_pasto 
                    where tbl_pasto_codigo_local='$local' and tbl_pasto_lixeira=0"); 

                    while($reg_pasto = mysqli_fetch_object($tbl_pasto)) { 
                        if ($pasto_anterior==$reg_pasto->tbl_pasto_id) {
                            echo "<option value=".$reg_pasto->tbl_pasto_id ." selected>".$reg_pasto->tbl_pasto_descricao."</option>";
                        }
                        else {
                            echo "<option value=".$reg_pasto->tbl_pasto_id .">".$reg_pasto->tbl_pasto_descricao."</option>";
                        }
                    }

                    echo "</select>";
                    echo "</td>";
                    echo "</tr>";
                    $total_animais = 0;
                    $sexo_anterior = $sexo;
                }
            }

            $total_animais+=$qtd;           
        } 

        echo "<tr>";
        echo "<td class='grupo' width='12%'>".$grupo_anterior."</td>";
        echo "<td width='20%'>".$desc_categoria_anterior."</td>";
        echo "<td width='8%'>".$sexo_anterior."</td>";
        echo "<td width='12%'>".$total_animais."</td>";
        echo "<td width='48%'>
             <select class='form-control pasto' name='pasto' onchange='gravar_pasto_grupo_pesagem(this.value, \"{$grupo_anterior}\", \"{$id_pesagem}\")'>
                 <option value='000'>...</option>";

            $tbl_pasto = mysqli_query($conector, "select * from tbl_pasto 
                where tbl_pasto_codigo_local='$local' and tbl_pasto_lixeira=0"); 

            while($reg_pasto = mysqli_fetch_object($tbl_pasto)) { 
                if ($pasto_anterior==$reg_pasto->tbl_pasto_id) {
                    echo "<option value=".$reg_pasto->tbl_pasto_id ." selected>".$reg_pasto->tbl_pasto_descricao."</option>";
                }
                else {
                    echo "<option value=".$reg_pasto->tbl_pasto_id .">".$reg_pasto->tbl_pasto_descricao."</option>";
                }
            }

        echo "</select>";
        echo "</td>";
        echo "</tr>";
        
    mysqli_close($conector);
        
    echo '</tbody>';

    echo '<thead>
        <tr>
            <th> Grupo</th>
            <th> Categoria</th>
            <th> Sexo</th>
            <th> Quantidade</th>
            <th> Pasto Destino</th>
            <th> </th>
        </tr>
        </thead>';

    echo '</table>';
    echo '</section>';

?>
<!--   <script src="js/pesagem.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html> -->



                
                
