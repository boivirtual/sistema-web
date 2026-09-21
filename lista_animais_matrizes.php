<?php
    include "conecta_mysql.inc";
    $data_hoje = date("Y-m-d");
    $local = ltrim($_POST['local']);
    $vacas_paridas = $_POST['vacas_paridas'];
    $data_paridas = $_POST['data_paridas'];
    $vacas_solteiras = $_POST['vacas_solteiras'];
    $novilhas = $_POST['novilhas'];
    $idade_de = $_POST['idade_de'];
    $idade_ate = $_POST['idade_ate'];
    $peso_acima = $_POST['peso_acima'];

    if ($idade_ate=='') {
        $idade_ate=9999;
    }

    if ($peso_acima==0) {
        $peso_acima=1;
    }

    $animais_listados = 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

  <style>
      .table_overflow table thead th{
        position: sticky;
        top: 0;
        z-index: 1;
      }
      .table_overflow th{
        background-color: #eee;
      }
      
      table.dataTable.no-footer{
          border: none;
      }

      #tabela_matrizes_wrapper{
          width: 100% !important;
          /*overflow-x: scroll !important;*/
          overflow-y: scroll !important;
          max-height: 300px;
      }

      #tabela_matrizes_filter{
          float: right;
      }
  </style>

</head>

<body> 

<?php
	echo '<section class="panel table-responsive">';
    echo '<table class="table table-striped table-advance table-hover" style="font-size: 12px; width: 100%;" id="tabela_matrizes">';
                          
    echo '<tbody>';
          
    $sql = mysqli_query($conector, "select * from tbl_animais 
                    where tbl_animal_sexo='F' and 
                          tbl_animal_ativo='S' and
                          tbl_animal_lixeira=0 and 
                          (tbl_animal_descarte_reproducao='' or 
                           tbl_animal_descarte_reproducao IS NULL) and
                          tbl_animal_codigo_fazenda='$local'
                    order by tbl_animal_codigo_numerico ASC"); 
    $num_row = mysqli_num_rows($sql);

    if ($num_row!=0) {
        while ($reg_animal = mysqli_fetch_object($sql)){
            $codigo_id= $reg_animal->tbl_animal_codigo_id ;
            $codigo_alfa= $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico= $reg_animal->tbl_animal_codigo_numerico;
            $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
            $ultimo_peso= $reg_animal->tbl_animal_ultimo_peso;

            if ($codigo_alfa=='') {
                $codigo_ed = $codigo_numerico;
            }
            else {
                $codigo_ed = $codigo_alfa.'-'.$codigo_numerico;
            }

            $data_inicial = $data_nascimento;
            $data_final = date("Y-m-d");
            $diferenca = strtotime($data_final) - 
                         strtotime($data_inicial);
            $idade = floor($diferenca / (60 * 60 * 24 * 30));
            $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);

            $numero_abortos = 0;
            $dias_ultimo_parto = 0;
            $coberturas_estacao = 0;

            if ($vacas_paridas=='VP' || $novilhas=='NO') {
                $tbl_filhos = mysqli_query($conector,"select * from tbl_animais 
                                where tbl_animal_codigo_mae='$codigo_id'
                                order by tbl_animal_codigo_numerico ASC"); 
                $numero_partos = mysqli_num_rows($tbl_filhos);

                if ($numero_partos!=0) {
                    while ($reg_filhos = mysqli_fetch_object($tbl_filhos)){
                        $ultimo_parto=new DateTime($reg_filhos->tbl_animal_data_nascimento);
                        $ultimo_parto_edi = $ultimo_parto->format('d/m/Y');

                        $data_inicial = $reg_filhos->tbl_animal_data_nascimento;
                        $data_final = date("Y-m-d");
                        $diferenca = strtotime($data_final) - 
                                     strtotime($data_inicial);
                        $dias_ultimo_parto = floor($diferenca / (60 * 60 * 24));

                        $ultimo_parto=$reg_filhos->tbl_animal_data_nascimento;
                        $codigo_pai=$reg_filhos->tbl_animal_codigo_pai;
                    }
                }

                $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$codigo_pai'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_semem_nome;
                }
                else {
                    $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$codigo_pai'");
                    $num_rows_pai = mysqli_num_rows($tab_pai);

                    if ($num_rows_pai!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                    }
                    else {
                        $descricao_pai = '';
                    }
                }
            }
            else {
                $descricao_pai = '';
                $numero_partos='';
                $numero_abortos='';
                $ultimo_parto_edi='';
                $ultimo_parto_edi='';
            }

            if (($vacas_paridas=='VP' && $ultimo_parto<=$data_paridas && $numero_partos!=0) ||
                ($novilhas=='NO' && $idade>=$idade_de && $idade<=$idade_ate && $ultimo_peso>=$peso_acima && $peso_acima!=0 && $numero_partos==0) ) {
                echo "<tr>";
                echo "<td width='10%'>
                    <input type='checkbox' name='id_animal' class='checkbox1' data-toggle='tooltip' data-placement='top' 
                            title='Selecionar esse animal' 
                            onClick='somar_selecionados()' value='".$codigo_id."'>
                        </td>";
                echo "<td width='11%'>".$codigo_ed."</td>";
                echo "<td width='8%' align='center'>".$numero_partos."</td>";
                echo "<td width='10%' align='center'>".$numero_abortos."</td>";
                echo "<td width='8%'>".$idade."</td>";
                echo "<td width='12%'>".$ultimo_parto_edi."</td>";
                echo "<td width='15%' align='center'>".$dias_ultimo_parto."</td>";
                echo "<td width='15%'>".$descricao_pai."</td>";
                echo "<td width='10%' align='center'>".$coberturas_estacao."</td>";
                echo "</tr>";
                $animais_listados++;
            }
        } 

    }


    mysqli_close($conector);

    echo '</tbody>';

