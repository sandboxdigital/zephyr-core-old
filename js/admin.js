
$(document).ready(function() {
	$('#Nav ul li').each (function (){
		var sub = $(this).find ("ul");
		if (sub.length>0) {
			$(this).children ("a:first").click (function(){
				$(this).parent().children ("ul").slideToggle('fast');
				return false;
			});
		}
	
	});
});	