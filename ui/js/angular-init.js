APP.controller('initController',['$scope','Resource','API','$timeout','$location',

	function($scope,$resource,API,$timeout,$location) {
		$scope.minHeight=$(window).height()-3;
		$scope.headerURL = 'ui/tpl/header.html';

		API.Resources = {
			Session: $resource( 'api/session/:id/ ', { id: '@id' } ),
			Note: $resource( 'api/note/:id/ ', { id: '@id' } ),
		};

		API.Helpers.path = function(){
			return '/notes/';
		}

		API.Helpers.popup = function(block, done){
            console.log('popup '+block);
            if(done){
            	$('.dialog-'+block).fadeOut('fast');
            }
            else {
            	$('.dialog-'+block).fadeIn('fast');
            	$('.dialog-'+block+' .confirm').focus();
            }
        }

		$scope.API = API;
		API.REST.query('Session', 'user', false, function(){
			window.SessionUser = API.Scope.user;
			API.Session.check();
		});
	}

]);
