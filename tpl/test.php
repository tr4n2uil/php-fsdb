<?php
/**
 *	Sample Project Test Page
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	require_once( PR_ROOT. 'auth/session.php' );
	require_once( ROOT. 'models.php' );

	/*$qs = User::objects()->filter( array( 'email' => 'vibhaj.itbhu@gmail.com' ) )->only( array( 'id', 'username', 'email' ) )->order_by( array( 'username' ) )->using( 'default' );

	foreach( $qs as $row )
		print_r( $row );
	echo '<br /><br />';

	$row->set( 'email', 'tr4n2uil@gmail.com' );
	print_r( $row );
	echo '<br /><br />';
	//$row->save();
	//$row->delete();

	$u = User::objects()->get( array( 'email' => 'vibhajitbhu@gmail.com' ) );
	print_r( $u );
	echo '<br /><br />';

	$p = Person::objects()->values( array( 'user__id', 'user__username', '*' ) )->get( new Q_OR( array( 'user__email' => 'vibhajitbhu@gmail.com', 'user__username' => 'vibhaj8' ) ) );
	print_r( $p );
	echo '<br /><br />';

	//$dbt = User::objects()->create( array( 'email' => 'vibhajitbhu@gmail.com', 'username' => 'vbj' ) );
	//print_r( $dbt );*/

?>

<html>
	<head>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>
		<style type="text/css">
			body, input { font-family: 'Open Sans', sans-serif; font-size: 14px; }
		</style>
	</head>
	<body>

<?php

	echo 'Hello World! from BlackPearl Sample Project<br /><br />';

	$user = isset( $URL_ARGS[ 'user' ] ) ? $URL_ARGS[ 'user' ] : '';
	$id = isset( $URL_ARGS[ 'id' ] ) ? $URL_ARGS[ 'id' ] : '';

	$u = null;
	if( $user )
		$u = session_user();

	echo "Parameters: Username=$user ID=$id<br /><br />";
	echo "Pass parameters in URL /view/username/id/<br /><br />";

	echo '<a href="'.APP.'view/vibhaj-rajan/15/">Vibhaj\'s Posts</a><br /><br />';

	if( isset( $_SESSION[ 'user_id' ] ) )
		echo '<a href="'.APP.'logout/">Logout</a>';
	else
		echo '<a href="'.APP.'login/google/">Login with Google</a>';

?>

	</body>
</html>