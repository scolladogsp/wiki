$(document).ready(function(){
	$("#bs-button-user div.bs-userminiprofile, #bs-personal-name").click(function(e) {
		e.preventDefault();
		var event = e;
		$('#bs-personal-menu-container').fadeToggle(200);
		//needs to be done here, bs-personal-menu-top got a width of 0px before the click due to display none
		if ($("#bs-button-user div.bs-personal-menu-top").css('margin-left') === "0px"){
			var marginLeft = $("#bs-button-user div.bs-userminiprofile").outerWidth();
			marginLeft = $("#bs-personal-name").outerWidth() === null ? marginLeft : (marginLeft+$("#bs-personal-name").outerWidth());
			marginLeft = ($("#bs-personal-menu").width() - 10) - (marginLeft/2);
			$("#bs-button-user div.bs-personal-menu-top").css("margin-left", marginLeft);
		}

	});
	$(document).click(function(e){
		if ($(e.target).attr("id") !== "bs-personal-name" && $(e.target).parent().parent().parent().attr('id') !== "bs-button-user" && $(e.target).parent().attr('id') !== 'bs-cactions-button'){
			$('#bs-personal-menu-container').fadeOut(200);
			$('#bs-flyout').fadeOut(200);
			$("#bs-cactions-button > a").removeClass("open");
		}
	});
	var menuTop = $("<div>");
	menuTop.addClass("bs-personal-menu-top");
	$("#bs-personal-menu-container").prepend(menuTop);
});