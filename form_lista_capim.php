<?php
    include "conecta_mysql.inc";

    $wlocal = "";
    if (isset($_POST['local'])) {
        $codigo_local = $_POST['local'];

        if(in_array("", $codigo_local)) {
            $wlocal='';
        }
        else {
            $wlocal = " AND tbl_pasto_codigo_local IN(";
            $wlocal.= implode(',', $codigo_local);
            $wlocal.= ")";
            }
    }
    else {
        $wlocal='';
    }

    @ session_start(); 

    $array_local = $_SESSION['local_pastos'];

?>

  <?php    
	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_capim">';
                          
            echo '<tbody>';
                $sql = "SELECT * FROM tbl_tipo_capim WHERE tbl_tipo_capim_lixeira=0"; 
                $rs = mysqli_query($conector, $sql);
                
                while($reg_capim = mysqli_fetch_object($rs)){
                    $id = $reg_capim->tbl_tipo_capim_id;
                    $descricao = $reg_capim->tbl_tipo_capim_descricao;
                    $lixeira = $reg_capim->tbl_tipo_capim_lixeira;

                    $incluido_em = new DateTime($reg_capim->tbl_tipo_capim_incluido_em);
                    $incluido_por = $reg_capim->tbl_tipo_capim_incluido_por; 
                    $alterado_em = new DateTime($reg_capim->tbl_tipo_capim_alterado_em);
                    $alterado_por = $reg_capim->tbl_tipo_capim_alterado_por; 
                    $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
                    $alterado_em_edi = $alterado_em->format('d/m/Y H:i:s');

                    $array_conta = array(
                        $id,
                        $descricao,
                        $incluido_em_edi,
                        $incluido_por,
                        $alterado_em_edi,
                        $alterado_por
                    );

                    $string_array = implode('|', $array_conta);

                    echo "<tr>";
                    echo "<td width='45%'>".$descricao."</td>";
                    echo "<td width='10%'>";    
                    echo "<div class='btn-group'>";
                    echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_capim(\"{$string_array}\");' ></i></a>";
                    echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2);' ></i></a>";
                    echo "</div>";
                    echo "</td>";
                }
                mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Descrição</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

    echo '<script src="js/tabela_capim.js" charset="utf-8" type="text/javascript" ></script>';

?>
<!-- <
    <script src="js/tabela_capim.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html> -->


                
                
