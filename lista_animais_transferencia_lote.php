<?php
    include "conecta_mysql.inc";

    for ($i=1; $i<=21; $i++){
        $valor[$i]=0;
    }

    $matriz_com_itens = 0;
    $numero_do_item=0;
    $animais_listados=0;
    $matriz_itens= array();

    $local = $_POST['local'];

    $wsexo = "";
    if (isset($_POST['sexo'])) {
        $sexo = $_POST['sexo'];

        if(in_array("Todos", $sexo)) {
            $wsexo='';
        }
        else {
            $wsexo = " AND tbl_animal_pasto_sexo IN(";
            $wsexo .= "'" . implode("','", $sexo) . "'";
            $wsexo.= ")";
            }
    }
    else {
        $wsexo='';
    }

    $wcategoria = "";
    if (isset($_POST['categoria'])) {
        $categoria_filtro = $_POST['categoria'];

        if(in_array("", $categoria_filtro)) {
            $wcategoria='';
        }
        else {
            $wcategoria = " AND tbl_animal_pasto_categoria IN(";
            $wcategoria.= implode(',', $categoria_filtro);
            $wcategoria.= ")";
        }
    }
    else {
        $wcategoria='';
    }

    // pega o codigo do pasto curral de saida do local

    $sql = mysqli_query($conector, "SELECT * FROM tbl_pasto
        WHERE tbl_pasto_codigo_local='$local' AND 
              tbl_pasto_modulo=999 AND 
              tbl_pasto_tipo_curral='S'"); 

    $num_rows = mysqli_num_rows($sql);

    if ($num_rows!=0){
        $reg_pasto = mysqli_fetch_object($sql);
        $pasto = $reg_pasto->tbl_pasto_id;
    }
    else {
        $pasto = 0;
    }

    // popular array com animais do pasto de saida do local

    for ($i = 1; $i <=5; $i++) {
        $j = str_pad($i, 3, "0", STR_PAD_LEFT);
        $total_cat_macho[$j]=0;
        $total_cat_femea[$j]=0;
    }

    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_situacao='A' AND
              tbl_animal_pasto_local='$local' AND 
              tbl_animal_pasto_id='$pasto'"); 

    $num_rows = mysqli_num_rows($sql);

    if ($num_rows!=0){
        while ($reg_animal = mysqli_fetch_object($sql)){
            $sexo = $reg_animal->tbl_animal_pasto_sexo;
            $codigo_categoria = $reg_animal->tbl_animal_pasto_categoria;

            if ($sexo=='M') {
                $total_cat_macho[$codigo_categoria]++;
            }
            else {
                $total_cat_femea[$codigo_categoria]++;
            }
        }
    }

    for ($i = 1; $i <=5; $i++) {
        $j = str_pad($i, 3, "0", STR_PAD_LEFT);
        $total_cat_macho_lista[$j]=0;
        $total_cat_femea_lista[$j]=0;
    }

echo '
    <table class="table table-striped table-advance table-hover" id="tabela_itens_digitados" width="100%" style="font-size: 13px;">
    <thead>
        <tr>
        <th><input type="checkbox" class="seleciona_todos" data-toggle="tooltip" data-placement="right" title="Selecionar Todos"></th>
        <th> Qtde Total</th>
        <th> Categoria</th>
        <th> Sexo</th>
        <th> Qtde Selecionada</th>
        <th></th>
        <th hidden></th>
        </tr>
    </thead>
    <tbody>
';

    $sql = "SELECT * FROM tbl_animal_pasto 
        WHERE tbl_animal_pasto_situacao='A' AND
              tbl_animal_pasto_local ='$local' AND
              tbl_animal_pasto_id='$pasto'" . $wcategoria . $wsexo .
        " ORDER BY tbl_animal_pasto_nascimento DESC"; 

    $rs = mysqli_query($conector, $sql); 

    while ($reg_animal = mysqli_fetch_object($rs)){
        $codigo_local = $reg_animal->tbl_animal_pasto_local ;
        $numero_item = $reg_animal->tbl_animal_pasto_numero_item ;
        //$codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
        //$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
        $codigo_raca = $reg_animal->tbl_animal_pasto_raca;
        $codigo_fazenda = $reg_animal->tbl_animal_pasto_local ;
        $sexo = $reg_animal->tbl_animal_pasto_sexo; 
        $nascimento = new DateTime($reg_animal->tbl_animal_pasto_nascimento);

        $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
            $num_rows = mysqli_num_rows($tab_raca);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_raca);
            $descricao_raca = $reg->tab_descricao_raca;
        }
        else {
            $descricao_raca = '';
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

        $data_nascimento = $reg_animal->tbl_animal_pasto_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');

        $idade_ano = $idade_acompanhamento->format('%Y');
        $idade_mes = $idade_acompanhamento->format('%m');

        if ($idade_ano==0 && $idade_mes!=0) {
            $idade_animal = $idade_mes . ' mes(es)';
        }
        else if ($idade_ano!=0 && $idade_mes==0){
            $idade_animal = $idade_ano . ' ano(s)';
        }
        else if ($idade_ano!=0 && $idade_mes!=0) {
            $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
        }
        else {
            $idade_animal = '';
        }

        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");

        $num_rows = mysqli_num_rows($categoria);    

        if ($num_rows!=0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $idade_de = $reg_categoria->tab_categoria_idade_de;
                $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                if ($idade >= $idade_de && $idade <= $idade_ate) {
                    $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                }
            }
        }                   

        if ($sexo=='M') {
            $total_cat_macho_lista[$codigo_categoria]++;
        }
        else {
            $total_cat_femea_lista[$codigo_categoria]++;
        }


    } // Fim while

    for ($i = 1; $i <=5; $i++) {
        $j = str_pad($i, 3, "0", STR_PAD_LEFT);

        switch ($j) {
            case '001':
                $desc_categoria = '00 a 07 meses';

                if ($total_cat_macho_lista[$j]!=0) {
                    echo '
                    <tr>
                    <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value=M'.$j.'></td>
                    <td width="8%" class="qtd_categoria">'.$total_cat_macho_lista[$j].'</td>
                    <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                    <td width="8%" class="sexo_animal">Macho</td>
                    <td width="12%"><input class="form-control input-sm qtd_digitada" name="qtd_digitada" type="number" onkeypress = "return numeros(this, event)" value="'.$total_cat_macho_lista[$j].'"></td>                   
                    <td hidden class="codigo_categoria">'.$j.'</td>
                    <td width="57%"></td>
                    </tr>
                    ';
                    $animais_listados+=$total_cat_macho_lista[$j];

                }

                if ($total_cat_femea_lista[$j]!=0) {
                    echo '
                    <tr>
                    <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value=F'.$j.'></td>
                    <td width="8%" class="qtd_categoria">'.$total_cat_femea_lista[$j].'</td>
                    <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                    <td width="8%" class="sexo_animal">Fêmea</td>
                    <td width="12%"><input class="form-control input-sm qtd_digitada" name="qtd_digitada" type="number" onkeypress = "return numeros(this, event)" value="'.$total_cat_femea_lista[$j].'"></td>                   
                    <td hidden class="codigo_categoria">'.$j.'</td>
                    <td width="57%"></td>
                    </tr>
                    ';
                    $animais_listados+=$total_cat_femea_lista[$j];
                    
                }
                break;

            case '002':
                $desc_categoria = '08 a 12 meses';

                if ($total_cat_macho_lista[$j]!=0) {
                    echo '
                    <tr>
                    <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value=M'.$j.'></td>
                    <td width="8%" class="qtd_categoria">'.$total_cat_macho_lista[$j].'</td>
                    <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                    <td width="8%" class="sexo_animal">Macho</td>
                    <td width="12%"><input class="form-control input-sm qtd_digitada" name="qtd_digitada" type="number" onkeypress = "return numeros(this, event)" value="'.$total_cat_macho_lista[$j].'"></td>                   
                    <td hidden class="codigo_categoria">'.$j.'</td>
                    <td width="57%"></td>
                    </tr>
                    ';
                    $animais_listados+=$total_cat_macho_lista[$j];

                }

                if ($total_cat_femea_lista[$j]!=0) {
                    echo '
                    <tr>
                    <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value=F'.$j.'></td>
                    <td width="8%" class="qtd_categoria">'.$total_cat_femea_lista[$j].'</td>
                    <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                    <td width="8%" class="sexo_animal">Fêmea</td>
                    <td width="12%"><input class="form-control input-sm qtd_digitada" name="qtd_digitada" type="number" onkeypress = "return numeros(this, event)" value="'.$total_cat_femea_lista[$j].'"></td>                   
                    <td hidden class="codigo_categoria">'.$j.'</td>
                    <td width="57%"></td>
                    </tr>
                    ';
                    $animais_listados+=$total_cat_femea_lista[$j];
                    
                }
                break;

            case '003':
                $desc_categoria = '13 a 24 meses';

                if ($total_cat_macho_lista[$j]!=0) {
                    echo '
                    <tr>
                    <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value=M'.$j.'></td>
                    <td width="8%" class="qtd_categoria">'.$total_cat_macho_lista[$j].'</td>
                    <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                    <td width="8%" class="sexo_animal">Macho</td>
                    <td width="12%"><input class="form-control input-sm qtd_digitada" name="qtd_digitada" type="number" onkeypress = "return numeros(this, event)" value="'.$total_cat_macho_lista[$j].'"></td>                   
                    <td hidden class="codigo_categoria">'.$j.'</td>
                    <td width="57%"></td>
                    </tr>
                    ';
                    $animais_listados+=$total_cat_macho_lista[$j];

                }

                if ($total_cat_femea_lista[$j]!=0) {
                    echo '
                    <tr>
                    <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value=F'.$j.'></td>
                    <td width="8%" class="qtd_categoria">'.$total_cat_femea_lista[$j].'</td>
                    <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                    <td width="8%" class="sexo_animal">Fêmea</td>
                    <td width="12%"><input class="form-control input-sm qtd_digitada" name="qtd_digitada" type="number" onkeypress = "return numeros(this, event)" value="'.$total_cat_femea_lista[$j].'"></td>                   
                    <td hidden class="codigo_categoria">'.$j.'</td>
                    <td width="57%"></td>
                    </tr>
                    ';
                    $animais_listados+=$total_cat_femea_lista[$j];
                    
                }
                break;

            case '004':
                $desc_categoria = '25 a 36 meses';

                if ($total_cat_macho_lista[$j]!=0) {
                    echo '
                    <tr>
                    <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value=M'.$j.'></td>
                    <td width="8%" class="qtd_categoria">'.$total_cat_macho_lista[$j].'</td>
                    <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                    <td width="8%" class="sexo_animal">Macho</td>
                    <td width="12%"><input class="form-control input-sm qtd_digitada" name="qtd_digitada" type="number" onkeypress = "return numeros(this, event)" value="'.$total_cat_macho_lista[$j].'"></td>                   
                    <td hidden class="codigo_categoria">'.$j.'</td>
                    <td width="57%"></td>
                    </tr>
                    ';
                    $animais_listados+=$total_cat_macho_lista[$j];

                }

                if ($total_cat_femea_lista[$j]!=0) {
                    echo '
                    <tr>
                    <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value=F'.$j.'></td>
                    <td width="8%" class="qtd_categoria">'.$total_cat_femea_lista[$j].'</td>
                    <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                    <td width="8%" class="sexo_animal">Fêmea</td>
                    <td width="12%"><input class="form-control input-sm qtd_digitada" name="qtd_digitada" type="number" onkeypress = "return numeros(this, event)" value="'.$total_cat_femea_lista[$j].'"></td>                   
                    <td hidden class="codigo_categoria">'.$j.'</td>
                    <td width="57%"></td>
                    </tr>
                    ';
                    $animais_listados+=$total_cat_femea_lista[$j];
                    
                }
                break;

            case '005':
                $desc_categoria = '> 36 meses';

                if ($total_cat_macho_lista[$j]!=0) {
                    echo '
                    <tr>
                    <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value=M'.$j.'></td>
                    <td width="8%" class="qtd_categoria">'.$total_cat_macho_lista[$j].'</td>
                    <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                    <td width="8%" class="sexo_animal">Macho</td>
                    <td width="12%"><input class="form-control input-sm qtd_digitada" name="qtd_digitada" type="number" onkeypress = "return numeros(this, event)" value="'.$total_cat_macho_lista[$j].'"></td>                   
                    <td hidden class="codigo_categoria">'.$j.'</td>
                    <td width="57%"></td>
                    </tr>
                    ';
                    $animais_listados+=$total_cat_macho_lista[$j];

                }

                if ($total_cat_femea_lista[$j]!=0) {
                    echo '
                    <tr>
                    <td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1" value=F'.$j.'></td>
                    <td width="8%" class="qtd_categoria">'.$total_cat_femea_lista[$j].'</td>
                    <td width="12%" class="desc_categoria">'.$desc_categoria.'</td>
                    <td width="8%" class="sexo_animal">Fêmea</td>
                    <td width="12%"><input class="form-control input-sm qtd_digitada" name="qtd_digitada" type="number" onkeypress = "return numeros(this, event)" value="'.$total_cat_femea_lista[$j].'"></td>                   
                    <td hidden class="codigo_categoria">'.$j.'</td>
                    <td width="57%"></td>
                    </tr>
                    ';
                    $animais_listados+=$total_cat_femea_lista[$j];
                    
                }
                break;
        }
    }

    echo '
        </tbody>
        </table>
    ';

    echo "<script>
        $(document).ready(function(){
            $('#tabela_itens_digitados').DataTable({
                'responsive': true,
                'paging':   false,
                'ordering': false,
                'info':     true,
                'language': {
                    'sSearch': 'Busca:',
                    'zeroRecords': 'Nada encontrado',
                    'info': '',
                    'infoEmpty': 'Nenhum registro disponível',
                    'infoFiltered': '(filtrado de _MAX_ registros no total)',
                },
                initComplete: function() {
                    $('table.dataTable').css('width', '100%');
                }
            });

            $('.seleciona_todos').click(function(event) {
                var total_selecionados = 0;

                if(this.checked) {
                    $('.checkbox1').each(function() {
                        this.checked = true;
                    });
                }else{
                    $('.checkbox1').each(function() {
                        this.checked = false;  
                    });         
                }

                $('#tabela_itens_digitados tbody tr').each(function(){
                    var qtd_selecionado = $(this).find('.qtd_digitada').val();

                    if (qtd_selecionado==0 || qtd_selecionado=='') {
                        $(this).find('.checkbox1').prop('checked',false);
                    }
                });

                if(this.checked==true) {
                    $('#tabela_itens_digitados tbody tr').each(function(){
                        var qtd_selecionado = parseInt($(this).find('.qtd_digitada').val());
                        var selecionada = $(this).find('.checkbox1').is(':checked');

                        if (selecionada==true) {
                            total_selecionados+=qtd_selecionado;
                        }
                    });
                }

                if(this.checked==false) {
                    $('#tabela_itens_digitados tbody tr').each(function(){
                        var qtd_selecionado = parseInt($(this).find('.qtd_digitada').val());
                        var selecionada = $(this).find('.checkbox1').is(':checked');

                        if (selecionada==true) {
                            total_selecionados+=qtd_selecionado;
                        }
                    });
                }

                $('.total_digitados').text('Animais Selecionados: ' + total_selecionados);
                $('.total_digitados').val(total_selecionados);

            });

            $('.checkbox1').click(function(event) {
                total_selecionados=0;

                if(this.checked==true) {
                    $('#tabela_itens_digitados tbody tr').each(function(){
                        var qtd_selecionado = $(this).find('.qtd_digitada').val();
                        var selecionada = $(this).find('.checkbox1').is(':checked');

                        if (qtd_selecionado==0 || qtd_selecionado=='') {
                            alert ('A quantidade selecionada não pode ser zero ou espaço.');
                            $(this).find('.checkbox1').prop('checked',false);
                            selecionada = false;
                        }

                        var qtd_selecionado = parseInt($(this).find('.qtd_digitada').val());

                        if (selecionada==true) {
                            total_selecionados+=qtd_selecionado;
                        }
                    });
                }

                if(this.checked==false) {
                    $('#tabela_itens_digitados tbody tr').each(function(){
                        var qtd_selecionado = parseInt($(this).find('.qtd_digitada').val());
                        var selecionada = $(this).find('.checkbox1').is(':checked');

                        if (selecionada==true) {
                            total_selecionados+=qtd_selecionado;
                        }
                    });
                }

                $('.total_digitados').text('Animais Selecionados: ' + total_selecionados);
                $('.total_digitados').val(total_selecionados);
            });

            $('.qtd_digitada').change(function(event) {
                total_selecionados=0;
                
                $('#tabela_itens_digitados tbody tr').each(function(){
                    var qtd_digitada = $(this).find('.qtd_digitada').val();
                    var selecionada = $(this).find('.checkbox1').is(':checked');
                    var item = $(this).find('.checkbox1').val();

                    var sexo = item.substr(0, 1);      
                    var cate = item.substr(1, 3);      

                    if (cate==001 && sexo=='M') {
                        var qtd_anterior = parseInt($('#cat_pasto_m1').val());

                        if (qtd_digitada > qtd_anterior){
                            alert ('A quantidade selecionada não pode ser maior que a quantidade existente na lista.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }

                        if ((qtd_digitada == 0 || qtd_digitada == '') && selecionada==true) {
                            alert ('Este item foi selecionado, a quantidade selecionada não pode ser zero ou espaço.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }
                    }

                    if (cate==001 && sexo=='F') {
                        var qtd_anterior = parseInt($('#cat_pasto_f1').val());

                        if (qtd_digitada > qtd_anterior){
                            alert ('A quantidade selecionada não pode ser maior que a quantidade existente na lista.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }

                        if ((qtd_digitada == 0 || qtd_digitada == '') && selecionada==true) {
                            alert ('Este item foi selecionado, a quantidade selecionada não pode ser zero ou espaço.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }
                    }

                    if (cate==002 && sexo=='M') {
                        var qtd_anterior = parseInt($('#cat_pasto_m2').val());

                        if (qtd_digitada > qtd_anterior){
                            alert ('A quantidade selecionada não pode ser maior que a quantidade existente na lista.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }

                        if ((qtd_digitada == 0 || qtd_digitada == '') && selecionada==true) {
                            alert ('Este item foi selecionado, a quantidade selecionada não pode ser zero ou espaço.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }
                    }

                    if (cate==002 && sexo=='F') {
                        var qtd_anterior = parseInt($('#cat_pasto_f2').val());

                        if (qtd_digitada > qtd_anterior){
                            alert ('A quantidade selecionada não pode ser maior que a quantidade existente na lista.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }

                        if ((qtd_digitada == 0 || qtd_digitada == '') && selecionada==true) {
                            alert ('Este item foi selecionado, a quantidade selecionada não pode ser zero ou espaço.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }
                    }

                    if (cate==003 && sexo=='M') {
                        var qtd_anterior = parseInt($('#cat_pasto_m3').val());

                        if (qtd_digitada > qtd_anterior){
                            alert ('A quantidade selecionada não pode ser maior que a quantidade existente na lista.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }

                        if ((qtd_digitada == 0 || qtd_digitada == '') && selecionada==true) {
                            alert ('Este item foi selecionado, a quantidade selecionada não pode ser zero ou espaço.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }
                    }

                    if (cate==003 && sexo=='F') {
                        var qtd_anterior = parseInt($('#cat_pasto_f3').val());

                        if (qtd_digitada > qtd_anterior){
                            alert ('A quantidade selecionada não pode ser maior que a quantidade existente na lista.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }

                        if ((qtd_digitada == 0 || qtd_digitada == '') && selecionada==true) {
                            alert ('Este item foi selecionado, a quantidade selecionada não pode ser zero ou espaço.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }
                    }

                    if (cate==004 && sexo=='M') {
                        var qtd_anterior = parseInt($('#cat_pasto_m4').val());

                        if (qtd_digitada > qtd_anterior){
                            alert ('A quantidade selecionada não pode ser maior que a quantidade existente na lista.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }

                        if ((qtd_digitada == 0 || qtd_digitada == '') && selecionada==true) {
                            alert ('Este item foi selecionado, a quantidade selecionada não pode ser zero ou espaço.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }
                    }

                    if (cate==004 && sexo=='F') {
                        var qtd_anterior = parseInt($('#cat_pasto_f4').val());

                        if (qtd_digitada > qtd_anterior){
                            alert ('A quantidade selecionada não pode ser maior que a quantidade existente na lista.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }

                        if ((qtd_digitada == 0 || qtd_digitada == '') && selecionada==true) {
                            alert ('Este item foi selecionado, a quantidade selecionada não pode ser zero ou espaço.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }
                    }

                    if (cate==005 && sexo=='M') {
                        var qtd_anterior = parseInt($('#cat_pasto_m5').val());

                        if (qtd_digitada > qtd_anterior){
                            alert ('A quantidade selecionada não pode ser maior que a quantidade existente na lista.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }

                        if ((qtd_digitada == 0 || qtd_digitada == '') && selecionada==true) {
                            alert ('Este item foi selecionado, a quantidade selecionada não pode ser zero ou espaço.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }
                    }

                    if (cate==005 && sexo=='F') {
                        var qtd_anterior = parseInt($('#cat_pasto_f5').val());

                        if (qtd_digitada > qtd_anterior){
                            alert ('A quantidade selecionada não pode ser maior que a quantidade existente na lista.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo) {

                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }

                        if ((qtd_digitada == 0 || qtd_digitada == '') && selecionada==true) {
                            alert ('Este item foi selecionado, a quantidade selecionada não pode ser zero ou espaço.');

                            $('#tabela_itens_digitados tbody tr').each(function(){
                                var item = $(this).find('.checkbox1').val();
                                var sexo_anterior = item.substr(0, 1);      
                                var cate_anterior = item.substr(1, 3);      

                                if (cate_anterior==cate && sexo_anterior==sexo ) {
                                    $(this).find('.qtd_digitada').val(qtd_anterior);
                                    qtd_digitada = qtd_anterior;
                                }
                            });
                        }
                    }
                    
                    if (selecionada==true) {
                        var qtd_digitada = parseInt($(this).find('.qtd_digitada').val());

                        total_selecionados+=qtd_digitada;
                    }
                });
                

                $('.total_digitados').text('Animais Selecionados: ' + total_selecionados);
                $('.total_digitados').val(total_selecionados);
            }); 
        });
          
    </script>";

    mysqli_close($conector);
            
?>


                
                
