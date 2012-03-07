
CMS.Form.Field.Block = $.inherit(
	CMS.Form.Field,
	{
		fields : null,
		xml : null,
		details : null,
		expandedOnCreate : null,
		number : null,
		view : null,
		expanded : null,
		populated : true,
		childrenRendered : false,
		_dataXml : null,
		
		__constructor : function(xml, parent) {
			this.__base (xml, parent);

            this.view = new String($(xml).attr("view"));
			this.xml = xml;
			
			this.expandedOnCreate = $(xml).attr("expanded") == 'false'?false:true;
			
			CMS.Form.loadFields (xml, this);
		},
		
		debug : function(indent) {
			var output = "\t".repeat(indent)+"Field "+this.type + ", id:"+this.id + ", path:"+this.path + "<br>"+"\t".repeat(indent)+"{<br>";
			jQuery.each(this.fields, function (id) {
				output += this.debug (indent+1);
			}) 
			output += "\t".repeat(indent)+"}<br>";
			return output;
		},
		
		render : function() {
			var output = "";
			
			if (this.expandedOnCreate) {
				jQuery.each(this.fields, function (id) {
					output += this.render ();
				})
			}
			
			var css = this.expandedOnCreate?"":"display:none;";

            return '<div class="CMSGroupoption CMSGroupoption'+this.id+'" id="' + this.elPath + '"><div id="' + this.elPath + '_title" class="CMSGroupoptionTitle">' + this.label + '</div><div id="' + this.elPath + '_fields" class="CMSGroupoptionBody" style="'+css+'">' + output + '<div style="clear:both;float:none;height:1px;">&nbsp;</div></div></div>';
		},
		
		renderDone : function () {
			if (this.expandedOnCreate){
				var cssClass = "CMSIconHide";
				var title = "Hide";
				this.expanded = true;
			}else{
				var cssClass = "CMSIconShow";
				var title = "Show";
				this.expanded = false;
			}
			var div = '<div class="CMSElementDetails" id="'+this.elPath+'_detail"><div><nobr><a href="#" class="CMSElementDetailsToggleVis '+cssClass+'" title="'+title+'">'+title+'</a></nobr></div></div>';
			var jqItem = $('#'+this.elPath);
			
			this.details = $(div).appendTo(jqItem).hide();

			jqItem.unbind ();
			jqItem.hover ($.inScope(this.onElementOver,this), $.inScope(this.onElementOut,this));
			$('#'+this.elPath+'_detail .CMSElementDetailsToggleVis').click ($.inScope(this.onToggleVis,this));	


			if (this.expandedOnCreate) {		
				jQuery.each(this.fields, function (id) {
					this.renderDone ();
				});
				this.childrenRendered = true;
			} else 
				this.childrenRendered = false;
		},
		
		populate : function (xml)
		{
            this.__base(xml);
			if (this.expandedOnCreate) {				
				this.populateChildren (xml);
			} else {
				this._dataXml = xml; // save for later
			}
		},
		
		hide : function ()
		{
			$('#'+this.elPath+' > div > nobr > .CMSElementDetailsToggleVis').addClass ('CMSIconShow').removeClass ('CMSIconHide').attr ("title","Show");
			$('#'+this.elPath+'_fields').slideUp();	
			
			this.expanded = false;		
		},
		
		show : function ()
		{
			$('#'+this.elPath+' > div > nobr > .CMSElementDetailsToggleVis').addClass ('CMSIconHide').removeClass ('CMSIconShow').attr ("title","Hide");
			$('#'+this.elPath+'_fields').slideDown();
			
			if (!this.childrenRendered)
			{

				c(this.childrenRendered)
				var output = '';

				jQuery.each(this.fields, function (id) {
					output += this.render ();
				})
				
				$('#'+this.elPath+'_fields').html (output);
				
				jQuery.each(this.fields, function (id) {
					this.renderDone ();
				});
				this.populateChildren (this._dataXml);
				this.childrenRendered = true;
			}
			$('#'+this.elPath+'_fields').slideDown();
			this.expanded = true;
		},
		
		populateChildren : function (xml)
		{
			var _this = this;
			$(xml).children().each (function (key) {
				if (_this.fields[$(this).attr("id")])
					_this.fields[$(this).attr("id")].populate (this);
			});
		},
		
		onElementOver : function (event) 
		{
			this.details.show();
		},
		
		onElementOut : function (event) 
		{			
			this.details.hide();
		},
		
		onToggleVis : function (event) 
		{
			// hide/show
			if (this.expanded)
				this.hide ();
			else
				this.show ();
				
			return false;
		},
		
		toXml: function (xmlDoc) {
			if (this.childrenRendered) {
			    var el = xmlDoc.createElement(this.type);
			    el.setAttribute("id", this.id);
		        el.setAttribute("uid", this.uid);
			    el.setAttribute("view", this.view);
	
			    if (this.value) {
			        var newCDATA = xmlDoc.createCDATASection(this.value);
			        el.appendChild(newCDATA);
			    }
	
			    if (this.fields) {
			        jQuery.each(this.fields, function (id) {
			            el.appendChild(this.toXml(xmlDoc));
			        });
			    }
			    return el;
			} else {
				xmlDoc.adoptNode (this._dataXml);
				return this._dataXml;
			}
		}
	});
