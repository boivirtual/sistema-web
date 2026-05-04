<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    @ session_start(); 

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];
    $controle_estoque = $_SESSION['controle_estoque'];

    $local = $_POST['local'];

/*    $tbl_pessoa = mysqli_query($conector, "select * from tbl_pessoa 
        where tbl_pessoa_id ='$local' and tbl_pessoa_lixeira=0"); 
    $reg_local = mysqli_fetch_object($tbl_pessoa);
    $desc_local = $reg_local->tbl_pessoa_nome;
*/
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->

</head>

<body>
	<section class="panel"  style="overflow-x:auto">
        <table id="tabela_lista_estoque" class="table table-bordered table-advance table-hover table-reponsive" 
        style="width:100%;">

        <tbody style="margin:0; padding: 0" >
            <?php
                $total_animais = 0;
                $total_cat_M_F = 0;

                for ($i = 1; $i <=5; $i++) {
                    $j = str_pad($i, 3, "0", STR_PAD_LEFT);
                    $total_cat_macho[$j]=0;
                    $total_cat_femea[$j]=0;
                }
            
                $tbl_pasto= mysqli_query($conector, "SELECT * FROM tbl_pasto
                        WHERE tbl_pasto_codigo_local='$local' AND 
                              tbl_pasto_modulo=999");

                $num_rows = mysqli_num_rows($tbl_pasto);

                if ($num_rows!=0) {
                    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
                        $descricao = $reg_pasto->tbl_pasto_descricao;
                        $codigo_pasto = $reg_pasto->tbl_pasto_id;

                        $tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                                WHERE tbl_animal_pasto_id='$codigo_pasto' AND 
                                      tbl_animal_pasto_situacao='A'
                                ORDER BY tbl_animal_pasto_nascimento DESC");

                        $num_rows_animal = mysqli_num_rows($tbl_animal_pasto);
                        $total_animais_pasto = 0;

                        for ($i = 1; $i <=5; $i++) {
                            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
                            $total_macho[$j]=0;
                            $total_femea[$j]=0;
                        }

                        if ($num_rows_animal!=0) {
                            while ($reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto)) {
                                $sexo = $reg_animal_pasto->tbl_animal_pasto_sexo;
                                $codigo_categoria = $reg_animal_pasto->tbl_animal_pasto_categoria;

                                if ($controle_estoque=='I'){
                                    $total_animais++;
                                    $total_animais_pasto++;

                                    if ($sexo=='M') {
                                        $total_macho[$codigo_categoria]++;
                                        $total_cat_macho[$codigo_categoria]++;
                                    }
                                    else {
                                        $total_femea[$codigo_categoria]++;
                                        $total_cat_femea[$codigo_categoria]++;
                                    }
                                }
                                else {
                                    $data_nascimento = $reg_animal_pasto->tbl_animal_pasto_nascimento;

                                    $data_nascimento = $data_nascimento;  
                                    $data_acompanhamento_calculo = date("Y-m-d");
                                    $date = new DateTime($data_nascimento); // Data de Nascimento
                                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                                   /* $data_inicial = $data_nascimento;
                                    $data_final = date("Y-m-d");
                                    $diferenca = strtotime($data_final) - 
                                                 strtotime($data_inicial);
                                    $idade = floor($diferenca / (60 * 60 * 24 * 30));
                                    $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);*/

                                    $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                                        WHERE tab_registro_lixeira_categoria_idade='0'");
                                    $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                                    while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                                        $idade_de = $reg_categoria->tab_categoria_idade_de;
                                        $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                                        if ($idade >= $idade_de && $idade <= $idade_ate) {
                                            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                                            $total_animais++;
                                            $total_animais_pasto++;

                                            if ($sexo=='M') {
                                                $total_macho[$codigo_categoria]++;
                                                $total_cat_macho[$codigo_categoria]++;
                                            }
                                            else {
                                                $total_femea[$codigo_categoria]++;
                                                $total_cat_femea[$codigo_categoria]++;
                                            }
                                        }
                                    }
                                }
                            }

                            if ($total_animais_pasto!=0) {
                                echo '<tr>';
                                echo '<td width="20%" class="text-right">'.$descricao.'</td>';
                                for ($i = 1; $i <=5; $i++) {
                                    $i = str_pad($i, 3, "0", STR_PAD_LEFT);

                                    if ($i==1) {
                                        $total_M_F = $total_macho[$i] + $total_femea[$i];

                                        if ($total_M_F==0) {
                                            $total_M_F='';
                                        }

                                        echo '<td width="8%" class="text-right">'.$total_M_F.'</td>';
                                    }
                                    else if ($i==2) {
                                        if ($total_macho[$i]==0) {
                                            $total_macho[$i]='';
                                        }

                                        if ($total_femea[$i]==0) {
                                            $total_femea[$i]='';
                                        }
                                        echo '<td width="8%" class="text-right">'.$total_macho[$i].'</td>';
                                        echo '<td width="8%" class="text-right">'.$total_femea[$i].'</td>';
                                    }
                                    else if ($i==3) {
                                        if ($total_macho[$i]==0) {
                                            $total_macho[$i]='';
                                        }

                                        if ($total_femea[$i]==0) {
                                            $total_femea[$i]='';
                                        }
                                        echo '<td width="8%" class="text-right">'.$total_macho[$i].'</td>';
                                        echo '<td width="8%" class="text-right">'.$total_femea[$i].'</td>';
                                    }
                                    else if ($i==4) {
                                        if ($total_macho[$i]==0) {
                                            $total_macho[$i]='';
                                        }

                                        if ($total_femea[$i]==0) {
                                            $total_femea[$i]='';
                                        }
                                        echo '<td width="8%" class="text-right">'.$total_macho[$i].'</td>';
                                        echo '<td width="8%" class="text-right">'.$total_femea[$i].'</td>';
                                    }
                                    else if ($i==5) {
                                        if ($total_macho[$i]==0) {
                                            $total_macho[$i]='';
                                        }

                                        if ($total_femea[$i]==0) {
                                            $total_femea[$i]='';
                                        }
                                        echo '<td width="8%" class="text-right">'.$total_macho[$i].'</td>';
                                        echo '<td width="8%" class="text-right">'.$total_femea[$i].'</td>';
                                    }
                                }

                                echo '<td width="8%" class="text-right">'.$total_animais_pasto.'</td>';
                                echo '</tr>';
                            }
                        }
                    }
                }

                $tbl_pasto= mysqli_query($conector, "SELECT * FROM tbl_pasto
                        WHERE tbl_pasto_codigo_local='$local' AND 
                              tbl_pasto_modulo!=999");

                $num_rows = mysqli_num_rows($tbl_pasto);

                if ($num_rows!=0) {
                    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
                        $descricao = $reg_pasto->tbl_pasto_descricao;
                        $codigo_pasto = $reg_pasto->tbl_pasto_id;

                        $tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                                WHERE tbl_animal_pasto_id='$codigo_pasto' AND 
                                      tbl_animal_pasto_situacao='A'
                                ORDER BY tbl_animal_pasto_nascimento DESC");

                        $num_rows_animal = mysqli_num_rows($tbl_animal_pasto);
                        $total_animais_pasto = 0;

                        for ($i = 1; $i <=5; $i++) {
                            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
                            $total_macho[$j]=0;
                            $total_femea[$j]=0;
                        }

                        if ($num_rows_animal!=0) {
                            while ($reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto)) {
                                $sexo = $reg_animal_pasto->tbl_animal_pasto_sexo;
                                $codigo_categoria = $reg_animal_pasto->tbl_animal_pasto_categoria;

                                if ($controle_estoque=='I'){
                                    $total_animais++;
                                    $total_animais_pasto++;

                                    if ($sexo=='M') {
                                        $total_macho[$codigo_categoria]++;
                                        $total_cat_macho[$codigo_categoria]++;
                                    }
                                    else {
                                        $total_femea[$codigo_categoria]++;
                                        $total_cat_femea[$codigo_categoria]++;
                                    }
                                }
                                else {
                                    $data_nascimento = $reg_animal_pasto->tbl_animal_pasto_nascimento;

                                    $data_nascimento = $data_nascimento;  
                                    $data_acompanhamento_calculo = date("Y-m-d");
                                    $date = new DateTime($data_nascimento); // Data de Nascimento
                                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                                    /*$data_inicial = $data_nascimento;
                                    $data_final = date("Y-m-d");
                                    $diferenca = strtotime($data_final) - 
                                                 strtotime($data_inicial);
                                    $idade = floor($diferenca / (60 * 60 * 24 * 30));
                                    $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);*/

                                    $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                                        WHERE tab_registro_lixeira_categoria_idade='0'");
                                    $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                                    while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                                        $idade_de = $reg_categoria->tab_categoria_idade_de;
                                        $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                                        if ($idade >= $idade_de && $idade <= $idade_ate) {
                                            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                                            $total_animais++;
                                            $total_animais_pasto++;

                                            if ($sexo=='M') {
                                                $total_macho[$codigo_categoria]++;
                                                $total_cat_macho[$codigo_categoria]++;
                                            }
                                            else {
                                                $total_femea[$codigo_categoria]++;
                                                $total_cat_femea[$codigo_categoria]++;
                                            }
                                        }
                                    }
                                }
                            }

                            if ($total_animais_pasto!=0) {
                                echo '<tr>';
                                echo '<td width="20%" class="text-right">'.$descricao.'</td>';
                                for ($i = 1; $i <=5; $i++) {
                                    $i = str_pad($i, 3, "0", STR_PAD_LEFT);

                                    if ($i==1) {
                                        $total_M_F = $total_macho[$i] + $total_femea[$i];

                                        if ($total_M_F==0) {
                                            $total_M_F='';
                                        }

                                        echo '<td width="8%" class="text-right">'.$total_M_F.'</td>';
                                    }
                                    else if ($i==2) {
                                        if ($total_macho[$i]==0) {
                                            $total_macho[$i]='';
                                        }

                                        if ($total_femea[$i]==0) {
                                            $total_femea[$i]='';
                                        }
                                        echo '<td width="8%" class="text-right">'.$total_macho[$i].'</td>';
                                        echo '<td width="8%" class="text-right">'.$total_femea[$i].'</td>';
                                    }
                                    else if ($i==3) {
                                        if ($total_macho[$i]==0) {
                                            $total_macho[$i]='';
                                        }

                                        if ($total_femea[$i]==0) {
                                            $total_femea[$i]='';
                                        }
                                        echo '<td width="8%" class="text-right">'.$total_macho[$i].'</td>';
                                        echo '<td width="8%" class="text-right">'.$total_femea[$i].'</td>';
                                    }
                                    else if ($i==4) {
                                        if ($total_macho[$i]==0) {
                                            $total_macho[$i]='';
                                        }

                                        if ($total_femea[$i]==0) {
                                            $total_femea[$i]='';
                                        }
                                        echo '<td width="8%" class="text-right">'.$total_macho[$i].'</td>';
                                        echo '<td width="8%" class="text-right">'.$total_femea[$i].'</td>';
                                    }
                                    else if ($i==5) {
                                        if ($total_macho[$i]==0) {
                                            $total_macho[$i]='';
                                        }

                                        if ($total_femea[$i]==0) {
                                            $total_femea[$i]='';
                                        }
                                        echo '<td width="8%" class="text-right">'.$total_macho[$i].'</td>';
                                        echo '<td width="8%" class="text-right">'.$total_femea[$i].'</td>';
                                    }
                                }

                                echo '<td width="8%" class="text-right">'.$total_animais_pasto.'</td>';
                                echo '</tr>';
                            }
                        }
                    }
                }

                for ($i = 1; $i <=5; $i++) {
                    $i = str_pad($i, 3, "0", STR_PAD_LEFT);

                    if ($i==1) {
                        $total_cat_M_F = intval($total_cat_macho[$i]) + intval($total_cat_femea[$i]);
                    }
                }

                if ($total_cat_M_F==0) {
                    $total_cat_M_F='';
                }         

                if ($total_cat_macho['002']==0) {
                    $total_cat_macho['002']='';
                }         

                if ($total_cat_femea['002']==0) {
                    $total_cat_femea['002']='';
                }       

                if ($total_cat_macho['003']==0) {
                    $total_cat_macho['003']='';
                }         

                if ($total_cat_femea['003']==0) {
                    $total_cat_femea['003']='';
                }       

                if ($total_cat_macho['004']==0) {
                    $total_cat_macho['004']='';
                }         

                if ($total_cat_femea['004']==0) {
                    $total_cat_femea['004']='';
                }       

                if ($total_cat_macho['005']==0) {
                    $total_cat_macho['005']='';
                }         

                if ($total_cat_femea['005']==0) {
                    $total_cat_femea['005']='';
                }       

            ?>
        </tbody>
        <thead>
            <?php
                echo '<div class="row col-md-12 filtro_escondido" id="total_contas">';

                echo '<div class="form-group col-md-9">';
                echo '<p id="descricao_filtro"
                    class="text-muted" style="font-size: 12px; color: #829c9c"></p>';
                echo '</div>';

                echo '<div class="form-group col-md-1">';
                echo '<button type="button" class="form-control btn btn-success pull-right"
                    onClick="lista_mapa_gado_excel()">Excel</button>';
                echo '</div>';

                echo '<div class="form-group col-md-1">';
                echo '<button type="button" class="form-control btn btn-info pull-right exibir"
                    data-toggle="tooltip" data-placement="bottom" title="Maximizar tela filtros" onClick="exibir_filtro()"><i class="fa fa-sort-up"></i>&nbsp;<i class="fa fa-filter"></i></button>';

                echo '<button type="button" class="form-control btn btn-info pull-right esconder" hidden=""
                    data-toggle="tooltip" data-placement="bottom" title="Minimizar tela filtros" onClick="esconder_filtro()"><i class="fa fa-sort-down"></i>&nbsp;<i class="fa fa-filter"></i></button>';
                echo '</div>';

                echo '<div class="form-group col-md-1 voltar">';
                echo '<button type="button" class="form-control btn btn-info pull-right" onclick="onclick=voltar_relatorios()">Voltar</button>';
                echo '</div>';

                echo '</div>';

                echo '<tr>';
                echo '<th class="text-center">Total de Animais</th>';
                echo '<th class="text-center">'.$total_animais.'</th>';
                echo '<th colspan="10"></th>';
                echo'</tr>';

                echo '<tr>';
                echo '<th class="text-center" rowspan="2">Pasto</th>';
                echo '<th class="text-center">00 a 07 meses</th>';
                echo '<th class="text-center">08 a 12 meses</th>';
                echo '<th class="text-center">08 a 12 meses</th>';
                echo '<th class="text-center">13 a 24 meses</th>';
                echo '<th class="text-center">13 a 24 meses</th>';
                echo '<th class="text-center">25 a 36 meses</th>';
                echo '<th class="text-center">25 a 36 meses</th>';
                echo '<th class="text-center">> 36 meses</th>';
                echo '<th class="text-center">> 36 meses</th>';
                echo '<th class="text-center" rowspan="2">Total Animais</th>';
                echo'</tr>';

                echo '<tr>';
                echo '<th class="text-center">Macho/Fêmea</th>';
                echo '<th class="text-center">Macho</th>';
                echo '<th class="text-center">Fêmea</th>';
                echo '<th class="text-center">Macho</th>';
                echo '<th class="text-center">Fêmea</th>';
                echo '<th class="text-center">Macho</th>';
                echo '<th class="text-center">Fêmea</th>';
                echo '<th class="text-center">Macho</th>';
                echo '<th class="text-center">Fêmea</th>';
                echo '</tr>';
            ?>
        </thead>

        <tfoot>
            <tr>
            <th class="text-right">Totais Animais</th>
            <th class="text-right"><?php echo $total_cat_M_F?></th>
            <th class="text-right"><?php echo $total_cat_macho['002']?></th>
            <th class="text-right"><?php echo $total_cat_femea['002']?></th>
            <th class="text-right"><?php echo $total_cat_macho['003']?></th>
            <th class="text-right"><?php echo $total_cat_femea['003']?></th>
            <th class="text-right"><?php echo $total_cat_macho['004']?></th>
            <th class="text-right"><?php echo $total_cat_femea['004']?></th>
            <th class="text-right"><?php echo $total_cat_macho['005']?></th>
            <th class="text-right"><?php echo $total_cat_femea['005']?></th>
            <th class="text-right"><?php echo $total_animais?></th>
            </tr>
            
        </tfoot>
        </table>

    </section>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>


                
                
