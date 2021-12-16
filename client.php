<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $argv[1]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

switch ( $httpCode ) {
    case 200:
        echo 'Conexión Establecida';
        break;
    case 400:
        echo 'ERROR 400 - Pedido incorrecto';
        break;
    case 401:
        echo 'ERROR 401 - Pedido no autorizado';
        break;
    case 404:
        echo 'ERROR 404 - Recurso no encontrado';
        break;
    case 500:
        echo 'ERROR 500 - Falla del servidor';
        break;
}