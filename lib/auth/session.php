<?php
/**
 *	Auth Session Managers and Helpers
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	require_once( PR_ROOT. 'auth/models.php' );

	// initialize cookie session
	function session_cookie( $session_name = COOKIE_NAME ){
		$session = false;
		if( isset( $_COOKIE[ $session_name ] ) ){
			$session = unique_object( 'Session', array( 'id' => $_COOKIE[ $session_name ], 'active' => 1 ) );
		}

		if( !$session ){
			$session = Session::objects()->create();
		}

		return $session;
	}

	// session manager class
	class SessionManager {
		private $session;

		// open session
		public function open( $save_path, $session_name ){
			$this->session = session_cookie( $session_name );
		}

		// close session
		public function close() {

		}

		// read session
		public function read( $sid ){
			return $this->session->data_get( 'phpsessiondata' );
		}

		// write session
		public function write( $sid, $data ){
			$this->session->data_set( 'phpsessiondata', $data );
			$this->session->save();

		}

		// destroy session
		public function destroy( $id ){
			$this->session->set( 'active', 0 );
			$this->session->save();

			if( !setcookie( session_name(), false, strtotime( date( "Y-m-d H:i:s" ) ) - 15000, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTPONLY ) )
				throw new AuthSetCookieError( 'Unable to set cookie header' );
		}

		// clean sessions
		public function clean( $delta ){

		}

		// get user
		public function user_get(){
			return $this->session->user_get();
		}

		// set user
		public function user_set( $u ){
			$this->session->user_set( $u );
			$this->session->save( true );
		}

		// logout session
		public function logout(){
			$this->session->logout();
		}
	}

	// SessionManager instance
	$SM = false;

	// initialize php session
	function session_init(){
		global $SM;

		if( !$SM ){
			$sm = new SessionManager();

			session_set_save_handler( 
				array( &$sm, 'open' ), 
				array( &$sm, 'close' ), 
				array( &$sm, 'read' ),
				array( &$sm, 'write' ),
				array( &$sm, 'destroy' ),
				array( &$sm, 'clean' )
			);

			session_name( COOKIE_NAME );
			if( !session_start() )
				throw new AuthPHPSessionError( 'Unable to start php session' );

			$SM = $sm;
		}
	}

	// check logged in
	function session_user(){
		global $SM;
		$u = $SM->user_get();
		
		if( !$u ){
			$next = $_SERVER[ 'REQUEST_URI' ];
			$arg = array();

			if( $next != LOGIN_REDIRECT )
				$arg[ 'next' ] = $next;

			http_redirect( LOGIN_URL, $arg );
		}
		
		return $u;
	}

	// initialize sessions
	session_init();

	// session authorization class
	class SessionAuth {
		private static function check_user(){
			global $SM;
			$u = $SM->user_get();
			return $u ? true : false;
		}

		public static function read_list($model){
			return self::check_user();
		}

		public static function read_detail($model, $pkval){
			return self::check_user();
		}

		public static function obj_create($model){
			return self::check_user();
		}

		public static function obj_update($model, $pkval){
			return self::check_user();
		}

		public static function obj_delete($model, $pkval){
			return self::check_user();
		}
	}

?>
