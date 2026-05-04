<?php
include "conecta_mysql.inc";

$tipo_conta = $_POST['tipo_conta'];

if ($tipo_conta=='D' || $tipo_conta=='T') {
    $_SESSION['limpa_conta_ctp']='';
    $_SESSION['limpa_conta_aceite']='';
}
else {
    $_SESSION['limpa_conta_ctr']='';
}

if ($tipo_conta=='T') {
    $conta = mysqli_query($conector, "select * from tbl_plano_contas
            where tbl_plano_contas_lixeira=0
            order by tbl_plano_contas_codigo_id ASC");
}
else {
    $conta = mysqli_query($conector, "select * from tbl_plano_contas
            where tbl_plano_contas_lixeira=0 and
                  tbl_plano_contas_debito_credito='$tipo_conta'
            order by tbl_plano_contas_codigo_id ASC");
}

echo mysqli_error($conector);

$count = 0;

while ($c = mysqli_fetch_object($conta)) {
    if ($count != 0) {
        echo "</div>";
    }

    if ($c->tbl_plano_contas_nivel == 1) {
        echo
        "<div class='row' style='margin-top: 5px;'>
            <div class='col-md-12'>
                <label class='checkbox-inline' for='{$c->tbl_plano_contas_codigo_id}'>
                    <input class='{$c->tbl_plano_contas_descricao}' type='checkbox' onchange='get_marked_boxes(this.id, this, {$c->tbl_plano_contas_nivel})' value='{$c->tbl_plano_contas_codigo_id}' id='{$c->tbl_plano_contas_codigo_id}' name='conta_option'>
                     {$c->tbl_plano_contas_descricao}
                </label>
            </div>
        ";
    } elseif ($c->tbl_plano_contas_nivel == 2) {
        echo
        "<div class='row' style='margin-left: 10px; margin-top: 5px;'>
            <div class='col-md-12'>
                <label class='checkbox-inline' for='{$c->tbl_plano_contas_codigo_id}'>
                    <input class='{$c->tbl_plano_contas_descricao}' type='checkbox' onchange='get_marked_boxes(this.id, this, {$c->tbl_plano_contas_nivel})' value='{$c->tbl_plano_contas_codigo_id}' id='{$c->tbl_plano_contas_codigo_id}' name='conta_option'>
                        {$c->tbl_plano_contas_descricao}
                </label>
            </div>
        </div>";
    } else {
        echo
        "<div class='row' style='margin-left: 30px;'>
            <div class='col-md-12'>
                <label class='checkbox-inline' for='{$c->tbl_plano_contas_codigo_id}'>
                    <input class='{$c->tbl_plano_contas_descricao}' type='checkbox' onchange='get_marked_boxes(this.id, this, {$c->tbl_plano_contas_nivel})' value='{$c->tbl_plano_contas_codigo_id}' id='{$c->tbl_plano_contas_codigo_id}' name='conta_option'>
                        {$c->tbl_plano_contas_descricao}
                </label>
            </div>
        </div>";
    }

    $count++;
}