/*        <tr>
            <th colspan="2" style="border: solid 1px; border-color: #ccc;">Animais Listados</th> 
            <td style="border: solid 1px; border-color: #ccc;">'.$animais_listados.'</td>
            <th colspan="2" style="border: solid 1px; border-color: #ccc;">Animais Selecionados</th>
            <td id="total_selecionados" style="border: solid 1px; border-color: #ccc;"></td>
            <th colspan="3" style="border: solid 1px; border-color: #ccc;"></th>

        </tr>
*/
    echo '<thead>
        <tr>
            <div class="row" id="total_contas">
                <div class="form-group col-md-2">
                    <label class="control-label">Animais Listados</label>
                    <input class="form-control" type="text" readonly="" value="'.$animais_listados.'">
                </div>

                <div class="form-group col-md-2">
                    <label class="control-label">Animais Selecionados</label>
                    <input class="form-control" type="text" id="total_selecionados" readonly="">
                </div>

                <div class="form-group col-md-2">
                    <label class="control-label">&nbsp;</label>
                    <button type="button" class="form-control btn btn-success confirma_gravar" onClick="indicar_protocolo()">Confirmar Cobertura</button>
                </div>

                <div class="form-group col-md-2">
                    <label class="control-label">&nbsp;</label>
                    <button type="button" class="form-control btn btn-danger confirma_descarte" onClick="gravar_descarte()">Confirmar Descarte</button>
                </div>

            </div>
        </tr>

        <tr>
            <th>
                <input type="checkbox" class="checkbox1" id="seleciona_todos" data-toggle="tooltip" data-placement="right" title="Selecionar Todos os animais"> 
            </th> 

            <th> Nº da Fêmea</th>
            <th> Nº de Partos</th>
            <th> Nº de Abortos</th>
            <th> Idade em meses</th>
            <th> Último Parto</th>
            <th> Dias do último parto</th>
            <th> Pai do último parto</th>
            <th> Coberturas nessa estação</th>
        </tr>
        </thead>';
   echo '</table>';

   echo '</section>';
?>

    <script src="js/matrizes.js" charset="utf-8" type="text/javascript" ></script>

    <script>
        $(document).ready(function() {
            var table = $('#tabela_matrizes').DataTable( {
                sDom: 'lfr<"table_overflow"t>ip',
                /* scrollY: "200px", */
                paging:   false,
                search:   true,
                info: false,
                ordering: false,
                language: {
                  sSearch: "Buscar na lista:",
                  zeroRecords: "Nada encontrado",
                  info: "Animais listados: _END_ ",
                  infoEmpty: "Nenhum registro disponível",
                  infoFiltered: "(filtrado de _MAX_ registros no total)",
                }
            });
        });


    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
    </script>

</body>
</html> 

                
                
