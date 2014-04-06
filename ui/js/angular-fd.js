APP.filter('embedimg', function(){
    return function( input ){
        if( input.indexOf( '/media/uploads/' ) != -1 ){
          	return '<img class="img-responsive" src="' + input + '"/>';
        }
        else
            return input;
    }
});

APP.directive('setMinHeight', function() {
	return {
		restrict : 'A',
		link : function(scope,element) {
				$('div.container-fluid[class*=-con]').css("min-height",$(window).height());
			}
		};
});
