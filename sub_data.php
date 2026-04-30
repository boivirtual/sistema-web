<?php

$inicio=date("Y-m-d");
$parcelas=12;
$data_termino = new DateTime($inicio);
$data_termino->sub(new DateInterval('P'.$parcelas.'M'));
$termino_pagamento=$data_termino->format('Y-m-d');

print_r($termino_pagamento);


?>