<?php
// Area de um anel de coordenadas GeoJSON [lng, lat, alt] em hectares (esfera WGS84).
function area_geodesica_ha($anel) {
    $raio = 6378137.0;
    $soma = 0;
    $n = count($anel);

    for ($i = 0; $i < $n - 1; $i++) {
        $lng1 = deg2rad($anel[$i][0]);
        $lng2 = deg2rad($anel[$i + 1][0]);
        $lat1 = deg2rad($anel[$i][1]);
        $lat2 = deg2rad($anel[$i + 1][1]);
        $soma += ($lng2 - $lng1) * (2 + sin($lat1) + sin($lat2));
    }

    return round(abs($soma * $raio * $raio / 2) / 10000, 2);
}
?>
