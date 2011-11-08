( function($) {
var inPageForms = new Array ();;

var defaultOptions = {
	mode :'visible', // visible | hidden | dialog
	autoClose :true, // auto close dialog if there is no form element
	onShowMenu :null,
	container :'',
	overlay :'',
	submit:'#submit',
	cancel:'#cancel',
	onclick:null
}

$.fn.loadInPanel = function(options) 
{
	inPageForms.push(new LoadInPanelController (this, options));
	
	// chainability
	return $(this);
}


function LoadInPanelController(elements, settings) 
{
	this.init(elements, settings);
}

$.extend(LoadInPanelController.prototype, {
	container : '',
	overlay : '',
	settings : {},
	autoClose : true,
	
	init : function(elements, settings) 
	{
		this.settings = $.extend({}, defaultOptions, settings);		
		
		this.container = this.settings.container;
		this.overlay = this.settings.overlay;
		this.autoClose = this.settings.autoClose;
		this.mode = this.settings.mode;
		
		if (this.mode=='dialog') {
			this.container = this.container==''?'#inpageDialog':this.container;
			if ($(this.container).length==0) {
				$('body').append ('<div id="'+this.container.substring(1)+'" style="display:none;">xxx</div');
				$(this.container).dialog({
					autoOpen: false
					,width:400
					});			
			}
		}

		if (this.container == '')
			alert('inpageform - container required')
		
		var _this = this;
		$(elements).live("click", function () {
			_this.onLinkClick (this);
			return false;
		});
		
		$(this.overlay).live("click", $.inScope(this.onCancelClick,this));
	}, 

	onLinkClick : function (target) 
	{		
		if (this.settings.onclick) {
			this.settings.onclick.call (target);
		}
		if (this.overlay) {
			$(this.overlay).fadeIn('fast');
		}

		var _this = this;
		var url = $(target).attr('href');
		var data = {};
		$.get(url, data, function (data) {_this.onLoad(data);});		
		$(document).keydown ($.inScope(this.onKeyPress,this));	
		
		if (this.settings.onclick) {
			var params = {
				el:target
			};
			this.settings.onclick.call(target,params)
		}
		return false;
	},
	
	onFormSubmit : function (form) 
	{
		tinyMCE.triggerSave();
		
		var form = $(form);
		var url = form.attr("action");
		if (url) {
			var data = form.serialize();
			$.post(url, data, $.inScope(this.onLoad,this));
		} else 
			alert ("inPageForm - Form does not contain an action - can't submit");
		
		return false;
	},

	onLoad : function (data) 
	{
		if (Tg.TinyMce)
			Tg.TinyMce.unregisterAll ();
		
		$(this.container).html(data);
		
		if (data.indexOf ("Bootstrap Exception Caught")>0) {
			// don't auto close
		} else {				
			var forms = $(this.container).find('form');
			if (forms.length == 0 && this.autoClose) {
				if (this.overlay)
					$(this.overlay).fadeOut('fast');

				if (this.mode == 'dialog')
					$(this.container).dialog("close");
				else if (this.mode == 'hidden')
					$(this.container).fadeOut();
			}
			else {
				if (this.mode == 'dialog')
					$(this.container).dialog("open");
				else if (this.mode == 'hidden')
					$(this.container).fadeIn("fast");
				
				var _this = this;
				
				forms.submit(function (){
					_this.onFormSubmit(this);
					return false;
				});
			}
		}		
	},
	
	onKeyPress : function (e) 
	{
	     if (e.which==27)
	    	 this.onCancelClick();
	},
	
	onSubmitLoad : function (data) 
	{
		$(this.container).html(data);
		
	},
	
	onCancelClick : function() 
	{
		if (this.overlay)
			$(this.overlay).fadeOut('fast');

		if (this.mode == 'dialog')
			$(this.container).dialog("close");
		else if (this.mode == 'hidden')
			$(this.container).hide();
		return false;
	}
});
	
function parseParams (url) 
{
	var params = url;
	var pHash = {};
	if (url.indexOf('?') > 0) {
		if (url.match(/\?(.+)$/)) {
			// in case it is a full query string with ?, only take
			// everything after the ?
			params = RegExp.$1;
		}
		var pArray = params.split("&");
		for ( var i = 0; i < pArray.length; i++) {
			var temp = pArray[i].split("=");
			pHash[temp[0]] = unescape(temp[1]);
		}
	}
	return pHash;
}
})(jQuery);
