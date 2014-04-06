<?php
/**
 *	Query Expression Classes
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	// class definition
	class EXPR {
		protected $_array;
		public static $_delimiter = ' AND ';

		// constructor
		public function __construct( $array = array() ){
			$this->_array = $array;
		}

		// collect keys
		public function vars(){
			return array_keys( $this->_array );
		}

		// generate sql
		public function sql( $vars, &$subs ){
			$q = array();

			foreach( $this->_array as $key => $value ) {
				if( is_a( $value, 'EXPR' ) ){
					// recurse
					array_push( $q, $value->sql( $vars, $subs ) );
				}
				else {
					// add to subs
					$subs[] = $value;
					array_push( $q, $vars[ $key ]. '= ?' );
				}
			}

			$cls = get_called_class();
			return implode( $cls::$_delimiter, $q );
		}
	}

	// class definition
	class Q_OR extends EXPR {
		public static $_delimiter = ' OR ';
	}

	// class definition
	class Q_NOT extends EXPR {
		public static $_delimiter = ' AND NOT ';

		// generate sql
		public function sql( $vars, &$subs ){
			return 'NOT '.parent::sql( $vars, $subs );
		}
	}

	// class definition
	class Q_IN extends EXPR {
		public static $_delimiter = ' AND ';

		// generate sql
		public function sql( $vars, &$subs ){
			$q = array();

			foreach( $this->_array as $key => $value ) {
				$qi = array();

				foreach( $value as $k => $v ){
					array_push( $qi, $v );
				}

				array_push( $q, $vars[ $key ]. ' IN ( '. implode( ', ', $qi ) .' )' );
			}

			$cls = get_called_class();
			return implode( $cls::$_delimiter, $q );
		}
	}

	// class definition
	class Q_FTS extends EXPR {
		public static $_delimiter = ', ';

		// collect keys
		public function vars(){
			return $this->_array[ 'match' ];
		}

		// generate sql
		public function sql( $vars, &$subs ){

			$subs[] = $this->_array[ 'value' ];

			$q = array();

			foreach( $this->_array[ 'match' ] as $k => $v ) {
				array_push( $q, $vars[ $v ] );
			}

			$mode = isset( $this->_array[ 'mode' ] ) ? ( $this->_array[ 'mode' ] == 'BOOLEAN' ? ' IN BOOLEAN MODE' : ( $this->_array[ 'mode' ] == 'QUERYEXP' ? ' WITH QUERY EXPANSION' : '' ) ) : '';

			$cls = get_called_class();
			return 'MATCH ('. implode( $cls::$_delimiter, $q ). ') AGAINST (?' .$mode .')';
		}
	}

	// class definition
	class Q_LIKE extends EXPR {
		public static $_delimiter = ' AND ';

		// generate sql
		public function sql( $vars, &$subs ){
			$q = array();

			foreach( $this->_array as $key => $value ) {
					// add to subs
				$subs[] = $value;
				array_push( $q, $vars[ $key ]. " LIKE CONCAT( '%', ?, '%' )" );
			}

			$cls = get_called_class();
			return implode( $cls::$_delimiter, $q );
		}
	}

	// class definition
	class Q_START extends EXPR {
		public static $_delimiter = ' AND ';

		// generate sql
		public function sql( $vars, &$subs ){
			$q = array();

			foreach( $this->_array as $key => $value ) {
				// add to subs
				$subs[] = $value;
				array_push( $q, $vars[ $key ]. " LIKE CONCAT( ?, '%' )" );
			}

			$cls = get_called_class();
			return implode( $cls::$_delimiter, $q );
		}
	}

	// class definition
	class F_EQ extends EXPR {
		protected $_key;
		protected $_value;

		// constructor
		public function __construct( $key, $value ){
			$this->key = $key;
			$this->value = $value;
		}

		// generate sql
		public function sql( $vars, &$subs ){
			$subs[] = $this->value;
			return $vars[ $this->key ].'= ?';
		}
	}

	// class definition
	class F_INC extends F_EQ {
		
		// generate sql
		public function sql( $vars, &$subs ){
			$subs[] = $this->value;
			return $vars[ $this->key ].'='.$vars[ $this->key ].' + ?';
		}
	}

	// class definition
	class F_DEC extends F_EQ {
		
		// generate sql
		public function sql( $vars, &$subs ){
			$subs[] = $this->value;
			return $vars[ $this->key ].'='.$vars[ $this->key ].' - ?';
		}
	}

?>