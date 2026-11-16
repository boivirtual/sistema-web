<?php

include "conecta_mysql.inc";

    $local = $_POST["local"];
    $pasto = $_POST["pasto"];
    $data = $_POST["data"];

    $tbl_nutricao = mysqli_query($conector, "SELECT * FROM tbl_nutricao 
        WHERE tbl_nutricao_data = '$data' AND 
              tbl_nutricao_codigo_local = '$local' AND 
              tbl_nutricao_codigo_pasto = '$pasto' AND 
              tbl_nutricao_lixeira=0");

    $numLinhas = mysqli_num_rows($tbl_nutricao);

    if($numLinhas > 0){
        $objItens = mysqli_query($conector, "SELECT * FROM tbl_nutricao 
            JOIN tbl_produto 
              ON tbl_nutricao_codigo_produto = tbl_produto_codigo_id 
            JOIN tabela_unidade_produtos 
              ON tbl_produto_unidade = tab_codigo_unidade_id 
           WHERE tbl_nutricao_data = '$data' AND 
                 tbl_nutricao_codigo_local = '$local' AND 
                 tbl_nutricao_codigo_pasto = '$pasto' AND 
                 tbl_nutricao_lixeira <> 1");

        $nL = mysqli_num_rows($objItens);
        if($nL > 0){
            echo '<table class="table table-striped table-advance table-hover">';
            echo "<thead>
                        <tr>
                            <th> Data</th>
                            <th> Produto</th>
                            <th> Quantidade</th>
                            <th> Und</th>
                            <th> Qtd Animais</th>
                            <th> Média/Cabeças</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>";

            while($regItens = mysqli_fetch_object($objItens)){
                $data = date("d/m/Y", strtotime($regItens->tbl_nutricao_data));
                $qtd_prouto = number_format($regItens->tbl_nutricao_quantidade_produto, 2, ",", ".");
                $qtd_media = number_format($regItens->tbl_nutricao_media_cabeca, 2, ",", ".");

                echo "<tr>
                        <td>$data</td>
                        <td>$regItens->tbl_produto_descricao</td>
                        <td>$qtd_prouto</td>
                        <td>$regItens->tab_codigo_unidade_produtos</td>
                        <td>$regItens->tbl_nutricao_qtd_animais</td>
                        <td>$qtd_media</td>
                        <td><a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviarLixeira(\"{$regItens->tbl_nutricao_id}\")' ></i></a></td>
                    </tr>";
            }
        }
    }

    mysqli_close($conector);

?>