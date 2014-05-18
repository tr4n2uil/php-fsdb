<?php
/**
 *	FSDB Models
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	require_once( PR_ROOT. 'db.php' );

	// map model
	class Map extends Model {
		var $id;
		var $key;
		var $value;
		var $owner;
		var $read;
		var $write;
		var $group;

		static $_table = 'data_map';
		static $_pk = 'key';
		static $_auth = 'GuardAuth';

		static $_refs = array(
			'owner' => array( 'User', 'id' ),
			'delegate' => array( 'Map', 'id' ),
		);

		// rest objects process
		public function obj_process($data){
			if(!$data) return $data;

			$value = json_decode($data['value'], true);
			if(isset($value['objects'])){
				$extra = array();
				foreach( $value['objects'] as $obj ){
					$extra[$obj] = self::obj_get($obj);
				}
				$data['extra'] = $extra;
			}

			if($data['group'])
				$data['group'] = self::obj_get($data['group']);
			
			return $data;
		}

		// override obj_create
		public static function obj_create( $data ){
			global $SM;
			$u = $SM->user_get();
			$data['owner'] = $u ? $u->id : null;

			return parent::obj_create($data);
		}
	}


	// group model
	class Group extends Model {
		var $id;
		var $map_id;
		var $user_id;

		static $_table = 'data_group';
		static $_pk = 'id';
		static $_refs = array(
			'user' => array( 'User', 'id' ),
			'map' => array( 'Map', 'id' ),
		);
	}


	define('GUARD_NONE', 0);
	define('GUARD_OWNER', 1);

	define('GUARD_GROUP', 2);
	define('GUARD_DELEGATE', 3);

	define('GUARD_SESSION', 4);
	define('GUARD_PUBLIC', 5);


	// guard authorization class
	class GuardAuth {
		private static function check_user(){
			global $SM;
			return $SM->user_get();
		}

		public static function read_list($model){
			return false;
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
