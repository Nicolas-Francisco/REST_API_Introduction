<?php

$method = strtoupper( $_SERVER['REQUEST_METHOD'] );

// Creamos un token temporal
$token = "5d0937455b6744.68357201";

// Si estamos en un método POST, debemos brindar un token válido de autentificación
if ( $method === 'POST' ) {
    // Si no está el id del cliente o la llave, lanzamos error
    if ( !array_key_exists( 'HTTP_X_CLIENT_ID', $_SERVER ) || !array_key_exists( 'HTTP_X_SECRET', $_SERVER ) ) {
        http_response_code( 400 );
        die( 'Faltan parametros' );
    }

    // Tomamos el CLIENT_ID del header y la llave secreta
    $clientId = $_SERVER['HTTP_X_CLIENT_ID'];
    $secret = $_SERVER['HTTP_X_SECRET'];

    // Si el id no es válido, o la llave es incorrecta, lanzamos error
    if ( $clientId !== '666' || $secret !== 'sic mundus creatus est' ) {
        http_response_code( 403 );

        die ( "No autorizado");
    }

    // Si todo está correcto, informamos el token
    echo "$token";

// Si en realidad estamos en un GET, debemos verificar si el token es correcto
} elseif ( $method === 'GET' ) {
    // Si no está el token, lanzamos error
    if ( !array_key_exists( 'HTTP_X_TOKEN', $_SERVER ) ) {
        http_response_code( 400 );
        die ( 'Faltan parametros' );
    }

    // Si el token recibido existe o es válido, lanzamos true
    if ( $_SERVER['HTTP_X_TOKEN'] == $token ) {
        echo 'true';
    } else {
        echo 'false';
    }

// Si recibimos otra acción, lanzamos error
} else {
    echo 'false';
}