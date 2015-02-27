$(document).ready(function(){
	mw.loader.using('jquery.ui.tabs', function() {
		$("#bs-nav-sections").tabs({
			cookie: {
				expires: 30
			}
		});
	});
});