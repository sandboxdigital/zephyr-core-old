( function($) {
	var defaultOptions = {
			loadIntoParent :false
	}
		
	$.fn.refresh = function (options)
	{

		options = $.extend(defaultOptions, options);
		
		$(this).each(function(){
			var url = $(this).attr("url");
			if (url) {
				if (options.loadIntoParent)
					$(this).parent().load(url);
				else
					$(this).load(url);
			}
		});
	}
})(jQuery);
