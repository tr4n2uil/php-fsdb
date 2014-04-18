<?php
/**
 *	Session Models
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	require_once( PR_ROOT. 'db.php' );
	require_once( PR_ROOT. 'util.php' );
	require_once( PR_ROOT. 'auth/exception.php' );

	// user model
	class User extends Model {
		var $id;
		var $username;
		var $passwd;
		var $email;
		var $name;
		var $title;
		var $phone;
		var $gender;
		var $dob;
		var $verify;
		var $expiry;
		var $data;
		var $profile;
		var $photo;
		var $address;
		var $country;
		var $region;
		var $city;
		var $zip;
		var $google;
		var $facebook;
		var $linkedin;
		var $twitter;
		var $stg_used;
		var $stg_max;

		static $_table = 'auth_users';
		static $_pk = 'id';

		static $_excludes = array( 'passwd', 'verify', 'expiry', 'data', 'google', 'facebook', 'linkedin', 'twitter', 'stg_used', 'stg_max' );

		// check password
		public function check_passwd( $passwd ){
			return $passwd && $this->passwd == md5( $this->username. $passwd );
		}

		// set password
		public function set_passwd( $passwd ){
			if( $passwd ){
				$this->passwd = md5( $this->$username. $passwd );
				$this->save();	
			}
		}

		// login helper
		public static function login( $username, $passwd, $next = LOGIN_REDIRECT ){
			global $SM;

			// authenticate
			$u = unique_object( 'User', array( 'username' => $username, 'passwd' => md5( $username. $passwd ) ) );

			// check for error
			if( !$u )
				throw new AuthInvalidCredentials( 'Invalid Credentials' );

			// save session with new id
			$SM->user_set( $u );
			$_SESSION[ 'user_id' ] = $u->id;

			// redirect after login
			http_redirect( $next );
		}

		// hybrid auth social login
		public static function social_login( $provider, $next ){
			global $SM;

			$config = ROOT . 'core/hybridauth_config.php';
			require_once( HA_ROOT. "hybridauth/Hybrid/Auth.php" );

			try{
				// create an instance for Hybridauth with the configuration file path as parameter
				$hybridauth = new Hybrid_Auth( $config );

				// call back the requested provider adapter instance 
				$adapter = $hybridauth->authenticate( $provider );
				
				if( !$adapter->isUserConnected() )
					throw new AuthHAError( 'Social Authentication Failed' );

				// grab the user profile
				$user_data = $adapter->getUserProfile();

				// find identifier
				$identifier = $user_data->identifier;
				if( !$identifier )
					throw new AuthHAError( 'Identity Information Not Obtained' );

				// find user object by identifier
				$u = unique_object( 'User', array( $provider => $identifier ) );
				if( !$u ){
					// find user object by email
					$email = $user_data->email;
					if( $email ){
						$u = unique_object( 'User', array( 'email' => $email ) );
					}

					// create new user object
					if( !$u ){
						// generate username
						if( $email ){
							$username = explode( '@', $email );
							list( $username, $repeat ) = unique_alias( 'User', $username[ 0 ], array(), 'username' );
							$u = self::objects()->create( array( 'email' => $email, 'username' => $username, 'repeat' => $repeat, $provider => $identifier ) );	
						}
						else {
							$username = explode( '@', $identifier );
							list( $username, $repeat ) = unique_alias( 'User', $username[ 0 ], array(), 'username' );
							$u = self::objects()->create( array( $provider => $identifier, 'username' => $username, 'repeat' => $repeat ) );
						}
						
						// force sync
						$u->sync( $provider, $user_data, true );
					}
					else {
						// save identifier
						$u->set( $provider, $identifier );
						$u->sync( $user_data );
					}
				}
				else {
					// normal sync
					$u->sync( $user_data );
				}

				// save session with new id
				$SM->user_set( $u );
				$_SESSION[ 'user_id' ] = $u->id;

				// redirect after login
				unset( $_SESSION[ 'next' ] );
				http_redirect( $next );
			}
			catch( Exception $e ){
				switch( $e->getCode() ){ 
					case 0 : $error = "Unspecified error."; break;
					case 1 : $error = "Hybriauth configuration error."; break;
					case 2 : $error = "Provider not properly configured."; break;
					case 3 : $error = "Unknown or disabled provider."; break;
					case 4 : $error = "Missing provider application credentials."; break;
					case 5 : $error = "Authentication failed. The user has canceled the authentication or the provider refused the connection."; break;
					case 6 : $error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again."; 
						     $adapter->logout(); 
						     break;
					case 7 : $error = "User not connected to the provider."; 
						     $adapter->logout(); 
						     break;
				} 

				//if( DEBUG ){
					$error .= "<br /><br /><b>Original error message:</b> " . $e->getMessage(); 
					$error .= "<hr /><pre>Trace:<br />" . $e->getTraceAsString() . "</pre>";	
				//}

				throw new Exception( $error, ENDPOINTERROR );
			}
		}

		// sync data from social auth
		public function sync( $profile, $force = false ){
			if( $profile->profileURL && ( !$this->profile || ( $force && $profile->profileURL != $this->profile ) ) ){
				$this->set( 'profile', $profile->profileURL );
			}

			if( $profile->photoURL && ( !$this->photo || ( $force && $profile->photoURL != $this->photo ) ) ){
				$this->set( 'photo', $profile->photoURL );
			}

			if( $profile->firstName && ( !$this->name || ( $force && $profile->firstName. ' '. $profile->lastName != $this->name ) ) ){
				$this->set( 'name', $profile->firstName. ' '. $profile->lastName );
			}

			if( $profile->description && ( !$this->title || ( $force && $profile->description != $this->title ) ) ){
				$this->set( 'title', $profile->description );
			}

			if( $profile->gender && ( !$this->gender || ( $force && substr( ucfirst( $profile->gender ), 0, 1 ) != $this->gender ) ) ){
				$this->set( 'gender', substr( ucfirst( $profile->gender ), 0, 1 ) );
			}

			if( $profile->birthDay && ( !$this->dob || ( $force && $profile->birthYear.'-'.$profile->birthMonth.'-'.$profile->birthDay != $this->dob ) ) ){
				$this->set( 'dob', $profile->birthYear.'-'.$profile->birthMonth.'-'.$profile->birthDay );
			}

			if( $profile->phone && ( !$this->phone || ( $force && $profile->phone != $this->phone ) ) ){
				$this->set( 'phone', $profile->phone );
			}

			if( $profile->address && ( !$this->address || ( $force && $profile->address != $this->address ) ) ){
				$this->set( 'address', $profile->address );
			}

			if( $profile->country && ( !$this->country || ( $force && $profile->country != $this->country ) ) ){
				$this->set( 'country', $profile->country );
			}

			if( $profile->region && ( !$this->region || ( $force && $profile->region != $this->region ) ) ){
				$this->set( 'region', $profile->region );
			}

			if( $profile->city && ( !$this->city || ( $force && $profile->city != $this->city ) ) ){
				$this->set( 'city', $profile->city );
			}

			if( $profile->zip && ( !$this->zip || ( $force && $profile->zip != $this->zip ) ) ){
				$this->set( 'zip', $profile->zip );
			}

			return $this->save();
		}
	}

	// session model
	class Session extends Model {
		var $id;
		var $user_id;
		var $expiry;
		var $active;
		var $data;

		private $_data;
		private $_user;

		static $_table = 'auth_sessions';
		static $_pk = 'id';
		static $_refs = array(
			'user' => array( 'User', 'id' ),
		);

		static $_excludes = array( 'expiry', 'data' );

		// initialize user and session data
		public function __construct( $array = array() ){
			parent::__construct( $array );

			$this->_user = null;
			$data = $this->data = isset( $array[ 'data' ] ) ? $array[ 'data' ] : '{"phpsessiondata":""}';

			if( isset( $array[ 'user_id' ] ) ){
				$this->_user = unique_object( 'User', array( 'id' => $array[ 'user_id' ] ) );
				if( $this->_user )
					$data = $this->_user->data;
			}

			$this->_data = json_decode( $data, true );
		}

		// get session data
		public function data_get( $key ){
			return $this->_data[ $key ];
		}

		// set session data
		public function data_set( $key, $value ){
			$this->_data[ $key ] = $value;
		}

		// get user object
		public function user_get(){
			return $this->_user;
		}

		// set user object
		public function user_set( $u ){
			$this->_user = $u;
			$this->user_id = $u->id;
		}

		// save session object
		public function save( $force_insert = false ){
			if( !$this->id or $force_insert ){
				// deactivate old session
				if( $this->id ){
					$this->set( 'active', 0 );
					parent::save();
				}

				// regenerate session id
				$this->set( 'id', $this->generate_id() );
				$this->set( 'expiry', date( "Y-m-d H:i:s", strtotime( date( "Y-m-d H:i:s" ). '+'. COOKIE_EXPIRY. ' days' ) ) );
				$this->set( 'active', 1 );

				session_id( $this->id );

				// delete old cookie
				if( !setcookie( session_name(), false, strtotime( date( "Y-m-d H:i:s" ) ) - 5000, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTPONLY ) )
					throw new Exception( 'Unable to set cookie header', COOKIEERROR );

				// set new cookie
				if( !setcookie( session_name(), $this->id, strtotime( $this->expiry ), COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTPONLY ) )
					throw new Exception( 'Unable to set cookie header', COOKIEERROR );
			}

			// extract session data
			if( $this->_user ){
				$this->_user->set( 'data', json_encode( $this->_data ) );
				$this->_user->save();
			}
			else {
				$this->set( 'data', json_encode( $this->_data ) );
			}
			
			// save object
			parent::save( $force_insert );
			return $this;
		}

		// generate random session id
		private function generate_id(){
			return random_string( 54 ). time();
		}

		// logout helper
		public function logout(){
			if( $this->id ){
				$this->set( 'active', 0 );
				$this->save();
			}

			$_SESSION = array();
			session_destroy();
		}

		// rest objects list
		public static function obj_query(){
			global $SM;
			$u = $SM->user_get(); 

			if( !$u ){
				throw new Exception( 'Invalid Session', INVALIDCREDENTIALS );
			}
			
			return $u->obj_serialize();
		}

		// rest object create
		public static function obj_create( $data ){
			global $SM;

			// get user
			$u = unique_object( 'User', array( 'username' => $data[ 'username' ] ) );
			if( !$u )
				throw new Exception( 'Invalid User', INVALIDCREDENTIALS );
			
			if( !$u->check_passwd( $data[ 'password' ] ) )
				throw new Exception( 'Invalid Credentials', INVALIDCREDENTIALS );

			// save session with new id
			$SM->user_set( $u );
			$_SESSION[ 'user_id' ] = $u->id;

			return $u->obj_serialize();
		}

		// rest object delete
		public static function obj_delete( $pkval ){
			global $SM;
			$SM->logout();

			return false;
		}
	}


?>
