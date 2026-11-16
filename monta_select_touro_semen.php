<select class="form-control" id="codigo_touro_semem" name="codigo_touro_semem">

<option value="000000000">...</option>

<optgroup label="SEMEM">
    <?php 
        $semem = mysqli_query($conector, "select * from tbl_semem where tbl_semem_lixeira=0"); 

        while($reg = mysqli_fetch_object($semem)) { 
    ?>

    <option value="<?php 
        echo $reg->tbl_semem_codigo_id ?>">
        <?php 
            echo $reg->tbl_semem_nome;
        ?>
    </option>

    <?php } ?>
</optgroup>

<optgroup label="TOUROS">
    <?php 
        $touro = mysqli_query($conector, "select * from tbl_animais where tbl_animal_lixeira=0 and
                                                tbl_animal_sexo='M'
                                          order by tbl_animal_codigo_numerico"); 

        while($reg = mysqli_fetch_object($touro)) { 
    ?>

    <option value="<?php 
        echo $reg->tbl_animal_codigo_id ?>">
        <?php 
        echo $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
        ?>
    </option>

    <?php } ?>
</optgroup>
 
</select>
