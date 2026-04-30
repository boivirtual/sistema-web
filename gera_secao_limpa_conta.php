<?php
    @session_start();

    $limpa = $_POST['limpa'];
    $_SESSION['limpa_conta_ctp']=$limpa;
    $_SESSION['limpa_conta_ctr']=$limpa;
    $_SESSION['limpa_conta_previsao']=$limpa;
    $_SESSION['limpa_conta_aceite']=$limpa;
?>