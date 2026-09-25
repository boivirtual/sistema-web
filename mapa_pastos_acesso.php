<?php
// Confere se a fazenda esta entre os locais liberados para o usuario logado
function usuario_pode_local($conector_acesso, $local) {
    @ session_start();

    $codigo_usuario = mysqli_real_escape_string($conector_acesso, $_SESSION['id_usuario']);

    $rs = mysqli_query($conector_acesso, "SELECT local_usuario FROM usuario
        WHERE id_usuario='$codigo_usuario' AND lixeira_usuario=0");

    if (!$rs || mysqli_num_rows($rs) == 0) {
        return false;
    }

    $reg = mysqli_fetch_assoc($rs);

    foreach (explode(',', $reg['local_usuario']) as $valor) {
        if (trim($valor) === $local) {
            return true;
        }
    }

    return false;
}
?>
