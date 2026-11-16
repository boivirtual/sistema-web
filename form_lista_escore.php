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
    echo '<table class="table table-striped table-advance table-hover" id="tabela_escore">';
                          
            echo '<tbody>';
                $sql = "SELECT * FROM tbl_escore_corporal WHERE tbl_escore_lixeira=0"; 
                $rs = mysqli_query($conector, $sql);
                
                while($reg_escore = mysqli_fetch_object($rs)){
                    $id = $reg_escore->tbl_escore_id;
                    $descricao = $reg_escore->tbl_escore_descricao;
                    $aparencia = $reg_escore->tbl_escore_aparencia;
                    $lixeira = $reg_escore->tbl_escore_lixeira;

                    $incluido_em = new DateTime($reg_escore->tbl_escore_incluido_em);
                    $incluido_por = $reg_escore->tbl_escore_incluido_por; 
                    $alterado_em = new DateTime($reg_escore->tbl_escore_alterado_em);
                    $alterado_por = $reg_escore->tbl_escore_alterado_por; 
                    $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
                    $alterado_em_edi = $alterado_em->format('d/m/Y H:i:s');

                    $array_conta = array(
                        $id,
                        $descricao,
                        $aparencia,
                        $incluido_em_edi,
                        $incluido_por,
                        $alterado_em_edi,
                        $alterado_por
                    );

                    $string_array = implode('|', $array_conta);

                    echo "<tr>";
                    echo "<td width='15%'>".$descricao."</td>";
                    echo "<td width='45%'>".$aparencia."</td>";
                    echo "<td width='10%'>";    
                    echo "<div class='btn-group'>";
                    echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_escore(\"{$string_array}\")' ></i></a>";
                    echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>";
                    echo "</div>";
                    echo "</td>";
                }
                mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Descrição</th>
                    <th> Aparência</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

    echo '<script src="js/tabela_escore.js" charset="utf-8" type="text/javascript" ></script>';

?>
<!--
    <script src="js/tabela_animais.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>-->


                
                
