<?php  
/**
 *	Image/Object Embed Handler
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	// extract path from url parameters
	if( $_FILES[ 'file' ] ){
		require_once( PR_ROOT. 'fs/file.php' );

		$f = save_file( MEDIA_ROOT );
		$url = str_replace( '\\', '/', substr( $f->path, strlen( ROOT ) ) );

		echo json_encode( array( 
			'html' => '<div><a href="' .$url. '" target="_blank"><img src="' .$url. '" class="image" /></a></div><div><br/></div>',
		) );
	}

?>
