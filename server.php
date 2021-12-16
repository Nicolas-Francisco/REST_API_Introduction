<?php

header('Content-Type: application/json');

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