<?php
/**
 *	Sample Project Login Page
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	require_once( PR_ROOT. 'auth/session.php' );

	$next = $_SESSION[ 'next' ] = isset( $_GET[ 'next' ] ) ? $_GET[ 'next' ] : ( isset( $_SESSION[ 'next' ] ) ? $_SESSION[ 'next' ] : LOGIN_REDIRECT );

	$provider = isset( $URL_ARGS[ 'provider' ] ) ? $URL_ARGS[ 'provider' ] : '';
	$error = '';

	if( $provider ){
		User::social_login( $provider, $next );
		exit();
	}
	else if( isset( $_POST[ 'username' ] ) && isset( $_POST[ 'passwd' ] ) ){
		if( !$_POST[ 'username' ] )
			$error = 'Username cannot be empty';
		elseif( !$_POST[ 'passwd' ] )
			$error = 'Password cannot be empty';
		else {
			try {
				User::login( $_POST[ 'username' ], $_POST[ 'passwd' ], $next );
				exit();
			}
			catch( AuthInvalidCredentials $e ){
				$error = 'Invalid Credentials';
			}	
		}
	}

?>
