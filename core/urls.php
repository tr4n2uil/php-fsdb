<?php 
/**
 *	Sample Project URLs
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licenced under MIT Licence 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/
	require_once( PR_ROOT. 'auth/models.php' );
	require_once( PR_ROOT. 'auth/session.php' );

	require_once( ROOT. 'apps/models.php' );

	$URLS = array(
		'/^(apps|core|lib|tmp)/i' => array( 'tpl/test.php' ),

		// auth views
		'/^auth$/i' => array( PR_ROOT. 'auth/endpoint.php' ),
		'/^login\/?(?P<provider>[\w\-]+)?$/i' => array( 'tpl/login.php' ),
		'/^logout$/i' => array( PR_ROOT. 'auth/logout.php' ),

		// auth api
		'/^api\/user\/?(?P<id>[\d]+)?$/i' => array( PR_ROOT. 'rest.php', array( 'model' => 'User' )),
		'/^api\/session\/?(?P<id>[\w\.]+)?$/i' => array( PR_ROOT. 'rest.php', array( 'model' => 'Session' )),

		// apps api
		'/^api\/note\/?(?P<id>[\d]+)?$/i' => array( PR_ROOT. 'rest.php', array( 'model' => 'Note' )),

		// file views
		'/^embed$/i' => array( PR_ROOT. 'embed.php' ),

		// include views
		'/^(?P<path>[\w\-\/]*)$/i' => array( PR_ROOT. 'include.php', array( 'root' => ROOT. 'tpl/',  ) ),
	);

?>
