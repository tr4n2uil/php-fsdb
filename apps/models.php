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

	// note model
	class Note extends Model {
		var $id;
		var $name;
		var $desc;

		static $_table = 'demo_note';
		static $_pk = 'id';
	}
	

?>
