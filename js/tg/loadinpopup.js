(function ($) {
    var loadingTimer, loadingFrame = 1, loading, overlay

    // private methods
    loadInPopupInit = function () {
        overlay = $('<div id="loadInPopup-overlay" style="display:none;">&nbsp;</div>')
		.appendTo('body')
		.css({ position: 'absolute',
		    top: '0px',
		    left: '0px',
		    width: '100%',
		    height: $(document).height(),
		    backgroundColor: '#000000',
		    zIndex: '99'
		})
		.bind("click", $.loadInPopup.cancel);

        $('body').append(
			loading = $('<div id="loadInPopup-loading" style="position:fixed;top:50%;left:50%;height:40px;width:40px;margin-top:-20px;margin-left:-20px;cursor: pointer;overflow:hidden;z-index:1104;display:none;"><div style="position: absolute;top:0;left: 0;width: 40px;height: 480px;background-image:url(\'/core/images/fancybox.png\');"></div></div>')
		);

        loading.click($.loadInPopup.cancel);

        $('#loadInPopup-close').live('click', $.loadInPopup.cancel);
    },

	loadInPopupAnim = function () {
	    if (!loading.is(':visible')) {
	        clearInterval(loadingTimer);
	        return;
	    }

	    $('div', loading).css('top', (loadingFrame * -40) + 'px');

	    loadingFrame = (loadingFrame + 1) % 12;
	};

    var popups = new Object();

    var defaultOptions = {
        autoClose: true, 		// auto close dialog if there is no form element
        overlay: true,
        container: '#popup',
        overlay: '#overlay',
        submit: '#submit',
        cancel: '#cancel',
        overlayOpacity: .25,
        centre: false,
        source: null				// load from an element not a URL
    }

    // Public methods	

    $.loadInPopup = function (action, options) {
        if (action == 'close') {
            $.loadInPopup.cancel();
        } else {
            var popup = new PopupController(null, options);
            popup.open(options.url);
        }
    };

    $.fn.loadInPopup = function (options) {
        if (options == 'close')
            popups[this].onCancelClick();
        else
            popups[this] = new PopupController(this, options);

        // chainability
        return $(this);
    };

    $.loadInPopup.setDefaultOption = function (name, value) {
        defaultOptions[name] = value;
    };

    $.loadInPopup.showOverlay = function () {
        overlay.show();
        overlay.css({ opacity: 0 });
        overlay.animate({ opacity: defaultOptions.overlayOpacity });
    };

    $.loadInPopup.hideOverlay = function () {
        overlay.fadeOut('fast');
        loading.hide();
    };

    $.loadInPopup.showActivity = function () {
        clearInterval(loadingTimer);

        loading.show();
        loadingTimer = setInterval(loadInPopupAnim, 66);
    };

    $.loadInPopup.hideActivity = function () {
        loading.hide();
    };

    $.loadInPopup.centreOnScreen = function (element) {
        var el = $(element);
        var h = el.height();
        var w = el.width();
        if (el.css('position') == 'absolute') {
            var left = ($(window).width() - w) / 2
            var top = ($(window).height() / 2) - (h / 2);
            $(element).css({ left: left, top: top });
        } else {
            $(element).css({ marginLeft: -1 * (w / 2) });
            $(element).css({ marginTop: -1 * (h / 2) });
        }
    };

    $.loadInPopup.open = function () {

    };

    $.loadInPopup.cancel = function () {
        for (var i in popups)
            popups[i].onCancelClick();
    };

    function PopupController(elements, settings) {
        this.init(elements, settings);
    }

    $.extend(PopupController.prototype, {
        container: '',
        settings: null,

        init: function (elements, settings) {
			this.settings = $.extend({}, defaultOptions, settings);

            this.container = $(this.settings.container);

            if (this.container.length == 0) {
                this.container = $('<div id="' + this.settings.container.substring(1) + '" style="display:none;">&nbsp;</div>')
				.appendTo('body');
            }

            var _this = this;
            $(elements).addClass("loadInPopup");
            $(elements).live("click", function () { _this.onLinkClick(this); return false; });
        },
        //	
        //	open : function (url, target) 
        //	{
        //		if (this.settings.onclick) {
        //			this.settings.onclick.call(target);
        //		}
        //		
        //		if (this.overlay) {
        //			this.overlay.fadeIn('fast');
        //		}
        //		
        //		$.loadInPopup.showActivity ();
        //		
        //		return false;
        //		
        //		var data = {};
        //		$.get(url, data, $.inScope(this.onLoad,this));		
        //		$(document).keydown ($.inScope(this.onKeyPress,this));	
        //		
        //		this.target = target;
        //		
        //		return false;
        //	},
        
        open : function (url)
        {
            $.loadInPopup.showOverlay();
            $.loadInPopup.showActivity();
            var data = {};
            $.get(url, data, $.inScope(this.onLoad, this));
        },

        onLinkClick: function (target) {
            if (this.settings.onclick) {
                this.settings.onclick.call(target);
            }

            if (this.settings['source']) {
                $.loadInPopup.showOverlay();
                $.loadInPopup.showActivity();
                this.onLoad($(this.settings['source']).html());
            } else {
                var url = $(target).attr('href');
                this.open(url);
            }

            this.target = target;

            return false;
        },

        onSubmitClick: function (submit) {
            
            var form = $(submit).parents("form");
            var url = form.attr("action");
            var data = form.serialize();
            data += "&buttonClicked=" + $(submit).attr("id");
            $.post(url, data, $.inScope(this.onLoad, this));
            
            $.loadInPopup.showActivity();
            
            if (this.submit)
            	this.submit.attr("disabled","disabled").after("&nbsp;&nbsp;Loading");
            return false;
        },

        onLoad: function (data) {
            $.loadInPopup.hideActivity();

            $(document).keydown($.inScope(this.onKeyPress, this));

            // need to show the container so that fileupload/swfupload can get the dimensions of
            // button
            //		this.container.css({top:-1000,left:-1000});
            this.container.show();
            // this.container.html(data); -- odd bug in ie7 causes error
            if ($.browser.msie == true && $.browser.version < 8)
            	this.container[0].innerHTML = data;
            else 
            	this.container.html(data);
            this.container.hide();
            var _this = this;

            if (data.indexOf("Bootstrap Exception Caught") > 0) {
                // don't auto close
            } else {
                var forms = this.container.find('form');
                if (forms.length == 0 && this.settings.autoClose) {
                    if (overlay)
                        overlay.fadeOut('fast');

                    this.container.fadeOut();
                }
                else {
                    // check make sure we can do our stuff with this form!

                    var submit = this.container.find('input:submit');
                    if (submit.length == 0)
                        c('Could not find submit button (' + this.settings['submit'] + ') to bind to!');

                    if (forms.length > 0) {
                        var action = $(forms).attr("action");
                        if (action == "") {
                            c('Form does not have an action!');
                        }
                    }

                    this.container.fadeIn("fast");

                    if (this.settings.centre)
                        $.loadInPopup.centreOnScreen(this.container);

                    submit.click(function () {
                        _this.onSubmitClick(this);
                        return false;
                    });
                    
                    this.submit = submit;

                    this.container.find('#windowClose').click(function () {
                        $('.popup').loadInPopup('close');
                        return false;
                    });

                    if (this.settings.onload) {
                        var params = {
                    };
                    this.settings.onload.call(this.target, params)
                }
            }
        }
    },

    onKeyPress: function (e) {
        if (e.which == 27)
            this.onCancelClick();
    },

    onCancelClick: function () {
        if (overlay)
            overlay.fadeOut('fast');

        this.container.hide();

        return false;
    }

});

$(document).ready(function () {
    loadInPopupInit();
});
})(jQuery);
