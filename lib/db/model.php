<?php
/**
 *	Database Object Model Abstract Base Class
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	// basic authorization class
	class Auth {
		public static function read_list($model){
			return true;
		}

		public static function read_detail($model, $pkval){
			return true;
		}

		public static function obj_create($model){
			return true;
		}

		public static function obj_update($model, $pkval){
			return true;
		}

		public static function obj_delete($model, $pkval){
			return true;
		}
	}
	

	// model definition
	abstract class Model {
		// objects manager
		private static $_objects;

		// meta settings for model
		public static $_table = 'model';
		public static $_pk = 'id';

		// meta settings for rest
		public static $_excludes = array();

		// private variables
		private $_loaded = array();
		private $_changed = array();
		private $_extra = array();

		// authorization class
		public static $_auth = "Auth";

		// constructor
		public function __construct( $array = array() ){
			self::$_objects = $_objects = array();

			foreach( $array as $k => $v ){
				// check if property
				if( property_exists( get_called_class(), $k ) ){
					$this->$k = $v;
					// add to loaded
					$this->_loaded[] = $k;
				}
				// add to extra
				else
					$this->_extra[ $k ] = $v;
			}
		}

		// setter for editing properties
		public function set( $name, $value ) {
			if( $this->$name != $value )
				$this->$name = $value;
				$this->_changed[ $name ] = true;
		}

		// getter for extra fields
		public function get( $key ){
			return isset( $this->_extra[ $key ] ) ? $this->_extra[ $key ] : null;
		}

		// save or create as per pk
		public function save( $force_insert = false ){
			$pk = self::$_pk;
			$pkval = $this->$pk;
			$db = self::objects();
			$args = array();

			// check for insert
			if( !$pkval or $force_insert ){
				// collect args
				foreach( get_object_vars( $this ) as $k => $v )
					if( $k[ 0 ] != '_' && $v != null )
						$args[ $k ] = $v;
				
				// insert into db
				$newpkval = $db->insert( $args );
				if( $pkval )
					return $db->get( array( $pk => $pkval ) );
				elseif( $newpkval )
					return $db->get( array( $pk => $newpkval ) );
				else
					return $db->get( $args );
			}
			else {	
				// collect args
				foreach( $this->_changed as $k => $v )
					if( in_array( $k, $this->_loaded ) )
						$args[ $k ] = $this->$k;

				// update in db
				$db->filter( array( $pk => $pkval ) )->update( $args );
				$this->_changed = array();
				return $this;
			}
		}

		// delete object
		public function delete(){
			$pk = self::$_pk;
			$pkval = $this->$pk;

			// delete from db
			$db = self::objects()->filter( array( $pk => $pkval ) );
			$db->delete();
			
			// return stale object
			return $this;
		}

		// objects manager helper
		public static function objects(){
			$cls = get_called_class();
			if( !isset( self::$_objects[ $cls ] ) ){
				self::$_objects[ $cls ] = new DB( $cls );
			}

			return self::$_objects[ $cls ];
		}

		// join setup helper in model
		public static function setup_join( $join, $pieces, &$i, &$joins, &$tables ){
			$cls = get_called_class();
			$j = $i;

			while( $pieces ){
				// find reference
				$field = array_pop( $pieces );	

				if( !isset( $cls::$_refs[ $field ] ) ){
					$ifield = $cls::$_pk;

					$cls = ucfirst( $field );
					$jfield = $cls::$_pk;
					
				}
				else {
					$ref = $cls::$_refs[ $field ];
					$cls = $ref[ 0 ];

					$jfield = $ref[ 1 ];
					$ifield = $field.'_id';
				}

				// init variables Ti
				$j = $i;
				$i++;

				// append join to tables
				$tables[] = '`'.$cls::$_table.'` T'.$i." ON ( T$i.`$jfield` = T$j.`$ifield` )";
			}

			// cache Ti variable into joins
			$joins[ $join ] = "T$i";
		}

		// rest objects serialize
		public function obj_serialize(){
			$data = array();
			$cls = get_called_class();
			foreach( get_object_vars( $this ) as $k => $v )
				if( $k[ 0 ] != '_' && !in_array( $k, $cls::$_excludes ) )
					$data[ $k ] = $v;

			return $data;
		}

		// rest objects list
		public static function obj_query(){
			$data = array();
			$cls = get_called_class();

			$auth = $cls::$_auth;
			if( $auth::read_list($cls) ){
				foreach( $cls::objects() as $obj ){
					$data[] = $obj->obj_serialize();
				}
			}

			return array( 'objects' => $data );
		}

		// rest objects list
		public static function obj_get( $pkval ){
			$cls = get_called_class();

			$auth = $cls::$_auth;
			if( $auth::read_detail($cls, $pkval) ){
				$pk = $cls::$_pk;
				$obj = $cls::objects()->get( array( $pk => $pkval ) );
				return $obj->obj_serialize();
			}
			
			return None;
		}

		// rest object create
		public static function obj_create( $data ){
			$cls = get_called_class();

			$auth = $cls::$_auth;
			if( $auth::obj_create($cls) ){
				$obj = $cls::objects()->create( $data );
				return $obj->obj_serialize();
			}

			return None;
		}

		// rest object update
		public static function obj_update( $pkval, $data ){
			$cls = get_called_class();
			$pk = $cls::$_pk;

			$auth = $cls::$_auth;
			if( $auth::obj_update($cls, $pkval) ){
				$obj = $cls::objects()->get( array( $pk => $pkval ) );

				foreach( $data as $k => $v )
					$obj->set( $k, $v );

				$obj->save();
				return $obj->obj_serialize();
			}

			return None;
		}

		// rest object delete
		public static function obj_delete( $pkval ){
			$cls = get_called_class();
			$pk = $cls::$_pk;

			$auth = $cls::$_auth;
			if( $auth::obj_delete($cls, $pkval) ){
				$obj = $cls::objects()->get( array( $pk => $pkval ) );

				$obj->delete();
				return false; //$obj->obj_serialize();
			}

			return None;
		}
	}

?>