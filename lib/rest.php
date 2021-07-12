<?php  
/**
 *	REST Controller for DB Models
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	// extract path from url parameters
	$request_method = isset( $_SERVER[ 'REQUEST_METHOD' ] ) ? $_SERVER[ 'REQUEST_METHOD' ] : 'GET';
	$content_type = isset( $_SERVER[ 'CONTENT_TYPE' ] ) ? $_SERVER[ 'CONTENT_TYPE' ] : false;
	$http_accept = isset( $_SERVER[ 'HTTP_ACCEPT' ] ) ? $_SERVER[ 'HTTP_ACCEPT' ] : REST_FORMAT;

	$output_format = isset( $URL_ARGS[ 'format' ] ) ? $URL_ARGS[ 'format' ] : $http_accept;
	$model = isset( $URL_ARGS[ 'model' ] ) ? $URL_ARGS[ 'model' ] : false;
	$id = isset( $URL_ARGS[ 'id' ] ) ? $URL_ARGS[ 'id' ] : false;
	
	$data = array();
	if($content_type){
		$content_type = explode( ';', $content_type );
		$content_type = $content_type[0];
	}
	switch( $content_type ){
		case 'application/json':
			$input = file_get_contents( 'php://input' );
			$data = json_decode( $input, true );
			break;

		case 'application/x-www-form-urlencoded':
		default:
			$data = $_POST ? $_POST : $_GET;
			break;
	}

	//echo json_encode($data);

	$output = array();
	try {
		switch( $request_method ){
			case 'GET':
				if( $id )
					$output = $model::obj_get( $id );
				else 
					$output = $model::obj_query();
				break;

			case 'POST':
				$output = $model::obj_create( $data );
				break;

			case 'PUT':
				$output = $model::obj_update( $id, $data );
				break;

			case 'DELETE':
				$output = $model::obj_delete( $id );
				break;

			default:
				break;
		}
	}
	catch( Exception $e ){
		header(':', true, 500);
		echo $e->getMessage()."<br /><br />";
		var_dump($e);
		exit();
	}

	switch( $output_format ){
		case 'application/json':
		default:
			echo json_encode( $output );
			break;
	}

?>
