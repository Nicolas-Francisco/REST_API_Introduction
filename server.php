<?php

header('Content-Type: application/json');

// AUTENTIFICACIÓN BASADA EN ACCESS TOKENS
// Si no recibimos un token, entonces lanzamos error
if ( !array_key_exists( 'HTTP_X_TOKEN', $_SERVER ) ) {
	http_response_code( 401 );
	echo json_encode(
		[
			'error' => "token needed",
		]
	);
	
	die;
}

// Guardamos el URL del servidor de autentificación
$url = 'https://localhost:8001';

// Se establece una conexión con el servidor de autentificación
$ch = curl_init( $url );

// Creamos un header a la conexión
curl_setopt( $ch, CURLOPT_HTTPHEADER, [
	"X-Token: {$_SERVER['HTTP_X_TOKEN']}",
]);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

// Enviamos el header a la conexión
$ret = curl_exec( $ch );

// Si hay un error en la conexión, lanzamos error
if ( curl_errno($ch) != 0 ) {
	die ( curl_error($ch) );
}

// Si la conexión no lanza verdadero, tamibén lanzamos error
if ( $ret !== 'true' ) {
	http_response_code( 403 );
	die;
}

// -----------------------------------------------------------------------------------------
// // AUTENTIFICACION BASADA EN HMAC
// // Si alguno de estos parámetros no está en el header
// if ( 
// 	!array_key_exists('HTTP_X_HASH', $_SERVER) || 
// 	!array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) || 
// 	!array_key_exists('HTTP_X_UID', $_SERVER)  
// 	) {

// 	// Lanzamos un error de autorización
// 	header( 'Status-Code: 403' );

// 	echo json_encode(
// 		[
// 			'error' => "No autorizado",
// 		]
// 	);
	
// 	die;
// }

// // Rescatamos todos los valores del header, id, timestamp y hash
// list( $hash, $uid, $timestamp ) = [ $_SERVER['HTTP_X_HASH'], $_SERVER['HTTP_X_UID'], $_SERVER['HTTP_X_TIMESTAMP'] ];
// // mensaje secreto
// $secret = 'sic mundus creatus est';
// // generamos el hash
// $newHash = sha1($uid.$timestamp.$secret);

// if ( $newHash !== $hash ) {
// 	header( 'Status-Code: 403' );
	
// 		echo json_encode(
// 			[
// 				'error' => "No autorizado. Hash esperado: $newHash, hash recibido: $hash",
// 			]
// 		);
		
// 		die;
// }

// -----------------------------------------------------------------------------------------
// AUTENTIFICACIÓN BASADA EN HTTP
// Autenticación del nombre de usuario
// $user = array_key_exists('PHP_AUTH_USER', $_SERVER) ? $_SERVER['PHP_AUTH_USER'] : '';
// // Autenticación de la contraseña del usuario
// $pwd = array_key_exists('PHP_AUTH_PW', $_SERVER) ? $_SERVER['PHP_AUTH_PW'] : '';

// // Verificación de la autenticación del usuario.
// // en este caso, solo mauro puede ingresar
// if ( $user !== 'blackfire' || $pwd !== '1234' ) {
// 	header('Status-Code: 403');

// 	echo json_encode(
// 		[ 
// 			'error' => "Usuario y/o password incorrectos", 
// 		]
// 	);

// 	die;
// }
// -----------------------------------------------------------------------------------------


// Definimos los recursos disponibles
$allowedResourceTypes = [
	'books',
	'authors',
	'genres',
];

// Validamos que el recurso este disponible
$resourceType = $_GET['resource_type'];
if ( !in_array( $resourceType, $allowedResourceTypes ) ) {
	http_response_code( 400 );
	echo json_encode(
		[
			'error' => "$resourceType is un unkown",
		]
	);
	
	die;
}

// NUESTROS DATOS A EXPONER EN CONEXION REST
$books = [
	1 => [
		'titulo' => 'Lo que el viento se llevo',
		'id_autor' => 2,
		'id_genero' => 2,
	],
	2 => [
		'titulo' => 'La Iliada',
		'id_autor' => 1,
		'id_genero' => 1,
	],
	3 => [
		'titulo' => 'La Odisea',
		'id_autor' => 1,
		'id_genero' => 1,
	],
];

// Rescatamos el ID ingresado en el GET
$resourceId = array_key_exists('resource_id', $_GET ) ? $_GET['resource_id'] : '';
$method = $_SERVER['REQUEST_METHOD'];

// SWITCH DE CASOS GET, PUT, POST, DELETE
switch ( strtoupper( $method ) ) {
	// Caso Get
	case 'GET':
		// Si no solicitan los libros, entonces arrojamos error
		if ( "books" !== $resourceType ) {
			http_response_code( 404 );

			echo json_encode(
				[
					'error' => $resourceType.' not yet implemented :(',
				]
			);

			die;
		}

		// Si el ID no est vacío, lo buscamos y lo retornamos
		if (!empty( $resourceId )) {
			// Si el id existe, encodeamos el libro con el código
			if ( array_key_exists( $resourceId, $books ) ) {
				echo json_encode( $books[ $resourceId ] );
			// Si no existe, lanzamos error
			} else {
				http_response_code( 404 );

				echo json_encode(
					[
						'error' => 'Book '.$resourceId.' not found :(',
					]
				);
			}

		// Si todo está bien, entonces retornamos los libros
		} else {
			echo json_encode(
				$books
			);
		}

		die;
		
		break;

	// Caso POST
	case 'POST':
		// Asumiendo que recibimos un json, lo recibimos en crudo
		$json = file_get_contents( 'php://input' );
		$books[] = json_decode( $json );
		// echo array_keys($books)[count($books)-1];        // entrega el ultimo id, como convención
		echo json_encode($books);			// En este caso, retornaremos toda la colección
		break;

	// Caso PUT
	case 'PUT':
		// Si existen elementos, y el ID está en el arreglo, lo modificamos
		if ( !empty($resourceId) && array_key_exists( $resourceId, $books ) ) {
			$json = file_get_contents( 'php://input' );
			
			$books[ $resourceId ] = json_decode( $json, true );

			// echo $resourceId;					// entrega el ultimo id, como convención
			echo json_encode($books);			// En este caso, retornaremos toda la colección
		}
		break;
	
	// Caso DELETE
	case 'DELETE':
		// Si existen elementos, y el ID está en el arreglo, lo eliminamos
		if ( !empty($resourceId) && array_key_exists( $resourceId, $books ) ) {
			unset( $books[ $resourceId ] );

			echo json_encode($books);			// En este caso, retornaremos toda la colección
		}
		break;
	default:
		http_response_code( 404 );

		echo json_encode(
			[
				'error' => $method.' not yet implemented :(',
			]
		);

		break;
}

?>