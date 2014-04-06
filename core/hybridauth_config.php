<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

$config = parse_ini_file( ROOT."/core/".CONF_INI.".conf.ini", true);
return 
	array(
		"base_url" => HOST. APP. 'auth/', 

		"providers" => array ( 
			// openid providers
			"openid" => array (
				"enabled" => false
			),

			"yahoo" => array ( 
				"enabled" => $config['HYBRIDAUTH_SETTINGS']['YAHOO'],
				"keys"    => array ( "id" => $config['HYBRIDAUTH_SETTINGS']['YAHOO_ID'], "secret" => $config['HYBRIDAUTH_SETTINGS']['YAHOO_SECRET'] ),
			),

			"aol"  => array ( 
				"enabled" => $config['HYBRIDAUTH_SETTINGS']['AOL'] 
			),

			"google" => array ( 
				"enabled" => $config['HYBRIDAUTH_SETTINGS']['GOOGLE'],
				"keys"    => array ( "id" => $config['HYBRIDAUTH_SETTINGS']['GOOGLE_ID'], "secret" => $config['HYBRIDAUTH_SETTINGS']['GOOGLE_SECRET'] )
			),

			"facebook" => array ( 
				"enabled" => $config['HYBRIDAUTH_SETTINGS']['FACEBOOK'],
				"keys"    => array ( "id" => $config['HYBRIDAUTH_SETTINGS']['FACEBOOK_ID'], "secret" => $config['HYBRIDAUTH_SETTINGS']['FACEBOOK_SECRET'] ), 
			),

			"twitter" => array ( 
				"enabled" => $config['HYBRIDAUTH_SETTINGS']['TWITTER'],
				"keys"    => array ( "key" => $config['HYBRIDAUTH_SETTINGS']['TWITTER_ID'], "secret" => $config['HYBRIDAUTH_SETTINGS']['TWITTER_SECRET'] ) 
			),

			// windows live
			"live" => array ( 
				"enabled" => $config['HYBRIDAUTH_SETTINGS']['LIVE'],
				"keys"    => array ( "id" => $config['HYBRIDAUTH_SETTINGS']['LIVE_ID'], "secret" => $config['HYBRIDAUTH_SETTINGS']['LIVE_SECRET'] ) 
			),

			"myspace" => array ( 
				"enabled" => $config['HYBRIDAUTH_SETTINGS']['MYSPACE'],
				"keys"    => array ( "key" => $config['HYBRIDAUTH_SETTINGS']['MYSPACE_ID'], "secret" => $config['HYBRIDAUTH_SETTINGS']['MYSPACE_SECRET'] ) 
			),

			"linkedin" => array ( 
				"enabled" => $config['HYBRIDAUTH_SETTINGS']['LINKEDIN'],
				"keys"    => array ( "key" => $config['HYBRIDAUTH_SETTINGS']['LINKEDIN_ID'], "secret" => $config['HYBRIDAUTH_SETTINGS']['LINKEDIN_SECRET'] ) 
			),

			"foursquare" => array (
				"enabled" => $config['HYBRIDAUTH_SETTINGS']['FOURSQUARE'],
				"keys"    => array ( "id" => $config['HYBRIDAUTH_SETTINGS']['FOURSQUARE_ID'], "secret" => $config['HYBRIDAUTH_SETTINGS']['FOURSQUARE_SECRET'] ) 
			),
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,

		"debug_file" => "",
	);

?>
