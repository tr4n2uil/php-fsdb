<?php
/**
 *	Single File Handler
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	require_once( PR_ROOT. 'auth/session.php' );
	require_once( PR_ROOT. 'db.php' );
	require_once( PR_ROOT. 'util.php' );
	

	// user model
	class File extends Model {
		var $id;
		var $owner_id;
		var $alias;
		var $repeat;
		var $ext;
		var $name;
		var $path;
		var $title;
		var $size;
		var $mime;
		var $type;

		static $_table = 'storage_files';
		static $_pk = 'id';
	}


	// file save helper
	function save_file( $PATH = false ){
		$PATH = $PATH ? $PATH : MEDIA_ROOT;

		if( isset( $_FILES[ 'file' ] ) && $_FILES[ 'file' ][ 'size' ] ){
			$file = $_FILES[ 'file' ];
			$u = session_user();

			$PATH .= ( $u->username .strftime( "/%Y/%m/%d/" ) );
			if( !is_dir( $PATH ) )
				mkdir( $PATH, 0777, true );

			$name = $file[ 'name' ];
			$parts = explode( '.', $name );
			$ext = array_pop( $parts );
			$alias = implode( '.', $parts );

			$size = $file[ 'size' ];

			if( isset( $_POST[ 'id' ] ) ){
				$f = File::objects()->get( array( 'id' => $_POST[ 'id' ] ) );

				if( $f->owner_id != $u->id ){
					$ERROR = 'Not Authorized';
					require_once( 'error.php' );
					exit();
				}

				$PATH = $f->path;
				$used = $u->stg_used - $f->size + $size;
			}
			else {
				list( $alias, $repeat ) = unique_alias( 'File', $alias );
				$f = File::objects()->create( array( 'alias' => $alias, 'ext' => $ext, 'repeat' => $repeat, 'owner_id' => $u->id ) );

				$PATH .= $alias.'.'.$ext;
				$used = $u->stg_used + $size;
			}

			if( $used > $u->stg_max ){
				$ERROR = 'No Storage Left';
				require_once( 'error.php' );
				exit();
			}

			move_uploaded_file( $file[ 'tmp_name' ], $PATH );

			$f->set( 'name', $file[ 'name' ] );
			$f->set( 'title', $file[ 'name' ] );
			$f->set( 'path', $PATH );
			$f->set( 'mime', $file[ 'type' ] );
			$f->set( 'size', $file[ 'size' ] );
			$f->save();

			$u->set( 'stg_used', $used );
			$u->save();

			return $f;
		}
	}


?>

