<?php
/**
 *	Database Object Manager Class
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	require_once( PR_ROOT. 'db/expression.php' );
	require_once( PR_ROOT. 'db/exception.php' );

	// class definition
	class DB implements IteratorAggregate  {
		private $_model;
		private $_result;
		private $_count;

		private $_select;
		private $_distinct;
		private $_where;
		private $_group;
		private $_having;
		private $_order;
		private $_low;
		private $_high;

		private $_dbkey;

		// constructor
		public function __construct( $model ){
			$this->_model = $model;
			$this->_result = false;
			$this->_count = 0;

			$this->_select = array();
			$this->_distinct = false;
			$this->_where = new EXPR();
			$this->_group = array();
			$this->_having = new EXPR();
			$this->_order = array();
			$this->_low = false;
			$this->_high = false;

			$this->_dbkey = 'default';
		}

		// iterator interface
		public function getIterator(){
			if( !is_array( $this->_result ) ){
				$this->select();
			}

			return new ArrayIterator( $this->_result );
		}

		// generate copy of self
		private function _clone(){
			$db = new DB( $this->_model );

			$db->_distinct = $this->_distinct;
			$db->_low = $this->_low;
			$db->_high = $this->_high;

			$db->_select = $this->_select;
			$db->_where = $this->_where;
			$db->_group = $this->_group;
			$db->_having = $this->_having;
			$db->_order = $this->_order;

			$db->_dbkey = $this->_dbkey;

			return $db;
		}

		public function query( $q, $subs ){
			$model = $this->_model;
			$q = str_replace( '{MODEL_TABLE}', $model::$_table, $q );

			// execute query
			$conn = db_get_connection( $this->_dbkey );
			$stmt = $conn->prepare( $q );
			$res = $stmt->execute( $subs );

			// check for error
			if( $res === false ){
				$error = $stmt->errorInfo();
				throw new Exception( ( DEBUG ? 'Error Executing Query: '. $q : '' ) .' Error: '.$error[ 2 ].' (Code '.$error[ 1 ].')', QUERYERROR );
			}
			
			// return result
			return $res;
		}

		// compile and run select query
		public function select(){
			// check for results in cache
			if( $this->_result )
				return $this->_result;

			// init variables
			$vars = array();
			$subs = array();
			$model = $this->_model;

			// add SELECT
			$q = array( "SELECT" );

			// add DISTINCT
			if( $this->_distinct ){
				array_push( $q, "DISTINCT" );
			}

			// add projection if none
			if( !$this->_select || in_array( '*', $this->_select ) ){
				foreach( array_keys( get_class_vars( $model ) ) as $k )
					if( $k[ 0 ] != '_' )
						$this->_select[] = $k;

				if( in_array( '*', $this->_select ) )
					unset( $this->_select[ array_search( '*', $this->_select ) ] );
			}

			// add pk in select if not exists
			if( !in_array( $model::$_pk, $this->_select ) ){
				$this->_select[] = $model::$_pk;
			}

			// add select keys to vars
			foreach( $this->_select as $e ){
				$vars[ $e ] = false;
			}

			// add where keys to vars
			foreach( $this->_where->vars() as $e ){
				$vars[ $e ] = false;
			}

			// add group keys to vars
			foreach( $this->_group as $e ){
				$vars[ $e ] = false;
			}

			// add having keys to vars
			foreach( $this->_having->vars() as $e ){
				$vars[ $e ] = false;
			}

			// add order keys to vars
			foreach( $this->_order as $e ){
				$vars[ $e ] = false;
			}

			// setup joins
			$tables = $this->_setup_joins( $vars );

			// add projections and FROM tables
			if( $this->_select ){
				$qs = array();
				foreach( $this->_select as $e ){
					array_push( $qs, $vars[ $e ].' AS `'. $e.'`' );
				}
				array_push( $q, implode( ', ', $qs ), 'FROM', $tables );	
			}
			
			// add WHERE expression
			if( $this->_where ){
				$qs = $this->_where->sql( $vars, $subs );
				if( $qs )
					array_push( $q, 'WHERE', $qs );	
			}
			
			// add GROUP BY expression
			if( $this->_group ){
				$qs = array();
				foreach( $this->_group as $e ){
					array_push( $qs, $vars[ $e ] );
				}
				array_push( $q, 'GROUP BY', implode( ', ', $qs ) );
			}

			// add HAVING expression
			if( $this->_having ){
				$qs = $this->_having->sql( $vars, $subs );
				if( $qs )
					array_push( $q, 'HAVING', $qs );
			}

			// add ORDER BY columns
			if( $this->_order ){
				$qs = array();
				foreach( $this->_order as $e ){
					array_push( $qs, $vars[ $e ] );
				}
				array_push( $q, 'ORDER BY', implode( ', ', $qs ) );	
			}

			// add LIMIT OFFSET
			if( $this->_low !== false && $this->_high !== false ){
				$limit = $this->_high - $this->_low;
				array_push( $q, 'LIMIT', $limit, 'OFFSET', $this->_low );
			}

			// form query
			$q = implode( ' ', $q );

			//print_r( $q );

			// execute query
			$conn = db_get_connection( $this->_dbkey );
			$stmt = $conn->prepare( $q );
			$res = $stmt->execute( $subs );

			// check for errors
			if( $res === false ){
				$error = $stmt->errorInfo();
				throw new Exception( 'Error Executing Query: '. $q. ' Error: '.$error[ 2 ].' (Code '.$error[ 1 ].')', QUERYERROR );
			}
			
			// cache results
			$this->_result = array();

			// create objects from results
			foreach( $stmt->fetchAll( PDO::FETCH_ASSOC ) as $row ){
				$this->_result[] = new $model( $row );
				$this->_count++;
			}

			// return result
			return $this->_result;
		}

		// setup joins helper
		private function _setup_joins( &$vars ){
			// initialize
			$model = $this->_model;
			$tables = array( '`'. $model::$_table. '` T1' );
			$joins = array();
			$i = 1;

			foreach( $vars as $var => $e ){
				// parse field and joiner
				$pieces = explode( '__', $var );
				$field = array_pop( $pieces );

				if( $pieces ){
					$join = implode( '__', $pieces );

					// check for joiner existence
					if( !isset( $joins[ $join ] ) ){
						// delegate to model to setup join
						$model::setup_join( $join, $pieces, $i, $joins, $tables );
					}
					
					// use joiner to get field scope
					$vars[ $var ] = $joins[ $join ]. '.`'. $field .'`';
				}
				else {
					// use default scope T1
					$vars[ $var ] = 'T1.`'. $field .'`';
				}
			}

			// return table joins
			return implode( ' INNER JOIN ', $tables );
		}

		// compile and run insert query
		public function insert( $set ){
			// init variables
			$model = $this->_model;
			$vars = array();
			$subs = array();

			// add INSERT INTO tables (
			$q = array( "INSERT INTO", '`'.$model::$_table.'`', '(', );

			// add projections ) VALUES (
			$qs = array();
			foreach( array_keys( $set ) as $k )
				array_push( $qs, '`'. $k.'`' );
			array_push( $q, implode( ', ', $qs ), ')', 'VALUES', '(' );

			// add values )
			$qs = array();
			foreach( $set as $k => $v ){
				array_push( $qs, '?' );
				$subs[] = $v;
			}
			array_push( $q, implode( ', ', $qs ), ')' );
			
			// form query
			$q = implode( ' ', $q );

			// execute query
			$conn = db_get_connection( $this->_dbkey );
			$stmt = $conn->prepare( $q );
			$res = $stmt->execute( $subs );

			// check for error
			if( $res === false ){
				$error = $stmt->errorInfo();
				throw new Exception( ( DEBUG ? 'Error Executing Query: '. $q : '' ) .' Error: '.$error[ 2 ].' (Code '.$error[ 1 ].')', QUERYERROR );
			}
			
			// return result
			return $conn->lastInsertId();
		}

		// compile and run update query
		public function update( $set ){
			if( $set ){

				// init variables
				$model = $this->_model;
				$vars = array();
				$subs = array();
				
				// add UPDATE table SET
				$q = array( "UPDATE", '`'.$model::$_table.'`', 'SET' );

				// add where keys to vars
				foreach( $this->_where->vars() as $e ){
					$vars[ $e ] = '`'.$e.'`';
				}

				// add var=expr
				$qs = array();
				foreach( $set as $k => $v ){
					if( is_a( $v, 'EXPR' ) ){
						$vars[ $k ] = '`'.$k.'`';
						array_push( $qs, $v->sql( $vars, $subs ) );
					}
					else {
						array_push( $qs, '`'.$k.'`=?' );
						$subs[] = $v;	
					}
				}
				array_push( $q, implode( ', ', $qs ) );	
			
				// add WHERE expression
				if( $this->_where ){
					$qs = $this->_where->sql( $vars, $subs );
					if( $qs )
						array_push( $q, 'WHERE', $qs );	
				}
				
				// form query
				$q = implode( ' ', $q );

				// execute query
				$conn = db_get_connection( $this->_dbkey );
				$stmt = $conn->prepare( $q );
				$res = $stmt->execute( $subs );

				// check for errors
				if( $res === false ){
					$error = $stmt->errorInfo();
					throw new Exception( 'Error Executing Query: '. $q. ' Error: '.$error[ 2 ].' (Code '.$error[ 1 ].')', QUERYERROR );
				}
				
				// return result
				return $res;
			}
		}

		// compile and run delete query
		public function delete(){
			// init variables
			$model = $this->_model;
			$vars = array();
			$subs = array();

			// add DELETE FROM table
			$q = array( "DELETE FROM", '`'.$model::$_table.'`' );

			// add where keys to vars
			foreach( $this->_where->vars() as $e ){
				$vars[ $e ] = '`'.$e.'`';
			}

			// add WHERE expression
			if( $this->_where ){
				$qs = $this->_where->sql( $vars, $subs );
				if( $qs )
					array_push( $q, 'WHERE', $qs );	
			}
			
			// form query
			$q = implode( ' ', $q );

			// execute query
			$conn = db_get_connection( $this->_dbkey );
			$stmt = $conn->prepare( $q );
			$res = $stmt->execute( $subs );

			// check for errors
			if( $res === false ){
				$error = $stmt->errorInfo();
				throw new Exception( 'Error Executing Query: '. $q. ' Error: '.$error[ 2 ].' (Code '.$error[ 1 ].')', QUERYERROR );
			}
			
			// return result
			return $res;
		}

		// helper to create new instance of model in db
		public function create( $args = array() ){
			$obj = new $this->_model( $args );
			return $obj->save( true );
		}

		// helper to create unique instance of model in db
		function get( $where ){
			$db = $this->filter( $where );
			$db->select();

			if( $db->_count == 0 )
				throw new Exception( 'Does Not Exist', DOESNOTEXIST );

			if( $db->_count != 1 )
				throw new DBMultipleObjectsReturned( 'Multiple Rows Returned', NOTUNIQUE );

			return $db->_result[ 0 ];
		}

		// helper to count the result set rows
		function count(){
			if( $this->result === false ){
				$db = $this->_clone();
				$db->select();
				return $db->_count;
			}

			return $this->_count;
		}

		// add projection columns
		function only( $select ){
			$db = $this->_clone();

			$db->_select = $select;
			return $db;
		}

		// add projection columns
		function values( $select ){
			$db = $this->_clone();

			$db->_select = $select;
			return $db;
		}

		// add where expressions
		function filter( $where ){
			$db = $this->_clone();

			if( is_array( $where ) )
				$where = new EXPR( $where );

			$db->_where = $where;
			return $db;
		}

		// set limits
		function limit( $low, $high ){
			$db = $this->_clone();

			$db->_low = $low;
			$db->_high = $high;
			return $db;
		}

		// set order by columns
		function order_by( $order ){
			$db = $this->_clone();

			$db->_order = $order;
			return $db;
		}

		// set db key
		function using( $dbkey ){
			$this->_dbkey = $dbkey;
			return $this;
		}

	}

?>
