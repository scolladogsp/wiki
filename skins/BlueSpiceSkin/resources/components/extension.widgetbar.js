$(document).ready(function() {
	var widgetBarToggler = $('#bs-cactions-button > a');
	widgetBarToggler.click(function(e){
		$(this).toggleClass("open");
		if ($('#bs-flyout').length !== 0)
			$("#bs-flyout").fadeToggle(200);
		else
			$("li#bs-cactions-button div.menu").fadeToggle(200);

		e.stopPropagation();
	});
	var widgetFlyout = $('#bs-flyout');
	if( widgetFlyout.length === 0 ) {
		return;
	}

	$("#bs-widget-container").find("h5.bs-widget-title").each(function(i, v){
		$(v).click(function(e){
			e.preventDefault();
			e.stopImmediatePropagation();
		});
	});

	//-2 because of the border 1px left + right
	widgetFlyout.width( $('#content').outerWidth() - 2 );

	var moreContent = $("div#bs-flyout-content div#bs-flyout-content-widgets div.bs-widget").first().clone(true);
	var title = $("h3#p-cactions-label").text();
	moreContent.attr("id", "bs-widget-more-menu");
	moreContent.attr("title", title);
	moreContent.find("h5").first().text(title);
	moreContent.find("div.bs-widget-body").first().html("");
	moreContent.find("div.bs-widget-body").first().append($("li#bs-cactions-button div.menu"));

	var moreContainer = $("<div>");
	var moreContainerHeadline = $("<h4>");
	moreContainer.attr('id', 'bs-flyout-content-more');
	moreContainerHeadline.attr("id", 'bs-flyout-content-more-header');
	moreContainerHeadline.text(mw.message("bs-tools-button").plain());
	moreContainer.append(moreContainerHeadline);
	$("#bs-flyout-content-widgets").after(moreContainer);
	$("#bs-flyout-content-more").append(moreContent);
});

