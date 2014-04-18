<?php
/**
 *	Sample Project Settings
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/
	
	// default settings
	define( 'ROOT', dirname( __FILE__ ) .'/../' );

	// project settings
	define( 'PR_ROOT', ROOT .'lib/' );
	define( 'DEBUG', false );
	
	//load config file
	define( 'CONF_INI', getenv( 'CONF_INI' ) ? getenv( 'CONF_INI' ) : "default" );
	$ini = parse_ini_file( CONF_INI.'.conf.ini', true);
	
	// url utility settings
	define( 'HOME', 'home' );
	define( 'CACHE_DIR', ROOT. 'tmp/' );
	define( 'HOST', $ini['URL_UTILITY_SETTINGS']['HOST']);
	define( 'APP', $_SERVER[ 'HTTP_HOST' ] == '127.0.0.1' ? $ini['URL_UTILITY_SETTINGS']['APP_LOCAL'] : $ini['URL_UTILITY_SETTINGS']['APP'] );
	define( 'HA_ROOT', ROOT. $ini['URL_UTILITY_SETTINGS']['HA_ROOT']);
	
	// auth utility settings
	define( 'COOKIE_DOMAIN', $_SERVER['HTTP_HOST'] );
	foreach($ini['AUTH_UTILITY_SETTINGS'] as $key => $value ){
		define( $key, $value);
	}
	
	define( 'LOGIN_URL', APP. 'login/' );
	define( 'LOGIN_REDIRECT', APP );
	define( 'LOGOUT_REDIRECT', APP );

	// db utility settings
	foreach($ini['DB_UTILITY_SETTINGS'] as $key => $value ){
		define( $key, $value);
	}
	
	$DATABASES = array(
		'default' => array( 
			'dsn' => 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME,
			'user' => DB_USER,
			'pass' => DB_PASS,
		),
		'socket' => array(
			'dsn' => 'mysql:unix_socket=/tmp/mysql.sock;dbname='.DB_NAME,
			'user' => DB_USER,
			'pass' => DB_PASS,
		),
	);
	
	// utility settings
	define( 'MEDIA_ROOT', ROOT. 'drive' .DIRECTORY_SEPARATOR );

?>

