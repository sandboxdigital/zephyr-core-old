

CMS.Form.Field.Custom = $.inherit(
	CMS.Form.Field,
	{
		__constructor : function(xml, parent) {
			this.__base (xml, parent);
			
			this._html = $(xml).attr("html") ? $(xml).attr("html") : "";
		},
		
		render : function() {
		    return '<div class="custom">'+this._html+'</div>';
		}
	});

