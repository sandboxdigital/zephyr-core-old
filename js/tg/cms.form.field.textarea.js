
CMS.Form.Field.Textarea = $.inherit(
	CMS.Form.Field,
	{
		__constructor : function(xml, parent) {
			this.__base (xml, parent);
		},
		
		renderField : function() {
		    return '<div class="field"><textarea name="' + this.path + '" id="' + this.elPath + '" class="text"></textarea></div>';
		},
		
		populate : function (xml) 
		{
			if (xml.firstChild)
				$('#'+this.elPath).val(xml.firstChild.data)
		}

        , getValue : function ()
        {
//            alert ('1')
            return CMS.Form.stripInvalidXmlChars (this.value);
        }
	});

