<?php
/**
 *	Sample Project Logout Page
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	require_once( PR_ROOT. 'auth/session.php' );

	$SM->logout();
	http_redirect( LOGOUT_REDIRECT );

?>
