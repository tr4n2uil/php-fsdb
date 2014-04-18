<?php
/**
 *	Sample Project Models
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
		var $name;
		var $value;

		static $_table = 'data_map';
		static $_pk = 'name';
		static $_auth = 'SessionAuth';
	}
	
?>
