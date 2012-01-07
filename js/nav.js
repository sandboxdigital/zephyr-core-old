$(document).ready (function(){
	$('#nav a')
	.each(function(){
		var t = $(this).text();
		$(this).empty();
		$(this).append ('<div class="over" />');
		$(this).append ('<div class="text">'+t+'</div>');
	})
	.hover (function(){
		$('> div.over',this).stop().animate({opacity:1});
	},function (){
		$('> div.over',this).stop().animate({opacity:0});
	});

	
	$('#nav a div.over').css({opacity:0});
});