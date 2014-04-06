<?php
/**
 *	DB Connection Helpers
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	// initialize and get connection by db key
	function db_get_connection( $key = 'default' ){
		global $DATABASES;

		// check for key
		if( !isset( $DATABASES[ $key ] ) )
			throw new DBImproperlyConfigured( 'Configuration Not Found For DB: '. $key );

		$db = $DATABASES[ $key ];

		// check if connection exists
		if( !isset( $db[ 'conn' ] ) ){
			// create pdo connection
			try {
				$conn = new PDO( $db[ 'dsn' ], $db[ 'user' ], $db[ 'pass'] );
				$db[ 'conn' ] = $conn;
			}
			catch( PDOException $e ) {
				throw new DBConnectionFailed( 'Connection failed: ' . $e->getMessage() );
			}
		}

		// return connection
		return $conn;
	}

	// close and reset connection by db key
	function db_close_connection( $key = 'default' ){
		global $DATABASES;

		// check for key
		if( !isset( $DATABASES[ $key ] ) )
			throw new DBImproperlyConfigured( 'Configuration Not Found For DB: '. $key );

		$db = $DATABASES[ $key ];

		// close if connection exists
		if( isset( $db[ 'conn' ] ) ){
			$db[ 'conn' ] = null;
			unset( $db[ 'conn' ] );
		}
	}

?>