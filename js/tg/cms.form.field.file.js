
CMS.Form.Field.FileDefaults = {
	uploadUrl:"/file/upload",
	shimName:"swfShim",
	swfUrl:"/core/flash/swfupload.swf",
	debug:false,
	sizeLimit:"32000",
	fileTypes : "*.jpg;*.gif;*.png;*.flv;*.f4v;*.mp3;*.pdf"
};

CMS.Form.Field.File = $.inherit(
	CMS.Form.Field,
	{
	    url: null,
	    name: null,
	    fileTypes: null,
	    fileUpload : null,
	    file : null,

	    __constructor: function (xml, parent) {
	        this.__base(xml, parent);

	        this.fileTypes = $(xml).attr("fileTypes") ? $(xml).attr("fileTypes") : CMS.Form.Field.FileDefaults.fileTypes;
	        this.sizeLimit = $(xml).attr("sizeLimit") ? $(xml).attr("sizeLimit") : CMS.Form.Field.FileDefaults.sizeLimit;
	    },

	    renderField: function () {
	        var output = '<div class="FormFile" style="width:98%;"><div class="FormFilePreviewTable" style="float:left;"><table id="' + this.elPath + '_table"></table></div>';
	        output += '<div id="'+this.elPath + '_FormFileButtons" class="FormFileButtons" style="float:left;"><div id="' + this.elPath + '_progress"></div><div>';
	        //output += '<input id="' + this.elPath + '_btnUpload" type="button" class="' + this.elPath + '_btnUpload btnUpload" value="Select file" />';
	        output += '<input id="' + this.elPath + '_btnSelect" type="button" class="' + this.elPath + '_btnSelect btnSelect" value="Select file" />';
	        output += '<input id="' + this.elPath + '_btnCancel" type="button" class="' + this.elPath + '_btnCancel btnCancel" value="Cancel upload" style="display:none;" />';
	        output += '</div></div></div>';

	        return '<div class="field">' + output + '</div>';
	    },

	    renderDone: function () {
	    	if (Ext.Element.get(this.elPath + '_btnSelect'))
	    		Ext.Element.get(this.elPath + '_btnSelect').on('click', this.showFileBrowser, this);
	    }    

	    ,populate: function (xml) {
	        if (xml.firstChild) {
	        	var file = {
        			url :$(xml).attr('url'),
        			name :$(xml).attr('name'),
        			thumbnailUrl :$(xml).attr('thumbnailUrl'),
	        		id:xml.firstChild.data
	        	}
	        		        	
	        	this.addFile (file);
	        }
	    }
	    
	    ,showFileBrowser : function ()	    
	    {
    		var options = {
    			onSelect: this.addFile.createDelegate (this)
    		}
    		Tg.FileManager.showBrowserInWindow (options);
	    }	
		
		,setValue : function (value) 
		{
			this.addFile(value);
		}
	    
        ,addFile : function (file) 
        {
        	c(file)
        	if (Tg.FileManager.fileBrowserWindow)
        		Tg.FileManager.fileBrowserWindow.hide ();

        	this.file = file;
        	
        	this._preview  = $('#' + this.elPath + '_table');

        	this._preview.empty();
	
        	var url = file.url;        	
        	if (file.thumbnailUrl != undefined)
        	{        		
        		url = file.thumbnailUrl;
        	}
            var ext = Tg.FileUtils.getExtension(url);

            if (ext != "jpg" && ext != "jpeg" && ext != "png" && ext != "gif")
                url = "/core/images/fileicons/"+ext+".png";
            
        	var xhtml = '<tr id="fileUploadRow' + this._id + '">';
            xhtml += '<td class="fileUploadThumbnail"><img id="' + this._id + '_image" src="' + url + '" width="32" height="32"  /></td>';
            xhtml += '<td id="' + this._id + '_name" class="fileUploadName">' + file.name + '</td>';
            xhtml += '<td class="fileUploadDelete">';
            xhtml += '<a href="#" class="formFileUploadDelete">Remove this file</a>';
            xhtml += '</td>';
            xhtml += '</tr>';

	        this._preview.append(xhtml);
            
	        this._preview.find('.formFileUploadDelete').bind('click', $.proxy(this.handleFileDelete, this));
	        
        	$('#' + this.elPath + '_FormFileButtons').css({float:'right'});
        },
        
        handleFileDelete : function (ev)
        {
        	if (confirm ('Are you sure you want to remove this file?'))
        	{
            	this.file = null;
            	$(ev.currentTarget).parent().parent().fadeOut ();            	
        	}
            return false;
        }

        ,debug: function (indent) {
            var s = "\t".repeat(indent) + "Field " + this.type + ", id:" + this.id + ", path:" + this.elPath + ", value:" + this.value
            
            if (this.file)
            	s += ", file:" + this.file.id;
            s+="\n";
            
            return s;            
        }

	    ,toXml: function (xmlDoc) {	  
	        var el = xmlDoc.createElement(this.type);
	        el.setAttribute("id", this.id);
	        
	        if (this.file != null) {
		        el.setAttribute("url", this.file.url);
		        el.setAttribute("name", this.file.name);
		        el.setAttribute("thumbnailUrl", this.file.thumbnailUrl);
	
		        if (this.file.id) {
		            var newCDATA = xmlDoc.createCDATASection(this.file.id);
		            el.appendChild(newCDATA);
		        }
	        }

	        return el;
	    }
	});

