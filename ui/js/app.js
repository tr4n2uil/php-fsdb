var APP = angular.module('APP',
	['ngRoute','ngSanitize', 'REST', 'API'])

	.config(['$routeProvider','$httpProvider', function($routeProvider, $httpProvider){

		$routeProvider
			.when('/', {templateUrl: 'ui/tpl/login.html',controller: "initController"})

			.when('/notes/', {templateUrl: 'ui/tpl/note/notes.html',
				controller: ['$scope', 'API', function($scope, API){
					API.Session.check();
					API.REST.query('Note', 'notes');
				}]
			});
		
		$httpProvider.defaults.headers.post['Content-Type'] = 'application/json';
		$httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
	}]);


$( document ).ready( function(){

	window.ajax_setup()
	window.fix_placeholder();

	window.base_wide = 70;
	window.base_divider = 45;
	window.base_subpart = 30;
	window.update_viewport();
	$( window ).resize( window.update_viewport );
	
	//tooltip_init();
	//selection_menu( $( '.editable' ) );

} );
