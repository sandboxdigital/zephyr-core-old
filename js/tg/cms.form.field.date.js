
CMS.Form.Field.Date = $.inherit(
	CMS.Form.Field,
	{
		__constructor : function(xml, parent) {
			this.__base (xml, parent);
		},
		
		renderField : function() {
			return '<div class="field"><input type="text" name="'+this.path+'" id="'+this.elPath+'" class="textsmall" /></div>';
		},
		
		populate : function (xml) 
		{
            this.__base(xml);
			if (xml.firstChild)
				$('#'+this.elPath).val(xml.firstChild.data)
		},
		
		renderDone : function ()
		{
			$('#'+this.elPath).datepicker({showAnim: 'fadeIn' ,dateFormat:"dd-mm-yy"});
		} 
	});
