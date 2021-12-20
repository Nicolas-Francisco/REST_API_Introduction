<?php

$matches=[];

// Excepcion para las url principal sea index.html
if (in_array( $_SERVER["REQUEST_URI"], [ '/index.html', '/', '' ] )) {
    echo file_get_contents( '/Users/luisrangel/dev/cursos/php/APIRest/index.html' );
    die;
}

// Verifica expresion regular de la forma "/(A-Z)*/(A-Z)*"
// En nuestro caso, tenemos "/<recurso>/<id_recurso>"
if (preg_match('/\/([^\/]+)\/([^\/]+)/', $_SERVER["REQUEST_URI"], $matches)) {
    // Conseguimos los valores internos del url y les hacemos match con nuestro servidor
    $_GET['resource_type'] = $matches[1];
    $_GET['resource_id'] = $matches[2];

    error_log( print_r($matches, 1) );
    // Llamamos al servidos.php
    require 'server.php';

// Verifica expresion regular del tipo "/(A-Z)*"
// En nuestro caso, tenemos "/<recurso>"
} elseif ( preg_match('/\/([^\/]+)\/?/', $_SERVER["REQUEST_URI"], $matches) ) {
    $_GET['resource_type'] = $matches[1];
    error_log( print_r($matches, 1) );

    // Llamamos al servidos.php
    require 'server.php';

// Si este no calza con ninguna expresion, lanzamos error
} else {

    error_log('No matches');
    http_response_code( 404 );
}