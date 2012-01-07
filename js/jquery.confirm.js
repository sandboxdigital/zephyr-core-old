(function($) {

    // Default configuration properties.
    var defaults = {
        message: 'Delete?',
        onclick: null
    };
   
	$.fn.confirm = function(o) {
        var options    = $.extend({}, defaults, o || {});
        $(this).live('click', function ()
    	{
        	var e = $(this);
	    	if (confirm(options.message)) {
				var href = e.attr("href");
				$.ajax({
						url:href
				});
				e.parent().parent().fadeOut();
				if (options.onclick)
					options.onclick.call(this, e)
			}
			return false;    	
    	});
    };
})(jQuery);