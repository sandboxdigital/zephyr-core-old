// JavaScript Document
(function($) {	
	$.fn.jTree = function(options) {
		if (options=='serialize') {			
			//return '';
			return serialize (this, 'item');
		}
		var opts = $.extend({}, $.fn.jTree.defaults, options);
		var cur = 0, curOff = 0, off =0, h =0, w=0, hover = 0;
		var str='<li class="jTreePlacement'+(opts.placmentClass?' '+opts.placementClass+'"':'" style="background-color:#ffefef"')+'></li>';
		var container = this;
		var placement = 0;
		
		var s = (!!opts.handle)?'li '+opts.handle:'li';
		//events are written here
		$(this).find(s).mousedown(function(e) {			
			if ($("#jTreeHelper").length==0) {
				$("body").append($('<ul id="jTreeHelper"></ul>'));
			}
			if ($("#jTreeHelper").is(":not(:animated)") && e.button !=2) {
				var li = (!!opts.handle)?$(this).closest("li")[0]:this;
				//get the current li and append to helper
				$(li).clone().appendTo("#jTreeHelper");
				
				// get initial state, cur and offset
				cur = li;
				curOff = $(cur).offset();
				$(cur).hide();
						
				// show initial helper
				$("#jTreeHelper").css ({
					position: "absolute",
					top: e.pageY + 5,
					left: e.pageX + 5,
					background: opts.hBg,
					opacity: opts.hOpacity
				}).show();
				$("#jTreeHelper *").css ({
					color: opts.hColor,
					background: opts.hBg
				});
				
				// start binding events to use
				// prevent text selection
				$(document).bind("selectstart", doNothing);
				
				// doubleclick is destructive, better disable
				$(container).find("li").bind("dblclick", doNothing);
				
				// in single li calculate the offset, width height of hovered block
				$(container).find("li").bind("mouseover", getInitial);
				
				// in single li put placement in correct places, also move the helper around
				$(container).find("li").bind("mousemove", putPlacement);
				
				// handle mouse movement outside our container
				$(document).bind("mousemove", helperPosition);
				
				placement = $(str);				
				$(cur).after(placement);
				placement.height($(cur).outerHeight());
				
				// handle mouse movement outside our container
				$(placement).bind("mousemove", doNothing);
			}
			//prevent bubbling of mousedown
			return false;
		});
		
		// in single li or in container, snap into placement if present then destroy placement
		// and helper then show snapped in object/li
		// also destroys events
		$(this).find("li").andSelf().mouseup(function(e) {
			// if placementBox is detected
			if ($(".jTreePlacement").is(":visible")) {
				$(cur).insertBefore(".jTreePlacement").show();
			}
			$(cur).show();
			// remove helper and placement box
			$(container).find("ul:empty").remove();
			$("#jTreeHelper").empty().hide();
			$(".jTreePlacement").remove();
			
		    if (!!opts.update) 
		    	opts.update (e, container);
			
			// remove bindings
			destroyBindings();
			
			return false;
		});
		
		$(document).mouseup(function(e){
			if (cur!=0) {
				destroyBindings();
				$("#jTreeHelper").animate({
					top: curOff.top,
					left: curOff.left
						}, opts.snapBack, function(){
							$(cur).show();
							$("#jTreeHelper").empty().hide();
							$(".jTreePlacement").remove();
							cur = 0;
						}
				);
			}
			return false;
		});
		//functions are written here
		var doNothing = function(){
			return false;
		};
		
		var destroyBindings = function(){
			$(document).unbind("selectstart", doNothing);
			$(container).find("li").unbind("dblclick", doNothing);
			$(container).find("li").unbind("mouseover", getInitial);
			$(container).find("li").unbind("mousemove", putPlacement);
			$(document).unbind("mousemove", helperPosition);
		};
		
		var helperPosition = function(e) {
			$("#jTreeHelper").css ({
				top: e.pageY + 15,
				left: e.pageX + 15
			});
		};
		
		var getInitial = function(e) {
			return false;
		};
		
		var putPlacement = function(e) {			
			if (e.currentTarget != hover) {				
				if (hover!=0)
					$(hover).removeClass ("jTreeHover");
				hover = e.currentTarget;
				off = $(hover).offset();
				h = $(hover).height();
				w = $(hover).width();
				$(hover).addClass ("jTreeHover");
			}

			$("#jTreeHelper").css ({
				top: e.pageY + 15,
				left: e.pageX + 15
			});
			
			var edgeV = 10;
			var edgeH = 5;
	
			//inserting before
			if ( e.pageY >= off.top && e.pageY < (off.top + edgeV) ) {
				if (!$(hover).prev().hasClass("jTreePlacement")) {
					placement.remove();
					$(hover).before(placement);
				}
			}
			//inserting after
			else if (e.pageY > (off.top + h - edgeV) &&  e.pageY <= (off.top + h) ) {
				// as a sibling
				if (e.pageX > off.left && e.pageX < off.left + opts.childOff) {
					if (!$(hover).next().hasClass("jTreePlacement")) {
						placement.remove();
						$(hover).after(placement);
					}
				}
				// as a child
				else if (e.pageX > off.left + opts.childOff) {	
					if ($(hover).find('.jTreePlacement').length==0) { // already a child
						placement.remove();
						if ($(hover).find("ul").length == 0)
							$(hover).append('<ul></ul>');
						$(hover).find("ul").append(placement);
					}
				}
			}
			
			// clean up!
			$(container).find("ul:empty").remove();
			return false;
		}
		
		var lockIn = function(e) {
			// if placement box is present, insert before placement box
			if ($(".jTreePlacement").length==1) {
				$(cur).insertBefore(".jTreePlacement");
			}
			$(cur).show();
			
			// remove helper and placement box
			$("#jTreeHelper").empty().hide();
			
			$(".jTreePlacement").remove();;
		}
	}; // end jTree

	function serialize (ul) {
		var children = new Array ();
		
		var items = ul.children('li');
		items.each (function (i) {
			var id = $(this).attr('pageid');
			var child = {'id':id};
			var ul = $(this).children('ul');
			if (ul.length>0)
				child.children = serialize (ul);
			children.push (child)
		});
		return children;
	};

	$.fn.jTree.defaults = {
		showHelper: false,
		hOpacity: 0.75,
		hBg: "#fff",
		hColor: "#222",
		placementClass:null,
		childOff: 20,
		snapBack: 400
	};
		  
})(jQuery);


