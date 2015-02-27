(function(mw, $){
	$(function(){
		if ( wgIsArticle === true && wgArticleId !== 0 ) {
			$.getJSON(
				bs.util.getAjaxDispatcherUrl('BlueSpiceSkinHooks::ajaxGetDiscussionCount', [], true),
				function( data ) {
					if(data.success) {
						var discussAmount = $('<span> ('+data.payload+')</span>');
						discussAmount.hide();
						discussAmount.appendTo( $('#ca-talk > a').first() );
						discussAmount.css('display', 'inline').fadeIn();
					}
				}
			);
		}
		$("#ca-watch, #ca-unwatch").click(function(e){
			$(this).children().first().trigger('click');
			if ($(this).hasClass("icon-star"))
				$(this).removeClass("icon-star").addClass("icon-star3");
			else
				$(this).removeClass("icon-star3").addClass("icon-star");
		});
	});
})( mediaWiki, jQuery );