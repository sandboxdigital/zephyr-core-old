

CMS.Form.Field.Text = $.inherit(
	CMS.Form.Field,
	{
		__constructor : function(xml, parent) {
			this.__base (xml, parent);
		},
		
		renderField : function() {
		    return '<div class="field"><input type="text" name="' + this.path + '" id="' + this.elPath + '" class="text" /></div>';
		},
		
		populate : function (xml) 
		{
			if (xml.firstChild)
				$('#'+this.elPath).val(xml.firstChild.data)
		}
	});

