
Tg.Window = Ext.extend(Ext.Window, {
	url : "",
	defaultConfig : {
		submitLabel:'Submit'	
	}

	,constructor: function(config) {

		config = config || {};
		
	    config = Ext.apply (this.defaultConfig, config);
	    
	    var winConfig = {
				autoScroll:true,
				width:500,
				height:300,
	            modal: true,
	            buttonAlign: 'center',
				buttons: [
					{
						text:config.submitLabel,
						disabled:true,
						scope: this,
						handler: this.handleSubmit
					},
					{
						text: 'Close',
						scope:this,
						handler:this.handleClose
					}
				],
				html:'<div>xxx</div>'
			};
	
	    Tg.Window.superclass.constructor.call(this, winConfig);
	},

    loadUrl : function (url, params)
    {
	  	this.buttons[0].disable();
	    this.url = url;
		this.load({
		    url: this.url,
		    params: params, // or a URL encoded string
		    callback: this.handleLoad,
		    scope: this, // optional scope for the callback
		    discardUrl: false,
		    nocache: true,
		    text: 'Loading...',
		    timeout: 30,
		    scripts: true
		});
    },

	handleClose : function (x)
    {
    	this.hide();
    },
    
	handleLoad : function (x)
    {
  	  this.buttons[0].enable();
  	  
  	  if (this.submitting)
  		  this.fireEvent ("submitted", this);
  	  
		this.submitting = false;
    },

    handleSubmit : function ()
    {
		this.submitting = true;
		var form = $(this.body.dom).find ("form");
		var data = form.serialize();
		this.loadUrl(this.url,data);
		
    }
});

Tg.FormWindow = Ext.extend(Ext.Window, {
    url: "",
    submitted: false,

    constructor: function (form) {

        var config = {};

        config = Ext.apply({
            title: '...',
            width: 414,
            autoHeight: true,
            resizable: false,
            modal: true,
            buttonAlign: 'center',
            stateful: false,
            defaultButton: 0,
            cls: 'formPanelWindow',
            keys: {
                key: Ext.EventObject.ENTER,
                handler: this.handleSubmit,
                scope: this
            },
            buttons: [
				{
				    text: 'Save',
				    scope: this,
				    handler: this.handleSubmit
				},
				{
				    text: 'Close',
				    scope: this,
				    handler: this.handleClose
				}
			],
            items: form
        }, config);

        Tg.FormWindow.superclass.constructor.call(this, config);
    },

    loadUrl: function (url, params) {
        this.buttons[0].disable();
        this.url = url;
        this.load({
            url: this.url,
            params: params, // or a URL encoded string
            callback: this.handleLoad,
            scope: this, // optional scope for the callback
            discardUrl: false,
            nocache: false,
            text: 'Loading...',
            timeout: 30,
            scripts: true
        });
    },

    handleClose: function (x) {
        this.hide();
    },

    handleLoad: function (x) {
        this.buttons[0].enable();
    },

    handleSubmit: function () {
        if (this.submitted)
            return false;

        this.submitted = true;
        var self = this;

        this.items.items[0].getForm().submit({
            clientValidation: true,
            url: this.url,
            params: {
                newStatus: 'delivered'
            },
            success: function (form, action) {
                self.hide();
                //Ext.Msg.alert('Success', action.result.msg);
                self.submitComplete(action.result);
            },
            failure: function (form, action) {
                switch (action.failureType) {
                    case Ext.form.Action.CLIENT_INVALID:
                        Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                        break;
                    case Ext.form.Action.CONNECT_FAILURE:
                        Ext.Msg.alert('Failure', 'Ajax communication failed');
                        break;
                    case Ext.form.Action.SERVER_INVALID:
                        Ext.Msg.alert('Failure', action.result.msg);
                }
            }
        });
    }

    , submitComplete: function (results) {
        var form = this.items.items[0].getForm();
        var values = form.getValues();
        this.fireEvent('save', this, values, results);
    }

    , findFirst: function (item) {
        if (item instanceof Ext.form.Field && item.inputType != "hidden" && !item.disabled) {
            item.focus(false, 50); // delayed focus by 50 ms
            return true;
        }
        if (item.items && item.items.find) {
            return item.items.find(this.findFirst, this);
        }
        return false;
    }

    , focus: function () {
        this.items.items[0].form.items.find(this.findFirst, this);
    }
});
