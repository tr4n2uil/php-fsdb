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

	require_once( PR_ROOT. 'auth/login.php' );
	//header( 'Location: '. APP. 'login/google/' );
	
?>

<html>
	<head>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>
		<style type="text/css">
			body, input { font-family: 'Open Sans', sans-serif; font-size: 14px; }
			input { border: solid 1px #ccc; padding: 5px 12px; }
		</style>
	</head>
	<body>
		<div style="width: 185px; text-align: left;">

			<?php if( $error ){
				echo '<div style="border: solid 1px red; color: darkred; font-weight: bold;">'.$error.'</div>';
			} ?>

			<form action="../api/session/" method="post">
				<label>Username<br /><input type="text" name="username" /></label><br />
				<label>Password<br /><input type="password" name="password" /></label><br />
				<input type="hidden" name="next" value="<?php echo $next; ?>"/>
				<input type="submit" value="Login" /><input type="reset" value="Reset" />
			</form>
		</div>
	</body>
</html>
