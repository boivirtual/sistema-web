<?php
    include "conecta_mysql.inc";

    @ session_start();
    $cnpj_cliente = $_SESSION['id_cliente'];

?>

<!--
<!DOCTYPE html>
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
    echo '<table class="table table-striped table-advance table-hover" id="tabela_usuarios" style="font-size: 13px">';
                          
            echo '<tbody>';
          
                $sql = "SELECT * FROM usuario 
                                WHERE cnpj_cpf_empresa_usuario='$cnpj_cliente' AND 
                                      lixeira_usuario=0"; 

                $rs = mysqli_query($conector_acesso, $sql); 

                while ($reg_usuario = mysqli_fetch_object($rs)){
                    $codigo = $reg_usuario->id_usuario;
                    $nome = $reg_usuario->nome_usuario;
                    $codigo_grupo = $reg_usuario->grupo_usuario; 

                    $grupo_acesso = mysqli_query($conector, "select * from grupos_acessos
                                    where codigo_grupo_acesso = '$codigo_grupo'"); 
                    $reg_grupo = mysqli_fetch_object($grupo_acesso); 

                    $descricao_grupo = $reg_grupo->descricao_grupo_acesso; 
                    $email = $reg_usuario->email_usuario; 
                    $cpf = $reg_usuario->cpf_usuario; 
                    $cpf_editado = substr($cpf,0,3) . "." . substr($cpf,3,3) . "." . 
                                   substr($cpf,6,3) . "-" . substr($cpf,9,2);

                    $situacao = $reg_usuario->situacao_usuario; 
                    
                    $local = explode(', ', $reg_usuario->local_usuario);
                    $quantidade_local = count($local);
                    $locais = $reg_usuario->local_usuario;
                    $lixeira = $reg_usuario->lixeira_usuario; 

                    if ($situacao=="A"){
                        $desc_situacao = 'Ativo';
                    }
                    else {
                        $desc_situacao = 'Desligado';
                    }

                    $incluido_em=new DateTime($reg_usuario->incluido_em_usuario);
                    $incluido_por=$reg_usuario->incluido_por_usuario; 
                    $alterado_em=new DateTime($reg_usuario->alterado_em_usuario);
                    $alterado_por=$reg_usuario->alterado_por_usuario; 
                    $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
                    $alterado_em_edi = $alterado_em->format('d/m/Y H:i:s');

                    $array_usuario = array(
                        $codigo,
                        $nome,
                        $codigo_grupo,
                        $cpf_editado,
                        $email,
                        $situacao,
                        $locais,
                        $incluido_em_edi,
                        $incluido_por,
                        $alterado_em_edi,
                        $alterado_por

                    );   
                                    
                    $string_array = implode('|', $array_usuario);

                    echo "<tr>";

                    if ($situacao!="A") {
                            echo "<td style='color:#FF9393'>".$codigo."</td>";
                            echo "<td style='color:#FF9393'>".$nome."</td>";
                            echo "<td style='color:#FF9393'>".$descricao_grupo."</td>";
                            echo "<td style='color:#FF9393'>".$desc_situacao."</td>";
                            echo "<td style='color:#FF9393'>";    
                            echo "<div class='btn-group'>";
                            echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\")' ></i></a>"; 
                            echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                            echo "</div>";
                            echo "</td>";
                    }
                    else {
                            echo "<td width='10%'>".$codigo."</td>";
                            echo "<td width='15%'>".$nome."</td>";
                            echo "<td width='12%'>".$descricao_grupo."</td>";
                            echo "<td width='13%'>".$desc_situacao."</td>";
                            echo "<td width='10%'>";    
                            echo "<div class='btn-group'>";
                            echo "<a class='btn' href='#'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' onClick='editar_animal(\"{$string_array}\")' ></i></a>"; 
                            echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$string_array}\",2)' ></i></a>"; 
                            echo "</div>";
                            echo "</td>";
                    }
                    echo "</tr>";
                } 
                mysqli_close($conector);
            
            echo '</tbody>';

            echo '<thead>
                <tr>
                    <th> Código</th>
                    <th> Nome</th>
                    <th> Grupo</th>
                    <th> Situação</th>
                    <th><i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>';
       echo '</table>';

    echo '</section>';

    echo '<script src="js/tabela_usuarios.js" charset="utf-8" type="text/javascript" ></script>'

?>
               
                
