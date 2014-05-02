<?php
/**
 *	JSONDB Models
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	require_once( PR_ROOT. 'db.php' );
	require_once( PR_ROOT. 'auth/session.php' );

	// map model
	class Map extends Model {
		var $id;
		var $key;
		var $value;

		static $_table = 'data_map';
		static $_pk = 'key';
		static $_auth = 'SessionAuth';

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
			
			return $data;
		}
	}
	
?>
