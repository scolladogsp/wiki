$(document).ready(function(){
	mw.loader.using('jquery.ui.tabs', function() {
		$("#bs-data-after-content").tabs({
			cookie: {
				expires: 30
			}
		});
	});
	$(document).on("onBsShoutboxAfterUpdated", function(e, BsShoutbox){
		$(".bs-sb-archive").addClass("icon-cancel-circle");
	});
});