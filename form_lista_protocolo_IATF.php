<?php
    include "conecta_mysql.inc";

    @ session_start();
    $grupo_usuario = $_SESSION['grupo_usuario'];
    $empresa = $_SESSION["id_cliente"];
?>

  <?php    
	echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_protocolo">';
                          
            echo '<tbody>';
                $sql = "SELECT * FROM tbl_protocoloiatf WHERE tbl_protocoloiatf_lixeira = 0 ORDER BY tbl_protocoloiatf_id ASC"; 
                $rs = mysqli_query($conector, $sql);
                
                while($reg_protocolo = mysqli_fetch_object($rs)){
                    $protocolo_id = $reg_protocolo->tbl_protocoloiatf_id;
                    $protocolo_descricao = $reg_protocolo->tbl_protocoloiatf_descricao;
                    $protocolo_qtde = $reg_protocolo->tbl_protocoloiatf_qtde;
                    $protocolo_dias_diagnostico = $reg_protocolo->tbl_protocoloiatf_dias_diagnostico;
                    $protocolo_lixeira = $reg_protocolo->tbl_protocoloiatf_lixeira;

                    $protocolo_incluido_em = new DateTime($reg_protocolo->tbl_protocoloiatf_incluido_em);
                    $protocolo_incluido_por = $reg_protocolo->tbl_protocoloiatf_incluido_por; 
                    $protocolo_alterado_em = new DateTime($reg_protocolo->tbl_protocoloiatf_alterado_em);
                    $protocolo_alterado_por = $reg_protocolo->tbl_protocoloiatf_alterado_por; 
                    $protocolo_incluido_em_edi = $protocolo_incluido_em->format('d/m/Y H:i:s');
                    $protocolo_alterado_em_edi = $protocolo_alterado_em->format('d/m/Y H:i:s');

                    $array_conta = array(
                        $protocolo_id,
                        $protocolo_descricao,
                        $protocolo_qtde,
                        $protocolo_incluido_em_edi,
                        $protocolo_incluido_por,
                        $protocolo_alterado_em_edi,
                        $protocolo_alterado_por,
                        $protocolo_dias_diagnostico
                    );

                    $string_array = implode('|', $array_conta);

                    echo "<tr>";
                    echo "<td width='15%'>".$protocolo_descricao."</td>";
                    echo "<td width='45%'>".$protocolo_qtde."</td>";

                    if ($empresa=='97174041604' || $empresa=='71746307668') {
                        if ($grupo_usuario==1) {
                            echo "<td width='10%'>";    
                            echo "<div class='btn-group'>";
                            echo "<a class='btn' href='#'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar e Editar esse registro' onClick='editar_protocolo(\"{$string_array}\");' ></i></a>";
                            echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>";
                            echo "</div>";
                            echo "</td>";
                        }
                        else {
                            if ($protocolo_id!=999) {
                                echo "<td width='10%'>";    
                                echo "<div class='btn-group'>";
                                echo "<a class='btn' href='#'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar e Editar esse registro' onClick='editar_protocolo(\"{$string_array}\");' ></i></a>";
                                echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>";
                                echo "</div>";
                                echo "</td>";
                            }
                            else {
                                echo "<td width='10%'></td>";
                            }
                        }
                    }
                    else {
                        if ($protocolo_id!=999) {
                            echo "<td width='10%'>";    
                            echo "<div class='btn-group'>";
                            echo "<a class='btn' href='#'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar e Editar esse registro' onClick='editar_protocolo(\"{$string_array}\");' ></i></a>";
                            echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>";
                            echo "</div>";
                            echo "</td>";
                        }
                        else {
                            echo "<td width='10%'></td>";
                        }
                    }
                }
                mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Nome protocolo</th>
                    <th> Quantidade</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

    echo '<script src="js/protocolosIATF.js" charset="utf-8" type="text/javascript" ></script>';

    echo '<script>
    $(document).ready(function(){
      $("[data-toggle="tooltip"]").tooltip();   
    });
    </script>';

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


                
                
